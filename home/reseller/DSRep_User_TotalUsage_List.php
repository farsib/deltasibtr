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


//VARIABLE ------------------------------------------------------------------------------------------------------------------------------	
	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	
	// Layout   ===================================================================
	
	if(!ISValidResellerSession()) return;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);	
	
	var ReportRecordCount=0;//////////temporary to fix lots of informartion bug
	var DataTitle="Report Payment Added";
	var DataName="DSRep_User_TotalUsage_";
	var MyPostString="";
	var ExtraUserInfoPostStr="";
	var RenderFile=DataName+"ListRender";

	var GColIds="";
	var GColHeaders="";
	var ISFilter=false;
	var FilterState=false;
	var GColFilterTypes=[];
	var GColFooter="";
	var GColInitWidths="";
	var GColAligns="";
	var GColTypes="";
	var GColVisibilitys=[];
	var ISSort=true;
	var GColSorting="";
	var ColSortIndex=0;
	var SortDirection='desc';
	var FilterRowNumber=0;	
	var OptionCount=4;
	var GroupByCount=0;
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATESTR(NOW())');?>";

	var HeaderAlignment = [];
	

	var TheFieldsItems=[
			["نام کاربری","شناسه کاربر"],
			["تاریخ ثبت","تاریخ مصرف"],
			["ترافیک ارسال واقعی","ترافیک دریافت واقعی","ترافیک مصرف شده در حالت اتمام","ترافیک مصرف شده در حالت اشکال","مجموع","ساعت ۰-۱","ساعت ۱-۲","ساعت ۲-۳","ساعت ۳-۴","ساعت ۴-۵","ساعت ۵-۶","ساعت ۶-۷","ساعت ۷-۸","ساعت ۸-۹","ساعت ۹-۱۰","ساعت ۱۰-۱۱","ساعت ۱۱-۱۲","ساعت ۱۲-۱۳","ساعت ۱۳-۱۴","ساعت ۱۴-۱۵","ساعت ۱۵-۱۶","ساعت ۱۶-۱۷","ساعت ۱۷-۱۸","ساعت ۱۸-۱۹","ساعت ۱۹-۲۰","ساعت ۲۰-۲۱","ساعت ۲۱-۲۲","ساعت ۲۲-۲۳","ساعت ۲۳-۲۴"],
			["زمان استفاده شده واقعی","زمان استفاده شده در حالت اتمام","زمان استفاده شده در حالت","مجموع","ساعت ۰-۱","ساعت ۱-۲","ساعت ۲-۳","ساعت ۳-۴","ساعت ۴-۵","ساعت ۵-۶","ساعت ۶-۷","ساعت ۷-۸","ساعت ۸-۹","ساعت ۹-۱۰","ساعت ۱۰-۱۱","ساعت ۱۱-۱۲","ساعت ۱۲-۱۳","ساعت ۱۳-۱۴","ساعت ۱۴-۱۵","ساعت ۱۵-۱۶","ساعت ۱۶-۱۷","ساعت ۱۷-۱۸","ساعت ۱۸-۱۹","ساعت ۱۹-۲۰","ساعت ۲۰-۲۱","ساعت ۲۱-۲۲","ساعت ۲۲-۲۳","ساعت ۲۳-۲۴"],
			["ارائه دهنده مجازی اینترنت","مرکز","نماینده فروش","پشتیبان","سرویس پایه فعال"],
			["مصرف سال","مصرف ماه","مصرف روز","نام کاربری","ارائه دهنده مجازی اینترنت","مرکز","نماینده فروش","پشتیبان","سرویس پایه فعال"]
		];
		
	var ComparisionItems=[
		{text: "=", value: "E"},
		{text: "<>", value: "NE"},
		{text: "<", value: "L"},
		{text: ">", value: "G"},
		{text: "<=", value: "LE"},
		{text: ">=", value: "GE"},
		{text: "مشابه", value: "Like"},
		{text: "غیر مشابه", value: "notLike"},
		{text: "باشد", value: "in"},
		{text: "نباشد", value: "notin"}
	];
		
//CONTROLS------------------------------------------------------------------------------------------------------------------------------
	//=======Popup1 Filter
	var Popup1;
	var PopupId1=['Filter'];//popup Attach to Which Buttom of Toolbar
	//=======Form1 Filter
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  = {	Username:'Username'};
	var Form1FieldHelpId=['Username'];
    var Form1Str = [];
	
	var BlockTMP1 = [];
	var BlockTMP2 = [];
	
		
	
	BlockTMP1 = [];
	for(i=0;i<TheFieldsItems.length;++i){
		BlockTMP1[i] = [];
		for(j=0;j<TheFieldsItems[i].length;j++)
			BlockTMP1[i].push({text: TheFieldsItems[i][j], value: j});
	}
	BlockTMP2=[
		{type: "block", width:800,style:"border-bottom:1px solid", list:[
			{type: "select", label:"لطفا نوع گزارش را انتخاب کنید : ", name: "ReportType", required:true, inputWidth:150, options:[
				{text: 'ترافیک', value: "Traffic"},
				{text: 'زمان', value: "Time"}
			]},
			{type: "label", label:""}
		]},
		{type: "block", list:[
			{type: "checkbox",name:"Chk00",position:"absolute",checked:false, list:[
				{type: "select", name: "Field00", inputWidth:150, options:BlockTMP1[0] },
				{type: "newcolumn"},
				{type: "select", name: "Comp00", options: ComparisionItems.slice(0,8) , inputWidth:60},
				{type: "newcolumn"},
				{type: "input" , name: "Value00", maxLength:32,inputWidth:101} ,
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk01",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt0", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt0", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field01", inputWidth:150, options:BlockTMP1[0]},
					{type: "newcolumn"},
					{type: "select", name: "Comp01", options: ComparisionItems.slice(0,8) , inputWidth:60, required:true},
					{type: "newcolumn"},
					{type: "input" , name: "Value01", maxLength:32,inputWidth:95}
				]}
			]}
		]},
		{type: "block", list:[
			{type: "checkbox",name:"Chk10",position:"absolute",checked:false, list:[
				{type: "select", name: "Field10", inputWidth:150, options:BlockTMP1[1]},
				{type: "newcolumn"},
				{type: "select", name: "Comp10", options: ComparisionItems.slice(0,6) , inputWidth:60},			
				{type: "newcolumn"},
				{type: "input" , name: "Value10", value: TNow, validate:"IsValidDate", maxLength:10,inputWidth:101},			
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk11",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt1", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt1", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field11", inputWidth:150, options:BlockTMP1[1].slice(0,4)},
					{type: "newcolumn"},
					{type: "select", name: "Comp11", options: ComparisionItems.slice(0,6) , inputWidth:60},			
					{type: "newcolumn"},
					{type: "input" , name: "Value11", value: TNow, validate:"IsValidDate", maxLength:10,inputWidth:95}
				]}
			]}
		]},
		{type: "block", name:"Traffic_Block",list:[
			{type: "checkbox",name:"Chk20_Traffic",position:"absolute",checked:false, list:[
				{type: "select", name: "Field20_Traffic", inputWidth:150, options:BlockTMP1[2] },
				{type: "newcolumn"},
				{type: "select", name: "Comp20_Traffic", options: ComparisionItems.slice(0,6) , inputWidth:60},
				{type: "newcolumn"},
				{type: "input" , name: "Value20_Traffic", value: 0, inputWidth:65, value: 0, maxLength: 9, validate: "NotEmpty,ValidInteger"},
				{type: "newcolumn"},
				{type: "label", label:"مگابایت", labelWidth:30},
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk21_Traffic",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt2_Traffic", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt2_Traffic", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field21_Traffic", inputWidth:150, options:BlockTMP1[2]},
					{type: "newcolumn"},
					{type: "select", name: "Comp21_Traffic", options: ComparisionItems.slice(0,6) , inputWidth:60},
					{type: "newcolumn"},
					{type: "input" , name: "Value21_Traffic", value: 0, inputWidth:65, value: 0, maxLength: 9, validate: "NotEmpty,ValidInteger"},
					{type: "newcolumn"},
					{type: "label", label:"مگابایت", labelWidth:24},					
				]}
			]}			
		]},
		{type: "block", name:"Time_Block", hidden:true, list:[
			{type: "checkbox",name:"Chk20_Time",position:"absolute",checked:false, list:[
				{type: "select", name: "Field20_Time", inputWidth:150, options:BlockTMP1[3] },
				{type: "newcolumn"},
				{type: "select", name: "Comp20_Time", options: ComparisionItems.slice(0,6) , inputWidth:60},
				{type: "newcolumn"},
				{type: "input" , name: "Value20_Time", value: 0, inputWidth:65, value: 0, maxLength: 9, validate: "NotEmpty,ValidInteger"},
				{type: "newcolumn"},
				{type: "label", label:"ثانیه", labelWidth:30},
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk21_Time",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt2_Time", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt2_Time", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field21_Time", inputWidth:150, options:BlockTMP1[3]},
					{type: "newcolumn"},
					{type: "select", name: "Comp21_Time", options: ComparisionItems.slice(0,6) , inputWidth:60},
					{type: "newcolumn"},
					{type: "input" , name: "Value21_Time", value: 0, inputWidth:65, value: 0, maxLength: 9, validate: "NotEmpty,ValidInteger"},
					{type: "newcolumn"},
					{type: "label", label:"ثانیه", labelWidth:24},					
				]}
			]}			
		]},
		{type: "block", list:[
			{type: "checkbox",name:"Chk3",position:"absolute",checked:false, list:[
				{type: "select", name: "Field3", inputWidth:150, options:BlockTMP1[4] },
				{type: "newcolumn"},
				{type: "select", name: "Comp3", options: ComparisionItems.slice(8) , inputWidth:60},
				{type: "newcolumn"},
				{type: "multiselect", name:"Value30",
					connector: RenderFile+".php?"+un()+"&act=SelectVisp",inputWidth:516,inputHeight: 70,
					note: {text: "<span style='direction:rtl;float:right;'>با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید</span>"}
				},					
				{type: "multiselect", hidden: true, name:"Value31",
					connector: RenderFile+".php?"+un()+"&act=SelectCenter",inputWidth:516,inputHeight: 70,
					note: {text: "<span style='direction:rtl;float:right;'>با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید</span>"}
				},				
				{type: "multiselect", hidden: true, name:"Value32",
					connector: RenderFile+".php?"+un()+"&act=SelectReseller",inputWidth:516,inputHeight: 70,
					note: {text: "<span style='direction:rtl;float:right;'>با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید</span>"}
				},
				{type: "multiselect", hidden: true, name:"Value33",
					connector: RenderFile+".php?"+un()+"&act=SelectSupporter",inputWidth:516,inputHeight: 70,
					note: {text: "<span style='direction:rtl;float:right;'>با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید</span>"}
				},
				{type: "multiselect", hidden: true, name:"Value34",
					connector: RenderFile+".php?"+un()+"&act=SelectActiveServiceBase",inputWidth:516,inputHeight: 70,
					note: {text: "<span style='direction:rtl;float:right;'>با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید</span>"}
				}
			]}
		]}
	];

	Form1Str=[
		{type: "fieldset", name:"F1", width:850, label: " فیلترها", list:BlockTMP2},
		{type: "fieldset", name:"F2", hidden: true, width:850, label: " دسته بندی براساس ", list:[
			{type: "block", width:800, list:[
				{type: "checkbox",name:"ChkGroupBy0", checked:true, hidden:true},
				{type: "select", name: "GroupBy0", position: "label-left",label:"دسته بندی براساس :",labelAlign:"left",labelWidth:60, inputWidth:270, options:BlockTMP1[5]},
				{type: "newcolumn", offset:50},
				{type: "checkbox",name:"ChkGroupBy1",position:"absolute",checked:false, list:[
					{type: "select", name: "GroupBy1", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:90, inputWidth:270, options:BlockTMP1[5]}
				]}
			]}
		]},
		{type: "block", width: 850, list:[
				{type: "button", name: "ToggleGroupBy", disabled:true, value: " تنظیم دسته بندی ", width:100},		
				{type: "newcolumn", offset:20},
				{type: "button",name: "Cancel",value: " لغو ",width :70},
				{type: "newcolumn", offset:20},				
				{type: "checkbox",name:"ExtraUsersInfo",label:"نمایش اطلاعات بیشتر کاربران", position: "label-right",labelWidth:140},
				{type: "newcolumn", offset:10},
				{type: "checkbox", label: "نمایش مجموع ردیف ها", position: "label-right", name: "TotalRow", checked:false},
				{type: "newcolumn", offset:10},
				{type: "checkbox", label: "قالب داده", position: "label-right", name: "FormatData", checked:true},
				{type: "newcolumn", offset:10},
				{type: "select", label:"ستون ثابت:", name: "SplitColumnsCount",labelAlign:"right",labelWidth:80, inputWidth:32, options:[
					{text: '0', value: 0,selected:true},
					{text: '1', value: 1},
					{text: '2', value: 2},				
					{text: '3', value: 3},
					{text: '4', value: 4},
					{text: '5', value: 5},
					{text: '6', value: 6}
				]},
				{type: "newcolumn", offset:5},				
				{type: "button", name: "GoToReport", disabled:true, value:"در حال بارگذاری("+OptionCount+")",width :80}
		]}
	];
	


	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);

	AddPopupFilter();
	
	
	
	var opts1 = [
		['SaveToXLSX', 'obj', 'XLSX'],
		['SaveToCSV', 'obj', 'CSV']
	];
	
	ToolbarOfGrid.addButtonSelect('SaveToFile',null, 'ذخیره در فایل', opts1, "ds_SaveToFile.png", "ds_SaveToFile_dis.png",false,true,6,'button');
	ToolbarOfGrid.setWidth("SaveToFile",100);
	for(var i=0;i<opts1.length;++i)
		ToolbarOfGrid.setListOptionImage("SaveToFile",opts1[i][0],"ds_"+opts1[i][0]+".png");
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGridOnClick);
	ToolbarOfGrid.disableItem('SaveToFile');
	
	Popup1.show("Filter");

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);	
	//-*********************************************************************************	
	
//FUNCTIONS------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------AddPopupFilter()
function AddPopupFilter(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Filter","تنظیم فیلتر","tow_Filter");
	ToolbarOfGrid.setItemToolTip("Filter","فیلترها را برای ایجاد گزارش تنظیم کنید");
	Popup1=DSInitialPopup(ToolbarOfGrid,PopupId1,Popup1OnShow);
	ToolbarOfGrid.disableItem('Filter');
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onBeforeChange",Form1OnBeforeChange);

	Form1.attachEvent("onOptionsLoaded", function(name,aaa){
		OptionCount--;
		if(OptionCount<=0){
			setTimeout(function(){
				Form1.setItemLabel("GoToReport"," برو به گزارش ");
				Form1.enableItem("GoToReport");
				Form1.enableItem("ToggleGroupBy");
				ToolbarOfGrid.enableItem('Filter')}
			,500)
		}
		else
			Form1.setItemLabel("GoToReport","در حال بارگذاری("+OptionCount+")");
	});
	Form1.setItemValue("GroupBy0",0);
	Form1.setItemValue("GroupBy1",1);
	GroupByCount=0;
}

//-------------------------------------------------------------------Popup1OnShow()
function Popup1OnShow(){
	if(!ISValidResellerSession()) return;
}

//-------------------------------------------------------------------Form1OnBeforeChange(id, value,New_value)
function Form1OnBeforeChange(id,value,New_value){
	// alert("Before:\nid:'"+id+"'\nvalue:'"+value+"'\nNew_value:'"+New_value+"'");
	if(id=="ReportType"){
		Form1.hideItem(value+"_Block");		
		Form1.showItem(New_value+"_Block");
	}
	else if(id=='Field3'){
		Form1.hideItem("Value3"+value);
		Form1.showItem("Value3"+New_value);
	}
	return true;
}

//-------------------------------------------------------------------Form1OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='Cancel') {
		Popup1.hide();
	}
	else if(name=='ToggleGroupBy'){
		if(GroupByCount){
			Form1.hideItem('F2');
			Form1.enableItem("ExtraUsersInfo");
			Form1.enableItem("TotalRow");
			Form1.enableItem("SplitColumnsCount");			
		}
		else{
			Form1.showItem('F2');
			Form1.disableItem("ExtraUsersInfo");
			Form1.disableItem("TotalRow");
			Form1.disableItem("SplitColumnsCount");			
		}
		GroupByCount=1-GroupByCount;
	}
	else{//GoToReport Clicked
		//if(DSFormValidate(Form1,Form1FieldHelpId))
		//{
			Form1.disableItem("GoToReport");
			//dhxLayout.progressOn();
			Popup1.hide();
			var ReportType=Form1.getItemValue("ReportType");
			MyPostString=CreatePostStr();			
			ExtraUserInfoPostStr="&ExtraUsersInfo="+(Form1.getItemValue("ExtraUsersInfo")?"1":"0");
			// dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=list&req=GetRecordCount&SortField=&SortOrder="+MyPostString,
			// function(loader){
				// response=loader.xmlDoc.responseText;
				// response=CleanError(response);
				// if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				// else if(response=='0'){
					// dhtmlx.alert("Not found any item with selected filter(s)");
					// Form1.enableItem("GoToReport");
				// }
				// else{
					//ReportRecordCount=response;
					if(GroupByCount)//{
						// if(ReportRecordCount>4000){
							// dhtmlx.message({title: "هشدار",title: "Attention",type: "alert-warning",text: ReportRecordCount+" records matched. Number of records is more than 4000 and group by is not available for this number of record in this version. Please limit your filter and try again later!"});
							// Form1.enableItem("GoToReport");
							// dhxLayout.progressOff();
							// return;
						// }
						SetGroupByGridLayout(ReportType);
					//}
					else
						SetNormalGridLayout(ReportType);
					
					
					InititalGridList();
					MyLoadGridDataFromServer();
					ToolbarOfGrid.enableItem('SaveToFile');
				// }
				
				Form1.enableItem("GoToReport");
				// dhxLayout.progressOff();				
			// });
		// }
	}
}

//-------------------------------------------------------------------CreatePostStr()
function CreatePostStr(){	
	var FieldValue;
	var ReportType=Form1.getItemValue("ReportType");
	var Out="&ReportType="+ReportType;
	if(Form1.getItemValue("Chk00")){
		Out+="&Chk00=1";
		Out+="&Field00="+Form1.getItemValue("Field00");
		Out+="&Comp00="+Form1.getItemValue("Comp00");
		Out+="&Value00="+Form1.getItemValue("Value00");		
		if(Form1.getItemValue("Chk01")){
			Out+="&Chk01=1";
			Out+="&Opt0="+Form1.getItemValue("Opt0");			
			Out+="&Field01="+Form1.getItemValue("Field01");
			Out+="&Comp01="+Form1.getItemValue("Comp01");
			Out+="&Value01="+Form1.getItemValue("Value01");		
		}
		else
			Out+="&Chk01=0";			
	}
	else
		Out+="&Chk00=0";
	if(Form1.getItemValue("Chk10")){
		Out+="&Chk10=1";
		Out+="&Field10="+Form1.getItemValue("Field10");
		Out+="&Comp10="+Form1.getItemValue("Comp10");
		Out+="&Value10="+Form1.getItemValue("Value10");		
		if(Form1.getItemValue("Chk11")){
			Out+="&Chk11=1";
			Out+="&Opt1="+Form1.getItemValue("Opt1");
			Out+="&Field11="+Form1.getItemValue("Field11");
			Out+="&Comp11="+Form1.getItemValue("Comp11");
			Out+="&Value11="+Form1.getItemValue("Value11");		
		}
		else
			Out+="&Chk11=0";			
	}
	else
		Out+="&Chk10=0";
	if(Form1.getItemValue("Chk20_"+ReportType)){
		Out+="&Chk20=1";
		Out+="&Field20="+Form1.getItemValue("Field20_"+ReportType);
		Out+="&Comp20="+Form1.getItemValue("Comp20_"+ReportType);
		Out+="&Value20="+Form1.getItemValue("Value20_"+ReportType);		
		if(Form1.getItemValue("Chk21_"+ReportType)){
			Out+="&Chk21=1";
			Out+="&Opt2="+Form1.getItemValue("Opt2_"+ReportType);
			Out+="&Field21="+Form1.getItemValue("Field21_"+ReportType);
			Out+="&Comp21="+Form1.getItemValue("Comp21_"+ReportType);
			Out+="&Value21="+Form1.getItemValue("Value21_"+ReportType);		
		}
		else
			Out+="&Chk21=0";			
	}
	else
		Out+="&Chk20=0";


	if(Form1.getItemValue("Chk3")){
		Out+="&Chk3=1";
		FieldValue=Form1.getItemValue("Field3");
		Out+="&Field3="+FieldValue;
		Out+="&Comp3="+Form1.getItemValue("Comp3");
		Out+="&Value3="+Form1.getItemValue("Value3"+FieldValue);		
	}
	else
		Out+="&Chk3=0";
	


	if(GroupByCount)
		for(i=0;i<2;++i){
			if(Form1.getItemValue("ChkGroupBy"+i))
				Out+="&ChkGroupBy"+i+"=1&GroupBy"+i+"="+Form1.getItemValue("GroupBy"+i);
			else{
				Out+="&ChkGroupBy"+i+"=0";
				break;
			}
		}
	else 
		Out+="&ChkGroupBy0=0";
	
	if(Form1.getItemValue("FormatData"))
		Out+="&FormatData=1";
	else
		Out+="&FormatData=0";
	return Out;
	
}

//-------------------------------------------------------------------SetNormalGridLayout()
function SetNormalGridLayout(ReportType){
	var ExtraUsersField=Form1.getItemValue("ExtraUsersInfo");
	ISSort=true;
	ColSortIndex=0;
	SortDirection='desc';
	
	var HaveFooter=Form1.getItemValue("TotalRow");
	if(HaveFooter){
		GColFooter="<div id='FooterField_0' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>زمان ثبت</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ مصرف</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام کاربری</div>"+
		(ExtraUsersField?
			(
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>ارائه دهنده مجازی اینترنت</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نماینده فروش</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>مرکز</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>پشتیبان</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام خانوادگی</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>سازمان</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>سرویس پایه فعال</div>"
			)
		:"")+
		",<div id='FooterField_1' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_2' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_3' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_4' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_5' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_6' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_7' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_8' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_9' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_10' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_11' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_12' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_13' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_14' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_15' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_16' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_17' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_18' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_19' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_20' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_21' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_22' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_23' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_24' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_25' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_26' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_27' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_28' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_29' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>";
		
		GColHeaders="شناسه";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GColFooter="";
	}
	if(ReportType=="Traffic"){		
		GColHeaders+=",زمان ثبت,تاریخ مصرف,نام کاربری"+
			(ExtraUsersField?",ارائه دهنده مجازی اینترنت,نماینده فروش,مرکز,پشتیبان,نام,نام خانوادگی,سازمان,سرویس پایه فعال":"")+
			",ترافیک ارسال واقعی,ترافیک دریافت واقعی,ترافیک مصرف شده در حالت اتمام,ترافیک مصرف شده در حالت اشکال,مجموع"+
			",ساعت ۰-۱,ساعت ۱-۲,ساعت ۲-۳,ساعت ۳-۴,ساعت ۴-۵,ساعت ۵-۶,ساعت ۶-۷,ساعت ۷-۸,ساعت ۸-۹,ساعت ۹-۱۰,ساعت ۱۰-۱۱,ساعت ۱۱-۱۲"+
			",ساعت ۱۲-۱۳,ساعت ۱۳-۱۴,ساعت ۱۴-۱۵,ساعت ۱۵-۱۶,ساعت ۱۶-۱۷,ساعت ۱۷-۱۸,ساعت ۱۸-۱۹,ساعت ۱۹-۲۰,ساعت ۲۰-۲۱,ساعت ۲۱-۲۲,ساعت ۲۲-۲۳,ساعت ۲۳-۲۴";	
		
		GColIds="DailyUsage_Id,CreateDT,UsageDate,Username"+
			(ExtraUsersField?",Visp,Reseller,Center,Supporter,Name,Family,Organization,Hservice.ServiceName":"")+
			",RealSendTr,RealReceiveTr,FinishUsedTr,BugUsedTr,Total"+
			",HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11"+
			",HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23";
			
		GColInitWidths="100,140,120,120"+
			(ExtraUsersField?",135,120,120,120,100,100,160,200":"")+
			",210,210,210,210,210"+
			",210,210,210,210,210,210,210,210,210,210,210,210"+
			",210,210,210,210,210,210,210,210,210,210,210,210";
		
		var FieldCount=33+(ExtraUsersField?8:0);
	}
	else{
		GColHeaders+=",زمان ثبت,تاریخ مصرف,نام کاربری"+
			(ExtraUsersField?",ارائه دهنده مجازی اینترنت,نماینده فروش,مرکز,پشتیبان,نام,نام خانوادگی,سازمان,سرویس پایه فعال":"")+
			",زمان مصرف شده واقعی,زمان مصرف شده در حالت اتمام,زمان مصرف شده در حالت اشکال,مجموع"+
			",ساعت ۰-۱,ساعت ۱-۲,ساعت ۲-۳,ساعت ۳-۴,ساعت ۴-۵,ساعت ۵-۶,ساعت ۶-۷,ساعت ۷-۸,ساعت ۸-۹,ساعت ۹-۱۰,ساعت ۱۰-۱۱,ساعت ۱۱-۱۲"+
			",ساعت ۱۲-۱۳,ساعت ۱۳-۱۴,ساعت ۱۴-۱۵,ساعت ۱۵-۱۶,ساعت ۱۶-۱۷,ساعت ۱۷-۱۸,ساعت ۱۸-۱۹,ساعت ۱۹-۲۰,ساعت ۲۰-۲۱,ساعت ۲۱-۲۲,ساعت ۲۲-۲۳,ساعت ۲۳-۲۴";	
		
		GColIds="DailyUsage_Id,CreateDT,UsageDate,Username"+
			(ExtraUsersField?",Visp,Reseller,Center,Supporter,Name,Family,Organization,Hservice.ServiceName":"")+
			",RealUsedTime,FinishUsedTi,BugUsedTi,Total"+
			",HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11"+
			",HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23";
			
		GColInitWidths="100,140,120,120"+
			(ExtraUsersField?",135,120,120,120,100,100,160,200":"")+
			",210,210,210,210"+
			",210,210,210,210,210,210,210,210,210,210,210,210"+
			",210,210,210,210,210,210,210,210,210,210,210,210";
		
		var FieldCount=32+(ExtraUsersField?8:0);

	}
	GColAligns="center";
	GColSorting="server";
	GColTypes="ro";
	GColVisibilitys = [1];
	HeaderAlignment = ["text-align:center"];
	for(i=1;i<FieldCount;++i){
		GColAligns+=",center";
		GColSorting+=",server";
		GColTypes+=",ro";
		GColVisibilitys.push(1);
		HeaderAlignment.push("text-align:center");
	}
}

//-------------------------------------------------------------------SetGroupByGridLayout()
function SetGroupByGridLayout(ReportType){
	ISSort=false;
	var tmp;
	
	GColHeaders="{#stat_count} ردیف";
	GColFooter="";
	
	
	
	GColIds="Row_Number";
	GColInitWidths="90";
	ColSortIndex=1;
	GColAligns="center";
	GColTypes="ra";
	GColVisibilitys = [1];
	HeaderAlignment = ["text-align:center"];
	// console.log(TheFieldsItems);
	for(i=0;i<2;++i)
		if(Form1.getItemValue("ChkGroupBy"+i)){
			tmp=Form1.getItemValue("GroupBy"+i);
			// console.log("GroupBy"+i+":"+tmp);
			GColIds+=","+TheFieldsItems[5][tmp];
			GColHeaders+=","+TheFieldsItems[5][tmp];
			GColInitWidths+=",200";
			GColAligns+=",center";
			GColTypes+=",ro";
			GColVisibilitys.push(1);
			HeaderAlignment.push("text-align:center");
		}
		else
			break;
	
	var Cnt=0;
	if(ReportType=="Traffic"){		
		GColIds+=",RealSendTr,RealReceiveTr,FinishUsedTr,BugUsedTr,Total"+
					",HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11"+
					",HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23";
		GColHeaders+=",ترافیک ارسال واقعی,ترافیک دریافت واقعی,ترافیک مصرف شده در حالت اتمام,ترافیک مصرف شده در حالت اشکال,مجموع"+
		",ساعت ۰-۱,ساعت ۱-۲,ساعت ۲-۳,ساعت ۳-۴,ساعت ۴-۵,ساعت ۵-۶,ساعت ۶-۷,ساعت ۷-۸,ساعت ۸-۹,ساعت ۹-۱۰,ساعت ۱۰-۱۱,ساعت ۱۱-۱۲"+
			",ساعت ۱۲-۱۳,ساعت ۱۳-۱۴,ساعت ۱۴-۱۵,ساعت ۱۵-۱۶,ساعت ۱۶-۱۷,ساعت ۱۷-۱۸,ساعت ۱۸-۱۹,ساعت ۱۹-۲۰,ساعت ۲۰-۲۱,ساعت ۲۱-۲۲,ساعت ۲۲-۲۳,ساعت ۲۳-۲۴";
			Cnt=29;
	}
	else{
		GColIds+=",RealUsedTime,FinishUsedTi,BugUsedTi,Total"+
					",HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11"+
					",HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23";
		GColHeaders+=",زمان مصرف شده واقعی,زمان مصرف شده در حالت اتمام,زمان مصرف شده در حالت اشکال,مجموع"+
		",ساعت ۰-۱,ساعت ۱-۲,ساعت ۲-۳,ساعت ۳-۴,ساعت ۴-۵,ساعت ۵-۶,ساعت ۶-۷,ساعت ۷-۸,ساعت ۸-۹,ساعت ۹-۱۰,ساعت ۱۰-۱۱,ساعت ۱۱-۱۲"+
			",ساعت ۱۲-۱۳,ساعت ۱۳-۱۴,ساعت ۱۴-۱۵,ساعت ۱۵-۱۶,ساعت ۱۶-۱۷,ساعت ۱۷-۱۸,ساعت ۱۸-۱۹,ساعت ۱۹-۲۰,ساعت ۲۰-۲۱,ساعت ۲۱-۲۲,ساعت ۲۲-۲۳,ساعت ۲۳-۲۴";
		Cnt=28;
	}
	for(i=1;i<=Cnt;++i){
		GColInitWidths+=",177";
		GColAligns+=",center";
		GColTypes+=",ro";
		GColVisibilitys.push(1);
		HeaderAlignment.push("text-align:center");
	}

}

//-------------------------------------------------------------------InititalGridList()
function InititalGridList(){
	mygrid =dhxLayout.cells("a").attachGrid();
	mygrid.setSkin(grid_main_skin);
    mygrid.setImagePath(grid_image_path);
	mygrid.setColumnIds(GColIds);
    mygrid.setHeader(GColHeaders,null,HeaderAlignment);
    mygrid.setInitWidths(GColInitWidths);
    mygrid.setColAlign(GColAligns);
	mygrid.setColTypes(GColTypes);
	
	for (var i=0;i<GColVisibilitys.length;i++){
		if(GColVisibilitys[i]==0)
			mygrid.setColumnHidden(i,true);
	}
	
	if (ISSort) mygrid.setColSorting(GColSorting);

	if (GColFooter != '') mygrid.attachFooter(GColFooter);
	mygrid.init();	

    if (ISSort){
		mygrid.setSortImgState(true,ColSortIndex,SortDirection);
		mygrid.attachEvent("onBeforeSorting",function(ind,type,direction){
			mygrid.setSortImgState(true,ind,direction);
			ColSortIndex=ind;
			SortDirection=((direction=='asc')?'asc':'desc');
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+MyPostString+ExtraUserInfoPostStr,DoAfterRefresh);
		});
	}
	
	if(!GroupByCount){
		var FreezeCount=Form1.getItemValue("SplitColumnsCount");
		if(FreezeCount>0) mygrid.splitAt(FreezeCount);
		mygrid.enableSmartRendering(true,100);
		mygrid.enablePreRendering(50);
	}
	mygrid.attachEvent("onRowDblClicked",function(id){PopupWindow(id)});

}

//-------------------------------------------------------------------MyLoadGridDataFromServer()
function MyLoadGridDataFromServer(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+MyPostString+ExtraUserInfoPostStr,DoAfterRefresh);
	if(GColFooter!=''){
		dhtmlxAjax.get(RenderFile+".php?act=list&req=GetFooterInfo&SortField=&SortOrder="+MyPostString+ExtraUserInfoPostStr,
			function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				else{
					ResponseList=response.split("`");
					document.getElementById("FooterField_0").innerHTML =ResponseList[0].replace("rows", " ردیف");
					for(i=1;i<=ResponseList.length;++i){
						document.getElementById("FooterField_"+i).innerHTML = ResponseList[i];	
					}
				}
			}
		);
	}
}
	
//-------------------------------------------------------------------ToolbarOfGridOnClick()
function ToolbarOfGridOnClick(name){
	if((name=="SaveToCSV")||(name=="SaveToXLSX")){
		if(mygrid.getRowsNum()<=0){
			dhtmlx.message({title: "هشدار",type: "alert-warning",text: "داده ای برای ذخیره موجود نیست"});
			return
		}	
		if(!ISValidResellerSession()) return;
		// if(ReportRecordCount>30000){
			// dhtmlx.message({title: "هشدار",title: "Attention",type: "alert-error",text: ReportRecordCount+" records matched. SaveToFile is available for only 30000 record in this version. Please limit your filter to use save file!"});
			// return;
		// }
		ToolbarOfGrid.disableItem('SaveToFile');
		SortField=mygrid.getColumnId(ColSortIndex);
		
		window.open(RenderFile+".php?act=list&req=SaveToFile&Type="+name.substr(6)+"&SortField="+SortField+"&SortOrder="+SortDirection+MyPostString+ExtraUserInfoPostStr);
		setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile')},2000);
	}
}
	
//-------------------------------------------------------------------PopupWindow(SelectedRowId)
function PopupWindow(SelectedRowId){
	if(typeof (mygrid.getColIndexById("Username")) == 'undefined')
		return;
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue();
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=Username,"+Username, false);
}


}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------DoAfterRefresh()
function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else	
		mygrid.selectRowById(SelectedRowId,false,true,true);
	dhxLayout.progressOff();
	mygrid.callEvent("onGridReconstructed",[]);
}


</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>