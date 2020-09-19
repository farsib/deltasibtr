<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSBatchProcess_Import_List ....................................................................................");
	PrintInputGetPost();
	$ISNoneBlock=DBSelectAsString("SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')");
	if(($ISNoneBlock=="No")||($LastError!="")||(extension_loaded("mbstring")==false)){
		?>
		<html><head><script type="text/javascript">
		window.onload = function(){
			var LastError="<?php echo $LastError;?>";
			var mbstring="<?php echo ((extension_loaded("mbstring")==false)?('No'):('Yes'));?>";
			if(LastError!="")
				parent.dhtmlx.alert("<?php echo escape($LastError) ?>");//"Session Expire, Please Relogin"
			else if(mbstring!='Yes')
				parent.dhtmlx.alert({text:"بارگذاری نشده PHP mbstring بسته<br/>را اجرا کنید <span style='color:red'>yum install php-mbstring</span> در لینوکس</br> را اجرا کنید <span style='color:red'>service httpd restart</span> و سپس",type:"alert-error",ok:"بستن"});
			else{
				parent.dhtmlx.confirm({
					title: "خطا",
					type:"confirm-error",
					cancel: "باشه",
					ok: "اطلاعات بیشتر",
					text: "آی پی شما قابل اعتماد نیست و ممکن است در ادامه مسدود شود.آی پی خود را در ' آی پی که مسدود نشود ' اضافه نمایید و مجدد وارد پنل شده و امتحان کنید",
					callback: function(Result){
						if(Result)
							parent.dhtmlx.alert({title: "اطلاعات بیشتر",type: "alert-warning",text: "لطفا آی پی خود را در ' مدیریت ' -> ' سرور ' -> ' مدیر ' -> ' آی پی که بلاک نمی شود ' اضافه نمایید"});
					}
				});
			}
			var button = document.createElement("input");
			button.type = "button";
			button.value = "مشکل را حل کنید و برای بروز کردن اینجا کلیک کنید";
			button.onclick = function(){window.location.reload();};
			button.style.width = "100%";
			button.style.height = "100%";
			button.style.color = "blue";
			button.style.font = "bold 100% tahoma";
			document.body.appendChild(button);
		}
		</script></head></html>
		<?php
			exit();
		}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
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
		.dhtmlx-myCss{
			color:white;
			background-color:green;
		}
		.ProgressFrameCss{
			text-align:center;
			width:250px;
			border:2px solid black;
			padding:2px;
			height:14px;
			margin: 20px auto 0;
			border-radius:5px 5px 5px 5px;
		}
		.ProgressBodyCss{
			width:0;
			height:100%;
			background-repeat:repeat-x;
			background-position:0 0;
			background-size:28px 14px;
			background-image:linear-gradient(-45deg,transparent,transparent 33%,rgba(0,0,0,.1) 33%,rgba(0,0,0,0.1) 66%,transparent 66%,transparent);
			border-radius:3px 3px 3px 3px;
			animation: ProgressShift 1s infinite linear;
		}
		.ProgressTextCss{
			color:black;
			font-weight:bolder;
			padding:1px 0 0 0;
			font-size:84%;
		}
		@keyframes ProgressShift{
			from {background-position:0px 0px;}
			to {background-position:28px 0px;}
		}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
var SelectedRowId=0;

window.onload = function(){


//VARIABLE ------------------------------------------------------------------------------------------------------------------------------
	var tabcount=parent.tabcount;
	var OnTabClose_EventId;
	var IsDebugEnable="<?php echo $DebugLevel;?>";
	// Layout   ===================================================================
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	var ExtraFilter="";
	var MyTimer=0;
	var TimerStringInterval_Id;
	var TNow="<?php echo DBSelectAsString("SELECT SHDATETIMESTR(NOW())");?>";
	var TotalUserCount=0;
	var FailCount=0;
	var ImportPassedCount=0;
	var ImportOKCount=0;
	var ImportFailCount=0;
	var PauseState=false;
	var ReqStar="<span style='color:red;font-weight:bold'> *</span>"
	var dhtmlxRequired="<span class='dhxform_item_required'>*</span>"
	var dhtmlxInfo="<span class='dhxform_info'>[?]</span>"
	var DataTitle="وارد کردن یا ساختن کاربر";
	var DataName="DSBatchProcess_Import_";
	var RenderFile=DataName+"ListRender";
	var ImportId=0;
	var GColIds="";
	var GColHeaders="";

	var ISFilter=false;
	var FilterState=false;
	var GColFilterTypes=[];
	var GColFooter="";
	var GColInitWidths="";
	var GColAligns="";
	var GColTypes="";
	var HeaderAlignment=[];
	var ISSort=true;
	var GColSorting="";
	var ColSortIndex=0;
	var SortDirection='desc';
	var FilterRowNumber=0;
	var PopupProgress;
//CONTROLS------------------------------------------------------------------------------------------------------------------------------
	//=======PopupImport
	var PopupImport;
	var PopupImport_Id=['Import'];//popup Attach to Which Buttom of Toolbar
	//=======FormImport
	var FormImport;
	var FormImportPopupHelp;
	var FormImportFieldHelp  = {
		DefaultReseller:"<div style='text-align:center;color:midnightblue'>تعیین نماینده فروش برای کاربرانی که نماینده فروش آن ها در فایل مشخص نشده</div>",
		DefaultVisp:"<div style='text-align:center;color:midnightblue'>تعیین ارائه دهنده مجازی اینترنت برای کاربرانی که ارائه دهنده مجازی اینترنت آن ها در فایل مشخص نشده</div>",
		DefaultCenter:"<div style='text-align:center;color:midnightblue'>تعیین مرکز برای کاربرانی که مرکز آن ها در فایل مشخص نشده</div>",
		DefaultSupporter:"<div style='text-align:center;color:midnightblue'>تعیین پشتیبان برای کاربرانی که پشتیبان آن ها در فایل مشخص نشده</div>",
		DefaultStatus:"<div style='text-align:center;color:midnightblue'>تعیین وضعیت برای کاربرانی که وضعیت آن ها در فایل مشخص نشده</div>",
		UserCount:"<div style='text-align:center;color:midnightblue'>تعداد کاربرانی که می خواهید ایجاد شود<br/>(هربار ،حداکثر ۹۹۹۹ کاربر)</div>",
		OrderType:"<div style='text-align:center;color:midnightblue'>Choose whether you want to generate<br/>a random set or ordinal set of Username.</div>",
		FixPartOfUserName:"<div style='text-align:center;color:midnightblue'>Enter the fix part of Username.<br/>This part will be constant in prefix of generated usernames.<br/>For example <span style='color:crimson'>User_</span> .</div>",
		SequenceFrom:"<div style='text-align:center;color:midnightblue'>Enter the start of sequence to generate users...<br/>Characters order is 0 to 9 and then a to z.<br/>For example <span style='color:crimson'>200</span> or <span style='color:crimson'>abbc</span> or <span style='color:crimson'>b1200</span> .</div>",
		SequenceTo:"<div style='text-align:center;color:midnightblue'>Enter the end of sequence to generate users by concatenating<br/>FixPart and generated sequence between<br/>SequenceFrom to this by increasing character order.<br/>Characters order is 0 to 9 and then a to z.<br/>For example <span style='color:crimson'>800</span> or <span style='color:crimson'>dwzz</span> or <span style='color:crimson'>c2000</span> .</div>",
		UsernameMask:"<div style='text-align:center;color:midnightblue'>برای نام کاربری از کاراکترهای معتبر و $ و # و . استفاده نمایید<br/>مثال : <span style='color:crimson'>User.$$###</span></div>",
		PasswordMask:"<div style='text-align:center;color:midnightblue'>برای کلمه عبور از کاراکترهای معتبر و $ و # و . استفاده نمایید<br/>مثال :<span style='color:crimson'>Pass#####</span></div>",
		NotUsedChar:"<div style='text-align:center;color:midnightblue;direction: rtl;'>مثال : <span style='color:crimson'>qw5</span> که از کاراکترهای<span style='color:crimson'> q</span> و <span style='color:crimson'>w</span> به عنوان حرف و <span style='color:crimson'>5</span> به عنوان عدد در الگو نام کاربری استفاده نشود</div>",
		IgnoreFieldLength:"<div style='text-align:center;color:midnightblue'>تیک این گزینه را برای حذف مقادیری که دارای  کاراکتر اضافه هستند،بگذارید تا خطا نادیده گرفته شود</div>",
		IgnoreFieldCount:"<div style='text-align:center;color:midnightblue'>این گزینه برای نادیده گرفتن خطا هنگامی است که تعداد ستون ها در ردیف،با ستون های عنوان متفاوت است</div>",
		Delimiter:"<div style='text-align:center;color:midnightblue'>جداکننده فیلد ها در فایل</div>"
	};
	var FormImportFieldHelpId=["DefaultReseller","DefaultVisp","DefaultCenter","DefaultSupporter","DefaultStatus","UserCount","OrderType","FixPartOfUserName","SequenceFrom","SequenceTo","UsernameMask","PasswordMask","NotUsedChar","IgnoreFieldLength","IgnoreFieldCount","Delimiter"];
    var FormImportStr = [
		{type: "fieldset", label: "عملیات", width:410, list: [
			{type: "select", label: "انتخاب عملیات:", name: "Action", labelWidth:120,inputWidth:230, required: true, options:[
				{text: "وارد کردن کاربر", value: "ImportUser", selected: true, list:[
					{type: "label", label:"<div style='width:100%;height:9px;border-bottom: 1px solid #336699;'></div>",labelAlign:"center"},
					{
					type: "upload",
					name: "FileUploader",
					mode: "html5",
					inputWidth: 320,
					inputHeight: 60,
					titleScreen: true,
					autoStart: true,
					autoRemove:false,
					titleText :"<div style='direction: rtl;'>کلیک کنید یا فایلها را اینجا بکشید<br/>(حداکثر "+"<?php echo min(ini_get("upload_max_filesize"),ini_get("post_max_size"));?>"+" به ازای هر فایل)</div>",
					url: RenderFile+".php?"+un()+"&act=UploadFile"
					},
					{type:"block", name:"AdvancedBlock", hidden:true, list:[
						{type: "select", label: "جداکننده :", name: "Delimiter", labelWidth:100,inputWidth:170, required: true, options:[
							{text: "خودکار", value: "Auto", selected: true},
							{text: "کاما", value: "Comma"},
							{text: "تب", value: "Tab"}
						], info: true},
						{type: "checkbox", label: "حذف کاراکترهای اضافی در صورت وجود", name: "IgnoreFieldLength", position: "label-right", checked: false , info:true},
						{type: "checkbox", label: "نادیده گرفتن ردیف در خطای شمارش فیلد", name: "IgnoreFieldCount", position: "label-right", checked: false, info:true}
					]},
					{type:"label"},
					{type:"block", list:[
						{type:"newcolumn",offset:10},
						{type:"button", name:"Advanced",value: " پیشرفته ",width :60},
						{type:"newcolumn",offset:10},
						{type:"button", name:"CheckFiles",value: " بررسی محتوای فایل ها ",width :180}
					]}

				]},
				{text: "ساختن کاربر", value: "GenerateUser", list:[
					{type: "label", label:"<div style='width:101%;height:9px;border-bottom: 1px solid #336699;'></div>",labelAlign:"center"},
					{type: "select", label: "ترتیب نام کاربری :", name: "OrderType", labelWidth:150,inputWidth:158, required: true, options:[
						{text: "تصادفی", value: "Random", selected: true},
						{text: "به ترتیب", value: "Sequential"}
					], info: false},
					{type: "input", label: "تعداد کاربران :", name:"UserCount", value:100, validate:"ValidInteger", required: true, labelWidth:168,inputWidth:90, maxLength:4, info: true},
					{ type: "input", label:"الگو نام کاربری :", name:"UsernameMask", value:"user$$$$##", required:true, validate:"^([a-zA-Z0-9-_.=@\$\#]+)$", maxLength:32, labelWidth:168,inputWidth:160, info: true},
					{ type: "input", label:"بخش ثابت نام کاربری :", name:"FixPartOfUserName", value:"user_", validate:"IsValidUserName"/*^([a-zA-Z0-9]+)$"*/, maxLength:28, labelWidth:150,inputWidth:160, hidden: true, info: true},

					{ type: "input", label:"به ترتیب از :", name:"SequenceFrom", value:"100", required:true, validate:"^([a-zA-Z0-9]+)$", maxLength:4, labelWidth:150,inputWidth:160, hidden: true, info: true},
					{ type: "input", label:"به ترتیب تا :", name:"SequenceTo", value:"220" ,required:true, validate:"^([a-zA-Z0-9]+)$", maxLength:4, labelWidth:150,inputWidth:160, hidden: true, info: true},

					{ type: "input", label:"الگوی کلمه عبور :", name:"PasswordMask", value: "$$$$$##", required:true, maxLength:32, labelWidth:168,inputWidth:160, info: true},
					{ type: "input", label:"این کاراکترها در نام کاربری نباشد :", name:"NotUsedChar", validate:"^([a-zA-Z0-9]*)$", maxLength:35, labelWidth:168,inputWidth:160, info: true},
					{type: "label", name:"MyInfo", label:"<div style='color:midnightblue;font-size:92%;font-weight:normal;text-align:center'>Generate from user_100 to user_220</div>", labelWidth:310,hidden:true},
					{type:"block",list:[
						{type: "checkbox", label: "استفاده از اعداد در نام کاربری", name: "UseDigits", position: "label-right", checked: true, hidden: true, disabled:true},
						{type:"newcolumn",offset:15},
						{type: "checkbox", label: "استفاده از حروف در نام کاربری", name: "UseLetters", position: "label-right", checked: false, hidden: true}
					]},
					{type: "label", name:"WildCardLegend", label:"<div style='width:100%;font-size:92%;font-weight:normal'>"+
						"<div style='width:45%;float:left;text-align:left'><span style='color:crimson'>#</span> : {اعداد در بازه {0..9</div>"+
						"<div style='width:45%;float:right;text-align:right'><span style='color:crimson'>$</span> : {a..z} حروف در بازه</div>"+
					"</div>"},
					{type:"block",list:[
						{type:"newcolumn",offset:25},
						{type:"button", name:"CheckFields",value: " بررسی فیلدهای ورودی ",width :222}
					]}
				]},
			]}
		]},
		{type: "fieldset", label: "مقادیر پیش فرض ", width:410, list: [
			{type: "select", label:"نماینده فروش :", name:"DefaultReseller",
					connector: RenderFile+".php?"+un()+"&act=SelectReseller", labelWidth:120,inputWidth:230, info: true
			},
			{type: "select", label:"ارائه دهنده مجازی :", name:"DefaultVisp",
					connector: RenderFile+".php?"+un()+"&act=SelectVisp", labelWidth:120,inputWidth:230, info: true
			},
			{type: "select", label:"مرکز :", name:"DefaultCenter",
					connector: RenderFile+".php?"+un()+"&act=SelectCenter", labelWidth:120,inputWidth:230, info: true
			},
			{type: "select", label:"پشتیبان :", name:"DefaultSupporter",
					connector: RenderFile+".php?"+un()+"&act=SelectSupporter", labelWidth:120,inputWidth:230, info: true
			},
			{type: "select", label:"وضعیت :", name:"DefaultStatus",
					connector: RenderFile+".php?"+un()+"&act=SelectStatus", labelWidth:120,inputWidth:230, info: true
			},
			{type: "label"}
		]},
		{type: "block", width: 400, list:[
				{type: "button",name:"GetHint",value: " دریافت فایل راهنما ",width :130},
				{type: "newcolumn", offset:50},
				{type: "button",name:"Close",value: " بستن ",width :80},
				{type: "newcolumn", offset:10},
				{type: "button",name:"Proceed",value: " برو ",width :80}
		]}
	];

	//=======PopupCheckForError
	var PopupCheckForError;
	var PopupCheckForError_Id=['CheckForError'];//popup Attach to Which Buttom of Toolbar
	//=======FormCheckForError
	var FormCheckForError;
	var FormCheckForErrorPopupHelp;
	var FormCheckForErrorFieldHelp  = {Username:""};
	var FormCheckForErrorFieldHelpId=["Username"];
    var FormCheckForErrorStr = [
		{type: "fieldset", label: "تجزیه انجام شد.بررسی پارامترهای دیگر", width:400, list: [
			{type:"block",list:[
				{type: "hidden", name: "InfoCreateReseller", value:0},
				{type:"newcolumn",offset:10},
				{type:"button", name:"CreateReseller",value: " Reseller ",width :285, hidden:true}
			]},
			{type:"block",list:[
					{type: "hidden", name: "InfoCreateVisp", value:0},
					{type:"newcolumn",offset:10},
					{type:"button", name:"CreateVisp",value: " Visp ",width :285, hidden:true}
			]},
			{type:"block",list:[
					{type: "hidden", name: "InfoCreateCenter", value:0},
					{type:"newcolumn",offset:10},
					{type:"button", name:"CreateCenter",value: " Center ",width :285, hidden:true}
			]},
			{type:"block",list:[
					{type: "hidden", name: "InfoCreateSupporter", value:0},
					{type:"newcolumn",offset:10},
					{type:"button", name:"CreateSupporter",value: " Supporter ",width :285, hidden:true}
			]},
			{type:"block", list:[
					{type: "hidden", name: "InfoCreateStatus", value:0},
					{type:"newcolumn",offset:10},
					{type:"button", name:"CreateStatus",value: " Status ",width :285, hidden:true}
			]},
			{type: "label"},
			{type: "checkbox", label: "بررسی الگوی نام کاربری در ارائه دهنده مجازی اینترنت،مرکز و پشتیبان", name: "PatternCheck", position: "label-right", checked: false},
			{type: "checkbox", label: "بررسی وضعیت پیش فرض برای وضعیت کاربر", name: "InitialStatusCheck", position: "label-right", checked: false},
			{type: "label"}
		]},
		{type: "block", width: 380, list:[
			{type: "newcolumn", offset:170},
			{type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:10},
			{type: "button",name:"Proceed",value: " بررسی ",width :80}
		]}
	];

	//=======PopupCreateUser
	var PopupCreateUser;
	var PopupCreateUser_Id=['CreateUser'];//popup Attach to Which Buttom of Toolbar
	//=======FormCreateUser
	var FormCreateUser;
	var FormCreateUserPopupHelp;
	var FormCreateUserFieldHelp  = {Username:""};
	var FormCreateUserFieldHelpId=["Username"];
    var FormCreateUserStr = [
		{type: "fieldset", label: "ساخت کاربر", name: "F1", width: 400, list:[
			{type: "input" , position: "label-top", name: "ImportName", label:"برای عملیات گروهی یک نام تعریف کنید : ",labelHeight:20,value:""
			, maxLength: 64,rows: 2,inputWidth: 350,
			note:{text:"این نام در آینده قابل ارجاع خواهد بود"}},
			{type:"label", name:"ImportSummary",label:"",labelWidth:350,labelHeight:54,hidden:true},
			{type:"block", hidden:true, name: "IgnoreErrorBlock", list:[
				{type:"label"},
				//{type: "newcolumn", offset:60},
				{type: "checkbox", label: "Ignore on errors and continue import...", name: "IgnoreError", position: "label-right", checked: false}
			]}
		]},
		{type: "block", width: 380, list:[
			{type: "button", name:"CancelImport", value: " لغو ", width :120,disabled:true},
			{type: "newcolumn", offset:50},
			{type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:10},
			{type: "button",name:"Proceed",value: " انجام ",width :80}
		]}
	];


	// ToolbarOfGrid   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);


	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",function(){LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh)});
	ToolbarOfGrid.addSeparator("sep1",null);
	ToolbarOfGrid.hideItem("Retrieve");
	ToolbarOfGrid.hideItem("sep1");

	AddPopupImport();
	AddPopupCheckForError();
	AddPopupCreateUser();
	ToolbarOfGrid.addSeparator("sep2",null);
	ToolbarOfGrid.addText("TimerStringLabel",null,"زمان تقریبی سپری شده : ");
	ToolbarOfGrid.addText("TimerString",null,"");
	ToolbarOfGrid.hideItem("TimerStringLabel");
	ToolbarOfGrid.hideItem("TimerString");
	DSToolbarAddButton(ToolbarOfGrid,null,"SaveToFile","ذخیره نتایج","SaveToFile",ToolbarOfGrid_OnSaveToFileClick);
	ToolbarOfGrid.setItemToolTip("SaveToFile","CSVذخیره نتایج گزارش در فایل");
	ToolbarOfGrid.hideItem("SaveToFile");

	ToolbarOfGrid.addSpacer("SaveToFile");
	DSToolbarAddButton(ToolbarOfGrid,null,"DeleteSelected","حذف مورد انتخاب شده","DeleteSelected",ToolbarOfGrid_OnDeleteSelectedClick);
	ToolbarOfGrid.setItemToolTip("DeleteSelected","Delete selected row");
	ToolbarOfGrid.hideItem("DeleteSelected");
	DSToolbarAddButton(ToolbarOfGrid,null,"DeleteErrors","حذف خطاها","DeleteErrors",ToolbarOfGrid_OnDeleteErrorsClick);
	ToolbarOfGrid.setItemToolTip("DeleteErrors","Ignore all the row have error");
	ToolbarOfGrid.hideItem("DeleteErrors");


	PopupProgress = new dhtmlXPopup({toolbar: ToolbarOfGrid,id:["TimerString"],mode:"bottom"});
	PopupProgress.attachEvent("onContentClick",PopupProgressOnContentClick);

	if(!(!!window.chrome && !!window.chrome.webstore))
		dhtmlx.alert({title: "هشدار",type: "alert-warning",text: "<span style='color:red'> توصیه می شود که <br/><span style='font-weight:bold'>از گوگل کروم بروز شده</span><br/>برای عملیات گروهی استفاده کنید</span>", callback:function(){setTimeout(DoSomthing,200)},ok:"باشه"
	});
	else
		DoSomthing();

	document.oncontextmenu = function(){return false};
	document.ondrop = function(){return false};
	document.onselectstart= function(){return false};

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	//*********************************************************************************

//FUNCTIONS--------------------------------------------------------------------------------------------------------------------
function ToolbarOfGrid_OnDeleteSelectedClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		text: "آبا از حذف مورد انتخاب شده مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DeleteSelected&id="+ImportId+"&RowId="+SelectedRowId,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					var responsearray=response.split("~",3);
					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(responsearray[0]=='OK'){
						TotalUserCount=responsearray[2];
						if(TotalUserCount<=0)
							dhtmlx.alert({text:"کاربری برای واردکردن باقی نمانده است.از ابتدا شروع کنید",callback:function(){
								parent.tabbar.detachEvent(OnTabClose_EventId);
								parent.BatchProcessInstanceCount--;
								setTimeout(function(){
									PopupImport.show("Import")
								},200)
							},type:"error",ok:"بستن"});
						else
							dhtmlx.alert({
								text:"تعداد "+responsearray[1]+" سطر با موفقیت از لیست حذف شد<br/>تعداد "+responsearray[2]+" سطر باقی مانده است<br/><span style='color:green'>برای ادامه،مجدد بررسی خطا انجام دهید</span>",
								ok:"بستن",
								callback:function(){
									setTimeout(function(){
										PopupCheckForError.show("CheckForError");
										FormCheckForError.setItemFocus("Proceed");
									},200)
								}
							});
					}
					else alert(response);
					LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
				});
			}
		}
	});
}

function ToolbarOfGrid_OnDeleteErrorsClick(){
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		text: "این کار باعث حذف همه ی سطر های دارای خطا می شود<br/>آیا مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DeleteErrors&id="+ImportId,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					var responsearray=response.split("~",3);
					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(responsearray[0]=='OK'){
						TotalUserCount=responsearray[2];
						if(TotalUserCount<=0)
							dhtmlx.alert({text:"کاربری برای واردکردن باقی نمانده است.از ابتدا شروع کنید",callback:function(){
								parent.tabbar.detachEvent(OnTabClose_EventId);
								parent.BatchProcessInstanceCount--;
								setTimeout(function(){
									PopupImport.show("Import")
								},200)
							},type:"error",ok:"بستن"});
						else
							dhtmlx.alert({
								text:"Successfully deleted all the existed errors from the list.<br/>"+responsearray[1]+" row(s) deleted.<br/>"+responsearray[2]+" row(s) remained.<br/><span style='color:green'>Please check the list for error again to continue...</span>",
								callback:function(){
									setTimeout(function(){
										PopupCheckForError.show("CheckForError");
										FormCheckForError.setItemFocus("Proceed");
									},200)
								}
							});
					}
					else alert(response);
					LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);

				});
			}
		}
	});
}
function PopupProgressOnContentClick(){
	if(PauseState==false){
		dhtmlx.message({text:"<span style='color:white;font-weight:bold'>Request to pause process...</span>",expire:3000,type:"error"});
		document.getElementById("ProgressBar").style.backgroundColor="#f5ba25"
		document.getElementById("ProgressText").innerHTML ="Wait...";
		PauseState=true;
	}
}

function DoSomthing(){
	if(IsDebugEnable>0)
		dhtmlx.alert({title: "هشدار",type: "alert-error",text: "حالت اشکال زدایی فعال است و ممکن است باعث شود بارگیری سرور در طی فرآیند عملیات گروهی شود!<br/>برای غیرفعال سازی، ds_nodebug را در لینوکس اجرا کنید.", callback:function(){setTimeout(function(){PopupImport.show("Import")},200)},ok:"باشه"
		});
	else
		PopupImport.show("Import");
}
//-------------------------------------------------------------------AddPopupImport()
function AddPopupImport(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Import","شروع","tow_Import");
	ToolbarOfGrid.setItemToolTip("Import","شروع به وارد کردن کاربران از طریق فایل یا ساختن کاربر");
	PopupImport=DSInitialPopup(ToolbarOfGrid,PopupImport_Id,PopupImportOnShow);
}

//-------------------------------------------------------------------PopupImportOnShow()
function PopupImportOnShow(){
	if(!ISValidResellerSession()) return;
	if((TotalUserCount==0)||((TotalUserCount>0)&&confirm("This will abandon current job with "+TotalUserCount+" user(s)..."))){
		if(TotalUserCount>0){
			parent.tabbar.detachEvent(OnTabClose_EventId);
			parent.BatchProcessInstanceCount--;
		}
		TotalUserCount=0;
		ToolbarOfGrid.hideItem("CreateUser");
		ToolbarOfGrid.hideItem("SaveToFile");
		ToolbarOfGrid.hideItem("DeleteErrors");
		ToolbarOfGrid.hideItem("DeleteSelected");
		ToolbarOfGrid.disableItem("CheckForError");
		FormImport=DSInitialForm(PopupImport,FormImportStr,FormImportPopupHelp,FormImportFieldHelpId,FormImportFieldHelp,FormImportOnButtonClick);
		MyHint = new dhtmlXPopup({form: FormImport,id:["FixPartOfUserName","SequenceFrom","SequenceTo"],mode:"right"});
		FormImport.attachEvent("onInputChange",FormImportOnInputChange);
		FormImport.attachEvent("onBeforeChange",FormImportOnBeforeChange);
		PopupImport.attachEvent("onHide",function(){MyHint.hide()});
		FormImport.attachEvent("onFocus",function(name){if((name=="FixPartOfUserName")||(name=="SequenceFrom")||(name=="SequenceTo"))FormImportOnInputChange(name,FormImport.getItemValue(name)); else MyHint.hide();});
		if(typeof mygrid != "undefined")
			mygrid.clearAll(true);
	}
	else
		PopupImport.hide();
}

//-------------------------------------------------------------------FormImportOnBeforeChange(name,value,newvalue)
function FormImportOnBeforeChange(name,value,newvalue){
	// dhtmlx.message("FormImport changed. name="+name+"   value="+value+"   newvalue="+newvalue);
	if(name=="UseDigits"){
		if((newvalue)&&(!FormImport.isItemChecked("UseLetters"))){
			alert("خطا، At least one of the UseDigits or UseLetters should be selected");
			return false;
		}
		FormImport.setItemFocus("FixPartOfUserName");
		setTimeout(function(){FormImportOnInputChange("FixPartOfUserName",FormImport.getItemValue("FixPartOfUserName"))},100);
	}
	else if(name=="UseLetters"){
		if((newvalue)&&(!FormImport.isItemChecked("UseDigits"))){
			alert("خطا، At least one of the UseLetters or UseDigits should be selected");
			return false;
		}
		FormImport.setItemFocus("FixPartOfUserName");
		setTimeout(function(){FormImportOnInputChange("FixPartOfUserName",FormImport.getItemValue("FixPartOfUserName"))},100);
	}
	else if(name=="Action"){
		if(newvalue=="ImportUser"){
			FormImport.enableItem("GetHint");
			FormImport.setItemLabel("DefaultReseller","نماینده فروش :"+dhtmlxInfo);
			FormImport.setItemLabel("DefaultVisp","ارائه دهنده مجازی :"+dhtmlxInfo);
			FormImport.setItemLabel("DefaultCenter","مرکز :"+dhtmlxInfo);
			FormImport.setItemLabel("DefaultSupporter","پشتیبان :"+dhtmlxInfo);
			FormImport.setItemLabel("DefaultStatus","وضعیت :"+dhtmlxInfo);
		}
		else{//GenerateUser
			FormImport.disableItem("GetHint");
			FormImport.setItemLabel("DefaultReseller","نماینده فروش :"+dhtmlxInfo+dhtmlxRequired);
			FormImport.setItemLabel("DefaultVisp","ارائه دهنده مجازی :"+dhtmlxInfo+dhtmlxRequired);
			FormImport.setItemLabel("DefaultCenter","مرکز :"+dhtmlxInfo+dhtmlxRequired);
			FormImport.setItemLabel("DefaultSupporter","پشتیبان :"+dhtmlxInfo+dhtmlxRequired);
			FormImport.setItemLabel("DefaultStatus","وضعیت :"+dhtmlxInfo+dhtmlxRequired);
		}
	}
	else if(name=="OrderType"){
		if(newvalue=="Sequential"){
			FormImport.showItem("FixPartOfUserName");
			FormImport.showItem("SequenceFrom");
			FormImport.showItem("SequenceTo");
			FormImport.showItem("UseDigits");
			FormImport.showItem("UseLetters");
			// FormImport.showItem("MyInfo");
			//FormImport.hideItem("WildCardLegend");
			FormImport.hideItem("UserCount");
			FormImport.hideItem("UsernameMask");
			FormImport.hideItem("NotUsedChar");
			// FormImport.hideItem("GenerationTimeout");
			FormImport.setItemFocus("FixPartOfUserName");
		}
		else{//Random
			FormImport.hideItem("FixPartOfUserName");
			FormImport.hideItem("SequenceFrom");
			FormImport.hideItem("SequenceTo");
			FormImport.hideItem("UseDigits");
			FormImport.hideItem("UseLetters");
			// FormImport.hideItem("MyInfo");
			//FormImport.showItem("WildCardLegend");
			FormImport.showItem("UserCount");
			FormImport.showItem("UsernameMask");
			FormImport.showItem("NotUsedChar");
			// FormImport.showItem("GenerationTimeout");
			FormImport.setItemFocus("UserCount");
		}
	}
	else if((name=="FixPartOfUserName")||(name=="UsernameMask")||/*(name=="PasswordMask")||*/(name=="NotUsedChar")||(name=="SequenceFrom")||(name=="SequenceTo"))
		FormImport.setItemValue(name,newvalue.toLowerCase());
	return true;
}

//-------------------------------------------------------------------FormImportOnInputChange(name,value)
function FormImportOnInputChange(name,value){
	//dhtmlx.message("FormImport Inputchanged. name="+name+"   value="+value);

	if((name=="SequenceFrom")||(name=="SequenceTo")||(name=="FixPartOfUserName")){
		var F="",S1="",S2="";
		if(name=="SequenceFrom"){
			F=FormImport.getItemValue("FixPartOfUserName").toLowerCase();
			S1=value.toLowerCase();
			S2=FormImport.getItemValue("SequenceTo").toLowerCase();
			value=S1+S2;
		}
		else if(name=="SequenceTo"){
			F=FormImport.getItemValue("FixPartOfUserName").toLowerCase();
			S1=FormImport.getItemValue("SequenceFrom").toLowerCase();
			S2=value.toLowerCase();
			value=S1+S2;
		}
		else{
			F=value.toLowerCase();
			S1=FormImport.getItemValue("SequenceFrom").toLowerCase();
			S2=FormImport.getItemValue("SequenceTo").toLowerCase();
			value="";
		}

		if(name!="FixPartOfUserName"){
			if(value.match(/[0-9]/)!=null){
				FormImport.checkItem("UseDigits");
				FormImport.disableItem("UseDigits");
			}
			else
				FormImport.enableItem("UseDigits");

			if(value.match(/[a-z]/)!=null){
				FormImport.checkItem("UseLetters");
				FormImport.disableItem("UseLetters");
			}
			else
				FormImport.enableItem("UseLetters");
		}

		var UseChar="";
		if(FormImport.isItemChecked("UseLetters")&&!FormImport.isItemChecked("UseDigits"))
			UseChar="</br><span style='color:teal;font-size:90%'>فقط از حروف بین <span style='color:darkgreen;font-weight:bold'>a تا z</span> برای تولید سری استفاده می شود</span>";
		else if(!FormImport.isItemChecked("UseLetters")&&FormImport.isItemChecked("UseDigits"))
			UseChar="</br><span style='color:teal;font-size:90%'>فقط از اعداد بین <span style='color:darkgreen;font-weight:bold'>۰ تا ۹</span> برای تولید سری استفاده می شود</span>";
		else
			UseChar="</br><span style='color:teal;font-size:90%'>از اعداد <span style='color:darkgreen;font-weight:bold'>۰ تا ۹ </span>و حروف <span style='color:darkgreen;font-weight:bold'>a تا z</span> برای تولید سری استفاده می شود</span>";

		if((S1!="")&&(S2!="")){
			if(S1.length==S2.length){
				for(i=0;i<S1.length;++i)
					if(S1.substr(i,1)!=S2.substr(i,1))
						break;
				if(i>0){
					MyHint.attachHTML("<div style='color:brown;text-align:center'>Both SequenceFrom and SequenceTo are started with '<span style='font-weight:bold'>"+S1.substring(0,i)+"</span>'.</br>Remove it from here and add it to the end of FixPartOfUserName</div>");
					name="SequenceFrom";
				}
				else if(S1.localeCompare(S2)==-1)
					MyHint.attachHTML("<div style='color:midnightblue;font-weight:normal;text-align:center;direction:rtl'>تولید کاربر از <span style='font-weight:bold;color:mediumblue'>"+F+"<span style='color:royalblue'>"+S1+"</span></span> تا <span style='font-weight:bold;color:mediumblue'>"+F+"<span style='color:royalblue'>"+S2+"</span></span>"+UseChar+"</div>");
				else{
					MyHint.attachHTML("<div style='color:firebrick;font-weight:bold;text-align:center'>SequenceFrom must be less than SequenceTo</div>");
					name="SequenceFrom";
				}
			}
			else if(S1.length<S2.length){
				MyHint.attachHTML("<div style='color:red;font-weight:bold;text-align:center'>Length of SequenceFrom and SequenceTo must be equal.</div>");
				name="SequenceFrom";
			}
			else{
				MyHint.attachHTML("<div style='color:red;font-weight:bold;text-align:center'>Length of SequenceFrom and SequenceTo must be equal.</div>");
				name="SequenceTo";
			}
		}
		else{
			MyHint.attachHTML("<div style='color:maroon;font-weight:bold;text-align:center'>Cannot be empty</div>");
			if(S1=="")
				name="SequenceFrom";
			else
				name="SequenceTo";
		}
		MyHint.show(name);
	}
}
//-------------------------------------------------------------------FormImportOnButtonClick(name)
function FormImportOnButtonClick(name){
	if(name=='Close') {
		PopupImport.hide();
	}
	else if(name=='Proceed'){

		if(!MyValidate())
			return;
		var Action=FormImport.getItemValue("Action");
		if(Action=="GenerateUser"){
			if(FormImport.getItemValue("DefaultReseller")==0){
				alert("برای ساخت کاربر می بایست نماینده فروش تعیین شود");
				FormImport.setItemFocus("DefaultReseller");
				return false;
			}
			else if(FormImport.getItemValue("DefaultVisp")==0){
				alert("برای ساخت کاربر می بایست ارائه دهنده مجازی اینترنت تعیین شود");
				FormImport.setItemFocus("DefaultVisp");
				return false;
			}
			else if(FormImport.getItemValue("DefaultCenter")==0){
				alert("برای ساخت کاربر می بایست مرکز تعیین شود");
				FormImport.setItemFocus("DefaultCenter");
				return false;
			}
			else if(FormImport.getItemValue("DefaultSupporter")==0){
				alert("برای ساخت کاربر می بایست پشتیبان تعیین شود");
				FormImport.setItemFocus("DefaultSupporter");
				return false;
			}
			else if(FormImport.getItemValue("DefaultStatus")==0){
				alert("برای ساخت کاربر می بایست وضعیت کاربر تعیین شود");
				FormImport.setItemFocus("DefaultStatus");
				return false;
			}
		}
		FormImport.lock();
		dhxLayout.progressOn();
		dhtmlx.message({text:"درحال پردازش اطلاعات...لصفا صبر کنید",expire:4000, type:"myCss"});
		FormImport.send(RenderFile+".php?"+un()+"&act=Parse","post",function(loader){
			PopupImport.hide();
			var UCount=FormImport.getItemValue("UserCount");
			response=loader.xmlDoc.responseText;
			response=CleanError(response);

			ResArray=response.split("~");
			if((response=='')||(response[0]=='~')){
				dhxLayout.progressOff();
				dhtmlx.alert({type:"alert-error",text:"<span style='direction:rtl;float:right;'>خطا، "+response.substring(1)+"</span>",ok:"بستن"});
			}
			else if(ResArray[0]!='OK'){
				dhxLayout.progressOff();
				dhtmlx.alert("خطا، "+response);
			}
			else{

				parent.BatchProcessInstanceCount++;
				OnTabClose_EventId=parent.tabbar.attachEvent("onTabClose", function(id){
					if(id==tabcount)
						if(confirm("این کار باعث رهاکردن عملیات جاری برای "+TotalUserCount+" کاربر می شود\nاز بستن مطمئن هستید؟")){
							parent.tabbar.detachEvent(OnTabClose_EventId);
							parent.BatchProcessInstanceCount--;
							return true;
						}
						else
							return false;
					return true;
				});

				ImportId=ResArray[2];
				TotalUserCount=ResArray[3];
				InititalGridListFull(ResArray[4]);
				FormImport.unload();
				FormCheckForError=DSInitialForm(PopupCheckForError,FormCheckForErrorStr,FormCheckForErrorPopupHelp,FormCheckForErrorFieldHelpId,FormCheckForErrorFieldHelp,FormCheckForErrorOnButtonClick);
				XLE_Id=mygrid.attachEvent("onXLE", function(grid_obj,count){

					if(Action=="ImportUser")
						dhtmlx.message({text:"با موفقیت پردازش شد<br/>فیلدها را برای خطا بررسی کنید",expire:6000, type:"myCss"});
					else
						dhtmlx.message({text:"لیست کاربرها با موفقیت ساخته شد<br/>بررسی فیلدها برای خطا",expire:6000, type:"myCss"});
					if(ResArray[1]!="")
						alert(ResArray[1].substring(1));

					ToolbarOfGrid.enableItem("CheckForError");
					FormCheckForError.lock();
					PopupCheckForError.show("CheckForError");
					setTimeout(function(){
						FormCheckForError.checkItem("PatternCheck");
						setTimeout(function(){
							FormCheckForError.checkItem("InitialStatusCheck");
							setTimeout(function(){
								FormCheckForError.unlock();
								if(PopupCheckForError.isVisible()){
									var MyHint = new dhtmlXPopup({form: FormCheckForError,id:["Proceed"],mode:"bottom"});
									MyHint.attachHTML("<div style='color:darkgreen;font-weight:bold'>برای ادامه کلیک کنید</div>");
									MyHint.show("Proceed");
									PopupCheckForError.attachEvent("onContentClick",function(){MyHint.hide();});
									PopupCheckForError.attachEvent("onHide",function(){MyHint.hide();});
									setTimeout(function(){MyHint.hide()},8000);
								}
							},300);
						},300);
					},300);
					mygrid.detachEvent(XLE_Id);
				});
			}

		});

	}
	else if((name=='CheckFields')||(name=='CheckFiles')){
		if(!MyValidate())
			return;
		FormImport.lock();
		dhxLayout.progressOn();

		FormImport.send(RenderFile+".php?"+un()+"&act=Parse&req=CheckUsers","post",function(loader){
			FormImport.unlock();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			ResArray=response.split("~");
			if((response=='')||(response[0]=='~'))
				alert("خطا، "+response.substring(1));
			else if(ResArray[0]!='OK')
				alert("خطا، "+response);
			else
				alert(ResArray[1]);
			dhxLayout.progressOff();
		});
	}
	else if(name=="Advanced"){
		FormImport.disableItem("Advanced");
		FormImport.showItem("AdvancedBlock");
	}
	else if(name=='GetHint'){
		FormImport.disableItem("GetHint");
		window.location=RenderFile+".php?act=GetHintFile";
		setTimeout(function(){FormImport.enableItem("GetHint")},1000);
	}
	else
		alert("Unhandled Button");
}

//-------------------------------------------------------------------MyValidate()
function MyValidate(){
	FormImport.updateValues();
	if(DSFormValidate(FormImport,FormImportFieldHelpId)==false)
		return false;
	var Action=FormImport.getItemValue("Action");
	if(Action=="ImportUser"){
		var UploadFileStatus=FormImport.getUploaderStatus("FileUploader");
		if(UploadFileStatus==-1){
			alert("خطا در فایل آپلود شده،لطفا مجدد آپلود نمایید");
			return false;
		}
		else if(UploadFileStatus==0){
			alert("حداقل یک فایل را انتخاب کنید");
			return false;
		}
	}
	else{
		FormImport.setItemValue("FixPartOfUserName",FormImport.getItemValue("FixPartOfUserName").toLowerCase());
		FormImport.setItemValue("SequenceFrom",FormImport.getItemValue("SequenceFrom").toLowerCase());
		FormImport.setItemValue("SequenceTo",FormImport.getItemValue("SequenceTo").toLowerCase());
		FormImport.setItemValue("UsernameMask",FormImport.getItemValue("UsernameMask").toLowerCase());
		// FormImport.setItemValue("PasswordMask",FormImport.getItemValue("PasswordMask").toLowerCase());
		FormImport.setItemValue("NotUsedChar",FormImport.getItemValue("NotUsedChar").toLowerCase());
		if(FormImport.getItemValue("OrderType")=="Random"){//Random
			var UCount=FormImport.getItemValue("UserCount");
			if(UCount<0){
				alert("Invalid number of users entered.");
				FormImport.setItemFocus("UserCount");
				return false;
			}
			else if(UCount>9999){
				alert("Maximum 9999 users can generate at once.");
				FormImport.setItemFocus("UserCount");
				return false;
			}
		}
		else{//Sequential
			var SequenceFrom=FormImport.getItemValue("SequenceFrom");
			var SequenceTo=FormImport.getItemValue("SequenceTo");

			if(SequenceFrom.length!=SequenceTo.length){
				alert("The length of SequenceFrom and SequenceTo must be equal");
				FormImport.setItemFocus("SequenceFrom");
				return false;
			}

			for(i=0;i<SequenceFrom.length;++i)
					if(SequenceFrom.substr(i,1)!=SequenceTo.substr(i,1))
						break;
			if(i>0){
				alert("Both SequenceFrom and SequenceTo are started with '"+SequenceFrom.substring(0,i)+"'.\nRemove it from here and add it to the end of FixPartOfUserName");
				FormImport.setItemFocus("SequenceFrom");
				return false;
			}
			else if(SequenceFrom.localeCompare(SequenceTo)!=-1){
				alert("SequenceFrom should be less than SequenceTo.");
				FormImport.setItemFocus("SequenceFrom");
				return false;
			}
		}

	}
	return true;

}

//-------------------------------------------------------------------AddPopupCheckForError()
function AddPopupCheckForError(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"CheckForError","بررسی خطا","tow_CheckForError");
	ToolbarOfGrid.setItemToolTip("CheckForError","Validate users and CheckForError");
	PopupCheckForError=DSInitialPopup(ToolbarOfGrid,PopupCheckForError_Id,PopupCheckForErrorOnShow);
	ToolbarOfGrid.disableItem("CheckForError");
}

//-------------------------------------------------------------------PopupCheckForErrorOnShow()
function PopupCheckForErrorOnShow(){
	ToolbarOfGrid.disableItem("CreateUser");
}

//-------------------------------------------------------------------FormCheckForErrorOnButtonClick(name)
function FormCheckForErrorOnButtonClick(name){
	if(name=='Close') {
		PopupCheckForError.hide();
	}
	else if(name=='Proceed'){

		FormCheckForError.disableItem("Proceed");

		// ISSort=true;
		// GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
		// mygrid.setColSorting(GColSorting);
		// mygrid.setSortImgState(true,ColSortIndex,SortDirection);
		// GColTypes="ro,ro,ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ro,ro";
		// mygrid.setColTypes(GColTypes);



		PatternCheck=(FormCheckForError.getItemValue("PatternCheck")?"Yes":"No");
		InitialStatusCheck=(FormCheckForError.getItemValue("InitialStatusCheck")?"Yes":"No");
		dhxLayout.progressOn();
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=TotalValidate&PatternCheck="+PatternCheck+"&InitialStatusCheck="+InitialStatusCheck+"&id="+ImportId,function (loader){
			PopupCheckForError.hide();
			FormCheckForError.enableItem("Proceed");
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			ResponseArray=response.split("~");
			// dhxLayout.progressOff();
			ToolbarOfGrid.showItem("DeleteSelected");
			if((response=='')||(response[0]=='~')){
				ToolbarOfGrid.showItem("DeleteErrors");
				dhtmlx.alert({type:"alert-error",text:"خطا، "+response.substring(1),ok:"بستن"});
				LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
			}
			else if(ResponseArray[0]=='OK'){
				// ISSort=false;
				// GColSorting="na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na";
				// mygrid.setColSorting(GColSorting);
				// mygrid.setSortImgState(false,0,'asc');
				// GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
				// mygrid.setColTypes(GColTypes);
				ToolbarOfGrid.disableItem("DeleteErrors");
				LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
				TotalUserCount=ResponseArray[1];
				if(TotalUserCount<=0){
					dhtmlx.alert({text:"There is o user to import!!! Exception occured",type:"error"});
					return;
				}

				dhtmlx.message({text:"خطایی یافت نشد.همه ی کاربرها معتبر هستند", expire:8000,type:"myCss"});

				XLE_Id=mygrid.attachEvent("onXLE", function(grid_obj,count){
					FormCreateUser=DSInitialForm(PopupCreateUser,FormCreateUserStr,FormCreateUserPopupHelp,FormCreateUserFieldHelpId,FormCreateUserFieldHelp,FormCreateUserOnButtonClick);

					PauseState=true;
					ToolbarOfGrid.showItem("CreateUser");
					ToolbarOfGrid.enableItem("CreateUser");
					PopupCreateUser.show("CreateUser");
					FormCreateUser.setItemValue("ImportName","Import-"+TNow+"-N="+TotalUserCount);

					var MyHint = new dhtmlXPopup({form: FormCreateUser,id:["Proceed"],mode:"bottom"});
					MyHint.attachHTML("<div style='color:darkgreen;font-weight:bold;text-align:center'>خطا یافت نشد،همه کاربر ها معتبرند<br/>برای ساخت کاربر/ها کلیک کنید</div>");
					MyHint.show("Proceed");
					PopupCreateUser.attachEvent("onContentClick",function(){MyHint.hide();});
					PopupCreateUser.attachEvent("onHide",function(){MyHint.hide();});
					setTimeout(function(){
						MyHint.hide();
					},8000);
					MyTimer=0;
					ImportPassedCount=0;
					ImportFailCount=0;
					ImportOKCount=0;
					mygrid.detachEvent(XLE_Id);
				});
			}
			else if((ResponseArray[0]=='PatternCheck')||(ResponseArray[0]=='InitialStatusCheck')){
				ToolbarOfGrid.showItem("DeleteErrors");
				LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
				dhtmlx.alert({text:ResponseArray[1],type:"alert-error",ok:"بستن"});
			}
			else if((ResponseArray[0]=='NotFound')){
				ToolbarOfGrid.showItem("DeleteErrors");
				LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
				dhtmlx.alert({type:"alert-error",text:"خطا، "+ResponseArray[2],callback:function(){
					setTimeout(function(){
						NotFounds=ResponseArray[1].split("`");
						FormCheckForError.hideItem("CreateReseller");
						FormCheckForError.hideItem("CreateVisp");
						FormCheckForError.hideItem("CreateCenter");
						FormCheckForError.hideItem("CreateSupporter");
						FormCheckForError.hideItem("CreateStatus");
						PopupCheckForError.show("CheckForError");
						var mapObj = {
						Reseller:"نماینده فروش",
						Visp:"ارائه دهنده مجازی",
						Center:"مرکز",
						Supporter:"پشتیبان",
						Status:"وضعیت"
						};
						for(i=0;i<NotFounds.length;++i){
							tmp=NotFounds[i].split("=");
							FormCheckForError.setItemValue("InfoCreate"+tmp[0],tmp[1]);
							FormCheckForError.setItemLabel("Create"+tmp[0]," ایجاد "+tmp[1]+" "+tmp[0].replace(/Reseller|Visp|Center|Supporter|Status/gi, function(matched){return mapObj[matched];})+" مفقود شده");
							FormCheckForError.showItem("Create"+tmp[0]);
						}
					},200);
				},ok:"بستن"});
			}
			else
				alert(response);
		});
	}
	else if((name=="CreateReseller")||(name=="CreateVisp")||(name=="CreateCenter")||(name=="CreateSupporter")||(name=="CreateStatus")){
			var mapObj = {
			CreateReseller:"ایجاد نماینده فروش",
			CreateVisp:"ایجاد ارائه دهنده مجازی",
			CreateCenter:"ایجاد مرکز",
			CreateSupporter:"ایجاد پشتیبان",
			CreateStatus:"ایجاد وضعیت"
			};
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm",
			ok: "بلی",
			cancel: "خیر",
			text: " آیا از "+name.replace(/CreateReseller|CreateVisp|CreateCenter|CreateSupporter|CreateStatus/gi, function(matched){return mapObj[matched];})+" مطمئن هستید؟"+"("+FormCheckForError.getItemValue("Info"+name)+" مورد)",
			callback: function(Result){
				if(Result){
					FormCheckForError.hideItem(name);
					dhxLayout.progressOn();
					dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Create&Item="+name+"&id="+ImportId,function(loader){
						dhxLayout.progressOff();
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						ResponseArray2=response.split("~");
						if((response=='')||(response[0]=='~'))
							dhtmlx.alert({type:"alert-error",text:"خطا، "+response.substring(1)});
						else if(ResponseArray2[0]!='OK')
							dhtmlx.alert({type:"alert-error",text:response});
						else{
							dhtmlx.alert({text:ResponseArray2[1]+"</span>",callback:function(){FormCheckForErrorOnButtonClick("Proceed")},ok:"بستن"});
						}
					});
				}
			}
		});
	}
	else
		alert("Unhandled Button");
}

//-------------------------------------------------------------------AddPopupCreateUser()
function AddPopupCreateUser(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"CreateUser","ایجاد کاربر/ها","tow_CreateUser");
	ToolbarOfGrid.setItemToolTip("CreateUser","CreateUsers");
	// PopupCreateUser=DSInitialPopup(ToolbarOfGrid,PopupCreateUser_Id,PopupCreateUserOnShow);

	PopupCreateUser=new dhtmlXPopup({toolbar: ToolbarOfGrid,id: PopupCreateUser_Id,mode:"right"});
	PopupCreateUser.setSkin(popup_main_skin);
	// PopupCreateUser.attachEvent("onShow",PopupCreateUserOnShow);

	ToolbarOfGrid.hideItem("CreateUser");
}

//-------------------------------------------------------------------PopupCreateUserOnShow()
// function PopupCreateUserOnShow(){
// }

//-------------------------------------------------------------------FormCreateUserOnButtonClick(name)
function FormCreateUserOnButtonClick(name){
	if(name=='Close') {
		PopupCreateUser.hide();
	}
	else if(name=='Proceed'){
		PopupCreateUser.hide();
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-warning",
			ok: "بلی",
			cancel: "خیر",
			text: "با "+(TotalUserCount-ImportPassedCount)+" کاربر ادامه می دهید؟",
			callback: function(Result){
				if(Result){
					ToolbarOfGrid.showItem("Retrieve");
					ToolbarOfGrid.hideItem("DeleteErrors");
					ToolbarOfGrid.hideItem("DeleteSelected");
					ToolbarOfGrid.enableItem("DeleteErrors");
					ToolbarOfGrid.showItem("sep1");
					parent.tabbar.detachEvent(OnTabClose_EventId);
					OnTabClose_EventId=parent.tabbar.attachEvent("onTabClose", function(id){
						if(id==tabcount){
							parent.dhtmlx.message({text:"وارد کردن و یا تولید کاربران درحال اجرا است", type:"error",expire:5000});
							return false;
						}
						else
							return true;
					});


					//dhxLayout.cells("a").progressOn();
					ImportName=FormCreateUser.getItemValue("ImportName");
					if(ImportName==""){
						ImportName="Import-"+TNow+"-N="+TotalUserCount;
						FormCreateUser.setItemValue("ImportName",ImportName);
					}
					FormCreateUser.disableItem("ImportName");
					if(ImportPassedCount<=0)
						dhtmlxAjax.get(
							RenderFile+".php?"+un()+"&act=PrepareImport&id="+ImportId+"&ImportName="+ImportName,
							function(loader){
								response=loader.xmlDoc.responseText;
								response=CleanError(response);
								ResArray=response.split("~");
								if((response=='')||(response[0]=='~'))
									dhtmlx.alert({type:"alert-error",text:"خطا، "+response.substring(1)});
								else if((ResArray[0]=="OK")||(ResArray[0]=="NewName")){
									if(ResArray[0]=="NewName"){
										alert("ImportName was duplicate and changed to:\n'"+ResArray[1]+"'");
										FormCreateUser.setItemValue("ImportName",ResArray[1]);
									}

									ToolbarOfGrid.disableItem("Import");
									ToolbarOfGrid.disableItem("CheckForError");
									FormCreateUser.setItemLabel("Proceed"," ادامه دادن ");
									ToolbarOfGrid.showItem("TimerStringLabel");
									ToolbarOfGrid.showItem("TimerString");
									FormCreateUser.enableItem("CancelImport");
									FormCreateUser.showItem("ImportSummary");
									ContinueImport();
								}
								else
									alert(response);
							}
						);
					else
						ContinueImport();
				}
			}
		});
	}
	else if(name=='CancelImport'){
		PopupCreateUser.hide();
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-error",
			ok: "بلی",
			cancel: "خیر",
			text: "از لغو ("+(TotalUserCount-ImportPassedCount)+") مورد باقی مانده مطمئن هستید؟",
			callback: function(Result){
				if(Result)
					Finalize("canceled");
			}
		});

	}
}

function ContinueImport(){
	ToolbarOfGrid.disableItem("CreateUser");
	PauseState=false;
	PopupProgress.show("TimerString");
	FailCount=0;
	PopupProgress.attachHTML(
	'<div style="border:1px solid #95b9db;width:300px;height:220px;text-align:center;margin:4px;padding:12px">'+
		'<div style="font-weight:bold;font-size:150%;color:darkgreen">عملیات واردکردن در حال انجام است</div>'+
		'<div class="ProgressFrameCss">'+
			'<div class="ProgressBodyCss" id="ProgressBar">'+
				'<div style="" class="ProgressTextCss" id="ProgressText"></div>'+
			'</div>'+
			'<br/>'+
			'<table border="1" align="center" width="80%" style="font-size:100%;font-weight:bold;background:lightyellow" cellspacing="2" cellpadding="2">'+
				'<tr>'+
					'<td width="65%" align="left">کل موارد :</td>'+
					'<td width="35%" align="center">'+TotalUserCount+'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="left">موارد انجام شده :</td>'+
					'<td align="center" id="PassedCount" style="color:blue">'+ImportPassedCount+'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="left">موارد وارد شده :</td>'+
					'<td align="center" id="OKCount" style="color:green">'+ImportOKCount+'</td>'+
				'</tr>'+
				'<tr>'+
					'<td align="left">موارد ناموفق :</td>'+
					'<td align="center" id="FailCount" style="color:red">'+ImportFailCount+'</td>'+
				'</tr>'+
			'</table>'+
			'<br/><br/>'+
			'<span style="font-weight:bold;color:firebrick;font-size:130%;">برای وقفه در عملیات،اینجا کلیک کنید</span>'+
		'</div>'+
	'</div>');
	document.getElementById("ProgressBar").style.backgroundColor="#008000"
	SetProgressTimer();
	DoCreate();
}

function DoCreate(){
	if((ImportPassedCount<TotalUserCount)&&(PauseState==false)){
		ProgressPercent=Math.round(10000*ImportPassedCount/TotalUserCount)/100;
		document.getElementById("ProgressBar").style.width =ProgressPercent+"%";
		document.getElementById("ProgressText").innerHTML =ProgressPercent+"%";

		if(!PopupProgress.isVisible())
			PopupProgress.show("TimerString");

		// var CurrentItem=ImportPassedCount+1;
		// FormCreateUser.setItemLabel("ImportSummary","<span style='color:indianred'>"+CurrentItem+" of "+TotalUserCount+" is in progress. Please wait...</span>");
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=InsertUser&id="+ImportId+"&RowIndex="+ImportPassedCount,function (loader){
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			if((response=='')||(response[0]=='~')||(response!='OK~')){
				if(FormCreateUser.isItemChecked("IgnoreError")){
					ImportPassedCount++;
					ImportFailCount++;
					document.getElementById("PassedCount").innerHTML =ImportPassedCount;
					document.getElementById("FailCount").innerHTML =ImportFailCount;
					setTimeout(function(){DoCreate();},200);
				}
				else{
					clearInterval(TimerStringInterval_Id);
					PopupProgress.hide();
					if(++FailCount<3)
						dhtmlx.confirm({
							title: "Error",
							type:"confirm-error",
							ok: "Ignore",
							cancel: "Retry",
							text: "خطا، "+response.substring(1)+"<br/>Press Retry to try again on this user<br/>or press Ignore to skip this user and continue...",
							callback: function(Result){
								if(Result){
									ImportPassedCount++;
									ImportFailCount++;
									document.getElementById("PassedCount").innerHTML =ImportPassedCount;
									document.getElementById("FailCount").innerHTML =ImportFailCount;
								}
								SetProgressTimer();
								PopupProgress.show("TimerString");
								setTimeout(function(){DoCreate();},500);
							}
						});
					else
						dhtmlx.alert({
							title: "Error",
							type:"alert-error",
							ok: "OK",
							text: "Multiple error, "+response.substring(1)+"<br/>Choose what to do.",
							callback: function(Result){
								setTimeout(function(){

									PauseState=true;
									FormCreateUser.setItemLabel("ImportSummary","<div>موارد انجام شده : <span style='color:blue'>"+ImportPassedCount+" از "+TotalUserCount+"</span><br/>موارد وارد شده : <span style='color:green'>"+ImportOKCount+"</span><br/>موارد ناموفق : <span style='color:red'>"+ImportFailCount+"</span></div>");
									ToolbarOfGrid.enableItem("CreateUser");
									PopupCreateUser.show("CreateUser");
									FormCreateUser.showItem("IgnoreErrorBlock");
									FormCreateUser.setItemFocus("IgnoreError");
									FormCreateUser.unlock();
								},200);
							}
						});
				}
			}
			else{
				ImportOKCount++;
				FailCount=0;
				ImportPassedCount++;
				document.getElementById("PassedCount").innerHTML =ImportPassedCount;
				document.getElementById("OKCount").innerHTML =ImportOKCount;
				setTimeout(function(){DoCreate();},100);
			}
		});
	}
	else if(ImportPassedCount>=TotalUserCount){
		document.getElementById("ProgressText").innerHTML ="Finalizing";
		PopupCreateUser.hide();
		dhtmlx.message({text:"<span style='color:navy;font-weight:bold'>عملیات وارد کردن کاربران پایان یافت.در تاریخچه عملیات گروهی برای ارجاع آینده ذخیره شد</span>",expire:8000});
		Finalize("finished");
		clearInterval(TimerStringInterval_Id);
	}
	else{//PauseState==true
		FormCreateUser.lock();
		setTimeout(function(){
			if(PopupProgress.isVisible())
				PopupProgress.hide();
			FormCreateUser.setItemLabel("ImportSummary","<div>موارد انجام شده : <span style='color:blue'>"+ImportPassedCount+" از "+TotalUserCount+"</span><br/>موارد وارد شده : <span style='color:green'>"+ImportOKCount+"</span><br/>موارد ناموفق : <span style='color:red'>"+ImportFailCount+"</span></div>");
			ToolbarOfGrid.enableItem("CreateUser");
			PopupCreateUser.show("CreateUser");
			clearInterval(TimerStringInterval_Id);
			dhtmlx.message({text:"<span style='font-weight:bold'>Process paused at item number : "+ImportPassedCount+".</span>",expire:3000});
			FormCreateUser.unlock();
		},2000);
	}
}

function Finalize(State){
	ToolbarOfGrid.disableItem("CreateUser");
	dhxLayout.cells("a").progressOn();
	var loader2=dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=FinalizeImport&State="+State+"&id="+ImportId);
	if(PopupProgress.isVisible())
		PopupProgress.hide();
	response2=loader2.xmlDoc.responseText;
	response2=CleanError(response2);
	ResArray=response2.split("~");
	if((response2=='')||(response2[0]=='~')){
		dhtmlx.alert({type:"alert-error",text:"خطا، "+response2.substring(1)});
		ToolbarOfGrid.enableItem("CreateUser");
		FormCreateUser.disableItem("Proceed");
	}
	else if((ResArray[0]=="OK")||(ResArray[0]=="NewName")){
		if(ResArray[0]=="NewName")
			alert("ImportName was duplicate and changed to:\n'"+ResArray[1]+"'");
			var mapObj = {
   finished:"به اتمام رسید",
   canceled:"لغو شد",
};
		dhtmlx.alert({text:"عملیات واردکردن کاربران "+State.replace(/finished|canceled/gi,function(matched){
  return mapObj[matched];
})+"<br/>تعداد "+ImportOKCount+" کاربر،وارد شدند"+(ImportFailCount>0?(" and fail to import "+ImportFailCount+" user(s)"):"")+"<br/>مجموع زمان سپری شده:"+GetTimerString(MyTimer),ok:"بستن"});
		ToolbarOfGrid.hideItem("TimerStringLabel");
		ToolbarOfGrid.hideItem("TimerString");
		ToolbarOfGrid.enableItem("Import");
		ToolbarOfGrid.showItem("SaveToFile");
		ToolbarOfGrid.enableItem("SaveToFile");
		TotalUserCount=0;
		parent.tabbar.detachEvent(OnTabClose_EventId);
		parent.BatchProcessInstanceCount--;
	}
	else{
		alert(response2);
		ToolbarOfGrid.enableItem("CreateUser");
		FormCreateUser.disableItem("Proceed");
	}
	dhxLayout.cells("a").progressOff();

	ToolbarOfGrid.hideItem("Retrieve");
	ToolbarOfGrid.hideItem("sep1");

	ISSort=false;
	GColSorting="na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na";
	mygrid.setColSorting(GColSorting);
	mygrid.setSortImgState(false,0,'asc');
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	mygrid.setColTypes(GColTypes);
	mygrid.setColumnHidden(2,true);//parsecomment
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
}
//-------------------------------------------------------------------InititalGridListFull(Arr)
function InititalGridListFull(Arr){

	var FieldsArray=[
		["user_import_id","parseresult","parsecomment","username","pass","name","family","resellername","vispname","centername","supportername","fathername","nationalcode","nationality","birthdate","adslphone","phone","mobile","comment","email","paybalance","statusname","noe","address","organization","companyregistrycode","companyeconomycode","companynationalcode","expirationdate","filerownumber","filename"],
		["User_Import_Id","ParseResult","ParseComment","Username","Pass","Name","Family","ResellerName","VispName","CenterName","SupporterName","FatherName","NationalCode","Nationality","BirthDate","AdslPhone","Phone","Mobile","Comment","Email","PayBalance","StatusName","NOE","Address","Organization","CompanyRegistryCode","CompanyEconomyCode","CompanyNationalCode","ExpirationDate","FileRowNumber","FileName"],
		[0,0,0,32,64,32,32,32,32,64,64,32,10,32,10,15,32,11,255,128,10,64,32,255,64,12,12,12,10,0,0]
	];

	ExtraFilter="&id="+ImportId;
	GColIds="user_import_id,parseresult,parsecomment,username,pass,name,family,resellername,vispname,centername,supportername,fathername,nationalcode,nationality,birthdate,adslphone,phone,mobile,comment,email,paybalance,statusname,noe,address,organization,companyregistrycode,companyeconomycode,companynationalcode,filerownumber,filename";
	GColHeaders="{#stat_count} ردیف,نتیجه تجزیه,توضیح تحزیه,نام کاربری"+ReqStar+",کلمه عبور,نام,نام خانوادگی,نام نماینده فروش"+ReqStar+",نام ارائه دهنده مجازی اینترنت"+ReqStar+",نام مرکز"+ReqStar+",نام پشتیبان"+ReqStar+",نام پدر,کد ملی,ملیت,تاریخ تولد,تلفنADSL,تلفن,موبایل,توضیح,ایمیل,تراز مالی,نام وضعیت"+ReqStar+",موقعیت مکانی وایرلس,آدرس,سازمان,شماره ثبت شرکت,شماره اقتصادی شرکت,شناسه ملی شرکت,تاریخ انقضا کاربری,شماره ردیف فایل,نام فایل";


	GColInitWidths="100,100,200,120,120,120,120,120,180,120,120,120,140,140,120,120,120,120,200,150,120,150,120,200,140,140,140,140,120,100,140";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ro,ro";
	HeaderAlignment=["text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center","text-align:center"];
	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=2;
	SortDirection='desc';
	mygrid =dhxLayout.cells("a").attachGrid();
	mygrid.setSkin(grid_main_skin);
	mygrid.setImagePath(grid_image_path);
	mygrid.setColumnIds(GColIds);
	mygrid.setHeader(GColHeaders,null,HeaderAlignment);
	mygrid.setInitWidths(GColInitWidths);
	mygrid.setColAlign(GColAligns);
	// mygrid.attachHeader(",#connector_select_filter,#connector_text_filter,#numeric_filter");

	ExistedFields=Arr.split("`");
	for (i=0;i<FieldsArray[0].length;i++)
		if(ExistedFields.indexOf(FieldsArray[0][i])<0)
			mygrid.setColumnHidden(i,true);
	mygrid.init();

	mygrid.setColSorting(GColSorting);
	mygrid.setSortImgState(true,ColSortIndex,SortDirection);
	mygrid.setColTypes(GColTypes);


	mygrid.attachEvent("onBeforeSorting",function(ind,type,direction){
		mygrid.setSortImgState(true,ind,direction);
		ColSortIndex=ind;
		SortDirection=((direction=='asc')?'asc':'desc');
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
	});
	var mydp = new dataProcessor(RenderFile+".php?"+un()+"&act=list"+ExtraFilter);
	mydp.init(mygrid);

	//mydp.defineAction("Mohsen",function(response){alert(response.getAttribute("message"));return true;});
	for (i=3;i<FieldsArray[2].length;i++){
		if(FieldsArray[2][i]>0)
			mydp.setVerificator(i,function(New_Value,RowId,ColId){

				mygrid.cells(RowId,1).setValue("NotChecked");
				mygrid.cells(RowId,2).setValue("");
				mygrid.setRowTextStyle(RowId,"color:black");

				if(New_Value.length>FieldsArray[2][ColId]){
					dhtmlx.alert(FieldsArray[1][ColId]+" length should be less than or equal "+FieldsArray[2][ColId]+" characters.<br/>But you entered "+New_Value.length+" characters");
					return false;
				}
				return true;
			});
	}
	mygrid.enableSmartRendering(true,100);
	// mygrid.enablePreRendering(50);
	// mygrid.enableDistributedParsing(true);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,ExtraFilter,DoAfterRefresh);
}



function ToolbarOfGrid_OnSaveToFileClick(){

	if(!ISValidResellerSession()) return;
	ToolbarOfGrid.disableItem('SaveToFile');
	TotalUserCount=0;
	setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile');},3000);
	window.location=RenderFile+".php?act=SaveToFile&id="+ImportId;
}

function SetProgressTimer(){
	clearInterval(TimerStringInterval_Id);
	ToolbarOfGrid.setItemText("TimerString","<span style='color:darkblue;font-weight:bold'>"+GetTimerString(MyTimer)+"</span>");
	TimerStringInterval_Id = setInterval(function(){
		MyTimer+=1;
		ToolbarOfGrid.setItemText("TimerString","<span style='color:darkblue;font-weight:bold'>"+GetTimerString(MyTimer)+"</span>");
	  },100);
}

//-------------------------------------------------------------------GetTimerString()
function GetTimerString(FTime){
	var decseconds = Math.floor(FTime % 10 );
	var seconds = Math.floor((FTime /10)% 60 );
	var minutes = Math.floor((FTime/600) % 60 );
	var hours = Math.floor((FTime/(36000)) % 24 );
	var days = Math.floor(FTime/864000 );

	return (days>0?(days+"d "):"")+(hours<10?"0"+hours:hours)+":"+(minutes<10?"0"+minutes:minutes)+":"+(seconds<10?"0"+seconds:seconds)+":"+decseconds;
}
}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------DoAfterRefresh()
function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);
	// dhxLayout.progressOff();
	mygrid.callEvent("onGridReconstructed",[]);
}
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
