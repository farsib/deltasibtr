<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSUserEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//exitifnotpermit(0,"Admin.User.Access");

$act=Get_Input('GET','DB','act','ARRAY',array("load",'GetPass',"insert","update","ChangePass","SelectVisp","SelectCenter",'SelectVispByUsername','checkusername','SelectSupporter','ChangeUsername','SelectChangeReseller','ChangeReseller','SelectCenterByUsername','SelectSupporterByUsername','WebUnBlock','RadiusUnBlock','SelectStatus','SetInitialMonthOff','SetMaxPrepaidDebit','CheckSMSprovider','SendSMS','Shahkar'),0,0,0);

switch ($act) {
    case "load":
				DSDebug(1,"DSUserEditRender Load ********************************************");
				
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				ExitIfNotPermitRowAccess('user',$User_Id);
				$Session=DBSelectAsString("Select Count(1) from Tonline_radiususer Where User_Id='$User_Id' And ServiceInfo_Id=1 and AcctUniqueId<>'' and TerminateCause=''");
				$IsFinish=DBSelectAsString("Select Count(1) from Tonline_radiususer Where User_Id='$User_Id' And ServiceInfo_Id=1 and AcctUniqueId<>'' and TerminateCause='' and ISFinishUser='Yes'");
				if($IsFinish>0)
					$Session=-$Session;
				$StaleSession=DBSelectAsString("Select Count(1) from Tonline_radiususer Where User_Id='$User_Id' And ServiceInfo_Id=1 and AcctUniqueId<>'' and TerminateCause<>''");
				DSDebug(0,"Session=$Session	StaleSession=$StaleSession");	
				
				$sql="Select '' As Error,User_Id,{$DT}datetimestr(UserCDT) as UserCDT,UserType,ResellerName,us.UserStatus As UserStatus,StatusName,".
					"'$Session' as Session,'$StaleSession' as StaleSession,".
					"InitialMonthOff,Format(MaxPrepaidDebit,$PriceFloatDigit) As MaxPrepaidDebit,Username,if((select Param1 from Hserver where PartName='Shahkar')<>'No',if(Shahkar_Id='','Not Set','Set'),'') as Shahkar,".
					"u.Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,u.Comment,Organization,".
					"CompanyRegistryCode,{$DT}datestr(CompanyRegistrationDate) as CompanyRegistrationDate,CompanyEconomyCode,CompanyNationalCode,u.Name,u.Family,FatherName,".
					"Nationality,u.Mobile,NationalCode,{$DT}datestr(BirthDate) as BirthDate,u.Phone,".
					"u.Address,{$DT}datestr(ExpirationDate) as ExpirationDate ".
					",PostalCode,CertificateNo,BirthPlace,IdentificationType,Gender,CompanyType,CustomerType,OwnershipType,IdentificationNo".
					" From Huser u ".
					"Left join Hreseller r on (u.Reseller_Id=r.Reseller_Id) ".
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
						if ((in_array($Field,array("Error","User_Id","UserCDT","ResellerName","UserStatus","StatusName","Session","StaleSession","Username","UserType","Shahkar")))||(ISPermit($data["Visp_Id"],"Visp.User.Info.ViewField.".$Field)))
							GenerateLoadField($Field,$Value);
					}
				}
				echo '</data>';
				
       break;
    case "insert":
				DSDebug(1,"DSUserEditRender Insert ******************************************");
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
					ExitError("الگوی نام کاربری ارائه دهنده مجازی زیر مطابقت ندارد</br>$UsernamePattern");
				}
				
				$NewRowInfo['Center_Id']=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
				//-------------------------------------------------------------------------
				$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
				if($ISUsernameOk!=1){
					$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
					ExitError("الگوی نام کاربری مرکز زیر مطابقت ندارد</br>$UsernamePattern");
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
					ExitError("الگوی نام کاربری پشتیبان زیر مطابقت ندارد</br>$UsernamePattern");
				}	
				
				$NewRowInfo['Status_Id']=Get_Input('POST','DB','Status_Id','INT',1,4294967295,0,0);
				
				
				$ISStatusOK=DBSelectAsString("SELECT count(1) from Hstatus s Where Status_Id='".$NewRowInfo['Status_Id']."' and InitialStatus='Yes' and (ResellerAccess='All' or ($LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Status_Id='".$NewRowInfo['Status_Id']."' and Checked='Yes'))) and (VispAccess='All' or ('".$NewRowInfo['Visp_Id']."' in (select Visp_Id from Hstatus_vispaccess where Status_Id='".$NewRowInfo['Status_Id']."' and Checked='Yes')))");
				if($ISStatusOK<=0)
					ExitError("!وضعیت انتخاب شده،وضعیت اولیه ی مجاز نیست");
				//check count of center
				$IsBusyPort=DBSelectAsString("SELECT IsBusyPort from Hstatus Where Status_Id='".$NewRowInfo['Status_Id']."'");
				if($IsBusyPort=='Yes') {
					$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id='".$NewRowInfo['Center_Id']."' and IsBusyPort='Yes'");
					$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id='".$NewRowInfo['Center_Id']."'");
					if($n>=$max)
							ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده است");
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
				
				$Visp_Id=$NewRowInfo['Visp_Id'];
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Pass')){
					$Pass=Get_Input('POST','DB','Pass','STR',0,32,0,0);
					$sql.=",Pass='".$Pass."'";//Use direct variable to avoid displaying password in changelog
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.AdslPhone')){
					$NewRowInfo['AdslPhone']=Get_Input('POST','DB','AdslPhone','STR',0,10,0,0);
					if(($NewRowInfo['AdslPhone']>0)&&(strlen(utf8_decode($NewRowInfo['AdslPhone']))!=10))
						ExitError("تلفن ADSL باید ۱۰ رقم باشد");
					$sql.=",AdslPhone='".$NewRowInfo['AdslPhone']."'";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.OwnershipType')){
					$NewRowInfo['OwnershipType']=Get_Input('POST','DB','OwnershipType','ARRAY',ARRAY('Owner','Renter'),0,0,0);
					$sql.=",OwnershipType='".$NewRowInfo['OwnershipType']."'";
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
				
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.CustomerType')){
					$NewRowInfo['CustomerType']=Get_Input('POST','DB','CustomerType','ARRAY',ARRAY('Person','Company'),0,0,0);
					$sql.=",CustomerType='".$NewRowInfo['CustomerType']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Organization')){
					$NewRowInfo['Organization']=Get_Input('POST','DB','Organization','STR',0,64,0,0);
					$sql.=",Organization='".$NewRowInfo['Organization']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.CompanyRegistryCode')){
					$NewRowInfo['CompanyRegistryCode']=Get_Input('POST','DB','CompanyRegistryCode','STR',0,12,0,0);
					$sql.=",CompanyRegistryCode='".$NewRowInfo['CompanyRegistryCode']."'";
				}

				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.CompanyRegistrationDate')){
					$NewRowInfo['CompanyRegistrationDate']=Get_Input('POST','DB','CompanyRegistrationDate','DateOrBlank',0,0,0,0);
					$sql.=",CompanyRegistrationDate='".$NewRowInfo['CompanyRegistrationDate']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.CompanyEconomyCode')){
					$NewRowInfo['CompanyEconomyCode']=Get_Input('POST','DB','CompanyEconomyCode','STR',0,12,0,0);
					$sql.=",CompanyEconomyCode='".$NewRowInfo['CompanyEconomyCode']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.CompanyNationalCode')){
					$NewRowInfo['CompanyNationalCode']=Get_Input('POST','DB','CompanyNationalCode','STR',0,12,0,0);
					$sql.=",CompanyNationalCode='".$NewRowInfo['CompanyNationalCode']."'";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.CompanyType')){
					$NewRowInfo['CompanyType']=Get_Input('POST','DB','CompanyType','ARRAY',ARRAY('Sahami_Aam','Sahami_Khas','Masouliyat_Mahdoud','Tazamoni','Mokhtalet_Gheyr_Sahami','Mokhtalet_Sahami','Nesbi','TaAavoni','Dolati','Vezaratkhane','Sefaratkhane','Masjed','Madrese','NGO'),0,0,0);
					$sql.=",CompanyType='".$NewRowInfo['CompanyType']."'";
				}				
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Nationality')){
					$NewRowInfo['Nationality']=Get_Input('POST','DB','Nationality','STR',3,3,0,0);
					$sql.=",Nationality='".$NewRowInfo['Nationality']."'";
				}
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Name')){
					$NewRowInfo['Name']=Get_Input('POST','DB','Name','STR',0,32,0,0);
					$sql.=",Name='".$NewRowInfo['Name']."'";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.Gender')){
					$NewRowInfo['Gender']=Get_Input('POST','DB','Gender','ARRAY',ARRAY("Male","Female"),0,0,0);
					$sql.=",Gender='".$NewRowInfo['Gender']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Family')){
					$NewRowInfo['Family']=Get_Input('POST','DB','Family','STR',0,32,0,0);
					$sql.=",Family='".$NewRowInfo['Family']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.FatherName')){
					$NewRowInfo['FatherName']=Get_Input('POST','DB','FatherName','STR',0,32,0,0);
					$sql.=",FatherName='".$NewRowInfo['FatherName']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Mobile')){
					$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,11,0,0);
					$sql.=",Mobile='".$NewRowInfo['Mobile']."'";
				}
				

				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.IdentificationType')){
					$NewRowInfo['IdentificationType']=Get_Input('POST','DB','IdentificationType','ARRAY',ARRAY('NationalCode','Passport','Amayesh','Refugee','Identity'),0,0,0);
					$sql.=",IdentificationType='".$NewRowInfo['IdentificationType']."'";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.NationalCode')){
					$NewRowInfo['NationalCode']=Get_Input('POST','DB','NationalCode','STR',0,10,0,0);
					$sql.=",NationalCode='".$NewRowInfo['NationalCode']."'";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.IdentificationNo')){
					$NewRowInfo['IdentificationNo']=Get_Input('POST','DB','IdentificationNo','STR',0,20,0,0);
					$sql.=",IdentificationNo='".$NewRowInfo['IdentificationNo']."'";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.CertificateNo')){
					$NewRowInfo['CertificateNo']=Get_Input('POST','DB','CertificateNo','STR',0,10,0,0);
					$sql.=",CertificateNo='".$NewRowInfo['CertificateNo']."'";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.UniversalNo')){
					$NewRowInfo['UniversalNo']=Get_Input('POST','DB','UniversalNo','STR',0,20,0,0);
					$sql.=",UniversalNo='".$NewRowInfo['UniversalNo']."'";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.BirthPlace')){
					$NewRowInfo['BirthPlace']=Get_Input('POST','DB','BirthPlace','STR',0,32,0,0);
					$sql.=",BirthPlace='".$NewRowInfo['BirthPlace']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.BirthDate')){
					$NewRowInfo['BirthDate']=Get_Input('POST','DB','BirthDate','DateOrBlank',0,0,0,0);
					$sql.=",BirthDate='".$NewRowInfo['BirthDate']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Phone')){
					$NewRowInfo['Phone']=Get_Input('POST','DB','Phone','STR',0,32,0,0);
					$sql.=",Phone='".$NewRowInfo['Phone']."'";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.AddField.PostalCode')){
					$NewRowInfo['PostalCode']=Get_Input('POST','DB','PostalCode','STR',0,10,0,0);
					$sql.=",PostalCode='".$NewRowInfo['PostalCode']."'";
				}
				
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.ExpirationDate')){
					$NewRowInfo['ExpirationDate']=Get_Input('POST','DB','ExpirationDate','DateOrBlank',0,0,0,0);
					$sql.=",ExpirationDate='".$NewRowInfo['ExpirationDate']."'";
				}
				if(ISPermit($NewRowInfo['Visp_Id'],'Visp.User.Info.AddField.Address')){
					$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,255,0,0);
					$sql.=",Address='".$NewRowInfo['Address']."'";
				}
				
				
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_Id']=$RowId;
				DBInsert("Insert into Huser_status Set Reseller_Id=$LReseller_Id,StatusCDT=Now(),User_Id=$RowId,Status_Id='".$NewRowInfo['Status_Id']."'");
				
				logdbinsert($NewRowInfo,'Add','User',$RowId,'User');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSUserEditRender Update ******************************************");
				$NewRowInfo=array();
				$OldRowInfo= Array();
				$NewRowInfo['User_Id']=Get_Input('POST','DB','User_Id','INT',1,4294967295,0,0);
				// $Username=Get_Input('POST','DB','Username','STR',1,32,0,0);
				
				// ExitIfNotPermitRowAccess('user',$NewRowInfo['User_Id']);
				
				$Username=DBSelectAsString("select Username from Huser where User_Id='".$NewRowInfo['User_Id']."'");
				$Visp_Id=DBSelectAsString("select Visp_Id from Huser where User_Id='".$NewRowInfo['User_Id']."'");
				exitifnotpermit($Visp_Id,"Visp.User.Edit");
				

				$sql= "update Huser set ".
					"Username='$Username'";
				$OldRow_sql="select User_Id,Username";
						
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Visp_Id')){
					$NewRowInfo['Visp_Id']=Get_Input('POST','DB','Visp_Id','INT',1,4294967295,0,0);
					$ISEnableVisp=DBSelectAsString("Select IsEnable from Hvisp where Visp_Id='".$NewRowInfo['Visp_Id']."'");
					if($ISEnableVisp!='Yes'){
						$OldVispId=DBSelectAsString("Select Visp_Id from Huser where User_Id=".$NewRowInfo['User_Id']);
						if($NewRowInfo['Visp_Id']!=$OldVispId)
							ExitError("این ارائه دهنده مجازی اینترنت،فعال نیست");
					}		
					//-------------------------------------------------------------------------
					$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hvisp where Visp_Id='".$NewRowInfo['Visp_Id']."'");
					if($ISUsernameOk!=1){
						$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hvisp where Visp_Id='".$NewRowInfo['Visp_Id']."'");
						ExitError("الگوی نام کاربری ارائه دهنده مجازی زیر مطابقت ندارد</br>$UsernamePattern");
					}
					
					$sql.=",Visp_Id='".$NewRowInfo['Visp_Id']."'";
					$OldRow_sql.=",Visp_Id";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Center_Id')){
					$NewRowInfo['Center_Id']=Get_Input('POST','DB','Center_Id','INT',1,4294967295,0,0);
					//check count of center
					$IsBusyPort=DBSelectAsString("SELECT IsBusyPort from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where User_Id=".$NewRowInfo['User_Id']);
					if($IsBusyPort=='Yes') {
						$OldCenter=DBSelectAsString("SELECT Center_Id from Huser Where User_Id=".$NewRowInfo['User_Id']);
						if($NewRowInfo['Center_Id']!=$OldCenter){
							$n=DBSelectAsString("SELECT Count(*) from Huser u left join Hstatus s on (u.Status_Id=s.Status_Id) Where Center_Id='".$NewRowInfo['Center_Id']."' and IsBusyPort='Yes'");
							$max=DBSelectAsString("SELECT TotalPort-BadPort from Hcenter Where Center_Id='".$NewRowInfo['Center_Id']."'");
							if($n>=$max)
								ExitError("تعداد پورت های تعریف شده مرکز به سقف خود رسیده است");
						}
					}
					//-------------------------------------------------------------------------
					$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
					if($ISUsernameOk!=1){
						$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hcenter where Center_Id='".$NewRowInfo['Center_Id']."'");
						ExitError("الگوی نام کاربری مرکز زیر مطابقت ندارد</br>$UsernamePattern");
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
					
					
					
					
					$sql.=",Center_Id='".$NewRowInfo['Center_Id']."'";
					$OldRow_sql.=",Center_Id";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Supporter_Id')){
					$NewRowInfo['Supporter_Id']=Get_Input('POST','DB','Supporter_Id','INT',1,4294967295,0,0);
					//-------------------------------------------------------------------------
					$ISUsernameOk=DBSelectAsString("Select '$Username' REGEXP UsernamePattern from Hsupporter where Supporter_Id='".$NewRowInfo['Supporter_Id']."'");
					if($ISUsernameOk!=1){
						$UsernamePattern=DBSelectAsString("Select UsernamePattern from Hsupporter where Supporter_Id='".$NewRowInfo['Supporter_Id']."'");
						ExitError("الگوی نام کاربری پشتیبان زیر مطابقت ندارد</br>$UsernamePattern");
					}
					
					$sql.=",Supporter_Id='".$NewRowInfo['Supporter_Id']."'";
					$OldRow_sql.=",Supporter_Id";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.AdslPhone')){
					$NewRowInfo['AdslPhone']=Get_Input('POST','DB','AdslPhone','STR',0,10,0,0);
					if(($NewRowInfo['AdslPhone']>0)&&(strlen(utf8_decode($NewRowInfo['AdslPhone']))!=10))
						ExitError("تلفن ADSL باید ۱۰ رقم باشد");
					$sql.=",AdslPhone='".$NewRowInfo['AdslPhone']."'";
					$OldRow_sql.=",AdslPhone";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.OwnershipType')){
					$NewRowInfo['OwnershipType']=Get_Input('POST','DB','OwnershipType','ARRAY',ARRAY('Owner','Renter'),0,0,0);
					$sql.=",OwnershipType='".$NewRowInfo['OwnershipType']."'";
					$OldRow_sql.=",OwnershipType";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.NOE')){
					$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);
					$sql.=",NOE='".$NewRowInfo['NOE']."'";
					$OldRow_sql.=",NOE";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.IdentInfo')){
					$NewRowInfo['IdentInfo']=Get_Input('POST','DB','IdentInfo','STR',0,32,0,0);
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
				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CustomerType')){
					$NewRowInfo['CustomerType']=Get_Input('POST','DB','CustomerType','ARRAY',ARRAY('Person','Company'),0,0,0);
					$sql.=",CustomerType='".$NewRowInfo['CustomerType']."'";
					$OldRow_sql.=",CustomerType";
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
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CompanyRegistrationDate')){
					$NewRowInfo['CompanyRegistrationDate']=Get_Input('POST','DB','CompanyRegistrationDate','DateOrBlank',0,0,0,0);
					$sql.=",CompanyRegistrationDate='".$NewRowInfo['CompanyRegistrationDate']."'";
					$OldRow_sql.=",CompanyRegistrationDate";
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
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CompanyType')){
					$NewRowInfo['CompanyType']=Get_Input('POST','DB','CompanyType','ARRAY',ARRAY('Sahami_Aam','Sahami_Khas','Masouliyat_Mahdoud','Tazamoni','Mokhtalet_Gheyr_Sahami','Mokhtalet_Sahami','Nesbi','TaAavoni','Dolati','Vezaratkhane','Sefaratkhane','Masjed','Madrese','NGO'),0,0,0);
					$sql.=",CompanyType='".$NewRowInfo['CompanyType']."'";
					$OldRow_sql.=",CompanyType";
				}		
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Nationality')){
					$NewRowInfo['Nationality']=Get_Input('POST','DB','Nationality','STR',3,3,0,0);
					$sql.=",Nationality='".$NewRowInfo['Nationality']."'";
					$OldRow_sql.=",Nationality";
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
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Gender')){
					$NewRowInfo['Gender']=Get_Input('POST','DB','Gender','ARRAY',ARRAY("Male","Female"),0,0,0);
					$sql.=",Gender='".$NewRowInfo['Gender']."'";
					$OldRow_sql.=",Gender";
				}				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.FatherName')){
					$NewRowInfo['FatherName']=Get_Input('POST','DB','FatherName','STR',0,32,0,0);
					$sql.=",FatherName='".$NewRowInfo['FatherName']."'";
					$OldRow_sql.=",FatherName";
				}	
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Mobile')){
					$NewRowInfo['Mobile']=Get_Input('POST','DB','Mobile','STR',0,11,0,0);
					$sql.=",Mobile='".$NewRowInfo['Mobile']."'";
					$OldRow_sql.=",Mobile";
				}
				
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.IdentificationType')){
					$NewRowInfo['IdentificationType']=Get_Input('POST','DB','IdentificationType','ARRAY',ARRAY('NationalCode','Passport','Amayesh','Refugee','Identity'),0,0,0);
					$sql.=",IdentificationType='".$NewRowInfo['IdentificationType']."'";
					$OldRow_sql.=",IdentificationType";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.NationalCode')){
					$NewRowInfo['NationalCode']=Get_Input('POST','DB','NationalCode','STR',0,10,0,0);
					$sql.=",NationalCode='".$NewRowInfo['NationalCode']."'";
					$OldRow_sql.=",NationalCode";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.IdentificationNo')){
					$NewRowInfo['IdentificationNo']=Get_Input('POST','DB','IdentificationNo','STR',0,20,0,0);
					$sql.=",IdentificationNo='".$NewRowInfo['IdentificationNo']."'";
					$OldRow_sql.=",IdentificationNo";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.CertificateNo')){
					$NewRowInfo['CertificateNo']=Get_Input('POST','DB','CertificateNo','STR',0,10,0,0);
					$sql.=",CertificateNo='".$NewRowInfo['CertificateNo']."'";
					$OldRow_sql.=",CertificateNo";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.UniversalNo')){
					$NewRowInfo['UniversalNo']=Get_Input('POST','DB','UniversalNo','STR',0,20,0,0);
					$sql.=",UniversalNo='".$NewRowInfo['UniversalNo']."'";
					$OldRow_sql.=",UniversalNo";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.BirthPlace')){
					$NewRowInfo['BirthPlace']=Get_Input('POST','DB','BirthPlace','STR',0,32,0,0);
					$sql.=",BirthPlace='".$NewRowInfo['BirthPlace']."'";
					$OldRow_sql.=",BirthPlace";
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
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.PostalCode')){
					$NewRowInfo['PostalCode']=Get_Input('POST','DB','PostalCode','STR',0,10,0,0);
					$sql.=",PostalCode='".$NewRowInfo['PostalCode']."'";
					$OldRow_sql.=",PostalCode";
				}
			
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.ExpirationDate')){
					$NewRowInfo['ExpirationDate']=Get_Input('POST','DB','ExpirationDate','DateOrBlank',0,0,0,0);
					$sql.=",ExpirationDate='".$NewRowInfo['ExpirationDate']."'";
					$OldRow_sql.=",ExpirationDate";
				}
				if (ISPermit($Visp_Id,'Visp.User.Info.EditField.Address')){
					$NewRowInfo['Address']=Get_Input('POST','DB','Address','STR',0,255,0,0);
					$sql.=",Address='".$NewRowInfo['Address']."'";
					$OldRow_sql.=",Address";
				}

				
				//PostalCode,CertificateNo,BirthPlace,IdentificationType,Gender,CompanyType,CustomerType,OwnershipType,IdentificationNo
				
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
    case "ChangePass": 
				DSDebug(1,"DSUserEditRender ChangePass ******************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				exitifnotpermituser($User_Id,"Visp.User.ChangePassword");
				$Pass=Get_Input('POST','DB','Pass','STR',1,32,0,0);//Get_POST($mysqli,"enpass");
				$sql =" Update Huser set ";
				$sql.=" Pass='$Pass'";
				$sql.=" Where ";
				$sql.=" User_Id=$User_Id";
				$res = $conn->sql->query($sql);
				logdb('Edit','User',$User_Id,'User',"Password Changed");
				echo "OK~";
		
        break;
    case "SetInitialMonthOff": 
				DSDebug(1,"DSUserEditRender SetInitialMonthOff ******************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				exitifnotpermituser($User_Id,"Visp.User.SetInitialMonthOff");
				$InitialMonthOff=Get_Input('POST','DB','InitialMonthOff','INT',0,999,0,0);//Get_POST($mysqli,"enpass");
				$sql =" Update Huser set ";
				$sql.=" InitialMonthOff='$InitialMonthOff'";
				$sql.=" Where ";
				$sql.=" User_Id=$User_Id";
				$res = $conn->sql->query($sql);
				logdb('Edit','User',$User_Id,'User',"InitialMonthOff Changed to $InitialMonthOff");
				echo "OK~";
		
        break;
    case "SetMaxPrepaidDebit": 
				DSDebug(1,"DSUserEditRender SetMaxPrepaidDebit ******************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.SetMaxPrepaidDebit");
				$MaxPrepaidDebit=Get_Input('POST','DB','MaxPrepaidDebit','PRC',1,14,0,0);
				$sql =" Update Huser set ";
				$sql.=" MaxPrepaidDebit='$MaxPrepaidDebit'";
				$sql.=" Where ";
				$sql.=" User_Id=$User_Id";
				$res = $conn->sql->query($sql);
				logdb('Edit','User',$User_Id,'User',"MaxPrepaidDebit Changed to $MaxPrepaidDebit");
				echo "OK~";
		
        break;
    case "GetPass": 
				DSDebug(1,"DSUserEditRender GetPass ******************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				//exitifnotpermituser($User_Id,"Visp.User.GetPassword");
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				if (ISPermit($Visp_Id,"Visp.User.GetPassword")) 
					$Pass=DBSelectAsString("SELECT Pass from Huser Where User_Id=$User_Id");
				else
					$Pass='';

				echo "OK`$Pass`";
		
        break;
	case "ChangeUsername": 
				DSDebug(1,"DSUserEditRender ChangeUsername ******************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				exitifnotpermituser($User_Id,"Visp.User.ChangeUsername");
				$Username=Get_Input('POST','DB','Username','STR',1,32,0,0);
				$OldUsername=DBSelectAsString("Select Username From Huser where User_Id='$User_Id'");
				$IsDuplicate=DBSelectAsString("Select Count(*) From Huser where Username='$Username'");
				if($IsDuplicate>0){
					echo 'Username Duplicate';
					exit;
				}	

	
				$sql =" Update Huser set ";
				$sql.=" Username='$Username'";
				$CurrentVisp_Id=DBSelectAsString("Select Visp_Id From Huser Where User_id=$User_Id");
				$IfCurrentVispMatched=DBSelectAsString("SELECT Count(*) FROM Hvisp Where Visp_Id=$CurrentVisp_Id And '$Username' REGEXP UsernamePattern=1 ");
				if($IfCurrentVispMatched==0){//check if found another vidp matche username
					$Visp_Id=DBSelectAsString("SELECT Visp_Id FROM Hvisp Where '$Username' REGEXP UsernamePattern=1 order by VispName asc Limit 1");
					if($Visp_Id==0){echo 'None of Visp matched UsernamePattern';exit;}
					else $sql.=",Visp_Id=$Visp_Id";
				}

				$CurrentCenter_Id=DBSelectAsString("Select Center_Id From Huser Where User_id=$User_Id");
				$IfCurrentCenterMatched=DBSelectAsString("SELECT Count(*) FROM Hcenter Where Center_Id=$CurrentCenter_Id And '$Username' REGEXP UsernamePattern=1 ");
				if($IfCurrentCenterMatched==0){//check if found another vid matche username
					$Center_Id=DBSelectAsString("SELECT Center_Id FROM Hcenter Where '$Username' REGEXP UsernamePattern=1 order by CenterName asc Limit 1");
					if($Center_Id==0){echo 'None of Center matched UsernamePattern';exit;}
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
				}
				
				$CurrentSupporter_Id=DBSelectAsString("Select Supporter_Id From Huser Where User_id=$User_Id");
				$IfCurrentSupporterMatched=DBSelectAsString("SELECT Count(*) FROM Hsupporter Where Supporter_Id=$CurrentSupporter_Id And '$Username' REGEXP UsernamePattern=1 ");
				if($IfCurrentSupporterMatched==0){//check if found another vidp matche username
					$Supporter_Id=DBSelectAsString("SELECT Supporter_Id FROM Hsupporter Where '$Username' REGEXP UsernamePattern=1 order by SupporterName asc Limit 1");
					if($Supporter_Id==0){echo 'None of Supporter matched UsernamePattern';exit;}
					else $sql.=",Supporter_Id=$Supporter_Id";
				}
				$sql.=" Where ";
				$sql.=" User_Id=$User_Id";
				$res = $conn->sql->query($sql);
				DBDelete("Delete From Tuser_log Where Username='$Username' or User_Id=$User_Id");
				logdb('Edit','User',$User_Id,'User',"User Changed From [$OldUsername] to [$Username]");
				echo "OK~";
		
        break;
    case "SelectVisp":
				DSDebug(1,"DSUserEditRender SelectVisp *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','id','INT',0,4294967295,0,0);//0 means create new row  
				
				$PermitItem_Id=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.View'");
				$CurrentVisp_Id=DBSelectAsString("Select Visp_Id From Huser where User_Id=$User_Id");
				$Username=DBSelectAsString("Select Username from Huser Where User_Id=$User_Id");
				/*	
				if($LReseller_Id==1)
					$sql="SELECT v.Visp_Id,VispName FROM Hvisp v ".
									"where (ISEnable='Yes') order by VispName ASC";
									
				else
				*/
					$sql="SELECT v.Visp_Id,VispName FROM Hvisp v ".
									"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
									"where ((v.Visp_Id='$CurrentVisp_Id') Or '$Username' REGEXP UsernamePattern=1) ".
									"And((v.IsEnable='Yes')And(ISPermit='Yes')) order by VispName ASC";
				$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectVispByUsername":
				DSDebug(1,"DSUserEditRender SelectVisp *****************");
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
				DSDebug(1,"DSUserEditRender SelectCenterByUsername *****************");
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
				DSDebug(1,"DSUserEditRender SelectStatus *****************");
				$Visp_Id=Get_Input('GET','DB','Visp_Id','INT',1,4294967295,0,0);
				
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				// $Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
				$sql="SELECT Status_Id,StatusName from Hstatus s Where InitialStatus='Yes' and (ResellerAccess='All' or ($LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Status_Id=s.Status_Id and Checked='Yes'))) and (VispAccess='All' or ($Visp_Id in (select Visp_Id from Hstatus_vispaccess where Status_Id=s.Status_Id and Checked='Yes')))";
				$options->render_sql($sql,"","Status_Id,StatusName","","");
        break;
    case "SelectSupporterByUsername":
				DSDebug(1,"DSUserEditRender SelectSupporterByUsername *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
			
				$sql="SELECT Supporter_Id,SupporterName FROM Hsupporter Where IsEnable='Yes' and '$Username' REGEXP UsernamePattern=1 order by SupporterName asc";
				$options->render_sql($sql,"","Supporter_Id,SupporterName","","");
        break;
    case "checkusername":
				DSDebug(1,"DSUserEditRender checkusername *****************");
				$Username=Get_Input('GET','DB','Username','STR',1,32,0,0);
	
				$PermitItem_Id=DBSelectAsString("Select PermitItem_Id from Hpermititem where PermitItemName='Visp.User.Add'");
				$IsPermitVisp=DBSelectAsString("SELECT Count(*) FROM Hvisp v ".
						"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
						"where (v.IsEnable='Yes')And(ISPermit='Yes') ");
				if($IsPermitVisp==0)
					ExitError('شما مجاز به افزودن کاربر در هیچ ارائه دهنده مجازی ای نیستید');
				
				$NumPatternMatched=DBSelectAsString("Select Count(*) From Hvisp v ".
						"left join Hreseller_permit rgp on (v.Visp_id=rgp.Visp_Id)and(rgp.Reseller_Id=$LReseller_Id)and(rgp.PermitItem_Id='$PermitItem_Id') ".
						"where (v.IsEnable='Yes')And(ISPermit='Yes')and('$Username' REGEXP UsernamePattern=1)");
				if($NumPatternMatched==0)
					ExitError('هیچ ارائه دهنده مجازی ای با الگوی نام کاربری مطابقت ندارد');
				
				
				$NumPatternMatched=DBSelectAsString("Select Count(*) From Hcenter where '$Username' REGEXP UsernamePattern=1");
				if($NumPatternMatched==0)
					ExitError('هیچ مرکزی با الگوی نام کاربری مطابقت ندارد');
				
				$NumPatternMatched=DBSelectAsString("Select Count(*) From Hsupporter where IsEnable='Yes' and '$Username' REGEXP UsernamePattern=1");
				if($NumPatternMatched==0)
					ExitError('هیچ پشتیبانی با الگوی نام کاربری مطابقت ندارد');
					
				$NumPatternMatched=DBSelectAsString("Select Count(*) from Hstatus s Where InitialStatus='Yes' and (ResellerAccess='All' or ($LReseller_Id in (select Reseller_Id from Hstatus_reselleraccess where Status_Id=s.Status_Id and Checked='Yes')))");
				if($NumPatternMatched==0)
					ExitError('وضعیت اولیه مجازی یافت نشد');
				
				$IsDuplicate=DBSelectAsString("Select Count(*) From Huser where Username='$Username'");
				if($IsDuplicate>0)
					ExitError('نام کاربری وجود دارد');
					
				echo 'OK~';
        break;
    case "SelectCenter":
				DSDebug(1,"DSUserEditRender SelectCenter *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','id','INT',0,4294967295,0,0);//0 means create new row  
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
//				$sql="SELECT Center_Id,CenterName FROM Hcenter Where Center_Id='$CurrentCenter_Id' Or '$Username' REGEXP UsernamePattern=1 order by CenterName asc";
				$options->render_sql($sql,"","Center_Id,CenterName","","");
        break;
    case "SelectSupporter":
				DSDebug(1,"DSUserEditRender SelectSupporter *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','id','INT',0,4294967295,0,0);//0 means create new row 
				$Username=DBSelectAsString("Select Username From Huser where User_Id=$User_Id");
				$CurrentSupporter_Id=DBSelectAsString("Select Supporter_Id From Huser where User_Id=$User_Id");
				$sql="SELECT Supporter_Id,SupporterName FROM Hsupporter Where Supporter_Id='$CurrentSupporter_Id' Or '$Username' REGEXP UsernamePattern=1 order by SupporterName asc";
				$options->render_sql($sql,"","Supporter_Id,SupporterName","","");
        break;
    case "SelectChangeReseller":
				DSDebug(1,"DSSupporterEditRender SelectChangeReseller *****************");
				//exitifnotpermit(0,"Admin.Supporter.View");
				$User_Id=Get_Input('GET','DB','id','INT',0,4294967295,0,0);//0 means create new row 
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$UserReseller_Id=DBSelectAsString("Select Reseller_Id From Huser where User_Id=$User_Id");
				$sql="SELECT Reseller_Id,ResellerName ".
					"From Hreseller r Where (Reseller_Id<>$UserReseller_Id) and(ISOperator='No')and(ISEnable='Yes') order by ResellerName Asc";
				$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
        break;
    case "ChangeReseller": 
				DSDebug(1,"DSUserEditRender ChangeReseller ******************************************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				exitifnotpermituser($User_Id,"Visp.User.ChangeReseller");
				$Reseller_Id=Get_Input('POST','DB','Reseller_Id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				
				$NewResellerName=DBSelectAsString("Select ResellerName from Hreseller where Reseller_Id='$Reseller_Id' and ISOperator='No' and IsEnable='Yes'");
				if($NewResellerName=='')
					ExitError("نماینده فروش نامعتبر انتخاب شده است");
				$OldResellerName=DBSelectAsString("Select ResellerName from Hreseller r join Huser u on u.User_Id=$User_Id and r.Reseller_Id=u.Reseller_Id");
				
				if($NewResellerName==$OldResellerName)
					ExitError("نام جدید نماینده فروش و نام قدیم آن،یکسان است");
				$sql =" Update Huser set ";
				$sql.=" Reseller_Id='$Reseller_Id'";
				$sql.=" Where ";
				$sql.=" User_Id=$User_Id ";
				$res = $conn->sql->query($sql);
				//DBUpdate("Select UpdateAllUserParam($User_Id)");
				logdb('Edit','User',$User_Id,'Reseller',"Reseller Changed from $OldResellerName to $NewResellerName");
				echo "OK~";
		
        break;
    case "WebUnBlock":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> WebUnBlock *****************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				// ExitIfNotPermitRowAccess('user',$User_Id);
				exitifnotpermituser($User_Id,"Visp.User.WebUnblock");
				
				DBUpdate("Delete From Tonline_web_ipblock Where ClientIP in (Select FramedIpAddress From Tonline_radiususer Where User_Id=$User_Id)");
				logdb('Edit','User',$User_Id,'-',"WebUnBlocked");
				echo "OK~";
        break;
    case "RadiusUnBlock":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> RadiusUnBlock *****************");
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				// ExitIfNotPermitRowAccess('user',$User_Id);
				exitifnotpermituser($User_Id,"Visp.User.RadiusUnblock");
				
				DBUpdate("Delete From Tonline_radius_userblock Where Username=(Select Username from Huser Where User_Id=$User_Id)");
				logdb('Edit','User',$User_Id,'-',"RadiusUnBlocked");
				echo "OK~";
        break;
	case "CheckSMSprovider":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> CheckSMSprovider ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				ExitIfNotPermitRowAccess('user',$User_Id);
				$sql="SELECT SMSProvider_Id From Huser_notifystate where User_Id='$User_Id'";
				if(DBSelectAsString($sql)<1)
					echo "NoProvider~";
				else
					echo "OK~";
		break;
	case "SendSMS":
				DSDebug(1,"DSUser_ServiceBase_ListRender-> SendSMS ********************************************");
				$NewRowInfo=array();
				$NewRowInfo['User_Id']=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				$NewRowInfo['SMSType']=Get_Input('POST','DB','SMSType','ARRAY',array('InfoSMS','CustomSMS'),0,0,0);
				
				exitifnotpermituser($NewRowInfo['User_Id'],"Visp.User.Send".$NewRowInfo['SMSType']);
				
				$NewRowInfo['Mobile']=DBSelectAsString("Select Mobile from Huser where User_Id='".$NewRowInfo['User_Id']."'");
				if($NewRowInfo['Mobile']==''){
					DSDebug(0,"Mobile number is empty. Can not send sms to an empty mobile");
					ExitError("نمیتوان به شماره همراه خالی پیامک ارسال کرد");
				}
				
				$sql="SELECT SMSProvider_Id From Huser_notifystate where User_Id='".$NewRowInfo['User_Id']."'";
				if(DBSelectAsString($sql)<1){
					DSDebug(0,"User has no SMS Procid");
					ExitError("برای کاربر ارائه دهنده پیام کوتاه تعریف نشده");
				}
				
				if($NewRowInfo['SMSType']=="InfoSMS"){
					$InfoSMSFields=Get_Input("POST","DB","InfoSMSFields","STR",1,200,0,0);
					$InfoSMSFieldsArr=explode(",",$InfoSMSFields);

					$str="";
					for($i=0;$i<count($InfoSMSFieldsArr);++$i){
						if($InfoSMSFieldsArr[$i]=="Username")
							$str.=",'\\n','نام کاربری:',Hu.Username";
						elseif($InfoSMSFieldsArr[$i]=="Pass")
							$str.=",'\\n','رمز:',Hu.Pass";
						elseif($InfoSMSFieldsArr[$i]=="SHDate")
							$str.=",'\\n','تاریخ:',shdatestr(Date(Now()))";
						elseif($InfoSMSFieldsArr[$i]=="Time")
							$str.=",'\\n','ساعت:',Time(Now())";
						elseif($InfoSMSFieldsArr[$i]=="RTrM")
							$str.=",'\\n','ترافیک:',if(DSSessionTraffic(0,1,0,STrA,STrU,Tu_u.ETrA,ETrU,YTrA,YTrU,MTrA,MTrU,WTrA,WTrU,DTrA,DTrU)<999999999999999999,
													ByteToR(DSSessionTraffic(0,1,0,STrA,STrU,Tu_u.ETrA,ETrU,YTrA,YTrU,MTrA,MTrU,WTrA,WTrU,DTrA,DTrU)-LastTrU),'Unlimited')";
						elseif($InfoSMSFieldsArr[$i]=="GiftTraffic")
							$str.=",'\\n','هدیه ترافیکی:',ByteToR(Tu_u.GiftExtraTr)";
						elseif($InfoSMSFieldsArr[$i]=="RTiH")
							$str.=",'\\n','اعتبار زمانی:',if(DSSessionTime(0,'3000-01-01',0,1,0,0,0,STiA,STiU,Tu_u.ETiA,ETiU,YTiA,YTiU,MTiA,MTiU,WTiA,WTiU,DTiA,DTiU)<300000000,SecondToR(DSSessionTime(0,'3000-01-01',0,1,0,0,0,STiA,STiU,Tu_u.ETiA,ETiU,YTiA,YTiU,MTiA,MTiU,WTiA,WTiU,DTiA,DTiU)),'Unlimited')";
						elseif($InfoSMSFieldsArr[$i]=="GiftTime")
							$str.=",'\\n','هدیه زمانی:',SecondToR(Tu_u.GiftExtraTi)";
						elseif($InfoSMSFieldsArr[$i]=="ShExpireDate"){
							$str.=",'\\n','اعتبار سرویس:',if(Hu.EndDate<=Date(Now()),'منقضی',shdatestr(Hu.EndDate))";
						}
						elseif($InfoSMSFieldsArr[$i]=="UserDebit")
							$str.=",'\\n',if(Hu.PayBalance>0,concat(format(Hu.PayBalance,0),' بستانکار'),if(Hu.PayBalance<0,concat(format(-Hu.PayBalance,0),' بدهکار'),'تسویه'))";
						elseif($InfoSMSFieldsArr[$i]=="CompanyName")
							$str.=",'\\n',(Select Param2 From Hserver Where PartName='Param')";
						elseif($InfoSMSFieldsArr[$i]=="SupportPhone")
							$str.=",'\\n','تلفن:',(Select Param4 From Hserver Where PartName='Param')";
						else{
							DSDebug(1,"Checking array_key_exists(".$InfoSMSFieldsArr[$i].",$TheFields)=False");
							ExitError("فیلد زیر نامعتبر است</br>'".$InfoSMSFieldsArr[$i]."'");
						}
					}
					$str="concat(".substr($str,6).")";//to remove first unwanted newline
					DSDebug(1,"Parsed Message:`$str`");
					$NewRowInfo['Message']=DBSelectAsString("select $str from Huser Hu join Tuser_usage Tu_u on Hu.User_Id=Tu_u.User_Id where Hu.User_Id='".$NewRowInfo['User_Id']."'");
					$NewRowInfo['Message']=mysqli_real_escape_string($mysqli,$NewRowInfo['Message']);
					$NewRowInfo['Mobile']=mysqli_real_escape_string($mysqli,$NewRowInfo['Mobile']);
					DSDebug(1,"SMSMessage=`".$NewRowInfo['Message']."`");
				}
				else{
					$NewRowInfo['Message']=Get_Input("POST","DB","CustomSMSMessage","STR",1,201,0,0);
					$NewRowInfo['Mobile']=mysqli_real_escape_string($mysqli,$NewRowInfo['Mobile']);
				}
				
				if(strlen($NewRowInfo['Message'])<=0){
					ExitError("متن پیام کوتاه خالی تشخیص داده شد");
					DSDebug(0,"Empty SMS message detected!");
				}
				
				$sql="Insert Into Huser_smshistory Set ".
				"Creator='Reseller',".
				"CreateDT=Now(),".
				"Status='Schedule',".
				"ExpireDT=Now()+ INTERVAL 3600 Second,".
				"Message='".$NewRowInfo['Message']."',".
				"Mobile='".$NewRowInfo['Mobile']."',".
				"User_Id='".$NewRowInfo['User_Id']."'";
				$res=DBUpdate($sql);
				logdbinsert($NewRowInfo,'Add','User',$NewRowInfo['User_Id'],'SMS');
				
				
				echo "OK~";
				
		break;
	case "Shahkar":
								
				$User_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);//Get_GET($mysqli,"id");
				require_once("../../lib/DSShahkarLib.php");
				$Service_Id=DBSelectAsString("Select Service_Id from Huser where User_Id='$User_Id'");
				$Result=SendShahkarPUT($User_Id,$Service_Id);
				DSDebug(0,print_r($Result,true));
				if($Result["Error"]!='')
					ExitError($Result["Error"]);
				else
					echo str_replace(array("\n","\t"," "),array("<br/>"," "," "),print_r($Result["Response"],true));
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
