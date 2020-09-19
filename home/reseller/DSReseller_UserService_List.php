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
	

	var DataTitle="Report Service Added";
	var DataName="DSReseller_UserService_";
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
	var FilterRowNumber=0;	
	var OptionCount=4;
	var GroupByCount=0;
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATESTR(NOW())');?>";

	var HeaderAlignment = [];
	

	var TheFieldsItems=[
			["نام کاربری","شناسه کاربر"],
			["تاریخ ثبت","تاریخ لغو","تاریخ شروع","تاریخ پایان","تاریخ اعمال"],
			["هزینه کسر شده","قیمت سرویس","بازگشت وجه","مالیات","تخفیف","تعداد اقساط","دوره اقساط"],
            ["روش پرداخت","وضعیت سرویس","نام سرویس"],
			["سال ایجاد","ماه ایجاد","روز ایجاد","روش پرداخت","نام سرویس","ارائه دهنده مجازی اینترنت","نماینده فروش","مرکز","پشتیبان"]
		];

	var Row34Map=[
		[0,1,0,0],
		[0,1,0,2],
		[0,1,2,3]
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
	
	var BlockTMP = [];
	
	BlockTMP = [];
	for(i=0;i<TheFieldsItems.length;++i){
		BlockTMP[i] = [];
		for(j=0;j<TheFieldsItems[i].length;j++)
			BlockTMP[i].push({text: TheFieldsItems[i][j], value: j});
	}
	Form1Str=[
		{type: "fieldset", name:"F1", width:850, label: " فیلترها ", list:[
			{type: "block", width:800,style:"border-bottom:1px solid", list:[
				{type: "select", label:"لطفا نوع سرویس را انتخاب کنید: ", name: "ServiceType", required:true, inputWidth:150, options:[
					{text: 'سرویس پایه', value: 0},
					{text: 'سرویس اعتبار اضافی', value: 1},
					{text: 'سرویس آی پی', value: 2},				
					{text: 'سرویس سایر', value: 3}				
				]},
				{type: "label", label:""}
			]},
			{type: "label", label:""},
			{type: "block", list:[
				{type: "checkbox",name:"Chk00",position:"absolute",checked:false, list:[
					{type: "select", name: "Field00", inputWidth:150, options:BlockTMP[0] },
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
						{type: "select", name: "Field01", inputWidth:150, options:BlockTMP[0]},
						{type: "newcolumn"},
						{type: "select", name: "Comp01", options: ComparisionItems.slice(0,8) , inputWidth:60, required:true},
						{type: "newcolumn"},
						{type: "input" , name: "Value01", maxLength:32,inputWidth:95}
					]}
				]}
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk10",position:"absolute",checked:false, list:[
					{type: "select", name: "Field10_0", inputWidth:150, options:BlockTMP[1].slice(0,4)},
					{type: "select", name: "Field10_1", hidden: true, inputWidth:150, options:BlockTMP[1].slice(0,2).concat(BlockTMP[1].slice(4))},
					{type: "select", name: "Field10_2", hidden: true, inputWidth:150, options:BlockTMP[1].slice(0,4)},
					{type: "select", name: "Field10_3", hidden: true, inputWidth:150, options:BlockTMP[1].slice(0,2)},
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
						{type: "select", name: "Field11_0", inputWidth:150, options:BlockTMP[1].slice(0,4)},
						{type: "select", name: "Field11_1", hidden: true, inputWidth:150, options:BlockTMP[1].slice(0,2).concat(BlockTMP[1].slice(4))},
						{type: "select", name: "Field11_2", hidden: true, inputWidth:150, options:BlockTMP[1].slice(0,4)},
						{type: "select", name: "Field11_3", hidden: true, inputWidth:150, options:BlockTMP[1].slice(0,2)},
						{type: "newcolumn"},
						{type: "select", name: "Comp11", options: ComparisionItems.slice(0,6) , inputWidth:60},			
						{type: "newcolumn"},
						{type: "input" , name: "Value11", value: TNow, validate:"IsValidDate", maxLength:10,inputWidth:95}
					]}
				]}
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk20",position:"absolute",checked:false, list:[
					{type: "select", name: "Field20", inputWidth:150, options:BlockTMP[2] },
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
						{type: "select", name: "Field21", inputWidth:150, options:BlockTMP[2]},
						{type: "newcolumn"},
						{type: "select", name: "Comp21", options: ComparisionItems.slice(0,6) , inputWidth:60},
						{type: "newcolumn"},
						{type: "input" , name: "Value21", value: 0, validate:"IsValidPrice", maxLength:13,inputWidth:95}
					]}
				]}			
			]},
			{type: "block", list:[
				{type: "checkbox",name:"Chk3",position:"absolute",checked:false, list:[
					{type: "select", name: "Field3", inputWidth:150, options:BlockTMP[3] },
					{type: "newcolumn"},
					{type: "select", name: "Comp3", options: ComparisionItems.slice(8) , inputWidth:60},
					{type: "newcolumn"},
					
					
					{type: "multiselect", name:"Value300",
						options:[
							{text: "پیش پرداخت", value: "PrePaid"},
							{text: "پس پرداخت", value: "PostPaid"}
						],inputWidth:516,
						note: {text: "<span style='direction:rtl;float:right;'>با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید</span>"}
					},				
					{type: "multiselect", hidden: true, name:"Value301",
						options:[
							{text: "پیش پرداخت", value: "PrePaid"},
							{text: "پس پرداخت", value: "PostPaid"},
							{text: "ارسال به", value: "SendTo"},
							{text: "دریافت از", value: "GetFrom"},
							{text: "تنظیم مجدد", value: "Reset"}
						],inputWidth:516,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},				
					{type: "multiselect", hidden: true, name:"Value310",
						options:[
							{text: "فعال", value: "Active"},
							{text: "استفاده شده", value: "Used"},
							{text: "انتظار", value: "Pending"},
							{text: "لغو", value: "Cancel"}
						],inputWidth:516,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value311",
						options:[
							{text: "انتظار", value: "Pending"},
							{text: "اعمال شده", value: "Applied"},
							{text: "لغو", value: "Cancel"}
						],inputWidth:516,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},					
					{type: "multiselect", hidden: true, name:"Value312",
						options:[
							{text: "استفاده شده", value: "Used"},
							{text: "لغو", value: "Cancel"}
						],inputWidth:516,
						note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},	

					{type: "multiselect", hidden: true, name:"Value320",
							connector: RenderFile+".php?"+un()+"&act=SelectServiceNameBase",inputWidth:516,
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value321",
							connector: RenderFile+".php?"+un()+"&act=SelectServiceNameExtraCredit",inputWidth:516,
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value322",
							connector: RenderFile+".php?"+un()+"&act=SelectServiceNameIP",inputWidth:516,
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					},
					{type: "multiselect", hidden: true, name:"Value323",
							connector: RenderFile+".php?"+un()+"&act=SelectServiceNameOther",inputWidth:516,
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
					}
				]}
			]}			
		]},
		{type: "fieldset", name:"F2", hidden: true, width:850, label: " دسته بندی براساس ", list:[
			{type: "block", width:800, list:[
				{type: "checkbox",name:"ChkGroupBy0", checked:true, hidden:true},
				{type: "select", name: "GroupBy0", position: "label-left",label:"دسته بندی براساس :",labelAlign:"left",labelWidth:60, inputWidth:140, options:BlockTMP[4]},
				{type: "newcolumn", offset:32},
				{type: "checkbox",name:"ChkGroupBy1",position:"absolute",checked:false, list:[
					{type: "select", name: "GroupBy1", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:140, options:BlockTMP[4]},
					{type: "newcolumn", offset:32},
					{type: "checkbox",name:"ChkGroupBy2",position:"absolute",checked:false, list:[
						{type: "select", name: "GroupBy2", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:140, options:BlockTMP[4]}
					]}
				]}
			]}
		]},
		{type: "block", width: 850, list:[
				{type: "button", name: "ToggleGroupBy", disabled:true, value: " تنظیم دسته بندی ", width:100},		
				{type: "newcolumn", offset:230},
				{type: "button",name: "Cancel",value: " لغو ",width :80},
				{type: "newcolumn", offset:30},
				{type: "checkbox", label: "نمایش مجموع ردیف ها", position: "label-right", name: "TotalRow", checked:false},
				{type: "newcolumn", offset:10},
				{type: "select", label:"ستون ثابت:", name: "SplitColumnsCount",labelAlign:"right",labelWidth:100, inputWidth:35, options:[
					{text: '0', value: 0,selected:true},
					{text: '1', value: 1},
					{text: '2', value: 2},				
					{text: '3', value: 3},
					{text: '4', value: 4}
				]},
				{type: "newcolumn", offset:10},				
				{type: "button", name: "GoToReport", disabled:true, value: "Loading("+OptionCount+")",width :80}
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
			Form1.setItemLabel("GoToReport","Loading("+OptionCount+")");
	});
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
	//alert("Before:\nid:'"+id+"'     value:'"+value+"'");
	var tmp;
	if(id=='ServiceType'){
		Form1.hideItem("Field10_"+value);
		Form1.showItem("Field10_"+New_value);
		Form1.setItemValue("Field10_"+New_value,Form1.getItemValue("Field10_"+value));
		Form1.setItemValue("Field11_"+New_value,Form1.getItemValue("Field11_"+value));
		Form1.hideItem("Field11_"+value);
		Form1.showItem("Field11_"+New_value);	
		tmp=Form1.getItemValue("Field3");
		Form1.hideItem("Value3"+tmp+Row34Map[tmp][value]);
		Form1.showItem("Value3"+tmp+Row34Map[tmp][New_value]);	
	}
	else if(id=='Field3'){
		tmp=Form1.getItemValue("ServiceType");
		Form1.hideItem("Value3"+value+Row34Map[value][tmp]);		
		Form1.showItem("Value3"+New_value+Row34Map[New_value][tmp]);		
	}

	return true;
}

//-------------------------------------------------------------------Form1OnChange(id, value)
function Form1OnChange(id,value,state){
}

//-------------------------------------------------------------------Form1OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='Cancel') {
		Popup1.hide();
	}
	else if(name=='ToggleGroupBy'){
		if(GroupByCount){
			Form1.hideItem('F2');
			Form1.enableItem("SplitColumnsCount");			
			Form1.enableItem("TotalRow");			
		}
		else{
			Form1.showItem('F2');
			Form1.disableItem("SplitColumnsCount");
			Form1.disableItem("TotalRow");
		}
		GroupByCount=1-GroupByCount;
	}
	else{//GoToReport Clicked
		//if(DSFormValidate(Form1,Form1FieldHelpId))
		// {
			Form1.disableItem("GoToReport");
			Popup1.hide();
			// dhxLayout.progressOn();
			
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
						SetGridLayout();
					
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
	var ServiceType=Form1.getItemValue("ServiceType");
	var Out='&ServiceType='+ServiceType;	
	var FieldValue;
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
		Out+="&Field10="+Form1.getItemValue("Field10_"+ServiceType);
		Out+="&Comp10="+Form1.getItemValue("Comp10");
		Out+="&Value10="+Form1.getItemValue("Value10");		
		if(Form1.getItemValue("Chk11")){
			Out+="&Chk11=1";
			Out+="&Opt1="+Form1.getItemValue("Opt1");
			Out+="&Field11="+Form1.getItemValue("Field11_"+ServiceType);
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
		if(Form1.getItemValue("Chk01")){
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

	if(Form1.getItemValue("Chk3")){
		Out+="&Chk3=1";
		FieldValue=Form1.getItemValue("Field3");
		Out+="&Field3="+FieldValue;
		Out+="&Comp3="+Form1.getItemValue("Comp3");
		Out+="&Value3="+Form1.getItemValue("Value3"+FieldValue+Row34Map[FieldValue][ServiceType]);		
	}
	else
		Out+="&Chk3=0";
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

//-------------------------------------------------------------------SetGridLayout()
function SetGridLayout(){
	var ServiceType=Form1.getItemValue("ServiceType");
	ISSort=true;
	ColSortIndex=0;
	SortDirection='desc';
	
	var HaveFooter=Form1.getItemValue("TotalRow");
	if(HaveFooter){
	
		GColFooter="<div id='FooterField_0' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>ایجاد کننده</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام سرویس</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام کاربری</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>زمان ثبت</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>وضعیت سرویس</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نوع پرداخت</div>"+
		",<div id='FooterField_1' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_2' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_3' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div id='FooterField_4' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ لغو</div>"+
		",<div id='FooterField_5' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تعداد اقساط</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>مدت اقساط</div>"+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>قسط اول زمان ثبت</div>"+
		((ServiceType==0||ServiceType==2)?
			(
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ شروع</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ پایان</div>"
			)
		:"")+
		((ServiceType==1)?
			(
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>تاریخ اعمال</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>نام کاربری انتقال دهنده</div>"+
			",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>ترافیک انتقال</div>"
			)
		:"")+
		",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>بیشتر</div>";

		GColHeaders="شناسه سرویس";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GColFooter="";
	}	
	
	GColHeaders+=",ایجاد کننده,نام سرویس,نام کاربری,زمان ثبت,وضعیت سرویس"+
		",نوع پرداخت,قیمت سرویس,هزینه کسر شده,مالیات,تخفیف,زمان لغو,بازگشت وجه,تعداد اقساط,دوره اقساط,قسط اول زمان ثبت"+
		((ServiceType==0||ServiceType==2)?",تاریخ شروع,تاریخ پایان":"")+
		((ServiceType==1)?",تاریخ اعمال,نام کاربری انتقال داده شده,ترافیک انتقال داده شده":"")+
		",بیشتر";
		
		
	GColIds="UserServiceId,Creator,ServiceName,Username,CDT,ServiceStatus"+
		",PayPlan,ServicePrice,PayPrice,VAT,Off,CancelDT,ReturnPrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash"+
		((ServiceType==0||ServiceType==2)?",StartDate,EndDate":"")+
		((ServiceType==1)?",ApplyDT,TransferUsername,TransferTraffic":"")+
		",ServiceIsDel";

		
	GColInitWidths="100,120,250,100,140,100"+
		",90,130,130,80,80,140,100,110,130,150"+
		((ServiceType==0||ServiceType==2)?",110,110":"")+
		((ServiceType==1)?",130,140,140":"")+
		",120";
	
	var FieldCount=17+((ServiceType==0||ServiceType==2)?2:0)+((ServiceType==1)?3:0);
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
	GColInitWidths="80";
	ColSortIndex=0;
	GColAligns="center";
	GColTypes="ro";
	GColVisibilitys = [1];
	HeaderAlignment = ["text-align:center"];
	
	for(i=0;i<3;++i)
		if(Form1.getItemValue("ChkGroupBy"+i)){
			tmp=Form1.getItemValue("GroupBy"+i);
			GColIds+=","+TheFieldsItems[4][tmp];
			GColHeaders+=","+TheFieldsItems[4][tmp];
			GColInitWidths+=",200";
			GColAligns+=",center";
			GColTypes+=",ro";
			GColVisibilitys.push(1);
			HeaderAlignment.push("text-align:center");
		}
		else
			break;
	

	GColIds+=",CountService,SumServicePrice,SumPayPrice,SumReturnPrice,AvgVAT,AvgOff";
	GColHeaders+=",تعداد سرویس,مجموع مبلغ سرویس,مجموع مبلغ پرداختی,مجموع مبلغ برگشتی,میانگین مالیات,میانگین تخفیف";
	for(i=1;i<=6;++i){
		GColInitWidths+=",155";
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
					document.getElementById("FooterField_2").innerHTML = ResponseList[2]+" ریال";
					document.getElementById("FooterField_3").innerHTML = ResponseList[3]+" %";
					document.getElementById("FooterField_4").innerHTML = ResponseList[4]+" %";
					document.getElementById("FooterField_5").innerHTML = ResponseList[5]+" ریال";
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
	//dhxLayout.cells("a").attachURL(RenderFile+".php?act=list&req=SaveToFile&SortField="+SortField+"&SortOrder="+SortDirection+MyPostString);
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