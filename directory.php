<?php
session_start();
include "include/config.php";
include "include/utility.php";



// 取得sUid
$sUid=$_SESSION["JACL_sUid"];

// 專案檔
include "JACL_project_contorl.php";
// 資料表
include "JACL_table_control.php";
// 資料夾
include "JACL_project_directory.php";







// ACL 專案檔
$ACL_project_file="ACL_Demo.ACL";
// $ACL_project_file="Metaphor_Employee_Data.ACL";
// 建立專案
$JACL_project_contorl = new JACL_project_contorl($db_conn , $sUid , $ACL_project_file);
// 取得 專案的 p_id
$p_id =  $JACL_project_contorl->get_p_id();
if (empty($p_id)) {
	$JACL_project_contorl->create_project();
	$p_id =  $JACL_project_contorl->get_p_id();
}
// 放入p_id到session
if (!empty($p_id)) {
	$_SESSION["JACL_p_id"]=$p_id;
}


// 建立專案的資料夾與內容
$JACL_project_directory = new JACL_project_directory($db_conn , $p_id , $ACL_project_file);
// 取得專案資料夾的 root_folder_id
$root_folder_id = $JACL_project_directory->get_root_folder_id();
// $JACL_project_directory->destroy_project_directory();
// die();

// 沒有該專案資料夾 
if (empty($root_folder_id)) {
	// 新增該專案資料夾
	$JACL_project_directory->create_new_directory();
	// 取得新增的專案編號
	$root_folder_id = $JACL_project_directory->get_root_folder_id();
}

// 已建立此專案
if (!empty($root_folder_id)) {
	// 取得第一層的資料
	try {
		$sql = "SELECT * from JACL_directory_structure where p_id='$p_id' and parent_id='$root_folder_id'";
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
		$project_handler->destroy_project();
		die();
	}
	while($row = $stmt->fetch()) {
		$d_id = $row["d_id"];
		$name = $row["name"];
		// 放資料內容的 id
		// table => t_id
		$content_id = $row["content_id"];
		$type = $row["type"];
		$directory_arr[$d_id]["name"]=$name;


		$directory_arr[$d_id]["type"]=$type;
		// 資料夾需確認資料夾內有無資料
		if ($type==="folder") {
			try {
				$sql = "SELECT COUNT(*) as folder_chk from JACL_directory_structure where p_id='$p_id' and parent_id='$d_id'";
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
				$project_handler->destroy_project();
				die();
			}
			$folder_chk  = $folder_chk_stmt->fetchColumn();
			$directory_arr[$d_id]["folder_chk"]=$folder_chk;
		}
		// 取得 資料表的內容
		if ($type==="table") {
			$JACL_table_control = new JACL_table_control($db_conn);
			$JACL_table_control ->get_table_data($content_id);
			$directory_arr[$d_id]["name"] = $JACL_table_control->table_name;
			$directory_arr[$d_id]["t_id"]=$content_id;	
		}
	}
}



?>
<!doctype html>
<html lang="zh_TW">
	<head>
		<meta charset="UTF-8" />
		<!-- fancytree css  skin -->
		<link href="lib/fancytree/src/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
		<!-- custom.css -->
		<!-- <link href="lib/fancytree/demo/skin-custom/custom.css" rel="stylesheet" type="text/css" > -->
		<link href="css/directory.css" rel="stylesheet" type="text/css" >
		<script src="js/jquery-1.11.0.min.js"></script>
	  	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" type="text/javascript"></script>
		<!-- facyytree  -->
		<script src="lib/fancytree/src/jquery.fancytree.js" type="text/javascript"></script>
		<script src="js/directory.js"></script>
	<head>
</head>
<body>
	<div id="tree">
	    <ul>
	      	<li class="folder expanded"><?echo $ACL_project_file;?>
		        <ul>		          
		        <?php
		        	if (!empty($directory_arr)) {
		        		foreach ($directory_arr as $d_id => $directory_data) {
		        			$name = $directory_data["name"];
		        			$table_db_name = $directory_data["content_table"];
		        			$folder_chk = $directory_data["folder_chk"];

		        			if ($directory_data["type"]==="folder" ) {
		        				$sub_chk="";
		        				$expandable="";
		        				if ($folder_chk>0) {
		        					// 有資料夾可以展開
		        					$expandable="lazy";
		        					$sub_chk="has_sub";
		        				}
		        				// 設定節點的兩種格式   放入data-type 或是  使用json格式放入data-json
		        				// echo '<li class="folder" data-type="folder" data-openstatus="unopen" data-did="'.$d_id.'" data-subchk="'.$sub_chk.'" >'.$name.$folder_data.'</li>';
		        				/*<li class="folder"  data-json='{"type": "folder" ,"openstatus": "unopen", "did":"<?echo $d_id;?>", "subchk": "<?echo $sub_chk;?>" }'  ><?echo $name.$folder_data;?></li>*/
		        				// 放入設定
		        				$folder_setting_json->type="folder";
		        				$folder_setting_json->openstatus="unopen";
		        				$folder_setting_json->did=$d_id;
		        				$folder_setting_json->subchk="$sub_chk";
		        				$folder_setting_json->subchk="$sub_chk";
		        				?>
		        				<li class="folder <?echo $expandable;?>"  data-json='<?echo json_encode($folder_setting_json);?>' id="node_<?php echo $d_id;?>"  ><?echo $name;?></li>
								<?

		        			}elseif($directory_data["type"]==="table" ){
		        				// table 的 t_id
		        				$tid = $directory_data["t_id"];
								// echo '<li class="table_img '.$name.'"  data-dbtable="'.$table_db_name.'"  data-tablename="'.$name.'" data-type="table" data-nodeid="node_'.$d_id.'"  id="node_'.$d_id.'" ><span class="folder" >'.$name.'</span></li>';
								$table_setting_json->tablename="$name";
								$table_setting_json->type="table";
								$table_setting_json->nodeid="node_$d_id";
								$table_setting_json->tid="$tid";
								?>
								<li class="table_img '.$name.'" data-json='<?echo json_encode($table_setting_json);?>'  id="node_<?php echo $d_id;?>" ><span class="folder" ><?echo $name;?></span></li>
								<?
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


