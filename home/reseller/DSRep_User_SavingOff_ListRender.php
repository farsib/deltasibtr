<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_SavingOff_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.SavingOff.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_SavingOff_ListRender->List ********************************************");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')
					$SortStr="Order by $SortField $SortOrder";
				else
					$SortStr="order by User_SavingOff_Id desc";
				function color_rows($row){
					$data = $row->get_value("SavingOffStatus");
					if($data=='Used')
						$row->set_row_style("color:green");
					elseif(($data=='Expire'))
						$row->set_row_style("color:chocolate");
					else if($data=='Cancel')
						$row->set_row_style("color:red");
				}
				$SelectStr="User_SavingOff_Id,Username,SavingOffStatus,Format(SavingOffAmount,$PriceFloatDigit) as SavingOffAmount,Format(UsedAmount,$PriceFloatDigit) as UsedAmount,{$DT}DateTimeStr(SavingOffCDT) as SavingOffCDT,{$DT}DateTimeStr(SavingOffUseDT) as SavingOffUseDT,{$DT}DateTimeStr(SavingOffExpDT) as SavingOffExpDT,uso.User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,uso.Comment";
				$ColumnStr="User_SavingOff_Id,Username,SavingOffStatus,SavingOffAmount,UsedAmount,SavingOffCDT,SavingOffUseDT,SavingOffExpDT,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,Comment";
				
				$sql="select $SelectStr from Huser_savingoff uso ".
					"join Huser u on uso.User_Id=u.User_Id ";
				if($LReseller_Id!=1){
					$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$VispUserSavingOffList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.SavingOff.List'");
					$sql.=
						"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=u.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=u.Visp_Id and Hrp2.PermitItem_Id=$VispUserSavingOffList and Hrp2.ISPermit='Yes' ";
				}
				$sql.="where 1 ".$sqlfilter." $SortStr";
				DBUpdate("update Huser_savingoff set SavingOffStatus='Expire' where SavingOffStatus='Pending' and SavingOffExpDT<=NOW()");
				DSGridRender_Sql(100,$sql,"User_SavingOff_Id",$ColumnStr,"","","color_rows");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
