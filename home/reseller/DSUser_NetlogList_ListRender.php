<?php
try{
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSRep_NetLog_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete","ExportDownload","ExportRowCount"),0,0,0);

switch ($act) {
    case "ExportRowCount":
				DSDebug(0,"DSRep_NetLog_ListRender->ExportRowCount ********************************************");
				exitifnotpermit(0,"Report.NetLog.List.Export");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$Identication=DBSelectAsString("Select Username From Huser Where User_Id='$User_Id'");
//				$Identication=Get_Input('GET','DB','Identication','STR',0,32,0,0);
				$DateFrom=Get_Input('GET','DB','DateFrom','DateOrBlank',0,0,0,0);
				$DateTo=Get_Input('GET','DB','DateTo','DateOrBlank',0,0,0,0);
				$MACAddress=Get_Input('GET','DB','MACAddress','STROrBlank',12,12,0,0);
				$SrcIP=Get_Input('GET','DB','SrcIP','IP',0,0,0,0);
				$SrcPort=Get_Input('GET','DB','SrcPort','INTOrBlank',0,64000,0,0);
				$NatIP=Get_Input('GET','DB','NatIP','IP',0,0,0,0);
				$NatPort=Get_Input('GET','DB','NatPort','INTOrBlank',0,64000,0,0);
				$DstIP=Get_Input('GET','DB','DstIP','IP',0,0,0,0);
				$DstPort=Get_Input('GET','DB','DstPort','INTOrBlank',0,64000,0,0);
				$DstDomain=Get_Input('GET','DB','DstDomain','STR',0,100,0,0);
				$ExtraFilter="(Identication='$Identication')";
				//if($Identication!='') $ExtraFilter.=" And (Identication='$Identication')";
				if($DateFrom!='') $ExtraFilter.=" And (StartDate>=Date('$DateFrom'))";
				if($DateTo!='') $ExtraFilter.="And (StartDate<=AddDate('$DateTo',INTERVAL 1 DAY))";
				if($MACAddress!='') $ExtraFilter.="And (MACAddress=CONV('$MACAddress',16,10))";
				if($SrcIP!='') $ExtraFilter.="And (SrcIP=INET_ATON('$SrcIP'))";
				if($SrcPort!='') $ExtraFilter.="And (SrcPort='$SrcPort')";
				if($NatIP!='') $ExtraFilter.="And (NatIP=INET_ATON('$NatIP'))";
				if($NatPort!='') $ExtraFilter.="And (NatPort='$NatPort')";
				if($DstIP!='') $ExtraFilter.="And (DstIP=INET_ATON('$DstIP'))";
				if($DstPort!='') $ExtraFilter.="And (DstPort='$DstPort')";
				if($DstDomain!='') $ExtraFilter.=" And (DstDomain like '$DstDomain')";
				
				
				$tmpfilename=GenerateRandomString(10);
				$ExportFile_Id=DBInsert("Insert deltasib_netlog.Hexportfile Set CreateDT=Now(),Name=''");
				$ExportFilename='__dsfile__Netlog_Export__'.$ExportFile_Id.'_'.$tmpfilename;
				DBUpdate("Update deltasib_netlog.Hexportfile Set FileName='$ExportFilename'");
				$Query="SELECT count(1) From deltasib_netlog.nlog where ".$ExtraFilter;
				$n=DBSelectAsString($Query);
				DSDebug(0,"~Found $n records");
				echo "OK~$n";
		break;
	case "ExportDownload":

				DSDebug(0,"DSRep_NetLog_ListRender->ExportDownload ********************************************");
				exitifnotpermit(0,"Report.NetLog.List.Export");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$Identication=DBSelectAsString("Select Username From Huser Where User_Id='$User_Id'");
//				$Identication=Get_Input('GET','DB','Identication','STR',0,32,0,0);
				$DateFrom=Get_Input('GET','DB','DateFrom','DateOrBlank',0,0,0,0);
				$DateTo=Get_Input('GET','DB','DateTo','DateOrBlank',0,0,0,0);
				$MACAddress=Get_Input('GET','DB','MACAddress','STROrBlank',12,12,0,0);
				$SrcIP=Get_Input('GET','DB','SrcIP','IP',0,0,0,0);
				$SrcPort=Get_Input('GET','DB','SrcPort','INTOrBlank',0,64000,0,0);
				$NatIP=Get_Input('GET','DB','NatIP','IP',0,0,0,0);
				$NatPort=Get_Input('GET','DB','NatPort','INTOrBlank',0,64000,0,0);
				$DstIP=Get_Input('GET','DB','DstIP','IP',0,0,0,0);
				$DstPort=Get_Input('GET','DB','DstPort','INTOrBlank',0,64000,0,0);
				$DstDomain=Get_Input('GET','DB','DstDomain','STR',0,100,0,0);
				$ExtraFilter="(Identication='$Identication')";
				if($Identication!='') $ExtraFilter.=" And (Identication='$Identication')";
				if($DateFrom!='') $ExtraFilter.=" And (StartDate>=Date('$DateFrom'))";
				if($DateTo!='') $ExtraFilter.="And (StartDate<=AddDate('$DateTo',INTERVAL 1 DAY))";
				if($MACAddress!='') $ExtraFilter.="And (MACAddress=CONV('$MACAddress',16,10))";
				if($SrcIP!='') $ExtraFilter.="And (SrcIP=INET_ATON('$SrcIP'))";
				if($SrcPort!='') $ExtraFilter.="And (SrcPort='$SrcPort')";
				if($NatIP!='') $ExtraFilter.="And (NatIP=INET_ATON('$NatIP'))";
				if($NatPort!='') $ExtraFilter.="And (NatPort='$NatPort')";
				if($DstIP!='') $ExtraFilter.="And (DstIP=INET_ATON('$DstIP'))";
				if($DstPort!='') $ExtraFilter.="And (DstPort='$DstPort')";
				if($DstDomain!='') $ExtraFilter.=" And (DstDomain like '$DstDomain')";
				$sql="SELECT Id,Identication,CONV(MACAddress,10,16) as MACAddress,concat({$DT}DateStr(StartDate),' ',StartTime) As StartDT,ProtocolNumber,INET_NTOA(SrcIP)as SrcIP,SrcPort,INET_NTOA(NatIP) as NatIP,NatPort,INET_NTOA(DstIP) as DstIP,DstDomain,DstPort,Transfer From deltasib_netlog.nlog where ".$ExtraFilter;
				//DEBUG("sql=$sql");
				
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename="UserReport.csv";');
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
				exit;
		break;
    case "list":
				DSDebug(0,"DSRep_NetLog_ListRender->List ********************************************");
				exitifnotpermit(0,"Report.NetLog.List.List");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$Identication=DBSelectAsString("Select Username From Huser Where User_Id='$User_Id'");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				//if($sqlfilter=='') $sqlfilter=' And startdt>Date(Now())';
				//$Identication=Get_Input('GET','DB','Identication','STR',0,32,0,0);
				$DateFrom=Get_Input('GET','DB','DateFrom','DateOrBlank',0,0,0,0);
				$DateTo=Get_Input('GET','DB','DateTo','DateOrBlank',0,0,0,0);
				$MACAddress=Get_Input('GET','DB','MACAddress','STROrBlank',12,12,0,0);
				$SrcIP=Get_Input('GET','DB','SrcIP','IP',0,0,0,0);
				$SrcPort=Get_Input('GET','DB','SrcPort','INTOrBlank',0,64000,0,0);
				$NatIP=Get_Input('GET','DB','NatIP','IP',0,0,0,0);
				$NatPort=Get_Input('GET','DB','NatPort','INTOrBlank',0,64000,0,0);
				$DstIP=Get_Input('GET','DB','DstIP','IP',0,0,0,0);
				$DstPort=Get_Input('GET','DB','DstPort','INTOrBlank',0,64000,0,0);
				$DstDomain=Get_Input('GET','DB','DstDomain','STR',0,100,0,0);
				$Export=Get_Input('GET','DB','Export','ARRAY',array("Yes","No",""),0,0,0);
				$ExtraFilter="(Identication='$Identication')";
				if($Identication!='') $ExtraFilter.=" And (Identication='$Identication')";
				if($DateFrom!='') $ExtraFilter.=" And (StartDate>=Date('$DateFrom'))";
				if($DateTo!='') $ExtraFilter.="And (StartDate<=AddDate('$DateTo',INTERVAL 1 DAY))";
				if($MACAddress!='') $ExtraFilter.="And (MACAddress=CONV('$MACAddress',16,10))";
				if($SrcIP!='') $ExtraFilter.="And (SrcIP=INET_ATON('$SrcIP'))";
				if($SrcPort!='') $ExtraFilter.="And (SrcPort='$SrcPort')";
				if($NatIP!='') $ExtraFilter.="And (NatIP=INET_ATON('$NatIP'))";
				if($NatPort!='') $ExtraFilter.="And (NatPort='$NatPort')";
				if($DstIP!='') $ExtraFilter.="And (DstIP=INET_ATON('$DstIP'))";
				if($DstPort!='') $ExtraFilter.="And (DstPort='$DstPort')";
				if($DstDomain!='') $ExtraFilter.=" And (DstDomain like '$DstDomain')";

				DSDebug(1,"ExtraFilter=$ExtraFilter");
				DSGridRender_Sql(100,
					"SELECT Id,Identication,CONV(MACAddress,10,16) as MACAddress,concat({$DT}DateStr(StartDate),' ',StartTime) As StartDT,ProtocolNumber,INET_NTOA(SrcIP)as SrcIP,SrcPort,INET_NTOA(NatIP) as NatIP,NatPort,INET_NTOA(DstIP) as DstIP,DstDomain,DstPort,Transfer From deltasib_netlog.nlog where ".$ExtraFilter." $SortStr ",
					"Id","Id,Identication,MACAddress,StartDT,ProtocolNumber,SrcIP,SrcPort,NatIP,NatPort,DstIP,DstDomain,DstPort,Transfer",
					"","","");
       break;
	case "Delete":
				exit;
				DSDebug(1,"DSRep_NetLog_ListRender Delete ******************************************");
				exitifnotpermit(0,"Report.NetLog.List.List.Delete");
				$NewRowInfo=array();
				$NewRowInfo['NetworkIP_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);

				$ar=DBDelete('delete from Hnetworkip Where NetworkIP_Id='.$NewRowInfo['NetworkIP_Id']);
				logdbdelete($NewRowInfo,'Delete','NetworkIP',$NewRowInfo['NetworkIP_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>