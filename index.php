<?php
$loader = require __DIR__ . '/protected/vendor/autoload.php';

$app = new \Slim\Slim( array(
    'view' => new \Slim\Views\Smarty(),
));

$app->get('/', function () use ($app) {
    $app->render(
    	__DIR__ . '/protected/views/index.tpl',
    	array('noticeMessage'=>'', 'errorMessage'=>'')
   	);
});

$app->post('/', function () use ($app) {
	$error = $_FILES['upload']['error']['file1'];
	$type = $_FILES['upload']['type']['file1'];
	$name = $_FILES['upload']['name']['file1'];
	$tmp_name = $_FILES['upload']['tmp_name']['file1'];
	$description = (isset($_POST['description']) and $_POST['description']!=='') 
						? $_POST['description'] : null;
	if($error){
		$app->render(
    		__DIR__ . '/protected/views/index.tpl',
    		array(
				'noticeMessage'=>'',
				'errorMessage'=>"Файл не был загружен. Код ошибки: $error",
			)
   		);
	}else{
		$model = new \Model\File\Mapper();
		$model->save($name, null, $description);
		move_uploaded_file($tmp_name, __DIR__ . '/upload/'.$model->id.'_'.$name);
		$app->render(
			__DIR__ . '/protected/views/index.tpl',
			array(
				'noticeMessage'=>'Файл был успешно загружен на сервер.',
				'errorMessage'=>''
			)
		);
	}
});

$app->get('/view', function () use ($app) {
	$model = new \Model\File\Mapper();
	$list = $model->find();
    $app->render(__DIR__ . '/protected/views/list.tpl', array('list'=>$list));
});

$app->get('/view/:id', function ($id) use ($app) {
	$model = new \Model\File\Mapper();
	$file = $model->find($id);
	$finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file(__DIR__ . '/upload/'.$file[0]['id'].'_'.$file[0]['name']);
    if(in_array($mime, array('image/jpeg', 'image/gif', 'image/png'))){
    	$app->render(__DIR__ . '/protected/views/image_view.tpl', array('file'=>$file));
    }else{
    	$app->render(__DIR__ . '/protected/views/detail_view.tpl', array('file'=>$file));
    }
});

$app->run();