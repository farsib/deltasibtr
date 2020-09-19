<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSIPPoolEdit ....................................................................................");
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
	var DataTitle="کنترل بدهی کاربر";
	var DataName="DSDebitControl_";
	var ChangeLogDataName='DebitControl';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSDebitControl_Edit","70","Admin.User.DebitControl.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.DebitControl.ChangeLog.List","ChangeLogDataName=DebitControl"]
					];
	//=======Form1 DebitControl Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={DebitControlName:'انگلیسی - ۳۲ کاراکتر - غیر قابل ویرایش پس از ذخیره'};
	var Form1FieldHelpId=["DebitControlName"];
	var Form1TitleField="DebitControlName";
	var Form1DisableItems=["DebitControlName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="MaxDebit";

	var Form1Str = [
		{ type:"settings" , labelWidth:120, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"DebitControl_Id", label:"DebitControl_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"DebitControlName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:150},
		{ type: "input" , name:"MaxDebit", label:"حداکثر بدهی(ریال) :", validate:"IsValidPrice",value:"0", labelAlign:"left", maxLength:14,inputWidth:100,numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{ type:"input" , name:"MaxDelay", label:"حداکثر زمان مجاز تسویه(ثانیه) :",maxLength:9, validate:"ValidInteger",required:true, labelAlign:"left", inputWidth:150},
		];

	var PermitView=ISPermit("Admin.User.DebitControl.View");
	var PermitAdd=ISPermit("Admin.User.DebitControl.Add");
	var PermitEdit=ISPermit("Admin.User.DebitControl.Edit");
	var PermitDelete=ISPermit("Admin.User.DebitControl.Delete");

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

	MyHint = new dhtmlXPopup({form: Form1,id:["MaxDelay"],mode:"right"});
	MyHint.attachEvent("onContentClick",function(){MyHint.hide()});
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	Form1.attachEvent("onFocus",function(name){
		if(name=="MaxDelay")
			Form1OnInputChange("MaxDelay",Form1.getItemValue("MaxDelay"));
		else
			MyHint.hide();
	});	
	
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
function Form1OnInputChange(name,value){
	if(name=="MaxDelay"){
		if(value==parseInt(value)){
			MyHint.attachHTML("<div style='color:blue;text-align:center;font-weight:bold'>&nbsp;&nbsp;"+GetTimeString(value)+"&nbsp;&nbsp;</div>");
			MyHint.show("MaxDelay");
		}
		else
			MyHint.hide();
	}	
}

function GetTimeString(t){
	var seconds = Math.floor(t % 60 );
	var minutes = Math.floor((t/60) % 60 );
	var hours = Math.floor((t/(3600)) % 24 );
	var days = Math.floor(t/86400 );
	return (days>0?(days+" Day"+(days>1?"s":"")+" & "):"")+(hours<10?"0"+hours:hours)+":"+(minutes<10?"0"+minutes:minutes)+":"+(seconds<10?"0"+seconds:seconds);
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
	Form1OnInputChange("MaxDelay",Form1.getItemValue("MaxDelay"));
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
