<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSService_ResellerAccess_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
//$Table=Get_Input('GET','DB','Item','ARRAY',array("User","Service"),0,0,0);
$Service_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

//$Table=strtolower($Table);

//Check Permission
exitifnotpermit(0,"CRM.Service.ResellerAccess.List");

if($act=='do'){//request for CHANGE Permission !!!!!!!!!!!!!!!!!!!!!!!!!!!

	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	exitifnotpermit(0,"CRM.Service.ResellerAccess.Edit");
	//ExitIfNotPermitRowAccess("reseller",$TreeId);//TreeId is Reseller_Id
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	if($TreeId=='a'){
		DBUpdate("Update Hservice set ResellerAccess=If($State=1,'All','Selected') Where  Service_Id=$Service_Id");
		$ResellerName='PermitAll';
	}
	else{
		if($State==1)
			$sql="Insert ignore Hservice_reselleraccess set Service_Id=$Service_Id,Reseller_Id=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
		else
			$sql="Update Hservice_reselleraccess Set Checked='No'  where  (Service_Id=$Service_Id)AND(Reseller_Id=$TreeId)";
		$res = $conn->sql->query($sql);
		$ResellerName=DBSelectAsString("Select ResellerName From Hreseller Where Reseller_Id=$TreeId");
	}	
	if($State==1)
		logdb('Edit','Service',$Service_Id,'ResellerAccess',"Add Access to Reseller[$ResellerName]");
	else	
		logdb('Edit','Service',$Service_Id,'ResellerAccess',"Deleted Access to[$ResellerName]");
	echo "OK";
	exit;
}

header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');


$IsAccessAll=DBSelectAsString("Select If(ResellerAccess='All',1,'') From Hservice Where Service_Id=$Service_Id");
$sql="Select '$IsAccessAll' As Checked,'a' As Reseller_Id,'دسترسی به همه' As ResellerName union ".
	"SELECT if(Checked='Yes',1,'') as Checked, rg.Reseller_Id As Reseller_Id, ResellerName ".
	"FROM Hreseller rg Left JOIN Hservice_reselleraccess s_va ON (Service_Id =$Service_Id) AND (rg.Reseller_Id = s_va.Reseller_Id)".
	"Where (rg.ISEnable='Yes')";
//	"And (rg.ResellerPath Like '$AccessResellerPath')";
$res = $conn->sql->query($sql);
$data =  $conn->sql->get_next($res);

while($data){
	PrintNodeOpenClose($data["Reseller_Id"],$data["ResellerName"],$data["Checked"]);
	$data =$conn->sql->get_next($res);
	}





echo "</tree>";


}
catch (Exception $e) {
ExitError($e->getMessage());
}


function PrintNodeOpenClose($Id,$ItemName,$Checked){
	echo "<item text='$ItemName' id='$Id' checked='$Checked' im0='DSTree0.png' im1='DSTree1.png' im2='DSTree2.png'></item>";
}


?>

