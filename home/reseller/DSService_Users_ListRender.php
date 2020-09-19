<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSService_Users_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSService_Users_ListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Service.Users.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				function color_rows($row){
					//if ($row->get_index()%3)
					//$row->set_row_color("red");
					
					$data = $row->get_value("ServiceStatus");
					if(($data=='Active')||($data=='Using'))
						$row->set_row_style("color:blue");
					else if($data=='Used')
						$row->set_row_style("color:green");
					else if($data=='Cancel')
						$row->set_row_style("color:red");
				}

				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				$ServiceType=DBSelectAsString("Select ServiceType From Hservice Where Service_Id=$Service_Id");
				if($ServiceType=='Base')
					DSGridRender_Sql(100,
					"Select usb.User_ServiceBase_Id,r.ResellerName As Creator,Username,ServiceStatus,{$DT}DateStr(usb.StartDate) As StartDate,".
					"{$DT}DateStr(usb.EndDate) As EndDate,ExtraDay,PayPlan, ".
					"Format(usb.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
					",usb.InstallmentNo,usb.InstallmentPeriod,usb.InstallmentFirstCash,Format(usb.PayPrice,$PriceFloatDigit) AS PayPrice, ".
					"{$DT}DateTimeStr(CancelDT) As CancelDT,".
					"Format(usb.ReturnPrice,$PriceFloatDigit) AS ReturnPrice,{$DT}DateTimeStr(CDT) As CDT,VAT,Off ".
					"From Huser_servicebase usb Left Join Huser u on usb.User_Id=u.User_Id ".
					"Left join Hreseller r on usb.Creator_Id=r.Reseller_Id ".
					"Where (usb.Service_Id=$Service_Id)" .$sqlfilter." $SortOrder ",
					"User_ServiceBase_Id",
					"User_ServiceBase_Id,Creator,Username,ServiceStatus,StartDate,EndDate,ExtraDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,CancelDT,ReturnPrice,CDT,VAT,Off",
					"","","color_rows");
				else if($ServiceType=='ExtraCredit')
				DSGridRender_Sql(100,"User_ServiceExtraCredit_Id,r.ResellerName As Creator,Username,ServiceStatus,".
					"{$DT}DateTimeStr(ApplyDT) As ApplyDT, ".
					"Round(s.ExtraTraffic/1048576) As ExtraTraffic,s.ExtraTime As ExtraTime,s.ExtraActiveDay As ExtraActiveDay,PayPlan,".
					"Format(u_sec.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
					",u_sec.InstallmentNo,u_sec.InstallmentPeriod,u_sec.InstallmentFirstCash,Format(u_sec.PayPrice,$PriceFloatDigit) AS PayPrice, ".
					"Format(ReturnPrice,$PriceFloatDigit) AS ReturnPrice,{$DT}DateTimeStr(CancelDT) As CancelDT,u_sec.User_ServiceBase_Id,".
					"{$DT}DateTimeStr(CDT) As CDT,VAT,Off,Username as TransferUsername,round(TransferTraffic/(1024*1024)) as TransferTraffic ".
					"From  Huser_serviceextracredit u_sec ".
					"Left join Huser u on u_sec.User_Id=u.User_Id ".
					"Left join Hservice s on s.Service_Id=u_sec.Service_Id ".
					"Left join Hreseller r on u_sec.Creator_Id=r.Reseller_Id ".
					"Where (u_sec.Service_Id=$Service_Id)" .$sqlfilter." $SortOrder ",
					"User_ServiceExtraCredit_Id",
					"User_ServiceExtraCredit_Id,Creator,Username,ServiceStatus,ApplyDT,ExtraTraffic,ExtraTime,".
					"ExtraActiveDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,ReturnPrice,CancelDT,User_ServiceBase_Id,CDT,VAT,Off,TransferUsername,TransferTraffic",
					"","","color_rows");
				else if($ServiceType=='IP')
				DSGridRender_Sql(100,
					"Select User_ServiceIP_Id,ResellerName As Creator,Username,ServiceStatus,{$DT}DateStr(u_si.StartDate) As StartDate,Concat(datediff(u_si.EndDate, u_si.StartDate),' days') As Period,".
					"{$DT}DateStr(u_si.EndDate) As EndDate,PayPlan, ".
					"Format(u_si.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
					",u_si.InstallmentNo,u_si.InstallmentPeriod,u_si.InstallmentFirstCash,Format(u_si.PayPrice,$PriceFloatDigit) AS PayPrice, ".
					"{$DT}DateTimeStr(CancelDT) As CancelDT,".
					"Format(ReturnPrice,$PriceFloatDigit) AS ReturnPrice,{$DT}DateTimeStr(CDT) As CDT,VAT,Off ".
					"From Huser_serviceip u_si Left Join Huser u on u_si.User_Id=u.User_Id ".
					"Left join Hreseller r on u_si.Creator_Id=r.Reseller_Id ".
					"Where (u_si.Service_Id=$Service_Id)" .$sqlfilter." $SortOrder ",
					"User_ServiceIP_Id",
					"User_ServiceIP_Id,Creator,Username,ServiceStatus,StartDate,Period,EndDate,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,CancelDT,ReturnPrice,CDT,VAT,Off",
					"","","color_rows");
				else if($ServiceType=='Other')
				DSGridRender_Sql(100,"User_ServiceOther_Id,ResellerName as Creator,Username,ServiceStatus,PayPlan,".
					"Format(u_so.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
					",u_so.InstallmentNo,u_so.InstallmentPeriod,u_so.InstallmentFirstCash,Format(u_so.PayPrice,$PriceFloatDigit) AS PayPrice, ".
					"{$DT}DateTimeStr(CancelDT) As CancelDT,Format(ReturnPrice,$PriceFloatDigit) AS ReturnPrice,".
					"{$DT}DateTimeStr(CDT) As CDT,VAT,Off ".
					"From  Huser_serviceother u_so ".
					"Left join Huser u on u.User_Id=u_so.User_Id ".
					"Left join Hreseller r on u_so.Creator_Id=r.Reseller_Id ".
					"Where (u_so.Service_Id=$Service_Id)" .$sqlfilter." $SortOrder ",
					"User_ServiceOther_Id",
					"User_ServiceOther_Id,Creator,Username,ServiceStatus,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,CancelDT,ReturnPrice,CDT,VAT,Off",
					"","","color_rows");
					
       break;
		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>