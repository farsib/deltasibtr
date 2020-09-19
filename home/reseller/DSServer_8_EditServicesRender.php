<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_8_EditServicesRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();
usleep(200000);
$act=Get_Input('GET','DB','act','ARRAY',array("Status","Stop","Start","Restart","SetAutoStart"),0,0,0);
$ServiceName=Get_Input('GET','DB','ServiceName','ARRAY',array("netlog","notify","mikrotikrate","disconnect","urlreporting","ds_graph"),0,0,0);
switch ($act) {
	case "Stop":
	case "Start":
	case "Restart":
			exitifnotpermit(0,"Admin.Server.DeltasibServices.$ServiceName.$act");
			$act=strtolower($act);
			$res=runshellcommand('service',$ServiceName,$act,'-');
			DSDebug(0,"service $ServiceName $act    ===> '$res'");
			logdb('Edit','Server',8,"$ServiceName","service $ServiceName $act");
			echo "OK~".runshellcommand('service',$ServiceName,'status','-');
		break;
	case "Status":
			exitifnotpermit(0,"Admin.Server.DeltasibServices.$ServiceName.Status");
			echo "OK~".runshellcommand('service',$ServiceName,'status','-');
		break;
	case "SetAutoStart":
				DSDebug(1,"DSServer_8_EditRender SetAutoStart ******************************************");
				exitifnotpermit(0,"Admin.Server.DeltasibServices.$ServiceName.SetAutoStart");
				$AutoStart=Get_Input('POST','DB','AutoStart','INT',0,1,0,0);
				
				$OutStr=runshellcommand("php","DSSetStartup",$ServiceName,"$AutoStart");
				DSDebug(0,"DSSetStartup.php ".$ServiceName." $AutoStart");
				DSDebug(0,$OutStr);
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
