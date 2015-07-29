{include file="header.tpl"}
<div id="container-wide">
    {if $login !== true}
    <div class="authentication">
        <form action="" method="post" name="login" class="loginForm">
            <input type="text" name="login[email]" placeholder="email">
            <input type="password" name="login[password]" placeholder="password">
            <input type="submit" value="Login" class="small-button">
        </form>
    </div>
    {/if}
<div class="logo2">
    <div id="folder-main-part"></div>
    <div id="folder-bookmark"></div>
    <div id="initial-letter">f</div>
    <div id="logo-text">ile-sharing</div>
</div>
<div class="content">
    <div class="files-list">
{foreach $list as $item}
    <div class="file-item">
        <div class="file-type-icon file-type-icon-image"></div>
        <div class="file-name">{$item->name|truncate:25|escape}</div>
        <div class="file-date">{$item->upload_time}</div>
        <div class="file-size">
            {\Storage\Helper\ViewHelper::formatSize($item->size)}
        </div>
        <div class="file-download-icon"></div>
    </div>
{/foreach}
        <div class="pager">
            <div class="pager-previous">Previous</div>
            <div class="pager-pages">
                <span class="pager-number">1</span>
                <span class="pager-number">2</span>
                <span class="pager-number">3</span>
                <span class="pager-number">4</span>
                <span class="pager-number">5</span>
                <span class="pager-number">6</span>
                <span class="pager-number">...</span>
                <span class="pager-number">324</span>
                <span class="pager-number">325</span>
            </div>
            <div class="pager-next">Next</div>
            <div class="pager-last">Last</div>
        </div>
    </div>
    <div class="preview">
        <img src="/image/mononoke.jpg" alt="image">
        <div class="preview-name">
            mononoke_art.jpg
        </div>
        <div class="preview-size">Size: 824Kb</div>
        <div class="preview-date">Uploaded: 23-07-2015 18:45</div>
        <div class="preview-downloads">Downloads: 245</div>
        <div class="preview-format">Format: image/jpg</div>
        <div class="preview-resolution">Resolution: 1590 &times; 1920</div>
        <div class="preview-more">more...</div>
    </div>
    <div class="clearDummy"></div>
</div>
{include file="footer.tpl"}