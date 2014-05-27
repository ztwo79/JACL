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



<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <title>Fancytree - Example</title>

  	<!-- 換成directory的 -->
  	<!-- fancytree css  skin -->
	<link href="lib/fancytree/src/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
	<link href="css/directory.css" rel="stylesheet" type="text/css" >

	<script src="js/jquery-1.11.0.min.js"></script>
  	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" type="text/javascript"></script>
	<!-- facyytree  -->
	<script src="lib/fancytree/src/jquery.fancytree.js" type="text/javascript"></script>

	<style type="text/css">
	span.fancytree-node.table_img > span.fancytree-title {
	  /*color: maroon;*/
	  /*font-family: "Audiowide";*/
	}
	span.fancytree-node.log_time_img > span.fancytree-icon {
	  /*background-image: url("fancytree/demo/skin-custom/customDoc2.gif");*/
	  background-image: url("img/acl_treeview/LOG_time.png");
	  background-position: 0 0;
	}
	span.fancytree-node.log_open_table_img > span.fancytree-icon {	  
	  background-image: url("img/acl_treeview/LOG_open_table.png");
	  background-position: 0 0;
	}
	span.fancytree-node.log_open_ok_img > span.fancytree-icon {	  
	  background-image: url("img/acl_treeview/LOG_open_ok.png");
	  background-position: 0 0;
	}
	span.fancytree-node.log_open_not_img > span.fancytree-icon {	  
	  background-image: url("img/acl_treeview/LOG_open_not.png");
	  background-position: 0 0;
	}
	</style>



  <!-- (Irrelevant source removed.) -->

  <script type="text/javascript">
  $(function(){
    var DT = $.ui.fancytree;
    $.ui.fancytree.debug("Using fancytree " + $.ui.fancytree.version);
    // attach to all instances
    $("#tree, #tree3").fancytree({
      checkbox: true,
      selectMode: 1,
      activate: function(event, data){
        var node = data.node;
        //var oo = JSON.stringify(node.data);
        //var rrr = oo["j"];
        var log_file_num = node.data.i;
        var log_table_num = node.data.j;
        var log_index_num = node.data.k;
        var log_close_num = node.data.close;

        if(!(typeof(log_file_num) == "undefined") && !(typeof(log_table_num) == "undefined")){
        //alert(log_file_num);
        //alert(log_table_num);
        	if(!(typeof(log_index_num) == "undefined")){
        		//alert(log_index_num);
        		$('#file_content', window.parent.parent.document).get(0).contentWindow.change_dyanamic_log_detail(log_file_num,log_table_num,log_index_num,0,"Index");
        	}
        	else if(!(typeof(log_close_num) == "undefined")){
        		//alert(log_close_num);
        		$('#file_content', window.parent.parent.document).get(0).contentWindow.change_dyanamic_log_detail(log_file_num,log_table_num,0,log_close_num,"Close");
        	}
        	else{
        		$('#file_content', window.parent.parent.document).get(0).contentWindow.change_dyanamic_log_detail(log_file_num,log_table_num,0,0,"Open");
        		//alert(log_file_num+" "+log_table_num);
        	}


    	}


        //alert(node.data.i);
        $.ui.fancytree.debug("activate: event=", event, ", data=", data);
        if(!$.isEmptyObject(node.data)){

        	//var choose = document.getElementById('choose');
          //alert("custom node data: " + JSON.stringify(node.data));
        }
      },
      lazyLoad: function(event, data){
        data.result = {url: "ajax-sub2.json"};
      }
    }).bind("fancytreeactivate", function(event, data){
      $.ui.fancytree.debug("fancytreeactivate: event=", event, ", data=", data);
      return false;
//          $(this).fancytree("debug", "fancytreeactivate");
    });
    var tree = $("div:ui-fancytree").data("fancytree").getTree();
    DT.debug("Test ':ui-fancytree' expression " + $("div:ui-fancytree").length);

    /* Load tree from Ajax JSON
     */
    $("#tree2").fancytree({
      source: {
        url: "ajax-tree-plain.json"
      },
      lazyLoad: function(event, data){
        // we can't return values from an event handler, so we
        // pass the result as `data`attribute.
//              data.result = {url: "unit/ajax-sub2.json"};
        data.result = $.ajax({
          url: "ajax-sub2.json",
          dataType: "json"
        });
      }
    });
    // call methods on multiple instances
    $("div:ui-fancytree").fancytree("foo", "after init");
    //
    $("button").button();
    $("button#destroy").click(function(event){
      $(":ui-fancytree").fancytree("destroy");
    });
    $("button#init").click(function(event){
      $(".sampletree").fancytree();
    });
    $("button#reload").click(function(event){
      tree.reload([
        {title: "node1"},
        {title: "node2"}
        ]).done(function(){
          alert("reloaded");
        });
    });
    $("button#expand").click(function(event){
      tree.getNodeByKey("id3").toggleExpand().done(function(){
        alert("expanded " + this);
      }).fail(function(errMsg){
        alert("failed to expand " + this + ": " + errMsg);
      });
    });
    $("button#setSource").click(function(event){
      $(".sampletree").fancytree("option", "source", [
        {title: "node1"}
      ]);
    });
  });
  </script>




</head>
<body class="example">

	




<?php

if(!isset($log_file_name)){
	//$log_file_name = "Metaphor_Employee_Data";




	$ALC_project_file="Metaphor_Employee_Data.ACL";
	$ALC_project_src="ACL DATA/$ALC_project_file";

	$ALC_project_file_fp=fopen($ALC_project_src , "r");
	$all_size=filesize($ALC_project_src);
	$all=fread($ALC_project_file_fp, $all_size);
	$all=mb_convert_encoding($all, 'UTF-8', 'UTF-16LE');

	$data_arr=explode("\n", $all);

	if (!empty($data_arr)) {
		foreach ($data_arr as $line => $each_line) {

		//有可能會有多個LOG檔?這樣的話就要存成陣列
			$log_file_chk = strpos($each_line,"^LOGFILE");
			if ($log_file_chk!==false){
				$log_file_arr=explode(" ", $each_line);	// $log_file_arr : 將這行依空格分割
				$log_file_arr = array_diff($log_file_arr, array(null,'null','',' '));	// $table_arr 陣列中與null,'null','',' '匹配的刪掉
				$log_file_name =  next($log_file_arr);	//$log_file_name 此log顯示名稱
				$log_file_noting =  next($log_file_arr);	//""
				$log_file_file =  next($log_file_arr);	//$log_file_file 此log檔案名稱
				
				$log_src="ACL DATA/$log_file_file";	//$log_src 路徑
				
			}

		}//foreach ($data_arr as $line => $each_line) {
	}//if (!empty($data_arr)) {


}



//假如有讀到log檔名
if(isset($log_file_name)){


	$LOG_project_file="$log_file_name.LOG";
	$LOG_project_src="ACL DATA/$LOG_project_file";

	$LOG_project_file_fp=fopen($LOG_project_src , "r");
	$all_size=filesize($LOG_project_src);
	$all=fread($LOG_project_file_fp, $all_size);
	$all=mb_convert_encoding($all, 'UTF-8', 'UTF-16LE');

	
		//刪除log在資料庫中所有資料
		$sql_delete_logfile = "DELETE FROM log_all_file";
		

		try {
			$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$stmt = $db_conn->exec($sql_delete_logfile);
			} catch (PDOException $e) {						
			die("刪除資料表資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
			}		

		$sql_delete_opentable = "DELETE FROM log_open_table";
		
		try {
			$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$stmt = $db_conn->exec($sql_delete_opentable);
			} catch (PDOException $e) {						
			die("刪除資料表資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
			}		

		$sql_delete_tableindex = "DELETE FROM log_table_index";
		
		try {
			$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$stmt = $db_conn->exec($sql_delete_tableindex);
			} catch (PDOException $e) {						
			die("刪除資料表資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
			}		

		$sql_delete_tableclose = "DELETE FROM log_table_close";
		
		try {
			$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$stmt = $db_conn->exec($sql_delete_tableclose);
			} catch (PDOException $e) {						
			die("刪除資料表資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
			}		

		
		$all_log_line = array();
		
	

	$data_arr=explode("\n", $all);	// $data_arr : LOG檔分成一行一行

	if (!empty($data_arr)) {

		//echo "enter if";
		
		$nowline = 0;
	

		$log_file_num = 0;
		//每行找
		foreach ($data_arr as $line => $each_line) {

			$nowline = $nowline +1;	//記錄log檔目前行數
		
			
			$LOG_big_chk = strpos($each_line,"@@");	

			//假如有找到"@@"就進入，狀態第一層
			if ($LOG_big_chk!==false) {	
				$log_file_num = $log_file_num +1;
			
				$LOG_big_arr=explode(" Opened at ", $each_line);	//用Opened at分前後句

				$LOG_big_time_all = next($LOG_big_arr);	//$LOG_big_time_all : Opened at 的後半段字

				$LOG_big_time_arr = explode(" ", $LOG_big_time_all);	//$LOG_big_time_arr :Opened at 後半段字分成陣列

				$LOG_big_time_time = next($LOG_big_time_arr);	//$LOG_big_time_time :時間
				$LOG_big_time_on = next($LOG_big_time_arr);		//$LOG_big_time_on :on
				$LOG_big_time_day = next($LOG_big_time_arr);	//$LOG_big_time_day :日期

				
				$all_log_line[$log_file_num]["line_word"] = $each_line;
				$all_log_line[$log_file_num]["time"] = $LOG_big_time_time;
				

				$all_log_line[$log_file_num]["day"] = $LOG_big_time_day;
				
				$all_log_line["number"] = $log_file_num;
						
				$LOG_second_chk=true;

				$open_table_num = 0;


				$log_show_tree_word = $LOG_big_time_time." on ".$LOG_big_time_day;


				//insert到資料庫

				$sql_insert_logallfile = "INSERT INTO log_all_file(log_file_id, time, day, show_tree_word) VALUES('$log_file_num', '$LOG_big_time_time', '$LOG_big_time_day', '$log_show_tree_word') ";
			

				try {
					$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$stmt = $db_conn->exec($sql_insert_logallfile);
					} catch (PDOException $e) {						
					die("新增資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
					}		


						//時間判斷
						$log_file_open_month = substr($all_log_line[$log_file_num]["day"],0,2);
						$log_file_open_day = substr($all_log_line[$log_file_num]["day"],3,2);
						$log_file_open_year = substr($all_log_line[$log_file_num]["day"],6,4);

						$log_file_open_date = $log_file_open_year.'-'.$log_file_open_month.'-'.$log_file_open_day;

						$nowyear = date("Y");    //取得今年
	   
	   					$nowmonth = date("m");       //取得這個月
	   
	   					$nowday = date("d");        //取得今天
	   					
	   					$now_date = $nowyear.'-'.$nowmonth.'-'.$nowday;

	   					
	   					$Date_List_1=explode("-",$now_date);
						$Date_List_2=explode("-",$log_file_open_date);
						$d1=mktime(0,0,0,$Date_List_1[1],$Date_List_1[2],$Date_List_1[0]);
						$d2=mktime(0,0,0,$Date_List_2[1],$Date_List_2[2],$Date_List_2[0]);
						$Daysago=round(($d1-$d2)/3600/24);
	

						$all_log_line[$log_file_num]["Daysago"] = $Daysago;

				

				continue;
			}	
			//假如有找到"@@"就進入結束，$LOG_second_chk 設成true，讀下一行

			//假如$LOG_second_chk 為true，狀態第二層，就做
			if($LOG_second_chk == true){

				$LOG_detail_oneline_chk = strpos($each_line,"@ OPEN");	

				//假如有找到"@ OPEN"，就做
				if($LOG_detail_oneline_chk!==false){

					$open_table_num = $open_table_num +1;
		

					$LOG_second_arr=explode("OPEN", $each_line);
					
					$LOG_second_tablename = next($LOG_second_arr);


					$show_tree_word_arr=explode("@", $each_line);
					$show_tree_word = next($show_tree_word_arr);


					$LOG_detail_tableopen_chk = true;
					$LOG_second_chk = false;
					$now_tabdet_line = 0;

				
					$all_log_line[$log_file_num]["opentable"][$open_table_num]["tablename"] = $LOG_second_tablename;
			
					$all_log_line[$log_file_num]["table_number"] = $open_table_num;
				

					//insert到資料庫

					$sql_insert_logopentable = "INSERT INTO log_open_table(log_file_id, table_open_id, table_name, show_tree_word) VALUES('$log_file_num', '$open_table_num', '$LOG_second_tablename', '$show_tree_word') ";

					try {
					$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$stmt = $db_conn->exec($sql_insert_logopentable);
					} catch (PDOException $e) {						
					die("新增資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
					}		


					$index_on_num = 0; 
					$close_number = 0;

			
				}
				
				//假如有找到"@ OPEN"結束，$LOG_detail_chk = true，$LOG_second_chk = false，讀下一行

				$LOG_index_output_chk = strpos($each_line,"@ INDEX ON Address");

				//
				if($LOG_index_output_chk !== false){

					$index_on_num = $index_on_num+1;
					$LOG_index_treeshow = substr($each_line,2);
		

					$all_log_line[$log_file_num]["opentable"][$open_table_num]["indexoutput"][$index_on_num]["index_show_word"] = $LOG_index_treeshow;
					//echo $all_log_line[$log_file_num]["opentable"][$open_table_num]["indexoutput"][$index_on_num]["index_show_word"];
					//echo '<br>';
					$all_log_line[$log_file_num]["opentable"][$open_table_num]["index_on_num"] = $index_on_num;

					//insert到資料庫

					$sql_insert_tableindex = "INSERT INTO log_table_index(log_file_id, table_open_id, table_open_index_id, show_tree_word) VALUES('$log_file_num', '$open_table_num', '$index_on_num', '$LOG_index_treeshow') ";
					
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_insert_tableindex);
						} catch (PDOException $e) {						
						die("新增資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}		
						
						$now_inddet_line = 0;
						$table_index_detail_chk = true;
						$LOG_second_chk = false;
						
				}

				//
				$LOG_close_chk =strpos($each_line,"@ CLOSE");

				if($LOG_close_chk !== false){

					$close_number = 1;

					$LOG_close_treeshow = substr($each_line,2);
				
					$all_log_line[$log_file_num]["opentable"][$open_table_num]["clsoe_show_word"] = $LOG_close_treeshow;
					$all_log_line[$log_file_num]["opentable"][$open_table_num]["close_num"] = $close_number;

					//insert到資料庫

					$sql_insert_tableclose = "INSERT INTO log_table_close(log_file_id, table_open_id, table_open_close_id, show_tree_word) VALUES('$log_file_num', '$open_table_num', '$close_number', '$LOG_close_treeshow') ";
				
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_insert_tableclose);
						} catch (PDOException $e) {						
						die("新增資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}

						$table_close_detail_chk = true;
						$LOG_second_chk = false;


				}

				continue;

			}

			//若$LOG_detail_chk== true，進入第三層
			if($LOG_detail_tableopen_chk == true){

				$now_tabdet_line = $now_tabdet_line+1;
	

				switch ($now_tabdet_line) {
					case '1':
						$now_database_line = "show_detail_firstline";
						break;
					case '2':
						$now_database_line = "show_detail_secondline";
						break;
					case '3':
						$now_database_line = "show_detail_thirdline";
						break;
					default:
						
						break;
				}

				if($now_tabdet_line ==3){
					$LOG_second_chk = true;
					$LOG_detail_tableopen_chk = false;
				}


				$sql_update_tabledetail = "UPDATE log_open_table SET $now_database_line='$each_line' where log_file_id='$log_file_num' and table_open_id='$open_table_num'";
				
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_update_tabledetail);
						} catch (PDOException $e) {						
						die("更新資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}


				$LOG_detail_oneword_chk = strpos($each_line,"fields activated");
				
				if($LOG_detail_oneword_chk!==false){

						$all_log_line[$log_file_num]["opentable"][$open_table_num]["openornot"] = "yes";
						
					$sql_update_tableopenornot = "UPDATE log_open_table SET openornot='1' where log_file_id='$log_file_num' and table_open_id='$open_table_num'";
	
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_update_tableopenornot);
						} catch (PDOException $e) {						
						die("更新資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}


				}

				$LOG_detail_oneword_chk = strpos($each_line,"Command Failed");
				if($LOG_detail_oneword_chk!==false){

						$all_log_line[$log_file_num]["opentable"][$open_table_num]["openornot"] = "no";
		

					$sql_update_tableopenornot = "UPDATE log_open_table SET openornot='2' where log_file_id='$log_file_num' and table_open_id='$open_table_num'";

					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_update_tableopenornot);
						} catch (PDOException $e) {						
						die("更新資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}
				}

				continue;
			}


			if($table_close_detail_chk == true){

				$sql_update_closedetail = "UPDATE log_table_close SET show_detail_word='$each_line' where log_file_id='$log_file_num' and table_open_id='$open_table_num' and table_open_close_id='$close_number'";
				
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_update_closedetail);
						} catch (PDOException $e) {						
						die("更新資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}

						$table_close_detail_chk = false;
						$LOG_second_chk = true;
				continue;
			}

			if($table_index_detail_chk == true){

				$now_inddet_line = $now_inddet_line+1;
			
				switch ($now_inddet_line) {
					case '1':
						$now_database_line = "show_detail_firstline";
						break;
					case '2':
						$now_database_line = "show_detail_secondline";
						break;
					case '3':
						$now_database_line = "show_detail_thirdline";
						break;
					default:
						
						break;
				}


				$sql_update_indexdetail = "UPDATE log_table_index SET $now_database_line='$each_line' where log_file_id='$log_file_num' and table_open_id='$open_table_num' and table_open_index_id ='$index_on_num'";
			
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($sql_update_indexdetail);
						} catch (PDOException $e) {						
						die("更新資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}

				if($now_inddet_line ==3){
					$LOG_second_chk = true;
					$table_index_detail_chk = false;


					continue;
				}

			}

		}

	}


echo '<div id="tree" class="sampletree">';
	    echo '<ul id="treeData" styleXXX="display: none;">';
	      	echo '<li class="log_time_img  expanded" data-test="test" ><span class="folder">Project History</span>';
		        echo '<ul>';		          

		        
		        	$current_daysago_status = 1;
					for($i=1 ; $i <= $all_log_line["number"] ; $i++) {	


						//差入時間判斷

							$daysago = $all_log_line[$i]["Daysago"];


							if($current_daysago_status == 1){
								echo '<li class="log_time_img" data-test="test" ><span class="folder">Over 30 Days</span>';
								echo '<ul>';
								$current_daysago_status = 2;
							}
							if($daysago < 30 && $current_daysago_status == 2){
								echo '</ul>';
								echo '</li>';
								echo '<li class="log_time_img" data-test="test" ><span class="folder">15-30 Days Ago</span>';
								echo '<ul>';
								$current_daysago_status = 3;
							}
							if($daysago < 14 && $current_daysago_status == 3){
								echo '</ul>';
								echo '</li>';
								echo '<li class="log_time_img" data-test="test" ><span class="folder">8-14 Days Ago</span>';
								echo '<ul>';
								$current_daysago_status = 4;
							}
							if($daysago < 7 && $current_daysago_status == 4){
								echo '</ul>';
								echo '</li>';
								echo '<li class="log_time_img expanded" data-test="test" ><span class="folder">Last 7 Days</span>';
								echo '<ul>';
								$current_daysago_status = 5;
							}


						
						echo '<li class="log_open_table_img item" data-test="test" ><span class="folder">'. $all_log_line[$i]["time"].' on '.$all_log_line[$i]["day"].'</span>';
						
						echo '<ul>';

							

						$this_logfile_table_number = $all_log_line[$i]["table_number"];


						for($j=1 ; $j <= $this_logfile_table_number ; $j++) {

							if($all_log_line[$i]["opentable"][$j]["openornot"] == "yes"){
								echo '<li class="log_open_ok_img" data-i="'.$i.'" data-j="'.$j.'">';
							}
							else{
								echo '<li class="log_open_not_img" data-i="'.$i.'" data-j="'.$j.'">';
							}
							
							echo '<span class="folder">OPEN '.$all_log_line[$i]["opentable"][$j]["tablename"].'  '.$all_log_line[$i]["opentable"][$j]["openornot"].'</span>';

							$this_table_index_number = $all_log_line[$i]["opentable"][$j]["index_on_num"];

							echo '<ul>';
							for($k=1 ; $k<=$this_table_index_number ; $k++){

								echo'<li class="expanded" data-i="'.$i.'" data-j="'.$j.'" data-k="'.$k.'">'.$all_log_line[$i]["opentable"][$j]["indexoutput"][$k]["index_show_word"].'</li>';
							}

							if($all_log_line[$i]["opentable"][$j]["close_num"] != 0){

							echo '<li class="log_open_ok_img" data-i="'.$i.'" data-j="'.$j.'" data-close="1" ><sapn class="expanded">'.$all_log_line[$i]["opentable"][$j]["clsoe_show_word"].'</span></li>';

							}
							echo '</ul>';
						}

						echo '</li>';
						echo '</ul>';
						
					}
					echo '</ul>';
					echo '</li>';
					echo '</li>';
					
		        echo '</ul>';
			echo '</li>';
	    echo '</ul>';
  	echo '</div>';
  	

}

?>



</body>
</html>