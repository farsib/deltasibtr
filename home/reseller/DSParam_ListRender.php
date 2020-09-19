<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSParam_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array('SelectCalledId','SelectDebitControl','SelectWebAccess','SelectActiveDirectory','SelectOffFormula','SelectLoginTime','SelectIPPool','SelectTrafficRate','SelectTimeRate','SelectMikrotikRate','SelectFinishRule','SelectSMSProvider','SelectReplyParamItem',"list", "SelectParamItem","insert","LoadParamForm","update","GetParamInfo","Delete",'SelectAuthParamItem','SelectAccParamItem','SelectHelperParamItem','SelectServiceInfo','SelectNotifyCreditFinish','SelectNotifyServiceExpire','SelectNotifyUserDebit'),0,0,0);
$ParamItemGroup=Get_Input('GET','DB','ParamItemGroup','ARRAY',array('Radius','Nas','Server','Center','Visp','Reseller','Service','Class','User'),0,0,0);
$TableName=strtolower($ParamItemGroup);
$TableId=Get_Input('GET','DB','TableId','INT',1,4294967295,0,0);
switch ($act) {
    case "list":
				DSDebug(0,"DSParam_ListRender->List ********************************************");
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.List");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.List");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.List");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.List");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.List");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				function color_rows($row){
					global $TableId,$ParamItemGroup;
					$ParamItemType = $row->get_value("ParamItemType");
					If($ParamItemType=='Reply'){
						$Format1="color:green";
					}
					elseif($ParamItemGroup=='User'){
						$ParamItem_Id = $row->get_value("ParamItem_Id");
						$Param_Id=DBSelectAsString("Select FindActiveParamIdByUserId($ParamItem_Id,$TableId)");
						if($Param_Id==$row->get_value("Param_Id"))
							$Format1="color:blue";
						else
							$Format1="color:#001122";
					}
					else
						$Format1="";
					
					$data = $row->get_value("ParamStatus");
					if($data=='Passthrough-Yes')
						$Format2="font-weight:Normal";
					elseif($data=='Passthrough-No')
						$Format2="font-weight:Bold";
					else
						$Format2="font-style:Italic";
					$row->set_row_style("$Format1;$Format2");	
				}

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				
				if(isset($_GET['act2'])){//user press retrieveall in Huserpage  TableId is User_Id
					$sql="SELECT Param_Id,ParamItemType,ParamStatus,Mp.ParamItem_Id,ParamItemName,ServiceInfoName,Value,TableName,TableId From ".
						"Hparam Mp Left join Hparamitem Mpi on(Mp.ParamItem_Id=Mpi.ParamItem_Id) ".
						"Left join Hserviceinfo si on (Mp.ServiceInfo_Id=si.ServiceInfo_Id)".
						"Left join Huser Mu_u on(Mu_u.User_Id=$TableId) ".
						"Where (ParamStatus<>'Disable') ".
						"And( ".
						"(TableName='Server') ".
						"Or (TableName='Center' And TableId=Mu_u.Center_Id) ".
						"Or (TableName='Reseller' And TableId=Mu_u.Reseller_Id) ".
						"Or (TableName='Visp' And TableId=Mu_u.Visp_Id ) ".
						"Or (TableName='Service' And TableId=Mu_u.Service_Id) ".
						"Or (TableName='Class' And TableId in(Select Class_Id from Huser_class Where User_Id=$TableId And Checked='Yes')) ".
						"or (TableName='User' And TableId=$TableId)) ".
						"$sqlfilter Order By TableName Asc ";
					DSGridRender_Sql(100,$sql,"Param_Id","Param_Id,ParamItemType,ParamStatus,ParamItemName,ServiceInfoName,Value,TableName,TableId","ParamItem_Id","","color_rows");
				}	
				else{	
					$sql="SELECT Param_Id,ParamItemType,ParamStatus,Mp.ParamItem_Id,ParamItemName,ServiceInfoName,Value,TableName,TableId from ".
						"Hparam Mp Left Join Hparamitem Mpi on Mp.ParamItem_Id=Mpi.ParamItem_Id ".
						"Left join Hserviceinfo si on (Mp.ServiceInfo_Id=si.ServiceInfo_Id)".
						"Where (TableName='$TableName')And(TableId=$TableId)" .$sqlfilter." $SortStr ";
					DSGridRender_Sql(100,$sql,"Param_Id","Param_Id,ParamItemType,ParamStatus,ParamItemName,ServiceInfoName,Value,TableName,TableId","ParamItem_Id","","color_rows");
				}
       break;
    case "LoadParamForm":
				DSDebug(1,"DSParam_ListRender Load ********************************************");
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.List");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.List");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.List");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.List");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.List");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.List");
					
				$Param_Id=Get_Input('GET','DB','Param_Id','INT',1,4294967295,0,0);
/*
				ParamItemType?Check reply
				ParamStatus
				CheckParamItemName
				ReplyParamItem_Id
				Value
				LoginTime_Id
				.
				.
				
	*/			
				$sql="SELECT Param_Id,ParamItemType,ParamStatus,ParamItemName,".
					"If(ParamItemType='Check',ParamItemName,'') As CheckParamItemName, ".
					"Mp.ServiceInfo_Id, ".
					"Mp.ParamItem_Id As ReplyParamItem_Id, ".
					"If(ParamItemName='Simulation',Value,'') As Simulation, ".
					"If(ParamItemName='InterimTime',Value,'') As InterimTime, ".
					"If(ParamItemName='LoginTime',(Select LoginTime_Id From Hlogintime Where LoginTimeName=Value),0) As LoginTime_Id, ".
					"If(ParamItemName='MikrotikRate',(Select MikrotikRate_Id From Hmikrotikrate Where MikrotikRateName=Value),0) As MikrotikRate_Id, ".
					"If(ParamItemName='TimeRate',(Select TimeRate_Id From Htimerate Where TimeRateName=Value),0) As TimeRate_Id, ".
					"If(ParamItemName='TrafficRate',(Select TrafficRate_Id From Htrafficrate Where TrafficRateName=Value),0) As TrafficRate_Id, ".
					"If(ParamItemName='ReceiveRate',Value,'') As ReceiveRate, ".
					"If(ParamItemName='SendRate',Value,'') As SendRate, ".
					"If(ParamItemName='Calendar',Value,0) As Calendar, ".
					"If(ParamItemName='PeriodicUse',Value,0) As PeriodicUse, ".
					"If(ParamItemName='IPPool',(Select IPPool_Id From Hippool Where IPPoolName=Value),0) As IPPool_Id, ".
					"If(ParamItemName='FinishRule',(Select FinishRule_Id From Hfinishrule Where FinishRuleName=Value),0) As FinishRule_Id, ".
					"If(ParamItemName='SMSProvider',(Select SMSProvider_Id From  Hsmsprovider Where SMSProviderName=Value),0) As SMSProvider_Id, ".
					"If(ParamItemName='OffFormula',(Select OffFormula_Id From Hoffformula Where OffFormulaName=Value),0) As OffFormula_Id, ".
					"If(ParamItemName='MaxSessionTime',Value,0) As MaxSessionTime, ".
					"If(ParamItemName='URLReporting',Value,'') As URLReporting, ".
					"If(ParamItemName='ActiveDirectory',(Select ActiveDirectory_Id From Hactivedirectory Where ActiveDirectoryName=Value),0) As ActiveDirectory_Id, ".
					"If(ParamItemName='AuthMethod',Value,'') As AuthMethod, ".
					"If(ParamItemName='UserType',Value,'') As UserType, ".
					"If(ParamItemName='Notify-CreditFinish',(Select Notify_Id From Hnotify Where NotifyName=Value),0) As NotifyCreditFinish_Id, ".
					"If(ParamItemName='Notify-ServiceExpire',(Select Notify_Id From Hnotify Where NotifyName=Value),0) As NotifyServiceExpire_Id, ".
					"If(ParamItemName='Notify-UserDebit',(Select Notify_Id From Hnotify Where NotifyName=Value),0) As NotifyUserDebit_Id, ".
					"If(ParamItemName='WebAccess',(Select WebAccess_Id From  Hwebaccess Where WebAccessName=Value),0) As WebAccess_Id, ".
					"If(ParamItemName='DebitControl',(Select DebitControl_Id From  Hdebitcontrol Where DebitControlName=Value),0) As DebitControl_Id, ".
					"If(ParamItemName='CalledId',(Select CalledId_Id From  Hcalledid Where CalledIdName=Value),0) As CalledId_Id, ".
					"If(ParamItemName='AutoResetExtraCredit',Value,'') As AutoResetExtraCredit, ".
					"Value,Comment from ".
					"Hparam Mp Left Join Hparamitem Mpi on Mp.ParamItem_Id=Mpi.ParamItem_Id ".
					"Where (Param_Id=$Param_Id)";
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
				DSDebug(1,"DSParam_ListRender Insert ******************************************");
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.Add");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.Add");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.Add");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.Add");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.Add");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.Add");
				$NewRowInfo=array();
				$ParamItemType=Get_Input('POST','DB','ParamItemType','ARRAY',array("Acc",'Auth','Helper',"Reply"),0,0,0);
				if($ParamItemType=='Helper'){
					$NewRowInfo['ParamStatus']="Disable";
					$NewRowInfo['Value']='';
					$NewRowInfo['ParamItem_Id']=Get_Input('POST','DB','HelperParamItem_Id','INT',1,4294967295,0,0);
					$MaxPerItem=DBSelectAsString("Select MaxPerItem from Hparamitem where ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
					$NoItem=DBSelectAsString("Select count(*) from Hparam where (TableName='$TableName')And(TableId='$TableId') And ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
					if($NoItem>=$MaxPerItem) ExitError("فقط ".$MaxPerItem." مورد از این پارامتر مجاز است");
				}	
				if($ParamItemType=='Auth'){
					$NewRowInfo['ParamStatus']="Disable";
					$NewRowInfo['Value']='';
					$NewRowInfo['ParamItem_Id']=Get_Input('POST','DB','AuthParamItem_Id','INT',1,4294967295,0,0);
					$MaxPerItem=DBSelectAsString("Select MaxPerItem from Hparamitem where ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
					$NoItem=DBSelectAsString("Select count(*) from Hparam where (TableName='$TableName')And(TableId='$TableId') And ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
					if($NoItem>=$MaxPerItem) ExitError("فقط ".$MaxPerItem." مورد از این پارامتر مجاز است");
				}	
				if($ParamItemType=='Acc'){
					$NewRowInfo['ParamStatus']="Disable";
					$NewRowInfo['Value']='';
					$NewRowInfo['ParamItem_Id']=Get_Input('POST','DB','AccParamItem_Id','INT',1,4294967295,0,0);
					if($NewRowInfo['ParamItem_Id']==7 || $NewRowInfo['ParamItem_Id']==8){
						$MaxPerItem=DBSelectAsString("Select MaxPerItem from Hparamitem where ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
						$NoItem=DBSelectAsString("Select count(*) from Hparam where (TableName='$TableName')And(TableId='$TableId') And ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
						if($NoItem>=$MaxPerItem) ExitError("فقط ".$MaxPerItem." مورد از این پارامتر مجاز است");
					}	
				}	
				else if($ParamItemType=='Reply'){
					$NewRowInfo['ParamStatus']='Disable';
					$NewRowInfo['ParamItem_Id']=Get_Input('POST','DB','ReplyParamItem_Id','INT',1,4294967295,0,0);
					$NewRowInfo['Value']='';
					$MaxPerItem=DBSelectAsString("Select MaxPerItem from Hparamitem where ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
					$NoItem=DBSelectAsString("Select count(*) from Hparam where (TableName='$TableName')And(TableId='$TableId') And ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
					if($NoItem>=$MaxPerItem) ExitError("فقط ".$MaxPerItem." مورد از این پارامتر مجاز است");
				}
				
				$sql= "insert Hparam set ";
				$sql.="ParamItem_Id='".$NewRowInfo['ParamItem_Id']."',";
				$sql.="TableName='$TableName',";
				$sql.="TableId=$TableId,";
				$sql.="ParamStatus='".$NewRowInfo['ParamStatus']."',";
				$sql.="ServiceInfo_Id=1,";//do not del!!!!!!!!!!!!!!!!!!
				$sql.="Value='".$NewRowInfo['Value']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['Param_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add',$ParamItemGroup,$TableId,'Param');
				echo "OK~$RowId~";
        break;
    case "update": 
				DSDebug(1,"DSParam_ListRender Update ******************************************");
				$NewRowInfo=array();
				$NewRowInfo['Param_Id']=Get_Input('POST','DB','Param_Id','INT',1,4294967295,0,0);
				if($ParamItemGroup<>DBSelectAsString("Select TableName from Hparam where Param_Id=".$NewRowInfo['Param_Id']))
					ExitError("این سطر در اینجا قابل ویرایش نیست");
				$TableId=DBSelectAsString("Select TableId from Hparam where Param_Id=".$NewRowInfo['Param_Id']);
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.Edit");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.Edit");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.Edit");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.Edit");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.Edit");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.Edit");

				
				$NewRowInfo['ParamStatus']=Get_Input('POST','DB','ParamStatus','ARRAY',array("Passthrough-Yes","Passthrough-No","Disable"),0,0,0);
				$NewRowInfo['ParamItem_Id']=DBSelectAsString("Select ParamItem_Id from Hparam where Param_Id=".$NewRowInfo['Param_Id']);
				$ParamItemName=DBSelectAsString("Select ParamItemName from Hparamitem where ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
				
				$MaxPerItem=DBSelectAsString("Select MaxPerItem from Hparamitem where ParamItem_Id=".$NewRowInfo['ParamItem_Id']);
				if($ParamItemName=='TimeRate' ||$ParamItemName=='TrafficRate' ||$ParamItemName=='ReceiveRate' ||$ParamItemName=='SendRate'){
					$NewRowInfo['ServiceInfo_Id']=Get_Input('POST','DB','ServiceInfo_Id','INT',0,999999,0,0);
					$NoItem=DBSelectAsString("Select count(*) from Hparam where (TableName='$TableName')And(TableId='$TableId') And ParamItem_Id=".$NewRowInfo['ParamItem_Id'].' and ServiceInfo_Id='.$NewRowInfo['ServiceInfo_Id']);
				}
				else{
					$NewRowInfo['ServiceInfo_Id']=1;
					$NoItem=DBSelectAsString("Select count(*) from Hparam where (TableName='$TableName')And(TableId='$TableId') And ParamItem_Id=".$NewRowInfo['ParamItem_Id']." And Param_Id<>".$NewRowInfo['Param_Id']);
				}	
				$OldServiceInfo_Id=DBSelectAsString("Select ServiceInfo_Id from Hparam where Param_Id=".$NewRowInfo['Param_Id']);
				if($OldServiceInfo_Id<>$NewRowInfo['ServiceInfo_Id'])
					if($NoItem>=$MaxPerItem) ExitError("فقط ".$MaxPerItem." مورد از این پارامتر مجاز است");
				
				switch ($ParamItemName) {					
					case 'Simulation':
						$NewRowInfo['Value']=Get_Input('POST','DB','Simulation','INT',1,999999,0,0);
						break;
					case 'InterimTime':
						$NewRowInfo['Value']=Get_Input('POST','DB','InterimTime','INT',1,999999,0,0);
						break;
					case 'AuthMethod':
						$NewRowInfo['Value']=Get_Input('POST','DB','AuthMethod','ARRAY',array('UP','UC','U','UPC','A','AC'),0,0,0);
						break;
					case 'LoginTime':
						$LoginTime_Id=Get_Input('POST','DB','LoginTime_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select LoginTimeName from Hlogintime where LoginTime_Id='$LoginTime_Id'");
						break;
					case 'MikrotikRate':
						$MikrotikRate_Id=Get_Input('POST','DB','MikrotikRate_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select MikrotikRateName from Hmikrotikrate where MikrotikRate_Id='$MikrotikRate_Id'");
						break;
					case 'TimeRate':
						$TimeRate_Id=Get_Input('POST','DB','TimeRate_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select TimeRateName from Htimerate where TimeRate_Id='$TimeRate_Id'");
						break;
					case 'TrafficRate':
						$TrafficRate_Id=Get_Input('POST','DB','TrafficRate_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select TrafficRateName from Htrafficrate where TrafficRate_Id='$TrafficRate_Id'");
						break;
					case 'ReceiveRate':
						$NewRowInfo['Value']=Get_Input('POST','DB','ReceiveRate','FLT',0,1,0,0);
						If($NewRowInfo['Value']<0 Or $NewRowInfo['Value']>1) 
							ExitError("خطای محدوده ضریب دریافت.[0,1.00]");
						break;
					case 'SendRate':
						$NewRowInfo['Value']=Get_Input('POST','DB','SendRate','FLT',0,1,0,0);
						If($NewRowInfo['Value']<0 Or $NewRowInfo['Value']>1) 
							ExitError("خطای محدوده ضریب ارسال.[0,1.00]");
						break;
					case 'Calendar':
						$NewRowInfo['Value']=Get_Input('POST','DB','Calendar','ARRAY',array("Jalali","Gregorian"),0,0,0);
						break;
					case 'PeriodicUse':
						$NewRowInfo['Value']=Get_Input('POST','DB','PeriodicUse','ARRAY',array("Fix","Relative"),0,0,0);
						break;
					case 'IPPool':
						$IPPool_Id=Get_Input('POST','DB','IPPool_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select IPPoolName from Hippool where IPPool_Id='$IPPool_Id'");
						break;
					case 'FinishRule':
						$FinishRule_Id=Get_Input('POST','DB','FinishRule_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select FinishRuleName from Hfinishrule where FinishRule_Id='$FinishRule_Id'");
						break;
					case 'SMSProvider':
						$SMSProvider_Id=Get_Input('POST','DB','SMSProvider_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select SMSProviderName from Hsmsprovider where SMSProvider_Id='$SMSProvider_Id'");
						break;
					case 'OffFormula':
						$OffFormula_Id=Get_Input('POST','DB','OffFormula_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select OffFormulaName from Hoffformula where OffFormula_Id='$OffFormula_Id'");
						break;
					case 'MaxSessionTime':
						$NewRowInfo['Value']=Get_Input('POST','DB','MaxSessionTime','INT',1,99999999,0,0);
						break;
					case 'URLReporting':
						$NewRowInfo['Value']=Get_Input('POST','DB','URLReporting','ARRAY',array("Yes","No"),0,0,0);
						break;
					case 'ActiveDirectory':
						$ActiveDirectory_Id=Get_Input('POST','DB','ActiveDirectory_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select ActiveDirectoryName from Hactivedirectory where ActiveDirectory_Id='$ActiveDirectory_Id'");
						break;
					case 'AuthMethod':
						$NewRowInfo['Value']=Get_Input('POST','DB','AuthMethod','ARRAY',array('UP','UC','U','UPC','A','AC'),0,0,0);
						break;
					case 'UserType':
						$NewRowInfo['Value']=Get_Input('POST','DB','UserType','ARRAY',array('LAN','ADSL','Wireless','Wi-Fi','WiFiMobile','Dialup','Dialup-PRM','NotLog'),0,0,0);
						break;
					case 'Notify-CreditFinish':
						$Notify_Id=Get_Input('POST','DB','NotifyCreditFinish_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select NotifyName from Hnotify where Notify_Id='$Notify_Id'");
						break;
					case 'Notify-ServiceExpire':
						$Notify_Id=Get_Input('POST','DB','NotifyServiceExpire_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select NotifyName from Hnotify where Notify_Id='$Notify_Id'");
						break;
					case 'Notify-UserDebit':
						$Notify_Id=Get_Input('POST','DB','NotifyUserDebit_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select NotifyName from Hnotify where Notify_Id='$Notify_Id'");
						break;
					case 'WebAccess':
						$WebAccess_Id=Get_Input('POST','DB','WebAccess_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select WebAccessName from Hwebaccess where WebAccess_Id='$WebAccess_Id'");
						break;
					case 'DebitControl':
						$DebitControl_Id=Get_Input('POST','DB','DebitControl_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select DebitControlName from Hdebitcontrol where DebitControl_Id='$DebitControl_Id'");
						break;
					case 'CalledId':
						$CalledId_Id=Get_Input('POST','DB','CalledId_Id','INT',1,4294967295,0,0);
						$NewRowInfo['Value']=DBSelectAsString("Select CalledIdName from Hcalledid where CalledId_Id='$CalledId_Id'");
						break;
					case 'AutoResetExtraCredit':
						$NewRowInfo['Value']=Get_Input('POST','DB','AutoResetExtraCredit','ARRAY',array("Yes","No"),0,0,0);
						break;
											
					default:   //ReplyItem
						$NewRowInfo['Value']=Get_Input('POST','DB','Value','STR',1,128,0,0);
						
				}//end of switch	

				$OldRowInfo= LoadRowInfo("Hparam","Param_Id='".$NewRowInfo['Param_Id']."'");
				$sql= "update Hparam set ";
				$sql.="ParamStatus='".$NewRowInfo['ParamStatus']."',";
				$sql.="ServiceInfo_Id='".$NewRowInfo['ServiceInfo_Id']."',";
				$sql.="Value='".$NewRowInfo['Value']."'";
				$sql.=" Where ";
				$sql.="(Param_Id='".$NewRowInfo['Param_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				/*
				if($ar!=1){//probably hack
					logdb('Edit','Service',$NewRowInfo['Service_Id'],'Param',"Update Fail,Table=Param affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Param affected row=0");
					ExitError("(ar=$ar) Security problem, Report Sent to Administrator");	
				}
					*/
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",$ParamItemGroup,$TableId,'Param')){
					logunfair("UnFair",$ParamItemGroup,$TableId,'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
		
    case "SelectParamItem":
				DSDebug(1,"DSParam_ListRender-> SelectParamItem *****************");
				require_once('../../lib/connector/options_connector.php');
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.List");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.List");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.List");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.List");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.List");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.List");
				//$ParamItemGroup=Get_Input('GET','DB','ParamItemGroup','ARRAY',array('Radius','Nas','Class','Service','User','Reseller','VISPs'),0,0,0);
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="Select 0 As ParamItem_Id,'Please Select From List' As ParamItemName union ".
					"SELECT ParamItem_Id,ParamItemName FROM Hparamitem ".
					"Where FIND_IN_SET('$ParamItemGroup',ParamItemGroup)>0 ";
				$options->render_sql($sql,"","ParamItem_Id,ParamItemName","","");
        break;
		
	case "GetParamInfo":
				DSDebug(1,"DSParam_ListRender-> GetParamInfo *****************");
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.List");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.List");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.List");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.List");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.List");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.List");
				$ParamItem_Id=Get_Input('GET','DB','ParamItem_Id','INT',-1,4294967295,0,0);
				$Regex=DBSelectAsString("Select ParamItemRegex From Hparamitem where ParamItem_Id=$ParamItem_Id");
				$Example=DBSelectAsString("Select Example From Hparamitem where ParamItem_Id=$ParamItem_Id");
				$Default=DBSelectAsString("Select Def From Hparamitem where ParamItem_Id=$ParamItem_Id");
				$Comment=DBSelectAsString("Select Comment From Hparamitem where ParamItem_Id=$ParamItem_Id");
				echo "$Regex`$Example`$Default`$Comment`";
					
		break;
	case "Delete":
				DSDebug(1,"DSParam_ListRender-> Delete *****************");
				if($ParamItemGroup=='User')
					exitifnotpermituser($TableId,"Visp.User.Param.Delete");
				else if($ParamItemGroup=='Reseller')
					exitifnotpermit(0,"CRM.Reseller.Param.Delete");
				else if($ParamItemGroup=='Service')
					exitifnotpermit(0,"CRM.Service.Param.Delete");
				else if($ParamItemGroup=='Visp')
					exitifnotpermit(0,"Admin.VISPs.Param.Delete");
				else if($ParamItemGroup=='Class')
					exitifnotpermit(0,"Admin.User.Class.Param.Delete");
				else
					exitifnotpermit(0,"Admin.$ParamItemGroup.Param.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Param_Id']=Get_Input('GET','DB','Param_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ParamItem_Id']=DBSelectAsString("Select ParamItem_Id from Hparam where Param_Id=".$NewRowInfo['Param_Id']);
				
				if($ParamItemGroup<>DBSelectAsString("Select TableName from Hparam where Param_Id=".$NewRowInfo['Param_Id']))
					ExitError("این سطر در اینجا قابل حذف نیست");
				$NewRowInfo['Name']=DBSelectAsString("Select ParamItemName from Hparamitem pi left join Hparam p on  pi.ParamItem_Id=p.ParamItem_Id where Param_Id=".$NewRowInfo['Param_Id']);
				$NewRowInfo['Value']=DBSelectAsString("Select value  from Hparam where Param_Id=".$NewRowInfo['Param_Id']);
				$ar=DBDelete('delete from Hparam Where Param_Id='.$NewRowInfo['Param_Id']);
				logdbdelete($NewRowInfo,'Delete',$ParamItemGroup,$TableId,'Param');
				echo "OK~";
		break;
	case 'SelectAuthParamItem':
				DSDebug(1,"DSParam_ListRender SelectAuthParamItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT ParamItem_Id,ParamItemName From Hparamitem Where ParamItemType='Auth' and FIND_IN_SET('$ParamItemGroup',ParamItemGroup)>0 Order by ParamItemName ASC","","ParamItem_Id,ParamItemName","","");
        break;
	case 'SelectReplyParamItem':
				DSDebug(1,"DSParam_ListRender SelectReplyParamItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT ParamItem_Id,ParamItemName From Hparamitem Where ParamItemType='Reply' and FIND_IN_SET('$ParamItemGroup',ParamItemGroup)>0 Order by ParamItemName ASC","","ParamItem_Id,ParamItemName","","");
        break;
	case 'SelectAccParamItem':
				DSDebug(1,"DSParam_ListRender SelectAccParamItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT ParamItem_Id,ParamItemName From Hparamitem Where ParamItemType='Acc' and FIND_IN_SET('$ParamItemGroup',ParamItemGroup)>0 Order by ParamItemName ASC","","ParamItem_Id,ParamItemName","","");
        break;
	case 'SelectHelperParamItem':
				DSDebug(1,"DSParam_ListRender SelectHelperParamItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT ParamItem_Id,ParamItemName From Hparamitem Where ParamItemType='Helper' and FIND_IN_SET('$ParamItemGroup',ParamItemGroup)>0 Order by ParamItemName ASC","","ParamItem_Id,ParamItemName","","");
        break;
	case 'SelectFinishRule':
				DSDebug(1,"DSParam_ListRender SelectFinishRule *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT FinishRule_Id,FinishRuleName From Hfinishrule Order by FinishRuleName ASC","","FinishRule_Id,FinishRuleName","","");
        break;
	case 'SelectSMSProvider':
				DSDebug(1,"DSParam_ListRender SelectSMSProvider *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT SMSProvider_Id,SMSProviderName From Hsmsprovider Order by SMSProviderName ASC","","SMSProvider_Id,SMSProviderName","","");
        break;
	case 'SelectMikrotikRate':
				DSDebug(1,"DSParam_ListRender SelectMikrotikRate *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT MikrotikRate_Id,MikrotikRateName From Hmikrotikrate Order by MikrotikRateName ASC","","MikrotikRate_Id,MikrotikRateName","","");
        break;
	case 'SelectTimeRate':
				DSDebug(1,"DSParam_ListRender SelectTimeRate *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT TimeRate_Id,TimeRateName From Htimerate Order by TimeRateName ASC","","TimeRate_Id,TimeRateName","","");
        break;
	case 'SelectTrafficRate':
				DSDebug(1,"DSParam_ListRender SelectTrafficRate *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT TrafficRate_Id,TrafficRateName From Htrafficrate Order by TrafficRateName ASC","","TrafficRate_Id,TrafficRateName","","");
        break;
	case 'SelectIPPool':
				DSDebug(1,"DSParam_ListRender SelectIPPool *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT IPPool_Id,IPPoolName From Hippool Where IsFinishedIP='No' Order by IPPoolName ASC","","IPPool_Id,IPPoolName","","");
        break;
	case 'SelectLoginTime':
				DSDebug(1,"DSParam_ListRender SelectLoginTime *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT LoginTime_Id,LoginTimeName From Hlogintime Order by LoginTimeName ASC","","LoginTime_Id,LoginTimeName","","");
        break;
	case 'SelectActiveDirectory':
				DSDebug(1,"DSParam_ListRender SelectActiveDirectory *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT ActiveDirectory_Id,ActiveDirectoryName From Hactivedirectory Order by ActiveDirectoryName ASC","","ActiveDirectory_Id,ActiveDirectoryName","","");
        break;
	case 'SelectOffFormula':
				DSDebug(1,"> SelectOffFormula *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT OffFormula_Id,OffFormulaName From Hoffformula Order by OffFormulaName ASC","","OffFormula_Id,OffFormulaName","","");
        break;
	case 'SelectServiceInfo':
				DSDebug(1,"> SelectServiceInfo *****************");
							 
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT ServiceInfo_Id,ServiceInfoName From Hserviceinfo Order by ServiceInfoName ASC","","ServiceInfo_Id,ServiceInfoName","","");
        break;
	case 'SelectNotifyCreditFinish':
				DSDebug(1,"> SelectNotifyCreditFinish *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT Notify_Id as NotifyCreditFinish_Id,NotifyName From Hnotify  Where NotifyType='CreditFinishNotify' Order by NotifyName ASC","","NotifyCreditFinish_Id,NotifyName","","");
        break;
	case 'SelectNotifyServiceExpire':
				DSDebug(1,"> SelectNotifyServiceExpire *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT Notify_Id as NotifyServiceExpire_Id,NotifyName From Hnotify  Where NotifyType='ServiceExpireNotify' Order by NotifyName ASC","","NotifyServiceExpire_Id,NotifyName","","");
        break;
	case 'SelectNotifyUserDebit':
				DSDebug(1,"> SelectNotifyUserDebit *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT Notify_Id as NotifyUserDebit_Id,NotifyName From Hnotify  Where NotifyType='UserDebitNotify' Order by NotifyName ASC","","NotifyUserDebit_Id,NotifyName","","");
        break;
	case 'SelectWebAccess':
				DSDebug(1,"DSParam_ListRender SelectWebAccess *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT WebAccess_Id,WebAccessName From Hwebaccess Order by WebAccessName ASC","","WebAccess_Id,WebAccessName","","");
        break;
	case 'SelectDebitControl':
				DSDebug(1,"DSParam_ListRender SelectDebitControl *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT DebitControl_Id,DebitControlName From Hdebitcontrol Order by DebitControlName ASC","","DebitControl_Id,DebitControlName","","");
        break;
	case 'SelectCalledId':
				DSDebug(1,"DSParam_ListRender SelectCalledId *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT CalledId_Id,CalledIdName From Hcalledid Order by CalledIdName ASC","","CalledId_Id,CalledIdName","","");
        break;
	
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
