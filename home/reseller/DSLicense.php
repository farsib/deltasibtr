<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Version History</title>
 <style type="text/css">
 .GeneralStyle{
	font-family:Calibri,arial,tahoma;
	font-weight:bold;
	cursor:pointer;
 }
 </style>
 <script>
 var IsLoading=false;
 function DoOnClick(){
	if(IsLoading)
		return;
	IsLoading=true;
	document.getElementById("MainContent").innerHTML="در حال بارگذاری...";
	window.location.reload();
}
 </script>
</head>
<body style='Background-color:rgb(235,245,255);direction:rtl;'>
<p> اطلاعات قفل </p>
<?php
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSLicence ..................................................................................");

//$lockinfo=DBSelectAsString("select LockString from Hlockinfo order by LockInfo_Id desc limit 1");
$reply=runshellcommand("php","DSLockInfo","","");
if(!strpos($reply,"SupportDateStr")){
	echo '<p>شما از دلتاسیب رایگان که محدود به حداکثر ۳ کاربر آنلاین است، استفاده می کنید</p>';
	exit;
}
//Result=`` Type=`SoftKey` SupportDateStr=`1398/04/17` 474 DemoDateStr=`1397/04/27` 119 ISExpire=0 Version=`` HID=`` Mem=`` Serial=`1000`
// MaxOnlineUser=`10000` ISURL=`Yes` ISAD=`Yes` ISTelegram=`Yes` ISGMikrotik=`Yes` ISGCisco=`No` NetLogMaxClient=`10000`
    echo '<table cellspacing="1" cellpadding="1" border="1" class="TBL">';
	echo '<tr><td width="120px">LockType</td><td width="320px">'.$Info[0].'</td></tr>';
	echo '<tr><td>Type</td><td>'.ExtractWord($reply,"Type").'</td></tr>';
	echo '<tr><td>SupportDate</td><td>'.ExtractWord($reply,"SupportDateStr").'</td></tr>';
	echo '<tr><td>DemoDate</td><td>'.ExtractWord($reply,"DemoDateStr").'</td></tr>';
	echo '<tr><td>Serial</td><td>'.ExtractWord($reply,"Serial").'</td></tr>';
	echo '<tr><td>MaxOnlineUser</td><td>'.ExtractWord($reply,"MaxOnlineUser").'</td></tr>';
	echo '<tr><td>URL</td><td>'.ExtractWord($reply,"ISURL").'</td></tr>';
	echo '<tr><td>Active Directory</td><td>'.ExtractWord($reply,"ISAD").'</td></tr>';
	echo '<tr><td>Telegram</td><td>'.ExtractWord($reply,"ISTelegram").'</td></tr>';
	echo '<tr><td>ISG</td><td>'.ExtractWord($reply,"ISGMikrotik").'</td></tr>';
	echo '<tr><td>NetLogMaxClient</td><td>'.ExtractWord($reply,"NetLogMaxClient").'</td></tr>';
	echo '</table>';




Function ExtractWord($info,$feild){
	$i=strpos($info,$feild);
    if ($i == 0) return '';
    $i += strlen($feild)+2;
	$ret="";
	while(($ini<strlen($info))&&(substr( $info, $i, 1 )!='`')) {
		$ret=$ret.substr( $info, $i, 1 );
		$i++;
	}
	return $ret;
}

exit;
//Type=`SoftKey` SupportDateStr=`1398/04/17` 474 DemoDateStr=`1397/04/27` 119 ISExpire=0 Version=`` HID=`` Mem=`` Serial=`1000` MaxOnlineUser=`10000` ISURL=`Yes` ISAD=`Yes` ISTelegram=`Yes` ISGMikrotik=`Yes` ISGCisco=`No` NetLogMaxClient=`10000`
$out=preg_replace( "/\r|\n/", "", $lockinfo);
$Info = explode("`", $out);
echo '<div id="MainContent" class="GeneralStyle" title="برای بروزکردن کلیک کنید " onClick="DoOnClick()">';
if($lockinfo===false)
	echo '<p>قادر به خواندن اطلاعات قفل نیست... سرویس های موردنیاز را بررسی کنید...</p>';
elseif(($lockinfo=="SOFTKEY`````````````")||(count($Info)<11)){
	echo '<p>شما از دلتاسیب رایگان که محدود به حداکثر ۳ کاربر آنلاین است، استفاده می کنید</p>';
}
else if($Info[1]!='DS'){
	echo '<p>قفل ارائه شد ولی قفل دیتاسیب نیست</p>';
}
else{
    echo '<table cellspacing="1" cellpadding="1" border="1" class="TBL">';
	echo '<tr><td width="120px">نوع قفل</td><td width="320px">'.$Info[0].'</td></tr>';
	echo '<tr><td>سریال</td><td>'.$Info[2].'</td></tr>';
	echo '<tr><td>حداکثر کاربر آنلاین</td><td>'.$Info[3].'</td></tr>';
	echo '<tr><td>دایرکتوری فعال</td><td>'.$Info[4].'</td></tr>';
	echo '<tr><td>URL گزارش</td><td>'.$Info[5].'</td></tr>';
	echo '<tr><td>تاریخ انتضاء</td><td>'.$Info[6].'</td></tr>';
	echo '<tr><td>تاریخ پشتیبانی</td><td>'.$Info[7].'</td></tr>';
	echo '<tr><td>پارامتر1</td><td>'.$Info[8].'</td></tr>';
	echo '<tr><td>پارامتر2</td><td>'.$Info[9].'</td></tr>';
	echo '<tr><td>پارامتر3</td><td>'.$Info[10].'</td></tr>';
	echo '</table>';
}
echo "</div>";
/*



function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    echo "The number is: $x <br>";
    $x++;
}
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);




$str = "String to loop through"
$strlen = strlen( $str );
for( $i = 0; $i <= $strlen; $i++ ) {
    $char = substr( $str, $i, 1 );
    // $char contains the current character, so do your processing here
}

	*/



?>
</body>
</html>
