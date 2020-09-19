<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSServerEdit ....................................................................................");
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
	var DataTitle="سرویس های دلتاسیب";
	var DataName="DSServer_8_";
	var ChangeLogDataName='Server';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["صفحه اتمام","DSServer_8_Edit","95","Admin.Server.DeltasibServices.wwwfinish.status",""],
					["سرعت میکروتیک","DSServer_8_EditServices","110","Admin.Server.DeltasibServices.mikrotikrate.status","ServiceName=mikrotikrate"],
					["گزارش صفحات بازدید شده","DSServer_8_EditServices","150","Admin.Server.DeltasibServices.urlreporting.status","ServiceName=urlreporting"],
					["قطع کاربر","DSServer_8_EditServices","90","Admin.Server.DeltasibServices.disconnect.status","ServiceName=disconnect"],
					["اعلان","DSServer_8_EditServices","70","Admin.Server.DeltasibServices.notify.status","ServiceName=notify"],
					["نت لاگ","DSServer_8_EditServices","70","Admin.Server.DeltasibServices.netlog.status","ServiceName=netlog"],
					// ["ds_graph","DSServer_8_EditServices","70","Admin.Server.DeltasibServices.ds_graph.status","ServiceName=ds_graph"],
					["لیست تغییرات","DSChangeLog","90","Admin.Server.ChangeLog.List","ParentId="+RowId+"&ChangeLogDataName=Server"],
					];
	//=======Form1 Server Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
		FinishPageMode:"Two options is available.<br/>In multiple IP mode service wwwfinish will run on one port and requests will redirect depend on source IP address(recommended).<br/>If somethong like NAT cause source IPs change before it can be processed by wwwfinish, you can run wwwfinish on multiple port and<br/>then requests will redirect depend on requested port",
		DefaultListenningPort:"In multiple IP mode this will specify default listening port of wwwfinish(By default 80 is used for httpd. Default value is 82)",
		DefaultURL:"This is default URL. If none of source IP specified bellow satisfy source IP of user, this will used for",
		
		IPRangeDay:"آی پی مبدا برای کاربرانی که روز آن ها به اتمام رسیده.از رنج آی پی هم می توان استفاده نمود<br>مثال : 192.168.30.0/24,172.16.6.32/29",
		IPRangeTraffic:"Source IP for finished traffic users. You can use comma as delimiter for more than one IP range. For example 192.168.30.0/24,172.16.6.32/29",
		IPRangeDebit:"Source IP for finished debit users. You can use comma as delimiter for more than one IP range. For example 192.168.30.0/24,172.16.6.32/29",
		IPRangeTime:"Source IP for finished time users. You can use comma as delimiter for more than one IP range. For example 192.168.30.0/24,172.16.6.32/29",
		
		PortDay:'عدد پورت را وارد کنید،مانند 82',
		PortTraffic:'Port number example 83',
		PortDebit:'Port number example 84',
		PortTime:'Port number example 85',
		
		ReturnURLDay1:"لینک بازگشت پس از نمایش صفحه اتمام روز که کاربر بتواند وارد پنل کاربری شود و شارژ نماید",
		ReturnURLTraffic1:"This is used for default finish traffic page as return URL when user press the button supplied in finish page",
		ReturnURLDebit1:"This is used for default debit page page as return URL when user press the button supplied in finish page",
		ReturnURLTime1:"This is used for default finish time page as return URL when user press the button supplied in finish page",
		
		ReturnURLDay2:"لینک بازگشت پس از نمایش صفحه اتمام روز که کاربر بتواند وارد پنل کاربری شود و شارژ نماید",
		ReturnURLTraffic2:"This is used for default finish traffic page as return URL when user press the button supplied in finish page",
		ReturnURLDebit2:"This is used for default debit page page as return URL when user press the button supplied in finish page",
		ReturnURLTime2:"This is used for default finish time page as return URL when user press the button supplied in finish page"
	};
	var Form1FieldHelpId=["FinishPageMode","DefaultListenningPort","DefaultURL","IPRangeDay","PortDay","IPRangeTraffic","PortTraffic","IPRangeDebit","PortDebit","IPRangeTime","PortTime","ReturnURLDay1","ReturnURLTraffic1","ReturnURLDebit1","ReturnURLTime1","ReturnURLDay2","ReturnURLTraffic2","ReturnURLDebit2","ReturnURLTime2"];
	var Form1TitleField="PartName";
	var Form1DisableItems=["PartName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1Str = [
		{type:"settings" , labelWidth:190, offsetLeft:10, labelAlign:"left"},
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{type:"hidden" , name:"Server_Id", label:"شناسه سرور:",disabled:"true", inputWidth:130},
		{type:"hidden" , name:"PartName", label:"نام بخش:",disabled:"true", inputWidth:150},
		{type:"fieldset", width:680, label:"wwwfinish سرویس", list:[
			{ type: "label"},
			{type:"block",list:[
				{type: "label", name:"ServiceStatus", label:"", labelWidth:560}
			]},
			{type:"block",list:[
				{type: "button", name:"Status", value: " وضعیت ", width :80, disabled:true},
				{type: "newcolumn", offset:30},
				{type: "button", name:"Stop", value: " توقف ", width :80, disabled:true},
				{type: "newcolumn", offset:30},
				{type: "button", name:"Start", value: " شروع ", width :80, disabled:true},
				{type: "newcolumn", offset:40},
				{type: "button", name:"Restart", value: " شروع مجدد ", width :80, disabled:true}
			]},
			{ type: "label"},
			{type:"block",list:[
				{type: "checkbox", label: "شروع خودکار", name: "AutoStart", position: "label-right", labelWidth:100, checked: <?php echo (count(glob("/etc/rc2.d/S*wwwfinish"))>0)?"true":"false" ?>},
				{type: "newcolumn"},
				{type: "button", name:"SetAutoStart", value: "تنظیم", width :40,disabled:true},
			]},
		]},		
		{type:"fieldset", width:680, label:"پیکربندی", list:[
			{ type: "select", name:"FinishPageMode", label: "حالت :",inputWidth:150,required:true, info:"false", labelWidth:100, options:[
				{text: "چند پورت", value: "MultiplePort",selected: true,list:[
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkDay2",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"PortDay", label:"پورت صفحه اتمام روز", inputWidth:100,maxLength:5, validate:"NotEmpty,ValidInteger", info:true,required:true,disabled:true},
							{type:"input", name:"ReturnURLDay2",label:"مسیر برگشت برای روز:",inputWidth:330,maxLength:60,info:true,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]}
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkTraffic2",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"PortTraffic", label:"پورت صفحه اتمام ترافیک:", inputWidth:100,maxLength:5, validate:"NotEmpty,ValidInteger", info:false,required:true,disabled:true},
							{type:"input", name:"ReturnURLTraffic2",label:"مسیر برگشت برای ترافیک:",inputWidth:330,maxLength:60,info:false,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]},
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkDebit2",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"PortDebit", label:"پورت صفحه بدهی:", inputWidth:100,maxLength:5, validate:"NotEmpty,ValidInteger", info:false,required:true,disabled:true},
							{type:"input", name:"ReturnURLDebit2",label:"مسیر برگشت برای بدهی:",inputWidth:330,maxLength:60,info:false,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]},
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkTime2",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"PortTime", label:"پورت صفحه اتمام زمان:", inputWidth:100,maxLength:5, validate:"NotEmpty,ValidInteger", info:false,required:true,disabled:true},
							{type:"input", name:"ReturnURLTime2",label:"مسیر برگشت برای اتمام زمان:",inputWidth:330,maxLength:60,info:false,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]},
					]}
				]},
				{text: "چند آی پی", value: "MultipleIP",list:[
					{ type: "label"},
					{type:"block",name:"DefaultBlock",list:[
						{type:"newcolumn",offset:10},
						{type:"input", name:"DefaultListenningPort",inputWidth:100,label:"پورت پیش فرض :",maxLength:5,info:true,required:true,disabled:true},
						{type:"input", name:"DefaultURL",label:"مسیر پیش فرض :",inputWidth:302,maxLength:60,info:true,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkDay1",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"IPRangeDay", label:"آی پی  صفحه اتمام روز :", inputWidth:330,maxLength:128, validate:"NotEmpty,ISPermitIp", info:true,required:true},
							{type:"input", name:"ReturnURLDay1",label:"مسیر برگشت برای روز:",inputWidth:330,maxLength:60,info:true,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]}
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkTraffic1",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"IPRangeTraffic", label:"آی پی صفحه اتمام ترافیک :", inputWidth:330,maxLength:128, validate:"NotEmpty,ISPermitIp", info:false,required:true},
							{type:"input", name:"ReturnURLTraffic1",label:"مسیر برگشت برای ترافیک:",inputWidth:330,maxLength:60,info:false,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]},
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkDebit1",checked:false, position: "absolute", inputLeft :-10, list:[
							{ type:"input" , name:"IPRangeDebit", label:"آی پی صفحه بدهی :", inputWidth:330,maxLength:128, validate:"NotEmpty,ISPermitIp", info:false,required:true},
							{type:"input", name:"ReturnURLDebit1",label:"مسیر برگشت برای بدهی:",inputWidth:330,maxLength:60,info:false,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]},
					]},
					{ type: "label"},
					{type:"block",list:[
						{type: "checkbox",name:"ChkTime1",checked:false, position: "absolute", inputLeft :-10, list:[
							{type:"input" , name:"IPRangeTime", label:"آی پی صفحه اتمام زمان :", inputWidth:330,maxLength:128, validate:"NotEmpty,ISPermitIp", info:false,required:true},
							{type:"input", name:"ReturnURLTime1",label:"مسیر برگشت برای اتمام زمان:",inputWidth:330,maxLength:60,info:false,note: { text: "http://users.myisp.com:88/ : خالی بگذارید یا مانند نمونه وارد کنید"}}
						]},
					]}
				]}
			]},
		]},
		{ type: "label"},{ type: "label"},
	];

	var PermitView=ISPermit("Admin.Server.DeltasibServices.wwwfinish.View");
	var PermitApply=ISPermit("Admin.Server.DeltasibServices.wwwfinish.Apply");
	
		
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
	Form1.attachEvent("onChange",Form1OnChange);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
	if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(PermitApply)	DSToolbarAddButton(Toolbar1,null,"Apply","اعمال پیکربندی","tow_Apply",Toolbar1_OnApplyClick);
	if(!PermitApply)	FormDisableAllItem(Form1);
	if(ISPermit("Admin.Server.DeltasibServices.wwwfinish.Status")){
		Form1.enableItem("Status");
		if(ISPermit("Admin.Server.DeltasibServices.wwwfinish.Stop")) Form1.enableItem("Stop");
		if(ISPermit("Admin.Server.DeltasibServices.wwwfinish.Start")) Form1.enableItem("Start");
		if(ISPermit("Admin.Server.DeltasibServices.wwwfinish.Restart")) Form1.enableItem("Restart");
		Form1OnButtonClick("Status");
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnChange(name,value,chkstate){
	if(name=="AutoStart")
		Form1.enableItem("SetAutoStart");
}
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnApplyClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		// DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		dhxLayout.cells("a").progressOn();
		Form1.send(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update","post",function(loader, response){
			dhxLayout.cells("a").progressOff();
			response=CleanError(response);
			
			var ErrorStr="";
			var responsearray=response.split("~",3);
			if (responsearray.length==0) ErrorStr=response;//
			if(response=="")
				ErrorStr="خطا، درخواست آپدیت نتیجه ای نداشت";
			else if(responsearray[0]=='OK'){
				dhtmlx.message("پیکربندی با موفقیت اعمال شد");
				if(responsearray[1]=='Yes')
					alert("لطفا سرویس wwwfinish را برای اعمال تغییرات پورت شروع مجدد نمایید");
				if(responsearray[2]!='') 
					dhtmlx.alert({text:" تنظیم شد `"+responsearray[2]+"`مسیر",ok:"بستن"});
				DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
			}else{	
				if(response[0]=='~') ErrorStr=response.substring(1);//"Error,"+response.substring(1)
				else ErrorStr=response;
			}
			if(ErrorStr!="") alert(ErrorStr);//MUST use alert function
		});
	}
}

function Form1OnButtonClick(name){
	if(name=="SetAutoStart")
		DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act="+name,function(){Form1.disableItem("SetAutoStart");},null);
	else{
		Form1.setItemLabel("ServiceStatus","Processing...");
		Form1.lock();
		dhxLayout.cells("a").progressOn();
		dhtmlxAjax.get(TabbarMainArray[0][1]+"Render.php?"+un()+"&act="+name, function(l){
			Form1.unlock();
			dhxLayout.cells("a").progressOff();
			response=l.xmlDoc.responseText;
			response=CleanError(response);
			ResArray=response.split("~");
			if((response=='')||(response[0]=='~')){
				dhtmlx.alert("خطا، "+response.substring(1));
				Form1.setItemLabel("ServiceStatus","خطا، "+response.substring(1));
			}
			else if(ResArray[0]!='OK'){
				dhtmlx.alert("خطا، "+response);
				Form1.setItemLabel("ServiceStatus","خطا، "+response);
			}
			else
				Form1.setItemLabel("ServiceStatus",ResArray[1]);
		});
	}
}

function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}	
function Form1DoAfterLoadOk(){
	parent.dhxLayout.dhxWins.window("popupWindow").setText(DataTitle);
}	

function Form1DoAfterLoadFail(){
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
}
function Form1DoAfterUpdateFail(){
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