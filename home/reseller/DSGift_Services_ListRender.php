<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSGift_Services_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSGift_Services_ListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.Gift.Services.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$Gift_Id=Get_Input('GET','DB','Gift_Id','INT',1,4294967295,0,0);
				
				$sql="Select Service_Gift_Id,ServiceType,s.ISEnable,ServiceName,count(1) as GiftCount ".
					"From Hservice_gift g join Hservice s on g.Service_Id=s.Service_Id ".
					"Where Gift_Id='$Gift_Id' and IsDel='No' $sqlfilter group by g.Service_Id $SortStr ";

				$req=Get_InputIgnore('GET','DB','req','ARRAY',array("SaveToFile"),0,0,0);
				
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
						header('Content-Disposition: attachment;filename="GiftService.csv";');
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
						header('Content-Disposition: attachment; filename="GiftService.xlsx"');
						
						ini_set('memory_limit','64M');
						set_time_limit(300);
						require_once("../../lib/Export_lib/Excel.php");
						$writer = new XLSXWriter();
						
						$data =  $conn->sql->get_next($res);
						$writer->writeSheetRow("GiftService",array_keys($data));
						
						while($data){
							$writer->writeSheetRow("GiftService",$data);
							$data =  $conn->sql->get_next($res);
						}
						echo $writer->writeToString();
					}
					DSDebug(0,"After Save Used Memory=[".number_format(memory_get_usage())."]");
				}
				else{
					
					function color_rows($row){
						$data = $row->get_value("ServiceType");
						if($data=='Base')
							$row->set_row_style("color:black");
						else if($data=='ExtraCredit')
							$row->set_row_style("color:blue");
						else if($data=='IP')
							$row->set_row_style("color:red");
						else if($data=='Other')
							$row->set_row_style("color:green");
					}
					
					$Fields="Service_Gift_Id,ServiceType,ISEnable,ServiceName,GiftCount";
					DSGridRender_Sql(-1,$sql,"Service_Gift_Id",$Fields,"","","color_rows");
				}
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>