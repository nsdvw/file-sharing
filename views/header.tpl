<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <link rel="stylesheet" href="{$baseUrl}/css/main.css">
    <script src="{$baseUrl}/js/lib.js"></script>
</head>
<body>
    <div id="wrapper">
        <header>
            <div class="sitename">Название файлообменника</div>
            {if $login !== true}<a href="/reg">Регистрация</a>{else}
                <a href="/?logout">Выйти</a>
            {/if}
        </header>
        <div id="content">
            {if $login !== true}
            <div id="login">
                <form action="" method="POST" name="login">
                    <label>Имейл: <input type="text" name="login[email]"><br>
                    <label>Пароль: <input type="text" name="login[password]"><br>
                    <input type="submit" value="вход">
                </form>
            </div>
            {/if}