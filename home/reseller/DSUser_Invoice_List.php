<?php
require_once("../../lib/DSInitialReseller.php");
$User_Id=addslashes($_GET['ParentId']);
$HaveIPService=DBSelectAsString("select count(1) from Huser_serviceip where (User_Id='$User_Id')And((ServiceStatus='Active')or(ServiceStatus='Pending'))");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
	<script src="../codebase/dhtmlxform_dyn.js"></script>
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
	
	<?php $Arr=Array();$n=CopyTableToArray($Arr,"select concat(name,' ',family,if(Organization<>'',concat(' - ',Organization),'')) as CustomerName,Phone,Address,CompanyEconomyCode,CompanyRegistryCode,CompanyNationalCode from Huser where User_Id='".addslashes($_GET['ParentId'])."'"); ?>
	
	var CustomerName="<?php echo $Arr[0]["CustomerName"];?>";
	var CustomerPhone="<?php echo $Arr[0]["Phone"];?>";
	var CustomerAddress="<?php echo $Arr[0]["Address"];?>";
	var CustomerEconomyCode="<?php echo $Arr[0]["CompanyEconomyCode"];?>";
	var CustomerRegistryCode="<?php echo $Arr[0]["CompanyRegistryCode"];?>";
	var CustomerNationalCode="<?php echo $Arr[0]["CompanyNationalCode"];?>";
	
	DataTitle="Invoice";
	DataName="DSUser_Invoice_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	
	
	GColIds="User_Invoice_Id,Creator,InvoiceCDT,InvoiceStatus,TotalPrice,Comment";
	GColHeaders="{#stat_count} ردیف,ثبت کننده,زمان ثبت,InvoiceStatus,جمع کل,توضیح";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="60,80,110,100,90,300";
	GColAligns="center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,0,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';
	
	ISPermitAdd=ISPermit('Visp.User.Invoice.Add');
	ISPermitEdit=ISPermit('Visp.User.Invoice.Edit');
	ISPermitView=ISPermit('Visp.User.Invoice.View');
	ISPermitDelete=ISPermit('Visp.User.Invoice.Delete');
	ISPermitPrint=ISPermit('Visp.User.Invoice.Print');
	
	EditWindow={
				id:"popupWindow",
				x:100,y:20,width:300,height:200,
				center:true,
				modal:true,
				park :false
				};
				
	var EfficientRowCount=0;
				
	//=======Popup2 AddInvoice
	var Popup2;
	var PopupId2=['AddInvoice'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	
		Service_Id:'You can only choose item from list',
		Comment:'Any custom text up to 255 characters'		
	};
	var Form2FieldHelpId=['Service_Id'];
	var Form2Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		
		
		{type: "block",width:640,list:[
			{type:"input" , name:"CustomerName", label:"نام :", value:CustomerName,maxLength:64, validate:"", labelAlign:"left", inputWidth:100},
			{type:"newcolumn"},
			{type:"input" , name:"CustomerPhone", label:"تلفن :", value:CustomerPhone, maxLength:32, validate:"", labelAlign:"left", inputWidth:100},
			{type:"newcolumn"},
			{type:"input" , name:"CustomerPostalCode", label:"کد پستی :", maxLength:10, validate:"", labelAlign:"left", inputWidth:100},
			{type:"newcolumn"},
			{type:"input" , name:"CustomerEconomyCode", label:"شماره اقتصادی :", value:CustomerEconomyCode, maxLength:12, validate:"", labelAlign:"left", inputWidth:100},
			{type:"newcolumn"},
			{type:"input" , name:"CustomerRegistryCode", label:"شماره ثبت :", value:CustomerRegistryCode, maxLength:12, validate:"", labelAlign:"left", inputWidth:100},
			{type:"newcolumn"},
			{type:"input" , name:"CustomerNationalCode", label:"کد ملی :", value:CustomerNationalCode, maxLength:12, validate:"", labelAlign:"left", inputWidth:100},
			{type:"newcolumn"},
			{type:"input" , name:"CustomerAddress", label:"آدرس :", value:CustomerAddress, maxLength:255, validate:"", labelAlign:"left", inputWidth:488},
		]},
		{type:"input" , name:"RowCount", value:0,hidden:true},
		{type: "fieldset",name:"ItemsBlock",label:"آیتم های فاکتور",width:620,offsetLeft:0,list:[
			{type: "block",width:600,list:[
				{ type: "label" ,label:"شناسه آیتم",labelWidth:80},
				{type:"newcolumn"},
				{ type: "label" ,label:"نام آیتم",labelWidth:220},
				{type:"newcolumn"},
				{ type: "label" ,label:"قیمت",labelWidth:90},
				{type:"newcolumn"},
				{ type: "label" ,label:"پرداخت شده",labelWidth:90},
				{type:"newcolumn"},
				{ type: "button",name:"AddItem",value: "+",width :20},
			]},
		]},
		{ type: "label"},
		{ type:"input" , name:"Comment", label:"توضیح :", maxLength:255, rows: 3, validate:"", labelAlign:"left", inputWidth:402},
		{type: "block", width: 450, list:[
			{ type: "button",name:"Preview",value: "پیش نمایش",width :100},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Proceed",value: "ثبت",width :100},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
	function CreateItemStr(RowCount,Item_Type,Item_Id,ServicePrice,PayPrice,ItemName){
		Form2.setItemValue("RowCount",RowCount);
		var t={type: "block",name:"Row"+RowCount,width:600,list:[
				{type:"hidden" ,labelWidth:10, name:"Item_Type"+RowCount,value:Item_Type, inputWidth:80,readonly:true, style:"background-color:#E6EEF0"},
				{type:"hidden" ,labelWidth:10, name:"Item_Id"+RowCount,value:Item_Id, inputWidth:80,readonly:true, style:"background-color:#E6EEF0"},
				{type:"input" ,labelWidth:10, name:"Item"+RowCount,value:Item_Type+":"+Item_Id, inputWidth:80,readonly:true, style:"background-color:#E6EEF0"},
				{type:"newcolumn"},
				{type:"input" ,labelWidth:10, name:"ItemName"+RowCount,value:ItemName, validate:"", labelAlign:"left", inputWidth:220,readonly:true, style:"background-color:#E6EEF0"},
				{type:"newcolumn"},
				{type:"input" , name:"ServicePrice"+RowCount, value:formatMoney(ServicePrice,0), validate:"", labelAlign:"left", inputWidth:90,readonly:true, style:"background-color:#E6EEF0"},
				{type:"newcolumn"},
				{type:"input" , name:"PayPrice"+RowCount, value:formatMoney(PayPrice,0), validate:"", labelAlign:"left", inputWidth:90,readonly:true, style:"background-color:#E6EEF0"},
				{type:"newcolumn"},
				{ type: "button",name:"RemoveItem"+RowCount,value: "-",width :20},
			]};
		return t;
	}
	
	var Popup2_1;
	
	var Form2_1;
	function CreateForm2_1Str(){
		var Form2_1Str = [
			{type: "fieldset", label:"از لیست انتخاب کنید",width: 500, list:[
				{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
				{ type: "select", name:"InvoiceItem",connector: RenderFile+".php?"+un()+"&act=SelectItem&User_Id="+ParentId,required:true,inputWidth:400, info:true},
				{type: "block", width: 460, list:[
					{ type: "button",name:"OK",value: "افزودن",width :80},
					{type: "newcolumn", offset:20},
					{ type: "button",name:"Close",value: " بستن ",width :80}
				]}	
			]}	
		];
		return Form2_1Str;
	}
	
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

	
	if (ISPermitAdd)
		AddPopupAddInvoice();
	
	if(ISPermitPrint)	DSToolbarAddButton(ToolbarOfGrid,null,"Print","چاپ","Print",ToolbarOfGrid_OnPrintClick);
	
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
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnPrintClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		var NewWindow=window.open(RenderFile+".php?"+un()+"&act=PrintInvoice&Id="+SelectedRowId+"&User_Id="+ParentId,"_blank");
		if(!NewWindow || NewWindow.closed || typeof NewWindow.closed=='undefined') 
			dhtmlx.message({title: "PopupBlocked",type: "alert-error",text: "Your browser is set to block popup window!<br/>Trust this webpage and try again..."});
	}
}
function AddPopupAddInvoice(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddInvoice","افزودن","tog_Add");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Popup2_1OnShow(){
	// dhtmlx.message("onShow="+name);
	if(parseInt(Form2.getItemValue("RowCount"))>10){
		alert("Limitted to only 10 item");
		Popup2_1.hide();
		return;
	}
	Form2_1.unload();
	Form2_1 = Popup2_1.attachForm(CreateForm2_1Str());

	Form2_1.lock();
	Form2_1.attachEvent("onOptionsLoaded",function(){
		Form2_1.unlock();
		var opts=Form2_1.getOptions("InvoiceItem");
		if(opts.length==0){
			parent.dhtmlx.message({text:"هیچ موردی یافت نشد",type:"error"});
			Popup2.hide();
		}
		else{
			Form2_1.attachEvent("onButtonClick", function(name){
				if(name=="Close"){
					Popup2_1.hide();
				}
				else{
					Form2_1.updateValues();
					var ItemFields=Form2_1.getItemValue("InvoiceItem").split("~",5);
					var RowCount=parseInt(Form2.getItemValue("RowCount"))+1;
					for(i=1;i<RowCount;++i){
						if((Form2.getItemValue("Item_Type"+i)==ItemFields[0])&&(Form2.getItemValue("Item_Id"+i)==ItemFields[1])){
							alert("این آیتم قبلا اضافه شده است");
							return;
						}
					}
					Popup2_1.hide();
					EfficientRowCount++;
					Form2.addItem("ItemsBlock",CreateItemStr(RowCount,ItemFields[0],ItemFields[1],ItemFields[2],ItemFields[3],ItemFields[4]),RowCount,0);
				}
			});
		}
	});
}


function Form2OnButtonClick(name){//AddInvoice
	if(name=='Close') Popup2.hide();
	else if(name=='AddItem'){
		Popup2_1.show('AddItem');
	}
	else if(name.substr(0,10)=='RemoveItem'){
		var ItemIndex=name.substr(10);
		Form2.setItemValue("Item_Type"+ItemIndex,'none');
		Form2.removeItem("Item_Id"+ItemIndex);
		Form2.removeItem("Item"+ItemIndex);
		Form2.removeItem("ItemName"+ItemIndex);
		Form2.removeItem("ServicePrice"+ItemIndex);
		Form2.removeItem("PayPrice"+ItemIndex);
		Form2.removeItem("RemoveItem"+ItemIndex);
		Form2.hideItem("Row"+ItemIndex);
		EfficientRowCount--;
	}
	else if(name=="Preview"){
		if(EfficientRowCount<=0){
			alert("لطفا حداقل یک مورد را اضافه کنید");
			Popup2_1.show("AddItem");
			return;
		}
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			dhxLayout.cells("a").progressOn();
			Form2.send(RenderFile+".php?"+un()+"&act=GetInvoice&req=Preview&User_Id="+ParentId,"post",function(loader, response){
				dhxLayout.cells("a").progressOff();
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if((response=='')||(response[0]=='~')){
					dhtmlx.alert("خطا، "+response.substring(1));
				}
				else{
					Form2.enableItem('Proceed');
					var Invoice = window.open("", "PreviewInvoice", "location=0,status=0,title=0,toolbar=0,titlebar=0,scrollbars=1,menubar=0,width=840,height=680",false);
					if (Invoice){
						Invoice.document.open();
						Invoice.document.write(response);
						Invoice.focus();
						Invoice.onblur =  Invoice.close;
						Invoice.document.close();
					}
					else
						dhtmlx.message({title: "PopupBlocked",type: "alert-error",text: "Your browser is set to block popup window!<br/>Trust this webpage and try again..."});
				}
			});	
		}
	}
	else{
		if(EfficientRowCount<=0){
			alert("لطفا حداقل یک مورد را اضافه کنید");
			Popup2_1.show("AddItem");
			return;
		}
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.lock();
			Popup2.hide();
			dhxLayout.cells("a").progressOn();
			DSFormInsertRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=GetInvoice&req=Insert&User_Id="+ParentId,Form2DoAfterInsertOk,Form2DoAfterInsertFail);
		}
	}
}


function Form2DoAfterInsertOk(RId){
	SelectedRowId=RId;
	Form2.unlock();
	dhxLayout.cells("a").progressOff();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,function(){
		DoAfterRefresh();
	});
}

function Form2DoAfterInsertFail(){
	Form2.unlock();
	dhxLayout.cells("a").progressOff();
}

function Popup2OnShow(){//AddInvoice
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Popup2_1=new dhtmlXPopup({form:Form2,id:["AddItem"],mode:"left"});
	Popup2_1.attachEvent("onShow",Popup2_1OnShow);
	Form2_1 = Popup2_1.attachForm({});
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


}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function DoAfterRefresh(){
	if((SelectedRowId==null)||(SelectedRowId==0))
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);

	dhxLayout.cells("a").progressOff();
	
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
