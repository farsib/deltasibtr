<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSResellerCredit_Credit_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","TransferCredit", ""),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSResellerCredit_Credit_ListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Reseller.Credit.List");
				
				$Reseller_Id=Get_Input('GET','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(($Reseller_Id==$LReseller_Id)&&($LReseller_Id!=1))
					ExitError('!نمی توانید اطلاعات خودتان را ویرایش کرده و یا ببینید');
				
				ExitIfNotPermitRowAccess("reseller",$Reseller_Id);
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				if($SortField=='FromResellerName') $SortField='FResellerName';
				if($SortField=='ToResellerName') $SortField='TResellerName';
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,"SELECT  Reseller_Credit_Id,{$DT}DateTimeStr(Reseller_CreditCDT) as CDT,CreditType,fr.ResellerName as  FResellerName ".
					",tr.ResellerName as TResellerName,Format(Credit,$PriceFloatDigit) AS Credit,Format(Price,$PriceFloatDigit) As Price,Comment  ". 
					"From Hreseller_credit rc left join Hreseller fr on (rc.From_Reseller_Id=fr.Reseller_Id) ". 
					"left join Hreseller tr on (rc.To_Reseller_Id=tr.Reseller_Id) where (From_Reseller_Id=$Reseller_Id)Or(To_Reseller_Id=$Reseller_Id) ".$sqlfilter." $SortStr ",
					"Reseller_Credit_Id",
					"Reseller_Credit_Id,CDT,CreditType,FResellerName,TResellerName,Credit,Price,Comment",
					"","","");
       break;
    case "TransferCredit":
				DSDebug(1,"DSResellerCredit_Credit_ListRender TransferCredit ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Credit.TransferCredit");
				$From_Reseller_Id=$LReseller_Id;
				$To_Reseller_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				if($From_Reseller_Id==$To_Reseller_Id)
						ExitError('!نمی توانید اطلاعات خودتان را ویرایش کرده و یا ببینید');
				ExitIfNotPermitRowAccess('reseller',$To_Reseller_Id);

				
				$Credit=floatval(Get_Input('POST','DB','Credit','PRC',1,14,0,0));
				
				//Reseller have enough credit
				if($Credit>0){// Check From Reseller
					if($From_Reseller_Id!=1){
						$FromResellerCreditBalance=DBSelectAsString("Select CreditBalance From Hreseller_transaction Where Reseller_Id=$From_Reseller_Id Order by reseller_transaction_Id  desc Limit 1");
						if($FromResellerCreditBalance=='')$FromResellerCreditBalance=0;
						if($FromResellerCreditBalance<$Credit){
							ExitError("تراز اعتبار از نماینده فروش،اعتبار کافی ندارد(تراز اعتبار  $FromResellerCreditBalance<$Credit)");
						}
					}
				}
				else if($Credit<0){//Check To Reseller
						if($From_Reseller_Id!=1)
							ExitError("اعتبار باید بزرگتر از ۰ باشد");
						
						$ToResellerCreditBalance=DBSelectAsString("Select CreditBalance From Hreseller_transaction Where Reseller_Id=$To_Reseller_Id Order by reseller_transaction_Id  desc Limit 1");
						if($ToResellerCreditBalance=='')$ToResellerCreditBalance=0;
						if($ToResellerCreditBalance<-$Credit){
							
							ExitError("تراز اعتبار به نماینده فروش،اعتبار کافی ندارد(تراز اعتبار  $ToResellerCreditBalance<".-$Credit.")");
						}
				}
				
				$Price=Get_Input('POST','DB','Price','PRC',1,14,0,0);
				$Comment=Get_Input('POST','DB','Comment','STR',0,128,0,0);

				//----------------------
				$sql= "insert Hreseller_credit set Reseller_CreditCDT=Now(),";
				$sql.="Creator_Id=$LReseller_Id,";
				$sql.="From_Reseller_Id=$From_Reseller_Id,";
				$sql.="To_Reseller_Id=$To_Reseller_Id,";
				$sql.="Credit=$Credit,";
				$sql.="Price='$Price',";
				$sql.="Comment='$Comment'";

				$RowId=DBInsert($sql);
				
				$RowInfo=LoadRowInfoSqlAsStr("Select CreditType,fr.ResellerName as  FResellerName,tr.ResellerName as TResellerName,Format(Credit,$PriceFloatDigit) AS Credit,Format(Price,$PriceFloatDigit) As Price,Comment  From Hreseller_credit rc left join Hreseller fr on (rc.From_Reseller_Id=fr.Reseller_Id) left join Hreseller tr on (rc.To_Reseller_Id=tr.Reseller_Id) Where Reseller_Credit_Id=$RowId");
				logdb("Edit","Reseller",$To_Reseller_Id,"Credit",$RowInfo);
				
				
				//Update Receiver Credit Transaction
				AddResellerTransaction($To_Reseller_Id,$From_Reseller_Id,0,'CreditGet',$Credit);

				//Update Sender Credit Transaction
				AddResellerTransaction($From_Reseller_Id,$To_Reseller_Id,0,'CreditSend',-$Credit);
				//Update Receiver Credit Payment
				DBInsert("Insert Hreseller_payment(Creator_Id,Reseller_Id,Reseller_PaymentCDT,PaymentType,Price,PayBalance,Comment)".
						"select $LReseller_Id,$To_Reseller_Id,Now(),'Transfer',-1*($Price),PayBalance-1*($Price),'$RowInfo' From Hreseller_payment ".
						"Where Reseller_Id=$To_Reseller_Id Order by Reseller_Payment_Id desc Limit 1");

				echo "OK~";
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>