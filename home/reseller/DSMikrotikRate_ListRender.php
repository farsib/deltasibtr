﻿<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSMikrotikRateListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);
//rx-rate[/tx-rate] [rx-burst-rate[/tx-burst-rate] [rx-burst-threshold[/tx-burst-threshold] [rx-burst-time[/tx-burst-time] [priority] [rx-rate-min[/tx-rate-min]]]] 
switch ($act) {
    case "list":
				DSDebug(0,"DSMikrotikRateListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRate.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT MikrotikRate_Id,MikrotikRateName,mrv.MikrotikRateValueName As ParentName ".
					"From Hmikrotikrate mr left join Hmikrotikratevalue mrv on mr.Parent_MikrotikRateValue_Id=mrv.MikrotikRateValue_Id Where 1 ".$sqlfilter." $SortStr ",
					"MikrotikRate_Id",
					"MikrotikRate_Id,MikrotikRateName,ParentName",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSTimeRateListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRate.Delete");
				$NewRowInfo=array();
				$NewRowInfo['MikrotikRate_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$MikrotikRateName=DBSelectAsString("Select MikrotikRateName from Hmikrotikrate Where MikrotikRate_Id=".$NewRowInfo['MikrotikRate_Id']);

				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='MikrotikRate') And value='$MikrotikRateName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط ارائه دهنده مجازی اینترنت زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این سرعت میکروتیک به عنوان پارامتر توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}
				
				$ar=DBDelete('delete from Hmikrotikrate Where MikrotikRate_Id='.$NewRowInfo['MikrotikRate_Id']);
				logdbdelete($NewRowInfo,'Delete','MikrotikRate',$NewRowInfo['MikrotikRate_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>