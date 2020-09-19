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
	DataTitle="User_Note";
	DataName="DSUser_Note_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";

	GColIds="User_note_Id,CDT,ResellerName,Note";
	GColHeaders="{#stat_count} ردیف,زمان ثبت,توسط,یادداشت";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1];

	GFooter="";
	GColInitWidths="100,120,120,400";
	GColAligns="center,center,center,center";
	GColTypes="ro,ro,ro,edtxt";
	GColVisibilitys=[1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server";
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
	var PermitEdit=false;
	var PermitDelete=false;

	//=======Popup2 AddNote
	var Popup2;
	var PopupId2=['AddNote'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Filename:'Filename(Char max length 64)'};
	var Form2FieldHelpId=['Filename'];
	var Form2Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"Note", label:"یادداشت :", maxLength:2048, rows: 8, validate:"", labelAlign:"left", inputWidth:400},
		{ type: "label"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Add",value: "افزودن",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];

	//=======Popup3 ViewNote
	var Popup3;
	var PopupId3=['ViewNote'];//  popup Attach to Which Buttom of Toolbar

	//=======Form3
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp;
	var Form3FieldHelpId=[];
	var Form3Str = [
		{ type:"settings" , labelWidth:90, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"Note", label:"یادداشت :", maxLength:2048, rows: 8, validate:"", labelAlign:"left", inputWidth:402,readonly:true},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];

	ISPermitAdd=ISPermit('Visp.User.Note.Add');
	ISPermitView=ISPermit('Visp.User.Note.View');
	PermitDelete=ISPermit('Visp.User.Note.Delete');
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
		AddPopupAddNote();
		AddPopupViewNote();
		if(!PermitDelete)
			DSToolbarAddButton(ToolbarOfGrid,null,"Delete","Delete(Limited)","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	}
	else
		AddPopupViewNote();
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
	//if(ISPermitView) mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);

	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);


	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function AddPopupViewNote(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ViewNote","نمایش","tog_Edit");

	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}
function Popup3OnShow(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	{
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
		Popup3.hide();
		return;
	}
	var Note=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Note")).getValue();
	Form3.setItemValue("Note",Note);
}
function Form3OnButtonClick(name){
	Popup3.hide();
}


function AddPopupAddNote(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddNote","افزودن","tow_AddNote");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}


function Form2OnButtonClick(name){//AddNote
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.disableItem("Add");
			Popup2.hide();
			dhxLayout.cells("a").progressOn();
			DSFormInsertRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=AddNote&User_Id="+ParentId,Form2DoAfterInsertOk,Form2DoAfterInsertFail);
		}
	}
}

function Form2DoAfterInsertOk(){
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterInsertFail(){
	dhxLayout.cells("a").progressOff();
	Popup2.hide();
}

function Popup2OnShow(){//AddNote
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	var tmpfilename=mygrid.cells(SelectedRowId,mygrid.getColIndexById("tmpfilename")).getValue();
	window.open(RenderFile+".php?"+un()+"&act=ViewNote&User_Note_Id="+SelectedRowId+"&tmpfilename="+tmpfilename);
	//var loader = dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=ViewNote&User_Note_Id="+SelectedRowId);
	//PopupWindow(SelectedRowId);
}
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
						dhtmlx.alert("You can only delete notes created by you!");
					else if(response=='LimitTime~')
						dhtmlx.alert("You can only delete notes created by you in 10 minutes!");
					else alert(response);

				});
			}

		}
	});
}


function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL(RenderFile+".php?"+un()+"&act=ViewNote&User_Note_Id="+SelectedRowId, false);

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
