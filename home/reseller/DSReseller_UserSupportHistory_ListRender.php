<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSReseller_UserSupportHistory_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"CRM.UserSupportHistory.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
	"list",
    "SelectSupportItem"
	),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_UserSupportHistory_ListRender->Filter********************************************");
				
				
				$FieldsItems=Array(
					Array("Hu.Username","Hus.User_SupportHistory_Id","Hus.Comment"),
					Array("Hus.SupportItem_Id"/* ,"Hus.CustomerSatisfactionLevel" */)
				);				

				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(Hus.CDT),4)","left(SHDATESTR(Hus.CDT),7)","left(SHDATESTR(Hus.CDT),10)","Hus.SupportItem_Id"/* ,"Hus.CustomerSatisfactionLevel" */,"Hu.Visp_Id","Hu.Center_Id","Hu.Reseller_Id","Hu.Supporter_Id"),
					"GroupByValue" => Array("left(SHDATESTR(Hus.CDT),4)","left(SHDATESTR(Hus.CDT),7)","left(SHDATESTR(Hus.CDT),10)","Hs.SupportItemTitle"/* ,"Hus.CustomerSatisfactionLevel" */,"Hv.VispName","Hc.CenterName","Hr.ResellerName","Hsu.SupporterName"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","SupportItem"/* ,"CSLevel" */,"UserVisp","UserCenter","UserReseller","UserSupporter")
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
					$CustomerSatisfactionLevel = $row->get_value("CustomerSatisfactionLevel");
					if($CustomerSatisfactionLevel=='VeryBad')
						$row->set_row_style("color:red");
					elseif($CustomerSatisfactionLevel=='Bad')
						$row->set_row_style("color:firebrick");
					elseif($CustomerSatisfactionLevel=='Good')
						$row->set_row_style("color:darkgreen");
					elseif($CustomerSatisfactionLevel=='VeryGood')
						$row->set_row_style("color:green");	
				}


				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				
				$WhereStr="(Hus.Creator_Id='$LReseller_Id')";
				$ChkStatus=Get_Input('GET','DB','Chk0','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value0','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (CDT >= '$FieldValue')";					
				}
				$ChkStatus=Get_Input('GET','DB','Chk1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value1','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (CDT <= '$FieldValue')";					
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
				
				$ChkStatus=Get_Input('GET','DB','Chk3','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field3','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp3','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value3','STR',0,250,0,0));
					$FieldName=$FieldsItems[1][$FieldIndex];
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				
				
			
				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile"),0,0,0);
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from Huser_supporthistory Hus ".
						"join Huser Hu on Hu.User_Id=Hus.User_Id ".
						"join Hsupportitem Hs on Hus.SupportItem_Id=Hs.SupportItem_Id ".
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
						
						$ColumnStr.=",CountSupportHistory";
						$SelectStr.=",count(1) as CountSupportHistory";

						$IndexColumn="Row_Number";
						//$SortStr="order by Row_Number asc";
					}
					else{
						$GroupByStr="";
						$IndexColumn="User_SupportHistory_Id";

						$SelectStr="Hus.User_SupportHistory_Id,HrSupportHistory.ResellerName as Creator".
							",SHDATETIMESTR(Hus.CDT) as CDT,Hu.Username,Hs.SupportItemTitle,Hus.Comment as Comment";//,Hus.CustomerSatisfactionLevel";				

						$ColumnStr="User_SupportHistory_Id,Creator,CDT,Username,SupportItemTitle,Comment";//,CustomerSatisfactionLevel";
					}
					
					$sql="Select $SelectStr from Huser_supporthistory Hus ".
						"join Huser Hu on Hus.User_Id=Hu.User_Id ".
						"left join Hreseller HrSupportHistory on Hus.Creator_Id=HrSupportHistory.Reseller_Id ".
						"join Hsupportitem Hs on Hus.SupportItem_Id=Hs.SupportItem_Id ".
						((strpos($SelectStr,"Hv.")!==false)?"join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ":"").
						((strpos($SelectStr,"Hr.")!==false)?"join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ":"").
						((strpos($SelectStr,"Hc.")!==false)?"join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ":"").
						((strpos($SelectStr,"Hsu.")!==false)?"join Hsupporter Hsu on Hu.Supporter_Id=Hsu.Supporter_Id ":"").
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="SupportHistoryReport.csv";');
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
							DSGridRender_Sql(100,$sql,$IndexColumn,$ColumnStr,"","","");
					}
				}
				else
					echo "~Unknown Request";
					 

       break;
	case "SelectSupportItem":
				DSDebug(1,"DSUser_SupportHistory_ListRender -> SelectSupportItem");	
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT SupportItem_Id,SupportItemTitle FROM Hsupportitem order by SupportItemTitle ASC","","SupportItem_Id,SupportItemTitle","","");
	break;	
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
