<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSNetworkIPEdit ....................................................................................");
	PrintInputGetPost();
	if($LastError!=""){
		DSDebug(0,"نشست منقضی شد");
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
<html dir="rtl">
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
	var DataTitle="آی پی شبکه";
	var DataName="DSNetworkIP_";
	var ChangeLogDataName='NetworkIP';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSNetworkIP_Edit","70","Admin.NetworkIP.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.NetworkIP.ChangeLog.List","ChangeLogDataName=NetworkIP"],
					];
	//=======Form1 NetworkIP Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
		AssignmentTo:'نام مستعار برای دامنه آی پی. فقط برای گزارش استفاده می شود',
		IPType:'نوع دامنه آی پی .اگر دامنه آی پی NAT شده است،نت لاگ باید آی پی ترجمه را بررسی کند و اگر دامنه route شده است،خود آی پی ذخیره می شود',
		ISAuthenticate:'اگر دامنه آی پی  برای کاربران PPP یا هات اسپات استفاده می شود گزینه بلی و برای دامنه کاربران شبکه محلی از خیر استفاده کنید',
		StartIP:'آی پی شروع دامنه',
		EndIP:'آی پی پایان دامنه',
		Comment:'توضیحات برای دامنه آی پی. فقط برای گزارش استفاده می شود'
	};

	var Form1FieldHelpId=["AssignmentTo","IPType","ISAuthenticate","StartIP","EndIP","Comment"];
	var Form1TitleField="AssignmentTo";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="AssignmentTo";

	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"NetworkIP_Id", label:"شناسه آی پی شبکه :",disabled:true, labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"AssignmentTo", label:"اختصاص به :",maxLength:32, validate:"NotEmpty",required:true, labelAlign:"left", info:true, inputWidth:200},
		{ type: "select", name:"IPType", label: "نوع آی پی :", options:[{text: "NAT", value: "NAT",selected: true},{text: "Route", value: "Route"}],inputWidth:80,required:true, info:true},
		{ type: "select", name:"ISAuthenticate", label: "تایید اعتبار :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true, info:true},
		{ type: "select", name:"UseByIPDR", label: "استفاده برای نت لاگ :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type: "select", name:"UserType", label: "نوع کاربر :",
			options:[
				{text: "DialUp", value: "DialUp"},
				{text: "ADSL", value: "ADSL",selected: true},
				{text: "Wireless", value: "Wireless"},
				{text: "TD-LTE", value: "TD-LTE"},
				{text: "Wi-Fi", value: "Wi-Fi"},
				{text: "Mobile2G", value: "Mobile2G"},
				{text: "Mobile3G", value: "Mobile3G"},
				{text: "Mobile4G", value: "Mobile4G"},
				{text: "Mobile5G", value: "Mobile5G"},
				{text: "DedicatedBandwidth", value: "DedicatedBandwidth"}
			],inputWidth:80,required:true, info:true},
		{ type: "select", name:"ISHotSpot", label: "هات اسپات ؟ :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true, info:true},
		{ type:"input" , name:"StartIP", label:"آی پی شروع :",maxLength:15,value:"", validate:"NotEmpty,ValidIPv4",required:true, labelAlign:"left", inputWidth:200, info:true},
		{ type:"input" , name:"EndIP", label:"آی پی پایان :",maxLength:15,value:"", validate:"NotEmpty,ValidIPv4",required:true, labelAlign:"left", inputWidth:200, info:true},
		{ type:"input" , name:"NOE", label:"موقعیت مکانی وایرلس :",maxLength:32,value:"", labelAlign:"left", inputWidth:200},
		{ type:"input" , name:"Comment", label:"توضیح :",maxLength:200,rows:3, labelAlign:"left", info:true, inputWidth:200}
		];

	var PermitView=ISPermit("Admin.NetworkIP.View");
	var PermitAdd=ISPermit("Admin.NetworkIP.Add");
	var PermitEdit=ISPermit("Admin.NetworkIP.Edit");
	var PermitDelete=ISPermit("Admin.NetworkIP.Delete");

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