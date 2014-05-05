<?

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

$table_name="agents_metaphor";


$select_sql="SELECT COUNT(*) AS total_col from information_schema.columns  where TABLE_NAME='$table_name'";
$select_exe=mysql_query($select_sql);
$select_rs=mysql_fetch_array($select_exe);
$total_col=$select_rs["total_col"];
echo $total_col;


?>