<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_SavingOff_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list",'CancelSavingOff'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_SavingOff_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.SavingOff.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				function color_rows($row){
					$data = $row->get_value("SavingOffStatus");
					if($data=='Used')
						$row->set_row_style("color:green");
					elseif(($data=='Expire'))
						$row->set_row_style("color:chocolate");
					else if($data=='Cancel')
						$row->set_row_style("color:red");
				}
				DBUpdate("update Huser_savingoff set SavingOffStatus='Expire' where User_Id='$User_Id' and SavingOffStatus='Pending' and SavingOffExpDT<=NOW()");
				DSGridRender_Sql(100,
					"Select User_SavingOff_Id,SavingOffStatus,Format(SavingOffAmount,$PriceFloatDigit) as SavingOffAmount,Format(UsedAmount,$PriceFloatDigit) as UsedAmount,{$DT}DateTimeStr(SavingOffCDT) as SavingOffCDT,{$DT}DateTimeStr(SavingOffUseDT) as SavingOffUseDT,{$DT}DateTimeStr(SavingOffExpDT) as SavingOffExpDT,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,Comment ".
					"From Huser_savingoff ".
					"Where (User_Id=$User_Id)" .$sqlfilter." $SortStr ",
					"User_SavingOff_Id",
					"User_SavingOff_Id,SavingOffStatus,SavingOffAmount,UsedAmount,SavingOffCDT,SavingOffUseDT,SavingOffExpDT,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,Comment",
					"","","color_rows");
       break;
    case "CancelSavingOff":
				DSDebug(0,"DSUser_SavingOff_ListRender->CancelSavingOff ********************************************");
				$User_SavingOff_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_savingoff where User_SavingOff_Id=$User_SavingOff_Id");
				exitifnotpermituser($User_Id,"Visp.User.SavingOff.Cancel");
				$SavingOffStatus=DBSelectAsString("Select SavingOffStatus from Huser_savingoff where User_SavingOff_Id=$User_SavingOff_Id");
				if($SavingOffStatus=='Pending'){
					$ar=DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_SavingOff_Id=$User_SavingOff_Id");
					logdb('Update','User',$User_Id,'SavingOff',"Cancel SavingOff. User_SavingOff_Id=$User_SavingOff_Id");
					echo "OK~";
				}
				else
					ExitError('Only Pending Gift can Cancel');
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>