<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSCenter_VispAccess_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
//$Table=Get_Input('GET','DB','Item','ARRAY',array("User","Center"),0,0,0);
$Center_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

//$Table=strtolower($Table);

if($act=='do'){//request for CHANGE Permission !!!!!!!!!!!!!!!!!!!!!!!!!!!
	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	exitifnotpermit(0,"Admin.Center.VispAccess.Edit");

	if($TreeId=='a'){
		DBUpdate("Update Hcenter set VispAccess=If($State=1,'All','Selected') Where  Center_Id=$Center_Id");
		$VispName='PermitAll';
	}
	else{
		if($State==1)
			$sql="Insert ignore Hcenter_vispaccess set Center_Id=$Center_Id,Visp_Id=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
		else
			$sql="Update Hcenter_vispaccess Set Checked='No'  where  (Center_Id=$Center_Id)AND(Visp_Id=$TreeId)";
		$res = $conn->sql->query($sql);
	
		$VispName=DBSelectAsString("Select VispName From Hvisp Where Visp_Id=$TreeId");
	}	
	if($State==1)
		logdb('Edit','Center',$Center_Id,'VispAccess',"Add Access to Visp[$VispName]");
	else	
		logdb('Edit','Center',$Center_Id,'VispAccess',"Deleted Access to[$VispName]");
	echo "OK";
	exit;
}

exitifnotpermit(0,"Admin.Center.VispAccess.List");

header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');

$IsAccessAll=DBSelectAsString("Select If(VispAccess='All',1,'') From Hcenter Where Center_Id=$Center_Id");
$sql="Select '$IsAccessAll' As Checked,'a' As Visp_Id,'دسترسی به همه' As VispName union SELECT if(Checked='Yes',1,'') as Checked, v.Visp_Id As Visp_Id, VispName FROM Hvisp v Left JOIN Hcenter_vispaccess s_va ON (Center_Id =$Center_Id) AND (v.Visp_Id = s_va.Visp_Id) Where (v.ISEnable='Yes')";
$res = $conn->sql->query($sql);
$data =  $conn->sql->get_next($res);

while($data){
	PrintNodeOpenClose($data["Visp_Id"],$data["VispName"],$data["Checked"]);
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

