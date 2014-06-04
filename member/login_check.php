<meta charset='utf8'>
<?php
session_start();
include "../include/config.php";


$systemUser=$_POST["systemUser"];
$sPass=$_POST["sPass"];
$sPass=md5($sPass);




try {
	$sql = "SELECT * from JACL_member where  systemUser='$systemUser' and sPass='$sPass'";
	$stmt = $db_conn->prepare($sql);
	$exe = $stmt->execute();
	if ($exe===false) {
		throw new PDOException('取得登入資訊錯誤');
	}
} catch (PDOException $e) {
	$error = $db_conn->errorInfo();
	echo "資料庫存取錯誤: " . $e->getMessage()."<br>";
	echo "錯誤行數: " . $e->getline()."<br>";
	// echo "錯誤內容: " . $error[2];
	die();
}
$row  = $stmt->fetch();
if ($systemUser===$row["systemUser"] && $sPass === $row["sPass"]) {
	// 1234 => 81dc9bdb52d04dc20036dbd8313ed055
	$_SESSION["JACL_sUid"]=$row["sUid"];

	// 登入成功
	header("Location:../index.php");
}else{
	?>
	<script type="text/javascript">
		alert("帳號或密碼錯誤，請確認後重新登入！");
		window.location="login.php";
	</script>
	<?
	@session_destroy();
}







?>