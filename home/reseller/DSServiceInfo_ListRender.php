<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSServiceInfoListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSServiceInfoListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.ServiceInfo.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT ServiceInfo_Id,ServiceInfoName,ServiceInfoValue,ServiceRate From Hserviceinfo ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"ServiceInfo_Id",
					"ServiceInfo_Id,ServiceInfoName,ServiceInfoValue,ServiceRate",
					"","","");
       break;
	   case "Delete":
				DSDebug(1,"DSServiceInfoListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.ServiceInfo.Delete");
				$NewRowInfo=array();
				$NewRowInfo['ServiceInfo_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				if($NewRowInfo['ServiceInfo_Id']==1)
					ExitError("This ServiceInfo can not delete!");
					
				/*
				$ServiceInfoName=DBSelectAsString("Select ServiceInfoName from Hserviceinfo Where ServiceInfo_Id=".$NewRowInfo['ServiceInfo_Id']);

				$Param_Id=DBSelectAsString("Select Param_Id from Hparam Where ParamItem_Id=(Select ParamItem_Id From Hparamitem Where ParamItemName='ServiceInfo') And value='$ServiceInfoName'");

				if($Param_Id>0){
					$TableName=DBSelectAsString("Select TableName from Hparam Where Param_Id=$Param_Id");
					$TableId=DBSelectAsString("Select TableId from Hparam Where Param_Id=$Param_Id");
					if($TableName=='Server'){
						ExitError("This ServiceInfo is used by Server Param and Can not delete");
					}
					else if($TableName=='Center'){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter Where Center_Id=$TableId");
						ExitError("This ServiceInfo is used by Center '$CenterName' and Can not delete");
					}
					else if($TableName=='Visp'){
						$VispName=DBSelectAsString("Select VispName from Hvisp Where Visp_Id=$TableId");
						ExitError("This ServiceInfo is used by Visp '$VispName' and Can not delete");
					}
					else if($TableName=='Reseller'){
						$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where Reseller_Id=$TableId");
						ExitError("This ServiceInfo is used by Reseller '$ResellerName' and Can not delete");
					}
					else if($TableName=='Service'){
						$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$TableId");
						ExitError("This ServiceInfo is used by Service '$ServiceName' and Can not delete");
					}
					else if($TableName=='Class'){
						$ClassName=DBSelectAsString("Select ClassName from Hclass Where Class_Id=$TableId");
						ExitError("This ServiceInfo is used by Class '$ClassName' and Can not delete");
					}
					else if($TableName=='User'){
						$UserName=DBSelectAsString("Select UserName from Huser Where User_Id=$TableId");
						ExitError("This ServiceInfo is used by User '$UserName' and Can not delete");
					}
					
				}
				
				
				DBDelete("delete from Hparam Where value='$ServiceInfoName'");
				 	
				$ar=DBDelete('delete from Hserviceinfo Where ServiceInfo_Id='.$NewRowInfo['ServiceInfo_Id']);
				logdbdelete($NewRowInfo,'Delete','ServiceInfo',$NewRowInfo['ServiceInfo_Id'],'');
				*/
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>