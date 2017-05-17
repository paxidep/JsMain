<?php
/*
 * Created on: 9/12/2015
 * API created to insert entries in newjs.ASTRO_PULLING_REQUEST and MIS.ASTRO_CLICK_COUNT on the call of third party astro form
 * 
 */
class HoroscopeV1Action extends sfAction{
    
    private $type = 'E';
    public function execute($request){
        $this->loginData = $request->getAttribute("loginData");
        $this->loginProfile = LoggedInProfile::getInstance();
        $this->loginProfile->getDetail('','',"MTONGUE");
        $profileid = $this->loginData["PROFILEID"];
        $apiResponseHandlerObj=ApiResponseHandler::getInstance();
        if ( $_SERVER['HTTP_X_REQUESTED_BY'] === NULL && ( MobileCommon::isNewMobileSite() || MobileCommon:: isDesktop()))
        {
            $http_msg=print_r($_SERVER,true);
            mail("ahmsjahan@gmail.com,lavesh.rawat@gmail.com","CSRF header is missing.","details :$http_msg");
            $msg["Error"] = "Something went wrong";
            $apiResponseHandlerObj->setResponseBody($msg);
            $apiResponseHandlerObj->setHttpArray(ResponseHandlerConfig::$FAILURE);
        }
        else
        {
            if($profileid){
                if($request->getParameter("update")){
                    $msgAstroPull = $this->updateAstroPullingRequest($profileid);
                    $msgAstroClick = $this->updateAstroClickCount($profileid, $this->type, $this->loginProfile->getMTONGUE());
                    if($msgAstroPull === true && $msgAstroClick === true){
                        $msg["Success"] = "Successfull";
                        $apiResponseHandlerObj->setResponseBody($msg);
                        $apiResponseHandlerObj->setHttpArray(ResponseHandlerConfig::$SUCCESS);
                    }
                    else{
                        $msg["Error"] = "Something gone wrong";
                        $apiResponseHandlerObj->setResponseBody($msg);
                        $apiResponseHandlerObj->setHttpArray(ResponseHandlerConfig::$FAILURE);
                    }
                }
            }
        }
        $apiResponseHandlerObj->generateResponse();
        die;
    }
    
    public function updateAstroPullingRequest($profileid){
        $astroObj = ProfileAstro::getInstance();
        $msg = $astroObj->insertInAstroPullingDetails($profileid);
        return $msg;
    }
    
    public function updateAstroClickCount($profileid,$type,$user_mtongue){
        $astroObj = new MIS_ASTRO_CLICK_COUNT();
        $msg = $astroObj->insertInAstroClickCount($profileid, $type, $user_mtongue);
        return $msg;
    }
    
}
