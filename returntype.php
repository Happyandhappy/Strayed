<?php
$final=[];
include("dvapet.php");
$a = ucitaj("types","publish is not null");
foreach($a as $b){
	$ob[id]=$b[id];
	$ob[bdesc]=$b[bdesc];
	array_push($final,$ob);
}
$myJSON = json_encode($final);
echo $myJSON;
?>