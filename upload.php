<?php
if(!empty($_POST['name']) || !empty($_POST['email']) || !empty($_FILES['file']['name'])){
	include("dvapet.php");
    $uploadedFile = '';
    if(!empty($_FILES["file"]["type"])){
        $fileName = time().'_'.$_FILES['file']['name'];
        $valid_extensions = array("jpeg", "jpg", "png");
        $temporary = explode(".", $_FILES["file"]["name"]);
        $file_extension = end($temporary);
        if((($_FILES["hard_file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")) && in_array($file_extension, $valid_extensions)){
            $sourcePath = $_FILES['file']['tmp_name'];
            /*$targetPath = "uploads/".$fileName;
            if(move_uploaded_file($sourcePath,$targetPath)){
                $uploadedFile = $fileName;
            }*/
			$fp = fopen($sourcePath, 'r');
			$content = fread($fp, filesize($sourcePath));
			fclose($fp);
        }
    };
	
  	$visitorname = mysqli_real_escape_string($conn, $_POST['name']);
  	$email = mysqli_real_escape_string($conn, $_POST['email']);
  	$phone = mysqli_real_escape_string($conn, $_POST['phone']);
  	$reporttype = mysqli_real_escape_string($conn, $_POST['newtype']);
  	$reportgroup = mysqli_real_escape_string($conn, $_POST['newgroup']);
  	$latitude = mysqli_real_escape_string($conn, $_POST['lat']);
  	$longitude = mysqli_real_escape_string($conn, $_POST['lon']);
  	$comments = mysqli_real_escape_string($conn, $_POST['comment']);
  	$posttime = mysqli_real_escape_string($conn, $_POST['taken2']);
  	$image = mysqli_real_escape_string($conn, $content);
  	//$image = "";
	
  	$sql = "INSERT INTO maindb (visitorname,email,phone,reporttype,reportgroup,latitude,longitude,comments,posttime,publish,image) VALUES ('$visitorname','$email','$phone','$reporttype','$reportgroup','$latitude','$longitude','$comments','$posttime',null,'$image')";
	$insert = mysqli_query($conn, $sql);
    echo $insert?'Record is saved into database and waiting for admin approval.':'There is something wrong.';
}
?>