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
		$req = 'SELECT pictures.userid, users.username, users.email
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
			exit(json_encode(['status' => 'error', 'value' => 'Ce  montage n\'existe pas']));
		$req = 'DELETE FROM likes
				WHERE userid=:userid
				AND	picid=:picid';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':userid' => $_SESSION['logged_id'], ':picid' => $_GET['id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
	}
	exit(json_encode(['status' => 'done']));

?>
