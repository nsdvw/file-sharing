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
                <td class="value">{$file.properties->mime_type}</td>
            </tr>
            <tr>
                <td class="property">Размер</td>
                <td class="value">{$file.properties->size}</td>
            </tr>
            <tr>
                <td class="property">Дата загрузки</td>
                <td class="value">{$file['upload_time']}</td>
            </tr>
        </table>

        <div class="caption">Предпросмотр изображения</div>
        <div class="player">
            <video src="../upload/{$file['id']}_{$file['name']}.txt"
            controls="controls"></video>
        </div>

        <div class="caption">Специфические характеристики</div>
        <table class="description">
            <tr>
                <td class="property">Продолжительность</td>
                <td class="value">{$file['properties']->playtime}</td>
            </tr>
            <tr>
                <td class="property">Разрешение</td>
                <td class="value">
                    {$file['properties']->resolution_x} x
                    {$file['properties']->resolution_y}
                </td>
            </tr>
            <tr>
                <td class="property">Частота кадров</td>
                <td class="value">{$file.properties->frame_rate}</td>
            </tr>
        </table>
    </div>  
</body>
</html>