<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<div id="form">
		<form name="upload" action="" method="post" enctype="multipart/form-data">
			<p>Допустимые форматы: пока только изображение (jpeg, png, gif)</p>
			<p>
				<label>Выберите файл: <input type="file" name="upload[file1]"></label>
			</p>
			<p>
				<label for="description">Описание (до 255 символов, не обязательное поле):</label>
				<div>
					<textarea id="description" name="description"></textarea>
				</div>
			</p>
			<p><input type="submit" value="Загрузить"></p>
		</form>
	</div>
</body>
</html>