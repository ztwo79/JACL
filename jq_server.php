<?
include "include/config.php";
$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;

// $page=1;
// $limit=20;

// get total row
try {
	$stmt = $db_conn->query('SELECT COUNT(*) FROM employee_list');	
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
	$stmt = $db_conn->query("SELECT * FROM employee_list ORDER BY $sidx $sord LIMIT $start , $limit");
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
$i=0;
while($row = $stmt->fetch()) {
	
	$responce->rows[$i]['id']=$row["key_id"];
    // $responce->rows[$i]['cell']=array($row["key_id"], $row["First_Name"], $row["Last_Name"], $row["CardNum"], $row["EmpNo"], $row["HireDate"], $row["Salary"]);
    $responce->rows[$i]['cell']=array($row["key_id"], $row["First_Name"], $row["Last_Name"], $row["CardNum"], $row["EmpNo"], $row["HireDate"], $row["Salary"], $row["Bonus_2005"]);
    $i++;


}

// $SQL = "SELECT a.id, a.invdate, b.name, a.amount,a.tax,a.total,a.note FROM invheader a, clients b WHERE a.client_id=b.client_id ORDER BY $sidx $sord LIMIT $start , $limit";

// $i=0;
// while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    
// }
echo json_encode($responce);




?>