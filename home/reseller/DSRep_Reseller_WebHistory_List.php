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
	var DataTitle="Report User Web History";
	var DataName="DSRep_Reseller_WebHistory_";
	var ExtraFilter="";
	var RenderFile=DataName+"ListRender";
	
	GColIds="Reseller_WebConnection_Id,Resellername,ClientIP,uw.StartDT,uw.EndDT,BrowserInfo,NumRequest";
	GColHeaders="{#stat_count} ردیف,نام کاربری,آی پی,زمان شروع,زمان پایان,اطلاعات مرورگر,تعداد درخواست";

	var ISFilter=true;
	var FilterState=false;
	var GColFilterTypes=[1,1,1,1,1,1,1];
	
	var GFooter="";
	var GColInitWidths="90,100,120,130,130,400,120";
	var GColAligns="center,center,center,center,center,center,center";
	var GColTypes="ro,ro,ro,ro,ro,ro,ro";
	var GColVisibilitys=[1,1,1,1,1,1,1];
	
	var ISSort=true;
	var GColSorting="server,server,server,server,server,server,server";
	var ColSortIndex=0;
	var SortDirection='desc';
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
	ToolbarOfGrid_OnRetrieveClick();
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================

//-------------------------------------------------------------------GridOnSortDo(ind,type,direction)
function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};

//-------------------------------------------------------------------function ToolbarOfGrid_OnRetrieveClick()
function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	

//-------------------------------------------------------------------function ToolbarOfGrid_OnFilterClick()
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else	
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);;
}

//-------------------------------------------------------------------function ToolbarOfGrid_OnFilterAddClick()
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}
	FilterRowNumber++;
	if(FilterRowNumber>=3)
		ToolbarOfGrid.disableItem("FilterAddRow");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

//-------------------------------------------------------------------function OnFilterTextPressEnter()
function OnFilterTextPressEnter(){
		if(FilterState)
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

//-------------------------------------------------------------------function ToolbarOfGrid_OnFilterDeleteClick()
function ToolbarOfGrid_OnFilterDeleteClick(){
	DSGridDeleteFilterRow(GColIds,GColFilterTypes);
	FilterRowNumber--;
	ToolbarOfGrid.enableItem("FilterAddRow");
	if(FilterRowNumber==0){
		mygrid.detachHeader(1);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
		FilterState=false;
		ToolbarOfGrid.disableItem("Filter");
	}
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

//-------------------------------------------------------------------function GridOnDblClickDo(rId,cInd)
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);
}

//-------------------------------------------------------------------function PopupWindow(SelectedRowId)
function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("UserName")).getValue();
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=Username,"+Username, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------


function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else	
		mygrid.selectRowById(SelectedRowId,false,true,true);
	dhxLayout.progressOff();
	
}


</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>