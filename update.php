<?PHP

include "include/config.php";


$Update_SQL_arr = array(
	array('check' => "SELECT count from detail_show_setting", 'update' => "CREATE TABLE IF NOT EXISTS `detail_show_setting` (
																				  `show_id` BIGINT NOT NULL auto_increment,																				  
																				  `sUid` int(11) default NULL,
																				  `show_module_list` varchar(65535) collate utf8_unicode_ci default NULL,
																				  `show_process_list` varchar(65535) collate utf8_unicode_ci default NULL,																				  
																				  INDEX (show_id),
																				  UNIQUE (show_id)
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