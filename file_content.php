<?php
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

// $table_name="agents_metaphor";		// 之後接收GET就刪掉這行


// 使用 information_schema.columns 資料表找出某一資料庫的某一資料表的欄位資訊
$sql_colname = "SELECT * FROM  information_schema.columns where TABLE_SCHEMA='acl_online' and TABLE_NAME='$table_name' order by ORDINAL_POSITION";

				mysql_query("SET NAMES 'utf8'");
				$colname_exe = mysql_query($sql_colname);	// $colname_exe	取出的資料
				$colname_rs=mysql_num_rows($colname_exe);	// $colname_rs 有幾筆資料
				

echo '<TABLE BORDER="1" ALIGN="center">';	// 輸出表格開始
echo '<tr bgcolor="#859e78">';	// 第一行(顯示欄位名稱)，設欄位顏色
echo '<td></td>';		// 第一行第一格空格
				
// $colname_exe 每筆資料做輸出
for($i=0; $i < $colname_rs ;$i++)
{
		$rowcolname = mysql_fetch_assoc($colname_exe);	// $rowcolname 是$colname_exe的一筆資料
		
		echo "<td>".$rowcolname['COLUMN_NAME']."</td>";	// $rowcolname['COLUMN_NAME'] 顯示 $rowcolname這筆資料的 COLUMN_NAME 欄位資料
		
}
				
echo '</tr>';		// 第一行結束





		
// 找出要找的表格($table_name)的所有資料
$sql_alldata = "SELECT * FROM $table_name";
				mysql_query("SET NAMES 'utf8'");
				$alldata_exe = mysql_query($sql_alldata);	// $alldata_exe 取出的資料
				
				$alldatanumber = mysql_num_rows($alldata_exe);	// $alldatanumber 有幾筆資料

	
// $alldata_exe 每筆資料做輸出
for($i=1; $i <= $alldatanumber ;$i++)
{
	echo '<tr>';	//一行
	echo '<td bgcolor="#859e78">'.$i.'</td>';	// 每行第一格顯示數字(第幾筆資料)，欄位顏色
	
	$onelinedata = mysql_fetch_row($alldata_exe);	// $onelinedata 是 $alldata_exe 的一筆資料
	
	//用上一個sql找出的欄位個數($colname_rs)，輸出這筆資料($onelinedata)的每一格資料
	for($j=0; $j < $colname_rs ;$j++)
	{
		echo '<td bgcolor="#f2f2c2">';	//一格，欄位顏色
		echo $onelinedata[$j];	// $onelinedata[$j] 顯示 $onelinedata 的第j格資料
		echo '</td>';
	}
	
	echo '</tr>';
		
}

echo '</TABLE>';	//表格結束

?>