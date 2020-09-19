<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSSupportItem_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSSupportItem_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.SupportItem.View");
				$SupportItem_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,SupportItem_Id,IsEnable,SupportItemTitle from Hsupportitem where SupportItem_Id='$SupportItem_Id'";
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
				DSDebug(1,"DSSupportItem_EditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.SupportItem.Add");
				
				$NewRowInfo=array();
				$NewRowInfo['IsEnable']=Get_Input('POST','DB','IsEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['SupportItemTitle']=Get_Input('POST','DB','SupportItemTitle','STR',1,64,0,0);
				
				$sql= "insert Hsupportitem set ";
				$sql.="IsEnable='".$NewRowInfo['IsEnable']."',";
				$sql.="SupportItemTitle='".$NewRowInfo['SupportItemTitle']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['SupportItem_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','SupportItem',$NewRowInfo['SupportItem_Id'],'SupportItem');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSSupportItem_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.SupportItem.Edit");
				$NewRowInfo=array();
				$SupportItem_Id=Get_Input('POST','DB','SupportItem_Id','INT',1,4294967295,0,0);
				$NewRowInfo['IsEnable']=Get_Input('POST','DB','IsEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['SupportItemTitle']=Get_Input('POST','DB','SupportItemTitle','STR',1,64,0,0);
				
				$OldRowInfo= LoadRowInfoSql("SELECT IsEnable,SupportItemTitle from Hsupportitem where (SupportItem_Id='$SupportItem_Id')");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));
				
				$sql= "update Hsupportitem set ";
				$sql.="IsEnable='".$NewRowInfo['IsEnable']."',";
				$sql.="SupportItemTitle='".$NewRowInfo['SupportItemTitle']."'";
				$sql.=" Where ";
				$sql.="(SupportItem_Id='$SupportItem_Id')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();

				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'SupportItem',$SupportItem_Id,'SupportItem')){
					logunfair("UnFair",'SupportItem',$SupportItem_Id,'SupportItem','');
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
