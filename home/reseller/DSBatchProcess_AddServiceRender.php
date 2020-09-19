<?php
try {
require_once("../../lib/DSInitialReseller.php");
require_once("../../lib/DSRenderLib.php");
DSDebug(1,"DSBatchProcess_AddServiceRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

if($LReseller_Id!=1) ExitError('فقط ادمین میتواند عملیات گروهی را انجام دهد');

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
	"SelectServiceNameBase",
	"SelectServiceNameExtraCredit",
	"SelectServiceNameOther",
	"GetServiceInfo"
),0,0,0);

	
	
switch ($act) {
	case "InititalizeBatchProcess":
				DSDebug(0,"DSBatchProcess_AddServiceRender->InititalizeBatchProcess********************************************");
				
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				
				// if(DBSelectAsString("select SessionID from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!=$SessionId)
					// ExitError("Your batch process expired due to relogin with the same browser again...");
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="Pending")
					ExitError("عملیات گروهی شما منقضی شده");
				
				$sql="SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')";
				$res=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				if($res!='Yes')
					ExitError("آی پی شما مورد اعتماد نیست،ابتدا آی پی خود را در 'آی پی که مسدود نمی شود' وارد کنید");
				
				$Service_Id=Get_Input('POST','DB','Service_Id','INT',1,4294967295,0,0);
				$ServiceName=DBSelectAsString("SELECT ServiceName from Hservice where Service_Id=$Service_Id");
				if($ServiceName=='')
					ExitError("سرویس نامعتبر");
				
				$UserVispList=Array();
				$sql="select Tua.Visp_Id,Count(1) as CNT from Tuser_authhelper Tua ".
						"join Hbatchprocess_users Hbu on Hbu.BatchProcess_Id=$BatchProcess_Id and Tua.User_Id=Hbu.User_Id ".
						"group by Tua.Visp_Id";
				$n=CopyTableToArray($UserVispList,$sql);
				
				if($n<=0)
					throw new Exception('Error in permission check. Empty list or users visp not set');
				
				DSDebug(0,"***********************************************************");
				
				$ServiceVispAccess=DBSelectAsString("select VispAccess from Hservice where Service_Id=$Service_Id");
				$HaveAccessVispListtmp=array();
				if($ServiceVispAccess=='All')
					$Op1="not in";
				else
					$Op1="in";
				CopyTableToArray($HaveAccessVispListtmp,"select Visp_Id from Hvisp where Visp_Id $Op1 (select Visp_Id from Hservice_vispaccess where Checked='Yes' and Service_Id=$Service_Id)");
				
				$HaveAccessVispList=array_map('current', $HaveAccessVispListtmp);
				
				unset($HaveAccessVispListtmp);
				
				for($i=0;$i<$n;$i++){
					if(!in_array($UserVispList[$i]['Visp_Id'],$HaveAccessVispList)){
						$VName=DBSelectAsString("Select VispName from Hvisp where Visp_Id=".$UserVispList[$i]['Visp_Id']);
						
						$sql="Update Hbatchprocess set Option3='Not permit to add for ".$UserVispList[$i]['CNT']." users' where BatchProcess_Id=$BatchProcess_Id and BatchState='Pending'";
						DBUpdate($sql);
						
						ExitError($UserVispList[$i]['CNT']." user(s) are in Visp '$VName' and '$VName' does not have access to this service");
					}
				}
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				
				$sql="UPDATE Hbatchprocess set Option1='AddService',Option2='$Service_Id',Option3='',BatchState='InProgress',".
					"BatchComment='Add ServiceName=$ServiceName with Service_Id=$Service_Id to $UserCount users',StartDT=now() ".
					"where BatchProcess_Id=$BatchProcess_Id and BatchState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				if($res!=1)
					ExitError('درخواست غیرمجاز تشخیص داده شد');
				sleep(1);
				echo "OK~";
	break;
	case "StartBatchProcess":
								
				DSDebug(0,"DSBatchProcess_AddServiceRender->StartBatchProcess********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);

				
				// if(DBSelectAsString("select SessionID from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!=$SessionId)
					// ExitError("Your batch process expired due to relogin with the same browser again...");
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="InProgress")
					ExitError("عملیات گروهی شما منقضی شده است");
				
				$CheckedPermission=DBSelectAsString("select Option3 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				if($CheckedPermission!="")
					ExitError($CheckedPermission);
				
				
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Pending' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Paused'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				DSDebug(0,"DSBatchProcess_AddServiceRender->AddService********************************************");
				$Action=DBSelectAsString("select Option1 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				if($Action!="AddService")
					ExitError("عملیات زیر نامعتبر تشخیص داده شد</br>`$Action`");
				$Service_Id=DBSelectAsString("select Option2 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				$ServiceType=DBSelectAsString("Select ServiceType from Hservice where Service_Id=$Service_Id");
				if($ServiceType=='Base')
					$DelayTime=120000;
				elseif($ServiceType=='ExtraCredit')
					$DelayTime=80000;
				else
					$DelayTime=35000;
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				$From_Index=DBSelectAsString("SELECT MIN(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'");
				
				$LowerBound=DBSelectAsString("SELECT From_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				$UpperLimit=DBSelectAsString("SELECT To_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				DSDebug(0,"*************************************************************************\n***********************************************************");
				DSDebug(0,"LowerBound=$LowerBound\nUpperLimit=$UpperLimit\nUserCount=$UserCount");
				if(($UpperLimit-$LowerBound+1)!=$UserCount)
					ExitError("Checksum خطای");//Due to table lock and then insert record in prepare list all the $UserCount records is contiguous
				
				
				for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
					$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
					$res=DBUpdate($sql);
					DSDebug(2,"sql=$sql\nQueryResult=$res");
					if($res==1){
						$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index=$User_Index");
						$res=AddServiceToUser($LReseller_Id,$User_Id,$Service_Id,"PostPaid","","",0);
						if($res!="")
							ExitError($res);
						
						$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemDT=NOW() where User_Index=$User_Index";
						$res=DBUpdate($sql);
						DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
						
						DBUpdate("Update Hbatchprocess set CompletedCount=$i where BatchProcess_Id=$BatchProcess_Id");
						usleep($DelayTime);//wait $DelayTime microsecond for each user						
						
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
				
				DSDebug(0,"DSBatchProcess_AddServiceRender->FinalizeBatchProcess********************************************");
				$sql="UPDATE Hbatchprocess set BatchState='Done',EndDT=now() where BatchProcess_Id=$BatchProcess_Id";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");

				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Done'";
				$res1=DBSelectAsString($sql);
				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Fail'";
				$res2=DBSelectAsString($sql);
				
				
				echo "OK~".(($res1>0)?("افزودن سرویس با موفقیت برای $res1 کاربر انجام شد"):("هیچ سرویسی اضافه نشد")).(($res2>0)?(" و هیچ عملیاتی برای $res2 کاربر انجام نشد(گزارش را بررسی کنید)"):(""));				
				
	break;	
    case "PauseInProgress":
				DSDebug(0,"DSBatchProcess_AddServiceRender->PauseInProgress********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);			
		
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Paused' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				echo "OK~$res~";
	break;	
    case "CancelInProgress":
				DSDebug(0,"DSBatchProcess_AddServiceRender->CancelInProgress********************************************");
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
    case "SelectServiceNameBase":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="Select 0 As Service_Id,'-- از لیست انتخاب کنید --' As ServiceName union ".
								"(SELECT Service_Id,ServiceName From Hservice Hs where ".
								"Hs.ServiceType='Base' and ".
								"Hs.ISEnable='Yes' and ".
								"Hs.ResellerChoosable='Yes' and ".
								"( (Hs.ResellerAccess='All') or ".
									"(Hs.ResellerAccess='Selected' and $LReseller_Id in (select Reseller_Id from Hservice_reselleraccess where Checked='Yes')) ) and ".
								"( (Hs.AvailableFromDate=0) or (Date(Now())>=Hs.AvailableFromDate)) and ".
								"( (Hs.AvailableToDate=0) or (Date(Now())<=Hs.AvailableToDate)) and ".
								"Hs.IsDel='No' ".
								"order by Hs.ServiceName Asc)";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;	   
    case "SelectServiceNameExtraCredit":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="Select 0 As Service_Id,'-- از لیست انتخاب کنید --' As ServiceName union ".
								"(SELECT Service_Id,ServiceName From Hservice Hs where ".
								"ServiceType='ExtraCredit' and ".
								"Hs.ISEnable='Yes' and ".
								"Hs.ResellerChoosable='Yes' and ".
								"( (Hs.ResellerAccess='All') or ".
									"(Hs.ResellerAccess='Selected' and $LReseller_Id in (select Reseller_Id from Hservice_reselleraccess where Checked='Yes')) ) and ".
								"( (Hs.AvailableFromDate=0) or (Date(Now())>=Hs.AvailableFromDate)) and ".
								"( (Hs.AvailableToDate=0) or (Date(Now())<=Hs.AvailableToDate)) and ".
								"Hs.IsDel='No' ".
								"order by Hs.ServiceName Asc)";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;
    case "SelectServiceNameOther":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="Select 0 As Service_Id,'-- از لیست انتخاب کنید --' As ServiceName union ".
								"(SELECT Service_Id,ServiceName From Hservice Hs where ".
								"ServiceType='Other' and ".
								"Hs.ISEnable='Yes' and ".
								"Hs.ResellerChoosable='Yes' and ".
								"( (Hs.ResellerAccess='All') or ".
									"(Hs.ResellerAccess='Selected' and $LReseller_Id in (select Reseller_Id from Hservice_reselleraccess where Checked='Yes')) ) and ".
								"( (Hs.AvailableFromDate=0) or (Date(Now())>=Hs.AvailableFromDate)) and ".
								"( (Hs.AvailableToDate=0) or (Date(Now())<=Hs.AvailableToDate)) and ".
								"Hs.IsDel='No' ".
								"order by Hs.ServiceName Asc)";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;
	case "GetServiceInfo":
				DSDebug(0,"DSBatchProcess_AddServiceRender-> GetServiceInfo *****************");
				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				$TempArray=Array();
				CopyTableToArray($TempArray,"Select Description,Price,InstallmentNo,InstallmentPeriod,InstallmentFirstCash From Hservice where Service_Id=$Service_Id");
				$Description=$TempArray[0]["Description"];
				$ServicePrice=$TempArray[0]["Price"];
				$InstallmentNo=$TempArray[0]["InstallmentNo"];
				$InstallmentFirstCash=$TempArray[0]["InstallmentFirstCash"];

				$ServicePrice=$ServicePrice;
				if($InstallmentNo==0)
					$Price=$ServicePrice;
				elseif($InstallmentFirstCash=='Yes')
					$Price=($ServicePrice/$InstallmentNo);
				else
					$Price=0;
				
				$VAT=DBSelectAsString("Select Param5 From Hserver where PartName='Param'");
				if($VAT=='') $VAT=0;
				$PriceWithVAT=$Price*(1+$VAT/100);
				
				$ServicePrice=number_format($ServicePrice, 0, '.', ',');
				$Price=number_format($Price, 0, '.', ',');
				$PriceWithVAT=number_format($PriceWithVAT, 0, '.', ',');
				
				
				DSDebug(0,"InstallmentNo`ServicePrice`Price`VAT`PriceWithVAT`Description\n$InstallmentNo`$ServicePrice`$Price`$VAT`$PriceWithVAT`$Description");
				echo "$InstallmentNo`$ServicePrice`$Price`$VAT`$PriceWithVAT`$Description";
	break;				
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
