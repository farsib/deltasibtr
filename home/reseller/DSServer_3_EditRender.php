<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_3_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

if($LReseller_Id!=1) ExitError('شما مجاز به ویرایش و دیدن اطلاعات ادمین نیستید');

$act=Get_Input('GET','DB','act','ARRAY',array("load","update","apply"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSServer_3_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Server.Admin.View");
				$Server_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Server_Id,PartName,If(Param1='','0.0.0.0/0',Param1) As PermitIP,If(Param2='',600,Param2) As SessionTimeout,".
					"Param3 as AutoUpdate,Param4 as UpdateMode,Param5 as NoneBlockIP,Param6 as ADConnectType,{$DT}DateTimeStr(LastLoginDT) as LastLoginDT,inet_ntoa(LastLoginIP) as LastLoginIP from Hserver,Hreseller where Server_Id='$Server_Id' and Reseller_Id=1";
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
				DSDebug(1,"DSServer_3_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Server.Admin.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Server_Id']=Get_Input('POST','DB','Server_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Param1']=Get_Input('POST','DB','PermitIP','STR',1,250,0,0);
				$NewRowInfo['Param2']=Get_Input('POST','DB','SessionTimeout','INT',600,99999999,0,0);
				$NewRowInfo['Param3']=Get_Input('POST','DB','AutoUpdate','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['Param4']=Get_Input('POST','DB','UpdateMode','ARRAY',array("ApprovedVersion","BetaVersion"),0,0,0);
				$NewRowInfo['Param5']=Get_Input('POST','DB','NoneBlockIP','STR',0,250,0,0);
				$NewRowInfo['Param6']=Get_Input('POST','DB','ADConnectType','ARRAY',array("New","Old"),0,0,0);

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
				if($ar!=1){//probably hack
					logdb('Edit','Server',$NewRowInfo['Server_Id'],'Server',"Update Fail,Table=Server affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Server affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Server',$NewRowInfo['Server_Id'],'Server')){
					logunfair("UnFair",'Server',$NewRowInfo['Server_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				DBUpdate("Update Hreseller Set PermitIP='".$NewRowInfo['Param1']."',NoneBlockIP='".$NewRowInfo['Param5']."',SessionTimeout='".$NewRowInfo['Param2']."' Where Reseller_Id=1");
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
