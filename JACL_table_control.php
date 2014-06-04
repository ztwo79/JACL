<?php

/**
* JACL table 表的動作
*/
class JACL_table_control {
	function __construct($db_conn , $p_id='') {
		$this->db_conn = $db_conn;
		$this->p_id = $p_id;
		$this->search_chk = false;
		$this->limit_chk = false;
		$this->order_chk = false;
	}
	// 取得資料表資料
	public function get_table_data($t_id) {
		try {
			$sql = "SELECT * from jacl_table where t_id='$t_id' ";
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
		$this ->table_name	= $row["table_name"];
		$this ->content_table	= $row["content_table"];
			
	}

	// 建立新資料表
	public function create_new_table($table_name='', $table_db_name='' , $table_file_name='') {
		// 輸入資料表記錄的table
		$insert_arr = array(
			"p_id"          => $this->p_id,
			"table_name"      => $table_name,
			"content_table"      => $table_db_name,
			"ACL_file"      => $table_file_name
		);			
		$insert_sql  = "INSERT INTO jacl_table";
		$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
		$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
		
		$stmt = $this->db_conn->exec($insert_sql);
		return $this->db_conn->lastInsertId();
		try {
			if ($stmt===false) {
				throw new PDOException('記錄資料表發生錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}

	// 複製資料表
	public function copy_table($t_id ,$extract_table_name) {
		// 取得要被複製表的 db_table_name
		$this->get_table_data($t_id);
		$extracted_table_name = $this->content_table;

		// 在資料庫裡面的 資料表名稱
		$table_db_name = "extract".uniqid();
		// 複製資料表
		$duplicate_sql = "CREATE TABLE `$table_db_name` LIKE `$extracted_table_name`";
		$stmt = $this->db_conn->exec($duplicate_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('複製資料表錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}

		// 並新增資料
		$duplicate_sql = "INSERT $table_db_name  SELECT * FROM $extracted_table_name ";
		// 是否搜尋
		if ($this->search_chk===true) {
			$searchField =  $this->search_confidion->searchField;
			$searchOper =  $this->search_confidion->searchOper;
			$searchString =  $this->search_confidion->searchString;
			$duplicate_sql.=" where  $searchField $searchOper '$searchString'";
		}
		$stmt = $this->db_conn->exec($duplicate_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('複製資料表錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			// 刪除複製的資料
			$del_sql = "DROP TABLE IF EXISTS $table_db_name ";
			$stmt = $db_conn->exec($del_sql);
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}

		// 複製 jacl_table
		$copy_arr = array(
			"p_id"          => "p_id",
			"ACL_file"      => "ACL_file",				
			"table_name"      => "'$extract_table_name'",				
			"content_table"      => "'$table_db_name'",				
		);			
		$copy_row_sql  = "INSERT INTO jacl_table";
		$copy_row_sql .= " (".implode(",", array_keys($copy_arr)).")";
		$copy_row_sql .= "SELECT   ".implode(", ", $copy_arr)."  FROM jacl_table  WHERE  t_id = '$t_id'";
		$stmt = $this->db_conn->exec($copy_row_sql);
		$new_t_id = $this->db_conn->lastInsertId();
		try {
			if ($stmt===false) {
				throw new PDOException('複製資料表錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		// 複製 table  layout
		$copy_arr = array(
			"t_id"      => "'$new_t_id'",				
			"col_name"          => "col_name",
			"acl_data_type"          => "acl_data_type",
			"col_length"          => "col_length",
			"col_decimal_dot"          => "col_decimal_dot",
			"db_data_type"          => "db_data_type",
			"col_order"          => "col_order"
		);			
		$copy_table_layout_sql  = "INSERT INTO jacl_tlayout";
		$copy_table_layout_sql .= " (".implode(",", array_keys($copy_arr)).")";
		$copy_table_layout_sql .= "SELECT   ".implode(", ", $copy_arr)."  FROM jacl_tlayout  WHERE  t_id = '$t_id' order by col_order";
		$stmt = $this->db_conn->exec($copy_table_layout_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('複製table layout錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		return $new_t_id;
	}

	// 設定ACL file
	public function set_ACL_file($t_id , $ACL_file) {
		// 設定 ACL file 
		$update_sql = "UPDATE jacl_table set ACL_file = '$ACL_file' where t_id = '$t_id'";
		$stmt = $this->db_conn->exec($update_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('修改資料表的ACL file 錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}
	// 記錄 table 的 layout
	public function record_table_layout($t_id , $table_data) {
		$col_name_arr= $table_data["col_name"];
		$col_length_arr= $table_data["col_length"];
		$col_type_arr= $table_data["col_type"];
		$decimal_dot_arr= $table_data["decimal_dot"];
		$db_data_type_arr= $table_data["db_data_type"];
		
		if (!empty($col_name_arr)) {
			foreach ($col_name_arr as $key => $col_name) {
				// 取得　col_order
				try {
					$sql = "SELECT MAX(col_order) MAX_order from JACL_TLAYOUT where t_id='$t_id' ";
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

				$insert_order  = $stmt->fetchColumn()+1;
				$insert_arr = array(
					"t_id"          => $t_id,
					"col_name"      => $col_name,
					"acl_data_type" => $col_type_arr[$key],
					"col_length" => $col_length_arr[$key],
					"col_decimal_dot" => $decimal_dot_arr[$key],
					"db_data_type" => $db_data_type_arr[$key],
					"col_order" => $insert_order
				);			
				$insert_sql  = "INSERT INTO JACL_TLAYOUT";
				$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
				$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
				
				$stmt = $this->db_conn->exec($insert_sql);
				$root_folder_id = $this->db_conn->lastInsertId();
				try {
					if ($stmt===false) {
						throw new PDOException('新增table layout 錯誤');
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

	// 刪除資料表 by p_id
	public function del_table_by_p_id($p_id) {
		try {
			$sql = "SELECT * from jacl_table where p_id='$p_id'";
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
			$t_id = $row["t_id"];
			$this->del_table($t_id);
		}
	}
	// 刪除table layout
	public function del_table_layout($del_by='L_id' , $del_id) {
		$del_sql = "DELETE FROM JACL_TLAYOUT where $del_by='$del_id'";
		$stmt = $this->db_conn->exec($del_sql);
		try {
			if ($stmt===false) {
				throw new PDOException('刪除table layout 錯誤');
			}
		} catch (PDOException $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}

	// 刪除資料表
	public function del_table($t_id) {
		// 取得資料表 然後drop 掉
		try {
			$sql = "SELECT * from jacl_table where t_id='$t_id'";
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
				$del_sql = "DELETE FROM jacl_table where t_id='$t_id'";
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
		// 刪除table layout
		$this->del_table_layout("t_id" , $t_id);
	}
	// 設定t_id
	public function set_t_id($t_id) {
		$this->t_id = $t_id;
	}
	
	// 取得table layout
	public function get_table_layout_by_t_id($t_id) {
		try {
			$sql = "SELECT * from jacl_tlayout where t_id='$t_id' order by col_order";
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
		$table_layout->t_id= $t_id;
		while($row = $stmt->fetch()) {
			$L_id = $row["L_id"];
			
			$table_layout->data[$L_id]["col_name"]=$row["col_name"];
			$table_layout->data[$L_id]["acl_data_type"]=$row["acl_data_type"];
			$table_layout->data[$L_id]["col_length"]=$row["col_length"];
			$table_layout->data[$L_id]["col_decimal_dot"]=$row["col_decimal_dot"];
			$table_layout->data[$L_id]["db_data_type"]=$row["db_data_type"];
			$table_layout->data[$L_id]["col_order"]=$row["col_order"];
		}
		return $table_layout;
	}
	// 設定查詢條件
	public function set_search_confidion($search_confidion) {
		$this->search_chk=true;
		$this->search_confidion = $search_confidion;
	}
	// 設定搜尋的limit
	public function set_search_limit($start , $limit) {
		$this->limit_chk=true;
		$this->search_limit->start = $start;
		$this->search_limit->limit = $limit;
	}
	// 設定排序
	public function set_search_order($order_col , $order_type) {
		$this->order_chk=true;
		$this->order_col = $order_col;
		$this->order_type = $order_type;
	}


	// 取得資料表總列數
	public function get_total_row() {
		// 取得資料表 名稱
		$this->get_table_data($this->t_id);
		// 取得資料表的總列數
		try {
			$total_row_sql="SELECT COUNT(*) FROM $this->content_table";
			// 是否搜尋
			if ($this->search_chk===true) {
				$searchField =  $this->search_confidion->searchField;
				$searchOper =  $this->search_confidion->searchOper;
				$searchString =  $this->search_confidion->searchString;
				$total_row_sql.=" where  $searchField $searchOper '$searchString'";
			}
			$stmt = $this->db_conn->query($total_row_sql);
			if ($stmt===false) {
				throw new Exception('取得資料總筆數出現錯誤');
			}
		} catch (Exception $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取發生錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		return $stmt->fetchColumn();
	}
	// 取得資料表總欄數
	public function get_total_col() {
		// 取得總欄位數
		try {
			$stmt = $this->db_conn->query("SELECT COUNT(*) AS total_row FROM jacl_tlayout WHERE t_id='$this->t_id'");
			if ($stmt===false) {
				throw new Exception('取得資料表總欄位數出現錯誤');
			}
			return $stmt->fetchColumn();

		} catch (Exception $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取發生錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
	}

	// 取得table 完整資訊
	public function get_table_detail_data() {
		// 取得資料表 名稱
		$this->get_table_data($this->t_id);
		$total_col = $this->get_total_col();
		$table_data->total_col=$total_col;
		try {
			$table_sql="SELECT * FROM $this->content_table ";
			// 是否搜尋
			if ($this->search_chk===true) {
				// 增加搜尋條件
				$searchField =  $this->search_confidion->searchField;
				$searchOper =  $this->search_confidion->searchOper;
				$searchString =  $this->search_confidion->searchString;
				$table_sql.=" where  $searchField $searchOper '$searchString'";
			}
			if ($this->order_chk===true) {
				$table_sql.=" ORDER BY $this->order_col $this->order_type";
			}
			if ($this->limit_chk===true) {
				$start = $this->search_limit->start;
				$limit = $this->search_limit->limit;
				$table_sql.=" LIMIT $start , $limit";
			}
			//@debug
			// echo "$table_sql<br>";
			$stmt = $this->db_conn->query($table_sql);
			if ($stmt===false) {
				throw new Exception('取得資料表內容出現錯誤');
			}
		} catch (Exception $e) {
			$error = $this->db_conn->errorInfo();
			echo "資料庫存取發生錯誤: " . $e->getMessage()."<br>";
			echo "錯誤行數: " . $e->getline()."<br>";
			// echo "錯誤內容: " . $error[2];
			die();
		}
		$row_counter=0;
		while($row = $stmt->fetch()) {
			$table_data->table[$row_counter]['key_id']=$row["key_id"];
 			$data_arr = array($row["key_id"]);
			// $data_arr[] = ;
			for ($i=0; $i < $total_col; $i++) {
				$data_arr[] = $row[$i];
			}
		    // $responce->row[$i]['cell']=array($row["key_id"], $row["First_Name"], $row["Last_Name"], $row["CardNum"], $row["EmpNo"], $row["HireDate"], $row["Salary"]);
		    $table_data->table[$row_counter]['row'] = $data_arr;
		    $row_counter++;
		}
		return $table_data;
	}

}


?>