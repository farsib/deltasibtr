<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSReseller_UserAttachment_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"CRM.UserAttachment.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list","ViewAttachment"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSReseller_UserAttachment_ListRender->List ********************************************");
				
				function color_rows($row){
					if($row->get_value("Size")==0){
						$row->set_cell_style("RealFilename","font-weight:Bold;background-color:lightblue;color:firebrick");
						$row->set_cell_style("Size","font-weight:Bold;color:firebrick");
						$row->set_row_style("color:firebrick");
					}
					else
						$row->set_cell_style("RealFilename","font-weight:Bold;background-color:lightblue");
				}
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				else $SortStr="Order by User_Attachment_Id Desc";
				
				$SelectStr="Hua.User_Attachment_Id,if(Hr.ResellerName is Null,'User\'s WebSite',Hr.ResellerName) As Creator,SHDateTimeStr(User_AttachmentCDT) as User_AttachmentCDT,Hu.UserName,Hua.RealFilename,Hua.Size,Hua.Comment,tmpfilename,Hu.Visp_Id as Visp_Id,Hua.Creator_Id as Creator_Id";
							
				$sql="CREATE or REPLACE VIEW UserAttachmentTemp as select $SelectStr from Huser_attachment Hua ".
					"join Huser Hu on Hua.User_Id=Hu.User_id ".
					"left join Hreseller Hr on Hua.Creator_Id=Hr.Reseller_Id ";
				DBUpdate($sql);
				
				$SelectStr="User_Attachment_Id,Creator,User_AttachmentCDT,UserName,RealFilename,ByteToR(Size) as Size,Comment,tmpfilename";
				$ColumnStr="User_Attachment_Id,Creator,User_AttachmentCDT,UserName,RealFilename,Size,Comment,tmpfilename";
				
				$sql="select $SelectStr from UserAttachmentTemp UAT ".
					"where UAT.Creator_Id=$LReseller_Id ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"User_Attachment_Id",$ColumnStr,"","","color_rows");
       break;
    case "ViewAttachment":
				DSDebug(1,"DSUser_Attachment_ListRender ViewAttachment ********************************************");
				$User_Attachment_Id=Get_Input('GET','DB','User_Attachment_Id','INT',1,4294967295,0,0);
				$tmpfilename=Get_Input('GET','DB','tmpfilename','STR',10,10,0,0);
				
				CopyTableToArray($dataarray,"Select User_Attachment_Id,User_Id,RealFilename From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				$User_Id=$dataarray[0]["User_Id"];
				If($User_Id<=0){
					echo "<html><head><script type=\"text/javascript\">";
					echo "window.onload = function(){alert('Not permit');window.close();}";
					echo "</script></head><body></body></html>";
					exit();
				}	
				//$User_Attachment_Id=[0]["User_Attachment_Id"];
				//$User_Id=DBSelectAsString("Select User_Id From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				$Visp_Id=DBSelectAsString("Select Visp_Id from Huser where User_Id=$User_Id");
				If($Visp_Id<=0){
					echo "<html><head><script type=\"text/javascript\">";
					echo "window.onload = function(){alert('Not permit');window.close();}";
					echo "</script></head><body></body></html>";
					exit();
				}	
				$p=ISPermit($Visp_Id,"Visp.User.Attachment.View");
				if($p!=true){
					echo "<html><head><script type=\"text/javascript\">";
					echo "window.onload = function(){alert('Not permit');window.close();}";
					echo "</script></head><body></body></html>";
					exit();
				}
				
				
				$RealFilename=$dataarray[0]["RealFilename"];
				$file='/payamavaran/www/deltasib/attachment/'.'__dsfile__'.$User_Id.'_'.$User_Attachment_Id.'_'.$tmpfilename;
				
				DSDebug(1,"RealFilename=$RealFilename file=$file");
				if (file_exists($file)) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($RealFilename));
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					ob_clean();
					flush();
					readfile($file);
					exit;
				}
				else {
					echo "<html><head><script type=\"text/javascript\">";
					echo "window.onload = function(){alert('file not exist');window.close();}";
					echo "</script></head><body></body></html>";
					exit;
				}
				exit;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
