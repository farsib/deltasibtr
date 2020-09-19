<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSReseller_UserService_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"CRM.UserService.List");
$act=Get_Input('GET','DB','act','ARRAY',array(
    "list",
    "SelectServiceNameBase",
    "SelectServiceNameExtraCredit",
    "SelectServiceNameIP",
    "SelectServiceNameOther"
    ),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_UserService_ListRender->Filter********************************************");
				$FieldsItems=Array(
					Array("Hu.Username","TName.User_Id"),
					Array("TName.CDT","TName.CancelDT","TName.StartDate","TName.EndDate","TName.ApplyDT"),
					Array("TName.PayPrice","TName.ServicePrice","TName.ReturnPrice","TName.VAT","TName.OFF","TName.InstallmentNo","TName.InstallmentPeriod"),
					Array("TName.PayPlan","TName.ServiceStatus","TName.Service_Id")
				);				

				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(TName.CDT),4)","left(SHDATESTR(TName.CDT),7)","left(SHDATESTR(TName.CDT),10)","TName.PayPlan","TName.Service_Id","Hu.Visp_Id","Hu.Reseller_Id","Hu.Center_Id","Hu.Supporter_Id"),
					"GroupByValue" => Array("left(SHDATESTR(TName.CDT),4)","left(SHDATESTR(TName.CDT),7)","left(SHDATESTR(TName.CDT),10)","TName.PayPlan","Hse.ServiceName","Hv.VispName","Hr.ResellerName","Hc.CenterName","Hsu.SupporterName"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","PayPlan","ServiceName","Visp","Reseller","Center","Supporter")
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
					$data = $row->get_value("ServiceStatus");
					if(($data=='Active')||($data=='Applied'))
						$row->set_row_style("color:blue");
					else if($data=='Used')
						$row->set_row_style("color:green");
					else if($data=='Cancel')
						$row->set_row_style("color:red");
					
					$data = $row->get_value("ServiceIsDel");
					if($data=='DeletedService')
						$row->set_row_style("color:gray;font-weight:bold");
				}				
				


				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				$ServiceType=Get_Input('GET','DB','ServiceType','INT',0,4,0,0);
				if($ServiceType==0){$TableName='Huser_servicebase';$IndexColumn="User_ServiceBase_Id";}
				elseif($ServiceType==1){$TableName='Huser_serviceextracredit';$IndexColumn="User_ServiceExtraCredit_Id";}
				elseif($ServiceType==2){$TableName='Huser_serviceip';$IndexColumn="User_ServiceIP_Id";}
				elseif($ServiceType==3){$TableName='Huser_serviceother';$IndexColumn="User_ServiceOther_Id";}
				else echo "~InvalidData";
				
				$WhereStr="(TName.Creator_Id=$LReseller_Id)";
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
					
					if($FieldName!='StartDate' and $FieldName!='EndDate')
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
						
						if($FieldName!='StartDate' and $FieldName!='EndDate')
							$FieldName="Date($FieldName)";						
						
						if($FieldValue=="")
							$WhereStr.=" $OptionButton $FieldName $CompareOperator 0)";
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
						if($FieldName!='StartDate' and $FieldName!='EndDate')
							$FieldName="Date($FieldName)";						
						
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}	
				
				$ChkStatus=Get_Input('GET','DB','Chk3','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field3','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp3','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value3','STR',0,250,0,0));
					$FieldName=$FieldsItems[3][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				

				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile","GetFooterInfo"),0,0,0);
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from $TableName TName join Huser Hu on Hu.User_Id=TName.User_Id where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif($req=="GetFooterInfo"){
				
					$SelectStr="COUNT(1),Format(SUM(TName.ServicePrice),0),Format(SUM(TName.PayPrice),0)".
						",Format(AVG(TName.VAT),2),Format(AVG(TName.Off),2),Format(SUM(TName.ReturnPrice),0)".
						($ExtraUsersInfo?",Format(SUM(Hu.PayBalance),0)":"");
					
					$sql="Select $SelectStr from $TableName TName ".
						"join Huser Hu on Hu.User_Id=TName.User_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ":"").
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispTServiceList and Hrp2.ISPermit='Yes' ":"").
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
						$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
						$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						
						
						for($i=1;$i<3;++$i){
						$ChkStatus=Get_Input('GET','DB','ChkGroupBy'.$i,'INT',0,1,0,0);
						if($ChkStatus){
							$FieldIndex=Get_Input('GET','DB','GroupBy'.$i,'STR',0,100,0,0);
							$GroupByStr.=",".$GroupByArray["GroupByOn"][$FieldIndex];
							$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
							$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						}
						else
							break;
						}
						
						$ColumnStr.=",CountService,SumServicePrice,SumPayPrice,SumReturnPrice,AvgVAT,AvgOff";
						$SelectStr.=",count(1) as CountService,Format(sum(TName.ServicePrice),0) as SumServicePrice,Format(sum(TName.PayPrice),0) as SumPayPrice,Format(sum(TName.ReturnPrice),0) as SumReturnPrice,avg(TName.VAT) as AvgVAT,avg(TName.Off) as AvgOff";

						$IndexColumn="Row_Number";
						$SortStr="order by Row_Number asc";
					}
					else{
						$GroupByStr="";
						$SelectStr="TName.$IndexColumn as UserServiceId,Hrc.ResellerName as Creator".
							",Hse.ServiceName as ServiceName,Hu.Username as Username,SHDATETIMESTR(TName.CDT) as CreateDT,TName.ServiceStatus as  ServiceStatus".
							",TName.PayPlan as PayPlan,Format(TName.ServicePrice,0) as ServicePrice,Format(TName.PayPrice,0) as PayPrice,TName.VAT as VAT,TName.Off as Off,SHDATETIMESTR(TName.CancelDT) as CancelDT,TName.ReturnPrice as ReturnPrice,TName.InstallmentNo,TName.InstallmentPeriod,TName.InstallmentFirstCash".
							((($ServiceType==0) or (ServiceType==2))?",SHDATESTR(TName.StartDate) as StartDate,SHDATESTR(TName.EndDate) as EndDate":"").
							(($ServiceType==1)?",SHDATESTR(TName.ApplyDT) as ApplyDT,Hut.UserName as TransferUsername,TName.TransferTraffic":"").
							",if(Hse.IsDel='Yes','DeletedService','') as ServiceIsDel";					

						$ColumnStr="UserServiceId,Creator,ServiceName,Username,CreateDT,ServiceStatus".
							",PayPlan,ServicePrice,PayPrice,VAT,Off,CancelDT,ReturnPrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash".
							((($ServiceType==0) or (ServiceType==2))?",StartDate,EndDate":"").
							(($ServiceType==1)?",ApplyDT,TransferUsername,TransferTraffic":"").
							",ServiceIsDel";
					}
					
					$sql="Select $SelectStr from $TableName TName ".
						"join Huser Hu on TName.User_Id=Hu.User_Id ".
						"left join Hreseller Hrc on TName.Creator_Id=Hrc.Reseller_Id ".
						"left join Hservice Hse on TName.Service_Id=Hse.Service_Id ".
						((strpos($SelectStr,"Hut.")!==false)?"left join Huser Hut on TName.TransferUser_Id=Hut.User_Id ":"").
						((strpos($SelectStr,"Hv.")!==false)?"join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ":"").
						((strpos($SelectStr,"Hr.")!==false)?"join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ":"").
						((strpos($SelectStr,"Hsu.")!==false)?"join Hsupporter Hsu on Hu.Supporter_Id=Hsu.Supporter_Id ":"").
						((strpos($SelectStr,"Hc.")!==false)?"join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ":"").
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="ServiceReport.csv";');
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
    case "SelectServiceNameBase":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT distinct s.Service_Id,s.ServiceName From Hservice s join Huser_servicebase sb on s.Service_Id=sb.Service_Id where s.IsDel='No' and sb.Creator_Id=$LReseller_Id order by ServiceName Asc";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;	   
    case "SelectServiceNameExtraCredit":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT distinct s.Service_Id,s.ServiceName From Hservice s join Huser_serviceextracredit se on s.Service_Id=se.Service_Id where s.IsDel='No' and se.Creator_Id=$LReseller_Id order by ServiceName Asc";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;	   
    case "SelectServiceNameIP":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT distinct s.Service_Id,s.ServiceName From Hservice s join Huser_serviceip si on s.Service_Id=si.Service_Id where s.IsDel='No' and si.Creator_Id=$LReseller_Id order by ServiceName Asc";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;	   
    case "SelectServiceNameOther":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT distinct s.Service_Id,s.ServiceName From Hservice s join Huser_serviceother so on s.Service_Id=so.Service_Id where s.IsDel='No' and so.Creator_Id=$LReseller_Id order by ServiceName Asc";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;	   
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
