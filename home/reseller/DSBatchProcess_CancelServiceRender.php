<?php
try {
require_once("../../lib/DSInitialReseller.php");
require_once("../../lib/DSRenderLib.php");
DSDebug(1,"DSBatchProcess_CancelServiceRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

if($LReseller_Id!=1) ExitError('فقط ادمین می تواند عملیات گروهی را انجام دهد');

set_time_limit(300);
// if($DebugLevel>0)
	// ExitError("You cannot use debug during BatchProcess. First, run ds_nodebug from linux shell");


$act=Get_Input('GET','DB','act','ARRAY',array(
	"InititalizeBatchProcess",
	"StartBatchProcess",
	"PauseInProgress",
	"CancelInProgress",
	"GetProgress",
	"SaveLog",
	"GetUserCount"
),0,0,0);

	
	
switch ($act) {
	case "InititalizeBatchProcess":
				DSDebug(0,"DSBatchProcess_CancelServiceRender->InititalizeBatchProcess********************************************");
				
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="Pending")
					ExitError("عملیات گروهی شما منقضی شده است");
				
				$sql="SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')";
				$res=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				if($res!='Yes')
					ExitError("آی پی شا معتبر نیست،ابتدا آی پی خود را در 'آی پی که مسدود نمی شود وارد کنید");
				
				
				$Action=Get_Input('POST','DB','Action','ARRAY',array('StartNextService','CancelActiveService','CancelPendingService','CancelAllService'),0,0,0);
				
				$ReturnValueType=Get_Input('POST','DB','ReturnValueType','ARRAY',array('PayPricePercent','PayPriceFix'),0,0,0);
				
				$ReturnValue=Get_Input('POST','DB','ReturnValue','PRC',1,14,0,0);
				if($ReturnValueType=='PayPricePercent'){
					$ReturnFix=0;
					if($ReturnValue>100)
						$ReturnValue=100;
					elseif($ReturnValue<0)
						$ReturnValue=0;
					$ReturnFactor=$ReturnValue/100;
				}
				else{
					$ReturnFix=$ReturnValue;
					$ReturnFactor=0;
				}
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				
				$sql="UPDATE Hbatchprocess set Option1='$Action',Option2='$ReturnFix',Option3='$ReturnFactor',BatchState='InProgress',".
					"BatchComment='$Action for $UserCount number of users',StartDT=now() ".
					"where BatchProcess_Id=$BatchProcess_Id and BatchState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				if($res!=1)
					ExitError('درخواست غیرمجاز تشخیص داده شده');
				sleep(1);
				echo "OK~";
	break;
	case "StartBatchProcess":
								
				DSDebug(0,"DSBatchProcess_CancelServiceRender->StartBatchProcess********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);

				
				// if(DBSelectAsString("select SessionID from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!=$SessionId)
					// ExitError("Your batch process expired due to relogin with the same browser again...");
				
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="InProgress")
					ExitError("عملیات گروهی شما منقضی شده است");
				
				
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Pending' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Paused'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				DSDebug(0,"DSBatchProcess_CancelServiceRender->CancelingService********************************************");
				

				
				$ReturnFix=DBSelectAsString("select Option2 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				$ReturnFactor=DBSelectAsString("select Option3 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				$From_Index=DBSelectAsString("SELECT MIN(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'");
				
				$LowerBound=DBSelectAsString("SELECT From_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				$UpperLimit=DBSelectAsString("SELECT To_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				DSDebug(0,"*************************************************************************\n***********************************************************");
				DSDebug(0,"LowerBound=$LowerBound\nUpperLimit=$UpperLimit\nUserCount=$UserCount");
				if(($UpperLimit-$LowerBound+1)!=$UserCount)
					ExitError("Checksum خطای");//Due to table lock and then insert record in prepare list all rhe $UserCount records is contiguous
				
				$Action=DBSelectAsString("select Option1 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				if($Action=='StartNextService'){
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index=$User_Index");
							
							
							$IfExistNextService=DBSelectAsString("SELECT User_ServiceBase_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Pending') order by User_ServiceBase_Id asc limit 1");
							if($IfExistNextService>0){
								$CurrentServiceBase_Id=DBSelectAsString("SELECT User_ServiceBase_Id From Huser Where User_Id=$User_Id");
								if($CurrentServiceBase_Id>0){
									DBUpdate("Update Huser_servicebase Set ServiceStatus='Cancel',CancelDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id' ");
									
									DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where user_servicebase_Id=$CurrentServiceBase_Id and GiftStatus='Pending'");
									
									DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id' and SavingOffStatus='Pending'");
									
									DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceBase_Id='$CurrentServiceBase_Id' and Status='Pending'");
									
									logdb("Edit","User",$User_Id,"ServiceBase","BatchProcess[StartNextService User_ServiceBase_Id=$IfExistNextService. Canceled User_ServiceBase_Id=$CurrentServiceBase_Id]");
									if(DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)")==1){
										$BatchItemComment="Next service started.";	
										usleep(120000);
									}
									else{
										$BatchItemComment="Current service canceled. Next service cannot started.";
										usleep(90000);
									}
								}
								else{
									$BatchItemComment="User have not any Active Service";
									usleep(60000);
								}
							}
							else{
								$BatchItemComment="User have not any pending Service";
								usleep(30000);
							}
							
							
							
							$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemComment='$BatchItemComment',BatchItemDT=NOW() where User_Index=$User_Index";
							$res=DBUpdate($sql);
							DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
							
							DBUpdate("Update Hbatchprocess set CompletedCount=$i where BatchProcess_Id=$BatchProcess_Id");
							DBUpdate("Update Tonline_webreseller Set LastSeenDT=Now() Where SessionID='$SessionId'");
						}
						else if($res==0){
							sleep(2);
							echo $i-1;
							exit();
						}
						else{
							logsecurity("Error in BatchProcess structure. $res row updated for one request");
							ExitError("خطا در ساختار عملیات گروهی");
						}
					}
				}
				elseif($Action=='CancelActiveService'){
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index=$User_Index");
							$CurrentServiceBase_Id=DBSelectAsString("SELECT User_ServiceBase_Id From Huser Where User_Id=$User_Id");
							if($CurrentServiceBase_Id>0){
								$PayPrice=DBSelectAsString("select PayPrice from Huser_servicebase where User_ServiceBase_Id='$CurrentServiceBase_Id'");
								$ReturnPrice=min($PayPrice,$ReturnFix)+$PayPrice*$ReturnFactor;
								
								DBUpdate("Update Huser_servicebase Set ServiceStatus='Cancel',ReturnPrice=$ReturnPrice,CancelDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id'");
								
								DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where user_servicebase_Id=$CurrentServiceBase_Id and GiftStatus='Pending'");
								
								DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id' and SavingOffStatus='Pending'");
								
								DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceBase_Id='$CurrentServiceBase_Id' and Status='Pending'");
								
								AddPaymentToUser($LReseller_Id,$User_Id,'CancelService',$ReturnPrice,'','','','','By BatchProcess');
								// $sql= "insert Huser_payment(User_Id,Creator_Id,User_PaymentCDT,PaymentType,Price,PayBalance,Comment) ".
									// "Select $User_Id,'$LReseller_Id',Now(),'CancelService','$ReturnPrice',PayBalance+($ReturnPrice),'By BatchProcess' From ".
									// "Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc Limit 1";
								// DBInsert($sql);
								
								logdb("Edit","User",$User_Id,"ServiceBase","BatchProcess[Cancel Service User_ServiceBase_Id=$CurrentServiceBase_Id]");
								if(DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)")==1){
									$BatchItemComment="Active service canceled. Next service activated.";
									usleep(120000);
								}
								else{
									$BatchItemComment="Active service canceled.";
									usleep(60000);
								}
							}
							else{
								$BatchItemComment="User have not any Active Service";
								usleep(30000);
							}
							
							$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemComment='$BatchItemComment',BatchItemDT=NOW() where User_Index=$User_Index";
							$res=DBUpdate($sql);
							DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
							
							DBUpdate("Update Hbatchprocess set CompletedCount=$i where BatchProcess_Id=$BatchProcess_Id");
							
							DBUpdate("Update Tonline_webreseller Set LastSeenDT=Now() Where SessionID='$SessionId'");
						}
						else if($res==0){
							sleep(2);
							echo $i-1;
							exit();
						}
						else{
							logsecurity("Error in BatchProcess structure. $res row updated for one request");
							ExitError("خطا در ساختار عملیات گروهی");
						}
					}
				}
				elseif(($Action=='CancelPendingService')||($Action=="CancelAllService")){
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index=$User_Index");
							$ServiceInfoArray=Array();
							$n=CopyTableToArray($ServiceInfoArray,"Select User_ServiceBase_Id,PayPrice,ServiceStatus From Huser_servicebase ".
								"Where User_Id=$User_Id and (ServiceStatus='Active' or ServiceStatus='Pending') ".
								"order by ServiceStatus desc,User_ServiceBase_Id asc");
							$Activecount=0;
							for($j=0;$j<$n;++$j){
								$ServiceStatus=$ServiceInfoArray[$j]["ServiceStatus"];
								if($ServiceStatus=='Active'){
									if($Action=='CancelPendingService')
										continue;
									else
										$Activecount++;
								}
									
								$CurrentServiceBase_Id=$ServiceInfoArray[$j]["User_ServiceBase_Id"];
								$PayPrice=DBSelectAsString("select PayPrice from Huser_servicebase where User_ServiceBase_Id='$CurrentServiceBase_Id'");
								$ReturnPrice=min($PayPrice,$ReturnFix)+$PayPrice*$ReturnFactor;

								DBUpdate("Update Huser_servicebase Set ServiceStatus='Cancel',ReturnPrice=$ReturnPrice,CancelDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id'");
								
								DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where user_servicebase_Id=$CurrentServiceBase_Id And GiftStatus='Pending'");
								
								DBUpdate("Update Huser_savingoff Set SavingOffStatus='Cancel',SavingOffUseDT=Now() Where User_ServiceBase_Id='$CurrentServiceBase_Id' and SavingOffStatus='Pending'");
								
								DBUpdate("Update Huser_installment Set Status='Cancel' Where User_ServiceBase_Id='$CurrentServiceBase_Id' and Status='Pending'");

								
								AddPaymentToUser($LReseller_Id,$User_Id,'CancelService',$ReturnPrice,'','','','','By BatchProcess');
								// $sql= "insert Huser_payment(User_Id,Creator_Id,User_PaymentCDT,PaymentType,Price,PayBalance,Comment) ".
									// "Select $User_Id,'$LReseller_Id',Now(),'CancelService','$ReturnPrice',PayBalance+($ReturnPrice),'By BatchProcess' From ".
									// "Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc Limit 1";
								// DBInsert($sql);
							
								logdb("Edit","User",$User_Id,"ServiceBase","BatchProcess[Cancel `$ServiceStatus` ServiceBase User_ServiceBase_Id=$CurrentServiceBase_Id]");
								usleep(60000);
							}
							usleep(40000);
							if($Activecount>0){
								DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)");
								usleep(100000);
							}
							
							if($n>0){
								if($Activecount>0)
									$BatchItemComment.="$n service canceled.($Activecount active service,".($n-$Activecount)." pending service)";
								else
									$BatchItemComment.="$n pending service canceled.";
							}
							else
								$BatchItemComment="User has no service to cancel.";
							
							$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemComment='$BatchItemComment',BatchItemDT=NOW() where User_Index=$User_Index";
							$res=DBUpdate($sql);
							DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
							
							DBUpdate("Update Hbatchprocess set CompletedCount=$i where BatchProcess_Id=$BatchProcess_Id");
							
							DBUpdate("Update Tonline_webreseller Set LastSeenDT=Now() Where SessionID='$SessionId'");
						}
						else if($res==0){
							sleep(2);
							echo $i-1;
							exit();
						}
						else{
							logsecurity("Error in BatchProcess structure. $res row updated for one request");
							ExitError("خطا در ساختار عملیات گروهی");
						}
					}
				}
				else
					ExitError("عمل زیر نامعتبر تشخیص داده شد</br>(`$Action`)");
				
				DSDebug(0,"DSBatchProcess_CancelServiceRender->FinalizeBatchProcess********************************************");
				$sql="UPDATE Hbatchprocess set BatchState='Done',EndDT=now() where BatchProcess_Id=$BatchProcess_Id";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");

				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Done'";
				$res1=DBSelectAsString($sql);
				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Fail'";
				$res2=DBSelectAsString($sql);
				$ActionFa=str_replace(array("StartNextService","CancelActiveService","CancelPendingService","CancelAllService"),array("شروع سرویس بعدی","لغو سرویس فعال","لغو سرویس رزرو","لغو سرویس فعال و رزرو"),$Action);
				
				echo "OK~".(($res1>0)?("عملیات $ActionFa برای $res1 کاربر با موفقیت پایان یافت"):("$ActionFa برای هیچ کاربری انجام نشد")).(($res2>0)?(" و هیج عملیاتی برای $res2 کاربر انجام نشد(گزارش را بررسی کنید)"):(""));				
				
	break;	
    case "PauseInProgress":
				DSDebug(0,"DSBatchProcess_CancelServiceRender->PauseInProgress********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);			
		
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Paused' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				echo "OK~$res~";
	break;	
    case "CancelInProgress":
				DSDebug(0,"DSBatchProcess_CancelServiceRender->CancelInProgress********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);			
				$sql="UPDATE Hbatchprocess_users set BatchItemState='CanceledInProgress' where BatchProcess_Id=$BatchProcess_Id and (BatchItemState='Pending' or BatchItemState='Paused')";
				$res1=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res1");
				$sql="UPDATE Hbatchprocess set BatchState='CanceledInProgress',BatchComment=concat(BatchComment,' $res1 Items Canceled') where BatchProcess_Id=$BatchProcess_Id";
				$res2=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res2");			
				echo "OK~$res1";
	break;
	case "GetProgress":
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				$sql="select CompletedCount from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id";
				$res=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				$sql="update Tonline_web_ipblock set ".
					"LastDayRequest=LastDayRequest-1,".
					"LastHourRequest=LastHourRequest-1,".
					"LastMinuteRequest=LastMinuteRequest-1,".
					"LastSecondRequest=LastSecondRequest-1 ".
					"where ClientIP=INET_ATON('$LClientIP')";
				DBUpdate($sql);
				echo "$res";
	break;
	case "SaveLog":
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);			
				$sql="select User_Id,SHDATETIMESTR(BatchItemDT) as BatchItemDT,BatchItemState,BatchItemComment from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id";
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; charset=utf-8; filename="BatchProcessLog.csv";');
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				$f = fopen('php://output', 'w');
					foreach ($data as $key=>$Value)
						$Arr[$key]=$key;		
					fputcsv($f, $Arr, ',');
				while($data){
					foreach ($data as $key=>$Value)
						$Arr[$key]=mysqli_real_escape_string($mysqli,$data[$key]);		
					$data =  $conn->sql->get_next($res);
					fputcsv($f, $Arr, ',');
				}
	break;
	case "GetUserCount":
				DSDebug(0,"DSBatchProcess_CancelServiceRender-> GetUserCount *****************");
				$Action=Get_Input('GET','DB','Action','ARRAY',array('StartNextService','CancelActiveService','CancelPendingService','CancelAllService'),0,0,0);
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				if($Action=="StartNextService"){
					$Out1=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where u.User_ServiceBase_Id<>0");
					$Out2=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where u.User_Id in (select User_Id from Huser_servicebase where ServiceStatus='Pending')");
				}
				if($Action=="CancelActiveService"){
					$Out1=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where u.User_ServiceBase_Id<>0");
					$Out2="";//DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where u.User_Id in (select User_Id from Huser_servicebase where ServiceStatus='Pending')");
				}
				elseif($Action=="CancelPendingService"){
					$Out1=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where u.User_Id in (select User_Id from Huser_servicebase where ServiceStatus='Pending')");
					$Out2=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser_servicebase u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where ServiceStatus='Pending'");
				}
				elseif($Action=="CancelAllService"){
					$Out1=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where u.User_ServiceBase_Id<>0");
					$Out2=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser_servicebase u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id where ServiceStatus='Pending'");
				}
				$Out3=DBSelectAsString("Select count(1) from Hbatchprocess_users b join Huser u on b.BatchProcess_Id=$BatchProcess_Id and b.User_Id=u.User_Id join Hstatus s on u.Status_Id=s.Status_Id where UserStatus<>'Disable'");
				echo "OK~$Out1~$Out2~$Out3";
	break;				
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
