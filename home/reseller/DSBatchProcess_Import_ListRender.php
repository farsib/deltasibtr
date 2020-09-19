<?php
try {
require_once("../../lib/DSInitialReseller.php");
DSDebug(1,"DSBatchProcess_Import_ListRender.........................................................................");
PrintInputGetPost();
if($LResellerName==""){
	header ("Content-Type:text/xml");
	echo "نشست منقضی شده، لطفا مجدد وارد شوید";
	Exit();
}

if($LReseller_Id!=1) ExitError('فقط ادمین می تواند کاربران را وارد کند');

set_time_limit(300);

$act=Get_Input('GET','DB','act','ARRAY',array(
    "list",
	"Parse",
	"TotalValidate",
	"InsertUser",
    "PrepareImport",
    "FinalizeImport",
    "Create",
    "SelectReseller",
    "SelectVisp",
    "SelectCenter",
    "SelectSupporter",
	"SelectStatus",
	"UploadFile",
	"GetHintFile",
	"DeleteSelected",
	"DeleteErrors",
	"SaveToFile"
    ),0,0,0);

if(extension_loaded("mbstring")==false)
	ExitError("بارگذاری نشده PHP mbstring بسته<br/>را اجرا کنید <span style='color:red'>yum install php-mbstring</span> در لینوکس</br> را اجرا کنید <span style='color:red'>service httpd restart</span> و سپس");
	
function GenerateLikePattern($Pattern,$NotUsedLetters,$NotUsedDigits){
	// DSDebug(2,"GenerateLikePattern for Pattern='$Pattern'      NotUsedChar='$NotUsedChar'");
	/*
	//Method 1
	while(strpos($Pattern,"$")!==false){
		do $rc=mt_rand(97,122); while(strpos($NotUsedLetters,$rc)!==false);
		$Pattern=preg_replace('/\$/',$rc,$Pattern,1);
	}
	while(strpos($Pattern,"#")!==false){
		do $rc=mt_rand(48,57); while(strpos($NotUsedDigits,$rc)!==false);
		$Pattern=preg_replace('/#/',$rc,$Pattern,1);
	}
	return $Pattern;
	//end of method 1*/
	/*
	//Method 2
	while(($pos = strpos($Pattern,"$"))!==false){
		do $rc=mt_rand(97,122); while(strpos($NotUsedLetters,$rc)!==false);
		$Pattern=substr_replace($Pattern,chr($rc),$pos,1);
	}
	while(($pos = strpos($Pattern,"#"))!==false){
		do $rc=mt_rand(48,57); while(strpos($NotUsedDigits,$rc)!==false);
		$Pattern=substr_replace($Pattern,chr($rc),$pos,1);
	}
	return $Pattern;
	//end of method 2*/
	
	
	//Method 3
	if((strpos($Pattern,"#")===false)&&(strpos($Pattern,"$")===false))
		return $Pattern;
	$Out="";
	$Len=strlen($Pattern);
	for($i=0;$i<$Len;++$i)
		if($Pattern[$i]=="$"){
			while(strpos($NotUsedLetters,$rc=mt_rand(97,122))!==false);
			$Out.=chr($rc);
		}
		elseif($Pattern[$i]=="#"){
			while(strpos($NotUsedDigits,$rc=mt_rand(48,57))!==false);
			$Out.=chr($rc);
		}
		else
			$Out.=$Pattern[$i];
	// DSDebug(2,"Result = ".$Out);
	return $Out;
	//end of method 3
}

function converttoutf8($s,$ISUtf8){
if($ISUtf8)
	return mb_ereg_replace("ي","ی",$s);
$len=strlen($s);
$out='';
for($i=0;$i<$len;$i++){
	if(ord($s[$i])==32) $out.=' ';
	elseif(ord($s[$i])==63) $out.='ی';
	elseif(ord($s[$i])<=128) $out.=$s[$i];
	elseif(ord($s[$i])==129) $out.='پ';
	elseif(ord($s[$i])==141) $out.='چ';
	elseif(ord($s[$i])==142) $out.='ژ';
	elseif(ord($s[$i])==144) $out.='گ';
	elseif(ord($s[$i])==152) $out.='ک';
	elseif(ord($s[$i])==170) $out.='ه';
	elseif(ord($s[$i])==192) $out.='ه';
	elseif(ord($s[$i])==193) $out.='ء';
	elseif(ord($s[$i])==194) $out.='آ';
	elseif(ord($s[$i])==195) $out.='أ';
	elseif(ord($s[$i])==196) $out.='ؤ';
	elseif(ord($s[$i])==197) $out.='إ';
	elseif(ord($s[$i])==198) $out.='ئ';
	elseif(ord($s[$i])==199) $out.='ا';
	elseif(ord($s[$i])==200) $out.='ب';
	elseif(ord($s[$i])==201) $out.='ة';
	elseif(ord($s[$i])==202) $out.='ت';
	elseif(ord($s[$i])==203) $out.='ث';
	elseif(ord($s[$i])==204) $out.='ج';
	elseif(ord($s[$i])==205) $out.='ح';
	elseif(ord($s[$i])==206) $out.='خ';
	elseif(ord($s[$i])==207) $out.='د';
	elseif(ord($s[$i])==208) $out.='ذ';
	elseif(ord($s[$i])==209) $out.='ر';
	elseif(ord($s[$i])==210) $out.='ز';
	elseif(ord($s[$i])==211) $out.='س';
	elseif(ord($s[$i])==212) $out.='ش';
	elseif(ord($s[$i])==213) $out.='ص';
	elseif(ord($s[$i])==214) $out.='ض';
	elseif(ord($s[$i])==216) $out.='ط';
	elseif(ord($s[$i])==217) $out.='ظ';
	elseif(ord($s[$i])==218) $out.='ع';
	elseif(ord($s[$i])==219) $out.='غ';
	elseif(ord($s[$i])==220) $out.='ـ';
	elseif(ord($s[$i])==221) $out.='ف';
	elseif(ord($s[$i])==222) $out.='ق';
	elseif(ord($s[$i])==223) $out.='ک';
	elseif(ord($s[$i])==225) $out.='ل';
	elseif(ord($s[$i])==227) $out.='م';
	elseif(ord($s[$i])==228) $out.='ن';
	elseif(ord($s[$i])==229) $out.='ه';
	elseif(ord($s[$i])==230) $out.='و';
	elseif(ord($s[$i])==236) $out.='ی';
	elseif(ord($s[$i])==237) $out.='ی';
	else $out.=$s[$i];
}
return $out;
}

function CreateImportUserTable(){
	$sql="CREATE TEMPORARY TABLE UserImport (".
		"User_Import_Id int(10) unsigned NOT NULL AUTO_INCREMENT,".
		"FileName varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"FileRowNumber int(10) unsigned NOT NULL DEFAULT 0,".
		"ParseResult ENUM('OK','Error','Warning','Imported','NotChecked','NotImported','Expired') DEFAULT 'NotChecked',".
		"ImportDT DATETIME,".
		"ParseComment varchar(400) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"User_Id int(10) unsigned NOT NULL DEFAULT 0,".
		"Username varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Pass varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Name varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Family varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"FatherName varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"NationalCode varchar(10) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Nationality varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"BirthDate varchar(10) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"AdslPhone varchar(15) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Phone varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Mobile varchar(11) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Comment varchar(255) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Email varchar(128) COLLATE utf8_persian_ci NOT NULL,".
		"PayBalance decimal(10,2) NOT NULL DEFAULT '0.00',".
		
		//"ServiceName varchar(128) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"StatusName varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"ResellerName varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"VispName varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"CenterName varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"SupporterName varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".			
		
		"NOE varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Address varchar(255) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"Organization varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"CompanyRegistryCode varchar(12) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"CompanyEconomyCode varchar(12) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"CompanyNationalCode varchar(12) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		"ExpirationDate varchar(10) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
		
		"PRIMARY KEY (User_Import_Id)".
		") DEFAULT CHARACTER SET utf8 COLLATE utf8_persian_ci ";
	DBUpdate($sql);
}
		
	
switch ($act) {
    case "list":
		// sleep(5);
		DSDebug(0,"DSBatchProcess_Import_ListRender->list********************************************");
		
		function color_rows($row){
			$ParseResult = $row->get_value("ParseResult");
			if($ParseResult=='Error')
				$row->set_row_style("color:red");
			elseif($ParseResult=='Warning')
				$row->set_row_style("color:saddlebrown");
			elseif($ParseResult=='Imported')
				$row->set_row_style("color:darkgreen;font-weight:bold");
			elseif($ParseResult=='NotImported')
				$row->set_row_style("color:crimson;font-weight:bold");
			elseif($ParseResult=='OK')
				$row->set_row_style("color:mediumblue");
		}
		
		
		$SortField=Get_InputIgnore('GET','DB','SortField','STR',0,32,0,0);
		if($SortField=='') $SortStr="order by User_Import_Id asc";
		else{
			$SortOrder=Get_Input('GET','DB','SortOrder','ARRAY',array("desc","asc",""),0,0,0);
			$SortStr="Order by $SortField $SortOrder";
		}
		DSDebug(0,"SortStr=$SortStr");		

		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		

		global $mysqli;
		require_once("../../lib/connector/grid_connector.php");
		$GridConn = new GridConnector($mysqli,"MySQLi");
		$GridConn->event->attach("beforeRender","color_rows");
	
		$ColumnStr="User_Import_Id,ParseResult,ParseComment,Username".
				",Pass,Name,Family,ResellerName,VispName,CenterName,SupporterName,".
				"FatherName,NationalCode,Nationality,BirthDate,AdslPhone,Phone,".
				"Mobile,Comment,Email,PayBalance,StatusName,NOE,Address,Organization,".
				"CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,ExpirationDate,FileRowNumber,FileName";
		$GridConn->dynamic_loading(100);
		$GridConn->render_sql("select $ColumnStr from deltasib_tmp.Import$Import_Id $SortStr","User_Import_Id",$ColumnStr,"","");

    break;
	
	
	
	case "Parse"://Parse****************************************************************************************
		DSDebug(0,"DSBatchProcess_Import_ListRender->Parse********************************************");
		
		$Action=Get_Input('POST','DB','Action','ARRAY',array("ImportUser","GenerateUser"),0,0,0);
		
		$Result="";
		$req=Get_InputIgnore('GET','DB','req','ARRAY',array("CheckUsers"),0,0,0);
		if($Action=="ImportUser"){
			function DeleteFileAndExitError($ErrMsg){
				global $req;
				if($req!="CheckUsers"){
					$FileCount=Get_Input('POST','DB','FileUploader_count','INT',1,4294967295,0,0);
					for($i=0;$i<$FileCount;++$i){
						$FileName="/tmp/dsimport/".Get_Input('POST','DB','FileUploader_s_'.$i,'STR',1,200,0,0);
						if(file_exists($FileName)){
							unlink($FileName);
							DSDebug(1,$FileName." removed.");
						}
					}
				}
				ExitError($ErrMsg);
			}
			$FieldsLengthList=Array(
					"username"=>32,
					"pass"=>64,
					"name"=>32,
					"family"=>32,
					"fathername"=>32,
					"nationalcode"=>10,
					"nationality"=>32,
					"birthdate"=>10,
					"adslphone"=>15,
					"phone"=>32,
					"mobile"=>11,
					"comment"=>255,
					"email"=>128,
					"paybalance"=>10,
					//"servicename"=>128,
					"statusname"=>64,
					"resellername"=>32,
					"vispname"=>32,
					"centername"=>64,
					"supportername"=>64,
					"noe"=>32,
					"address"=>255,
					"organization"=>64,
					"companyregistrycode"=>12,
					"companyeconomycode"=>12,
					"companynationalcode"=>12,
					"expirationdate"=>10
			);
			$ExtraFieldCount=0;
			$ExtraFieldLength=0;
			clearstatcache();
			CreateImportUserTable();
			$FileUploader_count=Get_Input('POST','DB','FileUploader_count','INT',1,4294967295,0,0);
			
			$IgnoreFieldLength=Get_Input('POST','DB','IgnoreFieldLength','INT',0,1,0,0);
			$IgnoreFieldCount=Get_Input('POST','DB','IgnoreFieldCount','INT',0,1,0,0);
			$DelimiterText=Get_Input('POST','DB','Delimiter','ARRAY',array("Auto","Comma","Tab"),0,0,0);
			
			for($i=0;$i<$FileUploader_count;++$i){//loop for files
				
				$ServerInputFile="/tmp/dsimport/".Get_Input('POST','DB','FileUploader_s_'.$i,'STR',1,200,0,0);
				$RealInputFile=Get_Input('POST','DB','FileUploader_r_'.$i,'STR',1,200,0,0);
				
				$FileEncodingTmp=shell_exec("file -bi $ServerInputFile");
				if(strpos($FileEncodingTmp,"charset=utf-8")===false)
					$ISUtf8=false;
				else
					$ISUtf8=true;

				if(($f=fopen($ServerInputFile,"r"))===false){
					DSDebug(0,"Cannot locate '$ServerInputFile'");
					DeleteFileAndExitError("فایل زیر در سرور مکان یابی نشد</br>'$RealInputFile'");
				}
				DSDebug(0,"The file $ServerInputFile succesfully opened for read");

				
				if(($linedata = fgets($f,1000))===false){
					DSDebug(0,"Invalid file content in '$ServerInputFile'");
					DeleteFileAndExitError("محتوا فایل زیر نامعتبر است</br>'$RealInputFile'!");
				}
				
				
				
				$linedata=mb_strtolower(trim($linedata));
				if($DelimiterText=="Auto"){
					if(strpos($linedata,"\t")!==false){
						$Delimiter="\t";
					}
					elseif((strpos($linedata,",")!==false)||($linedata=="username")){
						$Delimiter=",";
						$linedata=mb_ereg_replace("\s+", " ",$linedata);
					}
					else
						DeleteFileAndExitError("نمی توان جداکننده رو بطور خودکار تشخیص داد");
					
				}
				elseif($DelimiterText=="Comma"){
					$Delimiter=",";
					if(strpos($linedata,"\t")!==false)
						DeleteFileAndExitError("Header row in file '$RealInputFile' contains Tab(\\t) character while you have selected Comma(,) as delimiter!");
					$linedata=mb_ereg_replace("\s+", " ",$linedata);
				}
				elseif($DelimiterText=="Tab"){
					$Delimiter="\t";
					if(strpos($linedata,",")!==false)
						DeleteFileAndExitError("Header row in file '$RealInputFile' contains Comma(,) character while you have selected Tab(\\t) as delimiter!");
				}
				else
					DeleteFileAndExitError("جداکننده نامعتبر");
				
				DSDebug(2,"FieldsLengthList keys=\n".implode(",",array_keys($FieldsLengthList)));
				DSDebug(2,"Fields Name=\n".$linedata);
				
				$TheFields=explode($Delimiter,$linedata);
				$FieldCount=count($TheFields);
				$FileRowNumber=1;
				DSDebug(1,"count(TheFields)=".count($TheFields)."\ncount(FieldsLengthList)=".count($FieldsLengthList));
				
				if($FieldCount > count($FieldsLengthList))
					DeleteFileAndExitError("تعداد فیلد اضافی در فایل</br>'$RealInputFile'</br>در خط$FileRowNumber!");
				
				for($j=0;$j<$FieldCount;$j++){
									
					DSDebug(1,"TheFields[$j]=".$TheFields[$j]);
					
					if(!array_key_exists($TheFields[$j],$FieldsLengthList)){
						DSDebug(1,"Checking array_key_exists(TheFields[$j],FieldsLengthList)=False");
						DeleteFileAndExitError("در ستون '".($j+1)."' این فیلد معتبر نیست :'".$TheFields[$j]."' ".(($FileUploader_count>1)?"در فایل '$RealInputFile' ":""));
					}
				}
				if(!in_array("username",$TheFields))
					DeleteFileAndExitError("There is not required field 'Username' ".(($FileUploader_count>1)?"in file '$RealInputFile' ":"")."at line $FileRowNumber!");
				
				while (($linedata = fgets($f,2000)) !== false){//loop for rows of a file
					$linedata=converttoutf8($linedata,$ISUtf8);
					
					if($Delimiter!="\t")
						$linedata=mb_ereg_replace("\s+", " ",trim($linedata));
					else
						$linedata=trim($linedata," \n\r\0\x0B");
					
					$DataArray=explode($Delimiter,$linedata);
					
					$FileRowNumber++;
					DSDebug(1,"File=$ServerInputFile\nRow=$FileRowNumber\n$linedata");
					
					if(count($DataArray)!=$FieldCount){
						if($IgnoreFieldCount){
							$ExtraFieldCount++;
							continue;
						}
						DeleteFileAndExitError("Fields count error in file '$RealInputFile'. The column count=$FieldCount and there ".((count($DataArray)>1)?("are ".count($DataArray)." columns "):("is ".count($DataArray)." column ")).(($FileUploader_count>1)?"in file '$RealInputFile' ":"")."at line $FileRowNumber!");
					}
					
					
					$sql="Insert into UserImport set FileName='$RealInputFile',FileRowNumber=$FileRowNumber";
					
					$FieldLengthError=false;
					for($j=0;$j<$FieldCount;$j++){//loop for field of a row
						if(mb_strlen($DataArray[$j],"UTF-8")>$FieldsLengthList[$TheFields[$j]]){
							$FieldLengthError=true;
							$ExtraFieldLength++;
							if(!$IgnoreFieldLength)
								DeleteFileAndExitError("Extra character for field '".$TheFields[$j]."' ".(($FileUploader_count>1)?"in file '$RealInputFile' ":"")."at line $FileRowNumber!"." '".$DataArray[$j]."' has ".mb_strlen($DataArray[$j],"UTF-8")." character. Max ".$FieldsLengthList[$TheFields[$j]]." characters allowed!");
						}
						if($TheFields[$j]=='nationalcode')
							$sql.=",".$TheFields[$j]."=LPAD('".DSescape($DataArray[$j])."',10,'0')";
						elseif($TheFields[$j]=='mobile')
							$sql.=",".$TheFields[$j]."=LPAD('".DSescape($DataArray[$j])."',11,'0')";
						else
							$sql.=",".$TheFields[$j]."='".DSescape($DataArray[$j])."'";
						
					}//loop for field of a row
					if($FieldLengthError)
						$sql.=",ParseResult='Warning',ParseComment='ExtraFieldLength'";
					$res=DBInsert($sql);
					
					if($res>50000)
						DeleteFileAndExitError("در هربار بیش از ۵۰۰۰۰ کاربر را نمی توان وارد کرد.فایل /ها خود را چند بخش کنید و مجدد تلاش کنید");
					
				}//loop for rows of a file
				
				fclose($f);
				if($req!="CheckUsers")
					unlink($ServerInputFile);
			}//loop of files
			
			$TotalUserCount=DBSelectAsString("Select count(1) from UserImport");
			if($ExtraFieldCount>0)
				$Result="\n$ExtraFieldCount row(s) ignored due to extra column in row.";
			if($ExtraFieldLength>0)
				$Result.="\nData truncated in $ExtraFieldLength column due to extra characters.";
			if($req=="CheckUsers")
				if($FileUploader_count==1)
					exit("OK~فایل ورودی صحیح است\nتعداد $TotalUserCount نام کاربری در فایل تشخیص داده شد$Result");			
				else
					exit("OK~فایل های ورودی صحیح هستند\nتعداد $TotalUserCount نام کاربری در فایل ها تشخیص داده شد$Result");			
			$DefaultReseller=Get_Input('POST','DB','DefaultReseller','INT',0,4294967295,0,0);
			$DefaultVisp=Get_Input('POST','DB','DefaultVisp','INT',0,4294967295,0,0);
			$DefaultCenter=Get_Input('POST','DB','DefaultCenter','INT',0,4294967295,0,0);
			$DefaultSupporter=Get_Input('POST','DB','DefaultSupporter','INT',0,4294967295,0,0);
			$DefaultStatus=Get_Input('POST','DB','DefaultStatus','INT',0,4294967295,0,0);				
			array_push($TheFields,"filerownumber","filename","user_import_id","parseresult","parsecomment","username","pass","resellername","vispname","centername","supportername","statusname");
		}
		else{//Action=GenerateUser
		
			DSDebug(0,"------------------------CheckUsers---------------------------");
			$OrderType=Get_Input('POST','DB','OrderType','ARRAY',array("Random","Sequential"),0,0,0);
			$PasswordMask=Get_Input('POST','DB','PasswordMask','STR',1,64,0,0);	
			$TheFields=Array("user_import_id","parseresult","parsecomment","username","pass","resellername","vispname","centername","supportername","statusname");
			if($OrderType=="Random"){
				DSDebug(0,"OrderType='Random'");
				
				$UserCount=Get_Input('POST','DB','UserCount','INT',1,9999,0,0);
				$UsernameMask=strtolower(Get_Input('POST','DB','UsernameMask','STR',0,32,0,0));
				$NotUsedChar=strtolower(Get_Input('POST','DB','NotUsedChar','STR',0,35,0,0));
				$NotUsedLettersList=array();
				$NotUsedDigitsList=array();
				for($i=0;$i<strlen($NotUsedChar);$i++){
					$ch=substr($NotUsedChar,$i,1);
					if(($ch>="a")&&($ch<="z")&&(!in_array($ch,$NotUsedLettersList))){
						array_push($NotUsedLettersList,$ch);
						DSDebug(2,"Splitting NotUsedChar==>ch=$ch is a Letter...");
					}
					elseif(($ch>="0")&&($ch<="9")&&(!in_array($ch,$NotUsedDigitsList))){
						array_push($NotUsedDigitsList,$ch);
						DSDebug(2,"Splitting NotUsedChar==>ch=$ch is a Digit...");
					}
				}
				sort($NotUsedLettersList);
				sort($NotUsedDigitsList);
				
				$NotUsedLetters=implode("",$NotUsedLettersList);
				$NotUsedLettersCount=count($NotUsedLettersList);
				
				$NotUsedDigits=implode("",$NotUsedDigitsList);
				$NotUsedDigitsCount=count($NotUsedDigitsList);
				$NotUsedChar=$NotUsedLetters.$NotUsedDigits;

				DSDebug(1,"OrderType='$OrderType'\nUserCount='$UserCount'\nUsernameMask='$UsernameMask'\nNotUsedChar='$NotUsedChar'\nCalculated ==> NotUsedLetters='$NotUsedLetters'\nCalculated ==> NotUsedDigits='$NotUsedDigits'");		
				
				$NumberOfDollar=substr_count($UsernameMask,"$");
				$NumberOfSharp=substr_count($UsernameMask,"#");
				
				if(($NotUsedLettersCount>=26)&&($NumberOfDollar>0))
					ExitError("نمی توان نام کاربری ایجاد کرد در حالی که تمام حروف وارد شده جزء کاراکترهایی هستند که تعریف شده اند تا در نام کاربری نباشند");
				if(($NotUsedDigitsCount>=10)&&($NumberOfSharp>0))
					ExitError("نمی توان نام کاربری ایجاد کرد در حالی که تمام اعداد وارد شده جزء کاراکترهایی هستند که تعریف شده اند تا در نام کاربری نباشند");
				
				if(($NumberOfDollar==0)&&($NumberOfSharp==0))
					ExitError("در الگوی نام کاربری می بایست حداقل یک حرف و یا یک عدد وجود داشته باشد");
				
				$CountOfPossibleGenerate=pow(26-$NotUsedLettersCount,$NumberOfDollar)*pow(10-$NotUsedDigitsCount,$NumberOfSharp);
				if($CountOfPossibleGenerate<$UserCount)
					ExitError("حداکثر تعداد نام کاربری با الگو '$UsernameMask' ".($NotUsedChar!=""?"بدون استفاده از '$NotUsedChar' ":"")."تعداد '$CountOfPossibleGenerate' کاربر می باشد. بنابراین تعداد خواسته شده '$UserCount' کاربر را نمی توان ساخت");
				
				DSDebug(0,"NumberOfDollar=$NumberOfDollar   *****  NumberOfSharp=$NumberOfSharp   ====>    CountOfPossibleGenerate=$CountOfPossibleGenerate");
				
				
				if(($NotUsedLettersCount>0)&&($NumberOfDollar>0)){
					DSDebug(2,"Calcuating LettersRange regexp...");
					
					$LettersRange="";
					for($i=97;$i<=122;++$i){//97 is ascii code for 'a' and 122 is ascii code for 'z'
						if(in_array(chr($i),$NotUsedLettersList)){
							DSDebug(2,"Character $i => '".chr($i)."' entered as not used");
							continue;
						}
						DSDebug(2,"LowerBound set to $i => '".chr($i)."'");
						$LowerBound=$i;
						$UpperBound=$i++;
						while((!in_array(chr($i),$NotUsedLettersList))&&($i<=122)){
							DSDebug(2,"Looking for UpperBound... i = $i => '".chr($i)."'");
							$UpperBound=$i++;
						}
						DSDebug(2,"Finish UpperBound loop. LowerBound = $LowerBound => '".chr($LowerBound)."'  UpperBound = $UpperBound => '".chr($UpperBound)."'      i = $i => '".chr($i)."'");
						if($LowerBound==$UpperBound)
							$LettersRange.=chr($LowerBound);
						else
							$LettersRange.=chr($LowerBound)."-".chr($UpperBound);
						DSDebug(1,"LettersRange = '$LettersRange'");
					}
				}
				else
					$LettersRange="a-z";
				
				
				if(($NotUsedDigitsCount>0)&&($NumberOfSharp)){
					DSDebug(2,"Calcuating DigitsRange regexp...");
					
					$DigitsRange="";
					for($i=0;$i<=9;++$i){
						if(in_array($i,$NotUsedDigitsList)){
							DSDebug(2,"Character $i => $i entered as not used");
							continue;
						}
						DSDebug(2,"LowerBound set to $i");
						$LowerBound=$i;
						$UpperBound=$i++;
						while((!in_array($i,$NotUsedDigitsList))&&($i<=9)){
							DSDebug(2,"Looking for UpperBound... i = $i");
							$UpperBound=$i++;
						}
						DSDebug(2,"Finish UpperBound loop. LowerBound = $LowerBound       UpperBound = $UpperBound    i = $i");
						if($LowerBound==$UpperBound)
							$DigitsRange.=$LowerBound;
						else
							$DigitsRange.=$LowerBound."-".$UpperBound;
						DSDebug(1,"DigitsRange = '$DigitsRange'");
						
					}
				}
				else
					$DigitsRange="0-9";
				
				$UserRegExp=$UsernameMask;
				for($i=32;$i>1;--$i) 
					$UserRegExp=str_replace(substr("$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$",0,$i),"[$LettersRange]{".$i."}",str_replace(substr("################################",0,$i),"[$DigitsRange]{".$i."}",$UserRegExp));
				$UserRegExp=str_replace("$","[$LettersRange]",str_replace("#","[$DigitsRange]",$UserRegExp));
				
				$sql="select count(1) from Huser where Username regexp '^$UserRegExp$'";
				$CountOfExistedUsersLikePattern=DBSelectAsString($sql);
				DSDebug(0,"sql=$sql\nUserRegExp=$UserRegExp======>CountOfExistedUsersLikePattern=$CountOfExistedUsersLikePattern");
				
				
				$TotalCountOfPossibleGenerate = $CountOfPossibleGenerate-$CountOfExistedUsersLikePattern;
				
				if($TotalCountOfPossibleGenerate<$UserCount)
					ExitError("Despite total possible username with '$UsernameMask' ".($NotUsedChar!=""?"without using '$NotUsedChar' ":"")."is '$CountOfPossibleGenerate' users, But there ".(($CountOfExistedUsersLikePattern>1)?("are $CountOfExistedUsersLikePattern usernames"):("is $CountOfExistedUsersLikePattern username "))."like entered pattern exist".(($CountOfExistedUsersLikePattern>1)?(""):("s"))." in deltasib . So at most ".($TotalCountOfPossibleGenerate>1?"$TotalCountOfPossibleGenerate unique usernames":"$TotalCountOfPossibleGenerate unique username")." can generate with the requested pattern.");
				
				if($req=="CheckUsers")
					exit("OK~فیلدهای ورودی صحیح است\nتعداد $TotalCountOfPossibleGenerate نام کاربری با این الگو میتوان ساخت");
				
				CreateImportUserTable();
				DSDebug(1,"\n\nGenerate Random Username-----------------------------------------------------------------");
				
				$sql="Create temporary table TempUsers (".
					"Username varchar(32) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
					"Pass varchar(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
					"PRIMARY KEY (Username)".
					")";
				DBUpdate($sql);
				
				$UniqueCount=0;
				$t=microtime(true);
				$TimeOut=30;
				while($UniqueCount<$UserCount){
					if((microtime(true)-$t)>$TimeOut){
						DSDebug(0,"Timeout=$TimeOut occured");
						$Result="Only ".$UniqueCount." of requested ".$UserCount." generated due to timeout...";
						break;
					}
					$RequestedUser=$UserCount-$UniqueCount;
					DSDebug(1,"Generate $RequestedUser user.....................................................");
					$i=0;
					while(($i<$RequestedUser)&&((microtime(true)-$t)<=$TimeOut)){
						$tmp="Username='".GenerateLikePattern($UsernameMask,$NotUsedLetters,$NotUsedDigits)."',Pass='".GenerateLikePattern($PasswordMask,"","")."'";
						DSDebug(1,"$i of $RequestedUser => `$tmp`");
						$res=DBUpdate("Insert ignore into TempUsers set $tmp");
						$i+=$res;
					}
					
					DSDebug(1,"Inserted into unique username constrained table = $RequestedUser");
					$res=DBUpdate("delete tu from TempUsers tu join Huser du on tu.Username=du.Username");
					DSDebug(1,"Deleted duplicate username with existed users in deltasib =$res");
					
					$UniqueCount=DBSelectAsString("Select count(Username) from TempUsers");
					DSDebug(1,"Unique Generated Username till this phase = $UniqueCount");
				}
				DSDebug(0,"Elapsed time to generate $UniqueCount user(s) = ".(microtime(true)-$t));
				DBInsert("insert into UserImport(UserName,Pass) select Username,pass from TempUsers limit $UserCount");
				DBUpdate("Drop temporary table TempUsers");
			}
			else{//Sequential
				$FixPartOfUserName=strtolower(Get_Input('POST','DB','FixPartOfUserName','STR',0,32,0,0));
				$SequenceFrom=strtolower(Get_Input('POST','DB','SequenceFrom','STR',1,32,0,0));
				$SequenceTo=strtolower(Get_Input('POST','DB','SequenceTo','STR',1,32,0,0));
				$UseDigits=Get_Input('POST','DB','UseDigits','INT',0,1,0,0);
				$UseLetters=Get_Input('POST','DB','UseLetters','INT',0,1,0,0);
				

				DSDebug(1,"OrderType='$OrderType'\nFixPartOfUserName='$FixPartOfUserName'\nSequenceFrom='$SequenceFrom'\nSequenceTo='$SequenceTo'");
				
				if(preg_match("/[0-9]/", $SequenceFrom.$SequenceTo)&&($UseDigits!=1))
					ExitError("شما عدم استفاده از رقم را انتخاب کرده اید.ولی در الگوی به ترتیب رقم وارد کرده اید");
				if(preg_match("/[a-z]/", $SequenceFrom.$SequenceTo)&&($UseLetters!=1))
					ExitError("شما عدم استفاده از حرف را انتخاب کرده اید.ولی در الگوی به ترتیب حرف وارد کرده اید");
					
				if(strlen($SequenceFrom)!=strlen($SequenceTo))
					ExitError("length of SequenceFrom('$SequenceFrom') and SequenceTo('$SequenceTo') must be equal.");
				
				$Len=strlen($SequenceFrom);
				for($i=0;$i<$Len;++$i)
					if(substr($SequenceFrom,$i,1)!=substr($SequenceTo,$i,1))
						break;
				if($i>0)
					ExitError("Both '$SequenceFrom' and '$SequenceTo' are started with '".substr($SequenceFrom,0,$i)."'. Remove it from here and add it to the end of '$FixPartOfUserName'");
				
				if(strcmp($SequenceFrom,$SequenceTo)>=0)
					ExitError("SequenceFrom('$SequenceFrom') should be less than SequenceTo('$SequenceTo').");
				
				$SeriesBase=0;
				if($UseDigits==1)
					$SeriesBase+=10;
				if($UseLetters==1)
					$SeriesBase+=26;
				
				DSDebug(1,"SeriesBase=$SeriesBase");
				if($SeriesBase==10){
					$Diff=$SequenceTo-$SequenceFrom+1;
					DSDebug(1,"SequenceFrom = $SequenceFrom");
					DSDebug(1,"SequenceTo = $SequenceTo");
					DSDebug(1,"Difference = $Diff");
				}
				else{
					
					DSDebug(1,"SequenceFrom = $SequenceFrom");
					DSDebug(1,"SequenceTo = $SequenceTo");
					
					if($SeriesBase==26){
						$Mapper=substr("aaaaaaaaaa",0,$Len);
						DSDebug(1,"Mapper = $Mapper");
						$SequenceTo=base_convert(base_convert($SequenceTo,36,10)-base_convert($Mapper,36,10),10,36);
						$SequenceFrom=base_convert(base_convert($SequenceFrom,36,10)-base_convert($Mapper,36,10),10,36);
						DSDebug(1,"SequenceFrom after Mapped = $SequenceFrom");
						DSDebug(1,"SequenceTo after Mapped = $SequenceTo");
					}
					$Diff=base_convert($SequenceTo,$SeriesBase,10)-base_convert($SequenceFrom,$SeriesBase,10)+1;
					
					DSDebug(1,"$SequenceFrom in base 10 = ".base_convert($SequenceFrom,$SeriesBase,10));
					DSDebug(1,"$SequenceTo in base 10 = ".base_convert($SequenceTo,$SeriesBase,10));
					DSDebug(1,"Difference = $Diff");
				}
				if($Diff>9999)
					ExitError("Maximum 9999 users can generate at once($Diff users satisfy in the enetered '$FixPartOfUserName$SequenceFrom'-'$FixPartOfUserName$SequenceTo' ".($SeriesBase==10?"using only digits":($SeriesBase==26?"using only alphabets letters":"using both digits and alphabet letters")).").");
				
				if($req=="CheckUsers")
					exit("OK~فیلدهای ورودی صحیح هست\n$Diff نام کاربری در الگوی وارد شده ساخته خواهد شد");
				
				CreateImportUserTable();
				
				$OutUsers=Array();
				if($SeriesBase==10)
					for($i=0;$i<$Diff;++$i){
						$u=str_pad($i+$SequenceFrom,$Len,"0",STR_PAD_LEFT);
						DBInsert("Insert into UserImport set UserName='$FixPartOfUserName$u',Pass='".GenerateLikePattern($PasswordMask,"","")."'");
					}
				elseif($SeriesBase==26){
					$s=base_convert($SequenceFrom,26,10);
					$m=base_convert($Mapper,36,10);
					for($i=0;$i<$Diff;++$i){
						$u=base_convert(base_convert(base_convert($s+$i,10,26),36,10)+$m,10,36);
						DBInsert("Insert into UserImport set UserName='$FixPartOfUserName$u',Pass='".GenerateLikePattern($PasswordMask,"","")."'");
					}
				}
				else{
					$s=base_convert($SequenceFrom,36,10);
					for($i=0;$i<$Diff;++$i){
						$u=str_pad(base_convert($s+$i,10,36),$Len,"0",STR_PAD_LEFT);
						DBInsert("Insert into UserImport set UserName='$FixPartOfUserName$u',Pass='".GenerateLikePattern($PasswordMask,"","")."'");
					}
				}
			}
			
			$DefaultReseller=Get_Input('POST','DB','DefaultReseller','INT',1,4294967295,0,0);
			$DefaultVisp=Get_Input('POST','DB','DefaultVisp','INT',1,4294967295,0,0);
			$DefaultCenter=Get_Input('POST','DB','DefaultCenter','INT',1,4294967295,0,0);
			$DefaultSupporter=Get_Input('POST','DB','DefaultSupporter','INT',1,4294967295,0,0);
			$DefaultStatus=Get_Input('POST','DB','DefaultStatus','INT',1,4294967295,0,0);
			$TotalUserCount=DBSelectAsString("Select count(1) from UserImport");
		}

		if($TotalUserCount<=0)
			ExitError("کاربری تشخیص داده نشد");
		// elseif($TotalUserCount>10000)
			// ExitError("Cannot import or generate more than 10000 user at once.");
		$DefResellerName=(($DefaultReseller!=0)?(DBSelectAsString("Select ResellerName from Hreseller where Reseller_Id=$DefaultReseller")):"");
		$DefVispName=(($DefaultVisp!=0)?(DBSelectAsString("Select VispName from Hvisp where Visp_Id=$DefaultVisp")):"");
		$DefCenterName=(($DefaultCenter!=0)?(DBSelectAsString("Select CenterName from Hcenter where Center_Id=$DefaultCenter")):"");
		$DefSupporterName=(($DefaultSupporter!=0)?(DBSelectAsString("Select SupporterName from Hsupporter where Supporter_Id=$DefaultSupporter")):"");
		$DefStatusName=(($DefaultStatus!=0)?(DBSelectAsString("Select StatusName from Hstatus where Status_Id=$DefaultStatus")):"");
		DBUpdate("update UserImport set ResellerName='$DefResellerName' where ResellerName=''");
		DBUpdate("update UserImport set VispName='$DefVispName' where VispName=''");
		DBUpdate("update UserImport set CenterName='$DefCenterName' where CenterName=''");
		DBUpdate("update UserImport set SupporterName='$DefSupporterName' where SupporterName=''");
		DBUpdate("update UserImport set StatusName='$DefStatusName' where StatusName=''");

		
		if($Action=="ImportUser"){//Normalize all relational fields to the character case in the database... eg: AdMiN----->Admin
			DBUpdate("update UserImport UI join Hreseller Hr on UI.ResellerName=Hr.ResellerName set UI.ResellerName=Hr.ResellerName");
			DBUpdate("update UserImport UI join Hvisp Hv on UI.VispName=Hv.VispName set UI.VispName=Hv.VispName");
			DBUpdate("update UserImport UI join Hcenter Hc on UI.CenterName=Hc.CenterName set UI.CenterName=Hc.CenterName");
			DBUpdate("update UserImport UI join Hsupporter Hs on UI.SupporterName=Hs.SupporterName set UI.SupporterName=Hs.SupporterName");
			DBUpdate("update UserImport UI join Hstatus Hs on UI.StatusName=Hs.StatusName set UI.StatusName=Hs.StatusName");
		}
		
		$sql="CREATE DATABASE IF NOT EXISTS deltasib_tmp";
		DBUpdate($sql);

		$sql="CREATE TABLE IF NOT EXISTS deltasib_tmp.ImportSummary (".
				"Import_Id int(10) NOT NULL AUTO_INCREMENT,".
				"ImportCDT DATETIME,".
				"StartDT DATETIME,".
				"ImportState ENUM('Pending','Done','Error','InProgress') NOT NULL DEFAULT 'Pending',".
				"ImportName VARCHAR(64) COLLATE utf8_persian_ci NOT NULL DEFAULT '',".
				"ImportType ENUM('GenerateUser','ImportUser') NOT NULL DEFAULT 'GenerateUser',".
				"PRIMARY KEY Import_Id (Import_Id)".
			")";
		DBUpdate($sql);
		
		$sql="Insert into deltasib_tmp.ImportSummary set ImportCDT=now(),ImportType='$Action'";
		$Import_Id=DBInsert($sql);
		if($Import_Id<=0)
			ExitError("!نمی توان نشست وارد کردن را ایجاد کرد");
		DBUpdate("CREATE TABLE deltasib_tmp.Import$Import_Id SELECT * from UserImport");
		
		echo "OK~$Result~$Import_Id~$TotalUserCount~".implode("`",$TheFields);
	break;
	case "TotalValidate":
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		$PatternCheck=Get_Input('GET','DB','PatternCheck','ARRAY',array("Yes","No"),0,0,0);
		if($PatternCheck=='Yes')
			$PatternParseLevel='Error';
		else
			$PatternParseLevel='Warning';
		$InitialStatusCheck=Get_Input('GET','DB','InitialStatusCheck','ARRAY',array("Yes","No"),0,0,0);
		if($InitialStatusCheck=='Yes')
			$StatusParseLevel='Error';
		else
			$StatusParseLevel='Warning';


		
		//Reset all validation to NotChecked
		DBUpdate("Update deltasib_tmp.Import$Import_Id set ParseResult='NotChecked',ParseComment=''");	
		
		
		$sql="update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment=concat(".
			"if(UserName='','UserName ',''),".
			"if(ResellerName='','ResellerName ',''),".
			"if(VispName='','VispName ',''),".
			"if(CenterName='','CenterName ',''),".
			"if(SupporterName='','SupporterName ',''),".
			"if(StatusName='','StatusName ',''),".
			"'can not be empty') where ResellerName='' or VispName='' or CenterName='' or SupporterName='' or StatusName='' or UserName=''";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError("برخی کاربرها،فیلدهای ضروری برای آن ها موجود نیست<br/>نام کاربری, نام نماینده فروش, نام ارائه دهنده مجازی اینترنت, نام مرکز, نام پشتیبان و نام وضعیت ، فیلدهای ضروری هستند و نباید خالی باشند <br/>[تعداد $res ردیف]");//Empty fields
		
		
		$sql="update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment='Invalid UserName' where UserName not regexp '^[\-\_\.\:\=\@a-zA-Z0-9]+$'";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError("نام کاربری نامعتبر [$res row(s)].");//Invalid UserName
		
		$sql="update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment='Invalid ResellerName' where ResellerName not regexp '^([\-\_\.a-zA-Z0-9]+)$'";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError("نماینده فروش نامعتبر [$res row(s)].");//Invalid ResellerName
		
		$sql="update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment='Invalid VispName' where VispName not regexp '^([\-\_\}\{a-zA-Z0-9]+)$'";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError("ارائه دهنده مجازی اینترنت نامعتبر [$res row(s)].");//Invalid VispName		
		
		//Check for UserDuplicate
		$sql="update deltasib_tmp.Import$Import_Id UI join (select Username,Count(1) as cnt from deltasib_tmp.Import$Import_Id group by Username having cnt>1) dup on UI.Username=dup.Username set ParseResult='Error',ParseComment='Duplicate Username in the LIST'";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError("برخی از نام های کاربری تکرار شده اند [$res row(s)].");
		
		$sql="update deltasib_tmp.Import$Import_Id UI join Huser Hu on UI.Username=Hu.Username set ParseResult='Error',ParseComment='Duplicate Username found'";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError("نام کاربری تکراری یافت شد [$res row(s)].");
		
		
		//Operator check
		$sql="Update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment='An operator could not set as Reseller' where ResellerName in (Select ResellerName from Hreseller where ISOperator='Yes')";
		$res=DBUpdate($sql);
		if($res>0)
			ExitError(" نماینده فروش برخی کاربران اپراتور تشخیص داده شده<br/>اپراتور نمیتواند به عنوان نماینده فروش قرار بگیرد [$res row(s)].");//IsOperator Resellers

		
		$NotFound=array();
		//Reseller check
		$sql="Update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment='ResellerName ' where ResellerName not in (Select ResellerName from Hreseller where ISOperator='No')";
		$res=DBUpdate($sql);
		if($res>0){
			$tmp=DBSelectAsString("select count(distinct UI.ResellerName) from deltasib_tmp.Import$Import_Id UI where UI.ResellerName not in (Select ResellerName from Hreseller where ISOperator='No')");
			array_push($NotFound,"Reseller=".$tmp);
		}

		//Visp check
		$sql="Update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment=concat(ParseComment,'VispName ') where VispName not in (Select VispName from Hvisp)";
		$res=DBUpdate($sql);
		if($res>0){
			$tmp=DBSelectAsString("select count(distinct UI.VispName) from deltasib_tmp.Import$Import_Id UI where UI.VispName not in (Select VispName from Hvisp)");
			array_push($NotFound,"Visp=".$tmp);
		}
		
		//Center check		
		$sql="Update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment=concat(ParseComment,'CenterName ') where CenterName not in (Select CenterName from Hcenter)";
		$res=DBUpdate($sql);
		if($res>0){
			$tmp=DBSelectAsString("select count(distinct UI.CenterName) from deltasib_tmp.Import$Import_Id UI where UI.CenterName not in (Select CenterName from Hcenter)");
			array_push($NotFound,"Center=".$tmp);
		}
		
		//Supporter check		
		$sql="Update deltasib_tmp.Import$Import_Id set ParseResult='Error',ParseComment=concat(ParseComment,'SupporterName ') where SupporterName not in (Select SupporterName from Hsupporter)";
		$res=DBUpdate($sql);
		if($res>0){
			$tmp=DBSelectAsString("select count(distinct UI.SupporterName) from deltasib_tmp.Import$Import_Id UI where UI.SupporterName not in (Select SupporterName from Hsupporter)");
			array_push($NotFound,"Supporter=".$tmp);
		}
		
		//Status check
		$sql="Update deltasib_tmp.Import$Import_Id t set ParseResult='Error',ParseComment=concat(ParseComment,'StatusName ') where t.StatusName not in (Select s.StatusName from Hstatus s)";
		$res=DBUpdate($sql);
		if($res>0){
			$tmp=DBSelectAsString("select count(distinct UI.StatusName) from deltasib_tmp.Import$Import_Id UI where UI.StatusName not in (Select StatusName from Hstatus)");
			array_push($NotFound,"Status=".$tmp);
		}
		
		if(count($NotFound)>0){
			$sql="Update deltasib_tmp.Import$Import_Id set ParseComment=concat('Invalid ',ParseComment,' specified!') where ParseResult='Error'";
			$res=DBUpdate($sql);
			exit("NotFound~".implode("`",$NotFound)."~مقادیر نامعتبر را بررسی کنید");
		}
		
		//Pattern check
		$sql="Update deltasib_tmp.Import$Import_Id UI join Hvisp Hv on UI.VispName=Hv.VispName set ParseResult='$PatternParseLevel',ParseComment='Username pattern not matched in Visp' where Username not regexp UsernamePattern";
		$res=DBUpdate($sql);
		if(($PatternParseLevel=='Error')&&($res>0))
			exit("PatternCheck~Username pattern not matched in Visp");
		$sql="Update deltasib_tmp.Import$Import_Id UI join Hcenter Hc on UI.CenterName=Hc.CenterName set ParseResult='$PatternParseLevel',ParseComment=concat(ParseComment,if(ParseComment='','',' & '),'Username pattern not matched in Center') where Username not regexp UsernamePattern";
		$res=DBUpdate($sql);
		if(($PatternParseLevel=='Error')&&($res>0))
			exit("PatternCheck~Username pattern not matched in Center");
		$sql="Update deltasib_tmp.Import$Import_Id UI join Hsupporter Hs on UI.SupporterName=Hs.SupporterName set ParseResult='$PatternParseLevel',ParseComment=concat(ParseComment,if(ParseComment='','',' & '),'Username pattern not matched in Supporter') where Username not regexp UsernamePattern";
		$res=DBUpdate($sql);
		if(($PatternParseLevel=='Error')&&($res>0))
			exit("PatternCheck~Username pattern not matched in Supporter");

		
		$sql="Update deltasib_tmp.Import$Import_Id UI join Hstatus Hs on UI.StatusName=Hs.StatusName set ParseResult='$StatusParseLevel',ParseComment=concat(ParseComment,if(ParseComment='','',' & '),'Supplied StatusName is not an InitialStatus') where InitialStatus='No'";
		$res=DBUpdate($sql);
		if(($StatusParseLevel=='Error')&&($res>0))
			exit("InitialStatusCheck~وضعیت داده شده،وضعیت اولیه آن فعال نیست");
		
		
		DBUpdate("Update deltasib_tmp.Import$Import_Id set ParseResult='OK' where ParseResult='NotChecked'");	
		
		echo "OK~".DBSelectAsString("select count(1) from deltasib_tmp.Import$Import_Id");
	break;
	case "Create":
		$Item=Get_Input('GET','DB','Item','ARRAY',array("CreateReseller","CreateVisp","CreateCenter","CreateSupporter","CreateStatus"),0,0,0);
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		if($Item=='CreateReseller'){
			$sql="create temporary table ResellerTemp select 0 as RId,ResellerName from deltasib_tmp.Import$Import_Id where resellername ".
					 "not in (select Resellername from deltasib.Hreseller) group by ResellerName";
			$res1=DBUpdate($sql);
			
			$sql = "insert into Hreseller(ParentReseller_Id,ResellerPath,ISEnable,ResellerCDT,ResellerName,Pass,Salt,PermitIp,ISOperator) ".
					 "select 1,'>1>','Yes',now(),ResellerName,floor(rand() * 4000000000),floor(rand() * 4000000000),'0.0.0.0/0','No' ".
					 "from ResellerTemp";
			DBUpdate($sql);
			
			$sql="Update ResellerTemp t join Hreseller r on t.ResellerName=r.ResellerName set t.RId=Reseller_Id";
			$res2=DBUpdate($sql);
			
			$sql ="Insert Hlogdb(LogDbCDT,Reseller_Id,User_Id,ClientIP,LogType,DataName,DataId,ChildDataName,Comment) ".
					"select now(),'$LReseller_Id','',INET_ATON('$LClientIP'),'Add','Reseller',RId,'Reseller','Created for import user'".
					"from ResellerTemp";
			DBUpdate($sql);
			if($res1==$res2)
				echo "OK~ایجاد نماینده فروش مورد نیاز با موفقیت انجام شد";
			else
				echo "OK~Neccessary Reseller(s) created. Check for error again.";
		}
		elseif($Item=='CreateVisp'){
			$sql="create temporary table VispTemp select 0 as VId,VispName from deltasib_tmp.Import$Import_Id where VispName ".
					 "not in (select VispName from deltasib.Hvisp) group by VispName";
			$res1=DBUpdate($sql);
			
			$sql = "insert into Hvisp(VispName,ISEnable,UsernamePattern) ".
					 "select VispName,'Yes','.*' from VispTemp";
			DBUpdate($sql);
			
			$sql="Update VispTemp t join Hvisp v on t.VispName=v.VispName set t.VId=Visp_Id";
			$res2=DBUpdate($sql);
			
			$sql ="Insert Hlogdb(LogDbCDT,Reseller_Id,User_Id,ClientIP,LogType,DataName,DataId,ChildDataName,Comment)".
					"select now(),'$LReseller_Id','',INET_ATON('$LClientIP'),'Add','Visp',VId,'Visp','Created for import user'".
					"from VispTemp";
			DBUpdate($sql);
			if($res1==$res2)
				echo "OK~ایجاد ارائه دهنده مجازی مورد نیاز با موفقیت انجام شد";
			else
				echo "OK~Neccessary Visp(s) created. Check for error again.";
		}
		elseif($Item=='CreateCenter'){
			$sql="create temporary table CenterTemp select 0 as CId,CenterName from deltasib_tmp.Import$Import_Id where CenterName ".
					 "not in (select CenterName from deltasib.Hcenter) group by CenterName";
			$res1=DBUpdate($sql);
			
			$sql = "insert into Hcenter(CenterName,TotalPort,BadPort,ISEnable,UsernamePattern) ".
					 "select CenterName,999999,0,'Yes','.*' from CenterTemp";
			DBUpdate($sql);
			
			$sql="Update CenterTemp t join Hcenter c on t.CenterName=c.CenterName set t.CId=Center_Id";
			$res2=DBUpdate($sql);
			
			$sql ="Insert Hlogdb(LogDbCDT,Reseller_Id,User_Id,ClientIP,LogType,DataName,DataId,ChildDataName,Comment)".
					"select now(),'$LReseller_Id','',INET_ATON('$LClientIP'),'Add','Center',CId,'Center','Created for import user'".
					"from CenterTemp";
			DBUpdate($sql);
			if($res1==$res2)
				echo "OK~ایجاد مرکز مورد نیاز با موفقیت انجام شد";
			else
				echo "OK~Neccessary Center(s) created. Check for error again.";
		}
		elseif($Item=='CreateSupporter'){
			$sql="create temporary table SupporterTemp select 0 as SId,SupporterName from deltasib_tmp.Import$Import_Id where SupporterName ".
					 "not in (select SupporterName from deltasib.Hsupporter) group by SupporterName";
			$res1=DBUpdate($sql);
			
			$sql = "insert into Hsupporter(SupporterName,Reseller_Id,ISEnable,UsernamePattern) ".
					 "select SupporterName,1,'Yes','.*' from SupporterTemp";
			DBUpdate($sql);
			
			$sql="Update SupporterTemp t join Hsupporter s on t.SupporterName=s.SupporterName set t.SId=Supporter_Id";
			$res2=DBUpdate($sql);
			
			$sql ="Insert Hlogdb(LogDbCDT,Reseller_Id,User_Id,ClientIP,LogType,DataName,DataId,ChildDataName,Comment)".
					"select now(),'$LReseller_Id','',INET_ATON('$LClientIP'),'Add','Supporter',SId,'Supporter','Created for import user'".
					"from SupporterTemp";
			DBUpdate($sql);
			if($res1==$res2)
				echo "OK~ایجاد پشتیبان مورد نیاز با موفقیت انجام شد";
			else
				echo "OK~Neccessary Supporter(s) created. Check for error again.";
		}
		elseif($Item=='CreateStatus'){
			$sql="create temporary table StatusTemp select 0 as SId,StatusName from deltasib_tmp.Import$Import_Id where StatusName ".
					 "not in (select StatusName from deltasib.Hstatus) group by StatusName";
			$res1=DBUpdate($sql);
			
			$sql = "insert into Hstatus(StatusName,UserStatus,InitialStatus,CanWebLogin,CanAddService,IsBusyPort,PortStatus) ".
					 "select StatusName,'Enable','Yes','Yes','Yes','Yes','Busy' from StatusTemp";
			DBUpdate($sql);
			
			$sql="Update StatusTemp t join Hstatus s on t.StatusName=s.StatusName set t.SId=Status_Id";
			$res2=DBUpdate($sql);
			
			$sql ="Insert Hlogdb(LogDbCDT,Reseller_Id,User_Id,ClientIP,LogType,DataName,DataId,ChildDataName,Comment)".
					"select now(),'$LReseller_Id','',INET_ATON('$LClientIP'),'Add','Status',SId,'Status','Created for import user'".
					"from StatusTemp";
			DBUpdate($sql);
			if($res1==$res2)
				echo "OK~ایجاد وضعیت مورد نیاز با موفقیت انجام شد)";
			else
				echo "OK~Neccessary Status(s) created. Check for error again.";
		}
		else
			echo "~InvalidRequest";
	break;
	case "InsertUser":
		usleep(150000);
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		$RowIndex=Get_Input('GET','DB','RowIndex','INT',0,4294967295,0,0);
		$RowId=DBSelectAsString("select User_Import_Id from deltasib_tmp.Import$Import_Id limit $RowIndex,1");
		DSDebug(1,"RowIndex=$RowIndex	=>	RowId=$RowId");
		
		$sql="update deltasib_tmp.Import$Import_Id set ParseResult='Error' where User_Import_Id=$RowId";
		$res=DBUpdate($sql);
		
		$sql= "insert Huser(UserCDT,Username,Visp_Id,Center_Id,Supporter_Id,Reseller_Id,Pass,Name,Family,FatherName,NationalCode,Nationality,BirthDate,AdslPhone,Phone,Mobile,Comment,Email,PayBalance,NOE,Address,Organization,CompanyRegistryCode,CompanyEconomyCode,CompanyNationalCode,ExpirationDate) ".
				"select Now(),UI.Username,Visp_Id,Center_Id,Supporter_Id,Hr.Reseller_Id,UI.Pass,UI.Name,UI.Family,UI.FatherName,".
				"LPAD(UI.NationalCode,10,'0'),UI.Nationality,shdatestrtomstr(UI.BirthDate),UI.AdslPhone,UI.Phone,LPAD(UI.Mobile,11,'0'),UI.Comment,UI.Email,UI.PayBalance,".
				"UI.NOE,UI.Address,UI.Organization,UI.CompanyRegistryCode,UI.CompanyEconomyCode,UI.CompanyNationalCode,shdatestrtomstr(UI.ExpirationDate) ".
				"from deltasib_tmp.Import$Import_Id UI join ".
				"Hvisp Hv on UI.VispName=Hv.VispName join ".
				"Hcenter Hc on UI.CenterName=Hc.CenterName join ".
				"Hsupporter Hs on UI.SupporterName=Hs.SupporterName join ".
				"Hreseller Hr on UI.ResellerName=Hr.ResellerName ".
				"where User_Import_Id=$RowId";
		$NewUserId=DBInsert($sql);
		
		if($NewUserId==0){
			$UserName=DBSelectAsString("select Username from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			$VispName=DBSelectAsString("select VispName from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			if(DBSelectAsString("select count(1) from Hvisp where VispName=$VispName")!=1)
				ExitError("را ایجاد کرد '$UserName'نمیتوان کاربر </br> ('$VispName')ارائه دهنده مجازی نامعتبر");
			
			$CenterName=DBSelectAsString("select CenterName from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			if(DBSelectAsString("select count(1) from Hcenter where CenterName=$CenterName")!=1)
				ExitError("را ایجاد کرد '$UserName'نمیتوان کاربر </br> ('$CenterName')مرکز نامعتبر");
			
			$SupporterName=DBSelectAsString("select SupporterName from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			if(DBSelectAsString("select count(1) from Hsupporter where SupporterName=$SupporterName")!=1)
				ExitError("را ایجاد کرد '$UserName'نمیتوان کاربر </br>('$SupporterName')پشتیبان نامعتبر");
			
			$ResellerName=DBSelectAsString("select ResellerName from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			if(DBSelectAsString("select count(1) from Hreseller where ResellerName=$ResellerName")!=1)
				ExitError("را ایجاد کرد '$UserName'نمیتوان کاربر </br>('$ResellerName')نماینده فروش نامعتبر");
			
			ExitError("نمی توان کاربر زیر راایجاد کرد</br>'$UserName'");
		}
		
		$PayBalance=DBSelectAsString("Select PayBalance from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
		if($PayBalance<>0)
			DBUpdate("update Huser_payment set price=$PayBalance,PayBalance=$PayBalance where User_Id=$NewUserId");
		
		$sql= "Insert into Huser_status(Reseller_Id,StatusCDT,User_Id,Status_Id) select $LReseller_Id,Now(),$NewUserId,Status_Id from ".
				"deltasib_tmp.Import$Import_Id UI join ".
				"Hstatus Hs on UI.StatusName=Hs.StatusName ".
				"where User_Import_Id=$RowId";
		$res=DBInsert($sql);
		if($res==0){
			$UserName=DBSelectAsString("select Username from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			$StatusName=DBSelectAsString("select StatusName from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId");
			ExitError("User '$UserName' Created. But cannot add entered UserStatus('$StatusName') for user.");
		}
		
		
		$sql ="Insert Hlogdb set ".
				"LogDbCDT=Now(),".
				"Reseller_Id='$LReseller_Id',".
				"User_Id='',".
				"ClientIP=INET_ATON('$LClientIP'),".
				"LogType='Add',".
				"DataName='User',".
				"DataId='$NewUserId',".
				"ChildDataName='User',".
				"Comment='BatchProcess[Import User]'";
		DBUpdate($sql);
		
		$sql="update deltasib_tmp.Import$Import_Id set ParseResult='Imported',User_Id=$NewUserId,ImportDT=now() where User_Import_Id=$RowId";
		$res=DBUpdate($sql);
		
		$sql="update Tonline_web_ipblock set ".
			"LastDayRequest=LastDayRequest-1,".
			"LastHourRequest=LastHourRequest-1,".
			"LastMinuteRequest=LastMinuteRequest-1,".
			"LastSecondRequest=LastSecondRequest-1 ".
			"where ClientIP=INET_ATON('$LClientIP')";
		DBUpdate($sql);
		echo "OK~";
	break;
	case "PrepareImport":
		DSDebug(0,"DSBatchProcess_Import_ListRender->PrepareImport********************************************");
		// $sql="SELECT ISNoneBlock from Tonline_web_ipblock where ClientIP=INET_ATON('$LClientIP')";
		// $res=DBSelectAsString($sql);
		// DSDebug(0,"sql=$sql\nQueryResult=$res");
		// if($res!='Yes')
			// ExitError('Your IP is not trusted. Set NoneBlockIP first');
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);

		$Out="OK~";
		DBUpdate("update deltasib_tmp.Import$Import_Id set ParseResult='OK',ParseComment=''");
		$ImportName=Get_Input('GET','DB','ImportName','STR',0,50,0,0);
		do{
			$n=DBSelectAsString("select count(1) from Hbatchprocess where BatchProcessName='$ImportName'");
			if($n>0){
				$ImportName=$ImportName."(".($n+1).")";
				$Out="NewName~";
			}
		}while($n>0);
		
		if($Out!="OK~")
			$Out=$Out.$ImportName;
		DBUpdate("Update deltasib_tmp.ImportSummary set ImportName='$ImportName',StartDT=now(),ImportState='InProgress' where Import_Id=$Import_Id and ImportName=''");
		echo $Out;
	break;
	
    case "FinalizeImport":
		DSDebug(0,"DSBatchProcess_Import_ListRender->FinalizeImport********************************************");
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		$State=Get_Input('GET','DB','State','ARRAY',array("finished","canceled"),0,0,0);
		$UserCount=DBSelectAsString("Select count(1) from deltasib_tmp.Import$Import_Id where ParseResult='Imported'");
		
		DBUpdate("update deltasib_tmp.Import$Import_Id set ParseResult='NotImported',ParseComment='' where ParseResult<>'Imported'");
		
		$Out="OK~";
		if($UserCount>0){
			$ImportName=DBSelectAsString("select ImportName from deltasib_tmp.ImportSummary where Import_Id=$Import_Id");
			do{
				$n=DBSelectAsString("select count(1) from Hbatchprocess where BatchProcessName='$ImportName'");
				if($n>0){
					$ImportName=$ImportName."(".($n+1).")";
					$Out="NewName~";
				}
			}while($n>0);
			
			if($Out!="OK~")
				$Out=$Out.$ImportName;
			
			$sql="INSERT INTO Hbatchprocess(BatchProcessName,CDT,Creator_Id,SessionID,ClientIP,BatchItem,BatchComment,StartDT,EndDT,BatchState,CompletedCount) ".
				"select '$ImportName',ImportCDT,$LReseller_Id,'$SessionId',INET_ATON('$LClientIP'),ImportType,".
				($State=="canceled"?
					"'Process canceled InProgress. Only $UserCount users imported.'":
					"'$UserCount users successfully Imported.'"
				).
				",StartDT,now(),'Done','$UserCount' from ".
				"deltasib_tmp.ImportSummary where Import_Id=$Import_Id";
			$BatchProcess_Id=DBInsert($sql);
			DSDebug(2,"sql=$sql\nQueryResult=$BatchProcess_Id");

			if(!$BatchProcess_Id)
				ExitError("~نمی توان نشست عملیات گروهی را ایجاد کرد");
			
			DBUpdate("Lock Table Hbatchprocess_users write,deltasib_tmp.Import$Import_Id read");//this guarantee all the records related to this batch process will be contiguous

			$sql="INSERT INTO Hbatchprocess_users(BatchProcess_Id,User_Id,BatchItemState,BatchItemDT) ".
				"select $BatchProcess_Id,User_Id,'Done',ImportDT from deltasib_tmp.Import$Import_Id  where ParseResult='Imported' order by User_Id asc";
			$res=DBUpdate($sql);

			DBUpdate("UnLock Tables");

			$FromIndex=DBSelectAsString("SELECT MIN(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");
			$ToIndex=DBSelectAsString("SELECT MAX(User_Index) from Hbatchprocess_users where BatchProcess_Id=$BatchProcess_Id");

			if(($ToIndex-$FromIndex+1)!=$res)
				ExitError("خطای همزمان رخ داده است");

			DSDebug(0,"\n**********************************************************************************\nsql=$sql\nQueryResult=$res\nMin=$FromIndex\nMax=$ToIndex");

			DBUpdate("update Hbatchprocess set From_User_Index=$FromIndex,To_User_Index=$ToIndex where BatchProcess_Id=$BatchProcess_Id");
			
			DBUpdate("Update deltasib_tmp.ImportSummary set ImportState='Done' where Import_Id=$Import_Id");
		}
		echo $Out;
	break;
    case "SelectReseller":
		require_once('../../lib/connector/options_connector.php');
		$options = new SelectOptionsConnector($mysqli,"MySQLi");
		$sql="Select 0 As Reseller_Id,'-- لطفا از لیست انتخاب کنید --' As ResellerName union ".
			"(SELECT Reseller_Id,ResellerName From Hreseller where ISEnable='Yes' and ISOperator='No' order by ResellerName Asc)";
		$options->render_sql($sql,"","Reseller_Id,ResellerName","","");
	break;   
    case "SelectVisp":
		require_once('../../lib/connector/options_connector.php');
		$options = new SelectOptionsConnector($mysqli,"MySQLi");
		$sql="Select 0 As Visp_Id,".
			"if(count(1)=0,'-- No Enabled Visp --','-- لطفا از لیست انتخاب کنید --') As VispName ".
			"From Hvisp where ISEnable='Yes' union ".
			"(SELECT Visp_Id,VispName From Hvisp where ISEnable='Yes' order by VispName Asc)";
		$options->render_sql($sql,"","Visp_Id,VispName","","");
        break;
    case "SelectCenter":
		require_once('../../lib/connector/options_connector.php');
		$options = new SelectOptionsConnector($mysqli,"MySQLi");
		$sql="Select 0 As Center_Id,".
			"if(count(1)=0,'-- No Enabled Center --','-- لطفا از لیست انتخاب کنید --') As CenterName ".
			"From Hcenter where ISEnable='Yes' union ".
			"(SELECT Center_Id,CenterName From Hcenter where ISEnable='Yes' order by CenterName Asc)";
		$options->render_sql($sql,"","Center_Id,CenterName","","");
    break;
    case "SelectSupporter":
		require_once('../../lib/connector/options_connector.php');
		$options = new SelectOptionsConnector($mysqli,"MySQLi");
		$sql="Select 0 As Supporter_Id,".
			"if(count(1)=0,'-- No Enabled Supporter --','-- لطفا از لیست انتخاب کنید --') As SupporterName ".
			"From Hsupporter where ISEnable='Yes' union ".
			"(SELECT Supporter_Id,SupporterName From Hsupporter where ISEnable='Yes' order by SupporterName Asc)";
		$options->render_sql($sql,"","Supporter_Id,SupporterName","","");
    break;
    case "SelectStatus":
		require_once('../../lib/connector/options_connector.php');
		$options = new SelectOptionsConnector($mysqli,"MySQLi");
		$sql="Select 0 As Status_Id,".
			"if(count(1)=0,'-- No Initial Status --','-- لطفا از لیست انتخاب کنید --') As StatusName ".
			"From Hstatus where InitialStatus='Yes' union ".
			"(SELECT Status_Id,StatusName From Hstatus where ISEnable='Yes' and InitialStatus='Yes' order by StatusName Asc)";
		$options->render_sql($sql,"","Status_Id,StatusName","","");
    break;
	case "GetHintFile":
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; charset=utf-8; char=utf-8; filename="Hint.txt";');
		$Arr=Array(
			"Username",
			"Pass",
			"Name",
			"Family",
			"FatherName",
			"NationalCode",
			"Nationality",
			"BirthDate",
			"AdslPhone",
			"Phone",
			"Mobile",
			"Comment",
			"Email",
			"PayBalance",
			//"ServiceName",
			"StatusName",
			"ResellerName",
			"VispName",
			"CenterName",
			"SupporterName",
			"NOE",
			"Address",
			"Organization",
			"CompanyRegistryCode",
			"CompanyEconomyCode",
			"CompanyNationalCode",
			"ExpirationDate"
			);
		$f = fopen('php://output', 'w');
		fputs($f, "- First row MUST have column Name\r\n- Available Columns are:\r\n  ".implode(", ",$Arr)."\r\n- File must contain At least one column as UserName. Other columns are OPTIONAL\r\n- You can use Tab(\\t) character or Comma(,) character as field delimiter.");
	break;
	case "UploadFile":
		// print_r("<SCRIPT>parent.myCallBack(true, 'filename.rar', '234123');</SCRIPT>");
				
		if(!is_dir("/tmp/dsimport"))
			mkdir("/tmp/dsimport");
		
		if ( @$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
			if(isset($_FILES["file"])){
				$filename =mysqli_real_escape_string($mysqli,$_FILES["file"]["name"]);
				
				$ServerFileName='_Import_'.GenerateRandomString(10).date("YmdHis");
				$FileFullPath="/tmp/dsimport/".$ServerFileName;
				
				DSDebug(1,' check file_exists '.$FileFullPath);
				if(move_uploaded_file($_FILES["file"]["tmp_name"],$FileFullPath)){
					
					$FileEncodingTmp=shell_exec("file -bi $FileFullPath");
					if(strpos($FileEncodingTmp,"text")===false){
						$tmp=explode(" ",$FileEncodingTmp);
						print_r("{state: false, extra:alert('خطا.\\nمحتوای فایل شما `".addslashes($tmp[0])."` است\\nفقط می توان فایل با فرمت txt آپلود کرد')}");
						unlink($FileFullPath);
					}
					elseif(strpos($FileEncodingTmp,"charset=utf-8")===false)
						print_r("{state: true, name:'".$ServerFileName."', extra:dhtmlx.message({text:'با موفقیت آپلود شد<br/><span style=\"color:limegreen;font-weight:bold\">وجود ندارد UTF8 در فایل شما کاراکتر  </span>',expire:10000})}");
					else
						print_r("{state: true, name:'".$ServerFileName."', extra:dhtmlx.message({text:'Upload OK.<br/><span style=\"color:blue;font-weight:bold\">Your file encoding is UTF-8 and also has UTF8 characters.</span>',expire:10000})}");
				}
				else
					print_r("{state: false, extra:alert('Upload failed.\\nCheck file size limit.')}");
					
			}
			else print_r("{state: false, extra:alert('Upload failed. Bad request.')}");
			DSDebug(1,"Request File Upload :\n".DSPrintArray($_FILES));
		}
	break;
	case "DeleteSelected":
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		$RowId=Get_Input('GET','DB','RowId','INT',1,4294967295,0,0);
		$sql="delete from deltasib_tmp.Import$Import_Id where User_Import_Id=$RowId";
		$res=DBUpdate($sql);
		echo "OK~$res~".DBSelectAsString("select count(1) from deltasib_tmp.Import$Import_Id");
	break;
	case "DeleteErrors":
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		$sql="delete from deltasib_tmp.Import$Import_Id where ParseResult='Error'";
		$res=DBUpdate($sql);
		echo "OK~$res~".DBSelectAsString("select count(1) from deltasib_tmp.Import$Import_Id");
	break;
	case "SaveToFile":
		$Import_Id=Get_Input('GET','DB','id','INT',1,4294967295,0,0);
		$sql="Select User_Import_Id as Row,User_Id,Username,Pass,ResellerName,VispName,CenterName,SupporterName,StatusName,ParseResult ".
			"from deltasib_tmp.Import$Import_Id order by User_Import_Id asc";
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; charset=utf-8; filename="Users.csv";');
		$res = $conn->sql->query($sql);
		$data =  $conn->sql->get_next($res);
		$f = fopen('php://output', 'w');
			foreach ($data as $key=>$Value)
				$Arr[$key]=$key;		
			fputcsv($f, $Arr, ',');
		while($data){
			foreach ($data as $key=>$Value)
				$Arr[$key]=mysqli_real_escape_string($mysqli,$data[$key]);		
			$data =  $conn->sql->get_next($res);
			fputcsv($f, $Arr, ',');
		}
		fclose($f);
	break;
	default :
		echo "~Unknown Request";
		
}//switch ($act)
} catch (Exception $e) {
ExitError($e->getMessage());
}
?>
