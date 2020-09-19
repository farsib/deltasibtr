<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSStatus_StatusTo_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
$Status_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

if($act=='do'){//request for CHANGE StausTo !!!!!!!!!!!!!!!!!!!!!!!!!!!

	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	
	//$Status_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);
	exitifnotpermit(0,"Admin.User.Status.StatusTo.Edit");
	
	if($State==1){
		if($TreeId==$Status_Id)
			ExitError("نمی توان وضعیت را به خودش تغییر داد");
		$sql="Insert ignore Hstatus_statusto set Status_Id=$Status_Id,StatusTo_Id=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
	}
	else
		$sql="Update Hstatus_statusto Set Checked='No'  where  (Status_Id=$Status_Id)AND(StatusTo_Id=$TreeId)";
	$res = $conn->sql->query($sql);
	
	$StatusName=DBSelectAsString("Select StatusName From Hstatus Where Status_Id=$TreeId");
	if($State==1)
		logdb('Edit','Status',$Status_Id,'',"StatusTo $StatusName set to Yes");
	else	
		logdb('Edit','Status',$Status_Id,'',"StatusTo $StatusName Set to No");
	echo "OK";
	exit;
}

exitifnotpermit(0,"Admin.User.Status.StatusTo.List");
header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');

$sql="SELECT if(Checked='Yes',1,'') as Checked, us.Status_Id As Status_Id, StatusName ".
	"FROM Hstatus us Left JOIN Hstatus_statusto us_s ON (us_s.Status_Id =$Status_Id) And (us.Status_Id = us_s.StatusTo_Id) ".
	"Where us.Status_Id<>$Status_Id and us.ISEnable='Yes'";
$res = $conn->sql->query($sql);
$data =  $conn->sql->get_next($res);

while($data){
	PrintNodeOpenClose($data["Status_Id"],$data["StatusName"],$data["Checked"]);
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

