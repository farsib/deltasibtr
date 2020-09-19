<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSNasInfoEdit ....................................................................................");
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
	var DataTitle="پارامترهای ردیوس";
	var DataName="DSNasInfo_";
	var ChangeLogDataName='NasInfo';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSNasInfo_Edit","70","Admin.NasInfo.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.NasInfo.ChangeLog.List","ChangeLogDataName=NasInfo"]
					];
	//=======Form1 NasInfo Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={MikrotikBWPriority:'Set to single for mikrotik os less than 5'};
	var Form1FieldHelpId=["DCCommand",'MikrotikBWPriority'];
	var Form1TitleField="NasInfoName";
	var Form1DisableItems=[""];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="UsernamePattern";

	var Form1Str = [
		{ type:"settings" , labelWidth:140, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"NASInfo_Id", label:"شناسه اطلاعات ردیوس :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"NasInfoName", label:"نام پارامتر ردیوس :",maxLength:40, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:100},
		{ type:"input" , name:"TelnetPort", label:"Telnet پورت :",maxLength:5,value:"23", validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:100},
		{ type:"input" , name:"SSHPort", label:"SSH پورت :",maxLength:5,value:"22", validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:100},
		{ type: "select", name:"DeleteUserStaleMethod", label: "حذف کاربر بدون فعالیت :",inputWidth:80,required:true, options:[
			{text: "هرگز", value: "Never"},
			{text: "یک مرحله", value: "OneStep", list:[
				{ type:"input" , name:"MaxInterimTime", label:"حداکثر زمان دریافت اطلاعات از روتر :",value:"120",maxLength:9, validate:"NotEmpty,ValidInteger", labelAlign:"left",inputWidth:176,labelWidth:164},
				{ type:"input" , name:"InterimRate", label:"ضریب زمانی دریافت اطلاعات :",value:"1",maxLength:4, validate:"NotEmpty", labelAlign:"left",inputWidth:176,labelWidth:164},
				{ type:"input" , name:"StepOneWaitingTime", label:"زمان انتظار مرحله ۱ :",value:"60",maxLength:4, validate:"NotEmpty,ValidInteger", labelAlign:"left",inputWidth:200},
			]},
			{text: "دو مرحله", value: "TwoStep",selected: true, list:[
				{ type:"input" , name:"MaxInterimTime2", label:"حداکثر زمان دریافت اطلاعات از روتر :",value:"120",maxLength:9, validate:"NotEmpty,ValidInteger", labelAlign:"left",inputWidth:176,labelWidth:164},
				{ type:"input" , name:"InterimRate2", label:"ضریب زمانی دریافت اطلاعات :",value:"1",maxLength:4, validate:"NotEmpty", labelAlign:"left",inputWidth:176,labelWidth:164},
				{ type:"input" , name:"StepOneWaitingTime2", label:"زمان انتظار مرحله ۱ :",value:"60",maxLength:4, validate:"NotEmpty,ValidInteger", labelAlign:"left",inputWidth:200},
				{ type:"input" , name:"StepTwoWaitingTime2", label:"زمان انتظار مرحله ۲ :",value:"120",maxLength:4, validate:"NotEmpty,ValidInteger", labelAlign:"left",inputWidth:200},
			]},
		]},
		{ type: "select", name:"NasType", label: "نوع ردیوس :",inputWidth:100,info:"true",required:true, options:[
			{text: "هیچ", value: "None",selected: true},
			{text: "Mikrotik", value: "Mikrotik", list:[
				{ type:"input" , name:"MikrotikDCMethod", label:"روش قطع :",value:"DM",validate:"",required:true, labelAlign:"left",disabled:"true", info:"true", inputWidth:200},
				{type: "select", name:"MikrotikDMAttribute", label: "ویژگی :", inputWidth:198, options:[
					{text: "Acct-Session-Id", value: "Acct-Session-Id"},
					{text: "User-Name", value: "User-Name"},
					{text: "User-Name,Framed-IP-Address", value: "User-Name,Framed-IP-Address",selected: true}
				]},
				{ type:"input" , name:"MikrotikDMPort", label:"پورت قطع :",value:"3799",validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", disabled:true, info:"true", inputWidth:200},
				{type: "select", name:"MikrotikBWManager", label: "مدیریت پهنای باند:", inputWidth:100, options:[
							{text: "خیر", value: "No", selected: true},
							{text: "بلی-COA", value: "Yes-COA"},
							{text: "بلی-SSH", value: "Yes-SSH", list:[
								{ type:"input" , name:"MikrotikBWSSHUser", label:"SSH نام کاربری  :",value:"",maxLength:32,validate:"",required:true, labelAlign:"left",required:true, info:"true", inputWidth:200},
								{ type:"input" , name:"MikrotikBWSSHPass", label:"SSH کلمه عبور  :",value:"",maxLength:32,validate:"",required:true, labelAlign:"left",required:true, info:"true", inputWidth:200},
								{ type: "select", name:"MikrotikBWPriority", label: "اولویت مدیریت پهنای باند :", options:[{text: "یک برابر", value: "Single"},{text: "دوبرابر", value: "Double",selected: true}],inputWidth:100},
							]},
						]},
				
			]},
			{text: "Cisco", value: "Cisco",list:[
				{ type:"input" , name:"CiscoDCMethod", label:"روش قطع :",value:"POD",validate:"",required:true, labelAlign:"left", disabled:"true", info:"true", inputWidth:200},
				{type: "select", name:"CiscoPODAttribute", label: "ویژگی :", inputWidth:200, options:[
					{text: "Acct-Session-Id", value: "Acct-Session-Id"},
					{text: "User-Name", value: "User-Name",selected: true},
					{text: "User-Name,Framed-IP-Address", value: "User-Name,Framed-IP-Address"}
				]},
				{ type:"input" , name:"CiscoPODPort", label:"پورت قطع :",value:"1700",validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", info:"true", inputWidth:200},
				{ type:"input" , name:"CiscoPODCommunity", label:"PODCommunity :",maxLength:32, validate:"", labelAlign:"left", required:true,inputWidth:200}
			]},
			{text: "HuaweiBRas", value: "HuaweiBRas",list:[
				{ type:"input" , name:"HuaweiBRasDCMethod", label:"روش قطع :",value:"DM",validate:"",required:true, labelAlign:"left", info:"true", disabled:"true", inputWidth:200},
				{type: "select", name:"HuaweiBRasDMAttribute", label: "ویژگی :", inputWidth:200, options:[
					{text: "Acct-Session-Id", value: "Acct-Session-Id", selected: true},
					{text: "User-Name", value: "User-Name"},
					{text: "User-Name,Framed-IP-Address", value: "User-Name,Framed-IP-Address"}
				]},
				{ type:"input" , name:"HuaweiBRasDMPort", label:"پورت قطع :",value:"3799",validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", info:"true",  inputWidth:200},
			]},
			{text: "TC1000", value: "TC1000",list:[
				{ type:"input" , name:"TC1000DCMethod", label:"روش قطع :",value:"Telnet",validate:"",required:true, labelAlign:"left", info:"true", disabled:"true", inputWidth:200},
				{ type:"input" , name:"TC1000DCTelnetUser", label:"Telnet نام کاربری :",maxLength:32,value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
				{ type:"input" , name:"TC1000DCTelnetPass", label:"Telnet کلمه عبور :",maxLength:32,value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
			]},
			{text: "AS5200", value: "AS5200",list:[
				{ type:"input" , name:"AS5200DCMethod", label:"روش قطع :",value:"Telnet",validate:"",required:true, labelAlign:"left", info:"true", disabled:"true", inputWidth:200},
				{ type:"input" , name:"AS5200DCTelnetUser", label:"Telnet نام کاربری :",maxLength:32,value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
				{ type:"input" , name:"AS5200DCTelnetPass", label:"Telnet کلمه عبور :",maxLength:32,value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
				{ type:"input" , name:"AS5200DCENPass", label:"DCENPass :",value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
			]},
			{text: "ASA5525", value: "ASA5525",list:[
				{ type:"input" , name:"ASA5525DCMethod", label:"روش قطع :",value:"Telnet",validate:"",required:true, labelAlign:"left", info:"true", disabled:"true", inputWidth:200},
				{ type:"input" , name:"ASA5525DCTelnetUser", label:"Telnet نام کاربری :",maxLength:32,value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
				{ type:"input" , name:"ASA5525DCTelnetPass", label:"Telnet کلمه عبور :",maxLength:32,value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
				{ type:"input" , name:"ASA5525DCENPass", label:"DCENPass :",value:"",validate:"",required:true, labelAlign:"left", info:"true", inputWidth:200},
			]},
			{text: "ZTE", value: "ZTE",list:[
				{ type:"input" , name:"ZTEDCMethod", label:"روش قطع :",value:"DM",validate:"",required:true, labelAlign:"left", info:"true", disabled:"true", inputWidth:200},
				{type: "select", name:"ZTEDMAttribute", label: "ویژگی :", inputWidth:200, options:[
					{text: "Acct-Session-Id", value: "Acct-Session-Id"},
					{text: "User-Name", value: "User-Name",selected: true},
					{text: "User-Name,Framed-IP-Address", value: "User-Name,Framed-IP-Address"}
				]},
				{ type:"input" , name:"ZTEDMPort", label:"پورت قطع :",value:"3799",validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", info:"true",  inputWidth:200},
			]},
			{text: "سفارشی", value: "Custom",list:[
				{type: "select", name:"CustomDCMethod",label: "روش قطع :",inputWidth:200, required:false, labelLeft:5, labelTop:130, inputLeft:120, inputTop:130, options:[
					{text: "DM", value: "DM",selected :true, list:[
						{type: "select", name:"CustomDMAttribute", label: "ویژگی :", inputWidth:190, options:[
							{text: "Acct-Session-Id", value: "Acct-Session-Id", selected: true},
							{text: "User-Name", value: "User-Name"},
							{text: "User-Name,Framed-IP-Address", value: "User-Name,Framed-IP-Address"}
						]},
						{ type:"input" , name:"CustomDMPort", label:"پورت قطع :",maxLength:9,value:"3799", validate:"ValidInteger",required:true, labelAlign:"left", info:"true", inputWidth:190},
					]},	
					{text: "POD", value: "POD",list:[
						{type: "select", name:"CustomPODAttribute", label: "ویژگی :", inputWidth:190, options:[
							{text: "Acct-Session-Id", value: "Acct-Session-Id", selected: true},
							{text: "User-Name", value: "User-Name"},
							{text: "User-Name,Framed-IP-Address", value: "User-Name,Framed-IP-Address"}
						]},
						{ type:"input" , name:"CustomPODPort", label:"پورت قطع :",value:"1700",validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left",info:"true", inputWidth:200},
						{ type:"input" , name:"CustomPODCommunity", label:"PODCommunity :",required:true,maxLength:32, validate:"", labelAlign:"left", inputWidth:190}
					]},
				]},
			]},
		]},
		{type: "select", name:"CreateNewUser", label: "ایجاد کاربر جدید :",inputWidth:80,required:true, options:[
			{text: "خیر", value: "No",selected: true},
			{text: "بلی", value: "Yes", list:[
				{ type: "select", name:"DefReseller_Id",label: "نماینده فروش :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectReseller",required:true,Validate:"IsID",inputWidth:200},
				{ type: "multiselect", name:"DefVisp_Ids",label: "ارائه دهنده مجازی اینترنت :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectVisp",required:true,Validate:"IsID",inputWidth:200,inputHeight:105},
				{ type: "select", name:"DefCenter_Id",label: "مرکز :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectCenter",required:true,Validate:"Integer",inputWidth:200},
				{ type: "select", name:"DefSupporter_Id",label: "پشتیبان :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectSupporter",required:true,Validate:"Integer",inputWidth:200},
				{ type: "select", name:"DefStatus_Id",label: "وضعیت :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectStatus",required:true,Validate:"Integer",inputWidth:200},
				{ hidden:true,type: "select", name:"DefAuthMethod", label: "روش تایید :", options:[{text: "Username-Password", value: "UP"},{text: "Username-CallerId", value: "UC"},{text: "Username", value: "U"},{text: "Username-Password-CallerId", value: "UPC"},{text: "ActiveDirectory", value: "A",selected: true}],inputWidth:200,required:true},
				{ type: "select", name:"DefService_Id",label: "نام سرویس :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectServiceBase",required:true,validate:"IsID",inputWidth:400},
				{ type: "select", name:"SetLocalPassMethod", label: "تنظیم کلمه عبور محلی :", options:[{text: "تصادفی", value: "Random"},{text: "به عنوان شناسایی شده", value: "AsDetected",selected: true},{text: "برابر با نام کاربری", value: "AsUsername"}],inputWidth:200,required:true},
			]},
		]},
		{type:"label"},
		{type:"label"},
		{type:"label"},
		];

	var PermitView=ISPermit("Admin.NasInfo.View");
	var PermitAdd=ISPermit("Admin.NasInfo.Add");
	var PermitEdit=ISPermit("Admin.NasInfo.Edit");
	var PermitDelete=ISPermit("Admin.NasInfo.Delete");


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
	
	if(RowId>0){
		Form1.attachEvent("onOptionsLoaded",function(name){
			if(name=='DefVisp_Ids')
				DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		});
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