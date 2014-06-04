<?php


include_once "JACL_table_control.php";

/**
*  建立專案目錄資料夾
*/
class JACL_project_directory {

	function __construct($db_conn, $p_id, $ACL_project_file='') {
		$this->db_conn = $db_conn;
		$this->p_id = $p_id;
		$this->ACL_project_file = $ACL_project_file;
		// 資料夾的來源
		$this->ACL_project_file_src="ACL DATA/$ACL_project_file";
		//  對資料表的操作
		$this->JACL_table_control = new JACL_table_control($this->db_conn , $this->p_id);
	}


	// 取得專案資料夾的id的
	public function get_root_folder_id() {
		// $this->destroy_project_directory();
		if (!empty($this->root_folder_id)) {
			return $this->root_folder_id;
		}
		// 先讀取使用者是否已經有此專案
		try {
			$sql = "SELECT * from JACL_directory_structure where p_id='$this->p_id' and parent_id='0' and type='root_folder' ";
			$stmt = $this->db_conn->prepare($sql);
			$exe=$stmt->execute();
			if ($exe===false) {
				throw new PDOException('取得專案目錄資料夾出現錯誤');
			}
			$root_folder_rs = $stmt->fetch();
			$this->root_folder_id = $root_folder_rs["d_id"];
			return $root_folder_rs["d_id"];
			
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}
	// 取得資料 by d_id
	public function get_directory_by_d_id($d_id) {
		try {
			$sql = "SELECT * from jacl_directory_structure where d_id='$d_id'";
			$stmt = $this->db_conn->prepare($sql);
			$exe = $stmt->execute();
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
		$row  = $stmt->fetch();
		return $row;

	}

	// 建立整個專案資料夾
	public function create_new_directory() {
		// 要是建立專案資料夾的過程中有問題 就把現在這個專案資料夾刪除
		// 建立專案根目錄
		$this->create_new_root_directory();
		// 讀取ACL 專案檔 建立專案資料夾結構
		$this->create_new_directory_structure_by_ACLfile();
		
	}
	// 建立專案根目錄
	public function create_new_root_directory() {
		// 把專案檔輸入進資料庫
		$insert_arr = array(
			"p_id"          => $this->p_id,
			"name"      => $this->ACL_project_file,				
			"root_id"      => 0,				
			"parent_id"      => 0,				
			"type"      => "root_folder"
		);
		try {
			$insert_sql  = "INSERT INTO JACL_directory_structure";
			$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
			$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
			$stmt = $this->db_conn->exec($insert_sql);
			$this->root_folder_id = $this->db_conn->lastInsertId();
			if ($stmt===false) {
				throw new PDOException('新增專案根目錄資料夾錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			$this->destroy_project_directory();
		}
	}

	// 建立整個專案結構
	public function create_new_directory_structure_by_ACLfile() {
		// 讀取專案檔資料
		if (file_exists($this->ACL_project_file_src)) {
			$ACL_project_file_fp=fopen($this->ACL_project_file_src , "r");
			$ACL_project_file_size=filesize($this->ACL_project_file_src);
			$ACL_project_file_data=fread($ACL_project_file_fp, $ACL_project_file_size);
			$ACL_project_file_data=mb_convert_encoding($ACL_project_file_data, 'UTF-8', 'UTF-16LE');
			// 取得整個ACL專案檔資料
			$this->acl_project_file_arr=explode("\n", $ACL_project_file_data);
		}else{
			die("該ACL專案檔不存在:".$this->ACL_project_file_src);
		}
		if (!empty($this->acl_project_file_arr)) {
			foreach ($this->acl_project_file_arr as $line => $each_line) {
				$this->line=$line;
				$this->each_line=$each_line;

				// 建立資料表的結構   並新增id進入專案資料夾內
				$this->create_table_structue();
				// 建立資料夾結構
				$this->create_folder_structure();
				// 輸入資料表的資料進入資料庫
				$this->insert_table_data();
				// 建立script
				$this->create_script();
			}
			// 更新資料表的父資料夾id
			$this->update_table_parent_id();	
		}
	}


	// 取得資料表 名稱
	public function create_table_structue() {
		// 取得資料表結構
		$table_chk = strpos($this->each_line,"^LAYOUT");				
		if ($table_chk!==false) {
			$table_arr=explode(" ", $this->each_line);
			$table_arr = array_diff($table_arr, array(null,'null','',' '));
			$this->table_name =  next($table_arr);
			$this->get_table_detail_chk=true;
			$table_length =  next($table_arr);

			// 在資料庫裡面的 資料表名稱
			$table_db_name = "table".uniqid();
			$this->table_description[$this->table_name]["table_db_name"] = $table_db_name;
			// 取得 資料表 長度 內容格式
			$this->table_description[$this->table_name]["line_length"] = $table_length;
			$this->table_col_arr=array();
		}


		// 取得資料表內容 並輸入進資料庫
		if ($this->get_table_detail_chk ===true && $table_chk ===false) {
			$table_detail_arr=explode(" ", $this->each_line);
			$table_detail_arr = array_diff($table_detail_arr, array(null,'null','',' '));
			$col_name=current($table_detail_arr);
			$col_type=next($table_detail_arr);
			$col_index = next($table_detail_arr);
			$col_length = next($table_detail_arr);
			$detail_index = $col_index+$col_length-1;

			$table_name=$this->table_name;
			$table_length = $this->table_description[$this->table_name]["line_length"];
			$table_db_name = $this->table_description[$this->table_name]["table_db_name"];

			$this->table_description[$table_name]["col_name"][]=$col_name;
			$this->table_description[$table_name]["col_start"][]=$col_index;
			$this->table_description[$table_name]["col_length"][]=$col_length;
			$this->table_description[$table_name]["col_type"][]=$col_type;
			
			$decimal_dot="";
			switch ($col_type) {
				case 'NUMERIC':
					$sql_type="INT";
					$decimal_dot = next($table_detail_arr);
					$this->table_description[$table_name]["db_data_type"][]="INT";
				break;

				case 'DATETIME':
					$sql_type="varchar(10)";
					$this->table_description[$table_name]["db_data_type"][]="varchar";
				break;

				case 'ACL':
					$sql_type="double";
					$decimal_dot = next($table_detail_arr);
					$this->table_description[$table_name]["db_data_type"][]="double";
				break;

				case 'ASCII':
					$sql_type="varchar($col_length)";
					$this->table_description[$table_name]["db_data_type"][]="varchar";
				break;

				// VARCHAR
				default:
					$sql_type="varchar($col_length)";
					$this->table_description[$table_name]["db_data_type"][]="varchar";
				break;
			}		
			
			// 小數點後幾位
			$this->table_description[$table_name]["decimal_dot"][]=$decimal_dot;
			// 存入資料庫的資料型態
			$this->table_col_arr[]="`$col_name` $sql_type".' NULL';
			// 取得最後一個col 
			if ($detail_index==$table_length ) {
				
				// 新增資料表 並取得table 的 id
				$t_id = $this->JACL_table_control->create_new_table( $table_name , $table_db_name);
				// 記錄 table layout
				$this->JACL_table_control->record_table_layout( $t_id , $this->table_description[$table_name]);
				// 記錄 t_id
				$this->table_description[$table_name]["t_id"]=$t_id;

				// 輸入資料表 進入目錄
				$insert_arr = array(
					"p_id"          => $this->p_id,
					"root_id"      => $this->root_folder_id,
					"parent_id"      => $this->root_folder_id,
					"content_id" => $t_id,
					"type"       => "table",
				);			
				$insert_sql  = "INSERT INTO JACL_directory_structure";
				$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
				$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
				$stmt = $this->db_conn->exec($insert_sql);
				// 儲存資料表的d_id
				$this->table_description[$table_name]["d_id"] = $this->db_conn->lastInsertId();
				try {
					if ($stmt===false) {
						throw new PDOException('新增目錄資料表錯誤');
					}
				} catch (PDOException $e) {
					$error = $this->db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					$this->destroy_project_directory();
					die();
				}
				$this->get_table_detail_chk=false;				
				if (!empty($this->table_col_arr)) {
					$table_col_sql=implode(",", $this->table_col_arr);					
				}

				// create table sql
				$create_table_sql="CREATE TABLE $table_db_name (";
				$create_table_sql.=$table_col_sql;				
				$create_table_sql.=",key_id  INT  AUTO_INCREMENT  NOT NULL ,INDEX (key_id)";
				$create_table_sql.=")";
				try {
					$this->db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$stmt = $this->db_conn->exec($create_table_sql);
				} catch (PDOException $e) {						
					echo "新增資料表時出現錯誤   錯誤如下 <br>" . $e->getMessage();
					echo "錯誤行數: " . $e->getline()."<br>";
					$this->destroy_project_directory();
					die();
				}
			}
		}
	}
	// 建立資料夾
	public function create_folder_structure() {
		// 取得資料夾結構
		$folder_structure_chk = strpos($this->each_line,"^FOLDER");
		if ($folder_structure_chk !==false) {
			$folder_name_arr=explode(" ", $this->each_line);
			// 清除空白
			$folder_name_arr = array_diff($folder_name_arr, array(null,'null','',' '));
			$folder_name_arr = array_values($folder_name_arr);
			$folder_name =  next($folder_name_arr);		
			$folder_id =  next($folder_name_arr);		
			// 取得 parent id 資料行
			$parent_id_line = $this->acl_project_file_arr[$this->line+1];
			$parent_id_arr=explode(" ", $parent_id_line);
			// 清除空白
			$parent_id_arr = array_diff($parent_id_arr, array(null,'null','',' '));
			$parent_id_arr = array_values($parent_id_arr);
			$folder_parent_id =  next($parent_id_arr);
			
			// 建立資料夾結構
			$folder_db_folder_id = $ACL_folder_data["db_folder_id"];
			
			// parent_id = 0 代表parent folder 為根目錄
			if ($folder_parent_id==="0") {
				$folder_parent_id=$this->root_folder_id;
			}else{
				$folder_parent_id = $this->folder_db_mapping_arr[$folder_parent_id];
			}
			
			// 把資料夾結構輸入進入資料庫
			$insert_arr = array(
				"p_id"          => $this->p_id,
				"root_id"      => $this->root_folder_id,
				"parent_id"      => $folder_parent_id,
				"name"       => $folder_name,
				"type"       => "folder"
			);			
			$insert_sql  = "INSERT INTO JACL_directory_structure";
			$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
			$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
			$stmt = $this->db_conn->exec($insert_sql);
			// 資料夾對應的資料庫的id
			$this->folder_db_mapping_arr[$folder_id] = $this->db_conn->lastInsertId();

			try {
				if ($stmt===false) {
					throw new PDOException('新增專案目錄資料夾錯誤');
				}
			} catch (PDOException $e) {
				$error = $this->db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				$this->destroy_project_directory();
				// echo "錯誤內容: " . $error[2];
			}
		}
	}
	// 輸入資料進入資料表
	public function insert_table_data() {
		// 取得需要輸入資料表的內容
		$table_file_chk = strpos($this->each_line,"^FORMAT");				
		if ($table_file_chk!==false) {
			// 取得資料表的資料
			$table_file_arr=explode(" ", $this->each_line);
			$table_file_arr = array_diff($table_file_arr, array(null,'null','',' '));
			$table_file_arr=array_values($table_file_arr);
			$insert_table = $table_file_arr[1];
			// ACL 存 table 資料的檔案
			$table_file_name= str_replace('"', "", $table_file_arr[4]);
			// 資料庫內表的名稱
			$table_db_name=$this->table_description[$insert_table]["table_db_name"];
			$each_ling_length=$this->table_description[$insert_table]["line_length"]+1;
			$col_name_arr =  $this->table_description[$insert_table]["col_name"];
			$col_width_arr =  $this->table_description[$insert_table]["col_length"];
			$col_start_arr =  $this->table_description[$insert_table]["col_start"];
			$col_type_arr =  $this->table_description[$insert_table]["col_type"];
			$col_decimal_dot_arr =  $this->table_description[$insert_table]["decimal_dot"];
			$table_d_id =  $this->table_description[$insert_table]["d_id"];
			$table_t_id =  $this->table_description[$insert_table]["t_id"];
			$arr_count = count($col_start_arr)-1;


			// 記錄ACL FILE 到 資料庫裡
			$this->JACL_table_control->set_ACL_file( $table_t_id , $table_file_name);

			// 取得該資料表的資料夾位置
			// 取得 parent id 資料行
			$parent_id_line = $this->acl_project_file_arr[$this->line+1];
			$parent_id_arr=explode(" ", $parent_id_line);
			// 清除空白
			$parent_id_arr = array_diff($parent_id_arr, array(null,'null','',' '));
			$parent_id_arr = array_values($parent_id_arr);
			$table_parent_id =  next($parent_id_arr);
			
			
			// 儲存資料表要更新的資訊  父資料夾id 與 ACL file name
			$this->update_table_data[$table_d_id]["parent_folder_id"]=$table_parent_id;
			$this->update_table_data[$table_d_id]["ACL_file"]=$table_file_name;

			// 讀取資料表內容 輸入進資料庫
			$insert_table_file_src="ACL DATA/table_file/$table_file_name";
			try {
				if (!file_exists($insert_table_file_src)) {
					throw new PDOException("該資料檔不存在:$table_file_name");
				}	
			} catch (Exception $e) {
				echo $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				$this->destroy_project_directory();
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
						$this->db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $this->db_conn->exec($insert_sql);
					} catch (PDOException $e) {						
						echo "輸入資料時出現錯誤   錯誤如下 <br>" . $e->getMessage();
						echo "錯誤行數: " . $e->getline()."<br>";
						$this->destroy_project_directory();
						die();
					}		
 				}
			}
		}
	}

	// 更新資料表的parent_id
	public function update_table_parent_id() {
		// 取得該parent_id  在db的id
		if (!empty($this->update_table_data)) {
			// f取得更新資料
			foreach ($this->update_table_data as $table_d_id => $update_data) {
				$table_file_name=$update_data["ACL_file"];
				$table_db_parent_id = $this->folder_db_mapping_arr[$update_data["parent_folder_id"]];
				// 更新資料表的 父資料夾id
				$update_sql = "UPDATE JACL_directory_structure set parent_id='$table_db_parent_id' where d_id='$table_d_id'";
				$stmt = $this->db_conn->exec($update_sql);
				try {
					if ($stmt===false) {
						throw new PDOException('更新資料表位置錯誤');
					}
				} catch (PDOException $e) {
					$error = $this->db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					$this->destroy_project_directory();
					die();
				}
			}
		}
	}

	// 建立script
	public function create_script() {
		// 取得script 並新增至 資料庫
		$script_chk = strpos($this->each_line,"^BATCH");
		if ($script_chk!==false) {
			$script_name_arr=explode(" ", $this->each_line);
			// 清除空白
			$script_name_arr = array_diff($script_name_arr, array(null,'null','',' '));
			$script_name_arr = array_values($script_name_arr);
			$script_name =  next($script_name_arr);
			// 取得 parent id 資料行
			$parent_id_line = $this->acl_project_file_arr[$this->line+1];
			$parent_id_arr=explode(" ", $parent_id_line);
			// 清除空白
			$parent_id_arr = array_diff($parent_id_arr, array(null,'null','',' '));
			$parent_id_arr = array_values($parent_id_arr);
			$folder_parent_id =  next($parent_id_arr);
			if ($folder_parent_id==="0") {
				$folder_parent_id = $this->root_folder_id;
			}else{
				$folder_parent_id = $this->folder_db_mapping_arr[$folder_parent_id];	
			}
			// 把script 輸入進入資料庫
			$insert_arr = array(
				"p_id"          => $this->p_id,
				"root_id"      => $this->root_folder_id,
				"parent_id"      => $folder_parent_id,
				"name"       => $script_name,
				"type"       => "script"
			);			
			$insert_sql  = "INSERT INTO JACL_directory_structure";
			$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
			$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
			$stmt = $this->db_conn->exec($insert_sql);
			try {
				if ($stmt===false) {
					throw new PDOException('新增專案目錄資料夾錯誤');
				}
			} catch (PDOException $e) {
				$error = $this->db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				$this->destroy_project_directory();
			}
		}
	}

	// 複製資料表  t_id :要被複製的 table id , extract_table_name : 新複製的表的名稱 , 搜尋的條件 :  $search_confidion
	public function directroy_duplicate_table($t_id ,$extract_table_name, $search_confidion) {
		//有查詢條件
		if (!empty($search_confidion)) {
			$this->JACL_table_control->set_search_confidion($search_confidion);
		}
		// 複製整個資料表
		$new_t_id =  $this->JACL_table_control->copy_table($t_id , $extract_table_name);
		// 建立新被複製的資料夾節點
		$copy_arr = array(
			"p_id"          => "p_id",
			"root_id"      => "root_id",				
			"parent_id"      => "parent_id",				
			"name"      => "name",				
			"type"      => "type",				
			"file_order"      => "file_order",				
			"content_id"      => "'$new_t_id'",
		);			
		$copy_sql  = "INSERT INTO jacl_directory_structure";
		$copy_sql .= " (".implode(",", array_keys($copy_arr)).")";
		$copy_sql .= "SELECT   ".implode(", ", $copy_arr)."  FROM jacl_directory_structure  WHERE  p_id = '$this->p_id' and content_id = '$t_id'";
		$stmt = $this->db_conn->exec($copy_sql);
		$new_d_id = $this->db_conn->lastInsertId();
		try {
			if ($stmt===false) {
				throw new PDOException('新增專案目錄資料夾錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		return $new_d_id;

	}

	// 刪除專案資料夾內容
	public function destroy_project_directory() {
		// 刪除資料夾內script
		$this->del_script();
		// 刪除資料夾內資料表
		$this->del_folder_table();
		// 刪除資料夾
		$this->del_folder();
		// 刪除根目錄
		$this->destroy_root_folder();
		
	}
	// 刪除 根目錄資料夾
	public function destroy_root_folder() {
		$del_sql = "DELETE FROM JACL_directory_structure where p_id='$this->p_id' and root_id='0'";
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
	// 刪除所有folder
	public function del_folder() {
		$del_sql = "DELETE FROM JACL_directory_structure where p_id='$this->p_id' and root_id='$this->root_folder_id' and type='folder'";
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
		// 刪除資料表
		$this->JACL_table_control->del_table_by_p_id($this->p_id);
		// 刪除檔案目錄上的資料表
		$del_sql = "DELETE FROM JACL_directory_structure where p_id='$this->p_id'";
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
	// 刪除script
	public function del_script() {
		$del_sql = "DELETE FROM JACL_directory_structure where p_id='$this->p_id' and root_id='$this->root_folder_id' and type='script'";
		$stmt = $this->db_conn->exec($del_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('刪除script錯誤');
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

?>