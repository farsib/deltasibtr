<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_ServiceBase_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","SelectServiceBase","GetServicePrice","AddService","EditService","LoadEditServiceForm","CancelService","LoadCancelServiceForm","CheckService","StartNextService"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_ServiceBase_ListRender->List ********************************************");

				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				//$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				//$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				//if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$SortStr='Order By User_ServiceBase_Id Desc';
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
					"Select User_ServiceBase_Id,r.ResellerName As Creator,ServiceStatus,ServiceName,{$DT}DateStr(StartDate) As StartDate,".
					"concat({$DT}DateStr(EndDate),' ',ActiveTime) As EndDate,ExtraDay,PayPlan, ".
					"Format(usb.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
					",usb.InstallmentNo,usb.InstallmentPeriod,usb.InstallmentFirstCash,Format(usb.SavingOffUsed,$PriceFloatDigit) as SavingOffUsed,DirectOff,VAT,Format(usb.PayPrice,$PriceFloatDigit) AS PayPrice, ".
					"{$DT}DateTimeStr(CancelDT) As CancelDT,".
					"Format(usb.ReturnPrice,$PriceFloatDigit) AS ReturnPrice,{$DT}DateTimeStr(CDT) As CDT,Off,SavingOff ".
					",Visibility ".
					"From Huser_servicebase usb Left Join Hservice s on usb.Service_Id=s.Service_Id ".
					"Left join Hreseller r on usb.Creator_Id=r.Reseller_Id ".
					"Where (User_Id=$User_Id)".(($LReseller_Id!=1)?"and(Visibility<>'VeryHidden')":"").$sqlfilter." $SortStr ",
					"User_ServiceBase_Id",
					"User_ServiceBase_Id,Creator,ServiceStatus,ServiceName,StartDate,EndDate,ExtraDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,SavingOffUsed,DirectOff,VAT,PayPrice,CancelDT,ReturnPrice,CDT,Off,SavingOff,Visibility",
					"","","color_rows");
       break;

    case "LoadCancelServiceForm":
				DSDebug(1,"DSVispEditRender LoadCancelServiceForm ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$User_ServiceBase_Id=Get_Input('GET','DB','User_ServiceBase_Id','INT',1,4294967295,0,0);
				$sql="SELECT PayPrice From Huser_servicebase where User_ServiceBase_Id='$User_ServiceBase_Id'";
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
				DSDebug(1,"DSUser_ServiceBase_ListRender CancelService ******************************************");
				$User_ServiceBase_Id=Get_Input('GET','DB','User_ServiceBase_Id','INT',1,4294967295,0,0);
				$ServiceInfoArray=Array();
				CopyTableToArray($ServiceInfoArray,"Select User_Id,u_sb.PayPrice,ServiceStatus,ServiceName,s.Service_Id ".
													"From Huser_servicebase u_sb left join Hservice s on u_sb.Service_Id=s.Service_Id ".
													"Where (User_ServiceBase_Id=$User_ServiceBase_Id)");
				$ServiceStatus=$ServiceInfoArray[0]["ServiceStatus"];
				$Service_Id=$ServiceInfoArray[0]["Service_Id"];
				if(($ServiceStatus==='Used')||($ServiceStatus==='Cancel')||($ServiceStatus==='Finished'))
					ExitError("سرویس هم اکنون $ServiceStatus!");
				$User_Id=$ServiceInfoArray[0]["User_Id"];
				if($User_Id!=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0)){
					logsecurity('HackTry',"Try CancelService with wrong User_Id, Report sent to administrator");
					ExitError('تلاش برای لغو سرویس با شناسه کاربری اشتباه، گزارش به مدیر اصلی ارسال شد');
				}
				
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.Cancel");
				$ReturnPrice=Get_Input('POST','DB','ReturnPrice','PRC',1,14,0,0);

				$PayPrice=$ServiceInfoArray[0]["PayPrice"];
				DSDebug(0,"ReturnPrice($ReturnPrice)>Price($PayPrice)");
				if($ReturnPrice>$PayPrice)
					ExitError("بازگشت وجه($ReturnPrice)>قیمت($PayPrice)");
				if($ReturnPrice<0)
					ExitError("بازگشت وجه باید بزرگتر یا مساوی 0 باشد");

				$ServiceName=DSescape($ServiceInfoArray[0]["ServiceName"]);
				$LogComment="Cancel Service Base. User_ServiceBase_Id=[$User_ServiceBase_Id] ServiceName=[$ServiceName]";
				
				DBUpdate("Update Huser_servicebase Set ServiceStatus='Cancel',ReturnPrice=$ReturnPrice,CancelDT=Now() Where User_ServiceBase_Id='$User_ServiceBase_Id'");
				
				$n=DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where User_ServiceBase_Id='$User_ServiceBase_Id' and GiftStatus='Pending'");
				if($n>0)
					$LogComment.=" And $n related pending gift canceled. ";
				
				$User_Gift_Id=DBSelectAsString("select User_Gift_Id from Huser_gift where User_ServiceBase_Id=$User_ServiceBase_Id and GiftStatus='Active'");
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
				
				$n=DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceBase_Id='$User_ServiceBase_Id' and SavingOffStatus='Pending'");
				if($n>0)
					$LogComment.=" $n related pending SavingOff canceld. ";				

				$n=DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceBase_Id='$User_ServiceBase_Id' and Status='Pending'");		
				if($n>0)
					$LogComment.=" $n related installment canceld. ";
				
				AddPaymentToUser($LReseller_Id,$User_Id,'CancelService',$ReturnPrice,'','Now()','','','');
				//AddResellerTransaction($LReseller_Id,0,$User_Id,'CancelService',0);
				logdb("Edit","User",$User_Id,"ServiceBase",$LogComment);
				DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)");
				//DBUpdate("Select UpdateAllUserParam($User_Id)");
				
				echo "OK~";
       break;
	case "AddService":
				DSDebug(1,"DSUser_ServiceBase_ListRender AddService ******************************************");
				global $CurrencySymbol;
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.Add");
				$Service_Id=Get_Input('POST','DB','Service_Id','INT',0,4294967295,0,0);
				if($Service_Id==0) ExitError('لطفا سرویس را انتخاب کنید');
				
				$WithdrawSavingOff=Get_Input('POST','DB','WithdrawSavingOff','PRC',0,14,0,0);
				//check if Hreseller allowed add this Hservice
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				$ServiceInfoArray=Array();
				$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($CurrentBase_Service_Id>0)
					$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
				else
					$ServiceBaseAccessFilter=1;
				$sql="Select ServiceName,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash From Hservice ".
					"Where (Service_Id=$Service_Id)And(ISEnable='Yes')And(ResellerChoosable='Yes')And(ServiceType='Base')".
					"And(Service_Id in( ".
					"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug, Hservice_class s_ug ".
					"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
					"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ".
						"(VispAccess='All' and Service_Id not in ( Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes')))or ".
						"(VispAccess<>'All' and Service_Id in ( Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes'))) ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
					"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
					"))And($ServiceBaseAccessFilter)";
				$n=CopyTableToArray($ServiceInfoArray,$sql);

				if($n!=1){
					ExitError('مجاز نیست');
					$Service_Name=DBSelectAsString("Select ServiceName From Hservice Where Service_Id=$Service_Id");
					logsecurity('HackTry',"Try Add Service id=[$Service_Id] SeviceName=[$Service_Name]");	
				}
				$ServiceName=$ServiceInfoArray[0]["ServiceName"];
				$MaxYearlyCountAllowed=$ServiceInfoArray[0]["MaxYearlyCount"];
				$MaxMonthlyCountAllowed=$ServiceInfoArray[0]["MaxMonthlyCount"];
				$MaxActiveCountAllowed=$ServiceInfoArray[0]["MaxActiveCount"];
				$ServicePrice=$ServiceInfoArray[0]["Price"];
				$OffRate=$ServiceInfoArray[0]["OffRate"];
				$InstallmentNo=$ServiceInfoArray[0]["InstallmentNo"];
				$InstallmentPeriod=$ServiceInfoArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$ServiceInfoArray[0]["InstallmentFirstCash"];
				
				//check if Service Count
				if($MaxYearlyCountAllowed>0){//0 means no limit
					$MaxYearlyCountUsed=DBSelectAsString("Select Count(*) From Huser_servicebase where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(CDT>shdateadd(Now(),-1,0,0))");
					if($MaxYearlyCountUsed>=$MaxYearlyCountAllowed)
						ExitError("تعداد خرید کاربر در سال برای این سرویس به سقف خود رسیده [Used=$MaxYearlyCountUsed]");
				}	
				if($MaxMonthlyCountAllowed>0){//0 means no limit
					$MaxMonthlyCountUsed=DBSelectAsString("Select Count(*) From Huser_servicebase where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(CDT>shdateadd(Now(),0,-1,0))");
					if($MaxMonthlyCountUsed>=$MaxMonthlyCountAllowed)
						ExitError("تعداد خرید کاربر در ماه برای این سرویس به سقف خود رسیده [Used=$MaxMonthlyCountUsed]");
				}	
				if($MaxActiveCountAllowed>0){//0 means no limit
					$MaxActiveCountUsed=DBSelectAsString("Select Count(*) From Huser_servicebase where (Service_Id=$Service_Id)And(ServiceStatus='Active')And((EndDate=0)Or(EndDate>Date(now())))");
					if($MaxActiveCountUsed>=$MaxActiveCountAllowed)
						ExitError("تعداد فعال از این سرویس به سقف خود رسیده [Used=$MaxActiveCountUsed]");
				}
				
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
						"کاربر اعتبار کافی برای افزودن ندارد ".
						number_format($PriceWithVAT, $PriceFloatDigit, '.', ',').
						" $CurrencySymbol بدهی!!! (اعتبار کاربر ".
						number_format($UserCredit, $PriceFloatDigit, '.', ',').
						". حداکثر می توانید اضافه کنید ".
						number_format($MaxPrepaidDebit, $PriceFloatDigit, '.', ',').
						" بدهی برای این کاربر.)"
					);
				
				$res=AddServiceToUser($LReseller_Id,$User_Id,$Service_Id,$PayPlan,'','',$WithdrawSavingOff);
				if($res!="")
					ExitError($res);
				// DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)");
				echo "OK~";
        break;
    case "SelectServiceBase":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> SelectServiceBase *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.List");
				
				$CanAddService=DBSelectAsString("Select CanAddService from Hstatus s Left join Huser u on(s.Status_Id=u.Status_Id) where User_Id=$User_Id");
				if($CanAddService=='No')
					$sql="Select 0 As Service_Id,'NOT allowed add service'As ServiceName";
				else{
					$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
					$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
					if($CurrentBase_Service_Id>0)
						$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
					else
						$ServiceBaseAccessFilter=1;
					$sql="Select 0 As Service_Id,'-- از لیست انتخاب کنید --' As ServiceName union ".
						"(Select Service_Id,ServiceName From Hservice ".
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='Base')And ".
						"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						
							"Select Service_Id from Hservice Where ".
							"(VispAccess='All' and Service_Id not in ( Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes')))or ".
							"(VispAccess<>'All' and Service_Id in ( Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes'))) ".
						
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes'))) ".
						")And($ServiceBaseAccessFilter) ) order by ServiceName";
				}
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	case "GetServicePrice":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> GetServicePrice *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.List");
				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				$TempArray=Array();
				CopyTableToArray($TempArray,"Select Description,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount From Hservice where Service_Id=$Service_Id");
				$Description=$TempArray[0]["Description"];
				$ServicePrice=$TempArray[0]["Price"];
				$OffRate=$TempArray[0]["OffRate"];
				$InstallmentNo=$TempArray[0]["InstallmentNo"];
				$InstallmentPeriod=$TempArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$TempArray[0]["InstallmentFirstCash"];

				$MaxYearlyCountAllowed=$TempArray[0]["MaxYearlyCount"];
				$MaxMonthlyCountAllowed=$TempArray[0]["MaxMonthlyCount"];
				$MaxActiveCountAllowed=$TempArray[0]["MaxActiveCount"];
				
				//check if Service Count
				$Err="";
				if($MaxYearlyCountAllowed>0){//0 means no limit
					$MaxYearlyCountUsed=DBSelectAsString("Select Count(*) From Huser_servicebase where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(CDT>shdateadd(Now(),-1,0,0))");
					if($MaxYearlyCountUsed>=$MaxYearlyCountAllowed)
						$Err="MaxYearlyCount of This Service is reached![Used=$MaxYearlyCountUsed]";
				}	
				if(($Err=="")&&($MaxMonthlyCountAllowed>0)){//0 means no limit
					$MaxMonthlyCountUsed=DBSelectAsString("Select Count(*) From Huser_servicebase where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(CDT>shdateadd(Now(),0,-1,0))");
					if($MaxMonthlyCountUsed>=$MaxMonthlyCountAllowed)
						$Err="MaxMonthlyCount of This Service is reached![Used=$MaxMonthlyCountUsed]";
				}	
				if(($Err=="")&&($MaxActiveCountAllowed>0)){//0 means no limit
					$MaxActiveCountUsed=DBSelectAsString("Select Count(*) From Huser_servicebase where (Service_Id=$Service_Id)And(ServiceStatus='Active')And((EndDate=0)Or(EndDate>Date(now())))");
					if($MaxActiveCountUsed>=$MaxActiveCountAllowed)
						$Err="MaxActiveCount of this Hservice is reached![Used=$MaxActiveCountUsed]";
				}
				
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
				
				// echo "$RemainedSavingOff`$InstallmentNo`$ServicePrice`$Price`$Off`$PriceWithOff`$VAT`$PriceWithVAT`$UserCredit`$RemainCredit`$Err`$Description";
				// DSDebug(0,"$RemainedSavingOff`$InstallmentNo`$ServicePrice`$Price`$Off`$PriceWithOff`$VAT`$PriceWithVAT`$UserCredit`$RemainCredit`$Err`$Description");	
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
				
				DSDebug(0,"RemainedSavingOff`ServicePrice`InstallmentNo`Price`OffRate`Off`SavingOff`DirectOff`SavingOffExpirationDays`VAT`UserCredit`Err`Description`");
				DSDebug(0,"$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description`");
				echo "$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description`";
				
		break;
    case "CheckService":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> CheckService *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.CheckService");
				if(DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)")<=0)
					ExitError("هیچ سرویسی فعال نشد");
				echo "OK~";
        break;
    case "StartNextService":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> CheckService *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.StartNextService");
				
				
				if(DBSelectAsString("Select UserStatus from Huser u left join Hstatus us on (u.Status_Id=us.Status_Id) Where User_Id='$User_Id'")!='Enable')
					ExitError("وضعیت کاربر فعال نیست");
				
				
				$IfExistNextService=DBSelectAsString("SELECT User_ServiceBase_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Pending') limit 1");
				if($IfExistNextService<=0){
					ExitError("کاربر هیچ سرویس در حال انتظاری ندارد");
				}
				$CurrentServiceBase_Id=DBSelectAsString("SELECT User_ServiceBase_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($CurrentServiceBase_Id<=0){
					ExitError("کاربر هیچ سرویس فعالی ندارد");
				}
				
				
				$ServiceName=DSescape(DBSelectAsString("Select ServiceName from Hservice s join Huser_servicebase u on s.Service_Id=u.Service_Id where User_ServiceBase_Id='$CurrentServiceBase_Id'"));
				$LogComment="StartNextService. Cancel Service with User_ServiceBase_Id=[$CurrentServiceBase_Id] ServiceName=[$ServiceName]";
				
				DBUpdate("Update Huser_servicebase Set ServiceStatus='Cancel',CancelDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id'");
				$n=DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id' and GiftStatus='Pending'");
				if($n>0)
					$LogComment.=" and $n related pending gift canceled. ";
				
				$User_Gift_Id=DBSelectAsString("select User_Gift_Id from Huser_gift where User_ServiceBase_Id=$CurrentServiceBase_Id and GiftStatus='Active'");
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
				
				$n=DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id' and SavingOffStatus='Pending'");
				if($n>0)
					$LogComment.=" $n related pending SavingOff canceld. ";				
				
				$n=DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceBase_Id='$CurrentServiceBase_Id' and Status='Pending'");		
				if($n>0)
					$LogComment.=" $n related installment canceld. ";
				
				logdb("Edit","User",$User_Id,"ServiceBase",$LogComment);
				DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)");
				//DBUpdate("Select UpdateAllUserParam($User_Id)");
				echo "OK~";
        break;
		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
