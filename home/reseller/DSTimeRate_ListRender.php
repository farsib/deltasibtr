<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSTimeRateListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSTimeRateListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.TimeRate.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT TimeRate_Id,TimeRateName,".
					"dslistvalue(1,TimeRateValue) As H0,dslistvalue(2,TimeRateValue) As H1,dslistvalue(3,TimeRateValue) As H2,dslistvalue(4,TimeRateValue) As H3,".
					"dslistvalue(5,TimeRateValue) As H4,dslistvalue(6,TimeRateValue) As H5,dslistvalue(7,TimeRateValue) As H6,dslistvalue(8,TimeRateValue) As H7,".
					"dslistvalue(9,TimeRateValue) As H8,dslistvalue(10,TimeRateValue) As H9,dslistvalue(11,TimeRateValue) As H10,dslistvalue(12,TimeRateValue) As H11,".
					"dslistvalue(13,TimeRateValue) As H12,dslistvalue(14,TimeRateValue) As H13,dslistvalue(15,TimeRateValue) As H14,dslistvalue(16,TimeRateValue) As H15,".
					"dslistvalue(17,TimeRateValue) As H16,dslistvalue(18,TimeRateValue) As H17,dslistvalue(19,TimeRateValue) As H18,dslistvalue(20,TimeRateValue) As H19,".
					"dslistvalue(21,TimeRateValue) As H20,dslistvalue(22,TimeRateValue) As H21,dslistvalue(23,TimeRateValue) As H22,dslistvalue(24,TimeRateValue) As H23 ".
					"From Htimerate Where 1 ".$sqlfilter." $SortStr ",
					"TimeRate_Id",
					"TimeRate_Id,TimeRateName,H0,H1,H2,H3,H4,H5,H6,H7,H8,H9,H10,H11,H12,H13,H14,H15,H16,H17,H18,H19,H20,H21,H22,H23",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSTimeRateListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.TimeRate.Delete");
				$NewRowInfo=array();
				$NewRowInfo['TimeRate_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$TimeRateName=DBSelectAsString("Select TimeRateName from Htimerate Where TimeRate_Id=".$NewRowInfo['TimeRate_Id']);

				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='TimeRate') And value='$TimeRateName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط ارائه دهنده مجازی اینترنت زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این ضریب محاسبه زمان به عنوان پارامتر توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}
				
				
				//DBDelete("delete from Hparam Where value='$TimeRateName'");
				 	
				$ar=DBDelete('delete from Htimerate Where TimeRate_Id='.$NewRowInfo['TimeRate_Id']);
				logdbdelete($NewRowInfo,'Delete','TimeRate',$NewRowInfo['TimeRate_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>