<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSWebServiceEdit ....................................................................................");
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
	var DataTitle="وب سرویس";
	var DataName="DSWebService_";
	var ChangeLogDataName='WebService';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSWebService_Edit","70","Admin.WebService.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.WebService.ChangeLog.List","ChangeLogDataName=WebService"],
					];
	//=======Form1 WebService Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
		WebService_Username:'نام کاربری وب سرویس',
		WebService_UserPass:'کلمه عبور وب سرویس',
		PermitIP:'آی پی مجاز برای سرویس وب',
		UserCreate:'بتواند کاربر ایجاد کند',
		UserAddService:'بتواند یک سرویس پس پرداخت را برای کاربر اضافه کند',
		UserChangeStatus:'بتواند وضعیت کاربر را تفییر دهد',
		UserActivateNextService:'بتواند سرویس پایه بعدی کاربر را در صورت وجود فعال کند',
		UserDelete:'بتواند کاربر را حذف کند',
		UserAddPayment:'بتواند پرداخت را برای کاربر اضافه کند',
		UserSendSMS:'توانایی ارسال پیامک',
		UserUpdateInfo:'بتواند اطلاعات کاربر را بروزرسانی کند',
		UserChangePassord:'بتواند کلمه عبور کاربر را بروزرسانی کند'
	};
	var Form1FieldHelpId=["WebService_Username","WebService_UserPass","PermitIP","UserCreate","UserAddService","UserChangeStatus","UserActivateNextService","UserDelete","UserAddPayment","UserSendSMS","UserUpdateInfo"];
	var Form1TitleField="WebService_Username";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="WebService_Username";
	
	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"WebService_User_Id", label:"شناسه کاربر وب سرویس :",disabled:true, labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال:", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"WebService_Username", label:"نام کاربری وب سرویس :",maxLength:32, validate:"NotEmpty",required:true, labelAlign:"left", info:true, inputWidth:100},
		{ type:"input" , name:"WebService_UserPass", label:"کلمه عبور وب سرویس :",maxLength:64, validate:"NotEmpty",required:true, labelAlign:"left", info:true, inputWidth:100},
		{ type:"input" , name:"PermitIP", label:"آی پی مجاز:", value:"127.0.0.1/32",maxLength:255,validate:"ISPermitIp", labelAlign:"left", inputWidth:300, info:true, required:true},
		{type: "label"},
		{type: "fieldset", label: "مجوزها", width:700, list: [
			{ type:"settings" , labelWidth:130, inputWidth:200,offsetLeft:10},
			{type: "checkbox", label: "ایجاد کاربر", name: "UserCreate", position: "label-right", checked: false , info:true,labelWidth:149},
			{type: "newcolumn"},
			{type: "checkbox", label: "تغییر کلمه عبور کاربر", name: "UserChangePassword", position: "label-right", checked: false , info:true},
			{type: "newcolumn"},
			{type: "checkbox", label: "افزودن سرویس کاربر", name: "UserAddService", position: "label-right", checked: false , info:true},
			{type: "newcolumn"},
			{type: "checkbox", label: "تغییر وضعیت کاربر", name: "UserChangeStatus", position: "label-right", checked: false , info:true},
			{type: "newcolumn"},
			{type: "checkbox", label: "فعال کردن سرویس بعدی کاربر", name: "UserActivateNextService", position: "label-right", checked: false , info:true,labelWidth:149},
			{type: "newcolumn"},
			{type: "checkbox", label: "حذف کاربر", name: "UserDelete", position: "label-right", checked: false , info:true},
			{type: "newcolumn"},
			{type: "checkbox", label: "افزودن پرداخت کاربر", name: "UserAddPayment", position: "label-right", checked: false , info:true},
			{type: "newcolumn"},
			{type: "checkbox", label: "ارسال پیامک کاربر", name: "UserSendSMS", position: "label-right", checked: false , info:true},
			{type: "newcolumn"},
			{type: "checkbox", label: "بروزرسانی اطلاعات کاربر", name: "UserUpdateInfo", position: "label-right", checked: false , info:true,hidden:true},
		]}
		];

	var PermitView=ISPermit("Admin.WebService.View");
	var PermitAdd=ISPermit("Admin.WebService.Add");
	var PermitEdit=ISPermit("Admin.WebService.Edit");
	var PermitDelete=ISPermit("Admin.WebService.Delete");

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


function Form1OnButtonClick(name){
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