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

if($_GET['id'] == $b[id]){
header('Content-type: image/jpeg');
echo $b[image];
exit;
}



	$ob[type]=$b[type];
	$ob[group]=$b[group];
	array_push($final,$ob);
}
//$myJSON = json_encode($final);
//echo $myJSON;
?>