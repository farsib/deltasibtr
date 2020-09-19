<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSNotifyEdit ....................................................................................");
	PrintInputGetPost();	
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
			margin: =10px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background=color:white;
        }
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="اعلان";
	var DataName="DSNotify_";
	var ChangeLogDataName='Notify';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSNotify_Edit","70","Admin.Message.Notify.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.Message.Notify.ChangeLog.List","ChangeLogDataName=Notify"],
					];
	//=======Form1 Notify Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	NotifyName:'فقط کاراکتر انگلیسی و اعداد،بدون فاصله - حداکثر ۳۲ کاراکتر',
							MinCreditTime:'هنگامی که اعتبار زمانی کاربر به کمتر از این مقدار رسید،پیام ارسال می شود',
							MinCreditTraffic:'هنگامی که اعتبار ترافیک کاربر به کمتر از این مقدار رسید،پیام ارسال می شود',
							CreditFinishLastSeenHours:'اگر کاربر از زمان اتمام اعتبارش تا مقدار وارد شده به بر حسب ساعت - به قبل متصل بوده باشد،پیام ارسال می شود',
							ServiceExpireLastSeenHours:'Send message if user seen online',
							BeforeCreditFinishMessage:'',
							AfterCreditFinishMessage:'',
							CreditFinishSendTime:'',
							MinActiveDays:'',
							BeforeServiceExpireMessage:'',
							AfterServiceExpireMessage:'',
							ServiceExpireSendTime:''
							
							};
	Form1FieldHelpId=["NotifyName",'NormalMinHourAfterPriorSend','MinCreditTime','MinCreditTraffic','CreditFinishLastSeenHours','BeforeCreditFinishMessage','AfterCreditFinishMessage','CreditFinishSendTime'];
	var Form1TitleField="NotifyName";
	var Form1DisableItems=["NotifyName",'NotifyType'];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="NotifyName";

	
	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"Notify_Id", label:"شناسه اعلان :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"NotifyName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"NormalMinHourAfterPriorSend", label:"ارسال مجدد پیامک در شرایط حداقل(ساعت) :",maxLength:5, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:122,labelWidth:248},
		{ type:"input" , name:"FinishMinHourAfterPriorSend", label:"ارسال مجدد پیامک در شرایط اتمام(ساعت) :",maxLength:5, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:122,labelWidth:248},

		{type: "select", name:"NotifyType",label: "نوع اعلان :" ,value:"Traffic",validate:"",required:true,options:[
			{text: "اعلان پایان اعتبار", value: "CreditFinishNotify",list:[
				{ type:"input" , name:"MinCreditTime", label:"حداقل اعتبار زمانی(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"3600", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"MinCreditTraffic", label:"حداقل اعتبار ترافیک(مگابایت) :", validate:"NotEmpty,ValidInteger",value:"300", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"CreditFinishLastSeenHours", label:"آخرین اتصال کاربر تا (ساعت) :", validate:"NotEmpty,ValidInteger",value:"1", maxLength:3, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"BeforeCreditFinishMessage", label:"متن پیام در شرایط حداقل اعتبار :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[RTrM]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
				{ type:"input" , name:"AfterCreditFinishMessage", label:"متن پیام در شرایط اتمام :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text:"<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[RTrM]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
				{ type:"input" , name:"CreditFinishSendTime", label:"زمان مجاز ارسال :",value:'Al0800-2300',maxLength:120, validate:"NotEmpty,IsValidLoginTime", labelAlign:"left", inputWidth:300,note: { text: "<span style='direction:rtl;float:right;text-align:justify;padding-bottom:40px;'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;su:یکشنبه&nbsp;mo:دوشنبه&nbsp;tu:سه شنبه&nbsp;we:چهارشنبه&nbsp;th:پنج شنبه&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo اعلان فقط روزهای دوشنبه ارسال خواهد شد.</br>درحالتی که بخواهیم در روز،ساعات خاصی را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ اعلان ارسال شود</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ اعلان ارسال شود </span>"}},
			]},
			{text: "اعلان پایان سرویس", value: "ServiceExpireNotify",list:[
				{ type:"input" , name:"MinActiveDays", label:"حداقل روز باقیمانده :", validate:"NotEmpty,ValidInteger",value:"3", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"ServiceExpireLastSeenHours", label:"آخرین اتصال کاربر تا (ساعت) :", validate:"NotEmpty,ValidInteger",value:"1", maxLength:3, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"BeforeServiceExpireMessage", label:"متن پیام در شرایط حداقل :",maxLength:250,rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[ShExpireDate]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+"</span>"}},
				{ type:"input" , name:"AfterServiceExpireMessage", label:"متن پیام بعد از انقضا سرویس :",maxLength:250,rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[ShExpireDate]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+"</span>"}},
				{ type:"input" , name:"ServiceExpireSendTime", label:"زمان مجاز ارسال :",value:'Al0800-2300',maxLength:120, validate:"NotEmpty,IsValidLoginTime", labelAlign:"left", inputWidth:300,note: { text: "<span style='direction:rtl;float:right;text-align:justify;padding-bottom:40px;'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;su:یکشنبه&nbsp;mo:دوشنبه&nbsp;tu:سه شنبه&nbsp;we:چهارشنبه&nbsp;th:پنج شنبه&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo اعلان فقط روزهای دوشنبه ارسال خواهد شد.</br>درحالتی که بخواهیم در روز،ساعات خاصی را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ اعلان ارسال شود</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ اعلان ارسال شود </span>"}},
			]},
			{text: "اعلان بدهی کاربر", value: "UserDebitNotify",list:[
				{ type:"input" , name:"MinUserDebit", label:"حداقل بدهی کاربر :", validate:"NotEmpty,ValidInteger",value:"10000", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"UserDebitLastSeenHours", label:"آخرین اتصال کاربر تا (ساعت) :", validate:"NotEmpty,ValidInteger",value:"1", maxLength:3, inputWidth:100, info:"true",labelWidth:170},
				{ type:"input" , name:"UserDebitMessage", label:"متن پیام :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[RTrM]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
				{ type:"input" , name:"UserDebitSendTime", label:"زمان مجاز ارسال :",value:'Al0800-2300',maxLength:120, validate:"NotEmpty,IsValidLoginTime", labelAlign:"left", inputWidth:300,note: { text: "<span style='direction:rtl;float:right;text-align:justify;padding-bottom:40px;'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;su:یکشنبه&nbsp;mo:دوشنبه&nbsp;tu:سه شنبه&nbsp;we:چهارشنبه&nbsp;th:پنج شنبه&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo اعلان فقط روزهای دوشنبه ارسال خواهد شد.</br>درحالتی که بخواهیم در روز،ساعات خاصی را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ اعلان ارسال شود</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ اعلان ارسال شود </span>"}},
			]},
		]},
		
	];
	 	 	 	 	 	 
	var Form1StrEdit = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"Notify_Id", label:"شناسه اعلان :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال:", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"NotifyName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"NormalMinHourAfterPriorSend", label:"ارسال مجدد پیامک در شرایط حداقل(ساعت) :",maxLength:5, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:122,labelWidth:248},
		{ type:"input" , name:"FinishMinHourAfterPriorSend", label:"ارسال مجدد پیامک در شرایط اتمام(ساعت) :",maxLength:5, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:122,labelWidth:248},
		{type: "select", name:"NotifyType",label: "نوع اعلان :" ,value:"Traffic",validate:"",required:true,options:[
			{text: "اعلان پایان اعتبار", value: "CreditFinishNotify"},
			{text: "اعلان پایان سرویس", value: "ServiceExpireNotify"},
			{text: "اعلان بدهی کاربر", value: "UserDebitNotify"},
		]},
		{hidden:true, type:"input" , name:"MinCreditTime", label:"حداقل اعتبار زمانی(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"3600", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"MinCreditTraffic", label:"حداقل اعتبار ترافیک(مگابایت) :", validate:"NotEmpty,ValidInteger",value:"300", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"CreditFinishLastSeenHours", label:"آخرین اتصال کاربر تا (ساعت) :", validate:"NotEmpty,ValidInteger",value:"1", maxLength:3, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"BeforeCreditFinishMessage", label:"متن پیام در شرایط حداقل اعتبار :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[RTrM]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
		{hidden:true, type:"input" , name:"AfterCreditFinishMessage", label:"متن پیام در شرایط اتمام :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[RTrM]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
		{hidden:true,  type:"input" , name:"CreditFinishSendTime", label:"زمان مجاز ارسال :",value:'Al0800-2300',maxLength:120, validate:"NotEmpty,IsValidLoginTime", labelAlign:"left", inputWidth:300,note: { text: "<span style='direction:rtl;float:right;text-align:justify;padding-bottom:40px;'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;su:یکشنبه&nbsp;mo:دوشنبه&nbsp;tu:سه شنبه&nbsp;we:چهارشنبه&nbsp;th:پنج شنبه&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo اعلان فقط روزهای دوشنبه ارسال خواهد شد.</br>درحالتی که بخواهیم در روز،ساعات خاصی را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ اعلان ارسال شود</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ اعلان ارسال شود </span>"}},
		
		{hidden:true, type:"input" , name:"MinActiveDays", label:"حداقل روز باقیمانده :", validate:"NotEmpty,ValidInteger",value:"3", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"ServiceExpireLastSeenHours", label:"آخرین اتصال کاربر تا (ساعت) :", validate:"NotEmpty,ValidInteger",value:"720", maxLength:3, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"BeforeServiceExpireMessage", label:"متن پیام در شرایط حداقل :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[ShExpireDate]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
		{hidden:true, type:"input" , name:"AfterServiceExpireMessage", label:"متن پیام بعد از انقضا سرویس :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[ShExpireDate]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
		{hidden:true, type:"input" , name:"ServiceExpireSendTime", label:"زمان مجاز ارسال :",value:'Al0800-2300',maxLength:120, validate:"NotEmpty,IsValidLoginTime", labelAlign:"left", inputWidth:300,note: { text: "<span style='direction:rtl;float:right;text-align:justify;padding-bottom:40px;'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;su:یکشنبه&nbsp;mo:دوشنبه&nbsp;tu:سه شنبه&nbsp;we:چهارشنبه&nbsp;th:پنج شنبه&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo اعلان فقط روزهای دوشنبه ارسال خواهد شد.</br>درحالتی که بخواهیم در روز،ساعات خاصی را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ اعلان ارسال شود</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ اعلان ارسال شود </span>"}},

		{hidden:true, type:"input" , name:"MinUserDebit", label:"حداقل بدهی کاربر :", validate:"NotEmpty,ValidInteger",value:"10000", maxLength:9, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"UserDebitLastSeenHours", label:"آخرین اتصال کاربر تا (ساعت) :", validate:"NotEmpty,ValidInteger",value:"1", maxLength:3, inputWidth:100, info:"true",labelWidth:170},
		{hidden:true, type:"input" , name:"UserDebitMessage", label:"متن پیام :",maxLength:250, rows:3,validate:"NotEmpty", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>"+"می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Company]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[RTrM]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[UserDebit]")+"</span>"}},
		{hidden:true, type:"input" , name:"UserDebitSendTime", label:"زمان مجاز ارسال :",value:'Al0800-2300',maxLength:120, validate:"NotEmpty,IsValidLoginTime", labelAlign:"left", inputWidth:300,note: { text: "<span style='direction:rtl;float:right;text-align:justify;padding-bottom:40px;'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;su:یکشنبه&nbsp;mo:دوشنبه&nbsp;tu:سه شنبه&nbsp;we:چهارشنبه&nbsp;th:پنج شنبه&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo اعلان فقط روزهای دوشنبه ارسال خواهد شد.</br>درحالتی که بخواهیم در روز،ساعات خاصی را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ اعلان ارسال شود</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ اعلان ارسال شود </span>"}},
	];

	var PermitView=ISPermit("Admin.Message.Notify.View");
	var PermitAdd=ISPermit("Admin.Message.Notify.Add");
	var PermitEdit=ISPermit("Admin.Message.Notify.Edit");
	var PermitDelete=ISPermit("Admin.Message.Notify.Delete");

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);

	// TabbarMain   ===================================================================
	var TabbarMain = dhxLayout.cells("a").attachTabbar();
	DSTabbarInitial(TabbarMain,TabbarMainArray);

	// Toolbar1   ===================================================================
	var Toolbar1 = TabbarMain.cells(0).attachToolbar();
	DSToolbarInitial(Toolbar1);
	
	// Form1   ===================================================================
	var Form1;
	if(RowId>0){
		Form1=DSInitialForm(TabbarMain.cells(0),Form1StrEdit,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(PermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!PermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(PermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function CreateSMSItems(Item){
	return "<a href='javascript:void(0)' onclick='CopyTextToClipBoard(\""+Item+"\");' style='text-decoration:none' title='برای کپی کلیک کنید'>"+Item+"</a>";
}
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnSaveClick(){
	//if(DSFormValidate(Form1,Form1FieldHelpId))
	if (Form1.validate())
	{
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
		else{//insert
			DSFormInsertRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=insert",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
		}//insert
	}
	else dhxLayout.progressOff();
}

function Form1OnButtonClick(){
}

function TopToolbar_OnExitClick(){
	TopToolbar.attachEvent("onclick",function(id){
		if(id=="Exit"){parent.dhxLayout.dhxWins.window("popupWindow").close();}	
	});
}	
function Form1DoAfterLoadOk(){
	parent.dhxLayout.dhxWins.window("popupWindow").setText("ویرایش "+DataTitle+" ["+Form1.getItemValue(Form1TitleField)+"]");
	var NotifyType=Form1.getItemValue('NotifyType');
	if(NotifyType=='CreditFinishNotify'){
		FormRemoveItem(Form1,['MinActiveDays','ServiceExpireLastSeenHours','BeforeServiceExpireMessage','AfterServiceExpireMessage','ServiceExpireSendTime','MinUserDebit','UserDebitLastSeenHours','UserDebitMessage','UserDebitSendTime']);
		FormShowItem(Form1,['MinCreditTime','MinCreditTraffic','CreditFinishLastSeenHours','BeforeCreditFinishMessage','AfterCreditFinishMessage','CreditFinishSendTime']);
	}
	else if(NotifyType=='ServiceExpireNotify'){
		FormRemoveItem(Form1,['MinCreditTime','MinCreditTraffic','CreditFinishLastSeenHours','BeforeCreditFinishMessage','AfterCreditFinishMessage','CreditFinishSendTime','MinUserDebit','UserDebitLastSeenHours','UserDebitMessage','UserDebitSendTime']);
		FormShowItem(Form1,['MinActiveDays','ServiceExpireLastSeenHours','BeforeServiceExpireMessage','AfterServiceExpireMessage','ServiceExpireSendTime']);
	}
	else if(NotifyType=='UserDebitNotify'){
		FormRemoveItem(Form1,['MinCreditTime','MinCreditTraffic','CreditFinishLastSeenHours','BeforeCreditFinishMessage','AfterCreditFinishMessage','CreditFinishSendTime','MinActiveDays','ServiceExpireLastSeenHours','BeforeServiceExpireMessage','AfterServiceExpireMessage','ServiceExpireSendTime']);
		FormShowItem(Form1,['MinUserDebit','UserDebitLastSeenHours','UserDebitMessage','UserDebitSendTime']);
	}

	
	dhxLayout.progressOff();
}	

function Form1DoAfterLoadFail(){
	dhxLayout.progressOff();
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
	dhxLayout.progressOff();

}
function Form1DoAfterUpdateFail(){
	dhxLayout.progressOff();
}
function Form1DoAfterInsertOk(r){
	RowId=r;
	parent.UpdateGrid(r);
	LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
	Form1.unload();
	Form1=DSInitialForm(TabbarMain.cells(0),Form1StrEdit,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!PermitEdit){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	dhxLayout.progressOff();
	
}
function Form1DoAfterInsertFail(){
	dhxLayout.progressOff();
}

function LoadTabbarMain(f_TabbarMain,f_TabbarMainArray,f_RowId){
	
	for (var i=1;i<f_TabbarMainArray.length;i++)
	if(ISPermit(f_TabbarMainArray[i][3])){
		f_TabbarMain.addTab(i,f_TabbarMainArray[i][0] , f_TabbarMainArray[i][2]);
		f_TabbarMain.setContentHref(i,f_TabbarMainArray[i][1]+".php?"+un()+"&ParentId="+f_RowId+"&"+f_TabbarMainArray[i][4]);
	}
	//f_TabbarMain.normalize();
}

	
}

	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>