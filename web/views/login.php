<!doctype html>
<html>
    <head>
        <title>Bolklogin</title>
        <link rel="stylesheet" href="stylesheets/application.css">
    </head>
    <body>
        <h1>Bolklogin</h1>
        <p>
            Je bent hierheen gestuurd door een applicatie die graag toegang wilt tot jouw Bolk-account. Log in om je gegevens te delen met deze applicatie.
        </p>
        <?php if ($error !== null): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="<?= $_SERVER['REQUEST_URI'];?>" method="POST">
            <div>
                <label for="username">Gebruikersnaam</label><br>
                <input type="text" name="username" id="username">
            </div>
            <div>
                <label for="password">Wachtwoord</label><br>
                <input type="password" name="password" id="password">
            </div>
            <div>
                <button type="submit" class="login">Inloggen</button>
            </div>
        </form>
    </body>
</html>