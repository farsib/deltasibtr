<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSGift_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

$act=Get_Input('GET','DB','act','ARRAY',array("list", "Delete"),0,0,0);


switch ($act) {
    case "list":
				DSDebug(0,"DSGift_ListRender->List ********************************************");
				exitifnotpermit(0,"Admin.User.Gift.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				DSGridRender_Sql(-1,
					"SELECT g.Gift_Id,GiftName,GiftISEnable,GiftMode,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,ByteToR(GiftExtraTr) As GiftExtraTr,GiftStopOnTrFinish,SecondToR(GiftExtraTi) as GiftExtraTi,MikrotikRateName,(select count(1) from Hservice_gift where Gift_Id=g.Gift_Id) as ServiceCount,".
					"(select count(1) from Hservice where AttachedGift_Id=g.Gift_Id) as AttachedGiftCount,".
					"(select count(1) from Huser_gift where Gift_Id=g.Gift_Id) as UserCount ".
					"From Hgift g left join Hmikrotikrate m on g.GiftMikrotikRate_Id=m.MikrotikRate_Id Where (GiftIsDel='No') ".$sqlfilter." group by g.Gift_Id $SortStr",
					"Gift_Id",
					"Gift_Id,GiftName,GiftISEnable,GiftMode,GiftDurationDays,GiftExpirationDays,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftStopOnTrFinish,GiftExtraTiGift,MikrotikRateName,ServiceCount,AttachedGiftCount,UserCount",
					"","","");
       break;
	case "Delete":
				DSDebug(1,"DSGift_ListRender Delete ******************************************");
				exitifnotpermit(0,"Admin.User.Gift.Delete");
				$NewRowInfo=array();
				$NewRowInfo['Gift_Id']=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				
				$Service_Id=DBSelectAsString("Select Service_Id from Hservice Where AttachedGift_Id=".$NewRowInfo['Gift_Id']." Limit 1");
				if($Service_Id>0){
					$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$Service_Id");
					ExitError("این هدیه به سرویس زیر پیوست شده است و قابل حذف نیست</br>'$ServiceName'");
				}
				
				$Service_Id=DBSelectAsString("Select Service_Id from Hservice_gift Where Gift_Id=".$NewRowInfo['Gift_Id']." Limit 1");
				if($Service_Id>0){
					$ServiceName=DBSelectAsString("Select ServiceName from Hservice Where Service_Id=$Service_Id");
					ExitError("این هدیه توسط سرویس زیر استفاده می شود و قابل حذف نیست</br>'$ServiceName'");
				}

				$User_Id=DBSelectAsString("Select User_Id from Huser_gift Where (Gift_Id='".$NewRowInfo['Gift_Id']."') and (GiftStatus='Pending') Limit 1");
				if($User_Id>0){
					$Username=DBSelectAsString("Select Username from Huser Where User_Id='$User_Id'");
					ExitError("این هدیه برای کاربر زیر در حالت انتظار قرار دارد و قابل حذف نیست</br>'$Username'");
				}
				
				$GiftCount=0;
				$GiftCount+=DBSelectAsString("Select Count(1) from Hservice_gift Where Gift_Id=".$NewRowInfo['Gift_Id']);
				$GiftCount+=DBSelectAsString("Select Count(1) from Huser_gift Where Gift_Id=".$NewRowInfo['Gift_Id']);			
				
				If($UserCount>0)
					$ar=DBDelete("Update Hgift Set GiftName=left(Concat('Del".rand(100,999)."-',GiftName),64),GiftIsDel='Yes',ISEnable='No' Where Gift_Id=".$NewRowInfo['Gift_Id']);
				else
					$ar=DBDelete("delete from Hgift Where Gift_Id=".$NewRowInfo['Gift_Id']);
				
				
				logdbdelete($NewRowInfo,'Delete','Gift',$NewRowInfo['Gift_Id'],'');				
				
				echo "OK~";				
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}

?>
