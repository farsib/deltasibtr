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
	DataTitle="Netlog";
	DataName="DSRep_NetLog_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	
	GColIds="Id,Identication,MACAddress,StartDT,ProtocolNumber,SrcIP,SrcPort,NatIP,NatPort,DstIP,DstDomain,DstPort,Transfer";
	GColHeaders="{#stat_count} ردیف,شناسایی,مک آدرس,تاریخ شروع,شماره پروتکل,آی پی مبدا,پورت مبدا,آی پی ترجمه,پورت ترجمه,آی پی مقصد,دامنه مقصد,پورت مقصد,انتقال";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,120,120,120,100,100,60,100,80,100,200,100,100";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	
	//=======Popup2 Find
	var Popup2;
	var PopupId2=['Find'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 Find
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Password:'Char max length 16'};
	var Form2FieldHelpId=['Password'];
	var Form2Str = [
		{type: "fieldset", width: 400, label: "یافتن", list:[
			{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
			{ type:"input" ,name:"Identication", label:"شناسایی :", maxLength:32, inputWidth:100, focus:true },
			{ type:"input" ,name:"DateFrom", label:"تاریخ از :", maxLength:19, validate:"NotEmpty",inputWidth:150, required:true,focus:true },
			{ type:"input" ,name:"DateTo", label:"تاریخ تا :", validate:"NotEmpty", maxLength:19,inputWidth:150},
			{ type:"input" ,name:"MACAddress", label:"مک آدرس :", maxLength:12,inputWidth:100},
			{ type:"input" ,name:"SrcIP", label:"آی پی مبدا :", maxLength:15,inputWidth:100},
			{ type:"input" ,name:"SrcPort", label:"پورت مبدا :",  maxLength:5,inputWidth:100},
			{ type:"input" ,name:"NatIP", label:"آی پی ترجمه :", maxLength:15,inputWidth:100},
			{ type:"input" ,name:"NatPort", label:"پورت ترجمه :", maxLength:5,inputWidth:100},
			{ type:"input" ,name:"DstIP", label:"آی پی مقصد :", maxLength:15,inputWidth:100},
			{ type:"input" ,name:"DstPort", label:"پورت مقصد :", maxLength:5,inputWidth:100},
			{ type:"input" ,name:"DstDomain", label:"دامنه مقصد :", maxLength:100,inputWidth:100}
		]},
		{type: "block", width: 400, offsetLeft:30,list:[
			{ type:"button",name:"Show",value: "نمایش",width :60},
			{type: "newcolumn", offset:20},
			{ type:"button",name:"ExportToFile",value: "ذخیره در فایل",width :80},
			{type: "newcolumn", offset:20},
			{ type:"button",name:"Clear",value: "پاک کردن",width :60},
			{type: "newcolumn", offset:20},
			{ type:"button" , name:"Close", value:"بستن", width:60}
		]}
	];


	var PermitDelete=ISPermit("Report.NetLog.List.List.Delete");
	var PermitDeleteAll=ISPermit("Report.NetLog.List.List.DeleteAll");
	
	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	/*
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
	*/
	//DSToolbarAddButton(ToolbarOfGrid,null,"View","View","tog_"+DataTitle+"_Edit",ToolbarOfGrid_OnEditClick);
	
	//if(PermitAdd&&PermitView) DSToolbarAddButton(ToolbarOfGrid,null,"Add","افزودن","tog_Add",ToolbarOfGrid_OnAddClick);
	//if(PermitEdit) DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	//else if(PermitView) DSToolbarAddButton(ToolbarOfGrid,null,"View","نمایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	//if(PermitDelete) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	
	AddPopupFind();

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
	

    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo);
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo); 
	
	//LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function AddPopupFind(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Find","یافتن","tow_Filter");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Form2DoAfterUpdateOk(){
	Popup2.hide();
	//LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function Form2DoAfterUpdateFail(){
	Popup2.hide();
}


function Form2DoAfterUpdateFail(){Popup2.hide();}
function Popup2OnShow(){
	if(Form2.getItemValue("DateFrom")=='') Form2.setItemValue("DateFrom",(typeof(Storage) !== "undefined")?localStorage.getItem('LS_CurrentDate')+' 00:00:00':'');
	if(Form2.getItemValue("DateTo")=='') Form2.setItemValue("DateTo",(typeof(Storage) !== "undefined")?localStorage.getItem('LS_CurrentDate')+' 23:59:59':'');
	
}

function Form2OnButtonClick(name){//TestAuth
//	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);;
	dre = /[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]/;

	
	if(name=='Close') Popup2.hide();
	else if((name=='Show')||(name=='ExportToFile')){
		if(!Form2.getItemValue("DateFrom").match(dre)) {
			dhtmlx.alert('Error,Invalid Date From');
			return;
		}
		
		if(!Form2.getItemValue("DateTo").match(dre)) {
			dhtmlx.alert('Error,Invalid Date To');
			return;
		}

		ExtraFilter="&DateFrom="+Form2.getItemValue("DateFrom");
		ExtraFilter=ExtraFilter+"&Identication="+Form2.getItemValue("Identication");
		ExtraFilter=ExtraFilter+"&DateTo="+Form2.getItemValue("DateTo");
		ExtraFilter=ExtraFilter+"&MACAddress="+Form2.getItemValue("MACAddress");
		ExtraFilter=ExtraFilter+"&SrcIP="+Form2.getItemValue("SrcIP");
		ExtraFilter=ExtraFilter+"&SrcPort="+Form2.getItemValue("SrcPort");
		ExtraFilter=ExtraFilter+"&NatIP="+Form2.getItemValue("NatIP");
		ExtraFilter=ExtraFilter+"&NatPort="+Form2.getItemValue("NatPort");
		ExtraFilter=ExtraFilter+"&DstIP="+Form2.getItemValue("DstIP");
		ExtraFilter=ExtraFilter+"&DstPort="+Form2.getItemValue("DstPort");
		ExtraFilter=ExtraFilter+"&DstDomain="+Form2.getItemValue("DstDomain");
		
		if(name=='ExportToFile'){
			Popup2.hide();
			dhxLayout.progressOn();
			var n=0;
			var l=dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=ExportRowCount&ExportFile_Id="+n+ExtraFilter);
			dhxLayout.progressOff();
			response=l.xmlDoc.responseText;
			response=CleanError(response);
			ResArray=response.split("~");
			if((response=='')||(response[0]=='~'))
				dhtmlx.alert("خطا، "+response.substring(1));
			else if(ResArray[0]!='OK')
				dhtmlx.alert("خطا، "+response);
			else	
				n=ResArray[1];		
			if(n==0)
				parent.dhtmlx.alert("Found "+n+" rows!!!");
			else if(n>100000)
				parent.dhtmlx.alert("Found "+n+" rows, It is too high for export");
			else{
				dhxLayout.progressOn();
				parent.dhtmlx.alert("Found "+n+" rows, Press any key to download csv file,\n Please be patient for create and downloading....");
				window.location=RenderFile+".php?"+un()+"&act=ExportDownload&ExportFile_Id="+n+ExtraFilter;
				dhxLayout.progressOff();
			}
		}
		else{
			Popup2.hide();
			ExtraFilter=ExtraFilter+"&Export=No";
			FilterState=false;
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
		}	
	}
	else if(name=='Clear'){
		Form2.setItemValue("DateFrom",(typeof(Storage) !== "undefined")?localStorage.getItem('LS_CurrentDate')+' 00:00:00':'');
		Form2.setItemValue("DateTo",(typeof(Storage) !== "undefined")?localStorage.getItem('LS_CurrentDate')+' 23:59:59':'');

		Form2.setItemValue("Identication","");
		Form2.setItemValue("MACAddress","");
		Form2.setItemValue("SrcIP","");
		Form2.setItemValue("SrcPort","");
		Form2.setItemValue("NatIP","");
		Form2.setItemValue("NatPort","");
		Form2.setItemValue("DstIP","");
		Form2.setItemValue("DstPort","");
		Form2.setItemValue("DstDomain","");
	}
}



function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	//alert('1');
	SelectedRowId=rId;
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Identication")).getValue();
	//alert(Username);
	PopupWindowByUsername(Username);
//	PopupWindow(SelectedRowId);		
}			
function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","Filter: On");
	else	
		ToolbarOfGrid.setItemText("Filter","Filter: Off");
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
		ToolbarOfGrid.setItemText("Filter","Filter: Off");
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
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت انجام شد");
					}
					else alert(response);

				});
			}	
		
		}
});	
}	

function PopupWindowByUsername(Username){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"User");
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=Username,"+Username, false);
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