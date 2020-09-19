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
	

	var DataTitle="Report Payment Added";
	var DataName="DSRep_User_Payment_";
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
	var OptionCount=12;
	var GroupByCount=0;
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATESTR(NOW())');?>";

	var HeaderAlignment = [];
	

	var TheFieldsItems=[
			["نام کاربری","شناسه کاربر"],
			["تاریخ ثبت","تاریخ پرداخت"],
			["مبلغ پرداختی","تراز مالی","پورسانت شارژر","پورسانت پشتیبان","پورسانت نماینده"],
            ["سریال/پیگیری","نام بانک","کد شعبه","توضیح"],
			["روش پرداخت","ارائه دهنده مجازی اینترنت","مرکز","ثبت کننده","نماینده فروش","پشتیبان","شارژر ثبت کننده","نماینده ثبت کننده","پشتیبان ثبت کننده"],
			["سال ایجاد","ماه ایجاد","روز ایجاد","ارائه دهنده مجازی اینترنت","مرکز","ثبت کننده پرداختی","نماینده فروش","پشتیبان","شارژر ثبت کننده","نماینده ثبت کننده","پشتیبان ثبت کننده"]
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
	var Form1FieldHelp  = {	UserName:'UserName'};
	var Form1FieldHelpId=['UserName'];
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
		{type: "block", list:[
			{type: "checkbox",name:"Chk00",position:"absolute",checked:false, list:[
				{type: "select", name: "Field00", inputWidth:150, options:BlockTMP1[0] },
				{type: "newcolumn"},
				{type: "select", name: "Comp00", options: ComparisionItems.slice(0,8) , inputWidth:60},
				{type: "newcolumn"},
				{type: "input" , name: "Value00", maxLength:32,inputWidth:95} ,
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
				{type: "input" , name: "Value10", value: TNow, validate:"IsValidDate", maxLength:10,inputWidth:95},			
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
		{type: "block", list:[
			{type: "checkbox",name:"Chk20",position:"absolute",checked:false, list:[
				{type: "select", name: "Field20", inputWidth:150, options:BlockTMP1[2] },
				{type: "newcolumn"},
				{type: "select", name: "Comp20", options: ComparisionItems.slice(0,6) , inputWidth:60},
				{type: "newcolumn"},
				{type: "input" , name: "Value20", value: 0, validate:"IsValidPrice", maxLength:13,inputWidth:95} ,
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk21",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt2", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt2", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field21", inputWidth:150, options:BlockTMP1[2]},
					{type: "newcolumn"},
					{type: "select", name: "Comp21", options: ComparisionItems.slice(0,6) , inputWidth:60},
					{type: "newcolumn"},
					{type: "input" , name: "Value21", value: 0, validate:"IsValidPrice", maxLength:13,inputWidth:95}
				]}
			]}			
		]},
		{type: "block", list:[
			{type: "checkbox",name:"Chk30",position:"absolute",checked:false, list:[
				{type: "select", name: "Field30", inputWidth:150, options:BlockTMP1[3] },
				{type: "newcolumn"},
				{type: "select", name: "Comp30", options: ComparisionItems.slice(0,8) , inputWidth:60},
				{type: "newcolumn"},
				{type: "input" , name: "Value30", maxLength:64,inputWidth:95} ,
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk31",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt3", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt3", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field31", inputWidth:150, options:BlockTMP1[3]},
					{type: "newcolumn"},
					{type: "select", name: "Comp31", options: ComparisionItems.slice(0,8) , inputWidth:60},
					{type: "newcolumn"},
					{type: "input" , name: "Value31", maxLength:64,inputWidth:95}
				]}
			]}			
		]}
	];
	for(i=4;i<=5;++i)
		BlockTMP2.push(
			{type: "block", list:[
				{type: "checkbox",name:"Chk"+i,position:"absolute",checked:false, list:[
					{type: "select", name: "Field"+i, inputWidth:150, options:BlockTMP1[4] },
					{type: "newcolumn"},
					{type: "select", name: "Comp"+i, options: ComparisionItems.slice(8) , inputWidth:60},
					{type: "newcolumn"},
					{type: "multiselect", name:"Value"+i+"0",
						options:[
							{text: "خرید سرویس", value: "BuyService"},
							{text: "وجه نقد", value: "Cash"},
							{text: "آنلاین", value: "Online"},
							{text: "چک", value: "Cheque"},
							{text: "کارت خوان", value: "Pos"},
							{text: "بیعانه", value: "Deposit"},
							{text: "تخفیف", value: "Off"},
							{text: "مالیات", value: "TAX"},
							{text: "سایر", value: "Other"},
							{text: "اعمال قسط", value: "ApplyInstallment"},
							{text: "لغو سرویس", value: "CancelService"},
							{text: "بازگشت وجه", value: "ReturnPrice"},
							{text: "انتقال اعتبار", value: "TransferCredit"},
							{text: "اعتبار اولیه", value: "Initial"}
						],inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value"+i+"1",
						connector: RenderFile+".php?"+un()+"&act=SelectVisp",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},					
					{type: "multiselect", hidden: true, name:"Value"+i+"2",
						connector: RenderFile+".php?"+un()+"&act=SelectCenter",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},				
					{type: "multiselect", hidden: true, name:"Value"+i+"3",
						connector: RenderFile+".php?"+un()+"&act=SelectCreator",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},				
					{type: "multiselect", hidden: true, name:"Value"+i+"4",
						connector: RenderFile+".php?"+un()+"&act=SelectReseller",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value"+i+"5",
						connector: RenderFile+".php?"+un()+"&act=SelectSupporter",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value"+i+"6",
						connector: RenderFile+".php?"+un()+"&act=SelectPaymentReseller",inputWidth:516,inputHeight: 70,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					}					
				]}
			]}
		);

	Form1Str=[
		{type: "fieldset", name:"F1", width:850, label: " فیلترها", list:BlockTMP2},
		{type: "fieldset", name:"F2", hidden: true, width:850, label: " دسته بندی براساس ", list:[
			{type: "block", width:800, list:[
				{type: "checkbox",name:"ChkGroupBy0", checked:true, hidden:true},
				{type: "select", name: "GroupBy0", position: "label-left",label:"دسته بندی براساس :",labelAlign:"left",labelWidth:60, inputWidth:140, options:BlockTMP1[5]},
				{type: "newcolumn", offset:32},
				{type: "checkbox",name:"ChkGroupBy1",position:"absolute",checked:false, list:[
					{type: "select", name: "GroupBy1", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:140, options:BlockTMP1[5]},
					{type: "newcolumn", offset:32},
					{type: "checkbox",name:"ChkGroupBy2",position:"absolute",checked:false, list:[
						{type: "select", name: "GroupBy2", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:140, options:BlockTMP1[5]}
					]}
				]}
			]}
		]},
		{type: "block", width: 850, list:[
				{type: "button", name: "ToggleGroupBy", disabled:true, value: " تنظیم دسته بندی ", width:100},		
				{type: "newcolumn", offset:40},
				{type: "button",name: "Cancel",value: " لغو ",width :80},
				{type: "newcolumn", offset:40},				
				{type: "checkbox",name:"ExtraUsersInfo",label:"نمایش اطلاعات بیشتر کاربران", position: "label-right",labelWidth:150},
				{type: "newcolumn", offset:10},
				{type: "checkbox", label: "نمایش مجموع ردیف ها", position: "label-right", name: "TotalRow", checked:false},
				{type: "newcolumn", offset:10},
				{type: "select", label:"ستون ثابت:", name: "SplitColumnsCount",labelAlign:"right",labelWidth:100, inputWidth:35, options:[
					{text: '0', value: 0,selected:true},
					{text: '1', value: 1},
					{text: '2', value: 2},				
					{text: '3', value: 3},
					{text: '4', value: 4},
					{text: '5', value: 5},
					{text: '6', value: 6}
				]},
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
	Form1.setItemValue("Field5",1);
	Form1.hideItem("Value50");
	Form1.showItem("Value51");
	Form1.setItemValue("GroupBy0",0);
	Form1.setItemValue("GroupBy1",1);
	Form1.setItemValue("GroupBy2",2);
	GroupByCount=0;
}

//-------------------------------------------------------------------Popup1OnShow()
function Popup1OnShow(){
	if(!ISValidResellerSession()) return;
}

//-------------------------------------------------------------------Form1OnBeforeChange(id, value,New_value)
function Form1OnBeforeChange(id,value,New_value){
	//alert("Before:\nid:'"+id+"'\nvalue:'"+value+"'\nNew_value:'"+New_value+"'");
	//alert(GetRelatedValue(value));
	//alert(GetRelatedValue(New_value));
	if(id=='Field4'){
		Form1.hideItem("Value4"+GetRelatedValue(value));		
		Form1.showItem("Value4"+GetRelatedValue(New_value));		
	}
	else if(id=='Field5'){
		Form1.hideItem("Value5"+GetRelatedValue(value));		
		Form1.showItem("Value5"+GetRelatedValue(New_value));
	}
	return true;
}

//-------------------------------------------------------------------GetRelatedValue(n)
function GetRelatedValue(n){
	if(n<6)
		return n
	else if(n==6)
		return 3;
	else if(n==7)
		return 6;
	else if(n==8)
		return 3;
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
			Popup1.hide();
			//dhxLayout.progressOn();
			
			MyPostString=CreatePostStr();			
			ExtraUserInfoPostStr="&ExtraUsersInfo="+(Form1.getItemValue("ExtraUsersInfo")?"1":"0");
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
	var FieldValue;
	var Out="";
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
		FieldValue=Form1.getItemValue("Field4");
		Out+="&Field4="+FieldValue;
		Out+="&Comp4="+Form1.getItemValue("Comp4");
		Out+="&Value4="+Form1.getItemValue("Value4"+GetRelatedValue(FieldValue));		
	}
	else
		Out+="&Chk4=0";
	
	if(Form1.getItemValue("Chk5")){
		Out+="&Chk5=1";
		FieldValue=Form1.getItemValue("Field5");
		Out+="&Field5="+FieldValue;
		Out+="&Comp5="+Form1.getItemValue("Comp5");
		Out+="&Value5="+Form1.getItemValue("Value5"+GetRelatedValue(FieldValue));		
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
	var ExtraUsersField=Form1.getItemValue("ExtraUsersInfo");
	ISSort=true;
	ColSortIndex=0;
	SortDirection='desc';
	
	var HaveFooter=Form1.getItemValue("TotalRow");
	if(HaveFooter){
	
		GColFooter="<div id='FooterField_0' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>ثبت کننده پرداخت</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام کاربری</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>زمان ثبت</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>روش پرداخت</div>"+
		",<div id='FooterField_1' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		(ExtraUsersField?
			(
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>ارائه دهنده مجازی اینترنت</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نماینده فروش</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>مرکز</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>پشتیبان</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام خانوادگی</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>سازمان</div>"+
			",<div id='FooterField_5' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"
			)
		:"")+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>سریال/پیگیری</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ رسید</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام بانک</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>کد شعبه</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>شارژر ثبت کننده</div>"+
		",<div id='FooterField_2' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>پشتیبان ثبت کننده</div>"+
		",<div id='FooterField_3' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نماینده ثبت کننده</div>"+
		",<div id='FooterField_4' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>توضیح</div>";

		GColHeaders="شناسه";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GColFooter="";
	}
	
	GColHeaders+=",ثبت کننده پرداخت,نام کاربری,زمان ثبت,روش پرداخت,قیمت با مالیات"+
		(ExtraUsersField?",ارائه دهنده مجازی اینترنت,نماینده فروش,مرکز,پشتیبان,نام,نام خانوادگی,سازمان,تراز مالی":"")+	",سریال/پیگیری,تاریخ رسید,نام بانک,کد شعبه,شارژر ثبت کننده,پورسانت شارژر,پشتیبان ثبت کننده"+
		",پورسانت پشتیبان,نماینده ثبت کننده,پورسانت نماینده,توضیح";	
	
		GColIds="UserPaymentId,PaymentCreator,Username,User_PaymentCDT,PaymentType,Price"+
		(ExtraUsersField?",UserVisp,UserReseller,UserCenter,UserSupporter,Name,Family,Organization,PayBalance":"")+",VoucherNo,VoucherDate,BankBranchName,BankBranchNo,PaymentCharger,ChargerCommission,PaymentSupporter"+
		",SupporterCommission,PaymentReseller,ResellerCommission,Comment";
		
	GColInitWidths="100,120,100,140,100,140"+
		(ExtraUsersField?",135,120,120,120,100,100,160,140":"")+
		",90,110,130,100,140,140,140,140,140,140,240";
	
	var FieldCount=17+(ExtraUsersField?8:0);
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
	GColIds="Row_Number";
	GColHeaders="{#stat_count} ردیف";
	GColFooter="";
	GColInitWidths="90";
	ColSortIndex=0;
	GColAligns="center";
	GColTypes="ra";
	GColVisibilitys = [1];
	HeaderAlignment = ["text-align:center"];
	
	for(i=0;i<3;++i)
		if(Form1.getItemValue("ChkGroupBy"+i)){
			tmp=Form1.getItemValue("GroupBy"+i);
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
	

	GColIds+=",CountPayment,SumPrice";
	GColHeaders+=",تعداد پرداختی,مجموع مبلغ";

	GColInitWidths+=",125,125";
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
	//mygrid.entBox.onselectstart = function(){ return true; };
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
		var FreezeCount=Form1.getItemValue("SplitColumnsCount");
		if(FreezeCount>0) mygrid.splitAt(FreezeCount);
		mygrid.enableSmartRendering(true,100);
		mygrid.attachEvent("onRowDblClicked",function(id){PopupWindow(id)});
	}

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
					document.getElementById("FooterField_0").innerHTML = ResponseList[0]+" ردیف";
					document.getElementById("FooterField_1").innerHTML = ResponseList[1]+" ریال";
					document.getElementById("FooterField_2").innerHTML = ResponseList[2]+" ریال";
					document.getElementById("FooterField_3").innerHTML = ResponseList[3]+" ریال";
					document.getElementById("FooterField_4").innerHTML = ResponseList[4]+" ریال";
					if(Form1.getItemValue("ExtraUsersInfo"))
						document.getElementById("FooterField_5").innerHTML = ResponseList[6]+" ریال";					
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
	
	window.location=RenderFile+".php?act=list&req=SaveToFile&SortField="+SortField+"&SortOrder="+SortDirection+MyPostString+ExtraUserInfoPostStr;
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
