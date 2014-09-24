<!doctype html>
<html>
    <head>
        <title>Bolklogin</title>
        <style>
            .error { border-radius: 0.25em; padding: 1em; background-color: red; color: white; }
            .warning { border-radius: 0.25em; padding: 1em; background-color: #ffffd5; color: black; }
        </style>
    </head>
    <body>
        <h1>Bolklogin</h1>
        <p class="warning">
            <strong>Let op:</strong> <?= $_GET['client_id']; ?> krijgt toegang tot al jouw gegevens in de ledenadminstratie.
        </p>
        <form action="<?= $_SERVER['REQUEST_URI'];?>" method="POST">
            <p>
                <label for="authorization">Ik geef toestemming</label>
                <input type="radio" name="authorization" value="1"> Ja
                <input type="radio" name="authorization" value="0"> Nee
            </p>
            <p>
                <button type="submit">Versturen</button>
            </p>
        </form>
    </body>
</html>