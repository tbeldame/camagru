<?php
	if (session_status() === PHP_SESSION_NONE)
		session_start();
	if (!isset($_SESSION['logged_user']))
	{
		header('Location: index.php');
		exit;
	}

	include 'config/dbconnect.php';
	$req = 'SELECT like_notif, comment_notif
			FROM users
			WHERE id=:id';
	try
	{
		$sth = $sql->prepare($req);
		$sth->execute([':id'=> $_SESSION['logged_id']]);
		$result = $sth->fetch(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		exit($e);
	}
?>

<!DOCTYPE html>

<html>

	<head>
		<meta charset="UTF-8">
		<meta name="theme-color" content="#00838F">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="icon" type="image/png" href="images/icons/favicon.png">
		<title>Camagru - Preferences</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/inputs.css">
		<link rel="stylesheet" type="text/css" href="css/settings.css">
	</head>

	<body>
			<div class="global">
				<?php include('header.php'); ?>

				<div class="content">
					<div class="card">
						<div class="card_title">
							<p>Preferences</p>
						</div>
						<div class="settings">
							<p class="setting_title">
								Notifications
							</p>
							<div class="setting">
								<p class="setting_text">
									J'aimes
								</p>
								<?php if ($result['like_notif'] === '1'): ?>
									<input type="checkbox" id="like" class="switch_input hidden" checked="true"/>
								<?php else: ?>
									<input type="checkbox" id="like" class="switch_input hidden"/>
								<?php endif; ?>
								<label for="like" class="notif_switch no_highlight">
									<div class="switch">
										<div class="switch_circle"></div>
									</div>
								</label>
							</div>
							<div class="setting">
								<p class="setting_text">
									Commentaires
								</p>
								<?php if ($result['comment_notif'] === '1'): ?>
									<input type="checkbox" id="com" class="switch_input hidden" checked="true"/>
								<?php else: ?>
									<input type="checkbox" id="com" class="switch_input hidden"/>
								<?php endif; ?>
								<label for="com" class="notif_switch no_highlight">
									<div class="switch">
										<div class="switch_circle"></div>
									</div>
								</label>
							</div>
							<p class="setting_title">
								Apparence
							</p>
							<div class="setting">
								<p class="setting_text">
									Couleur du compte
								</p>
								<input type="text" placeholder="HEX" class="color_input" value="<?php if (isset($_SESSION['logged_color'])) echo '#' . $_SESSION['logged_color'];?>"/>
								<div class="color_preview"></div>
							</div>
							<p class="setting_title">
								Compte
							</p>
							<div class="settings_btn" id="del_account">
								SUPPRIMER LE COMPTE
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include('footer.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/settings.js"></script>
	<script type="text/javascript" src="scripts/rainbow.js"></script>

</html>
