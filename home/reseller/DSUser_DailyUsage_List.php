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
	DataTitle="User_DailyUsage";
	DataName="DSUser_DailyUsage_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";

	var GColIds="";
	var GColHeaders="";

	var ISFilter=true;
	var FilterState=false;
	var GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	var GFooter="";
	var GColInitWidths="";
	var GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	var GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	var GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	var ISSort=true;
	var GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	var ColSortIndex=0;
	var SortDirection='des';


	var GColIdsArray={
		HourlyTrafficUsage:"RealSendTr,RealReceiveTr,FinishUsedTr,BugUsedTr,Total,HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11,HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23",
		HourlyTimeUsage:"RealUsedTime,FinishUsedTi,BugUsedTi,Total,HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11,HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23"

	};
	var GColHeadersArray={
		HourlyTrafficUsage:"ترافیک ارسالی واقعی (بایت),ترافیک دریافتی واقعی (بایت),ترافیک مصرفی در حالت اتمام (بایت),ترافیک مصرفی در حالت اشکال (بایت),کل ترافیک (بایت),ساعت ۰-۱ (بایت),ساعت ۱-۲ (بایت),ساعت ۲-۳ (بایت),ساعت ۳-۴ (بایت),ساعت ۴-۵ (بایت),ساعت ۵-۶ (بایت),ساعت ۶-۷ (بایت),ساعت ۷-۸ (بایت),ساعت ۸-۹ (بایت),ساعت ۹-۱۰ (بایت),ساعت ۱۰-۱۱ (بایت),ساعت ۱۱-۱۲ (بایت),ساعت ۱۲-۱۳ (بایت),ساعت ۱۳-۱۴ (بایت),ساعت ۱۴-۱۵ (بایت),ساعت ۱۵-۱۶ (بایت),ساعت ۱۶-۱۷ (بایت),ساعت ۱۷-۱۸ (بایت),ساعت ۱۸-۱۹ (بایت),ساعت ۱۹-۲۰ (بایت),ساعت ۲۰-۲۱ (بایت),ساعت ۲۱-۲۲ (بایت),ساعت ۲۲-۲۳ (بایت),ساعت ۲۳-۲۴ (بایت)",
		HourlyTimeUsage:"زمان استفاده واقعی (ثانیه),زمان استفاده در حالت اتمام (ثانیه),زمان استفاده شده در حالت اشکال (ثانیه),مجموع (ثانیه),ساعت ۰-۱ (ثانیه),ساعت ۱-۲ (ثانیه),ساعت ۲-۳ (ثانیه),ساعت ۳-۴ (ثانیه),ساعت ۴-۵ (ثانیه),ساعت ۵-۶ (ثانیه),ساعت ۶-۷ (ثانیه),ساعت ۷-۸ (ثانیه),ساعت ۸-۹ (ثانیه),ساعت ۹-۱۰ (ثانیه),ساعت ۱۰-۱۱ (ثانیه),ساعت ۱۱-۱۲ (ثانیه),ساعت ۱۲-۱۳ (ثانیه),ساعت ۱۳-۱۴ (ثانیه),ساعت ۱۴-۱۵ (ثانیه),ساعت ۱۵-۱۶ (ثانیه),ساعت ۱۶-۱۷ (ثانیه),ساعت ۱۷-۱۸ (ثانیه),ساعت ۱۸-۱۹ (ثانیه),ساعت ۱۹-۲۰ (ثانیه),ساعت ۲۰-۲۱ (ثانیه),ساعت ۲۱-۲۲ (ثانیه),ساعت ۲۲-۲۳ (ثانیه),ساعت ۲۳-۲۴ (ثانیه)"

	};
	var GColInitWidthsArray={
		HourlyTrafficUsage:"150,150,185,190,110,110,110,110,110,110,110,110,110,110,110,120,120,120,120,120,120,120,120,120,120,120,120,120,120",
		HourlyTimeUsage:"150,175,210,190,110,110,110,110,110,110,110,110,110,110,120,120,120,120,120,120,120,120,120,120,120,120,120,120,120"
	};




	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};

	var PermitAdd=false;
	var PermitEdit=false;
	var PermitDelete=false;


	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	// ToolbarOfGrid   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();

	DSToolbarInitial(ToolbarOfGrid);


	var opts1 = [
		['HourlyTrafficUsage', 'obj', 'مصرف ترافیک'],
		['HourlyTimeUsage', 'obj', 'مصرف زمان']
	];

	ToolbarOfGrid.addButtonSelect('ReportItem',null, 'Traffic View', opts1, null, null,true,true,6,'select');
	ToolbarOfGrid.setWidth("ReportItem",110);
	for(var i=0;i<opts1.length;++i)
		ToolbarOfGrid.setListOptionImage("ReportItem",opts1[i][0],"ds_tog_"+opts1[i][0]+".png");
	ToolbarOfGrid.setListOptionSelected("ReportItem","HourlyTrafficUsage");
	ToolbarOfGrid.setItemImage("ReportItem","ds_tog_HourlyTrafficUsage.png");
	ToolbarOfGrid.addSeparator(null,null);
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGridOnClick);

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
	if(PermitEdit) mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);

	ToolbarOfGridOnClick("HourlyTrafficUsage");

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGridOnClick(name){
	if((name=='HourlyTrafficUsage')||(name=='HourlyTimeUsage')){
		ToolbarOfGrid.setItemImage("ReportItem","ds_tog_"+name+".png");
		for(;FilterRowNumber>0;FilterRowNumber--)
			DSGridDeleteFilterRow(GColIds,GColFilterTypes);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
		FilterState=false;
		ToolbarOfGrid.disableItem("Filter");
		GColIds="DailyUsage_Id,CreateDT,UsageDate,"+GColIdsArray[name];
		GColInitWidths="80,140,120,"+GColInitWidthsArray[name];
		GColHeaders="{#stat_count} ردیف,زمان ثبت,تاریخ استفاده,"+GColHeadersArray[name];
		// alert(name);
		// alert(GColIds);
		// alert(GColHeaders);
		// alert(GColInitWidths);
		mygrid.clearAll(true);
		DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
		ToolbarOfGrid_OnRetrieveClick();
	}
}
function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	ToolbarOfGrid_OnRetrieveClick();
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	var ReportItem=ToolbarOfGrid.getListOptionSelected("ReportItem");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&ReportItem="+ReportItem,DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	ToolbarOfGrid_OnRetrieveClick();
}
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}

	FilterRowNumber++;
	ToolbarOfGrid_OnRetrieveClick();
}
function OnFilterTextPressEnter(){
	if(FilterState)
		ToolbarOfGrid_OnRetrieveClick();
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
	ToolbarOfGrid_OnRetrieveClick();
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
	ToolbarOfGrid_OnRetrieveClick();
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
	mygrid.selectRowById(SelectedRowId,false,true,true);
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
