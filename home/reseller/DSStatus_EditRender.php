<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSStatusItemEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update","SelectNewStatus"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSStatusItemEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.Status.View");
				$Status_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Status_Id,ISEnable,StatusName,UserStatus,NewStatus_Id,InitialStatus,CanWebLogin,CanAddService,IsBusyPort,PortStatus,AfterStatusSMS,SMSExpireTime,ServiceStatus  from Hstatus where Status_Id='$Status_Id'";
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

				DSDebug(1,"DSStatusItemEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.Status.Add");
				$NewRowInfo=array();
				$NewRowInfo['StatusName']=Get_Input('POST','DB','StatusName','STR',1,64,0,0);
				$NewRowInfo['UserStatus']=Get_Input('POST','DB','UserStatus','ARRAY',array('Enable','Disable','ChangeOnFirstConnect','AddFreeServiceByNAS'),0,0,0);
				if(($NewRowInfo['UserStatus']=='ChangeOnFirstConnect')||($NewRowInfo['UserStatus']=='AddFreeServiceByNAS')){
					$NewRowInfo['NewStatus_Id']=Get_Input('POST','DB','NewStatus_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select UserStatus from Hstatus where Status_Id='".$NewRowInfo['NewStatus_Id']."'")!='Enable')
						ExitError("وضعیت جدید می بایست وضعیت فعال باشد");
				}
				else
					$NewRowInfo['NewStatus_Id']=0;
				
				$NewRowInfo['InitialStatus']=Get_Input('POST','DB','InitialStatus','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanWebLogin']=Get_Input('POST','DB','CanWebLogin','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanAddService']=Get_Input('POST','DB','CanAddService','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['IsBusyPort']=Get_Input('POST','DB','IsBusyPort','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['PortStatus']=Get_Input('POST','DB','PortStatus','ARRAY',array("GoToBusy",'Busy','GoToFree','Free','Waiting','Reserve','None'),0,0,0);
				$NewRowInfo['AfterStatusSMS']=Get_Input('POST','DB','AfterStatusSMS','STR',0,250,0,0);
				if($NewRowInfo['AfterStatusSMS']!="")
					$NewRowInfo['SMSExpireTime']=Get_Input('POST','DB','SMSExpireTime','INT',60,99999,0,0);
				else
					$NewRowInfo['SMSExpireTime']=0;
				$NewRowInfo['ServiceStatus']=Get_Input('POST','DB','ServiceStatus','ARRAY',array("Erased",'ReadytoActivateSoft','Hard','Soft','Active'),0,0,0);
				//----------------------
				$sql= "insert Hstatus set ";
				$sql.="StatusName='".$NewRowInfo['StatusName']."',";
				$sql.="UserStatus='".$NewRowInfo['UserStatus']."',";
				$sql.="NewStatus_Id='".$NewRowInfo['NewStatus_Id']."',";
				$sql.="InitialStatus='".$NewRowInfo['InitialStatus']."',";
				$sql.="CanWebLogin='".$NewRowInfo['CanWebLogin']."',";
				$sql.="CanAddService='".$NewRowInfo['CanAddService']."',";
				$sql.="IsBusyPort='".$NewRowInfo['IsBusyPort']."',";
				$sql.="PortStatus='".$NewRowInfo['PortStatus']."',";
				$sql.="AfterStatusSMS='".$NewRowInfo['AfterStatusSMS']."',";
				$sql.="SMSExpireTime='".$NewRowInfo['SMSExpireTime']."',";
				$sql.="ServiceStatus='".$NewRowInfo['ServiceStatus']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Status_Id']=$RowId;
				// $res = $conn->sql->query("Insert Ignore Hpermititem set PermitGroup='Visp',PermitItemName='Visp.User.Status.ChangeStatus.".$NewRowInfo['Status_Id']."'");
				// $res = $conn->sql->query("Insert Ignore Hreseller_permit(Reseller_Id,Visp_Id,ISPermit,PermitItem_Id) SELECT rg.Reseller_Id, v.Visp_Id, if(rg.Reseller_Id=1,'Yes','No'), PermitItem_Id FROM Hpermititem pi, Hreseller rg,Hvisp v WHERE PermitGroup = 'Visp'");
				logdbinsert($NewRowInfo,'Add','Status',$RowId,'Status');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSStatusItemEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.Status.Edit");
				$NewRowInfo=array();

				$NewRowInfo['Status_Id']=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);
				//if(($NewRowInfo['Status_Id']==1)||($NewRowInfo['Status_Id']==2))					ExitError('This Status is not editable');
				$NewRowInfo['StatusName']=Get_Input('POST','DB','StatusName','STR',1,64,0,0);
				$NewRowInfo['UserStatus']=Get_Input('POST','DB','UserStatus','ARRAY',array('Enable','Disable','ChangeOnFirstConnect','AddFreeServiceByNAS'),0,0,0);
				
				if($NewRowInfo['UserStatus']!='Enable'){
					$n=DBSelectAsString("Select count(1) from Hstatus where NewStatus_Id='".$NewRowInfo['Status_Id']."'");
					if($n>0)
						ExitError("This status is used as NewStatus for $n status and UserStatus of itself must be Enable");
				}
				
				if(($NewRowInfo['UserStatus']=='ChangeOnFirstConnect')||($NewRowInfo['UserStatus']=='AddFreeServiceByNAS')){
					$NewRowInfo['NewStatus_Id']=Get_Input('POST','DB','NewStatus_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select UserStatus from Hstatus where Status_Id='".$NewRowInfo['NewStatus_Id']."'")!='Enable')
						ExitError("وضعیت جدید می بایست وضعیت فعال باشد");
				}
				else
					$NewRowInfo['NewStatus_Id']=0;
				
				$NewRowInfo['InitialStatus']=Get_Input('POST','DB','InitialStatus','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanWebLogin']=Get_Input('POST','DB','CanWebLogin','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanAddService']=Get_Input('POST','DB','CanAddService','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['IsBusyPort']=Get_Input('POST','DB','IsBusyPort','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['PortStatus']=Get_Input('POST','DB','PortStatus','ARRAY',array("GoToBusy",'Busy','GoToFree','Free','Waiting','Reserve','None'),0,0,0);
				$NewRowInfo['AfterStatusSMS']=Get_Input('POST','DB','AfterStatusSMS','STR',0,250,0,0);
				if($NewRowInfo['AfterStatusSMS']<>"")
					$NewRowInfo['SMSExpireTime']=Get_Input('POST','DB','SMSExpireTime','INT',60,99999,0,0);
				else
					$NewRowInfo['SMSExpireTime']=0;
				$NewRowInfo['ServiceStatus']=Get_Input('POST','DB','ServiceStatus','ARRAY',array("Erased",'ReadytoActivateSoft','Hard','Soft','Active'),0,0,0);

				$OldRowInfo= LoadRowInfo("Hstatus","Status_Id='".$NewRowInfo['Status_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "update Hstatus set ";
				$sql.="StatusName='".$NewRowInfo['StatusName']."',";
				$sql.="UserStatus='".$NewRowInfo['UserStatus']."',";
				$sql.="NewStatus_Id='".$NewRowInfo['NewStatus_Id']."',";
				$sql.="InitialStatus='".$NewRowInfo['InitialStatus']."',";
				$sql.="CanWebLogin='".$NewRowInfo['CanWebLogin']."',";
				$sql.="CanAddService='".$NewRowInfo['CanAddService']."',";
				$sql.="IsBusyPort='".$NewRowInfo['IsBusyPort']."',";
				$sql.="PortStatus='".$NewRowInfo['PortStatus']."',";
				$sql.="AfterStatusSMS='".$NewRowInfo['AfterStatusSMS']."',";
				$sql.="SMSExpireTime='".$NewRowInfo['SMSExpireTime']."',";
				$sql.="ServiceStatus='".$NewRowInfo['ServiceStatus']."'";
				$sql.=" Where ";
				$sql.="(Status_Id='".$NewRowInfo['Status_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Status',$NewRowInfo['Status_Id'],'Status',"Update Fail,Table=StatusItem affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=StatusItem affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Status',$NewRowInfo['Status_Id'],'Status')){
					logunfair("UnFair",'Status',$NewRowInfo['Status_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	case "SelectNewStatus":
				exitifnotpermit(0,"Admin.User.Status.View");
				DSDebug(1,"DSStatusItemEditRender SelectNewStatus *****************");
				$Status_Id=Get_Input('GET','DB','Status_Id','INT',0,4294967295,0,0);
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Status_Id,StatusName FROM Hstatus where Status_Id<>$Status_Id and UserStatus='Enable' order by StatusName ASC";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
