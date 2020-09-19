<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSBriefUser_EditRender ..................................................................................");

// sleep(4);//test
$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update","change","GetPass","SelectVisp_Id","SelectCenter_Id","SelectSupporter_Id","SelectReseller_Id","SelectStatus_Id","SelectStatus","SelectVispByUsername","SelectCenterByUsername","SelectSupporterByUsername","checkusername","SelectUsername","SelectServiceBase","SelectServiceExtraCredit","SelectServiceIP","SelectServiceOther","GetServicePrice","LoadIPRequest","CheckIPRequest","AddService","GetUserPayBalance","AddPayment","GetCreditInfo","GetUserLog","WebUnblock","RadiusUnblock"),0,0,0);

if($LResellerName=='')
	if($act=='SelectUsername'){
		header ("Content-Type:text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<data>';
		echo '<item value="Session Expire" label="نشست منقضی شده" />';
		echo '</data>';
		exit();
	}
	else
		ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();
try {
switch ($act) {
    case "load":
				DSDebug(1,"DSBriefUser_EditRender Load ********************************************");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Tuser_authhelper where Username='$Username'");
				if($User_Id<="")
					ExitError("NotFound");
				// ExitIfNotPermitRowAccess('user',$User_Id);
				
				
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser Where User_id='$User_Id'");
				if(!(ISPermit($Visp_Id,"Visp.User.View")))
					ExitError("کاربر وجود دارد ولی شما مجوز دیدن آن را ندارید");
				
				$Session=DBSelectAsString("Select Count(1) from Tonline_radiususer Where User_Id='$User_Id' And ServiceInfo_Id=1 And AcctUniqueId<>'' and TerminateCause=''");
				$IsFinish=DBSelectAsString("Select Count(1) from Tonline_radiususer Where User_Id='$User_Id' And ServiceInfo_Id=1 And AcctUniqueId<>'' and TerminateCause='' and ISFinishUser='Yes'");
				if($IsFinish>0)
					$Session=-$Session;
				$StaleSession=DBSelectAsString("Select Count(1) from Tonline_radiususer Where User_Id='$User_Id' And ServiceInfo_Id=1 And AcctUniqueId<>'' and TerminateCause<>''");
				DSDebug(0,"Session=$Session	StaleSession=$StaleSession");	
				
				$sql="Select '' As Error,User_Id,{$DT}datetimestr(UserCDT) as UserCDT,ResellerName,us.UserStatus As UserStatus,StatusName,".
					"'$Session' as Session,'$StaleSession' as StaleSession,".
					"InitialMonthOff,Format(MaxPrepaidDebit,$PriceFloatDigit) As MaxPrepaidDebit,Username,".
					"u.Visp_Id,VispName,CenterName,SupporterName,AdslPhone,u.NOE,IdentInfo,IPRouteLog,Email,u.Comment,Organization,".
					"CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,u.Name,u.Family,FatherName,".
					"Nationality,u.Mobile,NationalCode,{$DT}datestr(BirthDate) as BirthDate,u.Phone,".
					"u.Address,{$DT}datestr(ExpirationDate) as ExpirationDate From Huser u ".
					"Left join Hreseller r on (u.Reseller_Id=r.Reseller_Id) ".
					"Left join Hvisp v on (u.Visp_Id=v.Visp_Id) ".
					"Left join Hcenter c on (u.Center_Id=c.Center_Id) ".
					"Left join Hsupporter s on (u.Supporter_Id=s.Supporter_Id) ".
					"Left Join Hstatus us on (u.Status_Id=us.Status_Id) ".
					"where User_Id='$User_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				if($data)
					exitifnotpermit($data["Visp_Id"],"Visp.User.View");
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data){
					foreach ($data as $Field=>$Value){
						switch ($Field) {
							case "VispName":
								if(ISPermit($data["Visp_Id"],"Visp.User.Info.ViewField.Visp_Id"))
									GenerateLoadField("Visp_Id",$Value);
								break;
							case "CenterName":
								if(ISPermit($data["Visp_Id"],"Visp.User.Info.ViewField.Center_Id"))
									GenerateLoadField("Center_Id",$Value);
								break;
							case "SupporterName":
								if(ISPermit($data["Visp_Id"],"Visp.User.Info.ViewField.Supporter_Id"))
									GenerateLoadField("Supporter_Id",$Value);
								break;
							case "ResellerName":
								GenerateLoadField("Reseller_Id",$Value);
								break;
							case "StatusName":
								GenerateLoadField("Status_Id",$Value);
								break;
							case "Visp_Id":
								break;
							case "Error":
							case "User_Id":
							case "UserCDT":
							case "UserStatus":
							case "Session":
							case "StaleSession":
							case "Username":
							case "MaxPrepaidDebit":
								GenerateLoadField($Field,$Value);
								break;
							default:
								if(ISPermit($data["Visp_Id"],"Visp.User.Info.ViewField.".$Field))
									GenerateLoadField($Field,$Value);
						}
					}
				}
				echo '</data>';			
       break;
    case "insert":
				DSDebug(1,"DSBriefUser_EditRender Insert ******************************************");
				$NewRowInfo=array();
				
				$NewRowInfo['Username']=Get_Input('POST','DB','Username','STR',1,32,0,0);
				$Username=$NewRowInfo['Username'];
				$NewRowInfo['User_Id']=0;
				
				$NewRowInfo['Visp_Id']=Get_Input('POST','DB','Visp_Id','INT',1,4294967295,0,0);
				exitifnotpermit($NewRowInfo['Visp_Id'],"Visp.User.Add");
				//-------------------------------------------------------------------------
				$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hvisp where Visp_Id='".$NewRowInfo['Visp_Id']."'");
				if($ISUsernameOk!=1){
					$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hvisp where Visp_Id='".$NewRowInfo['Visp_Id']."'");
					ExitError("Visp Pattern($UsernamePattern) not matched!");
				}
				
				$NewRowInfo['Center_Id']=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
				//-------------------------------------------------------------------------
				$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
				if($ISUsernameOk!=1){
					$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
					ExitError("Center Pattern($UsernamePattern) not matched!");
				}
				
				$VispAccess=DBSelectAsString("select VispAccess from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
				if($VispAccess!='All'){
					$VispAccess=DBSelectAsString("select Center_Id from Hcenter_vispaccess where Center_Id='".$NewRowInfo['Center_Id']."' and Visp_Id='".$NewRowInfo['Visp_Id']."' and Checked='Yes'");
					if($VispAccess<=0){
						$CenterName=DBSelectAsString("Select CenterName from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
						$VispName=DBSelectAsString("Select VispName from Hvisp where Visp_Id='".$NewRowInfo['Visp_Id']."'");
						ExitError("را ندارد ($VispName) دسترسی به این ارائه دهنده مجازی اینترنت ($CenterName) این مرکز");
					}
				}
				
				$NewRowInfo['Supporter_Id']=Get_Input('POST','DB','Supporter_Id','INT',1,4294967295,0,0);
				//-------------------------------------------------------------------------
				$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hsupporter where Supporter_Id='".$NewRowInfo['Supporter_Id']."'");
				if($ISUsernameOk!=1){
					$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hsupporter where Supporter_Id='".$NewRowInfo['Supporter_Id']."'");
					ExitError("Supporter Pattern($UsernamePattern) not matched!");
				}	
				
				$NewRowInfo['Status_Id']=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);
				
				
				$ISStatusOK=DBSelectAsString("SELECT count(1) from Hstatus s Where Status_Id='".$NewRowInfo['Status_Id']."' and InitialStatus='Yes' and (ResellerAccess='All' or ($LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Status_Id='".$NewRowInfo['Status_Id']."' and Checked='Yes'))) and (VispAccess='All' or ('".$NewRowInfo['Visp_Id']."' in (select Visp_Id from Hstatus_vispaccess where Status_Id='".$NewRowInfo['Status_Id']."' and Checked='Yes')))");
				if($ISStatusOK<=0)
					ExitError("وضعیت انتخاب شده جز وضعیت های اولیه مجاز نیست");
				//check count of center
				$IsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id='".$NewRowInfo['Status_Id']."'");
				if($IsBusyPort=='Yes') {
					$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id='".$NewRowInfo['Center_Id']."' and IsBusyPort='Yes'");
					$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id='".$NewRowInfo['Center_Id']."'");
					if($n>=$max)
							ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده");
				}

				$Reseller_Id=$LReseller_Id;
				$ISOperator=DBSelectAsString("SELECT ISOperator from Hreseller Where Reseller_Id=$Reseller_Id");
				DSDebug(1,"Reseller_Id=$Reseller_Id   ISOperator=$ISOperator");
				
				While(($ISOperator=='Yes') && ($Reseller_Id!=1)){
					$Reseller_Id=DBSelectAsString("SELECT ParentReseller_Id from Hreseller Where Reseller_Id=$Reseller_Id");
					$ISOperator=DBSelectAsString("SELECT ISOperator from Hreseller Where Reseller_Id=$Reseller_Id");
					DSDebug(1,"Reseller_Id=$Reseller_Id   ISOperator=$ISOperator");
				}		
				$NewRowInfo['Reseller_Id']=$Reseller_Id;
				
				//----------------------
				$sql= "insert Huser set UserCDT=Now()";
				$sql.=",Username='".$NewRowInfo['Username']."'";
				$sql.=",Visp_Id='".$NewRowInfo['Visp_Id']."'";
				$sql.=",Center_Id='".$NewRowInfo['Center_Id']."'";
				$sql.=",Supporter_Id='".$NewRowInfo['Supporter_Id']."'";
				$sql.=",Reseller_Id='".$NewRowInfo['Reseller_Id']."'";
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Pass')){
					$Pass=Get_Input('POST','DB','Pass','STR',0,32,0,0);
					$sql.=",Pass='".$Pass."'";//Use direct variable to avoid setting password in changelog
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.AdslPhone')){
					$NewRowInfo['AdslPhone']=Get_Input('POST','DB','AdslPhone','STR',0,10,0,0);
					if(($NewRowInfo['AdslPhone']>0)&&(strlen(utf8_decode($NewRowInfo['AdslPhone']))!=10))
						ExitError("تلفن ADSL باید ۱۰ رقم باشد");
					$sql.=",AdslPhone='".$NewRowInfo['AdslPhone']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.NOE')){
					$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);
					$sql.=",NOE='".$NewRowInfo['NOE']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.IdentInfo')){
					$NewRowInfo['IdentInfo']=Get_Input('POST','DB','IdentInfo','STR',0,32,0,0);
					$sql.=",IdentInfo='".$NewRowInfo['IdentInfo']."'";
				}
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.IPRouteLog')){
					$NewRowInfo['IPRouteLog']=Get_Input('POST','DB','IPRouteLog','STR',0,100,0,0);
					$sql.=",IPRouteLog='".$NewRowInfo['IPRouteLog']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Email')){
					$NewRowInfo['Email']=Get_Input('POST','DB','Email','STR',0,255,0,0);
					$sql.=",Email='".$NewRowInfo['Email']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Comment')){
					$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,255,0,0);
					$sql.=",Comment='".$NewRowInfo['Comment']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Organization')){
					$NewRowInfo['Organization']=Get_Input('POST','DB','Organization','STR',0,64,0,0);
					$sql.=",Organization='".$NewRowInfo['Organization']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.CompanyRegistryCode')){
					$NewRowInfo['CompanyRegistryCode']=Get_Input('POST','DB','CompanyRegistryCode','STR',0,12,0,0);
					$sql.=",CompanyRegistryCode='".$NewRowInfo['CompanyRegistryCode']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.CompanyEconomyCode')){
					$NewRowInfo['CompanyEconomyCode']=Get_Input('POST','DB','CompanyEconomyCode','STR',0,12,0,0);
					$sql.=",CompanyEconomyCode='".$NewRowInfo['CompanyEconomyCode']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.CompanyNationalCode')){
					$NewRowInfo['CompanyNationalCode']=Get_Input('POST','DB','CompanyNationalCode','STR',0,12,0,0);
					$sql.=",CompanyNationalCode='".$NewRowInfo['CompanyNationalCode']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Name')){
					$NewRowInfo['Name']=Get_Input('POST','DB','Name','STR',0,32,0,0);
					$sql.=",Name='".$NewRowInfo['Name']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Family')){
					$NewRowInfo['Family']=Get_Input('POST','DB','Family','STR',0,32,0,0);
					$sql.=",Family='".$NewRowInfo['Family']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.FatherName')){
					$NewRowInfo['FatherName']=Get_Input('POST','DB','FatherName','STR',0,32,0,0);
					$sql.=",FatherName='".$NewRowInfo['FatherName']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Nationality')){
					$NewRowInfo['Nationality']=Get_Input('POST','DB','Nationality','STR',0,64,0,0);
					$sql.=",Nationality='".$NewRowInfo['Nationality']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Mobile')){
					$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,11,0,0);
					$sql.=",Mobile='".$NewRowInfo['Mobile']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.NationalCode')){
					$NewRowInfo['NationalCode']=Get_Input('POST','DB','NationalCode','STR',0,10,0,0);
					$sql.=",NationalCode='".$NewRowInfo['NationalCode']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.BirthDate')){
					$NewRowInfo['BirthDate']=Get_Input('POST','DB','BirthDate','DateOrBlank',0,0,0,0);
					$sql.=",BirthDate='".$NewRowInfo['BirthDate']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Phone')){
					$NewRowInfo['Phone']=Get_Input('POST','DB','Phone','STR',0,32,0,0);
					$sql.=",Phone='".$NewRowInfo['Phone']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Address')){
					$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,255,0,0);
					$sql.=",Address='".$NewRowInfo['Address']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.ExpirationDate')){
					$NewRowInfo['ExpirationDate']=Get_Input('POST','DB','ExpirationDate','DateOrBlank',0,0,0,0);
					$sql.=",ExpirationDate='".$NewRowInfo['ExpirationDate']."'";
				}
				
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_Id']=$RowId;
				DBInsert("Insert into Huser_status Set Reseller_Id=$LReseller_Id,StatusCDT=Now(),User_Id=$RowId,Status_Id='".$NewRowInfo['Status_Id']."'");
								
				logdbinsert($NewRowInfo,'Add','User',$RowId,'User');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSBriefUser_EditRender.php  $act********************************************");
				$NewRowInfo=array();
				$OldRowInfo= Array();
				$NewRowInfo['User_Id']=Get_Input('POST','DB','User_Id','INT',1,4294967295,0,0);
				
				$Username=DBSelectAsString("select Username from Huser where User_Id='".$NewRowInfo['User_Id']."'");
				$Visp_Id=DBSelectAsString("select Visp_Id from Huser where User_Id='".$NewRowInfo['User_Id']."'");
				exitifnotpermit($Visp_Id,"Visp.User.Edit");
				

				$sql= "update Huser set ".
					"Username='$Username'";
				$OldRow_sql="select User_Id,Username";
				
				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.AdslPhone')){
					$NewRowInfo['AdslPhone']=Get_Input('POST','DB','AdslPhone','STR',0,10,0,0);
					if(($NewRowInfo['AdslPhone']>0)&&(strlen(utf8_decode($NewRowInfo['AdslPhone']))!=10))
						ExitError("تلفن ADSL باید ۱۰ رقم باشد");
					$sql.=",AdslPhone='".$NewRowInfo['AdslPhone']."'";
					$OldRow_sql.=",AdslPhone";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.NOE')){
					$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);
					$sql.=",NOE='".$NewRowInfo['NOE']."'";
					$OldRow_sql.=",NOE";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.IdentInfo')){
					$NewRowInfo['IdentInfo']=Get_Input('POST','DB','IdentInfo','STR',0,100,0,0);
					$sql.=",IdentInfo='".$NewRowInfo['IdentInfo']."'";
					$OldRow_sql.=",IdentInfo";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.IPRouteLog')){
					$NewRowInfo['IPRouteLog']=Get_Input('POST','DB','IPRouteLog','STR',0,100,0,0);
					$sql.=",IPRouteLog='".$NewRowInfo['IPRouteLog']."'";
					$OldRow_sql.=",IPRouteLog";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Email')){
					$NewRowInfo['Email']=Get_Input('POST','DB','Email','STR',0,255,0,0);
					$sql.=",Email='".$NewRowInfo['Email']."'";
					$OldRow_sql.=",Email";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Comment')){
					$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,255,0,0);
					$sql.=",Comment='".$NewRowInfo['Comment']."'";
					$OldRow_sql.=",Comment";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.ExpirationDate')){
					$NewRowInfo['ExpirationDate']=Get_Input('POST','DB','ExpirationDate','DateOrBlank',0,0,0,0);
					$sql.=",ExpirationDate='".$NewRowInfo['ExpirationDate']."'";
					$OldRow_sql.=",ExpirationDate";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Organization')){
					$NewRowInfo['Organization']=Get_Input('POST','DB','Organization','STR',0,64,0,0);
					$sql.=",Organization='".$NewRowInfo['Organization']."'";
					$OldRow_sql.=",Organization";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CompanyRegistryCode')){
					$NewRowInfo['CompanyRegistryCode']=Get_Input('POST','DB','CompanyRegistryCode','STR',0,12,0,0);
					$sql.=",CompanyRegistryCode='".$NewRowInfo['CompanyRegistryCode']."'";
					$OldRow_sql.=",CompanyRegistryCode";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CompanyEconomyCode')){
					$NewRowInfo['CompanyEconomyCode']=Get_Input('POST','DB','CompanyEconomyCode','STR',0,12,0,0);
					$sql.=",CompanyEconomyCode='".$NewRowInfo['CompanyEconomyCode']."'";
					$OldRow_sql.=",CompanyEconomyCode";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CompanyNationalCode')){
					$NewRowInfo['CompanyNationalCode']=Get_Input('POST','DB','CompanyNationalCode','STR',0,12,0,0);
					$sql.=",CompanyNationalCode='".$NewRowInfo['CompanyNationalCode']."'";
					$OldRow_sql.=",CompanyNationalCode";
				}				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Name')){
					$NewRowInfo['Name']=Get_Input('POST','DB','Name','STR',0,32,0,0);
					$sql.=",Name='".$NewRowInfo['Name']."'";
					$OldRow_sql.=",Name";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Family')){
					$NewRowInfo['Family']=Get_Input('POST','DB','Family','STR',0,32,0,0);
					$sql.=",Family='".$NewRowInfo['Family']."'";
					$OldRow_sql.=",Family";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.FatherName')){
					$NewRowInfo['FatherName']=Get_Input('POST','DB','FatherName','STR',0,32,0,0);
					$sql.=",FatherName='".$NewRowInfo['FatherName']."'";
					$OldRow_sql.=",FatherName";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Nationality')){
					$NewRowInfo['Nationality']=Get_Input('POST','DB','Nationality','STR',0,64,0,0);
					$sql.=",Nationality='".$NewRowInfo['Nationality']."'";
					$OldRow_sql.=",Nationality";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Mobile')){
					$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,11,0,0);
					$sql.=",Mobile='".$NewRowInfo['Mobile']."'";
					$OldRow_sql.=",Mobile";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.NationalCode')){
					$NewRowInfo['NationalCode']=Get_Input('POST','DB','NationalCode','STR',0,10,0,0);
					$sql.=",NationalCode='".$NewRowInfo['NationalCode']."'";
					$OldRow_sql.=",NationalCode";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.BirthDate')){
					$NewRowInfo['BirthDate']=Get_Input('POST','DB','BirthDate','DateOrBlank',0,0,0,0);
					$sql.=",BirthDate='".$NewRowInfo['BirthDate']."'";
					$OldRow_sql.=",BirthDate";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Phone')){
					$NewRowInfo['Phone']=Get_Input('POST','DB','Phone','STR',0,32,0,0);
					$sql.=",Phone='".$NewRowInfo['Phone']."'";
					$OldRow_sql.=",Phone";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Address')){
					$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,255,0,0);
					$sql.=",Address='".$NewRowInfo['Address']."'";
					$OldRow_sql.=",Address";
				}
				
				$sql.=" Where (User_Id='".$NewRowInfo['User_Id']."')";
				$OldRow_sql.=" from Huser Where (User_Id='".$NewRowInfo['User_Id']."')";
				
				CopyTableToArray($tmpArray,$OldRow_sql);
				$OldRowInfo=$tmpArray[0];
				
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','User',$NewRowInfo['User_Id'],'User',"Update Fail,Table=User affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=User affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));
				
				
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'User',$NewRowInfo['User_Id'],'User')){
					logunfair("UnFair",'User',$NewRowInfo['User_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
        break;
	case "change":
				DSDebug(1,"DSBriefUser_EditRender.php  $act********************************************");
				$User_Id=Get_Input('POST','DB','User_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("Select count(1) from Huser where User_ID='$User_Id'")!=1)
					ExitError("کاربر نامعتبر");
				$ItemName=Get_Input('POST','DB','ItemName','ARRAY',Array("Pass","Reseller_Id","Username","Status_Id","InitialMonthOff","MaxPrepaidDebit","Visp_Id","Center_Id","Supporter_Id"),0,0,0);
				switch ($ItemName){
					case "Pass":
							exitifnotpermituser($User_Id,"Visp.User.ChangePassword");
							$Pass=Get_Input('POST','DB','Pass','STR',1,32,0,0);
							$sql="Update Huser set Pass='$Pass' where User_Id='$User_Id'";
							DBUpdate($sql);
							logdb('Edit','User',$User_Id,'User',"کلمه عبور تغییر کرد");
							echo "OK~";
						break;
					case "Reseller_Id":
							exitifnotpermituser($User_Id,"Visp.User.ChangeReseller");
							$Reseller_Id=Get_Input('POST','DB','Reseller_Id','STR',1,32,0,0);
							
							$NewResellerName=DBSelectAsString("Select ResellerName from Hreseller where Reseller_Id='$Reseller_Id' and ISOperator='No' and IsEnable='Yes'");
							if($NewResellerName=='')
								ExitError("نماینده فروش نامعتبر انتخاب شده");
							$OldResellerName=DBSelectAsString("Select ResellerName from Hreseller r join Huser u on u.User_Id=$User_Id and r.Reseller_Id=u.Reseller_Id");
							
							if($NewResellerName==$OldResellerName)
								ExitError("نام جدید نماینده فروش و نام قدیم نماینده فروش یکسان است");

							$sql="Update Huser set Reseller_Id='$Reseller_Id' where User_Id='$User_Id'";
							DBUpdate($sql);
							logdb('Edit','User',$User_Id,'Reseller',"Reseller Changed from $OldResellerName to $NewResellerName");
							echo "OK~";
						break;
					case "Username":
							exitifnotpermituser($User_Id,"Visp.User.ChangeUsername");
							
							$Username=Get_Input('POST','DB','Username','STR',1,32,0,0);
							
							$OldUsername=DBSelectAsString("Select Username From Huser where User_Id='$User_Id'");
							
							if($Username==$OldUsername)
								ExitError("نام جدید کاربری و نام قدیم کاربری یکسان است");
							
							$IsDuplicate=DBSelectAsString("Select Count(*) From Huser where Username='$Username'");
							if($IsDuplicate>0)
								ExitError('یک نسخه از نام کاربری وجود دارد');
							
							
							$sql =" Update Huser set ";
							$sql.=" Username='$Username'";

							$Warning=Array();
							
							$CurrentVisp_Id=DBSelectAsString("Select Visp_Id From Huser Where User_Id=$User_Id");
							$IfCurrentVispMatched=DBSelectAsString("SELECT Count(*) FROM Hvisp Where Visp_Id=$CurrentVisp_Id And '$Username' REGEXP UsernamePattern=1 ");
							if($IfCurrentVispMatched==0){//check if found another vidp matche username
								$Visp_Id=DBSelectAsString("SELECT Visp_Id FROM Hvisp Where '$Username' REGEXP UsernamePattern=1 order by VispName asc Limit 1");
								if($Visp_Id==0){ExitError('الگوی نام کاربری در هیچ یک از ارائه دهندگان مجازی اینترنت مطابقت ندارد');}
								else $sql.=",Visp_Id=$Visp_Id";
								array_push($Warning,"Visp");
							}

							$CurrentCenter_Id=DBSelectAsString("Select Center_Id From Huser Where User_Id=$User_Id");
							$IfCurrentCenterMatched=DBSelectAsString("SELECT Count(*) FROM Hcenter Where Center_Id=$CurrentCenter_Id And '$Username' REGEXP UsernamePattern=1 ");
							if($IfCurrentCenterMatched==0){//check if found another vid matche username
								$Center_Id=DBSelectAsString("SELECT Center_Id FROM Hcenter Where '$Username' REGEXP UsernamePattern=1 order by CenterName asc Limit 1");
								if($Center_Id==0){ExitError('الگوی نام کاربری در هیچ یک از مراکز مطابقت ندارد');}
								else {$sql.=",Center_Id=$Center_Id";
								
									$IsBusyPort=DBSelectAsString("SELECT IsBusyPort from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where User_Id=$User_Id");
									if($IsBusyPort=='Yes') {
										if($CurrentCenter_Id<>$Center_Id){
											//$n=DBSelectAsString("SELECT Count(*) from Huser Where Center_Id=$Center_Id");
											$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=$Center_Id and IsBusyPort='Yes'");
											$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id=$Center_Id");
											if($n>=$max)
												ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده است");
										}
									}	
								}
								array_push($Warning,"Center");
							}
							
							$CurrentSupporter_Id=DBSelectAsString("Select Supporter_Id From Huser Where User_Id=$User_Id");
							$IfCurrentSupporterMatched=DBSelectAsString("SELECT Count(*) FROM Hsupporter Where Supporter_Id=$CurrentSupporter_Id And '$Username' REGEXP UsernamePattern=1 ");
							if($IfCurrentSupporterMatched==0){//check if found another vidp matche username
								$Supporter_Id=DBSelectAsString("SELECT Supporter_Id FROM Hsupporter Where '$Username' REGEXP UsernamePattern=1 order by SupporterName asc Limit 1");
								if($Supporter_Id==0){ExitError('الگوی نام کاربری در هیچ یک از پشتیبان ها مطابقت ندارد');}
								else $sql.=",Supporter_Id=$Supporter_Id";
								array_push($Warning,"Supporter");
							}
							$sql.=" Where ";
							$sql.=" User_Id=$User_Id";
							DBUpdate($sql);
							DBDelete("Delete From Tuser_log Where Username='$Username' or User_Id=$User_Id");
							logdb('Edit','User',$User_Id,'User',"User Changed From [$OldUsername] to [$Username]");
							if(count($Warning)>0)
								echo "OK~".implode(" & ",$Warning)." changed due to user pattern";
							else
								echo "OK~";
						break;
					case "Status_Id":
							
							$NewRowInfo=array();
							
							$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id='$User_Id'");
							
							exitifnotpermit($Visp_Id,"Visp.User.Status.ChangeStatus");
							
							$To_Status_Id=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);


							//Check Permition
							//StatusTo
							$From_Status_Id=DBSelectAsString("Select Status_Id From Huser where (User_Id=$User_Id)");
							if($From_Status_Id==$To_Status_Id)
								ExitError("وضعیت جدید و قدیم  یکسان است");
							$Checked=DBSelectAsString("Select Checked From Hstatus_statusto Where (Status_Id=$From_Status_Id)And(StatusTo_Id=$To_Status_Id)");
							if($Checked!='Yes') {
								$StatusFrom=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$From_Status_Id");
								$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
								ExitError("مجاز نیست [$StatusTo] به  [$StatusFrom] تغییر وضعیت از");
							}
							//Reseller-Access
							$ResellerAccess=DBSelectAsString("Select ResellerAccess From Hstatus Where (Status_Id=$To_Status_Id)");
							if($ResellerAccess!='All'){
								$Checked=DBSelectAsString("Select Checked From Hstatus_reselleraccess Where (Status_Id=$To_Status_Id)And(Reseller_Id=$LReseller_Id)");
								if($Checked!='Yes') {
									$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
									ExitError("شما مجاز به تغییر وضعیت به وضعیت زیر نیستید</br>[$StatusTo]!");
								}
							}
							//VispAccess
							$VispAccess=DBSelectAsString("Select VispAccess From Hstatus Where (Status_Id=$To_Status_Id)");
							if($VispAccess!='All'){
								$Checked=DBSelectAsString("Select Checked From Hstatus_vispaccess Where (Status_Id=$To_Status_Id)And(Visp_Id=$Visp_Id)");
								if($Checked!='Yes') {
									$StatusFrom=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$From_Status_Id");
									$StatusTo=DBSelectAsString("Select StatusName from Hstatus where Status_Id=$To_Status_Id");
									ExitError("کاربر این ارائه دهنده مجازی،نمی تواند وضعیت را به وضعیت زیر تغییر دهد</br>[$StatusTo]");
								}
							}
							
							//check count of center
							$OldIsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id=$From_Status_Id");
							$NewIsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id=$To_Status_Id");
							DSDebug(1,"OldIsBusyPort=$OldIsBusyPort show change to NewIsBusyPort=$NewIsBusyPort");
							if($NewIsBusyPort=='Yes' && $OldIsBusyPort=='No'){
								$Center_Id=DBSelectAsString("SELECT Center_Id from Huser Where User_Id=$User_Id");
								$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=$Center_Id and IsBusyPort='Yes'");
								$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id=$Center_Id");
								DSDebug(2,"$n busy port exist in this center(Center_Id=$Center_Id). Max available port is $max");
								if($n>=$max)
										ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده است");
							}
							
							//----------------------
							$sql ="insert Huser_status set StatusCDT=Now(),";
							$sql.="Reseller_Id='$LReseller_Id',";
							$sql.="User_Id='$User_Id',";
							$sql.="Status_Id='$To_Status_Id'";

							$RowId=DBInsert($sql);
							$StatusName=DBSelectAsString("Select StatusName From Hstatus where Status_Id='$To_Status_Id'");
							logdb("Edit","User",$User_Id,"Status","Set to $StatusName");

							DBSelectAsString("Select ActivateUserNextServiceBase($User_Id)");
							DBSelectAsString("Select ActivateUserNextServiceIP($User_Id)");

							echo "OK~";
						break;
					case "InitialMonthOff":
							
							exitifnotpermituser($User_Id,"Visp.User.SetInitialMonthOff");
							$InitialMonthOff=Get_Input('POST','DB','InitialMonthOff','INT',0,999,0,0);//Get_POST($mysqli,"enpass");
							$sql =" Update Huser set ";
							$sql.=" InitialMonthOff='$InitialMonthOff'";
							$sql.=" Where ";
							$sql.=" User_Id=$User_Id";
							$r=DBUpdate($sql);
							if($r<=0)
								ExitError("تخفیف اولیه درحال حاضر</br>$InitialMonthOff");
								
							logdb('Edit','User',$User_Id,'User',"InitialMonthOff Changed to $InitialMonthOff");
							echo "OK~";
						break;
					case "MaxPrepaidDebit":
							
							exitifnotpermituser($User_Id,"Visp.User.SetMaxPrepaidDebit");
							$MaxPrepaidDebit=Get_Input('POST','DB','MaxPrepaidDebit','PRC',1,14,0,0);
							$sql =" Update Huser set ";
							$sql.=" MaxPrepaidDebit='$MaxPrepaidDebit'";
							$sql.=" Where ";
							$sql.=" User_Id=$User_Id";
							$r=DBUpdate($sql);
							if($r<=0)
								ExitError("حداکثر بدهی مجاز دی حال حاضر</br>$MaxPrepaidDebit");
							logdb('Edit','User',$User_Id,'User',"MaxPrepaidDebit Changed to $MaxPrepaidDebit");
							echo "OK~";
						break;
					case "Visp_Id":
							exitifnotpermituser($User_Id,"Visp.User.Info.EditField.Visp_Id");
							$Visp_Id=Get_Input('POST','DB','Visp_Id','INT',1,4294967295,0,0);
							$VispName=DBSelectAsString("Select VispName from Hvisp where Visp_Id='$Visp_Id'");
							
							$OldVispId=DBSelectAsString("Select Visp_Id from Huser where User_Id='$User_Id'");
							if($Visp_Id==$OldVispId)
								ExitError("ارائه دهنده مجازی اینترنت در حال حاضر</br>$VispName");
							
							$ISEnableVisp=DBSelectAsString("Select IsEnable from Hvisp where Visp_Id='$Visp_Id'");
							if($ISEnableVisp!='Yes')
								ExitError("این ارائه دهنده مجازی اینترنت،فعال نیست");
							//-------------------------------------------------------------------------
							$Username=DBSelectAsString("select Username from Huser where User_Id='$User_Id'");
							$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hvisp where Visp_Id='$Visp_Id'");
							if($ISUsernameOk!=1){
								$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hvisp where Visp_Id='$Visp_Id'");
								ExitError("الگوی نام کاربری ارائه دهنده مجازی اینترنت مطابقت ندارد</br>$UsernamePattern");
							}
							
							$sql =" Update Huser set ";
							$sql.=" Visp_Id='$Visp_Id'";
							$sql.=" Where ";
							$sql.=" User_Id=$User_Id";
							$r=DBUpdate($sql);
							
							logdb('Edit','User',$User_Id,'User',"Visp Changed to $VispName[$Visp_Id]");
							echo "OK~";
						break;
					case "Center_Id":
							exitifnotpermituser($User_Id,"Visp.User.Info.EditField.Center_Id");
							$Center_Id=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
							$CenterName=DBSelectAsString("Select CenterName from Hcenter where Center_Id='$Center_Id'");
							
							$OldCenter=DBSelectAsString("SELECT Center_Id from Huser Where User_Id=".$User_Id);
							if($Center_Id==$OldCenter)
								ExitError("مرکز در حال حاضر</br>$CenterName");


							$IsBusyPort=DBSelectAsString("SELECT IsBusyPort from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where User_Id=".$User_Id);
							if($IsBusyPort=='Yes') {
								$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id='$Center_Id' and IsBusyPort='Yes'");
								$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id='$Center_Id'");
								if($n>=$max)
										ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده است");
							}
							$Username=DBSelectAsString("select Username from Huser where User_Id='$User_Id'");							
							$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hcenter where Center_Id='$Center_Id'");
							if($ISUsernameOk!=1){
								$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hcenter where Center_Id='$Center_Id'");
								ExitError("الگوی نام کاربری مرکز مطابقت ندارد</br>$UsernamePattern");
							}
							
							$VispAccess=DBSelectAsString("select VispAccess from Hcenter where Center_Id='$Center_Id'");
							if($VispAccess!='All'){
								$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id='$User_Id'");
								$VispAccess=DBSelectAsString("select Center_Id from Hcenter_vispaccess where Center_Id='$Center_Id' and Visp_Id='$Visp_Id' and Checked='Yes'");
								if($VispAccess<=0){
									$CenterName=DBSelectAsString("Select CenterName from Hcenter where Center_Id='$Center_Id'");
									$VispName=DBSelectAsString("Select VispName from Hvisp where Visp_Id='$Visp_Id'");
									ExitError("را ندارد ($VispName) دسترسی به این ارائه دهنده مجازی اینترنت ($CenterName) این مرکز");
								}
							}

							
							
							$sql =" Update Huser set ";
							$sql.=" Center_Id='$Center_Id'";
							$sql.=" Where ";
							$sql.=" User_Id=$User_Id";
							$r=DBUpdate($sql);
							
							logdb('Edit','User',$User_Id,'User',"Center Changed to $CenterName[$Center_Id]");
							echo "OK~";
						break;
					case "Supporter_Id":
							exitifnotpermituser($User_Id,"Visp.User.Info.EditField.Supporter_Id");
							$Supporter_Id=Get_Input('POST','DB','Supporter_Id','INT',1,4294967295,0,0);
							$SupporterName=DBSelectAsString("Select SupporterName from Hsupporter where Supporter_Id='$Supporter_Id'");
							
							$OldSupporter=DBSelectAsString("SELECT Supporter_Id from Huser Where User_Id=".$User_Id);
							if($Supporter_Id==$OldSupporter)
								ExitError("پشتیبان در حال حاضر</br>$SupporterName");

							$Username=DBSelectAsString("select Username from Huser where User_Id='$User_Id'");							
							$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hsupporter where Supporter_Id='$Supporter_Id'");
							if($ISUsernameOk!=1){
								$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hsupporter where Supporter_Id='$Supporter_Id");
								ExitError("الگوی نام کاربری پشتیبان مطابقت ندارد</br>$UsernamePattern");
							}
							
							$sql =" Update Huser set ";
							$sql.=" Supporter_Id='$Supporter_Id'";
							$sql.=" Where ";
							$sql.=" User_Id=$User_Id";
							$r=DBUpdate($sql);
							
							logdb('Edit','User',$User_Id,'User',"Supporter Changed to $SupporterName[$Supporter_Id]");
							echo "OK~";
						break;
					default:
						ExitError("مورد نامعتبر");
					
				}
		break;
	case "GetPass":
				DSDebug(1,"DSBriefUser_EditRender.php  $act********************************************");

				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");

				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				if (ISPermit($Visp_Id,"Visp.User.GetPassword")) 
					$Pass=DBSelectAsString("SELECT Pass from Huser Where User_Id=$User_Id");
				else
					$Pass='';

				echo "OK`$Pass`";
		break;
	
	case "SelectVisp_Id":
				DSDebug(1,"DSBriefUser_EditRender.php $act********************************************");

				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB',"User_Id",'INT',0,4294967295,0,0);//0 means create new row  
				
				$PermitItem_Id=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.View'");
				$CurrentVisp_Id=DBSelectAsString("Select Visp_Id From Huser where User_Id=$User_Id");
				$Username=DBSelectAsString("Select Username from Huser Where User_Id=$User_Id");
				$sql="SELECT v.Visp_Id,VispName FROM Hvisp v ".
					"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
					"where ((v.Visp_Id='$CurrentVisp_Id') Or '$Username' REGEXP UsernamePattern=1) ".
					"And((v.IsEnable='Yes')And(ISPermit='Yes')) order by VispName ASC";
				
				$res = $conn->sql->query($sql);
				
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				while($data =  $conn->sql->get_next($res)){
					if($CurrentVisp_Id==$data['Visp_Id'])
						echo '<item selected="true" value="'.$data['Visp_Id'].'" label="'.$data['VispName'].'"/>';
					else
						echo '<item value="'.$data['Visp_Id'].'" label="'.$data['VispName'].'"/>';
				}
				echo '</data>';
				// $options->render_sql($sql,"","Visp_Id,VispName","","");
		break;
	case "SelectCenter_Id":
				DSDebug(1,"DSBriefUser_EditRender.php $act********************************************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB',"User_Id",'INT',0,4294967295,0,0);//0 means create new row  
				$Username=DBSelectAsString("Select Username From Huser where User_Id=$User_Id");
				$Visp_Id=DBSelectAsString("Select Visp_Id From Huser where User_Id=$User_Id");
				$CurrentCenter_Id=DBSelectAsString("Select Center_Id From Huser where User_Id=$User_Id");
				$sql="SELECT c.Center_Id,Concat(CenterName,'-'".
						",'F',TotalPort-BadPort-(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and IsBusyPort='Yes')".
						",'R',(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and PortStatus='Reserve')".
						",'G',(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and PortStatus='GoToFree')".
						",'W',(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and PortStatus='Waiting')".
						") As CenterName from Hcenter c ".
						" Where  c.Center_Id='$CurrentCenter_Id' Or(('$Username' REGEXP UsernamePattern=1) and (VispAccess='All' or(Center_Id in (select Center_Id from Hcenter_vispaccess where Visp_Id='$Visp_Id' and Checked='Yes')))) Group by Center_Id order by CenterName asc";
				$res = $conn->sql->query($sql);
				
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				while($data =  $conn->sql->get_next($res)){
					if($CurrentCenter_Id==$data['Center_Id'])
						echo '<item selected="true" value="'.$data['Center_Id'].'" label="'.$data['CenterName'].'"/>';
					else
						echo '<item value="'.$data['Center_Id'].'" label="'.$data['CenterName'].'"/>';
				}
				echo '</data>';
				// $options->render_sql($sql,"","Center_Id,CenterName","","");
		break;
	case "SelectSupporter_Id":
				DSDebug(1,"DSBriefUser_EditRender.php $act********************************************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB',"User_Id",'INT',0,4294967295,0,0);//0 means create new row 
				$Username=DBSelectAsString("Select Username From Huser where User_Id=$User_Id");
				$CurrentSupporter_Id=DBSelectAsString("Select Supporter_Id From Huser where User_Id=$User_Id");
				$sql="SELECT Supporter_Id,SupporterName FROM Hsupporter Where Supporter_Id='$CurrentSupporter_Id' Or '$Username' REGEXP UsernamePattern=1 order by SupporterName asc";
				
				$res = $conn->sql->query($sql);
				
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				while($data =  $conn->sql->get_next($res)){
					if($CurrentSupporter_Id==$data['Supporter_Id'])
						echo '<item selected="true" value="'.$data['Supporter_Id'].'" label="'.$data['SupporterName'].'"/>';
					else
						echo '<item value="'.$data['Supporter_Id'].'" label="'.$data['SupporterName'].'"/>';
				}
				echo '</data>';
				
				// $options->render_sql($sql,"","Supporter_Id,SupporterName","","");
		break;
	case "SelectReseller_Id":
				DSDebug(1,"DSBriefUser_EditRender.php $act********************************************");
				$User_Id=Get_Input('GET','DB',"User_Id",'INT',0,4294967295,0,0);//0 means create new row 
				
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$UserReseller_Id=DBSelectAsString("Select Reseller_Id From Huser where User_Id=$User_Id");
				$sql="SELECT Reseller_Id,ResellerName ".
					"From Hreseller r Where (ISEnable='Yes')And(ISOperator='No') order by ResellerName Asc";
					
				$res = $conn->sql->query($sql);
				
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				while($data =  $conn->sql->get_next($res)){
					if($UserReseller_Id==$data['Reseller_Id'])
						echo '<item selected="true" value="'.$data['Reseller_Id'].'" label="'.$data['ResellerName'].'"/>';
					else
						echo '<item value="'.$data['Reseller_Id'].'" label="'.$data['ResellerName'].'"/>';
				}
				echo '</data>';
					
					
				// $options->render_sql($sql,"","Reseller_Id,ResellerName","","");
		break;
	case "SelectStatus_Id":
				DSDebug(1,"DSBriefUser_EditRender.php $act********************************************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$CurrentStatus_Id=DBSelectAsString("Select Status_Id from Huser where User_Id=$User_Id");
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				
				$sql=//"Select 0 As Status_Id,'-- Please Select From List --' As StatusName union ".
				"SELECT us.Status_Id As Status_Id,us.StatusName As StatusName FROM Hstatus us ".
				"Where  us.Status_Id in".
				"(Select StatusTo_Id From Hstatus_statusto WHERE (Status_Id=$CurrentStatus_Id)And(Checked='Yes'))".
				"And us.Status_Id in ".
				"(Select Status_Id From Hstatus Where(ResellerAccess='All') union Select Status_Id From Hstatus_reselleraccess where (Reseller_Id=$LReseller_Id)And(Checked='Yes'))".
				"And us.Status_Id in ".
				"(Select Status_Id From Hstatus Where(VispAccess='All') union Select Status_Id From Hstatus_vispaccess where (Visp_Id=$Visp_Id)And(Checked='Yes'))";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
		break;
	case "SelectVispByUsername":
				DSDebug(1,"DSBriefUser_EditRender SelectVisp *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
			
				$PermitItem_Id=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Add'");
				$sql="SELECT v.Visp_Id,VispName FROM Hvisp v ".
						"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
						"where (v.IsEnable='Yes')And(ISPermit='Yes') ".
						"And('$Username' REGEXP UsernamePattern=1) ".
						"order by VispName ASC";
				$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectCenterByUsername":
				DSDebug(1,"DSBriefUser_EditRender SelectCenterByUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
				$Visp_Id=Get_Input('GET','DB','Visp_Id','INT',1,4294967295,0,0);
				$sql="SELECT c.Center_Id,Concat(CenterName,'-'".
						",'F',TotalPort-BadPort-(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and IsBusyPort='Yes')".
						",'R',(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and PortStatus='Reserve')".
						",'G',(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and PortStatus='GoToFree')".
						",'W',(SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id=c.Center_Id and PortStatus='Waiting')".
						") As CenterName from Hcenter c ".
				" Where ('$Username' REGEXP UsernamePattern=1) and (VispAccess='All' or(Center_Id in (select Center_Id from Hcenter_vispaccess where Visp_Id='$Visp_Id' and Checked='Yes'))) Group by Center_Id order by CenterName asc";
				//TotalPort-BadPort-count(*)>0
				$options->render_sql($sql,"","Center_Id,CenterName","","");
        break;		
	case "SelectStatus":
				DSDebug(1,"DSBriefUser_EditRender SelectStatus *****************");
				$Visp_Id=Get_Input('GET','DB','Visp_Id','INT',1,4294967295,0,0);
				
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				// $Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
				$sql="SELECT Status_Id,StatusName from Hstatus s Where InitialStatus='Yes' and (ResellerAccess='All' or ($LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Status_Id=s.Status_Id and Checked='Yes'))) and (VispAccess='All' or ($Visp_Id in (select Visp_Id from Hstatus_vispaccess where Status_Id=s.Status_Id and Checked='Yes')))";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
        break;
    case "SelectSupporterByUsername":
				DSDebug(1,"DSBriefUser_EditRender SelectSupporterByUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
			
				$sql="SELECT Supporter_Id,SupporterName FROM Hsupporter Where IsEnable='Yes' and '$Username' REGEXP UsernamePattern=1 order by SupporterName asc";
				$options->render_sql($sql,"","Supporter_Id,SupporterName","","");
        break;
    case "checkusername":
				DSDebug(1,"DSBriefUser_EditRender checkusername *****************");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
	
				$PermitItem_Id=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Add'");
				$IsPermitVisp=DBSelectAsString("SELECT Count(*) FROM Hvisp v ".
						"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
						"where (v.IsEnable='Yes')And(ISPermit='Yes') ");
				if($IsPermitVisp==0)
					ExitError('!شما دسترسی افزودن کاربر به هیچ ارائه دهنده مجازی ای را ندارید');
				
				$NumVispPatternMatched=DBSelectAsString("Select Count(*) From Hvisp v ".
						"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
						"where (v.IsEnable='Yes')And(ISPermit='Yes')and('$Username' REGEXP UsernamePattern=1)");
				if($NumVispPatternMatched==0)
					ExitError('!الگوی نام کاربری با هیچ ارائه دهنده مجازی ای مطابقت ندارد');
				elseif($NumVispPatternMatched==1)
					$VispPatternMatched_Visp_Id=DBSelectAsString("Select concat(v.Visp_Id,'~',v.VispName) From Hvisp v ".
						"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
						"where (v.IsEnable='Yes')And(ISPermit='Yes')and('$Username' REGEXP UsernamePattern=1) limit 1");
				else
					$VispPatternMatched_Visp_Id="0~";
				
				
				$NumPatternMatched=DBSelectAsString("Select Count(*) From Hcenter where '$Username' REGEXP UsernamePattern=1");
				if($NumPatternMatched==0)
					ExitError('الگوی نام کاربری با هیچ یک از مراکز مطابقت ندارد');
				
				$NumPatternMatched=DBSelectAsString("Select Count(*) From Hsupporter where IsEnable='Yes' and '$Username' REGEXP UsernamePattern=1");
				if($NumPatternMatched==0)
					ExitError('الگوی نام کاربری با هیچ یک از پشتیبان ها مطابقت ندارد');
					
				$NumPatternMatched=DBSelectAsString("Select Count(*) from Hstatus s Where InitialStatus='Yes' and (ResellerAccess='All' or ($LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Status_Id=s.Status_Id and Checked='Yes')))");
				if($NumPatternMatched==0)
					ExitError('!وضعیت اولیه مجازی یافت نشد');
				
				$IsDuplicate=DBSelectAsString("Select Count(*) From Huser where Username='$Username'");
				if($IsDuplicate>0)
					ExitError('یک نسخه از نام کاربری وجود دارد');
					
				echo "OK~$NumVispPatternMatched~$VispPatternMatched_Visp_Id~";
        break;
	case "SelectUsername":
				DSDebug(1,"DSBriefUser_EditRender SelectUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
			
				$sql="Create temporary table SelectedUsers SELECT username FROM Huser u ";
				if($LReseller_Id!=1){
					$PermitItem_Id_Of_Visp_User_List=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.List'");
					$sql.="Left Join Hreseller_permit rgp on (u.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id_Of_Visp_User_List') Where (rgp.ISPermit='Yes') ";
				}
				else
					$sql.=" where 1 ";
				$sql.=" and Username like '%$Username%' order by Username asc limit 500";
				DBUpdate($sql);
				$options->render_sql("select Username,username from SelectedUsers","","Username,Username","","");
			
		break;
	case "SelectServiceBase":
				DSDebug(1,"DSBriefUser_EditRender-> SelectServiceBase *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Base.List");
				
				$CanAddService=DBSelectAsString("Select CanAddService from Hstatus s Left join Huser u on(s.Status_Id=u.Status_Id) where User_Id=$User_Id");
				if($CanAddService=='No')
					$sql="Select 0 As Service_Id,'NOT allowed add service'As ServiceName";
				else{
					$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
					$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
					if($CurrentBase_Service_Id>0)
						$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
					else
						$ServiceBaseAccessFilter=1;
					$sql="Select Service_Id,ServiceName From Hservice ".
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='Base')And ".
						"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						
							"Select Service_Id from Hservice Where ".
							"(VispAccess='All' and Service_Id not in ( Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes')))or ".
							"(VispAccess<>'All' and Service_Id in ( Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes'))) ".
						
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes'))) ".
						")And($ServiceBaseAccessFilter) Order By cast(Speed as unsigned) desc,(ActiveYear*365)+(ActiveMonth*30)+ActiveDay desc,ServiceName";
				}
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	case "SelectServiceExtraCredit":
				DSDebug(1,"DSBriefUser_EditRender-> SelectServiceExtraCredit *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.ExtraCredit.List");
				
				$CanAddService=DBSelectAsString("Select CanAddService from Hstatus s Left join Huser u on(s.Status_Id=u.Status_Id) where User_Id=$User_Id");
				if($CanAddService=='No')
					$sql="Select 0 As Service_Id,'NOT allowed add service'As ServiceName";
				else{
					$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
					$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
					if($CurrentBase_Service_Id>0)
						$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
					else
						$ServiceBaseAccessFilter=1;
					$sql="Select Service_Id,ServiceName From Hservice ".
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='ExtraCredit')And((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
						"))And($ServiceBaseAccessFilter) order by ServiceName";
				}
				
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	case "SelectServiceIP":
				DSDebug(1,"DSBriefUser_EditRender-> SelectServiceIP *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.List");
				// $StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
				// $EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
				$Number=1;//Get_Input('GET','DB','Number','INT',1,999999,0,0);
				
				
				

				$CanAddService=DBSelectAsString("Select CanAddService from Hstatus s Left join Huser u on(s.Status_Id=u.Status_Id) where User_Id=$User_Id");
				if($CanAddService=='No')
					$sql="Select 0 As Service_Id,'NOT allowed add service'As ServiceName";
				else{
					$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
					$CurrentService_Id=DBSelectAsString("Select Service_Id From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')");
					if($CurrentService_Id!='') $Filter="Service_Id=$CurrentService_Id";
					else $Filter=1;
					
					$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
					if($CurrentBase_Service_Id>0)
						$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
					else
						$ServiceBaseAccessFilter=1;
					
					$sql="Select Service_Id,ServiceName From Hservice ".
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='IP')And(IPCount=$Number)And($Filter)And ".
						"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
						"))And(Service_Id not in( ".
						"Select Service_Id From Huser_serviceip ".
						"Where ((ServiceStatus='Active')Or(ServiceStatus='Pending'))And(User_Id<>$User_Id) ".
						"))And($ServiceBaseAccessFilter) order by ServiceName";
				}
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
    case "SelectServiceOther":
				DSDebug(1,"DSBriefUser_EditRender-> SelectServiceOther *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.Other.List");
				
				$CanAddService=DBSelectAsString("Select CanAddService from Hstatus s Left join Huser u on(s.Status_Id=u.Status_Id) where User_Id=$User_Id");
				if($CanAddService=='No')
					$sql="Select 0 As Service_Id,'NOT allowed add service'As ServiceName";
				else{
					$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
					$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
					if($CurrentBase_Service_Id>0)
						$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
					else
						$ServiceBaseAccessFilter=1;
					$sql="Select Service_Id,ServiceName From Hservice ".
						"Where (ISEnable='Yes')and(IsDel='No')And(ResellerChoosable='Yes')And(ServiceType='Other')And ".
						"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
						"And(Service_Id in( ".
						"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
						"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
						"))And(Service_Id in( ".
						"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
						"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
						"))And($ServiceBaseAccessFilter) order by ServiceName ";
				}
				
				$options->render_sql($sql,"","Service_Id,ServiceName","","");
        break;
	case "GetServicePrice":
				DSDebug(1,"DSBriefUser_EditRender-> GetServicePrice *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				
				$Service_Id=Get_Input('GET','DB','Service_Id','INT',1,4294967295,0,0);
				
				$TempArray=Array();
				CopyTableToArray($TempArray,"Select ServiceType,Description,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash From Hservice where Service_Id=$Service_Id");
				$Description=$TempArray[0]["Description"];
				
				$OffRate=$TempArray[0]["OffRate"];
				$InstallmentNo=$TempArray[0]["InstallmentNo"];
				$InstallmentPeriod=$TempArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$TempArray[0]["InstallmentFirstCash"];
				$ServiceType=$TempArray[0]["ServiceType"];

				exitifnotpermituser($User_Id,"Visp.User.Service.$ServiceType.List");
				
				//Calculate Price
				if($ServiceType=="IP"){
					$StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
					$EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
					$Number=Get_Input('GET','DB','Number','INT',1,999999,0,0);
					$ServicePricePerDay=$TempArray[0]["Price"];
					$Diff=DBSelectAsString("Select datediff('$EndDate', '$StartDate')");
					if($Diff<=0)
						ExitError("تاریخ پایان باید از تاریخ شروع بزرگتر باشد");
					$ServicePrice=$Diff*$ServicePricePerDay;
				}
				else
					$ServicePrice=$TempArray[0]["Price"];
				
				if($InstallmentNo==0){
					$Price=$ServicePrice;
				}
				else if($InstallmentFirstCash=='Yes'){
					$Price=($ServicePrice/$InstallmentNo);
				}
				else $Price=0;
				
				$VAT=DBSelectAsString("Select Param5 From Hserver where PartName='Param'");
				if($VAT=='') $VAT=0;

				$UserCredit=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id Order by User_Payment_Id Desc");
				$UserCredit=number_format($UserCredit, $PriceFloatDigit, '.', '');
				// $RemainCredit=number_format($RemainCredit, $PriceFloatDigit, '.', '');
				
				$RemainedSavingOff=DBSelectAsString("SELECT sum(SavingOffAmount-UsedAmount) FROM Huser_savingoff where User_Id=$User_Id and SavingOffStatus='Pending' and SavingOffExpDT>Now()")*1;
				
				$ServicePrice=number_format($ServicePrice, $PriceFloatDigit, '.', '');
				$Price=number_format($Price, $PriceFloatDigit, '.', '');
				
				$Off=$OffRate*DBSelectAsString("Select FindOffValueOfUser($User_Id)");
				if($Off>0){
					$OffFormula_Id=DBSelectAsString("select OffFormula_Id from Huser where User_Id='$User_Id'");
					$SavingOffPercent=DBSelectAsString("select SavingOffPercent from Hoffformula where OffFormula_Id='$OffFormula_Id'");
					$SavingOffExpirationDays=DBSelectAsString("select SavingOffExpirationDays from Hoffformula where OffFormula_Id='$OffFormula_Id'");
					$SavingOff=$Off*$SavingOffPercent/100;
					$DirectOff=$Off-$SavingOff;
				}
				else{
					$SavingOff=0;
					$DirectOff=0;
					$SavingOffExpirationDays=0;
				}
				$Err="";
				DSDebug(0,"RemainedSavingOff`ServicePrice`InstallmentNo`Price`OffRate`Off`SavingOff`DirectOff`SavingOffExpirationDays`VAT`UserCredit`Err`Description`");
				DSDebug(0,"$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description`");
				echo "$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description`";
					
		break;		
	case "LoadIPRequest":
				DSDebug(1,"DSBriefUser_EditRender LoadIPRequest ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$StartDate=DBSelectAsString("SELECT Max(If(ServiceStatus='Cancel',CancelDT,EndDate)) From Huser_serviceip where (User_Id='$User_Id') And (ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')");
				if($StartDate=='') $StartDate=DBSelectAsString("SELECT Date(Now())");
				$shStartDate=DBSelectAsString("SELECT shDateStr(DATE_ADD('$StartDate',INTERVAL 0 DAY))");
				$shEndDate=DBSelectAsString("SELECT shDateStr(DATE_ADD('$StartDate',INTERVAL 1 DAY))");
				$Number=DBSelectAsString("SELECT IPCount From Hservice s Left join Huser_serviceip u_si On s.Service_Id=u_si.Service_Id where (User_Id='$User_Id')And(ServiceType='IP')And(ServiceStatus='Active')");
				if($Number=='')
					$Number=1;
				echo "Ok`$shStartDate`$shEndDate`$Number";
		break;
	case "CheckIPRequest":
				DSDebug(1,"-> DSBriefUser_EditRender CheckIPRequest");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Service.IP.List");
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				$StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
				$EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
				$n=DBSelectAsString("Select Count(*) From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')and((('$StartDate'>=StartDate)And('$StartDate'<EndDate))or(('$EndDate'>=StartDate)And('$EndDate'<EndDate)))");
				if($n>0)
					ExitError('کاربر در این بازه زمانی دارای سرویس دیگری است');
				
				$Number=Get_Input('GET','DB','Number','INT',1,999999,0,0);
				if($StartDate>=$EndDate)
					ExitError('تاریخ پایان باید از تاریخ شروع بزرگتر باشد');
					
				$CurrentService_Id=DBSelectAsString("Select Service_Id From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus<>'Used')And(ServiceStatus<>'Cancel')");
				if($CurrentService_Id!='') $Filter="Service_Id=$CurrentService_Id";
				else $Filter=1;
				$sql="Select Count(*) From Hservice ".
					"Where (ISEnable='Yes')And(ResellerChoosable='Yes')And(ServiceType='IP')And(IPCount=$Number)And($Filter)And ".
					"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
					"And(Service_Id in( ".
					"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug,Hservice_class s_ug ".
					"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
					"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
					"))And(Service_Id not in( ".
					"Select Service_Id From Huser_serviceip ".
					"Where ((ServiceStatus='Active')Or(ServiceStatus='Pending'))And(User_Id<>$User_Id) ".//sure IP not user by another
					"))";


//					"((('$StartDate'>=StartDate)And('$StartDate'<EndDate))or(('$EndDate'>=StartDate)And('$EndDate'<EndDate)))".
					
				$n=DBSelectAsString($sql);
				if($n<=0)
					ExitError('هیچ سرویس آی پی موجود نیست!!!');
					
				echo "OK,";
		break;
	case "AddService":
				DSDebug(1,"DSBriefUser_EditRender AddService ******************************************");
				global $CurrencySymbol;
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				
				$Service_Id=Get_Input('POST','DB','Service_Id','INT',0,4294967295,0,0);
				if($Service_Id==0) ExitError('لطفا سرویس را انتخاب کنید');
				
				$WithdrawSavingOff=Get_Input('POST','DB','WithdrawSavingOff','PRC',0,14,0,0);
				//check if Hreseller allowed add this Hservice
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				$CurrentBase_Service_Id=DBSelectAsString("SELECT Service_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($CurrentBase_Service_Id>0)
					$ServiceBaseAccessFilter="(ServiceBaseAccess='All')or(Service_Id in (select Service_Id from Hservice_servicebaseaccess where Accessed_Service_Id='$CurrentBase_Service_Id' and Checked='Yes'))";
				else
					$ServiceBaseAccessFilter=1;
				$ServiceInfoArray=Array();
				$n=CopyTableToArray($ServiceInfoArray,
					"Select ServiceName,MaxYearlyCount,MaxMonthlyCount,MaxActiveCount,Price,OffRate,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,ServiceType From Hservice ".
					"Where (Service_Id=$Service_Id)And(ISEnable='Yes')And(ResellerChoosable='Yes')And".
					"((AvailableFromDate=0)Or(Date(Now())>=AvailableFromDate))And((AvailableToDate=0)Or(Date(Now())<AvailableToDate))".
					"And(Service_Id in( ".
					"Select Service_Id from Hservice Where ClassAccess='All' union Select Service_Id from  Huser_class u_ug, Hservice_class s_ug ".
					"Where (User_Id=1)And(u_ug.Class_Id=s_ug.Class_Id)And(u_ug.Checked='Yes')And(s_ug.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where VispAccess='All' union Select Service_Id from Hservice_vispaccess s_va Where (Visp_Id=$Visp_Id)And(s_va.Checked='Yes') ".
					"))And(Service_Id in( ".
					"Select Service_Id from Hservice Where ResellerAccess='All' union Select Service_Id from Hservice_reselleraccess s_rga ".
					"Where ((Reseller_Id=$LReseller_Id)And(s_rga.Checked='Yes')) ".
					"))And(Service_Id not in( ".
					"Select Service_Id From Huser_serviceip ".
					"Where ((ServiceStatus='Active')Or(ServiceStatus='Pending'))And(User_Id<>$User_Id) ".
					"))And($ServiceBaseAccessFilter)");

				if($n!=1) {
					ExitError('مجاز نیست');
					$Service_Name=DBSelectAsString("Select ServiceName From Hservice Where Service_Id=$Service_Id");
					logsecurity('HackTry',"Try Add Service id=[$Service_Id] SeviceName=[$Service_Name]");	
				}
				$ServiceName=$ServiceInfoArray[0]["ServiceName"];
				$MaxYearlyCountAllowed=$ServiceInfoArray[0]["MaxYearlyCount"];
				$MaxMonthlyCountAllowed=$ServiceInfoArray[0]["MaxMonthlyCount"];
				$MaxActiveCountAllowed=$ServiceInfoArray[0]["MaxActiveCount"];

				$OffRate=$ServiceInfoArray[0]["OffRate"];
				$InstallmentNo=$ServiceInfoArray[0]["InstallmentNo"];
				$InstallmentPeriod=$ServiceInfoArray[0]["InstallmentPeriod"];
				$InstallmentFirstCash=$ServiceInfoArray[0]["InstallmentFirstCash"];

				$ServiceType=$ServiceInfoArray[0]["ServiceType"];
				
				exitifnotpermituser($User_Id,"Visp.User.Service.$ServiceType.Add");
				
				//Calculate Price
				if($ServiceType=="IP"){
					$StartDate=Get_Input('GET','DB','StartDate','DateOrBlank',0,0,0,0);
					$EndDate=Get_Input('GET','DB','EndDate','DateOrBlank',0,0,0,0);
					$Number=Get_Input('GET','DB','Number','INT',1,999999,0,0);
					$ServicePricePerDay=$ServiceInfoArray[0]["Price"];
					$Diff=DBSelectAsString("Select datediff('$EndDate', '$StartDate')");
					if($Diff<=0)
						ExitError("تاریخ پایان باید از تاریخ شروع بزرگتر باشد");
					$ServicePrice=$Diff*$ServicePricePerDay;
				}
				else{
					$StartDate="";
					$EndDate="";
					$ServicePrice=$ServiceInfoArray[0]["Price"];				
				}
				
				/*
				$n=DBSelectAsString("Select Count(*) From Huser_serviceip where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($n>0) ExitError('Only one Active Service IP is Allowed');
				
				$IPOwnerUser_Id=DBSelectAsString("Select User_Id From Huser_serviceip where (Service_Id=$Service_Id)And(ServiceStatus in('Active','Pending'))And(EndDate>Date(now())) Limit 1");
				if($IPOwnerUser_Id!=''){
					if($IPOwnerUser_Id==$User_Id)
						ExitError("This IP Service Used By Yourself");
					else{	
						$UserName=DBSelectAsString("Select UserName From Huser Where User_Id=$IPOwnerUser_Id");
						ExitError("This IP Service Used By User[$UserName]");
					}	
				}
				*/
				$PayPlan=Get_Input('POST','DB','PayPlan','ARRAY',array("PrePaid","PostPaid"),0,0,0);
				exitifnotpermituser($User_Id,"Visp.User.PayPlan.".$PayPlan);
				
				if($InstallmentNo==0){
					$Price=$ServicePrice;
				}
				else if($InstallmentFirstCash=='Yes'){
					$Price=($ServicePrice/$InstallmentNo);
				}
				else $Price=0;
				$Price=$Price;
				
				$Off=$OffRate*DBSelectAsString("Select FindOffValueOfUser($User_Id)");
				if($Off>0){
					$OffFormula_Id=DBSelectAsString("select OffFormula_Id from Huser where User_Id='$User_Id'");
					$SavingOffPercent=DBSelectAsString("select SavingOffPercent from Hoffformula where OffFormula_Id='$OffFormula_Id'");
					$DirectOff=$Off*(100-$SavingOffPercent)/100;
				}
				else
					$DirectOff=0;
				
				if($WithdrawSavingOff>0){
					if($WithdrawSavingOff>$Price)
						return "Cannot withdraw ".number_format($WithdrawSavingOff, $PriceFloatDigit, '.', ',')." $CurrencySymbol amount of Saving Off whilst service price is only ".number_format($Price, $PriceFloatDigit, '.', ',')." $CurrencySymbol";
					
					$RemainedSavingOff=DBSelectAsString("SELECT sum(SavingOffAmount) FROM Huser_savingoff where User_Id=$User_Id and SavingOffStatus='Pending' and SavingOffExpDT>Now()")*1;
					if($WithdrawSavingOff>$RemainedSavingOff)
						Return "User has only $RemainedSavingOff $CurrencySymbol SavingOff. Cannot withdraw $WithdrawSavingOff $CurrencySymbol";
					
					$Price-=$WithdrawSavingOff;
				}
				else
					$WithdrawSavingOff=0;
				
				$DirectOffAmount=$Price*$DirectOff/100;
				
				$PriceWithOff=$Price-$DirectOffAmount;
				$PriceWithOff=number_format($PriceWithOff, $PriceFloatDigit, '.', '');
				
				$VAT=DBSelectAsString("Select Param5 From Hserver where PartName='Param'");
				if($VAT=='') $VAT=0;
				$PriceWithVAT=$PriceWithOff*(1+$VAT/100);
				$PriceWithVAT=number_format($PriceWithVAT, $PriceFloatDigit, '.', '');
				
				$UserCredit=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id Order by User_Payment_Id Desc");
				$RemainCredit=$PriceWithVAT-$UserCredit;
				if($RemainCredit<0) $RemainCredit=0;
				
				 //Check If User Have Enough credit
				$MaxPrepaidDebit=DBSelectAsString("Select MaxPrepaidDebit from Huser where User_Id='$User_Id'");
				if(($RemainCredit>$MaxPrepaidDebit)&&($LReseller_Id!=1)&&($PayPlan!='PostPaid'))
					ExitError(
						"User have not enough credit to add ".
						number_format($PriceWithVAT, $PriceFloatDigit, '.', ',').
						" $CurrencySymbol debit!!! (UserCredit is ".
						number_format($UserCredit, $PriceFloatDigit, '.', ',').
						". You can at most add ".
						number_format($MaxPrepaidDebit, $PriceFloatDigit, '.', ',').
						" debit for this user.)"
					);
				
				$res=AddServiceToUser($LReseller_Id,$User_Id,$Service_Id,$PayPlan,$StartDate,$EndDate,$WithdrawSavingOff);
				if($res!="")
					ExitError($res);
				DBSelectAsString("Select ActivateUserNextServiceIP($User_Id)");
				echo "OK~";
				
        break;		
	case "GetUserPayBalance":
				DSDebug(0,"DSBriefUser_EditRender->GetUserPayBalance ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.List");
				$PayBalance=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc limit 1");
				echo "OK~".number_format($PayBalance, $PriceFloatDigit, '.', '');
		break;
	case "AddPayment":
				DSDebug(1,"DSBriefUser_EditRender AddPayment ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.List");

				$PaymentType=Get_Input('POST','DB','PaymentType','ARRAY',array('Cash','Cheque','Pos','Deposit','Other','TAX','Off'),0,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Payment.PaymentType.".$PaymentType);
				$Price=Get_Input('POST','DB','Price','PRC',1,14,0,0);
				if($Price<=0){
					ExitError("مبلغ باید بیشتر از ۰ باشد");
				}
				$Direction=Get_Input('POST','DB','Direction','ARRAY',array('GetMoney','RefundMoney'),0,0,0);					
				exitifnotpermituser($User_Id,"Visp.User.Payment.Add.".$Direction);//GetMoney or RefundMoney
				if($Direction=='RefundMoney'){//check if user have credit
					//reseller want to pay money to User, check if User have money
					$Paybalance=DBSelectAsString("Select Paybalance From Huser_payment Where User_Id=$User_Id order by User_Payment_Id desc limit 1");
					if(($Price>$Paybalance)&&($LReseller_Id!=1))
						ExitError("کاربر دارای تراز مالی زیر است ولی شما تلاش می کنید مبلغ بیشتری را به کاربر برگشت دهید</br>$Paybalance");
					$Price=-$Price;
				}
				else{//check if reseller have enough money
					if($LReseller_Id!=1){
						$FromResellerCreditBalance=DBSelectAsString("Select CreditBalance From Hreseller_transaction Where Reseller_Id=$LReseller_Id Order by reseller_transaction_Id  desc Limit 1");
						if($FromResellerCreditBalance=='')$FromResellerCreditBalance=0;
						if($FromResellerCreditBalance<$Price)
							ExitError("شما اعتبار کافی ندارید(تراز اعتبار  $FromResellerCreditBalance<".$Price.")");				
					}
				
					
				}
				$VoucherNo=Get_Input('POST','DB','VoucherNo','STR',0,15,0,0);
				$VoucherDate=Get_Input('POST','DB','VoucherDate','DateOrBlank',0,0,0,0);
				$BankBranchName=Get_Input('POST','DB','BankBranchName','STR',0,32,0,0);
				$BankBranchNo=Get_Input('POST','DB','BankBranchNo','STR',0,32,0,0);
				$Comment=Get_Input('POST','DB','Comment','STR',0,256,0,0);


				
				//-----------------------------------

				if($Price>=0)
					AddResellerTransaction($LReseller_Id,0,$User_Id,'GetMoney',(-1)*$Price);
				else
					AddResellerTransaction($LReseller_Id,0,$User_Id,'RefundMoney',(-1)*$Price);
				
				$RowId=AddPaymentToUser($LReseller_Id,$User_Id,$PaymentType,$Price,$VoucherNo,$VoucherDate,$BankBranchName,$BankBranchNo,$Comment);
				$RowInfo=LoadRowInfoSqlAsStr("Select User_Payment_Id,{$DT}DateTimeStr(User_PaymentCDT) as User_PaymentCDT,PaymentType,".
											"Format(Price,$PriceFloatDigit) AS Price,Format(PayBalance,$PriceFloatDigit) AS PayBalance,VoucherNo,".
											"{$DT}DateStr(VoucherDate) as VoucherDate,BankBranchName,BankBranchNo ".
											"From Huser_payment Where User_Payment_Id=$RowId");
				
				logdb("Edit","User",$User_Id,"Payment",$RowInfo);

				echo "OK~";
        break;
	case "GetCreditInfo":
				DSDebug(1,"DSBriefUser_EditRender GetCreditInfo ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.CreditStatus.List");
				
				$sql="Select ServiceStatus,ServiceName, ".
				"s.ActiveYear,s.ActiveMonth,s.ActiveDay,ExtraDay,".
				"{$DT}DateStr(u.StartDate) As StartDate,{$DT}DateStr(u.EndDate) As EndDate, ".
				"{$DT}DateTimeStr(Now()) As CurrentDT,".
				
				"User_Gift_Id,{$DT}DateTimeStr(GiftEndDT) As GiftEndDT,".
				"GiftTrafficRate,GiftTimeRate,ByteToR(GiftExtraTr) As GiftExtraTr,SecondToR(GiftExtraTi)As GiftExtraTi,Hg.MikrotikRateName as GiftMikrotikRate,".
				
				// "ByteToR(ServiceFreeTrU) as ServiceFreeTrU,".
				
				"ByteToR(Tu_u.ETrA-ETrU) As ETrR,ByteToR(ETrU) As ETrU,ByteToR(Tu_u.ETrA) As ETrA,".
				"If(Tu_u.STrA=0,'UL',ByteToR(Tu_u.STrA-STrU)) As STrR,ByteToR(STrU) As STrU,If(Tu_u.STrA=0,'UL',ByteToR(Tu_u.STrA)) As STrA, ".
				"If(Tu_u.YTrA=0,'UL',ByteToR(Tu_u.YTrA-YTrU)) As YTrR,ByteToR(YTrU) As YTrU,If(Tu_u.YTrA=0,'UL',ByteToR(Tu_u.YTrA)) As YTrA, ".
				"If(Tu_u.MTrA=0,'UL',ByteToR(Tu_u.MTrA-MTrU)) As MTrR,ByteToR(MTrU) As MTrU,If(Tu_u.MTrA=0,'UL',ByteToR(Tu_u.MTrA)) As MTrA, ".
				"If(Tu_u.WTrA=0,'UL',ByteToR(Tu_u.WTrA-WTrU)) As WTrR,ByteToR(WTrU) As WTrU,If(Tu_u.WTrA=0,'UL',ByteToR(Tu_u.WTrA)) As WTrA, ".
				"If(Tu_u.DTrA=0,'UL',ByteToR(Tu_u.DTrA-DTrU)) As DTrR,ByteToR(DTrU) As DTrU,If(Tu_u.DTrA=0,'UL',ByteToR(Tu_u.DTrA)) As DTrA, ".
				"SecondToR(Tu_u.ETiA-ETiU) As ETiR,SecondToR(ETiU) As ETiU,SecondToR(Tu_u.ETiA) As ETiA,".
				"If(Tu_u.STiA=0,'UL',SecondToR(Tu_u.STiA-STiU)) As STiR,SecondToR(STiU) As STiU,If(Tu_u.STiA=0,'UL',SecondToR(Tu_u.STiA)) As STiA, ".
				"If(Tu_u.YTiA=0,'UL',SecondToR(Tu_u.YTiA-YTiU)) As YTiR,SecondToR(YTiU) As YTiU,If(Tu_u.YTiA=0,'UL',SecondToR(Tu_u.YTiA)) As YTiA, ".
				"If(Tu_u.MTiA=0,'UL',SecondToR(Tu_u.MTiA-MTiU)) As MTiR,SecondToR(MTiU) As MTiU,If(Tu_u.MTiA=0,'UL',SecondToR(Tu_u.MTiA)) As MTiA, ".
				"If(Tu_u.WTiA=0,'UL',SecondToR(Tu_u.WTiA-WTiU)) As WTiR,SecondToR(WTiU) As WTiU,If(Tu_u.WTiA=0,'UL',SecondToR(Tu_u.WTiA)) As WTiA, ".
				"If(Tu_u.DTiA=0,'UL',SecondToR(Tu_u.DTiA-DTiU)) As DTiR,SecondToR(DTiU) As DTiU,If(Tu_u.DTiA=0,'UL',SecondToR(Tu_u.DTiA)) As DTiA, ".
				// "ByteToR(RealReceiveTr) As RealReceiveTr,ByteToR(RealSendTr) As RealSendTr,ByteToR(BugUsedTr) As BugUsedTr,ByteToR(FinishUsedTr) As FinishUsedTr,".
				// "SecondToR(RealUsedTime) As RealUsedTime,SecondToR(BugUsedTi) As BugUsedTi,SecondToR(FinishUsedTi) As FinishUsedTi, ".
				// "{$DT}DateTimeStr(Tu_u.LastRequestDT) As  LastRequestDT,".
				"SecondToR(TimeStampDiff(Second,LastRequestDT,now())) as LastUpdate".
				
				" From Tuser_usage Tu_u Left join Huser u on (Tu_u.User_Id=u.User_Id) ".
				"Left join Tuser_authhelper Tu_a on (Tu_u.User_Id=Tu_a.User_Id) ".
				"Left join Hmikrotikrate Hg on (Tu_u.GiftMikrotikRate_Id=Hg.MikrotikRate_Id) ".
				"Left join Hservice s on (u.Service_Id=s.Service_Id) ".
				"left join Huser_servicebase u_sb on(u.User_ServiceBase_Id=u_sb.User_ServiceBase_Id) ".
				
				" Where (u.User_Id=$User_Id) ";
				
				$ResArray=Array();
				$n=CopyTableToArray($ResArray,$sql);
				
				DSDebug(2,DSPrintArray($ResArray));
				$ResArray[0]["LastUpdate"]=str_replace(array("D","H","M","Sec"),array("روز","ساعت","دقیقه","ثانیه"),$ResArray[0]["LastUpdate"]);
				function CreateLabel($t){
					return "<span style='color:lightslategray'>$t </span>";
				}
				function MakeBold($t){
					return "<span style='font-weight:bold;color:indianred'>$t</span>";
				}
				
				$OutStr="<div style='font-weight:normal'>";
				
				
				$OutStr.="<fieldset class='dhxform_fs'><legend>اطلاعات سرویس</legend>";
				if($ResArray[0]["ServiceName"]!=""){
					$title="مدت: ";
					if($ResArray[0]["ActiveYear"]!=0)
						$title.=$ResArray[0]["ActiveYear"]."سال ";
					if($ResArray[0]["ActiveMonth"]!=0)
						$title.=$ResArray[0]["ActiveMonth"]."ماه ";
					if($ResArray[0]["ActiveDay"]!=0)
						$title.=$ResArray[0]["ActiveDay"]."روز ";
					
					if($ResArray[0]["ExtraDay"]!=0)
						$title.="(+".$ResArray[0]["ExtraDay"]." ExtraDay)";
					
					$OutStr.="<div title='$title'>".CreateLabel("سرویس:")."<span style='font-weight:bold'>".$ResArray[0]["ServiceName"]."</span><br/>";
					
					if($ResArray[0]["ServiceStatus"]=="Cancel")
						$OutStr.="<span style='font-size:88%;color:red'>لغو</span>";
					else{
						if($ResArray[0]["ServiceStatus"]=="Active")
							$OutStr.="<span style='font-size:88%;color:royalblue'>فعال ".CreateLabel("از").$ResArray[0]["StartDate"]." ".CreateLabel("تا").$ResArray[0]["EndDate"]."</span>";
						elseif($ResArray[0]["ServiceStatus"]=="Used")
							$OutStr.="<span style='font-size:88%;color:goldenrod'>استفاده ".CreateLabel("از").$ResArray[0]["StartDate"]." ".CreateLabel("تا").$ResArray[0]["EndDate"]."</span>";
					}
					$OutStr.="</div> ";
				}
				else
					$OutStr.="<div style='color:indianred;font-weight:bold'>کاربر هیچ سرویس پایه فعالی ندارد</div>";

				$OutStr.="</fieldset>";
				
				
				
				if(($ResArray[0]["User_Gift_Id"]>0)&&($ResArray[0]["GiftEndDT"]>$ResArray[0]["CurrentDT"])){
					$OutStr.="<fieldset class='dhxform_fs'><legend>هدیه</legend>";
					$OutStr.="<div>".CreateLabel("تاریخ پایان:")."<span style='font-weight:bold'>".$ResArray[0]["GiftEndDT"]."</span></div>";
					if($ResArray[0]["GiftTrafficRate"]!=1)
						$OutStr.="<div>".CreateLabel("ضریب ترافیک:")."<span style='font-weight:bold'>".$ResArray[0]["GiftTrafficRate"]."</span></div>";
					if($ResArray[0]["GiftTimeRate"]!=1)
						$OutStr.="<div>".CreateLabel("ضریب زمان:")."<span style='font-weight:bold'>".$ResArray[0]["GiftTimeRate"]."</span></div>";
					
					$OutStr.="<div>".CreateLabel("ترافیک اضافی:")."<span style='font-weight:bold'>".$ResArray[0]["GiftExtraTr"]."</span></div>";
					
					if($ResArray[0]["GiftExtraTi"]!=0)
						$OutStr.="<div>".CreateLabel("زمان اضافی:")."<span style='font-weight:bold'>".$ResArray[0]["GiftExtraTi"]."</span></div>";
					
					if($ResArray[0]["GiftMikrotikRate"]!="")
						$OutStr.="<div>".CreateLabel("سرعت میکروتیک:")."<span style='font-weight:bold'>".$ResArray[0]["GiftMikrotikRate"]."</span></div>";
					if($ResArray[0]["LastUpdate"]!="")
						$OutStr.="<div style='font-size:80%;color:darkgray;float:right'>".CreateLabel("آخرین بروزرسانی:")."<span style='font-weight:bold'>".$ResArray[0]["LastUpdate"]." پیش</span></div>";
					$OutStr.="</fieldset>";
				}
				
				
				
				$OutStr.="<fieldset class='dhxform_fs'><legend>ترافیک</legend>";
				$tr=array("G","M");
				$trFa=array(" گیگابایت"," مگابایت");
				$ResArray[0]["STrA"]=str_replace($tr,$trFa,$ResArray[0]["STrA"]);
				$ResArray[0]["STrU"]=str_replace($tr,$trFa,$ResArray[0]["STrU"]);
				$ResArray[0]["STrR"]=str_replace($tr,$trFa,$ResArray[0]["STrR"]);
				
				$ResArray[0]["YTrA"]=str_replace($tr,$trFa,$ResArray[0]["YTrA"]);
				$ResArray[0]["YTrU"]=str_replace($tr,$trFa,$ResArray[0]["YTrU"]);
				$ResArray[0]["YTrR"]=str_replace($tr,$trFa,$ResArray[0]["YTrR"]);
				
				$ResArray[0]["MTrA"]=str_replace($tr,$trFa,$ResArray[0]["MTrA"]);
				$ResArray[0]["MTrU"]=str_replace($tr,$trFa,$ResArray[0]["MTrU"]);
				$ResArray[0]["MTrR"]=str_replace($tr,$trFa,$ResArray[0]["MTrR"]);
				
				$ResArray[0]["WTrA"]=str_replace($tr,$trFa,$ResArray[0]["WTrA"]);
				$ResArray[0]["WTrU"]=str_replace($tr,$trFa,$ResArray[0]["WTrU"]);
				$ResArray[0]["WTrR"]=str_replace($tr,$trFa,$ResArray[0]["WTrR"]);
							
				$ResArray[0]["DTrA"]=str_replace($tr,$trFa,$ResArray[0]["DTrA"]);
				$ResArray[0]["DTrU"]=str_replace($tr,$trFa,$ResArray[0]["DTrU"]);
				$ResArray[0]["DTrR"]=str_replace($tr,$trFa,$ResArray[0]["DTrR"]);
				
				$ResArray[0]["ETrA"]=str_replace($tr,$trFa,$ResArray[0]["ETrA"]);
				$ResArray[0]["ETrU"]=str_replace($tr,$trFa,$ResArray[0]["ETrU"]);
				$ResArray[0]["ETrR"]=str_replace($tr,$trFa,$ResArray[0]["ETrR"]);
				
				$ti=array("H","M","Sec");
				$tiFa=array("ساعت"," دقیفه"," ثانیه ");
				
				
				$ResArray[0]["STiA"]=str_replace($ti,$tiFa,$ResArray[0]["STiA"]);
				$ResArray[0]["STiU"]=str_replace($ti,$tiFa,$ResArray[0]["STiU"]);
				$ResArray[0]["STiR"]=str_replace($ti,$tiFa,$ResArray[0]["STiR"]);
				
				$ResArray[0]["YTiA"]=str_replace($ti,$tiFa,$ResArray[0]["YTiA"]);
				$ResArray[0]["YTiU"]=str_replace($ti,$tiFa,$ResArray[0]["YTiU"]);
				$ResArray[0]["YTiR"]=str_replace($ti,$tiFa,$ResArray[0]["YTiR"]);
				
				$ResArray[0]["MTiA"]=str_replace($ti,$tiFa,$ResArray[0]["MTiA"]);
				$ResArray[0]["MTiU"]=str_replace($ti,$tiFa,$ResArray[0]["MTiU"]);
				$ResArray[0]["MTiR"]=str_replace($ti,$tiFa,$ResArray[0]["MTiR"]);
				
				$ResArray[0]["WTiA"]=str_replace($ti,$tiFa,$ResArray[0]["WTiA"]);
				$ResArray[0]["WTiU"]=str_replace($ti,$tiFa,$ResArray[0]["WTiU"]);
				$ResArray[0]["WTiR"]=str_replace($ti,$tiFa,$ResArray[0]["WTiR"]);
							
				$ResArray[0]["DTiA"]=str_replace($ti,$tiFa,$ResArray[0]["DTiA"]);
				$ResArray[0]["DTiU"]=str_replace($ti,$tiFa,$ResArray[0]["DTiU"]);
				$ResArray[0]["DTiR"]=str_replace($ti,$tiFa,$ResArray[0]["DTiR"]);
				
				$ResArray[0]["ETiA"]=str_replace($ti,$tiFa,$ResArray[0]["ETiA"]);
				$ResArray[0]["ETiU"]=str_replace($ti,$tiFa,$ResArray[0]["ETiU"]);
				$ResArray[0]["ETiR"]=str_replace($ti,$tiFa,$ResArray[0]["ETiR"]);
				
				$IsUnlimitted=true;
				if($ResArray[0]["STrR"]!="UL"){
					$title="ترافیک سرویس:\nمجاز: ".$ResArray[0]["STrA"]."\nاستفاده شده: ".$ResArray[0]["STrU"]."\nباقی مانده: ".$ResArray[0]["STrR"];
					if($ResArray[0]["STrR"]!=0)
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("سرویس")." :")."<span style='font-weight:bold'>".$ResArray[0]["STrR"]."</span></div>";
					else
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("سرویس")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["STrR"]."</span></div>";
					$IsUnlimitted=false;
				}
				if($ResArray[0]["YTrR"]!="UL"){
					$title="ترافیک سالیانه:\nمجاز: ".$ResArray[0]["YTrA"]."\nاستفاده شده: ".$ResArray[0]["YTrU"]."\nباقی مانده: ".$ResArray[0]["YTrR"];
					if($ResArray[0]["YTrR"]!=0)
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("سالیانه")." :")."<span style='font-weight:bold'>".$ResArray[0]["YTrR"]."</span></div>";
					else
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("سالیانه")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["YTrR"]."</span></div>";
					$IsUnlimitted=false;
				}
				if($ResArray[0]["MTrR"]!="UL"){
					$title="ترافیک ماهیانه:\nمجاز: ".$ResArray[0]["MTrA"]."\nاستفاده شده: ".$ResArray[0]["MTrU"]."\nباقی مانده: ".$ResArray[0]["MTrR"];
					if($ResArray[0]["MTrR"]!=0)
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("ماهیانه")." :")."<span style='font-weight:bold'>".$ResArray[0]["MTrR"]."</span></div>";
					else
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("ماهیانه")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["MTrR"]."</span></div>";
					$IsUnlimitted=false;
				}
				if($ResArray[0]["WTrR"]!="UL"){
					$title="ترافیک هفتگی:\nمجاز: ".$ResArray[0]["WTrA"]."\nاستفاده شده: ".$ResArray[0]["WTrU"]."\nباقی مانده: ".$ResArray[0]["WTrR"];
					if($ResArray[0]["WTrR"]!=0)
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("هفتگی")." :")."<span style='font-weight:bold'>".$ResArray[0]["WTrR"]."</span></div>";
					else
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("هفتگی")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["WTrR"]."</span></div>";
					$IsUnlimitted=false;
				}
				if($ResArray[0]["DTrR"]!="UL"){
					$title="ترافیک روزانه:\nمجاز: ".$ResArray[0]["DTrA"]."\nاستفاده شده: ".$ResArray[0]["DTrU"]."\nباقی مانده: ".$ResArray[0]["DTrR"];
					if($ResArray[0]["DTrR"]!=0)
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("روزانه")." :")."<span style='font-weight:bold'>".$ResArray[0]["DTrR"]."</span></div>";
					else
						$OutStr.="<div title='$title'>".CreateLabel("ترافیک باقی مانده ".MakeBold("روزانه")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["DTrR"]."</span></div>";
					$IsUnlimitted=false;
				}
				if($IsUnlimitted){
					$OutStr.="<div>".CreateLabel(" ترافیک سرویس:")."<span style='font-weight:bold'>نامحدود</span></div>";
				}
				
				if($ResArray[0]["ETrA"]!=0){
					$title="ترافیک اضافی:\nمجاز: ".$ResArray[0]["ETrA"]."\nاستفاده شده: ".$ResArray[0]["ETrU"]."\nباقی مانده: ".$ResArray[0]["ETrR"];
					if($ResArray[0]["ETrR"]!=0)
						$OutStr.="<div title='$title' style='margin-top:15px'>".CreateLabel(MakeBold("اضافه")." ترافیک باقی مانده :")."<span style='font-weight:bold'>".$ResArray[0]["ETrR"]."</span></div>";
					else
						$OutStr.="<div title='$title' style='margin-top:15px'>".CreateLabel(MakeBold("اضافه")." ترافیک باقی مانده :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["ETrR"]."</span></div>";
				}
				if($ResArray[0]["LastUpdate"]!="")
					$OutStr.="<div style='font-size:80%;color:darkgray;float:right'>".CreateLabel("آخرین بروزرسانی:")."<span style='font-weight:bold'>".$ResArray[0]["LastUpdate"]." پیش</span></div>";				
				$OutStr.="</fieldset>";
				
				$TimeStr="";
				if($ResArray[0]["STiR"]!="UL"){
					$title="زمان سرویس:\nمجاز: ".$ResArray[0]["STiA"]."\nاستفاده شده: ".$ResArray[0]["STiU"]."\nباقی مانده: ".$ResArray[0]["STiR"];
					if($ResArray[0]["STiR"]!=0)
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("سرویس")." :")."<span style='font-weight:bold'>".$ResArray[0]["STiR"]."</span></div>";
					else
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("سرویس")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["STiR"]."</span></div>";
				}
				if($ResArray[0]["YTiR"]!="UL"){
					$title="زمان سالیانه:\nمجاز: ".$ResArray[0]["YTiA"]."\nاستفاده شده: ".$ResArray[0]["YTiU"]."\nباقی مانده: ".$ResArray[0]["YTiR"];
					if($ResArray[0]["YTiR"]!=0)
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("سالیانه")." :")."<span style='font-weight:bold'>".$ResArray[0]["YTiR"]."</span></div>";
					else
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("سالیانه")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["YTiR"]."</span></div>";
				}
				if($ResArray[0]["MTiR"]!="UL"){
					$title="زمان ماهیانه:\nمجاز: ".$ResArray[0]["MTiA"]."\nاستفاده شده: ".$ResArray[0]["MTiU"]."\nباقی مانده: ".$ResArray[0]["MTiR"];
					if($ResArray[0]["MTiR"]!=0)
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("ماهیانه")." :")."<span style='font-weight:bold'>".$ResArray[0]["MTiR"]."</span></div>";
					else
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("ماهیانه")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["MTiR"]."</span></div>";
				}
				if($ResArray[0]["WTiR"]!="UL"){
					$title="زمان هفتگی:\nمجاز: ".$ResArray[0]["WTiA"]."\nاستفاده شده: ".$ResArray[0]["WTiU"]."\nباقی مانده: ".$ResArray[0]["WTiR"];
					if($ResArray[0]["WTiR"]!=0)
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("هفتگی")." :")."<span style='font-weight:bold'>".$ResArray[0]["WTiR"]."</span></div>";
					else
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("هفتگی")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["WTiR"]."</span></div>";
				}
				if($ResArray[0]["DTiR"]!="UL"){
					$title="زمان روزانه:\nمجاز: ".$ResArray[0]["DTiA"]."\nاستفاده شده: ".$ResArray[0]["DTiU"]."\nباقی مانده: ".$ResArray[0]["DTiR"];
					if($ResArray[0]["DTiR"]!=0)
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("روزانه")." :")."<span style='font-weight:bold'>".$ResArray[0]["DTiR"]."</span></div>";
					else
						$TimeStr.="<div title='$title'>".CreateLabel("زمان باقی مانده ".MakeBold("روزانه")." :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["DTiR"]."</span></div>";
				}
				
				
				if($ResArray[0]["ETiA"]!=0){
					$title="زمان اضافی:\nمجاز: ".$ResArray[0]["ETiA"]."\nاستفاده شده: ".$ResArray[0]["ETiU"]."\nباقی مانده: ".$ResArray[0]["ETiR"];
					if($ResArray[0]["ETiR"]!=0)
						$TimeStr.="<div title='$title' style='margin-top:15px'>".CreateLabel(MakeBold("اضافه")." زمان باقی مانده :")."<span style='font-weight:bold'>".$ResArray[0]["ETiR"]."</span></div>";
					else
						$TimeStr.="<div title='$title' style='margin-top:15px'>".CreateLabel(MakeBold("اضافه")." زمان باقی مانده :")."<span style='color:indianred;font-weight:bold'>".$ResArray[0]["ETiR"]."</span></div>";
				}
				
				
				if($TimeStr!=""){
					$OutStr.="<fieldset class='dhxform_fs'><legend>زمان</legend>$TimeStr";
					if($ResArray[0]["LastUpdate"]!="")
						$OutStr.="<div style='font-size:80%;color:darkgray;float:right'>".CreateLabel("آخرین بروزرسانی:")."<span style='font-weight:bold'>".$ResArray[0]["LastUpdate"]." پیش</span></div>";
					$OutStr.="</fieldset>";
				}
				
				$OutStr.="</div>";
				
				echo "$OutStr";
		break;
	case "GetUserLog":
				DSDebug(1,"DSBriefUser_EditRender-> GetUserLog *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);

				exitifnotpermituser($User_Id,"Visp.User.RadiusLog.List");
				
				$sql="call ListLogUser($User_Id,'$DT')";
				$ResArray=Array();
				$n=CopyTableToArray($ResArray,$sql);
				
				DSDebug(2,DSPrintArray($ResArray));
				
				
				$TableBody="";
				for($i=0;$i<9;++$i){
					if($ResArray[$i]["CDT"]=="")
						break;
					$TableBody.="<tr".(($i%2==0)?(""):(" style='background-color:#DEDEDE'")).">".
						"<td>".$ResArray[$i]["CDT"]."</td>".
						"<td>".$ResArray[$i]["LogType"]."</td>".
						"<td>".$ResArray[$i]["CallingStationId"]."</td>".
						"<td>".$ResArray[$i]["NasName"]."</td>".
						"<td>".$ResArray[$i]["Comment"]."</td>".
					"</tr>";
				}
				$OutStr="<fieldset class='dhxform_fs'><legend>گزارش کاربر</legend>";
				if($TableBody!=""){
					$OutStr.="<table id='UserLog' border='1' cellpadding='3' align='center' cellspacing='0'>";
					$OutStr.="<tr>".
						"<td>CDT</td>".
						"<td>LogType</td>".
						"<td>CallingStationId</td>".
						"<td>NasName</td>".
						"<td>Comment</td>".
					"</tr>";
					$OutStr.=$TableBody;
					$OutStr.="</table>";
				}
				else
					$OutStr.="گزارش خالی است";
				$OutStr.="</fieldset>";
				echo "$OutStr";
		break;
	case "WebUnblock":
				DSDebug(1,"DSBriefUser_EditRender-> WebUnblock *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.WebUnblock");
				
				DBUpdate("Delete From Tonline_web_ipblock Where ClientIP in (Select FramedIpAddress From Tonline_radiususer Where ServiceInfo_Id=1 and User_Id=$User_Id)");
				logdb('Edit','User',$User_Id,'-',"WebUnBlocked");
				echo "OK~";
        break;
    case "RadiusUnblock":
				DSDebug(1,"DSBriefUser_EditRender-> RadiusUnblock *****************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.RadiusUnblock");
				
				DBUpdate("Delete From Tonline_radius_userblock Where Username=(Select Username from Huser Where User_Id=$User_Id)");
				logdb('Edit','User',$User_Id,'-',"RadiusUnBlocked");
				echo "OK~";
        break;			
	default :
		ExitError("درخواست ناشناس");
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
