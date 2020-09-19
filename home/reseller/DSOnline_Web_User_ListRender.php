<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_Web_User_ListRender.php.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list","DeleteSession"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(1,"DSOnline_Web_User_ListRender.php list ******************************************");
				//Permission -----------------
				exitifnotpermit(0,"Online.Web.User.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				
				DSGridRender_Sql(100,"SELECT Online_WebUser_Id,ow.Username,INET_NTOA(ClientIP) as ClientIP,{$DT}DateTimeStr(LoginDT) as LoginDT,{$DT}DateTimeStr(LastSeenDT) As LastSeenDT,".
				"BrowserInfo,NumRequest FROM  Tonline_webuser ow ".
				(($LReseller_Id!=1)?"join Huser u on ow.User_Id=u.User_Id join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=u.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ":"").
				"Where 1 $sqlfilter $SortStr",
				"Online_WebUser_Id",
				"Online_WebUser_Id,Username,ClientIP,LoginDT,LastSeenDT,BrowserInfo,NumRequest","","","");
       break;
	case "DeleteSession":
				DSDebug(1,"DSOnline_Web_User_ListRender.php DeleteSession ******************************************");
				exitifnotpermit(0,"Online.Web.User.DeleteSession");
				$NewRowInfo=array();
				$NewRowInfo['Online_WebUser_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$NewRowInfo['UserClientIP']=DBSelectAsString("select ClientIP from Tonline_webuser where Online_WebUser_Id='".$NewRowInfo['Online_WebUser_Id']."'");
				$NewRowInfo['Username']=DBSelectAsString("select Username from Tonline_webuser where Online_WebUser_Id='".$NewRowInfo['Online_WebUser_Id']."'");
				$ar=DBDelete("delete from Tonline_webuser Where Online_WebUser_Id='".$NewRowInfo['Online_WebUser_Id']."'");
				logdbdelete($NewRowInfo,'Delete','WebUser',$NewRowInfo['Online_WebUser_Id'],'');
				echo "OK~";
		break;	   
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
