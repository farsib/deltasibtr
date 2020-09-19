<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSActiveDirectoryEdit ....................................................................................");
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
	var DataTitle="اکتیودایرکتوری";
	var DataName="DSActiveDirectory_";
	var ChangeLogDataName='ActiveDirectory';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSActiveDirectory_Edit","70","Admin.ActiveDirectory.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.ActiveDirectory.ChangeLog.List","ChangeLogDataName=ActiveDirectory"],
					];
	//=======Form1 ActiveDirectory Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	ActiveDirectoryName:'Name of bank ActiveDirectory'};
	var Form1FieldHelpId=["ActiveDirectoryName","IP","GroupName","BaseDN","BindDN","Filter","Timeout"];
	var Form1TitleField="ActiveDirectoryName";
	var Form1DisableItems=["ActiveDirectoryName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="ActiveDirectoryName";

	
	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"ActiveDirectory_Id", label:"ActiveDirectory_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال:", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"ActiveDirectoryName", label:"نام اکتیودایرکتوری :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", info:"true", inputWidth:100},
		{ type: "input" , name:"Domain", label:"دامنه :", validate:"NotEmpty",required:true,value:"", labelAlign:"left", maxLength:32,inputWidth:100},
		{ type: "input" , name:"IP", label:"آی پی :", validate:"NotEmpty,ValidIPv4",required:true,value:"", labelAlign:"left", maxLength:15,inputWidth:100},
		{ type: "input" , name:"GroupName", label:"نام گروه :", validate:"",required:false,value:"", labelAlign:"left", maxLength:64,inputWidth:100},
		{ type: "input" , name:"Timeout", label:"اتمام مهلت :", validate:"^([1-5])$",required:true,value:3, labelAlign:"left", maxLength:128,inputWidth:300},
		{ type: "label"},
		{ type: "label"},
		{ type: "label"},
		{type: "block", width: 600, list:[
			{ type: "button",name:"FillExample1",value: "مثال ۱",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"FillExample2",value: "مثال ۲",width :80},
			{type: "newcolumn", offset:20}
		]}	
		];
	//=======Popup2 TestAuth
	var Popup2;
	var PopupId2=['TestAuth'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 TestAuth
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Password:'Char max length 16'};
	var Form2FieldHelpId=['Password'];
	var Form2Str = [
		{type: "fieldset", width: 250, label: "تست اتصال", list:[
			{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
			{ type:"input" ,name:"Username", label:"نام کاربری :", maxLength:32, validate:"NotEmpty",inputWidth:90, required:true,focus:true },
			{ type:"input" ,name:"Password", label:"کلمه عبور :", validate:"NotEmpty", maxLength:16,inputWidth:90, required:true }
		]},
		{type: "block", width: 300, offsetLeft:30,list:[
			{ type:"button",name:"Ldap",value: "Ldap",width :60},
			{type: "newcolumn", offset:20},
			{ type:"button",name:"Ntlm_auth",value: "Ntlm_auth",width :60},
			{type: "newcolumn", offset:20},
			{ type:"button" , name:"Close", value:"بستن", width:60}
		]}
	];

	var PermitView=ISPermit("Admin.ActiveDirectory.View");
	var PermitAdd=ISPermit("Admin.ActiveDirectory.Add");
	var PermitEdit=ISPermit("Admin.ActiveDirectory.Edit");
	var PermitDelete=ISPermit("Admin.ActiveDirectory.Delete");

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);
	AddPopupTestAuth();

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
function AddPopupTestAuth(){//TestAuth
	DSToolbarAddButtonPopup(TopToolbar,null,"TestAuth","تست اتصال","tow_ReLogin");
	Popup2=DSInitialPopup(TopToolbar,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	
	Form2.attachEvent("onEnter", function(){Form2OnButtonClick('Ldap')});
}
function Form2OnButtonClick(name){//TestAuth
	if(name=='Close') Popup2.hide();
	else if(name=='Ldap'){
		Form2.updateValues();
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Popup2.hide();
			dhxLayout.progressOn();
			Form2.send(TabbarMainArray[0][1]+"Render.php?act=TestAuth&Method=Old&RowId="+RowId+"&"+un(), function(loader, response) {
				dhxLayout.progressOff();
				response=CleanError(response);
				var responsearray=response.split("~",8);
				var mapObj = {
				"error, can't contact ldap server":"خطا،امکان اتصال به سرور وجود ندارد"
			};
				parent.dhtmlx.alert({text:response.replace("error, can't contact ldap server", function(matched){
				return mapObj[matched];
				}),ok:"بستن"});
				
			});
		}
	}
	else if(name=='Ntlm_auth'){
		Form2.updateValues();
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Popup2.hide();
			dhxLayout.progressOn();
			Form2.send(TabbarMainArray[0][1]+"Render.php?act=TestAuth&Method=New&RowId="+RowId+"&"+un(), function(loader, response) {
				dhxLayout.progressOff();
				response=CleanError(response);
				var responsearray=response.split("~",8);
				var mapObj = {
				"":"خطا"					
			};
				parent.dhtmlx.alert({text:response.replace("", function(matched){
				return mapObj[matched];
				}),ok:"بستن"});
				
			});
		}
	}
}
	
	

function Popup2OnShow(){//TestAuth
	//Form2.setItemValue("Username",'');
	//Form2.setItemValue("Password",'');
	Form2.setItemFocus("Username");
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


function Form1OnButtonClick(name){
	if(name=='FillExample1'){
		Form1.setItemValue('GroupName','Internet');
		Form1.setItemValue('Domain','payam.com');
	}	
	else if(name=='FillExample2'){
		Form1.setItemValue('GroupName','Internet');
		Form1.setItemValue('Domain','birjand.ac.ir');
	}	

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