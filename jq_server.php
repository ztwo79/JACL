<?
include "include/config.php";
include "JACL_table_control.php";

// table id
$t_id=$_GET["t_id"];
// 確認是否搜尋
$search_chk=$_GET["_search"];
// 搜尋條件
$searchField=$_GET["searchField"];
$searchOper=$_GET["searchOper"];
$searchString=$_GET["searchString"];


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;

// $page=1;
// $limit=20;

$JACL_table_control = new JACL_table_control($db_conn);
$JACL_table_control->set_t_id($t_id);

// 搜尋條件
if ($search_chk==="true") {
	// 設定搜尋條件
	$search_confidion->searchField = $searchField;
	$search_confidion->searchOper = $searchOper;
	$search_confidion->searchString = $searchString;
	$JACL_table_control->set_search_confidion($search_confidion);
}
// 取得資料表總列數
$count = $JACL_table_control->get_total_row();
// get start and length 
if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)
if ($start<0) {
	$start=0;
}
// table data
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
// 設定 資料表搜尋的 start 與 limit
$JACL_table_control->set_search_limit( $start , $limit);
// 設定 排序
$JACL_table_control->set_search_order( $sidx  , $sord);

// 取得資料表的內容
$table_data_obj =  $JACL_table_control->get_table_detail_data($search_limit);
$total_col = $table_data_obj->total_col;
$table_data_arr = $table_data_obj->table;

// 放入傳回的資料
$row_counter=0;
if (!empty($table_data_arr)) {
	foreach ($table_data_arr as $row_data) {
		$responce->rows[$row_counter]['id']=$row_data["key_id"];
	    $responce->rows[$row_counter]['cell'] = $row_data["row"];
	    $row_counter++;
	}
}
// echo "<pre>";
// print_r($responce);
// echo "</pre>";
echo json_encode($responce);

?>