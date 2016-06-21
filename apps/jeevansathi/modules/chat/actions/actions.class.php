<?php

/**
 * chat actions.
 *
 * @package    jeevansathi
 * @subpackage chat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chatActions extends sfActions
{
	/**
	* Executes authenticateChatSession action  - returns jid,sid and rid for chat session
	*
	* @param sfRequest $request A request object
	*/
 	public function executeAuthenticateChatSessionV1(sfWebRequest $request)
 	{
		$xmppPrebind = new XmppPrebind('localhost', 'http://localhost:7070/http-bind/', 'converse', false, false);
		$username = substr("a1@localhost", 0,2);
		$xmppPrebind->connect($username, '123');
		$xmppPrebind->auth();
		$response = $xmppPrebind->getSessionInfo(); // array containing sid, rid and jid

		$apiResponseHandlerObj = ApiResponseHandler::getInstance();
		$apiResponseHandlerObj->setHttpArray(ResponseHandlerConfig::$SUCCESS);
		$apiResponseHandlerObj->setResponseBody($response);
		$apiResponseHandlerObj->generateResponse();
		die;
 	}
    
    public function executeChatUserAuthenticationV1(sfWebRequest $request)
    {
        $apiResponseHandlerObj = ApiResponseHandler::getInstance();
        $loginData = $request->getAttribute("loginData");
        if($loginData){
            $username = $loginData['USERNAME'];
            $url = "http://localhost:9090/plugins/restapi/v1/users/".$username;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

            $headers = array();
            $headers[] = 'Authorization: '.ChatEnum::$openFireAuthorizationKey;
            $headers[] = 'Accept: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $curlResult = curl_exec ($ch);
            curl_close ($ch);
            $result = json_decode($curlResult, true);
            if($result['username']){
                //User exists
                $response['userStatus'] = "User exists";
                $apiResponseHandlerObj->setHttpArray(ChatEnum::$userExists);
            }
            else{
                //create user
                $response['userStatus'] = "New user created";
                $url = "http://localhost:9090/plugins/restapi/v1/users/";
                $data = array("username" => "$username", "password" => "123");
                $jsonData = json_encode($data);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
                curl_setopt($ch, CURLOPT_TIMEOUT, 4);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

                $headers = array();
                $headers[] = 'Authorization: '.ChatEnum::$openFireAuthorizationKey;
                $headers[] = 'Accept: application/json';
                $headers[] = 'Content-Type: application/json';

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $curlResult = curl_exec ($ch);
                
                if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '201'){
                    $response['userStatus'] = "New user created";
                    $apiResponseHandlerObj->setHttpArray(ChatEnum::$newUserCreated);
                }
                elseif(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '409'){
                    $response['userStatus'] = "User Exists";
                    $apiResponseHandlerObj->setHttpArray(ChatEnum::$userCreationError);
                }
                else{
                    $result = json_decode($curlResult, true);
                    $reponse['exception'] = $result['exception'];
                    $apiResponseHandlerObj->setHttpArray(ChatEnum::$error);
                }
                curl_close ($ch);
            }
        }
        else{
            $response = "Logged Out Profile";
            $apiResponseHandlerObj->setHttpArray(ChatEnum::$loggedOutProfile);
        }
        $apiResponseHandlerObj->setResponseBody($response);
        $apiResponseHandlerObj->generateResponse();
        die;
    }
}
?>