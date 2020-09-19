<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_Reseller_Credit_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.Reseller.Summary.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
	"list",
    "SelectResellersAndOperators"
	),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSRep_Reseller_Credit_ListRender->Filter********************************************");
				
				$FieldsItems=Array(
					Array("Hrc.Reseller_Credit_Id","Hrc.Comment"),
					Array("Hrc.Credit","Hrc.Price"),
					Array("Hrc.CreditType","Hrc.Creator_Id","Hrc.From_Reseller_Id","Hrc.To_Reseller_Id")
				);				

				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(Hrc.Reseller_CreditCDT),4)","left(SHDATESTR(Hrc.Reseller_CreditCDT),7)","left(SHDATESTR(Hrc.Reseller_CreditCDT),10)","Hrc.Creator_Id","Hrc.From_Reseller_Id","Hrc.To_Reseller_Id","Hrc.CreditType"),
					"GroupByValue" => Array("left(SHDATESTR(Hrc.Reseller_CreditCDT),4)","left(SHDATESTR(Hrc.Reseller_CreditCDT),7)","left(SHDATESTR(Hrc.Reseller_CreditCDT),10)","rc.ResellerName","fr.ResellerName","tr.ResellerName","Hrc.CreditType"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","Creator","FReseller","TReseller","CreditType")
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


				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				if($LReseller_Id!=1){
					if($LISManager=='Yes')
						$MyResellerAccess=$LResellerAccessAllow;
					else
						$MyResellerAccess="(r.Reseller_Id=$LReseller_Id or $LResellerAccessAllow)";
					
					DBUpdate("set group_concat_max_len=20480");
					
					$MyResellerAccessList=DBSelectAsString("SELECT GROUP_CONCAT(Reseller_Id) FROM Hreseller r WHERE $MyResellerAccess");					
					
					$WhereStr="((Hrc.From_Reseller_Id in ($MyResellerAccessList)) or (Hrc.To_Reseller_Id in ($MyResellerAccessList)))";
				}
				else
					$WhereStr="1";
				$ChkStatus=Get_Input('GET','DB','Chk0','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value0','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (Hrc.Reseller_CreditCDT >= '$FieldValue')";					
				}
				$ChkStatus=Get_Input('GET','DB','Chk1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value1','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (Hrc.Reseller_CreditCDT <= '$FieldValue')";					
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
					$FieldValue=Get_Input('GET','DB','Value30','FLT',-4294967295,4294967295,0,0);
					$FieldName=$FieldsItems[1][$FieldIndex];
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk31','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt3','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field31','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp31','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value31','FLT',-4294967295,4294967295,0,0);
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
				
			
				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile","GetFooterInfo"),0,0,0);
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from Hreseller_credit Hrc ".
							"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif($req=="GetFooterInfo"){
					
					$SelectStr="COUNT(1),Format(SUM(Hrc.Credit),0),Format(SUM(Hrc.Price),0)";
					
					$sql="Select $SelectStr from Hreseller_credit Hrc ".
							"where $WhereStr";

					global $conn;
					$res = $conn->sql->query($sql);
					$data =  $conn->sql->get_next($res);
					DSDebug(0,"\n\n\n--------------------**************************************--------------------------------");
					DSDebug(0,"sql=$sql");
					DSDebug(0,"\n\n--------------------**************************************--------------------------------");
					if($data!=""){
						$Out=implode("`",$data);
						DSDebug(0,"Footer Fileds value : $Out");
						echo $Out;
					}
					else
						echo "~Error in calculating summary";
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
						
						$ColumnStr.=",CountCredit,SumCredit,SumPrice";
						$SelectStr.=",count(1) as CountCredit,Format(sum(Hrc.Credit),0) as SumCredit,Format(sum(Hrc.Price),0) as SumPrice";

						$IndexColumn="Row_Number";
						$SortStr="order by Row_Number asc";
					}
					else{
						$GroupByStr="";
						$IndexColumn="Reseller_Credit_Id";

						$SelectStr="Hrc.Reseller_Credit_Id,rc.ResellerName as Creator,SHDATETIMESTR(Hrc.Reseller_CreditCDT) as Reseller_CreditCDT,Hrc.CreditType,fr.ResellerName as FReseller,tr.ResellerName as TReseller,Format(Hrc.Credit,0) as Credit,Format(Hrc.Price,0) as Price,Hrc.Comment";				

						$ColumnStr="Reseller_Credit_Id,Creator,Reseller_CreditCDT,CreditType,FReseller,TReseller,Credit,Price,Comment";
					}
					
					$sql="Select $SelectStr from Hreseller_credit Hrc ".
						"left join Hreseller rc on Hrc.Creator_Id=rc.Reseller_Id ".
						"left join Hreseller fr on Hrc.From_Reseller_Id=fr.Reseller_Id ".
						"left join Hreseller tr on Hrc.To_Reseller_Id=tr.Reseller_Id ".
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="CreditReport.csv";');
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
    case "SelectResellersAndOperators":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							if($LISManager=='Yes')
								$MyResellerAccess=$LResellerAccessAllow;
							else
								$MyResellerAccess="(r.Reseller_Id=$LReseller_Id or $LResellerAccessAllow)";
							$sql="SELECT 0 as Reseller_Id,'' as ResellerName union ".
								"(SELECT Reseller_Id,ResellerName From Hreseller r where $MyResellerAccess order by ResellerName Asc)";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
