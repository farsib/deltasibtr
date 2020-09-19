<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_RadiusLog_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","AddPayment",'AddCallerId'),0,0,0);


switch ($act) {
    case "list":
				DSDebug(1,"DSUser_RadiusLog_ListRender List ******************************************");
				//Permission -----------------
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.RadiusLog.List");
				
				
				function color_rows($row){
					global $CurrentDate;
					$LogType = $row->get_value("LogType");
					if((stripos($LogType,'Fail')!==false)||(stripos($LogType,'Block')!==false))
						$row->set_row_style("color:red");
					elseif(stripos($LogType,'OK')!==false)
						$row->set_row_style("color:green");
					else
						$row->set_row_style("color:Black");
					$row->set_value("Comment",htmlspecialchars($row->get_value("Comment")));
				}
				
				DSGridRender_Sql(-1,"call ListLogUser($User_Id,'$DT') ","Id","Id,CDT,LogType,CallingStationId,NasName,Comment","","","color_rows");		
		break;
	case "AddCallerId":
				DSDebug(1,"DSUser_Connection_ListRender AddCallerId ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.RadiusLog.AddCallerId");
				
				$NewRowInfo=array();
				
				$NewRowInfo['CallerId']=Get_Input('POST','DB','CallerId','STR',1,17,0,0);
				$sql= "insert Huser_callerid set ";
				$sql.="User_Id='$User_Id',";
				$sql.="CallerId='".$NewRowInfo['CallerId']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_CallerId_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','User',$User_Id,'CallerId');
				echo "OK~";
		break;		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>