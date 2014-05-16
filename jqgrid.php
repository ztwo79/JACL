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
			

			$("#list4").jqGrid({
				datatype: "local",
				height: 250,
			   	colNames:['Inv No','Date', 'Client', 'Amount','Tax','Total','Notes'],
			   	colModel:[
			   		{name:'id',index:'id', width:60, sorttype:"int"},
			   		{name:'invdate',index:'invdate', width:90, sorttype:"date"},
			   		{name:'name',index:'name', width:100},
			   		{name:'amount',index:'amount', width:80, align:"right",sorttype:"float"},
			   		{name:'tax',index:'tax', width:80, align:"right",sorttype:"float"},		
			   		{name:'total',index:'total', width:80,align:"right",sorttype:"float"},		
			   		{name:'note',index:'note', width:150, sortable:false}		
			   	],
			   	multiselect: true,
			   	caption: "Manipulating Array Data"
			});
			var mydata = [
					{id:"1",invdate:"2007-10-01",name:"test",note:"note",amount:"200.00",tax:"10.00",total:"210.00"},
					{id:"2",invdate:"2007-10-02",name:"test2",note:"note2",amount:"300.00",tax:"20.00",total:"320.00"},
					{id:"3",invdate:"2007-09-01",name:"test3",note:"note3",amount:"400.00",tax:"30.00",total:"430.00"},
					{id:"4",invdate:"2007-10-04",name:"test",note:"note",amount:"200.00",tax:"10.00",total:"210.00"},
					{id:"5",invdate:"2007-10-05",name:"test2",note:"note2",amount:"300.00",tax:"20.00",total:"320.00"},
					{id:"6",invdate:"2007-09-06",name:"test3",note:"note3",amount:"400.00",tax:"30.00",total:"430.00"},
					{id:"7",invdate:"2007-10-04",name:"test",note:"note",amount:"200.00",tax:"10.00",total:"210.00"},
					{id:"8",invdate:"2007-10-03",name:"test2",note:"note2",amount:"300.00",tax:"20.00",total:"320.00"},
					{id:"9",invdate:"2007-09-01",name:"test3",note:"note3",amount:"400.00",tax:"30.00",total:"430.00"}
					];
			for(var i=0;i<=mydata.length;i++)
				$("#list4").jqGrid('addRowData',i+1,mydata[i]);



			$("#list2").jqGrid({
			   	url:'jq_server.php?q=2',
				datatype: "json",
			   	colNames:['Inv No','Date', 'Client', 'Amount','Tax','Total','Notes'],
			   	colModel:[
			   		{name:'id',index:'id', width:55},
			   		{name:'invdate',index:'invdate', width:90},
			   		{name:'name',index:'name asc, invdate', width:100},
			   		{name:'amount',index:'amount', width:80, align:"right"},
			   		{name:'tax',index:'tax', width:80, align:"right"},		
			   		{name:'total',index:'total', width:80,align:"right"},		
			   		{name:'note',index:'note', width:150, sortable:false}		
			   	],
			   	rowNum:10,
			   	rowList:[10,20,30],
			   	pager: '#pager2',
			   	sortname: 'id',
			    viewrecords: true,
			    sortorder: "desc",
			    caption:"JSON Example"
			});
			$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		});

		
	
	</script>
</head>
<body>
	<table id="list4"></table>

	
	<table id="list2"></table>
	<div id="pager2"></div>
</body>
</html>


