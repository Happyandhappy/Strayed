<?php
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
	header('HTTP/1.1 304 Not Modified');
	die();
}

$image;

$a = (explode(".", $_GET['slika']));

$table = $a[0];
$column = $a[1];
$id = $a[2];
include ("./admin/config.php");
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
$result = mysqli_query($conn, "SELECT $column
                             FROM $table
                            WHERE id=$id LIMIT 1");
if (mysqli_num_rows($result) == 0)
	die('no image');
list($data) = mysqli_fetch_row($result);


$image = imagecreatefromstring($data);
if(isset($_GET['resizeH'])){
	$ratio = intval($_GET['resizeH'])/imagesy($image);
	$width = imagesx($image) * $ratio;
	$image = imagescale($image, $width);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400 * 365 + 60) . ' GMT', true, 200);
	header("Content-type: image/jpg");
	imagejpeg($image);
	imagedestroy($image);
}elseif(isset($_GET['resize'])){
	$ratio = intval($_GET['resize']) / imagesx($image);
	$width = imagesx($image) * $ratio;
	$image = imagescale($image, $width);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400 * 365 + 60) . ' GMT', true, 200);
	header("Content-type: image/jpg");
	imagejpeg($image);
	imagedestroy($image);
}else{
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400 * 365 + 60) . ' GMT', true, 200);
	header("Content-type: image/jpg");
	echo $data;
}
die();
?>