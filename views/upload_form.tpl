{include file="header.tpl"}
<div id="container-narrow">
    {include file="logo1.tpl"}
    <form class="upload-form" name="upload" method="post" action="{$baseUrl}/upload_file" enctype="multipart/form-data">
        <div class="input-file" id="fakeInputFile">
            <span class="input-file-name" id="inputFileName"></span>
        </div>
        <div class="input-button">+</div>
        <input type="file" required id="file1" name="upload[file1]">
        <div class="agree"><label>
            <input type="checkbox" name="upload[agree]" checked id="agreeBox">
            Agree with TOS</label>
        </div>
        <input type="submit" value="Upload" class="big-button" id="send">
        <div class="error" id="upload-error">{$uploadError}</div>
    </form>
    <div id="progressBox" class="progressBox">
        <div id="progressBar" class="progressBar"></div>
    </div>
    <div id="notice"></div>
    <script src="{$baseUrl}/js/main.js"></script>
</div>
{include file="footer.tpl"}