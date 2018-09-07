<?php
/**
 * Main CRUD class
 *
 * @package dva_pet
 * @author  Dejan Zdravkovic <dejan@zdravkovic.rs>
 * @copyright Copyright (c) 2016, zombie.studio
 */
class crud {
	private $conn;
	public $tabele = array();
	public $kolone = array();
	public $option = array();
	public $veze = array();
	public $dependencies=array();
	public $pogledi = array();
	public $funkcije = array();
	public $lastInsertId;

	public function __construct() {
	}

	/**
	 * This function is used to connect dva_pet to database.
	 * If database is not found at server from config.php file
	 * then dva_pet will try to create database.
	 * Also, before this job, this function will drop database if variable reset is equal to 0
	 */
	public function connect() {
		global $reset, $db_host, $db_user, $db_db, $db_pass;
		if ($reset == 1) {
			$dbh = new PDO("mysql:host=$db_host", $db_user, $db_pass);
			$sql = "DROP DATABASE " . $db_db;
			if ($dbh -> exec($sql))
				echo "Database is deleted<br>";
		}
		try {
			$this -> conn = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_db, $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$this -> conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//$this -> conn -> setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		} catch(PDOException $e) {
			$this -> createDatabase();
		}
	}

	/**
	 * This function will select all rows from input table and will return it as array
	 *
	 * @param string $table Name of a table
	 * @return array Array of rows of table
	 */
	public function showData($table) {
		$data = array();
		$sql = "SELECT * FROM $table order by id desc";
		$q = $this -> conn -> query($sql) or die("failed!");
		while ($r = $q -> fetch(PDO::FETCH_ASSOC)) {
			$data[] = $r;
		}
		return $data;
	}

	/**
	 * Will return only one row where id is equal to input parameter
	 *
	 * @param integer $id ID of row that would like to be returned
	 * @param string $table Table name from which we want result
	 * @return array Array of data for current row of a table
	 */
	public function getById($id, $table) {
		$sql = "SELECT * FROM $table WHERE id = :id";
		$q = $this -> conn -> prepare($sql);
		$q -> execute(array(':id' => $id));
		$data = $q -> fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	/**
	 * Will execute input sql statement and return array of data
	 *
	 * @param string $sql is SQL statement that will be executed
	 * @return array Array of data that will return SQL statement
	 */
	public function executeSQL($sql) {
		$data = array();
		$q = $this -> conn -> query($sql) or die("failed!");
		while ($r = $q -> fetch(PDO::FETCH_ASSOC)) {
			$data[] = $r;
		}
		return $data;
	}

	public function execSQL($sql) {
		$this -> conn -> exec($sql);
	}

	/**
	 * Will update row in table
	 *
	 * @param array $vrednosti is set of new data that should be altered into table
	 * @param string $table is name of a table
	 */
	public function update($vrednosti, $table) {
		foreach ($this->kolone as $value) {
			if ($table == $value[0] && array_key_exists($value[1], $vrednosti) && $value[4] == "date")
				$vrednosti[$value[1]] = $vrednosti[$value[1]] ? date_format(date_create($vrednosti[$value[1]]), "Y-m-d H:i:s") : null;
		}

		$temp1 = "";
		$temp2 = array();
		foreach ($vrednosti as $key => $value) {
			if ($key != "id" and $key != "update" and $key != "files" and $key != "function" and $key != "table") {
				$temp1 = $temp1 . $key . "=:" . $key . ",";
			}
			if (($key != "update") and ($key != "function") and ($key != "files") and ($key != "table")) {
				if ($value == "")
					$temp2[":" . $key] = null;
				else
					$temp2[":" . $key] = $value;
			}
		}
		$sql = 'UPDATE ' . $table . ' SET ' . rtrim($temp1, ",") . ' WHERE id=:id';
		$q = $this -> conn -> prepare($sql);
		$q -> execute($temp2);
		return true;
	}

	/**
	 * Will insert row into table
	 *
	 * @param array $vrednosti is dataset that should be inserted into table
	 * @param string $table is name of a table
	 */
	public function insertData($vrednosti, $table) {
		foreach ($this->kolone as $value) {
			if ($table == $value[0] && array_key_exists($value[1], $vrednosti) && $value[4] == "date")
				$vrednosti[$value[1]] = $vrednosti[$value[1]] ? date_format(date_create($vrednosti[$value[1]]), "Y-m-d H:i:s") : null;
		}

		$temp1 = "";
		$temp2 = array();
		foreach ($vrednosti as $key => $value) {
			if ($key != "id" and $key != "insert" and $key != "function" and $key != "table") {
				$temp1 = $temp1 . $key . "=:" . $key . ",";
				if ($value == "")
					$temp2[":" . $key] = null;
				else
					$temp2[":" . $key] = $value;
			}
		}
		$sql = "INSERT INTO $table SET " . rtrim($temp1, ",");
		$q = $this -> conn -> prepare($sql);
		$q -> execute($temp2);
		$this->lastInsertId=$this->conn->lastInsertId();
		return true;
	}

	/**
	 * This will delete row in table
	 *
	 * @param integer $id is id of row that we wish to delete
	 * @param string $table is name of a table
	 */
	public function deleteData($id, $table) {
		$sql = "DELETE FROM $table WHERE id=:id";
		$q = $this -> conn -> prepare($sql);
		$q -> execute(array(':id' => $id));
		return true;
	}

	/**
	 * This function will append all stuff related to one table
	 * into main public variable $tabele
	 *
	 * @param string $name is a name of table in database
	 * @param string $full_name is name that will be shown as menu item in main menu
	 * @param string $desc is description of a table that will be shown when user open that table
	 * @param string $parentMenu is a name of menu category
	 */
	public function addTable($name, $full_name, $desc, $parentMenu) {
		$a = array($name, $full_name, $desc, $parentMenu);
		array_push($this -> tabele, $a);
	}

	/**
	 * This function will append all stuff related to one column
	 * into main public variable $kolone
	 *
	 * @param string $table is table name to which this column will be assigned
	 * @param string $name is name of column in database
	 * @param string $full_name is name of column that will be shown as row header in table view
	 * @param string $desc is description of a column that will be shown as tooltip for that column
	 * @param string $type is MySQL type of column in database, based on this type all relevant operation will be done
	 */
	public function addColumn($table, $name, $full_name, $desc, $type) {
		$a = array($table, $name, $full_name, $desc, $type);
		array_push($this -> kolone, $a);
	}

	public function addOption($table, $column, $required="", $default="", $seo="", $class="") {
		$a = array($table, $column, $required, $default, $seo, $class);
		array_push($this -> option, $a);
	}

	public function addFunction($table, $function, $when) {
		$a = array($table, $function, $when);
		array_push($this -> funkcije, $a);
	}
	
	public function getFunction($table, $when) {
		switch ($when) {
			case 'after_insert' :
				foreach ($this->funkcije as $value) {
					if ($value[0] == $table && $value[2] == $when) {
						return $value[1];
					}
				}
				break;
			default :
				break;
		}
	}

	public function getTableDesc($table) {
		foreach ($this->tabele as $value) {
			if ($value[0] == $table)
				return $value[2];
		}
	}

	public function getOption($table, $column, $type) {
		switch ($type) {
			case 'required' :
				foreach ($this->option as $value) {
					if ($value[0] == $table && $value[1] == $column) {
						if ($value[2] == 1)
							return "required";
					}
				}
				break;
			case 'default' :
				foreach ($this->option as $value) {
					if ($value[0] == $table && $value[1] == $column)
						return $value[3];
				}
				break;
			case 'seo' :
				foreach ($this->option as $value) {
					if ($value[0] == $table && $value[1] == $column) {
						if ($value[4] <> "")
							return '<script src="js/seo.js"></script><script>document.getElementById("'.$value[4].'").onchange = function() {document.getElementById("'.$column.'").value = seo(document.getElementById("'.$value[4].'").value);};</script>';
					}
				}
				break;
			case 'class' :
				foreach ($this->option as $value) {
					if ($value[0] == $table && $value[1] == $column)
						return "col-xs-6 ".$value[5];
				}
				break;
			default :
				break;
		}
	}

	/**
	 * This function will store all relationship data of two tables into global variable $veze
	 * that would be in use for creating relationships into process of creation of database
	 * also, this variable will be used for CodeBooks inside of dva_pet
	 *
	 * @param string $parent_table is name of parent table in database
	 * @param string $parent_id is name of field (column) of parent table that will be used in relationship
	 * @param string $child_table is name of child table in database
	 * @param string $child_id is name of field (column) of child table that will be used in relationship
	 * @param string $columns_to_show is string of columns separated by comas that will be shown into select form element
	 * @param string $ondelete is event action when creating relationship for ON DELETE event
	 */
	public function addRelation($parent_table, $parent_id, $child_table, $child_id, $columns_to_show, $ondelete = "NO ACTION") {
		$a = array($parent_table, $parent_id, $child_table, $child_id, $columns_to_show, $ondelete);
		array_push($this -> veze, $a);
	}

	public function addDependence($table, $parent, $child) {
		$a = array($table, $parent, $child);
		array_push($this -> dependencies, $a);
	}

	public function addView($name, $full_name, $desc, $show_in_menu, $first_table, $second_table, $first_table_columns, $second_table_columns, $join_type = "INNER") {
		$a = array($name, $full_name, $desc, $show_in_menu, $first_table, $second_table, $first_table_columns, $second_table_columns, $join_type);
		array_push($this -> pogledi, $a);
	}

	/**
	 * This function will create database based on config.php and model.php
	 */
	public function createDatabase() {
		global $db_host, $db_user, $db_db, $db_pass;
		$dbh = new PDO("mysql:host=$db_host", $db_user, $db_pass);
		$sql = "CREATE DATABASE " . $db_db . " COLLATE = utf8_general_ci";
		if ($dbh -> exec($sql))
			echo "Database is created<br>";
		$kon = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_db, $db_user, $db_pass);
		foreach ($this->tabele as $value) {
			$sqll = $this -> createTable($value[0]);
			$kon -> exec($sqll);
		}
		$this -> createRelations();
		$this -> createViews();
	}

	/**
	 * This function is called by create_database function, which will create query
	 *
	 * @param string $table is name of table
	 * @param array $fields is array of columns that should be addeded to table
	 * @return string
	 */
	public function createTable($table) {
		$sql = "CREATE TABLE IF NOT EXISTS `$table` (";
		$pk = '';
		foreach ($this->kolone as $kolona) {
			if ($table == $kolona[0]) {
				switch ($kolona[4]) {
					case 'pk' :
						$ja = "INT AUTO_INCREMENT";
						$pk = $kolona[1];
						break;
					case "check" :
						$ja = "varchar(3)";
						break;
					case "money" :
						$ja = "double(30,15)";
						break;
					case "integer" :
						$ja = "int";
						break;
					case "combo" :
						$ja = "int";
						break;
					case "date" :
						$ja = "datetime";
						break;
					case "image" :
						$ja = "mediumblob";
						break;
					case "file" :
						$ja = "mediumblob";
						break;
					default :
						$ja = "text";
						break;
				}
				$sql .= "`$kolona[1]` $ja,";
			}
		}
		$sql .= ' PRIMARY KEY (`' . $pk . '`))';
		$sql .= " ENGINE=InnoDB  DEFAULT CHARSET=utf8 CHARACTER SET utf8 COLLATE utf8_general_ci; ";
		return $sql;
	}

	/**
	 * This function is called by create_database function, which will create relationship between tables in database
	 */
	public function createRelations() {
		global $db_host, $db_user, $db_db, $db_pass;
		$kon = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_db, $db_user, $db_pass);
		foreach ($this->veze as $value) {
			$sql = 'ALTER TABLE `' . $value[2] . '` ADD INDEX `fk_' . $value[2] . '_' . $value[0] . '_idx` (`' . $value[3] . '` ASC);';
			$sql .= 'ALTER TABLE `' . $value[2] . '` ADD CONSTRAINT `fk_' . $value[2] . '_' . $value[0] . '` FOREIGN KEY (`' . $value[3] . '`) REFERENCES `' . $value[0] . '` (`' . $value[1] . '`) ON DELETE ' . $value[5] . ' ON UPDATE NO ACTION;';
			$kon -> exec($sql);
		}
	}

	/**
	 * This function is called by create_database function, which will create views in database
	 */
	public function createViews() {
		global $db_host, $db_user, $db_db, $db_pass;
		$kon = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_db, $db_user, $db_pass);
		foreach ($this->pogledi as $value) {
			$sql = 'create view ' . $db_db . '.' . $value[0] . ' as select ';
			foreach (explode(",",$value[6]) as $kvalue) {
				$sql .= $db_db . '.' . $value[4] . '.' . $kvalue . ',';
			}
			foreach (explode(",",$value[7]) as $kvalue) {
				$sql .= $db_db . '.' . $value[5] . '.' . $kvalue . ',';
			}
			$sql = rtrim($sql, ",");
			foreach ($this->veze as $lvalue) {
				if ($lvalue[0] == $value[4] && $lvalue[2] == $value[5]) {$prvi = $lvalue[1];
					$drugi = $lvalue[3];
				}
			}
			$sql .= ' from ' . $db_db . '.' . $value[4] . ' ' . $value[8] . ' JOIN ' . $db_db . '.' . $value[5] . ' on ' . $db_db . '.' . $value[4] . '.' . $prvi . '=' . $db_db . '.' . $value[5] . '.' . $drugi;
			$kon -> exec($sql);
		}
	}

}
?>
