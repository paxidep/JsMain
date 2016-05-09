<?php
class HamburgerApp
{
	public static function getHamburgerDetails($profileid,$version='',$forwardingArray)
        {
		$moduleName = $forwardingArray['moduleName'];
		$actionName = $forwardingArray['actionName'];
		if($profileid && RequestHandlerConfig::$moduleActionHamburgerArray[$moduleName][$actionName])
		{
			$profileObj=LoggedInProfile::getInstance('newjs_master');
			$profileObj->getDetail("","","HAVEPHOTO,PHOTO_DISPLAY");
			$pictureServiceObj=new PictureService($profileObj);
			if($profileObj->getHAVEPHOTO()!="U"||MobileCommon::isApp() != "I")
			{
				$profilePicObj = $pictureServiceObj->getProfilePic();
				if($profilePicObj)
					$thumbNail = $profilePicObj->getThumbailUrl();
				if(!$thumbNail)
					$thumbNail = PictureService::getRequestOrNoPhotoUrl('noPhoto','ThumbailUrl',$profileObj->getGENDER());
				$thumbNail = PictureFunctions::mapUrlToMessageInfoArr($thumbNail,'ThumbailUrl');
			}
			else
				$thumbNail = NULL;
			$hamburgerDetails['THUMBNAIL']=$thumbNail;
			$request = sfContext::getInstance()->getRequest();
			$memHandlerObj = new MembershipHandler();
			$data2 = $memHandlerObj->fetchHamburgerMessage($request);
			$membershipMessage = $data2['hamburger_message'];
			$hamburgerDetails["MEMBERSHIPT_TOP"] = $membershipMessage["top"]?$membershipMessage["top"]:null;
			$hamburgerDetails["MEMBERSHIPT_BOTTOM"] = $membershipMessage["bottom"]?$membershipMessage["bottom"]:null;
			$profileMemcacheObj = new ProfileMemcacheService($profileObj);
			$hamburgerDetails['AWAITING_RESPONSE_NEW']=$profileMemcacheObj->get("AWAITING_RESPONSE_NEW");
			$hamburgerDetails['AWAITING_RESPONSE']=$profileMemcacheObj->get("AWAITING_RESPONSE");
			$hamburgerDetails['FILTERED']=$profileMemcacheObj->get("FILTERED");
			$hamburgerDetails['ACC_ME_NEW']=$profileMemcacheObj->get("ACC_ME_NEW");
	                $hamburgerDetails['MESSAGE_NEW']=0;
	                //$profileMemcacheObj->get("MESSAGE_NEW");
			$hamburgerDetails['MATCHALERT']=$profileMemcacheObj->get("MATCHALERT_TOTAL");
			$hamburgerDetails['VISITOR_ALERT']=$profileMemcacheObj->get("VISITOR_ALERT");
			$hamburgerDetails['BOOKMARK']=$profileMemcacheObj->get("BOOKMARK");
				$hamburgerDetails['JUST_JOINED_COUNT'] = $profileMemcacheObj->get('JUST_JOINED_MATCHES');
				$hamburgerDetails['JUST_JOINED_NEW'] = $profileMemcacheObj->get('JUST_JOINED_MATCHES_NEW');
				$hamburgerDetails['INTEREST_PENDING'] = $profileMemcacheObj->get('AWAITING_RESPONSE')+$profileMemcacheObj->get('NOT_REP');
				$hamburgerDetails['ACCEPTED_MEMBERS'] = $profileMemcacheObj->get('ACC_ME')+$profileMemcacheObj->get('ACC_BY_ME');
				if(MobileCommon::isApp() == "I")
				{
					$request->setParameter("perform","count");
					ob_start();
					$jsonData = sfContext::getInstance()->getController()->getPresentationFor("search", "saveSearchCallV1");
					$output = ob_get_contents();
					ob_end_clean();
					$savedSearchCountData = json_decode($output,true);
					if($savedSearchCountData['saveDetails']['count'])
						$hamburgerDetails['SAVE_SEARCH'] = $savedSearchCountData['saveDetails']['count'];
					else
						$hamburgerDetails['SAVE_SEARCH'] = 0;
				}
				
			return $hamburgerDetails;
		}
        }
}
?>
