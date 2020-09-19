<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSResellerLogout ..................................................................................");

$conn->sql->query("Delete From Tonline_webreseller where (SessionId='".session_id()."')");
logreseller($LReseller_Id,"Logout","");

echo "OK";

?>

 
 
