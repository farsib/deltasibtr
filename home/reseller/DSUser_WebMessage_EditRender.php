<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSUser_WebMessage_Edit.php ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSUser_WebMessage_Edit.php Load ********************************************");
				
				$User_WebMessage_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				
				$User_Id=DBSelectAsString("Select User_Id from Huser_webmessage where User_WebMessage_Id='$User_WebMessage_Id'");
				exitifnotpermituser($User_Id,"Visp.User.WebMessage.View");
				
				$sql="SELECT '' As Error,User_WebMessage_Id,WebMessageStatus,WebMessageTitle,WebMessageBody ".
					"from Huser_webmessage where User_WebMessage_Id='$User_WebMessage_Id'";
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
				DSDebug(1,"DSUser_WebMessage_Edit.php Insert ******************************************");
				
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);;
				exitifnotpermituser($User_Id,"Visp.User.WebMessage.Add");
				
				$NewRowInfo=array();
								
				$NewRowInfo['WebMessageTitle']=Get_Input('POST','DB','WebMessageTitle','STR',1,64,0,0);
				$ReplaceCR=Get_Input('POST','DB','ReplaceCR','INT',0,1,0,0);
				$NewRowInfo['WebMessageBody']=Get_Input('POST','DB','WebMessageBody','STR',1,2048,0,0);
				$NewRowInfo['WebMessageBody']=trim($NewRowInfo['WebMessageBody']);
				if($ReplaceCR==1)
					$NewRowInfo['WebMessageBody']=trim(str_replace("\\n","<br/>",$NewRowInfo['WebMessageBody']));
				
				//----------------------
				$sql= "insert Huser_webmessage set ";
				$sql.="User_Id='$User_Id',";
				$sql.="Creator_Id='$LReseller_Id',";
				$sql.="CDT=NOW(),";
				$sql.="WebMessageTitle='".$NewRowInfo['WebMessageTitle']."',";
				$sql.="WebMessageBody='".$NewRowInfo['WebMessageBody']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_WebMessage_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','User',$User_Id,'WebMessage');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSUser_WebMessage_Edit.php Update ******************************************");
				
				$NewRowInfo=array();
				$NewRowInfo['User_WebMessage_Id']=Get_Input('POST','DB','User_WebMessage_Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_webmessage where User_WebMessage_Id='".$NewRowInfo['User_WebMessage_Id']."'");
				exitifnotpermituser($User_Id,"Visp.User.WebMessage.Edit");
				
				$NewRowInfo['WebMessageTitle']=Get_Input('POST','DB','WebMessageTitle','STR',1,64,0,0);
				
				$ReplaceCR=Get_Input('POST','DB','ReplaceCR','INT',0,1,0,0);
				$NewRowInfo['WebMessageBody']=Get_Input('POST','DB','WebMessageBody','STR',1,2048,0,0);
				$NewRowInfo['WebMessageBody']=trim($NewRowInfo['WebMessageBody']);
				if($ReplaceCR==1)
					$NewRowInfo['WebMessageBody']=trim(str_replace("\\n","<br/>",$NewRowInfo['WebMessageBody']));
				
				$OldRowInfo= LoadRowInfo("Huser_webmessage","User_WebMessage_Id='".$NewRowInfo['User_WebMessage_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Huser_webmessage set ";
				$sql.="WebMessageTitle='".$NewRowInfo['WebMessageTitle']."',";
				$sql.="WebMessageBody='".$NewRowInfo['WebMessageBody']."'";
				$sql.=" Where ";
				$sql.="(User_WebMessage_Id='".$NewRowInfo['User_WebMessage_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','User',$User_Id,'WebMessage',"Update Fail,Table=WebMessage affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=WebMessage affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'User',$User_Id,'WebMessage')){
					logunfair("UnFair",'User',$User_Id,'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
