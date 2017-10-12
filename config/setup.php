<?php


include('database.php');

try
{
	$sql = new PDO($DB_DSN, $DB_USER, $_DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}
catch (Exception $e)
{
	exit('Erreur' . $e->getMessage());
}

try
{
$sql->query("CREATE DATABASE IF NOT EXISTS `" . $DB_NAME . "`
			DEFAULT CHARACTER SET utf8
			DEFAULT COLLATE utf8_general_ci");

$sql->query("USE `$DB_NAME`");

$sql->query("CREATE TABLE IF NOT EXISTS `users` (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`username` varchar(20) NOT NULL,
			`email` varchar(128) NOT NULL,
			`password` varchar(128) NOT NULL,
			`salt` varchar(10) NOT NULL,
			`verified` tinyint(1) NOT NULL,
			`verif_hash` varchar(128) NOT NULL,
			`reset_hash` varchar(32) NOT NULL,
			`like_notif` tinyint(1) NOT NULL DEFAULT '1',
			`comment_notif` tinyint(1) NOT NULL DEFAULT '1',
			`color` varchar(6) DEFAULT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

$sql->query("CREATE TABLE IF NOT EXISTS `pictures` (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`userid` int(11) NOT NULL,
			`date` datetime NOT NULL,
			`filename` varchar(25) NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

$sql->query("CREATE TABLE IF NOT EXISTS `likes` (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`userid` int(11) NOT NULL,
			`picid` int(11) NOT NULL,
			`date` datetime NOT NULL,
			CONSTRAINT `UniqLike` UNIQUE (`userid`, `picid`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

$sql->query("CREATE TABLE IF NOT EXISTS `comments` (
			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`userid` int(11) NOT NULL,
			`picid` int(11) NOT NULL,
			`date` datetime NOT NULL,
			`comment` text CHARACTER SET utf8 NOT NULL
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
}
catch (Exception $e)
{
	exit($e);
}

if (!file_exists(dirname(dirname(__FILE__)) . '/images/tmp'))
	    mkdir(dirname(dirname(__FILE__)) . '/images/tmp', 0777, true);
if (!file_exists(dirname(dirname(__FILE__)) . '/images/pictures'))
	    mkdir(dirname(dirname(__FILE__)) . '/images/pictures', 0777, true);

$file = dirname(__FILE__) . '/setup_done';
file_put_contents($file, '');
chmod($file, 0777)

?>
