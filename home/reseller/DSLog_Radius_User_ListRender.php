<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSLog_Radius_User_ListRender.php.........................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');

$act=Get_Input('GET','DB','act','ARRAY',array("list","BlockCallerId"),0,0,0);
PrintInputGetPost();
switch ($act) {
    case "list":
				DSDebug(0,"DSLog_Radius_User_ListRender.php->List ********************************************");
				//Permission -----------------
				exitifnotpermit(0,"Log.Radius.User.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				function color_rows($row){
					global $CurrentDate;
					$LogType = $row->get_value("LogType1");
					if((stripos($LogType,'Fail')!==false)||(stripos($LogType,'Block')!==false))
						$row->set_row_style("color:red");
					elseif(stripos($LogType,'OK')!==false)
						$row->set_row_style("color:green");
					else
						$row->set_row_style("color:Black");
					$row->set_value("Comment1",htmlspecialchars($row->get_value("Comment1")));
				}
				
				$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");

				if($LReseller_Id==1) 
				DSGridRender_Sql(100,"SELECT User_LogId,User_Id,Username,{$DT}DateTimeStr(CDT1) as CDT1,LogType1,CallingStationId1,NasName As NasName1,Comment1 ".
									"FROM  Tuser_log u_l Left join Hnas n on  (u_l.SRCNasIpAddress1=n.NasIP) ".
									" Where 1 ".$sqlfilter." $SortStr ",
									"User_LogId",
									"User_LogId,User_Id,Username,CDT1,LogType1,CallingStationId1,NasName1,Comment1","","","color_rows");
				else
				DSGridRender_Sql(100,"SELECT User_LogId,u.User_Id,u_l.Username,{$DT}DateTimeStr(CDT1) as CDT1,LogType1,CallingStationId1,NasName As NasName1,Comment1 ".
									"FROM  Tuser_log u_l Left join Hnas n on  (u_l.SRCNasIpAddress1=n.NasIP) ".
									"Left join Huser u on (u_l.User_Id=u.User_Id) ".
									"Left Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) ".
									" Where (ISPermit='Yes') ".$sqlfilter." $SortStr ",
									"User_LogId",
									"User_LogId,User_Id,Username,CDT1,LogType1,CallingStationId1,NasName1,Comment1","","","color_rows");
				
/*				
				if($LReseller_Id==1) 
				DSGridRender_Sql(100,"SELECT User_LogId,Username,{$DT}DateTimeStr(CDT1) as CDT1,LogType1,CallingStationId1,NasName As NasName1,Comment1,{$DT}DateTimeStr(CDT2) As CDT2,Comment2,{$DT}DateTimeStr(CDT3) As CDT3,Comment3 ".
									"FROM  Tuser_log u_l Left join Hnas n on  (u_l.SRCNasIpAddress1=n.NasIP) ".
									" Where 1 ".$sqlfilter." $SortStr ",
									"User_LogId",
									"User_LogId,Username,CDT1,LogType1,CallingStationId1,NasName1,Comment1,CDT2,Comment2,CDT3,Comment3","","","");
				else
				DSGridRender_Sql(100,"SELECT User_LogId,u_l.Username,{$DT}DateTimeStr(CDT1) as CDT1,LogType1,CallingStationId1,NasName As NasName1,Comment1,{$DT}DateTimeStr(CDT2) As CDT2,Comment2,{$DT}DateTimeStr(CDT3) As CDT3,Comment3 ".
									"FROM  Tuser_log u_l Left join Hnas n on  (u_l.SRCNasIpAddress1=n.NasIP) ".
									"Left join Huser u on (u_l.User_Id=u.User_Id) ".
									"Left Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id=$PermitItem_Id_Of_Visp_User_List) ".
									" Where (ISPermit='Yes') ".$sqlfilter." $SortStr ",
									"User_LogId",
									"User_LogId,Username,CDT1,LogType1,CallingStationId1,NasName1,Comment1,CDT2,Comment2,CDT3,Comment3","","","");
*/									
       break;
	case "BlockCallerId":
				DSDebug(0,"DSLog_Radius_User_ListRender.php->BlockCallerId ********************************************");
				exitifnotpermit(0,"Log.Radius.User.BlockCallerId");
				$NewRowInfo=array();
				$NewRowInfo['CallerId']=Get_Input('POST','DB','CallerId','STR',1,17,0,0);
				$sql= "insert Hcalleridblock set ";
				$sql.="CallerId='".$NewRowInfo['CallerId']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['CallerIdBlock_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','BlockCallerId',$NewRowInfo['CallerIdBlock_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
