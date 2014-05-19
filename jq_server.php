<?
include "include/config.php";
$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;

// $page=1;
// $limit=20;
// $table_name="employee_list";

// get total row
try {
	$stmt = $db_conn->query("SELECT COUNT(*) FROM $table_name");
	if ($stmt===false) {
		throw new Exception('取得資料總筆數出現錯誤');
	}
} catch (Exception $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取發生錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}
$count = $stmt->fetchColumn();

// get start and length 
if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

// table data
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;






// get table data
try {
	$stmt = $db_conn->query("SELECT COUNT(*) AS total_col from information_schema.columns  where TABLE_NAME='$table_name'");
	if ($stmt===false) {
		throw new Exception('取得資料表總欄位數出現錯誤');
	}
} catch (Exception $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取發生錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}
$total_col= $stmt->fetchColumn()-1;

// get table data
try {
	$stmt = $db_conn->query("SELECT * FROM $table_name ORDER BY $sidx $sord LIMIT $start , $limit");
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
$row_counter=0;
while($row = $stmt->fetch()) {
	//  table order
	$responce->rows[$row_counter]['id']=$row["key_id"];
	
	$data_arr = array();
	$data_arr[] = $row["key_id"];
	for ($i=0; $i < $total_col; $i++) {
		$data_arr[] = $row[$i];
	}
    // $responce->row[$i]['cell']=array($row["key_id"], $row["First_Name"], $row["Last_Name"], $row["CardNum"], $row["EmpNo"], $row["HireDate"], $row["Salary"]);
    $responce->rows[$row_counter]['cell'] = $data_arr;
    $row_counter++;
}

echo json_encode($responce);




?>