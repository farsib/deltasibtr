<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Status_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","SelectStatusItem","SelectScheduleStatusItem","ChangeStatus", "GetStatusDetail", "DeleteScheduled"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Status_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Status.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				function color_rows($row){
					if($row->get_value("User_Status_Id")==0)
						$row->set_row_style("color:blue");
				}

				DSGridRender_Sql(-1,
					"SELECT 2 as SortKey,0 as User_Status_Id,Concat(StatusName,'[زمانبندی شده]') as StatusName,{$DT}DateTimeStr(Scheduled_DT) As StatusCDT,ResellerName,'برای تغییر زمانبندی شده' as Comment from ".
						"Huser_statusqueue usq left join Hstatus s1 on usq.ScheduledStatus_Id=s1.Status_Id Left join Hreseller r1 on usq.Reseller_Id=r1.Reseller_Id Where (User_Id=$User_Id) union ".
					"SELECT 1 as SortKey,User_Status_Id,StatusName,{$DT}DateTimeStr(StatusCDT) As StatusCDT,ResellerName,Comment From ".
						"Huser_status us left join Hstatus s2 on us.Status_Id=s2.Status_Id Left join Hreseller r2 on us.Reseller_Id=r2.Reseller_Id Where (User_Id=$User_Id)" .$sqlfilter." Order by SortKey Desc,User_Status_Id Desc",

					"User_Status_Id",

					"User_Status_Id,StatusName,StatusCDT,ResellerName,Comment",

					"","","color_rows");
       break;
    case "ChangeStatus":
				DSDebug(1,"DSUser_Status_ListRender Update ******************************************");
				$NewRowInfo=array();
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id='$User_Id'");

				exitifnotpermit($Visp_Id,"Visp.User.Status.ChangeStatus");

				$To_Status_Id=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);
				$Comment=Get_Input('POST','DB','Comment','STR',0,128,0,0);

				//Check Permition
				//StatusTo
				$From_Status_Id=DBSelectAsString("Select Status_Id From Huser where (User_Id=$User_Id)");
				if($From_Status_Id==$To_Status_Id)
					ExitError("وضعیت قدیم با وضعیت جدید یکسان است");
				$Checked=DBSelectAsString("Select Checked From Hstatus_statusto Where (Status_Id=$From_Status_Id)And(StatusTo_Id=$To_Status_Id)");
				if($Checked!='Yes') {
					$StatusFrom=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$From_Status_Id");
					$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
					ExitError("مجاز نیست [$StatusTo] به  [$StatusFrom] تغییر وضعیت از");
				}
				//Reseller-Access
				$ResellerAccess=DBSelectAsString("Select ResellerAccess From Hstatus Where (Status_Id=$To_Status_Id)");
				if($ResellerAccess!='All'){
					$Checked=DBSelectAsString("Select Checked From Hstatus_reselleraccess Where (Status_Id=$To_Status_Id)And(Reseller_Id=$LReseller_Id)");
					if($Checked!='Yes') {
						$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
						ExitError("شما مجاز به تغییر وضعیت،به وضعیت زیر نیستید</br>[$StatusTo]");
					}
				}
				//VispAccess
				$VispAccess=DBSelectAsString("Select VispAccess From Hstatus Where (Status_Id=$To_Status_Id)");
				if($VispAccess!='All'){
					$Checked=DBSelectAsString("Select Checked From Hstatus_vispaccess Where (Status_Id=$To_Status_Id)And(Visp_Id=$Visp_Id)");
					if($Checked!='Yes') {
						$StatusFrom=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$From_Status_Id");
						$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
						ExitError("کاربر این ارائه دهنده مجازی نمی تواند وضعیت را به وضعیت زیر تغییر دهد</br>[$StatusTo]");
					}
				}

				if(ISPermit($Visp_Id,"Visp.User.Status.SetTimedStatus"))
					$StatusType=Get_Input('POST','DB','StatusType','ARRAY',array("Fixed","Timed"),0,0,0);
				else
					$StatusType="Fixed";

				//check count of center
				$OldIsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id=$From_Status_Id");
				$NewIsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id=$To_Status_Id");
				DSDebug(1,"OldIsBusyPort=$OldIsBusyPort show change to NewIsBusyPort=$NewIsBusyPort");
				if($NewIsBusyPort=='Yes' && $OldIsBusyPort=='No'){
					$Center_Id=DBSelectAsString("SELECT Center_Id from Huser Where User_Id=$User_Id");
					$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=$Center_Id and IsBusyPort='Yes'");
					$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id=$Center_Id");
					DSDebug(2,"$n busy port exist in this center(Center_Id=$Center_Id). Max available port is $max");
					if($n>=$max)
							ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده است");
				}

				if($StatusType=="Timed"){
					$ScheduledStatus_Id=Get_Input('POST','DB','ScheduledStatus_Id','INT',1,4294967295,0,0);
					$ScheduleHour=Get_Input('POST','DB','ScheduleHour','INT',1,9999,0,0);

					$Checked=DBSelectAsString("Select Checked From Hstatus_statusto Where (Status_Id=$To_Status_Id)And(StatusTo_Id=$ScheduledStatus_Id)");
					if($Checked!='Yes') {
						$StatusFrom=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
						$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$ScheduledStatus_Id");
						ExitError("مجاز نیست [$StatusTo] به  [$StatusFrom] تغییر وضعیت از");
					}
					//Reseller-Access
					$ResellerAccess=DBSelectAsString("Select ResellerAccess From Hstatus Where (Status_Id=$ScheduledStatus_Id)");
					if($ResellerAccess!='All'){
						$Checked=DBSelectAsString("Select Checked From Hstatus_reselleraccess Where (Status_Id=$ScheduledStatus_Id)And(Reseller_Id=$LReseller_Id)");
						if($Checked!='Yes') {
							$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$ScheduledStatus_Id");
							ExitError("شما مجاز به تغییر وضعیت،به وضعیت زیر نیستید</br>[$StatusTo]");
						}
					}
					//VispAccess
					$VispAccess=DBSelectAsString("Select VispAccess From Hstatus Where (Status_Id=$ScheduledStatus_Id)");
					if($VispAccess!='All'){
						$Checked=DBSelectAsString("Select Checked From Hstatus_vispaccess Where (Status_Id=$ScheduledStatus_Id)And(Visp_Id=$Visp_Id)");
						if($Checked!='Yes') {
							$StatusFrom=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
							$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$ScheduledStatus_Id");
							ExitError("کاربر این ارائه دهنده مجازی نمی تواند وضعیت را به وضعیت زیر تغییر دهد</br>[$StatusTo]");
						}
					}

					$ScheduleIsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id=$ScheduledStatus_Id");
					if($NewIsBusyPort!=$ScheduleIsBusyPort)
						ExitError("فیلد 'پورت مشغول است' وضعیت زمانبندی شده با وضعیت جدید باید برابر باشد");
				}


				//----------------------
				$sql ="insert Huser_status set StatusCDT=Now(),";
				$sql.="Reseller_Id='$LReseller_Id',";
				$sql.="User_Id='$User_Id',";
				$sql.="Status_Id='$To_Status_Id',";
				$sql.="Comment='$Comment'";

				$RowId=DBInsert($sql);
				$StatusName=DBSelectAsString("Select StatusName From Hstatus where Status_Id='$To_Status_Id'");
				logdb("Edit","User",$User_Id,"Status","Set to $StatusName");

				DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)");
				DBSelectAsString("Select ActivateUserNextServiceIP($User_Id)");

				//Must be after status changed
				if($StatusType=="Timed"){
					$sql ="Replace Huser_statusqueue set ";
					$sql.="User_Id='$User_Id',";
					$sql.="Reseller_Id='$LReseller_Id',";
					$sql.="Scheduled_DT=Convert(DATE_FORMAT(Date_Add(Now(),interval '$ScheduleHour' Hour),'%Y-%m-%d-%H:00:00'),DATETIME),";
					$sql.="ScheduledStatus_Id='$ScheduledStatus_Id'";

					$RowId=DBInsert($sql);
					$StatusName=DBSelectAsString("Select StatusName From Hstatus where Status_Id='$ScheduledStatus_Id'");
					$Scheduled_DT=DBSelectAsString("Select {$DT}datetimestr(Scheduled_DT) From Huser_statusqueue where User_Id='$User_Id'");
					logdb("Edit","User",$User_Id,"Status","Scheduled to set to [$StatusName] at [$Scheduled_DT]");
				}
				echo "OK~";
        break;
    case "SelectStatusItem":
				DSDebug(1,"DSUser_Status_ListRender-> SelectStatusItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$CurrentStatus_Id=DBSelectAsString("Select Status_Id from Huser where User_Id=$User_Id");
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");

				$sql=//"Select 0 As Status_Id,'-- Please Select From List --' As StatusName union ".
				"SELECT us.Status_Id As Status_Id,us.StatusName As StatusName FROM Hstatus us ".
				"Where  us.Status_Id in".
				"(Select StatusTo_Id From Hstatus_statusto WHERE (Status_Id=$CurrentStatus_Id)And(Checked='Yes'))".
				"And us.Status_Id in ".
				"(Select Status_Id From Hstatus Where(ResellerAccess='All') union Select Status_Id From Hstatus_reselleraccess where (Reseller_Id=$LReseller_Id)And(Checked='Yes'))".
				"And us.Status_Id in ".
				"(Select Status_Id From Hstatus Where(VispAccess='All') union Select Status_Id From Hstatus_vispaccess where (Visp_Id=$Visp_Id)And(Checked='Yes'))";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
        break;
    case "SelectScheduleStatusItem":
				DSDebug(1,"DSUser_Status_ListRender-> SelectStatusItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$CurrentStatus_Id=Get_Input('GET','DB','Status_Id','INT',1,4294967295,0,0);
				$CurrentIsBusyPort=DBSelectAsString("Select IsBusyPort from Hstatus where Status_Id='$CurrentStatus_Id'");

				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");

				$sql="SELECT us.Status_Id As Status_Id,us.StatusName As StatusName FROM Hstatus us ".
				"Where us.IsBusyPort='$CurrentIsBusyPort' ".
				"And us.Status_Id in (Select StatusTo_Id From Hstatus_statusto WHERE (Status_Id=$CurrentStatus_Id)And(Checked='Yes'))".
				"And us.Status_Id in (Select Status_Id From Hstatus Where(ResellerAccess='All') union Select Status_Id From Hstatus_reselleraccess where (Reseller_Id=$LReseller_Id)And(Checked='Yes'))".
				"And us.Status_Id in (Select Status_Id From Hstatus Where(VispAccess='All') union Select Status_Id From Hstatus_vispaccess where (Visp_Id=$Visp_Id)And(Checked='Yes'))";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
        break;
	case "GetStatusDetail":
				DSDebug(1,"DSUser_Status_ListRender-> GetStatusDetail *****************");
				$Status_Id=Get_Input('GET','DB','Status_Id','INT',1,4294967295,0,0);

				$OutStr=DBSelectAsString("Select Concat(UserStatus,'`',CanWebLogin,'`',CanAddService,'`',IsBusyPort,'`',PortStatus) from Hstatus where Status_Id='$Status_Id'");
				$NewStatusName=DBSelectAsString("select ns.StatusName from Hstatus s left join Hstatus ns on s.NewStatus_Id=ns.Status_Id where s.Status_Id='$Status_Id'");
			echo "$OutStr`$NewStatusName";
        break;
	case "DeleteScheduled":
				DSDebug(1,"DSUser_Status_ListRender->DeleteScheduled ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Status.DeleteScheduled");
				$LogComment=DBSelectAsString("Select Concat('Scheduled status ',StatusName,' at ',{$DT}DateTimeStr(Scheduled_DT),' deleted') from Huser_statusqueue usq join Hstatus s on usq.ScheduledStatus_Id=s.Status_Id where User_Id='$User_Id'");
				DBDelete("Delete from Huser_statusqueue where User_Id='$User_Id'");
				logdb("Edit","User",$User_Id,"Status",$LogComment);
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";

}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
