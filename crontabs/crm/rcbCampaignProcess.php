<?php
/*********************************************************************************************
* FILE NAME   	: rcbCampaignProcess.php 
* DESCRIPTION 	: RCM Campaign process
*********************************************************************************************/
$to     ="manoj.rana@naukri.com";
$from   ="From:JeevansathiCrm@jeevansathi.com";

//Connection at JSDB
$db_js = mysql_connect("ser2.jeevansathi.jsb9.net","user_dialer","DIALlerr") or die("Unable to connect to vario server");
$db_master = mysql_connect("master.js.jsb9.net","user","CLDLRTa9") or die("Unable to connect to js server at ".$start);
$db_js_111 = mysql_connect("localhost:/tmp/mysql_06.sock","user_sel","CLDLRTa9") or die("Unable to connect to local server");
$db_dialer = mssql_connect("dialer.infoedge.com","online","jeev@nsathi@123") or die("Unable to connect to dialer server");

include("DialerHandler.class.php");
$dialerHandlerObj =new DialerHandler($db_js, $db_js_111, $db_dialer,$db_master);

/* Config array Start */
$campaignArr 		=array('OB_JS_RCB');
$campaignIdArr		=array('OB_JS_RCB'=>17);
$campaignTableArr       =array('OB_JS_RCB'=>array('SALES_CSV_DATA_RCB'));

foreach($campaignArr as $key=>$campaignName)
{
		$processId	=$campaignIdArr[$campaignName];
		$startID 	=$dialerHandlerObj->getLastHandledID($processId);
                /*if($startDate=='0000-00-00 00:00:00')
                        break;*/
		if(!$startID)
			break;

		$tableArr	=$campaignTableArr[$campaignName];
		foreach($tableArr as $key=>$tableName){
			$profilesArr    =$dialerHandlerObj->getProfilesForCampaign($tableName,'',$campaignName, '','',$startID);
			$totalRecord	=count($profilesArr);
			if($totalRecord>0){
				foreach($profilesArr as $key=>$dataArr){
					if(!isset($startDate))
						$startDate =$dataArr['CSV_ENTRY_DATE'];
					$dataArr =$dialerHandlerObj->formatDataSet($campaignName,$dataArr,$startDate);		
					$endID =$dataArr['ID'];
					unset($dataArr['ID']);
					$dialerHandlerObj->addProfileinCampaign($dataArr, $campaignName);
					if($endID>0)
						$dialerHandlerObj->updateLastHandledID($processId,$endID);
					unset($dataArr);
				}
			}
			unset($profilesArr);
		}
		if(isset($startDate))
			$dialerCampaignReords =$dialerHandlerObj->getCampaignRecordsForDuration($campaignName, $startDate );
		if($totalRecord !=$dialerCampaignReords){
			$sub	="FAILED: Dialer insert for $campaignName";
			$msg	="Campaign Records:".$totalRecord."# Dialer Records Inserted:".$dialerCampaignReords;	
			mail($to,$sub,$msg,$from);
		}
		unset($startDate);
		unset($campaignRecord);
		unset($dialerCampaignReords);
}

?>