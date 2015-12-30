<?php
namespace Storage;

use Storage\Model\Comment;
use Storage\Model\CommentForm;
use Storage\Helper\Pager;
use Storage\Helper\Token;
use Storage\Helper\ViewHelper;

mb_internal_encoding('UTF-8');

$loader = require '../vendor/autoload.php';
$config = require '../config.php';

$app = new \Slim\Slim([
        'view' => new \Slim\Views\Smarty(),
        'templates.path' => '../views',
        'debug' => true,
]);

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
$app->container->singleton('fileUploadService', function () use ($app) {
    return new \Storage\Helper\FileUploadService($app->fileMapper);
});

$token = Token::init();

$app->view->appendData([
    'baseUrl' => $app->request->getUrl(),
    'loginManager' => $app->loginManager,
    'title'=>'FileSharing &mdash; upload file',
    'bookmark'=>'Upload',
    'token'=>$token,
]);

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

$app->map('/', function() use ($app) {
    if ($app->request->isGet()) {
        $app->render('upload_form.tpl');
        $app->stop();
    }
    $isAjax = ($app->request->get('ajax') !== null) ? true : false;
    $error = $_FILES['upload']['error']['file1'];
    $name = $_FILES['upload']['name']['file1'];
    $tempName = $_FILES['upload']['tmp_name']['file1'];
    if ($error) {
        if ($isAjax) {
            echo 'error';
        } else {
            $uploadError = 'File wasn\'t uploaded, please try again later';
            $app->render('upload_form.tpl', ['uploadError'=>$uploadError]);
        }
    } else {
        $author_id = ($app->loginManager->loggedUser)
                     ? $app->loginManager->loggedUser->id
                     : null;
        $file = \Storage\Model\File::fromUser($name, $tempName, $author_id);
        if ($app->fileUploadService->upload($file, $tempName)) {
            if ($isAjax) {
                echo $file->id;
            } else {
                $app->response->redirect("/view/{$file->id}");
            }
        } else {
            if ($isAjax) {
                echo 'error';
            } else {
                $uploadError = 'File wasn\'t uploaded, please try again later';
                $app->render(
                    'upload_form.tpl', ['uploadError' => $uploadError]
                );
            }
        }
    }
})->via('GET', 'POST');

$app->map('/login', function () use ($app) {
    $loginForm = new \Storage\Model\LoginForm($app->request);
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
    $registerForm = new \Storage\Model\RegisterForm($app->request);
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

$app->get('/view', function() use ($app) {
    $page = $app->request->get('page') ? intval($app->request->get('page')) : 1;
    $pager = new Pager($app->fileMapper, $page);
    $offset = ($page - 1) * Pager::$perPage;
    $list = $app->fileMapper->findAll($offset);
    $title = 'FileSharing &mdash; files';
    $noticeMessage = ($app->request->get('page') === 'ok')
                    ? "File has been uploaded successfully" : '';
    $bookmark = 'Files';
    $app->render(
        'list_info.tpl', [
            'list'=>$list,
            'title'=>$title,
            'noticeMessage'=>$noticeMessage,
            'bookmark'=>$bookmark,
            'pager'=>$pager,
    ]);
});

$app->get('/download/:id/:name', function ($id, $name) use ($app){
    $app->fileMapper->updateCounter($id);
    header('X-SendFile: ../'.ViewHelper::getUploadPath($id, $name));
    header('Content-Disposition: attachment');
    $app->stop();
});

$app->map('/view/:id', function ($id) use ($app) {
    $title = 'FileSharing &mdash; file description';
    $bookmark = 'Files';
    $reply = $app->request->get('reply') ? intval($app->request->get('reply')) : '';
    if (!$file = $app->fileMapper->findById($id)) {
        $app->notFound();
    }
    ViewHelper::createPreviewChecker($file);
    $pageViewService = new \Storage\Helper\PageViewService(
        $app->commentMapper,
        $app->userMapper
    );
    $commentsAndAuthors = $pageViewService->getCommentsAndAuthors($id);
    $loggedUserID = $app->loginManager->loggedUser
                ? $app->loginManager->loggedUser->id : null;
    $commentForm = new CommentForm($app->request, $file->id, $loggedUserID);
    if ($app->request->isPost()) {
        if (!$loggedUserID) {
            $commentForm->setCaptchaRequired();
        }
        if ($commentForm->validate()) {
            $app->commentMapper->save($commentForm->getComment());
            $app->redirect($app->request->getResourceUri());
        }
    }
    $app->render(
        'file_info.tpl', [
            'file'=>$file,
            'title'=>$title,
            'bookmark'=>$bookmark,
            'commentsAndAuthors'=>$commentsAndAuthors,
            'reply'=>$reply,
            'form'=>$commentForm,
    ]);
})->via('GET', 'POST');

$app->run();
