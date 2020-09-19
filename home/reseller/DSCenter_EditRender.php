<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSCenterEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

/*
http://www.tutorialspoint.com/mysql/mysql-regexps.htm
Pattern	What the pattern matches
^	Beginning of string
$	End of string
.	Any single character
[...]	Any character listed between the square brackets
[^...]	Any character not listed between the square brackets
p1|p2|p3	Alternation; matches any of the patterns p1, p2, or p3
*	Zero or more instances of preceding element
+	One or more instances of preceding element
{n}	n instances of preceding element
{m,n}	m through n instances of preceding element
*/


$act=Get_Input('GET','DB','act','ARRAY',array('load','insert','update','SelectSupporter','LoadCommission','UpdateCommission'),0,0,0);

try {
switch ($act) {
    case 'load':
				DSDebug(1,'DSCenterEditRender Load ********************************************');
				exitifnotpermit(0,'Admin.Center.View');
				$Center_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT Center_Id,CenterName,UsernamePattern,TotalPort,BadPort,Country,State,City,Center,PopSite,NOE  from Hcenter where Center_Id='$Center_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
    case "insert": 
				DSDebug(1,"DSVispEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Center.Add");
				$NewRowInfo=array();
				$NewRowInfo['CenterName']=Get_Input('POST','DB','CenterName','STR',1,64,0,0);
				$NewRowInfo['TotalPort']=Get_Input('POST','DB','TotalPort','INT',0,999999,0,0);
				$NewRowInfo['BadPort']=Get_Input('POST','DB','BadPort','INT',0,999999,0,0);
				$NewRowInfo['UsernamePattern']=Get_Input('POST','DB','UsernamePattern','STR',1,250,0,0);
				$NewRowInfo['Country']=Get_Input('POST','DB','Country','STR',0,64,0,0);
				$NewRowInfo['State']=Get_Input('POST','DB','State','STR',0,64,0,0);
				$NewRowInfo['City']=Get_Input('POST','DB','City','STR',0,64,0,0);
				$NewRowInfo['Center']=Get_Input('POST','DB','Center','STR',0,64,0,0);
				$NewRowInfo['PopSite']=Get_Input('POST','DB','PopSite','STR',0,64,0,0);
				$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);
				//----------------------
				$sql= "insert Hcenter set ";
				$sql.="CenterName='".$NewRowInfo['CenterName']."',";
				$sql.="TotalPort='".$NewRowInfo['TotalPort']."',";
				$sql.="BadPort='".$NewRowInfo['BadPort']."',";
				$sql.="UsernamePattern='".$NewRowInfo['UsernamePattern']."',";
				$sql.="Country='".$NewRowInfo['Country']."',";
				$sql.="State='".$NewRowInfo['State']."',";
				$sql.="City='".$NewRowInfo['City']."',";
				$sql.="Center='".$NewRowInfo['Center']."',";
				$sql.="PopSite='".$NewRowInfo['PopSite']."',";
				$sql.="NOE='".$NewRowInfo['NOE']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Center_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','Center',$RowId,'Center');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSVispEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Center.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Center_Id']=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
				$NewRowInfo['CenterName']=Get_Input('POST','DB','CenterName','STR',1,64,0,0);
				$NewRowInfo['TotalPort']=Get_Input('POST','DB','TotalPort','INT',0,999999,0,0);
				$NewRowInfo['BadPort']=Get_Input('POST','DB','BadPort','INT',0,999999,0,0);
				$NewRowInfo['UsernamePattern']=Get_Input('POST','DB','UsernamePattern','STR',1,250,0,0);
				$NewRowInfo['Country']=Get_Input('POST','DB','Country','STR',1,64,0,0);
				$NewRowInfo['State']=Get_Input('POST','DB','State','STR',1,64,0,0);
				$NewRowInfo['City']=Get_Input('POST','DB','City','STR',1,64,0,0);
				$NewRowInfo['Center']=Get_Input('POST','DB','Center','STR',1,64,0,0);
				$NewRowInfo['PopSite']=Get_Input('POST','DB','PopSite','STR',1,64,0,0);
				$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);

				$OldRowInfo= LoadRowInfo("Hcenter","Center_Id='".$NewRowInfo['Center_Id']."'");
				
				//DSDebug(2,DSPrintArray($OldRowInfo));
				//DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "Update Hcenter set ";
				$sql.="CenterName='".$NewRowInfo['CenterName']."',";
				$sql.="TotalPort='".$NewRowInfo['TotalPort']."',";
				$sql.="BadPort='".$NewRowInfo['BadPort']."',";
				$sql.="UsernamePattern='".$NewRowInfo['UsernamePattern']."',";
				$sql.="Country='".$NewRowInfo['Country']."',";
				$sql.="State='".$NewRowInfo['State']."',";
				$sql.="City='".$NewRowInfo['City']."',";
				$sql.="Center='".$NewRowInfo['Center']."',";
				$sql.="PopSite='".$NewRowInfo['PopSite']."',";
				$sql.="NOE='".$NewRowInfo['NOE']."'";
				$sql.=" Where ";
				$sql.="(Center_Id='".$NewRowInfo['Center_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Center',$NewRowInfo['Center_Id'],'Center',"Update Fail,Table=center affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=center affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Center',$NewRowInfo['Center_Id'],'Center')){
					logunfair("UnFair",'Center',$NewRowInfo['Center_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
