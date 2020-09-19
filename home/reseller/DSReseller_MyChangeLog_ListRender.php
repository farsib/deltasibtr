<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSReseller_MyChangeLog_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"CRM.MyChangeLog.List");

$act=Get_Input('GET','DB','act','ARRAY',array(
	"list",
    "SelectDataName"
	),0,0,0);		
	
switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_MyChangeLog_ListRender->Filter********************************************");
				$FieldsItems=Array(
					Array("Hldb.Comment","Hldb.ChildDataName"),
					Array("Hldb.ClientIP"),
					Array("Hldb.DataName","Hldb.LogType")
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
				$WhereStr="(Hldb.Reseller_Id=$LReseller_Id)";
				
				$ChkStatus=Get_Input('GET','DB','Chk0','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value0','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (Hldb.LogDbCDT >= '$FieldValue')";					
				}
				$ChkStatus=Get_Input('GET','DB','Chk1','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value1','STR',0,100,0,0);
					$FieldValue=DBSelectAsString("select SHDATETIMESTRTOMSTR('$FieldValue')");
					$WhereStr.=" AND (Hldb.LogDbCDT <= '$FieldValue')";					
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
					$FieldValue=Get_Input('GET','DB','Value30','STR',0,20,0,0);
					$FieldName=$FieldsItems[1][$FieldIndex];
					
					if(strpos($CompareOperator,"like")!==false)
						$FieldName="INET_NTOA($FieldName)";
					else
						$FieldValue=DBSelectAsString("select INET_ATON('$FieldValue')");
					
					
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					
					$ChkStatus=Get_Input('GET','DB','Chk31','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt3','STR',0,5,0,0);
						$FieldIndex=Get_Input('GET','DB','Field31','INT',0,10,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp31','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value31','STR',0,20,0,0);
						$FieldName=$FieldsItems[1][$FieldIndex];							
						
						if(strpos($CompareOperator,"like")!==false)
							$FieldName="INET_NTOA($FieldName)";
						else
							$FieldValue=DBSelectAsString("select INET_ATON('$FieldValue')");

					
						$WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue')";
					}
					else $WhereStr.=")";					
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk4','INT',0,1,0,0);
				if($ChkStatus){
					$FieldIndex=Get_Input('GET','DB','Field4','INT',0,10,0,0);
					$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4','STR',0,10,0,0));
					$FieldValue=str_replace(",","','",Get_Input('GET','DB','Value4','STR',0,100,0,0));
					$FieldName=$FieldsItems[2][$FieldIndex];
					$WhereStr.=" AND ($FieldName $CompareOperator ('$FieldValue'))";				
				}
				
			
				$req=Get_Input('GET','DB','req','ARRAY',array("GetRecordCount","ShowInGrid","SaveToFile"),0,0,0);
				
				DSDebug(0,"--------------------**************************************--------------------------------");
				DSDebug(0,"WhereStr='".$WhereStr."'");
				DSDebug(0,"--------------------**************************************--------------------------------");
				
				if($req=="GetRecordCount"){
					$sql="select count(1) from Hlogdb Hldb ".
						"left join Hreseller r on Hldb.Reseller_Id=r.Reseller_Id ".
						"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif(($req=="ShowInGrid")or($req=="SaveToFile")){
					
					$IndexColumn="Logdb_Id";
					
					$SelectStr="Hldb.Logdb_Id,SHDATETIMESTR(Hldb.LogDbCDT) as LogDbCDT,r.ResellerName".
						",INET_NTOA(Hldb.ClientIP) as ClientIP,Hldb.LogType,Hldb.DataName".
						",Hldb.DataId as DataItemId,Hldb.ChildDataName,Hldb.Comment";				

					$ColumnStr="Logdb_Id,LogDbCDT,ResellerName,ClientIP,LogType,DataName".
						",DataItemId,ChildDataName,Comment";
					
					$sql="Select $SelectStr from Hlogdb Hldb ".
						"left join Hreseller r on Hldb.Reseller_Id=r.Reseller_Id ".
						"where $WhereStr $SortStr";			
					
					if($req=="SaveToFile"){
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; charset=utf-8; filename="MyChangeLog.csv";');
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
						DSGridRender_Sql(100,$sql,$IndexColumn,$ColumnStr,"","","");
					}
				}
				else
					echo "~Unknown Request";
       break;  
     case "SelectDataName":
							require_once('../../lib/connector/options_connector.php');
							$options = new SelectOptionsConnector($mysqli,"MySQLi");
							$sql="SELECT distinct DataName,DataName from Hlogdb order by DataName desc";
							$options->render_sql($sql,"","DataName,DataName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
