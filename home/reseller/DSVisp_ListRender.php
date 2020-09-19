<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSVispListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSVispListRender->List ********************************************");
				exitifnotpermit(0,"Admin.VISPs.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(-1,
					"SELECT Visp_Id,VispName,ISEnable,UsernamePattern,(select Count(1) from Huser u where u.Visp_Id=v.Visp_Id) as UserCount From Hvisp v Where 1 ".$sqlfilter." Group by v.Visp_Id $SortStr ",
					"Visp_Id","Visp_Id,VispName,ISEnable,UsernamePattern,UserCount",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSVispListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Visps.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Visp_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$n=DBSelectAsString("Select Count(*) from Huser Where Visp_Id=".$NewRowInfo['Visp_Id']);
				if($n>0) ExitError("$n کاربر در این ارائه دهنده مجازی هستند،لطفا ارائه دهنده مجازی آن ها را تغییر دهید");
				
				$NasInfoName=DBSelectAsString("Select NasInfoName from Hnasinfo Where DefVisp_Ids regexp '^".$NewRowInfo['Visp_Id'].",' or DefVisp_Ids regexp ',".$NewRowInfo['Visp_Id']."$' or DefVisp_Ids regexp ',".$NewRowInfo['Visp_Id'].",' Limit 1");
				if($NasInfoName<>'') ExitError("این ارائه دهنده مجازی توسط پارامتر سرور ردیوس زیر،برای ایجاد کاربر جدید استفاده می شود.برای حذف،آن را تغییر دهید</br>'$NasInfoName");
				
				$IsUsedForWebNewUser=DBSelectAsString("Select if(Param3=".$NewRowInfo['Visp_Id'].",'Yes','No') from Hserver Where PartName='WebNewUser' and Param1='Yes'");
				if($IsUsedForWebNewUser=='Yes') ExitError("این ارائه دهنده مجازی،توسط ثبت نام کاربر در پنل کاربری مورد استفاده است");

				DBDelete('delete from Hclass_vispaccess Where Visp_Id='.$NewRowInfo['Visp_Id']);
				DBDelete('delete from Hreseller_permit Where Visp_Id='.$NewRowInfo['Visp_Id']);
				DBDelete('delete from Hservice_vispaccess Where Visp_Id='.$NewRowInfo['Visp_Id']);
				DBDelete('delete from Hstatus_vispaccess Where Visp_Id='.$NewRowInfo['Visp_Id']);
				DBDelete("delete from Hparam Where TableName='Visp' and TableId=".$NewRowInfo['Visp_Id']);
				
				DBDelete('delete from Hvisp Where Visp_Id='.$NewRowInfo['Visp_Id']);

				logdbdelete($NewRowInfo,'Delete','Visp',$NewRowInfo['Visp_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>