<?
date_default_timezone_set('Asia/Taipei');
ini_set( 'memory_limit', '99999M' );
set_time_limit(0);
/*************************³sµ²¸ê®Æ®w**********************************/
	
// 使用localhost 會導致連結Mysql過慢
// $HOST = "localhost";
$HOST = "127.0.0.1";
$DB_name="acl_online";
$USER = "root";
$PASSWORD = "jacksoft";	 
$conn = mysql_connect($HOST,$USER,$PASSWORD) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_select_db($DB_name, $conn) or die(mysql_error());


// 
try {
	$db_conn = new PDO("mysql:host=$HOST;dbname=$DB_name", $USER, $PASSWORD);
} catch (PDOException $e) {
	echo "Could not connect to database ".$e->getMessage();; 
	exit;
}



/*********************************************************************/


// extension=php_pdo.dll
// extension=php_pdo_mysql.dll
// extension=php_pdo_pgsql.dll
// extension=php_pdo_sqlite.dll
// extension=php_pdo_mssql.dll
// extension=php_pdo_odbc.dll
// extension=php_pdo_firebird.dll
// ;extension=php_pdo_oci8.dll
?>