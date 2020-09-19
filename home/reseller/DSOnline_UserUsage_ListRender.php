<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSOnline_UserUsage_ListRender.php ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSOnline_UserUsage_ListRender.php->List ********************************************");
				exitifnotpermit(0,"Admin.Center.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by User_Id desc";
				
				$ReportItem=Get_Input('GET','DB','ReportItem','ARRAY',array("Traffic","Time","HourlyTrafficUsage","HourlyTimeUsage","Gifts","Etc"),0,0,0);
				$Formatted=Get_Input('GET','DB','Formatted','INT',0,1,0,0);
				$req=Get_Input('GET','DB','req','ARRAY',array("ShowInGrid","SaveToFile"),0,0,0);
				if($req=="ShowInGrid"){
					$UnlimitTraffic=999999999999999999;
					$UnlimitTime=9999999999;
				}
				else{
					$UnlimitTraffic="'UL'";
					$UnlimitTime="'UL'";
				}
				$sql="SELECT Tu.User_Id,Username,Name,Family,{$DT}DateTimeStr(LastRequestDT) as LastRequestDT,";
				$ColumnStr="User_Id,Username,Name,Family,LastRequestDT,";
				switch ($ReportItem){
					case "Traffic":
							//STrA,STrU,STrR,YTrA,YTrU,YTrR,MTrA,MTrU,MTrR,WTrA,WTrU,WTrR,DTrA,DTrU,DTrR,ETrA,ETrU,ETrR,RealSendTr,RealReceiveTr,BugUsedTr,FinishUsedTr,ReturnTr
							$sqlfilter=str_replace(
								"STrR","if(STrA,(STrA-STrU),'UL')",str_replace(
									"YTrR","if(YTrA,(YTrA-YTrU),'UL')",str_replace(
										"MTrR","if(MTrA,(MTrA-MTrU),'UL')",str_replace(
											"WTrR","if(WTrA,(WTrA-WTrU),'UL')",str_replace(
												"DTrR","if(DTrA,(DTrA-DTrU),'UL')",str_replace(
													"ETrR","(Tu.ETrA-Tu.ETrU)",$sqlfilter
												)
											)
										)
									)
								)
							);
							$F="Formatted_";
							if($Formatted)
								$FO="ByteToR";
							else
								$FO="";
							$sql.="if(STrA,{$FO}(STrA),$UnlimitTraffic) as {$F}STrA,".
									"{$FO}(STrU) as {$F}STrU,".
									"if(STrA,{$FO}(STrA-STrU),$UnlimitTraffic) as STrR,".
									"if(YTrA,{$FO}(YTrA),$UnlimitTraffic) as {$F}YTrA,".
									"{$FO}(YTrU) as {$F}YTrU,".
									"if(YTrA,{$FO}(YTrA-YTrU),$UnlimitTraffic) as YTrR,".
									"if(MTrA,{$FO}(MTrA),$UnlimitTraffic) as {$F}MTrA,".
									"{$FO}(MTrU) as {$F}MTrU,".
									"if(MTrA,{$FO}(MTrA-MTrU),$UnlimitTraffic) as MTrR,".
									"if(WTrA,{$FO}(WTrA),$UnlimitTraffic) as {$F}WTrA,".
									"{$FO}(WTrU) as {$F}WTrU,".
									"if(WTrA,{$FO}(WTrA-WTrU),$UnlimitTraffic) as WTrR,".
									"if(DTrA,{$FO}(DTrA),$UnlimitTraffic) as {$F}DTrA,".
									"{$FO}(DTrU) as {$F}DTrU,".
									"if(DTrA,{$FO}(DTrA-DTrU),$UnlimitTraffic) as DTrR,".
									"{$FO}(Tu.ETrA) as {$F}ETrA,".
									"{$FO}(Tu.ETrU) as {$F}ETrU,".
									"{$FO}(Tu.ETrA-Tu.ETrU) as ETrR,".
									"{$FO}(RealSendTr) as {$F}RealSendTr,".
									"{$FO}(RealReceiveTr) as {$F}RealReceiveTr,".
									"{$FO}(BugUsedTr) as {$F}BugUsedTr,".
									"{$FO}(FinishUsedTr) as {$F}FinishUsedTr,".
									"{$FO}(ReturnTr) as {$F}ReturnTr".
								" from Tuser_usage Tu join Huser Hu on Tu.User_Id=Hu.User_Id";
							$ColumnStr.="{$F}STrA,{$F}STrU,STrR,{$F}YTrA,{$F}YTrU,YTrR,{$F}MTrA,{$F}MTrU,MTrR,{$F}WTrA,{$F}WTrU,WTrR,{$F}DTrA,{$F}DTrU,DTrR,{$F}ETrA,{$F}ETrU,ETrR,{$F}RealSendTr,{$F}RealReceiveTr,{$F}BugUsedTr,{$F}FinishUsedTr,{$F}ReturnTr";
							$UnlimitArray=Array("{$F}STrA","STrR","{$F}YTrA","YTrR","{$F}MTrA","MTrR","{$F}WTrA","WTrR","{$F}DTrA","DTrR");
							$UnlimitThreshold=$UnlimitTraffic;
						break;
					case "Time":
						//STiA,STiU,STiR,YTiA,YTiU,YTiR,MTiA,MTiU,MTiR,WTiA,WTiU,WTiR,DTiA,DTiU,DTiR,ETiA,ETiU,ETiR,RealUsedTime,BugUsedTi,FinishUsedTi
							$sqlfilter=str_replace(
								"STiR","if(STiA,(STiA-STiU),'UL')",str_replace(
									"YTiR","if(YTiA,(YTiA-YTiU),'UL')",str_replace(
										"MTiR","if(MTiA,(MTiA-MTiU),'UL')",str_replace(
											"WTiR","if(WTiA,(WTiA-WTiU),'UL')",str_replace(
												"DTiR","if(DTiA,(DTiA-DTiU),'UL')",str_replace(
													"ETiR","(Tu.ETiA-Tu.ETiU)",$sqlfilter
												)
											)
										)
									)
								)
							);
							$F="Formatted_";
							if($Formatted)
								$FO="SecondToR";
							else
								$FO="";
							$sql.="if(STiA,{$FO}(STiA),$UnlimitTime) as {$F}STiA,".
									"{$FO}(STiU) as {$F}STiU,".
									"if(STiA,{$FO}(STiA-STiU),$UnlimitTime) as STiR,".
									"if(YTiA,{$FO}(YTiA),$UnlimitTime) as {$F}YTiA,".
									"{$FO}(YTiU) as {$F}YTiU,".
									"if(YTiA,{$FO}(YTiA-YTiU),$UnlimitTime) as YTiR,".
									"if(MTiA,{$FO}(MTiA),$UnlimitTime) as {$F}MTiA,".
									"{$FO}(MTiU) as {$F}MTiU,".
									"if(MTiA,{$FO}(MTiA-MTiU),$UnlimitTime) as MTiR,".
									"if(WTiA,{$FO}(WTiA),$UnlimitTime) as {$F}WTiA,".
									"{$FO}(WTiU) as {$F}WTiU,".
									"if(WTiA,{$FO}(WTiA-WTiU),$UnlimitTime) as WTiR,".
									"if(DTiA,{$FO}(DTiA),$UnlimitTime) as {$F}DTiA,".
									"{$FO}(DTiU) as {$F}DTiU,".
									"if(DTiA,{$FO}(DTiA-DTiU),$UnlimitTime) as DTiR,".
									"{$FO}(Tu.ETiA) as {$F}ETiA,".
									"{$FO}(Tu.ETiU) as {$F}ETiU,".
									"{$FO}(Tu.ETiA-Tu.ETiU) as ETiR,".
									"{$FO}(RealUsedTime) as {$F}RealUsedTime,".
									"{$FO}(BugUsedTi) as {$F}BugUsedTi,".
									"{$FO}(FinishUsedTi) as {$F}FinishUsedTi".
								" from Tuser_usage Tu join Huser Hu on Tu.User_Id=Hu.User_Id";
							$ColumnStr.="{$F}STiA,{$F}STiU,STiR,{$F}YTiA,{$F}YTiU,YTiR,{$F}MTiA,{$F}MTiU,MTiR,{$F}WTiA,{$F}WTiU,WTiR,{$F}DTiA,{$F}DTiU,DTiR,{$F}ETiA,{$F}ETiU,ETiR,{$F}RealUsedTime,{$F}BugUsedTi,{$F}FinishUsedTi";					
							$UnlimitArray=Array("{$F}STiA","STiR","{$F}YTiA","YTiR","{$F}MTiA","MTiR","{$F}WTiA","WTiR","{$F}DTiA","DTiR");
							$UnlimitThreshold=$UnlimitTime;
						break;
					case "HourlyTrafficUsage":
					//HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11,HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23
							$F="Formatted_";
							if($Formatted)
								$FO="ByteToR";
							else
								$FO="";
							$sql.="{$FO}(HTrU0) as {$F}HTrU0,".
									"{$FO}(HTrU1) as {$F}HTrU1,".
									"{$FO}(HTrU2) as {$F}HTrU2,".
									"{$FO}(HTrU3) as {$F}HTrU3,".
									"{$FO}(HTrU4) as {$F}HTrU4,".
									"{$FO}(HTrU5) as {$F}HTrU5,".
									"{$FO}(HTrU6) as {$F}HTrU6,".
									"{$FO}(HTrU7) as {$F}HTrU7,".
									"{$FO}(HTrU8) as {$F}HTrU8,".
									"{$FO}(HTrU9) as {$F}HTrU9,".
									"{$FO}(HTrU10) as {$F}HTrU10,".
									"{$FO}(HTrU11) as {$F}HTrU11,".
									"{$FO}(HTrU12) as {$F}HTrU12,".
									"{$FO}(HTrU13) as {$F}HTrU13,".
									"{$FO}(HTrU14) as {$F}HTrU14,".
									"{$FO}(HTrU15) as {$F}HTrU15,".
									"{$FO}(HTrU16) as {$F}HTrU16,".
									"{$FO}(HTrU17) as {$F}HTrU17,".
									"{$FO}(HTrU18) as {$F}HTrU18,".
									"{$FO}(HTrU19) as {$F}HTrU19,".
									"{$FO}(HTrU20) as {$F}HTrU20,".
									"{$FO}(HTrU21) as {$F}HTrU21,".
									"{$FO}(HTrU22) as {$F}HTrU22,".
									"{$FO}(HTrU23) as {$F}HTrU23".
								" from Tuser_usage Tu join Huser Hu on Tu.User_Id=Hu.User_Id";
							$ColumnStr.="{$F}HTrU0,{$F}HTrU1,{$F}HTrU2,{$F}HTrU3,{$F}HTrU4,{$F}HTrU5,{$F}HTrU6,{$F}HTrU7,{$F}HTrU8,{$F}HTrU9,{$F}HTrU10,{$F}HTrU11,{$F}HTrU12,{$F}HTrU13,{$F}HTrU14,{$F}HTrU15,{$F}HTrU16,{$F}HTrU17,{$F}HTrU18,{$F}HTrU19,{$F}HTrU20,{$F}HTrU21,{$F}HTrU22,{$F}HTrU23";
							$UnlimitThreshold=0;
						break;
					case "HourlyTimeUsage":
					//HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11,HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23
							$F="Formatted_";
							if($Formatted)
								$FO="SecondToR";
							else
								$FO="";
							$sql.="{$FO}(HTiU0) as {$F}HTiU0,".
									"{$FO}(HTiU1) as {$F}HTiU1,".
									"{$FO}(HTiU2) as {$F}HTiU2,".
									"{$FO}(HTiU3) as {$F}HTiU3,".
									"{$FO}(HTiU4) as {$F}HTiU4,".
									"{$FO}(HTiU5) as {$F}HTiU5,".
									"{$FO}(HTiU6) as {$F}HTiU6,".
									"{$FO}(HTiU7) as {$F}HTiU7,".
									"{$FO}(HTiU8) as {$F}HTiU8,".
									"{$FO}(HTiU9) as {$F}HTiU9,".
									"{$FO}(HTiU10) as {$F}HTiU10,".
									"{$FO}(HTiU11) as {$F}HTiU11,".
									"{$FO}(HTiU12) as {$F}HTiU12,".
									"{$FO}(HTiU13) as {$F}HTiU13,".
									"{$FO}(HTiU14) as {$F}HTiU14,".
									"{$FO}(HTiU15) as {$F}HTiU15,".
									"{$FO}(HTiU16) as {$F}HTiU16,".
									"{$FO}(HTiU17) as {$F}HTiU17,".
									"{$FO}(HTiU18) as {$F}HTiU18,".
									"{$FO}(HTiU19) as {$F}HTiU19,".
									"{$FO}(HTiU20) as {$F}HTiU20,".
									"{$FO}(HTiU21) as {$F}HTiU21,".
									"{$FO}(HTiU22) as {$F}HTiU22,".
									"{$FO}(HTiU23) as {$F}HTiU23".
								" from Tuser_usage Tu join Huser Hu on Tu.User_Id=Hu.User_Id";
							$ColumnStr.="{$F}HTiU0,{$F}HTiU1,{$F}HTiU2,{$F}HTiU3,{$F}HTiU4,{$F}HTiU5,{$F}HTiU6,{$F}HTiU7,{$F}HTiU8,{$F}HTiU9,{$F}HTiU10,{$F}HTiU11,{$F}HTiU12,{$F}HTiU13,{$F}HTiU14,{$F}HTiU15,{$F}HTiU16,{$F}HTiU17,{$F}HTiU18,{$F}HTiU19,{$F}HTiU20,{$F}HTiU21,{$F}HTiU22,{$F}HTiU23";
							$UnlimitThreshold=0;
						break;
					case "Gifts":
					//GiftName,GiftEndDT,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftExtraTi,GiftMikrotikRateName
							$F="Formatted_";
							if($Formatted){
								$FTrO="ByteToR";
								$FTiO="SecondToR";
							}
							else{
								$FTrO="";
								$FTiO="";
							}
							$sql.="GiftName,".
									"{$DT}DateTimeStr(GiftEndDT) as GiftEndDT,".
									"Tu.GiftTrafficRate,".
									"Tu.GiftTimeRate,".
									"{$FTrO}(Tu.GiftExtraTr) as {$F}GiftExtraTr,".
									"{$FTiO}(Tu.GiftExtraTi) as {$F}GiftExtraTi,".
									"MikrotikRateName as GiftMikrotikRateName".
								" from Tuser_usage Tu join Huser Hu on Tu.User_Id=Hu.User_Id".
								" left join Huser_gift Hug on Tu.User_Gift_Id=Hug.User_Gift_Id".
								" left join Hgift Hg on Hug.Gift_Id=Hg.Gift_Id".
								" left join Hmikrotikrate Hm on Tu.GiftMikrotikRate_Id=Hm.MikrotikRate_Id";
							$ColumnStr.="GiftName,GiftEndDT,GiftTrafficRate,GiftTimeRate,{$F}GiftExtraTr,{$F}GiftExtraTi,GiftMikrotikRateName";
							$UnlimitThreshold=0;
						break;
					case "Etc":
					//LastSaveDT,ISFairService,UPFairStatus,FairMikrotikRateName
					
							$sql.="{$DT}DateTimeStr(LastSaveDT) as LastSaveDT,".
									"ISFairService,".
									"UPFairStatus,".
									"MikrotikRateName as FairMikrotikRateName".
								" from Tuser_usage Tu join Huser Hu on Tu.User_Id=Hu.User_Id".
								" left join Hmikrotikrate Hm on Tu.FairMikrotikRate_Id=Hm.MikrotikRate_Id";
							$ColumnStr.="LastSaveDT,ISFairService,UPFairStatus,FairMikrotikRateName";
							$UnlimitThreshold=0;
						break;
					default:
						ExitError("درخواست ناشناخته");
				}
				$sql.=" Where 1 $sqlfilter $SortStr";
				
				if($req=="ShowInGrid"){
					if(count($UnlimitArray)>0){
						DSDebug(0,"UnlimitThreshold=$UnlimitThreshold");
						function color_rows($row){
							global $UnlimitArray,$UnlimitThreshold;
							foreach($UnlimitArray as $Value){
								if($row->get_value($Value)>=$UnlimitThreshold)
									$row->set_value($Value,"UL");
							}
						}
						DSGridRender_Sql(100,$sql,"User_Id",$ColumnStr,"","","color_rows");
					}
					else
						DSGridRender_Sql(100,$sql,"User_Id",$ColumnStr,"","","");
				}
				else if($req=="SaveToFile"){
						$res = $conn->sql->query($sql);
						$data =  $conn->sql->get_next($res);
						$n=$conn->sql->get_affected_rows();
						if($n<=0){
							echo "<html><head><script type=\"text/javascript\">";
							echo "window.onload = function(){alert('No data to save!');window.close();}";
							echo "</script></head><body></body></html>";
							exit();
						}
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="Online_UserUsage.csv";');
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
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>