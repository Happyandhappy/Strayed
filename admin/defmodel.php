<?php
$obj = new crud;
include ("model.php");
//Create database object
$obj -> connect();
if($reset==1)die();
if(!isset($_SESSION['id'])){
	$GLOBALS['hop']=1;
	$user=new user;
	$_SESSION['id']="0";
	$_SESSION['username']="User";
}
?>
