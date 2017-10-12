<?php

	session_start();
	if (!isset($_SESSION['logged_user']))
	{
		header('Location: index.php');
		exit;
	}
	include('config/dbconnect.php');
	$req = "SELECT * FROM `pictures` WHERE `userid`=:userid
			ORDER BY `date` DESC
			LIMIT 0,20";
	try
	{
		$sth = $sql->prepare($req);
		$sth->execute([':userid' => $_SESSION['logged_id']]);
		$prev_pic = $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		$ret = ['status' => 'error', 'value' => 'Une erreur est survenue'];
		$result = json_encode($ret);
		exit($result);
	}

?>
<!DOCTYPE html>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/create.css">
		<link rel="stylesheet" type="text/css" href="css/inputs.css">
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
					<div class="main">
						<div id="preview">
							<div id="visual_preview">
								<video class="video_preview" id="video"></video>
								<div class="overlay_container">
								</div>
							</div>
						</div>
						<div class="overlays">
							<?php
								$files = scandir('images/overlays');
								$nb = 1;
								foreach($files as $file): 
									if (substr($file, -3) !== 'png')
										continue; ?>
									<div id="<?= $nb ?>" class="overlay_thumb" sel="0">
										<img src="images/overlays/thumb/<?= $file ?>"></img>
									</div>
								<?php
									$nb++;
									 endforeach;
								?>
							</div>
						<div class="btns">
							<input type="file" name="file" id="file" class="file_input"/>
							<label for="file" class="upload_btn">ENVOYER UNE IMAGE</label>
							<div id="create_btn" class="disabled">CREER LE MONTAGE</div>
						</div>
					</div>
					<div class="side">
						<?php foreach($prev_pic as $pic): ?>
						<div class="prev_pic">
							<a href="viewpic.php?pic=<?= $pic['id'] ?>">
								<img src="images/pictures/thumb_<?= $pic['filename'] ?>">
							</a>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php include('footer.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/create.js"></script>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/rainbow.js"></script>
</html>
