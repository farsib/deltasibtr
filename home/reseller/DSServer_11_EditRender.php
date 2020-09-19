<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_11_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSServer_11_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Server.UserWebsitePasswordRecovery.View");
				$Server_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Server_Id,PartName,Param1 As ISEnable,Param2 As ProviderCode,Param3 As Username,Param4 As Password,Param5 As IP,Param6 As Port,Param7 As CRM_OperatorName from Hserver where Server_Id='$Server_Id'";
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
				DSDebug(1,"DSServer_11_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Server.UserWebsitePasswordRecovery.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Server_Id']=Get_Input('POST','DB','Server_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Param1']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No","Offline"),0,0,0);
				
				$sql= "Update Hserver set  ";
				$sql.="Param1='".$NewRowInfo['Param1']."'";
				
				if($NewRowInfo['Param1']!='No'){
					$NewRowInfo['Param2']=Get_Input('POST','DB','ProviderCode','STR',1,4,0,0);
					$NewRowInfo['Param3']=Get_Input('POST','DB','Username','STR',1,16,0,0);
					$NewRowInfo['Param4']=Get_Input('POST','DB','Password','STR',1,16,0,0);
					$NewRowInfo['Param5']=Get_Input('POST','DB','IP','STR',0,15,0,0);
					$NewRowInfo['Param6']=Get_Input('POST','DB','Port','INT',0,65535,0,0);
					$NewRowInfo['Param7']=Get_Input('POST','DB','CRM_OperatorName','STR',5,32,0,0);
					
					

					$sql.=",Param2=LPAD('".$NewRowInfo['Param2']."',4,'0')";
					$sql.=",Param3='".$NewRowInfo['Param3']."'";
					$sql.=",Param4='".$NewRowInfo['Param4']."'";
					$sql.=",Param5='".$NewRowInfo['Param5']."'";
					$sql.=",Param6='".$NewRowInfo['Param6']."'";
					$sql.=",Param7='".$NewRowInfo['Param7']."'";
				}
				
				$OldRowInfo= LoadRowInfo("Hserver","Server_Id='".$NewRowInfo['Server_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));
				
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
