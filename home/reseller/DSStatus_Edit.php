<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSStatusEdit ....................................................................................");
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
	var DataTitle="وضعیت";
	var DataName="DSStatus_";
	var ChangeLogDataName='Status_';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSStatus_Edit","70","Admin.User.Status.Edit",""],
					["وضعیت به","DSStatus_StatusTo_List","80","Admin.User.Status.StatusTo.List",""],
					["دسترسی نماینده","DSStatus_ResellerAccess_List","130","Admin.User.Status.ResellerAccess.List",""],
					["دسترسی ارائه دهنده مجازی اینترنت","DSStatus_VispAccess_List","200","Admin.User.Status.VispAccess.List",""],
					["لیست تغییرات","DSChangeLog","90","Admin.User.Status.ChangeLog.List","ChangeLogDataName=Status"]
					];
	//=======Form1 Visp Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	StatusName:'حداکثر ۶۴ کاراکتر',
							UserStatus:'انتخاب وضعیت تعریف شده',
							InitialStatus:'هنگام ساخت کاربر،فقط وضعیت هایی که وضعیت اولیه آن ها فعال است قابل انتخاب اند',
							CanWebLogin:'مشخص می کند که کاربر با این وضعیت،می تواند وارد پنل کاربری بشود یا خیر',
							CanAddService:'Specifies whether for a user in this status can add any service',
							IsBusyPort:'مشخص می کند که کاربران با این وضعیت،از تعداد پورت های تعریف شده در مرکز کسر شود یا خیر',
							PortStatus:'برای گزارش کاربران مورد استفاده است',
							AfterStatusSMS:'متن پیام برای ارسال پیامک پس از تغییر وضعیت کاربر به این وضعیت',
							SMSExpireTime:'تعداد ثانیه هایی که سرور سعی می کند پیامک را پس تغییر وضعیت ارسال کند</br>اگر این زمان قبل از ارسال سپری شود،پیامک منقضی خواهد شد(مقدار ۶۰ تا ۹۹۹۹۹ ثانیه)',
							NewStatus_Id:'New status to set after first connect request.'
						};
	var Form1FieldHelpId=["StatusName","UserStatus","NewStatus_Id","InitialStatus","CanWebLogin","CanAddService","IsBusyPort","PortStatus","AfterStatusSMS","SMSExpireTime"];
	var Form1TitleField="StatusName";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="UserStatus";
	
	function CreateForm1Str(RowId){
		var Form1Str = [
			{ type:"settings" , labelWidth:140, inputWidth:250,offsetLeft:10  },
			{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:160, info:true},
			{ type: "label"},
			{ type:"hidden" , name:"Status_Id", label:"شناسه وضعیت :",disabled:true, labelAlign:"left", inputWidth:130},
			{ type:"input" , name:"StatusName", label:"نام وضعیت :",maxLength:64, validate:"NotEmpty",required:true, labelAlign:"left", info:true, inputWidth:500},
			{ type: "select", name:"UserStatus", label: "وضعیت کاربر :",required:true, options:[
				{text: "فعال", value: "Enable",selected: true},
				{text: "غیرفعال", value: "Disable"},
				{text: "تغییر در اولین اتصال", value: "ChangeOnFirstConnect",list:[
					{ type: "select", name:"NewStatus_Id",label: "وضعیت جدید :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectNewStatus&Status_Id="+RowId,required:true,validate:"IsID",inputWidth:500,info:true},
				]},
				{text: "سرویس رایگان ردیوس", value: "AddFreeServiceByNAS",list:[
					{ type: "select", name:"NewStatus_Id",label: "وضعیت جدید :",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectNewStatus&Status_Id="+RowId,required:true,validate:"IsID",inputWidth:500,info:true},
				]}
				],inputWidth:160,info:true},
			{ type: "select", name:"InitialStatus", label: "وضعیت اولیه :",required:true, options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}],inputWidth:160,info:true},
			{ type: "select", name:"CanWebLogin", label: "دسترسی به پنل کاربر :",required:true, options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:160,info:true},
			{ type: "select", name:"CanAddService", label: "دسترسی افزودن سرویس :",required:true, options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:160,info:false},
			{ type: "select", name:"IsBusyPort", label: "پورت اشغال شده؟ :",required:true, options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:160,info:true},
			
			{ type: "select", name:"PortStatus", label: "وضعیت پورت :",required:true, 
				options:[
				{text: "در حال انتظار", value: "Waiting"},
				{text: "رزرو", value: "Reserve"},
				{text: "ارسال برای رانژه", value: "GoToBusy"},
				{text: "مشغول", value: "Busy",selected: true},
				{text: "در حال جمع آوری", value: "GoToFree"},
				{text: "آزاد", value: "Free"},
				{text: "هیچکدام", value: "None"},
				],inputWidth:160,info:true},
			{ type:"input" , name:"AfterStatusSMS", label:"پیامک پس از تغییر وضعیت :",maxLength:250, rows:3,validate:"", labelAlign:"left",inputWidth:500,note: { text: "<span style='direction:rtl;float:right;'>برای عدم ارسال پیامک خالی بگذارید<br/>می توانید استفاده کنید از : "+CreateSMSItems("[Name]")+" , "+CreateSMSItems("[Family]")+" , "+CreateSMSItems("[Username]")+" , "+CreateSMSItems("[Pass]")+" , "+CreateSMSItems("[SHDate]")+" , "+CreateSMSItems("[Time]</span>")},info:true},
			{ type: "input" , name:"SMSExpireTime",label: "زمان انقضا پیامک(ثانیه):",value:'3600',validate:"^[6-9][0-9]|[1-9][0-9][0-9]+$",inputWidth:160,maxLength:5, required: true,info:true,disable:true},
			{ type: "select", name:"ServiceStatus", label: "وضعیت سرویس :",required:true, 
				options:[
				{text: "سلب امتیاز", value: "Erased"},
				{text: "برای فعال شدن منتظر تایید مدارک", value: "ReadytoActivateSoft",selected: true},
				{text: "تعلیق دو طرفه", value: "Hard"},
				{text: "تعلیق یک طرفه", value: "Soft"},
				{text: "فعال", value: "Active"},
				]/*,inputWidth:160,info:true,note: { text: "Erased = سلب امتیاز<br/>Ready to Activate Soft = برای فعال شدن منتظر تایید مدارک <br/>Hard = تعلیق دوطرفه<br/>Active = فعال<br/>Soft = تعلیق یک طرفه"}*/}
		];
		return Form1Str;
	}


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
	if(RowId>0){
		Form1=DSInitialForm(TabbarMain.cells(0),CreateForm1Str(RowId),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
		Form1.attachEvent("onInputChange",Form1OnInputChange);
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(ISPermit("Admin.User.Status.View"))	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(ISPermit("Admin.User.Status.Edit"))	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!ISPermit("Admin.User.Status.Edit"))	FormDisableAllItem(Form1);
	}
	else{
		Form1=DSInitialForm(TabbarMain.cells(0),CreateForm1Str(0),Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
		Form1.attachEvent("onInputChange",Form1OnInputChange);
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(ISPermit("Admin.User.Status.Add"))		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function CreateSMSItems(Item){
	return "<a href='javascript:void(0)' onclick='CopyTextToClipBoard(\""+Item+"\");' style='text-decoration:none' title='کلیک برای کپی'>"+Item+"</a>";
}

function Form1OnInputChange(name,value){
	if(name=="AfterStatusSMS")
		if(value!='')
			Form1.enableItem("SMSExpireTime");
		else
			Form1.disableItem("SMSExpireTime");
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
	if(Form1.getItemValue("AfterStatusSMS")!='')
		Form1.enableItem("SMSExpireTime");
	else
		Form1.disableItem("SMSExpireTime");
	/*
	Status=Form1.getItemValue("Status");
	if((Status=='Register')||(Status=='Normal')){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	*/
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
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	if(ISPermit("Admin.User.Status.View"))	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!ISPermit("Admin.User.Status.Edit")){
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