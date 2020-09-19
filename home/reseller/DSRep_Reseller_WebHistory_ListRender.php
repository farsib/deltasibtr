<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_Reseller_WebHistory_List.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.Reseller.WebHistory.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_Reseller_WebHistory_List->List ********************************************");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by Reseller_WebConnection_Id Desc";
				
				$sql="select Reseller_WebConnection_Id,ResellerName,INET_NtoA(ClientIP) as ClientIP,{$DT}DateTimeStr(rw.StartDT) As StartDT,{$DT}DateTimeStr(rw.EndDT) As EndDT,BrowserInfo,NumRequest from Hreseller_webconnection rw ".
					"join Hreseller r on rw.Reseller_Id=r.Reseller_Id ".
					"where ".(($LReseller_Id==1)?"1 ":"$LResellerAccessAllow ").$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"Reseller_WebConnection_Id","Reseller_WebConnection_Id,ResellerName,ClientIP,StartDT,EndDT,BrowserInfo,NumRequest","","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
