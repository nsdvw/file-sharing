<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Подробное описание изображения</title>
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
			{if $file['description']}
			<tr>
				<td class="property">Комментарий автора</td>
				<td class="value">{htmlspecialchars($file['description'])}</td>
			</tr>
			{/if}
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

		<div class="caption">Предпросмотр изображения</div>
		<div class="preview">
			<a href="../full-size/{$file['id']}" target="_blank">
				<img src="../upload/{$file['id']}_{$file['name']}.txt"
							alt="image" width="100%">в полном разрешении</a>
		</div>

		<div class="caption">Специфические характеристики формата</div>
		<table class="description">
			<tr>
				<td class="property">Разрешение</td>
				<td class="value">
					{$file['properties']->video->resolution_x} x
					{$file['properties']->video->resolution_x}
				</td>
			</tr>
			<tr>
				<td class="property">Bits per sample</td>
				<td class="value">
					{$file['properties']->video->bits_per_sample}
				</td>
			</tr>
		</table>
	</div>	
</body>
</html>