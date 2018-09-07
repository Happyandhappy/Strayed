<?php
$final=[];
include("dvapet.php");
$a = ucitaj("returnall");
foreach($a as $b){
	$ob[id]=$b[id];
	$ob[lat]=floatval($b[latitude]);
	$ob[lng]=floatval($b[longitude]);
	$ob[comments]=$b[comments];
	$ob[posttime]=$b[posttime];
	$ob[fullimg]="./imgdb/maindb.image.".$b[id].".jpg";
	$ob[img]="./preview/maindb.image.".$b[id].".jpg";
	$ob[thumb]="./thumbs/maindb.image.".$b[id].".jpg";


$ob[fullimg]="/hvimage.php?id=".$b[id];
$ob[img]="/hvimage.php?id=".$b[id];
$ob[thumb]="/hvimage.php?id=".$b[id];


	$ob[type]=$b[type];
	$ob[group]=$b[group];
	array_push($final,$ob);
}
$myJSON = json_encode($final);
echo $myJSON;
?>