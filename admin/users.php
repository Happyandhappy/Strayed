<?php
class user {
	public $user_id, $user_name, $usertable;
	public $por = "";
	public $restrictions = array();

	public function __construct() {
		global $hop, $reset;
		if (!isset($hop) && !isset($reset)) {
			$this -> sessionWork();
			$this -> logOut();
			if (!isset($_SESSION['id']))
				$this -> showLoginForm();
		}
	}

	public function sessionWork() {
		global $owner, $max_activity;
		session_name("dva_pet_" . $owner);
		session_start();
		if (isset($_SESSION['zadnja_aktivnost']) && (time() - $_SESSION['zadnja_aktivnost'] > $max_activity * 60)) {
			$this -> por = '<div class="alert alert-danger">' . t('You have not been here longer then ') . $max_activity . t(' minutes.<br>Your session is expired!') . '</div>';
			session_unset();
			session_destroy();
		} else {
			$_SESSION['zadnja_aktivnost'] = time();
		}
	}

	public function showLoginForm() {
		global $usertable;
		if (isset($_POST['username'])) {
			if (empty($_SESSION['captcha_code']) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code']) != 0) {
				$this -> por = '<div class="alert alert-danger">' . t('Incorrect Captcha!') . '</div>';
				include ("loginform.php");
				die();
			} else {
				$trt = new crud;
				$trt -> connect();
				$username = htmlspecialchars($_POST['username']);
				$password = htmlspecialchars($_POST['password']);
				$sql = 'select * from ' . $usertable[0] . ' where ' . $usertable[1] . '="' . $username . '" and ' . $usertable[2] . '="' . $password . '"';
				$result = $trt -> executeSQL($sql);
				if (!empty($result)) {
					$_SESSION['id'] = $result[0]['id'];
					$_SESSION['username'] = $result[0][$usertable[1]];
				} else {
					$this -> por = '<div class="alert alert-danger">' . t('Login credentials do not match any account!') . '</div>';
					include ("loginform.php");
					die();
				}
			}
		} else {
			include ("loginform.php");
			die();
		}
	}

	public function logOut() {
		if (isset($_GET['logout'])) {
			session_unset();
			session_destroy();
		}
	}

	/**
	 * $type moze da bude: viewT, viewC, delete, insert, updateT, updateC, limitD, limitA
	 */
	public function setRestrictions($type = '', $table = '', $column = '', $what = '', $value = '') {
		if ($what == "")
			$what = "id";
		if ($value == "")
			$value = $_SESSION['id'];
		$a = array($type, $table, $column, $what, $value);
		array_push($this -> restrictions, $a);
	}

	public function getRestrictions($type = "", $table = "", $column = "") {
		global $obj, $usertable;
		foreach ($this->restrictions as $value) {
			$sql = 'select * from ' . $usertable[0] . ' where ' . $value[3] . '="' . $value[4] . '"';
			$result = $obj -> executeSQL($sql);

			for ($i = 0; $i < count($result); $i++) {
				if ($value[0] == $type && $value[1] == $table) {
					switch ($value[0]) {
						case 'limitA' :
							if ($value[2] == $column && isset($result[$i][$column])) {
								return $result[$i][$column];
							}
							break;
						case 'limitD' :
							if ($value[2] == $column && isset($result[$i][$column]))
								return $column . "=" . $result[$i][$column];
							break;
						case 'viewT' :
							if ($_SESSION['id'] == @$result[$i]['id'])
								return 1;
							break;
						case 'viewC' :
							if ($_SESSION['id'] == @$result[$i]['id'] && $value[2] == $column)
								return 1;
							break;
						case 'delete' :
							if ($_SESSION['id'] == @$result[$i]['id'])
								return 1;
							break;
						case 'insert' :
							if ($_SESSION['id'] == @$result[$i]['id'])
								return 1;
							break;
						case 'updateT' :
							if ($_SESSION['id'] == @$result[$i]['id'])
								return 1;
							break;
						default :
							break;
					}
				}
			}

		}
	}

}
?>