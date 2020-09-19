<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"

        "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>صورتحساب فروش</title>
	<style>
	.Factor_TBL{direction:rtl;text-align:center;font-family:"B Roya",tahoma;border:1px solid #000;border-radius:5px;overflow:hidden;line-height:20px}
	.LeftSide{text-align:left}
	.RightSide{text-align:right;padding-right:5px}
	.FontBold{font-weight:bold}
	.BGColor{background:#dedede}
	.BottomBorder{border-bottom:1px solid #000}
	.LeftBorder{border-left:1px solid #000}
	.InvoiceCaption{font-size:140%;line-height:20px;font-family:"B Titr",tahoma;}
	 @page { size: landscape; }
	</style>
	<script>
		window.onload=function(){
			window.print();
			window.onfocus=function(){window.close();}
			setTimeout(function () { window.close(); }, 100);
		}
	</script>
</head>

<body>
<table border="0" cellpadding="3" cellspacing="0" align="center" width="1024px" class="Factor_TBL" style="border:none">
			<caption class="InvoiceCaption">صورتحساب فروش کالا و خدمات</caption>
			<tr>
				<td colspan="9" style="text-align:left">شماره سریال : <?php echo "96".str_pad($InvoiceInfo[0]["User_Invoice_Id"],7,"0",STR_PAD_LEFT) ?></td>
			</tr>
			<tr>
				<td colspan="9" style="text-align:left">تاریخ : <?php echo $InvoiceInfo[0]["InvoiceCDT"]?></td>
			</tr>
</table>			
<table border="0" cellpadding="3" cellspacing="0" align="center" width="1024px" class="Factor_TBL">
			<tr>
				<td class="FontBold BGColor BottomBorder">مشخصات فروشنده</td>
			</tr>
			<td>
				<table  border="0" cellpadding="3" cellspacing="0" align="center" width="1024px" class="Factor_TBL" style="border:none">
					<tr>
						<td class="FontBold LeftSide">نام شخص حقیقی / حقوقی:</td>
						<td class="RightSide" colspan="4"><?php echo $InvoiceInfo[0]["SellerName"] ?></td>
						<td class="FontBold LeftSide">شماره اقتصادی :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["SellerEconomyCode"] ?></td>
						<td class="FontBold LeftSide">شماره ثبت / شماره ملی :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["SellerNationalCode"]."/".$InvoiceInfo[0]["SellerRegistryCode"] ?></td>
					</tr>	
					<tr>
						<td class="FontBold LeftSide">نشانی :</td>
						<td class="RightSide" colspan="4"><?php echo $InvoiceInfo[0]["SellerAddress"] ?></td>
						<td class="FontBold LeftSide">کد پستی :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["SellerPostalCode"] ?></td>
						<td class="FontBold LeftSide">تلفن :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["SellerPhone"] ?></td>
					</tr>
				</table>
			</td>
</table>
<table border="0" cellpadding="3" cellspacing="0" align="center" width="1024px" class="Factor_TBL" style="margin-top:6px">
			<tr>
				<td class="FontBold BGColor BottomBorder">مشخصات خریدار</td>
			</tr>
			<td>
				<table  border="0" cellpadding="3" cellspacing="0" align="center" width="1024px" class="Factor_TBL" style="border:none">
					<tr>
						<td class="FontBold LeftSide">نام شخص حقیقی / حقوقی:</td>
						<td class="RightSide" colspan="4"><?php echo $InvoiceInfo[0]["CustomerName"] ?></td>
						<td class="FontBold LeftSide">شماره اقتصادی :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["CustomerEconomyCode"] ?></td>
						<td class="FontBold LeftSide">شماره ثبت / شماره ملی :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["CustomerNationalCode"]."/".$InvoiceInfo[0]["CustomerRegistryCode"] ?></td>
					</tr>	
					<tr>
						<td class="FontBold LeftSide">نشانی :</td>
						<td class="RightSide" colspan="4"><?php echo $InvoiceInfo[0]["CustomerAddress"] ?></td>
						<td class="FontBold LeftSide">کد پستی :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["CustomerPostalCode"] ?></td>
						<td class="FontBold LeftSide">تلفن :</td>
						<td class="RightSide"><?php echo $InvoiceInfo[0]["CustomerPhone"] ?></td>
					</tr>
				</table>
			</td>
</table>
<table border="0" cellpadding="3" cellspacing="0" align="center" width="1032px" class="Factor_TBL" style="margin-top:6px">
	<tr>
		<td colspan="9" class="FontBold BGColor BottomBorder">مشخصات کالا یا خدمات مورد معامله</td>
	</tr>	
	<tr>
		<td class="FontBold BottomBorder LeftBorder" style="width:4%;">ردیف</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:28%">شرح کالا / خدمت</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:10%">تعداد / مقدار</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:10%">مبلغ واحد</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:10%">مبلغ کل</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:8%">مبلغ تخفیف</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:10%">مبلغ کل پس از تخفیف</td>
		<td class="FontBold BottomBorder LeftBorder" style="width:8%">مالیات و عوارض ارزش افزوده</td>
		<td class="FontBold BottomBorder" style="width:12%">خالص فاکتور</td>
	</tr>
	<?php 
	$GrandTotalPrice=0;
	$GrandDiscountAmount=0;
	$GrandAfterDiscount=0;
	$GrandVATAmount=0;
	$GrandAfterVAT=0;
	for($i=0;$i<count($InvoiceBody);$i++){
		
		$TotalPrice=($InvoiceBody[$i]["ItemCount"]*$InvoiceBody[$i]["UnitPrice"]);
		$AfterDiscount=($TotalPrice-$InvoiceBody[$i]["DiscountAmount"]);
		$AfterVAT=$AfterDiscount+$InvoiceBody[$i]["VATAmount"];
		
		
		$GrandTotalPrice+=$TotalPrice;
		$GrandDiscountAmount+=$InvoiceBody[$i]["DiscountAmount"];
		$GrandAfterDiscount+=$AfterDiscount;
		$GrandVATAmount+=$InvoiceBody[$i]["VATAmount"];
		$GrandAfterVAT+=$AfterVAT;

	?>
		<tr>
			<td class="BottomBorder LeftBorder"><?php echo $i+1 ?></td>
			<td class="BottomBorder LeftBorder"><?php echo $InvoiceBody[$i]["ItemName"] ?></td>
			<td class="BottomBorder LeftBorder"><?php echo $InvoiceBody[$i]["ItemCount"] ?></td>
			<td class="BottomBorder LeftBorder"><?php echo number_format($InvoiceBody[$i]["UnitPrice"]) ?></td>
			<td class="BottomBorder LeftBorder"><?php echo number_format($TotalPrice); ?></td>
			<td class="BottomBorder LeftBorder"><?php echo number_format($InvoiceBody[$i]["DiscountAmount"]) ?></td>
			<td class="BottomBorder LeftBorder"><?php echo number_format($AfterDiscount) ?></td>
			<td class="BottomBorder LeftBorder"><?php echo number_format($InvoiceBody[$i]["VATAmount"]) ?></td>
			<td class="BottomBorder"><?php echo number_format($AfterVAT) ?></td>
		</tr>
	<?php } ?>
	<tr>
		<td colspan="4" class="LeftSide FontBold BottomBorder LeftBorder">جمع کل</td>
		<td  class="BottomBorder LeftBorder"><?php echo number_format($GrandTotalPrice) ?></td>
		<td  class="BottomBorder LeftBorder"><?php echo number_format($GrandDiscountAmount) ?></td>
		<td  class="BottomBorder LeftBorder"><?php echo number_format($GrandAfterDiscount) ?></td>
		<td  class="BottomBorder LeftBorder"><?php echo number_format($GrandVATAmount) ?></td>
		<td  class="BottomBorder"><?php echo number_format($GrandAfterVAT) ?></td>
	</tr>
	<?php
	if($InvoiceInfo[0]["TotalSavingOffUsed"]>0){
	?>
	<tr>
		<td colspan="8" class="LeftSide FontBold BottomBorder LeftBorder">تخفیف کلی</td>
		<td class="BottomBorder"><?php echo number_format($InvoiceInfo[0]["TotalSavingOffUsed"]) ?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="8" class="FontBold BottomBorder LeftBorder">مبلغ خالص فاکتور : <?php echo GetNumberString($InvoiceInfo[0]["TotalPrice"]) ?> ریال</td>
		<td class="BottomBorder"><?php echo number_format($InvoiceInfo[0]["TotalPrice"]) ?></td>
	</tr>
	<?php if($InvoiceInfo[0]["Comment"]!=""){?>
		<tr>
			<td class="BottomBorder" style="text-align:right" colspan="9">توضیحات : <strong><?php echo $InvoiceInfo[0]["Comment"] ?></strong></td>
		</tr>
	<?php } ?>
	
	<tr>
		<td style="padding:20px 0" colspan="4">مهر و امضای فروشنده</td>
		<td colspan="5" >مهر و امضای خریدار</td>
	</tr>

</table>

</body>

</html>
<?php
function GetThreeDigitNumberString($a){
	$yekan = array("صفر","يک", "دو", "سه","چهار", "پنج","شش", "هفت","هشت", "نه");
	$dahtabist = array("يازده","دوازده", "سيزده", "چهارده","پانزده", "شانزده","هفده","هجده","نوزده");
	$dahgan=array("ده","بيست","سي","چهل","پنجاه","شصت","هفتاد","هشتاد","نود");
	$sadgan=array("يکصد","دويست","سيصد","چهارصد","پانصد","ششصد","هفتصد","هشتصد","نهصد");
	if($a<10){
		return $yekan[$a];
	}
	elseif($a<20 && $a>10)
		return $dahtabist[$a-11];
	elseif($a<100 && $a>=20 || $a==10){
		$b=intval($a/10);
		$d=$b-1;
		$c=($a-($b*10));
		if($a % 10 == 0){
			return $dahgan[$d];
		}
		else
			return $dahgan[$d]." و ".$yekan[$c];
	}
	elseif ($a<1000 && $a>=100){
		$e=intval($a/100);
		$g=($a-($e*100));
		$h=intval($g/10);
		$k=$h-1;
		$f=$e-1;
		$j=$g-($h*10);
		if($a % 100 == 0){
			return $sadgan[$f];
		}
		else{
			if($j === 0)
				return $sadgan[$f]." و ".$dahgan[$k];
			else{
				if($k<0)				
					return $sadgan[$f]." و ".$yekan[$j];
				if($k==0)				
					return $sadgan[$f]." و ".$dahtabist[$j-1];
				else
					return $sadgan[$f]." و ".$dahgan[$k]." و ".$yekan[$j];
			}
		}
	}
	else
		return "";
}
function GetNumberString($a){
	$OutArr=Array();
	$a1=intval(fmod($a,1000));
	$a=intval($a/1000);
	$a2=intval(fmod($a,1000));
	$a=intval($a/1000);
	$a3=intval(fmod($a,1000));
	$a=intval($a/1000);
	$a4=intval(fmod($a,1000));
	$a=intval($a/1000);
	if($a>0)
		return "N/A";
	if($a4>0)
		array_push($OutArr,GetThreeDigitNumberString($a4)." میلیارد");
	if($a3>0)
		array_push($OutArr,GetThreeDigitNumberString($a3)." میلیون");
	if($a2>0)
		array_push($OutArr,GetThreeDigitNumberString($a2)." هزار");
	if($a1>0)
		array_push($OutArr,GetThreeDigitNumberString($a1));
	return implode(" و ",$OutArr);
	
}
?>