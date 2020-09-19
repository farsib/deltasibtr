<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSChangeLogRender .................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();
require_once("../../lib/connector/grid_connector.php");
$act=Get_Input('GET','DB','act','ARRAY',array("list", ""),0,0,0);

try {
switch ($act) {
    case "list":
				//Permission -----------------
				$ChangeLogDataName=Get_Input('GET','DB','ChangeLogDataName','ARRAY',array('Feedback','ActiveDirectory','Supporter','Package','LoginTime','FinishRule','IPPool',"MikrotikRate","MikrotikRateValue","Server","TrafficRate","TimeRate","Center","Visp", "Reseller","Service","User","Status","Class","Radius","Nas","NasInfo","Gift","SupportItem","WebAccess","DebitControl","Terminal","OffFormula","SMSProvider","Notify","CalledId","WebService","NetworkIP"),0,0,0);
				$ChangeLogDataId=Get_Input('GET','DB','ChangeLogDataId','INT',1,4294967295,0,0);
				if($ChangeLogDataName=='User')
					exitifnotpermituser($ChangeLogDataId,"Visp.User.ChangeLog.List");
				else if($ChangeLogDataName=='Reseller'){
					if($ChangeLogDataId==$LReseller_Id)
						ExitError('شما نمی توانید اطلاعات خود را ویرایش کرده و یا ببینید');
					exitifnotpermit(0,"CRM.Reseller.ChangeLog.List");
					ExitIfNotPermitRowAccess("reseller",$ChangeLogDataId);
				}
				else if(in_array($ChangeLogDataName, Array('Reseller','Service','Feedback')))
					exitifnotpermit(0,"CRM.$ChangeLogDataName.ChangeLog.List");
				else if($ChangeLogDataName=='Visp')
					exitifnotpermit(0,"Admin.VISPs.ChangeLog.List");
				else if(in_array($ChangeLogDataName, Array('LoginTime','FinishRule','IPPool',"MikrotikRate","MikrotikRateValue","TrafficRate","TimeRate","Status","Class","Gift","SupportItem","WebAccess","DebitControl","OffFormula")))
					exitifnotpermit(0,"Admin.User.$ChangeLogDataName.ChangeLog.List");
				else if(in_array($ChangeLogDataName, Array("SMSProvider","Notify")))
					exitifnotpermit(0,"Admin.Message.$ChangeLogDataName.ChangeLog.List");
				else if($ChangeLogDataName=='Server'){
					exitifnotpermit(0,"Admin.Server.ChangeLog.List");
					if($ChangeLogDataId==1)
						exitifnotpermit(0,"Admin.Server.Param.View");
					else if($ChangeLogDataId==2)
						exitifnotpermit(0,"Admin.Server.HttpLog.View");
					else if(($ChangeLogDataId==3)&&($LReseller_Id!=1))
						ExitError("دیدن اطلاعات ادمین مجاز نیست");
					else if($ChangeLogDataId==4)
						exitifnotpermit(0,"Admin.Server.BackupWebAccess.View");
					else if($ChangeLogDataId==5)
						exitifnotpermit(0,"Admin.Server.WebNewUser.View");
					else if($ChangeLogDataId==6)
						exitifnotpermit(0,"Admin.Server.WebFeedback.View");
					else if($ChangeLogDataId==7)
						exitifnotpermit(0,"Admin.Server.GeneralNoneBlockIP.View");
					else if($ChangeLogDataId==8)
						exitifnotpermit(0,"Admin.Server.DeltasibServices.View");
					else if($ChangeLogDataId==9)
						exitifnotpermit(0,"Admin.Server.UserWebsitePasswordRecovery.View");
					else if($ChangeLogDataId==10)
						exitifnotpermit(0,"Admin.Server.WebService.View");
					else if($ChangeLogDataId==11)
						exitifnotpermit(0,"Admin.Server.Graph.View");
				}
				else
					exitifnotpermit(0,"Admin.$ChangeLogDataName.ChangeLog.List");
		
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"Select Logdb_Id,{$DT}DateTimeStr(LogDbCDT) as LogDbCDT,ResellerName,WebService_Username,INET_NTOA(ClientIP) as IP,LogType,ChildDataName,Comment ".
					"From Hlogdb l left join Hreseller r on l.Reseller_Id=r.Reseller_Id left join Hwebservice_user w on l.WebService_User_Id=w.WebService_User_Id Where (DataName='$ChangeLogDataName')and(DataId=$ChangeLogDataId)".$sqlfilter." $SortStr ",
					"Logdb_Id",
					"Logdb_Id,LogDbCDT,ResellerName,WebService_Username,IP,LogType,ChildDataName,Comment",
					"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}