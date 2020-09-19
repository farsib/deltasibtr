<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSReseller_WebHistory_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_WebHistory_ListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Reseller.WebHistory.List");
				$Reseller_Id=Get_Input('GET','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(($Reseller_Id==$LReseller_Id)&&($LReseller_Id!=1))
					ExitError('You can not Edit or View Your Info!!!');
				ExitIfNotPermitRowAccess("reseller",$Reseller_Id);
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$sql="select Reseller_WebConnection_Id,INET_NtoA(ClientIP) as ClientIP,{$DT}DateTimeStr(StartDT) As StartDT,{$DT}DateTimeStr(EndDT) As EndDT,BrowserInfo,NumRequest from Hreseller_webconnection ".
					"where Reseller_Id=$Reseller_Id ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"Reseller_WebConnection_Id","Reseller_WebConnection_Id,ClientIP,StartDT,EndDT,BrowserInfo,NumRequest","","","");
					
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>