<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRadiusEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSRadiusEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Radius.View");
				$Radius_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Radius_Id,ISEnable,RadiusName,AuthPort,AcctPort from Hradius where Radius_Id='$Radius_Id'";
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
				DSDebug(1,"DSRadiusEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Radius.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['RadiusName']=Get_Input('POST','DB','RadiusName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['AuthPort']=Get_Input('POST','DB','AuthPort','INT',0,65000,0,0);
				$NewRowInfo['AcctPort']=Get_Input('POST','DB','AcctPort','INT',0,65000,0,0);
				//----------------------
				$sql= "insert Hradius set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="RadiusName='".$NewRowInfo['RadiusName']."',";
				$sql.="AuthPort='".$NewRowInfo['AuthPort']."',";
				$sql.="AcctPort='".$NewRowInfo['AcctPort']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Radius_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','Radius',$RowId,'Radius');
				radiusapply();
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSRadiusEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Radius.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Radius_Id']=Get_Input('POST','DB','Radius_Id','INT',1,4294967295,0,0);
				$NewRowInfo['RadiusName']=Get_Input('POST','DB','RadiusName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['AuthPort']=Get_Input('POST','DB','AuthPort','INT',0,65000,0,0);
				$NewRowInfo['AcctPort']=Get_Input('POST','DB','AcctPort','INT',0,65000,0,0);

				$OldRowInfo= LoadRowInfo("Hradius","Radius_Id='".$NewRowInfo['Radius_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "Update Hradius set ";
				$sql.="RadiusName='".$NewRowInfo['RadiusName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="AuthPort='".$NewRowInfo['AuthPort']."',";
				$sql.="AcctPort='".$NewRowInfo['AcctPort']."'";
				$sql.=" Where ";
				$sql.="(Radius_Id='".$NewRowInfo['Radius_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Radius',$NewRowInfo['Radius_Id'],'Radius',"Update Fail,Table=Radius affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Radius affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Radius',$NewRowInfo['Radius_Id'],'Radius')){
					logunfair("UnFair",'Radius',$NewRowInfo['Radius_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else {
					echo "OK~";
					radiusapply();
					}
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
