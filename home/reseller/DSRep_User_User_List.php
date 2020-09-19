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
window.onload = function(){
	
//VARIABLE ------------------------------------------------------------------------------------------------------------------------------
	var DataTitle="Report User List";
	var DataName="DSRep_User_User_";
	var WhereGetStr="";
	var FieldsGetStr="";
	var FooterGetStr="";
	var RenderFile=DataName+"ListRender";
	var Steps;
	var GroupByCount=0;
	var	OptionCount=22;
	var GroupByGetStr="";
	
	var MyFields=[];
	var MyFooterFields=[];
	var MyFooterFieldsIndex=[];
	var HeaderAlignment = [];		
	var ComparisionItems = [];
	var TNow="<?php require_once('../../lib/DSInitialReseller.php');echo DBSelectAsString('SELECT SHDATESTR(NOW())');?>";
	
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
	
	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:530,
				center:true,
				modal:true,
				park :false
				};
		
//CONTROLS------------------------------------------------------------------------------------------------------------------------------

	if(!ISValidResellerSession()) return;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	var ExportFieldArray=[//ColName,ColId,ColWidth,CollFooter,GroupByPostfix
		["User_Id","Hu.User_Id",100,"Count","rows"],
		["Username","Hu.Username",120,""],
		["Password","Hu.Pass",120,""],
		["LastRequestDT","shdatetimestr(Tuu.LastRequestDT)",130,""],
		["CreatedDate","shdatetimestr(Hu.UserCDT)",130,""],
		["Name","Hu.Name",100,""],
		["Family","Hu.Family",110,""],
		["Fathername","Hu.FatherName",100,""],
		["PayBalance","Hu.PayBalance",150,"Sum","Rial"],		
		["NationalCode","Hu.NationalCode",110,""],
		["BirthDate","shdatestr(Hu.BirthDate)",100,""],
		["ExpirationDate","shdatestr(Hu.ExpirationDate)",100,""],
		["Mobile","Hu.Mobile",100,""],
		["Phone","Hu.Phone",100,""],
		["ADSLPhone","Hu.adslphone",100,""],
		["Address","Hu.Address",170,""],
		["Comment","Hu.Comment",150,""],
		["EMail","Hu.Email",210,""],
		["Company","Hu.Organization",120,""],
		["Nationality","Hu.Nationality",120,""],
		["Reseller","Hr.ResellerName",120,""],
		["Visp","Hv.VispName",120,""],
		["Center","Hc.CenterName",120,""],
		["Supporter","Hsu.SupporterName",120,""],
		["ServiceName","Hse.ServiceName",150,""],
		["StartDate","shdatestr(Hu.Startdate)",110,""],
		["EndDate","shdatestr(Hu.EndDate)",110,""],
		["StatusName","Hst.StatusName",300,""],
		["UserStatus","Hst.UserStatus",90,""],
		["PortStatus","Hst.PortStatus",90,""],
		["InitialMonthOff","Hu.InitialMonthOff",100,""],
		["MaxPrepaidDebit","Hu.MaxPrepaidDebit",100,""],
		["UserType","Hu.UserType",80,""],
		["AuthMethod","Hu.Authmethod",100,""],
		["NOE","Hu.NOE",120,""],
		["IdentInfo","Hu.IdentInfo",120,""],
		["IPRouteLog","Hu.IPRouteLog",120,""],
		["IPPool","Hi.IPPoolName",120,""],
		["LoginTime","Hl.LoginTimeName",120,""],
		["MikrotikRate","Hm.MikrotikRateName",140,""],
		["PeriodicUse","Hu.PeriodicUse",90,""],
		["Calendar","Hu.Calendar",90,""],
		["Simulation","Hu.Simulation",90,""],
		["InterimTime","Hu.InterimTime",90,""],
		["TrafficType","TrafficType",90,""],
		["TimeType","TimeType",90,""],
		["ServiceTraffic","Tuu.STrA",160,"Sum","Bytes"],
		["YearlyTraffic","Tuu.YTrA",160,"Sum","Bytes"],
		["MonthlyTraffic","Tuu.MTrA",160,"Sum","Bytes"],
		["WeeklyTraffic","Tuu.WTrA",160,"Sum","Bytes"],
		["DailyTraffic","Tuu.DTrA",160,"Sum","Bytes"],
		["ExtraTraffic","Tuu.ETrA",160,"Sum","Bytes"],
		["ServiceTraUsed","Tuu.STrU",160,"Sum","Bytes"],
		["YealyTraUsed","Tuu.YTrU",160,"Sum","Bytes"],
		["MonthlyTraUsed","Tuu.MTrU",160,"Sum","Bytes"],
		["WeeklyTraUsed","Tuu.WTrU",160,"Sum","Bytes"],
		["DailyTraUsed","Tuu.DTrU",160,"Sum","Bytes"],
		["ExtraTraUsed","Tuu.ETrU",160,"Sum","Bytes"],
		["BugTraUsed","Tuu.BugUsedTr",160,"Sum","Bytes"],
		["FinishTraUsed","Tuu.FinishUsedTr",160,"Sum","Bytes"],
		["RealReceiveTra","Tuu.RealReceiveTr",160,"Sum","Bytes"],
		["RealSendTraffic","Tuu.RealSendTr",160,"Sum","Bytes"],
		
		["ServiceTraRemain","ServiceTraRemain",160,"Sum","Bytes"],
		["YealyTraRemain","YealyTraRemain",160,"Sum","Bytes"],
		["MonthlyTraRemain","MonthlyTraRemain",160,"Sum","Bytes"],
		["WeeklyTraRemain","WeeklyTraRemain",160,"Sum","Bytes"],
		["DailyTraRemain","DailyTraRemain",160,"Sum","Bytes"],
		["ExtraTraRemain","ExtraTraRemain",160,"Sum","Bytes"],
		
		["ServiceTime","Tuu.STiA",160,"Sum","Sec"],
		["YearlyTime","Tuu.YTiA",160,"Sum","Sec"],
		["MonthlyTime","Tuu.MTiA",160,"Sum","Sec"],
		["WeeklyTime","Tuu.WTiA",160,"Sum","Sec"],
		["DailyTime","Tuu.DTiA",160,"Sum","Sec"],
		["ExtraTime","Tuu.ETiA",160,"Sum","Sec"],
		["ServiceTimeUsed","Tuu.STiU",160,"Sum","Sec"],
		["YealyTimeUsed","Tuu.YTiU",160,"Sum","Sec"],
		["MonthlyTimeUsed","Tuu.MTiU",160,"Sum","Sec"],
		["WeeklyTimeUsed","Tuu.WTiU",160,"Sum","Sec"],
		["DailyTimeUsed","Tuu.DTiU",160,"Sum","Sec"],
		["ExtraTimeUsed","Tuu.ETiU",160,"Sum","Sec"],
		["BugTimeUsed","Tuu.BugUsedTi",160,"Sum","Sec"],
		["FinishTimeUsed","Tuu.FinishUsedTi",160,"Sum","Sec"],		
		["RealUsedTime","Tuu.RealUsedTime",160,"Sum","Sec"],

		// ["ServiceTimeRemain","Tuu.STiA-Tuu.STiU",160,"Sum","Sec"],
		// ["YealyTimeRemain","Tuu.YTiA-Tuu.YTiU",160,"Sum","Sec"],
		// ["MonthlyTimeRemain","Tuu.MTiA-Tuu.MTiU",160,"Sum","Sec"],
		// ["WeeklyTimeRemain","Tuu.WTiA-Tuu.WTiU",160,"Sum","Sec"],
		// ["DailyTimeRemain","Tuu.DTiA-Tuu.DTiU",160,"Sum","Sec"],
		// ["ExtraTimeRemain","Tuu.ETiA-Tuu.ETiU",160,"Sum","Sec"],
		
		["GiftExtraTraffic","Tuu.GiftExtraTr",160,"Sum","Bytes"],
		["GiftExtraTime","Tuu.GiftExtraTi",160,"Sum","Sec"],
		["GiftEndDT","Tuu.GiftEndDT",120,""],
		["GiftTrafficRate","Tuu.GiftTrafficRate",90,""],
		["GiftTimeRate","Tuu.GiftTimeRate",90,""]		
	];
	
	
	var ExportFieldArrayFa=[//ColName,ColId,ColWidth,CollFooter,GroupByPostfix
		["شناسه کاربر","Hu.User_Id",100,"تعداد","ردیف"],
		["نام کاربری","Hu.Username",120,""],
		["کلمه عبور","Hu.Pass",120,""],
		["تاریخ آخرین درخواست","shdatetimestr(Tuu.LastRequestDT)",130,""],
		["تاریخ ایجاد","shdatetimestr(Hu.UserCDT)",130,""],
		["نام","Hu.Name",100,""],
		["نام خانوادگی","Hu.Family",110,""],
		["نام پدر","Hu.FatherName",100,""],
		["تراز مالی","Hu.PayBalance",150,"جمع","ریال"],		
		["کد ملی","Hu.NationalCode",110,""],
		["تاریخ تولد","shdatestr(Hu.BirthDate)",100,""],
		["تاریخ انقضا","shdatestr(Hu.ExpirationDate)",100,""],
		["موبایل","Hu.Mobile",100,""],
		["تلفن","Hu.Phone",100,""],
		["ADSL تلفن","Hu.adslphone",100,""],
		["آدرس","Hu.Address",170,""],
		["توضیح","Hu.Comment",150,""],
		["ایمیل","Hu.Email",210,""],
		["شرکت","Hu.Organization",120,""],
		["ملیت","Hu.Nationality",120,""],
		["نماینده فروش","Hr.ResellerName",120,""],
		["ارائه دهنده مجازی اینترنت","Hv.VispName",135,""],
		["مرکز","Hc.CenterName",120,""],
		["پشتیبان","Hsu.SupporterName",120,""],
		["نام سرویس","Hse.ServiceName",150,""],
		["تاریخ شروع","shdatestr(Hu.Startdate)",110,""],
		["تاریخ پایان","shdatestr(Hu.EndDate)",110,""],
		["نام وضعیت","Hst.StatusName",300,""],
		["وضعیت کاربر","Hst.UserStatus",90,""],
		["وضعیت پورت","Hst.PortStatus",90,""],
		["تخفیف اولیه","Hu.InitialMonthOff",100,""],
		["حداکثر میزان بدهی","Hu.MaxPrepaidDebit",105,""],
		["نوع کاربر","Hu.UserType",80,""],
		["روش اعتبارسنجی","Hu.Authmethod",100,""],
		["موقعیت مکانی وایرلس","Hu.NOE",120,""],
		["شناسه هویتی","Hu.IdentInfo",120,""],
		["گزارش مسیر آی پی","Hu.IPRouteLog",120,""],
		["دامنه آی پی","Hi.IPPoolName",120,""],
		["محدودیت زمان ورود","Hl.LoginTimeName",120,""],
		["سرعت میکروتیک","Hm.MikrotikRateName",140,""],
		["نوع محاسبه دوره","Hu.PeriodicUse",95,""],
		["تقویم","Hu.Calendar",90,""],
		["اتصال همزمان","Hu.Simulation",90,""],
		["زمان بروزرسانی","Hu.InterimTime",90,""],
		["نوع ترافیک","TrafficType",90,""],
		["نوع زمان","TimeType",90,""],
		["ترافیک سرویس","Tuu.STrA",160,"جمع","بایت"],
		["ترافیک سالیانه","Tuu.YTrA",160,"جمع","بایت"],
		["ترافیک ماهیانه","Tuu.MTrA",160,"جمع","بایت"],
		["ترافیک هفتگی","Tuu.WTrA",160,"جمع","بایت"],
		["ترافیک روزانه","Tuu.DTrA",160,"جمع","بایت"],
		["اضافه ترافیک","Tuu.ETrA",160,"جمع","بایت"],
		["ترافیک استفاده شده سرویس","Tuu.STrU",160,"جمع","بایت"],
		["ترافیک استفاده شده سالیانه","Tuu.YTrU",160,"جمع","بایت"],
		["ترافیک استفاده شده ماهیانه","Tuu.MTrU",160,"جمع","بایت"],
		["ترافیک استفاده شده هفتگی","Tuu.WTrU",160,"جمع","بایت"],
		["ترافیک استفاده شده روزانه","Tuu.DTrU",160,"جمع","بایت"],
		["اضافه ترافیک استفاده شده","Tuu.ETrU",160,"جمع","بایت"],
		["ترافیک استفاده شده در حالت اشکال","Tuu.BugUsedTr",185,"جمع","بایت"],
		["ترافیک استفاده شده در حالت اتمام","Tuu.FinishUsedTr",178,"جمع","بایت"],
		["ترافیک دریافتی واقعی","Tuu.RealReceiveTr",160,"جمع","بایت"],
		["ترافیک ارسالی واقعی","Tuu.RealSendTr",160,"جمع","بایت"],
		
		["ترافیک باقیمانده سرویس","ServiceTraRemain",160,"جمع","بایت"],
		["ترافیک باقیمانده سالیانه","YealyTraRemain",160,"جمع","بایت"],
		["ترافیک باقیمانده ماهیانه","MonthlyTraRemain",160,"جمع","بایت"],
		["ترافیک باقیمانده هفتگی","WeeklyTraRemain",160,"جمع","بایت"],
		["ترافیک باقیمانده روزانه","DailyTraRemain",160,"جمع","بایت"],
		["اضافه ترافیک باقیمانده","ExtraTraRemain",160,"جمع","بایت"],
		
		["زمان سرویس","Tuu.STiA",160,"جمع","ثانیه"],
		["زمان سالیانه","Tuu.YTiA",160,"جمع","ثانیه"],
		["زمان ماهیانه","Tuu.MTiA",160,"جمع","ثانیه"],
		["زمان هفتگی","Tuu.WTiA",160,"جمع","ثانیه"],
		["زمان روزانه","Tuu.DTiA",160,"جمع","ثانیه"],
		["اضافه زمان","Tuu.ETiA",160,"جمع","ثانیه"],
		["زمان استفاده شده سرویس","Tuu.STiU",160,"جمع","ثانیه"],
		["زمان استفاده شده سالیانه","Tuu.YTiU",160,"جمع","ثانیه"],
		["زمان استفاده شده ماهیانه","Tuu.MTiU",160,"جمع","ثانیه"],
		["زمان استفاده شده هفتگی","Tuu.WTiU",160,"جمع","ثانیه"],
		["زمان استفاده شده روزانه","Tuu.DTiU",160,"جمع","ثانیه"],
		["اضافه زمان استفاده شده","Tuu.ETiU",160,"جمع","ثانیه"],
		["زمان استفاده شده در حالت اشکال","Tuu.BugUsedTi",176,"جمع","ثانیه"],
		["زمان استفاده شده در حالت اتمام","Tuu.FinishUsedTi",168,"جمع","ثانیه"],		
		["زمان استفاده شده واقعی","Tuu.RealUsedTime",160,"جمع","ثانیه"],

		// ["ServiceTimeRemain","Tuu.STiA-Tuu.STiU",160,"جمع","Sec"],
		// ["YealyTimeRemain","Tuu.YTiA-Tuu.YTiU",160,"جمع","Sec"],
		// ["MonthlyTimeRemain","Tuu.MTiA-Tuu.MTiU",160,"جمع","Sec"],
		// ["WeeklyTimeRemain","Tuu.WTiA-Tuu.WTiU",160,"جمع","Sec"],
		// ["DailyTimeRemain","Tuu.DTiA-Tuu.DTiU",160,"جمع","Sec"],
		// ["ExtraTimeRemain","Tuu.ETiA-Tuu.ETiU",160,"جمع","Sec"],
		
		["اضافه ترافیک هدیه","Tuu.GiftExtraTr",160,"جمع","بایت"],
		["اضافه زمان هدیه","Tuu.GiftExtraTi",160,"جمع","ثانیه"],
		["تاریخ پایان هدیه","Tuu.GiftEndDT",120,""],
		["ضریب ترافیک هدیه","Tuu.GiftTrafficRate",103,""],
		["ضریب زمان هدیه","Tuu.GiftTimeRate",94,""]		
	];	
	
	
	
	
	
	
	var GroupByFieldsArray=[		
		["Visp","Hv.VispName","Hu.Visp_Id"],
		["Reseller","Hr.ResellerName","Hu.Reseller_Id"],
		["Center","Hc.CenterName","Hu.Center_Id"],
		["Supporter","Hsu.SupporterName","Hu.Supporter_Id"],
		["Service","Hse.ServiceName","Hu.Service_Id"],
		["Status","Hst.StatusName","Hu.Status_Id"],
		["PortStatus","Hst.PortStatus","Hst.PortStatus"],
		["IPPool","Hi.IPPoolName","Hu.IPPool_Id"],
		["LoginTime","Hl.LoginTimeName","Hu.LoginTime_Id"],
		["MikrotikRate","Hm.MikrotikRateName","Hu.MikrotikRate_Id"],
		["PeriodicUse","Hu.PeriodicUse","Hu.PeriodicUse"],
		["Calnedar","Hu.Calendar","Hu.Calendar"],
		["TrafficType","TrafficType","TrafficType"],
		["TimeType","TimeType","TimeType"],
		["Nationality","Hu.Nationality","Hu.Nationality"],
		["Simulation","Hu.Simulation","Hu.Simulation"],
		["ServiceSpeed","Hse.Speed","Hse.Speed"],
		["AdvancePortStatus","AdvancePortStatus","AdvancePortStatus"]
	];
	
	var GroupByFieldsArrayFa=[		
		["ارائه دهنده مجازی اینترنت","Hv.VispName","Hu.Visp_Id"],
		["نماینده فروش","Hr.ResellerName","Hu.Reseller_Id"],
		["مرکز","Hc.CenterName","Hu.Center_Id"],
		["پشتیبان","Hsu.SupporterName","Hu.Supporter_Id"],
		["سرویس","Hse.ServiceName","Hu.Service_Id"],
		["وضعیت","Hst.StatusName","Hu.Status_Id"],
		["وضعیت پورت","Hst.PortStatus","Hst.PortStatus"],
		["دامنه آی پی","Hi.IPPoolName","Hu.IPPool_Id"],
		["محدودیت زمان ورود","Hl.LoginTimeName","Hu.LoginTime_Id"],
		["سرعت میکروتیک","Hm.MikrotikRateName","Hu.MikrotikRate_Id"],
		["نوع محاسبه دوره","Hu.PeriodicUse","Hu.PeriodicUse"],
		["نوع تقویم","Hu.Calendar","Hu.Calendar"],
		["نوع ترافیک","TrafficType","TrafficType"],
		["نوع زمان","TimeType","TimeType"],
		["ملیت","Hu.Nationality","Hu.Nationality"],
		["اتصال همزمان","Hu.Simulation","Hu.Simulation"],
		["سرعت سرویس","Hse.Speed","Hse.Speed"],
		["وضعیت پورت پیشرفته","AdvancePortStatus","AdvancePortStatus"]
	];	
	var TheFieldsItems=[
			["UserName","User_Id","Password","PayBalance","Name","Family","FatherName","NationalCode","CompanyName","Nationality","Phone","AdslPhone","Mobile","Comment","NOE","IdentInfo","IPRouteLog","InitialMonthOff","MaxPrepaidDebit","InterimTime"],
			["StartDate","EndDate","GiftEndDate","LastRequestDate","CreatedDate","BirthDate","ExpirationDate"],
			["TrafficType","TimeType","URLReporting","Calendar","PeriodicUse"],
			["Service Traffic","Yearly Traffic","Monthly Traffic","Weekly Traffic","Daily Traffic","Extra Traffic","ServiceTraUsed","YearlyTraUsed","MonthlyTraUsed","WeeklyTraUsed","DailyTraUsed","ExtraTraUsed","FinishTraUsed","BugTraUsed","RealTraSend","RealTraReceive","ServiceTraRemain","YearlyTraRemain","MonthlyTraRemain","WeeklyTraRemain","DailyTraRemain","ExtraTraRemain"],
			["ServiceTime","YearlyTime","MonthlyTime","WeeklyTime","DailyTime","ExtraTime","ServiceTimeUsed","YearlyTimeUsed","MonthlyTimeUsed","WeeklyTimeUsed","DailyTimeUsed","ExtraTimeUsed","FinishTimeUsed","BugTimeUsed","RealTimeUsed","ServiceTimeRemain","YearlyTimeRemain","MonthlyTimeRemain","WeeklyTimeRemain","DailyTimeRemain","ExtraTimeRemain"],
			["ActiveServiceName","Visp","Reseller","Center","Supporter","MikrotikRate","IPPool","LoginTime","OffFormula","ActiveDirectory","Status","PortStatus","UserType","AuthMethod"]
		];
	var TheFieldsItemsFa=[
			["نام کاربری","شناسه کاربر","کلمه عبور","تراز مالی","نام","نام خانوادگی","نام پدر","کد ملی","نام شرکت","ملیت","تلفن","ADSL تلفن","موبایل","توضیح","موقعیت مکانی وایرلس","شناسه هویتی","IPRoute گزارش","تخفیف اولیه","حداکثر بدهی مجاز","زمان بروزرسانی از سرور"],
			["تاریخ شروع","تاریخ پایان","تاریخ پایان هدیه","تاریخ آخرین درخواست","تاریخ ایجاد","تاریخ تولد","تاریخ انقضا"],
			["نوع ترافیک","نوع زمان","گزارش URL","تقویم","استفاده دوره ای"],
			["ترافیک سرویس","ترافیک سالیانه","ترافیک ماهیانه","ترافیک هفتگی","ترافیک روزانه","اضافه ترافیک","ترافیک استفاده شده سرویس","ترافیک استفاده شده سالیانه","ترافیک استفاده شده ماهیانه","ترافیک استفاده شده هفتگی","ترافیک استفاده شده روزانه","اضافه ترافیک استفاده شده","ترافیک استفاده شده در حالت اتمام","ترافیک استفاده شده در حالت اشکال","ترافیک واقعی ارسال","ترافیک واقعی دریافت","ترافیک باقی مانده سرویس","ترافیک باقی مانده سالیانه","ترافیک باقی مانده ماهیانه","ترافیک باقی مانده هفتگی","ترافیک باقی مانده روزانه","اضافه ترافیک باقی مانده"],
			["زمان سرویس","زمان سالیانه","زمان ماهیانه","زمان هفتگی","زمان روزانه","زمان اضافی","زمان استفاده شده سرویس","زمان استفاده شده سالیانه","زمان استفاده شده ماهیانه","زمان استفاده شده هفتگی","زمان استفاده شده روزانه","اضافه زمان استفاده شده","زمان استفاده شده در حالت اتمام","زمان استفاده شده در حالت اشکال","زمان استفاده شده واقعی","زمان باقی مانده سرویس","زمان باقی مانده سالیانه","زمان باقی مانده ماهیانه","زمان باقی مانده هفتگی","زمان باقی مانده روزانه","اضافه زمان باقی مانده"],
			["نام سرویس فعال","ارائه دهنده مجازی اینترنت","نماینده فروش","مرکز","پشتیبان","سرعت میکروتیک","دامنه آی پی","محدودیت زمان ورود","فرمول تخفیف","اکتیو دایرکتوری","وضعیت","وضعیت پورت","نوع کاربر","نوع اعتبارسنجی"]
		];
	var TheFieldsItemsValue=[
			["Username","User_Id","Pass","PayBalance","Name","Family","Fathername","NationalCode","Organization","Nationality","Phone","AdslPhone","Mobile","Comment","NOE","IdentInfo","IPRouteLog","InitialMonthOff","MaxPrepaidDebit","InterimTime"],
			["StartDate","EndDate","GiftEndDT","LastRequestDT","UserCDT","BirthDate","ExpirationDate"],
			["TrafficType","TimeType","URLReporting","Calendar","PeriodicUse"],
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
	//=======Popup2 SelectFields
	var Popup2;
	var PopupId2=['SelectFields'];//popup Attach to Which Buttom of Toolbar
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
	BlockTMP2 = [];
	for (i=1;i<=3;++i){
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
	Form1Str.push({type: "fieldset", name:"F1", width:840, label: "گام 1-1", list:BlockTMP2});

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
				{type: "input" , name: "Value2_1_1", value: TNow, maxLength:10,inputWidth:120,validate:"IsValidDate"} ,
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
					{type: "input" , name: "Value2_1_2", value: TNow, maxLength:10,inputWidth:120,validate:"IsValidDate"}
				]}
			]}
		]}
	];
        
	Form1Str.push({type: "fieldset", name:"F2", width:840, label: "گام 1-2", list:BlockTMP2});
	
	BlockTMP2 = [
		{type: "block", list:[
			{type: "checkbox",name:"Chk3_1_1",position:"absolute",checked:false, list:[
				{type: "select", name: "Field3_1_1", inputWidth:130, options:[
					{text: "نوع ترافیک", value: "TrafficType"},
					{text: "نوع زمان", value: "TimeType"},
					{text: "گزارش صفحات بازدید شده", value: "URLReporting"},
					{text: "محاسبه دوره", value: "PeriodicUse"},
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
					{text: "از ابتدا هفته/ماه", value: "Fix"},
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
						{text: "گزارش URL", value: "URLReporting"},
						{text: "استفاده دوره ای", value: "PeriodicUse"},
						{text: "تقویم", value: "Calendar"}                       
					]},
					{type: "newcolumn",offset:4},
					{type: "label", label:" = ",labelAlign:"center", labelWidth:51},
					{type: "newcolumn"},				
					{type: "select", name: "TrafficType_2", inputWidth:118, options:[
						{text: "نامحدود", value: "Unlimit"},
						{text: "محدود", value: "Limit"},
						{text: "بدون سرویس فعال", value: "NoActiveService"}
					]},
					{type: "select", name: "TimeType_2", hidden:true, inputWidth:118, options:[
						{text: "نامحدود", value: "Unlimit"},
						{text: "محدود", value: "Limit"},
						{text: "بدون سرویس فعال", value: "NoActiveService"}
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
	
	// Form1Str.push({type: "fieldset", name:"F3" , width:840, label: "Step 1-3", list:BlockTMP2});	
	
	// BlockTMP2 = [];
	
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
				{type: "input" , name: "Value4_1_1", value: 0, maxLength: 9, inputWidth: 90, validate: "NotEmpty,ValidInteger"} ,
				{type: "newcolumn"},
				{type: "label", label:"مگابایت", labelWidth:39},
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
				{type: "input" , name: "Value4_2_1", value: 0, maxLength:9, inputWidth: 90, validate: "NotEmpty,ValidInteger"} ,
				{type: "newcolumn"},
				{type: "label", label:"ثانیه", labelWidth:39},
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
	Form1Str.push({type: "fieldset", name:"F4", width:840, label: "گام 1-4", list:BlockTMP2});	
	

	
	BlockTMP2 = [];
	for (i=1;i<=2;++i){
		BlockTMP1 = [];
		for(j=0;j<TheFieldsItems[5].length-3;j++){
			BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[ 
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 80, name:TheFieldsItemsValue[5][j]+"_"+i,
							connector: RenderFile+".php?"+un()+"&act=Select"+TheFieldsItems[5][j], inputWidth: 630,
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
						}
				]}
			);
		}

		BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[ 
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 80, name:TheFieldsItemsValue[5][j]+"_"+i, inputWidth: 630,
							options:[
								{text: "در حال انتظار", value: "Waiting"},
								{text: "رزرو", value: "Reserve"},
								{text: "ارسال برای رانژه", value: "GoToBusy"},
								{text: "مشغول", value: "Busy"},
								{text: "در حال جمع آوری", value: "GoToFree"},
								{text: "آزاد", value: "Free"},
								{text: "هیچکدام", value: "None"}	
							],
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
						}
				]}
			);
		j++;
		BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[ 
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 80, name:TheFieldsItemsValue[5][j]+"_"+i, inputWidth: 630,
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
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
						}
				]}
			);
		j++;
		BlockTMP1.push(
				{text: TheFieldsItemsFa[5][j], value: TheFieldsItemsValue[5][j],list:[ 
						{type: "select", name: "Comp5_"+TheFieldsItemsValue[5][j]+"_"+i, options: ComparisionItems[3] , inputWidth:80, required:true},
						{type: "newcolumn"},
						{type: "multiselect", inputHeight: 80, name:TheFieldsItemsValue[5][j]+"_"+i, inputWidth: 630,
							options:[
								{text: "Username-Password", value: "UP"},
								{text: "Username-CallerId", value: "UC"},
								{text: "Username", value: "U"},
								{text: "Username-Password-CallerId", value: "UPC"},
								{text: "ActiveDirectory", value: "A"}
							],
							note: {text: "با استفاده از کلیدهای Ctrl و Shift می توانید بیش از یک مورد را انتخاب کنید"}
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
		{type: "fieldset", name:"F5", hidden:true, width:840, label: "گام 2", list:BlockTMP2},
		{type: "block", width: 840, list:[
				{type: "newcolumn", offset:400},   
				{type: "button",name: "Cancel",value: " لغو ",width :80},
				{type: "newcolumn", offset:20},                        
				{type: "button",name: "Back",value: " بازگشت ", disabled:true, width :80},
				{type: "newcolumn", offset:0},
				{type: "button",name: "Next",value: " بعدی ",width :80},
				{type: "newcolumn", offset:20},
				{type: "button", name: "GoToReport", disabled:true, hidden:true, value:"در حال بارگذاری("+OptionCount+")",width :80}
		]}
	);

	
		


	//=======Form2 Fields
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = {	UserName:'UserName'};
	var Form2FieldHelpId=['UserName'];
    var Form2Str = [];


		

	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);

	AddPopupFilter();
	AddPopupSelectFields();
	
	DSToolbarAddButton(ToolbarOfGrid,null,"SaveToFile","ذخیره در فایل","SaveToFile",ToolbarOfGrid_OnSaveToFileClick);
	ToolbarOfGrid.setItemToolTip("SaveToFile","CSVذخیره نتایج گزارش در فایل");
	ToolbarOfGrid.disableItem('SaveToFile');
	Popup1.show("Filter");

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);	
	
	
//FUNCTION========================================================================================================================

//-------------------------------------------------------------------AddPopupFilter()
function AddPopupFilter(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"Filter","تنظیم فیلتر","tow_Filter");
	ToolbarOfGrid.setItemToolTip("Filter","فیلترها را برای ایجاد گزارش تنظیم کنید");
	Popup1=DSInitialPopup(ToolbarOfGrid,PopupId1,Popup1OnShow);
	Form1=DSInitialForm(Popup1,Form1Str,Form1PopupHelp,Form1FieldHelpId,Form1FieldHelp,Form1OnButtonClick);
	Form1.attachEvent("onChange",Form1OnChange);
	ToolbarOfGrid.disableItem('Filter');
	Form1.attachEvent("onOptionsLoaded", function(name){
		OptionCount--;
		if(OptionCount<=0){
			setTimeout(function(){
				Form1.setItemLabel("GoToReport","انتخاب فیلد");
				Form1.enableItem("GoToReport");
				ToolbarOfGrid.enableItem('Filter');
			},500);
		}
		else
			Form1.setItemLabel("GoToReport","در حال بارگذاری("+OptionCount+")");
	});
	Steps=1;
	Form1.setItemValue("Field5_2",TheFieldsItemsValue[5][1]);//to set second selectbox to second option on page two
}

//-------------------------------------------------------------------Popup1OnShow()
function Popup1OnShow(){
	if(!ISValidResellerSession()) return;
	ToolbarOfGrid.disableItem('SelectFields');
	ToolbarOfGrid.disableItem('SaveToFile');
}

//-------------------------------------------------------------------Form1OnChange(id, value)
function Form1OnChange(id,value){
	//alert("id:'"+id+"'     value:'"+value+"'");
	if((id=='Field3_1_1')||(id=='Field3_1_2')||(id=='Chk3_1_1')||(id=='Chk3_1_2')
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
			var ShowItems=['F5','GoToReport'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['F1','F2'/*,'F3'*/,'F4'];
			FormHideItem(Form1,HideItems);
			
			Form1.enableItem("Back");
			Form1.disableItem("Next");
			Steps=2;
		}
	}	
	else if(name=='Back') {
		if(Steps==2){
			var ShowItems=['F1','F2'/*,'F3'*/,'F4'];
			FormShowItem(Form1,ShowItems);
			var HideItems=['F5'];
			FormHideItem(Form1,HideItems);			
			Form1.enableItem("Next");
			Form1.disableItem("Back");
			Steps=1;
		}
	}
	else{//GoToReport Clicked
		//if(DSFormValidate(Form1,Form1FieldHelpId))
		{
			Form1.disableItem("GoToReport");
			Popup1.hide();			
			dhxLayout.progressOn();
			
			WhereGetStr=CreateWhereGetStr();			
			
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=list&req=GetUserCount&SortField=&SortOrder="+WhereGetStr,
			function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				else if(response=='0'){
					if((typeof mygrid != "undefined"))
						mygrid.clearAll();
					dhtmlx.alert({text:"هیچ کاربری با این فیلتر وجود نداشت",ok:"بستن"});
				}
				else{
					CreateForm2Str(response);
					Form1.enableItem("GoToReport");
					GroupByCount=0;
					Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
					Popup2.show("SelectFields");
					ToolbarOfGrid.enableItem("SelectFields");
					Form2.setItemValue("GroupBy1",0);
					Form2.setItemValue("GroupBy2",1);
					Form2.setItemValue("GroupBy3",2);
				}
				
				Form1.enableItem("GoToReport");
				dhxLayout.progressOff();
				
			});
		}
	}
}

//-------------------------------------------------------------------AddPopupSelectFields()
function AddPopupSelectFields(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"SelectFields","انتخاب فیلد","tow_SelectFields");
	ToolbarOfGrid.setItemToolTip("SelectFields","فیلدهایی که قصد دارید در گزارش نمایش داده شود را انتخاب کنید");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	ToolbarOfGrid.disableItem('SelectFields');
}

//-------------------------------------------------------------------Popup2OnShow()
function Popup2OnShow(){
	if(!ISValidResellerSession()) return;
}

//-------------------------------------------------------------------CreateForm2Str(UserCount)
function CreateForm2Str(UserCount){
	BlockTMP1=[];
	for(i=0;i<GroupByFieldsArray.length;++i){
		BlockTMP1.push({text: GroupByFieldsArrayFa[i][0], value: i});
	}
	BlockTMP2=[
		{type: "block", width:800, list:[
			{type: "checkbox",name:"ChkGroupBy1", checked:true, hidden:true},
			{type: "select", name: "GroupBy1", position: "label-left",label:"دسته بندی براساس :",labelAlign:"left",labelWidth:60, inputWidth:135, options:BlockTMP1},
			{type: "newcolumn", offset:35},
			{type: "checkbox",name:"ChkGroupBy2",position:"absolute",checked:false, list:[
				{type: "select", name: "GroupBy2", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:135, options:BlockTMP1},
				{type: "newcolumn", offset:35},
				{type: "checkbox",name:"ChkGroupBy3",position:"absolute",checked:false, list:[
					{type: "select", name: "GroupBy3", position: "label-left",label:"و دسته بندی براساس :",labelAlign:"left",labelWidth:80, inputWidth:135, options:BlockTMP1}
				]}
			]}
		]},
		{type: "label", label:"<div style='width:900px;;height:9px;border-bottom: 1px solid #336699;'></div>",labelAlign:"center"},
	];
	BlockTMP1=[{type: "checkbox", label: "تعداد کاربران", name: "Grp_Hu.User_Id", disabled: true, checked: true}];
	j=1;
	for(i=1;i<ExportFieldArray.length;++i){
		if(ExportFieldArray[i][3]!=""){
			BlockTMP1.push({type: "checkbox", label: ExportFieldArrayFa[i][3]+" "+ExportFieldArrayFa[i][0], name: "Grp_"+ExportFieldArray[i][1], checked: false});
			if(j++%11==10)
				BlockTMP1.push({type: "newcolumn",offset:40});
		}
	}
	BlockTMP2.push({type: "block", list:BlockTMP1});
	BlockTMP1=[{type: "checkbox", label: "شناسه کاربر", name: "Hu.User_Id", disabled: true, checked: true}];
	for(i=1;i<ExportFieldArray.length;++i){
		BlockTMP1.push({type: "checkbox", label: ExportFieldArrayFa[i][0], name: ExportFieldArray[i][1], checked:false});
		if(i%15==14)
			BlockTMP1.push({type: "newcolumn",offset:40});
	}
	Form2Str=[
		{type:"settings", position: "label-right"},
		{type: "fieldset", name:"NormalFields", width:1125, label: "تعداد کاربر یافت شده:"+UserCount+" فیلدهایی که می خواهید انتخاب کنید", list: BlockTMP1},
		{type: "fieldset", name:"GroupByFields", width:860, hidden:true, label: ""+UserCount+" : فیلدهایی که می خواهید انتخاب کنید. کاربر یافت شده ", list: BlockTMP2},
		{type: "block", width: 860, list:[
			{type: "button", name: "SelectAll", value: " انتخاب همه ",width :70},
			{type: "newcolumn", offset:10},
			{type: "button", name: "SelectNone", value: " انتخاب هیچکدام ",width :90},
			{type: "newcolumn", offset:10},
			{type: "button", name: "Reverse", value: " انتخاب معکوس ",width :90},
			{type: "newcolumn", offset:50},
			{type: "button", name: "ToggleGroupBy", value: " تنظیم دسته بندی ", width:100},
			{type: "newcolumn", offset:110},			
			{type: "checkbox", label: "نمایش مجموع ردیف ها", name: "TotalRow", checked:false},
			{type: "newcolumn", offset:30},
			{type: "button", name: "Cancel", value: " لغو ",width :50},
			{type: "newcolumn", offset:10},
			{type: "button", name: "Finish", value: " گزارش ",width :50}
		]}
	];
}

//-------------------------------------------------------------------Form2OnButtonClick(name)
function Form2OnButtonClick(name){
	CheckBoxPrefix=(GroupByCount?("Grp_"):"");
		
	if(name=='Cancel')
		Popup2.hide();
	else if(name=='SelectAll'){
		for(i=1;i<ExportFieldArray.length;++i){
			if((!GroupByCount)||(ExportFieldArray[i][3]!=""))
				Form2.checkItem(CheckBoxPrefix+ExportFieldArray[i][1]);
		}
	}
	else if(name=='SelectNone'){
		for(i=1;i<ExportFieldArray.length;++i){
			if((!GroupByCount)||(ExportFieldArray[i][3]!=""))
				Form2.uncheckItem(CheckBoxPrefix+ExportFieldArray[i][1]);
		}
	}
	else if(name=='Reverse'){
		for(i=1;i<ExportFieldArray.length;++i){
			if((!GroupByCount)||(ExportFieldArray[i][3]!=""))
				if(Form2.isItemChecked(CheckBoxPrefix+ExportFieldArray[i][1]))
					Form2.uncheckItem(CheckBoxPrefix+ExportFieldArray[i][1])
				else
					Form2.checkItem(CheckBoxPrefix+ExportFieldArray[i][1]);
		}
	}
	else if(name=='ToggleGroupBy'){
		if(GroupByCount){
			FormShowItem(Form2,['NormalFields']);
			FormHideItem(Form2,['GroupByFields']);	
		}
		else{
			FormHideItem(Form2,['NormalFields']);
			FormShowItem(Form2,['GroupByFields']);
		}
		GroupByCount=1-GroupByCount;
	}
	else{//finish button
			
		Form2.disableItem("Finish");
		if(GroupByCount){
			SetGroupByGridLayout();
		}
		else{ 
			var SelectedFields=SetNormalGridLayout();
			if(SelectedFields==0){
				alert("موردی توسط شما انتخاب نشده.\nبرای ادامه،موارد انتخابی خود را تیک بگذارید");
				Form2.enableItem("Finish");
				return;
			}
		}
		Popup2.hide();
		InititalGridList();
		FieldsGetStr="&MyFields="+MyFields.join()+"&MyFieldsName="+GColIds;
		FooterGetStr="&FooterFields="+MyFooterFields.join();
		Popup2.hide();
		MyLoadGridDataFromServer();
		Form2.enableItem("Finish");		
		ToolbarOfGrid.enableItem('SaveToFile');
	}
}

//-------------------------------------------------------------------MyLoadGridDataFromServer()
function MyLoadGridDataFromServer(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",ISFilter,GColIds,"",ISSort,"&req=ShowInGrid"+FieldsGetStr+WhereGetStr+GroupByGetStr,DoAfterRefresh);

	if(GFooter!=''){
		dhtmlxAjax.get(RenderFile+".php?act=list&req=GetFooterInfo&SortField=&SortOrder="+FooterGetStr+WhereGetStr,
			function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);
				if((response=='')||(response[0]=='~'))	dhtmlx.alert("خطا، "+response.substring(1));
				else{
					ColumnIdArray=GColIds.split(",");
					ResponseList=response.split("`");
					var FieldIndex;
					for(i=0;i<MyFooterFields.length;++i){
						FieldIndex=MyFooterFieldsIndex[i];
						document.getElementById("FooterField_"+FieldIndex).innerHTML = ResponseList[i]+" "+ExportFieldArrayFa[FieldIndex][4];
					}
				}
			}
		);
	}
}

//-------------------------------------------------------------------CreateWhereGetStr()
function CreateWhereGetStr(){
	var Out='';
	var f11='',f12='',f21='',f22='',tmp='';
	for(i=1;i<=3;++i){
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

//-------------------------------------------------------------------SetNormalGridLayout()
function SetNormalGridLayout(){
	GroupByGetStr="&ChkGroupBy1=0";
	ISSort=true;
	ColSortIndex=0;
	SortDirection='asc';
	
	MyFields=[ExportFieldArray[0][1]];
	var HaveFooter=Form2.getItemValue("TotalRow");
	GColIds="User_Id";
	if(HaveFooter){
		MyFooterFieldsIndex=[0];
		MyFooterFields=['COUNT(1)'];
		GFooter="<div id='FooterField_0' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>";
		GColHeaders="شناسه کاربر";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GFooter="";
	}
	GColInitWidths=ExportFieldArrayFa[0][2];
	GColAligns="center";
	GColTypes="ro";
	GColSorting="server";
	GColVisibilitys=[1];
	HeaderAlignment=["text-align:center"];
	for(i=1;i<ExportFieldArray.length;++i){
		if(Form2.getItemValue(ExportFieldArray[i][1])){
			MyFields.push(ExportFieldArray[i][1]);
			//MyFieldsName.push(ExportFieldArray[i][0]);
			GColIds+=","+ExportFieldArray[i][0];
			GColHeaders+=","+ExportFieldArrayFa[i][0];
			if(HaveFooter){
				if(ExportFieldArray[i][3]!=""){
					MyFooterFieldsIndex.push(i);
					MyFooterFields.push("SUM("+ExportFieldArrayFa[i][1]+")");
					GFooter+=",<div id='FooterField_"+i+"' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>";				
				}
				else{			
					GFooter+=",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>"+ExportFieldArrayFa[i][0]+"</div>";
				}
			}
			GColInitWidths+=","+ExportFieldArrayFa[i][2];
			GColAligns+=",center";
			GColSorting+=",server";
			GColTypes+=",ro";
			GColVisibilitys.push(1);
			HeaderAlignment.push("text-align:center");
		}
	}

	return MyFields.length-1;
}

//-------------------------------------------------------------------SetGroupByGridLayout()
function SetGroupByGridLayout(){
	ISSort=false;
	var tmp;
	MyFields=["Row_Number"];
	var HaveFooter=Form2.getItemValue("TotalRow");
	GColIds="Row_Number";
	if(HaveFooter){
		MyFooterFieldsIndex=[];
		MyFooterFields=[];
		GColHeaders="شماره ردیف";		
		GFooter="<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>{#stat_count} ردیف</div>";
	}
	else{
		GColHeaders="{#stat_count} ردیف";
		GFooter="";
	}
	

	
	GColInitWidths=90;
	GColAligns="center";
	GColTypes="ra";
	HeaderAlignment=["text-align:center"];
	GColVisibilitys=[1];
	
	GroupByGetStr="";

	for(i=1;i<=3;++i)
		if(Form2.getItemValue("ChkGroupBy"+i)){
			
			tmp=Form2.getItemValue("GroupBy"+i);
			MyFields.push(GroupByFieldsArray[tmp][1]);
			GColIds+=","+GroupByFieldsArray[tmp][0];
			GColHeaders+=","+GroupByFieldsArrayFa[tmp][0];
			if(HaveFooter){			
				GFooter+=",<div style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>"+GroupByFieldsArrayFa[tmp][0]+"</div>";
			}			
			GColInitWidths+=","+135;
			GColAligns+=",center";
			GColTypes+=",ro";
			HeaderAlignment.push("text-align:center");
			GColVisibilitys.push(1);

			GroupByGetStr+="&ChkGroupBy"+i+"=1&GroupBy"+i+"="+GroupByFieldsArray[tmp][2];
		}
		else{
			GroupByGetStr+="&ChkGroupBy"+i+"=0";
			break;
		}
		
	for(i=0;i<ExportFieldArray.length;++i){
		if((ExportFieldArray[i][3]!="")&&(Form2.getItemValue("Grp_"+ExportFieldArray[i][1]))){
			MyFields.push(ExportFieldArray[i][3]+"("+ExportFieldArray[i][1]+")");
			GColIds+=","+ExportFieldArray[i][3]+"of"+ExportFieldArray[i][0];
			GColHeaders+=","+ExportFieldArrayFa[i][3]+" "+ExportFieldArrayFa[i][0];
			if(HaveFooter){
				MyFooterFieldsIndex.push(i);
				MyFooterFields.push(((i==0)?"COUNT(":"SUM(")+ExportFieldArray[i][1]+")");
				GFooter+=",<div id='FooterField_"+i+"' style='padding:2px 3px 2px 3px;text-align:center;color:darkblue;font-weight: bold;line-height:200%'>-</div>";				
			}
			GColInitWidths+=","+209;
			GColAligns+=",center";
			GColTypes+=",ro";
			HeaderAlignment.push("text-align:center");
			GColVisibilitys.push(1);
		}
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
	for (var i=0;i<GColVisibilitys.length;i++){
		if(GColVisibilitys[i]==0)
			mygrid.setColumnHidden(i,true);
	}	
	if (ISSort) mygrid.setColSorting(GColSorting);

	if (GFooter != '') mygrid.attachFooter(GFooter);

	mygrid.init();
 
	if (ISSort){
		mygrid.setSortImgState(true,ColSortIndex,SortDirection);
		mygrid.attachEvent("onBeforeSorting",function(ind,type,direction){
			mygrid.setSortImgState(true,ind,direction);
			ColSortIndex=ind;
			SortDirection=((direction=='asc')?'asc':'desc');
			MyLoadGridDataFromServer();
		});
	}
	
	if(!GroupByCount){
		if(HeaderAlignment.length>5) mygrid.splitAt(2);
		
		mygrid.enableSmartRendering(true,100);
		mygrid.attachEvent("onRowDblClicked",function(id){PopupWindow(id)});
	}
	
	

}
	
//-------------------------------------------------------------------ToolbarOfGrid_OnSaveToFileClick()
function ToolbarOfGrid_OnSaveToFileClick(){
	if(!ISValidResellerSession()) return;
	ToolbarOfGrid.disableItem('SaveToFile');
	window.location=RenderFile+".php?act=list&req=SaveToFile&SortField="+MyFields[ColSortIndex]+"&SortOrder="+SortDirection+FieldsGetStr+WhereGetStr+GroupByGetStr;
	//dhxLayout.cells("a").attachURL(RenderFile+".php?act=list&req=SaveToFile&SortField="+MyFields[ColSortIndex]+"&SortOrder="+SortDirection+FieldsGetStr+WhereGetStr+GroupByGetStr);
	setTimeout(function(){ToolbarOfGrid.enableItem('SaveToFile')},2000);
}
	
//-------------------------------------------------------------------PopupWindow(SelectedRowId)
function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	var User_Id=mygrid.cells(SelectedRowId,mygrid.getColIndexById("User_Id")).getValue();
	popupWindow.attachURL("DSUser_Edit.php?"+un()+"&RowId=User_Id,"+User_Id, false);
}



}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

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
