<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_Param_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.Param.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_Param_ListRender->List ********************************************");
				function color_rows($row){
					$ParamItemType = $row->get_value("ParamItemType");
					If($ParamItemType=='Reply'){
						$Format1="color:green";
					}
					else
						$Format1="";
					$data = $row->get_value("ParamStatus");
					if($data=='Passthrough-Yes')
						$Format2="font-weight:Normal";
					elseif($data=='Passthrough-No')
						$Format2="font-weight:Bold";
					else
						$Format2="font-style:Italic";
					$row->set_row_style("$Format1;$Format2");
				}				
				$sql="CREATE TEMPORARY TABLE ParamTemp (
						Param_Id INT(10),
						ParamItemGroupId INT(10),
						ParamItemGroup ENUM('Server','Center','Visp','Reseller','Service','Class','User'),
						ParamItemGroupName CHAR(128),
						ParamStatus ENUM('Passthrough-Yes','Passthrough-No','Disable'),
						ParamItemType ENUM('Helper','Reply','Acc','Auth'),
						ParamItemName CHAR(64),
						Value VARCHAR(254),
						ServiceInfoName CHAR(32))";
				$res=DBUpdate($sql);
				DSDebug(0,"QueryResult=$res\nCount of `ParamTemp` value:".DBSelectAsString("select count(*) from ParamTemp"));
				
				if((ISPermit(0,'Admin.Server.List'))and(ISPermit(0,'Admin.Server.Param.List'))){
					$sql="INSERT INTO ".
						"ParamTemp(Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName) ".
						"SELECT p.Param_Id,1,'Server','-',p.ParamStatus,pi.ParamItemType,pi.ParamItemName,p.Value,si.ServiceInfoName ".
						"from Hparam p ".
						"join Hparamitem pi on p.ParamItem_Id=pi.ParamItem_Id ".
						"join Hserviceinfo si on p.ServiceInfo_Id=si.ServiceInfo_Id ".
						"where p.TableName='Server'";
					DSDebug(1,"Filling temporary table with server param");
					DBUpdate($sql);
				}
				
				if((ISPermit(0,'Admin.Center.List'))and(ISPermit(0,'Admin.Center.Param.List'))){
					$sql="INSERT INTO ".
						"ParamTemp(Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName) ".
						"SELECT p.Param_Id,p.TableId,p.TableName,Hc.CenterName,p.ParamStatus,pi.ParamItemType,pi.ParamItemName,p.Value,si.ServiceInfoName ".
						"from Hparam p ".
						"join Hparamitem pi on p.ParamItem_Id=pi.ParamItem_Id ".
						"join Hserviceinfo si on p.ServiceInfo_Id=si.ServiceInfo_Id ".
						"join Hcenter Hc on p.TableName='Center' and p.TableId=Hc.Center_Id";
					DSDebug(1,"Filling temporary table with Center param");
					DBUpdate($sql);
				}
				
				if((ISPermit(0,'Admin.VISPs.List'))and(ISPermit(0,'Admin.VISPs.Param.List'))){
					$sql="INSERT INTO ".
						"ParamTemp(Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName) ".
						"SELECT p.Param_Id,p.TableId,p.TableName,Hv.VispName,p.ParamStatus,pi.ParamItemType,pi.ParamItemName,p.Value,si.ServiceInfoName ".
						"from Hparam p ".
						"join Hparamitem pi on p.ParamItem_Id=pi.ParamItem_Id ".
						"join Hserviceinfo si on p.ServiceInfo_Id=si.ServiceInfo_Id ".
						"join Hvisp Hv on p.TableName='Visp' and p.TableId=Hv.Visp_Id";
					DSDebug(1,"Filling temporary table with Visp param");
					DBUpdate($sql);
				}
				
				if((ISPermit(0,'CRM.Reseller.List'))and(ISPermit(0,'CRM.Reseller.Param.List'))){
					$sql="INSERT INTO ".
						"ParamTemp(Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName) ".
						"SELECT p.Param_Id,p.TableId,p.TableName,r.ResellerName,p.ParamStatus,pi.ParamItemType,pi.ParamItemName,p.Value,si.ServiceInfoName ".
						"from Hparam p ".
						"join Hparamitem pi on p.ParamItem_Id=pi.ParamItem_Id ".
						"join Hserviceinfo si on p.ServiceInfo_Id=si.ServiceInfo_Id ".
						"join Hreseller r on p.TableName='Reseller' and p.TableId=r.Reseller_Id ".
						"where $LResellerAccessAllow";
					DSDebug(1,"Filling temporary table with Reseller param");
					DBUpdate($sql);
				}
				
				if((ISPermit(0,'CRM.Service.List'))and(ISPermit(0,'CRM.Service.Param.List'))){
					$sql="INSERT INTO ".
						"ParamTemp(Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName) ".
						"SELECT p.Param_Id,p.TableId,p.TableName,Hs.ServiceName,p.ParamStatus,pi.ParamItemType,pi.ParamItemName,p.Value,si.ServiceInfoName ".
						"from Hparam p ".
						"join Hparamitem pi on p.ParamItem_Id=pi.ParamItem_Id ".
						"join Hserviceinfo si on p.ServiceInfo_Id=si.ServiceInfo_Id ".
						"join Hservice Hs on p.TableName='Service' and p.TableId=Hs.Service_Id and Hs.IsDel='No'";
					DSDebug(1,"Filling temporary table with Service param");
					DBUpdate($sql);
				}
				
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserParamList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Param.List'");
				
				$sql="INSERT INTO ".
					"ParamTemp(Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName) ".
					"SELECT p.Param_Id,p.TableId,p.TableName,Hu.UserName,p.ParamStatus,pi.ParamItemType,pi.ParamItemName,p.Value,si.ServiceInfoName ".
					"from Hparam p ".
					"join Hparamitem pi on p.ParamItem_Id=pi.ParamItem_Id ".
					"join Hserviceinfo si on p.ServiceInfo_Id=si.ServiceInfo_Id ".
					"join Huser Hu on p.TableName='User' and p.TableId=Hu.User_Id ".
					(($LReseller_Id!=1)?
						"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and ".
							"Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and ".
							"Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserParamList and Hrp2.ISPermit='Yes'"
						:""
					);
				DSDebug(1,"Filling temporary table with User param");
				DBUpdate($sql);
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by Param_Id Desc";
									
				$ColumnStr="Param_Id,ParamItemGroupId,ParamItemGroup,ParamItemGroupName,ParamStatus,ParamItemType,ParamItemName,Value,ServiceInfoName";
				
				$sql="select $ColumnStr from ParamTemp ".
					"where 1 ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"Param_Id",$ColumnStr,"","","color_rows");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
