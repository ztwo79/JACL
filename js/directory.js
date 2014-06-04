// 記錄動態的 table 節點ID
var dynamic_table_node_id;	

$(document).ready(function() {

	
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
									    	childNode.lazy=true;
									    	childNode.data.subchk="has_sub";
									    	// childNode.data.lazy=true;
									    	// alert("123");
									    	// childNode.addChildren({
									     //    	title: "useless",
									     //    	key:"useless_"+obj_d_id,
									    	// });		
									    };
									    
							        break;  
							
									// table
								    case "table":  
								    	var  obj_t_id = obj.t_id;
								        // 替當下的節點下增加檔案
										var childNode = node.addChildren({
								        	title: obj_name,
								        	// tooltip: "This folder and all child nodes were added programmatically.",
								        	// icon: "table_img.png",
								        	type:obj_type,
								        	nodeid:"nodeid"+obj_d_id,
								        	key:"nodeid"+obj_d_id,
								        	extraClasses:'table_img',
								        	tid : obj_t_id,
								        	tablename : obj_name,
									    });
							        break;

							        // table
								    case "script":  
								        // 替當下的節點下增加檔案
										var childNode = node.addChildren({
								        	title: obj_name,
								        	type:obj_type,
								        	nodeid:"nodeid"+obj_d_id,
								        	key:"nodeid"+obj_d_id,
								        	// tooltip: "This folder and all child nodes were added programmatically.",
								        	// icon: "table_img.png",
								        	extraClasses:'script_img',
									    });
							        break;  
								    
								    default:  
								        
							        break;  
								};
							});
							// 打開當下這個資料夾
							node.setExpanded();
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
			var t_id = node.data.tid;

			if (type==="table") {
				// 開啟動態table
				open_dynamic_table( nodeid , tablename , t_id);
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

// 新增table的節點
function add_table_node (nodeid , parent_node_id , table_name , t_id ) {
	// 取得dynamic_table_node_id 的 id
	parent_node_node = $("#tree").fancytree("getTree").getNodeByKey(parent_node_id);
	// 替當下的節點下增加檔案
	var childNode = parent_node_node.addChildren({
    	title: table_name,
    	type:"table",
    	nodeid: nodeid,
    	key: nodeid,
    	extraClasses:'table_img',
    	tid : t_id,
    	tablename : table_name
    });
}

// 打開table節點 
function open_dynamic_table (node_id  , table_name , t_id) {
	// 取得要開啟的節點
	var open_node = $("#tree").fancytree("getTree").getNodeByKey(node_id);
 	// $("#tree").fancytree("getTree").getNodeByKey(node_id).setActive();
 	// 把節點設為 focus
 	open_node.setActive();

 	// 把圖片設為已開啟的table
 	var $span = $(open_node.span);
    $span.find("> span.fancytree-icon").css({
        backgroundImage: "url(img/acl_treeview/table_img_active.png)",
        backgroundPosition: "0 0"
    });

     // dynamic table有被開啟了
    if (dynamic_table_node_id!==node_id) {
		// 若dynamic table 有被開啟了 需要把原先的table 圖片改為關閉
		if (dynamic_table_node_id !== undefined) {
			// 取得dynamic_table_node_id 的 id
			onblur_node = $("#tree").fancytree("getTree").getNodeByKey(dynamic_table_node_id);
			onblur_node_type =onblur_node.data.type;
			// alert(onblur_node.data.type);		
			// 相同type
			if (onblur_node_type==="table") {
				// 把之前的 dynamic  table 改為為關閉
				var $span = $(onblur_node.span);
		        $span.find("> span.fancytree-icon").css({
		            backgroundImage: "url(img/acl_treeview/table_img.png)",
		            backgroundPosition: "0 0"
		        });
			};
		};
		dynamic_table_node_id=node_id;
    };
    // 開啟動態table
	$('#file_content', window.parent.document).get(0).contentWindow.set_dynamic_table(table_name , t_id);
}