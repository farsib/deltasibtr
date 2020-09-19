<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSService_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();
$act=Get_Input('GET','DB','act','ARRAY',array("list","Cleanup","Delete"),0,0,0);

if($LReseller_Id!=1) ExitError('Only Admin can list batch process history');


switch ($act) {
    case "list":
				DSDebug(0,"DSBatchProcessHistory_ListRender->List ********************************************");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				$sql="select BatchProcess_Id,BatchProcessName,SHDATETIMESTR(CDT) as CDT,BatchItem,BatchState,".
					"SHDATETIMESTR(StartDT) as StartDT,SHDATETIMESTR(EndDT) as EndDT,BatchComment,".
					"ResellerName,INET_NTOA(ClientIP) as ClientIP ".
					"from Hbatchprocess b left join Hreseller r on b.Creator_Id=r.Reseller_Id $SortStr";
				$Fields="BatchProcess_Id,BatchProcessName,CDT,BatchItem,BatchState,StartDT,EndDT,BatchComment,ResellerName,ClientIP";
				
				

				function color_rows($row){
					$data = $row->get_value("BatchState");
					
					if($data=='Pending')
						$row->set_row_style("color:royalblue");
					else if($data=='CanceledBeforeStart')
						$row->set_row_style("color:gray");
					elseif($data=='InProgress')
						$row->set_row_style("color:green");
					else if($data=='CanceledInProgress')
						$row->set_row_style("color:brown");
					else if($data=='PartiallyDone')
						$row->set_row_style("color:orangered");
					else if($data=='Abandoned')
						$row->set_row_style("color:indianred");
					else if($data=='Done')
						$row->set_row_style("color:blue");
				}

				DSGridRender_Sql(100,$sql,"BatchProcess_Id",$Fields,"","","color_rows");
		break;
	case "Cleanup":
				DSDebug(0,"DSBatchProcessHistory_ListRender->Cleanup ********************************************");
				$sql="update Hbatchprocess b ".
					"join (select BatchProcess_Id,max(BatchItemDT) as LastDT from Hbatchprocess_users group by BatchProcess_Id) bu ".
					"on b.BatchProcess_Id=bu.BatchProcess_Id ".
					"set b.BatchState=if(b.BatchState='Pending','Abandoned',if(b.BatchState='InProgress','PartiallyDone',b.BatchState)) ".
					"where ((b.BatchState='Pending') or (b.BatchState='InProgress')) and ((timestampdiff(day,bu.LastDT,now())>3) or (bu.LastDT=0 and timestampdiff(day,b.CDT,now())>3))";
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				echo "OK~";
		break;
	case "Delete":
				DSDebug(0,"DDSBatchProcessHistoryListRender->Delete ********************************************");
				exitifnotpermit('Admin.BatchProcess.History.Delete');
				$NewRowInfo=array();
				$NewRowInfo['BatchProcess_Id']=Get_Input('GET','DB','BatchProcess_Id','INT',1,4294967295,0,0);
				
				
				$BatchState=DBSelectAsString("select BatchState from Hbatchprocess where BatchProcess_Id=".$NewRowInfo['BatchProcess_Id']);
				
				if(($BatchState=='Pending')or($BatchState=='InProgress'))
					ExitError("عملیات گروهی در حال انجام را نمی توانید حذف کنید");
				
				if($BatchState=='PartiallyDone'){
					$DTDiff=DBSelectAsString("select TIMESTAMPDIFF(hour,if(max(Hbu.BatchItemDT)=0,Hb.StartDT,max(Hbu.BatchItemDT)),now()) ".
						"from Hbatchprocess_users Hbu join Hbatchprocess Hb on Hbu.BatchProcess_Id=Hb.BatchProcess_Id ".
						"where Hb.BatchProcess_Id=".$NewRowInfo['BatchProcess_Id']);
					if($DTDiff<=48)
						ExitError("می توان عملیات گروهی که تا اندازه ای انجام شده را حذف کرد بعد از</br> ".(48-$DTDiff)." ساعت");
				}
				
				$sql="Delete from Hbatchprocess where BatchProcess_Id=".$NewRowInfo['BatchProcess_Id'];
				$res=DBUpdate($sql);
				$sql="Delete from Hbatchprocess_users where BatchProcess_Id=".$NewRowInfo['BatchProcess_Id'];
				$res=DBUpdate($sql);
				DSDebug(0,"sql=$sql\nQueryResult=$res");
				
				logdbdelete($NewRowInfo,'Delete','BatchProcess',$NewRowInfo['BatchProcess_Id'],'-');
				echo "OK~";
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>