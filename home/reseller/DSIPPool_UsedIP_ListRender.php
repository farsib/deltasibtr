<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSIPPool_UsedIP_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","insert","LoadPoolForm",'update','Delete'),0,0,0);

switch ($act) {
    case "list":
				//Permission -----------------
				exitifnotpermit(0,"Admin.User.IPPool.UsedIP.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$IPPool_Id=Get_Input('GET','DB','IPPool_Id','INT',1,4294967295,0,0);
				DSGridRender_Sql(100,"SELECT Online_UsedIP_Id,INET_NTOA(IP) As IP,IPPoolName,Username,{$DT}DateTimeStr(LastUsedDT) As LastUsedDT,ISFree ".
									"FROM Honline_usedip o_ui Left join Hippool ip on (o_ui.IPPool_Id=ip.IPPool_Id) ".
									"Left Join Huser u_u on(o_ui.User_Id=u_u.User_Id) ". 
									"Where o_ui.IPPool_Id=$IPPool_Id $sqlfilter $SortStr",
				"Online_UsedIP_Id",
				"Online_UsedIP_Id,IP,IPPoolName,Username,LastUsedDT,ISFree","","","");
       break;
   default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>