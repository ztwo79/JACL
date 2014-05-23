<?php
session_start();
include "include/config.php";
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


$sUid = $_SESSION["sUid"];







if ($action=="get_subdirectory") {

	try {

		$sql = "SELECT * from directory_structure where sUid='$sUid' and parent_id='$d_id' ";
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
				$sql = "SELECT COUNT(*) from directory_structure where sUid='$sUid' and parent_id='$d_id' ";
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


		$subdirectory_arr[]=$$name;
	}
	// echo "<pre>";
	// print_r($subdirectory_arr);
	// echo "</pre>";

	echo json_encode($subdirectory_arr);
		


}







?>