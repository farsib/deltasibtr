<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Installment_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list",'CancelInstallment','ApplyInstallment'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Installment_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Installment.List");

				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				function color_rows($row){
					$Status=$row->get_value("Status");
					if($Status=='Applied')
						$row->set_row_style("color:green");
					elseif($Status=='Cancel')
						$row->set_row_style("color:red");
					else
						$row->set_row_style("color:black");
				}
				
				DSGridRender_Sql(100,
					"SELECT User_Installment_Id,InstallmentNo,Format(Price,$PriceFloatDigit) as Price,{$DT}DateStr(ScheduleDate) As ScheduleDate,Status,Comment,ResellerName,{$DT}DateTimeStr(User_InstallmentCDT) As CDT,"."User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,Format(SavingOffAmount,$PriceFloatDigit) as SavingOffAmount,SavingOffExpirationDays from ".
					" Huser_installment u_i left join Hreseller r on u_i.Reseller_Id=r.Reseller_Id ".
					"Where (User_Id=$User_Id)" .$sqlfilter." $SortStr ",
					"User_Installment_Id",
					"User_Installment_Id,InstallmentNo,Price,ScheduleDate,Status,Comment,ResellerName,CDT,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,SavingOffAmount,SavingOffExpirationDays",
					"","","color_rows");
       break;
	case "CancelInstallment":
				DSDebug(1,"DSUser_Installment_ListRender CancelInstallment ******************************************");
				$User_Installment_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_installment where User_Installment_Id=$User_Installment_Id");
				exitifnotpermituser($User_Id,"Visp.User.Installment.Cancel");
				$Status=DBSelectAsString("Select Status from Huser_installment where User_Installment_Id=$User_Installment_Id");
				if($Status!='Pending') 
					ExitError('فقط قسط های در حال انتظار را میتوان لغو کرد');
				$ar=DBUpdate("Update Huser_installment Set Status='Cancel' Where User_Installment_Id=$User_Installment_Id");
				logdb('Cancel','User',$User_Id,'Installment',"Installment Id=$User_Installment_Id Canceled");
				echo "OK~";
		break;
	case "ApplyInstallment":
				DSDebug(1,"DSUser_Installment_ListRender ApplyInstallment ******************************************");
				$User_Installment_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_installment where User_Installment_Id=$User_Installment_Id");
				exitifnotpermituser($User_Id,"Visp.User.Installment.Apply");
				$Status=DBSelectAsString("Select Status from Huser_installment where User_Installment_Id=$User_Installment_Id");
				if($Status!='Pending') 
					ExitError('Only Pending Installment can Apply!!!');
				
				// $n=	DBSelectAsString("Select Count(*) from Huser_installment where User_Id=$User_Id and Status='Pending' And User_Installment_Id<$User_Installment_Id");
				// if($n>0)
					// ExitError('Previous installment have not paid yet');
				
				$InstallmentArray=Array();
				$n=CopyTableToArray($InstallmentArray,"Select Price,SavingOffAmount,SavingOffExpirationDays from Huser_installment where User_Installment_Id=$User_Installment_Id");
				$Price=$InstallmentArray[0]["Price"];
				$SavingOffAmount=$InstallmentArray[0]["SavingOffAmount"];
				$SavingOffExpirationDays=$InstallmentArray[0]["SavingOffExpirationDays"];
				
				AddPaymentToUser($LReseller_Id,$User_Id,'ApplyInstallment',$Price*(-1),'','Now()','','','');
				
				if($SavingOffAmount>0)
					DBInsert("insert Huser_savingoff set User_Id='$User_Id',SavingOffStatus='Pending',SavingOffAmount='$SavingOffAmount',UsedAmount=0,SavingOffCDT=Now(),SavingOffExpDT=DATE_ADD(NOW(),INTERVAL $SavingOffExpirationDays DAY)");
				
				$ar=DBUpdate("Update Huser_installment Set Status='Applied' Where User_Installment_Id=$User_Installment_Id");
				AddResellerTransaction($LReseller_Id,0,$User_Id,'ApplyInstallment',0);
			
				logdb('Update','User',$User_Id,'Installment',"Installment Id=$User_Installment_Id Applied");
				echo "OK~";
		break;
		
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>