<?php
namespace FileSharing;

use FileSharing\Helper\ViewHelper;
use FileSharing\Helper\JsonEncoder;

mb_internal_encoding('UTF-8');

$loader = require '../vendor/autoload.php';
$config = require '../config.php';

$app = new \Slim\Slim([
        'view' => new \Slim\Views\Twig(),
        'templates.path' => '../views',
        'debug' => false,
]);
// $app->view()->parserOptions = ['cache' => '../twig_cache'];

$app->view()->getInstance()->addExtension(new \Twig_Extensions_Extension_Text());
$app->view()->getInstance()->addExtension(new \Twig_Extensions_Extension_Date());

Helper\Token::init($app->response, $app->request);

$app->container->singleton('connection', function () use ($config) {
    $dbh = new \PDO( $config['conn'], $config['user'], $config['pass'] );
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $dbh;
});
$app->container->singleton('fileMapper', function () use ($app) {
    return new Mapper\FileMapper($app->connection);
});
$app->container->singleton('userMapper', function () use ($app) {
    return new Mapper\UserMapper($app->connection);
});
$app->container->singleton('commentMapper', function () use ($app) {
    return new Mapper\CommentMapper($app->connection);
});
$app->container->singleton('loginManager', function () use ($app) {
    return new Auth\LoginManager($app->userMapper, $app->response, $app->request);
});
$app->container->singleton('viewHelper', function () {
    return new ViewHelper;
});

$app->view->setData([
    'loginManager'=>$app->loginManager,
    'viewHelper'=>$app->viewHelper,
]);

$app->notFound(function () use ($app) {
    $app->render('404.twig');
});

$app->error(function (\Exception $e) use ($app) {
    $app->log->info($e);
    $app->render('500.twig');
});

$app->post('/logout', function () use ($app) {
    if ( $app->loginManager->checkToken($app->request->post('logoutForm')) ) {
        $app->loginManager->logout();
    }
    $app->response->redirect('/');
});

$app->map('/', function () use ($app) {
    $isAjax = ($app->request->get('ajax') !== null) ? true : false;
    $author_id = $app->loginManager->getUserID();
    $uploadForm = new Form\UploadForm($app->request, $_FILES, $author_id);
    $fileUploadService = new Helper\FileUploadService($app->fileMapper);
    if ($app->request->isPost()) {
        if ($uploadForm->validate() and $fileUploadService->upload($uploadForm)) {
            if ($isAjax) {
                ajaxResponse($app->response, $uploadForm->getFile()->id);
                $app->stop();
            } else {
                $app->response->redirect(
                    $app->viewHelper->getDetailViewUrl($uploadForm->getFile()->id)
                );
            }
        } else {
            if ($isAjax) {
                ajaxResponse($app->response, null, $uploadForm->errorMessage);
                $app->stop();
            }
        }
    }
    $app->render('upload_form.twig', ['uploadForm' => $uploadForm]);
})->via('GET', 'POST');

$app->map('/login', function () use ($app) {
    $loginForm = new Form\LoginForm($app->request);
    if ($app->request->isPost()) {
        if ($app->loginManager->validateLoginForm($loginForm)) {
            $app->loginManager->authorizeUser(
                $loginForm->getUser(),
                $loginForm->remember
            );
            $app->response->redirect('/');
        }
    }
    $app->render('login.twig', ['loginForm' => $loginForm]);
})->via('GET', 'POST');

$app->map('/reg', function () use ($app) {
    $registerForm = new Form\RegisterForm($app->request);
    if ($app->request->isPost()) {
        if ($app->loginManager->validateRegisterForm($registerForm)) {
            $app->userMapper->register($registerForm->getUser());
            $app->loginManager->authorizeUser($registerForm->getUser());
            $app->response->redirect('/');
        }
    }
    $app->render('register_form.twig', ['registerForm' => $registerForm]);
})->via('GET', 'POST');

$app->get('/view', function () use ($app) {
    $page = $app->request->get('page') ? intval($app->request->get('page')) : 1;
    $pager = new Helper\Pager( $page, $app->fileMapper->getFileCount(), 15 );
    $list = $app->fileMapper->findAll($pager->getOffset(), $pager->perPage);
    $app->render('list_info.twig', ['list'=>$list, 'pager'=>$pager]);
});

$app->get('/download/:id/:name', function ($id, $name) use ($app){
    $app->fileMapper->updateCounter($id);
    header('X-SendFile: ..'.$app->viewHelper->getUploadUrl($id, $name));
    header('Content-Disposition: attachment');
});

$app->map('/view/:id', function ($id) use ($app) {
    $reply = $app->request->get('reply') ? intval($app->request->get('reply')) : '';
    $isAjax = ($app->request->get('ajax') !== null) ? true : false;
    if (!$file = $app->fileMapper->findById($id)) {
        $app->notFound();
    }
    $app->viewHelper->createPreviewChecker($file);
    $pageViewService = new Helper\PageViewService(
        $app->commentMapper,
        $app->userMapper
    );
    $commentsAndAuthors = $pageViewService->getCommentsAndAuthors($id);
    $commentForm = new Form\CommentForm(
        $app->request, $file->id, $app->loginManager->getUserID()
    );
    if ($app->request->isPost()) {
        if (!$app->loginManager->isLogged()) {
            $commentForm->setCaptchaRequired();
        }
        if ($commentForm->validate()) {
            $app->commentMapper->save($commentForm->getComment());
            if ($isAjax) {
                ajaxResponse($app->response, [
                    'comment'=>$commentForm->getComment(),
                    'login'=>$app->loginManager->getUserLogin(),
                ]);
                $app->stop();
            } else {
                $app->redirect($app->request->getResourceUri());
            }
        } else {
            if ($isAjax) {
                ajaxResponse($app->response, null, $commentForm->errorMessage);
                $app->stop();
            }
        }
    }
    $app->render(
        'file_info.twig', [
            'commentsAndAuthors'=>$commentsAndAuthors,
            'reply'=>$reply,
            'form'=>$commentForm,
            'file'=>$file,
    ]);
})->via('GET', 'POST');

$app->map('/edit/:id', function ($id) use ($app) {
    $isAjax = ($app->request->get('ajax') !== null) ? true : false;
    if (!$file = $app->fileMapper->findById($id)) {
        if ($isAjax) {
            ajaxResponse($app->response, null, 'Page not found');
            $app->stop();
        } else {
            $app->notFound();
        }
    }
    if ($app->loginManager->hasRights($file)) {
        $form = new Form\EditFileForm($app->request, $id);
        if ($app->request->isPost()) {
            if ( $app->loginManager->checkToken($app->request->post('edit')) ) {
                $app->fileMapper->update($form->getFile());
            } elseif ($app->loginManager->checkToken(
                $app->request->post('delete'))
            ) {
                $app->fileMapper->delete($id);
            }
        }
        if ($isAjax) {
            ajaxResponse($app->response, [
                'description' => $form->getFile()->description,
                'date' => $form->getFile()->best_before,
            ]);
        } else {
            $app->render('edit_file.twig', [
                'deleted' => $app->request->post('delete'),
                'file' => $file,
            ]);
        }
    } else {
        if ($isAjax) {
            ajaxResponse($app->response, null, 'Access denied');
        } else {
            $app->render('403.twig', [], 403);
        }
    }
})->via('GET', 'POST');

$app->get('/tos', function () use ($app) {
    $app->render('tos.twig');
});

$app->run();

function ajaxResponse(\Slim\Http\Response $response, $text, $error = null) {
    $response->headers->set('Content-Type', 'application/json');
    $response->setBody(JsonEncoder::createResponse($text, $error));
}
