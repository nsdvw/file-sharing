<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="../css/main.css">
</head>
<body>
	<h3>Описание файла</h3>
	Название: {$file[0]['name']}<br>
	Ссылка для скачивания: <a href="../upload/{$file[0]['id']}_{$file[0]['name']}">скачать</a><br>
	Дата загрузки: {$file[0]['upload_time']} <br>
	Предпросмотр изображения: <br>
	<img class="preview" src="../upload/{$file[0]['id']}_{$file[0]['name']}" alt="image">
</body>
</html>