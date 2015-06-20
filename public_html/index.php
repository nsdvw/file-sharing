<?php
$loader = require '../protected/vendor/autoload.php';

$app = new \Slim\Slim( array(
    'view' => new \Slim\Views\Smarty(),
));

/* Соединение с бд в синглтон, там же разбираем конфиги */
$app->container->singleton('connection', function(){
	$db_config = parse_ini_file('../protected/config.txt');
	return new \PDO(
					$db_config['conn'],
					$db_config['user'],
					$db_config['pass']
				);
});

$app->get('/', function() use ($app) {
    $app->render(
    	'../protected/views/index.tpl',
    	array('noticeMessage'=>'', 'errorMessage'=>'')
   	);
});

$app->post('/', function() use ($app) {
	/* Собираем данные из FILES и POST */
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

$app->get('/view', function() use ($app) {
	$mapper = new \Model\File\Mapper($app->connection);
	$list = $mapper->findAll();
    $app->render('../protected/views/list.tpl', array('list'=>$list));
});

$app->get('/view/:id', function ($id) use ($app) {
	$mapper = new \Model\File\Mapper($app->connection);
	$file = $mapper->findById($id);
	/* Форматирование сырых данных перед выводом в шаблон
	(я бы вынес это в отдельный метод контроллера, но тут нет контроллера)
	*/
    if ($file['size'] > pow(1024, 3)) {
    	$file['size'] = round($file['size'] / pow(1024, 3), 2) . ' Гб';
    } elseif ($file['size'] > pow(1024, 2)) {
    	$file['size'] = round($file['size'] / pow(1024, 2), 2) . ' Мб';
    }elseif ($file['size'] > 1024) {
    	$file['size'] = round($file['size'] / 1024, 2) . ' Кб';
    }
    /* Выбор шаблона в зависимости от типа файла */
    if (in_array($file['mime_type'], array('image/jpeg', 'image/gif',
    									  'image/png', 'image/tiff',
    ))) {
    	$app->render('../protected/views/image_view.tpl', array('file'=>$file));
    } else {
    	$app->render('../protected/views/detail_view.tpl', array('file'=>$file));
    }
});

$app->run();