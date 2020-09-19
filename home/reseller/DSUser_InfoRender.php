<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSUserInfoEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","update"),0,0,0);

switch ($act) {
    case "load":
				DSDebug(1,"DSUserInfoEditRender Load ********************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				//exitifnotpermit(0,"CRM.Service.Load"); //Visp.user.Account.View
				$sql="Select '' As Error,User_Id,Visp_Id,UserType,AdslPhone,NationalCode,Name,Family,{$DT}datestr(BirthDate) as BirthDate,Organization,Phone,Mobile,Address,Comment,NOE";
				$sql.=" from Huser where User_Id='$User_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				if($data)
					exitifnotpermit($data["Visp_Id"],"Visp.UserInfo.View.TabView");
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data){
					foreach ($data as $Field=>$Value)
						if(function ISPermit($Visp_Id,$PermitItemName){
						GenerateLoadField($Field,$Value);
				}		
				echo '</data>';
       break;
    case "update":
				DSDebug(1,"DSUserInfoEditRender Update ******************************************");
				$NewRowInfo=array();
				$NewRowInfo['User_Id']=Get_Input('POST','DB','User_Id','INT',1,4294967295,0,0);
				$NewRowInfo['UserType']=Get_Input('POST','DB','UserType','ARRAY',array('LAN','ADSL','Wireless','Wi-Fi','WiFiMobile','Dialup','Dialup-PRM'),0,0,0);
				if($NewRowInfo['UserType']=='ADSL') 
					$NewRowInfo['AdslPhone']=Get_Input('POST','DB','AdslPhone','STR',10,16,0,0);
				else
					$NewRowInfo['AdslPhone']=Get_Input('POST','DB','AdslPhone','STR',0,16,0,0);
				
				$NewRowInfo['NationalCode']=Get_Input('POST','DB','NationalCode','STR',0,16,0,0);
				$NewRowInfo['Name']=Get_Input('POST','DB','Name','STR',0,16,0,0);
				$NewRowInfo['Family']=Get_Input('POST','DB','Family','STR',0,16,0,0);
				$NewRowInfo['BirthDate']=Get_Input('POST','DB','BirthDate','DateOrBlank',0,0,0,0);
				/*
				if($NewRowInfo['BirthDate']<>''){
					if($DT=='sh')
						$BirthDate=DBSelectAsString("Select shdatestrtomstr('".$NewRowInfo['BirthDate']."')");
					else
						$BirthDate=DBSelectAsString("Select DATE('".$NewRowInfo['BirthDate']."')");
					if($BirthDate=='')
						ExitError("Invalid BirthDate".$NewRowInfo['BirthDate']."->$BirthDate");
						
				}
				*/
				$NewRowInfo['Organization']=Get_Input('POST','DB','Organization','STR',0,16,0,0);
				$NewRowInfo['Phone']=Get_Input('POST','DB','Phone','STR',0,16,0,0);
				$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,16,0,0);
				$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,16,0,0);
				$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,16,0,0);
				
				$OldRowInfo= LoadRowInfo("user","User_Id='".$NewRowInfo['User_Id']."'");				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "update Huser set SKey=floor(rand() * 4000000000),";
				$sql.="UserType='".$NewRowInfo['UserType']."',";
				$sql.="AdslPhone='".$NewRowInfo['AdslPhone']."',";
				$sql.="NationalCode='".$NewRowInfo['NationalCode']."',";
				$sql.="Name='".$NewRowInfo['Name']."',";
				$sql.="Family='".$NewRowInfo['Family']."',";
				$sql.="BirthDate='$BirthDate',";
				$sql.="Organization='".$NewRowInfo['Organization']."',";
				$sql.="Phone='".$NewRowInfo['Phone']."',";
				$sql.="Mobile='".$NewRowInfo['Mobile']."',";
				$sql.="Address='".$NewRowInfo['Address']."',";
				$sql.="NOE='".$NewRowInfo['NOE']."'";

				$sql.=" Where (User_Id='".$NewRowInfo['User_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','User',$NewRowInfo['User_Id'],'User',"Update Fail,Table=User affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=User affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'User',$NewRowInfo['User_Id'],'User')){
					logunfair("UnFair",'User',$NewRowInfo['User_Id'],'',"User Info");
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
