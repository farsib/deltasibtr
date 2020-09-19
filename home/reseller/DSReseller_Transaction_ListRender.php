<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSResellerCredit_Transaction_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list", ""),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSResellerCredit_Transaction_ListRender->List ********************************************");
				exitifnotpermit(0,"CRM.Reseller.Transaction.List");
				$Reseller_Id=Get_Input('GET','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(($Reseller_Id==$LReseller_Id)&&($LReseller_Id!=1))
					ExitError('You can not Edit or View Your Info!!!');
				ExitIfNotPermitRowAccess("reseller",$Reseller_Id);
				/*
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				*/
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$SortStr="Order by Reseller_Transaction_Id desc";
				function color_rows($row){
					
					$data = $row->get_value("TransactionType");
					if($data=='Transfer')
						$row->set_row_style("color:orange");
					else if($data=='BuyService')
						$row->set_row_style("color:black");
					else if($data=='Initial')
						$row->set_row_style("color:black");
				}
				
				
				DSGridRender_Sql(100,"SELECT Reseller_Transaction_Id,cr.ResellerName As Creator,rr.ResellerName As ResellerName,rr_r.ResellerName As Relate_ResellerName, ".
								"{$DT}DateTimeStr(Reseller_TransactionCDT) as CDT, ".
								"TransactionType,Format(Credit,$PriceFloatDigit) AS Credit,Format(r_t.CreditBalance,$PriceFloatDigit) AS CreditBalance, ".
								"UserName".
								" from  Hreseller_transaction r_t ". 
								"Left Join Hreseller cr on r_t.Creator_Id=cr.Reseller_Id ".
								"Left Join Hreseller rr on r_t.Reseller_Id=rr.Reseller_Id ".
								"Left Join Hreseller rr_r on r_t.Relate_Reseller_Id=rr_r.Reseller_Id ".
								"Left Join Huser u On r_t.User_Id=u.User_Id ".
								"where (r_t.Reseller_Id=$Reseller_Id) ".$sqlfilter." $SortStr ",
					"Reseller_Transaction_Id",
					"Reseller_Transaction_Id,Creator,CDT,TransactionType,ResellerName,Relate_ResellerName,Credit,CreditBalance,UserName",
					"","","color_rows");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>