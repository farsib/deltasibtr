<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Invoice_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list","SelectItem","GetInvoice","PrintInvoice"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Invoice_ListRender->List ********************************************");

				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Invoice.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				$SortStr='Order By User_Invoice_Id Desc';
				
				DSGridRender_Sql(100,
					"Select User_Invoice_Id,r.ResellerName As Creator,{$DT}datetimestr(InvoiceCDT) as InvoiceCDT,InvoiceStatus,Format(TotalPrice,0) as TotalPrice,Comment ".
					"From Huser_invoice ui Left join Hreseller r on ui.Creator_Id=r.Reseller_Id ".
					"Where (User_Id=$User_Id)".$sqlfilter." $SortStr ",
					"User_Invoice_Id",
					"User_Invoice_Id,Creator,InvoiceCDT,InvoiceStatus,TotalPrice,Comment",
					"","","");
       break;
	case "SelectItem":
				DSDebug(1,"DSUser_Invoice_ListRender-> SelectItem *****************");
				require_once('../../lib/connector/options_connector.php');
				$options = new SelectOptionsConnector($mysqli,"MySQLi");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Invoice.Add");
				if($Lreseller_Id==1)
					$WhereStr="where User_Id='$User_Id'";
				else
					$WhereStr="where User_Id='$User_Id' and Visibility<>'VeryHidden'";
				$sql="select distinct concat('base~',User_ServiceBase_Id,'~',ServicePrice,'~',PayPrice,'~',ServiceName) as Id,concat('[Base:',User_ServiceBase_Id,'] ',ServiceName,' (Price=',Format(PayPrice,$PriceFloatDigit),')') as ItemName from Huser_servicebase b join Hservice s on b.Service_Id=s.Service_Id $WhereStr union ".
					"select distinct concat('extracredit~',User_ServiceExtraCredit_Id,'~',ServicePrice,'~',PayPrice,'~',ServiceName) as Id,concat('[Extra:',User_ServiceExtraCredit_Id,'] ',ServiceName,' (Price=',Format(PayPrice,$PriceFloatDigit),')') as ItemName from Huser_serviceextracredit e join Hservice s on e.Service_Id=s.Service_Id $WhereStr union ".
					"select distinct concat('ip~',User_ServiceIP_Id,'~',ServicePrice,'~',PayPrice,'~',ServiceName) as Id,concat('[IP:',User_ServiceIP_Id,'] ',ServiceName,' (Price=',Format(PayPrice,$PriceFloatDigit),')') as ItemName from Huser_serviceip i join Hservice s on i.Service_Id=s.Service_Id $WhereStr union ".
					"select distinct concat('other~',User_ServiceOther_Id,'~',ServicePrice,'~',PayPrice,'~',ServiceName) as Id,concat('[Other:',User_ServiceOther_Id,'] ',ServiceName,' (Price=',Format(PayPrice,$PriceFloatDigit),')') as ItemName from Huser_serviceother o join Hservice s on o.Service_Id=s.Service_Id $WhereStr ";
				$options->render_sql($sql,"","Id,ItemName","","");
        break;	   

    case "GetInvoice":
				DSDebug(1,"DSUser_Invoice_ListRender-> GetInvoice *****************");
				$NewRowInfo=array();
				
				$NewRowInfo['User_Id']=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($NewRowInfo['User_Id'],"Visp.User.Invoice.Add");
				
				
				$NewRowInfo['CustomerName']=Get_Input('POST','DB','CustomerName','STR',0,64,0,0);
				$NewRowInfo['CustomerPhone']=Get_Input('POST','DB','CustomerPhone','STR',0,32,0,0);
				$NewRowInfo['CustomerPostalCode']=Get_Input('POST','DB','CustomerPostalCode','STR',0,10,0,0);
				$NewRowInfo['CustomerEconomyCode']=Get_Input('POST','DB','CustomerEconomyCode','STR',0,12,0,0);
				$NewRowInfo['CustomerRegistryCode']=Get_Input('POST','DB','CustomerRegistryCode','STR',0,12,0,0);
				$NewRowInfo['CustomerNationalCode']=Get_Input('POST','DB','CustomerNationalCode','STR',0,12,0,0);
				$NewRowInfo['CustomerAddress']=Get_Input('POST','DB','CustomerAddress','STR',0,255,0,0);
				$NewRowInfo['Comment']=Get_Input('POST','DB','Comment','STR',0,255,0,0);
				$OutStr=
				
				"<table border='1' dir='rtl' align='center' width='600px' style='font-family:\"B Roya\",Sans-serif,tahoma;text-align:right;font-weight:bold' cellspacing='0' cellpadding='4'>".
					"<caption style='font-family:\"B Titr\",Sans-serif,tahoma;'>پیش نمایش اطلاعات صورتحساب</caption>".
					"<tr>".
						"<td>نام خریدار : <strong>".$NewRowInfo['CustomerName']."</strong></td>".
						"<td>تلفن : <strong>".$NewRowInfo['CustomerPhone']."</strong></td>".
						"<td>کدپستی : <strong>".$NewRowInfo['CustomerPostalCode']."</strong></td>".
					"</tr>".
					"<tr>".
						"<td>شماره اقتصادی : <strong>".$NewRowInfo['CustomerEconomyCode']."</strong></td>".
						"<td>شماره ثبت : <strong>".$NewRowInfo['CustomerRegistryCode']."</strong></td>".
						"<td>شماره ملی : <strong>".$NewRowInfo['CustomerNationalCode']."</strong></td>".
					"</tr>".
					"<tr>".
						"<td colspan='3'>آدرس : ".$NewRowInfo['CustomerAddress']."</td>".
					"</tr>".
				"</table>";
				
				
				$RowCount=Get_Input('POST','DB','RowCount','INT',0,100,0,0);
				$Arr=Array();
				$RowNumber=0;
				$InvoiceArr=Array();
				$InvoiceArr[0]=Array("ItemName"=>"GrandTotal","Count"=>"","UnitPrice"=>0,"TotalPrice"=>0,"DiscountAmount"=>0,"AfterDiscount"=>0,"VATAmount"=>0,"AfterVAT"=>0,"SavingOffUsed"=>0);
				
				for($i=1;$i<=$RowCount;++$i){
					$Item_Type=Get_Input('POST','DB','Item_Type'.$i,'ARRAY',Array("none","base","extracredit","ip","other"),0,0,0);
					if($Item_Type!='none'){
						$Item_Id=Get_Input('POST','DB','Item_Id'.$i,'INT',1,4294967295,0,0);
						$RowNumber++;
						$KeyName="User_Service{$Item_Type}_Id";
						$TableName="Huser_service{$Item_Type}";
						$ItemName=Get_Input('POST','DB','ItemName'.$i,'STR',1,200,0,0);
						CopyTableToArray($Arr,"select ServiceName,ServicePrice,DirectOff,SavingOffUsed,VAT,PayPrice from $TableName t join Hservice s on t.Service_Id=s.Service_Id where User_Id='".$NewRowInfo['User_Id']."' and $KeyName='$Item_Id'");
						
						
						$DiscountAmount=$Arr[0]["ServicePrice"]*$Arr[0]["DirectOff"]/100;
						$AfterDiscount=$Arr[0]["ServicePrice"]-$DiscountAmount;
						$VATAmount=$AfterDiscount*$Arr[0]["VAT"]/100;
						$AfterVAT=$AfterDiscount+$VATAmount;
						
						$InvoiceArr[$RowNumber]=Array(
							"ItemName"=>$Arr[0]["ServiceName"],
							"Count"=>1,
							"UnitPrice"=>$Arr[0]["ServicePrice"],
							"TotalPrice"=>$Arr[0]["ServicePrice"],
							"DiscountAmount"=>$DiscountAmount,
							"AfterDiscount"=>$AfterDiscount,
							"VATAmount"=>$VATAmount,
							"AfterVAT"=>$AfterVAT,
							"SavingOffUsed"=>$Arr[0]["SavingOffUsed"]
						);
						
						$InvoiceArr[0]["TotalPrice"]+=$Arr[0]["ServicePrice"];
						$InvoiceArr[0]["DiscountAmount"]+=$DiscountAmount;
						$InvoiceArr[0]["AfterDiscount"]+=$AfterDiscount;
						$InvoiceArr[0]["VATAmount"]+=$VATAmount;
						$InvoiceArr[0]["AfterVAT"]+=$AfterVAT;
						$InvoiceArr[0]["SavingOffUsed"]+=$Arr[0]["SavingOffUsed"];
						
					}
				}
				$c=count($InvoiceArr);
				for($i=1;$i<$c;++$i){
					for($j=$i+1;$j<$c;++$j){
						$ItemName=$InvoiceArr[$i]["ItemName"];
						$UnitPrice=$InvoiceArr[$i]["UnitPrice"];
						$DiscountAmount=$InvoiceArr[$i]["DiscountAmount"];
						$VATAmount=$InvoiceArr[$i]["VATAmount"];
						if(
							($InvoiceArr[$j]["Count"]>0)&&
							($ItemName==$InvoiceArr[$j]["ItemName"])&&
							($UnitPrice==$InvoiceArr[$j]["UnitPrice"])&&
							($DiscountAmount==$InvoiceArr[$j]["DiscountAmount"])&&
							($VATAmount==$InvoiceArr[$j]["VATAmount"])
						){
							$InvoiceArr[$i]["Count"]+=1;
							$InvoiceArr[$i]["TotalPrice"]+=$InvoiceArr[$j]["TotalPrice"];
							$InvoiceArr[$i]["DiscountAmount"]+=$InvoiceArr[$j]["DiscountAmount"];
							$InvoiceArr[$i]["AfterDiscount"]+=$InvoiceArr[$j]["AfterDiscount"];
							$InvoiceArr[$i]["VATAmount"]+=$InvoiceArr[$j]["VATAmount"];
							$InvoiceArr[$i]["AfterVAT"]+=$InvoiceArr[$j]["AfterVAT"];
							$InvoiceArr[$i]["SavingOffUsed"]+=$InvoiceArr[$j]["SavingOffUsed"];
							$InvoiceArr[$j]["Count"]=0;
						}
					}
				}
				$OutStr.=
					"<table border='1' dir='rtl' align='center' width='600px' style='font-family:\"B Roya\",Sans-serif,tahoma;text-align:center;font-weight:bold' cellspacing='0' cellpadding='4'>".
						"<tr>".
								"<td>ردیف</td>".
								"<td>شرح کالا</td>".
								"<td>تعداد</td>".
								"<td>مبلغ واحد</td>".
								"<td>مبلغ کل</td>".
								"<td>مبلغ تخفیف</td>".
								"<td>مبلغ پس از تخفیف</td>".
								"<td>مالیات و عوارض ارزش افزوده</td>".
								"<td>خالص فاکتور</td>".
						"</tr>";
				$RowNumber=1;
				foreach($InvoiceArr as $Key=>$Value){
					if($Value["Count"]>0)
						$OutStr.=
							"<tr>".
								"<td>".($RowNumber++)."</td>".
								"<td>".$Value["ItemName"]."</td>".
								"<td>".$Value["Count"]."</td>".
								"<td>".number_format($Value["UnitPrice"])."</td>".
								"<td>".number_format($Value["TotalPrice"])."</td>".
								"<td>".number_format($Value["DiscountAmount"])."</td>".
								"<td>".number_format($Value["AfterDiscount"])."</td>".
								"<td>".number_format($Value["VATAmount"])."</td>".
								"<td>".number_format($Value["AfterVAT"])."</td>".
							"</tr>";
				}
				$OutStr.=
					"<tr>".
						"<td style='text-align:left' colspan='4'>جمع کل</td>".
						"<td>".number_format($InvoiceArr[0]["TotalPrice"])."</td>".
						"<td>".number_format($InvoiceArr[0]["DiscountAmount"])."</td>".
						"<td>".number_format($InvoiceArr[0]["AfterDiscount"])."</td>".
						"<td>".number_format($InvoiceArr[0]["VATAmount"])."</td>".
						"<td>".number_format($InvoiceArr[0]["AfterVAT"])."</td>".
					"</tr>";
				if($InvoiceArr[0]["SavingOffUsed"]>0){
					$OutStr.=
					"<tr>".
						"<td style='text-align:left' colspan='8'>تخفیف کلی</td>".
						"<td>".number_format($InvoiceArr[0]["SavingOffUsed"])."</td>".
					"</tr>".
					"<tr>".
						"<td style='text-align:left' colspan='8'>قابل پرداخت</td>".
						"<td colspan='8'>".number_format ($InvoiceArr[0]["AfterVAT"]-$InvoiceArr[0]["SavingOffUsed"])."</td>".
					"</tr>";
				}
				if($NewRowInfo['Comment']!='')
					$OutStr.=
						"<tr>".
							"<td style='text-align:right' colspan='9'>توضیحات : <strong>".$NewRowInfo['Comment']."</strong></td>".
						"</tr>";
				$OutStr.="</table>";
				if($RowNumber==0)
					ExitError("فاکتور خالی است");
				$req=Get_Input('GET','DB','req','ARRAY',array("Preview","Insert"),0,0,0);
				
				if($req=="Preview")
					echo $OutStr;
				else{
					$sql="insert into Huser_invoice set ".
						"User_Id='".$NewRowInfo['User_Id']."'".
						",Creator_Id='$LReseller_Id'".
						",InvoiceCDT=now()".
						",InvoiceStatus='InvoiceIssued'".
						",CustomerName='".$NewRowInfo['CustomerName']."'".
						",CustomerPhone='".$NewRowInfo['CustomerPhone']."'".
						",CustomerAddress='".$NewRowInfo['CustomerAddress']."'".
						",CustomerPostalCode='".$NewRowInfo['CustomerPostalCode']."'".
						",CustomerEconomyCode='".$NewRowInfo['CustomerEconomyCode']."'".
						",CustomerRegistryCode='".$NewRowInfo['CustomerRegistryCode']."'".
						",CustomerNationalCode='".$NewRowInfo['CustomerNationalCode']."'".
						",TotalSavingOffUsed='".$InvoiceArr[0]["SavingOffUsed"]."'".
						",TotalPrice='".($InvoiceArr[0]["AfterVAT"]-$InvoiceArr[0]["SavingOffUsed"])."'".
						",Comment='".$NewRowInfo['Comment']."'";
					
										
					
					$res = $conn->sql->query($sql);
					$RowId=$conn->sql->get_new_id();
					logdbinsert($NewRowInfo,'Add','User',$NewRowInfo['User_Id'],'Invoice');
					$c=count($InvoiceArr);
					for($i=1;$i<$c;++$i){
						if($InvoiceArr[$i]["Count"]>0){
							$sql="insert into Huser_invoice_items set ".
								"User_Invoice_Id='$RowId'".
								",ItemName='".$InvoiceArr[$i]["ItemName"]."'".
								",ItemCount='".$InvoiceArr[$i]["Count"]."'".
								",UnitPrice='".$InvoiceArr[$i]["UnitPrice"]."'".
								",VATAmount='".$InvoiceArr[$i]["VATAmount"]."'".
								",DiscountAmount='".$InvoiceArr[$i]["DiscountAmount"]."'";
							DBInsert($sql);
						}
					}
					echo "OK~$RowId~";
				}
       break;
	case "PrintInvoice":
				DSDebug(1,"DSUser_Invoice_ListRender-> PrintInvoice *****************");
					
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Invoice.Print");
				
				$User_Invoice_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$sql="select User_Invoice_Id,SHDATESTR(InvoiceCDT) as InvoiceCDT,CustomerName,CustomerPhone,CustomerAddress,CustomerPostalCode,TotalSavingOffUsed,TotalPrice,Comment,CustomerNationalCode,CustomerRegistryCode,CustomerEconomyCode,Param10 As SellerName,Param11 As SellerPhone,Param12 As SellerAddress,Param13 As SellerEconomyCode,Param14 As SellerNationalCode,Param15 As SellerPostalCode,Param16 As SellerRegistryCode from Huser_invoice,Hserver where User_Id='$User_Id' and User_Invoice_Id='$User_Invoice_Id' and Server_Id=1";
				
				$InvoiceInfo=Array();
				
				
				$n=CopyTableToArray($InvoiceInfo,$sql);
				if($n<>1)
					Exit("خطا!!!");
				
				$InvoiceBody=Array();
				$sql="select ItemName,ItemCount,UnitPrice,VATAmount,DiscountAmount from Huser_invoice_items where User_Invoice_Id='$User_Invoice_Id'";
				$n=CopyTableToArray($InvoiceBody,$sql);
				
				require_once("DSInvoice.php");					
				
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>