<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSRep_Reseller_Summary_ListRender.........................................................................");

if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

exitifnotpermit(0,"Report.Reseller.Summary.List");

$act=Get_Input('GET','DB','act','ARRAY',array("list"),0,0,0);

switch ($act) {
    case "list":
				DSDebug(0,"DSRep_Reseller_Summary_ListRender->List ********************************************");
				function color_rows($row){
					$data = $row->get_value("PayBalance");
					if($data<0)
						$Style="color:red;";
					else
						$Style="";
					$data = $row->get_value("Type");
					if($data=="Reseller")
						$Style.="font-weight:bold";
					
					$row->set_row_style($Style);
				}
			
				$sql="CREATE TEMPORARY TABLE ResellerSummaryTemp (
						Reseller_Id int(10),
						ResellerName varchar(32),
						ResellerCDT DATETIME,
						LastLoginDT DATETIME,
						LastLoginIP int(10),
						CreditBalance decimal(15,2),
						PayBalance decimal(15,2),
						SharePercent tinyint(4),
						ISEnable enum('Yes','No'),
						SessionTimeout mediumint(9),
						Type enum('Operator','Reseller'),
						ISManager enum('Yes','No'),
						ParentReseller varchar(32),
						PermitIp varchar(255),
						NoneBlockIP varchar(255),
						Name varchar(32),
						Family varchar(32),
						Mobile varchar(15),
						Phone varchar(100),
						Address varchar(255)
						)";
				$res=DBUpdate($sql);
				DSDebug(0,"QueryResult=$res\nCount of `ResellerSummaryTemp` value:".DBSelectAsString("select count(*) from ResellerSummaryTemp"));

				$sql="INSERT INTO ".
					"ResellerSummaryTemp(Reseller_Id,ResellerName,ResellerCDT,LastLoginDT,LastLoginIP,CreditBalance,".
					"PayBalance,SharePercent,ISEnable,SessionTimeout,Type,ISManager,".
					"ParentReseller,PermitIp,NoneBlockIP,Name,Family,Mobile,Phone,Address) ".
					"SELECT r.Reseller_Id,r.ResellerName,r.ResellerCDT,r.LastLoginDT,r.LastLoginIP,r.CreditBalance,".
					"r.PayBalance,r.SharePercent,r.ISEnable,r.SessionTimeout,if(r.ISOperator='Yes','Operator','Reseller'),r.ISManager,".
					"rp.ResellerName as ParentReseller,r.PermitIp,r.NoneBlockIP,r.Name,r.Family,r.Mobile,r.Phone,r.Address ".
					"from Hreseller r ".
					"left join Hreseller rp on r.ParentReseller_Id=rp.Reseller_Id ".
					"where $LResellerAccessAllow";
				DSDebug(1,"Filling temporary table with Service param");
				DBUpdate($sql);
				
				$sqlfilter=GetSqlFilter_GET("dsfilter");

				$SortField=Get_Input('GET','DB','SortField','STR',0,32,0,0);
				$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
				if($SortField!='')
					$SortStr="Order by $SortField $SortOrder";
				else 
					$SortStr="Order by Reseller_Id Desc";
									
				$SelectStr="Reseller_Id,ResellerName,{$DT}DateTimeStr(ResellerCDT) as ResellerCDT,{$DT}DateTimeStr(LastLoginDT) as LastLoginDT,".
							"inet_ntoa(LastLoginIP) as LastLoginIP,Format(CreditBalance,0) as CreditBalance,".
							"Format(PayBalance,0) as PayBalance,SharePercent,ISEnable,SessionTimeout,Type,ISManager".
							",ParentReseller,PermitIp,NoneBlockIP,Name,Family,Mobile,Phone,Address";
				$ColumnStr="Reseller_Id,ResellerName,ResellerCDT,LastLoginDT,LastLoginIP,CreditBalance,".
							"PayBalance,SharePercent,ISEnable,SessionTimeout,Type,ISManager".
							",ParentReseller,PermitIp,NoneBlockIP,Name,Family,Mobile,Phone,Address";
				
				$sql="select $SelectStr from ResellerSummaryTemp ".
					"where 1 ".$sqlfilter." $SortStr";
				DSGridRender_Sql(100,$sql,"Param_Id",$ColumnStr,"","","color_rows");
       break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}


?>
