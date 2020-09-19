<?php
try {
require_once("../../lib/DSInitialReseller.php");

DSDebug(0,"DSClass_VispAccess_ListRender ..............................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//for Hreseller of Hstatus->  1-status   2-reseller  3-status_reselleraccess

$Item='Group';
$Table1='Huser';
$Table1_IdName='User_Id';
$Table1_Id=0;

$Table2='Hclass';
$Table2_IdName='Class_Id';
$Table2_TextFieldName='ClassName';

$Table3='Huser_class';



$act=Get_Input('GET','DB','act','ARRAY',array("list","do"),0,0,0);
$Table1_Id=Get_Input('GET','DB','ParentId','INT',1,4294967295,0,0);

if($act=='do'){//request for CHANGE

	$TreeId=Get_Input('GET','DB','Id','STR',1,10,0,0);
	$State=Get_Input('GET','DB','state','INT',0,1,0,0);//0,1
	//check Permition
	$User_Id=$Table1_Id;
	$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
	
	$Class_Id=$TreeId;
	//Check Hreseller access
	$ResellerAccess=DBSelectAsString("Select ResellerAccess from Hclass where (Class_Id=$Class_Id)");
	If($ResellerAccess=='Selected'){
		$Checked=DBSelectAsString("Select Checked from Hclass_reselleraccess where (Class_Id=$Class_Id)And(Reseller_Id=$LReseller_Id)");
		if($Checked!='Yes') ExitError('You not allowed to change');
	}	
	$VispAccess=DBSelectAsString("Select VispAccess from Hclass where (Class_Id=$Class_Id)");
	If($VispAccess=='Selected'){
		$Checked=DBSelectAsString("Select Checked from Hclass_vispaccess where (Class_Id=$Class_Id)And(Visp_Id=$Visp_Id)");
		if($Checked!='Yes') ExitError('Visp not access to this Class');
	}	
	
	
	if($State==1)
		DBInsert("Insert ignore $Table3 set $Table1_IdName=$Table1_Id,$Table2_IdName=$TreeId,Checked='Yes' on Duplicate KEY UPDATE Checked='Yes'");
	else
		DBUpdate("Update $Table3 Set Checked='No'  where  ($Table1_IdName=$Table1_Id)AND($Table2_IdName=$TreeId)");
	
	
	$Table2_TextFieldName=DBSelectAsString("Select $Table2_TextFieldName From $Table2 Where $Table2_IdName=$TreeId");
	if($State==1)
		logdb('Edit',$Table1,$Table1_Id,"$Table2","Register to Group[$Table2_TextFieldName]");
	else	
		logdb('Edit',$Table1,$Table1_Id,"$Table2","Deleted from Group[$Table2_TextFieldName]");
	echo "OK";
	exit;
}

header ("Content-Type:text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
print('<tree id="0" >');

$sql="SELECT if(Checked='Yes',1,'') as Checked, ug.$Table2_IdName As $Table2_IdName, $Table2_TextFieldName FROM $Table2 ug Left JOIN $Table3 uug ON ($Table1_IdName =$Table1_Id) AND (ug.$Table2_IdName = uug.$Table2_IdName) Where (ug.ISEnable='Yes')";
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

