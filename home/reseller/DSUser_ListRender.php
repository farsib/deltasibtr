<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUserListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list",'Delete','ChangeLayout'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"		>>> List");
				exitifnotpermit(0,"CRM.User.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);

				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$DTNow=DBSelectAsString("Select concat(ShdateNow(),' ',CURRENT_TIME())");
				function color_rows($row){
					global $CurrentDate,$DTNow;
					if($row->get_value("EndDate")<=$DTNow){
						$row->set_row_style("color:red");
						$row->set_cell_style("EndDate","color:red;font-weight:bold");
					}
					elseif(($row->get_value("ExpirationDate")!="")&&($row->get_value("ExpirationDate")<=$CurrentDate)){
						$row->set_row_style("color:red");
						$row->set_cell_style("ExpirationDate","color:red;font-weight:bold");
					}
					else
						$row->set_row_style("color:black");
					$PortStatus = $row->get_value("PortStatus");
					if($PortStatus=='Free'||$PortStatus=='Waiting'||$PortStatus=='GoToFree')
						$row->set_row_style("color:Chocolate");
				}

				$FixPartSQL="u.User_Id,u.Username,UserType,PortStatus,u.IdentInfo,u.Name,u.Family,u.NationalCode,{$DT}DateStr(u.BirthDate) as BirthDate,{$DT}DateStr(u.ExpirationDate) as ExpirationDate,u.Mobile,u.Phone,Organization,concat({$DT}DateStr(EndDate),' ',ActiveTime) As EndDate,Format(u.PayBalance,$PriceFloatDigit) as PayBalance,Note,r.ResellerName,{$DT}DateTimeStr(StatusDT) As StatusDT,StatusName,sbr.ResellerName as StatusCreator,u.Comment,u.Address,VispName,CenterName,SupporterName,ServiceName";

				$FixPartColumns=DBSelectAsString("Select RenderColumnIds from Hgrid_layout where Reseller_Id='$LReseller_Id' and ItemName='CRMUser'");

				if($LReseller_Id!=1){
					$PermitItem_Id_Of_Visp_User_UsersWebsite=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.UsersWebsite'");
					$PermitItem_Id_Of_Visp_User_View=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.View'");
					$PermitItem_Id_Of_Visp_User_Edit=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Edit'");
					$PermitItem_Id_Of_Visp_User_Delete=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Delete'");
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$permissionPartSQL="if(rpw.ISPermit='Yes',1,0) as CanWWW,if(rpv.ISPermit='Yes',1,0) as CanView,if(rpe.ISPermit='Yes',1,0) as CanEdit,if(rpd.ISPermit='Yes',1,0) as CanDelete,";
				}
				else
					$permissionPartSQL="1 as CanWWW,1 as CanView,1 as CanEdit,1 as CanDelete,";

				$permissionPartColumns="CanWWW,CanView,CanEdit,CanDelete,";


				$req=Get_InputIgnore('GET','DB','req','ARRAY',array("SaveToFile"),0,0,0);

				if($req=="SaveToFile"){
					$Type=Get_Input('GET','DB','Type','ARRAY',array("CSV","XLSX"),0,0,0);


					$posStart=Get_Input('GET','DB','posStart','INT',0,4294967295,0,0);
					$count=Get_Input('GET','DB','count','INT',1,4294967295,0,0);
					DBUpdate("set @rowInd=$posStart");

					$sql="Select  @rowInd:=@rowInd+1 as Row,$FixPartSQL ".
						"From Huser u ".
						"Left Join Huser_note u_n on (u.User_note_Id=u_n.User_note_Id) ".
						"Left Join Hvisp v on (u.Visp_Id=v.Visp_Id) ".
						"Left Join Hcenter c on (u.Center_Id=c.Center_Id) ".
						"Left Join Hsupporter su on (u.Supporter_Id=su.Supporter_Id) ".
						"Left Join Hstatus us on (u.Status_Id=us.Status_Id) ".
						"Left Join Hreseller r on (u.Reseller_Id=r.Reseller_Id) ".
						"Left Join Hservice s on (u.Service_Id=s.Service_Id) ".
						"Left join Hreseller sbr on (u.StatusBy_Id=sbr.Reseller_Id) ";
					if($LReseller_Id!=1){
						$sql.="join Hreseller_permit rpw on (u.Visp_Id=rpw.Visp_Id)and(rpw.Reseller_Id=$LReseller_Id)and(rpw.PermitItem_Id='$PermitItem_Id_Of_Visp_User_UsersWebsite') ";
						$sql.="join Hreseller_permit rpv on (u.Visp_Id=rpv.Visp_Id)and(rpv.Reseller_Id=$LReseller_Id)and(rpv.PermitItem_Id='$PermitItem_Id_Of_Visp_User_View') ";
						$sql.="join Hreseller_permit rpe on (u.Visp_Id=rpe.Visp_Id)and(rpe.Reseller_Id=$LReseller_Id)and(rpe.PermitItem_Id='$PermitItem_Id_Of_Visp_User_Edit') ";
						$sql.="join Hreseller_permit rpd on (u.Visp_Id=rpd.Visp_Id)and(rpd.Reseller_Id=$LReseller_Id)and(rpd.PermitItem_Id='$PermitItem_Id_Of_Visp_User_Delete') ";
						$sql.="Left Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id_Of_Visp_User_List') Where (rgp.ISPermit='Yes') ";
					}
					else
						$sql.="Where 1 ";
					$sql.="$sqlfilter $SortStr";

					$sql.=" limit $posStart,$count";

					$res = $conn->sql->query($sql);

					$n=$conn->sql->get_affected_rows();
					if($n>20000){
						echo "<html><head><script type=\"text/javascript\">";
						echo "window.onload = function(){alert('$n records matched. SaveToFile is available for only 20000 record in this version. Please limit your filter to use save file!');window.close();}";
						echo "</script></head><body></body></html>";
						exit();
					}
					if($n==0){
						echo "<html><head><script type=\"text/javascript\">";
						echo "window.onload = function(){alert('$n records matched!');window.close();}";
						echo "</script></head><body></body></html>";
						exit();
					}
					DSDebug(0,"Before Save Used Memory=[".number_format(memory_get_usage())."]");
					if($Type=="CSV"){


						header('Content-Encoding: UTF-8');
						header('Content-type: text/csv; charset=UTF-8');
						header('Content-Disposition: attachment;filename="Users.csv";');
						echo "\xEF\xBB\xBF";


						$f = fopen('php://output', 'w');

						$data =  $conn->sql->get_next($res);
						foreach ($data as $key=>$Value)
							$Arr[$key]=$key;
						fputcsv($f, $Arr, ',');

						while($data){
							foreach ($data as $key=>$Value)
								$Arr[$key]=mysqli_real_escape_string($mysqli,$data[$key]);
							$data =  $conn->sql->get_next($res);
							fputcsv($f, $Arr, ',');
						}
						fclose($f);
					}
					else{

						header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
						header('Content-Disposition: attachment; filename="Users.xlsx"');

						ini_set('memory_limit','64M');
						set_time_limit(300);
						require_once("../../lib/Export_lib/Excel.php");
						$writer = new XLSXWriter();

						$data =  $conn->sql->get_next($res);
						$writer->writeSheetRow("Users",array_keys($data));

						while($data){
							$writer->writeSheetRow("Users",$data);
							$data =  $conn->sql->get_next($res);
						}
						echo $writer->writeToString();
					}
					DSDebug(0,"After Save Used Memory=[".number_format(memory_get_usage())."]");
				}
				else{

					$sql="Select $permissionPartSQL$FixPartSQL ".
						"From Huser u ".
						"Left Join Huser_note u_n on (u.User_note_Id=u_n.User_note_Id) ".
						"Left Join Hvisp v on (u.Visp_Id=v.Visp_Id) ".
						"Left Join Hcenter c on (u.Center_Id=c.Center_Id) ".
						"Left Join Hsupporter su on (u.Supporter_Id=su.Supporter_Id) ".
						"Left Join Hstatus us on (u.Status_Id=us.Status_Id) ".
						"Left Join Hreseller r on (u.Reseller_Id=r.Reseller_Id) ".
						"Left Join Hservice s on (u.Service_Id=s.Service_Id) ".
						"Left join Hreseller sbr on (u.StatusBy_Id=sbr.Reseller_Id) ";
					if($LReseller_Id!=1){
						$sql.="join Hreseller_permit rpw on (u.Visp_Id=rpw.Visp_Id)and(rpw.Reseller_Id=$LReseller_Id)and(rpw.PermitItem_Id='$PermitItem_Id_Of_Visp_User_UsersWebsite') ";
						$sql.="join Hreseller_permit rpv on (u.Visp_Id=rpv.Visp_Id)and(rpv.Reseller_Id=$LReseller_Id)and(rpv.PermitItem_Id='$PermitItem_Id_Of_Visp_User_View') ";
						$sql.="join Hreseller_permit rpe on (u.Visp_Id=rpe.Visp_Id)and(rpe.Reseller_Id=$LReseller_Id)and(rpe.PermitItem_Id='$PermitItem_Id_Of_Visp_User_Edit') ";
						$sql.="join Hreseller_permit rpd on (u.Visp_Id=rpd.Visp_Id)and(rpd.Reseller_Id=$LReseller_Id)and(rpd.PermitItem_Id='$PermitItem_Id_Of_Visp_User_Delete') ";
						$sql.="Left Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id_Of_Visp_User_List') Where (rgp.ISPermit='Yes') ";
					}
					else
						$sql.="Where 1 ";
					$sql.="$sqlfilter $SortStr";

					DSGridRender_Sql(100,$sql,"User_Id",$permissionPartColumns.$FixPartColumns,"","","color_rows");
				}
       break;
	   case "Delete":
				DSDebug(1,"DSService_ListRender Delete ******************************************");
				$NewRowInfo=array();
				$NewRowInfo['User_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				exitifnotpermituser($NewRowInfo['User_Id'],"Visp.User.Delete");

				$res=DSDeleteUser($NewRowInfo['User_Id'],true);
				if($res!="")
					ExitError("[$res]");

				echo "OK~";
			break;
		case "ChangeLayout":
				DSDebug(1,"DSService_ListRender ChangeLayout ******************************************");
				exitifnotpermit(0,"CRM.User.List");
				$Req=Get_Input('GET','DB','Req','ARRAY',array('SaveLayout','ResetLayout'),0,0,0);
				if($Req=='SaveLayout'){
					$GColIds=Get_Input("POST","DB","GColIds","STR",20,400,0,0);
					$GColHeaders=Get_Input("POST","DB","GColHeaders","STR",20,400,0,0);
					$GColInitWidths=Get_Input("POST","DB","GColInitWidths","STR",3,150,0,0);
					$ColIdsArray=array_slice(explode(",",$GColIds),4);//to remove CanWWW,CanView,CanEdit,CanDelete, part from the beginning
					$ColHeadersArray=array_slice(explode(",",$GColHeaders),4);
					$ColInitWidthsArray=array_slice(explode(",",$GColInitWidths),4);
				}
				else{
					$GColIds="u.User_Id,u.Username,UserType,PortStatus,u.IndetInfo,u.Name,u.Family,u.NationalCode,u.Mobile,u.Phone,Organization,u.PayBalance,EndDate,u.ExpirationDate,u.BirthDate,Note,r.ResellerName,StatusDT,StatusName,sbr.ResellerName,u.Comment,u.Address,VispName,CenterName,SupporterName,ServiceName";
					$GColHeaders="{#stat_count} ردیف,Username,UserType,PortStatus,IdentInfo,Name,Family,NationalCode,Mobile,Phone,Organization,PayBalance,EndDate,ExpireDate,BirthDate,Note,ResellerName,StatusDT,StatusName,StatusCreator,Comment,Address,VispName,CenterName,SupporterName,ServiceName";
					$GColInitWidths="80,100,60,90,100,100,150,100,100,100,150,80,80,80,80,300,100,120,300,90,160,300,110,110,110,300";
					$ColIdsArray=explode(",",$GColIds);
					$ColHeadersArray=explode(",",$GColHeaders);
					$ColInitWidthsArray=explode(",",$GColInitWidths);
				}

				DSDebug(1,"ColIdsArray=".DSPrintArray($ColIdsArray));
				DSDebug(1,"ColHeadersArray=".DSPrintArray($ColHeadersArray));
				DSDebug(1,"ColInitWidthsArray".DSPrintArray($ColInitWidthsArray));

				$FieldToColIdMapper=Array(
					"User_Id"			=>	"u.User_Id",
					"Username"			=>	"u.Username",
					"UserType"			=>	"UserType",
					"PortStatus"		=>	 "PortStatus",
					"IdentInfo"			=>	 "u.IdentInfo",
					"Name"				=>	"u.Name",
					"Family"			=>	"u.Family",
					"NationalCode"		=>	"u.NationalCode",
					"Mobile"			=>	"u.Mobile",
					"Phone"				=>	"u.Phone",
					"Organization"		=>	"Organization",
					"EndDate"			=>	"EndDate",
					"ExpirationDate"	=>	"u.ExpirationDate",
					"BirthDate"			=>	"u.BirthDate",
					"PayBalance"		=>	"u.PayBalance",
					"Note"				=>	"Note",
					"ResellerName"		=>	"r.ResellerName",
					"StatusDT"			=>	"StatusDT",
					"StatusName"		=>	"StatusName",
					"StatusCreator"		=>	"sbr.ResellerName",
					"Comment"			=>	"u.Comment",
					"Address"			=>	"u.Address",
					"VispName"			=>	"VispName",
					"CenterName"		=>	"CenterName",
					"SupporterName"		=>	"SupporterName",
					"ServiceName"		=>	"ServiceName"
				);

				$GridLayoutArray=Array(
					"u.User_Id"			=>	Array(	"User_Id",			"{#stat_count} ردیف",	"80"	),
					"u.Username"		=>	Array(	"Username",			"Username",				"100"	),
					"UserType"			=>	Array(	"UserType",			"UserType",				"60"	),
					"PortStatus"		=>	Array( 	"PortStatus",		"PortStatus",			"90"	),
					"IdentInfo"			=>	Array( 	"IdentInfo",		"IdentInfo",			"100"	),
					"u.Name"			=>	Array(	"Name",				"Name",					"100"	),
					"u.Family"			=>	Array(	"Family",			"Family",				"150"	),
					"u.NationalCode"	=>	Array(	"NationalCode",		"NationalCode",			"100"	),
					"u.Mobile"			=>	Array(	"Mobile",			"Mobile",				"100"	),
					"u.Phone"			=>	Array(	"Phone",			"Phone",				"100"	),
					"Organization"		=>	Array(	"Organization",		"Organization",			"150"	),
					"EndDate"			=>	Array(	"EndDate",			"EndDate",				"80"	),
					"u.ExpirationDate"	=>	Array(	"ExpirationDate",	"ExpireDate",			"80"	),
					"u.BirthDate"		=>	Array(	"BirthDate",		"BirthDate",			"80"	),
					"u.PayBalance"		=>	Array(	"PayBalance",		"PayBalance",			"80"	),
					"Note"				=>	Array(	"Note",				"Note",					"300"	),
					"r.ResellerName"	=>	Array(	"ResellerName",		"ResellerName",			"100"	),
					"StatusDT"			=>	Array(	"StatusDT",			"StatusDT",				"120"	),
					"StatusName"		=>	Array(	"StatusName",		"StatusName",			"300"	),
					"sbr.ResellerName"	=>	Array(	"StatusCreator",	"StatusCreator",		"90"	),
					"u.Comment"			=>	Array(	"Comment",			"Comment",				"160"	),
					"u.Address"			=>	Array(	"Address",			"Address",				"300"	),
					"VispName"			=>	Array(	"VispName",			"VispName",				"110"	),
					"CenterName"		=>	Array(	"CenterName",		"CenterName",			"110"	),
					"SupporterName"		=>	Array(	"SupporterName",	"SupporterName",		"110"	),
					"ServiceName"		=>	Array(	"ServiceName",		"ServiceName",			"300"	)
				);

				$PermissionArray=Array();
				$sql="Select PermitItemName,ISPermit from Hreseller_permit r join Hpermititem p on r.PermitItem_Id=p.PermitItem_Id where Reseller_Id='$LReseller_Id' and PermitItemName<>'CRM.User.List' and PermitItemName like 'CRM.User.%'";
				$n=CopyTableToArray($PermissionArray,$sql);

				for($i=0;$i<$n;++$i){
					$PermissionField=end(explode(".",$PermissionArray[$i]['PermitItemName']));//Username
					$ColumnIdField=$FieldToColIdMapper[$PermissionField];//u.Username
					$ISPermit=$PermissionArray[$i]['ISPermit'];

					if($ISPermit=='Yes'){
						if(!in_array($ColumnIdField,$ColIdsArray)){
							DSDebug(2,"ISPermit='$ISPermit'\t'$PermissionField'\t'$ColumnIdField'\tnot exist in the list. Adding $ColumnIdField to the end of list");
							array_push($ColIdsArray			,	$ColumnIdField);
							array_push($ColHeadersArray		,	$GridLayoutArray[$ColumnIdField][1]);
							array_push($ColInitWidthsArray	,	$GridLayoutArray[$ColumnIdField][2]);
						}
						else
							DSDebug(2,"ISPermit='$ISPermit'\t'$PermissionField'\t'$ColumnIdField'\talready exists in the list. Do nothing...");
					}
					else{
						if(($key = array_search($ColumnIdField, $ColIdsArray)) !== false){
							DSDebug(2,"ISPermit='$ISPermit'\t'$PermissionField'\t'$ColumnIdField'\tfound in the list as $key. Removing it from the list");
							unset($ColIdsArray[$key]);
							unset($ColHeadersArray[$key]);
							unset($ColInitWidthsArray[$key]);
						}
						else
							DSDebug(2,"ISPermit='$ISPermit'\t'$PermissionField'\t'$ColumnIdField'\talready NOT exists in the list. Do nothing...");
					}
				}
				$IsAllZero=true;
				foreach($ColInitWidthsArray as $key=>$value){
					if($value<=0){
						$ColInitWidthsArray[$key]=0;
					}
					else
						$IsAllZero=false;
				}
				if($IsAllZero)
					$ColInitWidthsArray[0]=100;

				DSDebug(1,"ColIdsArray=".DSPrintArray($ColIdsArray));
				DSDebug(1,"ColHeadersArray=".DSPrintArray($ColHeadersArray));
				DSDebug(1,"ColInitWidthsArray=".DSPrintArray($ColInitWidthsArray));

				$RenderColumnIds=Array();
				foreach($ColIdsArray as $key=>$value){
					DSDebug(1,"key  =  $key	=>	value  =  $value");
					if(!array_key_exists($value,$GridLayoutArray)){
						DSDebug(1,"Invalid ColIds[$value] supplied!!!");
						ExitError("Invalid ColIds[$value] supplied!!!");
					}
					if($ColHeadersArray[$key]!=$GridLayoutArray[$value][1]){
						DSDebug(1,"Supplied ColHeadersArray[$key]=".$ColHeadersArray[$key]." is mismatch with GridLayoutArray[$value][1]=".$GridLayoutArray[$value][1]."!!!");
						ExitError("Supplied ColHeaders [".$ColHeadersArray[$key]."] is mismatch with [".$GridLayoutArray[$value][1]."] at position $key!!!");
					}
					if(filter_var($ColInitWidthsArray[$key], FILTER_VALIDATE_INT)===false){
						DSDebug(1,"Supplied ColInitWidthsArray[$key]=".$ColInitWidthsArray[$key]." is no a valid integer value!!!");
						ExitError("Supplied ColInitWidths [".$ColInitWidthsArray[$key]."] is no a valid integer value at position $key!!!");
					}
					array_push($RenderColumnIds,$GridLayoutArray[$value][0]);
				}
				$ColIds=implode(",",$ColIdsArray);
				$ColHeaders=implode(",",$ColHeadersArray);
				$ColInitWidths=implode(",",$ColInitWidthsArray);
				$RenderColumnIds=implode(",",$RenderColumnIds);
				$sql="Update Hgrid_layout set ".
					"ColIds='$ColIds',".
					"ColHeaders='$ColHeaders',".
					"ColInitWidths='$ColInitWidths',".
					"RenderColumnIds='$RenderColumnIds' ".
					"where Reseller_Id='$LReseller_Id' and ItemName='CRMUser'";

				$n=DBUpdate($sql);
				DSDebug(1,"Affected rows=$n");
				echo "OK~";
			break;
		default :
			echo "~Unknown Request";

}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
