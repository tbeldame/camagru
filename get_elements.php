<?php
function getelapsedtime($time)
{
	$time = time() - strtotime($time);
	$time = ($time < 1) ? 1 : $time;
	$values = [
		31536000 => 'an',
		2592000 => 'mois',
		604800 => 'semaine',
		86400 => 'jour',
		3600 => 'heure',
		60 => 'minute',
		1 => 'seconde'
	];
	foreach ($values as $unit => $text)
	{
		if ($time < $unit)
			continue;
		$numberOfUnits = floor($time / $unit);
		if ($unit === 86400 && $time < 172800)
			return 'Hier';
		if ($numberOfUnits > 1 && $text !== 'mois')
			$text .= 's';
		return 'Il y a ' . $numberOfUnits . ' ' . $text;
	}
}

	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' )
	{
		header('Location: index.php');
		exit();
	}

	if (!isset($_POST['nb']))
	{
		exit(json_encode(['status' => 'error', 'value' => 'Une erreur est survenue']));
	}
	include('config/dbconnect.php');
	$per_page = intval($_POST['nb']);
	$start = intval($_POST['page']) * $per_page;
	$end = $start + $per_page;
	$requested = $per_page;
	$req = "SELECT p.*, u.username AS 'creator',
			GROUP_CONCAT(u2.username SEPARATOR '.') AS 'likers'
			FROM pictures AS p
			LEFT JOIN likes AS l ON l.picid = p.id
			LEFT JOIN users AS u ON u.id = p.userid
			LEFT JOIN users AS u2 ON u2.id = l.userid
			GROUP BY p.id
			ORDER BY p.date DESC
			LIMIT :start, :requested";
	try
	{
		$sth = $sql->prepare($req);
		$sth->bindParam(':start', $start, PDO::PARAM_INT);
		$sth->bindParam(':requested', $requested, PDO::PARAM_INT);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	catch (Exception $e)
	{
		$ret = ['status' => 'error', 'value' => 'Une erreur est survenue'];
		$result = json_encode($ret);
		exit($result);
	}
	foreach ($result as &$entry)
	{
		$req = "SELECT COUNT(*) AS `comments`
				FROM `comments`
				WHERE `picid`=:id";
		try
		{
			$sth = $sql->prepare($req);
			$sth->execute([':id' => $entry['id']]);
			$val = $sth->fetch(PDO::FETCH_ASSOC);
		}
		catch (Exception $e)
		{
			$ret = ['status' => 'error', 'value' => 'Une erreur est survenue'];
			$result = json_encode($ret);
			exit($result);
		}
		$entry['com_nb'] = $val['comments'];
		$entry['elapsed'] = getelapsedtime($entry['date']);
		$entry['date'] = date('d/m/Y H:i:s', strtotime($entry['date']));
	}
	$ret = ['status' => 'done', 'value' => $result];
	$result = json_encode($ret);
	exit($result);
?>
