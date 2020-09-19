<?php
require_once("../../lib/DSInitialReseller.php");
$User_Id=addslashes($_GET['ParentId']);
$HaveIPService=DBSelectAsString("select count(1) from Huser_serviceip where (User_Id='$User_Id')And((ServiceStatus='Active')or(ServiceStatus='Pending'))");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
	<link rel="STYLESHEET" type="text/css" href="../codebase/rtl.css">
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
var SelectedRowId=0;
window.onload = function(){
	ParentId="<?php  echo $_GET['ParentId'];  ?>";
	if(ParentId == "" ) {return;}
	var MaxPrepaidDebit=<?php echo DBSelectAsString("Select MaxPrepaidDebit from Huser where User_Id='".addslashes($_GET['ParentId'])."'");?>;
	DataTitle="ServiceBase";
	DataName="DSUser_ServiceBase_";
	ExtraFilter="&User_Id="+ParentId;
	RenderFile=DataName+"ListRender";


	GColIds="User_ServiceBase_Id,ResellerName,ServiceStatus,ServiceName,StartDate,EndDate,ExtraDay,PayPlan,ServicePrice,InstallmentNo,InstallmentPeriod,InstallmentFirstCash,SavingOffUsed,DirectOff,VAT,PayPrice,CancelDT,ReturnPrice,CDT,Off,SavingOff";
	GColHeaders="{#stat_count} ردیف,ثبت کننده,وضعیت سرویس,نام سرویس,تاریخ شروع,تاریخ خاتمه,روز اضافی,نوع پرداخت,قیمت,تعداد اقساط,مدت اقساط,قسط اول زمان ثبت پرداخت شود,پس انداز استفاده شده,تخفیف مستقیم(%),مالیات(%),هزینه کسر شده,تاریخ لغو سرویس,مبلغ برگشت داده شده,تاریخ اضافه شدن,تخفیف(%),پس انداز(%)";

	ISFilter=true;
	FilterState=false;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	GFooter="";
	GColInitWidths="60,80,100,300,70,120,70,75,75,90,100,180,130,110,80,100,120,130,120,80,100";
	GColAligns="center,center,left,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=false;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	ColSortIndex=0;
	SortDirection='Desc';

	var ISPermitPostPaid=ISPermit('Visp.User.PayPlan.PostPaid');
	ISPermitAdd=ISPermit('Visp.User.Service.Base.Add');
	ISPermitEdit=false;//ISPermit('Visp.User.Service.Base.Edit.StartDate');
	ISPermitCancel=ISPermit('Visp.User.Service.Base.Cancel');
	ISPermitCheckService=ISPermit('Visp.User.Service.Base.CheckService');
	ISPermitStartNextService=ISPermit('Visp.User.Service.Base.StartNextService');

	EditWindow={
				id:"popupWindow",
				x:100,y:20,width:300,height:200,
				center:true,
				modal:true,
				park :false
				};

	//=======Popup2 AddService
	var Popup2;
	var PopupId2=['AddService'];//popup Attach to Which Buttom of Toolbar

	//=======Form2 AddService
	var Form2;
	var Form2PopupHelp;
	var Form2FieldHelp  = { Service_Id:'سرویس کاربر',
							StartDate:'زمان شروع سرویس',
							VoucherNo:'Voucher No(Char max length 15)',
							VoucherDate:'Date of Pay, Leave blank or enter format yyyy/mm/dd '};
	var Form2FieldHelpId=['Service_Id','StartDate','VoucherNo','VoucherDate'];
	var PayPlanItem={ type: "input" , name:"PayPlan", label:"PayPlan:", disabled:true,labelAlign:"left",inputWidth:80,value:"PrePaid"};
	var Form2Str = [
		{ type:"settings" , labelWidth:90, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"Service_Id",label: "نام :",connector: RenderFile+".php?"+un()+"&act=SelectServiceBase&User_Id="+ParentId,required:true,validate:"IsID",inputWidth:418,info:true},
		{ type: "input" , name:"Description", label:"<span style='color:#273737'>توضیحات :</span>", validate:"",disabled: true, style:"color:#273737",rows: 2,labelAlign:"left",inputWidth:420,inputHeight:38},
		{type: "input" , name:"RemainedSavingOff",hidden:true, label:"<span style='color:blue'>Total Available SavingOff(Rls) :</span>", validate:"", disabled: true,style:"color:#050505",labelAlign:"left",inputWidth:100, labelWidth:170, numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{type:"hidden",name:"PriceValue"},
		{type:"hidden",name:"OffRateValue"},
		{type:"hidden",name:"OffPercnt"},
		{type:"hidden",name:"SavingOffPercent"},
		{type:"hidden",name:"DirectOffPercent"},
		{type:"hidden",name:"SavingOffExpirationDays"},
		{type:"hidden",name:"VATPercent"},
		{type: "block", name:"InvoiceBlock1",hidden:true,width: 530,list:[
			{ type: "input" , name:"ServicePrice", label:"<span style='color:#273737'>قیمت سرویس :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
			{ type: "input" , name:"InstallmentNo", label:"<span style='color:#273737'>تعداد اقساط :</span>", validate:"" ,disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120},
			{type: "input" , name:"WithdrawSavingOff", label:"Withdraw SavingOff :", validate:"IsValidPrice", disabled: true,labelAlign:"left", maxLength:14,value:0,inputWidth:75, labelWidth:135,numberFormat: "<?php  echo $PriceFormat;?>"},
			{ type: "input" , name:"Price", label:"<span style='color:#273737'>قیمت هر قسط :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
			{type: "newcolumn", offset:30},
			// { type: "input" , name:"SavingOff",label:"<span style='color:#273737'>Saving Off :</span>",validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120},
			{ type: "input" , name:"DirectOff",label:"<span style='color:#273737'>Direct Off :</span>",validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120},
			{ type: "input" , name:"PriceWithOff", label:"<span style='color:#273737'>Price(-DirectOff) :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
			{ type: "input" , name:"VAT", label:"<span style='color:#273737'>مالیات :</span>", validate:"", disabled: true, style:"color:#273737",labelAlign:"left", maxLength:2,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
			{ type: "input" , name:"PriceWithVAT", label:"<span style='color:maroon'>قیمت با مالیات :</span>", validate:"", disabled: true, style:"color:maroon;background-color:#FFCCCC;font-weight:bold",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"},
		]},
		{type: "block", name:"InvoiceBlock2",hidden:true, width: 500, style:"border:1px dotted #C0C0E0;margin:4px 0 4px 0;padding:2px 0 4px 0", list:[
			{ type: "input" , name:"UserCredit", label:"<span style='color:#666666'>اعتبار کاربر :</span>", validate:"", disabled: true,style:"color:#666666",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>",labelWidth:79},
			{type: "newcolumn", offset:40},
			{type: "input" , name:"RemainCredit", label:"<span style='color:#666666'>باقیمانده اعتبار :</span>", validate:"", disabled: true,style:"color:#666666",labelAlign:"left", maxLength:13,inputWidth:120,numberFormat: "<?php  echo $PriceFormat;  ?>"}
		]}
	];
	if(ISPermitPostPaid==true)
		Form2Str.push(
		{ type: "select", name:"PayPlan", label: "نوع پرداخت :", value: "PrePaid",inputWidth:90,validate:"",required:true, disabled: true,options:[
			{text: "پیش پرداخت", value: "PrePaid"},
			{text: "پس پرداخت", value: "PostPaid"}
			]
		});
	else
		Form2Str.push(
		{ type: "select", name:"PayPlan", label: "PayPlan :", value: "PrePaid",inputWidth:80,validate:"",required:true, disabled: true,options:[
			{text: "پیش پرداخت", value: "PrePaid"}
			]
		});

	Form2Str.push(
		{type:"label",name:"Detail",label:""},
		{type: "block", name:"ControlBlock", width: 510, list:[
			{ type: "button",name:"Proceed",value: "انجام",disabled:true,width :90},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Refresh",value: "نوسازی",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80},
			{type: "newcolumn", offset:40},
			{ type: "button",name:"BuyingBasket",value: "سبد خرید",width :90}
		]}
		<?php
			if($HaveIPService>0)
				print(',{type:"label", label:"<span style=\'color:red;font-size:90%;font-style:oblique\'>توجه: کاربر دارای سرویس آی پی می باشد</span>"}');
		?>
		);
	var PopupBuyingBasket;
/*
	//=======Popup3 EditService
	var Popup3;
	var PopupId3=['EditService'];//  popup Attach to Which Buttom of Toolbar
	//=======Form3 EditService
	var Form3;
	var Form3PopupHelp;
	var Form3FieldHelp  = {StartDate:'When Service Start',EndDate:'When Service Stop'};
	var Form3FieldHelpId=['StartDate','EndDate'];
	var Form3Str = [
		{ type:"settings" , labelWidth:120, inputWidth:80,offsetLeft:10  },
		{ type: "select", name:"ServiceStartDate", label: "ServiceStartDate :", value: "Today",inputWidth:120,validate:"",required:true,options:[
			{text: "Today", value: "Today",list :[

				]
			},
			{text: "Custom", value: "Custom",list :[
				{ type:"input" , name:"StartDate", label:"StartDate :",maxLength:10, validate:"IsValidDateOrBlank", labelAlign:"left",info:true,inputWidth:100}
				]
			}

			]
		},
		{type: "block", width: 250, list:[
			{ type: "button",name:"Proceed",value: "Proceed",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " Close ",width :80}
		]}
		];
*/
	//=======Popup4 CancelService
	var Popup4;
	var PopupId4=['CancelService'];//  popup Attach to Which Buttom of Toolbar
	//=======Form4 CancelService
	var Form4;
	var Form4PopupHelp;
	var Form4FieldHelp  = {Price:'User paid',ReturnPrice:'How much will retrun to Huser',ExtraCredit:'Whenever Remian ExtraCredit transfer to next Hservice or not'};
	var Form4FieldHelpId=['ReturnPrice','Price','ExtraCredit'];
	var Form4Str = [
		{ type:"settings" , labelWidth:150, inputWidth:80,offsetLeft:10  },
		{ type: "input" , name:"PayPrice", label:"پرداخت شده :",labelAlign:"left", disabled:true, maxLength:13,inputWidth:80,info:false,numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{ type: "input" , name:"ReturnPrice", label:"مبلغ برگشتی :",labelAlign:"left", maxLength:14,inputWidth:80,info:false,numberFormat: "<?php  echo $PriceFormat;  ?>"},
		{type: "block", width: 260, list:[
			{ type: "button",name:"Proceed",value: "انجام",width :80},
			{type: "newcolumn", offset:20},
			{ type: "button",name:"Close",value: " بستن ",width :80}
		]}
		<?php
			if($HaveIPService>0)
				print(',{type:"label", label:"<span style=\'color:red;font-size:90%;font-style:oblique\'>توجه : کاربر دارای سرویس آی پی می باشد</span>"}');
		?>
		];

	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	// ToolbarOfGrid   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);

	if(ISFilter==true){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"Filter","فیلتر: غیرفعال","toolbarfilter",ToolbarOfGrid_OnFilterClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterAddRow","افزودن فیلتر جدید","toolbarfilteradd",ToolbarOfGrid_OnFilterAddClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterDeleteRow","حذف فیلتر","toolbarfilterDelete",ToolbarOfGrid_OnFilterDeleteClick);
		ToolbarOfGrid.addSeparator("sep2", null);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.disableItem("Filter");
	}

	if(ISPermitAdd)	AddPopupAddService();
	//if(ISPermitEdit) AddPopupEditService();
	if(ISPermitCancel)	AddPopupCancelService();
	if(ISPermitCheckService)	DSToolbarAddButton(ToolbarOfGrid,null,"CheckService","بررسی سرویس","CheckService",ToolbarOfGrid_OnCheckServiceServiceClick);
	if(ISPermitStartNextService)	DSToolbarAddButton(ToolbarOfGrid,null,"StartNextService","فعال کردن رزرو","StartNextService",ToolbarOfGrid_OnStartNextServiceClick);

		// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);

	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);
			if(input) input.style.width = cWidth-20;
		}
		return true;
	});
	mygrid.attachEvent("onRowSelect", SetButton);

    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)

	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);


	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
function ToolbarOfGrid_OnCheckServiceServiceClick(){
	dhtmlx.confirm({
		title: "هشدار",
		type: "confirm-warning",
		text: "آیا مطمئن هستید که می خواهید سرویس را برای فعال کردن بررسی کنید؟",
		ok: "بلی",
		cancel: "خیر",
		callback: function(result) {

			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=CheckService&User_Id="+ParentId,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert({text:"خطا، "+response.substring(1),ok:"بستن"});
					else if(response=='OK~') {
						dhtmlx.message("سرویس با موفقیت بررسی شد");
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
					}
					else alert(response);

				});
			}
		}
	});

}

function ToolbarOfGrid_OnStartNextServiceClick(){
	dhtmlx.confirm({
		title: "هشدار",
		type: "confirm-warning",
		text: "فعال کردن سرویس باعث لغو سرویس کنونی و شروع سرور انتخابی می شود. آیا مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=StartNextService&User_Id="+ParentId,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert({text:"خطا، "+response.substring(1),ok:"بستن"});
					else if(response=='OK~') {
						dhtmlx.message("سرویس با موفقیت شروع شد");
						LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
					}
					else alert(response);

				});
			}

		}
	});

}

function SetParamInfo2(loader){
	dhxLayout.cells("a").progressOff();
	Form2.unlock();
	response=loader.xmlDoc.responseText;
	response=CleanError(response);
	if((response=='')||(response[0]=='~')){
		dhtmlx.alert("خطا، "+response.substring(1));
		Form2.lock();
	}
	else{
		//$RemainedSavingOff`$ServicePrice`$InstallmentNo`$Price`$OffRate`$Off`$SavingOff`$DirectOff`$SavingOffExpirationDays`$VAT`$UserCredit`$Err`$Description``
		var parray=response.split("`",14);
		Form2.setItemValue("RemainedSavingOff",parray[0]);
		if(parray[0]<=0){
			Form2.hideItem("WithdrawSavingOff");
			Form2.hideItem("RemainedSavingOff");
		}
		else{
			Form2.showItem("WithdrawSavingOff");
			Form2.showItem("RemainedSavingOff");
		}

		Form2.setItemValue("ServicePrice",parray[1]);
		Form2.setItemValue("InstallmentNo",parray[2]);
		Form2.setItemValue("PriceValue",parray[3]);//Price
		Form2.setItemValue("OffRateValue",parray[4]);//OffRate
		Form2.setItemValue("OffPercnt",parray[5]);//Off
		Form2.setItemValue("SavingOffPercent",parray[6]);//SavingOff
		Form2.setItemValue("DirectOffPercent",parray[7]);//DirectOff
		Form2.setItemValue("SavingOffExpirationDays",parray[8]);//SavingOffExpirationDays
		Form2.setItemValue("VATPercent",parray[9]);//VAT
		Form2.setItemValue("UserCredit",parray[10]);//UserCredit
		var Err=parray[11];
		Form2.setItemValue("Description",parray[12]);
		Form2.getInput("Description").style.direction=GetTextDirection(parray[12]);


		parent.MyService["Base"][0]=false;
		parent.MyService["Base"][1]=0;
		parent.MyService["Base"][2]=0;
		parent.MyService["Balance"]=parray[10];
		parent.MyService["RemainedSavingOff"]=parray[0];
		SetInvoice(Form2.getItemValue("WithdrawSavingOff"));
		if(Err!=""){
			Form2.disableItem("ControlBlock");
			alert(Err);
		}
		else
			Form2.enableItem("ControlBlock");
	}
}
function SetInvoice(WithdrawSavingOff){
	WithdrawSavingOff = WithdrawSavingOff ==''? 0 : parseInt(WithdrawSavingOff.replace(/,/g,''));

	var Price=Form2.getItemValue("PriceValue");
	var RemainedSavingOff=Form2.getItemValue("RemainedSavingOff");
	var SumOfWithdrawSavingOff=parent.GetSumOfWithdrawSavingOff("Base")
	var TotalRemainedSavingOff=RemainedSavingOff - SumOfWithdrawSavingOff;
	var MinWithdrawable=Math.min(Price,TotalRemainedSavingOff);

	if(WithdrawSavingOff>MinWithdrawable){
		if(MinWithdrawable==TotalRemainedSavingOff)
			/* dhtmlx. */alert((SumOfWithdrawSavingOff>0?(formatMoney(SumOfWithdrawSavingOff,0,".",",")+" ریال از پس انداز استفاده شده برای اعتبار اضافی، آی پی و سرویس سایر\nبنابراین می توانید برداشت کنید "):"مجموع پس انداز باقی مانده = ")+formatMoney(TotalRemainedSavingOff,0,".",",")+"\nنمی توانید برداشت کنید "+formatMoney(WithdrawSavingOff,0,".",","));
		else
			/* dhtmlx. */alert("شما حداکثر می توانید به اندازه قیمت مورد نیاز سرویس برداشت کنید("+formatMoney(Price,0,".",",")+")");

		Form2.setItemValue("WithdrawSavingOff",MinWithdrawable);
		WithdrawSavingOff=MinWithdrawable;
	}


	Price=Price-WithdrawSavingOff;
	Form2.setItemValue("Price",formatMoney(Price,0,".",","));

	var PayPlanType=Form2.getItemValue("PayPlan");
	var OffRateValue=Form2.getItemValue("OffRateValue");
	var OffPercnt=Form2.getItemValue("OffPercnt");
	var SavingOffPercent=Form2.getItemValue("SavingOffPercent");
	var DirectOffPercent=Form2.getItemValue("DirectOffPercent");
	var PriceWithOff=Price;
	if(OffRateValue==0){
		// Form2.setItemValue("SavingOff","Service has no Off");
		Form2.setItemValue("DirectOff","Service has no Off");
		Form2.showItem("DirectOff");
		Form2.hideItem("PriceWithOff");
		Form2.setItemLabel("Detail","");
	}
	else{
		var DirectOffAmount=Math.round(Price*DirectOffPercent/100);
		var SavingOffAmount=Math.round(Price*SavingOffPercent/100);

		if(DirectOffPercent>0){
			PriceWithOff=Math.round(Price-DirectOffAmount);
			Form2.setItemValue("DirectOff",formatMoney(DirectOffAmount,0,".",",")+" ("+formatMoney(DirectOffPercent,0,".",",")+"%)");
			Form2.setItemValue("PriceWithOff",PriceWithOff);
			Form2.showItem("DirectOff");
			Form2.showItem("PriceWithOff");
		}
		else{
			Form2.hideItem("DirectOff");
			Form2.hideItem("PriceWithOff");
		}

		if(SavingOffPercent>0)
			Form2.setItemLabel("جزئیات","<span style='color:darkgreen;'>"+formatMoney(SavingOffAmount,0,".",",")+" ریال ("+formatMoney(SavingOffPercent,0,".",",")+"%) پس از افزودن سرویس به پس انداز اضافه می شود</span>");
		else
			Form2.setItemLabel("جزئیات","");
	}

	SavingOffExpirationDays=Form2.getItemValue("SavingOffExpirationDays");
	VATPercent=Form2.getItemValue("VATPercent");
	UserCredit=Form2.getItemValue("UserCredit");

	var VATAmount=0;
	if(VATPercent==0)
		Form2.setItemValue("VAT","0");
	else{
		VATAmount=Math.round(PriceWithOff*VATPercent/100);
		Form2.setItemValue("VAT",formatMoney(VATAmount,0,".",",")+" ("+formatMoney(VATPercent,0,".",",")+"%)");
	}
	var PriceWithVAT=Math.round(PriceWithOff+VATAmount);
	Form2.setItemValue("PriceWithVAT",PriceWithVAT);

	parent.MyService["Base"][0]=true;
	parent.MyService["Base"][1]=PriceWithVAT;
	parent.MyService["Base"][2]=WithdrawSavingOff;

	var RemainCredit=parent.GetRemainCredit();
	if(RemainCredit<0) RemainCredit=0;
	Form2.setItemValue("RemainCredit",RemainCredit);

	if(PayPlanType=='PrePaid'){
		if(RemainCredit>MaxPrepaidDebit)
			Form2.disableItem("Proceed");
		else
			Form2.enableItem("Proceed");
	}
	else{
		if(ISPermitPostPaid==true)
			Form2.enableItem("Proceed");
		else
			Form2.disableItem("Proceed");
	}
	if(PopupBuyingBasket.isVisible())
		setTimeout(function(){PopupBuyingBasket.hide();PopupBuyingBasket.show("BuyingBasket")},200);
}
function Form2onInputChange(id, value){
	//parent.parent.dhtmlx.message("<span style='color:red'>Input</span>\nid="+id+"\nvalue="+value);
	if(id=="WithdrawSavingOff")
		SetInvoice(value);
}
function Form2onChange(id, value){
	// dhtmlx.message("<span style='color:blue'>Input</span>\nid="+id+"\nvalue="+value);
	if(id=='Service_Id'){
		if(value==0){
			if(PopupBuyingBasket.isVisible())
				PopupBuyingBasket.hide();
			parent.MyService["Base"][0]=false;
			parent.MyService["Base"][1]=0;
			parent.MyService["Base"][2]=0;
			Form2.hideItem("RemainedSavingOff");
			Form2.hideItem("InvoiceBlock1");
			Form2.hideItem("InvoiceBlock2");
			Form2.setItemValue("Off",'');
			Form2.setItemValue("PriceWithOff",'');
			Form2.setItemValue("InstallmentNo",'');
			Form2.setItemValue("RemainedSavingOff",'');
			Form2.setItemValue("ServicePrice",'');
			Form2.setItemValue("Price",'');
			Form2.setItemValue("VAT",'');
			Form2.setItemValue("PriceWithVAT",'');
			Form2.setItemValue("UserCredit",'');
			Form2.setItemValue("RemainCredit",'');
			Form2.setItemValue("Description",'');
			Form2.disableItem("Proceed");
			Form2.disableItem("PayPlan");
			Form2.disableItem("WithdrawSavingOff");
			Form2.setItemValue("DirectOff",'');
			Form2.setItemLabel("Detail",'');
		}
		else{
			Form2.showItem("InvoiceBlock1");
			Form2.showItem("InvoiceBlock2");
			Form2.enableItem("PayPlan");
			Form2.enableItem("WithdrawSavingOff");
			dhxLayout.cells("a").progressOn();
			Form2.lock();
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetServicePrice&User_Id="+ParentId+"&Service_Id="+value,SetParamInfo2);
		}
	}
	else if(id=='PayPlan'){
		if(value=='PrePaid'){
			if(parent.GetRemainCredit()>MaxPrepaidDebit)
				Form2.disableItem("Proceed");
			else
				Form2.enableItem("Proceed");
		}
		else{
			if(ISPermitPostPaid==true)
				Form2.enableItem("Proceed");
			else
				Form2.disableItem("Proceed");
		}
	}
	else if(id=="WithdrawSavingOff")
		SetInvoice(value);
}

function AddPopupAddService(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"AddService","افزودن","tow_AddService");
	Popup2=DSInitialPopup(ToolbarOfGrid,PopupId2,Popup2OnShow);
	Popup2.attachEvent("onHide",function(){
		parent.MyService["Base"][0]=false;
		parent.MyService["Base"][1]=0;
		parent.MyService["Base"][2]=0;
		if(PopupBuyingBasket.isVisible())
			PopupBuyingBasket.hide();
	});
	// Popup2.attachEvent("onContentClick",function(){if(PopupBuyingBasket.isVisible())PopupBuyingBasket.hide()});
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
	Form2.attachEvent("onInputChange",Form2onInputChange);
}

function AddPopupEditService(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"EditService","ویرایش","tow_EditService");
	 ToolbarOfGrid.disableItem('EditService');
	Popup3=DSInitialPopup(ToolbarOfGrid,PopupId3,Popup3OnShow);
	Form3=DSInitialForm(Popup3,Form3Str,Form3PopupHelp,Form3FieldHelpId,Form3FieldHelp,Form3OnButtonClick);
}

function AddPopupCancelService(){
	DSToolbarAddButtonPopup(ToolbarOfGrid,null,"CancelService","لغو کردن","tow_"+DataTitle+"CancelService");
	ToolbarOfGrid.disableItem('CancelService');
	Popup4=DSInitialPopup(ToolbarOfGrid,PopupId4,Popup4OnShow);
	Form4=DSInitialForm(Popup4,Form4Str,Form4PopupHelp,Form4FieldHelpId,Form4FieldHelp,Form4OnButtonClick);
}

function Form2OnButtonClick(name){//AddService
	if(name=='Close')
		Popup2.hide();
	else if(name=='Refresh'){
		Form2.updateValues();
		var value=Form2.getItemValue("Service_Id");
		if(value>0){
			dhxLayout.cells("a").progressOn();
			Form2.lock();
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=GetServicePrice&User_Id="+ParentId+"&Service_Id="+value,SetParamInfo2);
		}
	}
	else if(name=='Proceed'){
		if(DSFormValidate(Form2,Form2FieldHelpId)){
			Form2.disableItem("Proceed");
			Popup2.hide();
			// dhxLayout.cells("a").progressOn();
			DSFormUpdateRequestProgress(dhxLayout,Form2,RenderFile+".php?"+un()+"&act=AddService&User_Id="+ParentId,Form2DoAfterUpdateOk,Form2DoAfterUpdateFail);
		}
	}
	else if(name=='BuyingBasket')
		if(PopupBuyingBasket.isVisible())
			PopupBuyingBasket.hide("BuyingBasket");
		else
			PopupBuyingBasket.show("BuyingBasket");
}

function Form3OnButtonClick(name){//EditService
	if(name=='Close') Popup3.hide();
	else{
		SelectedRowId=mygrid.getSelectedRowId();
		if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
		else
		if(DSFormValidate(Form3,Form3FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form3,RenderFile+".php?"+un()+"&act=EditService&User_Id="+ParentId+"&User_ServiceBase_Id="+SelectedRowId,Form3DoAfterUpdateOk,Form3DoAfterUpdateFail);
		}
	}
}

function Form4OnButtonClick(name){//CancelService
	if(name=='Close') Popup4.hide();
	else{
		SelectedRowId=mygrid.getSelectedRowId();
		if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
		else
		if(DSFormValidate(Form4,Form4FieldHelpId)){
			DSFormUpdateRequestProgress(dhxLayout,Form4,RenderFile+".php?"+un()+"&act=CancelService&User_Id="+ParentId+"&User_ServiceBase_Id="+SelectedRowId,Form4DoAfterUpdateOk,Form4DoAfterUpdateFail);
		}
	}
}

function Form2DoAfterUpdateOk(){
	Popup2.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	Form2.clear();
}
function Form3DoAfterUpdateOk(){
	Popup3.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form4DoAfterUpdateOk(){
	Popup4.hide();
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function Form2DoAfterUpdateFail(){
	Popup2.hide();
}
function Form3DoAfterUpdateFail(){
	Popup3.hide();
}
function Form4DoAfterUpdateFail(){
	Popup4.hide();
}


function Popup2OnShow(){//AddService
	Form2.unload();
	Form2=DSInitialForm(Popup2,Form2Str,Form2PopupHelp,Form2FieldHelpId,Form2FieldHelp,Form2OnButtonClick);
	Form2.attachEvent("onChange",Form2onChange);
	Form2.attachEvent("onInputChange",Form2onInputChange);
	PopupBuyingBasket= new dhtmlXPopup({form: Form2,id:["BuyingBasket"],mode:"bottom"});

	PopupBuyingBasket.attachEvent("onShow",function(){
		if(parent.IsBuyingBasketEmpty(false)){
			PopupBuyingBasket.hide();
			parent.dhtmlx.message({text:"سبد خرید خالی است",expire:4000,type:"error"});
		}
		else
			PopupBuyingBasket.attachHTML(parent.GetBuyingBasket());
	});
	PopupBuyingBasket.attachEvent("onContentClick",function(){PopupBuyingBasket.hide();});
}

function Popup3OnShow(){//EditService
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else
	Form3.load(RenderFile+".php?"+un()+"&act=LoadEditServiceForm&User_Id="+ParentId+"&User_ServiceBase_Id="+SelectedRowId,function(id,respond){
		Form3.setItemFocus("StartDate");
	});

}

function Popup4OnShow(){//CancelService
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	var ServiceStatus=mygrid.cells(SelectedRowId,mygrid.getColIndexById("ServiceStatus")).getValue();
	if((ServiceStatus=='CancelTransfer')||(ServiceStatus=='CancelNotTransfer')||(ServiceStatus=='Used'))	dhtmlx.message({title: "هشدار",type: "alert-error",text: "!سرویس یا لغو شده یا در حال استفاده است"});

	Form4.load(RenderFile+".php?"+un()+"&act=LoadCancelServiceForm&User_Id="+ParentId+"&User_ServiceBase_Id="+SelectedRowId,function(id,respond){
		Form4.setItemFocus("ReturnPrice");
		Form4.setItemValue("ReturnPrice",0);
	});
}

function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);;
}
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}

	FilterRowNumber++;
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function OnFilterTextPressEnter(){
	if(FilterState)
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}
function ToolbarOfGrid_OnFilterDeleteClick(){
	DSGridDeleteFilterRow(GColIds,GColFilterTypes);
	FilterRowNumber--;
	if(FilterRowNumber==0){
		mygrid.detachHeader(1);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
		FilterState=false;
		ToolbarOfGrid.disableItem("Filter");
	}
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function ToolbarOfGrid_OnAddClick(){
		PopupWindow(0);
}
function ToolbarOfGrid_OnEditClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"})
	else{
		PopupWindow(SelectedRowId);
	}
}
function ToolbarOfGrid_OnDeleteClick(){
	LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
}

function PopupWindow(SelectedRowId){
	popupWindow=dhxLayout.dhxWins.createWindow(EditWindow);
	popupWindow.setText("Loading ...");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId="+SelectedRowId, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServer(RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServer(RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	}
}

function SetButton(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId!=null){
		var ServiceStatus=mygrid.cells(SelectedRowId,mygrid.getColIndexById("ServiceStatus")).getValue();
		if((ServiceStatus=='Pending')||(ServiceStatus=='Active')){
			if(ISPermitCancel) ToolbarOfGrid.enableItem('CancelService');
		}
		else ToolbarOfGrid.disableItem('CancelService');
	}
	else
		if(ISPermitCancel) ToolbarOfGrid.disableItem('CancelService');
}

function DoAfterRefresh(){
	if((SelectedRowId==null)||(SelectedRowId==0))
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);

	SetButton();
	dhxLayout.cells("a").progressOff();

}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
