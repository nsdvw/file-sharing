<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Подробное описание файла</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<div id="wrapper">
		<h3>Описание файла</h3>
		<p class="row">Название: {$file[0]['name']}</p>
		{if $file[0]['description']}
		<p class="row">Комментарий автора: {$file[0]['description']}</p>
		{/if}
		<p class="row">Ссылка для скачивания: 
			<a href="../download/{$file[0]['id']}/{$file[0]['name']}">скачать</a>
		</p>
		<p class="row">Тип: {$file[0]['mime_type']}</p>
		<p class="row">Размер: {$file[0]['size']}</p>
		<p class="row">Дата загрузки: {$file[0]['upload_time']}</p>
	</div>
</body>
</html>