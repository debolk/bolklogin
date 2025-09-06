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

		<?php if ($error !== null): ?>
			<p class="error"><?php echo $error; ?></p>
		<?php endif; ?>

		<form action="/password?user_id=<?=$user;?>" method="POST">
            <p>
                Please change your password, <?=$user_fullname;?>.
            </p>
            <div>
                <label for="password_old">Old password</label><br>
                <input type="password" name="password_old" id="password_old">
            </div>
            <div>
                <label for="password_new">New password</label><br>
                <input type="password" name="password_new" id="password_new">
            </div>
            <div>
                <label for="password_new_confirm">Confirm new password</label><br>
                <input type="password" name="password_new_confirm" id="password_new_confirm">
            </div>

			<div>
				<button type="submit" name="change_pass" value="1" class="yes">Change password</button>
				<button type="submit" name="change_pass" value="0" class="no">Cancel</button>
			</div>
		</form>
	</body>
</html>
