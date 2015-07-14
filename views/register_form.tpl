{include file="header.tpl"}
<div class="narrow">
    <div class="register">
        <form action="" method="POST" name="register" id="registerForm">
            <p>
                <label>Логин:<br>
                    <input type="text" name="register[login]">
                </label>
            </p>
            <p>
                <label>Имейл:<br>
                    <input type="text" name="register[email]">
                </label>
            </p>
            <p>
                <label>Пароль:<br>
                    <input type="text" name="register[password]">
                </label>
            </p>
            <p><input type="submit"></p>
        </form>
    </div>
</div>
{include file="footer.tpl"}