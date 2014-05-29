<!DOCTYPE html>
<html lang="zh_TW">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JACL</title>

    <!-- Bootstrap -->
    <link href="../lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/submenu.css" rel="stylesheet">
    <link href="../css/index.css" rel="stylesheet">
    
    <!-- easyui -->
    <link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.3.6/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.3.6/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.0.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/index.js"></script>
    <div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="account-wall">
                <form class="form-signin" action = "login_check.php" method="POST">
                <input name="systemUser" type="text" class="form-control" placeholder="帳號" required autofocus>
                <input name="sPass" type="password" class="form-control" placeholder="密碼" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit"> 登 入</button>
                <a href="#" class="pull-right need-help">建立帳號 </a><span class="clearfix"></span>
                </form>
            </div>
        </div>
    </div>
</div>
 
    
  </body>
</html>