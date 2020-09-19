<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSUserEdit ....................................................................................");
	if($LastError!=""){
		DSDebug(0,"Session Expire");
		?>
		<html><head><script type="text/javascript">
			window.onload = function(){
			parent.dhxLayout.dhxWins.window("popupWindow").hide();
			parent.dhtmlx.alert("<?php echo escape($LastError) ?>");//"Session Expire, Please Relogin"
			parent.dhxLayout.dhxWins.window("popupWindow").close();
		}
		</script></head><body></body></html>
		<?php
			exit();
		}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; char=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
	<script src="../codebase/dhtmlxform_dyn.js"></script>
    <script src="../js/skin.js" type="text/javascript"></script>
    <script src="../js/dsfunFa.js" type="text/javascript"></script>
    <style>
        html, body {
			width: 100%;
			height: 100%;
			margin: 0px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background-color:white;
        }
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission='';
var LoginResellerName=parent.LoginResellerName;
var Username;
window.onload = function(){

	var RowId =	"<?php 
		$Row_Id=addslashes($_GET['RowId']);
		$details=explode(',',$Row_Id,2);
		$FieldName=$details[0];
		$FieldValue=$details[1];
		if($FieldName=='User_Id')
			if($FieldValue==0)
				$User_Id=0;
			else
				$User_Id=DBSelectAsString('Select User_Id From Tuser_authhelper Where User_Id="'.$FieldValue.'"');
		else
			$User_Id=DBSelectAsString('Select User_Id From Tuser_authhelper Where Username="'.$FieldValue.'"');
		echo $User_Id;
	?>";
	if(RowId == "" ) {alert('Id not detected');parent.dhxLayout.dhxWins.window("popupWindow").close();return;}
	
	document.onkeydown = function(e){
		e = e || window.event;
		var x = e.which || e.keyCode;
		if(x==27){
			dhtmlx.confirm({
				title: "هشدار",
				type:"confirm-warning",
				text: "تمامی داده های ذخیره نشده، از بین خواهد رفت.<br/>برای بستن مطمئن هستید؟",
				ok:"بلی",
				cancel:"خیر",
				callback: function(Ans) {if(Ans) parent.dhxLayout.dhxWins.window("popupWindow").close();}
			});
		}
	};	
	
	
	var DataTitle="کاربر";
	var DataName="DSUser_";
	var ChangeLogDataName='User';
	// var TabbarMain,TopToolbar;
	
	//=======TabbarMain
	var TabbarMainArray=[
					["مشخصات","DSUser_Edit",70,"Visp.User.Info.View",""],
					["رخداد ردیوس","DSUser_RadiusLog_List",73,"Visp.User.RadiusLog.List",""],
					["اعتبار کاربر","DSUser_CreditInfoEdit",80,"Visp.User.CreditStatus.List",""],
					["وضعیت","DSUser_Status_List",55,"Visp.User.Status.List",""],
					["دسته","DSUser_Class_List",40,"Visp.User.Class.List",""],
					["مک/آی پی","DSUser_CallerId_List",65,"Visp.User.CallerId.List",""],
					["پایه","DSUser_ServiceBase_List",50,"Visp.User.Service.Base.List",""],
					["اعتبار اضافی","DSUser_ServiceExtraCredit_List",85,"Visp.User.Service.ExtraCredit.List",""],
					["آی پی","DSUser_ServiceIP_List",45,"Visp.User.Service.IP.List",""],
					["سایر","DSUser_ServiceOther_List",55,"Visp.User.Service.Other.List",""],
					["هدیه","DSUser_Gift_List",45,"Visp.User.Gift.List",""],
					["اقساط","DSUser_Installment_List",60,"Visp.User.Installment.List",""],
					["پرداخت","DSUser_Payment_List",70,"Visp.User.Payment.List",""],
					["پیوست","DSUser_Attachment_List",85,"Visp.User.Attachment.List",""],
					["پارامتر","DSParam_List",55,"Visp.User.Param.List","ParamItemGroup=User"],
					["اتصال","DSUser_Connection_List",80,"Visp.User.Connection.List",""],
					["یادداشت","DSUser_Note_List",70,"Visp.User.Note.List",""],
					["تغییرات","DSChangeLog",90,"Visp.User.ChangeLog.List","ChangeLogDataName=User"],
					["نشست","DSUser_Session_List",65,"Visp.User.Session.List",""],
					["لیست","DSUser_UrlList_List",70,"Visp.User.URL.UrlList.List",""],
					["پر بازدید","DSUser_TopSite_List",70,"Visp.User.URL.TopSite.List",""],
					["مصرف امروز","DSUser_DailyUsage_List",75,"Visp.User.DailyUsage.List",""],
					["اعلان","DSUser_Notify_List",50,"Visp.User.Notify.List",""],
					["تاریخچه پشتیبانی","DSUser_SupportHistory_List",120,"Visp.User.SupportHistory.List",""],
					["پرداخت آنلاین","DSUser_PayOnline_List",80,"Visp.User.PayOnline.List",""],
					["پس انداز","DSUser_SavingOff_List",70,"Visp.User.SavingOff.List",""],
					["تاریخچه پنل","DSUser_WebHistory_List",80,"Visp.User.WebHistory.List",""],
					["فاکتور","DSUser_Invoice_List",65,"Visp.User.Invoice.List",""],
					["پیام پنل کاربری","DSUser_WebMessage_List",90,"Visp.User.WebMessage.List",""],
					["لیست","DSUser_NetlogList_List",70,"Visp.User.Netlog.NetlogList.List",""],
				];
	var IsSupportTabNotLoaded=true,IsServiceTabNotLoaded=true,IsURLTabNotLoaded=true;IsNetlogTabNotLoaded=true;
	//=======Form0_1
	var Form0_1;
	var Form0_1PopupHelp;
	var Form0_1FieldHelp  ={Username:'کاراکتر انگلیسی حداکثر ۳۲ کاراکتر'};
	var Form0_1FieldHelpId=["Username"];
	var Form0_1Str = [
					{ type:"block",list:[
						{ type:"settings",labelWidth:124,offsetLeft:10, labelAlign:"left"},
						{ type: "label"},
						{ type: "label"},
						{ type: "input" , name:"Username", label:"نام کاربری :", value: "" , validate:"NotEmpty,IsValidUserName", maxLength:32,inputWidth:200, required:true,info:true},
						{ type: "select", name:"Visp_Id",label: "ارائه دهنده مجازی :",required:true,inputWidth:198,disabled:true},
						{ type: "select", name:"Center_Id",label: "مرکز :",required:true,inputWidth:198,disabled:true},
						{ type: "select", name:"Supporter_Id",label: "پشتیبان :",required:true,inputWidth:198,disabled:true},
						{ type: "select", name:"Status_Id",label: "وضعیت :",required:true,inputWidth:198,disabled:true},
					]},
						{ type: "label"},
						{type: "block", width: 250, list:[
						{ type: "button",name:"Next",value: "بررسی نام کاربری",width :120},
						{type: "newcolumn", offset:20},
						{ type: "button",name:"Close",value: " بستن ",width :80}
						]}
					];
					
	//=======Form0_2
	var Form0_2;
	var Form0_2PopupHelp;
	var Form0_2FieldHelp  ={Username:'کاراکتر انگلیسی حداکثر ۳۲ کاراکتر'};
	var Form0_2FieldHelpId=["Username","Visp_Id"];
	var Form0_2Str;
	//=======Form0_3
	var Form0_3;
	var Form0_3PopupHelp;
	var Form0_3FieldHelp  ={	Username:'کاراکتر انگلیسی حداکثر ۳۲ کاراکتر',
							AdslPhone:'و حداکثر ۱۰ کاراکتر ADSL شماره تلفن کاربر',
							NOE :'برای گزارش مورد نیاز است',
							BirthDate:'خالی بگذارید و یا با فرمت روز/ماه/سال وارد نمایید',
							Phone:'شماره تلفن ۱۱ رقمی- مثال:05632233041',
							MaxPrepaidDebit:'مبلغی که نماینده فروش می تواند با وجود بدهی،برای کاربر سرویس پیش پرداخت ثبت نماید',
							ExpirationDate:'تاریخ انقضا کاربری.بعد از این تاریخ کاربر نمی تواند متصل شود<br/>در صورتی که بخواهید کاربری منقضی نشود،خالی بگذارید',
							Mobile:'شماره همراه ۱۰ رقمی که با ۰ شروع می شود',
							NationalCode:'کد ملی کاربر می بایست به صورت صحیح وارد شود و یا 0000000000 را وارد کنید'
						};
	var Form0_3FieldHelpId=["Username","Pass","AdslPhone","NOE","BirthDate","Mobile","Phone","NationalCode","Email","Supporter_Id","Center_Id","Visp_Id","ExpirationDate"];
	var Form0_3Str;
	//=======Form1 Service Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	Username:'کاراکتر انگلیسی حداکثر ۳۲ کاراکتر',
							AdslPhone:'و حداکثر ۱۰ کاراکتر ADSL شماره تلفن کاربر',
							NOE :'برای گزارش مورد نیاز است',
							BirthDate:'خالی بگذارید و یا با فرمت روز/ماه/سال وارد نمایید',
							Phone:'شماره تلفن ۱۱ رقمی- مثال:05632233041',
							MaxPrepaidDebit:'مبلغی که نماینده فروش می تواند با وجود بدهی،برای کاربر سرویس پیش پرداخت ثبت نماید',
							ExpirationDate:'تاریخ انقضا کاربری.بعد از این تاریخ کاربر نمی تواند متصل شود<br/>در صورتی که بخواهید کاربری منقضی نشود،خالی بگذارید',
							Mobile:'شماره همراه ۱۰ رقمی که با ۰ شروع می شود',
							NationalCode:'کد ملی کاربر می بایست به صورت صحیح وارد شود و یا 0000000000 را وارد کنید'
						};
	var Form1FieldHelpId=["Username","AdslPhone","NOE","BirthDate","Mobile","Phone","NationalCode","Email","Supporter_Id","Center_Id","Visp_Id","MaxPrepaidDebit","ExpirationDate"];
	var Form1TitleField="Username";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	function CreateForm1Str(RowId){
		var Form1Str = [
			{ type:"settings",labelWidth:124,offsetLeft:10},
			{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:32,inputWidth:120, info:true},
			
			{ type: "hidden" , name:"User_Id", label:"شناسه کاربر :",disabled:true, labelAlign:"left", inputWidth:200},
			{ type: "label"},
			/*{type: "newcolumn"},*/{ type: "input" , name:"UserCDT", label:"زمان ایجاد :",readonly:true, style:"background-color:#E6EEF0", labelAlign:"left", maxLength:16,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Shahkar", label:"شاهکار :",readonly:true, style:"background-color:#E6EEF0", labelAlign:"left", maxLength:16,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"ResellerName", label:"نام نماینده فروش :",readonly:true, style:"background-color:#E6EEF0",value:"", labelAlign:"left", inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"UserStatus", label:"وضعیت کاربر :",readonly:true, style:"background-color:#E6EEF0",value:"", labelAlign:"left", inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "hidden" , name:"StatusName", label:"نام وضعیت :", validate:"",value:"",readonly:true, style:"background-color:#E6EEF0", maxLength:12,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "hidden" , name:"UserType", label:"نوع کاربر :", validate:"",value:"0",readonly:true, style:"background-color:#E6EEF0", maxLength:12,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "hidden" , name:"Session", label:"نشست :", validate:"",value:"0",readonly:true, style:"background-color:#E6EEF0", maxLength:12,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "hidden" , name:"StaleSession", label:"نشست قدیمی :", validate:"",value:"0",readonly:true, style:"background-color:#E6EEF0", maxLength:12,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"InitialMonthOff", label:"تخفیف اولیه :",readonly:true, style:"background-color:#E6EEF0", maxLength:12,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"MaxPrepaidDebit", label:"(ریال)حداکثر بدهی :", validate:"NotEmpty",readonly:true, style:"background-color:#E6EEF0", maxLength:14,inputWidth:200, info:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Username", label:"نام کاربری :", labelAlign:"left", readonly:true, style:"background-color:#E6EEF0", maxLength:32,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "select", name:"Visp_Id",label: "ارائه دهنده مجازی :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectVisp&id="+RowId,required:true,validate:"IsID",inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "select", name:"Center_Id",label: "مرکز :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectCenter&id="+RowId,required:true,validate:"IsID",inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "select", name:"Supporter_Id",label: "پشتیبان :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectSupporter&id="+RowId,required:true,validate:"IsID",inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "input" , name:"AdslPhone", label:"ADSL شماره :", validate:"IsValidAdslPhone", labelAlign:"left", maxLength:10,inputWidth:200, info:true},
			/*{type: "newcolumn"},*/{ type: "select", name:"OwnershipType",label: "ADSL نوع مالکیت :",options:[{text:"مالک",value:"Owner"},{text:"مستاجر",value:"Renter"}],required:true,inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "input" , name:"NOE", label:"موقعیت مکانی وایرلس :", validate:"",value:"", maxLength:32,inputWidth:200, info:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"IdentInfo", label:"شناسه هویتی :", validate:"",value:"", maxLength:32,required:true,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"IPRouteLog", label:"لاگ IPRoute :", validate:"",value:"", maxLength:100,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Email", label:"ایمیل :", validate:"IsValidEMail",value:"", maxLength:128,inputWidth:200},
			{ type: "input" , name:"Comment", label:"توضیح :", validate:"",value:"",rows:3, maxLength:255,inputWidth:200,inputHeight:50},
			{type: "newcolumn"},
			{ type: "label"},{ type: "select", name:"CustomerType",label: "نوع کاربر :",options:[{text:"شخص حقیقی",value:"Person"},{text:"حقوقی",value:"Company"}],required:true,inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Organization", label:"نام شرکت :", validate:"",value:"", maxLength:64,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"CompanyRegistryCode", label:"شماره ثبت شرکت :", validate:"",value:"", maxLength:12,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"CompanyRegistrationDate", label:"تاریخ ثبت شرکت :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"CompanyEconomyCode", label:"شماره اقتصادی شرکت :", validate:"",value:"", maxLength:12,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"CompanyNationalCode", label:"شناسه ملی شرکت :", validate:"",value:"", maxLength:12,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "select", name:"CompanyType",label: "نوع شرکت :",options:[
				{text:"سهامی عام",value:"Sahami_Aam"},
				{text:"سهامی خاص",value:"Sahami_Khas"},
				{text:"مسئولیت محدود",value:"Masouliyat_Mahdoud"},
				{text:"تضامنی",value:"Tazamoni"},
				{text:"مختلط غیر سهامی",value:"Mokhtalet_Gheyr_Sahami"},
				{text:"مختلط سهامی",value:"Mokhtalet_Sahami"},
				{text:"نسبی",value:"Nesbi"},
				{text:"تعاونی",value:"TaAavoni"},
				{text:"دولتی",value:"Dolati"},
				{text:"وزارتخانه",value:"Vezaratkhane"},
				{text:"سفارتخانه",value:"Sefaratkhane"},
				{text:"مسجد",value:"Masjed"},
				{text:"مدرسه",value:"Madrese"},
				{text:"NGO",value:"NGO"}
				],required:true,inputWidth:198,hidden:true},
			/*{type: "newcolumn"},*/{ type: "select", name:"Nationality",label: "ملیت :",options:GetCountryList(),required:true,inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Name", label:"نام :", validate:"",value:"", maxLength:32,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Family", label:"نام خانوادگی :", validate:"",value:"", maxLength:32,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "select", name:"Gender",label: "جنسیت :",options:[{text:"آقا",value:"Male"},{text:"خانم",value:"Female"}],required:true,inputWidth:198},
			/*{type: "newcolumn"},*/{ type: "input" , name:"FatherName", label:"نام پدر :", validate:"",value:"", maxLength:32,inputWidth:200},
			/*{type: "newcolumn"},*/{ type: "input" , name:"Mobile", label:"موبایل :", validate:"IsValidMobileNo",value:"", maxLength:11,inputWidth:200, info:true,required:true},
			/*{type: "newcolumn"},*/{ type: "select", name:"IdentificationType",label: "نوع شناسایی :",options:[/* {text:"NationalCode",value:"NationalCode"}, */{text:"Passport",value:"Passport"},{text:"Amayesh",value:"Amayesh"},{text:"Refugee",value:"Refugee"},{text:"Identity",value:"Identity"}],required:true,inputWidth:198,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"NationalCode", label:"کد ملی :", validate:"IsValidNationalCode",value:"", maxLength:10,inputWidth:200,required:true, info:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"IdentificationNo", label:"شماره شناسایی :", validate:"",value:"", maxLength:10,inputWidth:200,required:true, hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"CertificateNo", label:"شماره شناسنامه :", validate:"",value:"", maxLength:10,inputWidth:200,required:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"UniversalNo", label:"کد جهانی :", validate:"",value:"", maxLength:10,inputWidth:200,hidden:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"BirthDate", label:"تاریخ تولد :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200, info:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"BirthPlace", label:"محل تولد :", validate:"",value:"", maxLength:10,inputWidth:200,required:true},
            /*{type: "newcolumn"},*/
			/*{type: "newcolumn"},*/{ type: "input" , name:"Phone", label:"تلفن :", validate:"",value:"", maxLength:32,inputWidth:200,info:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"PostalCode", label:"کدپستی :", validate:"",value:"", maxLength:10,inputWidth:200,required:true},
			/*{type: "newcolumn"},*/{ type: "input" , name:"ExpirationDate", label:"تاریخ انقضا :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200, info:true},
			{ type: "input" , name:"Address", label:"آدرس :", validate:"",value:"",rows:3 , maxLength:255,inputWidth:200,inputHeight:50},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"}
		
		];
		return Form1Str;
	}	

	//=======Popup2 ChangePass
	var Popup2;
	var PopupId2=['ChangePass'];// popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangePass
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {Pass:'یک رشته برای کلمه عبور'};
	var Form2FieldHelpId=['Pass'];
	var Form2Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"Pass", label:"کلمه عبور :",validate:"", labelAlign:"left", info:true, inputWidth:200, required:true},
		{ type:"hidden" , name:"enpass"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "انجام",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
		
	//=======Popup3 ChangeUsername
	var Popup3;
	var PopupId3=['ChangeUsername'];

	//=======Form3 ChangeUsername
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {Username:'حداکثر 32 کاراکتر'};
	var Form3FieldHelpId=['Username'];
	var Form3Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"Username", label:"نام کاربری :",validate:"NotEmpty,IsValidUserName", labelAlign:"left", info:true, inputWidth:200, required:true},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "انجام",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];

	//=======Popup4 ChangeReseller
	var Popup4;
	var PopupId4=['ChangeReseller'];

	//=======Form4 ChangeReseller
	var Form4;
	var Form4PopupHelp;
	var Form4FieldHelp  = {Reseller:'حداکثر ۳۲ کاراکتر'};
	var Form4FieldHelpId=['Username'];
	function CreateForm4Str(RowId){
		var Form4Str = [
			{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
			{ type: "select", name:"Reseller_Id",label: "نماینده فروش :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectChangeReseller&id="+RowId,required:true,validate:"IsID",inputWidth:170},
			{type: "block", width: 250, list:[
				{ type: "button",name:"Proceed",value: "انجام",width :80},
				{type: "newcolumn", offset:20},
				{ type: "button",name:"Close",value: " بستن ",width :80}
			]}	
			];
		return Form4Str;
	}
		
	//=======Popup5 SetInitialMonthOff
	var Popup5;
	var PopupId5=['SetInitialMonthOff'];

	//=======Form5 SetInitialMonthOff
	var Form5;
	var Form5PopupHelp;
	var Form5FieldHelp  = {InitialMonthOff:'از ۰ تا ۹۹.۹۹'};
	var Form5FieldHelpId=['InitialMonthOff'];
	var Form5Str = [
		{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"InitialMonthOff", label:"تخفیف اولیه :",validate:"NotEmpty,IsValidPercent", labelAlign:"left", info:true, inputWidth:100, required:true},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "ذخیره",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
	];
	
	//=======Popup6 SendSMS
	var Popup6;
	var PopupId6=['SendSMS'];

	//=======Form6 SendSMS
	var Form6;
	var Form6PopupHelp;
	var Form6FieldHelp  = {
		Username:"نام کاربری",
		Pass:"کلمه عبور کاربر",
		SHDate:"تاریخ فعلی(شمسی)",
		Time:"تاریخ فعلی",
		RTrM:"ترافیک باقی مانده کلی فعلی بجز هدیه(مگابایت)",
		GiftTraffic:"ترافیک باقی مانده فعلی هدیه(مگابایت)",
		RTiH:"زمان باقی مانده کلی فعلی بجز هدیه(ساعت)",
		GiftTime:"زمان باقی مانده فعلی هدیه(ساعت)",
		ShExpireDate:"تاریخ انقضا سرویس",
		UserDebit:"وضعیت بدهی کاربر",
		CompanyName:"نام شرکت شما(توسط مدیر کل تنظیم می شود)",
		SupportPhone:"شماره پشتیبانی شرکت شما(توسط مدیر کل تنظیم می شود)"		
	};
	var Form6FieldHelpId=["Username","Pass","SHDate","Time","RTrM","GiftTraffic","RTiH","GiftTime","ShExpireDate","UserDebit","CompanyName","SupportPhone"];
	var Form6Str = [
		{type: "fieldset",width:480,label:" گزینه های پیامک ", list:[
			{type:"hidden",name:"CheckSMSprovider",value:0},
			{type:"settings", position: "label-right"},
			{type: "radio", name: "SMSType", value: "InfoSMS", label: "ارسال اطلاعات پیامک",disabled:true,list:[
				{type: "checkbox", label: "نام کاربری", name: "Username", checked: false,info:true},
				{type: "checkbox", label: "کلمه عبور", name: "Pass", checked: false,info:true},
				{type: "checkbox", label: "(تاریخ فعلی(شمسی", name: "SHDate", checked: false,info:true},
				{type: "checkbox", label: "زمان فعلی", name: "Time", checked: false,info:true},
				{type:"newcolumn", offset:5},
				{type: "checkbox", label: "ترافیک باقی مانده", name: "RTrM", checked: false,info:true},
				{type: "checkbox", label: "ترافیک باقی مانده هدیه", name: "GiftTraffic", checked: false,info:true},
				{type: "checkbox", label: "زمان باقی مانده", name: "RTiH", checked: false,info:true},
				{type: "checkbox", label: "زمان باقی مانده هدیه", name: "GiftTime", checked: false,info:true},
				{type:"newcolumn", offset:5},
				{type: "checkbox", label: "(تاریخ پایان(شمسی", name: "ShExpireDate", checked: false,info:true},
				{type: "checkbox", label: "بدهی کاربر", name: "UserDebit", checked: false,info:true},
				{type: "checkbox", label: "<span style='font-style:oblique;font-weight:bold'>نام شرکت</span>", name: "CompanyName", checked: false,info:true},
				{type: "checkbox", label: "<span style='font-style:oblique;font-weight:bold'>تلفن پشتیبان</span>", name: "SupportPhone", checked: false,info:true},
			]},
			{type: "radio", name: "SMSType", value: "CustomSMS", label: "ارسال پیامک سفارشی",disabled:true}
		]},
		{type: "fieldset",width:480,label:" جزئیات پیامک ", list:[
			// {type:"label", name:"CharacterCount",label:"<div style='color:slategray;font-weight:normal'><span style='color:indianred;font-weight:bold'>0</span> character</div>",labelWidth:340},
			{type:"input", style:"direction:rtl;text-align:right;padding:1px 4px 1px 4px", name:"SMSSample", label:"نمونه</br><span style='color:indianred'>0</br>کاراکتر</span>:",maxLength:210, rows:4, inputWidth:360,labelWidth:60,hidden:true, disabled:true},
			{type:"hidden",name:"InfoSMSFields",value:""},
			{type:"input", style:"direction:rtl;text-align:right;padding:1px 4px 1px 4px", name:"CustomSMSMessage", label:"پیام</br><span style='color:indianred'>0</br>کاراکتر</span>:",maxLength:268, rows:4, inputWidth:360,labelWidth:60,hidden:true}
		]},
		{type: "block", width: 460, list:[
			{ type: "button",name:"Proceed",value: "ارسال",width :80, disabled:true},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:120},
			{ type: "button",name:"Clear",value: " پاک کردن ",width :80}
		]}	
	];

	//=======Popup7 SetMaxPrepaidDebit
	var Popup7;
	var PopupId7=['SetMaxPrepaidDebit'];

	//=======Form7 SetMaxPrepaidDebit
	var Form7;
	var Form7PopupHelp;
	var Form7FieldHelp  = {MaxPrepaidDebit:'مبلغی که نماینده فروش می تواند با وجود بدهی،برای کاربر سرویس پیش پرداخت ثبت نماید(۰ تا ۱۰۰,۰۰۰,۰۰۰ ریال)'};
	var Form7FieldHelpId=['MaxPrepaidDebit'];
	var Form7Str = [
		{ type:"settings" , labelWidth:140, inputWidth:80,offsetLeft:10  },
		{ type: "input" , name:"MaxPrepaidDebit", label:"حداکثر بدهی مجاز (ریال) :", validate:"IsValidPrice",value:"10000", labelAlign:"left", maxLength:9,inputWidth:200,info:"true"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "ذخیره",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
	];
	// Layout   ===================================================================

	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	var TopToolbar;
	var TabbarMain;
	var Toolbar1;
	if(RowId>0){
	
		// TopToolbar   ===================================================================
		TopToolbar = dhxLayout.cells("a").attachToolbar();
		DSToolbarInitial(TopToolbar);
		DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);

		// TabbarMain   ===================================================================
		TabbarMain = dhxLayout.cells("a").attachTabbar();
		DSTabbarInitial(TabbarMain,TabbarMainArray);

		// Toolbar1   ===================================================================
		Toolbar1 = TabbarMain.cells(0).attachToolbar();
		DSToolbarInitial(Toolbar1);
		
		// Form1   ===================================================================
		Permission=LoadPermissionByUser(RowId);
		var SpacerButton="Exit";
		if(ISPermit("Visp.User.GetPassword")){
			AddPopupChangePass();
			SpacerButton="ChangePass";
		}
		if(ISPermit("Visp.User.ChangeUsername")) {
			AddPopupChangeUsername();
			SpacerButton="ChangeUsername";
		}
		if(ISPermit("Visp.User.ChangeReseller")){
			AddPopupChangeReseller();
			SpacerButton="ChangeReseller";
		}
		TopToolbar.addSpacer(SpacerButton);
		
		if(ISPermit("Visp.User.WebUnblock")) DSToolbarAddButton(TopToolbar,null,"WebUnBlock","رفع مسدودی پنل","tow_WebUnBlock",TopToolbar_OnWebUnBlockClick);
		if(ISPermit("Visp.User.RadiusUnblock")) DSToolbarAddButton(TopToolbar,null,"RadiusUnBlock","رفع مسدودی ردیوس","tow_RadiusUnBlock",TopToolbar_OnRadiusUnBlockClick);
		
		Form1=DSInitialForm(TabbarMain.cells(0),CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
		SetViewEditFieldState(Form1,Permission,"InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate");
		Form1.attachEvent("onChange",Form1OnChange);
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		
		DSToolbarAddButton(Toolbar1,null,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		SpacerButton="Retrieve";
		if(ISPermit("Visp.User.Edit")){
			DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
			SpacerButton="Save";
		}
		if((ISPermit("Visp.User.SetInitialMonthOff"))||(ISPermit("Visp.User.SetMaxPrepaidDebit"))){
	
			Toolbar1.addSeparator("sep1",null);
			if(ISPermit("Visp.User.SetInitialMonthOff")){
				AddPopupSetInitialMonthOff();
				SpacerButton="SetInitialMonthOff";
			}
			if(ISPermit("Visp.User.SetMaxPrepaidDebit")){
				AddPopupSetMaxPrepaidDebit();
				SpacerButton="SetMaxPrepaidDebit";
			}
		}
		if(ISPermit("Visp.User.SendInfoSMS")||ISPermit("Visp.User.SendCustomSMS")){
			Toolbar1.addSeparator("sep2",null);
			AddPopupSendSMS();
			SpacerButton="SendSMS";
		}
		if(ISPermit("Visp.User.Shahkar")){
			Toolbar1.addSeparator("sep3",null);
			DSToolbarAddButton(Toolbar1,null,"Shahkar","شاهکار","tog_Shahkar",Toolbar1_OnShahkarClick);
			Toolbar1.hideItem("Shahkar");
			SpacerButton="Shahkar";
		}
		if(ISPermit("Visp.User.UsersWebsite")){
			Toolbar1.addSpacer(SpacerButton);
			DSToolbarAddButton(Toolbar1,null,"UsersWebsite","پنل کاربر","tog_UsersWebsite",function(){window.open("DSGetUserSession.php?"+un()+"&Id="+RowId)});
		}
	}
	else{
		Form0_1=DSInitialForm(dhxLayout.cells("a"),Form0_1Str,Form0_1PopupHelp,Form0_1FieldHelpId,Form0_1FieldHelp,Form0_1OnButtonClick);
		Form0_1.attachEvent("onEnter",function(){Form0_1OnButtonClick("Next")});
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		Form0_1.setFocusOnFirstActive();
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);

//FUNCTION========================================================================================================================
//================================================================================================================================
function Toolbar1_OnShahkarClick(){
	dhxLayout.progressOn();
	dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=Shahkar&id="+RowId,function(loader){
		dhxLayout.progressOff();
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		if((response=='')||(response[0]=='~'))
			dhtmlx.alert({text:response.substring(1),type:"alert-error",title:"هشدار"});
		else if(response=='OK~') {
			// Toolbar1.hideItem("Shahkar");
			dhtmlx.alert("OK");
		}
		else dhtmlx.alert(response);
	});
}

function Form1OnChange(id,value){
	// dhtmlx.message(id);
	// dhtmlx.message(value);
	if(id=="CustomerType"){
		if(value=='Person'){
			FormHideItem(Form1,["CompanyType","CompanyNationalCode","CompanyEconomyCode","CompanyRegistrationDate","CompanyRegistryCode","Organization"]);
			Form1.setItemLabel("Name","نام :");
			Form1.setItemLabel("Family","نام خانوادگی :");
			Form1.setItemLabel("Nationality","ملیت :");
			Form1.setItemLabel("FatherName","نام پدر :");
			Form1.setItemLabel("BirthDate","تاریخ تولد :");			
			Form1.setItemLabel("Gender","جنسیت :");			
		}
		else{
			FormShowItem(Form1,["CompanyType","CompanyNationalCode","CompanyEconomyCode","CompanyRegistrationDate","CompanyRegistryCode","Organization"]);
			SetViewEditFieldState(Form1,Permission,"CompanyType,CompanyNationalCode,CompanyEconomyCode,CompanyRegistrationDate,CompanyRegistryCode,Organization");
			Form1.setItemLabel("Name","نام نماینده :");
			Form1.setItemLabel("Family","نام خانوادگی نماینده :");
			Form1.setItemLabel("Nationality","ملیت نماینده شرکت :");
			Form1.setItemLabel("FatherName","نام پدر نماینده :");
			Form1.setItemLabel("BirthDate","تاریخ تولد نماینده :");
			Form1.setItemLabel("Gender","جنسیت نماینده :");
		}
	}
	if(id=="Nationality"){
		if(value=='IRN'){
			FormShowItem(Form1,["NationalCode","CertificateNo","BirthPlace"]);
			FormHideItem(Form1,["UniversalNo","IdentificationType","IdentificationNo"]);
			SetViewEditFieldState(Form1,Permission,"NationalCode,CertificateNo,BirthPlace");
		}
		else{
			FormShowItem(Form1,[/* "UniversalNo", */"IdentificationType","IdentificationNo"]);
			FormHideItem(Form1,["NationalCode","CertificateNo","BirthPlace"]);
			// SetViewEditFieldState(Form1,Permission,"UniversalNo,IdentificationType,IdentificationNo");
			SetViewEditFieldState(Form1,Permission,"IdentificationType,IdentificationNo");
		}
	}
	if(id=="Visp_Id"){
		
			//alert("1");
//DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);			
	}
}

function Form0_3OnChange(id,value){
	// dhtmlx.message(id);
	// dhtmlx.message(value);
	if(id=="CustomerType"){
		if(value=='Person'){
			FormHideItem(Form0_3,["CompanyType","CompanyNationalCode","CompanyEconomyCode","CompanyRegistrationDate","CompanyRegistryCode","Organization"]);
			Form0_3.setItemLabel("Name","نام :");
			Form0_3.setItemLabel("Family","نام خانوادگی :");
			Form0_3.setItemLabel("Nationality","ملیت :");
			Form0_3.setItemLabel("FatherName","نام پدر :");
			Form0_3.setItemLabel("BirthDate","تاریخ تولد :");			
			Form0_3.setItemLabel("Gender","جنسیت :");			
		}
		else{
			FormShowItem(Form0_3,["CompanyType","CompanyNationalCode","CompanyEconomyCode","CompanyRegistrationDate","CompanyRegistryCode","Organization"]);
			SetAddFieldState(Form0_3,Permission,"CompanyType,CompanyNationalCode,CompanyEconomyCode,CompanyRegistrationDate,CompanyRegistryCode,Organization");
			Form0_3.setItemLabel("Name","نام نماینده :");
			Form0_3.setItemLabel("Family","نام خانوادگی نماینده :");
			Form0_3.setItemLabel("Nationality","ملیت نماینده شرکت :");
			Form0_3.setItemLabel("FatherName","نام پدر نماینده :");
			Form0_3.setItemLabel("BirthDate","تاریخ تولد نماینده :");
			Form0_3.setItemLabel("Gender","جنسیت نماینده :");
		}
	}
	if(id=="Nationality"){
		if(value=='IRN'){
			FormShowItem(Form0_3,["NationalCode","CertificateNo","BirthPlace"]);
			FormHideItem(Form0_3,["UniversalNo","IdentificationType","IdentificationNo"]);
			SetAddFieldState(Form0_3,Permission,"NationalCode,CertificateNo,BirthPlace");
		}
		else{
			FormShowItem(Form0_3,[/* "UniversalNo", */"IdentificationType","IdentificationNo"]);
			FormHideItem(Form0_3,["NationalCode","CertificateNo","BirthPlace"]);
			// SetAddFieldState(Form0_3,Permission,"UniversalNo,IdentificationType,IdentificationNo");
			SetAddFieldState(Form0_3,Permission,"IdentificationType,IdentificationNo");
		}
	}
}

function Form0_1OnButtonClick(name){
	if(name=='Next'){
		if(DSFormValidate(Form0_1,Form0_1FieldHelpId)){
			Username=Form0_1.getItemValue("Username");
			dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=checkusername&Username="+Username,function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if(response=='OK~'){
					Form0_2Str = [
					{ type:"block",list:[
						{ type:"settings",labelWidth:124,offsetLeft:10},
						{ type: "label"},
						{ type: "label"},
						{ type: "input" , name:"Username", label:"نام کاربری :", value: Username , labelAlign:"left", maxLength:32,inputWidth:200, required:true,disabled:true},
						{ type: "select", name:"Visp_Id",label: "ارائه دهنده مجازی :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectVispByUsername&Username="+Username,required:true,validate:"IsID",inputWidth:200},
						{ type: "select", name:"Center_Id",label: "مرکز :",required:true,inputWidth:200,disabled:true},
						{ type: "select", name:"Supporter_Id",label: "پشتیبان :",required:true,inputWidth:200,disabled:true},
						{ type: "select", name:"Status_Id",label: "وضعیت :",required:true,inputWidth:200,disabled:true},
					]},
						{ type: "label"},
						{type: "block", width: 250, list:[
						{ type: "button",name:"Next",value: "بعدی",width :120},
						{type: "newcolumn", offset:20},
						{ type: "button",name:"Close",value: " بستن ",width :80}
						]}
					];
					dhxLayout.cells("a").progressOn();
					Form0_1.unload();
					Form0_2=DSInitialForm(dhxLayout.cells("a"),Form0_2Str,Form0_2PopupHelp,Form0_2FieldHelpId,Form0_2FieldHelp,Form0_2OnButtonClick);
					Form0_2.lock();
					Form0_2.attachEvent("onOptionsLoaded", function(name){
						dhxLayout.cells("a").progressOff();
						if(name=="Visp_Id"){
							Form0_2.unlock();
							Form0_2.setFocusOnFirstActive();
							var opts = Form0_2.getOptions("Visp_Id");
							if(opts.length==1)
								Form0_2OnButtonClick("Next");
							else{
								Form0_2.attachEvent("onEnter",function(){
									Form0_2.updateValues();
									Form0_2OnButtonClick("Next");
								});
							};
						}
					});
				}
				else
					dhtmlx.alert({type:"error",text:"خطا، "+response.substring(1),callback:function(){Form0_1.setFocusOnFirstActive();}});
			});
		}
	}	
	else if(name=='Close')
		parent.dhxLayout.dhxWins.window("popupWindow").close();
}
function Form0_2OnButtonClick(name){
	if(name=='Next'){
		Form0_2.lock();
		Username=Form0_2.getItemValue("Username");
		
		var Visp_Id=Form0_2.getItemValue("Visp_Id");
		var opts=Form0_2.getOptions("Visp_Id");
		var VispName=opts[opts.selectedIndex].text;
		
		Permission=LoadPermissionByVisp(Visp_Id);
		Form0_3Str = [
			{ type:"block",list:[
				{ type:"settings",labelWidth:124,offsetLeft:10},
				{ type: "label"},
				{ type: "label"},
				{ type: "input" , name:"Username", label:"نام کاربری :", value: ""+Username , labelAlign:"left", maxLength:32,inputWidth:200, required:true,disabled:true},
				{ type: "input" , name:"Pass", label:"کلمه عبور :" , labelAlign:"left", maxLength:32,inputWidth:200/* , required:true */},
				{ type: "select", name:"Visp_Id",label: "ارائه دهنده مجازی :",options:[{text:VispName,value:Visp_Id}],required:true,validate:"IsID",inputWidth:200,disabled:true},
				{ type: "select", name:"Center_Id",label: "مرکز :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectCenterByUsername&Visp_Id="+Visp_Id+"&Username="+Username,required:true,validate:"IsID",inputWidth:200},
				{ type: "select", name:"Supporter_Id",label: "پشتیبان :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectSupporterByUsername&Username="+Username,required:true,validate:"IsID",inputWidth:200},
				{ type: "select", name:"Status_Id",label: "وضعیت :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectStatus&Visp_Id="+Visp_Id,required:true,validate:"IsID",inputWidth:200},
				{ type: "input" , name:"AdslPhone", label:"ADSL تلفن :", validate:"IsValidAdslPhone", labelAlign:"left", maxLength:10,inputWidth:200, info:true},
				{ type: "select", name:"OwnershipType",label: "ADSL نوع مالکیت :",options:[{text:"مالک",value:"Owner"},{text:"مستاجر",value:"Renter"}],required:true,inputWidth:198},
				{ type: "input" , name:"NOE", label:"موقعیت مکانی وایرلس :", validate:"",value:"", maxLength:32,inputWidth:200, info:true},
				{ type: "input" , name:"IdentInfo", label:"شناسه هویتی :", validate:"",value:"", maxLength:32,required:true,inputWidth:200},
				{ type: "input" , name:"IPRouteLog", label:"IPRoute لاگ :", validate:"",value:"", maxLength:100,inputWidth:200},
				{ type: "input" , name:"Email", label:"ایمیل :", validate:"IsValidEMail",value:"", maxLength:128,inputWidth:200},
				{ type: "input" , name:"Comment", label:"توضیح :", validate:"",value:"",rows:3, maxLength:255,inputWidth:200,inputHeight:50},
				{type: "newcolumn"},
				{ type: "label"},
				{ type: "select", name:"CustomerType",label: "نوع کاربر :",options:[{text:"شخص حقیقی",value:"Person"},{text:"حقوقی",value:"Company"}],required:true,inputWidth:198},				
				{ type: "input" , name:"Organization", label:"نام شرکت :", validate:"",value:"", maxLength:64,inputWidth:200,hidden:true},
				{ type: "input" , name:"CompanyRegistryCode", label:"شماره ثبت شرکت :", validate:"",value:"", maxLength:12,inputWidth:200,hidden:true},
				{ type: "input" , name:"CompanyRegistrationDate", label:"تاریخ ثبت شرکت :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200,hidden:true},
				{ type: "input" , name:"CompanyEconomyCode", label:"شماره اقتصادی شرکت :", validate:"",value:"", maxLength:12,inputWidth:200,hidden:true},
				{ type: "input" , name:"CompanyNationalCode", label:"شناسه ملی شرکت :", validate:"",value:"", maxLength:12,inputWidth:200,hidden:true},
				{ type: "select", name:"CompanyType",label: "نوع شرکت :",options:[
					{text:"سهامی عام",value:"Sahami_Aam"},
					{text:"سهامی خاص",value:"Sahami_Khas"},
					{text:"مسئولیت محدود",value:"Masouliyat_Mahdoud"},
					{text:"تضامنی",value:"Tazamoni"},
					{text:"مختلط غیر سهامی",value:"Mokhtalet_Gheyr_Sahami"},
					{text:"مختلط سهامی",value:"Mokhtalet_Sahami"},
					{text:"نسبی",value:"Nesbi"},
					{text:"تعاونی",value:"TaAavoni"},
					{text:"دولتی",value:"Dolati"},
					{text:"وزارتخانه",value:"Vezaratkhane"},
					{text:"سفارتخانه",value:"Sefaratkhane"},
					{text:"مسجد",value:"Masjed"},
					{text:"مدرسه",value:"Madrese"},
					{text:"NGO",value:"NGO"}
					],required:true,inputWidth:198,hidden:true},
				{ type: "select", name:"Nationality",label: "ملیت :",options:GetCountryList(),required:true,inputWidth:198},				
				{ type: "input" , name:"Name", label:"نام :", validate:"",value:"", maxLength:32,inputWidth:200},
				{ type: "input" , name:"Family", label:"نام خانوادگی :", validate:"",value:"", maxLength:32,inputWidth:200},
				{ type: "select", name:"Gender",label: "جنسیت :",options:[{text:"آقا",value:"Male"},{text:"خانم",value:"Female"}],required:true,inputWidth:198},
				{ type: "input" , name:"FatherName", label:"نام پدر :", validate:"",value:"", maxLength:32,inputWidth:200},
				{ type: "input" , name:"Mobile", label:"موبایل :", validate:"IsValidMobileNo",value:"", maxLength:11,inputWidth:200,info:true/* ,required:true */},
				{ type: "select", name:"IdentificationType",label: "نوع شناسایی :",options:[/* {text:"NationalCode",value:"NationalCode"}, */{text:"Passport",value:"Passport"},{text:"Amayesh",value:"Amayesh"},{text:"Refugee",value:"Refugee"},{text:"Identity",value:"Identity"}],required:true,inputWidth:198,hidden:true},				
				{ type: "input" , name:"NationalCode", label:"کد ملی :",validate:"IsValidNationalCode",value:"", maxLength:10,inputWidth:200, info:true/* ,required:true */},
				
				{ type: "input" , name:"IdentificationNo", label:"شماره شناسایی :", validate:"",value:"", maxLength:10,inputWidth:200,required:true, hidden:true},
				{ type: "input" , name:"CertificateNo", label:"شماره شناسنامه :", validate:"",value:"", maxLength:10,inputWidth:200,required:true},
				{ type: "input" , name:"UniversalNo", label:"شماره جهانی :", validate:"",value:"", maxLength:10,inputWidth:200,hidden:true},				
				
				{ type: "input" , name:"BirthPlace", label:"محل تولد :", validate:"",value:"", maxLength:10,inputWidth:200,required:true},
				{ type: "input" , name:"BirthDate", label:"تاریخ تولد :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200,info:true},
				{ type: "input" , name:"Phone", label:"تلفن :", validate:"",value:"", maxLength:32,inputWidth:200,info:true},
				{ type: "input" , name:"PostalCode", label:"کد پستی :", validate:"",value:"", maxLength:10,inputWidth:200,required:true},
				{ type: "input" , name:"ExpirationDate", label:"تاریخ انقضا :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200, info:true},
				{ type: "input" , name:"Address", label:"آدرس :", validate:"",value:"",rows:3 , maxLength:255,inputWidth:200,inputHeight:50},
			]},
				{ type: "label"},
				{type: "block", width: 250, list:[
				{ type: "button",name:"Next",value: "ایجاد کاربر",width :120},
				{type: "newcolumn", offset:20},
				{ type: "button",name:"Close",value: " بستن ",width :80}
				]}
		];
		dhxLayout.cells("a").progressOn();
		Form0_2.unload();
		Form0_3=DSInitialForm(dhxLayout.cells("a"),Form0_3Str,Form0_3PopupHelp,Form0_3FieldHelpId,Form0_3FieldHelp,Form0_3OnButtonClick);
		
		Form0_3.attachEvent("onChange",Form0_3OnChange);
		
		SetAddFieldState(Form0_3,Permission,"Pass,AdslPhone,NOE,IdentInfo,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate");
		Form0_3.lock();
		var PendingLoadObjects=3;
		Form0_3.attachEvent("onOptionsLoaded", function(name){
			var opts = Form0_3.getOptions(name);
			if(opts.length<=0){
				dhtmlx.alert("The field ["+name+"] has no choice to select");
				Form0_3.disableItem("Next");
			}
			PendingLoadObjects--;
			if(PendingLoadObjects<=0){
				dhxLayout.cells("a").progressOff();
				Form0_3.unlock();
				Form0_3.setFocusOnFirstActive();
				Form0_3.attachEvent("onEnter",function(){
					Form0_3.updateValues();
					Form0_3OnButtonClick("Next");
				});
			}
		});
	}
	else if(name=='Close')
		parent.dhxLayout.dhxWins.window("popupWindow").close();
}

function Form0_3OnButtonClick(name){
	if(name=='Next'){
		if(DSFormValidate(Form0_3,Form0_3FieldHelpId)){
			Form0_3.lock();
			dhtmlx.confirm({
				title: "هشدار",
				type:"confirm",
				ok:"بلی",
				cancel:"خیر",
				text: "کاربر ایجاد خواهد شد<br/>آیا مطمئن هستید؟",
				callback: function(result) {
					if(result)
						DSFormInsertRequestProgress(dhxLayout,Form0_3,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=insert",Form0_3DoAfterInsertOk,Form0_3DoAfterInsertFail);
					else{
						Form0_3.unlock();
						Form0_3.setFocusOnFirstActive();
					}
				}
			});
		}
	}	
	else if(name=='Close')
		parent.dhxLayout.dhxWins.window("popupWindow").close();
}

//-------------------------------------------------------------------------------------------------
function AddPopupChangePass(){
	DSToolbarAddButtonPopup(TopToolbar,null,"ChangePass","تغییر کلمه عبور","tow_ChangePass");
	Popup2=DSInitialPopup(TopToolbar,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Form2OnButtonClick(name){//ChangePass
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId))
			DSFormUpdateRequestProgress(dhxLayout,Form2,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=ChangePass&id="+RowId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
	}
}

function Form2DoAfterUpdateOk(){
	Popup2.hide();
}

function Popup2OnShow(){//ChangePass
	if(!ISPermit("Visp.User.ChangePassword")){
		Form2.disableItem("Proceed");
		Form2.setReadonly("Pass",true);
	}
	else{
		Form2.enableItem("Proceed");
		Form2.setReadonly("Pass",false);
	}
	dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=GetPass&id="+RowId,function(loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		
		var parray=response.split("`",3);
		if(parray[0]!='OK'){
			dhtmlx.alert(response);
		}
		else{
			Form2.setItemValue("Pass",parray[1]);

		}
	});
	Form2.setItemFocus("Pass");
}

function Form2DoAfterLoadOk(){

}

function Form2DoAfterLoadFail(){

}

function Form2DoAfterUpdateFail(){

}

//-------------------------------------------------------------------------------------------------
function AddPopupChangeUsername(){
	DSToolbarAddButtonPopup(TopToolbar,null,"ChangeUsername","تغییر نام کاربری","tow_ChangeUsername");
	Popup3=DSInitialPopup(TopToolbar,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}

function Form3OnButtonClick(name){//ChangeUsername
	if(name=='Close') Popup3.hide();
	else{
		if(DSFormValidate(Form3,Form3FieldHelpId))
			DSFormUpdateRequestProgress(dhxLayout,Form3,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=ChangeUsername&id="+RowId,Form3DoAfterUpdateOk,Form3DoAfterUpdateFail);
	}
}

function Form3DoAfterUpdateOk(){//ChangeUsername
	NewPermission=LoadPermissionByUser(RowId);
	if(NewPermission!=Permission){
		dhxLayout.cells("a").progressOn();
		window.location.reload();
		return;
		Permission=NewPermission;
		Form1.unload();
		Form1=DSInitialForm(TabbarMain.cells(0), CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);	
		SetViewEditFieldState(Form1,Permission,"InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate");
	}
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	Popup3.hide();
	parent.UpdateGrid(0);
}

function Popup3OnShow(){//ChangeUsername
	Form3.setItemValue("Username",Form1.getItemValue(Form1TitleField));
	Form3.setItemFocus("Username");
}

function Form3DoAfterUpdateFail(){

}


//-------------------------------------------------------------------------------------------------
function AddPopupSetInitialMonthOff(){
	DSToolbarAddButtonPopup(Toolbar1,null,"SetInitialMonthOff","تخفیف اولیه","tow_SetInitialMonthOff");
	Popup5=DSInitialPopup(Toolbar1,PopupId5,Popup5OnShow);
	Form5=DSInitialForm(Popup5,Form5Str,Form5PopupHelp,Form5FieldHelpId,Form5FieldHelp,Form5OnButtonClick);
}

function Form5OnButtonClick(name){//SetInitialMonthOff
	if(name=='Close') Popup5.hide();
	else{
		if(DSFormValidate(Form5,Form5FieldHelpId))
			DSFormUpdateRequestProgress(dhxLayout,Form5,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SetInitialMonthOff&id="+RowId,Form5DoAfterUpdateOk,Form5DoAfterUpdateFail);
	}
}

function Form5DoAfterUpdateOk(){//SetInitialMonthOff
/* 	Form1.unload();
	Form1=null;
	// Permission=LoadPermissionByUser(RowId);
	Form1=DSInitialForm(TabbarMain.cells(0), CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);	
	SetViewEditFieldState(Form1,Permission,"InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate"); */
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	Popup5.hide();
	parent.UpdateGrid(0);
}

function Popup5OnShow(){//SetInitialMonthOff
	Form5.setItemValue("InitialMonthOff",Form1.getItemValue('InitialMonthOff'));
	Form5.setItemFocus("InitialMonthOff");
}

function Form5DoAfterUpdateFail(){

}

//-------------------------------------------------------------------------------------------------
function AddPopupSetMaxPrepaidDebit(){
	DSToolbarAddButtonPopup(Toolbar1,null,"SetMaxPrepaidDebit","حداکثر بدهی مجاز","tow_SetMaxPrepaidDebit");
	Popup7=DSInitialPopup(Toolbar1,PopupId7,Popup7OnShow);
	Form7=DSInitialForm(Popup7,Form7Str,Form7PopupHelp,Form7FieldHelpId,Form7FieldHelp,Form7OnButtonClick);
}

function Form7OnButtonClick(name){//SetMaxPrepaidDebit
	if(name=='Close') Popup7.hide();
	else{
		if(DSFormValidate(Form7,Form7FieldHelpId))
			DSFormUpdateRequestProgress(dhxLayout,Form7,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SetMaxPrepaidDebit&id="+RowId,Form7DoAfterUpdateOk,Form7DoAfterUpdateFail);
	}
}

function Form7DoAfterUpdateOk(){//SetMaxPrepaidDebit
	/* Form1.unload();
	Form1=null;
	// Permission=LoadPermissionByUser(RowId);
	Form1=DSInitialForm(TabbarMain.cells(0), CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);	
	SetViewEditFieldState(Form1,Permission,"InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate"); */
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	Popup7.hide();
	parent.UpdateGrid(0);
}

function Popup7OnShow(){//SetMaxPrepaidDebit
	
	Form7.setItemValue("MaxPrepaidDebit",Form1.getItemValue('MaxPrepaidDebit').replace(/,/g,''));
	Form7.setItemFocus("MaxPrepaidDebit");
}

function Form7DoAfterUpdateFail(){

}

//-------------------------------------------------------------------------------------------------
function AddPopupChangeReseller(){
	DSToolbarAddButtonPopup(TopToolbar,null,"ChangeReseller","تغییر نماینده فروش","tow_ChangeChangeReseller");
	Popup4=DSInitialPopup(TopToolbar,PopupId4,Popup4OnShow);
}

function Form4OnButtonClick(name){//ChangeReseller
	if(name=='Close') Popup4.hide();
	else{
		if(DSFormValidate(Form4,Form4FieldHelpId)){
			Popup4.hide();
			DSFormUpdateRequestProgress(dhxLayout,Form4,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=ChangeReseller&id="+RowId,Form4DoAfterUpdateOk,Form4DoAfterUpdateFail);
		}
	}
}

function Form4DoAfterUpdateOk(){//ChangeReseller
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
}

function Popup4OnShow(){//ChangeReseller
	Form4=DSInitialForm(Popup4,CreateForm4Str(RowId),Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,Form4OnButtonClick);
}

function Form4DoAfterUpdateFail(){

}

//-------------------------------------------------------------------------------------------------

function AddPopupSendSMS(){
	DSToolbarAddButtonPopup(Toolbar1,null,"SendSMS","ارسال پیامک","tow_SendSMS");
	Popup6=DSInitialPopup(Toolbar1,PopupId6,Popup6OnShow);
	InititialSendSMS();
}
function InititialSendSMS(){
	Form6=DSInitialForm(Popup6,Form6Str,Form6PopupHelp,Form6FieldHelpId,Form6FieldHelp,Form6OnButtonClick);
	Form6.attachEvent("onChange",Form6OnChange);
	Form6.attachEvent("onInputChange",Form6OnInputChange);
	var PermitSendCustomSMS=ISPermit("Visp.User.SendCustomSMS");
	var PermitSendInfoSMS=ISPermit("Visp.User.SendInfoSMS");
	if(PermitSendCustomSMS||PermitSendInfoSMS){
		if(PermitSendCustomSMS){
			Form6.enableItem("SMSType","CustomSMS");
			Form6.checkItem("SMSType","CustomSMS");
			Form6OnChange("SMSType","CustomSMS");
		}
		if(PermitSendInfoSMS){
			Form6.enableItem("SMSType","InfoSMS");
			Form6.checkItem("SMSType","InfoSMS");
			Form6OnChange("SMSType","InfoSMS");
		}
		Form6.enableItem("Proceed");
	}
	
}

function Popup6OnShow(){
	if(Form6.getItemValue("CheckSMSprovider")==0){
		Form6.lock();
		var loader=dhtmlxAjax.getSync(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=CheckSMSprovider&User_Id="+RowId);
			Form6.unlock();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
			else if(response=='NoProvider~'){
				Form6.setItemValue("CheckSMSprovider",-1);
			}
			else if(response=='OK~'){
				Form6.setItemValue("CheckSMSprovider",1);
			}
			else alert(response);
	}
	if(Form6.getItemValue("CheckSMSprovider")==-1){
		Popup6.hide();
		dhtmlx.message({text:"ارائه دهنده پیام کوتاه تعریف نشده",type:"error"});
	}
}


function Form6OnChange(name,value,value2){
	// dhtmlx.message("OnChange<br/>name="+name+"<br/>Value="+value+"<br/>Value2="+value2);
	if((name=="Username")||(name=="Pass")||(name=="SHDate")||(name=="Time")||(name=="RTrM")||(name=="GiftTraffic")||(name=="RTiH")||(name=="GiftTime")||(name=="ShExpireDate")||(name=="UserDebit")||(name=="CompanyName")||(name=="SupportPhone")){
		CreateTextMessage(name,value2);
	}
	else if(name=="SMSType")
		if(value=="InfoSMS"){
			Form6.hideItem("CustomSMSMessage");
			Form6.showItem("SMSSample");
		}
		else{
			Form6.setItemFocus("CustomSMSMessage");
			Form6.showItem("CustomSMSMessage");
			Form6.hideItem("SMSSample");
		}
}

function CreateTextMessage(name,IsAdd){
	var InfoSMSFields=Form6.getItemValue("InfoSMSFields");
	var SMSFields=((InfoSMSFields=="")?[]:InfoSMSFields.split(","));
	if(IsAdd){
		SMSFields.push(name);
	}
	else
		SMSFields.splice(SMSFields.indexOf(name),1);
	
	var r=[];
	for(i=0;i<SMSFields.length;++i){
		if(SMSFields[i]=="Username")
			r.push("نام کاربری:"+"[Username]");
		else if(SMSFields[i]=="Pass")
			r.push("رمز:"+"[Pass]");
		else if(SMSFields[i]=="SHDate")
			r.push("تاریخ:"+"[SHDate]");
		else if(SMSFields[i]=="Time")
			r.push("ساعت:"+"[Time]");
		else if(SMSFields[i]=="RTrM")
			r.push("ترافیک:"+"[RTrM]");
		else if(SMSFields[i]=="GiftTraffic")
			r.push("هدیه ترافیکی:"+"[GiftTraffic]");
		else if(SMSFields[i]=="RTiH")
			r.push("زمان:"+"[RTiH]");
		else if(SMSFields[i]=="GiftTime")
			r.push("هدیه زمانی:"+"[GiftTime]");
		else if(SMSFields[i]=="ShExpireDate")
			r.push("اعتبار سرویس:"+"[ShExpireDate]");
		else if(SMSFields[i]=="UserDebit")
			r.push("[UserDebit]");
		else if(SMSFields[i]=="CompanyName")
			r.push("[CompanyName]");
		else if(SMSFields[i]=="SupportPhone")
			r.push("[SupportPhone]");
	}
	var s=r.join("\n");
	var N=s.length;
	Form6.setItemValue("InfoSMSFields",SMSFields.join())
	Form6.setItemValue("SMSSample",s);
	Form6.setItemLabel("SMSSample","نمونه</br><span style='color:indianred'>"+N+"</br>کاراکتر</span>:");
}
function Form6OnInputChange(name,value){
	// dhtmlx.message("OnInputChange<br/>name="+name+"<br/>Value="+value);	
	if(name=="CustomSMSMessage"){
		var N=value.length;
		Form6.setItemLabel("CustomSMSMessage","پیام</br><span style='color:indianred'>"+N+"</br>کاراکتر"+(N>1?" ":"")+"</span>:");
	}
}

function Form6OnButtonClick(name){
	if(name=="Close"){
		Popup6.hide();
	}
	else if(name=="Proceed"){
		var PostStr="&SMSType="+Form6.getItemValue("SMSType");
		if(Form6.getItemValue("SMSType")=="InfoSMS"){
			var InfoSMSFields=Form6.getItemValue("InfoSMSFields");
			if(InfoSMSFields==""){
				alert("لطفا فیلدی را انتخاب کنید");
				return;
			}
			PostStr+="&InfoSMSFields="+InfoSMSFields;
		}
		else{
			var CustomSMSMessage=Form6.getItemValue("CustomSMSMessage");
			if(CustomSMSMessage==""){
				alert("لطفا متن پیام را وارد کنید");
				Form6.setItemFocus("CustomSMSMessage");
				return;
			}
			PostStr+="&CustomSMSMessage="+CustomSMSMessage;
		}
		Popup6.hide();
		dhxLayout.cells("a").progressOn();
		Form6.lock();
		dhtmlxAjax.post(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SendSMS&User_Id="+RowId,PostStr,function (loader){
			dhxLayout.cells("a").progressOff();
			Form2.unlock();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);

			if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
			else if(response=='OK~'){
				dhtmlx.message(" پیامک با موفقیت برای ارائه دهنده پیام کوتاه ارسال شد");
			}
			else alert(response);
		});
	}
	else if(name=="Clear"){
		Form6.unload();
		InititialSendSMS();
		Form6.setItemValue("CheckSMSprovider",1);
	}
	
}

function Form6DoAfterUpdateOk(){
	Form6.unlock();
}

function Form6DoAfterUpdateFail(){
	Form6.unlock();
}


function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}
function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		Form1.updateValues();
		//Form1.setItemValue("Address",Form1.getItemValue("Address").replace(/\s+/ig," "));
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
	}
}

function Form1OnButtonClick(){
}

function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}	


function TopToolbar_OnWebUnBlockClick(){
	dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=WebUnBlock&id="+RowId,function (loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);

		if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
		else if(response=='OK~') {
			dhtmlx.message("رفع مسدودی پنل با موفقیت انجام شد");
		}
		else alert(response);

	});
}


function TopToolbar_OnRadiusUnBlockClick(){
	dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=RadiusUnBlock&id="+RowId,function (loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);

		if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
		else if(response=='OK~') {
			dhtmlx.message("رفع مسدودی ردیوس با موفقیت انجام شد");
		}
		else alert(response);

	});
}

function Form1DoAfterLoadOk(){
	Form1OnChange("CustomerType",Form1.getItemValue("CustomerType"));
	Form1OnChange("Nationality",Form1.getItemValue("Nationality"));
	if(Form1.getItemValue("UserType")=='ADSL'){
		Form1.showItem("AdslPhone");
		Form1.showItem("OwnershipType");
	}
	else{
		Form1.hideItem("AdslPhone");
		Form1.hideItem("OwnershipType");
	}
	if(Form1.getItemValue("UserType")=='Wireless'){
		Form1.showItem("NOE");
	}
	else{
		Form1.hideItem("NOE");
	}
	if(Form1.getItemValue("Shahkar")==''){
		Form1.hideItem("Shahkar");
		Toolbar1.hideItem("Shahkar");
	}
	else{
		Form1.showItem("Shahkar");
		if(Form1.getItemValue("Shahkar")=='Not Set'){
			Toolbar1.showItem("Shahkar");
			Form1.getInput("Shahkar").style.color='red';
			Form1.getInput("Shahkar").style.fontWeight='bold';
		}
		else{
			Toolbar1.hideItem("Shahkar");
			Form1.getInput("Shahkar").style.color='black';
			Form1.getInput("Shahkar").style.fontWeight='normal';
		}
	}
	Err=Form1.getItemValue("Error");
	if(Err!=''){
		alert('خطا،'+Err);
		parent.dhxLayout.dhxWins.window("popupWindow").close();	
	}
	var Session=Form1.getItemValue('Session');
	var StaleSession=Form1.getItemValue('StaleSession');
	parent.dhxLayout.dhxWins.window("popupWindow").setText('ویرایش '+DataTitle+' ['+Form1.getItemValue(Form1TitleField)+'] "'+
	(Session>0?('<span style="color:forestgreen">آنلاین</span>')://'+(Session>1?('*'+Session):'')+'
		(Session==0?('<span style="color:red">آفلاین</span>'):('<span style="color:orange">آنلاین</span>'))//'+(Session<-1?('*'+(-Session)):'')+'
	)
	+(StaleSession>0?('<span style="color:indianred;font-size:80%">(+'+StaleSession+' StaleSession)</span>'):'')+'" [ '+Form1.getItemValue('StatusName')+' ]');
}	

function Form1DoAfterLoadFail(){
}	

function Form1DoAfterUpdateOk(){
	NewPermission=LoadPermissionByUser(RowId);
	if(NewPermission!=Permission){
		dhxLayout.cells("a").progressOn();
		window.location.reload();
		return;
		Permission=NewPermission;
		Form1=DSInitialForm(TabbarMain.cells(0), CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);	
		SetViewEditFieldState(Form1,Permission,"InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate");	
	}
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
}
function Form1DoAfterUpdateFail(){
	dhxLayout.progressOff();
}
function Form0_3DoAfterInsertOk(r){
	// TopToolbar   ===================================================================
	TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);

	// TabbarMain   ===================================================================
	TabbarMain = dhxLayout.cells("a").attachTabbar();
	DSTabbarInitial(TabbarMain,TabbarMainArray);

	// Toolbar1   ===================================================================
	Toolbar1 = TabbarMain.cells(0).attachToolbar();
	DSToolbarInitial(Toolbar1);
	
	// Form1   ===================================================================
	RowId=r;

	Permission=LoadPermissionByUser(RowId);
	
	var SpacerButton="Exit";
	if(ISPermit("Visp.User.GetPassword")){
		AddPopupChangePass();
		SpacerButton="ChangePass";
	}
	if(ISPermit("Visp.User.ChangeUsername")) {
		AddPopupChangeUsername();
		SpacerButton="ChangeUsername";
	}
	if(ISPermit("Visp.User.ChangeReseller")){
		AddPopupChangeReseller();
		SpacerButton="ChangeReseller";
	}
	TopToolbar.addSpacer(SpacerButton);	

	if(ISPermit("Visp.User.WebUnblock")) DSToolbarAddButton(TopToolbar,null,"WebUnBlock","رفع مسدودی پنل","tow_WebUnBlock",TopToolbar_OnWebUnBlockClick);
	if(ISPermit("Visp.User.RadiusUnblock")) DSToolbarAddButton(TopToolbar,null,"RadiusUnBlock","رفع مسدودی ردیوس","tow_RadiusUnBlock",TopToolbar_OnRadiusUnBlockClick);
	
	Form0_3.unload();
	Form1=DSInitialForm(TabbarMain.cells(0), CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	SetViewEditFieldState(Form1,Permission,"InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate");
	LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	
	DSToolbarAddButton(Toolbar1,null,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	SpacerButton="Retrieve";
	if(ISPermit("Visp.User.Edit")){
		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		SpacerButton="Save";
	}
	if((ISPermit("Visp.User.SetInitialMonthOff"))||(ISPermit("Visp.User.SetMaxPrepaidDebit"))){

		Toolbar1.addSeparator("sep1",null);
		if(ISPermit("Visp.User.SetInitialMonthOff")){
			AddPopupSetInitialMonthOff();
			SpacerButton="SetInitialMonthOff";
		}
		if(ISPermit("Visp.User.SetMaxPrepaidDebit")){
			AddPopupSetMaxPrepaidDebit();
			SpacerButton="SetMaxPrepaidDebit";
		}
	}
	if(ISPermit("Visp.User.SendInfoSMS")||ISPermit("Visp.User.SendCustomSMS")){
		Toolbar1.addSeparator("sep2",null);
		AddPopupSendSMS();
		SpacerButton="SendSMS";
	}
	if(ISPermit("Visp.User.UsersWebsite")){
		Toolbar1.addSpacer(SpacerButton);
		DSToolbarAddButton(Toolbar1,null,"UsersWebsite","پنل کاربر","tog_UsersWebsite",function(){window.open("DSGetUserSession.php?"+un()+"&Id="+RowId)});
	}
	parent.UpdateGrid(r);
}

function Form0_3DoAfterInsertFail(){
	Form0_3.unlock();
	Form0_3.setFocusOnFirstActive();
}

function DSAddTab(f_Tabbar,i,f_ar,f_RowId){
	f_Tabbar.addTab(i,f_ar[i][0] , f_ar[i][2]);
	f_Tabbar.setContentHref(i,f_ar[i][1]+".php?"+un()+"&ParentId="+f_RowId+"&"+f_ar[i][4]);
}

function LoadTabbarMain(f_TabbarMain,f_TabbarMainArray,f_RowId){
	var IsPermit_RadiusLog=ISPermit('Visp.User.RadiusLog.List');//1
	var IsPermit_CreditStatus=ISPermit('Visp.User.CreditStatus.List');//2
	var IsPermitStatus=ISPermit('Visp.User.Status.List');//3
	var IsPermitClass=ISPermit('Visp.User.Class.List');//4
	var IsPermitCallerId=ISPermit('Visp.User.CallerId.List');//5
	var IsPermitBase=ISPermit('Visp.User.Service.Base.List');//6
	var IsPermitExtraCredit=ISPermit('Visp.User.Service.ExtraCredit.List');//7
	var IsPermitIP=ISPermit('Visp.User.Service.IP.List');//8
	var IsPermitOther=ISPermit('Visp.User.Service.Other.List');//9
	var IsPermitGift=ISPermit('Visp.User.Gift.List');//10
	var IsPermitInstallment=ISPermit('Visp.User.Installment.List');//11
	var IsPermitPayment=ISPermit('Visp.User.Payment.List');//12
	var IsPermitAttachment=ISPermit('Visp.User.Attachment.List');//13
	var IsPermitParam=ISPermit('Visp.User.Param.List');//14
	var IsPermitConnection=ISPermit('Visp.User.Connection.List');//15
	var IsPermitNote=ISPermit('Visp.User.Note.List');//16
	var IsPermitChangeLog=ISPermit('Visp.User.ChangeLog.List');//17
	var IsPermitSession=ISPermit('Visp.User.Session.List');//18
	var IsPermitUrlList=ISPermit('Visp.User.URL.UrlList.List');//19
	var IsPermitTopSite=ISPermit('Visp.User.URL.TopSite.List');//20
	var IsPermitDailyUsage=ISPermit('Visp.User.DailyUsage.List');//21
	var IsPermitNotify=ISPermit('Visp.User.Notify.List');//22
	var IsPermitSupportHistory=ISPermit('Visp.User.SupportHistory.List');//23
	var IsPermitPayOnline=ISPermit('Visp.User.PayOnline.List');//24
	var IsPermitSavingOff=ISPermit('Visp.User.SavingOff.List');//25
	var IsPermitWebHistory=ISPermit('Visp.User.WebHistory.List');//26
	var IsPermitInvoice=ISPermit('Visp.User.Invoice.List');//27
	var IsPermitWebMessage=ISPermit('Visp.User.WebMessage.List');//28
	var IsPermitNetlogList=ISPermit('Visp.User.Netlog.NetlogList.List');//29
	
	
	if(IsPermit_RadiusLog || IsPermit_CreditStatus || IsPermitStatus || IsPermitClass || IsPermitCallerId ||IsPermitParam || IsPermitConnection || IsPermitSession || IsPermitDailyUsage || IsPermitNotify || IsPermitWebHistory){
		f_TabbarMain.addTab(1,'پشتیبانی' , 70);
		var TabbarSupport = f_TabbarMain.cells(1).attachTabbar();
		DSTabbarInitial1(TabbarSupport);
		if(IsPermit_RadiusLog) DSAddTab(TabbarSupport,1,f_TabbarMainArray,f_RowId);
		if(IsPermit_CreditStatus) DSAddTab(TabbarSupport,2,f_TabbarMainArray,f_RowId);
		if(IsPermitStatus) DSAddTab(TabbarSupport,3,f_TabbarMainArray,f_RowId);
		if(IsPermitClass) DSAddTab(TabbarSupport,4,f_TabbarMainArray,f_RowId);
		if(IsPermitCallerId) DSAddTab(TabbarSupport,5,f_TabbarMainArray,f_RowId);
		if(IsPermitParam) DSAddTab(TabbarSupport,14,f_TabbarMainArray,f_RowId);
		if(IsPermitConnection) DSAddTab(TabbarSupport,15,f_TabbarMainArray,f_RowId);
		if(IsPermitSession) DSAddTab(TabbarSupport,18,f_TabbarMainArray,f_RowId);
		if(IsPermitDailyUsage) DSAddTab(TabbarSupport,21,f_TabbarMainArray,f_RowId);
		if(IsPermitNotify) DSAddTab(TabbarSupport,22,f_TabbarMainArray,f_RowId);	
		if(IsPermitWebHistory) DSAddTab(TabbarSupport,26,f_TabbarMainArray,f_RowId);	
	}
	
	if(IsPermitBase || IsPermitExtraCredit || IsPermitIP || IsPermitOther || IsPermitGift || IsPermitInstallment || IsPermitPayment || IsPermitAttachment || IsPermitPayOnline || IsPermitSavingOff || IsPermitInvoice){
		f_TabbarMain.addTab(2,'سرویس' , 50);
		var TabbarService = f_TabbarMain.cells(2).attachTabbar();
		DSTabbarInitial1(TabbarService);
		if(IsPermitBase) DSAddTab(TabbarService,6,f_TabbarMainArray,f_RowId);
		if(IsPermitExtraCredit) DSAddTab(TabbarService,7,f_TabbarMainArray,f_RowId);
		if(IsPermitIP) DSAddTab(TabbarService,8,f_TabbarMainArray,f_RowId);
		if(IsPermitOther) DSAddTab(TabbarService,9,f_TabbarMainArray,f_RowId);
		if(IsPermitGift) DSAddTab(TabbarService,10,f_TabbarMainArray,f_RowId);
		if(IsPermitInstallment) DSAddTab(TabbarService,11,f_TabbarMainArray,f_RowId);
		if(IsPermitPayment) DSAddTab(TabbarService,12,f_TabbarMainArray,f_RowId);
		if(IsPermitInvoice) DSAddTab(TabbarService,27,f_TabbarMainArray,f_RowId);
		if(IsPermitPayOnline) DSAddTab(TabbarService,24,f_TabbarMainArray,f_RowId);	
		if(IsPermitSavingOff) DSAddTab(TabbarService,25,f_TabbarMainArray,f_RowId);	
		if(IsPermitAttachment) DSAddTab(TabbarService,13,f_TabbarMainArray,f_RowId);
	}
	
	if(IsPermitUrlList || IsPermitTopSite){
		f_TabbarMain.addTab(3,'صفحات بازدید شده' , 110);
		var TabbarURL = f_TabbarMain.cells(3).attachTabbar();
		DSTabbarInitial1(TabbarURL);
		if(IsPermitUrlList) DSAddTab(TabbarURL,19,f_TabbarMainArray,f_RowId);
		if(IsPermitTopSite) DSAddTab(TabbarURL,20,f_TabbarMainArray,f_RowId);
	}
	if(IsPermitNetlogList){
		f_TabbarMain.addTab(4,'نت لاگ' , 50);
		var TabbarNetlog = f_TabbarMain.cells(4).attachTabbar();
		DSTabbarInitial1(TabbarNetlog);
		if(IsPermitNetlogList) DSAddTab(TabbarNetlog,29,f_TabbarMainArray,f_RowId);
	}
	
	if(IsPermitNote) DSAddTab(f_TabbarMain,16,f_TabbarMainArray,f_RowId);
	if(IsPermitSupportHistory) DSAddTab(f_TabbarMain,23,f_TabbarMainArray,f_RowId);
	if(IsPermitWebMessage) DSAddTab(f_TabbarMain,28,f_TabbarMainArray,f_RowId);
	if(IsPermitChangeLog) DSAddTab(f_TabbarMain,17,f_TabbarMainArray,f_RowId);
	f_TabbarMain.normalize();
	f_TabbarMain.attachEvent("onSelect", function(id, lastId){
		if((IsSupportTabNotLoaded)&&(id==1)){
			IsSupportTabNotLoaded=false;
			if(IsPermit_RadiusLog) TabbarSupport.setTabActive(1);
			else if(IsPermit_CreditStatus) TabbarSupport.setTabActive(2);
			else if(IsPermitStatus) TabbarSupport.setTabActive(3);
			else if(IsPermitClass) TabbarSupport.setTabActive(4);
			else if(IsPermitCallerId) TabbarSupport.setTabActive(5);
			else if(IsPermitParam) TabbarSupport.setTabActive(14);
			else if(IsPermitConnection) TabbarSupport.setTabActive(15);
			else if(IsPermitSession) TabbarSupport.setTabActive(18);
			else if(IsPermitDailyUsage) TabbarSupport.setTabActive(21);
			else if(IsPermitNotify) TabbarSupport.setTabActive(22);
			else if(IsPermitWebHistory) TabbarSupport.setTabActive(26);
		}
		else if((IsServiceTabNotLoaded)&&(id==2)){
			IsServiceTabNotLoaded=false;
				if(IsPermitBase) TabbarService.setTabActive(6);
				else if(IsPermitExtraCredit) TabbarService.setTabActive(7);
				else if(IsPermitIP) TabbarService.setTabActive(8);
				else if(IsPermitOther) TabbarService.setTabActive(9);
				else if(IsPermitGift) TabbarService.setTabActive(10);
				else if(IsPermitInstallment) TabbarService.setTabActive(11);
				else if(IsPermitPayment) TabbarService.setTabActive(12);
				else if(IsPermitAttachment) TabbarService.setTabActive(13);
				else if(IsPermitPayOnline) TabbarSupport.setTabActive(24);
				else if(IsPermitSavingOff) TabbarSupport.setTabActive(25);
				else if(IsPermitInvoice) TabbarSupport.setTabActive(27);
		}
		else if((IsURLTabNotLoaded)&&(id==3)){
			IsURLTabNotLoaded=false;
			if(IsPermitUrlList) TabbarURL.setTabActive(19);
			else if(IsPermitTopSite) TabbarURL.setTabActive(20);
		}
		else if((IsNetlogTabNotLoaded)&&(id==4)){
			//alert('1');
			IsNetlogTabNotLoaded=false;
			if(IsPermitNetlogList) TabbarNetlog.setTabActive(29);
			//else if(IsPermitTopSite) TabbarURL.setTabActive(20);
		}
    return true;
	});
}

function SetViewEditFieldState(f_Form,VispPermit,FieldList){
	//alert("VispPermit="+VispPermit);
	//alert("FieldList="+FieldList); 
	if(LoginResellerName=='admin') return;
	f_Form.lock();
	var temp=FieldList.split(",");
	//alert('1='+temp.length);
	for(var i=0;i<temp.length;i++){
	//alert(temp[i]);
		VP=VispPermit.search(",Visp.User.Info.ViewField."+temp[i]+",");
		EP=VispPermit.search(",Visp.User.Info.EditField."+temp[i]+",");
		if(VP<=0){
			if(EP>0){
				dhtmlx.alert({text:"خطای مجوز برای قسمت ["+temp[i]+"]!!!<br/>شما مجوز EditField را دارید, اما اجازه ViewField را ندارید !!!",type:"alert-error",callback:function(){parent.dhxLayout.dhxWins.window("popupWindow").close();},ok:"بستن"});
				return;
			}
			f_Form.hideItem(temp[i]);
		}
		else if(EP<=0)
			f_Form.disableItem(temp[i]);
	}
	f_Form.unlock();
}

function SetAddFieldState(f_Form,VispPermit,FieldList){
	//alert("VispPermit="+VispPermit);
	//alert("FieldList="+FieldList); 
	if(LoginResellerName=='admin') return;
	f_Form.lock();
	var temp=FieldList.split(",");
	for(var i=0;i<temp.length;i++){
		//alert(temp[i]);
		AP=VispPermit.search(",Visp.User.Info.AddField."+temp[i]+",");
		if(temp[i]=="Pass")
			VP=true;
		else
			VP=VispPermit.search(",Visp.User.Info.ViewField."+temp[i]+",");
		//alert("AP="+AP);
		if(VP<=0){
			if(AP>0){
				dhtmlx.alert({text:"خطای مجوز برای قسمت ["+temp[i]+"]!!!<br/>شما مجوز AddField را دارید, اما اجازه ViewField را ندارید !!!",type:"alert-error",callback:function(){parent.dhxLayout.dhxWins.window("popupWindow").close();},ok:"بستن"});
				return;
			}
			f_Form.hideItem(temp[i]);
		}
		else if(AP<=0)
			f_Form.disableItem(temp[i]);
	}
	f_Form.unlock();
}

function DSTabbarInitial1(f_TabbarMain){
	
	f_TabbarMain.setSkin(tabbar_main_skin);
	f_TabbarMain.setImagePath(tabbar_image_path);
	f_TabbarMain.setHrefMode("iframes-on-demand");
	f_TabbarMain.setMargin(0);
	f_TabbarMain.setOffset(0);
}
	
}//window.onload
var MyService={
	Base:[false,0,0],
	Extra:[false,0,0],
	Other:[false,0,0],
	IP:[false,0,0],
	Balance:0,
	RemainedSavingOff:0
};

function GetSumOfWithdrawSavingOff(Service){
	return MyService["Base"][2] + MyService["Extra"][2] + MyService["Other"][2] + MyService["IP"][2] - MyService[Service][2];
}
function GetRemainCredit(){
	return parseFloat(MyService["Base"][1])+parseFloat(MyService["Extra"][1])+parseFloat(MyService["Other"][1])+parseFloat(MyService["IP"][1])-parseFloat(MyService["Balance"]);
}

function GetBuyingBasket(){
	var s="<table border='1' align='center' width='250px' style='text-align:center;font-size:11px;cursor:pointer;white-space: nowrap;' cellpadding='4' cellspacing='1' title='Click to close'>";
	s+="<tr><td colspan='2' border='0' style='font-weight:bold'>سبد خرید</td></tr>";
	if(MyService["Base"][0]){
		s+="<tr style='background-color:#FFFFDD'><td>سرویس پایه"+(MyService["Base"][2]>0?("( - "+formatMoney(MyService["Base"][2],0, '.', ',')+"Rls)"):"")+":</td><td>(ریال) "+formatMoney(MyService["Base"][1],0, '.', ',')+"</td></tr>";
	}
	if(MyService["Extra"][0]){
		s+="<tr style='background-color:#FFFFDD'><td>سرویس اضافی"+(MyService["Extra"][2]>0?("( - "+formatMoney(MyService["Extra"][2],0, '.', ',')+"Rls)"):"")+":</td><td>(ریال) "+formatMoney(MyService["Extra"][1],0, '.', ',')+"</td></tr>";
	}
	if(MyService["IP"][0]){
		s+="<tr style='background-color:#FFFFDD'><td>سرویس آی پی"+(MyService["IP"][2]>0?("( - "+formatMoney(MyService["IP"][2],0, '.', ',')+"Rls)"):"")+":</td><td>(ریال) "+formatMoney(MyService["IP"][1],0, '.', ',')+"</td></tr>";
	}
	if(MyService["Other"][0]){
		s+="<tr style='background-color:#FFFFDD'><td>سرویس سایر"+(MyService["Other"][2]>0?("( - "+formatMoney(MyService["Other"][2],0, '.', ',')+"Rls)"):"")+":</td><td>(ریال) "+formatMoney(MyService["Other"][1],0, '.', ',')+"</td></tr>";
	}
	if(MyService["Balance"]!=0){
		s+="<tr style='background-color:"+(MyService["Balance"]>0?"#DDFFDD":"FFDDDD")+"'><td>تراز مالی:</td><td>(ریال) "+formatMoney(MyService["Balance"],0, '.', ',')+"  </td></tr>";
	}
	var t=GetRemainCredit();
	s+="<tr style='background-color:#BBDDFF;font-weight:bold'><td>جمع کل:</td><td>"+(t>0?("<span style='color:green'>دریافت "+formatMoney(t,0, '.', ',')+" ریال</span>"):t<0?("<span style='color:indianred'>بازگشت "+formatMoney(-t,0, '.', ',')+" ریال</span>"):"0")+"</td></tr>";
	s+="</table>";
	return s;
}

function IsBuyingBasketEmpty(IsForPaymentPage){
	if( ((IsForPaymentPage) && (MyService["Balance"]!=0)) || MyService["Base"][0] || MyService["Extra"][0] || MyService["Other"][0] || MyService["IP"][0] )
		return false
	else
		return true;
}

function GetCountryList(){
	return [{text:"ایران",value:"IRN",selected:true},{text:"اتریش",value:"AUT"},{text:"اتیوپی",value:"ETH"},{text:"اردن",value:"JOR"},{text:"ارمنستان",value:"ARM"},{text:"اروگوئه",value:"URY"},{text:"اریتره",value:"ERI"},{text:"ازبکستان",value:"UZB"},{text:"اسپانیا",value:"ESP"},{text:"استرالیا",value:"AUS"},{text:"استونی",value:"EST"},{text:"اسلواکی",value:"SVK"},{text:"اسلوونی",value:"SVN"},{text:"افغانستان",value:"AFG"},{text:"اکوادور",value:"ECU"},{text:"الجزایر",value:"DZA"},{text:"السالوادور",value:"SLV"},{text:"امارات متحده عربی",value:"ARE"},{text:"اندونزی",value:"IDN"},{text:"اوکراین",value:"UKR"},{text:"اوگاندا",value:"UGA"},{text:"ایالات فدرال میکرونزی",value:"FSM"},{text:"ایالات متحده آمریکا",value:"USA"},{text:"ایتالیا",value:"ITA"},{text:"ایسلند",value:"ISL"},{text:"آرژانتین",value:"ARG"},{text:"آروبا",value:"ABW"},{text:"آفریقای جنوبی",value:"ZAF"},{text:"آلبانی",value:"ALB"},{text:"آلمان",value:"DEU"},{text:"آنتیگوا و باربودا",value:"ATG"},{text:"آنتیل هلند",value:"ANT"},{text:"آندورا",value:"AND"},{text:"آنگولا",value:"AGO"},{text:"آنگویلا",value:"AIA"},{text:"باربادوس",value:"BRB"},{text:"باهاما",value:"BHS"},{text:"بحرین",value:"BHR"},{text:"برزیل",value:"BRA"},{text:"برمودا",value:"BMU"},{text:"برونئی",value:"BRN"},{text:"بریتانیا",value:"GBR"},{text:"بلاروس",value:"BLR"},{text:"بلژیک",value:"BEL"},{text:"بلغارستان",value:"BGR"},{text:"بلیز",value:"BLZ"},{text:"بنگلادش",value:"BGD"},{text:"بنین",value:"BEN"},{text:"بوتسوانا",value:"BWA"},{text:"بورکینافاسو",value:"BFA"},{text:"بوروندی",value:"BDI"},{text:"بوسنی و هرزگوین",value:"BIH"},{text:"بولیوی",value:"BOL"},{text:"پاپوآ گینه نو",value:"PNG"},{text:"پادشاهی بوتان",value:"BTN"},{text:"پاراگوئه",value:"PRY"},{text:"پاکستان",value:"PAK"},{text:"پالائو",value:"PLW"},{text:"پاناما",value:"PAN"},{text:"پرتغال",value:"PRT"},{text:"پرو",value:"PER"},{text:"پورتوریکو",value:"PRI"},{text:"پولی‌نزی فرانسه",value:"PYF"},{text:"تاجیکستان",value:"TJK"},{text:"تانزانیا",value:"TZA"},{text:"تایلند",value:"THA"},{text:"ترکمنستان",value:"TKM"},{text:"ترکیه",value:"TUR"},{text:"ترینیداد و توباگو",value:"TTO"},{text:"توکلائو",value:"TKL"},{text:"توگو",value:"TGO"},{text:"تونس",value:"TUN"},{text:"تونگا",value:"TON"},{text:"تووالو",value:"TUV"},{text:"تیمور شرقی",value:"TLS"},{text:"جامائیکا",value:"JAM"},{text:"جبل طارق",value:"GIB"},{text:"جرسی",value:"JEY"},{text:"جزایر آلند",value:"ALA"},{text:"جزایر پیت‌کرن",value:"PCN"},{text:"جزایر تورکس و کایکوس",value:"TCA"},{text:"جزایر سلیمان",value:"SLB"},{text:"جزایر فارو",value:"FRO"},{text:"جزایر فالکند",value:"FLK"},{text:"جزایر کوچک حاشیه‌ای ایالات متحده",value:"UMI"},{text:"جزایر کوک",value:"COK"},{text:"جزایر کوکوس",value:"CCK"},{text:"جزایر کیمن",value:"CYM"},{text:"جزایر مارشال",value:"MHL"},{text:"جزایر ماریانای شمالی",value:"MNP"},{text:"جزایر ویرجین انگلستان",value:"VGB"},{text:"جزایر ویرجین ایالات متحده",value:"VIR"},{text:"جزیره بووه",value:"BVT"},{text:"جزیره کریسمس",value:"CXR"},{text:"جزیره گوادلوپ",value:"GLP"},{text:"جزیره من",value:"IMN"},{text:"جزیره نورفولک",value:"NFK"},{text:"جزیره هرد و جزایر مک‌دونالد",value:"HMD"},{text:"جمهوری ایرلند",value:"IRL"},{text:"جمهوری آذربایجان",value:"AZE"},{text:"جمهوری آفریقای مرکزی",value:"CAF"},{text:"جمهوری چک",value:"CZE"},{text:"جمهوری دموکراتیک کنگو",value:"COD"},{text:"جمهوری دومینیکن",value:"DOM"},{text:"جمهوری کنگو",value:"COG"},{text:"جنوبگان",value:"ATA"},{text:"جورجیای جنوبی و جزایر ساندویچ جنوبی",value:"SGS"},{text:"جیبوتی",value:"DJI"},{text:"چاد",value:"TCD"},{text:"چین",value:"CHN"},{text:"دانمارک",value:"DNK"},{text:"دومینیکا",value:"DMA"},{text:"رواندا",value:"RWA"},{text:"روسیه",value:"RUS"},{text:"رومانی",value:"ROU"},{text:"رئونیون",value:"REU"},{text:"زامبیا",value:"ZMB"},{text:"زیمبابوه",value:"ZWE"},{text:"ژاپن",value:"JPN"},{text:"ساحل عاج",value:"CIV"},{text:"ساموای آمریکا",value:"ASM"},{text:"ساموآ",value:"WSM"},{text:"سائوتومه و پرنسیپ",value:"STP"},{text:"سرزمین‌های قطب جنوب و جنوبی فرانسه",value:"ATF"},{text:"سری‌لانکا",value:"LKA"},{text:"سن مارینو",value:"SMR"},{text:"سنت بارثلمی",value:"BLM"},{text:"سنت پیر و ماژلان",value:"SPM"},{text:"سنت کیتس و نویس",value:"KNA"},{text:"سنت لوسیا",value:"LCA"},{text:"سنت مارتین",value:"MAF"},{text:"سنت وینسنت و گرنادین‌ها",value:"VCT"},{text:"سنگاپور",value:"SGP"},{text:"سنگال",value:"SEN"},{text:"سوازیلند",value:"SWZ"},{text:"سوالبارد و یان ماین",value:"SJM"},{text:"سودان",value:"SDN"},{text:"سورینام",value:"SUR"},{text:"سوریه",value:"SYR"},{text:"سومالی",value:"SOM"},{text:"سوئد",value:"SWE"},{text:"سوئیس",value:"CHE"},{text:"سیرالئون",value:"SLE"},{text:"سیشل",value:"SYC"},{text:"سینت هلینا",value:"SHN"},{text:"شیلی",value:"CHL"},{text:"صحرای غربی",value:"ESH"},{text:"صربستان",value:"SRB"},{text:"عراق",value:"IRQ"},{text:"عربستان سعودی",value:"SAU"},{text:"عمان",value:"OMN"},{text:"غنا",value:"GHA"},{text:"فرانسه",value:"FRA"},{text:"فلسطین",value:"PSE"},{text:"فنلاند",value:"FIN"},{text:"فیجی",value:"FJI"},{text:"فیلیپین",value:"PHL"},{text:"قبرس",value:"CYP"},{text:"قرقیزستان",value:"KGZ"},{text:"قزاقستان",value:"KAZ"},{text:"قطر",value:"QAT"},{text:"قلمرو اقیانوس هند بریتانیا",value:"IOT"},{text:"کاستاریکا",value:"CRI"},{text:"کالدونیای جدید",value:"NCL"},{text:"کامبوج",value:"KHM"},{text:"کامرون",value:"CMR"},{text:"کانادا",value:"CAN"},{text:"کره جنوبی",value:"KOR"},{text:"کره شمالی",value:"PRK"},{text:"کرواسی",value:"HRV"},{text:"کلمبیا",value:"COL"},{text:"کنیا",value:"KEN"},{text:"کوبا",value:"CUB"},{text:"کومور",value:"COM"},{text:"کویت",value:"KWT"},{text:"کیپ ورد",value:"CPV"},{text:"کیریباتی",value:"KIR"},{text:"گابون",value:"GAB"},{text:"گامبیا",value:"GMB"},{text:"گرجستان",value:"GEO"},{text:"گرنادا",value:"GRD"},{text:"گرنزی",value:"GGY"},{text:"گرینلند",value:"GRL"},{text:"گواتمالا",value:"GTM"},{text:"گوآم",value:"GUM"},{text:"گویان",value:"GUY"},{text:"گویان فرانسه",value:"GUF"},{text:"گینه",value:"GIN"},{text:"گینه استوایی",value:"GNQ"},{text:"گینه بیسائو",value:"GNB"},{text:"لائوس",value:"LAO"},{text:"لبنان",value:"LBN"},{text:"لتونی",value:"LVA"},{text:"لسوتو",value:"LSO"},{text:"لهستان",value:"POL"},{text:"لوکزامبورگ",value:"LUX"},{text:"لیبریا",value:"LBR"},{text:"لیتوانی",value:"LTU"},{text:"لیختن‌اشتاین",value:"LIE"},{text:"ماداگاسکار",value:"MDG"},{text:"مارتینیک",value:"MTQ"},{text:"ماکائو",value:"MAC"},{text:"مالاوی",value:"MWI"},{text:"مالت",value:"MLT"},{text:"مالدیو",value:"MDV"},{text:"مالزی",value:"MYS"},{text:"مالی",value:"MLI"},{text:"مایوت",value:"MYT"},{text:"مجارستان",value:"HUN"},{text:"مراکش",value:"MAR"},{text:"مصر",value:"EGY"},{text:"مغولستان",value:"MNG"},{text:"مقدونیه",value:"MKD"},{text:"مکزیک",value:"MEX"},{text:"موریتانی",value:"MRT"},{text:"موریس",value:"MUS"},{text:"موزامبیک",value:"MOZ"},{text:"مولداوی",value:"MDA"},{text:"موناکو",value:"MCO"},{text:"مونتسرات",value:"MSR"},{text:"مونته‌نگرو",value:"MNE"},{text:"میانمار",value:"MMR"},{text:"نامیبیا",value:"NAM"},{text:"نائورو",value:"NRU"},{text:"نپال",value:"NPL"},{text:"نروژ",value:"NOR"},{text:"نیجر",value:"NER"},{text:"نیجریه",value:"NGA"},{text:"نیکاراگوئه",value:"NIC"},{text:"نیوزیلند",value:"NZL"},{text:"نیووی",value:"NIU"},{text:"هائیتی",value:"HTI"},{text:"هلند",value:"NLD"},{text:"هند",value:"IND"},{text:"هندوراس",value:"HND"},{text:"هنگ کنگ",value:"HKG"},{text:"واتیکان",value:"VAT"},{text:"والیس و فوتونا",value:"WLF"},{text:"وانواتو",value:"VUT"},{text:"ونزوئلا",value:"VEN"},{text:"ویتنام",value:"VNM"},{text:"یمن",value:"YEM"},{text:"یونان",value:"GRC"}];
}
	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
