<?php
/**
 * crmAllocation actions.
 *
 * @package    jeevansathi
 * @subpackage crmAllocation
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class crmAllocationActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  // Get Outbound Process List for Executives
  public function executeOutboundProcessList(sfWebRequest $request)
  {
        $this->cid      =$request->getParameter("cid");

        //JSB9 CRM Tracking
        $this->getResponse()->setSlot("optionaljsb9Key", Jsb9Enum::jsCrmOubPageUrl);

        $processObj             =new PROCESS();
	$crmUtilityObj          =new crmUtility();
	$agentBucketHandlerObj  =new AgentBucketHandler();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

	$this->agentName =$agentAllocDetailsObj->fetchAgentName($this->cid); 
	$processObj->setExecutive($this->agentName);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('OUTBOUND_PROCESS_COUNT');

        $this->privilege	=$agentAllocDetailsObj->getprivilage($this->cid);
	$this->linkArr		=$crmUtilityObj->fetchPrivilegeLinks($this->privilege);	
	$profileCountArr 	=$agentBucketHandlerObj->fetchOutboundProfilesCount($processObj);

	//Set template parameters
	$this->followupProfilesForDay 		=$profileCountArr['FOLLOWUP'];
	$this->ffollowupProfilesForDay  	=$profileCountArr['FFOLLOWUP'];
	$this->newProfilesForDay		=$profileCountArr['NEW_PROFILES'];
	$this->onlineNewProfiles	        =$profileCountArr['ONLINE_NEW_PROFILES'];
	$this->subscriptionExpiringProfiles  	=$profileCountArr['SUB_EXPIRY'];
	$this->renewalProfiles			=$profileCountArr['RENEWAL'];
	$this->upsellProfiles			=$profileCountArr['UPSELL'];
	$this->profilesRenewalNotDue		=$profileCountArr['RENEWAL_NOT_DUE'];
	$this->prevHandledProfiles		=$profileCountArr['HANDLED'];
	$this->ftaProfiles              	=$profileCountArr['FTA'];
	$this->fieldSalesProfiles		=$profileCountArr['FIELD_SALES'];
	
	//end
	
	$this->setTemplate('outboundProcessList');
  }

  // Get New Profiles for the Day
  public function executeOutboundProcess(sfWebRequest $request)
  {
        $this->cid		=$request->getParameter("cid");
	$flag			=$request->getParameter("flag");	
	$pageIndex              =$request->getParameter("pageIndex");

        //JSB9 CRM Tracking
        $this->getResponse()->setSlot("optionaljsb9Key", Jsb9Enum::jsCrmOubLinksUrl);

	// Pagination setting
	$pageLimit =25;
        if (!$pageIndex ){
                $pageIndex =0;
                $currentPage =1;
        }
        else
                $currentPage = ($pageIndex/$pageLimit)+1;
	// ends

	$processObj		=new PROCESS();
	$crmUtilityObj		=new crmUtility();
	$agentBucketHandlerObj  =new AgentBucketHandler();
	$agentAllocDetailsObj   =new AgentAllocationDetails();

	$this->agentName =$agentAllocDetailsObj->fetchAgentName($this->cid);
	$processObj->setExecutive($this->agentName);
	$processObj->setProcessName('Allocation');
	$processObj->setMethod('OUTBOUND_PROCESS');
	
	if($flag=='F' || $flag=='FF'){
		$this->yy1    	=$request->getParameter("yy1");
		$this->mm1    	=$request->getParameter("mm1");
		$this->dd1    	=$request->getParameter("dd1");
		$this->yy2    	=$request->getParameter("yy2");
		$this->mm2    	=$request->getParameter("mm2");
		$this->dd2    	=$request->getParameter("dd2");

		$datesArr 		=$crmUtilityObj->fetchDates($request);
		$getold			=$datesArr['getold'];
		$this->dateDropdown 	=$crmUtilityObj->fetchDateDropdown();

		$processObj->setStartDate($datesArr['start_dt']);
		$processObj->setEndDate($datesArr['end_dt']);
		if($flag=='F')
			$processObj->setSubMethod('FOLLOWUP');
		elseif($flag=='FF')
			$processObj->setSubMethod('FFOLLOWUP');
	}
	elseif($flag=='N')
                $processObj->setSubMethod('NEW_PROFILES');
        elseif($flag=='ONP')
                $processObj->setSubMethod('ONLINE_NEW_PROFILES');
	elseif($flag=='S')
                $processObj->setSubMethod('SUB_EXPIRY');
	elseif($flag=='R')
                $processObj->setSubMethod('RENEWAL');
	elseif($flag=='U')
                $processObj->setSubMethod('UPSELL');
	elseif($flag=='RND')
                $processObj->setSubMethod('RENEWAL_NOT_DUE');
	elseif($flag=='C')
                $processObj->setSubMethod('HANDLED');
        elseif($flag=='FTA')
                $processObj->setSubMethod('FTA');
	elseif($flag=='FP'){
		$processObj->setSubMethod('FAILED_PAYMENT');
		$this->orders ='Y';
	}
	elseif($flag=='PH'){
		$processObj->setSubMethod('PAYMENT_HITS');
		$this->orders ='Y';
	}
        elseif($flag=='NFP')
                $processObj->setSubMethod('NEW_FAILED_PAYMENT');
	elseif($flag=='WL')
                $processObj->setSubMethod('WEBMASTER_LEADS');
        elseif($flag=='NFS')
                $processObj->setSubMethod('FIELD_SALES');

	$this->subMethod	=$processObj->getSubMethod();
	$profileCountArr	=$agentBucketHandlerObj->fetchOutboundProfilesCount($processObj);	
	$this->profilesArr	=$agentBucketHandlerObj->fetchOutboundProfilesDisplayList($processObj,$pageLimit,$pageIndex);
	$totalRec               =$profileCountArr[$this->subMethod];

	$linkUrl		=sfConfig::get("app_site_url")."/operations.php/crmAllocation/outboundProcess";
        $this->pageLinkVar 	=$crmUtilityObj->pageLink($pageLimit,$totalRec,$currentPage,$this->cid,$linkUrl,'',$flag,$getold);
	$this->totalPages 	=ceil($totalRec/$pageLimit);
	$this->currentPage	=$currentPage;
	$this->serialNo		=$pageIndex;

	$this->setTemplate('outboundProcess');
  }
  // allocate the profile to the agent by Outbound Process
  public function executeAgentAllocation(sfWebRequest $request)
  {	
        $this->cid      	=$request->getParameter("cid");
	$this->profileid	=$request->getParameter("profileid");
	$this->subMethod	=$request->getParameter("subMethod");
	$this->orders		=$request->getParameter("orders");		
	$submit			=$request->getParameter("submit");
	if(!$this->profileid){
		$this->forward('commoninterface','CrmLogin');
	}

	$processObj		=new PROCESS();
	$crmUtilityObj          =new crmUtility();	
	$agentBucketHandlerObj  =new AgentBucketHandler();
	$agentAllocDetailsObj   =new AgentAllocationDetails();


        $checksum               =md5($this->profileid)."i".$this->profileid;
        $this->agentName        =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $privilege              =$agentAllocDetailsObj->getprivilage($this->cid);

        $processObj->setProcessName('Allocation');
        $processObj->setMethod('OUTBOUND');
	$processObj->setSubMethod($this->subMethod);	
        $processObj->setProfiles($this->profileid);
	$processObj->setExecutive($this->agentName);

	// form submission
	if($submit)
	{
		$status			=$request->getParameter("follow");
		$email			=$request->getParameter("email");
		$alternatePhone 	=$request->getParameter("alternatePhone");
		$resPhone		=$request->getParameter("resPhone");
		$mobPhone		=$request->getParameter("mobPhone");	
		$comments		=$request->getParameter("comments");
		$willPay        	=$request->getParameter("willPay");
		$reason			=$request->getParameter("reason");
		$this->username		=$request->getParameter("username");
		$follow_date    	=$request->getParameter("follow_date");
		$follow_hour    	=$request->getParameter("follow_hour");
		$follow_min     	=$request->getParameter("follow_min");
		$error			=false;

		// Disposition value parsing  
		if($willPay=='AA|X')
			$willPayArr =explode("|X",$willPay);
		else
			$willPayArr =explode("|X|",$willPay);
		$willPay =$willPayArr[0];

		// Error condition check
                if(($status=="F" && !$follow_date) || ($alternatePhone && !is_numeric($alternatePhone)) || !$willPay || !$reason || !$comments){
			$this->checkDetails ='Y';
			$error =true;
        	}        

		// Profile allocation 
		if(!$error){
			if(!$status)
				$status='C';
			if($status=='F'){
				if(!$follow_min)
					$follow_min	='00';
				$follow_hour            =$crmUtilityObj->fetchHourDetails($follow_hour);
				$follow_time            =date("Y-m-d",JSstrToTime($follow_date))." ".$follow_hour.":".$follow_min.":"."00";
			}
			
			$paramsArr =array("PROFILEID"=>"$this->profileid","ALLOTED_TO"=>"$this->agentName","STATUS"=>"$status","EMAIL"=>"$email","ALTERNATE_NO"=>"$alternatePhone","RES_NO"=>"$resPhone","MOB_NO"=>"$mobPhone","COMMENTS"=>"$comments","WILL_PAY"=>"$willPay","REASON"=>"$reason","RELAX_DAYS"=>"$relaxDays","FOLLOWUP_TIME"=>"$follow_time","USERNAME"=>"$this->username","ALLOT_TIME"=>date("Y-m-d H:i:s"),"PRIVILEGE"=>"$privilege","ALLOTED_BY"=>"$this->agentName","MODE"=>"O","ORDERS"=>"$this->orders");

			$agentBucketHandlerObj->allocate($processObj,$paramsArr);
			$this->allocatedSuccessfully =true;
		}
	}

	$this->details          =$agentAllocDetailsObj->fetchProfileDisplayDetails($this->profileid,$privilege);
        $temp_email             =explode("@",$this->details['EMAIL']);
	$subscription		=$this->details['SUBSCRIPTION'];
        $this->details['EMAIL'] =$temp_email[0]."@xxx.com";
	$this->willPay          =$crmUtilityObj->populateDisposition($this->details['WILL_PAY']);
	$this->followupDate     =$crmUtilityObj->getFollowupDate($this->profileid,$this->agentName);	

	$this->history		=$this->details['HISTORY'];
	$this->username		=$this->details['USERNAME'];
	$this->orderDetails     =$this->details['ORDER_DETAILS'];
	$this->pmsg 		=$crmUtilityObj->getCurlData($this->profileid,$this->username,$this->cid);
    $this->pid = $this->profileid;
    $incentiveMAINADMINObj = new incentive_MAIN_ADMIN();
    $allotedAgent = $incentiveMAINADMINObj->getAllotedExecForProfile($this->profileid);
    if($allotedAgent == $this->agentName)
        $this->ISALLOTED = "Y";
    
        if(strstr($subscription,"F")!="")
                $this->paidProfile ='1';

	$apiAuthenticationObj=AuthenticationFactory::getAuthenicationObj(null);
	$apiAuthenticationObj->setTrackLogin(false);
	$apiAuthenticationObj->removeLoginCookies();
	$this->checksum=md5($this->profileid)."i".$this->profileid;
	$this->profileChecksum =$this->checksum;
	$this->echecksum=$apiAuthenticationObj->js_encrypt($this->checksum);
        $this->setTemplate('agentAllocation');
  }
  // allocate the profile to the agent by Outbound Process

  public function executeInboundAllocation(sfWebRequest $request)	
  {
        $this->cid              =$request->getParameter("cid");
        $this->username        	=$request->getParameter("username");
        $submit                 =$request->getParameter("submit");

        $processObj             =new PROCESS();
        $crmUtilityObj          =new crmUtility();
        $agentBucketHandlerObj  =new AgentBucketHandler();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName        =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $privilege              =$agentAllocDetailsObj->getprivilage($this->cid);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('INBOUND');
        $processObj->setExecutive($this->agentName);

        // form submission
        if($submit)
        {
		$this->error ='';
		if($this->username){
                	$jProfilesDetails       =$agentAllocDetailsObj->fetchJProfileDetails($this->username,'PROFILEID');
                	$this->profileid        =$jProfilesDetails['PROFILEID'];
			if(!$this->profileid)
				$this->errorUsername ='WRONG_USERNAME';
		}
		else
			$this->errorUsername ='NO_USERNAME';
		
		if($submit=='submit'){
                	$this->email	              =$request->getParameter("email");
                	$this->resPhone               =$request->getParameter("resPhone");
                	$this->mobPhone               =$request->getParameter("mobPhone");
			$this->alternatePhone         =$request->getParameter("alternatePhone");
			$this->mobAltPhone            =$request->getParameter("mobAltPhone");
			$this->call_source            =$request->getParameter("call_source");
			$this->query_type             =$request->getParameter("query_type");
                	$willPayVal                   =$request->getParameter("willPay");
			$this->reason                 =$request->getParameter("reason");	
			$this->comments               =$request->getParameter("comments");
	                $this->follow_date            =$request->getParameter("follow_date");
	                $follow_hour                  =$request->getParameter("follow_hour");
	                $follow_min                   =$request->getParameter("follow_min");
			$showDetail                   =$request->getParameter("showDetail");

	                // Disposition value parsing  
	                if($willPayVal=='AA|X')
	                        $willPayArr =explode("|X",$willPayVal);
	                else
	                        $willPayArr =explode("|X|",$willPayVal);
	                $this->willPayVal 	=$willPayArr[0];
			$followupDateSet 	=$this->follow_date."-".$follow_hour."-".$follow_min;

	           	// Error condition check
			if(!$this->username || !$this->email || !$this->comments || !$this->call_source || !$this->query_type || !$this->willPayVal || !$this->reason || !$this->follow_date)
                	        $this->error ='Y';
			if($this->email){
				$emailChk =$crmUtilityObj->checkemail($this->email);
				if($emailChk)
					$this->errorEmail ='WRONG_EMAIL';
			}
			if($this->username && !$this->errorUsername){
				$this->errorCriteria 	=$agentBucketHandlerObj->checkLocationCriteriaForAllotment($this->username,$this->agentName);	
				if(!$this->errorCriteria)
					$this->errorPaid 	=$agentAllocDetailsObj->checkPaidProfile($this->profileid);		
			}
			if($this->errorUsername || $this->errorEmail || $this->errorCriteria || $this->errorPaid)
				$this->error ='Y';

	                // Profile allocation 
	                if(!$this->error){

				$status ='C';
				if($this->follow_date){
                                	if(!$follow_min)
                                        	$follow_min     ='00';
                                	$follow_hour            =$crmUtilityObj->fetchHourDetails($follow_hour);
                                	$this->follow_time      =date("Y-m-d",JSstrToTime($this->follow_date))." ".$follow_hour.":".$follow_min.":"."00";
					$status			='F';
				}

				$paramsArr =array("ALLOTED_TO"=>"$this->agentName","STATUS"=>"$status","ALTERNATE_NO"=>"$this->alternatePhone","RES_NO"=>"$this->resPhone","MOB_NO"=>"$this->mobPhone","ALT_MOB"=>"$this->mobAltPhone","COMMENTS"=>"$this->comments","WILL_PAY"=>"$this->willPayVal","REASON"=>"$this->reason","FOLLOWUP_TIME"=>"$this->follow_time","EMAIL"=>"$this->email","USERNAME"=>"$this->username","PROFILEID"=>"$this->profileid","PRIVILEGE"=>"$privilege","ALLOTED_BY"=>"$this->agentName","ALLOT_TIME"=>date("Y-m-d H:i:s"),"MODE"=>"I","CALL_SOURCE"=>"$this->call_source","QUERY_TYPE"=>"$this->query_type");
	
	                        $agentBucketHandlerObj->allocate($processObj,$paramsArr);
				$this->exceededAllocationCount 	=$agentAllocDetailsObj->fetchExceededAllocationCount($this->agentName);
				$this->allocationLimit		=$agentAllocDetailsObj->fetchAllocationLimit();
	                        $this->allocatedSuccessfully 	=true;
        	        }
        	}

		if(($submit=='Get History' || $submit=='history' || $showDetail) && !$this->errorUsername){
	        	$this->details          =$agentAllocDetailsObj->fetchProfileDisplayDetails($this->profileid,$privilege);
		        $this->history          =$this->details['HISTORY'];
			$this->pmsg             =$crmUtilityObj->getCurlData($this->profileid,$this->username,$this->cid);
			$this->showDetail	=true;		
		}
	}
	$this->followupDate =$crmUtilityObj->getFollowupDate($this->profileid,$this->agentName,$followupDateSet);
	$this->callSource   =$crmUtilityObj->populateCallSource();
        $this->queryType    =$crmUtilityObj->populateQueryType();
	$this->willPay      =$crmUtilityObj->populateDisposition($this->willPayVal);
       	$this->setTemplate('inboundAllocation');
  }
  public function executeManualAllocation(sfWebRequest $request)
  {
        $this->cid              =$request->getParameter("cid");
        $submit                 =$request->getParameter("submit");

        $processObj             =new PROCESS();
        $crmUtilityObj          =new crmUtility();
        $agentBucketHandlerObj  =new AgentBucketHandler();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName        =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $this->privilege        =$agentAllocDetailsObj->getprivilage($this->cid);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('MANUAL');
        $processObj->setExecutive($this->agentName);

        // form submission
        if($submit)
        {
		$this->username         =$request->getParameter("username");
		$this->allot_to         =$request->getParameter("allot_to");
		$this->call_source      =$request->getParameter("call_source");
		$this->allot_time       =$request->getParameter("allot_time");
                $this->comments         =$request->getParameter("comments");
                $this->follow_date      =$request->getParameter("follow_date");
                $follow_hour            =$request->getParameter("follow_hour");
                $follow_min             =$request->getParameter("follow_min");

                // Error condition check
                if($this->username){
                        $jProfilesDetails       =$agentAllocDetailsObj->fetchJProfileDetails($this->username,'SUBSCRIPTION');
                        $subscription           =$jProfilesDetails['SUBSCRIPTION'];
                        if(strstr($subscription,"F")!="")
                                $this->paidProfile ='1';
		}	
		$this->error ='';
		$followupDateSet =$this->follow_date."-".$follow_hour."-".$follow_min;
		if(!$this->username || !$this->call_source || !$this->comments || (!$this->paidProfile && !$this->follow_date))
			$this->error ='Y';
		if($this->username){
			$criteria =$agentBucketHandlerObj->checkUnEligibleCriteria($this->username);
			if($criteria){
				$this->errorCondition =$criteria;
				$this->error ='Y';
			}
		}	
		// Error condition ends

                // Profile allocation 
                if(!$this->error){
			$status='C';
			if($this->follow_date){
				if(!$follow_min)
					$follow_min     ='00';
				$follow_hour            =$crmUtilityObj->fetchHourDetails($follow_hour);
				$this->follow_time      =date("Y-m-d",JSstrToTime($this->follow_date))." ".$follow_hour.":".$follow_min.":"."00";
				$status='F';
			}
			$currDate =date("Y-m-d H:i:s");
			if(!$this->allot_time || $this->allot_time>$currDate)
				$this->allot_time =$currDate;

                        $paramsArr =array("ALLOT_TIME"=>"$this->allot_time","ALLOTED_TO"=>"$this->allot_to","STATUS"=>"$status","FOLLOWUP_TIME"=>"$this->follow_time","COMMENTS"=>"$this->comments","WILL_PAY"=>"MA","REASON"=>"$this->call_source","USERNAME"=>"$this->username","PRIVILEGE"=>"$this->privilege","CALL_SOURCE"=>"$this->call_source","ALLOTED_BY"=>"$this->agentName","MODE"=>"M");
                        $agentBucketHandlerObj->allocate($processObj,$paramsArr);
                        $this->allocatedSuccessfully =true;
                }
        }
	$this->followupDate 	=$crmUtilityObj->getFollowupDate('',$this->agentName,$followupDateSet);
	$this->callSource	=$crmUtilityObj->populateCallSource();
	$this->execArr 		=$agentAllocDetailsObj->fetchExecutives($processObj);
        $priv 			= explode("+",$this->privilege);

        if(in_array("SLHD",$priv) || in_array("P",$priv) || in_array("MG",$priv))
                $this->showAllotTime ='Y';
	
        $this->setTemplate('manualAllocation');
  }
  public function executeManualExtAllocation(sfWebRequest $request)
  {
        $this->cid              =$request->getParameter("cid");
        $submit                 =$request->getParameter("submit");

        $processObj             =new PROCESS();
        $crmUtilityObj          =new crmUtility();
        $agentBucketHandlerObj  =new AgentBucketHandler();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName        =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $privilege        	=$agentAllocDetailsObj->getprivilage($this->cid);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('MANUAL_EXT');
        $processObj->setExecutive($this->agentName);

        // form submission
        if($submit)
        {
                $this->username         =$request->getParameter("username");
                $this->call_source      =$request->getParameter("call_source");
                $this->comments         =$request->getParameter("comments");
                $this->follow_date      =$request->getParameter("follow_date");
                $follow_hour            =$request->getParameter("follow_hour");
                $follow_min             =$request->getParameter("follow_min");
	
                // Error condition check
                $this->error ='';
		$followupDateSet =$this->follow_date."-".$follow_hour."-".$follow_min;
                if(!$this->username || !$this->call_source || !$this->comments || !$this->follow_date)
                        $this->error ='Y';
                if($this->username){
                        $criteria =$agentBucketHandlerObj->checkUnEligibleCriteria($this->username,$this->agentName,'manualExt');
                        if($criteria){
                                $this->errorCondition =$criteria;
                                $this->error ='Y';
                        }
                }
                // Profile allocation 
                if(!$this->error){
                        // followup time parsing
                        if($this->follow_date){
                                if(!$follow_min)
                                        $follow_min     ='00';
                                $follow_hour            =$crmUtilityObj->fetchHourDetails($follow_hour);
                                $this->follow_time      =date("Y-m-d",JSstrToTime($this->follow_date))." ".$follow_hour.":".$follow_min.":"."00";
                        }

                        $paramsArr =array("ALLOT_TIME"=>date("Y-m-d H:i:s"),"ALLOTED_TO"=>"$this->agentName","STATUS"=>"F","FOLLOWUP_TIME"=>"$this->follow_time","COMMENTS"=>"$this->comments","WILL_PAY"=>"MA","REASON"=>"$this->call_source","PRIVILEGE"=>"$privilege","CALL_SOURCE"=>"$this->call_source","ALLOTED_BY"=>"$this->agentName","USERNAME"=>"$this->username","MODE"=>"M");
                        $agentBucketHandlerObj->allocate($processObj,$paramsArr);
                        $this->allocatedSuccessfully =true;
                }
        }
	$this->followupDate     =$crmUtilityObj->getFollowupDate('',$this->agentName,$followupDateSet);	
        $this->callSource =$crmUtilityObj->populateCallSource();
        $this->setTemplate('manualExtAllocation');
  }	
  public function executeManualExtDaysAllocation(sfWebRequest $request)
  {
        $this->cid              =$request->getParameter("cid");
        $submit                 =$request->getParameter("submit");

        $processObj             =new PROCESS();
        $agentBucketHandlerObj  =new AgentBucketHandler();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName        =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('MANUAL_EXT_DAYS');
        $processObj->setExecutive($this->agentName);

        // form submission
        if($submit)
        {
                $this->username         =$request->getParameter("username");
                $this->days      	=$request->getParameter("days");

                // Error condition check
                if(!$this->username)
                        $this->error ='Y';
		if(!is_numeric($this->days) || $this->days<=0){
			$this->error ='Y';
			$this->errorDays ='Y';
		}
                if($this->username){
                        $criteria =$agentBucketHandlerObj->checkUnEligibleCriteria($this->username,'','extDays');
                        if($criteria){
                                $this->errorCondition =$criteria;
                                $this->error ='Y';
                        }
                }
                // Profile allocation 
                if(!$this->error){
                        $paramsArr 		=array("RELAX_DAYS"=>"$this->days","USERNAME"=>"$this->username");
                        $agentBucketHandlerObj->allocate($processObj,$paramsArr);

			$this->allocationDetails	=$agentAllocDetailsObj->fetchLastAllocationDetails('',$this->username);
	   	        $deAllocationDate 		=$this->allocationDetails['DE_ALLOCATION_DT'];
			$this->showDeAllocationDate	=date("d/M/y",JSstrToTime($deAllocationDate));
			$this->moreDays			=ceil((JSstrToTime($deAllocationDate)-JSstrToTime(date("Y-m-d")))/86400);
                        $this->allocatedSuccessfully 	=true;	
                }
        }
        $this->setTemplate('manualExtDaysAllocation');
  }

  // Get Outbound Process List for Executives
  public function executeTransferProfiles(sfWebRequest $request)
  {
        $this->cid      =$request->getParameter("cid");
	$this->subMethod=$request->getParameter("subMethod");
	$this->singleProfile=$request->getParameter("singleProfile");
	$submit         =$request->getParameter("submit");
	$this->profilesTransferred =false;

        $processObj             =new PROCESS();
        $agentBucketHandlerObj  =new AgentBucketHandler();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('TRANSFER_PROFILES');
	$processObj->setSubMethod($this->subMethod);

	if($submit){
		$this->agentFrom      	=$request->getParameter("agentFrom");
		$this->agentTo        	=$request->getParameter("agentTo");
		$this->singleUser	=$request->getParameter("userName");
		if(strcmp($this->agentFrom,$this->agentTo)!=0)
		{
			$day  			=$request->getParameter("AllocationDay");
			$month			=$request->getParameter("AllocationMonth");
			$year 			=$request->getParameter("AllocationYear");
			if(isset($day) && isset($month) && isset($year)){
				$this->allocationDt 	=date("Y-m-d",JSstrToTime("$year-$month-$day"));
			}
				
			$processObj->setUsername($this->agentFrom);
			$processObj->setExecutives(array($this->agentTo));
			$processObj->setCurDate($this->allocationDt);
			$this->errorUsername='';
			if($this->singleProfile)
			{
				if($this->singleUser)
				{
	        	                $jProfilesDetails       =$agentAllocDetailsObj->fetchJProfileDetails($this->singleUser,'PROFILEID');
		                        $this->singleProfileid  =$jProfilesDetails['PROFILEID'];
                		        if(!$this->singleProfileid)
                        		        $this->errorUsername ='WRONG_USERNAME';
					else
						$processObj->setProfiles($this->singleProfileid);
                		}
                		else
		                        $this->errorUsername ='NO_USERNAME';
			}
			if($this->errorUsername=='')
			{
				$this->errorAlloted = $agentBucketHandlerObj->allocate($processObj);	// profile allocation
				if($this->errorAlloted=='')
				{
					$profiles =$processObj->getProfiles();
					$this->profilesCnt = $processObj->getTransferredProfilesCount();
		        	        $this->remainingCnt = $processObj->getRemainingTransferrableLimit();
					if(count($profiles)>0){
						$agentBucketHandlerObj->updateTransferLog($profiles, $this->agentName, $this->agentFrom, $this->agentTo, $this->subMethod, $this->allocationDt);	
					}
					// Need for this has been removed
					//$this->profilesCnt =count($profiles);
					$this->profilesTransferred =true;
				}
			}
		}
	}
	$this->agentList =$agentAllocDetailsObj->fetchExecutives($processObj); 
        $this->setTemplate('transferProfiles');
  }

  // Set Allocation limit for executives
  public function executeFieldSalesAllocationLimit(sfWebRequest $request)
  {
        $this->cid      =$request->getParameter("cid");
        $submit         =$request->getParameter("submit");

        $processObj             =new PROCESS();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName =$agentAllocDetailsObj->fetchAgentName($this->cid);
        $processObj->setProcessName('Allocation');
        $processObj->setMethod('FIELD_SALES');
        $processObj->setSubMethod('FIELD_SALES_LIMIT');

        if($submit){
                $this->center =$request->getParameter("locality");
                $this->limit  =$request->getParameter("limit");
		if(!$this->center || !$this->limit)
			$this->error =true;
		if($this->limit && !preg_match('/^[0-9 ]+$/i',$this->limit))	
			$this->errorLimit =true;
		
		if(!$this->error && !$this->errorLimit){
			$agentAllocDetailsObj->updateLocalityLimit($this->limit,$this->center,$processObj->getMethod());		
                	$this->success =true;
		}
        }
	$this->fieldSalesLocalityLimitArr =$agentAllocDetailsObj->getLocalityLimit($processObj->getMethod());
        $this->setTemplate('fieldSalesAllocationLimit');
  }

  // Set Allocation limit for executives
  public function executePreAllocationLimit(sfWebRequest $request)
  {
        $this->cid  		    =$request->getParameter("cid");
        $submit         		=$request->getParameter("submit");
        $processObj             =new PROCESS();
        $agentAllocDetailsObj   =new AgentAllocationDetails();

        $this->agentName 		=$agentAllocDetailsObj->fetchAgentName($this->cid);
        
        if($submit){
            $this->center =$request->getParameter("locality");
            $this->limit  =$request->getParameter("limit");
			if(!$this->center || !$this->limit)
				$this->error =true;
			if($this->limit && !preg_match('/^[0-9 ]+$/i',$this->limit))	
				$this->errorLimit =true;
			if($this->limit && $this->limit>120)	
				$this->errorRange =true;
			
			if(!$this->error && !$this->errorLimit && !$this->errorRange){
				$agentAllocDetailsObj->updateLocalityLimit($this->limit,$this->center,'PREALLOCATION');		
            	$this->success =true;
			}
        }
		$this->localityLimitArr =$agentAllocDetailsObj->getLocalityLimit('PREALLOCATION');
  }

  // Set Sales Target for Field Sales Team
  public function executeSetSalesTarget(sfWebRequest $request)
  {
        $this->cid      =$request->getParameter("cid");
        $submit         =$request->getParameter("submit");
        $show           =$request->getParameter("show");
        $calculate      =$request->getParameter("calculate");
	
	$this->monthName     =$request->getParameter("monthValue");
        $this->yearName      =$request->getParameter("yearValue");
        $this->fortnight = $request->getParameter("fortnightValue");

	$this->SUBMIT_STATUS = 0;
	$this->editable = 0;

	if(!$this->monthName || !$this->yearName){
		$this->monthName = date('M');
		$this->yearName = date('Y');
        $day = date('d');
        if($day >15){
            $this->fortnight = '2';
        }
        else{
            $this->fortnight = '1';
        }
	}
	$agentAllocationDetailsObj =new AgentAllocationDetails();
	$incentiveSalesTargetObj   =new incentive_SALES_TARGET();

	$this->monthArr = array_keys(crmParams::$monthOrder);
        $this->yearArr = range(date('Y')+1,2004);
	$monthYear      = $this->monthName."-".$this->yearName;
        $givenMonthSeq = $this->yearName * 100 + crmParams::$monthOrder[$this->monthName];
        $currentMonthSeq = date('Y') * 100 + crmParams::$monthOrder[date('M')];

        if(date('d')<=7){
                if($givenMonthSeq >= $currentMonthSeq-1)
                        $this->editable = 1;
        }
        else{
                if($givenMonthSeq >= $currentMonthSeq)
                        $this->editable = 1;
        }

	if($this->editable == 1){
		$names = $agentAllocationDetailsObj->getValidUsersForSalesTarget();
		$this->usernames = $names['USERNAMES'];
		$boss = $names['BOSS'];

		if(count($boss)!=1){                     
			$this->overall_sales_head_check = 1;
			$this->setTemplate('setSalesTarget');
			return;
		}

		$hierarchyRoot = $boss[0];
		$hierarchyObj = new hierarchy($hierarchyRoot);
	
		$individual_target = array();

		if($submit || $calculate)
		        $individual_target = $request->getParameter("INDIVIDUAL_TARGET");
		else{
			$individual_target = $incentiveSalesTargetObj->getDetailsForEditMode($monthYear, $this->usernames, $this->fortnight);
			foreach($this->usernames as $value){
				if(!in_array($value, array_keys($individual_target)))
					$individual_target[$value] = 0;
			}
		}
		$this->targetInfo = array();	
		$this->targetInfo = $hierarchyObj->getHierarchyInfoStructure($individual_target);
			
		if($submit)
		{
			$this->SUBMIT_STATUS = 1;
			$incentiveSalesTargetObj->removeData($monthYear, $this->fortnight);
			$incentiveSalesTargetObj->updateDetails($monthYear, $this->targetInfo, $this->fortnight);
        	}
	}
	else
	{
		if($submit)
			$this->editable_error = 1;
		else
			$this->targetInfo = array();	
			$this->targetInfo = $incentiveSalesTargetObj->getDetailsForViewMode($monthYear, $this->fortnight);
	}

	$this->setTemplate('setSalesTarget');
  }

  /*function to get list of exclusive members
  *@param : type of request(PENDING/ASSIGNED) via GET
  */
  public function executeGetExclusiveMembers(sfWebRequest $request)
  {
  	$type = $request->getParameter("EX_STATUS");
  	$this->cid = $request->getParameter("cid");
  	$this->user = $request->getParameter("user");
  	$memHandlerObj = new MembershipHandler();
  	if($type=='PENDING')
  		$assigned=false;
  	else if($type=='ASSIGNED')
  		$assigned=true;
  	else
  	{
  		echo "Please specify action type(ASSIGNED/PENDING) as input:exAction";
  		die;
  	}
  	$pswrdsObj = new jsadmin_PSWRDS('newjs_slave'); 
  	$whereCondition = array("ACTIVE"=>'Y');
  	$greaterCondition = array("LAST_LOGIN_DT"=>date('Y-m-d',strtotime("- 15 day")));
  	$this->ExPmSrExecutivesList = $pswrdsObj->getArray('%ExPmSr%','PRIVILAGE',"USERNAME,PHONE,EMAIL",$whereCondition,$greaterCondition);
  	$this->executivesData = json_encode($this->ExPmSrExecutivesList);
  	$this->result = $memHandlerObj->getExclusiveAllocationDetails($assigned,"BILLING_DT");
  	//active tab
  	$this->tabChosenDetails = exclusiveMemberList::$TYPE_TABID_MAPPING[$type];
  	//horizontal tab details
  	$this->tabDetails = exclusiveMemberList::$TYPE_TABID_MAPPING;
  	//columns list for interface
  	$this->columnNamesArr = exclusiveMemberList::$displayColumnsNames;
  }

  /*function: api to handle assign/unassign action for exclusive members
  *@param : $request
  * @return : api response
  */
  public function executeHandleExMemAllocationApi(sfWebRequest $request)
  {
  	$inputArr = $request->getParameter("profileDetails");
  	$sendAssignMailer = $request->getParameter("sendAssignMailer");
  	$sendAssignSMS = $request->getParameter("sendAssignSMS");
  	$profileid = intval($inputArr["profile"]);
  	$success = true;

  	if($inputArr["exAction"] && $profileid)
  	{
	  	$exObj = new billing_EXCLUSIVE_MEMBERS();
	  	if($inputArr["exAction"]=="UNASSIGN")
	  	{
	  		$exObj->updateExclusiveMemberAssignment($profileid,NULL,"0000-00-00");
	  	}
	  	else if($inputArr["exAction"]=="ASSIGN")
	  	{
	  		$exObj->updateExclusiveMemberAssignment($profileid,$inputArr["executiveDetails"]["USERNAME"],date('Y-m-d'));
	  		//send mail to profile in case of assignment if flag true
	  		if($sendAssignMailer==true)
	  		{
	            $executiveDetails = $inputArr["executiveDetails"];

	            $profileDetails = array("PROFILEID"=>$profileid,"USERNAME"=>$inputArr["username"],"EXECUTIVE_NAME"=>$inputArr["executiveDetails"]["USERNAME"],"EXECUTIVE_PHONE"=>$executiveDetails["PHONE"],"EXECUTIVE_EMAIL"=>$executiveDetails["EMAIL"]);
	            $mailerObj = new MembershipMailer();
		  		$mailerObj->sendServiceActivationMail(1808,$profileDetails);
		  	}
		  	//send sms to profile in case of assignment if flag true
		  	if($sendAssignSMS==true)
		  	{
	        	$smsParams = array("EXECUTIVE_NAME"=>$executiveDetails["USERNAME"],"EXECUTIVE_PHONE"=>$executiveDetails["PHONE"],"EXECUTIVE_EMAIL"=>$executiveDetails["EMAIL"]);
	        	$SMS_MESSAGE = exclusiveMemberList::getSMSContentForAssign($smsParams);
	        	CommonUtility::sendInstantSms($SMS_MESSAGE,$inputArr["phone"],$profileid,"transaction");
		  	}
	  	}
	  	else
	  		$success=false;

	  	unset($exObj);
  	}
  	else
  		$success=false;
  	$respObj = ApiResponseHandler::getInstance();
    if ($success==true) {
        $respObj->setHttpArray(ResponseHandlerConfig::$SUCCESS);
    } else {
        $respObj->setHttpArray(ResponseHandlerConfig::$FAILURE);
    } 
    $respObj->generateResponse();
    die();
  }

}