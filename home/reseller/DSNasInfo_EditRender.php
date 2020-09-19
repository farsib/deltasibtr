<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSNasInfoEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update",'SelectVisp','SelectCenter','SelectStatus','SelectSupporter','SelectServiceBase','SelectReseller'),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSNasInfoEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.NasInfo.View");
				$NASInfo_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$NasType=DBSelectAsString("Select NasType from Hnasinfo where NASInfo_Id=$NASInfo_Id");
				$sql="SELECT '' As Error,NASInfo_Id,NasInfoName,NasType,".
					"MaxInterimTime,MaxInterimTime as MaxInterimTime2,".
					"DeleteUserStaleMethod,StepOneWaitingTime,StepOneWaitingTime as StepOneWaitingTime2,".
					"StepTwoWaitingTime,StepTwoWaitingTime as StepTwoWaitingTime2,".
					"InterimRate,InterimRate as InterimRate2,".
					"DCMethod As {$NasType}DCMethod,DMAttribute As {$NasType}DMAttribute,DMPort As {$NasType}DMPort,PODAttribute As {$NasType}PODAttribute,PODPort As {$NasType}PODPort,".
					"PODCommunity As {$NasType}PODCommunity,DCTelnetUser As {$NasType}DCTelnetUser, ".
					"DCTelnetPass As {$NasType}DCTelnetPass,DCENPass As {$NasType}DCENPass,SSHPort,TelnetPort,BWManager As {$NasType}BWManager,BWSSHUser As {$NasType}BWSSHUser,".
					"BWSSHPass As {$NasType}BWSSHPass, ".
					"BWPriority As {$NasType}BWPriority, ".
					"CreateNewUser,DefVisp_Ids,DefReseller_Id,DefCenter_Id,DefSupporter_Id,DefStatus_Id,DefService_Id,DefAuthMethod,SetLocalPassMethod  ".
					"from Hnasinfo where NASInfo_Id='$NASInfo_Id'";
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
				DSDebug(1,"DSNasInfoEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.NasInfo.Add");
				$NewRowInfo=array();
				$NewRowInfo['DCMethod']='';
				$NewRowInfo['DMAttribute']='';
				$NewRowInfo['DMPort']='';
				$NewRowInfo['PODAttribute']='';
				$NewRowInfo['PODCommunity']='';
				$NewRowInfo['DCTelnetPass']='';
				$NewRowInfo['DCENPass']='';
				$NewRowInfo['SSHPort']='';
				$NewRowInfo['BWManager']='';
				$NewRowInfo['BWSSHUser']='';
				$NewRowInfo['BWSSHPass']='';
				$NewRowInfo['BWPriority']='';
				$NewRowInfo['DCENPass']='';
				$NewRowInfo['NasInfoName']=Get_Input('POST','DB','NasInfoName','STRENCHARNUMBER',1,32,0,0);				
				$NewRowInfo['TelnetPort']=Get_Input('POST','DB','TelnetPort','INT',0,65000,0,0);
				$NewRowInfo['SSHPort']=Get_Input('POST','DB','SSHPort','INT',0,65000,0,0);

				$NewRowInfo['DeleteUserStaleMethod']=Get_Input('POST','DB','DeleteUserStaleMethod','ARRAY',array("Never","OneStep",'TwoStep'),0,0,0);
				if($NewRowInfo['DeleteUserStaleMethod']=='OneStep'){
					$NewRowInfo['StepOneWaitingTime']=Get_Input('POST','DB','StepOneWaitingTime','INT',0,9999,0,0);
					$NewRowInfo['StepTwoWaitingTime']='0';
					$NewRowInfo['MaxInterimTime']=Get_Input('POST','DB','MaxInterimTime','INT',0,999999999,0,0);
					$NewRowInfo['InterimRate']=Get_Input('POST','DB','InterimRate','FLT',0,100,0,0);
				}	
				else if($NewRowInfo['DeleteUserStaleMethod']=='TwoStep'){
					$NewRowInfo['StepOneWaitingTime']=Get_Input('POST','DB','StepOneWaitingTime2','INT',0,9999,0,0);
					$NewRowInfo['StepTwoWaitingTime']=Get_Input('POST','DB','StepTwoWaitingTime2','INT',0,9999,0,0);
					$NewRowInfo['MaxInterimTime']=Get_Input('POST','DB','MaxInterimTime2','INT',0,999999999,0,0);
					$NewRowInfo['InterimRate']=Get_Input('POST','DB','InterimRate2','FLT',0,100,0,0);
				}	
				else{
					$NewRowInfo['StepOneWaitingTime']='60';
					$NewRowInfo['MaxInterimTime']='300';
					$NewRowInfo['InterimRate']='1';
					$NewRowInfo['StepTwoWaitingTime']='600';
				}
				
				$NewRowInfo['NasType']=Get_Input('POST','DB','NasType','STR',1,32,0,0);
				if($NewRowInfo['NasType']=='Mikrotik'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
					$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
					$NewRowInfo['BWManager']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWManager','ARRAY',array('Yes-SSH','Yes-COA','No'),0,0,0);
					$NewRowInfo['BWSSHUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWSSHUser','STR',0,32,0,0);
					$NewRowInfo['BWSSHPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWSSHPass','STR',0,32,0,0);
					$NewRowInfo['BWPriority']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWPriority','STR',0,32,0,0);
				}	
				else if($NewRowInfo['NasType']=='Cisco'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['PODAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODAttribute','STR',1,64,0,0);
					$NewRowInfo['PODCommunity']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODCommunity','STRENCHARNUMBER',1,64,0,0);
					$NewRowInfo['PODPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODPort','INT',0,65000,0,0);
				}
				else if($NewRowInfo['NasType']=='HuaweiBRas'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
					$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
				}
				else if($NewRowInfo['NasType']=='TC1000'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DCTelnetUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetUser','STR',0,32,0,0);
					$NewRowInfo['DCTelnetPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetPass','STR',0,32,0,0);
				}
				else if($NewRowInfo['NasType']=='AS5200'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DCTelnetUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetUser','STR',0,32,0,0);
					$NewRowInfo['DCTelnetPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetPass','STR',0,32,0,0);
					$NewRowInfo['DCENPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCENPass','STR',0,32,0,0);
				}
				else if($NewRowInfo['NasType']=='ASA5525'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DCTelnetUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetUser','STR',0,32,0,0);
					$NewRowInfo['DCTelnetPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetPass','STR',0,32,0,0);
					$NewRowInfo['DCENPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCENPass','STR',0,32,0,0);
				}
				else if($NewRowInfo['NasType']=='ZTE'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
					$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
				}
				else if($NewRowInfo['NasType']=='Custom'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					if($NewRowInfo['DCMethod']=='DM'){
						$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
						$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
					}
					if($NewRowInfo['DCMethod']=='POD'){
						$NewRowInfo['PODAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODAttribute','STR',1,64,0,0);
						$NewRowInfo['PODCommunity']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODCommunity','STR',1,64,0,0);
						$NewRowInfo['PODPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODPort','INT',0,65000,0,0);
					}
				}
				$NewRowInfo['CreateNewUser']=Get_Input('POST','DB','CreateNewUser','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CreateNewUser']=='Yes'){
					$NewRowInfo['DefReseller_Id']=Get_Input('POST','DB','DefReseller_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select ResellerName from Hreseller where Reseller_Id='".$NewRowInfo['DefReseller_Id']."'")=="")
						ExitError("نماینده فروش نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefVisp_Ids']=Get_Input('POST','DB','DefVisp_Ids','STR',1,255,0,0);
					$tmp=explode(",",$NewRowInfo['DefVisp_Ids']);
					foreach($tmp as $key=>$value)
						if(DBSelectAsString("select VispName from Hvisp where Visp_Id='$value'")=="")
							ExitError("ارائه دهنده مجازی نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefCenter_Id']=Get_Input('POST','DB','DefCenter_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select CenterName from Hcenter where Center_Id='".$NewRowInfo['DefCenter_Id']."'")=="")
						ExitError("مرکز نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefSupporter_Id']=Get_Input('POST','DB','DefSupporter_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select SupporterName from Hsupporter where Supporter_Id='".$NewRowInfo['DefSupporter_Id']."'")=="")
						ExitError("پشتیبان نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefStatus_Id']=Get_Input('POST','DB','DefStatus_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select StatusName from Hstatus where Status_Id='".$NewRowInfo['DefStatus_Id']."'")=="")
						ExitError("وضعیت نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefService_Id']=Get_Input('POST','DB','DefService_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select ServiceName from Hservice where Service_Id='".$NewRowInfo['DefService_Id']."'")=="")
						ExitError("سرویس نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefAuthMethod']=Get_Input('POST','DB','DefAuthMethod','STR',1,3,0,0);
					$NewRowInfo['SetLocalPassMethod']=Get_Input('POST','DB','SetLocalPassMethod','STR',1,20,0,0);
				}
				else{
					$NewRowInfo['DefReseller_Id']=0;
					$NewRowInfo['DefVisp_Ids']=0;
					$NewRowInfo['DefCenter_Id']=0;
					$NewRowInfo['DefSupporter_Id']=0;
					$NewRowInfo['DefStatus_Id']=0;
					$NewRowInfo['DefService_Id']=0;
					$NewRowInfo['DefAuthMethod']='';
					$NewRowInfo['SetLocalPassMethod']='';
				}
																			 
				//----------------------
				$sql= "insert Hnasinfo set ";
				$sql.="NasInfoName='".$NewRowInfo['NasInfoName']."',";
				$sql.="NasType='".$NewRowInfo['NasType']."',";
				$sql.="DCMethod='".$NewRowInfo['DCMethod']."',";
				$sql.="DMAttribute='".$NewRowInfo['DMAttribute']."',";
				$sql.="DMPort='".$NewRowInfo['DMPort']."',";
				$sql.="PODPort='".$NewRowInfo['PODPort']."',";
				$sql.="PODAttribute='".$NewRowInfo['PODAttribute']."',";
				$sql.="PODCommunity='".$NewRowInfo['PODCommunity']."',";
				$sql.="DCTelnetUser='".$NewRowInfo['DCTelnetUser']."',";
				$sql.="DCTelnetPass='".$NewRowInfo['DCTelnetPass']."',";
				$sql.="DCENPass='".$NewRowInfo['DCENPass']."',";
				$sql.="SSHPort='".$NewRowInfo['SSHPort']."',";
				$sql.="TelnetPort='".$NewRowInfo['TelnetPort']."',";
				$sql.="BWManager='".$NewRowInfo['BWManager']."',";
				$sql.="BWSSHUser='".$NewRowInfo['BWSSHUser']."',";
				$sql.="BWSSHPass='".$NewRowInfo['BWSSHPass']."',";
				$sql.="BWPriority='".$NewRowInfo['BWPriority']."',";
				$sql.="DeleteUserStaleMethod='".$NewRowInfo['DeleteUserStaleMethod']."',";
				$sql.="StepOneWaitingTime='".$NewRowInfo['StepOneWaitingTime']."',";
				$sql.="StepTwoWaitingTime='".$NewRowInfo['StepTwoWaitingTime']."',";
				$sql.="MaxInterimTime='".$NewRowInfo['MaxInterimTime']."',";
				$sql.="InterimRate='".$NewRowInfo['InterimRate']."',";
				$sql.="CreateNewUser='".$NewRowInfo['CreateNewUser']."',";
				$sql.="DefReseller_Id='".$NewRowInfo['DefReseller_Id']."',";
				$sql.="DefVisp_Ids='".$NewRowInfo['DefVisp_Ids']."',";
				$sql.="DefCenter_Id='".$NewRowInfo['DefCenter_Id']."',";
				$sql.="DefSupporter_Id='".$NewRowInfo['DefSupporter_Id']."',";
				$sql.="DefStatus_Id='".$NewRowInfo['DefStatus_Id']."',";
				$sql.="DefService_Id='".$NewRowInfo['DefService_Id']."',";
				$sql.="DefAuthMethod='".$NewRowInfo['DefAuthMethod']."',";
				$sql.="SetLocalPassMethod='".$NewRowInfo['SetLocalPassMethod']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['NASInfo_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','NasInfo',$RowId,'NasInfo');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSNasInfoEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.NasInfo.Edit");
				$NewRowInfo=array();
				$NewRowInfo['NASInfo_Id']=Get_Input('POST','DB','NASInfo_Id','INT',1,4294967295,0,0);
				$NewRowInfo['DCMethod']='';
				$NewRowInfo['DMAttribute']='';
				$NewRowInfo['DMPort']='';
				$NewRowInfo['PODAttribute']='';
				$NewRowInfo['PODCommunity']='';
				$NewRowInfo['DCTelnetPass']='';
				$NewRowInfo['DCENPass']='';
				$NewRowInfo['SSHPort']='';
				$NewRowInfo['BWManager']='';
				$NewRowInfo['BWSSHUser']='';
				$NewRowInfo['BWSSHPass']='';
				$NewRowInfo['DCENPass']='';
				$NewRowInfo['NasInfoName']=Get_Input('POST','DB','NasInfoName','STRENCHARNUMBER',1,32,0,0);				
				$NewRowInfo['TelnetPort']=Get_Input('POST','DB','TelnetPort','INT',0,65000,0,0);
				$NewRowInfo['SSHPort']=Get_Input('POST','DB','SSHPort','INT',0,65000,0,0);
				$NewRowInfo['DeleteUserStaleMethod']=Get_Input('POST','DB','DeleteUserStaleMethod','ARRAY',array("Never","OneStep",'TwoStep'),0,0,0);
				if($NewRowInfo['DeleteUserStaleMethod']=='OneStep'){
					$NewRowInfo['StepOneWaitingTime']=Get_Input('POST','DB','StepOneWaitingTime','INT',0,9999,0,0);
					$NewRowInfo['StepTwoWaitingTime']='0';
					$NewRowInfo['MaxInterimTime']=Get_Input('POST','DB','MaxInterimTime','INT',0,999999999,0,0);
					$NewRowInfo['InterimRate']=Get_Input('POST','DB','InterimRate','FLT',0,100,0,0);
				}	
				else if($NewRowInfo['DeleteUserStaleMethod']=='TwoStep'){
					$NewRowInfo['StepOneWaitingTime']=Get_Input('POST','DB','StepOneWaitingTime2','INT',0,9999,0,0);
					$NewRowInfo['StepTwoWaitingTime']=Get_Input('POST','DB','StepTwoWaitingTime2','INT',0,9999,0,0);
					$NewRowInfo['MaxInterimTime']=Get_Input('POST','DB','MaxInterimTime2','INT',0,999999999,0,0);
					$NewRowInfo['InterimRate']=Get_Input('POST','DB','InterimRate2','FLT',0,100,0,0);
				}	
				else{
					$NewRowInfo['StepOneWaitingTime']='60';
					$NewRowInfo['MaxInterimTime']='300';
					$NewRowInfo['InterimRate']='1';
					$NewRowInfo['StepTwoWaitingTime']='600';
				}				
				$NewRowInfo['NasType']=Get_Input('POST','DB','NasType','STR',1,32,0,0);
				if($NewRowInfo['NasType']=='Mikrotik'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
					$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
					$NewRowInfo['BWManager']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWManager','ARRAY',array('Yes-SSH','Yes-COA','No'),0,0,0);
					$NewRowInfo['BWSSHUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWSSHUser','STR',0,32,0,0);
					$NewRowInfo['BWSSHPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWSSHPass','STR',0,32,0,0);
					$NewRowInfo['BWPriority']=Get_Input('POST','DB',$NewRowInfo['NasType'].'BWPriority','STR',0,32,0,0);
				}	
				else if($NewRowInfo['NasType']=='Cisco'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['PODAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODAttribute','STR',1,64,0,0);
					$NewRowInfo['PODCommunity']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODCommunity','STRENCHARNUMBER',1,64,0,0);
					$NewRowInfo['PODPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODPort','INT',0,65000,0,0);
				}
				else if($NewRowInfo['NasType']=='HuaweiBRas'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
					$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
				}
				else if($NewRowInfo['NasType']=='TC1000'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DCTelnetUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetUser','STR',0,32,0,0);
					$NewRowInfo['DCTelnetPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetPass','STR',0,32,0,0);
				}
				else if($NewRowInfo['NasType']=='AS5200'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DCTelnetUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetUser','STR',0,32,0,0);
					$NewRowInfo['DCTelnetPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetPass','STR',0,32,0,0);
					$NewRowInfo['DCENPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCENPass','STR',0,32,0,0);
				}
				else if($NewRowInfo['NasType']=='ASA5525'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DCTelnetUser']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetUser','STR',0,32,0,0);
					$NewRowInfo['DCTelnetPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCTelnetPass','STR',0,32,0,0);
					$NewRowInfo['DCENPass']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCENPass','STR',0,32,0,0);
				}
				else if($NewRowInfo['NasType']=='ZTE'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
					$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
				}
				else if($NewRowInfo['NasType']=='Custom'){
					$NewRowInfo['DCMethod']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DCMethod','STRENCHARNUMBER',1,32,0,0);
					if($NewRowInfo['DCMethod']=='DM'){
						$NewRowInfo['DMAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMAttribute','STR',1,64,0,0);
						$NewRowInfo['DMPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'DMPort','INT',0,65000,0,0);
					}
					if($NewRowInfo['DCMethod']=='POD'){
						$NewRowInfo['PODAttribute']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODAttribute','STR',1,64,0,0);
						$NewRowInfo['PODCommunity']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODCommunity','STR',1,64,0,0);
						$NewRowInfo['PODPort']=Get_Input('POST','DB',$NewRowInfo['NasType'].'PODPort','INT',0,65000,0,0);
					}
				}
				
				$NewRowInfo['CreateNewUser']=Get_Input('POST','DB','CreateNewUser','ARRAY',array("Yes","No"),0,0,0);
				if($NewRowInfo['CreateNewUser']=='Yes'){
					$NewRowInfo['DefReseller_Id']=Get_Input('POST','DB','DefReseller_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select ResellerName from Hreseller where Reseller_Id='".$NewRowInfo['DefReseller_Id']."'")=="")
						ExitError("نماینده فروش نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefVisp_Ids']=Get_Input('POST','DB','DefVisp_Ids','STR',1,255,0,0);
					$tmp=explode(",",$NewRowInfo['DefVisp_Ids']);
					foreach($tmp as $key=>$value)
						if(DBSelectAsString("select VispName from Hvisp where Visp_Id='$value'")=="")
							ExitError("ارائه دهنده مجازی نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefCenter_Id']=Get_Input('POST','DB','DefCenter_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select CenterName from Hcenter where Center_Id='".$NewRowInfo['DefCenter_Id']."'")=="")
						ExitError("مرکز نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefSupporter_Id']=Get_Input('POST','DB','DefSupporter_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select SupporterName from Hsupporter where Supporter_Id='".$NewRowInfo['DefSupporter_Id']."'")=="")
						ExitError("پشتیبان نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefStatus_Id']=Get_Input('POST','DB','DefStatus_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select StatusName from Hstatus where Status_Id='".$NewRowInfo['DefStatus_Id']."'")=="")
						ExitError("وضعیت نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefService_Id']=Get_Input('POST','DB','DefService_Id','INT',1,4294967295,0,0);
					if(DBSelectAsString("select ServiceName from Hservice where Service_Id='".$NewRowInfo['DefService_Id']."'")=="")
						ExitError("سرویس نامعتبر انتخاب شده است");
					
					$NewRowInfo['DefAuthMethod']=Get_Input('POST','DB','DefAuthMethod','STR',1,3,0,0);
					$NewRowInfo['SetLocalPassMethod']=Get_Input('POST','DB','SetLocalPassMethod','STR',1,20,0,0);
				}
				else{
					$NewRowInfo['DefReseller_Id']=0;
					$NewRowInfo['DefVisp_Ids']=0;
					$NewRowInfo['DefCenter_Id']=0;
					$NewRowInfo['DefSupporter_Id']=0;
					$NewRowInfo['DefStatus_Id']=0;
					$NewRowInfo['DefService_Id']=0;
					$NewRowInfo['DefAuthMethod']='';
					$NewRowInfo['SetLocalPassMethod']='';
				}
				 	 	 	 	 	 	 	 	 	 	 	 	 	 	 
				//----------------------
				$sql= "Update Hnasinfo set ";
				$sql.="NasInfoName='".$NewRowInfo['NasInfoName']."',";
				$sql.="NasType='".$NewRowInfo['NasType']."',";
				$sql.="DCMethod='".$NewRowInfo['DCMethod']."',";
				$sql.="DMAttribute='".$NewRowInfo['DMAttribute']."',";
				$sql.="DMPort='".$NewRowInfo['DMPort']."',";
				$sql.="PODPort='".$NewRowInfo['PODPort']."',";
				$sql.="PODAttribute='".$NewRowInfo['PODAttribute']."',";
				$sql.="PODCommunity='".$NewRowInfo['PODCommunity']."',";
				$sql.="DCTelnetUser='".$NewRowInfo['DCTelnetUser']."',";
				$sql.="DCTelnetPass='".$NewRowInfo['DCTelnetPass']."',";
				$sql.="DCENPass='".$NewRowInfo['DCENPass']."',";
				$sql.="SSHPort='".$NewRowInfo['SSHPort']."',";
				$sql.="TelnetPort='".$NewRowInfo['TelnetPort']."',";
				$sql.="BWManager='".$NewRowInfo['BWManager']."',";
				$sql.="BWSSHUser='".$NewRowInfo['BWSSHUser']."',";
				$sql.="BWSSHPass='".$NewRowInfo['BWSSHPass']."',";
				$sql.="BWPriority='".$NewRowInfo['BWPriority']."',";
				$sql.="DeleteUserStaleMethod='".$NewRowInfo['DeleteUserStaleMethod']."',";
				$sql.="StepOneWaitingTime='".$NewRowInfo['StepOneWaitingTime']."',";
				$sql.="StepTwoWaitingTime='".$NewRowInfo['StepTwoWaitingTime']."',";
				$sql.="MaxInterimTime='".$NewRowInfo['MaxInterimTime']."',";
				$sql.="InterimRate='".$NewRowInfo['InterimRate']."',";
				$sql.="CreateNewUser='".$NewRowInfo['CreateNewUser']."',";
				$sql.="DefReseller_Id='".$NewRowInfo['DefReseller_Id']."',";
				$sql.="DefVisp_Ids='".$NewRowInfo['DefVisp_Ids']."',";
				$sql.="DefCenter_Id='".$NewRowInfo['DefCenter_Id']."',";
				$sql.="DefSupporter_Id='".$NewRowInfo['DefSupporter_Id']."',";
				$sql.="DefStatus_Id='".$NewRowInfo['DefStatus_Id']."',";
				$sql.="DefService_Id='".$NewRowInfo['DefService_Id']."',";
				$sql.="DefAuthMethod='".$NewRowInfo['DefAuthMethod']."',";
				$sql.="SetLocalPassMethod='".$NewRowInfo['SetLocalPassMethod']."'";
				$sql.=" Where ";
				$sql.="(NASInfo_Id='".$NewRowInfo['NASInfo_Id']."')";
				$OldRowInfo= LoadRowInfo("Hnasinfo","NASInfo_Id='".$NewRowInfo['NASInfo_Id']."'");
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','NasInfo',$NewRowInfo['NASInfo_Id'],'NasInfo',"Update Fail,Table=NasInfo affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=NasInfo affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'NasInfo',$NewRowInfo['NASInfo_Id'],'NasInfo')){
					logunfair("UnFair",'NasInfo',$NewRowInfo['NASInfo_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
    case "SelectVisp":
				DSDebug(1,"DSActiveDirectoryEditRender SelectVisp *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Visp_Id,VispName FROM Hvisp ".
					" order by VispName ASC";
				$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectCenter":
				DSDebug(1,"DSActiveDirectoryEditRender SelectCenterByUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Center_Id,CenterName ".
						"from Hcenter ".
						"order by CenterName asc";
				$options->render_sql($sql,"","Center_Id,CenterName","","");
        break;
    case "SelectStatus":
				DSDebug(1,"DSActiveDirectoryEditRender SelectStatus *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Status_Id,StatusName from Hstatus";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
        break;
    case "SelectSupporter":
				DSDebug(1,"DSActiveDirectoryEditRender SelectSupporterByUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Supporter_Id,SupporterName FROM Hsupporter order by SupporterName asc";
				$options->render_sql($sql,"","Supporter_Id,SupporterName","","");
        break;
    case "SelectReseller":
				DSDebug(1,"DSActiveDirectoryEditRender SelectSupporterByUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="SELECT Reseller_Id,ResellerName ".
					"From Hreseller r Where (ISOperator='No') order by ResellerName Asc";
				$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
            break;
    case "SelectServiceBase":
				DSDebug(1,"DSActiveDirectoryEditRender-> SelectServiceBase *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$sql="Select Service_Id,ServiceName From Hservice ".
					"Where (IsDel='No')And(ServiceType='Base') order by ServiceName";
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
