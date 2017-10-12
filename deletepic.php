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
		$req = 'SELECT userid, filename
				FROM pictures
				WHERE id=:id';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':id' => $_GET['id']]);
		}
		catch (Exception $e)
		{
			exit (json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($sth->rowCount() === 0)
			exit (json_encode(['status' => 'error', 'value' => 'Ce montage n\'existe pas']));
		$res = $sth->fetch(PDO::FETCH_ASSOC);
		if ($res['userid'] !== $_SESSION['logged_id'])
			exit(json_encode(['status' => 'error', 'value' => 'Vous en pouvez pas supprimer ce montage']));
		$reqs[] = 'DELETE
					FROM pictures
					WHERE id=:id';
		$reqs[] = 'DELETE
					FROM likes
					WHERE picid=:id';
		$reqs[] = 'DELETE
					FROM comments
					WHERE picid=:id';
		foreach($reqs as $req)
		{
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
		unlink('images/pictures/' . $res['filename']);
	}
	exit (json_encode(['status' => 'done', 'message' => 'Picture deleted']));

?>
