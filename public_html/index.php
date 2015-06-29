<?php
$loader = require '../protected/vendor/autoload.php';

$smarty = new \Slim\Views\Smarty();
//$smarty->caching = false;
// я не понял, как теперь напрямую обратиться к объекту смарти?
$app = new \Slim\Slim( array(
    'view' => $smarty,
    'templates.path' => '../protected/views',
    'debug' => true,
));

$app->notFound(function () use ($app) {
    $app->render('404.php');
});

$app->container->singleton('connection', function(){
        $db_config = parse_ini_file('../protected/config.ini');
        return new \PDO(
                        $db_config['conn'],
                        $db_config['user'],
                        $db_config['pass']
                    );
});

$app->get('/', function() use ($app) {
    $title = 'Загрузка нового файла';
    $errorMessage = "Файл не был загружен. Код ошибки: ";
    $errorMessage = (isset($_GET['error']))
                    ? ('Ошибка. Файл не был загружен. Попробуйте снова.') : '';
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
    if($error){
        $app->response->redirect("/?error=$error");
    }else{
        $mapper = new \Storage\Model\FileMapper($app->connection);
        $file = \Storage\Model\File::fromUser($name, $tmp_name, $description);
        $app->connection->beginTransaction();
        $mapper->save($file);
        if (move_uploaded_file($tmp_name, "upload/{$file->id}_{$name}.txt")) {
            $app->connection->commit();
            $app->response->redirect("/?notice=ok");
        } else {
            $app->connection->rollBack();
            $app->response->redirect("/?error=server_error");
        }
    }
});

$app->get('/full-size/:id', function($id) use ($app) {
    $mapper = new \Model\File\Mapper($app->connection);
    if (!$file = $mapper->findById($id)) {
        $app->notFound();
    }
    //$app->render('gallery.tpl', array('file'=>$file));
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

    if (in_array($file->mime_type, array(
            'image/jpeg', 'image/gif', 'image/png',
        )))
    {
        $preview = 'image_preview';
        $description = 'image_description';
        $app->render('file_info.tpl', array(
            'file'=>$file,
            'title'=>$title,
            'preview'=>$preview,
            'description'=>$description,
        ));
    } elseif(in_array($file->mime_type, array(
            'video/webm', 'video/mp4', 'video/ogg',
        )))
    {
        $preview = 'video_player';
        $description = 'video_description';
        $app->render('file_info.tpl', array(
            'file'=>$file,
            'title'=>$title,
            'preview'=>$preview,
            'description'=>$description,
        ));
    }else{
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