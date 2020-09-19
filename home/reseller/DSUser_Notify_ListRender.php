<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_DailyUsage_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);


switch ($act) {
	case "list":
				DSDebug(0,"DSUser_DailyUsage_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Notify.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$SortStr="Order by User_SMSHistory_Id Desc";
				DSGridRender_Sql(100,"SELECT  User_SMSHistory_Id,{$DT}DateTimeStr(CreateDT) As CreateDT,Status,{$DT}DateTimeStr(SendDT) As SendDT, ".
					"{$DT}DateTimeStr(ExpireDT) as ExpireDT,NotifyName,u_sh.Mobile,Message,Res,Creator ".
					"From Huser_smshistory u_sh left join Hnotify n on u_sh.Notify_Id=n.Notify_Id ".
					" left join Huser u on u_sh.User_Id=u.User_Id ".
					"where (u_sh.User_Id=$User_Id) $sqlfilter $SortStr ",
					"User_SMSHistory_Id",
					"User_SMSHistory_Id,CreateDT,Status,SendDT,ExpireDT,NotifyName,Mobile,Message,Res,Creator",
					"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>