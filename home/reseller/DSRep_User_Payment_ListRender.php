<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_Payment_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}
exitifnotpermit(0,"Report.User.Payment.List");
$act=Get_Input('GET','DB','act','ARRAY',array(
    "list",
    "SelectReseller",
    "SelectCreator",
    "SelectVisp",
    "SelectCenter",
    "SelectSupporter",
	"SelectPaymentReseller"
    ),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_Payment_ListRender->Filter********************************************");				
				$FieldsItems=Array(
					Array("Hu.Username","Hup.User_Id"),
					Array("Hup.User_PaymentCDT","Hup.VoucherDate"),
					Array("Hup.Price","Hu.PayBalance","Hup.ChargerCommission","Hup.SupporterCommission","Hup.ResellerCommission"),
					Array("Hup.VoucherNo","Hup.BankBranchName","Hup.BankBranchNo","Hup.Comment"),
					Array("Hup.PaymentType","Hu.Visp_Id","Hu.Center_Id","Hup.Creator_Id","Hu.Reseller_Id","Hu.Supporter_Id","Hup.Charger_Id","Hup.Reseller_Id","Hup.Supporter_Id")
				);				

				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(Hup.User_PaymentCDT),4)","left(SHDATESTR(Hup.User_PaymentCDT),7)","left(SHDATESTR(Hup.User_PaymentCDT),10)","Hu.Visp_Id","Hu.Center_Id","Hup.Creator_Id","Hu.Reseller_Id","Hu.Supporter_Id","Hup.Charger_Id","Hup.Reseller_Id","Hup.Supporter_Id"),
					"GroupByValue" => Array("left(SHDATESTR(Hup.User_PaymentCDT),4)","left(SHDATESTR(Hup.User_PaymentCDT),7)","left(SHDATESTR(Hup.User_PaymentCDT),10)","Hv.VispName","Hc.CenterName","Hrcreator.ResellerName","Hr.ResellerName","Hsu.SupporterName","Hrcharger.ResellerName","HrReseller.ResellerName","HrSupporter.ResellerName"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","UserVisp","UserCenter","PaymentCreator","UserReseller","UserSupporter","PaymentCharger","PaymentReseller","PaymentSupporter")
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
				
					$data = $row->get_value("Price");
					
					if($data<0)
						$row->set_row_style("color:red");
				}
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				
				$WhereStr='1';
				$ChkStatus=Get_Input('GET','DB','Chk00','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field00','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp00','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value00','STR',0,32,0,0);
					
					if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
						$FieldValue="%".$FieldValue."%";
					
					$FieldName=$FieldsItems[0][$FieldIndex];
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk01','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt0','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field01','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp01','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value01','STR',0,32,0,0);
						
						if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
							$FieldValue="%".$FieldValue."%";
						
						$FieldName=$FieldsItems[0][$FieldIndex];
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk10','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field10','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp10','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value10','DateOrBlank',0,0,0,0);

					$FieldName=$FieldsItems[1][$FieldIndex];
					
					if($FieldIndex==0)
						$FieldName="Date($FieldName)";
					
					if($FieldValue=="")
						$WhereStr.=" AND ($FieldName $CompareOperator 0";
					else
						$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk11','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt1','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field11','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp11','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value11','DateOrBlank',0,0,0,0);

						$FieldName=$FieldsItems[1][$FieldIndex];
						
						if($FieldIndex==0)
							$FieldName="Date($FieldName)";						
						
						if($FieldValue=="")
							$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
						else
							$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}	
				
				$ChkStatus=Get_Input('GET','DB','Chk20','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field20','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp20','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value20','FLT',-4294967295,4294967295,0,0);
					$FieldName=$FieldsItems[2][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk21','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt2','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field21','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp21','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value21','FLT',-4294967295,4294967295,0,0);
						$FieldName=$FieldsItems[2][$FieldIndex];											
						
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk30','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field30','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp30','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value30','STR',0,64,0,0);
					$FieldName=$FieldsItems[3][$FieldIndex];
					
					if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
						$FieldValue="%".$FieldValue."%";
					
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk31','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt3','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field31','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp31','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value31','STR',0,64,0,0);
						$FieldName=$FieldsItems[3][$FieldIndex];						

						if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
							$FieldValue="%".$FieldValue."%";						
						
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}	
				
				$ChkStatus=Get_Input('GET','DB','Chk4','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field4','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value4','STR',0,250,0,0));
					$FieldName=$FieldsItems[4][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				$ChkStatus=Get_Input('GET','DB','Chk5','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field5','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp5','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value5','STR',0,250,0,0));
					$FieldName=$FieldsItems[4][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
			
				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile","GetFooterInfo"),0,0,0);
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserPaymentList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Payment.List'");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from Huser_payment Hup ".
						"join Huser Hu on Hu.User_Id=Hup.User_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserPaymentList and Hrp2.ISPermit='Yes' ":"").
						"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif($req=="GetFooterInfo"){
			
				
					$ExtraUsersInfo=Get_Input('GET','DB','ExtraUsersInfo','INT',0,1,0,0);
					$SelectStr="COUNT(1),Format(SUM(Hup.Price),0),Format(SUM(Hup.ChargerCommission),0)".
						",Format(SUM(Hup.SupporterCommission),0),Format(SUM(Hup.ResellerCommission),0)".
						($ExtraUsersInfo?",Format(SUM(Hu.PayBalance),0)":"");
					
					$sql="Select $SelectStr from Huser_payment Hup ".
						"join Huser Hu on Hu.User_Id=Hup.User_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserPaymentList and Hrp2.ISPermit='Yes' ":"").
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
						
						
						$FieldIndex=Get_Input('GET','DB','GroupBy0','INT',0,15,0,0);
						$GroupByStr="group by ".$GroupByArray["GroupByOn"][$FieldIndex];
						$SortStr="order by ".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
						$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
						$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						
						
						for($i=1;$i<3;++$i){
						$ChkStatus=Get_Input('GET','DB','ChkGroupBy'.$i,'INT',0,1,0,0);
						if($ChkStatus){
							$FieldIndex=Get_Input('GET','DB','GroupBy'.$i,'INT',0,15,0,0);
							$GroupByStr.=",".$GroupByArray["GroupByOn"][$FieldIndex];
							$SortStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
							$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
							$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						}
						else
							break;
						}
						
						$ColumnStr.=",CountPayment,SumPrice";
						$SelectStr.=",count(1) as CountPayment,Format(sum(Hup.Price),0) as SumPrice";

						$IndexColumn="Row_Number";
						//$SortStr="order by Row_Number asc";
					}
					else{
						$GroupByStr="";
						$IndexColumn="User_Payment_Id";
						$ExtraUsersInfo=Get_Input('GET','DB','ExtraUsersInfo','INT',0,1,0,0);
						
						$SelectStr="Hup.User_Payment_Id as UserPaymentId,Hrcreator.ResellerName as PaymentCreator".
							",Hu.Username as Username,SHDATETIMESTR(Hup.User_PaymentCDT) as User_PaymentCDT,Hup.PaymentType as  PaymentType,Format(Hup.Price,0) as Price".
							($ExtraUsersInfo?",Hv.VispName as UserVisp,Hr.ResellerName as UserReseller,Hc.CenterName as UserCenter,Hsu.SupporterName as UserSupporter,Hu.Name as Name,Hu.Family as Family,Hu.Organization,Format(Hu.PayBalance,0) as PayBalance":"").
							",Hup.VoucherNo,SHDATESTR(Hup.VoucherDate) as VoucherDate,Hup.BankBranchName,Hup.BankBranchNo".
							",Hrcharger.ResellerName as PaymentCharger,Format(Hup.ChargerCommission,0) as ChargerCommission".
							",HrSupporter.ResellerName as PaymentSupporter,Format(Hup.SupporterCommission,0) as SupporterCommission".
							",HrReseller.ResellerName as PaymentReseller,Format(Hup.ResellerCommission,0) as ResellerCommission".
							",Hup.Comment";				


						$ColumnStr="UserPaymentId,PaymentCreator,Username,User_PaymentCDT,PaymentType,Price".
							($ExtraUsersInfo?",UserVisp,UserReseller,UserCenter,UserSupporter,Name,Family,Organization,PayBalance":"").						",VoucherNo,VoucherDate,BankBranchName,BankBranchNo,PaymentCharger,ChargerCommission,PaymentSupporter".
							",SupporterCommission,PaymentReseller,ResellerCommission,Comment";
					}
					
					$sql="Select $SelectStr from Huser_payment Hup ".
						"join Huser Hu on Hup.User_Id=Hu.User_Id ".
						"left join Hreseller Hrcreator on Hup.Creator_Id=Hrcreator.Reseller_Id ".
						"left join Hreseller Hrcharger on Hup.Charger_Id=Hrcharger.Reseller_Id ".
						"left join Hreseller HrSupporter on Hup.Supporter_Id=HrSupporter.Reseller_Id ".
						"left join Hreseller HrReseller on Hup.Reseller_Id=HrReseller.Reseller_Id ".
						((strpos($SelectStr,"Hut.")!==false)?"left join Huser Hut on Hup.TransferUser_Id=Hut.User_Id ":"").
						((strpos($SelectStr,"Hv.")!==false)?"join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ":"").
						((strpos($SelectStr,"Hr.")!==false)?"join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ":"").
						((strpos($SelectStr,"Hsu.")!==false)?"join Hsupporter Hsu on Hu.Supporter_Id=Hsu.Supporter_Id ":"").
						((strpos($SelectStr,"Hc.")!==false)?"join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ":"").
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserPaymentList and Hrp2.ISPermit='Yes' ":"").
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="PaymentReport.csv";');
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
    case "SelectReseller":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT Reseller_Id,ResellerName From Hreseller where ISOperator='No' order by ResellerName Asc";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;      
    case "SelectPaymentReseller":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT '' as Reseller_Id,'' as ResellerName union ".
							"(SELECT Reseller_Id,ResellerName From Hreseller where ISOperator='No' order by ResellerName Asc)";
							$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;      
     case "SelectCreator":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT '' as Reseller_Id,'' as ResellerName union ".
								"(SELECT Reseller_Id,ResellerName From Hreseller order by ResellerName Asc)";
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
