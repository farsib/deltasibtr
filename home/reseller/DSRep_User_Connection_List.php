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
	
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATETIMESTR(NOW())');?>";
	var DTArr=TNow.split(" ");
	var CurDate=DTArr[0].split("/");
	var CurTime=DTArr[1].split(":");	
	
	
	var BlockTMP = [];
	
	BlockTMP = [];
	
	BlockTMP[1]=[];
	for(i=1390;i<=1450;++i)
		BlockTMP[1].push({text: i, value: i});
	
	BlockTMP[2]=[];
	for(i=1;i<=12;++i)
		BlockTMP[2].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP[3]=[];
	for(i=1;i<=31;++i)
		BlockTMP[3].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP[4]=[];
	for(i=0;i<=23;++i)
		BlockTMP[4].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});
	
	BlockTMP[5]=[];
	for(i=0;i<=59;++i)
		BlockTMP[5].push({text: ("0"+i).slice(-2), value: ("0"+i).slice(-2)});	
	
	
	
	
	
	
	
	
	DataTitle="Report Connection";
	DataName="DSRep_User_Connection_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	
	GColIds="Conn_Id,Username,AcctStartTime,AcctStopTime,AcctSessionTime,SendTr,ReceiveTr,CalledStationId,CallingStationId,FramedIpAddress,NasIpAddress,TerminateCause,ServiceInfoName,NasPortType,ServiceType,FramedProtocol";
	GColHeaders="{#stat_count} ردیف,نام کاربری,زمان شروع,زمان توقف,زمان نشست,ترافیک ارسالی,ترافیک دریافتی,مک/آی پی سرور,مک/آی پی,آی پی تعریف شده,آی پی ردیوس,دلیل قطع,نام سرویس محاسبه,نوع پورت ردیوس,نوع سرویس,پروتکل";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	GColInitWidths="100,120,120,120,95,120,120,95,100,102,100,100,150,100,100,150";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=2;
	SortDirection='desc';

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


	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);


	
	
	//=======Popup1 WhoHaveIP
	var Popup1;
	var PopupId1=['WhoHaveIP'];//popup Attach to Which Buttom of Toolbar
	//=======Form1 WhoHaveIP
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  = {	User_Id:'ReqIP'};
	var Form1FieldHelpId=['ReqIP'];
    var Form1Str = [
		{type: "fieldset", width:310, label: " بررسی آی پی ", list:[
			{type: "block", width:280, list:[
				{type:"input", label:"آی پی : ", name: "ReqIP", labelWidth: 52, inputWidth: 167, maxLength: 15, validate: "NotEmpty,ValidIPv4", required:true}
			]},
			{type: "block", list:[
				{type: "select",  name: "IPYear", label: "تاریخ : ", options:BlockTMP[1], labelWidth: 50, inputWidth:55, required:true},
				{type: "newcolumn", offset:5},
				{type: "select",  label: " / ", labelAlign:"left", name: "IPMonth", options:BlockTMP[2], inputWidth:40},
				{type: "newcolumn", offset:5},
				{type: "select",  label: " / ", labelAlign:"left", name: "IPDay31", options:BlockTMP[3], inputWidth:40,hidden:true},
				{type: "select",  label: " / ", labelAlign:"left", name: "IPDay30", options:BlockTMP[3].slice(0,30), inputWidth:40,hidden:true},
				{type: "select",  label: " / ", labelAlign:"left", name: "IPDay29", options:BlockTMP[3].slice(0,29), inputWidth:40,hidden:true},
			]},
			{type: "block", list:[
				{type: "select",  name: "IPHour", label: "زمان : ", options:BlockTMP[4], labelWidth: 50, inputWidth:55, required:true},
				{type: "newcolumn", offset:5},
				{type: "select",  label: " : ", labelAlign:"left", name: "IPMinute", options:BlockTMP[5], inputWidth:40},
				{type: "newcolumn", offset:5},
				{type: "select",  label: " : ", labelAlign:"left", name: "IPSecond", options:BlockTMP[5], inputWidth:40},
				{type: "newcolumn", offset:5}
			]}
		]},
		{type: "block", width: 280, list:[
			{type: "newcolumn", offset:80},
			{type: "button",name: "Cancel",value: " لغو ",width :70},			
			{type: "newcolumn", offset:22},
			{type: "button",name: "Proceed",value: " جستجو ",width :70}
		]}
	];	

	
	//=======Popup2 ChangeDate
	var Popup2;
	var PopupId2=['ChangeDate'];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 ChangeDate
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	Date_Id:'Date of usage(yyyy/mm/dd)'};
	var Form2FieldHelpId=['Date_Id'];
	var Form2Str = [
		{ type:"settings" , labelWidth:90, inputWidth:150,offsetLeft:10  },
		{ type: "select", name:"Date_Id",label: "تاریخ :",required:true,inputWidth:130},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "نمایش",width :80},
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
	
	DSToolbarAddButton(ToolbarOfGrid,null,"SaveToFile","ذخیره در فایل","SaveToFile",ToolbarOfGrid_OnSaveToFileClick);
	ToolbarOfGrid.setItemToolTip("SaveToFile","CSVذخیره نتایج گزارش در فایل");
	
	ToolbarOfGrid.addSeparator("sep3", null);
	AddPopupWhoHaveIP();
	

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
	
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo); 
	
	// ToolbarOfGrid_OnRetrieveClick();
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//-------------------------------------------------------------------MonthDays(FYear,FMonth)
function MonthDays(FYear,FMonth){
	if(FMonth<=6)
		return 31
	else if(FMonth<=11)
		return 30
	else{
		var tmp=(FYear % 33);
		if(tmp==1||tmp==5||tmp==9||tmp==13||tmp==18||tmp==22||tmp==26||tmp==30)
			return 30
		else
			return 29;
	} 
}
//-------------------------------------------------------------------AddPopupWhoHaveIP()
function AddPopupWhoHaveIP(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"WhoHaveIP","بررسی آی پی","tow_WhoHaveIP");
	ToolbarOfGrid.setItemToolTip("WhoHaveIP","بررسی آی پی در تاریخ و زمان مشخص");
	Popup1=DSInitialPopup(ToolbarOfGrid,PopupId1,Popup2OnShow);
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onBeforeChange",Form1OnBeforeChange);
	Form1.attachEvent("onEnter",function(){Form2OnButtonClick("Proceed")});
	
	Form1.setItemValue("IPYear",CurDate[0]);
	Form1.setItemValue("IPMonth",CurDate[1]);
	Form1.setItemValue("IPDay"+MonthDays(CurDate[0],CurDate[1]),CurDate[2]);Form1.showItem("IPDay"+MonthDays(CurDate[0],CurDate[1]));
	Form1.setItemValue("IPHour",CurTime[0]);
	Form1.setItemValue("IPMinute",CurTime[1]);
	Form1.setItemValue("SIPSecond",CurTime[2]);
}

//-------------------------------------------------------------------Popup1OnShow()
function Popup1OnShow(){
	if(!ISValidResellerSession()) return;
	Form1.setItemFocus("ReqIP");
}

//-------------------------------------------------------------------Form2OnBeforeChange(id, value)
function Form1OnBeforeChange(id,value,Newvalue){
	//alert("Before:\nid:'"+id+"'     value:'"+value+"'      state:'"+state+"'");
	var tmp1;
	var tmp2;
	var tmp3;
	if(id=='IPYear'){
		tmp1=Form1.getItemValue("IPMonth");
		tmp2=MonthDays(value,tmp1);
		tmp3=MonthDays(Newvalue,tmp1);
		Form1.hideItem("IPDay"+tmp2);
		Form1.showItem("IPDay"+tmp3);
		Form1.setItemValue("IPDay"+tmp3,Math.min(Form1.getItemValue("IPDay"+tmp2),tmp3));
	}
	else if(id=='IPMonth'){
		tmp1=Form1.getItemValue("IPYear");
		tmp2=MonthDays(tmp1,value);
		tmp3=MonthDays(tmp1,Newvalue);
		Form1.hideItem("IPDay"+tmp2);
		Form1.showItem("IPDay"+tmp3);
		Form1.setItemValue("IPDay"+tmp3,Math.min(Form1.getItemValue("IPDay"+tmp2),tmp3));
	}
	return true;
}

//-------------------------------------------------------------------Form2OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='Cancel')
		Popup1.hide();
	else{
		if(!Form1.validateItem("ReqIP")){
			Form1.setItemFocus("ReqIP");
			dhtmlx.message("لطفا آی پی موردنظر خود را وارد کنید");
			return
		}
		Popup1.hide();
		var tmp1=Form1.getItemValue("IPYear");
		var tmp2=Form1.getItemValue("IPMonth");
		ToolbarOfGrid.disableItem('WhoHaveIP');
		dhxLayout.progressOn();
		var ReqDT=tmp1+"/"+tmp2+"/"+Form1.getItemValue("IPDay"+MonthDays(tmp1,tmp2))+" "+Form1.getItemValue("IPHour")+":"+Form1.getItemValue("IPMinute")+":"+Form1.getItemValue("IPSecond");
		var ReqIP=Form1.getItemValue("ReqIP");
		
		dhtmlxAjax.get(RenderFile+".php?act=WhoHaveIP&ReqDT="+ReqDT+"&ReqIP="+ReqIP,
			function(loader){
				dhxLayout.progressOff();
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				ResponseArray=response.split('~');
				if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				else if(ResponseArray[0]!='OK')
					dhtmlx.alert(response);
				else if(ResponseArray[1]==0)
					dhtmlx.alert("Not found "+ReqIP+" at "+ReqDT);
				else{
					n=ResponseArray[1];
					if(n==1){
						ResponseList=ResponseArray[2].split("`");
						dhtmlx.confirm({
							title: "هشدار",
							type:"confirm",
							ok: "بلی",
							cancel: "خیر",
							text: (ResponseList[0]=='Online')?
									("Username '"+ResponseList[2]+"' currently have the IP. (Online_RadiusUser_Id="+ResponseList[3]+"). بازکردن اطلاعات کاربر؟"):
									("Username '"+ResponseList[2]+"' has the IP. (ConnectionId="+ResponseList[3]+"). بازکردن اطلاعات کاربر؟"),
							callback: function(Result){
								if(Result){
									PopupWindowUserId(ResponseList[1]);
								}
							}
						});	
					}
					else{
						var OutStr=n+" entry found for "+ReqIP+" at "+ReqDT;
						for(i=1;i<=n;++i){
							ResponseList=ResponseArray[i+1].split("`");
							OutStr+="<br/>"+
								(
									(ResponseList[0]=='Online')?
									(i+"/"+n+" : Username '"+ResponseList[2]+"' currently have the IP. (Online_RadiusUser_Id="+ResponseList[3]+")"):
									(i+"/"+n+" : Username '"+ResponseList[2]+"' has the IP. (Connection_Id="+ResponseList[3]+").")
								);
						}
						dhtmlx.alert(OutStr);
					}
					
				}
				ToolbarOfGrid.enableItem('WhoHaveIP');
			}
		);
	}
}

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
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=SelectDate",function(loader){
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



function Popup2OnShow(){//AddPayment
	// if(typeof Form2 != 'undefined')
		// Form2.unload();
	
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
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter+"&req=ShowInGrid&RepDate="+RepDate,DoAfterRefresh);
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
	if(FilterState)
		ToolbarOfGrid_OnRetrieveClick();
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

//-------------------------------------------------------------------ToolbarOfGrid_OnSaveToFileClick()
function ToolbarOfGrid_OnSaveToFileClick(){
	if(mygrid.getRowsNum()<=0){
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "داده ای برای ذخیره موجود نیست"});
		return
	}	
	if(!ISValidResellerSession()) return;
	ToolbarOfGrid.disableItem('SaveToFile');
	SortField=mygrid.getColumnId(ColSortIndex);
	var RepDate=Form2.getItemValue('Date_Id');
	ExtraFilter+"&req=ShowInGrid&RepDate="+RepDate
	window.open(RenderFile+".php?act=list&req=SaveToFile&RepDate="+RepDate+"&SortField="+SortField+"&SortOrder="+SortDirection);
	//dhxLayout.cells("a").attachURL(RenderFile+".php?act=list&req=SaveToFile&SortField="+SortField+"&SortOrder="+SortDirection+MyPostString);
	setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile')},2000);
}
function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	var Username=mygrid.cells(SelectedRowId,mygrid.getColIndexById("Username")).getValue();
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=Username,"+Username, false);
}
//-------------------------------------------------------------------PopupWindowUserId(User_Id)
function PopupWindowUserId(User_Id){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=User_Id,"+User_Id, false);
}
}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function UpdateGrid(r){
}

function DoAfterRefresh(){
	mygrid.selectRowById(SelectedRowId,false,true,true);
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
