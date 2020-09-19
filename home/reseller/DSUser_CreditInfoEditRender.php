<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSUserEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//exitifnotpermit(0,"Admin.User.Access");

$act=Get_Input('GET','DB','act','ARRAY',array("load"),0,0,0);
$unit=Get_Input('GET','DB','unit','ARRAY',array("BS","MM","GH"),0,0,0);

switch ($act) {
    case "load":
				DSDebug(1,"DSUserEditRender Load ********************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CreditStatus.List");
				$ShowExtraInfo=Get_Input('GET','DB','ShowExtraInfo','INT',0,1,0,0);
				//$User_ServiceBase_Id=DBSelectAsString("Select User_ServiceBase_Id from Huser where (User_Id=$User_Id)");
				If($unit=='BS') {
					$B=1;//Byte To Byte Convert
					$BF=0;//Traffic Float Number
					$T=1; //Second to Second Convert
					$TF=0;////Traffic Float Number
				}	
				Else If($unit=='MM') {
					$B=1048576;//Mega Byte To Byte Convert
					$BF=3;//Traffic Float Number
					$T=60; //Minute to Second Convert
					$TF=3;////Traffic Float Number
				}	
				If($unit=='GH') {
					$B=1073741824;//Byte To Byte Convert
					$BF=3;//Traffic Float Number
					$T=3600; //Second to Second Convert
					$TF=3;////Traffic Float Number
				}	
				
		
				
				$sql="Select '' As Error,ServiceStatus,ServiceName,{$DT}DateStr(u.StartDate) As StartDate,Format(ServiceFreeTrU/$B,$BF) as ServiceFreeTrU,Format(LastTrU/$B,$BF) as ServiceBufferTr, ".
					"Concat(s.ActiveYear,'Y, ', s.ActiveMonth,'M, ',s.ActiveDay,if(ExtraDay>0,concat('+',ExtraDay),''),'D') As Period,Concat({$DT}DateStr(u.EndDate),' ',u.ActiveTime) As EndDate, ".
					"{$DT}DateTimeStr(Now()) As CurrentDT,User_Gift_Id,{$DT}DateTimeStr(GiftEndDT) As GiftEndDT,".
					"GiftTrafficRate,GiftTimeRate,Format(GiftExtraTr/$B,$BF) As GiftExtraTr,GiftStopOnTrFinish,Format(GiftExtraTi/$T,$TF)As GiftExtraTi,Hg.MikrotikRateName as GiftMikrotikRate,".
					"Format ((Tu_u.ETrA-ETrU)/$B,$BF) As ETrR,Format (ETrU/$B,$BF) As ETrU,Format (Tu_u.ETrA/$B,$BF) As ETrA,".
					"If(Tu_u.STrA=0,'UL',Format((Tu_u.STrA-STrU)/$B,$BF)) As STrR,Format(STrU/$B,$BF) As STrU,If(Tu_u.STrA=0,'UL',Format((Tu_u.STrA)/$B,$BF)) As STrA, ".
					"Format(Tu_u.ReturnTr/$B,$BF) as ReturnTr,".
					"If(Tu_u.YTrA=0,'UL',Format((Tu_u.YTrA-YTrU)/$B,$BF)) As YTrR,Format(YTrU/$B,$BF) As YTrU,If(Tu_u.YTrA=0,'UL',Format((Tu_u.YTrA)/$B,$BF)) As YTrA, ".
					"If(Tu_u.MTrA=0,'UL',Format((Tu_u.MTrA-MTrU)/$B,$BF)) As MTrR,Format(MTrU/$B,$BF) As MTrU,If(Tu_u.MTrA=0,'UL',Format((Tu_u.MTrA)/$B,$BF)) As MTrA, ".
					"If(Tu_u.WTrA=0,'UL',Format((Tu_u.WTrA-WTrU)/$B,$BF)) As WTrR,Format(WTrU/$B,$BF) As WTrU,If(Tu_u.WTrA=0,'UL',Format((Tu_u.WTrA)/$B,$BF)) As WTrA, ".
					"If(Tu_u.DTrA=0,'UL',Format((Tu_u.DTrA-DTrU)/$B,$BF)) As DTrR,Format(DTrU/$B,$BF) As DTrU,If(Tu_u.DTrA=0,'UL',Format((Tu_u.DTrA)/$B,$BF)) As DTrA, ".
					"Format ((Tu_u.ETiA-ETiU)/$T,$TF) As ETiR,Format (ETiU/$T,$TF) As ETiU,Format (Tu_u.ETiA/$T,$TF) As ETiA,".
					"If(Tu_u.STiA=0,'UL',Format((Tu_u.STiA-STiU)/$T,$TF)) As STiR,Format(STiU/$T,$TF) As STiU,If(Tu_u.STiA=0,'UL',Format((Tu_u.STiA)/$T,$TF)) As STiA, ".
					"If(Tu_u.YTiA=0,'UL',Format((Tu_u.YTiA-YTiU)/$T,$TF)) As YTiR,Format(YTiU/$T,$TF) As YTiU,If(Tu_u.YTiA=0,'UL',Format((Tu_u.YTiA)/$T,$TF)) As YTiA, ".
					"If(Tu_u.MTiA=0,'UL',Format((Tu_u.MTiA-MTiU)/$T,$TF)) As MTiR,Format(MTiU/$T,$TF) As MTiU,If(Tu_u.MTiA=0,'UL',Format((Tu_u.MTiA)/$T,$TF)) As MTiA, ".
					"If(Tu_u.WTiA=0,'UL',Format((Tu_u.WTiA-WTiU)/$T,$TF)) As WTiR,Format(WTiU/$T,$TF) As WTiU,If(Tu_u.WTiA=0,'UL',Format((Tu_u.WTiA)/$T,$TF)) As WTiA, ".
					"If(Tu_u.DTiA=0,'UL',Format((Tu_u.DTiA-DTiU)/$T,$TF)) As DTiR,Format(DTiU/$T,$TF) As DTiU,If(Tu_u.DTiA=0,'UL',Format((Tu_u.DTiA)/$T,$TF)) As DTiA, ".
					"Format(RealReceiveTr/$B,$BF) As RealReceiveTr,Format(RealSendTr/$B,$BF) As RealSendTr,Format(BugUsedTr/$B,$BF) As BugUsedTr,Format(FinishUsedTr/$B,$BF) As FinishUsedTr,".
					"Format(RealUsedTime/$T,$TF) As RealUsedTime,Format(BugUsedTi/$T,$TF) As BugUsedTi,Format(FinishUsedTi/$T,$TF) As FinishUsedTi, ".
					"Tu_u.ISFairService,Tu_u.UPFairStatus,Hf.MikrotikRateName as FairMikrotikRate,".
					"(Select Count(1) from Tonline_radiususer Where User_Id=$User_Id And ServiceInfo_Id=1 and AcctUniqueId<>'' and TerminateCause='') As Session,".
					"{$DT}DateTimeStr(Tu_u.LastRequestDT) As  LastRequestDT".
					
					($ShowExtraInfo==1?
						",Tu_a.Simulation as Simulation, ".
						"sec_to_time(Tu_a.InterimTime) as InterimTime,LoginTimeName, ".
						"(Select REPLACE(TimeRateValue,'.00','') From Htimerate Where TimeRate_Id=FindActiveParamValueByAllId(3,Tu_a.Center_Id,Tu_a.Visp_Id,Tu_a.Reseller_Id,Tu_a.Service_Id,Tu_a.User_Id,1)) As TimeRate, ".
						"(Select REPLACE(TrafficRateValue,'.00','') From Htrafficrate Where TrafficRate_Id=FindActiveParamValueByAllId(4,Tu_a.Center_Id,Tu_a.Visp_Id,Tu_a.Reseller_Id,Tu_a.Service_Id,Tu_a.User_Id,1)) As TrafficRate,".
						"(Select FindActiveParamValueByAllId(5,Tu_a.Center_Id,Tu_a.Visp_Id,Tu_a.Reseller_Id,Tu_a.Service_Id,Tu_a.User_Id,1)) as ReceiveRate,".
						"(Select FindActiveParamValueByAllId(6,Tu_a.Center_Id,Tu_a.Visp_Id,Tu_a.Reseller_Id,Tu_a.Service_Id,Tu_a.User_Id,1)) as SendRate, ".
						"u.Calendar,u.PeriodicUse,Hm.MikrotikRateName,IPPoolName,FinishRuleName,SecondToR(Tu_a.MaxSessionTime) as MaxSessionTime,".
						"OffFormulaName,u.URLReporting,ActiveDirectoryName,Tu_a.AuthMethod as AuthMethod,u.UserType as UserType,".
						"{$DT}DateTimeStr(Tu_u.LastSaveDT) As  LastSaveDT,".
						"DebitControlName,WebAccessName,CalledIdName,AutoResetExtraCredit":
						""
					).
					" From Tuser_usage Tu_u Left join Huser u on (Tu_u.User_Id=u.User_Id) ".
					"Left join Tuser_authhelper Tu_a on (Tu_u.User_Id=Tu_a.User_Id) ".
					"Left join Hmikrotikrate Hg on (Tu_u.GiftMikrotikRate_Id=Hg.MikrotikRate_Id) ".
					"Left join Hservice s on (u.Service_Id=s.Service_Id) ".
					"Left join Hmikrotikrate Hf on (Tu_u.FairMikrotikRate_Id=Hf.MikrotikRate_Id) ".
					"left join Huser_servicebase u_sb on(u.User_ServiceBase_Id=u_sb.User_ServiceBase_Id) ".
					($ShowExtraInfo==1?
						"Left join Hlogintime lt on (Tu_a.LoginTime_Id=lt.LoginTime_Id) ".
						"Left join Hmikrotikrate Hm on (u.MikrotikRate_Id=Hm.MikrotikRate_Id) ".
						"Left join Hippool Hip on (u.IPPool_Id=Hip.IPPool_Id) ".
						"Left join Hfinishrule Hfr on (u.FinishRule_Id=Hfr.FinishRule_Id) ".
						"Left join Hoffformula Hof on (u.OffFormula_Id=Hof.OffFormula_Id) ".
						"Left join Hactivedirectory Had on (u.ActiveDirectory_Id=Had.ActiveDirectory_Id) ".
						"Left join Hdebitcontrol Hdc on (u.DebitControl_Id=Hdc.DebitControl_Id) ".
						"Left join Hwebaccess Hwa on (u.WebAccess_Id=Hwa.WebAccess_Id) ".
						"Left join Hcalledid Hca on (u.CalledId_Id=Hca.CalledId_Id) ":
						""
					).
					//"Left join Hstatus Mus on (u.Status_Id=Mus.Status_Id) ".
					" Where (u.User_Id=$User_Id) ";
					
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				//if($data)
					//exitifnotpermit($data["Visp_Id"],"Visp.User.View");
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data){
					foreach ($data as $Field=>$Value){
							GenerateLoadField($Field,$Value);
					}
				}
				echo '</data>';
				
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
