<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSPermit ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Permit  Every Hreseller can load their Permission list

$LoadType=Get_Input('GET','DB','LoadType','ARRAY',array("ByVisp", "ByUser"),0,0,0);
if($LoadType=='ByUser'){
	$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
	$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id='$User_Id'");
	if($Visp_Id<=0)
		ExitError("شناسه کاربر نامعتبر");
}
else
	$Visp_Id=Get_Input('GET','DB','Visp_Id','INT',0,4294967295,0,0);
$Permission=LoadPermissionAsStr($Visp_Id);
	
DSDebug(1,"Permission=$Permission");	
echo $Permission;




} catch (Exception $e) {
	ExitError($e->getMessage());
}


function LoadPermissionAsStr($Visp_Id){
	global $conn,$LReseller_Id;
	$sql="SELECT PermitItemName FROM Hreseller_permit rp left join Hpermititem pi on rp.PermitItem_Id=pi.PermitItem_Id ";
	$sql.=" Where (Reseller_Id =$LReseller_Id)AND(Visp_Id='$Visp_Id')AND(ISPermit='Yes') ";
	$res = $conn->sql->query($sql);
	$data =  $conn->sql->get_next($res);
	$Permission="Start,";//When Not found any permit
	while($data){
		$Permission.=$data["PermitItemName"].",";
		$data =  $conn->sql->get_next($res);
	}
	$Permission.="End";
	return $Permission;
}

?>
