<?php
if (isset($_GET['account']))
{	
	//PDO Creation
	include 'config/dbconnect.php';

	//CHecking that this hash corresponds to an account which is not activated
	$req = 'SELECT COUNT(id) AS `c`, username, id
			FROM users
			WHERE verif_hash=:hash
			AND verified=:not
			GROUP BY id';
	try
	{
		$sth = $sql->prepare($req);
		$sth->execute([':hash' => $_GET['account'], ':not' => '0']);
		$val = $sth->fetch(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		exit('Erreur' . $e);
	}
	//Activate the account, log the user and redirects him to the index
	if ($val['c'] === "1") //maybe this is not a string
	{
		session_start();
		$req = 'UPDATE users
				SET verified=:verified,
				verif_hash=:not
				WHERE verif_hash=:hash';
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':verified' => '1', ':not' => '0', ':hash' => $_GET['account']]);
		}
		catch (Exception $e)
		{
			exit('Erreur' . $e);
		}
		$_SESSION['logged_user'] = $val['username'];
		$_SESSION['logged_id'] = $val['id'];
		header('Location: index.php');
		exit;
	}

	//Redirects the user to the login page
	else
	{
		header('Location: login.php');
		exit;
	}

}
else
{
	header('Location: index.php');
	exit;
}

?>
