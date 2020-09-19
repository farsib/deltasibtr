<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSResellerIsValidSession ..........................................................................");
//sleep(10);
if(isset($_GET['WhoIs'])){
	$WhoIs=$_GET["WhoIs"];
	if($WhoIs=="YES") echo "~".$LResellerName."~OK~";//~ali~OK~
	else if($LResellerName=="")
		echo "NO";
	else	
		echo "YES";
}
else
	echo "Your IP reported as a Hacker to Administrator";
?>
