<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSCalledIdListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSCalledIdListRender->List ********************************************");
				exitifnotpermit(0,"Admin.CalledId.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT CalledId_Id,CalledIdName,CalledIdValue From Hcalledid ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"CalledId_Id",
					"CalledId_Id,CalledIdName,CalledIdValue",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSCalledIdListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.CalledId.Delete");
				$NewRowInfo=array();
				$NewRowInfo['CalledId_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
					
				$CalledIdName=DBSelectAsString("Select CalledIdName from Hcalledid Where CalledId_Id=".$NewRowInfo['CalledId_Id']);

				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='CalledId') And value='$CalledIdName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در ارائه دهنده مجازی زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این نام سرویس/آی پی سرور به عنوان پارامتر در کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}

				DBDelete("delete from Hparam Where value='$CalledIdName'");
				 	
				$ar=DBDelete('delete from Hcalledid Where CalledId_Id='.$NewRowInfo['CalledId_Id']);
				logdbdelete($NewRowInfo,'Delete','CalledId',$NewRowInfo['CalledId_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>