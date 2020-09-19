<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSNasListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSNasListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Nas.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT Nas_Id,n.ISEnable,NasName,INET_NTOA(NasIP) as NasIP,NASInfoName From Hnas n left join Hnasinfo ni on n.NasInfo_id=ni.NasInfo_Id ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"Nas_Id","Nas_Id,ISEnable,NasName,NasIP,NASInfoName",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSNasListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Nas.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Nas_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$OnlineUserCount=DBSelectAsString("select count(1) from Tonline_radiususer where Nas_Id='".$NewRowInfo['Nas_Id']."'");
				if($OnlineUserCount>0)
					ExitError("این سرور ردیوس دارای تعداد کاربر آنلاین زیر است و حذف نمی شود</br>$OnlineUserCount");
				$n=DBDelete('delete from Hradius_nasaccess Where Nas_Id='.$NewRowInfo['Nas_Id']);
				$ar=DBDelete('delete from Hnas Where Nas_Id='.$NewRowInfo['Nas_Id']);
				if(($ar>0)||($n>0)) radiusapply();
				$ar=DBDelete('delete from Hnas_centeraccess Where Nas_Id='.$NewRowInfo['Nas_Id']);				
				logdbdelete($NewRowInfo,'Delete','Nas',$NewRowInfo['Nas_Id'],'');
				radiusapply();
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>