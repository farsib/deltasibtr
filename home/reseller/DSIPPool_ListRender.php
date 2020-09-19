<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSIPPoolListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSIPPoolListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.IPPool.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT IPPool_Id,IPPoolName,Method,IsFinishedIP From Hippool ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"IPPool_Id",
					"IPPool_Id,IPPoolName,Method,IsFinishedIP",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSIPPoolListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Delete");
				$NewRowInfo=array();
				$NewRowInfo['IPPool_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				
				$FinishRuleName=DBSelectAsString("Select FinishRuleName from Hfinishrule Where ".
								"OnActiveServiceExpirePool_Id=".$NewRowInfo['IPPool_Id'].
								" or OnTrafficFinishPool_Id=".$NewRowInfo['IPPool_Id'].
								" or OnTimeFinishPool_Id=".$NewRowInfo['IPPool_Id'].
								" or OnDebitFinishPool_Id=".$NewRowInfo['IPPool_Id']
								);
				if($FinishRuleName!='')
					ExitError("این دامنه آی پی توسط قانون اتمام زیر استفاده می شود و قابل حذف نیست</br>'$FinishRuleName'");

				$IPPoolName=DBSelectAsString("Select IPPoolName from Hippool Where IPPool_Id=".$NewRowInfo['IPPool_Id']);
				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='IPPool') And value='$IPPoolName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این دامنه آی پی به عنوان پارامتر توسط سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این دامنه آی پی به عنوان پارامتر توسط مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این دامنه آی پی به عنوان پارامتر توسط ارائه دهنده مجازی اینترنت زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این دامنه آی پی به عنوان پارامتر توسط نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این دامنه آی پی به عنوان پارامتر توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این دامنه آی پی به عنوان پارامتر توسط دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این دامنه آی پی به عنوان پارامتر توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}
				
				
				//DBDelete("delete from Hparam Where value='$IPPoolName'");
				 	
				$ar=DBDelete('delete from Hippool Where IPPool_Id='.$NewRowInfo['IPPool_Id']);
				$ar=DBDelete('delete from Hippool_pool Where IPPool_Id='.$NewRowInfo['IPPool_Id']);
				logdbdelete($NewRowInfo,'Delete','IPPool',$NewRowInfo['IPPool_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>