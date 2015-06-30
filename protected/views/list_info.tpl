{include file="header.tpl"}
<a href="../">Назад на главную</a>
<h3>Список последних 100 загруженных на сервер файлов</h3>
<table id="list-items">
    <tr>
        <th> # </th>
        <th>Имя</th>
        <th>Тип файла</th>
        <th>Ссылка на подробное описание</th>
        <th>Размер</th>
        <th>Дата загрузки</th>
    </tr>
    {foreach $list as $item}
    <tr>
        <td>{counter}</td>
        <td>{$item->name|escape|truncate:25}</td>
        <td class="item-description">{$item->mime_type}</td>
        <td class="link">
            <a href="view/{$item->id}">подробное описание</a>
        </td>
        <td class="size">
            {\Storage\Helper\ViewHelper::formatSize($item->size)}
        </td>
        <td class="time">{$item->upload_time}</td>
    </tr>
    {/foreach}
</table>
{include file="footer.tpl"}