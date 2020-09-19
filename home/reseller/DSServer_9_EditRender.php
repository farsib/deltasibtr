<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_9_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSServer_9_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Server.UserWebsitePasswordRecovery.View");
				$Server_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Server_Id,PartName,Param1 As ISEnable,Param2 As MaxYearlyCount,Param3 As MaxMonthlyCount,Param4 As MaxWeeklyCount,Param5 As MaxDailyCount,Param6 as MessageText ".
				"from Hserver where Server_Id='$Server_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
    case "update":
				DSDebug(1,"DSServer_9_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Server.UserWebsitePasswordRecovery.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Server_Id']=Get_Input('POST','DB','Server_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Param1']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['Param2']=Get_Input('POST','DB','MaxYearlyCount','INT',1,1000,0,0);
				$NewRowInfo['Param3']=Get_Input('POST','DB','MaxMonthlyCount','INT',1,1000,0,0);
				$NewRowInfo['Param4']=Get_Input('POST','DB','MaxWeeklyCount','INT',1,1000,0,0);
				$NewRowInfo['Param5']=Get_Input('POST','DB','MaxDailyCount','INT',1,1000,0,0);
				if($NewRowInfo['Param2']<$NewRowInfo['Param3'])
					ExitError("محدودیت سالیانه نمی تواند کمتر از محدودیت ماهیانه باشد");
				elseif($NewRowInfo['Param3']<$NewRowInfo['Param4'])
					ExitError("محدودیت ماهیانه نمی تواند کمتر از محدودیت هفتگی باشد");
				elseif($NewRowInfo['Param4']<$NewRowInfo['Param5'])
					ExitError("محدودیت هفتگی نمی تواند کمتر از محدودیت روزانه باشد");
				$NewRowInfo['Param6']=Get_Input('POST','DB','MessageText','STR',1,250,0,0);
				
				$OldRowInfo= LoadRowInfo("Hserver","Server_Id='".$NewRowInfo['Server_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));
				
				//----------------------
				$sql= "Update Hserver set  ";
				$sql.="Param1='".$NewRowInfo['Param1']."',";
				$sql.="Param2='".$NewRowInfo['Param2']."',";
				$sql.="Param3='".$NewRowInfo['Param3']."',";
				$sql.="Param4='".$NewRowInfo['Param4']."',";
				$sql.="Param5='".$NewRowInfo['Param5']."',";
				$sql.="Param6='".$NewRowInfo['Param6']."'";
				$sql.=" Where ";
				$sql.="(Server_Id='".$NewRowInfo['Server_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				DSDebug(2,$res);
				
				if($ar!=1){//probably hack
					logdb('Edit','Server',$NewRowInfo['Server_Id'],'Server',"Update Fail,Table=Server affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Server affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Server',$NewRowInfo['Server_Id'],'Server')){
					logunfair("UnFair",'Server',$NewRowInfo['Server_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
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
