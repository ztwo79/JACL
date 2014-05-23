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


//echo "$log_file_name";	// $log_file_name 接收的log檔名稱

//$table_name="agents_metaphor";		// 之後接收GET就刪掉這行

if(isset($table_name)){


// 使用 information_schema.columns 資料表找出某一資料庫的某一資料表的欄位資訊
$sql_colname = "SELECT * FROM  information_schema.columns where TABLE_SCHEMA='acl_online' and TABLE_NAME='$table_name' order by ORDINAL_POSITION";

mysql_query("SET NAMES 'utf8'");
$colname_exe = mysql_query($sql_colname);	// $colname_exe	取出的資料
$colname_rs=mysql_num_rows($colname_exe);	// $colname_rs 有幾筆資料
				

// <table style="font-size:像素或點數;color:顏色代碼;background-color:顏色代碼">


echo '<TABLE BORDER="1" ALIGN="center" style="font-size:9px">';	// 輸出表格開始
echo '<tr bgcolor="#859e78">';	// 第一行(顯示欄位名稱)，設欄位顏色
echo '<td></td>';		// 第一行第一格空格
				
// $colname_exe 每筆資料做輸出
for($i=1; $i < $colname_rs ;$i++)
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
for($i=1; $i <= $alldatanumber ;$i++) {
	echo '<tr>';	//一行
	echo '<td bgcolor="#859e78">'.$i.'</td>';	// 每行第一格顯示數字(第幾筆資料)，欄位顏色
	
	$onelinedata = mysql_fetch_row($alldata_exe);	// $onelinedata 是 $alldata_exe 的一筆資料
	
	$colname_rsdownone = $colname_rs - 1;	// 最後一欄不用輸出
	
	//用上一個sql找出的欄位個數($colname_rs)，輸出這筆資料($onelinedata)的每一格資料
	for($j=0; $j < $colname_rsdownone ;$j++)
	{
		
		//$onelinedata2 = $onelinedata[$j];
		//$nochangeline = str_replace ("\n","\t",$onelinedata2);
		
		
		
		echo '<td bgcolor="#f2f2c2">';	//一格，欄位顏色
		
		//echo $nochangeline;	// $onelinedata[$j] 顯示 $onelinedata 的第j格資料
		echo $onelinedata[$j];	// $onelinedata[$j] 顯示 $onelinedata 的第j格資料
		echo '</td>';
	}
	
	
	
	echo '</tr>';
		
}

echo '</TABLE>';	//表格結束

	mysql_free_result($colname_exe);
	mysql_free_result($alldata_exe);


}

// 取得lOG 檔
if(isset($log_file_name)){

	$LOG_project_file="$log_file_name.LOG";
	$LOG_project_src="ACL DATA/$LOG_project_file";

	$LOG_project_file_fp=fopen($LOG_project_src , "r");
	$all_size=filesize($LOG_project_src);
	$all=fread($LOG_project_file_fp, $all_size);
	$all=mb_convert_encoding($all, 'UTF-8', 'UTF-16LE');

	//echo "$LOG_project_src";
	//echo "$all";

	$nowline = 0;

	$data_arr=explode("\n", $all);	// $data_arr : LOG檔分成一行一行

	if (!empty($data_arr)) {

		//echo "enter if";




		//每行找
		foreach ($data_arr as $line => $each_line) {
			$nowline = $nowline +1;	//記錄log檔目前行數
			//echo "enter foreach";
			$LOG_big_chk = strpos($each_line,"@@");
			if ($LOG_big_chk!==false) {
				//echo "enter m if";
				$LOG_big_arr=explode(" Opened at ", $each_line);	//用Opened at分前後句
				//$LOG_big_arr = array_diff($LOG_arr, array(null,'null','',' '));	//??在這是否需要
				$LOG_big_time_all = next($LOG_big_arr);	//$LOG_big_time_all : Opened at 的後半段字

				$LOG_big_time_arr = explode(" ", $LOG_big_time_all);	//$LOG_big_time_arr :Opened at 後半段字分成陣列
				$LOG_big_time_time = next($LOG_big_time_arr);	//$LOG_big_time_time :時間
				$LOG_big_time_on = next($LOG_big_time_arr);		//$LOG_big_time_on :on
				$LOG_big_time_day = next($LOG_big_time_arr);	//$LOG_big_time_day :日期

				echo "$LOG_big_time_time ";
				echo "$LOG_big_time_on ";
				echo "$LOG_big_time_day";
				echo "<br>";
				$LOG_second_chk=true;
				//$LOG_second_arr = array();

				continue;
			}	

			if($LOG_second_chk == true){
				$LOG_detail_oneline_chk = strpos($each_line,"@ OPEN");
				if($LOG_detail_oneline_chk!==false){
					//$detail_line_number = 0;
					$LOG_second_arr=explode(" ", $each_line);
					$LOG_second_open = next($LOG_second_arr);
					$LOG_second_tablename = next($LOG_second_arr);

					$LOG_detail_chk = true;
					$LOG_second_chk = false;

					echo "--";
					echo "$LOG_second_tablename";
					$LOG_detal_line = 1;
				}
				continue;
			}

			if($LOG_detail_chk == true){
				//echo "enter detail";
				//$LOG_detal_line = $LOG_detal_line + 1;
				$LOG_detail_oneword_chk = strpos($each_line,"fields activated");
				if($LOG_detail_oneword_chk!==false){

					echo "    yes";
					echo "<br>";
					//if($LOG_detail_line ==3){
						$LOG_second_chk = true;
					//}
				}

				$LOG_detail_oneword_chk = strpos($each_line,"Command Failed");
				if($LOG_detail_oneword_chk!==false){
					echo "    no";
					echo "<br>";
					//if($LOG_detail_line ==4){
						$LOG_second_chk = true;
					//}
				}
				continue;
			}
		}
	}
}

?>