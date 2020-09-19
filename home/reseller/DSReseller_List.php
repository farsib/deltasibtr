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
		.PermSp{
			font-weight:bold;
			color:navy;
		}
		.ResellerSp{
			font-weight:bold;
			color:green;
		}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
var SelectedRowId=0;
window.onload = function(){
	DataTitle="Reseller";
	DataName="DSReseller_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	GColIds="Reseller_Id,ResellerName,ResellerType,ISEnable,ResellerCDT,LastLoginIP,LastLoginDT";
	GColHeaders="{#stat_count} ردیف,نام,نوع,فعال,زمان ایجاد ,آخرین آی پی ورود,زمان آخرین ورود";

	ISFilter=false;
	FilterState=false;
	GColFilterTypes=[0,0,0,0,0,0,0];
	
	GFooter="";
	GColInitWidths="70,280,90,70,140,140,140";
	GColAligns="center,left,center,center,center,center,center,center";
	GColTypes="ro,tree,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1];

	ISSort=false;
	GColSorting="server,server,server,server,server,server,server";
	ColSortIndex=1;
	SortDirection='asc';

	EditWindow={id:"popupWindow",x:340,y:20,width:750,height:550,center:true,modal:true,park :false};
	var PermitAdd=ISPermit("CRM.Reseller.Add");
	var PermitView=ISPermit("CRM.Reseller.View");
	var PermitEdit=ISPermit("CRM.Reseller.Edit");
	var PermitDelete=ISPermit("CRM.Reseller.Delete");
	var PermitCopyPermission=ISPermit("CRM.Reseller.Permit.Edit");
	var CopyFrom_Reseller_Id=0;
	var CopyFrom_ResellerName="";

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
	
	if(PermitAdd&&PermitView) DSToolbarAddButton(ToolbarOfGrid,null,"Add","افزودن","tog_Add",ToolbarOfGrid_OnAddClick);
	if(PermitEdit) DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	else if(PermitView) DSToolbarAddButton(ToolbarOfGrid,null,"View","نمایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	if(PermitDelete) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	if(PermitCopyPermission){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"CopyPermissions","کپی سطوح دسترسی","tog_CopyPermissions",ToolbarOfGrid_OnCopyPermissionsClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"PastePermissions","جایگذاری سطوح دسترسی","tog_PastePermissions",ToolbarOfGrid_OnPastePermissionsClick);
		ToolbarOfGrid.disableItem("PastePermissions");
		ToolbarOfGrid.addSpacer("PastePermissions");
		ToolbarOfGrid.addText("CopiedResellerText",null,"");
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
	// mygrid.attachEvent("onRowSelect", function(id,cid){alert("id="+id+"\ncid="+cid);});
    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	if(PermitView||PermitEdit)mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo); 
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//===============================================================================================================================
function ToolbarOfGrid_OnCopyPermissionsClick(){
	var Reseller_Id=mygrid.getSelectedRowId();
	if(Reseller_Id==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		dhxLayout.progressOn();
		dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=LoadCopyInformation","&From_Reseller_Id="+Reseller_Id,function(loader){
			dhxLayout.progressOff();
			var response=loader.xmlDoc.responseText;
			response=CleanError(response);
			var ResponseArray=response.split("~",3);
			if((response=='')||(response[0]=='~'))dhtmlx.alert("خظا, "+response.substring(1));
			else if(ResponseArray[0]!='OK')
				dhtmlx.alert("خطا، "+response);
			else{
				if((ResponseArray[1]+ResponseArray[2])<=0)
					dhtmlx.alert({text:"Selected reseller has no permissions to copy",type:"alert-error"});
				else{
					CopyFrom_Reseller_Id=Reseller_Id;
					CopyFrom_ResellerName=mygrid.cells(CopyFrom_Reseller_Id,1).getValue();
					dhtmlx.alert({text:"کپی شدند [<span class='ResellerSp'>"+CopyFrom_ResellerName+"]</span> سطوح دسترسی <br/>تعداد "+ResponseArray[1]+" مورد <span class='PermSp'>مجاز است</span><br/>تعداد "+ResponseArray[2]+" مورد <span class='PermSp'>مجاز نیست</span>", ok:"بستن"});
					ToolbarOfGrid.setItemText("CopiedResellerText","[<span class='ResellerSp' title='Permitted="+ResponseArray[1]+"\r\nNot Permitted="+ResponseArray[2]+"'>"+CopyFrom_ResellerName+"</span>] کپی شده");
					ToolbarOfGrid.enableItem("PastePermissions");
				}
			}
		});
	}
}
function ToolbarOfGrid_OnPastePermissionsClick(){
	PasteTo_Reseller_Id=mygrid.getSelectedRowId();
	if(PasteTo_Reseller_Id==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		if(PasteTo_Reseller_Id==CopyFrom_Reseller_Id)
			dhtmlx.alert({text:"جایگذاری سطوح دسترسی به خودش امکان پذیر نیست",type:"alert-error",ok:"بستن"});
		else if((CopyFrom_Reseller_Id!=mygrid.getParentId(PasteTo_Reseller_Id))&&(mygrid.getParentId(CopyFrom_Reseller_Id)!=mygrid.getParentId(PasteTo_Reseller_Id)))
			dhtmlx.alert({text:"You can only paste permissions of a reseller/operator to <span style='font-style:oblique;font-weight:bold'>its direct child</span> or to reseller/operator <span style='font-style:oblique;font-weight:bold'>at the same level</span>.",type:"alert-error"});
		else{
			dhxLayout.progressOn();
			dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=LoadPasteInformation","&From_Reseller_Id="+CopyFrom_Reseller_Id+"&To_Reseller_Id="+PasteTo_Reseller_Id,function(loader){
				dhxLayout.progressOff();
				var response=loader.xmlDoc.responseText;
				response=CleanError(response);
				var ResponseArray=response.split("~",4);
				if((response=='')||(response[0]=='~'))
					dhtmlx.alert("خطا، "+response.substring(1));
				else if(ResponseArray[0]!='OK') 
					dhtmlx.alert("خطا، "+response);
				else{
					var PasteTo_ResellerName=mygrid.cells(PasteTo_Reseller_Id,1).getValue();
					var ConfirmMsg="کپی سطوح دسترسی از<br/>[<span class='ResellerSp'>"+CopyFrom_ResellerName+"</span>]<br/>به<br/>[<span class='ResellerSp'>"+PasteTo_ResellerName+"</span>]<br/>تعداد "+ResponseArray[1]+" مورد <span class='PermSp'>مجاز است</span><br/>تعداد " +ResponseArray[2]+" مورد <span class='PermSp'>مجاز نیست</span><br/>";
					if(ResponseArray[3]>0)
						ConfirmMsg+="<br/>Also "+(ResponseArray[3]*ResponseArray[2])+" item"+(ResponseArray[3]*ResponseArray[2]>1?"s":"")+" will be <span class='PermSp'>not permitted</span> from its childs (For "+ResponseArray[3]+" subReseller).<br/>";					
					dhtmlx.confirm({
						title:"Confirmation",
						type:"confirm-warning",
						text: ConfirmMsg+"!قابل برگشت نیست<br/>مطمئن هستید ؟",
						ok:"بلی",
						cancel:"خیر",
						callback: function(result){
							if(result){
								dhxLayout.cells("a").progressOn();
									dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=PastePermissions","&From_Reseller_Id="+CopyFrom_Reseller_Id+"&To_Reseller_Id="+PasteTo_Reseller_Id,function (loader){
										dhxLayout.cells("a").progressOff();
										var response=loader.xmlDoc.responseText;
										response=CleanError(response);
										if((response=='')||(response[0]=='~'))
											dhtmlx.alert("خطا، "+response.substring(1));
										else if(response=='OK~')
											dhtmlx.alert({text:"با موفقیت انجام شد",ok:"بستن"});
										else 
											alert(response);
									});
							}
						}
					});					
				}
			});
		}
	}
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
	SelectedRowId=mygrid.getSelectedRowId();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,function(){
		mygrid.forEachRow(function(id){mygrid.openItem(id)});
		DoAfterRefresh();
	});
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
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
		text: "برای حذف مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert({text:"خطا، "+response.substring(1),ok:"بستن"});
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت حذف شد");
					}
					else alert(response);

				});
			}	
		
		}
});	
}	

function PopupWindow(SelectedRowId){
	if(SelectedRowId>0){
		var Reseller=mygrid.cells(SelectedRowId,1).getValue();
		if(Reseller.toLowerCase()==LoginResellerName.toLowerCase()){
			dhtmlx.message({title: "هشدار",type: "alert-error",text: "شما نمی توانید اطلاعات خودتان را ببینید!"});
			return;
		}
	}
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"Resellers_and_Operators");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+SelectedRowId, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
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
