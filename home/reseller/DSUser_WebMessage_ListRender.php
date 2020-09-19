<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_WebMessage_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list","Add","Delete"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_WebMessage_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.WebMessage.List");			

				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				function color_rows($row){
					if($row->get_value("WebMessageStatus")=='Unread')
						$row->set_row_style("color:blue;font-weight:bold");
					else
						$row->set_row_style("color:graytext;");	
				}
				
				DSGridRender_Sql(100,"SELECT  User_WebMessage_Id,ResellerName as Creator,{$DT}DateTimeStr(CDT) As CDT,WebMessageTitle,WebMessageStatus ".
					"From Huser_webmessage u_w left join Hreseller r on u_w.Creator_Id=r.Reseller_Id ".
					"where (User_Id=$User_Id) ".$sqlfilter." $SortStr ",
					"User_WebMessage_Id",
					"User_WebMessage_Id,Creator,CDT,WebMessageTitle,WebMessageStatus",
					"","","color_rows");
       break;
	case "Delete":
				DSDebug(1,"DSUser_WebMessage_ListRender Delete ******************************************");
				$User_WebMessage_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id From Huser_webmessage Where User_WebMessage_Id='$User_WebMessage_Id'");
				
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				if(ISPermit($Visp_Id,"Visp.User.WebMessage.Delete")!==true){
					$Creator_Id=DBSelectAsString("select Creator_Id from Huser_webmessage where User_WebMessage_Id='$User_WebMessage_Id'");
					DSDebug(2,"WebMessage Creaotr=$Creator_Id and Reseller_Id=$LReseller_Id");
					if($Creator_Id!=$LReseller_Id)
						exit("LimitCreator~");
					$Diff=DBSelectAsString("select TIMESTAMPDIFF(SECOND,CDT,Now()) from Huser_webmessage where User_WebMessage_Id='$User_WebMessage_Id'");
					DSDebug(2,"Difference between creation time and now=$Diff sec");
					if($Diff>600)
						exit("LimitTime~");
				}
				
				$tmp=array();
				$sql="select User_WebMessage_Id,WebMessageTitle from Huser_webmessage where User_WebMessage_Id='$User_WebMessage_Id'";
				$n=CopyTableToArray($tmp,$sql);
				$n=DBDelete("delete from Huser_webmessage Where User_WebMessage_Id='$User_WebMessage_Id'");
				logdbdelete($tmp[0],'Delete','User',$User_Id,'WebMessage');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>