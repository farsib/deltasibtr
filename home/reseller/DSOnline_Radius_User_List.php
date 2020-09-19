<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSUserEdit ....................................................................................");
	if($LastError!=""){
		DSDebug(0,"Session Expire");
		$LoginResellerName=Get_InputIgnore('GET','DB','LoginResellerName','STR',0,32,0,0);
		$Reseller_Id=DBSelectAsString("Select Reseller_Id from Hreseller where ResellerName='$LoginResellerName'");
	}
	else
		$Reseller_Id=$LReseller_Id;
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
var ISDataChange=false;
var SelectedRowId=0;
window.onload = function(){
	//VARIABLE ------------------------------------------------------------------------------------------------------------------------------
	DataTitle="OnlineRadiusUser";
	DataName="DSOnline_Radius_User_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";

	GColIds="<?php echo DBSelectAsString("Select ColIds from Hgrid_layout where Reseller_Id='$Reseller_Id' and ItemName='OnlineRadiusUser'");?>";
	GColHeaders=<?php
		$ColHeader=DBSelectAsString("Select ColHeaders from Hgrid_layout where Reseller_Id='$Reseller_Id' and ItemName='OnlineRadiusUser'");
		$StrFindArray=Array("{#stat_count} rows","RUsername","DownloadSpeed(Kb/s)","UploadSpeed(Kb/s)","ServiceInfoName","ServiceRate","VispName","NasName","CenterName","Name","Family","ISFinishUser","CallingId","CalledId","FramedIP","SRCNasIP","NasIP","StartTime","LastUpdate","SessionTime(s)","SendTr(B)","ReceiveTr(B)","NasPortId","NasPortType","ServiceType","FramedProtocol","URLReporting","InterimTime","InterimCount","TerminateCause","ReturnTr","InternetUse","IntranetUse","MessengerUse","FreeUse","SpecialUse");
		$StrReplaceArray=Array("{#stat_count} ردیف","نام کاربری","سرعت دانلود(Kb/s)","سرعت آپلود(Kb/s)","نام سرویس محاسبه","ضریب سرویس","ارائه دهنده مجازی اینترنت","نام ردیوس","نام مرکز","نام","نام خانوادگی","کاربر در حالت اتمام","مک/آی پی","نام سرویس/آی پی سرور","آی پی کاربر","آی پی ردیوس مبدا","آی پی ردیوس","زمان شروع","آخرین بروزرسانی","زمان نشست(s)","ترافیک ارسال(B)","ترافیک دریافت(B)","شناسه پورت ردیوس","نوع پورت ردیوس","نوع سرویس","پروتکل","گزارش صفحات بازدید شده","زمان بروزرسانی از سرور","تعداد بروزرسانی از سرور","علت قطع","ترافیک بازگشتی","مصرف اینترنت","مصرف اینترانت","مصرف پیام رسان","مصرف رایگان","مصرف ویژه");
		echo '"'.str_replace($StrFindArray,$StrReplaceArray,$ColHeader).'"';
		?>;

	ISFilter=true;
	FilterState=true;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="80,100,120,120,120,100,140,100,100,100,100,120,100,140,100,100,120,100,100,100,100,100,150,100,100,150,150,150,150,100,100,100,100,100,100,100";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='asc';

	GColIds_listSummary="Nas_Id,NasName,NasIpAddress,OnlineUser,LastDownloadSpeed,LastUploadSpeed,ServiceInfo_Id,ReturnTr,InternetUse,IntranetUse,MessengerUse,FreeUse,SpecialUse";
	GColHeaders_listSummary="شناسه ردیوس,نام ردیوس {#stat_count} ردیف,آی پی ردیوس,کاربر آنلاین,سرعت دانلود(Kb/s),سرعت آپلود(Kb/s),شناسه اطلاعات سرویس,ترافیک برگشتی(Kb),استفاده اینترنت(Kb),استفاده اینترانت(Kb),استفاده پیام رسان(Kb),استفاده رایگان(Kb),استفاده ویژه(Kb)";
	
	GFooter_listSummary="";
	GColInitWidths_listSummary="10,140,100,100,150,120,1,120,120,120,130,120,120";
	GColAligns_listSummary="center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes_listSummary="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys_listSummary=[0,1,1,1,1,1,0,1,1,1,1,1,1];

	ISSort_listSummary=false;
	GColSorting_listSummary="server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex_listSummary=0;
	SortDirection_listSummary='Asc';
	
	
	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:520,
				center:true,
				modal:true,
				park :false
				};
	
	// Layout   ===================================================================
	
	FilterRowNumber=0;
	
	var PermitDisconnectUser=ISPermit("Online.Radius.User.DisconnectUser");
	var PermitDeleteSession=ISPermit("Online.Radius.User.DeleteSession");
	var PermitDisconnectAllUser=ISPermit("Online.Radius.User.DisconnectAllUser");
	var PermitlistSummary=ISPermit("Online.Radius.User.ListSummary");
	
	if(PermitlistSummary){
		var LayoutTwoColumn;
		if(typeof(Storage) !== "undefined")
			LayoutTwoColumn=localStorage.getItem('OnlineUser_dhxLayoutTwoColumn');
		if(LayoutTwoColumn){
			dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
			DSLayoutInitial1(dhxLayout);
			dhxLayout.cells("a").setWidth(Math.round(window.innerWidth/4));
		}
		else{
			dhxLayout = new dhtmlXLayoutObject(document.body, "2E");
			DSLayoutInitial1(dhxLayout);
			dhxLayout.cells("a").setHeight(Math.round(window.innerHeight/4));
		}
		
		ToolbarOfGrid_listSummary = dhxLayout.cells("a").attachToolbar();
		DSToolbarInitial(ToolbarOfGrid_listSummary);
		DSToolbarAddButton(ToolbarOfGrid_listSummary,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_listSummary_OnRetrieveClick);
		if(typeof(Storage) !== "undefined"){
			ToolbarOfGrid_listSummary.addSeparator("sep1",null);
			DSToolbarAddButton(ToolbarOfGrid_listSummary,null,"ChangeView","تغییر نما","ChangeView",ToolbarOfGrid_listSummary_OnChangeViewClick);
		}
		ToolbarOfGrid = dhxLayout.cells("b").attachToolbar();
	}
	else{
		dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
		DSLayoutInitial(dhxLayout);
		ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	}
	
	
	
	// TopToolBar   ===================================================================
	
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	if(ISFilter==true){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"Filter","فیلتر: فعال","toolbarfilter",ToolbarOfGrid_OnFilterClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterAddRow","افزودن فیلتر جدید","toolbarfilteradd",ToolbarOfGrid_OnFilterAddClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterDeleteRow","حذف فیلتر","toolbarfilterDelete",ToolbarOfGrid_OnFilterDeleteClick);
		ToolbarOfGrid.addSeparator("sep2", null);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.disableItem("Filter");
	}
	
	DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	ToolbarOfGrid.addSeparator("sep3", null);
	
	var SpacerButton="Edit";
	
	if(PermitDeleteSession)   {
		DSToolbarAddButton(ToolbarOfGrid,null,"DeleteSession","حذف نشست","DeleteSession",ToolbarOfGrid_OnDeleteSession);
		SpacerButton="DeleteSession";
	}	

	if(PermitDisconnectUser)   {
		DSToolbarAddButton(ToolbarOfGrid,null,"DisconnectUser","قطع کاربر","DisconnectUser",ToolbarOfGrid_OnDisconnectUser);
		SpacerButton="DisconnectUser";
	}	
	
	if(PermitDisconnectAllUser)   {
		DSToolbarAddButton(ToolbarOfGrid,null,"DisconnectAllUser","قطع همه کاربران","DisconnectAllUser",ToolbarOfGrid_OnDisconnectAllUser);
		SpacerButton="DisconnectAllUser";
	}	

	ToolbarOfGrid.addSpacer(SpacerButton);
	ToolbarOfGrid.addButtonSelect("SaveLayout", null, "ذخیره طرح بندی", [], "tog_SaveLayoutButton.png", "tog_SaveLayoutButton_dis.png");
	ToolbarOfGrid.setWidth("SaveLayout",115);
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGrid_OnClick);

	ToolbarOfGrid.addListOption("SaveLayout", "ResetLayout", null, "button", "طرح بندی پیشفرض", "tog_ResetLayoutButton.png");
	ToolbarOfGrid.hideItem("SaveLayout");	
	
	
	// mygrid   ===================================================================
	if(PermitlistSummary){
		mygrid_listSummary =dhxLayout.cells("a").attachGrid();
		DSGridInitial(mygrid_listSummary,GColIds_listSummary,GColHeaders_listSummary,GColInitWidths_listSummary,GColAligns_listSummary,GColTypes_listSummary,GColVisibilitys_listSummary,GFooter_listSummary,ISSort_listSummary,GColSorting_listSummary,ColSortIndex_listSummary,SortDirection_listSummary);
		mygrid_listSummary.enableSmartRendering(false);
		mygrid =dhxLayout.cells("b").attachGrid();
	}
	else
		mygrid =dhxLayout.cells("a").attachGrid();
	
	mygrid.enableColumnMove(true);
	mygrid.enableHeaderMenu("false,false,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true");
	
	
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	
	mygrid.attachEvent("onAfterCMove", function(cInd,posInd){
		var TempArr=GColIds.split(",");
		var r=TempArr.splice(cInd,1);
		TempArr.splice(posInd,0,r);
		GColIds=TempArr.join();
		
		TempArr=GColHeaders.split(",");
		r=TempArr.splice(cInd,1);
		TempArr.splice(posInd,0,r);
		GColHeaders=TempArr.join();
		
		TempArr=GColInitWidths.split(",");
		r=TempArr.splice(cInd,1);
		TempArr.splice(posInd,0,r);
		GColInitWidths=TempArr.join();
		ToolbarOfGrid.showItem("SaveLayout");
		ToolbarOfGrid.enableItem("SaveLayout");
	});	
	
	
	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);		
			if(input) input.style.width = cWidth-20;
		}
		var GColInitWidthsArray= GColInitWidths.split(",");
		GColInitWidthsArray[cInd]=cWidth;
		GColInitWidths=GColInitWidthsArray.join();		
		ToolbarOfGrid.showItem("SaveLayout");
		ToolbarOfGrid.enableItem("SaveLayout");
		return true;
	});
	
    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo); 
	
	// LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	if(PermitlistSummary){
		mygrid_listSummary.attachEvent("onRowDblClicked",Grid_listSummaryOnDblClickDo); 
		ToolbarOfGrid_listSummary_OnRetrieveClick();
	}
	ToolbarOfGrid_OnFilterAddClick();
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_listSummary_OnChangeViewClick(){
	var MsgText="";
	if(localStorage.getItem('OnlineUser_dhxLayoutTwoColumn')){
		localStorage.removeItem('OnlineUser_dhxLayoutTwoColumn');
		MsgText="به نمای 2 ردیف تغییر یافت";
	}
	else{
		localStorage.setItem('OnlineUser_dhxLayoutTwoColumn',1);
		MsgText="به نمای 2 ستون تغییر یافت";
	}
	dhtmlx.alert({text:MsgText,callback:function(){dhxLayout.progressOn();window.location.reload();},ok:"بارگذاری مجدد"});
}

function ToolbarOfGrid_OnClick(name){
	if((name=="SaveLayout")||(name=="ResetLayout")){
		ToolbarOfGrid.disableItem("SaveLayout");
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm",
			text: "آیا مطمئن هستید؟<br/>بارگذاری مجدد صفحه کاربر",
			ok:"بلی",
			cancel:"خیر",
			callback: function(result) {
				if(result){
					dhxLayout.progressOn();
					dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=ChangeLayout&Req="+name,"&GColIds="+GColIds+"&GColHeaders="+GColHeaders+"&GColInitWidths="+GColInitWidths,function (loader){
						dhxLayout.progressOff();
						ToolbarOfGrid.hideItem("SaveLayout");
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						if((response=='')||(response[0]=='~'))
							dhtmlx.alert({text:"خطا، "+response.substring(1),callback:function(){dhxLayout.progressOn();window.location.reload();},ok:"Reload"});
						else if(response=='OK~')
							dhtmlx.alert({text:(name=="SaveLayout"?"طرح بندی جدید با  موفقیت ذخیره شد.":"طرح بندی پیشفرض با موفقیت اعمال شد"),callback:function(){dhxLayout.progressOn();window.location.reload();},ok:"بارگذاری مجدد"});
						else{
							alert(response);
							dhxLayout.progressOn();
							window.location.reload();
						}
					});
				}
				else
					ToolbarOfGrid.enableItem("SaveLayout");
			}
		});
	}
}

function ToolbarOfGrid_OnDeleteSession(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
		text: "Delete Session of user?",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DeleteSession&Id="+SelectedRowId+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("نشست با موفقیت حذف شد");
					}
					else alert(response);

				});
		
		}
});	
}

function ToolbarOfGrid_OnDisconnectUser(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا کاربر را انتخاب کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "Verify",
		type:"confirm-warning",
		text: "Disconnect user?",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DisconnectUser&Id="+SelectedRowId+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						dhtmlx.message("کاربر با موفقیت به صف فطع افزوده شد");
					}
					else alert(response);

				});
		
		}
});	
}

function ToolbarOfGrid_OnDisconnectAllUser(){
//	SelectedRowId=mygrid.getSelectedRowId();
//	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "!لطفا کاربری را انتخاب کنید"});
//	else
	var FilterRowNumber=0;
	GColumnIdArray=GColIds.split(",");
	while (document.getElementById(GColumnIdArray[0]+"_f_"+FilterRowNumber)!=null) FilterRowNumber++;

	DSFilter='';
	var ColumnIdArray=GColIds.split(",");
	for(var r=0;r<FilterRowNumber;r++){
	
		for(var f=0;f<ColumnIdArray.length;f++){
			if(GColFilterTypes[f]==1){//text filter
				var input =document.getElementById(ColumnIdArray[f]+"_f_"+r);
				if(input.value!="")
					DSFilter=DSFilter+"&dsfilter["+r+"]["+ColumnIdArray[f]+"]="+input.value;
			}
		}//for
	}
	

	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		text: "آیا از قطع همه کاربران مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DisconnectAllUser"+DSFilter+"&FilterRowNumber="+FilterRowNumber+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert({text:"خطا، "+response.substring(1),ok:"بستن"});
					else if(response=='OK~') {
						dhtmlx.message("کاربر با موفقیت به صف فطع افزوده شد");
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

function Grid_listSummaryOnDblClickDo(rId,cInd){
	var Nas_Id=mygrid_listSummary.cells(rId,mygrid_listSummary.getColIndexById("Nas_Id")).getValue();
	var ServiceInfo_Id=mygrid_listSummary.cells(rId,mygrid_listSummary.getColIndexById("ServiceInfo_Id")).getValue();
	var NasName=mygrid_listSummary.cells(rId,mygrid_listSummary.getColIndexById("NasName")).getValue(); 	
	if(Nas_Id==0){
		ExtraFilter="&dsfilter[-1][oru.ServiceInfo_Id]=="+ServiceInfo_Id;
		dhxLayout.cells("b").setText("All NAS'");
	}
	else{
		ExtraFilter="&dsfilter[-1][oru.Nas_Id]=="+Nas_Id+"&dsfilter[-1][oru.ServiceInfo_Id]=="+ServiceInfo_Id;
		if(ServiceInfo_Id==1)
			dhxLayout.cells("b").setText("Detail of NAS '<span style='color:blue'>"+NasName+"</span>'");
		else
			dhxLayout.cells("b").setText("Detail of NAS '<span style='color:red;'>"+NasName+"</span>'");
	}
	ToolbarOfGrid_listSummary_OnRetrieveClick();
	ToolbarOfGrid_OnRetrieveClick();
}
			
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);		
}			

function ToolbarOfGrid_listSummary_OnRetrieveClick(){
	var rowIndex=mygrid_listSummary.getSelectedRowId();
	if(rowIndex==null)
		rowIndex=0;
	dhxLayout.progressOn();
	mygrid_listSummary.clearAll();
	mygrid_listSummary.loadXML(RenderFile+".php?"+un()+"&act=listSummary",function(){
		if(rowIndex==0)
			mygrid_listSummary.selectRow(0);
		else
			mygrid_listSummary.selectRowById(rowIndex,false,true,true);
		dhxLayout.progressOff();
		ToolbarOfGrid_OnRetrieveClick();
	});
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
	//if(ISValidResellerSession())
		PopupWindow(0);

}	
function ToolbarOfGrid_OnEditClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		//if(ISValidResellerSession())
			PopupWindow(SelectedRowId);
	}	
}	
function ToolbarOfGrid_OnDeleteClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("oru.RUsername")).getValue();
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=Username,"+Username, false);
}

function DSLayoutInitial1(Layout){
	Layout.setSkin(dhxLayout_main_skin);
	//Layout.cells("a").hideHeader();
	dhxLayout.cells("a").setText("مجموع");
	dhxLayout.cells("b").setText("همه");
	ExtraFilter="&dsfilter[-1][oru.ServiceInfo_Id]==1";
	Layout.dhxWins.setEffect("move", true);
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