{include file="header.tpl"}
<div class="container-narrow">
    <div class="logo2">
        <div id="folder-main-part"></div>
        <div id="folder-bookmark"></div>
        <div id="initial-letter">f</div>
        <div id="logo-text">ile-sharing</div>
    </div>
    <div class="register">
        <div class="register-message">Registration allows you to upload files havier than 500Mb, download with maximum speed, comment files without captcha and many other useful things!</div>
        <form action="" method="post" name="register" id="registerForm">
            <div class="register-field-name">
                <label for="register[login]">Login:</label>
            </div>
            <div class="register-field-input">
                <div><input type="text" name="register[login]"></div>
                <div class="register-field-error"></div>
            </div>
            <div class="register-field-name">
                <label for="register[email]">Email:</label>
            </div>
            <div class="register-field-input">
                <div><input type="text" name="register[email]"></div>
                <div class="register-field-error">hello</div>
            </div>
            <div class="register-field-name">
                <label for="register[password]">Password:</label>
            </div>
            <div class="register-field-input">
                <div><input type="password" name="register[password]"></div>
                <div class="register-field-error">world</div>
            </div>
            <div class="register-field-name"></div>
            <div class="register-field-input">
                <div>
                    <input type="submit" value="sign up" class="small-button">
                </div>
            </div>
        </form>
    </div>
</div>
{include file="footer.tpl"}