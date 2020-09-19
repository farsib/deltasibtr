<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_UserNotify_ReportingListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_UserNotifyList_ListRender->List ********************************************");
				exitifnotpermit(0,"Report.User.Notify.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by User_SMSHistory_Id Desc";
				DSGridRender_Sql(100,"SELECT  User_SMSHistory_Id,UserName,{$DT}DateTimeStr(CreateDT) As CreateDT,Status,{$DT}DateTimeStr(SendDT) As SendDT, ".
					"{$DT}DateTimeStr(ExpireDT) as ExpireDT,NotifyName,u_sh.Mobile,Message,Res,Creator ".
					"From Huser_smshistory u_sh left join Hnotify n on u_sh.Notify_Id=n.Notify_Id ".
					" left join Huser u on u_sh.User_Id=u.User_Id ".
					"where 1 ".$sqlfilter." $SortStr ",
					"User_SMSHistory_Id",
					"User_SMSHistory_Id,UserName,CreateDT,Status,SendDT,ExpireDT,NotifyName,Mobile,Message,Res,Creator",
					"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
