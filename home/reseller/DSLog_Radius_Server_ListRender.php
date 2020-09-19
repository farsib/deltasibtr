<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSLOg_Reseller_www_ListRender.........................................................................");

if($LResellerName=='') 	exit("<script>alert('خطا، نشست منقضی شده،لطفا مجدد وارد شوید')</script>خطا، نشست منقضی شده،لطفا مجدد وارد شوید");

//$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);
$act='list';
PrintInputGetPost();
switch ($act) {
    case "list":
				//Permission -----------------
				if(!ISPermit(0,"Log.Server.List"))
					exit("<script>alert('خطا، مجاز نیست [Log.Server.List]')</script>خطا، مجاز نیست [Log.Server.List]");

				$Item=Get_Input('GET','DB','Item','ARRAY',array("RLog","MLog","HAL","HEL","MS","MP","LP","NS","Top","dmesg","lsof","HDS","MemS"),0,0,0);
				$NumberOfRows=Get_Input('GET','DB','NumberOfRows','INT',0,1000,0,0);
				$OutStr=runshellcommand("php","DSLog","$Item","$NumberOfRows");
				DSDebug(0,"runshellcommand(\"php\",\"DSLog\",\"$Item\",\"$NumberOfRows\");");
				echo "<html>";
					echo "<body>";
						echo "<div style='height:100%;width:100%;overflow:auto;background-color:black;color:white;'>";
							echo "<code>&nbsp;";
								echo str_replace("\n","<br/>&nbsp;",str_replace(" ","&nbsp;",$OutStr));
							echo "</code>";
						echo "</div>";
					echo "</body>";
				echo "</html>";
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
