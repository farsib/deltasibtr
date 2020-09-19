<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSCallerIdBlock_ListRender.php ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","insert",'Delete'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSCallerIdBlock_ListRender.php->List ********************************************");
				exitifnotpermit(0,"Admin.CallerIdBlock.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"CallerIdBlock_Id,CallerId from Hcalleridblock ".
					"Where 1 ".$sqlfilter." $SortStr ",
					"CallerIdBlock_Id",
					"CallerIdBlock_Id,CallerId",
					"","","");
       break;
    case "insert": 
				DSDebug(1,"DSCallerIdBlock_ListRender.php Insert ******************************************");
				exitifnotpermit(0,"Admin.CallerIdBlock.Add");
				
				$NewRowInfo=array();
				$NewRowInfo['CallerId']=Get_Input('POST','DB','CallerId','STR',1,17,0,0);

				
				$sql= "insert Hcalleridblock set ";
				$sql.="CallerId='".$NewRowInfo['CallerId']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['CallerIdBlock_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','BlockCallerId',$NewRowInfo['CallerIdBlock_Id'],'');
				echo "OK~$RowId~";
        break;
	case "Delete":
				DSDebug(1,"DSCallerIdBlock_ListRender.php Delete ******************************************");
				exitifnotpermit(0,"Admin.CallerIdBlock.Delete");
				$NewRowInfo=array();
				$NewRowInfo['CallerIdBlock_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$NewRowInfo['CallerId']=DBSelectAsString('Select CallerId from Hcalleridblock where CallerIdBlock_Id='.$NewRowInfo['CallerIdBlock_Id']);
				$ar=DBDelete('delete from Hcalleridblock Where CallerIdBlock_Id='.$NewRowInfo['CallerIdBlock_Id']);
				logdbdelete($NewRowInfo,'Delete','BlockCallerId',$NewRowInfo['CallerIdBlock_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>