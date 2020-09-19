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
	DataTitle="User_Payment";
	DataName="DSUser_Payment_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	
	GColIds="User_Payment_Id,Creator,User_PaymentCDT,PaymentType,Price,PayBalance,VoucherNo,VoucherDate,BankBranchName,BankBranchNo,Charger,ChargerCommission,Supporter,SupporterCommission,Reseller,ResellerCommission,Comment";
	GColHeaders="{#stat_count} ردیف,ثبت کننده,زمان ثبت,روش پرداخت,مبلغ با مالیات,تراز مالی,سریال/پیگیری,تاریخ رسید,نام بانک,کد شعبه,شارژر,پورسانت شارژر,پشتیبان,پورسانت پشتیبان,نماینده فروش,پورسانت نماینده,توضیح";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,80,110,90,80,80,85,85,100,100,80,120,80,120,80,120,150";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,left";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=false;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	
	
	var PermitGetMoney=ISPermit("Visp.User.Payment.Add.GetMoney");
	var PermitRefundMoney=ISPermit("Visp.User.Payment.Add.RefundMoney");
	var PermitEdit=false;
	var PermitDelete=false;

	//=======Popup2 AddPayment
	var Popup2;
	var PopupId2=['AddPayment'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 AddPayment
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	VoucherNo:'حداکثر 15 کاراکتر',
							VoucherDate:'yyyy/mm/dd تاریخ پرداخت, خالی بگذارید یا با این فرمت بنویسید',
							Price:'Price you get from or refund to user'};
	var Form2FieldHelpId=['VoucherNo','VoucherDate','Price'];
	var DirectionOptions=[];
	if(PermitGetMoney) DirectionOptions.push({text: "دریافت وجه", value: "GetMoney"});
	if(PermitRefundMoney) DirectionOptions.push({text: "برگشت وجه", value: "RefundMoney"});
	var PaymentTypeOptions=[];
	if(ISPermit("Visp.User.Payment.PaymentType.Cash"))
		PaymentTypeOptions.push({text: "نقد", value: "Cash"});
	if(ISPermit("Visp.User.Payment.PaymentType.Cheque"))
		PaymentTypeOptions.push({text: "چک", value: "Cheque"});
	if(ISPermit("Visp.User.Payment.PaymentType.Pos"))
		PaymentTypeOptions.push({text: "کارت خوان", value: "Pos"});
	if(ISPermit("Visp.User.Payment.PaymentType.Deposit"))
		PaymentTypeOptions.push({text: "بیعانه", value: "Deposit"});
	if(ISPermit("Visp.User.Payment.PaymentType.TAX"))
		PaymentTypeOptions.push({text: "مالیات", value: "TAX"});
	if(ISPermit("Visp.User.Payment.PaymentType.Off"))
		PaymentTypeOptions.push({text: "تخفیف", value: "Off"});
	if(ISPermit("Visp.User.Payment.PaymentType.Other"))
		PaymentTypeOptions.push({text: "سایر", value: "Other"});
	
	
	var Form2Str = [
		{ type:"settings" , labelWidth:110, inputWidth:90,offsetLeft:10  },
		{type:"hidden", name:"IsPriceChanged", value:0},
		{ type: "select", name:"Direction", label: "جهت :", options: DirectionOptions, inputWidth:128,required:true},
		{ type: "select", name:"PaymentType", label: "روش پرداخت :", options:PaymentTypeOptions,inputWidth:128,required:true},
		{ type: "input" , name:"Price", label:"(ریال)مبلغ", validate:"IsValidPrice",value:"0", labelAlign:"left", maxLength:14,inputWidth:130,info:"false",numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{ type:"input" , name:"VoucherNo", label:"سریال/پیگیری :",maxLength:15, validate:"", labelAlign:"left", info:"true",inputWidth:220},
		{ type:"input" , name:"VoucherDate", label:"تاریخ :",maxLength:10, validate:"IsValidDateOrBlank", labelAlign:"left",info:"true",inputWidth:220},
		{ type:"input" , name:"BankBranchName", label:"نام بانک :",maxLength:32, validate:"", labelAlign:"left", inputWidth:220},
		{ type:"input" , name:"BankBranchNo", label:"کد شعبه :",maxLength:32, validate:"", labelAlign:"left", inputWidth:220},
		{ type: "input" , name:"Comment", label:"توضیح :", labelAlign:"left", maxLength:255,inputWidth:220,rows:2},
		{ type:"label"},
		{type: "block", width: 370, list:[
			{ type: "button",name:"Proceed",value: "انجام",width :65},
			{type: "newcolumn", offset:10},
			{ type: "button",name:"Close",value: " بستن ",width :50},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Refresh",value: "نوسازی",width :50, disabled:true},
			{type: "newcolumn", offset:1},
			{ type: "button",name:"BuyingBasket",value: " سبد خرید ",width :80}
		]}	
		];

	var PopupBuyingBasket;
	
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
	if(PermitGetMoney || PermitRefundMoney)AddPopupAddPayment();
	
	
	//if(PermitGetMoney || PermitRefundMoney)  DSToolbarAddButton(ToolbarOfGrid,null,"Add","Add","tog_"+DataTitle+"_Add",ToolbarOfGrid_OnAddClick);
	//if(PermitEdit) if(ISPermit(DataTitle+".Edit")) DSToolbarAddButton(ToolbarOfGrid,null,"Edit","Edit","tog_"+DataTitle+"_Edit",ToolbarOfGrid_OnEditClick);
	//if(PermitDelete) if(ISPermit(DataTitle+".Delete")) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","Delete","tog_"+DataTitle+"_Delete",ToolbarOfGrid_OnDeleteClick);
	

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
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddPayment","افزودن","tow_ChangeStatus");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Popup2.attachEvent("onHide",function(){if(PopupBuyingBasket.isVisible())PopupBuyingBasket.hide()});
	// Popup2.attachEvent("onContentClick",function(){if(PopupBuyingBasket.isVisible())PopupBuyingBasket.hide()});
	
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onInputChange",Form2OnInputChange);
	PopupBuyingBasket= new dhtmlXPopup({form: Form2,id:["BuyingBasket"],mode:"bottom"});
	PopupBuyingBasket.attachEvent("onShow",PopupBuyingBasketOnShow);
	PopupBuyingBasket.attachEvent("onContentClick",function(){PopupBuyingBasket.hide();});	
	PopupBuyingBasket.attachEvent("onHide",function(){Form2.disableItem("Refresh");});	
	
}

function PopupBuyingBasketOnShow(){
	dhxLayout.cells("a").progressOn();
	Form2.lock();
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetUserPayBalance&User_Id="+ParentId,function (loader){
		dhxLayout.cells("a").progressOff();
		Form2.unlock();
		Form2.enableItem("Refresh");
		response=loader.xmlDoc.responseText;
		response=CleanError(response);

		if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
		else{
			ResArray=response.split("~");
		if(ResArray[0]=='OK'){
				parent.MyService["Balance"]=parseFloat(ResArray[1]);
				if(parseInt(mygrid.cells2(0,mygrid.getColIndexById("PayBalance")).getValue().replace(/,/g, ""))!=parent.MyService["Balance"])
					LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
				if(parent.IsBuyingBasketEmpty(true)){
					PopupBuyingBasket.hide();
					parent.dhtmlx.message({text:"سبد خرید خالی است",expire:4000,type:"error"});
				}
				else{
					PopupBuyingBasket.attachHTML(parent.GetBuyingBasket());
					if(Form2.getItemValue("IsPriceChanged")==0)
						SetBuyingBasketPrice();
				}
			}
			else alert(response);
		}
	});	
}

function Form2OnInputChange(name,value){
	if(name=="Price"){
		Form2.setItemValue("IsPriceChanged",1);
		// Form2.getInput("Price").style.color="black";
		Form2.getInput("Price").style.fontWeight="normal";
	}
/* 	else if(name=="VoucherDate")
		if(typeof(Storage) !== "undefined")
			localStorage.setItem('UserVoucherDate', value); */
}

function SetBuyingBasketPrice(){
	var p=0;
		p+=parent.MyService["Base"][0]?parseFloat(parent.MyService["Base"][1]):0;
		p+=parent.MyService["Extra"][0]?parseFloat(parent.MyService["Extra"][1]):0;
		p+=parent.MyService["Other"][0]?parseFloat(parent.MyService["Other"][1]):0;
		p+=parent.MyService["IP"][0]?parseFloat(parent.MyService["IP"][1]):0;
		p+=-parseFloat(parent.MyService["Balance"]);
	Form2.getInput("Price").style.fontWeight="bold";
	if(p>=0){
		if(PermitGetMoney){
			Form2.setItemValue("Price",p);
			Form2.setItemValue("Direction","GetMoney");
			Form2.getSelect("Direction").style.backgroundColor="lightgreen";
			Form2.getInput("Price").style.backgroundColor="lightgreen";
		}
		else
			parent.dhtmlx.message("مجاز به دریافت وجه نیست!");
	}
	else{
		if(PermitRefundMoney){
			Form2.setItemValue("Price",-p);
			Form2.setItemValue("Direction","RefundMoney");
			Form2.getSelect("Direction").style.backgroundColor="tomato";
			Form2.getInput("Price").style.backgroundColor="tomato";
		}
		else
			parent.dhtmlx.message("مجاز به برگشت وجه نیست!");
	}
	setTimeout(function(){
		Form2.getSelect("Direction").style.backgroundColor="white";
		Form2.getInput("Price").style.backgroundColor="white";
	},2000);
	
}

function Form2OnButtonClick(name){//AddPayment
	if(name=='Close') Popup2.hide();
	else if(name=='Proceed'){
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.updateValues();
			Form2.setItemValue("Comment",Form2.getItemValue("Comment").replace(/\s+/ig," "));
			Form2.disableItem("Proceed");
			Popup2.hide();
			dhxLayout.cells("a").progressOn();
			DSFormUpdateRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=AddPayment&User_Id="+ParentId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
	else if(name=='Refresh'){
		if(PopupBuyingBasket.isVisible()){
			PopupBuyingBasket.hide();
			setTimeout(function(){PopupBuyingBasket.show("BuyingBasket")},200);
		}
	}
	else if(name=='BuyingBasket'){
		if(PopupBuyingBasket.isVisible())
			PopupBuyingBasket.hide("BuyingBasket");
		else
			PopupBuyingBasket.show("BuyingBasket");	
	}
}
function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetUserPayBalance&User_Id="+ParentId,function (loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا در دریافت تراز مالی,<br/>"+response.substring(1));
		else{
			ResArray=response.split("~");
			if(ResArray[0]=='OK')
				parent.MyService["Balance"]=parseFloat(ResArray[1]);
			else alert(response);
		}
	});		
	
	
}

function Form2DoAfterUpdateFail(){
	dhxLayout.cells("a").progressOff();
	Popup2.hide();
}


function Popup2OnShow(){//AddPayment
	Form2.clear();
	Form2.setItemValue("Price",0);
	Form2.setItemValue("VoucherDate",(typeof(Storage) !== "undefined")?localStorage.getItem('LS_CurrentDate'):'');
	Form2.setItemFocus("Price");
	Form2.enableItem("Proceed");
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
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب،روی ردیف مورد نظر کلیک کنید!"})
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
