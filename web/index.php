<?php
namespace Storage;

use Storage\Model\Comment;
use Storage\Form\CommentForm;
use Storage\Helper\Pager;
use Storage\Helper\Token;
use Storage\Helper\ViewHelper;
use Storage\Helper\JsonEncoder;

mb_internal_encoding('UTF-8');

$loader = require '../vendor/autoload.php';
$config = require '../config.php';

$app = new \Slim\Slim([
        'view' => new \Slim\Views\Twig(),
        'templates.path' => '../views',
]);
// $app->view()->parserOptions = ['cache' => '../twig_cache'];
$function = new \Twig_SimpleFunction(
    'callStaticMethod',
    function ($class, $method, array $args) {
        return call_user_func_array("{$class}::{$method}", $args);
});
$app->view()->getInstance()->addFunction($function);
$app->view()->getInstance()->addExtension(new \Twig_Extensions_Extension_Text());

$app->container->singleton('connection', function () use ($config) {
    $dbh = new \PDO( $config['conn'], $config['user'], $config['pass'] );
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $dbh;
});
$app->container->singleton('fileMapper', function () use ($app) {
    return new \Storage\Mapper\FileMapper($app->connection);
});
$app->container->singleton('userMapper', function () use ($app) {
    return new \Storage\Mapper\UserMapper($app->connection);
});
$app->container->singleton('commentMapper', function () use ($app) {
    return new \Storage\Mapper\CommentMapper($app->connection);
});
$app->container->singleton('loginManager', function () use ($app) {
    return new \Storage\Auth\LoginManager($app->userMapper);
});

$token = Token::init();

$app->notFound(function () use ($app) {
    $title = 'FileSharing &mdash; page not found';
    $app->render('404.tpl', ['title'=>$title] );
});

$app->post('/logout', function () use ($app) {
    if (Token::checkToken()) {
        $app->loginManager->logout();
    }
    $app->response->redirect('/');
});

$app->get('/ajax/fileinfo/:id', function ($id) use ($app) {
    header('Content-Type: application/json');
    $file = $app->fileMapper->findById($id);
    ViewHelper::createPreviewChecker($file);
    if (!$file) {
        echo json_encode("{\"error\": \"File not found\"}");
    } else {
        echo json_encode( $file->toArray() );
    }
});

$app->map('/', function () use ($app) {
    $isAjax = ($app->request->get('ajax') !== null) ? true : false;
    $author_id = $app->loginManager->getUserID();
    $uploadForm = new \Storage\Form\UploadForm($app->request, $_FILES, $author_id);
    $fileUploadService = new \Storage\Helper\FileUploadService($app->fileMapper);
    if ($app->request->isPost()) {
        if ($uploadForm->validate() and $fileUploadService->upload($uploadForm)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo JsonEncoder::createResponse($uploadForm->getFile()->id);
                $app->stop();
            } else {
                $app->response->redirect(
                    ViewHelper::getDetailViewUrl($uploadForm->getFile()->id)
                );
            }
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo JsonEncoder::createResponse(null, $uploadForm->errorMessage);
                $app->stop();
            }
        }
    }
    $app->render('upload_form.twig', ['uploadForm' => $uploadForm]);
})->via('GET', 'POST');

$app->map('/login', function () use ($app) {
    $loginForm = new \Storage\Form\LoginForm($app->request);
    if ($app->request->isPost()) {
        if ($app->loginManager->validateLoginForm($loginForm)) {
            $app->loginManager->authorizeUser($loginForm->getUser());
            $app->response->redirect('/');
        }
    }
    $app->render('login.tpl', ['loginForm' => $loginForm]);
})->via('GET', 'POST');

$app->map('/reg', function () use ($app) {
    $title = 'FileSharing &mdash; registration';
    $bookmark = 'Sign up';
    $registerForm = new \Storage\Form\RegisterForm($app->request);
    if ($app->request->isPost()) {
        if ($app->loginManager->validateRegisterForm($registerForm)) {
            $app->userMapper->register($registerForm->getUser());
            $app->loginManager->authorizeUser($registerForm->getUser());
            $app->response->redirect('/');
        }
    }
    $app->render(
        'register_form.tpl', [
            'registerForm' => $registerForm,
            'title' => $title,
            'bookmark' => $bookmark,
    ]);
})->via('GET', 'POST');

$app->get('/view', function () use ($app) {
    $page = $app->request->get('page') ? intval($app->request->get('page')) : 1;
    $pager = new Pager($app->fileMapper, $page);
    $offset = ($page - 1) * Pager::$perPage;
    $list = $app->fileMapper->findAll($offset);
    $app->render(
        'list_info.twig', [
            'list'=>$list,
            'pager'=>$pager,
    ]);
});

$app->get('/download/:id/:name', function ($id, $name) use ($app){
    $app->fileMapper->updateCounter($id);
    header('X-SendFile: ../'.ViewHelper::getUploadPath($id, $name));
    header('Content-Disposition: attachment');
});

$app->map('/view/:id', function ($id) use ($app) {
    $reply = $app->request->get('reply') ? intval($app->request->get('reply')) : '';
    $isAjax = ($app->request->get('ajax') !== null) ? true : false;
    if (!$file = $app->fileMapper->findById($id)) {
        $app->notFound();
    }
    ViewHelper::createPreviewChecker($file);
    $pageViewService = new \Storage\Helper\PageViewService(
        $app->commentMapper,
        $app->userMapper
    );
    $commentsAndAuthors = $pageViewService->getCommentsAndAuthors($id);
    $loggedUserID = $app->loginManager->isLogged()
                ? $app->loginManager->getUserID() : null;
    $commentForm = new CommentForm($app->request, $file->id, $loggedUserID);
    if ($app->request->isPost()) {
        if (!$loggedUserID) {
            $commentForm->setCaptchaRequired();
        }
        if ($commentForm->validate()) {
            $app->commentMapper->save($commentForm->getComment());
            if ($isAjax) {
                header('Content-Type: application/json');
                echo JsonEncoder::createResponse([
                    'comment'=>$commentForm->getComment(),
                    'author'=>$app->loginManager->getLoggedUser(),
                ]);
                $app->stop();
            } else {
                $app->redirect($app->request->getResourceUri());
            }
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo JsonEncoder::createResponse(null, $commentForm->errorMessage);
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

$app->run();
