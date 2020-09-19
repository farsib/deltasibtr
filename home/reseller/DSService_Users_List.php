<html>
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
	DataTitle="Users";
	DataName="DSService_Users_";
	ExtraFilter="&Service_Id="+ParentId;
	ServiceType="<?php require_once("../../lib/DSInitialReseller.php");$Service_Id=$_GET['ParentId']; echo DBSelectAsString('Select ServiceType From Hservice Where Service_Id='.$Service_Id); ?>";
	RenderFile=DataName+"ListRender";

	if(ServiceType=='Base'){
			
		GColIds="User_ServiceBase_Id,r.ResellerName,Username,ServiceStatus,StartDate,EndDate,ExtraDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,CancelDT,ReturnPrice,CDT,VAT,Off";
		GColHeaders="{#stat_count} ردیف,ثبت کننده,نام کاربری,وضعیت,تاریخ شروع,تاریخ خاتمه,روز اضافی,نوع پرداخت,قیمت سرویس,تعداد اقساط,مدت اقساط,پرداخت قسط اول زمان ثبت,قیمت با مالیات,تاریخ لغو,مبلغ برگشتی,زمان ثبت,مالیات(٪),تخفیف(٪)";

		ISFilter=true;
		FilterState=false;
		GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
		GFooter="";
		GColInitWidths="60,80,120,80,70,70,70,70,85,90,100,140,85,100,80,120,100,100";
		GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
		GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
		GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

		ISSort=false;
		GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
		ColSortIndex=0;
		SortDirection='Desc';
	}
	else if(ServiceType=='ExtraCredit'){
		GColIds="User_ServiceExtraCredit_Id,Creator,Username,ServiceStatus,ApplyDT,ExtraTraffic,ExtraTime,ExtraActiveDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,ReturnPrice,CancelDT,User_ServiceBase_Id,CDT,VAT,Off,TransferUsername,TransferTraffic";
		GColHeaders="{#stat_count} ردیف,Creator,Username,ServiceStatus,ApplyDT,ExtraTraffic,ExtraTime,ExtraActiveDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,ReturnPrice,CancelDT,User_ServiceBase_Id,CDT,VAT,Off,TransferUsername,TransferTraffic";

		ISFilter=true;
		FilterState=false;
		GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
		GFooter="";
		GColInitWidths="60,80,120,80,100,80,70,90,70,70,80,90,110,60,70,80,90,120,130,100,100,100,100";
		GColAligns= "center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
		GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
		GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

		ISSort=false;
		GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
		ColSortIndex=0;
		SortDirection='Desc';
	}
	else if(ServiceType=='IP'){
		GColIds="User_ServiceIP_Id,Creator,Username,ServiceStatus,StartDate,Period,EndDate,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,CancelDT,ReturnPrice,CDT,VAT,Off",
		GColHeaders="{#stat_count} ردیف,Creator,Username,ServiceStatus,StartDate,Period,EndDate,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,CancelDT,ReturnPrice,CDT,VAT,Off",

		ISFilter=true;
		FilterState=false;
		GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
		GFooter="";
		GColInitWidths="60,80,120,80,70,70,70,70,70,80,90,110,70,80,90,80,100,100";
		GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
		GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
		GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

		ISSort=false;
		GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
		ColSortIndex=0;
		SortDirection='Desc';
	}
	else if(ServiceType=='Other'){
		GColIds="User_ServiceOther_Id,Creator,Username,ServiceStatus,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,ReturnPrice,CancelDT,CDT,VAT,Off";
		GColHeaders="{#stat_count} ردیف,Creator,Username,ServiceStatus,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,PayPrice,ReturnPrice,CancelDT,CDT,VAT,Off";

		ISFilter=true;
		FilterState=false;
		GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
		GFooter="";
		GColInitWidths="60,80,120,80,70,70,80,90,110,90,70,80,90,100,100";
		GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
		GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
		GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

		ISSort=false;
		GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server";
		ColSortIndex=0;
		SortDirection='Desc';
	}
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
	DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);

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
	

    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo);
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);
	
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
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	parent.parent.parent.OpenItem("CRM_FullUser","&DefaultUsername="+mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue());
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
		parent.parent.parent.OpenItem("CRM_FullUser","&DefaultUsername="+mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue());
	}	
}	

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+SelectedRowId, false);
}

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	}
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
