<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_NetLog_ReportingListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list",'SelectDate','DeleteAll','Delete'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_NetLogList_ListRender->List ********************************************");
				exitifnotpermit(0,"Report.NetLog.List.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("select MID(TABLE_NAME,5,8) from information_schema.TABLES where TABLE_SCHEMA='deltasib_netlog' and TABLE_NAME regexp '^nlog[0-9]{8}$' order by TABLE_NAME desc limit 1");
				
				if(strlen($RepDate)!=8)//return empty 
					DSGridRender_Sql(100,"SELECT  1 from (select 1) a where false",
						"NLog_Id",
						"NLog_Id,FirstDT,LastDT,Username,SrcAddr,SrcPort,DstAddr,DstPort,Proto",
						"","","");
				else{
					$Tablename='deltasib_netlog.nlog'.$RepDate;
					DSGridRender_Sql(100,"SELECT  NLog_Id,{$DT}DateTimeStr(FirstDT) As FirstDT,{$DT}DateTimeStr(LastDT) As LastDT,Username,INET_NtoA(SrcAddr) As SrcAddr,SrcPort,INET_NtoA(DstAddr) As DstAddr,DstPort,Proto ".
						"From $Tablename u_p join deltasib.Huser u on u_p.User_Id=u.User_Id ".
						"where 1 ".$sqlfilter." $SortStr ",
						"NLog_Id",
						"NLog_Id,FirstDT,LastDT,Username,SrcAddr,SrcPort,DstAddr,DstPort,Proto",
						"","","");
				}
				
       break;
    case "SelectDate":
				DSDebug(1,"DSUser_NetLogList_ListRender-> SelectDate *****************");
				exitifnotpermit(0,"Report.NetLog.List.List");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");

				$sql="select MID(TABLE_NAME,5,8) as Date_Id from information_schema.TABLES where TABLE_SCHEMA='deltasib_netlog' and TABLE_NAME regexp '^nlog[0-9]{8}$' order by TABLE_NAME desc";
				$options->render_sql($sql,"","Date_Id,Date_Id","","");				
				
        break;
	case "DeleteAll":
				DSDebug(1,"DSUser_NetLogList_ListRender DeleteAll ******************************************");
				exitifnotpermit(0,"Report.NetLog.List.List.DeleteAll");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_NetLog.NetLogsummary Order by NetLogSummary_Id desc limit 1");
				if(strlen($RepDate)==8){
					$Tablename='deltasib_NetLog.NetLog'.$RepDate;
					$ar=DBDelete("Delete From $Tablename Where 1 $sqlfilter");
				}
				else
					$ar=0;
				echo "OK~$ar~";
		break;
	case "Delete":
				DSDebug(1,"DSUser_NetLogList_ListRender Delete ******************************************");
				exitifnotpermit(0,"Report.NetLog.List.List.Delete");
				$Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_NetLog.NetLogsummary Order by NetLogSummary_Id desc limit 1");
				$Tablename='deltasib_NetLog.NetLog'.$RepDate;
				
				$ar=DBDelete("Delete From $Tablename Where Id=$Id"); 
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
