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

		//Detecting if a username or email is sent
		if (filter_var($usr, FILTER_VALIDATE_EMAIL))
		{
			$email = TRUE;
			$to = $usr;
		}
		else
			$email = FALSE;

		//PDO Creation
		include 'config/dbconnect.php';

		//Checking that login exists
		if ($email)
		{
			$req = 'SELECT id
					FROM users
					WHERE email=:email';
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':email' => $usr]);
				if ($sth->rowCount() === 0)
					exit(json_encode(['status' => 'error', 'value' => 'Adresse email invalide invalide']));
				$val = $sth->fetch(PDO::FETCH_ASSOC);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
			}
			if ($val['count'] === "0")
			{
				exit(json_encode(['status' => 'error', 'value' => 'E-mail invalide']));
			}
			$hash = md5($usr . uniqid());
			$req = 'UPDATE users
					SET reset_hash=:hash
					WHERE email=:email';
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':hash' => $hash, ':email' => $usr]);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
			}

		}
		else
		{

			$req = 'SELECT email
					FROM users
					WHERE username=:username';
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':username' => $usr]);
				if ($sth->rowCount() === 0)
					exit(json_encode(['status' => 'error', 'value' => 'Nom d\'utilisateur invalide']));
				$val = $sth->fetch(PDO::FETCH_ASSOC);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue' . $e]));
			}
			$hash = md5($usr . time());
			$req = 'UPDATE users
					SET reset_hash=:hash
					WHERE username=:username';
			try
			{
				$sth = $sql->prepare($req);
				$sth->execute([':hash' => $hash, ':username' => $usr]);
			}
			catch (Exception $e)
			{
				exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
			}
			$to = $val['email'];
		}

		//Sending reset e-mail
		$headers = 'From: noreply@camagru.fr\n';
		mail($to, 'Reinitialisation du mot de passe Camagru', 'Veuillez cliquer sur ce lien pour reinitialiser votre mot de passe : http://' . $_SERVER['HTTP_HOST'] . '/reset.php?account=' . $hash, $headers);

		//Redirect the user to the login page
		exit(json_encode(['status' => 'done', 'value' => 'Veuillez suivre le lien qui vous a ete envoye par e-mail pour defnir votre nouveau mot de passe']));
	}
	exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));

?>
