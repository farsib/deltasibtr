﻿<?php
try{
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSNetworkIPListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSNetworkIPListRender->List ********************************************");
				exitifnotpermit(0,"Admin.NetworkIP.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				DSGridRender_Sql(100,
					"SELECT NetworkIP_Id,ISEnable,UseByIPDR,AssignmentTo,IPType,ISAuthenticate,UserType,ISHotSpot,INET_NToA(StartIP) As StartIP,INET_NToA(EndIP) as EndIP,NOE,Comment From Hnetworkip Where 1 ".$sqlfilter." $SortStr ",
					"NetworkIP_Id","NetworkIP_Id,ISEnable,UseByIPDR,AssignmentTo,IPType,ISAuthenticate,UserType,ISHotSpot,StartIP,EndIP,NOE,Comment",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSNetworkIPListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.NetworkIP.Delete");
				$NewRowInfo=array();
				$NewRowInfo['NetworkIP_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);

				$ar=DBDelete('delete from Hnetworkip Where NetworkIP_Id='.$NewRowInfo['NetworkIP_Id']);
				logdbdelete($NewRowInfo,'Delete','NetworkIP',$NewRowInfo['NetworkIP_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>