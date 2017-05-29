<?php

class jsValidatorSectMuslim extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('religion');
  }
  
  protected function doClean($value)
  {
    if(MobileCommon::isApp())
    {   
        $appVersion=sfContext::getInstance()->getRequest()->getParameter("API_APP_VERSION")?sfContext::getInstance()->getRequest()->getParameter("API_APP_VERSION"):0;
        if((MobileCommon::isAndroidApp() && $appVersion<99) || (MobileCommon::isIOSApp()))//don't validate on older app versions
            return $value;
    }
    $clean = (string) $value;
  	$religion = $this->getOption('religion');
  	if($clean)
	{
		if(!$religion)
		{
			throw new sfValidatorError($this,'please provide a religion value for Sect');
		}
		elseif($religion==Religion::MUSLIM)
			$field_map_sect_name=ObjectiveFieldMap::getFieldMapKey("SECT_MUSLIM");
		else
			throw new sfValidatorError($this,'Sect is not allowed for '.FieldMap::getFieldLabel('religion',$religion));
		$choices=@array_keys(FieldMap::getFieldLabel($field_map_sect_name,'',1));
		if(!in_array($clean,$choices))
		{
			throw new sfValidatorError($this,'please provide a valid value for sect for '.FieldMap::getFieldLabel('religion',$religion));
		}
	}
        else if($religion == Religion::MUSLIM)
            throw new sfValidatorError($this,'Caste is mandatory for '.FieldMap::getFieldLabel('religion',$religion));
      return $clean;
  }
  
  protected function isEmpty($value)
  {
    return 0;
  }
 }



