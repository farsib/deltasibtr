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
			margin: 10px;
			overflow: hidden;
			padding: 0px;
			overflow: hidden;
			background-color:white;
        }
   </style>
<script type="text/javascript">
window.onload = function(){
	var dhxLayout = new dhtmlXLayoutObject(document.body, "1C");
	DSLayoutInitial(dhxLayout);
	dhxLayout.progressOn();

<?php
	require_once("../../lib/DSInitialReseller.php");
	DSDebug(0,"DSGetUserSession.php ....................................................................................");
	PrintInputGetPost();	
	if($LastError!=""){
	DSDebug(0,$LastError);
	?>
	dhtmlx.alert({
		title: "هشدار",
		type: "alert-error",
		title:"Error",
		text: "<?php echo escape($LastError) ?>",
		callback:function(){
			window.close();
		}
	});
	
	<?php
	}
	else{
		$User_Id=DSescape($_GET["Id"]);//Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
		$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id='$User_Id'");
		if($Visp_Id<=0){
			DSDebug(0,"Invalid User_Id($User_Id) supplied!");
			?>
			dhtmlx.alert({
				title: "هشدار",
				type: "alert-error",
				title:"Error",
				text: "Invalid User supplied",
				callback:function(){
					window.close();
				}
			});
			<?php
		}
		elseif(!ISPermit($Visp_Id,"Visp.User.UsersWebsite")){
			DSDebug(0,"Not permit Visp.User.UsersWebsite");
		?>
			dhtmlx.alert({
				title: "هشدار",
				type: "alert-error",
				title:"Error",
				text: "You have not permit to open website of this user!!!",
				callback:function(){
					window.close();
				}
			});
		<?php
		}
		else{
			$Token=GenerateRandomString(5).dsuniquid14().GenerateRandomString(5);
			// $n=DBUpdate("delete from Huser_token where TIMESTAMPDIFF(SECOND ,CDT,now())>60");
			// DSDebug(1,"'$n' expired token removed!");
			$n=DBInsert("insert ignore into Huser_token set User_Id='$User_Id',Token='$Token',CDT=now()");
			if($n>0){
			?>
				dhtmlx.modalbox({
					text:"Please wait till login<br/><br/><img src='/codebase/imgs/dhxlayout_dhx_skyblue/dhxlayout_progress.gif'/>",
					// title:"Message"
				});
				dhtmlxAjax.post("/users/commonpages/DSUserProcessLogin.php","&act=TokenLogin&Token=<?php echo $Token; ?>",function(loader){
					response=loader.xmlDoc.responseText;
					response=CleanError(response);
					if((response=='')||(response[0]=='~')){
						if(response[0]=='~')
							response=response.substring(1);
						dhtmlx.alert({
							title: "هشدار",
							type: "alert-error",
							title:"خطا",
							text: response,
							callback:function(){
								window.close();
							}
						});
					}
					else
						window.location.href="/users/";
				});
			<?php
			}
			else{
			?>
				dhtmlx.alert({
					title: "هشدار",
					type: "alert-error",
					title:"Error",
					text: "Can not create session.<br/>Try again!!!",
					callback:function(){
						window.close();
					}
				});
			<?php
			}
		}	
	}
?>
}//window.onload
</script>

<title>Delta SIB Accounting</title>
</head>
<body>
</body>
</html>