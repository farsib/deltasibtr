<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_ServiceExtraCredit_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","SelectServiceExtraCredit","GetServicePrice","AddService","CancelService","LoadCancelServiceForm","ResetToZero"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_ServiceExtraCredit_ListRender->List ********************************************");

				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				//$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				//$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				//if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$SortStr='Order By User_ServiceExtraCredit_Id Desc';
				function color_rows($row){
					$data = $row->get_value("ServiceStatus");
					if($data=='Applied')
						$style="color:blue;";
					else if($data=='Cancel')
						$style="color:red;";
					else if($data=='Reset')
						$style="color:green;";
					else if($data=='Pending')
						$style="";
					$data = $row->get_value("PayPlan");
					if(($data=='SendTo')||($data=='GetFrom'))
						$style.="font-weight:bold;";					
					
					if($row->get_value("Visibility")=='Hidden')
						$style.="text-decoration:underline;font-style: italic;opacity:0.5;";
					elseif($row->get_value("Visibility")=='VeryHidden')
						$style.="text-decoration:line-through;font-style: oblique;opacity:0.2";
					
					$row->set_row_style($style);
				}
				DSGridRender_Sql(100,
					"Select User_ServiceExtraCredit_Id,r.ResellerName As Creator,ServiceStatus,".
						"If(u_sec.Service_Id=0,if(ServiceStatus='Reset','- Reset ExtraCredit -','- Transfer Traffic -'),ServiceName)As ServiceName,".
						"{$DT}DateTimeStr(ApplyDT) As ApplyDT, ".
						"Round(s.ExtraTraffic/1048576) As ExtraTraffic,s.ExtraTime As ExtraTime,s.ExtraActiveDay As ExtraActiveDay,PayPlan,".
						"Format(u_sec.ServicePrice,$PriceFloatDigit) AS ServicePrice ".
						",u_sec.InstallmentNo,u_sec.InstallmentPeriod,u_sec.InstallmentFirstCash,Format(u_sec.SavingOffUsed,$PriceFloatDigit) as SavingOffUsed,DirectOff,VAT,Format(u_sec.PayPrice,$PriceFloatDigit) AS PayPrice, ".
						"Format(ReturnPrice,$PriceFloatDigit) AS ReturnPrice,{$DT}DateTimeStr(CancelDT) As CancelDT,u_sec.User_ServiceBase_Id,".
						"{$DT}DateTimeStr(CDT) As CDT,Off,SavingOff,Username as TransferUsername,round(TransferTraffic/(1024*1024)) as TransferTraffic,".
						"ByteToR(ResetTraffic) as ResetTraffic,SecondToR(ResetTime) as ResetTime ".
						",Visibility ".
						"From  Huser_serviceextracredit u_sec ".
						"Left join Huser u on u_sec.TransferUser_Id=u.User_Id ".
						"Left join Hservice s on s.Service_Id=u_sec.Service_Id ".
						"Left join Hreseller r on u_sec.Creator_Id=r.Reseller_Id ".
						"Where (u_sec.User_Id=$User_Id)".(($LReseller_Id!=1)?"and(Visibility<>'VeryHidden')":"").$sqlfilter." $SortStr "
					,
					"User_ServiceExtraCredit_Id",	"User_ServiceExtraCredit_Id,Creator,ServiceStatus,ServiceName,ApplyDT,ExtraTraffic,ExtraTime,ExtraActiveDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,SavingOffUsed,DirectOff,VAT,PayPrice,ReturnPrice,CancelDT,User_ServiceBase_Id,CDT,Off,SavingOff,TransferUsername,TransferTraffic,ResetTraffic,ResetTime,Visibility",
					"","","color_rows");
       break;
	case "AddService":
				DSDebug(1,"DSUser_ServiceExtraCredit_ListRender AddService ******************************************");
				global $CurrencySymbol;
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.Add");
				$Service_Id=Get_Input('POST','DB','Service_Id','INT',0,4294967295,0,0);
				if($Service_Id==0) ExitError('لطفا سرویس انتخاب کنید');
				
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
					"Select ServiceName,MaxYearlyCount,MaxMonthlyCount,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash From Hservice ".
					"Where (Service_Id=$Service_Id)And(ISEnable='Yes')And(ResellerChoosable='Yes')And(ServiceType='ExtraCredit')".
					"And(Service_Id in( ".
					"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug, Hservice_class s_ug ".
					"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
					"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
					"))And($ServiceBaseAccessFilter)");

				if($n!=1) {
					ExitError('مجاز نیست');
					$Service_Name=DBSelectAsString("Select ServiceName From Hservice Where Service_Id=$Service_Id");
					logsecurity('HackTry',"Try Add Service id=[$Service_Id] SeviceName=[$Service_Name]");	
				}
				$ServiceName=$ServiceInfoArray[0]["ServiceName"];
				$MaxYearlyCountAllowed=$ServiceInfoArray[0]["MaxYearlyCount"];
				$MaxMonthlyCountAllowed=$ServiceInfoArray[0]["MaxMonthlyCount"];
				$ServicePrice=$ServiceInfoArray[0]["Price"];
				$OffRate=$ServiceInfoArray[0]["OffRate"];
				$InstallmentNo=$ServiceInfoArray[0]["InstallmentNo"];
				$InstallmentPeriod=$ServiceInfoArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$ServiceInfoArray[0]["InstallmentFirstCash"];
				
				//check if Service Count
				if($MaxYearlyCountAllowed>0){//0 means no limit
					$MaxYearlyCountUsed=DBSelectAsString("Select Count(*) From Huser_serviceextracredit where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(ServiceStatus<>'Cancel')And(CDT>shdateadd(Now(),-1,0,0))");
					if($MaxYearlyCountUsed>=$MaxYearlyCountAllowed)
						ExitError("تعداد خرید کاربر در سال به سقف خود رسیده است</br>[Used=$MaxYearlyCountUsed]");
				}	
				if($MaxMonthlyCountAllowed>0){//0 means no limit
					$MaxMonthlyCountUsed=DBSelectAsString("Select Count(*) From Huser_serviceextracredit where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(ServiceStatus<>'Cancel')And(CDT>shdateadd(Now(),0,-1,0))");
					if($MaxMonthlyCountUsed>=$MaxMonthlyCountAllowed)
						ExitError("تعداد خرید کاربر در ماه به سقف خود رسیده است</br>[Used=$MaxMonthlyCountUsed]");
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
						"User have not enough credit to add ".
						number_format($PriceWithVAT, $PriceFloatDigit, '.', ',').
						" $CurrencySymbol debit!!! (UserCredit is ".
						number_format($UserCredit, $PriceFloatDigit, '.', ',').
						". You can at most add ".
						number_format($MaxPrepaidDebit, $PriceFloatDigit, '.', ',').
						" debit for this user.)"
					);
				
				$res=AddServiceToUser($LReseller_Id,$User_Id,$Service_Id,$PayPlan,'','',$WithdrawSavingOff);
				if($res!="")
					ExitError($res);
				echo "OK~";

        break;
    case "LoadCancelServiceForm":
				DSDebug(1,"DSUser_ServiceExtraCredit_ListRender LoadCancelServiceForm ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$User_ServiceExtraCredit_Id=Get_Input('GET','DB','User_ServiceExtraCredit_Id','INT',1,4294967295,0,0);
				$sql="SELECT PayPrice,PayPrice As ReturnPrice From Huser_serviceextracredit where User_ServiceExtraCredit_Id='$User_ServiceExtraCredit_Id'";
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
				DSDebug(1,"DSUser_ServiceExtraCredit_ListRender CancelService ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.Cancel");
				$User_ServiceExtraCredit_Id=Get_Input('GET','DB','User_ServiceExtraCredit_Id','INT',0,4294967295,0,0);
				$ServiceInfoArray=Array();
				CopyTableToArray($ServiceInfoArray,"Select User_Id,u_sec.PayPrice,ServiceStatus,ServiceName,s.Service_Id,".
						"TransferUser_Id,ExtraTraffic,ExtraTime,ExtraActiveDay ".
						"From Huser_serviceextracredit u_sec left join Hservice s on u_sec.Service_Id=s.Service_Id ".
						"Where (User_ServiceExtraCredit_Id=$User_ServiceExtraCredit_Id)");
				
				$ServiceStatus=$ServiceInfoArray[0]["ServiceStatus"];
				$Service_Id=$ServiceInfoArray[0]["Service_Id"];
				$PayPrice=$ServiceInfoArray[0]["PayPrice"];
				$TransferUser_Id=$ServiceInfoArray[0]["TransferUser_Id"];
				$ExtraTraffic=$ServiceInfoArray[0]["ExtraTraffic"];
				$ExtraTime=$ServiceInfoArray[0]["ExtraTime"];
				$ExtraActiveDay=$ServiceInfoArray[0]["ExtraActiveDay"];
				if($ServiceStatus=='Cancel')
					ExitError("سرویس پیش از این لغو شده است");
				if($ServiceStatus=='Reset')
					ExitError("شما نمیتوانید رکوردهای ریست شده را لغو کنید");
				if($TransferUser_Id>0) 
					ExitError('شما نمیتوانید اعتبار انتقالی را لغو کنید');
				
				$User_Id=$ServiceInfoArray[0]["User_Id"];
				if($User_Id!=$ServiceInfoArray[0]["User_Id"]){
					logsecurity('HackTry',"Try CancelService with wrong User_Id, Report sent to administrator");
					ExitError('برای لغو سرویس با شناسه کاربر اشتباه تلاش کردید،گزارش به مدیر ارسال شد');
				}
				if($ServiceStatus=='Applied'){
					if($ExtraTraffic>0){
						$RemainExtraTraffic=DBSelectAsString("Select ETrA-ETrU From Tuser_usage where User_Id=$User_Id");
						DSDebug(1,"RemainExtraTraffic=$RemainExtraTraffic ExtraTraffic=$ExtraTraffic");
						if($RemainExtraTraffic<$ExtraTraffic)
							ExitError('کاربر اضافه ترافیک باقی مانده کافی ندارد');
						DBUpdate("Update Huser Set ETrA=ETrA-$ExtraTraffic Where User_Id=$User_Id");
					}	
					if($ExtraTime>0){
						$RemainExtraTime=DBSelectAsString("Select ETiA-ETiU From Tuser_usage where User_Id=$User_Id");
						if($ExtraTime>$RemainExtraTime)
							ExitError('کاربر اضافه زمان باقی مانده کافی ندارد');
						DBUpdate("Update Huser Set ETiA=ETiA-$ExtraTime Where User_Id=$User_Id");
					}	
					if($ExtraActiveDay>0){
						$User_ServiceBase_Id=DBSelectAsString("SELECT User_ServiceBase_Id From Huser Where (User_Id=$User_Id)");
						If ($User_ServiceBase_Id>0){
							DBUpdate("Update Huser_servicebase u_sb Left join Hservice s on u_sb.Service_Id=s.Service_Id ".
									"Set EndDate=shdateadd(StartDate,ActiveYear,ActiveMonth,ActiveDay+ExtraDay-$ExtraActiveDay),ExtraDay=ExtraDay-$ExtraActiveDay ".
									"Where User_ServiceBase_Id=$User_ServiceBase_Id");
						}	
					}	
				}
				$ReturnPrice=$PayPrice;				
				
				
				$ServiceName=DSescape($ServiceInfoArray[0]["ServiceName"]);
				$LogComment="Cancel Service Extra. User_ServiceExtraCredit_Id=[$User_ServiceExtraCredit_Id] ServiceName=[$ServiceName]";
				
				DBUpdate("Update Huser_serviceextracredit Set ServiceStatus='Cancel',ReturnPrice=$ReturnPrice,CancelDT=Now() Where User_ServiceExtraCredit_Id='$User_ServiceExtraCredit_Id'");
				$n=DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where User_ServiceExtraCredit_Id='$User_ServiceExtraCredit_Id' and GiftStatus='Pending'");
				if($n>0)
					$LogComment.=" And $n related pending gift canceled. ";
				
				$User_Gift_Id=DBSelectAsString("select User_Gift_Id from Huser_gift where User_ServiceExtraCredit_Id='$User_ServiceExtraCredit_Id' and GiftStatus='Active'");
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
				
				$n=DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceExtraCredit_Id='$User_ServiceExtraCredit_Id' and SavingOffStatus='Pending'");
				if($n>0)
					$LogComment.=" $n related pending canceld. ";				
				
				$n=DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceExtraCredit_Id='$User_ServiceExtraCredit_Id' and Status='Pending'");		
				if($n>0)
					$LogComment.=" $n related installment canceld. ";
				
				AddPaymentToUser($LReseller_Id,$User_Id,'CancelService',$ReturnPrice,'','Now()','','','');
				logdb("Edit","User",$User_Id,"ServiceExtraCredit",$LogComment);
				
				echo "OK~";
       break;
		
    case "SelectServiceExtraCredit":
				DSDebug(1,"DSUser_ServiceExtraCredit_ListRender-> SelectServiceExtraCredit *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.List");
				
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
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='ExtraCredit')And((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
						"))And($ServiceBaseAccessFilter)) order by ServiceName";
				}
				
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
		
	case "GetServicePrice":
				DSDebug(1,"DSUser_ServiceExtraCredit_ListRender-> GetServicePrice *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.List");
				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				$TempArray=Array();
				CopyTableToArray($TempArray,"Select Description,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,MaxYearlyCount,MaxMonthlyCount From Hservice where Service_Id=$Service_Id");
				$Description=$TempArray[0]["Description"];
				$ServicePrice=$TempArray[0]["Price"];
				$OffRate=$TempArray[0]["OffRate"];
				$InstallmentNo=$TempArray[0]["InstallmentNo"];
				$InstallmentPeriod=$TempArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$TempArray[0]["InstallmentFirstCash"];
				
				$MaxYearlyCountAllowed=$TempArray[0]["MaxYearlyCount"];
				$MaxMonthlyCountAllowed=$TempArray[0]["MaxMonthlyCount"];
				
				//check if Service Count
				$Err="";
				if($MaxYearlyCountAllowed>0){//0 means no limit
					$MaxYearlyCountUsed=DBSelectAsString("Select Count(*) From Huser_serviceextracredit where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(CDT>shdateadd(Now(),-1,0,0))");
					if($MaxYearlyCountUsed>=$MaxYearlyCountAllowed)
						$Err="MaxYearlyCount of This Service is reached![Used=$MaxYearlyCountUsed]";
				}

				if(($Err=="")&&($MaxMonthlyCountAllowed>0)){//0 means no limit
					$MaxMonthlyCountUsed=DBSelectAsString("Select Count(*) From Huser_serviceextracredit where (User_Id=$User_Id)And(Service_Id=$Service_Id)And(CDT>shdateadd(Now(),0,-1,0))");
					// DSDebug(0,"Err='$Err'\nMaxMonthlyCountUsed='$MaxMonthlyCountUsed'");
					if($MaxMonthlyCountUsed>=$MaxMonthlyCountAllowed)
						$Err="MaxMonthlyCount of This Service is reached![Used=$MaxMonthlyCountUsed]";
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
	case "ResetToZero":
				DSDebug(1,"DSUser_ServiceExtraCredit_ListRender-> ResetToZero *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.ResetToZero");
						
						
				$RemainExtraTraffic=DBSelectAsString("Select ETrA-ETrU From Tuser_usage where User_Id=$User_Id");
				$RemainExtraTime=DBSelectAsString("Select ETiA-ETiU From Tuser_usage where User_Id=$User_Id");
				DSDebug(1,"RemainExtraTraffic=$RemainExtraTraffic	RemainExtraTime=$RemainExtraTime");
				if(($RemainExtraTraffic>0)||($RemainExtraTime>0)){
					DBUpdate("Insert Huser_serviceextracredit Set User_Id=$User_Id,Creator_Id=$LReseller_Id,CDT=Now(),ApplyDT=Now(),ServiceStatus='Reset',ResetTraffic=$RemainExtraTraffic,ResetTime=$RemainExtraTime");
					DBUpdate("Update Huser Set ETrA=ETrA-$RemainExtraTraffic,ETiA=ETiA-$RemainExtraTime Where User_Id=$User_Id");
					
					$ar=DBUpdate("replace Tonline_dcqueue(Online_RadiusUser_Id,CDT) Select Online_RadiusUser_Id,Now() From Tonline_radiususer Where ServiceInfo_Id=1 and User_Id=$User_Id");
					
					logdb("Edit","User",$User_Id,"ServiceExtraCredit","Reset Remained Traffic($RemainExtraTraffic byte) and Time($RemainExtraTime sec) to zero");					
					echo "OK~";
				}
				else
					echo "NoNeedChange~";
				
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
