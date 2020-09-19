<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSReseller_MyTransaction_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"CRM.MyTransaction.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
	"list",
    "SelectCreator",
	"SelectRelatedReseller"
	),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_MyTransaction_ListRender->Filter********************************************");
				$FieldsItems=Array(
					Array("Hrt.Reseller_Transaction_Id","Hu.UserName","Hrt.User_Id"),
					Array("Hrt.Credit"),
					Array("Hrt.TransactionType","Hrt.Creator_Id","Hrt.Relate_Reseller_Id")
				);				

				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(Hrt.Reseller_TransactionCDT),4)","left(SHDATESTR(Hrt.Reseller_TransactionCDT),7)","left(SHDATESTR(Hrt.Reseller_TransactionCDT),10)","Hrt.Creator_Id","Hrt.TransactionType"),
					"GroupByValue" => Array("left(SHDATESTR(Hrt.Reseller_TransactionCDT),4)","left(SHDATESTR(Hrt.Reseller_TransactionCDT),7)","left(SHDATESTR(Hrt.Reseller_TransactionCDT),10)","rc.ResellerName","Hrt.TransactionType"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","Creator","TransactionType")
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
					$data = $row->get_value("Credit");
					if($data<0)
						$Format1="color:red";
					else
						$Format1="color:black";
					$data = $row->get_value("TransactionType");
					if(($data=='BuyService') or ($data=='GetMoney') or ($data=='CancelService') or ($data=='RefundMoney')){
						$Format2="font-weight:Normal";
					}
					else if(($data=='CreditGet') or ($data=='CreditSend') or ($data=='S-Commission') or ($data=='R-Commission') or ($data=='C-Commission') or ($data=='OnlineCreditGet')){
						$Format2="font-weight:Bold";
					}
					//else if($data=='Initial'){
					//	$Format1="color:gray";
					//	$Format2="font-style:Italic";
					//}

					$row->set_row_style("$Format1;$Format2");
					$row->set_cell_style("CreditBalance","color:gray");
				}


				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				

				$WhereStr="((Hrt.Reseller_Id=$LReseller_Id) and (Hrt.TransactionType<>'Initial'))";
				$ChkStatus=Get_Input('GET','DB','Chk0','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value0','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (Hrt.Reseller_TransactionCDT >= '$FieldValue')";					
				}
				$ChkStatus=Get_Input('GET','DB','Chk1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value1','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (Hrt.Reseller_TransactionCDT <= '$FieldValue')";					
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk20','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field20','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp20','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value20','STR',0,32,0,0);
					$FieldName=$FieldsItems[0][$FieldIndex];
					
					if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
						$FieldValue="%".$FieldValue."%";		
					
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk21','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt2','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field21','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp21','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value21','STR',0,32,0,0);

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
			
				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile","GetFooterInfo"),0,0,0);
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from Hreseller_transaction Hrt ".
						"left join Huser Hu on Hu.User_Id=Hrt.User_Id ".
						"join Hreseller r on Hrt.Reseller_Id=r.Reseller_Id ".
						"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif($req=="GetFooterInfo"){
					
					$SelectStr="COUNT(1),Format(SUM(Hrt.Credit),0)";
					
					$sql="Select $SelectStr from Hreseller_transaction Hrt ".
						"left join Huser Hu on Hu.User_Id=Hrt.User_Id ".
						"join Hreseller r on Hrt.Reseller_Id=r.Reseller_Id ".
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
						
						$ChkStatus=Get_Input('GET','DB','ChkGroupBy1','INT',0,1,0,0);
						if($ChkStatus){
							$FieldIndex=Get_Input('GET','DB','GroupBy1','STR',0,100,0,0);
							$GroupByStr.=",".$GroupByArray["GroupByOn"][$FieldIndex];
							$SortStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
							$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
							$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						}
						
						$ColumnStr.=",CountTransaction,SumCredit";
						$SelectStr.=",count(1) as CountTransaction,Format(sum(Hrt.Credit),0) as SumCredit";

						$IndexColumn="Row_Number";
						//$SortStr="order by Row_Number asc";
					}
					else{
						$GroupByStr="";
						$IndexColumn="Reseller_Transaction_Id";

						$SelectStr="Hrt.Reseller_Transaction_Id,rc.ResellerName as Creator,SHDATETIMESTR(Hrt.Reseller_TransactionCDT) as TransactionCDT,Hrt.TransactionType,r.Resellername as Reseller,r_r.ResellerName as Relate_Reseller,".
						"Format(Hrt.Credit,0) as Credit,Format(Hrt.CreditBalance,0) as CreditBalance,Hu.UserName as UserName";				

						$ColumnStr="Reseller_Transaction_Id,Creator,TransactionCDT,TransactionType,Reseller,Relate_Reseller,Credit,CreditBalance,UserName";
					}
					
					$sql="Select $SelectStr from Hreseller_transaction Hrt ".
						"left join Hreseller rc on Hrt.Creator_Id=rc.Reseller_Id ".
						"join Hreseller r on Hrt.Reseller_Id=r.Reseller_Id ".
						"left join Hreseller r_r on Hrt.Relate_Reseller_Id=r_r.Reseller_Id ".
						"left join Huser Hu on Hu.User_Id=Hrt.User_Id ".
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="TransactionReport.csv";');
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
    case "SelectCreator":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT distinct r.Reseller_Id,r.ResellerName From Hreseller r join Hreseller_transaction rt ".
								"on rt.Creator_Id=r.Reseller_Id where rt.Reseller_Id=$LReseller_Id order by r.ResellerName Asc";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;
    case "SelectRelatedReseller":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT '' as Reseller_Id,'' as ResellerName union ".
								"(SELECT distinct r.Reseller_Id,r.ResellerName From Hreseller r join Hreseller_transaction rt ".
								"on rt.Relate_Reseller_Id=r.Reseller_Id where rt.Reseller_Id=$LReseller_Id order by r.ResellerName Asc)";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
