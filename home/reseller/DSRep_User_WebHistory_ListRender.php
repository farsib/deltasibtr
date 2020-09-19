<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_WebHistory_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.WebHistory.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_WebHistory_ListRender->List ********************************************");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by User_WebConnection_Id Desc";
				
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				
				$sql="select User_WebConnection_Id,Username,INET_NtoA(ClientIP) as ClientIP,{$DT}DateTimeStr(uw.StartDT) As StartDT,{$DT}DateTimeStr(uw.EndDT) As EndDT,BrowserInfo,NumRequest from Huser_webconnection uw ".
					"join Huser u on uw.User_Id=u.User_Id ".
					(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=u.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ":"").
					"where 1 ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"User_WebConnection_Id","User_WebConnection_Id,Username,ClientIP,StartDT,EndDT,BrowserInfo,NumRequest","","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
