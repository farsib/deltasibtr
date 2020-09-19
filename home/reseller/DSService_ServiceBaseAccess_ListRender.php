<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSService_ServiceBaseAccess_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
$Service_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

exitifnotpermit(0,"CRM.Service.ServiceBaseAccess.List");

if($act=='do'){//request for CHANGE Permission !!!!!!!!!!!!!!!!!!!!!!!!!!!

	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	exitifnotpermit(0,"CRM.Service.ServiceBaseAccess.Edit");
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	if($TreeId=='a'){
		DBUpdate("Update Hservice set ServiceBaseAccess=If($State=1,'All','Selected') Where  Service_Id=$Service_Id");
		$ServiceBaseName='PermitAll';
	}
	else{
		if($State==1)
			$sql="Insert ignore Hservice_servicebaseaccess set Service_Id=$Service_Id,Accessed_Service_Id=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
		else
			$sql="Update Hservice_servicebaseaccess Set Checked='No'  where  (Service_Id=$Service_Id)AND(Accessed_Service_Id=$TreeId)";
		$res = $conn->sql->query($sql);
		$ServiceBaseName=DBSelectAsString("Select ServiceName From Hservice Where Service_Id=$TreeId");
	}	
	if($State==1)
		logdb('Edit','Service',$Service_Id,'ServiceBaseAccess',"Add Access to[$ServiceBaseName]");
	else	
		logdb('Edit','Service',$Service_Id,'ServiceBaseAccess',"Deleted Access to[$ServiceBaseName]");
	echo "OK";
	exit;
}

header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');


$IsAccessAll=DBSelectAsString("Select If(ServiceBaseAccess='All',1,'') From Hservice Where Service_Id=$Service_Id");

$sql="Select '$IsAccessAll' As Checked,'a' As Accessed_Service_Id,'دسترسی به همه' As ServiceName union ".
	"SELECT if(Checked='Yes',1,'') as Checked, rs.Service_Id As Accessed_Service_Id, rs.ServiceName ".
	"FROM Hservice rs Left JOIN Hservice_servicebaseaccess s_sba ON (s_sba.Service_Id =$Service_Id) AND (rs.Service_Id = s_sba.Accessed_Service_Id)".
	"Where (rs.ISEnable='Yes')AND(rs.IsDel='No')AND(rs.ServiceType='Base')";
$res = $conn->sql->query($sql);
$data =  $conn->sql->get_next($res);

while($data){
	PrintNodeOpenClose($data["Accessed_Service_Id"],$data["ServiceName"],$data["Checked"]);
	$data =$conn->sql->get_next($res);
	}

echo "</tree>";


}
catch (Exception $e) {
ExitError($e->getMessage());
}


function PrintNodeOpenClose($Id,$ItemName,$Checked){
	echo "<item text='".XMLescape($ItemName)."' id='$Id' checked='$Checked' im0='DSTree0.png' im1='DSTree1.png' im2='DSTree2.png'></item>";
}

?>

