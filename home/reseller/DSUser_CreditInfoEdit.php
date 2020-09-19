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
			margin: 0px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background-color:white;
        }
		div#vp {
			height: 600px;
			border: 0px solid #cecece;
		}
		
		div.myHeader {
			overflow: hidden;
			border: 0px solid #cecece;
			background-color: #F6F0F0;
		}
		div.myGift {
			overflow: hidden;
			border: 0px solid #cecece;
			background-color: #E6EDF6;
		}
		div.myTraffic {
			overflow: hidden;
			border: 0px solid #cecece;
			background-color: #F6F0F0;
		}
		div.myTime {
			overflow: hidden;
			border: 0px solid #cecece;
			background-color: #E6EDF6;
		}
		div.ExtraInfo{
			overflow: hidden;
			border: 0px solid #cecece;
			background-color: #F0F0F6;			
		}
		div.None {
			overflow: hidden;
			border: 0px solid #cecece;
			#background-color:blue;
		}
		.tooltip {
			border-bottom: 1px dotted black;
			color:firebrick;
		}

		.tooltip .tooltiptext {
			visibility: hidden;
			width: 240px;
			background-color: steelblue;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 12px 8px;
			position: absolute;
			z-index: 1;
		}

		.tooltip:hover .tooltiptext {
			visibility: visible;
		}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission='';
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	RowId="<?php  echo $_GET['ParentId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="کاربر";
	var DataName="DSUser_";
	var ChangeLogDataName='User';
	var RenderFile="DSUser_CreditInfoEditRender";
	var TabbarMain,TopToolbar;
	//=======Form1 Service Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={};
	var Form1FieldHelpId=[];
	var Form1DisableItems=[];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1Str = [
		{ type:"settings",labelWidth:70,offsetLeft:5},
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:128,inputWidth:300},
		{ type: "hidden" , name:"CurrentDT", label:"CurrentDT :", validate:"",value:"", maxLength:128,inputWidth:300},
		// { type: "label"},
		{type: "fieldset",width: 680,className :"myHeader", label:"مشخصات سرویس", list:[
			{ type: "input" , name:"ServiceStatus", label:"Status:", readonly:true, labelAlign:"right", inputWidth:50,labelWidth:45},
			{type: "newcolumn"},
			{ type: "input" , name:"ServiceName", label:"نام سرویس :", readonly:true, labelAlign:"right", inputWidth:300},
			{type: "newcolumn"},
			{ type: "input" , name:"ISFairService", label:"مصرف منصفانه هست :", readonly:true, labelAlign:"right", inputWidth:30,labelWidth:110},
			{type: "newcolumn"},
			{ type: "label" ,label:"تاریخ شروع", labelWidth:75},
			{ type: "input" , name:"StartDate", label:"", readonly:true, labelAlign:"right", inputWidth:75},
			{type: "newcolumn"},
			{ type: "label" ,label:"دوره",labelWidth:100},
			{ type: "input" , name:"Period", label:"", readonly:true, labelAlign:"right", inputWidth:100},
			 /* {type: "newcolumn",hidden:true},
			{ type: "label" ,label:"ExtraDay",labelWidth:65,hidden:true},
			{ type: "input" , name:"ExtraDay", label:"", readonly:true, labelAlign:"right", inputWidth:65,hidden:true}, */
			{type: "newcolumn"},
			{ type: "label" ,label:"تاریخ پایان",labelWidth:130},
			{ type: "input" , name:"EndDate", label:"", readonly:true, labelAlign:"right",labelWidth:5, inputWidth:130},
			{type: "newcolumn"},
			{ type: "label" ,label:"نشست",labelWidth:40},
			{ type: "input" , name:"Session", label:"", readonly:true, labelAlign:"right", inputWidth:40},
			{type: "newcolumn"},
			{ type: "label" ,label:"آخرین بروزرسانی",labelWidth:130},
			{ type: "input" , name:"LastRequestDT", label:"", readonly:true, labelAlign:"right", inputWidth:130},
			{type: "newcolumn"},
			{ type: "label" ,label:"برگشت ترافیک",labelWidth:95},
			{ type: "input" , name:"ReturnTr", label:"", readonly:true, labelAlign:"right", inputWidth:95},
			{type: "newcolumn"},
			{ type: "label" , name:"UPFairStatusLabel", label:"UPFairStatus",labelWidth:110},
			{ type: "input" , name:"UPFairStatus", label:"",disabled:true, readonly:true, labelAlign:"right", inputWidth:110},
			{type: "newcolumn"},
			{ type: "label" ,name:"FairMikrotikRateLabel", label:"FairMikrotikRate",labelWidth:200},
			{ type: "input" , name:"FairMikrotikRate", label:"",disabled:true, readonly:true, labelAlign:"right", inputWidth:200},
			{type: "newcolumn"},
			//{ type: "label" ,name:"ServiceFreeTrULabel", label:"Service Free Traffic Use",labelWidth:200,hidden:true},
			//{ type: "input" , name:"ServiceFreeTrU", label:"",disabled:true, readonly:true, labelAlign:"right", inputWidth:200,hidden:true},
			//{type: "newcolumn"},
			{ type: "label" ,name:"ServiceBufferTrULabel", label:"Service Buffer Tr",labelWidth:200,hidden:true},
			{ type: "input" , name:"ServiceBufferTr", label:"",disabled:true, readonly:true, labelAlign:"right", inputWidth:200,hidden:true},
		]},
		// { type: "label"},
		{type: "fieldset",  width: 680,className :"myGift", label:"مشخصات هدیه", list:[ 
				{ type: "hidden" , name:"User_Gift_Id",disabled:true, readonly:true, labelAlign:"right", inputWidth:130},
				{ type: "label" ,label:"تاریخ پایان",labelWidth:130,name: "Gift"},
				{ type: "input" , name:"GiftEndDT",disabled:true, readonly:true, labelAlign:"right", inputWidth:130},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"ضریب ترافیک",labelWidth:85},
				{ type: "input" , name:"GiftTrafficRate",disabled:true, readonly:true, labelAlign:"right", inputWidth:75},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"ضریب زمان",labelWidth:75},
				{ type: "input" , name:"GiftTimeRate",disabled:true, readonly:true, labelAlign:"right", inputWidth:75},
				{type: "newcolumn", offset:0},
				{ type: "label" , name:"GiftExtraTrLabel",label:"ExtraTr",labelWidth:75},
				{ type: "input" , name:"GiftExtraTr",disabled:true, readonly:true, labelAlign:"right", inputWidth:75},
				{ type: "hidden" , name:"GiftStopOnTrFinish"},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"زمان",labelWidth:75},
				{ type: "input" , name:"GiftExtraTi",disabled:true, readonly:true, labelAlign:"right", inputWidth:75},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"سرعت",labelWidth:125},
				{ type: "input" , name:"GiftMikrotikRate",disabled:true, readonly:true, labelAlign:"right", inputWidth:125},
		//{type: "label",label: "Traffic(GigaByte)-------------------------------------------------------------------------------------------------",name: "Traffic",},
		]},
		// { type: "label"},
		{type: "fieldset",  width: 680,className :"myTraffic",name: "Traffic", label:"Traffic(GigaByte)",list:[
				{ type: "label" ,label:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;سرویس",labelWidth:120},
				{ type: "input" , name:"STrA",labelWidth:70, label:"<span style='color:black'>مجاز :</span>",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"STrU",labelWidth:70, label:"<span style='color:black'>استفاده شده :</span>",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"STrR",labelWidth:70, label:"<span style='font-size:90%;color:black'>باقی مانده :</span>",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"سالیانه",labelWidth:75},
				{ type: "input" , name:"YTrA",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"YTrU",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"YTrR",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"ماهیانه",labelWidth:75},
				{ type: "input" , name:"MTrA",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"MTrU",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"MTrR",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"هفتگی",labelWidth:70},
				{ type: "input" , name:"WTrA",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"WTrU",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"WTrR",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"روزانه",labelWidth:70},
				{ type: "input" , name:"DTrA",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"DTrU",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"DTrR",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{type: "newcolumn", offset:20},
				{ type: "label" ,label:"<span style='color:blue'>اضافی</span>",labelWidth:80},
				{ type: "input" , name:"ETrA",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"ETrU",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"ETrR",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{type: "newcolumn"},
				// { type: "label"},
				{type: "block", width: 630,className :"None", list:[
					{ type: "input" , name:"BugUsedTr", label:"استفاده بیشتر :",labelWidth:75,disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
					{type: "newcolumn", offset:1},
					{ type: "input" , name:"FinishUsedTr", label:"اتمام :",labelWidth:50,disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
					{type: "newcolumn", offset:1},
					{ type: "input" , name:"RealReceiveTr", label:"دریافت واقعی :",labelWidth:70,disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
					{type: "newcolumn", offset:1},
					{ type: "input" , name:"RealSendTr", label:"ارسال واقعی :",labelWidth:70,disabled:true, readonly:true, labelAlign:"right", inputWidth:80}
					]
				},
		]},
		// { type: "label"},
		{type: "fieldset",  width: 680,className :"myTime",name: "Time", label:"Time(Hour)",list:[
				{ type: "label" ,label:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;سرویس",labelWidth:120},
				{ type: "input" , name:"STiA",labelWidth:70, label:"<span style='color:black'>مجاز :</span>",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"STiU",labelWidth:70, label:"<span style='color:black'>استفاده شده :</span>",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"STiR",labelWidth:70, label:"<span style='font-size:90%;color:black'>باقی مانده :</span>",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"سالیانه",labelWidth:75},
				{ type: "input" , name:"YTiA",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"YTiU",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"YTiR",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"ماهیانه",labelWidth:75},
				{ type: "input" , name:"MTiA",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"MTiU",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{ type: "input" , name:"MTiR",disabled:true, readonly:true, labelAlign:"right", inputWidth:85},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"هفتگی",labelWidth:70},
				{ type: "input" , name:"WTiA",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"WTiU",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"WTiR",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{type: "newcolumn", offset:0},
				{ type: "label" ,label:"روزانه",labelWidth:70},
				{ type: "input" , name:"DTiA",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"DTiU",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{ type: "input" , name:"DTiR",disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
				{type: "newcolumn", offset:20},
				{ type: "label" ,label:"<span style='color:blue'>اضافی</span>",labelWidth:80},
				{ type: "input" , name:"ETiA",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"ETiU",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{ type: "input" , name:"ETiR",disabled:true, readonly:true, labelAlign:"right", inputWidth:90},
				{type: "newcolumn"},
				// { type: "label"},
				{type: "block", width: 630,className :"None", list:[
					{ type: "input" , name:"BugUsedTi", label:"استفاده بیشتر :",labelWidth:75,disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
					{type: "newcolumn", offset:1},
					{ type: "input" , name:"FinishUsedTi", label:"اتمام :",labelWidth:50,disabled:true, readonly:true, labelAlign:"right", inputWidth:80},
					{type: "newcolumn", offset:1},
					{ type: "input" , name:"RealUsedTime", label:"زمان واقعی مصرف :",labelWidth:110,disabled:true, readonly:true, labelAlign:"right", inputWidth:80}
					]
				},
		]},
		{ type: "label"},
		{type: "fieldset",  name:"ExtraInfoBlock", hidden:true, width: 680, label:"اطلاعات اضافی",className :"ExtraInfo", list:[
			{ type: "label" ,label:"زمان آخرین دخیره",labelWidth:200},
			{ type: "input" , name:"LastSaveDT", label:"", readonly:true, labelAlign:"right", inputWidth:200},
			{type: "newcolumn"},
			{ type: "label" ,label:"سرعت سرویس",labelWidth:200},
			{ type: "input" , name:"MikrotikRateName", label:"", readonly:true, labelAlign:"right", inputWidth:200},
			{type: "newcolumn"},
			{ type: "label" ,label:"IPPoolName",labelWidth:200},
			{ type: "input" , name:"IPPoolName", label:"", readonly:true, labelAlign:"right", inputWidth:200},
			{type: "newcolumn"},
			{ type: "label" ,label:"ضریب زمان",labelWidth:300},
			{ type: "input" , name:"TimeRate", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"ضریب ترافیک",labelWidth:300},
			{ type: "input" , name:"TrafficRate", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"ضریب دریافت",labelWidth:90},
			{ type: "input" , name:"ReceiveRate", label:"", readonly:true, labelAlign:"right", inputWidth:94},
			{type: "newcolumn"},
			{ type: "label" ,label:"ضریب ارسال",labelWidth:90},
			{ type: "input" , name:"SendRate", label:"", readonly:true, labelAlign:"right", inputWidth:94},
			{type: "newcolumn"},
			{ type: "label" ,label:"اتصال همزمان",labelWidth:90},
			{ type: "input" , name:"Simulation", label:"", readonly:true, labelAlign:"right", inputWidth:94},
			{type: "newcolumn"},
			{ type: "label" ,label:"زمان بروزرسانی اطلاعات",labelWidth:180},
			{ type: "input" , name:"InterimTime", label:"", readonly:true, labelAlign:"right", inputWidth:180},
			{type: "newcolumn"},
			{ type: "label" ,label:"نوع تقویم",labelWidth:90},
			{ type: "input" , name:"Calendar", label:"", readonly:true, labelAlign:"right", inputWidth:94},
			{type: "newcolumn"},
			{ type: "label" ,label:"نوع محاسبه مصرف",labelWidth:120},
			{ type: "input" , name:"PeriodicUse", label:"", readonly:true, labelAlign:"right", inputWidth:120},
			{type: "newcolumn"},
			{ type: "label" ,label:"نوع اعتبارسنجی",labelWidth:146},
			{ type: "input" , name:"AuthMethod", label:"", readonly:true, labelAlign:"right", inputWidth:146},
			{type: "newcolumn"},
			{ type: "label" ,label:"نوع کاربری",labelWidth:146},
			{ type: "input" , name:"UserType", label:"", readonly:true, labelAlign:"right", inputWidth:146},
			{type: "newcolumn"},
			{ type: "label" ,label:"ریست خودکار اعتبار اضافی",labelWidth:170},
			{ type: "input" , name:"AutoResetExtraCredit", label:"", readonly:true, labelAlign:"right", inputWidth:147},
			{type: "newcolumn"},
			{ type: "label" ,label:"گزارش صفحات بازدید شده",labelWidth:165},
			{ type: "input" , name:"URLReporting", label:"", readonly:true, labelAlign:"right", inputWidth:165},
			{type: "newcolumn"},
			{ type: "label" ,label:"حداکثر زمان نشست",labelWidth:300},
			{ type: "input" , name:"MaxSessionTime", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"محدودیت زمان ورود",labelWidth:300},
			{ type: "input" , name:"LoginTimeName", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"قانون اتمام اعمال شده",labelWidth:300},
			{ type: "input" , name:"FinishRuleName", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"فرمول تخفیف اعمال شده",labelWidth:300},
			{ type: "input" , name:"OffFormulaName", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"اکتیو دایرکتوری",labelWidth:300},
			{ type: "input" , name:"ActiveDirectoryName", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"کنترل بدهی اعمال شده",labelWidth:300},
			{ type: "input" , name:"DebitControlName", label:"", readonly:true, labelAlign:"right", inputWidth:309 },
			{type: "newcolumn"},
			{ type: "label" ,label:"محدودیت پنل کاربری",labelWidth:300},
			{ type: "input" , name:"WebAccessName", label:"", readonly:true, labelAlign:"right", inputWidth:309},
			{type: "newcolumn"},
			{ type: "label" ,label:"مک/آی پی سرور",labelWidth:300},
			{ type: "input" , name:"CalledIdName", label:"", readonly:true, labelAlign:"right", inputWidth:309},
		]},
	
		
	];

	// Layout   ===================================================================

	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);


	// Form1   ===================================================================
	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	if(RowId>0){
		DSToolbarAddButton(TopToolbar,0,"Retrieve_Byte_Second","(بروزکردن(بایت-ثانیه","Retrieve",TopToolbar_OnRetrieve_Byte_SecondClick);
		DSToolbarAddButton(TopToolbar,0,"Retrieve_MByte_Minute","(بروزکردن(مگابایت-دقیقه","Retrieve",TopToolbar_OnRetrieve_MByte_MinuteClick);
		DSToolbarAddButton(TopToolbar,0,"Retrieve_GByte_Hour","(بروزکردن(گیگابایت-ساعت","Retrieve",TopToolbar_OnRetrieve_GByte_HourClick);
		
		TopToolbar.addSeparator("sep1",null);
		TopToolbar.addButtonTwoState("ShowExtraInfo", null, "نمایش اطلاعات بیشتر", "ds_tow_ShowExtraInfo.png", "ds_tow_ShowExtraInfo_dis.png");
		TopToolbar.setItemToolTip("ShowExtraInfo","Show extra informations...");
		TopToolbar.attachEvent("onStateChange", function(id, state){
			if(id=="ShowExtraInfo"){
				if(state){
					var TrafficLabel=Form1.getItemLabel("Traffic");
					if(TrafficLabel=="Traffic(Byte)")
						TopToolbar_OnRetrieve_Byte_SecondClick();
					else if(TrafficLabel=="Traffic(MegaByte)")
						TopToolbar_OnRetrieve_MByte_MinuteClick();
					else if(TrafficLabel=="Traffic(GigaByte)")
						TopToolbar_OnRetrieve_GByte_HourClick();
					//Form1.showItem("ExtraInfoBlock");
					//Form1.showItem("ServiceFreeTrU");
					//Form1.showItem("ServiceFreeTrULabel");
					Form1.showItem("ServiceBufferTrULabel");
					Form1.showItem("ServiceBufferTr");
				}
				else{
					Form1.hideItem("ExtraInfoBlock");
					//Form1.hideItem("ServiceFreeTrU");
					//Form1.hideItem("ServiceFreeTrULabel");
					Form1.hideItem("ServiceBufferTrULabel");
					Form1.hideItem("ServiceBufferTr");
				}
			}
		});
		
		TopToolbar_OnRetrieve_GByte_HourClick();
	}
	else{
		alert("Can not find Row");
	}
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
//myForm.setFocusOnFirstActive();

//FUNCTION========================================================================================================================
//================================================================================================================================
function TopToolbar_OnRetrieve_Byte_SecondClick(){
	Form1.setItemLabel("Traffic","(ترافیک(بایت");	
	Form1.setItemLabel("Time",	 "(زمان(ثانیه");
	TopToolbar_Retrieve("BS");
}
function TopToolbar_OnRetrieve_MByte_MinuteClick(){
	Form1.setItemLabel("Traffic","(ترافیک(مگابایت");	
	Form1.setItemLabel("Time",   "(زمان(دقیقه");	
	TopToolbar_Retrieve("MM");
}
function TopToolbar_OnRetrieve_GByte_HourClick(){
	Form1.setItemLabel("Traffic","(ترافیک(گیگابایت");	
	Form1.setItemLabel("Time",   "(زمان(ساعت");	
	TopToolbar_Retrieve("GH");
}

function TopToolbar_Retrieve(unit){
	TopToolbar.disableItem("Retrieve_Byte_Second");
	TopToolbar.disableItem("Retrieve_MByte_Minute");
	TopToolbar.disableItem("Retrieve_GByte_Hour");
	TopToolbar.disableItem("ShowExtraInfo");
	var ShowExtraInfo=(TopToolbar.getItemState("ShowExtraInfo"))?1:0;
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,RenderFile+".php?&ShowExtraInfo="+ShowExtraInfo+"&unit="+unit+"&",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Form1OnButtonClick(name){
}

function Form1DoAfterLoadOk(){
	setTimeout(function(){
		TopToolbar.enableItem("Retrieve_Byte_Second");
		TopToolbar.enableItem("Retrieve_MByte_Minute");
		TopToolbar.enableItem("Retrieve_GByte_Hour");
		TopToolbar.enableItem("ShowExtraInfo");
	},(LoginResellerName=='admin')?50:1000);
	//Err=Form1.getItemValue("Error");
	//if(Err!='')	alert(Err);
	
	if(Form1.getItemValue("ServiceStatus")!='Active'){
		Form1.setItemLabel("ServiceStatus", "<span style='color:red;font-weight:bold;'>وضعیت:</span>");
	}
	else{
		Form1.setItemLabel("ServiceStatus", "وضعیت :");

		if(Form1.getItemValue("CurrentDT")>=Form1.getItemValue("EndDate"))
			Form1.getInput("EndDate").style.color="#FF0000";
		else
			Form1.getInput("EndDate").style.color="black";
		
		// if(parseFloat(Form1.getItemValue("ExtraDay"))>0)
			// Form1.getInput("ExtraDay").style.backgroundColor="#00FF66";
		// else
			// Form1.getInput("ExtraDay").style.backgroundColor="#FFFFFF";
		
		if(parseFloat(Form1.getItemValue("Session"))>0){
			Form1.getInput("Session").style.backgroundColor="#C9E9FF";
			Form1.getInput("LastRequestDT").style.backgroundColor="#C9E9FF";
			Form1.getInput("Session").style.fontWeight="bold";
		}
		else{
			Form1.getInput("Session").style.backgroundColor="#FFFFFF";
			Form1.getInput("LastRequestDT").style.backgroundColor="#FFFFFF";
			Form1.getInput("Session").style.fontWeight="normal";
		}
	}
	var IsMikrotikRateSet=false;
	if(Form1.getItemValue("ISFairService")=='Yes'){
		FormShowItem(Form1,["UPFairStatus","UPFairStatusLabel","FairMikrotikRate","FairMikrotikRateLabel"]);
		if(Form1.getItemValue("UPFairStatus")=='Yes'){
			FormEnableItem(Form1,["UPFairStatus","FairMikrotikRate"]);
			Form1.getInput("UPFairStatus").style.backgroundColor="#00FF66";
			Form1.getInput("FairMikrotikRate").style.backgroundColor="#C6BAFF";
			IsMikrotikRateSet=true;
			
		}
		else{
			FormDisableItem(Form1,["UPFairStatus","FairMikrotikRate"]);
			Form1.getInput("UPFairStatus").style.backgroundColor="#FFFFFF";
			Form1.getInput("FairMikrotikRate").style.backgroundColor="#FFFFFF";
		}
	}
	else{
		FormHideItem(Form1,["UPFairStatus","UPFairStatusLabel","FairMikrotikRate","FairMikrotikRateLabel"]);
	}
	
	var IsOnGiftTraffic=false;
	var IsOnGiftTime=false;
	
	if((Form1.getItemValue("User_Gift_Id")!=0)&&(Form1.getItemValue("GiftEndDT")>Form1.getItemValue("CurrentDT"))){
		Form1.enableItem("GiftEndDT");
		Form1.enableItem("GiftExtraTi");
		Form1.enableItem("GiftExtraTr");
		if(Form1.getItemValue("GiftStopOnTrFinish")=='Yes')
			Form1.setItemLabel("GiftExtraTrLabel","ترافیک <span class='tooltip'>*<span class='tooltiptext'>پس از اتمام ترافیک،هدیه خاتمه مییابد</span></span>");
		Form1.enableItem("GiftTimeRate");
		Form1.enableItem("GiftTrafficRate");
		Form1.enableItem("GiftMikrotikRate");
		
		Form1.getInput("GiftEndDT").style.backgroundColor="#66DDFF";
		if(!IsMikrotikRateSet){
			Form1.getInput("GiftMikrotikRate").style.backgroundColor="#C6BAFF";
			IsMikrotikRateSet=true;
		}
		else
			Form1.getInput("GiftMikrotikRate").style.backgroundColor="#FFFFFF";
		
		if(parseFloat(Form1.getItemValue("GiftExtraTi"))>0){
			Form1.getInput("GiftExtraTi").style.backgroundColor="#00FF66";
			Form1.getInput("GiftExtraTi").style.fontWeight="bold";
			IsOnGiftTime=true;
		}
		else{
			Form1.getInput("GiftExtraTi").style.backgroundColor="#FFBBBB";
			Form1.getInput("GiftExtraTi").style.fontWeight="normal";
		}
		
		if(parseFloat(Form1.getItemValue("GiftExtraTr"))>0){
			Form1.getInput("GiftExtraTr").style.backgroundColor="#00FF66";
			Form1.getInput("GiftExtraTr").style.fontWeight="bold";
			IsOnGiftTraffic=true;
		}
		else{
			Form1.getInput("GiftExtraTr").style.backgroundColor="#FFBBBB";
			Form1.getInput("GiftExtraTr").style.fontWeight="normal";
		}
		
		if(parseFloat(Form1.getItemValue("GiftTimeRate"))!=1)
			Form1.getInput("GiftTimeRate").style.backgroundColor="#99DDFF";
		else
			Form1.getInput("GiftTimeRate").style.backgroundColor="#FFFFFF";
		
		if(parseFloat(Form1.getItemValue("GiftTrafficRate"))!=1)
			Form1.getInput("GiftTrafficRate").style.backgroundColor="#99DDFF";
		else
			Form1.getInput("GiftTrafficRate").style.backgroundColor="#FFFFFF";
	}
	else{
		Form1.disableItem("GiftEndDT");
		Form1.disableItem("GiftExtraTi");
		Form1.disableItem("GiftExtraTr");
		Form1.disableItem("GiftTimeRate");
		Form1.disableItem("GiftTrafficRate");
		Form1.disableItem("GiftMikrotikRate");
		if(Form1.getItemValue("GiftEndDT")=="")
			Form1.getInput("GiftEndDT").style.backgroundColor="#FFFFFF";
		else
			Form1.getInput("GiftEndDT").style.backgroundColor="#FFDDDD";
		Form1.getInput("GiftExtraTi").style.fontWeight="normal";
		Form1.getInput("GiftExtraTr").style.fontWeight="normal";
		Form1.getInput("GiftExtraTi").style.backgroundColor="#FFFFFF";
		Form1.getInput("GiftExtraTr").style.backgroundColor="#FFFFFF";
		Form1.setItemLabel("GiftExtraTrLabel","ترافیک");
		Form1.getInput("GiftTimeRate").style.backgroundColor="#FFFFFF";
		Form1.getInput("GiftTrafficRate").style.backgroundColor="#FFFFFF";
		Form1.getInput("GiftMikrotikRate").style.backgroundColor="#FFFFFF";
	}
	SetCreditStyle("Tr",IsOnGiftTraffic);
	SetCreditStyle("Ti",IsOnGiftTime);
}

function Form1DoAfterLoadFail(){
	setTimeout(function(){
		TopToolbar.enableItem("Retrieve_Byte_Second");
		TopToolbar.enableItem("Retrieve_MByte_Minute");
		TopToolbar.enableItem("Retrieve_GByte_Hour");
		TopToolbar.enableItem("ShowExtraInfo");
	},2000);
}	
function SetCreditStyle(Type,IsOnGift){
	var MyValues=["UL","UL","UL","UL","UL"];
	var MyFields=["D","W","M","Y","S"];
	var IsOnExtra=false;
	var IsUnlimit=true;
	for(var i=0;i<MyFields.length;++i){
		Form1.getInput(MyFields[i]+Type+"R").style.fontWeight="normal";
		Form1.getInput(MyFields[i]+Type+"R").style.backgroundColor="#FFFFFF";
		if(Form1.getItemValue(MyFields[i]+Type+"A")!="UL"){
			IsUnlimit=false;
			Form1.enableItem(MyFields[i]+Type+"A");
			Form1.enableItem(MyFields[i]+Type+"U");
			Form1.enableItem(MyFields[i]+Type+"R");
			if(parseFloat(Form1.getItemValue(MyFields[i]+Type+"R"))==0)
				IsOnExtra=true;
		}
		else{
			Form1.disableItem(MyFields[i]+Type+"A");
			Form1.disableItem(MyFields[i]+Type+"U");
			Form1.disableItem(MyFields[i]+Type+"R");
		}
	}

	Form1.getInput("E"+Type+"R").style.fontWeight="normal";
	Form1.getInput("E"+Type+"R").style.backgroundColor="#FFFFFF";
	
	if(IsOnExtra){
		for(var i=0;i<MyFields.length;++i)
			if(Form1.getItemValue(MyFields[i]+Type+"A")!="UL")
				if(parseFloat(Form1.getItemValue(MyFields[i]+Type+"R"))==0)
					Form1.getInput(MyFields[i]+Type+"R").style.backgroundColor="#FFBBBB";
				else
					Form1.getInput(MyFields[i]+Type+"R").style.backgroundColor="#FFDDDD";
		if(parseFloat(Form1.getItemValue("E"+Type+"R"))==0)
			Form1.getInput("E"+Type+"R").style.backgroundColor="#FFBBBB";
		else if(!IsOnGift){
			Form1.getInput("E"+Type+"R").style.fontWeight="bold";
			Form1.getInput("E"+Type+"R").style.backgroundColor="#00FF66";
		}
		else
			Form1.getInput("E"+Type+"R").style.backgroundColor="#BBFFCC";
	}
	else{
		if(IsUnlimit){
			Form1.enableItem("S"+Type+"A");
			Form1.enableItem("S"+Type+"U");
			Form1.enableItem("S"+Type+"R");
			if(!IsOnGift){
				Form1.getInput("S"+Type+"R").style.fontWeight="bold";
				Form1.getInput("S"+Type+"R").style.backgroundColor="#00FF66";
			}
			else
				Form1.getInput("S"+Type+"R").style.backgroundColor="#BBFFCC";
		}
		else{
			for(var i=0;i<MyFields.length;++i)
				if(Form1.getItemValue(MyFields[i]+Type+"A")!="UL")
					if(parseFloat(Form1.getItemValue(MyFields[i]+Type+"R"))==0)
						Form1.getInput(MyFields[i]+Type+"R").style.backgroundColor="#FFBBBB";
					else if(!IsOnGift){
						Form1.getInput(MyFields[i]+Type+"R").style.fontWeight="bold";
						Form1.getInput(MyFields[i]+Type+"R").style.backgroundColor="#00FF66";
					}
					else
						Form1.getInput(MyFields[i]+Type+"R").style.backgroundColor="#BBFFCC";
		}
		
		if(parseFloat(Form1.getItemValue("E"+Type+"R"))==0)
			Form1.getInput("E"+Type+"R").style.backgroundColor="#FFDDDD";
		else
			Form1.getInput("E"+Type+"R").style.backgroundColor="#BBFFCC";
	}
	if(parseFloat(Form1.getItemValue("E"+Type+"A"))==0){
		Form1.disableItem("E"+Type+"A");
		Form1.disableItem("E"+Type+"U");
		Form1.disableItem("E"+Type+"R");
	}else{
		Form1.enableItem("E"+Type+"A");
		Form1.enableItem("E"+Type+"U");
		Form1.enableItem("E"+Type+"R");
	}
}

}
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>