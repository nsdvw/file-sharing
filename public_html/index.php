<?php
use Slim\Slim;
use Slim\Views\Smarty;
use Storage\Model\File;
use Storage\Model\User;
use Storage\Model\LoginForm;
use Storage\Model\MediaInfo;
use Storage\Model\FileMapper;
use Storage\Model\RegisterForm;
use Storage\Model\UserMapper;
use Storage\Helper\ViewHelper;
use Storage\Helper\HashGenerator;
use Storage\Helper\PreviewGenerator;

define('UPLOAD_DIR', 'upload');
define('PREVIEW_DIR', 'preview');
define('DOWNLOAD_DIR', 'download');
define('BASE_DIR', '..');

$loader = require BASE_DIR.'/vendor/autoload.php';

$app = new Slim(
    array(
        'view' => new Smarty(),
        'templates.path' => BASE_DIR.'/views',
        'debug' => true,
));
/*$app->view->caching = false;
$app->view->compile_check = false;
$app->view->force_compile = true;
да чтоб тебя, как же отключить это долбаное кеширование?*/

$baseUrl = $app->request->getUrl();
$app->view->appendData( array(
    'baseUrl' => $baseUrl,
));

$app->notFound(function () use ($app) {
    $app->render('404.php');
});

$app->container->singleton('connection', function () {
    $db_config = parse_ini_file(BASE_DIR.'/config.ini');
    return new PDO(
                    $db_config['conn'],
                    $db_config['user'],
                    $db_config['pass']
                );
});

$app->container->singleton('fileMapper', function () use ($app) {
    return new FileMapper($app->connection);
});

$app->container->singleton('userMapper', function () use ($app) {
    return new UserMapper($app->connection);
});

$app->get('/', function() use ($app) {
    if (isset($_GET['register']) or isset($_GET['login'])) {
        session_start();
        $id = strval($_SESSION['id']);
        $hash = strval($_SESSION['hash']);
        setcookie('id', strval($_SESSION['id']), time() + 3600 * 24 * 7);
        setcookie('hash', strval($_SESSION['hash']), time() + 3600 * 24 * 7);
        $_COOKIE['id'] = $id;
        $_COOKIE['hash'] = $hash;
    }
    $title = 'Загрузить файл на сервер';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $logout = (isset($_GET['logout'])) ? true : false;
    if ($logout) {
        setcookie('id', '');
        setcookie('hash', '');
    }
    $login = ($id and $hash and !$logout) ? true : false;
    $errorMessage = (isset($_GET['error']))
                    ? 'Ошибка. Файл не был загружен. Попробуйте снова.' : '';
    $noticeMessage = (isset($_GET['notice']) and $_GET['notice'] == 'ok')
                    ? "Файл успешно загружен!" : '';
    $app->render(
        'upload_form.tpl',
        array(
            'noticeMessage'=>$noticeMessage,
            'errorMessage'=>$errorMessage,
            'title'=>$title,
            'login'=>$login,
        )
    );
});

$app->post('/ajax/upload', function() use ($app) {
    $error = $_FILES['upload']['error']['file1'];
    $name = $_FILES['upload']['name']['file1'];
    $tmp_name = $_FILES['upload']['tmp_name']['file1'];
    $description = (isset($_POST['description']) and $_POST['description']!=='') 
                        ? $_POST['description'] : null;
    if ($error) {
        echo 'error';
    } else {
        $file = File::fromUser($name, $tmp_name, $description);
        $app->connection->beginTransaction();
        $app->fileMapper->save($file);
        if (move_uploaded_file(
            $tmp_name,
            ViewHelper::getUploadPath($file->id, $file->name)))
        {
            $app->connection->commit();
            echo 'ok';
        } else {
            $app->connection->rollBack();
            echo 'error';
        }
    }
});

$app->get('/ajax/finfo/:id', function ($id) use ($app) {
    $types = array(
        'audio/mpeg'=>'mp3',
        'audio/mp4'=>'m4a',
        'audio/webm'=>'webma',
        'audio/x-wav'=>'wav',
        'audio/x-flv'=>'fla',
        'audio/ogg'=>'oga',
        'video/ogg'=>'ogv',
        'video/webm'=>'webmv',
        'video/mp4'=>'m4v',
        'video/x-flv'=>'flv',
    );
    if (!$file = $app->fileMapper->findById($id)) {
        echo 'error';
    } elseif (!key_exists($file->mime_type, $types)) {
        echo 'error';
    } else {
        $type = $types[$file->mime_type];
        $name = ViewHelper::getUploadName($file->id, $file->name);
        $json = '{"' . $type . '": "/' . UPLOAD_DIR . "/$name" . '"}';
        echo $json;
    }
});

$app->post('/', function() use ($app) {
    if (isset($_POST['login'])) {
        $loginForm = new LoginForm(array(
            'email'=>$_POST['login']['email'],
            'password'=>$_POST['login']['password'],
        ));
        if ($loginForm->validate()) {
            if ( !$user = $app->userMapper->findByEmail($loginForm->email) ) {
                echo 'not found...';die;
                /* Я в курсе, что здесь надо передать ошибку
                * либо гет-параметром, либо кукой. Но запутался с
                * гет-переменными, они у меня перекликаются, и эти ветки условий
                * разрослись нехорошо... Потом подумаю и исправлю.
                */
            } else {
                if ($user->hash !== sha1($user->salt . $loginForm->password)) {
                    echo 'password is wrong...<br>';
                } else {
                    session_start();
                    $_SESSION['id'] = $user->id;
                    $_SESSION['hash'] = $user->hash;
                    $app->response->redirect('/?login');
                }
            }
        }
    } elseif (isset($_POST['upload'])) {
        $error = $_FILES['upload']['error']['file1'];
        $name = $_FILES['upload']['name']['file1'];
        $tmp_name = $_FILES['upload']['tmp_name']['file1'];
        $description = (isset($_POST['description']) and $_POST['description']!=='') 
                            ? $_POST['description'] : null;
        if ($error) {
            $app->response->redirect("/?error=$error");
        } else {
            $file = File::fromUser($name, $tmp_name, $description);
            $app->connection->beginTransaction();
            $app->fileMapper->save($file);
            if (move_uploaded_file(
                $tmp_name,
                ViewHelper::getUploadPath($file->id, $file->name)))
            {
                $app->connection->commit();
                $app->response->redirect("/?notice=ok");
            } else {
                $app->connection->rollBack();
                $app->response->redirect("/?error=server_error");
            }
        }
    }
});

$app->get('/reg', function () use ($app) {
    $title = 'Регистрация';
    $login = false;
    $app->render(
        'register_form.tpl',
        array(
            'title'=>$title,
            'login'=>$login,
        )
    );
});

$app->post('/reg', function () use ($app) {
    $registerForm = new RegisterForm(
        array(
            'login'=>$_POST['register']['login'],
            'email'=>$_POST['register']['email'],
            'password'=>$_POST['register']['password'],
        )
    );
    if ($registerForm->validate()) {
        $user = new User;
        $user->fromForm($registerForm);
        $mapper = new UserMapper($app->connection);
        $mapper->register($user);
        session_start();
        $_SESSION['id'] = $user->id;
        $_SESSION['hash'] = $user->hash;
        $app->response->redirect('/?register=ok');
    }
    else echo '<meta charset="utf-8">Не прошла валидация.';
});

$app->get('/view', function() use ($app) {
    $list = $app->fileMapper->findAll();
    $title = 'Список файлов на сервере';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $login = ($id and $hash) ? true : false;
    $app->render(
        'list_info.tpl',
        array(
            'list'=>$list,
            'title'=>$title,
            'login'=>$login,
        )
    );
});

$app->get('/download/:id/:name', function ($id, $name) use ($app){
    $app->fileMapper->updateCounter($id);
    header('X-SendFile: '.'..'.DIRECTORY_SEPARATOR.
        ViewHelper::getUploadPath($id, $name));
    header('Content-Disposition: attachment');
    exit;
});

$app->get('/view/:id', function ($id) use ($app) {
    if (!$file = $app->fileMapper->findById($id)) {
        $app->notFound();
    }
    $title = 'Информация о файле';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $login = ($id and $hash) ? true : false;

    if ($file->isImage()) {
        $path = ViewHelper::getPreviewPath($id);
        if (!PreviewGenerator::hasPreview($path)) {
            PreviewGenerator::createPreview($file);
        }
        $preview = 'image_preview';
        $description = 'image_description';
        $app->render(
            'file_info.tpl',
            array(
                'file'=>$file,
                'title'=>$title,
                'preview'=>$preview,
                'description'=>$description,
                'login'=>$login,
            )
        );
    } elseif ($file->isVideo()) {
        $preview = 'video_player';
        $description = 'video_description';
        $app->render(
            'file_info.tpl',
            array(
                'file'=>$file,
                'title'=>$title,
                'preview'=>$preview,
                'description'=>$description,
                'login'=>$login,
            )
        );
    } elseif($file->isAudio()) {
        $preview = 'audio_player';
        $description = 'audio_description';
        $app->render(
            'file_info.tpl',
            array(
                'file'=>$file,
                'title'=>$title,
                'preview'=>$preview,
                'description'=>$description,
                'login'=>$login,
            )
        );
    } else {
        $preview = false;
        $description = false;
        $app->render(
            'file_info.tpl',
            array(
                'file'=>$file,
                'title'=>$title,
                'preview'=>$preview,
                'description'=>$description,
                'login'=>$login,
            )
        );
    }
});

$app->run();
