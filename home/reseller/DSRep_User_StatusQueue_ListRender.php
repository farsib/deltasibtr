<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_StatusQueue_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.StatusQueue.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_StatusQueue_ListRender->List ********************************************");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by usq.User_Id Desc";
				
				$SelectStr="usq.User_Id,Username,StatusName,{$DT}DateTimeSTr(Scheduled_DT) as Scheduled_DT,ResellerName";
				$ColumnStr="User_Id,Username,StatusName,Scheduled_DT,ResellerName";
				
				$sql="select $SelectStr from Huser_statusqueue usq ".
					"join Huser u on usq.User_Id=u.User_Id ".
					"join Hstatus s on usq.ScheduledStatus_Id=s.Status_Id ".
					"left join Hreseller r on usq.Reseller_Id=r.Reseller_Id ";
				if($LReseller_Id!=1){
					$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$VispUserStatusList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Status.List'");
					$sql.=
						"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=u.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=u.Visp_Id and Hrp2.PermitItem_Id=$VispUserStatusList and Hrp2.ISPermit='Yes' ";
				}
				$sql.="where 1 ".$sqlfilter." $SortStr";
				function color_rows($row){
					$row->set_row_style("color:blue");
				}
				DSGridRender_Sql(100,$sql,"User_Id",$ColumnStr,"","","color_rows");
       break;
	case "Delete":
				DSDebug(1,"DSRep_User_StatusQueue_ListRender->Delete ******************************************");
				$User_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
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
