<?php
try {
require_once("../../lib/DSInitialReseller.php");
require_once("../../lib/DSRenderLib.php");
DSDebug(1,"DSBatchProcess_AddPaymentRender.........................................................................");
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
	"SaveLog"
),0,0,0);

	
	
switch ($act) {
	case "InititalizeBatchProcess":
				DSDebug(0,"DSBatchProcess_AddPaymentRender->InititalizeBatchProcess********************************************");
				
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				
				// if(DBSelectAsString("select SessionID from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!=$SessionId)
					// ExitError("Your batch process expired due to relogin with the same browser again...");
				
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="Pending")
					ExitError("عملیات گروهی شما منقضی شده است");
				
				
				$sql="SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')";
				$res=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				if($res!='Yes')
					ExitError("آی پی شا معتبر نیست،ابتدا آی پی خود را در 'آی پی که مسدود نمی شود' وارد کنید");
				
				$Action=Get_Input('POST','DB','Action','ARRAY',array('AddPayment','ResetPayment'),0,0,0);
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				if($Action=='AddPayment'){
				
					$Price=Get_Input('POST','DB','Price','PRC',1,14,0,0);
					if($Price<=0){
						ExitError("مبلغ باید بیشتر از ۰ باشد");
					}
					$Direction=Get_Input('POST','DB','Direction','ARRAY',array('GetMoney','RefundMoney'),0,0,0);
					$PaymentType=Get_Input('POST','DB','PaymentType','ARRAY',array('Cash','Cheque','Pos','Deposit','Other','TAX','Off'),0,0,0);
					$Option1="AddPayment";
					$Option2=$Price;
					$Option3="$Direction";
					$Option4="$PaymentType";
					$BatchComment="Add $Direction with Price=[$Price],PaymentType=[$PaymentType] to $UserCount users";
				}
				else{
					$Option1="ResetPayment";
					$Option2="";
					$Option3="";
					$Option4="";
					$BatchComment="Reset PayBalance of $UserCount users to zero";
				}
				
				
				
				
				$sql="UPDATE Hbatchprocess set ".
					"Option1='$Option1',".
					"Option2='$Option2',".
					"Option3='$Option3',".
					"Option4='$Option4',".
					"BatchState='InProgress',".
					"BatchComment='$BatchComment',".
					"StartDT=now() ".
					"where BatchProcess_Id=$BatchProcess_Id and BatchState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				if($res!=1)
					ExitError('درخواست غیرمجاز تشخیص داده شده');
				sleep(1);
				echo "OK~";
	break;
	case "StartBatchProcess":
	
				
				DSDebug(0,"DSBatchProcess_AddPaymentRender->StartBatchProcess********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);

				
				// if(DBSelectAsString("select SessionID from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!=$SessionId)
					// ExitError("Your batch process expired due to relogin with the same browser again...");
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="InProgress")
					ExitError("عملیات گروهی شما منقضی شده است");
		
				
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Pending' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Paused'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				DSDebug(0,"DSBatchProcess_AddPaymentRender->AddPayment********************************************");

				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				$From_Index=DBSelectAsString("SELECT MIN(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'");
				
				$LowerBound=DBSelectAsString("SELECT From_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				$UpperLimit=DBSelectAsString("SELECT To_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				DSDebug(0,"*************************************************************************\n***********************************************************");
				DSDebug(0,"LowerBound=$LowerBound\nUpperLimit=$UpperLimit\nUserCount=$UserCount");
				
				if(($UpperLimit-$LowerBound+1)!=$UserCount)
					ExitError("Checksum خطای");//Due to table lock and then insert record in prepare list all rhe $UserCount records is contiguous
				
				
				$Action=DBSelectAsString("select Option1 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				if($Action=='AddPayment'){
					
					$Price=DBSelectAsString("select Option2 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
					$Direction=DBSelectAsString("select Option3 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
					$PaymentType=DBSelectAsString("select Option4 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
					// $VoucherNo="";
					// $VoucherDate="";
					// $BankBranchName="";
					// $BankBranchNo="";
					// $Comment="BatchProcess";
					$logdbComment="BatchProcess:[$Direction with Price=[$Price] & PaymentType=[$PaymentType]]";
					if($Direction=='RefundMoney')
						$Price=-$Price;
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index=$User_Index");
							
							AddResellerTransaction($LReseller_Id,0,$User_Id,$Direction,(-1)*$Price);
							// AddPaymentToUser($LReseller_Id,$User_Id,$PaymentType,$Price,$VoucherNo,$VoucherDate,$BankBranchName,$BankBranchNo,$Comment);
													
							$sql= "insert Huser_payment(User_Id,Creator_Id,User_PaymentCDT,PaymentType,Price,PayBalance,Comment) ".
									"Select $User_Id,'$LReseller_Id',Now(),'$PaymentType','$Price',PayBalance+($Price),'By BatchProcess' From ".
									"Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc Limit 1";
							
							DBInsert($sql);
							
							
							$sql ="Insert Hlogdb set ".
									"LogDbCDT=Now(),".
									"Reseller_Id='$LReseller_Id',".
									"User_Id='',".
									"ClientIP=INET_ATON('$LClientIP'),".
									"LogType='Edit',".
									"DataName='User',".
									"DataId='$User_Id',".
									"ChildDataName='Payment',".
									"Comment='$logdbComment'";
							DBUpdate($sql);						
							$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemDT=NOW() where User_Index=$User_Index";
							$res=DBUpdate($sql);
							DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
							DBUpdate("Update Hbatchprocess set CompletedCount=$i where BatchProcess_Id=$BatchProcess_Id");
							
							usleep(80000);//wait 80 milisecond for each user							
							
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
				elseif($Action=='ResetPayment'){

					$PaymentType="Other";

					$VoucherNo="";
					$VoucherDate="";
					$BankBranchName="";
					$BankBranchNo="";
					$Comment="BatchProcess";
					$logdbComment="BatchProcess:[Resetting User PayBalance to 0]";
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index=$User_Index");
							
							$UserPayBalance=DBSelectAsString("Select PayBalance from Huser where User_Id=$User_Id");
							if($UserPayBalance!=0){
								$Price=-$UserPayBalance;
								if($Price<0)
									$Direction='GetMoney';
								else
									$Direction='RefundMoney';
							
								AddResellerTransaction($LReseller_Id,0,$User_Id,$Direction,(-1)*$Price);
								// AddPaymentToUser($LReseller_Id,$User_Id,$PaymentType,$Price,$VoucherNo,$VoucherDate,$BankBranchName,$BankBranchNo,$Comment);
								$sql= "insert Huser_payment(User_Id,Creator_Id,User_PaymentCDT,PaymentType,Price,PayBalance,Comment) ".
										"Select $User_Id,'$LReseller_Id',Now(),'$PaymentType','$Price',PayBalance+($Price),'By BatchProcess' From ".
										"Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc Limit 1";
								
								DBInsert($sql);							
							
							
								$sql ="Insert Hlogdb set ".
										"LogDbCDT=Now(),".
										"Reseller_Id='$LReseller_Id',".
										"User_Id='',".
										"ClientIP=INET_ATON('$LClientIP'),".
										"LogType='Edit',".
										"DataName='User',".
										"DataId='$User_Id',".
										"ChildDataName='Payment',".
										"Comment='$logdbComment'";
								DBUpdate($sql);
								
								$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemDT=NOW() where User_Index=$User_Index";
								$res=DBUpdate($sql);
								DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
								usleep(80000);//wait 80 milisecond for each user
							}
							else{
								$sql="UPDATE Hbatchprocess_users set BatchItemState='Fail',BatchItemComment='Paybalance currently is 0',BatchItemDT=NOW() where User_Index=$User_Index";
								$res=DBUpdate($sql);
								DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
								
								usleep(10000);//wait 10 milisecond for each user
							}
							
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
					ExitError("Invalid action(`$Action`) detected.");
				
				DSDebug(0,"DSBatchProcess_AddPaymentRender->FinalizeBatchProcess********************************************");
				$sql="UPDATE Hbatchprocess set BatchState='Done',EndDT=now() where BatchProcess_Id=$BatchProcess_Id";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");

				
				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Done'";
				$res1=DBSelectAsString($sql);
				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Fail'";
				$res2=DBSelectAsString($sql);
				
				
				
				if($Action=="AddPayment")
					echo "OK~".(($res1>0)?("افزودن پرداختی برای $res1 کاربر با موفقیت انجام شد"):("هیج پرداختی اضافه نشد")).(($res2>0)?(" و هیج عملیاتی برای $res2 کاربر انجام نشد(گزارش را بررسی کنید)"):(""));
				else
					echo "OK~".(($res1>0)?("صفر کردن تراز مالی برای  $res1 کاربر با موفقیت انجام شد"):("هیج پرداختی اضافه نشد")).(($res2>0)?(" و هیچ عملیاتی برای $res2 کاربر انجام نشد(گزارش را بررسی کنید)"):(""));
				
				
	break;	
    case "PauseInProgress":
				DSDebug(0,"DSBatchProcess_AddPaymentRender->PauseInProgress********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);			
		
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Paused' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				echo "OK~$res~";
	break;	
    case "CancelInProgress":
				DSDebug(0,"DSBatchProcess_AddPaymentRender->CancelInProgress********************************************");
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
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
