<?php

use Storage\Model\File;
use Storage\Model\Comment;
use Storage\Helper\Pager;
use Storage\Helper\Token;
use Storage\Helper\ViewHelper;
use Storage\Helper\HashGenerator;
use Storage\Helper\PreviewGenerator;

define('UPLOAD_DIR', 'upload');
define('PREVIEW_DIR', 'preview');
define('DOWNLOAD_DIR', 'download');
define('BASE_DIR', '..');
mb_internal_encoding('UTF-8');

$loader = require BASE_DIR.'/vendor/autoload.php';
$app = new Slim\Slim(
    array(
        'view' => new Slim\Views\Smarty(),
        'templates.path' => BASE_DIR.'/views',
        'debug' => true,
));

$app->container->singleton('connection', function () {
    $db_config = parse_ini_file(BASE_DIR.'/config.ini');
    return new PDO(
                    $db_config['conn'],
                    $db_config['user'],
                    $db_config['pass']
                );
});
$app->container->singleton('fileMapper', function () use ($app) {
    return new Storage\Mapper\FileMapper($app->connection);
});
$app->container->singleton('userMapper', function () use ($app) {
    return new Storage\Mapper\UserMapper($app->connection);
});
$app->container->singleton('commentMapper', function () use ($app) {
    return new Storage\Mapper\CommentMapper($app->connection);
});
$app->container->singleton('loginManager', function () use ($app){
    return new Storage\Auth\LoginManager($app->userMapper);
});

if (!Token::issetToken()) $token = Token::generateToken();
else $token = Token::getToken();
$time = time() + 24*3600;
Token::setToken($token, $time);

$app->view->appendData( array(
    'baseUrl' => $app->request->getUrl(),
    'loginManager' => $app->loginManager,
    'title'=>'FileSharing &mdash; upload file',
    'bookmark'=>'Upload',
    'token'=>$token,
));

$app->notFound(function () use ($app) {
    $title = 'FileSharing &mdash; page not found';
    $app->render('404.tpl', array('title'=>$title,) );
});

$app->post('/logout', function () use ($app) {
    if (Token::checkToken()) $app->loginManager->logout();
    $app->response->redirect('/');
});

$app->get('/ajax/fileinfo/:id', function ($id) use ($app) {
    header('Content-Type: application/json');
    $file = $app->fileMapper->findById($id);
    if (!$file) {
        echo json_encode(null);
    } else {
        echo json_encode( $file->toArray() );
    }
});

$app->map('/login', function () use ($app) {
    if ($app->request->isGet()) {
        $app->render('upload_form.tpl');
    } else {
        $loginForm = new Storage\Model\LoginForm(
            array(
                'email'=>$_POST['login']['email'],
                'password'=>$_POST['login']['password'],
            )
        );
        if ($loginForm->validate()) {
            if ($user = $app->userMapper->findByEmail($loginForm->email)) {
                if ($user->hash !==
                    HashGenerator::generateHash($user->salt, $loginForm->password)) {
                } else {
                    $app->loginManager->authorizeUser($user);
                    $app->response->redirect('/login');
                }
            }
        }
        $app->render(
            'login.tpl', array('loginForm'=>$loginForm,)
        );
    }
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
        $tmp_name = $_FILES['upload']['tmp_name']['file1'];
        if ($error) {
            if ($isAjax) {
                echo 'error';
            } else {
                $uploadError = 'File wasn\'t uploaded, please try again later';
                $app->render(
                    'upload_form.tpl',
                    array(
                        'uploadError'=>$uploadError,
                    )
                );
            }
        } else {
            $author_id = ($app->loginManager->loggedUser) ?
                         $app->loginManager->loggedUser->id : null;
            $file = File::fromUser($name, $tmp_name, $author_id);
            $app->connection->beginTransaction();
            $app->fileMapper->save($file);
            if (move_uploaded_file(
                $tmp_name,
                ViewHelper::getUploadPath($file->id, $file->name)))
            {
                $app->connection->commit();
                if ($file->isImage()) {
                    $path = ViewHelper::getPreviewPath($file->id);
                    PreviewGenerator::createPreview($file);
                }
                if ($isAjax) {
                    echo $file->id;
                } else {
                    $app->response->redirect("/view/{$file->id}");
                }
            } else {
                $app->connection->rollBack();
                if ($isAjax) {
                    echo 'error';
                } else {
                    $uploadError = 'Server error, please try again later';
                    $app->render(
                        'upload_form.tpl',
                        array(
                            'uploadError'=>$uploadError,
                        )
                    );
                }
            }
        }
    }
})->via('GET', 'POST');

$app->get('/reg', function () use ($app) {
    $title = 'FileSharing &mdash; registration';
    $bookmark = 'Sign up';
    $registerForm = new Storage\Model\RegisterForm(array(
        'errorMessage'=>'', 'login'=>'', 'email'=>'', 'password'=>'',
    ));
    $app->render(
        'register_form.tpl',
        array(
            'title'=>$title,
            'bookmark'=>$bookmark,
            'registerForm'=>$registerForm,
        )
    );
});

$app->post('/reg', function () use ($app) {  
    $registerForm = new Storage\Model\RegisterForm(
        array(
            'login'=>$_POST['register']['login'],
            'email'=>$_POST['register']['email'],
            'password'=>$_POST['register']['password'],
        )
    );
    if ($registerForm->validate()) {
        $user = $registerForm->getUser();
        $app->userMapper->register($user);
        $app->loginManager->authorizeUser($user);
        $app->response->redirect('/?register=ok');
    } else {
        $title = 'FileSharing &mdash; registration';
        $bookmark = 'Sign up';
        $app->render(
            'register_form.tpl',
            array(
                'title'=>$title,
                'bookmark'=>$bookmark,
                'registerForm'=>$registerForm,
            )
        );
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
        'list_info.tpl',
        array(
            'list'=>$list,
            'title'=>$title,
            'noticeMessage'=>$noticeMessage,
            'bookmark'=>$bookmark,
            'pager'=>$pager,
        )
    );
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

    $form = new Storage\Model\CommentForm(
        array('contents'=>'','reply_id'=>'','file_id'=>'','author_id'=>''));
    if ($app->request->isPost()) {
        if (!$app->loginManager->loggedUser) {
            $author_id = null;
            $captcha = new Storage\Model\FormWithCaptcha(
                        array('captcha'=>$_POST['comment_form']['captcha'],)
                    );
            $captchaError = ($captcha->validate()) ? '' : $captcha->errorMessage;
        } else {
            $author_id = $app->loginManager->loggedUser->id;
            $captchaError = '';
        }
        $form = new Storage\Model\CommentForm(
                array(
                    'contents'=>$_POST['comment_form']['contents'],
                    'reply_id'=>$_POST['comment_form']['reply_id'],
                    'file_id'=>$id,
                    'author_id'=>$author_id,
                ));
        if ($form->validate()) $form->errorMessage = $captchaError;
        if (!$form->errorMessage) {
            $comment = new Comment;
            $comment->fromForm($form, $app->commentMapper);
            $app->commentMapper->save($comment);
            $app->response->redirect('/view/'.$id);
        }
    }
    $app->render(
        'file_info.tpl',
        array(
            'file'=>$file,
            'title'=>$title,
            'bookmark'=>$bookmark,
            'comments'=>$comments,
            'reply'=>$reply,
            'form'=>$form,
        )
    );
})->via('GET', 'POST');

$app->run();
