<?php

/**
 * myjs actions.
 *
 * @package    jeevansathi
 * @subpackage myjs
 * @author     Reshu Rajput
 * @Created on   2013-10-12
 */
class myjsActions extends sfActions
{

	/**
  	*this function is for jsms myjs page... to map the membership id to the proper link to which to redirect when clicked on the membership banner
  	*
  	* 
  	*/
	private function getMembershipLink($pageId){
  	$arr=array('16'=>'/profile/contacts_made_received.php?page=phonebook_contacts_viewed&filter=M',
  		'6'=>'/profile/mem_comparison.php','4'=>'/profile/viewprofile.php?ownview=1#Family','2'=>'/profile/viewprofile.php?ownview=1#Education','3'=>'/profile/viewprofile.php?ownview=1#Career');
  	return $arr[$pageId];
  }



 	/**
  	* Executes index action
  	*
  	* @param sfRequest $request A request object
  	*/
   
	public function executeIndex(sfWebRequest $request)
  	{
		$module= "MYJS";
		$profileCommunication = new ProfileCommunication();
		$loggedInProfileObj=LoggedInProfile::getInstance('newjs_master');
        	$pid=$loggedInProfileObj->getPROFILEID();
        //	$loggedInProfileObj->getDetail("","","HAVEPHOTO");
		$infoTypeId = $request->getParameter("infoTypeId");
		$pageNo = $request->getParameter("pageNo");
		if($infoTypeId)
		{
			$json=1;
			$infoType = ProfileInformationModuleMap::getInfoTypeById($module,$infoTypeId);	
			$infoTypenav = array($infoType=>$pageNo);
			unset($infoTypeId);
			unset($infoType);
			unset($pageNo);
		}

		$this->countObj= $profileCommunication->getCount($module,$loggedInProfileObj);
		$this->displayObj= $profileCommunication->getDisplay($module,$loggedInProfileObj,$infoTypenav);
		if($json)
		{
			foreach($this->displayObj as $k=>$v)
			{
				//print_r($v);die;
				//print_r(json_encode($v));	
				//die;
			}
		}
			//IP address of the user
       		$this->ipAddress=CommonUtility::getCurrentIP();
		$this->profilechecksum = JsAuthentication::jsEncryptProfilechecksum($pid);
		//Retrieving photo count for Self profile widget links
		$pictureService = new PictureService($loggedInProfileObj);
		if($pictureService->isProfilePhotoPresent() != "Y")
		{
			$pictureUploadLink="U"; // For upload photo link
			$this->myPic = PictureService::getRequestOrNoPhotoUrl('noPhoto','ThumbailUrl',$loggedInProfileObj->getGENDER());
		}
		else
		{
			$pictureCount= $pictureService->getUserUploadedPictureCount();
			if($pictureService->isProfilePhotoUnderScreening() =="Y")
				 $this->myPic = PictureService::getRequestOrNoPhotoUrl('underScreeningPhoto','ThumbailUrl',$loggedInProfileObj->getGENDER());
			else
			{
				$profilePicObj = $pictureService->getProfilePic();
				$this->myPic = $profilePicObj->getThumbailUrl();
			}
			if($pictureCount < sfConfig::get("app_max_no_of_photos"))
			{
				$pictureUploadLink="M";  // for more photo link
			}
			else 
				$pictureUploadLink="NA"; // for no link
		}
		$this->pictureUploadLink = $pictureUploadLink;
			// Profile completion widget function call
		$completionObj=  ProfileCompletionFactory::getInstance(null,$loggedInProfileObj,null);
                $this->profileCompletePer =$completionObj->getProfileCompletionScore();
       	        $this->profilePercentMessages =$completionObj->GetIncompleteDetails();
		$this->profilePercentLinks =$completionObj->GetLink();
	}


	/**
        * Mobile Api version 1.0 action class
        */
        public function executePerformV1(sfWebRequest $request)	
        {                

                               
		$module= "MYJSAPP";
		$inputValidateObj = ValidateInputFactory::getModuleObject("myjs");
		$respObj = ApiResponseHandler::getInstance();
		$inputValidateObj->validateRequestMyJsData($request);
                $output = $inputValidateObj->getResponse();
		if($output["statusCode"]==ResponseHandlerConfig::$SUCCESS["statusCode"])
                {
			$profileCommunication = new ProfileCommunication();
                	$loggedInProfileObj=LoggedInProfile::getInstance('newjs_master');
                	$pid= $loggedInProfileObj->getPROFILEID();
            	//  	$loggedInProfileObj->getDetail("","","HAVEPHOTO");
			$infoTypeId = $request->getParameter("infoTypeId");
                	$pageNo = $request->getParameter("pageNo");
			$params["profileList"] = $request->getParameter("profileList");
			if((MobileCommon::isApp() == "I")||MobileCommon::isNewMobileSite())
			{
				$Apptype="IOS";
				$appV1obj = new MyJsIOSV1();
                                
			}
			else{
					
                    $appV1obj = new MyJsAndroidV1();
                            
                }
                        
                        if($infoTypeId)
                	{
                        	$infoType = ProfileInformationModuleMap::getInfoTypeById($module,$infoTypeId,$Apptype);
                        	$json=1;
                        	$infoTypeNav = array($infoType=>$pageNo);
							$displayObj= $profileCommunication->getDisplay($module,$loggedInProfileObj,$infoTypeNav,$params);
                        	$appV1DisplayJson = $appV1obj->getJsonAppV1($displayObj);
                        	unset($infoTypeId);
                        	unset($infoType);
                        	unset($pageNo);
                	}
			else
			{
				$pictureService = new PictureService($loggedInProfileObj);
                		if($pictureService->isProfilePhotoPresent() == "Y")
					$profileInfo["PHOTO_FLAG"]="Y";
				else
					$profileInfo["PHOTO_FLAG"]="N";
				unset($pictureService);
				$completionObj=  ProfileCompletionFactory::getInstance("API",$loggedInProfileObj,null);
				$profileInfo["COMPLETION"]=$completionObj->getProfileCompletionScore();
				$profileInfo["INCOMPLETE"]=$completionObj->GetAPIResponse("MYJS");
				$displayObj= $profileCommunication->getDisplay($module,$loggedInProfileObj);
				$profileInfo["PHOTO"] = NULL;
				if(MobileCommon::isApp() != "I"||$loggedInProfileObj->getHAVEPHOTO()!="U")
					$profileInfo["PHOTO"] = $appV1obj->getProfilePicAppV1($loggedInProfileObj);
									$appV1DisplayJson = $appV1obj->getJsonAppV1($displayObj,$profileInfo); 
			}
			$appV1DisplayJson['BELL_COUNT'] = BellCounts::getDetails($pid);

                         if(MobileCommon::isApp() == "I"){
                         	$appV1DisplayJson['membership_message'] = NULL;
                         } 
//cal layer added by palash                       
        ob_start();
    	sfContext::getInstance()->getController()->getPresentationFor("common", "ApiCALayerV1");
    	$layerData = ob_get_contents();
    	ob_end_clean();
    	$layerData=json_decode($layerData,true);
        $appV1DisplayJson['calObject']=$layerData['calObject']?$layerData['calObject']:null;
////////////////////////////////////////////////

				////cal layer added by palash                       
			    ob_start();
    			sfContext::getInstance()->getController()->getPresentationFor("common", "ApiCALayerV1");
    			$layerData = ob_get_contents();
    			ob_end_clean();
    			$layerData=json_decode($layerData,true);
				//////////////////////////////////
                    
        		$appV1DisplayJson['calObject']=$layerData['calObject']?$layerData['calObject']:null;


		$respObj->setHttpArray(ResponseHandlerConfig::$SUCCESS);
        $respObj->setResponseBody($appV1DisplayJson);

		}
                else
                {
                	$respObj->setHttpArray($output);
                }
                unset($output);
		unset($inputValidateObj);
		$respObj->generateResponse();
		if(MobileCommon::isApp()==null)
			return sfView::NONE;
                die;
	}
	public function executeJsmsPerform(sfWebRequest $request)
	{
            	$this->getResponse()->setSlot("optionaljsb9Key", Jsb9Enum::jsMobMYJSUrl);
                $this->loginData=$request->getAttribute("loginData");
                $this->profile=Profile::getInstance();
                $this->loginProfile=LoggedInProfile::getInstance();
          //      $this->loginProfile->getDetail($request->getAttribute("profileid"),"PROFILEID","*");
                ob_start();
                $jsonData = sfContext::getInstance()->getController()->getPresentationFor("myjs", "performV1");

                $output = ob_get_contents();
                ob_end_clean();
           	    $this->apiData=json_decode($output,true);
           	    // redirection to cal layers if calObject is not null
           	    if ($this->apiData['calObject'])
           	    {
           	    	$request->setAttribute('calObject',$this->apiData['calObject']);
           	    	$request->setAttribute('gender',$this->loginProfile->getGENDER());

           	    	sfContext::getInstance()->getController()->forward("common","CALJSMS");
           	    	die;
	            }
	            $this->apiData['gender']=$this->loginProfile->getGENDER();
              	$this->apiData['membership_message_link']=$this->getMembershipLink($this->apiData['membership_message']['pageId']);	

              			
///// block for adding desired partner option in profile completion slider in mobile

              		$tempDpp['url']='/profile/viewprofile.php?ownview=1#Dpp';	
              		$tempDpp['cssClass']='dppHeart';	
              		$tempDpp['title']='Desired Partner';	
//////////////////////////////////////////

              		$length=count($this->apiData['my_profile']['incomplete']);
              		$this->apiData['my_profile']['incomplete'][$length]=$tempDpp;	

                   $this->setTemplate("jsmsPerform");
                   $request->setParameter('INTERNAL',1);
				$request->setParameter('getMembershipMessage',1);
 	}

 	public function executeJspcPerform(sfWebRequest $request)
	{
		if(MobileCommon::isNewMobileSite())
		{
			header("Location:".sfConfig::get("app_site_url"));die;
		}
		
		$this->getResponse()->setSlot("optionaljsb9Key", Jsb9Enum::jspcMYJSUrl);
		$this->loginProfile=LoggedInProfile::getInstance();
		$this->profileid=$this->loginProfile->getPROFILEID();
		$this->gender=$this->loginProfile->getGENDER();
		$entryDate = $this->loginProfile->getENTRY_DT();
		$CITY_RES_pixel = $this->loginProfile->getCITY_RES();
		$this->profilePic = $this->loginProfile->getHAVEPHOTO();
		if (empty($this->profilePic))
			$this->profilePic="N";
		$this->username = $this->loginProfile->getUSERNAME();
		
		//New Membership 
		$memHandlerObj = new MembershipHandler();
		$this->membershipStatus = $memHandlerObj->getRealMembershipName($this->profileid);
		
		if($this->profilePic!="N"){
			$pictureServiceObj=new PictureService($this->loginProfile);
			$profilePicObj = $pictureServiceObj->getProfilePic();
			if($profilePicObj){

			$photoArray = PictureFunctions::mapUrlToMessageInfoArr($profilePicObj->getProfilePic120Url(),'ThumbailUrl','',$this->gender);
            if($photoArray[label] != '')
                   $this->photoUrl = PictureFunctions::getNoPhotoJSMS($this->gender,'ProfilePic120Url');
            else
                   $this->photoUrl = $photoArray['url'];
				
			$this->ThumbailUrl=$profilePicObj->getThumbailUrl();
			}
		}
   		else{
			$this->photoUrl=$this->ThumbailUrl=PictureFunctions::getNoPhotoJSMS($this->gender,'ProfilePic120Url');
		}
		$this->otherthumbnail = PictureService::getRequestOrNoPhotoUrl("noPhoto","ProfilePic120Url",$this->gender=="F"?"M":"F");
		$this->otherPhotoUrl = PictureService::getRequestOrNoPhotoUrl("noPhoto","ProfilePic235Url",$this->gender=="F"?"M":"F");
		
		$memHandlerObj = new MembershipHandler();
		$userObj = new memUser($this->profileid);
		$purchasesObj = new BILLING_PURCHASES();
		list($ipAddress, $currency) = $memHandlerObj->getUserIPandCurrency();
		$userObj->setIpAddress($ipAddress);
		$userObj->setCurrency($currency);
		if (!empty($this->profileid)) {
		     $userObj->setMemStatus();
		     $userType = $userObj->userType;
		     $subStatus = $memHandlerObj->getSubscriptionStatusArray($userObj,NULL,'myjs');
		     $this->contactsRemaining = $userObj->getRemainingContacts($this->profileid );
		     $subscriptionExp = date("Y-m-d",strtotime($subStatus[0]['EXPIRY_DT']));
		     $yrdata= strtotime($subscriptionExp);
    		 $this->expirySubscription = date('d M Y', $yrdata);;
		}

		$data2 = $memHandlerObj->fetchHamburgerMessage($request);
		$this->MembershipMessage = $data2['hamburger_message']; 
		//PROFILE COMPLETIION 
		$this->membershipPlanExpiry=$this->MembershipMessage['expiry'];
		
		
		$cScoreObject = ProfileCompletionFactory::getInstance(null,$this->loginProfile,null);
		$this->iPCS = $cScoreObject->getProfileCompletionScore();
		$noOfLinks=3;
		$this->arrMsgDetails = $cScoreObject->GetIncompleteDetails($noOfLinks);
		$this->arrLinkDetails = $cScoreObject->GetLink('MyJS');
		$this->arrAPI= $cScoreObject->GetAPIResponse();
		$this->apiData=json_decode($output,true);
		
		
		//FTU
		//USING ENTRY DATE TO COMPARE WITH CURRENT TIME AND SET FLAG
		$currentTime=time();
		$registrationTime = strtotime($entryDate);
		$this->engagementCount=array();
		$this->showFtu = 0;
		if(($currentTime - $registrationTime)/(3600)<24){
			$this->engagementCount= BellCounts::getFTUCountDetails($this->profileid); 
			if($this->engagementCount["TOTAL"] == 0){
				$this->showFtu = 1;
			}
		}
		else
			$this->engagementCount= BellCounts::getNewCountsMyjsPc($this->profileid);
		//Personal Verification
		$incHistObj = new incentive_HISTORY();
		$purchasesObj = new BILLING_PURCHASES();
		$incFieldSalesCityObj = new incentive_FIELD_SALES_CITY();
		$dispositionDone = $incHistObj->get($this->profileid,'PROFILEID',"DISPOSITION = 'FVD' AND PROFILEID=$this->profileid");
		$activeServices = $purchasesObj->getCurrentlyActiveService($data['PROFILEID']);
		$checkFieldSalesCity = $incFieldSalesCityObj->checkFieldSalesCityCodeExists($CITY_RES_pixel);
		$this->scheduleVisitCount=0;
		if(!$dispositionDone && $activeServices=="FREE" && $checkFieldSalesCity){
			$fieldSalesWidgetObj = new incentive_FIELD_SALES_WIDGET();
			$this->scheduleVisitCount = $fieldSalesWidgetObj->checkIfProfileidExists($this->profileid);
			$this->schedule_visit_widget = 1;
		}
		else
			$this->schedule_visit_widget = 0;
		$personalVerif = $this->schedule_visit_widget && !($this->scheduleVisitCount) ? '1' : '0';
	
		
		$this->staticCardArr = $this->getStaticCardDetails($personalVerif,$this->loginProfile->getSUBSCRIPTION(),$this->membershipStatus);
		
		//name of user
		$nameOfUserOb=new incentive_NAME_OF_USER();
		$this->nameOfUser=$nameOfUserOb->getName($this->profileid);
	
//--------------- Critical Action Layer section ------------
	    ob_start();
    	sfContext::getInstance()->getController()->getPresentationFor("common", "ApiCALayerV1");
    	$layerData = ob_get_contents();
    	ob_end_clean();
    	$layerData=json_decode($layerData,true);
        $calObject=$layerData['calObject']?$layerData['calObject']:null;
		$this->CALayerShow = $calObject[LAYERID] ? $calObject[LAYERID] : '0';
//--------------- Critical Action Layer section ends ------------
				
// ---------------consent message variable
		$this->showConsentMsg=$request->getParameter('showConsentMsg');
//--------------------------------------------------------


///// -----------------help Screen variable

		if(CommonConstants::showHelpScreensJSPC && $this->showConsentMsg!='Y' && $this->CALayerShow=='0')
		{		
			$this->showHelpScreen=JsMemcache::getInstance()->get($this->profileid."_showHelpScreen");
			if(!$this->showHelpScreen)
			{
			$helpScreenObj= new newjs_HELP_SCREEN();
		
			if($helpScreenObj->doesExist($this->profileid)) 
			$this->showHelpScreen='N'; 
		
			else 
			{ 
				$this->showHelpScreen='Y';
				$helpScreenObj->insertOneTimeEntry($this->profileid);
			}
		
			JsMemcache::getInstance()->set($this->profileid."_showHelpScreen",'N');
			}
		} 

		else $this->showHelpScreen='N';


			$this->videoLinkLayer=JsMemcache::getInstance()->get($this->profileid."_videoLinkLayer");
			if(!$this->videoLinkLayer)
			{
				$videoLinkObj= new NEWJS_VIDEO_LINK();
			
				if($videoLinkObj->doesExist($this->profileid)) 
					$this->videoLinkLayer='N'; 
			
				else 
					$this->videoLinkLayer='Y';
			}
			else
				$this->videoLinkLayer='N';

		//enable JPSC notifications layer depending on user earlier registered or not
       	$notificationObj = new NotificationConfigurationFunc();
        $this->showEnableNotificationsLayer = $notificationObj->showEnableNotificationLayer($this->profileid);
        unset($notificationObj);

		$this->setTemplate("_jspcMyjs/jspcPerform");
		sfContext::getInstance()->getResponse()->setSlot("optionaljsb9Key", Jsb9Enum::jsMyJSPageUrl);

	}



	public function executeVideolink(sfWebRequest $request)

	{
		
		
			$videoLinkObj= new NEWJS_VIDEO_LINK();
				$profileid=$request->getParameter("profileid");
				
				$videoLinkObj->insertOneTimeEntry($profileid);
			JsMemcache::getInstance()->set($profileid."_videoLinkLayer",'N');
			echo "success";die;
	}


	private function getStaticCardDetails($personalVerif,$subscription,$memStatus)
	{

$counter=0;
if($personalVerif=='1'){
$staticCardArr[$counter]['head']='Personal Verification';
$staticCardArr[$counter]['msg']='Get a seal of trust on your profile through personal verification';
$staticCardArr[$counter]['url']='/static/agentinfo';
$counter++;
}

$staticCardArr[$counter]['head']='Desired Partner Profile';
$staticCardArr[$counter]['msg']='Get relevant interests and recommendations';
$staticCardArr[$counter]['url']='/profile/dpp';
$counter++;

if(($memStatus!='Free') && !(strstr($subscription,'R') || strstr($subscription,'T')))  {
$staticCardArr[$counter]['head']='Additional Services';
$staticCardArr[$counter]['msg']='These additional services can boost your responses';
$staticCardArr[$counter]['url']='/profile/mem_comparison.php';
$counter++;
}

if($memStatus=='Free'){
$staticCardArr[$counter]['head']='Upgrade Membership';
$staticCardArr[$counter]['msg']='Send messages, view contacts and get more responses';
$staticCardArr[$counter]['url']='/profile/mem_comparison.php';
$counter++;
}

$staticCardArr[$counter]['head']='Success Stories';
$staticCardArr[$counter]['msg']='Stories of people who found their soulmate through us';
$staticCardArr[$counter]['url']='/successStory/story';
$counter++;

$staticCardArr[$counter]['head']='Contact Us';
$staticCardArr[$counter]['msg']='Have queries? Feel free to contact us right away!';
$staticCardArr[$counter]['url']='/profile/contact.php';
$counter++;

$staticCardArr[$counter]['head']='Protect Yourself'; 
$staticCardArr[$counter]['msg']='Tips for a safe and secure partner search experience';
$staticCardArr[$counter]['url']='/static/page/fraudalert';

return $staticCardArr;


	} 
}
 