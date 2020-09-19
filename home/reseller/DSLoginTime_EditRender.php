<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSLoginTimeEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSLoginTimeEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.LoginTime.View");
				$LoginTime_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,LoginTime_Id,LoginTimeName,LoginTimeValue ".
				"From Hlogintime where LoginTime_Id='$LoginTime_Id'";
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
				DSDebug(1,"DSLoginTimeEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.LoginTime.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['LoginTimeName']=Get_Input('POST','DB','LoginTimeName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['LoginTimeValue']=Get_Input('POST','DB','LoginTimeValue','STR',1,250,0,0);

				//----------------------
				$sql= "insert Hlogintime set ";
				$sql.="LoginTimeName='".$NewRowInfo['LoginTimeName']."',";
				$sql.="LoginTimeValue='".$NewRowInfo['LoginTimeValue']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','LoginTime',$RowId,'LoginTime');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSLoginTimeEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.LoginTime.Edit");
				$NewRowInfo=array();
				$NewRowInfo['LoginTime_Id']=Get_Input('POST','DB','LoginTime_Id','INT',1,4294967295,0,0);
				//$NewRowInfo['LoginTimeName']=Get_Input('POST','DB','LoginTimeName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['LoginTimeValue']=Get_Input('POST','DB','LoginTimeValue','STR',1,250,0,0);

				$OldRowInfo= LoadRowInfo("Hlogintime","LoginTime_Id='".$NewRowInfo['LoginTime_Id']."'");
				
				//----------------------
				
				$sql= "Update Hlogintime set ";
				//$sql.="LoginTimeName='".$NewRowInfo['LoginTimeName']."',";
				$sql.="LoginTimeValue='".$NewRowInfo['LoginTimeValue']."'";
				$sql.=" Where ";
				$sql.="(LoginTime_Id='".$NewRowInfo['LoginTime_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','LoginTime',$NewRowInfo['LoginTime_Id'],'LoginTime',"Update Fail,Table=LoginTime affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=LoginTime affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'LoginTime',$NewRowInfo['LoginTime_Id'],'LoginTime')){
					logunfair("UnFair",'LoginTime',$NewRowInfo['LoginTime_Id'],'',"");
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
