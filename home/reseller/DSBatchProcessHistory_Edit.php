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
	var RowId = "<?php  echo addslashes($_GET['RowId']);  ?>";
	var BatchProcessName ="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString("SELECT BatchProcessName from Hbatchprocess where BatchProcess_Id=".addslashes($_GET['RowId']))?>";
	DataTitle="BatchHistory";
	DataName="DSBatchProcessHistory_";
	RenderFile=DataName+"EditRender";
	GColIds="User_Index,User_Id,BatchItemState,BatchItemDT,Username,Name,Family,BatchItemComment";
	GColHeaders="{#stat_count} ردیف,شناسه کاربر,وضعیت مورد,زمان انجام,نام کاربری,نام,نام خانوادگی,توضیح";

	ISFilter=false;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="60,80,120,150,120,110,110,250";
	GColAligns="center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='asc';

	
	// Layout   ===================================================================

	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);	
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","tog_Service_Retrieve",ToolbarOfGrid_OnRetrieveClick);

	
	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);

    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo);

	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,"&RowId="+RowId,DoAfterRefresh);

	parent.dhxLayout.dhxWins.window("popupWindow").setText("مورد عملیات گروهی ["+BatchProcessName+"]");
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================

//-------------------------------------------------------------------TopToolbar_OnExitClick()
function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}

function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,"&RowId="+RowId,DoAfterRefresh);
};

			
function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,"&RowId="+RowId,DoAfterRefresh);
}


}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function DoAfterRefresh(){
	mygrid.selectRowById(SelectedRowId,false,true,true);
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>