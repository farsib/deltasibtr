<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_ServiceIP_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array('LoadIPRequest','CheckIPRequest',"list","SelectServiceIP","GetServicePrice","AddService","CancelService","LoadCancelServiceForm"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_ServiceIP_ListRender->List ********************************************");

				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$SortStr='Order By User_ServiceIP_Id Desc';
				function color_rows($row){
					//if ($row->get_index()%3)
					//$row->set_row_color("red");
					
					$data = $row->get_value("ServiceStatus");
					if(($data=='Active')||($data=='Using'))
						$style="color:blue;";
					else if($data=='Used')
						$style="color:green;";
					else if($data=='Cancel')
						$style="color:red;";
					else
						$style="";
					
					if($row->get_value("Visibility")=='Hidden')
						$style.="text-decoration:underline;font-style: italic;opacity:0.5;";
					elseif($row->get_value("Visibility")=='VeryHidden')
						$style.="text-decoration:line-through;font-style: oblique;opacity:0.2";
					
					$row->set_row_style($style);
				}
				
				DSGridRender_Sql(100,
					"Select User_ServiceIP_Id,ResellerName As Creator,ServiceStatus,ServiceName,{$DT}DateStr(StartDate) As StartDate,Concat(datediff(EndDate, StartDate),' days') As Period,".
					"{$DT}DateStr(EndDate) As EndDate,PayPlan, ".
					"Format(u_si.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
					",u_si.InstallmentNo,u_si.InstallmentPeriod,u_si.InstallmentFirstCash,Format(u_si.SavingOffUsed,$PriceFloatDigit) as SavingOffUsed,DirectOff,VAT,Format(u_si.PayPrice,$PriceFloatDigit) AS PayPrice, ".
					"{$DT}DateTimeStr(CancelDT) As CancelDT,".
					"Format(ReturnPrice,$PriceFloatDigit) AS ReturnPrice,{$DT}DateTimeStr(CDT) As CDT,Off,SavingOff ".
					",Visibility ".
					"From Huser_serviceip u_si Left Join Hservice s on u_si.Service_Id=s.Service_Id ".
					"Left join Hreseller r on u_si.Creator_Id=r.Reseller_Id ".
					"Where (User_Id=$User_Id)".(($LReseller_Id!=1)?"and(Visibility<>'VeryHidden')":"").$sqlfilter." $SortStr ",
					"User_ServiceIP_Id",
					"User_ServiceIP_Id,Creator,ServiceStatus,ServiceName,StartDate,Period,EndDate,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,SavingOffUsed,DirectOff,VAT,PayPrice,CancelDT,ReturnPrice,CDT,Off,SavingOff,Visibility",
					"","","color_rows");
       break;
    case "LoadCancelServiceForm":
				DSDebug(1,"DSVispEditRender LoadCancelServiceForm ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$User_ServiceIP_Id=Get_Input('GET','DB','User_ServiceIP_Id','INT',1,4294967295,0,0);
				$PayPrice=DBSelectAsString("SELECT PayPrice From Huser_serviceip where User_ServiceIP_Id='$User_ServiceIP_Id'");
				$ReturnPrice=DBSelectAsString("SELECT If(StartDate>Date(Now()),PayPrice,DateDiff(EndDate,Date(Now()))*(PayPrice/(DateDiff(EndDate,StartDate)))) From Huser_serviceip where User_ServiceIP_Id='$User_ServiceIP_Id'");
				if($ReturnPrice<0) $ReturnPrice=0;
				$PayPrice=number_format($PayPrice, $PriceFloatDigit, '.', '');
				$ReturnPrice=number_format($ReturnPrice, $PriceFloatDigit, '.', '');

				$sql="SELECT $PayPrice as PayPrice,$ReturnPrice as ReturnPrice";
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
    case "CancelService":
				DSDebug(1,"DSUser_ServiceIP_ListRender CancelService ******************************************");
				$User_ServiceIP_Id=Get_Input('GET','DB','User_ServiceIP_Id','INT',0,4294967295,0,0);
				$ServiceInfoArray=Array();
				CopyTableToArray($ServiceInfoArray,"Select User_Id,u_si.PayPrice,ServiceStatus,ServiceName,s.Service_Id ".
													"From Huser_serviceip u_si left join Hservice s on u_si.Service_Id=s.Service_Id ".
													"Where (User_ServiceIP_Id=$User_ServiceIP_Id)");
				$ServiceStatus=$ServiceInfoArray[0]["ServiceStatus"];
				$Service_Id=$ServiceInfoArray[0]["Service_Id"];
				if(($ServiceStatus==='Used')||($ServiceStatus==='Cancel'))
					ExitError('Service already cancel or used!');
				$User_Id=$ServiceInfoArray[0]["User_Id"];
				if($User_Id!=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0)){
					logsecurity('HackTry',"Try CancelService with wrong User_Id, Report sent to administrator");
					ExitError('Try CancelService with wrong User_Id, Report sent to administrator');
				}
				
				
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.Cancel");
				//$ReturnPrice=Get_Input('POST','DB','ReturnPrice','PRC',0,13,0,0);
				$ReturnPrice=DBSelectAsString("SELECT If(StartDate>Date(Now()),PayPrice,DateDiff(EndDate,Date(Now()))*(PayPrice/(DateDiff(EndDate,StartDate)))) From Huser_serviceip where User_ServiceIP_Id='$User_ServiceIP_Id'");
				if($ReturnPrice<0) $ReturnPrice=0;

				
				$PayPrice=$ServiceInfoArray[0]["PayPrice"];
				if($ReturnPrice>$PayPrice)
					ExitError("ReturnPrice($ReturnPrice)>Price($PayPrice)");
				if($ReturnPrice<0)
					ExitError("مبلغ برگشتی می بایست بزرگتر و یا مساوی با ۰ باشد");
				
				$ServiceName=DSescape($ServiceInfoArray[0]["ServiceName"]);
				$LogComment="Cancel Service IP. User_ServiceIP_Id=[$User_ServiceIP_Id] ServiceName=[$ServiceName]";
				
				DBUpdate("Update Huser_serviceip Set ServiceStatus='Cancel',ReturnPrice=$ReturnPrice,CancelDT=Now() Where User_ServiceIP_Id='$User_ServiceIP_Id'");
				$n=DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where User_ServiceIP_Id='$User_ServiceIP_Id' and GiftStatus='Pending'");
				if($n>0)
					$LogComment.=" And $n related pending gift canceled. ";
				
				$User_Gift_Id=DBSelectAsString("select User_Gift_Id from Huser_gift where User_ServiceIP_Id=$User_ServiceIP_Id and GiftStatus='Active'");
				if($User_Gift_Id>0){
					$TempArr=Array();
					$sql="select User_Gift_Id,{$DT}DateTimeStr(GiftEndDT) as GiftEndDT,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftExtraTi from Tuser_usage where User_Id='$User_Id'";
					$n=CopyTableToArray($TempArr,$sql);
					$LogComment.=
						" and $n Active Gift abandoned->[GiftEndDT=".$TempArr[0]["GiftEndDT"]."],".
						"[GiftExtraTr=".$TempArr[0]["GiftExtraTr"]." Byte],".
						"[GiftTrafficRate=".$TempArr[0]["GiftTrafficRate"]."],".
						"[GiftExtraTi=".$TempArr[0]["GiftExtraTi"]." Sec],".
						"[GiftTimeRate=".$TempArr[0]["GiftTimeRate"]."]";
					DBUpdate("Update Huser_gift Set GiftStatus='Abandoned' Where User_Gift_Id='$User_Gift_Id'");
				}
				
				$n=DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceIP_Id='$User_ServiceIP_Id' and SavingOffStatus='Pending'");
				if($n>0)
					$LogComment.=" $n related pending SavingOff canceld. ";				

				$n=DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceIP_Id='$User_ServiceIP_Id' and Status='Pending'");		
				if($n>0)
					$LogComment.=" $n related installment canceld. ";				
				
				//DSDebug(0,"LReseller_Id=$LReseller_Id");
				AddPaymentToUser($LReseller_Id,$User_Id,'CancelService',$ReturnPrice,'','Now()','','','');
				DBSelectAsString("Select ActivateUserNextServiceIP($User_Id)");
				//AddResellerTransaction($LReseller_Id,0,$User_Id,'CancelServiceIP',0);
				logdb("Edit","User",$User_Id,"ServiceIP",$LogComment);
				
				echo "OK~";
       break;
	case "AddService":
				DSDebug(1,"DSUser_ServiceIP_ListRender AddService ******************************************");
				global $CurrencySymbol;
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.Add");
				$Service_Id=Get_Input('POST','DB','Service_Id','INT',0,4294967295,0,0);
				if($Service_Id==0) ExitError('لطفا سرویس را انتخاب کنید');
				$StartDate=Get_Input('POST','DB','StartDate','DateOrBlank',0,0,0,0);
				$EndDate=Get_Input('POST','DB','EndDate','DateOrBlank',0,0,0,0);
				$Number=Get_Input('POST','DB','Number','INT',1,999999,0,0);
				
				$WithdrawSavingOff=Get_Input('POST','DB','WithdrawSavingOff','PRC',0,14,0,0);
				//check if Hreseller allowed add this Hservice
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($CurrentBase_Service_Id>0)
					$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
				else
					$ServiceBaseAccessFilter=1;
				$ServiceInfoArray=Array();
				$n=CopyTableToArray($ServiceInfoArray,
					"Select ServiceName,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash From Hservice ".
					"Where (Service_Id=$Service_Id)And(ISEnable='Yes')And(ResellerChoosable='Yes')And(ServiceType='IP')And(IPCount=$Number)And".
					"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
					"And(Service_Id in( ".
					"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug, Hservice_class s_ug ".
					"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
					"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
					"))And(Service_Id not in( ".
					"Select Service_Id From Huser_serviceip ".
					"Where ((ServiceStatus='Active')Or(ServiceStatus='Pending'))And(User_Id<>$User_Id) ".
					"))And($ServiceBaseAccessFilter)");

				if($n!=1) {
					ExitError('مجاز نیست');
					$Service_Name=DBSelectAsString("Select ServiceName From Hservice Where Service_Id=$Service_Id");
					logsecurity('HackTry',"Try Add Service id=[$Service_Id] SeviceName=[$Service_Name]");	
				}
				$ServiceName=$ServiceInfoArray[0]["ServiceName"];
				$MaxYearlyCountAllowed=$ServiceInfoArray[0]["MaxYearlyCount"];
				$MaxMonthlyCountAllowed=$ServiceInfoArray[0]["MaxMonthlyCount"];
				$MaxActiveCountAllowed=$ServiceInfoArray[0]["MaxActiveCount"];
				$ServicePricePerDay=$ServiceInfoArray[0]["Price"];
				$OffRate=$ServiceInfoArray[0]["OffRate"];
				$InstallmentNo=$ServiceInfoArray[0]["InstallmentNo"];
				$InstallmentPeriod=$ServiceInfoArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$ServiceInfoArray[0]["InstallmentFirstCash"];

				
				$Diff=DBSelectAsString("Select datediff('$EndDate', '$StartDate')");
				if($Diff<=0)
					ExitError("تاریخ پایان باید از تاریخ شروع بزرگتر باشد");
				$ServicePrice=$Diff*$ServicePricePerDay;				
				
				/*
				$n=DBSelectAsString("Select Count(*) From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($n>0) ExitError('Only one Active Service IP is Allowed');
				
				$IPOwnerUser_Id=DBSelectAsString("Select User_Id From Huser_serviceip where (Service_Id=$Service_Id)And(ServiceStatus in('Active','Pending'))And(EndDate>Date(now())) Limit 1");
				if($IPOwnerUser_Id!=''){
					if($IPOwnerUser_Id==$User_Id)
						ExitError("This IP Service Used By Yourself");
					else{	
						$UserName=DBSelectAsString("Select UserName From Huser Where User_Id=$IPOwnerUser_Id");
						ExitError("This IP Service Used By User[$UserName]");
					}	
				}
				*/
				$PayPlan=Get_Input('POST','DB','PayPlan','ARRAY',array("PrePaid","PostPaid"),0,0,0);
				exitifnotpermituser($User_Id,"Visp.User.PayPlan.".$PayPlan);
				
				if($InstallmentNo==0){
					$Price=$ServicePrice;
				}
				else if($InstallmentFirstCash=='Yes'){
					$Price=($ServicePrice/$InstallmentNo);
				}
				else $Price=0;
				$Price=$Price;
				
				$Off=$OffRate*DBSelectAsString("Select FindOffValueOfUser($User_Id)");
				if($Off>0){
					$OffFormula_Id=DBSelectAsString("select OffFormula_Id from Huser where User_Id='$User_Id'");
					$SavingOffPercent=DBSelectAsString("select SavingOffPercent from Hoffformula where OffFormula_Id='$OffFormula_Id'");
					$DirectOff=$Off*(100-$SavingOffPercent)/100;
				}
				else
					$DirectOff=0;
				
				if($WithdrawSavingOff>0){
					if($WithdrawSavingOff>$Price)
						return "Cannot withdraw ".number_format($WithdrawSavingOff, $PriceFloatDigit, '.', ',')." $CurrencySymbol amount of Saving Off whilst service price is only ".number_format($Price, $PriceFloatDigit, '.', ',')." $CurrencySymbol";
					
					$RemainedSavingOff=DBSelectAsString("SELECT sum(SavingOffAmount) FROM Huser_savingoff where User_Id=$User_Id and SavingOffStatus='Pending' and SavingOffExpDT>Now()")*1;
					if($WithdrawSavingOff>$RemainedSavingOff)
						Return "User has only $RemainedSavingOff $CurrencySymbol SavingOff. Cannot withdraw $WithdrawSavingOff $CurrencySymbol";
					
					$Price-=$WithdrawSavingOff;
				}
				else
					$WithdrawSavingOff=0;
				
				$DirectOffAmount=$Price*$DirectOff/100;
				
				$PriceWithOff=$Price-$DirectOffAmount;
				$PriceWithOff=number_format($PriceWithOff, $PriceFloatDigit, '.', '');
				
				$VAT=DBSelectAsString("Select Param5 From Hserver where PartName='Param'");
				if($VAT=='') $VAT=0;
				$PriceWithVAT=$PriceWithOff*(1+$VAT/100);
				$PriceWithVAT=number_format($PriceWithVAT, $PriceFloatDigit, '.', '');
				
				$UserCredit=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id Order by User_Payment_Id Desc");
				$RemainCredit=$PriceWithVAT-$UserCredit;
				if($RemainCredit<0) $RemainCredit=0;
				
				 //Check If User Have Enough credit
				$MaxPrepaidDebit=DBSelectAsString("Select MaxPrepaidDebit from Huser where User_Id='$User_Id'");
				if(($RemainCredit>$MaxPrepaidDebit)&&($LReseller_Id!=1)&&($PayPlan!='PostPaid'))
					ExitError(
						"User have not enough credit to add ".
						number_format($PriceWithVAT, $PriceFloatDigit, '.', ',').
						" $CurrencySymbol debit!!! (UserCredit is ".
						number_format($UserCredit, $PriceFloatDigit, '.', ',').
						". You can at most add ".
						number_format($MaxPrepaidDebit, $PriceFloatDigit, '.', ',').
						" debit for this user.)"
					);
				
				$res=AddServiceToUser($LReseller_Id,$User_Id,$Service_Id,$PayPlan,$StartDate,$EndDate,$WithdrawSavingOff);
				if($res!="")
					ExitError($res);
				DBSelectAsString("Select ActivateUserNextServiceIP($User_Id)");
				echo "OK~";
				
        break;
    case "SelectServiceIP":
				DSDebug(1,"DSUser_ServiceIP_ListRender-> SelectServiceIP *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.List");
				$StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
				$EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
				$Number=Get_Input('GET','DB','Number','INT',1,999999,0,0);
				
				
				

				$CanAddService=DBSelectAsString("Select CanAddService from Hstatus s Left join Huser u on(s.Status_Id=u.Status_Id) where User_Id=$User_Id");
				if($CanAddService=='No')
					$sql="Select 0 As Service_Id,'NOT allowed add service'As ServiceName";
				else{
					$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
					$CurrentService_Id=DBSelectAsString("Select Service_Id From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')");
					if($CurrentService_Id!='') $Filter="Service_Id=$CurrentService_Id";
					else $Filter=1;
					
					$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
					if($CurrentBase_Service_Id>0)
						$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
					else
						$ServiceBaseAccessFilter=1;
					
					$sql="Select 0 As Service_Id,'-- از لیست انتخاب کنید --' As ServiceName union ".
						"(Select Service_Id,ServiceName From Hservice ".
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='IP')And(IPCount=$Number)And($Filter)And ".
						"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
						"))And(Service_Id not in( ".
						"Select Service_Id From Huser_serviceip ".
						"Where ((ServiceStatus='Active')Or(ServiceStatus='Pending'))And(User_Id<>$User_Id) ".
						"))And($ServiceBaseAccessFilter)) order by ServiceName";
				}
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	case "GetServicePrice":
				DSDebug(1,"DSUser_ServiceIP_ListRender-> GetServicePrice *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.List");
				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				$StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
				$EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
				$Number=Get_Input('GET','DB','Number','INT',1,999999,0,0);
				$TempArray=Array();
				CopyTableToArray($TempArray,"Select Description,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash From Hservice where Service_Id=$Service_Id");
				$Description=$TempArray[0]["Description"];
				$ServicePricePerDay=$TempArray[0]["Price"];
				$OffRate=$TempArray[0]["OffRate"];
				$InstallmentNo=$TempArray[0]["InstallmentNo"];
				$InstallmentPeriod=$TempArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$TempArray[0]["InstallmentFirstCash"];

				//Calculate Price
				
				$Diff=DBSelectAsString("Select datediff('$EndDate', '$StartDate')");
				if($Diff<=0)
					ExitError("تاریخ پایان باید از تاریخ شروع بزرگتر باشد");
				$ServicePrice=$Diff*$ServicePricePerDay;
				
				$ServicePrice=$ServicePrice;
				if($InstallmentNo==0){
					$Price=$ServicePrice;
				}
				else if($InstallmentFirstCash=='Yes'){
					$Price=($ServicePrice/$InstallmentNo);
				}
				else $Price=0;
				
				$VAT=DBSelectAsString("Select Param5 From Hserver where PartName='Param'");
				if($VAT=='') $VAT=0;

				/*$Off=DBSelectAsString("Select FindOffValueOfUser($User_Id)");
				$PriceWithOff=$Price*(100-$OffRate*$Off)/100;
				$PriceWithVAT=$PriceWithOff*(1+$VAT/100);
				
				
				$UserCredit=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id Order by User_Payment_Id Desc");
				$RemainCredit=$PriceWithVAT-$UserCredit;
				//if($RemainCredit<0) $RemainCredit=0;
				
				$ServicePrice=number_format($ServicePrice, $PriceFloatDigit, '.', '');
				$Price=number_format($Price, $PriceFloatDigit, '.', '');
				if($Off>0)
					if($OffRate==0)
						$Off="Service has no Off";
					elseif($OffRate==1)
						$Off=number_format($Price-$PriceWithOff, $PriceFloatDigit, '.', ',')." (".number_format($Off, 2, '.', '')."%)";
					else
						$Off=number_format($Price-$PriceWithOff, $PriceFloatDigit, '.', ',')." (".number_format($Off, 2, '.', '')."%*".$OffRate.")";
				else
					$Off=number_format($Off, 2, '.', '')."%";
				
				if($VAT>0)
					$VAT=number_format($PriceWithVAT-$PriceWithOff, $PriceFloatDigit, '.', ',')." (".number_format($VAT, 2, '.', '')."%)";
				else
					$VAT=number_format($VAT, 2, '.', '')."%";
				
				$PriceWithOff=number_format($PriceWithOff, $PriceFloatDigit, '.', '');
				$PriceWithVAT=number_format($PriceWithVAT, $PriceFloatDigit, '.', '');
				*/
				$UserCredit=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id Order by User_Payment_Id Desc");
				$UserCredit=number_format($UserCredit, $PriceFloatDigit, '.', '');
				// $RemainCredit=number_format($RemainCredit, $PriceFloatDigit, '.', '');
				
				$RemainedSavingOff=DBSelectAsString("SELECT sum(SavingOffAmount-UsedAmount) FROM Huser_savingoff where User_Id=$User_Id and SavingOffStatus='Pending' and SavingOffExpDT>Now()")*1;
				
				$ServicePrice=number_format($ServicePrice, $PriceFloatDigit, '.', '');
				$Price=number_format($Price, $PriceFloatDigit, '.', '');
				
				$Off=$OffRate*DBSelectAsString("Select FindOffValueOfUser($User_Id)");
				if($Off>0){
					$OffFormula_Id=DBSelectAsString("select OffFormula_Id from Huser where User_Id='$User_Id'");
					$SavingOffPercent=DBSelectAsString("select SavingOffPercent from Hoffformula where OffFormula_Id='$OffFormula_Id'");
					$SavingOffExpirationDays=DBSelectAsString("select SavingOffExpirationDays from Hoffformula where OffFormula_Id='$OffFormula_Id'");
					$SavingOff=$Off*$SavingOffPercent/100;
					$DirectOff=$Off-$SavingOff;
				}
				else{
					$SavingOff=0;
					$DirectOff=0;
					$SavingOffExpirationDays=0;
				}
				$Err="";
				DSDebug(0,"RemainedSavingOff`ServicePrice`InstallmentNo`Price`OffRate`Off`SavingOff`DirectOff`SavingOffExpirationDays`VAT`UserCredit`Err`Description`");
				DSDebug(0,"$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description`");
				echo "$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description`";
					
		break;
	case "CheckIPRequest":
				DSDebug(1,"-> CheckIPRequest");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.List");
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				$StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
				$EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
				$n=DBSelectAsString("Select Count(*) From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')and((('$StartDate'>=StartDate)And('$StartDate'<EndDate))or(('$EndDate'>=StartDate)And('$EndDate'<EndDate)))");
				if($n>0)
					ExitError('!کاربر در این بازه زمانی دارای سرویس دیگری است');
				
				$Number=Get_Input('GET','DB','Number','INT',1,999999,0,0);
				if($StartDate>=$EndDate)
					ExitError('تاریخ پایان باید از تاریخ شروع بزرگتر باشد');
					
				$CurrentService_Id=DBSelectAsString("Select Service_Id From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')");
				if($CurrentService_Id!='') $Filter="Service_Id=$CurrentService_Id";
				else $Filter=1;
				$sql="Select Count(*) From Hservice ".
					"Where (ISEnable='Yes')And(ResellerChoosable='Yes')And(ServiceType='IP')And(IPCount=$Number)And($Filter)And ".
					"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
					"And(Service_Id in( ".
					"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
					"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
					"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
					"))And(Service_Id not in( ".
					"Select Service_Id From Huser_serviceip ".
					"Where ((ServiceStatus='Active')Or(ServiceStatus='Pending'))And(User_Id<>$User_Id) ".//sure IP not user by another
					"))";


//					"((('$StartDate'>=StartDate)And('$StartDate'<EndDate))or(('$EndDate'>=StartDate)And('$EndDate'<EndDate)))".
					
				$n=DBSelectAsString($sql);
				if($n<=0)
					ExitError('هیچ سرویس آی پی موجود نیست');
					
				echo "OK,";
		break;
	case "LoadIPRequest":
		DSDebug(1,"DSVispEditRender LoadCancelServiceForm ********************************************");
		$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
		$StartDate=DBSelectAsString("SELECT Max(If(ServiceStatus='Cancel',CancelDT,EndDate)) From Huser_serviceip where (User_Id='$User_Id') And (ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')");
		if($StartDate=='') $StartDate=DBSelectAsString("SELECT Date(Now())");
		$shStartDate=DBSelectAsString("SELECT shDateStr(DATE_ADD('$StartDate',INTERVAL 0 DAY))");
		$shEndDate=DBSelectAsString("SELECT shDateStr(DATE_ADD('$StartDate',INTERVAL 1 DAY))");
		$Number=DBSelectAsString("SELECT IPCount From Hservice s Left join Huser_serviceip u_si On s.Service_Id=u_si.Service_Id where (User_Id='$User_Id')And(ServiceType='IP')And(ServiceStatus='Active')");
		if($Number=='')
			$Number=1;
		echo "Ok`$shStartDate`$shEndDate`$Number";
	break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
