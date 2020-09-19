<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSNasInfoListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete",""),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSNasInfoListRender->List ********************************************");
				exitifnotpermit(0,"Admin.NasInfo.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT NASInfo_Id,NasInfoName,NasType,DCMethod, ".
					"If(DCMethod='DM',DMPort,If(DCMethod='POD',PODPort,If(DCMethod='Telnet',TelnetPort,''))) As Port,".
					"If(DCMethod='DM',DMAttribute,PODAttribute) As Attribute,".
					"SSHPort,TelnetPort,BWManager,MaxInterimTime,DeleteUserStaleMethod,InterimRate,StepOneWaitingTime,StepTwoWaitingTime ".
					"From Hnasinfo Where 1 ".$sqlfilter." $SortStr ",
					"NASInfo_Id","NASInfo_Id,NasInfoName,NasType,DCMethod,Port,Attribute,SSHPort,TelnetPort,BWManager,MaxInterimTime,DeleteUserStaleMethod,InterimRate,StepOneWaitingTime,StepTwoWaitingTime",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSNasInfoListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.NasInfo.Delete");
				$NewRowInfo=array();
				$NewRowInfo['NASInfo_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$NasName=DBSelectAsString("Select NasName from Hnas Where NASInfo_Id=".$NewRowInfo['NASInfo_Id']." Limit 1");
				if($NasName<>'') ExitError("این پارامتر توسط  ردیوس زیر استفاده می شود</br>'$NasName'");
				$ar=DBDelete('delete from Hnasinfo Where NASInfo_Id='.$NewRowInfo['NASInfo_Id']);
				logdbdelete($NewRowInfo,'Delete','NasInfo',$NewRowInfo['NASInfo_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}

?>
