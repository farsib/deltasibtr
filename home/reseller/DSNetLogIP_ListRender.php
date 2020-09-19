<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSNetLogIPListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSNetLogIPListRender->List ********************************************");
				exitifnotpermit(0,"Admin.NetLogIP.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT NetLogIP_Id,AssignmentTo,IPType,ISAuthenticate,INET_NTOA(StartIP) as StartIP,INET_NTOA(EndIP) as EndIP,Comment From Hnetlogip Where 1 ".$sqlfilter." $SortStr ",
					"NetLogIP_Id","NetLogIP_Id,AssignmentTo,IPType,ISAuthenticate,StartIP,EndIP,Comment",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSNetLogIPListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.NetLogIP.Delete");
				$NewRowInfo=array();
				$NewRowInfo['NetLogIP_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);

				$ar=DBDelete('delete from Hnetlogip Where NetLogIP_Id='.$NewRowInfo['NetLogIP_Id']);
				logdbdelete($NewRowInfo,'Delete','NetLogIP',$NewRowInfo['NetLogIP_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>