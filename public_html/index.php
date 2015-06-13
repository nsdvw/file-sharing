<?php
$loader = require '../protected/vendor/autoload.php';

$app = new \Slim\Slim( array(
    'view' => new \Slim\Views\Smarty(),
));

$app->container->singleton('connection', function(){
	$db_config = parse_ini_file('config.txt');
	return new \PDO(
					$db_config['conn'],
					$db_config['user'],
					$db_config['pass']
				);
});

$app->get('/', function () use ($app) {
    $app->render(
    	'../protected/views/index.tpl',
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
    		'../protected/views/index.tpl',
    		array(
				'noticeMessage'=>'',
				'errorMessage'=>"Файл не был загружен. Код ошибки: $error",
			)
   		);
	}else{
		$model = new \Model\File\Mapper($app->connection);
		$model->save($name, null, $description);
		move_uploaded_file($tmp_name, __DIR__ . '/upload/'.$model->id.'_'.$name);
		$app->render(
			'../protected/views/index.tpl',
			array(
				'noticeMessage'=>'Файл был успешно загружен на сервер.',
				'errorMessage'=>''
			)
		);
	}
});

$app->get('/view', function () use ($app) {
	$model = new \Model\File\Mapper($app->connection);
	$list = $model->find();
    $app->render('../protected/views/list.tpl', array('list'=>$list));
});

$app->get('/view/:id', function ($id) use ($app) {
	$model = new \Model\File\Mapper($app->connection);
	$file = $model->find($id);
	$finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file(__DIR__ . '/upload/'.$file[0]['id'].'_'.$file[0]['name']);
    if(in_array($mime, array('image/jpeg', 'image/gif', 'image/png'))){
    	$app->render('../protected/views/image_view.tpl', array('file'=>$file));
    }else{
    	$app->render('../protected/views/detail_view.tpl', array('file'=>$file));
    }
});

$app->run();
