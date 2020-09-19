<?php
ob_start();

try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSServerListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSServerListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Server.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				if($LReseller_Id==1)
					$sql="SELECT Server_Id,PartName From Hserver Where 1 ".$sqlfilter." $SortStr ";
				else
					$sql="SELECT Server_Id,PartName From Hserver s join ".
						"Hpermititem pi on pi.PermitItemName=concat('Admin.Server.',s.PartName,'.View') join ".
						"Hreseller_permit rp on rp.Reseller_Id='$LReseller_Id' and pi.PermitItem_Id=rp.PermitItem_Id ".
						"Where rp.ISPermit='Yes' ".$sqlfilter." $SortStr ";
				
				$ResArray=Array();
				$n=CopyTableToArray($ResArray,$sql);
				$header = "Content-type: text/xml; charset=utf-8";
				header($header);			
				$OutStr="<?xml version='1.0' encoding='utf-8' ?>";
				$OutStr.="<rows total_count='$n'>";
				for($i=0;$i<$n;++$i)
					$OutStr.="<row id='".$ResArray[$i]["Server_Id"]."'><cell><![CDATA[".$ResArray[$i]["Server_Id"]."]]></cell><cell><![CDATA[".$ResArray[$i]["PartName"]."]]></cell></row>";
				$OutStr.="</rows>";
				echo str_replace(Array("Param","HTTPLog","Admin","BackupWebAccess","WebNewUser","WebFeedback","GeneralNoneBlockIP","DeltasibServices","UserWebsitePasswordRecovery","WebService","Shahkar"),Array("پارامتر","لاگ سازمان تنظیم","مدیر","دسترسی به فایل های پشتیبانی","ثبت نام کاربر در پنل کاربری","سامانه شکایات و پیشنهادات پنل کاربران","آی پی هایی که مسدود نمی شوند","سرویس های دلتاسیب","بازیابی کلمه عبور در پنل کاربری","دسترسی به وب سرویس","شاهکار"),$OutStr);
				//DSGridRender_Sql(100,$sql,"Server_Id","Server_Id,PartName","","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
