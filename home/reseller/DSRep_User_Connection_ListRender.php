<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_Connection_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.Connection.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list","WhoHaveIP","SelectDate"),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
	
				DSDebug(0,"DSRep_User_Connection_ListRender->List ********************************************");
				function color_rows($row){
					
					if($row->get_value("TerminateCause")=="DailySave"){
						if($row->get_value("ServiceInfo_Id")!=1)
							$Style="font-style: oblique;color:burlywood;";
						else
							$Style="font-style: oblique;color:chocolate;";
					}
					if(($row->get_value("TerminateCause")=="Stale-User-Delete")||($row->get_value("TerminateCause")=="Stale-User-Detect")){
						if($row->get_value("ServiceInfo_Id")!=1)
							$Style="color:indianred;";
						else
							$Style="color:red;";
					}
					elseif($row->get_value("ServiceInfo_Id")!=1)
						$Style="color:#557799";
					else
						$Style="";
					$row->set_row_style($Style);
				}

				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";

				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);

				
				$req=Get_Input('GET','DB','req','ARRAY',array("SaveToFile","ShowInGrid"),0,0,0);
				
				
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserConnectionList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Connection.List'");
				
				
				if(($RepDate=="Last30")||($RepDate=="Last20")||($RepDate=="Last10")||($RepDate=="Last5")){
					$Cnt=ltrim($RepDate,"Last");
					$TableArr=Array();
					$sql="select MID(TABLE_NAME,6,8) as TableDate from information_schema.TABLES where TABLE_SCHEMA='deltasib_conn' and TABLE_NAME regexp '^Hconn[0-9]{8}$' order by TABLE_NAME desc limit $Cnt";
					$n=CopyTableToArray($TableArr,$sql);
					DSDebug(0,"$n record returned");
					
					DBUpdate("set @Counter=0");
					
					$sqlArray=Array();
					
					for($i=0;$i<$n;++$i){
						$Tablename='deltasib_conn.Hconn'.$TableArr[$i]["TableDate"];
						$sqlArray[$i]="SELECT @Counter:=@Counter+1 as My_Id,Conn_Id,Username,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(AcctStopTime) As AcctStopTime, ".
						"SecondToR(TIMESTAMPDIFF(Second, AcctStartTime,AcctStopTime)) AS AcctSessionTime,ByteToR(SendTr) As SendTr,ByteToR(ReceiveTr) As ReceiveTr,CalledStationId,CallingStationId, ".
						"INET_NTOA(FramedIpAddress) as FramedIpAddress,INET_NTOA(NasIpAddress) As NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,Hc.ServiceInfo_Id ".
						"From $Tablename Hc join Huser Hu on Hu.User_Id=Hc.User_Id ".
						
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserConnectionList and Hrp2.ISPermit='Yes' ":"").
						
						"join Hserviceinfo s on s.ServiceInfo_Id=Hc.ServiceInfo_Id ".
						"where (TerminateCause<>'DailySave') $sqlfilter";
					}
					$sql=implode("\nunion\n",$sqlArray);
					$CountSql="select count(1) from ($sql) tmp";
					$sql.="\n".$SortStr;


					if($req=="SaveToFile"){
						$res = $conn->sql->query($sql);
						$data =  $conn->sql->get_next($res);
						$n=$conn->sql->get_affected_rows();
						if($n>20000){
							echo "<html><head><script type=\"text/javascript\">";
							echo "window.onload = function(){alert('$n records matched. SaveToFile is available for only 20000 record in this version. Please limit your filter to use save file!');window.close();}";
							echo "</script></head><body></body></html>";
							exit();
						}
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="ConnectionsReport.csv";');
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
						fclose($f);
					}
					else if($req=="ShowInGrid"){
						DSGridRender_Array(100,$sql,"My_Id","Conn_Id,Username,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol","","","color_rows",$CountSql);
					}
				}
				else{
					
					if(strlen($RepDate)!=8)
						$RepDate=DBSelectAsString("select MID(TABLE_NAME,6,8) from information_schema.TABLES where TABLE_SCHEMA='deltasib_conn' and TABLE_NAME regexp '^Hconn[0-9]{8}$' order by TABLE_NAME desc limit 1");
				
					if(strlen($RepDate)!=8){
						if($req=="ShowInGrid")
							DSGridRender_Sql(100,"SELECT  1 from (select 1) a where false",
								"Conn_Id",
								"Conn_Id,Username,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol",
								"","","");
						else{
							echo "<html><head><script type=\"text/javascript\">";
							echo "window.onload = function(){alert('No record mathed!');window.close();}";
							echo "</script></head><body></body></html>";
							exit();
						}
					}
					else{
						$Tablename='deltasib_conn.Hconn'.$RepDate;
						$sql="SELECT Conn_Id,Username,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(AcctStopTime) As AcctStopTime,SecondToR(TIMESTAMPDIFF(Second, AcctStartTime,AcctStopTime)) AS AcctSessionTime,ByteToR(SendTr) As SendTr,ByteToR(ReceiveTr) As ReceiveTr,CalledStationId,CallingStationId,INET_NTOA(FramedIpAddress) as FramedIpAddress,INET_NTOA(NasIpAddress) As NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,Hc.ServiceInfo_Id ".
						"From $Tablename Hc join Huser Hu on Hu.User_Id=Hc.User_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserConnectionList and Hrp2.ISPermit='Yes' ":"").
						"join Hserviceinfo s on s.ServiceInfo_Id=Hc.ServiceInfo_Id ".
						"where 1 ".$sqlfilter;
						$sql.=" $SortStr ";
						
						if($req=="SaveToFile"){
							$res = $conn->sql->query($sql);
							$data =  $conn->sql->get_next($res);
							$n=$conn->sql->get_affected_rows();
							if($n>20000){
								echo "<html><head><script type=\"text/javascript\">";
								echo "window.onload = function(){alert('$n records matched. SaveToFile is available for only 20000 record in this version. Please limit your filter to use save file!');window.close();}";
								echo "</script></head><body></body></html>";
								exit();
							}
							header('Content-Type: application/csv');
							header('Content-Disposition: attachment; charset=utf-8; filename="ConnectionsReport.csv";');
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
							fclose($f);
						}
						else if($req=="ShowInGrid"){
							
							DSGridRender_Sql(100,$sql,
								"Conn_Id",
								"Conn_Id,Username,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol",
								"","","color_rows");
						}						
					}
				}
       break;
	case "WhoHaveIP":
					$ReqIP=Get_Input('GET','DB','ReqIP','STR',7,15,0,0);
					$NReqIP=DBSelectAsString("select INET_ATON('$ReqIP')");
					$ReqDT=Get_Input('GET','DB','ReqDT','STR',0,100,0,0);
					$ReqDT=DBSelectAsString("select SHDATETIMESTRTOMSTR('$ReqDT')");
					
					$TableName="deltasib_conn.Hconn".DBSelectAsString("select Date_Format(Date('$ReqDT'),'%Y%m%d')");
					$IPList=Array();
					$sql="select 'Connection' as TName,Hu.User_Id as User_Id,Hu.Username as Username,Hc.Conn_Id as CId from ".
						"$TableName Hc join Huser Hu on Hc.User_Id=Hu.User_Id ".
						"where Hc.FramedIpAddress=$NReqIP and Hc.AcctStartTime<='$ReqDT' and Hc.AcctStopTime>='$ReqDT' ".
						"union all ".
						"select 'Online' as TName,Hu.User_Id as User_Id,Hu.Username as Username,Tor.Online_RadiusUser_Id as CId from ".
						"Tonline_radiususer Tor join Huser Hu on Tor.User_Id=Hu.User_Id ".
						"where Tor.FramedIpAddress=$NReqIP and Tor.AcctStartTime<='$ReqDT'";
					$n=CopyTableToArray($IPList,$sql);
					
					$OutArray=Array();
					for($i=0;$i<$n;++$i)
						array_Push($OutArray,$IPList[$i]['TName']."`".$IPList[$i]['User_Id']."`".$IPList[$i]['Username']."`".$IPList[$i]['CId']);
					
					echo "OK~$n~".implode("~",$OutArray);
					
		break;
    case "SelectDate":
				DSDebug(1,"DSUser_Connection_ListRender-> SelectDate *****************");

				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");

				$sql="select MID(TABLE_NAME,6,8) as Date_Id,{$DT}DATESTR(DATE_FORMAT(MID(TABLE_NAME,6,8),'%Y-%m-%d')) as Date_Label from information_schema.TABLES where TABLE_SCHEMA='deltasib_conn' and TABLE_NAME regexp '^Hconn[0-9]{8}$' order by TABLE_NAME asc";
				
				
				
				$Arr=Array();
				$n=CopyTableToArray($Arr,$sql);
				DSDebug(0,"n=$n");
				// array_push($Arr,Array("Date_Id"=>"Last30","Date_Label"=>"Last 30 Days"));
				// array_push($Arr,Array("Date_Id"=>"Last20","Date_Label"=>"Last 20 Days"));
				// array_push($Arr,Array("Date_Id"=>"Last10","Date_Label"=>"Last 10 Days"));
				// array_push($Arr,Array("Date_Id"=>"Last5","Date_Label"=>"Last 52 Days"));
				
				// $options->render_array($Arr,"","Date_Id,Date_Label","","");			
				echo '<optgroup style="direction:rtl;" label="روزانه">';
				for($i=0;$i<$n;++$i){
					echo '<option value="'.$Arr[$i]["Date_Id"].'">'.$Arr[$i]["Date_Label"].'</option>';
				}
				echo '</optgroup><optgroup label="ــــــــــــــــــــــــــــــ">';
				echo '<option style="direction:rtl;" value="Last30">۳۰ روز اخیر</option>';
				echo '<option style="direction:rtl;" value="Last20">۲۰ روز اخیر</option>';
				echo '<option style="direction:rtl;" value="Last10">۱۰ روز اخیر</option>';
				echo '<option style="direction:rtl;" value="Last5">&nbsp۵&nbsp روز اخیر</option>';
				echo '</optgroup>';
				
        break;		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
