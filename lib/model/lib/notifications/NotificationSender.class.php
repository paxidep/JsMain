<?php

/**
 * Description of GCM
 *
 */

class NotificationSender
{
    function __construct($notificationKey="") 
    {
        if(NotificationEnums::$multiCurlReqConfig["sendMultipleParallelNotification"] == true && in_array($notificationKey, NotificationEnums::$multiCurlReqConfig["notificationKey"])){
            $this->sendMultipleParallelNotification = true;
        }
        else{
            $this->sendMultipleParallelNotification = false;
        }
        $this->notificationEngineFactoryObj = new NotificationEngineFactory($this->sendMultipleParallelNotification);
    }

    /**
     * Sending Push Notification
     */
    public function sendNotifications($profileDetails,$regIds='') 
    {
	$JsMemcacheObj =JsMemcache::getInstance();
	$date =date("Ymd");

    	if(is_array($profileDetails))
    	{
    		$notificationLogObj = new MOBILE_API_NOTIFICATION_LOG;
            $notificationLogAtnObj = new MOBILE_API_NOTIFICATION_LOG_ATN;
            $notificationLogEtnObj = new MOBILE_API_NOTIFICATION_LOG_ETN;
    		foreach($profileDetails as $identifier=>$details)
    		{

                $osType = "";
                if(!in_array($profileDetails[$identifier]["NOTIFICATION_KEY"], NotificationEnums::$loggedOutNotifications)){
                    $profileid = $identifier;
                }
                else{
                    $profileid = $profileDetails[$identifier]["PROFILEID"];
                }
    			if(!isset($details))
    				continue;

                if(!is_array($regIds)){ 
                    /*add notification image support in case logged out notifications or upgrade app is extended for IOS channel*/    
                    if(in_array($profileDetails[$identifier]["NOTIFICATION_KEY"], NotificationEnums::$loggedOutNotifications)){
                       $regIds = $this->getRegistrationIds($identifier,$profileDetails[$identifier]['OS_TYPE'],$profileDetails[$identifier]['NOTIFICATION_KEY'],$profileDetails[$identifier]['REG_ID']); 
                    }
                    else if($details["NOTIFICATION_KEY"] == "UPGRADE_APP"){
                        $regIds = $this->getRegistrationIds($identifier,$profileDetails[$identifier]['OS_TYPE'],$details["NOTIFICATION_KEY"],'',array("andUpdateVersion"=>$details["ANDROID_UPDATE_VERSION"],"curAndMaxVersion"=>$details["CURRENT_ANDROID_MAX_VERSION"]));
                    }
                    else{
                        $regIds = $this->getRegistrationIds($identifier,$profileDetails[$identifier]['OS_TYPE']);
                    }
                }
    			if(is_array($regIds))
    			{
    				if(is_array($regIds[$identifier]["AND"]) && ($profileDetails[$identifier]['OS_TYPE'] == "AND" || $profileDetails[$identifier]['OS_TYPE'] == "ALL" ))
    				{

    					$osType = "AND";
                        if($details['NOTIFICATION_KEY']=='ATN')
                            $notificationLogAtnObj->insert($profileid,$details['NOTIFICATION_KEY'],$details['MSG_ID'],NotificationEnums::$PENDING,$osType);
                        if($details['NOTIFICATION_KEY']=='ETN')
                            $notificationLogEtnObj->insert($profileid,$details['NOTIFICATION_KEY'],$details['MSG_ID'],NotificationEnums::$PENDING,$osType);
                        
    					$notificationLogObj->insert($profileid,$details['NOTIFICATION_KEY'],$details['MSG_ID'],NotificationEnums::$PENDING,$osType);
    					$engineObject = $this->notificationEngineFactoryObj->geNotificationEngineObject('GCM');
                        
                        if($this->sendMultipleParallelNotification == false){
                           $result = $engineObject->sendNotification($regIds[$identifier]["AND"], $details,$profileid);
                        }
                        else{
                            $engineObject->sendMultipleParallelNotification($regIds[$identifier]["AND"], $details,$profileid);
                        }
    				}
    				if(is_array($regIds[$identifier]["IOS"]) && ($profileDetails[$identifier]['OS_TYPE'] == "IOS" || $profileDetails[$identifier]['OS_TYPE'] == "ALL" ) )
                    {

    					$osType = "IOS";
                        //$details['PHOTO_URL'] = 'D'; //Added here so that any image url generated is sent to android and not to IOS
    					if($details['NOTIFICATION_KEY']=='ATN')
                            $notificationLogAtnObj->insert($profileid,$details['NOTIFICATION_KEY'],$details['MSG_ID'],NotificationEnums::$PENDING,$osType);
                        if($details['NOTIFICATION_KEY']=='ETN')
                            $notificationLogEtnObj->insert($profileid,$details['NOTIFICATION_KEY'],$details['MSG_ID'],NotificationEnums::$PENDING,$osType);
                        $notificationLogObj->insert($profileid,$details['NOTIFICATION_KEY'],$details['MSG_ID'],NotificationEnums::$PENDING,$osType);
                        $engineObject = $this->notificationEngineFactoryObj->geNotificationEngineObject($osType);
                        
                        $engineObject->sendNotification($regIds[$identifier]['IOS'], $details,$profileid,$regIds[$identifier]['IOS_NOTIFICATION_IMAGE']);

                    }
    			}
    			// logging of Notification Messages 
    			$key            =$details['NOTIFICATION_KEY'];
    			$msgId          =$details['MSG_ID'];
    			$message        =$details['MESSAGE'];
    			$title          =$details['TITLE'];
    			$notificationMsgLog =new MOBILE_API_NOTIFICATION_MESSAGE_LOG();
    			$notificationMsgLog->insert($key,$msgId,$message,$title);
    			// end
		
			// Redis Tracking for Notification sending
        		$notificationFunction =new NotificationFunctions();
        		$notificationFunction->appNotificationCountCachng($key,'','APP_NOTIFICATION');
		
			// Notification Increment counter fir profile specific using Hash
			if(in_array("$key", NotificationEnums::$timeCriteriaNotification)){	
	        	        $key    ="INST_APP|".$key."|".$date;
	        	        $field  ="PID-".$profileid;
	        	        $JsMemcacheObj->hIncrBy($key,$field,1);
			}
			// end
              
                	unset($regIds);
    		}
            	if($this->sendMultipleParallelNotification == true){
            	    $engineObject = $this->notificationEngineFactoryObj->geNotificationEngineObject('GCM');
            	    $engineObject->executeMultiCurlRequest(true);
            	}
    	}
    }

    public function getRegistrationIds($profileid,$osType,$notificationKey='',$regId="",$params=array())
    {
	$valArr['PROFILEID']=$profileid;
	if($osType != "ALL")
		$valArr['OS_TYPE']=$osType;
	$valArr['NOTIFICATION_STATUS'] = "Y";
    if($regId && $regId != ""){
        $regIdArr = array($profileid=>array($valArr['OS_TYPE']=>array($regId)));
        return $regIdArr;
    }
    else{
        if(array_key_exists($notificationKey, NotificationEnums::$appVersionCheck)){
            $appVersion = NotificationEnums::$appVersionCheck[$notificationKey];
        }
        else{
            $appVersion = NotificationEnums::$appVersionCheck["DEFAULT"];
        }
       	if($osType=='AND' || $osType=='ALL')
            $appVersionAnd =$appVersion['AND'];
        if($osType=='IOS' || $osType=='ALL')
            $appVersionIos =$appVersion['IOS'];

    	$registrationIdObj = new MOBILE_API_REGISTRATION_ID('newjs_masterRep');
    	$registrationIdData = $registrationIdObj->getArray($valArr,'','','*');
        
    	if(is_array($registrationIdData))
    	{
            $iosCounter = 0;
    		foreach($registrationIdData as $k=>$v){
    			$os_type 	=$v['OS_TYPE'];
    			$appVersion 	=$v['APP_VERSION'];
                if($notificationKey == "UPGRADE_APP"){
                    if($os_type=='AND' && $appVersion>=$appVersionAnd && $appVersion<$params["andUpdateVersion"])
                        $regIdArr[$v['PROFILEID']][$v['OS_TYPE']][]=$v['REG_ID'];
                }
    			elseif(($os_type=='AND' && $appVersion>=$appVersionAnd) || ($os_type=='IOS' && $appVersion>=$appVersionIos)){
                    if($os_type == 'IOS'){
                         $regIdArr[$v['PROFILEID']][$v['OS_TYPE']][$iosCounter]=$v['REG_ID'];
                        if($appVersion>=NotificationEnums::$IosNotificationImageCheck['APP_VERSION'] && $v['OS_VERSION']>=NotificationEnums::$IosNotificationImageCheck['OS_VERSION']){
                            $regIdArr[$v['PROFILEID']]['IOS_NOTIFICATION_IMAGE'][$iosCounter] = 'Y';
                        }
                        else{
                            $regIdArr[$v['PROFILEID']]['IOS_NOTIFICATION_IMAGE'][$iosCounter] = 'N';
                        }
                        ++$iosCounter;
                    }
                    else{
                        $regIdArr[$v['PROFILEID']][$v['OS_TYPE']][]=$v['REG_ID'];
                    }
                }
    		}
    		return $regIdArr;
    	}
    }
	return false;
    }

    public function filterProfilesBasedOnNotificationCount($profiledetailsArr,$notificationKey)
    {
    	$profileidArr = array_keys($profiledetailsArr);
	$profileidStr = implode(",",$profileidArr);
    	$countObj = new MOBILE_API_SENT_NOTIFICATIONS_COUNT();
	$scheduledAppNotificationObj = new MOBILE_API_SCHEDULED_APP_NOTIFICATIONS();
	$count_arr =  $countObj->getCountGroupByProfile($profileidStr);
    	$idArr = array();
    	foreach($profileidArr as $key=>$profileid)
    	{
    		$count = $count_arr[$profileid];
    		if($count>=0 && $count<NotificationEnums::$scheduledNotificationsLimit)
    		{
    			$countObj->incrementNotificationsCountForProfile($profileid,$count+1);
    		}
    		else if($count==NotificationEnums::$scheduledNotificationsLimit)
    		{
                        $scheduledAppNotificationObj->updateSuccessSent(NotificationEnums::$CANCELLED,$profiledetailsArr[$profileid]['MSG_ID']);
			unset($profiledetailsArr[$profileid]);
    		}
    	}
	unset($count_arr);
    	unset($profileidArr);
    	unset($countObj);
	unset($scheduledAppNotificationObj);

	return $profiledetailsArr;
     }
    public function filterProfilesBasedOnNotificationCountNew($profiledetailsArr,$notificationKey)
    {
        $profileidArr = array_keys($profiledetailsArr);
        $profileidStr = implode(",",$profileidArr);
        $countObj = new MOBILE_API_SENT_NOTIFICATIONS_COUNT();
        $count_arr =  $countObj->getCountGroupByProfile($profileidStr);
        $idArr = array();
        foreach($profileidArr as $key=>$profileid)
        {
                $count = $count_arr[$profileid];
                if($count>=0 && $count<NotificationEnums::$scheduledNotificationsLimit)
                {
                        $countObj->incrementNotificationsCountForProfile($profileid,$count+1);
                }
                else if($count==NotificationEnums::$scheduledNotificationsLimit)
                {
                        $idArr[] = $profiledetailsArr[$profileid]['ID'];
                        unset($profiledetailsArr[$profileid]);
                }
        }
        unset($count_arr);
        unset($profileidArr);
        unset($countObj);

        if(is_array($idArr) && $idArr)
                {
                        $scheduledAppNotificationObj = new MOBILE_API_SCHEDULED_APP_NOTIFICATIONS();
                        $scheduledAppNotificationObj->updateNotificationStatus($idArr,$notificationKey,NotificationEnums::$CANCELLED);
                        unset($scheduledAppNotificationObj);
                }
                return $profiledetailsArr;
        }

}
?>
