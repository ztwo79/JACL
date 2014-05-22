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
        });

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