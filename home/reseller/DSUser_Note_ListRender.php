<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Note_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list","", "AddNote","DeleteNote","LoadEditNoteForm","EditNote","ViewNote",'Delete'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Note_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Note.List");			

				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				function color_rows($row){
				}
				
				DSGridRender_Sql(100,"SELECT  User_Note_Id,{$DT}DateTimeStr(CDT) As CDT,ResellerName as Creator,Note ".
					"From Huser_note u_p left join Hreseller r on u_p.Creator_Id=r.Reseller_Id ".
					"where (User_Id=$User_Id) ".$sqlfilter." $SortStr ",
					"User_Note_Id",
					"User_Note_Id,CDT,Creator,Note",
					"","","color_rows");
       break;
    case "AddNote":
				DSDebug(1,"DSUser_Note_ListRender AddNote ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Note.Add");
				$Note=Get_Input('POST','DB','Note','STR',1,2048,0,0);
				$sql= "insert Huser_note set CDT=Now()";
				$sql.=",User_Id='$User_Id'";
				$sql.=",Creator_Id=$LReseller_Id";
				$sql.=",Note='$Note'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_Note_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','User',$User_Id,'Note');
				echo "OK~$RowId~";
        break;
	case "Delete":
				DSDebug(1,"DSUser_Note_ListRender Delete ******************************************");
				$NewRowInfo=array();
				$NewRowInfo['User_Note_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString('Select User_Id From Huser_note Where User_Note_Id='.$NewRowInfo['User_Note_Id']);
				
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				if(ISPermit($Visp_Id,"Visp.User.Note.Delete")!==true){
					$Creator_Id=DBSelectAsString("select Creator_Id from Huser_note where User_Note_Id=".$NewRowInfo['User_Note_Id']);
					DSDebug(2,"Note Creaotr=$Creator_Id and Reseller_Id=$LReseller_Id");
					if($Creator_Id!=$LReseller_Id)
						exit("LimitCreator~");
					$Diff=DBSelectAsString("select TIMESTAMPDIFF(SECOND,CDT,Now()) from Huser_note where User_Note_Id=".$NewRowInfo['User_Note_Id']);
					DSDebug(2,"Difference between creation time and now=$Diff sec");
					if($Diff>600)
						exit("LimitTime~");
				}
				
				$n=DBDelete('delete from Huser_note Where User_Note_Id='.$NewRowInfo['User_Note_Id']);
				logdbdelete($NewRowInfo,'Delete','User',$User_Id,'Note');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>