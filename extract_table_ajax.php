<?php
session_start();
$sUid=$_SESSION["sUid"];

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


// 取得複製的表的資料
try {
	$sql = "SELECT * from directory_structure where sUid='$sUid' and content_table='$extracted_table_name'";
	$stmt = $db_conn->prepare($sql);
	$exe = $stmt->execute();
	if ($exe===false) {
		throw new PDOException('取得專案目錄資料夾出現錯誤');
	}
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}
$row  = $stmt->fetch();
$root_id = $row["root_id"];
$parent_id = $row["parent_id"];





// 在資料庫裡面的 資料表名稱
$table_db_name = "extract".uniqid();
// 複製資料表
$duplicate_sql = "CREATE TABLE `$table_db_name` LIKE `$extracted_table_name`";
$stmt = $db_conn->exec($duplicate_sql);
try {
	if ($stmt===false) {
		throw new PDOException('複製資料表錯誤');
	}
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}


// 並新增資料
$duplicate_sql = "INSERT $table_db_name  SELECT * FROM $extracted_table_name ";
if ($search_chk==="true") {
	$duplicate_sql.= " WHERE $searchField $searchOper '$searchString'";
}

$stmt = $db_conn->exec($duplicate_sql);
try {
	if ($stmt===false) {
		throw new PDOException('複製資料表錯誤');
	}
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	// 刪除複製的資料
	$del_sql = "DROP TABLE IF EXISTS $table_db_name ";
	$stmt = $db_conn->exec($del_sql);
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}


// 新增到資料夾目錄
$insert_arr = array(
	"sUid"          => $sUid,
	"root_id"      => $root_id,
	"parent_id"      => $parent_id,
	"name"       => $extract_table_name,
	"content_table" => $table_db_name,
	"type"       => "table",
);			
$insert_sql  = "INSERT INTO directory_structure";
$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
$stmt = $db_conn->exec($insert_sql);

// 回傳的資料
$new_extract_table->d_id = $db_conn->lastInsertId();
$new_extract_table->parent_id = $parent_id;
$new_extract_table->table_name = $extract_table_name;
$new_extract_table->table_db_name = $table_db_name;

try {
	if ($stmt===false) {
		throw new PDOException('新增資料表到目錄資料夾錯誤');
	}
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}

echo json_encode($new_extract_table);


?>