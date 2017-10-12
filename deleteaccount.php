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

		$req = 'SELECT filename
				FROM pictures
				WHERE userid=:id';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':id' => $_SESSION['logged_id']]);
			$pics = $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		foreach ($pics as $pic)
		{
			unlink('images/pictures/' . $pic['filename']);
		}
		$reqs[] = 'DELETE c FROM comments AS c
					LEFT JOIN pictures AS p ON p.id = c.picid
					WHERE p.userid=:id';
		$reqs[] = 'DELETE l FROM likes AS l
					LEFT JOIN pictures AS p ON p.id = l.picid
					WHERE p.userid=:id';
		$reqs[] = 'DELETE
					FROM pictures
					WHERE userid=:id';
		$reqs[] = 'DELETE
					FROM likes
					WHERE userid=:id';
		$reqs[] = 'DELETE
					FROM comments
					WHERE userid=:id';
		$reqs[] = 'DELETE
					FROM users
					WHERE id=:id';
		foreach($reqs as $req)
		{
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':id' => $_SESSION['logged_id']]);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
			}
		}
		unset($_SESSION['logged_user']);
		unset($_SESSION['logged_id']);
	}
	exit (json_encode(['status' => 'done', 'value' => 'User killed']));

?>
