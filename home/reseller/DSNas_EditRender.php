<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSNasEditRender ..................................................................................");

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


$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update","SelectNasInfoName","SelectServiceBase"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSNasEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Nas.View");
				$Nas_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Nas_Id,ISEnable,NasName,INET_NTOA(NasIP) As NasIP,NASInfo_Id,Secret from Hnas where Nas_Id='$Nas_Id'";
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
				DSDebug(1,"DSNasEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.Nas.Add");
				$NewRowInfo=array();
				
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['NasName']=Get_Input('POST','DB','NasName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['NasIP']=Get_Input('POST','DB','NasIP','STR',7,15,0,0);
				$NewRowInfo['NASInfo_Id']=Get_Input('POST','DB','NASInfo_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("select NasInfoName from Hnasinfo where NasInfo_Id='".$NewRowInfo['NASInfo_Id']."'")=="")
					ExitError("پارامتر ردیوس نامعتبر انتخاب شده");
				$NewRowInfo['Secret']=Get_Input('POST','DB','Secret','STR',1,32,0,0);
				$NewRowInfo['FreeService_Id']=Get_Input('POST','DB','FreeService_Id','INT',0,4294967295,0,0);
				
				//check for change
				
				
				//----------------------
				$sql= "insert Hnas set ";
				$sql.="NasName='".$NewRowInfo['NasName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="NasIP=INET_ATON('".$NewRowInfo['NasIP']."'),";
				$sql.="NASInfo_Id='".$NewRowInfo['NASInfo_Id']."',";
				$sql.="Secret='".$NewRowInfo['Secret']."',";
				$sql.="FreeService_Id='".$NewRowInfo['FreeService_Id']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Nas_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','Nas',$RowId,'Nas');
				radiusapply();
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSNasEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Nas.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Nas_Id']=Get_Input('POST','DB','Nas_Id','INT',1,4294967295,0,0);
				$NewRowInfo['NasName']=Get_Input('POST','DB','NasName','STRENCHARNUMBER',1,32,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['NasIP']=Get_Input('POST','DB','NasIP','STR',7,15,0,0);
				$NewRowInfo['NASInfo_Id']=Get_Input('POST','DB','NASInfo_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("select NasInfoName from Hnasinfo where NasInfo_Id='".$NewRowInfo['NASInfo_Id']."'")=="")
					ExitError("پارامتر ردیوس نامعتبر انتخاب شده");				
				
				$NewRowInfo['Secret']=Get_Input('POST','DB','Secret','STR',1,32,0,0);
				$NewRowInfo['FreeService_Id']=Get_Input('POST','DB','FreeService_Id','INT',0,4294967295,0,0);


				$OldRowInfo= LoadRowInfo("Hnas","Nas_Id='".$NewRowInfo['Nas_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				$ISChange=DBSelectAsString("Select NasName<>'".$NewRowInfo['NasName']."' or NasIP<>INET_ATON('".$NewRowInfo['NasIP']."') Or Secret<>'".$NewRowInfo['Secret']."' From Hnas Where Nas_Id=".$NewRowInfo['Nas_Id']);

				//----------------------
				$sql= "Update Hnas set  ";
				$sql.="NasName='".$NewRowInfo['NasName']."',";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="NasIP=INET_ATON('".$NewRowInfo['NasIP']."'),";
				$sql.="NASInfo_Id='".$NewRowInfo['NASInfo_Id']."',";
				$sql.="Secret='".$NewRowInfo['Secret']."',";
				$sql.="FreeService_Id='".$NewRowInfo['FreeService_Id']."'";
				$sql.=" Where ";
				$sql.="(Nas_Id='".$NewRowInfo['Nas_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Nas',$NewRowInfo['Nas_Id'],'Nas',"Update Fail,Table=Nas affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Nas affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Nas',$NewRowInfo['Nas_Id'],'Nas')){
					logunfair("UnFair",'Nas',$NewRowInfo['Nas_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else{
					echo "OK~";
					DSDebug(2,"ISChange=$ISChange");
					if($ISChange==1){
						DSDebug(2,'Run radiusapply');
						radiusapply();
					}	
				}
        break;
	case "SelectNasInfoName":
				DSDebug(1,"DSNasEditRender SelectNasInfoName *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT NasInfo_Id,NasInfoName FROM Hnasinfo order by NasInfoName ASC","","NasInfo_Id,NasInfoName","","");
	
        break;
    case "SelectServiceBase":
				DSDebug(1,"DSActiveDirectoryEditRender-> SelectServiceBase *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="Select 0 As FreeService_Id,'هیچی' As ServiceName union (Select Service_Id As FreeService_Id,ServiceName From Hservice ".
					"Where (ServiceType='Base')and(IsDel='No') order by ServiceName)";
				$options->render_sql($sql,"","FreeService_Id,ServiceName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
