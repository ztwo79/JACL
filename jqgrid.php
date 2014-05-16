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
	
	<script type="text/javascript" src="lib/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
	
	<script src="lib/jquery.jqGrid/plugins/ui.multiselect.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/plugins/jquery.tablednd.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/plugins/jquery.contextmenu.js" type="text/javascript"></script>

	<script src="lib/jquery.jqGrid/js/jquery.jqGrid.js" type="text/javascript"></script>
	<script src="lib/jquery.jqGrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>

	

	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({ url: 'get_table_info_ajax.php' ,
		        cache: false,
		        dataType: 'html',// <== 設定傳送格式
		        type:'GET',// <== 設定傳值方式
		        data: { table_name: "test" },// <== 傳GET的變數，此例是gsn
		        error: function(xhr) { alert('Ajax request 發生錯誤'+ xhr); },
		        success: function(response) {
		        	// alert(response);
		            
		            var table_info_obj = $.parseJSON(response);
		            // col name
		            var colNames_arr = table_info_obj.colNames_arr;
		            // col data type
		            var colModel_arr = table_info_obj.colModel_arr;
		            
		            // colNames_arr[0]="";
		            // JSON 格式
					// var colModel_obj = [
					//    		{name:'key_id',index:'key_id', width:100},
					//    		{name:'First_Name',index:'First_Name', width:90},
					//    		{name:'Last_Name',index:'Last_Name', width:90},
					//    		{name:'CardNum',index:'CardNum', width:80, align:"right"},
					//    		{name:'EmpNo',index:'EmpNo', width:80, align:"right"},		
					//    		{name:'HireDate',index:'HireDate', width:80,align:"right"},		
					//    		{name:'Salary',index:'Salary', width:80,align:"right"},		
					//    		{name:'Bonus_2005',index:'Bonus_2005', width:80,align:"right"},		
					//    		// {name:'name',index:'name asc, invdate', width:100},
					//    		// {name:'Bonus_2005',index:'Bonus_2005', width:80,align:"right" , sortable:false}
					// ];
					$("#list2").jqGrid({
					   	url:'jq_server.php?q=2',
						datatype: "json",
					   	colNames: colNames_arr,
					   	colModel: colModel_arr,
					   	// default row
					   	rowNum:30,
					   	rowList:[30,50,100],
					   	pager: '#pager',
					    viewrecords: true,
					    sortname: 'key_id',
					    sortorder: "asc",
					    // width: 500,
						// set 100% it wll auto resize
						height: "100%",
						// autowidth:true,
					    caption:"JSON Example"			    
					});
					$("#list2").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});
		        }
		    });
			
			
			


			// var colNames_arr = ['key_id','First_Name', 'Last_Name', 'CardNum','EmpNo','HireDate','Bonus_2005'];
			// var colModel_obj = [
			//    		{name:'key_id',index:'key_id', width:100},
			//    		{name:'First_Name',index:'First_Name', width:90},
			//    		{name:'Last_Name',index:'Last_Name', width:90},
			//    		{name:'CardNum',index:'CardNum', width:80, align:"right"},
			//    		{name:'EmpNo',index:'EmpNo', width:80, align:"right"},		
			//    		{name:'HireDate',index:'HireDate', width:80,align:"right"},		
			//    		{name:'Bonus_2005',index:'Bonus_2005', width:80,align:"right"},		
			//    		// {name:'name',index:'name asc, invdate', width:100},
			//    		// {name:'Bonus_2005',index:'Bonus_2005', width:80,align:"right" , sortable:false}
			// ];
			// $("#list2").jqGrid({
			//    	url:'jq_server.php?q=2',
			// 	datatype: "json",
			//    	colNames: colNames_arr,
			//    	colModel: colModel_obj,
			//    	// default row
			//    	rowNum:30,
			//    	rowList:[30,50,100],
			//    	pager: '#pager',
			//     viewrecords: true,
			//     sortname: 'key_id',
			//     sortorder: "asc",
			//     // width: 500,
			// 	// set 100% it wll auto resize
			// 	height: "100%",
			// 	// autowidth:true,
			//     caption:"JSON Example"			    
			// });
			// $("#list2").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});
		});

		
	
	</script>
</head>
<body>
	<table id="list2"></table>
	<div id="pager"></div>
</body>
</html>


