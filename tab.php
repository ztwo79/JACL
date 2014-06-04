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
    <script src="js/tab.js"></script>
    <script src="lib/jquery-iframe-auto-height/jquery.browser.js"></script>
    <script src="lib/jquery-iframe-auto-height/jquery.iframe-auto-height.plugin.1.9.5.min.js"></script>
    
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
                    <input type="text" id="command_line" name="command_line" value='' />
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