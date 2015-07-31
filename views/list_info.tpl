{include file="header.tpl"}
<div id="container-wide">
    <div class="logo2">
        <div id="folder-main-part"></div>
        <div id="folder-bookmark"></div>
        <div id="initial-letter">f</div>
        <div id="logo-text">ile-sharing</div>
    </div>
    <div class="notice" id="notice">{$noticeMessage}</div>
    <div class="content">
        <div class="files-list">
        {foreach $list as $file}
        <div class="file-item file-nonselected">
            <div class="file-icon
                {if $file->isImage()}file-icon-image
                {elseif $file->isVideo()}file-icon-video
                {elseif $file->isAudio()}file-icon-audio
                {elseif $file->isText()}file-icon-text
                {elseif $file->isArchive()}file-icon-archive
                {else}file-icon-none
                {/if}">
            </div>
            <div class="file-name">
                <a href="{$baseUrl}{\Storage\Helper\ViewHelper::getDetailViewUrl($file->id)}" target="_blank">
                    {$file->name|truncate:30|escape}
                </a>
            </div>
            <div class="file-date">{$file->upload_time}</div>
            <div class="file-size">
                {\Storage\Helper\ViewHelper::formatSize($file->size)}
            </div>
            <div class="file-download-icon">
                <a href="{\Storage\Helper\ViewHelper::getDownloadUrl($file->id,$file->name)}"></a>
            </div>
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
</div>
<script src="{$baseUrl}/js/list.js"></script>
{include file="footer.tpl"}