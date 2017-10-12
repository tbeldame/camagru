<?php
	if (session_status() === PHP_SESSION_NONE)
		session_start();

function randGreeting()
{
	$arr = ['Bienvenue', 'Bonjour', 'Salut'];
	return ($arr[array_rand($arr)]);
}

function comCount($userid)
{
	include('config/dbconnect.php');

	$req = 'SELECT COUNT(p.id) AS count
			FROM pictures AS p
			JOIN comments AS c ON c.picid = p.id
			WHERE p.userid=:userid';
	try
	{
		$sth = $sql->prepare($req);
		$sth->execute([':userid' => $userid]);
		$ret = $sth->fetch(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		return('Erreur');
	}
	return (intval($ret['count']));
}

function likeCount($userid)
{
	include('config/dbconnect.php');

	$req = 'SELECT COUNT(p.id) AS count
			FROM pictures AS p
			JOIN likes AS l ON l.picid = p.id
			WHERE p.userid=:userid';
	try
	{
		$sth = $sql->prepare($req);
		$sth->execute([':userid' => $userid]);
		$ret = $sth->fetch(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		return('Erreur');
	}
	return (intval($ret['count']));
}

function picCount($userid)
{
	include('config/dbconnect.php');

	$req = 'SELECT COUNT(p.id) AS count
			FROM pictures AS p
			WHERE p.userid=:userid';
	try
	{
		$sth = $sql->prepare($req);
		$sth->execute([':userid' => $userid]);
		$ret = $sth->fetch(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		return('Erreur');
	}
	return (intval($ret['count']));
}

?>
<!DOCTYPE html>

<html>

	<head>
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/inputs.css">
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<meta charset="UTF-8">
		<meta name="theme-color" content="#00838F">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="icon" type="image/png" href="images/icons/favicon.png">
		<title>Camagru</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
	</head>

	<body>
		<div class="global">
			<?php include('header.php'); ?>
			<div class="content">
				<?php if (isset($_SESSION['logged_user'])): ?>
					<div class="home_greet">
						<p> 
							<?= randGreeting() . ' ' . $_SESSION['logged_user'] ?>
						</p>
					</div>
					<div class="home_info">
						<p> 
							<?php
								$pics = picCount($_SESSION['logged_id']);
								if ($pics === 0): ?>
									Vous n'avez pas cree de monatge
							<?php else:
									$coms = comCount($_SESSION['logged_id']);
									$likes = likeCount($_SESSION['logged_id']);
							?>
								Vous avez recu <?= $coms ?> commentaire<?php if ($coms > 1) echo 's'; ?> et <?= $likes ?> like<?php if ($likes > 1) echo 's'; ?> sur vos <?= $pics ?> montagne<?php if ($pics > 1) echo 's'; ?>
							<?php endif; ?>
						</p>
					</div>
				<?php else: ?>
					<div class="home_txt">
						<p>Bienvenue sur Camagru</p>
					</div>
					<div class="home_btns">
						<div class="home_btn" id="reg">Inscription</div>
						<div class="home_btn" id="log">Connexion</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php include('footer.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/index.js"></script>
	<script type="text/javascript" src="scripts/rainbow.js"></script>

</html>
