<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<div id="content">
		<h3>Список последних 100 загруженных на сервер файлов</h3>
		<table id="list-items">
			<tr>
				<th>#</th>
				<th>Комментарий автора</th>
				<th>Ссылка на подробное описание</th>
				<th>Дата загрузки</th>
			</tr>
			{$i = 1}
			{foreach $list as $item}
			<tr>
				<td>
					{$i++}
				</td>
				<td class="item-description">
					{if $item.description}
						{$item.description}
					{/if}
				</td>
				<td class="link">
					<a href="view/{$item.id}">подробное описание</a>
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