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
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var ServiceName = "<?php  echo $_GET['ServiceName'];  ?>";
	if(ServiceName == "" ) {return;}
	var DataTitle="سرویس";
	var DataName="DSServer_8_EditServices";
	var RenderFile=DataName+"Render";
	//=======Form1 Service Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={};
	var Form1FieldHelpId=[];
	var Form1Str = [
		{ type: "label"},
		{type: "block",list:[
			{type:"fieldset", width:660, label:" "+ServiceName+" سرویس ", list:[
				{ type: "label"},
				{type:"block",list:[
					{type: "label", name:"ServiceStatus", label:"", labelWidth:560}
				]},
				{type:"block",list:[
					{type: "button", name:"Status", value: " وضعیت ", width :80, disabled:true},
					{type: "newcolumn", offset:30},
					{type: "button", name:"Stop", value: " توقف ", width :80, disabled:true},
					{type: "newcolumn", offset:30},
					{type: "button", name:"Start", value: " شروع ", width :80, disabled:true},
					{type: "newcolumn", offset:40},
					{type: "button", name:"Restart", value: " شروع مجدد ", width :80, disabled:true}
				]},
				{ type: "label"},
				{type:"block",list:[
					{type: "checkbox", label: "شروع خودکار", name: "AutoStart", position: "label-right", labelWidth:100, checked: <?php echo (count(glob("/etc/rc2.d/S*".addslashes($_GET['ServiceName'])))>0)?"true":"false" ?>},
					{type: "newcolumn"},
					{type: "button", name:"SetAutoStart", value: "تنظیم", width :40,disabled:true},
				]},
			]}
		]}
	];

	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	
	// Form1   ===================================================================
	Form1=DSInitialForm(dhxLayout.cells("a"),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onChange",Form1OnChange);
	if(ISPermit("Admin.Server.DeltasibServices."+ServiceName+".Status")){
		Form1.enableItem("Status");
		if(ISPermit("Admin.Server.DeltasibServices."+ServiceName+".Stop")) Form1.enableItem("Stop");
		if(ISPermit("Admin.Server.DeltasibServices."+ServiceName+".Start")) Form1.enableItem("Start");
		if(ISPermit("Admin.Server.DeltasibServices."+ServiceName+".Restart")) Form1.enableItem("Restart");
		Form1OnButtonClick("Status");
	}
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnChange(name,value,chkstate){
	if(name=="AutoStart")
		Form1.enableItem("SetAutoStart");
}
function Form1OnButtonClick(name){
	if(name=="SetAutoStart")
		DSFormUpdateRequestProgress(dhxLayout,Form1,RenderFile+".php?"+un()+"&act="+name+"&ServiceName="+ServiceName,function(){Form1.disableItem("SetAutoStart");},null);
	else{
		Form1.setItemLabel("ServiceStatus","در حال پردازش...");
		Form1.lock();
		dhxLayout.cells("a").progressOn();
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act="+name+"&ServiceName="+ServiceName, function(l){
			Form1.unlock();
			dhxLayout.cells("a").progressOff();
			response=l.xmlDoc.responseText;
			response=CleanError(response);
			ResArray=response.split("~");
			if((response=='')||(response[0]=='~')){
				dhtmlx.alert("خطا، "+response.substring(1));
				Form1.setItemLabel("ServiceStatus","خطا، "+response.substring(1));
			}
			else if(ResArray[0]!='OK'){
				dhtmlx.alert("خطا، "+response);
				Form1.setItemLabel("ServiceStatus","خطا، "+response);
			}
			else
				Form1.setItemLabel("ServiceStatus",ResArray[1]);
		});
	}
}

}

	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>