<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSVispEdit ....................................................................................");
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
	var DataTitle="ارائه دهنده مجازی اینترنت";
	var DataName="DSVisp_";
	var ChangeLogDataName='Visp';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSVisp_Edit","70","Admin.VISPs.Edit",""],
					["پارامتر","DSParam_List","80","Admin.VISPs.Param.List","ParamItemGroup=Visp"],
					["لیست تغییرات","DSChangeLog","95","Admin.VISPs.ChangeLog.List","ChangeLogDataName=Visp"]
					];
	//=======Form1 Visp Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={	VispName:'فقط کلمات انگلیسی و اعداد، بدون فاصله. حداکثر ۳۲ کاراکتر',
							UsernamePattern:"علامت '.' میتواند جایگزین هر کاراکتری شود.لیست کاراکترهایی که می خواهیم جایگزین شوند درون براکت قرار می گیرند\r[...] و لیست کاراکترهایی که نمی خواهیم از آن ها استفاده شود بصورت [...^] وارد می کنیم"};
	var Form1FieldHelpId=["VispName","UsernamePattern"];
	var Form1TitleField="VispName";
	var Form1DisableItems=["VispName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="UsernamePattern";

	var Form1Str = [
		{ type:"settings" , labelWidth:170, inputWidth:250,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type: "label"},
		{ type:"hidden" , name:"Visp_Id", label:"ارائه دهنده مجازی اینترنت شناسه :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type: "select", name:"ISEnable", label: "فعال :", options:[{text: "بلی", value: "Yes",selected: true},{text: "خیر", value: "No"}],inputWidth:80,required:true},
		{ type:"input" , name:"VispName", label:"نام ارائه دهنده مجازی اینترنت :",maxLength:40, validate:"NotEmpty,IsValidVispName",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"UsernamePattern", label:"الگو نام کاربری :",maxLength:250, value:".*",validate:"NotEmpty",required:true, labelAlign:"left", info:"true", inputWidth:400,note: { text: "<a href='http://smartispbilling.com/help/deltasib/content/loadContent/regularExpressions' target='myIframe'>صفحه راهنما</a>"}}
		];
	//=======Popup2 Commission
	var Popup2;
	var PopupId2=['Commission'];// popup Attach to Which Buttom of Toolbar

	//=======Form2 Commission
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {ResellerCF:'0,0.10,14500,0.15,50000,0.20 : مثال<br>اگر پرداختی کاربر از ۰ تا ۱۴۵۰۰ باشد،۱۰ درصد پورسانت و <br>اگر بین ۱۴۵۰۰ تا ۵۰۰۰۰ باشد،۱۵ درصد  و<br> اگر بیشتر باشد،۲۰ درصد پورسانت به نماینده تعلق می گیرد'
						,MinPriceChangeReseller:'مبلغی که اگر نماینده فروشی،بیش از آن را در پرداختی کاربر ثبت کرد<br>مالکیت آن کاربر به این نماینده تغییر خواهد کرد'
						};
	var Form2FieldHelpId=['ResellerCF','MinPriceChangeReseller'];
	var Form2Str = [
		{ type:"settings" , labelWidth:220, inputWidth:350,offsetLeft:10  },
		{ type: "hidden" , name:"Error", label:"خطا :", validate:"",value:"", maxLength:16,inputWidth:120, info:"true"},
		{ type:"input" , name:"ResellerCF", label:"پورسانت نماینده فروش:",validate:"IsValidCommissionFormula", labelAlign:"left", value:"0,0",info:"true", inputWidth:200, required:true},
		{ type:"input" , name:"ChargerCF", label:"پورسانت شارژر:",validate:"IsValidCommissionFormula", labelAlign:"left", value:"0,0",info:"false", inputWidth:200, required:true},
		{ type:"input" , name:"MinPriceChangeReseller", label:"حداقل مبلغ تغییر نماینده :",validate:"IsValidPrice", value:"0",labelAlign:"left", info:"true", inputWidth:200, maxLength:14, required:true},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "ثبت",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}	
		];
	ISPermitView=ISPermit("Admin.VISPs.View");
	ISPermitEdit=ISPermit("Admin.VISPs.Edit");
	ISPermitAdd=ISPermit("Admin.VISPs.Add");
	
	
	ISPermitCommissionView=ISPermit('Admin.VISPs.Commission.View');
	ISPermitCommissionEdit=ISPermit('Admin.VISPs.Commission.Edit');
	
	
	
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
		if(ISPermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
		if(ISPermitEdit)	DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
		if(!ISPermitEdit)	FormDisableAllItem(Form1);
		if(ISPermitCommissionView || ISPermitCommissionEdit) AddPopupCommission();
		if(!ISPermitCommissionEdit) From2.removeItem("Proceed");
	}
	else{
		parent.dhxLayout.dhxWins.window("popupWindow").setText("افزودن "+DataTitle);
		if(ISPermitAdd)		DSToolbarAddButton(Toolbar1,null,"Save","ذخیره","tof_Save",Toolbar1_OnSaveClick);
	}

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	

//FUNCTION========================================================================================================================
//================================================================================================================================

function AddPopupCommission(){
	DSToolbarAddButtonPopup(TopToolbar,null,"Commission","پورسانت","tow_Commission");
	Popup2=DSInitialPopup(TopToolbar,PopupId2,Popup2OnShow);
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
}

function Form2OnButtonClick(name){//Commission
	if(name=='Close') Popup2.hide();
	else{
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form2,TabbarMainArray[0][1]+"Render.php?"+un()+"&act=UpdateCommission&id="+RowId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
}
function Form2DoAfterUpdateOk(){
	Popup2.hide();
}

function Popup2OnShow(){//Commission
	if(!ISPermitCommissionEdit) From2.removeItem("Proceed");
	Form2.load(TabbarMainArray[0][1]+"Render.php?"+un()+"&act=LoadCommission&id="+RowId,function(id,respond){
//		Form2.setItemFocus("ResellerCF");
		var ErrorValue=Form2.getItemValue("Error");
		
		if((ErrorValue!=null)&&(ErrorValue!='')){
			Popup2.hide();
			alert("خطا، "+ErrorValue);
		}
		else{
		}	
		});
}

function Form2DoAfterUpdateFail(){

}



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
	if(ISPermitView)	DSToolbarAddButton(Toolbar1,0,"Retrieve","بروزکردن","Retrieve",Toolbar1_OnRetrieveClick);
	if(!ISPermitEdit){
		Toolbar1.removeItem('Save');
		FormDisableAllItem(Form1);
	}
	if(ISPermitCommissionView || ISPermitCommissionEdit) AddPopupCommission();
	if(!ISPermitCommissionEdit) From2.removeItem("Proceed");
	
	
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
