<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSService_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

if($LReseller_Id!=1) ExitError('Only Admin can view batch process history');

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSBatchProcessHistory_EditRender->List ********************************************");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);				
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$RowId=Get_Input('GET','DB','RowId','INT',1,4294967295,0,0);				
				
				$sql="select User_Index,b.User_Id,BatchItemState,SHDATETIMESTR(BatchItemDT) as BatchItemDT,if(Username is null,'`Deleted User`',Username) as Username,Name,Family,BatchItemComment from Hbatchprocess_users b left join ".
					"Huser u on b.User_Id=u.User_Id where BatchProcess_Id=$RowId $SortStr";
					
				$Fields="User_Index,User_Id,BatchItemState,BatchItemDT,Username,Name,Family,BatchItemComment";
				function color_rows($row){
					$data = $row->get_value("BatchItemState");					
					if($data=='Pending')
						$s="color:royalblue;";
					else if($data=='CanceledBeforeStart')
						$s="color:gray;";
					elseif($data=='InProgress')
						$s="color:green;";
					else if($data=='CanceledInProgress')
						$s="color:brown;";
					else if($data=='Paused')
						$s="color:black;";
					else if($data=='Done')
						$s="color:blue;";
					else if($data=='Fail')
						$s="color:indianred;";
					else
						$s="";
					$data = $row->get_value("Username");
					if($data=='`Deleted User`')//no real username can have back quote ` character. So this comparison is accurate
						$s.="font-style:oblique;font-weight:bold;";
					$row->set_row_style($s);
				}

				DSGridRender_Sql(100,$sql,"User_Index",$Fields,"","","color_rows");
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>