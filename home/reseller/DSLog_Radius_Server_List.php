<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx.css">
    <link rel="STYLESHEET" type="text/css" href="../codebase/dhtmlx_custom.css">
    <script src="../codebase/dhtmlx.js" type="text/javascript"></script>
    <script src="../js/skin.js" type="text/javascript"></script>
    <script src="../js/dsfunFa.js" type="text/javascript"></script>
    <style>
        html, body {
			width: 100%;
			height: 100%;
			margin: 0px;
			overflow: auto;
			padding: 0px;
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
	
	// Layout   ===================================================================
	
	var FilterRowNumber=0;
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	//DSLayoutInitial(dhxLayout);
	dhxLayout.attachEvent('onContentLoaded', progreessoff);
	// TopToolBar   ===================================================================
	ToolbarOfGrid = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(ToolbarOfGrid);
	var opts1 = [
		['100', 'obj', 'نمایش ۱۰۰ خط'],
		['200' , 'obj', 'نمایش ۲۰۰ خط'],
		['300', 'obj', 'نمایش ۳۰۰ خط'],
		['400', 'obj', 'نمایش ۴۰۰ خط'],
		['500', 'obj','نمایش ۵۰۰ خط'],
		['600', 'obj','نمایش ۶۰۰ خط'],
		['700', 'obj','نمایش ۷۰۰ خط'],
		['800', 'obj','نمایش ۸۰۰ خط'],
		['900', 'obj','نمایش ۹۰۰ خط'],
		['1000', 'obj','نمایش ۱۰۰۰ خط']
	]
	var opts2 = [
		['RLog', 'obj', 'تاریخچه ردیوس'],
		['MLog', 'obj', 'mysql تاریخچه'],
		['HAL', 'obj', 'httpd تاریخچه دسترسی'],
		['HEL', 'obj', 'httpd تاریخچه خطا'],
		['MS', 'obj', 'mysql وضعیت'],
		['MP', 'obj', 'mysql لیست پردازش'],
		['LP', 'obj', 'Linux لیست پردازش'],
		['NS', 'obj', 'NetState'],
		['Top', 'obj', 'Top'],
		['dmesg', 'obj', 'dmesg'],
		['lsof', 'obj', 'فایل های باز'],
		['HDS', 'obj', 'HardDisk وضعیت'],
		['MemS', 'obj', 'Memory وضعیت']
	];
	
	ToolbarOfGrid.addButtonSelect('NumberOfRows',null, 'Display 100 line of', opts1, null, null,true,true,12,'select');
	ToolbarOfGrid.setWidth("NumberOfRows",120);
	ToolbarOfGrid.addSeparator(null,null);
	
	ToolbarOfGrid.addButtonSelect('MyOptionButton',null, 'Radius Log', opts2, null, null,true,true,14,'select');
	ToolbarOfGrid.setWidth("MyOptionButton",105);
	ToolbarOfGrid.addSeparator(null,null);
		
	ToolbarOfGrid.setListOptionSelected("MyOptionButton","RLog");
	ToolbarOfGrid.setListOptionSelected("NumberOfRows","100");
	
	ToolbarOfGrid.attachEvent("onClick",ToolbarOfGridOnClick);
	
	DSToolbarAddButton(ToolbarOfGrid,null,"Retrieve","اجرا","Retrieve",ToolbarOfGrid_OnRetrieveClick);
	ToolbarOfGrid_OnRetrieveClick();

}
function ToolbarOfGridOnClick(name,value){
	if((name=="RLog")||(name=="MLog")||(name=="HAL")||(name=="HEL")){
		// ToolbarOfGrid.setListOptionSelected("NumberOfRows","100");
		ToolbarOfGrid.enableItem("NumberOfRows");
	}
	else if ((name=='MS')||(name=='MP')||(name=='LP')||(name=='NS')||(name=='Top')||(name=='dmesg')||(name=='lsof')||(name=='HDS')||(name=='MemS')){
		// ToolbarOfGrid.setListOptionSelected("NumberOfRows","0");
		ToolbarOfGrid.disableItem("NumberOfRows");
	}
}

function ToolbarOfGrid_OnRetrieveClick(){
	NumberOfRows=ToolbarOfGrid.getListOptionSelected("NumberOfRows");
	MyOptionButton=ToolbarOfGrid.getListOptionSelected("MyOptionButton");
	dhxLayout.cells("a").progressOn();
	dhxLayout.cells("a").attachURL("DSLog_Radius_Server_ListRender.php?"+un()+"&act=list&Item="+MyOptionButton+"&NumberOfRows="+NumberOfRows, true);
}	

function progreessoff(){
	dhxLayout.cells("a").progressOff();
}

</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>