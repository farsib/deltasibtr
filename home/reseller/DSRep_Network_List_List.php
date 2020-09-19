<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
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
	var DataTitle="گزارش آی پی های نت لاگ";
	var DataName="DSRep_NetLog_List_";
	var ExtraFilter="";
	var RenderFile=DataName+"ListRender";

	var GColIds=    "NLog_Id,FirstDT,LastDT,Username,SrcAddr,SrcPort,DstAddr,DstPort,Proto";
	var GColHeaders="{#stat_count} ردیف,FirstDT,LastDT,Username,SrcAddr,SrcPort,DstAddr,DstPort,Proto";

	var ISFilter=true;
	var FilterState=false;
	var GColFilterTypes=[1,1,1,1,1,1,1,1,1];
	
	var GFooter="";
	var GColInitWidths="80,120,120,100,120,100,120,100,100";
	var GColAligns="center,center,center,center,center,center";
	var GColTypes="ro,ro,ro,ro,ed,ed,ed,ed,ed";
	var GColVisibilitys=[1,1,1,1,1,1,1,1,1];

	var ISSort=true;
	var GColSorting="server,server,server,server,server,server,server,server,server";
	var ColSortIndex=0;
	var SortDirection='desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	var PermitDelete=ISPermit("Report.NetLog.List.List.Delete");
	var PermitDeleteAll=ISPermit("Report.NetLog.List.List.DeleteAll");
	
	// Layout   ===================================================================
	
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	//var PermitDisconnectUser=ISPermit("Online.Radius.User.DisconnectUser");


	var RepDate='';	
		
	//=======Popup2 ChangeDate
	var Popup2;
	var PopupId2=['ChangeDate'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangeDate
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Date:'Date of usage(yyyy/mm/dd)'};
	var Form2FieldHelpId=['Date'];
	var Form2Str = [
		{ type:"settings" , labelWidth:110, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"Date_Id",label: "Date :",connector: RenderFile+".php?"+un()+"&act=SelectDate",required:true,inputWidth:100},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "Proceed",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " Close ",width :80}
		]}	
		];

	
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

	if(PermitDelete)   {
		DSToolbarAddButton(ToolbarOfGrid,null,"Delete","Delete","Delete",ToolbarOfGrid_OnDeleteClick);
	}	
	if(PermitDeleteAll)   {
		DSToolbarAddButton(ToolbarOfGrid,null,"DeleteAll","DeleteAll","DeleteAll",ToolbarOfGrid_OnDeleteAllClick);
	}	

	AddPopupChangeDate();
	ToolbarOfGrid.addSeparator("sep3", null);
	DSToolbarAddButton(ToolbarOfGrid,null,"ReverseLookup","Reverse Lookup","ReverseLookup",ToolbarOfGrid_OnReverseLookupClick);

	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	// mygrid.enableLightMouseNavigation(true);
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
	
	ToolbarOfGrid_OnRetrieveClick();
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnReverseLookupClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		var NetLog="https://mxtoolbox.com/SuperTool.aspx?action=ptr%3a"+mygrid.cells(SelectedRowId,mygrid.getColIndexById("DstAddr")).getValue()+"#";
		
		if(prompt("Open reverse lookup?",NetLog))
			window.open(NetLog);
	}
}

function ToolbarOfGrid_OnDeleteClick(){
	alert("Under construction");
	return;
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "Verify",
                type:"confirm-warning",
		text: "Delete selected row?",
		callback: function(result) {
			if(result){
				dhxLayout.progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter+"&RepDate="+RepDate,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						UpdateGrid(1);
						dhtmlx.message("One row successfully deleted");
					}
					else alert(response);

				});
			}
		
		}
});	
}

function ToolbarOfGrid_OnDeleteAllClick(){
	alert("Under construction");
	return;
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
		title: "Verify",
		type:"confirm-warning",
		text: "Delete ALL "+(DSFilter!=''?"filtered ":"")+"row(s)?",
		callback: function(result) {
			if(result){
				dhxLayout.progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DeleteAll"+DSFilter+"&FilterRowNumber="+FilterRowNumber+ExtraFilter+"&RepDate="+RepDate,function (loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					var responseArray=response.split("~",2);
					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(responseArray[0]=='OK') {
						UpdateGrid(1);
						dhtmlx.message(responseArray[1]+" row(s) successfully deleted");
					}
					else alert(response);
				});
			}
		}
	});	
	
}

function AddPopupChangeDate(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ChangeDate","ChangeDate","tow_ChangeDate");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
}

function Form2OnButtonClick(name){
	if(name=='Close') Popup2.hide();
	else{
		Popup2.hide();
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			dhxLayout.progressOn();
			RepDate=Form2.getItemValue('Date_Id');
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
		}
	}
}



function Popup2OnShow(){//AddPayment
	if(typeof Form2 != 'undefined')
		Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
};

function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);		
}			
	

function ToolbarOfGrid_OnRetrieveClick(){
	dhxLayout.progressOn();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
}	

function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else	
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);;
}	
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}
		
	FilterRowNumber++;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
}	
function OnFilterTextPressEnter(){
		if(FilterState)
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
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
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
}	

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue(); 	
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=Username,"+Username, false);
}

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
	}
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------


function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else	
		mygrid.selectRowById(SelectedRowId,false,true,true);
	dhxLayout.progressOff();
	
}


</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>