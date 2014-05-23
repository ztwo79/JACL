<meta charset="utf-8"/>
<?php
session_start();
include "include/config.php";
include "include/utility.php";


$_SESSION["sUid"]=1;

// 取得sUid
$sUid=$_SESSION["sUid"];
$ALC_project_file="Metaphor_Employee_Data.ACL";
$ALC_project_src="ACL DATA/$ALC_project_file";



// 先讀取使用者是否已經有此專案
try {
	$sql = "SELECT * from directory_structure where sUid='$sUid' and parent_id='0' and type='root_folder' and ACL_file='$ALC_project_file'";
	$stmt = $db_conn->prepare($sql);
	$exe=$stmt->execute();
	if ($exe===false) {
		throw new PDOException('取得專案目錄資料夾出現錯誤');
	}
	$root_folder_rs = $stmt->fetch();
	$root_folder_id = $root_folder_rs["d_id"];
	
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}

$root_folder->name="Metaphor_Employee_Data.ACL";

// 有建立過此專案
if (!empty($root_folder_id)) {
	// 取得第一層的資料
	try {
		$sql = "SELECT * from directory_structure where sUid='$sUid' and parent_id='$root_folder_id'";
		$stmt = $db_conn->prepare($sql);
		$exe = $stmt->execute();
		if ($exe===false) {
			throw new PDOException('取得專案目錄第一層資料夾出現錯誤');
		}
	} catch (PDOException $e) {
		$error = $db_conn->errorInfo();
		echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
		echo "錯誤行數: " . $e->getline()."<br>";
		// echo "錯誤內容: " . $error[2];
		die();
	}
	while($row = $stmt->fetch()) {
		$d_id = $row["d_id"];
		$name = $row["name"];
		$content_table = $row["content_table"];
		$type = $row["type"];
		$directory_arr[$d_id]["name"]=$name;
		$directory_arr[$d_id]["content_table"]=$content_table;
		$directory_arr[$d_id]["type"]=$type;
		// 資料夾需確認資料夾內有無資料
		if ($type==="folder") {
			try {
				$sql = "SELECT COUNT(*) as folder_chk from directory_structure where sUid='$sUid' and parent_id='$d_id'";
				$folder_chk_stmt = $db_conn->prepare($sql);
				$exe = $folder_chk_stmt->execute();
				if ($exe===false) {
					throw new PDOException('取得專案目錄資料夾出現錯誤');
				}
			} catch (PDOException $e) {
				$error = $db_conn->errorInfo();
				echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
				echo "錯誤行數: " . $e->getline()."<br>";
				// echo "錯誤內容: " . $error[2];
				die();
			}
			$folder_chk  = $folder_chk_stmt->fetchColumn();
			$directory_arr[$d_id]["folder_chk"]=$folder_chk;
		}
	}
}


// 使用者還沒有該專案 建立該專案
if (empty($root_folder_id)) {

	// 讀取專案檔
	$ALC_project_file_fp=fopen($ALC_project_src , "r");
	$all_size=filesize($ALC_project_src);
	$all=fread($ALC_project_file_fp, $all_size);
	$all=mb_convert_encoding($all, 'UTF-8', 'UTF-16LE');



	// 把專案檔輸入進資料庫
	$insert_arr = array(
		"sUid"          => $sUid,
		"root_id"      => 0,				
		"parent_id"      => 0,				
		"type"      => "root_folder",		
		"ACL_file"      => $ALC_project_file
	);			
	try {
		$insert_sql  = "INSERT INTO directory_structure";
		$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
		$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
		$stmt = $db_conn->exec($insert_sql);
		$root_folder_id = $db_conn->lastInsertId();
		if ($stmt===false) {
			throw new PDOException('新增專案目錄資料夾錯誤');
		}
	} catch (PDOException $e) {
		$error = $db_conn->errorInfo();
		echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
		echo "錯誤行數: " . $e->getline()."<br>";
		// echo "錯誤內容: " . $error[2];
		die();
	}

	$data_arr=explode("\n", $all);

	if (!empty($data_arr)) {
		foreach ($data_arr as $line => $each_line) {
			// 取得資料表名稱
			$table_chk = strpos($each_line,"^LAYOUT");				
			if ($table_chk!==false) {			
				// echo $line. "   ". $each_line."<br>";
				$table_arr=explode(" ", $each_line);
				$table_arr = array_diff($table_arr, array(null,'null','',' '));
				$table_name =  next($table_arr);
				
				// 在資料庫裡面的 資料表名稱
				$table_db_name = "table".uniqid();
				$table_description[$table_name]["table_db_name"] = $table_db_name;

				$get_table_detail_chk=true;
				$table_length =  next($table_arr);

				// 取得 資料表 長度 內容格式
				$table_description[$table_name]["line_length"] = $table_length;
				$table_length_count=0;
				$table_col_arr=array();
				continue;
			}

			// 取得資料表內容 並輸入進資料庫
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
				
				// 小數點後幾位
				$table_description[$table_name]["decimal_dot"][]=$decimal_dot;
				// 存入資料庫的資料型態
				$table_col_arr[]="`$col_name` $sql_type".' NULL';
				// 取得最後一個col 
				if ($detail_index==$table_length ) {				
					$get_table_detail_chk=false;				
					if (!empty($table_col_arr)) {
						$table_col_sql=implode(",", $table_col_arr);					
					}
					
					// create table sql
					$create_table_sql="CREATE TABLE $table_db_name (";
					$create_table_sql.=$table_col_sql;				
					$create_table_sql.=",key_id  INT  AUTO_INCREMENT  NOT NULL ,INDEX (key_id)";
					$create_table_sql.=")";
					try {
						$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						$stmt = $db_conn->exec($create_table_sql);
					} catch (PDOException $e) {						
						echo "新增資料表時出現錯誤   錯誤如下 <br>" . $e->getMessage();
						echo "錯誤行數: " . $e->getline()."<br>";
						die();
					}			
								
				}
			}


					
			// 取得需要輸入資料表的內容
			$table_file_chk = strpos($each_line,"^FORMAT");				
			if ($table_file_chk!==false) {						
				$table_file_arr=explode(" ", $each_line);
				$table_file_arr = array_diff($table_file_arr, array(null,'null','',' '));
				$table_file_arr=array_values($table_file_arr);
				$insert_table = $table_file_arr[1];
				$table_file_name= str_replace('"', "", $table_file_arr[4]);
				// 資料庫內表的名稱
				$table_db_name=$table_description[$insert_table]["table_db_name"];						

				$each_ling_length=$table_description[$insert_table]["line_length"]+1;
				$col_name_arr =  $table_description[$insert_table]["col_name"];
				$col_width_arr =  $table_description[$insert_table]["col_length"];
				$col_start_arr =  $table_description[$insert_table]["col_start"];
				$col_type_arr =  $table_description[$insert_table]["col_type"];
				$col_decimal_dot_arr =  $table_description[$insert_table]["decimal_dot"];
				$arr_count = count($col_start_arr)-1;

									
				// 輸入資料夾進入資料庫
				$insert_arr = array(
					"sUid"          => $sUid,
					"root_id"      => $root_folder_id,
					"parent_id"      => $root_folder_id,
					"name"       => $insert_table,
					"content_table" => $table_db_name,
					"type"       => "table",
					"ACL_file"       => "$table_file_name"
				);			
				$insert_sql  = "INSERT INTO directory_structure";
				$insert_sql .= " (".implode(",", array_keys($insert_arr)).")";
				$insert_sql .= " VALUES ('".implode("', '", $insert_arr)."') ";
				$stmt = $db_conn->exec($insert_sql);
				try {
					if ($stmt===false) {
						throw new PDOException('新增專案目錄資料夾錯誤');
					}
				} catch (PDOException $e) {
					$error = $db_conn->errorInfo();
					echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
					echo "錯誤行數: " . $e->getline()."<br>";
					// echo "錯誤內容: " . $error[2];
					die();
				}
				$d_id = $db_conn->lastInsertId(); 
				// 放入第一層的資料 展示
				$directory_arr[$d_id]["name"]=$insert_table;
				$directory_arr[$d_id]["content_table"]=$table_db_name;
				$directory_arr[$d_id]["type"]="table";
				
		
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
	 					$insert_sql  = "INSERT INTO $table_db_name";
	 					$insert_sql .= " (".implode(",", ($col_name_arr)).")";
	 					$insert_sql .= " VALUES ('".implode("', '", $insert_table_arr)."') ";
	 					try {
							$db_conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
							$stmt = $db_conn->exec($insert_sql);
						} catch (PDOException $e) {						
							echo "輸入資料時出現錯誤   錯誤如下 <br>" . $e->getMessage();
							echo "錯誤行數: " . $e->getline()."<br>";
							die();
						}		
	 				}
				}
			}
		}//foreach ($data_arr as $line => $each_line) {
	}//if (!empty($data_arr)) {

}




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
	span.fancytree-node.script_img > span.fancytree-icon {
	  /*background-image: url("fancytree/demo/skin-custom/customDoc2.gif");*/
	  background-image: url("img/acl_treeview/script.png");
	  background-position: 0 0;
	}
	span.fancytree-node.table_img_active > span.fancytree-icon {	  
	  background-image: url("img/acl_treeview/table_img_active.png");
	  background-position: 0 0;
	}
	</style>
	

	<script type="text/javascript">
	$(document).ready(function() {

		// 記錄動態的 table 節點ID
		var dynamic_table_node_id;
		// 記錄動態的 script 節點 ID
		var dynamic_script_node_id;

	  	$("#tree").fancytree({
			// 設定圖片路徑
	  		imagePath: "img/acl_treeview/",
	  		clickFolderMode: 3, // 1:activate, 2:expand, 3:activate and expand, 4:activate (dblclick expands)
	  		
	  		
	  		// 離開一個節點的時候更換 icon 
			blur: function(event, data) {
				var node = data.node;
				var nodeid = node.data.nodeid;
			},

	  		click: function(event, data) {
		    	var node = data.node;
		    	var tablename = node.data.tablename;
		    	var type = node.data.type;
		    	var test = node.data.test;
		    	
		    	// alert(node.data.dbtable);
		    	// alert($(this).html());

		    	
				if (type==="table") {
					// 換icon
					// $(this).find("."+tablename).removeClass('table_img').addClass('table_img_active');
					// alert(node.data.icon);
					// node.data.icon="table_img_active.png";
					// alert(node.data.icon);

					// var $span = $(node.span);
				  //       $span.find("> span.fancytree-icon").css({
				  //           backgroundImage: "url(img/acl_treeview/table_img_active.png)",
				  //           backgroundPosition: "0 0"
				  //       });
				};
				// 打開folder
			    if (type==="folder") {
					var openstatus = node.data.openstatus;
					var sub_chk = node.data.subchk;

					// folder 還沒被開過且該資料夾內有資料
					if (openstatus==="unopen" & sub_chk==="has_sub") {
						var d_id = node.data.did;
						// alert(d_id);

						// 切換為已開啟
						node.data.openstatus="";
						// 取得資料夾內的內容
						$.ajax({ url: 'directory_ajax.php' ,
							cache: false,
							dataType: 'html',// <== 設定傳送格式
							type:'GET',// <== 設定傳值方式
							data: { action: "get_subdirectory" , d_id:d_id },// <== 傳GET的變數，此例是gsn
							error: function(xhr) { alert('取得資料夾內容 Ajax request 發生錯誤'+ xhr); },
							success: function(response) {
								// alert(response);
								var subdirectory_arr=$.parseJSON(response);

								// 取得每一個物件
								$.each(subdirectory_arr, function(index, obj) {
									var  obj_name = obj.name;
									var  obj_type = obj.type;
									var  obj_d_id = obj.d_id;
									// 確認資料夾是否有值
									var  obj_folder_inside_count = obj.folder_inside_count;

									switch (obj_type) {  
										// 資料夾
									    case "folder":  
									        // 替當下的節點下增加檔案
											var childNode = node.addChildren({
									        	title: obj_name,
									        	// tooltip: "This folder and all child nodes were added programmatically.",
									        	folder: true,
									        	openstatus:"unopen",
									        	type:obj_type,
									        	did: obj_d_id,
										    });
										    
										    // 該子資料夾內有還有資料
										    if (obj_folder_inside_count>0) {
										    	childNode.data.subchk="has_sub";
										    	// alert("123");
										    	childNode.addChildren({
										        	title: "useless",
										        	key:"useless_"+obj_d_id,
										    	});		
										    };
								        break;  
								
										// table
									    case "table":  
									        // 替當下的節點下增加檔案
											var childNode = node.addChildren({
									        	title: obj_name,
									        	// tooltip: "This folder and all child nodes were added programmatically.",
									        	// icon: "table_img.png",
									        	type:obj_type,
									        	nodeid:"nodeid"+d_id,
									        	key:"nodeid"+d_id,
									        	extraClasses:'table_img',
										    });
								        break;  

								        // table
									    case "script":  
									        // 替當下的節點下增加檔案
											var childNode = node.addChildren({
									        	title: obj_name,
									        	type:obj_type,
									        	nodeid:"nodeid"+d_id,
									        	key:"nodeid"+d_id,
									        	// tooltip: "This folder and all child nodes were added programmatically.",
									        	// icon: "table_img.png",
									        	extraClasses:'script_img',
										    });
								        break;  
									    
									    default:  
									        
								        break;  
									};  


								});
								// 使用d_id 取得節點 並刪掉不需要的節點
								$("#tree").fancytree("getTree").getNodeByKey("useless_"+d_id).remove();
							}
						});
					};
			    };
		    },
		    // 連點
			dblclick: function(event, data) {
				// 當下這個節點
				var node = data.node;
				// 取得table的名稱
				var tablename = node.data.tablename;
				// 該table的資料表
				var dbtable = node.data.dbtable;
				var type = node.data.type;
				// 動態的表
				var nodeid = node.data.nodeid;

				if (type==="table") {
					// 開啟動態table
			        $('#file_content', window.parent.document).get(0).contentWindow.set_dynamic_table(tablename , dbtable);	
			        // 把現在的table icon 換成開啟
					var $span = $(node.span);
			        $span.find("> span.fancytree-icon").css({
			            backgroundImage: "url(img/acl_treeview/table_img_active.png)",
			            backgroundPosition: "0 0"
			        });
			        // dynamic table有被開啟了
			        if (dynamic_table_node_id!==nodeid) {
						// 先確認dynamic_table_node_id不是 undefined
						if (dynamic_table_node_id !== undefined) {
							// 取得dynamic_table_node_id 的 id
							onblur_node = $("#tree").fancytree("getTree").getNodeByKey(dynamic_table_node_id);
							onblur_node_type =onblur_node.data.type;
							// alert(onblur_node.data.type);		
							// 相同type
							if (onblur_node_type===type) {
								// 把之前的 dynamic  table 改為為開啟
								var $span = $(onblur_node.span);
						        $span.find("> span.fancytree-icon").css({
						            backgroundImage: "url(img/acl_treeview/table_img.png)",
						            backgroundPosition: "0 0"
						        });
							};
						};
						dynamic_table_node_id=nodeid;
			        };
				};

				if (type==="script") {
					// 把現在的script icon 換成開啟
					var $span = $(node.span);
			        $span.find("> span.fancytree-icon").css({
			            backgroundImage: "url(img/acl_treeview/script_active.png)",
			            backgroundPosition: "0 0"
			        });

			        // dynamic script 有被開啟了
			        if (dynamic_script_node_id!==nodeid) {
						// 先確認dynamic_script_node_id不是 undefined
						if (dynamic_script_node_id !== undefined) {
							// 取得dynamic_script_node_id 的 id
							onblur_node = $("#tree").fancytree("getTree").getNodeByKey(dynamic_script_node_id);
							onblur_node_type =onblur_node.data.type;
							// alert(onblur_node.data.type);		
							// 相同type
							if (onblur_node_type===type) {
								// 把之前的 dynamic  table 改為為開啟
								var $span = $(onblur_node.span);
						        $span.find("> span.fancytree-icon").css({
						            backgroundImage: "url(img/acl_treeview/script.png)",
						            backgroundPosition: "0 0"
						        });
							};
						};
						dynamic_script_node_id=nodeid;
			        };
				};

			},
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
		        	if (!empty($directory_arr)) {
		        		foreach ($directory_arr as $d_id => $directory_data) {
		        			$name = $directory_data["name"];
		        			$table_db_name = $directory_data["content_table"];
		        			$folder_chk = $directory_data["folder_chk"];

		        			if ($directory_data["type"]=="folder" ) {
		        				$folder_data="";
		        				$sub_chk="";
		        				if ($folder_chk>0) {
		        					$folder_data='<ul><li id="useless_'.$d_id.'">useless</li></ul>';
		        					$sub_chk="has_sub";
		        				}
		        				// 多useless 為了讓資料夾看起來可以展開
		        				// echo '<li class="folder" data-type="folder" data-openstatus="unopen" data-did="'.$d_id.'" data-subchk="'.$sub_chk.'" >'.$name.$folder_data.'</li>';
		        				?>
		        				<li class="folder"  data-json='{"type": "folder" ,"openstatus": "unopen", "did":"<?echo $d_id;?>", "subchk": "<?echo $sub_chk;?>" }'  ><?echo $name.$folder_data;?></li>
								<?

		        			}elseif($directory_data["type"]=="table" ){
								// echo '<li class="table_img '.$name.'" data-dbtable="'.$table_db_name.'"  data-tablename="'.$name.'" data-type="table"><span class="folder">'.$name.'</span></li>';
								echo '<li class="table_img '.$name.'"  data-dbtable="'.$table_db_name.'"  data-tablename="'.$name.'" data-type="table" data-nodeid="node_'.$d_id.'"  id="node_'.$d_id.'" ><span class="folder" >'.$name.'</span></li>';
		        			}
						}
		        	}
				?>	
		        </ul>
			</li>
	    </ul>
  	</div>
</body>
</html>


