<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSUserList ....................................................................................");
	if($LastError!=""){
		DSDebug(0,"Session Expire");
		$LoginResellerName=Get_InputIgnore('GET','DB','LoginResellerName','STR',0,32,0,0);
		$Reseller_Id=DBSelectAsString("Select Reseller_Id from Hreseller where ResellerName='$LoginResellerName'");
	}
	else
		$Reseller_Id=$LReseller_Id;
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
var SelectedRowId=0;
// var CurrentposStart=0;
// var CurrentRowCount=100;
window.onload = function(){
	DataTitle="User";
	DataName="DSUser_";
	ExtraFilter="";
	RenderFile=DataName+"ListRender";
	GColIds="CanWWW,CanView,CanEdit,CanDelete,<?php echo DBSelectAsString("Select ColIds from Hgrid_layout where Reseller_Id='$Reseller_Id' and ItemName='CRMUser'");?>";
	GColHeaders=<?php
		$ColHeader=DBSelectAsString("Select ColHeaders from Hgrid_layout where Reseller_Id='$Reseller_Id' and ItemName='CRMUser'");
		$StrFindArray=Array("{#stat_count} rows","Username","UserType","PortStatus","IdentInfo","Family","NationalCode","Mobile","Phone","Organization","PayBalance","EndDate","ExpireDate","BirthDate","Note","ResellerName","StatusDT","StatusName","StatusCreator","Comment","Address","VispName","CenterName","SupporterName","ServiceName","Name");
		$StrReplaceArray=Array("{#stat_count} ردیف","نام کاربری","نوع کاربر","وضعیت پورت","شناسه هویتی","نام خانوادگی","کد ملی","موبایل","تلفن","سازمان/شرکت","تراز مالی","زمان خاتمه سرویس","تاریخ انقضا کاربری","تاریخ تولد","یادداشت","نام نماینده فروش","زمان ثبت آخرین وضعیت","نام وضعیت","ثبت کننده وضعیت","توضیح","آدرس","ارائه دهنده مجازی اینترنت","نام مرکز","نام پشتیبان","نام سرویس","نام");
		echo '"CanWWW,CanView,CanEdit,CanDelete,'.str_replace($StrFindArray,$StrReplaceArray,$ColHeader).'"';
		?>;

	ISFilter=true;
	FilterState=true;
	GColFilterTypes=[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	GFooter="";//"#stat_count";
	GColInitWidths="90,90,90,90,90,90,90,90,90,90,90,90,90,90,90,90,110,110,90,90,110,125,90,110,90,90,135,90,90,200";
	GColAligns="center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center";
	GColTypes="ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro";
	GColVisibilitys=[0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1];

	ISSort=true;
	GColSorting="server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server,server";
	SortDirection='asc';

	ColSortIndex=GColIds.split(",").indexOf("u.Username");

	EditWindow={
				id:"popupWindow",
				x:340,y:20,width:750,height:520,
				center:true,
				modal:true,
				park :false
				};

	// Layout   ===================================================================
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	<?php if(isset($_GET["DefaultUsername"]))
		echo "setTimeout(function(){PopupWindowByUsername('".$_GET["DefaultUsername"]."');},100);";
	?>

	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	// DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","بروزکردن","Retrieve",ToolbarOfGrid_OnRetrieveClick);


	var opts1 = [
		['SaveToXLSX', 'obj', 'XLSX ذخیره به'],
		['SaveToCSV', 'obj', 'CSV ذخیره به']
	];

	ToolbarOfGrid.addButtonSelect('Retrieve',null, 'بروزکردن', opts1, "ds_Retrieve.png", "ds_Retrieve_dis.png",false,false,6,'buttone');
	// ToolbarOfGrid.setWidth("Retrieve",100);
	for(var i=0;i<opts1.length;++i)
		ToolbarOfGrid.setListOptionImage("Retrieve",opts1[i][0],"ds_"+opts1[i][0]+".png");
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGridOnClick);

	if(ISFilter==true){
		ToolbarOfGrid.addSeparator("sep1",null);
		DSToolbarAddButton(ToolbarOfGrid,null,"Filter","فیلتر: فعال","toolbarfilter",ToolbarOfGrid_OnFilterClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterAddRow","افزودن فیلتر جدید","toolbarfilteradd",ToolbarOfGrid_OnFilterAddClick);
		DSToolbarAddButton(ToolbarOfGrid,null,"FilterDeleteRow","حذف فیلتر","toolbarfilterDelete",ToolbarOfGrid_OnFilterDeleteClick);
		ToolbarOfGrid.addSeparator("sep2", null);
		ToolbarOfGrid.disableItem("FilterDeleteRow");
		ToolbarOfGrid.disableItem("Filter");
	}

	DSToolbarAddButton(ToolbarOfGrid,null,"Add","افزودن","tog_Add",ToolbarOfGrid_OnAddClick);
	DSToolbarAddButton(ToolbarOfGrid,null,"Edit","ویرایش","tog_Edit",ToolbarOfGrid_OnEditClick);
	DSToolbarAddButton(ToolbarOfGrid,null,"Delete","حذف","tog_Delete",ToolbarOfGrid_OnDeleteClick);
	ToolbarOfGrid.addSeparator("sep3",null);
	DSToolbarAddButton(ToolbarOfGrid,null,"UsersWebsite","پنل کاربر","tog_UsersWebsite",ToolbarOfGrid_OnUsersWebsiteClick);


	ToolbarOfGrid.addSpacer("UsersWebsite");
	ToolbarOfGrid.addButtonSelect("SaveLayout", null, "ذخیره تغییرات", [], "tog_SaveLayoutButton.png", "tog_SaveLayoutButton_dis.png");
	ToolbarOfGrid.setWidth("SaveLayout",100);
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGrid_OnClick);

	ToolbarOfGrid.addListOption("SaveLayout", "ResetLayout", null, "button", "پیش فرض", "tog_ResetLayoutButton.png");
	ToolbarOfGrid.hideItem("SaveLayout");

	// mygrid   ===================================================================
	mygrid =dhxLayout.cells("a").attachGrid();
	mygrid.enableColumnMove(true);
	mygrid.enableHeaderMenu("false,false,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,false,false,false,false");

	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);

	mygrid.attachEvent("onAfterCMove", function(cInd,posInd){
		var TempArr=GColIds.split(",");
		var r=TempArr.splice(cInd,1);
		TempArr.splice(posInd,0,r);
		GColIds=TempArr.join();

		TempArr=GColHeaders.split(",");
		r=TempArr.splice(cInd,1);
		TempArr.splice(posInd,0,r);
		GColHeaders=TempArr.join();

		TempArr=GColInitWidths.split(",");
		r=TempArr.splice(cInd,1);
		TempArr.splice(posInd,0,r);
		GColInitWidths=TempArr.join();
		ToolbarOfGrid.showItem("SaveLayout");
		ToolbarOfGrid.enableItem("SaveLayout");
	});

	mygrid.attachEvent("onResize", function(cInd,cWidth,obj){
		var GColumnIdArray= GColIds.split(",");
		for (var i=0;i<FilterRowNumber;i++){
			var input =document.getElementById(GColumnIdArray[cInd]+"_f_"+i);
			if(input) input.style.width = cWidth-20;
		}
		var GColInitWidthsArray= GColInitWidths.split(",");
		GColInitWidthsArray[cInd]=cWidth;
		GColInitWidths=GColInitWidthsArray.join();
		ToolbarOfGrid.showItem("SaveLayout");
		ToolbarOfGrid.enableItem("SaveLayout");
		return true;
	});

	mygrid.attachEvent("onRowSelect", SetButton);
    if (ISSort)	mygrid.attachEvent("onBeforeSorting",GridOnSortDo)
	mygrid.attachEvent("onRowDblClicked",GridOnDblClickDo);
	/*mygrid.attachEvent("onDynXLS",function(posStart,RowCount){
		CurrentposStart=posStart;
		CurrentRowCount=RowCount;
		return true;
	});*/


	ToolbarOfGrid_OnFilterAddClick();

	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);


//FUNCTION========================================================================================================================
//================================================================================================================================
//-------------------------------------------------------------------ToolbarOfGridOnClick()
function ToolbarOfGridOnClick(name){
	if(name=="Retrieve"){
		/*CurrentposStart=0;
		CurrentRowCount=100;*/
		LoadGridDataFromServerProgress(dhxLayout,RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	}
	else if((name=="SaveToCSV")||(name=="SaveToXLSX")){

		if(mygrid.getRowsNum()<=0){
			dhtmlx.message({title: "هشدار",type: "alert-warning",text: "داده ای برای ذخیره موجود نیست"});
			return
		}
		if(!ISValidResellerSession()) return;

		var DSFilter='';

		if(ISSort){
			state=mygrid.getSortingState();
			SortStr="&SortField="+mygrid.getColumnId(state[0])+"&SortOrder="+((state[1]=="asc")?"asc":"desc");
		}
		else
			SortStr="&SortField=&SortOrder=";

		if(FilterState==true){
			for(var r=0;r<FilterRowNumber;r++){
				for(var f=0;f<mygrid.getColumnsNum();f++){
					if(GColFilterTypes[f]==1){//text filter
						var input =document.getElementById(mygrid.getColumnId(f)+"_f_"+r);
						if(input.value!="")
							DSFilter=DSFilter+"&dsfilter["+r+"]["+mygrid.getColumnId(f)+"]="+input.value;
					}
				}//for f=0
			}
		}
		var t=mygrid.getStateOfView();
		window.open(RenderFile+".php?act=list"+ExtraFilter+"&posStart="+t[0]+"&count="+t[1]+"&req=SaveToFile&Type="+name.substr(6)+SortStr+DSFilter);
	}
}

function ToolbarOfGrid_OnClick(name){
	if((name=="SaveLayout")||(name=="ResetLayout")){
		ToolbarOfGrid.disableItem("SaveLayout");
		dhtmlx.confirm({
			title: "هشدار",
			type:"confirm",
			text: "آیا مطمئن هستید؟<br/>",
			ok:"بلی",
			cancel:"خیر",
			callback: function(result) {
				if(result){
					dhxLayout.progressOn();
					dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=ChangeLayout&Req="+name,"&GColIds="+GColIds+"&GColHeaders="+GColHeaders+"&GColInitWidths="+GColInitWidths,function (loader){
						dhxLayout.progressOff();
						ToolbarOfGrid.hideItem("SaveLayout");
						response=loader.xmlDoc.responseText;
						response=CleanError(response);
						if((response=='')||(response[0]=='~'))
							dhtmlx.alert({text:"خطا، "+response.substring(1),callback:function(){dhxLayout.progressOn();window.location.reload();},ok:"Reload"});
						else if(response=='OK~')
							dhtmlx.alert({text:(name=="SaveLayout"?"New layout successfully saved.":"Successfully reseted."),callback:function(){dhxLayout.progressOn();window.location.reload();},ok:"Reload"});
						else{
							alert(response);
							dhxLayout.progressOn();
							window.location.reload();
						}
					});
				}
				else
					ToolbarOfGrid.enableItem("SaveLayout");
			}
		});
	}
}
function ToolbarOfGrid_OnUsersWebsiteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId!=null){
		window.open("DSGetUserSession.php?"+un()+"&Id="+SelectedRowId);
	}
	else
		dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب،روی ردیف مورد نظر کلیک کنید!"});
}
function GridOnSortDo(ind,type,direction){
	mygrid.setSortImgState(true,ind,direction);
	ToolbarOfGridOnClick("Retrieve");
};
function GridOnDblClickDo(rId,cInd){
	SelectedRowId=rId;
	var CanEdit=mygrid.cells(SelectedRowId,mygrid.getColIndexById("CanEdit")).getValue();
	var CanView=mygrid.cells(SelectedRowId,mygrid.getColIndexById("CanView")).getValue();
	if((CanEdit==1)||(CanView==1))
		PopupWindow(SelectedRowId);
}
function ToolbarOfGrid_OnRetrieveClick(){
	ToolbarOfGridOnClick("Retrieve");
}
function ToolbarOfGrid_OnFilterClick(){
	FilterState=!FilterState;//if(ToolbarOfGrid.getItemText("Filter")=="Filter: On")
	if(FilterState==true)
		ToolbarOfGrid.setItemText("Filter","فیلتر: فعال");
	else
		ToolbarOfGrid.setItemText("Filter","فیلتر: غیرفعال");
	ToolbarOfGridOnClick("Retrieve");
}
function ToolbarOfGrid_OnFilterAddClick(){
	if(ISFilter){
		DSGridAddFilterRow(mygrid,GColIds,GColFilterTypes,OnFilterTextPressEnter);
		ToolbarOfGrid.enableItem("FilterDeleteRow");
		ToolbarOfGrid.enableItem("Filter");
	}

	FilterRowNumber++;
	ToolbarOfGridOnClick("Retrieve");
}
function OnFilterTextPressEnter(){
	if(FilterState)
		ToolbarOfGridOnClick("Retrieve");
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
	ToolbarOfGridOnClick("Retrieve");
}

function ToolbarOfGrid_OnAddClick(){
		PopupWindow(0);
}
function ToolbarOfGrid_OnEditClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	//alert("->"+SelectedRowId);
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب،روی ردیف مورد نظر کلیک کنید!"})
	else{
		PopupWindow(SelectedRowId);
	}
}

function ToolbarOfGrid_OnDeleteClick(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب،روی ردیف مورد نظر کلیک کنید!"});
	else
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		text: "<span style='color:Red;font-weight:bold'>بازیابی کاربر امکان پذیر نیست</span><br/>برای حذف مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				dhxLayout.cells("a").progressOn();
				dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=Delete&Id="+SelectedRowId+ExtraFilter,function (loader){
					dhxLayout.cells("a").progressOff();
					response=loader.xmlDoc.responseText;
					response=CleanError(response);

					if((response=='')||(response[0]=='~'))dhtmlx.alert({text:"خطا، "+response.substring(1),ok:"بستن"});
					else if(response=='OK~') {
						mygrid.deleteRow(SelectedRowId);
						dhtmlx.message("با موفقیت حذف شد");
					}
					else alert(response);

				});
			}

		}
});
}

function PopupWindow(SelectedRowId){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"User");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId=User_Id,"+SelectedRowId, false);
}
function PopupWindowByUsername(Username){
	popupWindow=DSCreateWindow(dhxLayout,EditWindow,"User");
	popupWindow.attachURL(DataName+"Edit.php?"+un()+"&RowId=Username,"+Username, false);
}

}//END window.onload ---------------------------------------------------------------------------------------------------------------------------------------------

function UpdateGrid(r){
	if(r==0)
		LoadGridDataFromServer(RenderFile,mygrid,"Update",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	else{
		SelectedRowId=r;
		LoadGridDataFromServer(RenderFile,mygrid,"LoadAll",FilterState,GColIds,GColFilterTypes,ISSort,ExtraFilter,DoAfterRefresh);
	}
	/*CurrentposStart=0;
	CurrentRowCount=100;*/
}

function SetButton(){
	SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId!=null){
		var CanWWW=mygrid.cells(SelectedRowId,mygrid.getColIndexById("CanWWW")).getValue();
		var CanEdit=mygrid.cells(SelectedRowId,mygrid.getColIndexById("CanEdit")).getValue();
		var CanDelete=mygrid.cells(SelectedRowId,mygrid.getColIndexById("CanDelete")).getValue();

		if(CanEdit==1){
			ToolbarOfGrid.enableItem("Edit");
			ToolbarOfGrid.setItemText("Edit","ویرایش");
			ToolbarOfGrid.setItemToolTip("Edit","Edit User's information");
		}
		else{
			ToolbarOfGrid.setItemText("Edit","نمایش");
			var CanView=mygrid.cells(SelectedRowId,mygrid.getColIndexById("CanView")).getValue();
			if(CanView==1){
				ToolbarOfGrid.enableItem("Edit");
				ToolbarOfGrid.setItemToolTip("Edit","نمایش اطلاعات کاربر");
			}
			else{
				ToolbarOfGrid.disableItem("Edit");
				ToolbarOfGrid.setItemToolTip("Edit","Not permit to view user's info");
			}
		}

		if(CanDelete==1){
			ToolbarOfGrid.enableItem("Delete");
			ToolbarOfGrid.setItemToolTip("Delete","Delete selected user");
		}
		else{
			ToolbarOfGrid.disableItem("Delete");
			ToolbarOfGrid.setItemToolTip("Delete","Not permit to delete user");
		}

		if(CanWWW==1){
			ToolbarOfGrid.enableItem("UsersWebsite");
			ToolbarOfGrid.setItemToolTip("UsersWebsite","Open User's Website for selected user");
		}
		else{
			ToolbarOfGrid.disableItem("UsersWebsite");
			ToolbarOfGrid.setItemToolTip("UsersWebsite","Not permit to UsersWebsite");
		}
	}
	else{
		ToolbarOfGrid.disableItem("Edit");
		ToolbarOfGrid.disableItem("Delete");
		ToolbarOfGrid.disableItem("UsersWebsite");

		ToolbarOfGrid.setItemToolTip("UsersWebsite","No row selected");
		ToolbarOfGrid.setItemToolTip("Edit","No row selected");
		ToolbarOfGrid.setItemToolTip("Delete","No row selected");

	}
}

function DoAfterRefresh(){

	if((SelectedRowId==null)||(SelectedRowId==0))
		mygrid.selectRow(0);
	else
		mygrid.selectRowById(SelectedRowId,false,true,true);
	SetButton();
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
