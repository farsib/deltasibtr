<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSBatchProcess_ListRender.........................................................................");

PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

if($LReseller_Id!=1) ExitError('فقط ادمین می تواند عملیات گروهی را انجام دهد');

$act=Get_Input('GET','DB','act','ARRAY',array(
    "list",
	"SelectBatchProcess",
	"GetPreviousBatchProcessComment",
    "CancelBeforeStart",
    "SelectActiveServiceName",
    "SelectVisp",
    "SelectReseller",
    "SelectCenter",
    "SelectSupporter",
    "SelectUserStatus",
    "SelectMikrotikRate",
    "SelectIPPool",
    "SelectLoginTime",
    "SelectOffFormula",
    "SelectActiveDirectory",
	"TakingCare",
	"UploadFile",
	"SubmitFiles"
    ),0,0,0);
	

set_time_limit(500);	
	
switch ($act) {
    case "list":
				DSDebug(0,"DSBatchProcess_ListRender->list********************************************");
				$sql="SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')";
				$res=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				if($res!='Yes')
					ExitError("آی پی شا معتبر نیست،ابتدا آی پی خود را در 'آی پی که مسدود نمی شود' وارد کنید");
				
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='') $SortStr="";
				else{
					$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
					$SortStr="Order by $SortField $SortOrder";
				}
				DSDebug(0,"SortStr=$SortStr");
				
				
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
				$DTNow=DBSelectAsString("Select concat(ShdateNow(),' ',CURRENT_TIME())");
				function color_rows($row){
					global $DTNow;
					$EndDate = $row->get_value("EndDate");
					if($EndDate<=$DTNow)
						$row->set_row_style("color:red");
				}
				
				$ChkStatus=Get_Input('GET','DB','Chk0','INT',0,1,0,0);
				if($ChkStatus){
					$FieldValue=Get_Input('GET','DB','Value0','STR',0,500,0,0);
					$PreviousBatchJoinStr="join Hbatchprocess_users Hbi on Hbi.BatchProcess_Id=$FieldValue and Hu.User_Id=Hbi.User_Id ";
				}
				else
					$PreviousBatchJoinStr="";
				
				$WhereStr='1';
				for($i=1;$i<=2;++$i){
					$ChkStatus=Get_Input('GET','DB','Chk1_'.$i.'_1','INT',0,1,0,0);
					if($ChkStatus){
						$FieldName="Hu.".Get_Input('GET','DB','Field1_'.$i.'_1','STR',0,100,0,0);
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp1_'.$i.'_1','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value1_'.$i.'_1','STR',0,500,0,0);
						if((strpos($CompareOperator,"like")!==false)and(strpos($FieldValue,"%")===false))
							$FieldValue="%".$FieldValue."%";
						$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
						$ChkStatus=Get_Input('GET','DB','Chk1_'.$i.'_2','INT',0,1,0,0);
						if($ChkStatus){
							$OptionButton=Get_Input('GET','DB','Opt1_'.$i,'STR',0,5,0,0);
							$FieldName="Hu.".Get_Input('GET','DB','Field1_'.$i.'_2','STR',0,100,0,0);					
							$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp1_'.$i.'_2','STR',0,10,0,0));
							$FieldValue=Get_Input('GET','DB','Value1_'.$i.'_2','STR',0,500,0,0);
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
					else if($FieldName=='TimeType')
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
						else if($FieldName=='TimeType')
							$FieldName="if(Hu.Service_Id=0,'NoActiveService',if((Tuu.STiA<>0)or(Tuu.YTiA<>0)or(Tuu.MTiA<>0)or(Tuu.WTiA<>0)or(Tuu.DTiA<>0),'limit','UnLimit'))";
						else 
							$FieldName="Hu.$FieldName";
						
						$WhereStr.=" $OptionButton $FieldName='$FieldValue'";
					}
					$WhereStr.=")";
				}

				// $ChkStatus=Get_Input('GET','DB','Chk4_1_1','INT',0,1,0,0);
				// if($ChkStatus){
					// $FieldName="Tuu.".Get_Input('GET','DB','Field4_1_1','STR',0,100,0,0);
					// $CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_1_1','STR',0,10,0,0));
					// $FieldValue=1048576*Get_Input('GET','DB','Value4_1_1','STR',0,500,0,0);
					// $WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					// $ChkStatus=Get_Input('GET','DB','Chk4_1_2','INT',0,1,0,0);
					// if($ChkStatus){
						// $OptionButton=Get_Input('GET','DB','Opt4_'.$i,'STR',0,5,0,0);
						// $FieldName="Tuu.".Get_Input('GET','DB','Field4_1_2','STR',0,100,0,0);					
						// $CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_1_2','STR',0,10,0,0));
						// $FieldValue=1048576*Get_Input('GET','DB','Value4_1_2','STR',0,500,0,0);
						// $WhereStr.=" $OptionButton $FieldName $CompareOperator '$FieldValue'";												
					// }
					// $WhereStr.=" )";
				// }
				
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
					$FieldValue=Get_Input('GET','DB','Value4_2_1','STR',0,500,0,0);
					$WhereStr.=" AND ($FieldName $CompareOperator '$FieldValue'";
					$ChkStatus=Get_Input('GET','DB','Chk4_2_2','INT',0,1,0,0);
					if($ChkStatus){
						$OptionButton=Get_Input('GET','DB','Opt4_'.$i,'STR',0,5,0,0);
						$FieldName=Get_Input('GET','DB','Field4_2_2','STR',0,100,0,0);					
						$CompareOperator=GetCompareOperator(Get_Input('GET','DB','Comp4_2_2','STR',0,10,0,0));
						$FieldValue=Get_Input('GET','DB','Value4_2_2','STR',0,500,0,0);
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

				$req=Get_Input('GET','DB','req','ARRAY',array("GetUserCount","PrepareBatchProcess","ShowInGrid"),0,0,0);
				
				DSDebug(0,"\n\n\n--------------------**************************************--------------------------------");
				DSDebug(0,"req=$req\nWhereStr=\"$WhereStr\"");
				DSDebug(0,"\n\n--------------------**************************************--------------------------------");

				$FilterFile=Get_InputIgnore('GET','DB','FilterFile','STR',16,16,0,0);
				$FilterFileQuery="";
				if($FilterFile!=''){
					if(($f=fopen("/tmp/dstemp/ds_bp_$FilterFile.tmp","r"))===false){
						DSDebug(0,"Cannot open '$ServerInputFile'");
						ExitError("فایل خواسته شده یافت نشد");
					}
					
					$OutStr="";
					while (($linedata = fgets($f,1000)) !== false){//loop for rows of a file
					DSDebug(5,"linedata=[$linedata]");
					$OutStr.=$linedata;
					}
					fclose($f);					
					$OutStr=trim(base64_decode($OutStr)," \n\r\0\x0B");;
					DSDebug(5,"Values=".$OutStr);
					if($OutStr!=""){
						DBUpdate("CREATE TEMPORARY TABLE UserFilter (Username varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',PRIMARY KEY (Username)) DEFAULT CHARACTER SET utf8 COLLATE utf8_persian_ci ");							
						$TotalDistinctDetected=DBUpdate("Insert into UserFilter(Username) values $OutStr");
						$FilterFileQuery="join UserFilter on Hu.Username=UserFilter.Username ";
					}
				}				
				
				if($req=="GetUserCount"){
					$sql="select count(1) from Huser Hu ".
						"join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
						((strpos($WhereStr,"Tuu.")!==false)?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_id ":"").
						$PreviousBatchJoinStr.
						$FilterFileQuery.
						"where $WhereStr";
					echo DBSelectAsString($sql);
				}
				elseif($req=="PrepareBatchProcess"){
					
					
					/*if($DebugLevel>0)
						ExitError("You cannot use debug during BatchProcess. First, run ds_nodebug from linux shell");*/

					
					$BatchItem=Get_Input('GET','DB','BatchItem','STR',0,50,0,0);
					
					if($BatchItem=="SendSMS"){
						if(extension_loaded("mbstring")==false)
							ExitError("<span style='float:left;padding-left: 90px'>نصب نشده php-mbstring ,</span><br/>را اجرا نمایید <span style='color:red'>yum install php-mbstring</span> روی سرور را اجرا نمایید <span style='color:red'>service httpd restart</span> و سپس");
						$NoProviderUsersCount=DBSelectAsString("
						Select count(1) from Huser_notifystate Huns ".
						"join Huser Hu on Huns.User_Id=Hu.User_Id ".
						"join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
						((strpos($WhereStr,"Tuu.")!==false)?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_id ":"").
						$PreviousBatchJoinStr.
						$FilterFileQuery.
						"where (Huns.SMSProvider_Id=0) and $WhereStr");
						if($NoProviderUsersCount>0)
							ExitError("برای کاربران فیلتر شده،ارائه دهنده پیام کوتاه تعریف نشده");
					}
					
					$sql="CREATE TEMPORARY TABLE BatchProcessTemp as ".
						"select Hu.User_Id from Huser Hu ".
						"join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
						((strpos($WhereStr,"Tuu.")!==false)?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_id ":"").
						$PreviousBatchJoinStr.
						$FilterFileQuery.
						"where $WhereStr";
					$res=DBUpdate($sql);					
					if($res==0)
						ExitError("کاربری انتخاب نشده");
					
					$BatchName=Get_Input('GET','DB','BatchName','STR',0,50,0,0);
					
					$sql="INSERT INTO Hbatchprocess(BatchProcessName,CDT,Creator_Id,SessionID,ClientIP,BatchItem) ".
						"select '$BatchName',NOW(),$LReseller_Id,'$SessionId',INET_ATON('$LClientIP'),'$BatchItem'";
					$BatchProcess_Id=DBInsert($sql);
					DSDebug(0,"sql=$sql\nQueryResult=$BatchProcess_Id");
					
					if(!$BatchProcess_Id)
						ExitError("نشست عملیات گروهی را نمی توان ایجاد کرد");
					
					
					DBUpdate("Lock Table Hbatchprocess_users write");//this guarantee all the records related to this batch process will be contiguous
					
					$sql="INSERT INTO Hbatchprocess_users(BatchProcess_Id,User_Id) ".
						"select $BatchProcess_Id,User_Id from BatchProcessTemp order by User_Id asc";
					$res=DBUpdate($sql);
					
					DBUpdate("UnLock Tables");
					
					$FromIndex=DBSelectAsString("SELECT MIN(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
					$ToIndex=DBSelectAsString("SELECT MAX(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
					
					if(($ToIndex-$FromIndex+1)!=$res)
						ExitError("خطای همزمان رخ داده");
					
					DSDebug(0,"\n**********************************************************************************\nsql=$sql\nQueryResult=$res\nMin=$FromIndex\nMax=$ToIndex");
					
					DBUpdate("update Hbatchprocess set From_User_Index=$FromIndex,To_User_Index=$ToIndex where BatchProcess_Id=$BatchProcess_Id");
					
					echo "OK~$BatchProcess_Id~$res";
				}				
				elseif($req=="ShowInGrid"){
					$ColumnStr="User_Id,Username,Name,Family,Reseller,Visp,Center,Supporter,StatusName,UserStatus,PortStatus,StartDate,EndDate,PayBalance,ServiceName";
					$SelectStr="Hu.User_Id,Hu.Username,Hu.Name,Hu.Family,Hr.ResellerName as Reseller,Hv.VispName as Visp,Hc.CenterName as Center".
					",Hsu.SupporterName as Supporter,Hst.StatusName as StatusName,Hst.UserStatus as UserStatus,Hst.PortStatus as PortStatus,shdatestr(Hu.Startdate) as StartDate,concat(shdatestr(Hu.EndDate),' ',ActiveTime) as EndDate,FORMAT(Hu.PayBalance,0) as PayBalance,Hse.ServiceName";
					$sql="select $SelectStr from Huser Hu ".
						"left join Hvisp Hv on Hu.Visp_Id=Hv.Visp_Id ".
						"left join Hreseller Hr on Hu.Reseller_Id=Hr.Reseller_Id ".
						"left join Hcenter Hc on Hu.Center_Id=Hc.Center_Id ".
						"left join Hstatus Hst on Hu.Status_Id=Hst.Status_Id ".
						"left join Hsupporter Hsu on Hu.Supporter_Id=Hsu.Supporter_Id ".
						"left join Hservice Hse on Hu.Service_id=Hse.Service_id ".
						((strpos($WhereStr,"Tuu.")!==false)?"join Tuser_usage Tuu on Hu.User_id=Tuu.User_Id ":"").
						$PreviousBatchJoinStr.
						$FilterFileQuery.
						"where $WhereStr $SortStr";
					DSDebug(0,"\n\n\n--------------------**************************************--------------------------------");
					DSDebug(0,"sql=$sql");
					DSDebug(0,"\n\n--------------------**************************************--------------------------------");						
					DSGridRender_Sql(100,$sql,"User_Id",$ColumnStr,"","","color_rows");
				}
				else
					echo "~Unknown Request";
       break;
    case "CancelBeforeStart":
				DSDebug(0,"DSBatchProcess_ListRender->CancelBeforeStart********************************************");
				$BatchProcess_Id=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);						
				$sql="UPDATE Hbatchprocess_users set BatchItemState='CanceledBeforeStart' where BatchProcess_Id=$BatchProcess_Id and BatchItemState='Pending'";
				$res1=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res1");
				$sql="UPDATE Hbatchprocess set BatchState='CanceledBeforeStart' where BatchProcess_Id=$BatchProcess_Id";
				$res2=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res2");				
				echo "OK~$res1";
	break;	   
    case "GetPreviousBatchProcessComment":
							$PrevBatchProcess=Get_Input('GET','DB','PrevBatchProcess','INT',0,4294967295,0,0);
							$sql="SELECT BatchComment From Hbatchprocess where BatchProcess_Id=$PrevBatchProcess limit 1";
							$res=DBSelectAsString($sql);
							DSDebug(0,"Select BatchProcess Comment\nsql=$sql\nQueryResult=$res");
							echo $res." ";
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
    case "SelectUserStatus":
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
                            $sql="SELECT IPPool_Id,IPPoolName From Hippool where IsFinishedIP='No' order by IPPoolName Asc";
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
	case "SelectBatchProcess":
                            require_once('../../lib/connector/options_connector.php');
                            $options = new SelectOptionsConnector($mysqli,"MySQLi");
                            $sql="SELECT BatchProcess_Id,BatchProcessName From Hbatchprocess where BatchState='Done' order by CDT,BatchProcessName Asc";
                            $options->render_sql($sql,"","BatchProcess_Id,BatchProcessName","","");
    break;
	case "TakingCare":
							echo "OK~".DBSelectAsString("select count(1) from Hbatchprocess")."~".DBSelectAsString("select count(1) from Hbatchprocess_users");
	break;
	case "UploadFile":
							DSDebug(0,"DSBatchProcess_ListRender.php->UploadFile********************************************");
							if(!is_dir("/tmp/dstemp"))
								mkdir("/tmp/dstemp");
							
							if ( @$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
									
								if(isset($_FILES["file"])){
									DSDebug(1,"Request File Upload :\n".DSPrintArray($_FILES));
									
									$filename =mysqli_real_escape_string($mysqli,$_FILES["file"]["name"]);
									
									$ServerFileName='_BP_'.GenerateRandomString(10).date("YmdHis");
									$FileFullPath="/tmp/dstemp/".$ServerFileName;
									
									DSDebug(1,' check file_exists '.$FileFullPath);
									if(move_uploaded_file($_FILES["file"]["tmp_name"],$FileFullPath)){
										
										$FileEncodingTmp=shell_exec("file -bi $FileFullPath");
										if(strpos($FileEncodingTmp,"text")===false){
											$tmp=explode(" ",$FileEncodingTmp);
											$Result="{state: false, extra:alert('Error.\\nYour file content is `".addslashes($tmp[0])."`.\\nYou can only upload text files.')}";
											unlink($FileFullPath);
										}
										elseif(strpos($FileEncodingTmp,"charset=utf-8")===false)
											$Result="{state: true, name:'".$ServerFileName."', extra:dhtmlx.message({text:'Upload OK.<br/><span style=\"color:limegreen;font-weight:bold\">Your file does not have UTF8 characters.</span>',expire:10000})}";
										else
											$Result="{state: true, name:'".$ServerFileName."', extra:dhtmlx.message({text:'Upload OK.<br/><span style=\"color:blue;font-weight:bold\">Your file encoding is UTF-8 and also has UTF8 characters.</span>',expire:10000})}";
									}
									else
										$Result="{state: false, extra:alert('Upload failed.\\nCheck file size limit.')}";
										
								}
								else 
									$Result="{state: false, extra:alert('Upload failed. Bad request.')}";
								
								DSDebug(0,"Upload Result=[$Result]");
								print_r($Result);
							}
	break;
	case "SubmitFiles":
							function DeleteFileAndExitError($ErrMsg){
								global $req;
								if($req!="CheckUsers"){
									$FileCount=Get_Input('POST','DB','FileUploader_count','INT',1,4294967295,0,0);
									for($i=0;$i<$FileCount;++$i){
										$FileName="/tmp/dstemp/".Get_Input('POST','DB','FileUploader_s_'.$i,'STR',1,200,0,0);
										if(file_exists($FileName)){
											unlink($FileName);
											DSDebug(1,$FileName." removed.");
										}
									}
								}
								ExitError($ErrMsg);
							}
							$FileCount=Get_Input('POST','DB','FileUploader_count','INT',1,4294967295,0,0);

							$UserArray=Array();
							for($i=0;$i<$FileCount;++$i){//loop for files
								
								$ServerInputFile="/tmp/dstemp/".Get_Input('POST','DB','FileUploader_s_'.$i,'STR',1,200,0,0);
								$RealInputFile=Get_Input('POST','DB','FileUploader_r_'.$i,'STR',1,200,0,0);
								
								$FileEncodingTmp=shell_exec("file -bi $ServerInputFile");
								DSDebug(0,"file -bi $ServerInputFile ='$FileEncodingTmp'");
								if(strpos($FileEncodingTmp,"charset=utf-8")===false)
									$ISUtf8=false;
								else
									$ISUtf8=true;
								
								if(($f=fopen($ServerInputFile,"r"))===false){
									DSDebug(0,"Cannot locate '$ServerInputFile'");
									DeleteFileAndExitError("Cannot locate file '$RealInputFile' on the server!");
								}
								DSDebug(0,"The file $ServerInputFile succesfully opened for read");								

								$FileRowNumber=1;

								while (($linedata = fgets($f,35)) !== false){//loop for rows of a file
									$linedata=trim(converttoutf8($linedata,$ISUtf8)," \n\r\0\x0B");
									if(strlen(utf8_decode($linedata))>32){
										DSDebug(0,"len($linedata)=".strlen(utf8_decode($linedata)));
										DeleteFileAndExitError("Extra value length at row '$FileRowNumber' in file '$RealInputFile'");
									}
									$linedata=DSescape($linedata);
									// DSDebug(3,"read data[$linedata]");
									$FileRowNumber++;
									array_push($UserArray,$linedata);
									if(count($UserArray)>10000)
										DeleteFileAndExitError("هربار بیش از ۱۰۰۰۰ کاربر را نمی توان توسط فایل فیلتر کرد");
									
								}//loop for rows of a file
								
								fclose($f);
								unlink($ServerInputFile);
							}
							
							DBUpdate("CREATE TEMPORARY TABLE UserFilter (Username varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',PRIMARY KEY (Username)) DEFAULT CHARACTER SET utf8 COLLATE utf8_persian_ci ");
							$TotalDetected=count($UserArray);
							if($TotalDetected==0)
								ExitError("در فایل،کاربری تشخیص داده نشد");
							$UserArray=array_unique($UserArray);
							$Values="('".implode("'),('",$UserArray)."')";
							$TotalDistinctDetected=DBUpdate("Insert ignore into UserFilter(Username) values $Values");
							$CommonUsers=DBSelectAsString("select count(1) from Huser u join UserFilter f on u.Username=f.Username");
							DBUpdate("Drop TEMPORARY table UserFilter");
							if($CommonUsers==0)
								ExitError("$TotalDistinctDetected unique user detected in file(s), but no one match existing users");
							$FilterFile_Name=GenerateRandomString(16);
							$TryToGenerate=0;
							while((file_exists("/tmp/dstemp/ds_bp_$FilterFile_Name.tmp"))and($TryToGenerate++<10))
								$FilterFile_Name=GenerateRandomString(16);
							if($TryToGenerate>10)
								ExitError("نام فایل خواسته شده را نمی توان ایجاد کرد");
							if(($f=fopen("/tmp/dstemp/ds_bp_$FilterFile_Name.tmp","w"))===false){
								DSDebug(0,"Cannot create '$ServerInputFile'");
								DeleteFileAndExitError("فایل خواسته شده را نمی توان ایجاد کرد");
							}
							$OutStr=base64_encode($Values);
							DSDebug(0,$OutStr);
							fwrite($f,$OutStr);
							fclose($f);
							DSDebug(1,"OK~$TotalDetected~$TotalDistinctDetected~$CommonUsers~$FilterFile_Name~");
							
							echo "OK~$TotalDetected~$TotalDistinctDetected~$CommonUsers~$FilterFile_Name~";
	break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
function converttoutf8($s,$ISUtf8){
if($ISUtf8)
	return mb_ereg_replace("ي","ی",$s);
$len=strlen($s);
$out='';
for($i=0;$i<$len;$i++){
	if(ord($s[$i])==32) $out.=' ';
	elseif(ord($s[$i])==63) $out.='ی';
	elseif(ord($s[$i])<=128) $out.=$s[$i];
	elseif(ord($s[$i])==129) $out.='پ';
	elseif(ord($s[$i])==141) $out.='چ';
	elseif(ord($s[$i])==142) $out.='ژ';
	elseif(ord($s[$i])==144) $out.='گ';
	elseif(ord($s[$i])==152) $out.='ک';
	elseif(ord($s[$i])==170) $out.='ه';
	elseif(ord($s[$i])==192) $out.='ه';
	elseif(ord($s[$i])==193) $out.='ء';
	elseif(ord($s[$i])==194) $out.='آ';
	elseif(ord($s[$i])==195) $out.='أ';
	elseif(ord($s[$i])==196) $out.='ؤ';
	elseif(ord($s[$i])==197) $out.='إ';
	elseif(ord($s[$i])==198) $out.='ئ';
	elseif(ord($s[$i])==199) $out.='ا';
	elseif(ord($s[$i])==200) $out.='ب';
	elseif(ord($s[$i])==201) $out.='ة';
	elseif(ord($s[$i])==202) $out.='ت';
	elseif(ord($s[$i])==203) $out.='ث';
	elseif(ord($s[$i])==204) $out.='ج';
	elseif(ord($s[$i])==205) $out.='ح';
	elseif(ord($s[$i])==206) $out.='خ';
	elseif(ord($s[$i])==207) $out.='د';
	elseif(ord($s[$i])==208) $out.='ذ';
	elseif(ord($s[$i])==209) $out.='ر';
	elseif(ord($s[$i])==210) $out.='ز';
	elseif(ord($s[$i])==211) $out.='س';
	elseif(ord($s[$i])==212) $out.='ش';
	elseif(ord($s[$i])==213) $out.='ص';
	elseif(ord($s[$i])==214) $out.='ض';
	elseif(ord($s[$i])==216) $out.='ط';
	elseif(ord($s[$i])==217) $out.='ظ';
	elseif(ord($s[$i])==218) $out.='ع';
	elseif(ord($s[$i])==219) $out.='غ';
	elseif(ord($s[$i])==220) $out.='ـ';
	elseif(ord($s[$i])==221) $out.='ف';
	elseif(ord($s[$i])==222) $out.='ق';
	elseif(ord($s[$i])==223) $out.='ک';
	elseif(ord($s[$i])==225) $out.='ل';
	elseif(ord($s[$i])==227) $out.='م';
	elseif(ord($s[$i])==228) $out.='ن';
	elseif(ord($s[$i])==229) $out.='ه';
	elseif(ord($s[$i])==230) $out.='و';
	elseif(ord($s[$i])==236) $out.='ی';
	elseif(ord($s[$i])==237) $out.='ی';
	else $out.=$s[$i];
}
return $out;
}
?>
