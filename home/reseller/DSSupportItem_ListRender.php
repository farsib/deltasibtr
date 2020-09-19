<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSSupportItem_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSSupportItem_ListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.SupportItem.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT SupportItem_Id,IsEnable,SupportItemTitle ".
					"From Hsupportitem Where 1 ".$sqlfilter." $SortStr ",
					"SupportItem_Id",
					"SupportItem_Id,IsEnable,SupportItemTitle",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSSupportItem_ListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.SupportItem.Delete");
				$NewRowInfo=array();
				$NewRowInfo['SupportItem_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				if(DBSelectAsString("Select count(1) from Huser_supporthistory Where SupportItem_Id='".$NewRowInfo['SupportItem_Id']."'")>0)
					if(DBSelectAsString("Select IsEnable from Hsupportitem Where SupportItem_Id='".$NewRowInfo['SupportItem_Id']."'")=='No')
						ExitError("این مورد پشتیبانی حداقل یکبار استفاده شده است و به علت تاریخچه قابل حذف نیست");
					else
						ExitError("این مورد پشتیبانی حداقل یکبار استفاده شده است و به علت تاریخچه قابل حذف نیست. می توانید آن را غیرفعال کنید");
				$ar=DBDelete('delete from Huser_supporthistory Where SupportItem_Id='.$NewRowInfo['SupportItem_Id']);
				logdbdelete($NewRowInfo,'Delete','SupportItem',$NewRowInfo['SupportItem_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}

?>
