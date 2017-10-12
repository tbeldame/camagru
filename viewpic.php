<?php
if (session_status() === PHP_SESSION_NONE)
	session_start();
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
$req = 'SELECT p.*, GROUP_CONCAT(u.username SEPARATOR \'.\') as \'likers\', u2.username as \'creator\'
	FROM pictures as p
	LEFT JOIN likes as l ON l.picid = p.id
	LEFT JOIN users as u ON u.id = l.userid
	LEFT JOIN users as u2 on u2.id = p.userid
	WHERE p.id=:picid';
try
{
	$sth = $sql->prepare($req);
	$sth->execute([':picid' => $_GET['pic']]);
	$result = $sth->fetch(PDO::FETCH_ASSOC);
}
catch (Exception $e)
{
	exit('Erreur' . $e);
}
if (!isset($result['id']))
{
	header('Location: gallery.php');
	exit();
}
if (isset($result['likers']))
{
	$likers = explode('.', $result['likers']);
	$like_nb = count($likers);
}
else
{
	$like_nb = 0;
	$likers = [];
}

$comments = [];
$req = 'SELECT comments.*, users.username
	FROM comments
	LEFT JOIN users ON users.id=comments.userid
	WHERE picid=:picid
	ORDER BY date DESC';
try
{
	$sth = $sql->prepare($req);
	$sth->execute([':picid' => $_GET['pic']]);
	if ($sth->rowCount() > 0)
		$comments = $sth->fetchAll(PDO::FETCH_ASSOC);
}
catch (Exception $e)
{
	exit('Erreur' . $e);
}
?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<link rel="stylesheet" type="text/css" href="css/viewpic.css">
		<meta charset="UTF-8">
		<meta name="theme-color" content="#00838F">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="icon" type="image/png" href="images/icons/favicon.png">
		<title>Camagru</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700|Lato:400,300,700|Roboto:400,300,100,500' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">	
	</head>

	<body>
		<script>window.twttr = (function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0],
			t = window.twttr || {};
			if (d.getElementById(id)) return t;
			js = d.createElement(s);
			js.id = id;
			js.src = "https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js, fjs);

			t._e = [];
			t.ready = function(f) {
				t._e.push(f);
			 };

			return t;
			}(document, "script", "twitter-wjs"));</script>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.8";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>

		<div class="global">
			<?php include('header.php'); ?>
			<div class="content">
			<div class="picture">
				<div class="pic_container">
					<img class="pic_img" src="images/pictures/<?= $result['filename'] ?>"></img>
				</div>
				<div class="infos" id="<?= $result['id'] ?>">
					<div class="creator">
						<?= $result['creator'] ?>
					</div>
					<div class="date" title="<?= date('d/m/Y H:i:s', strtotime($result['date'])) ?>">
						<?= getelapsedtime($result['date']) ?>
					</div>
					<?php if (isset($_SESSION['logged_user'], $likers) && in_array($_SESSION['logged_user'], $likers)): ?>
						<div class="like_btn liked" liked="1">
							<i class="material-icons">thumb_up</i>
						</div>
					<?php else: ?>
						<div class="like_btn" liked="0">
							<i class="material-icons">thumb_up</i>
						</div>
					<?php endif; ?>
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
						<?php $address = 'http://' . $_SERVER['HTTP_HOST'] . '/viewpic.php?pic=' . $_GET['pic']; ?>
						<div class="fb_btn">
							<div class="fb-share-button" data-href="<?= $address ?>" data-layout="button" data-size="small" data-mobile-iframe="false">
								<a class="fb-xfbml-parse-ignore" target="_blank" href="<?= urlencode($address) ?>&amp;src=sdkpreparse">Partager</a>
							</div>
						</div>
						<div class="tw_btn">
							<a class="twitter-share-button" href="https://twitter.com/intent/tweet?<?= urlencode($address) ?>">Tweet</a>
						</div>
						<?php if (isset($_SESSION['logged_user']) && $_SESSION['logged_user'] === $result['creator']): ?>
							<div class="del_btn">
								<i class="material-icons">delete</i>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="comment_area">
					<?php if (isset($_SESSION['logged_user'])): ?>
						<div class="new_comment">
							<textarea id="comment_box" class="new_com_txt" placeholder="Commenter"></textarea>
							<input type="button" id="post_comment" class="post_com" value="Envoyer">
							<div class="com_length" id="rem_chars">2000</div>
						</div>
					<?php endif; ?>

					<div id="comments">
					<?php if (!empty($comments)): ?>
						<?php foreach($comments as $comment): ?>
							<div class="comment" comid="<?= $comment['id'] ?> ">
								<div class="com_author">
									<?= $comment['username'] ?>
								</div>
								<div class="com_date" title="<?= date('d-m-Y \a H:i', strtotime($comment['date'])) ?>">
									<?= getelapsedtime($comment['date']) ?>
								</div>
								<div class="com_txt">
									<?= htmlspecialchars($comment['comment']) ?>
								</div>
								<div class="del_com">
									<?php if (isset($_SESSION['logged_id']) && $comment['userid'] === $_SESSION['logged_id']): ?>
										<i class="material-icons">delete</i>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php include('footer.php'); ?>
	</body>
	<script> var id = <?= $result['id'] ?>; <?php if (isset($_SESSION['logged_user'])) echo 'logged = "' . $_SESSION['logged_user'] . '";'; ?> </script>
	<script type="text/javascript" src="scripts/infos.js"></script>
	<script type="text/javascript" src="scripts/likepic.js"></script>
	<script type="text/javascript" src="scripts/viewpic.js"></script>
	<script type="text/javascript" src="scripts/comment.js"></script>
	<script type="text/javascript" src="scripts/rainbow.js"></script>

</html>
