<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSVispEditRender ..................................................................................");

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


$act=Get_Input('GET','DB','act','ARRAY',array('load','insert','update','LoadCommission','UpdateCommission'),0,0,0);

try {
switch ($act) {
    case 'load':
				DSDebug(1,'DSVispEditRender Load ********************************************');
				exitifnotpermit(0,'Admin.VISPs.View');
				$Visp_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT Visp_Id,VispName,ISEnable,UsernamePattern from Hvisp where Visp_Id='$Visp_Id'";
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
				exitifnotpermit(0,"Admin.VISPs.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['VispName']=Get_Input('POST','DB','VispName','STR',1,32,0,0);
				$NewRowInfo['UsernamePattern']=Get_Input('POST','DB','UsernamePattern','STR',1,250,0,0);
				//----------------------
				$sql= "insert Hvisp set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="VispName='".$NewRowInfo['VispName']."',";
				$sql.="UsernamePattern='".$NewRowInfo['UsernamePattern']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Visp_Id']=$RowId;
				//$res = $conn->sql->query("Insert Ignore Hreseller_permit(Reseller_Id,Visp_Id,ISPermit,PermitItem_Id) SELECT rg.Reseller_Id, v.Visp_Id, if(rg.Reseller_Id=1,'Yes','No'), PermitItem_Id FROM Hpermititem pi, Hreseller rg,Hvisp v WHERE (PermitGroup = 'Visp')And(v.Visp_Id='$RowId')");

				logdbinsert($NewRowInfo,'Add','Visp',$RowId,'Visp');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSVispEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.VISPs.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Visp_Id']=Get_Input('POST','DB','Visp_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['VispName']=Get_Input('POST','DB','VispName','STR',1,32,0,0);
				$NewRowInfo['UsernamePattern']=Get_Input('POST','DB','UsernamePattern','STR',1,250,0,0);

				$OldRowInfo= LoadRowInfo("Hvisp","Visp_Id='".$NewRowInfo['Visp_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "Update Hvisp set ";
				$sql.="VispName='".$NewRowInfo['VispName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="UsernamePattern='".$NewRowInfo['UsernamePattern']."'";
				$sql.=" Where ";
				$sql.="(Visp_Id='".$NewRowInfo['Visp_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Visp',$NewRowInfo['Visp_Id'],'Visp',"Update Fail,Table=visp affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=visp affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Visp',$NewRowInfo['Visp_Id'],'Visp')){
					logunfair("UnFair",'Visp',$NewRowInfo['Visp_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
    case "LoadCommission":
				DSDebug(1,"DSVispEditRender LoadSupproterCommission ********************************************");
				exitifnotpermit(0,"Admin.VISPs.Commission.View");
				$Visp_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="Select ResellerCF,ChargerCF,MinPriceChangeReseller  from Hvisp where Visp_Id='$Visp_Id'";
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
    case "UpdateCommission":
				DSDebug(1,"DSVispEditRender LoadSupproterCommission ********************************************");
				exitifnotpermit(0,"Admin.VISPs.Commission.View");
				$Visp_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);

				$ResellerCF=Get_Input('POST','DB','ResellerCF','STR',1,64,0,0);
				$ISFormulaOK=DBSelectAsString("Select '$ResellerCF' REGEXP '[0-9.,]*'");
				if($ISFormulaOK!=1)
					ExitError("فرمول پورسانت نماینده فروش اشتباه است");

				$ChargerCF=Get_Input('POST','DB','ChargerCF','STR',1,64,0,0);
				$ISFormulaOK=DBSelectAsString("Select '$ChargerCF' REGEXP '[0-9.,]*'");
				if($ISFormulaOK!=1)
					ExitError("فرمول پورسانت شارژر اشتباه است");

				$MinPriceChangeReseller=Get_Input('POST','DB','MinPriceChangeReseller','PRC',1,14,0,0);
				
				//----------------------
				$sql= "Update Hvisp set ";
				$sql.="ResellerCF='$ResellerCF', ";
				$sql.="ChargerCF='$ChargerCF', ";
				$sql.="MinPriceChangeReseller='$MinPriceChangeReseller' ";
				$sql.="Where (Visp_Id=$Visp_Id)";
				$ar=DBUpdate($sql);
				logdb('Edit','Visp',$Visp_Id,'Visp',"ChargerCF=$ChargerCF Visp_Id=$Visp_Id ResellerCF=$GeneralRCF");
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
