<?php




class emailVerification

{
	


	public function sendVerificationMail($profileId,$uniqueId) 
	{

		if(!$profileId || !$uniqueId)return false;
		$emailSender = new EmailSender('4', '1834');
        $tpl = $emailSender->setProfileId($profileId);
		$memObject=JsMemcache::getInstance();
		$instanceID=$memObject->get('emailVerInstanceId');	

		if(!$instanceID)
		{
			$countObj=new jeevansathi_mailer_DAILY_MAILER_COUNT_LOG();
			$instanceID = $countObj->getID('EMAIL_VER_MAILER');
			$memObject->set('emailVerInstanceId',$instanceID);
		}
//        die($emailSender->getProfile()->getEMAIL());
        $tpl->getSmarty()->assign("profileid", $profileId);
        $tpl->getSmarty()->assign("uniqueId", $uniqueId);
        $tpl->getSmarty()->assign("instanceID", $instanceID);
		$partialObj = new PartialList();
        $partialObj->addPartial("jeevansathi_contact_address", "jeevansathi_contact_address");
        $tpl->setPartials($partialObj);
        //$EmailTemplateObj= new EmailTemplate(1796);
        $date= strtotime(date("Y-m-d"));
    	$date = date('d M ', $date);
        $subject = "Verify your Email ID";
		$tpl->setSubject($subject);
        $emailSender->send();
        $status = $emailSender->getEmailDeliveryStatus();
       	(new MAIL_EMAIL_VER_MAILER())->makeEntry($profileId,$status);

	}



	public function markVerifiedInEmailLog($profileid,$email)
	{

		
		(new NEWJS_EMAIL_CHANGE_LOG())->markAsVerified($profileid,$email);


	}








}