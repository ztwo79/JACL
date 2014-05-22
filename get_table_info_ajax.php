<?php
include "include/config.php";




// $db_table_name="employee_list";
// get table column  
try {
	$stmt = $db_conn->query("SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM  information_schema.columns where TABLE_SCHEMA='acl_online' and TABLE_NAME='$db_table_name' and COLUMN_NAME != 'key_id'  order by ORDINAL_POSITION ");
	//@debug
	// echo "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM  information_schema.columns where TABLE_SCHEMA='acl_online' and TABLE_NAME='$db_table_name' and COLUMN_NAME != 'key_id'  order by ORDINAL_POSITION <br>";
	if ($stmt===false) {
		throw new Exception('取得資料表內容出現錯誤');
	}
} catch (Exception $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取發生錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}

$col_name_arr = array("key_id");

// put first 
$key_id->name="key_id";
$key_id->index="key_id";
$key_id->width=120;
// $key_id->sortable=false;
$colModel_arr[]=$key_id;	
// 
while($row = $stmt->fetch()) {
	$col_name_arr[]=$row["COLUMN_NAME"];
	$obj_name =	$row["COLUMN_NAME"];
	$$obj_name->name=$row["COLUMN_NAME"];
	$$obj_name->index=$row["COLUMN_NAME"];
	
	$data_type = $row["DATA_TYPE"];
	$CHARACTER_MAXIMUM_LENGTH = $row["CHARACTER_MAXIMUM_LENGTH"];
	switch ($data_type) {
		case 'varchar':
			$var_width =ceil($CHARACTER_MAXIMUM_LENGTH/10);
			$$obj_name->width=100 * $var_width;
		break;

		case 'int':
			$$obj_name->width=120;
		break;

		case 'double':
			$$obj_name->width=120;
		break;
		
		default:
			$$obj_name->width=100;
		break;
	}
 	$colModel_arr[]=$$obj_name;
}

$respose_json->colNames_arr=$col_name_arr;
$respose_json->colModel_arr=$colModel_arr;

echo json_encode($respose_json);


?>