<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Загрузка файла</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p>Тут что-то должно быть написано.</p>
			<p><a href="./view">Список последних 100 загруженных файлов</a></p>
		</div>
		<div id="form">
			<form name="upload" action="" method="post" enctype="multipart/form-data">
				<p class="notice">{$noticeMessage}</p>
				<p>
					<label>Выберите файл: <input type="file" name="upload[file1]"></label>
				</p>
				<p>
					<label for="description">Описание (до 255 символов, необязательное поле):</label>
					<div>
						<textarea id="description" name="description"></textarea>
					</div>
				</p>
				<p><input type="submit" value="Загрузить"></p>
			</form>
			<p class="error">{$errorMessage}</p>
		</div>
	</div>
</body>
</html>