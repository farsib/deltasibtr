<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSNetLogIPEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSNetLogIPEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.NetLogIP.View");
				$NetLogIP_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				
				$sql="SELECT '' As Error,NetLogIP_Id,AssignmentTo,IPType,ISAuthenticate,INET_NTOA(StartIP) as StartIP,INET_NTOA(EndIP) as EndIP,Comment ".
					"from Hnetlogip where NetLogIP_Id='$NetLogIP_Id'";
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
				DSDebug(1,"DSNetLogIPEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.NetLogIP.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['AssignmentTo']=Get_Input('POST','DB','AssignmentTo','STR',1,32,0,0);
				$NewRowInfo['IPType']=Get_Input('POST','DB','IPType','ARRAY',array("NAT","Route"),0,0,0);
				$NewRowInfo['ISAuthenticate']=Get_Input('POST','DB','ISAuthenticate','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['StartIP']=Get_Input('POST','DB','StartIP','STR',7,15,0,0);
				$NewRowInfo['EndIP']=Get_Input('POST','DB','EndIP','STR',7,15,0,0);
				$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,200,0,0);
				
				//----------------------
				$sql= "insert Hnetlogip set ";
				$sql.="AssignmentTo='".$NewRowInfo['AssignmentTo']."',";
				$sql.="IPType='".$NewRowInfo['IPType']."',";
				$sql.="ISAuthenticate='".$NewRowInfo['ISAuthenticate']."',";
				$sql.="StartIP=INET_ATON('".$NewRowInfo['StartIP']."'),";
				$sql.="EndIP=INET_ATON('".$NewRowInfo['EndIP']."'),";
				$sql.="Comment='".$NewRowInfo['Comment']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['NetLogIP_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','NetLogIP',$RowId,'-');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSNetLogIPEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.NetLogIP.Edit");
				$NewRowInfo=array();
				$NewRowInfo['NetLogIP_Id']=Get_Input('POST','DB','NetLogIP_Id','INT',1,4294967295,0,0);
				$NewRowInfo['AssignmentTo']=Get_Input('POST','DB','AssignmentTo','STR',1,32,0,0);
				$NewRowInfo['IPType']=Get_Input('POST','DB','IPType','ARRAY',array("NAT","Route"),0,0,0);
				$NewRowInfo['ISAuthenticate']=Get_Input('POST','DB','ISAuthenticate','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['StartIP']=Get_Input('POST','DB','StartIP','STR',7,15,0,0);
				$NewRowInfo['EndIP']=Get_Input('POST','DB','EndIP','STR',7,15,0,0);
				$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,200,0,0);			
				
				
				$OldRowInfo= LoadRowInfo("Hnetlogip","NetLogIP_Id='".$NewRowInfo['NetLogIP_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hnetlogip set ";
				$sql.="AssignmentTo='".$NewRowInfo['AssignmentTo']."',";
				$sql.="IPType='".$NewRowInfo['IPType']."',";
				$sql.="ISAuthenticate='".$NewRowInfo['ISAuthenticate']."',";
				$sql.="StartIP=INET_ATON('".$NewRowInfo['StartIP']."'),";
				$sql.="EndIP=INET_ATON('".$NewRowInfo['EndIP']."'),";
				$sql.="Comment='".$NewRowInfo['Comment']."'";
				$sql.=" Where ";
				$sql.="(NetLogIP_Id='".$NewRowInfo['NetLogIP_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','NetLogIP',$NewRowInfo['NetLogIP_Id'],'NetLogIP',"Update Fail,Table=NetLogIP affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=NetLogIP affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'NetLogIP',$NewRowInfo['NetLogIP_Id'],'-')){
					logunfair("UnFair",'NetLogIP',$NewRowInfo['NetLogIP_Id'],'',"");
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
