<?php
session_start();
include "include/config.php";
include "JACL_table_control.php";
//取得GET
if ($_GET) {
	foreach ($_GET as $key => $value) {
    	$$key=$value; 
	}
}	

//避免GET的值被GET蓋掉
// 取得POST
if ($_POST) {
	foreach ($_POST as $key => $value) {
    	if (empty($$key)) {
    		$$key=$value; 
    	}        	
	}
}

$JACL_table_control = new JACL_table_control($db_conn);
$p_id = $_SESSION["JACL_p_id"];

if ($action=="get_subdirectory") {

	try {
		$sql = "SELECT * from JACL_directory_structure where p_id='$p_id' and parent_id='$d_id' ORDER BY FIELD(type,'table','script','folder'),d_id";
		$stmt = $db_conn->prepare($sql);
		$exe = $stmt->execute();
		if ($exe===false) {
			throw new PDOException('取得資料夾內資料出現錯誤');
		}
	} catch (PDOException $e) {
		$error = $db_conn->errorInfo();
		echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
		echo "錯誤行數: " . $e->getline()."<br>";
		// echo "錯誤內容: " . $error[2];
		die();
	}

	while($row = $stmt->fetch()) {
		$d_id = $row["d_id"];
		$type = $row["type"];
		$name = $row["name"];
		
		
		$$name ->name = $name;
		$$name ->type = $type;
		$$name ->d_id = $d_id;

		// folder 需確認 是否底下還有值
		if ($type  == "folder") {
			try {
				$sql = "SELECT COUNT(*) from JACL_directory_structure where p_id='$p_id' and parent_id='$d_id' ";
				$folder_inside_stmt = $db_conn->prepare($sql);
				$exe = $folder_inside_stmt->execute();
				if ($exe===false) {
					throw new PDOException('取得計算子資料夾數量出現錯誤');
				}
			} catch (PDOException $e) {
				$error = $db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				die();
			}
			$folder_inside_count  = $folder_inside_stmt->fetchColumn();
			$$name ->folder_inside_count = $folder_inside_count;
		}
		if ($type == "table") {
			$t_id = $row["content_id"];
			$JACL_table_control ->get_table_data($t_id);
			$name = $JACL_table_control ->table_name;
			$$name ->name = $name;
			$$name ->t_id = $t_id;
			$$name ->type = $type;
			$$name ->d_id = $d_id;
		} 
		$subdirectory_arr[]=$$name;
	}
	
	
	// echo "<pre>";
	// print_r($subdirectory_arr);
	// echo "</pre>";

	echo json_encode($subdirectory_arr);
		


}







?>