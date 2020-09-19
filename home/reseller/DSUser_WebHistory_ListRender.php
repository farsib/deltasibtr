<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_WebHistory_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_WebHistory_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.WebHistory.List");			

				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$sql="select User_WebConnection_Id,INET_NtoA(ClientIP) as ClientIP,{$DT}DateTimeStr(StartDT) As StartDT,{$DT}DateTimeStr(EndDT) As EndDT,BrowserInfo,NumRequest from Huser_webconnection ".
					"where User_Id=$User_Id ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"User_WebConnection_Id","User_WebConnection_Id,ClientIP,StartDT,EndDT,BrowserInfo,NumRequest","","","");
					
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>