{include file="header.tpl"}
<a href="{$baseUrl}">Назад на главную</a>
<h3>Список последних 100 загруженных на сервер файлов</h3>
<table id="list-items">
    <tr>
        <th> # </th>
        <th>Имя</th>
        <th>Тип файла</th>
        <th>Размер</th>
        <th>Скачиваний</th>
        <th>Дата загрузки</th>
    </tr>
    {foreach $list as $item}
    <tr>
        <td>{counter}</td>
        <td><a href="view/{$item->id}">{$item->name|truncate:25|escape}</a></td>
        <td class="item-description">{$item->mime_type}</td>
        <td class="size">
            {\Storage\Helper\ViewHelper::formatSize($item->size)}
        </td>
        <td class="counter">{$item->download_counter}</td>
        <td class="time">{$item->upload_time}</td>
    </tr>
    {/foreach}
</table>
{include file="footer.tpl"}