<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSBatchProcess_List ....................................................................................");
	PrintInputGetPost();
	$ISNoneBlock=DBSelectAsString("SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')");
	if(($ISNoneBlock=="No")||($LastError!="")){
		?>
		<html><head><script type="text/javascript">
		window.onload = function(){
			var LastError="<?php echo $LastError;?>";
			if(LastError!="")
				parent.dhtmlx.alert("<?php echo escape($LastError) ?>");//"Session Expire, Please Relogin"
			else{
				parent.dhtmlx.confirm({
					title: "خطا",
					type:"confirm-error",
					cancel: "باشه",
					ok: "اطلاعات بیشتر",
					text: "آی پی شما قابل اعتماد نیست و ممکن است در ادامه مسدود شود.آی پی خود را در ' آی پی که مسدود نشود ' اضافه نمایید و مجدد وارد پنل شده و امتحان کنید",
					callback: function(Result){
						if(Result)
							parent.dhtmlx.alert({title: "اطلاعات بیشتر",type: "alert-warning",text: "لطفا آی پی خود را در ' مدیریت ' -> ' سرور ' -> ' مدیر ' -> ' آی پی که بلاک نمی شود ' اضافه نمایید"});
					}
				});
			}
			var button = document.createElement("input");
			button.type = "button";
			button.value = "مشکل را حل کنید و برای بروز کردن اینجا کلیک کنید";
			button.onclick = function(){window.location.reload();};
			button.style.width = "100%";
			button.style.height = "100%";
			button.style.color = "blue";
			button.style.font = "bold 120% tahoma";
			document.body.appendChild(button);
		}
		</script></head></html>
		<?php
			exit();
		}
?>
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
var ISDataChange=false;
var SelectedRowId=0;
var tabcount=parent.tabcount;
var OnTabClose_EventId;

window.onload = function(){
//VARIABLE ------------------------------------------------------------------------------------------------------------------------------

	var IsDebugEnable="<?php echo $DebugLevel;?>";

	var TNow="<?php echo DBSelectAsString("SELECT SHDATETIMESTR(NOW())");?>";
	var CurDate=TNow.split(" ")[0];
	var DataTitle="عملیات گروهی";
	var DataName="DSBatchProcess_";
	var WherePostStr="";
	var FieldsPostStr="";
	var FooterPostStr="";
	var RenderFile=DataName+"ListRender";
	var Steps;
	var GroupByCount=0;
	var	OptionCount=23;
	var GroupByPostStr="";
	var MyGetString="";
	var FilterByFileString="";
	var MyFields=[];
	var MyFooterFields=[];
	var MyFooterFieldsIndex=[];
	var HeaderAlignment = [];
	var ComparisionItems = [];

	var GColIds="";
	var GColHeaders="";
	var ISFilter=false;
	var FilterState=false;
	var GColFilterTypes=[];
	var GFooter="";
	var GColInitWidths="";
	var GColAligns="";
	var GColTypes="";
	var GColVisibilitys=[];
	var ISSort=true;
	var GColSorting="";
	var ColSortIndex=0;
	var SortDirection='asc';
	var FilterRowNumber=0;
	var BatchCountOnThisFilter=1;
	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:530,
				center:true,
				modal:true,
				park :false
				};

//CONTROLS------------------------------------------------------------------------------------------------------------------------------
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	var TheFieldsItems=[
			["UserName","User_Id","Password","PayBalance","Name","Family","FatherName","NationalCode","CompanyName","Nationality","Phone","AdslPhone","Mobile","Comment","NOE","InitialMonthOff","MaxPrepaidDebit"],
			["StartDate","EndDate","GiftEndDT","LastRequestDate","CreatedDate","BirthDate","ExpirationDate"],
			["TrafficType","TimeType","URLReporting","Calendar","PeriodicUse"],
			// ["Service Traffic","Yearly Traffic","Monthly Traffic","Weekly Traffic","Daily Traffic","Extra Traffic","ServiceTraUsed","YearlyTraUsed","MonthlyTraUsed","WeeklyTraUsed","DailyTraUsed","ExtraTraUsed","FinishTraUsed","BugTraUsed","RealTraSend","RealTraReceive"],
			// ["ServiceTime","YearlyTime","MonthlyTime","WeeklyTime","DailyTime","ExtraTime","ServiceTimeUsed","YearlyTimeUsed","MonthlyTimeUsed","WeeklyTimeUsed","DailyTimeUsed","ExtraTimeUsed","FinishTimeUsed","BugTimeUsed","RealTimeUsed"],
			["Service Traffic","Yearly Traffic","Monthly Traffic","Weekly Traffic","Daily Traffic","Extra Traffic","ServiceTraUsed","YearlyTraUsed","MonthlyTraUsed","WeeklyTraUsed","DailyTraUsed","ExtraTraUsed","FinishTraUsed","BugTraUsed","RealTraSend","RealTraReceive","ServiceTraRemain","YearlyTraRemain","MonthlyTraRemain","WeeklyTraRemain","DailyTraRemain","ExtraTraRemain"],
			["ServiceTime","YearlyTime","MonthlyTime","WeeklyTime","DailyTime","ExtraTime","ServiceTimeUsed","YearlyTimeUsed","MonthlyTimeUsed","WeeklyTimeUsed","DailyTimeUsed","ExtraTimeUsed","FinishTimeUsed","BugTimeUsed","RealTimeUsed","ServiceTimeRemain","YearlyTimeRemain","MonthlyTimeRemain","WeeklyTimeRemain","DailyTimeRemain","ExtraTimeRemain"],
			["ActiveServiceName","Visp","Reseller","Center","Supporter","MikrotikRate","IPPool","LoginTime","OffFormula","ActiveDirectory","UserStatus","PortStatus","UserType","AuthMethod"]
		];
		var TheFieldsItemsFa=[
			["نام کاربری","شناسه کاربری","کلمه عبور","تراز مالی","نام","نام خانوادگی","نام پدر","کد ملی","نام شرکت","ملیت","تلفن","ADSL تلفن","موبایل","توضیح","موقعیت مکانی وایرلس","تخفیف اولیه","حداکثر بدهی مجاز"],
			["تاریخ شروع","تاریخ پایان","تاریخ پایان هدیه","تاریخ آخرین درخواست","تاریخ ایجاد","تاریخ تولد","تاریخ انقضا"],
			["نوع ترافیک","نوع زمان","گزارش صفحات بازدید شده","تقویم","نوع محاسبه مصرف"],
			// ["Service Traffic","Yearly Traffic","Monthly Traffic","Weekly Traffic","Daily Traffic","Extra Traffic","ServiceTraUsed","YearlyTraUsed","MonthlyTraUsed","WeeklyTraUsed","DailyTraUsed","ExtraTraUsed","FinishTraUsed","BugTraUsed","RealTraSend","RealTraReceive"],
			// ["ServiceTime","YearlyTime","MonthlyTime","WeeklyTime","DailyTime","ExtraTime","ServiceTimeUsed","YearlyTimeUsed","MonthlyTimeUsed","WeeklyTimeUsed","DailyTimeUsed","ExtraTimeUsed","FinishTimeUsed","BugTimeUsed","RealTimeUsed"],
			["ترافیک سرویس","ترافیک سالیانه","ترافیک ماهیانه","ترافیک هفتگی","ترافیک روزانه","ترافیک اضافی","ترافیک استفاده شده سرویس","ترافیک استفاده شده سالیانه","ترافیک استفاده شده ماهیانه","ترافیک استفاده شده هفتگی","ترافیک استفاده شده روزانه","ترافیک استفاده شده اضافی","ترافیک استفاده شده در حالت اتمام","ترافیک استفاده شده در حالت اشکال","ترافیک ارسال واقعی","ترافیک دریافت واقعی","ترافیک باقی مانده سرویس","ترافیک باقی مانده سالیانه","ترافیک باقی مانده ماهیانه","ترافیک باقی مانده هفتگی","ترافیک باقی مانده روزانه","اضافه ترافیک باقیمانده"],
			["زمان سرویس","زمان سالیانه","زمان ماهیانه","زمان هفتگی","زمان روزانه","اضافه زمان","زمان باقیمانده سرویس","زمان استفاده شده سالیانه","زمان استفاده شده ماهیانه","زمان استفاده شده هفتگی","زمان استفاده شده روزانه","زمان استفاده شده اضافی","زمان استفاده شده در حالت اتمام","زمان استفاده شده در حالت اشکال","زمان استفاده شده واقعی","زمان باقیمانده سرویس","زمان باقیمانده سالیانه","زمان باقیمانده ماهیانه","زمان باقیمانده هفتگی","زمان باقیمانده روزانه","زمان باقیمانده اضافی"],
			["نام سرویس فعال","ارائه دهنده مجازی اینترنت","نماینده فروش","مرکز","پشتیبان","سرعت میکروتیک","دامنه آی پی","زمان ورود","فرمول تخفیف","اکتیودایرکتوری","وضعیت کاربر","وضعیت پورت","نوع کاربر","روش اعتبارسنجی"]
		];
	var TheFieldsItemsValue=[
			["Username","User_Id","Pass","PayBalance","Name","Family","Fathername","NationalCode","Organization","Nationality","Phone","AdslPhone","Mobile","Comment","NOE","InitialMonthOff","MaxPrepaidDebit"],
			["StartDate","EndDate","GiftEndDT","LastRequestDT","UserCDT","BirthDate","ExpirationDate"],
			["TrafficType","TimeType","URLReporting","Calendar","PeriodicUse"],
			// ["STrA","YTrA","MTrA","WTrA","DTrA","ETrA","STrU","YTrU","MTrU","WTrU","DTrU","ETrU","FinishUsedTr","BugUsedTr","RealSendTr","RealReceiveTr"],
            // ["STiA","YTiA","MTiA","WTiA","DTiA","ETiA","STiU","YTiU","MTiU","WTiU","DTiU","ETiU","FinishUsedTi","BugUsedTi","RealUsedTime"],
			["Tuu.STrA","Tuu.YTrA","Tuu.MTrA","Tuu.WTrA","Tuu.DTrA","Tuu.ETrA","Tuu.STrU","Tuu.YTrU","Tuu.MTrU","Tuu.WTrU","Tuu.DTrU","Tuu.ETrU","Tuu.FinishUsedTr","Tuu.BugUsedTr","Tuu.RealSendTr","Tuu.RealReceiveTr","Tuu.STrA-Tuu.STrU","Tuu.YTrA-Tuu.YTrU","Tuu.MTrA-Tuu.MTrU","Tuu.WTrA-Tuu.WTrU","Tuu.DTrA-Tuu.DTrU","Tuu.ETrA-Tuu.ETrU"],
            ["Tuu.STiA","Tuu.YTiA","Tuu.MTiA","Tuu.WTiA","Tuu.DTiA","Tuu.ETiA","Tuu.STiU","Tuu.YTiU","Tuu.MTiU","Tuu.WTiU","Tuu.DTiU","Tuu.ETiU","Tuu.FinishUsedTi","Tuu.BugUsedTi","Tuu.RealUsedTime","Tuu.STiA-Tuu.STiU","Tuu.YTiA-Tuu.YTiU","Tuu.MTiA-Tuu.MTiU","Tuu.WTiA-Tuu.WTiU","Tuu.DTiA-Tuu.DTiU","Tuu.ETiA-Tuu.ETiU"],
			["Service_Id","Visp_Id","Reseller_Id","Center_Id","Supporter_Id","MikrotikRate_Id","IPPool_Id","LoginTime_Id","OffFormula_Id","ActiveDirectory_Id","Status_Id","PortStatus","UserType","AuthMethod"]
		];

	var TheOptions=[
			["=","<>","<",">","<=",">=","مشابه","غیرمشابه"],
            ["=","<>","<",">","<=",">=","=DayInYear","=DayInMonth"],
            ["=","<>","<",">","<=",">="],
			["باشد","نباشد"],
            ["=","<>"]
		];
	var TheOptionsValue=[
			["E","NE","L","G","LE","GE","Like","notLike"],
            ["E","NE","L","G","LE","GE","DIY","DIM"],
            ["E","NE","L","G","LE","GE"],
			["in","notin"],
            ["E","NE"]
		];

	for(i=0;i<5;++i){
		ComparisionItems[i] = [];
		for (j=0;j<TheOptions[i].length;++j){
			ComparisionItems[i].push(
					{text: TheOptions[i][j], value: TheOptionsValue[i][j]}
			);
		}
	}

	//=======Popup1 Filter
	var Popup1;
	var PopupId1=['Filter'];//popup Attach to Which Buttom of Toolbar
	//=======Form1 Filter
	var Form1;
	var Form1PopupHelp;
	var Form1FieldHelp  = {	UserName:'UserName'};
	var Form1FieldHelpId=['UserName'];
    var Form1Str = [];


	var BlockTMP1 = [];
	var BlockTMP2 = [];

	BlockTMP1 = [];
	for(i=0;i<TheFieldsItems[0].length;i++)
		BlockTMP1.push({text: TheFieldsItemsFa[0][i], value: TheFieldsItemsValue[0][i]});

	Form1Str.push({type: "fieldset", name:"F0", width:840, label: "پیوستن به عملیات گروهی قبلی", list:[
		{type: "block", list:[
			{type: "checkbox",name:"Chk0",position:"absolute",checked:false, list:[
				{ type: "select", name:"PrevBatchProcess",connector: RenderFile+".php?"+un()+"&act=SelectBatchProcess",validate:"IsID",inputWidth:315},
				{ type: "input", name:"PrevBatchProcess2", value:"-لیست خالی-", style: "text-align:center",inputWidth:316, hidden: true},
				{type: "newcolumn", offset: 51},
				{type: "input", name: "PreviousBatchProcessDescription",label: "یادداشت:",inputWidth:317,readonly:true,rows: 2, inputHeight: 40}
			]}
		]}
	]});

	BlockTMP2 = [];
	for (i=1;i<=2;++i){
		BlockTMP2.push(
			{type: "block", list:[
				{type: "checkbox",name:"Chk1_"+i+"_1",position:"absolute",checked:false, list:[
					{type: "select", name: "Field1_"+i+"_1", inputWidth:130, options:BlockTMP1 },
					{type: "newcolumn"},
					{type: "select", name: "Comp1_"+i+"_1", options: ComparisionItems[0] , inputWidth:55, required:true},
					{type: "newcolumn"},
					{type: "input" , name: "Value1_"+i+"_1", maxLength:64,inputWidth:120} ,
					{type: "newcolumn", offset:15},
					{type: "checkbox",name:"Chk1_"+i+"_2",position:"absolute",checked:false, list:[
						{type: "radio", name: "Opt1_"+i, value: "AND", label: "و",checked:true},
						{type: "newcolumn"},
						{type: "radio", name: "Opt1_"+i, value: "OR", label: "یا"},
						{type: "newcolumn"},
						{type: "select", name: "Field1_"+i+"_2", inputWidth:130, options:BlockTMP1},
						{type: "newcolumn"},
						{type: "select", name: "Comp1_"+i+"_2", options: ComparisionItems[0] , inputWidth:55, required:true},
						{type: "newcolumn"},
						{type: "input" , name: "Value1_"+i+"_2", maxLength:64,inputWidth:120}
					]}
				]}
			]}
		);
	}
	Form1Str.push({type: "fieldset", name:"F1", width:840, label: "فیلتر 1-2", list:BlockTMP2});

	BlockTMP1 = [];
	for(i=0;i<TheFieldsItems[1].length;i++)
		BlockTMP1.push({text: TheFieldsItemsFa[1][i], value: TheFieldsItemsValue[1][i]});

	BlockTMP2 = [
		{type: "block", list:[
			{type: "checkbox",name:"Chk2_1_1",position:"absolute",checked:false, list:[
				{type: "select", name: "Field2_1_1", inputWidth:130, options:BlockTMP1 },
				{type: "newcolumn"},
				{type: "select", name: "Comp2_1_1", options: ComparisionItems[1] , inputWidth:55, required:true},
				{type: "newcolumn"},
				{type: "input" , name: "Value2_1_1", value: CurDate, maxLength:10,inputWidth:120,validate:"IsValidDate"} ,
				{type: "newcolumn", offset:15},
				{type: "checkbox",name:"Chk2_1_2",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt2_1", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt2_1", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field2_1_2", inputWidth:130, options:BlockTMP1},
					{type: "newcolumn"},
					{type: "select", name: "Comp2_1_2", options: ComparisionItems[1] , inputWidth:55, required:true},
					{type: "newcolumn"},
					{type: "input" , name: "Value2_1_2", value: CurDate, maxLength:10,inputWidth:120,validate:"IsValidDate"}
				]}
			]}
		]}
	];

	Form1Str.push({type: "fieldset", name:"F2", width:840, label: "فیلتر 1-3", list:BlockTMP2});

	BlockTMP2 = [
		{type: "block", list:[
			{type: "checkbox",name:"Chk3_1_1",position:"absolute",checked:false, list:[
				{type: "select", name: "Field3_1_1", inputWidth:130, options:[
					{text: "نوع ترافیک", value: "TrafficType"},
					{text: "نوع زمان", value: "TimeType"},
					{text: "گزارش صفحات بازدید شده", value: "URLReporting"},
					{text: "نوع محاسبه مصرف", value: "PeriodicUse"},
					{text: "تقویم", value: "Calendar"}
				]},
				{type: "newcolumn", offset:4},
				{type: "label", label:" = ",labelAlign:"center", labelWidth:51},
				{type: "newcolumn"},
				{type: "select", name: "TrafficType_1", inputWidth:118, options:[
					{text: "نامحدود", value: "Unlimit"},
					{text: "محدود", value: "Limit"},
					{text: "بدون سرویس فعال", value: "NoActiveService"}
				]},
				{type: "select", name: "TimeType_1", hidden:true, inputWidth:118, options:[
					{text: "نامحدود", value: "Unlimit"},
					{text: "محدود", value: "Limit"},
					{text: "بدون سرویس فعال", value: "NoActiveService"}
				]},
				{type: "select", name: "URLReporting_1", hidden:true, inputWidth:118, options:[
					{text: "بلی", value: "Yes"},
					{text: "خیر", value: "No"}
				]},
				{type: "select", name: "PeriodicUse_1", hidden:true, inputWidth:118, options:[
					{text: "از ابتدای هفته", value: "Fix"},
					{text: "نسبت به روز فعالسازی", value: "Relative"}
				]},
				{type: "select", name: "Calendar_1", hidden:true, inputWidth:118, options:[
					{text: "جلالی", value: "Jalali"},
					{text: "میلادی", value: "Gregorian"}
				]},
				{type: "newcolumn", offset:15},//,style:"border:1px solid"
				{type: "checkbox",name:"Chk3_1_2",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt3_1", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt3_1", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field3_1_2", inputWidth:130, options:[
						{text: "نوع ترافیک", value: "TrafficType"},
						{text: "نوع زمان", value: "TimeType"},
						{text: "گزارش صفحات بازدید شده", value: "URLReporting"},
						{text: "نوع محاسبه مصرف", value: "PeriodicUse"},
						{text: "تقویم", value: "Calendar"}
					]},
					{type: "newcolumn",offset:4},
					{type: "label", label:" = ",labelAlign:"center", labelWidth:51},
					{type: "newcolumn"},
					{type: "select", name: "TrafficType_2", inputWidth:118, options:[
						{text: "نامحدود", value: "Unlimit"},
						{text: "محدود", value: "Limit"},
						{text: "نسبت به روز فعالسازی", value: "NoActiveService"}
					]},
					{type: "select", name: "TimeType_2", hidden:true, inputWidth:118, options:[
						{text: "نامحدود", value: "Unlimit"},
						{text: "محدود", value: "Limit"},
						{text: "نسبت به روز فعالسازی", value: "NoActiveService"}
					]},
					{type: "select", name: "URLReporting_2", hidden:true, inputWidth:118, options:[
						{text: "بلی", value: "Yes"},
						{text: "خیر", value: "No"}
					]},
					{type: "select", name: "PeriodicUse_2", hidden:true, inputWidth:118, options:[
						{text: "از ابتدای هفته", value: "Fix"},
						{text: "نسبت به روز فعالسازی", value: "Relative"}
					]},
					{type: "select", name: "Calendar_2", hidden:true, inputWidth:118, options:[
						{text: "جلالی", value: "Jalali"},
						{text: "میلادی", value: "Gregorian"}
					]}
				]}
			]}
		]}
	];

	//Form1Str.push({type: "fieldset", name:"F3" , width:840, label: "Filter 1-4", list:BlockTMP2});

	//BlockTMP2 = [];

	BlockTMP1 = [];
	for(i=0;i<TheFieldsItems[3].length;i++)
		BlockTMP1.push({text: TheFieldsItemsFa[3][i], value: TheFieldsItemsValue[3][i]});

	BlockTMP2.push(
		{type: "block", disabled: true, name:"TrafficTypeLimitBlock", list:[
			{type: "checkbox", name:"Chk4_1_1",position:"absolute",checked:false, list:[
				{type: "select", name: "Field4_1_1", inputWidth:130, options:BlockTMP1 },
				{type: "newcolumn"},
				{type: "select", name: "Comp4_1_1", options: ComparisionItems[2] , inputWidth:55, required:true},
				{type: "newcolumn"},
				{type: "input" , name: "Value4_1_1", value: 0, maxLength: 9, inputWidth: 69, validate: "NotEmpty,ValidInteger"} ,
				{type: "newcolumn"},
				{type: "label", label:"مگابایت", labelWidth:60},
				{type: "newcolumn"},
				{type: "checkbox",name:"Chk4_1_2",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt4_1", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt4_1", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field4_1_2", inputWidth:130, options:BlockTMP1},
					{type: "newcolumn"},
					{type: "select", name: "Comp4_1_2", options: ComparisionItems[2] , inputWidth:55, required:true},
					{type: "newcolumn"},
					{type: "input" , name: "Value4_1_2", value: 0, maxLength: 9,inputWidth: 90, validate: "NotEmpty,ValidInteger"},
					{type: "newcolumn"},
					{type: "label", label:"مگابایت", labelWidth:39},
				]}
			]}
		]}
	);

	BlockTMP1 = [];
	for(i=0;i<TheFieldsItems[4].length;i++)
		BlockTMP1.push({text: TheFieldsItemsFa[4][i], value: TheFieldsItemsValue[4][i]});
	BlockTMP2.push(
		{type: "block", disabled: true, name:"TimeTypeLimitBlock", list:[
			{type: "checkbox", name:"Chk4_2_1",position:"absolute",checked:false, list:[
				{type: "select", name: "Field4_2_1", inputWidth:130, options:BlockTMP1 },
				{type: "newcolumn"},
				{type: "select", name: "Comp4_2_1", options: ComparisionItems[2] , inputWidth:55, required:true},
				{type: "newcolumn"},
				{type: "input" , name: "Value4_2_1", value: 0, maxLength:9, inputWidth: 69, validate: "NotEmpty,ValidInteger"} ,
				{type: "newcolumn"},
				{type: "label", label:"ثانیه", labelWidth:60},
				{type: "newcolumn"},
				{type: "checkbox",name:"Chk4_2_2",position:"absolute",checked:false, list:[
					{type: "radio", name: "Opt4_2", value: "AND", label: "و",checked:true},
					{type: "newcolumn"},
					{type: "radio", name: "Opt4_2", value: "OR", label: "یا"},
					{type: "newcolumn"},
					{type: "select", name: "Field4_2_2", inputWidth:130, options:BlockTMP1},
					{type: "newcolumn"},
					{type: "select", name: "Comp4_2_2", options: ComparisionItems[2] , inputWidth:55, required:true},
					{type: "newcolumn"},
					{type: "input" , name: "Value4_2_2", value: 0, maxLength:9, inputWidth: 90, validate: "NotEmpty,ValidInteger"},
					{type: "newcolumn"},
					{type: "label", label:"ثانیه", labelWidth:39},
				]}
			]}
		]}
	);
	Form1Str.push({type: "fieldset", name:"F4", width:840, label: "فیلتر 1-5", list:BlockTMP2});



	BlockTMP2 = [];
	for (i=1;i<=2;++i){
		BlockTMP1 = [];
		for(j=0;j<TheFieldsItems[5].length-3;j++){
			BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 107, name:TheFieldsItemsValue[5][j]+"_"+i,
							connector: RenderFile+".php?"+un()+"&act=Select"+TheFieldsItems[5][j], inputWidth: 630,
							note: {text: "می توانید تعداد بیشتری را انتخاب کنید Ctrl و Shift با کلید های"}
						}
				]}
			);
		}

		BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 107, name:TheFieldsItemsValue[5][j]+"_"+i, inputWidth: 630,
							options:[
								{text: "در حال انتظار", value: "Waiting"},
								{text: "رزرو", value: "Reserve"},
								{text: "در حال جمع آوری", value: "GoToBusy"},
								{text: "جمع آوری شده", value: "Busy"},
								{text: "در حال آزادسازی", value: "GoToFree"},
								{text: "آزاد شده", value: "Free"},
								{text: "هیچکدام", value: "None"}
							],
							note: {text: "می توانید تعداد بیشتری را انتخاب کنید Ctrl و Shift با کلید های"}
						}
				]}
			);
		j++;
		BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 107, name:TheFieldsItemsValue[5][j]+"_"+i, inputWidth: 630,
							options:[
								{text: "ADSL", value: "ADSL"},
								{text: "Dialup", value: "Dialup"},
								{text: "Dialup-PRM", value: "Dialup-PRM"},
								{text: "LAN", value: "LAN"},
								{text: "Wireless", value: "Wireless"},
								{text: "Wi-Fi", value: "Wi-Fi"},
								{text: "WiFiMobile", value: "WiFiMobile"},
								{text: "NotLog", value: "NotLog"}
							],
							note: {text: "با کلید های Ctrl و Shift می توانید تعداد بیشتری را انتخاب کنید."}
						}
				]}
			);
		j++;
		BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 107, name:TheFieldsItemsValue[5][j]+"_"+i, inputWidth: 630,
							options:[
								{text: "Username-Password", value: "UP"},
								{text: "Username-CallerId", value: "UC"},
								{text: "Username", value: "U"},
								{text: "Username-Password-CallerId", value: "UPC"},
								{text: "ActiveDirectory", value: "A"}
							],
							note: {text: "با کلید های Ctrl و Shift می توانید تعداد بیشتری را انتخاب کنید."}
						}
				]}
			);


		BlockTMP2.push(
			{type: "block", list:[
				{type: "checkbox",name:"Chk5_"+i,position:"absolute",checked:false, list:[
					{type: "select", name: "Field5_"+i, inputWidth:150, options:BlockTMP1}
				]}
			]}
		);
	}

	Form1Str.push(
		{type: "fieldset", name:"F5", hidden:true, width:840, label: "فیلتر 2", list:BlockTMP2},
		{type: "block", width: 840, list:[
				{type: "button",name: "FilterByFile",value: " فیلتر توسط فایل ",width :90},
				{type: "newcolumn", offset:5},
				{type:"label",labelWidth:140,name:"FilterByFileLabel",label:""},
				{type: "newcolumn", offset:40},
				{type: "button",name: "Cancel",value: " لغو ",width :80},
				{type: "newcolumn", offset:20},
				{type: "button",name: "Back",value: " بازگشت ", disabled:true, width :80},
				{type: "newcolumn", offset:0},
				{type: "button",name: "Next",value: " بعدی ",width :80},
				{type: "newcolumn", offset:10},
				{type: "select", label:"ستون ثابت:", name: "SplitColumnsCount",labelAlign:"right",labelWidth:80, inputWidth:35, options:[
					{text: '0', value: 0,selected:true},
					{text: '1', value: 1},
					{text: '2', value: 2},
					{text: '3', value: 3},
					{text: '4', value: 4},
					{text: '5', value: 5}
				], hidden: true},
				{type: "newcolumn", offset:10},
				{type: "button", name: "DoFiltering", disabled:true, hidden:true, value:"در حال بارگذاری("+OptionCount+")",width :90}
		]}
	);

	//=======Popup2 GoToBatchProcess
	var Popup2;
	var PopupId2=['GoToBatchProcess'];//popup Attach to Which Buttom of Toolbar
	//=======Form2 GoToBatchProcess
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	UserName:'UserName'};
	var Form2FieldHelpId=['UserName'];
    var Form2Str;

	//=======Popup3 FilterByFile
	var Popup3;
	var PopupId3=['FilterByFile'];
	//=======Form3 FilterByFile
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {FileUploader:""};
	var Form3FieldHelpId=['FileUploader','Delimiter'];
    var Form3Str=[
		{type: "fieldset", label: "بارگذاری فایل<span style='color:indianred;font-size:85%;float:left'>(محتوا فایل فقط شامل نام های کاربری باشد)</span>", width:360, list: [
			{
			type: "upload",
			name: "FileUploader",
			mode: "html5",
			inputWidth: 320,
			inputHeight: 60,
			titleScreen: true,
			autoStart: true,
			autoRemove:false,
			info:true,
			titleText :"کلیک کنید یا فایلها را اینجا بکشید<br/>("+"<?php echo min(ini_get("upload_max_filesize"),ini_get("post_max_size"));?>"+" به ازای هر فایل حداکثر)",
			url: RenderFile+".php?"+un()+"&act=UploadFile"
			},
			{type: "label", label: "هر نام کاربری را در یک خط وارد کنید", labelWidth:320},
		]},
		{type: "block", width: 380, list:[
			{type: "button",name:"Remove",value: " حذف ",width :80,disabled:true},
			{type: "newcolumn", offset:80},
			{type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:10},
			{type: "button",name:"Proceed",value: " برو ",width :80}
		]}
	];




	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	AddPopupFilter();
	AddPopupGoToBatchProcess();


	if(!(!!window.chrome && !!window.chrome.webstore))
		dhtmlx.alert({title: "هشدار",type: "alert-warning",text: "<span style='color:red'> توصیه می شود که <br/><span style='font-weight:bold'>از گوگل کروم بروز شده</span><br/>برای عملیات گروهی استفاده کنید</span>", callback:function(){setTimeout(DoSomthing1,200)}
	,ok:"باشه"});
	else
		DoSomthing1();


	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);



//FUNCTION========================================================================================================================
function DoSomthing1(){
	if(IsDebugEnable>0)
		dhtmlx.alert({title: "هشدار",type: "alert-error",text: "حالت اشکال زدایی فعال است و ممکن است باعث بار اضافی روی سرور شود<br/>را در کنسول اجرا نمایید pak.www.nodebug", callback:function(){setTimeout(DoSomthing2,200)},ok:"باشه"
		});
	else
		DoSomthing2();
}

function DoSomthing2(){
	var loader = dhtmlxAjax.getSync(RenderFile+".php?"+un()+"&act=TakingCare");
	response=loader.xmlDoc.responseText;
	response=CleanError(response);
	ResArray=response.split("~");
	if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
	else if(ResArray[0]!='OK') dhtmlx.alert("خطا، "+response);
	else{
		if((ResArray[1]>=200)||(ResArray[2]>800000))
			dhtmlx.alert({title: "هشدار",type: "alert-error",text: "تعداد رکوردهای تاریخچه عملیات گروهی زیاد شده و باعت کند شدن عملیات گروهی می شود.لطفا چند رکورد را حذف نمایید.از مسیر<br/>مدیریت->عملیات گروهی->تاریخچه",ok:"بستن", callback:function(){setTimeout(function(){Popup1.show("Filter")},200)}});
		else if((ResArray[1]>=100)||(ResArray[2]>400000))
			dhtmlx.alert({title: "هشدار",type: "alert-warning",text: "تعداد رکوردهای تاریخچه عملیات گروهی زیاد شده و باعت کند شدن عملیات گروهی می شود.لطفا چند رکورد را حذف نمایید.از مسیر<br/>مدیریت->عملیات گروهی->تاریخچه",ok:"بستن", callback:function(){setTimeout(function(){Popup1.show("Filter")},200)}});
		else
			Popup1.show("Filter");
	}
}

//-------------------------------------------------------------------AddPopupFilter()
function AddPopupFilter(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Filter","تنظیم فیلتر","tow_Filter");
	ToolbarOfGrid.setItemToolTip("Filter","فیلترها را برای ایجاد گزارش تنظیم کنید");
	ToolbarOfGrid.disableItem('Filter');

	Popup1=DSInitialPopup(ToolbarOfGrid,PopupId1,Popup1OnShow);
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);

	Form1.attachEvent("onChange",Form1OnChange);
	ToolbarOfGrid.disableItem('Filter');
	Form1.attachEvent("onOptionsLoaded", function(name){
		if(name=="PrevBatchProcess"){
			if(Form1.getItemValue("PrevBatchProcess")<=0){
				Form1.disableItem("F0");
				Form1.hideItem("PrevBatchProcess");
				Form1.showItem("PrevBatchProcess2");
			}
			else
				Form1OnChange("PrevBatchProcess",0);
		}
		OptionCount--;
		if(OptionCount<=0){
			setTimeout(function(){
				Form1.setItemLabel("DoFiltering","برو به گزارش");
				Form1.enableItem("DoFiltering");
				ToolbarOfGrid.enableItem('Filter');
			},500);
		}
		else
			Form1.setItemLabel("DoFiltering","در حال بارگذاری("+OptionCount+")");
	});
	Steps=1;
	Form1.setItemValue("Field5_2",TheFieldsItemsValue[5][1]);//to set second selectbox to second option on page two

	Popup3 = new dhtmlXPopup({form: Form1,id:["FilterByFile"],mode:"right"});
	Popup3.attachEvent("onShow",Popup3OnShow);
	Popup1.attachEvent("onHide",function(){if(Popup3.isVisible()) Popup3.hide();});
}

//-------------------------------------------------------------------Popup1OnShow()
function Popup1OnShow(){
	if(!ISValidResellerSession()) return;
}

//-------------------------------------------------------------------Form1OnChange(id, value)
function Form1OnChange(id,value){
	//alert("id:'"+id+"'     value:'"+value+"'");
	if(id=="PrevBatchProcess"){
		PrevBatchProcess=Form1.getItemValue("PrevBatchProcess");
		dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetPreviousBatchProcessComment&PrevBatchProcess="+PrevBatchProcess,
			function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if((response=='')||(response[0]=='~'))
					dhtmlx.alert("خطا، "+response.substring(1));
				else
					Form1.setItemValue("PreviousBatchProcessDescription",response);
			}
		);
	}
	else if((id=='Field3_1_1')||(id=='Field3_1_2')||(id=='Chk3_1_1')||(id=='Chk3_1_2')
		||(id=='TrafficType_1')||(id=='TrafficType_2')||(id=='TimeType_1')||(id=='TimeType_2')){
		var HideItems=[];
		var ShowItems=[];
		if(id=='Field3_1_1'){
			HideItems=['TrafficType_1','TimeType_1','URLReporting_1','PeriodicUse_1','Calendar_1'];
			ShowItems=[value+'_1'];
			FormHideItem(Form1,HideItems);
			FormShowItem(Form1,ShowItems);
			}
		else if(id=='Field3_1_2'){
			HideItems=['TrafficType_2','TimeType_2','URLReporting_2','PeriodicUse_2','Calendar_2'];
			ShowItems=[value+'_2'];
			FormHideItem(Form1,HideItems);
			FormShowItem(Form1,ShowItems);
		}


		Form1.disableItem('TrafficTypeLimitBlock');
		Form1.disableItem('TimeTypeLimitBlock');
		if(Form1.getItemValue('Chk3_1_1')){
			var TMP;
			TMP=Form1.getItemValue('Field3_1_1');
			if( ((TMP=='TrafficType')||(TMP=='TimeType')) && (Form1.getItemValue(TMP+'_1')=='Limit') ){
					Form1.enableItem(TMP+'LimitBlock');
			}
			if(Form1.getItemValue('Chk3_1_2')){
				TMP=Form1.getItemValue('Field3_1_2');
				if( ((TMP=='TrafficType')||(TMP=='TimeType')) && (Form1.getItemValue(TMP+'_2')=='Limit') ){
					Form1.enableItem(TMP+'LimitBlock');
				}
			}
		}
	}
}

//-------------------------------------------------------------------Form1OnButtonClick(name)
function Form1OnButtonClick(name){
	if(name=='Cancel') {
		Popup1.hide();
	}
	else if(name=='Next') {
		if(Steps==1){
			var ShowItems=['F5','SplitColumnsCount','DoFiltering'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['F0','F1','F2'/*,'F3'*/,'F4'];
			FormHideItem(Form1,HideItems);

			Form1.enableItem("Back");
			Form1.disableItem("Next");
			Steps=2;
		}
	}
	else if(name=='Back') {
		if(Steps==2){
			var ShowItems=['F0','F1','F2'/*,'F3'*/,'F4'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['F5'];
			FormHideItem(Form1,HideItems);

			Form1.enableItem("Next");
			Form1.disableItem("Back");
			Steps=1;
		}
	}
	else if(name=='DoFiltering'){
		//if(DSFormValidate(Form1,Form1FieldHelpId)){
			Form1.disableItem("DoFiltering");
			ToolbarOfGrid.disableItem('GoToBatchProcess');
			Popup1.hide();
			dhxLayout.progressOn();
			MyGetString=CreatePostStr();
			dhtmlxAjax.get(
				RenderFile+".php?"+un()+"&act=list&req=GetUserCount&SortField=&SortOrder="+MyGetString+FilterByFileString,
				function(loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
					else if(response=='0'){
						if(typeof mygrid != "undefined")
							mygrid.clearAll(true);
						dhtmlx.alert("هیچ کاربری با فیلتر انتخابی یافت نشد");
						//setTimeout(function(){Popup1.show("Filter")},500);
					}
					else{
						UserCount=parseInt(response);
						SetGridLayout();
						InititalGridList();
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+MyGetString+FilterByFileString,DoAfterRefresh);
						ToolbarOfGrid.enableItem('GoToBatchProcess');
					}
					Form1.enableItem("DoFiltering");
					dhxLayout.progressOff();
				}
			);
		//}
	}
	else if(name=='FilterByFile'){
		Popup3.show("FilterByFile");
	}
}

//-------------------------------------------------------------------CreateForm2Str(UserCount)
function CreateForm2Str(UserCount){
	BlockTMP1=[];
	i=1;
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "AddService", label: (i++)+"<span style='padding-left: 100px'> : افزودن سرویس</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "AddPayment", label: (i++)+"<span style='padding-left: 37px'> : افزودن یا تنظیم مجدد پرداخت</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "CancelService", label: (i++)+"<span style='padding-left: 91px'> : لغو سرویس کاربر</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "ResetExtraCredit", label: (i++)+"<span style='padding-left: 30px'> : تنظیم مجدد اعتبار اضافی کاربر</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "ChangeInfo", label: (i++)+"<span style='padding-left: 84px'> : تغییر اطلاعات کاربر</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "SendSMS", label: (i++)+"<span style='padding-left: 110px'> : ارسال پیامک</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "DeleteUser", label: (i++)+"<span style='padding-left: 111px'>: حذف کاربران</span>"});
		BlockTMP1.push({type: "radio", name: "BatchItem", offsetTop: 5, value: "WebMessage", label: (i++)+"<span style='padding-left: 17px'> : ارسال/حذف پیام تحت وب کاربران</span>"});

	BlockTMP1.push({type: "label", label: ""});
	Form2Str = [
		{type:"settings" , position: "label-right",offsetLeft:0},
		{type: "fieldset", label: "انجام عملیات برای "+UserCount+" کاربر", name: "F0", width: 325, list:[{type: "block",  list:BlockTMP1}]},
		{type: "fieldset", label: "نام عملیات گروهی", name: "F1", width: 325, list:[
			{type: "block",  list:[
				{type: "hidden", name:"IsUserEnteredName", value:"0"},
				{type: "input" , position: "label-top", name: "BatchName", label:"نام عملیات گروهی : ",labelHeight:20, value:"", maxLength: 64,rows: 2,inputWidth: 230,
				note:{text:"این نام می تواند در آینده قابل رجوع باشد"}}
			]}
		], disabled: true},
		{type: "block", width: 320, list:[
			{type: "newcolumn", offset:110},
			{ type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Proceed",value: "برو", width :80, disabled: true}

		]}
	];
	return true;
}

//-------------------------------------------------------------------Popup3OnShow()
function Popup3OnShow(){
	if(typeof Form3 != "undefined")
		Form3.unload();
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
	if(FilterByFileString!="")
		Form3.enableItem("Remove");
}

//-------------------------------------------------------------------Form3OnButtonClick()
function Form3OnButtonClick(name){
	if(name=='Close') {
		Popup3.hide();
	}
	else if(name=='Remove') {
		Form1.setItemLabel("FilterByFileLabel","");
		FilterByFileString="";
		Popup3.hide();
	}
	else{
		var UploadFileStatus=Form3.getUploaderStatus("FileUploader");
		if(UploadFileStatus==-1){
			alert("Error in uploaded file. Check Upload status");
			return false;
		}
		else if(UploadFileStatus==0){
			alert("حداقل یک فایل را انتخاب کنید");
			return false;
		}
		Popup3.hide();

		dhxLayout.cells("a").progressOn();
		Form3.lock();
		Form3.send(RenderFile+".php?"+un()+"&act=SubmitFiles","post",function(loader, response){
			Form3.unlock();
			dhxLayout.cells("a").progressOff();
			response=CleanError(response);
			var responsearray=response.split("~",5);
			if(response==""){
				parent.dhtmlx.alert("خطا، درخواست هیچ چیزی دربرنداشت");
			}
			else if(responsearray[0]!='OK'){
				if(response[0]=='~') parent.dhtmlx.alert(response.substring(1));//"Error,"+response.substring(1)
				else parent.dhtmlx.alert(response);
			}
			else{
				var MSG=responsearray[1]+" user(s) detected in file"+((responsearray[1]>1)?"s.":".");
				if(responsearray[1]!=responsearray[2])
					MSG+="<br/>"+responsearray[2]+" user"+((responsearray[2]>1)?"s are":" is")+" unique.";
				if(responsearray[2]!=responsearray[3])
					MSG+="<br/>"+responsearray[3]+" user"+((responsearray[3]>1)?"s are":" is")+" common.";
				parent.dhtmlx.alert(MSG);
				Form1.setItemLabel("FilterByFileLabel",responsearray[2]+" user"+((responsearray[2]>1)?"s are":" is ")+" in file");
				FilterByFileString="&FilterFile="+responsearray[4];
				Form1.showItem("SplitColumnsCount");
				Form1.showItem("DoFiltering");
			}
		});

	}
}

//-------------------------------------------------------------------AddPopupGoToBatchProcess()
function AddPopupGoToBatchProcess(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"GoToBatchProcess","انجام عملیات گروهی","tow_GoToBatchProcess");
	ToolbarOfGrid.setItemToolTip("GoToBatchProcess","چه کاری را برای کاربران فیلترشده انتخاب می کنید");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	ToolbarOfGrid.disableItem('GoToBatchProcess');
}

//-------------------------------------------------------------------Popup2OnShow()
function Popup2OnShow(){
	if(!ISValidResellerSession()) return;
	CreateForm2Str(UserCount);

	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2OnChange);
}

//-------------------------------------------------------------------Form2OnChange(id, value)
function Form2OnChange(id,value){
	//alert("id:'"+id+"'     value:'"+value+"'");
	if(id=='BatchItem'){
		Form2.enableItem("F1");
		Form2.enableItem("Proceed");
		if(Form2.getItemValue("IsUserEnteredName")==0)
			Form2.setItemValue("BatchName",value+"-"+TNow+"-N="+UserCount+(BatchCountOnThisFilter>1?("("+BatchCountOnThisFilter+")"):""));
	}
	else if(id="BatchName")
		Form2.setItemValue("IsUserEnteredName",1);
}

//-------------------------------------------------------------------Form2OnButtonClick(name)
function Form2OnButtonClick(name){
	var BatchItem="";
	var BatchName="";
	var BatchProcess_Id=0;
	if(name=='Close') {
		Popup2.hide();
	}
	else{
		Popup2.hide();

		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm-warning",
			ok: "بلی",
			cancel: "خیر",
			text: "مطمئن هستید که میخواهید با "+UserCount+" کاربر ادامه دهید؟",
			callback: function(Result){
				if(Result){
					dhxLayout.progressOn();
					BatchItem=Form2.getItemValue("BatchItem");
					BatchName=Form2.getItemValue("BatchName");
					if(BatchName=="")
						BatchName=BatchItem+"-"+TNow+"-N="+UserCount+(BatchCountOnThisFilter>1?("("+BatchCountOnThisFilter+")"):"");
					dhtmlxAjax.get(
						RenderFile+".php?"+un()+"&act=list&req=PrepareBatchProcess&BatchName="+BatchName+"&BatchItem="+BatchItem+"&SortField=User_Id&SortOrder=asc"+MyGetString+FilterByFileString,
						function(loader){
							response=loader.xmlDoc.responseText;
							response=CleanError(response);
							ResArray=response.split("~");
							if((response=='')||(response[0]=='~'))
								dhtmlx.alert("خطا، "+response.substring(1));
							else if(ResArray[0]!="OK")
								dhtmlx.alert(response);
							else{
								BatchCountOnThisFilter++;
								BatchProcess_Id=parseInt(ResArray[1]);
								if(UserCount!=ResArray[2]){
									UserCount=ResArray[2];
									LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+MyGetString+FilterByFileString,DoAfterRefresh);
									PopupBatchProcessWindow(BatchItem,BatchProcess_Id);
									alert("تعداد کاربران از آخرین فیلتر به "+UserCount+" کاربر تغییر کرده است");
								}
								else
									PopupBatchProcessWindow(BatchItem,BatchProcess_Id);
							}
							dhxLayout.progressOff();
						}
					);
				}
				else
					dhtmlx.message("Try other filters");
			}
		});
	}
}

//-------------------------------------------------------------------CreatePostStr()
function CreatePostStr(){
	var Out='';
	var f11='',f12='',f21='',f22='',tmp='';
	if(Form1.getItemValue("Chk0")){
		Out+="&Chk0=1&Value0="+Form1.getItemValue("PrevBatchProcess");
	}
	else
		Out+="&Chk0=0";

	for(i=1;i<=2;++i){
		if(Form1.getItemValue("Chk1_"+i+"_1")){
			Out+="&Chk1_"+i+"_1=1";
			Out+="&Field1_"+i+"_1="+Form1.getItemValue("Field1_"+i+"_1");
			Out+="&Comp1_"+i+"_1="+Form1.getItemValue("Comp1_"+i+"_1");
			Out+="&Value1_"+i+"_1="+Form1.getItemValue("Value1_"+i+"_1");
			if(Form1.getItemValue("Chk1_"+i+"_2")){
				Out+="&Chk1_"+i+"_2=1";
				Out+="&Opt1_"+i+"="+Form1.getItemValue("Opt1_"+i);
				Out+="&Field1_"+i+"_2="+Form1.getItemValue("Field1_"+i+"_2");
				Out+="&Comp1_"+i+"_2="+Form1.getItemValue("Comp1_"+i+"_2");
				Out+="&Value1_"+i+"_2="+Form1.getItemValue("Value1_"+i+"_2");
			}
			else
				Out+="&Chk1_"+i+"_2=0";
		}
		else
			Out+="&Chk1_"+i+"_1=0";
	}
	if(Form1.getItemValue("Chk2_1_1")){
		Out+="&Chk2_1_1=1";
		Out+="&Field2_1_1="+Form1.getItemValue("Field2_1_1");
		Out+="&Comp2_1_1="+Form1.getItemValue("Comp2_1_1");
		Out+="&Value2_1_1="+Form1.getItemValue("Value2_1_1");
		if(Form1.getItemValue("Chk2_1_2")){
			Out+="&Chk2_1_2=1";
			Out+="&Opt2_1="+Form1.getItemValue("Opt2_1");
			Out+="&Field2_1_2="+Form1.getItemValue("Field2_1_2");
			Out+="&Comp2_1_2="+Form1.getItemValue("Comp2_1_2");
			Out+="&Value2_1_2="+Form1.getItemValue("Value2_1_2");
		}
		else
			Out+="&Chk2_1_2=0";
	}
	else
		Out+="&Chk2_1_1=0";

	if(Form1.getItemValue("Chk3_1_1")){
		Out+="&Chk3_1_1=1";
			f11=Form1.getItemValue("Field3_1_1");
		Out+="&Field3_1_1="+f11;
		Out+="&Comp3_1_1=E";
			f12=Form1.getItemValue(f11+"_1");
		Out+="&Value3_1_1="+f12;
		if(Form1.getItemValue("Chk3_1_2")){
			Out+="&Chk3_1_2=1";
			Out+="&Opt3_1="+Form1.getItemValue("Opt3_1");
				f21=Form1.getItemValue("Field3_1_2");
			Out+="&Field3_1_2="+f21;
			Out+="&Comp3_1_2=E";
				f22=Form1.getItemValue(f21+"_2");
			Out+="&Value3_1_2="+f22;
		}
		else
			Out+="&Chk3_1_2=0";
	}
	else
		Out+="&Chk3_1_1=0";

	if(((f11=="TrafficType")&&(f12=="Limit"))||((f21=="TrafficType")&&(f22=="Limit"))){
		if(Form1.getItemValue("Chk4_1_1")){
			Out+="&Chk4_1_1=1";
			Out+="&Field4_1_1="+Form1.getItemValue("Field4_1_1");
			Out+="&Comp4_1_1="+Form1.getItemValue("Comp4_1_1");
			Out+="&Value4_1_1="+Form1.getItemValue("Value4_1_1");
			if(Form1.getItemValue("Chk4_1_2")){
				Out+="&Chk4_1_2=1";
				Out+="&Opt4_1="+Form1.getItemValue("Opt4_1");
				Out+="&Field4_1_2="+Form1.getItemValue("Field4_1_2");
				Out+="&Comp4_1_2="+Form1.getItemValue("Comp4_1_2");
				Out+="&Value4_1_2="+Form1.getItemValue("Value4_1_2");
			}
			else
				Out+="&Chk4_1_2=0";
		}
		else
			Out+="&Chk4_1_1=0";
	}
	else
		Out+="&Chk4_1_1=0";

	if(((f11=="TimeType")&&(f12=="Limit"))||((f21=="TimeType")&&(f22=="Limit"))){
		if(Form1.getItemValue("Chk4_2_1")){
			Out+="&Chk4_2_1=1";
			Out+="&Field4_2_1="+Form1.getItemValue("Field4_2_1");
			Out+="&Comp4_2_1="+Form1.getItemValue("Comp4_2_1");
			Out+="&Value4_2_1="+Form1.getItemValue("Value4_2_1");
			if(Form1.getItemValue("Chk4_2_2")){
				Out+="&Chk4_2_2=1";
				Out+="&Opt4_2="+Form1.getItemValue("Opt4_2");
				Out+="&Field4_2_2="+Form1.getItemValue("Field4_2_2");
				Out+="&Comp4_2_2="+Form1.getItemValue("Comp4_2_2");
				Out+="&Value4_2_2="+Form1.getItemValue("Value4_2_2");
			}
			else
				Out+="&Chk4_2_2=0";
		}
		else
			Out+="&Chk4_2_1=0";
	}
	else
		Out+="&Chk4_2_1=0";


	if(Form1.getItemValue("Chk5_1")){
		Out+="&Chk5_1=1";
			f11=Form1.getItemValue("Field5_1");
		Out+="&Field5_1="+f11;
		Out+="&Comp5_1="+Form1.getItemValue("Comp5_"+f11+"_1");
		Out+="&Value5_1="+Form1.getItemValue(f11+"_1");;
	}
	else
		Out+="&Chk5_1=0";
	if(Form1.getItemValue("Chk5_2")){
		Out+="&Chk5_2=1";
			f21=Form1.getItemValue("Field5_2");
		Out+="&Field5_2="+f21;
		Out+="&Comp5_2="+Form1.getItemValue("Comp5_"+f21+"_2");
		Out+="&Value5_2="+Form1.getItemValue(f21+"_2");
	}
	else
		Out+="&Chk5_2=0";
	return Out;

}

//-------------------------------------------------------------------SetGridLayout()
function SetGridLayout(){
	ISSort=true;
	GFooter="";
	GColIds="User_Id,Username,Name,Family,Reseller,Visp,Center,Supporter,StatusName,UserStatus,PortStatus,StartDate,EndDate,PayBalance,ServiceName";
	GColHeaders="{#stat_count} ردیف,نام کاربری,نام,نام خانوادگی,نماینده فروش,ارائه دهنده مجازی اینترنت,مرکز,پشتیبان,نام وضعیت,وضعیت کاربر,وضعیت پورت,تاریخ شروع,تاریخ پایان,تراز مالی,نام سرویس";
	GColInitWidths="100,120,120,120,120,140,120,120,120,80,80,130,130,120,250";
	GColAligns="center";
	GColTypes="ro";
	GColSorting="server";
	GColVisibilitys=[1];
	ColSortIndex=0;
	SortDirection='asc';
	HeaderAlignment=["text-align:center"];

	for(i=1;i<=14;++i){
		GColAligns+=",center";
		GColTypes+=",ro";
		GColSorting+=",server";
		GColVisibilitys.push(1);
		HeaderAlignment.push("text-align:center");
	}
}

//-------------------------------------------------------------------InititalGridList()
function InititalGridList(){
	mygrid =dhxLayout.cells("a").attachGrid();
	mygrid.setSkin(grid_main_skin);
    mygrid.setImagePath(grid_image_path);
	mygrid.setColumnIds(GColIds);
    mygrid.setHeader(GColHeaders,null,HeaderAlignment);
    mygrid.setInitWidths(GColInitWidths);
    mygrid.setColAlign(GColAligns);
	mygrid.setColTypes(GColTypes);

	if (ISSort) mygrid.setColSorting(GColSorting);

	mygrid.init();
    if (ISSort){
		mygrid.setSortImgState(true,ColSortIndex,SortDirection);
		mygrid.attachEvent("onBeforeSorting",function(ind,type,direction){
			mygrid.setSortImgState(true,ind,direction);
			ColSortIndex=ind;
			SortDirection=((direction=='asc')?'asc':'desc');
			LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+MyGetString+FilterByFileString,DoAfterRefresh);
		});
	}
	var FreezeCount=Form1.getItemValue("SplitColumnsCount");
	if(FreezeCount>0) mygrid.splitAt(FreezeCount);
	mygrid.enableSmartRendering(true,100);
	mygrid.attachEvent("onRowDblClicked",function(id){PopupUserWindow(id)});
}

//-------------------------------------------------------------------PopupUserWindow(SelectedRowId)
function PopupUserWindow(SelectedRowId){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"User");
	var User_Id=mygrid.cells(SelectedRowId,mygrid.getColIndexById("User_Id")).getValue();
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=User_Id,"+User_Id, false);
}

//-------------------------------------------------------------------PopupUserWindow(SelectedRowId)
function PopupBatchProcessWindow(BatchItem,BatchProcess_Id){
	parent.BatchProcessInstanceCount++;
	OnTabClose_EventId=parent.tabbar.attachEvent("onTabClose", function(id){
		if(id==tabcount){
			parent.dhtmlx.message({text:"این برگه را نمی توان بست<br/>عملیات گروهی در حال اجرا است", type:"error",expire:3000});
			return false;
		}
		else
			return true;
	});
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"Do_BatchProcess");
	popupWindow.centerOnScreen();
	popupWindow.denyResize();
	popupWindow.attachURL("DSBatchProcess_"+BatchItem+".php?"+un()+"&BatchProcess_Id="+BatchProcess_Id+"&UserCount="+UserCount, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function DetachOnCloseTab(){
	parent.BatchProcessInstanceCount--;
	parent.tabbar.detachEvent(OnTabClose_EventId);
}

//-------------------------------------------------------------------DoAfterRefresh()
function DoAfterRefresh(){
	if(SelectedRowId==0)
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);
	dhxLayout.progressOff();
	mygrid.callEvent("onGridReconstructed",[]);
}


</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
