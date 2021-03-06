<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSCenterEdit ....................................................................................");
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
	var DataTitle="مرکز";
	var DataName="DSCenter_";
	var ChangeLogDataName='Center';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSCenter_Edit","70","Admin.Center.Edit",""],
					["پارامتر","DSParam_List","80","Admin.Center.Param.List","ParamItemGroup=Center"],
					//["Ports","DSCenter_Ports_List","80","Admin.Center.Ports.List",""],
					["دسترسی ارائه دهنده مجازی اینترنت","DSCenter_VispAccess_List","200","Admin.Center.VispAccess.List",""],
					["لیست تغییرات","DSChangeLog","95","Admin.Center.ChangeLog.List","ChangeLogDataName=Center"]
					];
	//=======Form1 Center Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	TotalPort:'مجموع پورت های فعال در این مرکز - تعداد کاربر قابل محدود سازی است',
							BadPort:'پورت های خراب یا غیرقابل استفاده در این مرکز',
							UsernamePattern:"علامت '.' میتواند جایگزین هر کاراکتری شود.لیست کاراکترهایی که می خواهیم جایگزین شوند درون براکت قرار می گیرند\r[...] و لیست کاراکترهایی که نمی خواهیم از آن ها استفاده شود بصورت [...^] وارد می کنیم",
							NOE :'موقعیت مکانی وایرلس،برای گزارش مورد استفاده است'
						};
	var Form1FieldHelpId=["UsernamePattern",",TotalPort","BadPort","NOE"];
	var Form1TitleField="CenterName";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="UsernamePattern";

	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"Center_Id", label:"شناسه مرکز :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"CenterName", label:"نام مرکز :",maxLength:64, validate:"NotEmpty",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"Country", label:"کشور :",maxLength:64, validate:"NotEmpty", labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"State", label:"استان :",maxLength:64, validate:"NotEmpty", labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"City", label:"شهر :",maxLength:64, validate:"NotEmpty", labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"Center", label:"مرکز :",maxLength:64, validate:"NotEmpty", labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"PopSite", label:"نقطه دسترسی :",maxLength:64, validate:"NotEmpty", labelAlign:"left", info:"true", inputWidth:200},
		{ type: "input" , name:"NOE", label:"موقعیت مکانی وابرلس :", validate:"",value:"", maxLength:32,inputWidth:200, info:true},
		{ type:"input" , name:"TotalPort", label:"مجموع پورت ها :",maxLength:6,value:"999999", validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"BadPort", label:"پورت خراب :",maxLength:6,value:"0", validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"UsernamePattern", label:"الگو نام کاربری :",maxLength:250, value:".*",validate:"NotEmpty",required:true, labelAlign:"left", info:"true", inputWidth:400,note: { text: "<a href='http://smartispbilling.com/help/deltasib/content/loadContent/regularExpressions' target='myIframe'>صفحه راهنما</a>"}},
		];
		
		
	ISPermitView=ISPermit("Admin.Center.View");
	ISPermitEdit=ISPermit("Admin.Center.Edit");
	ISPermitAdd=ISPermit("Admin.Center.Add");
	
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
		if(ISPermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(ISPermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!ISPermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(ISPermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
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
	if(ISPermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!ISPermitEdit){
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
