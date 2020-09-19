<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSOffFormulaEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,">Load");
				exitifnotpermit(0,"Admin.User.OffFormula.View");
				$OffFormula_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,OffFormula_Id,OffFormulaName,FixOff,MonthlyRate,MonthlyMaxOff,TimeBaseOff,TimeRegex,100-SavingOffPercent as DirectOffPercent,SavingOffPercent,SavingOffExpirationDays ".
				"From Hoffformula where OffFormula_Id='$OffFormula_Id'";
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
				DSDebug(1,">Insert");
				exitifnotpermit(0,"Admin.User.OffFormula.Add");
				$NewRowInfo=array();

				$NewRowInfo['OffFormulaName']=Get_Input('POST','DB','OffFormulaName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['FixOff']=Get_Input('POST','DB','FixOff','FLT',0,100,0,0);
				$NewRowInfo['MonthlyRate']=Get_Input('POST','DB','MonthlyRate','FLT',0,100,0,0);
				$NewRowInfo['MonthlyMaxOff']=Get_Input('POST','DB','MonthlyMaxOff','FLT',0,100,0,0);
				$NewRowInfo['TimeBaseOff']=Get_Input('POST','DB','TimeBaseOff','FLT',0,100,0,0);
				$NewRowInfo['TimeRegex']=Get_Input('POST','DB','TimeRegex','STR',0,250,0,0);
				$NewRowInfo['SavingOffPercent']=Get_Input('POST','DB','SavingOffPercent','FLT',0,100,0,0);
				$NewRowInfo['SavingOffExpirationDays']=Get_Input('POST','DB','SavingOffExpirationDays','INT',0,65535,0,0);

				//----------------------
				$sql= "insert Hoffformula set ";
				$sql.="OffFormulaName='".$NewRowInfo['OffFormulaName']."',";
				$sql.="FixOff='".$NewRowInfo['FixOff']."',";
				$sql.="MonthlyRate='".$NewRowInfo['MonthlyRate']."',";
				$sql.="MonthlyMaxOff='".$NewRowInfo['MonthlyMaxOff']."',";
				$sql.="TimeBaseOff='".$NewRowInfo['TimeBaseOff']."',";
				$sql.="TimeRegex='".$NewRowInfo['TimeRegex']."',";
				$sql.="SavingOffPercent='".$NewRowInfo['SavingOffPercent']."',";
				$sql.="SavingOffExpirationDays='".$NewRowInfo['SavingOffExpirationDays']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','OffFormula',$RowId,'OffFormula');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,">Update");
				exitifnotpermit(0,"Admin.User.OffFormula.Edit");
				$NewRowInfo=array();
				$NewRowInfo['OffFormula_Id']=Get_Input('POST','DB','OffFormula_Id','INT',1,4294967295,0,0);
				$NewRowInfo['FixOff']=Get_Input('POST','DB','FixOff','FLT',0,100,0,0);
				$NewRowInfo['MonthlyRate']=Get_Input('POST','DB','MonthlyRate','FLT',0,100,0,0);
				$NewRowInfo['MonthlyMaxOff']=Get_Input('POST','DB','MonthlyMaxOff','FLT',0,100,0,0);
				$NewRowInfo['TimeBaseOff']=Get_Input('POST','DB','TimeBaseOff','FLT',0,100,0,0);
				$NewRowInfo['TimeRegex']=Get_Input('POST','DB','TimeRegex','STR',0,250,0,0);
				$NewRowInfo['SavingOffPercent']=Get_Input('POST','DB','SavingOffPercent','FLT',0,100,0,0);
				$NewRowInfo['SavingOffExpirationDays']=Get_Input('POST','DB','SavingOffExpirationDays','INT',0,65535,0,0);
				
				$OldRowInfo= LoadRowInfo("Hoffformula","OffFormula_Id='".$NewRowInfo['OffFormula_Id']."'");
				
				//----------------------
				
				$sql= "Update Hoffformula set ";
				//$sql.="OffFormulaName='".$NewRowInfo['OffFormulaName']."',";
				$sql.="FixOff='".$NewRowInfo['FixOff']."',";
				$sql.="MonthlyRate='".$NewRowInfo['MonthlyRate']."',";
				$sql.="MonthlyMaxOff='".$NewRowInfo['MonthlyMaxOff']."',";
				$sql.="TimeBaseOff='".$NewRowInfo['TimeBaseOff']."',";
				$sql.="TimeRegex='".$NewRowInfo['TimeRegex']."',";
				$sql.="SavingOffPercent='".$NewRowInfo['SavingOffPercent']."',";
				$sql.="SavingOffExpirationDays='".$NewRowInfo['SavingOffExpirationDays']."'";				
				$sql.=" Where ";
				$sql.="(OffFormula_Id='".$NewRowInfo['OffFormula_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','OffFormula',$NewRowInfo['OffFormula_Id'],'OffFormula',"Update Fail,Table=OffFormula affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=OffFormula affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'OffFormula',$NewRowInfo['OffFormula_Id'],'OffFormula')){
					logunfair("UnFair",'OffFormula',$NewRowInfo['OffFormula_Id'],'',"");
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
