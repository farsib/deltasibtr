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
	DataTitle="Installment";
	DataName="DSUser_Installment_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	GColIds="User_Installment_Id,InstallmentNo,Price,ScheduleDate,Status,Comment,ResellerName,CDT,SavingOffAmount,SavingOffExpirationDays,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id";
	GColHeaders="{#stat_count} ردیف,شماره قسط,قیمت,تاریخ اعمال,وضعیت,توضیح,نام نماینده فروش,تاریخ افزودن,شناسه سرویس پایه,شناسه سرویس اضافی,شناسه سرویس آی پی,شناسه سرویس سایر,پس انداز,تا انقضا پس انداز";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="80,90,80,80,70,400,95,110,120,130,130,120,110,140";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server";
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

	ISPermitCancelInstallment=ISPermit("Visp.User.Installment.Cancel");
	ISPermitApplyInstallment=ISPermit("Visp.User.Installment.Apply");
	if(ISPermitCancelInstallment) DSToolbarAddButton(ToolbarOfGrid,null,"CancelInstallment","لغو","CancelInstallment",ToolbarOfGrid_OnCancelInstallmentClick);
	if(ISPermitApplyInstallment) DSToolbarAddButton(ToolbarOfGrid,null,"ApplyInstallment","اعمال","ApplyInstallment",ToolbarOfGrid_OnApplyInstallmentClick);
	

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

	mygrid.attachEvent("onRowSelect", SetButton);	
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function SetButton(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId!=null){
		var Status=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Status")).getValue(); 
		if(Status=='Pending'){
			if(ISPermitCancelInstallment) ToolbarOfGrid.enableItem('CancelInstallment');
			if(ISPermitApplyInstallment) ToolbarOfGrid.enableItem('ApplyInstallment');
		}
		else{
			if(ISPermitCancelInstallment) ToolbarOfGrid.disableItem('CancelInstallment');
			if(ISPermitApplyInstallment) ToolbarOfGrid.disableItem('ApplyInstallment');
		}
	}
}	


function ToolbarOfGrid_OnCancelInstallmentClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
                ok:"بلی",
                cancel:"خیر",
		text: "از لغو کردن قسط انتخاب شده مطمئن هستید؟",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=CancelInstallment&Id="+SelectedRowId+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
						dhtmlx.message("قسط با موفقیت لفو شد");
					}
					else alert(response);

				});
		
		}
});	
}

function ToolbarOfGrid_OnApplyInstallmentClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
		text: "قسط اعمال شود؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=ApplyInstallment&Id="+SelectedRowId+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
						dhtmlx.message("قسط با موفقیت اعمال شد");
					}
					else alert(response);

				});
		
		}
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
