<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSIndex_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array('SelectPackage','LoadCreditBalance','SelectToReseller','TransferCredit','SelectTerminal','ChangePassword','AddPackage'),0,0,0);

try {
switch ($act){
    case 'LoadCreditBalance':
				DSDebug(1,'DSIndex_EditRender LoadCreditBalance ********************************************');
				$sql="SELECT Concat(Format(CreditBalance,$PriceFloatDigit),' ریال') as CreditBalance from Hreseller where Reseller_Id='$LReseller_Id'";
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
    case "SelectToReseller":
				DSDebug(1,"DSIndex_EditRender SelectToReseller *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				if($LISManager=='Yes')
					$options->render_sql("SELECT Reseller_Id,ResellerName FROM Hreseller where (Reseller_Id<>$LReseller_Id)And(ResellerPath Like '$LResellerPath%') order by ResellerName ASC",
						"","Reseller_Id,ResellerName","","");
				else	
					$options->render_sql("SELECT Reseller_Id,ResellerName FROM Hreseller where (Reseller_Id<>$LReseller_Id)And(ResellerPath Like '$LResellerPath{$LReseller_Id}>%') order by ResellerName ASC",
						"","Reseller_Id,ResellerName","","");
				
        break;
    case "SelectPackage":
				DSDebug(1,"DSIndex_EditRender SelectPackage *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT p.Package_Id,PackageName FROM Hpackage p ".
									"left join Hreseller_packageaccess r_pa on(p.Package_Id=r_pa.Package_Id and r_pa.Reseller_Id=$LReseller_Id) ".
									"left join Hreseller r on(r.Reseller_Id=$LReseller_Id) ".
									"where (p.IsEnable='Yes')And(r.Reseller_Id=$LReseller_Id)And((Checked='Yes')Or(r.PackageAccess='All')) order by PackageName ASC",
									"","Package_Id,PackageName","","");
        break;
    case "SelectTerminal":
				DSDebug(1,"DSIndex_EditRender SelectTerminal *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT p.Terminal_Id,TerminalName FROM Hterminal p ".
									"left join Hreseller_terminalaccess r_pa on(p.Terminal_Id=r_pa.Terminal_Id and r_pa.Reseller_Id=$LReseller_Id) ".
									"left join Hreseller r on(r.Reseller_Id=$LReseller_Id) ".
									"where (p.IsEnable='Yes')And(r.Reseller_Id=$LReseller_Id)And((Checked='Yes')Or(r.TerminalAccess='All')) order by TerminalName ASC",
									"","Terminal_Id,TerminalName","","");
        break;
	case "TransferCredit":
				DSDebug(1,"DSIndex_EditRender TransferCredit ******************************************");
				//exitifnotpermit(0,"CRM.Reseller.Credit.TransferCredit");

				$To_Reseller_Id=Get_Input('POST','DB','To_Reseller_Id','INT',1,4294967295,0,0);
				$From_Reseller_Id=$LReseller_Id;
				ExitIfNotPermitRowAccess('reseller',$To_Reseller_Id);

				$Credit=floatval(Get_Input('POST','DB','Credit','PRC',1,14,0,0));
				if($Credit<=0)
					ExitError("اعتبار باید بیشتر از ۰ باشد");
				
				//Reseller have enough credit
				$Comment=Get_Input('POST','DB','Comment','STR',0,128,0,0);
				if($Credit>0){// Check From Reseller
					if($From_Reseller_Id!=1){
						$FromResellerCreditBalance=DBSelectAsString("Select CreditBalance From Hreseller_transaction Where Reseller_Id=$From_Reseller_Id Order by reseller_transaction_Id  desc Limit 1");
						if($FromResellerCreditBalance=='')$FromResellerCreditBalance=0;
						if($FromResellerCreditBalance<$Credit){
							ExitError("تراز اعتبار از نماینده فروش کافی نیست(تراز اعتبار  $FromResellerCreditBalance<$Credit)");
						}
					}
				}
				
				//----------------------
				$sql= "insert Hreseller_credit set Reseller_CreditCDT=Now(),";
				$sql.="Creator_Id=$LReseller_Id,";
				$sql.="From_Reseller_Id=$From_Reseller_Id,";
				$sql.="To_Reseller_Id=$To_Reseller_Id,";
				$sql.="Credit=$Credit,";
				$sql.="Price=0,";
				$sql.="Comment='$Comment'";

				$RowId=DBInsert($sql);
				
				logdb("Edit","Reseller",$To_Reseller_Id,"Credit","Transfer From $From_Reseller_Id to $To_Reseller_Id Credit=$Credit");
				logdb("Edit","Reseller",$From_Reseller_Id,"Credit","Transfer From $From_Reseller_Id to $To_Reseller_Id Credit=$Credit");
				
				
				//Update Receiver Credit Transaction
				AddResellerTransaction($To_Reseller_Id,$From_Reseller_Id,0,'CreditGet',$Credit);

				//Update Sender Credit Transaction
				AddResellerTransaction($From_Reseller_Id,$To_Reseller_Id,0,'CreditSend',-$Credit);

				echo "OK~";
        break;
	case 'ChangePassword':
				$inputpass=Get_Input('POST','DB','enpass','STR',128,128,0,0);
				$NewPassword1=Get_Input('POST','DB','NewPassword1','STR',6,16,0,0);
				$NewPassword2=Get_Input('POST','DB','NewPassword2','STR',6,16,0,0);
				if(strcmp($NewPassword1,$NewPassword2)!==0)
						exitError('کلمه عبور وارد شده مجدد،مطابفت ندارد');
				$sql="SELECT Pass,Salt FROM Hreseller  WHERE Reseller_Id = '$LReseller_Id' LIMIT 1";//BINARY ResellerName make case sensetive
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				if($data){
					$DBPass=$data["Pass"];
					$Salt=$data["Salt"];
					$password = hash('sha512', $inputpass.$Salt); // hash the password with the unique salt.
					if($DBPass == $password) {
						$pass1= hash('sha512', $NewPassword1);
						$Salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
						$enpass = hash('sha512', $pass1.$Salt);
						DBUpdate("Update Hreseller set pass='$enpass',Salt='$Salt' Where Reseller_Id='$LReseller_Id'");
						logdb("Edit","Reseller",$From_Reseller_Id,"Password","Password of $LResellerName has been changed");
						echo "OK~";
					
					}
					else
						exitError('خطا،پسورد قبلی صحیح نیست');
				
				}
				else
					exitError('خطا،نماینده فروش یافت نشد');
        break;
    case "AddPackage":
				require_once("../../lib/DSPayLib.php");
				DSDebug(1,"DSIndex_EditRender AddPackage *****************");
				if($LReseller_Id==1)
					ExitError("نماینده فروش ادمین دارای اعنبار نامحدود است و نیازی به خرید بسته ندارد");
				$Package_Id=Get_Input('POST','DB','Package_Id','INT',1,4294967295,0,0);
				$Terminal_Id=Get_Input('POST','DB','Terminal_Id','INT',1,4294967295,0,0);
				$Credit=DBSelectAsString("Select Round(Credit) From Hpackage Where Package_Id=$Package_Id");
				$Price=DBSelectAsString("Select Round(Price) From Hpackage Where Package_Id=$Package_Id");
				$OrderId=dsuniquid14();
				$PayOnline_Id=DBInsert("Insert into Hpayonline ".
										"Set CDT=Now(),Reseller_Id=$LReseller_Id,OrderId=$OrderId,RequestType='AddPackage',Package_Id=$Package_Id,Credit='$Credit',Price='$Price',".
										"Terminal_Id='$Terminal_Id',Status='RequestCreated'");
				$Content=Gen_Redirect($PayOnline_Id);
				echo $Content;
				
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
