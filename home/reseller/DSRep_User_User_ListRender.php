<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_User_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.User.UserAndUsage.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
    "list",
    "GetUserCount",
    "SelectReseller",
    "SelectVisp",
    "SelectCenter",
    "SelectSupporter",
    "SelectStatus",
    "SelectActiveServiceName",
    "SelectMikrotikRate",
    "SelectIPPool",
    "SelectLoginTime",
    "SelectOffFormula",
    "SelectActiveDirectory"
    ),0,0,0);

	
switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_User_ListRender->Filter********************************************");
				//$sqlfilter=GetSqlFilter_GET("dsfilter");

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
					global $CurrentDate;
					$EndDate = $row->get_value("EndDate");
					if($EndDate<=$CurrentDate)
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
				for($i=1;$i<=3;++$i){
					$ChkStatus=Get_Input('GET','DB','Chk1_'.$i.'_1','INT',0,1,0,0);
					if($ChkStatus){
						$FieldName="Hu.".Get_Input('GET','DB','Field1_'.$i.'_1','STR',0,100,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp1_'.$i.'_1','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value1_'.$i.'_1','STR',0,64,0,0);
						if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
							$FieldValue="%".$FieldValue."%";						
						$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
						$ChkStatus=Get_Input('GET','DB','Chk1_'.$i.'_2','INT',0,1,0,0);
						if($ChkStatus){
							$OptionButton=Get_Input('GET','DB','Opt1_'.$i,'STR',0,5,0,0);
							$FieldName="Hu.".Get_Input('GET','DB','Field1_'.$i.'_2','STR',0,100,0,0);					
							$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp1_'.$i.'_2','STR',0,10,0,0));
							$FieldValue=Get_Input('GET','DB','Value1_'.$i.'_2','STR',0,64,0,0);
							if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
								$FieldValue="%".$FieldValue."%";								
							$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue'";												
						}
						$WhereStr.=")";
					}
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk2_1_1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldName=Get_Input('GET','DB','Field2_1_1','STR',0,100,0,0);
					DSDebug(0,"*******************************************************************FieldName='$FieldName'");
					if(($FieldName=="GiftEndDT")||($FieldName=="LastRequestDT"))
						$FieldName="Date(Tuu.$FieldName)";
					elseif($FieldName=="UserCDT")
						$FieldName="Date(Hu.UserCDT)";
					else
						$FieldName="Hu.".$FieldName;
						
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp2_1_1','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value2_1_1','DateOrBlank',0,0,0,0);
					if($CompareOperator=='DIY')
						$WhereStr.=" AND (SHDAYOFYEAR($FieldName) = SHDAYOFYEAR('$FieldValue')";
					elseif($CompareOperator=='DIM')
						$WhereStr.=" AND (SHDAYOFMONTH($FieldName) = SHDAYOFMONTH('$FieldValue')";
					elseif($FieldValue=="")
						$WhereStr.=" AND ($FieldName $CompareOperator 0";
					else
						$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					$ChkStatus=Get_Input('GET','DB','Chk2_1_2','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt2_1','STR',0,5,0,0);
						$FieldName=Get_Input('GET','DB','Field2_1_2','STR',0,100,0,0);					
						if(($FieldName=="GiftEndDT")||($FieldName=="LastRequestDT"))
							$FieldName="Date(Tuu.$FieldName)";
						elseif($FieldName=="UserCDT")
							$FieldName="Date(Hu.UserCDT)";
						else
							$FieldName="Hu.".$FieldName;
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp2_1_2','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value2_1_2','DateOrBlank',0,0,0,0);
						if($CompareOperator=='DIY')
							$WhereStr.=" $OptionButton SHDAYOFYEAR($FieldName) = SHDAYOFYEAR('$FieldValue')";
						elseif($CompareOperator=='DIM')
							$WhereStr.=" $OptionButton SHDAYOFMONTH($FieldName) = SHDAYOFMONTH('$FieldValue')";
						elseif($FieldValue=="")
							$WhereStr.=" $OptionButton $FieldName $CompareOperator 0";
						else
							$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue'";					
						
						
					}
					$WhereStr.=" )";
				}	

				$ChkStatus=Get_Input('GET','DB','Chk3_1_1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldName=Get_Input('GET','DB','Field3_1_1','STR',0,100,0,0);
					$FieldValue=Get_Input('GET','DB','Value3_1_1','STR',0,500,0,0);
					if($FieldName=='TrafficType')
						$FieldName="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STrA<>0)or(Tuu.YTrA<>0)or(Tuu.MTrA<>0)or(Tuu.WTrA<>0)or(Tuu.DTrA<>0),'limit','UnLimit'))";
					elseif($FieldName=='TimeType')
						$FieldName="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
					else 
						$FieldName="Hu.$FieldName";
					$WhereStr.=" AND ($FieldName='$FieldValue'";
					$ChkStatus=Get_Input('GET','DB','Chk3_1_2','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt3_1','STR',0,5,0,0);
						$FieldName=Get_Input('GET','DB','Field3_1_2','STR',0,100,0,0);
						$FieldValue=Get_Input('GET','DB','Value3_1_2','STR',0,500,0,0);
						if($FieldName=='TrafficType')
							$FieldName="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STrA<>0)or(Tuu.YTrA<>0)or(Tuu.MTrA<>0)or(Tuu.WTrA<>0)or(Tuu.DTrA<>0),'limit','UnLimit'))";
						elseif($FieldName=='TimeType')
							$FieldName="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
						else 
							$FieldName="Hu.$FieldName";
						
						$WhereStr.=" $OptionButton $FieldName='$FieldValue'";
					}
					$WhereStr.=" )";
				}
				

				$ChkStatus=Get_Input('GET','DB','Chk4_1_1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldName=Get_Input('GET','DB','Field4_1_1','STR',0,100,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_1_1','STR',0,10,0,0));
					$FieldValue=1048576*Get_Input('GET','DB','Value4_1_1','INT',0,4294967295,0,0);
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					$ChkStatus=Get_Input('GET','DB','Chk4_1_2','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt4_1','STR',0,5,0,0);
						$FieldName=Get_Input('GET','DB','Field4_1_2','STR',0,100,0,0);					
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_1_2','STR',0,10,0,0));
						$FieldValue=1048576*Get_Input('GET','DB','Value4_1_2','INT',0,4294967295,0,0);
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue'";												
					}
					$WhereStr.=" )";
				}

				$ChkStatus=Get_Input('GET','DB','Chk4_2_1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldName=Get_Input('GET','DB','Field4_2_1','STR',0,100,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_2_1','STR',0,10,0,0));
					$FieldValue=Get_Input('GET','DB','Value4_2_1','INT',0,4294967295,0,0);
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					$ChkStatus=Get_Input('GET','DB','Chk4_2_2','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt4_2','STR',0,5,0,0);
						$FieldName=Get_Input('GET','DB','Field4_2_2','STR',0,100,0,0);					
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_2_2','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value4_2_2','INT',0,4294967295,0,0);
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue'";												
					}
					$WhereStr.=" )";
				}


				
				for($i=1;$i<=2;++$i){
					$ChkStatus=Get_Input('GET','DB','Chk5_'.$i,'INT',0,1,0,0);
					if($ChkStatus){
						$FieldName=Get_Input('GET','DB','Field5_'.$i,'STR',0,100,0,0);
						if($FieldName=='PortStatus')
							$FieldName="Hst.$FieldName";
						else
							$FieldName="Hu.$FieldName";
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp5_'.$i,'STR',0,10,0,0));
						$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value5_'.$i,'STR',0,500,0,0));
						$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";
					}	
				}
				
				$req=Get_Input('GET','DB','req','ARRAY',array("GetUserCount","ShowInGrid","SaveToFile","GetFooterInfo"),0,0,0);
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				
				
				DSDebug(0,"\n\n\n--------------------**************************************--------------------------------");
				DSDebug(0,"req=$req\nWhereStr=\"$WhereStr\"");
				DSDebug(0,"\n\n--------------------**************************************--------------------------------");
				
				if($req=="GetUserCount"){
					$sql="select count(1) from Huser Hu ".
					"join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
					(strpos($WhereStr,"Tuu.")?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_id ":"").
					(($LReseller_Id!=1)?"join Hreseller_permit Hrp on Hrp.Reseller_Id=$LReseller_Id and Hrp.Visp_Id=Hu.Visp_Id and Hrp.PermitItem_Id=$VispUserList and Hrp.ISPermit='Yes' ":"").
					"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif($req=="GetFooterInfo"){
					$TheFields=Get_Input('GET','DB','FooterFields','STR',0,1000,0,0);
					DSDebug(0,"TheFields=$TheFields");
					$sql="select $TheFields from Huser Hu ".
						"join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
						((strpos($TheFields,"Tuu.") or strpos($WhereStr,"Tuu."))?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_Id ":"").
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp on Hrp.Reseller_Id=$LReseller_Id and Hrp.Visp_Id=Hu.Visp_Id and Hrp.PermitItem_Id=$VispUserList and Hrp.ISPermit='Yes' ":"").
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
					
					$MyFields=Get_Input('GET','DB','MyFields','STR',0,2500,0,0);
					$FieldsList=explode(",",$MyFields);
					
					$MyFieldsName=Get_Input('GET','DB','MyFieldsName','STR',0,2500,0,0);
					$FieldsNameList=explode(",",$MyFieldsName);
					
					if($FieldsList[0]=='Row_Number'){
						DBUpdate("set @cnt=0");
						$FieldsList[0]="(@cnt:=@cnt+1)";
						//$SortStr="Order by Row_Number asc";
					}
					$TheFields=$FieldsList[0]." as ".$FieldsNameList[0];
					for($i=1;$i<count($FieldsList);$i++){
						if($FieldsList[$i]=='TrafficType')
							$FieldsList[$i]="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STrA<>0)or(Tuu.YTrA<>0)or(Tuu.MTrA<>0)or(Tuu.WTrA<>0)or(Tuu.DTrA<>0),'limit','UnLimit'))";
						else if($FieldsList[$i]=='TimeType')
							$FieldsList[$i]="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
						else if($FieldsList[$i]=='ServiceTraRemain')
							$FieldsList[$i]="if(Tuu.STrA=0,'Unlimit',Tuu.STrA-Tuu.STrU)";
						else if($FieldsList[$i]=='YealyTraRemain')
							$FieldsList[$i]="if(Tuu.YTrA=0,'Unlimit',Tuu.YTrA-Tuu.YTrU)";
						else if($FieldsList[$i]=='MonthlyTraRemain')
							$FieldsList[$i]="if(Tuu.MTrA=0,'Unlimit',Tuu.MTrA-Tuu.MTrU)";
						else if($FieldsList[$i]=='WeeklyTraRemain')
							$FieldsList[$i]="if(Tuu.WTrA=0,'Unlimit',Tuu.WTrA-Tuu.WTrU)";
						else if($FieldsList[$i]=='DailyTraRemain')
							$FieldsList[$i]="if(Tuu.DTrA=0,'Unlimit',Tuu.DTrA-Tuu.DTrU)";
						else if($FieldsList[$i]=='ExtraTraRemain')
							$FieldsList[$i]="Tuu.ETrA-Tuu.ETrU";
						else if($FieldsList[$i]=='AdvancePortStatus')
							$FieldsList[$i]="concat(Hst.PortStatus,'-',if(Hu.EndDate<=Date(now()),'ServiceExpire','ServiceNotExpired'))";
						
						$TheFields.=",".$FieldsList[$i]." as ".$FieldsNameList[$i];
					}
					
					$ChkStatus=Get_Input('GET','DB','ChkGroupBy1','INT',0,1,0,0);
					if($ChkStatus){
						$GroupByTMP=Get_Input('GET','DB','GroupBy1','STR',0,100,0,0);
						$SortStr="order by ".$FieldsNameList[1]." DESC";
						
						if($GroupByTMP=='TrafficType')
							$GroupByStr="group by if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STrA<>0)or(Tuu.YTrA<>0)or(Tuu.MTrA<>0)or(Tuu.WTrA<>0)or(Tuu.DTrA<>0),'limit','UnLimit'))";
						elseif($GroupByTMP=='TimeType')
							$GroupByStr="group by if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
						elseif($GroupByTMP=='AdvancePortStatus')
							$GroupByStr="group by Hst.PortStatus,Hu.EndDate<=Date(now())";
						else
							$GroupByStr="group by ".$GroupByTMP;
						
						$ChkStatus=Get_Input('GET','DB','ChkGroupBy2','INT',0,1,0,0);
						if($ChkStatus){
							$GroupByTMP=Get_Input('GET','DB','GroupBy2','STR',0,100,0,0);
							$SortStr.=",".$FieldsNameList[2]." DESC";
							if($GroupByTMP=='TrafficType')
								$GroupByStr.=",if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STrA<>0)or(Tuu.YTrA<>0)or(Tuu.MTrA<>0)or(Tuu.WTrA<>0)or(Tuu.DTrA<>0),'limit','UnLimit'))";
							elseif($GroupByTMP=='TimeType')
								$GroupByStr.=",if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
							elseif($GroupByTMP=='AdvancePortStatus')
								$GroupByStr.=",Hst.PortStatus,Hu.EndDate<=Date(now())";
							else
								$GroupByStr.=",".$GroupByTMP;
							$ChkStatus=Get_Input('GET','DB','ChkGroupBy3','INT',0,1,0,0);
							if($ChkStatus){
								$GroupByTMP=Get_Input('GET','DB','GroupBy3','STR',0,100,0,0);
								$SortStr.=",".$FieldsNameList[3]." DESC";
								if($GroupByTMP=='TrafficType')
									$GroupByStr.=",if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STrA<>0)or(Tuu.YTrA<>0)or(Tuu.MTrA<>0)or(Tuu.WTrA<>0)or(Tuu.DTrA<>0),'limit','UnLimit'))";
								elseif($GroupByTMP=='TimeType')
									$GroupByStr.=",if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
								elseif($GroupByTMP=='AdvancePortStatus')
									$GroupByStr.=",Hst.PortStatus,Hu.EndDate<=Date(now())";
								else
									$GroupByStr.=",".$GroupByTMP;
							}
						}
					}
					else 
						$GroupByStr="";					
					
					
					$sql="select $TheFields from Huser Hu ".
						"join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
						(strpos($TheFields,"Hv.")?"join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ":"").
						(strpos($TheFields,"Hr.")?"join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ":"").
						(strpos($TheFields,"Hc.")?"join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ":"").
						(strpos($TheFields,"Hi.")?"left join Hippool Hi on Hu.IPPool_Id=Hi.IPPool_Id ":"").
						(strpos($TheFields,"Hl.")?"left join Hlogintime Hl on Hu.LoginTime_Id=Hl.LoginTime_Id ":"").
						(strpos($TheFields,"Hm.")?"left join Hmikrotikrate Hm on Hu.MikrotikRate_Id=Hm.MikrotikRate_Id ":"").
						(strpos($TheFields,"Hsu.")?"join Hsupporter Hsu on Hu.Supporter_Id=Hsu.Supporter_Id ":"").
						((strpos($TheFields,"Hse.") or strpos($GroupByStr,"Hse."))?"left join Hservice Hse on Hu.Service_id=Hse.Service_id ":"").
						((strpos($TheFields,"Tuu.") or strpos($GroupByStr,"Tuu.") or strpos($WhereStr,"Tuu.") or strpos($SortStr,"Tuu."))?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_Id ":"").
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp on Hrp.Reseller_Id=$LReseller_Id and Hrp.Visp_Id=Hu.Visp_Id and Hrp.PermitItem_Id=$VispUserList and Hrp.ISPermit='Yes' ":"").
						"where $WhereStr $GroupByStr $SortStr";
						
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; filename="UserReport.csv";');
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
					}
					else if($req=="ShowInGrid"){
						DSDebug(0,"\n\n\n--------------------**************************************--------------------------------");
						DSDebug(0,"sql=$sql");
						DSDebug(0,"\n\n--------------------**************************************--------------------------------");
						DSGridRender_Sql((($GroupByStr!="")?-1:100),$sql,$FieldsNameList[0],$MyFieldsName,"","",((($GroupByStr!="")or(strpos($MyFieldsName,"EndDate")===false))?"":"color_rows"));
					}
					
				}
				else
					echo "~Unknown Request";
					 

       break;
    case "SelectReseller":
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
    case "SelectStatus":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT Status_Id,StatusName From Hstatus order by StatusName Asc";
                            $options->render_sql($sql,"","Status_Id,StatusName","","");
    break;
    case "SelectActiveServiceName":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT Service_Id,ServiceName From Hservice where ServiceType='Base' and IsDel='No' order by ServiceName Asc";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;
    case "SelectMikrotikRate":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT MikrotikRate_Id,MikrotikRateName From Hmikrotikrate order by MikrotikRateName Asc";
                            $options->render_sql($sql,"","MikrotikRate_Id,MikrotikRateName","","");
    break;
    case "SelectIPPool":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT IPPool_Id,IPPoolName From Hippool order by IPPoolName Asc";
                            $options->render_sql($sql,"","IPPool_Id,IPPoolName","","");
    break;
    case "SelectLoginTime":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT LoginTime_Id,LoginTimeName From Hlogintime order by LoginTimeName Asc";
                            $options->render_sql($sql,"","LoginTime_Id,LoginTimeName","","");
    break;
    case "SelectOffFormula":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT OffFormula_Id,OffFormulaName From Hoffformula order by OffFormulaName Asc";
                            $options->render_sql($sql,"","OffFormula_Id,OffFormulaName","","");
    break;
    case "SelectActiveDirectory":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT ActiveDirectory_Id,ActiveDirectoryName From Hactivedirectory order by ActiveDirectoryName Asc";
                            $options->render_sql($sql,"","ActiveDirectory_Id,ActiveDirectoryName","","");
    break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
