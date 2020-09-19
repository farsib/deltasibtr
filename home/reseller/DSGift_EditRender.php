<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSGift_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update","SelectMikrotikRate"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSGift_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.Gift.View");
				$Gift_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Gift_Id,GiftName,GiftISEnable,GiftStopOnTrFinish,GiftMode,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,round(GiftExtraTr/1048576) As GiftExtraTr,GiftExtraTi,GiftMikrotikRate_Id from Hgift where Gift_Id='$Gift_Id'";
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
				DSDebug(1,"DSGift_EditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.Gift.Add");
				
				$NewRowInfo=array();
				$NewRowInfo['GiftName']=Get_Input('POST','DB','GiftName','STR',1,64,0,0);
				$NewRowInfo['GiftISEnable']=Get_Input('POST','DB','GiftISEnable','ARRAY',array('Yes','No'),0,0,0);
				$NewRowInfo['GiftMode']=Get_Input('POST','DB','GiftMode','ARRAY',array('MultiDay','FixedDay'),0,0,0);
				if($NewRowInfo['GiftMode']=='MultiDay')
					$NewRowInfo['GiftDurationDays']=Get_Input('POST','DB','GiftDurationDays','INT',1,9999,0,0);
				else
					$NewRowInfo['GiftDurationDays']=1;
				
				$NewRowInfo['GiftExpirationDays']=Get_Input('POST','DB','GiftExpirationDays','INT',0,9999,0,0);
				$NewRowInfo['GiftTrafficRate']=Get_Input('POST','DB','GiftTrafficRate','FLT',0,1,0,0);
				$NewRowInfo['GiftTimeRate']=Get_Input('POST','DB','GiftTimeRate','FLT',0,1,0,0);
				$NewRowInfo['GiftExtraTr']=1048576*Get_Input('POST','DB','GiftExtraTr','INT',0,999999,0,0);
				if($NewRowInfo['GiftExtraTr']>0)
					$NewRowInfo['GiftStopOnTrFinish']=Get_Input('POST','DB','GiftStopOnTrFinish','ARRAY',array('Yes','No'),0,0,0);
				else
					$NewRowInfo['GiftStopOnTrFinish']="No";
				
				
				$NewRowInfo['GiftExtraTi']=Get_Input('POST','DB','GiftExtraTi','INT',0,999999,0,0);
				$NewRowInfo['GiftMikrotikRate_Id']=Get_Input('POST','DB','GiftMikrotikRate_Id','INT',0,4294967295,0,0);

				
				$sql= "insert Hgift set ";
				$sql.="GiftName='".$NewRowInfo['GiftName']."',";
				$sql.="GiftISEnable='".$NewRowInfo['GiftISEnable']."',";
				$sql.="GiftMode='".$NewRowInfo['GiftMode']."',";
				$sql.="GiftDurationDays='".$NewRowInfo['GiftDurationDays']."',";
				$sql.="GiftExpirationDays='".$NewRowInfo['GiftExpirationDays']."',";
				$sql.="GiftTrafficRate='".$NewRowInfo['GiftTrafficRate']."',";
				$sql.="GiftTimeRate='".$NewRowInfo['GiftTimeRate']."',";
				$sql.="GiftExtraTr='".$NewRowInfo['GiftExtraTr']."',";
				$sql.="GiftStopOnTrFinish='".$NewRowInfo['GiftStopOnTrFinish']."',";
				$sql.="GiftExtraTi='".$NewRowInfo['GiftExtraTi']."',";
				$sql.="GiftMikrotikRate_Id='".$NewRowInfo['GiftMikrotikRate_Id']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Gift_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','Gift',$NewRowInfo['Gift_Id'],'Gift');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSGift_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.Gift.Edit");
				$NewRowInfo=array();
				$Gift_Id=Get_Input('POST','DB','Gift_Id','INT',1,4294967295,0,0);
				$NewRowInfo['GiftName']=Get_Input('POST','DB','GiftName','STR',1,64,0,0);
				$NewRowInfo['GiftISEnable']=Get_Input('POST','DB','GiftISEnable','ARRAY',array('Yes','No'),0,0,0);
				$NewRowInfo['GiftMode']=Get_Input('POST','DB','GiftMode','ARRAY',array('MultiDay','FixedDay'),0,0,0);
				if(($NewRowInfo['GiftMode']=='FixedDay')&&(DBSelectAsString("select GiftDurationDays from Hgift where (Gift_Id='$Gift_Id')")>1))
					ExitError("نمی توان حالت هدیه را به روز ثابت تغییر داد در حالی که مدت زمان هدیه بزرگتر از ۱ است");

				$NewRowInfo['GiftMikrotikRate_Id']=Get_Input('POST','DB','GiftMikrotikRate_Id','INT',0,4294967295,0,0);
				
				$OldRowInfo= LoadRowInfoSql("SELECT GiftName,GiftISEnable,GiftMode from Hgift where (Gift_Id='$Gift_Id')");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));
				
				$sql= "update Hgift set ";
				$sql.="GiftName='".$NewRowInfo['GiftName']."',";
				$sql.="GiftISEnable='".$NewRowInfo['GiftISEnable']."',";
				$sql.="GiftMode='".$NewRowInfo['GiftMode']."',";
				$sql.="GiftMikrotikRate_Id='".$NewRowInfo['GiftMikrotikRate_Id']."'";
				$sql.=" Where GiftIsDel='No' and (Gift_Id='$Gift_Id')";//Deleted gift cannot update
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();

				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Gift',$Gift_Id,'Gift')){
					logunfair("UnFair",'Gift',$Gift_Id,'Gift','');
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
    case "SelectMikrotikRate":
				DSDebug(1,"DSGift_EditRender SelectMikrotikRate *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="select 0 as GiftMikrotikRate_Id,' -- همان سرعت سرویس پایه -- ' as MikrotikRateName union ".
					"SELECT MikrotikRate_Id as GiftMikrotikRate_Id,MikrotikRateName FROM Hmikrotikrate order by MikrotikRateName ASC";
				$options->render_sql($sql,"","GiftMikrotikRate_Id,MikrotikRateName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
