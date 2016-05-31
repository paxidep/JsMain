<?php
/**
 * @brief This class is used to add-log/fetch-log of search results.
 * @author Lavesh Rawat
 * @created 2012-07-15
 */

class SearchLogger extends SearchParamters
{
	private $ID;
	private $pid;
        private $LastSearchRequiredFor = Array(SearchTypesEnums::Advance,SearchTypesEnums::MobileSearchBand,SearchTypesEnums::Quick,SearchTypesEnums::App);

        public function __construct($loggedInProfileObj='')
        {
		parent::__construct();
                $this->possibleSearchParamters = SearchConfig::$possibleSearchParamters;
                if($loggedInProfileObj && $loggedInProfileObj->getPROFILEID())
                        $this->pid =  $loggedInProfileObj->getPROFILEID();
        }

	/*
	* This Function is to log search records 
	* @param SearchParamtersObj object-array storing the deatils of search perfomed.
	* @param totalResults int total number of results
        * @return searchId unique id of searching
	*/
	public function logSearchCriteria($SearchParamtersObj,$totalResults)
	{ 
                $flag = 0;
		$SEARCHQUERYObj = new SEARCHQUERY;
		$possibleSearchParamters = explode(",",$this->possibleSearchParamters);
                foreach($possibleSearchParamters as $v)
                {
                        if($v)
                        {
                                $getter = "get".$v;
                                $vv = $SearchParamtersObj->$getter();
                                if(isset($vv))
                                {
        	                	$updateArr[$v]="'".$vv."'";
                                }
                        }
                }
                /* Addition Things need to be stored */
		if($this->pid)
		{
			$key = 'PROFILEID';
			$updateArr[$key]=$this->pid;
		}
		else
		{
			if(isset($_COOKIE["ISEARCH"]))
			{
				$updateArr["ISEARCH_PROFILEID"]=$_COOKIE["ISEARCH"];
			}
			else
			{
				if(isset($_COOKIE["TRACK_COOKIE_SEARCH"]))
					$updateArr["TRACKING_COOKIE_ID"] = $_COOKIE["TRACK_COOKIE_SEARCH"];
				else
				{
					$updateArr["TRACKING_COOKIE_ID"] = "NEW";
					$flag = 1;
				}
			}
		}

                $key= 'RECORDCOUNT';
		$updateArr[$key] = $totalResults;
                
                /* Addition Things need to be stored */
                $searchId = $SEARCHQUERYObj->addRecords($updateArr);
                if($this->pid && in_array(trim($updateArr["SEARCH_TYPE"],"/'"),$this->LastSearchRequiredFor))
		{
                        $search_LATEST_SEARCHQUERYObj = new search_LATEST_SEARCHQUERY;
			$paramArray["ID"]=$searchId;
			$paramArray["PROFILEID"]=$this->pid;
                        $paramArray["SEARCH_CHANNEL"]=CommonFunction::getChannel();
                        $search_LATEST_SEARCHQUERYObj->insertOrReplace($paramArray); 
                       
		}
		if($flag == 1)
			setcookie("TRACK_COOKIE_SEARCH",$searchId,time()+60*60*24*120,'/');
		$SearchParamtersObj->setID($searchId);
		return $searchId;
	}

	/*
	* This function is to set the SearchLoggerObjects(searchParamters) based on unique id.
	* @param id int unique id of search logging table(SEARCHQUERY)	
	*/
	public function getSearchCriteria($id,$ifNonCritical="")
	{
                $paramArr['ID'] = $id;

                /**
                * called the store(SEARCHQUERY) to get details for the id.
                * set these details to SearchParamtersObj.
                */
		$keySC = "SearchQueryLog$id";
		$arr = unserialize(JsMemcache::getInstance()->get($keySC));
		if(!$arr)
		{
               		$SEARCHQUERYobj = new SEARCHQUERY;
	                $arr = $SEARCHQUERYobj->get($paramArr,$this->possibleSearchParamters,$ifNonCritical);
			if($arr)
			{
				JsMemcache::getInstance()->set($keySC,serialize($arr));
			}
		}

                if(is_array($arr[0]))
                {
                        foreach($arr[0] as $field=>$value)
                        {
                                if(strstr($this->possibleSearchParamters,$field))
                                        eval ('$this->set'.$field.'($value);');
                        }
                        unset($arr);
                }
                $request    = sfContext::getInstance()->getRequest();
                $searchBasedParam = $request->getParameter("searchBasedParam");
                if($searchBasedParam && array_key_exists($searchBasedParam,searchCriteriaParamsEnum::$searchCriteria)){
                    $settingField = searchCriteriaParamsEnum::$searchCriteria[$searchBasedParam]['field'];
                    $settingValue = searchCriteriaParamsEnum::$searchCriteria[$searchBasedParam]['value'];
                    eval ('$this->set'.$settingField.'(\''.$settingValue.'\');');
                }

	}
	
	public function getLastSearchCriteria($id,$stype='')
	{
                $search_LATEST_SEARCHQUERYObj = new search_LATEST_SEARCHQUERY;
		$paramArray["PROFILEID"]= $id;
                $paramArray["SEARCH_CHANNEL"]=CommonFunction::getChannel();
                if($stype)
                        $paramArray["SEARCH_TYPE"]=$stype;
                $arr =  $search_LATEST_SEARCHQUERYObj->getSearchQuery($paramArray,$this->possibleSearchParamters);
		if(is_array($arr))
                {
                        foreach($arr as $field=>$value)
                        {
                                if(strstr($this->possibleSearchParamters,$field))
                                        eval ('$this->set'.$field.'($value);');
                        }
                        unset($arr);
                        return 1;
                }
                return 0;

	}
	

        /* setters and getters*/
        function setID($ID) { $this->ID = $ID; }
        function getID() { return $this->ID; }
        /* setters and getters*/
}
?>