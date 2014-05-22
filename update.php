<meta charset="utf-8">
<?PHP

include "include/config.php";
$Update_SQL_arr = array(
	array('check' => "SELECT * from directory_structure", 'update' => "CREATE TABLE IF NOT EXISTS `directory_structure` (
																				  d_id BIGINT NOT NULL auto_increment,																				  
																				  sUid int(11)  NULL,
																				  root_id BIGINT  NULL,
																				  parent_id BIGINT  NULL,
																				  name varchar(200)  NULL,																				  
																				  content_table varchar(200)  NULL,																				  
																				  ACL_file varchar(200)  NULL,																				  
																				  type varchar(20)  NULL,
																				  UNIQUE(d_id)
																				)"),




);
// array( 'check' => "SELECT `o_id` from `data_process`", 'update' => "ALTER TABLE  `data_process` ADD  `o_id` INT NOT NULL" ),

foreach ($Update_SQL_arr as $key => $Update_SQL_data) {
	$check_sql=$Update_SQL_data["check"];
	$update_sql=$Update_SQL_data["update"];
	
	// 檢查是否需要更新
	$check_exe=mysql_query($check_sql);
	if (!$check_exe) {
		// 更新
		$update_exe=mysql_query($update_sql);
		
		if (!$update_exe) {
			$check_exe=mysql_query($check_sql);
	    	// 檢查是否更新成功
	    	if (!$check_exe) {
	    		 echo "第 $key 個更新失敗<br>";
	    		 echo "$update_sql<br>";
	    	}
		}else{
			echo "第 $key 個已更新<br>";
		}
	}else{
		echo "第 $key 個不需更新<br>";
	}
}





?>