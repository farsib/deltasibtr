<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSSMSProviderEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update",'SendSMS'),0,0,0);
function str_replace_deep($search, $replace, $subject)
{
    if (is_array($subject))
    {
        foreach($subject as &$oneSubject)
            $oneSubject = str_replace_deep($search, $replace, $oneSubject);
        unset($oneSubject);
        return $subject;
    } else {
        return str_replace($search, $replace, $subject);
    }
} 
try {
switch ($act) {
    case "load":
				DSDebug(1,"DSSMSProviderEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Message.SMSProvider.View");
				$SMSProvider_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,SMSProvider_Id,ISEnable,SMSProviderName,PhpSendCode  from Hsmsprovider where SMSProvider_Id='$SMSProvider_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
    case "insert": 
				DSDebug(1,"DSSMSProviderEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Message.SMSProvider.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['SMSProviderName']=Get_Input('POST','DB','SMSProviderName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['PhpSendCode']=Get_Input('POST','DB','PhpSendCode','STR',1,4096,0,0);
				
				//Check php code
				$randfilename=GenerateRandomString(10);
				$Res=file_put_contents("/tmp/{$randfilename}.php", '<?php '.$NewRowInfo['PhpSendCode'].' ?>');
				//$output = shell_exec("rm -f /etc/cron.daily/ds_createlog");
				$output = shell_exec("php -l /tmp/{$randfilename}.php");
				DSDebug(2,"output=[$output]");				
				
				
				//----------------------
				$sql= "insert Hsmsprovider set ";
				$sql.="SMSProviderName='".$NewRowInfo['SMSProviderName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="PhpSendCode='".$NewRowInfo['PhpSendCode']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['SMSProvider_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','SMSProvider',$RowId,'SMSProvider');
				$Res=runshellcommand("php","DSCreateSMSProviderLib","","");
				DSDebug(1,"DSCreateSMSProviderLib->Reply [$Res]");
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSSMSProviderEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Message.SMSProvider.Edit");
				$NewRowInfo=array();
				$NewRowInfo['SMSProvider_Id']=Get_Input('POST','DB','SMSProvider_Id','INT',1,4294967295,0,0);
				$NewRowInfo['SMSProviderName']=Get_Input('POST','DB','SMSProviderName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['PhpSendCode']=Get_Input('POST','DB','PhpSendCode','STR',1,4096,0,0);

				//Check php code
				$randfilename=GenerateRandomString(10);
				$randoutfilename=GenerateRandomString(10);
				$Content=str_replace_deep(array("\\\"", "\\'","\\n"), array("\"", "'","\n"),$NewRowInfo['PhpSendCode']);
				DSDebug(2,"Content=[$Content]");				
				
				$Res=file_put_contents("/tmp/{$randfilename}.php", '<?php '.$Content.' ?>');
				$Command="php -l /tmp/{$randfilename}.php >/tmp/{$randoutfilename}.tmp1 2>>/tmp/{$randoutfilename}.tmp";
				DSDebug(2,"Command=[$Command]");
				system($Command);
				$Output=file_get_contents("/tmp/{$randoutfilename}.tmp");
				DSDebug(2,"Outputt=[$Output]");				
				unlink("/tmp/{$randfilename}.php");
				unlink("/tmp/{$randoutfilename}.php");
				if($Output!='')ExitError($Output);	
				
				
				$OldRowInfo= LoadRowInfo("Hsmsprovider","SMSProvider_Id='".$NewRowInfo['SMSProvider_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hsmsprovider set  ";
				$sql.="SMSProviderName='".$NewRowInfo['SMSProviderName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="PhpSendCode='".$NewRowInfo['PhpSendCode']."'";
				$sql.=" Where ";
				$sql.="(SMSProvider_Id='".$NewRowInfo['SMSProvider_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				$Res=runshellcommand("php","DSCreateSMSProviderLib","","");
				DSDebug(1,"DSCreateSMSProviderLib->Reply [$Res]");
				echo "OK~";
        break;
    case "SendSMS":
				DSDebug(1,"DSSMSProviderEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Message.SMSProvider.Edit");
				require_once("../../lib/DSSMSProviderLib.php");
				$SMSProvider_Id=Get_Input('GET','DB','SMSProvider_Id','INT',1,4294967295,0,0);
				$MobileNo=Get_Input('POST','DB','MobileNo','STR',11,11,0,0);
				$Message=Get_Input('POST','DB1','Message','STR',0,512,0,0);
				DSDebug(1,"SendSMS($SMSProvider_Id,$MobileNo,$Message,'Test')");
				
				$Res=SendSMS($SMSProvider_Id,"$MobileNo","$Message","Test");
				DSDebug(1,"SendSMS Reply [$Res]");
				If($Res=='OK')
					echo "OK~";
				Else
					echo $Res;
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
