<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSLOg_Reseller_www_ListRender.........................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);
PrintInputGetPost();
switch ($act) {
    case "list":
				//Permission -----------------
				exitifnotpermit(0,"Log.Reseller.www.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				if($LReseller_Id==1)
				DSGridRender_Sql(100,"SELECT LogReseller_Id,{$DT}DateTimeStr(LogDbCDT) as LogDbCDT,ResellerName,INET_NTOA(ClientIp) as IP,LogType,Comment ".
									"FROM  Hlogreseller ls left join Hreseller r on ls.Reseller_Id=r.Reseller_Id Where 1 ".$sqlfilter." $SortStr ",
									"LogReseller_Id",
									"LogReseller_Id,LogDbCDT,ResellerName,IP,LogType,Comment","","","");
				else
				DSGridRender_Sql(100,"SELECT LogReseller_Id,{$DT}DateTimeStr(LogDbCDT) as LogDbCDT,ResellerName,INET_NTOA(ClientIp) as IP,LogType,Comment ".
									"FROM  Hlogreseller ls left join Hreseller r on ls.Reseller_Id=r.Reseller_Id ".
									"Where  $LResellerAccessAllow ".$sqlfilter." $SortStr ",
									"LogReseller_Id",
									"LogReseller_Id,LogDbCDT,ResellerName,IP,LogType,Comment","","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
