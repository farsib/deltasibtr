<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSNetworkIPEditRender ..................................................................................");

if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("load","insert","update"),0,0,0);

try {
switch ($act) {
    case "load":
				DSDebug(1,"DSNetworkIPEditRender Load ********************************************");
				exitifnotpermit(0,"Admin.NetworkIP.View");
				$NetworkIP_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
				
				$sql="SELECT '' As Error,NetworkIP_Id,ISEnable,UseByIPDR,AssignmentTo,IPType,ISAuthenticate,UserType,ISHotSpot,INET_NTOA(StartIP) as StartIP,INET_NTOA(EndIP) as EndIP,NOE,Comment ".
					"from Hnetworkip where NetworkIP_Id='$NetworkIP_Id'";
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
    case "insert": 
				DSDebug(1,"DSNetworkIPEditRender Insert ******************************************");
				exitifnotpermit(0,"Admin.NetworkIP.Add");
				$NewRowInfo=array();
				$NewRowInfo['AssignmentTo']=Get_Input('POST','DB','AssignmentTo','STR',1,32,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['UseByIPDR']=Get_Input('POST','DB','UseByIPDR','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['IPType']=Get_Input('POST','DB','IPType','ARRAY',array("NAT","Route"),0,0,0);
				$NewRowInfo['ISAuthenticate']=Get_Input('POST','DB','ISAuthenticate','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['UserType']=Get_Input('POST','DB','UserType','ARRAY',array("DialUp","ADSL","Wireless","TD-LTE","Wi-Fi","Mobile2G","Mobile3G","Mobile4G","Mobile5G","DedicatedBandwidth"),0,0,0);
				$NewRowInfo['ISHotSpot']=Get_Input('POST','DB','ISHotSpot','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['StartIP']=Get_Input('POST','DB','StartIP','STR',7,15,0,0);
				$NewRowInfo['EndIP']=Get_Input('POST','DB','EndIP','STR',7,15,0,0);
				$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);
				$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,200,0,0);
				
				//----------------------
				$sql= "insert Hnetworkip set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="UseByIPDR='".$NewRowInfo['UseByIPDR']."',";
				$sql.="AssignmentTo='".$NewRowInfo['AssignmentTo']."',";
				$sql.="IPType='".$NewRowInfo['IPType']."',";
				$sql.="ISAuthenticate='".$NewRowInfo['ISAuthenticate']."',";
				$sql.="UserType='".$NewRowInfo['UserType']."',";
				$sql.="ISHotSpot='".$NewRowInfo['ISHotSpot']."',";
				$sql.="StartIP=INET_ATON('".$NewRowInfo['StartIP']."'),";
				$sql.="EndIP=INET_ATON('".$NewRowInfo['EndIP']."'),";
				$sql.="NOE='".$NewRowInfo['NOE']."',";
				$sql.="Comment='".$NewRowInfo['Comment']."'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['NetworkIP_Id']=$RowId;

				logdbinsert($NewRowInfo,'Add','NetworkIP',$RowId,'-');
				echo "OK~$RowId~";
        break;
    case "update":
				DSDebug(1,"DSNetworkIPEditRender Update ******************************************");
				exitifnotpermit(0,"Admin.NetworkIP.Edit");
				$NewRowInfo=array();
				$NewRowInfo['NetworkIP_Id']=Get_Input('POST','DB','NetworkIP_Id','INT',1,4294967295,0,0);
				$NewRowInfo['ISEnable']=Get_Input('POST','DB','ISEnable','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['UseByIPDR']=Get_Input('POST','DB','UseByIPDR','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['AssignmentTo']=Get_Input('POST','DB','AssignmentTo','STR',1,32,0,0);
				$NewRowInfo['IPType']=Get_Input('POST','DB','IPType','ARRAY',array("NAT","Route"),0,0,0);
				$NewRowInfo['ISAuthenticate']=Get_Input('POST','DB','ISAuthenticate','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['UserType']=Get_Input('POST','DB','UserType','ARRAY',array("DialUp","ADSL","Wireless","TD-LTE","Wi-Fi","Mobile2G","Mobile3G","Mobile4G","Mobile5G","DedicatedBandwidth"),0,0,0);
				$NewRowInfo['ISHotSpot']=Get_Input('POST','DB','ISHotSpot','ARRAY',array("Yes","No"),0,0,0);
				$NewRowInfo['StartIP']=Get_Input('POST','DB','StartIP','STR',7,15,0,0);
				$NewRowInfo['EndIP']=Get_Input('POST','DB','EndIP','STR',7,15,0,0);
				$NewRowInfo['NOE']=Get_Input('POST','DB','NOE','STR',0,32,0,0);
				$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,200,0,0);			
				
				
				$OldRowInfo= LoadRowInfo("Hnetworkip","NetworkIP_Id='".$NewRowInfo['NetworkIP_Id']."'");
				
				DSDebug(2,DSPrintArray($OldRowInfo));
				DSDebug(2,DSPrintArray($NewRowInfo));

				//----------------------
				$sql= "Update Hnetworkip set ";
				$sql.="ISEnable='".$NewRowInfo['ISEnable']."',";
				$sql.="UseByIPDR='".$NewRowInfo['UseByIPDR']."',";
				$sql.="AssignmentTo='".$NewRowInfo['AssignmentTo']."',";
				$sql.="IPType='".$NewRowInfo['IPType']."',";
				$sql.="ISAuthenticate='".$NewRowInfo['ISAuthenticate']."',";
				$sql.="UserType='".$NewRowInfo['UserType']."',";
				$sql.="ISHotSpot='".$NewRowInfo['ISHotSpot']."',";
				$sql.="StartIP=INET_ATON('".$NewRowInfo['StartIP']."'),";
				$sql.="EndIP=INET_ATON('".$NewRowInfo['EndIP']."'),";
				$sql.="NOE='".$NewRowInfo['NOE']."',";
				$sql.="Comment='".$NewRowInfo['Comment']."'";
				$sql.=" Where ";
				$sql.="(NetworkIP_Id='".$NewRowInfo['NetworkIP_Id']."')";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				if($ar!=1){//probably hack
					logdb('Edit','NetworkIP',$NewRowInfo['NetworkIP_Id'],'NetworkIP',"Update Fail,Table=NetworkIP affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=NetworkIP affected row=0");
					ExitError("(ar=$ar) مشکل امنیتی, گزارش به مدیر ارسال شد");	
				}
					
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'NetworkIP',$NewRowInfo['NetworkIP_Id'],'-')){
					logunfair("UnFair",'NetworkIP',$NewRowInfo['NetworkIP_Id'],'',"");
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
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
