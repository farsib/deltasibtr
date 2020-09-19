<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSTimeRateEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSTimeRateEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.TimeRate.View");
				$TimeRate_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,TimeRate_Id,TimeRateName,".
					"dslistvalue(1,TimeRateValue) As H0,dslistvalue(2,TimeRateValue) As H1,dslistvalue(3,TimeRateValue) As H2,dslistvalue(4,TimeRateValue) As H3,".
					"dslistvalue(5,TimeRateValue) As H4,dslistvalue(6,TimeRateValue) As H5,dslistvalue(7,TimeRateValue) As H6,dslistvalue(8,TimeRateValue) As H7,".
					"dslistvalue(9,TimeRateValue) As H8,dslistvalue(10,TimeRateValue) As H9,dslistvalue(11,TimeRateValue) As H10,dslistvalue(12,TimeRateValue) As H11,".
					"dslistvalue(13,TimeRateValue) As H12,dslistvalue(14,TimeRateValue) As H13,dslistvalue(15,TimeRateValue) As H14,dslistvalue(16,TimeRateValue) As H15,".
					"dslistvalue(17,TimeRateValue) As H16,dslistvalue(18,TimeRateValue) As H17,dslistvalue(19,TimeRateValue) As H18,dslistvalue(20,TimeRateValue) As H19,".
					"dslistvalue(21,TimeRateValue) As H20,dslistvalue(22,TimeRateValue) As H21,dslistvalue(23,TimeRateValue) As H22,dslistvalue(24,TimeRateValue) As H23 ".
				"From Htimerate where TimeRate_Id='$TimeRate_Id'";
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
				DSDebug(1,"DSTimeRateEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.TimeRate.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['TimeRateName']=Get_Input('POST','DB','TimeRateName','STRENCHARNUMBER',1,32,0,0);
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
				$sql= "insert Htimerate set ";
				$sql.="TimeRateName='".$NewRowInfo['TimeRateName']."',";
				$sql.="TimeRateValue=Concat(";
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
				logdbinsert($NewRowInfo,'Add','TimeRate',$RowId,'TimeRate');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSTimeRateEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.TimeRate.Edit");
				$NewRowInfo=array();
				$NewRowInfo['TimeRate_Id']=Get_Input('POST','DB','TimeRate_Id','INT',1,4294967295,0,0);
				$NewRowInfo['TimeRateName']=Get_Input('POST','DB','TimeRateName','STRENCHARNUMBER',1,32,0,0);
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


				$OldRowInfo= LoadRowInfo("Htimerate","TimeRate_Id='".$NewRowInfo['TimeRate_Id']."'");
				
				//----------------------
				$sql= "Update Htimerate set ";
				//$sql.="TimeRateName='".$NewRowInfo['TimeRateName']."',";
				$sql.="TimeRateValue=Concat(";
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
				$sql.="(TimeRate_Id='".$NewRowInfo['TimeRate_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','TimeRate',$NewRowInfo['TimeRate_Id'],'TimeRate',"Update Fail,Table=TimeRate affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=TimeRate affected row=0");
					ExitError("(ar=$ar) خطای امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'TimeRate',$NewRowInfo['TimeRate_Id'],'TimeRate')){
					logunfair("UnFair",'TimeRate',$NewRowInfo['TimeRate_Id'],'',"");
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
