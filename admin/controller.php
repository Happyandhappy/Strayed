<?php
switch (@$_REQUEST['ajax']) {
	case 'gettabledata' :
		global $obj;
		$table = $_REQUEST['table'];
		$primaryKey = 'id';
		$izlaz = array();
		$gdeje = array();
		if(@$_GET['filtert'])array_push($gdeje, $_GET['filterc'] . "=" . $_GET['filterid']);
		foreach ($obj->kolone as $kol) {
			if ($kol['0'] == $table) {
				if ($user -> getRestrictions("viewC", $table, $kol[1])) {
					array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) {
						return t("Data are hidden");
					}));
					continue;
				}
				if($user -> getRestrictions("limitD", $table, $kol['1']))array_push($gdeje, $user -> getRestrictions("limitD", $table, $kol['1']));
				switch ($kol['4']) {
					case "wysiwyg" :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol) {
							$opala = $row['id'];
							return '<div id="a' . $opala . '" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-body">
										' . html_entity_decode($d) . '
										</div>
									</div>
								</div>
							</div><a data-toggle="modal" data-target="#a' . $opala . '" class="btn btn-default btn-xs open-popup-link"><span class="glyphicon glyphicon-eye-open"></span></a>';
						}));
						break;
					case 'combo' :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol) {
							$a = combo::show($table, $kol['1'], $d);
							if (strlen($a) > 25)
								$a = substr($a, 0, 22) . '...';
							return htmlentities($a);
						}));
						break;
					case 'image' :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol) {
							return $d ? '<img max-height="70px" height="70px" src="' . slika($table, $kol['1'], $row['id']) . '"/>' : null;
							//return $d ? '<img max-height="70px" height="70px" src="data:image/jpeg;base64,' . base64_encode($d) . '"/>':null;
						}));
						break;
					case 'password' :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol) {
							return $d ? '***' : null;
						}));
						break;
					case 'check' :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol) {
							if ($d == "on")
								$checked = "checked";
							return '<input readonly data-size="mini" data-on-text="' . t('Yes') . '" data-off-text="' . t('No') . '" type="checkbox"' . @$checked . '/>';
						}));
						break;
					case 'date' :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol, $datetimeformat) {
							return $d ? date_format(date_create($d), $datetimeformat) : null;
						}));
						break;
					case 'file' :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1'], 'formatter' => function($d, $row) use ($table, $kol) {
							return $d ? '<a href="uploads/'.$d.'">'.$d.'</a>' : null;
							//return $d ? '<img max-height="70px" height="70px" src="data:image/jpeg;base64,' . base64_encode($d) . '"/>':null;
						}));
						break;
					default :
						array_push($izlaz, array('db' => $kol['1'], 'dt' => $kol['1']));
						break;
				}
			}
		}
		$sql_details = array('user' => $db_user, 'pass' => $db_pass, 'db' => $db_db, 'host' => $db_host);
		require ('ssp.class.php');
		//die(var_dump($gdeje));
		echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $izlaz, $gdeje), JSON_UNESCAPED_UNICODE);
		//echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $izlaz),JSON_UNESCAPED_UNICODE);
		die();
		break;
	case 'doesimgexist' :
        $id=$_GET['id'];
        $sql="select id from images where article=$id";
        $aa=$obj->executeSQL($sql);
		if(count($aa)==0)echo $id;
		die();
		break;
	case 'getcombo' :
		echo combo::updatewhere($_GET['tab'], $_GET['kol'], @$_GET['selected'], @$_GET['where']);
		die();
		break;
	default :
		break;
}

//Proverava funkcije za rad nad podacima u tabeli
switch (@$_REQUEST['function']) {
	case 'ucitajtroskove':
		ucitajtroskove(1,1,1,1);
		break;
	case 'delete' :
		$_REQUEST = array_map('htmlspecialchars', $_REQUEST);
		deleteDataFromTable($_GET['table'], $_GET['id']);
		break;
	case 'insert' :
		$_REQUEST = array_map('htmlspecialchars', $_REQUEST);
		/*		foreach ($_REQUEST as $key => $value) {
		 $_REQUEST[$key] = htmlspecialchars($value, ENT_QUOTES);
		 }*/
		foreach ($_FILES as $key => $value) {
			if ($_FILES[$key]['size'] > 0 && (($_FILES[$key]["type"] == "image/jpeg") || ($_FILES[$key]["type"] == "image/jpg"))) {
 				$tmpName = $_FILES[$key]['tmp_name'];
				$fp = fopen($tmpName, 'r');
				$content = fread($fp, filesize($tmpName));
				fclose($fp);
				$_REQUEST[$key] = $content;
			}elseif ($_FILES[$key]['size'] > 0 ) {
				$path = "uploads/";
			    $path = $path . basename( $_FILES[$key]['name']);
			    if(move_uploaded_file($_FILES[$key]['tmp_name'], $path)) {
			    	$_REQUEST[$key]=basename( $_FILES[$key]['name']);
			    }
	 
			}
		}
		insertDataIntoTable($_REQUEST['table'], $_REQUEST);
		break;
	case 'update' :
		$_REQUEST = array_map('htmlspecialchars', $_REQUEST);
		/*foreach ($_REQUEST as $key => $value) {
		 $_REQUEST[$key] = htmlspecialchars($value, ENT_QUOTES);
		 }*/
		foreach ($_FILES as $key => $value) {
			if ($_FILES[$key]['size'] > 0 && (($_FILES[$key]["type"] == "image/jpeg") || ($_FILES[$key]["type"] == "image/jpg"))) {
 				$tmpName = $_FILES[$key]['tmp_name'];
				$fp = fopen($tmpName, 'r');
				$content = fread($fp, filesize($tmpName));
				fclose($fp);
				$_REQUEST[$key] = $content;
			}elseif ($_FILES[$key]['size'] > 0 ) {
				$path = "uploads/";
			    $path = $path . basename( $_FILES[$key]['name']);
			    if(move_uploaded_file($_FILES[$key]['tmp_name'], $path)) {
			    	$_REQUEST[$key]=basename( $_FILES[$key]['name']);
			    }
	 
			}
		}
		updateDataIntoTable($_GET['table'], $_REQUEST, $_REQUEST['id']);
		break;
	default :
		break;
}

//Proverava status funkcija
switch (@$_GET['status']) {
	case 'insertsuccess' :
		$poruka = t("Insert success");
		break;
	case 'noinsert' :
		$porukam = t("You have no rights to append data in this table!");
		break;
	case 'insertfailed' :
		$porukam = t("Insert failed!");
		break;
	case 'updatesuccess' :
		$poruka = t("Update success");
		break;
	case 'noupdate' :
		$porukam = t("You have no rights to update data in this table!");
		break;
	case 'updatefailed' :
		$poruka = t("Update failed!");
		break;
	case 'deletesuccess' :
		$poruka = t("Delete success");
		break;
	case 'nodelete' :
		$porukam = t("You have no rights to delete data from this table!");
		break;
	default :
		$poruka = "";
		break;
}

switch (@$_REQUEST['print']) {
	case 'ino1' :
		stampaino1();
		break;
	case 'o125' :
		stampao125();
		break;
	default :
		break;
}

//Proverava koji deo ide za prikaz
switch (@$_REQUEST['show']) {
	case 'chat' :
			$trt=show::menu();
			$beforebody = 'var locale="' . $locale . '"; ' . file_get_contents("templates/js/beforebody.js");
			include("templates/chat/index.php");
		break;
	case 'table' :
		echo replaceVarInTheme("tabela.html");
		break;
	case 'qtable' :
		echo replaceVarInQTheme("tabela.html");
		break;
	case 'insert' :
		if ($user -> getRestrictions("insert", $table = $_GET['table'])) {
			echo '<script>
			$(document).ready(function () {
				var table = $("#glavnatabela").DataTable();
				table.ajax.reload( null, false ); 
				new parent.PNotify({
				   title: \'dva.pet\',
				   text: \'' . t("You have no rights to append data in this table!") . '\',
				   delay: 2000,
				   type: \'error\',
				   styling: \'bootstrap3\',
				   nonblock: true
				});
				$(".addnewmodal").modal("hide");
			});
			</script>';
			die();
		};
		echo show::insertForm();
		break;
	case 'update' :
		if ($user -> getRestrictions("updateT", $table = $_GET['table'])) {
			echo '<script>
			$(document).ready(function () {
				var table = $("#glavnatabela").DataTable();
				table.ajax.reload( null, false ); 
				new parent.PNotify({
				   title: \'dva.pet\',
				   text: \'' . t("You have no rights to update data in this table!") . '\',
				   delay: 2000,
				   type: \'error\',
				   styling: \'bootstrap3\',
				   nonblock: true
				});
				$(".editmodal").modal("hide");
			});
			</script>';
			die();
		}
		echo show::updateForm();
		break;
	default :
		echo replaceVarInTheme();
		break;
}

function replaceVarInTheme($maintheme = "index.html") {
	global $s_root, $appname, $appversion, $ownername, $locale, $develop_year;
	$theme = "";
	$theme = file_get_contents("templates/header.html");
	$theme .= file_get_contents("templates/$maintheme");
	$theme .= file_get_contents("templates/footer.html");
	$customjs = file_get_contents("templates/js/custom.js");
	$beforebody = 'var locale="' . $locale . '"; ' . file_get_contents("templates/js/beforebody.js");

	$theme = str_replace("{{beforebody}}", $beforebody, $theme);
	$theme = str_replace("{{customjs}}", $customjs, $theme);
	$theme = str_replace("{{appname}}", $appname, $theme);
	$theme = str_replace("{{appversion}}", $appversion, $theme);
	$theme = str_replace("{{korisnik}}", $_SESSION['username'], $theme);
	$theme = str_replace("{{mainmenu}}", show::menu(), $theme);
	$theme = str_replace("{{opistabele}}", IfIsNull(show::getTableData($vrsta = 'opistabele')), $theme);
	$theme = str_replace("{{tabela}}", IfIsNull(@$_GET['table']), $theme);
	$theme = str_replace("{{ownername}}", $ownername, $theme);
	$theme = str_replace("{{develop_year}}", copyright($develop_year), $theme);
	$theme = str_replace("{{kolone}}", IfIsNull(show::getTableData($vrsta = 'kolone')), $theme);
	$theme = str_replace("{{kolonezajs}}", IfIsNull(show::getTableData($vrsta = 'kolonezajs')), $theme);
	/*
	 $theme = str_replace("{{s_root}}", $s_root, $theme);
	 $theme = str_replace("{{poruka}}", IfIsNull(show::message()), $theme);
	 */
	return $theme;
}
function replaceVarInQTheme($maintheme = "index.html") {
	global $s_root, $appname, $appversion, $ownername, $locale, $develop_year;
	$theme = "";
	$theme = file_get_contents("templates/headerbezmeni.html");
	$theme .= file_get_contents("templates/$maintheme");
	$theme .= file_get_contents("templates/footerbezkopi.html");
	$customjs = file_get_contents("templates/js/custom.js");
	$beforebody = 'var locale="' . $locale . '"; ' . file_get_contents("templates/js/beforebody.js");

	$theme = str_replace("{{beforebody}}", $beforebody, $theme);
	$theme = str_replace("{{customjs}}", $customjs, $theme);
	$theme = str_replace("{{appname}}", $appname, $theme);
	$theme = str_replace("{{appversion}}", $appversion, $theme);
	$theme = str_replace("{{korisnik}}", $_SESSION['username'], $theme);
	$theme = str_replace("{{mainmenu}}", show::menu(), $theme);
	$theme = str_replace("{{opistabele}}", IfIsNull(show::getTableData($vrsta = 'opistabele')), $theme);
	$theme = str_replace("{{tabela}}", IfIsNull(@$_GET['table']), $theme);
	$theme = str_replace("{{ownername}}", $ownername, $theme);
	$theme = str_replace("{{develop_year}}", copyright($develop_year), $theme);
	$theme = str_replace("{{kolone}}", IfIsNull(show::getTableData($vrsta = 'kolone')), $theme);
	$theme = str_replace("{{kolonezajs}}", IfIsNull(show::getTableData($vrsta = 'kolonezajs')), $theme);
	/*
	 $theme = str_replace("{{s_root}}", $s_root, $theme);
	 $theme = str_replace("{{poruka}}", IfIsNull(show::message()), $theme);
	 */
	return $theme;
}
?>
