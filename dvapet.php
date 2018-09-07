<?php
//error_reporting(0);
global $locale;
$jez='en';
include("admin/config.php");
include("admin/global.php");
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
mysqli_query($conn, "set names 'utf8'");
function ucitaj($sta,$kadje="",$sort="",$limit="") {
	global $conn;
	$data=array();
	$kolone[] = "id";
	$sql = "SHOW COLUMNS FROM $sta";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result);
	while ($row = mysqli_fetch_array($result)) {
		$kolone[] = $row['Field'];
	}

	if(!$kadje=="")$kadje=" where ".$kadje;
	if(!$sort=="")$sort=" order by $sort";
	if(!$limit=="")$limit=" limit $limit";
	$sql = "SELECT * FROM $sta$kadje$sort$limit";
	$result = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = $row;
	}
	return $data;
}
function datum($ulaz){
	global $datetimeformat,$jez;
	if($jez=="")$aa="m/d/Y H:i";
	if($jez=="sr")$aa="d.m.Y H.i";
	if($jez=="en")$aa="Y.m.d H.i";
	return date_format (date_create($ulaz),$aa);
}
function ReturnImage($id,$size="n",$index=null){
	if($index!=null)$zaindex='sort_index="'.$index.'" and ';else $zaindex="";
	$image=ucitaj("images",$zaindex."article=".$id,"sort_index","1");
	$folder=substr($image[0]['image'],-1,1).'/'.substr($image[0]['image'],-2,1).'/'.substr($image[0]['image'],-3,1).'/'.$image[0]['image'].'.jpg';
	$mainfolder=ucitaj("gimages","id=".$image[0]['image'],"","1");
	if(empty($image))return "/templates/noimage.jpg";
	else
	return $mainfolder[0]['server'].$size.'/'.$folder;
}