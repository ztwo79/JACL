<?php

/**
* JCAL 專案 
*/
class JACL_project_contorl {
	function __construct($db_conn , $sUid , $project_name) {
		$this->db_conn = $db_conn;
		$this->sUid = $sUid;
		$this->project_name = $project_name;
	}
	public function get_p_id() {
		if (empty($this->p_id)) {
			try {
				$sql = "SELECT * from jacl_project where sUid='$this->sUid' and project_name='$this->project_name'";
				$stmt = $this->db_conn->prepare($sql);
				$exe = $stmt->execute();
				if ($exe===false) {
					throw new PDOException('取得專案編號出現錯誤');
				}
			} catch (PDOException $e) {
				$error = $this->db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				die();
			}
			$row  = $stmt->fetch();
			$this->p_id=$p_id;
			return $row["p_id"];
		}
	}
	// 建立JACL專案
	public function create_project(){
		$insert_arr = array(
			"sUid"          => $this->sUid,
			"project_name"      => $this->project_name,
			"last_modified_date"      => date("Y-m-d H:i:s"),
		);			
		$insert_sql  = "INSERT INTO jacl_project";
		$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
		$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
		$stmt = $this->db_conn->exec($insert_sql);
		$root_folder_id = $this->db_conn->lastInsertId();
		try {
			if ($stmt===false) {
				throw new PDOException('新增專案錯誤');
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