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
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
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
	var DataTitle="ثبت نام کاربر در پنل کاربران";
	var DataName="DSServer_5_";
	var ChangeLogDataName='Server';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSServer_5_Edit","70","Admin.Server.WebNewUser.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.Server.ChangeLog.List","ParentId="+RowId+"&ChangeLogDataName=Server"],
					];
	//=======Form1 Server Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	
							PartName:'آی پی/شبکه مثال 192.168.5.2/32,172.16.2.0/24',
							AfterInsertSMS:'هنگامی که نام کاربری ایجاد شد،این متن ارسال خواهد شد',
							SMSExpireTime:'اگر در این مدت ناموفق بود، برای ارسال پیامک مجدد امتحان می کند',
							AfterInsertMessage:'این پیام هنگامی که کاربر با موفقیت ایجاد شد، به کاربر نشان داده می شود<br>استفاده کنید &lt;br&gt;برای خط جدید از<br>از کاراکتر ~ استفاده نشود',
							OnDuplicateMessage:'این پیام هنگامی که نام کاربری در دلتاسیب وجود داشته باشد و یا قبلا ثبت نام کرده باشد نمایش داده می شود'
						 };
	var Form1FieldHelpId=["PartName",'AfterInsertSMS','SMSExpireTime','AfterInsertMessage','OnDuplicateMessage'];
	var Form1TitleField="PartName";
	var Form1DisableItems=["PartName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="SessionTimeout ";
	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا: ", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"Server_Id", label:"شناسه سرور: ",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"PartName", label:"نام بخش : ",maxLength:32, validate:"NotEmpty,IsValidENName",disabled:"true", labelAlign:"left", inputWidth:122},
		{ type: "select", name:"IsEnableWebNewUser", label: "فعال : ", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:120,required:true},
		{type:"block",name:"BodyBlock",width:600,list:[
			{ type:"settings" , labelWidth:150, inputWidth:250,offsetLeft:10  },
			{ type: "select", name:"SetUsernameTo", label: "ایجاد نام کاربری با :", options:[{text: "موبایل", value: "Mobile",selected: true},{text: "کد ملی", value: "NationalCode"}],inputWidth:120,required:true},
			{ type: "select", name:"NationalCodeRequired", label: "ورود کد ملی ضروری است؟", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:120,required:true},
			{ type: "select", name:"ShahkarValidation", label: "اعتبارسنجی شاهکار :", options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}],inputWidth:120,required:true},
			{ type: "select", name:"Visp_Id",label: "ارائه دهنده مجازی اینترنت :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectVisp",validate:"IsID",inputWidth:200,required:true},
			{ type: "select", name:"Center_Id",label: "تنظیم مرکز :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectCenter",validate:"IsID",inputWidth:200,required:true},
			{ type: "select", name:"Supporter_Id",label: "تنظیم پشتیبان :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectSupporter",validate:"IsID",inputWidth:200,required:true},
			{ type: "select", name:"Reseller_Id",label: "تنظیم نماینده فروش :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectReseller",validate:"IsID",inputWidth:200,required:true},
			{ type: "select", name:"Status_Id",label: "تنظیم وضعیت :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectStatus",validate:"IsID",inputWidth:420,required:true},
			{ type: "select", name:"Service_Id",label: "انتخاب سرویس :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectServiceBase",inputWidth:420},
			{ type: "input" , name:"AfterInsertMessage", label:"پیغام پس از ایجاد کاربر :", validate:"^[^\~]+$",note:{text:"مثال:ثبت نام انجام شد. نام کاربری به شماره همراه شما ارسال می گردد"},rows:3 , maxLength:255,inputWidth:422,required:true,info:true},
			{ type: "input" , name:"OnDuplicateMessage", label:"پیغام وجود نام کاربری  :", validate:"^[^\~]+$",note:{text:"مثال:ثبت نام قبلا انجام شده است. نام کاربری و رمز عبور مجددا به شماره همراه شما ارسال می گردد"},rows:3 , maxLength:255,inputWidth:422,required:true,info:true},
			{ type: "input" , name:"AfterInsertSMS", label:"پیام کوتاه پس ازایجاد کاربر :", validate:"",note:{text:"ثبت نام انجام شد.  </br> نام كاربري:"+CreateSMSItems("[Username]")+"</br> كلمه عبور:"+CreateSMSItems("[Pass]")+"<br/><span style='color:red;font-weight:bold'></span>"},rows:4 , maxLength:255,inputWidth:422,info:true},
			{ type: "input" , name:"SMSExpireTime",label: " زمان انقضا پیامک(ثانیه) : ",value:'3600',validate:"^[6-9][0-9]|[1-9][0-9][0-9]+$",inputWidth:120,maxLength:5,required:true,info:true},
		]},{ type: "label"},{ type: "label"},{ type: "label"},{ type: "label"},
		];

	var PermitView=ISPermit("Admin.Server.WebNewUser.View");
	var PermitEdit=ISPermit("Admin.Server.WebNewUser.Edit");
		
		
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
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(PermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!PermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		//parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		//if(PermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function CreateSMSItems(Item){
	return "<a href='javascript:void(0)' onclick='CopyTextToClipBoard(\""+Item+"\")' style='text-decoration:none' title='Click to copy'>"+Item+"</a>";
}
function Form1OnInputChange(name,value){
	if(name=="IsEnableWebNewUser"){
		if(value=="Yes")
			Form1.enableItem("BodyBlock");
		else
			Form1.disableItem("BodyBlock")
	}
	else if(name=="SetUsernameTo"){
		if(value=="NationalCode"){
			Form1.setItemValue("NationalCodeRequired","Yes");
			Form1.disableItem("NationalCodeRequired");
		}
		else
			Form1.enableItem("NationalCodeRequired");
	}
	else if(name=="AfterInsertSMS"){
		if(value!='')
			Form1.enableItem("SMSExpireTime");
		else
			Form1.disableItem("SMSExpireTime");
	}
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
	if(Form1.getItemValue("IsEnableWebNewUser")=="Yes")
		Form1.enableItem("BodyBlock");
	else
		Form1.disableItem("BodyBlock");
	if(Form1.getItemValue("SetUsernameTo")=="NationalCode")
		Form1.disableItem("NationalCodeRequired");
	else
		Form1.enableItem("NationalCodeRequired");
	
	if(Form1.getItemValue("AfterInsertSMS")!='')
		Form1.enableItem("SMSExpireTime");
	else
		Form1.disableItem("SMSExpireTime");
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