{include file="header.tpl"}
<div class="narrow">
    <div id="header">
        <p><a href="./view">Список последних 100 загруженных файлов</a></p>
    </div>
    <div id="form">
        <form name="upload" action="" method="post" enctype="multipart/form-data">
            <p>
                <label>Выберите файл: <input type="file" 
                    id="file1" name="upload[file1]"></label>
            </p>
            <p>
                <label for="description">
                    Описание (до 255 символов, необязательное поле):
                </label>
                <div>
                    <textarea id="description" name="description"></textarea>
                </div>
            </p>
            <p><input type="submit" value="Загрузить" id="send"></p>
        </form>
        <p class="notice" id="notice">{$noticeMessage}</p>
        <p class="error" id="error">{$errorMessage}</p>
        <div id="progressBox" class="progressBox">
            <div id="progressBar" class="progressBar"></div>
        </div>
    </div>
</div>
<script src="{$baseUrl}/js/main.js"></script>
{include file="footer.tpl"}