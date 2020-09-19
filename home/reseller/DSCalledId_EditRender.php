<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSCalledIdEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSCalledIdEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.CalledId.View");
				$CalledId_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,CalledId_Id,CalledIdName,CalledIdValue ".
				"From Hcalledid where CalledId_Id='$CalledId_Id'";
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
				DSDebug(1,"DSCalledIdEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.CalledId.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['CalledIdName']=Get_Input('POST','DB','CalledIdName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['CalledIdValue']=trim(Get_Input('POST','DB','CalledIdValue','STR',0,50,0,0),",");

				//----------------------
				$sql= "insert Hcalledid set ";
				$sql.="CalledIdName='".$NewRowInfo['CalledIdName']."',";
				$sql.="CalledIdValue='".$NewRowInfo['CalledIdValue']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','CalledId',$RowId,'CalledId');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSCalledIdEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.CalledId.Edit");
				$NewRowInfo=array();
				$NewRowInfo['CalledId_Id']=Get_Input('POST','DB','CalledId_Id','INT',1,4294967295,0,0);

				$NewRowInfo['CalledIdName']=Get_Input('POST','DB','CalledIdName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['CalledIdValue']=trim(Get_Input('POST','DB','CalledIdValue','STR',0,50,0,0),",");

				$OldRowInfo= LoadRowInfo("Hcalledid","CalledId_Id='".$NewRowInfo['CalledId_Id']."'");
				
				//----------------------
				
				$sql= "Update Hcalledid set ";
				//$sql.="CalledIdName='".$NewRowInfo['CalledIdName']."',";
				$sql.="CalledIdValue='".$NewRowInfo['CalledIdValue']."'";
				$sql.=" Where ";
				$sql.="(CalledId_Id='".$NewRowInfo['CalledId_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','CalledId',$NewRowInfo['CalledId_Id'],'CalledId',"Update Fail,Table=CalledId affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=CalledId affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
				
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'CalledId',$NewRowInfo['CalledId_Id'],'CalledId')){
					logunfair("UnFair",'CalledId',$NewRowInfo['CalledId_Id'],'',"");
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
