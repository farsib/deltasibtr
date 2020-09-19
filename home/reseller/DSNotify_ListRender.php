<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSNotifyListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSNotifyListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Message.Notify.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT Notify_Id,NotifyName,NotifyType,ISEnable From Hnotify ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"Notify_Id","Notify_Id,NotifyName,NotifyType,ISEnable",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSNotifyListRender Delete ******************************************");

				exitifnotpermit(0,"Admin.Message.Notify.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Notify_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$NotifyName=DBSelectAsString("Select NotifyName from Hnotify Where Notify_Id=".$NewRowInfo['Notify_Id']);
				
				$NotifyType=DBSelectAsString("Select NotifyType from Hnotify Where Notify_Id=".$NewRowInfo['Notify_Id']);

				if($NotifyType=='CreditFinishNotify')
					$ParamItemName='Notify-CreditFinish';
				elseif($NotifyType=='ServiceExpireNotify')
					$ParamItemName='Notify-ServiceExpire';
				elseif($NotifyType=='UserDebitNotify')
					$ParamItemName='Notify-UserDebit';
				else
					ExitError('نوع اعلان تعریف نشده');				
				
				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='$ParamItemName') And value='$NotifyName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این اعلان به عنوان پارامتر توسط سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این اعلان به عنوان پارامتر توسط مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این اعلان به عنوان پارامتر توسط ارائه دهنده مجازی اینترنت زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این اعلان به عنوان پارامتر توسط نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این اعلان به عنوان پارامتر توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این اعلان به عنوان پارامتر توسط دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این اعلان به عنوان پارامتر توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}
				
				$n=DBDelete('delete from Hnotify Where Notify_Id='.$NewRowInfo['Notify_Id']);
				
				logdbdelete($NewRowInfo,'Delete','Notify',$NewRowInfo['Notify_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>