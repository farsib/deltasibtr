<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
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
var SelectedRowId=0;
window.onload = function(){

	ParentId="<?php  echo $_GET['ParentId'];  ?>";
	if(ParentId == "" ) {return;}
	ParamItemGroup="<?php  echo $_GET['ParamItemGroup'];  ?>";
	if(ParamItemGroup == "" ) {return;}
	DataTitle="Param";
	DataName="DSParam_";
	ExtraFilter="&TableId="+ParentId+"&ParamItemGroup="+ParamItemGroup;
	RenderFile=DataName+"ListRender";
		GColIds="Param_Id,ParamItemType,ParamStatus,ParamItemName,ServiceInfoName,Value,TableName,TableId";
		GColHeaders="{#stat_count} ردیف,نوع پارامتر,وضعیت پارامتر,نام,سرویس محاسبه,مقدار,نام جدول,شناسه جدول";
		GColFilterTypes=[1,1,1,1,1,1,1];
		GColInitWidths="80,95,100,145,100,120,80,100";
		GColAligns="center,center,center,center,center,left,center,center";
		GColTypes="ro,ro,ro,ro,ro,ro,ro,ro";
		GColVisibilitys=[1,1,1,1,1,1,1,1];
		GColSorting="server,server,server,server,server,server,server";
		ISFilter=true;
		FilterState=false;
		GFooter="";
		ISSort=true;
		ColSortIndex=0;
		SortDirection='Asc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	var InitialId=0;
	//=======Popup2 AddParam
	var Popup2;
	var PopupId2=['AddParam'];//  popup Attach to Which Button of Tool bar

	//=======Form2 AddParam
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {ParamStatus:'Yes: Check next element and replace No: Not check next element(force parameter this value) Ignore: Ignore this parameter '};
	var Form2FieldHelpId=['ParamStatus'];
	var Form2Str = [
		{ type:"settings" , labelWidth:105, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"ParamItemType", label: "نوع پارامتر :",inputWidth:188,required:true, options:[
			{text: "Auth", value: "Auth",selected: true,list:[
				{ type: "select", name:"AuthParamItem_Id",label: "نام :",connector: RenderFile+".php?"+un()+"&act=SelectAuthParamItem"+ExtraFilter,required:true,inputWidth:188}
			]},
			{text: "Acc", value: "Acc",list:[
				{ type: "select", name:"AccParamItem_Id",label: "نام :",connector: RenderFile+".php?"+un()+"&act=SelectAccParamItem"+ExtraFilter,required:true,inputWidth:188}
			]},
			{text: "Helper", value: "Helper",list:[
				{ type: "select", name:"HelperParamItem_Id",label: "نام :",connector: RenderFile+".php?"+un()+"&act=SelectHelperParamItem"+ExtraFilter,required:true,inputWidth:188}
			]},
			{text: "Reply", value: "Reply",list:[
				{ type: "select", name:"ReplyParamItem_Id",label: "نام :",connector: RenderFile+".php?"+un()+"&act=SelectReplyParamItem"+ExtraFilter,required:true,inputWidth:188}
			]},

		]},
		{type: "block", width: 300, list:[
			{ type: "button",name:"Proceed",value: "مرحله بعد",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];
	//=======Popup3 EditParam
	var Popup3;
	var PopupId3=['EditParam'];//  pop-up Attach to Which Button of Tool bar

	//=======Form2 EditParam
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {ParamStatus:'Yes: Check next element and replace No: Not check next element(force parameter this value) Ignore: Ignore this parameter '};
	var Form3FieldHelpId=['ParamStatus'];

	function CreateForm3Str(ParamItemName){
		if(ParamItemName=='Simulation')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "input" , name:"Simulation", label:"مقدار :", note: { text: 'No of concurrent user'},validate:"", labelAlign:"left", maxLength:128,inputWidth:190},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='InterimTime')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "input" , name:"InterimTime", label:"مقدار :", note: { text: 'Acct-Interim-Update second'},validate:"", labelAlign:"left", maxLength:128,inputWidth:190},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='AuthMethod')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"AuthMethod", label: "روش اعتبارسنجی :", options:[{text: "Username-Password", value: "UP",selected: true},{text: "Username-CallerId", value: "UC"},{text: "Username", value: "U"},{text: "Username-Password-CallerId", value: "UPC"},{text: "ActiveDirectory", value: "A"},{text: "ActiveDirectory-CallerId", value: "AC"}],inputWidth:188,required:true},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='UserType')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"UserType", label: "نوع کاربری :", options:[
					{text: "LAN", value: "LAN",selected: true},
					{text: "ADSL", value: "ADSL"},
					{text: "Wireless", value: "Wireless"},
					{text: "Wi-Fi", value: "Wi-Fi"},
					{text: "Dialup", value: "Dialup"},
					{text: "Dialup-PRM", value: "Dialup-PRM"},
					{text: "WiFiMobile", value: "WiFiMobile"},
					{text: "NotLog", value: "NotLog"}
					],inputWidth:188,required:true},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='LoginTime')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"LoginTime_Id",label: "محدودیت زمان ورود :",connector: RenderFile+".php?"+un()+"&act=SelectLoginTime"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='TimeRate')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"ServiceInfo_Id",label: "سرویس محاسبه :",connector: RenderFile+".php?"+un()+"&act=SelectServiceInfo"+ExtraFilter,required:true,inputWidth:188},
				{ type: "select", name:"TimeRate_Id",label: "ضریب زمان :",connector: RenderFile+".php?"+un()+"&act=SelectTimeRate"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='TrafficRate')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"ServiceInfo_Id",label: "سرویس محاسبه :",connector: RenderFile+".php?"+un()+"&act=SelectServiceInfo"+ExtraFilter,required:true,inputWidth:188},
				{ type: "select", name:"TrafficRate_Id",label: "ضریب ترافیک :",connector: RenderFile+".php?"+un()+"&act=SelectTrafficRate"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='SendRate')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"ServiceInfo_Id",label: "سرویس محاسبه :",connector: RenderFile+".php?"+un()+"&act=SelectServiceInfo"+ExtraFilter,required:true,inputWidth:188},
				{ type: "input" , name:"SendRate", label:"ضریب ارسال :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='ReceiveRate')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"ServiceInfo_Id",label: "سرویس محاسبه :",connector: RenderFile+".php?"+un()+"&act=SelectServiceInfo"+ExtraFilter,required:true,inputWidth:188},
				{ type: "input" , name:"ReceiveRate", label:"ضریب دریافت :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='Calendar')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"Calendar", label: "نوع تقویم :", options:[{text: "Jalali", value: "Jalali",selected: true},{text: "Gregorian", value: "Gregorian"}],inputWidth:188,required:true},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='PeriodicUse')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"PeriodicUse", label: "نوع محاسبه دوره :", options:[{text: "Fix", value: "Fix",selected: true},{text: "Relative", value: "Relative"}],inputWidth:188,required:true},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='IPPool')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"IPPool_Id",label: "محدوده های آی پی :",connector: RenderFile+".php?"+un()+"&act=SelectIPPool"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='MikrotikRate')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"MikrotikRate_Id",label: "سرعت میکروتیک :",connector: RenderFile+".php?"+un()+"&act=SelectMikrotikRate"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='SMSProvider')
			var Form3Str = [
				{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"SMSProvider_Id",label: "سرویس دهنده پیامک :",connector: RenderFile+".php?"+un()+"&act=SelectSMSProvider"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='FinishRule')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"FinishRule_Id",label: "قانون اتمام :",connector: RenderFile+".php?"+un()+"&act=SelectFinishRule"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='OffFormula')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"OffFormula_Id",label: "فرمول تخفیف :",connector: RenderFile+".php?"+un()+"&act=SelectOffFormula"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='MaxSessionTime')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "input" , name:"MaxSessionTime", label:"حداکثر زمان نشست :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='URLReporting')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"URLReporting", label: "گزارش صفحات بازدید شده :", options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}],inputWidth:188,required:true},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='ActiveDirectory')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"ActiveDirectory_Id",label: "اکتیودایرکتوری :",connector: RenderFile+".php?"+un()+"&act=SelectActiveDirectory"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='Notify-CreditFinish')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"NotifyCreditFinish_Id",label: "مقدار :",connector: RenderFile+".php?"+un()+"&act=SelectNotifyCreditFinish"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='Notify-ServiceExpire')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"NotifyServiceExpire_Id",label: "مقدار :",connector: RenderFile+".php?"+un()+"&act=SelectNotifyServiceExpire"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='Notify-UserDebit')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"NotifyUserDebit_Id",label: "مقدار :",connector: RenderFile+".php?"+un()+"&act=SelectNotifyUserDebit"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='WebAccess')
			var Form3Str = [
				{ type:"settings" , labelWidth:113, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"WebAccess_Id",label: "محدودیت پنل کاربری :",connector: RenderFile+".php?"+un()+"&act=SelectWebAccess"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام  ",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='DebitControl')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"DebitControl_Id",label: "کنترل بدهی :",connector: RenderFile+".php?"+un()+"&act=SelectDebitControl"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='CalledId')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"CalledId_Id",label: "مک/آی پی سرور :",connector: RenderFile+".php?"+un()+"&act=SelectCalledId"+ExtraFilter,required:true,inputWidth:188},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		else if(ParamItemName=='AutoResetExtraCredit')
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Passthrough-No", value: "Passthrough-No"},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "select", name:"AutoResetExtraCredit", label: "ریست خودکار اعتبار اضافی :", options:[{text: "بلی", value: "Yes"},{text: "خیر", value: "No",selected: true}],inputWidth:188,required:true},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];

		else//ReplyItem
			var Form3Str = [
				{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
				{ type:"hidden" , name:"Param_Id", label:"Param_Id :",disabled:true, labelAlign:"left", inputWidth:130},
				{ type: "select", name:"ParamStatus", label: "وضعیت :", options:[{text: "Passthrough-Yes", value: "Passthrough-Yes",selected: true},{text: "Disable", value: "Disable"}],inputWidth:188,required:true},
				{ type: "input" , name:"ParamItemName", label:"نام :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190,readonly:true},
				{ type: "input" , name:"Value", label:"مقدار :", validate:"", labelAlign:"left", maxLength:128,inputWidth:190},
				{type: "block", width: 320, list:[
					{ type: "button",name:"Proceed",value: "انجام",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
					]}
				];
		return 	Form3Str;

	}

	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	dhxLayout.attachEvent('onContentLoaded', function(){dhxLayout.cells("a").progressOff();});

	dhxLayout.cells("a").progressOn();

	if(ParamItemGroup=='User'){
		ISPermitAddParam=ISPermit("Visp."+ParamItemGroup+".Param.Add");
		ISPermitEditParam=ISPermit("Visp."+ParamItemGroup+".Param.Edit");
		ISPermitDeleteParam=ISPermit("Visp."+ParamItemGroup+".Param.Delete");
		ISPermitRetrieveAll=ISPermit("Visp."+ParamItemGroup+".Param.RetrieveAll");
	}
	else if((ParamItemGroup=='Reseller')||(ParamItemGroup=='Service')){
		ISPermitAddParam=ISPermit("CRM."+ParamItemGroup+".Param.Add");
		ISPermitEditParam=ISPermit("CRM."+ParamItemGroup+".Param.Edit");
		ISPermitDeleteParam=ISPermit("CRM."+ParamItemGroup+".Param.Delete");
		ISPermitRetrieveAll=false;
	}
	else if(ParamItemGroup=='Visp'){
		ISPermitAddParam=ISPermit("Admin.VISPs.Param.Add");
		ISPermitEditParam=ISPermit("Admin.VISPs.Param.Edit");
		ISPermitDeleteParam=ISPermit("Admin.VISPs.Param.Delete");
		ISPermitRetrieveAll=false;
	}
	else if(ParamItemGroup=='Class'){
		ISPermitAddParam=ISPermit("Admin.User.Class.Param.Add");
		ISPermitEditParam=ISPermit("Admin.User.Class.Param.Edit");
		ISPermitDeleteParam=ISPermit("Admin.User.Class.Param.Delete");
		ISPermitRetrieveAll=false;
	}
	else{
		ISPermitAddParam=ISPermit("Admin."+ParamItemGroup+".Param.Add");
		ISPermitEditParam=ISPermit("Admin."+ParamItemGroup+".Param.Edit");
		ISPermitDeleteParam=ISPermit("Admin."+ParamItemGroup+".Param.Delete");
		ISPermitRetrieveAll=false;
	}

	// ToolbarOfGrid   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	if(ISPermitRetrieveAll &&(ParamItemGroup=='User')) DSToolbarAddButton(ToolbarOfGrid,null,"RetrieveAll","همه پارامترها","RetrieveAll",ToolbarOfGrid_OnRetrieveAllClick);
	if(ISFilter==true){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"Filter","فیلتر: غیرفعال","toolbarfilter",ToolbarOfGrid_OnFilterClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterAddRow","افزودن فیلتر جدید","toolbarfilteradd",ToolbarOfGrid_OnFilterAddClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterDeleteRow","حذف فیلتر","toolbarfilterDelete",ToolbarOfGrid_OnFilterDeleteClick);
		ToolbarOfGrid.addSeparator("sep2", null);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.disableItem("Filter");
	}
	if(ISPermitAddParam) AddPopupAddParam();
	if(ISPermitEditParam) AddPopupEditParam();
	if(ISPermitDeleteParam)	DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","Delete",ToolbarOfGrid_OnDeleteClick);


	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);

	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);
			if(input) input.style.width = cWidth-20;
		}
		return true;
	});


    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)

	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);

	if(ISPermitEditParam) mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnDeleteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
		text: "برای حذف مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Param_Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت اعمال شد");
					}
					else alert(response);

				});
			}
		}
	});
}
function Form2onChange(id, value){
}

function Form3onChange(id, value){
}

function AddPopupAddParam(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddParam","افزودن","tow_AddParam");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
}

function AddPopupEditParam(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"EditParam","ویرایش","tow_EditParam");
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	//Form3=DSInitialForm(Popup3,CreateForm3Str(ParamItemName),Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}

function Form2OnButtonClick(name){// Add Param
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Popup2.hide();
			dhxLayout.cells("a").progressOn();
			DSFormInsertRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=insert"+ExtraFilter,Form2DoAfterInsertOk,Form2DoAfterInsertFail);
		}
	}
}

function Form3OnButtonClick(name){// Edit Param
	if(name=='Close') Popup3.hide();
	else{
		if(DSFormValidate(Form3,Form3FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form3,RenderFile+".php?"+un()+"&act=update"+ExtraFilter,Form3DoAfterUpdateOk,Form3DoAfterUpdateFail);
		}
	}
}



function Form2DoAfterInsertOk(RId){
	SelectedRowId=RId;
	InitialId=RId;
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,function(){
		DoAfterRefresh();
		Popup3.show("EditParam");
	});
}

function Form3DoAfterUpdateOk(){
	Popup3.hide();
	dhxLayout.cells("a").progressOff();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterInsertFail(){
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
}

function Form3DoAfterUpdateFail(){
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
}

function Popup2OnShow(){//Add Param
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
}

function SetParamInfo3(){
}

function Popup3OnShow(){//Edit Param

//alert('Popup3OnShow');
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{

		var ParamItemName=mygrid.cells(SelectedRowId,mygrid.getColIndexById("ParamItemName")).getValue();
		Form3=DSInitialForm(Popup3,CreateForm3Str(ParamItemName),Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
		Form3.lock();
		Form3.load(RenderFile+".php?"+un()+"&act=LoadParamForm"+ExtraFilter+"&Param_Id="+SelectedRowId,function(id,respond){
			if(InitialId==SelectedRowId){
				InitialId=0;
				setTimeout(function(){
					Form3.setItemValue("ParamStatus","Passthrough-Yes");
					Form3.unlock();
					Form3.setFocusOnFirstActive();
				},300);
			}
			else
				Form3.unlock();
		//dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetParamInfo"+ExtraFilter+"&ParamItem_Id="+Form3.getItemValue("ParamItem_Id"),SetParamInfo3);
		//Form3.setItemFocus("Value");
		});
	}
}


function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	//alert('show');
	Popup3.show("EditParam");
//	Popup3.show(0,20,0,0);
	//SelectedRowId=rId;
	//PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function ToolbarOfGrid_OnRetrieveAllClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&act2=all",DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else
		ToolbarOfGrid.setItemText("Filter","فبلتر: غیرفعال");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);;
}
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}

	FilterRowNumber++;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function OnFilterTextPressEnter(){
	if(FilterState)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterDeleteClick(){
	DSGridDeleteFilterRow(GColIds,GColFilterTypes);
	FilterRowNumber--;
	if(FilterRowNumber==0){
		mygrid.detachHeader(1);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
		FilterState=false;
		ToolbarOfGrid.disableItem("Filter");
	}
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function ToolbarOfGrid_OnAddClick(){
		PopupWindow(0);
}
function ToolbarOfGrid_OnEditClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		PopupWindow(SelectedRowId);
	}
}

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+SelectedRowId, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServer(RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServer(RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	}
}

function DoAfterRefresh(){
	if((SelectedRowId==null)||(SelectedRowId==0))
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);
	dhxLayout.cells("a").progressOff();
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
