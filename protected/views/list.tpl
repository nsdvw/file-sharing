<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Последние загруженные файлы</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<div id="content">
		<a href="../">Назад на главную</a>
		<h3>Список последних 100 загруженных на сервер файлов</h3>
		<table id="list-items">
			<tr>
				<th>#</th>
				<th>Имя</th>
				<th>Тип файла</th>
				<th>Ссылка на подробное описание</th>
				<th>Размер</th>
				<th>Дата загрузки</th>
			</tr>
			{$i = 1}
			{foreach $list as $item}
			<tr>
				<td>
					{$i++}
				</td>
				<td>
					{$item.name}
				</td>
				<td class="item-description">
					{$item.mime_type}
				</td>
				<td class="link">
					<a href="view/{$item.id}">подробное описание</a>
				</td>
				<td class="size">
					{$item.size}
				</td>
				<td class="time">
					{$item.upload_time}
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
</body>
</html>