<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_CallerId_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","insert","LoadCallerIdForm",'update','Delete','AutoYes','AutoNo','AutoAddState','Check'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_CallerId_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";

				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				DSGridRender_Sql(100,
					"User_CallerId_Id,CallerId from Huser_callerid ".
					"Where (User_Id=$User_Id)" .$sqlfilter." $SortStr ",
					"User_CallerId_Id",
					"User_CallerId_Id,CallerId",
					"","","");
       break;
    case "LoadCallerIdForm":
				DSDebug(1,"DSUser_CallerId_ListRender LoadCallerIdForm ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.List");
				$User_CallerId_Id=Get_Input('GET','DB','User_CallerId_Id','INT',1,4294967295,0,0);
				$sql="SELECT User_CallerId_Id,CallerId from Huser_callerid where (User_CallerId_Id=$User_CallerId_Id)";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				
				echo '</data>';
				
       break;
    case "insert": 
				DSDebug(1,"DSUser_CallerId_ListRender Insert ******************************************");
				$NewRowInfo=array();
				$NewRowInfo['User_Id']=Get_Input('GET','DB','User_Id','INT',0,4294967295,0,0);
				exitifnotpermituser($NewRowInfo['User_Id'],"Visp.User.CallerId.Add");
				$NewRowInfo['CallerId']=Get_Input('POST','DB','CallerId','STR',1,17,0,0);

				
				$sql= "insert Huser_callerid set ";
				$sql.="CallerId='".$NewRowInfo['CallerId']."',";
				$sql.="User_Id='".$NewRowInfo['User_Id']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_CallerId_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','User',$NewRowInfo['User_Id'],'CallerId');
				echo "OK~$RowId~";
        break;
    case "update": 
				DSDebug(1,"DSUser_CallerId_ListRender Update ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.Edit");
				$NewRowInfo=array();
				$User_CallerId_Id=Get_Input('POST','DB','User_CallerId_Id','INT',1,4294967295,0,0);
				$NewRowInfo['CallerId']=Get_Input('POST','DB','CallerId','STR',1,17,0,0);

				$OldRowInfo= LoadRowInfoSql("SELECT CallerId from Huser_callerid where (User_CallerId_Id=$User_CallerId_Id)");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "update Huser_callerid set ";
				$sql.="CallerId='".$NewRowInfo['CallerId']."'";
				$sql.=" Where ";
				$sql.="(User_CallerId_Id=$User_CallerId_Id)";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				/*
				if($ar!=1){//probably hack
					logdb('Edit','Service',$NewRowInfo['Service_Id'],'Param',"Update Fail,Table=Param affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Param affected row=0");
					ExitError("(ar=$ar) Security problem, Report Sent to Administrator");	
				}
					*/
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'User',$User_Id,'CallerId')){
					logunfair("UnFair",'User',$User_Id,'CallerId','');
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
		
	case "Delete":
				DSDebug(1,"DSUser_CallerId_ListRender Delete ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.Delete");
				$NewRowInfo=array();
				$NewRowInfo['User_CallerId_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_callerid where User_CallerId_Id=".$NewRowInfo['User_CallerId_Id']);
				$ar=DBDelete('delete from Huser_callerid Where User_CallerId_Id='.$NewRowInfo['User_CallerId_Id']);
				logdbdelete($NewRowInfo,'Delete','User',$User_Id,'CallerId');
				echo "OK~";
		break;
	case "AutoYes":
				DSDebug(1,"DSUser_CallerId_ListRender AutoYes ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.AutoAdd");
				$NewRowInfo=array();
				$NewRowInfo['User_Id']=Get_Input('GET','DB','User_Id','INT',0,4294967295,0,0);
				DBUpdate("Update Huser Set AutoAddCallerId='Yes' Where User_Id=".$NewRowInfo['User_Id']);
				logdb("Edit","User",$User_Id,"CallerId",'Set AutoAddCallerId to Yes');
				echo "OK~";
		break;
	case "AutoNo":
				DSDebug(1,"DSUser_CallerId_ListRender AutoNo ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.AutoAdd");
				$NewRowInfo=array();
				$NewRowInfo['User_Id']=Get_Input('GET','DB','User_Id','INT',0,4294967295,0,0);
				$res=DBUpdate("Update Huser Set AutoAddCallerId='No' Where User_Id=".$NewRowInfo['User_Id']);
				logdb("Edit","User",$User_Id,"CallerId",'Set AutoAddCallerId to 3No');
				echo "OK~";
		break;
	case "AutoAddState"	:
				DSDebug(1,"DSUser_CallerId_ListRender AutoNo ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.AutoAdd");
				$AutoAddState=DBSelectAsString("select AutoAddCallerId from Huser where User_Id=$User_Id");
				echo "OK~Auto$AutoAddState";
		break;
	case "Check":
				DSDebug(1,"DSUser_CallerId_ListRender Check ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CallerId.Check");
				
				$User_CallerId_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$CallerId=DBSelectAsString("Select CallerId from Huser_callerid where User_CallerId_Id=$User_CallerId_Id");
				
				if($LReseller_Id==1)
					$sql="select concat(Count(1),' User(s):<br/>',group_concat(Username separator '<br/>')) from Huser_callerid c join Huser u on c.User_Id=u.User_Id where c.CallerId='$CallerId'";
				else{
					$VispUserList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$VispUserCallerIdList=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.CallerId.List'");
					$sql="select concat(Count(1),' User(s):[<br/>',group_concat(Username separator '<br/>'),']') from Huser_callerid c join Huser Hu on c.User_Id=Hu.User_Id ".
					"join Hreseller_permit Hrp1 on Hrp1.Reseller_Id=$LReseller_Id and Hrp1.Visp_Id=Hu.Visp_Id and Hrp1.PermitItem_Id=$VispUserList and Hrp1.ISPermit='Yes' ".
					"join Hreseller_permit Hrp2 on Hrp2.Reseller_Id=$LReseller_Id and Hrp2.Visp_Id=Hu.Visp_Id and Hrp2.PermitItem_Id=$VispUserCallerIdList and Hrp2.ISPermit='Yes' ".
					"where c.CallerId='$CallerId'";
				}
				
				$Res=DBSelectAsString($sql);
				echo "OK~$Res";				
				
				
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>