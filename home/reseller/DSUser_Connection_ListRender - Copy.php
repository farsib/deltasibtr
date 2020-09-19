<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Connection_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list",'AddCallerId',"SelectDate"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Connection_ListRender->List ********************************************");
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
				
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Connection.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";

				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);

				
				if(($RepDate=="Last30")||($RepDate=="Last20")||($RepDate=="Last10")||($RepDate=="Last5")){
					$Cnt=ltrim($RepDate,"Last");
					$TableArr=Array();
					$sql="select MID(TABLE_NAME,6,8) as TableDate from information_schema.TABLES where TABLE_SCHEMA='deltasib_conn' and TABLE_NAME regexp '^Hconn[0-9]{8}$' order by TABLE_NAME desc limit $Cnt";
					$n=CopyTableToArray($TableArr,$sql);
					DSDebug(0,"$n record returned");
					
					$sqlArray=Array();
					
					DBUpdate("set @Counter=0");
					for($i=0;$i<$n;++$i){
						$Tablename='deltasib_conn.Hconn'.$TableArr[$i]["TableDate"];
						$sqlArray[$i]="SELECT @Counter:=@Counter+1 as My_Id,Conn_Id,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(AcctStopTime) As AcctStopTime, ".
						"SecondToR(TIMESTAMPDIFF(Second, AcctStartTime,AcctStopTime)) AS AcctSessionTime,ByteToR(SendTr) As SendTr,ByteToR(ReceiveTr) As ReceiveTr,CalledStationId,CallingStationId, ".
						"INET_NTOA(FramedIpAddress) as FramedIpAddress,INET_NTOA(NasIpAddress) As NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,".
						"ByteToR(ReturnTr) As ReturnTr,ByteToR(InternetTr) As InternetTr,ByteToR(IntranetTr) as IntranetTr,ByteToR(MessengerTr) as MessengerTr,ByteToR(FreeTr) as FreeTr,ByteToR(SpecialTr) as SpecialTr".
						" From $Tablename Hc join Hserviceinfo s on s.ServiceInfo_Id=Hc.ServiceInfo_Id ".
						"where (User_Id=$User_Id)and(TerminateCause<>'DailySave') $sqlfilter";
						$Tablename='deltasib_conn.Hconn'.$TableArr[$i]["TableDate"];
						$sqlArray[$i].="union SELECT @Counter:=@Counter+1 as My_Id,Conn_Id,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(AcctStopTime) As AcctStopTime, ".
						"SecondToR(TIMESTAMPDIFF(Second, AcctStartTime,AcctStopTime)) AS AcctSessionTime,ByteToR(SendTr) As SendTr,ByteToR(ReceiveTr) As ReceiveTr,CalledStationId,CallingStationId, ".
						"INET_NTOA(FramedIpAddress) as FramedIpAddress,INET_NTOA(NasIpAddress) As NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,".
						"ByteToR(ReturnTr) As ReturnTr,ByteToR(InternetTr) As InternetTr,ByteToR(IntranetTr) as IntranetTr,ByteToR(MessengerTr) as MessengerTr,ByteToR(FreeTr) as FreeTr,ByteToR(SpecialTr) as SpecialTr".
						" From deltasib_conn.Hconn Hc join Hserviceinfo s on s.ServiceInfo_Id=Hc.ServiceInfo_Id ".
						"where (User_Id=$User_Id)and(Date(AcctStartTime)='".$TableArr[$i]["TableDate"]."')and(TerminateCause<>'DailySave') $sqlfilter";
						
					}
					$sql=implode("\nunion\n",$sqlArray);
					$CountSql="select count(1) from ($sql) tmp";
					$sql.="\n".$SortStr;
					
					DSGridRender_Array(100,$sql,"My_Id","Conn_Id,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,ReturnTr,InternetTr,IntranetTr,MessengerTr,FreeTr,SpecialTr","","","color_rows",$CountSql);
					
				}
				else{
					if(strlen($RepDate)!=8)
						$RepDate=DBSelectAsString("select MID(TABLE_NAME,6,8) from information_schema.TABLES where TABLE_SCHEMA='deltasib_conn' and TABLE_NAME regexp '^Hconn[0-9]{8}$' order by TABLE_NAME desc limit 1");
				
					if(strlen($RepDate)!=8)
						$sql="SELECT  1 from (select 1) a where false";
					else{
						DBUpdate("set @Counter=0");
						$Tablename='deltasib_conn.Hconn';
						$sql="SELECT @Counter:=@Counter+1 as My_Id,Conn_Id,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(AcctStopTime) As AcctStopTime, ".
						"SecondToR(TIMESTAMPDIFF(Second, AcctStartTime,AcctStopTime)) AS AcctSessionTime,ByteToR(SendTr) As SendTr,ByteToR(ReceiveTr) As ReceiveTr,CalledStationId,CallingStationId, ".
						"INET_NTOA(FramedIpAddress) as FramedIpAddress,INET_NTOA(NasIpAddress) As NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,".
						"ByteToR(ReturnTr) As ReturnTr,ByteToR(InternetTr) As InternetTr,ByteToR(IntranetTr) as IntranetTr,ByteToR(MessengerTr) as MessengerTr,ByteToR(FreeTr) as FreeTr,ByteToR(SpecialTr) as SpecialTr ".
						" From $Tablename Hc join Hserviceinfo s on s.ServiceInfo_Id=Hc.ServiceInfo_Id ".
						"where (User_Id=$User_Id) And Date(AcctStartTime)='$RepDate' $sqlfilter union ";

						$Tablename='deltasib_conn.Hconn'.$RepDate;
						$sql.="SELECT @Counter:=@Counter+1 as My_Id,Conn_Id,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(AcctStopTime) As AcctStopTime, ".
						"SecondToR(TIMESTAMPDIFF(Second, AcctStartTime,AcctStopTime)) AS AcctSessionTime,ByteToR(SendTr) As SendTr,ByteToR(ReceiveTr) As ReceiveTr,CalledStationId,CallingStationId, ".
						"INET_NTOA(FramedIpAddress) as FramedIpAddress,INET_NTOA(NasIpAddress) As NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,".
						"ByteToR(ReturnTr) As ReturnTr,ByteToR(InternetTr) As InternetTr,ByteToR(IntranetTr) as IntranetTr,ByteToR(MessengerTr) as MessengerTr,ByteToR(FreeTr) as FreeTr,ByteToR(SpecialTr) as SpecialTr ".
						" From $Tablename Hc join Hserviceinfo s on s.ServiceInfo_Id=Hc.ServiceInfo_Id ".
						"where (User_Id=$User_Id) $sqlfilter ";
					$CountSql="select count(1) from ($sql) tmp";
					$sql.="\n".$SortStr;

					}
					DSGridRender_Array(100,$sql,"My_Id","Conn_Id,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,ReturnTr,InternetTr,IntranetTr,MessengerTr,FreeTr,SpecialTr","","","color_rows",$CountSql);
					
				}
				
       break;
	case "AddCallerId":
				DSDebug(1,"DSUser_Connection_ListRender AddCallerId ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Connection.AddCallerId");
				$NewRowInfo=array();
				$NewRowInfo['Conn_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				
				if(($RepDate=="Last30")||($RepDate=="Last20")||($RepDate=="Last10")||($RepDate=="Last5"))
					ExitError("گزارش تاریخ باید صریحا برای افزودن مک/آی پی تعریف شده باشد ");
				if(strlen($RepDate)!=8)
					ExitError("تاریخ نامعتبر داده شده");
				$Tablename='deltasib_conn.Hconn'.$RepDate;
				
				
				
				$ar=DBInsert("Insert into Huser_callerid(User_Id,CallerId) Select User_Id,CallingStationId From $Tablename Where Conn_Id='".$NewRowInfo['Conn_Id']."'");
				//logsecurity('Web',"IP $IP deleted from Web IP Block");
				echo "OK~";
		break;
    case "SelectDate":
				DSDebug(1,"DSUser_Connection_ListRender-> SelectDate *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Connection.List");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");

				$sql="select Date_Id,Date_Label from (select MID(TABLE_NAME,6,8) as Date_Id,{$DT}DATESTR(DATE_FORMAT(MID(TABLE_NAME,6,8),'%Y-%m-%d')) as Date_Label,TABLE_NAME from information_schema.TABLES where TABLE_SCHEMA='deltasib_conn' and TABLE_NAME regexp '^Hconn[0-9]{8}$' order by TABLE_NAME desc limit 60) a order by a.TABLE_NAME asc";
				$Arr=Array();
				$n=CopyTableToArray($Arr,$sql);
				DSDebug(0,"n=$n");
				// array_push($Arr,Array("Date_Id"=>"Last30","Date_Label"=>"Last 30 Days"));
				// array_push($Arr,Array("Date_Id"=>"Last20","Date_Label"=>"Last 20 Days"));
				// array_push($Arr,Array("Date_Id"=>"Last10","Date_Label"=>"Last 10 Days"));
				// array_push($Arr,Array("Date_Id"=>"Last5","Date_Label"=>"Last 5 Days"));
				
				// $options->render_array($Arr,"","Date_Id,Date_Label","","");	
				
				echo '<optgroup label="Daily">';
				for($i=0;$i<$n;++$i){
					echo '<option value="'.$Arr[$i]["Date_Id"].'">'.$Arr[$i]["Date_Label"].'</option>';
				}
				echo '</optgroup><optgroup label="ــــــــــــــــــــــــــــــ">';
				echo '<option value="Last30">Last 30 Days</option>';
				echo '<option value="Last20">Last 20 Days</option>';
				echo '<option value="Last10">Last 10 Days</option>';
				echo '<option value="Last5">Last 5 Days</option>';
				echo '</optgroup>';
				
        break;		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>