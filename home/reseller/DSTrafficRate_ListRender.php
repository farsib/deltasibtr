<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSTrafficRateListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSTrafficRateListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.TrafficRate.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT TrafficRate_Id,TrafficRateName,".
					"dslistvalue(1,TrafficRateValue) As H0,dslistvalue(2,TrafficRateValue) As H1,dslistvalue(3,TrafficRateValue) As H2,dslistvalue(4,TrafficRateValue) As H3,".
					"dslistvalue(5,TrafficRateValue) As H4,dslistvalue(6,TrafficRateValue) As H5,dslistvalue(7,TrafficRateValue) As H6,dslistvalue(8,TrafficRateValue) As H7,".
					"dslistvalue(9,TrafficRateValue) As H8,dslistvalue(10,TrafficRateValue) As H9,dslistvalue(11,TrafficRateValue) As H10,dslistvalue(12,TrafficRateValue) As H11,".
					"dslistvalue(13,TrafficRateValue) As H12,dslistvalue(14,TrafficRateValue) As H13,dslistvalue(15,TrafficRateValue) As H14,dslistvalue(16,TrafficRateValue) As H15,".
					"dslistvalue(17,TrafficRateValue) As H16,dslistvalue(18,TrafficRateValue) As H17,dslistvalue(19,TrafficRateValue) As H18,dslistvalue(20,TrafficRateValue) As H19,".
					"dslistvalue(21,TrafficRateValue) As H20,dslistvalue(22,TrafficRateValue) As H21,dslistvalue(23,TrafficRateValue) As H22,dslistvalue(24,TrafficRateValue) As H23 ".
					"From Htrafficrate Where 1 ".$sqlfilter." $SortStr ",
					"TrafficRate_Id",
					"TrafficRate_Id,TrafficRateName,H0,H1,H2,H3,H4,H5,H6,H7,H8,H9,H10,H11,H12,H13,H14,H15,H16,H17,H18,H19,H20,H21,H22,H23",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSTrafficRateListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.TrafficRate.Delete");
				$NewRowInfo=array();
				$NewRowInfo['TrafficRate_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$TrafficRateName=DBSelectAsString("Select TrafficRateName from Htrafficrate Where TrafficRate_Id=".$NewRowInfo['TrafficRate_Id']);

				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='TrafficRate') And value='$TrafficRateName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط ارائه دهنده مجازی اینترنت زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این ضریب محاسبه ترافیک به عنوان پارامتر توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}
				
				
				//DBDelete("delete from Hparam Where value='$TrafficRateName'");
				 	
				$ar=DBDelete('delete from Htrafficrate Where TrafficRate_Id='.$NewRowInfo['TrafficRate_Id']);
				logdbdelete($NewRowInfo,'Delete','TrafficRate',$NewRowInfo['TrafficRate_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>