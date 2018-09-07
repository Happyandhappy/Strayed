<?php
function t($input) {
	global $locale;
	if($locale=="")return $input;
	$fajl = "langs/" . $locale . ".txt";
	if (file_exists($fajl)) {
		$file_handle = fopen($fajl, "rb");
		$data = array();
		while (!feof($file_handle)) {
			$line_of_text = fgets($file_handle);
			$parts = explode('==', $line_of_text);
			$data[$parts[0]] = $parts[1];
		}
		fclose($file_handle);
	}else{die("nece");}
	if (@$data[$input] == "") {$izlaz = $input;
	} else {$izlaz = $data[$input];
	}
	$izlaz = str_replace("\n", "", $izlaz);
	$izlaz = str_replace("\r", "", $izlaz);
	return $izlaz;
}
function IfIsNull($var){
	return is_null($var) ? "" : $var;
}
function slika($tabela, $kolona, $id) {
	return "slika.php?slika=$tabela.$kolona.$id.jpg";
}
function copyright($startYear = null) {
	$thisYear = date('Y');
    if (!is_numeric($startYear)) {
		$year = $thisYear;
	} else {
		$year = intval($startYear);
	}
	if ($year == $thisYear || $year > $thisYear) {
		return "&copy; $thisYear";
	} else {
		return "&copy; $year&ndash;$thisYear";
	}   
}
function nacir($recenica){
	$izlaz=$recenica;
	$latmala=array("a","b","v","g","d","đ","e","ž","z","i","j","k","l","m","n","o","p","r","s","t","ć","u","f","h","c","č","š");
	$cirmala=array("а","б","в","г","д","ђ","е","ж","з","и","ј","к","л","м","н","о","п","р","с","т","ћ","у","ф","х","ц","ч","ш");
	$latvelika=array("A","B","V","G","D","Đ","E","Ž","Z","I","J","K","L","M","N","O","P","R","S","T","Ć","U","F","H","C","Č","Š");
	$cirvelika=array("А","Б","В","Г","Д","Ђ","Е","Ж","З","И","Ј","К","Л","М","Н","О","П","Р","С","Т","Ћ","У","Ф","Х","Ц","Ч","Ш");
	$latspecmala=array("dj","dJ","lj","lJ","nj","nJ","dz","dZ","dž","dŽ");
	$cirspecmala=array("ђ","ђ","љ","љ","њ","њ","џ","џ","џ","џ");
	$latspecvelika=array("Dj","DJ","Lj","LJ","Nj","NJ","Dz","DZ","Dž","DŽ");
	$cirspecvelika=array("Ђ","Ђ","Љ","Љ","Њ","Њ","Џ","Џ","Џ","Џ");
	
	$izlaz=str_replace($latspecvelika, $cirspecvelika, $izlaz);
	$izlaz=str_replace($latspecmala, $cirspecmala, $izlaz);
	$izlaz=str_replace($latvelika, $cirvelika, $izlaz);
	$izlaz=str_replace($latmala, $cirmala, $izlaz);
	return $izlaz;
}
?>