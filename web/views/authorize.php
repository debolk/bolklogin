<!doctype html>
<html>
    <head>
        <title>Bolklogin</title>
        <link rel="stylesheet" href="stylesheets/application.css">
    </head>
    <body>
        <h1>Toestemming</h1>
        <p class="warning">
            <strong>Let op:</strong> <?= $_GET['client_id']; ?> krijgt toegang tot jouw volledige account.
        </p>
        <form action="<?= $_SERVER['REQUEST_URI'];?>" method="POST">
            <div>
                <button type="submit" name="authorization" value="1" class="yes">Ja, ik geef toestemming</button>
                <button type="submit" name="authorization" value="0" class="no">Nee, ik geef geen toestemming</button>
            </div>
        </form>
    </body>
</html>