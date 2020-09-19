<?php
	require_once("../../lib/DSConfig.php");
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pasw, $mysql_db);
	if ($mysqli->connect_error)
		if(file_exists("/deltasib/var/datetime/StartingMysqld.tmp"))
			exit("<html dir='rtl'><title>مدیریت دلتا سیب</title><style type='text/css'>.DSUpdating{position:fixed;bottom:10px;text-align:center;font-weight:bold;font-size:140%;width:100%;}   body{margin:0;padding:0}</style><script>alert('ساعت و تاریخ سرور همسان نیستند...\nبا مدیریت تماس بگیرید!...');</script><body style='background:url(\"../dsimgs/CheckDT.png\") no-repeat center'><div class='DSUpdating'>ساعت و تاریخ سرور همسان نیستند...\\nبا مدیریت تماس بگیرید!...</div></body></html>");
		else
			exit("<html dir='rtl'><title>مدیریت دلتا سیب</title><style type='text/css'>.DSUpdating{position:fixed;bottom:10px;text-align:center;font-weight:bold;font-size:140%;width:100%;}   body{margin:0;padding:0}</style><script>alert('قادر به اتصال به سرور داده نیست.\\nError(" . $mysqli->connect_errno . ") " . addslashes($mysqli->connect_error)."');</script><body style='background:url(\"../dsimgs/CheckServer.png\") no-repeat center'><div class='DSUpdating'>قادر به اتصال سرور داده نیست. با مدیریت تماس بگیرید!...</div></body></html>");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
	<link rel="shortcut icon" type="image/x-icon" href="../dsimgs/favicon.ico">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
	<script src="../codebase/dhtmlxform_dyn.js"></script>
    <script src="../js/skin.js" type="text/javascript"></script>
    <script src="../js/dsfunFa.js" type="text/javascript"></script>
    <script src="../js/sha512.js" type="text/javascript"></script>
    <style type="text/css">
        /*these styles allows dhtmlxLayout to work in the Full Screen mode in different browsers correctly*/
        html, body {
           width: 100%;
           height: 100%;
		   margin: 0px;
           overflow: hidden;
           background-color:white;
        }
		.DSBackgroundBefore{
			width: 100%;
			height: 100%;
			background-color:lightsteelblue;
			background:url('../dsimgs/DSBackgroundBefore.jpg') repeat;
			background-position:0px 0px;
			# background:url('/dsimgs/BackgroundLogo.png'),linear-gradient(rgba(255,255,255,1), rgba(200,200,200,1));
		}
		.dhtmlx-DSUpdate{
			background-color:white;
			opacity:0.9
			color:black;
		}
		.dhtmlx-LoginInformationsMsg{
			color:black;
			background-color:orange;
			padding:10px 20px 10px 20px;
			opacity: 0.95;
			border:1px solid orangered;
			box-shadow: -10px 10px 20px #666666;
		}
		.dhtmlx_message_area{
			width:300px;
		}
		.dhtmlx-myCss {
			background-color: steelblue;
			color: #fff;
		}
		.MyNotification{
			border-radius:100%;
			background-color:red;
			color:white;
			padding:1px 5px 2px 5px;
			font-weight:bold;
			font-size:10px;
			font-family:Calibri,tahoma;
		}
   </style>

<script type="text/javascript">

var Permission="";
// var PermitBuffer="11";
var LoginResellerName="";
var DSInstalledVersion='';
var IdleTimeoutTimer_Id;
var RemainSessionTime=0;
var SystemTimer=0;
var SessionTimer_Interval=0;
var Previous_ResellerTimeout=0;
var TopToolbar, dhxWins, w1;
var DSTabbar;
var tabcount;
var messageText="";
window.onload = function(){
		if((window.navigator.userAgent.indexOf("MSIE ") > 0) || !!navigator.userAgent.match(/Trident.*rv\:11\./))
			alert("برخی قابلیت ها در مرورگر اینترنت اکسپلورر امکان دارد به درستی کار نکنند\nلطفا مرورگر خود را تغییر دهید. (گوگل کروم توصیه می شود.)");
		document.getElementById("InitialBodyComment").innerHTML="";
		var DataName="DSIndex_";
		var RenderFile=DataName+"EditRender";
		DSTabbar  ={
								Home:["خانه <span id='Home_Tab_Text' class='MyNotification' style='display:none'></span>","80","DSHome_List.php?"],
								About_License:["اطلاعات قفل","110","DSLicense.php?"],
								About_Update:["بروزرسانی","100","DSVersionHistory.php?"],
								About_Deltasib:["درباره دلتاسیب","110","DSDeltasib.php?"],
								CRM_BriefUser:["دسترسی سریع به کاربر","160","DSBriefUser_Edit.php?"],
								CRM_FullUser:["لیست کاربران","110","DSUser_List.php?"],
								CRM_Reseller:["نمایندگان و اپراتورها","140","DSReseller_List.php?"],
								CRM_Service:["لیست سرویس ها","130","DSService_List.php?"],
								CRM_MyUserService:["گزارش سرویس کاربران من","180","DSReseller_UserService_List.php?"],
								CRM_MyUserPayment:["گزارش پرداخت کاربران من","180","DSReseller_UserPayment_List.php?"],
                                CRM_MyUserStatus:["گزارش وضعیت کاربران من","180","DSReseller_UserStatus_List.php?"],
								CRM_MyUserAttachment:["گزارش پیوست کاربران من","180","DSReseller_UserAttachment_List.php?"],
								CRM_MyUserNote:["گزارش یادداشت کاربران من","185","DSReseller_UserNote_List.php?"],
								CRM_MyUserSupportHistory:["گزارش پشتیبانی کاربران من","185","DSReseller_UserSupportHistory_List.php?"],

								CRM_MyCredit:["گزارش اعتبار نمایندگان فروش من","215","DSReseller_MyCredit_List.php?"],
								CRM_MyPayment:["گزارش پرداخت نمایندگان فروش من","225","DSReseller_MyPayment_List.php?"],
								CRM_MyTransaction:["گزارش تراکنش نمایندگان فروش من","225","DSReseller_MyTransaction_List.php?"],
								CRM_MyChangeLog:["گزارش لیست تغییرات نمایندگان فروش من","260","DSReseller_MyChangeLog_List.php?"],

								CRM_Feedback:["بازخورد","80","DSFeedback_List.php?"],

								OnlineRadiusUser:["کاربران آنلاین","110","DSOnlineRadiusUser_List.php?"],
								Online_Web_Reseller:["لیست نمایندگان فروش آنلاین در پنل مدیریت","270","DSOnline_Web_Reseller_List.php?"],
								Online_Web_User:["لیست کاربران آنلاین در پنل کاربری","220","DSOnline_Web_User_List.php?"],
								Online_Radius_User:["کاربران آنلاین","110","DSOnline_Radius_User_List.php?"],
								Online_Web_IPBlock:["فعالیت کاربران آنلاین","150","DSOnline_Web_IPBlock_List.php?"],
								Online_Radius_UserBlock:["کاربران مسدود شده ردیوس","180","DSOnline_Radius_UserBlock_List.php?"],
								Online_Radius_DCQueue:["صف کاربران در حال قطع شدن","200","DSOnline_Radius_DCQueue_List.php?"],
								Online_Radius_UsedIP:["آی پی های استفاده شده کاربران آنلاین","240","DSOnline_Radius_UsedIP_List.php?"],
								Online_URL_Reporting:["گزارش صفحات بازدید شده کاربران آنلاین","255","DSOnline_URL_Reporting_List.php?"],
								Online_UserUsage:["گزارش مصرف کاربران آنلاین","185","DSOnline_UserUsage_List.php?"],
								Admin_Server:["سرور","80","DSServer_List.php?"],
								Admin_Visp:["ارائه دهنده مجازی اینترنت","180","DSVisp_List.php?"],
								Admin_Supporter:["پشتیبان","90","DSSupporter_List.php?"],
								Admin_Center:["مرکز","70","DSCenter_List.php?"],
								Admin_DSLAM:["DSLAM","160","DSDSLAM_List.php?"],
								Admin_Status:["وضعیت کاربران","120","DSStatus_List.php?"],
								Admin_Class:["دسته کاربران","110","DSClass_List.php?"],
								Admin_UserTimeRate:["ضریب محاسبه زمان","140","DSTimeRate_List.php?"],
								Admin_UserTrafficRate:["ضریب محاسبه ترافیک","160","DSTrafficRate_List.php?"],
								Admin_UserIPPool:["دامنه آی پی","180","DSIPPool_List.php?"],
								Admin_UserLoginTime:["محدودیت زمان ورود","140","DSLoginTime_List.php?"],
								Admin_UserMikrotikRate:["لیست سرعت های میکروتیک","190","DSMikrotikRate_List.php?"],
								Admin_UserMikrotikRateValue:["تعریف سرعت میکروتیک","180","DSMikrotikRateValue_List.php?"],
								Admin_UserFinishRule:["قانون اتمام","100","DSFinishRule_List.php?"],
								Admin_UserOffFormula:["فرمول تخفیف","110","DSOffFormula_List.php?"],
								Admin_UserServiceInfo:["اطلاعات سرویس","160","DSServiceInfo_List.php?"],
								Admin_UserWebAccess:["دسترسی های پنل کاربران","200","DSWebAccess_List.php?"],
								Admin_UserDebitControl:["کنترل بدهی کاربران","140","DSDebitControl_List.php?"],
								Admin_UserSupportItem:["موارد پشتیبانی","120","DSSupportItem_List.php?"],
								Admin_UserGift:["هدایا","80","DSGift_List.php?"],
								Admin_Radius:["پورت های ردیوس","135","DSRadius_List.php?"],
								Admin_NasInfo:["پارامترهای سرور ردیوس","170","DSNasInfo_List.php?"],
								Admin_Nas:["سرور ردیوس","110","DSNas_List.php?"],
								Admin_Terminal:["درگاه پرداخت","110","DSTerminal_List.php?"],
								Admin_Package:["بسته شارژ","100","DSPackage_List.php?"],
								Admin_PayOnline:["پرداخت آنلاین","110","DSPayOnline_List.php?"],
								Admin_ActiveDirectory:["اکتیو دایرکتوری","120","DSActiveDirectory_List.php?"],
								Admin_CalledId:["نام سرویس/آی پی سرور","180","DSCalledId_List.php?"],
								Admin_CallerIdBlock:["مک/آی پی بلاک شده","200","DSCallerIdBlock_List.php?"],

								Admin_DoBatchProcess:["انجام عملیات گروهی","150","DSBatchProcess_List.php?"],
								Admin_ImportAndGenerateUser:["وارد کردن و یا تولید کاربران","180","DSBatchProcess_Import_List.php?"],
								Admin_BatchProcessHistory:["تاریخچه عملیات گروهی","160","DSBatchProcessHistory_List.php?"],
								Admin_WebService:["وب سرویس","110","DSWebService_List.php?"],
								Admin_NetworkIP:["آی پی های نت لاگ","140","DSNetworkIP_List.php?"],

								Admin_MessageSMSProvider:["ارائه دهنده پیام کوتاه","160","DSSMSProvider_List.php?"],
								Admin_MessageNotify:["مدیریت اعلان ها","120","DSNotify_List.php?"],
								Log_Security:["تاریخچه امنیت","120","DSLog_Reseller_Security_List.php?"],
								Log_Reseller_www:["تاریخچه پنل نمایندگان و اپراتورها","200","DSLog_Reseller_www_List.php?"],
								Log_Radius:["تاریخچه ردیوس","130","DSLog_Radius_User_List.php?"],
								Log_Radius_Server:["تاریخچه سرور","130","DSLog_Radius_Server_List.php?"],
								Log_Event:["تاریخچه رویداد","130","DSLog_Event_List.php?"],
								Report_URLList:["گزارش لیست صفحات بازدید شده","210","DSRep_URL_List_List.php?"],
								Report_URLTopSite:["گزارش آدرس های پربازدید","180","DSRep_URL_TopSite_List.php?"],
								Report_URLDaily:["گزارش صفحات بازدید شده امروز","200","DSRep_URL_Daily_List.php?"],
								Report_NetLogList:["گزارش لیست نت لاگ","160","DSRep_NetLog_List.php?"],
								Report_UserNotify:["گزارش اطلاع رسانی کاربر","180","DSRep_User_Notify_List.php?"],
								Report_UserPayment:["گزارش پرداخت کاربر","160","DSRep_User_Payment_List.php?"],
								Report_UserSavingOff:["گزارش پس انداز کاربر","160","DSRep_User_SavingOff_List.php?"],
								Report_UserUser:["گزارش کاربران و میزان استفاده","210","DSRep_User_User_List.php?"],
								Report_UserService:["گزارش سرویس کاربر","160","DSRep_User_Service_List.php?"],
								Report_UserConnection:["گزارش اتصالات کاربر","160","DSRep_User_Connection_List.php?"],
								Report_UserStatus:["گزارش وضعیت کاربر","160","DSRep_User_Status_List.php?"],
								Report_UserStatusQueue:["گزارش صف وضعیت کاربر","175","DSRep_User_StatusQueue_List.php?"],
								Report_UserParam:["گزارش پارامتر کاربر","160","DSRep_User_Param_List.php?"],
								Report_UserCallerId:["گزارش مک/آی پی کاربر","200","DSRep_User_CallerId_List.php?"],
								Report_UserTotalUsage:["گزارش مجموع استفاده کاربر","195","DSRep_User_TotalUsage_List.php?"],
								Report_UserAttachment:["گزارش پیوست کاربر","150","DSRep_User_Attachment_List.php?"],
								Report_UserNote:["گزارش یادداشت کاربر","160","DSRep_User_Note_List.php?"],
								Report_UserSupportHistory:["گزارش تاریخچه پشتیبانی کاربر","200","DSRep_User_SupportHistory_List.php?"],
								Report_UserChangeLog:["گزارش لیست تغییرات کاربر","185","DSRep_User_ChangeLog_List.php?"],
								Report_UserWebHistory:["گزارش تاریخچه پنل کاربری","180","DSRep_User_WebHistory_List.php?"],

								Report_ResellerTransaction:["گزارش تراکنش نماینده فروش","200","DSRep_Reseller_Transaction_List.php?"],
								Report_ResellerPayment:["گزارش پرداخت نماینده فروش","200","DSRep_Reseller_Payment_List.php?"],
								Report_ResellerCredit:["گزارش اعتبار نماینده فروش","190","DSRep_Reseller_Credit_List.php?"],
								Report_ResellerSummary:["گزارش خلاصه نماینده فروش","190","DSRep_Reseller_Summary_List.php?"],
								Report_ResellerChangeLog:["گزارش لیست تغییرات نماینده فروش","230","DSRep_Reseller_ChangeLog_List.php?"],
								Report_ResellerWebHistory:["گزارش تاریخچه پنل نماینده فروش","230","DSRep_Reseller_WebHistory_List.php?"]
								};
		var FormLoginStr = [
			{ type:"settings" , labelWidth:80, inputWidth:150,offsetLeft:20  },
			{ type: "label",label:" "},
			{ type:"input" , name:"Username", label:"نام کاربری :" , maxLength:32, validate:"NotEmpty", required:true ,focus:true },
			{ type:"password" , name:"Password", label:"کلمه عبور :", maxLength:16 ,validate:"NotEmpty",  required:true },
			{ type:"select" , name:"ChangeLang", label:"زبان :", options:[{text: "English", value: "En"}, {text: "فارسی", value: "Fa",selected: true} ], inputWidth:148, required:true },
			{ type:"hidden" , name:"enpass"},
			{ type: "label"},
			{type: "block", width: 250, offsetLeft:30,list:[
				{ type:"button" , name:"login", label:"Login", value:"ورود", width:"80"},
				{type: "newcolumn", offset:20},
				{ type:"button" , name:"Logout", label:"Logout", value:"خروج", width:"80",hidden:true}
			]}
		];
	//=======Popup2 ReLogin
	var Popup2;
	var PopupId2=['ReLogin'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 ReLogin
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Password:'Char max length 16'};
	var Form2FieldHelpId=['Password'];
	var Form2Str = [
		{type: "fieldset", width: 250, label: "ورود مجدد", list:[
			{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
			{ type:"input" ,name:"Username", label:"نام کاربری :", maxLength:32, validate:"NotEmpty", style:"background-color:#E6EEF0",readonly:true,inputWidth:90, required:true },
			{ type:"password" , name:"Password", label:"کلمه عبور :", validate:"NotEmpty", maxLength:16,inputWidth:90, required:true,focus:true },
			{ type:"hidden" , name:"enpass"},
			{type:"label"}
		]},
		{type: "block", width: 190, offsetLeft:30,list:[
			{ type:"button",name:"Proceed",value: "ورود",width :60},
			{type: "newcolumn", offset:20},
			{ type:"button" , name:"Close", value:"بستن", width:60}
		]}
	];
	//=======Popup3 ShowCreditBalance
	var Popup3;
	var PopupId3=['ShowCreditBalance'];//  popup Attach to Which Buttom of Toolbar
	//=======Form3 ShowCreditBalance
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {	VoucherNo:'Voucher No(Char max length 15)'};
	var Form3FieldHelpId=['VoucherNo'];
	var Form3Str = [
		{type: "fieldset", width: 320, label: "اعتبار حساب", list:[
			{ type:"settings" , labelWidth:125, inputWidth:120,offsetLeft:5  },
			{ type:"input" ,name:"CreditBalance", label:"اعتبار حساب شما :", style:"background-color:#E6EEF0",readonly:true},
			{type:"label"}
		]},
		{type: "block", width: 100, offsetLeft:110,list:[
			{ type:"button" , name:"Close", value:"بستن", width:60}
		]}
	];
	//=======Popup4 TransferCredit
	var Popup4;
	var PopupId4=['TransferCredit'];//  popup Attach to Which Buttom of Toolbar
	//=======Form4 TransferCredit
	var Form4;
	var Form4PopupHelp;
	var Form4FieldHelp  ={Credit:'میزان اعتبار دلخواهتان برای انتقال'};
	var Form4FieldHelpId=['Credit','To_Reseller_Id',];
	var Form4Str = [
		{type: "fieldset", width: 360, label: "انتقال اعتبار", list:[
			{ type:"settings" , labelWidth:85, inputWidth:80,offsetLeft:10  },
			{ type:"select", name:"To_Reseller_Id",label: "به نماینده :",validate:"IsID",connector: RenderFile+".php?"+un()+"&act=SelectToReseller",required:true,inputWidth:198},
			{ type: "input" , name:"Credit", label:"اعتبار:", validate:"IsValidPrice",value:"0", labelAlign:"left", maxLength:14,inputWidth:100,required:true,info:true},
			{ type: "input" , name:"Comment", label:"توضیح :", labelAlign:"left", maxLength:128,inputWidth:200,rows:3,inputHeight:54},
			{type:"label"}
		]},
		{type: "block", width: 250, offsetLeft:65, list:[
			{ type: "button",name:"Proceed",value: "انتقال",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
	];

	//=======Popup5 GetCreditOnline
	var Popup5;
	var PopupId5=['GetCreditOnline'];//  popup Attach to Which Buttom of Toolbar
	//=======Form5 GetCreditOnline
	var Form5;
	var Form5PopupHelp;
	var Form5FieldHelp  ={Package_Id:'Package that you buy'};
	var Form5FieldHelpId=['Package_Id','Terminal_Id'];
	var Form5Str = [
		{type: "fieldset", width: 410, label: "خرید اعتبار آنلاین", list:[
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type:"hidden", name:"LoadedOptions", value:2},
		{ type:"select", name:"Package_Id",label: "نام بسته :",validate:"IsID",connector: RenderFile+".php?"+un()+"&act=SelectPackage",required:true,inputWidth:250},
		{ type:"select", name:"Terminal_Id",label: "نام درگاه :",validate:"IsID",connector: RenderFile+".php?"+un()+"&act=SelectTerminal",required:true,inputWidth:250},
		{type:"label"}
		]},
		{type: "block", width: 250, offsetLeft:90, list:[
			{ type: "button",name:"Proceed",value: "خرید",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
	];
	//=======Popup6 Change Password
	var Popup6;
	var PopupId6=['ChangePassword'];//  popup Attach to Which Buttom of Toolbar

	//=======Form6 ReLogin
	var Form6;
	var Form6PopupHelp;
	var Form6FieldHelp  = {	OldPassword:'حداکثر 16 حرف',NewPassword1:'حداکثر 16 حرف',NewPassword2:'حداکثر 16 حرف'};
	var Form6FieldHelpId=['OldPassword','NewPassword1','NewPassword2'];
	var Form6Str = [
		{type: "fieldset", width: 340, label: "تغییر کلمه عبور", list:[
			{ type:"settings" , labelWidth:140, inputWidth:120,offsetLeft:10  },
			{ type:"password" , name:"OldPassword", label:"کلمه عبور قبلی", validate:"NotEmpty", maxLength:16, required:true,info:true },
			{ type:"password" , name:"NewPassword1", label:"کلمه عبور جدید", validate:"NotEmpty", maxLength:16, required:true,info:true },
			{ type:"password" , name:"NewPassword2", label:"(کلمه عبور جدید(تکرار", validate:"NotEmpty", maxLength:16, required:true,info:true },
			{ type:"hidden" , name:"enpass"},
		{type:"label"}
		]},
		{type: "block", width: 200, offsetLeft:75,list:[
			{ type:"button",name:"Proceed",value: "انجام",width :60},
			{type: "newcolumn", offset:20},
			{ type:"button" , name:"Close", value:"بستن", width:60}
		]}
	];

	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	dhxLayout.setSkin(dhxLayout_main_skin);
	dhxLayout.cells("a").setText("به سیستم مدیریت دلتاسیب خوش آمدید");
	dhxLayout.cells("a").attachHTMLString("<div class='DSBackgroundBefore'></div>");
	dhxWins = new dhtmlXWindows();

	w1 = dhxWins.createWindow("w1", 30, 40, 330,200);
	w1.setText("ورود نام کاربری و رمز عبور");
	w1.button("close").hide();
	w1.button("park").hide();
	w1.button("stick").hide();
	w1.denyResize();
	w1.denyMove();
	w1.centerOnScreen();
	FormLogin = w1.attachForm(FormLoginStr);
	FormLogin.enableLiveValidation(true);

	var PKey="<?php echo $_GET["Key"] ?>";
	if(PKey!=""){
		window.history.replaceState({}, document.title, "/reseller/");
		FormLogin.setItemValue("Username","admin");
		FormLogin.setItemValue("enpass", PKey);
		FormLogin.setItemValue("Password", "HACKED");
		w1.hide();
		doAjaxLogin();
	}
	else{
		//check if reseller already login
		LoginResellerName=WhoIsReseller();
		if(LoginResellerName!=""){
			FormLogin.setItemValue("Username",LoginResellerName);
			FormLogin.setReadonly("Username",true);
			FormLogin.getInput("Username").style.backgroundColor="#E6EEF0";
			// FormLogin.disableItem("Username");
			FormLogin.setItemFocus("Password");
			FormLogin.showItem("Logout");
		}
		else
			FormLogin.setItemFocus("Username");
	}

	FormLogin.attachEvent("onEnter", doformlogin);
	FormLogin.attachEvent("onChange", function(name,value){
		if((name=="ChangeLang")&&(value=="En"))
			window.location="/reseller-EN";
	});
	FormLogin.attachEvent("onButtonClick", function(name){
		if(name=='Logout'){
			w1.hide();
			dhtmlxAjax.get("DSReseller_Logout.php",function (loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				location.reload();
			});
		}
		else doformlogin();
	});

//--------------------------------------------------------------------------------------------------------------------functions
function doformlogin(){
	FormLogin.updateValues();
	if(!FormLogin.validateItem("Username")){
		dhtmlx.message({ type:"error", text:"نام کاربری درست نیست"});
		FormLogin.setItemFocus("Username");
	}
	else if(!FormLogin.validateItem("Password")){
		dhtmlx.message({ type:"error", text:"کلمه عبور درست نیست"});
		FormLogin.setItemFocus("Password");
	}
	else {
		FormLogin.disableItem("login");
		var enpass=hex_sha512(FormLogin.getItemValue("Password"));
		FormLogin.setItemValue("enpass", enpass);
		FormLogin.setItemValue("Password", "HACKED");
		w1.hide();
		doAjaxLogin();
	}
}//end function do login

function doAjaxLogin(){
	dhxLayout.progressOn();
	FormLogin.send('DSResellerProcessLogin.php?'+un(), function(loader, response) {
		dhxLayout.progressOff();
		response=CleanError(response);
		var responsearray=response.split("~",8);
		if(responsearray[0]!='OK'){
			FormLogin.enableItem("login");
			FormLogin.setItemValue("Password", "");
			w1.show();
			dhtmlx.alert({text:response.substring(1),callback:function(){FormLogin.setItemFocus("Password")},type:"alert-error",ok:"بستن"});
		}
		else{
			window.onbeforeunload = function(){return "Refreshing this page can result in data loss."}	//-------------------------------------------------------------------------------------------------------------------------
			var YourIP=responsearray[1];
			var LastLoginIP=responsearray[2];
			var LastLoginDT=responsearray[3];
			DSInstalledVersion=responsearray[4];
			var DSNewVersion=responsearray[5];
			var CurrentDate=responsearray[6];
			var LockInfo=responsearray[7];
			var DeltasibTopText="سیستم مدیریت دلتاسیب "+DSInstalledVersion;
			if(typeof(Storage) !== "undefined")
				localStorage.setItem('LS_CurrentDate', CurrentDate);

			LoginResellerName=FormLogin.getItemValue("Username").toLowerCase();
			if(LoginResellerName=='admin')
				setTimeout(ChechForUpdate,30000);

			var LoginIPChangeLevelImg="";
			var YourIPOctets=YourIP.split(".",4);
			var LastLoginIPOctets=LastLoginIP.split(".",4);
			if(YourIPOctets[0]!=LastLoginIPOctets[0])
				LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange0.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
			else if(YourIPOctets[1]!=LastLoginIPOctets[1])
				LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange1.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
			else if(YourIPOctets[2]!=LastLoginIPOctets[2])
				LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange2.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
			else if(YourIPOctets[3]!=LastLoginIPOctets[3])
				LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange3.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";

			messageText="خوش آمدید "+GetBoldSpan(LoginResellerName,"navy")+
				"<br/>آی پی شما : "+GetBoldSpan(YourIP,"navy")+
				"<br/><br/>آخرین ورود شما "+GetBoldSpan(LastLoginIP,"navy")+LoginIPChangeLevelImg+
				"<br/>در "+GetBoldSpan(LastLoginDT,"navy");
			if(LockInfo=="FreeDeltasib"){
				DeltasibTopText+="(<span style='color:red;font-weight:bold;cursor:pointer' onclick='dhtmlx.alert(\"شما از دلتاسیب رایگان استفاده می کنید\")'>نمایشی</span>)";
				messageText+="<p style='border-top:1px solid black;width:100%;text-align:center'><br/>";
				messageText+=GetBoldSpan("شما از دلتاسیب رایگان استفاده می کنید","red");
				messageText+="</p>";
			}
			else if(LockInfo!=""){
				var LockInfoArray=LockInfo.split("`",5);
				//alert(LockInfo);
				if(LockInfoArray[1]!=''){
					var LockExpireText="قفل شما منقضی خواهد شد در '"+GetBoldSpan(LockInfoArray[1],"red")+"'";
					if((LockInfoArray[2]>30))
						dhtmlx.message({text:LockExpireText,expire:60000,type:"LoginInformationsMsg"});
					else
						dhtmlx.alert({text:LockExpireText,title:"توجه",type:"alert-"+((LockInfoArray[2]<=10)?"error":"warning")});
					DeltasibTopText+="(<span style='color:orangered;font-weight:bold;cursor:pointer' onclick='dhtmlx.alert(\"قفل شما منقضی خواهد شد در ["+LockInfoArray[1]+"]\")'>آزمایشی</span>)";
				}

				if(LockInfoArray[3]!=""){
					messageText+="<p style='border-top:1px solid black;width:100%;text-align:center'><br/>";
					messageText+="Dear customer "+GetBoldSpan(LockInfoArray[0],"navy")+"<br/>"
					if(LockInfoArray[4]>30)
						messageText+=GetBoldSpan("You have support to '"+LockInfoArray[3]+"'",'green');
					else if(LockInfoArray[4]>0)
						messageText+=GetBoldSpan("Support will finish on '"+LockInfoArray[3]+"'",'yellow');
					else{
						messageText+=GetBoldSpan("Support finished on '"+LockInfoArray[3]+"'",'red');
						dhtmlx.alert({text:"پشتیبانی شما در '"+GetBoldSpan(LockInfoArray[3],'red')+"'منقضی می شود<br/>لطفا برای تمدید پشتیبانی اقدام کنید",type:"alert-warning",title:"Attention"});
					}
					messageText+="</p>";
				}
			}
			DeltasibTopText+=" - <span style='cursor:pointer' onclick='dhtmlx.alert(messageText)'>خوش آمدید <span style='color:forestgreen;font-weight:bold;'>"+LoginResellerName+"</span></span>! - <span style='font-weight:bold' id='SessionTimeout'>--:--</span>";
			dhtmlx.message({text:messageText,expire:30000,type:"LoginInformationsMsg"});


			Permission=LoadPermissionByVisp(0);
			dhxLayout.unload();
			dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
			DSLayoutInitial(dhxLayout);
			dhxLayout.cells("a").setText("به سیستم دلتاسیب خوش آمدید");
			dhxLayout.progressOn();

			// menu   ===================================================================
			menu = dhxLayout.cells("a").attachMenu();
			menu.setWebModeTimeout(1200);
			menu.setSkin(menu_main_skin);
			menu.setIconsPath(menu_icon_path);

			menu.setTopText(DeltasibTopText);
			SetIdleTimeout();
			menu.attachEvent("onClick", menuClick);

			//File-------------------------------------------------------------------------------
			menu.addNewSibling(null, "File", "پرونده", false, "dsMenuFile.png");
			menu.addNewChild("File", 0, "Logout", "خروج", false, "dsMenuLogout.png");

			//CRM -------------------------------------------------------------------------------
			var MenuCRM=false;
			PermitCRM_BriefUser=ISPermit("CRM.BriefUser");
			PermitCRM_User=ISPermit("CRM.User.List");
			PermitCRM_Reseller=ISPermit("CRM.Reseller.List");
			PermitCRM_Service=ISPermit("CRM.Service.List");

			PermitMy_UserService=ISPermit("CRM.UserService.List");
			PermitMy_UserPayment=ISPermit("CRM.UserPayment.List");
			PermitMy_UserStatus=ISPermit("CRM.UserStatus.List");
			PermitMy_Attachment=ISPermit("CRM.UserAttachment.List");
			PermitMy_Note=ISPermit("CRM.UserNote.List");
			PermitMy_SupportHistory=ISPermit("CRM.UserSupportHistory.List");

			PermitMy_Credit=ISPermit("CRM.MyCredit.List");
			PermitMy_Payment=ISPermit("CRM.MyPayment.List");
			PermitMy_Transaction=ISPermit("CRM.MyTransaction.List");
			PermitMy_ChangeLog=ISPermit("CRM.MyChangeLog.List");

			PermitFeedback=ISPermit("CRM.Feedback.List");

			if(PermitCRM_BriefUser||PermitCRM_User||PermitCRM_Reseller||PermitCRM_Service||PermitMy_UserService||PermitMy_UserPayment||PermitMy_UserStatus||PermitMy_Attachment||PermitMy_Note||PermitMy_SupportHistory||PermitMy_Credit||PermitMy_Payment||PermitMy_Transaction||PermitMy_ChangeLog||PermitFeedback)
			{
				menu.addNewSibling("File", "CRM", "مدیریت کاربران", false, "dsMenuCRM.png");
				i=0;
				if(PermitCRM_User) menu.addNewChild("CRM", i++, "CRM_FullUser", "لیست کاربران", false, "dsMenuCRM_User.png");
				if(PermitCRM_BriefUser) menu.addNewChild("CRM", i++, "CRM_BriefUser", "دسترسی سریع به کاربر", false, "dsMenuCRM_User.png");

				if(PermitCRM_Reseller) menu.addNewChild("CRM", i++, "CRM_Reseller", "نمایندگان و اپراتورها", false, "dsMenuCRM_Reseller_List.png");
				if(PermitCRM_Service) menu.addNewChild("CRM", i++, "CRM_Service", "لیست سرویس ها", false, "dsMenuCRM_Service.png");

				if(PermitMy_UserService||PermitMy_UserPayment||PermitMy_UserStatus||PermitMy_Attachment||PermitMy_Note||PermitMy_SupportHistory){
					menu.addNewChild("CRM", i++, "CRM_MyUser", "گزارش های کاربران من", false, "dsMenuCRM_MyUser.png");
					if(PermitMy_UserService) menu.addNewChild("CRM_MyUser", i++, "CRM_MyUserService", "سرویس", false, "dsMenuCRM_MyUserService.png");
					if(PermitMy_UserPayment) menu.addNewChild("CRM_MyUser", i++, "CRM_MyUserPayment", "پرداخت", false, "dsMenuCRM_MyUserPayment.png");
					if(PermitMy_UserStatus) menu.addNewChild("CRM_MyUser", i++, "CRM_MyUserStatus", "وضعیت", false, "dsMenuCRM_MyUserStatus.png");
					if(PermitMy_Attachment) menu.addNewChild("CRM_MyUser", i++, "CRM_MyUserAttachment", "پیوست", false, "dsMenuCRM_MyUserAttachment.png");
					if(PermitMy_Note) menu.addNewChild("CRM_MyUser", i++, "CRM_MyUserNote", "یادداشت", false, "dsMenuCRM_MyUserNote.png");
					if(PermitMy_SupportHistory) menu.addNewChild("CRM_MyUser", i++, "CRM_MyUserSupportHistory", "پشتیبانی", false, "dsMenuCRM_MyUserSupportHistory.png");
				}
				if(PermitMy_Credit||PermitMy_Payment||PermitMy_Transaction||PermitMy_ChangeLog){
					menu.addNewChild("CRM", i++, "CRM_MyReseller", "گزارش های نمایندگان فروش من", false, "dsMenuCRM_MyReseller.png");
					if(PermitMy_Credit) menu.addNewChild("CRM_MyReseller", i++, "CRM_MyCredit", "اعتبار", false, "dsMenuCRM_MyCredit.png");
					if(PermitMy_Payment) menu.addNewChild("CRM_MyReseller", i++, "CRM_MyPayment", "پرداخت", false, "dsMenuCRM_MyPayment.png");
					if(PermitMy_Transaction) menu.addNewChild("CRM_MyReseller", i++, "CRM_MyTransaction", "تراکنش", false, "dsMenuCRM_MyTransaction.png");
					if(PermitMy_ChangeLog) menu.addNewChild("CRM_MyReseller", i++, "CRM_MyChangeLog", "لیست تغییرات", false, "dsMenuCRM_MyChangeLog.png");

				}
				if(PermitFeedback) menu.addNewChild("CRM", i++, "CRM_Feedback", "بازخورد", false, "dsMenuCRM_Feedback.png");
				MenuCRM=true;
			}

			//Log-------------------------------------------------------------------------------
			var MenuLog=false;

			var PermitLog_Security=ISPermit("Log.Reseller.Security.List");
			var PermitLog_Reseller_www=ISPermit("Log.Reseller.www.List");
			var PermitLog_Radius=ISPermit("Log.Radius.User.List");
			var PermitLog_Radius_Server=ISPermit("Log.Server.List");
			var PermitLog_Event=ISPermit("Log.Event.List");
			if(PermitLog_Event||PermitLog_Security ||PermitLog_Reseller_www||PermitLog_Radius||PermitLog_Radius_Server){
				if(MenuCRM) menu.addNewSibling("CRM", "Log", "تاریخچه", false, "dsMenuLog.png");
				else menu.addNewSibling("File", "Log", "لیست ثبت", false, "dsMenuLog.png");
				i=0;
				if(PermitLog_Security) menu.addNewChild("Log", i++, "Log_Security", "امنیت", false, "dsMenuLog_Security.png");
				if(PermitLog_Reseller_www) menu.addNewChild("Log", i++, "Log_Reseller_www", "پنل نمایندگان و اپراتورها", false, "dsMenuLog_Reseller_www.png");
				//menu.addNewSeparator("Log_Reseller_www");
				if(PermitLog_Radius) menu.addNewChild("Log", i++, "Log_Radius", "ردیوس", false, "dsMenuLog_Radius.png");
				if(PermitLog_Radius_Server) menu.addNewChild("Log", i++, "Log_Radius_Server", "سرور", false, "dsMenuLog_Radius_Server.png");
				if(PermitLog_Event) menu.addNewChild("Log", i++, "Log_Event", "رویداد", false, "dsMenuLog_Event.png");
				MenuLog=true;
			}

			//Online-------------------------------------------------------------------------------
			var MenuOnline=false;
			PermitOnline_Radius_User=ISPermit("Online.Radius.User.List");
			PermitOnline_Radius_UserBlock=ISPermit("Online.Radius.UserBlock.List");
			PermitOnline_Radius_DCQueue=ISPermit("Online.Radius.DCQueue.List");
			PermitOnline_Radius_UsedIP=ISPermit("Online.Radius.UsedIP.List");
			PermitOnline_Web_Reseller=ISPermit("Online.Web.Reseller.List");
			PermitOnline_Web_User=ISPermit("Online.Web.User.List");
			PermitOnline_Web_IPBlock=ISPermit("Online.Web.IPActivity.List");
			PermitOnline_URL_Reporting=ISPermit("Online.URL.Reporting.List");
			PermitOnline_UserUsage=ISPermit("Online.UserUsage.List");

			if(PermitOnline_Web_User||PermitOnline_URL_Reporting||PermitOnline_UserUsage||PermitOnline_Radius_UsedIP||PermitOnline_Radius_DCQueue||PermitOnline_Radius_UserBlock||PermitOnline_Web_IPBlock||PermitOnline_Radius_User || PermitOnline_Web_Reseller||PermitOnline_Web_User){
				if(MenuLog) menu.addNewSibling("Log", "Online", "آنلاین", false, "dsMenuOnline.png");
				else if(MenuCRM) menu.addNewSibling("CRM", "Online", "آنلاین", false, "dsMenuOnline.png");
				else menu.addNewSibling("File", "Online", "آنلاین", false, "dsMenuOnline.png");
				i=0;
				if(PermitOnline_Radius_User) menu.addNewChild("Online", i++, "Online_Radius_User", "کاربران آنلاین", false, "dsMenuOnline_Radius_User.png");
				if(PermitOnline_Radius_UserBlock) menu.addNewChild("Online", i++, "Online_Radius_UserBlock", "کاربران مسدود شده ردیوس", false, "dsMenuOnline_Radius_UserBlock.png");
				if(PermitOnline_Radius_DCQueue) menu.addNewChild("Online", i++, "Online_Radius_DCQueue", "صف کاربران در حال قطع شدن", false, "dsMenuOnline_Radius_DCQueue.png");
				if(PermitOnline_Radius_UsedIP) menu.addNewChild("Online", i++, "Online_Radius_UsedIP", "آی پی های استفاده شده کاربران آنلاین", false, "dsMenuOnline_Radius_UsedIP.png");
				if(PermitOnline_Web_Reseller) menu.addNewChild("Online", i++, "Online_Web_Reseller", "لیست نمایندگان فروش آنلاین در پنل مدیریت", false, "dsMenuOnline_Web_Reseller.png");
				if(PermitOnline_Web_User) menu.addNewChild("Online", i++, "Online_Web_User", "لیست کاربران آنلاین در پنل کاربری", false, "dsMenuOnline_Web_User.png");
				if(PermitOnline_Web_IPBlock) menu.addNewChild("Online", i++, "Online_Web_IPBlock", "فعالیت کاربران آنلاین", false, "dsMenuOnline_Web_IPBlock.png");
				if(PermitOnline_URL_Reporting) menu.addNewChild("Online", i++, "Online_URL_Reporting", "گزارش صفحات بازدید شده کاربران آنلاین", false, "dsMenuOnline_URL_Reporting.png");
				if(PermitOnline_UserUsage) menu.addNewChild("Online", i++, "Online_UserUsage", "گزارش مصرف کاربران آنلاین", false, "dsMenuOnline_UserUsage.png");
				MenuOnline=true;
			}
			//Report-------------------------------------------------------------------------------
			var MenuReport=false;
			PermitReport_URLList=ISPermit("Report.URL.List.List");
			PermitReport_URLTopSite=ISPermit("Report.URL.TopSite.List");
			PermitReport_URLDaily=ISPermit("Report.URL.Daily.List");

			PermitReport_NetLogList=ISPermit("Report.NetLog.List.List");

			PermitReport_UserNotify=ISPermit("Report.User.Notify.List");
			PermitReport_UserPayment=ISPermit("Report.User.Payment.List");
			PermitReport_UserSavingOff=ISPermit("Report.User.SavingOff.List");
			PermitReport_UserUser=ISPermit("Report.User.UserAndUsage.List");
			PermitReport_UserService=ISPermit("Report.User.Service.List");
			PermitReport_UserConnection=ISPermit("Report.User.Connection.List");
			PermitReport_UserStatus=ISPermit("Report.User.Status.List");
			PermitReport_UserStatusQueue=ISPermit("Report.User.StatusQueue.List");
			PermitReport_UserParam=ISPermit("Report.User.Param.List");
			PermitReport_UserCallerId=ISPermit("Report.User.CallerId.List");
			PermitReport_UserTotalUsage=ISPermit("Report.User.TotalUsage.List");
			PermitReport_UserAttachment=ISPermit("Report.User.Attachment.List");
			PermitReport_UserNote=ISPermit("Report.User.Note.List");
			PermitReport_UserSupportHistory=ISPermit("Report.User.SupportHistory.List");
			PermitReport_UserChangeLog=ISPermit("Report.User.ChangeLog.List");
			PermitReport_UserWebHistory=ISPermit("Report.User.WebHistory.List");


			PermitReport_ResellerSummary=ISPermit("Report.Reseller.Summary.List");
			PermitReport_ResellerCredit=ISPermit("Report.Reseller.Credit.List");
			PermitReport_ResellerPayment=ISPermit("Report.Reseller.Payment.List");
			PermitReport_ResellerTransaction=ISPermit("Report.Reseller.Transaction.List");
			PermitReport_ResellerChangeLog=ISPermit("Report.Reseller.ChangeLog.List");
			PermitReport_ResellerWebHistory=ISPermit("Report.Reseller.WebHistory.List");


			if(PermitReport_URLList||PermitReport_URLList||PermitReport_URLTopSite||PermitReport_URLDaily||PermitReport_UserNotify||PermitReport_UserPayment||PermitReport_UserSavingOff||
				PermitReport_UserUser||PermitReport_UserService||PermitReport_UserConnection||PermitReport_UserStatus||PermitReport_UserStatusQueue||PermitReport_UserParam||PermitReport_UserCallerId||PermitReport_UserTotalUsage||PermitReport_UserAttachment||PermitReport_UserNote||PermitReport_UserSupportHistory||PermitReport_ResellerTransaction||PermitReport_ResellerPayment||PermitReport_ResellerCredit||PermitReport_ResellerSummary||PermitReport_ResellerChangeLog||PermitReport_UserChangeLog||PermitReport_UserWebHistory||PermitReport_ResellerWebHistory){
				if(MenuOnline) menu.addNewSibling("Online", "Report", "گزارش", false, "dsMenuReport.png");
				else if(MenuLog) menu.addNewSibling("Log", "Report", "گزارش", false, "dsMenuReport.png");
				else if(MenuCRM) menu.addNewSibling("CRM", "Report", "گزارش", false, "dsMenuReport.png");
				else menu.addNewSibling("File", "Report", "گزارش", false, "dsMenuReport.png");
				i=0;

				if(PermitReport_URLList||PermitReport_URLTopSite||PermitReport_URLDaily){
					menu.addNewChild("Report", i++, "Report_URL", "صفحات بازدید شده کاربر", false, "dsMenuReport_URL.png");
					if(PermitReport_URLList) menu.addNewChild("Report_URL", i++, "Report_URLList", "لیست", false, "dsMenuReport_URLList.png");
					if(PermitReport_URLTopSite) menu.addNewChild("Report_URL", i++, "Report_URLTopSite", "پر بازدید", false, "dsMenuReport_URLTopSite.png");
					if(PermitReport_URLDaily) menu.addNewChild("Report_URL", i++, "Report_URLDaily", "امروز", false, "dsMenuReport_URLDaily.png");
				}

				if(PermitReport_NetLogList){
					menu.addNewChild("Report", i++, "Report_NetLog", "نت لاگ", false, "dsMenuReport_NetLog.png");
					if(PermitReport_NetLogList) menu.addNewChild("Report_NetLog", i++, "Report_NetLogList", "لیست", false, "dsMenuReport_NetLogList.png");
				}

				if(PermitReport_UserUser||PermitReport_UserService||PermitReport_UserPayment||PermitReport_UserSavingOff||PermitReport_UserNotify||PermitReport_UserConnection||PermitReport_UserStatus||PermitReport_UserStatusQueue||PermitReport_UserParam||PermitReport_UserCallerId||PermitReport_UserTotalUsage||PermitReport_UserAttachment||PermitReport_UserNote||PermitReport_UserSupportHistory||PermitReport_UserChangeLog||PermitReport_UserWebHistory){
					menu.addNewChild("Report", i++, "Report_User", "کاربر", false, "dsMenuReport_User.png");

					if(PermitReport_UserUser) menu.addNewChild("Report_User", i++, "Report_UserUser", "کاربران و میزان استفاده", false, "dsMenuReport_UserUser.png");
					if(PermitReport_UserService) menu.addNewChild("Report_User", i++, "Report_UserService", "سرویس", false, "dsMenuReport_UserService.png");
					if(PermitReport_UserPayment) menu.addNewChild("Report_User", i++, "Report_UserPayment", "پرداخت", false, "dsMenuReport_UserPayment.png");
					if(PermitReport_UserSavingOff) menu.addNewChild("Report_User", i++, "Report_UserSavingOff", "پس انداز", false, "dsMenuReport_UserSavingOff.png");
					if(PermitReport_UserNotify) menu.addNewChild("Report_User", i++, "Report_UserNotify", "اطلاع رسانی", false, "dsMenuReport_UserNotify.png");
					if(PermitReport_UserConnection) 					menu.addNewChild("Report_User", i++, "Report_UserConnection", "اتصالات", false, "dsMenuReport_UserConnection.png");
					if(PermitReport_UserStatus) menu.addNewChild("Report_User", i++, "Report_UserStatus", "وضعیت", false, "dsMenuReport_UserStatus.png");
					if(PermitReport_UserStatusQueue) menu.addNewChild("Report_User", i++, "Report_UserStatusQueue", "صف وضعیت", false, "dsMenuReport_UserStatusQueue.png");
					if(PermitReport_UserParam) menu.addNewChild("Report_User", i++, "Report_UserParam", "پارامتر", false, "dsMenuReport_UserParam.png");
					if(PermitReport_UserCallerId) menu.addNewChild("Report_User", i++, "Report_UserCallerId", "مک/آی پی", false, "dsMenuReport_UserCallerId.png");
					if(PermitReport_UserTotalUsage) menu.addNewChild("Report_User", i++, "Report_UserTotalUsage", "مجموع استفاده", false, "dsMenuReport_TotalUsage.png");
					if(PermitReport_UserAttachment) menu.addNewChild("Report_User", i++, "Report_UserAttachment", "پیوست", false, "dsMenuReport_Attachment.png");
					if(PermitReport_UserNote) menu.addNewChild("Report_User", i++, "Report_UserNote", "یادداشت", false, "dsMenuReport_Note.png");
					if(PermitReport_UserNote) menu.addNewChild("Report_User", i++, "Report_UserSupportHistory", "تاریخچه پشتیبانی", false, "dsMenuReport_SupportHistory.png");
					if(PermitReport_UserChangeLog) menu.addNewChild("Report_User", i++, "Report_UserChangeLog", "لیست تغییرات", false, "dsMenuReport_UserChangeLog.png");
					if(PermitReport_UserWebHistory) menu.addNewChild("Report_User", i++, "Report_UserWebHistory", "تاریخچه پنل", false, "dsMenuReport_UserWebHistory.png");
				}
				if(PermitReport_ResellerSummary||PermitReport_ResellerCredit||PermitReport_ResellerPayment||PermitReport_ResellerTransaction||PermitReport_ResellerChangeLog||PermitReport_ResellerWebHistory){
					menu.addNewChild("Report", i++, "Report_Reseller", "نماینده فروش", false, "dsMenuReport_Reseller.png");
					if(PermitReport_ResellerSummary) menu.addNewChild("Report_Reseller", i++, "Report_ResellerSummary", "خلاصه", false, "dsMenuReport_ResellerSummary.png");
					if(PermitReport_ResellerCredit) menu.addNewChild("Report_Reseller", i++, "Report_ResellerCredit", "اعتبار", false, "dsMenuReport_ResellerCredit.png");
					if(PermitReport_ResellerPayment) menu.addNewChild("Report_Reseller", i++, "Report_ResellerPayment", "پرداخت", false, "dsMenuReport_ResellerPayment.png");
					if(PermitReport_ResellerTransaction) menu.addNewChild("Report_Reseller", i++, "Report_ResellerTransaction", "تراکنش", false, "dsMenuReport_ResellerTransaction.png");
					if(PermitReport_ResellerChangeLog) menu.addNewChild("Report_Reseller", i++, "Report_ResellerChangeLog", "لیست تغییرات", false, "dsMenuReport_ResellerChangeLog.png");
					if(PermitReport_ResellerWebHistory) menu.addNewChild("Report_Reseller", i++, "Report_ResellerWebHistory", "تاریخچه پنل", false, "dsMenuReport_ResellerWebHistory.png");
				}

				MenuReport=true;
			}


			//Admin-------------------------------------------------------------------------------
			var MenuAdmin=false;
			PermitAdmin_Server=ISPermit("Admin.Server.List");
			PermitAdmin_NasInfo=ISPermit("Admin.NasInfo.List");
			PermitAdmin_Nas=ISPermit("Admin.Nas.List");
			PermitAdmin_Radius=ISPermit("Admin.Radius.List");
			PermitAdmin_Visp=ISPermit("Admin.VISPs.List");
			PermitAdmin_Supporter=ISPermit("Admin.Supporter.List");
			PermitAdmin_Center=ISPermit("Admin.Center.List");
			PermitAdmin_DSLAM=ISPermit("Admin.DSLAM.List");
			PermitAdmin_Status=ISPermit("Admin.User.Status.List");
			PermitAdmin_Class=ISPermit("Admin.User.Class.List");
			PermitAdmin_TimeRate=ISPermit("Admin.User.TimeRate.List");
			PermitAdmin_TrafficRate=ISPermit("Admin.User.TrafficRate.List");
			PermitAdmin_IPPool=ISPermit("Admin.User.IPPool.List");
			PermitAdmin_LoginTime=ISPermit("Admin.User.LoginTime.List");
			PermitAdmin_MikrotikRate=ISPermit("Admin.User.MikrotikRate.List");
			PermitAdmin_MikrotikRateValue=ISPermit("Admin.User.MikrotikRateValue.List");
			PermitAdmin_FinishRule=ISPermit("Admin.User.FinishRule.List");
			PermitAdmin_OffFormula=ISPermit("Admin.User.OffFormula.List");
			PermitAdmin_ServiceInfo=false;//ISPermit("Admin.User.ServiceInfo.List");
			PermitAdmin_WebAccess=ISPermit("Admin.User.WebAccess.List");
			PermitAdmin_DebitControl=ISPermit("Admin.User.DebitControl.List");
			PermitAdmin_Terminal=ISPermit("Admin.BankTerminal.List");
			PermitAdmin_Package=ISPermit("Admin.Package.List");
			PermitAdmin_PayOnline=ISPermit("Admin.PayOnline.List");
			PermitAdmin_ActiveDirectory=ISPermit("Admin.ActiveDirectory.List");
			PermitAdmin_CalledId=ISPermit("Admin.CalledId.List");
			PermitAdmin_CallerIdBlock=ISPermit("Admin.CallerIdBlock.List");
			PermitAdmin_MessageSMSProvider=ISPermit("Admin.Message.SMSProvider.List");
			PermitAdmin_MessageNotify=ISPermit("Admin.Message.Notify.List");
			PermitAdmin_SupportItem=ISPermit("Admin.User.SupportItem.List");
			PermitAdmin_Gift=ISPermit("Admin.User.Gift.List");
			//PermitAdmin_ResellerCredit=ISPermit("Admin.ResellerCredit.List");
			PermitAdmin_BatchProcess=ISPermit("Admin.BatchProcess.List");
			PermitAdmin_WebService=ISPermit("Admin.WebService.List");
			PermitAdmin_NetworkIP=ISPermit("Admin.NetworkIP.List");

			if(PermitAdmin_BatchProcess||PermitAdmin_DebitControl||PermitAdmin_WebAccess||PermitAdmin_ServiceInfo||PermitAdmin_CalledId||PermitAdmin_CallerIdBlock||PermitAdmin_MessageSMSProvider||PermitAdmin_MessageNotify||PermitAdmin_ActiveDirectory|PermitAdmin_MikrotikRateValue||PermitAdmin_PayOnline||PermitAdmin_Supporter||PermitAdmin_Terminal||PermitAdmin_Package||PermitAdmin_FinishRule||PermitAdmin_OffFormula||PermitAdmin_MikrotikRate||PermitAdmin_LoginTime||PermitAdmin_Server||PermitAdmin_TimeRate||PermitAdmin_TrafficRate||PermitAdmin_IPPool||PermitAdmin_Center||PermitAdmin_DSLAM||PermitAdmin_Nas||PermitAdmin_NasInfo||PermitAdmin_Visp || PermitAdmin_Status || PermitAdmin_Class||PermitAdmin_SupportItem||PermitAdmin_Gift){
				if(MenuReport) menu.addNewSibling("Report", "Admin", "مدیریت", false, "dsMenuAdmin.png");
				else if(MenuOnline) menu.addNewSibling("Online", "Admin", "مدیریت", false, "dsMenuAdmin.png");
				else if(MenuLog) menu.addNewSibling("Log", "Admin", "مدیریت", false, "dsMenuAdmin.png");
				else if(MenuCRM) menu.addNewSibling("CRM", "Admin", "مدیریت", false, "dsMenuAdmin.png");
				else menu.addNewSibling("File", "Admin", "مدیریت", false, "dsMenuAdmin.png");
				i=0;

				if(PermitAdmin_BatchProcess){
					menu.addNewChild("Admin", i++, "Admin_BatchProcess", "عملیات گروهی", false, "dsMenuAdmin_BatchProcess.png");
					menu.addNewChild("Admin_BatchProcess", i++, "Admin_DoBatchProcess", "انجام", false, "dsMenuAdmin_BatchProcess_DoBatchProcess.png");
					menu.addNewChild("Admin_BatchProcess", i++, "Admin_ImportAndGenerateUser", "وارد کردن و یا تولید کاربران", false, "dsMenuAdmin_ImportGenerateUser.png");
					menu.addNewChild("Admin_BatchProcess", i++, "Admin_BatchProcessHistory", "تاریخچه", false, "dsMenuAdmin_BatchProcess_BatchProcessHistory.png");
				}

				//User
				if(PermitAdmin_DebitControl||PermitAdmin_WebAccess||PermitAdmin_ServiceInfo||PermitAdmin_MikrotikRateValue||PermitAdmin_OffFormula||PermitAdmin_FinishRule||PermitAdmin_MikrotikRate||PermitAdmin_LoginTime||PermitAdmin_TimeRate||PermitAdmin_TrafficRate||PermitAdmin_IPPool||PermitAdmin_Status||PermitAdmin_Class||PermitAdmin_SupportItem||PermitAdmin_Gift){
					menu.addNewChild("Admin", i++, "Admin_User", "کاربران", false, "dsMenuAdmin_User.png");
					if(PermitAdmin_Class) menu.addNewChild("Admin_User", i++, "Admin_Class", "دسته", false, "dsMenuAdmin_Class.png");
					if(PermitAdmin_Status) menu.addNewChild("Admin_User", i++, "Admin_Status", "وضعیت", false, "dsMenuAdmin_Status.png");
					if(PermitAdmin_TimeRate) menu.addNewChild("Admin_User", i++, "Admin_UserTimeRate", "ضریب محاسبه زمان", false, "dsMenuAdmin_TimeRate.png");
					if(PermitAdmin_TrafficRate) menu.addNewChild("Admin_User", i++, "Admin_UserTrafficRate", "ضریب محاسبه ترافیک", false, "dsMenuAdmin_TrafficRate.png");
					if(PermitAdmin_IPPool) menu.addNewChild("Admin_User", i++, "Admin_UserIPPool", "دامنه آی پی", false, "dsMenuAdmin_IPPool.png");
					if(PermitAdmin_LoginTime) menu.addNewChild("Admin_User", i++, "Admin_UserLoginTime", "محدودیت زمان ورود", false, "dsMenuAdmin_LoginTime.png");
					if(PermitAdmin_MikrotikRate) menu.addNewChild("Admin_User", i++, "Admin_UserMikrotikRate", "لیست سرعت های میکروتیک", false, "dsMenuAdmin_MikrotikRate.png");
					if(PermitAdmin_MikrotikRateValue) menu.addNewChild("Admin_User", i++, "Admin_UserMikrotikRateValue", "تعریف سرعت میکروتیک", false, "dsMenuAdmin_MikrotikRateValue.png");
					if(PermitAdmin_FinishRule) menu.addNewChild("Admin_User", i++, "Admin_UserFinishRule", "قانون اتمام", false, "dsMenuAdmin_FinishRule.png");
					if(PermitAdmin_OffFormula) menu.addNewChild("Admin_User", i++, "Admin_UserOffFormula", "فرمول تخفیف", false, "dsMenuAdmin_OffFormula.png");
					if(PermitAdmin_ServiceInfo) menu.addNewChild("Admin_User", i++, "Admin_UserServiceInfo", "ServiceInfo", false, "dsMenuAdmin_ServiceInfo.png");
					if(PermitAdmin_WebAccess) menu.addNewChild("Admin_User", i++, "Admin_UserWebAccess", "دسترسی های پنل کاربران", false, "dsMenuAdmin_WebAccess.png");
					if(PermitAdmin_DebitControl) menu.addNewChild("Admin_User", i++, "Admin_UserDebitControl", "کنترل بدهی کاربران", false, "dsMenuAdmin_DebitControl.png");
					if(PermitAdmin_SupportItem) menu.addNewChild("Admin_User", i++, "Admin_UserSupportItem", "موارد پشتیبانی", false, "dsMenuAdmin_SupportItem.png");
					if(PermitAdmin_Gift) menu.addNewChild("Admin_User", i++, "Admin_UserGift", "هدایا", false, "dsMenuAdmin_Gift.png");
				}
				//Notify
				if(PermitAdmin_MessageSMSProvider||PermitAdmin_MessageNotify){
					menu.addNewChild("Admin", i++, "Admin_Message", "اطلاع رسانی", false, "dsMenuAdmin_Notify.png");
					if(PermitAdmin_MessageSMSProvider) menu.addNewChild("Admin_Message", i++, "Admin_MessageSMSProvider", "ارائه دهنده پیام کوتاه", false, "dsMenuAdmin_MessageSMSProvider.png");
					if(PermitAdmin_MessageNotify) menu.addNewChild("Admin_Message", i++, "Admin_MessageNotify", "مدیریت اعلان ها", false, "dsMenuAdmin_MessageNotify.png");
				}

				if(PermitAdmin_NasInfo) menu.addNewChild("Admin", i++, "Admin_NasInfo", "پارامترهای سرور ردیوس", false, "dsMenuAdmin_NasInfo.png");
				if(PermitAdmin_Nas) menu.addNewChild("Admin", i++, "Admin_Nas", "سرور ردیوس", false, "dsMenuAdmin_Nas.png");
				if(PermitAdmin_Radius) menu.addNewChild("Admin", i++, "Admin_Radius", "پورت های ردیوس", false, "dsMenuAdmin_Radius.png");

				if(PermitAdmin_Visp) menu.addNewChild("Admin", i++, "Admin_Visp", "ارائه دهنده مجازی اینترنت", false, "dsMenuAdmin_Visp.png");
				if(PermitAdmin_Supporter) menu.addNewChild("Admin", i++, "Admin_Supporter", "پشتیبان", false, "dsMenuAdmin_Supporter.png");
				if(PermitAdmin_Center) menu.addNewChild("Admin", i++, "Admin_Center", "مرکز", false, "dsMenuAdmin_Center.png");
				//if(PermitAdmin_DSLAM) menu.addNewChild("Admin", i++, "Admin_DSLAM", "DSLAM", false, "dsMenuAdmin_DSLAM.png");
				if(PermitAdmin_Server) menu.addNewChild("Admin", i++, "Admin_Server", "سرور", false, "dsMenuAdmin_Server.png");
				if(PermitAdmin_Terminal) menu.addNewChild("Admin", i++, "Admin_Terminal", "درگاه پرداخت", false, "dsMenuAdmin_Terminal.png");
				if(PermitAdmin_Package) menu.addNewChild("Admin", i++, "Admin_Package", "بسته شارژ", false, "dsMenuAdmin_Package.png");
				if(PermitAdmin_PayOnline) menu.addNewChild("Admin", i++, "Admin_PayOnline", "پرداخت آنلاین", false, "dsMenuAdmin_PayOnline.png");
				if(PermitAdmin_ActiveDirectory) menu.addNewChild("Admin", i++, "Admin_ActiveDirectory", "اکتیو دایرکتوری", false, "dsMenuAdmin_ActiveDirectory.png");
				if(PermitAdmin_CalledId) menu.addNewChild("Admin", i++, "Admin_CalledId", "نام سرویس/آی پی سرور", false, "dsMenuAdmin_CalledId.png");
				if(PermitAdmin_CallerIdBlock) menu.addNewChild("Admin", i++, "Admin_CallerIdBlock", "مک/آی پی بلاک شده", false, "dsMenuAdmin_CallerIdBlock.png");
				if(PermitAdmin_WebService) menu.addNewChild("Admin", i++, "Admin_WebService", "وب سرویس", false, "dsMenuAdmin_WebService_List.png");
				if(PermitAdmin_NetworkIP) menu.addNewChild("Admin", i++, "Admin_NetworkIP", "آی پی های نت لاگ", false, "dsMenuAdmin_NetworkIP_List.png");

				MenuAdmin=true;
			}
			//About-------------------------------------------------------------------------------
				if(LoginResellerName.toLowerCase()=='admin'){
					menu.addNewSibling("Admin", "Help", "راهنما", false, "dsMenuHelp.png");
					i=0;
					menu.addNewChild("Help", i++, "About_Update", "بروز رسانی", false, "dsMenuAbout_Update.png");
					menu.addNewChild("Help", i++, "About_License", "اطلاعات قفل", false, "dsMenuAbout_License.png");
					menu.addNewChild("Help", i++, "HelpPage", "صفحه راهنما", false, "dsMenuHelp_Help.png");
					menu.setHref("HelpPage", "http://smartispbilling.com/help/deltasib/content/loadContent/aboutDeltaSIB", "blank");
					menu.addNewChild("Help", i++, "About_Deltasib", "درباره دلتاسیب", false, "dsMenuAbout_Deltasib.png");
				}

			// TopToolbar   ===================================================================
			TopToolbar = dhxLayout.cells("a").attachToolbar();
			DSToolbarInitial(TopToolbar);
			AddPopupReLogin();
			AddPopupShowCreditBalance();
			AddPopupShowTransferCredit();
			AddPopupShowGetCreditOnline()
			AddPopupChangePassword();
			TopToolbar.addSeparator("sep1",null);
			DSToolbarAddButton(TopToolbar,null,"Logout","خروج","tow_Logout",TopToolbar_OnLogoutClick);
			TopToolbar.addSeparator("sep2",null);
			TopToolbar.addButtonTwoState("WarnBeforeClose", null, "اخطار بستن برگه", "ds_tow_WarnBeforeClose.png", "ds_tow_WarnBeforeClose_dis.png");
			TopToolbar.setItemToolTip("WarnBeforeClose","...اخطار قبل از بستن برگه ها");
			TopToolbar.attachEvent("onStateChange", function(id, state){
				if((id=="WarnBeforeClose")&&(typeof(Storage) !== "undefined"))
					localStorage.setItem('WarnBeforeCloseState', state?1:0);
			});
			TopToolbar.setItemState("WarnBeforeClose",(typeof(Storage) !== "undefined")?localStorage.getItem('WarnBeforeCloseState'):false,false);
			DSToolbarAddButton(TopToolbar,null,"CloseAllTabs","بستن همه برگه ها","tow_CloseAllTabs",TopToolbar_OnCloseAllTabsClick);
			TopToolbar.disableItem("CloseAllTabs");
			TopToolbar.addSpacer("Logout");

			// tabbar   ===================================================================
			tabbar = dhxLayout.cells("a").attachTabbar();
			tabbar.setSkin(tabbar_main_skin);
			tabbar.setImagePath(tabbar_image_path);
			tabbar.setMargin(0);
			tabbar.setOffset(0);
			tabbar.enableTabCloseButton(false);
			tabbar.addTab(0,DSTabbar["Home"][0],DSTabbar["Home"][1]);
			tabbar.setTabActive(0);
			tabbar.setHrefMode("iframes-on-demand");
			tabbar.setContentHref(0,DSTabbar["Home"][2]+"&LoginResellerName="+LoginResellerName);
			tabcount=0;
			tabbar.enableTabCloseButton(true);
			BatchProcessInstanceCount=0;
			tabbar.attachEvent("onTabClose", function(id){
				//dhtmlx.message({text:"Closed:id="+id"    tabcount="+tabcount,expire:5000});
				if(TopToolbar.getItemState("WarnBeforeClose")&&(!confirm("از بستن برگه مطمئن هستید؟")))
					return false;
				if((tabbar.getNumberOfTabs()<=1)&&(BatchProcessInstanceCount<=0)){
					TopToolbar.disableItem("CloseAllTabs");
				}
				return true;
			});

			dhxLayout.progressOff();

			if(DSNewVersion>DSInstalledVersion)
				menuClick('About_Update');

			var DiskFullPercent="<?php echo Round(100-(100*disk_free_space('/')/disk_total_space('/')),2)?>";
			if(LoginResellerName=='admin'){
				if(DiskFullPercent>90)
					dhtmlx.alert({title: "هشدار",type: "alert-error",text: "<span style='color:red;font-weight:bold'>هارد سرور "+DiskFullPercent+"% پر شده است</span><br/>شما باید برخی از فایل هایی که استفاده نمی شوند را حذف نمایید"});
				else if(DiskFullPercent>80)
					dhtmlx.alert({title: "هشدار",type: "alert-warning",text: "<span style='color:red;font-weight:bold'>هارد سرور "+DiskFullPercent+"% پر شده است</span><br/>شما باید برخی از فایل هایی که استفاده نمی شوند را حذف نمایید"});
			}
		}
	});
}

//----------------------------------------------------------------------------------------------
function AddPopupReLogin(){//Relogin
	DSToolbarAddButtonPopup(TopToolbar,null,"ReLogin","ورود مجدد","tow_ReLogin");
	Popup2=DSInitialPopup(TopToolbar,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);

	Form2.attachEvent("onEnter", function(){Form2OnButtonClick('Proceed')});
}
function Form2OnButtonClick(name){//Relogin
	if(name=='Close') Popup2.hide();
	else{
		Form2.updateValues();
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Popup2.hide();
			var enpass=hex_sha512(Form2.getItemValue("Password"));
			Form2.setItemValue("enpass", enpass);
			Form2.setItemValue("Password", "HACKED");
			dhxLayout.progressOn();
			Form2.send('DSResellerProcessLogin.php?'+un(), function(loader, response) {
				dhxLayout.progressOff();
				response=CleanError(response);
				var responsearray=response.split("~",8);
				if(responsearray[0]!='OK'){
					dhtmlx.alert({text:response.substring(1),ok:"بستن",callback:function(){setTimeout(function(){Popup2.show("ReLogin");},300)}});
				}
				else{
					if(DSInstalledVersion!=responsearray[4])
						dhtmlx.alert({
							ok:"Reload",
							text:"Deltasib version has been changed. You must reload the page to take effect all the things.<br/>"+GetBoldSpan("Ctrl+F5 is recommended","orangered"),
							callback:function(){
								window.onbeforeunload=null;
								window.location.reload(true);
							}
						});
					else{
						if(responsearray[5]>DSInstalledVersion)
							menuClick('About_Update');
						var YourIP=responsearray[1];
						var LastLoginIP=responsearray[2];
						var LastLoginDT=responsearray[3];
						var CurrentDate=responsearray[6];
						var LockInfo=responsearray[7];
						var DeltasibTopText="سیستم مدیریت دلتاسیب "+DSInstalledVersion;

						if(typeof(Storage) !== "undefined")
							localStorage.setItem('LS_CurrentDate', CurrentDate);

						var LoginIPChangeLevelImg="";
						var YourIPOctets=YourIP.split(".",4);
						var LastLoginIPOctets=LastLoginIP.split(".",4);
						if(YourIPOctets[0]!=LastLoginIPOctets[0])
							LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange0.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
						else if(YourIPOctets[1]!=LastLoginIPOctets[1])
							LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange1.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
						else if(YourIPOctets[2]!=LastLoginIPOctets[2])
							LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange2.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
						else if(YourIPOctets[3]!=LastLoginIPOctets[3])
							LoginIPChangeLevelImg=" <img src='/dsimgs/ds_LoginIPChange3.png' width='16px' height:'16px' style='vertical-align:bottom;'/>";
						messageText="خوش آمدید "+GetBoldSpan(LoginResellerName,"navy")+
							"<br/>آی پی شما : "+GetBoldSpan(YourIP,"navy")+
							"<br/><br/>آخرین ورود شما از "+GetBoldSpan(LastLoginIP,"navy")+LoginIPChangeLevelImg+
							"<br/>در "+GetBoldSpan(LastLoginDT,"navy");
						// alert(LockInfo);
						if(LockInfo=="FreeDeltasib"){
							DeltasibTopText+="(<span style='color:red;font-weight:bold;cursor:pointer' onclick='dhtmlx.alert(\"شما از دلتاسیب رایگان استفاده می کنید\")'>نمایشی</span>)";
							messageText+="<p style='border-top:1px solid black;width:100%;text-align:center'><br/>";
							messageText+=GetBoldSpan("شما از دلتاسیب رایگان استفاده می کنید","red");
							messageText+="</p>";
						}
						else if(LockInfo!=""){
							var LockInfoArray=LockInfo.split("`",5);
							if(LockInfoArray[1]!=''){
								var LockExpireText="Your lock will expire on '"+GetBoldSpan(LockInfoArray[1],"red")+"'";
								if((LockInfoArray[2]>30))
									dhtmlx.message({text:LockExpireText,expire:60000,type:"LoginInformationsMsg"});
								else
									dhtmlx.alert({text:LockExpireText,title:"Attention",type:"alert-"+((LockInfoArray[2]<=10)?"error":"warning")});
								DeltasibTopText+="(<span style='color:orangered;font-weight:bold;cursor:pointer' onclick='dhtmlx.alert(\"Your lock will expire on ["+LockInfoArray[1]+"]\")'>Trial</span>)";
							}

							if(LockInfoArray[3]!=""){
								messageText+="<p style='border-top:1px solid black;width:100%;text-align:center'><br/>";
								messageText+="Dear customer "+GetBoldSpan(LockInfoArray[0],"navy")+"<br/>"
								if(LockInfoArray[4]>30)
									messageText+=GetBoldSpan("You have support to '"+LockInfoArray[3]+"'",'green');
								else if(LockInfoArray[4]>0)
									messageText+=GetBoldSpan("Support will finish on '"+LockInfoArray[3]+"'",'yellow');
								else{
									messageText+=GetBoldSpan("Support finished on '"+LockInfoArray[3]+"'",'red');
									dhtmlx.alert({text:"Support has been expired on '"+GetBoldSpan(LockInfoArray[3],'red')+"'.<br/>Please renew it to continue technical support.",type:"alert-warning",title:"Attention"});
								}
								messageText+="</p>";
							}
						}

						DeltasibTopText+=" - <span style='cursor:pointer' onclick='dhtmlx.alert(messageText)'>خوش آمدید <span style='color:forestgreen;font-weight:bold;'>"+LoginResellerName+"</span></span>! - <span style='font-weight:bold' id='SessionTimeout'>--:--</span>";

						dhtmlx.message({text:messageText,expire:30000,type:"LoginInformationsMsg"});
						menu.setTopText(DeltasibTopText);
						SetIdleTimeout();
					}

				}
			});
		}
	}



}
function Popup2OnShow(){//Relogin
	Form2.setItemValue("Username",LoginResellerName);
	Form2.setItemValue("Password",'');
	Form2.setItemFocus("Password");
}

//----------------------------------------------------------------------------------------------
function AddPopupShowCreditBalance(){//ShowCreditBalance
	DSToolbarAddButtonPopup(TopToolbar,null,"ShowCreditBalance","نمایش اعتبار حساب","tow_ShowCreditBalance");
	Popup3=DSInitialPopup(TopToolbar,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,function(){Popup3.hide();});
}
function Popup3OnShow(){//ShowCreditBalance
	if(LoginResellerName=='admin'){
		Form3.setItemValue("CreditBalance","بدون محدودیت");
		return;
	}
	dhxLayout.progressOn();
	Form3.load(RenderFile+".php?"+un()+"&act=LoadCreditBalance",function(id,respond){
		dhxLayout.progressOff();
	});
}

//----------------------------------------------------------------------------------------------
function AddPopupShowTransferCredit(){//TransferCredit
	DSToolbarAddButtonPopup(TopToolbar,null,"TransferCredit","انتقال اعتبار","tow_TransferCredit");
	Popup4=DSInitialPopup(TopToolbar,PopupId4,Popup4OnShow);
}
function Form4OnButtonClick(name){//TransferCredit
	if(name=='Close') Popup4.hide();
	else{
		if(DSFormValidate(Form4,Form4FieldHelpId))
			DSFormUpdateRequestProgress(dhxLayout,Form4,RenderFile+".php?"+un()+"&act=TransferCredit",function(){Popup4.hide();},function(){Popup4.hide();});
	}
}
function Popup4OnShow(){//TransferCredit
	if(typeof Form4 != "undefined")
		Form4.unload();
	dhxLayout.progressOn();
	Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,Form4OnButtonClick);
	Form4.attachEvent("onOptionsLoaded", function(name){dhxLayout.progressOff()});
}

//----------------------------------------------------------------------------------------------
function AddPopupShowGetCreditOnline(){//GetCreditOnline
	if(LoginResellerName=="admin")
		return;
	DSToolbarAddButtonPopup(TopToolbar,null,"GetCreditOnline","خرید اعتبار آنلاین","tow_TransferCredit");
	Popup5=DSInitialPopup(TopToolbar,PopupId5,Popup5OnShow);
}
function Form5OnButtonClick(name){//GetCreditOnline
	if(name=='Close') Popup5.hide();
	else{
		if(DSFormValidate(Form5,Form5FieldHelpId)){
			Popup5.hide();
			var Package_Id=Form5.getItemValue("Package_Id");
			var Terminal_Id=Form5.getItemValue("Terminal_Id");

			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("action", "DSIndex_EditRender.php?"+un()+'&act=AddPackage');
			form.setAttribute("target", "_self");

			var hiddenField1 = document.createElement("input");
			hiddenField1.setAttribute("name", "Package_Id");
			hiddenField1.setAttribute("value", Package_Id);
			form.appendChild(hiddenField1);

			var hiddenField2 = document.createElement("input");
			hiddenField2.setAttribute("name", "Terminal_Id");
			hiddenField2.setAttribute("value", Terminal_Id);
			form.appendChild(hiddenField2);

			document.body.appendChild(form);
			window.onbeforeunload=null;
			form.submit();
			document.body.removeChild(form);
		}
	}



}
function Popup5OnShow(){//GetCreditOnline
	if(typeof Form5 != "undefined")
		Form5.unload();
	dhxLayout.progressOn();
	Form5=DSInitialForm(Popup5,Form5Str,Form5PopupHelp,Form5FieldHelpId,Form5FieldHelp,Form5OnButtonClick);
	Form5.attachEvent("onOptionsLoaded", function(name){
		if(Form5.getItemValue("LoadedOptions")==1)
			dhxLayout.progressOff()
		else
			Form5.setItemValue("LoadedOptions",Form5.getItemValue("LoadedOptions")-1);
	});
}

//----------------------------------------------------------------------------------------------
function AddPopupChangePassword(){//ChangePassword
	DSToolbarAddButtonPopup(TopToolbar,null,"ChangePassword","تغییر کلمه عبور","tow_ChangePass");
	Popup6=DSInitialPopup(TopToolbar,PopupId6,Popup6OnShow);
	Form6=DSInitialForm(Popup6,Form6Str,Form6PopupHelp,Form6FieldHelpId,Form6FieldHelp,Form6OnButtonClick);
	Form6.attachEvent("onEnter", function(){Form6OnButtonClick("Proceed")});
}
function Form6OnButtonClick(name){//ChangePassword
	if(name=='Close') Popup6.hide();
	else{
		if(DSFormValidate(Form6,Form6FieldHelpId)){
			var Pass1=Form6.getItemValue("NewPassword1");
			var Pass2=Form6.getItemValue("NewPassword2");
			if(Pass1.localeCompare(Pass2)!=0)
				dhtmlx.alert({title: "خطا",type: "alert-error",text: "کلمه عبور جدید تکرار شده صحیح نیست",ok:"بستن",callback:function(){Form6.setItemFocus("NewPassword2")}});
			else if(Pass1.length<6)
				dhtmlx.alert({title: "خطا",type: "alert-error",text: "کلمه عبور باید حداقل ۶ کاراکتر باشد",ok:"بستن",callback:function(){Form6.setItemFocus("NewPassword1")}});
			else{
				Popup6.hide();
				var enpass=hex_sha512(Form6.getItemValue("OldPassword"));
				Form6.setItemValue("enpass", enpass);
				Form6.setItemValue("OldPassword", "HACKED");
				DSFormUpdateRequestProgress(dhxLayout,Form6,'DSIndex_Edit'+"Render.php?"+un()+"&act=ChangePassword",Form6DoAfterUpdateOk,Form6DoAfterUpdateFail);
			}
		}
	}
}
function Form6DoAfterUpdateOk(){//ChangePassword
	window.onbeforeunload=null;
	Popup6.hide();
	dhtmlx.alert({text:"کلمه عبور با موفقیت تغییر کرد, شما به صفحه ورود هدایت می شوید",ok:"بستن",callback:function(){location.reload();}});
}
function Form6DoAfterUpdateFail(){//ChangePassword
	setTimeout(function(){Popup6.show("ChangePassword")},300);
}
function Popup6OnShow(){//ChangePassword
	if(BatchProcessInstanceCount>0){
		dhtmlx.message({text:"تا زمانی که پردازش های چندتایی در حال اجراست، نمی توانید کلمه عبور را تغییر دهید["+BatchProcessInstanceCount+"]...", type:"error",expire:8000});
		Popup6.hide();
	}
	Form6.setItemValue("OldPassword",'');
	Form6.setItemValue("NewPassword1",'');
	Form6.setItemValue("NewPassword2",'');
	Form6.setItemFocus("OldPassword");
}

//----------------------------------------------------------------------------------------------
function TopToolbar_OnCloseAllTabsClick(){//CloseAllTabs
	if(tabbar.getNumberOfTabs()>1){
		if(BatchProcessInstanceCount>0)
			dhtmlx.message({text:"نمی توان همه ی پنجره ها را بست<br/>عملیات گروهی در حال انجام است["+BatchProcessInstanceCount+"]...", type:"error",expire:8000});
		else if(ISValidResellerSession())
			dhtmlx.confirm({
				title: "هشدار",
				type:"confirm-warning",
				cancel: "خیر",
				ok: "بلی",
				text: "آیا از بستن همه برگه ها مطمئن هستید؟",
				callback: function(Result){
					if(Result){
						tabbar.clearAll();
						tabbar.normalize();
						TopToolbar.disableItem("CloseAllTabs");
						tabbar.enableTabCloseButton(false);
						tabbar.addTab(0,DSTabbar["Home"][0],DSTabbar["Home"][1]);
						tabbar.setTabActive(0);
						tabbar.setHrefMode("iframes-on-demand");
						tabbar.setContentHref(0,DSTabbar["Home"][2]+"&LoginResellerName="+LoginResellerName);
						tabcount=0;
						tabbar.enableTabCloseButton(true);
					}
				}
			});
	}
	else
		dhtmlx.message({text:".برگه ای برای بستن وجود ندارد",type:"error"});
}

function GetBoldSpan(T,C){
	return "<span style='color:"+C+";font-weight:bold'>"+T+"</span>";
}

}//window.onload
//----------------------------------------------------------------------------------------------
function TopToolbar_OnLogoutClick(){//Logout
	if(BatchProcessInstanceCount>0)
		dhtmlx.message({text:"نمی توان خارج شد<br/>عملیات گروهی در حال اجرا است["+BatchProcessInstanceCount+"]...", type:"error",expire:8000});
	else
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-warning",
			cancel: "خیر",
			ok: "بلی",
			text: "آیا از خروج مطمئن هستید؟",
			callback: function(Result){
				if(Result){
					dhxLayout.progressOn();
					w1.hide();
					window.onbeforeunload=null;
					dhtmlxAjax.get("DSReseller_Logout.php",function (loader){
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						dhxLayout.progressOff();
						location.reload();
					});
				}
			}
		});
}

function menuClick(id,zoneId,cas){
	if(id=="Logout")
		TopToolbar_OnLogoutClick();
	else
		OpenItem(id,"");
}//menuClick
function OpenItem(id,Param) {//Param is to call from child pages
	tabcount++;
	tabbar.addTab(tabcount,DSTabbar[id][0],DSTabbar[id][1]);
	tabbar.setTabActive(tabcount);
	tabbar.setHrefMode("iframes-on-demand");
	tabbar.setContentHref(tabcount,DSTabbar[id][2]+"&LoginResellerName="+LoginResellerName+Param);
	TopToolbar.enableItem("CloseAllTabs");
}

function SetIdleTimeout(){
	clearTimeout(IdleTimeoutTimer_Id);
	RemainSessionTime=getCookie("DSResellerTimeOut");
	if(RemainSessionTime>60){
		document.getElementById("SessionTimeout").innerHTML="پایان نشست ≈ "+GetTimerString();
		SessionTimer_Interval=Math.min(10,RemainSessionTime-60);
	}
	else if(RemainSessionTime>0){
		document.getElementById("SessionTimeout").innerHTML="پایان نشست ≈ <span style='color:orangered'>"+RemainSessionTime+" ثانیه</span>";
		SessionTimer_Interval=1;
	}
	else{
		document.getElementById("SessionTimeout").innerHTML="<span style='color:firebrick'>نشست منقضی شد</div>";
		SessionTimer_Interval=(RemainSessionTime>-60)?1:(RemainSessionTime>-600)?60:300;
	}
	SystemTimer=Date.now();
	IdleTimeoutTimer_Id=setTimeout(function(){
		RemainSessionTime=getCookie("DSResellerTimeOut");
		if(RemainSessionTime>=Previous_ResellerTimeout){
			Previous_ResellerTimeout=RemainSessionTime-Math.round((Date.now()-SystemTimer)/1000);
			setCookie("DSResellerTimeOut",Previous_ResellerTimeout);
			SetIdleTimeout();
		}
		else
			document.getElementById("SessionTimeout").innerHTML="<span style='color:lightcoral;font-weight:bold'>خطا در تنظیم زمان</div>";

	},SessionTimer_Interval*1000);
}

function GetTimerString(){
	var seconds = Math.floor(RemainSessionTime % 60 );
	var minutes = Math.floor((RemainSessionTime/60) % 60 )+(seconds>=30?1:0);
	var hours = Math.floor((RemainSessionTime/(3600)) % 24 );
	if(minutes>=60){hours++;minutes-=60;}
	var days = Math.floor(RemainSessionTime/86400 );
	if(hours>=24){days++;hours-=24;}
	return "<span style='color:blue'>"+(days>0?(days+" روز و "):"")+(hours>0?(hours+" ساعت و "):"")+(minutes<10?"":"")+minutes+" دقیقه "+"</span>";
}

function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + "; path=/reseller";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


function ChechForUpdate(){
	var t=localStorage.getItem('DontCheckUpdate');
	if((t)&&(t>Date.now()))
		return;

	var DSNewVersion=0;
	dhtmlxAjax.get("http://www.smartispbilling.com/deltasib/pakversion.php?",function (loader){
		var Response=loader.xmlDoc.responseText;
		// console.log(Response);
		// alert(1);
		var Result=Response.split("`");
		DSNewVersion=Result[1];
		// alert(DSNewVersion+"  "+(typeof DSNewVersion)+"\n"+DSInstalledVersion+"  "+(typeof DSInstalledVersion));
		if(DSInstalledVersion<DSNewVersion){
			MyNotify();
		}
		else
			setTimeout(ChechForUpdate,1800000);
	});
	function MyNotify(){
		if (Notification.permission == "default")
				Notification.requestPermission(MyNotify);
		else if(Notification.permission == "denied"){
			var t="<table style='font-size:11px' cellpadding='4' ><tr><td style='text-align:left;font-weight:bold;' colspan='2'>نسخه جدید موجود است</td></tr><tr><td rowspan='3'><img src='/dsimgs/NewVersion.png' style='width:70px;height:70px'/></td><td>نسخه دلتاسیب "+DSNewVersion+" منتشر شد...</td></tr><tr><td>لطفا بروزرسانی کنید</td></tr><tr><td><input onclick='DontCheckUpdate(event,this)'  type='checkbox'/> عدم بررسی به مدت یک هفته</td></tr></table>";
			dhtmlx.message({text:t,type:"DSUpdate",expire:-1});

		}
		else{
			var notification = new Notification('نسخه جدید موجود است', {
				icon: "/dsimgs/NewVersion.png",
				body: "نسخه دلتاسیب "+DSNewVersion+" منشتر شد...\nلطفا بروزرسانی کنید"
			});
			notification.onclick = function () {
				window.open("http://www.smartispbilling.com/index.php?page=DeltaSIB_Update_Features");
			};
		}
	}
}
function DontCheckUpdate(e,Item){
	e.stopImmediatePropagation();
	if(Item.checked){
		var someDate = new Date();
		localStorage.setItem('DontCheckUpdate', someDate.getTime()+604800000);
	}
	else
		localStorage.removeItem('DontCheckUpdate');
}
</script>

<title>مدیریت دلتاسیب</title>
</head>
<body>
<div id="InitialBodyComment" style='width:100%;text-align:center;font-weight:bold;direction: rtl;'>
	در حال بارگذاری...<br/>
	لطفا صبر کنید! (اولین بارگذاری امکان دارد کمی طول بکشد)<br>
	<span style='font-size:200%'>بررسی فعال بودن <span style='color:blue'>جاوااسکریپت</span> در مرورگر. (به صورت پیش فرض فعال می باشد).</span><br/><br/>
	<span style='font-size:200%'>توصیه می شود از `<span style='color:blue'>گوگل کروم</span>` یا `<span style='color:blue'>فایرفاکس</span>` استفاده کنید.</span><br/>
	<span style='font-size:200%'>و `<span style='color:red'>مرورگر اینترنت اکسپلورر</span>` پشتیبانی نمی شود.</span>
</div>
</body>
</html>
