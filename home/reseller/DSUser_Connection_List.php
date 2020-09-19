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
	DataTitle="User_Connection";
	DataName="DSUser_Connection_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	
	GColIds="Conn_Id,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol,ReturnTr,InternetTr,IntranetTr,MessengerTr,FreeTr,SpecialTr";
	GColHeaders="{#stat_count} ردیف,زمان شروع,زمان خاتمه,زمان نشست,ترافیک ارسالی,ترافیک دریافتی,نام سرویس/آی پی سرور,مک/آی پی,آی پی رزرو شده توسط کاربر,آی پی ردیوس,علت خاتمه,نام سرویس محاسبه,نوع پورت,نوع سرویس,پروتکل,ترافیک برگشتی,ترافیک اینترنت,ترافیک اینترانت,ترافیک پیام رسان,ترافیک رایگان,ترافیک ویژه";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,120,120,95,120,120,140,150,150,100,100,115,100,100,100,100,100,100,100,100,100";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=2;
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

	var PermitAddCallerId=ISPermit("Visp.User.Connection.AddCallerId");

	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);


	//=======Popup2 ChangeDate
	var Popup2;
	var PopupId2=['ChangeDate'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangeDate
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Date:'Date of usage(yyyy/mm/dd)'};
	var Form2FieldHelpId=['Date'];
	var Form2Str = [
		{ type:"settings" , labelWidth:90, inputWidth:150,offsetLeft:10  },
		{ type: "select", name:"Date_Id",label: "تاریخ :",required:true,inputWidth:130},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "اعمال",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];


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
	AddPopupChangeDate();
	ToolbarOfGrid.addSeparator("sep3", null);

	if(PermitAddCallerId)   {
		DSToolbarAddButton(ToolbarOfGrid,null,"AddCallerId","افزودن مک/آی پی","AddCallerId",ToolbarOfGrid_OnAddCallerId);
	}


	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	// mygrid.enableSmartRendering(true);
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

	// Popup2.show("ChangeDate");
	// ToolbarOfGrid_OnRetrieveClick();


	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function AddPopupChangeDate(){
	ToolbarOfGrid.addButton("PreviousDate", null, "","ar_left.gif","ar_left_dis.gif");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ChangeDate","<span style='color:indianred;font-style:oblique;'>Loading...</span>","tow_ChangeDate");
	ToolbarOfGrid.addButton("NextDate", null, "","ar_right.gif","ar_right_dis.gif");
	ToolbarOfGrid.disableItem("PreviousDate");
	ToolbarOfGrid.disableItem("ChangeDate");
	ToolbarOfGrid.disableItem("NextDate");
	ToolbarOfGrid.attachEvent("onclick",function(id){
		if((id=="PreviousDate")||(id=="NextDate")){
			var opts = Form2.getSelect('Date_Id');
			if(id=="PreviousDate")
				opts.selectedIndex--;
			else
				opts.selectedIndex++;
			Form2OnButtonClick("Proceed");
		}
	});
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onEnter",function(){Form2OnButtonClick("Proceed");});
	Form2.lock();
	dhxLayout.progressOn();
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=SelectDate&User_Id="+ParentId,function(loader){
		dhxLayout.progressOff();
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		ResponseArray=response.split('~');
		if(response=='')
			dhtmlx.alert({
				text:"No date available",
				type:"alert-error",
				title:"هشدار"
			});
		else if(response[0]=='~')
			dhtmlx.alert("خطا، "+response.substring(1));
		else{
			var opts = Form2.getSelect('Date_Id');

			opts.innerHTML=response;
			opts.size=10;
			opts.onclick=function(){
				Form2OnButtonClick("Proceed");
			}
			if(opts.length>1)
				ToolbarOfGrid.enableItem("PreviousDate");


			opts.selectedIndex=opts.length-1;
			var SelectedDate=opts[opts.selectedIndex].text;
			if(SelectedDate.substr(0,4)=="Last")
				ToolbarOfGrid.setItemText("ChangeDate","<span style='color:firebrick;font-weight:bold'>"+SelectedDate+"</span>");
			else
				ToolbarOfGrid.setItemText("ChangeDate","<span style='color:royalblue;font-weight:bold'>"+SelectedDate+"</span>");
			ToolbarOfGrid.enableItem("ChangeDate");
			Popup2.show("ChangeDate");
			Form2.unlock();
		}
	});

}

function Form2OnButtonClick(name){
	if(name=='Close') Popup2.hide();
	else{

		Popup2.hide();
		var opts=Form2.getSelect("Date_Id");

		ToolbarOfGrid.enableItem("PreviousDate");
		ToolbarOfGrid.enableItem("NextDate");
		if(opts.selectedIndex==0)
			ToolbarOfGrid.disableItem("PreviousDate");
		if(opts.selectedIndex==(opts.length-1))
			ToolbarOfGrid.disableItem("NextDate");

		var SelectedDate=opts[opts.selectedIndex].text;
		if(SelectedDate.substr(0,4)=="Last")
			ToolbarOfGrid.setItemText("ChangeDate","<span style='color:firebrick;font-weight:bold'>"+SelectedDate+"</span>");
		else
			ToolbarOfGrid.setItemText("ChangeDate","<span style='color:royalblue;font-weight:bold'>"+SelectedDate+"</span>");
		ToolbarOfGrid_OnRetrieveClick();

	}
}

function Popup2OnShow(){
}

function ToolbarOfGrid_OnAddCallerId(){
	var RepDate=Form2.getItemValue('Date_Id');
	if(RepDate.substr(0,4)=="Last"){
		dhtmlx.alert({text:"نمی توان مک/آی پی را اضافه کرد درحالی که داده ها آشکار نیستند",title:"هشدار",type:"alert-error",ok:"بستن"});
		return;
	}
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-warning",
			text: "Add CallerId["+mygrid.cells(SelectedRowId,mygrid.getColIndexById("CallingStationId")).getValue()+"]?",
			callback: function(result) {
				if(result){
					dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=AddCallerId&Id="+SelectedRowId+ExtraFilter+"&RepDate="+RepDate,function (loader){
						response=loader.xmlDoc.responseText;
						response=CleanError(response);

						if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
						else if(response=='OK~') {
							dhtmlx.message("Successfully CallerId Added");
						}
						else alert(response);

					});
				}
			}
		});
	}
}


function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	ToolbarOfGrid_OnRetrieveClick();
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	var RepDate=Form2.getItemValue('Date_Id');
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&RepDate="+RepDate,DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	ToolbarOfGrid_OnRetrieveClick();
}
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}

	FilterRowNumber++;
	ToolbarOfGrid_OnRetrieveClick();
}
function OnFilterTextPressEnter(){
	if(FilterState){
		ToolbarOfGrid_OnRetrieveClick();
	}
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
	ToolbarOfGrid_OnRetrieveClick();
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
	ToolbarOfGrid_OnRetrieveClick();
}

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+SelectedRowId, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function DoAfterRefresh(){
	mygrid.selectRowById(SelectedRowId,false,true,true);
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
