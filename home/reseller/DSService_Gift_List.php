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
	DataTitle="Gift";
	DataName="DSService_Gift_";
	ExtraFilter="&Service_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	GColIds="Service_Gift_Id,GiftName,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftExtraTi,MikrotikRateName";
	GColHeaders="{#stat_count} ردیف,نام,مدت زمان هدیه,مدت زمان انقضا,ضریب ترافیک,ضریب زمان,ترافیک هدیه,زمان اضافی هدیه,سرعت هدیه";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="80,200,100,100,100,100,120,120,200";
	GColAligns="center,left,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	
	//=======Popup2 AddGift
	var Popup2;
	var PopupId2=['AddGift'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 AddGift
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {ParamStatus:'Yes: Check next element and replace No: Not check next element(force parameter this value) Ignore: Ignore this parameter '};
	var Form2FieldHelpId=['ParamStatus'];
	var Form2Str = [
		{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"Gift_Id",label: "نام هدیه :",connector: RenderFile+".php?"+un()+"&act=SelectGift",required:true,validate:"IsID",inputWidth:400,info:true},
		{type:"block",width:550,disabled:true,list:[
			{ type:"settings", offsetLeft:10  },
			{ type: "input" , name:"GiftDurationDays", label:"<span style='color:#666666'>مدت زمان هدیه :</span>", value: "", labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:120},
			{type:"newcolumn",offset:30},
			{ type: "input" , name:"GiftExpirationDays", label:"<span style='color:#666666'>مدت زمان انقضا :</span>", value: "", labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:90,tooltip:"GiftExpirationDays"},
		]},
		{type:"block",width:550,disabled:true,list:[
			{ type:"settings" ,offsetLeft:10  },
			{ type: "input" , name:"GiftTrafficRate", label:"<span style='color:#666666'>ضریب ترافیک :</span>", value: "", labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:90},
			{ type: "input" , name:"GiftTimeRate", label:"<span style='color:#666666'>ضریب زمان :</span>", value: "",labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:90},
			{type:"newcolumn",offset:10},
			{ type: "input" , name:"GiftExtraTr", label:"<span style='color:#666666'>ترافیک هدیه :</span>", value: "",labelAlign:"left",inputWidth:120,style:"color:#666666", labelWidth:70},
			{ type: "input" , name:"GiftExtraTi", label:"<span style='color:#666666'>زمان هدیه :</span>", value: "",labelAlign:"left",inputWidth:120,style:"color:#666666", labelWidth:70},
			
			{type:"newcolumn",offset:10},
			{ type: "input" , name:"GiftStopOnTrFinish", label:"<span style='color:#666666'>خاتمه پس از اتمام ترافیک :</span>", value: "",labelAlign:"left",inputWidth:35,style:"color:#666666", labelWidth:90},
			// { type: "input" , name:"GiftExtraTi", label:"<span style='color:#666666'>GiftExtraTi :</span>", value: "",labelAlign:"left",inputWidth:150,style:"color:#666666", labelWidth:90},
			
			
			
			{type:"newcolumn"},
			{ type: "input" , name:"MikrotikRateName", label:"<span style='color:#666666'>سرعت هدیه :</span>", value: "",labelAlign:"left",inputWidth:334,style:"color:#666666", labelWidth:130},
		]},
		{ type: "input" , name:"GiftCount", label:"تعداد :", validate:"NotEmpty,ValidInteger", value: "1",labelAlign:"left", maxLength:3,inputWidth:50,required:true},
		{type: "block", width: 400, list:[
			{ type: "button",name:"Proceed",value: "افزودن",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
	//=======Popup3 EditGift
	var Popup3;
	var PopupId3=['EditGift'];//  popup Attach to Which Buttom of Toolbar

	//=======Form3 EditGift
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {ParamStatus:'Yes: Check next element and replace No: Not check next element(force parameter this value) Ignore: Ignore this parameter '};
	var Form3FieldHelpId=['ParamStatus'];
	var Form3Str = [
		{ type:"hidden" , name:"Service_Gift_Id", label:"Service_Gift_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"Gift_Id",label: "نام هدیه :",connector: RenderFile+".php?"+un()+"&act=SelectGift",required:true,validate:"IsID",inputWidth:400,info:true},
		{type:"block",width:550,disabled:true,list:[
			{ type:"settings" ,offsetLeft:10  },
			{ type: "input" , name:"GiftDurationDays", label:"<span style='color:#666666'>مدت زمان هدیه :</span>", value: "", labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:120},
			{type:"newcolumn",offset:30},
			{ type: "input" , name:"GiftExpirationDays", label:"<span style='color:#666666'>مدت زمان انقضا :</span>", value: "", labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:90,tooltip:"GiftExpirationDays"},
		]},
		{type:"block",width:550,disabled:true,list:[
			{ type:"settings",offsetLeft:10  },
			{ type: "input" , name:"GiftTrafficRate", label:"<span style='color:#666666'>ضریب ترافیک :</span>", value: "", labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:90},
			{ type: "input" , name:"GiftTimeRate", label:"<span style='color:#666666'>ضریب زمان :</span>", value: "",labelAlign:"left",inputWidth:50,style:"color:#666666", labelWidth:90},
			{type:"newcolumn",offset:10},
			{ type: "input" , name:"GiftExtraTr", label:"<span style='color:#666666'>ترافیک هدیه :</span>", value: "",labelAlign:"left",inputWidth:120,style:"color:#666666", labelWidth:70},
			{ type: "input" , name:"GiftExtraTi", label:"<span style='color:#666666'>زمان هدیه :</span>", value: "",labelAlign:"left",inputWidth:120,style:"color:#666666", labelWidth:70},
			{type:"newcolumn",offset:10},
			{ type: "input" , name:"GiftStopOnTrFinish", label:"<span style='color:#666666'>خاتمه پس از اتمام ترافیک :</span>", value: "",labelAlign:"left",inputWidth:35,style:"color:#666666", labelWidth:90},
			{type:"newcolumn"},
			{ type: "input" , name:"MikrotikRateName", label:"<span style='color:#666666'>سرعت هدیه :</span>", value: "",labelAlign:"left",inputWidth:334,style:"color:#666666", labelWidth:130},
		]},
		{type: "block", width: 400, list:[
			{ type: "button",name:"Proceed",value: "ویرایش",width :80},
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
	if(ISPermit("CRM.Service.Gift.Add"))  AddPopupAddGift();
	if(ISPermit("CRM.Service.Gift.Edit")) AddPopupEditGift();
	if(ISPermit("CRM.Service.Gift.Delete")) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","Delete",ToolbarOfGrid_OnDeleteClick);
	

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
			if(result)
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("باموفقیت حذف شد");
					}
					else alert(response);

				});
		
		}
});	
}

function Form2onChange(id, value){
	if(id=='Gift_Id'){
		if(value==0){
			Form2.setItemValue("GiftDurationDays",'');
			Form2.setItemValue("GiftExpirationDays",'');
			Form2.setItemValue("GiftTrafficRate",'');
			Form2.setItemValue("GiftTimeRate",'');
			Form2.setItemValue("GiftExtraTi",'');
			Form2.setItemValue("GiftExtraTr",'');
			Form2.setItemValue("MikrotikRateName",'');
		}
		else{
			dhxLayout.cells("a").progressOn();
			Form2.lock();
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetGiftInfo&Service_Id="+ParentId+"&Gift_Id="+value,SetParamInfo1);
		}
	}
}

function SetParamInfo1(loader){
	dhxLayout.cells("a").progressOff();
	Form2.unlock();
	response=loader.xmlDoc.responseText;
	response=CleanError(response);
	if((response=='')||(response[0]=='~')){
		dhtmlx.alert("خطا، "+response.substring(1));
		Form2.lock();
	}
	else{
		//echo "$GiftDurationDays`$GiftExpirationDays`$GiftTrafficRate`$GiftTimeRate`$GiftExtraTr`GiftStopOnTrFinish`$GiftExtraTi`$MikrotikRateName";
		var parray=response.split("`",8);
		var i=0;
		Form2.setItemValue("GiftDurationDays",parray[i++]);
		Form2.setItemValue("GiftExpirationDays",parray[i++]);
		Form2.setItemValue("GiftTrafficRate",parray[i++]);
		Form2.setItemValue("GiftTimeRate",parray[i++]);
		Form2.setItemValue("GiftExtraTr",parray[i++]);
		Form2.setItemValue("GiftStopOnTrFinish",parray[i++]);
		Form2.setItemValue("GiftExtraTi",parray[i++]);
		Form2.setItemValue("MikrotikRateName",parray[i++]);
	}
}

function SetParamInfo2(loader){
	dhxLayout.cells("a").progressOff();
	Form3.unlock();
	response=loader.xmlDoc.responseText;
	response=CleanError(response);
	if((response=='')||(response[0]=='~')){
		dhtmlx.alert("خطا، "+response.substring(1));
		Form3.lock();
	}
	else{
		//echo "$GiftDurationDays`$GiftExpirationDays`$GiftTrafficRate`$GiftTimeRate`$GiftExtraTr`$GiftStopOnTrFinish`$GiftExtraTi`$MikrotikRateName";
		var parray=response.split("`",8);
		var i=0;
		Form3.setItemValue("GiftDurationDays",parray[i++]);
		Form3.setItemValue("GiftExpirationDays",parray[i++]);
		Form3.setItemValue("GiftTrafficRate",parray[i++]);
		Form3.setItemValue("GiftTimeRate",parray[i++]);
		Form3.setItemValue("GiftExtraTr",parray[i++]);
		Form3.setItemValue("GiftStopOnTrFinish",parray[i++]);
		Form3.setItemValue("GiftExtraTi",parray[i++]);
		Form3.setItemValue("MikrotikRateName",parray[i++]);
	}
}


function Form3onChange(id, value){
	if(id=='Gift_Id'){
		if(value==0){
			Form2.setItemValue("GiftDurationDays",'');
			Form2.setItemValue("GiftExpirationDays",'');
			Form2.setItemValue("GiftTrafficRate",'');
			Form2.setItemValue("GiftTimeRate",'');
			Form2.setItemValue("GiftExtraTi",'');
			Form2.setItemValue("GiftExtraTr",'');
			Form2.setItemValue("MikrotikRateName",'');
		}
		else{
			dhxLayout.cells("a").progressOn();
			Form2.lock();
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetGiftInfo&Service_Id="+ParentId+"&Gift_Id="+value,SetParamInfo2);
		}
	}
}

function AddPopupAddGift(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddGift","افزودن هدیه","tow_AddGift");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
}

function AddPopupEditGift(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"EditGift","ویرایش","tow_EditGift");
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
	Form3.attachEvent("onChange",Form3onChange);
}

function Form2OnButtonClick(name){// Add Param
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			DSFormInsertRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=insert"+ExtraFilter,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}

function Form3OnButtonClick(name){// Edit Param
	if(name=='Close') Popup3.hide();
	else{
		if(DSFormValidate(Form3,Form3FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form3,RenderFile+".php?"+un()+"&act=update"+ExtraFilter,Form3DoAfterUpdateOk,Form3DoAfterUpdateFail);
		}
	}
}



function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form3DoAfterUpdateOk(){
	Popup3.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterUpdateFail(){
	Popup2.hide();
}

function Form3DoAfterUpdateFail(){
	Popup2.hide();
}

function Popup2OnShow(){//Add Gift
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
}

function Popup3OnShow(){//Edit Gift
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		Form3.lock();
		dhxLayout.cells("a").progressOn();
		Form3.load(RenderFile+".php?"+un()+"&act=LoadGiftForm"+ExtraFilter+"&Service_Gift_Id="+SelectedRowId,function(id,respond){
			Form3.unlock();
			dhxLayout.cells("a").progressOff();
		});
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
