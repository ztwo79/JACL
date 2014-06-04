$(document).ready(function() {
    // var table_name="agents_metaphor";
    // add_dynamic_table(table_name);
    $('#myTab a').click(function (e) {
      // e.preventDefault();
      // $(this).tab('show');
      // change_dyanamic_table("agents_metaphor");
    })
    
    // 按 enter 執行command line
    $("#command_line").on('keyup', function(event) {
        event.preventDefault();
        var code = event.keyCode || event.which;
        if(code == 13) { //Enter keycode
            command_run();
        }
    });
    $("#run_btn").on('click', function(event) {
        // 執行 command line
        command_run();
    });
});

// 執行command line
function command_run () {
    // 取得command line 資料
    var command_line_original_data = $("#command_line").val();
    command_line_original_data_len=command_line_original_data.length;
    if (command_line_original_data_len>0) {
        // var command_line_original_data_string_arr=[];
        // var set_chk=false;
        // var set_start=false;
        // var filter_chk=false;
        // for (var i = 0 ; i < command_line_original_data_len; i++) {
        //     var each_char=command_line_original_data.substr(i, 1);
        //     if (each_char!==" " ) {
        //         command_first.push(set_chk);    
        //     };
        //     command_line_original_data_string_arr.push(each_char);
        // };
        // alert( JSON.stringify(set_chk));
        // alert( JSON.stringify(command_line_original_data_string_arr));

        // 替比對符號前後增加空格
        var command_line_data = command_line_original_data.replace('=',' = ');
        var command_line_original_arr = command_line_data.split(" ");
        // 用來比對的arr
        var command_line_arr = command_line_data.split(" ");
        
        // 去除空白
        command_line_arr = $.grep(command_line_arr, function(n, i){
            return (n !== "" && n != null);
        });

        if (command_line_arr.length>2) {
            // 取得指令
            var command = command_line_arr[0].toLowerCase();
            //要設定的欄位
            var command_target = command_line_arr[1].toLowerCase();
            // 第三個參數
            var command_third = command_line_arr[2].toLowerCase();

        };

         // 確認是否為有效的指令
        var command_veify=false;
        // 執行指令的種類
        var command_type="";
        // set filter
        if (command ==="set" && command_target ==="filter") {
            // SET FILTER TO type1="cn"
            command_veify=true;                    
            command_type="set_filter";
        };
        // extrac record
        if (command === "extract" && command_target === "record"  && command_third ==="to") {
            // EXTRACT RECORD TO "TEST_11" OPEN 
            command_veify=true;                    
            command_type="extract_record";
        };

        // 都不是可執行的命令
        if (command_veify===false) {
            alert(command_line_original_data+"is not a valid command"); 
            return false;
        };


    
        // 對每一個指令做不同的比對
        switch (true) {  
            //  SET FILTER  過濾 
            case command_type ==="set_filter":
                var cmmand_verify=true;
                var undefined_index=1;
                // 刪除 set 與 filter
                command_line_arr.splice(0,2) ;

                // To t 可以省略
                var to = command_line_arr[0].toLowerCase();
                if (to === "to" || to ==="t") {
                    command_line_arr.splice(0,1);
                }

                var original_filter = command_line_arr[0];
                // 取得filter  並轉換為小寫用來做比對
                var filter= command_line_arr[0].toLowerCase();

                // 確認欄位是否有效
                // 取得現在開啟的表的 col list 
                var col_list=$("#dynamic_table_content").contents().find("#col_list").val();
                if (typeof col_list !== "undefined") {    
                    // 確認是否是有效的 欄位名稱
                    col_list=col_list.toLowerCase();
                    col_arr = col_list.split(",");
                    // filter 不是有效的欄位名稱
                    if ($.inArray(filter , col_arr)===-1 ) {
                        // alert(filter);
                        command_line_original_data = command_line_original_data.replace(original_filter , "filter");
                        
                        // 確認 filter是不是保留字  是保留字需要抓保留字在下一個
                        var reserved_chk=false;
                        if ($.inArray(filter , ["set" , "filter" , "to" , "t"])===-1) {
                            reserved_chk=true;
                        }
                        // 找到filter 確認
                        var find_filter_chk=false;
                        $.each(command_line_original_arr, function(index, val) {
                                if (val===original_filter) {
                                if (reserved_chk===true) {
                                    // 尋找的欄位與保留字相同 所以目前抓到的是保留字 下一個才會抓到正確的 欄位
                                    reserved_chk=false   
                                };
                                if (reserved_chk===false) {
                                    find_filter_chk=true;
                                    command_line_original_arr[index]= '<span class="error_hightlight ">'+original_filter+"</span>";
                                };
                            }
                        });
                        var error_message="'"+filter+"' is undefined";
                        var error_detail =  command_line_original_arr.join("&nbsp");
                        // 顯示錯誤訊息
                        parent.error_show( error_message , error_detail );
                        cmmand_verify=false;
                        return false;
                    }else{
                        // 是有效的欄位 執行搜尋
                        var searchField= command_line_arr[0];
                        var searchOper= command_line_arr[1];
                        var searchString= command_line_arr[2];
                        // 去除 '' " "
                        searchString=searchString.replace(/\'/g,'');
                        searchString=searchString.replace(/\"/g,'');
                        // alert( searchField + "  " + searchOper + "  " +searchString );

                        // alert("執行command");
                        $("#dynamic_table_content")[0].contentWindow.col_filter(searchField , searchOper , searchString);
                    }
                };
            break;      
            // EXTRACT RECORD TO  新增資料表
            case (command_type==="extract_record"):
                var extract_table_name = command_line_arr[3];
                // 移除 ' ' 與 " "
                extract_table_name = extract_table_name.
                extract_table_name=extract_table_name.replace(/\'/g,'');
                extract_table_name=extract_table_name.replace(/\"/g,'');

                // alert(extract_table_name);
                if (command_line_arr.length>=5) {
                    // 確認是否要打開table
                    var open_cmd = command_line_arr[4].toLowerCase();
                    if (open_cmd==="open") {
                        extract_table(extract_table_name , true);
                    };
                }else{
                    // 新增該表
                    extract_table(extract_table_name , false );
                }
                // alert( JSON.stringify(command_line_arr));
                
            break;  
            
            default:  
                // category = "Young";  
            break;  
        };

        // 清除指令列
        $("#command_line").val("");
    };    
}

// 新增此表
function extract_table (extract_table_name , open_chk) {
    var t_id = $('#dynamic_table_content').contents().find('#t_id').val();

    if (typeof t_id === "undefined") {
        alert("You must first OPEN a table before you can execute this command");
    }else{
        var search_chk = $('#dynamic_table_content').contents().find("#search_chk").val();
        var searchField = $('#dynamic_table_content').contents().find("#searchField").val();
        var searchOper = $('#dynamic_table_content').contents().find("#searchOper").val();
        var searchString = $('#dynamic_table_content').contents().find("#searchString").val();
        
        // 送出的資料
        var send_data = {
            extract_table_name: extract_table_name,
            t_id: t_id,
            search_chk: search_chk,
            searchField: searchField,
            searchOper: searchOper,
            searchString: searchString
        };
        // alert( JSON.stringify(send_data));

        $.ajax({ url: 'extract_table_ajax.php' ,
            cache: false,
            dataType: 'html',// <== 設定傳送格式
            type:'POST',// <== 設定傳值方式
            data: send_data,// <== 傳GET的變數，此例是gsn
            error: function(xhr) { alert('Ajax request 發生錯誤'+ xhr); },
            success: function(response) {
                // alert(response);
                var extrac_node=$.parseJSON(response);
                // 取得資料
                // var extrac_d_id = extrac_node.d_id
                // var extrac_parent_id = extrac_node.parent_id
                var extrac_d_id = "node_"+extrac_node.d_id;
                var extrac_parent_id = "node_"+extrac_node.parent_id;
                var extrac_table_name = extrac_node.table_name;
                var extrac_t_id = extrac_node.t_id;
                
                // 新增這個資料表的節點
                $('#directory_iframe', window.parent.document)[0].contentWindow.add_table_node(extrac_d_id , extrac_parent_id  , extrac_table_name, extrac_t_id  );
                 // 開啟新增的表
                if (open_chk===true) {
                    // 打開這個資料表節點
                    $('#directory_iframe', window.parent.document)[0].contentWindow.open_dynamic_table(extrac_d_id ,  extrac_table_name , extrac_t_id );
                }

            }
        });

    }
}

function set_dynamic_table (table_name , t_id) {
    // check if dynamic is existing or not
    if ($("#dynamic_tab").length===0) {
        add_dynamic_table(table_name , t_id);
    }else{
        change_dynamic_table(table_name , t_id);
    }
}
// creat a new dynamic table
function add_dynamic_table (table_name , t_id) {
    // 增加tab 頁
    $("#myTab").append('<li id="dynamic_tab" ><a href="#dynamic_table" data-toggle="tab" id="dynamic_table_name">'+table_name+'</a></li>');
    $("#myTabContent").append('<div class="tab-pane" id="dynamic_table"><iframe name="ifm-right" id="dynamic_table_content" scrolling="no"  src="jqgrid.php?t_id='+t_id+'" width="100%" height="700" frameborder="0"></iframe></div>');
    $("#myTabContent").append('<div class="tab-pane" id="dynamic_table"></div>');
    resize_heigth();
    $("#dynamic_table_name").tab('show');
}
// change dynamic table
function change_dynamic_table (table_name , t_id) {
    $("#dynamic_table_content").attr("src", 'jqgrid.php?table_name='+table_name+'&t_id='+t_id);
    $("#dynamic_table_name").html(table_name);
    resize_heigth();          
    $("#dynamic_table_name").tab('show');  
}
// 自動調整高度
function resize_heigth (argument) {
    $('#dynamic_table_content').iframeAutoHeight({heightOffset: 20 , minHeight: 700});
    $('#dynamic_table_content').attr("scrolling" , "yes");
}
