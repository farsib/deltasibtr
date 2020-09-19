<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSBatchProcess_SendSMS ....................................................................................");
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
    <meta http-equiv="Content-Type" content="text/html; char=utf-8; charset=UTF-8" />
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
	if((BatchProcess_Id == "")||(BatchProcess_Id == 0) ) {alert("خطایی روی داد. با پشتیبانی تماس بگیرید");return;}
	var BatchProcessName ="<?php echo DBSelectAsString("SELECT BatchProcessName from Hbatchprocess where BatchProcess_Id=".addslashes($_GET['BatchProcess_Id']))?>";
	var UserCount ="<?php  echo addslashes($_GET['UserCount']);?>";
	if((UserCount == "")||(UserCount == 0)) {alert("خطایی روی داد. با پشتیبانی تماس بگیرید");return;}
	
	var From_Index=0;
	
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATETIMESTR(NOW())');?>";
	var DTArr=TNow.split(" ");
	
	var DataTitle="پیام پنل کاربری";
	var DataName="DSBatchProcess_WebMessage";
	var RenderFile=DataName+"Render";

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);
	TopToolbar.addSpacer("Exit");


	//=======Form1 GoToBatchProcess
	var Form1;
	var Form1Help;
	var Form1FieldHelp  = {	
							Action:'نوع عملیات را انتخاب نمایید',
							WebMessageTitle:'عنوان پیام',
							WebMessageBody:'متن پیام',
							Type:'انتخاب کنید که چه نوع پیامی را می خواهید حذف کنید',
							ReplaceCR:'نمایش داده می شود.برای نمایش درست خط بعدی،تیک این گزینه را بگذارید HTML پیام در پنل کاربران به صورت تگ های'
						};
	var Form1FieldHelpId=['Action','WebMessageTitle','WebMessageBody','Type','ReplaceCR'];
    var Form1Str=[
		// { type: "label"},
		{type: "block", name: "B1", list:[
			{type: "fieldset",width:680,label:" جزئیات ", list:[			
				{ type:"settings" , labelWidth:90, inputWidth:160  },
				{ type: "select", name:"Action", label: "نوع عملیات :", options:[
						{text: "ارسال پیام", value: "SendMessage",selected: true,list:[
							{ type:"input" , name:"WebMessageTitle", label:"عنوان :", maxLength:64, validate:"NotEmpty", inputWidth:350, labelAlign:"left", info:true},
							{type: "input", name: "WebMessageBody", label: "پیام : ", maxLength:2048, validate:"NotEmpty", inputHeight:230,rows:14, inputWidth: 520,
							note: { text: "<span style='direction:rtl;float:right'>می توان از  "+CreateSMSItems("[Name]")+","+CreateSMSItems("[Family]")+","+CreateSMSItems("[Company]")+","+CreateSMSItems("[Username]")+","+CreateSMSItems("[ShExpireDate]")+","+CreateSMSItems("[RTrM]")+","+CreateSMSItems("[UserDebit]")+","+CreateSMSItems("[SHDate]")+","+CreateSMSItems("[Time]")+" استفاده نمود</span>"}
							, info:true},
							{type: "checkbox", label: "&lt;br/&gt; با \\n  جایگذاری", labelWidth:200, name: "ReplaceCR", position: "label-right", checked: true , info:true}
						]},
						{text: "حذف پیام", value: "DeleteMessage",list:[
							{ type: "select", name:"Type", label: "نوع پیام :", options:[
								{text: "پیام های خوانده شده", value: "Read"},
								{text: "پیام های خوانده نشده", value: "UnRead"},
								{text: "همه پیام ها", value: "All"}
								],inputWidth:160,required:true, info:true},
							{type:"label", name:"DeleteMessageLabel",label:"",labelHeight:235},
						]},
					],inputWidth:160,required:true, info:true},
				{ type: "label"},
			]}
		]},
		{ type: "label"},
		{type: "block", name: "B3", list:[
			{type: "newcolumn", offset:50},
			{type: "button", name:"CancelBeforeStart", value: " لغو ", width :100},
			{type: "newcolumn", offset:190},
			{type: "button", name:"DoBatch", value: " ارسال پیام برای "+UserCount+" کاربر ", width :280}
		]},
		{type: "block", name: "B5", hidden: true, list:[
			{type: "newcolumn", offset:404},
			{type: "button", name:"Close", value: " بستن ", width :100},			
			{type: "newcolumn", offset:10},
			{type: "button", name:"SaveLog", value: " ذخیره گزارش ", width :100}
		]},
		{type: "label"},	
		{type: "block",name: "B8", list:[
			{type: "label" ,label:"<div style='position:fixed;bottom:10px;border-top:2px dotted darkgray;font-size:15;color:red;background-color:white;padding: 0 131px 0 100;'>تا پایان عملیات گروهی از ورود مجدد به پنل مدیریت خودداری کنید</div>",labelHeight:20}
		]}
	];
	
	parent.dhxLayout.dhxWins.window("popupWindow").setText("عملیات گروهی [ "+BatchProcessName+" ]");
	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1Help,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.enableLiveValidation(false);
	Form1.attachEvent("onChange",function(name,value){
		if(name=="Action"){
			if(value=="SendMessage")
				Form1.setItemText("DoBatch"," ارسال پیام برای "+UserCount+" کاربر ");
			else
				Form1.setItemText("DoBatch"," حذف "+" پیام از "+UserCount+" کاربر ");
		}
		else if(name=="Type")
			Form1.setItemText("DoBatch"," حذف "+" پیام از "+UserCount+" کاربر ");
	});
	parent.document.oncontextmenu=document.oncontextmenu = function(){return false};
	parent.document.ondrop = document.ondrop = function(){return false};
	document.onselectstart= function(){return false};
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	




//FUNCTION========================================================================================================================
function CreateSMSItems(Item){
	return "<a href='javascript:void(0)' onclick='CopyTextToClipBoard(\""+Item+"\");' style='text-decoration:none' title='برای کپی کلیک کنید'>"+Item+"</a>";
}
//-------------------------------------------------------------------TopToolbar_OnExitClick()
function TopToolbar_OnExitClick(){
	parent.dhxLayout.dhxWins.window("popupWindow").close();
}	

//-------------------------------------------------------------------Form1OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='CancelBeforeStart')
		CancelBeforeStart(false);
	else if(name=='DoBatch'){
		Form1.updateValues();		
		if(!DSFormValidate(Form1,Form1FieldHelpId))
			return;
		Form1.disableItem('DoBatch');
		parent.dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-warning",
			ok: "بلی",
			cancel: "خیر",						
			text: "از آغاز عملیات مطمئن هستید؟",
			callback: function(Result){
				if(Result){
					parent.dhtmlx.confirm({
						title: "هشدار",
						type:"confirm-warning",
						ok: "بلی",
						cancel: "خیر",						
						text: "این عملیات گروهی همه کارها را به یکبار انجام می دهد. نمی توانید در حین پردازش لغو کنید<br/>ادامه می دهید؟",
						callback: function(Result){
							if(Result){
								dhxLayout.progressOn();
								
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
								
								Form1.disableItem('B1');
								Form1.disableItem('B3');
									
								var MyTimerStart=new Date();
								Form1.send(RenderFile+".php?"+un()+"&act=StartBatchProcess&BatchProcess_Id="+BatchProcess_Id,"post",function(loader, response){
									dhxLayout.progressOff();
									response=loader.xmlDoc.responseText;
									response=CleanError(response);
									if((response=='')||(response[0]=='~')){
										if(response[0]=='~')
											response=response.substring(1);
										dhtmlx.alert(response);
									}
									else{
										var MyTimerEnd=new Date();
										parent.dhtmlx.alert({text:"با موفقیت انجام شد<br/>مجموع زمان سپری شده:"+GetTimerString(Math.round(MyTimerEnd-MyTimerStart)/100),ok:"بستن"});
										parent.DetachOnCloseTab();
										parent.dhxLayout.dhxWins.window("popupWindow").detachEvent(Window_OnCloseEvId);
									}
									Form1.hideItem('B3');
									Form1.showItem('B5');
								});				
							}
							else
								Form1.enableItem('DoBatch');
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
					Form1.hideItem('B3');
					Form1.showItem('B5');
					Form1.disableItem('SaveLog');
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
function GetTimerString(FTime){
	var decseconds = Math.floor(FTime % 10 );
	var seconds = Math.floor((FTime /10)% 60 );
	var minutes = Math.floor((FTime/600) % 60 );
	var hours = Math.floor((FTime/(36000)) % 24 );
	var days = Math.floor(FTime/864000 );
	
	return (days>0?(days+"d "):"")+(hours<10?"0"+hours:hours)+":"+(minutes<10?"0"+minutes:minutes)+":"+(seconds<10?"0"+seconds:seconds)+":"+decseconds;
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