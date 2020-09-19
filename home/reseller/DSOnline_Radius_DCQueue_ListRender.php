<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_Radius_DCQueue_.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				//Permission -----------------
				exitifnotpermit(0,"Online.Radius.DCQueue.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				if($LReseller_Id==1)
					DSGridRender_Sql(100,"SELECT Online_DCQueue_Id,{$DT}DateTimeStr(CDT) as CDT,".
					"If(o_dc.RUsername<>'',o_dc.RUsername,o_ru.RUsername) RUsername,".
					"If(o_dc.SRCNasIpAddress>0,o_dc.SRCNasIpAddress,o_ru.NasIpAddress) as NasIpAddress, ".
					"NasName ".
					"FROM Tonline_dcqueue o_dc left join Tonline_radiususer o_ru on o_dc.Online_RadiusUser_Id=o_ru.Online_RadiusUser_Id ".
					" Left join Hnas n on(o_ru.Nas_Id=n.Nas_Id) ".
					"Where 1 $sqlfilter $SortStr",
					"Online_DCQueue_Id",
					"Online_DCQueue_Id,CDT,RUsername,NasName,NasIpAddress","","","");
				else{
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					DSGridRender_Sql(100,"SELECT Online_DCQueue_Id,{$DT}DateTimeStr(CDT) as CDT,".
					"If(o_dc.RUsername<>'',o_dc.RUsername,o_ru.RUsername) RUsername,".
					"If(o_dc.SRCNasIpAddress>0,o_dc.SRCNasIpAddress,o_ru.NasIpAddress) as NasIpAddress, ".
					"NasName ".
					"From Tonline_dcqueue o_dc left join Tonline_radiususer o_ru on o_dc.Online_RadiusUser_Id=o_ru.Online_RadiusUser_Id ".
					"Left join Huser Mu_u On (Mu_u.User_Id=o_ru.User_Id) ".
					"left join Hreseller r on Mu_u.Reseller_Id=r.Reseller_Id  ".
					"Left join Hnas n on(o_ru.Nas_Id=n.Nas_Id) ".
					"Left Join Hreseller_permit rgp on (Mu_u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) ".
					"Where (ISPermit='Yes') $sqlfilter $SortStr",
					"Online_DCQueue_Id",
					"Online_DCQueue_Id,CDT,RUsername,NasName,NasIpAddress","","","");
				}
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
