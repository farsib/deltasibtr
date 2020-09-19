<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_1_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSServer_1_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Server.Param.View");
				$Server_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,".
					"Server_Id,".
					"PartName,".
					"Param2 As FaCompanyname,".
					"Param3 As Address,".
					"Param4 As Phone,".
					"Param5 As VAT,".
					"Param6 As KeepOldDeltasibBackupItems,".
					"Param7 As KeepOldURLExportItems,".
					"Param8 As KeepOldHTTPLogItems,".
					"Param9 As EnCompanyname, ".
					"Param10 As SellerName, ".
					"Param11 As SellerPhone, ".
					"Param12 As SellerAddress, ".
					"Param13 As SellerEconomyCode, ".
					"Param14 As SellerNationalCode, ".
					"Param15 As SellerPostalCode, ".
					"Param16 As SellerRegistryCode ".
					"from Hserver where Server_Id='$Server_Id'";
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
    case "update":
				DSDebug(1,"DSServer_2_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Server.HttpLog.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Server_Id']=Get_Input('POST','DB','Server_Id','INT',1,4294967295,0,0);
				//Param1=Version ReadOnly
				$NewRowInfo['Param2']=Get_Input('POST','DB','FaCompanyname','STR',1,128,0,0);
				$NewRowInfo['Param3']=Get_Input('POST','DB','Address','STR',1,128,0,0);
				$NewRowInfo['Param4']=Get_Input('POST','DB','Phone','STR',1,128,0,0);
				$NewRowInfo['Param5']=Get_Input('POST','DB','VAT','FLT',0,50,0,0);
				$NewRowInfo['Param6']=Get_Input('POST','DB','KeepOldDeltasibBackupItems','INT',0,999,0,0);
				$NewRowInfo['Param7']=Get_Input('POST','DB','KeepOldURLExportItems','INT',0,999,0,0);
				$NewRowInfo['Param8']=Get_Input('POST','DB','KeepOldHTTPLogItems','INT',0,999,0,0);
				$NewRowInfo['Param9']=Get_Input('POST','DB','EnCompanyname','STR',1,128,0,0);
				
				$NewRowInfo['Param10']=Get_Input('POST','DB','SellerName','STR',1,32,0,0);
				$NewRowInfo['Param11']=Get_Input('POST','DB','SellerPhone','STR',1,20,0,0);
				$NewRowInfo['Param12']=Get_Input('POST','DB','SellerAddress','STR',1,128,0,0);
				$NewRowInfo['Param13']=Get_Input('POST','DB','SellerEconomyCode','STR',1,12,0,0);
				$NewRowInfo['Param14']=Get_Input('POST','DB','SellerNationalCode','STR',1,11,0,0);
				$NewRowInfo['Param15']=Get_Input('POST','DB','SellerPostalCode','STR',1,10,0,0);
				$NewRowInfo['Param16']=Get_Input('POST','DB','SellerRegistryCode','STR',1,10,0,0);

				$OldRowInfo= LoadRowInfo("Hserver","Server_Id='".$NewRowInfo['Server_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hserver set  ";
				//$sql.="Param1='".$NewRowInfo['Param1']."',";
				$sql.="Param2='".$NewRowInfo['Param2']."',";
				$sql.="Param3='".$NewRowInfo['Param3']."',";
				$sql.="Param4='".$NewRowInfo['Param4']."',";
				$sql.="Param5='".$NewRowInfo['Param5']."',";
				$sql.="Param6='".$NewRowInfo['Param6']."',";
				$sql.="Param7='".$NewRowInfo['Param7']."',";
				$sql.="Param8='".$NewRowInfo['Param8']."',";
				$sql.="Param9='".$NewRowInfo['Param9']."',";
				$sql.="Param10='".$NewRowInfo['Param10']."',";
				$sql.="Param11='".$NewRowInfo['Param11']."',";
				$sql.="Param12='".$NewRowInfo['Param12']."',";
				$sql.="Param13='".$NewRowInfo['Param13']."',";
				$sql.="Param14='".$NewRowInfo['Param14']."',";
				$sql.="Param15='".$NewRowInfo['Param15']."',";
				$sql.="Param16='".$NewRowInfo['Param16']."'";
				$sql.=" Where ";
				$sql.="(Server_Id='".$NewRowInfo['Server_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Server',$NewRowInfo['Server_Id'],'Server',"Update Fail,Table=Server affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Server affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Server',$NewRowInfo['Server_Id'],'Server')){
					logunfair("UnFair",'Server',$NewRowInfo['Server_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				$res=runshellcommand("php","DSSetInvoiceFields","","");
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
