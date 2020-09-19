<?php
try {
	require_once("../../lib/DSInitialUser.php");
	DSDebug(0,"DSFeedbackRender  ..................................................................................");
	/*
	if($LUsername==''){
		$WebNewUser=DBSelectAsString("Select Param1 From Hserver Where PartName='WebNewUser'");
		//ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
		echo "Session Expire`$WebNewUser`";
		exit;
	}
*/	
	PrintInputGetPost();

	$act=Get_Input('GET','DB','act','ARRAY',array("SendRequest",'loadrespond'),0,0,0);

	switch ($act) {
		case 'loadrespond':
				DSDebug(1,'DSFeedbackRender Load ********************************************');
				$KeyStr=Get_Input('GET','DB','KeyStr','STR',0,30,0,0);
				$Email=Get_Input('GET','DB','Email','STR',0,100,0,0);
				//$RKeyStr=Reverse($KeyStr);
				if($KeyStr!='')
					$sql="SELECT shdatetimestr(RequestCDT) as RequestCDT,Message,if(shdatetimestr(ReplyCDT)='','پاسخ داده نشده',shdatetimestr(ReplyCDT)) as ReplyCDT,Reply from Huser_feedback where KeyStr='$KeyStr' order by User_Feedback_Id desc Limit 1";
				else
					$sql="SELECT shdatetimestr(RequestCDT) as RequestCDT,Message,if(shdatetimestr(ReplyCDT)='','پاسخ داده نشده',shdatetimestr(ReplyCDT)) as ReplyCDT,Reply from Huser_feedback where Email='$Email' order by User_Feedback_Id desc limit 1";
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
		case "SendRequest":
 		
				$Username=Get_Input('POST','DB','Username','STR',0,32,0,0);
				$Email=Get_Input('POST','DB','Email','STR',0,100,0,0);
				$MobileNo=Get_Input('POST','DB','MobileNo','STR',11,11,0,0);
				$RequestType=Get_Input('POST','DB','RequestType','ARRAY',array("suggestions", "criticism"),0,0,0);
				$ServiceType="";
				$ServiceType=Get_Input('POST','DB','ServiceType','STR',3,3,0,0);
				$Message=Get_Input('POST','DB','Message','STR',1,255,0,0);
				
				$sql="	Insert Into  Huser_feedback Set ".
						"RequestCDT=Now(),Username='$Username',Email='$Email',MobileNo='$MobileNo',RequestType='$RequestType',ServiceType='$ServiceType',".
						"Message='$Message',IP=INET_ATON('$LClientIP'),RequestDate=Now(),".
						"OnlineUsername=(Select RUsername From  Tonline_radiususer Where ServiceInfo_Id=1 and FramedIpAddress=INET_ATON('$LClientIP'))";
				
				$id=DBInsert($sql);
				$shdate=DBSelectAsString("Select Replace(shdatestr(now()),'/','')");
				$counter=100+DBSelectAsString("Select Count(1) from Huser_feedback Where RequestDate=Date(Now()) and User_Feedback_Id<$id");
				$CompanyCode=DBSelectAsString("Select Param2 From Hserver Where PartName='WebFeedback'");
				$KeyStr=GenerateKey($CompanyCode,$ServiceType,$counter,$shdate);
				DBUpdate("Update Huser_feedback Set KeyStr='$KeyStr' Where User_Feedback_Id=$id");
				//$KeyStr=Reverse($KeyStr);
				echo "OK~$KeyStr";
			
			break;
		default :
			echo "~Unknown Request";
	}//switch ($act)
} catch (Exception $e) {
//ExitError($e->getMessage());
	$err=$e->getMessage();
	$pos = strpos($err, 'Duplicate');
	if ($pos === false) {
		ExitError($err);
	} else {					
		ExitError('کاربر گرامی در طی روز جاری یک درخواست از شما ثبت شده است');
	}	
}

function Reverse($KeyStr){
	$vlist=explode("_",$KeyStr);
	$vlistcount=count($vlist);
	$RKeyStr='';
	for($j=$vlistcount-1;$j>=0;$j--){
		if($RKeyStr!='')$RKeyStr.='_';
		$RKeyStr=$RKeyStr.$vlist[$j];
	}
	return $RKeyStr;
	
}

//GenerateKey('1348','007','101','13941203');
function GenerateKey($CompanyCode,$ServiceType,$counter,$date){
    $MyStr=$CompanyCode.$ServiceType.$counter.$date;
    $MySum=0;
    for($i=1;$i<=strlen($MyStr);$i++)
        $MySum+=substr($MyStr,$i,1);
    $a=(($MySum%2==0)?0:1);

    return $CompanyCode."_".$ServiceType."_".$counter."_".$date."_".$a;
}

?>