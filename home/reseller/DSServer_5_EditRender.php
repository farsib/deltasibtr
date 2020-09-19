<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_5_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","update",'SelectVisp','SelectCenter','SelectSupporter','SelectReseller','SelectStatus','SelectSMSProvider','SelectServiceBase'),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSServer_5_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Server.WebNewUser.View");
				$Server_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Server_Id,PartName,Param1 As IsEnableWebNewUser,Param2 As SetUsernameTo,Param3 As Visp_Id,Param4 As Center_Id,".
				"Param5 As Supporter_Id,Param6 As Reseller_Id ".
				",Param7 as AfterInsertSMS,Param8 as SetStatusTo,Param9 As SMSExpireTime,Param10 As Service_Id,Param11 as NationalCodeRequired,".
				"Param12 as AfterInsertMessage,Param13 as OnDuplicateMessage,Param14 as ShahkarValidation ".
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
				DSDebug(1,"DSServer_5_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Server.WebNewUser.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Server_Id']=Get_Input('POST','DB','Server_Id','INT',1,4294967295,0,0);
				$NewRowInfo['Param1']=Get_Input('POST','DB','IsEnableWebNewUser','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['Param1']=="Yes"){
					$NewRowInfo['Param2']=Get_Input('POST','DB','SetUsernameTo','ARRAY',array("Mobile","NationalCode"),0,0,0);
					$NewRowInfo['Param3']=Get_Input('POST','DB','Visp_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select VispName from Hvisp where Visp_Id='".$NewRowInfo['Param3']."'")=="")
						ExitError("ارائه دهنده مجازی نامعتبر انتخاب شده است");
					
					$NewRowInfo['Param4']=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select CenterName from Hcenter where Center_Id='".$NewRowInfo['Param4']."'")=="")
						ExitError("مرکز نامعتبر انتخاب شده است");
					
					$NewRowInfo['Param5']=Get_Input('POST','DB','Supporter_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select SupporterName from Hsupporter where Supporter_Id='".$NewRowInfo['Param5']."'")=="")
						ExitError("پشتیبان نامعتبر انتخاب شده است");
					
					$NewRowInfo['Param6']=Get_Input('POST','DB','Reseller_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select ResellerName from Hreseller where Reseller_Id='".$NewRowInfo['Param6']."'")=="")
						ExitError("نماینده فروش نامعتبر انتخاب شده است");
					
					$NewRowInfo['Param7']=Get_Input('POST','DB','AfterInsertSMS','STR',0,255,0,0);
					if($NewRowInfo['Param7']!="")
						$NewRowInfo['Param9']=Get_Input('POST','DB','SMSExpireTime','INT',1,4294967295,0,0);
					else
						$NewRowInfo['Param9']=0;
					$NewRowInfo['Param8']=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select StatusName from Hstatus where Status_Id='".$NewRowInfo['Param8']."'")=="")
						ExitError("وضعیت نامعتبر انتخاب شده است");
					
					$NewRowInfo['Param10']=Get_Input('POST','DB','Service_Id','INT',0,4294967295,0,0);
					if(($NewRowInfo['Param10']>0)&&(DBSelectAsString("select ServiceName from Hservice where Service_Id='".$NewRowInfo['Param10']."'")==""))
						ExitError("سرویس نامعتبر انتخاب شده است");
					
					if($NewRowInfo['Param2']=="Mobile")
						$NewRowInfo['Param11']=Get_Input('POST','DB','NationalCodeRequired','ARRAY',array("Yes","No"),0,0,0);
					else
						$NewRowInfo['Param11']="Yes";
					
					$NewRowInfo['Param12']=Get_Input('POST','DB','AfterInsertMessage','STR',1,255,0,0);
					$NewRowInfo['Param13']=Get_Input('POST','DB','OnDuplicateMessage','STR',1,255,0,0);
					$NewRowInfo['Param14']=Get_Input('POST','DB','ShahkarValidation','ARRAY',array("Yes","No"),0,0,0);
					
				}
				else{
					$NewRowInfo['Param2']="Mobile";
					$NewRowInfo['Param3']=0;
					$NewRowInfo['Param4']=0;
					$NewRowInfo['Param5']=0;
					$NewRowInfo['Param6']=0;
					$NewRowInfo['Param7']="";
					$NewRowInfo['Param9']=0;
					$NewRowInfo['Param8']=0;
					$NewRowInfo['Param10']=0;
					$NewRowInfo['Param11']="Yes";
					$NewRowInfo['Param12']="";
					$NewRowInfo['Param13']="";					
					$NewRowInfo['Param14']="No";					
				}
				
				$OldRowInfo= LoadRowInfo("Hserver","Server_Id='".$NewRowInfo['Server_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hserver set  ";
				$sql.="Param1='".$NewRowInfo['Param1']."',";
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
				$sql.="Param14='".$NewRowInfo['Param14']."'";
				$sql.=" Where ";
				$sql.="(Server_Id='".$NewRowInfo['Server_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				DSDebug(2,$res);
				
				if($ar!=1){//probably hack
					logdb('Edit','Server',$NewRowInfo['Server_Id'],'Server',"Update Fail,Table=Server affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Server affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Server',$NewRowInfo['Server_Id'],'Server')){
					logunfair("UnFair",'Server',$NewRowInfo['Server_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				echo "OK~";
        break;
    case "SelectVisp":
				DSDebug(1,"DSServer_5_EditRender SelectVisp *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT v.Visp_Id,VispName FROM Hvisp v ".
					"where (v.IsEnable='Yes') order by VispName ASC";
				$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectCenter":
				DSDebug(1,"DSServer_5_EditRender SelectCenter *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT c.Center_Id,CenterName from Hcenter c ".
						" order by CenterName asc";
				$options->render_sql($sql,"","Center_Id,CenterName","","");
        break;
    case "SelectSupporter":
				DSDebug(1,"DSServer_5_EditRender SelectSupporter *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Supporter_Id,SupporterName FROM Hsupporter order by SupporterName asc";
				$options->render_sql($sql,"","Supporter_Id,SupporterName","","");
        break;
    case "SelectReseller":
				DSDebug(1,"DSServer_5_EditRender SelectReseller *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Reseller_Id,ResellerName ".
					"From Hreseller r Where (ISOperator='No') order by ResellerName Asc";
				$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;
    case "SelectStatus":
				DSDebug(1,"DSServer_5_EditRender SelectStatus *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Status_Id,StatusName from Hstatus Order By StatusName";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
        break;
	case 'SelectSMSProvider':
				DSDebug(1,"DSServer_5_EditRender SelectSMSProvider *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT SMSProvider_Id,SMSProviderName From Hsmsprovider Order by SMSProviderName ASC","","SMSProvider_Id,SMSProviderName","","");
        break;
    case "SelectServiceBase":
				DSDebug(1,"DSActiveDirectoryEditRender-> SelectServiceBase *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="Select 0 As Service_Id,'None' As ServiceName union (Select Service_Id,ServiceName From Hservice ".
					"Where (ServiceType='Base')and(IsDel='No') order by ServiceName)";
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
