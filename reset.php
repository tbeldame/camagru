<?php
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		header('Location: index.php');
		exit;
	}
	if (isset($_GET['account']))
	{

		//PDO Creation
		include 'config/dbconnect.php';

		//Checking that reset hash exists
		$req = 'SELECT id
				FROM users
				WHERE reset_hash=:hash';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':hash' => $_GET['account']]);
			if ($sth->rowCount() === 0)
			{
				header('Location: index.php');
				exit;
			}
			$val = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue' .$e]));
		}
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
		<title>Camagru - Changement de mot de passe</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
		<style>
			.user_btn {box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);}
			.account_btn {box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);}
			.user_field {background-position: -200px 0;}
		</style>

	</head>

	<body>
			<div class="global">
				<?php include('header.php'); ?>

				<div class="content">
				<div class="card">
					<div class="card_title">
							<p>Changer de mot de passe</p>
						</div>
						<div class="user_form">
							<form method="post" id="reset_form">
								<div class="field">
									<input class="user_field" placeholder="Nouveau mot de passe" type="password" name="pass"/>
								</div>
								<div class="field">
									<input class="user_field" placeholder="Confirmation" type="password" name="pass2"/>
								</div>
								<input type="hidden" name="hash" value="<?php echo $_GET['account']; ?>"/>
								<div class="user_btn" id="reset">VALIDER</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php include('header.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/reset.js"></script>

</html>
