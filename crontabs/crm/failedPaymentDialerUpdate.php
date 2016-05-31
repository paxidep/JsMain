<?php
/*********************************************************************************************
* FILE NAME   	: failedPaymentDialerUpdate.php 
* DESCRIPTION 	: Change the Dialer Dial status to 0
* MADE BY     	: MANOJ RANA 
*********************************************************************************************/

//Open connection at JSDB
$db_js = mysql_connect("ser2.jeevansathi.jsb9.net","user_dialer","DIALlerr") or die("Unable to connect to js server at ".$start);
$db_master = mysql_connect("master.js.jsb9.net","user","CLDLRTa9") or die("Unable to connect to js server at ".$start);
$db_js_157 = mysql_connect("localhost:/tmp/mysql_06.sock","user_sel","CLDLRTa9") or die("Unable to connect to js server".$start);

//Connection at DialerDB
$db_dialer = mssql_connect("dialer.infoedge.com","online","jeev@nsathi@123") or die("Unable to connect to dialer server");
$campaignName	='FP_JS';
$action		='FP';
$profilesArr 	=fetchProfiles($db_js);
$profileStr 	=implode(",",$profilesArr);
$dateTime 	=date("Y-m-d H:i:s",time()-22.5*60*60);

if($profileStr!='')
{
	$query1 = "UPDATE easy.dbo.ct_$campaignName SET Dial_Status='0' WHERE Dial_Status=1 AND Login_Timestamp<'$dateTime'";
	mssql_query($query1,$db_dialer) or logerror($query1,$db_dialer,1);

	deleteProfiles($db_master,$profileStr);

        foreach($profilesArr as $key=>$profileid){
                $log_query = "INSERT into test.DIALER_UPDATE_LOG (PROFILEID,CAMPAIGN,UPDATE_STRING,TIME,ACTION) VALUES ('$profileid','$campaignName','DIAL_STATUS=0',now(),'$action')";
                mysql_query($log_query,$db_js_157) or die($log_query.mysql_error($db_js_157));
        }

}

// mail added
$to="manoj.rana@naukri.com";
$sub="Dialer updates of failed payment.";
$from="From:vibhor.garg@jeevansathi.com";
mail($to,$sub,$profileStr,$from);

function fetchProfiles($db_js)
{
        $profileArr =array();
        $sql= "SELECT PROFILEID FROM incentive.SALES_CSV_DATA_FAILED_PAYMENT WHERE DIAL_STATUS=0";
        $res=mysql_query($sql,$db_js) or die($sql.mysql_error($db_js));
        while($myrow = mysql_fetch_array($res))
                $profileArr[] = $myrow["PROFILEID"];
        return $profileArr;
}

function deleteProfiles($db_master,$profiles)
{
	$sql= "delete FROM incentive.SALES_CSV_DATA_FAILED_PAYMENT WHERE DIAL_STATUS=0 AND PROFILEID IN ($profiles)";
        $res=mysql_query($sql,$db_master) or die($sql.mysql_error($db_js));
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
                fwrite($handle,"\n\nQuery : $sql \t Error : " .mssql_get_last_message(). " \t $today");
                fclose($handle);
        }
        else
        {
                echo "The file $filename is not writable";
        }
}
?>