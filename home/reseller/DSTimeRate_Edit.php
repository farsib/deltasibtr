<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSTimeRateEdit ....................................................................................");
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
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="ضریب زمان";
	var DataName="DSTimeRate_";
	var ChangeLogDataName='TimeRate';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSTimeRate_Edit","70","Admin.User.TimeRate.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.TimeRate.ChangeLog.List","ChangeLogDataName=TimeRate"]
					];
	//=======Form1 TimeRate Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={TimeRateName:'نامی که به ضرایب تعریف شده زیر انتساب داده می شود'};
	var Form1FieldHelpId=["TimeRateName",'H0','H1','H2','H3','H4','H5','H6','H7','H8','H9','H10','H11','H12','H13','H14','H15','H16','H17','H18','H19','H20','H21','H22','H23'];
	var Form1TitleField="TimeRateName";
	var Form1DisableItems=["TimeRateName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="UsernamePattern";

	var Form1Str = [
		{ type:"settings" , labelWidth:60, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"TimeRate_Id", label:"TimeRate_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"TimeRateName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:150},
		{ type: "label" ,label:"<span style='float:right;padding-bottom:6px;font-weight: normal;font-size: 14px;'>(اعداد بین ۰ تا ۲.۵ را می توانید وارد نمایید)</span>",labelWidth:360},
		{type: "block",  width: 690,className :"myHeader", list:[ 
			{ type: "input" , name:"H0",labelWidth:40, label:"۰-۱:", value: "1.00",validate:"IsValidTimeRate",maxLength:4, labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H1",labelWidth:40, label:"۱-۲:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H2",labelWidth:40, label:"۲-۳:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H3",labelWidth:40, label:"۳-۴:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H4",labelWidth:40, label:"۴-۵:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H5",labelWidth:40, label:"۵-۶:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{type: "newcolumn"},
			{ type: "input" , name:"H6",labelWidth:40, label:"۶-۷:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H7",labelWidth:40, label:"۷-۸:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H8",labelWidth:40, label:"۸-۹:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H9",labelWidth:40, label:"۹-۱۰:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H10",labelWidth:40, label:"۱۰-۱۱:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H11",labelWidth:40, label:"۱۱-۱۲:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{type: "newcolumn"},
			{ type: "input" , name:"H12",labelWidth:40, label:"۱۲-۱۳:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H13",labelWidth:40, label:"۱۳-۱۴:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H14",labelWidth:40, label:"۱۴-۱۵:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H15",labelWidth:40, label:"۱۵-۱۶:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H16",labelWidth:40, label:"۱۶-۱۷:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H17",labelWidth:40, label:"۱۷-۱۸:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{type: "newcolumn"},
			{ type: "input" , name:"H18",labelWidth:40, label:"۱۸-۱۹:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H19",labelWidth:40, label:"۱۹-۲۰:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H20",labelWidth:40, label:"۲۰-۲۱:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H21",labelWidth:40, label:"۲۱-۲۲:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H22",labelWidth:40, label:"۲۲-۲۳:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50},
			{ type: "input" , name:"H23",labelWidth:40, label:"۲۳-۲۴:", value: "1.00",maxLength:4 , validate:"IsValidTimeRate", labelAlign:"right",inputWidth:50}
		]}
		];
	var PermitAdd=ISPermit("Admin.User.TimeRate.Add");
	var PermitView=ISPermit("Admin.User.TimeRate.View");
	var PermitEdit=ISPermit("Admin.User.TimeRate.Edit");
	var PermitDelete=ISPermit("Admin.User.TimeRate.Delete");

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
	
	if(RowId>0){
		DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
		LoadTabbarMain(TabbarMain,TabbarMainArray,RowId);
		if(PermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(PermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!PermitEdit)	FormDisableAllItem(Form1);
	}
	else{
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(PermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================
function Toolbar1_OnRetrieveClick(){
	DSFormLoadProgress(dhxLayout,Form1,Form1DoAfterLoadOk,Form1DoAfterLoadFail,TabbarMainArray[0][1]+"Render.php?",RowId,Form1DisableItems,Form1EnableItems,Form1HideItems,Form1ShowItems);
}

function Toolbar1_OnSaveClick(){
	if(DSFormValidate(Form1,Form1FieldHelpId)){
		if(RowId>0){//update
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
