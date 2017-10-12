<?php
	session_start();

	//Checking if a user is already logged and therefore should not be here
	if (isset($_SESSION['logged_user']))
	{
		session_unset();
	}
	header('Location: index.php');
	exit;
?>
