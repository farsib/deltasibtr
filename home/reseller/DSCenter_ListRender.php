<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSCenterListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSCenterListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Center.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(-1,
					"SELECT Center_Id,CenterName,TotalPort,BadPort,".
					"TotalPort-BadPort-(SELECT Count(*) from Huser u left join Hstatus s on u.Status_Id=s.Status_Id Where Center_Id=c.Center_Id and IsBusyPort='Yes') as FreePort,".
					"(SELECT Count(*) from Huser u left join Hstatus s on u.Status_Id=s.Status_Id Where Center_Id=c.Center_Id and PortStatus='Reserve') as ReservePort,".
					"(SELECT Count(*) from Huser u left join Hstatus s on u.Status_Id=s.Status_Id Where Center_Id=c.Center_Id and PortStatus='GoToFree') as GoToFreePort,".
					"(SELECT Count(*) from Huser u left join Hstatus s on u.Status_Id=s.Status_Id Where Center_Id=c.Center_Id and PortStatus='Waiting') as WaitingPort,".
					"(SELECT Count(*) from Huser u left join Hstatus s on u.Status_Id=s.Status_Id Where Center_Id=c.Center_Id) as UserCount,".
					"UsernamePattern,Country,State,City,Center,PopSite,NOE ".
					"From Hcenter c Where 1 ".$sqlfilter." group by c.Center_Id $SortStr ",
					"Center_Id","Center_Id,CenterName,TotalPort,BadPort,FreePort,ReservePort,GoToFreePort,WaitingPort,UserCount,UsernamePattern,Country,State,City,Center,PopSite,NOE",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSCenterListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Center.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Center_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);

				$n=DBSelectAsString("Select Count(*) from Huser Where Center_Id=".$NewRowInfo['Center_Id']);
				if($n>0) ExitError("$n کاربر در این مرکز هستند،لطفا مرکز آن ها را تغییر دهید");

				$NasInfoName=DBSelectAsString("Select NasInfoName from Hnasinfo Where DefCenter_Id=".$NewRowInfo['Center_Id']." Limit 1");
				if($NasInfoName<>'') ExitError("این مرکز توسط پارامتر ردیوس زیر به عنوان مرکز پیش فرض برای ایجاد کاربر جدید استفاده می شود،لطفا آن را تغییر دهید</br>'$NasInfoName'");
				
				$IsUsedForWebNewUser=DBSelectAsString("Select if(Param4=".$NewRowInfo['Center_Id'].",'Yes','No') from Hserver Where PartName='WebNewUser' and Param1='Yes'");
				if($IsUsedForWebNewUser=='Yes') ExitError("این مرکز توسط ثبت نام کاربر در پنل کاربری استفاده می شود");				
				
				DBDelete("delete from Hparam Where TableName='Center' and TableId=".$NewRowInfo['Center_Id']);
				
				$ar=DBDelete('delete from Hcenter Where Center_Id='.$NewRowInfo['Center_Id']);
				logdbdelete($NewRowInfo,'Delete','Center',$NewRowInfo['Center_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>