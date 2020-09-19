<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSService_Gift_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","insert","LoadGiftForm","update","Delete","SelectGift","GetGiftInfo"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSService_Gift_ListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Service.Gift.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";

				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				DSGridRender_Sql(100,
					"SELECT Service_Gift_Id,GiftName,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,ByteToR(GiftExtraTr) As GiftExtraTr,SecondToR(GiftExtraTi) as GiftExtraTi,MikrotikRateName from ".
					"Hservice_gift s left join Hgift g on s.Gift_Id=g.Gift_Id left join Hmikrotikrate m on g.GiftMikrotikRate_Id=m.MikrotikRate_Id ".
					"Where (Service_Id=$Service_Id)" .$sqlfilter." $SortStr ",
					"Service_Gift_Id",
					"Service_Gift_Id,GiftName,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftExtraTi,MikrotikRateName",
					"","","");
       break;
    case "LoadGiftForm":
				DSDebug(1,"DSService_Gift_ListRender Load ********************************************");
				exitifnotpermit(0,"CRM.Service.Gift.List");
				$Service_Gift_Id=Get_Input('GET','DB','Service_Gift_Id','INT',1,4294967295,0,0);
				$sql="SELECT Service_Gift_Id,s.Gift_Id,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,ByteToR(GiftExtraTr) As GiftExtraTr,GiftStopOnTrFinish,SecondToR(GiftExtraTi) as GiftExtraTi,if(MikrotikRateName is null,' -- Do not change MikrotikRate -- ',MikrotikRateName) as MikrotikRateName from Hservice_gift s left join Hgift g on s.Gift_Id=g.Gift_Id left join Hmikrotikrate m on g.GiftMikrotikRate_Id=m.MikrotikRate_Id ".
					"where (Service_Gift_Id=$Service_Gift_Id)";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				
				echo '</data>';
				
       break;
    case "insert": 
				DSDebug(1,"DSService_Gift_ListRender Insert ******************************************");
				exitifnotpermit(0,"CRM.Service.Gift.Add");
				$NewRowInfo=array();
				$NewRowInfo['Service_Id']=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Gift_Id']=Get_Input('POST','DB','Gift_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("select Gift_Id from Hgift where Gift_Id='".$NewRowInfo['Gift_Id']."'")<=0)
					ExitError("هدیه نامعتبر انتخاب شده");				
				
				$GiftCount=Get_Input('POST','DB','GiftCount','INT',1,999,0,0);

				
				for($i=0;$i<$GiftCount;++$i){
					$sql= "insert Hservice_gift set ";
					$sql.="Service_Id='".$NewRowInfo['Service_Id']."',";
					$sql.="Gift_Id='".$NewRowInfo['Gift_Id']."'";
					$res = $conn->sql->query($sql);
					$RowId=$conn->sql->get_new_id();
					$NewRowInfo['Service_Gift_Id']=$RowId;
					logdbinsert($NewRowInfo,'Add','Service',$NewRowInfo['Service_Id'],'Gift');
				}
				echo "OK~$RowId~";
        break;
    case "update": 
				DSDebug(1,"DSService_Gift_ListRender Update ******************************************");
				exitifnotpermit(0,"CRM.Service.Gift.Edit");
				$NewRowInfo=array();
				$Service_Gift_Id=Get_Input('POST','DB','Service_Gift_Id','INT',1,4294967295,0,0);
				$Service_Id=Get_Input('GET','DB','Service_Id','INT',0,4294967295,0,0);
				
				$NewRowInfo['Gift_Id']=Get_Input('POST','DB','Gift_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("select Gift_Id from Hgift where Gift_Id='".$NewRowInfo['Gift_Id']."'")<=0)
					ExitError("هدیه نامعتبر انتخاب شده");
				
				

				$OldRowInfo= LoadRowInfoSql("SELECT Gift_Id from Hservice_gift where (Service_Gift_Id=$Service_Gift_Id)");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "update Hservice_gift set ";
				$sql.="Gift_Id='".$NewRowInfo['Gift_Id']."'";
				$sql.=" Where ";
				$sql.="(Service_Gift_Id=$Service_Gift_Id)";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				/*
				if($ar!=1){//probably hack
					logdb('Edit','Service',$NewRowInfo['Service_Id'],'Param',"Update Fail,Table=Param affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Param affected row=0");
					ExitError("(ar=$ar) Security problem, Report Sent to Administrator");	
				}
					*/
					
				if($NewRowInfo['Gift_Id']==$OldRowInfo['Gift_Id']){
					logunfair("UnFair",'Service',$Service_Id,'Gift','');
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else{
					logdb("Edit","Service",$Service_Id,"Gift","Gift_Id=[".$OldRowInfo['Gift_Id']."]>[".$NewRowInfo['Gift_Id']."] Service_Gift_Id=$Service_Gift_Id");
					echo "OK~";
				}
        break;
		
	case "Delete":
				DSDebug(1,"DSService_Gift_ListRender Delete ******************************************");
				exitifnotpermit(0,"CRM.Service.Gift.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Service_Gift_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$NewRowInfo['Gift_Id']=DBSelectAsString("Select Gift_Id from Hservice_gift where Service_Gift_Id=".$NewRowInfo['Service_Gift_Id']);
				$NewRowInfo['GiftName']=DBSelectAsString("Select GiftName from Hgift where Gift_Id=".$NewRowInfo['Gift_Id']);
				$Service_Id=DBSelectAsString("Select Service_Id from Hservice_gift where Service_Gift_Id=".$NewRowInfo['Service_Gift_Id']);
				$ar=DBDelete('delete from Hservice_gift Where Service_Gift_Id='.$NewRowInfo['Service_Gift_Id']);
				logdbdelete($NewRowInfo,'Delete','Service',$Service_Id,'Gift');
				echo "OK~";
		break;
	case "SelectGift":
				DSDebug(1,"DSService_Gift_ListRender -> SelectGift");	
				exitifnotpermit(0,"CRM.Service.Gift.List");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("Select 0 As Gift_Id,'-- از لیست انتخاب کنید --' As GiftName union SELECT Gift_Id,GiftName FROM Hgift where GiftIsDel='No' and GiftISEnable='Yes' order by GiftName ASC","","Gift_Id,GiftName","","");
		break;
	case "GetGiftInfo":
				DSDebug(1,"DSService_Gift_ListRender-> GetGiftInfo *****************");
				exitifnotpermit(0,"CRM.Service.Gift.List");
				$Gift_Id=Get_Input('GET','DB','Gift_Id','INT',1,4294967295,0,0);
				$TempArray=Array();
				CopyTableToArray($TempArray,"Select GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,ByteToR(GiftExtraTr) As GiftExtraTr,GiftStopOnTrFinish,SecondToR(GiftExtraTi) as GiftExtraTi,if(MikrotikRateName is null,' -- Do not change MikrotikRate -- ',MikrotikRateName) as MikrotikRateName From Hgift g left join Hmikrotikrate m on g.GiftMikrotikRate_Id=m.MikrotikRate_Id where Gift_Id=$Gift_Id");
				$GiftDurationDays=$TempArray[0]["GiftDurationDays"];
				$GiftExpirationDays=$TempArray[0]["GiftExpirationDays"];
				$GiftTrafficRate=$TempArray[0]["GiftTrafficRate"];
				$GiftTimeRate=$TempArray[0]["GiftTimeRate"];
				$GiftExtraTr=$TempArray[0]["GiftExtraTr"];
				$GiftStopOnTrFinish=$TempArray[0]["GiftStopOnTrFinish"];
				$GiftExtraTi=$TempArray[0]["GiftExtraTi"];
				$MikrotikRateName=$TempArray[0]["MikrotikRateName"];
				echo "$GiftDurationDays`$GiftExpirationDays`$GiftTrafficRate`$GiftTimeRate`$GiftExtraTr`$GiftStopOnTrFinish`$GiftExtraTi`$MikrotikRateName";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
