<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSClass_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSClass_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.Class.View");
				$Class_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Class_Id,ISEnable,ClassName from Hclass where Class_Id='$Class_Id'";
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

				DSDebug(1,"DSClass_EditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.Class.Add");
				$NewRowInfo=array();
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ClassName']=Get_Input('POST','DB','ClassName','STR',1,32,0,0);
				//----------------------
				$sql= "insert Hclass set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="ClassName='".$NewRowInfo['ClassName']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Class_Id']=$RowId;
				$res = $conn->sql->query("Insert Ignore Hpermititem set PermitGroup='Visp',PermitItemName='Visp.User.Class.ChangeClass.".$NewRowInfo['Class_Id']."'");
				$res = $conn->sql->query("Insert Ignore Hreseller_permit(Reseller_Id,Visp_Id,ISPermit,PermitItem_Id) SELECT rg.Reseller_Id, v.Visp_Id, if(rg.Reseller_Id=1,'Yes','No'), PermitItem_Id FROM Hpermititem pi, Hreseller rg,Hvisp v WHERE PermitGroup = 'Visp'");

				logdbinsert($NewRowInfo,'Add','Class',$RowId,'Class');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSClass_EditRender Update ******************************************");
				
				exitifnotpermit(0,"Admin.User.Class.Edit");
				$NewRowInfo=array();

				$NewRowInfo['Class_Id']=Get_Input('POST','DB','Class_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ClassName']=Get_Input('POST','DB','ClassName','STR',1,32,0,0);

				if($NewRowInfo['ClassName']=='All')
					ExitError("این گروه قابل ویرایش نیست");
				$OldRowInfo= LoadRowInfo("Hclass","Class_Id='".$NewRowInfo['Class_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "update Hclass set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="ClassName='".$NewRowInfo['ClassName']."'";
				$sql.=" Where ";
				$sql.="(Class_Id='".$NewRowInfo['Class_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Class',$NewRowInfo['Class_Id'],'Class',"Update Fail,Table=Class_ affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Class_ affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Class',$NewRowInfo['Class_Id'],'Class')){
					logunfair("UnFair",'Class',$NewRowInfo['Class_Id'],'',"");
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
