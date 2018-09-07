<?php
	include("../config.php");
	session_name("dva_pet_" . $owner);
	session_start();
	include("./phptextClass.php");	
	
	/*create class object*/
	$phptextObj = new phptextClass();	
	/*phptext function to genrate image with text*/
	$phptextObj->phpcaptcha('#555','#eee',100,35,3,10,"#d85506");	
 ?>