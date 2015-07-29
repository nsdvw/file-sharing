{include file="header.tpl"}
<div id="container-narrow">
    {if $login !== true}
    <div class="authentication">
        <form action="" method="post" name="login" class="loginForm">
            <input type="text" name="login[email]" placeholder="email">
            <input type="password" name="login[password]" placeholder="password">
            <input type="submit" value="Login" class="small-button">
        </form>
    </div>
    {/if}
<div class="logo1">
    <div id="folder-main-part"></div>
    <div id="folder-bookmark"></div>
    <div id="initial-letter">f</div>
    <div id="logo-text">ile-sharing</div>
</div>
<form class="upload-form" name="upload" method="post" action="" enctype="multipart/form-data">
    <div class="input-file"></div>
    <div class="input-button">+</div>
    <input type="file" required id="file1" name="upload[file1]">
    <div class="agree"><label>
        <input type="checkbox" name="upload[agree]" checked>
        Agree with TOS</label>
    </div>
    <input type="submit" value="Upload" class="big-button" id="send">
</form>
<p class="notice" id="notice">{$noticeMessage}</p>
<p class="error" id="error">{$errorMessage}</p>
<div id="progressBox" class="progressBox">
    <div id="progressBar" class="progressBar"></div>
</div>
<script src="{$baseUrl}/js/main.js"></script>
{include file="footer.tpl"}