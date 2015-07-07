<?php
use Slim\Slim;
use Slim\Views\Smarty;
use Storage\Model\File;
use Storage\Model\MediaInfo;
use Storage\Model\FileMapper;
use Storage\Helper\ViewHelper;
use Storage\Helper\PreviewGenerator;

define('UPLOAD_DIR', 'upload');
define('PREVIEW_DIR', 'preview');
define('DOWNLOAD_DIR', 'download');
define('BASE_DIR', '../protected');

$loader = require BASE_DIR.'/vendor/autoload.php';

$app = new Slim(
    array(
        'view' => new \Slim\Views\Smarty(),
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

$app->container->singleton('connection', function(){
        $db_config = parse_ini_file(BASE_DIR.'/config.ini');
        return new PDO(
                        $db_config['conn'],
                        $db_config['user'],
                        $db_config['pass']
                    );
});

$app->get('/', function() use ($app) {
    $title = 'Загрузить файл на сервер';
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
        $mapper = new FileMapper($app->connection);
        $file = File::fromUser($name, $tmp_name, $description);
        $app->connection->beginTransaction();
        $mapper->save($file);
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

$app->post('/', function() use ($app) {
    $error = $_FILES['upload']['error']['file1'];
    $name = $_FILES['upload']['name']['file1'];
    $tmp_name = $_FILES['upload']['tmp_name']['file1'];
    $description = (isset($_POST['description']) and $_POST['description']!=='') 
                        ? $_POST['description'] : null;
    if ($error) {
        $app->response->redirect("/?error=$error");
    } else {
        $mapper = new FileMapper($app->connection);
        $file = File::fromUser($name, $tmp_name, $description);
        $app->connection->beginTransaction();
        $mapper->save($file);
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
});
/*
$app->get('/full-size/:id', function($id) use ($app) {
    $mapper = new FileMapper($app->connection);
    if (!$file = $mapper->findById($id)) {
        $app->notFound();
    }
    $title = 'Просмотр изображения';
    $app->render('image_fullsize.tpl', array(
        'file'=>$file,
        'title'=>$title,
    ));
});*/

$app->get('/view', function() use ($app) {
    $mapper = new FileMapper($app->connection);
    $list = $mapper->findAll();
    $title = 'Список файлов на сервере';
    $app->render('list_info.tpl', array(
        'list'=>$list,
        'title'=>$title,
    ));
});

$app->get('/download/:id/:name', function ($id, $name) use ($app){
    $mapper = new FileMapper($app->connection);
    $mapper->updateCounter($id);
    header('X-SendFile: '.'..'.DIRECTORY_SEPARATOR.
        ViewHelper::getUploadPath($id, $name));
    header('Content-Disposition: attachment');
    exit;
});

$app->get('/view/:id', function ($id) use ($app) {
    $mapper = new FileMapper($app->connection);
    if (!$file = $mapper->findById($id)) {
        $app->notFound();
    }
    $title = 'Информация о файле';

    if ($file->isImage()) {
        $path = ViewHelper::getPreviewPath($id);
        if (!PreviewGenerator::hasPreview($path)) {
            PreviewGenerator::createPreview($file);
        }
        $preview = 'image_preview';
        $description = 'image_description';
        $app->render('file_info.tpl', array(
            'file'=>$file,
            'title'=>$title,
            'preview'=>$preview,
            'description'=>$description,
        ));
    } elseif ($file->isVideo()) {
        $preview = 'video_player';
        $description = 'video_description';
        $app->render('file_info.tpl', array(
            'file'=>$file,
            'title'=>$title,
            'preview'=>$preview,
            'description'=>$description,
        ));
    } else {
        $preview = false;
        $description = false;
        $app->render('file_info.tpl', array(
            'file'=>$file,
            'title'=>$title,
            'preview'=>$preview,
            'description'=>$description,
        ));
    }
});

$app->run();