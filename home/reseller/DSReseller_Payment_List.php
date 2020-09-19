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
	DataTitle="Reseller_Payment";
	DataName="DSReseller_Payment_";
	ExtraFilter="&Reseller_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	GColIds="Reseller_Payment_Id,Reseller_PaymentCDT,PaymentType,Price,PayBalance,VoucherNo,VoucherDate,BankBranchName,BankBranchNo,Comment";
	GColHeaders="{#stat_count} ردیف,زمان ثبت,نوع پرداخت,مبلغ,تراز مالی,سریال/پیگیری,تاریخ,نام بانک,کد شعبه,توضیح";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,110,80,80,80,100,80,100,105,300";
	GColAligns="center,center,center,center,center,center,center,center,center,left";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1];

	ISSort=false;
	GColSorting="server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

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

	//=======Popup2 AddPayment
	var Popup2;
	var PopupId2=['AddPayment'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 AddPayment
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	VoucherNo:'Voucher No(Char max length 15)',
							VoucherDate:'Date of Pay, Leave blank or enter format yyyy/mm/dd '};
	var Form2FieldHelpId=['Credit','Price'];
	var Form2Str = [
		{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"PaymentType", label: "روش پرداخت :", options:[{text: "وجه نقد", value: "Cash",selected: true},{text: "چک", value: "Cheque"},{text: "کارت خوان", value: "Pos"},{text: "بیعانه", value: "Deposit"},{text: "سایر", value: "Other"},{text: "آنلاین", value: "Online"}],inputWidth:100,required:true},
		{ type: "input" , name:"Price", label:"مبلغ (ریال) :", validate:"IsValidPrice",value:"0", labelAlign:"left", maxLength:14,inputWidth:100,info:"true",numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{ type:"input" , name:"VoucherNo", label:"سریال/پیگیری :",maxLength:15, validate:"", labelAlign:"left", info:"true",inputWidth:200},
		{ type:"input" , name:"VoucherDate", label:"تاریخ :",maxLength:10, validate:"IsValidDateOrBlank", labelAlign:"left",info:"true",inputWidth:200},
		{ type:"input" , name:"BankBranchName", label:"نام بانک :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200},
		{ type:"input" , name:"BankBranchNo", label:"کد شعبه :",maxLength:32, validate:"", labelAlign:"left", inputWidth:200},
		{ type: "input" , name:"Comment", label:"توضیح :", labelAlign:"left", maxLength:255,inputWidth:300},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "انجام",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];

	
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
	AddPopupAddPayment();
	
	
	if(PermitAdd) if(ISPermit(DataTitle+".Add")) DSToolbarAddButton(ToolbarOfGrid,null,"Add","افزودن","tog_"+DataTitle+"_Add",ToolbarOfGrid_OnAddClick);
	if(PermitEdit) if(ISPermit(DataTitle+".Edit")) DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_"+DataTitle+"_Edit",ToolbarOfGrid_OnEditClick);
	if(PermitDelete) if(ISPermit(DataTitle+".Delete")) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_"+DataTitle+"_Delete",ToolbarOfGrid_OnDeleteClick);
	

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
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================

function AddPopupAddPayment(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddPayment","افزودن پرداخت","tow_AddPayment");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
/* 	Form2.attachEvent("onInputChange",function(name,value){
		if((name=="VoucherDate")&&(typeof(Storage) !== "undefined"))
			localStorage.setItem('UserVoucherDate', value);
	}); */
}

function Form2OnButtonClick(name){//ChangeStatus
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Popup2.hide();
			dhxLayout.cells("a").progressOn();			
			DSFormUpdateRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=AddPayment&id="+ParentId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}
function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	dhxLayout.cells("a").progressOff();
	
	
}

function Form2DoAfterUpdateFail(){
	Popup2.hide();
	dhxLayout.cells("a").progressOff();
}


function Popup2OnShow(){//ChangeStatus
	Form2.clear();
	Form2.setItemValue("Price", 0);
	Form2.setItemValue("Credit", 0);
	Form2.setItemFocus("Credit");
	Form2.setItemValue("VoucherDate",(typeof(Storage) !== "undefined")?localStorage.getItem('LS_CurrentDate'):'');
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
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
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
	dhxLayout.cells("a").progressOff();			
}

function DoAfterRefresh(){
	mygrid.selectRowById(SelectedRowId,false,true,true);
	dhxLayout.cells("a").progressOff();
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>