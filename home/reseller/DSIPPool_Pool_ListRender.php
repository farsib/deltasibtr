<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSIPPool_Pool_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","insert","LoadPoolForm",'update','Delete'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSIPPool_Pool_ListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Pool.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";

				$IPPool_Id=Get_Input('GET','DB','IPPool_Id','INT',1,4294967295,0,0);
				DSGridRender_Sql(100,
					"SELECT IPPool_Pool_Id,INET_NTOA(IPFrom) As IPFrom,INET_NTOA(IPTo)As IPTo from Hippool_pool ".
					"Where (IPPool_Id=$IPPool_Id)" .$sqlfilter." $SortStr ",
					"IPPool_Pool_Id",
					"IPPool_Pool_Id,IPFrom,IPTo",
					"","","");
       break;
    case "LoadPoolForm":
				DSDebug(1,"DSIPPool_Pool_ListRender Load ********************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Pool.List");
				$IPPool_Pool_Id=Get_Input('GET','DB','IPPool_Pool_Id','INT',1,4294967295,0,0);
				$sql="SELECT IPPool_Pool_Id,INET_NTOA(IPFrom) As IPFrom,INET_NTOA(IPTo)As IPTo from Hippool_pool ".
					"where (IPPool_Pool_Id=$IPPool_Pool_Id)";
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
				DSDebug(1,"DSIPPool_Pool_ListRender Insert ******************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Pool.Add");
				$NewRowInfo=array();
				$NewRowInfo['IPPool_Id']=Get_Input('GET','DB','IPPool_Id','INT',0,4294967295,0,0);
				$NewRowInfo['IPFrom']=Get_Input('POST','DB','IPFrom','STR',7,15,0,0);
				$NewRowInfo['IPTo']=Get_Input('POST','DB','IPTo','STR',7,15,0,0);
				
				$IPPool_Id=$NewRowInfo['IPPool_Id'];
				$Start=sprintf('%u', ip2long($NewRowInfo['IPFrom']));
				$End=sprintf('%u', ip2long($NewRowInfo['IPTo']));
				If($Start>$End)
					ExitError('شروع محدوده آی پی بزرگتر از پایان آن است');
				
				//$Count=DBSelectAsString("Select Count(*) from Honline_usedip Where (IPPool_Id=$IPPool_Id)And(IP>=$Start)And(IP<=$End)");
				$IPPoolName=DBSelectAsString("Select IPPoolName from  Honline_usedip o_ui left join Hippool i on (o_ui.IPPool_Id=i.IPPool_Id) Where (IP>=$Start)And(IP<=$End)");
				If($IPPoolName!='')
					ExitError("این محدوده از آی پی در دامنه زیر وجود دارد</br>'$IPPoolName'");
				
				$sql= "insert Hippool_pool set ";
				$sql.="IPPool_Id='".$NewRowInfo['IPPool_Id']."',";
				$sql.="IPFrom=INET_ATON('".$NewRowInfo['IPFrom']."'),";
				$sql.="IPTo=INET_ATON('".$NewRowInfo['IPTo']."')";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['IPPool_Pool_Id']=$RowId;
				logdbinsert($NewRowInfo,'Add','IPPool',$NewRowInfo['IPPool_Id'],'Pool');
				While($Start<=$End){
					DBInsert("Insert IGNORE into Honline_usedip Set	IP=$Start,IPPool_Pool_Id=$RowId,IPPool_Id=$IPPool_Id,LastUsedDT=Now()");
					$Start=$Start+1;
				}
				
				echo "OK~$RowId~";
        break;
    case "update": 
				DSDebug(1,"DSIPPool_Pool_ListRender Update ******************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Pool.Edit");
				$NewRowInfo=array();
				$IPPool_Pool_Id=Get_Input('POST','DB','IPPool_Pool_Id','INT',1,4294967295,0,0);
				$NewRowInfo['IPFrom']=Get_Input('POST','DB','IPFrom','STR',7,15,0,0);
				$NewRowInfo['IPTo']=Get_Input('POST','DB','IPTo','STR',7,15,0,0);
				$IPPool_Id=DBSelectAsString("Select IPPool_Id From Hippool_pool Where IPPool_Pool_Id=$IPPool_Pool_Id");
				$Start=sprintf('%u', ip2long($NewRowInfo['IPFrom']));
				$End=sprintf('%u', ip2long($NewRowInfo['IPTo']));
				If($Start>$End)
					ExitError('شروع محدوده آی پی بزرگتر از پایان آن است');
				
				//$Count=DBSelectAsString("Select Count(*) from Honline_usedip Where (IPPool_Pool_Id<>$IPPool_Pool_Id)And(IPPool_Id=$IPPool_Id)And(IP>=$Start)And(IP<=$End)");
				$IPPoolName=DBSelectAsString("Select IPPoolName from  Honline_usedip o_ui left join Hippool i on (o_ui.IPPool_Id=i.IPPool_Id) Where (IPPool_Pool_Id<>$IPPool_Pool_Id)And(IP>=$Start)And(IP<=$End)");
				If($IPPoolName!='')
					ExitError("این محدوده از آی پی در دامنه زیر وجود دارد</br>'$IPPoolName'");


				$OldRowInfo= LoadRowInfoSql("SELECT IPPool_Pool_Id,INET_NTOA(IPFrom) As IPFrom,INET_NTOA(IPTo)As IPTo from Hippool_pool where (IPPool_Pool_Id=$IPPool_Pool_Id)");
				
				
				//----------------------
				$sql= "update Hippool_pool set ";
				$sql.="IPFrom=INET_ATON('".$NewRowInfo['IPFrom']."'),";
				$sql.="IPTo=INET_ATON('".$NewRowInfo['IPTo']."')";
				$sql.=" Where ";
				$sql.="(IPPool_Pool_Id=$IPPool_Pool_Id)";
				$res = $conn->sql->query($sql);
				$ar=$conn->sql->get_affected_rows();
				DBUpdate("Delete From Honline_usedip Where (IPPool_Pool_Id=$IPPool_Pool_Id)And(IP<$Start Or IP>$End)");;
				While($Start<=$End){
					DBInsert("Insert IGNORE into Honline_usedip Set	IP=$Start,IPPool_Pool_Id=$IPPool_Pool_Id,IPPool_Id=$IPPool_Id,LastUsedDT=Now()");
					$Start=$Start+1;
				}
				
				/*
				if($ar!=1){//probably hack
					logdb('Edit','IPPool',$NewRowInfo['IPPool_Id'],'Param',"Update Fail,Table=Param affected row=0");
					logsecurity('UpdateFail',"$LReseller_Id, Update Fail,Table=Param affected row=0");
					ExitError("(ar=$ar) Security problem, Report Sent to Administrator");	
				}
					*/
				if(!logdbupdate($NewRowInfo,$OldRowInfo,"Edit",'IPPool',$IPPool_Id,'Pool')){
					logunfair("UnFair",'IPPool',$IPPool_Id,'Pool','');
					echo "OK~Unfair Request, Report sent to administrator";
				}
				else	
					echo "OK~";
					
        break;
		
	case "Delete":
				DSDebug(1,"DSIPPool_Pool_ListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.IPPool.Pool.Delete");
				$NewRowInfo=array();
				$NewRowInfo['IPPool_Pool_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$IPPool_Id=DBSelectAsString("Select IPPool_Id from Hippool_pool where IPPool_Pool_Id=".$NewRowInfo['IPPool_Pool_Id']);
				$ar=DBDelete('delete from Hippool_pool Where IPPool_Pool_Id='.$NewRowInfo['IPPool_Pool_Id']);
				logdbdelete($NewRowInfo,'Delete','IPPool',$IPPool_Id,'Pool');
				echo "OK~";
		break;
		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>