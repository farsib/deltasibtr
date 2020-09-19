<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSBatchProcess_CancelService ....................................................................................");
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
		.ProgressFrame{
			text-align:center;
			width:672px;
			border:2px solid black;
			padding:2px;
			height:20px;
			margin: 1px auto 0;
			border-radius:5px 5px 5px 5px;
		}		
		.ProgressBody{
			width:0;
			height:100%;
			background-repeat:repeat-x;
			background-position:0 0;
			background-size:40px 20px;
			background-image:linear-gradient(-45deg,transparent,transparent 33%,rgba(0,0,0,.1) 33%,rgba(0,0,0,0.1) 66%,transparent 66%,transparent);
			border-radius:3px 3px 3px 3px;
			animation: ProgressShift 1s infinite linear;
		}
		@keyframes ProgressShift{
			from {background-position:0px 0px;}
			to {background-position:40px 0px;}
		}
		#ProgressText{
			font-weight:bold;
			padding:2px 0 0 0;
			color:black;
		}
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
window.onload = function(){
	
var Window_OnCloseEvId=parent.dhxLayout.dhxWins.window("popupWindow").attachEvent("onClose",function(){CancelBeforeStart(true)});
		
	var BatchProcess_Id="<?php  echo addslashes($_GET['BatchProcess_Id']);?>";
	if((BatchProcess_Id == "")||(BatchProcess_Id == 0) ) {alert("An error occured. Call support");return;}
	var BatchProcessName ="<?php echo DBSelectAsString("SELECT BatchProcessName from Hbatchprocess where BatchProcess_Id=".addslashes($_GET['BatchProcess_Id']))?>";
	var UserCount ="<?php  echo addslashes($_GET['UserCount']);?>";
	if((UserCount == "")||(UserCount == 0)) {alert("An error occured. Call support");return;}
	
	var From_Index=0;
	var MyTimer=0;
	var TopRightTextInterval_Id;
	var ProgressBarTimeout_Id;
	var ProgressBar=true;
	var ProgressBarView="%";
	var ProcessBarFailCount=0;
	var PauseState=true;
	
	var ProgressStep=(UserCount<1000)?500:((UserCount<4000)?1000:2000);
	var DataTitle="لغو سرویس";
	var DataName="DSBatchProcess_CancelService";
	var RenderFile=DataName+"Render";

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);
	TopToolbar.addText("TopRightText",1,"");
	TopToolbar.addSpacer("Exit");

	//=======Form1 GoToBatchProcess
	var Form1;
	var Form1Help;
	var Form1FieldHelp  = {	UserName:'UserName'};
	var Form1FieldHelpId=['UserName'];
	var Form1Str=[
		{type: "block", name: "B1", list:[
			{type: "fieldset",width:680,label:"عملیات", list:[
				{type:"block",list:[
					{type: "select", name: "Action", label: "نوع عملیات :", options:[
						{text: " -- لطفا انتخاب کنید -- ", value: 0,selected:true},
						{text: "شروع سرویس بعدی کاربر", value: "StartNextService"},
						{text: "لغو سرویس فعال کاربر", value: "CancelActiveService"},
						{text: "لغو سرویس رزرو کاربر", value: "CancelPendingService"},
						{text: "لغو سرویس فعال و رزرو کاربر", value: "CancelAllService"}				
					],inputWidth:300, labelWidth:80,required:true},
				]},
				{type:"label", name:"ActionInfo",label:"<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify'>لطفا نوع عملیات را مشخص نمایید</div>",labelWidth:615,labelHeight:48,offsetTop:10}
				
			]},
			{type: "fieldset",name:"ReturnBlock",disabled:true,width:680,label:"مبلغ برگشتی برای لغو سرویس فعال و رزرو", list:[
				{type:"block",list:[
					{ type:"input", name:"ReturnValue", label:"برگشت ",maxLength:14,value:'0',inputWidth:110, labelWidth:80},
					{type: "newcolumn"},
					{ type:"select" , name:"ReturnValueType",inputWidth:186, options:[
						{text: "از مبلغ پرداخت شده ٪", value: "PayPricePercent"},
						{text: "ریال", value: "PayPriceFix"}
					]}
				]},
				{type:"label", name:"ReturnValueInfo1",label:"<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify'>برگشت <span style='color:indianred'>'%0'</span> از <span style='font-weight:bold;color:indianred'>'مبلغ پرداخت شده بابت سرویس'</span> به کاربر و سپس سرویس لغو خواهد شد</div>",labelWidth:615,labelHeight:32,offsetTop:10,hidden:true},
				{type:"label", name:"ReturnValueInfo2",label:"",labelWidth:615,labelHeight:32,offsetTop:10}
			]}
		]},
		{type:"block", name:"B2",list:[
			{type: "fieldset",width:680,label:" زمان بروز شدن پیشرفت ", list:[
				{type: "label", labelWidth:100, labelAlign:"right", label:"بروزرسانی:"},
				{type: "newcolumn", offset:2},
				{type:"block", name:"B2_1",list:[
					{type: "button", name:"ProgressStepDecrease", value: "-", width :35},
					{type: "newcolumn", offset:1},
					{type: "button", name:"ProgressStep", value: "هر "+(ProgressStep/1000)+" ثانیه", width :120,disabled:true},
					{type: "newcolumn", offset:1},
					{type: "button", name:"ProgressStepIncrease", value: "+", width :35}
				]},
				{type: "newcolumn", offset:90},
				{type: "label", labelWidth:120, labelAlign:"right", label:"نمایش پیشرفت:"},
				{type: "newcolumn", offset:22},
				{type: "button", name:"ChangeProgressView", value: "%", width :35},
			]}
		],hidden:true},		
		{type:"label"},
		{type: "block", name: "B3", list:[
			{type: "newcolumn", offset:50},
			{type: "button", name:"CancelBeforeStart", value: " لغو ", width :100},
			{type: "newcolumn", offset:190},
			{type: "button", name:"DoBatch", value: " لطفا نوع عملیات را مشخص نمایید ", disabled: true, width :280}
		]},
		{type: "block", name: "B4", hidden: true, list:[
			{type: "newcolumn", offset:40},
			{type: "button", name:"Advanced", value: " پیشرفته ", width :120},
			{type: "newcolumn", offset:240},
			{type: "button", name:"Cancel", value: " لغو ", width :100},
			{type: "newcolumn", offset:10},
			{type: "button", name:"Pause", value: " وقفه ", width :100},
			{type: "button", name:"Resume", value: " ادامه ", hidden: true, width :100}
		]},
		{type: "block", name: "B5", hidden: true, list:[
			{type: "newcolumn", offset:404},
			{type: "button", name:"Close", value: " بستن ", width :100},			
			{type: "newcolumn", offset:10},
			{type: "button", name:"SaveLog", value: " ذخیره گزارش ", width :100}
		]},
		{type: "label"},
		{type: "block", name: "B6", hidden: true, list:[
			{type: "label" , name:"MyProgressBar",label:'',labelHeight:20}
			
		]},		
		{type: "label"},
		{type: "block", name: "B7",width:700, list:[
			{type: "label", name:"Legend", label: "",labelHeight:1}
		]},
		{type: "block",name: "B8", list:[
			{type: "label" ,label:"<div style='position:fixed;bottom:10px;border-top:2px dotted darkgray;font-size:15;color:red;background-color:white;padding: 0 131px 0 100;'>تا پایان عملیات گروهی از ورود مجدد به پنل مدیریت خودداری کنید</div>",labelHeight:20}
		]}
	];
	
	
	parent.dhxLayout.dhxWins.window("popupWindow").setText("عملیات گروهی [ "+BatchProcessName+" ]");
	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1Help,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.enableLiveValidation(false);
	Form1.attachEvent("onBeforeChange",Form1OnBeforeChange);

	Form1.getInput("ReturnValue").onkeypress = Form1OnKeyPress;
	Form1.getInput("ReturnValue").onkeyup = Form1OnKeyUp;
	
	parent.document.oncontextmenu=document.oncontextmenu = function(){return false};
	parent.document.ondrop = document.ondrop = function(){return false};
	document.onselectstart= function(){return false};
		
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
//FUNCTION========================================================================================================================

//-------------------------------------------------------------------Form1OnKeyPress(event)
function Form1OnKeyPress(event){
	var x = event.which || event.keyCode || event.charCode ;
	//alert(x);
	 if (
			[46, 9, 27, 13].indexOf(x) !== -1 ||// delete,backspace,  tab, escape, enter and .
            (x == 65 && event.ctrlKey === true) ||// Ctrl+A
            (x == 67 && event.ctrlKey === true) ||// Ctrl+C
            (x == 88 && event.ctrlKey === true) ||// Ctrl+X         
            (x >= 35 && x <= 39)// home, end, left, right
		) return;

	if ((event.shiftKey || (x < 48 || x > 57)))
		event.preventDefault(); // Ensure that it is a number and stop the keypress
}

//-------------------------------------------------------------------Form1OnKeyUp(event)
function Form1OnKeyUp(event){
	
	var ReturnValueFlt=parseFloat(Form1.getItemValue("ReturnValue"));
	
	if((isNaN(ReturnValueFlt))||(ReturnValueFlt<0))
		ReturnValueFlt=0;
	
	if(Form1.getItemValue("ReturnValueType")=="PayPricePercent")
		ReturnValueFlt=Math.round(ReturnValueFlt*100)/100;
	else
		ReturnValueFlt=Math.round(ReturnValueFlt);
	
	ReturnType=Form1.getItemValue("ReturnValueType");
	if(ReturnType=='PayPricePercent'){
		if(ReturnValueFlt>100)
			Form1.setItemLabel("ReturnValueInfo1","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify'>برگشت <span style='font-weight:bold;color:red'>'%100'</span> از <span style='font-weight:bold;color:indianred'>'مبلغ پرداخت شده بابت سرویس'</span> به کاربر و سپس سرویس لغو خواهد شد</div>");
		else
			Form1.setItemLabel("ReturnValueInfo1","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify'>برگشت <span style='color:indianred'>'%"+ReturnValueFlt+"'</span> از <span style='font-weight:bold;color:indianred'>'مبلغ پرداخت شده بابت سرویس'</span> به کاربر و سپس سرویس لغو خواهد شد</div>");
	}
	else if(ReturnType=='PayPriceFix'){
		Form1.setItemLabel("ReturnValueInfo1","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify'>برگشت <span style='font-weight:bold;color:indianred'>کمترین </span> بین <span style='color:indianred'>'"+ReturnValueFlt+" ریال'</span> و <span style='color:indianred'>'مبلغ پرداخت شده بابت سرویس'</span> به کاربر و سپس سرویس لغو خواهد شد</div>");
	}
}

//-------------------------------------------------------------------Form1OnBeforeChange(id,value,newvalue)
function Form1OnBeforeChange(id,value,newvalue){
	//alert("id:'"+id+"'     value:'"+value+"'     newvalue:'"+newvalue+"'");
	if(id=='Action'){
		if(newvalue==0){
			Form1.disableItem('DoBatch');
			Form1.setItemLabel("ActionInfo","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify'>Please select action</div>");
			Form1.setItemLabel("DoBatch", " Please select what to do ");
			Form1.disableItem("ReturnBlock");
			Form1.hideItem("ReturnValueInfo1");
			Form1.showItem("ReturnValueInfo2");
			return true;
		}
		dhxLayout.cells("a").progressOn();
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetUserCount&Action="+newvalue+"&BatchProcess_Id="+BatchProcess_Id,function(loader){
			dhxLayout.cells("a").progressOff();
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			var ResArray=response.split("~",4);
			if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
			else if(ResArray[0]!="OK")
				dhtmlx.alert("خطا، "+response);
			else{
				tmp="<span style='font-size:80%'> ("+ResArray[3]+" کاربر دارای وضعیت 'غیرفعال' نیست)</span>";
				if(newvalue=='StartNextService'){
					Form1.enableItem('DoBatch');
					Form1.setItemLabel("ActionInfo","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify;direction:rtl'>در صورتیکه کاربر سرویس بعدی داشته باشد،فعال خواهد شد.اگر کاربر سرویس فعال و رزرو نداشته باشد<span style='font-weight:bold'>اتفاقی رخ نمی دهد</span><br/>"+ResArray[1]+" کاربر سرویس فعال دارند و "+ResArray[2]+" کاربر حداقل یک سرویس رزرو دارند"+tmp+".</div>");
					Form1.setItemLabel("DoBatch", " شروع سرویس بعدی برای "+UserCount+" کاربر ");
					Form1.disableItem("ReturnBlock");
					Form1.hideItem("ReturnValueInfo1");
					Form1.showItem("ReturnValueInfo2");
				}
				else if(newvalue=='CancelActiveService'){
					Form1.enableItem('DoBatch');
					Form1.setItemLabel("ActionInfo","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify;direction:rtl'>اگر کاربر سرویس رزرو داشته باشد و وضعیت کاربر 'غیرفعال' نباشد <span style='font-weight:bold'>سرویس رزرو فعال خواهد شد</span><br/>"+ResArray[1]+" کاربر سرویس فعال دارند"+tmp+".</div>");
					Form1.setItemLabel("DoBatch", " لغو سرویس فعال برای "+UserCount+" کاربر ");
					Form1.enableItem("ReturnBlock");
					Form1.showItem("ReturnValueInfo1");
					Form1.hideItem("ReturnValueInfo2");
				}
				else if(newvalue=='CancelPendingService'){
					Form1.enableItem('DoBatch');
					Form1.setItemLabel("ActionInfo","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify;direction:rtl'>لغو همه سرویس های رزرو کاربر <span style='font-weight:bold'>در صورت وجود</span>.<br/>"+ResArray[1]+" کاربر دارای "+ResArray[2]+" سرویس رزرو است"+tmp+".</div>");
					Form1.setItemLabel("DoBatch", " لغو سرویس رزرو برای "+UserCount+" کاربر ");
					Form1.enableItem("ReturnBlock");
					Form1.showItem("ReturnValueInfo1");
					Form1.hideItem("ReturnValueInfo2");
				}
				else if(newvalue=='CancelAllService'){
					Form1.enableItem('DoBatch');
					Form1.setItemLabel("ActionInfo","<div style='border-top:1px dotted gray;color:slategray;font-weight:normal;text-align:justify;direction:rtl'>لغو <span style='font-weight:bold'> همه سرویس های فعال و رزرو</span> کاربر.<br/>کاربران فیلتر شده دارای "+ResArray[1]+" سرویس فعال و "+ResArray[2]+" سرویس رزرو می باشند"+tmp+".</div>");
					Form1.setItemLabel("DoBatch", " لغو سرویس فعال و رزرو برای "+UserCount+" کاربر ");
					Form1.enableItem("ReturnBlock");
					Form1.showItem("ReturnValueInfo1");
					Form1.hideItem("ReturnValueInfo2");
				}
			}
		});
	}
	else if(id=='ReturnValueType')
		Form1OnKeyUp(true);
	else if(id=="ReturnValue")
		if((newvalue!='') && (Math.abs(parseFloat(newvalue))!=newvalue))
			return false;
	return true;
}

//-------------------------------------------------------------------TopToolbar_OnExitClick()
function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}	

//-------------------------------------------------------------------Form1OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='Cancel') {
		parent.dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-error",
			ok: "بلی",
			cancel: "خیر",
			text: "توقف عملیات برای "+(UserCount-From_Index)+" کاربر باقی مانده<br/>آیا مطمئن هستید؟",
			callback: function(Result){
				if(Result)
					setTimeout(CancelBatchProcess,300);
			}
		});
	}
	else if(name=='CancelBeforeStart')
		CancelBeforeStart(false);
	else if(name=='Advanced'){
		// Form1.disableItem("Advanced");
		if(Form1.isItemHidden("B2"))
			Form1.showItem("B2");
		else
			Form1.hideItem("B2");
	}
	else if(name=='ProgressStepIncrease'){
		document.getElementById("ProgressText").innerHTML = "...درحال تغییر";
		Form1.enableItem("ProgressStepDecrease");
		if(ProgressStep<1000){
			ProgressStep+=500;
			Form1.setItemLabel("ProgressStep","هر "+(ProgressStep/1000)+" ثانیه");
		}
		else if(ProgressStep<4000){
			ProgressStep+=1000;
			Form1.setItemLabel("ProgressStep","هر "+(ProgressStep/1000)+" ثانیه");
		}
		else if(ProgressStep<10000){
			ProgressStep+=2000;
			Form1.setItemLabel("ProgressStep","هر "+(ProgressStep/1000)+" ثانیه");
		}
		else{
			ProgressStep++;
			clearTimeout(ProgressBarTimeout_Id);
			ProgressBar=false;
			SetProgressBarManually();
			Form1.disableItem("ProgressStepIncrease");
			Form1.enableItem("ProgressStep");
			Form1.setItemLabel("ProgressStep","بروزرسانی با کلیک");
		}
	}
	else if(name=='ProgressStepDecrease'){
		document.getElementById("ProgressText").innerHTML = "...درحال تغییر";
		if(ProgressStep>10000){
			ProgressStep=10000;
			Form1.enableItem("ProgressStepIncrease");
			Form1.disableItem("ProgressStep");
			ProgressBar=true;
			SetProgressBarPercentage();
		}
		else if(ProgressStep>4000)
			ProgressStep-=2000;
		else if(ProgressStep>1000)
			ProgressStep-=1000;
		else{
			ProgressStep-=500;
			Form1.disableItem("ProgressStepDecrease");
		}
		Form1.setItemLabel("ProgressStep","هر "+(ProgressStep/1000)+" ثانیه");
	}
	else if(name=="ProgressStep")
		SetProgressBarManually();
	else if(name=="ChangeProgressView"){
		ProgressBarView=(ProgressBarView=="%")?"/":"%";
		Form1.setItemLabel("ChangeProgressView", ProgressBarView);		
	}
	else if(name=='Pause')
		PauseBatchProcess();
	else if(name=='Resume'){
		Form1.hideItem('Resume');		
		Form1.showItem('Pause');
		setTimeout(DoBatchProcess(),500);
	}
	else if(name=='DoBatch'){
		Form1.updateValues();
		Form1.disableItem('DoBatch');
		parent.dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-warning",
			ok: "بلی",
			cancel: "خیر",						
			text: "از آغاز عملیات مطمئن هستید؟",
			callback: function(Result){
				if(Result){
					Form1.disableItem('B3');
					Form1.showItem("B6");
					Form1.setItemLabel("MyProgressBar",'<div class="ProgressFrame"><div id="ProgressBar" class="ProgressBody"><div id="ProgressText"></div></div></div>');
					SetProgressBarCustom("...مقداردهی اولیه عملیات گروهی",UserCount,"#00ced1","running");
					
					// dhxLayout.progressOn();
					Form1.send(RenderFile+".php?"+un()+"&act=InititalizeBatchProcess&BatchProcess_Id="+BatchProcess_Id,"post",function(loader, response){
						// dhxLayout.progressOff();
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						if((response=='')||(response[0]=='~')){
							parent.dhtmlx.alert("خطا، "+response.substring(1));
							SetProgressBarCustom(response.substr(1,70)+"...",UserCount,"#ff4040","paused");
							Form1.enableItem('DoBatch');
							Form1.enableItem('B3');
						}
						else{
							Form1.disableItem('B1');
							Form1.hideItem('B3');
							Form1.showItem('B4');
							setTimeout(DoBatchProcess(),300);
							parent.dhxLayout.dhxWins.window("popupWindow").detachEvent(Window_OnCloseEvId);
							Window_OnCloseEvId=parent.dhxLayout.dhxWins.window("popupWindow").attachEvent("onClose",
								function(){
									if(Form1.isItemEnabled("Cancel")&&!Form1.isItemHidden("Cancel"))
										Form1.setItemFocus("Cancel");
									else if(Form1.isItemEnabled("Pause")&&!Form1.isItemHidden("Pause"))
										Form1.setItemFocus("Pause");
									parent.parent.dhtmlx.message({text:"این پنجره را نمی توان بست<br/>عملیات گروهی در حال اجراست", type:"error",expire:3000});
									return false;
								});			
						}
					});				
				}
				else
					Form1.enableItem('DoBatch');
			}
		});
	}
	else if(name=='Close'){
		TopToolbar_OnExitClick();
	}
	else if(name=='SaveLog'){
		if(!ISValidResellerSession()) return;
		Form1.disableItem("SaveLog");
		window.location=RenderFile+".php?act=SaveLog&BatchProcess_Id="+BatchProcess_Id;
		setTimeout(function(){Form1.enableItem("SaveLog")},1000);
	}
	else
		dhtmlx.alert("Unhandled button");
}

function PauseBatchProcess(){
	// dhxLayout.progressOn();
	var l=dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=PauseInProgress&BatchProcess_Id="+BatchProcess_Id);
	// dhxLayout.progressOff();
	
	response=l.xmlDoc.responseText;
	response=CleanError(response);
	
	ResArray=response.split("~");
	if((response=='')||(response[0]=='~'))
		dhtmlx.alert("خطا، "+response.substring(1));
	else if(ResArray[0]!='OK')
		dhtmlx.alert("خطا، "+response);
	else{
		Form1.hideItem('Pause');
		Form1.showItem('Resume');
		Form1.disableItem('Resume');
		Form1.disableItem("B2");
		Form1.disableItem("Advanced");
		PauseState=true;
		SetProgressBarCustom("...درحال صبر",UserCount-ResArray[1],"#f5ba25","running");
		parent.dhtmlx.message("تعداد "+ResArray[1]+" مورد متوقف شد");
	}
}

function CancelBatchProcess(){
	// dhxLayout.progressOn();
	var l=dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=CancelInProgress&BatchProcess_Id="+BatchProcess_Id);
	// dhxLayout.progressOff();
	response=l.xmlDoc.responseText;
	response=CleanError(response);
	ResArray=response.split("~");
	if((response=='')||(response[0]=='~')){
		dhtmlx.alert("خطا، "+response.substring(1));
		return;
	}
	else if(ResArray[0]!='OK'){
		dhtmlx.alert("خطا، "+response);
		return;
	}
	else
		parent.dhtmlx.alert({text:"تعداد "+ResArray[1]+" مورد لغو شد",ok:"بستن"});
	Form1.disableItem("B2");
	Form1.hideItem('B4');
	Form1.showItem('B5');
	Form1.hideItem("B7");
	Form1.hideItem("B8");
	parent.DetachOnCloseTab();
	parent.dhxLayout.dhxWins.window("popupWindow").detachEvent(Window_OnCloseEvId);
}

function DoBatchProcess(){
	parent.dhtmlx.message("عملیات گروهی شروع شد از "+(From_Index+1));
	ProgressBarFailCount=0;
	Form1.disableItem('Cancel');
	Form1.enableItem("B2");
	Form1.enableItem("Advanced");
	PauseState=false;
	SetTopRightTimer();

	SetProgressBarPercentage();
	
	if(ProgressBar)
		SetProgressBarCustom("",From_Index,"#008000"/*0064ff"*/,"running");
	else
		SetProgressBarManually();
	
	dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=StartBatchProcess&BatchProcess_Id="+BatchProcess_Id,function(loader){
		response=loader.xmlDoc.responseText;
		response=CleanError(response);
		PauseState=true;
		Form1.disableItem('Resume');
		Form1.disableItem("B2");
		Form1.disableItem("Advanced");
		
		clearInterval(TopRightTextInterval_Id);
		var ResArray=response.split("~");
		if((response=='')||(response[0]=='~')){
			Form1.hideItem('Pause');
			Form1.showItem('Resume');			
			SetProgressBarCustom(response.substr(1,70)+"...",UserCount,"#ff4040","paused");
			parent.dhtmlx.alert({text:"خطا، "+response.substring(1),type:"alert-error"});
		}
		else if(ResArray[0]=='OK'){
			parent.dhtmlx.alert({text:ResArray[1]+"<br/>مجموع زمان سپری شده:"+GetTimerString(MyTimer),ok:"بستن"});
			parent.DetachOnCloseTab();
			parent.dhxLayout.dhxWins.window("popupWindow").detachEvent(Window_OnCloseEvId);
			Form1.disableItem("B2");
			Form1.hideItem('B4');
			Form1.showItem('B5');
			if(ProgressBar)
				SetProgressBarCustom("",UserCount,"#008000"/*0064ff"*/,"paused");
			else
				SetProgressBarCustom("BatchProcess successfully finished.",UserCount,"#008000"/*0064ff"*/,"paused");
			ClearTopRightText();
		}
		else{
			From_Index=parseInt(ResArray[0]);
			parent.dhtmlx.message("تعداد "+ResArray[0]+" مورد انجام شد");
			SetProgressBarCustom("",ResArray[0],"#f5ba25","paused");
		}
		setTimeout(function(){
			Form1.enableItem('Resume');
			Form1.enableItem('Cancel');
		},500);
	});
}

//-------------------------------------------------------------------GetTimerString()
function GetTimerString(FTime){
	var decseconds = Math.floor(FTime % 10 );
	var seconds = Math.floor((FTime /10)% 60 );
	var minutes = Math.floor((FTime/600) % 60 );
	var hours = Math.floor((FTime/(36000)) % 24 );
	var days = Math.floor(FTime/864000 );
	
	return (days>0?(days+"d "):"")+(hours<10?"0"+hours:hours)+":"+(minutes<10?"0"+minutes:minutes)+":"+(seconds<10?"0"+seconds:seconds)+":"+decseconds;
}

function SetTopRightTimer(){
	clearInterval(TopRightTextInterval_Id);
	TopToolbar.setItemText("TopRightText","زمان تقریبی سپری شده : <span style='color:darkblue;font-weight:bold'>"+GetTimerString(MyTimer)+"</span>");
	TopRightTextInterval_Id = setInterval(function(){
		MyTimer+=1;
		TopToolbar.setItemText("TopRightText","زمان تقریبی سپری شده : <span style='color:darkblue;font-weight:bold'>"+GetTimerString(MyTimer)+"</span>");
	  },100);
}

function ClearTopRightText(){
	clearInterval(TopRightTextInterval_Id);
	TopToolbar.setItemText("TopRightText","");
	Form1.hideItem("B7");
	Form1.hideItem("B8");
}

function SetProgressBarPercentage(){
	if((!PauseState)&&(ProgressBar))
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetProgress&BatchProcess_Id="+BatchProcess_Id,function(loader){
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			if((response=='')||(response[0]=='~')){
				ProgressBarFailCount++;
				dhtmlx.message({type:"error",text:"Error in get progress,<br/>"+response.substring(1), expire:8000});
				if((!PauseState)&&(ProgressBar)){
					document.getElementById("ProgressBar").style.width = "100%";
					document.getElementById("ProgressText").innerHTML = "BatchProcess is running on server, but error in get progress(Fail "+ProgressBarFailCount+" of 3).";
					if(ProgressBarFailCount>=3){
						document.getElementById("ProgressBar").style.backgroundColor="#ff4040";
						ProgressStep=10000;
						Form1OnButtonClick("ProgressStepIncrease");
					}
					else
						document.getElementById("ProgressBar").style.backgroundColor="#ff8383";
				}
			}
			else if((!PauseState)&&(ProgressBar)){
					ProgressPercent=Math.round(10000*parseInt(response)/UserCount)/100;
					document.getElementById("ProgressBar").style.width = ProgressPercent+"%";
					document.getElementById("ProgressText").innerHTML = (ProgressBarView=="%")?(ProgressPercent+"%"):(response+"/"+UserCount);
					document.getElementById("ProgressBar").style.backgroundColor="#008000"/*0064ff"*/;
				}
			ProgressBarTimeout_Id=setTimeout(function(){SetProgressBarPercentage()},ProgressStep);
		});
}

function SetProgressBarCustom(PinnerHTML,DoneCount,PbackgroundColor,PanimationPlayState){
	clearTimeout(ProgressBarTimeout_Id);
	document.getElementById("ProgressBar").style.backgroundImage =(PanimationPlayState=="paused")?("linear-gradient(-90deg,transparent,transparent 25%,rgba(0,0,0,.1) 25%,rgba(0,0,0,0.1) 75%,transparent 75%,transparent)"):("linear-gradient(-45deg,transparent,transparent 33%,rgba(0,0,0,.1) 33%,rgba(0,0,0,0.1) 66%,transparent 66%,transparent)");	
	document.getElementById("ProgressBar").style.animationPlayState=PanimationPlayState;
	
	Pwidth=(Math.round(10000*DoneCount/UserCount)/100)+"%";
	document.getElementById("ProgressBar").style.width = Pwidth;
	if(PinnerHTML != '')
		document.getElementById("ProgressText").innerHTML = PinnerHTML;
	else
		document.getElementById("ProgressText").innerHTML = ((ProgressBarView=="%")?Pwidth:(DoneCount+"/"+UserCount));
	
	document.getElementById("ProgressBar").style.backgroundColor=PbackgroundColor;
}

function SetProgressBarManually(){
	if(!PauseState){
		Form1.disableItem("B2_1");
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetProgress&BatchProcess_Id="+BatchProcess_Id,function(loader){
			Form1.enableItem("B2_1");
			response=loader.xmlDoc.responseText;
			response=CleanError(response);
			if((response=='')||(response[0]=='~')){
				if(!PauseState){
					document.getElementById("ProgressBar").style.animationPlayState="running";
					document.getElementById("ProgressBar").style.backgroundImage ="linear-gradient(-45deg,transparent,transparent 33%,rgba(0,0,0,.1) 33%,rgba(0,0,0,0.1) 66%,transparent 66%,transparent)";
					document.getElementById("ProgressBar").style.width = "100%";
					document.getElementById("ProgressText").innerHTML = "BatchProcess is running on server, but error in get progress(Fail "+ProgressBarFailCount+" of 3).";
					document.getElementById("ProgressBar").style.backgroundColor="#ff4040";
				}
			}
			else if(!PauseState){
					ProgressPercent=Math.round(10000*parseInt(response)/UserCount)/100;
					document.getElementById("ProgressBar").style.animationPlayState="running";
					document.getElementById("ProgressBar").style.backgroundImage ="linear-gradient(-45deg,transparent,transparent 33%,rgba(0,0,0,.1) 33%,rgba(0,0,0,0.1) 66%,transparent 66%,transparent)";
					document.getElementById("ProgressBar").style.width = ProgressPercent+"%";
					document.getElementById("ProgressText").innerHTML = (ProgressBarView=="%")?(ProgressPercent+"%"):(response+"/"+UserCount);
					document.getElementById("ProgressBar").style.backgroundColor="#808080";
				}
		});
	}
}

function CancelBeforeStart(CloseRequest){
	Form1.disableItem("CancelBeforeStart");
	parent.dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-error",
		ok: "بلی",
		cancel: "خیر",
		text: "از لغو کردن عملیات گروهی مطمئن هستید؟",
		callback: function(Result){
			if(Result){
				dhxLayout.progressOn();
				var loader=dhtmlxAjax.getSync("DSBatchProcess_ListRender.php?"+un()+"&act=CancelBeforeStart&BatchProcess_Id="+BatchProcess_Id);
				dhxLayout.progressOff();
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				ResArray=response.split("~");
				if((response=='')||(response[0]=='~')){
					dhtmlx.alert("خطا، "+response.substring(1))
					Form1.enableItem("CancelBeforeStart");
				}
				else if(ResArray[0]!='OK'){
					dhtmlx.alert("خطا، "+response);
					Form1.enableItem("CancelBeforeStart");
				}
				else{
					Form1.disableItem('B1');
					Form1.disableItem("B2");
					Form1.hideItem('B3');
					Form1.showItem('B5');
					Form1.disableItem('SaveLog');
					Form1.hideItem('B7');
					Form1.hideItem('B8');
					parent.dhtmlx.alert({text:"عملیات گروهی برای "+ResArray[1]+" کاربر لغو شد",ok:"بستن"});
					parent.DetachOnCloseTab();
					parent.dhxLayout.dhxWins.window("popupWindow").detachEvent(Window_OnCloseEvId);
					if(CloseRequest)
						parent.dhxLayout.dhxWins.window("popupWindow").close();
				}
			}
			else
				Form1.enableItem("CancelBeforeStart");
		}
	});
	return false;
}

}//window.onload

// window.onbeforeunload = function () {
	// window.event.returnValue = "Closing this window will cause to creating ambiguous result.\nPlease wait for batch process to do\nOr stop the process!!!\nDo you want to close it anyway???";
    // return  "Closing this window will cause to creating ambiguous result.\nPlease wait for batch process to do\nOr stop the process!!!\nDo you want to close it anyway???";
// }
	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
