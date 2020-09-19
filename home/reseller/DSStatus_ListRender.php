<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSStatusItemListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSStatusItemListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.Status.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				function color_rows($row){
					$PortStatus = $row->get_value("PortStatus");
					if($PortStatus=='Free'||$PortStatus=='Waiting'||$PortStatus=='GoToFree')
						$row->set_row_style("color:Chocolate");
					if($row->get_value("SMSExpireTime")=="0"){
						$row->set_cell_style("AfterStatusSMS","Background-Color:#999999");
						$row->set_cell_style("SMSExpireTime","Background-Color:#999999");
					}
				}
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,"SELECT st.Status_Id,st.StatusName,st.UserStatus,nst.StatusName as NewStatusName,st.PortStatus,st.InitialStatus,st.CanWebLogin,st.CanAddService,st.IsBusyPort,st.ResellerAccess,st.VispAccess,if(st.SMSExpireTime=0,' -- No SMS set -- ',st.AfterStatusSMS) as AfterStatusSMS,st.SMSExpireTime From Hstatus st left join Hstatus nst on st.NewStatus_Id=nst.Status_Id Where 1 ".$sqlfilter." $SortStr ",
					"Status_Id",
					"Status_Id,StatusName,UserStatus,NewStatusName,PortStatus,InitialStatus,CanWebLogin,CanAddService,IsBusyPort,ResellerAccess,VispAccess,AfterStatusSMS,SMSExpireTime",
					"","","color_rows");
       break;
	   case "Delete":
				DSDebug(1,"DSClass_ListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.Status.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Status_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				if(($NewRowInfo['Status_Id']==1)||($NewRowInfo['Status_Id']==2))
					ExitError("این وضعیت توسط سیستم استفاده می شود و قابل حذف نیست");
				
				$Username=DBSelectAsString("Select Username from Huser Where Status_Id=".$NewRowInfo['Status_Id']." Limit 1");
				if($Username<>'') ExitError("این وضعیت توسط کاربر '$Username' استفاده می شود،لطفا وضعیت این کاربر را تغییر دهید");
				
				$StatusName=DBSelectAsString("Select StatusName from Hstatus Where NewStatus_Id=".$NewRowInfo['Status_Id']." Limit 1");
				if($StatusName<>'') ExitError("وضعیت انتخاب شده به عنوان وضعیت جدید توسط وضعیت '$StatusName' استفاده می شود،لطفا وضعیت جدید آن را تغییر دهید");
				
				$NasInfoName=DBSelectAsString("Select NasInfoName from Hnasinfo Where DefStatus_Id=".$NewRowInfo['Status_Id']." Limit 1");
				if($NasInfoName<>'') ExitError("این وضعیت توسط پارامتر ردیوس زیر به عنوان وضعیت پیش فرض برای ایجاد کاربر جدید استفاده می شود،لطفا آن را تغییر دهید</br>'$NasInfoName'");
				
				$IsUsedForWebNewUser=DBSelectAsString("Select if(Param8=".$NewRowInfo['Status_Id'].",'Yes','No') from Hserver Where PartName='WebNewUser' and Param1='Yes'");
				if($IsUsedForWebNewUser=='Yes') ExitError("این وضعیت توسط ثبت نام کاربر در پنل کاربری استفاده می شود");
				
				DBDelete('delete from Hstatus_reselleraccess Where Status_Id='.$NewRowInfo['Status_Id']);
				DBDelete('delete from Hstatus_statusto Where Status_Id='.$NewRowInfo['Status_Id'].' or StatusTo_Id='.$NewRowInfo['Status_Id']);
				DBDelete('delete from Hstatus_vispaccess Where Status_Id='.$NewRowInfo['Status_Id']);
				DBDelete('delete from Huser_status Where Status_Id='.$NewRowInfo['Status_Id']);
				
				$ar=DBDelete('delete from Hstatus Where Status_Id='.$NewRowInfo['Status_Id']);
				logdbdelete($NewRowInfo,'Delete','Status',$NewRowInfo['Status_Id'],'');
				echo "OK~";
		break;	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>