<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSHome_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list","insert","AddFile","UndoLastAction","DownloadFile"),0,0,0);

try {
switch ($act) {
	case "list":
				DSDebug(1,"DSHome_EditRender.php list ********************************************");
				$Ticket_Id=Get_Input('GET','DB','Ticket_Id','INT',1,4294967295,0,0);
				
				$sql="select count(1) from Hticketing where Ticket_Id='$Ticket_Id' and (Sender_Id='$LReseller_Id')or(Receiver_Id='$LReseller_Id')";
				$Own=DBSelectAsString($sql);
				if($Own<=0)
					ExitError("اطلاعات تیکت یافت نشد و یا مجاز نیست");

				
				$sql="Update Hticketing set ".
					"TicketStatus=if(((TicketStatus='Sent') or (TicketStatus='Seen') or (TicketStatus='Open')) and (ADDDATE(DATE(CDT),DeadLine)<DATE(NOW())),'Expired',if(Receiver_Id='$LReseller_Id' and (TicketStatus='Sent' or TicketStatus='Seen'),'Open',TicketStatus))".
					",Sender_NotficationCount=if(Sender_Id='$LReseller_Id',0,Sender_NotficationCount)".
					",Receiver_NotficationCount=if(Receiver_Id='$LReseller_Id',0,Receiver_NotficationCount) where Ticket_Id='$Ticket_Id'";
				DBUpdate($sql);
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				function AddTicketHeader($Connector,$Output){
					if (isset($_GET["posStart"]))
						return;
					global $Ticket_Id,$DT;
					$sql="SELECT TicketTitle,TicketStatus,s.ResellerName as Sender,r.ResellerName as Receiver,Priority,{$DT}DATETIMESTR(CDT) as CDT,(DeadLine - DATEDIFF(now(),CDT)) as RemainDays,{$DT}DATESTR(ADDDATE(DATE(CDT),DeadLine)) as DeadLineDate".
					" from Hticketing t left join Hreseller r on t.Receiver_Id=r.Reseller_Id left join Hreseller s on t.Sender_Id=s.Reseller_Id where Ticket_Id='$Ticket_Id'";
					$Arr=Array();
					$n=CopyTableToArray($Arr,$sql);
					$UserData="";
					foreach($Arr[0] as $Key=>$Value)
						$UserData.="<userdata name='$Key'><![CDATA[$Value]]></userdata>";
					$Output->add($UserData);
				}
				function color_rows($row){
					$data = $row->get_value("Action");
					//'Info','File','ExtendTime','TimeRequest','Abandon','Done','Cancel','ReOpen','Confirm'
					if(($data=='Abandon'))
						$style="color:chocolate;";
					else if($data=='TimeRequest')
						$style="color:red;";
					else if($data=='ExtendTime')
						$style="color:blue;";
					else if($data=='ReOpen')
						$style="color:Tomato;";
					else if($data=='Done')
						$style="color:green;";
					else if($data=='Cancel')
						$style="color:Orange;";
					else if($data=='Confirm')
						$style="color:SteelBlue;";
					else
						return;
					
					$row->set_row_style($style);					
				}
				
				$LastNotEditable_Id=DBSelectAsString("select Max(TicketingAction_Id) from Hticketingaction where Ticket_Id='$Ticket_Id' and(Creator_Id<>'$LReseller_Id' or timestampdiff(Second,CDT,now())>600)");
				
				DSGridRender_Sql(100,"Select TicketingAction_Id,ResellerName,{$DT}DATETIMESTR(CDT) as CDT,Action,Comment,if(TicketingAction_Id<='$LastNotEditable_Id','-',SecondToR(600-timestampdiff(Second,CDT,now()))) as CanUndone from Hticketingaction t left join Hreseller r on t.Creator_Id=r.Reseller_Id where Ticket_Id='$Ticket_Id' $SortStr","TicketingAction_Id","TicketingAction_Id,ResellerName,CDT,Action,Comment,CanUndone","","","color_rows","AddTicketHeader");
		break;
    case "insert": 
				
				$Ticket_Id=Get_Input('POST','DB','Ticket_Id','INT',1,4294967295,0,0);
				$sql="select if(Sender_Id='$LReseller_Id' and Receiver_Id='$LReseller_Id','BiDirect',if(Receiver_Id='$LReseller_Id','Incoming',if(Sender_Id='$LReseller_Id','Outgoing','NotOwn'))) from Hticketing where Ticket_Id='$Ticket_Id'";
				$TicketDirection=DBSelectAsString($sql);
				
				if($TicketDirection=='')
					ExitError("اطلاعات تیکت یافت نشد");
				if($TicketDirection=='NotOwn')
					ExitError("شما مجوز دسترسی به این تیکت را ندارید");
				
				$sql="Select TicketStatus from Hticketing where Ticket_Id='$Ticket_Id'";
				$TicketStatus=DBSelectAsString($sql);
				
				$Action=Get_Input('POST','DB','Action','ARRAY',array("Info","Abandon","Done","ReOpen","Cancel","TimeRequest","ExtendTime","Confirm"),0,0,0);
				
				if($TicketDirection=="Outgoing"){
					$NotificationFieldPlus="Receiver_NotficationCount=Receiver_NotficationCount+1";
					if(($Action=='Abandon')||($Action=='Done')||($Action=='TimeRequest')){
						ExitError("Can not do ".strtolower($Action)." for outgoing ticket");
					}
					elseif($Action=='Info'){
						if($TicketStatus=='Confirmed')
							ExitError("نمی توان به تیکت خروجی با وضعیت تایید شده اطلاعات اضافه کرد");
					}					
					elseif($Action=='ReOpen'){
						if(($TicketStatus=="Sent")||($TicketStatus=="Seen")||($TicketStatus=="Open"))
							ExitError("تیکت پیش از این باز بوده");
						elseif($TicketStatus=="Expired")
							ExitError("تیکت منقضی شده است.به آن افزایش مهلت دهید تا مجدد در وضعیت باز قرار بگیرد");
						elseif($TicketStatus=='Confirmed')
							ExitError("نمی توان تیکت خروجی با وضعیت تایید شده را مجدد باز کرد");
					}
					elseif($Action=='Cancel'){
						if($TicketStatus=="Cancel")
							ExitError("تیکت پیش از این لغو شده");
						elseif(($TicketStatus=="Abandoned")||($TicketStatus=="Done")||($TicketStatus=='Confirmed'))
							ExitError("نمی توان تیکت خروجی با وضعیت های رهاشده/انجام شده/تایید شده را لغو کرد");
					}
					elseif($Action=='ExtendTime'){
						if(($TicketStatus=="Abandoned")||($TicketStatus=="Cancel")||($TicketStatus=="Done")||($TicketStatus=='Confirmed'))
							ExitError("نمیتوان به تیکت خروجی با وضعیت های رهاشده/انجام شده/تایید شده افزایش مهلت داد");
					}
					elseif($Action=='Confirm'){
						if(($TicketStatus=="Sent")||($TicketStatus=="Seen")||($TicketStatus=="Open")||($TicketStatus=="Expired"))
							ExitError("تیکت خروچی که وضعیت ارسال/دیدن/باز/منقضی قرار دارد را نمی توان تایید کرد");
						elseif($TicketStatus=='Confirmed')
							ExitError("تیکت پیش از این تایید شده است");
					}
				}
				else if($TicketDirection=="Incoming"){
					$NotificationFieldPlus="Sender_NotficationCount=Sender_NotficationCount+1";
					if(($Action=='Cancel')||($Action=='ExtendTime')||($Action=='Confirm')){
						ExitError("Can not do ".strtolower($Action)." for incoming ticket");
					}
					elseif($Action=='Info'){
						if(($TicketStatus=="Cancel")||($TicketStatus=="Abandoned")||($TicketStatus=="Done")||($TicketStatus=='Confirmed'))
							ExitError("به تیکت ورودی که در وضعیت لغو/رها شده/انجام شده/تایید شده قرار دارد نمی توان اطلاعات اضافه کرد");
					}
					elseif($Action=='Abandon'){
						if($TicketStatus=="Abandoned")
							ExitError("تیکت پیش از این رها شده است");
						elseif(($TicketStatus=="Cancel")||($TicketStatus=="Done")||($TicketStatus=='Confirmed'))
							ExitError("تیکت ورودی که در وضعیت لغو/انجام شده/تاییده شده دارد را نمی توان رها کرد");
					}
					elseif($Action=='Done'){
						if($TicketStatus=="Done")
							ExitError("تیکت پیش از این انجام شده است");
						elseif(($TicketStatus=="Cancel")||($TicketStatus=='Confirmed')||($TicketStatus=="Abandoned"))
							ExitError("تیکت ورودی که در وضعیت لغو/رها شده/تایید شده قرار دارد را نمی توان به انجام  شده تغییر داد");
					}
					elseif($Action=='ReOpen'){
						if($TicketStatus=="Open")
							ExitError("تیکت پیش از این باز شده است");
						elseif(($TicketStatus=="Expired")||($TicketStatus=="Cancel")||($TicketStatus=='Confirmed'))
							ExitError("Can not re open ".strtolower($TicketStatus)." incomming ticket");
							
					}
					elseif($Action=='TimeRequest'){
						if(($TicketStatus=="Cancel")||($TicketStatus=='Confirmed')||($TicketStatus=="Abandoned")||($TicketStatus=="Done"))
							ExitError("برای نیکت ورودی در وضعیت لغو/رها شده/انجام شده/تایید شده نمی توان درخواست زمان داد");
					}
				}else if($TicketDirection=="BiDirect"){
					$NotificationField='';
					if(($Action=='Abandon')||($Action=='Cancel')||($Action=='TimeRequest')){
						ExitError("Can not do ".strtolower($Action)." for bidirectional ticket");
					}
					elseif($Action=='Info'){
						if($TicketStatus=='Confirmed')
							ExitError("به تیکت دوطرفه در وضعیت تایید شده نمی توان اطلاعات اضافه کرد");
					}
					elseif($Action=='Done'){
						if($TicketStatus=="Done")
							ExitError("تیکت پیش از این انجام شده است");
							
						elseif($TicketStatus=='Confirmed')
							ExitError("تیکت دوطرفه در وضعیت تایید شده را نمی توان به انجام شده تغییر داد");
						
					}
					elseif($Action=='ReOpen'){
						if($TicketStatus=="Open")
							ExitError("تیکت پیش از این باز شده است");
						
						elseif($TicketStatus=="Expired")
							ExitError("تیکت منقضی شده است،برای بازکردن مجدد آن  از افزایش مهلت استفاده کنید");
						
						elseif($TicketStatus=='Confirmed')
							ExitError("تیکت دوطرفه ی تایید شده را نمی توان مجدد باز کرد");
					}
					elseif($Action=='ExtendTime'){
						if(($TicketStatus=="Done")||($TicketStatus=='Confirmed'))
							ExitError("برای تیکت دوطرفه در وضعیت انجام شده/تایید شده نمی توان زمان را افزایش داد");
					}
					elseif($Action=='Confirm'){
						if(($TicketStatus=="Open")||($TicketStatus=="Expired"))
							ExitError("تیکت دوطرفه در وضعیت ارسال/دیدن/باز/منقضی را نمی توان تایید کرد");
							
						elseif($TicketStatus=='Confirmed')
							ExitError("تیکت پیش از این تایید شده است");
					}
				}
				
				
				$Comment=Get_Input('POST','DB','Comment','STR',0,900,0,0);
				$Param="";
				switch($Action){
					case "Info":				
							if($NotificationFieldPlus!='')
								$PostSql="update Hticketing set $NotificationFieldPlus where Ticket_Id='$Ticket_Id'";
							else
								$PostSql="";
						break;
					case "Abandon":
							$Param=$TicketStatus;
							$PostSql="update Hticketing set TicketStatus='Abandoned' where Ticket_Id='$Ticket_Id'";
						break;
					case "Done":
							$Param=$TicketStatus;
							$PostSql="update Hticketing set TicketStatus='Done' where Ticket_Id='$Ticket_Id'";
						break;
					case "ReOpen":
							$Param=$TicketStatus;
							$PostSql="update Hticketing set TicketStatus='Open' where Ticket_Id='$Ticket_Id'";
						break;
					case "Cancel":
							$Param=$TicketStatus;
							$PostSql="update Hticketing set TicketStatus='Cancel' where Ticket_Id='$Ticket_Id'";
						break;
					case "TimeRequest":
							$Days=Get_Input('POST','DB','Days','INT',1,99999,0,0);
							$NewDate=DBSelectAsString("Select {$DT}datestr(Date(CDT)+interval DeadLine+$Days day) from Hticketing where Ticket_Id='$Ticket_Id'");
							if($Comment<>'')
								$Comment.="\n***\n";
							if($Days>1)
								$Comment.="$LResellerName has requested you to extend deadline of ticket to $Days days more(Until [$NewDate])";
							else
								$Comment.="$LResellerName has requested you to extend deadline of ticket to $Days day more(Until [$NewDate])";
							
							$Param=$Days;

							if($NotificationFieldPlus!='')
								$PostSql="update Hticketing set $NotificationFieldPlus where Ticket_Id='$Ticket_Id'";
							else
								$PostSql="";
						break;
					case "ExtendTime":
							$Days=Get_Input('POST','DB','Days','INT',1,99999,0,0);
							$IsNewDateUnfair=DBSelectAsString("select Date(CDT)+interval DeadLine+$Days day<date(now()) from Hticketing where Ticket_Id='$Ticket_Id'");
							if($IsNewDateUnfair)
								ExitError("مهلت جدید کارایی ندارد زیرا پس از افزودن،تیکت در حالت منقضی باقی خواهد ماند ");
							$NewDate=DBSelectAsString("Select {$DT}datestr(Date(CDT)+interval DeadLine+$Days day) from Hticketing where Ticket_Id='$Ticket_Id'");
							if($Comment<>'')
								$Comment.="\n***\n";
							if($Days>1)
								$Comment.="نماینده : $LResellerName مهلت تیکت را $Days روز افزایش داد(تا [$NewDate])";
							else
								$Comment.="نماینده : $LResellerName مهلت تیکت را $Days روز افزایش داد(تا [$NewDate])";

							$Param=$Days;
							
							$PostSql="update Hticketing set DeadLine=DeadLine+$Days,TicketStatus=if(TicketStatus='Expired','Open',TicketStatus) where Ticket_Id='$Ticket_Id'";
						break;
					case "Confirm":
							$Param=$TicketStatus;
							$PostSql="update Hticketing set TicketStatus='Confirmed' where Ticket_Id='$Ticket_Id'";
						break;
					default :
						ExitError("عمل ناشناخته");
				}
				
				$sql="insert Hticketingaction set ".
					"Ticket_Id='$Ticket_Id'".
					",Creator_Id='$LReseller_Id'".
					",CDT=now()".
					",Action='$Action'".
					",Comment='$Comment'".
					",Param='$Param'";
				$RowId=DBInsert($sql);
				
				if($PostSql!="")
					DBUpdate($PostSql);
				
				echo "OK~$RowId~";
        break;
	case "UndoLastAction":
	
				DSDebug(1,"DSHome_EditRender.php UndoLastAction ********************************************");
				$Ticket_Id=Get_Input('GET','DB','Ticket_Id','INT',1,4294967295,0,0);
				
				$LastTicketingAction_Id=DBSelectAsString("select Max(TicketingAction_Id) from Hticketingaction where Ticket_Id='$Ticket_Id'");
				
				$sql="select Creator_Id,timestampdiff(Second,CDT,now()) as TimeDiff,Action,Comment,Param from Hticketingaction where TicketingAction_Id='$LastTicketingAction_Id'";
				$LastActionArr=Array();
				$n=CopyTableToArray($LastActionArr,$sql);
				DSDebug(1,print_r($LastActionArr,true));
				$LastCreator_Id=$LastActionArr[0]["Creator_Id"];
				if($LastCreator_Id=='')
					ExitError("بازگردانی موجود نیست");
				if($LastCreator_Id!=$LReseller_Id)
					ExitError("آخرین عمل ،عمل شما نیست");
				
				$TimeDiff=$LastActionArr[0]["TimeDiff"];
				if($TimeDiff>600)
					ExitError("بازگردانی تنها برای ۱۰ دقیقه در دسترس است");
				$Action=$LastActionArr[0]["Action"];
				$Comment=$LastActionArr[0]["Comment"];
				$Param=$LastActionArr[0]["Param"];
				
				if($Action=="ExtendTime"){
					DBUpdate("update Hticketing set DeadLine=DeadLine-$Param where Ticket_Id='$Ticket_Id'");
				}
				elseif(($Action=="Abandon")||($Action=="Done")||($Action=="Cancel")||($Action=="ReOpen")||($Action=="Confirm")){
					DBUpdate("update Hticketing set TicketStatus='$Param' where Ticket_Id='$Ticket_Id'");
				}
				DBUpdate("delete from Hticketingaction where TicketingAction_Id='$LastTicketingAction_Id'");
				echo "OK~";
			//'Info','File','ExtendTime','TimeRequest','Abandon','Done','Cancel','ReOpen','Confirm
		break;
	case "AddFile":
				
				
				DSDebug(1,"DSHome_EditRender.php AddFile ********************************************");
				$Ticket_Id=Get_Input('GET','DB','Ticket_Id','INT',1,4294967295,0,0);
				$sql="select if(Sender_Id='$LReseller_Id' and Receiver_Id='$LReseller_Id','BiDirect',if(Receiver_Id='$LReseller_Id','Incoming',if(Sender_Id='$LReseller_Id','Outgoing','NotOwn'))) from Hticketing where Ticket_Id='$Ticket_Id'";
				$TicketDirection=DBSelectAsString($sql);
				
				if($TicketDirection=='')
					ExitError("اطلاعات تیکت یافت نشد");
				if($TicketDirection=='NotOwn')
					ExitError("شما دسترسی به این تیکت را ندارید");
				
				$sql="Select TicketStatus from Hticketing where Ticket_Id='$Ticket_Id'";
				$TicketStatus=DBSelectAsString($sql);
				
				if($TicketDirection=="Outgoing"){
					$NotificationFieldPlus="Receiver_NotficationCount=Receiver_NotficationCount+1";
					if($TicketStatus=='Confirmed')
						ExitError("برای تیکت خروجی در وضعیت تایید شده نمی توان فایل ارسال کرد");
				}
				elseif($TicketDirection=="Incoming"){
					$NotificationFieldPlus="Sender_NotficationCount=Sender_NotficationCount+1";
					if(($TicketStatus=="Cancel")||($TicketStatus=="Abandoned")||($TicketStatus=="Done")||($TicketStatus=='Confirmed'))
						ExitError("برای تیکت ورودی در وضعیت لغو/رها شده/انجام شده/تایید شده نمی توان فایل ارسال کرد");
				}
				else{
					$NotificationFieldPlus="";
					if($TicketStatus=='Confirmed')
						ExitError("برای تیکت دوطرفه در وضعیت تایید شده نمی توان فایل ارسال کرد");
				}
				
				DSDebug(1,"Request File Upload :\n".DSPrintArray($_FILES));
				
				if (@$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
					if(isset($_FILES["file"])){
						$filename =DSescape($_FILES["file"]["name"]);
						$RealFilename=DSescape($_FILES["file"]["name"]);
						$Size=DSescape($_FILES["file"]["size"]);

						$tmpfilename=GenerateRandomString(10);
						$sql="Insert Hticketingaction Set ".
							"Creator_Id='$LReseller_Id'".
							",Ticket_Id='$Ticket_Id'".
							",CDT=Now()".
							",Action='File'".
							",Comment=concat('Filename=[$RealFilename],Size=[',ByteToR('$Size'),']')".
							",Param='$tmpfilename'";
						$TicketingAction_Id=DBInsert($sql);
						$ServerFileName='__dsfile__Ticket_'.$TicketingAction_Id.'_'.$tmpfilename;
						DSDebug(1,' check file_exists /payamavaran/www/deltasib/attachment/'.$ServerFileName);
						if(move_uploaded_file($_FILES["file"]["tmp_name"],"/tmp/".$ServerFileName)){
							$reply=runshellcommand("php","DSUploadFile","","");
							// $reply=file_get_contents('http://127.0.0.1:99/upload');
							if($NotificationFieldPlus!='')
								DBUpdate("update Hticketing set $NotificationFieldPlus where Ticket_Id='$Ticket_Id'");
							print_r("{state: true, name:'".str_replace("'","\\'",$ServerFileName)."', extra:UploadOK($TicketingAction_Id)}");
						}
						else{
							DBUpdate("delete from Hticketingaction where TicketingAction_Id='$TicketingAction_Id'");
							print_r("{state: false, extra:dhtmlx.alert({text:'Upload failed.\\nCheck file size limit(".min(ini_get("upload_max_filesize"),ini_get("post_max_size")).").',type:'alert-error',title:'Error'})}");
						}
					}
					else
						print_r("{state: false, extra:dhtmlx.alert({text:'Upload failed. Bad request.',type:'alert-error'},title:'Error')}");
				}
		break;
	case "DownloadFile":

				function ShowError($St){
					echo "<html><head><script type=\"text/javascript\">";
					echo "window.onload = function(){alert('$St');window.close();}";
					echo "</script></head><body></body></html>";
					exit();
				}
	
				DSDebug(1,"DSHome_EditRender.php DownloadFile ********************************************");
				$TicketingAction_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$FileInfo=Array();
				$n=CopyTableToArray($FileInfo,"select Param,Comment from Hticketingaction where TicketingAction_Id='$TicketingAction_Id'");
				if($n<>1)
					ShowError("File information not found");
				
				DSDebug(3,print_r($FileInfo,true));
				$Param=$FileInfo[0]["Param"];
				$Comment=$FileInfo[0]["Comment"];//Filename=[$RealFilename],Size=[',ByteToR('$Size'),']
				$StartPos=strpos($Comment,"[");
				if($StartPos===false)
					ShowError("File information is not valid");
				$StartPos++;
				
				$EndPos=strpos($Comment,"]");
				if(($EndPos===false)||($EndPos<=$StartPos))
					ShowError("File information is not valid");
				
				$RealFilename=substr($Comment,$StartPos,$EndPos-$StartPos);
				$file='/payamavaran/www/deltasib/attachment/'.'__dsfile__Ticket_'.$TicketingAction_Id.'_'.$Param;
				
				DSDebug(1,"RealFilename=[$RealFilename] file=[$file]");
				if (file_exists($file)) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($RealFilename));
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					ob_clean();
					flush();
					readfile($file);
					exit;
				}
				else{
					echo "<html><head><script type=\"text/javascript\">";
					echo "window.onload = function(){alert('file not exist');window.close();}";
					echo "</script></head><body></body></html>";
					exit;
				}
				exit;
	
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
