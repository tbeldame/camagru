<?php
	//I just deleted my comments, fuck me
	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		include 'config/dbconnect.php';
		$req = 'SELECT pictures.userid, users.username, users.email, users.like_notif
				FROM pictures
				JOIN users ON users.id = pictures.userid
				WHERE pictures.id=:picid';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':picid' => $_GET['id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($sth->rowCount() === 0)
			exit(json_encode(['status' => 'error', 'value' => 'Cette imgae n\'existe pas']));
		$res = $sth->fetch(PDO::FETCH_ASSOC);

		$req = 'INSERT INTO likes
				SET userid=:userid,
				picid=:picid,
				date=NOW()';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':userid' => $_SESSION['logged_id'], ':picid' => $_GET['id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}

	//Sending like e-mail
		if ($res['userid'] !== $_SESSION['logged_id'] && $res['like_notif'] === '1')
		{
			$to = $res['email'];
			$subject = "Un utilisateur a aime un de vos montages";
			$content = 'Bonjour ' . $res['username'] . ', un utilisateur a aime un de vos montages, veuillez suivre ce lien pour voir le montage http://' .
			$_SERVER['HTTP_HOST'] . '/viewpic.php?pic=' . $_GET['id'];
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/plain; charset=iso-8859-1";
			$headers[] = "From: Camagru <noreply@camagru.tbeldame.fr>";
			$headers[] = "Subject: {$subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();
			mail($to, $subject, $content, implode("\r\n", $headers));
		}
		exit(json_encode(['status' => 'done']));
	}
	exit(json_encode(['status' => 'error', 'value' => 'Non connecte']));

?>
