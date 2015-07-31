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
$app->view->compile_check = true;
$app->view->force_compile = true;
как отключить это долбанное кеширование?
*/

$baseUrl = $app->request->getUrl();
$app->view->appendData( array(
    'baseUrl' => $baseUrl,
));

$app->notFound(function () use ($app) {
    $title = 'FileSharing &mdash; page not found';
    $bookmark = 'Upload';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $login = ($id and $hash) ? true : false;
    $loginEmail = '';
    $loginPassword = '';
    $loginError = '';
    $app->render(
        '404.tpl',
        array(
            'login'=>$login,
            'loginEmail'=>$loginEmail,
            'loginPassword'=>$loginPassword,
            'loginError'=>$loginError,
            'title'=>$title,
            'bookmark'=>$bookmark,
        )
    );
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
    $title = 'FileSharing &mdash; upload file';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $logout = (isset($_GET['logout'])) ? true : false;
    if ($logout) {
        setcookie('id', '');
        setcookie('hash', '');
    }
    $login = ($id and $hash and !$logout) ? true : false;
    $uploadError = (isset($_GET['error']))
                    ? 'File hasn\'t been uploaded, please try again later' : '';
    $noticeMessage = (isset($_GET['notice']) and $_GET['notice'] == 'ok')
                    ? 'File was uploded successfully' : '';
    $bookmark = 'Upload';
    $loginError = '';
    $loginEmail = '';
    $loginPassword = '';
    $app->render(
        'upload_form.tpl',
        array(
            'noticeMessage'=>$noticeMessage,
            'uploadError'=>$uploadError,
            'loginError'=>$loginError,
            'title'=>$title,
            'login'=>$login,
            'bookmark'=>$bookmark,
            'loginEmail'=>$loginEmail,
            'loginPassword'=>$loginPassword,
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
            if ($file->isImage()) {
                $path = ViewHelper::getPreviewPath($file->id);
                PreviewGenerator::createPreview($file);
            }
            echo 'ok';
        } else {
            $app->connection->rollBack();
            echo 'error';
        }
    }
});

$app->get('/ajax/mediainfo/:id', function ($id) use ($app) {
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

$app->get('/ajax/fileinfo/:id', function ($id) use ($app) {
    if (!$file = $app->fileMapper->findById($id)) {
        echo 'error';
    } else {
        $file->size = ViewHelper::formatSize($file->size);
        echo json_encode($file);
    }
});

$app->post('/', function() use ($app) {
    if (isset($_POST['login'])) {
        $loginForm = new LoginForm(
            array(
                'email'=>$_POST['login']['email'],
                'password'=>$_POST['login']['password'],
            )
        );
        if ($loginForm->validate()) {
            if (!$user = $app->userMapper->findByEmail($loginForm->email)) {
                $loginError = 'user not found';
            } else {
                if ($user->hash !== sha1($user->salt . $loginForm->password)) {
                    $loginError = 'password is wrong';
                } else {
                    session_start();
                    $_SESSION['id'] = $user->id;
                    $_SESSION['hash'] = $user->hash;
                    $app->response->redirect('/?login');
                }
            }
        } else {
            $loginError = $loginForm->errorMessage;
        }
    $noticeMessage = '';
    $uploadError = '';
    $title = 'FileSharing &mdash; upload file';
    $login = false;
    $bookmark = 'Upload';
    $loginEmail = $loginForm->email;
    $loginPassword = $loginForm->password;
    $app->render(
        'upload_form.tpl',
        array(
            'noticeMessage'=>$noticeMessage,
            'uploadError'=>$uploadError,
            'loginError'=>$loginError,
            'title'=>$title,
            'login'=>$login,
            'bookmark'=>$bookmark,
            'loginEmail'=>$loginEmail,
            'loginPassword'=>$loginPassword,
            )
        );
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
                if ($file->isImage()) {
                    $path = ViewHelper::getPreviewPath($file->id);
                    PreviewGenerator::createPreview($file);
                }
                $app->response->redirect('/view/?upload=ok');
            } else {
                $app->connection->rollBack();
                $app->response->redirect("/?error=server_error");
            }
        }
    }
});

$app->get('/reg', function () use ($app) {
    $title = 'FileSharing &mdash; registration';
    $login = false;
    $bookmark = 'Sign up';
    $loginError = '';
    $loginEmail = '';
    $loginPassword = '';
    $registerError = '';
    $registerLogin = '';
    $registerEmail = '';
    $registerPassword = '';
    $app->render(
        'register_form.tpl',
        array(
            'title'=>$title,
            'login'=>$login,
            'bookmark'=>$bookmark,
            'loginError'=>$loginError,
            'loginEmail'=>$loginEmail,
            'loginPassword'=>$loginPassword,
            'registerError'=>$registerError,
            'registerLogin'=>$registerLogin,
            'registerEmail'=>$registerEmail,
            'registerPassword'=>$registerPassword,
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
    } else {
        $title = 'FileSharing &mdash; registration';
        $login = false;
        $bookmark = 'Sign up';
        $loginError = '';
        $registerError = $registerForm->errorMessage;
        $registerLogin = $registerForm->login;
        $registerEmail = $registerForm->email;
        $loginEmail = '';
        $loginPassword = '';
        $registerPassword = $registerForm->password;
        $app->render(
            'register_form.tpl',
            array(
                'title'=>$title,
                'login'=>$login,
                'bookmark'=>$bookmark,
                'loginError'=>$loginError,
                'registerError'=>$registerError,
                'registerLogin'=>$registerLogin,
                'registerEmail'=>$registerEmail,
                'registerPassword'=>$registerPassword,
                'loginEmail'=>$loginEmail,
                'loginPassword'=>$loginPassword,
            )
        );
    }
});

$app->get('/view', function() use ($app) {
    $page = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * \Storage\Helper\Pager::PER_PAGE;
    $pager = new \Storage\Helper\Pager($app->connection, $page);
    $list = $app->fileMapper->findAll($offset);
    $title = 'FileSharing &mdash; files';
    $noticeMessage = (isset($_GET['upload']) and $_GET['upload'] == 'ok')
                    ? "File has been uploaded successfully" : '';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $login = ($id and $hash) ? true : false;
    $loginError = '';
    $loginEmail = '';
    $loginPassword = '';
    $bookmark = 'Files';
    $app->render(
        'list_info.tpl',
        array(
            'list'=>$list,
            'title'=>$title,
            'login'=>$login,
            'loginEmail'=>$loginEmail,
            'loginPassword'=>$loginPassword,
            'loginError'=>$loginError,
            'noticeMessage'=>$noticeMessage,
            'bookmark'=>$bookmark,
            'currentPage'=>$pager->currentPage,
            'lastPage'=>$pager->lastPage,
            'firstPage'=>$pager->firstPage,
            'pageCount'=>$pager->pageCount,
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
    $title = 'FileSharing &mdash; file description';
    $id = (isset($_COOKIE['id'])) ? $_COOKIE['id'] : null;
    $hash = (isset($_COOKIE['hash'])) ? $_COOKIE['hash'] : null;
    $login = ($id and $hash) ? true : false;
    $loginError = '';
    $loginEmail = '';
    $loginPassword = '';
    $bookmark = 'Files';

    if ($file->isImage()) {
        $path = ViewHelper::getPreviewPath($id);
        if (!PreviewGenerator::hasPreview($path)) {
            PreviewGenerator::createPreview($file);
        }
        $preview = 'image_preview';
        $description = 'image_description';
    } elseif ($file->isVideo()) {
        $preview = 'video_player';
        $description = 'video_description';
    } elseif($file->isAudio()) {
        $preview = 'audio_player';
        $description = 'audio_description';
    } else {
        $preview = false;
        $description = false;
    }
    $app->render(
        'file_info.tpl',
        array(
            'file'=>$file,
            'title'=>$title,
            'login'=>$login,
            'loginEmail'=>$loginEmail,
            'loginPassword'=>$loginPassword,
            'loginError'=>$loginError,
            'bookmark'=>$bookmark,
            'preview'=>$preview,
            'description'=>$description,
        )
    );
});

$app->run();
