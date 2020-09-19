<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSIPPoolEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSIPPoolEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.IPPool.View");
				$IPPool_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,IPPool_Id,IPPoolName,Method,IsFinishedIP ".
				"From Hippool where IPPool_Id='$IPPool_Id'";
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
				DSDebug(1,"DSIPPoolEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['IPPoolName']=Get_Input('POST','DB','IPPoolName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['Method']=Get_Input('POST','DB','Method','ARRAY',array("Last","FirstFree",'Random','Rarely'),0,0,0);
				$NewRowInfo['IsFinishedIP']=Get_Input('POST','DB','IsFinishedIP','ARRAY',array("Yes","No"),0,0,0);
				
				//----------------------
				$sql= "insert Hippool set ";
				$sql.="IPPoolName='".$NewRowInfo['IPPoolName']."',";
				$sql.="Method='".$NewRowInfo['Method']."',";
				$sql.="IsFinishedIP='".$NewRowInfo['IsFinishedIP']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','IPPool',$RowId,'IPPool');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSIPPoolEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Edit");
				$NewRowInfo=array();
				$NewRowInfo['IPPool_Id']=Get_Input('POST','DB','IPPool_Id','INT',1,4294967295,0,0);
				//$NewRowInfo['IPPoolName']=Get_Input('POST','DB','IPPoolName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['Method']=Get_Input('POST','DB','Method','ARRAY',array("Last","FirstFree",'Random','Rarely'),0,0,0);
				$NewRowInfo['IsFinishedIP']=Get_Input('POST','DB','IsFinishedIP','ARRAY',array("Yes","No"),0,0,0);

				if($NewRowInfo['IsFinishedIP']=='No'){
					$poolid=$NewRowInfo['IPPool_Id'];
					$n=DBSelectAsString("Select count(1) from Hfinishrule Where OnActiveServiceExpirePool_Id=$poolid or OnTrafficFinishPool_Id=$poolid Or OnTimeFinishPool_Id=$poolid");
					if($n>0)
						ExitError("این دامنه برای قانون اتمام استفاده می شود و شما نمیتوانید گزینه آی پی اتمام هست را روی خیر بگذارید");	
				}
				
				$OldRowInfo= LoadRowInfo("Hippool","IPPool_Id='".$NewRowInfo['IPPool_Id']."'");
				
				//----------------------
				
				$sql= "Update Hippool set ";
				//$sql.="IPPoolName='".$NewRowInfo['IPPoolName']."',";
				$sql.="Method='".$NewRowInfo['Method']."',";
				$sql.="IsFinishedIP='".$NewRowInfo['IsFinishedIP']."'";
				$sql.=" Where ";
				$sql.="(IPPool_Id='".$NewRowInfo['IPPool_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','IPPool',$NewRowInfo['IPPool_Id'],'IPPool',"Update Fail,Table=IPPool affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=IPPool affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'IPPool',$NewRowInfo['IPPool_Id'],'IPPool')){
					logunfair("UnFair",'IPPool',$NewRowInfo['IPPool_Id'],'',"");
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
