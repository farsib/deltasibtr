<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSDebitControlEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSDebitControlEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.DebitControl.View");
				$DebitControl_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,DebitControl_Id,DebitControlName,MaxDebit,MaxDelay ".
				"From Hdebitcontrol where DebitControl_Id='$DebitControl_Id'";
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
				DSDebug(1,"DSDebitControlEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.DebitControl.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['DebitControlName']=Get_Input('POST','DB','DebitControlName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['MaxDebit']=Get_Input('POST','DB','MaxDebit','PRC',1,14,0,0);
				$NewRowInfo['MaxDelay']=Get_Input('POST','DB','MaxDelay','INT',1,4294967295,0,0);
				
				//----------------------
				$sql= "insert Hdebitcontrol set ";
				$sql.="DebitControlName='".$NewRowInfo['DebitControlName']."',";
				$sql.="MaxDebit='".$NewRowInfo['MaxDebit']."',";
				$sql.="MaxDelay='".$NewRowInfo['MaxDelay']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','DebitControl',$RowId,'DebitControl');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSDebitControlEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.DebitControl.Edit");
				$NewRowInfo=array();
				$NewRowInfo['DebitControl_Id']=Get_Input('POST','DB','DebitControl_Id','INT',1,4294967295,0,0);
				//$NewRowInfo['DebitControlName']=Get_Input('POST','DB','DebitControlName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['MaxDebit']=Get_Input('POST','DB','MaxDebit','PRC',1,14,0,0);
				$NewRowInfo['MaxDelay']=Get_Input('POST','DB','MaxDelay','INT',1,4294967295,0,0);

				$OldRowInfo= LoadRowInfo("Hdebitcontrol","DebitControl_Id='".$NewRowInfo['DebitControl_Id']."'");
				
				//----------------------
				
				$sql= "Update Hdebitcontrol set ";
				$sql.="MaxDebit='".$NewRowInfo['MaxDebit']."',";
				$sql.="MaxDelay='".$NewRowInfo['MaxDelay']."'";
				$sql.=" Where ";
				$sql.="(DebitControl_Id='".$NewRowInfo['DebitControl_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','DebitControl',$NewRowInfo['DebitControl_Id'],'DebitControl',"Update Fail,Table=DebitControl affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=DebitControl affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'DebitControl',$NewRowInfo['DebitControl_Id'],'DebitControl')){
					logunfair("UnFair",'DebitControl',$NewRowInfo['DebitControl_Id'],'',"");
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
