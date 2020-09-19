<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSPackageListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSPackageListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Package.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT Package_Id,FindResellerName(Creator_Id) As Creator,ISEnable,PackageName,Credit,Price From Hpackage Where 1 ".$sqlfilter." $SortStr ",
					"Package_Id","Package_Id,Creator,ISEnable,PackageName,Credit,Price",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSPackageListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Package.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Package_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$ar=DBDelete('delete from Hreseller_packageaccess Where Package_Id='.$NewRowInfo['Package_Id']);
				$ar=DBDelete('delete from Hpackage Where Package_Id='.$NewRowInfo['Package_Id']);

				logdbdelete($NewRowInfo,'Delete','Package',$NewRowInfo['Package_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>