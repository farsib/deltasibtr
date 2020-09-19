<?php
try {
require_once("../../lib/DSInitialReseller.php");
require_once("../../lib/DSRenderLib.php");
DSDebug(1,"DSBatchProcess_ChangeInfoRender.........................................................................");
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
	"SelectReseller",
    "SelectVisp",
    "SelectCenter",
    "SelectSupporter",
	"SelectStatus"
),0,0,0);

	
	
switch ($act) {
	case "InititalizeBatchProcess":
				DSDebug(0,"DSBatchProcess_ChangeInfoRender->InititalizeBatchProcess********************************************");
				
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				
				// if(DBSelectAsString("select SessionID from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!=$SessionId)
					// ExitError("Your batch process expired due to relogin with the same browser again...");
				if(DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id")!="Pending")
					ExitError("عملیات گروهی شما منقضی شده است");
				
				$sql="SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')";
				$res=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				if($res!='Yes')
					ExitError("آی پی شا معتبر نیست،ابتدا آی پی خود را در 'آی پی که مسدود نمی شود وارد کنید");
				
				
				$Action=Get_Input('POST','DB','Action','ARRAY',array("ChangeReseller","ChangeVisp","ChangeCenter","ChangeSupporter","ChangeStatus","ChangeAutoAddCallerId","ChangeExpirationDate"),0,0,0);
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				switch ($Action){
					case "ChangeReseller":
						$Option2=Get_Input('POST','DB','Reseller_Id','INT',1,4294967295,0,0);
						$ResellerName=DBSelectAsString("SELECT ResellerName from Hreseller where Reseller_Id=$Option2");
						if($ResellerName=='')
							ExitError("نماینده فروش نامعتبر انتخاب شده است");

						$ISOperator=DBSelectAsString("SELECT ISOperator from Hreseller where Reseller_Id=$Option2");
						if($ISOperator=='Yes')
							ExitError("شما اپراتور انتخاب کرده اید");
						
						$Option3="";
						$BatchComment="Change ResellerName=$ResellerName with Reseller_Id=$Option2 for $UserCount users";
					break;
					case "ChangeVisp":
						$Option2=Get_Input('POST','DB','Visp_Id','INT',1,4294967295,0,0);
						$VispName=DBSelectAsString("SELECT VispName from Hvisp where Visp_Id=$Option2");
						if($VispName=='')
							ExitError("ارائه دهنده مجازی نامعتبر انتخاب شده است");
						$Option3="";
						$BatchComment="Change VispName=$VispName with Visp_Id=$Option2 for $UserCount users";
					break;
					case "ChangeCenter":
						$Option2=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
						$CenterName=DBSelectAsString("SELECT CenterName from Hcenter where Center_Id=$Option2");
						if($CenterName=='')
							ExitError("مرکز نامعتبر انتخاب شده است");
						
						$InCenterUserCount=DBSelectAsString("Select count(1) from Tuser_authhelper where Center_Id='$Option2'");
						
						$CommonUserCount=DBSelectAsString("Select count(1) from Tuser_authhelper a join Hbatchprocess_users b on BatchProcess_Id=$BatchProcess_Id and a.User_Id=b.User_Id where Center_Id='$Option2'");
						
						$FreePort=DBSelectAsString("select TotalPort-BadPort from Hcenter where Center_Id='$Option2'");
						$NeededPort=$UserCount+$InCenterUserCount-$CommonUserCount;
						if($NeededPort>$FreePort){
							$sql="Update Hbatchprocess set Option3='Have no capacity in center $CenterName' where BatchProcess_Id=$BatchProcess_Id";
							DBUpdate($sql);
							ExitError("پورت آزاد کافی نیست.TotalFreePort=$FreePort, ExistedUser=$InCenterUserCount. TotalNeededPort=$NeededPort.");
						}
						
						$Option3="";
						$BatchComment="Change CenterName=$CenterName with Center_Id=$Option2 for $UserCount users";
					break;
					case "ChangeSupporter":
						$Option2=Get_Input('POST','DB','Supporter_Id','INT',1,4294967295,0,0);
						$SupporterName=DBSelectAsString("SELECT SupporterName from Hsupporter where Supporter_Id=$Option2");
						if($SupporterName=='')
							ExitError("پشتیبان نامعتبر انتخاب شده است");
						$Option3="";
						$BatchComment="Change SupporterName=$SupporterName with Supporter_Id=$Option2 for $UserCount users";
					break;
					case "ChangeStatus":
						$Option2=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);
						$StatusName=DBSelectAsString("SELECT StatusName from Hstatus where Status_Id=$Option2");
						if($StatusName=='')
							ExitError("وضعیت نامعتبر انتخاب شده است");
						$StatusVispAccess=DBSelectAsString("select VispAccess from Hstatus where Status_Id=$Option2");
						
						if($StatusVispAccess=='Selected'){
							
							$UserVispList=Array();
							$sql="select Tua.Visp_Id,Count(1) as CNT from Tuser_authhelper Tua ".
									"join Hbatchprocess_users Hbu on Hbu.BatchProcess_Id=$BatchProcess_Id and Tua.User_Id=Hbu.User_Id ".
									"group by Tua.Visp_Id";
							$n=CopyTableToArray($UserVispList,$sql);
							
							if($n<=0)
								throw new Exception('Error in permission check. Empty list or users visp not set');
							
							$HaveAccessVispListtmp=array();
							CopyTableToArray($HaveAccessVispListtmp,"select Visp_Id from Hstatus_vispaccess where Checked='Yes' and Status_Id=$Option2");
							
							$HaveAccessVispList=array_map('current', $HaveAccessVispListtmp);
							
							unset($HaveAccessVispListtmp);
							
							for($i=0;$i<$n;$i++){
								if(!in_array($UserVispList[$i]['Visp_Id'],$HaveAccessVispList)){
									$VName=DBSelectAsString("Select VispName from Hvisp where Visp_Id=".$UserVispList[$i]['Visp_Id']);
									
									$sql="Update Hbatchprocess set Option3='Not permit to change status for ".$UserVispList[$i]['CNT']." users' where BatchProcess_Id=$BatchProcess_Id";
									DBUpdate($sql);
									
									ExitError($UserVispList[$i]['CNT']." user(s) are in Visp '$VName' and '$VName' does not have access to this status");
								}
							}
							unset($UserVispList);
							unset($HaveAccessVispList);
						}
						
						$NewStatusIsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus where Status_Id=$Option2");
						if($NewStatusIsBusyPort=='Yes'){
							$sql="Select Tua.Center_Id,TotalPort-BadPort-COUNT(1) as FreePort from Tuser_authhelper Tua ".
									"join Hstatus Hs on Tua.Status_id=Hs.Status_Id and Hs.IsBusyPort='Yes' ".
									"join Hcenter Hc on Tua.Center_Id=Hc.Center_Id ".
									"group by Tua.Center_Id";
							$CenterCapacityList=array();
							$n1=CopyTableToArray($CenterCapacityList,$sql);
							
							
							$sql="select Tua.Center_Id,COUNT(1) as CNT from  Tuser_authhelper Tua ".
									"join Hbatchprocess_users Hbu on Hbu.BatchProcess_Id=$BatchProcess_Id and Tua.User_Id=Hbu.User_Id ".
									"join Hstatus Hs on Tua.Status_id=Hs.Status_Id and Hs.IsBusyPort='No' ".
									"group by Tua.Center_Id";
							$NewBusyPortsCenterList=array();
							$n2=CopyTableToArray($NewBusyPortsCenterList,$sql);
							
							for($i=0;$i<$n1;++$i)
								for($j=0;$j<$n2;++$j)
									if($CenterCapacityList[$i]["Center_Id"]==$NewBusyPortsCenterList[$j]["Center_Id"])
										if($CenterCapacityList[$i]["FreePort"]<$NewBusyPortsCenterList[$j]["CNT"]){
											$CName=DBSelectAsString("Select CenterName from Hcenter where Center_Id=".$CenterCapacityList[$i]["Center_Id"]);
									
											$sql="Update Hbatchprocess set Option3='Have no capacity in center $CName' where BatchProcess_Id=$BatchProcess_Id";
											DBUpdate($sql);
											ExitError("Center '$CName' has ".$CenterCapacityList[$i]["FreePort"]." free port. ".$NewBusyPortsCenterList[$j]["CNT"]." port is needed to change status.");
										}
						}
						$Option3="";
						$BatchComment="Change StatusName=$StatusName with Status_Id=$Option2 for $UserCount users";
					break;
					case "ChangeAutoAddCallerId":
						$Option2=Get_Input('POST','DB','AutoAddCallerId','ARRAY',array("Yes","No"),0,0,0);
						$Option3="";
						$BatchComment="Change AutoAddCallerId=$Option2 for $UserCount users";
					break;
					case "ChangeExpirationDate":
						$Option2=Get_Input('POST','DB','ExpirationDate','DateOrBlank',0,0,0,0);
						$Option3="";
						$ExpirationDate=DBSelectAsString("select {$DT}DateStr('$Option2')");
						$BatchComment="Change ExpirationDate=\'$ExpirationDate\' for $UserCount users";
					break;
					default:
						ExitError("عمل نامعتبر");
				}
				$sql="UPDATE Hbatchprocess set Option1='$Action',Option2='$Option2',Option3='$Option3',BatchState='InProgress',".
					"BatchComment='$BatchComment',StartDT=now() ".
					"where BatchProcess_Id=$BatchProcess_Id";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				if($res!=1)
					ExitError('درخواست غیرمجاز تشخیص داده شده است');
				sleep(1);
				echo "OK~";
	break;
	case "StartBatchProcess":
				DSDebug(0,"DSBatchProcess_ChangeInfoRender->StartBatchProcess********************************************");
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
				
				DSDebug(0,"DSBatchProcess_ChangeInfoRender->ChangeInfo********************************************");
				
				
				$UserCount=DBSelectAsString("select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
				
				$From_Index=DBSelectAsString("SELECT MIN(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'");
				$LowerBound=DBSelectAsString("SELECT From_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				$UpperLimit=DBSelectAsString("SELECT To_User_Index from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				DSDebug(0,"*************************************************************************\n***********************************************************");
				DSDebug(0,"LowerBound=$LowerBound\nUpperLimit=$UpperLimit\nUserCount=$UserCount");
				if(($UpperLimit-$LowerBound+1)!=$UserCount)
					ExitError("Checksum خطای");//Due to table lock and then insert record in prepare list all rhe $UserCount records is contiguous
				
				$Action=DBSelectAsString("select Option1 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
				
				if(($Action=="ChangeReseller")||($Action=="ChangeVisp")||($Action=="ChangeCenter")||($Action=="ChangeSupporter")||($Action=="ChangeAutoAddCallerId")||($Action=="ChangeExpirationDate")){
					$Item_Id=DBSelectAsString("select Option2 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");

					switch ($Action){
						case "ChangeReseller":
							$ItemName=DBSelectAsString("SELECT ResellerName from Hreseller where Reseller_Id=$Item_Id");				
							$FieldName="Reseller_Id";
							$ChildDataName="Reseller";
							$StepsDelay=100000;
							$Item="Reseller";
						break;
						case "ChangeVisp":
							$ItemName=DBSelectAsString("SELECT VispName from Hvisp where Visp_Id=$Item_Id");				
							$FieldName="Visp_Id";
							$ChildDataName="-";
							$StepsDelay=100000;
							$Item="Visp";
						break;
						case "ChangeCenter":
							$ItemName=DBSelectAsString("SELECT CenterName from Hcenter where Center_Id=$Item_Id");				
							$FieldName="Center_Id";
							$ChildDataName="-";
							$StepsDelay=100000;
							$Item="Center";
						break;
						case "ChangeSupporter":
							$ItemName=DBSelectAsString("SELECT SupporterName from Hsupporter where Supporter_Id=$Item_Id");				
							$FieldName="Supporter_Id";
							$ChildDataName="-";
							$StepsDelay=50000;
							$Item="Supporter";
						break;
						case "ChangeAutoAddCallerId":
							$ItemName=$Item_Id;
							$FieldName="AutoAddCallerId";
							$ChildDataName="-";
							$StepsDelay=20000;
							$Item="AutoAddCallerId";
						break;
						case "ChangeExpirationDate":
							$ItemName=$Item_Id;
							$FieldName="ExpirationDate";
							$ChildDataName="-";
							$StepsDelay=10000;
							$Item="ExpirationDate";
						break;
					}
					
					// $Status_Id
					// $StatusName
					
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index='$User_Index'");
							$sql="Update Huser set $FieldName='$Item_Id' where User_Id='$User_Id'";
							$tmp=DBUpdate($sql);
							if($tmp>0){
								$sql ="Insert Hlogdb set ".
											"LogDbCDT=Now(),".
											"Reseller_Id='$LReseller_Id',".
											"User_Id='',".
											"ClientIP=INET_ATON('$LClientIP'),".
											"LogType='Edit',".
											"DataName='User',".
											"DataId='$User_Id',".
											"ChildDataName='$ChildDataName',".
											"Comment='BatchProcess[Change $Item to `$ItemName` with ItemId=$Item_Id]'";
								DBUpdate($sql);
					
								$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemDT=NOW() where User_Index='$User_Index'";
								$res=DBUpdate($sql);
								DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
								
								usleep($StepsDelay);
								
							}	
							else{
								$sql="UPDATE Hbatchprocess_users set BatchItemState='Fail',BatchItemComment='$Item is currently `$ItemName`!',BatchItemDT=NOW() where User_Index=$User_Index";
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
				elseif($Action=="ChangeStatus"){
					$Status_Id=DBSelectAsString("select Option2 from Hbatchprocess where BatchProcess_Id=$BatchProcess_Id");
					$StatusName=DBSelectAsString("SELECT StatusName from Hstatus where Status_Id=$Status_Id");				
					for( $User_Index=$From_Index , $i=($From_Index-$LowerBound+1) ;  $User_Index<=$UpperLimit; $User_Index++,$i++){
						$sql="UPDATE Hbatchprocess_users set BatchItemState='InProgress',BatchItemDT=NOW() where User_Index=$User_Index and BatchItemState='Pending'";
						$res=DBUpdate($sql);
						DSDebug(2,"sql=$sql\nQueryResult=$res");
						if($res==1){
							$User_Id=DBSelectAsString("SELECT User_Id from Hbatchprocess_users where User_Index='$User_Index'");
							$CurrentStatus_Id=DBSelectAsString("SELECT Status_Id from Tuser_authhelper where User_Id='$User_Id'");
							
							if($CurrentStatus_Id!=$Status_Id){
								$sql="INSERT INTO Huser_status set StatusCDT=Now(),Reseller_Id=1,User_Id='$User_Id',Status_Id='$Status_Id',Comment='BatchProcess'";
								DBInsert($sql);
								
								$sql ="Insert Hlogdb set ".
											"LogDbCDT=Now(),".
											"Reseller_Id='$LReseller_Id',".
											"User_Id='',".
											"ClientIP=INET_ATON('$LClientIP'),".
											"LogType='Edit',".
											"DataName='User',".
											"DataId='$User_Id',".
											"ChildDataName='Status',".
											"Comment='BatchProcess[ChangeStatus to `$StatusName` with ItemId=$Status_Id]'";
								DBUpdate($sql);

								DBUpdate("Select ActivateUserNextServiceBase($User_Id)");
								DBUpdate("Select ActivateUserNextServiceIP($User_Id)");
					
					
								$sql="UPDATE Hbatchprocess_users set BatchItemState='Done',BatchItemDT=NOW() where User_Index=$User_Index";
								$res=DBUpdate($sql);
								DSDebug(2,"User_Index=$User_Index\nsql=$sql\nQueryResult=$res");
								
								usleep(100000);//wait 100 milisecond for each user
								
							}	
							else{
								$sql="UPDATE Hbatchprocess_users set BatchItemState='Fail',BatchItemComment='Status is currently `$StatusName`!',BatchItemDT=NOW() where User_Index=$User_Index";
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
					ExitError("عمل زیر نامعتبر تشخیص داده شده</br>(`$Action`)");
				
				
				DSDebug(0,"DSBatchProcess_ChangeInfoRender->FinalizeBatchProcess********************************************");
				$sql="UPDATE Hbatchprocess set BatchState='Done',EndDT=now() where BatchProcess_Id=$BatchProcess_Id";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Done'";
				$res1=DBSelectAsString($sql);
				$sql="select count(1) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Fail'";
				$res2=DBSelectAsString($sql);
				$ActionFa=str_replace(array("ChangeReseller","ChangeVisp","ChangeCenter","ChangeSupporter","ChangeAutoAddCallerId","ChangeExpirationDate","ChangeStatus"),array("تغییر نماینده فروش","تغییر ارائه دهنده مجازی اینترنت","تغییر مرکز","تغییر پشتیبان","تغییر وضعیت افزودن خودکار مک/آی پی","تغییر تاریخ انقضا نام کاربری","تغییر وضعیت"),$Action);
				echo "OK~".(($res1>0)?("عملیات $ActionFa  برای  $res1 کاربر با موفقیت انجام شد"):("$ActionFa برای هیچ کاربری انجام نشد")).($res2>0?(" و هیچ عملیاتی برای $res2 کاربر انجام نشد(گزارش را بررسی کنید)"):(""));
	break;	
    case "PauseInProgress":
				DSDebug(0,"DSBatchProcess_ChangeInfoRender->PauseInProgress********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				$sql="UPDATE Hbatchprocess_users set BatchItemState='Paused' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				echo "OK~$res~";
	break;	
    case "CancelInProgress":
				DSDebug(0,"DSBatchProcess_ChangeInfoRender->CancelInProgress********************************************");
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
	
	case "SelectReseller":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT 0 as Reseller_Id,'-- از لیست انتخاب کنید --' as ResellerName union ".
								"(SELECT Reseller_Id,ResellerName From Hreseller where ISOperator='No' and ISEnable='Yes' order by ResellerName Asc)";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;      
    case "SelectVisp":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT 0 as Visp_Id,'-- از لیست انتخاب کنید --' as VispName union ".
								"(SELECT Visp_Id,VispName From Hvisp where ISEnable='Yes' order by VispName Asc)";
							$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectCenter":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT 0 as Center_Id,'-- از لیست انتخاب کنید --' as CenterName union ".
								"(SELECT Center_Id,CenterName From Hcenter where ISEnable='Yes' order by CenterName Asc)";
                            $options->render_sql($sql,"","Center_Id,CenterName","","");
    break;
    case "SelectSupporter":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT 0 as Supporter_Id,'-- از لیست انتخاب کنید --' as SupporterName union ".
								"(SELECT Supporter_Id,SupporterName From Hsupporter where ISEnable='Yes' order by SupporterName Asc)";
                            $options->render_sql($sql,"","Supporter_Id,SupporterName","","");
    break;	
    case "SelectStatus":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT 0 as Status_Id,'-- از لیست انتخاب کنید --' as StatusName union ".
								"(".
								"SELECT Hs.Status_Id,Hs.StatusName From Hstatus Hs where Hs.ISEnable='Yes' and ".
								"( (Hs.ResellerAccess='All') or ".
									"(Hs.ResellerAccess='Selected' and $LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Checked='Yes')) )".
								"order by Hs.StatusName Asc".
								")";
                            $options->render_sql($sql,"","Status_Id,StatusName","","");
    break;	   
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
