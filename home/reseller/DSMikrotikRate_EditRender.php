<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSMikrotikRateEditRender1 ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array('SelectMikrotikRateValue','SelectParentMikrotikRateValue',"load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSMikrotikRateEditRender Load ********************************************");
				
				exitifnotpermit(0,"Admin.User.MikrotikRate.View");
				$MikrotikRate_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,MikrotikRate_Id,MikrotikRateName,Parent_MikrotikRateValue_Id,".
					"dslistvalue(1,MikrotikRate) As H1_Id, ".
					"dslistvalue(2,MikrotikRate) As H2_Id, ".
					"dslistvalue(3,MikrotikRate) As H3_Id, ".
					"dslistvalue(4,MikrotikRate) As H4_Id, ".
					"dslistvalue(5,MikrotikRate) As H5_Id, ".
					"dslistvalue(6,MikrotikRate) As H6_Id, ".
					"dslistvalue(7,MikrotikRate) As H7_Id, ".
					"dslistvalue(8,MikrotikRate) As H8_Id, ".
					"dslistvalue(9,MikrotikRate) As H9_Id, ".
					"dslistvalue(10,MikrotikRate) As H10_Id, ".
					"dslistvalue(11,MikrotikRate) As H11_Id, ".
					"dslistvalue(12,MikrotikRate) As H12_Id, ".
					"dslistvalue(13,MikrotikRate) As H13_Id, ".
					"dslistvalue(14,MikrotikRate) As H14_Id, ".
					"dslistvalue(15,MikrotikRate) As H15_Id, ".
					"dslistvalue(16,MikrotikRate) As H16_Id, ".
					"dslistvalue(17,MikrotikRate) As H17_Id, ".
					"dslistvalue(18,MikrotikRate) As H18_Id, ".
					"dslistvalue(19,MikrotikRate) As H19_Id, ".
					"dslistvalue(20,MikrotikRate) As H20_Id, ".
					"dslistvalue(21,MikrotikRate) As H21_Id, ".
					"dslistvalue(22,MikrotikRate) As H22_Id, ".
					"dslistvalue(23,MikrotikRate) As H23_Id, ".
					"dslistvalue(24,MikrotikRate) As H24_Id ".
				"From Hmikrotikrate where MikrotikRate_Id='$MikrotikRate_Id'";
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
				DSDebug(1,"DSMikrotikRateEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRate.Add");
				$NewRowInfo=array();
				$sql="Insert Hmikrotikrate Set ";
				$NewRowInfo['MikrotikRateName']=Get_Input('POST','DB','MikrotikRateName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo["Parent_MikrotikRateValue_Id"]=Get_Input('POST','DB','Parent_MikrotikRateValue_Id','INT',0,4294967295,0,0);
				
				$sql.="MikrotikRateName='".$NewRowInfo['MikrotikRateName']."',";
				$sql.="Parent_MikrotikRateValue_Id='".$NewRowInfo['Parent_MikrotikRateValue_Id']."',";
				
				$temp='';
				for ($i=1; $i<=24; $i++) {
					$NewRowInfo["MikrotikRate"]=Get_Input('POST','DB','H'.$i.'_Id','INT',1,4294967295,0,0);
					$temp.=$NewRowInfo["MikrotikRate"];
					if($i<24) $temp.=',';
				} 
				$sql.="MikrotikRate='$temp'";

				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','MikrotikRate',$RowId,'');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"-> Update ******************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRate.Edit");
				$NewRowInfo=array();
				$NewRowInfo["MikrotikRate_Id"].=Get_Input('POST','DB','MikrotikRate_Id','INT',1,4294967295,0,0);
				//$NewRowInfo['MikrotikRateName']=Get_Input('POST','DB','MikrotikRateName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo["Parent_MikrotikRateValue_Id"]=Get_Input('POST','DB','Parent_MikrotikRateValue_Id','INT',0,4294967295,0,0);
				$sql="Update Hmikrotikrate Set ";
				$sql.="Parent_MikrotikRateValue_Id='".$NewRowInfo['Parent_MikrotikRateValue_Id']."',";

				$temp='';
				for ($i=1; $i<=24; $i++) {
					$NewRowInfo["H{$i}_Id"]=Get_Input('POST','DB',"H{$i}_Id",'INT',1,4294967295,0,0);
					$temp.=$NewRowInfo["H{$i}_Id"];
					if($i<24) $temp.=',';
				} 
				$sql.="MikrotikRate='$temp'";
				$sql.=" Where ";
				$sql.="(MikrotikRate_Id='".$NewRowInfo['MikrotikRate_Id']."')";

				$OldRowInfo= LoadRowInfo("Hmikrotikrate","MikrotikRate_Id='".$NewRowInfo['MikrotikRate_Id']."'");
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();

				if($ar!=1){//probably hack
					logdb('Edit','MikrotikRate',$NewRowInfo['MikrotikRate_Id'],'MikrotikRate',"Update Fail,Table=MikrotikRate affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=MikrotikRate affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'MikrotikRate',$NewRowInfo['MikrotikRate_Id'],'MikrotikRate')){
					logunfair("UnFair",'MikrotikRate',$NewRowInfo['MikrotikRate_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	case "SelectMikrotikRateValue":
				DSDebug(1,"-> SelectMikrotikRateValue");
				
				$sql="update Tonline_web_ipblock set ".
					"LastSecondRequest=LastSecondRequest-1 ".
					"where ClientIP=INET_ATON('$LClientIP')";
				DBUpdate($sql);//I was forced... Because 25 limitation of request in second cause block				
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("Select '' As MikrotikRateValue_Id,' --لطفا انتخاب کنید-- ' As MikrotikRateValueName ".
									"union SELECT MikrotikRateValue_Id,MikrotikRateValueName FROM Hmikrotikratevalue order by MikrotikRateValue_Id ASC",
									"","MikrotikRateValue_Id,MikrotikRateValueName","","");
        break;
	case "SelectParentMikrotikRateValue":
				DSDebug(1,"-> SelectMikrotikRateValue");
				
				$sql="update Tonline_web_ipblock set ".
					"LastSecondRequest=LastSecondRequest-1 ".
					"where ClientIP=INET_ATON('$LClientIP')";
				DBUpdate($sql);//I was forced... Because 25 limitation of request in second cause block				
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("Select '0' As MikrotikRateValue_Id,'هیچی' As MikrotikRateValueName ".
									"union SELECT MikrotikRateValue_Id,MikrotikRateValueName FROM Hmikrotikratevalue order by MikrotikRateValue_Id ASC",
									"","MikrotikRateValue_Id,MikrotikRateValueName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
//ExitError('dddddd');

}
//--------------------------------

?>
