<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_URL_TopSite_ListRender.........................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list",'SelectDate'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_URL_TopSite_ListRender->List ********************************************");
				exitifnotpermit(0,"Report.URL.TopSite.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Order by UrlSummary_Id desc limit 1");
				if(strlen($RepDate)!=8)//return empty  
					DSGridRender_Sql(100,"SELECT  '' as Id,'' as Date,'' as Domain,'' as NumReq from ".
						"(SELECT  '' as Id,'' as Date, '' as Domain,'' as NumReq) as tmp where (0=1)",
						"Id",
						"Id,Date,Domain,NumReq",
						"","","");
				else{
					$Tablename='deltasib_url.urltopsitesummary'.$RepDate;
					DSGridRender_Sql(-1,"SELECT  Id ,{$DT}DateStr(DATE_FORMAT('$RepDate','%Y-%m-%d')) As Date,Domain,NumReq ".
						"From $Tablename ".
						"order by NumReq Desc",
						"Id",
						"Id,Date,Domain,NumReq",
						"","","");
				}
		break;
    case "SelectDate":
				DSDebug(1,"DSRep_URL_TopSite_ListRender-> SelectDate *****************");
				exitifnotpermit(0,"Report.URL.TopSite.List");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");

				$sql="SELECT Distinct DATE_FORMAT(date,'%Y%m%d') As Date_Id,{$DT}DateStr(Date) As Date From deltasib_url.urlsummary order by UrlSummary_Id";
				$options->render_sql($sql,"","Date_Id,Date","","");				

        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
