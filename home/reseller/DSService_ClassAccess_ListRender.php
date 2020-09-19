<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSService_ClassAccess_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
//$Table=Get_Input('GET','DB','Item','ARRAY',array("User","Service"),0,0,0);
$Service_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

//$Table=strtolower($Table);

//Check Permission
exitifnotpermit(0,"CRM.Service.ClassAccess.List");

if($act=='do'){//request for CHANGE Permission !!!!!!!!!!!!!!!!!!!!!!!!!!!
/*
Input GET=Array
(
    [un] => 1395545090557
    [act] => do
    [ParentId] => 1
    [Item] => User
    [Id] => 5
    [state] => 1
)
*/
	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	exitifnotpermit(0,"CRM.Service.ClassAccess.Edit");
	if($TreeId=='a'){
		DBUpdate("Update Hservice set ClassAccess=If($State=1,'All','Selected') Where  Service_Id=$Service_Id");
		$ClassName='PermitAll';
	}
	else{
		if($State==1)
			$sql="Insert ignore Hservice_class set Service_Id=$Service_Id,Class_Id=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
		else
			$sql="Update Hservice_class Set Checked='No'  where  (Service_Id=$Service_Id)AND(Class_Id=$TreeId)";
		$res = $conn->sql->query($sql);
		$ClassName=DBSelectAsString("Select ClassName From Hclass Where Class_Id=$TreeId");
	}
		
	if($State==1)
		logdb('Edit','Service',$Service_Id,'ClassAccess',"Add Access to group[$ClassName]");
	else	
		logdb('Edit','Service',$Service_Id,'ClassAccess',"Deleted from group[$ClassName]");
	echo "OK";
	exit;
}

header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');

$IsAccessAll=DBSelectAsString("Select If(ClassAccess='All',1,'') From Hservice Where Service_Id=$Service_Id");
$sql="Select '$IsAccessAll' As Checked,'a' As Class_Id,'دسترسی به همه' As ClassName union SELECT if(Checked='Yes',1,'') as Checked, ug.Class_Id As Class_Id, ClassName FROM Hclass ug Left JOIN Hservice_class uug ON (Service_Id =$Service_Id) AND (ug.Class_Id = uug.Class_Id) Where (ug.ISEnable='Yes')";
$res = $conn->sql->query($sql);
$data =  $conn->sql->get_next($res);

while($data){
	PrintNodeOpenClose($data["Class_Id"],$data["ClassName"],$data["Checked"]);
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

