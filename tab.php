<!DOCTYPE html>
<html lang="zh_TW">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>table</title>

    <!-- Bootstrap -->
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <link rel="stylesheet" type="text/css" href="css/tab.css">
    <!-- <input type="hidde" value="" id="dynamic_table_name_data"> -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="lib/jquery-iframe-auto-height/jquery.browser.js"></script>
    <script src="lib/jquery-iframe-auto-height/jquery.iframe-auto-height.plugin.1.9.5.min.js"></script>
    

    <script type="text/javascript">
        $(document).ready(function() {
            // var table_name="agents_metaphor";
            // add_dynamic_table(table_name);
            $('#myTab a').click(function (e) {
              // e.preventDefault();
              // $(this).tab('show');
              // change_dyanamic_table("agents_metaphor");
            })
            
            //  執行command line
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
            var command_line_data = $("#command_line").val();
            if (command_line_data.length>0) {
                // 替assign 符號前後增加空格
                command_line_data = command_line_data.replace('=',' = ');
                var command_line_arr = command_line_data.split(" ");
                
                // 去除空白
                command_line_arr = $.grep(command_line_arr, function(n, i){
                    return (n !== "" && n != null);
                });
                
                // 取得指令
                var command = command_line_arr[0].toLowerCase();
                //要設定的欄位
                var command_target = command_line_arr[1].toLowerCase();
                // 不是有效的指令
                if (command!="set") {
                    alert(command_line_data+"is not a valid command"); 
                };

                // set filter 
                if (command==="set" && command_target==="filter") {
                    // 刪除 set 與 filter
                    command_line_arr.splice(0,2) ; 
                    // To t 可以省略
                    var to = command_line_arr[0].toLowerCase();
                    if (to === "to" || to ==="t") {
                        command_line_arr.splice(0,1); 
                    }; 

                    var filter= command_line_arr[0].toLowerCase();
                    // 取得現在開啟的表的 col list 
                    var col_list=$("#dynamic_table_content").contents().find("#col_list").val();
                    if (typeof col_list !== "undefined") {    
                        col_list=col_list.toLowerCase();
                        col_arr = col_list.split(",");
                        if ($.inArray(filter , col_arr)===-1 ) {
                            alert(filter);
                            alert( JSON.stringify(col_arr));
                            alert("undefine");
                        };
                    };
                    // $("#errorModal" , window.parent.document).get(0).contentWindow.error_show();
                    alert(filter);
                    parent.error_show();
                    // alert( JSON.stringify(col_arr));
                    // alert(filter);
                    // alert( JSON.stringify(command_line_arr));
                };

                // Set FILTER to NO1> '0'
                // Set FILTER NO1> '0'


                // FILTER
                // alert(command);
                // alert( JSON.stringify(command_line_arr));
            };    
        }

        function set_dynamic_table (table_name , db_table_name) {
            // check if dynamic is existing or not
            if ($("#dynamic_tab").length===0) {
                add_dynamic_table(table_name , db_table_name);
            }else{
                change_dynamic_table(table_name , db_table_name);
            }
        }
        // creat a new dynamic table
        function add_dynamic_table (table_name , db_table_name) {
            $("#myTab").append('<li id="dynamic_tab" ><a href="#dynamic_table" data-toggle="tab" id="dynamic_table_name">'+table_name+'</a></li>');
            $("#myTabContent").append('<div class="tab-pane" id="dynamic_table"><iframe name="ifm-right" id="dynamic_table_content" scrolling="no"  src="jqgrid.php?db_table_name='+db_table_name+'" width="100%" height="700" frameborder="0"></iframe></div>');
            $("#myTabContent").append('<div class="tab-pane" id="dynamic_table"></div>');
            resize_heigth();
            $("#dynamic_table_name").tab('show');
        }
        // change dynamic table
        function change_dynamic_table (table_name , db_table_name) {
            $("#dynamic_table_content").attr("src", 'jqgrid.php?table_name='+table_name+'&db_table_name='+db_table_name);
            $("#dynamic_table_name").html(table_name);
            resize_heigth();          
            $("#dynamic_table_name").tab('show');  
        }

        function resize_heigth (argument) {
            $('#dynamic_table_content').iframeAutoHeight({heightOffset: 20 , minHeight: 700});
            $('#dynamic_table_content').attr("scrolling" , "yes");
            
        }
        
    </script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
        <!-- <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="javascript:void">Home</a></li>
            <li><a href="javascript:void">Profile</a></li>
            <li><a href="javascript:void">Messages</a></li>
        </ul> -->
            <div id="conmmand_line_box">
                <h6>Command Line</h6>
                <div id="command_line_input">
                    <input type="text" id="command_line" name="command_line" value='SET FILTER TO BSAK_LIFNR = "0000000111"' />
                    <img src="img/commandline_run.png" alt="Run" id="run_btn">
                </div>
            </div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"  id="welcome_tab" ><a href="#welcome" data-toggle="tab">Welcome</a></li>
              <!-- <li><a href="#messages" data-toggle="tab">Messages</a></li> -->
            </ul>
            <!-- Tab panes -->
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane active" id="welcome">welcome page</div>
            </div>
    
  </body>
</html>