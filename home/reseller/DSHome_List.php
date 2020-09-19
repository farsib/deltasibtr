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
			padding: 0px;
			overflow: hidden;
			background-color:lightsteelblue;
			background:url('../dsimgs/DSBackgroundAfter.jpg') repeat;
			background-position:0px -58px;
			# background:url('/dsimgs/BackgroundLogo.png'),linear-gradient(rgba(140,190,240,0.08),rgba(140,190,240,1));
        }
		div.gridbox .objbox{
			background:none;
		}
		table.dhtmlxLayoutPolyContainer_dhx_skyblue td.dhtmlxLayoutSinglePoly div.dhxcont_global_content_area{
			background:none;
		}
		div.gridbox_dhx_skyblue .odd_dhx_skyblue{
			background-color:rgba(200,200,200,0.4);
		}
		
		.SideBarStyle{
			float:left;
			width:100%;
			list-style-type:none;
			padding:0;
			margin:0;
			user-select: none;
		}
		.SideBarStyle li{
			float:left;
			width:100%;
			border-bottom:1px solid #c5e4ff;
		}
		
		.SideBarStyle li a{
			font-family:tahoma;
			font-size:11px;
			color:black;
			float:left;
			width:165px;
			text-decoration:none;
			padding:5px 0px 5px 15px;
			margin:5px 10px 5px 10px;
			border-radius:3px;
			-moz-transition: all 0.3s ease;
			-o-transition: all 0.3s ease;
			-webkit-transition: all 0.3s ease;
			transition: all 0.3s ease;
		}
		.SideBarStyle li a:hover{
			background:rgba(0,0,0,0.5);
			font-weight:bold;
			color:white;
		}
		.SideBarSelectedItemStyle{
			background:rgba(0,0,0,0.5);
			font-weight:bold;
			color:white !important;
		}
		tr td img{
			padding-top:2px;
		}
		.dhxform_obj_dhx_skyblue div.dhxform_txt_label2{
			margin:0;
		}
		.MyNotification{
			border-radius:100%;
			background-color:red;
			color:white;
			padding:1px 5px 2px 5px;
			font-weight:bold;
			font-size:10px;
			font-family:Calibri,tahoma;
		}
		.DateTimeStyle{
			position: absolute;
			bottom:30px;
			left:5px;
			width:190px;
			height:200px;
			text-align:center;
			user-select: none;
		}
		.DateTimeCoverStyle{
			width:100%;
			height:100%;
			position:absolute;
			left:0;
			top:0;
			border:1px solid rgba(45,45,45,0.8);
			z-index:20;
			background:rgba(45,45,45,0.5);
			display:none;
			border-radius:5px;
		}
		.ClockStyle{
			position: absolute;
			width:170px;
			height:170px;
			top:0;
			left:10px;
			cursor:pointer;
			opacity:0.5;
			z-index:10;
			-webkit-transition: opacity 0.5s ease-in-out;
			-moz-transition: opacity 0.5s ease-in-out;
			transition: opacity 0.5s ease-in-out;
		}
		.ChangeClockView{
			position:absolute;
			top:0;
			width:20px;
			height:170px;
			opacity:0.05;
		}
		.DateTextAnalogStyle{
			position: absolute;
			padding:1px 0 0 0;
			left:52px;
			width:66px;
			height:15px;
			bottom:40px;
			text-align:center;
			z-index:2;
			font-size:70%;
			border-radius:1px;
			border:1px solid rgba(10,10,10,0.5);
			background:rgba(55,55,55,0.1);
			box-shadow: inset 0 0 3px rgba(55,55,55,0.4);
			font-family:Calibri,tahoma,arial;
		}
		.DigitalClockStyle{
			font-family:Calibri,tahoma,arial;
			position:absolute;
			top:30px;
			left:15px;
			width:120px;
			height:70px;
			padding:10px;
			-moz-transition: all 0.3s ease-in-out ;
			-o-transition: all 0.3s ease-in-out ;
			-webkit-transition: all 0.3s ease-in-out ;
			transition: all 0.3s ease-in-out;
			border:1px dotted rgba(0,0,0,0.15);
			border-radius: 4px;
			background-color:rgba(30,70,255,0.0.05);
		}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
var SelectedRowId=0;
var ServerTimeDiff=0;
var Loading=false;
var ClockInterval_Id;
var ClockType="AnalogClock";
window.onload = function(){
	
	DataTitle="Home";
	DataName="DSHome_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	
	GColIds="Notification,Ticket_Id,TicketStatus,Priority,TicketTitle,RemainDays,s.ResellerName,r.ResellerName,CDT";
GColHeaders="<img src='/dsimgs/bell.png'/>,{#stat_count} ردیف,وضعيت تيكت,اولويت,موضوع,روزهای باقیمانده,ارسال کننده,دریافت کننده,زمان ثبت";

	ISFilter=true;
	FilterState=true;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1];
	
	GFooter="";
	
	GColInitWidths="37,100,100,70,200,100,140,140,140";
	GColAligns="center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server";
	ColSortIndex=7;
	SortDirection='desc';

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:550,
				center:true,
				modal:true,
				park :false
				};
	//=======Popup1 AddNote
	var Popup1;
	var PopupId1=['NewTicket'];//  popup Attach to Which Buttom of Toolbar

	//=======Form1 
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp;
				
	var Form1FieldHelpId=['TicketTitle','Priority','Receiver','DeadLine'];
	var Form1Str = [
	{type:"fieldset",width:480,label:"تیکت جدید",list:[
		{ type:"settings" , labelWidth:100, inputWidth:250,offsetLeft:10,offsetRight:10},
		{ type:"input", name:"TicketTitle", label:"<span style='padding-left: 27px;'>موضوع :</span>",maxLength:64, validate:"NotEmpty",required:true, inputWidth:320},
		{type:"block",offsetLeft:0,width:420,list:[
			{ type:"settings" ,position:"label-right", labelWidth:40},
			{type:"label",offsetLeft:0,position:"label-left", labelWidth:100,className:"dhxform_label",label:"<span style='margin:0;color:black;font-weight:normal;margin:0;padding-left: 5px'>اولویت :<span class='dhxform_item_required'>*</span></span>"},
			{type:"newcolumn"},
			{type: "radio", name: "Priority", value: "0", label: "<img src='/dsimgs/Priority0.png'/>",checked:true},
			{type:"newcolumn"},
			{type: "radio", name: "Priority", value: "1", label: "<img src='/dsimgs/Priority1.png'/>"},
			{type:"newcolumn"},
			{type: "radio", name: "Priority", value: "2", label: "<img src='/dsimgs/Priority2.png'/>"},
			{type:"newcolumn"},
			{type: "radio", name: "Priority", value: "3", label: "<img src='/dsimgs/Priority3.png'/>"},
		]},
		{type: "select", name:"Receiver_Id",required:true, label:"دریافت کننده :",connector: RenderFile+".php?"+un()+"&act=SelectReceiver",inputWidth:320},					
		{ type:"input" , name:"DeadLine", label:"<span style='padding-left: 8px;'>(مهلت (روز :</span>",maxLength:5, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", inputWidth:60,value:1},
		]},
		{type: "block",offsetLeft:130, width: 250, list:[
			{ type: "button",name:"Proceed",value: "برو",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];


		
	// Layout   ===================================================================
	FilterRowNumber=0;
	
	dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
	// DSLayoutInitial(dhxLayout);
	
	dhxLayout.setSkin(dhxLayout_main_skin);
	dhxLayout.dhxWins.setEffect("move", true);
	
	
	dhxLayout.cells("a").setText("صندوق");
	dhxLayout.cells("a").hideArrow();
	dhxLayout.cells("b").setText("");
	dhxLayout.cells("b").hideArrow();
	
	dhxLayout.cells("a").appendObject("SideBar");	
	
	dhxLayout.cells("a").setWidth(202);
	dhxLayout.cells("a").fixSize(true,true);
	
	// TopToolBar   ===================================================================
	ToolbarOfGridSideBar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGridSideBar);
		
	DSToolbarAddButton(ToolbarOfGridSideBar,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	DSToolbarAddButtonPopup(ToolbarOfGridSideBar,null,"NewTicket","تیکت جدید","Ticket_NewTicket");
	Popup1=DSInitialPopup(ToolbarOfGridSideBar,PopupId1,Popup1OnShow);
	Form1=DSInitialForm(Popup1,{},Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	
	
	
	
	ToolbarOfGrid = dhxLayout.cells("b").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	if(ISFilter==true){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"Filter","فیلتر: فعال","toolbarfilter",ToolbarOfGrid_OnFilterClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterAddRow","افزودن فیلتر جدید","toolbarfilteradd",ToolbarOfGrid_OnFilterAddClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterDeleteRow","حذف فیلتر","toolbarfilterDelete",ToolbarOfGrid_OnFilterDeleteClick);
		ToolbarOfGrid.addSeparator("sep2", null);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.disableItem("Filter");
	}	
	
	DSToolbarAddButton(ToolbarOfGrid,null,"Open","بازکردن","Ticket_Open",ToolbarOfGrid_OnOpenClick);
	DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف کردن","Ticket_Delete",ToolbarOfGrid_OnDeleteClick);
	// DSToolbarAddButton(ToolbarOfGrid,null,"Priority","Priority","Ticket_Priority",ToolbarOfGrid_OnToolbarClick);
	

	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("b").attachGrid();
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
	mygrid.attachEvent("onRowSelect", SetButton);
	
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		document.getElementById("Notification_f_0").style.display="none"
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}
	FilterRowNumber++;
	
	
	DoItem("Inbox");
	initClocks();
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
	
//FUNCTION========================================================================================================================
//================================================================================================================================

function Popup1OnShow(){
	Form1.unload();
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.enableLiveValidation(false);
	Form1.setFocusOnFirstActive();
}
function Form1OnButtonClick(name){
	if(name=="Close"){
		Popup1.hide();
	}
	else{
		if(DSFormValidate(Form1,Form1FieldHelpId)){
			dhxLayout.progressOn();
			Popup1.hide();
			var PostStr="";
			PostStr+="&TicketTitle="+Form1.getItemValue("TicketTitle");
			PostStr+="&Priority="+Form1.getItemValue("Priority");
			PostStr+="&Receiver_Id="+Form1.getItemValue("Receiver_Id");
			PostStr+="&DeadLine="+Form1.getItemValue("DeadLine");
			dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=AddTicket",PostStr,function (loader){
					dhxLayout.progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					var ResponseArray=response.split("~",2);
					
					if((response=='')||(response[0]=='~'))
						dhtmlx.alert("خطا، "+response.substring(1));
					else if(ResponseArray[0]=='OK') {
						PopupWindow(ResponseArray[1]);
						DoItem("Sent");
					}
					else alert(response);

				});
			
			// DSFormInsertRequest(Form1,RenderFile+".php?"+un()+"&act=AddTicket",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
		}
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
	if(FilterRowNumber>2)
		ToolbarOfGrid.disableItem("FilterAddRow");
	for(var i=0; i<FilterRowNumber;++i)
		document.getElementById("Notification_f_"+i).style.display="none";
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	
function OnFilterTextPressEnter(){
	if(FilterState)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterDeleteClick(){
	ToolbarOfGrid.enableItem("FilterAddRow");
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

function ToolbarOfGrid_OnOpenClick(){
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
		ok:"بلی",
		cancel:"خیر",
		text: "آیا از حذف مطمئن هستید؟",
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
						dhtmlx.message("با موفقیت حذف شد");
					}
					else alert(response);

				});
			}	
		
		}
});	
}


function PopupWindow(SelectedRowId){
	Popup1.hide();
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"WebService");
	popupWindow.setMinDimension(750,550);
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+SelectedRowId, false);
}


}//END window.onload
function initClocks(){
	ReSyncClock();
	ClockType=localStorage.getItem("ClockType");
	if(!ClockType)
		ClockType="AnalogClock";
	
	
	var canvasFace = document.getElementById("AnalogClockFace");
	var ctxFace = canvasFace.getContext("2d");
	ctxFace.clearRect(0, 0, canvasFace.width, canvasFace.height);
	var radius = canvasFace.height / 2;
	ctxFace.translate(radius, radius);
	AnalogClockFrame(ctxFace, radius);
	
	var canvasHands = document.getElementById("AnalogClockHands");
	var ctxHands = canvasHands.getContext("2d");
	var radius = canvasHands.height / 2;
	ctxHands.translate(radius, radius);
	ctxHands.font = radius*0.19 + "px arial";
	ctxHands.textBaseline="middle";
	ctxHands.textAlign="center";
	
	RunClock(ClockType);
}

function RunClock(CType){
	MyDate=new Date();
	MyDate.setTime(Date.now()-ServerTimeDiff);
	
	if(CType=="DigitalClock"){
		document.getElementById("DigitalClockContainer").style.display="block";
		document.getElementById("AnalogClockContainer").style.display="none";
		clearInterval(ClockInterval_Id);
		
		document.getElementById("DigitalClockBody").innerHTML=("0"+MyDate.getHours()).slice(-2)+":"+("0"+MyDate.getMinutes()).slice(-2)+":"+("0"+MyDate.getSeconds()).slice(-2);
		ClockInterval_Id=setInterval(function(){
			MyDate.setTime(Date.now()-ServerTimeDiff);
			document.getElementById("DigitalClockBody").innerHTML=("0"+MyDate.getHours()).slice(-2)+":"+("0"+MyDate.getMinutes()).slice(-2)+":"+("0"+MyDate.getSeconds()).slice(-2);
		},1000);
	}
	else{
		document.getElementById("DigitalClockContainer").style.display="none";
		document.getElementById("AnalogClockContainer").style.display="block";
		clearInterval(ClockInterval_Id);
		
		var canvasHands = document.getElementById("AnalogClockHands");
		var ctxHands = canvasHands.getContext("2d");
		var radius = canvasHands.height / 2;		
		
		drawAnalogTime(ctxHands, radius,MyDate);
		ClockInterval_Id=setInterval(function(){
			MyDate.setTime(Date.now()-ServerTimeDiff);
			drawAnalogTime(ctxHands, radius,MyDate);
		},1000);
	}
}

function drawAnalogTime(ctx, radius,now){
	
    var hour = now.getHours();
    var minute = now.getMinutes();
    var second = now.getSeconds();
	ctx.clearRect(-radius, -radius, 2*radius, 2*radius);
    
	ctx.fillStyle = "navy";
    //hour
	hour=hour%12;
        
    hour=(hour + (minute/60)+(second/360))*Math.PI/6;
    drawAnalogClockHands(ctx, hour, radius*0.6, radius*0.1,radius*0.04,"#000000");

    //minute
    minute=(minute+(second/60))*Math.PI/30;
    drawAnalogClockHands(ctx, minute, radius*0.9, radius*0.1,radius*0.04,"#333333");

    // second
    second=(second*Math.PI/30);
    drawAnalogClockHands(ctx, second, radius*0.9, radius*0.45,radius*0.01,"#FF0000");
	ctx.beginPath();
	ctx.arc(0, 0, radius*0.03, 0, 2*Math.PI);
	ctx.fillStyle = "#FF0000";
	ctx.fill();
}

function drawAnalogClockHands(ctx, pos, length, tail, width, color) {
    ctx.beginPath();
    ctx.strokeStyle=color;
    ctx.fillStyle=color;
    ctx.lineWidth = width;
    ctx.lineCap = "round";
    ctx.rotate(pos);
    ctx.moveTo(0,-length);
    ctx.lineTo(0, 0);
    ctx.lineTo(-1, tail);
    ctx.lineTo(1, tail);
    ctx.lineTo(0, 0);
    ctx.stroke();
    ctx.fill();
    ctx.rotate(-pos);

}

function AnalogClockFrame(ctx, radius) {
  var ang;
  var num;

  for(num = 1; num < 61; num++){
    ang = num * Math.PI / 30;
    ctx.rotate(ang);
    ctx.translate(0, -radius*0.85);
    ctx.rotate(-ang);
	if(num%5==0){
		ctx.font = radius*0.15 + "px arial";
		ctx.textBaseline="middle";
		ctx.textAlign="center";
		var Hour=Math.round(num/5);
		ctx.fillText(Hour.toString(), 0, 0);
	}
	else{
		ctx.font = radius*0.06 + "px arial";
		ctx.textBaseline="middle";
		ctx.textAlign="center";		
		ctx.fillText('.', 0, -radius*0.04);
	}
    ctx.rotate(ang);
    ctx.translate(0, radius*0.85);
    ctx.rotate(-ang);
  }
}

function SetButton(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId!=null){
		ToolbarOfGrid.enableItem("Open");
		if((LoginResellerName.toLowerCase()=='admin')||(mygrid.cells(SelectedRowId,mygrid.getColIndexById("TicketStatus")).getValue()=="Sent"))
			ToolbarOfGrid.showItem("Delete");
		else
			ToolbarOfGrid.hideItem("Delete");
	}
	else{
		ToolbarOfGrid.hideItem("Delete");
		ToolbarOfGrid.disableItem("Open");
	}
}
function SetNotification(){
	var TimeStamp=parseInt(mygrid.getUserData('',"TicketTitle"));
	var Receiver_NotficationCount=parseInt(mygrid.getUserData('',"Receiver_NotficationCount"));
	var Sender_NotficationCount=parseInt(mygrid.getUserData('',"Sender_NotficationCount"));

	var InboxLinkNotificationSup=document.getElementById("InboxLinkNotification");
	var SentLinkNotificationSup=document.getElementById("SentLinkNotification");
	var Home_Tab_TextSup=top.document.getElementById("Home_Tab_Text");

	var TotalNotificationCount=Receiver_NotficationCount+Sender_NotficationCount;
	
	
	if(Receiver_NotficationCount>0){
		InboxLinkNotificationSup.style.display='inline';
		InboxLinkNotificationSup.innerHTML=Receiver_NotficationCount;
	}
	else
		InboxLinkNotificationSup.style.display='none';
	
	if(Sender_NotficationCount>0){
		SentLinkNotificationSup.style.display='inline';
		SentLinkNotificationSup.innerHTML=Sender_NotficationCount;
	}
	else
		SentLinkNotificationSup.style.display='none';
	
	if(TotalNotificationCount>0){
		Home_Tab_TextSup.style.display='inline';
		Home_Tab_TextSup.innerHTML=TotalNotificationCount;
	}
	else
		Home_Tab_TextSup.style.display='none';	
}
//---------------------------------------------------------------------------------------------------------------------------------------------
function UpdateGrid(r){
	SelectedRowId=r;
	LoadGridDataFromServer(RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else	
		mygrid.selectRowById(SelectedRowId,false,true,true);
	SetButton();
	SetNotification();
}
function DoItem(item){
	ExtraFilter="&Req="+item;
	
	document.getElementById("InboxLink").className="";
	document.getElementById("SentLink").className="";
	document.getElementById("ArchiveLink").className="";
	document.getElementById(item+"Link").className="SideBarSelectedItemStyle";
	if(item=="Inbox"){
		dhxLayout.cells("b").setText("صندوق ورودی");
		mygrid.setColumnHidden(6,false);
		mygrid.setColumnHidden(7,true);
		mygrid.setColumnHidden(0,false);
		for(i=0;i<FilterRowNumber;++i){
			var t=document.getElementById("r.ResellerName_f_"+i);
			if(t)
				t.value="";
		}
	}
	else if(item=="Sent"){
		dhxLayout.cells("b").setText("صندوق ارسال");
		mygrid.setColumnHidden(6,true);
		mygrid.setColumnHidden(7,false);
		mygrid.setColumnHidden(0,false);
		for(i=0;i<FilterRowNumber;++i){
			var t=document.getElementById("s.ResellerName_f_"+i);
			if(t)
				t.value="";
		}
	}
	else{
		dhxLayout.cells("b").setText("بایگانی");
		mygrid.setColumnHidden(6,false);
		mygrid.setColumnHidden(7,false);
		mygrid.setColumnHidden(0,true);
	}
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function ReSyncClock(){
	DateTimeTextMouseLeave();
	if(Loading)
		return;
	Loading=true;
	document.getElementById("MyClock").style.opacity=0.1;
	document.getElementById("MyClock").style.filter = "blur(2px)";
	document.getElementById("WhoIs").innerHTML="...در حال بروزکردن";
	setTimeout(function(){
		document.getElementById("WhoIs").innerHTML="...در حال بروزکردن";
		setTimeout(function(){
				document.getElementById("WhoIs").innerHTML="...در حال بروزکردن";
				setTimeout(DoSync,500);
			},500);
	},500);	
	
	function DoSync(){
		var beforeload = (new Date()).getTime();
		// dhxLayout.progressOn();
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=RefreshClock",function (loader){
			Loading=false;
			// dhxLayout.progressOff();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			var ResponseArray=response.split("~",4);

			var WhoIsText="";
			if((response=='')||(ResponseArray[0]!='OK')){
				if(response[0]=="~")
					response=response.substring(1);
				dhtmlx.alert({text:response,type:"alert-error",title:"هشدار"});
				WhoIsText=response;
				document.getElementById("DateTextAnalog").innerHTML="-";
				document.getElementById("DateTextDigital").innerHTML="-";
			}
			else{			
				var afterload = (new Date()).getTime();
				var delay = afterload - beforeload;
				ServerTimeDiff=Date.now()-Math.round(parseFloat(ResponseArray[1])+delay/2);
				// document.getElementById("DateTextAnalog").innerHTML=ResponseArray[2];
				// document.getElementById("DateTextDigital").innerHTML=ResponseArray[2];
				setTimeout(function(){
					document.getElementById("MyClock").style.filter = "none";
					document.getElementById("MyClock").style.opacity=1;
				},500);
				WhoIsText="خوش آمدید <span style='color:green;font-weight:bold;font-family:Calibri,arial,tahoma;'>"+ResponseArray[3]+"</span>";
			}
			document.getElementById("WhoIs").innerHTML=WhoIsText;
		});
	}
}

function DateTimeTextMouseEnter() {
	if(Loading)
		return;
	document.getElementById("DateTimeCover").style.display="block";
	document.getElementById("MyClock").style.filter = "blur(2px)";
	document.getElementById("WhoIs").style.filter = "blur(2px)";
}

function DateTimeTextMouseLeave() {
    document.getElementById("DateTimeCover").style.display="none";
    document.getElementById("MyClock").style.filter = "none";
    document.getElementById("WhoIs").style.filter = "none";
}

function ChangeClockView(){
	DateTimeTextMouseLeave();
	if(ClockType=="AnalogClock")
		ClockType="DigitalClock";
	else
		ClockType="AnalogClock";
	if(typeof(Storage) !== "undefined")
		localStorage.setItem('ClockType', ClockType);
	RunClock(ClockType);
}
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
<div style="display:none" id="SideBar">
	<ul class="SideBarStyle">
		<li>
			<a id="InboxLink" href="javascript:void(0);" onclick="DoItem('Inbox')"><span style="width:40px;float:left;">ورودی</span><sup id="InboxLinkNotification" class="MyNotification" style="display:none"></sup></a>
			
		</li>
		<li>
			<a id="SentLink" href="javascript:void(0);" onclick="DoItem('Sent')"><span style="width:30px;float:left;">ارسال</span><sup id="SentLinkNotification" class="MyNotification" style="display:none"></sup></a>
			
		</li>
		<li>
			<a id="ArchiveLink" href="javascript:void(0);" onclick="DoItem('Archive')"><span style="width:42px;float:left;">بایگانی</span></a>
		</li>
	</ul>
	<div id="DateTimeText" class="DateTimeStyle" onmouseenter="DateTimeTextMouseEnter()" onmouseleave="DateTimeTextMouseLeave()">
		<div id="DateTimeCover" class="DateTimeCoverStyle">
			<div style="position:absolute;left:35px;top:85px;width:30px;heigth:30px;border:1px solid black;box-shadow:0 0 12px gray;font-size:24px;font-weight:bold;border-radius:3px;color:white;cursor:pointer;user-select: none;" onclick="ReSyncClock()"  title="برای همگام سازی مجدد کلیک کنید">
				&#x27f3;
			</div>
			<div style="position:absolute;right:35px;top:85px;width:30px;heigth:30px;border:1px solid black;box-shadow:0 0 12px gray;font-size:24px;font-weight:normal;border-radius:3px;color:white;cursor:pointer;user-select: none;" onclick="ChangeClockView()"  title="تغییر ظاهر ساعت">
				&#x21c4;
			</div>
		</div>
		<div id="MyClock" class="ClockStyle">
			<div id="AnalogClockContainer" style="display:none">
				<canvas id="AnalogClockHands" width="170" height="170" style="position: absolute; left: 0; top: 0; z-index: 0;"></canvas>
				<canvas id="AnalogClockFace" width="170" height="170" style="position: absolute; left: 0; top: 0; z-index: 1;"></canvas>
				<div id="DateTextAnalog" class="DateTextAnalogStyle">-</div>
			</div>
			<div id="DigitalClockContainer" style="display:none">
				<div class="DigitalClockStyle">
					<div id="DigitalClockBody" style="font-size:32px;"></div>
					<div id="DateTextDigital" style ="font-size:20px;" class="DateTextDigitalStyle">-</div>
				</div>
			</div>
		</div>
		<div id="WhoIs" style="position:absolute;bottom:0;width:100%;text-align:center;text-shadow:0 0 12px wheat;font-size:19px; z-index: 10;"></div>
	</div>
</div>
<script>
	var LastDate=localStorage.getItem('LS_CurrentDate');
	document.getElementById("DateTextAnalog").innerHTML=LastDate;
	document.getElementById("DateTextDigital").innerHTML=LastDate;
</script>
</body>
</html>
