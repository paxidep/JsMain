<?php

/**
 * api actions.
 * AppRegV1
 * Controller to register a new device
 * @package    jeevansathi
 * @subpackage api
 * @author     Nitesh Sethi
 */
class LoginV1Action extends sfActions
{ 
	/**
	* Executes index action
	*
	* @param sfRequest $request A request object
	*/
	public function execute($request)
	{	
        $responseData = array();
	$loginObj=AuthenticationFactory::getAuthenicationObj();
	//To allow login from between api calls
	if($request->getParameter("fromIncompleteApi"))
		$loginObj->hashedPasswordFromDb=true;
	$email=trim($request->getParameter("email"));
	$password=$request->getParameter("password");
	$apiObj=ApiResponseHandler::getInstance();
	//Captcha check for PC and mobile
	if(MobileCommon::isNewMobileSite() || MobileCommon::isDesktop())
	{
			$captcha=$request->getParameter("captcha");
			$loginFailedObj = new LOGIN_FAILED1;
			if(JsMemcache::getInstance()->get($email."_failedLogin")!==null){
				$count=JsMemcache::getInstance()->get($email."_failedLogin");
			}
			else{
				$count=$loginFailedObj->selectFailedLoginPerDay($email,"1");
				if($count)
					JsMemcache::getInstance()->set($email."_failedLogin",$count);
				else
					JsMemcache::getInstance()->set($email."_failedLogin",0);
			}
        	//print_r($count);die;
        	if($count>2)
        	{
        		setcookie('loginAttempt','1',time()+86400000,"/");
        		if($captcha!=1)
        		{
        			if(MobileCommon::isDesktop())
        			{
        				$szToUrl = JsConstants::$siteUrl;
						if($_SERVER['HTTPS'] && strlen($_SERVER['HTTPS']) && $_GET['fmPwdReset'])
						{
							$szToUrl = JsConstants::$ssl_siteUrl;
						}
						$js_function = " <script>	var message = \"\";
						if(window.addEventListener)	
							message ={\"body\":\"1\"};
						else
							message = \"1\";

						if (typeof parent.postMessage != \"undefined\") {
							parent.postMessage(message, \"$szToUrl\");
						} else {
							window.name = message; //FOR IE7/IE6
							window.location.href = '$szToUrl';
						}
						</script> ";
						
						echo $js_function;
						die;
        			}
					else{
						$apiObj->setHttpArray(ResponseHandlerConfig::$LOGIN_FAILURE_ACCESS);
						$apiObj->generateResponse();
						die;
					}
        			//return 0;
        		}
        	}
	}
	if(MobileCommon::isNewMobileSite() || MobileCommon::isDesktop())
	{
		$password=rawurldecode($password);
	}
	$registrationid=$request->getParameter("registrationid");
	$rememberMe=$request->getParameter("rememberme");
	$result=$loginObj->login($email,$password,$rememberMe);
	
	if($result && $result[ACTIVATED]<>'D' && $result[GENDER]!="")
	{
		$apiObj->setAuthChecksum($result[AUTHCHECKSUM]);
                $maxAlarmTimeObj = new MOBILE_API_MAX_ALARM_TIME;
                $alarmCurrentTimeData = $maxAlarmTimeObj->getArray();
                $alarmCurrentTime = $alarmCurrentTimeData[0][MAX_ALARM_TIME];
                $alarmTime[$result['PROFILEID']]=alarmTimeManager::getNextTime($alarmCurrentTime,NotificationEnums::$alarmMaxTime,NotificationEnums::$alarmMinTime);
                $alarmTimeObj = new MOBILE_API_ALARM_TIME;
                $alarmTimeObj->replace($alarmTime);
                $maxAlarmTimeObj->updateMaxAlarmTime($alarmTime[$result['PROFILEID']]);
                if(CommonFunction::getMainMembership($result[SUBSCRIPTION]))
					$subscription=CommonFunction::getMainMembership($result[SUBSCRIPTION]);
				else
					$subscription="";
		$done = NotificationFunctions::manageGcmRegistrationid($registrationid,$result['PROFILEID'])?"1":"0";
		$notificationStatus = NotificationFunctions::settingStatus($registrationid,$result['PROFILEID']);
		
		if(MobileCommon::isMobile() || MobileCommon::isDesktop()==true)  
	    {
	    	//For JPSC/JSMS,reenable notifications  if disabled on logout
		    $channel = MobileCommon::isMobile()?"M":"D";
		    $registrationIdObj = new MOBILE_API_BROWSER_NOTIFICATION_REGISTRATION();
		    $registrationIdObj->updateNotificationDisableStatus($result['PROFILEID'],$channel,'N');
		    unset($registrationIdObj);
	    }

		$loginData=array("GENDER"=>$result[GENDER],"USERNAME"=>$result[USERNAME],"INCOMPLETE"=>$result[INCOMPLETE],"SUBSCRIPTION"=>$subscription,"LANDINGPAGE"=>'1',"GCM_REGISTER"=>$done,"NOTIFICATION_STATUS"=>$notificationStatus);
		$apiObj->setHttpArray(ResponseHandlerConfig::$LOGIN_SUCCESS);
                if($familyArr = $request->getParameter('setFamilyArr')){
                    $loginData['FamilyDetails'] = $familyArr;
                }
		$apiObj->setResponseBody($loginData);
		if($request->getParameter("fromPc"))
		{
			$result=$apiObj->getResponseStatusCode();
			$szToUrl = JsConstants::$siteUrl;
			if($_SERVER['HTTPS'] && strlen($_SERVER['HTTPS']) && $_GET['fmPwdReset'])
			{
				$szToUrl = JsConstants::$ssl_siteUrl;
			}
			$js_function = " <script>	var message = \"\";
			if(window.addEventListener)	
				message ={\"body\":\"$result\"};
			else
				message = \"$result\";

			if (typeof parent.postMessage != \"undefined\") {
				parent.postMessage(message, \"$szToUrl\");
			} else {
				window.name = message; //FOR IE7/IE6
				window.location.href = '$szToUrl';
			}
			</script> ";
			
			echo $js_function;
			die;
		}
		else
			$apiObj->generateResponse();
	}
	else
	{
		if($result[ACTIVATED]=='D'){
			$apiObj->setHttpArray(ResponseHandlerConfig::$LOGIN_FAILURE_DELETED);
			//ValidationHandler::getValidationHandler("","Profile with this email address has been deleted");
		}
		else if($result[PROFILEID] && $result[GENDER]=="")
		{
			$apiObj->setHttpArray(ResponseHandlerConfig::$GENDER_NOT_PRESENT);
		}
		else{
			$apiObj->setHttpArray(ResponseHandlerConfig::$LOGIN_FAILURE_ACCESS);
			$loginFailedObj=new LOGIN_FAILED1();
			$ip=CommonFunction::getIP();
			if($email && $password){
				$loginFailedObj->insertFailedLogin($email,$password,$_SERVER[HTTP_USER_AGENT],$ip);
				if(JsMemcache::getInstance()->get($email."_failedLogin")!==null)
					JsMemcache::getInstance()->set($email."_failedLogin",JsMemcache::getInstance()->get($email."_failedLogin")+1);
				else{
					$count=$loginFailedObj->selectFailedLoginPerDay($email,"1");
					if($count)
						JsMemcache::getInstance()->set($email."_failedLogin",$count);
					else
						JsMemcache::getInstance()->set($email."_failedLogin",0);
				}
			}
			//ValidationHandler::getValidationHandler("","Login details provided were not correct");
		}
		if($request->getParameter("fromPc"))
		{
			$result=$apiObj->getResponseStatusCode();
			$szToUrl = JsConstants::$siteUrl;
			if($_SERVER['HTTPS'] && strlen($_SERVER['HTTPS']) && $_GET['fmPwdReset'])
			{
				$szToUrl = JsConstants::$ssl_siteUrl;
			}
			$js_function = " <script>	var message = \"\";
			if(window.addEventListener)	
				message ={\"body\":\"$result\"};
			else
				message = \"$result\";

			if (typeof parent.postMessage != \"undefined\") {
				parent.postMessage(message, \"$szToUrl\");
			} else {
				window.name = message; //FOR IE7/IE6
				window.location.href = '$szToUrl';
			}
			</script> ";
			
			echo $js_function;
			die;
		}	
		else
			$apiObj->generateResponse();
		
	}
  if($request->getParameter('INTERNAL')==1){
			return sfView::NONE;
	} 
	die;
    }
}