<?php
/*$db_host="localhost";
$db_user="root";
$db_pass="";
$db_db="admin_autonet";
$s_root="http://localhost:8080/";
$server=$s_root.'gallery/';*/
include('../config.php');
$server=$s_root.'admin/gallery/';
$ds = DIRECTORY_SEPARATOR;
if (!empty($_FILES)) {
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	$sqla='SELECT MAX(`id`) FROM `gimages`';
	$broj = intval(mysqli_fetch_row(mysqli_query($con, $sqla))[0])+1;
	$subfolder='/'.substr($broj,-1,1).'/'.substr($broj,-2,1).'/'.substr($broj,-3,1).'/';
	$storeFolder = 'm'.$subfolder;
	if (!is_dir($storeFolder)) {
		mkdir($storeFolder, 0755, true);
	}
	$mediumFolder = 'n'.$subfolder;
	if (!is_dir($mediumFolder)) {
		mkdir($mediumFolder, 0755, true);
	}
	$thumbFolder = 't'.$subfolder;
	if (!is_dir($thumbFolder)) {
		mkdir($thumbFolder, 0755, true);
	}
    $tempFile = $_FILES['file']['tmp_name'];
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
    $mediumPath = dirname( __FILE__ ) . $ds. $mediumFolder . $ds;
    $thumbPath = dirname( __FILE__ ) . $ds. $thumbFolder . $ds;
	$path_parts = pathinfo($_FILES["file"]["name"]);
	$ext = $path_parts['extension'];
	$targetFile =  $targetPath.$broj.'.'.$ext;
	$mediumFile =  $mediumPath.$broj.'.'.$ext;
	$thumbFile =  $thumbPath.$broj.'.'.$ext;
    if(move_uploaded_file($tempFile,$targetFile)){
		$img = resize_image($targetFile, 1200, 750);
		imagejpeg($img, $targetFile);
		$img = resize_image($targetFile, 480, 300);
		imagejpeg($img, $mediumFile);
		$img = resize_image($targetFile, 100, 74);
		imagejpeg($img, $thumbFile);
		$sql = "INSERT INTO gimages (server) VALUES ('".$server."')";
		mysqli_query($con, $sql);
	}
}


function resize_image($file, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}

if(isset($_POST['sortimages'])){
	$response = array();
	$array = json_decode($_POST['sortimages']);
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	foreach ($array->images as $key=>$value) {
		$sqla='update images set sort_index='.$value.' where image='.$key.' and article='.$array->postid.'';
		mysqli_query($con, $sqla);
	}


	$response['reply']="Success";
	echo json_encode($response);
}
if(isset($_POST['connectimage'])){
	$response = array();
	$array = json_decode($_POST['connectimage']);
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	$sqla='insert into images (article,image) values ('.$array->postid.','.$array->image.')';
	mysqli_query($con, $sqla);
	$response['reply']="Success";
	echo json_encode($response);
}
if(isset($_POST['bulkconnectimage'])){
	$response = array();
	$array = json_decode($_POST['bulkconnectimage']);
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	mysqli_query($con, 'CALL BulkImages('.$array->article_bulk.','.$array->firstimg_bulk.','.$array->lastimg_bulk.')');
	$response['sq']=$array;
	$response['reply']="Success";
	echo json_encode($response);
}
if(isset($_POST['updateimageprop'])){
	$response = array();
	$array = json_decode($_POST['updateimageprop']);
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	$sql='update gimages set title="'.$array->imagep_title.'" where id='.$array->imagep_id;
	mysqli_query($con, $sql);
	$response['sq']=$array;
	$response['reply']="Success";
	echo json_encode($response);
}
?>