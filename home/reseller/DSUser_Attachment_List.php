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
	DataTitle="User_Attachment";
	DataName="DSUser_Attachment_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	
	GColIds="User_Attachment_Id,RealFilename,Comment,Size,Creator,User_AttachmentCDT,tmpfilename";
	GColHeaders="{#stat_count} ردیف,نام فایل,توضیح,حجم فایل,ثبت کننده,زمان ثبت,نام فایل موقت";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,150,300,100,90,140,10";
	GColAligns="center,center,left,left,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,0];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:500,height:300,
				center:true,
				modal:true,
				park :false
				};
	
	var PermitAdd=false;
	var PermitEdit=false;
	var PermitDelete=false;

	//=======Popup2 AddAttachment
	var Popup2;
	var PopupId2=['AddAttachment'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Filename:'Filename(Char max length 64)'};
	var Form2FieldHelpId=['Filename'];
	var Form2Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type: "label"},
		{type: "upload",name: "Uploader",inputWidth: 310,titleScreen: true,autoStart: true,autoRemove:true,titleText :"کلیک کنید یا فایل را اینجا بکشید",url: "DSUser_Attachment_ListRender.php?un="+un()+"&User_Id="+ParentId+"&act=AddAttachment",_swfLogs: "enabled",swfPath: "uploader.swf",swfUrl: "DSUser_Attachment_ListRender.php?un="+un()+"&User_Id="+ParentId+"&act=AddAttachment"},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
	//=======Popup3 DeleteAttachment
	var Popup3;
	var PopupId3=['DeleteAttachment'];//  popup Attach to Which Buttom of Toolbar
	//=======Form3 DeleteAttachment
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {	Filename:'Filename'};
	var Form3FieldHelpId=['Filename'];
	var Form3Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type: "label",label :'<span style="padding-left:115px;">از حذف فایل مطمئن هستید؟</span>'},
		{ type:"input" , name:"Comment", label:"توضیح :",disabled:"true",maxLength:64, validate:"", labelAlign:"left", info:"true",inputWidth:200},
		{ type:"input" , name:"FileName", label:"نام فایل :",disabled:"true",maxLength:64, validate:"", labelAlign:"left", info:"true",inputWidth:200},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Delete",value: "حذف",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];

	//=======Popup3 EditAttachment
	var Popup4;
	var PopupId4=['EditAttachment'];//  popup Attach to Which Buttom of Toolbar
	//=======Form4 EditAttachment
	var Form4;
	var Form4PopupHelp;
	var Form4FieldHelp  = {	Filename:'Filename'};
	var Form4FieldHelpId=['Filename'];
	var Form4Str = [
		{ type:"settings" , labelWidth:80, inputWidth:80,offsetLeft:10  },
		{ type:"input" , name:"Comment", label:"توضیح :",maxLength:64, validate:"", labelAlign:"left", info:"true",inputWidth:200},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Edit",value: "انجام",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
		
	ISPermitAdd=ISPermit('Visp.User.Attachment.Add');
	ISPermitView=ISPermit('Visp.User.Attachment.View');
	ISPermitEdit=ISPermit('Visp.User.Attachment.Edit');
	ISPermitDelete=ISPermit('Visp.User.Attachment.Delete');
	
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

	if (ISPermitAdd) AddPopupAddAttachment();
	if (ISPermitDelete) AddPopupDeleteAttachment();
	if (ISPermitEdit) AddPopupEditAttachment();
	
	

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
	if(ISPermitView){
		DSToolbarAddButton(ToolbarOfGrid,null,"Download","دانلود","tow_DownloadAttachment",ToolbarOfGrid_OnDownloadClick);
		mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo); 
	}
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnDownloadClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		var tmpfilename=mygrid.cells(SelectedRowId,mygrid.getColIndexById("tmpfilename")).getValue(); 
		window.open(RenderFile+".php?"+un()+"&act=ViewAttachment&User_Attachment_Id="+SelectedRowId+"&tmpfilename="+tmpfilename);
	}
}
function AddPopupAddAttachment(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddAttachment","افزودن","tow_AddAttachment");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function AddPopupDeleteAttachment(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"DeleteAttachment","حذف","tow_DeleteAttachment");
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}

function AddPopupEditAttachment(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"EditAttachment","ویرایش","tow_EditAttachment");
	Popup4=DSInitialPopup(ToolbarOfGrid,PopupId4,Popup4OnShow);
	Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,Form4OnButtonClick);
}

function Form2OnButtonClick(name){//AddAttachment
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=AddAttachment&User_Id="+ParentId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}
function Form3OnButtonClick(name){//DeleteAttachment
	if(name=='Close') Popup3.hide();
	else{
		DSFormUpdateRequestProgress(dhxLayout,Form3,RenderFile+".php?"+un()+"&act=DeleteAttachment&User_Attachment_Id="+SelectedRowId,Form3DoAfterUpdateOk,Form3DoAfterUpdateFail);
	}
}

function Form4OnButtonClick(name){//EditAttachment
	if(name=='Close') Popup4.hide();
	else{
		if(DSFormValidate(Form4,Form4FieldHelpId)){
			if(Form4.getItemValue("Comment")=="Upload failed")
				alert("Cannot change comment to 'Upload failed'!");
			else
				DSFormUpdateRequestProgress(dhxLayout,Form4,RenderFile+".php?"+un()+"&act=EditAttachment&User_Attachment_Id="+SelectedRowId,Form4DoAfterUpdateOk,Form4DoAfterUpdateFail);
		}
	}
}


function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form3DoAfterUpdateOk(){
	Popup3.hide();
	SelectedRowId=0;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function Form4DoAfterUpdateOk(){
	Popup4.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}


function Form2DoAfterUpdateFail(){
	Popup2.hide();
}

function Form3DoAfterUpdateFail(){
	Popup3.hide();
}

function Form4DoAfterUpdateFail(){
	Popup4.hide();
}

function Popup2OnShow(){//AddAttachment
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onUploadComplete",function(count){
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
    });
	Form2.attachEvent("onUploadFail",function(count){
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
    });
}

function Popup3OnShow(){//DeleteAttachment
	Form3.unload();
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null){
		Popup3.hide();
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	}
	else
		Form3.load(RenderFile+".php?"+un()+"&act=LoadDeleteAttachmentForm&User_Attachment_Id="+SelectedRowId);
}

function Popup4OnShow(){//EditAttachment
	Form4.unload();
	Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,Form4OnButtonClick);
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null){
		Popup4.hide();
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	}
	else
		Form4.load(RenderFile+".php?"+un()+"&act=LoadEditAttachmentForm&User_Attachment_Id="+SelectedRowId,function(){
			if(Form4.getItemValue("Comment")=="Upload failed"){
				Popup4.hide();
				dhtmlx.alert({text:"Cannot change comment of this file",type:"alert-error"});
			}
				
		});
}


function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	var tmpfilename=mygrid.cells(SelectedRowId,mygrid.getColIndexById("tmpfilename")).getValue(); 
	window.open(RenderFile+".php?"+un()+"&act=ViewAttachment&User_Attachment_Id="+SelectedRowId+"&tmpfilename="+tmpfilename);
	//var loader = dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=ViewAttachment&User_Attachment_Id="+SelectedRowId);
	//PopupWindow(SelectedRowId);		
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
	popupWindow.attachURL(RenderFile+".php?"+un()+"&act=ViewAttachment&User_Attachment_Id="+SelectedRowId, false);
	
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
