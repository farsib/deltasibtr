<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_Web_IPBlock_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list","UnBlockIP"),0,0,0);

switch ($act) {
    case "list":
				//Permission -----------------
				exitifnotpermit(0,"Online.Web.IPActivity.List");
				
				function color_rows($row){
					global $CurrentDate;
					$format="";
					if($row->get_value("ISNoneBlock")=='Yes')
						$format="font-weight:bold;";
					if($row->get_value("BlockExpireDT")>=$CurrentDate)
						$format.="color:red;";
					// elseif($row->get_value("IsBruteForce")=='Yes')
						// $format.="color:firebrick;"
					$row->set_row_style($format);
				}
				
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,"SELECT Online_Web_IPBlock_Id,INET_NtoA(ClientIP) As ClientIP,ISNoneBlock,Concat({$DT}DateStr(LastDate),' ',LPAD(LastHour,2,'0'),':',LPAD(LastMinute,2,'0'),':',LPAD(LastSecond,2,'0')) as LastRequestDT,LastDayRequest,LastHourRequest,LastMinuteRequest,LastSecondRequest, ".
									"IsBruteForce,{$DT}DateTimeStr(BruteForceDT) As BruteForceDT,{$DT}DateTimeStr(BlockExpireDT) As BlockExpireDT,".
									"{$DT}DateTimeStr(LoginFailDT) As LoginFailDT,LoginResellerFailCount,LoginUserFailCount ".
									" FROM   Tonline_web_ipblock Where 1 $sqlfilter $SortStr",
				"Online_Web_IPBlock_Id",
				"Online_Web_IPBlock_Id,ClientIP,ISNoneBlock,LastRequestDT,LastDayRequest,LastHourRequest,LastMinuteRequest,LastSecondRequest,IsBruteForce,BruteForceDT,BlockExpireDT,LoginFailDT,LoginResellerFailCount,LoginUserFailCount","","","color_rows");
       break;
	case "UnBlockIP":
				DSDebug(1,"DSOnline_Web_IPBlock_ListRender UnBlockIP ******************************************");
				exitifnotpermit(0,"Online.Web.IPActivity.DeleteEntry");
				$NewRowInfo=array();
				$NewRowInfo['Online_Web_IPBlock_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$IP=DBSelectAsString("Select INET_NTOA(ClientIp) from Tonline_web_ipblock where Online_Web_IPBlock_Id=".$NewRowInfo['Online_Web_IPBlock_Id']);
				$ar=DBDelete('delete from Tonline_web_ipblock Where Online_Web_IPBlock_Id='.$NewRowInfo['Online_Web_IPBlock_Id']);
				$ar=DBDelete("delete from Tonline_webreseller Where ClientIP=INET_AtoN('$IP')");
				$ar=DBDelete("delete from Tonline_webuser Where ClientIP=INET_AtoN('$IP')");
				logsecurity('Web',"IP $IP deleted from Web IP Block");
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
