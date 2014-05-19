<?php

include "include/config.php";
include "include/utility.php";








$ALC_project_file="Metaphor_Employee_Data.ACL";
$ALC_project_src="ACL DATA/$ALC_project_file";

$ALC_project_file_fp=fopen($ALC_project_src , "r");
$all_size=filesize($ALC_project_src);
$all=fread($ALC_project_file_fp, $all_size);
$all=mb_convert_encoding($all, 'UTF-8', 'UTF-16LE');

$data_arr=explode("\n", $all);

if (!empty($data_arr)) {
	foreach ($data_arr as $line => $each_line) {
		// get table name
		$table_chk = strpos($each_line,"^LAYOUT");				
		if ($table_chk!==false) {			
			// echo $line. "   ". $each_line."<br>";
			$table_arr=explode(" ", $each_line);
			$table_arr = array_diff($table_arr, array(null,'null','',' '));
			$table_name =  next($table_arr);
			
			$get_table_detail_chk=true;
			$table_length =  next($table_arr);

			// get table file  length od each line
			$table_description[$table_name]["line_length"] = $table_length;
			$table_length_count=0;
			$table_col_arr=array();
			continue;
		}
		// get table detail information
		if ($get_table_detail_chk) {
			$table_detail_arr=explode(" ", $each_line);
			$table_detail_arr = array_diff($table_detail_arr, array(null,'null','',' '));
			$col_name=current($table_detail_arr);
			$col_type=next($table_detail_arr);
			$col_index = next($table_detail_arr);
			$col_length = next($table_detail_arr);
			$detail_index = $col_index+$col_length-1;




			$table_description[$table_name]["col_name"][]=$col_name;
			$table_description[$table_name]["col_start"][]=$col_index;
			$table_description[$table_name]["col_length"][]=$col_length;
			$table_description[$table_name]["col_type"][]=$col_type;
			$decimal_dot="";
			switch ($col_type) {
				case 'NUMERIC':
					$sql_type="INT";
					$decimal_dot = next($table_detail_arr);
				break;

				case 'DATETIME':
					$sql_type="varchar(10)";
				break;

				case 'ACL':
					$sql_type="double";
					$decimal_dot = next($table_detail_arr);
				break;
				// VARCHAR
				default:
					$sql_type="varchar($col_length)";
				break;
			}		
			
			// digit after decimal 
			$table_description[$table_name]["decimal_dot"][]=$decimal_dot;
			// col sql type
			$table_col_arr[]="`$col_name` $sql_type".' NULL';
			// reach the last column
			if ($detail_index==$table_length ) {				
				$get_table_detail_chk=false;				
				if (!empty($table_col_arr)) {
					$table_col_sql=implode(",", $table_col_arr);					
				}

				// $res = $db_conn->query("SHOW TABLES LIKE '".$table_name."';SELECT FOUND_ROWS();");
				// echo "SHOW TABLES LIKE '".$table_name."';SELECT FOUND_ROWS();";
				// $num_rows = $res->fetchColumn();
				// echo $num_rows."<br>";
				// die;
				
				// echo mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$table_name."'"))."<br>";
				
				// check table is existing or not
				if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$table_name."'"))==1) {
					$table_description[$table_name]["table_chk"]=true;
				}else{
					$table_description[$table_name]["table_chk"]=false;
					// create table sql
					$create_table_sql="CREATE TABLE IF NOT EXISTS $table_name (";
					$create_table_sql.=$table_col_sql;				
					$create_table_sql.=",key_id  INT  AUTO_INCREMENT  NOT NULL ,INDEX (key_id)";
					$create_table_sql.=")";				
					// mysql_query($create_table_sql) or die("資料表新增失敗");
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($create_table_sql);
					} catch (PDOException $e) {						
						die("新增資料表時出現錯誤   錯誤如下 <br>" . $e->getMessage());
					}			
				}				
			}
		}


				
		// get insert table file
		$table_file_chk = strpos($each_line,"^FORMAT");				
		if ($table_file_chk!==false) {						
			$table_file_arr=explode(" ", $each_line);
			$table_file_arr = array_diff($table_file_arr, array(null,'null','',' '));
			$table_file_arr=array_values($table_file_arr);
			$insert_table = $table_file_arr[1];
			$table_file_name= str_replace('"', "", $table_file_arr[4]);
			$each_ling_length=$table_description[$insert_table]["line_length"]+1;						
			$col_name_arr =  $table_description[$insert_table]["col_name"];
			$col_width_arr =  $table_description[$insert_table]["col_length"];
			$col_start_arr =  $table_description[$insert_table]["col_start"];
			$col_type_arr =  $table_description[$insert_table]["col_type"];
			$col_decimal_dot_arr =  $table_description[$insert_table]["decimal_dot"];
			$arr_count = count($col_start_arr)-1;

			// T:table had been created F:first created
			$table_chk = $table_description[$insert_table]["table_chk"];
			// first created , so has to insert data
			if ($table_chk===false) {
				// open table file
				$insert_table_file_src="ACL DATA/table_file/$table_file_name";
				$insert_table_file = fopen($insert_table_file_src, "rb");
				$insert_table_file_size = filesize($insert_table_file_src);

				while(!feof($insert_table_file)){
					// get fil data by it's start point and length  define by above
	 				$file_line=fgets($insert_table_file , $each_ling_length);
	 				$file_line_len = strlen($file_line)+1;
	 				
	 				// at the end of file don't inert 0 data 				
	 				if ($file_line_len<$each_ling_length) { 					
	 					continue;
	 				}
	 				// echo $file_line."<br>";
	 				if (!empty($col_start_arr)) {
	 					$insert_table_arr=array();
	 					foreach ($col_start_arr as $col => $col_start) { 						
	 						$data_type = $col_type_arr[$col];
	 						$decimal_dot = $col_decimal_dot_arr[$col];
	 						$start=$col_start-1;

	 						// get data
	 						$line_data =  substr($file_line, $start , $col_width_arr[$col]); 						 						
	 						// ACL data type has to insert decimal dot
	 						if ($data_type=="ACL") { 							 							
	 							$line_data = bin2hex ($line_data);
	 							$line_data = substr($line_data, -22);
	 							$line_data = (float)$line_data; 
	 							// insert decimal dot
	 							$line_data =insert_dot($line_data ,$decimal_dot); 							
	 						}
	 						$line_data = trim($line_data);
	 						$line_data = mysql_real_escape_string($line_data);

	 						$insert_table_arr[]=trim($line_data);
	 					}
	 					// insert table sql
	 					$insert_sql  = "INSERT INTO $insert_table";
	 					$insert_sql .= " (".implode(",", ($col_name_arr)).")";
	 					$insert_sql .= " VALUES ('".implode("', '", $insert_table_arr)."') ";
	 					try {
							$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
							$stmt = $db_conn->exec($insert_sql);
						} catch (PDOException $e) {						
							die("輸入資料時出現錯誤   錯誤如下 <br>" . $e->getMessage());
						}		
	 				}
				}
			}
		}
	}//foreach ($data_arr as $line => $each_line) {
}//if (!empty($data_arr)) {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
A:link{color:#00519C; font-size:10pt; text-decoration:none}
A:visited{color:#00519C; font-size:10pt; text-decoration:none}
A:hover{color:#cc0000; font-size:10pt; text-decoration:none}
A:active{color:red; font-size:10pt; text-decoration:none}
td {font-size:12pt; font-family:Arial, Helvetica, sans-serif}
</style>
<head>
	<title></title>
	<!-- fancytree css  skin -->
	<link href="lib/fancytree/src/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
	<!-- custom.css -->
	<!-- <link href="lib/fancytree/demo/skin-custom/custom.css" rel="stylesheet" type="text/css" > -->
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
	span.fancytree-node.table_img > span.fancytree-icon {
	  /*background-image: url("fancytree/demo/skin-custom/customDoc2.gif");*/
	  background-image: url("img/acl_treeview/table_img.png");
	  background-position: 0 0;
	}
	span.fancytree-node.table_img_active > span.fancytree-icon {	  
	  background-image: url("img/acl_treeview/table_img_active.png");
	  background-position: 0 0;
	}
	</style>
	

	<script type="text/javascript">
	$(document).ready(function() {
	  	$("#tree").fancytree();
	  	// {
	  // 		activate: function(event, data){
			// 	var node = data.node;
			// 	// alert(node.data.test);

			// 	// $.ui.fancytree.debug("activate: event=", event, ", data=", data);
			// 	// if(!$.isEmptyObject(node.data)){
			// 	// 	alert("custom node data: " + JSON.stringify(node.data));
			// 	// }
			// },


	  	// }
	  	// );


	  	$(".item").click(function(event) {
	  		$(this).removeClass('table_img').addClass('table_img_active');
	  	});
	  	$(".item").dblclick(function(event) {
	  		// alert();
	  		var tablename = $(this).find(".fancytree-title").html();
	  		// $('#file_content', window.parent.document).prop("src" , "file_content.php?table_name="+tablename);
	  		$('#file_content', window.parent.document).prop("src" , "jqgrid.php?table_name="+tablename);
	  	});

	});
		
	</script>
	
</head>
<body>

	<div id="tree">
	    <ul>
	      	<li class="folder expanded"><?echo $ALC_project_file;?>
		        <ul>		          
		        <?php
					foreach ($table_description as $table_name => $value) {						
						?>
							<li class="table_img item"  data-test="test" ><span class='folder'><?echo $table_name?></span></li>
						<?
					}
				?>	
		        </ul>
			</li>
	    </ul>
  	</div>
<!--   	<li class="folder expanded">Override CSS style per node
	        <ul>
	          <li class="custom1">class="custom1": Additional classes are copied over to outer &lt;span>
	          <li class="folder custom1">class="custom1": Additional classes are copied over to outer &lt;span>
	        </ul>

	      <li class="folder expanded">Use 'data-NAME' attributes to define additional data
	        <ul>
	          <li data-icon="customDoc1.gif">Node with standard CSS, but custom icon
	          <li class="folder" data-icon="folder_docs.gif">Folder with standard CSS but custom icon
	          <li data-iconclass="ui-icon ui-icon-heart">'iconclass' is directly added to the image &lt;span>, so jQuery stock icons may be used
	        </ul>

	      <li class="folder expanded">Use 'data-json' attribute to define additional data
	        <ul>
	          <li data-json='{"icon": "customDoc1.gif"}'>Node with standard CSS, but custom 'data-icon'
	        </ul>

	      <li class="folder expanded">Use render callback
	        <ul>
	          <li id="renderNode1" data-cstrender="true">Node hat will be handled by 'render' calback
	        </ul>
 -->




</body>
</html>


