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
	DataTitle="BatchHistory";
	DataName="DSBatchProcessHistory_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	GColIds="BatchProcess_Id,BatchProcessName,CDT,BatchItem,BatchState,StartDT,EndDT,BatchComment,ResellerName,ClientIP";
	GColHeaders="{#stat_count} ردیف,نام علمیات گروهی,زمان ایجاد,مورد,وضعیت,تاریخ شروع,تاریخ پایان,توضیحات,نماینده فروش,آی پی کاربر";

	ISFilter=false;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1];

	GFooter="";
	GColInitWidths="80,250,120,140,120,120,120,500,130,140";
	GColAligns="center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='asc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};

	// Layout   ===================================================================

	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","tog_Service_Retrieve",ToolbarOfGrid_OnRetrieveClick);
	DSToolbarAddButton(ToolbarOfGrid,null,"View","نمایش","tog_Edit",ToolbarOfGrid_OnViewClick);
	DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_Delete",ToolbarOfGrid_OnDeleteClick);

	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);

	mygrid.attachEvent("onRowSelect", SetButton);
    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);

	dhtmlxAjax.get(
		RenderFile+".php?"+un()+"&act=Cleanup",
		function(loader){
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
			else if(response!='OK~')
				dhtmlx.alert("خطا، "+response);
			else
				LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
		}
	);




	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};

function ToolbarOfGrid_OnViewClick(){
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
		title: "تایید",
                type:"confirm-warning",
		text: "حذف عملیات گروهی و تمامی موارد مشابه<br/>مطمئن هستید؟ ",
    ok: "بلی",
    cancel: "خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&BatchProcess_Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت حذف شد");
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
					}
					else alert(response);

				});
			}

		}
});
}

function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function PopupWindow(SelectedRowId){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"BatchProcess_History");
	var BatchProcess_Id=mygrid.cells(SelectedRowId,mygrid.getColIndexById("BatchProcess_Id")).getValue();
	var BatchComment=mygrid.cells(SelectedRowId,mygrid.getColIndexById("BatchComment")).getValue();
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+BatchProcess_Id+"&BatchComment="+BatchComment, false);
}



}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function SetButton(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId!=null){
		ToolbarOfGrid.enableItem("نمایش");
		var BatchState=mygrid.cells(SelectedRowId,mygrid.getColIndexById("وضعیت")).getValue();
		if((BatchState=='Pending')||(BatchState=='InProgress'))
			ToolbarOfGrid.disableItem("حذف");
		else
			ToolbarOfGrid.enableItem("حذف");
	}
	else{
		ToolbarOfGrid.disableItem("حذف");
		ToolbarOfGrid.disableItem("نمایش");
	}
}

function DoAfterRefresh(){
	if((SelectedRowId==null)||(SelectedRowId==0))
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);
	SetButton();
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
