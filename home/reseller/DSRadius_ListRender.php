<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSRadiusListRender ..................................................................................");
$act=Get_Input('GET','DB','act','ARRAY',array("list","Apply", "Status","Start","Stop","Delete"),0,0,0);
PrintInputGetPost();

if($LResellerName=='') {//Session Expire
	if (in_array($act, array("Apply", "Status","Start","Stop"))) {//result is form
		header ("Content-Type:text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<data>';
		GenerateLoadField('Status','نشست منقضی شده، لطفا مجدد وارد شوید');
		echo '</data>';
		exit;
	}
	else
		ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
}

switch ($act) {
    case "list":
				DSDebug(0,"DSRadiusListRender->List ********************************************");
				exitifnotpermit(0,"Admin.Radius.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,
					"SELECT Radius_Id,ISEnable,RadiusName,AuthPort,AcctPort From Hradius Where 1 ".$sqlfilter." $SortStr ",
					"Radius_Id","Radius_Id,ISEnable,RadiusName,AuthPort,AcctPort",
					"","","");
       break;
    case "Apply":
				DSDebug(0,"DSRadiusListRender->Apply ********************************************");
				exitifnotpermit(0,"Admin.Radius.Apply");
				//runshellcommand('service','radius','restart','-'); 
				$data1['Status']= radiusapply();
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data1)
					foreach ($data1 as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
       break;
    case 'Status':
				DSDebug(1,'DSRadiusListRender Status ********************************************');
				exitifnotpermit(0,"Admin.Radius.Status");
				$data1['Status']= runshellcommand('service','radius','status','-'); 
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data1)
					foreach ($data1 as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
    case "Start":
				DSDebug(0,"DSRadiusListRender->Start ********************************************");
				exitifnotpermit(0,"Admin.Radius.Start");
				runshellcommand('service','radius','start','-'); 
				$data1['Status']= runshellcommand('service','radius','status','-'); 
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data1)
					foreach ($data1 as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
    case "Stop":
				DSDebug(0,"DSRadiusListRender->Stop ********************************************");
				exitifnotpermit(0,"Admin.Radius.Stop");
				runshellcommand('service','radius','stop','-'); 
				$data1['Status']= runshellcommand('service','radius','status','-'); 
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data1)
					foreach ($data1 as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
	case "Delete":
				DSDebug(1,"DSRadiusListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.Radius.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Radius_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$ar=DBDelete('delete from Hradius Where Radius_Id='.$NewRowInfo['Radius_Id']);
				$ar=DBDelete('Delete From Hradius_nasaccess Where Radius_Id='.$NewRowInfo['Radius_Id']);
				logdbdelete($NewRowInfo,'Delete','Radius',$NewRowInfo['Radius_Id'],'');
				radiusapply();
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}




?>