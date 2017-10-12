	<div class="top">
		<div class="logo">
			<a href="index.php">
				<span class="full_logo">Camagru</span>
				<div class="mini_logo">
					<i class="material-icons logo_icon">home</i>
					<!--<span>C</span><span class="mini_part">amagru</span>-->
				</div>
			</a>
		</div>
		<div class="top_btns">
		<?php if (isset($_SESSION['logged_user'])): ?>
			<div class="top_btn">
				<a class="top_link" href="create.php">
					<i class="top_lnk_icon material-icons">camera_alt</i>
					<span class="top_lnk_txt">Creer un montage</span>
				</a>
			</div>
		<?php endif; ?>
			<div class="top_btn">
				<a class="top_link" href="gallery.php">
					<i class="top_lnk_icon material-icons">photo_library</i>
					<span class="top_lnk_txt">Galerie</span>
				</a>
			</div>
		</div>
		<div class="top_usr">
			<?php if (isset($_SESSION['logged_user'])) : ?>
				<?php if (isset($_SESSION['logged_color']) && $_SESSION['logged_color'] !== 'rnbw'): ?>
						<div class="usr_name" style="color: #<?= $_SESSION['logged_color'] ?>;">
					<?php else: ?>
					<div class="usr_name <?php if (isset($_SESSION['logged_color']) && $_SESSION['logged_color'] === 'rnbw') echo 'rainbow'; ?>">
					<?php endif; ?>
					<?= $_SESSION['logged_user'] ?>
				</div>
				<a class="usr_btn" href="settings.php"><i class="material-icons md-24">settings</i></a>
				<a class="usr_btn" href="logout.php"><i class="material-icons md-24">power_settings_new</i></a>
			<?php else: ?>
				<a class="usr_btn" href="login.php"><i class="material-icons md-24">power_settings_new</i></a>
			<?php endif; ?>
		</div>
	</div>
