<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSNasEdit ....................................................................................");
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
	var DataTitle="درگاه پرداخت";
	var DataName="DSTerminal_";
	var ChangeLogDataName='Terminal';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSTerminal_Edit","70","Admin.BankTerminal.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.BankTerminal.ChangeLog.List","ChangeLogDataName=Terminal"],
					];
	//=======Form1 Terminal Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	TerminalName:'نام درگاه بانک. در پنل کاربری نمایش داده خواهد شد'};
	var Form1FieldHelpId=["ISEnable","TerminalName","BankName","Mellat_TerminalNo","Mellat_Username","Mellat_Password","Saman_MerchantID","Saman_MerchantPassword","Refah_MerchantID","Refah_MerchantPassword","HomeUrl","CallbackUrl","Melli_MerchandID","Melli_TerminalID",'Melli_TerminalType',"Melli_TransactionKey","Melli_PaymentIdentity","Tejarat_MerchantID","Tejarat_AccountNo","Saderat_MID","Saderat_TID","Saderat_PrivateKeys","Saderat_PublicKeys","Jahanpay_Api","Jahanpay_Username","Jahanpay_Password","ZarinPal_MerchantId","AP_MerchantID","AP_MerchantConfigID","AP_UserName","AP_Password","AP_EncryptionKey","AP_EncryptionVector"];
	
	var Form1TitleField="TerminalName";
	var Form1DisableItems=['BankName'];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="TerminalName";

	var Form1Str = [
		{ type:"settings" , labelWidth:150, inputWidth:340,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"Terminal_Id", label:"Terminal_Id :",disabled:true, labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:198,required:true},
		{ type:"input" , name:"TerminalName", label:"نام درگاه :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", info:true, inputWidth:200},
		{ type: "hidden" , name:"HomeUrl", label:"مسیر اولیه :",maxLength:128, validate:"", labelAlign:"left", inputWidth:300,note: { text: "Leave blank or enter for example :http://users.myisp.com:99/"}},
		{ type: "input" , name:"CallbackUrl", label:"مسیر برگشت :",maxLength:128, validate:"", labelAlign:"left", inputWidth:300,note: { text: "http://users.myisp.com:99/ : خالی بگذارید یا مانند نمونه وارد کنید"}},
		{ type: "select", name:"BankName", label: "نام بانک :", options:[
			{text: "ملت", value: "Mellat",selected: true,list:[
				{ type:"input" , name:"Mellat_TerminalNo", label:"شماره ترمینال :",maxLength:32, validate:"NotEmpty", labelAlign:"left", inputWidth:200,required:true},
				{ type: "input" , name:"Mellat_Username", label:"نام کاربری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:16,inputWidth:200,required:true},
				{ type: "input" , name:"Mellat_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:16,inputWidth:200,required:true},
			]},
			{text: "سامان", value: "Saman",list:[
				{ type:"input" , name:"Saman_MerchantID", label:"شناسه پذیرنده :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200,required:true},
				{ type: "input" , name:"Saman_MerchantPassword", label:"کلمه عبور :", validate:"",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
			]},
			{text: "رفاه", value: "Refah",list:[
				{ type:"input" , name:"Refah_MerchantID", label:"شناسه پذیرنده :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200,required:true},
				{ type: "input" , name:"Refah_MerchantPassword", label:"کلمه عبور :", validate:"",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
			]},
			{text: "ملی", value: "Melli",list:[
				{ type: "select", name:"Melli_TerminalType", label: "نوع درگاه :", options:[{text: "نوع ۱", value: "Type1",selected: true},{text: "نوع ۲", value: "Type2"},{text: "نوع ۳", value: "Type3"}],inputWidth:198,required:true},
				{ type: "input" , name:"Melli_TerminalID", label:"شناسه درگاه :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type:"input" , name:"Melli_MerchandID", label:"شناسه پذیرنده :",maxLength:32, validate:"NotEmpty", labelAlign:"left", inputWidth:200,required:true},
				{ type: "input" , name:"Melli_TransactionKey", label:"کلید تراکنش :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:300,required:true},
				{ type: "input" , name:"Melli_PaymentIdentity", label:"هویت پرداخت :", validate:"",value:"", labelAlign:"left", maxLength:30,inputWidth:300,required:false},

			]},
			{text: "تجارت", value: "Tejarat",list:[
				{ type: "input" , name:"Tejarat_MerchantID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type: "input" , name:"Tejarat_AccountNo", label:"شماره حساب :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,note:{text:"Don't enter 'No' prefix"},required:true},
			]},
			{text: "صادرات", value: "Saderat",list:[
				{ type: "input" , name:"Saderat_MID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:15,inputWidth:200,required:true},
				{ type: "input" , name:"Saderat_TID", label:"شناسه درگاه :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:200,required:true},
				{ type: "input" , name:"Saderat_PrivateKeys", label:"کلید اختصاصی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:1024,inputWidth:450,rows:7,required:true},
				{ type: "input" , name:"Saderat_PublicKeys", label:"کلید عمومی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:1024,inputWidth:450,rows:7,required:true},
			]},
			{text: "توسن", value: "TOSAN",list:[
				{ type: "input" , name:"TOSAN_Username", label:"نام کاربری(MID) :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:200,required:true},
				{ type: "input" , name:"TOSAN_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:16,inputWidth:200,required:true},
				{ type: "input" , name:"TOSAN_TID", label:"شناسه درگاه :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:200,required:true},
				{ type: "input" , name:"TOSAN_MID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:200,required:true},
				{ type: "input" , name:"TOSAN_goodReferenceId", label:"شناسه مرجع :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:30,inputWidth:200,required:true},
			]},
			{text: "جهان پی", value: "Jahanpay",list:[
				{ type: "input" , name:"Jahanpay_Api", label:"رابط برنامه نویسی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type:"input" , name:"Jahanpay_Username", label:"نام کاربری :",maxLength:32, validate:"NotEmpty", labelAlign:"left", inputWidth:200,required:true},
				{ type: "input" , name:"Jahanpay_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
			]},
			{text: "زرین پال", value: "ZarinPal",list:[
				{ type: "input" , name:"ZarinPal_MerchantId", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:36,inputWidth:300,required:true}
			]},
			{text: "آسان پرداخت", value: "AsanPardakht",list:[
				{ type: "input" , name:"AP_MerchantID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type: "input" , name:"AP_MerchantConfigID", label:"شناسه پیکربندی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type: "input" , name:"AP_UserName", label:"نام کاربری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type: "input" , name:"AP_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
				{ type: "input" , name:"AP_EncryptionKey", label:"کلید رمزگذاری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:64,inputWidth:350,required:true},
				{ type: "input" , name:"AP_EncryptionVector", label:"وکتور رمزگذاری :", validate:"NotEmpty",value:"", labelAlign:"left",maxLength:64,inputWidth:350,required:true},
			]},
			],inputWidth:198,required:true},
		];

	var Form1StrEdit = [
		{ type: "settings" , labelWidth:150, inputWidth:340,offsetLeft:10  },
		{ type: "label"},
		{ type: "hidden" , name:"Terminal_Id", label:"Terminal_Id :",disabled:true, labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:198,required:true},
		{ type: "input" , name:"TerminalName", label:"نام درگاه :",maxLength:128, validate:"NotEmpty",required:true, labelAlign:"left", inputWidth:200},
		{ type: "hidden" , name:"HomeUrl", label:"HomeUrl :",maxLength:128, validate:"", labelAlign:"left", inputWidth:300,note: { text: "Leave blank or enter for example :http://users.myisp.com:99/"}},
		{ type: "input" , name:"CallbackUrl", label:"مسیر برگشت :",maxLength:128, validate:"", labelAlign:"left", inputWidth:300,note: { text: "http://users.myisp.com:99/ : خالی بگذارید یا مانند نمونه وارد کنید"}},
		{ type: "input", name:"BankName", label: "نام بانک :",inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Mellat_TerminalNo", label:"شماره ترمینال :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Mellat_Username", label:"نام کاربری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:16,inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Mellat_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:16,inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Saman_MerchantID", label:"شناسه پذیرنده :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Saman_MerchantPassword", label:"کلمه عبور :", validate:"",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Refah_MerchantID", label:"شناسه پذیرنده :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200,required:true},
		{hidden:true,  type: "input" , name:"Refah_MerchantPassword", label:"کلمه عبور :", validate:"",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "select", name:"Melli_TerminalType", label: "نوع درگاه :", options:[{text: "نوع ۱", value: "Type1",selected: true},{text: "نوع ۲", value: "Type2"},{text: "نوع ۳", value: "Type3"}],inputWidth:198,required:true},
		{hidden:true, type: "input" , name:"Melli_TerminalID", label:"شناسه درگاه :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type:"input" , name:"Melli_MerchandID", label:"شناسه پذیرنده :",maxLength:32, validate:"NotEmpty", labelAlign:"left", inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"Melli_TransactionKey", label:"کلید تراکنش :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:300,required:true},
		{hidden:true, type: "input" , name:"Melli_PaymentIdentity", label:"هویت پرداخت :", validate:"",value:"", labelAlign:"left", maxLength:32,inputWidth:300,required:false},

		{hidden:true, type: "input" , name:"Tejarat_MerchantID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"Tejarat_AccountNo", label:"شماره حساب :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,note:{text:"Don't enter 'No' prefix"},required:true},
		{hidden:true, type: "input" , name:"Saderat_MID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:15,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"Saderat_TID", label:"شناسه درگاه :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"Saderat_PrivateKeys", label:"کلید اختصاصی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:1024,inputWidth:500,rows:7,required:true},
		{hidden:true, type: "input" , name:"Saderat_PublicKeys", label:"کلید عمومی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:1024,inputWidth:500,rows:7,required:true},
		{hidden:true, type: "input" , name:"Jahanpay_Api", label:"رابط برنامه نویسی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"Jahanpay_Username", label:"نام کاربری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type:"input" , name:"Jahanpay_Password", label:"کلمه عبور :",maxLength:32, validate:"NotEmpty", labelAlign:"left", inputWidth:200,required:true},
		{hidden:true, type:"input" , name:"ZarinPal_MerchantId", label:"شناسه پذیرنده :",maxLength:36, validate:"NotEmpty", labelAlign:"left", inputWidth:300,required:true},
		{hidden:true, type: "input" , name:"AP_MerchantID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"AP_MerchantConfigID", label:"شناسه پیکربندی :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"AP_UserName", label:"نام کاربری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"AP_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"AP_EncryptionKey", label:"کلید رمزگذاری :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:64,inputWidth:350,required:true},
		{hidden:true, type: "input" , name:"AP_EncryptionVector", label:"وکتور رمزگذاری :", validate:"NotEmpty",value:"", labelAlign:"left",maxLength:64,inputWidth:350,required:true},
		{hidden:true, type: "input" , name:"TOSAN_Username", label:"نام کاربری(MID) :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"TOSAN_Password", label:"کلمه عبور :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:32,inputWidth:200,required:true},
		{hidden:true, type: "input" , name:"TOSAN_TID", label:"شناسه درگاه :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:350,required:true},
		{hidden:true, type: "input" , name:"TOSAN_MID", label:"شناسه پذیرنده :", validate:"NotEmpty",value:"", labelAlign:"left", maxLength:8,inputWidth:350,required:true},
		{hidden:true, type: "input" , name:"TOSAN_goodReferenceId", label:"شناسه مرجع :", validate:"NotEmpty",value:"", labelAlign:"left",maxLength:30,inputWidth:350,required:true},
		];
	var PermitView=ISPermit("Admin.BankTerminal.View");
	var PermitAdd=ISPermit("Admin.BankTerminal.Add");
	var PermitEdit=ISPermit("Admin.BankTerminal.Edit");
	var PermitDelete=ISPermit("Admin.BankTerminal.Delete");

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
		Form1=DSInitialForm(TabbarMain.cells(0),Form1StrEdit,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
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
	var BankName=Form1.getItemValue('BankName');
	if(BankName=='Mellat'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Refah_MerchantID','Refah_MerchantPassword','Saman_MerchantID','Saman_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','ZarinPal_MerchantId','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Mellat_TerminalNo','Mellat_Username','Mellat_Password']);
	}
	else if(BankName=='Saman'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','ZarinPal_MerchantId','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Saman_MerchantID','Saman_MerchantPassword']);
	}
	else if(BankName=='Refah'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','ZarinPal_MerchantId','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Refah_MerchantID','Refah_MerchantPassword']);
	}
	else if(BankName=='Melli'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','ZarinPal_MerchantId','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity']);
	}
	else if(BankName=='Tejarat'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','ZarinPal_MerchantId','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Tejarat_MerchantID','Tejarat_AccountNo']);
	}
	else if(BankName=='Saderat'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','ZarinPal_MerchantId','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys']);
	}
	else if(BankName=='Jahanpay'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','ZarinPal_MerchantId','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['Jahanpay_Api','Jahanpay_Username','Jahanpay_Password']);
	}
	else if(BankName=='ZarinPal'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
		FormShowItem(Form1,['ZarinPal_MerchantId']);
	}
	else if(BankName=='AsanPardakht'){
		FormRemoveItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','ZarinPal_MerchantId']);
		FormShowItem(Form1,['AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector']);
	}
	else if(BankName=='TOSAN'){
		FormRemoveItem(Form1,['AP_MerchantID','AP_MerchantConfigID','AP_UserName','AP_Password','AP_EncryptionKey','AP_EncryptionVector','Mellat_TerminalNo','Mellat_Username','Mellat_Password','Saman_MerchantID','Saman_MerchantPassword','Refah_MerchantID','Refah_MerchantPassword','Melli_MerchandID','Melli_TerminalID','Melli_TerminalType','Melli_TransactionKey','Melli_PaymentIdentity','Tejarat_MerchantID','Tejarat_AccountNo','Jahanpay_Api','Jahanpay_Username','Jahanpay_Password','Saderat_MID','Saderat_TID','Saderat_PrivateKeys','Saderat_PublicKeys','ZarinPal_MerchantId']);
		FormShowItem(Form1,['TOSAN_Username','TOSAN_Password','TOSAN_MID','TOSAN_TID','TOSAN_goodReferenceId']);
	}
	
	
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
	Form1=DSInitialForm(TabbarMain.cells(0),Form1StrEdit,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
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