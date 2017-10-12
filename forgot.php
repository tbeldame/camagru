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
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/inputs.css">
		<meta charset="UTF-8">
		<meta name="theme-color" content="#00838F">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="icon" type="image/png" href="images/icons/favicon.png">
		<title>Camagru - Mot de passe oublie</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
		<style>
			.user_field {background-position: -200px 0;}
		</style>
	</head>

	<body>
			<div class="global">
				<?php include('header.php'); ?>

				<div class="content">
					<div class="card">
						<div class="card_title">
							<p>Mot de passe oublie</p>
						</div>
						<div class="user_form">
							<form method="post" id="forgot_form">
								<div class="field">
									<input class="user_field" placeholder="Utilisateur / E-Mail" type="text" name="user"/>
								</div>
								<div class="user_btn" id="forgot">ENVOYER</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php include('footer.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/forgot.js"></script>

</html>
