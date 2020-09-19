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
	DataTitle="Status";
	DataName="DSUser_Status_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";
	GColIds="User_Status_Id,StatusName,StatusCDT,ResellerName,Comment";
	GColHeaders="{#stat_count} ردیف,نام وضعیت,زمان ثبت,نام نماینده فروش,توضیح";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1];

	GFooter="";
	GColInitWidths="80,300,120,100,300";
	GColAligns="center,left,center,center,left";
	GColTypes="ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1];

	ISSort=false;
	GColSorting="server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};


	ISPermitChangeStatus= ISPermit("Visp.User.Status.ChangeStatus");
	ISPermitTimedStatus= ISPermit("Visp.User.Status.SetTimedStatus");
	ISPermitDeleteScheduled= ISPermit("Visp.User.Status.DeleteScheduled");

	//=======Popup2 ChangeStatus
	var Popup2;
	var PopupId2=['ChangeStatus'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangeStatus
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {
		Status_Id:'وضعیت کاربری',
		StatusType:'انتخاب نوع وضعیت.وضعیت زمانبندی شده،وضعیت کاربر را به وضعیت جدید تغییر خواهد داد',
		ScheduledStatus_Text:'وضعیت جدید پس از زمانبندی',
		ScheduleHour:'تعداد ساعت برای تغییر به وضعیت جدید انتخاب شده'
	};
	var Form2FieldHelpId=['Status_Id','StatusType','ScheduleHour','ScheduledStatus_Text'];
	var Form2Str = [
		{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"Status_Id",label: "نام وضعیت :",connector: RenderFile+".php?"+un()+"&act=SelectStatusItem&User_Id="+ParentId,required:true,inputWidth:400, info:true},

		{type: "block", name:"StatusDetailBlock1",hidden:true,width: 520,list:[
			{ type: "select" , name:"UserStatus", label:"<span style='color:#505050'>وضعیت کاربر :</span>", disabled: true, style:"color:#505050",labelAlign:"left",inputWidth:200, options:[
				{text: "Enable", value: "Enable",selected: true},
				{text: "Disable", value: "Disable"},
				{text: "ChangeOnFirstConnect", value: "ChangeOnFirstConnect",list:[
					{ type: "input", name:"NewStatus",label:"<span style='color:#505050'>وضعیت بعدی :</span>", disabled: true, style:"color:#505050",inputWidth:350,info:true},
				]},
				{text: "AddFreeServiceByNAS", value: "AddFreeServiceByNAS",list:[
					{ type: "input", name:"NewStatus",label:"<span style='color:#505050'>وضعیت بعدی :</span>", disabled: true, style:"color:#505050",inputWidth:350,info:true},
				]}

			]},
		]},
		{type: "block", name:"StatusDetailBlock2",hidden:true,width: 520,list:[
			{ type: "input" , name:"CanWebLogin", label:"<span style='color:#505050'>وارد پنل می شود :</span>", disabled: true, style:"color:#505050",labelAlign:"left", inputWidth:80},
			{ type: "input" , name:"CanAddService", label:"<span style='color:#505050'>سرویس اضافه می شود :</span>", disabled: true, style:"color:#505050",labelAlign:"left", inputWidth:80},
			{type: "newcolumn"},
			{ type: "input" , name:"IsBusyPort", label:"<span style='color:#505050'>پورت اشغال شده :</span>", disabled: true, style:"color:#505050",labelAlign:"left", inputWidth:80},
			{ type: "input" , name:"PortStatus", label:"<span style='color:#505050'>وضعیت پورت :</span>", disabled: true, style:"color:#505050",labelAlign:"left", inputWidth:80},
		]},
		{type:"label"},
	];
	if(ISPermitTimedStatus)
		Form2Str.push(
			{type: "select", name:"StatusType", label: "نوع وضعیت :",required:true, options:[
				{text: "ثابت", value: "Fixed",selected: true},
				{text: "زمانبندی", value: "Timed",list:[
					{ type: "input" , name:"ScheduledStatus_Text", label:"نام وضعیت :", labelAlign:"left",inputWidth:370, info:true,required:true,validate:"NotEmpty"},
					{ type: "hidden" , name:"ScheduledStatus_Id", value:0},
					{ type: "input" , name:"ScheduleHour", label:"تعداد ساعت تغییر :", validate:"NotEmpty,ValidInteger", labelAlign:"left", maxLength:4,inputWidth:100,required:true, info:true},
				]}
			],inputWidth:100,info:true,disabled:true}
		);
	else
		Form2Str.push({type:"hidden",name:"StatusType",value:"Fixed"});

	Form2Str.push(
		{ type: "input" , name:"Comment", label:"توضیحات :", validate:"", labelAlign:"left", maxLength:128,inputWidth:402,rows:3,disabled:true},
		{type: "block", width: 310, list:[
			{ type: "button",name:"Proceed",value: "تغییر وضعیت",width :80,disabled:true},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
	);


	var Popup2_1;

	var Form2_1;
	function CreateForm2_1Str(Status_Id){
		var Form2_1Str = [
			{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
			{type:"label",label:"برای زمانبندی وضعیت را انتخاب کنید"},
			{ type: "select", name:"ScheduledStatus_Id",connector: RenderFile+".php?"+un()+"&act=SelectScheduleStatusItem&User_Id="+ParentId+"&Status_Id="+Status_Id,required:true,inputWidth:450, info:true,note: { text: "<span style='color:red;font-weight:bold'>وضعیت هایی که پورت اشغال شده دارند قابل انتخاب هستند</span>"}},
			{type: "block", width: 310, list:[
				{ type: "button",name:"OK",value: "انتخاب",width :80,disabled:true},
				{type: "newcolumn"},
				{ type: "button",name:"Cancel",value: "بستن",width :80},
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
	if(ISPermitChangeStatus) AddPopupChangeStatus();
	if(ISPermitDeleteScheduled) DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف وضعیت زمان بندی شده","tog_Delete",ToolbarOfGrid_OnDeleteClick);


	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	mygrid.enableSmartRendering(false);
	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);
			if(input) input.style.width = cWidth-20;
		}
		return true;
	});
	mygrid.attachEvent("onRowSelect", SetButton);

    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)

	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);


	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
//-------------------------------------------------------------------ToolbarOfGrid_OnDeleteClick()
function ToolbarOfGrid_OnDeleteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	dhtmlx.confirm({
		title: "هشدار",
                type:"confirm-warning",
                cancel: "خیر",
        				ok: "بلی",
		text: "برای حذف مطمئن هستید؟",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=DeleteScheduled&User_Id="+ParentId,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت حذف شد");
						SetButton();
					}
					else alert(response);

				});
			}

		}
});
}

function AddPopupChangeStatus(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ChangeStatus","تغییر وضعیت","tow_ChangeStatus");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Popup2OnShow(){//ChangeStatus
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
	Form2.lock();
	Form2.attachEvent("onOptionsLoaded",function(name){
		Form2.unlock();
		if(name=='Status_Id'){
			var opts = Form2.getOptions("Status_Id");
			if(opts.length<=0){
				dhtmlx.alert({text:"گزینه ای برای انتخاب وجود ندارد",ok:"بستن"});
				Popup2.hide();
			}
			else
				Form2onChange("Status_Id",opts[opts.selectedIndex].value);
		}
	});

	if(ISPermitTimedStatus){
		Form2.attachEvent("onFocus",function(name){if(name=="ScheduledStatus_Text"){Popup2_1.show('ScheduledStatus_Text');}});
		Form2.attachEvent("onHide",function(){Popup2_1.hide()});
		Popup2_1=new dhtmlXPopup({form:Form2,id:["ScheduledStatus_Text"],mode:"bottom"});
		//Popup2_1.setSkin(popup_main_skin);
		Popup2_1.attachEvent("onShow",Popup2_1OnShow);
		Form2_1 = Popup2_1.attachForm({});
	}
}

function Popup2_1OnShow(){
	// dhtmlx.message("onShow="+name);
	Form2_1.unload();
	Form2_1 = Popup2_1.attachForm(CreateForm2_1Str(Form2.getItemValue("Status_Id")));
	//Form2_1.setSkin(form_main_skin);
	Form2_1.lock();
	Form2_1.attachEvent("onChange",function(){
		var Opts=Form2_1.getSelect("ScheduledStatus_Id");
		Opts.style.direction=GetTextDirection(Opts.options[Opts.selectedIndex].text);
	});
	Form2_1.attachEvent("onOptionsLoaded",function(){
		Form2_1.unlock();
		var Opts=Form2_1.getSelect("ScheduledStatus_Id");
		if(Opts.length==0){
			parent.dhtmlx.message({text:"پس از وضعیت انتخاب شده،وضعیتی برای تغییر به آن وجود ندارد",type:"error"});
			Form2.setItemValue("StatusType","Fixed");
			Popup2_1.hide();
			Form2.setItemFocus("Comment");
		}
		else{
			Opts.style.direction=GetTextDirection(Opts.options[Opts.selectedIndex].text);
			if(Form2.getItemValue("ScheduledStatus_Id")>0)
				Form2_1.setItemValue("ScheduledStatus_Id",Form2.getItemValue("ScheduledStatus_Id"));
			Form2_1.enableItem("OK");
			Form2_1.attachEvent("onButtonClick", function(name){
				Popup2_1.hide();
				if(name=="Cancel"){
					Form2.setItemValue("StatusType","Fixed");
					Form2onChange("StatusType","Fixed");
				}
				else if(name=="OK"){
					Form2.setItemValue("ScheduledStatus_Id",Form2_1.getItemValue("ScheduledStatus_Id"));
					Form2.setItemValue("ScheduledStatus_Text",opts[opts.selectedIndex].text);
					Form2.setItemFocus("ScheduleHour");
				}
			});

		}
	});
}
function Form2onChange(id, value){
	// dhtmlx.message("<span style='color:blue'>Input</span>\nid="+id+"\nvalue="+value);
	if(id=='Status_Id'){
		if(value==0){
			Form2.hideItem("StatusDetailBlock1");
			Form2.hideItem("StatusDetailBlock2");
			Form2.disableItem("StatusType");
			Form2.disableItem("Comment");
			Form2.disableItem("Proceed");
		}
		else{
			Form2.showItem("StatusDetailBlock1");
			Form2.showItem("StatusDetailBlock2");
			dhxLayout.cells("a").progressOn();
			Form2.lock();
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetStatusDetail&Status_Id="+value,SetParamInfo2);
		}
	}
	else if(id=='StatusType'){
		if(value=='Fixed')
			Popup2_1.hide();
		else if(value=='Timed')
			setTimeout(function(){Popup2_1.show('ScheduledStatus_Text');},100);
	}
}

function SetParamInfo2(loader){
	dhxLayout.cells("a").progressOff();
	Form2.unlock();
	Form2.enableItem("Comment");
	Form2.enableItem("Proceed");
	response=loader.xmlDoc.responseText;
	response=CleanError(response);
	if((response=='')||(response[0]=='~')){
		dhtmlx.alert("خطا، "+response.substring(1));
		Form2.lock();
	}
	else{
		// alert(response);
		var parray=response.split("`",6);
		Form2.setItemValue("UserStatus",parray[0]);
		Form2.setItemValue("CanWebLogin",parray[1]);
		Form2.setItemValue("CanAddService",parray[2]);
		Form2.setItemValue("IsBusyPort",parray[3]);
		Form2.setItemValue("PortStatus",parray[4]);
		Form2.setItemValue("NewStatus",parray[5]);
		Form2.getInput("NewStatus").style.direction=GetTextDirection(parray[5]);

		Form2.setItemFocus("Comment");

		Form2.setItemValue("ScheduledStatus_Text","");
		Form2.setItemValue("ScheduledStatus_Id",0);

		if(parray[0]=='ChangeOnFirstConnect'){
			Form2.disableItem("StatusType");
			Form2.setItemValue("StatusType","Fixed");
			Popup2_1.hide();
		}
		else{
			Form2.enableItem("StatusType");
			if(Form2.getItemValue("StatusType")=="Timed")
				setTimeout(function(){Popup2_1.show('ScheduledStatus_Text');},100);
		}
	}
}

function Form2OnButtonClick(name){//ChangeStatus
	if(name=='Close') Popup2.hide();
	else if(name=='Proceed'){
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.disableItem("Proceed");
			Popup2.hide();
			DSFormUpdateRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=ChangeStatus&id="+ParentId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}

function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterUpdateFail(){
	Popup2.hide();
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

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServer(RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServer(RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	}
}

function SetButton(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(ISPermitDeleteScheduled){
		if(SelectedRowId!=null){
			if(mygrid.cells(SelectedRowId,mygrid.getColIndexById("User_Status_Id")).getValue()==0)
				ToolbarOfGrid.enableItem('Delete');
			else
				ToolbarOfGrid.disableItem('Delete');
		}
		else
			ToolbarOfGrid.disableItem('Delete');
	}
}

function DoAfterRefresh(){
	if((SelectedRowId==null)||(SelectedRowId==0))
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);
	SetButton();
	dhxLayout.cells("a").progressOff();
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
