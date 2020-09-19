<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSFeedbackListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSFeedbackListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Feedback.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT User_Feedback_Id,{$DT}DateTimeStr(RequestCDT) As RequestCDT,Status,{$DT}DateTimeStr(ReplyCDT) As ReplyCDT,".
					"Username,INET_NToA(IP) As IP,OnlineUsername,Email,MobileNo,RequestType,ServiceType,KeyStr".
					" From Huser_feedback Where 1 ".$sqlfilter." $SortStr ",
					"User_Feedback_Id","User_Feedback_Id,RequestCDT,Status,ReplyCDT,Username,IP,OnlineUsername,Email,MobileNo,RequestType,ServiceType,KeyStr",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSCenterListRender Delete ******************************************");
				exitifnotpermit(0,"CRM.Feedback.Delete");
				$NewRowInfo=array();
				$NewRowInfo['User_Feedback_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$ar=DBDelete('delete from Huser_feedback Where User_Feedback_Id='.$NewRowInfo['User_Feedback_Id']);
				logdbdelete($NewRowInfo,'Delete','Feedback',$NewRowInfo['User_Feedback_Id'],'');
				echo "OK~";
		break;	   
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>