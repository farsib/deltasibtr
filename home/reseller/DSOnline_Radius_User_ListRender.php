<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_Radius_UserListRender.........................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list","listSummary",'DeleteSession','DisconnectUser','DisconnectAllUser','ChangeLayout'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(1,"DSOnline_Radius_UserListRender List ******************************************");
				//Permission -----------------
				exitifnotpermit(0,"Online.Radius.user.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				function color_rows($row){
					if($row->get_value("TerminateCause")!='')
						$row->set_row_style("color:red");
					elseif($row->get_value("ISFinishUser")=='Yes')
						$row->set_row_style("color:darkorange");
					else
						$row->set_row_style("color:black");
				}
				
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$SelectStr="Online_RadiusUser_Id,oru.RUsername,".
					"round((ReceiveTr-LastReceiveTr)/128/oru.InterimTime,0) as LastDownloadSpeed,".
					"round((SendTr-LastSendTr)/128/oru.InterimTime,0) as LastUploadSpeed,".
					"VispName,NasName,CenterName,ServiceInfoName,oru.ServiceRate,Name,Family,ISFinishUser,CallingStationId,".
					"CalledStationId,INET_NTOA(FramedIpAddress) As FramedIpAddress,".
					"INET_NTOA(NasIpAddress) As NasIpAddress,INET_NTOA(SRCNasIpAddress) As SRCNasIpAddress,{$DT}DateTimeStr(AcctStartTime) As AcctStartTime,{$DT}DateTimeStr(DTRequest) As DTRequest,".
					"SecondToR(AcctSessionTime) As AcctSessionTime,ByteToR(SendTr) As SendTr, ".
					"ByteToR(ReceiveTr) As ReceiveTr,NasPortId,NasPortType,ServiceType,FramedProtocol,oru.URLReporting,oru.InterimTime,InterimCount,TerminateCause,".
					"ByteToR(ReturnTr) As ReturnTr,".
					"ByteToR((Internet_SendTr+Internet_ReceiveTr)) As InternetUse,".
					"ByteToR((Intranet_SendTr+Intranet_ReceiveTr)) As IntranetUse,".
					"ByteToR((Messenger_SendTr+Messenger_ReceiveTr)) As MessengerUse,".
					"ByteToR((Free_SendTr+Free_ReceiveTr)) As FreeUse,".
					"ByteToR((Special_SendTr+Special_ReceiveTr)) As SpecialUse";
				$ColumnStr=DBSelectAsString("Select RenderColumnIds from Hgrid_layout where Reseller_Id='$LReseller_Id' and ItemName='OnlineRadiusUser'");
				$sql="Select $SelectStr ".
					"From Tonline_radiususer oru ".
					"Left Join Huser u on oru.User_Id=u.User_Id ".
					"Left Join Hnas n on oru.Nas_Id=n.Nas_Id ".
					"Left Join Hserviceinfo si on oru.ServiceInfo_Id=si.ServiceInfo_Id ".
					"Left Join Hvisp v on u.Visp_Id=v.Visp_Id ".
					"Left Join Hcenter c on u.Center_Id=c.Center_Id ";
				if($LReseller_Id!=1){
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$sql.="Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) and (ISPermit='Yes') ";
				}
				
				$sql.="Where 1 ";
				$sql.="$sqlfilter $SortStr ";
				DSGridRender_Sql(100,$sql,"Online_RadiusUser_Id",$ColumnStr,"","","color_rows");
       break;
	case "listSummary":

				DSDebug(1,"DSOnline_Radius_UserListRender listSummary ******************************************");
				//Permission -----------------
				exitifnotpermit(0,"Online.Radius.User.ListSummary");

				$ColumnStr="Nas_Id,NasName,NasIpAddress,OnlineUser,LastDownloadSpeed,LastUploadSpeed,ServiceInfo_Id,ReturnTr,InternetUse,IntranetUse,MessengerUse,FreeUse,SpecialUse";
				DBUpdate("set @cnt1=10000000");
				DBUpdate("set @cnt2=20000000");
				DBUpdate("set @cnt3=30000000");
				$sql1="Select @cnt1:=@cnt1+1 as Id,0 as Nas_Id,'All NAS' as NasName,'' as NasIpAddress,Count(1) as OnlineUser,".
				"Sum(round((ReceiveTr-LastReceiveTr)/128/oru.InterimTime,0)) as LastDownloadSpeed,".
				"Sum(round((SendTr-LastSendTr)/128/oru.InterimTime,0)) as LastUploadSpeed,oru.ServiceInfo_Id, ".
				"ByteToR(SUM(ReturnTr)) As ReturnTr,".
				"ByteToR(SUM(Internet_SendTr+Internet_ReceiveTr)) as InternetUse, ".
				"ByteToR(SUM(Intranet_SendTr+Intranet_ReceiveTr)) as IntranetUse, ".
				"ByteToR(SUM(Messenger_SendTr+Messenger_ReceiveTr)) as MessengerUse, ".
				"ByteToR(SUM(Free_SendTr+Free_ReceiveTr)) as FreeUse, ".
				"ByteToR(SUM(Special_SendTr+Special_ReceiveTr)) as SpecialUse ".
				"From Tonline_radiususer oru ";
				
				
				$sql2="Select @cnt2:=@cnt2+1 as Id,oru.Nas_Id,NasName,INET_NTOA(NasIpAddress) As NasIpAddress,Count(1) as OnlineUser,".
				"Sum(round((ReceiveTr-LastReceiveTr)/128/oru.InterimTime,0)) as LastDownloadSpeed,".
				"Sum(round((SendTr-LastSendTr)/128/oru.InterimTime,0)) as LastUploadSpeed,oru.ServiceInfo_Id, ".
				"ByteToR(SUM(ReturnTr)) As ReturnTr,".
				"ByteToR(SUM(Internet_SendTr+Internet_ReceiveTr)) as InternetUse, ".
				"ByteToR(SUM(Intranet_SendTr+Intranet_ReceiveTr)) as IntranetUse, ".
				"ByteToR(SUM(Messenger_SendTr+Messenger_ReceiveTr)) as MessengerUse, ".
				"ByteToR(SUM(Free_SendTr+Free_ReceiveTr)) as FreeUse, ".
				"ByteToR(SUM(Special_SendTr+Special_ReceiveTr)) as SpecialUse ".
				"From Tonline_radiususer oru ".
				"Left Join Hnas n on oru.Nas_Id=n.Nas_Id ";
					
				$sql3="Select @cnt3:=@cnt3+1 as Id,oru.Nas_Id,NasName,'' as NasIpAddress,Count(1) as OnlineUser,".
				"Sum(round((ReceiveTr-LastReceiveTr)/128/oru.InterimTime,0)) as LastDownloadSpeed,".
				"Sum(round((SendTr-LastSendTr)/128/oru.InterimTime,0)) as LastUploadSpeed,oru.ServiceInfo_Id, ".
				"ByteToR(SUM(ReturnTr)) As ReturnTr,".
				"ByteToR(SUM(Internet_SendTr+Internet_ReceiveTr)) as InternetUse, ".
				"ByteToR(SUM(Intranet_SendTr+Intranet_ReceiveTr)) as IntranetUse, ".
				"ByteToR(SUM(Messenger_SendTr+Messenger_ReceiveTr)) as MessengerUse, ".
				"ByteToR(SUM(Free_SendTr+Free_ReceiveTr)) as FreeUse, ".
				"ByteToR(SUM(Special_SendTr+Special_ReceiveTr)) as SpecialUse ".
				"From Tonline_radiususer oru ".
				"Left Join Hnas n on oru.Nas_Id=n.Nas_Id ".
				"Left Join Hserviceinfo si on oru.ServiceInfo_Id=si.ServiceInfo_Id ";
				
				if($LReseller_Id!=1){
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$sql1.="Left Join Huser u on oru.User_Id=u.User_Id ";
					$sql1.="Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) and (ISPermit='Yes') ";
					
					$sql2.="Left Join Huser u on oru.User_Id=u.User_Id ";
					$sql2.="Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) and (ISPermit='Yes') ";
				
					$sql3.="Left Join Huser u on oru.User_Id=u.User_Id ";
					$sql3.="Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) and (ISPermit='Yes') ";
				}
				$sql1.=" where ServiceInfo_Id=1";
				$sql2.=" where ServiceInfo_Id=1 group by oru.Nas_Id";
				$sql3.=" where oru.ServiceInfo_Id<>1 group by oru.ServiceInfo_Id";
				
				$sql="($sql1) union all ($sql2) union all ($sql3)";

				function color_rows($row){
					global $ISGNas_Id;
					if($row->get_value("Nas_Id")==0)
						$row->set_row_style("font-weight:bold;color:darkgreen");
					elseif($row->get_value("ServiceInfo_Id")!=1){
						$row->set_row_style("font-weight:bold;color:red;background-color:lightsteelblue;");
						$row->set_value("NasIpAddress","");
					}
				}
				DSGridRender_Sql(-1,$sql,"Id",$ColumnStr,"","","color_rows");
		break;
	case "DeleteSession":
				DSDebug(1,"DSOnline_Radius_UserListRender DeleteSession ******************************************");
				exitifnotpermit(0,"Online.Radius.User.DeleteSession");
				$NewRowInfo=array();
				$NewRowInfo['Online_RadiusUser_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$ar=DBDelete('delete from Tonline_radiususer Where Online_RadiusUser_Id='.$NewRowInfo['Online_RadiusUser_Id']);
				//logsecurity('Web',"IP $IP deleted from Web IP Block");
				echo "OK~";
		break;
	case "DisconnectAllUser":
				DSDebug(1,"DSOnline_Radius_UserListRender DisconnectAllUser ******************************************");
				exitifnotpermit(0,"Online.Radius.User.DisconnectAllUser");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$NewRowInfo=array();
				$sql="replace Tonline_dcqueue(Online_RadiusUser_Id,CDT) Select Online_RadiusUser_Id,Now() From Tonline_radiususer oru ";
				
				
				if($sqlfilter!=""){
					$sql.=
					"Left Join Huser u on oru.User_Id=u.User_Id ".
					"Left Join Hnas n on oru.Nas_Id=n.Nas_Id ".
					"Left Join Hserviceinfo si on oru.ServiceInfo_Id=si.ServiceInfo_Id ".
					"Left Join Hvisp v on u.Visp_Id=v.Visp_Id ".
					"Left Join Hcenter c on u.Center_Id=c.Center_Id ";
				}
				
				if($LReseller_Id!=1){
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$sql.="Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) and (ISPermit='Yes') ";
				}
				$sql.="Where (oru.ServiceInfo_Id=1) $sqlfilter"; 
				
				$ar=DBUpdate($sql);
				if($ar<=0)
					ExitError("هیج کاربری به صف قطع اتصال افزوده نشد");
				echo "OK~";
		break;
	case "DisconnectUser":
				DSDebug(1,"DSOnline_Radius_UserListRender DisconnectUser ******************************************");
				exitifnotpermit(0,"Online.Radius.User.DisconnectUser");
				$NewRowInfo=array();
				$Online_RadiusUser_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$ar=DBUpdate("replace Tonline_dcqueue(Online_RadiusUser_Id,CDT) Select Online_RadiusUser_Id,Now() From Tonline_radiususer Where Online_RadiusUser_Id=$Online_RadiusUser_Id and ServiceInfo_Id=1"); 

				echo "OK~";
		break;
	case "ChangeLayout":
			DSDebug(1,"DSOnline_Radius_UserListRender ChangeLayout ******************************************");
			exitifnotpermit(0,"Online.Radius.user.List");
			$Req=Get_Input('GET','DB','Req','ARRAY',array('SaveLayout','ResetLayout'),0,0,0);
			
			if($Req=='SaveLayout'){
				$GColIds=Get_Input("POST","DB","GColIds","STR",20,400,0,0);
				$GColHeaders=Get_Input("POST","DB","GColHeaders","STR",20,400,0,0);
				$GColInitWidths=Get_Input("POST","DB","GColInitWidths","STR",3,150,0,0);
			}
			else{
				$GColIds="Online_RadiusUser_Id,oru.RUsername,LastDownloadSpeed,LastUploadSpeed,ServiceInfoName,oru.ServiceRate,VispName,NasName,CenterName,Name,Family,ISFinishUser,CallingStationId,CalledStationId,FramedIpAddress,NasIpAddress,SRCNasIpAddress,AcctStartTime,DTRequest,AcctSessionTime,SendTr,ReceiveTr,NasPortId,NasPortType,ServiceType,FramedProtocol,URLReporting,oru.InterimTime,InterimCount,TerminateCause";
				$GColHeaders="{#stat_count} rows,RUsername,DownloadSpeed(Kb/s),UploadSpeed(Kb/s),ServiceInfoName,ServiceRate,VispName,NasName,CenterName,Name,Family,ISFinishUser,CallingId,CalledId,FramedIP,NasIP,SRCNasIP,StartTime,LastUpdate,SessionTime(s),SendTr(B),ReceiveTr(B),NasPortId,NasPortType,ServiceType,FramedProtocol,URLReporting,InterimTime,InterimCount,TerminateCause";
				$GColInitWidths="80,80,125,115,100,90,120,100,80,80,80,75,100,100,100,100,80,120,120,90,120,120,80,80,80,90,80,90,90,90";
			}
			
			$ColIdsArray=explode(",",$GColIds);
			$ColHeadersArray=explode(",",$GColHeaders);
			$ColInitWidthsArray=explode(",",$GColInitWidths);
			
			DSDebug(1,"ColIdsArray=".DSPrintArray($ColIdsArray));
			DSDebug(1,"ColHeadersArray=".DSPrintArray($ColHeadersArray));
			DSDebug(1,"ColInitWidthsArray".DSPrintArray($ColInitWidthsArray));

			$GridLayoutArray=Array(
				"Online_RadiusUser_Id"	=>	Array(	"Online_RadiusUser_Id",	"{#stat_count} rows"	),
				"oru.RUsername"			=>	Array(	"RUsername",			"RUsername"				),
				"LastDownloadSpeed"		=>	Array(	"LastDownloadSpeed",	"DownloadSpeed(Kb/s)"	),
				"LastUploadSpeed"		=>	Array(	"LastUploadSpeed",		"UploadSpeed(Kb/s)"		),
				"ServiceInfoName"		=>	Array(	"ServiceInfoName",		"ServiceInfoName"		),
				"oru.ServiceRate"		=>	Array(	"ServiceRate",			"ServiceRate"			),
				"VispName"				=>	Array(	"VispName",				"VispName"				),
				"NasName"				=>	Array(	"NasName",				"NasName"				),
				"CenterName"			=>	Array(	"CenterName",			"CenterName"			),
				"Name"					=>	Array(	"Name",					"Name"					),
				"Family"				=>	Array(	"Family",				"Family"				),
				"ISFinishUser"			=>	Array(	"ISFinishUser",			"ISFinishUser"			),
				"CallingStationId"		=>	Array(	"CallingStationId",		"CallingId"				),
				"CalledStationId"		=>	Array(	"CalledStationId",		"CalledId"				),
				"FramedIpAddress"		=>	Array(	"FramedIpAddress",		"FramedIP"				),
				"NasIpAddress"			=>	Array(	"NasIpAddress",			"NasIP"					),
				"SRCNasIpAddress"		=>	Array(	"SRCNasIpAddress",		"SRCNasIP"				),
				"AcctStartTime"			=>	Array(	"AcctStartTime",		"StartTime"				),
				"DTRequest"				=>	Array(	"DTRequest",			"LastUpdate"			),
				"AcctSessionTime"		=>	Array(	"AcctSessionTime",		"SessionTime(s)"		),
				"SendTr"				=>	Array(	"SendTr",				"SendTr(B)"				),
				"ReceiveTr"				=>	Array(	"ReceiveTr",			"ReceiveTr(B)"			),
				"NasPortId"				=>	Array(	"NasPortId",			"NasPortId"				),
				"NasPortType"			=>	Array(	"NasPortType",			"NasPortType"			),
				"ServiceType"			=>	Array(	"ServiceType",			"ServiceType"			),
				"FramedProtocol"		=>	Array(	"FramedProtocol",		"FramedProtocol"		),
				"URLReporting"			=>	Array(	"URLReporting",			"URLReporting"			),
				"oru.InterimTime"		=>	Array(	"InterimTime",			"InterimTime"			),
				"InterimCount"			=>	Array(	"InterimCount",			"InterimCount"			),
				"TerminateCause"		=>	Array(	"TerminateCause",		"TerminateCause"		)
			);
			
			$IsAllZero=true;
			foreach($ColInitWidthsArray as $key=>$value){
				if($value<=0){
					$ColInitWidthsArray[$key]=0;
				}
				else
					$IsAllZero=false;
			}
			if($IsAllZero)
				$ColInitWidthsArray[0]=100;			
			
			
			$RenderColumnIds=Array();
			foreach($ColIdsArray as $key=>$value){
				DSDebug(1,"key  =  $key	=>	value  =  $value");
				if(!array_key_exists($value,$GridLayoutArray)){
					DSDebug(1,"Invalid ColIds[$value] supplied!!!");
					ExitError("Invalid ColIds[$value] supplied!!!");
				}
				if($ColHeadersArray[$key]!=$GridLayoutArray[$value][1]){
					DSDebug(1,"Supplied ColHeadersArray[$key]=".$ColHeadersArray[$key]." is mismatch with GridLayoutArray[$value][1]=".$GridLayoutArray[$value][1]."!!!");
					ExitError("Supplied ColHeaders [".$ColHeadersArray[$key]."] is mismatch with [".$GridLayoutArray[$value][1]."] at position $key!!!");
				}
				if(filter_var($ColInitWidthsArray[$key], FILTER_VALIDATE_INT)===false){
					DSDebug(1,"Supplied ColInitWidthsArray[$key]=".$ColInitWidthsArray[$key]." is no a valid integer value!!!");
					ExitError("Supplied ColInitWidths [".$ColInitWidthsArray[$key]."] is no a valid integer value at position $key!!!");
				}
				array_push($RenderColumnIds,$GridLayoutArray[$value][0]);
			}
			$ColIds=implode(",",$ColIdsArray);
			$ColHeaders=implode(",",$ColHeadersArray);
			$ColInitWidths=implode(",",$ColInitWidthsArray);
			$RenderColumnIds=implode(",",$RenderColumnIds);
			$sql="Update Hgrid_layout set ".
				"ColIds='$ColIds',".
				"ColHeaders='$ColHeaders',".
				"ColInitWidths='$ColInitWidths',".
				"RenderColumnIds='$RenderColumnIds' ".
				"where Reseller_Id='$LReseller_Id' and ItemName='OnlineRadiusUser'";
			$n=DBUpdate($sql);
			DSDebug(1,"Affected rows=$n");
			echo "OK~";
		break;		
		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
