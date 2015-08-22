{include file="header.tpl"}
<div id="container-wide">
<div class="caption">File description</div>
<table class="description">
    <tr>
        <td class="property">Name</td>
        <td class="value">{$file->name|truncate:50|escape}</td>
    </tr>
    <tr>
        <td class="property">File type</td>
        <td class="value">{$file->mime_type}</td>
    </tr>
    {if $description !== false}
        {include file="$description.tpl"}
    {/if}
    <tr>
        <td class="property">Downloads</td>
        <td class="value" id="counter">{$file->download_counter}</td>
    </tr>
    <tr>
        <td class="property">Uploaded</td>
        <td class="value">{$file->upload_time}</td>
    </tr>
    <tr>
        <td class="property">Size</td>
        <td class="value">
            {\Storage\Helper\ViewHelper::formatSize($file->size)}
        </td>
    </tr>
    <tr>
        <td class="property"></td>
        <td class="value">
            <a href=
        "{$baseUrl}/{\Storage\Helper\ViewHelper::getDownloadUrl($file->id, $file->name)}"
            id="dowloadLink">download</a>
        </td>
    </tr>
</table>

{if $preview !== false}
    {include file="$preview.tpl"}
{/if}

<form action="" method="POST" name="comment_form" class="comment-form">
    <div>
        <textarea name="comment_form[contents]" placeholder="Leave a comment..."
            class="comment-area"></textarea>
        <input type="hidden" name="comment_form[reply_id]" value="{$reply}">
    </div>
    <div class="c-error">{$postError}</div>
    {if $login != true}
        <div class="c-captcha">
            <img src="{$baseUrl}/image/captcha_generator.php" alt="captcha"><br>
            <input type="text" name="comment_form[captcha]">
        </div>
    {/if}
    <input type="submit" class="small-button" value="send">
</form>

<div class="comments">
    {foreach $comments as $comment}
        <div class="comment level-{$comment->level}">
            <div class="c-title">
                <span class="c-author">
                    {$comment->author_id->login|default:'Anonymous'|escape}
                </span>
                <span class="c-added">{$comment->added}</span>
            </div>
            <div class="c-text">{$comment->contents|escape}</div>
            <div class="c-reply">
                <a href="{$baseUrl}/view/{$file->id}?reply={$comment->id}">Reply</a>
            </div>
        </div>
    {/foreach}
</div>

<script src="{$baseUrl}/js/detail_view.js"></script>
</div>
{include file="footer.tpl"}
