<?php

	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/config/setup_done"))
	{
		exit(json_encode(['status' => 'error', 'value' => 'Setup.php not run']));
	}

	include('database.php');

	try
	{
		$sql = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		$sth = $sql->prepare("USE `$DB_NAME`");
		$sth->execute();
	}
	catch(Exception $e)
	{
		if (session_status() === PHP_SESSION_NONE)
			session_start();
		$_SESSION['error'] = "Une erreur est survenue";
		header ('Location: index.php');
		error_log($e->getMessage());
		exit();
		//exit('Erreur' . $e->getMessage());
	}
?>
