<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSUser_WebMessage_Edit.php ....................................................................................");
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
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
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
	var User_Id = "<?php  echo $_GET['User_Id'];  ?>";
	if(User_Id == "" ) {return;}
	var DataTitle="پیام پنل";
	var DataName="DSUser_WebMessage_";
	var RenderFile=DataName+"EditRender";
	var ChangeLogDataName='WebMessage';
	var TopToolbar;
	
	//=======Form1 WebMessage Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
		WebMessageTitle:'عنوان پیام وب',
		WebMessageBody:'بدنه پیام',
		ReplaceCR:'نمایش داده می شود.برای نمایش درست خط بعدی،تیک این گزینه را بگذارید HTML پیام در پنل کاربران به صورت تگ های'
	};
	var Form1FieldHelpId=["WebMessageTitle","WebMessageBody","ReplaceCR"];
	var Form1TitleField="WebMessageTitle";
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="WebMessageTitle";

	
	var Form1Str = [
		{ type:"settings" , labelWidth:90, inputWidth:160,offsetLeft:10  },
		{ type:"hidden" , name:"WebMessage_Id"},
		{ type: "label"},
		// { type:"input" , name:"WebMessageStatus", label:"Status :", labelAlign:"left",disabled:true,hidden:true},
		{ type:"input" , name:"WebMessageTitle", label:"عنوان :", maxLength:64, validate:"NotEmpty", labelAlign:"left", inputWidth:350,info:true, required: true},
		{type: "input", name: "WebMessageBody", label: "پیام: ", maxLength:2048, validate:"NotEmpty", rows:12, inputWidth: 430,info:true, required: true,note: { text: "می توانید از کدهای HTML  و یا از متن ساده استفاده کنید"}},
		{type: "checkbox", label: "&lt;br/&gt; با \\n  جایگذاری", labelWidth:140, name: "ReplaceCR"/* , position: "label-right" */, checked: true , info:true,position: "label-right"},
		{type: "block",width:400, list:[
			{type: "button", name:"Sample1", value: " نمونه 1 ", width :65},
			{type: "newcolumn", offset:10},
			{type: "button", name:"Sample2", value: " نمونه 2 ", width :65}
		]}
	];

	var PermitView=ISPermit("Visp.User.WebMessage.View");
	var PermitAdd=ISPermit("Visp.User.WebMessage.Add");
	var PermitEdit=ISPermit("Visp.User.WebMessage.Edit");

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);
	TopToolbar.addSeparator("sep1",null);
	
	// Form1   ===================================================================
	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,RenderFile+".php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		if(PermitView)	DSToolbarAddButton(TopToolbar,null,"Retrieve","بروزکردن","Retrieve",TopToolbar_OnRetrieveClick);
		if(PermitEdit)
			DSToolbarAddButton(TopToolbar,null,"Save","ذخیره","tof_Save",TopToolbar_OnSaveClick);
		else
			FormDisableAllItem(Form1);
	}
	else{
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(PermitAdd)		DSToolbarAddButton(TopToolbar,null,"Save","ذخیره","tof_Save",TopToolbar_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function TopToolbar_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,RenderFile+".php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function TopToolbar_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,RenderFile+".php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
		else{//insert
			DSFormInsertRequestProgress(dhxLayout,Form1,RenderFile+".php?"+un()+"&act=insert&User_Id="+User_Id,Form1DoAfterInsertOk,Form1DoAfterInsertFail);
			
		}//insert
	}
}


function Form1OnButtonClick(name){
	if(Form1.getItemValue("WebMessageBody")!=""){
		parent.dhtmlx.confirm({
			title: "هشدار",
			type:"confirm",
			ok: "بلی",
			cancel: "خیر",
			text: "آیا از پاک شدن متن نوشته شده مطمئن هستید؟",
			callback: function(Result){
				if(Result){
					Form1.setItemValue("WebMessageBody","");
					Form1OnButtonClick(name);
				}
			}
		});	
		return;
	}
	if(name=="Sample1"){
		Form1.setItemValue("WebMessageTitle",'تست پیام شامل کد های HTML');
		Form1.setItemValue("WebMessageBody","<div style='width:100%;text-align:center'><strong style='font-size:200%;color:firebrick'>مشترک گرامی</strong><p>این یک متن عادی است - <b>و این یک متن تو پر می باشد</b>.</p><a href='http://www.smartispbilling.com' target='_blank'>شرکت پیام آوران کویر</a><br/><br/><img width='40%' src='https://www.google.com/inbox/assets/images/intro/intro-logo.png'/></div>");
		Form1.uncheckItem("ReplaceCR");
		Form1.setItemFocus("SMSMessage");
		Form1.resetValidateCss();
	}
	else if(name=="Sample2"){
		Form1.setItemValue("WebMessageTitle",'تست پیام شامل متن ساده');
		Form1.setItemValue("WebMessageBody","DeltaSIB یک نرم افزار Accounting قابل استفاده در ISP ها، ISDP ها، ادارات، دانشگاه ها، کافی نت ها و از این قبیل می باشد. این مجموعه پس از 3 سال تلاش مستمر با استفاده از تجربه 12 ساله در این زمینه طراحی و ایجاد شده است.");
		Form1.checkItem("ReplaceCR");
		Form1.setItemFocus("SMSMessage");
		Form1.resetValidateCss();
	}
}

function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}	
function Form1DoAfterLoadOk(){
	parent.dhxLayout.dhxWins.window("popupWindow").setText("ویرایش "+DataTitle+" ["+Form1.getItemValue(Form1TitleField)+"]");
}	

function Form1DoAfterLoadFail(){
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,RenderFile+".php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
}
function Form1DoAfterUpdateFail(){
}
function Form1DoAfterInsertOk(r){
	RowId=r;
	parent.UpdateGrid(r);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,RenderFile+".php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	if(PermitView)	DSToolbarAddButton(TopToolbar,null,"Retrieve","بروزکردن","Retrieve",TopToolbar_OnRetrieveClick);
	if(!PermitEdit){
		TopToolbar.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	
}
function Form1DoAfterInsertFail(){
}
	
}

	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>