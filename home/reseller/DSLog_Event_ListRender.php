<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSEvent_ListRender.........................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				//Permission -----------------
				exitifnotpermit(0,"Log.Event.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,"SELECT LogEvent_Id,{$DT}DateTimeStr(Log_CDT) as Log_CDT,EventType,AffectedRow,RunTime,Comment ".
									"FROM  Hlogevent Where 1 ".$sqlfilter." $SortStr ",
									"LogEvent_Id",
									"LogEvent_Id,Log_CDT,EventType,AffectedRow,RunTime,Comment","","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
