<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSSupporterListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSSupporterListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Supporter.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT Supporter_Id,ISEnable,SupporterName,UsernamePattern,PerPortDailyCF,PerPaymentCF ".
					"From Hsupporter ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"Supporter_Id","Supporter_Id,ISEnable,SupporterName,UsernamePattern,PerPortDailyCF,PerPaymentCF",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSSupporterListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Supporter.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Supporter_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$n=DBSelectAsString("Select Count(*) from Huser Where Supporter_Id=".$NewRowInfo['Supporter_Id']);
				if($n>0) ExitError("$n کاربر از این پشتیبان هستند،لطفا پشتیبان آن ها را تغییر دهید");

				$NasInfoName=DBSelectAsString("Select NasInfoName from Hnasinfo Where DefSupporter_Id=".$NewRowInfo['Supporter_Id']." Limit 1");
				if($NasInfoName<>'') ExitError("این پشتیبان توسط پارامتر ردیوس زیر به عنوان پشتیبان پیش فرض  برای ایجاد کاربر استفاده می شود،لطفا آن را تغییر دهید</br>'$NasInfoName'");
				
				$IsUsedForWebNewUser=DBSelectAsString("Select if(Param5=".$NewRowInfo['Supporter_Id'].",'Yes','No') from Hserver Where PartName='WebNewUser' and Param1='Yes'");
				if($IsUsedForWebNewUser=='Yes') ExitError("این پشتیبان توسط ثبت نام کاربر در پنل کاربری استفاده می شود");				
				
				$ar=DBDelete('delete from Hsupporter Where Supporter_Id='.$NewRowInfo['Supporter_Id']);
				logdbdelete($NewRowInfo,'Delete','Supporter',$NewRowInfo['Supporter_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>