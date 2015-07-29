<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <link rel="stylesheet" href="{$baseUrl}/css/main.css">
    <link type="text/css" href="/css/jplayer.pink.flag.min.css" rel="stylesheet">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery.jplayer.min.js"></script>
    <script src="{$baseUrl}/js/lib.js"></script>
</head>
<body>
    <div id="wrapper">
        <header>
            <div id="mainMenu">
                <div class="menuItems">
                    <div class="activeBookmark">Upload</div>
                    <div class="inactiveBookmark">Files</div>
                    <div class="inactiveBookmark">TOS</div>
                    {if $login !== true}
                    <div class="inactiveBookmark">
                        <a href="{$baseUrl}/reg">Sign up</a></div>
                    {else}
                    <div class="inactiveBookmark">
                        <a href="{$baseUrl}/?logout">Log out</a>
                    </div>
                    {/if}
                    <div class="clearDummy"></div>
                </div>
            </div>
        </header>
        