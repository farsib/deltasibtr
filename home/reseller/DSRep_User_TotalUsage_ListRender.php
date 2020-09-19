<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_User_TotalUsage_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}
exitifnotpermit(0,"Report.User.TotalUsage.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
    "list",
    "SelectReseller",
    "SelectVisp",
    "SelectCenter",
    "SelectSupporter",
	"SelectActiveServiceBase"
    ),0,0,0);

	
	
		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSRep_User_TotalUsage_ListRender->Filter********************************************");				
				$FieldsItems=Array(
					Array("Hu.Username","Hdu.User_Id"),
					Array("Hdu.CreateDT","Hdu.UsageDate"),
					Array("Hdu.RealSendTr","Hdu.RealReceiveTr","Hdu.FinishUsedTr","Hdu.BugUsedTr","Hdu.Total","Hdu.HTrU0","Hdu.HTrU1","Hdu.HTrU2","Hdu.HTrU3","Hdu.HTrU4","Hdu.HTrU5","Hdu.HTrU6","Hdu.HTrU7","Hdu.HTrU8","Hdu.HTrU9","Hdu.HTrU10","Hdu.HTrU11","Hdu.HTrU12","Hdu.HTrU13","Hdu.HTrU14","Hdu.HTrU15","Hdu.HTrU16","Hdu.HTrU17","Hdu.HTrU18","Hdu.HTrU19","Hdu.HTrU20","Hdu.HTrU21","Hdu.HTrU22","Hdu.HTrU23"),
					Array("Hdu.RealUsedTime","Hdu.FinishUsedTi","Hdu.BugUsedTi","Hdu.Total","Hdu.HTiU0","Hdu.HTiU1","Hdu.HTiU2","Hdu.HTiU3","Hdu.HTiU4","Hdu.HTiU5","Hdu.HTiU6","Hdu.HTiU7","Hdu.HTiU8","Hdu.HTiU9","Hdu.HTiU10","Hdu.HTiU11","Hdu.HTiU12","Hdu.HTiU13","Hdu.HTiU14","Hdu.HTiU15","Hdu.HTiU16","Hdu.HTiU17","Hdu.HTiU18","Hdu.HTiU19","Hdu.HTiU20","Hdu.HTiU21","Hdu.HTiU22","Hdu.HTiU23"),
					Array("Hu.Visp_Id","Hu.Center_Id","Hu.Reseller_Id","Hu.Supporter_Id","Hu.Service_Id")
				);
				
				$GroupByArray=Array(
					"GroupByOn" => Array("left(SHDATESTR(Hdu.UsageDate),4)","left(SHDATESTR(Hdu.UsageDate),7)","left(SHDATESTR(Hdu.UsageDate),10)","Hdu.User_Id","Hu.Visp_Id","Hu.Center_Id","Hu.Reseller_Id","Hu.Supporter_Id","Hu.Service_Id"),
					"GroupByValue" => Array("left(SHDATESTR(Hdu.UsageDate),4)","left(SHDATESTR(Hdu.UsageDate),7)","left(SHDATESTR(Hdu.UsageDate),10)","Hu.UserName","Hv.VispName","Hc.CenterName","Hr.ResellerName","Hs.SupporterName","Hservice.ServiceName"),
					"GroupByTitle" => Array("YearOfCreate","MonthOfCreate","DayOfCreate","Username","Visp","Center","Reseller","Supporter","ActiveServiceBase")
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
				
				$ReportType=Get_Input('GET','DB','ReportType','ARRAY',array("Traffic","Time"),0,0,0);
				if($ReportType=="Traffic"){
					$Table="deltasib_conn.Hdailyusage";
				}
				else{
					$Table="deltasib_conn.Hdailyusage2";
				}
				
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
					$FieldValue=(($ReportType=="Traffic")?1048576:1)*Get_Input('GET','DB','Value20','FLT',0,4294967295,0,0);
					$FieldName=$FieldsItems[($ReportType=="Traffic")?2:3][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk21','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt2','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field21','INT',0,30,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp21','STR',0,10,0,0));
						$FieldValue=(($ReportType=="Traffic")?1048576:1)*Get_Input('GET','DB','Value21','FLT',0,4294967295,0,0);
						$FieldName=$FieldsItems[($ReportType=="Traffic")?2:3][$FieldIndex];											
						
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}
				
				
				$ChkStatus=Get_Input('GET','DB','Chk3','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field3','INT',0,30,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp3','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value3','STR',0,250,0,0));
					$FieldName=$FieldsItems[4][$FieldIndex];
					
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
			
				$req=Get_Input('GET','DB','req','ARRAY',array("ShowInGrid","SaveToFile","GetFooterInfo"),0,0,0);
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserDailyUsageList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.DailyUsage.List'");
				
/* 				$ChkStatus=Get_Input('GET','DB','ChkGroupBy0','INT',0,1,0,0);
				if($ChkStatus){
					$GroupByStr="group by ".$GroupByArray["GroupByOn"][Get_Input('GET','DB','GroupBy0','INT',0,15,0,0)];					
					$ChkStatus=Get_Input('GET','DB','ChkGroupBy1','INT',0,1,0,0);
					if($ChkStatus)
						$GroupByStr.=",".$GroupByArray["GroupByOn"][Get_Input('GET','DB','GroupBy1','INT',0,15,0,0)];
				}
				else
					$GroupByStr=""; *///It seems it is ramained from before. No need further;
				
				
				if($req=="GetFooterInfo"){
					//$ExtraUsersInfo=Get_Input('GET','DB','ExtraUsersInfo','INT',0,1,0,0);
					if($ReportType=="Traffic"){
						$SelectStr="Concat(COUNT(1),' rows'),ByteToR(sum(Hdu.RealSendTr))".
							",ByteToR(sum(Hdu.RealReceiveTr))".
							",ByteToR(sum(Hdu.FinishUsedTr))".
							",ByteToR(sum(Hdu.BugUsedTr))".
							",ByteToR(sum(Hdu.Total))".
							",ByteToR(sum(Hdu.HTrU0))".
							",ByteToR(sum(Hdu.HTrU1))".
							",ByteToR(sum(Hdu.HTrU2))".
							",ByteToR(sum(Hdu.HTrU3))".
							",ByteToR(sum(Hdu.HTrU4))".
							",ByteToR(sum(Hdu.HTrU5))".
							",ByteToR(sum(Hdu.HTrU6))".
							",ByteToR(sum(Hdu.HTrU7))".
							",ByteToR(sum(Hdu.HTrU8))".
							",ByteToR(sum(Hdu.HTrU9))".
							",ByteToR(sum(Hdu.HTrU10))".
							",ByteToR(sum(Hdu.HTrU11))".
							",ByteToR(sum(Hdu.HTrU12))".
							",ByteToR(sum(Hdu.HTrU13))".
							",ByteToR(sum(Hdu.HTrU14))".
							",ByteToR(sum(Hdu.HTrU15))".
							",ByteToR(sum(Hdu.HTrU16))".
							",ByteToR(sum(Hdu.HTrU17))".
							",ByteToR(sum(Hdu.HTrU18))".
							",ByteToR(sum(Hdu.HTrU19))".
							",ByteToR(sum(Hdu.HTrU20))".
							",ByteToR(sum(Hdu.HTrU21))".
							",ByteToR(sum(Hdu.HTrU22))".
							",ByteToR(sum(Hdu.HTrU23))";
					}
					else{
						$SelectStr="Concat(COUNT(1),' rows')".
							",SecondToR(sum(Hdu.RealUsedTime))".
							",ByteToR(sum(Hdu.FinishUsedTi))".
							",SecondToR(sum(Hdu.BugUsedTi))".
							",SecondToR(sum(Hdu.Total))".
							",SecondToR(sum(Hdu.HTiU0))".
							",SecondToR(sum(Hdu.HTiU1))".
							",SecondToR(sum(Hdu.HTiU2))".
							",SecondToR(sum(Hdu.HTiU3))".
							",SecondToR(sum(Hdu.HTiU4))".
							",SecondToR(sum(Hdu.HTiU5))".
							",SecondToR(sum(Hdu.HTiU6))".
							",SecondToR(sum(Hdu.HTiU7))".
							",SecondToR(sum(Hdu.HTiU8))".
							",SecondToR(sum(Hdu.HTiU9))".
							",SecondToR(sum(Hdu.HTiU10))".
							",SecondToR(sum(Hdu.HTiU11))".
							",SecondToR(sum(Hdu.HTiU12))".
							",SecondToR(sum(Hdu.HTiU13))".
							",SecondToR(sum(Hdu.HTiU14))".
							",SecondToR(sum(Hdu.HTiU15))".
							",SecondToR(sum(Hdu.HTiU16))".
							",SecondToR(sum(Hdu.HTiU17))".
							",SecondToR(sum(Hdu.HTiU18))".
							",SecondToR(sum(Hdu.HTiU19))".
							",SecondToR(sum(Hdu.HTiU20))".
							",SecondToR(sum(Hdu.HTiU21))".
							",SecondToR(sum(Hdu.HTiU22))".
							",SecondToR(sum(Hdu.HTiU23))";
					}
					
					$sql="Select $SelectStr from $Table Hdu ".
						"join Huser Hu on Hu.User_Id=Hdu.User_Id ".
						"left join Hservice on Hu.Service_Id=Hservice.Service_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserDailyUsageList and Hrp2.ISPermit='Yes' ":"").
						"where $WhereStr";
					$ResultArr=Array();
					$n=CopyTableToArray($ResultArr,$sql);
					if($n!=1)
						echo "~Error in calculating summary";
					else{
						$Out=implode("`",$ResultArr[0]);
						DSDebug(0,"Footer Fileds value : $Out");
						echo $Out;
					}

				}
				elseif(($req=="ShowInGrid")or($req=="SaveToFile")){
					
					$FormatData=Get_Input('GET','DB','FormatData','INT',0,1,0,0);
					
					$ChkStatus=Get_Input('GET','DB','ChkGroupBy0','INT',0,1,0,0);
					if($ChkStatus){
						DBUpdate("set @MyCounter=0");
						$ColumnStr="Row_Number";
						$SelectStr="@MyCounter:=@MyCounter+1 as Row_Number";
						//$SortStr="";
						$FieldIndex=Get_Input('GET','DB','GroupBy0','INT',0,15,0,0);
						$GroupByStr="group by ".$GroupByArray["GroupByOn"][$FieldIndex];
						$SortStr="order by ".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
						
						$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
						$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						
						
						$ChkStatus=Get_Input('GET','DB','ChkGroupBy1','INT',0,1,0,0);
						if($ChkStatus){
							$FieldIndex=Get_Input('GET','DB','GroupBy1','INT',0,15,0,0);
							$GroupByStr.=",".$GroupByArray["GroupByOn"][$FieldIndex];
							$SortStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex]." DESC";
							$ColumnStr.=",".$GroupByArray["GroupByTitle"][$FieldIndex];
							$SelectStr.=",".$GroupByArray["GroupByValue"][$FieldIndex]." as ".$GroupByArray["GroupByTitle"][$FieldIndex];
						}

						if($ReportType=="Traffic"){
							$ColumnStr.=",sumRealSendTr,sumRealReceiveTr,sumFinishUsedTr,sumBugUsedTr,sumTotal".
									",sumHTrU0,sumHTrU1,sumHTrU2,sumHTrU3,sumHTrU4,sumHTrU5,sumHTrU6,sumHTrU7,sumHTrU8,sumHTrU9,sumHTrU10,sumHTrU11".
									",sumHTrU12,sumHTrU13,sumHTrU14,sumHTrU15,sumHTrU16,sumHTrU17,sumHTrU18,sumHTrU19,sumHTrU20,sumHTrU21,sumHTrU22,sumHTrU23";
							if($FormatData)
								$SelectStr.=",ByteToR(sum(Hdu.RealSendTr)) as sumRealSendTr".
									",ByteToR(sum(Hdu.RealReceiveTr)) as sumRealReceiveTr".
									",ByteToR(sum(Hdu.FinishUsedTr)) as sumFinishUsedTr".
									",ByteToR(sum(Hdu.BugUsedTr)) as sumBugUsedTr".
									",ByteToR(sum(Hdu.Total)) as sumTotal".
									",ByteToR(sum(Hdu.HTrU0)) as sumHTrU0".
									",ByteToR(sum(Hdu.HTrU1)) as sumHTrU1".
									",ByteToR(sum(Hdu.HTrU2)) as sumHTrU2".
									",ByteToR(sum(Hdu.HTrU3)) as sumHTrU3".
									",ByteToR(sum(Hdu.HTrU4)) as sumHTrU4".
									",ByteToR(sum(Hdu.HTrU5)) as sumHTrU5".
									",ByteToR(sum(Hdu.HTrU6)) as sumHTrU6".
									",ByteToR(sum(Hdu.HTrU7)) as sumHTrU7".
									",ByteToR(sum(Hdu.HTrU8)) as sumHTrU8".
									",ByteToR(sum(Hdu.HTrU9)) as sumHTrU9".
									",ByteToR(sum(Hdu.HTrU10)) as sumHTrU10".
									",ByteToR(sum(Hdu.HTrU11)) as sumHTrU11".
									",ByteToR(sum(Hdu.HTrU12)) as sumHTrU12".
									",ByteToR(sum(Hdu.HTrU13)) as sumHTrU13".
									",ByteToR(sum(Hdu.HTrU14)) as sumHTrU14".
									",ByteToR(sum(Hdu.HTrU15)) as sumHTrU15".
									",ByteToR(sum(Hdu.HTrU16)) as sumHTrU16".
									",ByteToR(sum(Hdu.HTrU17)) as sumHTrU17".
									",ByteToR(sum(Hdu.HTrU18)) as sumHTrU18".
									",ByteToR(sum(Hdu.HTrU19)) as sumHTrU19".
									",ByteToR(sum(Hdu.HTrU20)) as sumHTrU20".
									",ByteToR(sum(Hdu.HTrU21)) as sumHTrU21".
									",ByteToR(sum(Hdu.HTrU22)) as sumHTrU22".
									",ByteToR(sum(Hdu.HTrU23)) as sumHTrU23";
							else
								$SelectStr.=",sum(Hdu.RealSendTr) as sumRealSendTr".
									",sum(Hdu.RealReceiveTr) as sumRealReceiveTr".
									",sum(Hdu.FinishUsedTr) as sumFinishUsedTr".
									",sum(Hdu.BugUsedTr) as sumBugUsedTr".
									",sum(Hdu.Total) as sumTotal".
									",sum(Hdu.HTrU0) as sumHTrU0".
									",sum(Hdu.HTrU1) as sumHTrU1".
									",sum(Hdu.HTrU2) as sumHTrU2".
									",sum(Hdu.HTrU3) as sumHTrU3".
									",sum(Hdu.HTrU4) as sumHTrU4".
									",sum(Hdu.HTrU5) as sumHTrU5".
									",sum(Hdu.HTrU6) as sumHTrU6".
									",sum(Hdu.HTrU7) as sumHTrU7".
									",sum(Hdu.HTrU8) as sumHTrU8".
									",sum(Hdu.HTrU9) as sumHTrU9".
									",sum(Hdu.HTrU10) as sumHTrU10".
									",sum(Hdu.HTrU11) as sumHTrU11".
									",sum(Hdu.HTrU12) as sumHTrU12".
									",sum(Hdu.HTrU13) as sumHTrU13".
									",sum(Hdu.HTrU14) as sumHTrU14".
									",sum(Hdu.HTrU15) as sumHTrU15".
									",sum(Hdu.HTrU16) as sumHTrU16".
									",sum(Hdu.HTrU17) as sumHTrU17".
									",sum(Hdu.HTrU18) as sumHTrU18".
									",sum(Hdu.HTrU19) as sumHTrU19".
									",sum(Hdu.HTrU20) as sumHTrU20".
									",sum(Hdu.HTrU21) as sumHTrU21".
									",sum(Hdu.HTrU22) as sumHTrU22".
									",sum(Hdu.HTrU23) as sumHTrU23";
						}
						else{//ReportType=Time
							$ColumnStr.=",sumRealUsedTime,sumFinishUsedTi,sumBugUsedTi,sumTotal".
									",sumHTiU0,sumHTiU1,sumHTiU2,sumHTiU3,sumHTiU4,sumHTiU5,sumHTiU6,sumHTiU7,sumHTiU8,sumHTiU9,sumHTiU10,sumHTiU11".
									",sumHTiU12,sumHTiU13,sumHTiU14,sumHTiU15,sumHTiU16,sumHTiU17,sumHTiU18,sumHTiU19,sumHTiU20,sumHTiU21,sumHTiU22,sumHTiU23";
							if($FormatData)
								$SelectStr.=",SecondToR(sum(Hdu.RealUsedTime)) as sumRealUsedTime".
									",SecondToR(sum(Hdu.FinishUsedTi)) as sumFinishUsedTi".
									",SecondToR(sum(Hdu.BugUsedTi)) as sumBugUsedTi".
									",SecondToR(sum(Hdu.Total)) as sumTotal".
									",SecondToR(sum(Hdu.HTiU0)) as sumHTiU0".
									",SecondToR(sum(Hdu.HTiU1)) as sumHTiU1".
									",SecondToR(sum(Hdu.HTiU2)) as sumHTiU2".
									",SecondToR(sum(Hdu.HTiU3)) as sumHTiU3".
									",SecondToR(sum(Hdu.HTiU4)) as sumHTiU4".
									",SecondToR(sum(Hdu.HTiU5)) as sumHTiU5".
									",SecondToR(sum(Hdu.HTiU6)) as sumHTiU6".
									",SecondToR(sum(Hdu.HTiU7)) as sumHTiU7".
									",SecondToR(sum(Hdu.HTiU8)) as sumHTiU8".
									",SecondToR(sum(Hdu.HTiU9)) as sumHTiU9".
									",SecondToR(sum(Hdu.HTiU10)) as sumHTiU10".
									",SecondToR(sum(Hdu.HTiU11)) as sumHTiU11".
									",SecondToR(sum(Hdu.HTiU12)) as sumHTiU12".
									",SecondToR(sum(Hdu.HTiU13)) as sumHTiU13".
									",SecondToR(sum(Hdu.HTiU14)) as sumHTiU14".
									",SecondToR(sum(Hdu.HTiU15)) as sumHTiU15".
									",SecondToR(sum(Hdu.HTiU16)) as sumHTiU16".
									",SecondToR(sum(Hdu.HTiU17)) as sumHTiU17".
									",SecondToR(sum(Hdu.HTiU18)) as sumHTiU18".
									",SecondToR(sum(Hdu.HTiU19)) as sumHTiU19".
									",SecondToR(sum(Hdu.HTiU20)) as sumHTiU20".
									",SecondToR(sum(Hdu.HTiU21)) as sumHTiU21".
									",SecondToR(sum(Hdu.HTiU22)) as sumHTiU22".
									",SecondToR(sum(Hdu.HTiU23)) as sumHTiU23";
							else
								$SelectSTi.=",sum(Hdu.RealSendTi) as sumRealSendTi".
									",sum(Hdu.RealReceiveTi) as sumRealReceiveTi".
									",sum(Hdu.FinishUsedTi) as sumFinishUsedTi".
									",sum(Hdu.BugUsedTi) as sumBugUsedTi".
									",sum(Hdu.Total) as sumTotal".
									",sum(Hdu.HTiU0) as sumHTiU0".
									",sum(Hdu.HTiU1) as sumHTiU1".
									",sum(Hdu.HTiU2) as sumHTiU2".
									",sum(Hdu.HTiU3) as sumHTiU3".
									",sum(Hdu.HTiU4) as sumHTiU4".
									",sum(Hdu.HTiU5) as sumHTiU5".
									",sum(Hdu.HTiU6) as sumHTiU6".
									",sum(Hdu.HTiU7) as sumHTiU7".
									",sum(Hdu.HTiU8) as sumHTiU8".
									",sum(Hdu.HTiU9) as sumHTiU9".
									",sum(Hdu.HTiU10) as sumHTiU10".
									",sum(Hdu.HTiU11) as sumHTiU11".
									",sum(Hdu.HTiU12) as sumHTiU12".
									",sum(Hdu.HTiU13) as sumHTiU13".
									",sum(Hdu.HTiU14) as sumHTiU14".
									",sum(Hdu.HTiU15) as sumHTiU15".
									",sum(Hdu.HTiU16) as sumHTiU16".
									",sum(Hdu.HTiU17) as sumHTiU17".
									",sum(Hdu.HTiU18) as sumHTiU18".
									",sum(Hdu.HTiU19) as sumHTiU19".
									",sum(Hdu.HTiU20) as sumHTiU20".
									",sum(Hdu.HTiU21) as sumHTiU21".
									",sum(Hdu.HTiU22) as sumHTiU22".
									",sum(Hdu.HTiU23) as sumHTiU23";
						}

						$IndexColumn="Row_Number";
					}
					else{//No group by
						$GroupByStr="";
						$IndexColumn="DailyUsage_Id";
						$ExtraUsersInfo=Get_Input('GET','DB','ExtraUsersInfo','INT',0,1,0,0);

						if($ReportType=="Traffic"){
							if($FormatData)
								$SelectStr="Hdu.DailyUsage_Id,SHDATETIMESTR(Hdu.CreateDT) as CreateDT,SHDATESTR(Hdu.UsageDate) as UsageDate,Hu.UserName".
									($ExtraUsersInfo?",Hv.VispName as Visp,Hr.ResellerName as Reseller,Hc.CenterName as Center,Hs.SupporterName as Supporter,Hu.Name,Hu.Family,Hu.Organization,Hservice.ServiceName as ActiveServiceBase":"").
									",ByteToR(Hdu.RealSendTr) as RealSendTr,ByteToR(Hdu.RealReceiveTr) as RealReceiveTr".
									",ByteToR(Hdu.FinishUsedTr) as FinishUsedTr,ByteToR(Hdu.BugUsedTr) as BugUsedTr,ByteToR(Hdu.Total) as Total".
									",ByteToR(Hdu.HTrU0) as HTrU0,ByteToR(Hdu.HTrU1) as HTrU1,ByteToR(Hdu.HTrU2) as HTrU2,ByteToR(Hdu.HTrU3) as HTrU3,ByteToR(Hdu.HTrU4) as HTrU4,ByteToR(Hdu.HTrU5) as HTrU5".
									",ByteToR(Hdu.HTrU6) as HTrU6,ByteToR(Hdu.HTrU7) as HTrU7,ByteToR(Hdu.HTrU8) as HTrU8,ByteToR(Hdu.HTrU9) as HTrU9,ByteToR(Hdu.HTrU10) as HTrU10,ByteToR(Hdu.HTrU11) as HTrU11".
									",ByteToR(Hdu.HTrU12) as HTrU12,ByteToR(Hdu.HTrU13) as HTrU13,ByteToR(Hdu.HTrU14) as HTrU14,ByteToR(Hdu.HTrU15) as HTrU15,ByteToR(Hdu.HTrU16) as HTrU16,ByteToR(Hdu.HTrU17) as HTrU17".
									",ByteToR(Hdu.HTrU18) as HTrU18,ByteToR(Hdu.HTrU19) as HTrU19,ByteToR(Hdu.HTrU20) as HTrU20,ByteToR(Hdu.HTrU21) as HTrU21,ByteToR(Hdu.HTrU22) as HTrU22,ByteToR(Hdu.HTrU23) as HTrU23";
							else
								$SelectStr="Hdu.DailyUsage_Id,SHDATETIMESTR(Hdu.CreateDT) as CreateDT,SHDATESTR(Hdu.UsageDate) as UsageDate,Hu.UserName".
									($ExtraUsersInfo?",Hv.VispName as Visp,Hr.ResellerName as Reseller,Hc.CenterName as Center,Hs.SupporterName as Supporter,Hu.Name,Hu.Family,Hu.Organization,Hservice.ServiceName as ActiveServiceBase":"").
									",Hdu.RealSendTr,Hdu.RealReceiveTr,Hdu.FinishUsedTr,Hdu.BugUsedTr,Hdu.Total".
									",Hdu.HTrU0,Hdu.HTrU1,Hdu.HTrU2,Hdu.HTrU3,Hdu.HTrU4,Hdu.HTrU5".
									",Hdu.HTrU6,Hdu.HTrU7,Hdu.HTrU8,Hdu.HTrU9,Hdu.HTrU10,Hdu.HTrU11".
									",Hdu.HTrU12,Hdu.HTrU13,Hdu.HTrU14,Hdu.HTrU15,Hdu.HTrU16,Hdu.HTrU17".
									",Hdu.HTrU18,Hdu.HTrU19,Hdu.HTrU20,Hdu.HTrU21,Hdu.HTrU22,Hdu.HTrU23";
								
							$ColumnStr="DailyUsage_Id,CreateDT,UsageDate,UserName".
								($ExtraUsersInfo?",Visp,Reseller,Center,Supporter,Name,Family,Organization,ActiveServiceBase":"").
								",RealSendTr,RealReceiveTr,FinishUsedTr,BugUsedTr,Total".
								",HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11".
								",HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23";
						}
						else{//Time

							if($FormatData)
								$SelectStr="Hdu.DailyUsage_Id,SHDATETIMESTR(Hdu.CreateDT) as CreateDT,SHDATESTR(Hdu.UsageDate) as UsageDate,Hu.UserName".
									($ExtraUsersInfo?",Hv.VispName as Visp,Hr.ResellerName as Reseller,Hc.CenterName as Center,Hs.SupporterName as Supporter,Hu.Name,Hu.Family,Hu.Organization,Hservice.ServiceName as ActiveServiceBase":"").
									",SecondToR(Hdu.RealUsedTime) as RealUsedTime".
									",SecondToR(Hdu.FinishUsedTi) as FinishUsedTi,SecondToR(Hdu.BugUsedTi) as BugUsedTi,SecondToR(Hdu.Total) as Total".
									",SecondToR(Hdu.HTiU0) as HTiU0,SecondToR(Hdu.HTiU1) as HTiU1,SecondToR(Hdu.HTiU2) as HTiU2,SecondToR(Hdu.HTiU3) as HTiU3,SecondToR(Hdu.HTiU4) as HTiU4,SecondToR(Hdu.HTiU5) as HTiU5".
									",SecondToR(Hdu.HTiU6) as HTiU6,SecondToR(Hdu.HTiU7) as HTiU7,SecondToR(Hdu.HTiU8) as HTiU8,SecondToR(Hdu.HTiU9) as HTiU9,SecondToR(Hdu.HTiU10) as HTiU10,SecondToR(Hdu.HTiU11) as HTiU11".
									",SecondToR(Hdu.HTiU12) as HTiU12,SecondToR(Hdu.HTiU13) as HTiU13,SecondToR(Hdu.HTiU14) as HTiU14,SecondToR(Hdu.HTiU15) as HTiU15,SecondToR(Hdu.HTiU16) as HTiU16,SecondToR(Hdu.HTiU17) as HTiU17".
									",SecondToR(Hdu.HTiU18) as HTiU18,SecondToR(Hdu.HTiU19) as HTiU19,SecondToR(Hdu.HTiU20) as HTiU20,SecondToR(Hdu.HTiU21) as HTiU21,SecondToR(Hdu.HTiU22) as HTiU22,SecondToR(Hdu.HTiU23) as HTiU23";
							else
								$SelectStr="Hdu.DailyUsage_Id,SHDATETIMESTR(Hdu.CreateDT) as CreateDT,SHDATESTR(Hdu.UsageDate) as UsageDate,Hu.UserName".
									($ExtraUsersInfo?",Hv.VispName as Visp,Hr.ResellerName as Reseller,Hc.CenterName as Center,Hs.SupporterName as Supporter,Hu.Name,Hu.Family,Hu.Organization,Hservice.ServiceName as ActiveServiceBase":"").
									",Hdu.RealUsedTime,Hdu.FinishUsedTi,Hdu.BugUsedTi,Hdu.Total".
									",Hdu.HTiU0,Hdu.HTiU1,Hdu.HTiU2,Hdu.HTiU3,Hdu.HTiU4,Hdu.HTiU5".
									",Hdu.HTiU6,Hdu.HTiU7,Hdu.HTiU8,Hdu.HTiU9,Hdu.HTiU10,Hdu.HTiU11".
									",Hdu.HTiU12,Hdu.HTiU13,Hdu.HTiU14,Hdu.HTiU15,Hdu.HTiU16,Hdu.HTiU17".
									",Hdu.HTiU18,Hdu.HTiU19,Hdu.HTiU20,Hdu.HTiU21,Hdu.HTiU22,Hdu.HTiU23";
								
							$ColumnStr="DailyUsage_Id,CreateDT,UsageDate,UserName".
								($ExtraUsersInfo?",Visp,Reseller,Center,Supporter,Name,Family,Organization,ActiveServiceBase":"").
								",RealUsedTime,FinishUsedTi,BugUsedTi,Total".
								",HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11".
								",HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23";
							
						}
							
							
					
					}
					
					$sql="Select $SelectStr from $Table Hdu ".
						"join Huser Hu on Hdu.User_Id=Hu.User_Id ".
						(((strpos($SelectStr,"Hr.")!==false)||(strpos($WhereStr,"Hr.")!==false))?"join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ":"").
						(((strpos($SelectStr,"Hv.")!==false)||(strpos($WhereStr,"Hv.")!==false))?"join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ":"").
						(((strpos($SelectStr,"Hc.")!==false)||(strpos($WhereStr,"Hc.")!==false))?"join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ":"").
						(((strpos($SelectStr,"Hs.")!==false)||(strpos($WhereStr,"Hs.")!==false))?"join Hsupporter Hs on Hu.Supporter_Id=Hs.Supporter_Id ":"").
						"left join Hservice on Hu.Service_Id=Hservice.Service_Id ".
						(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
						"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserDailyUsageList and Hrp2.ISPermit='Yes' ":"").
						"where $WhereStr $GroupByStr $SortStr";			
					
					if($req=="SaveToFile"){
						$Type=Get_Input('GET','DB','Type','ARRAY',array("CSV","XLSX"),0,0,0);
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
							header('Content-Disposition: attachment;filename="TotalUsageReport.csv";');
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
							header('Content-Disposition: attachment; filename="TotalUsageReport.xlsx"');
							
							ini_set('memory_limit','64M');
							set_time_limit(300);
							require_once("../../lib/Export_lib/Excel.php");
							$writer = new XLSXWriter();
							
							$data =  $conn->sql->get_next($res);
							$writer->writeSheetRow("TotalUsageReport",array_keys($data));
							
							while($data){
								$writer->writeSheetRow("TotalUsageReport",$data);
								$data =  $conn->sql->get_next($res);
							}
							echo $writer->writeToString();
						}
						DSDebug(0,"After Save Used Memory=[".number_format(memory_get_usage())."]");
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
    case "SelectReseller":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT Reseller_Id,ResellerName From Hreseller where ISOperator='No' order by ResellerName Asc";
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
    case "SelectActiveServiceBase":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="Select 0 as Service_Id,'' as ServiceName union all SELECT Service_Id,ServiceName From Hservice where ServiceType='Base' and IsDel='No' order by ServiceName Asc";
                            $options->render_sql($sql,"","Service_Id,ServiceName","","");
    break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
