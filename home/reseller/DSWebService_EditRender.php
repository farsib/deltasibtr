<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSWebServiceEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSWebServiceEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.WebService.View");
				$WebService_User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,WebService_User_Id,ISEnable,WebService_Username,WebService_UserPass,PermitIP ".
					",UserCreate,UserAddService,UserDelete,UserActivateNextService,UserChangeStatus,UserAddPayment,UserSendSMS,UserUpdateInfo,UserChangePassword ".
					"from Hwebservice_user where WebService_User_Id='$WebService_User_Id'";
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
    case "insert": 
				DSDebug(1,"DSWebServiceEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.WebService.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['WebService_Username']=Get_Input('POST','DB','WebService_Username','STR',1,32,0,0);
				$NewRowInfo['WebService_UserPass']=Get_Input('POST','DB','WebService_UserPass','STR',1,64,0,0);
				$NewRowInfo['PermitIP']=Get_Input('POST','DB','PermitIP','STR',9,255,0,0);
				
				$NewRowInfo['UserCreate']=Get_Input('POST','DB','UserCreate','INT',0,1,0,0);
				$NewRowInfo['UserChangePassword']=Get_Input('POST','DB','UserChangePassword','INT',0,1,0,0);
				$NewRowInfo['UserAddService']=Get_Input('POST','DB','UserAddService','INT',0,1,0,0);
				$NewRowInfo['UserChangeStatus']=Get_Input('POST','DB','UserChangeStatus','INT',0,1,0,0);
				$NewRowInfo['UserActivateNextService']=Get_Input('POST','DB','UserActivateNextService','INT',0,1,0,0);
				$NewRowInfo['UserDelete']=Get_Input('POST','DB','UserDelete','INT',0,1,0,0);
				$NewRowInfo['UserAddPayment']=Get_Input('POST','DB','UserAddPayment','INT',0,1,0,0);
				$NewRowInfo['UserSendSMS']=Get_Input('POST','DB','UserSendSMS','INT',0,1,0,0);
				$NewRowInfo['UserUpdateInfo']=Get_Input('POST','DB','UserUpdateInfo','INT',0,1,0,0);
				
				//----------------------
				$sql= "insert Hwebservice_user set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="WebService_Username='".$NewRowInfo['WebService_Username']."',";
				$sql.="WebService_UserPass='".$NewRowInfo['WebService_UserPass']."',";
				$sql.="PermitIP='".$NewRowInfo['PermitIP']."',";
				$sql.="UserCreate='".$NewRowInfo['UserCreate']."',";
				$sql.="UserChangePassword='".$NewRowInfo['UserChangePassword']."',";
				$sql.="UserAddService='".$NewRowInfo['UserAddService']."',";
				$sql.="UserChangeStatus='".$NewRowInfo['UserChangeStatus']."',";
				$sql.="UserActivateNextService='".$NewRowInfo['UserActivateNextService']."',";
				$sql.="UserDelete='".$NewRowInfo['UserDelete']."',";
				$sql.="UserAddPayment='".$NewRowInfo['UserAddPayment']."',";
				$sql.="UserSendSMS='".$NewRowInfo['UserSendSMS']."',";
				$sql.="UserUpdateInfo='".$NewRowInfo['UserUpdateInfo']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['WebService_User_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','WebService',$RowId,'WebService');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSWebServiceEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.WebService.Edit");
				$NewRowInfo=array();
				$NewRowInfo['WebService_User_Id']=Get_Input('POST','DB','WebService_User_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['WebService_Username']=Get_Input('POST','DB','WebService_Username','STR',1,32,0,0);
				$NewRowInfo['WebService_UserPass']=Get_Input('POST','DB','WebService_UserPass','STR',1,64,0,0);
				$NewRowInfo['PermitIP']=Get_Input('POST','DB','PermitIP','STR',9,255,0,0);
				
				$NewRowInfo['UserCreate']=Get_Input('POST','DB','UserCreate','INT',0,1,0,0);
				$NewRowInfo['UserChangePassword']=Get_Input('POST','DB','UserChangePassword','INT',0,1,0,0);
				$NewRowInfo['UserAddService']=Get_Input('POST','DB','UserAddService','INT',0,1,0,0);
				$NewRowInfo['UserChangeStatus']=Get_Input('POST','DB','UserChangeStatus','INT',0,1,0,0);
				$NewRowInfo['UserActivateNextService']=Get_Input('POST','DB','UserActivateNextService','INT',0,1,0,0);
				$NewRowInfo['UserDelete']=Get_Input('POST','DB','UserDelete','INT',0,1,0,0);
				$NewRowInfo['UserAddPayment']=Get_Input('POST','DB','UserAddPayment','INT',0,1,0,0);
				$NewRowInfo['UserSendSMS']=Get_Input('POST','DB','UserSendSMS','INT',0,1,0,0);
				$NewRowInfo['UserUpdateInfo']=Get_Input('POST','DB','UserUpdateInfo','INT',0,1,0,0);				
				
				
				$OldRowInfo= LoadRowInfo("Hwebservice_user","WebService_User_Id='".$NewRowInfo['WebService_User_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hwebservice_user set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="WebService_Username='".$NewRowInfo['WebService_Username']."',";
				$sql.="WebService_UserPass='".$NewRowInfo['WebService_UserPass']."',";
				$sql.="PermitIP='".$NewRowInfo['PermitIP']."',";
				$sql.="UserCreate='".$NewRowInfo['UserCreate']."',";
				$sql.="UserChangePassword='".$NewRowInfo['UserChangePassword']."',";
				$sql.="UserAddService='".$NewRowInfo['UserAddService']."',";
				$sql.="UserChangeStatus='".$NewRowInfo['UserChangeStatus']."',";
				$sql.="UserActivateNextService='".$NewRowInfo['UserActivateNextService']."',";
				$sql.="UserDelete='".$NewRowInfo['UserDelete']."',";
				$sql.="UserAddPayment='".$NewRowInfo['UserAddPayment']."',";
				$sql.="UserSendSMS='".$NewRowInfo['UserSendSMS']."',";
				$sql.="UserUpdateInfo='".$NewRowInfo['UserUpdateInfo']."'";
				$sql.=" Where ";
				$sql.="(WebService_User_Id='".$NewRowInfo['WebService_User_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','WebService',$NewRowInfo['WebService_User_Id'],'WebService',"Update Fail,Table=WebService affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=WebService affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'WebService',$NewRowInfo['WebService_User_Id'],'WebService')){
					logunfair("UnFair",'WebService',$NewRowInfo['WebService_User_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
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
