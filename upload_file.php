<?php
	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit('not an xmlhttprequest');
	}
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		$tmpname = $_FILES['userfile']['tmp_name'];

		//Check size
		if ($_FILES['userfile']['size'] > 5242880)
			exit (json_encode(['status' => 'error', 'value' => 'Fichier trop volumineux']));

		//Check file type real
		$file = finfo_open(FILEINFO_MIME_TYPE);
		$imgtype = finfo_file($file, $tmpname);
		
		if ($imgtype === 'image/gif')
		{
			$ext = ".gif";
			$img = imagecreatefromgif($tmpname);
		}
		else if ($imgtype === 'image/jpeg')
		{
			$ext = ".jpg";
			$img = imagecreatefromjpeg($tmpname);
		}
		else if ($imgtype === 'image/png')
		{
			$ext = ".png";
			$img = imagecreatefrompng($tmpname);
		}
		else if (!$imgtype || !$img)
			exit (json_encode(['status' => 'error', 'value' => 'Image invalide']));

		//Resize it the best we can
		$dim = getimagesize($tmpname);
		$ratio = $dim[0] / $dim[1];
		if ($ratio > 4/3)
		{
		    $w = $dim[0] * (375 / $dim[1]);
			$h = 375;
			$x = $w / 2 - 500 / 2;$y = 0;
		}
		else if ($ratio < 4/3)
		{
			$w = 500;
			$h = $dim[1] * (500 / $dim[0]);
			$x = 0;
			$y = $h / 2 - 375 / 2;
		}
		else if ($ratio === 4/3)
		{
			$w = 500;
			$h = 375;
			$x = 0;
			$y = 0;
		}
		$new = ImageCreateTrueColor($w, $h);
		imagecopyresampled($new, $img, 0, 0, 0, 0, $w, $h, $dim[0], $dim[1]);
		$new = imagecrop($new, ['x' => $x, 'y' => $y, 'width' => 500, 'height' => 375]);	

		//Save it as tmp somthing
		$filename = 'tmp_' . $_SESSION['logged_id'] . uniqid() . '.png';
		$ret = imagepng($new, 'images/tmp/' . $filename, 9);

		//Send name to js
		exit(json_encode(['status' => 'done', 'value' => $filename]));

	}
	header('Content-Type: application/json');
	exit (json_encode(['status' => 'error', 'value' => 'Pas d\'utilisateur connecte']));

?>
