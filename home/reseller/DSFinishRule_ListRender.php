<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSFinishRuleListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSFinishRuleListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.FinishRule.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT FinishRule_Id,FinishRuleName, ".
					"ip3.IPPoolName As OnActiveServiceExpirePoolName,ip4.IPPoolName As OnTrafficFinishPoolName, ".
					"ip5.IPPoolName As OnTimeFinishPoolName,ip6.IPPoolName As OnDebitFinishPoolName ".
					"From Hfinishrule fl ".
					"Left join Hippool ip3 on(fl.OnActiveServiceExpirePool_Id=ip3.IPPool_Id) ".
					"Left join Hippool ip4 on(fl.OnTrafficFinishPool_Id=ip4.IPPool_Id) ".
					"Left join Hippool ip5 on(fl.OnTimeFinishPool_Id=ip5.IPPool_Id) ".
					"Left join Hippool ip6 on(fl.OnDebitFinishPool_Id=ip6.IPPool_Id) ".
					"Where 1 $sqlfilter $SortStr ",
					"FinishRule_Id",
					"FinishRule_Id,FinishRuleName,OnActiveServiceExpirePoolName,OnTrafficFinishPoolName,OnTimeFinishPoolName,OnDebitFinishPoolName",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSFinishRuleListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.FinishRule.Delete");
				$NewRowInfo=array();
				$NewRowInfo['FinishRule_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$FinishRuleName=DBSelectAsString("Select FinishRuleName from Hfinishrule Where FinishRule_Id=".$NewRowInfo['FinishRule_Id']);

				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='FinishRule') And value='$FinishRuleName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("این قانون اتمام به عنوان پارامتر توسط سرور استفاده می شود و قابل حذف نیست");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("این قانون اتمام به عنوان پارامتر توسط مرکز زیر استفاده می شود و قابل حذف نیست</br>'$CenterName'");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("این قانون اتمام به عنوان پارامتر توسط ارائه دهنده مجازی اینترنت زیر استفاده می شود و قابل حذف نیست</br>'$VispName'");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("این قانون اتمام به عنوان پارامتر توسط نماینده فروش زیر استفاده می شود و قابل حذف نیست</br>'$ResellerName'");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("این قانون اتمام به عنوان پارامتر توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("این قانون اتمام به عنوان پارامتر توسط دسته زیر استفاده می شود و قابل حذف نیست</br>'$ClassName'");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("این قانون اتمام به عنوان پارامتر توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$UserName'");
					}
					
				}
				
				
				//DBDelete("delete from Hparam Where value='$FinishRuleName'");
				 	
				$ar=DBDelete('delete from Hfinishrule Where FinishRule_Id='.$NewRowInfo['FinishRule_Id']);
				logdbdelete($NewRowInfo,'Delete','FinishRule',$NewRowInfo['FinishRule_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
