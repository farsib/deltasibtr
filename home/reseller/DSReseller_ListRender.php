<?php
try{
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSResellerListRender .........................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete", "PastePermissions", "LoadCopyInformation", "LoadPasteInformation"),0,0,0);

switch ($act) {
    case "list":
				exitifnotpermit(0,"CRM.Reseller.List");
				
				$sql="SELECT Reseller_Id,ResellerName,ISEnable,Concat(ResellerPath,Reseller_Id,'>')As ResellerPath,{$DT}DateTimeStr(ResellerCDT) as ResellerCDT,if(ISOperator='Yes','Operator','Reseller') as ResellerType,{$DT}DateTimeStr(LastLoginDT) as LastLoginDT,Inet_NTOA(LastLoginIP) as LastLoginIP from Hreseller r ";
				$sql.="  Where $LResellerAccessAllow order by ResellerPath";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo "<rows>";
				if($data){
					$InitialPathSize=substr_count($data["ResellerPath"], '>')-1;
					while($data){
						$Reseller_Id=$data["Reseller_Id"];
						$ResellerName=$data["ResellerName"];
						$ResellerType=$data["ResellerType"];
						$ISEnable=$data["ISEnable"];
						$ResellerCDT=$data["ResellerCDT"];
						$ResellerPath=$data["ResellerPath"];
						$LastLoginDT=$data["LastLoginDT"];
						$LastLoginIP=$data["LastLoginIP"];
						$PathSize=substr_count($ResellerPath, '>')-1;
						echo "<row id='$Reseller_Id' ".(($ResellerType=="Reseller")?"style='font-weight:bold'":"").">";
						echo "<cell>$Reseller_Id</cell>";
						echo "<cell>$ResellerName</cell>";
						echo "<cell>$ResellerType</cell>";
						echo "<cell>$ISEnable</cell>";
						echo "<cell>$ResellerCDT</cell>";
						echo "<cell>$LastLoginIP</cell>";
						echo "<cell>$LastLoginDT</cell>";
						$OldPathSize=$PathSize;
						$data =  $conn->sql->get_next($res);
						if($data){
							$ResellerPath=$data["ResellerPath"];
							$PathSize=substr_count($ResellerPath, '>')-1;
						}
						else $PathSize=$InitialPathSize;
						for($i=$PathSize;$i<=$OldPathSize;$i++){
							echo "</row>";
						}//end of while
					}//while
				}	
				echo "</rows>";
       break;
//----------------------------------------------------------------------------------------------------------	   
	   case "Delete":
				DSDebug(1,"DSResellerListRender Delete ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Reseller_Id']=Get_Input('GET','DB','Id','INT',2,4294967295,0,0);
				if($NewRowInfo['Reseller_Id']==$LReseller_Id)	
					ExitError('نمیتوانید اطلاعات خود را حذف کنید');
				ExitIfNotPermitRowAccess("reseller",$NewRowInfo['Reseller_Id']);

				$ResellerName=DBSelectAsString("Select ResellerName from Hreseller Where ParentReseller_Id=".$NewRowInfo['Reseller_Id']);
				if($ResellerName!='')
					ExitError("این نماینده فروش دارای زیرشاخه زیر است و قابل حذف نیست</br>'$ResellerName'");

				$Username=DBSelectAsString("Select Username from Huser Where Reseller_Id=".$NewRowInfo['Reseller_Id']);
				if($Username!='')
					ExitError("این نماینده فروش توسط کاربر زیر استفاده می شود و قابل حذف نیست</br>'$Username'");
				
				$SupporterName=DBSelectAsString("Select SupporterName from Hsupporter Where Reseller_Id=".$NewRowInfo['Reseller_Id']);
				if($SupporterName!='')
					ExitError("این نماینده فروش توسط پشتیبان زیر استفاده می شود و قابل حذف نیست</br>'$SupporterName'");

				$NasInfoName=DBSelectAsString("Select NasInfoName from Hnasinfo Where DefReseller_Id=".$NewRowInfo['Reseller_Id']." Limit 1");
				if($NasInfoName<>'') ExitError("این نماینده فروش توسط پارامتر ردیوس زیر به عنوان نماینده فروش پیش فرض برای ایجاد کاربر جدید استفاده می شود،لطفا آن را تغییر دهید</br>'$NasInfoName'");
				
				$IsUsedForWebNewUser=DBSelectAsString("Select if(Param6=".$NewRowInfo['Reseller_Id'].",'Yes','No') from Hserver Where PartName='WebNewUser' and Param1='Yes'");
				if($IsUsedForWebNewUser=='Yes') ExitError("این نماینده فروش در ثبت نام کاربر در پنل کاربری استفاده می شود");				
				
				
				$ar=DBDelete('delete from Hreseller_packageaccess Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hreseller_payment Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hreseller_permit Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hreseller_terminalaccess Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				//$ar=DBDelete('delete from Hreseller_transaction Where Reseller_Id='.$NewRowInfo['Reseller_Id']); Keep for history
				$ar=DBDelete('delete from Hreseller_webconnection Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hservice_reselleraccess Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hstatus_reselleraccess Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				
				$ar=DBDelete('delete from Hclass_reselleraccess Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hreseller Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hlogdb Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hlogreseller Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hlogsecurity Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete('delete from Hgrid_layout Where Reseller_Id='.$NewRowInfo['Reseller_Id']);
				$ar=DBDelete("delete from Hparam Where TableName='Reseller' and TableId=".$NewRowInfo['Reseller_Id']);
				
				logdbdelete($NewRowInfo,'Delete','Reseller',$NewRowInfo['Reseller_Id'],'');
				echo "OK~";
		break;
//----------------------------------------------------------------------------------------------------------
	case "LoadCopyInformation":
				DSDebug(1,"DSResellerListRender LoadCopyInformation ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Permit.Edit");
				$From_Reseller_Id=Get_Input('POST','DB','From_Reseller_Id','INT',2,4294967295,0,0);
				ExitIfNotPermitRowAccess("reseller",$From_Reseller_Id);
				
				$sql="select count(1) from Hreseller_permit rp where rp.Reseller_Id='$From_Reseller_Id' and rp.ISPermit='Yes'";
				$Permitted=DBSelectAsString($sql);
				
				$sql="select count(1) from Hreseller_permit rp where rp.Reseller_Id='$From_Reseller_Id' and rp.ISPermit='No'";
				$NotPermitted=DBSelectAsString($sql);
				
				echo "OK~$Permitted~$NotPermitted";
		break;
//----------------------------------------------------------------------------------------------------------
	case "LoadPasteInformation":
				DSDebug(1,"DSResellerListRender LoadPasteInformation ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Permit.Edit");
				$From_Reseller_Id=Get_Input('POST','DB','From_Reseller_Id','INT',2,4294967295,0,0);
				$To_Reseller_Id=Get_Input('POST','DB','To_Reseller_Id','INT',2,4294967295,0,0);

				if($To_Reseller_Id==$LReseller_Id)	
					ExitError('شما نمی توانید به دسترسی های خود جایگذاری کنید');
				
				ExitIfNotPermitRowAccess("reseller",$From_Reseller_Id);
				ExitIfNotPermitRowAccess("reseller",$To_Reseller_Id);
				if($From_Reseller_Id==$To_Reseller_Id)
					ExitError("نمی توان دسترسی ها را به خودشان جایگذاری کرد");
				
				$FromResellerPath=DBSelectAsString("select ResellerPath from Hreseller where Reseller_Id=$From_Reseller_Id");
				$ToResellerPath=DBSelectAsString("select ResellerPath from Hreseller where Reseller_Id=$To_Reseller_Id");
				DSDebug(1,"FromResellerPath = $FromResellerPath	ToResellerPath = $ToResellerPath");
				DSDebug(1,"FromResellerPath.From_Reseller_Id.'>' = $FromResellerPath$From_Reseller_Id>	ToResellerPath = $ToResellerPath	");
				if(($FromResellerPath!=$ToResellerPath)&&(($FromResellerPath.$From_Reseller_Id.">")!=$ToResellerPath))
					ExitError("شما تنها می توانید دسترسی ها ی نماینده فروش/اپراتور را به زیر شاخه مستقیم خودش و یا به نماینده فروش/اپراتور با همان سطح جایگذاری کنید");
				
				$sql="select count(1) from Hreseller_permit rp where rp.Reseller_Id='$From_Reseller_Id' and rp.ISPermit='Yes'";
				$Permitted=DBSelectAsString($sql);
				
				$sql="select count(1) from Hreseller_permit rp where rp.Reseller_Id='$From_Reseller_Id' and rp.ISPermit='No'";
				$NotPermitted=DBSelectAsString($sql);
				
				$ChildResellerCount=DBSelectAsString("select count(1) from Hreseller where (ResellerPath like '$ToResellerPath$To_Reseller_Id>%')");
				// $ChildNotPermitted=$NotPermitted*$ChildResellerCount;
				echo "OK~$Permitted~$NotPermitted~$ChildResellerCount";
		break;
//----------------------------------------------------------------------------------------------------------
	case "PastePermissions":
				DSDebug(1,"DSResellerListRender PastePermissions ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Permit.Edit");
				$From_Reseller_Id=Get_Input('POST','DB','From_Reseller_Id','INT',2,4294967295,0,0);
				$To_Reseller_Id=Get_Input('POST','DB','To_Reseller_Id','INT',2,4294967295,0,0);

				if($To_Reseller_Id==$LReseller_Id)	
					ExitError('شما نمی توانید به دسترسی های خود جایگذاری کنید');
				ExitIfNotPermitRowAccess("reseller",$From_Reseller_Id);
				ExitIfNotPermitRowAccess("reseller",$To_Reseller_Id);
				if($From_Reseller_Id==$To_Reseller_Id)
					ExitError("نمی توان دسترسی ها را به خودشان جایگذاری کرد");
				$FromResellerPath=DBSelectAsString("select ResellerPath from Hreseller where Reseller_Id=$From_Reseller_Id");
				$ToResellerPath=DBSelectAsString("select ResellerPath from Hreseller where Reseller_Id=$To_Reseller_Id");
				DSDebug(1,"FromResellerPath = $FromResellerPath	ToResellerPath = $ToResellerPath");
				DSDebug(1,"FromResellerPath.From_Reseller_Id.'>' = $FromResellerPath$From_Reseller_Id>	ToResellerPath = $ToResellerPath	");
				if(($FromResellerPath!=$ToResellerPath)&&(($FromResellerPath.$From_Reseller_Id.">")!=$ToResellerPath))
					ExitError("شما تنها می توانید دسترسی ها ی نماینده فروش/اپراتور را به زیر شاخه مستقیم خودش و یا به نماینده فروش/اپراتور با همان سطح جایگذاری کنید");
				
				$ParentReseller_Id=DBSelectAsString("select ParentReseller_Id from Hreseller where Reseller_Id=$From_Reseller_Id");
				DBUpdate("set group_concat_max_len=20480");
				$ChildResellerSet=DBSelectAsString("select group_concat(Reseller_Id separator \"','\") from Hreseller where (ResellerPath like '$ToResellerPath$To_Reseller_Id>%')");
				DSDebug(1,"ParentReseller_Id=$ParentReseller_Id	ChildResellerSet = [('$ChildResellerSet')]");
				
				$sql="Create TEMPORARY TABLE PermissionFromTemp as ".
				"Select rp.PermitItem_Id,rp.Visp_Id,rp.ISPermit from Hreseller_permit rp where rp.Reseller_Id='$From_Reseller_Id'";
				$ar=DBUpdate($sql);
				DSDebug(0,"$ar row affected");
								
				$sql="update Hreseller_permit rp join PermissionFromTemp PT on ".
				"(rp.PermitItem_Id=PT.PermitItem_Id) and (rp.Visp_Id=PT.Visp_Id) ".
				"set rp.ISPermit=PT.ISPermit where (rp.Reseller_Id='$To_Reseller_Id')";
				$ar1=DBUpdate($sql);
				DSDebug(0,"$ar1 rows affected");
				
				$sql="update Hreseller_permit rp join PermissionFromTemp PT on ".
				"(PT.ISPermit='No') and (rp.PermitItem_Id=PT.PermitItem_Id) and (rp.Visp_Id=PT.Visp_Id) ".
				"set rp.ISPermit='No' where (rp.Reseller_Id in ('$ChildResellerSet'))";
				$ar2=DBUpdate($sql);
				DSDebug(0,"$ar2 row affected");
				
				DBUpdate("DROP TEMPORARY TABLE PermissionFromTemp");

				$ResellerSet=DBSelectAsString("select group_concat(Reseller_Id) from Hreseller r where (r.Reseller_Id = $To_Reseller_Id)or(r.ResellerPath like '$ToResellerPath$To_Reseller_Id>%')");
				DSDebug(1,"ResellerSet = [($ResellerSet)]");
				

				$sql="Select ISPermit from Hreseller_permit rp join Hpermititem pi on PermitItemName='CRM.User.List' and rp.PermitItem_Id=pi.PermitItem_Id ".
				" where rp.Reseller_Id=$To_Reseller_Id and rp.Visp_Id=0";
				$ISPermit_CRM_User_List=DBSelectAsString($sql);
				if($ISPermit_CRM_User_List=="Yes"){
					$sql="Insert Ignore Hgrid_layout Set ".
						"Reseller_Id='$To_Reseller_Id',".
						"ItemName='CRMUser',".
						"ColIds='u.User_Id,u.Username',".
						"ColHeaders='{#stat_count} ردیف,Username',".
						"ColInitWidths='80,100',".
						"RenderColumnIds='User_Id,Username'";
					DBInsert($sql);
					$ResellerArray=explode(",",$ResellerSet);
					foreach($ResellerArray as $RId)
						SetCRMUserGridLayout($RId);
				}
				else{
					$n=DBDelete("Delete from Hgrid_layout where ItemName='CRMUser' and Reseller_Id in ($ResellerSet)");
					DSDebug(1,"No permit to 'CRM.User.List'  -->  'CRMUser' grid layout of $n reseller deleted");
				}

				$sql="Select ISPermit from Hreseller_permit rp join Hpermititem pi on PermitItemName='Online.Radius.User.List' and rp.PermitItem_Id=pi.PermitItem_Id ".
				" where rp.Reseller_Id=$To_Reseller_Id and rp.Visp_Id=0";
				$ISPermit_Online_Radius_User_List=DBSelectAsString($sql);
				if($ISPermit_Online_Radius_User_List=="Yes"){
					$sql="Insert Ignore Hgrid_layout Set ".
					"Reseller_Id='$To_Reseller_Id',".
					"ItemName='OnlineRadiusUser',".
					"ColIds='Online_RadiusUser_Id,oru.RUsername,LastDownloadSpeed,LastUploadSpeed,ServiceInfoName,oru.ServiceRate,VispName,NasName,CenterName,Name,Family,ISFinishUser,CallingStationId,CalledStationId,FramedIpAddress,NasIpAddress,SRCNasIpAddress,AcctStartTime,DTRequest,AcctSessionTime,SendTr,ReceiveTr,NasPortId,NasPortType,ServiceType,FramedProtocol,URLReporting,oru.InterimTime,InterimCount,TerminateCause',".
					"ColHeaders='{#stat_count} ردیف,RUsername,DownloadSpeed(Kb/s),UploadSpeed(Kb/s),ServiceInfoName,ServiceRate,VispName,NasName,CenterName,Name,Family,ISFinishUser,CallingId,CalledId,FramedIP,NasIP,SRCNasIP,StartTime,LastUpdate,SessionTime(s),SendTr(B),ReceiveTr(B),NasPortId,NasPortType,ServiceType,FramedProtocol,URLReporting,InterimTime,InterimCount,TerminateCause',".
					"ColInitWidths='80,80,125,115,100,90,120,100,80,80,80,75,100,100,100,100,80,120,120,90,120,120,80,80,80,90,80,90,90,90',".
					"RenderColumnIds='Online_RadiusUser_Id,RUsername,LastDownloadSpeed,LastUploadSpeed,ServiceInfoName,ServiceRate,VispName,NasName,CenterName,Name,Family,ISFinishUser,CallingStationId,CalledStationId,FramedIpAddress,NasIpAddress,SRCNasIpAddress,AcctStartTime,DTRequest,AcctSessionTime,SendTr,ReceiveTr,NasPortId,NasPortType,ServiceType,FramedProtocol,URLReporting,InterimTime,InterimCount,TerminateCause'";
					DBInsert($sql);
				}
				else{
					$n=DBDelete("Delete from Hgrid_layout where ItemName='OnlineRadiusUser' and Reseller_Id in ($ResellerSet)");
					DSDebug(1,"No permit to 'Online.Radius.User.List'  -->  'OnlineRadiusUser' grid layout of $n reseller deleted");
				}
				echo "OK~";
				
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)


} catch (Exception $e) {
ExitError($e->getMessage());
}


function SetCRMUserGridLayout($Reseller_Id){
	DSDebug(1,"In function SetCRMUserGridLayout for Reseller_Id=$Reseller_Id");
	$RenderColumnIds=DBSelectAsString("Select RenderColumnIds from Hgrid_layout where Reseller_Id='$Reseller_Id' and ItemName='CRMUser'");
	if($RenderColumnIds=="")
		return;
	$CurrentFieldsArray=explode(",",$RenderColumnIds);
	DSDebug(1,DSPrintArray($CurrentFieldsArray));
	$PermissionArray=Array();
	$sql="Select PermitItemName,ISPermit from Hreseller_permit r join Hpermititem p on r.PermitItem_Id=p.PermitItem_Id where Reseller_Id='$Reseller_Id' and PermitItemName<>'CRM.User.List' and PermitItemName like 'CRM.User.%' order by p.PermitItem_Id asc";
	$n=CopyTableToArray($PermissionArray,$sql);
	
	for($i=0;$i<$n;++$i){
		$Field=end(explode(".",$PermissionArray[$i]['PermitItemName']));
		$ISPermit=$PermissionArray[$i]['ISPermit'];
		DSDebug(2,"ISPermit='$ISPermit'	field='$Field'");
		if($ISPermit=='Yes'){
			if(!in_array($Field,$CurrentFieldsArray)){
				DSDebug(2,"	Not exist in the list. Adding $Field to the end of list");
				array_push($CurrentFieldsArray,$Field);
			}
			else
				DSDebug(2,"	Already exists in the list. Do nothing...");
		}
		else{
			if(($key = array_search($Field, $CurrentFieldsArray)) !== false){
				DSDebug(2,"	Found in the list as $key. Removing it from the list");
				unset($CurrentFieldsArray[$key]);
			}
			else
				DSDebug(2,"	Already not exists in the list. Do nothing...");
		}
	}
	
	$GridLayoutArray=Array(
		"User_Id"			=>	Array(	"u.User_Id",		"{#stat_count} ردیف",	"80"	),
		"Username"			=>	Array(	"u.Username",		"Username",				"100"	),
		"UserType"			=>	Array(	"UserType",			"UserType",				"60"	),
		"PortStatus"		=>	Array( 	"PortStatus",		"PortStatus",			"90"	),
		"Name"				=>	Array(	"u.Name",			"Name",					"100"	),
		"Family"			=>	Array(	"u.Family",			"Family",				"150"	),
		"NationalCode"		=>	Array(	"u.NationalCode",	"NationalCode",			"100"	),
		"Mobile"			=>	Array(	"u.Mobile",			"Mobile",				"100"	),
		"Phone"				=>	Array(	"u.Phone",			"Phone",				"100"	),
		"Organization"		=>	Array(	"Organization",		"Organization",			"150"	),
		"PayBalance"		=>	Array(	"u.PayBalance",		"PayBalance",			"80"	),
		"EndDate"			=>	Array(	"EndDate",			"EndDate",				"80"	),
		"ExpirationDate"	=>	Array(	"u.ExpirationDate",	"ExpireDate",			"80"	),
		"BirthDate"			=>	Array(	"u.BirthDate",		"BirthDate",			"80"	),
		"Note"				=>	Array(	"Note",				"Note",					"300"	),
		"ResellerName"		=>	Array(	"r.ResellerName",	"ResellerName",			"100"	),
		"StatusDT"			=>	Array(	"StatusDT",			"StatusDT",				"120"	),
		"StatusName"		=>	Array(	"StatusName",		"StatusName",			"300"	),
		"StatusCreator"		=>	Array(	"sbr.ResellerName",	"StatusCreator",		"90"	),
		"Comment"			=>	Array(	"u.Comment",		"Comment",				"160"	),
		"Address"			=>	Array(	"u.Address",		"Address",				"300"	),
		"VispName"			=>	Array(	"VispName",			"VispName",				"110"	),
		"CenterName"		=>	Array(	"CenterName",		"CenterName",			"110"	),
		"SupporterName"		=>	Array(	"SupporterName",	"SupporterName",		"110"	),
		"ServiceName"		=>	Array(	"ServiceName",		"ServiceName",			"300"	)	
	);
	
	DSDebug(1,DSPrintArray($CurrentFieldsArray));
	$ColIds='';
	$ColHeaders='';
	$ColInitWidths='';
	$RenderColumnIds='';
	foreach($CurrentFieldsArray as $key=>$value){
		DSDebug(1,"key  =  $key	=>	value  =  $value");
		$ColIds.=",".$GridLayoutArray[$value][0];
		$ColHeaders.=",".$GridLayoutArray[$value][1];
		$ColInitWidths.=",".$GridLayoutArray[$value][2];
		$RenderColumnIds.=",".$value;
	}
	$ColIds=substr($ColIds,1);
	$ColHeaders=substr($ColHeaders,1);
	$ColInitWidths=substr($ColInitWidths,1);
	$RenderColumnIds=substr($RenderColumnIds,1);
	$sql="Update Hgrid_layout set ".
		"ColIds='$ColIds',".
		"ColHeaders='$ColHeaders',".
		"ColInitWidths='$ColInitWidths',".
		"RenderColumnIds='$RenderColumnIds' ".
		"where Reseller_Id='$Reseller_Id' and ItemName='CRMUser'";
	DBUpdate($sql);
}
?>
