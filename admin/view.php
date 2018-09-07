<?php
class show {
	public static function menu() {
		global $obj, $user;
		$izlaz = '';
		$meniji = array();
		foreach ($obj->tabele as $key => $value) {
			if ($user -> getRestrictions("viewT", $value[0]))
						continue;
			if (!in_array($value[3], $meniji))
				$meniji[] = $value[3];
		}
		foreach ($meniji as $meni) {
			if ($meni == "") {
			} elseif ($meni == "main") {
				$izlaz.='<ul class="nav navbar-nav">';
				foreach ($obj->tabele as $key => $value) {
					if ($user -> getRestrictions("viewT", $value[0]))
						continue;
					if ($value[3] == $meni)
						$izlaz .= '<li><a href="./?show=table&table=' . $value[0] . '">' . $value[1] . '</a></li>';
				}
				$izlaz.="</ul>";
			} else {
				$izlaz .= '<ul class="nav navbar-nav">
                  <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.$meni.'<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">';
				foreach ($obj->tabele as $key => $value) {
					if ($user -> getRestrictions("viewT", $value[0]))
						continue;
					if ($value[3] == $meni)
						$izlaz .= '<li><a href="./?show=table&table=' . $value[0] . '">' . $value[1] . '</a></li>';
				}
				$izlaz .= '</ul></li></ul>';
			}
		}
		$izlaz .= '';
		return $izlaz;
	}

	public static function message() {
		global $poruka, $porukam;
		if ($poruka != "")
			$poruka = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $poruka . '</div>';
		if ($porukam != "")
			$poruka = '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $porukam . '</div>';
		return $poruka;
	}

	public static function getTableData($vrsta='', $table = '') {
		global $obj, $user, $datetimeformat;
		if ($user -> getRestrictions("viewT", @$_GET['table']))
			return '<div class="alert alert-danger">' . t("You do not have sufficient permissions to access this page!") . '</div>';
		if ($table == "")
			$table = @$_GET['table'];
		if ($table == "") {
			return;
		}
		//Check if table exist in table array
		$a = "";
		foreach ($obj->tabele as $value) {
			if ($value[0] == $table)
				$a = 1;
		}
		if ($a !== 1)
			return ('<div class="alert alert-danger">' . t('There is no table with such name!') . '</div>');

			$izlaz= array();
			$izlaz['opistabele']=$obj -> getTableDesc($table);
			$izlaz['kolone'] ="";
			foreach ($obj->kolone as $kol)
				if ($kol['0'] == $table) {
					switch ($kol['4']){
						case "combo":
							$izlaz['kolone'] .= '<th col-name="' . $kol['1'] . '" class="select-filter">' . $kol['2'] . '</th>';
						break;
						/*case "date":
							$izlaz['kolone'] .= '<th col-name="' . $kol['1'] . '" class="datetimepicker">' . $kol['2'] . '</th>';
						break;*/
						default:
							$izlaz['kolone'] .= '<th col-name="' . $kol['1'] . '" class="normalan">' . $kol['2'] . '</th>';
						break;
					}
				}

			$izlaz['kolonezajs'] ="";
			foreach ($obj->kolone as $kol)
				if ($kol['0'] == $table) {
					$izlaz['kolonezajs'] .= '{ "data": "' . $kol['1'] . '"},';
				}
			return $izlaz[$vrsta];
	}

	public static function tableData($table = '') {
		global $obj, $user, $datetimeformat;
		if ($user -> getRestrictions("viewT", $_GET['table']))
			return '<div class="alert alert-danger">' . t("You do not have sufficient permissions to access this page!") . '</div>';
		if ($table == "")
			$table = $_GET['table'];
		if ($table == "") {
			return;
		}
		//Check if table exist in table array
		$a = "";
		foreach ($obj->tabele as $value) {
			if ($value[0] == $table)
				$a = 1;
		}
		if ($a !== 1)
			return ('<div class="alert alert-danger">' . t('There is no table with such name!') . '</div>');

		//Main proccess
		$izlaz = "";
		$izlaz .= '<div class="gornjipanel"><a class="dugme btn btn-primary btn-sm" href="./?show=insert&table=' . $table . '"><span class="glyphicon glyphicon-plus"></span> ' . t('Add') . '</a><label style="padding-left:20px;">' . $obj -> getTableDesc($table) . '</label></div>';

		$izlaz .= '<div class="table-responsive"><table class="table table-hover table-condensed"><tr>';
		$izlaz .= '<th class="limitiran">' . t('Functions') . '</th>';
		foreach ($obj->kolone as $kol)
			if ($kol['0'] == $table) {
				$izlaz .= '<th>' . $kol['2'] . '</th>';
			}
		$izlaz .= '</tr>';
		foreach ($obj->showData($table) as $value) {
			$izlaz .= '<tr>';
			$izlaz .= '<td class="limitiran">
<a class="btn btn-danger btn-sm" onclick="return confirm(\'Да ли сте сигурни?\')" href="./?function=delete&table=' . $table . '&id=' . $value['id'] . '" title="' . t('Remove') . '"><span class="glyphicon glyphicon-remove"></span></a>
<a class="dugme btn btn-warning btn-sm" href="./?show=update&table=' . $table . '&id=' . $value['id'] . '" title="' . t('Edit') . '"><span class="glyphicon glyphicon-pencil"></span></a>
</td>';
			foreach ($obj->kolone as $kol)
				if ($kol['0'] == $table) {
					if ($user -> getRestrictions("viewC", $table, $kol[1])) {$izlaz .= "<td><span class='glyphicon glyphicon-eye-close'></span></td>";
						continue;
					}
					switch ($kol[
					'4']) {
						case "check" :
							$checked = "";
							if ($value[$kol[1]] == "on")
								$checked = "checked";
							$izlaz .= '<td><input readonly data-size="mini" data-on-text="' . t('Yes') . '" data-off-text="' . t('No') . '" type="checkbox"' . $checked . '/></td>';
							break;
						case "combo" :
							$izlaz .= '<td>' . combo::show($table, $kol[1], $value[$kol[1]]) . '</td>';
							break;
						case "date" :
							$izlaz .= '<td>' . date_format(date_create($value[$kol['1']]), $datetimeformat) . '</td>';
							break;
						case "file" :
							if ($value[$kol['1']] <> "") {
								$izlaz .= '<td>' . t('Download file') . '</td>';
							} else {
								$izlaz .= "<td>" . t('No file') . "</td>";
							}
							break;
						case "image" :
							if ($value[$kol['1']] <> "") {
								$izlaz .= '<td>' . '<img height="70px" src="data:image/jpeg;base64,' . base64_encode($value[$kol['1']]) . '"/>' . '</td>';
							} else {
								$izlaz .= "<td>" . t('No image') . "</td>";
							}
							break;
						case "wysiwyg" :
							$a = html_entity_decode($value[$kol['1']]);
							$izlaz .= '<td><div id="a' . $kol['1'] . $value['id'] . '" class="white-popup mfp-hide">' . $a . '</div><a href="#a' . $kol['1'] . $value['id'] . '" class="btn btn-default open-popup-link"><span class="glyphicon glyphicon-eye-open"></span></a></td>';
							break;
						default :
							$izlaz .= '<td>' . $value[$kol['1']] . '</td>';
							break;
					}
				}
			$izlaz .= '</tr>';
		}
		$izlaz .= '</table></div>';
		return $izlaz;
	}

	public static function viewData($view = "") {
		global $obj;
		if ($view == "")
			$view = $_GET['view'];
		if ($view == "") {
			return;
		}
		$izlaz = "";
		$izlaz .= '<div class="table-responsive"><table class="table table-hover table-condensed"><tr>';
		foreach ($obj->pogledi as $value) {
			if ($value[0] == $view) {
				foreach (explode(",",$value[6]) as $kvalue) {
					$izlaz .= "<th>" . $value[4] . $kvalue . "</th>";
				}
				foreach (explode(",",$value[7]) as $kvalue) {
					$izlaz .= "<th>" . $value[5] . $kvalue . "</th>";
				}
			}
		}
		/*foreach ($obj->kolone as $kol)
		 if ($kol['0'] == $view) {
		 $izlaz .= '<th>' . $kol['2'] . '</th>';
		 }*/
		$izlaz .= '</tr>';
		foreach ($obj->showData($view) as $value) {
			$izlaz .= '<tr>';
			foreach ($value as $kljuc => $vrednost)
				var_dump($value);
			$izlaz .= '</tr>';
		}
		$izlaz .= '</table></div>';
		return $izlaz;
	}

	public static function insertForm($table = "") {
		global $obj,$locale,$datetimeformat;
		$izlaz = "";
		if ($table == "")
			$table = $_GET['table'];
		if ($table == "")
			return;

		$izlaz .= '<div class="row"><form id="addnewform" class="form-horizontal" data-toggle="validator" action="./?table=' . $table . '" enctype="multipart/form-data" method="post">';
		foreach ($obj->kolone as $kol)
			if ($kol['0'] == $table) {
				$seo = $obj -> getOption($table, $kol['1'], "seo");
				$default = $obj -> getOption($table, $kol['1'], "default");
				if(@$_GET['filtert']==$table && @$_GET['filterc']==$kol['1'])$default=$_GET['filterid'];
				$required = $obj -> getOption($table, $kol['1'], "required");
				$class = $obj -> getOption($table, $kol['1'], "class") ? $obj -> getOption($table, $kol['1'], "class") : "col-xs-6";
				if($kol['4']=="pk")$class.=" hidden";
				$izlaz .= '<div class="form-group form-group-sm '.$class.'">
<label for="' . $kol['1'] . '" class="control-label">' . $kol['2'] . '</label>';
				switch ($kol['4']) {
					case "seo" :
						$izlaz .= $seo . '<input class="form-control" type="text" value="' . $default . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/>';
						break;
					case "check" :
						$izlaz .= '<br><input type="hidden" name="' . $kol['1'] . '" value=""/><input class="form-control" name="' . $kol['1'] . '" id="' . $kol['1'] . '" data-on-text="' . t('Yes') . '" data-off-text="' . t('No') . '" type="checkbox" ' . $required . '/>';
						break;
					case 'textarea' :
						$izlaz .= '<textarea class="form-control" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '>' . $default . '</textarea>';
						break;
					case 'wysiwyg' :
						$izlaz .= '<textarea class="form-control wysiwyg" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '>' . $default . '</textarea>';
						break;
					case "combo" :
						$a=0;
						foreach ($obj->dependencies as $key => $value) {
							if($value[0]==$table && $value[1]==$kol[1]){
								$izlaz.= '<script>
								$("#'.$value[1].'").on("change",function(){
									NProgress.configure({ parent: \'.addnewmodal .modal-body\' });
									NProgress.start();
									var selektovan=$(this).val();
									var link="./?ajax=getcombo&tab='.$value[0].'&kol='.$value[2].'&where=where%20'.$value[1].'="+selektovan;
									$(document).load(link, function(responseTxt, statusTxt, xhr) {
										if (statusTxt == "success") {
											NProgress.done();
											NProgress.configure({ parent: \'body\' });
											$( "#'.$value[2].'" ).selectpicker("destroy");
											$( "#'.$value[2].'" ).replaceWith(responseTxt);
											$( "#'.$value[2].'" ).selectpicker({
												liveSearch: true,
												title: p("Ništa nije odabrano"),
								  				size: false
											});
										}
										if (statusTxt == "error"){
											NProgress.done();
											NProgress.configure({ parent: \'body\' });
											alert("Error: " + xhr.status + ": " + xhr.statusText);
										}
									});
								});
								</script>';
								$izlaz .= combo::update($table, $kol[1], $default);
								$a=1;
							}elseif($value[0]==$table && $value[2]==$kol[1]){
								$op = $obj->getOption($value[0], $value[1], "default") ? $obj->getOption($value[0], $value[1], "default") : "0";
								$izlaz .= combo::updatewhere($table, $kol[1], $default, "where ".$value[1]."=".$op);
								$a=1;
							}
						}
						if($a==0)$izlaz .= combo::update($table, $kol[1], $default);
						break;
					case "file" :
						$izlaz .= '<input class="form-control" type="file" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/><span>' . $kol['3'] . '</span>';
						break;
					case "image" :
						$izlaz .= '<input class="form-control" type="file" accept="image/jpeg" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/><span>' . $kol['3'] . '</span>';
						break;
					case "date" :
						$izlaz .= '<input class="form-control datetimepicker" type="text" value="' . $default . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/>';
						break;
					case "tags" :
						$izlaz .= '<input class="form-control tags" type="text" value="' . $default . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/>';
						break;
					case "pk" :
						$izlaz .= '<input type="hidden" value="' . $default . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/>';
						break;
					case "password" :
						$izlaz .= '<input class="form-control" type="password" value="' . $default . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/>';
						break;
					default :
						$izlaz .= '<input class="form-control" type="text" value="' . $default . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '"' . $required . '/>';
						break;
				}

				$izlaz .= '</div>';
			}
		$izlaz .= '<input type="hidden" name="function" id="function" value="insert">
</form></div>';
$izlaz.='<script>
	$("#description").on("click", function(){
		$("#description").text(jQuery.trim($(".note-editable").text()).substring(0, 130).split(" ").slice(0, -1).join(" ") + "...");
	})
			$.datetimepicker.setLocale(\''.$locale.'\');
			$(\'.datetimepicker\').datetimepicker({format:\''.$datetimeformat.'\'});
		    $(".tags").tagsinput({trimValue: true});
			
var InsertIFrame = function (context) {
var ui = $.summernote.ui;
var ranges = [
  \'\ud83c[\udf00-\udfff]\', // U+1F300 to U+1F3FF
  \'\ud83d[\udc00-\ude4f]\', // U+1F400 to U+1F64F
  \'\ud83d[\ude80-\udeff]\'  // U+1F680 to U+1F6FF
];
function removeInvalidChars(str) {
  str = str.replace(new RegExp(ranges.join(\'|\'), \'g\'), \'\');
  return str;
}
var button = ui.button({
	contents: \'<i class="fa fa-child"/> iFrame\',
	tooltip: \'Insert IFrame\',
	click: function () {
		var node = document.createElement(\'div\');
		var hah=prompt("Paste your code here:");
		$(node).html(removeInvalidChars(hah));
		context.invoke(\'editor.insertNode\', node);
	}
});
return button.render();
}
	$(".wysiwyg").summernote({
  toolbar: [
    [\'style\', [\'bold\', \'italic\', \'underline\', \'clear\']],
    [\'font\', [\'strikethrough\', \'superscript\', \'subscript\']],
    [\'fontsize\', [\'fontsize\']],
    [\'color\', [\'color\']],
    [\'para\', [\'ul\', \'ol\', \'paragraph\']],
    [\'insert\', [\'link\',\'picture\',\'video\']],
	[\'mybutton\', [\'iFrame\']]
  ],
  buttons: {
    iFrame: InsertIFrame
  },
  height:150, lang: \''.$locale.'\'});
		    $("[type=\'checkbox\']").bootstrapSwitch({"size":"small"});
			$(\'.selectpickera\').selectpicker({
				liveSearch: true,
				title: p("Ništa nije odabrano"),
  				size: false
			});
		</script>
		';
		return $izlaz;

	}

	public static function updateForm($table = "", $id = "") {
		global $obj, $locale, $datetimeformat;
		$izlaz = "";
		if ($table == "")
			$table = $_GET['table'];
		if ($id == "")
			$id = $_GET['id'];
		if (($table == "") or ($id == ""))
			return;

		$daci = $obj -> getById($id, $table);
		$izlaz .= '<div class="row"><form id="editform" class="form-horizontal" data-toggle="validator" action="./?table=' . $table . '" enctype="multipart/form-data" method="post">';
		foreach ($obj->kolone as $kol)
			if ($kol['0'] == $table) {
				$required = $obj -> getOption($table, $kol['1'], "required");
				$class = $obj -> getOption($table, $kol['1'], "class") ? $obj -> getOption($table, $kol['1'], "class") : "col-sm-6";
				if($kol['4']=="pk")$class.=" hidden";
				$izlaz .= '<div class="form-group form-group-sm '.$class.'">
<label for="' . $kol['1'] . '" class="control-label">' . $kol['2'] . '</label>';
				switch ($kol['4']) {
					case "check" :
						$checked = "";
						if ($daci[$kol['1']] == "on")
							$checked = "checked";
						$izlaz .= '<br><input type="hidden" name="' . $kol['1'] . '" value=""/><input class="form-control" data-on-text="' . t('Yes') . '" data-off-text="' . t('No') . '" name="' . $kol['1'] . '" id="' . $kol['1'] . '" type="checkbox"' . $checked . '/>';
						break;
					case 'textarea' :
						$izlaz .= '<textarea class="form-control" name="' . $kol['1'] . '" id="' . $kol['1'] . '" ' . $required . '>' . $daci[$kol['1']] . '</textarea>';
						break;
					case 'wysiwyg' :
						$izlaz .= '<textarea class="form-control wysiwyg" name="' . $kol['1'] . '" id="' . $kol['1'] . '" ' . $required . '>' . $daci[$kol['1']] . '</textarea>';
						break;
					case "combo" :
						$a=0;
						foreach ($obj->dependencies as $key => $value) {
							if($value[0]==$table && $value[1]==$kol[1]){
								$izlaz.= '<script>
								$("#'.$value[1].'").on("change",function(){
									NProgress.configure({ parent: \'.editmodal .modal-body\' });
									NProgress.start();
									var selektovan=$(this).val();
									var link="./?ajax=getcombo&tab='.$value[0].'&kol='.$value[2].'&where=where%20'.$value[1].'="+selektovan;
									$(document).load(link, function(responseTxt, statusTxt, xhr) {
										if (statusTxt == "success") {
											NProgress.done();
											NProgress.configure({ parent: \'body\' });
											$( "#'.$value[2].'" ).selectpicker("destroy");
											$( "#'.$value[2].'" ).replaceWith(responseTxt);
											$( "#'.$value[2].'" ).selectpicker({
												liveSearch: true,
												title: p("Ništa nije odabrano"),
								  				size: false
											});
										}
										if (statusTxt == "error"){
											NProgress.done();
											NProgress.configure({ parent: \'body\' });
											alert("Error: " + xhr.status + ": " + xhr.statusText);
										}
									});
								});
								</script>';
								$izlaz .= combo::update($table, $kol[1], $daci[$kol['1']]);
								$a=1;
							}elseif($value[0]==$table && $value[2]==$kol[1]){
								$op = $daci[$value[1]] ? $daci[$value[1]] : "0";
								$izlaz .= combo::updatewhere($table, $kol[1], $daci[$kol['1']], "where ".$value[1]."=".$op);
								$a=1;
							}
						}
						if($a==0)$izlaz .= combo::update($table, $kol[1], $daci[$kol['1']]);
						break;
					case "file" :
						$izlaz .= '<input class="form-control" type="file" name="' . $kol['1'] . '" id="' . $kol['1'] . '" ' . $required . '/><span>' . $kol['3'] . '</span>';
						break;
					case "image" :
						$izlaz.='<br><img max-height="70px" height="70px" src="'.slika($table, $kol['1'], $id).'"/>';
						$izlaz .= '<input class="form-control" accept="image/jpeg" type="file" name="' . $kol['1'] . '" id="' . $kol['1'] . '" ' . $required . '/><span>' . $kol['3'] . '</span>';
						break;
					case "date" :
						$eee=$daci[$kol['1']] ? date_format(date_create($daci[$kol['1']]), $datetimeformat) : null;
						$izlaz .= '<input class="form-control datetimepicker" type="text" name="' . $kol['1'] . '" id="' . $kol['1'] . '" value="' . $eee . '" ' . $required . '/>';
						break;
					case "pk" :
						$izlaz .= '<input type="hidden" name="' . $kol['1'] . '" id="' . $kol['1'] . '" value="' . $daci[$kol['1']] . '" ' . $required . '/>';
						break;
					case "tags" :
						$izlaz .= '<input class="form-control tags" type="text" name="' . $kol['1'] . '" id="' . $kol['1'] . '" value="' . $daci[$kol['1']] . '" ' . $required . '/>';
						break;
					case "password" :
						$izlaz .= '<input class="form-control" type="password" name="' . $kol['1'] . '" id="' . $kol['1'] . '" value="' . $daci[$kol['1']] . '" ' . $required . '/>';
						break;
					default :
						$izlaz .= '<input class="form-control" type="text" name="' . $kol['1'] . '" id="' . $kol['1'] . '" value="' . $daci[$kol['1']] . '" ' . $required . '/>';
						break;
				}
				$izlaz .= '</div>';
			}
		$izlaz .= '<input type="hidden" name="function" id="function" value="update">
</form></row><div style="clear:both;"></div>';
$izlaz.='		<script>
	$("#description").on("click", function(){
		$("#description").text(jQuery.trim($(".note-editable").text()).substring(0, 130).split(" ").slice(0, -1).join(" ") + "...");
	})
			$.datetimepicker.setLocale(\''.$locale.'\');
			$(\'.datetimepicker\').datetimepicker({format:\''.$datetimeformat.'\'});
		    $(".tags").tagsinput({trimValue: true});
	$(".wysiwyg").summernote({height:150, lang: \''.$locale.'\'});
		  	$("[type=\'checkbox\']").bootstrapSwitch({"size":"small"});
			$(\'.selectpickera\').selectpicker({
				liveSearch: true,
 				title: p("Ništa nije odabrano"),
  				size: false
			});
		</script>
		';
		return $izlaz;

	}

}

function deleteDataFromTable($table = "", $id = "") {
	global $obj, $user;
	if (($table == "") or ($id == ""))
		return;

	if ($user -> getRestrictions("delete", $table)) {
		echo '<script>
			new parent.PNotify({
			   title: \'dva.pet\',
			   text: \''.t("You have no rights to delete data from this table!").'\',
				delay: 2000,
			   type: \'error\',
			   styling: \'bootstrap3\',
				   nonblock: true
			});
		</script>';
		die();
	}
	if ($obj -> deleteData($id, $table)) {
		echo '<script>
			new parent.PNotify({
			   title: \'dva.pet\',
			   text: \''.t("Delete success").'\',
				delay: 2000,
			   type: \'success\',
			   styling: \'bootstrap3\',
				   nonblock: true
			});
		</script>';
		die();
	}

}

function insertDataIntoTable($table = "", $data) {
	global $obj, $user;
	if ($table == "")
		return;

	if ($user -> getRestrictions("insert", $table)) {
		echo '<script>
			new parent.PNotify({
			   title: \'dva.pet\',
			   text: \''.t("Insert failed!").'\',
				delay: 2000,
			   type: \'error\',
			   styling: \'bootstrap3\',
				   nonblock: true
			});
		</script>';
		die();
	}
	if ($obj -> insertData($data, $table)) {
		echo '<script>
			new PNotify({
			   title: \'dva.pet\',
			   text: \''.t("Insert success").'\',
				delay: 2000,
			   type: \'success\',
			   styling: \'bootstrap3\',
				   nonblock: true
			});
		</script>';
		eval($obj->getFunction($_REQUEST['table'], "after_insert"));
		die();
	}
}

function updateDataIntoTable($table = '', $data, $id) {
	global $obj, $user;
	if ($table == "")
		return;

	if ($user -> getRestrictions("updateT", $table)) {
		echo '<script>
			new parent.PNotify({
			   title: \'dva.pet\',
			   text: \''.t("Update failed!").'\',
				delay: 2000,
			   type: \'error\',
			   styling: \'bootstrap3\',
				   nonblock: true
			});
		</script>';
		die();
	}
	//extract($data);
	if ($obj -> update($data, $table, $id)) {
		echo '<script>
			new parent.PNotify({
			   title: \'dva.pet\',
			   text: \''.t("Update success").'\',
				delay: 2000,
			   type: \'success\',
			   styling: \'bootstrap3\',
				   nonblock: true
			});
		</script>';
		die();
	}

}

class combo {
	public static function show($table, $column, $value) {
		global $obj;
		if ($value == "")
			return;
		$ret = "";
		foreach ($obj->veze as $temp) {
			if ($temp[2] == $table && $temp[3] == $column) {
				$sql = "select * from $temp[0] where $temp[1]='$value'";
				$a = $obj -> ExecuteSQL($sql);
				foreach ($a[0] as $key => $kvalue) {
					if (in_array($key, explode(",", $temp[4])))
						$ret .= $kvalue . " ";
				}
			}
		}
		return $ret;
	}
	public static function update($table, $column, $id) {
		global $obj, $user;
		$ret = "";
		foreach ($obj->veze as $temp) {
			if ($temp[2] == $table && $temp[3] == $column) {
				$dodatno="";
				if($temp[0]=="srepublika")$dodatno=" and isnull(vazido)";
				if($temp[0]=="sinokuca")$dodatno=" and aktivan='A'";
				if($temp[0]=="brand")$dodatno.=" order by bdesc asc";
				if($temp[0]=="category")$dodatno.=" order by bdesc asc";
				if($temp[0]=="news_source")$dodatno.=" order by name asc";
				if($user->getRestrictions("limitA",$table,$column))
					$sql = "select * from $temp[0] where id=".$user->getRestrictions("limitA",$table,$column) . $dodatno;
				else
					$sql = "select * from $temp[0]"." where 1=1".$dodatno;
				$a = $obj -> ExecuteSQL($sql);
				$required=$obj -> getOption($table, $column, "required") ? $obj -> getOption($table, $column, "required"):"";
				$ret .= '<select '.$required.' name="' . $column . '" id="' . $column . '" class="form-control selectpickera">';
				$ret .= '<option value=""></option>';
				foreach ($a as $fvalue) {
					if ($id == $fvalue[$temp[1]])
						$op = "selected";
					else
						$op = "";
					$ret .= '<option ' . $op . ' value="' . $fvalue[$temp[1]] . '">';
					foreach ($fvalue as $key => $kvalue) {
						if (in_array($key, explode(",", $temp[4])))
							$ret .= $kvalue . " ";
					}
					$ret .= "</option>";
				}
				$ret .= "</select>";
			}
		}
		return $ret;
	}
	public static function updatewhere($table, $column, $id, $where) {
		global $obj, $user, $required;
		$ret = "";
		if($where=="")$where=" where 1=1 ";
		foreach ($obj->veze as $temp) {
			if ($temp[2] == $table && $temp[3] == $column) {
				if(substr($where, -1)=="=")$where.="0";
				$dodatno="";
				if($temp[0]=="srepublika")$dodatno=" and isnull(vazido)";
				if($temp[0]=="sinokuca")$dodatno=" and aktivan='A'";
				if($temp[0]=="brand")$dodatno.=" order by bdesc asc";
				if($temp[0]=="category")$dodatno.=" order by bdesc asc";
				if($temp[0]=="news_source")$dodatno.=" order by name asc";
				if($user->getRestrictions("limitA",$table,$column))
					$sql = "select * from $temp[0] $where and id=".$user->getRestrictions("limitA",$table,$column).$dodatno;
				else
					$sql = "select * from $temp[0] $where".$dodatno;
				$a = $obj -> ExecuteSQL($sql);
				$required=$obj -> getOption($table, $column, "required") ? $obj -> getOption($table, $column, "required"):"";
				$ret .= '<select '.$required.' name="' . $column . '" id="' . $column . '" class="form-control selectpickera">';
				$ret .= '<option value=""></option>';
				foreach ($a as $fvalue) {
					if ($id == $fvalue[$temp[1]])
						$op = "selected";
					else
						$op = "";
					$ret .= '<option ' . $op . ' value="' . $fvalue[$temp[1]] . '">';
					foreach ($fvalue as $key => $kvalue) {
						if (in_array($key, explode(",", $temp[4])))
							$ret .= $kvalue . " ";
					}
					$ret .= "</option>";
				}
				$ret .= "</select>";
			}
		}
		return $ret;
	}

}
?>
