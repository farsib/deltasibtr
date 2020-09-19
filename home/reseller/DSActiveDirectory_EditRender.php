<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSActiveDirectoryEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array('TestAuth',"load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSActiveDirectoryEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.ActiveDirectory.View");
				$ActiveDirectory_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,ActiveDirectory_Id,ISEnable,ActiveDirectoryName,INET_NTOA(IP) As IP,Domain,GroupName,Timeout ".
					"from Hactivedirectory where ActiveDirectory_Id='$ActiveDirectory_Id'";
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
				DSDebug(1,"DSActiveDirectoryEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.ActiveDirectory.Add");
				$NewRowInfo=array();
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ActiveDirectoryName']=Get_Input('POST','DB','ActiveDirectoryName','STR',1,32,0,0);
				$NewRowInfo['IP']=Get_Input('POST','DB','IP','STR',1,15,0,0);
				$NewRowInfo['GroupName']=(Get_Input('POST','DB','GroupName','STR',0,64,0,0));
				$NewRowInfo['Domain']=(Get_Input('POST','DB','Domain','STR',1,32,0,0));
				$NewRowInfo['Timeout']=trim(Get_Input('POST','DB','Timeout','INT',1,5,0,0));
				//----------------------
				$sql= "insert Hactivedirectory set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="ActiveDirectoryName='".$NewRowInfo['ActiveDirectoryName']."',";
				$sql.="IP=INET_ATON('".$NewRowInfo['IP']."'),";
				$sql.="GroupName='".$NewRowInfo['GroupName']."',";
				$sql.="Domain='".$NewRowInfo['Domain']."',";
				$sql.="Timeout='".$NewRowInfo['Timeout']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['ActiveDirectory_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','ActiveDirectory',$RowId,'ActiveDirectory');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSActiveDirectoryEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.ActiveDirectory.Edit");
				$NewRowInfo=array();
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ActiveDirectory_Id']=Get_Input('POST','DB','ActiveDirectory_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ActiveDirectoryName']=Get_Input('POST','DB','ActiveDirectoryName','STR',1,32,0,0);
				$NewRowInfo['IP']=Get_Input('POST','DB','IP','STR',1,15,0,0);
				$NewRowInfo['GroupName']=trim(Get_Input('POST','DB','GroupName','STR',0,64,0,0));
				$NewRowInfo['Domain']=trim(Get_Input('POST','DB','Domain','STR',1,32,0,0));
				$NewRowInfo['Timeout']=trim(Get_Input('POST','DB','Timeout','INT',1,5,0,0));

				$OldRowInfo= LoadRowInfo("Hactivedirectory","ActiveDirectory_Id='".$NewRowInfo['ActiveDirectory_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hactivedirectory set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="ActiveDirectoryName='".$NewRowInfo['ActiveDirectoryName']."',";
				$sql.="IP=INET_ATON('".$NewRowInfo['IP']."'),";
				$sql.="GroupName='".$NewRowInfo['GroupName']."',";
				$sql.="Domain='".$NewRowInfo['Domain']."',";
				$sql.="Timeout='".$NewRowInfo['Timeout']."'";
				$sql.=" Where ";
				$sql.="(ActiveDirectory_Id='".$NewRowInfo['ActiveDirectory_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','ActiveDirectory',$NewRowInfo['ActiveDirectory_Id'],'ActiveDirectory',"Update Fail,Table=ActiveDirectory affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=ActiveDirectory affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'ActiveDirectory',$NewRowInfo['ActiveDirectory_Id'],'ActiveDirectory')){
					logunfair("UnFair",'ActiveDirectory',$NewRowInfo['ActiveDirectory_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	case 'TestAuth':	
				$Username=Get_Input('POST','DB','Username','STR',1,32,0,0);
				$Password=Get_Input('POST','DB','Password','STR',1,32,0,0);
				$RowId=Get_Input('GET','DB','RowId','INT',1,4294967295,0,0);
				$Method=Get_Input('GET','DB','Method','STR',1,32,0,0);
				
				$Res=ADCheckTest($Username,$Password,$RowId,$Method);
				echo($Res);
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
	ExitError($e->getMessage());
	}
//--------------------------------

?>
