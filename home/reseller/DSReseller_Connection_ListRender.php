<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSResellerCredit_Transaction_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission
if($LResellerName!='admin') 	ExitError('خطا، فقط ادمین میتواند گزارش را مشاهده کند');


$act=Get_Input('GET','DB','act','ARRAY',array("list", ""),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSResellerCredit_Transaction_ListRender->List ********************************************");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				Reseller_WebConnection_Id,Reseller_Id,ClientIP,StartDT,EndDT,BrowserInfo
				DSGridRender_Sql(100,"SELECT Reseller_Transaction_Id,{$DT}DateTimeStr(Reseller_TransactionCDT) as CDT,UserName,ResellerName As RelateResellerName,TransactionType,".
								"Format(Credit,$PriceFloatDigit) AS Credit,Format(r_t.CreditBalance,$PriceFloatDigit) AS CreditBalance,r_t.Comment".
								" from  Hreseller_transaction r_t Left Join Hreseller r on Relate_Reseller_Id=r.Reseller_Id Left Join Huser u On r_t.User_Id=u.User_Id ".
								"where (r_t.Reseller_Id=$Reseller_Id) ".$sqlfilter." $SortStr ",
					"Reseller_Transaction_Id",
					"Reseller_Transaction_Id,CDT,UserName,RelateResellerName,TransactionType,Credit,CreditBalance,Comment",
					"","","color_rows");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>