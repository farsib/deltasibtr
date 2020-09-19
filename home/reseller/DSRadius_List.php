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
	DataTitle="Radius";
	DataName="DSRadius_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	GColIds="Radius_Id,ISEnable,RadiusName,AuthPort,AcctPort";
	GColHeaders="{#stat_count} ردیف,فعال,نام پورت,پورت تصدیق,پورت حسابداری";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,100,150,80,100";
	GColAligns="center,center,left,Center,center";
	GColTypes="ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server";
	ColSortIndex=1;
	SortDirection='asc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	
	//=======Popup2 Apply
	var Popup2;
	var PopupId2=['Apply'];//  popup Attach to Which Buttom of Toolbar
	//=======Form2 Apply
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	VoucherNo:'Voucher No(Char max length 15)'};
	var Form2FieldHelpId=['VoucherNo'];
	var Form2Str = [
		{ type:"settings" , labelWidth:50, inputWidth:200,offsetLeft:5  },
		{ type:"input" ,name:"Status", label:"وضعیت :",disabled:"true"},
		{ type: "button",name:"Close",value: " بستن ",width :80}
	];
	//=======Popup3 Status
	var Popup3;
	var PopupId3=['Status'];//  popup Attach to Which Buttom of Toolbar
	//=======Form3 Status
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {	VoucherNo:'Voucher No(Char max length 15)'};
	var Form3FieldHelpId=['VoucherNo'];
	var Form3Str = [
		{ type:"settings" , labelWidth:50, inputWidth:200,offsetLeft:5  },
		{ type:"input" ,name:"Status", label:"وضعیت :",disabled:"true"},
		{ type: "button",name:"Close",value: " بستن ",width :80}
	];
	//=======Popup4 Start
	var Popup4;
	var PopupId4=['Start'];//  popup Attach to Which Buttom of Toolbar
	//=======Form4 Status
	var Form4;
	var Form4PopupHelp;
	var Form4FieldHelp  = {	VoucherNo:'Voucher No(Char max length 15)'};
	var Form4FieldHelpId=['VoucherNo'];
	var Form4Str = [
		{ type:"settings" , labelWidth:50, inputWidth:200,offsetLeft:5  },
		{ type:"input" ,name:"Status", label:"وضعیت :",disabled:"true"},
		{ type: "button",name:"Close",value: " بستن ",width :80}
	];
	//=======Popup5 Stop
	var Popup5;
	var PopupId5=['Stop'];//  popup Attach to Which Buttom of Toolbar
	//=======Form5 Status
	var Form5;
	var Form5PopupHelp;
	var Form5FieldHelp  = {	VoucherNo:'Voucher No(Char max length 15)'};
	var Form5FieldHelpId=['VoucherNo'];
	var Form5Str = [
		{ type:"settings" , labelWidth:50, inputWidth:200,offsetLeft:5  },
		{ type:"input" ,name:"Status", label:"وضعیت :",disabled:"true"},
		{ type: "button",name:"Close",value: " بستن ",width :80}
	];

	
	var DataPermit="Radius";
	var PermitAdd=ISPermit("Admin."+DataPermit+".Add");
	var PermitView=ISPermit("Admin."+DataPermit+".View");
	var PermitEdit=ISPermit("Admin."+DataPermit+".Edit");
	var PermitDelete=ISPermit("Admin."+DataPermit+".Delete");
	
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
	
	//DSToolbarAddButton(ToolbarOfGrid,null,"View","View","tog_"+DataTitle+"_Edit",ToolbarOfGrid_OnEditClick);
	
	if(PermitAdd&&PermitView) DSToolbarAddButton(ToolbarOfGrid,null,"Add","افزودن","tog_Add",ToolbarOfGrid_OnAddClick);
	if(PermitEdit) DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	else if(PermitView) DSToolbarAddButton(ToolbarOfGrid,null,"View","نمایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	if(PermitDelete) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	
	ToolbarOfGrid.addSeparator("sep3", null);	
	if(ISPermit("Admin.Radius.GenralConfig.View")) DSToolbarAddButton(ToolbarOfGrid,null,"GeneralConfig","پیکربندی عمومی","tog_GeneralConfig",ToolbarOfGrid_OnGeneralConfigClick);
	if(ISPermit("Admin.Radius.Apply")) AddPopupApply();
	if(ISPermit("Admin.Radius.Status")) AddPopupStatus();
	if(ISPermit("Admin.Radius.Start")) AddPopupStart();
	if(ISPermit("Admin.Radius.Stop")) AddPopupStop();
	
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
	if(PermitEdit||PermitView) mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo); 
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function AddPopupApply(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Apply","اعمال","tow_Apply");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}
function Form2OnButtonClick(name){Popup2.hide();}
function Form2DoAfterUpdateOk(){Popup2.hide();}
function Form2DoAfterUpdateFail(){Popup2.hide();}
function Popup2OnShow(){Form2.setItemValue("Status","در حال انتظار....");Form2.load(RenderFile+".php?"+un()+"&act=Apply",function(id,respond){});}
//------------------
function AddPopupStatus(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Status","وضعیت","tow_Status");
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}
function Form3OnButtonClick(name){Popup3.hide();}
function Form3DoAfterUpdateOk(){Popup3.hide();}
function Form3DoAfterUpdateFail(){Popup3.hide();}
function Popup3OnShow(){Form3.setItemValue("Status","در حال انتظار....");Form3.load(RenderFile+".php?"+un()+"&act=Status",function(id,respond){});}
//------------------
function AddPopupStart(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Start","شروع","tow_Start");
	Popup4=DSInitialPopup(ToolbarOfGrid,PopupId4,Popup4OnShow);
	Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,Form4OnButtonClick);
}
function Form4OnButtonClick(name){Popup4.hide();}
function Form4DoAfterUpdateOk(){Popup4.hide();}
function Form4DoAfterUpdateFail(){Popup4.hide();}
function Popup4OnShow(){Form4.setItemValue("Status","در حال انتظار....");Form4.load(RenderFile+".php?"+un()+"&act=Start",function(id,respond){});}
//------------------
function AddPopupStop(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Stop","پایان","tow_Stop");
	Popup5=DSInitialPopup(ToolbarOfGrid,PopupId5,Popup5OnShow);
	Form5=DSInitialForm(Popup5,Form5Str,Form5PopupHelp,Form5FieldHelpId,Form5FieldHelp,Form5OnButtonClick);
}
function Form5OnButtonClick(name){Popup5.hide();}
function Form5DoAfterUpdateOk(){Popup5.hide();}
function Form5DoAfterUpdateFail(){Popup5.hide();}
function Popup5OnShow(){Form5.setItemValue("Status","در حال انتظار....");Form5.load(RenderFile+".php?"+un()+"&act=Stop",function(id,respond){});}
//------------------



function ToolbarOfGrid_OnApplyClick(){
	var loader = dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=Apply");
	response=loader.xmlDoc.responseText;
	response=CleanError(response);

	if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
	else if(response=='OK~') dhtmlx.message("با موفقیت اعمال شد");
	else alert(response);
}

function ToolbarOfGrid_OnStartClick(){
	var loader = dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=Start");
	response=loader.xmlDoc.responseText;
	response=CleanError(response);

	if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
	else if(response=='OK~') dhtmlx.message("درخواست ارسال شد");
	else alert(response);
}

function ToolbarOfGrid_OnStopClick(){
	var loader = dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=Stop");
	response=loader.xmlDoc.responseText;
	response=CleanError(response);

	if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
	else if(response=='OK~') dhtmlx.message("درخواست ارسال شد");
	else alert(response);
}

function ToolbarOfGrid_OnGeneralConfigClick(){
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
function ToolbarOfGrid_OnDeleteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
		text: "برای حذف مطمئن هستید؟",
		ok: "بلی",
		cancel: "خیر",
		callback: function(result) {
			if(result)
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت حذف شد");
					}
					else alert(response);

				});
		
		}
});	
}	

function PopupWindow(SelectedRowId){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"Radius");
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