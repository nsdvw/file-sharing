<div id="content">
    <a href="../">Назад на главную</a>
    <div class="caption">Общие характеристики</div>
    <table class="description">
        <tr>
            <td class="property">Название</td>
            <td class="value">{$file->name|escape|truncate:25}</td>
        </tr>
        {if $file->description}
        <tr>
            <td class="property">Комментарий автора</td>
            <td class="value">{$file->description|escape}</td>
        </tr>
        {/if}
        <tr>
            <td class="property">Ссылка для скачивания</td>
            <td class="value">
                <a href="../download/{$file->id}/{$file->name}">скачать</a>
            </td>
        </tr>
        <tr>
            <td class="property">Тип файла</td>
            <td class="value">{$file->mime_type}</td>
        </tr>
        <tr>
            <td class="property">Размер</td>
            <td class="value">{$file->size}</td>
        </tr>
        <tr>
            <td class="property">Дата загрузки</td>
            <td class="value">{$file->upload_time}</td>
        </tr>
    </table>
    
    {if $preview !== false}
        {include file="$preview.tpl"}
    {/if}

    {if $description !== false}
        {include file="$description.tpl"}
    {/if}

</div>