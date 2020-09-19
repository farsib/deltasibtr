<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSWebServiceListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSWebServiceListRender->List ********************************************");
				exitifnotpermit(0,"Admin.WebService.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT WebService_User_Id,WebService_Username,ISEnable,PermitIP,LastRequestDT,INET_NTOA(LastRequestIP) as LastRequestIP,NumRequest From Hwebservice_user Where 1 ".$sqlfilter." $SortStr ",
					"WebService_User_Id","WebService_User_Id,WebService_Username,ISEnable,PermitIP,LastRequestDT,LastRequestIP,NumRequest",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSWebServiceListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.WebService.Delete");
				$NewRowInfo=array();
				$NewRowInfo['WebService_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$ar=DBDelete('delete from Hwebservice_user Where WebService_Id='.$NewRowInfo['WebService_Id']);
				logdbdelete($NewRowInfo,'Delete','WebService',$NewRowInfo['WebService_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>