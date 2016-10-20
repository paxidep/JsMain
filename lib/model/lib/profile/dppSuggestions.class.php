<?php

class dppSuggestions
{
	//This function fetches dppSuggestion values to be shown and returns it to the calling function
	public function getDppSuggestions($trendsArr,$type,$valArr)
	{
		if(is_array($trendsArr))
		{
			$percentileArr = $trendsArr[$type."_VALUE_PERCENTILE"];
			$trendVal = $this->getTrendsValues($percentileArr);	
			$valueArr = $this->getDppSuggestionsFromTrends($trendVal,$type,$valArr);
		}
		if(count($valueArr["data"])< DppAutoSuggestEnum::$NO_OF_DPP_SUGGESTIONS)
		{
			if($type == "CITY")
			{	
				foreach($valArr as $key=>$value)
				{
					$cityData = $this->getNCRMumbaiCity($value);
					if($cityData)
					{
						$valueArr['data'][$cityData['KEY']]=$cityData['VALUE'];
					}					
				}
			}
			elseif ($type == "EDUCATION" || $type == "OCCUPATION")
			{
				$valueArr = $this->getSuggestionsFromGroupings($valueArr,$type,$valArr);
			}
			else
			{
				foreach($valArr as $k2=>$v2)
				{
					$suggestedValueArr[$v2] = $this->getDppSuggestionsForFilledValues($type,$v2);
				}
				if(is_array($suggestedValueArr))
				{
					$valueArr = $this->getRemainingSuggestionValues($suggestedValueArr,$type,count($valueArr["data"]),$valueArr,$valArr);	
				}
			}
										
		}
		$valueArr["type"] = $type;
		return $valueArr;
	}

	//This function uses the array in $trendsArr and converts in into the desired key=>value paired Array
	public function getTrendsValues($val)
	{
		$tempArray=explode("|",$val);
		$count = count($tempArray);
		unset($tempArray[0]);
		unset($tempArray[$count-1]);
		if(is_array($tempArray))
		{
			foreach($tempArray as $value)
			{
				list($value,$trend)=explode("#",$value);
				$resultTrend[$value]=$trend;

			}
		}
		return $resultTrend;
	}

	//This function takes the trendsArr for each $type and gets the trends data to be sent as apiResponse
	public function getDppSuggestionsFromTrends($trendsArr,$type,$valArr)
	{
		$count = 0;
		foreach($trendsArr as $k1=>$v1)
		{
			if($count < DppAutoSuggestEnum::$NO_OF_DPP_SUGGESTIONS)
			{
				if(!in_array($k1,$valArr) && $type != "CITY")
				{
					$valueArr["data"][$k1] = $this->getFieldMapValueForTrends($k1,$type);
					$count++;
				}
				elseif(!in_array($k1,$valArr))
				{
					$this->stateIndiaArr = $this->getFieldMapLabels("state_india",'',1);//FieldMap::getFieldLabel("state_india",'',1);
					$this->cityIndiaArr = $this->getFieldMapLabels("city_india",'',1);//FieldMap::getFieldLabel("city_india",'',1);
					if(array_key_exists($k1, $this->stateIndiaArr) || array_key_exists($k1, $this->cityIndiaArr))
					{
						$valueArr["data"][$k1] = $this->getFieldMapValueForTrends($k1,$type);
						$count++;
					}
				}
			}
			else
			{
				break; //check 
			}
		}
		return $valueArr;


	}

	//This function gets the value for the $key specified for the given $type
	public function getFieldMapValueForTrends($key,$type)
	{
		$type = $this->getType($type);
		if($type != "city")
		{
			$returnValue = $this->getFieldMapLabels($type,$key,'');//FieldMap::getFieldlabel($type,$key,'');
		}
		else
		{
			if(array_key_exists($key, $this->stateIndiaArr))
			{
				$returnValue = $this->stateIndiaArr[$key];
			}
			elseif(array_key_exists($key, $this->cityIndiaArr))
			{
				$returnValue = $this->cityIndiaArr[$key];
			}
		}
		return $returnValue;
	}

	//this functions calls the dppAutoSuggestValue function to get the suggested Values corresponding to each input value based on the key and type
	public function getDppSuggestionsForFilledValues($type,$fieldValue)
	{
		$dppData = DppAutoSuggestEnum::$FIELD_ID_ARRAY;		
		foreach($dppData as $key=>$value)
		{
			if($value == $type)
			{
				$suggestedValue = $this->getValueFromDppAutoSuggestValue($type,$key,$fieldValue); //DppAutoSuggestValue::getDppSuggestionsForFilledValues($type,$key,$fieldValue);
			}
		}
		$suggestedValue = explode("','",trim($suggestedValue,"'"));
		return $suggestedValue;
	}

	//For the suggestedValueArr formed, frequency distribution is calculated and sorted array is then picked to fill in the remaining values.
	public function getRemainingSuggestionValues($suggestedValueArr,$type,$valueArrDataCount,$valueArr,$valArr)
	{
		$type = $this->getType($type);
		//frequency distribution calculation
		$suggestedValueCountArr = $this->getFrequencyDistributedArrForCasteMtongue($suggestedValueArr);
		$suggestedValueCountArr = $this->getSortedSuggestionArr($suggestedValueCountArr);
		$remainingCount = DppAutoSuggestEnum::$NO_OF_DPP_SUGGESTIONS - $valueArrDataCount;
		foreach($suggestedValueCountArr as $fieldId=>$freqDistribution)
		{
			if($remainingCount != 0)
			{
				if(!array_key_exists($fieldId, $valueArr["data"]) && !in_array($fieldId,$valArr))
				{
					$valueArr["data"][$fieldId] =  $this->getFieldMapLabels($type,$fieldId,'');//FieldMap::getFieldlabel($type,$fieldId,'');
					$remainingCount--;
				}				
			}									
			else
			{
				break;
			}
		}
		return $valueArr;	
	}

	//This functions finds dppSuggestions values for Education and Occupation depending on the frequency distribution of groupings that the input values belong to
	public function getSuggestionsFromGroupings($valueArr,$type,$valArr)
	{	
		$SuggestionArr = array();
		$GroupingArr = $this->getGroupingArr($type);
		//to find the frequency distribution of grouping array based on the input values sent
		$suggestionArr = $this->getFrequencyDistributedArr($valArr,$GroupingArr);
		$suggestionArr = $this->getSortedSuggestionArr($suggestionArr);
		$remainingCount = DppAutoSuggestEnum::$NO_OF_DPP_SUGGESTIONS - count($valueArr["data"]);
		$valueArr = $this->fillRemainingValuesInEduOccValueArr($remainingCount,$suggestionArr,$valueArr,$valArr,$GroupingArr,$type);	
		return $valueArr;
	}

	//This function checks redis if a value exists corresponding to the key specified or else sends a query to fetch trendsArr
	public function getTrendsArr($profileId,$percentileFields,$trendsObj)
	{
		$pidKey = $profileId."_dppSuggestions";
		$trendsArr = dppSuggestionsCacheLib::getInstance()->getHashValueForKey($pidKey);
		if($trendsArr == "noKey" || $trendsArr == false)
		{
			
			$trendsArr = $trendsObj->getTrendsScore($profileId,$percentileFields);
			//dppSuggestionsCacheLib::getInstance()->storeHashValueForKey($pidKey,$trendsArr);
			return $trendsArr;
		}
		else
		{        		
			return $trendsArr;        		
		}
	}

	//This function gets labels from FieldMapLib depending on $labels,$value,$returnArr
	public function getFieldMapLabels($label,$value,$returnArr='')
	{
		return FieldMap::getFieldlabel($label,$value,$returnArr);
	}

	//This function calls function of dppAutoSuggestValue to getDppSuggestion values
	public function getValueFromDppAutoSuggestValue($type,$key,$fieldValue)
	{
		return DppAutoSuggestValue::getDppSuggestionsForFilledValues($type,$key,$fieldValue);
	}

	//This function is used to get frequency distribution array
	public function getFrequencyDistributedArr($valArr,$GroupingArr)
	{
		$suggestionArr = array();
		foreach($valArr as $k1=>$v1)
		{
			foreach($GroupingArr as $groupingKey=>$vArr)
			{
				foreach($vArr as $k2=>$v2)
				{
					if($v1 == $v2)
					{
						if(array_key_exists($groupingKey, $suggestionArr))
						{
							$suggestionArr[$groupingKey]++;
						}
						else
						{
							$suggestionArr[$groupingKey]=1;
						}
					}
				}
			}
		}
		return $suggestionArr;
	}	

	// This function sorts the array
	public function getSortedSuggestionArr($suggestionArr)
	{
		arsort($suggestionArr);
		return $suggestionArr;
	}

	//This function checks if mumbai region or delhi region value exists in $value and accordingly puts value in $value
	public function getNCRMumbaiCity($value)
	{
		if(in_array($value,DppAutoSuggestEnum::$delhiNCRCities))
		{
			$key = implode(',',DppAutoSuggestEnum::$delhiNCRCities);
			$city['VALUE'] = "Delhi NCR";
			$city['KEY']=$key;
		}
		if(in_array($value,DppAutoSuggestEnum::$mumbaiRegion))
		{
			$key = implode(',',DppAutoSuggestEnum::$mumbaiRegion);
			$city['VALUE'] = "Mumbai Region";
			$city['KEY']=$key;
		}
		return $city;
	}

	//This function gets Frequency Distribution for Caste and Mtongue
	public function getFrequencyDistributedArrForCasteMtongue($suggestedValueArr)
	{	
		foreach($suggestedValueArr as $fieldId =>$vArr)
		{
			foreach($vArr as $k3=>$v3)
			{
				if($v3!="")
				{
					if(array_key_exists($v3, $suggestedValueCountArr))
					{
						$suggestedValueCountArr[$v3]++;
					}
					else
					{
						$suggestedValueCountArr[$v3] = 1;
					}
				}
				
			}
		}
		return $suggestedValueCountArr;
	}

	// This function fills remaining values in Education and occupation section of value array
	public function fillRemainingValuesInEduOccValueArr($remainingCount,$SuggestionArr,$valueArr,$valArr,$GroupingArr,$type)
	{
		//This loop is on sorted grouping array based on which values corresponding each grouping are fetched and evaluated
		foreach($SuggestionArr as $groupingKey=>$freqDistribution)
		{
			if($remainingCount != 0)
			{
				if(array_key_exists($groupingKey, $GroupingArr))
				{
					$ValArr1 = $GroupingArr[$groupingKey];
				}

				foreach($ValArr1 as $k=>$v)
				{
					if(!array_key_exists($v, $valueArr["data"]) && !in_array($v,$valArr) && $remainingCount >0)
					{
						$valueArr["data"][$v] =  $this->getFieldMapLabels(strtolower($type),$v,'');//FieldMap::getFieldlabel(strtolower($type),$v,'');
						$remainingCount--;
					}
				}						
			}									
			else
			{
				break;
			}
		}
		return $valueArr;
	}

	public function getType($type)
	{
		$type = strtolower($type);
		if($type == "mtongue")
		{
			$type = "community";
		}
		return $type;
	}

	public function getGroupingArr($type)
	{
		if($type == "EDUCATION")
		{
			$GroupingArr  = $this->getFieldMapLabels(DppAutoSuggestEnum::$eduGrouping,'',1);//FieldMap::getFieldlabel(DppAutoSuggestEnum::$eduGrouping,'',1);
		}
		if($type == "OCCUPATION")
		{
			$GroupingArr  = $this->getFieldMapLabels(DppAutoSuggestEnum::$occupationGrouping,'',1);//FieldMap::getFieldlabel(DppAutoSuggestEnum::$occupationGrouping,'',1);
		}
		foreach($GroupingArr as $groupingKey => $stringVal)
		{
			$GroupingArr[$groupingKey] = explode(",",$stringVal);
		}

		return $GroupingArr;
	}
}
?>