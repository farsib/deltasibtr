<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_CallerId_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.CallerId.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_CallerId_ListRender->List ********************************************");
				
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserCallerIdList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.CallerId.List'");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by User_CallerId_Id Desc";
									
				$ColumnStr="User_CallerId_Id,Username,CallerId";
				
				$sql="select Huc.User_CallerId_Id,Hu.Username,Huc.CallerId from Huser_callerid Huc join Huser Hu on  Huc.User_id=Hu.User_id ".
					(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserCallerIdList and Hrp2.ISPermit='Yes' ":"").
					"where 1 ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"User_CallerId_Id",$ColumnStr,"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
