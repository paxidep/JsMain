<?php 
  $curFilePath = dirname(__FILE__)."/"; 
 include_once("/usr/local/scripts/DocRoot.php");

	ini_set("max_execution_time","0");
	chdir("$docRoot/crontabs/crm");
	include ("../connect.inc");
	include_once("../comfunc.inc");
	include("allocate_functions.php");
	include($_SERVER['DOCUMENT_ROOT']."/profile/comfunc.inc");

	include($_SERVER['DOCUMENT_ROOT']."/classes/Memcache.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/classes/globalVariables.Class.php");
	include($_SERVER['DOCUMENT_ROOT']."/classes/Mysql.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/classes/Jpartner.class.php");

	$SITE_URL="http://www.jeevansathi.com";
	//include ("connect.inc");
	$mysqlObj=new Mysql;
	$jpartnerObj=new Jpartner;

	$db = connect_db();

	for($i=0;$i<$noOfActiveServers;$i++)
	{
		$myDbName=$activeServers[$i];
		$myDbArray[$myDbName]=$mysqlObj->connect("$myDbName");
	}

	$ts = time();
	$ts -= 30*24*60*60;
	$last_day = date("Y-m-d",$ts);

	//finding south communities.
	/*$sql_mt = "SELECT * FROM incentive.REGION_MTONGUE_MAPPING WHERE REGION='S'";
	$res_mt = mysql_query($sql_mt,$db) or die($sql_mt.mysql_error($db));
	while($row_mt = mysql_fetch_array($res_mt))
		$south_arr = explode(",",$row_mt['MTONGUE']);*/

	//cities under south region
	$sql_mt = "SELECT VALUE FROM incentive.BRANCH_CITY WHERE IN_REGION='S'";
        $res_mt = mysql_query($sql_mt,$db) or die($sql_mt.mysql_error($db));
        while($row_mt = mysql_fetch_array($res_mt))
                $south_arr[] = $row_mt['VALUE'];
	
	//Delhi-NCR
	$dncr_arr = array('DE00','UP25','HA03','HA02','UP12');

	//findng cities which fall under PUNE branch.
        $sql_city = "SELECT VALUE FROM incentive.BRANCH_CITY WHERE PRIORITY='MH08'";
        $res_city = mysql_query($sql_city,$db) or die($sql_city.mysql_error($db));
        while($row_city = mysql_fetch_array($res_city))
                $pune_city_arr[] = $row_city['VALUE'];

	//finding west cities
	$sql_west = "SELECT VALUE FROM incentive.BRANCH_CITY WHERE VALUE LIKE 'GU%' UNION SELECT VALUE FROM incentive.BRANCH_CITY WHERE VALUE LIKE 'MP%' UNION SELECT VALUE FROM incentive.BRANCH_CITY WHERE VALUE LIKE 'MH%'";
	$res_west = mysql_query($sql_west,$db) or die($sql_west.mysql_error($db));
	while($row_west = mysql_fetch_array($res_west))
		$west_city_arr[] = $row_west['VALUE'];
	
	//NRI Profiles
	$nri_arr = array('7','125','126');

	//define header to write into csv file.
	$header="\"PROFILEID\"".","."\"PHONE NO.(1)\"".","."\"PHONE NO.(2)\"".","."\"CITY\"".","."\"PHOTO\"".","."\"CONTACTS INITIATED\"".","."\"CONTACTS ACCEPTED\"".","."\"CONTACTS RECEIVED\"".","."\"ACCEPTANCE RECEIVED\"".","."\"DATE OF BIRTH\"".","."\"POSTEDBY\"".","."\"GENDER\"".","."\"CASTE\"".","."\"COMMUNITY\"".","."\"PROFILELENGTH\"".","."\"DESIRED PARTNER PROFILE\"".","."\"LAST LOGIN DATE\"".","."\"SCORE\"".","."\"ENTRY_DATE\"".","."\"EVER_PAID\"\n";

        $filename1 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_south.txt";
        $filename2 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_dncr.txt";
        $filename3 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_pune.txt";
        $filename4 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_west.txt";
        $filename5 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_roi.txt";
        $filename6 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_failed_payments.txt";
	$filename7 = "$_SERVER[DOCUMENT_ROOT]/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_nri.txt";
	

        $fp1 = fopen($filename1,"w+");
        $fp2 = fopen($filename2,"w+");
        $fp3 = fopen($filename3,"w+");
        $fp4 = fopen($filename4,"w+");
        $fp5 = fopen($filename5,"w+");
        $fp6 = fopen($filename6,"w+");
	$fp7 = fopen($filename7,"w+");

        if(!$fp1 || !$fp2 || !$fp3 || !$fp4 || !$fp5 || !$fp6 || !$fp7)
        {
                die("no file pointer");
        }

        fwrite($fp1,$header);
        fwrite($fp2,$header);
        fwrite($fp3,$header);
        fwrite($fp4,$header);
        fwrite($fp5,$header);
        fwrite($fp6,$header);
	fwrite($fp7,$header);
	
	unset($pidarr);
	unset($pidstr);

	//finding user's who tried payment.
	$sql = "SELECT DISTINCT PROFILEID FROM billing.ORDERS WHERE STATUS='' AND ENTRY_DT>=DATE_SUB(CURDATE(),INTERVAL 1 DAY) UNION SELECT DISTINCT PROFILEID FROM billing.PAYMENT_HITS WHERE ENTRY_DT>=DATE_SUB(CURDATE(),INTERVAL 1 DAY) AND PAGE>1";
	$myres=mysql_query($sql,$db) or die("$sql".mysql_error($db));
	while($myrow=mysql_fetch_array($myres))
	{
		$profileid=$myrow['PROFILEID'];
		if(!profile_allocated($profileid))
		{
			if(check_profile($profileid))
			{
				$failed_payments = 1;
				$myDbName=getProfileDatabaseConnectionName($profileid,'',$mysqlObj);
				$mysqlObj->ping($myDbArray[$myDbName]);
				$myDb=$myDbArray[$myDbName];
				$jpartnerObj->setPROFILEID($profileid);
				if($jpartnerObj->isPartnerProfileExist($myDb,$mysqlObj))
					$DPP=1;
				else
					$DPP=0;
				write_contents_to_file($profileid,"","",$failed_payments,$DPP);
			}
		}
		$pidarr[]=$profileid;
	}
	fclose($fp6);

	// changed by Shiv on 10th April 2008.
	// data minimized to just the required one


	//MTONGE <> 1 condition added to remove Foreign origin profiles.
	//$sql_pid = "SELECT PROFILEID, MTONGUE, SCORE FROM incentive.MAIN_ADMIN_POOL WHERE SCORE >= 250 AND ALLOTMENT_AVAIL='Y' AND TIMES_TRIED<3 AND MTONGUE <> '1'";

	$sql="TRUNCATE TABLE incentive.TEMP_CSV_PROFILES";
	mysql_query($sql,$db) or die("$sql".mysql_error($db));

	$sql="insert into incentive.TEMP_CSV_PROFILES (PROFILEID, SCORE, MTONGUE) select PROFILEID, SCORE, MTONGUE from incentive.MAIN_ADMIN_POOL WHERE SCORE >= 250 AND ALLOTMENT_AVAIL='Y' AND TIMES_TRIED<3 AND MTONGUE <> '1' AND SOURCE<>'OFL_PROF'";

	if(is_array($pidarr))
	{
		$pidstr = @implode("','",$pidarr);
		$sql .= " AND PROFILEID NOT IN ('$pidstr')";
	}
	mysql_query($sql,$db) or die("$sql".mysql_error($db));
	unset($pidarr);
	unset($pidstr);

	minimize_data();

	for($i=0;$i<$noOfActiveServers;$i++)
	{
		$myDbName=$activeServers[$i];
		$myDbArray[$myDbName]=$mysqlObj->connect("$myDbName");
	}

	$sql_pid = "SELECT PROFILEID, SCORE, MTONGUE from incentive.TEMP_CSV_PROFILES where 1";
	$res_pid = mysql_query($sql_pid,$db) or die("$sql_pid".mysql_error($db));
	$row_pid = mysql_fetch_array($res_pid);
	while($row_pid = mysql_fetch_array($res_pid))
	{
		$profileid = $row_pid['PROFILEID'];
		$mtongue = $row_pid['MTONGUE'];
		$score = $row_pid['SCORE'];

		$myDbName=getProfileDatabaseConnectionName($profileid,'',$mysqlObj);
		$mysqlObj->ping($myDbArray[$myDbName]);
		$myDb=$myDbArray[$myDbName];
		$jpartnerObj->setPROFILEID($profileid);
		if($jpartnerObj->isPartnerProfileExist($myDb,$mysqlObj))
			$DPP=1;
		else
			$DPP=0;
		$sql_co = "SELECT COUNTRY_RES from newjs.JPROFILE where PROFILEID='$profileid'";
	        $res_co = mysql_query($sql_co,$db) or die("$sql_co".mysql_error($db));
        	$row_co = mysql_fetch_array($res_co);	
		$country = $row_co['COUNTRY_RES'];
		if(in_array($country,$nri_arr))
			$NRI=1;
		else
			$NRI=0;
		write_contents_to_file($profileid,$mtongue,$score,'',$DPP,'',$NRI);
	}
        fclose($fp1);
        fclose($fp2);
        fclose($fp3);
        fclose($fp4);
        fclose($fp5);
	fclose($fp7);

	$profileid_file1 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_south.txt";
	$profileid_file2 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_dncr.txt";
	$profileid_file3 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_pune.txt";
	$profileid_file4 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_west.txt";
	$profileid_file5 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_roi.txt";
	$profileid_file6 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_failed_payments.txt";
	$profileid_file7 = $SITE_URL."/crm/csv_files/bulk_csv_crm_data_".date('Y-m-d')."_nri.txt";

	$msg="For south communities: ".$profileid_file1;
	$msg.="\nFor delhi - NCR : ".$profileid_file2;
	$msg.="\nFor pune : ".$profileid_file3;
	$msg.="\nFor west : ".$profileid_file4;
	$msg.="\nFor rest of india : ".$profileid_file5;
	$msg.="\nFor failed payments : ".$profileid_file6;
	$msg.="\nFor Nri : ".$profileid_file7;

	$to="anamika.singh@jeevansathi.com,surbhi.aggarwal@naukri.com,mandeep.choudhary@naukri.com,divya.jain@naukri.com,deepak.sharma@naukri.com, samrat.chadha@naukri.com, kamaljeet.singh@naukri.com";
	$bcc="vibhor.garg@jeevansathi.com";
	$sub="Daily CSV for calling";
	$from="From:vibhor.garg@jeevansathi.com";
	$from .= "\r\nBcc:$bcc";

	mail($to,$sub,$msg,$from);
?>
