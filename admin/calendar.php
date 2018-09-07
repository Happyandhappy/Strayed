<?php
include ("./config.php");
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
mysqli_set_charset($conn,"utf8");
$result = mysqli_query($conn, "SELECT * FROM events");
if (mysqli_num_rows($result) == 0)
	die('');
$ajd=array();
while($row = mysqli_fetch_assoc($result)) {
	unset($aj);
		$resulta = mysqli_query($conn, "SELECT * FROM users where id=" . $row['resperson']);
		while($rowa = mysqli_fetch_assoc($resulta)) {
			$resultaa = mysqli_query($conn, "SELECT * FROM colors where id=" . $rowa['color']);
			while($rowaa = mysqli_fetch_assoc($resultaa)) {
				$aj->color=$rowaa['bdesc'];
			}	
		}	
		$oprema=array();
		$resulta = mysqli_query($conn, "SELECT * FROM equipments where event=" . $row['id']);
		while($rowa = mysqli_fetch_assoc($resulta)) {
			$resultaa = mysqli_query($conn, "SELECT * FROM equipment where id=" . $rowa['equipment']);
			while($rowaa = mysqli_fetch_assoc($resultaa)) {
				array_push($oprema,$rowaa['bdesc']);
			}	
		}	
		$members=array();
		$resulta = mysqli_query($conn, "SELECT * FROM members where event=" . $row['id']);
		while($rowa = mysqli_fetch_assoc($resulta)) {
			$resultaa = mysqli_query($conn, "SELECT * FROM users where id=" . $rowa['employee']);
			while($rowaa = mysqli_fetch_assoc($resultaa)) {
				array_push($members,$rowaa['name']);
			}	
		}	
		$resulta = mysqli_query($conn, "SELECT * FROM users where id=" . $row['resperson']);
		while($rowa = mysqli_fetch_assoc($resulta)) {
			$aj->resperson=$rowa['name'];
		}	
	$aj->equipments=$oprema;
	$aj->members=$members;
	$aj->id=$row['id'];
	$aj->title=$row['name'];
	$aj->start=$row['datefrom'];
	$aj->end=$row['dateto'];
	$aj->location=$row['location'];
	$aj->description=$row['description'];
	array_push($ajd,$aj);
}
echo json_encode($ajd,JSON_UNESCAPED_UNICODE);
//var_dump($ajd);
mysqli_close($con);
?>