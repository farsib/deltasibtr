<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOnline_WebResellerListRender.........................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				//Permission -----------------
				exitifnotpermit(0,"Log.Reseller.Security.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				if($LReseller_Id==1)
					$sql="SELECT LogSecurity_Id,".
						"{$DT}DateTimeStr(LogSecurityCDT) as LogSecurityCDT,".
						"ResellerName,Username,INET_NTOA(ClientIp) as IP,LogType,ls.Comment ".
						"FROM  Hlogsecurity ls ".
						"left join Hreseller r on ls.Reseller_Id=r.Reseller_Id ".
						"left join Huser u on ls.User_Id=u.User_Id ".
						"where 1 ".$sqlfilter." $SortStr ";
				else{
					$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$sql="SELECT LogSecurity_Id,".
						"{$DT}DateTimeStr(LogSecurityCDT) as LogSecurityCDT,".
						"ResellerName,Username,INET_NTOA(ClientIp) as IP,LogType,ls.Comment ".
						"FROM  Hlogsecurity ls ".
						"left join Hreseller r on ls.Reseller_Id=r.Reseller_Id AND $LResellerAccessAllow ".
						"left join Huser u on ls.User_Id=u.User_Id ".
						"left join Hreseller_permit Hrp on Hrp.Reseller_Id=$LReseller_Id and Hrp.Visp_Id=u.Visp_Id and Hrp.PermitItem_Id=$VispUserList and Hrp.ISPermit='Yes' ".
						"where 1 ".$sqlfilter." $SortStr ";
				}
						
				DSGridRender_Sql(100,$sql,"LogSecurity_Id","LogSecurity_Id,LogSecurityCDT,ResellerName,Username,IP,LogType,Comment","","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
