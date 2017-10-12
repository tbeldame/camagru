<?php
	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		if (empty($_POST['overlay']))
		{
			exit(json_encode(['status' => 'error', 'value' => 'Aucun element sperposable choisi']));
		}
		if (isset($_POST['imgdata']))
		{
			$data = $_POST['imgdata'];
			$data = str_replace('data:image/png;base64,', '', $data);
			$data = str_replace(' ', '+', $data);
			$data = base64_decode($data);
			$img = imagecreatefromstring($data);
		}
		else if (isset($_POST['imgpath']))
		{
			$img = imagecreatefrompng($_POST['imgpath']);
		}
		$overlay = imagecreatefrompng('images/overlays/overlay' . $_POST['overlay'] . '.png');
		$posy = imagesy($img) - 375;
		$x = intval($_POST['x']);
		$y = intval($_POST['y']);
		$ovX = imagesx($overlay);
		$ovY = imagesy($overlay);
		$ratio = $_POST['w'] / $ovX;
		$angle = 360 - $_POST['angle'];
		$overlay = imagerotate($overlay, $angle, imagecolorallocatealpha($overlay , 0, 0, 0, 127));
		$nX = imagesx($overlay);
		$nY = imagesy($overlay);
		if (!imagecopyresampled($img, $overlay, $x - ((($nX - $ovX) / 2) * $ratio), $posy + $y - ((($nY - $ovY) / 2) * $ratio), 0, 0, $nX * $ratio, $nY * $ratio, $nX, $nY))
			exit(json_encode(['status' => 'error', 'value' => 'Erreur c\'est pas cool']));
		$filename = $_SESSION['logged_id'] . uniqid() . '.jpg';
		$ret = imagejpeg($img, 'images/pictures/' . $filename, 100);
		$thumb = imagecreatetruecolor(200, 150);
		imagecopyresampled($thumb, $img, 0, 0, 0, 0, 200, 150, 500, 375);
		$ret = imagejpeg($thumb, 'images/pictures/thumb_' . $filename, 100);

		include ('config/dbconnect.php');
		$req = 'INSERT INTO pictures
				SET userid=:userid,
				date=NOW(),
				filename=:filename';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':userid' => $_SESSION['logged_id'], ':filename' => $filename]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		exit(json_encode(['status' => 'done', 'value' => ['filename' => $filename, 'id' => $sql->lastInsertId()]]));
	}
	else
	{
		header('Location: index.php');
		exit();
	}


?>
