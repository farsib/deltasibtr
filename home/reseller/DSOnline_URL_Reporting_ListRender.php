<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_URL_ReportingListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list",'DeleteSession','DisconnectUser','DisconnectAllUser'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(1,"DSOnline_URL_ReportingListRender List ******************************************");
				//Permission -----------------
				exitifnotpermit(0,"Online.URL.Reporting.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$todaydate=date('Ymd');
				$TableName='url'.$todaydate;
				$n = DBSelectAsString("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'deltasib_url' AND table_name = '$TableName'");
				if($n==0) DBUpdate("CREATE TABLE IF NOT EXISTS  deltasib_url.{$TableName}(id int(10) unsigned NOT NULL AUTO_INCREMENT, CDT datetime NOT NULL,  User_Id int(10) unsigned NOT NULL DEFAULT '0', Username varchar(32) NOT NULL DEFAULT '',".
									"SRCIP int(10) unsigned NOT NULL DEFAULT '0',  Domain varchar(64) NOT NULL DEFAULT '',  Path varchar(256) NOT NULL DEFAULT '',  PRIMARY KEY (id)) ENGINE=Aria ");
				if($LReseller_Id!=1){
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$PermitItem_Id_Of_Visp_User_URL_UrlList_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.URL.UrlList.List'");
					$sql="Create temporary table PermittedUser ".
					"select Username as UN from Huser u ".
					"Join Hreseller_permit Hrp1 on (Hrp1.Reseller_Id=$LReseller_Id)and(Hrp1.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List)and(Hrp1.Visp_Id=u.Visp_Id) and (Hrp1.ISPermit='Yes') ".
					"Join Hreseller_permit Hrp2 on (Hrp2.Reseller_Id=$LReseller_Id)and(Hrp2.PermitItem_Id=$PermitItem_Id_Of_Visp_User_URL_UrlList_List)and(Hrp2.Visp_Id=u.Visp_Id) and (Hrp2.ISPermit='Yes') ";
					$n=DBUpdate($sql);
					DBUpdate("Alter table PermittedUser add index un(UN)");
					DSGridRender_Sql(100,"Select Id,{$DT}DateTimeStr(CDT) As CDT,Username,INET_NTOA(SRCIP) As SRCIP,REPLACE(Domain,'.',' .') as Domain,Path ".
						"From deltasib_url.$TableName u_p join PermittedUser u on u_p.Username=u.UN ".
						"Where 1 $sqlfilter $SortStr",
						"Id",
						"Id,CDT,Username,SRCIP,Domain,Path","","","");
				}
				else{
				DSGridRender_Sql(100,"Select Id,{$DT}DateTimeStr(CDT) As CDT,Username,INET_NTOA(SRCIP) As SRCIP,REPLACE(Domain,'.',' .') as Domain,Path ".
						"From deltasib_url.$TableName ".
						"Where 1 and domain not like '%1%' $sqlfilter $SortStr",
						"Id",
						"Id,CDT,Username,SRCIP,Domain,Path","","","");
/*
				DSGridRender_Sql(100,"Select Id,{$DT}DateTimeStr(CDT) As CDT,Username,INET_NTOA(SRCIP) As SRCIP,Domain,Path ".
						"From deltasib_url.$TableName ".
						"Where 1 $sqlfilter $SortStr",
						"Id",
						"Id,CDT,Username,SRCIP,Domain,Path","","","");
						*/
				}
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
