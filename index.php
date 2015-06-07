<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/my_autoloader.php';

$app = new \Slim\Slim( array(
    'view' => new \Slim\Views\Smarty(),
));

$app->get('/', function () use ($app) {
    $app->render(__DIR__ . '/views/index.tpl');
});

$app->post('/', function () use ($app) {
	$types = array('image/jpeg', 'image/gif', 'image/png',
				   'video/webm', 'video/mp4', 'video/ogg');
	$error = $_FILES['upload']['error']['file1'];
	$type = $_FILES['upload']['type']['file1'];
	$name = $_FILES['upload']['name']['file1'];
	$tmp_name = $_FILES['upload']['tmp_name']['file1'];
	$description = (isset($_POST['description']) and $_POST['description']!=='') 
						? $_POST['description'] : null;
	if($error){
		// флеш-сообщение об ошибке во время загрузки
	}elseif(in_array($type, $types)){
		$model = new FileModel();
		$model->save($name, null, $description);
		move_uploaded_file($tmp_name, __DIR__.'/upload/'.$model->id.'_'.$name);
		// файл успешно загружен
	}else{
		// флеш-сообщение о неправильном типе файла
	}
	// header('Location: '.$_SERVER['PHP_SELF']);
});

$app->get('/view', function () use ($app) {
	$model = new FileModel();
	$list = $model->find();
    $app->render(__DIR__ . '/views/list.tpl', array('list'=>$list));
});

$app->get('/view/:id', function ($id) use ($app) {
	$model = new FileModel();
	$file = $model->find($id);
	$finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file(__DIR__.'/upload/'.$file[0]['id'].'_'.$file[0]['name']);
    if(in_array($mime, array('image/jpeg', 'image/gif', 'image/png'))){
    	$app->render(__DIR__ . '/views/image_view.tpl', array('file'=>$file));
    }else{
    	$app->render(__DIR__ . '/views/detail_view.tpl', array('file'=>$file));
    }
});

$app->run();