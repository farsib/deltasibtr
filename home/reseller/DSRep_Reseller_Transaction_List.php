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
	

	var DataTitle="Report Transaction";
	var DataName="DSRep_Reseller_Transaction_";
	var MyPostString="";
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
	var GroupByCount=0;	
	var	OptionCount=2;

	var HeaderAlignment = [];
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATETIMESTR(NOW())');?>";
	var DTArr=TNow.split(" ");
	var CurDate=DTArr[0].split("/");
	var CurTime=DTArr[1].split(":");

	var TheFieldsItems=[
			["شناسه تراکنش","نام کاربری","شناسه کاربر"],
			["اعتبار"],
			["نوع تراکنش","ثبت کننده","نماینده فروش","نماینده فروش مرتبط"],
			["سال ایجاد","ماه ایجاد","روز ایجاد","ثبت کننده","نماینده فروش","نوع تراکنش"]
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
	
	var BlockTMP1 = [];
	var BlockTMP2 = [];

	
	BlockTMP1[1]=[];
	for(i=1390;i<=1450;++i)
		BlockTMP1[1].push({text: i, value: i});
	
	BlockTMP1[2]=[];
	for(i=1;i<=12;++i)
		BlockTMP1[2].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP1[3]=[];
	for(i=1;i<=31;++i)
		BlockTMP1[3].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP1[4]=[];
	for(i=0;i<=23;++i)
		BlockTMP1[4].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP1[5]=[];
	for(i=0;i<=59;++i)
		BlockTMP1[5].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP2 = [];
	for(i=0;i<TheFieldsItems.length;++i){
		BlockTMP2[i] = [];
		for(j=0;j<TheFieldsItems[i].length;j++)
			BlockTMP2[i].push({text: TheFieldsItems[i][j], value: j});
	}	
	
		
//CONTROLS------------------------------------------------------------------------------------------------------------------------------
	//=======Popup1 Filter
	var Popup1;
	var PopupId1=['Filter'];//popup Attach to Which Buttom of Toolbar
	//=======Form1 Filter
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  = {	User_Id:'User_Id'};
	var Form1FieldHelpId=['User_Id'];
    var Form1Str = [];
		
	Form1Str=[
		{type: "fieldset", name:"F1", width:850, label: " فیلترها", list:[
			{type: "block", list:[
				{type: "checkbox",name:"Chk0",position:"absolute",checked:false, list:[
					{type: "newcolumn", offset:10},
					{type: "select", name: "Comp0", label: "تاریخ و زمان ثبت :", labelAlign:"left", options: ComparisionItems.slice(5,6) ,labelWidth:146, inputWidth:60},
					{type: "newcolumn"},
					{type: "select", name: "FromYear", options:BlockTMP1[1]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " / ", labelAlign:"left", name: "FromMonth", options:BlockTMP1[2]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " / ", labelAlign:"left", name: "FromDay31", options:BlockTMP1[3],hidden:true},
					{type: "select",label: " / ", labelAlign:"left", name: "FromDay30", options:BlockTMP1[3].slice(0,30),hidden:true},
					{type: "select",label: " / ", labelAlign:"left", name: "FromDay29", options:BlockTMP1[3].slice(0,29),hidden:true},
					{type: "newcolumn", offset:5},
					{type: "select", name: "FromHour", options:BlockTMP1[4]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " : ", labelAlign:"left", name: "FromMinute", options:BlockTMP1[5]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " : ", labelAlign:"left", name: "FromSecond", options:BlockTMP1[5]},
					{type: "newcolumn", offset:5},
				]}
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk1",position:"absolute",checked:false, list:[
					{type: "newcolumn", offset:10},
					{type: "select", name: "Comp1", label: "تاریخ و زمان ثبت :", labelAlign:"left", options: ComparisionItems.slice(4,5) ,labelWidth:146, inputWidth:60},
					{type: "newcolumn"},
					{type: "select", name: "ToYear", options:BlockTMP1[1]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " / ", labelAlign:"left", name: "ToMonth", options:BlockTMP1[2]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " / ", labelAlign:"left", name: "ToDay31", options:BlockTMP1[3],hidden:true},
					{type: "select",label: " / ", labelAlign:"left", name: "ToDay30", options:BlockTMP1[3].slice(0,30),hidden:true},
					{type: "select",label: " / ", labelAlign:"left", name: "ToDay29", options:BlockTMP1[3].slice(0,29),hidden:true},
					{type: "newcolumn", offset:5},
					{type: "select", name: "ToHour", options:BlockTMP1[4]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " : ", labelAlign:"left", name: "ToMinute", options:BlockTMP1[5]},
					{type: "newcolumn", offset:5},
					{type: "select",label: " : ", labelAlign:"left", name: "ToSecond", options:BlockTMP1[5]},
					{type: "newcolumn", offset:5},
				]}
			]},
			{type: "label"},
			{type: "block", list:[
				{type: "checkbox",name:"Chk20",position:"absolute",checked:false, list:[
					{type: "select", name: "Field20", inputWidth:150, options:BlockTMP2[0] },
					{type: "newcolumn"},
					{type: "select", name: "Comp20", options: ComparisionItems.slice(0,8) , inputWidth:60},
					{type: "newcolumn"},
					{type: "input" , name: "Value20", maxLength:32,inputWidth:95} ,
					{type: "newcolumn", offset:15},
					{type: "checkbox",name:"Chk21",position:"absolute",checked:false, list:[
						{type: "radio", name: "Opt2", value: "AND", label: "و",checked:true},
						{type: "newcolumn"},
						{type: "radio", name: "Opt2", value: "OR", label: "یا"},
						{type: "newcolumn"},
						{type: "select", name: "Field21", inputWidth:150, options:BlockTMP2[0]},
						{type: "newcolumn"},
						{type: "select", name: "Comp21", options: ComparisionItems.slice(0,8) , inputWidth:60, required:true},
						{type: "newcolumn"},
						{type: "input" , name: "Value21", maxLength:32,inputWidth:95}
					]}
				]}
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk30",position:"absolute",checked:false, list:[
					{type: "select", name: "Field30", inputWidth:150, options:BlockTMP2[1] },
					{type: "newcolumn"},
					{type: "select", name: "Comp30", options: ComparisionItems.slice(0,6) , inputWidth:60},
					{type: "newcolumn"},
					{type: "input" , name: "Value30", value: 0, validate:"IsValidPrice", maxLength:13,inputWidth:95},
					{type: "newcolumn", offset:15},
					{type: "checkbox",name:"Chk31",position:"absolute",checked:false, list:[
						{type: "radio", name: "Opt3", value: "AND", label: "و",checked:true},
						{type: "newcolumn"},
						{type: "radio", name: "Opt3", value: "OR", label: "یا"},
						{type: "newcolumn"},
						{type: "select", name: "Field31", inputWidth:150, options:BlockTMP2[1]},
						{type: "newcolumn"},
						{type: "select", name: "Comp31", options: ComparisionItems.slice(0,6) , inputWidth:60, required:true},
						{type: "newcolumn"},
						{type: "input" , name: "Value31", value: 0, validate:"IsValidPrice", maxLength:13,inputWidth:95}
					]}
				]}
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk4",position:"absolute",checked:false, list:[
					{type: "select", name: "Field4", inputWidth:150, options:BlockTMP2[2] },
					{type: "newcolumn"},
					{type: "select", name: "Comp4", options: ComparisionItems.slice(8) , inputWidth:60},
					{type: "newcolumn"},				
					{ type: "multiselect", name:"Value40", 
						options:[
							{text: "خرید سرویس", value: "BuyService"},
							{text: "لغو سرویس", value: "CancelService"},
							{text: "دریافت وجه", value: "GetMoney"},
							{text: "برگشت وجه", value: "RefundMoney"},
							{text: "دریافت اعتبار", value: "CreditGet"},
							{text: "دریافت اعتبار آنلاین", value: "OnlineCreditGet"},
							{text: "ارسال اعتبار", value: "CreditSend"},
							{text: "پورسانت پشتیبان", value: "S-Commission"},
							{text: "پورسانت نماینده فروش", value: "R-Commission"},
							{text: "پورسانت شارژر", value: "C-Commission"}
						],inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value41",
						connector: RenderFile+".php?"+un()+"&act=SelectResellersAndOperators",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					}
				]}
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk5",position:"absolute",checked:false, list:[
					{type: "select", name: "Field5", inputWidth:150, options:BlockTMP2[2] },
					{type: "newcolumn"},
					{type: "select", name: "Comp5", options: ComparisionItems.slice(8) , inputWidth:60},
					{type: "newcolumn"},				
					{ type: "multiselect", name:"Value50", 
						options:[
							{text: "سرویس خرید", value: "BuyService"},
							{text: "لغو سرویس", value: "CancelService"},
							{text: "دربافت وجه", value: "GetMoney"},
							{text: "برگشت وجه", value: "RefundMoney"},
							{text: "دریافت اعتبار", value: "CreditGet"},
							{text: "دریافت اعتبار آنلاین", value: "OnlineCreditGet"},
							{text: "ارسال اعتبار", value: "CreditSend"},
							{text: "پورسانت پشتیبان", value: "S-Commission"},
							{text: "پورسانت نماینده فروش", value: "R-Commission"},
							{text: "پورسانت کاربر", value: "C-Commission"}
						],inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value51",
						connector: RenderFile+".php?"+un()+"&act=SelectResellersAndOperators",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					}
				]}
			]}			
		]},
		{type: "fieldset", name:"F2", hidden: true, width:850, label: " دسته بندی براساس ", list:[
			{type: "block", width:800, list:[
				{type: "checkbox",name:"ChkGroupBy0", checked:true, hidden:true},
				{type: "select", name: "GroupBy0", position: "label-left",label:"دسته بندی براساس :",labelAlign:"left",labelWidth:60, inputWidth:140, options:BlockTMP2[3]},
				{type: "newcolumn", offset:32},
				{type: "checkbox",name:"ChkGroupBy1",position:"absolute",checked:false, list:[
					{type: "select", name: "GroupBy1", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:140, options:BlockTMP2[3]},
					{type: "newcolumn", offset:32},
					{type: "checkbox",name:"ChkGroupBy2",position:"absolute",checked:false, list:[
						{type: "select", name: "GroupBy2", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:140, options:BlockTMP2[3]}
					]}
				]}
			]}
		]},		
		{type: "block", width: 850, list:[
				{type: "button", name: "ToggleGroupBy", disabled:true, value: " تنظیم دسته بندی ", width:100},		
				{type: "newcolumn", offset:365},
				{type: "button",name: "Cancel",value: " لغو ",width :80},
				{type: "newcolumn", offset:40},				
				{type: "checkbox", label: "نمایش مجموع ردیف ها", position: "label-right", name: "TotalRow", checked:false},
				{type: "newcolumn", offset:10},				
				{type: "button", name: "GoToReport", disabled:true, value:"در حال بارگذاری("+OptionCount+")",width :80}
		]}
	];
	


	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);

	AddPopupFilter();

	
	DSToolbarAddButton(ToolbarOfGrid,null,"SaveToFile","ذخیره در فایل","SaveToFile",ToolbarOfGrid_OnSaveToFileClick);
	ToolbarOfGrid.setItemToolTip("SaveToFile","CSVذخیره نتایج گزارش در فایل");
	ToolbarOfGrid.disableItem('SaveToFile');
	Popup1.show("Filter");

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);	
	//-*********************************************************************************	
	
//FUNCTIONS------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------MonthDays(FYear,FMonth)
function MonthDays(FYear,FMonth){
	if(FMonth<=6)
		return 31
	else if(FMonth<=11)
		return 30
	else{
		var tmp=(FYear % 33);
		if(tmp==1||tmp==5||tmp==9||tmp==13||tmp==18||tmp==22||tmp==26||tmp==30)
			return 30
		else
			return 29;
	} 
}
	
//-------------------------------------------------------------------AddPopupFilter()
function AddPopupFilter(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Filter","تنظیم فیلتر","tow_Filter");
	ToolbarOfGrid.setItemToolTip("Filter","فیلترها را برای ایجاد گزارش تنظیم کنید");
	Popup1=DSInitialPopup(ToolbarOfGrid,PopupId1,Popup1OnShow);
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onBeforeChange",Form1OnBeforeChange);
	
	ToolbarOfGrid.disableItem('Filter');
	Form1.attachEvent("onOptionsLoaded", function(name){
		OptionCount--;
		if(OptionCount<=0){
		setTimeout(function(){
			Form1.setItemLabel("GoToReport"," برو به گزارش ");
			Form1.enableItem("GoToReport");
			Form1.enableItem("ToggleGroupBy");
			ToolbarOfGrid.enableItem('Filter');
		},500)
		}
		else
			Form1.setItemLabel("GoToReport","در حال بارگذاری("+OptionCount+")");
	});
	
	Form1.setItemValue("FromYear",CurDate[0]);
	Form1.setItemValue("FromMonth",CurDate[1]);
	Form1.setItemValue("FromDay"+MonthDays(CurDate[0],CurDate[1]),CurDate[2]);Form1.showItem("FromDay"+MonthDays(CurDate[0],CurDate[1]));
	Form1.setItemValue("FromHour",0);
	Form1.setItemValue("FromMinute",0);
	Form1.setItemValue("FromSecond",0);
	Form1.setItemValue("ToYear",CurDate[0]);
	Form1.setItemValue("ToMonth",CurDate[1]);
	Form1.setItemValue("ToDay"+MonthDays(CurDate[0],CurDate[1]),CurDate[2]);Form1.showItem("ToDay"+MonthDays(CurDate[0],CurDate[1]));
	Form1.setItemValue("ToHour",CurTime[0]);
	Form1.setItemValue("ToMinute",CurTime[1]);
	Form1.setItemValue("ToSecond",CurTime[2]);
	
	Form1.setItemValue("Field5",1);	
	Form1.hideItem("Value50");
	Form1.showItem("Value51");
	Form1.setItemValue("GroupBy0",0);
	Form1.setItemValue("GroupBy1",1);
	Form1.setItemValue("GroupBy2",2);	
}

//-------------------------------------------------------------------Popup1OnShow()
function Popup1OnShow(){
	if(!ISValidResellerSession()) return;
}

//-------------------------------------------------------------------Form1OnBeforeChange(id, value,Newvalue))
function Form1OnBeforeChange(id,value,Newvalue){
	//alert("Before:\nid:'"+id+"'     value:'"+value+"'      Newvalue:'"+Newvalue+"'");
	//alert(GetRelatedValue(value));
	//alert(GetRelatedValue(Newvaluevalue));
	var tmp1;
	var tmp2;
	var tmp3;
	if(id=='Field4'){
		Form1.hideItem("Value4"+GetRelatedValue(value));		
		Form1.showItem("Value4"+GetRelatedValue(Newvalue));		
	}
	else if(id=='Field5'){
		Form1.hideItem("Value5"+GetRelatedValue(value));		
		Form1.showItem("Value5"+GetRelatedValue(Newvalue));
	}
	else if(id=='FromYear'){
		tmp1=Form1.getItemValue("FromMonth");
		tmp2=MonthDays(value,tmp1);
		tmp3=MonthDays(Newvalue,tmp1);
		Form1.hideItem("FromDay"+tmp2);
		Form1.showItem("FromDay"+tmp3);
		Form1.setItemValue("FromDay"+tmp3,Math.min(Form1.getItemValue("FromDay"+tmp2),tmp3));
	}
	else if(id=='FromMonth'){
		tmp1=Form1.getItemValue("FromYear");
		tmp2=MonthDays(tmp1,value);
		tmp3=MonthDays(tmp1,Newvalue);
		Form1.hideItem("FromDay"+tmp2);
		Form1.showItem("FromDay"+tmp3);
		Form1.setItemValue("FromDay"+tmp3,Math.min(Form1.getItemValue("FromDay"+tmp2),tmp3));
	}
	else if(id=='ToYear'){
		tmp1=Form1.getItemValue("ToMonth");
		tmp2=MonthDays(value,tmp1);
		tmp3=MonthDays(Newvalue,tmp1);
		Form1.hideItem("ToDay"+tmp2);
		Form1.showItem("ToDay"+tmp3);
		Form1.setItemValue("ToDay"+tmp3,Math.min(Form1.getItemValue("ToDay"+tmp2),tmp3));
	}
	else if(id=='ToMonth'){
		tmp1=Form1.getItemValue("ToYear");
		tmp2=MonthDays(tmp1,value);
		tmp3=MonthDays(tmp1,Newvalue);
		Form1.hideItem("ToDay"+tmp2);
		Form1.showItem("ToDay"+tmp3);
		Form1.setItemValue("ToDay"+tmp3,Math.min(Form1.getItemValue("ToDay"+tmp2),tmp3));
	}
	return true;
}

//-------------------------------------------------------------------GetRelatedValue(n)
function GetRelatedValue(n){
	if(n==0)
		return 0
	else 
		return 1;
}

//-------------------------------------------------------------------Form1OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='Cancel') {
		Popup1.hide();
	}
	else if(name=='ToggleGroupBy'){
		if(GroupByCount){
			Form1.hideItem('F2');
			//Form1.enableItem("TotalRow");
		}
		else{
			Form1.showItem('F2');
			//Form1.disableItem("TotalRow");
		}
		GroupByCount=1-GroupByCount;
	}
	else{//GoToReport Clicked
		//if(DSFormValidate(Form1,Form1FieldHelpId))
		//{
			Form1.disableItem("GoToReport");
			Popup1.hide();
			//dhxLayout.progressOn();
								
			MyPostString=CreatePostStr();
			// dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=list&req=GetRecordCount&SortField=&SortOrder="+MyPostString,
			// function(loader){
				// response=loader.xmlDoc.responseText;
				// response=CleanError(response);
				// if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				// else if(response=='0')
					// dhtmlx.alert("Not found any item with selected filter(s)");
				// else{
					if(GroupByCount)
						SetGroupByGridLayout();
					else 
						SetNormalGridLayout();
					InititalGridList();
					MyLoadGridDataFromServer();
					ToolbarOfGrid.enableItem('SaveToFile');
				//}
				
				Form1.enableItem("GoToReport");
				// dhxLayout.progressOff();
			// });
		//}
	}
}

//-------------------------------------------------------------------CreatePostStr()
function CreatePostStr(){
	var Out="";
	var tmp1;
	var tmp2;
	
	if(Form1.getItemValue("Chk0")){
		Out+="&Chk0=1";
		tmp1=Form1.getItemValue("FromYear");
		tmp2=Form1.getItemValue("FromMonth");
		Out+="&Value0="+tmp1+"/"+tmp2+"/"+Form1.getItemValue("FromDay"+MonthDays(tmp1,tmp2))+" "+Form1.getItemValue("FromHour")+":"+Form1.getItemValue("FromMinute")+":"+Form1.getItemValue("FromSecond");
	}
	else
		Out+="&Chk0=0";
	
	if(Form1.getItemValue("Chk1")){
		Out+="&Chk1=1";
		tmp1=Form1.getItemValue("ToYear");
		tmp2=Form1.getItemValue("ToMonth");
		Out+="&Value1="+tmp1+"/"+tmp2+"/"+Form1.getItemValue("ToDay"+MonthDays(tmp1,tmp2))+" "+Form1.getItemValue("ToHour")+":"+Form1.getItemValue("ToMinute")+":"+Form1.getItemValue("ToSecond");
	}
	else
		Out+="&Chk1=0";

	
	if(Form1.getItemValue("Chk20")){
		Out+="&Chk20=1";
		Out+="&Field20="+Form1.getItemValue("Field20");
		Out+="&Comp20="+Form1.getItemValue("Comp20");
		Out+="&Value20="+Form1.getItemValue("Value20");		
		if(Form1.getItemValue("Chk21")){
			Out+="&Chk21=1";
			Out+="&Opt2="+Form1.getItemValue("Opt2");
			Out+="&Field21="+Form1.getItemValue("Field21");
			Out+="&Comp21="+Form1.getItemValue("Comp21");
			Out+="&Value21="+Form1.getItemValue("Value21");		
		}
		else
			Out+="&Chk21=0";			
	}
	else
		Out+="&Chk20=0";
	if(Form1.getItemValue("Chk30")){
		Out+="&Chk30=1";
		Out+="&Field30="+Form1.getItemValue("Field30");
		Out+="&Comp30="+Form1.getItemValue("Comp30");
		Out+="&Value30="+Form1.getItemValue("Value30");		
		if(Form1.getItemValue("Chk31")){
			Out+="&Chk31=1";
			Out+="&Opt3="+Form1.getItemValue("Opt3");
			Out+="&Field31="+Form1.getItemValue("Field31");
			Out+="&Comp31="+Form1.getItemValue("Comp31");
			Out+="&Value31="+Form1.getItemValue("Value31");		
		}
		else
			Out+="&Chk31=0";			
	}
	else
		Out+="&Chk30=0";
	
	if(Form1.getItemValue("Chk4")){
		Out+="&Chk4=1";
		tmp1=Form1.getItemValue("Field4");
		Out+="&Field4="+tmp1;
		Out+="&Comp4="+Form1.getItemValue("Comp4");
		Out+="&Value4="+Form1.getItemValue("Value4"+GetRelatedValue(tmp1));			
	}
	else
		Out+="&Chk4=0";
	if(Form1.getItemValue("Chk5")){
		Out+="&Chk5=1";
		tmp1=Form1.getItemValue("Field5");
		Out+="&Field5="+tmp1;
		Out+="&Comp5="+Form1.getItemValue("Comp5");
		Out+="&Value5="+Form1.getItemValue("Value5"+GetRelatedValue(tmp1));			
	}
	else
		Out+="&Chk5=0";

	if(GroupByCount)
		for(i=0;i<3;++i){
			if(Form1.getItemValue("ChkGroupBy"+i))
				Out+="&ChkGroupBy"+i+"=1&GroupBy"+i+"="+Form1.getItemValue("GroupBy"+i);
			else{
				Out+="&ChkGroupBy"+i+"=0";
				break;
			}
		}
	else 
		Out+="&ChkGroupBy0=0";
	return Out;
	
}

//-------------------------------------------------------------------SetNormalGridLayout()
function SetNormalGridLayout(){
	ISSort=true;
	ColSortIndex=0;
	SortDirection='desc';
	var HaveFooter=Form1.getItemValue("TotalRow");
	if(HaveFooter){	
		GColFooter=
		"<div id='FooterField_0' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>ثبت کننده</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ ثبت</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نوع</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نماینده فروش</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نماینده فروش مرتبط</div>"+
		",<div id='FooterField_1' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام کاربری</div>";

		GColHeaders="شناسه تراکنش";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GColFooter="";
	}
	
	GColHeaders+=",ثبت کننده,تاریخ ثبت,نوع,نماینده فروش,نماینده فروش مرتبط,اعتبار,نام کاربری";
	
	GColIds="Reseller_Transaction_Id,Creator,TransactionCDT,TransactionType,Reseller,Relate_Reseller,Credit,UserName";
		
	GColInitWidths="100,140,140,140,140,140,120,120";
	
	var FieldCount=8;
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
function SetGroupByGridLayout(){
	ISSort=false;
	var tmp;
	
	var HaveFooter=Form1.getItemValue("TotalRow");
	if(HaveFooter){	
		GColFooter="<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تعداد ردیف</div>";
		GColHeaders="سطر";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GColFooter="";
	}	
	
	
	GColIds="Row_Number";
	GColInitWidths="100";
	ColSortIndex=0;
	GColAligns="center";
	GColTypes="ra";
	GColVisibilitys = [1];
	HeaderAlignment = ["text-align:center"];
	
	for(i=0;i<3;++i)
		if(Form1.getItemValue("ChkGroupBy"+i)){
			tmp=Form1.getItemValue("GroupBy"+i);			
			if(HaveFooter)
				GColFooter+=",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>"+TheFieldsItems[3][tmp]+"</div>"
			GColIds+=","+TheFieldsItems[3][tmp];
			GColHeaders+=","+TheFieldsItems[3][tmp];
			GColInitWidths+=",200";
			GColAligns+=",center";
			GColTypes+=",ro";
			GColVisibilitys.push(1);
			HeaderAlignment.push("text-align:center");
		}
		else
			break;
	

	
	
	if(HaveFooter){	
		GColFooter+=
		",<div id='FooterField_0' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_1' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>";
	}
	
	GColIds+=",CountTransaction,SumCredit";
	GColHeaders+=",تعداد تراکنش,مجموع اعتبار";
	GColInitWidths+=",200,200";
	GColAligns+=",center,center";
	GColTypes+=",ro,ro";
	GColVisibilitys.push(1);
	GColVisibilitys.push(1);
	HeaderAlignment.push("text-align:center");
	HeaderAlignment.push("text-align:center");
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
			MyLoadGridDataFromServer();
		});
	}
	
	if(!GroupByCount){
		mygrid.enableSmartRendering(true,100);
	}

}

//-------------------------------------------------------------------MyLoadGridDataFromServer()
function MyLoadGridDataFromServer(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+MyPostString,DoAfterRefresh);
	if(GColFooter!=''){
		dhtmlxAjax.get(RenderFile+".php?act=list&req=GetFooterInfo&SortField=&SortOrder="+MyPostString,
			function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				else{
					ResponseList=response.split("`");
					document.getElementById("FooterField_0").innerHTML = ResponseList[0]+" ردیف";
					document.getElementById("FooterField_1").innerHTML = ResponseList[1]+" ریال";				
				}
			}
		);
	}
}

	
//-------------------------------------------------------------------ToolbarOfGrid_OnSaveToFileClick()
function ToolbarOfGrid_OnSaveToFileClick(){
	if(mygrid.getRowsNum()<=0){
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "داده ای برای ذخیره موجود نیست"});
		return
	}	
	if(!ISValidResellerSession()) return;
	ToolbarOfGrid.disableItem('SaveToFile');
	SortField=mygrid.getColumnId(ColSortIndex);
	
	window.location=RenderFile+".php?act=list&req=SaveToFile&SortField="+SortField+"&SortOrder="+SortDirection+MyPostString;
	setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile')},2000);
}
	
//-------------------------------------------------------------------PopupWindow(SelectedRowId)
function PopupWindow(SelectedRowId){
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