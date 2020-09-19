<?php
try {
require_once("../../lib/DSInitialReseller.php");
require_once("../../lib/DSRenderLib.php");
DSDebug(1,"DSBatchProcess_WebMessageRender.php.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

if($LReseller_Id!=1) ExitError('فقط ادمین می تواند عمیات گروهی را انجام دهد');

set_time_limit(300);
//if($DebugLevel>0)
	//ExitError("You cannot use debug during BatchProcess. First, run ds_nodebug from linux shell");

$act=Get_Input('GET','DB','act','ARRAY',array(
	"StartBatchProcess",
	"SaveLog"
),0,0,0);

switch ($act) {
	case "StartBatchProcess":
				DSDebug(0,"DSBatchProcess_WebMessageRender.php->StartBatchProcess********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				
				$Action=Get_Input('POST','DB','Action','ARRAY',Array("SendMessage","DeleteMessage"),0,0,0);
				
				if($Action=="SendMessage"){
					$WebMessageTitle=Get_Input('POST','DB','WebMessageTitle','STR',1,64,0,0);
					$ReplaceCR=Get_Input('POST','DB','ReplaceCR','INT',0,1,0,0);
					$WebMessageBody=Get_Input('POST','DB','WebMessageBody','STR',1,2048,0,0);
					$WebMessageBody=trim($WebMessageBody);
					if($ReplaceCR==1)
						$WebMessageBody=trim(str_replace("\\n","<br/>",$WebMessageBody));
					
					$sql="insert into Huser_webmessage(User_Id,Creator_Id,CDT,WebMessageTitle,WebMessageBody) ".
						"select Hu.User_Id,'$LReseller_Id',now(),'$WebMessageTitle',".
									"Replace(
										Replace(
											Replace(
												Replace(
													Replace(
														Replace(
															Replace(
																Replace(
																	Replace(
																	'$WebMessageBody'
																	,'[Username]',Hu.Username )
																,'[Name]',Hu.Name )
															,'[Family]',Hu.Family )
														,'[Company]',Hu.Organization )	
													,'[ShExpireDate]',shdateStr(Hu.EndDate) )
												,'[RTrM]',if(DSSessionTraffic(GiftEndDT,GiftTrafficRate,GiftExtraTr,STrA,STrU,Tu_u.ETrA,ETrU,YTrA,YTrU,MTrA,MTrU,WTrA,WTrU,DTrA,DTrU)<999999999999999999,
													ByteToR(DSSessionTraffic(GiftEndDT,GiftTrafficRate,GiftExtraTr,STrA,STrU,Tu_u.ETrA,ETrU,YTrA,YTrU,MTrA,MTrU,WTrA,WTrU,DTrA,DTrU)-LastTrU),'Unlimit') )
											,'[UserDebit]',Format(-Hu.PayBalance,$PriceFloatDigit) )
										,'[SHDate]',shdateStr(Now()) )
									,'[Time]',time(Now()) )".
								"from Huser Hu join Tuser_usage Tu_u on Hu.User_Id=Tu_u.User_Id join Hbatchprocess_users bu on BatchProcess_Id='$BatchProcess_Id' and Hu.User_Id=bu.User_Id";
					$res=DBUpdate($sql);
					DSDebug(0,"Message sent for $res users");
				}
				else{
					$Type=Get_Input('POST','DB','Type','ARRAY',Array("Read","UnRead","All"),0,0,0);
					if($Type=="All")
						$WhereStr="";
					else
						$WhereStr="where WebMessageStatus='$Type'";
					$sql="delete wm from Huser_webmessage wm join Hbatchprocess_users bu on BatchProcess_Id='$BatchProcess_Id' and wm.User_Id=bu.User_Id $WhereStr";
					$ar=DBUpdate($sql);
					DSDebug(0,"Deleted row=$ar");
				}
				DBUpdate("Update Hbatchprocess_users set BatchItemState='Done',BatchItemDT=NOW() where BatchProcess_Id=$BatchProcess_Id");
				echo "OK~";
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
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
