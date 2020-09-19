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
	var DataTitle="پارامترهای سرور";
	var DataName="DSServer_1_";
	var ChangeLogDataName='Server';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSServer_1_Edit","70","Admin.Server.Param.Edit",""],
					["پارامتر","DSParam_List","80","Admin.Server.Param.List","ParamItemGroup=Server"],
					["لیست تغییرات","DSChangeLog","95","Admin.Server.ChangeLog.List","ParentId="+RowId+"&ChangeLogDataName=Server"],
					];
	//=======Form1 Server Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
		KeepOldDeltasibBackupItems:"تعداد آخرین موارد از فایل های پشتیبان دلتاسیب برای نگهداری. برای نگهداری همه، عدد 0 را وارد کنید",
		KeepOldURLExportItems:"تعداد آخرین موارد از فایل های گزارش صفحات بازدید شده برای نگهداری. برای نگهداری همه، عدد 0 را وارد کنید",
		KeepOldHTTPLogItems:"تعداد آخرین موارد از فایل های لاگ سازمان تنظیم برای نگهداری.برای نگهداری همه ،عدد 0 را وارد کنید"
	};
	var Form1FieldHelpId=["FaCompanyname","EnCompanyname","Address","Phone","VAT","KeepOldDeltasibBackupItems","KeepOldURLExportItems","KeepOldHTTPLogItems"];
	var Form1TitleField="PartName";
	var Form1DisableItems=["PartName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="PartName";
	var Form1Str = [
		{ type:"settings" , labelWidth:250, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"Server_Id", label:"شناسه سرور :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"PartName", label:"نام بخش :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", inputWidth:100},
		{ type:"input" , name:"FaCompanyname", label:"نام شرکت (Fa) :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:300},
		{ type:"input" , name:"EnCompanyname", label:"نام شرکت (En) :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:300},
		{ type:"input" , name:"Address", label:"آدرس :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:400},
		{ type:"input" , name:"Phone", label:"تلفن :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:400},
		{ type:"input" , name:"VAT", label:"(٪)مالیات :",maxLength:4, validate:"NotEmpty,IsValidPercent",required:true, labelAlign:"left", inputWidth:80},
		{ type: "label"},
		{ type:"input" , name:"SellerName", label:"نام نماینده فروش :",maxLength:32, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},
		{ type:"input" , name:"SellerPhone", label:"تلفن نماینده فروش :",maxLength:20, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},
		{ type:"input" , name:"SellerAddress", label:"آدرس نماینده فروش :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},
		{ type:"input" , name:"SellerEconomyCode", label:"کد اقتصادی نماینده فروش :",maxLength:12, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},
		{ type:"input" , name:"SellerNationalCode", label:"کد ملی نماینده فروش :",maxLength:11, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},
		{ type:"input" , name:"SellerPostalCode", label:"کد پستی نماینده فروش :",maxLength:10, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},
		{ type:"input" , name:"SellerRegistryCode", label:"شماره ثبت نماینده فروش :",maxLength:10, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:250},

		{ type: "label"},
		{ type:"input" , name:"KeepOldDeltasibBackupItems", label:"نگهداری تعداد فایل های پشتیبان :",maxLength:10, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:80,info:true},
		{ type:"input" , name:"KeepOldURLExportItems", label:"نگهداری تعداد گزارش های صفحات بازدید شده :",maxLength:3, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:80,info:true},
		{ type:"input" , name:"KeepOldHTTPLogItems", label:"نگهداری تعداد لاگ سازمان :",maxLength:3, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:80,info:true},

	];

		
	var PermitView=ISPermit("Admin.Server.Param.View");
	var PermitEdit=ISPermit("Admin.Server.Param.Edit");
		
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
		//parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		//if(PermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
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
dhxLayout.progressOff();	
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
