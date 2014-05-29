<?php
session_start();
include "include/config.php";
include "include/utility.php";



// 取得sUid
$sUid=$_SESSION["sUid"];
// $ALC_project_file="Metaphor_Employee_Data.ACL";
$ALC_project_file="ACL_Demo.ACL";

$ALC_project_src="ACL DATA/$ALC_project_file";

/**
* 專案發生錯誤   把資料回朔
*/
class project_handler {
	function __construct($db_conn, $sUid , $project_file) {
		$this->db_conn = $db_conn;
		$this->sUid = $sUid;
		$this->project_file = $project_file;
	}
	// 刪除專案內容
	public function destroy_project() {
		// 取得 root id
		$this->get_root_id();
		// 刪除資料夾內資料表
		$this->del_folder_table();
		// 刪除資料夾
		$this->del_folder();
		// 刪除根目錄
		$this->destroy_root_folder();
		
	}
	// 刪除 根目錄資料夾
	public function destroy_root_folder() {
		$del_sql = "DELETE FROM directory_structure where sUid='$this->sUid' and root_id='0' and ACL_file='$this->project_file'";
		$stmt = $this->db_conn->exec($del_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('刪除專案目錄資料夾錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}
	public function del_folder() {
		$del_sql = "DELETE FROM directory_structure where sUid='$this->sUid' and root_id='$this->root_id' and type='folder'";
		$stmt = $this->db_conn->exec($del_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('刪除目錄資料夾錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}

	// 刪除資料夾內資料表
	public function del_folder_table() {
		// 取得資料表 然後drop 掉
		try {
			$sql = "SELECT * from directory_structure where sUid='$this->sUid' and root_id='$this->root_id' and type='table'";
			$stmt = $this->db_conn->prepare($sql);
			$exe = $stmt->execute();
			if ($exe===false) {
				throw new PDOException('取得要刪除的資料表錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		while($row = $stmt->fetch()) {

			$d_id = $row["d_id"];
			$content_table = $row["content_table"];
			// 刪除資料表
			$del_sql = "DROP TABLE IF EXISTS $content_table";
			$drop_table_stmt = $this->db_conn->exec($del_sql);
			try {
				if ($drop_table_stmt===false) {
					throw new PDOException('刪除資料表錯誤');
				}
			} catch (PDOException $e) {
				$error = $this->db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				die();
			}
			// 確認該資料表已經被刪除了
			try {
				$sql = "SELECT COUNT(TABLE_NAME) FROM information_schema.tables WHERE table_schema = '$content_table'";
				$drop_table_chk_stmt = $this->db_conn->prepare($sql);
				$exe = $drop_table_chk_stmt->execute();
				if ($exe===false) {
					throw new PDOException('取得專案目錄資料夾出現錯誤');
				}
			} catch (PDOException $e) {
				$error = $this->db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				die();
			}
			$drop_chk  = $drop_table_chk_stmt->fetchColumn();
			// 已刪除該資料表 可以刪除檔案紀錄
			if ($drop_chk==="0") {
				$del_sql = "DELETE FROM directory_structure where d_id='$d_id'";
				$delete_table_stmt = $this->db_conn->exec($del_sql);
				try {
					if ($delete_table_stmt===false) {
						throw new PDOException('刪除專案目錄資料表錯誤');
					}
				} catch (PDOException $e) {
					$error = $this->db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					die();
				}
			}
		}
	}

	// 取得 root id
	public function get_root_id() {
		try {
			$sql = "SELECT * from directory_structure where sUid='$this->sUid' and parent_id='0' and type='root_folder' and ACL_file='$this->project_file'";
			$stmt = $this->db_conn->prepare($sql);
			$exe = $stmt->execute();
			if ($exe===false) {
				throw new PDOException('取得root id 出現錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		$this->root_id  = $stmt->fetchColumn();
	}
}



// 先讀取使用者是否已經有此專案
try {
	$sql = "SELECT * from directory_structure where sUid='$sUid' and parent_id='0' and type='root_folder' and ACL_file='$ALC_project_file'";
	$stmt = $db_conn->prepare($sql);
	$exe=$stmt->execute();
	if ($exe===false) {
		throw new PDOException('取得專案目錄資料夾出現錯誤');
	}
	$root_folder_rs = $stmt->fetch();
	$root_folder_id = $root_folder_rs["d_id"];
	
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}

// 清除整個專案
// $project_handler = new project_handler($db_conn , $sUid , $ALC_project_file);
// $project_handler->destroy_project();
// die();

// 使用者還沒有該專案 建立該專案
if (empty($root_folder_id)) {

	$project_handler = new project_handler($db_conn , $sUid , $ALC_project_file);
	// $project_handler->destroy_project();
	
	// 讀取專案檔
	$ALC_project_file_fp=fopen($ALC_project_src , "r");
	$all_size=filesize($ALC_project_src);
	$all=fread($ALC_project_file_fp, $all_size);
	$all=mb_convert_encoding($all, 'UTF-8', 'UTF-16LE');


	// 把專案檔輸入進資料庫
	$insert_arr = array(
		"sUid"          => $sUid,
		"name"      => $ALC_project_file,				
		"root_id"      => 0,				
		"parent_id"      => 0,				
		"type"      => "root_folder",		
		"ACL_file"      => $ALC_project_file
	);		

	try {
		$insert_sql  = "INSERT INTO directory_structure";
		$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
		$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
		$stmt = $db_conn->exec($insert_sql);
		$root_folder_id = $db_conn->lastInsertId();
		if ($stmt===false) {
			throw new PDOException('新增專案目錄資料夾錯誤');
		}
	} catch (PDOException $e) {
		$error = $db_conn->errorInfo();
		echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
		echo "錯誤行數: " . $e->getline()."<br>";
		// echo "錯誤內容: " . $error[2];
		$project_handler->destroy_project();
		die();
	}

	$data_arr=explode("\n", $all);

	if (!empty($data_arr)) {
		foreach ($data_arr as $line => $each_line) {
			

			// 取得資料表結構
			$table_chk = strpos($each_line,"^LAYOUT");				
			if ($table_chk!==false) {			
				// echo $line. "   ". $each_line."<br>";
				$table_arr=explode(" ", $each_line);
				$table_arr = array_diff($table_arr, array(null,'null','',' '));
				$table_name =  next($table_arr);
				
				// 在資料庫裡面的 資料表名稱
				$table_db_name = "table".uniqid();
				$table_description[$table_name]["table_db_name"] = $table_db_name;

				$get_table_detail_chk=true;
				$table_length =  next($table_arr);

				// 取得 資料表 長度 內容格式
				$table_description[$table_name]["line_length"] = $table_length;
				$table_length_count=0;
				$table_col_arr=array();
				continue;
			}

			// 取得資料表內容 並輸入進資料庫
			if ($get_table_detail_chk) {
				$table_detail_arr=explode(" ", $each_line);
				$table_detail_arr = array_diff($table_detail_arr, array(null,'null','',' '));
				$col_name=current($table_detail_arr);
				$col_type=next($table_detail_arr);
				$col_index = next($table_detail_arr);
				$col_length = next($table_detail_arr);
				$detail_index = $col_index+$col_length-1;
				$table_description[$table_name]["col_name"][]=$col_name;
				$table_description[$table_name]["col_start"][]=$col_index;
				$table_description[$table_name]["col_length"][]=$col_length;
				$table_description[$table_name]["col_type"][]=$col_type;
				$decimal_dot="";
				switch ($col_type) {
					case 'NUMERIC':
						$sql_type="INT";
						$decimal_dot = next($table_detail_arr);
					break;

					case 'DATETIME':
						$sql_type="varchar(10)";
					break;

					case 'ACL':
						$sql_type="double";
						$decimal_dot = next($table_detail_arr);
					break;

					case 'ASCII':
						$sql_type="varchar($col_length)";
					break;

					// VARCHAR
					default:
						$sql_type="varchar($col_length)";
					break;
				}		
				
				// 小數點後幾位
				$table_description[$table_name]["decimal_dot"][]=$decimal_dot;
				// 存入資料庫的資料型態
				$table_col_arr[]="`$col_name` $sql_type".' NULL';
				// 取得最後一個col 
				if ($detail_index==$table_length ) {

					// 輸入資料表 進入目錄
					$insert_arr = array(
						"sUid"          => $sUid,
						"root_id"      => $root_folder_id,
						"parent_id"      => $root_folder_id,
						"name"       => $table_name,
						"content_table" => $table_db_name,
						"type"       => "table",
						"ACL_file"       => "$table_file_name"
					);			
					$insert_sql  = "INSERT INTO directory_structure";
					$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
					$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
 					$stmt = $db_conn->exec($insert_sql);
 					// 儲存資料表的d_id
					$table_description[$table_name]["d_id"] = $db_conn->lastInsertId();
 					
					try {
						if ($stmt===false) {
							throw new PDOException('新增目錄資料表錯誤');
						}
					} catch (PDOException $e) {
						$error = $db_conn->errorInfo();
						echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
						echo "錯誤行數: " . $e->getline()."<br>";
						// echo "錯誤內容: " . $error[2];
						$project_handler->destroy_project();
						die();
					}

					

					$get_table_detail_chk=false;				
					if (!empty($table_col_arr)) {
						$table_col_sql=implode(",", $table_col_arr);					
					}
					// create table sql
					$create_table_sql="CREATE TABLE $table_db_name (";
					$create_table_sql.=$table_col_sql;				
					$create_table_sql.=",key_id  INT  AUTO_INCREMENT  NOT NULL ,INDEX (key_id)";
					$create_table_sql.=")";
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($create_table_sql);
					} catch (PDOException $e) {						
						echo "新增資料表時出現錯誤   錯誤如下 <br>" . $e->getMessage();
						echo "錯誤行數: " . $e->getline()."<br>";
						$project_handler->destroy_project();
						die();
					}
				}

			}
			






			// 取得資料夾結構
			$folder_structure_chk = strpos($each_line,"^FOLDER");
			if ($folder_structure_chk !==false) {
 				$folder_name_arr=explode(" ", $each_line);
				// 清除空白
				$folder_name_arr = array_diff($folder_name_arr, array(null,'null','',' '));
				$folder_name_arr = array_values($folder_name_arr);
				$folder_name =  next($folder_name_arr);		
				$folder_id =  next($folder_name_arr);		
				// 取得 parent id 資料行
				$parent_id_line = $data_arr[$line+1];
				$parent_id_arr=explode(" ", $parent_id_line);
				// 清除空白
				$parent_id_arr = array_diff($parent_id_arr, array(null,'null','',' '));
				$parent_id_arr = array_values($parent_id_arr);
				$folder_parent_id =  next($parent_id_arr);
				
				// 建立資料夾結構
				$folder_db_folder_id = $ACL_folder_data["db_folder_id"];
				
				// parent_id = 0 代表parent folder 為根目錄
				if ($folder_parent_id==="0") {
					$folder_parent_id=$root_folder_id;
				}else{
					$folder_parent_id = $folder_db_mapping_arr[$folder_parent_id];
				}

				
				
				// 把資料夾結構輸入進入資料庫
				$insert_arr = array(
					"sUid"          => $sUid,
					"root_id"      => $root_folder_id,
					"parent_id"      => $folder_parent_id,
					"name"       => $folder_name,
					"type"       => "folder"
				);			
				$insert_sql  = "INSERT INTO directory_structure";
				$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
				$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
				
				$stmt = $db_conn->exec($insert_sql);
				// 資料夾對應的資料庫的id
				$folder_db_mapping_arr[$folder_id] = $db_conn->lastInsertId();

				try {
					if ($stmt===false) {
						throw new PDOException('新增專案目錄資料夾錯誤');
					}
				} catch (PDOException $e) {
					$error = $db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					$project_handler->destroy_project();
					// echo "錯誤內容: " . $error[2];
				}
			}
					
			// 取得需要輸入資料表的內容
			$table_file_chk = strpos($each_line,"^FORMAT");				
			if ($table_file_chk!==false) {
				// 取得資料表的資料
				$table_file_arr=explode(" ", $each_line);
				$table_file_arr = array_diff($table_file_arr, array(null,'null','',' '));
				$table_file_arr=array_values($table_file_arr);
				$insert_table = $table_file_arr[1];
				$table_file_name= str_replace('"', "", $table_file_arr[4]);
				// 資料庫內表的名稱
				$table_db_name=$table_description[$insert_table]["table_db_name"];						

				$each_ling_length=$table_description[$insert_table]["line_length"]+1;
				$col_name_arr =  $table_description[$insert_table]["col_name"];
				$col_width_arr =  $table_description[$insert_table]["col_length"];
				$col_start_arr =  $table_description[$insert_table]["col_start"];
				$col_type_arr =  $table_description[$insert_table]["col_type"];
				$col_decimal_dot_arr =  $table_description[$insert_table]["decimal_dot"];
				$table_d_id =  $table_description[$insert_table]["d_id"];
				$arr_count = count($col_start_arr)-1;


				// 取得該資料表的資料夾位置
				// 取得 parent id 資料行
				$parent_id_line = $data_arr[$line+1];
				$parent_id_arr=explode(" ", $parent_id_line);
				// 清除空白
				$parent_id_arr = array_diff($parent_id_arr, array(null,'null','',' '));
				$parent_id_arr = array_values($parent_id_arr);
				$table_parent_id =  next($parent_id_arr);
				// 取得該parent_id  在db的id
				$table_db_parent_id=$folder_db_mapping_arr[$table_parent_id];

				$update_sql = "UPDATE directory_structure set parent_id='$table_db_parent_id' where d_id='$table_d_id'";
				$stmt = $db_conn->exec($update_sql);
				try {
					if ($stmt===false) {
						throw new PDOException('更新資料表位置錯誤');
					}
				} catch (PDOException $e) {
					$error = $db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					$project_handler->destroy_project();
					die();
				}
		
				// 讀取資料表內容 輸入進資料庫
				$insert_table_file_src="ACL DATA/table_file/$table_file_name";
				try {
					if (!file_exists($insert_table_file_src)) {
						throw new PDOException("$table_file_name:該資料檔不存在");
					}	
				} catch (Exception $e) {
					echo $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					$project_handler->destroy_project();
					die();
				}
				
				$insert_table_file = fopen($insert_table_file_src, "rb");
				$insert_table_file_size = filesize($insert_table_file_src);

				while(!feof($insert_table_file)){
					// get fil data by it's start point and length  define by above
	 				$file_line=fgets($insert_table_file , $each_ling_length);
	 				$file_line_len = strlen($file_line)+1;
	 				
	 				// at the end of file don't inert 0 data 				
	 				if ($file_line_len<$each_ling_length) { 					
	 					continue;
	 				}
	 				// echo $file_line."<br>";
	 				if (!empty($col_start_arr)) {
	 					$insert_table_arr=array();
	 					foreach ($col_start_arr as $col => $col_start) { 						
	 						$data_type = $col_type_arr[$col];
	 						$decimal_dot = $col_decimal_dot_arr[$col];
	 						$start=$col_start-1;

	 						// get data
	 						$line_data =  substr($file_line, $start , $col_width_arr[$col]); 						 						
	 						// ACL data type has to insert decimal dot
	 						if ($data_type=="ACL") { 							 							
	 							$line_data = bin2hex ($line_data);
	 							$line_data = substr($line_data, -22);
	 							$line_data = (float)$line_data; 
	 							// insert decimal dot
	 							$line_data =insert_dot($line_data ,$decimal_dot); 							
	 						}
	 						$line_data = trim($line_data);
	 						$line_data = mysql_real_escape_string($line_data);

	 						$insert_table_arr[]=trim($line_data);
	 					}
	 					// 輸入資料進table
	 					$insert_sql  = "INSERT INTO $table_db_name";
	 					$insert_sql .= " (`".implode("`, `", ($col_name_arr))."`)";
	 					$insert_sql .= " VALUES ('".implode("', '", $insert_table_arr)."') ";
	 					try {
							$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
							$stmt = $db_conn->exec($insert_sql);
						} catch (PDOException $e) {						
							echo "輸入資料時出現錯誤   錯誤如下 <br>" . $e->getMessage();
							echo "錯誤行數: " . $e->getline()."<br>";
							$project_handler->destroy_project();
							die();
						}		
	 				}
				}
			}


			// 取得script 並新增至 資料庫
			$script_chk = strpos($each_line,"^BATCH");
			if ($script_chk!==false) {
				$script_name_arr=explode(" ", $each_line);
				// 清除空白
				$script_name_arr = array_diff($script_name_arr, array(null,'null','',' '));
				$script_name_arr = array_values($script_name_arr);
				$script_name =  next($script_name_arr);
				// 取得 parent id 資料行
				$parent_id_line = $data_arr[$line+1];
				$parent_id_arr=explode(" ", $parent_id_line);
				// 清除空白
				$parent_id_arr = array_diff($parent_id_arr, array(null,'null','',' '));
				$parent_id_arr = array_values($parent_id_arr);
				$folder_parent_id =  next($parent_id_arr);
				$folder_parent_id = $folder_db_mapping_arr[$folder_parent_id];

				// 把script 輸入進入資料庫
				$insert_arr = array(
					"sUid"          => $sUid,
					"root_id"      => $root_folder_id,
					"parent_id"      => $folder_parent_id,
					"name"       => $script_name,
					"type"       => "script"
				);			
				$insert_sql  = "INSERT INTO directory_structure";
				$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
				$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
				
				$stmt = $db_conn->exec($insert_sql);
				try {
					if ($stmt===false) {
						throw new PDOException('新增專案目錄資料夾錯誤');
					}
				} catch (PDOException $e) {
					$error = $db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					$project_handler->destroy_project();
				}
			}


		}//foreach ($data_arr as $line => $each_line) {
	}//if (!empty($data_arr)) {

}


// 已建立此專案
if (!empty($root_folder_id)) {
	// 取得第一層的資料
	try {
		$sql = "SELECT * from directory_structure where sUid='$sUid' and parent_id='$root_folder_id'";
		$stmt = $db_conn->prepare($sql);
		$exe = $stmt->execute();
		if ($exe===false) {
			throw new PDOException('取得專案目錄第一層資料夾出現錯誤');
		}
	} catch (PDOException $e) {
		$error = $db_conn->errorInfo();
		echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
		echo "錯誤行數: " . $e->getline()."<br>";
		// echo "錯誤內容: " . $error[2];
		$project_handler->destroy_project();
		die();
	}
	while($row = $stmt->fetch()) {
		$d_id = $row["d_id"];
		$name = $row["name"];
		$content_table = $row["content_table"];
		$type = $row["type"];
		$directory_arr[$d_id]["name"]=$name;
		$directory_arr[$d_id]["content_table"]=$content_table;
		$directory_arr[$d_id]["type"]=$type;
		// 資料夾需確認資料夾內有無資料
		if ($type==="folder") {
			try {
				$sql = "SELECT COUNT(*) as folder_chk from directory_structure where sUid='$sUid' and parent_id='$d_id'";
				$folder_chk_stmt = $db_conn->prepare($sql);
				$exe = $folder_chk_stmt->execute();
				if ($exe===false) {
					throw new PDOException('取得專案目錄資料夾出現錯誤');
				}
			} catch (PDOException $e) {
				$error = $db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				$project_handler->destroy_project();
				die();
			}
			$folder_chk  = $folder_chk_stmt->fetchColumn();
			$directory_arr[$d_id]["folder_chk"]=$folder_chk;
		}
	}
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
A:link{color:#00519C; font-size:10pt; text-decoration:none}
A:visited{color:#00519C; font-size:10pt; text-decoration:none}
A:hover{color:#cc0000; font-size:10pt; text-decoration:none}
A:active{color:red; font-size:10pt; text-decoration:none}
td {font-size:12pt; font-family:Arial, Helvetica, sans-serif}
</style>
<head>
	<title></title>
	<!-- fancytree css  skin -->
	<link href="lib/fancytree/src/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
	<!-- custom.css -->
	<!-- <link href="lib/fancytree/demo/skin-custom/custom.css" rel="stylesheet" type="text/css" > -->
	<link href="css/directory.css" rel="stylesheet" type="text/css" >
	<script src="js/jquery-1.11.0.min.js"></script>
  	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" type="text/javascript"></script>
	<!-- facyytree  -->
	<script src="lib/fancytree/src/jquery.fancytree.js" type="text/javascript"></script>



	
	<style type="text/css">
	span.fancytree-node.table_img > span.fancytree-title {
	  /*color: maroon;*/
	  /*font-family: "Audiowide";*/
	}
	/*資料表圖片*/
	span.fancytree-node.table_img > span.fancytree-icon {
	  /*background-image: url("fancytree/demo/skin-custom/customDoc2.gif");*/
	  background-image: url("img/acl_treeview/table_img.png");
	  background-position: 0 0;
	}
	/*被選中的資料表圖片*/
	span.fancytree-node.table_img_active > span.fancytree-icon {	  
	  background-image: url("img/acl_treeview/table_img_active.png");
	  background-position: 0 0;
	}
	span.fancytree-node.script_img > span.fancytree-icon {
	  /*background-image: url("fancytree/demo/skin-custom/customDoc2.gif");*/
	  background-image: url("img/acl_treeview/script.png");
	  background-position: 0 0;
	}
	
	</style>
	



	<script type="text/javascript">
	// 記錄動態的 table 節點ID
	var dynamic_table_node_id;

	$(document).ready(function() {

		
		// 記錄動態的 script 節點 ID
		var dynamic_script_node_id;

	  	$("#tree").fancytree({
			// 設定圖片路徑
	  		imagePath: "img/acl_treeview/",
	  		clickFolderMode: 3, // 1:activate, 2:expand, 3:activate and expand, 4:activate (dblclick expands)
	  		
	  		
	  		// 離開一個節點的時候更換 icon 
			blur: function(event, data) {
				var node = data.node;
				var nodeid = node.data.nodeid;
			},

	  		click: function(event, data) {
		    	var node = data.node;
		    	var tablename = node.data.tablename;
		    	var type = node.data.type;
		    	var test = node.data.test;
		    	
		    	// alert(node.data.dbtable);
		    	// alert($(this).html());

		    	
				if (type==="table") {
					// 換icon
					// $(this).find("."+tablename).removeClass('table_img').addClass('table_img_active');
					// alert(node.data.icon);
					// node.data.icon="table_img_active.png";
					// alert(node.data.icon);

					// var $span = $(node.span);
				  //       $span.find("> span.fancytree-icon").css({
				  //           backgroundImage: "url(img/acl_treeview/table_img_active.png)",
				  //           backgroundPosition: "0 0"
				  //       });
				};
				// 打開folder
			    if (type==="folder") {
					var openstatus = node.data.openstatus;
					var sub_chk = node.data.subchk;

					// folder 還沒被開過且該資料夾內有資料
					if (openstatus==="unopen" & sub_chk==="has_sub") {
						var d_id = node.data.did;
						// alert(d_id);

						// 切換為已開啟
						node.data.openstatus="";
						// 取得資料夾內的內容
						$.ajax({ url: 'directory_ajax.php' ,
							cache: false,
							dataType: 'html',// <== 設定傳送格式
							type:'GET',// <== 設定傳值方式
							data: { action: "get_subdirectory" , d_id:d_id },// <== 傳GET的變數，此例是gsn
							error: function(xhr) { alert('取得資料夾內容 Ajax request 發生錯誤'+ xhr); },
							success: function(response) {
								// alert(response);
								var subdirectory_arr=$.parseJSON(response);

								// 取得每一個物件
								$.each(subdirectory_arr, function(index, obj) {
									var  obj_name = obj.name;
									var  obj_type = obj.type;
									var  obj_d_id = obj.d_id;
									var  obj_dbtable = obj.dbtable;
									// 確認資料夾是否有值
									var  obj_folder_inside_count = obj.folder_inside_count;

									switch (obj_type) {  
										// 資料夾
									    case "folder":  
									        // 替當下的節點下增加檔案
											var childNode = node.addChildren({
									        	title: obj_name,
									        	// tooltip: "This folder and all child nodes were added programmatically.",
									        	folder: true,
									        	openstatus:"unopen",
									        	type:obj_type,
									        	did: obj_d_id,
										    });
										    
										    // 該子資料夾內有還有資料
										    if (obj_folder_inside_count>0) {
										    	childNode.data.subchk="has_sub";
										    	// alert("123");
										    	childNode.addChildren({
										        	title: "useless",
										        	key:"useless_"+obj_d_id,
										    	});		
										    };
								        break;  
								
										// table
									    case "table":  
									        // 替當下的節點下增加檔案
											var childNode = node.addChildren({
									        	title: obj_name,
									        	// tooltip: "This folder and all child nodes were added programmatically.",
									        	// icon: "table_img.png",
									        	type:obj_type,
									        	nodeid:"nodeid"+obj_d_id,
									        	key:"nodeid"+obj_d_id,
									        	extraClasses:'table_img',
									        	dbtable : obj_dbtable,
									        	tablename : obj_name,
										    });
								        break;

								        // table
									    case "script":  
									        // 替當下的節點下增加檔案
											var childNode = node.addChildren({
									        	title: obj_name,
									        	type:obj_type,
									        	nodeid:"nodeid"+obj_d_id,
									        	key:"nodeid"+obj_d_id,
									        	// tooltip: "This folder and all child nodes were added programmatically.",
									        	// icon: "table_img.png",
									        	extraClasses:'script_img',
										    });
								        break;  
									    
									    default:  
									        
								        break;  
									};  


								});
								// 使用d_id 取得節點 並刪掉不需要的節點
								$("#tree").fancytree("getTree").getNodeByKey("useless_"+d_id).remove();
							}
						});
					};
			    };
		    },
		    // 連點
			dblclick: function(event, data) {
				// 當下這個節點
				var node = data.node;
				// 取得table的名稱
				var tablename = node.data.tablename;
				// 該table的資料表
				var dbtable = node.data.dbtable;
				var type = node.data.type;
				// 動態的表
				var nodeid = node.data.nodeid;

				if (type==="table") {
					// 開啟動態table
					open_dynamic_table( nodeid , tablename , dbtable);
				};

				if (type==="script") {
					// 把現在的script icon 換成開啟
					var $span = $(node.span);
			        $span.find("> span.fancytree-icon").css({
			            backgroundImage: "url(img/acl_treeview/script_active.png)",
			            backgroundPosition: "0 0"
			        });

			        // dynamic script 有被開啟了
			        if (dynamic_script_node_id!==nodeid) {
						// 先確認dynamic_script_node_id不是 undefined
						if (dynamic_script_node_id !== undefined) {
							// 取得dynamic_script_node_id 的 id
							onblur_node = $("#tree").fancytree("getTree").getNodeByKey(dynamic_script_node_id);
							onblur_node_type =onblur_node.data.type;
							// alert(onblur_node.data.type);		
							// 相同type
							if (onblur_node_type===type) {
								// 把之前的 dynamic  table 改為為開啟
								var $span = $(onblur_node.span);
						        $span.find("> span.fancytree-icon").css({
						            backgroundImage: "url(img/acl_treeview/script.png)",
						            backgroundPosition: "0 0"
						        });
							};
						};
						dynamic_script_node_id=nodeid;
			        };
				};

			},
	  	});
	  	
	});
	
	// 新增table的節點
	function add_table_node (nodeid , parent_node_id , table_name , table_db_name ) {
		// 取得dynamic_table_node_id 的 id
		parent_node_node = $("#tree").fancytree("getTree").getNodeByKey(parent_node_id);
		// 替當下的節點下增加檔案
		var childNode = parent_node_node.addChildren({
        	title: table_name,
        	type:"table",
        	nodeid: nodeid,
        	key: nodeid,
        	extraClasses:'table_img',
        	dbtable : table_db_name,
        	tablename : table_name
	    });
	}

	// 打開table節點 
	function open_dynamic_table (node_id  , table_name , table_db_name) {
		// 取得要開啟的節點
		var open_node = $("#tree").fancytree("getTree").getNodeByKey(node_id);
     	// $("#tree").fancytree("getTree").getNodeByKey(node_id).setActive();
     	// 把節點設為 focus
     	open_node.setActive();

     	// 把圖片設為已開啟的table
     	var $span = $(open_node.span);
        $span.find("> span.fancytree-icon").css({
            backgroundImage: "url(img/acl_treeview/table_img_active.png)",
            backgroundPosition: "0 0"
        });

	     // dynamic table有被開啟了
        if (dynamic_table_node_id!==node_id) {
			// 若dynamic table 有被開啟了 需要把原先的table 圖片改為關閉
			if (dynamic_table_node_id !== undefined) {
				// 取得dynamic_table_node_id 的 id
				onblur_node = $("#tree").fancytree("getTree").getNodeByKey(dynamic_table_node_id);
				onblur_node_type =onblur_node.data.type;
				// alert(onblur_node.data.type);		
				// 相同type
				if (onblur_node_type==="table") {
					// 把之前的 dynamic  table 改為為關閉
					var $span = $(onblur_node.span);
			        $span.find("> span.fancytree-icon").css({
			            backgroundImage: "url(img/acl_treeview/table_img.png)",
			            backgroundPosition: "0 0"
			        });
				};
			};
			dynamic_table_node_id=node_id;
        };
        // 開啟動態table
		$('#file_content', window.parent.document).get(0).contentWindow.set_dynamic_table(table_name , table_db_name);
	}





		
	</script>
	
</head>
<body>
	<div id="tree">
	    <ul>
	      	<li class="folder expanded"><?echo $ALC_project_file;?>
		        <ul>		          
		        <?php
		        	if (!empty($directory_arr)) {
		        		foreach ($directory_arr as $d_id => $directory_data) {
		        			$name = $directory_data["name"];
		        			$table_db_name = $directory_data["content_table"];
		        			$folder_chk = $directory_data["folder_chk"];

		        			if ($directory_data["type"]=="folder" ) {
		        				$folder_data="";
		        				$sub_chk="";
		        				if ($folder_chk>0) {
		        					// 多useless 為了讓資料夾看起來可以展開
		        					$folder_data='<ul><li id="useless_'.$d_id.'">useless</li></ul>';
		        					$sub_chk="has_sub";
		        				}
		        				// 設定節點的兩種格式   放入data-type 或是  使用json格式放入data-json
		        				// echo '<li class="folder" data-type="folder" data-openstatus="unopen" data-did="'.$d_id.'" data-subchk="'.$sub_chk.'" >'.$name.$folder_data.'</li>';
		        				/*<li class="folder"  data-json='{"type": "folder" ,"openstatus": "unopen", "did":"<?echo $d_id;?>", "subchk": "<?echo $sub_chk;?>" }'  ><?echo $name.$folder_data;?></li>*/
		        				// 放入設定
		        				$folder_setting_json->type="folder";
		        				$folder_setting_json->openstatus="unopen";
		        				$folder_setting_json->did=$d_id;
		        				$folder_setting_json->subchk="$sub_chk";
		        				?>
		        				<li class="folder"  data-json='<?echo json_encode($folder_setting_json);?>' id="node_<?php echo $d_id;?>"  ><?echo $name.$folder_data;?></li>
								<?

		        			}elseif($directory_data["type"]=="table" ){
								// echo '<li class="table_img '.$name.'"  data-dbtable="'.$table_db_name.'"  data-tablename="'.$name.'" data-type="table" data-nodeid="node_'.$d_id.'"  id="node_'.$d_id.'" ><span class="folder" >'.$name.'</span></li>';
								$table_setting_json->dbtable="$table_db_name";
								$table_setting_json->tablename="$name";
								$table_setting_json->type="table";
								$table_setting_json->nodeid="node_$d_id";
								?>
								<li class="table_img '.$name.'" data-json='<?echo json_encode($table_setting_json);?>'  id="node_<?php echo $d_id;?>" ><span class="folder" ><?echo $name;?></span></li>
								<?
		        			}
						}
		        	}
				?>	
		        </ul>
			</li>
	    </ul>
  	</div>
</body>
</html>


