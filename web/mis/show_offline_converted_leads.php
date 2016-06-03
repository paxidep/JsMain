<?php
/*******************************************************************************************o*
* FILE NAME     : order_conversion.php
* DESCRIPTION   : It provides MIS details for online member conversions.
* CREATION DATE : 4 july, 2005
* CREATED BY    : Aman Sharma
* Copyright  2005, InfoEdge India Pvt. Ltd.
*********************************************************************************************/
                                                                                                                             
                                                                                                                             
include("connect.inc");

$db=$db2=connect_master();

// privilege check Start
$privilage      =getprivilage($cid);
$priv           =explode("+",$privilage);
if(in_array("INBSUP",$priv) || in_array("SLHD",$priv) || in_array("P",$priv) || in_array("MG",$priv))
        $showAll =true;
else
        $showAll =false;
// privilege check ends 

@mysql_select_db("billing",$db);

if($yr1=='' || $mm=='')
	die("Invalid Request");

$day_of_month=$d+1;
if($day_of_month<10)
        $day_of_month="0".$day_of_month;
$st_dt=$yr1."-".$mm."-".$day_of_month;
echo $sel_dt=$yr1."-".$mm."-".$day_of_month;

$sql ="select t2.PROFILEID as pid from incentive.PAYMENT_COLLECT as t1,billing.PURCHASES as t2 where t1.PROFILEID=t2.PROFILEID and t2.STATUS='DONE' and t2.ENTRY_DT >= t1.ENTRY_DT and t1.ENTRY_DT between '$st_dt 00:00:00' and '$st_dt 23:59:59'";
        $res=mysql_query_decide($sql,$db) or die(mysql_error_js());
	 while($row=mysql_fetch_array($res))
         {
		$pidstr.=$row['pid'].',';
				
         }

	$l=strlen($pidstr);
	For($i=0;$i<$l-1;$i++)
	{
	$pid_str.=$pidstr[$i];
	}


	if($ctype=='N')
	{
		$pidstr='';
		$sql1="select PROFILEID from incentive.PAYMENT_COLLECT where ENTRY_DT between '$st_dt 00:00:00' and '$st_dt 23:59:59'";
		if($pid_str!='')
		{
			$sql1.=" and PROFILEID not in($pid_str)";
		}
		else
		{
			$sql1.=" group by PROFILEID";
		}
			
                         $res=mysql_query_decide($sql1,$db) or die(mysql_error_js());
	
			while($row=mysql_fetch_array($res))
         		{
                                        $pidstr.=$row['PROFILEID'].',';
                                                                                                                             
         		}
        $l=strlen($pidstr);
        For($i=0;$i<$l-1;$i++)
        {
        $pid_str.=$pidstr[$i];
        }
		
}

if($pid_str=='')
{
	echo "no result";
}
else
{
	if($showAll)
		$sql="select t1.PROFILEID,t1.USERNAME,t1.CITY_RES,t1.COUNTRY_RES,t2.ALLOTED_TO from newjs.JPROFILE as t1 left join incentive.MAIN_ADMIN as t2 on t1.PROFILEID=t2.PROFILEID where t1.PROFILEID in($pid_str) ";
	else
		$sql="select t1.PROFILEID,t1.USERNAME,t1.CITY_RES,t1.COUNTRY_RES,t2.ALLOTED_TO from newjs.JPROFILE as t1,incentive.MAIN_ADMIN as t2 where t1.PROFILEID=t2.PROFILEID and t1.PROFILEID in($pid_str) and t2.ALLOTED_TO!=''";

	$res=mysql_query_decide($sql,$db) or die(mysql_error_js());
	 echo"<table rows=2 cols =5 border=1 bordercolor=red width=\"100%\">";
	echo"<tr bgcolor='#CFEAC6'><td><b>SNO</td><td><b>USERNAME</td><td>TYPE</td><td><b>COUNTRY</td><td><b>CITY</td><td><b>ALLOTED_TO</td></tr>";
	$sno=1;
	while($row=mysql_fetch_array($res))
	{
		unset($pt_arr);
		unset($pt_str);
		$pid=$row['PROFILEID'];

		$sql_pt="SELECT DISTINCT(PICKUP_TYPE) from incentive.PAYMENT_COLLECT where PROFILEID='$pid' and ENTRY_DT between '$st_dt 00:00:00' and '$st_dt 23:59:59' ";
		$res_pt=mysql_query_decide($sql_pt,$db) or die(mysql_error_js());

		while($myrow_pt=mysql_fetch_array($res_pt))
		{
			$pt_arr[]=$myrow_pt["PICKUP_TYPE"];
		}
		if(count($pt_arr>0))
		{
			$pt_str=implode(",",$pt_arr);
		}
	
		$uname=$row['USERNAME'];
		$city=$row['CITY_RES'];
		$country=$row['COUNTRY_RES'];
		$alloted=$row['ALLOTED_TO'];
		$country1=label_select(COUNTRY,$country);	
		if($country=='51')
		{
			$city1=label_select(CITY_NEW,$city);
		}
		if($country=='128')
                {
                        $city1=label_select(CITY_NEW,$city);
                }

		echo"<tr bgcolor='#CFEAC6'><td>$sno</td><td>$uname</td><td>$pt_str</td><td>$country1[0]</td><td>$city1[0]</td><td>$alloted</td></tr>";
		$sno++;
	}
echo"</table>";


	
}


?>






