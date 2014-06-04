<?php
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
<!doctype html>
<html lang="zh_TW">
<head>
	<meta charset="UTF-8">
	<title>jqgrid</title>

	<!-- JQuery UI -->
	<link rel="stylesheet" type="text/css" media="screen" href="lib/jquery-ui/css/smoothness/jquery-ui-1.10.4.custom.css" />
	<!-- jqgrid -->
	<link rel="stylesheet" type="text/css" media="screen" href="lib/jquery.jqGrid/css/ui.jqgrid.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="lib/jquery.jqGrid/css/ui.multiselect.css" />
	<script src="js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<!-- <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script> -->

	<script type="text/javascript" src="lib/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
	<!-- <script src="lib/jquery-ui/development-bundle/ui/minified/jquery-ui.custom.min.js" type="text/javascript"></script> -->
	<!-- <script src="lib/jquery-ui-1.10.4/js/jquery-ui-1.10.4.min.js" type="text/javascript"></script> -->

	
	<script src="lib/jquery.jqGrid/plugins/ui.multiselect.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/js/jquery.jqGrid.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/plugins/jquery.tablednd.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/plugins/jquery.contextmenu.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/src/grid.jqueryui.js" type="text/javascript"></script>
	<!-- <script src="lib/jquery.jqGrid/js/grid.jqueryui.js" type="text/javascript"></script> -->

	
	<script src="lib/jquery.jqGrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jqgrid.js"></script>
</head>
<body>
	<input type="hidden" value="<?echo $table_name;?>" id="table_name">
	<input type="hidden" value="<?echo $t_id;?>" id="t_id">
	<input type="hidden" value="<?echo $col_list;?>" id="col_list">
	<input type="hidden" value="" id="search_chk">
	<input type="hidden" value="" id="searchField">
	<input type="hidden" value="" id="searchOper">
	<input type="hidden" value="" id="searchString">
	<table id="table_list"></table>
	<div id="pager"></div>
</body>
</html>


