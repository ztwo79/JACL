<?
date_default_timezone_set('Asia/Taipei');
ini_set( 'memory_limit', '99999M' );
set_time_limit(0);
/*************************³sµ²¸ê®Æ®w**********************************/
	
// 使用localhost 會導致連結Mysql過慢
// $HOST = "localhost";
$HOST = "127.0.0.1";
$USER = "root";
$PASSWORD = "jacksoft";	 
$conn = mysql_connect($HOST,$USER,$PASSWORD) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db("acl_online",$conn) or die(mysql_error());
/*********************************************************************/
// extension=php_pdo.dll
// extension=php_pdo_mysql.dll
// extension=php_pdo_pgsql.dll
// extension=php_pdo_sqlite.dll
// extension=php_pdo_mssql.dll
// extension=php_pdo_odbc.dll
// extension=php_pdo_firebird.dll
// ;extension=php_pdo_oci8.dll

// $dsn = "mysql:host=localhost;dbname=test";
// $db = new PDO($dsn, root, );
// $count = $db-＞exec("INSERT INTO foo SET name = heiyeluren,gender=男,time=NOW()");
// echo $count;
// $db = null;

?>