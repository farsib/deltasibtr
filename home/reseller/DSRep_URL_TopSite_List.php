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
var ISDataChange=false;
var SelectedRowId=0;
window.onload = function(){
	//VARIABLE ------------------------------------------------------------------------------------------------------------------------------
	var DataTitle="Report URL TopSite";
	var DataName="DSRep_URL_TopSite_";
	var ExtraFilter="";
	var RenderFile=DataName+"ListRender";

	var GColIds=    "Id,Date,Domain,NumReq";
	var GColHeaders="{#stat_count} ردیف,تاریخ,دامنه,تعداد درخواست";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,120,300,100";
	GColAligns="center,center,center,center";
	GColTypes="ro,ro,ed,ro";
	GColVisibilitys=[1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	
	// Layout   ===================================================================
	
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	//var PermitDisconnectUser=ISPermit("Online.Radius.User.DisconnectUser");


	var RepDate='';	
		
	//=======Popup2 ChangeDate
	var Popup2;
	var PopupId2=['ChangeDate'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangeDate
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Date:'تاریخ استفاده(yyyy/mm/dd)'};
	var Form2FieldHelpId=['Date'];
	var Form2Str = [
		{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"Date_Id",label: "زمان :",connector: RenderFile+".php?"+un()+"&act=SelectDate",required:true,inputWidth:100},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "برو",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];

	
	// TopToolBar   ===================================================================
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


	AddPopupChangeDate();
	
	ToolbarOfGrid.addSeparator("sep3", null);
	DSToolbarAddButton(ToolbarOfGrid,null,"OpenURL","URL بازکردن","OpenURL",ToolbarOfGrid_OnOpenURLClick);
	
	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	// mygrid.enableLightMouseNavigation(true);
	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);		
			if(input) input.style.width = cWidth-20;
		}
		return true;
	});
    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	ToolbarOfGrid_OnRetrieveClick();
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnOpenURLClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		var URL="http://"+mygrid.cells(SelectedRowId,mygrid.getColIndexById("Domain")).getValue(); 	
		if(prompt("Open URL?",URL))
			window.open(URL);
	}
}

function AddPopupChangeDate(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ChangeDate","تغییر تاریخ","tow_ChangeDate");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Form2OnButtonClick(name){//AddPayment
	F2_Username=Form2.getItemValue("Username");
	F2_StartDate=Form2.getItemValue("StartDate");
	F2_EndDate=Form2.getItemValue("EndDate");
	if(name=='Close') Popup2.hide();
	else{
		Popup2.hide();
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			dhxLayout.cells("a").progressOn();
			RepDate=Form2.getItemValue('Date_Id');
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
		}
	}
}
function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
	
	
}

function Form2DoAfterUpdateFail(){
	dhxLayout.cells("a").progressOff();
	Popup2.hide();
}


function Popup2OnShow(){//AddPayment
	//Form2.unload();
	//Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}



function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
};

function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);		
}			

function ToolbarOfGrid_OnChangeDateClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
}	

function ToolbarOfGrid_OnRetrieveClick(){
	dhxLayout.cells("a").progressOn();
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
}	

function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else	
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);;
}	
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}
		
	FilterRowNumber++;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
}	
function OnFilterTextPressEnter(){
		if(FilterState)
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
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
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
}	


}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServer(RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServer(RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate=",DoAfterRefresh);
	}
}

function DoAfterRefresh(){
	if(SelectedRowId==0)
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
