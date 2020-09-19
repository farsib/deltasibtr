<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_PayOnline_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_PayOnline_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.PayOnline.List");			

				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT PayOnline_Id,{$DT}DateTimeStr(CDT) as CDT,OrderId,RequestType,ServiceName,{$DT}DateStr(EndDate) as EndDate,ReferenceId,Format(po.Price,$PriceFloatDigit) as Price,Format(RequestedSavingOff,$PriceFloatDigit) as RequestedSavingOff,TerminalName,Status,CardHolderPan,LastError,Param ".
					"From Hpayonline po ".
					"Left join Hterminal t on po.Terminal_Id=t.Terminal_Id ".
					"Left join Hservice s on po.Service_Id=s.Service_Id ".
					"Where po.User_Id='$User_Id' ".$sqlfilter." $SortStr ",
					"PayOnline_Id","PayOnline_Id,CDT,OrderId,RequestType,ServiceName,EndDate,ReferenceId,Price,RequestedSavingOff,TerminalName,Status,CardHolderPan,LastError,Param",
					"","","");
					
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>