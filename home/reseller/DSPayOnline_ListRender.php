<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSPayOnlineListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", ""),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSPayOnlineListRender->List ********************************************");
				exitifnotpermit(0,"Admin.PayOnline.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT PayOnline_Id,{$DT}DateTimeStr(CDT) as CDT,ResellerName,u.Username,OrderId,RequestType,ReferenceId,Format(Credit,$PriceFloatDigit) as Credit,Format(Price,$PriceFloatDigit) as Price,Format(RequestedSavingOff,$PriceFloatDigit) as RequestedSavingOff,TerminalName,Status,CardHolderPan,ZarinPal_Authority,LastError,Param ".
					"From Hpayonline po Left join Hreseller r on po.Reseller_Id=r.Reseller_Id ".
					" Left join Hterminal t on po.Terminal_Id=t.Terminal_Id ".
					" Left join Huser u on po.User_Id=u.User_Id ".
					" Where 1 ".$sqlfilter." $SortStr ",
					"PayOnline_Id","PayOnline_Id,CDT,ResellerName,Username,OrderId,RequestType,ReferenceId,Credit,Price,RequestedSavingOff,TerminalName,Status,CardHolderPan,ZarinPal_Authority,LastError,Param",
					"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>

