<?php

	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}
	session_start();
	if (isset($_SESSION['logged_user']))
	{
		if (isset($_POST['color']))
		{
			if (preg_match('/#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3}|rnbw)$/', $_POST['color']))
			{
				include 'config/dbconnect.php';

				$color = substr($_POST['color'], 1);
				$req = 'UPDATE users
						SET color=:color
						WHERE id=:id';
				try
				{
					$sth = $sql->prepare($req);
					$sth->execute([':color' => $color, ':id' => $_SESSION['logged_id']]);
				}
				catch (Exception $e)
				{
					exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
				}
				$_SESSION['logged_color'] = $color;
				exit (json_encode(['status' => 'done', 'value' => 'Couleur changee']));
			}
			exit (json_encode(['status' => 'done', 'value' => 'Couleur invalide']));
		}
		exit (json_encode(['status' => 'error', 'value' => 'Une errueur est survenue']));
	}
	exit (json_encode(['status' => 'error', 'value' => 'Non connecte']));

?>
