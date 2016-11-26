<?php 

/**
 * ProfileJprofileContact
 * Library Class for store JPROFILE_CONTACT Table
 */

class ProfileContact
{
	
	/**
	 * @var Static Instance of this class
	 */
	private static $instance;

	/**
	 * Object of Store class
	 * @var instance of NEWJS_Jprofile_Contact|null
	 */
	private static $objJprofileContact = null;

	/**
	 * @param $dbName - Database to which the connection would be made
	 */
	private function __construct($dbname = "")
	{
		self::$objJprofileContact = new NEWJS_Jprofile_Contact($dbname);
	}

	/**
	 * To Stop clone of this class object
	 */
	private function __clone() {}

	/**
	 * To stop unserialize for this class object
	 */
	private function __wakeup() {}


	/**
	 * @fn getInstance
	 * @brief fetches the instance of the class
	 * @param $dbName - Database name to which the connection would be made
	 * @return instance of this class
	 */
	public static function getInstance($dbName = '')
	{
		if (!$dbName)
			$dbName = "newjs_master";
		if (isset(self::$instance)) {
			//If different instance is required
			if ($dbName != self::$instance->connectionName) {
				$class = __CLASS__;
				self::$instance = new $class($dbName);
				self::$instance->connectionName = $dbName;
			}
		}
		else {
			$class = __CLASS__;
			self::$instance = new $class($dbName);
			self::$instance->connectionName = $dbName;
		}
		return self::$instance;
	}

	public function getArray($valueArray="",$excludeArray="",$greaterThanArray="",$fields="PROFILEID",$indexProfileId = 0)
	{
		$bServedFromCache = false;
		$objProCacheLib = ProfileCacheLib::getInstance();
		$valueArray =  array('PROFILEID'=>$profileid);
		if(is_array($valueArray) && in_array(ProfileCacheConstants::CACHE_CRITERIA, $valueArray))
		{
			// Todo: From cache nd set
		}
	}

	public function getProfileContacts($pid)
	{
		$bServedFromCache = false;
		$fields='*'; // todo : see fields
		$objProCacheLib = ProfileCacheLib::getInstance();

		if ($objProCacheLib->isCached(ProfileCacheConstants::CACHE_CRITERIA, $pid, ProfileCacheConstants::ALL_FIELDS_SYM, __CLASS__))
		{
			$result = $objProCacheLib->get(ProfileCacheConstants::CACHE_CRITERIA, $pid, $fields, __CLASS__);
			//so for that case also we are going to query mysql
			if (false !== $result)
			{
				$bServedFromCache = true;
				$result = FormatResponse::getInstance()->generate(FormatResponseEnums::REDIS_TO_MYSQL, $result);
				// Todo: check if result is suitable acc to requirement
			}

			if($result && in_array(ProfileCacheConstants::NOT_FILLED, $result))
			{
				$result = array();
			}
		}

		if ($bServedFromCache && ProfileCacheConstants::CONSUME_PROFILE_CACHE) {
			$this->logCacheConsumeCount(__CLASS__);
			return $result;
		}

		// Get Records from Mysql
		$result = self::$objJprofileContact->getProfileContacts($pid);

		// Request to Cache this Record, on demand
		if(is_array($result) && count($result))
		{
			$result['PROFILEID'] = $pid;
		}

		if(is_array($result) && isset($result['PROFILEID']) && false === ProfileCacheLib::getInstance()->isCommandLineScript())
		{
			$objProCacheLib->cacheThis(ProfileCacheConstants::CACHE_CRITERIA, $result['PROFILEID'], $result, __CLASS__);
		}

		// todo : what is dummyResult case?
		return $result;
	}

	public function update($pid, $paramArr = array())
	{
		$bResult = self::$objJprofileContact->update($pid, $paramArr);
		if(true === $bResult) {
		  ProfileCacheLib::getInstance()->updateCache($paramArr, ProfileCacheConstants::CACHE_CRITERIA, $pid, __CLASS__);
		}

		return $bResult;
	}
}
?>