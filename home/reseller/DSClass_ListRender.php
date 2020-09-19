<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSClass_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSClass_ListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.Class.List");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				DSGridRender_Sql(100,"SELECT Class_Id,ISEnable,ClassName From Hclass Where 1 ".$sqlfilter." $SortStr ",
					"Class_Id",
					"Class_Id,ISEnable,ClassName",
					"","","");
       break;

	   case "Delete":
				DSDebug(1,"DSClass_ListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.Class.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Class_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);

				DBDelete('delete from Hclass_reselleraccess Where Class_Id='.$NewRowInfo['Class_Id']);
				DBDelete('delete from Hclass_vispaccess Where Class_Id='.$NewRowInfo['Class_Id']);
				DBDelete('delete from Hservice_class Where Class_Id='.$NewRowInfo['Class_Id']);
				DBDelete('delete from Huser_class Where Class_Id='.$NewRowInfo['Class_Id']);

				DBDelete("delete from Hparam Where TableName='Class' and TableId=".$NewRowInfo['Class_Id']);
				 	
				$ar=DBDelete('delete from Hclass Where Class_Id='.$NewRowInfo['Class_Id']);
				logdbdelete($NewRowInfo,'Delete','Class',$NewRowInfo['Class_Id'],'');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>