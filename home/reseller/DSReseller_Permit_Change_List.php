<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; char=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
    <script src="../codebase/dhtmlxdataprocessor.js" type="text/javascript"></script>
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
var Visp_Id=0;
// var PermitBuffer=parent.PermitBuffer;

window.onload = function(){
	ParentId = "<?php  echo addslashes($_GET['ParentId']);?>";
	if(ParentId == "" ) {return;}
	var Visp_Id ="<?php echo addslashes($_GET['Visp_Id']);?>";
	if(Visp_Id == "" ) {alert('Visp_Id not detected');return;}
	
	var DataTitle="Reseller_Permit2";
	DataName="DSReseller_Permit_Change_";
	RenderFile=DataName+"ListRender";
	var IsChanged=true;
	var PermitEdit=ISPermit("CRM.Reseller.Permit.Edit");
	
	GColIds="Item,State,Comment";
	GColHeaders="مورد,وضعیت,توضیح";

	ISFilter=false;
	FilterState=false;
	GColFilterTypes=[0,0,0]
	
	GFooter="";
	GColInitWidths="250,60,300";
	GColAligns="left,center,left";
	GColTypes="ro,ch,ro";
	GColVisibilitys=[1,1,1];

	ISSort=false;
	GColSorting="server,server,server";
	ColSortIndex=0;
	SortDirection='asc';
	
	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
	DSLayoutInitial(dhxLayout);
	dhxLayout.cells("b").setText("لیست عملیات");
	// dhxLayout.cells("b").setWidth(Math.round(window.innerWidth/3));
	// dhxLayout.cells("b").collapse();
	dhxLayout.cells("b").hideArrow();
	
	// TopToolbar_a   ===================================================================
	var TopToolbar_a = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar_a);
	DSToolbarAddButton(TopToolbar_a,null,"Retrieve","بروزکردن","Retrieve",TopToolbar_a_OnRetrieveClick);
	if(Visp_Id>0){
		TopToolbar_a.addSpacer("Retrieve");
		DSToolbarAddButton(TopToolbar_a,null,"PastePermissions","جایگذاری سطوح دسترسی","tow_PastePermissions",TopToolbar_a_OnPastePermissionsClick);
	}
	
	// TopToolbar_b   ===================================================================
	var TopToolbar_b = dhxLayout.cells("b").attachToolbar();
	DSToolbarInitial(TopToolbar_b);
	DSToolbarAddButton(TopToolbar_b,null,"Apply","اعمال","tow_Apply",TopToolbar_b_OnApplyClick);
	TopToolbar_b.disableItem("Apply");
	DSToolbarAddButton(TopToolbar_b,null,"Restore","بازگرداندن","tow_Restore",TopToolbar_b_OnRestoreClick);
	TopToolbar_b.hideItem("Restore");
	DSToolbarAddButton(TopToolbar_b,null,"Delete","حذف","tow_Delete",TopToolbar_b_OnDeleteClick);
	TopToolbar_b.disableItem("Delete");
	TopToolbar_b.addSpacer("Delete");
	DSToolbarAddButton(TopToolbar_b,null,"ClearAll","حذف همه","tow_ClearAll",TopToolbar_b_OnClearAllClick);
	TopToolbar_b.disableItem("ClearAll");
	
	// MyTree  ==================================================================
	MyTree = dhxLayout.cells("a").attachTree();
	DSTreeInitial(MyTree);
	MyTree.enableCheckBoxes(PermitEdit);
	MyTree.attachEvent("onBeforeCheck", MyTreeOnBeforeCheck);
	MyTree.attachEvent("onRowSelect", function(id,a){alert(id);alert(a);});
	
	// mygrid   ===================================================================
	var mygrid =dhxLayout.cells("b").attachGrid();
	DSGridInitial(mygrid,GColIds,GColHeaders,GColInitWidths,GColAligns,GColTypes,GColVisibilitys,GFooter,ISSort,GColSorting,ColSortIndex,SortDirection);
	mygrid.attachEvent("onCheck", function(rId,cInd,state){mygrid.cells(rId,cInd).setValue(!state);});//to avoid change on check box
	mygrid.attachEvent("onRowSelect", mygridOnRowSelect);
	dhxLayout.progressOn();
	var LoadParentChanges=false;
	MyTree.loadXML(RenderFile+".php?"+un()+"&act=list&ParentId="+ParentId+"&Visp_Id="+Visp_Id,function(){
		// MyTree.openAllItems(0);
		if((Visp_Id>0)&&(parent.mygridCSV!="")){
			mygrid.loadCSVString(parent.mygridCSV);
			LoadParentChanges=true;
			TopToolbar_a.disableItem("Retrieve");
			TopToolbar_b.enableItem("Apply");
			TopToolbar_b.enableItem("Restore");
			TopToolbar_b.enableItem("Delete");
			TopToolbar_b.enableItem("ClearAll");
		}
		dhxLayout.progressOff();
	});
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
//FUNCTION========================================================================================================================
//================================================================================================================================		
function TopToolbar_a_OnRetrieveClick(){
	ResetToolbarButtons();
	mygrid.clearAll();
	ReCreateMyTree();
}

function ResetToolbarButtons(){
	TopToolbar_b.disableItem("Apply");
	TopToolbar_b.hideItem("Restore");
	TopToolbar_b.disableItem("Restore");
	TopToolbar_b.showItem("Delete");
	TopToolbar_b.disableItem("Delete");
	TopToolbar_b.disableItem("ClearAll");
}

function ReCreateMyTree(){
	MyTree.destructor();
	MyTree = dhxLayout.cells("a").attachTree();
	DSTreeInitial(MyTree);
	MyTree.attachEvent("onBeforeCheck", MyTreeOnBeforeCheck);
	dhxLayout.progressOn();
	MyTree.loadXML(RenderFile+".php?"+un()+"&act=list&ParentId="+ParentId+"&Visp_Id="+Visp_Id,function(){
		MyTree.openAllItems(0); 
		dhxLayout.progressOff();
	});
}

function TopToolbar_a_OnPastePermissionsClick(){
	var CopyFrom_Visp_Id=parent.CopyFrom_Visp_Id;
	var CopyFrom_VispName=parent.CopyFrom_VispName;
	if(CopyFrom_Visp_Id==0)
		parent.dhtmlx.alert({text:"لطفا،ابتدا سطوح دسترسی را کپی نمایید",type:"alert-error"});
	else if(CopyFrom_Visp_Id==Visp_Id)
		parent.dhtmlx.alert({text:"جایگذاری سطوح دسترسی به خودش امکان پذیر نیست",type:"alert-error",ok:"بستن"});
	else{
		var ExtraMsg="";
		parent.dhxLayout.progressOn();
		dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=LoadPasteInformation&ParentId="+ParentId,"&From_Visp_Id="+CopyFrom_Visp_Id+"&To_Visp_Id="+Visp_Id,function(loader){
			parent.dhxLayout.progressOff();
			var response=loader.xmlDoc.responseText;
			response=CleanError(response);
			var ResponseArray=response.split("~",7);
			if((response=='')||(response[0]=='~'))
				parent.dhtmlx.alert("خطا، "+response.substring(1));
			else if(ResponseArray[0]!='OK')
				parent.dhtmlx.alert("خطا، "+response);
			else{
				var ConfirmMsg="";
				if((ResponseArray[2]>0)||(ResponseArray[3]>0)){
					ConfirmMsg+="Due to parent limitation there "+((ResponseArray[1]>1)?("are "+ResponseArray[1]+" common permissions"):("is "+ResponseArray[1]+" common permission"))+" between copied VISP ([<span class='VispSp'>"+CopyFrom_VispName+"</span>]) and this VISP<br/><br/>";
					if(ResponseArray[2]>0)
						ConfirmMsg+="Copied VISP has "+ResponseArray[2]+" item(s) extra than this (has no effect).<br/><br/>";
					if(ResponseArray[3]>0){
						ConfirmMsg+="This VISP has "+ResponseArray[3]+" item(s) extra than copied (will set to <span class='PermSp'>not permitted</span>).<br/><br/>";
						ExtraMsg="(include "+(ResponseArray[5]-ResponseArray[3])+" common and "+ResponseArray[3]+" extra)";
					}
				}
				ConfirmMsg+="تعداد "+ResponseArray[4]+" مورد <span class='PermSp'>مجاز</span> خواهد شد<br/>تعداد "+ResponseArray[5]+" مورد <span class='PermSp'>غیر مجاز</span> خواهد شد<br/>";
				if(ResponseArray[6]>0)
					ConfirmMsg+="<br/>Also "+ResponseArray[6]+" item"+(ResponseArray[6]>1?"s":"")+" will be <span class='PermSp'>not permitted</span> from its child<br/>";
				
				parent.dhtmlx.confirm({
					title: "هشدار",
					type:"confirm-warning",
					text: ConfirmMsg+"این عمل قابل برگشت نیست<br/>آیا مطمئن هستید ؟",
					ok:"بلی",
					cancel:"خیر",
					callback: function(result){
						if(result){
							parent.dhxLayout.cells("a").progressOn();
							dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=PastePermissions&ParentId="+ParentId,"&From_Visp_Id="+CopyFrom_Visp_Id+"&To_Visp_Id="+Visp_Id,function (loader){
								parent.dhxLayout.cells("a").progressOff();
								var response=loader.xmlDoc.responseText;
								response=CleanError(response);
								if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
								else if(response=='OK~') {
									dhtmlx.alert({text:"با موفقیت انجام شد",callback:function(){TopToolbar_a_OnRetrieveClick();}});
								}
								else alert(response);
							});
						}
					}
				});
			}
		});
	}	
}

function TopToolbar_b_OnApplyClick(){
	var NumOfRow=mygrid.getRowsNum();
	var CurrentRowIndex=0;
	var TotalItemCount=0;
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		text: "از اعمال "+NumOfRow+" تغییر مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				ResetToolbarButtons();
				dhxLayout.cells("a").progressOn();				
				dhxLayout.cells("b").progressOn();				
				DoApply();
			}
		}
	});
	
	function DoApply(){	
		var RowId=mygrid.getRowId(CurrentRowIndex++);
		if(mygrid.cells(RowId,2).getValue()==""){
			var Id=mygrid.cells(RowId,0).getValue();	
			var state=mygrid.cells(RowId,1).getValue();	
			mygrid.selectRowById(RowId,false,true,true);
			dhtmlxAjax.get(RenderFile+".php?"+un()+"&act=do&ParentId="+ParentId+"&Id="+Id+"&state="+state+"&Visp_Id="+Visp_Id,function(loader){
				response=loader.xmlDoc.responseText;
				response=CleanError(response);		
				var ResponseList=response.split("~",3);
				if((response=='')||(response[0]=='~')){
					dhtmlx.alert({title: "هشدار",type: "alert-error",text: "خطا، "+response.substring(1)});
					mygrid.cells(RowId,2).setValue("Error!");
					mygrid.setRowTextStyle(RowId,"color:red;font-weight:bold");
				}
				else if(ResponseList[0]!='OK'){
					dhtmlx.alert({title: "هشدار",type: "alert-error",text: "خطا، "+response});
					mygrid.cells(RowId,2).setValue("Error!");
					mygrid.setRowTextStyle(RowId,"color:red;font-weight:bold");
				}
				else{
					TotalItemCount=TotalItemCount+parseInt(ResponseList[1]);
					mygrid.cells(RowId,2).setValue("Done, with "+ResponseList[1]+" affected row(s)");
					mygrid.setRowTextStyle(RowId,"color:"+(ResponseList[1]>0?"DarkGreen":"steelblue")+";font-weight:bold");
				}

				if(CurrentRowIndex<NumOfRow)
					setTimeout(DoApply,600);
				else{
					FinalizeApply();
					dhtmlx.alert({text:"انجام شد, تعداد موارد تغییر کرده = "+TotalItemCount,callback:function(){ReCreateMyTree();},ok:"بستن"});
				}
			});
		}
		else{
			mygrid.cells(RowId,2).setValue("Ignored!");
			if(CurrentRowIndex<NumOfRow)
				setTimeout(DoApply,10);
			else{
				FinalizeApply();
				dhtmlx.alert({text:"Done, Total affected row(s) = "+TotalItemCount,callback:function(){ReCreateMyTree();}});
			}			
		}
	}
	
}
function FinalizeApply(){
	dhxLayout.cells("a").progressOff();
	dhxLayout.cells("b").progressOff();
	IsChanged=false;
	TopToolbar_a.enableItem("Retrieve");
	if(Visp_Id>0){
		var T="";
		mygrid.forEachRow(function(RowId){
			if(mygrid.cells(RowId,2).getValue()!="Ignored!")
				T+=mygrid.cells(RowId,0).getValue()+","+mygrid.cells(RowId,1).getValue()+",\n";
		});
		if(T!="")
			parent.mygridCSV=T;
	}
}

function MyTreeOnBeforeCheck(id,state){
	if((LoadParentChanges)&&!confirm("Previous applied changes loaded.\nPress ok to keep them or cancel to clear?"))
		mygrid.clearAll();
	LoadParentChanges=false;
	
	if(IsChanged==false){
		mygrid.clearAll();
	}
	if((state==0)||(state==2))
		NewState=1;
	else{
		NewState=0;
		dhxLayout.cells("b").setText("لیست عملیات<span style='font-size:80%;color:tomato;float:right'> (وقتی تیک موردی را بر می دارید،تیک زیرشاخه های آن نیز برداشته می شود)</span>");
	}
	var gridAddId=(new Date()).valueOf();
	mygrid.addRow(gridAddId,[id,NewState,""]);
	IsChanged=true;
	mygrid.selectRowById(gridAddId,false,true,true);
	TopToolbar_a.disableItem("Retrieve");
	TopToolbar_b.enableItem("Apply");
	TopToolbar_b.enableItem("Restore");
	TopToolbar_b.enableItem("Delete");
	TopToolbar_b.enableItem("ClearAll");
	return true;
}

function TopToolbar_b_OnDeleteClick(){
	var SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		mygrid.cells(SelectedRowId,2).setValue("Deleted!");
		mygrid.cells(SelectedRowId,1).setDisabled(true);
		mygrid.setRowTextStyle(SelectedRowId,"text-decoration:line-through;color:#454545");
	}
	TopToolbar_b.showItem("Restore");
	TopToolbar_b.hideItem("Delete");
}
function TopToolbar_b_OnRestoreClick(){
	var SelectedRowId=mygrid.getSelectedRowId();
	if(SelectedRowId==null)	dhtmlx.message({title: "هشدار",type: "alert-warning",text: "لطفا برای انتخاب، روی ردیف مورد نظر کلیک کنید",ok:"بستن"});
	else{
		mygrid.cells(SelectedRowId,2).setValue("");
		mygrid.cells(SelectedRowId,1).setDisabled(false);
		mygrid.setRowTextStyle(SelectedRowId,"color:black");
	}
	TopToolbar_b.hideItem("Restore");
	TopToolbar_b.showItem("Delete");
}

function TopToolbar_b_OnClearAllClick(){
	dhtmlx.confirm({
		title: "هشدار",
		type:"confirm-warning",
		text: "از حذف همه تغییرات مطمئن هستید؟",
		ok:"بلی",
		cancel:"خیر",
		callback: function(result) {
			if(result){
				TopToolbar_a.enableItem("Retrieve");
				TopToolbar_a_OnRetrieveClick();
				parent.mygridCSV="";
			}
		}
	});
}

function mygridOnRowSelect(rId,cId){
	if(mygrid.cells(rId,2).getValue()=="Deleted!"){
		TopToolbar_b.showItem("Restore");
		TopToolbar_b.hideItem("Delete");
	}
	else{
		TopToolbar_b.hideItem("Restore");
		TopToolbar_b.showItem("Delete");
	}
}
}//window.onload

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
