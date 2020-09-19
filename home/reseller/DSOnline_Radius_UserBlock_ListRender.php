<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_Radius_UserBlock_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list","UnBlockUser"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(1,"DSOnline_Radius_UserBlock_ListRender Update ******************************************");
				exitifnotpermit(0,"Online.Radius.UserBlock.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,"Select Online_Radius_UserBlock_Id,Username,CallingStationId,{$DT}DateTimeStr(LastRequestDT) As LastRequestDT,{$DT}DateTimeStr(BlockDT) As BlockDT ".
									" FROM Tonline_radius_userblock Where 1 $sqlfilter $SortStr",
				"Online_Radius_UserBlock_Id",
				"Online_Radius_UserBlock_Id,Username,CallingStationId,LastRequestDT,BlockDT","","","");
       break;
	case "UnBlockUser":
				DSDebug(1,"DSOnline_Radius_UserBlock_ListRender Update ******************************************");
				exitifnotpermit(0,"Online.Radius.UserBlock.UnBlock");
				$NewRowInfo=array();
				$NewRowInfo['Online_Radius_UserBlock_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$Username=DBSelectAsString("Select Username from Tonline_radius_userblock where Online_Radius_UserBlock_Id=".$NewRowInfo['Online_Radius_UserBlock_Id']);
				$ar=DBDelete('delete from  Tonline_radius_userblock Where Online_Radius_UserBlock_Id='.$NewRowInfo['Online_Radius_UserBlock_Id']);
				logsecurity('Web',"User $Username deleted from Radius User Block");
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
