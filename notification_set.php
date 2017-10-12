<?php
	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
	}
	if (session_status() === PHP_SESSION_NONE)
		session_start();
	if (!isset($_SESSION['logged_id']))
	{
		exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
	}
	if (isset($_POST['type']))
	{
		//PDO Creation
		include 'config/dbconnect.php';

		if ($_POST['type'] === 'like')
		{
			$req = 'UPDATE users
					SET like_notif = !like_notif
					WHERE id=:id';
		}
		else if ($_POST['type'] === 'com')
		{
			$req = 'UPDATE users
					SET comment_notif = !comment_notif
					WHERE id=:id';
		}
		else
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':id' => $_SESSION['logged_id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		exit(json_encode(['status' => 'done']));
	}
	else
		exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
?>
