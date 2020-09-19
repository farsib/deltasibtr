<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSRadius_NasAccess_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//for Hreseller of Hstatus->  1-status   2-reseller  3-status_reselleraccess

$Item='NasAccess';
$Table1='Hradius';
$Table1_IdName='Radius_Id';
$Table1_Id=0;

$Table2='Hnas';
$Table2_IdName='Nas_Id';
$Table2_TextFieldName='NasName';

$Table3='Hradius_nasaccess';



$act=Get_Input('GET','DB','act','ARRAY',array("list","do","Apply"),0,0,0);
$Table1_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

if($act=='do'){//request for CHANGE

	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	exitifnotpermit(0,"CRM.Supporter.{$Item}.Edit");
	
	if($TreeId=='a'){
		DBUpdate("Update $Table1 set {$Item}=If($State=1,'All','Selected') Where  $Table1_IdName=$Table1_Id");
		$ClickItemName='PermitAll';
	}
	else{
		if($State==1)
			$sql="Insert ignore $Table3 set $Table1_IdName=$Table1_Id,$Table2_IdName=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
		else
			$sql="Update $Table3 Set Checked='No'  where  ($Table1_IdName=$Table1_Id)AND($Table2_IdName=$TreeId)";
		$res = $conn->sql->query($sql);
		$ClickItemName=DBSelectAsString("Select $Table2_TextFieldName From $Table2 Where $Table2_IdName=$TreeId");
	}	
	if($State==1)
		logdb('Edit',$Table1,$Table1_Id,"$Table2","Register to $Table2_TextFieldName [$ClickItemName]");
	else	
		logdb('Edit',$Table1,$Table1_Id,"$Table2","Deleted from $Table2_TextFieldName [$ClickItemName]");
	echo "OK";
	exit;
}
if($act=='Apply'){
	exitifnotpermit(0,"CRM.Supporter.{$Item}.Edit");
	radiusapply();
	echo "OK";
}

exitifnotpermit(0,"CRM.Supporter.{$Item}.List");
header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');
$IsAccessAll=DBSelectAsString("Select If({$Item}='All',1,'') From $Table1 Where $Table1_IdName=$Table1_Id");
$sql="Select '$IsAccessAll' As Checked,'a' As $Table2_IdName,'دسترسی به همه' As $Table2_TextFieldName union SELECT if(Checked='Yes',1,'') as Checked, ug.$Table2_IdName As $Table2_IdName, $Table2_TextFieldName FROM $Table2 ug Left JOIN $Table3 uug ON ($Table1_IdName =$Table1_Id) AND (ug.$Table2_IdName = uug.$Table2_IdName) Where (ug.ISEnable='Yes')";
$res = $conn->sql->query($sql);
$data =  $conn->sql->get_next($res);

while($data){
	PrintNodeOpenClose($data["$Table2_IdName"],$data["$Table2_TextFieldName"],$data["Checked"]);
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
