<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSService_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete","Copy"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSServiceListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Service.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$sql="Select s.Service_Id,ServiceType,ISEnable,ServiceName,Format(Price,$PriceFloatDigit) As Price,".
					"OffRate,ISFairService,Speed,UserChoosable,ResellerChoosable,if(ServiceType='Base',GiftName,'--') as GiftName,Category1,Category2,".
					"If(ServiceType='Base',(Select count(*) From Huser_servicebase Where Service_Id=s.Service_Id and ServiceStatus in ('Active','Pending')),".
						"If(ServiceType='ExtraCredit',(Select count(*) From Huser_serviceextracredit Where Service_Id=s.Service_Id),".
						"If(ServiceType='IP',(Select count(*) From Huser_serviceip Where Service_Id=s.Service_Id and ServiceStatus in ('Active','Pending')),".
						"If(ServiceType='Other',(Select count(*) From  Huser_serviceother Where Service_Id=s.Service_Id),".
						"0)))) As UserCount,".
					"if(OnBuyFromWebsiteSMS<>'','Yes','No') as WebSMS,if(OnAddByResellerSMS<>'','Yes','No') as ResellerSMS".
					" From Hservice s left join Hgift g on s.AttachedGift_Id=g.Gift_Id ".
					"Where IsDel='No' $sqlfilter ".
					" Group by s.Service_Id $SortStr ";
				$Fields="Service_Id,ServiceType,ISEnable,ServiceName,Price,OffRate,ISFairService,Speed,UserChoosable,ResellerChoosable,GiftName,Category1,Category2,UserCount,WebSMS,ResellerSMS";
				function color_rows($row){
					$data = $row->get_value("ServiceType");
					if($data=='Base'){
						$row->set_row_style("color:black");
						// if($row->get_value("GiftName")=="")
							// $row->set_cell_style("GiftName","Background-Color:#999999");
					}
					else{
						$row->set_cell_style("ISFairService","Color:#999999");
						$row->set_cell_style("GiftName","Color:#999999");
						if($data=='ExtraCredit')
							$row->set_row_style("color:blue");
						else if($data=='IP')
							$row->set_row_style("color:red");
						else if($data=='Other')
							$row->set_row_style("color:green");
					}
					if($row->get_value("WebSMS")=="Yes")
						$row->set_cell_style("WebSMS","Background-Color:#999999");
					if($row->get_value("ResellerSMS")=="Yes")
						$row->set_cell_style("ResellerSMS","Background-Color:#999999");
				}
				DSGridRender_Sql(-1,$sql,"Service_Id",$Fields,"","","color_rows");
       break;
	   case "Delete":
				DSDebug(1,"DSService_ListRender Delete ******************************************");
				exitifnotpermit(0,"CRM.Service.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Service_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);

				$User_Id=DBSelectAsString("Select User_Id from Huser_servicebase Where ((ServiceStatus='Active' or ServiceStatus='Pending')) and Service_Id=".$NewRowInfo['Service_Id']." Limit 1");
				if($User_Id>0){
					$Username=DBSelectAsString("Select Username from Huser Where User_Id=$User_Id");
					ExitError("این سرویس توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$Username'");
				}

				$User_Id=DBSelectAsString("Select User_Id from Huser_serviceextracredit Where (ServiceStatus='Pending') and Service_Id=".$NewRowInfo['Service_Id']." Limit 1");
				if($User_Id>0){
					$Username=DBSelectAsString("Select Username from Huser Where User_Id=$User_Id");
					ExitError("این سرویس توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$Username'");
				}
				
				$User_Id=DBSelectAsString("Select User_Id from  Huser_serviceip Where ((ServiceStatus='Active' or ServiceStatus='Pending')) and Service_Id=".$NewRowInfo['Service_Id']." Limit 1");
				if($User_Id>0){
					$Username=DBSelectAsString("Select Username from Huser Where User_Id=$User_Id");
					ExitError("این سرویس توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$Username'");
				}
				
				$NasInfoName=DBSelectAsString("Select NasInfoName from Hnasinfo Where DefService_Id=".$NewRowInfo['Service_Id']." Limit 1");
				if($NasInfoName<>'') ExitError("این سرویس توسط پارامتر ردیوس زیر به عنوان سرویس پیش فرض برای ایجاد کاربر جدید استفاده می شود،لطفا آن را تغییر دهید</br>'$NasInfoName'");
				
				$IsUsedForWebNewUser=DBSelectAsString("Select if(Param10=".$NewRowInfo['Service_Id'].",'Yes','No') from Hserver Where PartName='WebNewUser' and Param1='Yes'");
				if($IsUsedForWebNewUser=='Yes') ExitError("این سرویس توسط ثبت نام کاربر در پنل کاربری استفاده می شود");

				DBDelete('delete from Hservice_vispaccess Where Service_Id='.$NewRowInfo['Service_Id']);
				DBDelete('delete from Hservice_reselleraccess Where Service_Id='.$NewRowInfo['Service_Id']);
				DBDelete('delete from Hservice_gift Where Service_Id='.$NewRowInfo['Service_Id']);
				DBDelete('delete from Hservice_class Where Service_Id='.$NewRowInfo['Service_Id']);
				DBDelete('delete from Hservice_servicebaseaccess Where Service_Id='.$NewRowInfo['Service_Id'].' or Accessed_Service_Id='.$NewRowInfo['Service_Id']);
				DBDelete('delete from Hparam Where TableName=\'Service\' and TableId='.$NewRowInfo['Service_Id']);
				$UserCount=0;
				$UserCount+=DBSelectAsString("Select Count(1) from Huser_servicebase Where Service_Id=".$NewRowInfo['Service_Id']);
				$UserCount+=DBSelectAsString("Select Count(1) from Huser_serviceextracredit Where Service_Id=".$NewRowInfo['Service_Id']);
				$UserCount+=DBSelectAsString("Select Count(1) from  Huser_serviceip Where Service_Id=".$NewRowInfo['Service_Id']);				
				$UserCount+=DBSelectAsString("Select Count(1) from  Huser_serviceother Where Service_Id=".$NewRowInfo['Service_Id']);				
				
				If($UserCount>0)
					$ar=DBDelete("Update Hservice Set ServiceName=left(Concat('Del".rand(100,999)."-',ServiceName),128),IsDel='Yes',ISEnable='No' Where Service_Id=".$NewRowInfo['Service_Id']);
				else
					$ar=DBDelete("delete from Hservice Where Service_Id=".$NewRowInfo['Service_Id']);
				
				
				//$ar=DBDelete("Update Hservice Set ServiceName=Concat('*D* ',ServiceName),IsDel='Yes',ISEnable='No' Where Service_Id=".$NewRowInfo['Service_Id']);
				logdbdelete($NewRowInfo,'Delete','Service',$NewRowInfo['Service_Id'],'');
				echo "OK~";
		break;
	case "Copy":
				DSDebug(1,"DSService_ListRender.php Copy ******************************************");
				exitifnotpermit(0,"CRM.Service.Copy");
				
				
				$Service_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$ServiceName=DBSelectAsString("Select ServiceName from Hservice where Service_Id='$Service_Id' and IsDel='No'");
				
				if($ServiceName=="")
					ExitError("سرویس مشخص شده نامعتبر");
				$i=1;
				while(true){
					$NewServiceName=$ServiceName." - $i";
					if(strlen($NewServiceName)>128)
						ExitError("به علت نام طولانی سرویس نمیتوان آن را کپی کرد");
					if(DBSelectAsString("Select count(1) from Hservice where ServiceName='$NewServiceName'")<=0)
						break;
					else
						$i++;
				}
				
				$sql="INSERT INTO Hservice(ServiceCDT,ServiceName,Category1,Category2,Description,Price,OffRate,ServiceType,ISEnable,ClassAccess,VispAccess,
ResellerAccess,STrA,YTrA,MTrA,WTrA,DTrA,STiA,YTiA,MTiA,WTiA,DTiA,ExtraTraffic,ExtraTime,ExtraActiveDay,ActiveYear,ActiveMonth,ActiveDay,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount,UserChoosable,ResellerChoosable,Speed,IPCount,AvailableFromDate,AvailableToDate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,FramedIP,FramedRoute,AttachedGift_Id,OnBuyFromWebsiteSMS,OnAddByResellerSMS,SMSExpireTime,ISFairService,FairMikrotikRate_Id,ServiceBaseAccess)".
				"Select now(),'$NewServiceName',Category1,Category2,Description,Price,OffRate,ServiceType,ISEnable,ClassAccess,VispAccess,
ResellerAccess,STrA,YTrA,MTrA,WTrA,DTrA,STiA,YTiA,MTiA,WTiA,DTiA,ExtraTraffic,ExtraTime,ExtraActiveDay,ActiveYear,ActiveMonth,ActiveDay,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount,UserChoosable,ResellerChoosable,Speed,IPCount,AvailableFromDate,AvailableToDate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,FramedIP,FramedRoute,AttachedGift_Id,OnBuyFromWebsiteSMS,OnAddByResellerSMS,SMSExpireTime,ISFairService,FairMikrotikRate_Id,ServiceBaseAccess from Hservice where Service_Id='$Service_Id'";
				$RowId=DBInsert($sql);
				$Type=Get_Input('GET','DB','Type','ARRAY',ARRAY("FullCopy","CreditCopy"),0,0,0);
				if($Type=="FullCopy"){
					DBUpdate("insert Hservice_gift(Service_Id,Gift_Id) select '$RowId',Gift_Id from Hservice_gift where Service_Id='$Service_Id'");
					
					
					DBUpdate("insert Hparam(ParamItem_Id,TableName,TableId,ParamStatus,ServiceInfo_Id,Value) select ParamItem_Id,TableName,'$RowId',ParamStatus,ServiceInfo_Id,Value from Hparam where TableName='Service' and TableId='$Service_Id'");
					
					
					DBUpdate("insert Hservice_class(Service_Id,Class_Id,Checked) select '$RowId',Class_Id,Checked from Hservice_class where Service_Id='$Service_Id'");					
					
					
					
					DBUpdate("insert Hservice_vispaccess(Service_Id,Visp_Id,Checked) select '$RowId',Visp_Id,Checked from Hservice_vispaccess where Service_Id='$Service_Id'");
					DBUpdate("insert Hservice_reselleraccess(Service_Id,Reseller_Id,Checked) select '$RowId',Reseller_Id,Checked from Hservice_reselleraccess where Service_Id='$Service_Id'");
					DBUpdate("insert Hservice_servicebaseaccess(Service_Id,Accessed_Service_Id,Checked) select '$RowId',Accessed_Service_Id,Checked	from Hservice_servicebaseaccess where Service_Id='$Service_Id'");					
				}
				logdb("Add","Service",$RowId,"Service","Service $Type from Id=[$Service_Id] Name=[$ServiceName]");
				echo "OK~$RowId~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>