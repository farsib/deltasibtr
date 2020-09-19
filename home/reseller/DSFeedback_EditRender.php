<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSFeedbackEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSFeedbackEditRender Load ********************************************");
				exitifnotpermit(0,"CRM.Feedback.View");
				$User_Feedback_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="Select User_Feedback_Id,{$DT}DateTimeStr(RequestCDT) As RequestCDT,Status,{$DT}DateTimeStr(ReplyCDT) As ReplyCDT,".
					"Username,INET_NToA(IP) As IP,OnlineUsername,Email,MobileNo,RequestType,ServiceType,KeyStr,Message,Reply ".
					"From Huser_feedback where User_Feedback_Id='$User_Feedback_Id'";
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
				DSDebug(1,"DSFeedbackEditRender Update ******************************************");
				exitifnotpermit(0,"CRM.Feedback.Edit");
				$NewRowInfo=array();
				$NewRowInfo['User_Feedback_Id']=Get_Input('POST','DB','User_Feedback_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Reply']=Get_Input('POST','DB','Reply','STR',1,1024,0,0);
				$OldRowInfo= LoadRowInfo("Huser_feedback","User_Feedback_Id='".$NewRowInfo['User_Feedback_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Huser_feedback set ";
				$sql.="Status='Replied', ";
				$sql.="Reply='".$NewRowInfo['Reply']."',";
				$sql.="ReplyCDT=now() ";
				$sql.=" Where ";
				$sql.="(User_Feedback_Id='".$NewRowInfo['User_Feedback_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Feedback',$NewRowInfo['User_Feedback_Id'],'Feedback',"Update Fail,Table=Feedback affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Feedback affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Feedback',$NewRowInfo['User_Feedback_Id'],'Feedback')){
					logunfair("UnFair",'Feedback',$NewRowInfo['User_Feedback_Id'],'',"");
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
