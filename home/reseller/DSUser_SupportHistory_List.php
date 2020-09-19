<?php
require_once("../../lib/DSInitialReseller.php");
?>
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
	DataTitle="User_SupportHistory";
	DataName="DSUser_SupportHistory_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	
	GColIds="User_SupportHistory_Id,ResellerName,CDT,SupportItemTitle,Comment";//,CustomerSatisfactionLevel";
	GColHeaders="{#stat_count} ردیف,ثبت کننده,زمان ثبت,مورد,توضیح";//,C.S.Level";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1/* ,1 */];
	
	GFooter="";
	GColInitWidths="75,120,120,200,400";//,90";
	GColAligns="center,center,center,center,left";//,center";
	GColTypes="ro,ro,ro,ro,edtxt";//,ro";
	GColVisibilitys=[1,1,1,1,1/* ,1 */];

	ISSort=true;
	GColSorting="server,server,server,server,server";//,server";
	ColSortIndex=0;
	SortDirection='Desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:500,height:300,
				center:true,
				modal:true,
				park :false
				};
	
	var PermitAdd=false;
	var PermitDelete=false;

	//=======Popup2 AddSupportHistory
	var Popup2;
	var PopupId2=['AddSupportHistory'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	
		SupportItem_Id:'You can only choose item from list',
		CustomerSatisfactionLevel:'Customer satisfaction level',
		Comment:'Any custom text up to 255 characters'		
	};
	var Form2FieldHelpId=['SupportItem_Id','CustomerSatisfactionLevel','Comment'];
	var Form2Str = [
		{ type:"settings" , labelWidth:90, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"SupportItem_Id",label: "مورد :",connector: RenderFile+".php?"+un()+"&act=SelectSupportItem",required:true,validate:"IsID",inputWidth:400,info:false},
		{ type: "select", name:"CustomerSatisfactionLevel", label: "C.S.Level :", options: [
			{text: "VeryGood", value: "VeryGood"},
			{text: "Good", value: "Good"},
			{text: "Fair", value: "Fair",selected:true},
			{text: "Bad", value: "Bad"},
			{text: "VeryBad", value: "VeryBad"}
		], inputWidth:400,required:true,info:true,hidden:true},
		{ type:"input" , name:"Comment", label:"توضیح :", maxLength:2048, rows: 8, validate:"", labelAlign:"left", inputWidth:402,info:false},
		{ type: "label"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Add",value: "افزودن",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
		
	//=======Popup3 ViewSupportHistory
	var Popup3;
	var PopupId3=['ViewSupportHistory'];//  popup Attach to Which Buttom of Toolbar

	//=======Form3 
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp;
	var Form3FieldHelpId=[];
	var Form3Str = [
		{ type:"settings" , labelWidth:90, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"Comment", label:"Comment :", maxLength:2048, rows: 8, validate:"", labelAlign:"left", inputWidth:402,readonly:true},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Close",value: " Close ",width :80}
		]}	
		];
		
	ISPermitAdd=ISPermit('Visp.User.SupportHistory.Add');
	PermitDelete=ISPermit('Visp.User.SupportHistory.Delete');
	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// ToolbarOfGrid   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	
	if(ISFilter==true){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"Filter","فیلتر: غیرفعال","toolbarfilter",ToolbarOfGrid_OnFilterClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterAddRow","افزودن فیلتر جدید","toolbarfilteradd",ToolbarOfGrid_OnFilterAddClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterDeleteRow","حذف فیلتر","toolbarfilterDelete",ToolbarOfGrid_OnFilterDeleteClick);
		ToolbarOfGrid.addSeparator("sep2", null);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.disableItem("Filter");
	}	

	if (ISPermitAdd){
		AddPopupAddSupportHistory();
		AddPopupViewSupportHistory();
		if(!PermitDelete)  
			DSToolbarAddButton(ToolbarOfGrid,null,"Delete","Delete(Limited)","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	}
	else
		AddPopupViewSupportHistory();
	if(PermitDelete)  
			DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	

	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	mygrid.enableMultiline(true);
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
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function AddPopupViewSupportHistory(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ViewSupportHistory","نمایش","tog_Edit");
	
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}
function Popup3OnShow(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	{
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب،روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
		Popup3.hide();
		return;
	}
	var Comment=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Comment")).getValue();
	Form3.setItemValue("Comment",Comment);
}
function Form3OnButtonClick(name){
	Popup3.hide();
}

function AddPopupAddSupportHistory(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddSupportHistory","افزودن","tow_AddSupportHistory");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",function(name,value){if(name=="SupportItem_Id") Form2.setItemFocus("Comment");});
}


function Form2OnButtonClick(name){//AddSupportHistory
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.lock();
			Popup2.hide();
			dhxLayout.cells("a").progressOn();
			DSFormInsertRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=Add&User_Id="+ParentId,Form2DoAfterInsertOk,Form2DoAfterInsertFail);
		}
	}
}

function Form2DoAfterInsertOk(){
	Form2.unlock();
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterInsertFail(){
	Form2.unlock();
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
}

function Popup2OnShow(){//AddSupportHistory
	Form2.clear();
	Form2.setItemValue("CustomerSatisfactionLevel","Fair");
}

function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};

function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else	
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
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

function ToolbarOfGrid_OnDeleteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب،روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
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
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت انجام شد");
					}
					else if(response=='LimitCreator~') 
						dhtmlx.alert("You can only delete items created by you!");
					else if(response=='LimitTime~') 
						dhtmlx.alert("You can only delete items created by you in 10 minutes!");
					else alert(response);

				});
			}	
		
		}
	});	
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
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
