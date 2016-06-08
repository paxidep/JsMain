<?php

class MissedCallVerification extends PhoneVerification
{
	
private $virtualNo;
private $virtualNoId;
private $knowlarityObj;

public function __construct($phone,$virtualNo)
{

		try
		{
    		if(!$phone || !$virtualNo) throw new Exception("wrong or null : phone or VirtualNo", 1);   	
			$profileId=phoneKnowlarity::getProfileFromPhoneVNo($phone,$virtualNo);
			if(!$profileId) throw new Exception("wrong or null : phone or VirtualNo", 1);			
			$this->profileObject=new Profile('',$profileId);
			$this->profileObject->getDetail("","","*");
			$this->isd=$this->profileObject->getISD();
			
			switch($phone)
			{

			case $this->isd.$this->profileObject->getPHONE_MOB():
			$this->isVerified=$this->profileObject->getMOB_STATUS();
			$this->phoneType='M';
			break;

			case $this->isd.$this->profileObject->getPHONE_WITH_STD():
			$this->isVerified=$this->profileObject->getLANDL_STATUS();
			$this->phoneType='L';
			break;

			default:
			$contactArray=(new newjs_JPROFILE_CONTACT())->getArray(array('PROFILEID'=>$profileId),'','',"ALT_MOBILE,ALT_MOB_STATUS");
			if($this->isd.$contactArray['0']['ALT_MOBILE']==$phone){
				$this->phoneType='A'; 		
				$this->isVerified=$contactArray['0']['ALT_MOB_STATUS']=='Y'?'Y':'N';
				}
			break;
			}

					if(!$this->phoneType) throw new Exception("The phone is not saved for any profile", 1);			
					else $this->phone=$phone;
			}
			catch(Exception $e){
				return null;
			}
		


}




public function phoneUpdateProcess() {

if(parent::phoneUpdateProcess('KNW'))
$this->clearEntry();

}



private function clearEntry()
{
	(new newjs_KNWLARITYVNO())->clearProfilePhoneEntry($this->profileObject->getPROFILEID(),$this->phone);
}

}
