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
	
	ExtraFilter="&Gift_Id="+ParentId;
	DataTitle="User";
	DataName="DSGift_Users_";
	RenderFile=DataName+"ListRender";
	GColIds="User_Gift_Id,Username,GiftStatus,User_Gift_ActiveDT,User_Gift_ExpirationDT,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,User_Gift_CDT";
	GColHeaders="{#stat_count} ردیف,نام کاربری,وضعیت هدیه,زمان فعالسازی,زمان انقضا,شناسه سرویس پایه,شناسه سرویس اضافی,شناسه سرویس آی پی,شناسه سرویس سایر,زمان ثبت";
	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="80,100,80,110,110,115,130,130,130,110";
	GColAligns="center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='desc';

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
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","tog_Service_Retrieve",ToolbarOfGrid_OnRetrieveClick);
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
	
	
	ToolbarOfGrid.addSeparator("sep3", null);
	var opts1 = [
		['SaveToXLSX', 'obj', 'XLSX'],
		['SaveToCSV', 'obj', 'CSV']
	];
	
	ToolbarOfGrid.addButtonSelect('SaveToFile',null, 'ذخیره در فایل', opts1, "ds_SaveToFile.png", "ds_SaveToFile_dis.png",false,true,6,'button');
	ToolbarOfGrid.setWidth("SaveToFile",100);
	for(var i=0;i<opts1.length;++i)
		ToolbarOfGrid.setListOptionImage("SaveToFile",opts1[i][0],"ds_"+opts1[i][0]+".png");
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGridOnClick);
	ToolbarOfGrid.disableItem('SaveToFile');
	

	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	// mygrid.enableSmartRendering(false);
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

//-------------------------------------------------------------------ToolbarOfGridOnClick()
function ToolbarOfGridOnClick(name){
	if((name=="SaveToCSV")||(name=="SaveToXLSX")){
		if(mygrid.getRowsNum()<=0){
			dhtmlx.message({title: "هشدار",type: "alert-warning",text: "داده ای برای ذخیره موجود نیست"});
			return
		}	
		if(!ISValidResellerSession()) return;

		ToolbarOfGrid.disableItem('SaveToFile');
		
		var DSFilter='';

		if(ISSort){
			state=mygrid.getSortingState();	
			SortStr="&SortField="+mygrid.getColumnId(state[0])+"&SortOrder="+((state[1]=="asc")?"asc":"desc");
		}
		else
			SortStr="&SortField=&SortOrder=";
		
		if(FilterState==true){
			for(var r=0;r<FilterRowNumber;r++){
				for(var f=0;f<mygrid.getColumnsNum();f++){
					if(GColFilterTypes[f]==1){//text filter
						var input =document.getElementById(mygrid.getColumnId(f)+"_f_"+r);
						if(input.value!="")
							DSFilter=DSFilter+"&dsfilter["+r+"]["+mygrid.getColumnId(f)+"]="+input.value;
					}
				}//for f=0
			}
		}
		
		window.open(RenderFile+".php?act=list"+ExtraFilter+"&req=SaveToFile&Type="+name.substr(6)+SortStr+DSFilter);
		setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile')},2000);
	}
}

function ToolbarOfGrid_OnEditClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		parent.parent.parent.OpenItem("CRM_FullUser","&DefaultUsername="+mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue());
	}	
}
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue();
	parent.parent.parent.OpenItem("CRM_FullUser","&DefaultUsername="+Username);
}
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
	if(mygrid.getRowsNum()<=0){
		ToolbarOfGrid.disableItem('SaveToFile');
		ToolbarOfGrid.disableItem('Edit');
	}
	else{
		ToolbarOfGrid.enableItem('SaveToFile');
		ToolbarOfGrid.enableItem('Edit');
	}
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
