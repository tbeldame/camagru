<?php

	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		include 'config/dbconnect.php';
		$req = 'SELECT userid
				FROM comments
				WHERE id=:id';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':id' => $_GET['id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($sth->rowCount() === 0)
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		$res = $sth->fetch(PDO::FETCH_ASSOC);
		if ($res['userid'] !== $_SESSION['logged_id'])
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		$req = 'DELETE
				FROM `comments`
				WHERE `id`=:id';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':id' => $_GET['id']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}		
	}
	exit(json_encode(['status' => 'done']));

?>
