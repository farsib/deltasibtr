<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSSupporterEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();
$act=Get_Input('GET','DB','act','ARRAY',array('load','insert','update','SelectReseller','LoadCommission','UpdateCommission'),0,0,0);

try {
switch ($act) {
    case 'load':
				DSDebug(1,'DSSupporterEditRender Load ********************************************');
				exitifnotpermit(0,'Admin.Supporter.View');
				$Supporter_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT Supporter_Id,ISEnable,SupporterName,Reseller_Id,UsernamePattern,PerPortDailyCF,PerPaymentCF  from Hsupporter where Supporter_Id='$Supporter_Id'";
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
				DSDebug(1,"DSSupporterEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Supporter.Add");
				$NewRowInfo=array();
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['SupporterName']=Get_Input('POST','DB','SupporterName','STR',1,64,0,0);
				$NewRowInfo['Reseller_Id']=Get_Input('POST','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("select Reseller_Id from Hreseller where Reseller_Id='".$NewRowInfo['Reseller_Id']."'")<=0)
						ExitError("نماینده فروش نامعتبر انتخاب شده است");
				
				$NewRowInfo['UsernamePattern']=Get_Input('POST','DB','UsernamePattern','STR',1,250,0,0);
				$NewRowInfo['PerPortDailyCF']=Get_Input('POST','DB','PerPortDailyCF','STR',1,64,0,0);
				$NewRowInfo['PerPaymentCF']=Get_Input('POST','DB','PerPaymentCF','STR',1,128,0,0);

				$PerPortDailyCF=Get_Input('POST','DB','PerPortDailyCF','STR',1,64,0,0);
				$ISFormulaOK=DBSelectAsString("Select '$PerPortDailyCF' REGEXP '[0-9.,]*'");
				if($ISFormulaOK!=1)
					ExitError("فرمول پورسانت به ازای هر پورت اشتباه است");

				$PerPaymentCF=Get_Input('POST','DB','PerPaymentCF','STR',1,64,0,0);
				$ISFormulaOK=DBSelectAsString("Select '$PerPaymentCF' REGEXP '[0-9.,]*'");
				if($ISFormulaOK!=1)
					ExitError("فرمول پورسانت به ازای هر پرداخت استباه است");

				//----------------------
				$sql= "insert Hsupporter set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="Reseller_Id='".$NewRowInfo['Reseller_Id']."',";
				$sql.="SupporterName='".$NewRowInfo['SupporterName']."',";
				$sql.="UsernamePattern='".$NewRowInfo['UsernamePattern']."',";
				$sql.="PerPortDailyCF='".$NewRowInfo['PerPortDailyCF']."',";
				$sql.="PerPaymentCF='".$NewRowInfo['PerPaymentCF']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Supporter_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','Supporter',$RowId,'Supporter');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSSupporterEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Supporter.Edit");
				$NewRowInfo=array();
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['SupporterName']=Get_Input('POST','DB','SupporterName','STR',1,64,0,0);
				$NewRowInfo['Supporter_Id']=Get_Input('POST','DB','Supporter_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Reseller_Id']=Get_Input('POST','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("select Reseller_Id from Hreseller where Reseller_Id='".$NewRowInfo['Reseller_Id']."'")<=0)
						ExitError("نماینده فروش نامعتبر انتخاب شده است");				
				$NewRowInfo['UsernamePattern']=Get_Input('POST','DB','UsernamePattern','STR',1,250,0,0);
				$NewRowInfo['PerPortDailyCF']=Get_Input('POST','DB','PerPortDailyCF','STR',1,64,0,0);
				$NewRowInfo['PerPaymentCF']=Get_Input('POST','DB','PerPaymentCF','STR',1,128,0,0);

				$PerPortDailyCF=Get_Input('POST','DB','PerPortDailyCF','STR',1,64,0,0);
				$ISFormulaOK=DBSelectAsString("Select '$PerPortDailyCF' REGEXP '[0-9.,]*'");
				if($ISFormulaOK!=1)
					ExitError("فرمول پورسانت به ازای هر پورت اشتباه است");

				$PerPaymentCF=Get_Input('POST','DB','PerPaymentCF','STR',1,64,0,0);
				$ISFormulaOK=DBSelectAsString("Select '$PerPaymentCF' REGEXP '[0-9.,]*'");
				if($ISFormulaOK!=1)
					ExitError("فرمول پورسانت به ازای هر پرداخت اشتباه است");

				$OldRowInfo= LoadRowInfo("Hsupporter","Supporter_Id='".$NewRowInfo['Supporter_Id']."'");
				
				//DSDebug(2,DSPrintArray($OldRowInfo));
				//DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "Update Hsupporter set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="SupporterName='".$NewRowInfo['SupporterName']."',";
				$sql.="Reseller_Id='".$NewRowInfo['Reseller_Id']."',";
				$sql.="UsernamePattern='".$NewRowInfo['UsernamePattern']."',";
				$sql.="PerPortDailyCF='".$NewRowInfo['PerPortDailyCF']."',";
				$sql.="PerPaymentCF='".$NewRowInfo['PerPaymentCF']."'";
				$sql.=" Where ";
				$sql.="(Supporter_Id='".$NewRowInfo['Supporter_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Supporter',$NewRowInfo['Supporter_Id'],'Supporter',"Update Fail,Table=Supporter affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Supporter affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Supporter',$NewRowInfo['Supporter_Id'],'Supporter')){
					logunfair("UnFair",'Supporter',$NewRowInfo['Supporter_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
    case "SelectReseller":
				DSDebug(1,"DSSupporterEditRender SelectReseller *****************");
				exitifnotpermit(0,"Admin.Supporter.View");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Reseller_Id,ResellerName ".
					"From Hreseller r order by ResellerName";
				$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
