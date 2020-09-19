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
			margin: 10px;
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

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="شاهکار";
	var DataName="DSServer_11_";
	var ChangeLogDataName='Server';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSServer_11_Edit","70","Admin.Server.Shahkar.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.Server.ChangeLog.List","ParentId="+RowId+"&ChangeLogDataName=Server"],
					];
	//=======Form1 Server Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	
							MessageText:'SMS Body'
						 };
	var Form1FieldHelpId=["MaxYearlyCount","MaxMonthlyCount","MaxWeeklyCount","MaxDailyCount","MessageText"];
	var Form1TitleField="PartName";
	var Form1DisableItems=["PartName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="";
	var Form1Str = [
		{ type:"settings" , labelWidth:120, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120},
		{ type: "label"},
		{ type:"hidden" , name:"Server_Id", label:"شناسه سرور:",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"PartName", label:"نام بخش :",maxLength:32, validate:"NotEmpty,IsValidENName",disabled:"true", labelAlign:"left", inputWidth:200},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"},{text: "آفلاین", value: "Offline"}],inputWidth:80,required:true},
		{type:"block",name:"BodyBlock",width:600,list:[
			{ type:"settings" , labelWidth:120, inputWidth:250,offsetLeft:10  },
			{ type:"input" , name:"ProviderCode", label:"کد ارائه دهنده :",maxLength:4, validate:"NotEmpty", labelAlign:"left",  inputWidth:100,required:true},
			{ type:"input" , name:"Username", label:"نام کاربری :",maxLength:16, validate:"NotEmpty", labelAlign:"left", inputWidth:100,required:true},
			{ type:"input" , name:"Password", label:"کلمه عبور :",maxLength:16, validate:"NotEmpty", labelAlign:"left", inputWidth:100,required:true},
			{ type:"input" , name:"IP", label:"آی پی :",maxLength:15,value:"", validate:"NotEmpty,ValidIPv4",required:true, labelAlign:"left", inputWidth:200},
			{ type:"input" , name:"Port", label:"پورت :",maxLength:5, validate:"NotEmpty,ValidInteger", labelAlign:"left", inputWidth:300,required:true},
			{ type:"input" , name:"CRM_OperatorName", label:"CRM نام کاربری :",maxLength:32, validate:"NotEmpty", labelAlign:"left", inputWidth:100,required:true},
			
			
		]}
		];

	var PermitView=ISPermit("Admin.Server.Shahkar.View");
	var PermitEdit=ISPermit("Admin.Server.Shahkar.Edit");
		
		
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
	if(name=="ISEnable"){
		if(value!="No")
			Form1.enableItem("BodyBlock");
		else
			Form1.disableItem("BodyBlock")
	}
}
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		if(RowId>0){//update
			if(parseInt(Form1.getItemValue("MaxYearlyCount"))<parseInt(Form1.getItemValue("MaxMonthlyCount"))){
				dhtmlx.alert("حد سالانه نمی تواند کمتر از حد ماهانه باشد");
				return;
			}
			else if(parseInt(Form1.getItemValue("MaxMonthlyCount"))<parseInt(Form1.getItemValue("MaxWeeklyCount"))){
				dhtmlx.alert("حد ماهانه نمی تواند کمتر از حد هفتگی باشد");
				return;
			}
			else if(parseInt(Form1.getItemValue("MaxWeeklyCount"))<parseInt(Form1.getItemValue("MaxDailyCount"))){
				dhtmlx.alert("حد هفتگی نمی تواند کمتر از حد روزانه باشد");
				return;
			}
			DSFormUpdateRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=update",Form1DoAfterUpdateOk,Form1DoAfterUpdateFail);
		}//update
		else{//insert
			DSFormInsertRequestProgress(dhxLayout,Form1,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=insert",Form1DoAfterInsertOk,Form1DoAfterInsertFail);
			
		}//insert
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
	if(Form1.getItemValue("ISEnable")!="No")
		Form1.enableItem("BodyBlock");
	else
		Form1.disableItem("BodyBlock");
}	

function Form1DoAfterLoadFail(){
}	

function Form1DoAfterUpdateOk(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
	parent.UpdateGrid(0);
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