<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSHome_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete","SelectReceiver","AddTicket","RefreshClock"),0,0,0);
// sleep(5);
switch ($act) {
    case "list":
				DSDebug(0,"DSHome_ListRender->List ********************************************");

				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$sqlfilter=$p=str_replace("RemainDays","(DeadLine - DATEDIFF(now(),CDT))",$sqlfilter);
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$Req=Get_Input('GET','DB','Req','ARRAY',array("Inbox", "Sent", "Archive"),0,0,0);
				if($Req=="Inbox"){
					$WhereStr="Where (Receiver_Id='$LReseller_Id')and(TicketStatus<>'Confirmed')".$sqlfilter;
					DBUpdate("update Hticketing t left join Hreseller s on t.Sender_Id=s.Reseller_Id left join Hreseller r on t.Receiver_Id=r.Reseller_Id set TicketStatus='Seen' where (TicketStatus='Sent')and(Receiver_Id='$LReseller_Id')".$sqlfilter);
					$Notification="if(Receiver_NotficationCount>0,concat('<sup class=\'MyNotification\'>',Receiver_NotficationCount,'</sup>'),'')";
				}
				elseif($Req=="Sent"){
					$WhereStr="Where (Sender_Id='$LReseller_Id')and(TicketStatus<>'Confirmed')".$sqlfilter;
					$Notification="if(Sender_NotficationCount>0,concat('<sup class=\'MyNotification\'>',Sender_NotficationCount,'</sup>'),'')";
				}
				else{
					$WhereStr="Where ((Sender_Id='$LReseller_Id')or(Receiver_Id='$LReseller_Id'))and(TicketStatus='Confirmed')".$sqlfilter;
					$Notification="''";
				}
				
				DBUpdate("update Hticketing set TicketStatus='Expired' where ((TicketStatus='Sent') or (TicketStatus='Seen') or (TicketStatus='Open')) and (ADDDATE(DATE(CDT),DeadLine)<DATE(NOW()))");
				
				
				$sql="SELECT $Notification as Notification,Ticket_Id,TicketStatus,concat('<img src=\"/dsimgs/Priority',Priority,'.png\"/>') as Priority,TicketTitle,(DeadLine - DATEDIFF(now(),CDT)) as RemainDays,s.ResellerName as SenderName,r.ResellerName as ReceiverName,{$DT}DATETIMESTR(CDT) as CDT From Hticketing t ".
				"left join Hreseller s on t.Sender_Id=s.Reseller_Id ".
				"left join Hreseller r on t.Receiver_Id=r.Reseller_Id ".
				"$WhereStr $SortStr";
				
				$Columns="Notification,Ticket_Id,TicketStatus,Priority,TicketTitle,RemainDays,SenderName,ReceiverName,CDT";
				
				function AddNotificationHeader($Connector,$Output){
					if (isset($_GET["posStart"]))
						return;
					global $LReseller_Id;
					$Receiver_NotficationCount=DBSelectAsString("select sum(Receiver_NotficationCount) from Hticketing where Receiver_Id='$LReseller_Id'")*1;
					$Sender_NotficationCount=DBSelectAsString("select sum(Sender_NotficationCount) from Hticketing where Sender_Id='$LReseller_Id'")*1;
					
					$UserData="<userdata name='Receiver_NotficationCount'><![CDATA[$Receiver_NotficationCount]]></userdata>";
					$UserData.="<userdata name='Sender_NotficationCount'><![CDATA[$Sender_NotficationCount]]></userdata>";
					$Output->add($UserData);
				}
				function color_rows($row){
					$data = $row->get_value("TicketStatus");
					//Sent','Seen','Open','Abandoned','Expired','Done','Cancel','Confirme
					if(($data=='Abandoned'))
						$style="color:chocolate;";
					else if($data=='Expired')
						$style="color:red;";
					else if($data=='Done')
						$style="color:green;";
					else if($data=='Cancel')
						$style="color:Orange;";
					else if($data=='Confirmed')
						$style="color:SteelBlue;";
					else
						return;
					
					$row->set_row_style($style);
				}
				
				DSGridRender_Sql(100,$sql,"Ticket_Id",$Columns,"","","color_rows","AddNotificationHeader");
		break;
	case "RefreshClock":
				$TimeStamp=microtime(true)*1000;
				echo "OK~$TimeStamp~$CurrentDate~$LResellerName";
				
		break;
	case "Delete":
				DSDebug(1,"DSHome_ListRender Delete ******************************************");

				$Ticket_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				if($LReseller_Id!=1){
					$Sender_Id=DBSelectAsString("select Sender_Id from Hticketing where Ticket_Id='$Ticket_Id'");
					if($Sender_Id!=$LReseller_Id)
						ExitError("شما تنها می توانید تیکت خروجی خود را حذف کنید");
					$TicketStatus=DBSelectAsString("select TicketStatus from Hticketing where Ticket_Id='$Ticket_Id'");
					if($TicketStatus!='Sent')
						ExitError("شما تنها می توانید تیکت ارسالی خود را حذف کنید قبل از اینکه توسط گیرنده دیده شود");
				}

				$ar=DBDelete("delete from Hticketingaction Where Ticket_Id='$Ticket_Id'");
				$ar=DBDelete("delete from Hticketing Where Ticket_Id='$Ticket_Id'");
				
				echo "OK~";
		break;
	case "SelectReceiver":
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Reseller_Id,ResellerName From Hreseller where IsEnable='Yes' order by ResellerName Asc";
				$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
		break;
	case "AddTicket":
				DSDebug(1,"DSHome_ListRender AddTicket ******************************************");
				$TicketTitle=Get_Input('POST','DB','TicketTitle','STR',1,64,0,0);
				$Priority=Get_Input('POST','DB','Priority','INT',0,3,0,0);
				$Receiver_Id=Get_Input('POST','DB','Receiver_Id','INT',0,4294967295,0,0);
				$DeadLine=Get_Input('POST','DB','DeadLine','INT',0,4294967295,0,0);
				if($Receiver_Id==$LReseller_Id){
					$TicketStatus='Open';
					$Receiver_NotficationCount=0;
				}
				else{
					$TicketStatus='Sent';
					$Receiver_NotficationCount=1;
				}
				$sql="insert into Hticketing set "
					."Sender_Id='$LReseller_Id'"
					.",CDT=Now()"
					.",TicketStatus='$TicketStatus'"
					.",TicketTitle='$TicketTitle'"
					.",Priority='$Priority'"
					.",Receiver_Id='$Receiver_Id'"
					.",Receiver_NotficationCount=$Receiver_NotficationCount"
					.",DeadLine='$DeadLine'";
				$RowId=DBInsert($sql);
				echo "OK~$RowId~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>