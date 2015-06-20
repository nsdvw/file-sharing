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
					{htmlspecialchars($item.name)}
				</td>
				<td class="item-description">
					{$item.mime_type}
				</td>
				<td class="link">
					<a href="view/{$item.id}">подробное описание</a>
				</td>
				<td class="size">
				{if $item.size > pow(1024, 3)}
					{round($item.size / pow(1024, 3), 2)} Гб
				{elseif $item.size > pow(1024, 2)}
					{round($item.size / pow(1024, 2), 2)} Мб
				{else}
					{round($item.size / 1024, 2)} Кб
				{/if}
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