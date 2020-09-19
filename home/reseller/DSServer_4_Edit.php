<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSServerEdit ....................................................................................");
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
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
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
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="دسترسی به فایل های پشتیبان از طریق وب";
	var DataName="DSServer_4_";
	var ChangeLogDataName='Server';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSServer_4_Edit","70","Admin.Server.BackupWebAccess.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.Server.ChangeLog.List","ParentId="+RowId+"&ChangeLogDataName=Server"],
					];
	//=======Form1 Server Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	
							PermitIP:'آی پی/شبکه مثال 192.168.5.2/32,172.16.2.0/24',
							Username:'نام کاربری برای دسترسی از طریق وب',
							Password:'رمزعبور برای دسترسی از طریق وب'
						 };
	var Form1FieldHelpId=["Username","Password","PermitIP"];
	var Form1TitleField="PartName";
	var Form1DisableItems=["PartName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="SessionTimeout ";
	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"Server_Id", label:"شناسه سرور :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"PartName", label:"نام بخش :",maxLength:32, validate:"NotEmpty,IsValidENName",disabled:"true", labelAlign:"left", inputWidth:140},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{type:"block",name:"BodyBlock",width:600,list:[
			{ type:"settings" , labelWidth:150, inputWidth:250,offsetLeft:10  },
			{ type:"input" , name:"Username", label:"نام کاربری :",maxLength:32, validate:"NotEmpty,IsValidENName", labelAlign:"left", info:"true", inputWidth:100,required:true},
			{ type:"input" , name:"Password", label:"رمزعبور :",maxLength:32, validate:"NotEmpty,IsValidENName", labelAlign:"left", info:"true", inputWidth:100,required:true},
			{ type:"input" , name:"PermitIP", label:"آی پی مجاز :",maxLength:128, validate:"NotEmpty,ISPermitIp", labelAlign:"left", info:"true", inputWidth:300,required:true},
			{ type:"label",name:"HintLabel", label:""}
		]}
		];

	var PermitView=ISPermit("Admin.Server.BackupWebAccess.View");
	var PermitEdit=ISPermit("Admin.Server.BackupWebAccess.Edit");
		
		
	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Exit","بستن","tow_Exit",TopToolbar_OnExitClick);

	// TabbarMain   ===================================================================
	var TabbarMain = dhxLayout.cells("a").attachTabbar();
	DSTabbarInitial(TabbarMain,TabbarMainArray);

	// Toolbar1   ===================================================================
	var Toolbar1 = TabbarMain.cells(0).attachToolbar();
	DSToolbarInitial(Toolbar1);
	
	// Form1   ===================================================================
	Form1=DSInitialForm(TabbarMain.cells(0),Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onChange",Form1OnChange);
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(PermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!PermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		//parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		//if(PermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Form1OnChange(name,value){
	// dhtmlx.message("name="+name+"<br/>value="+value);
	if(name=="ISEnable"){
		if(value=="Yes"){
			Form1.setItemLabel("HintLabel","<a target='_blank' href='https://"+window.location.hostname+"/backup'>لینک فایل های پشتیبان</a>");
			Form1.enableItem("BodyBlock");
		}
		else{
			Form1.setItemLabel("HintLabel","لینک فایل های پشتیبان");
			Form1.disableItem("BodyBlock");
		}
	}
}
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		dhtmlx.confirm({
			title: "تاییدیه",
			type:"confirm-warning",
			ok: "بلی",
			cancel: "خیر",
			text: "سرویس آپاچی سرور راه اندازی مجدد می شود،آیا مطمئن هستید؟",
			callback: function(Result){
				if(Result){
					Form1.updateValues();
					if(Form1.getItemValue("مجازIP")=="0.0.0.0/0"){
						dhtmlx.alert("شما نمی توانید اجازه دهید همه آی پی ها به پشتیبان دسترسی پیدا کنند");
						return;
					}
					dhxLayout.progressOn();
					Form1.send(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update","post",function(loader, response){
						dhxLayout.progressOff();
						response=CleanError(response);
						var ErrorStr="";
						if(response!=""){//difference with other is this restart httpd and if no error occure response wil be empty
							ErrorStr=response.substring(1);
							Form1DoAfterUpdateFail();
						}
						else{
							dhtmlx.message("عملیات شما با موفقیت انجام شد!");
							Form1DoAfterUpdateOk();
						}
						if(ErrorStr!="") alert(ErrorStr);//MUST use alert function
					});
				}
			}
		});
	}
}

function Form1OnButtonClick(){
}

function TopToolbar_OnExitClick(){
	TopToolbar.attachEvent("onclick",function(id){
		if(id=="Exit"){parent.dhxLayout.dhxWins.window("popupWindow").close();}	
	});
}	
function Form1DoAfterLoadOk(){
	parent.dhxLayout.dhxWins.window("popupWindow").setText("ویرایش "+DataTitle+" ["+Form1.getItemValue(Form1TitleField)+"]");
	Form1OnChange("ISEnable",Form1.getItemValue("ISEnable"));
}	

function Form1DoAfterLoadFail(){
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	//parent.UpdateGrid(0);
}
function Form1DoAfterUpdateFail(){
}
function Form1DoAfterInsertOk(r){
	RowId=r;
	parent.UpdateGrid(r);
	LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!PermitEdit){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	
}
function Form1DoAfterInsertFail(){
}

function LoadTabbarMain(f_TabbarMain,f_TabbarMainArray,f_RowId){
	
	for (var i=1;i<f_TabbarMainArray.length;i++)
	if(ISPermit(f_TabbarMainArray[i][3])){
		f_TabbarMain.addTab(i,f_TabbarMainArray[i][0] , f_TabbarMainArray[i][2]);
		f_TabbarMain.setContentHref(i,f_TabbarMainArray[i][1]+".php?"+un()+"&ParentId="+f_RowId+"&"+f_TabbarMainArray[i][4]);
	}
	//f_TabbarMain.normalize();
}

	
}

	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
