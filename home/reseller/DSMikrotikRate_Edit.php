<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSTimeRateEdit ....................................................................................");
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
	var DataTitle="سرعت میکروتیک";
	var DataName="DSMikrotikRate_";
	var ChangeLogDataName='MikrotikRate';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSMikrotikRate_Edit","70","Admin.MikrotikRate.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.MikrotikRate.ChangeLog.List","ChangeLogDataName=MikrotikRate"]
					];
	//=======Form1 MikrotikRate Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={MikrotikRateName:'انگلیسی،۳۲ کاراکتر',
							ParentName:'Parent queue name(it will be create if not exist)',
							Parent_MikrotikRateValue_Id:'(فعال شده تاثیر دارد SSH فقط روی میکروتیک با حالت) محدودیت و اولویت بندی پهنای باند والد'
							};
	var Form1FieldHelpId=["MikrotikRateName","ParentName","Parent_MikrotikRateValue_Id",
						"H1_Id","H2_Id","H3_Id","H4_Id","H5_Id","H6_Id","H7_Id","H8_Id","H9_Id","H10_Id","H11_Id","H12_Id",
						"H13_Id","H14_Id","H15_Id","H16_Id","H17_Id","H18_Id","H19_Id","H20_Id","H21_Id","H22_Id","H23_Id","H24_Id"];
	var Form1TitleField="MikrotikRateName";
	var Form1DisableItems=["MikrotikRateName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="UsernamePattern";

	var Form1Str = [
		{ type: "label"},
		{ type:"hidden" , name:"Error", label:"خطا :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"hidden" , name:"MikrotikRate_Id", label:"MikrotikRate_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75},
			{ type:"input" , name:"MikrotikRateName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:150},
			{type: "newcolumn", offset:48},
			{type: "select", style:"color:navy;border:1px solid gray", name:"Parent_MikrotikRateValue_Id",label: "والد",connector: "DSMikrotikRate_EditRender.php?act=SelectParentMikrotikRateValue", inputWidth:225, info:"true"},
			{type:"newcolumn"},
			{type:"button", name:"PasteAll", value:"جایگذاری همه", width :85,hidden:true},
			{ type: "hidden", name:"PPPOE", label: "PPPOE :",inputWidth:80, options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}]},
			{ type: "hidden", name:"VPN", label: "VPN :",inputWidth:80, options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}]},
			{ type: "hidden", name:"HOTSPOT", label: "HOTSPOT :",inputWidth:80, options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}]}
		]},
		{ type: "label"},
		{ type: "label"},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H1_Id",label: "۰-۱ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy1", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste1", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H13_Id",label: "۱۲-۱۳ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy13", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste13", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H2_Id",label: "۱-۲ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy2", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste2", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H14_Id",label: "۱۳-۱۴ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy14", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste14", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H3_Id",label: "۲-۳ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy3", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste3", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H15_Id",label: "۱۴-۱۵ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy15", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste15", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H4_Id",label: "۳-۴ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy4", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste4", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H16_Id",label: "۱۵-۱۶ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy16", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste16", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H5_Id",label: "۴-۵ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy5", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste5", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H17_Id",label: "۱۶-۱۷ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy17", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste17", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H6_Id",label: "۵-۶ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy6", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste6", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H18_Id",label: "۱۷-۱۸ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy18", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste18", value:"جایگذاری", width :55, disabled: true}
		]},
		{ type: "label"},
		{ type: "label"},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H7_Id",label: "۶-۷ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy7", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste7", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H19_Id",label: "۱۸-۱۹ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy19", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste19", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H8_Id",label: "۷-۸ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy8", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste8", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H20_Id",label: "۱۹-۲۰ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy20", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste20", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H9_Id",label: "۸-۹ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy9", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste9", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H21_Id",label: "۲۰-۲۱ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy21", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste21", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H10_Id",label: "۹-۱۰ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy10", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste10", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H22_Id",label: "۲۱-۲۲ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy22", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste22", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H11_Id",label: "۱۰-۱۱ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy11", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste11", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H23_Id",label: "۲۲-۲۳ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy23", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste23", value:"جایگذاری", width :55, disabled: true}
		]},
		{type: "block", width: 720, list:[
			{type:"settings" , labelWidth:75, inputWidth:150},
			{type: "select", name:"H12_Id",label: "۱۱-۱۲ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy12", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste12", value:"جایگذاری", width :55, disabled: true},
			{type: "newcolumn", offset:35},
			{type: "select", name:"H24_Id",label: "۲۳-۲۴ :",connector: "DSMikrotikRate_EditRender.php?act=SelectMikrotikRateValue",validate:'IsID',required:true},
			{type:"newcolumn"},
			{type:"button", name:"Copy24", value:"کپی", width :35},
			{type:"newcolumn"},
			{type:"button", name:"Paste24", value:"جایگذاری", width :55, disabled: true}
		]},		
		
	];
	
	var PermitView=ISPermit("Admin.User.MikrotikRate.View");
	var PermitAdd=ISPermit("Admin.User.MikrotikRate.Add");
	var PermitEdit=ISPermit("Admin.User.MikrotikRate.Edit");
	var PermitDelete=ISPermit("Admin.User.MikrotikRate.Delete");

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
	if(name.substr(0,4)=="Copy"){
		var MyId=name.substr(4);
		Form1.updateValues();
		var Rate_Id=Form1.getItemValue("H"+MyId+"_Id");
		if(Rate_Id==0){
			dhtmlx.message({text:"Can not copy this rate...", type:"error"});
			return;
		}
		Form1.setItemValue("CopyPaste",Rate_Id);
		FormEnableItem(Form1,["Paste1","Paste2","Paste3","Paste4","Paste5","Paste6","Paste7","Paste8","Paste9","Paste10","Paste11","Paste12","Paste13","Paste14","Paste15","Paste16","Paste17","Paste18","Paste19","Paste20","Paste21","Paste22","Paste23","Paste24"]);
		Form1.showItem("CopyPaste");
		Form1.showItem("PasteAll");
	}
	else if(name=="PasteAll"){
		var NewValue=Form1.getItemValue("CopyPaste");
		var Counter=0;
		for(var i=1;i<=24;++i)
			if(Form1.getItemValue("H"+i+"_Id")!=NewValue){
				++Counter;
				Form1.setItemValue("H"+i+"_Id",NewValue);
			}
		dhtmlx.message(Counter+" مورد تغییر کرد");
	}
	else if(name.substr(0,5)=="Paste"){
		var MyId=name.substr(5);
		var NewValue=Form1.getItemValue("CopyPaste");
		if(Form1.getItemValue("H"+MyId+"_Id")!=NewValue)
			Form1.setItemValue("H"+MyId+"_Id",Form1.getItemValue("CopyPaste"));
		else
			dhtmlx.message("Already has the same...");
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
//k=f_Form.getItemValue("Error");
	parent.dhxLayout.dhxWins.window("popupWindow").close();
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
