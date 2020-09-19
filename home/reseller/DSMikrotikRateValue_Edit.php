<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSMikrotikRateValue_Edit ....................................................................................");
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
		.CenterLabel{text-align:center;}
		.SpeedLabel{font-size:80%;}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="سرعت میکروتیک";
	var DataName="DSMikrotikRateValue_";
	var ChangeLogDataName='MikrotikRateValue';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSMikrotikRateValue_Edit","70","Admin.User.MikrotikRateValue.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.MikrotikRateValue.ChangeLog.List","ChangeLogDataName=MikrotikRateValue"]
					];
	//=======Form1 Visp Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	MikrotikRateValueName:'Character, maxlength:32',
							MikrotikRateValueText:'Enter Mikrotikrate Example 128k/128k , maxlength:64'
							};
	var Form1FieldHelpId=["MikrotikRateValueName"];
	var Form1TitleField="MikrotikRateValueName";
	var Form1DisableItems=[""];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="MikrotikRateValueName";

	var Form1Str = [
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"MikrotikRateValue_Id", label:"MikrotikRateValue_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{type:"block",list:[
			{ type:"input" , name:"MikrotikRateValueName", label:"نام سرعت میکروتیک :",maxLength:32,labelWidth:170, validate:"NotEmpty",required:true, labelAlign:"left", info:"false", inputWidth:200}
		]},
		{type:"block",list:[
			{ type:"input" , name:"MikrotikRateValueText", label:"مقادیر :",labelWidth:170,value:"256K/256K 2048K/2048K 1024K/1024K 1/1 8 128K/128K",maxLength:64, validate:"NotEmpty",required:true, readonly:true, labelAlign:"left", inputWidth:400},
			{type:"newcolumn"},
			{type:"button", name:"MikrateEditor", value:"ویرایش", width :70}
		]}
		// { type:"input" , name:"Example :", label:"Example :",value:"256K/256K 2048K/2048K 1024K/1024K 10/10 1 128K/128K",disabled:"true",maxLength:64, validate:"NotEmpty",required:true, labelAlign:"left", info:"true", inputWidth:400}
		];
	
	//=======Popup2
	var Popup2;
	var Popup2_Id=["MikrotikRateValueText"];//popup Attach to Which input of form1
	//=======Form2
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {Username:""};
	var Form2FieldHelpId=["Username"];
	var Form2Str = [
		{type: "fieldset", label: "ویرایشگر سرعت میکروتیک", width: 440, list:[
			{type:"block", list:[
				{type:"newcolumn", offset:104},
				{type:"label",className:"CenterLabel", labelWidth:94,label:"آپلود"},
				{type:"newcolumn", offset:9},
				{type:"label",className:"CenterLabel",labelWidth:94,label:"دانلود"}
			]},
			{type:"block", width: 390, list:[
				{type:"input", name:"MaxLimitUpload", label:"Max Limit : ", labelWidth:100, required:true, inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:5},
				{type:"input", name:"MaxLimitDownload", inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:1},
				{type:"label", className: "SpeedLabel", labelWidth:30, label:"bits/s"}
			]},
			{type:"label", label:"<div style='border-bottom:1px solid darkgray;width:100%'></div>"},
			{type:"block", list:[
				{type:"input", name:"BurstLimitUpload", label:"Burst Limit : ", labelWidth:100, required:true, inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:5},
				{type:"input", name:"BurstLimitDownload", inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:1},
				{type:"label", className: "SpeedLabel", labelWidth:30, label:"bits/s"}
			]},
			{type:"block", list:[
				{type:"input", name:"BurstThresholdUpload", label:"Burst Threshold : ", labelWidth:100, required:true, inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:5},
				{type:"input", name:"BurstThresholdDownload", inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:1},
				{type:"label", className: "SpeedLabel", labelWidth:30, label:"bits/s"}
			]},
			{type:"block", list:[
				{type:"input", name:"BurstTimeUpload", label:"Burst Time : ", labelWidth:100, required:true, inputWidth:100,maxLength:10, validate:"^[0-9]+$"},
				{type:"newcolumn", offset:5},
				{type:"input", name:"BurstTimeDownload", inputWidth:100,maxLength:10, validate:"^[0-9]+$"},
				{type:"newcolumn", offset:1},
				{type:"label", labelWidth:30, label:"<span style='font-size:80%;'>Sec</span>"},
			]},
			{type:"label", label:"<div style='border-bottom:1px solid darkgray;width:100%'></div>"},
			{type:"block", list:[
				{type:"input", name:"LimitAtUpload", label:"Limit At : ", labelWidth:100, required:true, inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:5},
				{type:"input", name:"LimitAtDownload", inputWidth:100,maxLength:10, validate:"^(unlimited|[0-9]+[kKmM]?)$"},
				{type:"newcolumn", offset:1},
				{type:"label", className: "SpeedLabel", labelWidth:30, label:"bits/s"}
			]},
			{type:"block", list:[
				{type:"input", name:"Priority", label:"Priority : ", labelWidth:100, required:true, inputWidth:100,maxLength:1, validate:"^[1-8]$"}
			]},
			{type:"block", list:[
				{type:"label", label:"صفر=بدون محدودیت", labelWidth:170},
				{type:"newcolumn", offset:30},
				{type:"label", label:'<a target="_blank" href="http://wiki.mikrotik.com/wiki/Manual:Queues_-_Burst">اطلاعات بیشتر</a>', labelWidth:85},
				{type:"newcolumn", offset:10},
				{type:"label", label:'<a target="_blank" href="https://www.ip-pro.pl/kalkulator/burst_simulator">شبیه ساز</a>', labelWidth:75}
			]}
		]},
		{type: "block", width: 430, list:[
			{type: "newcolumn", offset:210},
			{type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:10},
			{type: "button",name:"Proceed",value: " ذخیره ",width :80}
		]}
	];	
	
	
	
	var PermitView=ISPermit("Admin.User.MikrotikRateValue.View");
	var PermitAdd=ISPermit("Admin.User.MikrotikRateValue.Add");
	var PermitEdit=ISPermit("Admin.User.MikrotikRateValue.Edit");
	var PermitDelete=ISPermit("Admin.User.MikrotikRateValue.Delete");

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
	Form1.attachEvent("onFocus",function(name){if(name=="MikrotikRateValueText")Popup2.show("MikrotikRateValueText")});
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


	AddPopupMikrotikRateValueText();
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
//-------------------------------------------------------------------AddPopupMikrotikRateValueText()
function AddPopupMikrotikRateValueText(){
	Popup2=new dhtmlXPopup({form: Form1,id: Popup2_Id,mode:"bottom"});
	Popup2.setSkin(popup_main_skin);
	Popup2.attachEvent("onShow",Popup2OnShow);
	Popup2.attachEvent("onHide",function(){Form1.unlock()});
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onBlur",Form2OnBlur);
	Form2.attachEvent("onFocus",Form2OnFocus);
	// Form2.enableLiveValidation(false);
}

//-------------------------------------------------------------------Popup2OnShow()
function Popup2OnShow(){
	Form1.lock();
	var MikrateText=Form1.getItemValue("MikrotikRateValueText").replace(/[k]/ig,'k').replace(/[m]/ig,'M');
	var tmp1=MikrateText.split(" ");
	var tmp2=tmp1[0].split("/");
	Form2.setItemValue("MaxLimitUpload",tmp2[0]==0?"unlimited":tmp2[0]);
	Form2.setItemValue("MaxLimitDownload",tmp2[1]==0?"unlimited":tmp2[1]);
	tmp2=tmp1[1].split("/");
	Form2.setItemValue("BurstLimitUpload",tmp2[0]==0?"unlimited":tmp2[0]);
	Form2.setItemValue("BurstLimitDownload",tmp2[1]==0?"unlimited":tmp2[1]);
	tmp2=tmp1[2].split("/");
	Form2.setItemValue("BurstThresholdUpload",tmp2[0]==0?"unlimited":tmp2[0]);
	Form2.setItemValue("BurstThresholdDownload",tmp2[1]==0?"unlimited":tmp2[1]);
	tmp2=tmp1[3].split("/");
	Form2.setItemValue("BurstTimeUpload",tmp2[0]);
	Form2.setItemValue("BurstTimeDownload",tmp2[1]);
	
	Form2.setItemValue("Priority",tmp1[4]);
	
	tmp2=tmp1[5].split("/");
	Form2.setItemValue("LimitAtUpload",tmp2[0]==0?"unlimited":tmp2[0]);
	Form2.setItemValue("LimitAtDownload",tmp2[1]==0?"unlimited":tmp2[1]);
	
}

function Form2OnButtonClick(name){
	if(name=="Close")
		Popup2.hide();
	else if(name=="Proceed"){
		Form2.updateValues();
		if(!Form2.validate()){
			dhtmlx.message("Check errors...");
			return;
		}
		var Validation="";
			
		var MaxLimitUpload=Form2.getItemValue("MaxLimitUpload");
		var MaxLimitDownload=Form2.getItemValue("MaxLimitDownload");
		var BurstLimitUpload=Form2.getItemValue("BurstLimitUpload");
		var BurstLimitDownload=Form2.getItemValue("BurstLimitDownload");
		var BurstThresholdUpload=Form2.getItemValue("BurstThresholdUpload");
		var BurstThresholdDownload=Form2.getItemValue("BurstThresholdDownload");
		var BurstTimeUpload=Form2.getItemValue("BurstTimeUpload");
		var BurstTimeDownload=Form2.getItemValue("BurstTimeDownload");
		var Priority=Form2.getItemValue("Priority");
		var LimitAtUpload=Form2.getItemValue("LimitAtUpload");
		var LimitAtDownload=Form2.getItemValue("LimitAtDownload");
		var MikrateText=
			((MaxLimitUpload=="unlimited")?"0":MaxLimitUpload)+"/"+
			((MaxLimitDownload=="unlimited")?"0":MaxLimitDownload)+" "+
			((BurstLimitUpload=="unlimited")?"0":BurstLimitUpload)+"/"+
			((BurstLimitDownload=="unlimited")?"0":BurstLimitDownload)+" "+
			((BurstThresholdUpload=="unlimited")?"0":BurstThresholdUpload)+"/"+
			((BurstThresholdDownload=="unlimited")?"0":BurstThresholdDownload)+" "+
			BurstTimeUpload+"/"+
			BurstTimeDownload+" "+
			Priority+" "+
			((LimitAtUpload=="unlimited")?"0":LimitAtUpload)+"/"+
			((LimitAtDownload=="unlimited")?"0":LimitAtDownload);
		
		
				
		MaxLimitUpload=parseInt(MaxLimitUpload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		MaxLimitDownload=parseInt(MaxLimitDownload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		BurstLimitUpload=parseInt(BurstLimitUpload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		BurstLimitDownload=parseInt(BurstLimitDownload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		BurstThresholdUpload=parseInt(BurstThresholdUpload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		BurstThresholdDownload=parseInt(BurstThresholdDownload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		LimitAtUpload=parseInt(LimitAtUpload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		LimitAtDownload=parseInt(LimitAtDownload.replace(/unlimited/ig,'100000000000').replace(/[k]/ig,'000').replace(/[m]/ig,'000000'));
		
		if((BurstLimitUpload<MaxLimitUpload)&&(BurstLimitUpload<9999999999))
			Validation="حداکثر سرعت آپلود باید کمتر یا مساوی سرعت برست آپلود باشد";
		else if((BurstLimitDownload<MaxLimitDownload)&&(BurstLimitDownload<9999999999))
			Validation="حداکثر سرعت دانلود باید کمتر یا مساوی سرعت برست دانلود باشد";
		else if((BurstLimitUpload<9999999999)&&(BurstTimeUpload==0))
			Validation="زمان برست آپلود باید بزرگتر از ۰ باشد";
		else if((BurstLimitDownload<9999999999)&&(BurstTimeDownload==0))
			Validation="زمان برست دانلود باید بزرگتر از ۰ باشد";
		else if((BurstLimitUpload<BurstThresholdUpload)&&(BurstThresholdUpload<9999999999))
			Validation="آستانه برست آپلود باید کمتر یا مساوی سرعت برست آپلود باشد";
		else if((BurstLimitDownload<BurstThresholdDownload)&&(BurstThresholdDownload<9999999999))
			Validation="آستانه برست دانلود باید کمتر یا مساوی سرعت برست دانلود باشد";
		else if(MaxLimitUpload<LimitAtUpload)
			Validation="حداقل سرعت آپلود باید کمتر یا مساوی با حداکثر سرعت آپلود باشد";
		else if(MaxLimitDownload<LimitAtDownload)
			Validation="حداقل سرعت دانلود باید کمتر یا مساوی با حداکثر سرعت دانلود باشد";
		
		if(Validation!="")
			parent.dhtmlx.alert({text:Validation,type:"alert-warning", title:"توجه",ok:"بستن"});
		else 
			Popup2.hide();
		Form1.setItemValue("MikrotikRateValueText",MikrateText);
	}
}

function Form2OnBlur(name){
	Form2.updateValues();
	if((name=="MaxLimitUpload")||(name=="MaxLimitDownload")||(name=="BurstLimitUpload")||(name=="BurstLimitDownload")||(name=="BurstThresholdUpload")||(name=="BurstThresholdDownload")||(name=="LimitAtUpload")||(name=="LimitAtDownload")){
		value=Form2.getItemValue(name).replace(/^0+/, '0');
		if((value=="0")||(value.toLowerCase()=="0k")||(value.toLowerCase()=="0m")){
			Form2.setItemValue(name,"unlimited");
			// Form2.resetValidateCss(name);
		}
		else{
			value=value.replace(/^0+/, '');
			if(value.substr(value.length-1,1)=="K")
				Form2.setItemValue(name,value.toLowerCase());
			else if(value.substr(value.length-1)=="m")
				Form2.setItemValue(name,value.toUpperCase());
			else
				Form2.setItemValue(name,value);
		}
	}
}
function Form2OnFocus(name){
	if(((name=="MaxLimitUpload")||(name=="MaxLimitDownload")||(name=="BurstLimitUpload")||(name=="BurstLimitDownload")||(name=="BurstThresholdUpload")||(name=="BurstThresholdDownload")||(name=="LimitAtUpload")||(name=="LimitAtDownload"))&&(Form2.getItemValue(name)=="unlimited"))
		Form2.setItemValue(name,0);
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
	if(name=="MikrateEditor")Popup2.show("MikrotikRateValueText")
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
