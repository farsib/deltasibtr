<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSReseller_Permit_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$Reseller_Id=Get_Input('GET','DB','ParentId','INT',2,4294967295,0,0);
if($Reseller_Id==$LReseller_Id)	
	ExitError('You can not Edit or View Your Info!!!');
exitifnotpermit(0,"CRM.Reseller.Permit.List");
ExitIfNotPermitRowAccess("reseller",$Reseller_Id);

$act=Get_Input('GET','DB','act','ARRAY',array("VispList","LoadCopyInformation"),0,0,0);
switch ($act){
	case 'VispList':
		DSDebug(0,"DSReseller_Permit_ListRender -> VispList..............................................................................");

		
		$ParentReseller_Id=DBSelectAsString("select ParentReseller_Id from Hreseller where Reseller_Id=$Reseller_Id");
		
		$sql="Select count(1)";
		$sql.=" from Hreseller_permit rp";
		// $sql.=" join Hreseller rg on rp.Reseller_Id=rg.Reseller_Id";
		$sql.=" join Hreseller_permit ParentRGP on (ParentRGP.Reseller_Id='$ParentReseller_Id') and (ParentRGP.Visp_Id=rp.Visp_Id) and (rp.PermitItem_Id=ParentRGP.PermitItem_Id) and (ParentRGP.ISPermit='Yes')";
		$sql.=" join Hpermititem pi on pi.PermitItem_Id=rp.PermitItem_Id";
		$sql.=" Where(rp.reseller_Id=$Reseller_Id) and (rp.Visp_Id=0)";
		$GeneralPermitCount=DBSelectAsString($sql);
		
		$sql ="Select rp.Visp_Id as Visp_Id,VispName,count(1) as PermitCount";
		$sql.=" from Hreseller_permit rp";
		// $sql.=" join Hreseller rg on rp.Reseller_Id=rg.Reseller_Id";
		$sql.=" join Hreseller_permit ParentRGP on (ParentRGP.Reseller_Id='$ParentReseller_Id') and (ParentRGP.Visp_Id=rp.Visp_Id) and (rp.PermitItem_Id=ParentRGP.PermitItem_Id) and (ParentRGP.ISPermit='Yes')";
		$sql.=" join Hpermititem pi on pi.PermitItem_Id=rp.PermitItem_Id";
		$sql.=" join Hvisp v on rp.Visp_Id=v.Visp_Id";
		$sql.=" Where (rp.reseller_Id=$Reseller_Id) group by VispName Asc with rollup";
		$n=CopyTableToArray($Visp_IdArray,$sql);		
		
		
		header ('Content-Type:text/xml');
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<menu>";
		if(($GeneralPermitCount<=0)&&($n<=0))
			echo "<item id='NoPermit' text='No permit found for this reseller' img='dsMenu_ResellerPermitNoPermit.png'/>";
		else{
			if($GeneralPermitCount>0){
				echo "<item id='General' text='عمومی ($GeneralPermitCount مورد".")' img='dsMenu_ResellerPermitVisps.png'>";	
					echo "<item id='0' text='عمومی(".$GeneralPermitCount.")' img='dsMenu_ResellerPermitVisps.png'/>";
				echo "</item>";
			}
			else{
				echo "<item id='General' text='General(0 item)' imgdis='dsMenu_ResellerPermitVisps_dis.png' enabled='false'/>";
			}
			
			if($n>0){
				$Count=$n-1;//to remove last row containing rollup informations
				$VispPermitCount=$Visp_IdArray[$Count]['PermitCount'];
				echo "<item id='VISP' text='ارائه دهنده مجازی اینترنت"." ($VispPermitCount مورد".")' img='dsMenu_ResellerPermitVisps.png'>";
				for($i=0;$i<$Count;++$i)
					echo "<item id='".$Visp_IdArray[$i]['Visp_Id']."' text='".$Visp_IdArray[$i]['VispName']."(".$Visp_IdArray[$i]['PermitCount'].")' img='dsMenu_ResellerPermitVisps.png'/>";
				echo "</item>";
				if($Count>1){
					echo "<item id='sep_top_1' type='separator'/>";
					echo "<item id='CopyPermissions' text='کپی دسترسی' img='dsMenu_ResellerPermitCopyPermissions.png'/>";
				}
				
				echo "<item id='sep_top_2' type='separator'/>";
				echo "<item id='CloseAllTabs' text='بستن برگه ها' img='ds_tow_CloseAllTabs.png' imgdis='ds_tow_CloseAllTabs_dis.png' enabled='false'/>";
			}
			else{
				echo "<item id='VISP' text='VISP(0 item)' imgdis='dsMenu_ResellerPermitVisps_dis.png' enabled='false'/>";
			}
		}
		echo "</menu>";
	break;
//----------------------------------------------------------------------------------------------------------
	case "LoadCopyInformation":
		DSDebug(1,"DSReseller_Permit_ListRender LoadCopyInformation ******************************************");
		exitifnotpermit(0,"CRM.Reseller.Permit.Edit");
		$Reseller_Id=Get_Input('GET','DB','ParentId','INT',2,4294967295,0,0);
		$From_Visp_Id=Get_Input('POST','DB','From_Visp_Id','INT',1,4294967295,0,0);
		
		$ParentReseller_Id=DBSelectAsString("select ParentReseller_Id from Hreseller where Reseller_Id=$Reseller_Id");
		
		$sql="select count(1) from Hreseller_permit rp ".
		"join Hreseller_permit ParentRGP on (ParentRGP.Reseller_Id='$ParentReseller_Id') and (ParentRGP.Visp_Id=rp.Visp_Id) and (rp.PermitItem_Id=ParentRGP.PermitItem_Id) and (ParentRGP.ISPermit='Yes') ".
		"where rp.Reseller_Id='$Reseller_Id' and rp.Visp_Id='$From_Visp_Id' and rp.ISPermit='Yes'";
		$Permitted=DBSelectAsString($sql);
		
		$sql="select count(1) from Hreseller_permit rp ".
		"join Hreseller_permit ParentRGP on (ParentRGP.Reseller_Id='$ParentReseller_Id') and (ParentRGP.Visp_Id=rp.Visp_Id) and (rp.PermitItem_Id=ParentRGP.PermitItem_Id) and (ParentRGP.ISPermit='Yes') ".
		"where rp.Reseller_Id='$Reseller_Id' and rp.Visp_Id='$From_Visp_Id' and rp.ISPermit='No'";
		$NotPermitted=DBSelectAsString($sql);
		
		echo "OK~$Permitted~$NotPermitted";
	break;
	default :
		echo "~Unknown Request";	
}
}
catch (Exception $e) {
	ExitError($e->getMessage());
}
?>

