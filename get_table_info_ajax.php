<?php
include "include/config.php";

include "JACL_table_control.php";

// 取得t_id
$t_id=$_GET["t_id"];
// 取得table layout 的資料
$JACL_table_control = new JACL_table_control($db_conn);
$table_layout_obj = $JACL_table_control->get_table_layout_by_t_id($t_id);
$col_data_arr = $table_layout_obj->data;
// 先放入 key_id的值
$col_name_arr = array("key_id");

// put first 
$key_id->name="key_id";
$key_id->index="key_id";
$key_id->width=120;
// $key_id->sortable=false;
$colModel_arr[]=$key_id;	

// 放入資料
foreach ($table_layout_obj->data as $L_id => $layout_data) {
	$col_name_arr[]=$layout_data["col_name"];
	$obj_name =	$layout_data["col_name"];
	$$obj_name->name=$layout_data["col_name"];
	$$obj_name->index=$layout_data["col_name"];
	
	$data_type = $row["varchar"];
	$CHARACTER_MAXIMUM_LENGTH = $row["col_length"];
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

// echo "<pre>";
// print_r($respose_json);
// echo "</pre>";


echo json_encode($respose_json);

?>