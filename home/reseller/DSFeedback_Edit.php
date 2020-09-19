<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSFeedbackEdit ....................................................................................");
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
	var DataTitle="بازخورد";
	var DataName="DSFeedback_";
	var ChangeLogDataName='Feedback';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSFeedback_Edit","70","CRM.Feedback.Edit",""],
					["لیست تغییرات","DSChangeLog","95","CRM.Feedback.ChangeLog.List","ChangeLogDataName=Feedback"],
					];
	//=======Form1 Feedback Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	Username:'Username that user enter',OnlineUsername:'Online Username with Request IP'};
	var Form1FieldHelpId=["Reply"];
	var Form1TitleField="Reply";
	var Form1DisableItems=['FeedbackName'];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="Reply";
 	 	 	 	 	 	 	 	 	 	 	 	 	
	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"User_Feedback_Id", label:"User_Feedback_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{disabled:"true", type: "select", name:"Status", label: "وضعیت :", options:[{text: "درانتظار پاسخ", value: "Waitting",selected: true},{text: "پاسخ داده شده", value: "Replied"}],inputWidth:100},
		{disabled:"true", type:"input" , name:"Username", label:"نام کاربری:",maxLength:32, validate:"", labelAlign:"left", info:"true", inputWidth:100},
		{disabled:"true", type:"input" , name:"IP", label:"آی پی درخواست دهنده :",maxLength:32, validate:"", labelAlign:"left", info:"true", inputWidth:100},
		{disabled:"true", type:"input" , name:"OnlineUsername", label:"نام کاربری آنلاین :",maxLength:32, validate:"", labelAlign:"left", info:"true", inputWidth:100},
		{disabled:"true", type:"input" , name:"RequestCDT", label:"زمان درخواست :",maxLength:32, validate:"", labelAlign:"left",  inputWidth:200},
		{disabled:"true", type:"input" , name:"ReplyCDT", label:"زمان پاسخ :",maxLength:32, validate:"", labelAlign:"left",  inputWidth:200},
		{disabled:"true", type:"input" , name:"Email", label:"ایمیل :",maxLength:32, validate:"", labelAlign:"left",  inputWidth:300},
		{disabled:"true", type:"input" , name:"MobileNo", label:"موبایل :",maxLength:32, validate:"", labelAlign:"left",  inputWidth:100},
		{disabled:"true",type: "select", name:"RequestType", label: "نوع درخواست", inputWidth:200, options:[
			{text: "پیشنهاد", value: "suggestions",selected: true},
			{text: "انتقاد", value: "criticism"},
		]},
		{disabled:"true",type: "select", name:"ServiceType", label: "نوع سرویس :", inputWidth:200, options:[
			{text: "کارت اینترنت", value: "001"},
			{text: "اینترنت هوشمند", value: "002"},
			{text: "اینترنت بیسیم", value: "003"},
			{text: "اینترنت پرسرعت", value: "004",selected: true},
			{text: "خدمات تلفن بین الملل", value: "006"},
			{text: "پهنای باند اختصاصی", value: "007"},
		]},
		{disabled:"true", type:"input" , name:"KeyStr", label:"کد رهگیری :", validate:"", labelAlign:"left",  inputWidth:200},
		{disabled:"true", type:"input" , name:"Message", label:"پیام :",maxLength:32,rows:3, validate:"", labelAlign:"left",  inputWidth:400},
		{type:"input" , name:"Reply", label:"پاسخ :",maxLength:1024, validate:"NotEmpty",rows:4,required:true, labelAlign:"left",  inputWidth:400},
		];
	var PermitView=ISPermit("CRM.Feedback.View");
	var PermitEdit=ISPermit("CRM.Feedback.Edit");

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
		Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(PermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!PermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
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
	dhxLayout.progressOff();

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
	Form1.unload();
	Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!PermitEdit){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	dhxLayout.progressOff();
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