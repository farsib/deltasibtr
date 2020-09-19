<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServiceEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update",'DisconnectUser',"SelectGift","SelectMikrotikRate","SelectCategory"),0,0,0);

switch ($act) {
    case "load":
				DSDebug(1,"DSServiceEditRender Load ********************************************");
				exitifnotpermit(0,"CRM.Service.View");
				$Service_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="Select '' As Error,Service_Id,ISEnable,ServiceName,Category1,Category2,Description,OffRate,ROUND(Price,$PriceFloatDigit) As Price,InstallmentNo,InstallmentPeriod,InstallmentFirstCash".
						",Speed,ServiceType,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount,".
						"UserChoosable,ResellerChoosable,OnBuyFromWebsiteSMS,OnAddByResellerSMS,SMSExpireTime,ActiveYear,ActiveMonth,ActiveDay,".
						"round(STrA/1048576) As STrA,round(YTrA/1048576) As YTrA,round(MTrA/1048576) As MTrA,round(WTrA/1048576) As WTrA,round(DTrA/1048576) As DTrA,".
						"STiA,YTiA,MTiA,WTiA,DTiA,IPCount,FramedIP,FramedRoute,".
						"If(ExtraTraffic>0,'Traffic',If(ExtraTime>0,'Time','ActiveDay')) As ExtraType,".
						"round(ExtraTraffic/1048576) As ExtraTraffic,ExtraTime,ExtraActiveDay,{$DT}datestr(AvailableFromDate) as AvailableFromDate,".
						"{$DT}datestr(AvailableToDate) as AvailableToDate,AttachedGift_Id,ISFairService,FairMikrotikRate_Id, ".
						
						"If(ServiceType='Base',(Select count(*) From Huser_servicebase Where Service_Id='$Service_Id'),".
						"If(ServiceType='ExtraCredit',(Select count(*) From Huser_serviceextracredit Where Service_Id='$Service_Id'),".
						"If(ServiceType='IP',(Select count(*) From Huser_serviceip Where Service_Id='$Service_Id'),".
						"If(ServiceType='Other',(Select count(*) From  Huser_serviceother Where Service_Id='$Service_Id'),".
						"0)))) As UsedCount".
						
						" from Hservice where Service_Id='$Service_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				
				echo '</data>';
				
       break;
    case "insert": 
				DSDebug(1,"DSServiceEditRender Insert ******************************************");
				exitifnotpermit(0,"CRM.Service.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ServiceName']=Get_Input('POST','DB','ServiceName','STR',1,128,0,0);
								
				$NewRowInfo['Description']=Get_Input('POST','DB','Description','STR',0,254,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['Price']=Get_Input('POST','DB','Price','PRC',1,14,0,0);
				if($NewRowInfo['Price']<0)
						ExitError('قیمت نامعتبر');
				$NewRowInfo['OffRate']=Get_Input('POST','DB','OffRate','FLT',0,1,0,0);
				$NewRowInfo['InstallmentNo']=Get_Input('POST','DB','InstallmentNo','INT',0,99,0,0);
				$NewRowInfo['ServiceType']=Get_Input('POST','DB','ServiceType','ARRAY',array("Base","IP","ExtraCredit","Other"),0,0,0);
				if($NewRowInfo['InstallmentNo']>0){
					$NewRowInfo['InstallmentPeriod']=Get_Input('POST','DB','InstallmentPeriod','INT',1,99,0,0);
					$NewRowInfo['InstallmentFirstCash']=Get_Input('POST','DB','InstallmentFirstCash','ARRAY',array("Yes","No"),0,0,0);
				}
				else{
					$NewRowInfo['InstallmentPeriod']=0;
					$NewRowInfo['InstallmentFirstCash']='No';
				}
				
				$NewRowInfo['AvailableFromDate']=Get_Input('POST','DB','AvailableFromDate','DateOrBlank',0,0,0,0);
				$NewRowInfo['AvailableToDate']=Get_Input('POST','DB','AvailableToDate','DateOrBlank',0,0,0,0);
				
				
				$NewRowInfo['Category1']=Get_Input('POST','DB','Category1','STR',0,50,0,0);
				if($NewRowInfo['ServiceType']=='Base'){
					$NewRowInfo['Category2']=Get_Input('POST','DB','Category2','STR',0,50,0,0);
					$NewRowInfo['STrA']=Get_Input('POST','DB','STrA','INT',0,4294967295,0,0);
					$NewRowInfo['YTrA']=Get_Input('POST','DB','YTrA','INT',0,4294967295,0,0);
					$NewRowInfo['MTrA']=Get_Input('POST','DB','MTrA','INT',0,4294967295,0,0);
					$NewRowInfo['WTrA']=Get_Input('POST','DB','WTrA','INT',0,4294967295,0,0);
					$NewRowInfo['DTrA']=Get_Input('POST','DB','DTrA','INT',0,4294967295,0,0);
					$NewRowInfo['Speed']=Get_Input('POST','DB','Speed','STR',1,10,0,0);
					$NewRowInfo['STiA']=Get_Input('POST','DB','STiA','INT',0,4294967295,0,0);
					$NewRowInfo['YTiA']=Get_Input('POST','DB','YTiA','INT',0,4294967295,0,0);
					$NewRowInfo['MTiA']=Get_Input('POST','DB','MTiA','INT',0,4294967295,0,0);
					$NewRowInfo['WTiA']=Get_Input('POST','DB','WTiA','INT',0,4294967295,0,0);
					$NewRowInfo['DTiA']=Get_Input('POST','DB','DTiA','INT',0,4294967295,0,0);
					$NewRowInfo['IPCount']=0;
					$NewRowInfo['FramedIP']='';
					$NewRowInfo['FramedRoute']='';
					
					$NewRowInfo['ActiveYear']=Get_Input('POST','DB','ActiveYear','INT',0,99,0,0);
					$NewRowInfo['ActiveMonth']=Get_Input('POST','DB','ActiveMonth','INT',0,99,0,0);
					$NewRowInfo['ActiveDay']=Get_Input('POST','DB','ActiveDay','INT',0,99,0,0);
					$NewRowInfo['AttachedGift_Id']=Get_Input('POST','DB','AttachedGift_Id','INT',0,4294967295,0,0);
					if(($NewRowInfo['AttachedGift_Id']>0)&&(DBSelectAsString("select Gift_Id from Hgift where Gift_Id='".$NewRowInfo['AttachedGift_Id']."'")<=0))
						ExitError("هدیه پیوست شده نامعتبر انتخاب شده");
					
					$NewRowInfo['ISFairService']=Get_Input('POST','DB','ISFairService','ARRAY',array("Yes","No"),0,0,0);
					if($NewRowInfo['ISFairService']=="Yes"){
						$NewRowInfo['FairMikrotikRate_Id']=Get_Input('POST','DB','FairMikrotikRate_Id','INT',1,4294967295,0,0);
						if(DBSelectAsString("select MikrotikRate_Id from Hmikrotikrate where MikrotikRate_Id='".$NewRowInfo['FairMikrotikRate_Id']."'")<=0)
							ExitError("سرعت میکروتیک نامعتبر در حالت منصفانه انتخاب شده");
					}
					else
						$NewRowInfo['FairMikrotikRate_Id']=0;
					if(($NewRowInfo['ActiveDay']+$NewRowInfo['ActiveMonth']+$NewRowInfo['ActiveYear'])==0)
						ExitError('لطفا یکی از فیلدهای تعداد سال،تعداد ماه و تعداد روز را پر کنید');
						
				}
				else if($NewRowInfo['ServiceType']=='ExtraCredit'){
					$NewRowInfo['ExtraType']=Get_Input('POST','DB','ExtraType','ARRAY',array("Traffic","Time","ActiveDay"),0,0,0);
					if($NewRowInfo['ExtraType']=='Traffic'){
						$NewRowInfo['ExtraTraffic']=Get_Input('POST','DB','ExtraTraffic','INT',0,4294967295,0,0);
						if($NewRowInfo['ExtraTraffic']==0) ExitError('اضافه ترافیک باید بزرگتر از ۰ باشد'); 
					}
					else if($NewRowInfo['ExtraType']=='Time'){
						$NewRowInfo['ExtraTime']=Get_Input('POST','DB','ExtraTime','INT',0,4294967295,0,0);
						if($NewRowInfo['ExtraTime']==0) ExitError('اضافه زمان باید بزرگتر از ۰ باشد'); 
					}
					else{
						$NewRowInfo['ExtraActiveDay']=Get_Input('POST','DB','ExtraActiveDay','INT',0,4294967295,0,0);
						if($NewRowInfo['ExtraActiveDay']==0) ExitError('اضافه روز باید بزرگتر از ۰ باشد'); 
					}
					$NewRowInfo['YTrA']=0;
					$NewRowInfo['MTrA']=0;
					$NewRowInfo['WTrA']=0;
					$NewRowInfo['DTrA']=0;
					$NewRowInfo['Speed']='';
					$NewRowInfo['YTiA']=0;
					$NewRowInfo['MTiA']=0;
					$NewRowInfo['WTiA']=0;
					$NewRowInfo['DTiA']=0;
					$NewRowInfo['IPCount']=0;
					$NewRowInfo['FramedIP']='';
					$NewRowInfo['FramedRoute']='';
					$NewRowInfo['AttachedGift_Id']=0;
					$NewRowInfo['ISFairService']='No';
					$NewRowInfo['FairMikrotikRate_Id']=0;
					$NewRowInfo['Category2']='';
				}
				else if($NewRowInfo['ServiceType']=='IP'){
					$NewRowInfo['STrA']=0;
					$NewRowInfo['YTrA']=0;
					$NewRowInfo['MTrA']=0;
					$NewRowInfo['WTrA']=0;
					$NewRowInfo['DTrA']=0;
					$NewRowInfo['Speed']='';
					$NewRowInfo['STiA']=0;
					$NewRowInfo['YTiA']=0;
					$NewRowInfo['MTiA']=0;
					$NewRowInfo['MTiA']=0;
					$NewRowInfo['DTiA']=0;
					$NewRowInfo['IPCount']=Get_Input('POST','DB','IPCount','INT',1,9999999,0,0);
					$NewRowInfo['FramedIP']=Get_Input('POST','DB','FramedIP','STR',1,15,0,0);
					$NewRowInfo['FramedRoute']=Get_Input('POST','DB','FramedRoute','STR',0,128,0,0);
					$NewRowInfo['ActiveYear']=0;
					$NewRowInfo['ActiveMonth']=0;
					$NewRowInfo['ActiveDay']=0;
					$NewRowInfo['AttachedGift_Id']=0;
					$NewRowInfo['ISFairService']='No';
					$NewRowInfo['FairMikrotikRate_Id']=0;
					$NewRowInfo['Category2']='';
				}
				else if($NewRowInfo['ServiceType']=='Other'){
					$NewRowInfo['STrA']=0;
					$NewRowInfo['YTrA']=0;
					$NewRowInfo['MTrA']=0;
					$NewRowInfo['WTrA']=0;
					$NewRowInfo['DTrA']=0;
					$NewRowInfo['Speed']='';
					$NewRowInfo['STiA']=0;
					$NewRowInfo['YTiA']=0;
					$NewRowInfo['MTiA']=0;
					$NewRowInfo['WTiA']=0;
					$NewRowInfo['DTiA']=0;
					$NewRowInfo['IPCount']=0;
					$NewRowInfo['FramedIP']='';
					$NewRowInfo['FramedRoute']='';
					$NewRowInfo['AttachedGift_Id']=0;
					$NewRowInfo['ISFairService']='No';
					$NewRowInfo['FairMikrotikRate_Id']=0;
					$NewRowInfo['Category2']='';
				}

				$NewRowInfo['MaxYearlyCount']=Get_Input('POST','DB','MaxYearlyCount','INT',0,99,0,0);
				$NewRowInfo['MaxMonthlyCount']=Get_Input('POST','DB','MaxMonthlyCount','INT',0,99,0,0);
				$NewRowInfo['MaxActiveCount']=Get_Input('POST','DB','MaxActiveCount','INT',0,9999999,0,0);
				$NewRowInfo['UserChoosable']=Get_Input('POST','DB','UserChoosable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ResellerChoosable']=Get_Input('POST','DB','ResellerChoosable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['OnBuyFromWebsiteSMS']=Get_Input('POST','DB','OnBuyFromWebsiteSMS','STR',0,250,0,0);
				$NewRowInfo['OnAddByResellerSMS']=Get_Input('POST','DB','OnAddByResellerSMS','STR',0,250,0,0);
				if(($NewRowInfo['OnBuyFromWebsiteSMS']=="")&&($NewRowInfo['OnAddByResellerSMS']==""))
					$NewRowInfo['SMSExpireTime']=0;
				else
					$NewRowInfo['SMSExpireTime']=Get_Input('POST','DB','SMSExpireTime','INT',60,99999,0,0);
				
				//----------------------
				$sql= "insert Hservice set ServiceCDT=Now(),";
				$sql.="ServiceName='".$NewRowInfo['ServiceName']."',";
				$sql.="Category1='".$NewRowInfo['Category1']."',";
				$sql.="Category2='".$NewRowInfo['Category2']."',";
				$sql.="Description='".$NewRowInfo['Description']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="Price='".$NewRowInfo['Price']."',";
				$sql.="OffRate='".$NewRowInfo['OffRate']."',";
				$sql.="InstallmentNo='".$NewRowInfo['InstallmentNo']."',";
				$sql.="InstallmentPeriod='".$NewRowInfo['InstallmentPeriod']."',";
				$sql.="InstallmentFirstCash='".$NewRowInfo['InstallmentFirstCash']."',";
				$sql.="AvailableFromDate='".$NewRowInfo['AvailableFromDate']."',";
				$sql.="AvailableToDate='".$NewRowInfo['AvailableToDate']."',";
				$sql.="ServiceType='".$NewRowInfo['ServiceType']."',";
				$sql.="STrA=1048576*'".$NewRowInfo['STrA']."',";
				$sql.="YTrA=1048576*'".$NewRowInfo['YTrA']."',";
				$sql.="MTrA=1048576*'".$NewRowInfo['MTrA']."',";
				$sql.="WTrA=1048576*'".$NewRowInfo['WTrA']."',";
				$sql.="DTrA=1048576*'".$NewRowInfo['DTrA']."',";
				$sql.="STiA='".$NewRowInfo['STiA']."',";
				$sql.="YTiA='".$NewRowInfo['YTiA']."',";
				$sql.="MTiA='".$NewRowInfo['MTiA']."',";
				$sql.="WTiA='".$NewRowInfo['WTiA']."',";
				$sql.="DTiA='".$NewRowInfo['DTiA']."',";
				$sql.="ExtraTraffic=1048576*'".$NewRowInfo['ExtraTraffic']."',";
				$sql.="ExtraTime='".$NewRowInfo['ExtraTime']."',";
				$sql.="ExtraActiveDay='".$NewRowInfo['ExtraActiveDay']."',";

				$sql.="ActiveYear='".$NewRowInfo['ActiveYear']."',";
				$sql.="ActiveMonth='".$NewRowInfo['ActiveMonth']."',";
				$sql.="ActiveDay='".$NewRowInfo['ActiveDay']."',";
				$sql.="MaxYearlyCount='".$NewRowInfo['MaxYearlyCount']."',";
				$sql.="MaxMonthlyCount='".$NewRowInfo['MaxMonthlyCount']."',";
				$sql.="MaxActiveCount='".$NewRowInfo['MaxActiveCount']."',";
				$sql.="UserChoosable='".$NewRowInfo['UserChoosable']."',";
				$sql.="ResellerChoosable='".$NewRowInfo['ResellerChoosable']."',";
				$sql.="OnBuyFromWebsiteSMS='".$NewRowInfo['OnBuyFromWebsiteSMS']."',";
				$sql.="OnAddByResellerSMS='".$NewRowInfo['OnAddByResellerSMS']."',";
				$sql.="SMSExpireTime='".$NewRowInfo['SMSExpireTime']."',";
				$sql.="Speed='".$NewRowInfo['Speed']."',";
				$sql.="FramedIP='".$NewRowInfo['FramedIP']."',";
				$sql.="FramedRoute='".$NewRowInfo['FramedRoute']."',";
				$sql.="IPCount='".$NewRowInfo['IPCount']."',";
				$sql.="AttachedGift_Id='".$NewRowInfo['AttachedGift_Id']."',";
				$sql.="ISFairService='".$NewRowInfo['ISFairService']."',";
				$sql.="FairMikrotikRate_Id='".$NewRowInfo['FairMikrotikRate_Id']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Service_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','Service',$RowId,'Service');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSServiceEditRender Update ******************************************");
				exitifnotpermit(0,"CRM.Service.Edit");
				$NewRowInfo=array();
	
				$NewRowInfo['Service_Id']=Get_Input('POST','DB','Service_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ServiceName']=Get_Input('POST','DB','ServiceName','STR',1,128,0,0);
				$NewRowInfo['Description']=Get_Input('POST','DB','Description','STR',0,254,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['Price']=Get_Input('POST','DB','Price','PRC',1,14,0,0);
				if($NewRowInfo['Price']<0)
						ExitError('قیمت نامعتبر');
				$NewRowInfo['OffRate']=Get_Input('POST','DB','OffRate','FLT',0,1,0,0);
				$NewRowInfo['InstallmentNo']=Get_Input('POST','DB','InstallmentNo','INT',0,99,0,0);
				if($NewRowInfo['InstallmentNo']>0){
					$NewRowInfo['InstallmentPeriod']=Get_Input('POST','DB','InstallmentPeriod','INT',1,99,0,0);
					$NewRowInfo['InstallmentFirstCash']=Get_Input('POST','DB','InstallmentFirstCash','ARRAY',array("Yes","No"),0,0,0);
				}
				else{
					$NewRowInfo['InstallmentPeriod']=0;
					$NewRowInfo['InstallmentFirstCash']='No';
				}
				$AvailableFromDate=Get_Input('POST','DB','AvailableFromDate','DateOrBlank',0,0,0,0);
				$NewRowInfo['AvailableFromDate']=Get_Input('POST','DB','AvailableFromDate','STR',0,10,0,0);
				$AvailableToDate=Get_Input('POST','DB','AvailableToDate','DateOrBlank',0,0,0,0);
				$NewRowInfo['AvailableToDate']=Get_Input('POST','DB','AvailableToDate','STR',0,10,0,0);
				$NewRowInfo['ServiceType']=Get_Input('POST','DB','ServiceType','ARRAY',array("Base","IP","ExtraCredit","Other"),0,0,0);
				$NewRowInfo['Category1']=Get_Input('POST','DB','Category1','STR',0,50,0,0);
				
				$sql= "update Hservice set ServiceCDT=Now(),";
				$sql.="ServiceName='".$NewRowInfo['ServiceName']."',";
				$sql.="Description='".$NewRowInfo['Description']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="Price='".$NewRowInfo['Price']."',";
				$sql.="OffRate='".$NewRowInfo['OffRate']."',";
				$sql.="InstallmentNo='".$NewRowInfo['InstallmentNo']."',";
				$sql.="InstallmentPeriod='".$NewRowInfo['InstallmentPeriod']."',";
				$sql.="InstallmentFirstCash='".$NewRowInfo['InstallmentFirstCash']."',";
				$sql.="AvailableFromDate='".$AvailableFromDate."',";
				$sql.="AvailableToDate='".$AvailableToDate."',";
				$sql.="Category1='".$NewRowInfo['Category1']."',";
				
				if($NewRowInfo['ServiceType']=='Base'){
					$NewRowInfo['Category2']=Get_Input('POST','DB','Category2','STR',0,50,0,0);
					$NewRowInfo['MaxYearlyCount']=Get_Input('POST','DB','MaxYearlyCount','INT',0,99,0,0);
					$NewRowInfo['MaxMonthlyCount']=Get_Input('POST','DB','MaxMonthlyCount','INT',0,99,0,0);
					$NewRowInfo['MaxActiveCount']=Get_Input('POST','DB','MaxActiveCount','INT',0,9999999,0,0);
					$NewRowInfo['Speed']=Get_Input('POST','DB','Speed','STR',1,10,0,0);

					$NewRowInfo['STrA']=Get_Input('POST','DB','STrA','INT',0,999999999,0,0);
					$NewRowInfo['YTrA']=Get_Input('POST','DB','YTrA','INT',0,999999999,0,0);
					$NewRowInfo['MTrA']=Get_Input('POST','DB','MTrA','INT',0,999999999,0,0);
					$NewRowInfo['WTrA']=Get_Input('POST','DB','WTrA','INT',0,999999999,0,0);
					$NewRowInfo['DTrA']=Get_Input('POST','DB','DTrA','INT',0,999999999,0,0);
					$NewRowInfo['STiA']=Get_Input('POST','DB','STiA','INT',0,999999999,0,0);
					$NewRowInfo['YTiA']=Get_Input('POST','DB','YTiA','INT',0,999999999,0,0);
					$NewRowInfo['MTiA']=Get_Input('POST','DB','MTiA','INT',0,999999999,0,0);
					$NewRowInfo['WTiA']=Get_Input('POST','DB','WTiA','INT',0,999999999,0,0);
					$NewRowInfo['DTiA']=Get_Input('POST','DB','DTiA','INT',0,999999999,0,0);
					$NewRowInfo['ActiveYear']=Get_Input('POST','DB','ActiveYear','INT',0,99,0,0);
					$NewRowInfo['ActiveMonth']=Get_Input('POST','DB','ActiveMonth','INT',0,99,0,0);
					$NewRowInfo['ActiveDay']=Get_Input('POST','DB','ActiveDay','INT',0,99,0,0);
					if(($NewRowInfo['ActiveDay']+$NewRowInfo['ActiveMonth']+$NewRowInfo['ActiveYear'])==0)
						ExitError('لطفا یکی از فیلدهای تعداد سال،تعداد ماه و تعداد روز را پر کنید');
					
					$NewRowInfo['AttachedGift_Id']=Get_Input('POST','DB','AttachedGift_Id','INT',0,4294967295,0,0);
					if(($NewRowInfo['AttachedGift_Id']>0)&&(DBSelectAsString("select Gift_Id from Hgift where Gift_Id='".$NewRowInfo['AttachedGift_Id']."'")<=0))
						ExitError("هدیه پیوست شده نامعتبر انتخاب شده");
					
					$NewRowInfo['ISFairService']=Get_Input('POST','DB','ISFairService','ARRAY',array("Yes","No"),0,0,0);
					if($NewRowInfo['ISFairService']=="Yes"){
						$NewRowInfo['FairMikrotikRate_Id']=Get_Input('POST','DB','FairMikrotikRate_Id','INT',1,4294967295,0,0);
						if(DBSelectAsString("select MikrotikRate_Id from Hmikrotikrate where MikrotikRate_Id='".$NewRowInfo['FairMikrotikRate_Id']."'")<=0)
							ExitError("سرعت میکروتیک نامعتبر در حالت منصفانه انتخاب شده");
					}
					else
						$NewRowInfo['FairMikrotikRate_Id']=0;

					$sql.="STrA=1048576*'".$NewRowInfo['STrA']."',";
					$sql.="YTrA=1048576*'".$NewRowInfo['YTrA']."',";
					$sql.="MTrA=1048576*'".$NewRowInfo['MTrA']."',";
					$sql.="WTrA=1048576*'".$NewRowInfo['WTrA']."',";
					$sql.="DTrA=1048576*'".$NewRowInfo['DTrA']."',";
					$sql.="STiA='".$NewRowInfo['STiA']."',";
					$sql.="YTiA='".$NewRowInfo['YTiA']."',";
					$sql.="MTiA='".$NewRowInfo['MTiA']."',";
					$sql.="WTiA='".$NewRowInfo['WTiA']."',";
					$sql.="DTiA='".$NewRowInfo['DTiA']."',";
					$sql.="ActiveYear='".$NewRowInfo['ActiveYear']."',";
					$sql.="ActiveMonth='".$NewRowInfo['ActiveMonth']."',";
					$sql.="ActiveDay='".$NewRowInfo['ActiveDay']."',";
					$sql.="Category2='".$NewRowInfo['Category2']."',";
					$sql.="Speed='".$NewRowInfo['Speed']."',";
					$sql.="MaxYearlyCount='".$NewRowInfo['MaxYearlyCount']."',";
					$sql.="MaxMonthlyCount='".$NewRowInfo['MaxMonthlyCount']."',";
					$sql.="MaxActiveCount='".$NewRowInfo['MaxActiveCount']."',";
					$sql.="AttachedGift_Id='".$NewRowInfo['AttachedGift_Id']."',";
					$sql.="ISFairService='".$NewRowInfo['ISFairService']."',";
					$sql.="FairMikrotikRate_Id='".$NewRowInfo['FairMikrotikRate_Id']."',";
				}
				else if($NewRowInfo['ServiceType']=='ExtraCredit'){
					$NewRowInfo['MaxYearlyCount']=Get_Input('POST','DB','MaxYearlyCount','INT',0,99,0,0);
					$NewRowInfo['MaxMonthlyCount']=Get_Input('POST','DB','MaxMonthlyCount','INT',0,99,0,0);
					$sql.="MaxMonthlyCount='".$NewRowInfo['MaxMonthlyCount']."',";
					$sql.="MaxYearlyCount='".$NewRowInfo['MaxYearlyCount']."',";
					
					
					$NewRowInfo['ExtraType']=Get_Input('POST','DB','ExtraType','ARRAY',array("Traffic","Time","ActiveDay"),0,0,0);
					if($NewRowInfo['ExtraType']=='Traffic'){
						$NewRowInfo['ExtraTraffic']=Get_Input('POST','DB','ExtraTraffic','INT',0,4294967295,0,0);
						if($NewRowInfo['ExtraTraffic']==0)
							ExitError('اضافه ترافیک باید بزرگتر از ۰ باشد'); 
						$NewRowInfo['ExtraTime']=0;
						$NewRowInfo['ExtraActiveDay']=0;
					}
					else if($NewRowInfo['ExtraType']=='Time'){
						$NewRowInfo['ExtraTraffic']=0;
						$NewRowInfo['ExtraTime']=Get_Input('POST','DB','ExtraTime','INT',0,4294967295,0,0);
						if($NewRowInfo['ExtraTime']==0)
							ExitError('اضافه زمان باید بزرگتر از ۰ باشد');
						$NewRowInfo['ExtraActiveDay']=0;
					}
					else{
						$NewRowInfo['ExtraTraffic']=0;
						$NewRowInfo['ExtraTime']=0;
						$NewRowInfo['ExtraActiveDay']=Get_Input('POST','DB','ExtraActiveDay','INT',0,4294967295,0,0);
						if($NewRowInfo['ExtraActiveDay']==0)
							ExitError('اضافه روز باید بزرگتر از ۰ باشد');
					}
					$sql.="ExtraTraffic=1048576*'".$NewRowInfo['ExtraTraffic']."',";
					$sql.="ExtraTime='".$NewRowInfo['ExtraTime']."',";
					$sql.="ExtraActiveDay='".$NewRowInfo['ExtraActiveDay']."',";
					
				}
				else if($NewRowInfo['ServiceType']=='IP'){
					$NewRowInfo['IPCount']=Get_Input('POST','DB','IPCount','INT',1,9999999,0,0);
					$NewRowInfo['FramedIP']=Get_Input('POST','DB','FramedIP','STR',1,15,0,0);
					$NewRowInfo['FramedRoute']=Get_Input('POST','DB','FramedRoute','STR',0,128,0,0);
					$NewRowInfo['ActiveYear']=0;
					$NewRowInfo['ActiveMonth']=0;
					$NewRowInfo['ActiveDay']=0;
					$sql.="IPCount='".$NewRowInfo['IPCount']."',";
					$sql.="FramedIP='".$NewRowInfo['FramedIP']."',";
					$sql.="FramedRoute='".$NewRowInfo['FramedRoute']."',";
					
					$sql.="ActiveYear='".$NewRowInfo['ActiveYear']."',";
					$sql.="ActiveMonth='".$NewRowInfo['ActiveMonth']."',";
					$sql.="ActiveDay='".$NewRowInfo['ActiveDay']."',";
				}
				else if($NewRowInfo['ServiceType']=='Other'){
				}

				$OldRowInfo= LoadRowInfo("Hservice","Service_Id='".$NewRowInfo['Service_Id']."'");
				//DSDebug(1,DSPrintAsrray($OldRowInfo));

				$NewRowInfo['UserChoosable']=Get_Input('POST','DB','UserChoosable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ResellerChoosable']=Get_Input('POST','DB','ResellerChoosable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['OnBuyFromWebsiteSMS']=Get_Input('POST','DB','OnBuyFromWebsiteSMS','STR',0,250,0,0);
				$NewRowInfo['OnAddByResellerSMS']=Get_Input('POST','DB','OnAddByResellerSMS','STR',0,250,0,0);
				if(($NewRowInfo['OnBuyFromWebsiteSMS']=="")&&($NewRowInfo['OnAddByResellerSMS']==""))
					$NewRowInfo['SMSExpireTime']=0;
				else
					$NewRowInfo['SMSExpireTime']=Get_Input('POST','DB','SMSExpireTime','INT',60,99999,0,0);
				//----------------------
				$sql.="UserChoosable='".$NewRowInfo['UserChoosable']."',";
				$sql.="ResellerChoosable='".$NewRowInfo['ResellerChoosable']."',";
				$sql.="OnBuyFromWebsiteSMS='".$NewRowInfo['OnBuyFromWebsiteSMS']."',";
				$sql.="OnAddByResellerSMS='".$NewRowInfo['OnAddByResellerSMS']."',";
				$sql.="SMSExpireTime='".$NewRowInfo['SMSExpireTime']."'";
				$sql.=" Where (IsDel='No')And(Service_Id='".$NewRowInfo['Service_Id']."')";//delete service can not delete
				
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Service',$NewRowInfo['Service_Id'],'Service',"Update Fail,Table=Service affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Service affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Service',$NewRowInfo['Service_Id'],'Service')){
					logunfair("UnFair",'Service',$NewRowInfo['Service_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
    case "DisconnectUser": 
				DSDebug(1,"DSServiceEditRender->Insert");
				exitifnotpermit(0,"CRM.Service.DisconnectUser");
				
				$Service_Id=Get_Input('GET','DB','Service_Id','STR',1,128,0,0);
				DBInsert(	"Insert Tonline_dcqueue(Online_RadiusUser_Id,CDT) ".
							"Select Online_RadiusUser_Id,Now() From Tonline_radiususer o_r left join Huser u_u on o_r.User_Id=u_u.User_Id left join  Huser_servicebase u_sb on u_u.User_ServiceBase_Id=u_sb.User_ServiceBase_Id Where o_r.ServiceInfo_Id=1 and u_u.Service_Id=$Service_Id");				
							
				echo "OK~";
				
        break;
	case "SelectGift":
				DSDebug(1,"DSServiceEditRender -> SelectGift");	
				exitifnotpermit(0,"CRM.Service.View");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("Select 0 As Gift_Id,' -- هیچی -- ' As GiftName union SELECT Gift_Id,GiftName FROM Hgift where GiftIsDel='No' and GiftISEnable='Yes' order by GiftName ASC","","Gift_Id,GiftName","","");
		break;
    case "SelectMikrotikRate":
				DSDebug(1,"DSServiceEditRender SelectMikrotikRate *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT MikrotikRate_Id as GiftMikrotikRate_Id,MikrotikRateName FROM Hmikrotikrate order by MikrotikRateName ASC";
				$options->render_sql($sql,"","GiftMikrotikRate_Id,MikrotikRateName","","");
        break;
	case "SelectCategory":
				DSDebug(1,"DSServiceEditRender SelectCategory *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$n=Get_Input('GET','DB','n','ARRAY',Array(1,2),0,0,0);
				
				$sql="SELECT distinct Category$n FROM Hservice where IsDel='No' and Category$n<>'' order by Category$n ASC";
				$options->render_sql($sql,"","Category$n,Category$n","","");
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
