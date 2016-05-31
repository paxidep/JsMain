<?php

ini_set("max_execution_time","0");

//include('connect.inc');

function connect_db()
{
	$db=mysql_connect(MysqlDbConstants::$master[HOST],MysqlDbConstants::$master[USER],MysqlDbConstants::$master[PASS]) or die("no conn".mysql_error_js());
	@mysql_select_db("newjs",$db);
	return $db;
}

function label_select($columnname,$value)
{
        $sql = "select SQL_CACHE LABEL from $columnname WHERE VALUE='$value'";
        $res = mysql_query_decide($sql) or die("$sql".mysql_error_js());
        $myrow= mysql_fetch_row($res);
        return $myrow;
                                                                                                                            
}

$db=connect_db();

$j = 0;
$ts = time();
$end_dt=date("Y-m-d H:i:s",$ts);
$ts-=45*24*60*60;
$start_dt = date("Y-m-d H:i:s",$ts);

mysql_query_decide("drop table if exists CONTACT_CASTE_RANKING") or die(mysql_error_js());

$sql_createtbl="CREATE TABLE `CONTACT_CASTE_RANKING` (`ID` MEDIUMINT NOT NULL AUTO_INCREMENT ,`PARENT_CASTE` VARCHAR( 100 ) NOT NULL ,`RELATED_CASTE` VARCHAR( 100 ) NOT NULL ,`COUNT` MEDIUMINT NOT NULL ,`PERCENTAGE` DOUBLE NOT NULL ,PRIMARY KEY ( `ID` ))";
mysql_query_decide($sql_createtbl) or die(mysql_error_js());


$sql_caste = "SELECT VALUE , ISALL FROM newjs.CASTE";
$res_caste = mysql_query_decide($sql_caste,$db) or die("$sql_caste".mysql_error_js());
while($row = mysql_fetch_array($res_caste))
{
	if ($row['ISALL'] == 'Y')
	{
		$isall[] = $row['VALUE'];
	}
	else
		$castearr[] = $row['VALUE'];
}

$isall_caste=implode("','",$isall);
$caste_cnt = count($castearr);

$sql_mtongue = "SELECT VALUE FROM newjs.MTONGUE";
$res_mtongue = mysql_query_decide($sql_mtongue,$db) or die("$sql_mtongue".mysql_error_js());
while($row_mtongue = mysql_fetch_array($res_mtongue))
{
	$mtonguearr[] = $row_mtongue['VALUE'];
}
$mtongue_cnt = count ($mtonguearr);

for ($i = 0;$i < $caste_cnt;$i++)
//for ($i = 0;$i < 15;$i++)
{
	$sql = "SELECT PROFILEID FROM newjs.SEARCH_MALE WHERE CASTE = '$castearr[$i]' AND LAST_LOGIN_DT BETWEEN '$start_dt' AND '$end_dt'";
	//$sql = "SELECT PROFILEID FROM newjs.JPROFILE WHERE CASTE = '$castearr[$i]' AND LAST_LOGIN_DT BETWEEN '$start_dt' AND '$end_dt'";

	$res = mysql_query_decide($sql) or die("Error while retrieving data from newjs.JPROFILE".mysql_error_js());
	while($row=mysql_fetch_array($res))
	{
		$pid[] = $row['PROFILEID'];
	}
	if (is_array($pid))
		$pid_str = implode(",",$pid);
	if ($pid_str)
	{
		$sql_contact = "SELECT COUNT( * ) AS cnt, SENDER FROM newjs.CONTACTS WHERE SENDER IN ($pid_str) GROUP BY SENDER ORDER BY cnt ASC";
		$res_contact = mysql_query_decide($sql_contact,$db) or die("$sql_contact".mysql_error_js());
		while($row_contact = mysql_fetch_array($res_contact))
		{
			$senders[]=$row_contact['SENDER'];
		}
	}
	$sender_cnt = count($senders);
	if (is_array($senders))
	{
		$exclude = ceil((10 * $sender_cnt)/100);
	}
	$exc_count = $sender_cnt - $exclude;
	for ($j =0;$j < $exc_count;$j++)
	{
		$sender_list.="'".$senders[$j]."'".",";
	}
	$sender_list=substr($sender_list,0,-1);
	if ($sender_list)
	{
		$c = 0;
		$sql1 = "SELECT CASTE, count(*) AS cnt FROM newjs.CONTACTS c,newjs.TEMP_CASTE_MTONGUE_PID j where c.RECEIVER = j.PROFILEID and c.SENDER IN ($sender_list)  AND j.CASTE NOT IN ('$isall_caste') GROUP BY j.CASTE ORDER BY cnt DESC LIMIT 10";
		$res1 = mysql_query_decide($sql1) or die("$sql1".mysql_error_js());
		while($row1 = mysql_fetch_array($res1))
		{

			$finalcount[$c]['COUNT']= $row1['cnt'];
			
			$top_ten_caste[] = $row1['CASTE'];
			//$mtongue_val=label_select('MTONGUE',$row1['MTONGUE']);
			//$finalcount[$c]['MTONGUE']=$mtongue_val[0];
			$caste_val = label_select('CASTE',$row1['CASTE']);
			$finalcount[$c]['CASTE']= $caste_val[0];
			$total+= $row1['cnt'];
			$c++;
		}
		$pref_caste = implode(",",$top_ten_caste);
		$final_cnt = count($finalcount);
		for ($x =0;$x < $final_cnt;$x++)
		{
			if ($total)
			{
				$count = $finalcount[$x]['COUNT'];
				$caste = $finalcount[$x]['CASTE'];
				$per = ($count/$total) * 100;
				$per = round($per,2);
			}
			$castelabel = label_select('CASTE',$castearr[$i]);
			$sql_ins = "INSERT INTO newjs.CONTACT_CASTE_RANKING VALUES ('','$castelabel[0]','$caste','$count','$per')";
			mysql_query_decide($sql_ins) or die("$sql_ins".mysql_error_js());

			$m = 0;
			
			$sql_mtongue = "SELECT COUNT(*) AS cnt, j.MTONGUE, j.CASTE FROM newjs.CONTACTS c, newjs.TEMP_CASTE_MTONGUE_PID j where c.RECEIVER = j.PROFILEID and c.SENDER IN ($sender_list) AND j.CASTE = '$top_ten_caste[$x]' GROUP BY j.MTONGUE ORDER BY cnt DESC LIMIT 10";
			$res_mtongue = mysql_query_decide($sql_mtongue) or die("$sql_mtongue".mysql_error_js());
                	while($row_mtongue=mysql_fetch_array($res_mtongue))
			{
				$mtongue_caste[$x][$m]['CNT'] =  $row_mtongue['cnt'];
				$mtongue_val=label_select('MTONGUE',$row_mtongue['MTONGUE']);
				//$mtongue_caste[$x][$m]['MTONGUE']=$mtongue_val[0];
				$mtongue_caste[$x][$m]['CASTE-MTONGUE'] = $caste." - ".$mtongue_val[0];
				$total1[$x]+= $row_mtongue['cnt'];
				$m++;
			}

		}
		for ($x =0;$x < $final_cnt;$x++)
		{
			$cnt = count($mtongue_caste[$x]);
			for ($m = 0;$m < $cnt;$m++)
			{
				if ($total1[$x])
				{
					$CNT = $mtongue_caste[$x][$m]['CNT'];
					$percent = ($CNT/$total1[$x]) * 100;
                                	$percent = round($percent,2);
				}
				$association = $mtongue_caste[$x][$m]['CASTE-MTONGUE'];
				$sql_ins1 = "INSERT INTO newjs.CONTACT_CASTE_MTONGUE_RANKING VALUES ('','$castelabel[0]','$association','$CNT','$percent')";
				mysql_query_decide($sql_ins1) or die("$sql_ins1".mysql_error_js());
			}

		}
		unset($mtongue_caste);

	}
	unset($mtongue_caste);
	unset($top_ten_caste);
	unset($pid_str);
        unset($pid);
        unset($senders);
        unset($sender_list);
	unset($exclude);
	unset($finalcount);
	unset($total);
	unset($per);
}
?>