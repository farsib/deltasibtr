﻿<html>
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
	DataTitle="CallerId";
	DataName="DSUser_CallerId_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	GColIds="User_CallerId_Id,CallerId";
	GColHeaders="{#stat_count} ردیف, مک/آی پی";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1];

	GFooter="";
	GColInitWidths="80,250";
	GColAligns="center,center";
	GColTypes="ro,ro";
	GColVisibilitys=[1,1];

	ISSort=true;
	GColSorting="server,server";
	ColSortIndex=1;
	SortDirection='Asc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};

	//=======Popup2 AddCallerId
	var Popup2;
	var PopupId2=['AddCallerId'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 AddCallerId
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {CallerId:'مک/آی پی'};
	var Form2FieldHelpId=['CallerId'];
	var Form2Str = [
		{ type:"settings" , labelWidth:70, inputWidth:120,offsetLeft:10  },
		{ type: "input" , name:"CallerId", label:"مک/آی پی :", validate:"",value:"", maxLength:17,inputWidth:120, info:"true"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "افزودن",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];
	//=======Popup3 EditCallerId
	var Popup3;
	var PopupId3=['EditCallerId'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 EditCallerId
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {CallerId:'مک/آی پی'};
	var Form3FieldHelpId=['CallerId'];
	var Form3Str = [
		{ type:"settings" , labelWidth:70, inputWidth:120,offsetLeft:10  },
		{ type:"hidden" , name:"User_CallerId_Id", label:"User_CallerId_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type: "input" , name:"CallerId", label:"مک/آی پی :", validate:"",value:"", maxLength:17,inputWidth:120, info:"true"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "ویرایش",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];

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
	if(ISPermit("Visp.User.CallerId.Add"))  AddPopupAddCallerId();
	if(ISPermit("Visp.User.CallerId.Edit")) AddPopupEditCallerId();
	if(ISPermit("Visp.User.CallerId.Delete")) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","Delete",ToolbarOfGrid_OnDeleteClick);
	if(ISPermit("Visp.User.CallerId.AutoAdd")){
		ToolbarOfGrid.addSeparator("sep3",null);
		ToolbarOfGrid.addText("AutoAddLabel",null,"افزودن خودکار:");
		ToolbarOfGrid.addButtonTwoState("AutoYes", null, "بلی", "ds_AutoAddYes.png", "ds_AutoAddYes.png");
		ToolbarOfGrid.addButtonTwoState("AutoNo", null, "خیر", "ds_AutoAddNo.png", "ds_AutoAddNo.png");
		ToolbarOfGrid_CheckAutoAddState();
		ToolbarOfGrid.attachEvent("onBeforeStateChange", ToolbarOfGrid_OnBeforeStateChange);
	}
	if(ISPermit("Visp.User.CallerId.Check")) DSToolbarAddButton(ToolbarOfGrid,null,"Check","بررسی","Check",ToolbarOfGrid_OnCheckClick);


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


	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnBeforeStateChange(id, state){
	// dhtmlx.message("id="+id+"<br/>state="+state);
	if(state)
		return false;
	if((id=="AutoYes")||(id=="AutoNo")){
		if(id=="AutoYes")
			ToolbarOfGrid.setItemState("AutoNo",false,false);
		else
			ToolbarOfGrid.setItemState("AutoYes",false,false);
		ToolbarOfGrid_OnAutoAddClick(id);
	}
	return true;
}

function ToolbarOfGrid_OnDeleteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
    cancel:"خیر",
    ok:"بلی",
		text: "برای حذف مطمئن هستید؟",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت انجام شد");
					}
					else alert("");

				});

		}
});
}
function ToolbarOfGrid_OnCheckClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Check&Id="+SelectedRowId+ExtraFilter,function (loader){
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			ResponseArray=response.split("~",2);
			if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
			else if(ResponseArray[0]=='OK'){
				if(parseInt(ResponseArray[1])>10){
					dhtmlx.confirm({
						title:"Attention",
						text:"This CallerId is added for more than 10 user.<br/>View more?",
						ok:"بلی",
						cancel:"خیر",
						callback:function(result){
							if(result)
								setTimeout(function(){alert("This CallerId is added for "+(ResponseArray[1].replace(/<br\/>/g,"\n")))},100);
						}
					});

				}
				else
					dhtmlx.alert("This CallerId is added for "+ResponseArray[1]);
			}
			else alert(response);
		});
}

function ToolbarOfGrid_CheckAutoAddState(){
	ToolbarOfGrid.setItemState("AutoYes",false,false);
	ToolbarOfGrid.setItemState("AutoNo",false,false);
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=AutoAddState"+ExtraFilter,function (loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		ResponseArray=response.split("~",2);
		if((response=='')||(response[0]=='~')){
			ToolbarOfGrid.setItemText("AutoAddLabel","AutoAdd: Error in loading state");
			ToolbarOfGrid.disableItem("AutoYes");
			ToolbarOfGrid.disableItem("AutoNo");
			dhtmlx.alert("خطا، "+response.substring(1));
		}
		else if(ResponseArray[0]=='OK'){
			ToolbarOfGrid.setItemState(ResponseArray[1],true,false);
		}
		else {
			ToolbarOfGrid.setItemText("AutoAddLabel","AutoAdd: Error in loading state");
			ToolbarOfGrid.disableItem("AutoYes");
			ToolbarOfGrid.disableItem("AutoNo");
			alert(response);
		}
	});
}

function ToolbarOfGrid_OnAutoAddClick(State){
	dhxLayout.progressOn();
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act="+State+ExtraFilter,function (loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
		else if(response=='OK~'& State.substring(4)=="Yes")
			parent.dhtmlx.message("افزودن خودکار فعال شد");
		else if(response=='OK~'& State.substring(4)=="No")
			parent.dhtmlx.message("افزودن خودکار غیرفعال شد");
		else alert(response);
		dhxLayout.progressOff();
	});
}

function Form2onChange(id, value){

}

function Form3onChange(id, value){
}

function AddPopupAddCallerId(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddCallerId","افزودن","tow_AddCallerId");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
}

function AddPopupEditCallerId(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"EditCallerId","ویرایش","tow_EditCallerId");
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}

function Form2OnButtonClick(name){// Add Param
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.disableItem("Add");
			Popup2.hide();
			DSFormInsertRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=insert"+ExtraFilter,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}

function Form3OnButtonClick(name){// Edit Param
	if(name=='Close') Popup3.hide();
	else{
		if(DSFormValidate(Form3,Form3FieldHelpId)){
			Form3.disableItem("Proceed");
			Popup3.hide();
			DSFormUpdateRequestProgress(dhxLayout,Form3,RenderFile+".php?"+un()+"&act=update"+ExtraFilter,Form3DoAfterUpdateOk,Form3DoAfterUpdateFail);
		}
	}
}



function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form3DoAfterUpdateOk(){
	Popup3.hide();
	Form3.enableItem("Proceed");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterUpdateFail(){
	Popup2.hide();
}

function Form3DoAfterUpdateFail(){
	Form3.enableItem("Proceed");
	Popup2.hide();
}

function Popup2OnShow(){//Add CallerId
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
}

function Popup3OnShow(){//Edit CallerId
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null){
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
		Popup3.hide();
	}
	else Form3.load(RenderFile+".php?"+un()+"&act=LoadCallerIdForm"+ExtraFilter+"&User_CallerId_Id="+SelectedRowId,function(id,respond){
		//Form3.setItemFocus("Value");
	});
}


function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	ToolbarOfGrid_CheckAutoAddState();
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
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
