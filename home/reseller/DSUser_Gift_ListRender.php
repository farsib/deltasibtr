<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_Gift_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list",'StartGift','CancelGift'),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSUser_Gift_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.Gift.List");
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");
				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				function color_rows($row){
					$data = $row->get_value("GiftStatus");
					if(($data=='Used')||($data=='AutoStop'))
						$row->set_row_style("color:green");
					elseif(($data=='Expire'))
						$row->set_row_style("color:chocolate");
					elseif(($data=='Active')||($data=='AutoStart'))
						$row->set_row_style("color:blue");
					elseif(($data=='Abandoned'))
						$row->set_row_style("color:darkorange");
					else if($data=='Cancel')
						$row->set_row_style("color:red");
				}
				DBUpdate("update Huser_gift set GiftStatus='Expire' where User_Id=$User_Id and GiftStatus='Pending' and User_Gift_ExpirationDT<>0 and User_Gift_ExpirationDT<=NOW()");
				DSGridRender_Sql(100,
					"Select User_Gift_Id,GiftStatus,GiftName,{$DT}DateTimeStr(User_Gift_ActiveDT) As User_Gift_ActiveDT,{$DT}DateTimeStr(User_Gift_ExpirationDT) As User_Gift_ExpirationDT,GiftDurationDays,GiftTrafficRate,GiftTimeRate,ByteToR(GiftExtraTr) as GiftExtraTr,GiftStopOnTrFinish,SecondToR(GiftExtraTi)GiftExtraTi,MikrotikRateName,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,{$DT}DateTimeStr(User_Gift_CDT) As User_Gift_CDT ".
					"From Huser_gift u join Hgift g on u.Gift_Id=g.Gift_Id left join Hmikrotikrate m on g.GiftMikrotikRate_Id=m.MikrotikRate_Id ".
					"Where (User_Id=$User_Id)" .$sqlfilter." $SortStr ",
					"User_Gift_Id",
					"User_Gift_Id,GiftStatus,GiftName,User_Gift_ActiveDT,User_Gift_ExpirationDT,GiftDurationDays,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftStopOnTrFinish,GiftExtraTi,MikrotikRateName,User_ServiceBase_Id,User_ServiceExtraCredit_Id,User_ServiceIP_Id,User_ServiceOther_Id,User_Gift_CDT",
					"","","color_rows");
       break;
    case "StartGift":
				DSDebug(0,"DSUser_Gift_ListRender->StartGift ********************************************");
				$User_Gift_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_gift where User_Gift_Id=$User_Gift_Id");
				exitifnotpermituser($User_Id,"Visp.User.Gift.Start");
				$GiftStatus=DBSelectAsString("Select GiftStatus from Huser_gift where User_Gift_Id=$User_Gift_Id");
				if($GiftStatus!='Pending') 
					ExitError('فقط هدیه در حال انتظار را می توان فعال کرد');
				
				$IsExpired=DBSelectAsString("Select if(User_Gift_ExpirationDT<>0 and User_Gift_ExpirationDT<=Now(),1,0) from Huser_gift where User_Gift_Id='$User_Gift_Id'");
				if($IsExpired>0){
					DBUpdate("update Huser_gift set GiftStatus='Expire' where User_Id=$User_Id and GiftStatus='Pending' and User_Gift_ExpirationDT<>0 and User_Gift_ExpirationDT<=NOW()");
					$User_Gift_ExpirationDT=DBSelectAsString("Select {$DT}DateTimeStr(User_Gift_ExpirationDT) from Huser_gift where User_Gift_Id=$User_Gift_Id");
					ExitError("و نمیتواند شروع شود '$User_Gift_ExpirationDT' این هدیه منقضی می شود در");
				}				
				
				$User_ServiceBase_Id=DBSelectAsString("SELECT User_ServiceBase_Id From Huser_servicebase Where (User_Id=$User_Id)And(ServiceStatus='Active')");
				if($User_ServiceBase_Id<=0)
					ExitError('سرویس پایه فعال یافت نشد');
				
				$CurrentUser_Gift_Id=DBSelectAsString("SELECT User_Gift_Id from Tuser_usage Where (User_Id=$User_Id)");
				if($CurrentUser_Gift_Id>0)
					ExitError('!هدیه قبلی هنوز پایان نیافته است');
					
				$ar=DBUpdate("Update Huser_gift Set GiftStatus='Active',User_Gift_ActiveDT=Now() Where User_Gift_Id=$User_Gift_Id");
				logdb('Update','User',$User_Id,'Gift',"Gift Id=$User_Gift_Id Started By $LResellerName");
				echo "OK~";
		break;
    case "CancelGift":
				DSDebug(0,"DSUser_Gift_ListRender->CancelGift ********************************************");
				$User_Gift_Id=Get_Input('GET','DB','Id','INT',1,4294967295,0,0);
				$User_Id=DBSelectAsString("Select User_Id from Huser_gift where User_Gift_Id=$User_Gift_Id");
				exitifnotpermituser($User_Id,"Visp.User.Gift.Cancel");
				$GiftStatus=DBSelectAsString("Select GiftStatus from Huser_gift where User_Gift_Id=$User_Gift_Id");
				if($GiftStatus=='Pending'){
					$ar=DBUpdate("Update Huser_gift Set GiftStatus='Cancel',User_Gift_ActiveDT=Now() Where User_Gift_Id=$User_Gift_Id");
					logdb('Update','User',$User_Id,'Gift',"Cancel gift. User_Gift_Id=$User_Gift_Id");
					echo "OK~";
				}
				elseif($GiftStatus=='Active'){
					$TempArr=Array();
					$sql="select GiftEndDT,GiftTrafficRate,GiftTimeRate,GiftExtraTr,GiftExtraTi from Tuser_usage where User_Id='$User_Id'";
					CopyTableToArray($TempArr,$sql);
					$GiftComment=
					"[GiftEndDT=".$TempArr[0]["GiftEndDT"]."],".
					"[GiftExtraTr=".$TempArr[0]["GiftExtraTr"]."Byte],".
					"[GiftTrafficRate=".$TempArr[0]["GiftTrafficRate"]."],".
					"[GiftExtraTi=".$TempArr[0]["GiftExtraTi"]."Sec],".
					"[GiftTimeRate=".$TempArr[0]["GiftTimeRate"]."]";
					$ar=DBUpdate("Update Huser_gift Set GiftStatus='Abandoned' Where User_Gift_Id=$User_Gift_Id");
					logdb('Update','User',$User_Id,'Gift',"User_Gift_Id=$User_Gift_Id abandoned By $LResellerName $GiftComment");
					echo "OK~";
				}
				else
					ExitError('فقط هدیه در حال انتظار را می توان لغو کرد');
		break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>