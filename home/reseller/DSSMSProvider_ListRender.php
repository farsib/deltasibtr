<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSSMSProviderListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSSMSProviderListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Message.SMSProvider.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT SMSProvider_Id,ISEnable,SMSProviderName From Hsmsprovider ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"SMSProvider_Id","SMSProvider_Id,ISEnable,SMSProviderName",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSSMSProviderListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Message.SMSProvider.Delete");
				$NewRowInfo=array();
				$NewRowInfo['SMSProvider_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				//check if uses
				$ar=DBDelete('delete from Hsmsprovider Where SMSProvider_Id='.$NewRowInfo['SMSProvider_Id']);
				logdbdelete($NewRowInfo,'Delete','SMSProvider',$NewRowInfo['SMSProvider_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>