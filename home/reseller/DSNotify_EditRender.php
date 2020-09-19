<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSNotifyEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

/*
http://www.tutorialspoint.com/mysql/mysql-regexps.htm
Pattern	What the pattern matches
^	Beginning of string
$	End of string
.	Any single character
[...]	Any character listed between the square brackets
[^...]	Any character not listed between the square brackets
p1|p2|p3	Alternation; matches any of the patterns p1, p2, or p3
*	Zero or more instances of preceding element
+	One or more instances of preceding element
{n}	n instances of preceding element
{m,n}	m through n instances of preceding element
*/


$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSNotifyEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Message.Notify.View");
				$Notify_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Notify_Id,NotifyName,NormalMinHourAfterPriorSend,FinishMinHourAfterPriorSend,NotifyType,MinCreditTime,MinCreditTraffic,MinActiveDays,ISEnable,".
					"CreditFinishSendTime,ServiceExpireSendTime,BeforeCreditFinishMessage,AfterCreditFinishMessage, ".
					"BeforeServiceExpireMessage,AfterServiceExpireMessage,ServiceExpireLastSeenHours,CreditFinishLastSeenHours, ".
					"MinUserDebit,UserDebitLastSeenHours,UserDebitMessage,UserDebitSendTime ".
					"from Hnotify where Notify_Id='$Notify_Id'";
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
				DSDebug(1,"DSNotifyEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Message.Notify.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['NotifyName']=Get_Input('POST','DB','NotifyName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['NormalMinHourAfterPriorSend']=Get_Input('POST','DB','NormalMinHourAfterPriorSend','INT',1,99999,0,0);
				$NewRowInfo['FinishMinHourAfterPriorSend']=Get_Input('POST','DB','FinishMinHourAfterPriorSend','INT',1,99999,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['NotifyType']=Get_Input('POST','DB','NotifyType','ARRAY',array("CreditFinishNotify","ServiceExpireNotify",'UserDebitNotify'),0,0,0);
				if($NewRowInfo['NotifyType']=='CreditFinishNotify'){
					$NewRowInfo['MinCreditTime']=Get_Input('POST','DB','MinCreditTime','INT',1,99999999,0,0);
					$NewRowInfo['MinCreditTraffic']=Get_Input('POST','DB','MinCreditTraffic','INT',1,99999999,0,0);
					$NewRowInfo['CreditFinishLastSeenHours']=Get_Input('POST','DB','CreditFinishLastSeenHours','INT',1,999,0,0);
					$NewRowInfo['BeforeCreditFinishMessage']=Get_Input('POST','DB1','BeforeCreditFinishMessage','STR',1,250,0,0);
					$NewRowInfo['AfterCreditFinishMessage']=Get_Input('POST','DB1','AfterCreditFinishMessage','STR',1,250,0,0);
					$NewRowInfo['CreditFinishSendTime']=Get_Input('POST','DB','CreditFinishSendTime','STR',1,120,0,0);
					//$t=DBSelectAsString("Select DSLoginTime('".$NewRowInfo['CreditFinishSendTime']."')");
					//if($t<0) ExitError("Incorrect SendTime");
					$NewRowInfo['MinActiveDays']='0';
					$NewRowInfo['ServiceExpireLastSeenHours']='0';
					$NewRowInfo['BeforeServiceExpireMessage']='';
					$NewRowInfo['AfterServiceExpireMessage']='';
					$NewRowInfo['ServiceExpireSendTime']='Al';
					$NewRowInfo['MinUserDebit']='0';
					$NewRowInfo['UserDebitLastSeenHours']='0';
					$NewRowInfo['UserDebitMessage']='';
					$NewRowInfo['UserDebitSendTime']='';
					
				}
				else
				if($NewRowInfo['NotifyType']=='ServiceExpireNotify'){
					$NewRowInfo['MinCreditTime']='0';
					$NewRowInfo['MinCreditTraffic']='0';
					$NewRowInfo['CreditFinishLastSeenHours']='0';
					$NewRowInfo['BeforeCreditFinishMessage']='';
					$NewRowInfo['AfterCreditFinishMessage']='';
					$NewRowInfo['CreditFinishSendTime']='Al';
					$NewRowInfo['MinUserDebit']='0';
					$NewRowInfo['UserDebitLastSeenHours']='0';
					$NewRowInfo['UserDebitMessage']='';
					$NewRowInfo['UserDebitSendTime']='';

					$NewRowInfo['MinActiveDays']=Get_Input('POST','DB','MinActiveDays','INT',1,999,0,0);
					$NewRowInfo['ServiceExpireLastSeenHours']=Get_Input('POST','DB','ServiceExpireLastSeenHours','INT',1,999,0,0);
					$NewRowInfo['BeforeServiceExpireMessage']=Get_Input('POST','DB1','BeforeServiceExpireMessage','STR',1,250,0,0);
					$NewRowInfo['AfterServiceExpireMessage']=Get_Input('POST','DB1','AfterServiceExpireMessage','STR',1,250,0,0);
					$NewRowInfo['ServiceExpireSendTime']=Get_Input('POST','DB','ServiceExpireSendTime','STR',1,120,0,0);
					//$t=DBSelectAsString("Select DSLoginTime('".$NewRowInfo['ServiceExpireSendTime']."')");
					//if($t<0) ExitError("Incorrect SendTime");
				}
				else
				if($NewRowInfo['NotifyType']=='UserDebitNotify'){
					$NewRowInfo['MinCreditTime']='0';
					$NewRowInfo['MinCreditTraffic']='0';
					$NewRowInfo['CreditFinishLastSeenHours']='0';
					$NewRowInfo['BeforeCreditFinishMessage']='';
					$NewRowInfo['AfterCreditFinishMessage']='';
					$NewRowInfo['CreditFinishSendTime']='Al';
					$NewRowInfo['MinActiveDays']='0';
					$NewRowInfo['ServiceExpireLastSeenHours']='0';
					$NewRowInfo['BeforeServiceExpireMessage']='';
					$NewRowInfo['AfterServiceExpireMessage']='';
					$NewRowInfo['ServiceExpireSendTime']='';
					$NewRowInfo['MinUserDebit']=Get_Input('POST','DB','MinUserDebit','INT',1,999999999,0,0);
					$NewRowInfo['UserDebitLastSeenHours']=Get_Input('POST','DB','UserDebitLastSeenHours','INT',1,999,0,0);
					$NewRowInfo['UserDebitMessage']=Get_Input('POST','DB1','UserDebitMessage','STR',1,250,0,0);
					$NewRowInfo['UserDebitSendTime']=Get_Input('POST','DB','UserDebitSendTime','STR',1,120,0,0);
					//$t=DBSelectAsString("Select DSLoginTime('".$NewRowInfo['UserDebitSendTime']."')");
					//if($t<0) ExitError("Incorrect SendTime");
				}
				
				
				//----------------------
				$sql= "Insert Hnotify set ";
				$sql.="NotifyName='".$NewRowInfo['NotifyName']."',";
				$sql.="NormalMinHourAfterPriorSend='".$NewRowInfo['NormalMinHourAfterPriorSend']."',";
				$sql.="FinishMinHourAfterPriorSend='".$NewRowInfo['FinishMinHourAfterPriorSend']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="NotifyType='".$NewRowInfo['NotifyType']."',";
				$sql.="MinCreditTime='".$NewRowInfo['MinCreditTime']."',";
				$sql.="MinCreditTraffic='".$NewRowInfo['MinCreditTraffic']."',";
				$sql.="CreditFinishLastSeenHours='".$NewRowInfo['CreditFinishLastSeenHours']."',";
				$sql.="BeforeCreditFinishMessage='".$NewRowInfo['BeforeCreditFinishMessage']."',";
				$sql.="AfterCreditFinishMessage='".$NewRowInfo['AfterCreditFinishMessage']."',";
				$sql.="CreditFinishSendTime='".$NewRowInfo['CreditFinishSendTime']."',";
				$sql.="MinActiveDays='".$NewRowInfo['MinActiveDays']."',";
				$sql.="ServiceExpireLastSeenHours='".$NewRowInfo['ServiceExpireLastSeenHours']."',";
				$sql.="BeforeServiceExpireMessage='".$NewRowInfo['BeforeServiceExpireMessage']."',";
				$sql.="AfterServiceExpireMessage='".$NewRowInfo['AfterServiceExpireMessage']."',";
				$sql.="ServiceExpireSendTime='".$NewRowInfo['ServiceExpireSendTime']."',";
				$sql.="MinUserDebit='".$NewRowInfo['MinUserDebit']."',";
				$sql.="UserDebitLastSeenHours='".$NewRowInfo['UserDebitLastSeenHours']."',";
				$sql.="UserDebitMessage='".$NewRowInfo['UserDebitMessage']."',";
				$sql.="UserDebitSendTime='".$NewRowInfo['UserDebitSendTime']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Notify_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','Notify',$RowId,'Notify');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSNotifyEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Message.Notify.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Notify_Id']=Get_Input('POST','DB','Notify_Id','INT',1,4294967295,0,0);
				$NewRowInfo['NotifyName']=Get_Input('POST','DB','NotifyName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['NormalMinHourAfterPriorSend']=Get_Input('POST','DB','NormalMinHourAfterPriorSend','INT',1,99999,0,0);
				$NewRowInfo['FinishMinHourAfterPriorSend']=Get_Input('POST','DB','FinishMinHourAfterPriorSend','INT',1,99999,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['NotifyType']=Get_Input('POST','DB','NotifyType','ARRAY',array("CreditFinishNotify","ServiceExpireNotify",'UserDebitNotify'),0,0,0);
				if($NewRowInfo['NotifyType']=='CreditFinishNotify'){
					$NewRowInfo['MinCreditTime']=Get_Input('POST','DB','MinCreditTime','INT',1,99999999,0,0);
					$NewRowInfo['MinCreditTraffic']=Get_Input('POST','DB','MinCreditTraffic','INT',1,99999999,0,0);
					$NewRowInfo['CreditFinishLastSeenHours']=Get_Input('POST','DB','CreditFinishLastSeenHours','INT',1,999,0,0);
					$NewRowInfo['BeforeCreditFinishMessage']=Get_Input('POST','DB1','BeforeCreditFinishMessage','STR',1,250,0,0);
					$NewRowInfo['AfterCreditFinishMessage']=Get_Input('POST','DB1','AfterCreditFinishMessage','STR',1,250,0,0);
					$NewRowInfo['CreditFinishSendTime']=Get_Input('POST','DB','CreditFinishSendTime','STR',1,120,0,0);
					//$t=DBSelectAsString("Select DSLoginTime('".$NewRowInfo['CreditFinishSendTime']."')");
					//if($t<0) ExitError("Incorrect SendTime");
					$NewRowInfo['MinActiveDays']='0';
					$NewRowInfo['ServiceExpireLastSeenHours']='0';
					$NewRowInfo['BeforeServiceExpireMessage']='';
					$NewRowInfo['AfterServiceExpireMessage']='';
					$NewRowInfo['ServiceExpireSendTime']='Al';
					$NewRowInfo['MinUserDebit']='0';
					$NewRowInfo['UserDebitLastSeenHours']='0';
					$NewRowInfo['UserDebitMessage']='';
					$NewRowInfo['UserDebitSendTime']='';
					
				}
				else
				if($NewRowInfo['NotifyType']=='ServiceExpireNotify'){
					$NewRowInfo['MinCreditTime']='0';
					$NewRowInfo['MinCreditTraffic']='0';
					$NewRowInfo['CreditFinishLastSeenHours']='0';
					$NewRowInfo['BeforeCreditFinishMessage']='';
					$NewRowInfo['AfterCreditFinishMessage']='';
					$NewRowInfo['CreditFinishSendTime']='Al';
					$NewRowInfo['MinUserDebit']='0';
					$NewRowInfo['UserDebitLastSeenHours']='0';
					$NewRowInfo['UserDebitMessage']='';
					$NewRowInfo['UserDebitSendTime']='';

					$NewRowInfo['MinActiveDays']=Get_Input('POST','DB','MinActiveDays','INT',1,999,0,0);
					$NewRowInfo['ServiceExpireLastSeenHours']=Get_Input('POST','DB','ServiceExpireLastSeenHours','INT',1,999,0,0);
					$NewRowInfo['BeforeServiceExpireMessage']=Get_Input('POST','DB1','BeforeServiceExpireMessage','STR',1,250,0,0);
					$NewRowInfo['AfterServiceExpireMessage']=Get_Input('POST','DB1','AfterServiceExpireMessage','STR',1,250,0,0);
					$NewRowInfo['ServiceExpireSendTime']=Get_Input('POST','DB','ServiceExpireSendTime','STR',1,120,0,0);
					//$t=DBSelectAsString("Select DSLoginTime('".$NewRowInfo['ServiceExpireSendTime']."')");
					//if($t<0) ExitError("Incorrect SendTime");
				}
				else
				if($NewRowInfo['NotifyType']=='UserDebitNotify'){
					$NewRowInfo['MinCreditTime']='0';
					$NewRowInfo['MinCreditTraffic']='0';
					$NewRowInfo['CreditFinishLastSeenHours']='0';
					$NewRowInfo['BeforeCreditFinishMessage']='';
					$NewRowInfo['AfterCreditFinishMessage']='';
					$NewRowInfo['CreditFinishSendTime']='Al';
					$NewRowInfo['MinActiveDays']='0';
					$NewRowInfo['ServiceExpireLastSeenHours']='0';
					$NewRowInfo['BeforeServiceExpireMessage']='';
					$NewRowInfo['AfterServiceExpireMessage']='';
					$NewRowInfo['ServiceExpireSendTime']='';
					$NewRowInfo['MinUserDebit']=Get_Input('POST','DB','MinUserDebit','INT',1,999999999,0,0);
					$NewRowInfo['UserDebitLastSeenHours']=Get_Input('POST','DB','UserDebitLastSeenHours','INT',1,999,0,0);
					$NewRowInfo['UserDebitMessage']=Get_Input('POST','DB1','UserDebitMessage','STR',1,250,0,0);
					$NewRowInfo['UserDebitSendTime']=Get_Input('POST','DB','UserDebitSendTime','STR',1,120,0,0);
					//$t=DBSelectAsString("Select DSLoginTime('".$NewRowInfo['UserDebitSendTime']."')");
					//if($t<0) ExitError("Incorrect SendTime");
				}
				
				
				$OldRowInfo= LoadRowInfo("Hnotify","Notify_Id='".$NewRowInfo['Notify_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hnotify set  ";
				//$sql.="NotifyName='".$NewRowInfo['NotifyName']."',";
				$sql.="NormalMinHourAfterPriorSend='".$NewRowInfo['NormalMinHourAfterPriorSend']."',";
				$sql.="FinishMinHourAfterPriorSend='".$NewRowInfo['FinishMinHourAfterPriorSend']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="NotifyType='".$NewRowInfo['NotifyType']."',";
				$sql.="MinCreditTime='".$NewRowInfo['MinCreditTime']."',";
				$sql.="MinCreditTraffic='".$NewRowInfo['MinCreditTraffic']."',";
				$sql.="CreditFinishLastSeenHours='".$NewRowInfo['CreditFinishLastSeenHours']."',";
				$sql.="BeforeCreditFinishMessage='".$NewRowInfo['BeforeCreditFinishMessage']."',";
				$sql.="AfterCreditFinishMessage='".$NewRowInfo['AfterCreditFinishMessage']."',";
				$sql.="CreditFinishSendTime='".$NewRowInfo['CreditFinishSendTime']."',";
				$sql.="MinActiveDays='".$NewRowInfo['MinActiveDays']."',";
				$sql.="ServiceExpireLastSeenHours='".$NewRowInfo['ServiceExpireLastSeenHours']."',";
				$sql.="BeforeServiceExpireMessage='".$NewRowInfo['BeforeServiceExpireMessage']."',";
				$sql.="AfterServiceExpireMessage='".$NewRowInfo['AfterServiceExpireMessage']."',";
				$sql.="ServiceExpireSendTime='".$NewRowInfo['ServiceExpireSendTime']."',";
				$sql.="MinUserDebit='".$NewRowInfo['MinUserDebit']."',";
				$sql.="UserDebitLastSeenHours='".$NewRowInfo['UserDebitLastSeenHours']."',";
				$sql.="UserDebitMessage='".$NewRowInfo['UserDebitMessage']."',";
				$sql.="UserDebitSendTime='".$NewRowInfo['UserDebitSendTime']."'";
				$sql.=" Where ";
				$sql.="(Notify_Id='".$NewRowInfo['Notify_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Notify',$NewRowInfo['Notify_Id'],'Notify',"Update Fail,Table=Notify affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Notify affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Notify',$NewRowInfo['Notify_Id'],'Notify')){
					logunfair("UnFair",'Notify',$NewRowInfo['Notify_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else
					echo "OK~";
        break;
	case "SelectNotifyInfoName":
				DSDebug(1,"DSNotifyEditRender SelectNotifyInfoName *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT NotifyInfo_Id,NotifyInfoName FROM Hnotifyinfo order by NotifyInfoName ASC","","NotifyInfo_Id,NotifyInfoName","","");
	
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
