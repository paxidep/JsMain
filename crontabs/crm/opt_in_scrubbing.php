<?php
/*********************************************************************************************
* FILE NAME   	: opt_in_scrubbing.php
* DESCRIPTION 	: Re-start profiles who were marked in DNC but opt-in for calls
* MADE DATE 	: 12 May, 2015
*********************************************************************************************/

//Connection at JSDB
$db_js = mysql_connect("ser2.jeevansathi.jsb9.net","user_dialer","DIALlerr") or die("Unable to connect to vario server");
$db_js_157 = mysql_connect("localhost:/tmp/mysql_06.sock","user_sel","CLDLRTa9") or die("Unable to connect to local server");
//Connection at DialerDB
$db_dialer = mssql_connect("dialer.infoedge.com","online","jeev@nsathi@123") or die("Unable to connect to dialer server");

function compute_dnc_array($db_dialer,$campaign_name)
{
	$dnc_array = array();
	$squery1 = "SELECT PROFILEID FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE status=0 AND Dial_Status='9'";
        $sresult1 = mssql_query($squery1,$db_dialer) or logerror($squery1,$db_dialer);
	while($srow1 = mssql_fetch_array($sresult1))
		$dnc_array[] = $srow1["PROFILEID"];
        return $dnc_array;
}

function compute_opt_in_array($db_js,$dnc_array)
{
	$opt_in_profiles = array(); 
        $profileid_str = implode(",",$dnc_array);
        if($profileid_str!='')
        {       
                $sql_vd="select PROFILEID from newjs.CONSENT_DNC WHERE PROFILEID IN ($profileid_str)";
                $res_vd = mysql_query($sql_vd,$db_js) or die("$sql_vd".mysql_error($db_js));
                while($row_vd = mysql_fetch_array($res_vd))
                        $opt_in_profiles[] = $row_vd["PROFILEID"];
        }
        return $opt_in_profiles;
}

function start_opt_in_profiles($campaign_name,$opt_in_profile,$db_dialer,$db_js_157)
{
	$squery1 = "SELECT easycode,PROFILEID FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE status=0 AND PROFILEID ='$opt_in_profile'";
        $sresult1 = mssql_query($squery1,$db_dialer) or logerror($squery1,$db_dialer);
        while($srow1 = mssql_fetch_array($sresult1))
        {
                $ecode = $srow1["easycode"];
                $proid = $srow1["PROFILEID"];
		if($ecode!='')
		{
			$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='1' WHERE easycode='$ecode'";
			mssql_query($query1,$db_dialer) or logerror($query1,$db_dialer);

			$log_query = "INSERT into test.DIALER_UPDATE_LOG (PROFILEID,CAMPAIGN,UPDATE_STRING,TIME,ACTION) VALUES ('$proid','$campaign_name','DIAL_STATUS=1',now(),'OPTIN')";
			mysql_query($log_query,$db_js_157) or die($log_query.mysql_error($db_js_157));
                }
        }
}

function logerror($sql="",$db="",$ms)
{
        $today=@date("Y-m-d h:m:s");
        $filename="logerror.txt";
        if(is_writable($filename))
        {
                if (!$handle = fopen($filename, 'a'))
                {
                        echo "Cannot open file ($filename)";
                        exit;
                }
                if($ms)
                        fwrite($handle,"\n\nQUERY : $sql \t ERROR : " .mssql_get_last_message(). " \t $today");
                else
                        fwrite($handle,"\n\nQUERY : $sql \t ERROR : " .mysql_error(). " \t $today");
                fclose($handle);
        }
        else
        {
                echo "The file $filename is not writable";
        }
}

// MAH_JSNEW OPT-IN Check
$msg = "Start time #".@date('H:i:s');
$dnc_array = compute_dnc_array($db_dialer,'MAH_JSNEW');
$opt_in_array = compute_opt_in_array($db_js,$dnc_array);
for($i=0;$i<count($opt_in_array);$i++)
	start_opt_in_profiles('MAH_JSNEW',$opt_in_array[$i],$db_dialer,$db_js_157);
unset($dnc_array);
unset($opt_in_array);
$msg.="End time :".@date('H:i:s');
$to="vibhor.garg@jeevansathi.com,manoj.rana@naukri.com";
$sub="Dialer updates for MAH_JSNEW opt-in done.";
$from="From:vibhor.garg@jeevansathi.com";
mail($to,$sub,$msg,$from);


// JS_NCRNEW OPT-IN Check
$msg = "Start time #".@date('H:i:s');
$dnc_array = compute_dnc_array($db_dialer,'JS_NCRNEW');
$opt_in_array = compute_opt_in_array($db_js,$dnc_array);
for($i=0;$i<count($opt_in_array);$i++)
	start_opt_in_profiles('JS_NCRNEW',$opt_in_array[$i],$db_dialer,$db_js_157);
unset($dnc_array);
unset($opt_in_array);
$msg.="End time :".@date('H:i:s');
$to="vibhor.garg@jeevansathi.com,manoj.rana@naukri.com";
$sub="Dialer updates for JS_NCRNEW opt-in done.";
$from="From:vibhor.garg@jeevansathi.com";
mail($to,$sub,$msg,$from);


// Renewal OPT-IN Check
$msg = "Start time #".@date('H:i:s');
$dnc_array = compute_dnc_array($db_dialer,'JS_RENEWAL');
$opt_in_array = compute_opt_in_array($db_js,$dnc_array);
for($i=0;$i<count($opt_in_array);$i++)
        start_opt_in_profiles('JS_RENEWAL',$opt_in_array[$i],$db_dialer,$db_js_157);
unset($dnc_array);
unset($opt_in_array);
$msg.="End time :".@date('H:i:s');
$to="vibhor.garg@jeevansathi.com,manoj.rana@naukri.com";
$sub="Dialer updates for RENEWAL opt-in done.";
$from="From:vibhor.garg@jeevansathi.com";
mail($to,$sub,$msg,$from);

?>