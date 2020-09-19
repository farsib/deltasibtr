<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Attachment_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_InputIgnore('GET','DB','act','ARRAY',array("list", "AddAttachment","LoadDeleteAttachmentForm","DeleteAttachment","LoadEditAttachmentForm","EditAttachment","ViewAttachment"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Attachment_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Attachment.List");			
				
				function color_rows($row){
					if($row->get_value("Comment")=="Upload failed"){
						$RowFormat="color:firebrick";
						$row->set_cell_style("Size","font-weight:Bold;color:firebrick");
					}
					elseif($row->get_value("Creator")=="User's WebSite")
						$RowFormat="color:#3333CC";
					else
						$RowFormat="color:black";
					
					$row->set_row_style($RowFormat);
				}
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(100,"SELECT  User_Attachment_Id,RealFilename,Comment,ByteToR(Size) as Size,ResellerName As Creator,{$DT}DateTimeStr(User_AttachmentCDT) as User_AttachmentCDT,tmpfilename ".
					"From Huser_attachment u_p left join Hreseller r on u_p.Creator_Id=r.Reseller_Id ".
					"where (User_Id=$User_Id) ".$sqlfilter." $SortStr ",
					"User_Attachment_Id",
					"User_Attachment_Id,RealFilename,Comment,Size,Creator,User_AttachmentCDT,tmpfilename",
					"","","color_rows");
       break;
    case "LoadEditAttachmentForm":
				DSDebug(1,"DSUser_Attachment_ListRender LoadEditAttachmentForm ********************************************");
				$User_Attachment_Id=Get_Input('GET','DB','User_Attachment_Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				$sql="SELECT User_Attachment_Id,Comment From  Huser_attachment where User_Attachment_Id='$User_Attachment_Id'";
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
    case "EditAttachment":
				DSDebug(1,"DSUser_Attachment_ListRender EditAttachment ********************************************");
				$User_Attachment_Id=Get_Input('GET','DB','User_Attachment_Id','INT',1,4294967295,0,0);
				if(DBSelectAsString("Select Comment from Huser_attachment Where User_Attachment_Id=$User_Attachment_Id")=="Upload failed")
					ExitError("توضیح این فایل را نمی توان تغییر داد");
				$User_Id=DBSelectAsString("Select User_Id From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				exitifnotpermituser($User_Id,"Visp.User.Attachment.Edit");			
				$Comment=Get_Input('POST','DB','Comment','STR',0,64,0,0);
				if($Comment=="Upload failed")
					ExitError("نمیتوان برای 'بارگذاری ناموفق' توضیح نوشت");
				DBDelete("Update Huser_attachment Set Comment='$Comment' Where User_Attachment_Id=$User_Attachment_Id");
				logdb("Edit","User",$User_Id,"Attachment",'Comment='.$Comment);
				echo "OK~";
       break;
    case "LoadDeleteAttachmentForm":
				DSDebug(1,"DSUser_Attachment_ListRender LoadDeleteAttchmentForm ********************************************");
				$User_Attachment_Id=Get_Input('GET','DB','User_Attachment_Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				$sql="SELECT Comment,RealFileName as FileName From  Huser_attachment where User_Attachment_Id='$User_Attachment_Id'";
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
    case "DeleteAttachment":
				DSDebug(1,"DSUser_Attachment_ListRender DeleteAttachment ********************************************");
				$User_Attachment_Id=Get_Input('GET','DB','User_Attachment_Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				exitifnotpermituser($User_Id,"Visp.User.Attachment.Delete");			
				$Comment=DBSelectAsString("Select Concat('Delete FileName=',RealFileName,' Size=',Size) From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				$ServerFileName=DBSelectAsString("Select ServerFileName From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				DBDelete("Delete From Huser_attachment Where User_Attachment_Id=$User_Attachment_Id");
				unlink('/payamavaran/www/deltasib/attachment/'.$User_Attachment_Id.'-'.$ServerFileName);
				logdb("Edit","User",$User_Id,"Attachment",$Comment);
				echo "OK~";
       break;	
    case "AddAttachment":
				/*
					HTML5/FLASH MODE
					(MODE will detected on client side automaticaly. Working mode will passed to Hserver as GET Hparam "mode")
					response format
					if upload was good, you need to specify state=true and name - will passed in form.send() as HserverName param
					{state: 'true', name: 'filename'}
				*/		
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Attachment.Add");
				
				$randfilename=GenerateRandomString(10);
				if (@$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
					if(isset($_FILES["file"])){
						$filename =mysqli_real_escape_string($mysqli,$_FILES["file"]["name"]); //$_FILES["file"]["name"];
						$RealFilename=mysqli_real_escape_string($mysqli,$_FILES["file"]["name"]);//$_FILES["file"]["name"];
						$Size=mysqli_real_escape_string($mysqli,$_FILES["file"]["size"]);//$_FILES["file"]["size"];

						$tmpfilename=GenerateRandomString(10);
						$User_Attachment_Id=DBInsert("Insert Huser_attachment Set Creator_Id=$LReseller_Id,User_Id=$User_Id,User_AttachmentCDT=Now(),RealFilename='$RealFilename',Size=$Size,tmpfilename='$tmpfilename'");
						$ServerFileName='__dsfile__'.$User_Id.'_'.$User_Attachment_Id.'_'.$tmpfilename;
						DSDebug(1,' check file_exists /payamavaran/www/deltasib/attachment/'.$ServerFileName);
						if(move_uploaded_file($_FILES["file"]["tmp_name"],"/tmp/".$ServerFileName)){
							$reply=runshellcommand("php","DSUploadFile","","");
							// $reply=file_get_contents('http://127.0.0.1:99/upload');
							print_r("{state: true, name:'".str_replace("'","\\'",$ServerFileName)."', extra:dhtmlx.message('فایل شما با موفقیت بارگذاری شد')}");
						}
						else{
							DBUpdate("update Huser_attachment set Comment='Upload failed' where User_Attachment_Id='$User_Attachment_Id'");
							print_r("{state: false, extra:alert('Upload failed.\\nCheck file size limit(".min(ini_get("upload_max_filesize"),ini_get("post_max_size")).").')}");
						}
					}
					else print_r("{state: false, extra:alert('Upload failed. Bad request.')}");
					DSDebug(1,"Request File Upload :\n".DSPrintArray($_FILES));
					
				}
				/*
					HTML4 MODE
					response format:
					to cancel uploading
					{state: 'cancelled'}
					if upload was good, you need to specify state=true, name - will passed in form.send() as HserverName param, size - filesize to update in list
					{state: 'true', name: 'filename', size: 1234}
				*/
				if (@$_REQUEST["mode"] == "html4") {
					if (@$_REQUEST["action"] == "cancel") {
						print_r("{state:'cancelled'}");
					} else {
						$filename = $_FILES["file"]["name"];
						// move_uploaded_file($_FILES["file"]["tmp_name"], "uploaded/".$filename);
						print_r("{state: true, name:'".str_replace("'","\\'",$filename)."', size:".$_FILES["file"]["size"]/*filesize("uploaded/".$filename)*/."}");
					}
				}
				/* 
					SILVERLIGHT MODE 
				{state: true, name: 'filename', size: 1234}
				*/

				if (@$_REQUEST["mode"] == "sl" && isset($_REQUEST["fileSize"]) && isset($_REQUEST["fileName"]) && isset($_REQUEST["fileKey"])) {
					// available params
					// $_REQUEST["fileName"], $_REQUEST["fileSize"], $_REQUEST["fileKey"] are available here
					// each file got temporary 12-chang length key due some inner silverlight limitations,
					// there will another request to check if file transferred and saved corrrectly
					// key matched to regex below
	
					preg_match("/^[a-z0-9]{12}$/", $_REQUEST["fileKey"], $p);
					if (@$p[0] === $_REQUEST["fileKey"]) {
						// generate temp name for saving
						$temp_name = "uploaded/".$p[0]."_data";
						// if action=="getUploadStatus" - that means file transfer was done and silverlight is wondering if php/orhet_server_side
						// got and saved file correctly or not, filekey same for both requests
						if (@$_REQUEST["action"] != "getUploadStatus") {
							// file is coming, save under temp name
							/*
							$postData = file_get_contents("php://input");
							if (strlen($postData) == $_REQUEST["fileSize"]) {
								file_put_contents($temp_name, $postData);
							}
							*/
							// no needs to output something
						} else {
							// second "check" request is coming
							/*
							$state = "false";
							if (file_exists($temp_name)) {
								rename($temp_name, "uploaded/".$_REQUEST["fileName"]);
								$state = "true";
							}
							*/
							$state = "true"; // just for tests
							// print upload state
							// state: true/false (w/o any quotas)
							// name: Hserver name/id
							print_r('{state: '.$state.', name: "'.str_replace('"','\\"',$_REQUEST["fileName"]).'"}');
						}
					}
				}
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