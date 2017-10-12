<?php
	if (session_status() === PHP_SESSION_NONE)
		session_start();
	if (isset($_SESSION['logged_user']))
	{
		header('Location: index.php');
		exit;
	}
?>

<!DOCTYPE html>

<html>

	<head>
		<meta charset="UTF-8">
		<meta name="theme-color" content="#00838F">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="icon" type="image/png" href="images/icons/favicon.png">
		<title>Camagru - Inscription</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/inputs.css">
		<style>
			.user_field {background-position: -200px 0;}
		</style>
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>

	<body>
			<div class="global">
				<?php include('header.php'); ?>

				<div class="content">
					<div class="card">
						<div class="card_title">
							<p>Inscription</p>
						</div>
						<div class="user_form">
							<form method="post" action="register.php" id="reg_form">
								<div class="field">
									<input class="user_field" placeholder="Nom d'utilisateur" type="text" autocomplete="off" autocapitalize="off" name="username"/>
								</div>
								<div class="field">
									<input class="user_field" placeholder="Adresse E-Mail" type="email" autocomplete="off" autocapitalize="off" name="email"/>
								</div>
								<div class="field">
									<input class="user_field" placeholder="Mot de passe" type="password" name="pass"/>
								</div>
								<div class="field">
									<input class="user_field" placeholder="Confirmation" type="password" name="pass2"/>
								</div>
								<div class="field">
									<div align="center" class="g-recaptcha" data-sitekey="6LfqsgoUAAAAALSL9d2-BAjci7J_JvMuIvJJWWYF"></div>
								</div>
								<div class="user_btn" id="register">INSCRIPTION</div>
							</form>
						</div>
					<div class="bot_btns">
						<div class="account_btn">
							<a href="login.php">CONNEXION</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include('footer.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/register.js"></script>

</html>
