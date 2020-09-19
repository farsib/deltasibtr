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
	DataTitle="User Usage";
	DataName="DSOnline_UserUsage_";
	ExtraFilter="&Formatted=0";
	RenderFile=DataName+"ListRender";
	
	GColIds="";
	GColHeaders="";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=1;
	SortDirection='asc';
	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:520,
				center:true,
				modal:true,
				park :false
				};
	
	var GColIdsArray={
		Traffic:"STrA,STrU,STrR,YTrA,YTrU,YTrR,MTrA,MTrU,MTrR,WTrA,WTrU,WTrR,DTrA,DTrU,DTrR,Tu.ETrA,ETrU,ETrR,RealSendTr,RealReceiveTr,BugUsedTr,FinishUsedTr,ReturnTr",
		Time:"STiA,STiU,STiR,YTiA,YTiU,YTiR,MTiA,MTiU,MTiR,WTiA,WTiU,WTiR,DTiA,DTiU,DTiR,Tu.ETiA,ETiU,ETiR,RealUsedTime,BugUsedTi,FinishUsedTi",
		HourlyTrafficUsage:"HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11,HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23",
		HourlyTimeUsage:"HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11,HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23",
		Gifts:"GiftName,GiftEndDT,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftExtraTi,GiftMikrotikRateName",
		Etc:"LastSaveDT,ISFairService,UPFairStatus,FairMikrotikRateName"
	};
	var GColHeadersArray={
		Traffic:"ترافیک مجاز سرویس(بایت),ترافیک استفاده شده سرویس(بایت),ترافیک باقیمانده سرویس(بایت),ترافیک مجاز سالیانه(بایت),ترافیک استفاده شده سالیانه(بایت),ترافیک باقیمانده سالیانه(بایت),ترافیک مجاز ماهیانه(بایت),ترافیک استفاده شده ماهیانه(بایت),ترافیک باقیمانده ماهیانه(بایت),ترافیک مجاز هفتگی(بایت),ترافیک استفاده شده هفتگی(بایت),ترافیک باقیمانده هفتگی(بایت),ترافیک مجاز روزانه(بایت),ترافیک استفاده شده روزانه(بایت),ترافیک باقیمانده روزانه(بایت),اضافه ترافیک مجاز(بایت),اضافه ترافیک استفاده شده(بایت),اضافه ترافیک باقیمانده(بایت),ترافیک ارسال واقعی(بایت),ترافیک دریافت واقعی(بایت),ترافیک مصرف شده در حالت اشکال(بایت),ترافیک مصرف شده در حالت اتمام(بایت),ترافیک برگشتی(بایت)",
		Time:"زمان مجاز سرویس(ثانیه),زمان استفاده شده سرویس(ثانیه),زمان باقیمانده سرویس(ثانیه),زمان مجاز سالیانه(ثانیه),زمان استفاده شده سالیانه(ثانیه),زمان باقیمانده سالیانه(ثانیه),زمان مجاز ماهیانه(ثانیه),زمان استفاده شده ماهیانه(ثانیه),زمان باقیمانده ماهیانه(ثانیه),زمان مجاز هفتگی(ثانیه),زمان استفاده شده هفتگی(ثانیه),زمان باقیمانده هفتگی(ثانیه),زمان مجاز روزانه(ثانیه),زمان استفاده شده روزانه(ثانیه),زمان باقیمانده روزانه(ثانیه),اضافه زمان مجاز(ثانیه),اضافه زمان استفاده شده(ثانیه),اضافه زمان باقیمانده(ثانیه),زمان استفاده شده واقعی(ثانیه),زمان استفاده شده در حالت اشکال(ثانیه),زمان استفاده شده در حالت اتمام(ثانیه)",
		HourlyTrafficUsage:"ساعت ۰-۱ (بایت),ساعت ۱-۲ (بایت),ساعت ۲-۳ (بایت),ساعت ۳-۴ (بایت),ساعت ۴-۵ (بایت),ساعت ۵-۶ (بایت),ساعت ۶-۷ (بایت),ساعت ۷-۸ (بایت),ساعت ۸-۹ (بایت),ساعت ۹-۱۰ (بایت),ساعت ۱۰-۱۱ (بایت),ساعت ۱۱-۱۲ (بایت),ساعت ۱۲-۱۳ (بایت),ساعت ۱۳-۱۴ (بایت),ساعت ۱۴-۱۵ (بایت),ساعت ۱۵-۱۶ (بایت),ساعت ۱۶-۱۷ (بایت),ساعت ۱۷-۱۸ (بایت),ساعت ۱۸-۱۹ (بایت),ساعت ۱۹-۲۰ (بایت),ساعت ۲۰-۲۱ (بایت),ساعت ۲۱-۲۲ (بایت),ساعت ۲۲-۲۳ (بایت),ساعت ۲۳-۲۴ (بایت)",
		HourlyTimeUsage:"ساعت ۰-۱ (ثانیه),ساعت ۱-۲ (ثانیه),ساعت ۲-۳ (ثانیه),ساعت ۳-۴ (ثانیه),ساعت ۴-۵ (ثانیه),ساعت ۵-۶ (ثانیه),ساعت ۶-۷ (ثانیه),ساعت ۷-۸ (ثانیه),ساعت ۸-۹ (ثانیه),ساعت ۹-۱۰ (ثانیه),ساعت ۱۰-۱۱ (ثانیه),ساعت ۱۱-۱۲ (ثانیه),ساعت ۱۲-۱۳ (ثانیه),ساعت ۱۳-۱۴ (ثانیه),ساعت ۱۴-۱۵ (ثانیه),ساعت ۱۵-۱۶ (ثانیه),ساعت ۱۶-۱۷ (ثانیه),ساعت ۱۷-۱۸ (ثانیه),ساعت ۱۸-۱۹ (ثانیه),ساعت ۱۹-۲۰ (ثانیه),ساعت ۲۰-۲۱ (ثانیه),ساعت ۲۱-۲۲ (ثانیه),ساعت ۲۲-۲۳ (ثانیه),ساعت ۲۳-۲۴ (ثانیه)",
		Gifts:"نام هدیه,تاریخ پایان هدیه,ضریب ترافیک هدیه,ضریب زمان هدیه,ترافیک هدیه (بایت),زمان اضافی هدیه (ثانیه),نام سرعت میکروتیک",
		Etc:"زمان آخرین ذخیره,سرویس مصرف منصفانه هست؟,کاربر در وضعیت مصرف منصفانه است؟,سرعت مصرف منصفانه"
	};
	var GColInitWidthsArray={
		Traffic:"140,185,160,140,180,160,140,180,160,140,180,160,140,180,160,140,170,150,140,140,210,200,120",
		Time:"140,180,160,140,180,160,140,180,160,140,180,160,140,160,140,140,160,160,180,210,200",
		HourlyTrafficUsage:"110,110,110,110,110,110,110,110,110,110,120,120,120,120,120,120,120,120,120,120,120,120,120,120",
		HourlyTimeUsage:"110,110,110,110,110,110,110,110,110,110,120,120,120,120,120,120,120,120,120,120,120,120,120,120",
		Gifts:"140,140,120,120,120,140,200",
		Etc:"140,170,200,200"
	};
	
	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// ToolbarOfGrid   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	
	var opts1 = [
		['Traffic', 'obj', 'نمایش ترافیک'],
		['Time' , 'obj', 'نمایش زمان'],
		['HourlyTrafficUsage', 'obj', 'مصرف ترافیک ساعتی'],
		['HourlyTimeUsage', 'obj', 'مصرف زمان ساعتی'],
		['Gifts', 'obj', 'هدایا'],
		['Etc', 'obj', 'غیره'],
	];
	
	ToolbarOfGrid.addButtonSelect('ReportItem',null, 'Traffic View', opts1, null, null,true,true,6,'select');
	ToolbarOfGrid.setWidth("ReportItem",140);
	for(var i=0;i<opts1.length;++i)
		ToolbarOfGrid.setListOptionImage("ReportItem",opts1[i][0],"ds_tog_"+opts1[i][0]+".png");
	ToolbarOfGrid.setListOptionSelected("ReportItem","Traffic");
	ToolbarOfGrid.setItemImage("ReportItem","ds_tog_Traffic.png");
	
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
	
	DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	
	ToolbarOfGrid.addSeparator(null,null);
	ToolbarOfGrid.addButtonTwoState("Formatted", null, "تغییر فرمت نمایش", "ds_tow_FormatFields.png", "ds_tow_FormatFields_dis.png");
	ToolbarOfGrid.attachEvent("onStateChange", function(id, state){if(id=="Formatted"){ToolbarOfGrid_OnRetrieveClick();}});
	ToolbarOfGrid.addSeparator(null,null);
	DSToolbarAddButton(ToolbarOfGrid,null,"SaveToFile","ذخیره در فایل","SaveToFile",ToolbarOfGrid_OnSaveToFileClick);
	
	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	mygrid.enableColumnMove(true);
	GColIds="Tu.User_Id,Username,Name,Family,LastRequestDT,"+GColIdsArray["Traffic"];
	GColHeaders="{#stat_count} ردیف,نام کاربری,نام,نام خانوادگی,زمان آخرین بروزرسانی,"+GColHeadersArray["Traffic"];
	GColInitWidths="80,100,100,150,140,"+GColInitWidthsArray["Traffic"];
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	
	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);		
			if(input) input.style.width = cWidth-20;
		}
		return true;
	});
	if(ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);
	
	ToolbarOfGrid_OnRetrieveClick();
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGridOnClick(name){
	if((name=='Traffic')||(name=='Time')||(name=='HourlyTrafficUsage')||(name=='HourlyTimeUsage')||(name=='Gifts')||(name=='Etc')){
		if(name=="Etc"){
			ToolbarOfGrid.disableItem("Formatted");
			ToolbarOfGrid.setItemState("Formatted",false,false);
		}
		else
			ToolbarOfGrid.enableItem("Formatted");
		ToolbarOfGrid.setItemImage("ReportItem","ds_tog_"+name+".png");
		for(;FilterRowNumber>0;FilterRowNumber--)
			DSGridDeleteFilterRow(GColIds,GColFilterTypes);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
		FilterState=false;
		ToolbarOfGrid.disableItem("Filter");
		GColIds="Tu.User_Id,Username,Name,Family,LastRequestDT,"+GColIdsArray[name];
		GColInitWidths="80,100,100,150,140,"+GColInitWidthsArray[name];
		GColHeaders="{#stat_count} ردیف,نام کاربری,نام,نام خانوادگی,زمان آخرین درخواست,"+GColHeadersArray[name];
		// alert(name);
		// alert(GColIds);
		// alert(GColHeaders);
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
	var Formatted=(ToolbarOfGrid.getItemState("Formatted"))?1:0;
	ExtraFilter="&req=ShowInGrid&ReportItem="+ReportItem+"&Formatted="+Formatted;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
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
	
function ToolbarOfGrid_OnEditClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		PopupWindow(SelectedRowId);
	}	
}	

function ToolbarOfGrid_OnSaveToFileClick(){
	if(!ISValidResellerSession()) return;
	ToolbarOfGrid.disableItem('SaveToFile');
	
	var DSFilter='';
	state=mygrid.getSortingState();	
	SortStr="&SortField="+mygrid.getColumnId(state[0])+"&SortOrder="+((state[1]=="asc")?"asc":"desc");
	
	if(FilterState==true){
		for(var r=0;r<FilterRowNumber;r++){
			for(var f=0;f<mygrid.getColumnsNum();f++){
				if(GColFilterTypes[f]==1){
					var input =document.getElementById(mygrid.getColumnId(f)+"_f_"+r);
					if(input.value!="")
						DSFilter=DSFilter+"&dsfilter["+r+"]["+mygrid.getColumnId(f)+"]="+input.value;
				}
			}
		}
	}	

	var ReportItem=ToolbarOfGrid.getListOptionSelected("ReportItem");
	var Formatted=(ToolbarOfGrid.getItemState("Formatted"))?1:0;
	ExtraFilter="&req=SaveToFile&ReportItem="+ReportItem+"&Formatted="+Formatted;
	window.location=RenderFile+".php?"+un()+"&act=list"+SortStr+DSFilter+"&FilterRowNumber="+FilterRowNumber+ExtraFilter;
	setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile')},2000);
}

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=User_Id,"+SelectedRowId, false);
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
	if(SelectedRowId==0)
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
