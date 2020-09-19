<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSWebAccessEdit ....................................................................................");
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
			margin: 10px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background-color:white;
        }
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="دسترسی پنل کاربر";
	var DataName="DSWebAccess_";
	var ChangeLogDataName='WebAccess';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSWebAccess_Edit","70","Admin.User.WebAccess.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.WebAccess.ChangeLog.List","ChangeLogDataName=WebAccess"]
					];
	//=======Form1 WebAccess Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
		WebAccessName:'نام دسترسی - انگلیسی - ۳۲ کاراکتر و غیر قابل ویرایش پس از دخیره',
		CanWebAccess:'دسترسی کاربر برای ورود به پنل کاربری',
		SessionTimeout:'بر حسب ثانیه - از ۶۰۰ تا ۹۹۹۹',
		ShowDailyUsage:'مجوز کاربر برای نمایش مصرف روزانه در پنل کاربری',
		ShowPaymentHistory:'مجوز کاربر برای نمایش سوابق پرداخت در پنل کاربری',
		ShowServiceHistory:'مجوز کاربر برای نمایش سوابق سرویس/ها در پنل کاربری',
		ShowGiftHistory:'مجوز کاربر برای نمایش سوابق هدایا در پنل کاربری',
		ShowInstallmentHistory:'مجوز کاربر برای نمایش سوابق اقساط در پنل کاربری',
		ShowConnectionHistory:'مجوز کاربر برای نمایش سوابق اتصالات در پنل کاربری',
		CanChangePassword:'مجوز کاربر برای تغییر کلمه عبور در پنل کاربری',
		CanGetEmergencyTraffic:'مجوز کاربر برای استفاده از ترافیک اضطراری (اگر تعریف شده باشد) در پنل کاربری',
		AutoWebLogin:'ورود خودکار کاربر به پنل کاربری در صورتی که آنلاین باشد و آی پی کاربر تغییر نکرده باشد',
		AutoWebLoginDelay:'شمارش معکوس برای ورود خودکار کاربر.این گزینه باعث می شود کاربر بتواند از ورود خودکار انصراف دهد تا با نام کاربری دیگری وارد پنل شود',
		AutoWebLoginMode:'این گزینه به شما این انتخاب را می دهد که ورود خودکار را برای همه کاربران و یا فقط برای کاربرانی که قانون اتمام شامل آن ها شده است،فعال کنید',
		CanPayMoney:'مجوز کاربر برای پرداخت و شارژ پنل کاربری',
		MinPay:'حداقل مبلغی که کاربر می تواند برای شارژ پنل اقدام به پرداخت کند(به جز مبلغ بدهی اش)',
		ShowSendFile:'مجوز کاربر برای نمایش فایل های ارسالی( گزینه مدارک در پنل کاربری)',
		CanSendFile:'مجوز کاربر برای ارسال فایل در پنل کاربری(مدارک در پنل کاربری)',
		CanActiveServiceReserve:'مجوز کاربر برای فعال کردن سرویس رزرو در صورت وجود در پنل کاربری',
		CanBuyServiceBase:'مجوز کاربر برای خرید سرویس پایه در پنل کاربری',
		CanBuyServiceExtraTraffic:'مجوز کاربر برای خرید سرویس اضافه ترافیک در پنل کاربری',
		CanBuyServiceExtraTime:'مجوز کاربر برای خرید سرویس اضافه زمان در پنل کاربری',
		CanBuyServiceIP:"مجوز کاربر برای خرید سرویس آی پی در پنل کاربری",
		CanBuyServiceOther:"مجوز کاربر برای خرید سرویس سایر در پنل کاربری",
		ServiceOtherButtonWebTitleFa:"عنوان فارسی برای دکمه خرید سرویس سایر در پنل کاربری",
		ServiceOtherButtonWebTitleEn:"عنوان لاتین برای دکمه خرید سرویس سایر در پنل کاربری",
		CanInvoice:"مجوز کاربر برای چاپ فاکتور در پنل کاربری",
		CanTransferCredit:'مجوز کاربر برای انتقال اعتبار به کاربر دیگر',
		MinTransferAmount:'حداقل مقدار ترافیک به مگابایت که هر بار کاربر می تواند منتقل کند',
		MaxTransferAmount:'حداکثر مقدار ترافیک به مگابایت که هر بار کاربر می تواند منتقل کند<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		NonTransferableAmount:'حداقل مقدار ترافیک به مگابایت که باید برای خود کاربر باقی بماند و نمی تواند آن را انتقال دهد',
		YearlyTransferCountLimit:'محدودیت سالانه کاربر برای تعداد انتقال<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		YearlyTransferAmountLimit:'محدودیت سالانه برای مجموع مقدار ترافیک قابل انتقال توسط کاربر به مگابایت<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		MonthlyTransferCountLimit:'محدودیت ماهیانه کاربر برای تعداد انتقال<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		MonthlyTransferAmountLimit:'محدودیت ماهیانه برای مجموع مقدار ترافیک قابل انتقال توسط کاربر به مگابایت<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		DailyTransferCountLimit:'محدودیت روزانه کاربر برای تعداد انتقال<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		DailyTransferAmountLimit:'محدودیت روزانه برای مجموع مقدار ترافیک قابل انتقال توسط کاربر به مگابایت<br/>برای عدم محدودیت عدد ۰ را وارد کنید',
		CanDisconnect:'کاربر می تواند از طریق پنل کاربری،اتصال خودش را قطع کند',
		ShowAgreement:'pdf نمایش توافقنامه با فرمت'
	};
	var Form1FieldHelpId=["WebAccessName","CanWebAccess","SessionTimeout","ShowDailyUsage","ShowPaymentHistory","ShowServiceHistory","ShowGiftHistory","ShowInstallmentHistory","ShowConnectionHistory","CanChangePassword","CanGetEmergencyTraffic","AutoWebLogin",'AutoWebLoginDelay',"AutoWebLoginMode","CanPayMoney","MinPay","ShowSendFile","CanSendFile","CanActiveServiceReserve","CanBuyServiceBase","CanBuyServiceExtraTraffic","CanBuyServiceExtraTime","CanBuyServiceIP","CanBuyServiceOther","ServiceOtherButtonWebTitleFa","ServiceOtherButtonWebTitleEn","CanInvoice","CanTransferCredit","MinTransferAmount","MaxTransferAmount","NonTransferableAmount","YearlyTransferCountLimit","YearlyTransferAmountLimit","MonthlyTransferCountLimit","MonthlyTransferAmountLimit","DailyTransferCountLimit","DailyTransferAmountLimit","CanDisconnect","ShowAgreement"];
	
	var Form1TitleField="WebAccessName";
	var Form1DisableItems=["WebAccessName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="WebAccessName";
										
	var Form1Str = [
		{ type:"settings" , labelWidth:185, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"WebAccess_Id", label:"WebAccess_Id :",disabled:true, labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"WebAccessName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:true, inputWidth:150},
		// { type: "label"},
		// {type:"block",width:670, list:[
			{ type: "select", name:"CanWebAccess", label: "دسترسی به پنل :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
		// ]},
		// { type: "label"},
		{type:"block",name:"BodyBlock", width:700, list:[
			{ type: "input" , name:"SessionTimeout", label:"مهلت نشست کاربردر حالت بیکار :", validate:"NotEmpty,ValidInteger",value:600, labelAlign:"left", maxLength:4,inputWidth:102,info:true},
			{ type: "select", name:"ShowDailyUsage", label: "نمایش مصرف روزانه :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowPaymentHistory", label: "نمایش سوابق پرداخت :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowServiceHistory", label: "نمایش سوابق سرویس :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowGiftHistory", label: "نمایش سوابق هدیه :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanActivateGift", label: "بتواند هدیه را فعال کند؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanAbandonGift", label: "انصراف از هدیه؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowInstallmentHistory", label: "نمایش سوابق اقساط :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowConnectionHistory", label: "نمایش سوابق اتصالات :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanChangePassword", label: "بتواند رمز عبور را تغییر دهد؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanGetEmergencyTraffic", label: "بتواندترافیک اضطراری بگیرد؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"AutoWebLogin", label: "ورود خودکار :", options:[
				{text: "خیر", value: "No"},
				{text: "بلی", value: "Yes",selected: true,list:[
					{ type: "input" , name:"AutoWebLoginDelay", label:"تاخیر ورود خودکار(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"5", labelAlign:"left", maxLength:1,inputWidth:102,info:true},
					{type: "select", name:"AutoWebLoginMode", label: "ورود خودکار برای :", options:[{text: "Finish Users", value: "OnFinish"},{text: "All Users", value: "Always",selected: true}],inputWidth:100,required:true, info:true}
				]}
			],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanPayMoney", label: "بتواند پرداخت کند؟:", options:[
				{text: "خیر", value: "No"},
				{text: "بلی", value: "Yes",selected: true,list:[
					{ type: "input" , name:"MinPay", label:"حداقل پرداخت (ریال):", validate:"NotEmpty",value:"0", labelAlign:"left", maxLength:14,inputWidth:102,info:true,numberFormat: "<?php  echo $PriceFormat;  ?>"},
				]}
			],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowSendFile", label: "نمایش فایل ارسالی :", options:[
				{text: "خیر", value: "No"},
				{text: "بلی", value: "Yes",selected: true,list:[
					{ type: "select", name:"CanSendFile", label: "بتواند فایل ارسال کند؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
				]}
			],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanInvoice", label: "نمایش فاکتور؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{type:"newcolumn",offset:40},
			{ type: "select", name:"CanActiveServiceReserve", label: "بتواند سرویس رزرو را فعال کند :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanBuyServiceBase", label: "خرید سرویس پایه؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanBuyServiceExtraTraffic", label: "خرید سرویس اضافه ترافیک؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanBuyServiceExtraTime", label: "خرید سرویس اضافه زمان؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanBuyServiceIP", label: "خرید سرویس آی پی؟ :", options:[{text: "خیر", value: "No"},{text: "بلی", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanBuyServiceOther", label: "خرید سرویس سایر؟ :", options:[
				{text: "خیر", value: "No"},
				{text: "بلی", value: "Yes",selected: true,list:[
					{ type: "input", name:"ServiceOtherButtonWebTitleFa", label: "عنوان فارسی دکمه سرویس سایر :", value:"خرید سرویس ویژه", maxLength:32,inputWidth:115,required:true, info:true},
					{ type: "input", name:"ServiceOtherButtonWebTitleEn", label: "عنوان لاتین دکمه سرویس سایر :", value:"Buy Special Service", maxLength:32,inputWidth:115,required:true, info:true}
				]}
			],inputWidth:100,required:true, info:true},		
			{ type: "select", name:"CanTransferCredit", label: "انتقال اعتبار؟ :", options:[
				{text: "خیر", value: "No"},
				{text: "بلی", value: "Yes",selected: true,list:[
					{ type: "input", name:"MinTransferAmount", label: "حداقل ترافیک قابل انتقال(مگابایت) :",validate:"NotEmpty,ValidInteger", value:0, maxLength:9,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"MaxTransferAmount", label: "حداکثر ترافیک قابل انتقال(مگ) :",validate:"NotEmpty,ValidInteger", value:0, maxLength:9,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"NonTransferableAmount", label: "حداقل ترافیک باقیمانده لازم(مگ) :",validate:"NotEmpty,ValidInteger", value:512, maxLength:9,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"YearlyTransferCountLimit", label: "تعداد انتقال مجاز در سال:",validate:"NotEmpty,ValidInteger", value:0, maxLength:4,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"YearlyTransferAmountLimit", label: "ترافیک مجاز قابل انتقال در سال:",validate:"NotEmpty,ValidInteger", value:0, maxLength:9,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"MonthlyTransferCountLimit", label: "تعداد انتقال مجاز در ماه:",validate:"NotEmpty,ValidInteger", value:0, maxLength:4,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"MonthlyTransferAmountLimit", label: "ترافیک مجاز قابل انتقال در ماه:",validate:"NotEmpty,ValidInteger", value:0, maxLength:9,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"DailyTransferCountLimit", label: "تعداد انتقال مجاز در روز:",validate:"NotEmpty,ValidInteger", value:0, maxLength:4,inputWidth:102,required:true, info:true},					
					{ type: "input", name:"DailyTransferAmountLimit", label: "ترافیک مجاز قابل انتقال در روز:",validate:"NotEmpty,ValidInteger", value:0, maxLength:9,inputWidth:102,required:true, info:true},					
				]}
			],inputWidth:100,required:true, info:true},
			{ type: "select", name:"CanDisconnect", label: "بتواند قطع اتصال کند :", options:[{text: "No", value: "No"},{text: "Yes", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
			{ type: "select", name:"ShowAgreement", label: "نمایش توافقنامه :", options:[{text: "No", value: "No"},{text: "Yes", value: "Yes",selected: true}],inputWidth:100,required:true, info:true},
		]},
		{ type: "label"},{ type: "label"},{ type: "label"},{ type: "label"},
		];

	var PermitView=ISPermit("Admin.User.WebAccess.View");
	var PermitAdd=ISPermit("Admin.User.WebAccess.Add");
	var PermitEdit=ISPermit("Admin.User.WebAccess.Edit");
	var PermitDelete=ISPermit("Admin.User.WebAccess.Delete");

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
	Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onChange",function(){
		if(Form1.getItemValue("CanWebAccess")=="Yes")
			Form1.enableItem("BodyBlock");
		else{
			Form1.disableItem("BodyBlock");
			MyHint.hide();
		}
	});
	
	MyHint = new dhtmlXPopup({form: Form1,id:["SessionTimeout"],mode:"right"});
	MyHint.attachEvent("onContentClick",function(){MyHint.hide()});
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	Form1.attachEvent("onFocus",function(name){
		if(name=="SessionTimeout")
			Form1OnInputChange("SessionTimeout",Form1.getItemValue("SessionTimeout"));
		else
			MyHint.hide();
	});	
	
	
	
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(PermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!PermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(PermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnInputChange(name,value){
	if(name=="SessionTimeout"){
		if(value==parseInt(value)){
			MyHint.attachHTML("<div style='color:blue;text-align:center;font-weight:bold'>&nbsp;&nbsp;"+GetTimeString(value)+"&nbsp;&nbsp;</div>");
			MyHint.show("SessionTimeout");
		}
		else
			MyHint.hide();
	}	
}

function GetTimeString(t){
	var seconds = Math.floor(t % 60 );
	var minutes = Math.floor((t/60) % 60 );
	var hours = Math.floor((t/(3600)) % 24 );
	var days = Math.floor(t/86400 );
	return (days>0?(days+" Day"+(days>1?"s":"")+" & "):"")+(hours<10?"0"+hours:hours)+":"+(minutes<10?"0"+minutes:minutes)+":"+(seconds<10?"0"+seconds:seconds);
}

function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
		else{//insert
			DSFormInsertRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=insert",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
			
		}//insert
	}
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
	if(Form1.getItemValue("CanWebAccess")=="Yes")
		Form1.enableItem("BodyBlock");
	else
		Form1.disableItem("BodyBlock");
}

function Form1DoAfterLoadFail(){
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
}
function Form1DoAfterUpdateFail(){
}
function Form1DoAfterInsertOk(r){
	RowId=r;
	parent.UpdateGrid(r);
	LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EditEnableItems,Form1HideItems,Form1ShowItems);
	if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!PermitEdit){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	
}
function Form1DoAfterInsertFail(){
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