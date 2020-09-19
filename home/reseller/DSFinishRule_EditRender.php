<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSFinishRuleEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update",'SelectIPPool'),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSFinishRuleEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.FinishRule.View");
				$FinishRule_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,FinishRule_Id,FinishRuleName,OnActiveServiceExpirePool_Id,OnTrafficFinishPool_Id,".
				"OnTimeFinishPool_Id,OnDebitFinishPool_Id  ".
				"From Hfinishrule where FinishRule_Id='$FinishRule_Id'";
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
				DSDebug(1,"DSFinishRuleEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.FinishRule.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['FinishRuleName']=Get_Input('POST','DB','FinishRuleName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['OnActiveServiceExpirePool_Id']=Get_Input('POST','DB','OnActiveServiceExpirePool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['OnTrafficFinishPool_Id']=Get_Input('POST','DB','OnTrafficFinishPool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['OnTimeFinishPool_Id']=Get_Input('POST','DB','OnTimeFinishPool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['OnDebitFinishPool_Id']=Get_Input('POST','DB','OnDebitFinishPool_Id','INT',0,4294967295,0,0);
				if($NewRowInfo['OnActiveServiceExpirePool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnActiveServiceExpirePool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان انقضا سرویس،دامنه اتمام نیست");
				}
				if($NewRowInfo['OnTrafficFinishPool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnTrafficFinishPool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان اتمام ترافیک،دامنه اتمام نیست");
				}
				if($NewRowInfo['OnTimeFinishPool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnTimeFinishPool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان اتمام زمان،دامنه اتمام نیست");
				}
				if($NewRowInfo['OnDebitFinishPool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnDebitFinishPool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان کنترل بدهی،دامنه اتمام نیست");
				}
				//----------------------
				$sql= "insert Hfinishrule set ";
				$sql.="FinishRuleName='".$NewRowInfo['FinishRuleName']."',";
				$sql.="OnActiveServiceExpirePool_Id='".$NewRowInfo['OnActiveServiceExpirePool_Id']."',";
				$sql.="OnTrafficFinishPool_Id='".$NewRowInfo['OnTrafficFinishPool_Id']."',";
				$sql.="OnTimeFinishPool_Id='".$NewRowInfo['OnTimeFinishPool_Id']."',";
				$sql.="OnDebitFinishPool_Id='".$NewRowInfo['OnDebitFinishPool_Id']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				logdbinsert($NewRowInfo,'Add','FinishRule',$RowId,'FinishRule');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSFinishRuleEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.FinishRule.Edit");
				$NewRowInfo=array();
				$NewRowInfo['FinishRule_Id']=Get_Input('POST','DB','FinishRule_Id','INT',1,4294967295,0,0);
				//$NewRowInfo['FinishRuleName']=Get_Input('POST','DB','FinishRuleName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['OnActiveServiceExpirePool_Id']=Get_Input('POST','DB','OnActiveServiceExpirePool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['OnTrafficFinishPool_Id']=Get_Input('POST','DB','OnTrafficFinishPool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['OnTimeFinishPool_Id']=Get_Input('POST','DB','OnTimeFinishPool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['OnDebitFinishPool_Id']=Get_Input('POST','DB','OnDebitFinishPool_Id','INT',0,4294967295,0,0);
				
				
				if($NewRowInfo['OnActiveServiceExpirePool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnActiveServiceExpirePool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان انقضا سرویس،دامنه اتمام نیست");
				}
				if($NewRowInfo['OnTrafficFinishPool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnTrafficFinishPool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان اتمام ترافیک،دامنه اتمام نیست");
				}
				if($NewRowInfo['OnTimeFinishPool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnTimeFinishPool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان اتمام زمان،دامنه اتمام نیست");
				}
				if($NewRowInfo['OnDebitFinishPool_Id']>0){
					$IsFinishedIP=DBSelectAsString('Select IsFinishedIP From Hippool Where IPPool_Id='.$NewRowInfo['OnDebitFinishPool_Id']);
					if($IsFinishedIP!='Yes') ExitError("دامنه آی پی زمان کنترل بدهی،دامنه اتمام نیست");
				}

				$OldRowInfo= LoadRowInfo("Hfinishrule","FinishRule_Id='".$NewRowInfo['FinishRule_Id']."'");
				
				//----------------------
				
				$sql= "Update Hfinishrule set ";
				//$sql.="FinishRuleName='".$NewRowInfo['FinishRuleName']."',";
				$sql.="OnActiveServiceExpirePool_Id='".$NewRowInfo['OnActiveServiceExpirePool_Id']."',";
				$sql.="OnTrafficFinishPool_Id='".$NewRowInfo['OnTrafficFinishPool_Id']."',";
				$sql.="OnTimeFinishPool_Id='".$NewRowInfo['OnTimeFinishPool_Id']."',";
				$sql.="OnDebitFinishPool_Id='".$NewRowInfo['OnDebitFinishPool_Id']."'";
				$sql.=" Where ";
				$sql.="(FinishRule_Id='".$NewRowInfo['FinishRule_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','FinishRule',$NewRowInfo['FinishRule_Id'],'FinishRule',"Update Fail,Table=FinishRule affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=FinishRule affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'FinishRule',$NewRowInfo['FinishRule_Id'],'FinishRule')){
					logunfair("UnFair",'FinishRule',$NewRowInfo['FinishRule_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	case "SelectIPPool":
				DSDebug(1,"DSFinishRuleEditRender SelectNasInfoName *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("Select 0 As IPPool_Id,'- هیچی -' As IPPoolName union SELECT IPPool_Id,IPPoolName FROM Hippool Where IsFinishedIP='Yes' order by IPPoolName ASC","","IPPool_Id,IPPoolName","","");
	
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
