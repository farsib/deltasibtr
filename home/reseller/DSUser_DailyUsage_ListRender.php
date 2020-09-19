<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(0,"DSUser_DailyUsage_ListRender ..................................................................................");
if($LResellerName=='') 	ExitError('نشست منقضی شده، لطفا مجدد وارد شوید');
PrintInputGetPost();

//Check Permission


$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);


switch ($act) {
	case "list":
				DSDebug(0,"DSUser_DailyUsage_ListRender->List ********************************************");
				$User_Id=Get_Input('GET','DB','User_Id','INT',1,4294967295,0,0);
				exitifnotpermituser($User_Id,"Visp.User.DailyUsage.List");
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')	$SortStr="Order by $SortField $SortOrder";
				
				
				$ReportItem=Get_Input('GET','DB','ReportItem','ARRAY',array("HourlyTrafficUsage","HourlyTimeUsage"),0,0,0);

				$sql="SELECT DailyUsage_Id,{$DT}DateTimeStr(CreateDT) as CreateDT,{$DT}DateStr(UsageDate) as UsageDate,";
				$ColumnStr="DailyUsage_Id,CreateDT,UsageDate,";
				switch ($ReportItem){
					case "HourlyTrafficUsage":
					
						$sql.="ByteToR(RealSendTr) As RealSendTr,ByteToR(RealReceiveTr) As RealReceiveTr,ByteToR(FinishUsedTr) As FinishUsedTr,ByteToR(BugUsedTr) As BugUsedTr,ByteToR(Total) as Total, ".
						"ByteToR(HTrU0) As HTrU0,ByteToR(HTrU1) As HTrU1,ByteToR(HTrU2) As HTrU2,ByteToR(HTrU3) As HTrU3,ByteToR(HTrU4) As HTrU4,ByteToR(HTrU5) As HTrU5,".
						"ByteToR(HTrU6) As HTrU6,ByteToR(HTrU7) As HTrU7,ByteToR(HTrU8) As HTrU8,ByteToR(HTrU9) As HTrU9,ByteToR(HTrU10) As HTrU10,ByteToR(HTrU11) As HTrU11,".
						"ByteToR(HTrU12) As HTrU12,ByteToR(HTrU13) As HTrU13,ByteToR(HTrU14) As HTrU14,ByteToR(HTrU15) As HTrU15,ByteToR(HTrU16) As HTrU16,ByteToR(HTrU17) As HTrU17,".
						"ByteToR(HTrU18) As HTrU18,ByteToR(HTrU19) As HTrU19,ByteToR(HTrU20) As HTrU20,ByteToR(HTrU21) As HTrU21,ByteToR(HTrU22) As HTrU22,ByteToR(HTrU23) As HTrU23 ".
						"From deltasib_conn.Hdailyusage ";					

						$ColumnStr.="RealSendTr,RealReceiveTr,FinishUsedTr,BugUsedTr,Total,HTrU0,HTrU1,HTrU2,HTrU3,HTrU4,HTrU5,HTrU6,HTrU7,HTrU8,HTrU9,HTrU10,HTrU11,HTrU12,HTrU13,HTrU14,HTrU15,HTrU16,HTrU17,HTrU18,HTrU19,HTrU20,HTrU21,HTrU22,HTrU23";
						break;
					case "HourlyTimeUsage":
						$sql.="SecondToR(RealUsedTime) As RealUsedTime,SecondToR(FinishUsedTi) As FinishUsedTi,SecondToR(BugUsedTi) As BugUsedTi,SecondToR(Total) as Total, ".
						"SecondToR(HTiU0) As HTiU0,SecondToR(HTiU1) As HTiU1,SecondToR(HTiU2) As HTiU2,SecondToR(HTiU3) As HTiU3,SecondToR(HTiU4) As HTiU4,SecondToR(HTiU5) As HTiU5,".
						"SecondToR(HTiU6) As HTiU6,SecondToR(HTiU7) As HTiU7,SecondToR(HTiU8) As HTiU8,SecondToR(HTiU9) As HTiU9,SecondToR(HTiU10) As HTiU10,SecondToR(HTiU11) As HTiU11,".
						"SecondToR(HTiU12) As HTiU12,SecondToR(HTiU13) As HTiU13,SecondToR(HTiU14) As HTiU14,SecondToR(HTiU15) As HTiU15,SecondToR(HTiU16) As HTiU16,SecondToR(HTiU17) As HTiU17,".
						"SecondToR(HTiU18) As HTiU18,SecondToR(HTiU19) As HTiU19,SecondToR(HTiU20) As HTiU20,SecondToR(HTiU21) As HTiU21,SecondToR(HTiU22) As HTiU22,SecondToR(HTiU23) As HTiU23 ".
						"From deltasib_conn.Hdailyusage2 ";					

						$ColumnStr.="RealUsedTime,FinishUsedTi,BugUsedTi,Total,HTiU0,HTiU1,HTiU2,HTiU3,HTiU4,HTiU5,HTiU6,HTiU7,HTiU8,HTiU9,HTiU10,HTiU11,HTiU12,HTiU13,HTiU14,HTiU15,HTiU16,HTiU17,HTiU18,HTiU19,HTiU20,HTiU21,HTiU22,HTiU23";
						break;
					default:
						ExitError("~درخواست ناشناخته");
				}
				$sql.=" Where (User_Id=$User_Id) $sqlfilter $SortStr";				
				DSGridRender_Sql(100,$sql,"User_Id",$ColumnStr,"","","");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>