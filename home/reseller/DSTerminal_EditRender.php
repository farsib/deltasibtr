<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSTerminalEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);
 
try {
switch ($act) {
    case "load":
				DSDebug(1,"DSTerminalEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.BankTerminal.View");
				$Terminal_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Terminal_Id,ISEnable,TerminalName,BankName,Mellat_TerminalNo,Mellat_Username,Mellat_Password,".
				"Saman_MerchantID,Saman_MerchantPassword,".
				"Saman_MerchantID as Refah_MerchantID,Saman_MerchantPassword as Refah_MerchantPassword,".
				"HomeUrl,CallbackUrl,Melli_MerchandID,Melli_TerminalID,Melli_TerminalType,Melli_TransactionKey,Melli_PaymentIdentity,".
				"Tejarat_MerchantID,Tejarat_AccountNo,".
				"Saderat_MID,Saderat_TID,Saderat_PrivateKeys,Saderat_PublicKeys,".
				"Jahanpay_Api,Jahanpay_Username,Jahanpay_Password,ZarinPal_MerchantId ".
				",AP_MerchantID,AP_MerchantConfigID,AP_UserName,AP_Password,AP_EncryptionKey,AP_EncryptionVector ".
				",TOSAN_Username,TOSAN_Password,TOSAN_MID,TOSAN_TID,TOSAN_goodReferenceId ".
				" from Hterminal t left join Hsaderatkeys s on Terminal_Id=SaderatTerminal_Id where Terminal_Id='$Terminal_Id'";
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
				DSDebug(1,"DSTerminalEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.BankTerminal.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['TerminalName']=Get_Input('POST','DB','TerminalName','STR',1,128,0,0);
				$NewRowInfo['HomeUrl']=Get_Input('POST','DB','HomeUrl','STR',0,250,0,0);
				$NewRowInfo['CallbackUrl']=Get_Input('POST','DB','CallbackUrl','STR',0,250,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['BankName']=Get_Input('POST','DB','BankName','ARRAY',array("Mellat","Saman","Refah","Melli","Tejarat","Saderat","Jahanpay","ZarinPal","AsanPardakht","TOSAN"),0,0,0);
				if($NewRowInfo['BankName']=='TOSAN'){
					$NewRowInfo['TOSAN_Username']=Get_Input('POST','DB','TOSAN_Username','STR',1,32,0,0);
					$NewRowInfo['TOSAN_Password']=Get_Input('POST','DB','TOSAN_Password','STR',1,32,0,0);
					$NewRowInfo['TOSAN_TID']=Get_Input('POST','DB','TOSAN_TID','STR',1,32,0,0);
					$NewRowInfo['TOSAN_MID']=Get_Input('POST','DB','TOSAN_MID','STR',1,32,0,0);
					$NewRowInfo['TOSAN_goodReferenceId']=Get_Input('POST','DB','TOSAN_goodReferenceId','STR',1,32,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
				}
				elseif($NewRowInfo['BankName']=='Mellat'){
					$NewRowInfo['Mellat_TerminalNo']=Get_Input('POST','DB','Mellat_TerminalNo','STR',1,32,0,0);
					$NewRowInfo['Mellat_Username']=Get_Input('POST','DB','Mellat_Username','STR',1,16,0,0);
					$NewRowInfo['Mellat_Password']=Get_Input('POST','DB','Mellat_Password','STR',1,16,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';

					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif(($NewRowInfo['BankName']=='Saman')||($NewRowInfo['BankName']=='Refah')){
					$NewRowInfo['Saman_MerchantID']=Get_Input('POST','DB',$NewRowInfo['BankName'].'_MerchantID','STR',1,32,0,0);
					$NewRowInfo['Saman_MerchantPassword']=Get_Input('POST','DB',$NewRowInfo['BankName'].'_MerchantPassword','STR',1,32,0,0);
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Melli'){
					$NewRowInfo['Melli_MerchandID']=Get_Input('POST','DB','Melli_MerchandID','STR',1,32,0,0);
					$NewRowInfo['Melli_TerminalID']=Get_Input('POST','DB','Melli_TerminalID','STR',1,32,0,0);
					$NewRowInfo['Melli_TerminalType']=Get_Input('POST','DB','Melli_TerminalType','ARRAY',array("Type1","Type2","Type3"),0,0,0);
					$NewRowInfo['Melli_TransactionKey']=Get_Input('POST','DB','Melli_TransactionKey','STR',1,32,0,0);
					$NewRowInfo['Melli_PaymentIdentity']=Get_Input('POST','DB','Melli_PaymentIdentity','STR',0,30,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Tejarat'){
					$NewRowInfo['Tejarat_MerchantID']=Get_Input('POST','DB','Tejarat_MerchantID','STR',1,32,0,0);
					$NewRowInfo['Tejarat_AccountNo']=Get_Input('POST','DB','Tejarat_AccountNo','STR',1,32,0,0);
					if(stripos($NewRowInfo['Tejarat_AccountNo'],"No")===0)
						ExitError("وارد کردن پیشوند الزامی نیست");
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Saderat'){
					if(extension_loaded('openssl')==false)
						ExitError("بارگذاری نشده است openssl");
					$NewRowInfo['Saderat_MID']=Get_Input('POST','DB','Saderat_MID','STR',15,15,0,0);
					$NewRowInfo['Saderat_TID']=Get_Input('POST','DB','Saderat_TID','STR',8,8,0,0);
					$NewRowInfo['Saderat_PrivateKeys']=Get_Input('POST','DB','Saderat_PrivateKeys','STR',1,1024,0,0);
					$NewRowInfo['Saderat_PublicKeys']=Get_Input('POST','DB','Saderat_PublicKeys','STR',1,1024,0,0);
					
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Jahanpay'){
					$NewRowInfo['Jahanpay_Api']=Get_Input('POST','DB','Jahanpay_Api','STR',1,32,0,0);
					$NewRowInfo['Jahanpay_Username']=Get_Input('POST','DB','Jahanpay_Username','STR',1,32,0,0);
					$NewRowInfo['Jahanpay_Password']=Get_Input('POST','DB','Jahanpay_Password','STR',1,32,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='ZarinPal'){
					$NewRowInfo['ZarinPal_MerchantId']=Get_Input('POST','DB','ZarinPal_MerchantId','STR',1,36,0,0);
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']='AsanPardakht'){
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['AP_MerchantID']=Get_Input('POST','DB','AP_MerchantID','STR',1,32,0,0);
					$NewRowInfo['AP_MerchantConfigID']=Get_Input('POST','DB','AP_MerchantConfigID','STR',1,32,0,0);
					$NewRowInfo['AP_UserName']=Get_Input('POST','DB','AP_UserName','STR',1,32,0,0);
					$NewRowInfo['AP_Password']=Get_Input('POST','DB','AP_Password','STR',1,32,0,0);
					$NewRowInfo['AP_EncryptionKey']=Get_Input('POST','DB','AP_EncryptionKey','STR',1,64,0,0);
					$NewRowInfo['AP_EncryptionVector']=Get_Input('POST','DB','AP_EncryptionVector','STR',1,64,0,0);
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				//----------------------
				$sql= "insert Hterminal set ";
				$sql.="Creator_Id=$LReseller_Id,";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="TerminalName='".$NewRowInfo['TerminalName']."',";
				$sql.="BankName='".$NewRowInfo['BankName']."',";
				$sql.="Mellat_TerminalNo='".$NewRowInfo['Mellat_TerminalNo']."',";
				$sql.="Mellat_Username='".$NewRowInfo['Mellat_Username']."',";
				$sql.="Mellat_Password='".$NewRowInfo['Mellat_Password']."',";
				$sql.="Saman_MerchantID='".$NewRowInfo['Saman_MerchantID']."',";
				$sql.="Saman_MerchantPassword='".$NewRowInfo['Saman_MerchantPassword']."',";
				$sql.="HomeUrl='".$NewRowInfo['HomeUrl']."',";
				$sql.="CallbackUrl='".$NewRowInfo['CallbackUrl']."',";
				$sql.="Melli_MerchandID='".$NewRowInfo['Melli_MerchandID']."',";
				$sql.="Melli_TerminalID='".$NewRowInfo['Melli_TerminalID']."',";
				$sql.="Melli_TerminalType='".$NewRowInfo['Melli_TerminalType']."',";
				$sql.="Melli_TransactionKey='".$NewRowInfo['Melli_TransactionKey']."',";
				$sql.="Melli_PaymentIdentity='".$NewRowInfo['Melli_PaymentIdentity']."',";
				$sql.="Tejarat_MerchantID='".$NewRowInfo['Tejarat_MerchantID']."',";
				$sql.="Tejarat_AccountNo='".$NewRowInfo['Tejarat_AccountNo']."',";
				$sql.="Saderat_MID='".$NewRowInfo['Saderat_MID']."',";
				$sql.="Saderat_TID='".$NewRowInfo['Saderat_TID']."',";
				$sql.="Jahanpay_Api='".$NewRowInfo['Jahanpay_Api']."',";
				$sql.="Jahanpay_Username='".$NewRowInfo['Jahanpay_Username']."',";
				$sql.="Jahanpay_Password='".$NewRowInfo['Jahanpay_Password']."',";
				$sql.="ZarinPal_MerchantId='".$NewRowInfo['ZarinPal_MerchantId']."',";
				$sql.="AP_MerchantID='".$NewRowInfo['AP_MerchantID']."',";
				$sql.="AP_MerchantConfigID='".$NewRowInfo['AP_MerchantConfigID']."',";
				$sql.="AP_UserName='".$NewRowInfo['AP_UserName']."',";
				$sql.="AP_Password='".$NewRowInfo['AP_Password']."',";
				$sql.="AP_EncryptionKey='".$NewRowInfo['AP_EncryptionKey']."',";
				$sql.="AP_EncryptionVector='".$NewRowInfo['AP_EncryptionVector']."',";
				$sql.="TOSAN_Username='".$NewRowInfo['TOSAN_Username']."',";
				$sql.="TOSAN_Password='".$NewRowInfo['TOSAN_Password']."',";
				$sql.="TOSAN_TID='".$NewRowInfo['TOSAN_TID']."',";
				$sql.="TOSAN_MID='".$NewRowInfo['TOSAN_MID']."',";
				$sql.="TOSAN_goodReferenceId='".$NewRowInfo['TOSAN_goodReferenceId']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Terminal_Id']=$RowId;
								
				if($NewRowInfo['BankName']=='Saderat'){
					$OldRowInfo['Saderat_PrivateKeys']=DBSelectAsString("Select Saderat_PrivateKeys from Hsaderatkeys where SaderatTerminal_Id='".$NewRowInfo['Terminal_Id']."'");
					$OldRowInfo['Saderat_PublicKeys']=DBSelectAsString("Select Saderat_PublicKeys from Hsaderatkeys where SaderatTerminal_Id='".$NewRowInfo['Terminal_Id']."'");
					$sql="replace Hsaderatkeys set ".
						"SaderatTerminal_Id='".$NewRowInfo['Terminal_Id']."',".
						"Saderat_PrivateKeys='".$NewRowInfo['Saderat_PrivateKeys']."',".
						"Saderat_PublicKeys='".$NewRowInfo['Saderat_PublicKeys']."'";
					DBUpdate($sql);
					
					$Res=runshellcommand("php","DSCreateSaderatKeys","","");
					DSDebug(1,"DSCreateSaderatKeys->Reply [$Res]");					
				}

				logdbinsert($NewRowInfo,'Add','Terminal',$RowId,'Terminal');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSTerminalEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.BankTerminal.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Terminal_Id']=Get_Input('POST','DB','Terminal_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['TerminalName']=Get_Input('POST','DB','TerminalName','STR',1,128,0,0);
				$NewRowInfo['HomeUrl']=Get_Input('POST','DB','HomeUrl','STR',0,250,0,0);
				$NewRowInfo['CallbackUrl']=Get_Input('POST','DB','CallbackUrl','STR',0,250,0,0);
				$NewRowInfo['BankName']=Get_Input('POST','DB','BankName','ARRAY',array("Mellat","Saman","Refah","Melli","Tejarat","Saderat","Jahanpay","ZarinPal","AsanPardakht",'TOSAN'),0,0,0);
				$OldBank=DBSelectAsString('Select BankName From Hterminal Where Terminal_Id='.$NewRowInfo['Terminal_Id']);
				if($OldBank!=$NewRowInfo['BankName'])
					ExitError("نام بانک را نمی توانید تغییر دهید");	
				if($NewRowInfo['BankName']=='TOSAN'){
					$NewRowInfo['TOSAN_Username']=Get_Input('POST','DB','TOSAN_Username','STR',1,32,0,0);
					$NewRowInfo['TOSAN_Password']=Get_Input('POST','DB','TOSAN_Password','STR',1,32,0,0);
					$NewRowInfo['TOSAN_TID']=Get_Input('POST','DB','TOSAN_TID','STR',1,32,0,0);
					$NewRowInfo['TOSAN_MID']=Get_Input('POST','DB','TOSAN_MID','STR',1,32,0,0);
					$NewRowInfo['TOSAN_goodReferenceId']=Get_Input('POST','DB','TOSAN_goodReferenceId','STR',1,32,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
				}
				elseif($NewRowInfo['BankName']=='Mellat'){
					$NewRowInfo['Mellat_TerminalNo']=Get_Input('POST','DB','Mellat_TerminalNo','STR',1,32,0,0);
					$NewRowInfo['Mellat_Username']=Get_Input('POST','DB','Mellat_Username','STR',1,16,0,0);
					$NewRowInfo['Mellat_Password']=Get_Input('POST','DB','Mellat_Password','STR',1,16,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif(($NewRowInfo['BankName']=='Saman')||($NewRowInfo['BankName']=='Refah')){
					$NewRowInfo['Saman_MerchantID']=Get_Input('POST','DB',$NewRowInfo['BankName'].'_MerchantID','STR',1,32,0,0);
					$NewRowInfo['Saman_MerchantPassword']=Get_Input('POST','DB',$NewRowInfo['BankName'].'_MerchantPassword','STR',1,32,0,0);					
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Melli'){
					$NewRowInfo['Melli_MerchandID']=Get_Input('POST','DB','Melli_MerchandID','STR',1,32,0,0);
					$NewRowInfo['Melli_TerminalID']=Get_Input('POST','DB','Melli_TerminalID','STR',1,32,0,0);
					$NewRowInfo['Melli_TerminalType']=Get_Input('POST','DB','Melli_TerminalType','ARRAY',array("Type1","Type2","Type3"),0,0,0);
					$NewRowInfo['Melli_TransactionKey']=Get_Input('POST','DB','Melli_TransactionKey','STR',1,32,0,0);
					$NewRowInfo['Melli_PaymentIdentity']=Get_Input('POST','DB','Melli_PaymentIdentity','STR',0,30,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Tejarat'){
					$NewRowInfo['Tejarat_MerchantID']=Get_Input('POST','DB','Tejarat_MerchantID','STR',1,32,0,0);
					$NewRowInfo['Tejarat_AccountNo']=Get_Input('POST','DB','Tejarat_AccountNo','STR',1,32,0,0);
					if(stripos($NewRowInfo['Tejarat_AccountNo'],"No")===0)
						ExitError("وارد کردن پیشوند الزامی نیست");
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';
					
					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Saderat'){
					$NewRowInfo['Saderat_MID']=Get_Input('POST','DB','Saderat_MID','STR',15,15,0,0);
					$NewRowInfo['Saderat_TID']=Get_Input('POST','DB','Saderat_TID','STR',8,8,0,0);
					$NewRowInfo['Saderat_PrivateKeys']=Get_Input('POST','DB','Saderat_PrivateKeys','STR',1,1024,0,0);
					$NewRowInfo['Saderat_PublicKeys']=Get_Input('POST','DB','Saderat_PublicKeys','STR',1,1024,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='Jahanpay'){
					$NewRowInfo['Jahanpay_Api']=Get_Input('POST','DB','Jahanpay_Api','STR',1,32,0,0);
					$NewRowInfo['Jahanpay_Username']=Get_Input('POST','DB','Jahanpay_Username','STR',1,32,0,0);
					$NewRowInfo['Jahanpay_Password']=Get_Input('POST','DB','Jahanpay_Password','STR',1,32,0,0);
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']=='ZarinPal'){
					$NewRowInfo['ZarinPal_MerchantId']=Get_Input('POST','DB','ZarinPal_MerchantId','STR',1,36,0,0);
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['AP_MerchantID']='';
					$NewRowInfo['AP_MerchantConfigID']='';
					$NewRowInfo['AP_UserName']='';
					$NewRowInfo['AP_Password']='';
					$NewRowInfo['AP_EncryptionKey']='';
					$NewRowInfo['AP_EncryptionVector']='';
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				elseif($NewRowInfo['BankName']='AsanPardakht'){
					$NewRowInfo['ZarinPal_MerchantId']='';
					$NewRowInfo['Jahanpay_Api']='';
					$NewRowInfo['Jahanpay_Username']='';
					$NewRowInfo['Jahanpay_Password']='';
					$NewRowInfo['Saman_MerchantID']='';
					$NewRowInfo['Saman_MerchantPassword']='';
					$NewRowInfo['Mellat_TerminalNo']='';
					$NewRowInfo['Mellat_Username']='';
					$NewRowInfo['Mellat_Password']='';
					$NewRowInfo['Melli_MerchandID']='';
					$NewRowInfo['Melli_TerminalID']='';
					$NewRowInfo['Melli_TerminalType']='';

					$NewRowInfo['Melli_TransactionKey']='';
					$NewRowInfo['Melli_PaymentIdentity']='';
					$NewRowInfo['Tejarat_MerchantID']='';
					$NewRowInfo['Tejarat_AccountNo']='';
					$NewRowInfo['Saderat_MID']='';
					$NewRowInfo['Saderat_TID']='';
					$NewRowInfo['AP_MerchantID']=Get_Input('POST','DB','AP_MerchantID','STR',1,32,0,0);
					$NewRowInfo['AP_MerchantConfigID']=Get_Input('POST','DB','AP_MerchantConfigID','STR',1,32,0,0);
					$NewRowInfo['AP_UserName']=Get_Input('POST','DB','AP_UserName','STR',1,32,0,0);
					$NewRowInfo['AP_Password']=Get_Input('POST','DB','AP_Password','STR',1,32,0,0);
					$NewRowInfo['AP_EncryptionKey']=Get_Input('POST','DB','AP_EncryptionKey','STR',1,64,0,0);
					$NewRowInfo['AP_EncryptionVector']=Get_Input('POST','DB','AP_EncryptionVector','STR',1,64,0,0);
					$NewRowInfo['TOSAN_Username']='';
					$NewRowInfo['TOSAN_Password']='';
					$NewRowInfo['TOSAN_TID']='';
					$NewRowInfo['TOSAN_MID']='';
					$NewRowInfo['TOSAN_goodReferenceId']='';
				}
				$OldRowInfo= LoadRowInfo("Hterminal","Terminal_Id='".$NewRowInfo['Terminal_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hterminal set ";
				$sql.="Creator_Id=$LReseller_Id,";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="TerminalName='".$NewRowInfo['TerminalName']."',";
				$sql.="BankName='".$NewRowInfo['BankName']."',";
				$sql.="Mellat_TerminalNo='".$NewRowInfo['Mellat_TerminalNo']."',";
				$sql.="Mellat_Username='".$NewRowInfo['Mellat_Username']."',";
				$sql.="Mellat_Password='".$NewRowInfo['Mellat_Password']."',";
				$sql.="Saman_MerchantID='".$NewRowInfo['Saman_MerchantID']."',";
				$sql.="Saman_MerchantPassword='".$NewRowInfo['Saman_MerchantPassword']."',";
				$sql.="HomeUrl='".$NewRowInfo['HomeUrl']."',";
				$sql.="CallbackUrl='".$NewRowInfo['CallbackUrl']."',";
				$sql.="Melli_MerchandID='".$NewRowInfo['Melli_MerchandID']."',";
				$sql.="Melli_TerminalID='".$NewRowInfo['Melli_TerminalID']."',";
				$sql.="Melli_TerminalType='".$NewRowInfo['Melli_TerminalType']."',";

				$sql.="Melli_TransactionKey='".$NewRowInfo['Melli_TransactionKey']."',";
				$sql.="Melli_PaymentIdentity='".$NewRowInfo['Melli_PaymentIdentity']."',";
				$sql.="Tejarat_MerchantID='".$NewRowInfo['Tejarat_MerchantID']."',";
				$sql.="Tejarat_AccountNo='".$NewRowInfo['Tejarat_AccountNo']."',";
				$sql.="Saderat_MID='".$NewRowInfo['Saderat_MID']."',";
				$sql.="Saderat_TID='".$NewRowInfo['Saderat_TID']."',";
				$sql.="Jahanpay_Api='".$NewRowInfo['Jahanpay_Api']."',";
				$sql.="Jahanpay_Username='".$NewRowInfo['Jahanpay_Username']."',";
				$sql.="Jahanpay_Password='".$NewRowInfo['Jahanpay_Password']."',";
				$sql.="ZarinPal_MerchantId='".$NewRowInfo['ZarinPal_MerchantId']."',";
				$sql.="AP_MerchantID='".$NewRowInfo['AP_MerchantID']."',";
				$sql.="AP_MerchantConfigID='".$NewRowInfo['AP_MerchantConfigID']."',";
				$sql.="AP_UserName='".$NewRowInfo['AP_UserName']."',";
				$sql.="AP_Password='".$NewRowInfo['AP_Password']."',";
				$sql.="AP_EncryptionKey='".$NewRowInfo['AP_EncryptionKey']."',";
				$sql.="AP_EncryptionVector='".$NewRowInfo['AP_EncryptionVector']."',";
				$sql.="TOSAN_Username='".$NewRowInfo['TOSAN_Username']."',";
				$sql.="TOSAN_Password='".$NewRowInfo['TOSAN_Password']."',";
				$sql.="TOSAN_TID='".$NewRowInfo['TOSAN_TID']."',";
				$sql.="TOSAN_MID='".$NewRowInfo['TOSAN_MID']."',";
				$sql.="TOSAN_goodReferenceId='".$NewRowInfo['TOSAN_goodReferenceId']."'";
				$sql.=" Where ";
				$sql.="(Terminal_Id='".$NewRowInfo['Terminal_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				/*if($ar!=1){//probably hack
					logdb('Edit','Terminal',$NewRowInfo['Terminal_Id'],'Terminal',"Update Fail,Table=Terminal affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Terminal affected row=0");
					ExitError("(ar=$ar) Security problem, Report Sent to Administrator");	
				}*/
					
				if($NewRowInfo['BankName']=='Saderat'){
					$OldRowInfo['Saderat_PrivateKeys']=DBSelectAsString("Select Saderat_PrivateKeys from Hsaderatkeys where SaderatTerminal_Id='".$NewRowInfo['Terminal_Id']."'");
					$OldRowInfo['Saderat_PublicKeys']=DBSelectAsString("Select Saderat_PublicKeys from Hsaderatkeys where SaderatTerminal_Id='".$NewRowInfo['Terminal_Id']."'");
					$sql="replace Hsaderatkeys set ".
						"SaderatTerminal_Id='".$NewRowInfo['Terminal_Id']."',".
						"Saderat_PrivateKeys='".$NewRowInfo['Saderat_PrivateKeys']."',".
						"Saderat_PublicKeys='".$NewRowInfo['Saderat_PublicKeys']."'";
					DBUpdate($sql);
					$Res=runshellcommand("php","DSCreateSaderatKeys","","");
					DSDebug(1,"DSCreateSaderatKeys->Reply [$Res]");					
				}
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Terminal',$NewRowInfo['Terminal_Id'],'Terminal')){
					logunfair("UnFair",'Terminal',$NewRowInfo['Terminal_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else{
					echo "OK~";
				}
        break;
	case "SelectTerminalInfoName":
				DSDebug(1,"DSTerminalEditRender SelectTerminalInfoName *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT TerminalInfo_Id,TerminalInfoName FROM HTerminalinfo order by TerminalInfoName ASC","","TerminalInfo_Id,TerminalInfoName","","");
	
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
