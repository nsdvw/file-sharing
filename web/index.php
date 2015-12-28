<?php
namespace Storage;

use Slim\Slim;
use Slim\Views\Smarty;
use Storage\Model\File;
use Storage\Model\Comment;
use Storage\Model\LoginForm;
use Storage\Model\CommentForm;
use Storage\Model\FormWithCaptcha;
use Storage\Model\RegisterForm;
use Storage\Helper\Pager;
use Storage\Helper\Token;
use Storage\Helper\ViewHelper;
use Storage\Helper\HashGenerator;
use Storage\Helper\PreviewGenerator;
use Storage\Helper\FileUploadService;
use Storage\Mapper\FileMapper;
use Storage\Mapper\UserMapper;
use Storage\Mapper\CommentMapper;
use Storage\Auth\LoginManager;

define('UPLOAD_DIR', 'upload');
define('PREVIEW_DIR', 'preview');
define('DOWNLOAD_DIR', 'download');
define('BASE_DIR', dirname(__DIR__));
mb_internal_encoding('UTF-8');

$loader = require BASE_DIR.'/vendor/autoload.php';
$config = require BASE_DIR . DIRECTORY_SEPARATOR . 'config.php';

$app = new Slim([
        'view' => new Smarty(),
        'templates.path' => BASE_DIR.'/views',
        'debug' => true,
]);

$app->container->singleton('connection', function () use ($config) {
    return new \PDO( $config['conn'], $config['user'], $config['pass'] );
});
$app->container->singleton('fileMapper', function () use ($app) {
    return new FileMapper($app->connection);
});
$app->container->singleton('userMapper', function () use ($app) {
    return new UserMapper($app->connection);
});
$app->container->singleton('commentMapper', function () use ($app) {
    return new CommentMapper($app->connection);
});
$app->container->singleton('loginManager', function () use ($app) {
    return new LoginManager($app->userMapper);
});
$app->container->singleton('fileUploadService', function () use ($app) {
    return new FileUploadService($app->fileMapper);
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
    if (!$file) {
        echo json_encode("{\"error\": \"File not found\"}");
    } else {
        echo json_encode( $file->toArray() );
    }
});

$app->map('/login', function () use ($app) {
    if ($app->request->isGet()) {
        $app->render('upload_form.tpl');
        $app->stop();
    }
    $loginForm = new LoginForm([
        'email'=>$_POST['login']['email'],
        'password'=>$_POST['login']['password'],
    ]);
    if ($loginForm->validate()) {
        if ($app->loginManager->validateUser($loginForm)) {
            $app->loginManager->authorizeUser();
            $app->response->redirect('/login');
        } else {
            $loginForm->errorMessage = LoginForm::WRONG_PASSWORD;
        }
    }
    $app->render(
        'login.tpl', ['loginForm' => $loginForm]
    );
})->via('GET', 'POST');

$app->map('/', function() use ($app) {
    if ($app->request->isGet()) {
        $app->render('upload_form.tpl');
        $app->stop();
    }
    $isAjax = (isset($_GET['ajax'])) ? true : false;
    if (isset($_POST['upload'])) {
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
            $file = File::fromUser($name, $tempName, $author_id);
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
    }
})->via('GET', 'POST');

$app->get('/reg', function () use ($app) {
    $title = 'FileSharing &mdash; registration';
    $bookmark = 'Sign up';
    $registerForm = new RegisterForm([
        'errorMessage'=>'', 'login'=>'', 'email'=>'', 'password'=>'',
    ]);
    $app->render(
        'register_form.tpl', [
            'title'=>$title,
            'bookmark'=>$bookmark,
            'registerForm'=>$registerForm,
    ]);
});

$app->post('/reg', function () use ($app) {  
    $registerForm = new RegisterForm([
        'login'=>$_POST['register']['login'],
        'email'=>$_POST['register']['email'],
        'password'=>$_POST['register']['password'],
    ]);
    if ($registerForm->validate()) {
        $user = $registerForm->getUser();
        $app->userMapper->register($user);
        $app->loginManager->authorizeUser($user);
        $app->response->redirect('/?register=ok');
    } else {
        $title = 'FileSharing &mdash; registration';
        $bookmark = 'Sign up';
        $app->render(
            'register_form.tpl', [
                'title'=>$title,
                'bookmark'=>$bookmark,
                'registerForm'=>$registerForm,
        ]);
    }
});

$app->get('/view', function() use ($app) {
    $page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
    $pager = new Pager($app->fileMapper, $page);
    $offset = ($page - 1) * Pager::$perPage;
    $list = $app->fileMapper->findAll($offset);
    $title = 'FileSharing &mdash; files';
    $noticeMessage = (isset($_GET['upload']) and $_GET['upload'] == 'ok')
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
    header('X-SendFile: '.'..'.DIRECTORY_SEPARATOR.
        ViewHelper::getUploadPath($id, $name));
    header('Content-Disposition: attachment');
    $app->stop();
});

$app->map('/view/:id', function ($id) use ($app) {
    $title = 'FileSharing &mdash; file description';
    $bookmark = 'Files';
    $reply = (isset($_GET['reply'])) ? intval($_GET['reply']) : '';

    if (!$file = $app->fileMapper->findById($id)) {
        $app->notFound();
    }
    if ($file->isImage()) {
        $path = ViewHelper::getPreviewPath($id);
        if (!PreviewGenerator::hasPreview($path)) {
            PreviewGenerator::createPreview($file);
        }
    }
    $comments = $app->commentMapper->getComments($file->id);
    foreach ($comments as $comment) {
        $comment->level = Comment::getLevel($comment->materialized_path);
        $comment->author_id = $app->userMapper->findById($comment->author_id);
    }

    $form = new CommentForm([
        'contents'=>'','reply_id'=>'','file_id'=>'','author_id'=>''
    ]);
    if ($app->request->isPost()) {
        if (!$app->loginManager->loggedUser) {
            $author_id = null;
            $captcha = new FormWithCaptcha([
                'captcha' => $_POST['comment_form']['captcha']
            ]);
            $captchaError = ($captcha->validate()) ? '' : $captcha->errorMessage;
        } else {
            $author_id = $app->loginManager->loggedUser->id;
            $captchaError = '';
        }
        $form = new CommentForm([
                    'contents'=>$_POST['comment_form']['contents'],
                    'reply_id'=>$_POST['comment_form']['reply_id'],
                    'file_id'=>$id,
                    'author_id'=>$author_id,
                ]);
        if ($form->validate()) $form->errorMessage = $captchaError;
        if (!$form->errorMessage) {
            $comment = new Comment;
            $comment->fromForm($form, $app->commentMapper);
            $app->commentMapper->save($comment);
            $app->response->redirect('/view/'.$id);
        }
    }
    $app->render(
        'file_info.tpl', [
            'file'=>$file,
            'title'=>$title,
            'bookmark'=>$bookmark,
            'comments'=>$comments,
            'reply'=>$reply,
            'form'=>$form,
    ]);
})->via('GET', 'POST');

$app->run();
