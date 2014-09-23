<!doctype html>
<html>
    <head>
        <title>Bolklogin</title>
        <style>
            .error { border-radius: 0.25em; padding: 1em; background-color: red; color: white; }
        </style>
    </head>
    <body>
        <h1>Bolklogin</h1>
        <p>
            Je bent hier heengestuurd door een applicatie die graag jouw gegevens van De Bolk wilt gebruiken. Log in om je gegevens te delen met deze applicatie. Het is op dit moment niet mogelijk om te beperken welke gegevens er aan de applicatie worden verstrekt. In principe heeft de applicatie onbeperkte toegang tot jouw account. 
        </p>
        <?php if ($error !== null): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="<?= $_SERVER['REQUEST_URI'];?>" method="POST">
            <p>
                <label for="username">Gebruikersnaam</label><br>
                <input type="text" name="username" id="username">
            </p>
            <p>
                <label for="password">Wachtwoord</label><br>
                <input type="password" name="password" id="password">
            </p>
            <p>
                <button type="submit">Inloggen</button>
            </p>
        </form>
    </body>
</html>