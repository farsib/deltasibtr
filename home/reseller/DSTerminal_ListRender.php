<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSTerminalListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSTerminalListRender->List ********************************************");
				exitifnotpermit(0,"Admin.BankTerminal.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT Terminal_Id,ISEnable,TerminalName,BankName From Hterminal Where 1 ".$sqlfilter." $SortStr ",
					"Terminal_Id","Terminal_Id,ISEnable,TerminalName,BankName",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSTerminalListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.BankTerminal.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Terminal_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$n=DBSelectAsString('Select Terminal_Id From Hpayonline Where Terminal_Id='.$NewRowInfo['Terminal_Id']." And LastError='' Limit 1");
				if($n>0)
					ExitError("این ترمینال درحال استفاده است و قابل حذف نیست");
				$ar=DBDelete('delete from Hreseller_terminalaccess Where Terminal_Id='.$NewRowInfo['Terminal_Id']);
				$ar=DBDelete('delete from Hterminal Where Terminal_Id='.$NewRowInfo['Terminal_Id']);
				$ar=DBDelete('delete from Hsaderatkeys Where SaderatTerminal_Id='.$NewRowInfo['Terminal_Id']);
				if($ar>0){
					$Res=runshellcommand("php","DSCreateSaderatKeys","","");
					DSDebug(1,"DSCreateSaderatKeys->Reply [$Res]");
				}
				logdbdelete($NewRowInfo,'Delete','Terminal',$NewRowInfo['Terminal_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>