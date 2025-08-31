<!doctype html>
<html>
	<head>
		<title>Bolklogin</title>

		<link rel="icon" href="/images/favicon.png">

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<style><?php include "stylesheets/application.css"; ?></style>
	</head>
	<body>
		<h1>Bolklogin</h1>
		<p>
			<strong><?= $_GET['client_id']; ?></strong> requests access to your account.
		</p>

		<?php if ($error !== null): ?>
			<p class="error"><?php echo $error; ?></p>
		<?php endif; ?>
		<?php if ($msg !== null): ?>
            <p class="message"><?php echo $msg; ?></p>
		<?php endif; ?>

		<form action="<?= $_SERVER['REQUEST_URI']; ?>" method="POST">

			<?php if ($user): ?>
				<p>
					Welcome back, <?=$user_fullname;?>.
					<button type="submit" name="logout" value=1/>Log out</button>
				</p>
			<?php else: ?>
				<div>
					<label for="username">Username</label><br>
					<input type="text" name="username" id="username">
				</div>
				<div>
					<label for="password">Password</label><br>
					<input type="password" name="password" id="password">
				</div>
			<?php endif; ?>
            <?php if (!isset($error) || !str_contains($error, "Please change your password")): ?>
                <div>
                    <button type="submit" name="authorization" value="1" class="yes">Yes, I give access</button>
                    <button type="submit" name="authorization" value="0" class="no">No, I do not give access</button>
                </div>
            <?php endif; ?>
		</form>
	</body>
</html>
