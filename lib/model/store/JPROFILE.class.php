<?php
/**
 * JPROFILE
 *
 * This class handles all database queries to JPROFILE
 *
 * @package    jeevansathi
 * @author     Tanu Gupta
 * @created    10-06-2011
 */

class JPROFILE extends TABLE{

	private static $instance;

	private function convertValueToQuotseparated($x)
	{	if($x && !strstr($x,"'"))
		{
		        $y = explode(",",$x);
		        $z = implode("','",$y);
		        $z = "'".$z."'";
		        return $z;
		}
		return $x;
	}

	var $activatedKey; //archiving

        /**
         * @fn __construct
         * @brief Constructor function
         * @param $dbName - Database name to which the connection would be made
         */

        public function __construct($dbname="")
        {		//$this->setActivatedKey(1); //Set default activatedKey to false i.e. disable archiving
                parent::__construct($dbname); //To connect to the database
        }


        /**
         * @fn getInstance
         * @brief fetches the instance of the class
         * @param $dbName - Database name to which the connection would be made
	 * @return instance of this class
         */
        public static function getInstance($dbName='')
        {
                if(!$dbName)
					$dbName="newjs_master";
				if(isset(self::$instance))
                {
						//If different instance is required
                        if($dbName != self::$instance->dbName){
                                $class = __CLASS__;
								self::$instance = new $class($dbName);
                        }
                }
                else
                {
                        $class = __CLASS__;
                        self::$instance = new $class($dbName);
                }
                return self::$instance;
        }


        /**
         * @fn setActivatedKey
         * @brief Sets activatedKey for arhiving.
         * @param $activatedKey true/false
         */
	public function setActivatedKey($activatedKey){
		$this->activatedKey = $activatedKey;
	}

        /**
         * @fn getActivatedKey
         * @brief fetches activatedKey value
         * @return activatedKey value
         */
	public function getActivatedKey(){
		return $this->activatedKey;
	}

        /**
         * @fn getFields
         * @brief Returns column names to query
         */
	public function getFields(){
		$fields = sfConfig::get("mod_".sfContext::getInstance()->getModuleName()."_".sfContext::getInstance()->getActionName()."_LoggedInProfile");//Fields name set to module level module.yml
		if(!$fields)
			$fields = sfConfig::get("mod_".sfContext::getInstance()->getModuleName()."_default_LoggedInProfile");//Fields name set to app level module.yml
		return $fields;
	}

        /**
         * @fn get
         * @brief fetches results from JPROFILE
         * @param $value Query criteria value
         * @param $criteria Query criteria column
         * @param $fields Columns to query
         * @param $where additional where parameter
         * @return results according to criteria
         * @exception jsException for blank criteria
         * @exception PDOException for database level error handling
         */
	public function get($value="",$criteria="PROFILEID",$fields="",$extraWhereClause=null,$cache=false){
                if(!$value)
                        throw new jsException("","$criteria IS BLANK");
                try{
			$fields = $fields?$fields:$this->getFields(); //Get columns to query
                        $defaultFieldsRequired = array ("HAVE_JCONTACT","HAVEPHOTO","MOB_STATUS","LANDL_STATUS","SUBSCRIPTION","INCOMPLETE","ACTIVATED","PHOTO_DISPLAY","GENDER","PRIVACY");
                        if(!stristr($fields,"*"))
                        {
				if($fields)
				{
					foreach($defaultFieldsRequired as $k=>$fieldName)
					{
						if(!stristr($fields,$fieldName))
							$fields.=",".$fieldName;
					}
				}
				else
				{
					$fields = implode (", ",$defaultFieldsRequired);
				}
                        }
					if($cache)
						$sqlSelectDetail = "SELECT SQL_CACHE $fields FROM newjs.JPROFILE WHERE $criteria = :$criteria";
					else
						$sqlSelectDetail = "SELECT $fields FROM newjs.JPROFILE WHERE $criteria = :$criteria";
			if(is_array($extraWhereClause))
			{
				foreach($extraWhereClause as $key=>$val)
				{
					$sqlSelectDetail.=" AND $key=:$key";
					$extraBind[$key]=$val;
				}
			}

			$resSelectDetail = $this->db->prepare($sqlSelectDetail);
			$resSelectDetail->bindValue(":$criteria", $value, PDO::PARAM_INT);
			if(is_array($extraBind))
			foreach($extraBind as $key=>$val)
				$resSelectDetail->bindValue(":$key", $val);
			$resSelectDetail->execute();
			$rowSelectDetail = $resSelectDetail->fetch(PDO::FETCH_ASSOC);
			return $rowSelectDetail;
                }
                catch(PDOException $e){
                        throw new jsException($e);
                }
                return NULL;
	}

        /**
         * @fn getArray
         * @brief fetches results for multiple profiles to query from JPROFILE
         * @param $valueArray - array with field name as key and comma separated field values as the value corresp to the key - rows satisfying these values are included in the result
         * @param $excludeArray - array with field name as key and comma separated field values as the value corresp to the key - rows satisfying these values are excluded from the result
         * @param $fields Columns to query
	* @param $orderby string FIELDS ASC/DESC
	* @param $limit string 1/2/3
         * @return results Array according to criteria having incremented index
         * @exception jsException for blank criteria
         * @exception PDOException for database level error handling
         */

	public function getArray($valueArray="",$excludeArray="",$greaterThanArray="",$fields="PROFILEID",$lessThanArray="",$orderby="",$limit="",$greaterThanEqualArrayWithoutQuote="", $lessThanEqualArrayWithoutQuote="", $like="",$nolike="",$addWhereText="")
	{
		if(!$valueArray && !$excludeArray  && !$greaterThanArray && !$lessThanArray && !$lessThanEqualArrayWithoutQuote)
			throw new jsException("","no where conditions passed");
		try
		{
			if($fields!='returnOnlySql')
			{
				$fields = $fields?$fields:$this->getFields();//Get columns to query
        	                $defaultFieldsRequired = array ("HAVE_JCONTACT","HAVEPHOTO","MOB_STATUS","LANDL_STATUS","SUBSCRIPTION","INCOMPLETE","ACTIVATED","PHOTO_DISPLAY","GENDER","PRIVACY");
                	        if(!stristr($fields,"*"))
                        	{
					if($fields)
					{
						foreach($defaultFieldsRequired as $k=>$fieldName)
						{
							if(!stristr($fields,$fieldName))
								$fields.=",".$fieldName;
						}
					}
					else
					{
						$fields = implode (", ",$defaultFieldsRequired);
					}
	                        }
			}
			$sqlSelectDetail = "SELECT $fields FROM newjs.JPROFILE WHERE ";
			$count = 1;
			if(is_array($valueArray))
			{
				foreach($valueArray as $param=>$value)
				{	$value = $this->convertValueToQuotseparated($value);
					if($count == 1)
						$sqlSelectDetail.=" $param IN ($value) ";
					else
						$sqlSelectDetail.=" AND $param IN ($value) ";
					$count++;
				}
			}
			if(is_array($excludeArray))
			{
				foreach($excludeArray as $excludeParam => $excludeValue)
				{
					if($count == 1)
						$sqlSelectDetail.=" $excludeParam NOT IN ($excludeValue) ";
					else
						$sqlSelectDetail.=" AND $excludeParam NOT IN ($excludeValue) ";
					$count++;
				}
			}
			if(is_array($greaterThanArray))
			{
				foreach($greaterThanArray as $gParam => $gValue)
				{
					if($count == 1)
						$sqlSelectDetail.=" $gParam > '$gValue' ";
					else
						$sqlSelectDetail.=" AND $gParam > '$gValue' ";
					$count++;
				}
			}
			if(is_array($greaterThanEqualArrayWithoutQuote))
			{
				foreach($greaterThanEqualArrayWithoutQuote as $gParam => $gValue)
				{
					if($count == 1)
						$sqlSelectDetail.=" $gParam >= $gValue ";
					else
						$sqlSelectDetail.=" AND $gParam >= $gValue ";
					$count++;
				}
			}
			if(is_array($lessThanArray))
                        {
                                foreach($lessThanArray as $gParam => $gValue)
                                {
                                        if($count == 1)
                                                $sqlSelectDetail.=" $gParam < '$gValue' ";
                                        else
                                                $sqlSelectDetail.=" AND $gParam < '$gValue' ";
                                        $count++;
                                }
                        }
			if(is_array($lessThanEqualArrayWithoutQuote))
			{
				foreach($lessThanEqualArrayWithoutQuote as $gParam => $gValue)
				{
					if($count == 1)
						$sqlSelectDetail.=" $gParam <= $gValue ";
					else
						$sqlSelectDetail.=" AND $gParam <= $gValue ";
					$count++;
				}
			}
			if(is_array($like))
			{
				foreach($like as $gParam => $gValue)
				{
					if($count == 1)
						$sqlSelectDetail.=" $gParam LIKE '%$gValue%' ";
					else
						$sqlSelectDetail.=" AND $gParam LIKE '%$gValue%' ";
					$count++;
				}
			}
                        if(is_array($nolike))
                        {
                                foreach($nolike as $gParam => $gValue)
                                {
                                        if($count == 1)
                                                $sqlSelectDetail.=" $gParam NOT LIKE '%$gValue%' ";
                                        else
                                                $sqlSelectDetail.=" AND $gParam NOT LIKE '%$gValue%' ";
                                        $count++;
                                }
                        }

                        if($addWhereText)
                                $sqlSelectDetail.= " AND $addWhereText ";

			if($orderby)
			{
				$sqlSelectDetail.=" order by $orderby ";
			}
			if($limit)
			{
				$sqlSelectDetail.=" limit $limit ";
			}

			if($fields=='returnOnlySql')
				return $sqlSelectDetail;

			$resSelectDetail = $this->db->prepare($sqlSelectDetail);

			/*
			foreach ($valueArray as $k => $val)
			{
				$resSelectDetail->bindValue(($k+1), $val);
			}
			*/
			$resSelectDetail->execute();
			while($rowSelectDetail = $resSelectDetail->fetch(PDO::FETCH_ASSOC))
			{
				$detailArr[] = $rowSelectDetail;
			}
			return $detailArr;
		}
		catch(PDOException $e)
		{
			throw new jsException($e);
		}
		return NULL;
	}
	public function getProfileIdsThatSatisfyConditions($equality_cond_arr='',$between_cond='')
	{
		if(!$equality_cond_arr && !$between_cond)
			throw new jsException("","no where conditions passed");
		try
		{
			$sql="SELECT PROFILEID from newjs.JPROFILE where ";
			if($equality_cond_arr){
				foreach($equality_cond_arr as $var_name=>$var_val)
					$sql.="$var_name=$var_val AND";
			}
			if($between_cond)
				$sql.=" $between_cond";
			else
				$sql=substr($sql,0,-3);
			$resSelectDetail = $this->db->prepare($sql);
			$resSelectDetail->execute();
			while($rowSelectDetail = $resSelectDetail->fetch(PDO::FETCH_ASSOC))
			{
				$detailArr[] = $rowSelectDetail['PROFILEID'];
			}
			return $detailArr;
		}
		catch(PDOException $e)
		{
			throw new jsException($e);
		}
		return NULL;
	}

        /**
         * @fn edit
         * @brief edits JPROFILE
         * @param $value Query criteria value
         * @param $criteria Query criteria column
         * @param $paramArr key-value pair of columns and values to edit
         * @return edits results
         * @exception jsException for blank criteria
         * @exception PDOException for database level error handling
         */
        public function edit($paramArr=array(), $value, $criteria="PROFILEID"){
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                if(!$value)
                        throw new jsException("","$criteria IS BLANK");
                try {
			foreach($paramArr as $key=>$val){
				$set[] = $key." = :".$key;
			}
			$setValues = implode(",",$set);
                        $sqlEditProfile = "UPDATE JPROFILE SET $setValues WHERE $criteria = :$criteria";
                        $resEditProfile = $this->db->prepare($sqlEditProfile);
			foreach($paramArr as $key=>$val){
	                        $resEditProfile->bindValue(":".$key, $val);
			}
                        $resEditProfile->bindValue(":$criteria", $value);
                        $resEditProfile->execute();
                        return true;
                }
                catch(PDOException $e)
                    {
                        throw new jsException($e);
                    }
        }
        public function insert($paramArr=array()){
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
			try {
				$keys_arr=array_keys($paramArr);
				$keys= implode(",",$keys_arr);
				$values=":".implode(",:",$keys_arr);
				$sqlProfile = "INSERT INTO JPROFILE ( $keys ) VALUES( $values )";
				$resProfile = $this->db->prepare($sqlProfile);
				foreach($paramArr as $key=>$val){
					$resProfile->bindValue(":".$key, $val);
				}
				$resProfile->execute();
				return $this->db->lastInsertId();
			}
			catch(PDOException $e)
			{
				throw new jsException($e);
			}
        }

        /**
         * @fn fields
         * @brief Helper function for edit. Contains JPROFILE field names.
         */
        public function fields(){
                  return array("PROFILEID","USERNAME","PASSWORD","GENDER","RELIGION","CASTE","MANGLIK","MTONGUE","MSTATUS","DTOFBIRTH","OCCUPATION","COUNTRY_RES","CITY_RES","HEIGHT","EDU_LEVEL","EMAIL","IPADD","ENTRY_DT","MOD_DT","RELATION","COUNTRY_BIRTH","SOURCE","INCOMPLETE","PROMO","DRINK","SMOKE","HAVECHILD","RES_STATUS","BTYPE","COMPLEXION","DIET","HEARD","INCOME","CITY_BIRTH","BTIME","HANDICAPPED","NTIMES","SUBSCRIPTION","SUBSCRIPTION_EXPIRY_DT","ACTIVATED","ACTIVATE_ON","AGE","GOTHRA","NAKSHATRA","MESSENGER_ID","MESSENGER_CHANNEL","PHONE_RES","PHONE_MOB","FAMILY_BACK","SCREENING","CONTACT","SUBCASTE","YOURINFO","FAMILYINFO","SPOUSE","EDUCATION","LAST_LOGIN_DT","SHOWPHONE_RES","SHOWPHONE_MOB","HAVEPHOTO","PHOTO_DISPLAY","PHOTOSCREEN","PREACTIVATED","KEYWORDS","PHOTODATE","PHOTOGRADE","TIMESTAMP","PROMO_MAILS","SERVICE_MESSAGES","PERSONAL_MATCHES","SHOWADDRESS","UDATE","SHOWMESSENGER","PINCODE","PRIVACY","EDU_LEVEL_NEW","FATHER_INFO","SIBLING_INFO","WIFE_WORKING","JOB_INFO","MARRIED_WORKING","PARENT_CITY_SAME","PARENTS_CONTACT","SHOW_PARENTS_CONTACT","FAMILY_VALUES","SORT_DT","VERIFY_EMAIL","SHOW_HOROSCOPE","GET_SMS","STD","ISD","MOTHER_OCC","T_BROTHER","T_SISTER","M_BROTHER","M_SISTER","FAMILY_TYPE","FAMILY_STATUS","CITIZENSHIP","BLOOD_GROUP","HIV","WEIGHT","NATURE_HANDICAP","ORKUT_USERNAME","WORK_STATUS","ANCESTRAL_ORIGIN","HOROSCOPE_MATCH","SPEAK_URDU","PHONE_NUMBER_OWNER","PHONE_OWNER_NAME","MOBILE_NUMBER_OWNER","MOBILE_OWNER_NAME","RASHI","TIME_TO_CALL_START","TIME_TO_CALL_END","PHONE_WITH_STD","MOB_STATUS","LANDL_STATUS","PHONE_FLAG","CRM_TEAM","SUNSIGN","ID_PROOF_TYP","ID_PROOF_NO","SEC_SOURCE");
        }

	   /**
	   * Function to fetch profiles based on some conditions : lastlogin and registration date
	   *
	   * @param   $lastLoginOffset,$lastRegistrationOffset
	   * @return $profiles - array of desired profiles
	   */ 
		public function fetchProfilesConditionBased($lastLoginOffset,$lastRegistrationOffset)
	    {
		    try
		    {	
				$date15 = strtotime(date('Y-m-d') . $lastLoginOffset);
				$date15daysback= date('Y-m-d', $date15);
				$date6 = strtotime(date('Y-m-d H:i:s') . $lastRegistrationOffset);
				$date6monthsback = date('Y-m-d H:i:s', $date6);
				$sql="SELECT PROFILEID,EMAIL,USERNAME,COUNTRY_RES FROM JPROFILE WHERE LAST_LOGIN_DT>=:LAST_LOGIN_DT AND ENTRY_DT<=:ENTRY_DT ";
				$prep=$this->db->prepare($sql);
				$prep->bindValue(":LAST_LOGIN_DT",$date15daysback,PDO::PARAM_STR);
				$prep->bindValue(":ENTRY_DT",$date6monthsback,PDO::PARAM_STR);	
				$prep->execute();
			    while($res=$prep->fetch(PDO::FETCH_ASSOC))
                    $profilesArr[] =$res;
				return $profilesArr;
		    }
		    catch(PDOException $e)
		    {
				/*** echo the sql statement and error message ***/
				throw new jsException($e);
		    }
	    }

	    public function getProfileSelectedDetails($pid,$fields="*",$extraWhereClause=null)
	    {
	    	try
	    	{
	    		if(is_array($pid))
	    			$str = "(".implode(",", $pid).")";
	    		else
	    			$str = $pid;
	    		$sql="SELECT $fields FROM newjs.JPROFILE WHERE PROFILEID";
	    		if(is_array($pid))
	    			$sql=$sql." IN ".$str;
	    		else
	    			$sql=$sql." = ".$str;
	    		if(is_array($extraWhereClause))
	            {
		            foreach($extraWhereClause as $key=>$val)
		            {
		                if($key=='SUBSCRIPTION')
		                	$sql.=" AND $key LIKE :$key";
		                else
		                	$sql.=" AND $key=:$key";
		                $extraBind[$key]=$val;
		            }
	            }
	        
				$prep=$this->db->prepare($sql);
				if(is_array($extraBind))
		            foreach($extraBind as $key=>$val)
		            	$prep->bindValue(":$key", $val);
				$prep->execute();
			    while($res=$prep->fetch(PDO::FETCH_ASSOC))
                    $profilesArr[$res['PROFILEID']] =$res;
				return $profilesArr;
	    	}
	    	catch(Exception $e)
		    {
				/*** echo the sql statement and error message ***/
				throw new jsException($e);
		    }

	    }

        public function checkPhone($numberArray='',$isd=''){
                try
                {
                        $res=null;
			$str='';
			if($numberArray)
			{
				foreach($numberArray as $k=>$num)
				{
					if($k!=0)
						$valueArrayM['PHONE_MOB'].=", ";
					$valueArrayM['PHONE_MOB'].="'".$num."', '0".$num."', '".$isd.$num."', '+".$isd.$num."', '0".$isd.$num."'";
				}
			}
			if($valueArrayM)
			{
				$returnArr=$this->getArray($valueArrayM,'','','PROFILEID, PHONE_MOB, ISD,ACTIVATED,MOB_STATUS');
				$i=0;
				if($returnArr)
				{
					foreach($returnArr as $k=>$result)
					{
						$res[$i]["PROFILEID"]=$result['PROFILEID'];
						$res[$i]["NUMBER"]=$result['PHONE_MOB'];
						$res[$i]["TYPE"]="MOBILE";
						$res[$i]["ISD"]=$result['ISD'];
						$res[$i]["ACTIVATED"]=$result['ACTIVATED'];
						$res[$i]["MOB_STATUS"]=$result['MOB_STATUS'];

						$i++;
					}
				}
				$valueArrayL['PHONE_WITH_STD']=$valueArrayM['PHONE_MOB'];
				$returnArr=$this->getArray($valueArrayL,'','','PROFILEID, PHONE_WITH_STD, ISD,ACTIVATED,MOB_STATUS');
				if($returnArr)
				{
					foreach($returnArr as $k=>$result)
					{
                	                        $res[$i]["PROFILEID"]=$result['PROFILEID'];
						$res[$i]["ISD"]=$result['ISD'];
						$res[$i]["NUMBER"]=$result['PHONE_WITH_STD'];
						$res[$i]["ACTIVATED"]=$result['ACTIVATED'];
						$res[$i]["MOB_STATUS"]=$result['MOB_STATUS'];
						$res[$i]["TYPE"]="LANDLINE";
						$i++;
					}
				}
			}
                        else
                                throw new jsException("No phone number as Input paramter");

                        return $res;
                }
                catch(PDOException $e)
                {
                        /*** echo the sql statement and error message ***/
                        throw new jsException($e);
                }
        }

	public function Deactive($pid)
	{
		if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
		try{
			$sql="update JPROFILE set PREACTIVATED=IF(ACTIVATED<>'H',if(ACTIVATED<>'D',ACTIVATED,PREACTIVATED),PREACTIVATED), ACTIVATED='D', MOD_DT=now(),activatedKey=0 where PROFILEID=:profileid";
			$prep = $this->db->prepare($sql);
			$prep->bindValue(":profileid",$pid,PDO::PARAM_INT);
			$prep->execute();
		}
		catch(PDOException $e)
                {
                        /*** echo the sql statement and error message ***/
                        throw new jsException($e);
                }
	}

public function duplicateEmail($email)
	{
		try{
			$sql="SELECT ACTIVATED FROM newjs.JPROFILE WHERE EMAIL = :EMAIL";
			$prep = $this->db->prepare($sql);
			$prep->bindValue(":EMAIL",$email,PDO::PARAM_STR);
			$prep->execute();
			if($result = $prep->fetch(PDO::FETCH_ASSOC))
					return $result[ACTIVATED];
			else
				return -1;
		}
		catch(PDOException $e)
                {
                        /*** echo the sql statement and error message ***/
                        throw new jsException($e);
                }
	}


    public function getPassword($username)
    {
    	try{

			$sql = "SELECT PASSWORD FROM newjs.JPROFILE WHERE USERNAME =:USERNAME AND activatedKey=1";

		    $prep = $this->db->prepare($sql);
		    $prep->bindValue(":USERNAME",$username,PDO::PARAM_STR);
		    $prep->execute();
		    $res=$prep->fetch(PDO::FETCH_ASSOC);
		    $password = $res['PASSWORD'];
    	}

        catch(Exception $e){
                throw new jsException($e);
        }
        return $password;
    }

    public function getUsername($profileid)
	{
		try{

			$sql = "SELECT USERNAME FROM newjs.JPROFILE WHERE PROFILEID =:PROID";

			$prep = $this->db->prepare($sql);
			$prep->bindValue(":PROID",$profileid,PDO::PARAM_STR);
			$prep->execute();
			$res=$prep->fetch(PDO::FETCH_ASSOC);
			$username = $res['USERNAME'];
		}

		catch(Exception $e){
				throw new jsException($e);
		}
		return $username;
	}

    public function getProfileSubscription($proid){
    	try{
			$sql = "SELECT SUBSCRIPTION FROM newjs.JPROFILE WHERE PROFILEID =:PROFILEID";
		    $prep = $this->db->prepare($sql);
		    $prep->bindValue(":PROFILEID",$proid,PDO::PARAM_STR);
		    $prep->execute();
		    $res=$prep->fetch(PDO::FETCH_ASSOC);
		    $subscriptions = $res['SUBSCRIPTION'];
    	}

        catch(Exception $e){
                throw new jsException($e);
        }
        return $subscriptions;
    }
	/* Update Login Date Sort Date From Api Login Authentication
	 * @param int profileid
	 * @return int rowCount
	 */
	
	public function updateLoginSortDate($pid)
    {
		if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
		if(!$pid)
			throw new jsException("","VALUE OR TYPE IS BLANK IN insertIntoLoginHistory() of NEWJS_LOG_LOGIN_HISTORY.class.php");
		try 
		{
				$sql="update JPROFILE set LAST_LOGIN_DT=now(),SORT_DT=if(DATE_SUB(NOW(),INTERVAL 7 DAY)>SORT_DT,DATE_SUB(NOW(),INTERVAL 7 DAY),SORT_DT) where PROFILEID=:profileid";
				$prep=$this->db->prepare($sql);
				$prep->bindValue(":profileid",$pid,PDO::PARAM_INT);
				$prep->execute();
				return $prep->rowCount();
				
		}
		catch(PDOException $e)
		{			
			throw new jsException($e);
		}
	}

	public function getLoggedInProfilesForDateRange($logindDtStart, $loginDtEnd){
        	try{
                        $sql = "SELECT PROFILEID,USERNAME,ENTRY_DT FROM newjs.JPROFILE WHERE LAST_LOGIN_DT>:LOGIN_DT_START AND LAST_LOGIN_DT<=:LOGIN_DT_END";
                    	$prep = $this->db->prepare($sql);
                    	$prep->bindValue(":LOGIN_DT_START",$logindDtStart, PDO::PARAM_STR);
			$prep->bindValue(":LOGIN_DT_END",$loginDtEnd, PDO::PARAM_STR);
                    	$prep->execute();
                    	while($res=$prep->fetch(PDO::FETCH_ASSOC))
                                $profileIdArr[] =$res;
                        return $profileIdArr;
        	}
        	catch(Exception $e){
        	        throw new jsException($e);
        	}
    	}

    public function getAllPasswords($l1,$l2)
    {
        try{

		    $sql = "SELECT PROFILEID, PASSWORD FROM newjs.JPROFILE WHERE PROFILEID BETWEEN :L1 AND :L2";
                    $prep = $this->db->prepare($sql);
		    $prep->bindValue(":L1",$l1,PDO::PARAM_INT);
		    $prep->bindValue(":L2",$l2,PDO::PARAM_INT);
                    $prep->execute();
                    while($res=$prep->fetch(PDO::FETCH_ASSOC))
                    $data[] = $res;
        }

        catch(Exception $e){
                throw new jsException($e);
        }
        return $data;
    }
    public function getCity($profileIdArr){
    	try{
    		$profileIdArr = implode("','", $profileIdArr);
    		$sql = "SELECT PROFILEID, CITY_RES FROM newjs.JPROFILE WHERE COUNTRY_RES=51 AND PROFILEID IN ('".$profileIdArr."')";
    		$prep = $this->db->prepare($sql);
    		$prep->execute();
    		while($row=$prep->fetch(PDO::FETCH_ASSOC)) {
    			if($row['PROFILEID'] && $row['CITY_RES'])
    				$res[$row['PROFILEID']] = $row['CITY_RES'];
    		}
    		return $res;
    	}
    	catch(Exception $e){
    		throw new jsException($e);
    	}
    }
    public function getMembershipMailerProfiles($condition){
    	try{
	            $sql = "SELECT PROFILEID, SUBSCRIPTION, ISD,LAST_LOGIN_DT FROM newjs.JPROFILE WHERE ACTIVATED='Y' AND ".$condition;
		    $prep = $this->db->prepare($sql);
		    $prep->execute();
		    while($row=$prep->fetch(PDO::FETCH_ASSOC)) {
		    	$res[] = $row;
		    }
		    return $res;
    	}
        catch(Exception $e){
                throw new jsException($e);
        }
    }
    public function getLoggedInProfilesForPreAlloc($logindDtStart, $loginDtEnd){
	try{
		//$sql = "SELECT PROFILEID,CITY_RES FROM newjs.JPROFILE WHERE LAST_LOGIN_DT>=:LOGIN_DT_START AND LAST_LOGIN_DT<:LOGIN_DT_END";
		$sql = "SELECT PROFILEID,CITY_RES,ISD,LAST_LOGIN_DT FROM newjs.JPROFILE WHERE LAST_LOGIN_DT>=:LOGIN_DT_START AND LAST_LOGIN_DT<=:LOGIN_DT_END";
		$prep = $this->db->prepare($sql);
		$prep->bindValue(":LOGIN_DT_START",$logindDtStart, PDO::PARAM_STR);
		$prep->bindValue(":LOGIN_DT_END",$loginDtEnd, PDO::PARAM_STR);
		$prep->execute();
		while($res=$prep->fetch(PDO::FETCH_ASSOC))
			$profileIdArr[] =$res;
		return $profileIdArr;
	}
	catch(Exception $e){
		throw new jsException($e);
	}
    }
        public function fetchSourceWiseProfiles($start_dt, $end_dt)
        {
                try
                {
                        $sql="SELECT PROFILEID, SOURCE FROM newjs.JPROFILE WHERE ENTRY_DT >= :START_DATE AND ENTRY_DT <= :END_DATE";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":START_DATE", $start_dt, PDO::PARAM_STR);
                        $prep->bindValue(":END_DATE", $end_dt, PDO::PARAM_STR);
                        $prep->execute();
                        while($row = $prep->fetch(PDO::FETCH_ASSOC))
                        {
                                if($row['SOURCE'])
                                        $res[$row['SOURCE']][] = $row['PROFILEID'];
                        }
                        return $res;
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
	public function updateHaveJEducation($profiles)
	{
		if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
			if($profiles)
			{
                        $sql="UPDATE  `JPROFILE` SET  `HAVE_JEDUCATION` = 'Y' WHERE  `PROFILEID` IN (".$profiles.")";
                        $prep=$this->db->prepare($sql);
                        $prep->execute();
                        return true;
			}
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
	}
        public function getProfilesForDateRange($start_dt, $end_dt,$status)
        {
                try
                {
                        $sql="SELECT PROFILEID,USERNAME FROM newjs.JPROFILE WHERE MOD_DT >= :START_DATE AND MOD_DT <= :END_DATE AND ACTIVATED=:ACTIVATED";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":START_DATE", $start_dt, PDO::PARAM_STR);
                        $prep->bindValue(":END_DATE", $end_dt, PDO::PARAM_STR);
			$prep->bindValue(":ACTIVATED", $status, PDO::PARAM_STR);
                        $prep->execute();
                        while($row = $prep->fetch(PDO::FETCH_ASSOC))
				$res[$row['PROFILEID']] = $row['USERNAME'];
                        return $res;
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
        public function getSubscriptions($profileid,$field)
        {
                try
                {
                        $sql="SELECT ".$field." FROM newjs.JPROFILE WHERE PROFILEID=:PROFILEID";
                        $prep = $this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID",$profileid,PDO::PARAM_STR);
                        $prep->execute();
                        if($result=$prep->fetch(PDO::FETCH_ASSOC))
                        {
                                $res=$result[$field];
                        }
                }
                catch(Exception $e)
                {
                        throw new jsException($e);
                }
                return $res;
        }

        public function updateOfflineBillingDetails($profileid)
        {
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
                        $sql="UPDATE newjs.JPROFILE set PREACTIVATED = IF(ACTIVATED<>'Y', ACTIVATED, PREACTIVATED), ACTIVATED = 'Y' where PROFILEID=:PROFILEID";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        $prep->execute();
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }

        public function updateSubscriptionStatus($subscription, $profileid)
        {
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
                        $sql="UPDATE newjs.JPROFILE SET SUBSCRIPTION=:SUBSCRIPTION WHERE PROFILEID=:PROFILEID";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        $prep->bindValue(":SUBSCRIPTION", $subscription, PDO::PARAM_STR);
                        $prep->execute();
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
        
        public function updatePrivacy($privacy, $profileid)
        {
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
                        $sql="UPDATE newjs.JPROFILE SET PRIVACY=:PRIVACY , MOD_DT=now() WHERE PROFILEID=:PROFILEID and activatedKey=1";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        $prep->bindValue(":PRIVACY", $privacy, PDO::PARAM_STR);
                        $prep->execute();
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
        
        public function SelectPrivacy($profileId){
			try{
				$sql = "Select PRIVACY from newjs.JPROFILE where  activatedKey=1 and PROFILEID = :PROFILEID";
				$prep = $this->db->prepare($sql);
				$prep->bindValue(":PROFILEID", $profileId, PDO::PARAM_INT);
				$prep->execute();
				$privacyVal = $prep->fetch(PDO::FETCH_ASSOC);
				$privacyVal=$privacyVal["PRIVACY"];
				return $privacyVal;
			}
			catch(Exception $e){
				throw new jsException($e);
			}
		}
		
		
		public function SelectHide($profileid){
			try{
				$sql = "select EMAIL,ACTIVATED,ACTIVATE_ON from newjs.JPROFILE where  PROFILEID=:PROFILEID";
				$prep = $this->db->prepare($sql);
				$prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
				$prep->execute();
				$hideDetails[] = $prep->fetch(PDO::FETCH_ASSOC);
				return $hideDetails;
			}
			catch(Exception $e){
				throw new jsException($e);
			}
		}

		public function SelectActicated($profileid){
			try{
				$sql = "select PREACTIVATED from JPROFILE where  PROFILEID='$profileid'";
				$prep = $this->db->prepare($sql);
				$prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
				$prep->execute();
				$activate = $prep->fetch(PDO::FETCH_ASSOC);
				return $activate;
			}
			catch(Exception $e){
				throw new jsException($e);
			}
		}
		
		public function updateHide($privacy, $profileid,$dayinterval)
        {
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
                        $sql="update JPROFILE set PREACTIVATED=if(ACTIVATED<>'H',ACTIVATED,PREACTIVATED), ACTIVATED='H', ACTIVATE_ON=DATE_ADD(CURDATE(), INTERVAL $dayinterval DAY) where PROFILEID=:PROFILEID";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        $prep->execute();
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
        
        public function updateUnHide($privacy,$profileid)
        {
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
                        $sql="update JPROFILE set ACTIVATED=PREACTIVATED where PROFILEID=:PROFILEID";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        //$prep->bindValue(":PRIVACY", $privacy, PDO::PARAM_STR);
                        $prep->execute();
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }

        public function SelectDeleteData($profileid){
			try{
				$sql = "SELECT USERNAME,EMAIL,GENDER,ACTIVATED,CONTACT,SUBSCRIPTION FROM newjs.JPROFILE WHERE  PROFILEID=:PROFILEID";
				$prep = $this->db->prepare($sql);
				$prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
				$prep->execute();
				$activate = $prep->fetch(PDO::FETCH_ASSOC);
				return $activate;
			}
			catch(Exception $e){
				throw new jsException($e);
			}
		}


		public function updateDeleteData($profileid)
        {
			if($this->dbName=="newjs_bmsSlave")
				$this->setConnection("newjs_master");
                try
                {
                	
                        $sql="update JPROFILE set PREACTIVATED=IF(ACTIVATED<>'H',if(ACTIVATED<>'D',ACTIVATED,PREACTIVATED),PREACTIVATED), ACTIVATED='D', MOD_DT=now(),activatedKey=0 where PROFILEID=:PROFILEID";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        //$prep->bindValue(":PRIVACY", $privacy, PDO::PARAM_STR);
                        $prep->execute();
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
        public function getEmailFromUsername($username)
        {
        	try
                {
                        $sql="SELECT EMAIL FROM newjs.JPROFILE WHERE USERNAME=:USERNAME";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":USERNAME", $username, PDO::PARAM_INT);
                        $prep->execute();
                        $row = $prep->fetch(PDO::FETCH_ASSOC);
	        			return $row;
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }

        public function getEmailFromProfileId($profileid)
        {
        	try
                {
                        $sql="SELECT EMAIL FROM newjs.JPROFILE WHERE PROFILEID=:PROFILEID";
                        $prep=$this->db->prepare($sql);
                        $prep->bindValue(":PROFILEID", $profileid, PDO::PARAM_INT);
                        $prep->execute();
                        $row = $prep->fetch(PDO::FETCH_ASSOC);
	        			return $row;
                }
                catch(PDOException $e)
                {
                        throw new jsException($e);
                }
        }
                
	    /**
	   * Function to fetch profiles(registered after given date) 
	   *
	   * @param   $registerDate,$fieldsRequired(default-all JPROFILE fields)
	   * @return $profilesArr - array of desired profiles
	   */ 
		public function getRegisteredProfilesAfter($registerDate,$fieldsRequired="*")
	    {
		    try
		    {	
				$sql="SELECT ".$fieldsRequired." FROM newjs.JPROFILE WHERE ENTRY_DT>=:ENTRY_DT";				
				$prep=$this->db->prepare($sql);	
				$prep->bindValue(":ENTRY_DT",$registerDate,PDO::PARAM_STR);		
				$prep->execute();
				
			    while($res=$prep->fetch(PDO::FETCH_ASSOC))
                    $profilesArr[$res["PROFILEID"]] =$res;
				return $profilesArr;
		    }
		    catch(PDOException $e)
		    {
				/*** echo the sql statement and error message ***/
				throw new jsException($e);
		    }
	    }

	     /**
	   * Function to update incomplete status for profiles 
	   *
	   * @param   $profileid
	   * @return update results
	   */ 
	     public function updateIncompleteProfileStatus($profileIdArray)
	     {
	     	try
	     	{
	     		$inCondition=implode("','",$profileIdArray);
	     		$inCondition="'".$inCondition."'";
	     		$sql="UPDATE newjs.JPROFILE set INCOMPLETE ='Y' where PROFILEID IN ($inCondition)";
	     		$prep=$this->db->prepare($sql);
	     		$prep->execute();
	     		return true;
	     	}
	     	catch(PDOException $e)
	     	{
	     		throw new jsException($e);
	     	}
	     } 
       /**
	   * Function to fetch profiles registered in last 3 days)
	   * @return $profilesArr - array of desired profiles
	   */ 
		public function getProfileQualityRegistationData($registerDate)
	    {
		    try
		    {	
          $sql="SELECT jp.`PROFILEID` , jp.`GENDER` , jp.`MTONGUE` , jp.`ENTRY_DT` , jp.`SOURCE` , jp.`AGE` ,case when (jp.MOB_STATUS = 'Y' || jp.LANDL_STATUS = 'Y') THEN 'Y' ELSE jpc.ALT_MOB_STATUS END as MV FROM `JPROFILE` as jp LEFT JOIN JPROFILE_CONTACT as jpc ON jpc.PROFILEID = jp.profileid	 WHERE (jp.`ENTRY_DT` >= :REG_DATE AND jp.`ENTRY_DT` < CURDATE()) AND jp.`ACTIVATED` = 'Y'";				
          $prep=$this->db->prepare($sql);	
          $prep->bindValue(":REG_DATE",$registerDate,PDO::PARAM_STR);		
          $prep->execute();
          $profilesArr = array();
			    while($res=$prep->fetch(PDO::FETCH_ASSOC)){
                    $profilesArr[$res["PROFILEID"]] =$res;
          }
          return $profilesArr;
		    }
		    catch(PDOException $e)
		    {
				/*** echo the sql statement and error message ***/
				throw new jsException($e);
		    }
	    } 
    
    public function getAllSubscriptionsArr($profileArr) {
        try {
        	$profileStr = implode(",", $profileArr);
            $sql = "SELECT * FROM newjs.JPROFILE WHERE PROFILEID IN ($profileStr)";
            $prep = $this->db->prepare($sql);
            $prep->execute();
            while ($result = $prep->fetch(PDO::FETCH_ASSOC)) {
                $res[$result['PROFILEID']] = $result;
            }
        }
        catch(Exception $e) {
            throw new jsException($e);
        }
        return $res;
    }
    /*
     * this function returns profileids with entry date specified
     * @return - profileid array
     */
    public function getProfilesWithGivenRegDates($dateArr){
        try{
          if($dateArr && is_array($dateArr)){
            $sql = "SELECT PROFILEID FROM newjs.JPROFILE AS I LEFT JOIN PROFILE.DPP_REVIEW_MAILER_LOG AS L ON I.PROFILEID = L.RECEIVER WHERE ((ENTRY_DT >= :FIRST_LOWER AND ENTRY_DT < :FIRST_UPPER) OR (ENTRY_DT >= :SEC_LOWER AND ENTRY_DT < :SEC_UPPER) OR (ENTRY_DT >= :THIRD_LOWER AND ENTRY_DT < :THIRD_UPPER) OR (ENTRY_DT >= :FOURTH_LOWER AND ENTRY_DT < :FOURTH_UPPER)) AND ACTIVATED = 'Y' AND L.RECEIVER IS NULL";
            $prep = $this->db->prepare($sql);
            $prep->bindValue(":FIRST_UPPER",$dateArr['first_up'],PDO::PARAM_STR);
            $prep->bindValue(":FIRST_LOWER",$dateArr['first_low'],PDO::PARAM_STR);
            $prep->bindValue(":SEC_UPPER",$dateArr['sec_up'],PDO::PARAM_STR);
            $prep->bindValue(":SEC_LOWER",$dateArr['sec_low'],PDO::PARAM_STR);
            $prep->bindValue(":THIRD_UPPER",$dateArr['third_up'],PDO::PARAM_STR);
            $prep->bindValue(":THIRD_LOWER",$dateArr['third_low'],PDO::PARAM_STR);
            $prep->bindValue(":FOURTH_UPPER",$dateArr['fourth_up'],PDO::PARAM_STR);
            $prep->bindValue(":FOURTH_LOWER",$dateArr['fourth_low'],PDO::PARAM_STR);
            $prep->execute();
            while ($result = $prep->fetch(PDO::FETCH_ASSOC)){
                $res[] = $result;
            }
            return $res;
          }
        } catch (Exception $e) {
            throw new jsException($e);
        }
    }
    /*
     * this function return array of profileids who have registered within given dates
     * @param - date after which users have registered
     * @return - array of profiledids
     */
    public function getProfilesWithinGivenActiveDate($date){
        try{
          if($date){
            $sql = "SELECT PROFILEID FROM newjs.JPROFILE AS I LEFT JOIN PROFILE.DPP_REVIEW_MAILER_LOG AS L ON I.PROFILEID = L.RECEIVER WHERE LAST_LOGIN_DT > :date AND ACTIVATED = 'Y' AND L.RECEIVER IS NULL";
            $prep = $this->db->prepare($sql);
            $prep->bindValue(":date",$date,PDO::PARAM_STR);
            $prep->execute();
            while ($result = $prep->fetch(PDO::FETCH_ASSOC)){
                $res[] = $result;
            }
            return $res;
          }  
        } catch (Exception $e) {
            throw new jsException($e);
        }
    }
    public function getLatestValue($field) {
        try {
            $sql = "SELECT ".$field." FROM newjs.JPROFILE ORDER BY PROFILEID DESC LIMIT 1";
            $prep = $this->db->prepare($sql);
            $prep->execute();
            if($result = $prep->fetch(PDO::FETCH_ASSOC)){
                $res[] = $result;
            }
        }
        catch(Exception $e) {
            throw new jsException($e);
        }
        
        return $res;
    }

    //This function gets data for CITY_RES/MTONGUE/(AGE/GENDER) grouped by the same along with month/day as per the condition
    public function getRegistrationMisGroupedData($fromDate,$toDate,$month='',$groupType)
    {
    	try
    	{
    		if($groupType != "")
    		{
    			if($month == "")
    			{
    				$sql = "SELECT COUNT(*) AS COUNT,$groupType,EXTRACT(MONTH FROM ENTRY_DT) AS MONTH FROM newjs.JPROFILE WHERE ENTRY_DT BETWEEN :FROMDATE AND :TODATE AND DATEDIFF(VERIFY_ACTIVATED_DT,ENTRY_DT)<'3' GROUP BY $groupType,MONTH";
    			}
    			else
    			{
    				$sql = "SELECT COUNT(*) AS COUNT, $groupType,EXTRACT(DAY FROM ENTRY_DT) AS DAY FROM newjs.JPROFILE  WHERE  ENTRY_DT BETWEEN :FROMDATE AND :TODATE AND DATEDIFF(VERIFY_ACTIVATED_DT,ENTRY_DT)<'3' GROUP BY $groupType,DAY";
    			}
    		}
    		else
    		{
    			return;
    		}
    		$prep = $this->db->prepare($sql);
            $prep->bindValue(":FROMDATE",$fromDate,PDO::PARAM_STR);
            $prep->bindValue(":TODATE",$toDate,PDO::PARAM_STR);
            $prep->execute();
            while($result = $prep->fetch(PDO::FETCH_ASSOC))
			{
				$detailArr[] = $result;
			}
			return $detailArr;
            


    	}
    	catch(Exception $e) {
            throw new jsException($e);
        }
    	
    }
}
?>
