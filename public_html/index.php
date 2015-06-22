<?php
$loader = require '../protected/vendor/autoload.php';

$app = new \Slim\Slim( array(
    'view' => new \Slim\Views\Smarty(),
));

/* Соединение с бд в синглтон, там же разбираем конфиги */
$app->container->singleton('connection', function(){
	try {
		$db_config = parse_ini_file('../protected/config.txt');
		return new \PDO(
						$db_config['conn'],
						$db_config['user'],
						$db_config['pass']
					);
	} catch (Exception $e) {
		die('Something has happened... Please try again or come back later.');
	}
});

$app->get('/', function() use ($app) {
    $app->render(
    	'../protected/views/index.tpl',
    	array('noticeMessage'=>'', 'errorMessage'=>'')
   	);
});

$app->post('/', function() use ($app) {
	$error = $_FILES['upload']['error']['file1'];
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
		$mapper = new \Model\File\Mapper($app->connection);
		$file = new \Model\File\File($name, $tmp_name, $description);
		$app->connection->beginTransaction();
		$id = $mapper->save($file);
		if (move_uploaded_file(
				$tmp_name,
				"upload/{$id}_{$name}.txt"
			))
		{
			$app->connection->commit();
			$app->render(
				'../protected/views/index.tpl',
				array(
					'noticeMessage'=>'Файл был успешно загружен на сервер.',
					'errorMessage'=>'',
				)
			);
		} else {
			$app->connection->rollBack();
			$app->render(
				'../protected/views/index.tpl',
				array(
					'noticeMessage'=>'',
					'errorMessage'=>'Файл не был загружен.
					Ошибка на сервере: нет прав на запись,
					либо директория не существует.',
				)
			);
		}
	}
});

$app->get('/full-size', function() use ($app) {
	echo '<meta charset="utf-8">Здесь планируется галерея.';
});

$app->get('/full-size/:id', function($id) use ($app) {
	$mapper = new \Model\File\Mapper($app->connection);
	if (!$file = $mapper->findById($id)) {
		header( "HTTP/1.1 404 Not Found" );
  		exit();
	}
	$app->render('../protected/views/gallery.tpl', array('file'=>$file));
});

$app->get('/view', function() use ($app) {
	$mapper = new \Model\File\Mapper($app->connection);
	$list = $mapper->findAll();
	$list = array_map(
			function($el){
				$el['properties'] = json_decode($el['properties']);
				$el['properties']->size = 
					\Model\File\MediaInfo::formatSize($el['properties']->size);
				if (mb_strlen($el['name']) > 50) {
					mb_internal_encoding("UTF-8");
					$el['name'] = mb_substr($el['name'], 0, 50) . '...';
				}
				return $el;
			},
			$list
	);
    $app->render('../protected/views/list.tpl', array('list'=>$list));
});

$app->get('/view/:id', function ($id) use ($app) {
	$mapper = new \Model\File\Mapper($app->connection);
	if ( !($file = $mapper->findById($id)) ) {
		header( "HTTP/1.1 404 Not Found" );
  		exit();
	}
	$file['properties'] = json_decode($file['properties']);
	$file['properties']->size = 
		\Model\File\MediaInfo::formatSize($file['properties']->size);
	//$ua_info = parse_user_agent();
    /* Выбор шаблона в зависимости от типа файла */
    if (in_array($file['properties']->mime_type, array('image/jpeg', 'image/gif',
    									  'image/png', 'image/tiff',
    	)))
    {
    	$app->render('../protected/views/image_view.tpl', array('file'=>$file));
    } elseif(in_array($file['properties']->mime_type, array(
    		'video/webm', 'video/mp4', 'application/ogg',
    	)))
    {
    	$ua_info = parse_user_agent();
    	if(($file['properties']->mime_type == 'video/mp4') and
    		$ua_info['browser'] == 'Opera')
    	{
    		$app->render('../protected/views/detail_view.tpl', array('file'=>$file));
    	}else{
    		$app->render('../protected/views/video_view.tpl', array('file'=>$file));
    	}
    } else {
    	$app->render('../protected/views/detail_view.tpl', array('file'=>$file));
    }
});

$app->run();