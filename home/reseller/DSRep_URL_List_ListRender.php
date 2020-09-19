<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_URL_ReportingListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

$act=Get_Input('GET','DB','act','ARRAY',array("list",'SelectDate','DeleteAll','Delete'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_UrlList_ListRender->List ********************************************");
				exitifnotpermit(0,"Report.URL.List.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Order by UrlSummary_Id desc limit 1");
				
				if(strlen($RepDate)!=8)//return empty 
					DSGridRender_Sql(100,"SELECT  '' as Id,'' as CDT,'' as Username,'' as SRCIP, '' as Domain,'' as Path from ".
						"(SELECT  '' as Id,'' as CDT,'' As Username,'' as SRCIP, '' as Domain,'' as Path) as tmp where (0=1)",
						"Id",
						"Id,CDT,SRCIP,Domain,Path",
						"","","");
				else{
					$Tablename='deltasib_url.url'.$RepDate;
					if($LReseller_Id!=1){
						$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
						$PermitItem_Id_Of_Visp_User_URL_UrlList_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.URL.UrlList.List'");
						$sql="Create temporary table PermittedUser as ".
						"select Username as UN from Huser u ".
						"Join Hreseller_permit Hrp1 on (Hrp1.Reseller_Id=$LReseller_Id)and(Hrp1.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List)and(Hrp1.Visp_Id=u.Visp_Id) and (Hrp1.ISPermit='Yes') ".
						"Join Hreseller_permit Hrp2 on (Hrp2.Reseller_Id=$LReseller_Id)and(Hrp2.PermitItem_Id=$PermitItem_Id_Of_Visp_User_URL_UrlList_List)and(Hrp2.Visp_Id=u.Visp_Id) and (Hrp2.ISPermit='Yes') ";
						$n=DBUpdate($sql);
						DBUpdate("Alter table PermittedUser add index un(UN)");
						DSGridRender_Sql(100,"SELECT  Id,{$DT}DateTimeStr(CDT) As CDT,Username,INET_NtoA(SRCIP) As SRCIP,REPLACE(Domain,'.',' .') as Domain,Path ".
						"From $Tablename u_p join PermittedUser u on u_p.Username=u.UN ".
						"where 1 ".$sqlfilter." $SortStr ",
						"Id",
						"Id,CDT,Username,SRCIP,Domain,Path",
						"","","");
					}
					else{
						DSGridRender_Sql(100,"SELECT  Id,{$DT}DateTimeStr(CDT) As CDT,Username,INET_NtoA(SRCIP) As SRCIP,REPLACE(Domain,'.',' .') as Domain,Path ".
						"From $Tablename u_p ".
						"where 1 ".$sqlfilter." $SortStr ",
						"Id",
						"Id,CDT,Username,SRCIP,Domain,Path",
						"","","");
					}
				}		
       break;
    case "SelectDate":
				DSDebug(1,"DSUser_UrlList_ListRender-> SelectDate *****************");
				exitifnotpermit(0,"Report.URL.List.List");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");

				$sql="SELECT Distinct DATE_FORMAT(date,'%Y%m%d') As Date_Id,{$DT}DateStr(Date) As Date From deltasib_url.urlsummary order by UrlSummary_Id";
				$options->render_sql($sql,"","Date_Id,Date","","");				

/*				$Username=DBSelectAsString("SELECT Username From Huser Where User_Id=$User_Id");
				$sql="Select UrlSummary_Id,Date From deltasib_url.urlsummary Order By UrlSummary_Id ".
				$options->render_sql($sql,"","UrlSummary_Id,Date","","");
*/				
        break;
	case "DeleteAll":
				DSDebug(1,"DSUser_UrlList_ListRender DeleteAll ******************************************");
				exitifnotpermit(0,"Report.URL.List.List.DeleteAll");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Order by UrlSummary_Id desc limit 1");
				if(strlen($RepDate)==8){
					$Tablename='deltasib_url.url'.$RepDate;
					$ar=DBDelete("Delete From $Tablename Where 1 $sqlfilter");
				}
				else
					$ar=0;
				echo "OK~$ar~";
		break;
	case "Delete":
				DSDebug(1,"DSUser_UrlList_ListRender Delete ******************************************");
				exitifnotpermit(0,"Report.URL.List.List.Delete");
				$Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Order by UrlSummary_Id desc limit 1");
				$Tablename='deltasib_url.url'.$RepDate;
				
				$ar=DBDelete("Delete From $Tablename Where Id=$Id"); 
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
