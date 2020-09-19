<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSWebAccessEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSWebAccessEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.WebAccess.View");
				$WebAccess_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,WebAccess_Id,WebAccessName,CanWebAccess,SessionTimeout,ShowDailyUsage,ShowPaymentHistory,ShowServiceHistory,ShowGiftHistory,CanActivateGift,CanAbandonGift,".
				"ShowInstallmentHistory,ShowConnectionHistory,CanChangePassword,CanGetEmergencyTraffic,AutoWebLogin,AutoWebLoginDelay,AutoWebLoginMode,CanPayMoney,MinPay,".
				"ShowSendFile,CanSendFile,CanActiveServiceReserve,CanBuyServiceBase,CanBuyServiceExtraTraffic,CanBuyServiceExtraTime,CanBuyServiceIP,CanBuyServiceOther,ServiceOtherButtonWebTitleFa,ServiceOtherButtonWebTitleEn,CanInvoice,CanTransferCredit,".
				"round(MinTransferAmount/1048576) As MinTransferAmount,".
				"round(MaxTransferAmount/1048576) As MaxTransferAmount,".
				"round(NonTransferableAmount/1048576) As NonTransferableAmount,".
				"YearlyTransferCountLimit,round(YearlyTransferAmountLimit/1048576) As YearlyTransferAmountLimit,".
				"MonthlyTransferCountLimit,round(MonthlyTransferAmountLimit/1048576) As MonthlyTransferAmountLimit,".
				"DailyTransferCountLimit,round(DailyTransferAmountLimit/1048576) As DailyTransferAmountLimit,CanDisconnect,ShowAgreement ".
					"From Hwebaccess where WebAccess_Id='$WebAccess_Id'";
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
				DSDebug(1,"DSWebAccessEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.WebAccess.Add");
				$NewRowInfo=array();
				$NewRowInfo['WebAccessName']=Get_Input('POST','DB','WebAccessName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['SessionTimeout']=Get_Input('POST','DB','SessionTimeout','INT',600,86400,0,0);
				$NewRowInfo['CanWebAccess']=Get_Input('POST','DB','CanWebAccess','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowDailyUsage']=Get_Input('POST','DB','ShowDailyUsage','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowPaymentHistory']=Get_Input('POST','DB','ShowPaymentHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowServiceHistory']=Get_Input('POST','DB','ShowServiceHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowGiftHistory']=Get_Input('POST','DB','ShowGiftHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanActivateGift']=Get_Input('POST','DB','CanActivateGift','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanAbandonGift']=Get_Input('POST','DB','CanAbandonGift','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowInstallmentHistory']=Get_Input('POST','DB','ShowInstallmentHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowConnectionHistory']=Get_Input('POST','DB','ShowConnectionHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanChangePassword']=Get_Input('POST','DB','CanChangePassword','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceBase']=Get_Input('POST','DB','CanBuyServiceBase','ARRAY',array("Yes","No"),0,0,0);
				
				$NewRowInfo['CanActiveServiceReserve']=Get_Input('POST','DB','CanActiveServiceReserve','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceExtraTraffic']=Get_Input('POST','DB','CanBuyServiceExtraTraffic','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceExtraTime']=Get_Input('POST','DB','CanBuyServiceExtraTime','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceIP']=Get_Input('POST','DB','CanBuyServiceIP','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceOther']=Get_Input('POST','DB','CanBuyServiceOther','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CanBuyServiceOther']=='Yes'){
					$NewRowInfo['ServiceOtherButtonWebTitleFa']=Get_Input('POST','DB','ServiceOtherButtonWebTitleFa','STR',0,32,0,0);
					$NewRowInfo['ServiceOtherButtonWebTitleEn']=Get_Input('POST','DB','ServiceOtherButtonWebTitleEn','STR',0,32,0,0);
				}
				else{
					$NewRowInfo['ServiceOtherButtonWebTitleFa']='خرید سرویس ویژه';
					$NewRowInfo['ServiceOtherButtonWebTitleEn']='Buy Special Service';
				}
				$NewRowInfo['CanInvoice']=Get_Input('POST','DB','CanInvoice','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanTransferCredit']=Get_Input('POST','DB','CanTransferCredit','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CanTransferCredit']=='Yes'){
					$NewRowInfo['MinTransferAmount']=Get_Input('POST','DB','MinTransferAmount','INT',0,4294967295,0,0);
					$NewRowInfo['MaxTransferAmount']=Get_Input('POST','DB','MaxTransferAmount','INT',0,4294967295,0,0);
					$NewRowInfo['NonTransferableAmount']=Get_Input('POST','DB','NonTransferableAmount','INT',0,4294967295,0,0);
					$NewRowInfo['YearlyTransferCountLimit']=Get_Input('POST','DB','YearlyTransferCountLimit','INT',0,9999,0,0);
					$NewRowInfo['YearlyTransferAmountLimit']=Get_Input('POST','DB','YearlyTransferAmountLimit','INT',0,4294967295,0,0);
					$NewRowInfo['MonthlyTransferCountLimit']=Get_Input('POST','DB','MonthlyTransferCountLimit','INT',0,9999,0,0);
					$NewRowInfo['MonthlyTransferAmountLimit']=Get_Input('POST','DB','MonthlyTransferAmountLimit','INT',0,4294967295,0,0);
					$NewRowInfo['DailyTransferCountLimit']=Get_Input('POST','DB','DailyTransferCountLimit','INT',0,9999,0,0);
					$NewRowInfo['DailyTransferAmountLimit']=Get_Input('POST','DB','DailyTransferAmountLimit','INT',0,4294967295,0,0);
				}
				else{
					$NewRowInfo['MinTransferAmount']=0;
					$NewRowInfo['MaxTransferAmount']=0;
					$NewRowInfo['NonTransferableAmount']=512;
					$NewRowInfo['YearlyTransferCountLimit']=0;
					$NewRowInfo['YearlyTransferAmountLimit']=0;
					$NewRowInfo['MonthlyTransferCountLimit']=0;
					$NewRowInfo['MonthlyTransferAmountLimit']=0;
					$NewRowInfo['DailyTransferCountLimit']=0;
					$NewRowInfo['DailyTransferAmountLimit']=0;
				}
				
				$NewRowInfo['CanGetEmergencyTraffic']=Get_Input('POST','DB','CanGetEmergencyTraffic','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['AutoWebLogin']=Get_Input('POST','DB','AutoWebLogin','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['AutoWebLogin']=='Yes'){
					$NewRowInfo['AutoWebLoginDelay']=Get_Input('POST','DB','AutoWebLoginDelay','INT',0,9,0,0);
					$NewRowInfo['AutoWebLoginMode']=Get_Input('POST','DB','AutoWebLoginMode','ARRAY',array("OnFinish","Always"),0,0,0);
				}
				else{
					$NewRowInfo['AutoWebLoginDelay']=0;
					$NewRowInfo['AutoWebLoginMode']="OnFinish";
				}
				
				$NewRowInfo['CanPayMoney']=Get_Input('POST','DB','CanPayMoney','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CanPayMoney']=='Yes'){
					$NewRowInfo['MinPay']=Get_Input('POST','DB','MinPay','PRC',1,14,0,0);
					if($NewRowInfo['MinPay']<0)
						ExitError("حداقل پرداخت باید بزرگتر یا مساوی ۰ باشد");
				}
				else
					$NewRowInfo['MinPay']=0;
				
				$NewRowInfo['ShowSendFile']=Get_Input('POST','DB','ShowSendFile','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['ShowSendFile']=='Yes')
					$NewRowInfo['CanSendFile']=Get_Input('POST','DB','CanSendFile','ARRAY',array("Yes","No"),0,0,0);
				else
					$NewRowInfo['CanSendFile']='No';
				$NewRowInfo['CanDisconnect']=Get_Input('POST','DB','CanDisconnect','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowAgreement']=Get_Input('POST','DB','ShowAgreement','ARRAY',array("Yes","No"),0,0,0);
				
														
				//----------------------
				$sql= "insert Hwebaccess set ";
				$sql.="WebAccessName='".$NewRowInfo['WebAccessName']."',";
				$sql.="SessionTimeout='".$NewRowInfo['SessionTimeout']."',";
				$sql.="CanWebAccess='".$NewRowInfo['CanWebAccess']."',";
				$sql.="ShowDailyUsage='".$NewRowInfo['ShowDailyUsage']."',";
				$sql.="ShowPaymentHistory='".$NewRowInfo['ShowPaymentHistory']."',";
				$sql.="ShowServiceHistory='".$NewRowInfo['ShowServiceHistory']."',";
				$sql.="ShowGiftHistory='".$NewRowInfo['ShowGiftHistory']."',";
				$sql.="CanActivateGift='".$NewRowInfo['CanActivateGift']."',";
				$sql.="CanAbandonGift='".$NewRowInfo['CanAbandonGift']."',";
				$sql.="ShowInstallmentHistory='".$NewRowInfo['ShowInstallmentHistory']."',";
				$sql.="ShowConnectionHistory='".$NewRowInfo['ShowConnectionHistory']."',";
				$sql.="CanChangePassword='".$NewRowInfo['CanChangePassword']."',";
				$sql.="CanBuyServiceBase='".$NewRowInfo['CanBuyServiceBase']."',";
				$sql.="CanActiveServiceReserve='".$NewRowInfo['CanActiveServiceReserve']."',";
				$sql.="CanBuyServiceExtraTraffic='".$NewRowInfo['CanBuyServiceExtraTraffic']."',";
				$sql.="CanBuyServiceExtraTime='".$NewRowInfo['CanBuyServiceExtraTime']."',";
				$sql.="CanBuyServiceIP='".$NewRowInfo['CanBuyServiceIP']."',";
				$sql.="CanBuyServiceOther='".$NewRowInfo['CanBuyServiceOther']."',";
				$sql.="ServiceOtherButtonWebTitleFa='".$NewRowInfo['ServiceOtherButtonWebTitleFa']."',";				
				$sql.="ServiceOtherButtonWebTitleEn='".$NewRowInfo['ServiceOtherButtonWebTitleEn']."',";				
				$sql.="CanInvoice='".$NewRowInfo['CanInvoice']."',";				
				$sql.="CanTransferCredit='".$NewRowInfo['CanTransferCredit']."',";
				$sql.="MinTransferAmount=1048576*'".$NewRowInfo['MinTransferAmount']."',";
				$sql.="MaxTransferAmount=1048576*'".$NewRowInfo['MaxTransferAmount']."',";
				$sql.="NonTransferableAmount=1048576*'".$NewRowInfo['NonTransferableAmount']."',";
				$sql.="YearlyTransferCountLimit='".$NewRowInfo['YearlyTransferCountLimit']."',";
				$sql.="YearlyTransferAmountLimit=1048576*'".$NewRowInfo['YearlyTransferAmountLimit']."',";
				$sql.="MonthlyTransferCountLimit='".$NewRowInfo['MonthlyTransferCountLimit']."',";
				$sql.="MonthlyTransferAmountLimit=1048576*'".$NewRowInfo['MonthlyTransferAmountLimit']."',";
				$sql.="DailyTransferCountLimit='".$NewRowInfo['DailyTransferCountLimit']."',";
				$sql.="DailyTransferAmountLimit=1048576*'".$NewRowInfo['DailyTransferAmountLimit']."',";
				$sql.="CanGetEmergencyTraffic='".$NewRowInfo['CanGetEmergencyTraffic']."',";
				$sql.="AutoWebLogin='".$NewRowInfo['AutoWebLogin']."',";
				$sql.="AutoWebLoginDelay='".$NewRowInfo['AutoWebLoginDelay']."',";
				$sql.="AutoWebLoginMode='".$NewRowInfo['AutoWebLoginMode']."',";
				$sql.="CanPayMoney='".$NewRowInfo['CanPayMoney']."',";
				$sql.="MinPay='".$NewRowInfo['MinPay']."',";
				$sql.="ShowSendFile='".$NewRowInfo['ShowSendFile']."',";
				$sql.="CanSendFile='".$NewRowInfo['CanSendFile']."',";
				$sql.="CanDisconnect='".$NewRowInfo['CanDisconnect']."',";
				$sql.="ShowAgreement='".$NewRowInfo['ShowAgreement']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','WebAccess',$RowId,'WebAccess');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSWebAccessEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.WebAccess.Edit");
				$NewRowInfo=array();
				
				$NewRowInfo['WebAccess_Id']=Get_Input('POST','DB','WebAccess_Id','INT',1,4294967295,0,0);
				$NewRowInfo['SessionTimeout']=Get_Input('POST','DB','SessionTimeout','INT',600,86400,0,0);
				$NewRowInfo['CanWebAccess']=Get_Input('POST','DB','CanWebAccess','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowDailyUsage']=Get_Input('POST','DB','ShowDailyUsage','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowPaymentHistory']=Get_Input('POST','DB','ShowPaymentHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowServiceHistory']=Get_Input('POST','DB','ShowServiceHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowGiftHistory']=Get_Input('POST','DB','ShowGiftHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanActivateGift']=Get_Input('POST','DB','CanActivateGift','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanAbandonGift']=Get_Input('POST','DB','CanAbandonGift','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowInstallmentHistory']=Get_Input('POST','DB','ShowInstallmentHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowConnectionHistory']=Get_Input('POST','DB','ShowConnectionHistory','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanChangePassword']=Get_Input('POST','DB','CanChangePassword','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceBase']=Get_Input('POST','DB','CanBuyServiceBase','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanActiveServiceReserve']=Get_Input('POST','DB','CanActiveServiceReserve','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceExtraTraffic']=Get_Input('POST','DB','CanBuyServiceExtraTraffic','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceExtraTime']=Get_Input('POST','DB','CanBuyServiceExtraTime','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceIP']=Get_Input('POST','DB','CanBuyServiceIP','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanBuyServiceOther']=Get_Input('POST','DB','CanBuyServiceOther','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CanBuyServiceOther']=='Yes'){
					$NewRowInfo['ServiceOtherButtonWebTitleFa']=Get_Input('POST','DB','ServiceOtherButtonWebTitleFa','STR',0,32,0,0);
					$NewRowInfo['ServiceOtherButtonWebTitleEn']=Get_Input('POST','DB','ServiceOtherButtonWebTitleEn','STR',0,32,0,0);
				}
				else{
					$NewRowInfo['ServiceOtherButtonWebTitleFa']='خرید سرویس ویژه';
					$NewRowInfo['ServiceOtherButtonWebTitleEn']='Buy Special Service';
				}
				$NewRowInfo['CanTransferCredit']=Get_Input('POST','DB','CanTransferCredit','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['CanInvoice']=Get_Input('POST','DB','CanInvoice','ARRAY',array("Yes","No"),0,0,0);
				
				if($NewRowInfo['CanTransferCredit']=='Yes'){
					$NewRowInfo['MinTransferAmount']=Get_Input('POST','DB','MinTransferAmount','INT',0,4294967295,0,0);
					$NewRowInfo['MaxTransferAmount']=Get_Input('POST','DB','MaxTransferAmount','INT',0,4294967295,0,0);
					$NewRowInfo['NonTransferableAmount']=Get_Input('POST','DB','NonTransferableAmount','INT',0,4294967295,0,0);
					$NewRowInfo['YearlyTransferCountLimit']=Get_Input('POST','DB','YearlyTransferCountLimit','INT',0,9999,0,0);
					$NewRowInfo['YearlyTransferAmountLimit']=Get_Input('POST','DB','YearlyTransferAmountLimit','INT',0,4294967295,0,0);
					$NewRowInfo['MonthlyTransferCountLimit']=Get_Input('POST','DB','MonthlyTransferCountLimit','INT',0,9999,0,0);
					$NewRowInfo['MonthlyTransferAmountLimit']=Get_Input('POST','DB','MonthlyTransferAmountLimit','INT',0,4294967295,0,0);
					$NewRowInfo['DailyTransferCountLimit']=Get_Input('POST','DB','DailyTransferCountLimit','INT',0,9999,0,0);
					$NewRowInfo['DailyTransferAmountLimit']=Get_Input('POST','DB','DailyTransferAmountLimit','INT',0,4294967295,0,0);
				}
				else{
					$NewRowInfo['MinTransferAmount']=0;
					$NewRowInfo['MaxTransferAmount']=0;
					$NewRowInfo['NonTransferableAmount']=512;
					$NewRowInfo['YearlyTransferCountLimit']=0;
					$NewRowInfo['YearlyTransferAmountLimit']=0;
					$NewRowInfo['MonthlyTransferCountLimit']=0;
					$NewRowInfo['MonthlyTransferAmountLimit']=0;
					$NewRowInfo['DailyTransferCountLimit']=0;
					$NewRowInfo['DailyTransferAmountLimit']=0;
				}
				$NewRowInfo['CanGetEmergencyTraffic']=Get_Input('POST','DB','CanGetEmergencyTraffic','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['AutoWebLogin']=Get_Input('POST','DB','AutoWebLogin','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['AutoWebLogin']=='Yes'){
					$NewRowInfo['AutoWebLoginDelay']=Get_Input('POST','DB','AutoWebLoginDelay','INT',0,9,0,0);
					$NewRowInfo['AutoWebLoginMode']=Get_Input('POST','DB','AutoWebLoginMode','ARRAY',array("OnFinish","Always"),0,0,0);
				}
				else{
					$NewRowInfo['AutoWebLoginDelay']=0;
					$NewRowInfo['AutoWebLoginMode']="OnFinish";
				}
				$NewRowInfo['CanPayMoney']=Get_Input('POST','DB','CanPayMoney','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CanPayMoney']=='Yes'){
					$NewRowInfo['MinPay']=Get_Input('POST','DB','MinPay','PRC',1,14,0,0);
					if($NewRowInfo['MinPay']<0)
						ExitError("حداقل پرداخت باید بزرگتر یا مساوی ۰ باشد");
				}
				else
					$NewRowInfo['MinPay']=0;
				
				$NewRowInfo['ShowSendFile']=Get_Input('POST','DB','ShowSendFile','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['ShowSendFile']=='Yes')
					$NewRowInfo['CanSendFile']=Get_Input('POST','DB','CanSendFile','ARRAY',array("Yes","No"),0,0,0);
				else
					$NewRowInfo['CanSendFile']='No';
				$NewRowInfo['CanDisconnect']=Get_Input('POST','DB','CanDisconnect','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ShowAgreement']=Get_Input('POST','DB','ShowAgreement','ARRAY',array("Yes","No"),0,0,0);
				
				$OldRowInfo= LoadRowInfo("Hwebaccess","WebAccess_Id='".$NewRowInfo['WebAccess_Id']."'");
				
				//----------------------
				
				$sql= "Update Hwebaccess set ";
				$sql.="CanWebAccess='".$NewRowInfo['CanWebAccess']."',";
				$sql.="SessionTimeout='".$NewRowInfo['SessionTimeout']."',";
				$sql.="ShowDailyUsage='".$NewRowInfo['ShowDailyUsage']."',";
				$sql.="ShowPaymentHistory='".$NewRowInfo['ShowPaymentHistory']."',";
				$sql.="ShowServiceHistory='".$NewRowInfo['ShowServiceHistory']."',";
				$sql.="ShowGiftHistory='".$NewRowInfo['ShowGiftHistory']."',";
				$sql.="CanActivateGift='".$NewRowInfo['CanActivateGift']."',";
				$sql.="CanAbandonGift='".$NewRowInfo['CanAbandonGift']."',";
				$sql.="ShowInstallmentHistory='".$NewRowInfo['ShowInstallmentHistory']."',";
				$sql.="ShowConnectionHistory='".$NewRowInfo['ShowConnectionHistory']."',";
				$sql.="CanChangePassword='".$NewRowInfo['CanChangePassword']."',";
				$sql.="CanBuyServiceBase='".$NewRowInfo['CanBuyServiceBase']."',";
				$sql.="CanActiveServiceReserve='".$NewRowInfo['CanActiveServiceReserve']."',";
				$sql.="CanBuyServiceExtraTraffic='".$NewRowInfo['CanBuyServiceExtraTraffic']."',";
				$sql.="CanBuyServiceExtraTime='".$NewRowInfo['CanBuyServiceExtraTime']."',";
				$sql.="CanBuyServiceIP='".$NewRowInfo['CanBuyServiceIP']."',";
				$sql.="CanBuyServiceOther='".$NewRowInfo['CanBuyServiceOther']."',";
				$sql.="ServiceOtherButtonWebTitleFa='".$NewRowInfo['ServiceOtherButtonWebTitleFa']."',";
				$sql.="ServiceOtherButtonWebTitleEn='".$NewRowInfo['ServiceOtherButtonWebTitleEn']."',";
				$sql.="CanInvoice='".$NewRowInfo['CanInvoice']."',";
				$sql.="CanTransferCredit='".$NewRowInfo['CanTransferCredit']."',";
				
				$sql.="MinTransferAmount=1048576*'".$NewRowInfo['MinTransferAmount']."',";
				$sql.="MaxTransferAmount=1048576*'".$NewRowInfo['MaxTransferAmount']."',";
				$sql.="NonTransferableAmount=1048576*'".$NewRowInfo['NonTransferableAmount']."',";
				$sql.="YearlyTransferCountLimit='".$NewRowInfo['YearlyTransferCountLimit']."',";
				$sql.="YearlyTransferAmountLimit=1048576*'".$NewRowInfo['YearlyTransferAmountLimit']."',";
				$sql.="MonthlyTransferCountLimit='".$NewRowInfo['MonthlyTransferCountLimit']."',";
				$sql.="MonthlyTransferAmountLimit=1048576*'".$NewRowInfo['MonthlyTransferAmountLimit']."',";
				$sql.="DailyTransferCountLimit='".$NewRowInfo['DailyTransferCountLimit']."',";
				$sql.="DailyTransferAmountLimit=1048576*'".$NewRowInfo['DailyTransferAmountLimit']."',";
				
				$sql.="CanGetEmergencyTraffic='".$NewRowInfo['CanGetEmergencyTraffic']."',";
				$sql.="AutoWebLogin='".$NewRowInfo['AutoWebLogin']."',";
				$sql.="AutoWebLoginMode='".$NewRowInfo['AutoWebLoginMode']."',";
				$sql.="AutoWebLoginDelay='".$NewRowInfo['AutoWebLoginDelay']."',";
				$sql.="CanPayMoney='".$NewRowInfo['CanPayMoney']."',";
				$sql.="MinPay='".$NewRowInfo['MinPay']."',";
				$sql.="ShowSendFile='".$NewRowInfo['ShowSendFile']."',";
				$sql.="CanSendFile='".$NewRowInfo['CanSendFile']."',";
				$sql.="CanDisconnect='".$NewRowInfo['CanDisconnect']."',";
				$sql.="ShowAgreement='".$NewRowInfo['ShowAgreement']."'";
				
				$sql.=" Where ";
				$sql.="(WebAccess_Id='".$NewRowInfo['WebAccess_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','WebAccess',$NewRowInfo['WebAccess_Id'],'WebAccess',"Update Fail,Table=WebAccess affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=WebAccess affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'WebAccess',$NewRowInfo['WebAccess_Id'],'WebAccess')){
					logunfair("UnFair",'WebAccess',$NewRowInfo['WebAccess_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
