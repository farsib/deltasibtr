<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSPackageEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSPackageEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Package.View");
				$Package_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Package_Id,Creator_Id,ISEnable,PackageName,Credit,Price from Hpackage where Package_Id='$Package_Id'";
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
				DSDebug(1,"DSPackageEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Package.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['PackageName']=Get_Input('POST','DB','PackageName','STR',1,128,0,0);
				$Credit=floatval(Get_Input('POST','DB','Credit','PRC',1,14,0,0));
				$Price=floatval(Get_Input('POST','DB','Price','PRC',1,14,0,0));
				if($Credit<0)
						ExitError('Invalid Credit Value!!!');
				if($Price<0)
						ExitError('Invalid Price Value!!!');
				//----------------------
				$sql= "insert Hpackage set ";
				$sql.="Creator_Id=$LReseller_Id,";
				$sql.="PackageName='".$NewRowInfo['PackageName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="Credit=$Credit,";
				$sql.="Price='$Price'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Package_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','Package',$RowId,'Package');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSPackageEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Package.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Package_Id']=Get_Input('POST','DB','Package_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['PackageName']=Get_Input('POST','DB','PackageName','STR',1,128,0,0);
				$Credit=floatval(Get_Input('POST','DB','Credit','PRC',1,14,0,0));
				$Price=floatval(Get_Input('POST','DB','Price','PRC',1,14,0,0));
				$NewRowInfo['Credit']=$Credit;
				$NewRowInfo['Price']=$Price;
				if($Credit<0)
						ExitError('Invalid Credit Value!!!');
				if($Price<0)
						ExitError('Invalid Price Value!!!');
				$OldRowInfo= LoadRowInfo("Hpackage","Package_Id='".$NewRowInfo['Package_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hpackage set  ";
				$sql.="PackageName='".$NewRowInfo['PackageName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="Credit=$Credit,";
				$sql.="Price='$Price'";
				$sql.=" Where ";
				$sql.="(Package_Id='".$NewRowInfo['Package_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Package',$NewRowInfo['Package_Id'],'Package',"Update Fail,Table=Package affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Package affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Package',$NewRowInfo['Package_Id'],'Package')){
					logunfair("UnFair",'Package',$NewRowInfo['Package_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	case "SelectPackageInfoName":
				DSDebug(1,"DSPackageEditRender SelectPackageInfoName *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT PackageInfo_Id,PackageInfoName FROM HPackageinfo order by PackageInfoName ASC","","PackageInfo_Id,PackageInfoName","","");
	
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
