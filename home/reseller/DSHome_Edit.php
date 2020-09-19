<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSHome_Edit ....................................................................................");
	PrintInputGetPost();	
	if($LastError!=""){
		DSDebug(0,"Session Expire");
		?>
		<html><head><script type="text/javascript">
			window.onload = function(){
			parent.dhxLayout.dhxWins.window("popupWindow").hide();
			parent.dhtmlx.alert("<?php echo escape($LastError) ?>");//"Session Expire, Please Relogin"
			parent.dhxLayout.dhxWins.window("popupWindow").close();
		}
		</script></head><body></body></html>
		<?php
			exit();
		}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; char=utf-8" />
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
			margin: =10px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background=color:white;
        }
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
var SelectedRowId=0;

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	DataTitle="Home";
	DataName="DSHome_";
	RenderFile=DataName+"EditRender";
	ExtraFilter="&Ticket_Id="+RowId;
	
	
	
	
	GColIds="TicketingAction_Id,ResellerName,CDT,Action,Comment,CanUndone";
	GColHeaders="{#stat_count} ردیف,ثبت کننده,زمان ثبت,عملیات,توضیح,بازگردانی عمل";

	ISFilter=false;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,0];
	
	GFooter="";
	
	GColInitWidths="100,100,140,100,170,100";
	GColAligns="center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='desc';
	
	
	//=======Popup1 AddNote
	var Popup1;
	var PopupId1=["Info","Abandon","Done","ReOpen","Cancel","TimeRequest","ExtendTime","Confirm"];//  popup Attach to Which Buttom of Toolbar

	//=======Form1 
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp;

	
	var Form1FieldHelpId=['Comment','Day'];
	var Form1Str = [
	{type:"fieldset",width:420,label:"عمل جدید",list:[
		{ type:"settings" , labelWidth:80},
		{ type:"hidden", name:"Ticket_Id", value:RowId},
		{ type:"input" , name:"Action", label:"عملیات :",required:true, labelAlign:"left", inputWidth:120,readonly:true},
		{ type:"input" , name:"Days", label:"Days :",maxLength:4,value:1, validate:"IsID",required:true, labelAlign:"left", inputWidth:120},
		{ type:"input" , name:"Comment", label:"توضیح :",maxLength:900,rows:5,value:"", labelAlign:"left", inputWidth:300},
		]},
		{type: "block",offsetLeft:130, width: 250, list:[
			{ type: "button",name:"Proceed",value: "ذخیره",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];

	//=======Popup2 File
	var Popup2;
	var PopupId2=["File"];//  popup Attach to Which Buttom of Toolbar

	//=======Form2 
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp;
				
	var Form2FieldHelpId=['Uploader'];
	var Form2Str = [
	{type:"fieldset",width:420,label:"عمل جدید",list:[
		{ type:"settings" , labelWidth:80},
		{type:"label"},
		{type: "upload",name: "Uploader",inputWidth: 310,titleScreen: true,autoStart: true,autoRemove:true,titleText :"کلیک کنید یا فایل را اینجا بکشید",url: RenderFile+".php?un="+un()+"&Ticket_Id="+RowId+"&act=AddFile"},
		]},
		{type: "block",offsetLeft:130, width: 250, list:[
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		];

	
	// Layout   ===================================================================
	dhxLayout = new dhtmlXLayoutObject(document.body, "3E");
	DSLayoutInitial(dhxLayout);
	dhxLayout.cells("b").hideHeader();
	dhxLayout.cells("c").hideHeader();
	
	dhxLayout.cells("a").attachHTMLString("<table style='font-family:tahoma;font-size:11px'><tr style='height:29px;'><td style='font-weight:bold;color:navy'>Ticket Info:  Loading...</td></tr></table>");
	dhxLayout.cells("a").setHeight(60);
	dhxLayout.cells("a").fixSize(true,true);
	dhxLayout.attachEvent("onPanelResizeFinish", function(name){
		if(name=='b,c'){
			var t=document.getElementById("dhxLayoutCellC");
			if(t){
				t.style.height=dhxLayout.cells("c").getHeight()-10;
				// alert(dhxLayout.cells("c").getHeight());
			}
		}
	});

	
	
	ToolbarOfGrid = dhxLayout.cells("b").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	ToolbarOfGrid.addSeparator("sep1",null);
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Info","متن پیام","Ticket_Info");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"File","پیوست","Ticket_AttachFile");
	ToolbarOfGrid.addSeparator("sep2",null);
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Abandon","Abandon Ticket","Ticket_Abandon");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Done","تغییر به انجام شده","Ticket_Done");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ReOpen","بازکردن مجدد تیکت","Ticket_ReOpen");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Cancel","Cancel Ticket","Ticket_Cancel");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"TimeRequest","Time Request","Ticket_TimeRequest");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"ExtendTime","افزایش مهلت","Ticket_ExtendTime");
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Confirm","بایگانی تیکت","Ticket_Confirm");
	ToolbarOfGrid.disableItem("Info");
	ToolbarOfGrid.disableItem("File");
	ToolbarOfGrid.hideItem("Abandon");
	ToolbarOfGrid.hideItem("Done");
	ToolbarOfGrid.hideItem("ReOpen");
	ToolbarOfGrid.hideItem("Cancel");
	ToolbarOfGrid.hideItem("TimeRequest");
	ToolbarOfGrid.hideItem("ExtendTime");
	ToolbarOfGrid.hideItem("Confirm");
	
	
	
	Popup1=DSInitialPopup(ToolbarOfGrid,PopupId1,Popup1OnShow);
	Form1=DSInitialForm(Popup1,{},Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,{},Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	
	ToolbarOfGrid.addSpacer("Confirm");
	DSToolbarAddButton(ToolbarOfGrid,null,"Undo","بازگردانی آخرین عمل","Ticket_Undo",ToolbarOfGrid_OnUndoClick);
	
	
	
	
	
	
	
	
	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("b").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);	
	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		// for (var i=0;i<FilterRowNumber;i++){
			// var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);		
			// if(input) input.style.width = cWidth-20;
		// }
		return true;
	});
	

    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	// mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);
	mygrid.attachEvent("onRowSelect", GridOnRowSelect);
	
	
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnInputChange(name,value){
	if(name=="Days"){
		Form1.setNote("Days",{text:"تاریخ جدید = "+ShDatePlus(mygrid.getUserData('',"DeadLineDate"),value)});
	}
	
}

function ShDatePlus(BaseDate,Plus){
	var DateParts=BaseDate.split("/");
	var y=parseInt(DateParts[0]);
	var m=parseInt(DateParts[1]);
	var d=parseInt(DateParts[2]);
	var P=parseInt(Plus);
	if(isNaN(P))
		return BaseDate;
	// var t="Old "+BaseDate+" plus "+P+" Days";
	while(P>=YearDays(y)){
		// t+="<br/><br/>P: "+P+" - YearDays of "+y+" : "+YearDays(y);
		P-=YearDays(y);
		y++;
		// t+="<br/>P: "+P+" - new: "+ y+"/"+(m<10?("0"+m):m)+"/"+(d<10?("0"+d):d);
	}
	while(P>=MonthDays(y,m)){
		// t+="<br/><br/>P: "+P+" - MonthDays of "+y+"/"+m+" : "+MonthDays(y,m);
		P-=MonthDays(y,m);
		m++;
		if(m>12){
			y++;
			m=1;
		}
		// t+="<br/>P: "+P+" - new: "+ y+"/"+(m<10?("0"+m):m)+"/"+(d<10?("0"+d):d);
	}
	// t+="<br/><br/>P :"+P;
	d+=P;
	
	// t+="<br/>P: 0 - new: "+ y+"/"+(m<10?("0"+m):m)+"/"+(d<10?("0"+d):d);
	
	
	if(d>MonthDays(y,m)){
		d-=MonthDays(y,m);
		m++;
	}
	if(m>12){
		m=1;
		y++;
	}
	// t+="<br/>Normalized: "+ y+"/"+(m<10?("0"+m):m)+"/"+(d<10?("0"+d):d);
	// top.dhtmlx.message({text:t,expire:120000});
	return y+"/"+(m<10?("0"+m):m)+"/"+(d<10?("0"+d):d);
}

function YearDays(Y){
	return 365+LeapYear(Y);
}

function MonthDays(Y,M){
	if(M<=6)
		return 31;
	else if(M<=11)
		return 30;
	else{
		return 29+LeapYear(Y);
	}
}

function LeapYear(Y){
	var modY=Y % 33;
	if((modY == 1)||(modY == 5)||(modY == 9)||(modY == 13)||(modY == 17)||(modY == 22)||(modY == 26)||(modY == 30))
		return 1;
	return 0;
}

function Popup1OnShow(name){
	Form1.unload();
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onInputChange",Form1OnInputChange);
	Form1.setItemValue("Action",name);
	Form1.enableLiveValidation(false);
	if((name=="TimeRequest")||(name=="ExtendTime")){
		Form1.setNote("Days",{text:"تاریخ جدید = "+ShDatePlus(mygrid.getUserData('',"DeadLineDate"),1)});
		Form1.showItem("Days");
		Form1.setItemText("Days","روز :");
		Form1.setItemFocus("Days");
	}
	else{
		Form1.hideItem("Days");
		Form1.setItemFocus("Comment");
	}
}
function Form1OnButtonClick(name){
	if(name=="Close"){
		Popup1.hide();
	}
	else{
		if(DSFormValidate(Form1,Form1FieldHelpId)){
			Popup1.hide();
			DSFormInsertRequestProgress(dhxLayout,Form1,RenderFile+".php?"+un()+"&act=insert",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
		}
	}	
}
function Popup2OnShow(){
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onUploadComplete",function(count){
		Popup2.hide();
	});
	Form2.attachEvent("onUploadFail",function(count){
		Popup2.hide();
	});
}
function Form2OnButtonClick(name){
	Popup2.hide();
}

function Form1DoAfterInsertOk(r){
	SelectedRowId=r;
	ToolbarOfGrid_OnRetrieveClick();
}
function Form1DoAfterInsertFail(){
	ToolbarOfGrid_OnRetrieveClick();
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
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}	
function OnFilterTextPressEnter(){
	if(FilterState)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function ToolbarOfGrid_OnUndoClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		ok:"بلی",
		cancel:"خیر",
		text: "از بازگردانی آخرین عمل مطمئن هستید؟",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=UndoLastAction"+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert({text:"خطا، "+response.substring(1),ok:"بستن"});
					else if(response=='OK~') {
						SelectedRowId=0;
						ToolbarOfGrid_OnRetrieveClick();
						dhtmlx.message("با موفقیت بازگردانی شد");
					}
					else alert(response);
				});
			}
		}
	});	
}

function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);		
}


function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}
}

function SetHeader(){
	var TicketTitle=mygrid.getUserData('',"TicketTitle");
	var TicketStatus=mygrid.getUserData('',"TicketStatus");
	var Sender=mygrid.getUserData('',"Sender");
	var Receiver=mygrid.getUserData('',"Receiver");
	var Priority=mygrid.getUserData('',"Priority");
	var CDT=mygrid.getUserData('',"CDT");
	var RemainDays=mygrid.getUserData('',"RemainDays");
	var DeadLineDate=mygrid.getUserData('',"DeadLineDate");
	var TicketDirection=((Receiver.toLowerCase()==LoginResellerName.toLowerCase())&&(Sender.toLowerCase()==LoginResellerName.toLowerCase()))?"Both":(Receiver.toLowerCase()==LoginResellerName.toLowerCase())?"In":"Out";

	parent.dhxLayout.dhxWins.window("popupWindow").setText(TicketTitle+" ["+TicketStatus+"]");
	var Days="";
	var DayColor="";
	if((TicketStatus=="Abandoned")||(TicketStatus=="Cancel")||(TicketStatus=="Done")){
		Days="";
		DayColor="steelblue";
	}
	else{
		if(RemainDays>=0){
			Days=" (<span style='font-weight:normal'>"+RemainDays+" روز"+(RemainDays==1?"":"")+" باقی مانده"+"</span>)";
			DayColor="forestgreen";
		}
		else{
			Days=" (<span style='font-weight:normal'>"+RemainDays+" day"+(RemainDays==1?"":"s")+" passed"+"</span>)";
			DayColor="indianred";
		}
	}
	var Str=
		"<table border='0' cellpadding='6' cellspacing='0' style='width:100%;font-family:tahoma;font-size:11px;table-layout:fixed'>"+
			"<tr style='height:29px;'>"+
				"<td rowspan='2' style='border-right:1px solid #c5e4ff;font-weight:bold;font-size:12px;color:navy;width:80px'>اطلاعات تیکت</td>"+
				"<td style='width:260px;border-bottom:1px solid #c5e4ff;overflow:hidden;white-space:nowrap;' colspan='2' title='"+TicketTitle+"'>عنوان: <span style='font-weight:bold;color:darkblue'>"+TicketTitle+"</span></td>"+
				"<td style='width:105px;border-bottom:1px solid #c5e4ff;'>اولویت: <img style='vertical-align:middle' src=\"/dsimgs/Priority"+Priority+".png\"/></td>"+
				"<td style='border-bottom:1px solid #c5e4ff;white-space: nowrap;'>مهلت: <span style='font-weight:bold;color:"+DayColor+"'>"+DeadLineDate+Days+" </span>"+"</td>"+
			"</tr>"+
			"<tr style='height:29px;'>"+
				"<td style='width:130px'>ارسال کننده: <span style='font-weight:bold;color:mediumslateblue'>"+Sender+"</span></td>"+
				"<td style='width:130px'>دریافت کننده: <span style='font-weight:bold;color:mediumslateblue'>"+Receiver+"</span></td>"+
				"<td>وضعیت: <span style='font-weight:bold;color:firebrick'>"+TicketStatus+"</span></td>"+
				"<td>زمان ثبت: <span style='font-weight:bold;'>"+CDT+"</span></td>"+
			"</tr>"+
		"</table>";
	dhxLayout.cells("a").attachHTMLString(Str);

	if(TicketDirection=="Out"){
		ToolbarOfGrid.hideItem("Abandon");
		ToolbarOfGrid.hideItem("Done");
		ToolbarOfGrid.hideItem("TimeRequest");
		if((TicketStatus=="Sent")||(TicketStatus=="Seen")||(TicketStatus=="Open")||(TicketStatus=="Expired")){
			ToolbarOfGrid.enableItem("Info");
			ToolbarOfGrid.enableItem("File");
			ToolbarOfGrid.hideItem("ReOpen");
			ToolbarOfGrid.showItem("Cancel");
			ToolbarOfGrid.showItem("ExtendTime");
			ToolbarOfGrid.hideItem("Confirm");
		}
		else if((TicketStatus=="Abandoned")||(TicketStatus=="Cancel")||(TicketStatus=="Done")){
			ToolbarOfGrid.enableItem("Info");
			ToolbarOfGrid.enableItem("File");
			ToolbarOfGrid.showItem("ReOpen");
			ToolbarOfGrid.hideItem("Cancel");
			ToolbarOfGrid.hideItem("ExtendTime");
			ToolbarOfGrid.showItem("Confirm");
			
		}
		else if(TicketStatus=='Confirmed'){
			ToolbarOfGrid.disableItem("Info");
			ToolbarOfGrid.disableItem("File");
			ToolbarOfGrid.hideItem("ReOpen");
			ToolbarOfGrid.hideItem("Cancel");
			ToolbarOfGrid.hideItem("ExtendTime");			
			ToolbarOfGrid.hideItem("Confirm");			
		}
	}
	else if(TicketDirection=="In"){
		ToolbarOfGrid.hideItem("Cancel");
		ToolbarOfGrid.hideItem("ExtendTime");
		ToolbarOfGrid.hideItem("Confirm");
		
		if((TicketStatus=="Open")||(TicketStatus=="Expired")){
			ToolbarOfGrid.enableItem("Info");
			ToolbarOfGrid.enableItem("File");
			ToolbarOfGrid.hideItem("ReOpen");
			ToolbarOfGrid.showItem("Abandon");
			ToolbarOfGrid.showItem("Done");
			ToolbarOfGrid.showItem("TimeRequest");
		}
		else if((TicketStatus=="Cancel")||(TicketStatus=='Confirmed')){
			ToolbarOfGrid.disableItem("Info");
			ToolbarOfGrid.disableItem("File");
			ToolbarOfGrid.hideItem("ReOpen");
			ToolbarOfGrid.hideItem("Abandon");
			ToolbarOfGrid.hideItem("Done");
			ToolbarOfGrid.hideItem("TimeRequest");
		}
		else if((TicketStatus=="Abandoned")||(TicketStatus=="Done")){
			ToolbarOfGrid.disableItem("Info");
			ToolbarOfGrid.disableItem("File");
			ToolbarOfGrid.showItem("ReOpen");
			ToolbarOfGrid.hideItem("Abandon");
			ToolbarOfGrid.hideItem("Done");
			ToolbarOfGrid.hideItem("TimeRequest");
		}		
	}
	else if(TicketDirection=="Both"){
		ToolbarOfGrid.hideItem("Abandon");
		ToolbarOfGrid.hideItem("Cancel");
		ToolbarOfGrid.hideItem("TimeRequest");
		if((TicketStatus=="Open")||(TicketStatus=="Expired")){
			ToolbarOfGrid.enableItem("Info");
			ToolbarOfGrid.enableItem("File");
			ToolbarOfGrid.hideItem("ReOpen");
			ToolbarOfGrid.showItem("Done");
			ToolbarOfGrid.showItem("ExtendTime");
			ToolbarOfGrid.hideItem("Confirm");
		}
		else if(TicketStatus=="Done"){
			ToolbarOfGrid.enableItem("Info");
			ToolbarOfGrid.enableItem("File");
			ToolbarOfGrid.showItem("ReOpen");
			ToolbarOfGrid.hideItem("Done");
			ToolbarOfGrid.hideItem("ExtendTime");		
			ToolbarOfGrid.showItem("Confirm");
		}
		else if(TicketStatus=='Confirmed'){
			ToolbarOfGrid.disableItem("Info");
			ToolbarOfGrid.disableItem("File");
			ToolbarOfGrid.hideItem("ReOpen");
			ToolbarOfGrid.hideItem("Done");
			ToolbarOfGrid.hideItem("ExtendTime");		
			ToolbarOfGrid.hideItem("Confirm");
		}
	}
	else
		alert("Error");
	
}
function UploadOK(r){
	dhtmlx.message("Your file has been successfully uploaded");
	SelectedRowId=r;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function GridOnRowSelect(rId,cId){
	SelectedRowId=rId;
	if(SelectedRowId){
		var Action=mygrid.cells(SelectedRowId,3).getValue();
		var Comment=mygrid.cells(SelectedRowId,4).getValue();
		Comment=Comment.replace(/(?:\r\n|\r|\n)/g, '<br />');
		
		var Height=dhxLayout.cells("c").getHeight()-10;
		
		if(Action!='File')
			dhxLayout.cells("c").attachHTMLString("<div id='dhxLayoutCellC' style='font-size:13px;overflow:auto;height:"+Height+";padding:5px;direction:"+GetTextDirection(Comment)+";font-family:tahoma;'>"+Comment+"</div>");
		else{
			var TicketingAction_Id=mygrid.cells(SelectedRowId,0).getValue();
			var DownloadButtonOnClick='window.open(RenderFile+".php?"+un()+"&act=DownloadFile&Id='+TicketingAction_Id+'");';
			var DownloadButton="<button type='button' onclick='"+DownloadButtonOnClick+"'>دانلود</button>";
			dhxLayout.cells("c").attachHTMLString("<div id='dhxLayoutCellC' style='font-size:13px;overflow:auto;height:"+Height+";padding:5px;direction:"+GetTextDirection(Comment)+";font-family:tahoma;'>"+Comment+" "+DownloadButton+"</div>");
		}
	}
	else
		dhxLayout.cells("c").attachHTMLString("");
}
function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else	
		mygrid.selectRowById(SelectedRowId,false,true,true);
	SetHeader();
	parent.UpdateGrid();
	GridOnRowSelect(mygrid.getSelectedRowId(),0);
}
function SetCellCHeigh(){
	var t=document.getElementById("dhxLayoutCellC");
	if(t)
		setTimeout(function(){t.style.height=dhxLayout.cells("c").getHeight()-10},500);
}
	
</script>

<title>Delta SIB Accounting</title>
</head>
<body onresize="SetCellCHeigh()">
</body>
</html>