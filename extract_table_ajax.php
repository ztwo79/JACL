<?php
session_start();
$sUid=$_SESSION["JACL_sUid"];
$p_id=$_SESSION["JACL_p_id"];


include "JACL_project_directory.php";
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


$JACL_project_directory = new JACL_project_directory($db_conn , $p_id);
// 有搜尋就需放入條件
if ($search_chk==="true") {
	$search_confidion->searchField = $searchField;
	$search_confidion->searchOper = $searchOper;
	$search_confidion->searchString = $searchString;
}
// 先複製表
$new_d_id = $JACL_project_directory->directroy_duplicate_table($t_id ,$extract_table_name , $search_confidion);
$directory_data= $JACL_project_directory->get_directory_by_d_id($new_d_id);
$new_t_id =$directory_data["content_id"];
$JACL_table_control = new JACL_table_control($db_conn);
$JACL_table_control->get_table_data($new_t_id);
$extract_table_name =  $JACL_table_control->table_name;


// 回傳的資料
$new_extract_table->d_id = $new_d_id;
$new_extract_table->parent_id = $directory_data["parent_id"];
$new_extract_table->table_name = $extract_table_name;
$new_extract_table->t_id = $new_t_id;
echo json_encode($new_extract_table);






?>