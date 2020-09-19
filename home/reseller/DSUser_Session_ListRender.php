<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Session_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","DeleteSession",'DisconnectUser'),0,0,0);


switch ($act) {
	case "list":
				DSDebug(0,"DSUser_Session_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Session.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				function color_rows($row){
					if($row->get_value("TerminateCause")=='WaitForISG')
						$row->set_row_style("color:green");
					else if($row->get_value("TerminateCause")!='')
						$row->set_row_style("color:red");
					elseif($row->get_value("ISFinishUser")=='Yes')
						$row->set_row_style("color:darkorange");
					else
						$row->set_row_style("color:black");
				}
				
				$sql="Select Online_RadiusUser_Id,oru.RUsername,".
					"round((ReceiveTr-LastReceiveTr)/128/oru.InterimTime,0) as LastDownloadSpeed,".
					"round((SendTr-LastSendTr)/128/oru.InterimTime,0) as LastUploadSpeed,".
					"ServiceInfoName,NasName,ISFinishUser,CallingStationId,CalledStationId,INET_NTOA(FramedIpAddress) As FramedIpAddress,".
					"INET_NTOA(NasIpAddress) As NasIpAddress,INET_NTOA(SRCNasIpAddress) As SRCNasIpAddress,".
					"{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(DTRequest) As DTRequest,".
					"AcctSessionTime,ByteToR(SendTr) As SendTr, ".
					"ByteToR(ReceiveTr) As ReceiveTr,NasPortId,NasPortType,ServiceType,FramedProtocol,TerminateCause,Hg.MikrotikRateValueName, ".
					"ByteToR(SendTr+ReceiveTr) As RealTr,".
					"ByteToR(ReturnTr) As ReturnTr,".
					"ByteToR((Internet_SendTr+Internet_ReceiveTr)) as InternetUse, ".
					"ByteToR((Intranet_SendTr+Intranet_ReceiveTr)) as IntranetUse, ".
					"ByteToR((Messenger_SendTr+Messenger_ReceiveTr)) as MessengerUse, ".
					"ByteToR((Free_SendTr+Free_ReceiveTr)) as FreeUse, ".
					"ByteToR((Special_SendTr+Special_ReceiveTr)) as SpecialUse ".
					"From Tonline_radiususer oru ".
					"Left Join Huser u on oru.User_Id=u.User_Id ".
					"Left Join Hnas n on oru.Nas_Id=n.Nas_Id ".
					"Left Join Hserviceinfo si on oru.ServiceInfo_Id=si.ServiceInfo_Id ".
					"Left join Hmikrotikratevalue Hg on (oru.LastMikrotikRateValue_Id=Hg.MikrotikRateValue_Id) ".
					"Where (oru.User_Id=$User_Id) $sqlfilter $SortStr";
				$Column="Online_RadiusUser_Id,RUsername,LastDownloadSpeed,LastUploadSpeed,ServiceInfoName,NasName,ISFinishUser,CallingStationId,CalledStationId,FramedIpAddress,NasIpAddress,SRCNasIpAddress,AcctStartTime,DTRequest,AcctSessionTime,SendTr,ReceiveTr,NasPortId,NasPortType,ServiceType,FramedProtocol,TerminateCause,MikrotikRateValueName,RealTr,ReturnTr,InternetUse,IntranetUse,MessengerUse,FreeUse,SpecialUse";
				DSGridRender_Sql(100,$sql,"Online_RadiusUser_Id",$Column,"","","color_rows");
       break;
	case "DeleteSession":
				DSDebug(1,"DSUser_Session_ListRender DeleteSession ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Session.DeleteSession");
				$NewRowInfo=array();
				$NewRowInfo['Online_RadiusUser_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$ar=DBDelete('delete from Tonline_radiususer Where Online_RadiusUser_Id='.$NewRowInfo['Online_RadiusUser_Id']);
				logdb('Delete','User',$User_Id,'',"Delete-Session-ByReseller $LResellerName");
				$Username=DBSelectAsString("Select Username From Huser Where User_id=$User_Id ");
				DBSelectAsString("Select LogUser('0','$Username','Delete-Session-ByReseller','','By $LResellerName')");
				//logsecurity('Web',"IP $IP deleted from Web IP Block");
				echo "OK~";
		break;
	case "DisconnectUser":
				DSDebug(1,"DSUser_Session_ListRender DisconnectUser ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Session.DisconnectUser");
				$NewRowInfo=array();
				$Online_RadiusUser_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$ar=DBInsert("Insert Tonline_dcqueue(Online_RadiusUser_Id,CDT) Select Online_RadiusUser_Id,Now() From Tonline_radiususer Where Online_RadiusUser_Id=$Online_RadiusUser_Id on Duplicate KEY UPDATE CDT=Now()"); 
				logdb('Delete','User',$User_Id,'',"Disconnect-ByReseller $LResellerName");
				$Username=DBSelectAsString("Select Username From Huser Where User_id=$User_Id ");
				DBSelectAsString("Select LogUser('0','$Username','Disconnect-ByReseller','','By $LResellerName')");
				//$ar=DBInsert('Insert into Tonline_dcqueue Set CDT=Now(),Online_RadiusUser_Id='.$NewRowInfo['Online_RadiusUser_Id']);
				//logsecurity('Web',"IP $IP deleted from Web IP Block");
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>