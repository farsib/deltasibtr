function formatMoney(n,c, d, t){
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

function IsID(data){ 
	var regex=/^\d{1,9}$/;
	return regex.test(data);
}

function IsValidRowId(data){ 
    var n = ~~Number(data);
    return String(n) === data && n > 0;
}

function IsValidUserName(data){ 
//'~-=\\[];,./!@#$%^&*()_+|{}:;<>?'
	var regex=/^([a-zA-Z0-9-_.=:@]+)$/;
	return regex.test(data);
}


function IsValidENName(data){ 
	var regex=/^([a-zA-Z0-9-_]+)$/;
	return regex.test(data);
}
function IsValidENNameDot(data){ 
	var regex=/^([a-zA-Z0-9-_.]+)$/;
	return regex.test(data);
}

function IsValidVispName(data){ 
	var regex=/^([a-zA-Z0-9-_\{\}]+)$/;
	return regex.test(data);
}

function IsValidLoginTime(data){ 
	var regex=/^([a-zA-Z0-9,-|]+)$/;
	return regex.test(data);
}

function IsValidMobileNo(data){ 
	var regex=/^0\d{10}$/;
	return regex.test(data);
}

function IsValidNationalCode(meli_code){ 

if(
	// meli_code=="0000000000" ||
	meli_code=="1111111111" ||
	meli_code=="2222222222" ||
	meli_code=="3333333333" ||
	meli_code=="4444444444" ||
	meli_code=="5555555555" ||
	meli_code=="6666666666" ||
	meli_code=="7777777777" ||
	meli_code=="8888888888" ||
	meli_code=="9999999999" ||
	meli_code=="0123456789" ||
	meli_code=="9876543210" 
	)
	return false;
n = 
	parseInt(meli_code.charAt(0))*10 +
	parseInt(meli_code.charAt(1))*9  +
	parseInt(meli_code.charAt(2))*8  +
	parseInt(meli_code.charAt(3))*7  +
	parseInt(meli_code.charAt(4))*6  +
	parseInt(meli_code.charAt(5))*5  +
	parseInt(meli_code.charAt(6))*4  +
	parseInt(meli_code.charAt(7))*3  +
	parseInt(meli_code.charAt(8))*2;
	
c = parseInt(meli_code.charAt(9));
r = n%11;                      //reminder of n integer division to 11 stored in r
if ((r > 1 && c == 11 - r) || (r <= 1 && c == r))
	return true;
else
	return false;
}
function IsValidAdslPhone(data){ 
	var regex=/^[0-9]\d{9}$/;
	return regex.test(data);
}

function IsValidPhone(data){ 
	var regex=/^0\d{10}$|^0\d{10}-0\d{10}$|0\d{10}-0\d{10}-0\d{10}/;
//	var regex=/^0\d{10}$|^([0\d{10}-]*)[0\d{10}]+$/;
	return regex.test(data);
}

function IsValidEMail(data){ 
	var regex=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return regex.test(data);
}

function IsValidDate(data){ 
	var regex=/^[0-9]{4}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/;
	return regex.test(data);
}
function IsValidDateOrBlank(data){ 
	var regex=/^([0-9]{4}\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])){0,1}$/;
	return regex.test(data);
}

function IsValidPriod(data){ 
	var regex=/^([0-9]+,[0-9]+,[0-9]+)$/;
	return regex.test(data);
}
function IsValidMac(data){ 
	var regex=/^([[a-zA-Z0-9][a-zA-Z0-9]:]+)$/;
	return regex.test(data);
}
function ISPermitIp(data){ 
	var regex=/^(\d|[1-9]\d|1\d\d|2([0-4]\d|5[0-5]))\.(\d|[1-9]\d|1\d\d|2([0-4]\d|5[0-5]))\.(\d|[1-9]\d|1\d\d|2([0-4]\d|5[0-5]))\.(\d|[1-9]\d|1\d\d|2([0-4]\d|5[0-5]))\/(\d|[1-2]\d|3[0-2])$/;
	var temp=data.split(",");
	for(var i=0;i<temp.length;i++){
		if (!regex.test(temp[i])) return false;
	}	
	return true;
}

function IsValidPrice(data){
	var regex=/^[-]?[0-9\,]{1,15}$/;//(\.\d{0,2})?$/;
	// var regex=/^[-]?[0-9]{1,3}(?:,?[0-9]{3}){1,3}(?:\.[0-9]{2})?$/;
	return regex.test(data);
}

function IsValidPercent(data){
	if(parseInt(data)<0) return false;
	if(parseInt(data)>100) return false;
	return true;
}

function IsValidTimeRate(data){ 
	var regex=/^[0-9.]+$/;
	if (!regex.test(data)) return false;
	if(parseFloat(data)<0) return false;
	if(parseFloat(data)>2.50) return false;
	return true;
}
function IsValidTrafficRate(data){ 
	var regex=/^[0-9.]+$/;
	if (!regex.test(data)) return false;
	if(parseFloat(data)<0) return false;
	if(parseFloat(data)>2.50) return false;
	return true;
}

function IsValidCommissionFormula(data){ 
	var regex=/^0,([0-9,.]+)$/;
	if (!regex.test(data)) return false;
	var temp=data.split(",");

	if(temp.length%2==1) return false;
//alert(data+"-1");
	for(var i=0;i<temp.length;i++){
		if (isNaN(temp[i]))return false;//if nuber integer or float
		
		if(i%2==1){
			//alert(parseFloat(temp[i]));
			if ((parseFloat(temp[i])>1)||((parseFloat(temp[i])<0))) return false;
			}
	}	
//alert(data+"-2");

	
	for(var i=0;i<temp.length-2;i=i+2){
		//if (parseInt(temp[i])>=parseInt(temp[i+2])) alert(parseInt(temp[i])+">"+parseInt(temp[i]));
		if (parseInt(temp[i])>=parseInt(temp[i+2])) return false;
	}	
//alert(data+"-3");
	return true;
}

function IsValidSupporterFormula(data){ 
	var regex=/^0,([0-9,.]+)$/;//var regex=/^[0-9]$|^([0-9,]*)[0-9]+$/;
	if (!regex.test(data)) return false;
	var temp=data.split(",");

	if(temp.length%2==1) return false;
	
	for(var i=0;i<temp.length;i++)
		if (isNaN(temp[i]))return false;//if nuber integer or float
	
	for(var i=0;i<temp.length-2;i=i+2){
		if (parseInt(temp[i])>=parseInt(temp[i+2])) return false;
	}	
//alert(data+"-3");
	return true;
}

function IsValidServiceOffFormula(data){ 
	var regex=/^([0-9,]+)$/;
	if (!regex.test(data)) return false;
	var temp=data.split(",");
	if(temp.length%2==0) return false;
	return true;
}

function un(){
	if(typeof(LoginResellerName) !== "undefined")
		return "un="+(new Date().getTime())+"&LoginResellerName="+LoginResellerName;
	else
		return "un="+(new Date().getTime());
}

function ISValidResellerSession(){
	var loader = dhtmlxAjax.getSync("DSResellerIsValidSession.php?"+un()+"&WhoIs=NO");
	if(loader.xmlDoc.responseText=="YES")
		return true;
	else if(loader.xmlDoc.responseText=="NO"){
		dhtmlx.alert({text:"Session Expire, Please Relogin",title:"هشدار",ok:"بستن",type:"alert-error"});
		return false;
	}
	else{
		dhtmlx.alert({text:loader.xmlDoc.responseText,title:"هشدار",ok:"بستن",type:"alert-error"});
		return false;
	}
}
/* function SetVersion(){
	if(LoginResellerName!='admin') return 1;
	var loader = dhtmlxAjax.getSync("DSVersion.php?"+un());
	response=loader.xmlDoc.responseText;
	var responsearray=response.split("~",3);
	if(responsearray[2]=='OK')
		return responsearray[1];
	else	
		return "";
} */	
function WhoIsReseller(){
	var loader = dhtmlxAjax.getSync("DSResellerIsValidSession.php?"+un()+"&WhoIs=YES");
	response=loader.xmlDoc.responseText;
	var responsearray=response.split("~",3);
	if(responsearray[2]=='OK')
		return responsearray[1];
	else	
		return "";
}
function LoadPermissionByVisp(Visp_Id){
	if(LoginResellerName=='admin') return "admin";
	var loader = dhtmlxAjax.getSync("DSPermit.php?"+un()+"&LoadType=ByVisp&Visp_Id="+Visp_Id);
	response=loader.xmlDoc.responseText;
	if(response[0]=='~'){
		dhtmlx.alert({text:response.substring(1),title:"هشدار",ok:"بستن",type:"alert-error"});
		return "Start,End";
	}
	return response;
}	
function LoadPermissionByUser(User_Id){
	if(LoginResellerName=='admin') return "admin";
	var loader = dhtmlxAjax.getSync("DSPermit.php?"+un()+"&LoadType=ByUser&User_Id="+User_Id);
	response=loader.xmlDoc.responseText;
	if(response[0]=='~'){
		dhtmlx.alert({text:response.substring(1),title:"هشدار",ok:"بستن",type:"alert-error"});
		return "Start,End";
	}
	return response;
}	
function ISPermit(data){
	//return true;
	if(LoginResellerName=='admin') return true;
	if(Permission=='ALL') return true;
	n=Permission.search(","+data+",");
	if(n<=0) return false;
	else return true;
}
/* function ISPermitVisp(data){
	//return true;
	if(LoginResellerName=='admin') return true;
	if(VispPermission=='ALL') return true;
	n=VispPermission.search(","+data+",");
	if(n<=0) return false;
	else return true;
} */
function ds_error_handler_LoadXML(type, name, data) {
	// console.log(type);
	// console.log(name);
	// console.log(data);
	var response=data[0].responseText;
	response=CleanError(response);
	//alert("handler");
//dhtmlxError.catchError("LoadXML", ds_error_handler);
//dhtmlxError.catchError("updateFromXML", ds_error_handler_null);
//dhtmlxError.catchError("DataStructure", ds_error_handler);

	if(type=="LoadXML"){
		if(response=="")
			dhtmlx.alert({text:"Error: [" + name + "]<br/>Status: [" + data[0].status + "]",title:"هشدار",ok:"بستن",type:"alert-error"});
		else if(response[0]=='~')
			dhtmlx.alert({text:response.substring(1),title:"هشدار",ok:"بستن",type:"alert-error"});
		else 
			dhtmlx.alert({text:response,title:"هشدار",ok:"بستن",ok:"بستن",type:"alert-error"});
	}
	else if(type=="updateFromXML"){
		//do nothing
	}
	else if(type=="DataStructure"){
		alert("DS Error Type="+type+" Not configure");
	}
	else{
		alert("DS Error Type="+type+" Not configure");
	}
//if ! null	parent.dhxLayout.dhxWins.window("popupWindow").close();

};
function ds_error_handler_updateFromXML(type, name, data) {
//alert("null updateFromXML  type="+type);
	return false;
}
function ds_error_handler_DataStructure(type, name, data) {
//alert("null DataStructure  type="+type);
	return false;
}
//function ds_error_handler_null() {};
function DSGridAddFilterRow(Grid,ColumnIds,ColumnFilterType,f_OnFilterTextPressEnter){
	var FilterRowNumber=0;
	var GColumnIdArray=ColumnIds.split(",");
	while (document.getElementById(GColumnIdArray[0]+"_f_"+FilterRowNumber)!=null) FilterRowNumber++;
	if(FilterRowNumber==0){//only one time when first row add
		var dsheader='';
		for(var f=0;f<GColumnIdArray.length;f++){
			if(ColumnFilterType[f]>0){
				if(dsheader!="") dsheader=dsheader+",";
				dsheader=dsheader+"<div id='fh_"+GColumnIdArray[f]+"'></div>";//top right bottom left
			}
			else{
				if(dsheader!="") dsheader=dsheader+",";
				else dsheader=dsheader+"#rspan";
				}
			}
			Grid.attachHeader(dsheader);
			Grid.hdr.rows[2].onmousedown = Grid.hdr.rows[2].onclick = function(e) { (e || event).cancelBubble = true;}
	}
			
	for(var f=0;f<GColumnIdArray.length;f++){
		if(ColumnFilterType[f]==1){//text filter
			var input = document.createElement("input");
			input.type = "text";
			input.style.width = Grid.getColWidth(f)-22;
			input.style.marginTop = "3px";
			input.maxLength=32;
			input.id=GColumnIdArray[f]+"_f_"+FilterRowNumber;
			input.title="You can use =, <>, <, >, <=, >= and ! as NOT LIKE operator.\r\nDefault is LIKE operator if you do not enter any of above.\r\nUse % character as wildcard. For example X% find everything start with X or %X everything end with X.\r\nIf you enter X without any %, it will be equal to %X%";
			input.onkeypress=function(e){ if (e.which == 13 || e.keyCode == 13) f_OnFilterTextPressEnter();};
			document.getElementById("fh_"+GColumnIdArray[f]).appendChild(input);
				
		}
	}//for
}

function DSGridDeleteFilterRow(ColumnIds,ColumnFilterType){
	var FilterRowNumber=0;
	GColumnIdArray=ColumnIds.split(",");
	while (document.getElementById(GColumnIdArray[0]+"_f_"+FilterRowNumber)!=null) FilterRowNumber++;

	if(FilterRowNumber>0){
		FilterRowNumber--;
		for(var f=0;f<GColumnIdArray.length;f++){
			if(ColumnFilterType[f]==1){//text filter
				var input=document.getElementById(GColumnIdArray[f]+"_f_"+FilterRowNumber);
				document.getElementById("fh_"+GColumnIdArray[f]).removeChild(input);
			}	
		}//for
	}
}


function LoadGridDataFromServerProgress(f_dhxLayout,f_RenderFile,f_Grid,f_LoadType,f_FilterState,f_ColumnIds,f_ColumnFilterType,f_ISSort,f_ExtraFilter,f_DoAfterRefresh){
	var FilterRowNumber=0;
	var DSFilter='';

	if(f_ISSort){
		state=f_Grid.getSortingState();	
		SortStr="&SortField="+f_Grid.getColumnId(state[0])+"&SortOrder="+((state[1]=="asc")?"asc":"desc");
	}
	else
		SortStr="&SortField=&SortOrder=";
	
	if(f_FilterState==true){
		while (document.getElementById(f_Grid.getColumnId(0)+"_f_"+FilterRowNumber)!=null) FilterRowNumber++;
		for(var r=0;r<FilterRowNumber;r++){
			for(var f=0;f<f_Grid.getColumnsNum();f++){
				if(f_ColumnFilterType[f]==1){//text filter
					var input =document.getElementById(f_Grid.getColumnId(f)+"_f_"+r);
					if(input.value!="")
						DSFilter=DSFilter+"&dsfilter["+r+"]["+f_Grid.getColumnId(f)+"]="+input.value;
				}
			}//for f=0
		}
	}
	RowId=f_Grid.getSelectedRowId();
	if(f_LoadType=="Update")
		f_Grid.updateFromXML(f_RenderFile+".php?"+un()+"&act=list"+SortStr+DSFilter+"&FilterRowNumber="+FilterRowNumber+"&ExtraFilter="+f_ExtraFilter,function(){f_dhxLayout.progressOff();f_DoAfterRefresh()});
	else if(f_LoadType=="LoadAll"){
		f_dhxLayout.progressOn();
		f_Grid.clearAll();
		f_Grid.loadXML(f_RenderFile+".php?"+un()+"&act=list"+SortStr+DSFilter+"&FilterRowNumber="+FilterRowNumber+"&ExtraFilter="+f_ExtraFilter,function(){f_dhxLayout.progressOff();f_DoAfterRefresh()});
	}
	if(f_ISSort) f_Grid.setSortImgState(true,state[0],state[1]);
	return false;
}	

function LoadGridDataFromServer(f_RenderFile,f_Grid,f_LoadType,f_FilterState,f_ColumnIds,f_ColumnFilterType,f_ISSort,f_ExtraFilter,f_DoAfterRefresh){
	var FilterRowNumber=0;
	var DSFilter='';
	
	if(f_ISSort){
		state=f_Grid.getSortingState();	
		SortStr="&SortField="+f_Grid.getColumnId(state[0])+"&SortOrder="+((state[1]=="asc")?"asc":"desc");
	}
	else
		SortStr="&SortField=&SortOrder=";
	if(f_FilterState==true){
		while (document.getElementById(f_Grid.getColumnId(0)+"_f_"+FilterRowNumber)!=null) FilterRowNumber++;
		for(var r=0;r<FilterRowNumber;r++){
			for(var f=0;f<f_Grid.getColumnsNum();f++){
				if(f_ColumnFilterType[f]==1){//text filter
					var input =document.getElementById(f_Grid.getColumnId(f)+"_f_"+r);
					if(input.value!="")
						DSFilter=DSFilter+"&dsfilter["+r+"]["+f_Grid.getColumnId(f)+"]="+input.value;
				}
			}//for f=0
		}
	}	
	
	RowId=f_Grid.getSelectedRowId();
	if(f_LoadType=="Update"){
		f_Grid.updateFromXML(f_RenderFile+".php?"+un()+"&act=list"+SortStr+DSFilter+"&FilterRowNumber="+FilterRowNumber+f_ExtraFilter,f_DoAfterRefresh);
	}	
	else if(f_LoadType=="LoadAll"){
		f_Grid.clearAll();
		f_Grid.loadXML(f_RenderFile+".php?"+un()+"&act=list"+SortStr+DSFilter+"&FilterRowNumber="+FilterRowNumber+f_ExtraFilter,f_DoAfterRefresh);
	}
	if(f_ISSort) f_Grid.setSortImgState(true,state[0],state[1]);
	return false;
}	
					
function DSGridInitial(Grid,ColumnIds,Header,InitWidths,ColAlign,GColTypes,ColumnIdsVisibility,Footer,ISSort,GColSorting,ColSortIndex,SortDirection){
    Grid.setSkin(grid_main_skin);
    Grid.setImagePath(grid_image_path);
	Grid.setColumnIds(ColumnIds);
    Grid.setHeader(Header);
    Grid.setInitWidths(InitWidths);
    Grid.setColAlign(ColAlign);
	if (ISSort) Grid.setColSorting(GColSorting);
    Grid.setColTypes(GColTypes);
	for (var i=0;i<ColumnIdsVisibility.length;i++){
		if(ColumnIdsVisibility[i]==0)
			Grid.setColumnHidden(i,true); 				
	}
	if (Footer != '') Grid.attachFooter(Footer);
    
	Grid.init();

    if (ISSort){
		Grid.setSortImgState(true,ColSortIndex,SortDirection);
		Grid.attachEvent("onBeforeSorting",function(ind,type,direction){
			Grid.setSortImgState(true,ind,direction);
		});
	}
    Grid.enableSmartRendering(true,100);
	// Grid.enablePreRendering(50);
}

/* function DSGridInitial4(Grid,ColumnIds,Header,InitWidths,ColAlign,GColTypes,ColumnIdsVisibility,Footer,ISSort,GColSorting,ColSortIndex,SortDirection,GAttacheHeader1){
	Grid.setColumnIds(ColumnIds);
    Grid.setHeader(Header);
	if(GAttacheHeader1!=null)
		Grid.attachHeader(GAttacheHeader1);
    Grid.setInitWidths(InitWidths);
    Grid.setColAlign(ColAlign);
	if (ISSort) Grid.setColSorting(GColSorting);
    Grid.setColTypes(GColTypes);
	for (var i=0;i<ColumnIdsVisibility.length;i++){
		if(ColumnIdsVisibility[i]==0)
			Grid.setColumnHidden(i,true); 				
	}
	if (Footer != '') Grid.attachFooter(Footer);
    
	Grid.init();

    if (ISSort){
		Grid.setSortImgState(true,ColSortIndex,SortDirection);
		Grid.attachEvent("onBeforeSorting",function(ind,type,direction){
			Grid.setSortImgState(true,ind,direction);
		});
	}	
	
    Grid.enableSmartRendering(true,100);


} */

function DSTreeInitial(f_Tree){
	f_Tree.setSkin(tree_main_skin);
	f_Tree.setImagePath(tree_image_path);
	f_Tree.enableCheckBoxes(1);
	f_Tree.enableThreeStateCheckboxes(true);
}
function DSLayoutInitial(Layout){
	Layout.setSkin(dhxLayout_main_skin);
	Layout.cells("a").hideHeader();
	Layout.dhxWins.setEffect("move", true);
}

function DSToolbarInitial(Toolbar){
	Toolbar.setSkin(toolbar_main_skin);
	Toolbar.setIconsPath(toolbar_icon_path);
}	

function DSTabbarInitial(f_TabbarMain,f_TabbarMainArray){
	
	f_TabbarMain.setSkin(tabbar_main_skin);
	f_TabbarMain.setImagePath(tabbar_image_path);
	f_TabbarMain.setHrefMode("iframes-on-demand");
	f_TabbarMain.setMargin(0);
	f_TabbarMain.setOffset(0);
	
	f_TabbarMain.addTab(0,f_TabbarMainArray[0][0] , f_TabbarMainArray[0][2]);
	f_TabbarMain.setTabActive(0);
}

/* function DSTabbarInitial1(f_TabbarMain){
	
	f_TabbarMain.setSkin(tabbar_main_skin);
	f_TabbarMain.setImagePath(tabbar_image_path);
	f_TabbarMain.setHrefMode("iframes-on-demand");
	f_TabbarMain.setMargin(0);
	f_TabbarMain.setOffset(0);
} */

function DSInitialPopup(f_Toolbar,f_PopupId,f_PopupOnShow){
	var f_Popup=new dhtmlXPopup({toolbar: f_Toolbar,id: f_PopupId});
	f_Popup.setSkin(popup_main_skin);
	f_Popup.attachEvent("onShow",f_PopupOnShow);
	return f_Popup;
}

        
function DSInitialForm(f_AttachTo,f_FormStr,f_FormPopupHelp,f_FormFieldHelpId,f_FormFieldHelp,f_FormOnButtonClick){
	var f_Form = f_AttachTo.attachForm(f_FormStr);
	f_Form.setSkin(form_main_skin);
	f_FormPopupHelp = new dhtmlXPopup({form: f_Form,id:f_FormFieldHelpId,mode:"top"});
	f_FormPopupHelp.attachEvent("onContentClick",function(){f_FormPopupHelp.hide()});
	f_FormPopupHelp.setSkin(popup_main_skin);
	f_Form.enableLiveValidation(true);
	/*
	for(var i=0;i<f_FormFieldHelpId.length;i++){
		f_Form
	}
	*/
	f_Form.attachEvent("onValidateError", function (input, value, result){
		f_Form.setItemFocus(input);
		if(value=='')
			dhtmlx.message({text:"فیلد ["+f_Form.getItemLabel(input).replace(/\[.*?\]|[:]/g,"")+"] نمی تواند خالی باشد", type:"error"});
		else
			dhtmlx.message({text:" "+f_Form.getItemLabel(input).replace(/\[.*?\]|[:]/g,"")+" "+"["  +value+"]"+ " صحیح نیست ", type:"error"});
	});
	f_Form.attachEvent("onBlur", function(id, value) {f_FormPopupHelp.hide(); });
	f_Form.attachEvent("onFocus", function(id) {
		if(f_Form.getItemType(id)=="input")
			f_Form.getInput(id).select();
	});
	f_Form.attachEvent("onInputChange", function(id,value) {
		if(f_Form.getItemType(id)=="input")
			f_Form.getInput(id).style.direction=GetTextDirection(value);
	});
	f_Form.attachEvent("onChange", function(id,value) {
		if(f_Form.getItemType(id)=="select")
			SetSelectDirection(f_Form,id,1);
	});
	f_Form.attachEvent("onOptionsLoaded", function(id) {
		if(f_Form.getItemType(id)!="select") return;
		var Opts=f_Form.getSelect(id);
		// dhtmlx.message({text:"id="+id+"<br/>Opts.length="+Opts.length+"<br/>Opts.selectedIndex="+Opts.selectedIndex,expire:50000});
		if((Opts.length==0)||(Opts.selectedIndex<0)) return;
		Opts.style.direction=GetTextDirection(Opts.options[Opts.selectedIndex].text);
	});
	f_Form.attachEvent("onInfo", function (name){
		if(f_FormPopupHelp.isVisible()){
			f_FormPopupHelp.hide(name);
		}
		else if(f_FormFieldHelpId.indexOf(name)!=-1){	
			f_FormPopupHelp.attachHTML("<div style='border:1px solid #a4bed4;padding:8px'>"+f_FormFieldHelp[name]+"</div>");
			f_FormPopupHelp.show(name);
		}	
	});
	f_Form.attachEvent("onButtonClick", f_FormOnButtonClick);
	f_Form.setFocusOnFirstActive();
	return f_Form;
}

function DSFormValidate(f_Form,f_FormFieldHelpId){
	for(var i=0;i<f_FormFieldHelpId.length;i++){
		// dhtmlx.message("id="+f_FormFieldHelpId[i]+"\nisItem="+f_Form.isItem(f_FormFieldHelpId[i])+"\nIsEnable="+f_Form.isItemEnabled(f_FormFieldHelpId[i])+"\nIsHidden="+f_Form.isItemHidden(f_FormFieldHelpId[i])+"\nValidate="+f_Form.validateItem(f_FormFieldHelpId[i]))
		if(f_Form.isItem(f_FormFieldHelpId[i])&&f_Form.isItemEnabled(f_FormFieldHelpId[i])&&(!f_Form.isItemHidden(f_FormFieldHelpId[i])))//
			if(!f_Form.validateItem(f_FormFieldHelpId[i])) {
				
				return false;
			}
	}
	
	return true;
}

function CleanError(s){
//<?xml version="1.0" encoding="UTF-8"?><data><Error><![CDATA[~MySQL operation failedUnknown column 'v.Visp_Id' in 'on clause']]></Error></data>
	n=s.indexOf("<data><Error><![CDATA[")
	if(n>=0){
		return s.slice(n+22,s.length-18);//18 to cut ]]></Error></data> from last
	}	
	return s;	
}

function CleanErrorToArray(s){
	s=CleanError(s);
	var k=s.split("~",2);
	//alert("k.len="+k.length);
	return k;	
}

function DSFormUpdateRequestProgress(f_dhxLayout,f_Form,f_url,f_FormDoAfterUpdateOk,f_FormDoAfterUpdateFail){
	f_dhxLayout.cells("a").progressOn();
	f_Form.lock();
	f_Form.send(f_url,"post",function(loader, response){
		f_Form.unlock();
		f_dhxLayout.cells("a").progressOff();
		response=CleanError(response);
		
		var ErrorStr="";
		var responsearray=response.split("~",2);
		if (responsearray.length==0) ErrorStr=response;//
		if(response==""){
			ErrorStr="خطا،هیچ پاسخی از سرور دریافت نشد";
			if(f_FormDoAfterUpdateFail!=null) f_FormDoAfterUpdateFail(response);
		}
		else if(responsearray[0]!='OK'){
			if(response[0]=='~') ErrorStr=response.substring(1);//"Error,"+response.substring(1)
			else ErrorStr=response;
			if(f_FormDoAfterUpdateFail!=null) f_FormDoAfterUpdateFail(response);
		}
		else{
			if(responsearray[1]=='') dhtmlx.message("!عملیات با موفقیت انجام شد");
			else ErrorStr="هشدار,"+responsearray[1];//dhtmlx.alert("Warning, "+responsearray[1]);
			if(f_FormDoAfterUpdateOk!=null) f_FormDoAfterUpdateOk(response);
		}
		if(ErrorStr!="") alert(ErrorStr);//MUST use alert function
	});
}

/* function DSFormUpdateRequest(f_Form,f_url,f_FormDoAfterUpdateOk,f_FormDoAfterUpdateFail){
	f_Form.send(f_url,"post",function(loader, response){
		response=CleanError(response);
		
		var ErrorStr="";
		var responsearray=response.split("~",2);
		if (responsearray.length==0) ErrorStr=response;//
		if(response==""){
			ErrorStr="Error, UpdateRequest return nothing";
			if(f_FormDoAfterUpdateFail!=null) f_FormDoAfterUpdateFail();
		}
		else if(responsearray[0]!='OK'){
			if(response[0]=='~') ErrorStr=response.substring(1);//"Error,"+response.substring(1)
			else ErrorStr=response;
			if(f_FormDoAfterUpdateFail!=null) f_FormDoAfterUpdateFail();
		}
		else{
			if(responsearray[1]=='') dhtmlx.message("Your operation has been successfully done!");
			else ErrorStr="Warning,"+responsearray[1];//dhtmlx.alert("Warning, "+responsearray[1]);
			if(f_FormDoAfterUpdateOk!=null) f_FormDoAfterUpdateOk();
		}
		if(ErrorStr!="") alert(ErrorStr);//MUST use alert function
	});
} */

function DSFormInsertRequestProgress(f_dhxLayout,f_Form,f_url,f_FormDoAfterInsertOk,f_FormDoAfterInsertFail){
	f_dhxLayout.cells("a").progressOn();
	f_Form.lock();	
	f_Form.send(f_url,"post",function(loader, response){
		f_Form.unlock();
		f_dhxLayout.cells("a").progressOff()
		response=CleanError(response);
		var responsearray=response.split("~",2);
		if(response==""){
			dhtmlx.alert({text:"هیچ پاسخی از سرور دریافت نشد",title:"هشدار",ok:"بستن",type:"alert-error"});
			f_FormDoAfterInsertFail(response);
		}
		else if(responsearray[0]!='OK'){
			if(response[0]=='~')dhtmlx.alert({text:response.substring(1),title:"هشدار",ok:"بستن",type:"alert-error"});
			else dhtmlx.alert({text:response,title:"هشدار",ok:"بستن",type:"alert-error"});
			f_FormDoAfterInsertFail(response);
		}
		else{
			f_rowid=responsearray[1];
			if(!IsValidRowId(f_rowid))
				dhtmlx.alert({text:"شناسه سطر معتبر نیست->"+response[1],title:"هشدار",ok:"بستن",type:"alert-error"});
			else {
				f_FormDoAfterInsertOk(f_rowid);
				dhtmlx.message("!داده های شما با موفقیت ذخیره شد");
			}	
		}	

	});
}

/* function DSFormInsertRequest(f_Form,f_url,f_FormDoAfterInsertOk,f_FormDoAfterInsertFail){
	f_Form.lock();
	f_Form.send(f_url,"post",function(loader, response){
		f_Form.unlock();
		response=CleanError(response);
		var responsearray=response.split("~",2);
		if(response==""){
			dhtmlx.alert("Error, Not reply from server");
			f_FormDoAfterInsertFail();
		}
		else if(responsearray[0]!='OK'){
			if(response[0]=='~')dhtmlx.alert("Error, "+response.substring(1));
			else dhtmlx.alert("Error, "+response);
			f_FormDoAfterInsertFail();
		}
		else{
			f_rowid=responsearray[1];
			if(!IsValidRowId(f_rowid))
				dhtmlx.alert("Error, Not valid Rowid->"+response[1]);
			else {
				f_FormDoAfterInsertOk(f_rowid);
				dhtmlx.message("Your data has been successfully saved!");
			}	
		}	

	});
} */

function DSFormLoadProgress(f_dhxLayout,f_Form,f_FormDoAfterLoadOk,f_FormDoAfterLoadFail,f_url,f_rowid,f_DisableItems,f_EnableItems,f_HideItems,f_ShowItems){
	if(f_rowid<1) {f_FormDoAfterLoadFail();return}
	f_dhxLayout.cells("a").progressOn();
	f_Form.lock();
	f_Form.load(f_url+un()+"&act=load&id="+f_rowid,function(){
		f_Form.unlock();
		f_dhxLayout.cells("a").progressOff();
		var ErrorValue=f_Form.getItemValue("Error");
		for(var i=0;i<f_DisableItems.length;i++)
			f_Form.disableItem(f_DisableItems[i]);
		for(var i=0;i<f_EnableItems.length;i++)
			f_Form.enableItem(f_EnableItems[i]);
		for(var i=0;i<f_HideItems.length;i++)
			f_Form.hideItem(f_HideItems[i]);
		for(var i=0;i<f_ShowItems.length;i++)
			f_Form.showItem(f_ShowItems[i]);
		
		if((ErrorValue!=null)&&(ErrorValue!='')){
			if(ErrorValue[0]=="~")
				ErrorValue=ErrorValue.substring(1);
			alert("خطا, "+ErrorValue);
			f_FormDoAfterLoadFail();
		}
		else{
			f_Form.forEachItem(function(id){
				if(f_Form.getItemType(id)=="input"){
					var Inp=f_Form.getInput(id);
					Inp.style.direction=GetTextDirection(Inp.value);
				}
				else if(f_Form.getItemType(id)=="select"){
					SetSelectDirection(f_Form,id,1);
				}
			});
			f_FormDoAfterLoadOk();
		}	
	});
}

/* function LoadForm(f_Form,f_FormDoAfterLoadOk,f_FormDoAfterLoadFail,f_url,f_rowid,f_DisableItems,f_EnableItems,f_HideItems,f_ShowItems){
	if(f_rowid<1) {f_FormDoAfterLoadFail();return}
	f_Form.load(f_url+un()+"&act=load&id="+f_rowid,function(){
	
		var ErrorValue=f_Form.getItemValue("Error");
		for(var i=0;i<f_DisableItems.length;i++)
			f_Form.disableItem(f_DisableItems[i]);
		for(var i=0;i<f_EnableItems.length;i++)
			f_Form.enableItem(f_EnableItems[i]);
		for(var i=0;i<f_HideItems.length;i++)
			f_Form.hideItem(f_HideItems[i]);
		for(var i=0;i<f_ShowItems.length;i++)
			f_Form.showItem(f_ShowItems[i]);
		
		if((ErrorValue!=null)&&(ErrorValue!='')){
			alert("Error, "+ErrorValue);
			f_FormDoAfterLoadFail();
		}
		else{
			f_FormDoAfterLoadOk();
		}	
	});
} */

function FormDisableAllItem(f_Form){
	f_Form.lock();
	f_Form.forEachItem(function(id){
		f_Form.disableItem(id);
	});
}

function FormShowItem(f_Form,f_ShowItems){
	for(var i=0;i<f_ShowItems.length;i++)
		f_Form.showItem(f_ShowItems[i]);
}
function FormHideItem(f_Form,f_HideItems){
	for(var i=0;i<f_HideItems.length;i++)
		f_Form.hideItem(f_HideItems[i]);
}

function FormDisableItem(f_Form,f_DisableItems){
	for(var i=0;i<f_DisableItems.length;i++)
		f_Form.disableItem(f_DisableItems[i]);
}

function FormEnableItem(f_Form,f_EnableItems){
	for(var i=0;i<f_EnableItems.length;i++)
		f_Form.enableItem(f_EnableItems[i]);
}

function FormRemoveItem(f_Form,f_RemoveItems){
	for(var i=0;i<f_RemoveItems.length;i++)
		f_Form.removeItem(f_RemoveItems[i]);
}

function FormAddItem(f_Form,f_AddItems,f_pos){
	for(var i=0;i<f_AddItems.length;i++)
		f_Form.addItem(null, f_AddItems[i], f_pos+i);
}

function DSToolbarAddButton(f_Toolbar,f_Position,f_id,f_title,f_ImageName,f_OnClickDo){
	f_Toolbar.addButton(f_id, f_Position, f_title,"ds_"+f_ImageName+".png","ds_"+f_ImageName+"_dis.png");
	f_Toolbar.attachEvent("onclick",function(id){
		if(id==f_id){
			f_OnClickDo(id);
		}
	});
}

function DSToolbarAddButtonPopup(f_Toolbar,f_Position,f_id,f_title,f_ImageName){
	f_Toolbar.addButton(f_id, f_Position, f_title,"ds_"+f_ImageName+".png","ds_"+f_ImageName+"_dis.png");
}

function DSCreateWindow(f_dhxLayout,f_Window,f_HelpURL){
	var f_popupWindow=f_dhxLayout.dhxWins.createWindow(f_Window);
	f_popupWindow.setText("Loading ...");
	if(f_HelpURL!=""){
		f_popupWindow.button("Help").show();
		f_popupWindow.attachEvent("onHelp",function(){window.open("http://www.smartispbilling.com/help/scr/"+f_HelpURL+".htm","_blank");});
	}
	return f_popupWindow;
}

function CopyTextToClipBoard(CopyText){
	var TMP=document.createElement("INPUT");
    TMP.value=CopyText;
    document.body.appendChild(TMP);
    TMP.select();
  	var res=document.execCommand("copy");
    document.body.removeChild(TMP);
	if(res)
		dhtmlx.message(CopyText+" !در کلیپ بورد ذخیره شد");
	else{
		dhtmlx.message({text:"!!! نمی تواند در کلیپ بورد ذخیره شود",type:"error"});
		setTimeout(function(){prompt("Ctrl+C را و سپس Enter را برای کپی به کلیپ بورد بفشارید",CopyText);},100);
	}
}

function GetTextDirection(Str){
var Len=Str.length;
i=0;
while( (i<Len) && ( (Str.charCodeAt(i)<=32)|| ((Str.charCodeAt(i)>=48)&&(Str.charCodeAt(i)<=57)) ) ) i++;
if((i==Len)||(Str.charCodeAt(i)<200))
	return 'ltr';
else
	return 'rtl';
}
function SetSelectDirection(f_Form,f_Id,TryCount){
	// dhtmlx.message({text:f_Id,expire:20000});
	if((TryCount>10)||(!f_Form.isItem(f_Id)))
		return;
	var Opts=f_Form.getSelect(f_Id);
	if(Opts.length>0)
		Opts.style.direction=GetTextDirection(Opts.options[Opts.selectedIndex].text);
	else
		setTimeout(function(){
			SetSelectDirection(f_Form,f_Id,TryCount+1);
		},TryCount*100);
	
}