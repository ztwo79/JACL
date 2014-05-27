<?php
 include "include/config.php";
 include "include/utility.php";

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

?>



<?php

//echo $log_file_name;
//echo $log_file_num."<br>";
//echo $log_table_num."<br>";
//echo $log_index_num."<br>";
//echo $log_close_num."<br>";

if($log_index_num == 0 && $log_close_num ==0){
	try {
		$stmt = $db_conn->query("SELECT * FROM log_open_table where log_file_id='$log_file_num' and table_open_id='$log_table_num'");
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

	$row = $stmt->fetch();
	

	
	echo "&nbsp;&nbsp;<b>Command:</b>   <font color='blue'><u>".$row["show_tree_word"]."</u></font><br><br>";
	echo "&nbsp;&nbsp;".$row["show_detail_firstline"]."<br>";
	echo "&nbsp;&nbsp;".$row["show_detail_secondline"]."<br>";
	echo "&nbsp;&nbsp;".$row["show_detail_thirdline"]."<br>";
	

}
elseif($log_index_num!=0){
	try {
		$stmt = $db_conn->query("SELECT * FROM  log_table_index where log_file_id='$log_file_num' and table_open_id='$log_table_num' and table_open_index_id='$log_index_num'");
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

	$row = $stmt->fetch();
	
	echo "&nbsp;&nbsp;<b>Command:</b>   <font color='blue'><u>".$row["show_tree_word"]."</u></font><br><br>";
	echo "&nbsp;&nbsp;".$row["show_detail_firstline"]."<br>";
	echo "&nbsp;&nbsp;".$row["show_detail_secondline"]."<br>";
	echo "&nbsp;&nbsp;".$row["show_detail_thirdline"]."<br>";

}
elseif($log_close_num!=0){
	try {
		$stmt = $db_conn->query("SELECT * FROM  log_table_close where log_file_id='$log_file_num' and table_open_id='$log_table_num' and table_open_close_id='$log_close_num'");
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

	$row = $stmt->fetch();
	
	echo "&nbsp;&nbsp;<b>Command:</b>   <font color='blue'><u>".$row["show_tree_word"]."</u></font><br><br>";
	echo "&nbsp;&nbsp;".$row["show_detail_word"]."<br>";


}
else{}




	
?>