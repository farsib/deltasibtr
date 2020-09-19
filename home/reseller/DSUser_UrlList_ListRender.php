<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_UrlList_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","SelectDate",'DeleteAll','Delete', ""),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_UrlList_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.URL.UrlList.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				$Username=DBSelectAsString("SELECT Username From Huser Where User_Id=$User_Id");
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Where Username='$Username' Order by UrlSummary_Id desc limit 1");
				
				if(strlen($RepDate)!=8)//return empty 
					DSGridRender_Sql(100,"SELECT  '' as Id,'' as CDT,'' as SRCIP, '' as Domain,'' as Path from ".
						"(SELECT  '' as Id,'' as CDT,'' as SRCIP, '' as Domain,'' as Path) as tmp where (0=1)",
						"Id",
						"Id,CDT,SRCIP,Domain,Path",
						"","","");
				else{		
					$Tablename='deltasib_url.url'.$RepDate;
					DSGridRender_Sql(100,"SELECT  Id,{$DT}DateTimeStr(CDT) As CDT,INET_NtoA(SRCIP) As SRCIP,Domain,Path ".
						"From $Tablename u_p ".
						"where (Username='$Username') ".$sqlfilter." $SortStr ",
						"Id",
						"Id,CDT,SRCIP,Domain,Path",
						"","","");
				}		
       break;
    case "SelectDate":
				DSDebug(1,"DSUser_ServiceOther_ListRender-> SelectServiceOther *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.URL.UrlList.List");
				$Username=DBSelectAsString("SELECT Username From Huser Where User_Id=$User_Id");

				$sql="SELECT Distinct DATE_FORMAT(date,'%Y%m%d') As Date_Id,{$DT}DateStr(Date) As Date From deltasib_url.urlsummary Where Username='$Username' order by UrlSummary_Id";
				$options->render_sql($sql,"","Date_Id,Date","","");				

/*				$Username=DBSelectAsString("SELECT Username From Huser Where User_Id=$User_Id");
				$sql="Select UrlSummary_Id,Date From deltasib_url.urlsummary Order By UrlSummary_Id ".
				$options->render_sql($sql,"","UrlSummary_Id,Date","","");
*/				
        break;
	case "DeleteAll":
				DSDebug(1,"DSUser_UrlList_ListRender DeleteAll ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.URL.UrlList.DeleteAll");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				$Username=DBSelectAsString("SELECT Username From Huser Where User_Id=$User_Id");
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Where Username='$Username' Order by UrlSummary_Id desc limit 1");
				if(strlen($RepDate)==8){
					$Tablename='deltasib_url.url'.$RepDate;
					$ar=DBDelete("Delete From $Tablename Where Username='$Username' $sqlfilter"); 
				}
				else
					$ar=0;

				echo "OK~$ar~";
		break;
	case "Delete":
				DSDebug(1,"DSUser_UrlList_ListRender Delete ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.URL.UrlList.Delete");
				$Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$RepDate=Get_Input('GET','DB','RepDate','STR',0,10,0,0);
				if(strlen($RepDate)!=8)
					$RepDate=DBSelectAsString("Select DATE_FORMAT(date,'%Y%m%d') From deltasib_url.urlsummary Where Username='$Username' Order by UrlSummary_Id desc limit 1");
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