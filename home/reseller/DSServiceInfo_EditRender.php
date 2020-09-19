<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServiceInfoEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSServiceInfoEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.ServiceInfo.View");
				$ServiceInfo_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,ServiceInfo_Id,ServiceInfoName,ServiceInfoValue,ServiceRate ".
				"From Hserviceinfo where ServiceInfo_Id='$ServiceInfo_Id'";
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
				DSDebug(1,"DSServiceInfoEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.ServiceInfo.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ServiceInfoName']=Get_Input('POST','DB','ServiceInfoName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['ServiceInfoValue']=Get_Input('POST','DB','ServiceInfoValue','STR',1,16,0,0);
				$NewRowInfo['ServiceRate']=Get_Input('POST','DB','ServiceRate','STR',1,16,0,0);

				//----------------------
				$sql= "insert Hserviceinfo set ";
				$sql.="ServiceInfoName='".$NewRowInfo['ServiceInfoName']."',";
				$sql.="ServiceInfoValue='".$NewRowInfo['ServiceInfoValue']."',";
				$sql.="ServiceRate='".$NewRowInfo['ServiceRate']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','ServiceInfo',$RowId,'ServiceInfo');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSServiceInfoEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.ServiceInfo.Edit");
				$NewRowInfo=array();
				$NewRowInfo['ServiceInfo_Id']=Get_Input('POST','DB','ServiceInfo_Id','INT',1,4294967295,0,0);
				
					
				$NewRowInfo['ServiceInfoName']=Get_Input('POST','DB','ServiceInfoName','STRENCHARNUMBER',1,32,0,0);
				
				$NewRowInfo['ServiceRate']=Get_Input('POST','DB','ServiceRate','STR',1,16,0,0);

				$OldRowInfo= LoadRowInfo("Hserviceinfo","ServiceInfo_Id='".$NewRowInfo['ServiceInfo_Id']."'");
				
				//----------------------
				
				$sql= "Update Hserviceinfo set ";
				//$sql.="ServiceInfoName='".$NewRowInfo['ServiceInfoName']."',";
				if($NewRowInfo['ServiceInfo_Id']!=1){
					$NewRowInfo['ServiceInfoValue']=Get_Input('POST','DB','ServiceInfoValue','STR',1,16,0,0);
					$sql.="ServiceInfoValue='".$NewRowInfo['ServiceInfoValue']."',";
				}
				
				$sql.="ServiceRate='".$NewRowInfo['ServiceRate']."'";
				$sql.=" Where ";
				$sql.="(ServiceInfo_Id='".$NewRowInfo['ServiceInfo_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','ServiceInfo',$NewRowInfo['ServiceInfo_Id'],'ServiceInfo',"Update Fail,Table=ServiceInfo affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=ServiceInfo affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'ServiceInfo',$NewRowInfo['ServiceInfo_Id'],'ServiceInfo')){
					logunfair("UnFair",'ServiceInfo',$NewRowInfo['ServiceInfo_Id'],'',"");
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
