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
		.PermSp{
			font-weight:bold;
			color:navy;
		}
		.VispSp{
			font-weight:bold;
			color:green;
		}		
   </style>
<script type="text/javascript">
if(parent.Permission==undefined) window.location.href="./";
var Permission=parent.Permission;
var LoginResellerName=parent.LoginResellerName;
// var PermitBuffer=parent.PermitBuffer;
var	CopyFrom_Visp_Id=0;
var CopyFrom_VispName="";
var mygridCSV="";
window.onload = function(){
	ParentId = "<?php  echo addslashes($_GET['ParentId']);  ?>";
	if(ParentId == "" ) {return;}
	var DataTitle="Reseller_Permit";
	var DataName="DSReseller_Permit_";
	var RenderFile=DataName+"ListRender";
	
	// Layout   ===================================================================
	dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);

	var MyMenu = dhxLayout.cells("a").attachMenu();
	MyMenu.setSkin(menu_main_skin);
	MyMenu.setIconsPath(menu_icon_path);
	MyMenu.setOpenMode("win");
	MyMenu.setOverflowHeight(14);
	dhxLayout.progressOn();
	MyMenu.loadXML(RenderFile+".php?"+un()+"&act=VispList&ParentId="+ParentId,function(){dhxLayout.progressOff();});
	MyMenu.attachEvent("onClick", MyMenuClick);
	
	var TabbarMain = dhxLayout.cells("a").attachTabbar();
	TabbarMain.setSkin(tabbar_main_skin);
	TabbarMain.setImagePath(tabbar_image_path);
	TabbarMain.setHrefMode("iframes-on-demand");
	TabbarMain.setMargin(0);
	TabbarMain.setOffset(0);
	TabbarMain.enableTabCloseButton(true);
	TabbarMain.setHrefMode("iframes-on-demand");
	TabbarMain.attachEvent("onTabClose", function(id){if(TabbarMain.getNumberOfTabs()<=1){MyMenu.setItemDisabled("CloseAllTabs");}return true;});
	
	dhtmlxError.catchError("LoadXML", ds_error_handler_LoadXML);
	dhtmlxError.catchError("updateFromXML", ds_error_handler_updateFromXML);
	dhtmlxError.catchError("DataStructure", ds_error_handler_DataStructure);
//FUNCTION========================================================================================================================
//================================================================================================================================	
function MyMenuClick(id){
	if(id=="CopyPermissions"){
		var Visp_Id=TabbarMain.getActiveTab();
		if(Visp_Id == null)
			dhtmlx.alert({text:"برای کپی،ارائه دهنده مجازی اینترنت را انتخاب کنید",type:"alert-error"});
		else if(Visp_Id<=0)
			dhtmlx.alert({text:"فقط سطوح دسترسی ارائه دهنده مجازی اینترنت را می توان کپی کرد",ok:"بستن",type:"alert-error"});
		else{
			dhxLayout.progressOn();
			dhtmlxAjax.post(RenderFile+".php?"+un()+"&act=LoadCopyInformation&ParentId="+ParentId,"&From_Visp_Id="+Visp_Id,function(loader){
				dhxLayout.progressOff();
				var response=loader.xmlDoc.responseText;
				response=CleanError(response);
				var ResponseArray=response.split("~",3);
				if((response=='')||(response[0]=='~'))dhtmlx.alert("خطا، "+response.substring(1));
				else if(ResponseArray[0]!='OK')
					dhtmlx.alert("خطا، "+response);
				else{
					if((ResponseArray[1]+ResponseArray[2])<=0)
						dhtmlx.alert({text:"Selected VISP has no permissions to copy",type:"alert-error"});
					else{
						CopyFrom_Visp_Id=Visp_Id;
						CopyFrom_VispName=MyMenu.getItemText(CopyFrom_Visp_Id);					
					dhtmlx.alert({text:"کپی شد [<span class='VispSp'>"+CopyFrom_VispName+"]</span> سطوح دسترسی <br/>تعداد "+ResponseArray[1]+" مورد"+" <span class='PermSp'>مجاز است</span><br/>تعداد "+ResponseArray[2]+" مورد"+" <span class='PermSp'>مجاز نیست</span><br/><br/>ارائه دهنده مجازی اینترنت دیگری را انتخاب و سطوح دسترسی را جایگذاری کنید",ok:"بستن"});
						MyMenu.setTopText("[<span class='VispSp' title='Permitted="+ResponseArray[1]+"\r\nNot Permitted="+ResponseArray[2]+"'>"+CopyFrom_VispName+"</span>]کپی شد");
					}
				}
			});
		}
	}
	else if(id=="NoPermit"){
		dhtmlx.alert({text:"Due to parent limitation, there is no permission exists to set for this reseller.",callback:function(){dhxLayout.progressOn();window.location.reload();},ok:"Reload"});
	}
	else if(ISValidResellerSession()){
		if(id=="CloseAllTabs"){
			if(TabbarMain.getNumberOfTabs()>0){
				dhtmlx.confirm({
					title: "هشدار",
					type:"confirm-warning",
					cancel: "خیر",
					ok: "بلی",
					text: "از بستن همه برگه ها مطمئن هستید؟",
					callback: function(Result){
						if(Result){
							TabbarMain.clearAll();
							TabbarMain.normalize();
							MyMenu.setItemDisabled("CloseAllTabs");
						}
					}
				});
			}
			else
				dhtmlx.message({text:"No tab to close.",type:"error"});
			
		}
		else{
			MyMenu.setItemEnabled("CloseAllTabs");
			var AllTabs=TabbarMain.getAllTabs()
			if((AllTabs.indexOf(id))<0){
				var ItemText=MyMenu.getItemText(id);
				TabbarMain.addTab(id,ItemText,(ItemText.length)*7+40);
				TabbarMain.setContentHref(id,"DSReseller_Permit_Change_List.php?"+un()+"&ParentId="+ParentId+"&"+"Visp_Id="+id);
			}
			TabbarMain.setTabActive(id);
		}
	}
}

}//window.onload
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>
