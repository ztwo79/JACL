<meta charset="utf-8">
<?PHP

include "include/config.php";

// date_default_timezone_set('Asia/Taipei');
// ini_set( 'memory_limit', '99999M' );
// set_time_limit(0);
// /*************************³sµ²¸ê®Æ®w**********************************/
	
// // 使用localhost 會導致連結Mysql過慢
// // $HOST = "localhost";
// $HOST = "127.0.0.1";
// $DB_name="JACL";
// $USER = "root";
// $PASSWORD = "jacksoft";	 
// $conn = mysql_connect($HOST,$USER,$PASSWORD) or die(mysql_error());
// mysql_query("SET NAMES 'utf8'");
// mysql_select_db($DB_name, $conn) or die(mysql_error());



$Update_SQL_arr = array(
	// 舊的格式
	// array('check' => "SELECT * from member", 'update' => "CREATE TABLE `member` (
	// 															  `sUid` bigint(11)  NULL auto_increment,
	// 															  `systemUser` varchar(50)  NULL,
	// 															  `sPass` varchar(50)  NULL,
	// 															  `sMail` varchar(100)  NULL,
	// 															  `sFullname` varchar(100)  NULL,
	// 															  UNIQUE(sUid)
	// 															)"),//使用者清單
	// array('check' => "SELECT * from directory_structure", 'update' => "CREATE TABLE IF NOT EXISTS `directory_structure` (
	// 																	  d_id BIGINT NOT NULL auto_increment,																				  
	// 																	  sUid int(11)  NULL,
	// 																	  root_id BIGINT  NULL,
	// 																	  parent_id BIGINT  NULL,
	// 																	  name varchar(200)  NULL,																				  
	// 																	  content_table varchar(200)  NULL,																				  
	// 																	  ACL_file varchar(200)  NULL,																				  
	// 																	  type varchar(20)  NULL,
	// 																	  file_order BIGINT NULL,
	// 																	  UNIQUE(d_id)
	// 																	)"),


	array('check' => "SELECT * from JACL_directory_structure", 'update' => "CREATE TABLE IF NOT EXISTS `JACL_directory_structure` (
																				  d_id BIGINT NOT NULL auto_increment,																				  
																				  p_id BIGINT NOT NULL,
																				  root_id BIGINT  NULL,
																				  parent_id BIGINT  NULL,
																				  name varchar(200)  NULL,
																				  type varchar(20)  NULL,
																				  content_id BIGINT NULL,
																				  file_order BIGINT NULL,
																				  UNIQUE(d_id)
																				)"),//資料夾格式

	
	array('check' => "SELECT * from log_file_log", 'update' => "CREATE TABLE `log_file_log` (
																  `sUid` int(11) default NULL,
																  `LOG_file_name` varchar(100) default NULL,
																  `LOG_file_src` varchar(100) default NULL,
																  `LOG_file_number` int(11) default NULL,
																  `ACL_name` varchar(100) default NULL,
																  `log_file_LOG_id` int(11) NOT NULL auto_increment,
																  PRIMARY KEY  (`log_file_LOG_id`)
																) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"),
	array('check' => "SELECT * from log_all_file", 'update' => "CREATE TABLE `log_all_file` (
																  `sUid` int(11) NOT NULL,
																  `LOG_file_number` int(11) default NULL,
																  `log_file_id` int(11) default NULL,
																  `table_open_id` int(11) default NULL,
																  `time` varchar(20) default NULL,
																  `day` varchar(20) default NULL,
																  `show_tree_word` varchar(100) default NULL,
																  `table_open_number` int(11) default NULL,
																  `log_all_file_id` int(11) NOT NULL auto_increment,
																  PRIMARY KEY  (`log_all_file_id`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"),
	array('check' => "SELECT * from log_open_table", 'update' => "CREATE TABLE `log_open_table` (
																  `table_open_id` int(11) default NULL,
																  `table_name` varchar(100) default NULL,
																  `openornot` varchar(10) default NULL,
																  `show_tree_word` varchar(50) default NULL,
																  `show_detail_firstline` varchar(300) default NULL,
																  `show_detail_secondline` varchar(300) default NULL,
																  `show_detail_thirdline` varchar(300) default NULL,
																  `table_open_index_id` int(11) default NULL,
																  `table_open_close_id` int(11) default NULL,
																  `table_index_number` int(11) default NULL,
																  `log_file_id` int(11) default NULL,
																  `log_open_table_id` int(11) NOT NULL auto_increment,
																  PRIMARY KEY  (`log_open_table_id`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"),
	array('check' => "SELECT * from log_table_close", 'update' => "CREATE TABLE `log_table_close` (
																  `log_file_id` int(11) default NULL,
																  `table_open_id` int(11) default NULL,
																  `table_open_close_id` int(11) default NULL,
																  `show_tree_word` varchar(100) default NULL,
																  `show_detail_word` varchar(100) default NULL,
																  `log_table_close_id` int(11) NOT NULL auto_increment,
																  PRIMARY KEY  (`log_table_close_id`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"),
	array('check' => "SELECT * from log_table_index", 'update' => "CREATE TABLE `log_table_index` (
																  `log_file_id` int(11) default NULL,
																  `table_open_id` int(11) default NULL,
																  `table_open_index_id` int(11) default NULL,
																  `show_tree_word` varchar(100) default NULL,
																  `show_detail_firstline` varchar(300) default NULL,
																  `show_detail_secondline` varchar(300) default NULL,
																  `show_detail_thirdline` varchar(300) default NULL,
																  `log_table_index_id` int(11) NOT NULL auto_increment,
																  PRIMARY KEY  (`log_table_index_id`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"),
	
	array('check' => "SELECT * from JACL_member", 'update' => "CREATE TABLE `JACL_member` (
																  `sUid` bigint(11)  NULL auto_increment,
																  `systemUser` varchar(50)  NULL,
																  `sPass` varchar(50)  NULL,
																  `sMail` varchar(100)  NULL,
																  `sFullname` varchar(100)  NULL,
																  UNIQUE(sUid)
																)"),//使用者清單

	array('check' => "SELECT * from JACL_PROJECT", 'update' => "CREATE TABLE `JACL_PROJECT` (
																  `p_id` bigint(11)  NOT NULL auto_increment,
																  `sUid` bigint(11)  NOT NULL ,
																  `project_name` varchar(100)  NULL,
																  `last_modified_date` DATETIME  NULL,
																  UNIQUE(p_id)
																)"),//ACL 專案檔

	array('check' => "SELECT * from JACL_TABLE", 'update' => "CREATE TABLE `JACL_TABLE` (
																  `t_id` bigint(11) NOT NULL auto_increment,
																  `p_id` bigint(11) NOT NULL ,
																  `table_name` varchar(100)  NULL,
																  `content_table` varchar(100)  NULL,
																  `ACL_file` varchar(100)  NULL,
																  UNIQUE(t_id)
																)"),//ACL 資料表檔

	array('check' => "SELECT * from JACL_TLAYOUT", 'update' => "CREATE TABLE `JACL_TLAYOUT` (
																  `L_id` bigint(11)  NOT NULL auto_increment,
																  `t_id` bigint(11)  NOT NULL ,
																  `col_name` varchar(100)  NULL,
																  `acl_data_type` varchar(100)  NULL,
																  `col_length` varchar(100)  NULL,
																  `col_decimal_dot` varchar(100)  NULL,
																  `db_data_type` varchar(100)  NULL,
																  `col_order` int(11)  NULL,
																  UNIQUE(L_id)
																)"),//ACL 表格便於做資料字典

	array('check' => "SELECT * from JACL_INDEX", 'update' => "CREATE TABLE `JACL_INDEX` (
																  `i_id` bigint(11)  NOT NULL auto_increment,
																  `p_id` bigint(11)  NOT NULL ,
																  `t_id` bigint(11)  NOT NULL ,
																  `table_name` varchar(100)  NULL,
																  `index_file_name` varchar(100)  NULL,
																  `index_key` varchar(100)  NULL,
																  `create_date` DATETIME  NULL,
																  UNIQUE(i_id)
																)"),//ACL 引索表


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

// 建立使用者
$select_sql="SELECT * FROM JACL_member where systemUser = 'J0001' and sPass = '81dc9bdb52d04dc20036dbd8313ed055'";
$select_exe=mysql_query( $select_sql);
$select_rs=mysql_fetch_array($select_exe);
$sUid = $select_rs["sUid"];
if (empty($sUid)) {
	$insert_arr = array(
		"systemUser"          => "J0001",
		"sPass"      => "81dc9bdb52d04dc20036dbd8313ed055",				
		"sMail"      => "@",				
		"sFullname"      => "JACKSOFT",				
	);			
	$insert_sql  = "INSERT INTO JACL_member";
	$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
	$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
	mysql_query($insert_sql);
}




?>