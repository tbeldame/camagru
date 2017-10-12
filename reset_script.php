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
		//Have to check password validity and that both are the same then update the password in DB and set reset_hash to 0

		//PDO Creation
		include 'config/dbconnect.php';
	
		//Checking that reset hash exists
		$req = 'SELECT COUNT(*) AS `count`
				FROM users
				WHERE reset_hash=:hash';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':hash' => $_POST['hash']]);
			$val = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($val['count'] === "0")
		{
			exit(json_encode(['status' => 'error', 'value' => 'Compte invalide']));
		}

		//Checking password validity and match
		if ($_POST['pass'] === "" || $_POST['pass2'] === "") 
		{
			exit(json_encode(['status' => 'error', 'value' => 'Veuillez remplir les deux champs']));
		}
		if ($_POST['pass'] !== $_POST['pass2'])
		{
			exit(json_encode(['status' => 'error', 'value' => 'Les deux mots de passe ne sont pas identiques']));
		}
		if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d).{8,}$/", $_POST['pass']))
		{
			exit(json_encode(['status' => 'error', 'value' => 'Le mot de passe doit faire un minimum de 8 caracteres et contenir au moin une lettre, un chiffre']));
		}
		
		//Password operations
		$salt = bin2hex(mcrypt_create_iv(5));
		$pass = hash('whirlpool', $salt . $_POST['pass']);

		//Update the user password and remove the reset hash in DB
		$req = 'UPDATE users
				SET password=:pass,
				salt=:salt,
				reset_hash=:no_hash
				WHERE reset_hash=:hash';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':pass' => $pass, ':salt' => $salt, ':no_hash' => '0', ':hash' => $_POST['hash']]);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		exit(json_encode(['status' => 'done', 'value' => 'Votre pouvez desormais vous connecter avec votre nouveau mot de passe']));
	}
	else
	{
		exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
	}


?>
