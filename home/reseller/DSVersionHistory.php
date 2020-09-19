<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Version History</title>
</head>
<body style='Background-color:rgb(235,245,255);direction:rtl;'>
<?php
require_once("../../lib/DSInitialReseller.php");

if($LResellerName=='') 	exit("<script>alert('خطا، نشست منقضی شد, لطفا دوباره وارد شوید')</script>خطا، نشست منقضی شد, لطفا دوباره وارد شوید");
DSDebug(0,"DSVersionHistory ..................................................................................");
$n=DBSelectAsString("Select count(1) From  Hversionhistory Where InstallDT=0");
if($n==0){
	echo '<p> شما از آخرین نسخه استفاده می کنید</p>';
}
else{
	echo '<p> نسخه جدید موجود است</p>';
	echo '<p> لطفا از طریق ssh به سرور وارد شوید و برای بروزرسانی به نسخه جدید ds_update را اجرا نمایید. </p>';
}
	echo '<p> </p>';
	echo '<p> تاریخچه نسخه</br> </p>';
	$sql="SELECT Version,VersionType,shDatestr(PublishedDate) as PublishedDate,shDateTimestr(InstallDT) as InstallDT,ChangeLog From  Hversionhistory Order by VersionHistory_Id desc";
	$res = $conn->sql->query($sql);
	$data =  $conn->sql->get_next($res);
	if($data){
		echo '<table cellspacing="1" cellpadding="5" class="TBL" border="1">';
		echo '<tr class="mainTr">';
		echo '<td class="TblBg BorderLeft">Version</td>';
		// echo '<td class="TblBg BorderLeft">VersionType</td>';
		echo '<td class="TblBg BorderLeft">Published Date</td>';
		echo '<td class="TblBg BorderLeft">InstallDT</td>';
		echo '<td class="TblBg BorderLeft">ChangeLog</td>';
		echo '</tr>';


		while($data){
			echo '<tr>';
			echo '<td>'.$data['Version'].'</td>';
			// echo '<td>'.$data['VersionType'].'</td>';
			echo '<td>'.$data['PublishedDate'].'</td>';
			echo '<td>'.$data['InstallDT'].'</td>';
			echo '<td>'.$data['ChangeLog'].'</td>';
			echo '</tr>';
			$data =  $conn->sql->get_next($res);
		}
	}

?>
    </table>
    </div>

</body>
</html>
