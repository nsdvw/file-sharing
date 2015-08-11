{include file="header.tpl"}
<div id="container-wide">
    <div class="{if $login == true}logo3{else}logo2{/if}">
        <div id="folder-main-part"></div>
        <div id="folder-bookmark"></div>
        <div id="initial-letter">f</div>
        <div id="logo-text">ile-sharing</div>
    </div>
    <div class="content">
        <div class="notice" id="notice">{$noticeMessage}</div>
        <div class="files-list">
        {foreach $list as $file}
        <div class="file-item file-nonselected" data-id="{$file->id}">
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
        {if $pageCount > 1}
            <div class="pager">
                {if $currentPage == 1}
                <div class="pager-first not-active">First</div>
                <div class="pager-previous not-active">Previous</div>
                {else}
                <div class="pager-first">
                    <a href="{$baseUrl}/view?page=1">First</a>
                </div>
                <div class="pager-previous">
                    <a href="{$baseUrl}/view?page={$currentPage - 1}">Previous</a>
                </div>
                {/if}
                
                <div class="pager-pages">
                {for $i = $firstPage; $i lte $lastPage; $i++}
                    {if $i == $currentPage}
                    <span class="pager-number current-page">{$i}</span>
                    {else}
                    <span class="pager-number">
                        <a href="{$baseUrl}/view?page={$i}">{$i}</a>
                    </span>
                    {/if}
                {/for}
                </div>

                {if $currentPage != $pageCount}
                <div class="pager-last">
                    <a href="{$baseUrl}/view?page={$pageCount}">Last</a>
                </div>
                <div class="pager-next">
                    <a href="{$baseUrl}/view?page={$currentPage + 1}">Next</a>
                </div>
                {else}
                <div class="pager-last not-active">Last</div>
                <div class="pager-next not-active">Next</div>
                {/if}
            </div>
        {/if}
        </div>
        <div class="preview" id="previewBox"></div>
        <div class="clearDummy"></div>
    </div>
</div>
<script src="{$baseUrl}/js/list.js"></script>
{include file="footer.tpl"}