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
var dhxLayout;
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
var ISDataChange=false;
window.onload = function(){
	//VARIABLE ------------------------------------------------------------------------------------------------------------------------------
	var ChangeLogDataId="<?php  echo $_GET['ParentId'];  ?>";
	if(ChangeLogDataId == "" ) {return;}
	var DataTitle="لیست تغییرات";
	var ChangeLogDataName="<?php  echo $_GET['ChangeLogDataName'];  ?>";
	if(ChangeLogDataName == "" ) {return;}
	var ExtraFilter="&ChangeLogDataName="+ChangeLogDataName+"&ChangeLogDataId="+ChangeLogDataId;
	var RenderFile="DSChangeLogRender";
	var GColIds="Logdb_Id,LogDbCDT,ResellerName,WebService_Username,ClientIP,LogType,ChildDataName,Comment";
	var GColHeaders="{#stat_count} ردیف,زمان ثبت,نام نماینده فروش,وب سرویس,آی پی,نوع تغییرات,نام داده های زیرمجموعه,توضیحات";

	var ISFilter=true;
	var FilterState=false;
	var GColFilterTypes=[1,1,1,1,1,1,1,1];
	
	var GFooter="";
	var GColInitWidths="80,120,100,90,90,90,140,600";
	var GColAligns="center,center,center,center,center,center,center,left";
	var GColTypes="ro,ro,ro,ro,ro,ro,ro,ro";
	var GColVisibilitys=[1,1,1,1,1,1,1,1];

	var ISSort=true;
	var GColSorting="server,server,server,server,server,server,server,server";
	var ColSortIndex=0
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
    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
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
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
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
	//if(ISValidResellerSession())
		PopupWindow("-1");

}	
function ToolbarOfGrid_OnEditClick(){
	RowId=mygrid.getSelectedRowId();
	if(RowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		//if(ISValidResellerSession())
			PopupWindow(RowId);
	}	
}	
function ToolbarOfGrid_OnDeleteClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	

function PopupWindow(RowId){
			popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
			if(RowId=="-1")
			  popupWindow.setText("افزودن "+DataTitle);
			else  
			  popupWindow.setText("Edit "+DataTitle);
			ISDataChange=false;  
			popupWindow.attachEvent("onClose", function(win){
				if(ISDataChange){
					if(RowId=="-1")
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
					else
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
					
					}
				return true;
			});
			popupWindow.attachURL(DataName+"Edit.php?"+un()+"&rowid="+RowId, false);
	}
}	


//-------------------------------------------------------------------DoAfterRefresh()
function DoAfterRefresh(){
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>

