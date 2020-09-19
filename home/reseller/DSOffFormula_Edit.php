<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSFinishRuleEdit ....................................................................................");
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

window.onload = function(){
	var RowId = "<?php  echo $_GET['RowId'];  ?>";
	if(RowId == "" ) {return;}
	var DataTitle="فرمول تخفیف";
	var DataName="DSOffFormula_";
	var ChangeLogDataName='OffFormula';
	var TabbarMain,TopToolbar;
	//=======TabbarMain
	var TabbarMainArray=[
					["اطلاعات","DSOffFormula_Edit","70","Admin.User.OffFormula.Edit",""],
					["لیست تغییرات","DSChangeLog","95","Admin.User.OffFormula.ChangeLog.List","ChangeLogDataName=OffFormula"]
					];
	//=======Form1 OffFormula Info
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  ={OffFormulaName:'نام فرمول تخفیف - انگلیسی و ۳۲ کاراکتر',
						FixOff:'مقداری که همیشه به درصد تخفیف اضافه می شود (از ۰ تا ۱۰۰)',
						MonthlyRate:'به ازای گذشت هر ماه،این مقدار به درصد تخفیف اضافه می شود (از ۰ تا ۱۰۰)',
						MonthlyMaxOff:'سقف مجاز افزایش تخفیف ماهیانه',
						TimeBaseOff:'تخفیف وابسته به زمان که به درصد تخفیف اضافه می شود (از ۰ تا ۱۰۰)',
						TimeRegex:'رشته زمان - حداکثر ۲۵۰ کاراکتر',
						SavingOffPercent:'مشخص می کند چند درصد از تخفیف کاربر را به عنوان پس انداز برای خرید آینده نگه دارد ',
						SavingOffExpirationDays:'تعداد روز از زمان ایجاد پس انداز تا کاربر بتواند از آن استفاده کند'
						};
	var Form1FieldHelpId=["OffFormulaName","FixOff","MonthlyRate","MonthlyMaxOff","TimeBaseOff","TimeRegex","SavingOffPercent","SavingOffExpirationDays"];
	var Form1TitleField="OffFormulaName";
	var Form1DisableItems=["OffFormulaName"];
	var Form1EnableItems=[];
	var Form1HideItems=[];
	var Form1ShowItems=[];
	var Form1FocusItemAdd="FixOff";
	var Form1Str = [
		{ type:"settings" , labelWidth:185, inputWidth:250,offsetLeft:10  },
		{ type: "label"},
		{ type:"hidden" , name:"OffFormula_Id", label:"OffFormula_Id :",disabled:"true", labelAlign:"left", inputWidth:130},
		{ type:"input" , name:"OffFormulaName", label:"نام :",maxLength:32, validate:"NotEmpty,IsValidENName",required:true, labelAlign:"left", info:"true", inputWidth:200},
		{ type:"input" , name:"FixOff", label:"تخفیف ثابت :",value:"0",maxLength:5, validate:"IsValidPercent",required:true, labelAlign:"left", info:"true", inputWidth:120},
		{ type:"input" , name:"MonthlyRate", label:"افزایش تخفیف ماهیانه :",value:"0",maxLength:5, validate:"IsValidPercent",required:true, labelAlign:"left", info:"true", inputWidth:120},
		{ type:"input" , name:"MonthlyMaxOff", label:"حداکثر تخفیف ماهیانه :",value:"0",maxLength:5, validate:"IsValidPercent",required:true, labelAlign:"left", info:"true", inputWidth:120},
		{ type:"input" , name:"TimeBaseOff", label:"افزایش تخفیف در روز/ها مشخص :",value:"0",maxLength:5, validate:"IsValidPercent",required:true, labelAlign:"left", info:"true", inputWidth:120},
		{ type:"input" , name:"TimeRegex", label:"روز/ها مشخص شده :",maxLength:250, validate:"", labelAlign:"left", info:"true", inputWidth:400,note: { text: "<span style='direction:rtl;float:right;text-align:justify'></br>فرمت استفاده شده طبق استاندارد UUCP می باشد که تعریف یک یا چند رشته ساده زمان است که با «,» از هم جدا می شوند و هر رشته باید با «روز» شروع گردد.روز ها شامل</br> sa:شنبه&nbsp;&nbsp;&nbsp;su:یکشنبه&nbsp;&nbsp;&nbsp;mo:دوشنبه&nbsp;&nbsp;&nbsp;tu:سه شنبه&nbsp;&nbsp;&nbsp;we:چهارشنبه&nbsp;&nbsp;&nbsp;th:پنج شنبه&nbsp;&nbsp;&nbsp;fr:جمعه می باشد.</br>در حالتی که بخواهید محدودیتی اعمال نکنید،در فیلد مقدار «any»و یا«al» بگذارید.مثال :</br>mo کاربر فقط روزهای دوشنبه قادر به دریافت تخفیف بیشتر است.</br>درحالتی که بخواهیم در روز،ساعات خاصی  را مشخص کنیم از «-»  استفاده می شود:</br> sa0800-1200  فقط شنبه ها از ساعت ۰۸:۰۰ تا ساعت ۱۲:۰۰ می تواند تخفیف بیشتر دریافت کند</br> sa0855-2305,su0800-1600  شنبه ها از ساعت ۰۸:۵۵ تا ۲۳:۰۵ و یکشنبه ها از ساعت ۰۸:۰۰ تا ۱۶:۰۰ می تواند تخفیف بیشتر دریافت کند </span>"}},
		{ type:"input" , name:"SavingOffPercent", label:"درصد پس انداز :",value:"0",maxLength:5, validate:"IsValidPercent",required:true, labelAlign:"left", info:"true", inputWidth:120},
		{ type:"input" , name:"DirectOffPercent", label:"درصد مستقیم زمان خرید کاربر :",value:"100",maxLength:5, validate:"IsValidPercent",required:true, labelAlign:"left", info:"true", inputWidth:120,disabled:true,numberFormat: "0.00"},
		{ type:"input" , name:"SavingOffExpirationDays", label:"تعداد روز تا انقضا پس انداز :",value:"30",maxLength:5, validate:"NotEmpty,ValidInteger",required:true, labelAlign:"left", info:"true", inputWidth:120,disabled:true},
		];
 	 	 	 	 	 
	var PermitView=ISPermit("Admin.User.OffFormula.View");
	var PermitAdd=ISPermit("Admin.User.OffFormula.Add");
	var PermitEdit=ISPermit("Admin.User.OffFormula.Edit");
	var PermitDelete=ISPermit("Admin.User.OffFormula.Delete");
					 
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
	Form1.attachEvent("onInputChange",Form1OnInputChange);
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
function Form1OnInputChange(name,value){
	if(name=="SavingOffPercent"){
		var t=parseFloat(value);
		if(isNaN(t)||(t<0))
			t=0;
		else if(t>100)
			t=100;
		Form1.setItemValue("DirectOffPercent",Math.round((100-t)*100)/100);
		if(t==0)
			Form1.disableItem("SavingOffExpirationDays");
		else
			Form1.enableItem("SavingOffExpirationDays");
	}
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
	Form1.setFocusOnFirstActive();
	Form1OnInputChange("SavingOffPercent",Form1.getItemValue("SavingOffPercent"));
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