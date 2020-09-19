<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSPermitList ....................................................................................");
	if($LResellerName==""){
		DSDebug(0,"Session Expire");
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
	var RowId = "<?php  echo $_GET['ParentId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="اطلاعات کاربر";
	var DataName="DSUser_Info";
	
	//=======Form1 Service Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	AdslPhone:'Phone Number of Adsl Users, maxlength:15',
							UserType:'Type of User, using for create log report',
							NOE :'Wireless Location, using for create log report',
							BirthDate:'Leave blank or enter year(4 digit)/month/day'
						};
	var Form1FieldHelpId=["AdslPhone","UserType","NOE","BirthDate"];
	var Form1TitleField="";
	var Form1DisableItems=[""];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="Name";
	var Form1Str = [
		{ type:"settings",labelWidth:150,offsetLeft:10},
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type: "hidden" , name:"User_Id", label:"User_Id :",disabled:"true", labelAlign:"left", inputWidth:80},
		{ type: "select", name:"UserType", label: "UserType :", options:[{text: "LAN", value: "LAN",selected: true},{text: "ADSL", value: "ADSL"},{text: "Wireless", value: "Wireless"},{text: "Wi-Fi", value: "Wi-Fi"},{text: "WiFiMobile", value: "WiFiMobile"},{text: "Dialup", value: "Dialup"},{text: "Dialup-PRM", value: "Dialup-PRM"}],inputWidth:120,required:true},
		{ type: "input" , name:"AdslPhone", label:"AdslPhone :", validate:"", labelAlign:"left", maxLength:16,inputWidth:120, required:true, info:"true"},
		{ type: "input" , name:"NationalCode", label:"NationalCode :", validate:"",value:"", maxLength:10,inputWidth:120, info:"true"},
		{ type: "input" , name:"Name", label:"نام :", validate:"",value:"", maxLength:32,inputWidth:120, info:"true"},
		{ type: "input" , name:"Family", label:"Family :", validate:"",value:"", maxLength:32,inputWidth:120, info:"true"},
		{ type: "input" , name:"BirthDate", label:"BirthDate :", validate:"",value:"", maxLength:10,inputWidth:120, info:"true"},
		{ type: "input" , name:"Organization", label:"Organization :", validate:"",value:"", maxLength:64,inputWidth:120, info:"true"},
		{ type: "input" , name:"Phone", label:"Phone :", validate:"",value:"", maxLength:32,inputWidth:120, info:"true"},
		{ type: "input" , name:"Mobile", label:"Mobile :", validate:"",value:"", maxLength:15,inputWidth:120, info:"true"},
		{ type: "input" , name:"Address", label:"Address :", validate:"",value:"", maxLength:255,inputWidth:120, info:"true"},
		{ type: "input" , name:"Comment", label:"Comment :", validate:"",value:"", maxLength:255,inputWidth:120, info:"true"},
		{ type: "input" , name:"NOE", label:"NOE :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"}
	];

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// Toolbar1   ===================================================================
	var Toolbar1 = dhxLayout.attachToolbar();
	DSToolbarInitial(Toolbar1);
	DSToolbarAddButton(Toolbar1,null,"Retrieve","Retrieve","tof_"+DataTitle+"_Load",Toolbar1_OnRetrieveClick);
	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	
	// Form1   ===================================================================
	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	LoadForm(Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,DataName+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
//myForm.setFocusOnFirstActive();

//FUNCTION========================================================================================================================
//================================================================================================================================
function Toolbar1_OnRetrieveClick(){
	LoadForm(Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,DataName+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}	
function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		DSFormUpdateRequestProgress(dhxLayout,Form1,DataName+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
	}
}

function Form1OnButtonClick(){
}


function Form1DoAfterLoadOk(){
}	


function Form1DoAfterLoadFail(){
 dhtmlx.message({title: "هشدار",type: "alert-warning",text: "اطلاعات سطر بازگذاری نشد!منقضی شدن نشست واتصال خود به دلتاسیب را بررسی کنید"})
}	

function Form1DoAfterUpdateOk(){
	LoadForm(Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,DataName+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}
function Form1DoAfterUpdateFail(){
}
function Form1DoAfterInsertOk(r){
	LoadForm(Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,DataName+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
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