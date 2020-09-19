<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSServer_8_EditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();


$act=Get_Input('GET','DB','act','ARRAY',array("load","update","Status","Stop","Start","Restart","SetAutoStart"),0,0,0);

try {
switch ($act) {
	case "Stop":
	case "Start":
	case "Restart":
				DSDebug(1,"DSServer_8_EditRender $act ********************************************");
				exitifnotpermit(0,"Admin.Server.DeltasibServices.wwwfinish.$act");
				$act=strtolower($act);
				if(($act=="stop")||($act=="start")||($act=="restart")){
					if($act=="restart"){
						$res=runshellcommand('service','wwwfinish','stop','-');
						$res=runshellcommand('service','wwwfinish','start','-');
					}
					else
						$res=runshellcommand('service','wwwfinish',$act,'-');
					
					DSDebug(0,"pak.wwwfinish.$act    ===> '$res'");
					logdb('Edit','Server',8,"wwwfinish","pak.wwwfinish.$act");
				}
				echo "OK~".runshellcommand('service','wwwfinish','status','-');
		break;
	case "Status":
				DSDebug(1,"DSServer_8_EditRender Status ********************************************");
				exitifnotpermit(0,"Admin.Server.DeltasibServices.wwwfinish.Status");
				echo "OK~".runshellcommand('service','wwwfinish','status','-');
		break;
    case "load":
				DSDebug(1,"DSServer_8_EditRender Load ********************************************");
				exitifnotpermit(0,"Admin.Server.DeltasibServices.wwwfinish.View");
				$Server_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				$sql="SELECT '' As Error,Server_Id,PartName,Param1 as FinishPageMode,".
				"82 As DefaultListenningPort,if(Param1='MultipleIP',Param3,'') As DefaultURL, ".
				
				"if(Param1='MultipleIP',if(   Param5  ='No',0,1),0) As ChkDay1,     Param6  as IPRangeDay,     if(Param1='MultipleIP',   Param8  ,'') as ReturnURLDay1,".
				"if(Param1='MultipleIP',if(   Param9  ='No',0,1),0) As ChkTraffic1, Param10 as IPRangeTraffic, if(Param1='MultipleIP',   Param12 ,'') as ReturnURLTraffic1,".
				"if(Param1='MultipleIP',if(   Param13 ='No',0,1),0) As ChkDebit1,   Param14 as IPRangeDebit,   if(Param1='MultipleIP',   Param16 ,'') as ReturnURLDebit1,".
				"if(Param1='MultipleIP',if(   Param17 ='No',0,1),0) As ChkTime1,    Param18 as IPRangeTime,    if(Param1='MultipleIP',   Param20 ,'') as ReturnURLTime1,".
				
				"if(Param1='MultiplePort',if( Param5  ='No',0,1),0) As ChkDay2,     82  as PortDay,        if(Param1='MultiplePort', Param8  ,'') as ReturnURLDay2, ".
				"if(Param1='MultiplePort',if( Param13 ='No',0,1),0) As ChkDebit2,   84 as PortDebit,      if(Param1='MultiplePort', Param16 ,'') as ReturnURLDebit2, ".
				"if(Param1='MultiplePort',if( Param9  ='No',0,1),0) As ChkTraffic2, 83 as PortTraffic,    if(Param1='MultiplePort', Param12 ,'') as ReturnURLTraffic2, ".
				"if(Param1='MultiplePort',if( Param17 ='No',0,1),0) As ChkTime2,    85 as PortTime,       if(Param1='MultiplePort', Param20 ,'') as ReturnURLTime2 ".
				
				"from Hserver where Server_Id='$Server_Id'";
				$res = $conn->sql->query($sql);
				$data =  $conn->sql->get_next($res);
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<data>';
				if($data)
					foreach ($data as $Field=>$Value) 
						GenerateLoadField($Field,$Value);
				echo '</data>';
				
       break;
    case "update":
				DSDebug(1,"DSServer_8_EditRender Update ******************************************");
				exitifnotpermit(0,"Admin.Server.DeltasibServices.wwwfinish.Apply");
				$ServerAddrStr="";
				$NewRowInfo=array();
				$NewRowInfo['Server_Id']=Get_Input('POST','DB','Server_Id','INT',1,4294967295,0,0);
				
				for($i=1;$i<=20;++$i)
					$NewRowInfo["Param$i"]="";
				
				$NewRowInfo['Param1']=Get_Input('POST','DB','FinishPageMode','ARRAY',array("MultipleIP","MultiplePort"),0,0,0);
				
				if($NewRowInfo['Param1']=="MultipleIP"){				
					$NewRowInfo['Param2']=Get_Input('POST','DB','DefaultListenningPort','INT',0,65535,0,0);
					$NewRowInfo['Param3']=Get_Input('POST','DB','DefaultURL','STR',0,60,0,0);
					
					$NewRowInfo['Param5']=(Get_Input('POST','DB','ChkDay1','INT',0,1,0,0)==0?"No":"Yes");				
					$NewRowInfo['Param9']=(Get_Input('POST','DB','ChkTraffic1','INT',0,1,0,0)==0?"No":"Yes");
					$NewRowInfo['Param13']=(Get_Input('POST','DB','ChkDebit1','INT',0,1,0,0)==0?"No":"Yes");
					$NewRowInfo['Param17']=(Get_Input('POST','DB','ChkTime1','INT',0,1,0,0)==0?"No":"Yes");
					
					if($NewRowInfo['Param3']==''){
						$NewRowInfo['Param3']="http://".$_SERVER['SERVER_ADDR']."/users/";
						$ServerAddrStr="http://".$_SERVER['SERVER_ADDR']."/users/";
					}
					
					if($NewRowInfo['Param5']=='Yes'){
						$NewRowInfo['Param6']=Get_Input('POST','DB','IPRangeDay','STR',7,128,0,0);
						$NewRowInfo['Param8']=Get_Input('POST','DB','ReturnURLDay1','STR',0,60,0,0);
					}
					if($NewRowInfo['Param9']=='Yes'){
						$NewRowInfo['Param10']=Get_Input('POST','DB','IPRangeTraffic','STR',7,128,0,0);
						$NewRowInfo['Param12']=Get_Input('POST','DB','ReturnURLTraffic1','STR',0,60,0,0);
					}
					if($NewRowInfo['Param13']=='Yes'){
						$NewRowInfo['Param14']=Get_Input('POST','DB','IPRangeDebit','STR',7,128,0,0);
						$NewRowInfo['Param16']=Get_Input('POST','DB','ReturnURLDebit1','STR',0,60,0,0);
					}
					if($NewRowInfo['Param17']=='Yes'){
						$NewRowInfo['Param18']=Get_Input('POST','DB','IPRangeTime','STR',7,128,0,0);
						$NewRowInfo['Param20']=Get_Input('POST','DB','ReturnURLTime1','STR',0,60,0,0);
					}
				}
				else{
					$NewRowInfo['Param5']=(Get_Input('POST','DB','ChkDay2','INT',0,1,0,0)==0?"No":"Yes");				
					$NewRowInfo['Param9']=(Get_Input('POST','DB','ChkTraffic2','INT',0,1,0,0)==0?"No":"Yes");
					$NewRowInfo['Param13']=(Get_Input('POST','DB','ChkDebit2','INT',0,1,0,0)==0?"No":"Yes");
					$NewRowInfo['Param17']=(Get_Input('POST','DB','ChkTime2','INT',0,1,0,0)==0?"No":"Yes");				
					
					$tmp1=array();
					if($NewRowInfo['Param5']=='Yes'){
						$NewRowInfo['Param7']=Get_Input('POST','DB','PortDay','INT',1,65535,0,0);
						array_push($tmp1,$NewRowInfo['Param7']);
						$NewRowInfo['Param8']=Get_Input('POST','DB','ReturnURLDay2','STR',0,60,0,0);
					}
					if($NewRowInfo['Param9']=='Yes'){
						$NewRowInfo['Param11']=Get_Input('POST','DB','PortTraffic','INT',1,65535,0,0);	
						array_push($tmp1,$NewRowInfo['Param11']);
						$NewRowInfo['Param12']=Get_Input('POST','DB','ReturnURLTraffic2','STR',0,60,0,0);
					}
					if($NewRowInfo['Param13']=='Yes'){
						$NewRowInfo['Param15']=Get_Input('POST','DB','PortDebit','INT',1,65535,0,0);
						array_push($tmp1,$NewRowInfo['Param15']);
						$NewRowInfo['Param16']=Get_Input('POST','DB','ReturnURLDebit2','STR',0,60,0,0);
					}
					if($NewRowInfo['Param17']=='Yes'){
						$NewRowInfo['Param19']=Get_Input('POST','DB','PortTime','INT',1,65535,0,0);
						array_push($tmp1,$NewRowInfo['Param19']);
						$NewRowInfo['Param20']=Get_Input('POST','DB','ReturnURLTime2','STR',0,60,0,0);
					}
					$tmp2=array_unique($tmp1);
					if(count($tmp2)==0)
						ExitError("حداقل یک پورت باید انتخاب شود");
					$NewRowInfo['Param2']=implode(",",$tmp2);
				}
					
				if(($NewRowInfo['Param5']=='Yes')&&($NewRowInfo['Param8']=='')){
					$NewRowInfo['Param8']="http://".$_SERVER['SERVER_ADDR']."/users/";
					$ServerAddrStr="http://".$_SERVER['SERVER_ADDR']."/users/";
				}
				
				if(($NewRowInfo['Param9']=='Yes')&&($NewRowInfo['Param12']=='')){
					$NewRowInfo['Param12']="http://".$_SERVER['SERVER_ADDR']."/users/";
					$ServerAddrStr="http://".$_SERVER['SERVER_ADDR']."/users/";
				}
				
				if(($NewRowInfo['Param13']=='Yes')&&($NewRowInfo['Param16']=='')){
					$NewRowInfo['Param16']="http://".$_SERVER['SERVER_ADDR']."/users/";
					$ServerAddrStr="http://".$_SERVER['SERVER_ADDR']."/users/";
				}
				
				if(($NewRowInfo['Param17']=='Yes')&&($NewRowInfo['Param20']=='')){
					$NewRowInfo['Param20']="http://".$_SERVER['SERVER_ADDR']."/users/";
					$ServerAddrStr="http://".$_SERVER['SERVER_ADDR']."/users/";
				}
				
				$OldRowInfo= LoadRowInfo("Hserver","Server_Id='".$NewRowInfo['Server_Id']."'");
				
				if($NewRowInfo['Param2']!=$OldRowInfo['Param2'])
					$MongooseStr="Yes";
				else
					$MongooseStr="No";
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				
				//----------------------
				$sql= "Update Hserver set  ";
				$sql.="Param1='".$NewRowInfo['Param1']."',";
				$sql.="Param2='".$NewRowInfo['Param2']."',";
				$sql.="Param3='".$NewRowInfo['Param3']."',";
				$sql.="Param4='".$NewRowInfo['Param4']."',";
				$sql.="Param5='".$NewRowInfo['Param5']."',";
				$sql.="Param6='".$NewRowInfo['Param6']."',";
				$sql.="Param7='".$NewRowInfo['Param7']."',";
				$sql.="Param8='".$NewRowInfo['Param8']."',";
				$sql.="Param9='".$NewRowInfo['Param9']."',";
				$sql.="Param10='".$NewRowInfo['Param10']."',";
				$sql.="Param11='".$NewRowInfo['Param11']."',";
				$sql.="Param12='".$NewRowInfo['Param12']."',";
				$sql.="Param13='".$NewRowInfo['Param13']."',";
				$sql.="Param14='".$NewRowInfo['Param14']."',";
				$sql.="Param15='".$NewRowInfo['Param15']."',";
				$sql.="Param16='".$NewRowInfo['Param16']."',";
				$sql.="Param17='".$NewRowInfo['Param17']."',";
				$sql.="Param18='".$NewRowInfo['Param18']."',";
				$sql.="Param19='".$NewRowInfo['Param19']."',";
				$sql.="Param20='".$NewRowInfo['Param20']."'";
				$sql.=" Where ";
				$sql.="(Server_Id='".$NewRowInfo['Server_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				DSDebug(2,"Query result=$res");
				

					
				logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'Server',$NewRowInfo['Server_Id'],'wwwfinish');
					
				$Res=runshellcommand("php","DSCreateFinishPage","","");
				DSDebug(1,"DSCreateFinishPage->Reply [$Res]");
				echo "OK~$MongooseStr~$ServerAddrStr~";
        break;
	case "SetAutoStart":
				DSDebug(1,"DSServer_8_EditRender SetAutoStart ******************************************");
				exitifnotpermit(0,"Admin.Server.DeltasibServices.wwwfinish.SetAutoStart");
				$AutoStart=Get_Input('POST','DB','AutoStart','INT',0,1,0,0);
				
				$OutStr=runshellcommand("php","DSSetStartup","wwwfinish","$AutoStart");
				//DSDebug(0,"DSSetStartup.php wwwfinish $AutoStart T");
				DSDebug(0,$OutStr);
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
//--------------------------------

?>
