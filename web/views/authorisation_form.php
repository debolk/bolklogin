<!doctype html>
<html>
    <head>
        <title>Bolklogin</title>

        <link rel="icon" href="/images/favicon.png">

        <style><?php include "stylesheets/application.css"; ?></style>
    </head>
    <body>
        <h1>Bolklogin</h1>
        <p>
            <strong><?= $_GET['client_id']; ?></strong> heeft toegang tot je Bolkaccount nodig.
        </p>

        <?php if ($error !== null): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="<?= $_SERVER['REQUEST_URI'];?>" method="POST">

            <?php if ($current_user): ?>
                <p>
                    Je bent ingelogd als <?=$current_user;?>.
                    <button type="submit" name="logout" value=1 />Uitloggen</button>
                </p>
            <?php else: ?>
                <div>
                    <label for="username">Gebruikersnaam</label><br>
                    <input type="text" name="username" id="username">
                </div>
                <div>
                    <label for="password">Wachtwoord</label><br>
                    <input type="password" name="password" id="password">
                </div>
            <?php endif; ?>
            <div>
                <button type="submit" name="authorization" value="1" class="yes">Ja, ik geef toestemming</button>
                <button type="submit" name="authorization" value="0" class="no">Nee, ik geef geen toestemming</button>
            </div>
        </form>
    </body>
</html>
