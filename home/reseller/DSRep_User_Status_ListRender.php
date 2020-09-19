<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_Status_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.Status.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
	"list",
    "SelectReseller",
    "SelectStatus",
    "SelectCreator",
    "SelectVisp",
    "SelectCenter",
    "SelectSupporter"
	),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_Status_ListRender->Filter********************************************");
				$FieldsItems=Array(
					Array("Hu.Username","Hus.User_Status_Id","Hus.Comment"),
					Array("Hs.UserStatus","Hs.InitialStatus","Hs.CanWebLogin","Hs.CanAddService","Hs.IsBusyPort"),
					Array("Hus.Status_Id","Hs.PortStatus","Hus.Reseller_Id","Hu.Visp_Id","Hu.Center_Id","Hu.Reseller_Id","Hu.Supporter_Id")
				);				

				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(Hus.StatusCDT),4)","left(SHDATESTR(Hus.StatusCDT),7)","left(SHDATESTR(Hus.StatusCDT),10)","Hus.Status_Id","Hus.Reseller_Id","Hu.Visp_Id","Hu.Center_Id","Hu.Reseller_Id","Hu.Supporter_Id"),
					"GroupByValue" => Array("left(SHDATESTR(Hus.StatusCDT),4)","left(SHDATESTR(Hus.StatusCDT),7)","left(SHDATESTR(Hus.StatusCDT),10)","Hs.StatusName","Hrstatus.ResellerName","Hv.VispName","Hc.CenterName","Hr.ResellerName","Hsu.SupporterName"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","StatusName","Creator","UserVisp","UserCenter","UserReseller","UserSupporter")
				);

				function GetCompareOperator($CompTMP){
					switch ($CompTMP){
						case 'E':$CompareOperator='=';
							break;
						case 'NE':$CompareOperator='<>';
							break;
						case 'L':$CompareOperator='<';
							break;
						case 'G':$CompareOperator='>';
							break;
						case 'LE':$CompareOperator='<=';
							break;
						case 'GE':$CompareOperator='>=';
							break;
						case 'Like':$CompareOperator='like';
							break;
						case 'notLike':$CompareOperator='not like';
							break;
						case 'notin':$CompareOperator='not in';
							break;
						case 'in':$CompareOperator='in';
							break;
						default	:$CompareOperator=$CompTMP;						
					}
					return $CompareOperator;
				}		
				
				function color_rows($row){
					$PortStatus = $row->get_value("PortStatus");
					if($PortStatus=='Free'||$PortStatus=='Waiting'||$PortStatus=='GoToFree')
						$row->set_row_style("color:Chocolate");					
					/*if($PortStatus=='Busy')
						$row->set_row_style("color:#000000");
					elseif($PortStatus=='GoToBusy')
						$row->set_row_style("color:#000022");					
					elseif($PortStatus=='Reserve')
						$row->set_row_style("color:#000044");					
					elseif($PortStatus=='Waiting')
						$row->set_row_style("color:#000088");
					elseif($PortStatus=='GoToFree')
						$row->set_row_style("color:#0022AA");
					elseif($PortStatus=='Free')
						$row->set_row_style("color:#0022CC");						
					elseif($PortStatus=='None')
						$row->set_row_style("color:#0022FF");*/					
				}


				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				
				$WhereStr='1';
				$ChkStatus=Get_Input('GET','DB','Chk0','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value0','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (StatusCDT >= '$FieldValue')";					
				}
				$ChkStatus=Get_Input('GET','DB','Chk1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value1','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (StatusCDT <= '$FieldValue')";					
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk20','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field20','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp20','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value20','STR',0,128,0,0);
					$FieldName=$FieldsItems[0][$FieldIndex];
					
					if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
						$FieldValue="%".$FieldValue."%";		
					
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk21','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt2','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field21','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp21','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value21','STR',0,128,0,0);

						$FieldName=$FieldsItems[0][$FieldIndex];					
						if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
							$FieldValue="%".$FieldValue."%";							
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}
				$ChkStatus=Get_Input('GET','DB','Chk30','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field30','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp30','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value30','STR',0,5,0,0);
					$FieldName=$FieldsItems[1][$FieldIndex];
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk31','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt3','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field31','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp31','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value31','STR',0,5,0,0);
						$FieldName=$FieldsItems[1][$FieldIndex];							
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk4','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field4','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value4','STR',0,250,0,0));
					$FieldName=$FieldsItems[2][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk5','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field5','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp5','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value5','STR',0,250,0,0));
					$FieldName=$FieldsItems[2][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				
				
			
				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile"),0,0,0);
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserStatusList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Status.List'");
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from Huser_status Hus ".
						"join Huser Hu on Hu.User_Id=Hus.User_Id ".
						"join Hstatus Hs on Hus.Status_Id=Hs.Status_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserStatusList and Hrp2.ISPermit='Yes' ":"").
						"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif(($req=="ShowInGrid")or($req=="SaveToFile")){		
					
					$ChkStatus=Get_Input('GET','DB','ChkGroupBy0','INT',0,1,0,0);
					if($ChkStatus){
						DBUpdate("set @MyCounter=0");
						$ColumnStr="Row_Number";
						$SelectStr="@MyCounter:=@MyCounter+1 as Row_Number";
						
						
						$FieldIndex=Get_Input('GET','DB','GroupBy0','STR',0,100,0,0);
						$GroupByStr="group by ".$GroupByArray["GroupByOn"][$FieldIndex];
						$SortStr="order by ".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
						$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
						$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						
						
						for($i=1;$i<3;++$i){
						$ChkStatus=Get_Input('GET','DB','ChkGroupBy'.$i,'INT',0,1,0,0);
						if($ChkStatus){
							$FieldIndex=Get_Input('GET','DB','GroupBy'.$i,'STR',0,100,0,0);
							$GroupByStr.=",".$GroupByArray["GroupByOn"][$FieldIndex];
							$SortStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
							$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
							$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						}
						else
							break;
						}
						
						$ColumnStr.=",CountStatus";
						$SelectStr.=",count(1) as CountStatus";

						$IndexColumn="Row_Number";
						//$SortStr="order by Row_Number asc";
					}
					else{
						$GroupByStr="";
						$IndexColumn="User_Status_Id";
						$ExtraUsersInfo=Get_Input('GET','DB','ExtraUsersInfo','INT',0,1,0,0);

						$SelectStr="Hus.User_Status_Id as StatusId,Hrstatus.ResellerName as Creator".
							",Hs.StatusName as StatusName,Hu.Username,SHDATETIMESTR(Hus.StatusCDT) as CDT".
							($ExtraUsersInfo?",Hv.VispName as Visp,Hr.ResellerName as Reseller,Hc.CenterName as Center,Hsu.SupporterName as Supporter,Hu.Name,Hu.Family,Hu.Organization,Format(Hu.PayBalance,0) as PayBalance":"").
							",Hus.Comment as Comment,Hs.UserStatus,Hs.PortStatus,Hs.InitialStatus,Hs.CanWebLogin,Hs.CanAddService,Hs.IsBusyPort";				

						$ColumnStr="StatusId,Creator,StatusName,Username,CDT".
							($ExtraUsersInfo?",Visp,Reseller,Center,Supporter,Name,Family,Organization,PayBalance":"").
							",Comment,UserStatus,PortStatus,InitialStatus,CanWebLogin,CanAddService,IsBusyPort";
					}
					
					$sql="Select $SelectStr from Huser_status Hus ".
						"join Huser Hu on Hus.User_Id=Hu.User_Id ".
						"left join Hreseller Hrstatus on Hus.Reseller_Id=Hrstatus.Reseller_Id ".
						"join Hstatus Hs on Hus.Status_Id=Hs.Status_Id ".
						((strpos($SelectStr,"Hv.")!==false)?"join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ":"").
						((strpos($SelectStr,"Hr.")!==false)?"join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ":"").
						((strpos($SelectStr,"Hc.")!==false)?"join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ":"").
						((strpos($SelectStr,"Hsu.")!==false)?"join Hsupporter Hsu on Hu.Supporter_Id=Hsu.Supporter_Id ":"").
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserStatusList and Hrp2.ISPermit='Yes' ":"").
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="StatusReport.csv";');
						$res = $conn->sql->query($sql);
						$data =  $conn->sql->get_next($res);
						$f = fopen('php://output', 'w');
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
					else if($req=="ShowInGrid"){
						DSDebug(0,"\n\n\n--------------------**************************************--------------------------------");
						DSDebug(0,"sql=$sql\nIndexColumn=$IndexColumn\nColumnStr=$ColumnStr");
						DSDebug(0,"\n\n--------------------**************************************--------------------------------");						
						if($GroupByStr!="")
							DSGridRender_Sql(-1,$sql,$IndexColumn,$ColumnStr,"","","");
						else
							DSGridRender_Sql(100,$sql,$IndexColumn,$ColumnStr,"","","color_rows");
					}
				}
				else
					echo "~Unknown Request";
					 

       break;
    case "SelectStatus":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT Status_Id,StatusName From Hstatus order by StatusName Asc";
							$options->render_sql($sql,"","Status_Id,StatusName","","");
       break;
    case "SelectReseller":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT Reseller_Id,ResellerName From Hreseller where ISOperator='No' order by ResellerName Asc";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;     
     case "SelectCreator":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT Reseller_Id,ResellerName From Hreseller order by ResellerName Asc";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;      
    case "SelectVisp":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT v.Visp_Id,v.VispName From Hvisp v join Hreseller_permit rp on v.Visp_Id=rp.Visp_Id ".
								"join Hpermititem pi on rp.PermitItem_Id=pi.PermitItem_Id and pi.PermitItemName='Visp.User.View' ".
								"where rp.Reseller_Id=$LReseller_Id and ISPermit='Yes' order by VispName Asc";
							$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectCenter":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT Center_Id,CenterName From Hcenter order by CenterName Asc";
                            $options->render_sql($sql,"","Center_Id,CenterName","","");
    break;
    case "SelectSupporter":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT Supporter_Id,SupporterName From Hsupporter order by SupporterName Asc";
                            $options->render_sql($sql,"","Supporter_Id,SupporterName","","");
    break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
