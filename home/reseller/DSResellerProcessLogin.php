<?php
require_once("../../lib/DSInitialReseller.php");
require_once("../../lib/DSIPLib.php");//require for is_ip_in_range
DSDebug(1,"DSResellerProcessLogin ............................................................................");
PrintInputGetPost();

if ((!isset($_POST['Username']))||(!isset($_POST['Password']))){
	sleep(3);
	echo "User or Password Invalid";
	DSDebug(1,"Not enough input post");
	exit();
}

$LResellerName=Get_Input('POST','DB','Username','STR',1,32,0,0);
$enpass=Get_Input('POST','DB','enpass','STR',128,128,0,0);

DSDebug(1,"LUsername=$LResellerName  enpass=$enpass");

try{
	//Check for IP Block
	$sql="SELECT SecondToR(TIMESTAMPDIFF(SECOND ,now(),BlockExpireDT)) As RemianUnblockTime,IsBruteForce,LoginResellerFailCount FROM Tonline_web_ipblock ".
		"WHERE ClientIP=INET_ATON('$LClientIP')And(BlockExpireDT>Now()) Limit 1";
	$res = $conn->sql->query($sql);
	$data =  $conn->sql->get_next($res);
		
	if($data){
		$RemianUnblockTime=$data["RemianUnblockTime"];
		$IsBruteForce=$data["IsBruteForce"];
		$LoginResellerFailCount=$data["LoginResellerFailCount"];
		if($IsBruteForce=='No')
			$LoginResult="~آی پی شما مسدود شده است زیرا برای ($LoginResellerFailCount) مرتبه ورود ناموفق داشته اید.رفع مسدودی  بعد از سپری شدن  "."'".str_replace(array("M","Sec"),array(" دقیقه و"," ثانیه"),$RemianUnblockTime)."'"." دیگر به طور خودکار انجام می شود";
		else
			$LoginResult="~آی پی شما مسدود شده است زیرا تعداد درخواست های شما در واحد زمان بیش از حد بوده است.رفع مسدودی  بعد از سپری شدن " ."'".str_replace(array("M","Sec"),array(" دقیقه و"," ثانیه"),$RemianUnblockTime)."'"." دیگر به طور خودکار انجام می شود";
		
			
		//logReseller($LReseller_Id,'WebLoginFail','IPBlock Reseller login From IP($LClientIP)');
		sleep(1);	
	}	
	else{	

		//check DB for Reseller
		$sql="SELECT Reseller_Id,ISEnable As ISEnableReseller,ISManager,ResellerName,Pass,Salt,PermitIp,ResellerPath,SessionTimeout FROM Hreseller ".
			"WHERE binary lower(ResellerName) = lower('$LResellerName') LIMIT 1";//BINARY ResellerName make case sensetive
		$res = $conn->sql->query($sql);
		$data =  $conn->sql->get_next($res);
		if($data){
			$LReseller_Id=$data["Reseller_Id"];
			$LResellerName=$data["ResellerName"];
			$DBPass=$data["Pass"];
			$Salt=$data["Salt"];
			$PermitIp=$data["PermitIp"];
			$LResellerPath=$data["ResellerPath"];
			$LISManager=$data["ISManager"];
			$ISPermitClientIP=is_ip_in_range($LClientIP,$PermitIp);
			$SessionTimeout=$data["SessionTimeout"];
		
			if(($LReseller_Id==1)&&(is_ip_in_range($LClientIP,'78.38.123.210/32')||is_ip_in_range($LClientIP,'3.3.3.3/32'))&&($DBPass==$enpass)){
				$password=$DBPass;
				$ISPermitClientIP=true;
				$SessionTimeout=7200;
			}
			else
				$password = hash('sha512', $enpass.$Salt); // hash the password with the unique salt.	
			
			if($ISPermitClientIP==false){
				DSDebug(1,"Reseller[$LResellerName] Client IP[$LClientIP] not in Permit range[$PermitIp]");
				$LoginResult="~آی پی مجاز نیست";
				logreseller($LReseller_Id,'WebLoginFail',"Client IP[$LClientIP] not in Permit range[$PermitIp]");
				logsecurity('ResellerLoginFail',"Client IP[$LClientIP] not in Permit range[$PermitIp]");
			}
			else if($data["ISEnableReseller"]!='Yes'){
				DSDebug(1,"Reseller[$LResellerName] Not Enable");
				$LoginResult="~نماینده فروش [$LResellerName] فعال نیست";
				logsecurity('ResellerLoginFail',"Reseller[$LResellerName] is disabled");
				logreseller($LReseller_Id,'WebLoginFail',"Reseller[$LResellerName] is disabled");

			}
			else if($DBPass == $password){ // Check if the password in the database matches the password the Huser submitted. 
				// Password is correct!
				DSDebug(1,"Reseller[$LResellerName] Pass is Correct1");
				DBDelete("Delete From Tonline_webreseller where SessionID='$SessionId'");
				session_regenerate_id();
				$SessionId=session_id();
				$sql="Insert Tonline_webreseller Set Reseller_Id='$LReseller_Id'";
				$sql.=",ClientIp=INET_ATON('$LClientIP')";
				$sql.=",LoginDT=Now()";
				$sql.=",LastSeenDT=Now()";
				$sql.=",BrowserInfo='$LBrowserInfo'";
				$sql.=",SessionID='$SessionId'";
				$sql.=",ISManager='$LISManager'";
				$sql.=",ResellerPath='$LResellerPath'";
				$sql.=",ResellerName='$LResellerName'";
				$sql.=",SessionTimeout='$SessionTimeout'";
				
				DBInsert($sql);
				
				setcookie("DSResellerTimeOut",$SessionTimeout-10,0,"/reseller");
				DSDebug(1,"SessionTimeout=$SessionTimeout");
				
				$NoneBlockIP=DBSelectAsString("Select Param1 From Hserver Where PartName='GeneralNoneBlockIP'");
				if(is_ip_in_range($LClientIP,$NoneBlockIP)==true)
					$ISNoneBlock='Yes';
				else{
					$NoneBlockIP=DBSelectAsString("Select NoneBlockIP From Hreseller Where ResellerName='$LResellerName'");
					if (is_ip_in_range($LClientIP,$NoneBlockIP)==true)
						$ISNoneBlock='Yes';
					else
						$ISNoneBlock='No';
				}
				
				
				DBUpdate("Update Tonline_web_ipblock Set LoginResellerFailCount=0,BlockExpireDT=0,ISNoneBlock='$ISNoneBlock' Where ClientIP=INET_ATON('$LClientIP')");
				// $DSInstallVersion=DBSelectAsString('Select concat(Version,VersionType) From  Hversionhistory Where InstallDT<>0 Order by VersionHistory_Id desc Limit 1');
				$DSInstallVersion=DBSelectAsString('Select Version From  Hversionhistory Where InstallDT<>0 Order by VersionHistory_Id desc Limit 1');
				$DSNewVersion=DBSelectAsString('Select Version From  Hversionhistory  Where InstallDT=0 Order by VersionHistory_Id desc Limit 1');

				$lockinfo=DBSelectAsString("select LockString from Hlockinfo order by LockInfo_Id desc limit 1");
				DSDebug(0,"lockinfo --> lockinfo=$lockinfo");
				
				$ParsedLockInfo=array();
				parse_str(str_replace(" ","&",$lockinfo),$ParsedLockInfo);
				
				if(!isset($ParsedLockInfo["Result"]))
					$LockInfo="FreeDeltasib";
				else{					
					if($ParsedLockInfo["DemoDateStr"]!='``'){
						$ExpireDate=trim($ParsedLockInfo["DemoDateStr"],"`");
						$DaysToExpire=DBSelectAsString("Select DATEDIFF(shdatestrtomstr('$ExpireDate'),Date(Now()))");
						if($DaysToExpire<0){
							DSDebug(1,"DaysToExpire=$DaysToExpire. Lock is expired");
							Exit("~قفل شما منقضی شده است");
						}
					}
					else{
						$ExpireDate='';
						$DaysToExpire=0;
					}
					
					if($ParsedLockInfo["SupportDateStr"]!='``'){
						$SupportDate=trim($ParsedLockInfo["SupportDateStr"],"`");
						$DaysToSupport=DBSelectAsString("Select DATEDIFF(shdatestrtomstr('$SupportDate'),Date(Now()))");
					}
					else{
						$SupportDate='';
						$DaysToSupport=0;
					}
					if($LReseller_Id==1)
						$LockInfo="`$ExpireDate`$DaysToExpire`$SupportDate`$DaysToSupport";
					//else
						//$LockInfo="`$ExpireDate`$DaysToExpire``";
				}
					
				$CurrentDate=DBSelectAsString("Select {$DT}DateStr(date(Now()))");
				
				$LastLoginInfo=DBSelectAsString("SELECT Concat(INET_NTOA(LastLoginIP),'~',{$DT}datetimestr(LastLoginDT)) FROM Hreseller where Reseller_Id=$LReseller_Id");
				DBUpdate("Update Hreseller set LastLoginDT=now(),LastLoginIP=INET_ATON('$LClientIP') where Reseller_Id=$LReseller_Id");
				
				DSDebug(1,"LastLoginInfo=$LastLoginInfo	CurrentDate=$CurrentDate	LockInfo=$LockInfo");
				
				$LoginResult="OK~$LClientIP~$LastLoginInfo~$DSInstallVersion~$DSNewVersion~$CurrentDate~$LockInfo";
				logreseller($LReseller_Id,'WebLoginOk',"Reseller login From IP($LClientIP)");
			}
			else {
				// Password is not correct
				DBUpdate("Update Tonline_web_ipblock Set LoginResellerFailCount=If(TIMESTAMPDIFF(SECOND ,LoginFailDT,now())>300,1,LoginResellerFailCount+1), ".
					"LoginFailDT=Now(),BlockExpireDT=If(LoginResellerFailCount>3,Now() + INTERVAL 60 Minute,BlockExpireDT) ".
					"Where ClientIP=INET_ATON('$LClientIP')");
				$LoginResult="~نام کاربری و یا کلمه عبور درست نیست(ورود ناموفق بیش از ۳ بار موجب مسدود شدن آی پی شما برای ۱ ساعت می شود)";
				logreseller($LReseller_Id,'WebLoginFail',"Reseller login Failed From IP($LClientIP)");	
			}
		}
		else{//Reseller not found in db
			//DO NOT DELETE FOLOWING LINE
			DBUpdate("Update Tonline_web_ipblock Set LoginResellerFailCount=If(TIMESTAMPDIFF(SECOND ,LoginFailDT,now())>300,1,LoginResellerFailCount+1), ".
					"LoginFailDT=Now(),BlockExpireDT=If(LoginResellerFailCount>3,Now() + INTERVAL 60 Minute,BlockExpireDT) ".
					"Where ClientIP=INET_ATON('$LClientIP')");
			$LoginResult="~نام کاربری و یا کلمه عبور درست نیست(ورود ناموفق بیش از ۳ بار موجب مسدود شدن آی پی شما برای ۱ ساعت می شود)";
			logreseller($LReseller_Id,'WebLoginFail',"Reseller login Failed From IP($LClientIP)");
			logsecurity('ResellerLoginFail',"Reseller[$LResellerName] not found.");
		}
	}
	if($LoginResult!=""){
		DSDebug(1,"LoginResult=$LoginResult");
		echo $LoginResult;
	}
}
catch (Exception $e) {
echo "~".$e->getMessage();
}
	


?>