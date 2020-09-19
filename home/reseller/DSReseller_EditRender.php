<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSResellerEditRender .........................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();



$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update","ChangePass","SelectParentReseller"),0,0,0);

switch ($act) {
    case "load":
				DSDebug(1,"DSResellerEditRender Load ******************************************");
				exitifnotpermit(0,"CRM.Reseller.View");
				$Reseller_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				if($Reseller_Id==$LReseller_Id)	
					ExitError('شما نمیتوانید اطلاعات خود را ویراش کرده و یا ببینید');
				ExitIfNotPermitRowAccess('reseller',$Reseller_Id);
				
				$sql="SELECT '' As Error,Reseller_Id,ParentReseller_Id,ISEnable,ISManager,ISOperator,ResellerName,{$DT}DateTimeStr(ResellerCDT) as ResellerCDT,".
					// "{$DT}DateTimeStr(LastLoginDT) as LastLoginDT,inet_ntoa(LastLoginIP) as LastLoginIP,".
					"SharePercent,SessionTimeout,PermitIP,NoneBlockIP,Name,Family,Mobile,Phone,Address ".
						"from Hreseller where (Reseller_Id='$Reseller_Id')";
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
				DSDebug(1,"DSResellerEditRender Insert ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Add");
				
				$NewRowInfo=array();
				$NewRowInfo['ResellerName']=Get_Input('POST','DB','ResellerName','STR',1,32,0,0);//STRENCHARNUMBER
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ISManager']=Get_Input('POST','DB','ISManager','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ISOperator']=Get_Input('POST','DB','ISOperator','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ParentReseller_Id']=Get_Input('POST','DB','ParentReseller_Id','INT',1,4294967295,0,0);
				ExitIfNotPermitRowAccess("reseller-parent",$NewRowInfo['ParentReseller_Id']);
				$NewRowInfo['SharePercent']=Get_Input('POST','DB','SharePercent','INT',0,100,0,0);
				$NewRowInfo['SessionTimeout']=Get_Input('POST','DB','SessionTimeout','INT',600,99999999,0,0);
				$NewRowInfo['PermitIP']=Get_Input('POST','DB','PermitIP','STR',9,255,0,0);
				$NewRowInfo['NoneBlockIP']=Get_Input('POST','DB','NoneBlockIP','STR',9,255,0,0);
				$NewRowInfo['Name']=Get_Input('POST','DB','Name','STR',0,32,0,0);
				$NewRowInfo['Family']=Get_Input('POST','DB','Family','STR',0,32,0,0);
				$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,15,0,0);
				$NewRowInfo['Phone']=Get_Input('POST','DB','Phone','STR',0,100,0,0);
				$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,255,0,0);
				
				//----------------------
				$sql= "insert Hreseller set ";
				$sql.="ResellerName='".$NewRowInfo['ResellerName']."',";
				$sql.="ResellerCDT=Now(),";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="ISManager='".$NewRowInfo['ISManager']."',";
				$sql.="ISOperator='".$NewRowInfo['ISOperator']."',";
				$sql.="ParentReseller_Id='".$NewRowInfo['ParentReseller_Id']."',";
//				$sql.="ResellerPath='".$NewRowInfo['ResellerPath']."',";
				$sql.="Pass=floor(rand() * 4000000000),";//rand fill
				$sql.="Salt=floor(rand() * 4000000000),";//rand fill
				$sql.="PermitIP='".$NewRowInfo['PermitIP']."',";
				$sql.="NoneBlockIP='".$NewRowInfo['NoneBlockIP']."',";
				$sql.="SharePercent='".$NewRowInfo['SharePercent']."',";
				$sql.="SessionTimeout='".$NewRowInfo['SessionTimeout']."',";
				$sql.="Name='".$NewRowInfo['Name']."',";
				$sql.="Family='".$NewRowInfo['Family']."',";
				$sql.="Mobile='".$NewRowInfo['Mobile']."',";
				$sql.="Phone='".$NewRowInfo['Phone']."',";
				$sql.="Address='".$NewRowInfo['Address']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Reseller_Id']=$RowId;

				$ResellerPath=DBSelectAsString("Select Concat(ResellerPath,Reseller_Id,'>') from Hreseller Where Reseller_Id=".$NewRowInfo['ParentReseller_Id']);
				//DBUpdate("Update Hreseller Set ResellerPath=Concat('$TopResellerPath', LPAD($RowId,6,'00000'),'>') Where Reseller_Id=$RowId");
				DBUpdate("Update Hreseller Set ResellerPath='$ResellerPath' Where Reseller_Id=$RowId");
				// DBUpdate("Create View Vreseller_permit$RowId as select * from Hreseller_permit where Reseller_Id='$RowId';");
				/*	
				Update Hreseller r left join Hreseller tr on(r.ParentReseller_Id=tr.Reseller_Id)
				Set r.ResellerPath=Concat(tr.ResellerPath,r.Reseller_Id,'>')
				Where r.Reseller_Id>1
				*/	
				logdbinsert($NewRowInfo,'Add','Reseller',$RowId,"Reseller");
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSResellerEditRender Update ******************************************");
				exitifnotpermit(0,"CRM.Reseller.Edit");
				$NewRowInfo=array();
				$NewRowInfo['Reseller_Id']=Get_Input('POST','DB','Reseller_Id','INT',1,4294967295,0,0);
				if(intval($NewRowInfo['Reseller_Id'])==$LReseller_Id)
					ExitError('شما نمیتوانید اطلاعات خود را ویراش کرده و یا ببینید');
				ExitIfNotPermitRowAccess('reseller',$NewRowInfo['Reseller_Id']);

				$NewRowInfo['SharePercent']=Get_Input('POST','DB','SharePercent','INT',0,100,0,0);
				$NewRowInfo['SessionTimeout']=Get_Input('POST','DB','SessionTimeout','INT',600,99999999,0,0);
				$NewRowInfo['ResellerName']=Get_Input('POST','DB','ResellerName','STR',1,32,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ISManager']=Get_Input('POST','DB','ISManager','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['ISOperator']=Get_Input('POST','DB','ISOperator','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['PermitIP']=Get_Input('POST','DB','PermitIP','STR',9,255,0,0);
				$NewRowInfo['NoneBlockIP']=Get_Input('POST','DB','NoneBlockIP','STR',9,255,0,0);
				$NewRowInfo['Name']=Get_Input('POST','DB','Name','STR',0,32,0,0);
				$NewRowInfo['Family']=Get_Input('POST','DB','Family','STR',0,32,0,0);
				$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,15,0,0);
				$NewRowInfo['Phone']=Get_Input('POST','DB','Phone','STR',0,100,0,0);
				$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,255,0,0);
				
				if($NewRowInfo['ISOperator']=='Yes'){
					$n=DBSelectAsString("SELECT Count(1) from Huser Where Reseller_Id=".$NewRowInfo['Reseller_Id']);
					if($n>0)
						ExitError("کاربر است $n این نماینده فروش دارای");
				}

				$OldRowInfo=LoadRowInfoSql("SELECT Reseller_Id,ISEnable,ISOperator,ISManager,ParentReseller_Id,ResellerName,{$DT}DateTimeStr(ResellerCDT) as ResellerCDT,PermitIP,Name,SharePercent,SessionTimeout,Family,Mobile,Phone,Address from Hreseller where Reseller_Id=".$NewRowInfo['Reseller_Id']);
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				
				$sql= "Update Hreseller set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="ISManager='".$NewRowInfo['ISManager']."',";
				$sql.="ISOperator='".$NewRowInfo['ISOperator']."',";
				$sql.="PermitIP='".$NewRowInfo['PermitIP']."',";
				$sql.="NoneBlockIP='".$NewRowInfo['NoneBlockIP']."',";
				$sql.="SharePercent='".$NewRowInfo['SharePercent']."',";
				$sql.="SessionTimeout='".$NewRowInfo['SessionTimeout']."',";
				$sql.="Name='".$NewRowInfo['Name']."',";
				$sql.="Family='".$NewRowInfo['Family']."',";
				$sql.="Mobile='".$NewRowInfo['Mobile']."',";
				$sql.="Phone='".$NewRowInfo['Phone']."',";
				$sql.="Address='".$NewRowInfo['Address']."'";
				$sql.=" Where ";
				$sql.="(Reseller_Id='".$NewRowInfo['Reseller_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','Reseller',$NewRowInfo['Reseller_Id'],'Reseller',"Update Fail,Table=reseller affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=visp affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Reseller',$NewRowInfo['Reseller_Id'],'Reseller')){
					logunfair("UnFair",'Reseller',$NewRowInfo['Reseller_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
    case "ChangePass": 
				DSDebug(1,"DSResellerEditRender ChangePass ******************************************");
				$Reseller_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				ExitIfNotPermitRowAccess('reseller',$Reseller_Id);
				
				$enpass=Get_Input('POST','DB','enpass','STR',128,128,0,0);//Get_POST($mysqli,"enpass");
				$Salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
				$Pass = hash('sha512', $enpass.$Salt);
				$sql =" Update Hreseller set ";
				$sql.=" Salt='$Salt',";
				$sql.=" Pass='$Pass'";
				$sql.=" Where ";
				$sql.=" Reseller_Id=$Reseller_Id";
				$res = $conn->sql->query($sql);
				logdb('Edit','Reseller',$Reseller_Id,'Reseller',"Password Changed");
				echo "OK~";
		
        break;
    case "SelectParentReseller":
				DSDebug(1,"DSResellerEditRender SelectParentReseller *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$Reseller_Id=Get_Input('GET','DB','id','INT',0,4294967295,0,0);//0 means create new Hreseller
				if($LISManager=='Yes')
					$options->render_sql("SELECT Reseller_Id,ResellerName FROM Hreseller where (ResellerPath Like '$LResellerPath%') order by ResellerPath ASC",
						"","Reseller_Id,ResellerName","","");
				else	
					$options->render_sql("SELECT Reseller_Id,ResellerName FROM Hreseller where (Reseller_Id=$LReseller_Id Or ResellerPath Like '$LResellerPath{$LReseller_Id}>%') order by ResellerPath ASC",
						"","Reseller_Id,ResellerName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)

} catch (Exception $e) {
ExitError($e->getMessage());
}

//--------------------------------
?>
