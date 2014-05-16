<?php
include "include/config.php";




$table_name="employee_list";
// get table column  
try {
	$stmt = $db_conn->query("SELECT COLUMN_NAME FROM  information_schema.columns where TABLE_SCHEMA='acl_online' and TABLE_NAME='$table_name' and COLUMN_NAME != 'key_id'  order by ORDINAL_POSITION ");
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
while($row = $stmt->fetch()) {
	$col_name_arr[]=$row["COLUMN_NAME"];
	
}

$respose_json->colNames_arr=$col_name_arr;

$test_obj->name="key_id";
$test_obj->index="key_id";
$test_obj->width=100;
$colModel_arr[]=$test_obj;

$test_obj->name="First_Name";
$test_obj->index="First_Name";
$test_obj->width=90;
$colModel_arr[]=$test_obj;
$test_obj->name="Last_Name";
$test_obj->index="Last_Name";
$test_obj->width = 90;
$colModel_arr[]=$test_obj;
$test_obj->name="CardNum";
$test_obj->index="CardNum";
$test_obj->width = 80;
$colModel_arr[]=$test_obj;
$test_obj->name="EmpNo";
$test_obj->index="EmpNo";
$test_obj->width = 80;
$colModel_arr[]=$test_obj;
$test_obj->name="HireDate";
$test_obj->index="HireDate";
$test_obj->width = 80;
$colModel_arr[]=$test_obj;
$test_obj->name="Salary";
$test_obj->index="Salary";
$test_obj->width = 80;
$colModel_arr[]=$test_obj;
$test_obj->name="Bonus_2005";
$test_obj->index="Bonus_2005";
$test_obj->width = 80;
$colModel_arr[]=$test_obj;

$respose_json->colModel_arr=$colModel_arr;


					   		// {name:'key_id',index:'key_id', width:100},
					   		// {name:'First_Name',index:'First_Name', width:90},






					

echo json_encode($respose_json);


?>