<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSUser_Class_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//3 table info!!!
//for Hclass of Huser->  1-user   2-class  3-user_class
/*
$Table1='user';
$Table1_IdName='User_Id';
$Table1_Id=0;

$Table2='class';
$Table2_IdName='Class_Id';
$Table2_TextFieldName='ClassName';

$Table3='user_class';
*/
//for Hreseller of Hstatus->  1-status   2-reseller  3-status_reselleraccess

$Item='VispAccess';
$Table1='Hstatus';
$Table1_IdName='Status_Id';
$Table1_Id=0;

$Table2='Hvisp';
$Table2_IdName='Visp_Id';
$Table2_TextFieldName='VispName';

$Table3='Hstatus_vispaccess';



$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
$Table1_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

if($act=='do'){//request for CHANGE
	exitifnotpermit(0,"Admin.User.Status.VispAccess.Edit");

	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	
	if($TreeId=='a'){
		DBUpdate("Update $Table1 set VispAccess=If($State=1,'All','Selected') Where  $Table1_IdName=$Table1_Id");
		$Table2_TextFieldName='PermitAll';
	}
	else{
		if($State==1)
			$sql="Insert ignore $Table3 set $Table1_IdName=$Table1_Id,$Table2_IdName=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'";
		else
			$sql="Update $Table3 Set Checked='No'  where  ($Table1_IdName=$Table1_Id)AND($Table2_IdName=$TreeId)";
		$res = $conn->sql->query($sql);
	
		$Table2_TextFieldName=DBSelectAsString("Select $Table2_TextFieldName From $Table2 Where $Table2_IdName=$TreeId");
	}	
	if($State==1)
		logdb('Edit',$Table1,$Table1_Id,"","$Item checked [$Table2_TextFieldName]");
	else	
		logdb('Edit',$Table1,$Table1_Id,"","$Item unchecked [$Table2_TextFieldName]");
	echo "OK";
	exit;
}

exitifnotpermit(0,"Admin.User.Status.VispAccess.List");

header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');

//$sql="SELECT if(Checked='Yes',1,'') as Checked, ug.$Table2_IdName As $Table2_IdName, $Table2_TextFieldName FROM $Table2 ug Left JOIN $Table3 uug ON ($Table1_IdName =$Table1_Id) AND (ug.$Table2_IdName = uug.$Table2_IdName) Where (ug.ISEnable='Yes')";
$IsAccessAll=DBSelectAsString("Select If(VispAccess='All',1,'') From $Table1 Where $Table1_IdName=$Table1_Id");
$sql="Select '$IsAccessAll' As Checked,'a' As $Table2_IdName,'دسترسی به همه' As $Table2_TextFieldName union ".
"SELECT if(Checked='Yes',1,'') as Checked, ug.$Table2_IdName As $Table2_IdName, $Table2_TextFieldName ".
"FROM $Table2 ug Left JOIN $Table3 uug ON ($Table1_IdName =$Table1_Id) AND (ug.$Table2_IdName = uug.$Table2_IdName) ".
"Where (ug.ISEnable='Yes')";

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

