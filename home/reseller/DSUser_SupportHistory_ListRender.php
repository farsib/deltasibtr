<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_SupportHistory_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list","Add","Delete","SelectSupportItem"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_SupportHistory_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.SupportHistory.List");			

				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				function color_rows($row){
					$CustomerSatisfactionLevel = $row->get_value("CustomerSatisfactionLevel");
					if($CustomerSatisfactionLevel=='VeryBad')
						$row->set_row_style("color:red");
					elseif($CustomerSatisfactionLevel=='Bad')
						$row->set_row_style("color:firebrick");
					elseif($CustomerSatisfactionLevel=='Good')
						$row->set_row_style("color:darkgreen");
					elseif($CustomerSatisfactionLevel=='VeryGood')
						$row->set_row_style("color:green");	
				}

				DSGridRender_Sql(100,"SELECT  User_SupportHistory_Id,ResellerName as Creator,{$DT}DateTimeStr(CDT) As CDT,SupportItemTitle,Comment ".//,CustomerSatisfactionLevel ".
					"From Huser_supporthistory u_s left join Hsupportitem s on u_s.SupportItem_Id=s.SupportItem_Id left join Hreseller r on u_s.Creator_Id=r.Reseller_Id ".
					"where (User_Id=$User_Id) ".$sqlfilter." $SortStr ",
					"User_SupportHistory_Id",
					"User_SupportHistory_Id,Creator,CDT,SupportItemTitle,Comment",//,CustomerSatisfactionLevel",
					"","","");
       break;
    case "Add":
				DSDebug(1,"DSUser_SupportHistory_ListRender AddSupportHistory ******************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.SupportHistory.Add");
				
				$SupportItem_Id=Get_Input('POST','DB','SupportItem_Id','INT',1,4294967295,0,0);
				$CustomerSatisfactionLevel=Get_Input('POST','DB','CustomerSatisfactionLevel','ARRAY',array('VeryGood','Good','Fair','Bad','VeryBad'),0,0,0);
				$Comment=Get_Input('POST','DB','Comment','STR',0,2048,0,0);
				$sql= "insert Huser_supporthistory set CDT=Now()";
				$sql.=",User_Id='$User_Id'";
				$sql.=",Creator_Id=$LReseller_Id";
				$sql.=",SupportItem_Id='$SupportItem_Id'";
				$sql.=",CustomerSatisfactionLevel='$CustomerSatisfactionLevel'";
				$sql.=",Comment='$Comment'";
				$res = $conn->sql->query($sql);
				$RowId=$conn->sql->get_new_id();
				$NewRowInfo['User_SupportHistory_Id']=$RowId;
				$NewRowInfo['Item']=DBSelectAsString("select SupportItemTitle from Hsupportitem where SupportItem_Id='$SupportItem_Id'");
				$NewRowInfo['Comment']=$Comment;
				$NewRowInfo['CSLevel']=$CustomerSatisfactionLevel;
				logdbinsert($NewRowInfo,'Add','User',$User_Id,'SupportHistory');
				echo "OK~$RowId~";
        break;
	case "Delete":
				DSDebug(1,"DSUser_SupportHistory_ListRender Delete ******************************************");
				$User_SupportHistory_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id From Huser_supporthistory Where User_SupportHistory_Id='$User_SupportHistory_Id'");
				
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				if(ISPermit($Visp_Id,"Visp.User.SupportHistory.Delete")!==true){
					$Creator_Id=DBSelectAsString("select Creator_Id from Huser_supporthistory where User_SupportHistory_Id='$User_SupportHistory_Id'");
					DSDebug(2,"SupportHistory Creaotr=$Creator_Id and Reseller_Id=$LReseller_Id");
					if($Creator_Id!=$LReseller_Id)
						exit("LimitCreator~");
					$Diff=DBSelectAsString("select TIMESTAMPDIFF(SECOND,CDT,Now()) from Huser_supporthistory where User_SupportHistory_Id='$User_SupportHistory_Id'");
					DSDebug(2,"Difference between creation time and now=$Diff sec");
					if($Diff>600)
						exit("LimitTime~");
				}
				
				$tmp=array();
				$sql="select User_SupportHistory_Id,SupportItemTitle as Item,Comment,CustomerSatisfactionLevel as CSLevel from Huser_supporthistory h join Hsupportitem s on h.SupportItem_Id=s.SupportItem_Id where User_SupportHistory_Id='$User_SupportHistory_Id'";
				$n=CopyTableToArray($tmp,$sql);
				$n=DBDelete("delete from Huser_supporthistory Where User_SupportHistory_Id='$User_SupportHistory_Id'");
				logdbdelete($tmp[0],'Delete','User',$User_Id,'SupportHistory');
				echo "OK~";
		break;
	case "SelectSupportItem":
				DSDebug(1,"DSUser_SupportHistory_ListRender -> SelectSupportItem");	
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$options->render_sql("SELECT SupportItem_Id,SupportItemTitle FROM Hsupportitem where IsEnable='Yes' order by SupportItemTitle ASC","","SupportItem_Id,SupportItemTitle","","");
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>