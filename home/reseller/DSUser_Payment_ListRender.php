<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUserCredit_Credit_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","AddPayment","GetUserPayBalance"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUserCredit_Credit_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				//$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				//$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				//if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				function color_rows($row){
					//if ($row->get_index()%3)
					//$row->set_row_color("red");
					
					$data = $row->get_value("Price");
					
					if($data<0)
						$row->set_row_style("color:red");
				}
				
				$SortStr="Order by User_Payment_Id Desc";
				
				
			
				DSGridRender_Sql(100,"SELECT  User_Payment_Id,FindResellerName(Creator_Id) As Creator,{$DT}DateTimeStr(User_PaymentCDT) as User_PaymentCDT,PaymentType,Format(Price,$PriceFloatDigit) AS Price,".
					"Format(u_p.PayBalance,$PriceFloatDigit) AS PayBalance,VoucherNo,{$DT}DateStr(VoucherDate) as VoucherDate,BankBranchName,BankBranchNo, ".
					"FindResellerName(Charger_Id) As Charger,ChargerCommission,FindResellerName(Supporter_Id) as Supporter,SupporterCommission, ".
					"FindResellerName(Reseller_Id) as Reseller,ResellerCommission,Comment ".
					"From Huser_payment u_p ".
					"where (User_Id=$User_Id) ".$sqlfilter." $SortStr ",
					"User_Payment_Id",
					"User_Payment_Id,Creator,User_PaymentCDT,PaymentType,Price,PayBalance,VoucherNo,VoucherDate,BankBranchName,BankBranchNo,Charger,ChargerCommission,Supporter,SupporterCommission,Reseller,ResellerCommission,Comment",
					"","","color_rows");
       break;
    case "AddPayment":
				DSDebug(1,"DSUserCredit_Credit_ListRender AddPayment ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.List");

				$PaymentType=Get_Input('POST','DB','PaymentType','ARRAY',array('Cash','Cheque','Pos','Deposit','Other','TAX','Off'),0,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.PaymentType.".$PaymentType);
				$Price=Get_Input('POST','DB','Price','PRC',1,14,0,0);
				if($Price<=0){
					ExitError("مبلغ باید بیشتر از ۰ باشد");
				}
				$Direction=Get_Input('POST','DB','Direction','ARRAY',array('GetMoney','RefundMoney'),0,0,0);					
				exitifnotpermituser($User_Id,"Visp.User.Payment.Add.".$Direction);//GetMoney or RefundMoney
				if($Direction=='RefundMoney'){//check if user have credit
					//reseller want to pay money to User, check if User have money
					$Paybalance=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc limit 1");
					if(($Price>$Paybalance)&&($LReseller_Id!=1))
						ExitError("تراز مالی کاربر عدد زیر است ولی شما تلاش می کنید مبلغ بیشتری به کاربر برگشت دهید</br>$Paybalance");
					$Price=-$Price;
				}
				else{//check if reseller have enough money
					if($LReseller_Id!=1){
						$FromResellerCreditBalance=DBSelectAsString("Select CreditBalance From Hreseller_transaction Where Reseller_Id=$LReseller_Id Order by reseller_transaction_Id  desc Limit 1");
						if($FromResellerCreditBalance=='')$FromResellerCreditBalance=0;
						if($FromResellerCreditBalance<$Price)
							ExitError("شما اعتبار کافی ندارید(تراز اعتبار  $FromResellerCreditBalance<".$Price.")");				
					}
				
					
				}
				$VoucherNo=Get_Input('POST','DB','VoucherNo','STR',0,15,0,0);
				$VoucherDate=Get_Input('POST','DB','VoucherDate','DateOrBlank',0,0,0,0);
				$BankBranchName=Get_Input('POST','DB','BankBranchName','STR',0,32,0,0);
				$BankBranchNo=Get_Input('POST','DB','BankBranchNo','STR',0,32,0,0);
				$Comment=Get_Input('POST','DB','Comment','STR',0,256,0,0);


				
				//-----------------------------------

				if($Price>=0)
					AddResellerTransaction($LReseller_Id,0,$User_Id,'GetMoney',(-1)*$Price);
				else
					AddResellerTransaction($LReseller_Id,0,$User_Id,'RefundMoney',(-1)*$Price);
				
				$RowId=AddPaymentToUser($LReseller_Id,$User_Id,$PaymentType,$Price,$VoucherNo,$VoucherDate,$BankBranchName,$BankBranchNo,$Comment);
				$RowInfo=LoadRowInfoSqlAsStr("Select User_Payment_Id,{$DT}DateTimeStr(User_PaymentCDT) as User_PaymentCDT,PaymentType,".
											"Format(Price,$PriceFloatDigit) AS Price,Format(PayBalance,$PriceFloatDigit) AS PayBalance,VoucherNo,".
											"{$DT}DateStr(VoucherDate) as VoucherDate,BankBranchName,BankBranchNo ".
											"From Huser_payment Where User_Payment_Id=$RowId");
				
				logdb("Edit","User",$User_Id,"Payment",$RowInfo);

				echo "OK~";
        break;
	case "GetUserPayBalance":
				DSDebug(0,"DSUserCredit_Credit_ListRender->GetUserPayBalance ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.List");
				$PayBalance=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc limit 1");
				echo "OK~".number_format($PayBalance, $PriceFloatDigit, '.', '');
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>