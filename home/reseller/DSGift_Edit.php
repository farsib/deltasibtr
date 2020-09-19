<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSGift_Edit ....................................................................................");
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
			margin: 0px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background-color:white;
        }
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="هدیه";
	var DataName="DSGift_";
	var ChangeLogDataName='Gift';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSGift_Edit","70","Admin.User.Gift.Edit",""],
					["سرویس ها","DSGift_Services_List","80","Admin.User.Gift.Services.List",""],
					["پیوست شده به سرویس","DSGift_ServicesAttached_List","140","Admin.User.Gift.Services.List",""],
					["کاربران","DSGift_Users_List","80","Admin.User.Gift.Users.List",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.Gift.ChangeLog.List","ChangeLogDataName=Gift"]
	];
	//=======Form1 Gift Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={
					GiftISEnable:"Not enabled gift will not list in the service gift tab. Previous added gift in both service and users will add for users",
					GiftName:"Name of gift. Up to 64 character",
					GiftMode:"در حالت روز ثابت -هدیه ساعت ۲۴ روز فعال سازی خاتمه می یابد",
					GiftDurationDays:"تعداد روز",
					GiftTrafficRate:"عدد بین ۰ تا ۱",
					GiftTimeRate:"عدد بین ۰ تا ۱",
					GiftExtraTr:"Gift extra traffic for user to use within gift active time",
					GiftStopOnTrFinish:"Gift will stop after Extra Traffic finish",
					GiftExtraTi:"Gift extra time for user to use within gift active time",
					GiftExpirationDays:"تعداد روز پس از فعال سازی هدیه<br/>اگر ۰ وارد شود،هدیه سرویس پایه بعد از اتمام سرویس خاتمه می یابد و هدیه سرویس اضافی و سرویس آی پی و سرویس سایر هرگز منقضی نمیشود",
					GiftMikrotikRate_Id:"Mikrotike rate of gift.<br/>If you select ' -- Do not change MikrotikRate of base service -- '<br/>mikrotik rate of user will be unchanged"
	};
	var Form1FieldHelpId=["GiftISEnable","GiftName","GiftMode","GiftDurationDays","GiftTrafficRate","GiftTimeRate","GiftExtraTr","GiftStopOnTrFinish","GiftExtraTi","GiftExpirationDays","GiftMikrotikRate_Id"];
	var Form1TitleField="GiftName";
	var Form1DisableItems=["GiftExpirationDays","GiftDurationDays","GiftTrafficRate","GiftTimeRate","GiftExtraTr","GiftStopOnTrFinish","GiftExtraTi"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="GiftName";

	var Form1Str = [
		{ type:"settings" , labelWidth:140, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"Gift_Id", label:"Gift_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"GiftName", label:"نام هدیه :",maxLength:64, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:302},
		{ type: "select", name:"GiftISEnable", label: "فعال؟ :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:98,required:true,info:false},
		{ type: "select", name:"GiftMode", label: "حالت هدیه :", options:[{text: "چند روز", value: "MultiDay",selected: true},{text: "روز ثابت", value: "FixedDay"}],inputWidth:98,required:true,info:true},
		{ type:"input" , name:"GiftDurationDays", label:"مدت زمان هدیه :",maxLength:4, validate:"NotEmpty,ValidInteger",value:"1", required:true, labelAlign:"left", inputWidth:100,info:true},
		{ type:"input" , name:"GiftExpirationDays", label:"مدت زمان انقضا :",maxLength:4, validate:"NotEmpty,ValidInteger",value:"999", required:true, labelAlign:"left", inputWidth:100,info:true},
		{ type: "input" , name:"GiftTrafficRate", label:"ضریب ترافیک :", validate:"NotEmpty", value: "1", labelAlign:"left", maxLength:4,inputWidth:100,required:true,info:true},
		{ type: "input" , name:"GiftTimeRate", label:"ضریب زمان :", validate:"NotEmpty", value: "1",labelAlign:"left", maxLength:4,inputWidth:100,required:true,info:true},
		{ type: "input" , name:"GiftExtraTr", label:"ترافیک هدیه (مگابایت) :", validate:"NotEmpty", value: "0",labelAlign:"left", maxLength:6,inputWidth:100,required:true,info:false},
		{ type: "select", name:"GiftStopOnTrFinish", label: "خاتمه هدیه پس از اتمام ترافیک :", options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}],inputWidth:98,required:true,info:false,disabled:true},
		{ type: "input" , name:"GiftExtraTi", label:"زمان اضافی هدیه(ثانیه) :", validate:"NotEmpty", value: "0",labelAlign:"left", maxLength:6,inputWidth:100,required:true,info:false},
		{ type: "select", name:"GiftMikrotikRate_Id",label: "سرعت کاربر زمان استفاده از هدیه :",connector: DataName+"EditRender.php?act=SelectMikrotikRate",validate:'IsID',required:true,inputWidth:300,info:false},
	];

	var PermitView=ISPermit("Admin.User.Gift.View");
	var PermitAdd=ISPermit("Admin.User.Gift.Add");
	var PermitEdit=ISPermit("Admin.User.Gift.Edit");


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
	Form1.attachEvent("onBeforeChange",Form1OnBeforChange);
	Form1.attachEvent("onInputChange",Form1OnInputChange);
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
		Form1.attachEvent("onChange",Form1OnChange);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnInputChange(name,value){
	if(name=="GiftExtraTr"){
		if(value>0)
			Form1.enableItem("GiftStopOnTrFinish");
		else{
			Form1.disableItem("GiftStopOnTrFinish");
			Form1.setItemValue("GiftStopOnTrFinish","No");
		}
	}
}

function Form1OnBeforChange(name,value,newvalue){
	if(name=="GiftMode"){
		if((newvalue=="FixedDay")&&(Form1.getItemValue("GiftDurationDays")>1)){
			dhtmlx.alert("You cannot set GiftMode to FixedDay whilst Duration Days is greater than 1");
			return false;
		}
	}
	return true;
}
function Form1OnChange(name,value){
	// dhtmlx.message("name="+name+"<br/>value="+value);
	if(name=="GiftMode"){
		if(value=="MultiDay")
			Form1.enableItem("GiftDurationDays");
		else
			Form1.disableItem("GiftDurationDays");
	}
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
