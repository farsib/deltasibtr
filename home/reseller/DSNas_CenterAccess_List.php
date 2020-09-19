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
	ParentId = "<?php  echo $_GET['ParentId'];  ?>";
	if(ParentId == "" ) {return;}
	//Item = "<?php  echo $_GET['Item'];  ?>";
	//if(Item == "" ) {return;}
	var DataTitle="دسترسی مرکز";
	DataName="DSNas_CenterAccess_";
	// Layout   ===================================================================
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	
	// TopToolbar   ===================================================================
	var TopToolbar = dhxLayout.cells("a").attachToolbar();
	DSToolbarInitial(TopToolbar);
	DSToolbarAddButton(TopToolbar,null,"Retrieve","بروزکردن","Retrieve",TopToolbar_OnRetrieveClick);

	Tree = dhxLayout.cells("a").attachTree();
	DSTreeInitial(Tree);
	Tree.loadXML(DataName+"ListRender.php?"+un()+"&act=list&ParentId="+ParentId,function(){
		if(ParentId==1){
			//Tree.openAllItems(0); 
			//Tree.lockTree(true); 
		}	

	});
	Tree.attachEvent("onBeforeCheck", function(id,state){
		if(state==0) var newstate=1
		else newstate=0
		var loader = dhtmlxAjax.getSync(DataName+"ListRender.php?"+un()+"&act=do&ParentId="+ParentId+"&Id="+id+"&state="+newstate);
		var responsearray=CleanErrorToArray(loader.xmlDoc.responseText);
		if(responsearray[0]=="OK")
			return true;
		else dhtmlx.alert("خطا، "+responsearray[1]);
		return false;
	});
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
	
}
//FUNCTION========================================================================================================================
//================================================================================================================================
function TopToolbar_OnRetrieveClick(){

Tree.deleteChildItems(0);
	Tree.loadXML(DataName+"ListRender.php?"+un()+"&act=list&ParentId="+ParentId,function(){
		if(ParentId==1){
			//Tree.openAllItems(0); 
			//Tree.lockTree(true); 
		}	

	});

	//Tree.deleteItem(0);
	/*
	Tree.loadXML(DataName+"ListRender.php?"+un()+"&act=list&ParentId="+ParentId+"&Item="+Item,function(){
		if(ParentId==1){
			Tree.openAllItems(0); 
			Tree.lockTree(true); 
		}	

	});
	*/
//	Tree.refreshItem(0);//DataName+"ListRender.php?"+un()+"&act=list&ParentId="+ParentId+"&Item="+Item);
}	
function TopToolbar_OnSaveClick(){
	alert('save');
}	
function TopToolbar_OnCheckAllClick(){
}	
function TopToolbar_OnCheckNoneClick(){
}	
	
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>