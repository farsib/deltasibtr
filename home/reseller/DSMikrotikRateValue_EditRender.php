<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSMikrotikRateValue_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSMikrotikRateValue_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRateValue.View");
				$MikrotikRateValue_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,MikrotikRateValue_Id,MikrotikRateValueName,MikrotikRateValueText from Hmikrotikratevalue where MikrotikRateValue_Id='$MikrotikRateValue_Id'";
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

				DSDebug(1,"DSMikrotikRateValue_EditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.MikrotikRateValue.Add");
				$NewRowInfo=array();
				$NewRowInfo['MikrotikRateValueName']=Get_Input('POST','DB','MikrotikRateValueName','STR',1,32,0,0);
				$NewRowInfo['MikrotikRateValueText']=Get_Input('POST','DB','MikrotikRateValueText','STR',1,64,0,0);
				//----------------------
				$sql= "insert Hmikrotikratevalue set ";
				$sql.="MikrotikRateValueName='".$NewRowInfo['MikrotikRateValueName']."',";
				$sql.="MikrotikRateValueText=Upper('".$NewRowInfo['MikrotikRateValueText']."')";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['MikrotikRateValue_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','MikrotikRateValue',$RowId,'MikrotikRateValue');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSMikrotikRateValue_EditRender Update ******************************************");
				
				exitifnotpermit(0,"Admin.User.MikrotikRateValue.Edit");
				$NewRowInfo=array();

				$NewRowInfo['MikrotikRateValue_Id']=Get_Input('POST','DB','MikrotikRateValue_Id','INT',1,4294967295,0,0);
				$NewRowInfo['MikrotikRateValueName']=Get_Input('POST','DB','MikrotikRateValueName','STR',1,32,0,0);
				$NewRowInfo['MikrotikRateValueText']=Get_Input('POST','DB','MikrotikRateValueText','STR',1,64,0,0);

				$OldRowInfo= LoadRowInfo("Hmikrotikratevalue","MikrotikRateValue_Id='".$NewRowInfo['MikrotikRateValue_Id']."'");
				
				//----------------------
				$sql= "update Hmikrotikratevalue set ";
				$sql.="MikrotikRateValueName='".$NewRowInfo['MikrotikRateValueName']."',";
				$sql.="MikrotikRateValueText=Upper('".$NewRowInfo['MikrotikRateValueText']."')";
				$sql.=" Where ";
				$sql.="(MikrotikRateValue_Id='".$NewRowInfo['MikrotikRateValue_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','MikrotikRateValue',$NewRowInfo['MikrotikRateValue_Id'],'MikrotikRateValue',"Update Fail,Table=MikrotikRateValue_ affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=MikrotikRateValue_ affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'MikrotikRateValue',$NewRowInfo['MikrotikRateValue_Id'],'MikrotikRateValue')){
					logunfair("UnFair",'MikrotikRateValue',$NewRowInfo['MikrotikRateValue_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
