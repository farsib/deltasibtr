<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSSMSProviderEdit ....................................................................................");
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
	var DataTitle="ارائه دهنده پیام کوتاه";
	var DataName="DSSMSProvider_";
	var ChangeLogDataName='SMSProvider';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSSMSProvider_Edit","70","Admin.Message.SMSProvider.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.Message.SMSProvider.ChangeLog.List","ChangeLogDataName=SMSProvider"],
					];
	//=======Form1 SMSProvider Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	SMSProviderName:'کاراکتر انگلیسی و اعداد و حداکثر ۳۲ کاراکتر'};
	var Form1FieldHelpId=["SMSProviderName","PhpSendCode","ISEnable"];
	var Form1TitleField="SMSProviderName";
	var Form1DisableItems=[""];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="SMSProviderName";

	var Form1Str = [
		{ type:"settings" , labelWidth:150, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"SMSProvider_Id", label:"شناسه سرویس دهنده پیام کوتاه :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"SMSProviderName", label:"نام ارائه دهنده پیام کوتاه :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type: "input" , name:"PhpSendCode", label:"Php کد  :", validate:"NotEmpty",value:"",rows:15 , maxLength:4096,inputWidth:500,required:true},
		{type: "block", width: 600, list:[
			{ type: "button",name:"FillExample1",value: "پر کردن با مثال",width :80},
			{type: "newcolumn", offset:20},
		]}	
		];
	//=======Popup2 SendSMS
	var Popup2;
	var PopupId2=['SendSMS'];// popup Attach to Which Buttom of Toolbar

	//=======Form2 SendSMS
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {MobileNo:'شماره موبایل 11 رقمی'
						,Message:' پیام آزمایشی '
						};
	var Form2FieldHelpId=['ResellerCF','ChargerCF','MinPriceChangeReseller'];
	var Form2Str = [
		{ type:"settings" , labelWidth:80, inputWidth:350,offsetLeft:10  },
		{ type:"input" , name:"MobileNo", label:"شماره موبایل :",validate:"IsValidMobileNo", labelAlign:"left",info:"false", inputWidth:100, required:true},
		{ type:"input" , name:"Message", label:"متن پیام :",rows :4, labelAlign:"left", value:"پیام آزمایشی",info:"false", inputWidth:200, required:true},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "ارسال",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];

	var PermitView=ISPermit("Admin.Message.SMSProvider.View");
	var PermitAdd=ISPermit("Admin.Message.SMSProvider.Add");
	var PermitEdit=ISPermit("Admin.Message.SMSProvider.Edit");
	var PermitDelete=ISPermit("Admin.Message.SMSProvider.Delete");

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
		if(PermitEdit) AddPopupSendSMS();
		
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
function AddPopupSendSMS(){
	DSToolbarAddButtonPopup(Toolbar1,null,"SendSMS","ارسال پیامک","tow_SendSMS");
	Popup2=DSInitialPopup(Toolbar1,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Form2OnButtonClick(name){//SendSMS
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form2,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=SendSMS&SMSProvider_Id="+RowId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}
function Form2DoAfterUpdateOk(){
	Popup2.hide();
}
function Form2DoAfterUpdateFail(){
	Popup2.hide();
}

function Popup2OnShow(){//SendSMS
}

function Form1OnButtonClick(name){
	if(name=='FillExample1'){
		Form1.setItemValue('PhpSendCode',
'require_once(\'nusoap/nusoap.php\');\
\r$wsdl="http://www.afe.ir/WebService/WebService.asmx?wsdl";\
\r$client=new nusoap_client($wsdl, \'wsdl\');\
\r$client->soap_defencoding = \'UTF-8\';\
\r$client->decode_utf8 = false;\
\r\
\r$mobiles = array("$MobileNo");\
\r$param=array(\
\r	\'Username\' => \'omid\',\
\r	\'Password\' => \'1234\',\
\r	\'Number\' => 30007957,\
\r	\'Mobile\' => array(\'string\' => $mobiles) ,\
\r	\'Message\' => "$Message",\
\r	\'Type\' => \'1\'\
\r	);\
\r$results = $client->call(\'SendMessage\', $param);\
\r$results = $results["SendMessageResult"];\
\r$results = $results["string"];\
\rIF ($results == "Send Successfully") {\
\r	return  "OK";\
\r	}\
\rELSE{\
\r	return"Error: \'$results\'";\
\r	}\
		');
	}	

}
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		dhxLayout.cells("a").progressOn();
		if(RowId>0){//update
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
		else{//insert
			DSFormInsertRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=insert",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
			
		}//insert
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
	dhxLayout.cells("a").progressOff();
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
	dhxLayout.cells("a").progressOff();

}
function Form1DoAfterUpdateFail(){
	dhxLayout.cells("a").progressOff();
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
	else
		AddPopupSendSMS();
	dhxLayout.cells("a").progressOff();
	
}
function Form1DoAfterInsertFail(){
	dhxLayout.cells("a").progressOff();
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
