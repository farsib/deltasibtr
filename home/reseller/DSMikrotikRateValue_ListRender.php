<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSMikrotikRateValue_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSMikrotikRateValue_ListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRateValue.List");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				DSGridRender_Sql(100,"SELECT MikrotikRateValue_Id,MikrotikRateValueName,MikrotikRateValueText From Hmikrotikratevalue Where 1 ".$sqlfilter." $SortStr ",
					"MikrotikRateValue_Id",
					"MikrotikRateValue_Id,MikrotikRateValueName,MikrotikRateValueText",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSMikrotikRateValue_ListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRateValue.Delete");
				$NewRowInfo=array();
				$NewRowInfo['MikrotikRateValue_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$MikrotikRateName=DBSelectAsString("Select MikrotikRateName from Hmikrotikrate Where FIND_IN_SET('".$NewRowInfo['MikrotikRateValue_Id']."',MikrotikRate )>0 Limit 1");

				if($MikrotikRateName!='')
					ExitError("این نام تعریف میکروتیک توسط سرعت میکروتیک زیر استفاده می شود و قابل حذف نیست</br>'$MikrotikRateName'");
				
				$ar=DBDelete('delete from Hmikrotikratevalue Where MikrotikRateValue_Id='.$NewRowInfo['MikrotikRateValue_Id']);
				logdbdelete($NewRowInfo,'Delete','MikrotikRateValue',$NewRowInfo['MikrotikRateValue_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>