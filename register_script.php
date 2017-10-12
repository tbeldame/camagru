<?php

	function	checkCaptcha($response)
	{
		$secret = '6LfqsgoUAAAAAC0pGg1Yh77tq457ShHjw4JO_VDJ';
		$url = 'https://www.google.com/recaptcha/api/siteverify';

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=$secret&response=$response");
		$response = curl_exec($curl);
		if (empty($response) || is_null($response))
			return false;
		$response = json_decode($response);
		return $response->success;
	}

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
		$usrnm = trim($_POST['username']);
		if (!checkCaptcha($_POST['g-recaptcha-response']))
			exit(json_encode(['status' => 'error', 'value' => 'Ce site est reserve aux humains']));
		
		//Checking if all fields are filled
		if ($usrnm === "" || $_POST['email'] === "" || $_POST['pass'] === "" || $_POST['pass2'] === "")
		{
			exit(json_encode(['status' => 'error', 'value' => 'Tous les champs doivent etre remplis']));
		}

		//Username validity checking
		if (!preg_match("/^[a-zA-Z0-9\-\_]{3,20}$/", $usrnm))
		{
			exit(json_encode(['status' => 'error', 'value' => 'Le nom d\'utilisateur doit faire de 3 a 20 caractere et ne contenir que des lettres, chiffres ou - _']));
		}

		//E-mail validity checking
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			exit(json_encode(['status' => 'error', 'value' => 'L\'adresse e-mail entree n\'est pas valide']));
		}

		//Password validity checking
		if ($_POST['pass'] !== $_POST['pass2'])
		{
			exit(json_encode(['status' => 'error', 'value' => 'Les deux mots de passe ne sont pas identiques']));
		}
		if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d).{8,}$/", $_POST['pass']))
		{
			exit(json_encode(['status' => 'error', 'value' => 'Le mot de passe doit faire un minimum de 8 caracteres et contenir au moin une lettre et un chiffre']));
		}

		//PDO Creation
		include 'config/dbconnect.php';

		//Checking that username or email is not already used
		$req = 'SELECT COUNT(*) AS `count`
				FROM users
				WHERE username=:username';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':username' => $usrnm]);
			$val = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($val['count'] !== "0")
		{
			exit(json_encode(['status' => 'error', 'value' => 'Ce nom d\'utilisateur est deja utilise']));
		}
		$req = 'SELECT COUNT(*) AS `count`
				FROM users
				WHERE email=:email';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':email' => $_POST['email']]);
			$val = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
		}
		if ($val['count'] !== "0")
		{
			exit(json_encode(['status' => 'error', 'value' => 'Un compte utilise deja cette adresse e-mail']));
		}

		//Hash for e-mail validation
		$hash = md5($usrnm . time());

		//Password operations
		$salt = bin2hex(mcrypt_create_iv(5));
		$pass = hash('whirlpool', $salt . $_POST['pass']);

		//Creating user database entry
		$req = 'INSERT INTO users
				SET username=:username,
				email=:email,
				password=:password,
				salt=:salt,
				verified=:verified,
				reset_hash=:reset_hash,
				verif_hash=:verif_hash';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':username' => $usrnm,
							':email' => $_POST['email'],
							':password' => $pass,
							':salt' => $salt,
							':verified' => 0,
							':reset_hash' => '0',
							':verif_hash' => $hash]);
		}
		catch (Excpetion $e)
		{
			exit(json_encode(['status' => 'error', 'value' => 'Une erruer est survenue']));
		}

		//Sending validation e-mail
		$to = $_POST['email'];
		$subject = "Validation du compte Camagru";
		$content = 'Veuillez cliquer sur ce lien pour valider votre inscription : http://' . $_SERVER['HTTP_HOST'] . '/validate.php?account=' . $hash;
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=iso-8859-1";
		$headers[] = "From: Camagru <noreply@camagru.tbeldame.fr>";
		$headers[] = "Subject: {$subject}";
		$headers[] = "X-Mailer: PHP/".phpversion();
		mail($to, $subject, $content, implode("\r\n", $headers));

		//Redirect the user to the login page
		exit(json_encode(['status' => 'done', 'value' => 'Votre compte a ete cree avec succes, veuillez suivre le lien qui vous a ete envoye par e-mail pour l\'activer']));
	}

?>

