<?php
define('BASE_DIR', '../protected');
define('UPLOAD_DIR', 'upload');
define('DOWNLOAD_DIR', 'download');
define('PREVIEW_DIR', 'preview');

$loader = require BASE_DIR.'/vendor/autoload.php';

$smarty = new \Slim\Views\Smarty();
//$smarty->caching = false;
// я не понял, как теперь напрямую обратиться к объекту смарти?
$app = new \Slim\Slim( array(
    'view' => $smarty,
    'templates.path' => BASE_DIR.'/views',
    'debug' => true,
));

$baseUrl = $app->request->getUrl();
$app->view->appendData( array(
    'baseUrl' => $baseUrl,
));

$app->notFound(function () use ($app) {
    $app->render('404.php');
});

$app->container->singleton('connection', function(){
        $db_config = parse_ini_file(BASE_DIR.'/config.ini');
        return new \PDO(
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

$app->post('/', function() use ($app) {
    $error = $_FILES['upload']['error']['file1'];
    $name = $_FILES['upload']['name']['file1'];
    $tmp_name = $_FILES['upload']['tmp_name']['file1'];
    $description = (isset($_POST['description']) and $_POST['description']!=='') 
                        ? $_POST['description'] : null;
    if ($error) {
        $app->response->redirect("/?error=$error");
    } else {
        $mapper = new \Storage\Model\FileMapper($app->connection);
        $file = \Storage\Model\File::fromUser($name, $tmp_name, $description);
        $app->connection->beginTransaction();
        $mapper->save($file);
        if (move_uploaded_file(
            $tmp_name,
            UPLOAD_DIR . '/' .
            \Storage\Helper\ViewHelper::getUploadName($file->id, $file->name)))
        {
            $app->connection->commit();
            $app->response->redirect("/?notice=ok");
        } else {
            $app->connection->rollBack();
            $app->response->redirect("/?error=server_error");
        }
    }
});

$app->get('/full-size/:id', function($id) use ($app) {
    $mapper = new \Storage\Model\FileMapper($app->connection);
    if (!$file = $mapper->findById($id)) {
        $app->notFound();
    }
    $title = 'Просмотр изображения';
    $app->render('image_fullsize.tpl', array(
        'file'=>$file,
        'title'=>$title,
    ));
});

$app->get('/view', function() use ($app) {
    $mapper = new \Storage\Model\FileMapper($app->connection);
    $list = $mapper->findAll();
    $title = 'Список файлов на сервере';
    $app->render('list_info.tpl', array(
        'list'=>$list,
        'title'=>$title,
    ));
});

$app->get('/view/:id', function ($id) use ($app) {
    $mapper = new \Storage\Model\FileMapper($app->connection);
    if (!$file = $mapper->findById($id)) {
        $app->notFound();
    }
    $title = 'Информация о файле';

    if ($file->isImage()) {
        $path = PREVIEW_DIR."/{$id}.txt";
        if (!\Storage\Helper\PreviewGenerator::hasPreview($path)) {
            \Storage\Helper\PreviewGenerator::createPreview($file);
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