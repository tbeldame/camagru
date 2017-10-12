<?php

	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		//$comment = htmlspecialchars($_POST['comment']);
		$comment = $_POST['comment'];
		//Checking comment length
		$len = strlen($_POST['comment']);
		if ($len < 1 || $len > 2000)
			exit(json_encode(['status' => 'error', 'value' => 'Taille du commentaire invalide']));
		
		//Connecting to DB
		include 'config/dbconnect.php';

		//Checking that the picture exists and getting user infos
		$req = 'SELECT pictures.userid, users.username, users.email, users.comment_notif
				FROM pictures
				JOIN users ON users.id=pictures.userid
				WHERE pictures.id=:picid';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':picid' => $_POST['id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($sth->rowCount() === 0)
			exit(json_encode(['status' => 'error', 'value' => 'Montage non existant']));
		$res = $sth->fetch(PDO::FETCH_ASSOC);

		//Sending comment to DB
		$req = 'INSERT INTO comments
				SET userid=:userid,
				picid=:picid,
				date=NOW(),
				comment=:comment';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':userid' => $_SESSION['logged_id'], ':picid' => $_POST['id'], ':comment' => $comment]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		$id = $sql->lastInsertId();

		//Sending like e-mail
		if ($res['userid'] !== $_SESSION['logged_id'] && $res['comment_notif'] === '1')
		{
			$to = $res['email'];
			$subject = "Un utilisateur a commente un de vos montages";
			$content = 'Bonjour ' . $res['username'] . ', un utilisateur a commente un de vos montages, veuillez suivre ce lien pour voir le montage http://' .
			$_SERVER['HTTP_HOST'] . '/viewpic.php?pic=' . $_POST['id'];
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/plain; charset=iso-8859-1";
			$headers[] = "From: Camagru <noreply@camagru.tbeldame.fr>";
			$headers[] = "Subject: {$subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();
			mail($to, $subject, $content, implode("\r\n", $headers));
		}
		exit (json_encode(['status' => 'done', 'value' => $id]));
	}
	exit(json_encode(['status' => 'error', 'value' => 'Non connecte']));

?>
