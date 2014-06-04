$(document).ready(function() {
	// 資料表的名稱
	var table_name = $("#table_name").val();
	// 資料庫內該資料表的名稱
	var t_id = $("#t_id").val();
		
		if (t_id.length>0) {
		// 取得資料表的欄位與格式
		$.ajax({ url: 'get_table_info_ajax.php' ,
	        cache: false,
	        dataType: 'html',// <== 設定傳送格式
	        type:'GET',// <== 設定傳值方式
	        data: { t_id: t_id  },// <== 傳GET的變數，此例是gsn
	        error: function(xhr) { alert('Ajax request 發生錯誤'+ xhr); },
	        success: function(response) {
	        	// alert(response);
	            
	            var table_info_obj = $.parseJSON(response);
	            // col name
	            var colNames_arr = table_info_obj.colNames_arr;
	            // col data type
	            var colModel_arr = table_info_obj.colModel_arr;
	            
	            
	            // 複製 array
	            var col_list_arr = colNames_arr.slice();
	            // 去除 key_id
	            col_list_arr.splice(0 , 1);
	            $("#col_list").val(col_list_arr.join(","));
	            
	            colNames_arr[0]="id";

	            // 格式範例
	            // colNames: [ 'key_id', 'First_Name', 'Last_Name', 'CardNum', 'EmpNo', 'HireDate', 'Salary', 'Bonus_2005'],
	            // JSON 格式
				// var colModel_arr = [
				//    		{name:'key_id',index:'key_id', width:100},
				//    		{name:'First_Name',index:'First_Name', width:90},
				//    		{name:'Last_Name',index:'Last_Name', width:90},
				//    		{name:'CardNum',index:'CardNum', width:80, align:"right"},
				//    		{name:'EmpNo',index:'EmpNo', width:80, align:"right"},		
				//    		{name:'HireDate',index:'HireDate', width:80,align:"right"},		
				//    		{name:'Salary',index:'Salary', width:80,align:"right"},		
				//    		{name:'Bonus_2005',index:'Bonus_2005', width:80,align:"right"}
				//    		// {name:'name',index:'name asc, invdate', width:100},
				//    		// {name:'Bonus_2005',index:'Bonus_2005', width:80,align:"right" , sortable:false}
				// ];

				// 取得 table的資料
				jQuery("#table_list").jqGrid({
				   	url:'jq_server.php?t_id='+t_id,
					// ajaxGridOptions: { contentType: 'application/json; charset=utf-8' },
					datatype: "json",
				   	colNames: colNames_arr,
				   	colModel: colModel_arr,
				   	// default row
				   	rowNum:30,
				   	// 每頁筆數
				   	rowList:[50, 80 ,100],
				   	pager: '#pager',
				    viewrecords: true,
				    sortname: 'key_id',
				    sortorder: "asc",
				    // width: 500,
					// set 100% it wll auto resize
					height: "100%",
					width: "100%",
					// autowidth:true,
				    caption: table_name,
				    // colunm drag and drop
				    sortable:true
				    
				});
				$("#table_list").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});


				// cancel table_list_key_id sortable
				$('tr.ui-jqgrid-labels').sortable({ cancel: 'th#table_list_key_id'});
				// force other element can't sort table_list_key_id
				$('tr.ui-jqgrid-labels').sortable({ items: "th:not(#table_list_key_id)" });

				// $(window.parent.document).resize_heigth();
				$('.ui-pg-selbox').on('change', function(event) {
					event.preventDefault();
					// $('#dynamic_table_content', window.parent.document).get(0).contentWindow.resize_heigth();
				});
	        }
	    });
	}
});
// 開始搜尋
function col_filter	(searchField , searchOper , searchString ) {
	// $("#table_list").jqGrid('setGridParam',{url:'get_table_info_ajax.php?searchString='+searchString});
	// 取得傳入 的data
 	var postData = $("#table_list").jqGrid("getGridParam", "postData");
 	// var colModel = $("#table_list").jqGrid("getGridParam", "colModel");
 	// 開啟搜尋
 	$("#table_list").jqGrid("setGridParam", { search: true });
 	// 搜尋的欄位
 	postData.searchField=searchField;
 	// 比對的條件
 	postData.searchOper=searchOper;
 	// 使用者輸入的資料
 	postData.searchString=searchString;
 	// 送出查詢
 	$("#table_list").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
 	// 放入資料
	$("#search_chk").val("true");
	$("#searchField").val(searchField);
	$("#searchOper").val(searchOper);
	$("#searchString").val(searchString);
}