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
				include('config/dbconnect.php');

				$req = "SELECT p.*, u.username AS 'creator',
						GROUP_CONCAT(u2.username SEPARATOR '.') AS 'likers'
						FROM pictures AS p
						LEFT JOIN likes AS l ON l.picid = p.id
						LEFT JOIN users AS u ON u.id = p.userid
						LEFT JOIN users AS u2 ON u2.id = l.userid
						GROUP BY p.id
						ORDER BY p.date DESC
						LIMIT :start, :requested";
				$start = 0;
				$requested = 20;
				try
				{
					$sth = $sql->prepare($req);
					$sth->bindParam(':start', $start, PDO::PARAM_INT);
					$sth->bindParam(':requested', $requested, PDO::PARAM_INT);
					$sth->execute();
					if ($sth->rowCount() < 1)
					{
						header('Location: index.php');
						exit;
					}
					$results = $sth->fetchAll(PDO::FETCH_ASSOC);
				}
				catch (Exception $e)
				{
					exit('Erreur' . $e);
				}
if (session_status() === PHP_SESSION_NONE)
	session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/gallery.css">
		<meta charset="UTF-8">
		<meta name="theme-color" content="#00838F">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="icon" type="image/png" href="images/icons/favicon.png">
		<title>Gallerie - Camagru</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
	</head>

	<body>
		<div class="global">
		<?php include('header.php'); ?>
			<div class="content">

			<div class="gallery">
			<?php
				$current = 1;
				foreach($results as $row):
					$req = 'SELECT COUNT(*) AS `comments`
							FROM comments
							WHERE picid=:id';
					try
					{
						$sth = $sql->prepare($req);
						$sth->execute([':id' => $row['id']]);
						$count = $sth->fetch(PDO::FETCH_ASSOC);
					}
					catch (Exception $e)
					{
						exit('Erreur' . $e);
					}
					$like_nb = 0;
					if (isset($row['likers']))
					{
						$likers = explode('.', $row['likers']);
						$like_nb = count($likers);
					}
					else
						$likers = [];
			?>
					<div class="gallery_entry" id="<?= $row['id'] ?>">
					<div class="thumbcontainer">
						<a href="<?php echo "viewpic.php?pic=$row[id]";?>">
							<img src="images/pictures/thumb_<?php echo $row['filename'];?>">
						</a>
					</div>
					<div class="creator_name">
						<?= $row['creator'] ?>
					</div>
					<div class="creation_date" title="<?= date('d/m/Y H:i:s', strtotime($row['date'])) ?>">
						<?php echo getelapsedtime($row['date']); ?>
					</div>
			<?php if (isset($_SESSION['logged_id']) && $row['userid'] === $_SESSION['logged_id']): ?>
					<div class="del_btn">
						<i class="material-icons">delete</i>
					</div>
			<?php
				endif;
				if (isset($_SESSION['logged_id']) && !empty($likers) && in_array($_SESSION['logged_user'], $likers)): ?>
					<div class="like_btn liked" liked="1">
						<i class="material-icons">thumb_up</i>
					</div>
			<?php else: ?>
					<div class="like_btn" liked="0">
						<i class="material-icons">thumb_up</i>
					</div>
			<?php endif; ?>
			<?php
				unset($likes);
			?>
				<div class="like_counter" value="<?= $like_nb ?>" name="likecount">
						<div class="like_nb">
							<?= $like_nb ?>
						</div>
						<?php if ($like_nb > 0): ?>
							<div class="likers" name="like_list">
							<?php foreach ($likers as $liker): ?>
								<p>
									<?= $liker ?>
								</p>
							<?php endforeach; ?>
						<?php else: ?>
							<div class="hidden" name="like_list">
						<?php endif; ?>
						</div>
					</div>
					<div class="comment_icon">
						<i class="material-icons">comment</i>
					</div>
					<div class="comment_counter"><?php echo $count['comments']?></div>
				</div>
			<?php
					$current++;
				endforeach; ?>
			</div>
			</div>
		</div>
		<?php include('footer.php'); ?>
	</body>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/deletepic.js"></script>
	<script type="text/javascript" src="scripts/likepic.js"></script>
	<script type="text/javascript" src="scripts/gallery.js"></script>
	<script type="text/javascript" src="scripts/rainbow.js"></script>
	<?php if (isset($_SESSION['logged_user'])): ?>
		<script>logged = '<?= $_SESSION['logged_user'] ?>';</script>
	<?php endif; ?>
</html>
