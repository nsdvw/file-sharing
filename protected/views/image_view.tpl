<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Подробное описание изображения</title>
	<link rel="stylesheet" href="../css/main.css">
</head>
<body>
	<div id="wrapper">
		<h3>Описание файла</h3>
		<p class="row">Название: {$file['name']}</p>
		{if $file[0]['description']}
		<p class="row">Комментарий автора: {$file['description']}</p>
		{/if}
		<p class="row">Ссылка для скачивания: 
			<a href="../download/{$file[0]['id']}/{$file['name']}">скачать</a>
		</p>
		<p class="row">Тип: {$file['mime_type']}</p>
		<p class="row">Размер: {$file['size']}</p>
		<p class="row">Дата загрузки: {$file['upload_time']}</p>
		<img class="preview" src="../upload/{$file['id']}_{$file['name']}.txt"
			alt="image">
	</div>
</body>
</html>