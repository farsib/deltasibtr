<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSReseller_UserNote_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"CRM.UserNote.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list","ViewAttachment"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_UserNote_ListRender->List ********************************************");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by User_note_Id Desc";
				
				$SelectStr="Hun.User_note_Id,Hr.ResellerName As Creator,SHDateTimeStr(CDT) as User_NoteCDT,Hu.UserName,Note,Hu.Visp_Id as Visp_Id,Hun.Creator_Id as Creator_Id";
				
				$sql="CREATE or REPLACE VIEW UserNoteTemp as select $SelectStr from Huser_note Hun ".
					"left join Huser Hu on Hun.User_Id=Hu.User_id ".
					"left join Hreseller Hr on Hun.Creator_Id=Hr.Reseller_Id";
				DBUpdate($sql);
				
				$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
				$VispUserNoteList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Note.List'");					
				
				$ColumnStr="User_note_Id,Creator,User_NoteCDT,UserName,Note";
				
				$sql="select $ColumnStr from UserNoteTemp UNT ".
					(($LReseller_Id!=1)?"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=UNT.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
					"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=UNT.Visp_Id and Hrp2.PermitItem_Id=$VispUserNoteList and Hrp2.ISPermit='Yes' ":"").
					"where UNT.Creator_Id=$LReseller_Id  ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"User_note_Id",$ColumnStr,"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
