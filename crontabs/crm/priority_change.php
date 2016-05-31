<?php
/*********************************************************************************************
* FILE NAME   	: priority_change.php
* DESCRIPTION 	: Change the priorities of the online dialing at runtime
* MADE DATE   	: 30 Aug, 2010
* MODIFIED DATE : 12 Apr, 2016
* MADE BY     	: VIBHOR GARG
*********************************************************************************************/
$start = @date('H:i:s');

//Open connection at JSDB
$db_js = mysql_connect("ser2.jeevansathi.jsb9.net","user_dialer","DIALlerr") or die("Unable to connect to js server at".$start);
$db_js_157 = mysql_connect("localhost:/tmp/mysql_06.sock","user_sel","CLDLRTa9") or die("Unable to connect to js server".$start);

//Compute all online users
$pro_array = array();
$sql1= "SELECT userID FROM userplane.recentusers";
$res1=mysql_query($sql1,$db_js) or die($sql1.mysql_error($db_js));
while($myrow1 = mysql_fetch_array($res1))
{
        $pro_array[] = $myrow1["userID"];
	$online_array[] = $myrow1["userID"];
}

//Remove profiles who are online but already priortized
$last_pro_array = array();
if($start != '00:00:00')
{
	$sql11= "SELECT userID FROM test.last_recentusers";
	$res11=	mysql_query($sql11,$db_js_157) or die($sql11.mysql_error($db_js_157));
	while($myrow1 = mysql_fetch_array($res11))
        	$last_pro_array[] = $myrow1["userID"];
}

$sql12= "TRUNCATE test.last_recentusers";
$res12=	mysql_query($sql12,$db_js_157) or die($sql12.mysql_error($db_js_157));

for($r=0;$r<count($pro_array);$r++)
{
	$sql13= "INSERT IGNORE INTO test.last_recentusers VALUES ($pro_array[$r])";
	$res13=	mysql_query($sql13,$db_js_157) or die($sql13.mysql_error($db_js_157));
}

//Compute users who tried to pay in last one hour
$pro_array2 = array();
$sql2= "SELECT PROFILEID FROM billing.PAYMENT_HITS WHERE ENTRY_DT>DATE_SUB(now(),INTERVAL 1 HOUR) AND PROFILEID !=0";
$res2=mysql_query($sql2,$db_js) or die($sql2.mysql_error($db_js));
while($myrow2 = mysql_fetch_array($res2))
{
	$pro_array2[] = $myrow2["PROFILEID"];
	$pro_array[] = $myrow2["PROFILEID"];
	$online_array[] = $myrow2["PROFILEID"];
}

for($d=0;$d<count($pro_array2);$d++)
{
        $sql13= "INSERT IGNORE INTO test.last_recentusers VALUES ($pro_array2[$d])";
        $res13= mysql_query($sql13,$db_js_157) or die($sql13.mysql_error($db_js_157));
}

$pro_array = array_diff($pro_array,$last_pro_array);
$pro_array = array_unique($pro_array);
$pro_str = @implode("','",$pro_array);

//Compute all the active campaigns
/*$sqlc= "SELECT CAMPAIGN FROM incentive.CAMPAIGN WHERE ACTIVE = 'Y' AND CAMPAIGN!='PUNE_JS'";
$resc=mysql_query($sqlc,$db_js) or die($sqlc.mysql_error($db_js));
while($myrowc = mysql_fetch_array($resc))
	$camp_array[] = $myrowc["CAMPAIGN"];*/
$camp_array = array("JS_NCRNEW","MAH_JSNEW");

//Compute Suffix for active leadids
/*$sql_lf="SELECT LEAD_ID_SUFFIX FROM incentive.LARGE_FILE ORDER BY ENTRY_DT DESC LIMIT 1";
$res_lf=mysql_query($sql_lf,$db_js) or die($sql_lf.mysql_error($db_js));
$row_lf=mysql_fetch_assoc($res_lf);
$suffix = $row_lf['LEAD_ID_SUFFIX'];*/
$suffix = '070116';

if(count($camp_array)>0)
{
	for($i=0;$i<count($camp_array);$i++)
	{
		$campaign_name = $camp_array[$i];
		
		//Connection at DialerDB
		$db_dialer = mssql_connect("dialer.infoedge.com","online","jeev@nsathi@123") or die("Unable to connect to dialer server");

		//Part-1 : Priortization
		$cnt1=0;
		if($campaign_name == 'JS_RENEWAL')
			 $squery1 = "SELECT easycode,old_priority,PROFILEID,AGENT,DISCOUNT_PERCENT,SCORE FROM easy.dbo.ct_$campaign_name where Lead_Id IN ('renewal$suffix') AND PROFILEID IN ('$pro_str') AND Dial_Status!=0";
		else
			$squery1 = "SELECT easycode,old_priority,PROFILEID,AGENT,VD_PERCENT,SCORE FROM easy.dbo.ct_$campaign_name where Lead_Id IN ('noida$suffix','mumbai$suffix','delhi$suffix') AND PROFILEID IN ('$pro_str') AND Dial_Status!=0";
		$sresult1 = mssql_query($squery1,$db_dialer) or logerror($squery1,$db_dialer);
		while($srow1 = mssql_fetch_array($sresult1))
		{
			//$today = @date("Y-m-d",time());//When server is on IST
			$today = @date("Y-m-d",time()+9.5*3600);//When server is on EST
			$ecode = $srow1["easycode"];
			$opriority = $srow1["old_priority"];
			$profileid = $srow1["PROFILEID"];
			$allocated = trim($srow1["AGENT"]);
			if($campaign_name == 'JS_RENEWAL')
				$discount = trim($srow1["DISCOUNT_PERCENT"]);
			else
				$discount = trim($srow1["VD_PERCENT"]);
			$analytic_score = $srow1["SCORE"];
			/*Check weather this profiled dialed today or not*/
			$dialed_today = 0;
		 	$squery2 = "select segment.start_time from thread with (nolock) LEFT JOIN segment  with (nolock) ON segment.thread = thread.code LEFT JOIN data_context with (nolock) ON data_context.code = thread.data_context where data_context.contact=$ecode";
                        $sresult2 = mssql_query($squery2,$db_dialer) or logerror($squery2,$db_dialer);
                        while($srow2 = mssql_fetch_assoc($sresult2))
                        {
                                if($srow2["start_time"])
				{
                                        //$dial_date = @date("Y-m-d",@strtotime($srow2["start_time"]));//When server is on IST
					$dial_date = @date("Y-m-d",@strtotime($srow2["start_time"])+9.5*3600);//When server is on EST
				}
				if($dial_date>=$today)
					$dialed_today = 1;
                        }
			/*end*/
			$npriority = '';
			$ignore = 0;
			$query2 = "";
			$query1 = "";
			$query = "";
			if(in_array($profileid,$pro_array2))//Tried to pay in last one hour
			{
				if($allocated)//Allocated to agent
				{
					$cnt1++;
					$npriority='9';
					if($dialed_today)
                                        	$query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
					else
						$query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate(),lastonlinepriority='$npriority',lastpriortizationt=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
					$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='1' FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE status=0 and code='$ecode' and priority!='10' and Dial_Status='2'";
					$query = "UPDATE easy.dbo.ph_contact SET priority = '$npriority' WHERE code='$ecode' AND status=0 and priority!='10'";
					$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$npriority','1',now(),'P','$campaign_name','M_A1')";
				}
				else
				{
					$npriority='0';
					if($dialed_today)
                                                $query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
                                        else
                                                $query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate(),lastonlinepriority='$npriority',lastpriortizationt=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
					$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='2' FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE status=0 and code='$ecode' and priority!='10' and Dial_Status='1'";
					$query = "UPDATE easy.dbo.ph_contact SET priority = '$npriority' WHERE code='$ecode' AND status=0 and priority!='10'";
					$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$npriority','2',now(),'P','$campaign_name','M_A0')";
				}
			}
			else
			{
				$cnt1++;
				if($allocated)//Allocated to agent
				{
					if($discount)//Eligible for VD
		                                $npriority = '9';
					else
					{
						if($analytic_score>=81 && $analytic_score<=100)
                                                        $npriority = '8';
						elseif($analytic_score>=56 && $analytic_score<=80)
                                                        $npriority = '7';
                                                elseif($analytic_score>=1 && $analytic_score<=55)
                                                        $npriority = '6';
						else
							$ignore = 1;
					}
					if($dialed_today)
                                                $query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
                                        else
                                                $query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate(),lastonlinepriority='$npriority',lastpriortizationt=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
					$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='1',LAST_LOGIN_DATE=getdate() FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE status=0 and code='$ecode' and priority!='10' and Dial_Status='2'";
					$query = "UPDATE easy.dbo.ph_contact SET priority = '$npriority' WHERE code='$ecode' AND status=0 and priority!='10'";
					$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$npriority','1',now(),'P','$campaign_name','O_A1')";
				}
				else
				{
					if($discount)//Eligible for VD
					{
						if($analytic_score>=81 && $analytic_score<=100)
                                                        $npriority = '9';
						elseif($analytic_score>=56 && $analytic_score<=80)
							$npriority = '8';
						elseif($analytic_score>=1 && $analytic_score<=55)
							$npriority = '7';
						else
							$ignore = 1;
					}
                                        else
                                        {
                                                if($analytic_score>=91 && $analytic_score<=100)
                                                        $npriority = '9';
                                                elseif($analytic_score>=81 && $analytic_score<=90)
                                                        $npriority = '8';
						elseif($analytic_score>=56 && $analytic_score<=80)
                                                        $npriority = '7';
                                                elseif($analytic_score>=31 && $analytic_score<=55)
                                                        $npriority = '6';
                                                else
                                                        $ignore = 1;
                                        }
					if($dialed_today)
                                                $query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
                                        else
                                                $query2 = "UPDATE easy.dbo.ct_$campaign_name SET LAST_LOGIN_DATE=getdate(),lastonlinepriority='$npriority',lastpriortizationt=getdate() FROM easy.dbo.ct_$campaign_name where easycode='$ecode'";
					$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='1',LAST_LOGIN_DATE=getdate() FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE status=0 and code='$ecode' and priority!='10' and Dial_Status='2'";
					$query = "UPDATE easy.dbo.ph_contact SET priority = '$npriority' WHERE code='$ecode' AND status=0 and priority!='10'";
					$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$npriority','1',now(),'P','$campaign_name','O_A0')";
				}
			}
			if(!$ignore)
			{
				if(!$db_dialer)
					$db_dialer = mssql_connect("dialer.infoedge.com","online","jeev@nsathi@123");
				if($query2!="")
				{
					//echo $query2;echo "#";
                                	mssql_query($query2,$db_dialer) or logerror($query2,$db_dialer,1);
				}
				if($query1!="")
				{
					//echo $query1;echo "#";
					mssql_query($query1,$db_dialer) or logerror($query1,$db_dialer,1);
				}
				if($query!="")
				{
					//echo $query;echo "#";
					mssql_query($query,$db_dialer) or logerror($query,$db_dialer,1);
				}
				if($log_query!="")
                                {
                                        //echo $log_query;echo "#";
                                        mysql_query($log_query,$db_js_157) or die($log_query.mysql_error($db_js_157));
                                }
			}
		}
		echo "*****************************************";
		echo "P#".$cnt1;
		echo "*****************************************";
		echo "\n";

		//Part-2.1 : Depriortization
		$cnt2=0;
		$dep_array = array();
		//if(isset($opriority))//Previously online
		{
			$squery1 = "SELECT easycode,old_priority,PROFILEID,easy.dbo.ct_$campaign_name.AGENT,SCORE,priority FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE Lead_Id IN ('noida$suffix','mumbai$suffix','delhi$suffix','renewal$suffix') AND status=0 AND priority BETWEEN 6 and 9 AND Dial_Status!=0";
	                $sresult1 = mssql_query($squery1,$db_dialer) or logerror($squery1,$db_dialer);
        	        while($srow1 = mssql_fetch_array($sresult1))
                	{
                        	$ecode = $srow1["easycode"];
	                        $opriority = $srow1["old_priority"];
				$profileid = $srow1["PROFILEID"];
                	        $allocated = trim($srow1["AGENT"]);
				$npriority = $srow1["priority"];
				if($opriority>=0 && !in_array($profileid,$online_array) && $opriority!=$npriority)
				{
					$cnt2++;
					$ds = 0 ;
					if($allocated)
					{
						$ds = 1;
						$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='2' WHERE easycode='$ecode' and Dial_Status='1'";
						mssql_query($query1,$db_dialer) or logerror($query1,$db_dialer,1);
						$query = "UPDATE easy.dbo.ph_contact SET priority = '$opriority' WHERE code='$ecode'";
						mssql_query($query,$db_dialer) or logerror($query,$db_dialer,1);
					}
					else
					{
						$query = "UPDATE easy.dbo.ph_contact SET priority = '$opriority' WHERE code='$ecode'";
						mssql_query($query,$db_dialer) or logerror($query,$db_dialer,1);
					}
					$dep_array[] = $profileid;
					if($ds)
						$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$opriority','2',now(),'D','$campaign_name','PO_A1')";
					else
						$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$opriority','',now(),'D','$campaign_name','PO_A0')";
					mysql_query($log_query,$db_js_157) or die($log_query.mysql_error($db_js_157));
                        	}
			}
		}
		echo "*****************************************";
                echo "D1#".$cnt2;
                echo "*****************************************";
		echo "\n";

		//Part-2.2 : Depriortization
		$cnt3=0;
		//if(isset($opriority))//Previously visited membership page
		{
			$squery1 = "SELECT easycode,old_priority,PROFILEID,easy.dbo.ct_$campaign_name.AGENT,priority,SCORE FROM easy.dbo.ct_$campaign_name JOIN easy.dbo.ph_contact ON easycode=code WHERE Lead_Id IN ('noida$suffix','mumbai$suffix','delhi$suffix','renewal$suffix') AND status=0 AND Dial_Status=2 AND priority=0 and old_priority!=0";
	                $sresult1 = mssql_query($squery1,$db_dialer) or logerror($squery1,$db_dialer);
        	        while($srow1 = mssql_fetch_array($sresult1))
                	{
                        	$ecode = $srow1["easycode"];
	                        $opriority = $srow1["old_priority"];
				$profileid = $srow1["PROFILEID"];
                	        $allocated = trim($srow1["AGENT"]);
				$npriority = $srow1["priority"];
				if($opriority>=0 && !in_array($profileid,$online_array) && $opriority!=$npriority)
				{
					$cnt3++;
					$query1 = "UPDATE easy.dbo.ct_$campaign_name SET Dial_Status='1' WHERE easycode='$ecode' and Dial_Status='2'";
					mssql_query($query1,$db_dialer) or logerror($query1,$db_dialer,1);
					$query = "UPDATE easy.dbo.ph_contact SET priority = '$opriority' WHERE code='$ecode'";
					mssql_query($query,$db_dialer) or logerror($query,$db_dialer,1);
					if(!in_array($profileid,$dep_array))
					{
						$log_query = "INSERT into test.ONLINE_PRIORITY_LOG (PROFILEID,PRIORITY,DIAL_STATUS,TIME,ACTION,CAMPAIGN,TYPE) VALUES ('$profileid','$opriority','1',now(),'D','$campaign_name','PM_A01')";
	                                        mysql_query($log_query,$db_js_157) or die($log_query.mysql_error($db_js_157));
					}
                        	}
			}
		}
		echo "*****************************************";
                echo "D2#".$cnt3;
                echo "*****************************************";
		echo "\n";
		mssql_close($db_dialer);
	}
	echo "\n"."Thread Started At $start Completed At ".@date('H:i:s')."\n";
}
else
{
	echo $msg = "Not Pass : ".date("Y-m-d H:i:s")."=>".$depriortize."\n";
}
function logError($sql="",$db="",$ms)
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