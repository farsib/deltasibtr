<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSService_Edit ....................................................................................");
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
			margin: 0px;
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
	var DataTitle="سرویس";
	var DataName="DSService_";
	var TabbarMain,TopToolbar;
	var isadddcButton=false;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSService_Edit","90","CRM.Service.Edit",""],
					["هدیه","DSService_Gift_List","60","CRM.Service.Gift.List","ParamItemGroup=Gift"],
					["پارامتر","DSParam_List","70","CRM.Service.Param.List","ParamItemGroup=Service"],
					["دسترسی دسته ها","DSService_ClassAccess_List","110","CRM.Service.ClassAccess.List",""],
					["دسترسی ارائه دهنده مجازی اینترنت","DSService_VispAccess_List","200","CRM.Service.VispAccess.List",""],
					["دسترسی نمایندگان فروش","DSService_ResellerAccess_List","150","CRM.Service.ResellerAccess.List",""],
					["دسترسی سرویس پایه","DSService_ServiceBaseAccess_List","140","CRM.Service.ServiceBaseAccess.List",""],
					["کاربران","DSService_Users_List","70","CRM.Service.Users.List",""],
					["لیست تغییرات","DSChangeLog","115","CRM.Service.ChangeLog.List","ChangeLogDataName=Service"]
					];
	//=======Form1 Service Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	ServiceName:'حداکثر ۱۲۸ کاراکتر',
							Category1:'دسته بندی سرویس بر اساس گروه',
							Category2:'دسته بندی سرویس بر اساس سرعت',
							Price:'عدد',
							OffRate:'مشخص می کند که تخفیف کاربر در محاسبه پرداخت سرویس تاثیر دارد یا خیر',
							Speed:'سرعت این سرویس،مقدار این فیلد تاثیری بر سرعت سرویس ندارد و تنها برای گزارش مورد استفاده است',
							STrA:'برای ترافیک نامحدود،عدد ۰ را بگذارید',
							YTrA:'برای عدم محدودیت عدد ۰ را بگذارید',
							MTrA:'برای عدم محدودیت عدد ۰ را بگذارید',
							WTrA:'برای عدم محدودیت عدد ۰ را بگذارید',
							DTrA:'برای عدم محدودیت عدد ۰ را بگذارید',

							STiA:'برای زمان نامحدود،عدد ۰ را بگذارید',
							YTiA:'برای عدم محدودیت عدد ۰ را بگذارید',
							MTiA:'برای عدم محدودیت عدد ۰ را بگذارید',
							WTiA:'برای عدم محدودیت عدد ۰ را بگذارید',
							DTiA:'برای عدم محدودیت عدد ۰ را بگذارید',

							ActiveYear:'از ۰ تا ۹۹',
							ActiveMonth:'از ۰ تا ۹۹',
							ActiveDay:'از ۰ تا ۹۹',

							ExtraTraffic:'Extra Traffic Credit(MegaByte)',
							ExtraTime:'Extra Time Credit(Second)',
							
							MaxYearlyCount:'حداکثر تعداد استفاده از این سرویس در طول سال برای هر کاربر</br>برای عدم محدودیت عدد ۰ را بگذارید',
							MaxMonthlyCount:'حداکثر تعداد استفاده از این سرویس در طول ماه برای هر کاربر</br>برای عدم محدودیت عدد ۰ را بگذارید',
							UserChoosable:'کاربر در پنل کاربری بتواند این سرویس را مشاهده و انتخاب کند',
							ResellerChoosable:'نماینده بتواند این سرویس را انتخاب و برای کاربر اضافه نماید',
							AvailableFromDate:'Service is choosable only after This Date,leave blank for no limit',
							AvailableToDate:'Service is choosable only before This Date,leave blank for no limit',
							Installment:'Number of Installment(monthly) that Huser will pay.',
							AttachedGift_Id:'این هدیه به کاربر اضافه خواهد شد و به صورت خودکار با فعال شدن سرویس،فعال می شود',
							OnBuyFromWebsiteSMS:'اگر متنی وارد کنید،پس از اینکه کاربر این سرویس را از پنل کاربری خریداری نمود،این متن برای او ارسال خواهد شد',
							OnAddByResellerSMS:'اگر متنی وارد کنید،پس از اینکه نماینده فروش این سرویس را برای کاربر اضافه نمود،این متن برای کاربر ارسال خواهد شد',
							SMSExpireTime:'تعداد ثانیه هایی که سرور سعی می کند پیامک را پس از افزودن سرویس،ارسال کند</br>اگر این زمان قبل از ارسال سپری شود،پیامک منقضی خواهد شد(مقدار ۶۰ تا ۹۹۹۹۹ ثانیه)',
							ISFairService:'مشخص می کند که کاربر پس از اتمام زمان و ترافیک بتواند متصل شود یا خیر',
							FairMikrotikRate_Id:'سرعت میکروتیک در حالت مصرف منصفانه'
							};
	var Form1FieldHelpId=["ServiceName","Category1","Category2","Price","OffRate",
						  "STrA","YTrA","MTrA","WTrA","DTrA","Speed",
						  "STiA","YTiA","MTiA","WTiA","DTiA",
						  "ActiveYear","ActiveMonth","ActiveDay",
						  "ExtraTraffic","ExtraTime",
						  "MaxYearlyCount","MaxMonthlyCount","UserChoosable","ResellerChoosable",'Installment',
						  "AttachedGift_Id","ISFairService","FairMikrotikRate_Id","OnBuyFromWebsiteSMS","OnAddByResellerSMS","SMSExpireTime"];
	var Form1TitleField="ServiceName";
	var Form1DisableItems=["ServiceType"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="MaxYearlyCount";
	var Form1Str = [
	{ type:"settings",labelWidth:180,offsetLeft:10},
	{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:true},
	{ type: "label"},

	{ type:"hidden" , name:"Service_Id", label:"Service_Id :",disabled:true, labelAlign:"left", inputWidth:130},
	{ type:"hidden" , name:"UsedCount"},
	{ type: "select", name:"ServiceType", label: "نوع سرویس :", options:[{text: "پایه", value: "Base",selected: true},{text: "آی پی", value: "IP"},{text: "سایر", value: "Other"},{text: "اعتبار اضافی", value: "ExtraCredit"}],inputWidth:108,required:true, info:false},
	{ type:"input" , name:"ServiceName", label:"نام سرویس :", validate:"NotEmpty", labelAlign:"left", maxLength:128,inputWidth:435, required:true, info:true},
	{ type:"input" , name:"Description", label:"توضیحات :", validate:"", labelAlign:"left", maxLength:254,inputWidth:435},
	{ type:"input" , name:"FramedIP", label:"آی پی کاربر :", validate:"ValidIPv4",value:"", maxLength:15,inputWidth:110, required:false, info:true},
	{ type:"input" , name:"FramedRoute", label:"کلاس آی پی :", validate:"ISPermitIp",value:"", maxLength:128,inputWidth:300, required:false, info:true},
	{type: "block", width: 700, list:[
		{ type: "select", name:"ISEnable", label: "فعال؟ :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:108,required:true},
		{ type: "input" , name:"Price", label:"قیمت :", validate:"IsValidPrice",value:"0", maxLength:14,inputWidth:110, required:true, info:true,numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{ type: "select", name:"OffRate", label: "استفاده از تخفیف :", options:[{text: "بلی", value: "1.00",selected: true},{text: "خیر", value: "0.00"}],inputWidth:108,required:true,info:true},
		{ type: "input" , name:"InstallmentNo", label:"تعداد اقساط :", validate:"ValidInteger",value:"0", maxLength:10,inputWidth:110, required:true, info:true},
		{ type: "input" , name:"InstallmentPeriod", label:"مدت اقساط (ماه) :", validate:"ValidInteger",value:"0", maxLength:2,inputWidth:110, required:true, info:true},
		{ type: "select" , name:"InstallmentFirstCash", label:"قسط اول زمان ثبت پرداخت شود؟ :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:108,required:true},
		{ type: "input" , name:"AvailableFromDate", label:"دسترسی از تاریخ :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:110, info:true},
		{ type: "input" , name:"AvailableToDate", label:"خاتمه در دسترس بودن :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:110, info:true},
		{ type:"input" , name:"MaxYearlyCount", label:"سقف تعداد خرید کاربر در سال :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:3,inputWidth:110, required:true, info:true},
		{ type:"input" , name:"MaxMonthlyCount", label:"سقف تعداد خرید کاربر در ماه :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:3,inputWidth:110, required:true, info:true},
		{ type:"input" , name:"MaxActiveCount", label:"سقف تعداد فعال از این سرویس :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:3,inputWidth:110, required:true, info:true},
		{ type: "select", name:"UserChoosable", label: "قابل مشاهده توسط کاربر :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:108,required:true, info:true},
		{ type: "select", name:"ResellerChoosable", label: "قابل مشاهده توسط نماینده  :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:108,required:true, info:true},
		{ type:"input" , name:"Category1", label:"دسته بندی ۱(گروه) :", validate:"", labelAlign:"left", maxLength:50,inputWidth:140,info:true},
		{ type:"input" , name:"Category2", label:"دسته بندی ۲(سرعت) :", validate:"", labelAlign:"left", maxLength:50,inputWidth:140,info:true},
		{ type:"input" , name:"Speed", label:"سرعت سرویس :", validate:"NotEmpty",value:"0", maxLength:10,inputWidth:140, required:false, info:true},
		{ type:"input" , name:"IPCount", label:"تعداد آی پی :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:7,inputWidth:110, required:true, info:true},
		{type: "newcolumn", offset:10},
		{ type:"input" , name:"ActiveYear", label:"تعداد سال :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:2,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"ActiveMonth", label:"تعداد ماه :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:2,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"ActiveDay", label:"تعداد روز :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:2,inputWidth:110, required:true, info:true,labelWidth:180},
		{type: "select", name:"ExtraType",label: "نوع اعتبار :" ,labelWidth:180,value:"Traffic",validate:"",required:true,options:[
			{text: "ترافیک", value: "Traffic",list: [{ type:"input" , name:"ExtraTraffic", label:"ترافیک اضافی(مگابایت) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9, inputWidth:110, required:true, info:false,labelWidth:170}]},
			{text: "زمان", value: "Time",list: [{ type:"input" , name:"ExtraTime", label:"زمان اضافی(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:false,labelWidth:170}]},
			{text: "روز", value: "ActiveDay",list: [{ type:"input" , name:"ExtraActiveDay", label:"تعداد روز اضافی :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:4,inputWidth:110, required:true, info:true,labelWidth:170}]}	
		]},
		{ type:"input" , name:"STrA", label:"ترافیک سرویس(مگابایت) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9, inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"YTrA", label:"محدودیت ترافیک سالانه(مگ) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"MTrA", label:"محدودیت ترافیک ماهیانه(مگ) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"WTrA", label:"محدودیت ترافیک هفتگی(مگ) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"DTrA", label:"محدودیت ترافیک روزانه(مگ) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"STiA", label:"زمان سرویس(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"YTiA", label:"محدودیت زمان سالانه(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"MTiA", label:"محدودیت زمان ماهیانه(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"WTiA", label:"محدودیت زمان هفتگی(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180},
		{ type:"input" , name:"DTiA", label:"محدودیت زمان روزانه(ثانیه) :", validate:"NotEmpty,ValidInteger",value:"0", maxLength:9,inputWidth:110, required:true, info:true,labelWidth:180}
	]},
	{ type: "select", name:"ISFairService", label: "مصرف منصفانه هست؟ :", options:[
		{text: "خیر", value: "No",selected: true},
		{text: "بلی", value: "Yes"}
	],inputWidth:108,required:true,info:true},
	{ type: "select", name:"FairMikrotikRate_Id",label: "سرعت زمان مصرف منصفانه :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectMikrotikRate",required:true,validate:"IsID",inputWidth:433,info:true,hidden:true},
	{ type: "select", name:"AttachedGift_Id",label: "نام هدیه پیوست شده :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectGift",required:true,validate:"IsID",inputWidth:433,info:true},
	{ type:"input" , name:"OnBuyFromWebsiteSMS", label:"پیامک زمان خرید در پنل کاربری :",maxLength:250, rows:3,validate:"", labelAlign:"left",inputWidth:435,note: { text: "<span style='direction:rtl;float:right'>در صورت عدم تمایل به ارسال پیامک،فیلد را خالی بگذارید<br/>می توانید از  متغیر ها به این صورت استفاده نمایید :"+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[PayPrice]</span>")},info:true},
	{ type:"input" , name:"OnAddByResellerSMS", label:"پیامک زمان افزودن توسط نماینده :",maxLength:250, rows:3,validate:"", labelAlign:"left",inputWidth:435,note: { text: "<span style='direction:rtl;float:right'>در صورت عدم تمایل به ارسال پیامک،فیلد را خالی بگذارید<br/>می توانید از متغیرها به این صورت استفاده نمایید :"+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]")+" , "+CreateSMSItems("[PayPrice]</span>")},info:true},
	{ type: "input" , name:"SMSExpireTime",label: "زمان انقضا پیامک (ثانیه):",value:'3600',validate:"^[6-9][0-9]|[1-9][0-9][0-9]+$",inputWidth:110,maxLength:5, required: true,info:true,disabled:true},
		{type:"label"},
		{type:"label"},
		{type:"label"},
	];

	//=======Popup2 Category
	var Popup2;
	var PopupId2=['Category1','Category2'];//  popup Attach to Which input of form
	//=======Form2 Category
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {Category1:''};
	var Form2FieldHelpId=[];
	var Form2Str=[
		{type: "select", name:"Category1",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectCategory&n=1",inputWidth:180},
		{type: "select", name:"Category2",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectCategory&n=2",inputWidth:180}
	];
	
	var AutoCompleteTime_Id;
	var Form2OnOptionLoadded_Event_Id="";
	
	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Service_Exit",TopToolbar_OnExitClick);

	// TabbarMain   ===================================================================
	var TabbarMain = dhxLayout.cells("a").attachTabbar();
	DSTabbarInitial(TabbarMain,TabbarMainArray);

	// Toolbar1   ===================================================================
	var Toolbar1 = TabbarMain.cells(0).attachToolbar();
	DSToolbarInitial(Toolbar1);
	
	// Form1   ===================================================================
	Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(ISPermit("CRM.Service.View"))	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(ISPermit("CRM.Service.Edit"))	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Service_Save",Toolbar1_OnSaveClick);
		if(!ISPermit("CRM.Service.Edit"))	FormDisableAllItem(Form1);
		if(ISPermit("CRM.Service.DisconnectUser")) 	DSToolbarAddButton(TopToolbar,null,"DisconnectUser","قطع اتصال کاربران","DisconnectUser",TopToolbar_OnDisconnectUserClick);
	}
	else{
		var HideItems=['FramedRoute','FramedIP','ExtraType','IPCount','ExtraTraffic','ExtraTime','ExtraActiveDay'];
		FormHideItem(Form1,HideItems);
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(ISPermit("CRM.Service.Add"))		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Service_Save",Toolbar1_OnSaveClick);
	}
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	Form1.attachEvent("onChange",Form1OnChange);
	Form1.attachEvent("onFocus",Form1OnFocus);
	
	Popup2 = new dhtmlXPopup({form: Form1,id:PopupId2,mode:"right"});
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,function(){});

	
	Form2.attachEvent("onOptionsLoaded", function(id) {
		
		var Opts = Form2.getSelect('Category1');
		Opts.size=5;
		Opts.onclick=function(){
			// dhtmlx.message('Category1');
			Form1.setItemValue('Category1',Form2.getItemValue('Category1'));
			Form1.getInput('Category1').style.direction=GetTextDirection(Form1.getItemValue('Category1'));
			Popup2.hide();
		};
		if(Opts.length>0)
			Opts.style.direction=GetTextDirection(Opts.options[0].text);
		Opts = Form2.getSelect('Category2');
		Opts.size=5;
		Opts.onclick=function(){
			// dhtmlx.message('Category2');
			Form1.setItemValue('Category2',Form2.getItemValue('Category2'));
			Form1.getInput('Category2').style.direction=GetTextDirection(Form1.getItemValue('Category2'));
			Popup2.hide();
		};
		if(Opts.length>0)
			Opts.style.direction=GetTextDirection(Opts.options[0].text);
	});
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
//myForm.setFocusOnFirstActive();

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnFocus(name){
	if((name=='Category1')||(name=='Category2')){
		var Opts = Form2.getSelect(name);
		if(Opts.length>=1){
			Form2.hideItem("Category1");
			Form2.hideItem("Category2");
			Form2.showItem(name);
			Popup2.show(name);
		}
		else
			Popup2.hide();
	}
	else
		Popup2.hide();
}

function Form1OnInputChange(id, value){
	if((id=="OnBuyFromWebsiteSMS")||(id=="OnAddByResellerSMS")){
		var tmp="";
		if(id=="OnBuyFromWebsiteSMS")
			tmp=Form1.getItemValue("OnAddByResellerSMS");
		else(id=="OnAddByResellerSMS")
			tmp=Form1.getItemValue("OnBuyFromWebsiteSMS");
		if((value=="")&&(tmp==""))
			Form1.disableItem("SMSExpireTime");		
		else
			Form1.enableItem("SMSExpireTime");
	}
}
function Form1OnChange(id, value){
	//alert("Change id="+id+" value="+value);
	if(id=='ServiceType'){
		var ServiceType=Form1.getItemValue("ServiceType");
		if(ServiceType=='Base'){
			var ShowItems=['Category2','MaxYearlyCount','MaxMonthlyCount','MaxActiveCount','ActiveYear','ActiveMonth','ActiveDay','Speed','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','AttachedGift_Id','ISFairService' ,'FairMikrotikRate_Id' ];
			FormShowItem(Form1,ShowItems);
			var HideItems=['FramedRoute','FramedIP','ExtraType','IPCount','ExtraTraffic','ExtraTime','ExtraActiveDay'];
			FormHideItem(Form1,HideItems);
			Form1.setItemLabel("Price", "قیمت :");
		}
		else if(ServiceType=='IP'){
			var ShowItems=['FramedRoute','FramedIP','IPCount'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['Category2','ActiveYear','ActiveMonth','ActiveDay','MaxYearlyCount','MaxMonthlyCount','MaxActiveCount','Speed','ExtraType','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','ExtraTraffic','ExtraTime','ExtraActiveDay','AttachedGift_Id','ISFairService' ,'FairMikrotikRate_Id' ];
			FormHideItem(Form1,HideItems);
			Form1.setItemValue("MaxActiveCount","1");
			Form1.setItemValue("MaxYearlyCount","0");
			Form1.setItemValue("MaxMonthlyCount","0");
			Form1.setItemLabel("Price", "قیمت روزانه سرویس:");
		}
		else if(ServiceType=='ExtraCredit'){
			var ShowItems=['ExtraType','ExtraTraffic','ExtraTime','ExtraActiveDay','MaxYearlyCount','MaxMonthlyCount'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['Category2','FramedRoute','FramedIP','ActiveYear','ActiveMonth','ActiveDay','IPCount','MaxActiveCount','Speed','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','AttachedGift_Id','ISFairService','FairMikrotikRate_Id'];
			FormHideItem(Form1,HideItems);
			Form1.setItemValue("MaxActiveCount","0");
			Form1.setItemLabel("Price", "قیمت :");
		}
		else if(ServiceType=='Other'){
			var ShowItems=['MaxYearlyCount','MaxMonthlyCount'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['Category2','FramedRoute','FramedIP','MaxActiveCount','ActiveYear','ActiveMonth','ActiveDay','Speed','ExtraType','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','IPCount','ExtraTraffic','ExtraTime','ExtraActiveDay','AttachedGift_Id','ISFairService' ,'FairMikrotikRate_Id' ];
			FormHideItem(Form1,HideItems);
			Form1.setItemValue("MaxActiveCount","0");
			Form1.setItemLabel("Price", "قیمت :");
		}
	}
	if(Form1.getItemValue("ISFairService")=="No")
		Form1.hideItem("FairMikrotikRate_Id");
	else
		Form1.showItem("FairMikrotikRate_Id");
}

function CreateSMSItems(Item){
	return "<a href='javascript:void(0)' onclick='CopyTextToClipBoard(\""+Item+"\")' style='text-decoration:none' title='برای کپی کلیک کنید'>"+Item+"</a>";
}
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function TopToolbar_OnDisconnectUserClick(){
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
		text: "اتصال تمامی کاربران آنلاین دارای این سرویس قطع خواهد شد</br>مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=DisconnectUser&Service_Id="+RowId,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						dhtmlx.message("کاربران با موفقیت به صف قطع اتصال افزوده شدند");
					}
					else alert(response);
				});
		
		}
	});	


}

function Toolbar1_OnSaveClick(){
Form1.updateValues();
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
		else{//insert
			DSFormInsertRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=insert",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
			
		}//insert
	}
	// else dhtmlx.alert('Invalid Data');
}

function Form1OnButtonClick(){
}

function TopToolbar_OnExitClick(){
	TopToolbar.attachEvent("onclick",function(id){
		if(id=="Exit"){parent.dhxLayout.dhxWins.window("popupWindow").close();}	
	});
}	
function Form1DoAfterLoadOk(){
	parent.dhxLayout.dhxWins.window("popupWindow").setText("ویرایش سرویس ["+Form1.getItemValue(Form1TitleField)+"]");
	
	
	var ServiceType=Form1.getItemValue("ServiceType");
	if(ServiceType=='Base'){
		var ShowItems=['Category2','MaxYearlyCount','MaxMonthlyCount','MaxActiveCount','ActiveYear','ActiveMonth','ActiveDay','Speed','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','AttachedGift_Id','ISFairService'];
		FormShowItem(Form1,ShowItems);
		var HideItems=['FramedRoute','FramedIP','ExtraType','IPCount','ExtraTraffic','ExtraTime','ExtraActiveDay'];
		FormHideItem(Form1,HideItems);
		if(Form1.getItemValue("UsedCount")>0){
			var DisableItems=['ActiveYear','ActiveMonth','ActiveDay','ISFairService','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA'];
			FormDisableItem(Form1,DisableItems);
			TopToolbar.showItem("DisconnectUser");
		}
		else
			TopToolbar.hideItem("DisconnectUser");
		// TabbarMain.removeTab(6,true);
		if(Form1.getItemValue("ISFairService")=="No")
			Form1.hideItem("FairMikrotikRate_Id");
		else
			Form1.showItem("FairMikrotikRate_Id");
		Form1.setItemLabel("Price", "قیمت :");
	}
	else if(ServiceType=='IP'){
		var ShowItems=['FramedRoute','FramedIP','MaxYearlyCount','MaxMonthlyCount','MaxActiveCount','IPCount'];
		FormShowItem(Form1,ShowItems);
		var HideItems=['Category2','ActiveYear','ActiveMonth','ActiveDay','MaxYearlyCount','MaxMonthlyCount','MaxActiveCount','Speed','ExtraType','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','ExtraTraffic','ExtraTime','ExtraActiveDay','AttachedGift_Id','ISFairService' ,'FairMikrotikRate_Id'];
		FormHideItem(Form1,HideItems);
		if(Form1.getItemValue("UsedCount")>0){
			var DisableItems=['FramedRoute','FramedIP'];
			FormDisableItem(Form1,DisableItems);
			TopToolbar.showItem("DisconnectUser");
		}
		else
			TopToolbar.hideItem("DisconnectUser");
		TabbarMain.removeTab(2,true);
		Form1.setItemLabel("Price", "قیمت روزانه سرویس :");
	}
	else if(ServiceType=='ExtraCredit'){
		var ShowItems=['MaxYearlyCount','MaxMonthlyCount','ExtraType','ExtraTraffic','ExtraTime','ExtraActiveDay'];
		FormShowItem(Form1,ShowItems);
		var HideItems=['Category2','FramedRoute','FramedIP','IPCount','MaxActiveCount','ActiveYear','ActiveMonth','ActiveDay','Speed','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','AttachedGift_Id','ISFairService' ,'FairMikrotikRate_Id' ];
		FormHideItem(Form1,HideItems);
		if(Form1.getItemValue("UsedCount")>0){
			var DisableItems=['ExtraType','ExtraTraffic','ExtraTime','ExtraActiveDay'];
			FormDisableItem(Form1,DisableItems);
		}
		TabbarMain.removeTab(2,true);
		Form1.setItemLabel("Price", "قیمت :");
		TopToolbar.hideItem("DisconnectUser");
	}
	else if(ServiceType=='Other'){
		var ShowItems=['MaxYearlyCount','MaxMonthlyCount','ExtraType','ExtraTraffic','ExtraTime','ExtraActiveDay'];
		FormShowItem(Form1,ShowItems);
		var HideItems=['Category2','FramedRoute','FramedIP','MaxActiveCount','ActiveYear','ActiveMonth','ActiveDay','Speed','ExtraType','STrA','STiA','YTrA','MTrA','WTrA','DTrA','YTiA','MTiA','WTiA','DTiA','IPCount','ExtraTraffic','ExtraTime','ExtraActiveDay','AttachedGift_Id','ISFairService' ,'FairMikrotikRate_Id' ];
		FormHideItem(Form1,HideItems);
		TabbarMain.removeTab(2,true);
		Form1.setItemLabel("Price", "قیمت :");
		TopToolbar.hideItem("DisconnectUser");
	}
	else
		TopToolbar.hideItem("DisconnectUser");

	if((Form1.getItemValue("OnAddByResellerSMS")=="")&&(Form1.getItemValue("OnBuyFromWebsiteSMS")==""))
		Form1.disableItem("SMSExpireTime");
	else
		Form1.enableItem("SMSExpireTime");
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
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	if(ISPermit("CRM.Service.View"))	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!ISPermit("CRM.Service.Edit")){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	if(ISPermit("CRM.Service.DisconnectUser")) 	DSToolbarAddButton(TopToolbar,null,"DisconnectUser","DisconnectUser","DisconnectUser",TopToolbar_OnDisconnectUserClick);
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
