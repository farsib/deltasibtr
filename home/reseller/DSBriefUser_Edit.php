<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSBriefUser_Edit.php ....................................................................................");
	PrintInputGetPost();	
	if($LastError!=""){
		DSDebug(0,"Session Expire");
		?>
		<html><head><script type="text/javascript">
			window.onload = function(){
				parent.dhtmlx.alert("<?php echo escape($LastError) ?>");//"Session Expire, Please Relogin"
				parent.tabbar.removeTab(parent.tabbar.getActiveTab(),true);
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
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
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
		.dhxform_obj_dhx_skyblue fieldset.dhxform_fs{width:96%;margin:15px;}
		#UserLog{
			font-size:11px;font-weight:normal;border:1px solid #a4bed4;text-align:center;
		}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var DataTitle="دسترسی سریع کاربر";
	var DataName="DSBriefUser_";
	var RenderFile=DataName+"EditRender";
	var ChangeLogDataName='User';
	var AutoCompleteTime_Id;
	var Form2OnOptionLoadded_Event_Id="";
	
	//=======Popup0
	var Popup0;

	//=======Form0
	var Form0;
	var Form0PopupHelp;
	var Form0FieldHelp  ={	Username:'English Character, maxlength:32',
								AdslPhone:'و حداکثر ۱۰ کاراکتر ADSL شماره تلفن کاربر',
								NOE :'برای گزارش مورد نیاز است',
								BirthDate:'خالی بگذارید و یا با فرمت روز/ماه/سال مشخص کنید',
								Phone:'شماره تلفن ۱۱ رقمی- مثال:05632233041',
								ExpirationDate:'تاریخ انقضا کاربری.بعد از این تاریخ کاربر نمی تواند متصل شود<br/>در صورتی که بخواهید کاربری منقضی نشود،خالی بگذارید',
								Mobile:'شماره همراه ۱۰ رقمی که با ۰ شروع می شود',
								NationalCode:'کد ملی کاربر می بایست به صورت صحیح وارد شود و یا 0000000000 را وارد کنید'
							};
	var Form0FieldHelpId=["Username","Pass","AdslPhone","NOE","BirthDate","Mobile","Phone","NationalCode","Email","Supporter_Id","Center_Id","Visp_Id","ExpirationDate"];
	var Form0Str;
	
	
	//=======Form1
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	Username:''};
	var Form1FieldHelpId=["AdslPhone","NOE","BirthDate","Mobile","Phone","NationalCode","Email","Supporter_Id","Center_Id","Visp_Id","MaxPrepaidDebit","ExpirationDate"];
	
	var Form1TitleField="Username";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];

	var Form1Str = [
		{ type: "block",offsetTop:10,list:[
			{ type: "input" , name:"LookupUsername", label:"جستجو نام کاربری:",labelWidth:100,inputWidth:140, value: "" , validate:"IsValidUserName", maxLength:32},			
			{ type: "newcolumn",offset:10},
			{ type: "button", name:"Retrieve",value:"جستجو",width:80},
			{ type: "newcolumn"},
			{ type: "button", name:"AddUser",value:"ایجاد کاربر",width:80,hidden:true}
		]},				
		{type: "fieldset",name:"Informations",hidden:true,offsetTop:15,label:"---",list:[
			{ type:"settings",labelWidth:125,offsetLeft:10,inputWidth:200, labelAlign:"left"},
			{ type: "hidden" , name:"Error"},
			{ type: "hidden" , name:"Session"},
			{ type: "hidden" , name:"StaleSession"},
			{ type: "input" , name:"User_Id", label:"شناسه کاربر :",readonly:true, style:"background-color:#D6DDE0;color:dimgray"},
			{ type: "input" , name:"Username", label:"نام کاربری :" ,readonly:true},
			{ type: "password" , name:"Pass", label:"کلمه عبور :",readonly:true, maxLength:64, value:"NA/NA/NA/NA/NA/NA/NA"/* ,hidden:true */},
			{ type: "input" , name:"UserCDT", label:"زمان ایجاد :",readonly:true, style:"background-color:#D6DDE0;color:dimgray"},
			{ type: "input" , name:"Reseller_Id", label:"نماینده فروش :",readonly:true},
			{ type: "input" , name:"Status_Id", label:"نام وضعیت :",readonly:true},
			{ type: "input" , name:"InitialMonthOff", label:"تخفیف اولیه :",readonly:true/* ,hidden:true */},
			{ type: "input" , name:"MaxPrepaidDebit", label:"(حداکثر بدهی مجاز(ریال :",readonly:true, validate:"NotEmpty"/* ,hidden:true */},
			{ type: "input" , name:"Visp_Id", label:"ارائه دهنده مجازی  :",readonly:true},
			{ type: "input" , name:"Center_Id", label:"نام مرکز :",readonly:true/* ,hidden:true */},
			{ type: "input" , name:"Supporter_Id", label:"نام پشتیبان :",readonly:true/* ,hidden:true */},
			{ type: "input" , name:"AdslPhone", label:"Adsl شماره :", validate:"IsValidAdslPhone", maxLength:10/* ,hidden:true */},
			{ type: "input" , name:"NOE", label:"موقعیت مکانی وایرلس :", validate:"", maxLength:32/* ,hidden:true */},
			{ type: "input" , name:"IdentInfo", label:"شناسه هویتی :", validate:"", maxLength:100/* ,hidden:true */},
			{ type: "input" , name:"IPRouteLog", label:"IPRoute لاگ :", validate:"", maxLength:100/* ,hidden:true */},
			{ type: "input" , name:"Email", label:"ایمیل :", validate:"IsValidEMail:", maxLength:128/* ,hidden:true */},
			{ type: "input" , name:"Comment", label:"توضیح :", validate:"", maxLength:255,rows:3},
			{ type: "input" , name:"Organization", label:"نام شرکت :", validate:"", maxLength:64},
			{ type: "input" , name:"CompanyRegistryCode", label:"شماره ثبت شرکت :", validate:"", maxLength:12/* ,hidden:true */},
			{ type: "input" , name:"CompanyEconomyCode", label:"شماره اقتصادی شرکت :", validate:"", maxLength:12/* ,hidden:true */},
			{ type: "input" , name:"CompanyNationalCode", label:"شماره ملی شرکت :", validate:"", maxLength:12/* ,hidden:true */},
			{ type: "input" , name:"Name", label:"نام :", validate:"", maxLength:32},
			{ type: "input" , name:"Family", label:"نام خانوادگی :", validate:"", maxLength:32},
			{ type: "input" , name:"FatherName", label:"نام پدر :", validate:"", maxLength:32/* ,hidden:true */},
			{ type: "input" , name:"Nationality", label:"ملیت :", validate:"", maxLength:32/* ,hidden:true */},
			{ type: "input" , name:"Mobile", label:"موبایل :", validate:"IsValidMobileNo", maxLength:11/* ,hidden:true */},
			{ type: "input" , name:"NationalCode", label:"کد ملی :", validate:"IsValidNationalCode", maxLength:10/* ,hidden:true */},
			{ type: "input" , name:"BirthDate", label:"تاریخ تولد :", validate:"IsValidDateOrBlank", maxLength:10/* ,hidden:true */},
			{ type: "input" , name:"Phone", label:"تلفن :", validate:"", maxLength:32/* ,hidden:true */},
			{ type: "input" , name:"Address", label:"آدرس :", validate:"", maxLength:255,rows:3/* ,hidden:true */},
			{ type: "input" , name:"ExpirationDate", label:"تاریخ انقضا :", validate:"IsValidDateOrBlank", maxLength:10/* ,hidden:true */},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"},
			{ type: "label"},
			{type: "block", style:"position:fixed;bottom:25px;padding:4px;background: radial-gradient(rgba(250,250,255,1), rgba(255,255,255,0.05));", width: 320, list:[
				{ type: "button", name:"FullView",value:"نمایش کامل",width:70},
				{ type: "newcolumn"},
				{ type: "button", name:"WWW",value:"پنل کاربر",width:50},
				{ type: "newcolumn"},
				{ type: "button", name:"Save",value:"ذخیره",width:130,offsetLeft:20}
			]}
		]}
	];

	
	//=======Popup2
	var Popup2;
	
	//=======Form2ای
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  ={ Username:''};
	var Form2FieldHelpId=[];
	
	var Form2DisableItems=[];
	var Form2EnableItems=[];
	var Form2HideItems=[];
	var Form2ShowItems=[];

	function CreateForm2Str(ItemTitle,ItemType,User_Id,ItemName,ValidateStr){
		var Form2Str = [
			{type: "fieldset", width: 380,label:ItemTitle,list:[
				{ type:"settings",labelWidth:120,offsetLeft:10,inputWidth:180, labelAlign:"left"},
				{ type: "hidden" , name:"User_Id", value:User_Id},
				{ type: "hidden" , name:"ItemName", value:ItemName},
				(
				(ItemType=="Select")
				?
				{ type: "select", name:ItemName,label: ItemTitle+"",connector: RenderFile+".php?"+un()+"&act=Select"+ItemName+"&User_Id="+User_Id,required:true,inputWidth:178,validate:ValidateStr}
				:
				({ type: "input" , name:ItemName, label:ItemTitle+"",required:true,validate:ValidateStr})),
			]},
			{type: "block", width: 350, list:[
				{ type: "button", name:"OK",value:"انجام",width:100,offsetLeft:80},
				{type: "newcolumn"},
				{ type: "button", name:"Close",value:"بستن",width:100,offsetLeft:20}
			]}
		];
		return Form2Str;
	}
	
	//=======Form3
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  ={ Username:''};
	var Form3FieldHelpId=[];
	
	var Form3DisableItems=[];
	var Form3EnableItems=[];
	var Form3HideItems=[];
	var Form3ShowItems=[];

	var Form3Str = [
		{ type: "fieldset", label:"افزودن",className:"MinWidth",list:[
			{ type: "button", name:"ServiceBase",value:"سرویس پایه",width:70},
			{type: "newcolumn"},
			{ type: "button", name:"ServiceExtraCredit",value:"اعتبار اضافی",width:70},
			{type: "newcolumn"},
			{ type: "button", name:"ServiceIP0",value:"آی پی",width:70},
			{type: "newcolumn"},
			{ type: "button", name:"ServiceOther",value:"سایر",width:70},
			{type: "newcolumn"},
			{ type: "button", name:"Payment",value:"پرداخت",width:70,offsetLeft:30},
		]},
		{ type: "fieldset", label:"توضیحات",className:"MinWidth",list:[
			{ type: "button", name:"GetCreditInfo",value:"اطلاعات اعتبار",width:100},
			{ type: "newcolumn"},
			{ type: "button", name:"GetUserLog",value:"گزارش کاربر",width:100},
			{ type: "newcolumn"},
			{ type: "button", name:"WebUnblock",value:"رفع مسدودی پنل",width:90,offsetLeft:60},
			{ type: "newcolumn"},
			{ type: "button", name:"RadiusUnblock",value:"رفع مسدودی ردیوس",width:105},
		]},
		{ type: "label",name:"InfoBlock",label:""}
	];

	//=======Popup4
	var Popup4;
	
	//=======Form4
	var Form4;
	var Form4PopupHelp;
	var Form4FieldHelp  ={ Username:''};
	var Form4FieldHelpId=["StartDate","EndDate","Number"];
	
	var Form4DisableItems=[];
	var Form4EnableItems=[];
	var Form4HideItems=[];
	var Form4ShowItems=[];

	function CreateForm4Str_Service(ServiceType,User_Id,StartDate,EndDate,Number){
		
		var PayPlanOptions;
		if((ISPermit('Visp.User.PayPlan.PrePaid'))&&(ISPermit('Visp.User.PayPlan.PostPaid')))
			PayPlanOptions=
				{ type: "select", name:"PayPlan", label: "نوع پرداخت :", value: "PrePaid",inputWidth:90,validate:"",required:true, disabled: true,options:[
					{text: "پیش پرداخت", value: "PrePaid"},
					{text: "پس پرداخت", value: "PostPaid"}
				]};
		else if(ISPermit('Visp.User.PayPlan.PrePaid'))
			PayPlanOptions={ type:"hidden", name:"PayPlan",value:"پیش پرداخت"};
		else if(ISPermit('Visp.User.PayPlan.PostPaid'))
			PayPlanOptions={ type:"hidden", name:"PayPlan",value:"پس پرداخت"};
		else
			return "";
		
		var Form4Str = [
			{type: "block", hidden:(ServiceType=="ServiceIP")?false:true,width: 500, list:[
				{ type: "input" , name:"StartDate", label:"<span style='color:#273737'>تاریخ شروع :</span>",value:StartDate, validate:"IsValidDate",labelAlign:"left",inputWidth:80,labelWidth:60,disabled:true, style:"color:#273737"},
				{type: "newcolumn", offset:10},
				{ type: "input" , name:"EndDate", label:"<span style='color:#273737'>تاریخ پایان :</span>",value:EndDate, validate:"IsValidDate",labelAlign:"left",inputWidth:80,labelWidth:60,disabled:true, style:"color:#273737"},
				{type: "newcolumn", offset:1},
				{ type: "input" , name:"Number", label:"<span style='color:#273737'>تعداد :</span>",value:Number, validate:"NotEmpty,ValidInteger",labelAlign:"left",inputWidth:40,labelWidth:60,disabled:true, style:"color:#273737"},
			]},
			{ type:"settings" , labelWidth:90, inputWidth:80,offsetLeft:10  },
			{ type: "select", name:"Service_Id",label: "نام :",connector: RenderFile+".php?"+un()+"&act=Select"+ServiceType+"&User_Id="+User_Id,required:true,validate:"IsID",inputWidth:418,info:true,inputHeight:90},
			{ type: "input" , name:"Description", label:"<span style='color:#273737'>توضیحات :</span>", validate:"",disabled: true, style:"color:#273737",rows: 2,labelAlign:"left",inputWidth:420,inputHeight:38},
			{type: "input" , name:"RemainedSavingOff",hidden:true, label:"<span style='color:blue'>مجموع پس انداز(ریال) :</span>", validate:"", disabled: true,style:"color:#050505",labelAlign:"left",inputWidth:100, labelWidth:170, numberFormat: "<?php  echo $PriceFormat;  ?>"},
			{type:"hidden",name:"PriceValue"},
			{type:"hidden",name:"OffRateValue"},
			{type:"hidden",name:"OffPercnt"},
			{type:"hidden",name:"SavingOffPercent"},
			{type:"hidden",name:"DirectOffPercent"},
			{type:"hidden",name:"SavingOffExpirationDays"},
			{type:"hidden",name:"VATPercent"},
			{type: "block", name:"InvoiceBlock1",hidden:true,width: 530,list:[
				{ type: "input" , name:"ServicePrice", label:"<span style='color:#273737'>قیمت سرویس :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
				{ type: "input" , name:"InstallmentNo", label:"<span style='color:#273737'>تعداد اقسط :</span>", validate:"" ,disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120},
				{type: "input" , name:"WithdrawSavingOff", label:"Withdraw SavingOff :", validate:"IsValidPrice", disabled: true,labelAlign:"left", maxLength:14,value:0,inputWidth:75, labelWidth:135,numberFormat: "<?php  echo $PriceFormat;?>"},
				{ type: "input" , name:"Price", label:"<span style='color:#273737'>قیمت هر قسط :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
				{type: "newcolumn", offset:30},
				// { type: "input" , name:"SavingOff",label:"<span style='color:#273737'>Saving Off :</span>",validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120},
				{ type: "input" , name:"DirectOff",label:"<span style='color:#273737'>تخفیف مستقیم :</span>",validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120},
				{ type: "input" , name:"PriceWithOff", label:"<span style='color:#273737'>قیمت(-تخفیف مستقیم) :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
				{ type: "input" , name:"VAT", label:"<span style='color:#273737'>مالیات :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:2,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
				{ type: "input" , name:"PriceWithVAT", label:"<span style='color:maroon'>قیمت با مالیات :</span>", validate:"", disabled: true, style:"color:maroon;background-color:#FFCCCC;font-weight:bold",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
			]},
			{type: "block", name:"InvoiceBlock2",hidden:true, width: 500, style:"border:1px dotted #C0C0E0;margin:4px 0 4px 0;padding:2px 0 4px 0", list:[
				{ type: "input" , name:"UserCredit", label:"<span style='color:#666666'>اعتبار کاربر :</span>", validate:"", disabled: true,style:"color:#666666",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>",labelWidth:79},
				{type: "newcolumn", offset:40},
				{type: "input" , name:"RemainCredit", label:"<span style='color:#666666'>باقیمانده اعتبار :</span>", validate:"", disabled: true,style:"color:#666666",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"}
			]}	
		];
		
		Form4Str.push(PayPlanOptions);
		Form4Str.push(
			{type:"label",name:"Detail",label:""},
			{type: "block", name:"ControlBlock", width: 510, list:[
				{ type: "button",name:"Proceed",value: "انجام",disabled:true,width :110,offsetLeft:20},
				{type: "newcolumn", offset:50},
				{ type: "button",name:"Refresh",value: "نوسازی",width :100},
				{type: "newcolumn", offset:50},
				{ type: "button",name:"Close",value: " بستن ",width :100},
			]}
		);
		
		
		return Form4Str;
	}

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:520,
				center:true,
				modal:true,
				park :false
				};
				
	// Layout   ===================================================================
	dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
	dhxLayout.setSkin(dhxLayout_main_skin);
	dhxLayout.cells("a").setText("اطلاعات");
	dhxLayout.cells("b").setText("سرویس و پشتیبانی");
	dhxLayout.cells("a").hideArrow();
	// dhxLayout.cells("a").hideHeader();
	dhxLayout.cells("b").hideArrow();
	// dhxLayout.cells("b").hideHeader();
	// dhxLayout.cells("c").setText("Support");
	dhxLayout.cells("a").setWidth(460);
	dhxLayout.attachEvent("onPanelResizeFinish", function(){dhxLayout.cells("a").setWidth(460);});
	dhxLayout.dhxWins.setEffect("move", true);
	
	var ActiveItem="LookupUsername";
	
	
	// Form1   ===================================================================

	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onEnter",Form1OnEnter);
	Form1.attachEvent("onFocus",Form1OnFocus);
	Form1.attachEvent("onBlur",function(){if(Popup2.isVisible())Form2.setFocusOnFirstActive();});
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	
	
	Popup0 = new dhtmlXPopup({form: Form1,id:["AddUser"],mode:"bottom"});
	Popup0.attachEvent("onHide",function(){
		Form1.unlock();
		Form1.setFocusOnFirstActive();
	});

	Form0=DSInitialForm(Popup0,[],Form0PopupHelp,Form0FieldHelpId,Form0FieldHelp,Form0OnButtonClick);
	
	Popup2 = new dhtmlXPopup({form: Form1,id:["LookupUsername","Pass","Reseller_Id","Username","Status_Id","InitialMonthOff","MaxPrepaidDebit","Visp_Id","Center_Id","Supporter_Id"],mode:"right"});
	
	Popup2.attachEvent("onHide",function(name){
		Form1.unlock();
	});
	Form2=DSInitialForm(Popup2,[],Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	
	
	
	Form3=DSInitialForm(dhxLayout.cells("b"),Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
	Form3.lock();
	Popup4=new dhtmlXPopup({form: Form3,id:["ServiceBase","ServiceExtraCredit","ServiceIP0","ServiceOther","Payment"],mode:"bottom"});
	Popup4.attachEvent("onHide",function(){
		Form1.unlock();
		Form3.unlock();
		Form3.setFocusOnFirstActive();
	});
	Form4=DSInitialForm(Popup4,[],Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,function(){});
	
	Form1.setFocusOnFirstActive();
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//=============================================================================================================================
function Form3OnButtonClick(name){
	Popup2.hide();
	if((name=="ServiceBase")||(name=="ServiceExtraCredit")||(name=="ServiceOther")||(name=="ServiceIP")){
		var StartDate='';
		var EndDate='';
		var Number=0;
		if(name=="ServiceIP"){
			StartDate=Form4.getItemValue("StartDate");
			EndDate=Form4.getItemValue("EndDate");
			Number=Form4.getItemValue("Number");
		}
		var User_Id=Form1.getItemValue("User_Id");
		var Form4Str=CreateForm4Str_Service(name,User_Id,StartDate,EndDate,Number);
		if(Form4Str==""){
			dhtmlx.alert({text:"You have not permit to add any plan<br/>neither PrePaid nor PostPaid!",type:"alert-error"});
			return;
		}
		Form4.unload();
		Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,FormAddServiceOnButtonClick);
		Form4.attachEvent("onChange",FormAddServiceOnChange);
		Form4.attachEvent("onInputChange",FormAddServiceOnInputChange);
		
		dhxLayout.progressOn();
		Form4.attachEvent("onOptionsLoaded",function(){
			dhxLayout.progressOff();
			var opts = Form4.getSelect("Service_Id");
			
			if(opts.length<1){
				Popup4.hide();
				dhtmlx.message({text:"There is not service available",type:"error"});
			}
			else{
				opts.size=5;
				FormAddServiceOnChange("Service_Id",Form4.getItemValue("Service_Id"));
			}
		});
		Form1.lock();
		Form3.lock();
		Popup4.show(name);
	}
	else if(name=="ServiceIP0"){
		Form4.unload();
		var User_Id=Form1.getItemValue("User_Id");
		var Form4Str= [
			{ type:"settings" , labelWidth:130, inputWidth:80,offsetLeft:10  },
			{ type: "input" , name:"StartDate", label:"تاریخ شروع :",value:"", validate:"NotEmpty,IsValidDate",labelAlign:"left",inputWidth:100,disabled:true},
			{ type: "input" , name:"EndDate", label:"تاریخ پایان :", value:"",validate:"NotEmpty,IsValidDate",labelAlign:"left",inputWidth:100},
			{ type: "input" , name:"Number", label:"تعداد :",value:"1", validate:"NotEmpty,ValidInteger",labelAlign:"left",inputWidth:40},
			{ type: "button",name:"Next",value: "بعدی",width :80}
		];
		Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,function(name){
			if(DSFormValidate(Form4,Form4FieldHelpId)){
				var StartDate=Form4.getItemValue("StartDate");
				var EndDate=Form4.getItemValue("EndDate");
				var Number=Form4.getItemValue("Number");
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=CheckIPRequest&User_Id="+User_Id+"&StartDate="+StartDate+"&EndDate="+EndDate+"&Number="+Number,
					function(loader){
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						if((response=='')||(response[0]=='~'))/* dhtmlx. */alert("خطا، "+response.substring(1));
						else
							Form3OnButtonClick("ServiceIP");
					});
			}
		});
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=LoadIPRequest&User_Id="+User_Id,
		function(loader){
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
			else{
				var parray=response.split("`",4);
				var StartDate=parray[1];
				var EndDate=parray[2];
				var Number=parray[3];
				Form4.setItemValue("StartDate",StartDate);
				Form4.setItemValue("EndDate",EndDate);
				Form4.setItemValue("Number",Number);
			}
		});
		Form1.lock();
		Form3.lock();
		Popup4.show(name);
		Form4.setItemFocus("Next");
	}
	else if(name=="Payment"){
		var DirectionOptions=[];
		if(ISPermit("Visp.User.Payment.Add.GetMoney")) DirectionOptions.push({text: "دریافت وجه", value: "GetMoney"});
		if(ISPermit("Visp.User.Payment.Add.RefundMoney")) DirectionOptions.push({text: "برگشت وجه", value: "RefundMoney"});
		var PaymentTypeOptions=[];
		if(ISPermit("Visp.User.Payment.PaymentType.Cash"))
			PaymentTypeOptions.push({text: "وجه نقد", value: "Cash"});
		if(ISPermit("Visp.User.Payment.PaymentType.Cheque"))
			PaymentTypeOptions.push({text: "چک", value: "Cheque"});
		if(ISPermit("Visp.User.Payment.PaymentType.Pos"))
			PaymentTypeOptions.push({text: "کارت خوان", value: "Pos"});
		if(ISPermit("Visp.User.Payment.PaymentType.Deposit"))
			PaymentTypeOptions.push({text: "بیعانه", value: "Deposit"});
		if(ISPermit("Visp.User.Payment.PaymentType.TAX"))
			PaymentTypeOptions.push({text: "مالیات", value: "TAX"});
		if(ISPermit("Visp.User.Payment.PaymentType.Off"))
			PaymentTypeOptions.push({text: "تخفیف", value: "Off"});
		if(ISPermit("Visp.User.Payment.PaymentType.Other"))
			PaymentTypeOptions.push({text: "سایر", value: "Other"});
		
		
		var Form4Str = [
			{ type:"settings" , labelWidth:110, inputWidth:90,offsetLeft:10  },

			{ type: "input", name:"PayBalance", label: "تراز مالی :", inputWidth:128,readonly:true},
			{type:"label"},
			{ type: "select", name:"Direction", label: "جهت :", options: DirectionOptions, inputWidth:128,required:true},
			{ type: "select", name:"PaymentType", label: "روش پرداخت :", options:PaymentTypeOptions,inputWidth:128,required:true},
			{ type: "input" , name:"Price", label:"مبلغ(ریال) :", validate:"IsValidPrice",value:"0", labelAlign:"left", maxLength:14,inputWidth:130,numberFormat: "<?php  echo $PriceFormat;  ?>"},
			{ type:"input" , name:"VoucherNo", label:"سریال/پیگیری :",maxLength:15, validate:"", labelAlign:"left",inputWidth:220},
			{ type:"input" , name:"VoucherDate", label:"تاریخ  :",maxLength:10, validate:"IsValidDateOrBlank", labelAlign:"left",inputWidth:220},
			{ type:"input" , name:"BankBranchName", label:"نام بانک :",maxLength:32, validate:"", labelAlign:"left", inputWidth:220},
			{ type:"input" , name:"BankBranchNo", label:"کد شعبه  :",maxLength:32, validate:"", labelAlign:"left", inputWidth:220},
			{ type: "input" , name:"Comment", label:"توضیح :", labelAlign:"left", maxLength:255,inputWidth:220,rows:2},
			{ type:"label"},
			{type: "block", width: 370, list:[
				{ type: "button",name:"Proceed",value: "انجام",width :65},
				{type: "newcolumn", offset:10},
				{ type: "button",name:"Close",value: " بستن ",width :50},
			]}	
		];
		Form4.unload();
		Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,FormAddPaymentOnButtonClick);
		Form1.lock();
		Form3.lock();
		
		dhxLayout.progressOn();
		var User_Id=Form1.getItemValue("User_Id");
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetUserPayBalance&User_Id="+User_Id,function (loader){
			dhxLayout.progressOff();
			
			response=loader.xmlDoc.responseText;
			response=CleanError(response);

			if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
			else{
				ResArray=response.split("~");
				if(ResArray[0]=='OK'){
					var PayBalance=parseFloat(ResArray[1]);
					Form4.setItemValue("PayBalance",PayBalance);
					if(PayBalance<0){
						if(ISPermit("Visp.User.Payment.Add.GetMoney")){
							Form4.setItemValue("Direction","GetMoney");
							Form4.setItemValue("Price",-PayBalance);
						}
					}
					else{
						if(ISPermit("Visp.User.Payment.Add.RefundMoney")){
							Form4.setItemValue("Direction","RefundMoney");
							Form4.setItemValue("Price",PayBalance);
						}
					}
					Popup4.show(name);
					Form4.setFocusOnFirstActive();
				}
				else alert(response);
			}
		});
	}
	else if (name=="GetCreditInfo"){
		Form1.lock();
		Form3.lock();
		
		dhxLayout.progressOn();
		var User_Id=Form1.getItemValue("User_Id");
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetCreditInfo&User_Id="+User_Id,function (loader){
			dhxLayout.progressOff();
			Form1.unlock();
			Form3.unlock();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);

			if((response=='')||(response[0]=='~')){
				dhtmlx.alert("خطا، "+response.substring(1));
				Form3.setItemLabel("InfoBlock",response.substring(1));
			}
			else
				Form3.setItemLabel("InfoBlock",response);
		});
	}
	else if (name=="GetUserLog"){
		Form1.lock();
		Form3.lock();
		
		dhxLayout.progressOn();
		var User_Id=Form1.getItemValue("User_Id");
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetUserLog&User_Id="+User_Id,function (loader){
			dhxLayout.progressOff();
			Form1.unlock();
			Form3.unlock();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);

			if((response=='')||(response[0]=='~')){
				dhtmlx.alert("خطا، "+response.substring(1));
				Form3.setItemLabel("InfoBlock",response.substring(1));
			}
			else
				Form3.setItemLabel("InfoBlock",response);
		});
	}
	else if ((name=="RadiusUnblock")||(name=="WebUnblock")){
		dhtmlx.confirm({
			text:"آیا مطمئن هستید؟",
			ok:"بلی",
			cancel:"خیر",
			title:"هشدار",
			callback:function(ans){
				if(ans){
					Form1.lock();
					Form3.lock();
					dhxLayout.progressOn();
					var User_Id=Form1.getItemValue("User_Id");
					dhtmlxAjax.get(RenderFile+".php?"+un()+"&act="+name+"&User_Id="+User_Id,function (loader){
						dhxLayout.progressOff();
						Form1.unlock();
						Form3.unlock();
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						var mapObj = {
						WebUnblock:"رفع مسدودی پنل",
						RadiusUnblock:"رفع مسدودی ردیوس",
						};
						if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
						else if(response=='OK~') {
							dhtmlx.message("با موفقیت "+name.replace(/WebUnblock|RadiusUnblock/gi, function(matched){
  return mapObj[matched]
})+" انجام شد")
						}
						else alert(response);
					});
				}
			}
		});
	}
	else
		dhtmlx.message({text:"UnHandled button!",type:"error"});
}
function FormAddPaymentOnButtonClick(name){
	if(name=='Close') Popup4.hide();
	else if(name=='Proceed'){
		if(DSFormValidate(Form4,['VoucherDate','Price'])){
			Form4.updateValues();
			Form4.disableItem("Proceed");
			Popup4.hide();
			var User_Id=Form1.getItemValue("User_Id");
			MyDSFormUpdateRequestProgress(dhxLayout,Form4,RenderFile+".php?"+un()+"&act=AddPayment&User_Id="+User_Id,Form4DoAfterUpdateOk,Form4DoAfterUpdateFail);
		}
	}
	
}
function SetParamInfo2(loader){
	dhxLayout.progressOff();
	Form4.unlock();
	response=loader.xmlDoc.responseText;
	response=CleanError(response);
	if((response=='')||(response[0]=='~')){
		dhtmlx.alert("خطا، "+response.substring(1));
		Form4.lock();
	}
	else{
		var parray=response.split("`",14);
		Form4.setItemValue("RemainedSavingOff",parray[0]);
		if(parray[0]<=0){
			Form4.hideItem("WithdrawSavingOff");
			Form4.hideItem("RemainedSavingOff");
		}
		else{
			Form4.showItem("WithdrawSavingOff");
			Form4.showItem("RemainedSavingOff");
		}
		
		Form4.setItemValue("ServicePrice",parray[1]);
		Form4.setItemValue("InstallmentNo",parray[2]);
		Form4.setItemValue("PriceValue",parray[3]);//Price
		Form4.setItemValue("OffRateValue",parray[4]);//OffRate
		Form4.setItemValue("OffPercnt",parray[5]);//Off
		Form4.setItemValue("SavingOffPercent",parray[6]);//SavingOff
		Form4.setItemValue("DirectOffPercent",parray[7]);//DirectOff
		Form4.setItemValue("SavingOffExpirationDays",parray[8]);//SavingOffExpirationDays
		Form4.setItemValue("VATPercent",parray[9]);//VAT
		Form4.setItemValue("UserCredit",parray[10]);//UserCredit
		var Err=parray[11];
		if(parray[12]!=""){
			Form4.showItem("Description");
			Form4.setItemValue("Description",parray[12]);
			Form4.getInput("Description").style.direction=GetTextDirection(parray[12]);
		}
		else
			Form4.hideItem("Description");
			
		
		SetInvoice(Form4.getItemValue("WithdrawSavingOff"));
		if(Err!=""){
			Form4.disableItem("ControlBlock");
			alert(Err);
		}
		else
			Form4.enableItem("ControlBlock");
	}
}
function SetInvoice(WithdrawSavingOff){
	WithdrawSavingOff = WithdrawSavingOff ==''? 0 : parseInt(WithdrawSavingOff.replace(/,/g,''));
	
	var Price=Form4.getItemValue("PriceValue");
	var RemainedSavingOff=Form4.getItemValue("RemainedSavingOff");
	var MinWithdrawable=Math.min(Price,RemainedSavingOff);
	
	if(WithdrawSavingOff>MinWithdrawable){
		if(MinWithdrawable==RemainedSavingOff)
			dhtmlx.message({text:"Total remained SavingOff = "+formatMoney(RemainedSavingOff,0,".",",")+"<br/>Cannot withdraw "+formatMoney(WithdrawSavingOff,0,".",","),type:"error",expire:7000});
		else
			dhtmlx.message({text:"You can at most withdraw as much as price required for service("+formatMoney(Price,0,".",",")+")",type:"error",expire:7000});	
		
		Form4.setItemValue("WithdrawSavingOff",MinWithdrawable);
		WithdrawSavingOff=MinWithdrawable;
	}
	
	
	Price=Price-WithdrawSavingOff;
	Form4.setItemValue("Price",formatMoney(Price,0,".",","));
	
	var PayPlanType=Form4.getItemValue("PayPlan");		
	var OffRateValue=Form4.getItemValue("OffRateValue");
	var OffPercnt=Form4.getItemValue("OffPercnt");
	var SavingOffPercent=Form4.getItemValue("SavingOffPercent");
	var DirectOffPercent=Form4.getItemValue("DirectOffPercent");
	var PriceWithOff=Price;
	if(OffRateValue==0){
		// Form4.setItemValue("SavingOff","Service has no Off");
		Form4.setItemValue("DirectOff","Service has no Off");
		Form4.showItem("DirectOff");
		Form4.hideItem("PriceWithOff");
		Form4.setItemLabel("Detail","");
	}
	else{
		var DirectOffAmount=Math.round(Price*DirectOffPercent/100);
		var SavingOffAmount=Math.round(Price*SavingOffPercent/100);
		
		if(DirectOffPercent>0){
			PriceWithOff=Math.round(Price-DirectOffAmount);
			Form4.setItemValue("DirectOff",formatMoney(DirectOffAmount,0,".",",")+" ("+formatMoney(DirectOffPercent,0,".",",")+"%)");
			Form4.setItemValue("PriceWithOff",PriceWithOff);
			Form4.showItem("DirectOff");
			Form4.showItem("PriceWithOff");
		}
		else{
			Form4.hideItem("DirectOff");
			Form4.hideItem("PriceWithOff");
		}
		
		if(SavingOffPercent>0)
			Form4.setItemLabel("Detail","<span style='color:darkgreen;'>"+formatMoney(SavingOffAmount,0,".",",")+" Rls ("+formatMoney(SavingOffPercent,0,".",",")+"%) will be added to SavingOff after service add</span>");
		else
			Form4.setItemLabel("Detail","");
	}
	
	SavingOffExpirationDays=Form4.getItemValue("SavingOffExpirationDays");
	VATPercent=Form4.getItemValue("VATPercent");
	UserCredit=Form4.getItemValue("UserCredit");
	
	var VATAmount=0;
	if(VATPercent==0)
		Form4.setItemValue("VAT","0");
	else{
		VATAmount=Math.round(PriceWithOff*VATPercent/100);
		Form4.setItemValue("VAT",formatMoney(VATAmount,0,".",",")+" ("+formatMoney(VATPercent,0,".",",")+"%)");
	}
	var PriceWithVAT=Math.round(PriceWithOff+VATAmount);
	Form4.setItemValue("PriceWithVAT",PriceWithVAT);

	
	var RemainCredit=PriceWithVAT-Form4.getItemValue("UserCredit");
	if(RemainCredit<0) RemainCredit=0;
	Form4.setItemValue("RemainCredit",RemainCredit);
	if(PayPlanType=='PrePaid'){
		if(RemainCredit>parseFloat(Form1.getItemValue("MaxPrepaidDebit").replace(",",""))){
			Form4.disableItem("Proceed");
			// Form4.setFocusOnFirstActive();
		}
		else{
			Form4.enableItem("Proceed");
			// Form4.setItemFocus("Proceed");
		}
	}
	else{
		if(ISPermit('Visp.User.PayPlan.PostPaid')==true){
			Form4.enableItem("Proceed");
			// Form4.setItemFocus("Proceed");
		}
		else{
			Form4.disableItem("Proceed");
			// Form4.setFocusOnFirstActive();
		}
	}
}
function FormAddServiceOnInputChange(id, value){
	//parent.parent.dhtmlx.message("<span style='color:red'>Input</span>\nid="+id+"\nvalue="+value);
	if(id=="WithdrawSavingOff")
		SetInvoice(value);
}
function FormAddServiceOnChange(id, value){
	// dhtmlx.message("<span style='color:blue'>Input</span>\nid="+id+"\nvalue="+value);
	if(id=='Service_Id'){
		if(value==0){

			Form4.hideItem("RemainedSavingOff");
			Form4.hideItem("InvoiceBlock1");
			Form4.hideItem("InvoiceBlock2");
			Form4.setItemValue("Off",'');
			Form4.setItemValue("PriceWithOff",'');
			Form4.setItemValue("InstallmentNo",'');
			Form4.setItemValue("RemainedSavingOff",'');
			Form4.setItemValue("ServicePrice",'');
			Form4.setItemValue("Price",'');
			Form4.setItemValue("VAT",'');
			Form4.setItemValue("PriceWithVAT",'');
			Form4.setItemValue("UserCredit",'');
			Form4.setItemValue("RemainCredit",'');
			Form4.setItemValue("Description",'');
				Form4.hideItem("Description");
			Form4.disableItem("Proceed");
			Form4.disableItem("PayPlan");
			Form4.disableItem("WithdrawSavingOff");
			Form4.setItemValue("DirectOff",'');
			Form4.setItemLabel("Detail",'');
		}
		else{
			Form4.showItem("InvoiceBlock1");
			Form4.showItem("InvoiceBlock2");
			Form4.enableItem("PayPlan");
			Form4.enableItem("WithdrawSavingOff");
			dhxLayout.progressOn();
			Form4.lock();
			var User_Id=Form1.getItemValue("User_Id");
			var StartDate=Form4.getItemValue("StartDate");
			var EndDate=Form4.getItemValue("EndDate");
			var Number=Form4.getItemValue("Number");
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetServicePrice&User_Id="+User_Id+"&Service_Id="+value+"&StartDate="+StartDate+"&EndDate="+EndDate+"&Number="+Number,SetParamInfo2);
		}
	}
	else if(id=='PayPlan'){
		if(value=='PrePaid'){
			if((Form4.getItemValue("RemainCredit"))>parseFloat(Form1.getItemValue("MaxPrepaidDebit").replace(",","")))
				Form4.disableItem("Proceed");
			else	
				Form4.enableItem("Proceed");
		}
		else{	
			if(ISPermit('Visp.User.PayPlan.PostPaid')==true)
				Form4.enableItem("Proceed");
			else	
				Form4.disableItem("Proceed");
		}
	}
	else if(id=="WithdrawSavingOff")
		SetInvoice(value);
}

function FormAddServiceOnButtonClick(name){
	if(name=='Close') 
		Popup4.hide();
	else if(name=='Refresh'){
		Form4.updateValues();
		var value=Form4.getItemValue("Service_Id");
		if(value>0){
			var StartDate=Form4.getItemValue("StartDate");
			var EndDate=Form4.getItemValue("EndDate");
			var Number=Form4.getItemValue("Number");
			dhxLayout.progressOn();
			Form4.lock();
			var User_Id=Form1.getItemValue("User_Id");
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetServicePrice&User_Id="+User_Id+"&Service_Id="+value+"&StartDate="+StartDate+"&EndDate="+EndDate+"&Number="+Number,SetParamInfo2);
		}
	}	
	else if(name=='Proceed'){
		if(DSFormValidate(Form4,Form4FieldHelpId)){
			Form4.disableItem("Proceed");
			Popup4.hide();
			var User_Id=Form1.getItemValue("User_Id");
			MyDSFormUpdateRequestProgress(dhxLayout,Form4,RenderFile+".php?"+un()+"&act=AddService&User_Id="+User_Id,Form4DoAfterUpdateOk,Form4DoAfterUpdateFail,function(){});
		}
	}
	
}
function Form4DoAfterUpdateOk(){
	Popup4.hide();
}

function Form4DoAfterUpdateFail(){
	Popup4.hide();
}
function Form1OnFocus(name){
	ActiveItem=name;
	var MyItems=["Pass","Reseller_Id","Username","Status_Id","InitialMonthOff","MaxPrepaidDebit","Visp_Id","Center_Id","Supporter_Id"];
	
	if(MyItems.indexOf(name)>=0){
		var User_Id=Form1.getItemValue("User_Id");
		if(User_Id<=0)
			return;
		var IsPermitted;
		var ItemType;
		var FieldValue="";
		var ValidateStr;
		if(name=="Pass"){
			IsPermitted=ISPermit("Visp.User.GetPassword")||ISPermit("Visp.User.ChangePassword");
			ValidateStr="NotEmpty";
			ItemType="Input";
		}
		else if(name=="Reseller_Id"){
			IsPermitted=ISPermit("Visp.User.ChangeReseller");
			ValidateStr="IsID";
			ItemType="Select";
		}
		else if(name=="Username"){
			IsPermitted=ISPermit("Visp.User.ChangeUsername");
			FieldValue=Form1.getItemValue(name);
			ValidateStr="NotEmpty,IsValidUserName";
			ItemType="Input";
		}
		else if(name=="Status_Id"){
			IsPermitted=ISPermit("Visp.User.Status.ChangeStatus");
			ValidateStr="IsID";
			ItemType="Select";
		}
		else if(name=="InitialMonthOff"){
			IsPermitted=ISPermit("Visp.User.SetInitialMonthOff");
			FieldValue=Form1.getItemValue(name);
			ValidateStr="NotEmpty,IsValidPercent";
			ItemType="Input";
		}
		else if(name=="MaxPrepaidDebit"){
			IsPermitted=ISPermit("Visp.User.SetMaxPrepaidDebit");
			FieldValue=Form1.getItemValue(name);
			ValidateStr="NotEmpty,IsValidPrice";
			ItemType="Input";
		}
		else{
			IsPermitted=ISPermit("Visp.User.Info.EditField."+name);
			ValidateStr="IsID";
			ItemType="Select";
		}
		if(!IsPermitted)
			return;
		
		
		Popup2.show(name);
		Form1.lock();
		Form2.unload();
		Form2=DSInitialForm(Popup2,CreateForm2Str("تغییر "+Form1.getItemText(name),ItemType,User_Id,name,ValidateStr),Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
		Form2.attachEvent("onEnter",function(){Form2OnButtonClick("OK")});
		Form2.setFocusOnFirstActive();
		if(name=="Pass"){
			if(ISPermit("Visp.User.GetPassword")){
				dhxLayout.progressOn();
				Form2.lock();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetPass&User_Id="+User_Id,function(loader){
					Form2.unlock();
					dhxLayout.progressOff();
					var response=loader.xmlDoc.responseText;
					response=CleanError(response);
					
					var parray=response.split("`",3);
					if(parray[0]!='OK'){
						if(response[0]=='~') 
							dhtmlx.alert({text:response.substring(1),title:"هشدار",type:"alert-error"});
						else
							dhtmlx.alert({text:response,title:"هشدار",type:"alert-error"});
					}
					else{
						Form2.setItemValue(name,parray[1]);
						
					}
				});
				Form2OnOptionLoadded_Event_Id="";
			}
		}
		else if(ItemType=="Select"){
			dhxLayout.progressOn();
			Form2.lock();
			Form2OnOptionLoadded_Event_Id=Form2.attachEvent("onOptionsLoaded",function(){Form2.unlock();dhxLayout.progressOff();});
		}
		else{
			Form2OnOptionLoadded_Event_Id="";
			Form2.setItemValue(name,FieldValue);
		}
		
	}
}

function Form2OnButtonClick(name){
	if(name=="Close")
		Popup2.hide();
	else{
		if(Form2.validateItem(name))
			MyDSFormUpdateRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=change",Form2DoAfterUpdateOk,Form2DoAfterUpdateFail,function(){Form2.setFocusOnFirstActive();});
	}
}

function Form2DoAfterUpdateOk(){
	Popup2.hide();
	if(Form2.getItemValue("Username")!=null)
		Form1.setItemValue("LookupUsername",Form2.getItemValue("Username"));;
	Form1OnButtonClick("Retrieve");
}
function Form2DoAfterUpdateFail(){
}

function Form1OnEnter(){
	if(ActiveItem=="LookupUsername")
		Form1OnButtonClick("Retrieve");
}
	
function Form1OnButtonClick(name){
	if(name=="Retrieve"){
		Form1.hideItem("AddUser");
		clearTimeout(AutoCompleteTime_Id);
		Form2.detachEvent(Form2OnOptionLoadded_Event_Id);
		Popup2.hide();
		if(Form1.validateItem("LookupUsername")){
			var LookupUsername=Form1.getItemValue("LookupUsername");
			if(LookupUsername==""){
				dhtmlx.message({text:"لطفا نام کاربری را وارد کنید",type:"error"});
				Form1.setItemFocus("LookupUsername");
				return
			}
			dhxLayout.progressOn();
			Form1.lock();
			Form1.load(RenderFile+".php?"+un()+"&Username="+LookupUsername+"&act=load",function(){
				Form1.unlock();
				dhxLayout.progressOff();
				var ErrorValue=Form1.getItemValue("Error");
				if(ErrorValue=="~NotFound"){
					Form1.showItem("AddUser");
					dhtmlx.confirm({
						text:"خطا، کاربر یافت نشد",
						type:"alert-error",
						title:"هشدار",
						ok:"ایجاد کاربر",
						cancel:"بستن",
						callback:function(ans){
							if(ans)
								Form1OnButtonClick("AddUser");
							else
								Form1.setItemFocus("LookupUsername");
						}
					});
				}
				else if((ErrorValue!=null)&&(ErrorValue!='')){
					if(ErrorValue[0]=="~")
						ErrorValue=ErrorValue.substring(1);
					dhtmlx.alert({text:"خطا، "+ErrorValue,type:"alert-error",title:"هشدار",ok:"بستن",callback:function(){Form1.setItemFocus("LookupUsername")}});
					
				}
				else{
					Form1.setFocusOnFirstActive();
					Permission=LoadPermissionByUser(Form1.getItemValue("User_Id"));
					SetViewEditFieldState(Form1);
					
					var Session=Form1.getItemValue('Session');
					var StaleSession=Form1.getItemValue('StaleSession');
					Form1.setItemText("Informations",
						"کاربر در حال حاضر <strong>"+(Session>0?('<span style="color:limegreen">آنلاین</span>'):
							(Session==0?('<span style="color:red">آفلاین</span>'):('<span style="color:orange">آنلاین</span>'))
						)
						+(StaleSession>0?(' <span style="color:indianred;font-size:80%">(+'+StaleSession+' StaleSession)</span>'):'')+"</strong> است"
					);
					
					Form1.showItem("Informations");
					Form3.unlock();
					if(ISPermit('Visp.User.Service.Base.Add'))
						Form3.enableItem("ServiceBase");
					else
						Form3.disableItem("ServiceBase");
					if(ISPermit('Visp.User.Service.ExtraCredit.Add'))
						Form3.enableItem("ServiceExtraCredit");
					else
						Form3.disableItem("ServiceExtraCredit");
					if(ISPermit('Visp.User.Service.IP.Add'))
						Form3.enableItem("ServiceIP0");
					else
						Form3.disableItem("ServiceIP0");
					if(ISPermit('Visp.User.Service.Other.Add'))
						Form3.enableItem("ServiceOther");
					else
						Form3.disableItem("ServiceOther");
					if(ISPermit('Visp.User.Payment.Add.GetMoney')||ISPermit('Visp.User.Payment.Add.RefundMoney'))
						Form3.enableItem("Payment");
					else
						Form3.disableItem("Payment");
					if(ISPermit('Visp.User.CreditStatus.List')){
						Form3OnButtonClick("GetCreditInfo");
						Form3.enableItem("GetCreditInfo");
					}
					else
						Form3.disableItem("GetCreditInfo");
					
					if(ISPermit('Visp.User.RadiusLog.List'))
						Form3.enableItem("GetUserLog");
					else
						Form3.disableItem("GetUserLog");
					
					if(ISPermit('Visp.User.WebUnblock'))
						Form3.enableItem("WebUnblock");
					else
						Form3.disableItem("WebUnblock");
					
					if(ISPermit('Visp.User.RadiusUnblock'))
						Form3.enableItem("RadiusUnblock");
					else
						Form3.disableItem("RadiusUnblock");
					if(ISPermit("Visp.User.View")||ISPermit("Visp.User.Edit"))
						Form1.enableItem("FullView");
					else
						Form1.disableItem("FullView");
					if(ISPermit("Visp.User.UsersWebsite"))
						Form1.enableItem("WWW");
					else
						Form1.disableItem("WWW");
					
				}	
			});
		}
	}
	else if(name=="Save"){
		if(DSFormValidate(Form1,Form1FieldHelpId)){
			Form1.updateValues();
			MyDSFormUpdateRequestProgress(dhxLayout,Form1,RenderFile+".php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail,function(){Form1.setFocusOnFirstActive();});
		}
	}
	else if(name=="FullView"){
		PopupWindow(Form1.getItemValue("User_Id"));
	}
	else if(name=="WWW"){
		window.open("DSGetUserSession.php?"+un()+"&Id="+Form1.getItemValue("User_Id"));
	}
	else if(name=="AddUser"){
		if(Form1.validateItem("LookupUsername")){
			Username=Form1.getItemValue("LookupUsername");
			Form1.lock();
			var LastUsername=Form0.getItemValue("Username");
			
			if(LastUsername==Username){
				setTimeout(function(){
					Popup0.show("AddUser");
					Form0SetFocusOnNextInput();
				},500);
				return;
			}
			dhxLayout.progressOn();
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=checkusername&Username="+Username,function(loader){
				dhxLayout.progressOff();
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				var ResponseArray=response.split("~");
				if(ResponseArray[0]=='OK'){
					if(ResponseArray[1]==1){
						Form0Str = [
							{ type:"fieldset",label:"Create User",width:420, list:[
								{ type:"settings",labelWidth:124,offsetLeft:10},
								{ type: "input" , name:"Username", value: Username},
								{ type: "select" , name:"Visp_Id", options:[{text:ResponseArray[3],value: ResponseArray[2],selected:true}]}
							]}
						];
						Form0.unload();
						Form0=DSInitialForm(Popup0,Form0Str,Form0PopupHelp,Form0FieldHelpId,Form0FieldHelp,Form0OnButtonClick);
						Form0OnButtonClick("Next1");
					}
					else{
						Form0Str = [
							{ type:"fieldset",label:"ایجاد کاربر",width:420, list:[
								{ type:"settings",labelWidth:124,offsetLeft:10},
								{ type: "input" , name:"Username", label:"نام کاربری:", value: Username , labelAlign:"left", maxLength:32,inputWidth:140, required:true,disabled:true},
								{ type: "select", name:"Visp_Id",label: "ارائه دهنده مجازی :",connector: RenderFile+".php?"+un()+"&act=SelectVispByUsername&Username="+Username,required:true,validate:"IsID",inputWidth:200}
							]},
							{ type: "label"},
							{type: "block", width: 450, list:[
							{ type: "button",name:"Reset",value: "بازنشانی",width :70},
							{type: "newcolumn", offset:60},
							{ type: "button",name:"Next1",value: "بعدی",width :120},
							{type: "newcolumn", offset:20},
							{ type: "button",name:"Close",value: " بستن ",width :80}
							]}
						];
						Form0.unload();
						dhxLayout.progressOn();			
						Form0=DSInitialForm(Popup0,Form0Str,Form0PopupHelp,Form0FieldHelpId,Form0FieldHelp,Form0OnButtonClick);
						Form0.lock();
						Form0.attachEvent("onOptionsLoaded", function(name){
							dhxLayout.progressOff();
							Form0.unlock();
							Popup0.show("AddUser");
							Form0.setFocusOnFirstActive();
							Form0.attachEvent("onEnter",function(){
								Form0.updateValues();
								Form0OnButtonClick("Next1");
							});
						});
					}
				}
				else{
					Form1.unlock();
					dhtmlx.alert({type:"error",text:"خطا، "+response.substring(1),callback:function(){
						Form1.setFocusOnFirstActive();
						Popup0.hide();
					}});
				}
			});
		}
	}
	else
		dhtmlx.message({text:"UnHandled button!",type:"error"});
}

function Form0OnButtonClick(name){
	if(name=='Next1'){
		Form0.lock();
		Username=Form0.getItemValue("Username");
		
		var Visp_Id=Form0.getItemValue("Visp_Id");
		var opts=Form0.getOptions("Visp_Id");
		var VispName=opts[opts.selectedIndex].text;
		// alert(Visp_Id);
		Permission=LoadPermissionByVisp(Visp_Id);
		// prompt(Permission,Permission);
		var RandomPassword=0;
		var try_to_generate_RandomPass=0;
		do{
			RandomPassword=Math.round(Math.random()*100000);
			try_to_generate_RandomPass++;
		}while ((try_to_generate_RandomPass<10)&&(RandomPassword.toString().length!=5));
		
		
		Form0Str = [
		{ type:"fieldset",label:"ایجاد کاربر", list:[
			{ type:"settings",labelWidth:124,offsetLeft:10},
			{ type: "input" , name:"Username", label:"نام کاربری :", value: ""+Username , labelAlign:"left", maxLength:32,inputWidth:140, required:true,disabled:true},
			{ type: "input" , name:"Pass", label:"کلمه عبور :" , value:RandomPassword,labelAlign:"left", maxLength:32,inputWidth:140/* , required:true */},
			{ type: "select", name:"Visp_Id",label:"ارائه دهنده مجازی :",options:[{text:VispName,value:Visp_Id}],required:true,validate:"IsID",inputWidth:200,disabled:true},
			{ type: "select", name:"Center_Id",label:"مرکز :",connector: RenderFile+".php?"+un()+"&act=SelectCenterByUsername&Visp_Id="+Visp_Id+"&Username="+Username,required:true,validate:"IsID",inputWidth:200},
			{ type: "select", name:"Supporter_Id",label:"پشتیبان :",connector: RenderFile+".php?"+un()+"&act=SelectSupporterByUsername&Username="+Username,required:true,validate:"IsID",inputWidth:200},
			{ type: "select", name:"Status_Id",label:"وضعیت :",connector: RenderFile+".php?"+un()+"&act=SelectStatus&Visp_Id="+Visp_Id,required:true,validate:"IsID",inputWidth:200},
			{ type: "input" , name:"AdslPhone", label:"ADSL تلفن :", validate:"IsValidAdslPhone", labelAlign:"left", maxLength:10,inputWidth:140, info:true},
			{ type: "input" , name:"NOE", label:"موقعیت مکانی وایرلس :", validate:"",value:"", maxLength:32,inputWidth:200, info:true},
			{ type: "input" , name:"IdentInfo", label:"شناسه شاهکار :", validate:"",value:"", maxLength:32,inputWidth:200},
			{ type: "input" , name:"IPRouteLog", label:"IPRoute لاگ :", validate:"",value:"", maxLength:100,inputWidth:200},
			{ type: "input" , name:"Email", label:"ایمیل :", validate:"IsValidEMail",value:"", maxLength:128,inputWidth:200},
			{ type: "input" , name:"Comment", label:"توضیح :", validate:"",value:"", maxLength:255,inputWidth:200},
			{type: "newcolumn", offset:20},
			{ type: "label"},
			{ type: "input" , name:"Organization", label:"نام شرکت :", validate:"",value:"", maxLength:64,inputWidth:200},
			{ type: "input" , name:"CompanyRegistryCode", label:"شماره ثبت شرکت :", validate:"",value:"", maxLength:12,inputWidth:140},
			{ type: "input" , name:"CompanyEconomyCode", label:"شماره اقتصادی شرکت :", validate:"",value:"", maxLength:12,inputWidth:140},
			{ type: "input" , name:"CompanyNationalCode", label:"شناسه ملی شرکت :", validate:"",value:"", maxLength:12,inputWidth:140},
			{ type: "input" , name:"Name", label:"نام :", validate:"",value:"", maxLength:32,inputWidth:140},
			{ type: "input" , name:"Family", label:"نام خانوادگی :", validate:"",value:"", maxLength:32,inputWidth:140},
			{ type: "input" , name:"FatherName", label:"نام پدر :", validate:"",value:"", maxLength:32,inputWidth:140},
			{ type: "input" , name:"Nationality", label:"ملیت :", validate:"",value:"", maxLength:32,inputWidth:140},
			{ type: "input" , name:"Mobile", label:"موبایل :", validate:"IsValidMobileNo",value:((/^0\d{10}$/.test(Username))?Username:""), maxLength:11,inputWidth:140,info:true/* ,required:true */},
			{ type: "input" , name:"NationalCode", label:"کد ملی :",validate:"IsValidNationalCode",value:"", maxLength:10,inputWidth:140, info:true/* ,required:true */},
			{ type: "input" , name:"BirthDate", label:"تاریخ تولد :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:140,info:true},
			{ type: "input" , name:"Phone", label:"تلفن :", validate:"",value:"", maxLength:32,inputWidth:200,info:true},
			{ type: "input" , name:"Address", label:"آدرس :", validate:"",value:"" , maxLength:255,inputWidth:200},
			{ type: "input" , name:"ExpirationDate", label:"تاریخ انقضا :", validate:"IsValidDateOrBlank",value:"", maxLength:10,inputWidth:200, info:true},
		]},
			{type: "block", width: 560, list:[
				{ type: "button",name:"Reset",value: "بازنشانی",width :70},
				{type: "newcolumn", offset:100},
				{ type: "button",name:"Next2",value: "ایجاد کاربر",width :100},
				{type: "newcolumn", offset:20},
				{ type: "button",name:"Close",value: " بستن ",width :70}
			]}
		];
		Form0.unload();
		Form0=DSInitialForm(Popup0,Form0Str,Form0PopupHelp,Form0FieldHelpId,Form0FieldHelp,Form0OnButtonClick);
		// prompt(Permission,Permission);
		SetAddFieldState(Popup0,Form0,"Pass,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate");
		Popup0.show("AddUser");
		Form0.lock();
		var PendingLoadObjects=3;

		dhxLayout.progressOn();
		Form0.attachEvent("onOptionsLoaded", function(name){
			var opts = Form0.getOptions(name);
			if(opts.length<=0){
				dhtmlx.alert("The field ["+name+"] has no choice to select");
				Form0.disableItem("Next");
			}
			else if(opts.length==1){
				Form0.disableItem(name);
			}
			PendingLoadObjects--;
			if(PendingLoadObjects<=0){
				dhxLayout.progressOff();
				Form0.unlock();
				Form0SetFocusOnNextInput();
				Form0.attachEvent("onEnter",function(){
					Form0SetFocusOnNextInput();
				});
			}
		});
		Form0.attachEvent("onFocus",function(name){ActiveItem=name;});
	}
	else if(name=='Next2'){
		if(DSFormValidate(Form0,Form0FieldHelpId)){
			
			Form0.lock();
			Popup0.hide();
			dhtmlx.confirm({
				title: "هشدار",
				type:"confirm",
				ok:"بلی",
				cancel:"خیر",
				text: "["+Form0.getItemValue("Username")+"] ایجاد کاربر<br/>آیا مطمئن هستید؟",
				callback: function(result) {
					if(result){
						MyDSFormInsertRequestProgress(dhxLayout,Form0,RenderFile+".php?"+un()+"&act=insert",Form0DoAfterInsertOk,Form0DoAfterInsertFail);
					}
					else
						Form0.unlock();
				}
			});
		}
	}	
	else if(name=='Reset'){
		Form0.setItemValue("Username","");
		Popup0.hide();
		Form1OnButtonClick("AddUser");
	}
	else if(name=='Close'){
		Popup0.hide();
	}
}

function Form0SetFocusOnNextInput(){
	var Items=["Pass","Visp_Id","Center_Id","Supporter_Id","Status_Id","AdslPhone","NOE","IdentInfo","IPRouteLog","Email","Comment","Organization","CompanyRegistryCode","CompanyEconomyCode","CompanyNationalCode","Name","Family","FatherName","Nationality","Mobile","NationalCode","BirthDate","Phone","Address","ExpirationDate"];
	var Pos=Items.indexOf(ActiveItem);
	var L=Items.length;
	
	Pos++;
	while((Pos<L)&&((Form0.isItemHidden(Items[Pos]))||(!Form0.isItemEnabled(Items[Pos]))))
		Pos++;
	
	if(Pos==L){
		Form0.updateValues();
		Form0OnButtonClick("Next2");
	}
	else{
		ActiveItem=Items[Pos];
		Form0.setItemFocus(Items[Pos]);
	}
}

function Form0DoAfterInsertOk(r){
	Form1.setItemValue("LookupUsername",Form0.getItemValue("Username"));
	Form1OnButtonClick("Retrieve");
	Form1.setItemValue("User_Id",r);
	if(ISPermit('Visp.User.Service.Base.Add'))
		Form3OnButtonClick("ServiceBase");		
}

function Form0DoAfterInsertFail(){
	Form0.unlock();
}

function Form1OnInputChange(name,value){
	if(name=="LookupUsername"){
		Form1.resetValidateCss(name);
		clearTimeout(AutoCompleteTime_Id);
		Form2.detachEvent(Form2OnOptionLoadded_Event_Id);
		Form1.hideItem("Informations");
		Popup2.hide();
		Form3.lock();
		Form3.setItemLabel("InfoBlock","");
		Popup4.hide();
		
		AutoCompleteTime_Id=setTimeout(function(){
			if(value.length>1){
				Form2.unload();
				var Form2Str=[{type: "select", name:"Username",label: "کاربران مشابه <span style='color:red'>'"+value+"'</span> :",connector: RenderFile+".php?"+un()+"&act=SelectUsername&Username="+value,inputWidth:180,size:4}];
				
				Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,function(){});
				
				Form2OnOptionLoadded_Event_Id=Form2.attachEvent("onOptionsLoaded", function(name){
					
					if(Form2.getItemValue("Username")=="Session Expire"){
						dhtmlx.message({text:"خطا،نشست منقضی شده<br/>لطقا مجدد وارد شوید",type:"error",title:"هشدار"});
						Popup2.hide();
						return;
					}
					var opts = Form2.getSelect("Username");
					opts.size=5;
					if(opts.length>=1){
						opts.onclick=Form2FillLookupUsername;
						Form2.attachEvent("onEnter",Form2FillLookupUsername);
						Popup2.show("LookupUsername");
					}
					else
						Popup2.hide();
					Form1.setItemFocus("LookupUsername");
				});
			}
			else
				Popup2.hide();
		},800);
	}
	Form1.hideItem("AddUser");
}
function Form2FillLookupUsername(){
	Form1.setItemValue("LookupUsername",Form2.getItemValue("Username"));
	Form1OnButtonClick("Retrieve");
	Form1.setFocusOnFirstActive();
}
function Form1DoAfterUpdateOk(){
	Form1OnButtonClick("Retrieve");
}
function Form1DoAfterUpdateFail(){
	Form1OnButtonClick("Retrieve");
}

function SetAddFieldState(f_Popup,f_Form,FieldList){
	//alert("VispPermit="+VispPermit);
	//alert("FieldList="+FieldList); 
	// if(LoginResellerName=='admin') return;
	f_Form.lock();
	var temp=FieldList.split(",");
	for(var i=0;i<temp.length;i++){
		//alert(temp[i]);
		
		if(temp[i]=="Pass"){
			AP=ISPermit("Visp.User.Info.AddField.Pass");
			VP=true;
		}
		else{
			AP=ISPermit("Visp.User.Info.AddField."+temp[i]);
			VP=ISPermit("Visp.User.Info.ViewField."+temp[i]);
		}
		//alert("AP="+AP);
		if(VP<=0){
			if(AP>0){
				dhtmlx.alert({text:"Permission error for field ["+temp[i]+"]!!!<br/>You have AddField permission, but do not have ViewField permission!!!",type:"alert-error",ok:"Close Window",callback:function(){
					f_Form.setItemValue("Username","");
					f_Popup.hide();
				}});
				return;
			}
			f_Form.removeItem(temp[i]);
		}
		else if(AP<=0)
			f_Form.disableItem(temp[i]);
	}
	f_Form.unlock();
}



function SetViewEditFieldState(f_Form){
	//alert("VispPermit="+VispPermit);
	//alert("FieldList="+FieldList); 
	// if(LoginResellerName=='admin') return;
	var FieldList="Pass,Reseller_Id,Username,Status_Id,InitialMonthOff,MaxPrepaidDebit,Visp_Id,Center_Id,Supporter_Id,AdslPhone,NOE,IdentInfo,IPRouteLog,Email,Comment,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,Name,Family,FatherName,Nationality,Mobile,NationalCode,BirthDate,Phone,Address,ExpirationDate";
	f_Form.lock();
	var temp=FieldList.split(",");
	//alert('1='+temp.length);
	for(var i=0;i<temp.length;i++){
		if(temp[i]=="Pass"){
			VP=ISPermit("Visp.User.GetPassword");
			EP=ISPermit("Visp.User.GetPassword")||ISPermit("Visp.User.ChangePassword");
		}
		else if(temp[i]=="Reseller_Id"){
			VP=true;
			EP=ISPermit("Visp.User.ChangeReseller");
		}
		else if(temp[i]=="Username"){
			VP=true;
			EP=ISPermit("Visp.User.ChangeUsername");
		}
		else if(temp[i]=="Status_Id"){
			VP=true;
			EP=ISPermit("Visp.User.Status.ChangeStatus");
		}
		else if(temp[i]=="InitialMonthOff"){
			VP=ISPermit("Visp.User.Info.ViewField.InitialMonthOff");
			EP=ISPermit("Visp.User.SetInitialMonthOff");
		}
		else if(temp[i]=="MaxPrepaidDebit"){
			VP=ISPermit("Visp.User.Info.ViewField.MaxPrepaidDebit");
			EP=ISPermit("Visp.User.SetMaxPrepaidDebit");
		}
		else{
			VP=ISPermit("Visp.User.Info.ViewField."+temp[i]);
			EP=ISPermit("Visp.User.Info.EditField."+temp[i]);
		}
		
		if(VP<=0){
			if(EP>0){
				dhtmlx.alert({text:"Permission error for field ["+temp[i]+"]!!!<br/>You have EditField permission, but do not have ViewField permission!!!",type:"alert-error",ok:"Close Window"});
				return;
			}
			f_Form.hideItem(temp[i]);
		}
		else if(EP<=0){
			f_Form.showItem(temp[i]);
			f_Form.disableItem(temp[i]);
			f_Form.getInput(temp[i]).style.background="";
			f_Form.getInput(temp[i]).style.paddingRight="0";
		}
		else{
			f_Form.showItem(temp[i]);
			f_Form.enableItem(temp[i]);
			
			var MyItems=["Username","Pass","InitialMonthOff","MaxPrepaidDebit","Visp_Id","Center_Id","Supporter_Id","Reseller_Id","Status_Id"];
			if(MyItems.indexOf(temp[i])>=0){
				f_Form.getInput(temp[i]).style.background="#F2F4F7 url('../dsimgs/dsMorePoints.png') no-repeat right";
				f_Form.getInput(temp[i]).style.paddingRight="20px !important;";
			}
		}
	}
	f_Form.unlock();
}

function MyDSFormUpdateRequestProgress(f_dhxLayout,f_Form,f_url,f_FormDoAfterUpdateOk,f_FormDoAfterUpdateFail,f_Callback){
	// f_dhxLayout.cells("a").progressOn();
	// f_dhxLayout.cells("b").progressOn();
	// f_dhxLayout.cells("c").progressOn();
	f_dhxLayout.progressOn();
	f_Form.lock();
	f_Form.send(f_url,"post",function(loader, response){
		f_Form.unlock();
		// f_dhxLayout.cells("a").progressOff();
		// f_dhxLayout.cells("b").progressOff();
		// f_dhxLayout.cells("c").progressOff();
		f_dhxLayout.progressOff();
		response=CleanError(response);
		
		var ErrorStr="";
		var responsearray=response.split("~",2);
		if (responsearray.length==0) ErrorStr=response;//
		if(response==""){
			ErrorStr="خطا، درخواست آپدیت،نتیجه ای نداشت";
			if(f_FormDoAfterUpdateFail!=null) f_FormDoAfterUpdateFail(response);
		}
		else if(responsearray[0]!='OK'){
			if(response[0]=='~') ErrorStr=response.substring(1);//"Error,"+response.substring(1)
			else ErrorStr=response;
			if(f_FormDoAfterUpdateFail!=null) f_FormDoAfterUpdateFail(response);
		}
		else{
			if(responsearray[1]=='') dhtmlx.message("عملیات با موفقیت انجام شد");
			else ErrorStr="Warning,"+responsearray[1];//dhtmlx.alert("Warning, "+responsearray[1]);
			if(f_FormDoAfterUpdateOk!=null) f_FormDoAfterUpdateOk(response);
		}
		if(ErrorStr!="") dhtmlx.alert({text:ErrorStr,title:"هشدار",type:"alert-error",callback:f_Callback});
	});
}

function MyDSFormInsertRequestProgress(f_dhxLayout,f_Form,f_url,f_FormDoAfterInsertOk,f_FormDoAfterInsertFail){
	// f_dhxLayout.cells("a").progressOn();
	// f_dhxLayout.cells("b").progressOn();
	// f_dhxLayout.cells("c").progressOn();
	f_dhxLayout.progressOn();
	f_Form.lock();	
	f_Form.send(f_url,"post",function(loader, response){
		f_Form.unlock();
		// f_dhxLayout.cells("a").progressOff();
		// f_dhxLayout.cells("b").progressOff();
		// f_dhxLayout.cells("c").progressOff();
		f_dhxLayout.progressOff();
		response=CleanError(response);
		var responsearray=response.split("~",2);
		if(response==""){
			dhtmlx.alert({text:"خطا، پاسخی از سرور دریافت نشد",title:"هشدار",type:"alert-error"});
			f_FormDoAfterInsertFail(response);
		}
		else if(responsearray[0]!='OK'){
			if(response[0]=='~')
				dhtmlx.alert({text:"خطا، "+response.substring(1),title:"هشدار",type:"alert-error"});
			else 
				dhtmlx.alert({text:"خطا، "+response,title:"هشدار",type:"alert-error"});
			f_FormDoAfterInsertFail(response);
		}
		else{
			f_rowid=responsearray[1];
			if(!IsValidRowId(f_rowid))
				dhtmlx.alert({text:"خطا، شناسه سطر معتبر نیست->"+response[1],title:"هشدار",type:"alert-error"});
			else {
				f_FormDoAfterInsertOk(f_rowid);
				dhtmlx.message("Your data has been successfully saved!");
			}	
		}	

	});
}

function PopupWindow(SelectedRowId){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"User");
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=User_Id,"+SelectedRowId, false);
}	
}//window.onload
function UpdateGrid(r){
	alert(1);
}
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
