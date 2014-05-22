<!DOCTYPE html>
<html lang="zh_TW">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JACL</title>

    <!-- Bootstrap -->
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/submenu.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">

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

    <!-- Extract Modal -->
    <div class="modal fade modal-dialog-center" id="myModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Extract</h4>
          </div>
          <div class="modal-body">
                <span>to: </span> <input id="extract_input" type="text" value="">
          </div>
          <div class="modal-footer">
            <button id="extract_sumit_btn" type="button" class="btn btn-primary">確定</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
    </div>


    <div class="container">
      
        <div class="row">
            <!-- title name -->
            <div class="col-md-12"><h1 class="text-muted">JACL Analytics</h1></div>      
        </div>  
        <div class="row">        
            <nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="collapse navbar-collapse">
                  <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">File<b class="caret"></b></a>
                        <ul class="dropdown-menu">                            
                            <li class="dropdown-submenu">
                                <a tabindex="-1" href="#">New</a>
                                <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="#">Table</a></li>                                  
                                    <li><a href="#">Script</a></li>
                                    <li><a href="#">Workspace</a></li>
                                    <li><a href="#">Folder</a></li>                                    
                                    <li class="divider"></li>
                                    <li><a tabindex="-1" href="#">Project</a></li>
                                    <!-- submenu -->
                                    <!-- <li class="dropdown-submenu">
                                    <a href="#">More..</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">3rd level</a></li>
                                        <li><a href="#">3rd level</a></li>
                                    </ul>
                                    </li> -->
                                </ul>
                              </li>                   
                            </li>                                               
                            <li><a href="#">Delete</a></li>
                            <li><a href="#">Remote</a></li>
                            <li><a href="#">Properties</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Open Project</a></li>
                            <li><a href="#">Save Project</a></li>
                            <li><a href="#">Save Project As</a></li>
                            <li><a href="#">Close Project</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Save</a></li>
                            <li><a href="#">Save As</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Page Setup</a></li>
                            <li><a href="#">Print</a></li>
                            <li><a href="#">Print Preview</a></li>
                            <li><a href="#">Print Project Contents</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Exit</a></li>
                      </ul>
                    </li>    

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Edit<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="#">Undo</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Cut</a></li>
                            <li><a tabindex="-1" href="#">Copy</a></li>     
                            <li><a tabindex="-1" href="#">Paste</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Find</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Table Layout</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Filter</a></li>
                            <li><a tabindex="-1" href="#">Variable</a></li>                                         
                            <li class="dropdown-submenu"><a tabindex="-1" href="#">Notes</a>
                                <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="#">Delete All Notes from Table</a></li>
                                    <li><a tabindex="-1" href="#">Edit Note</a></li>                                                    
                                </ul>
                            </li>
                        </ul>
                    </li>                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Data<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="#" id="extrat_data">Extract Data</a></li>                                         
                            <li><a tabindex="-1" href="#">Export To Other Application</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-submenu"><a tabindex="-1" href="#">Crystal</a>
                                <ul class="dropdown-menu">
                                    <li><a tabindex="-1" href="#">Update Template</a></li>
                                    <li><a tabindex="-1" href="#">Create Template</a></li>
                                    <li><a tabindex="-1" href="#">View Report</a></li>
                                </ul>
                            </li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Create Index</a></li>                                         
                            <li><a tabindex="-1" href="#">Relate Table</a></li>                                         
                            <li><a tabindex="-1" href="#">Report</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Join Tables</a></li>
                            <li><a tabindex="-1" href="#">Merge Tables</a></li>                                         
                            <li><a tabindex="-1" href="#">Sort Records</a></li>
                            <li class="divider"></li>       
                            <li><a tabindex="-1" href="#">Verify</a></li>                                           
                            <li><a tabindex="-1" href="#">Search</a></li>
                        </ul>
                    </li>                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Analyze<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                        <li><a tabindex="-1" href="#">Count Records</a></li>                                            
                        <li><a tabindex="-1" href="#">Total Fields</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu"><a tabindex="-1" href="#">Statistical</a>
                            <ul class="dropdown-menu">
                                <li><a tabindex="-1" href="#">Statistics</a></li>
                                <li><a tabindex="-1" href="#">Profile</a></li>                                                  
                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li><a tabindex="-1" href="#">Stratify</a></li>                                         
                        <li><a tabindex="-1" href="#">Classify</a></li>                                         
                        <li><a tabindex="-1" href="#">Histogram</a></li>
                        <li><a tabindex="-1" href="#">Age</a></li>
                        <li><a tabindex="-1" href="#">Summarize</a></li>    
                        <li class="divider"></li>
                        <li><a tabindex="-1" href="#">Cross-tabulate</a></li>
                        <li><a tabindex="-1" href="#">Perform Benford Analysis</a></li>                                 
                        <li class="divider"></li>       
                        <li><a tabindex="-1" href="#">Examine Sequence</a></li>                                         
                        <li><a tabindex="-1" href="#">Look for Gaps</a></li>
                        <li><a tabindex="-1" href="#">Look for Duplicates</a></li>                                          
                        <li><a tabindex="-1" href="#">Fuzzy Duplicates</a></li>
                        </ul>
                    </li>                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sampling<b class="caret"></b></a>
                        <ul class="dropdown-menu">                        
                            <li><a tabindex="-1" href="#">Calculate Sample Size</a></li>                                            
                            <li><a tabindex="-1" href="#">Sample Records</a></li>                                           
                            <li><a tabindex="-1" href="#">Evaluate Error</a></li>
                        </ul>
                    </li>                
                    <li class="dropdown">                      
                      <li><a href="#">Applications</a></li>
                    </li>                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tools<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="#">Add New Session</a></li>                                         
                            <li><a tabindex="-1" href="#">Add Comment</a></li>                                          
                            <li><a tabindex="-1" href="#">Hex Dump</a></li>
                            <li><a tabindex="-1" href="#">Generate Random Numbers</a></li>                                          
                            <li><a tabindex="-1" href="#">Table History</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Notify by Email</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Create Script from Table History</a></li>                                         
                            <li><a tabindex="-1" href="#">Run Script</a></li>                                           
                            <li><a tabindex="-1" href="#">Set Script Recorder On</a></li>   
                        </ul>
                    </li>                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Server<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="#">Server Profiles</a></li>                                         
                            <li><a tabindex="-1" href="#">Database Profiles</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">ACLGRC Access Token</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Connect</a></li>                                          
                            <li><a tabindex="-1" href="#">Disconnect</a></li>                                       
                            <li><a tabindex="-1" href="#">Activity Log</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#">Transfer to Server</a></li>
                        </ul>
                    </li>  
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Windows<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="#">Show Command Line</a></li>                                            
                            <li><a tabindex="-1" href="#">Show Welcome Screen</a></li>
                        </ul>
                    </li>                    
                  </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
            </nav>       
        </div><!-- row -->
        <div class="row">
            <div class="col-md-3 show_td" ><iframe name="ifm-left" src="directory.php" width="100%" height="700" frameborder="0"></iframe></div>
            <!-- <div class="col-md-9 show_td" ><iframe name="ifm-right" id="file_content"  src="jqgrid.php" width="100%" height="700" frameborder="0"></iframe></div>   -->
            <div class="col-md-9 show_td" ><iframe name="ifm-right" id="file_content"  src="tab.php" width="100%" height="700" frameborder="0"></iframe></div>  
        </div>      
    </div><!-- main container -->
 
    
  </body>
</html>