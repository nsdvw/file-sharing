<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Подробное описание файла</title>
	<link rel="stylesheet" href="../css/main.css">
</head>
<body>
	<div id="content">
		<a href="../">Назад на главную</a>
		<div class="caption">Общие характеристики</div>
		<table class="description">
			<tr>
				<td class="property">Название</td>
				<td class="value">{htmlspecialchars($file['name'])}</td>
			</tr>
			<tr>
				{if $file['description']}
				<td class="property">Комментарий автора</td>
				<td class="value">{htmlspecialchars($file['description'])}</td>
				{/if}
			</tr>
			<tr>
				<td class="property">Ссылка для скачивания</td>
				<td class="value">
					<a href="../download/{$file['id']}/{$file['name']}">скачать</a>
				</td>
			</tr>
			<tr>
				<td class="property">Тип файла</td>
				<td class="value">{$file['mime_type']}</td>
			</tr>
			<tr>
				<td class="property">Размер</td>
				<td class="value">{$file['size']}</td>
			</tr>
			<tr>
				<td class="property">Дата загрузки</td>
				<td class="value">{$file['upload_time']}</td>
			</tr>
		</table>
	</div>
</body>
</html>