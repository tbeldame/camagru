<?php
	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}
	if (session_status() === PHP_SESSION_NONE)
		session_start();
	if (isset($_SESSION['logged_user']))
	{
		exit(json_encode(['status' => 'error', 'value' => 'Utilisateur deja connecte']));
	}

	if (isset($_POST))
	{
		//Checking if username filled
		if ($_POST['user'] === "")
		{
			exit(json_encode(['status' => 'error', 'value' => 'Veuillez entrer un nom d\'utilisateur ou e-mail']));
		}
		else
			$usr = trim($_POST['user']);
		if (filter_var($usr, FILTER_VALIDATE_EMAIL))
			$email = TRUE;
		else
			$email = FALSE;

		//PDO Creation
		include 'config/dbconnect.php';

		//Checking that login exists and password matches
		if ($email)
		{
			$req = 'SELECT *
					FROM users
					WHERE email=:email';
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':email' => $usr]);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
			}
			if ($sth->rowCount() === 0)
			{
				exit(json_encode(['status' => 'error', 'value' => 'E-mail ou mot de passe invalide']));
			}
			$val = $sth->fetch(PDO::FETCH_ASSOC);

			//Creating password hash
			$pass = hash('whirlpool', $val['salt'] . $_POST['pass']);
			if ($val['password'] !== $pass)
			{
				exit(json_encode(['status' => 'error', 'value' => 'E-mail ou mot de passe invalide']));
			}
			else if ($val['verified'] === '0')
			{
				exit(json_encode(['status' => 'error', 'value' => 'Votre compte n\'est pas valide, merci d\'utiliser le lien envoye par e-mail']));
			}
			else
			{
				$_SESSION['logged_user'] = $val['username'];
				$_SESSION['logged_id'] = $val['id'];
				exit(json_encode(['status' => 'done']));
			}
		}
		else
		{
			$req = 'SELECT *
					FROM users
					WHERE username=:username';
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':username' => $usr]);
				$val = $sth->fetch(PDO::FETCH_ASSOC);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
			}
			if ($sth->rowCount() === 0)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Nom d\'utilisateur ou mot de passe invalide']));
			}

			//Creating password hash
			$pass = hash('whirlpool', $val['salt'] . $_POST['pass']);
			if ($val['password'] !== $pass)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Nom d\'utilisateur ou mot de passe invalide']));
			}
			else if ($val['verified'] === '0')
			{
				exit(json_encode(['status' => 'error', 'value' => 'Votre compte n\'est pas valide, merci d\'utiliser le lien envoye par e-mail']));
			}
			else
			{
				$_SESSION['logged_user'] = $val['username'];
				$_SESSION['logged_id'] = $val['id'];
				if (!is_null($val['color']))
					$_SESSION['logged_color'] = $val['color'];
				exit(json_encode(['status' => 'done']));
			}
		}
	}
?>
