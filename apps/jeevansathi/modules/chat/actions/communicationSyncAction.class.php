<?php
/**
 * contacts actions.
 *
 * @package    jeevansathi
 * @subpackage contacts
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class communicationSyncAction extends sfAction
{
        /**
         * Executes index action
         *
         * @param sfRequest $request A   request object
         */

         function execute($request)
        {
              $apiObj = ApiResponseHandler::getInstance();
                $this->loginData = $request->getAttribute("loginData");
                $this->loginProfile = LoggedInProfile::getInstance();
                $pid=$this->loginProfile->getPROFILEID();
                $redisKey=$pid."_lastCommunicationId";
                $arr=JsMemcache::getInstance()->getHashAllValue($redisKey);
                $responseArray=array();
                $i=0;
                if(is_array($arr)){
                        foreach ($arr as $key => $value) {
                                $responseArray["syncRecords"][$i]["profileId"]= (string)$key;
                                $responseArray["syncRecords"][$i]["profileCheckSum"]=md5($key) . "i" . $key;
                                $responseArray["syncRecords"][$i]["lastMessageTimeStamp"]=strtotime($value) * 1000;
                                $i++;
                        }
                }
                else
                        $responseArray=array();
                
                $apiObj->setHttpArray(ResponseHandlerConfig::$SUCCESS);
                $apiObj->setResponseBody($responseArray);
                $apiObj->generateResponse();

                die;

        }




}
