<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_Web_Reseller_ListRender.php.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list","DeleteSession"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(1,"DSOnline_Web_Reseller_ListRender.php list ******************************************");
				//Permission -----------------
				exitifnotpermit(0,"Online.Web.Reseller.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				if($LReseller_Id==1)
					DSGridRender_Sql(100,"SELECT Online_WebReseller_Id,owr.ResellerName,INET_NTOA(ClientIp) as ClientIP,{$DT}DateTimeStr(LoginDT) as LoginDT,".
					"{$DT}DateTimeStr(LastSeenDT) As LastSeenDT,BrowserInfo,NumRequest FROM  Tonline_webreseller owr left join Hreseller r on owr.Reseller_Id=r.Reseller_Id ".
					"Where 1 $sqlfilter $SortStr",
					"Online_WebReseller_Id",
					"Online_WebReseller_Id,ResellerName,ClientIP,LoginDT,LastSeenDT,BrowserInfo,NumRequest","","","");
				else
					DSGridRender_Sql(100,"SELECT Online_WebReseller_Id,owr.ResellerName,INET_NTOA(ClientIp) as ClientIP,{$DT}DateTimeStr(LoginDT) as LoginDT,".
					"{$DT}DateTimeStr(LastSeenDT) As LastSeenDT,BrowserInfo,NumRequest FROM  Tonline_webreseller owr left join Hreseller r on owr.Reseller_Id=r.Reseller_Id ".
					"Where $LResellerAccessAllow $sqlfilter $SortStr",
					"Online_WebReseller_Id",
					"Online_WebReseller_Id,ResellerName,ClientIP,LoginDT,LastSeenDT,BrowserInfo,NumRequest","","","");
       break;
	case "DeleteSession":
				DSDebug(1,"DSOnline_Web_Reseller_ListRender.php DeleteSession ******************************************");
				exitifnotpermit(0,"Online.Web.Reseller.DeleteSession");
				$NewRowInfo=array();
				$NewRowInfo['Online_WebReseller_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$NewRowInfo['ResellerClientIP']=DBSelectAsString("select ClientIP from Tonline_webreseller where Online_WebReseller_Id='".$NewRowInfo['Online_WebReseller_Id']."'");
				$NewRowInfo['ResellerName']=DBSelectAsString("select ResellerName from Tonline_webreseller where Online_WebReseller_Id='".$NewRowInfo['Online_WebReseller_Id']."'");
				$ar=DBDelete("delete from Tonline_webreseller Where Online_WebReseller_Id='".$NewRowInfo['Online_WebReseller_Id']."'");
				logdbdelete($NewRowInfo,'Delete','WebReseller',$NewRowInfo['Online_WebReseller_Id'],'');
				echo "OK~";
		break;	   
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
