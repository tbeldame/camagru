<?php

$files = scandir('.');
foreach($files as $file)
{
	if (substr($file, -3) === 'png' && substr($file, 0, 6) !== 'thumb_')
	{
		$thumb = imagecreatetruecolor(100, 75);
		imagealphablending($thumb, false);
		$color = imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
		imagefill($thumb, 0, 0, $color);
		imagesavealpha($thumb, true);

		$source = imagecreatefrompng($file);
		imagealphablending($source, true);
		$ratio = 100 / imagesx($source);
		$x = intval($ratio * imagesx($source));
		$y = intval($ratio * imagesy($source));

		imagecopyresampled($thumb, $source, 0, 37 - $y / 2, 0, 0, $x, $y, imagesx($source), imagesy($source));
		imagealphablending($thumb, false);
		imagesavealpha($thumb, true);

		$ret = imagepng($thumb, 'thumb/' . $file, 9);
	}
}

?>
