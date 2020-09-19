<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSResellerCredit_Credit_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission

$act=Get_Input('GET','DB','act','ARRAY',array("list","AddPayment", ""),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSResellerCredit_Credit_ListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Reseller.Payment.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				//$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				//$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				//if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$SortStr="Order by Reseller_Payment_Id Desc";
				$Reseller_Id=Get_Input('GET','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(($Reseller_Id==$LReseller_Id)&&($LReseller_Id!=1))
					ExitError('!شما نمی توانید اطلاعات خود را ویرایش کرده و یا ببینید');
				ExitIfNotPermitRowAccess("reseller",$Reseller_Id);
			
				DSGridRender_Sql(100,"SELECT  Reseller_Payment_Id,{$DT}DateTimeStr(Reseller_PaymentCDT) as Reseller_PaymentCDT,PaymentType,Format(Price,$PriceFloatDigit) AS Price,".
					"Format(PayBalance,$PriceFloatDigit) AS PayBalance,VoucherNo,{$DT}DateStr(VoucherDate) as VoucherDate,BankBranchName,BankBranchNo,Comment ".
					"From Hreseller_payment where (Reseller_Id=$Reseller_Id) ".$sqlfilter." $SortStr ",
					"Reseller_Payment_Id",
					"Reseller_Payment_Id,Reseller_PaymentCDT,PaymentType,Price,PayBalance,VoucherNo,VoucherDate,BankBranchName,BankBranchNo,Comment",
					"","","");
       break;
    case "AddPayment":
				DSDebug(1,"DSResellerCredit_Credit_ListRender AddPayment ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Payment.AddPayment");				
				$NewRowInfo=array();
				$NewRowInfo['Reseller_Id']=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				if($NewRowInfo['Reseller_Id']==$LReseller_Id)
					ExitError('!شما نمی توانید اطلاعات خود را ویرایش کرده و یا ببینید');				
				ExitIfNotPermitRowAccess("reseller",$NewRowInfo['Reseller_Id']);				

				$NewRowInfo['PaymentType']=Get_Input('POST','DB','PaymentType','ARRAY',array('Cash','Cheque','Online','Pos','Deposit','Other'),0,0,0);
				$NewRowInfo['Price']=Get_Input('POST','DB','Price','PRC',1,14,0,0);
				$NewRowInfo['VoucherNo']=Get_Input('POST','DB','VoucherNo','STR',0,15,0,0);

				$NewRowInfo['VoucherDate']=Get_Input('POST','DB','VoucherDate','DateOrBlank',0,0,0,0);
				/*
				if($NewRowInfo['VoucherDate']<>''){
					if($DT=='sh')
						$VoucherDate=DBSelectAsString("Select shdatestrtomstr('".$NewRowInfo['VoucherDate']."')");
					else
						$VoucherDate=DBSelectAsString("Select DATE('".$NewRowInfo['VoucherDate']."')");
					if($VoucherDate=='')
						ExitError("Invalid VoucherDate".$NewRowInfo['VoucherDate']."->$VoucherDate");
						
				}
				*/
				$NewRowInfo['BankBranchName']=Get_Input('POST','DB','BankBranchName','STR',0,32,0,0);
				$NewRowInfo['BankBranchNo']=Get_Input('POST','DB','BankBranchNo','STR',0,32,0,0);
				$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,256,0,0);

				//----------------------
				$sql= "insert Hreseller_payment(Creator_Id,Reseller_PaymentCDT,Reseller_Id,PaymentType,Price,PayBalance,VoucherNo,VoucherDate,BankBranchName,BankBranchNo,Comment) ";
				$sql.="Select $LReseller_Id,Now(),'".$NewRowInfo['Reseller_Id']."','".$NewRowInfo['PaymentType']."','".$NewRowInfo['Price'];
				$sql.="',PayBalance+".$NewRowInfo['Price'].",'".$NewRowInfo['VoucherNo']."',";
				$sql.="'".$NewRowInfo['VoucherDate']."','".$NewRowInfo['BankBranchName']."','".$NewRowInfo['BankBranchNo']."','".$NewRowInfo['Comment']."'";
				$sql.=" From Hreseller_payment Where Reseller_Id=".$NewRowInfo['Reseller_Id']." order by  Reseller_Payment_Id Desc Limit 1";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$ar=$conn->sql->get_affected_rows();
				$RowInfo=LoadRowInfoSqlAsStr("Select Reseller_Payment_Id,{$DT}DateTimeStr(Reseller_PaymentCDT) as Reseller_PaymentCDT,PaymentType,".
											"Format(Price,$PriceFloatDigit) AS Price,Format(PayBalance,$PriceFloatDigit) AS PayBalance,VoucherNo,".
											"{$DT}DateStr(VoucherDate) as VoucherDate,BankBranchName,BankBranchNo,Comment ".
											"From Hreseller_payment Where Reseller_Payment_Id=$RowId");
				logdb("Edit","Reseller",$NewRowInfo['Reseller_Id'],"Payment",$RowInfo);
				echo "OK~";
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>