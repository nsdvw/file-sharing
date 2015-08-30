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
          <div class="{if $bookmark == 'Upload'}activeBookmark{else}inactiveBookmark{/if}">
            <a href="{$baseUrl}">Upload</a>
          </div>
          <div class="{if $bookmark == 'Files'}activeBookmark{else}inactiveBookmark{/if}">
            <a href="{$baseUrl}/view">Files</a>
          </div>
          {if $loginManager->loggedUser === null}
          <div class="{if $bookmark == 'TOS'}activeBookmark{else}inactiveBookmark{/if}">TOS</div>
          <div class="{if $bookmark == 'Sign up'}activeBookmark{else}inactiveBookmark{/if}">
            <a href="{$baseUrl}/reg">Sign up</a>
          </div>
          {else}
          <div class="{if $bookmark == 'Account'}activeBookmark{else}inactiveBookmark{/if}">Account</div>
          <div class="inactiveBookmark">
            <a href="{$baseUrl}/logout">Log out</a>
          </div>
          {/if}
          <div class="clearDummy"></div>
        </div>
      </div>
    </header>
    {if $loginManager->loggedUser === null}
    <div class="authentication">
      <form action="{$baseUrl}/login" method="post" name="login" class="loginForm">
        <input type="text" name="login[email]"
            placeholder="email" value="{$loginEmail|escape|default:''}">
        <input type="password" name="login[password]" placeholder="password"
            value="{$loginPassword|escape|default:''}">
        <input type="submit" value="Login" class="small-button">
        <div class="loginError">{$loginError|default:''}</div>
      </form>
    </div>
    {/if}
