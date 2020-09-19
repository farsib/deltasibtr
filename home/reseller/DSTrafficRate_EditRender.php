<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSTrafficRateEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSTrafficRateEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.TrafficRate.View");
				$TrafficRate_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,TrafficRate_Id,TrafficRateName,".
					"dslistvalue(1,TrafficRateValue) As H0,dslistvalue(2,TrafficRateValue) As H1,dslistvalue(3,TrafficRateValue) As H2,dslistvalue(4,TrafficRateValue) As H3,".
					"dslistvalue(5,TrafficRateValue) As H4,dslistvalue(6,TrafficRateValue) As H5,dslistvalue(7,TrafficRateValue) As H6,dslistvalue(8,TrafficRateValue) As H7,".
					"dslistvalue(9,TrafficRateValue) As H8,dslistvalue(10,TrafficRateValue) As H9,dslistvalue(11,TrafficRateValue) As H10,dslistvalue(12,TrafficRateValue) As H11,".
					"dslistvalue(13,TrafficRateValue) As H12,dslistvalue(14,TrafficRateValue) As H13,dslistvalue(15,TrafficRateValue) As H14,dslistvalue(16,TrafficRateValue) As H15,".
					"dslistvalue(17,TrafficRateValue) As H16,dslistvalue(18,TrafficRateValue) As H17,dslistvalue(19,TrafficRateValue) As H18,dslistvalue(20,TrafficRateValue) As H19,".
					"dslistvalue(21,TrafficRateValue) As H20,dslistvalue(22,TrafficRateValue) As H21,dslistvalue(23,TrafficRateValue) As H22,dslistvalue(24,TrafficRateValue) As H23 ".
				"From Htrafficrate where TrafficRate_Id='$TrafficRate_Id'";
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
				DSDebug(1,"DSTrafficRateEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.TrafficRate.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['TrafficRateName']=Get_Input('POST','DB','TrafficRateName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['H0']=Get_Input('POST','DB','H0','FLT',0,2.50,0,0);
				$NewRowInfo['H1']=Get_Input('POST','DB','H1','FLT',0,2.50,0,0);
				$NewRowInfo['H2']=Get_Input('POST','DB','H2','FLT',0,2.50,0,0);
				$NewRowInfo['H3']=Get_Input('POST','DB','H3','FLT',0,2.50,0,0);
				$NewRowInfo['H4']=Get_Input('POST','DB','H4','FLT',0,2.50,0,0);
				$NewRowInfo['H5']=Get_Input('POST','DB','H5','FLT',0,2.50,0,0);
				$NewRowInfo['H6']=Get_Input('POST','DB','H6','FLT',0,2.50,0,0);
				$NewRowInfo['H7']=Get_Input('POST','DB','H7','FLT',0,2.50,0,0);
				$NewRowInfo['H8']=Get_Input('POST','DB','H8','FLT',0,2.50,0,0);
				$NewRowInfo['H9']=Get_Input('POST','DB','H9','FLT',0,2.50,0,0);
				$NewRowInfo['H10']=Get_Input('POST','DB','H10','FLT',0,2.50,0,0);
				$NewRowInfo['H11']=Get_Input('POST','DB','H11','FLT',0,2.50,0,0);
				$NewRowInfo['H12']=Get_Input('POST','DB','H12','FLT',0,2.50,0,0);
				$NewRowInfo['H13']=Get_Input('POST','DB','H13','FLT',0,2.50,0,0);
				$NewRowInfo['H14']=Get_Input('POST','DB','H14','FLT',0,2.50,0,0);
				$NewRowInfo['H15']=Get_Input('POST','DB','H15','FLT',0,2.50,0,0);
				$NewRowInfo['H16']=Get_Input('POST','DB','H16','FLT',0,2.50,0,0);
				$NewRowInfo['H17']=Get_Input('POST','DB','H17','FLT',0,2.50,0,0);
				$NewRowInfo['H18']=Get_Input('POST','DB','H18','FLT',0,2.50,0,0);
				$NewRowInfo['H19']=Get_Input('POST','DB','H19','FLT',0,2.50,0,0);
				$NewRowInfo['H20']=Get_Input('POST','DB','H20','FLT',0,2.50,0,0);
				$NewRowInfo['H21']=Get_Input('POST','DB','H21','FLT',0,2.50,0,0);
				$NewRowInfo['H22']=Get_Input('POST','DB','H22','FLT',0,2.50,0,0);
				$NewRowInfo['H23']=Get_Input('POST','DB','H23','FLT',0,2.50,0,0);
				//----------------------
				$sql= "insert Htrafficrate set ";
				$sql.="TrafficRateName='".$NewRowInfo['TrafficRateName']."',";
				$sql.="TrafficRateValue=Concat(";
				$sql.=$NewRowInfo['H0'].",',',";
				$sql.=$NewRowInfo['H1'].",',',";
				$sql.=$NewRowInfo['H2'].",',',";
				$sql.=$NewRowInfo['H3'].",',',";
				$sql.=$NewRowInfo['H4'].",',',";
				$sql.=$NewRowInfo['H5'].",',',";
				$sql.=$NewRowInfo['H6'].",',',";
				$sql.=$NewRowInfo['H7'].",',',";
				$sql.=$NewRowInfo['H8'].",',',";
				$sql.=$NewRowInfo['H9'].",',',";
				$sql.=$NewRowInfo['H10'].",',',";
				$sql.=$NewRowInfo['H11'].",',',";
				$sql.=$NewRowInfo['H12'].",',',";
				$sql.=$NewRowInfo['H13'].",',',";
				$sql.=$NewRowInfo['H14'].",',',";
				$sql.=$NewRowInfo['H15'].",',',";
				$sql.=$NewRowInfo['H16'].",',',";
				$sql.=$NewRowInfo['H17'].",',',";
				$sql.=$NewRowInfo['H18'].",',',";
				$sql.=$NewRowInfo['H19'].",',',";
				$sql.=$NewRowInfo['H20'].",',',";
				$sql.=$NewRowInfo['H21'].",',',";
				$sql.=$NewRowInfo['H22'].",',',";
				$sql.=$NewRowInfo['H23'].")";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','TrafficRate',$RowId,'TrafficRate');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSTrafficRateEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.TrafficRate.Edit");
				$NewRowInfo=array();
				$NewRowInfo['TrafficRate_Id']=Get_Input('POST','DB','TrafficRate_Id','INT',1,4294967295,0,0);
				$NewRowInfo['TrafficRateName']=Get_Input('POST','DB','TrafficRateName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['H0']=Get_Input('POST','DB','H0','FLT',0,2.50,0,0);
				$NewRowInfo['H1']=Get_Input('POST','DB','H1','FLT',0,2.50,0,0);
				$NewRowInfo['H2']=Get_Input('POST','DB','H2','FLT',0,2.50,0,0);
				$NewRowInfo['H3']=Get_Input('POST','DB','H3','FLT',0,2.50,0,0);
				$NewRowInfo['H4']=Get_Input('POST','DB','H4','FLT',0,2.50,0,0);
				$NewRowInfo['H5']=Get_Input('POST','DB','H5','FLT',0,2.50,0,0);
				$NewRowInfo['H6']=Get_Input('POST','DB','H6','FLT',0,2.50,0,0);
				$NewRowInfo['H7']=Get_Input('POST','DB','H7','FLT',0,2.50,0,0);
				$NewRowInfo['H8']=Get_Input('POST','DB','H8','FLT',0,2.50,0,0);
				$NewRowInfo['H9']=Get_Input('POST','DB','H9','FLT',0,2.50,0,0);
				$NewRowInfo['H10']=Get_Input('POST','DB','H10','FLT',0,2.50,0,0);
				$NewRowInfo['H11']=Get_Input('POST','DB','H11','FLT',0,2.50,0,0);
				$NewRowInfo['H12']=Get_Input('POST','DB','H12','FLT',0,2.50,0,0);
				$NewRowInfo['H13']=Get_Input('POST','DB','H13','FLT',0,2.50,0,0);
				$NewRowInfo['H14']=Get_Input('POST','DB','H14','FLT',0,2.50,0,0);
				$NewRowInfo['H15']=Get_Input('POST','DB','H15','FLT',0,2.50,0,0);
				$NewRowInfo['H16']=Get_Input('POST','DB','H16','FLT',0,2.50,0,0);
				$NewRowInfo['H17']=Get_Input('POST','DB','H17','FLT',0,2.50,0,0);
				$NewRowInfo['H18']=Get_Input('POST','DB','H18','FLT',0,2.50,0,0);
				$NewRowInfo['H19']=Get_Input('POST','DB','H19','FLT',0,2.50,0,0);
				$NewRowInfo['H20']=Get_Input('POST','DB','H20','FLT',0,2.50,0,0);
				$NewRowInfo['H21']=Get_Input('POST','DB','H21','FLT',0,2.50,0,0);
				$NewRowInfo['H22']=Get_Input('POST','DB','H22','FLT',0,2.50,0,0);
				$NewRowInfo['H23']=Get_Input('POST','DB','H23','FLT',0,2.50,0,0);


				$OldRowInfo= LoadRowInfo("Htrafficrate","TrafficRate_Id='".$NewRowInfo['TrafficRate_Id']."'");
				
				//----------------------
				
				$sql= "Update Htrafficrate set ";
				//$sql.="TrafficRateName='".$NewRowInfo['TrafficRateName']."',";
				$sql.="TrafficRateValue=Concat(";
				$sql.=$NewRowInfo['H0'].",',',";
				$sql.=$NewRowInfo['H1'].",',',";
				$sql.=$NewRowInfo['H2'].",',',";
				$sql.=$NewRowInfo['H3'].",',',";
				$sql.=$NewRowInfo['H4'].",',',";
				$sql.=$NewRowInfo['H5'].",',',";
				$sql.=$NewRowInfo['H6'].",',',";
				$sql.=$NewRowInfo['H7'].",',',";
				$sql.=$NewRowInfo['H8'].",',',";
				$sql.=$NewRowInfo['H9'].",',',";
				$sql.=$NewRowInfo['H10'].",',',";
				$sql.=$NewRowInfo['H11'].",',',";
				$sql.=$NewRowInfo['H12'].",',',";
				$sql.=$NewRowInfo['H13'].",',',";
				$sql.=$NewRowInfo['H14'].",',',";
				$sql.=$NewRowInfo['H15'].",',',";
				$sql.=$NewRowInfo['H16'].",',',";
				$sql.=$NewRowInfo['H17'].",',',";
				$sql.=$NewRowInfo['H18'].",',',";
				$sql.=$NewRowInfo['H19'].",',',";
				$sql.=$NewRowInfo['H20'].",',',";
				$sql.=$NewRowInfo['H21'].",',',";
				$sql.=$NewRowInfo['H22'].",',',";
				$sql.=$NewRowInfo['H23'].")";
				$sql.=" Where ";
				$sql.="(TrafficRate_Id='".$NewRowInfo['TrafficRate_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','TrafficRate',$NewRowInfo['TrafficRate_Id'],'TrafficRate',"Update Fail,Table=TrafficRate affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=TrafficRate affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'TrafficRate',$NewRowInfo['TrafficRate_Id'],'TrafficRate')){
					logunfair("UnFair",'TrafficRate',$NewRowInfo['TrafficRate_Id'],'',"");
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
