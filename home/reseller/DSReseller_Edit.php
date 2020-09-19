<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSResellerEdit ....................................................................................");
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
    <script src="../js/sha512.js" type="text/javascript"></script>
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
// var PermitBuffer=parent.PermitBuffer;
window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="نماینده یا اپراتور";
	var DataName="DSReseller_";
	var ChangeLogDataName='Reseller';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSReseller_Edit","70","CRM.Reseller.Edit",""],
					["مجوزها","DSReseller_Permit_List","100","CRM.Reseller.Permit.List",""],
					["پارامتر","DSParam_List","80","CRM.Reseller.Param.List","ParamItemGroup=Reseller"],
					["دسترسی به بسته","DSReseller_PackageAccess_List","125","CRM.Reseller.PackageAccess.List",""],
					["دسترسی به درگاه بانک","DSReseller_TerminalAccess_List","125","CRM.Reseller.TerminalAccess.List",""],
					["اعتبار","DSReseller_Credit_List","80","CRM.Reseller.Credit.List",""],
					["پرداخت","DSReseller_Payment_List","80","CRM.Reseller.Payment.List",""],
					["تراکنش","DSReseller_Transaction_List","100","CRM.Reseller.Transaction.List",""],
					["تاریخچه وب","DSReseller_WebHistory_List","100","CRM.Reseller.WebHistory.List",""],
					["لیست تغییرات","DSChangeLog","95","CRM.Reseller.ChangeLog.List","ChangeLogDataName=Reseller"]
					];
	//=======Form1 Reseller Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	ResellerName:'فقط کاراکترهای انگلیسی و اعداد بدون فاصله،حداکثر ۳۲ کاراکتر',
							Mobile:'فقط یک شماره موبایل',
							PermitIP:'آی پی/شبکه مانند: 192.168.5.2/32,172.16.2.0/24',
							NoneBlockIP:'آی پی/شبکه مانند: 192.168.5.2/32,172.16.2.0/24<br/>مقدار پیش فرض : 127.0.0.1/32',
							SessionTimeout:'تعداد ثانیه برای پایان نشست نماینده فروش در صورت عدم فعالیت'
						 };
	var Form1FieldHelpId=["ResellerName","Mobile",'NoneBlockIP','PermitIP','SessionTimeout','SharePercent'];
	var Form1TitleField="ResellerName";
	var Form1DisableItems=["ResellerName","ParentReseller_Id"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=['ResellerCDT','LastLoginDT','LastLoginIP'];
	var Form1AdminItems=[];
	var Form1FocusItemAdd="Name";

	var Form1Str =[
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خظا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"Reseller_Id", label:"شناسه نماینده فروش :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"ResellerName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENNameDot",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"ResellerCDT", label:"زمان ایجاد نماینده فروش :",disabled:"true", labelAlign:"left", hidden:true, inputWidth:150},
		// { type:"input" , name:"LastLoginDT", label:"LastLoginDT :",disabled:"true", labelAlign:"left", hidden:true, inputWidth:150},
		// { type:"input" , name:"LastLoginIP", label:"LastLoginIP :",disabled:"true", labelAlign:"left", hidden:true, inputWidth:150},
		{ type:"select", name:"ParentReseller_Id",label: "نماینده فروش/اپراتور اصلی :",validate:"IsID",connector: TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SelectParentReseller&id="+RowId,required:true,inputWidth:198},
		{ type:"select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:100,required:true},
		{ type:"select", name:"ISOperator", label: "نوع :", options:[{text: "اپراتور", value: "Yes",selected: true},{text: "نماینده فروش", value: "No"}],inputWidth:100,required:true},
		{ type:"select", name:"ISManager", label: "مدیر :", options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}],inputWidth:100,required:true},
		{ type:"input" , name:"PermitIP", label:"آی پی مجاز :", value:"0.0.0.0/0",maxLength:255,validate:"ISPermitIp", labelAlign:"left", inputWidth:300, info:"true", required:true},
		{ type:"input" , name:"NoneBlockIP", label:"آی پی که مسدود نشود :", value:"127.0.0.1/32",maxLength:255,validate:"ISPermitIp", labelAlign:"left", inputWidth:300, info:"true", required:true},
		{ type:"input" , name:"SharePercent", label:"درصد پورسانت :",maxLength:3, value:"0", validate:"IsValidPercent", labelAlign:"left", inputWidth:100, required:true},
		{ type:"input" , name:"SessionTimeout", label:"زمان پایان نشست :",maxLength:8,  value:"600",validate:"NotEmpty,ValidInteger", labelAlign:"left", info:"true", inputWidth:100, required:true},
		{ type:"input" , name:"Name", label:"نام :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200},
		{ type:"input" , name:"Family", label:"نام خانوادگی :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200},
		{ type:"input" , name:"Mobile", label:"موبایل :", maxLength:15,validate:"", labelAlign:"left", info:"true", inputWidth:150},
		{ type:"input" , name:"Phone", label:"تلفن :", maxLength:100,validate:"", labelAlign:"left", inputWidth:300},
		{ type:"input" , name:"Address", label:"آدرس :",maxLength:255, rows:4 ,validate:"", labelAlign:"left", inputWidth:300}
		
		];

	//=======Popup2 ChangePass
	var Popup2;
	var PopupId2=['ChangePass'];// popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangePass
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {Pass:'رشته'};
	var Form2FieldHelpId=['Pass'];
	var Form2Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type:"password" , name:"Pass", label:"کلمه عبور :",validate:"", labelAlign:"left", info:"true", inputWidth:130, required:true},
		{ type:"hidden" , name:"enpass"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "ذخیره",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
		

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
		
	//alert('w->',PermitBuffer);

	
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
	
	MyHint = new dhtmlXPopup({form: Form1,id:["SessionTimeout"],mode:"right"});
	MyHint.attachEvent("onContentClick",function(){MyHint.hide()});
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	Form1.attachEvent("onFocus",function(name){
		if(name=="SessionTimeout")
			Form1OnInputChange("SessionTimeout",Form1.getItemValue("SessionTimeout"));
		else
			MyHint.hide();
	});	
	
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(ISPermit("CRM.Reseller.ChangePassword"))	AddPopupChangePass();
		if(ISPermit("CRM.Reseller.View"))	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(ISPermit("CRM.Reseller.Edit"))	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!ISPermit("CRM.Reseller.Edit"))	FormDisableAllItem(Form1);
	}
	else{
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(ISPermit("CRM.Reseller.Add"))		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnInputChange(name,value){
	if(name=="SessionTimeout"){
		if(value==parseInt(value)){
			MyHint.attachHTML("<div style='color:blue;text-align:center;font-weight:bold'>&nbsp;&nbsp;"+GetTimeString(value)+"&nbsp;&nbsp;</div>");
			MyHint.show("SessionTimeout");
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

function AddPopupChangePass(){
	DSToolbarAddButtonPopup(TopToolbar,null,"ChangePass","تغییر کلمه عبور","tow_ChangePass");
	Popup2=DSInitialPopup(TopToolbar,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Form2OnButtonClick(name){//ChangePass
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			var enpass=hex_sha512(Form2.getItemValue("Pass"));
			Form2.setItemValue("enpass", enpass);
			Form2.setItemValue("Pass", "*");
			DSFormUpdateRequestProgress(dhxLayout,Form2,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=ChangePass&id="+RowId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}
function Form2DoAfterUpdateOk(){
	Popup2.hide();
}

function Popup2OnShow(){//ChangePass
	Form2.setItemValue("Pass", "");
	Form2.setItemFocus("Pass");
}

function Form2DoAfterUpdateFail(){

}


function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update&id="+RowId,Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
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
	Form1OnInputChange("SessionTimeout",Form1.getItemValue("SessionTimeout"));
	var ResellerName = Form1.getItemValue("ResellerName");
	if(ResellerName=='admin'){
		Form1.disableItem('ISEnable');
		Form1.disableItem('ParentReseller_Id');
	}
	
}	

function Form1DoAfterLoadFail(){
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
	if(ISPermit("CRM.Reseller.ChangePassword"))
		AddPopupChangePass();
	if(ISPermit("CRM.Reseller.View"))
		DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!ISPermit("CRM.Reseller.Edit"))
		FormDisableAllItem(Form1);
	
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
