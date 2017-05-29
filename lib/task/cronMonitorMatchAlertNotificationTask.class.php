<?
/*
This php script is run to create object of rabbitmq Consumer class and call 
the receiveMessage function to let the consumer receive messages  on first server.
*/

class cronMonitorMatchAlertNotificationTask extends sfBaseTask
{
  /**
   * 
   * Configuration details for cron:cronMonitorMatchAlertNotificationTask
   * 
   * @access protected
   * @param none
   */
  protected function configure()
  {
    $this->namespace           = 'notification';
    $this->name                = 'cronMonitorMatchAlertNotification';
    $this->briefDescription    = 'Initialises instance of rabbitmq consumer class to retrieve messages on first server';
    $this->detailedDescription = <<<EOF
     The [cronConsumeQueueMessage|INFO] calls receiveMessage function of consumer class through its instance to retrieve messages on first server:
     [php symfony notification:cronMonitorMatchAlertNotification] 
EOF;
    $this->addOptions(array(
        new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'jeevansathi')
    ));
  }

  /**
   * 
   * Function for executing cron- creates consumer class object and calls receiveMessage func to consume messages on FIRST_SERVER.
   * 
   * @access protected
   * @param $arguments,$options
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!sfContext::hasInstance())
      sfContext::createInstance($this->configuration);
    include(JsConstants::$docRoot."/commonFiles/sms_inc.php");
    $curTime = date('Y-m-d H:i:s', strtotime('+9 hour 30 minutes'));
    $stTime = date('Y-m-d H:i:s', strtotime('+9 hour 25 minutes'));
    $hr = date('H', strtotime('+9 hour 30 minutes'));
    $notificationLogObj = new MOBILE_API_NOTIFICATION_LOG();
    $count = $notificationLogObj->getDataForDuration("MATCHALERT",$stTime,$curTime);
    print_r(array("curTime"=>$curTime,"stTime"=>$stTime,"curHr"=>$hr,"count"=>$count));
    if($count==0 && !($hr == "09" || $hr == 10 || $hr == 11)){
        $monitoringKey = "MA_N_".date('Y-m-d');
        $mailerStartTime = JsMemcache::getInstance()->get($monitoringKey);
        if(!$mailerStartTime){
            $to = "nitish.sharma@jeevansathi.com,vibhor.garg@jeevansathi.com,manoj.rana@naukri.com,ankita.g@jeevansathi.com";
            if(JsConstants::$whichMachine == "test"){
                $to = "nitish.sharma@jeevansathi.com";
            }
            $subject = "[Match Alert] Match Alert Not started";
            $msgBody = "[Match Alert] Match Alert Not started";
            SendMail::send_email($to, $msgBody, $subject);
            $msg = " Match Alert Not started";
            $this->sendAlertSMS($msg);
        }
        else{
            $offsetTime = date('Y-m-d H:i:s', strtotime("+1 hour",  strtotime($mailerStartTime)));
            print_r(array("mailerStartTime"=>$mailerStartTime,"offsetTime"=>$offsetTime));
            //die;
            if(strtotime(date('Y-m-d H:i:s')) > strtotime($offsetTime)){                
                $matchalertSentObject = new matchalerts_MATCHALERTS_TO_BE_SENT();
                $count = $matchalertSentObject->getTotalCountWithScript(1, 0);
                if ($count != 0 && $count != "") {
                    $matchalertMailertObject = new matchalerts_MAILER();
                    $MailersCount = $matchalertMailertObject->getMailerProfiles("COUNT(*) as CNT");
                    if($MailersCount[0]["CNT"] != 0){
                        $rmqObj = new RabbitmqHelper();
                        $rmqObj->killConsumerForCommand(MessageQueues::CRONNOTIFICATION_CONSUMER_STARTCOMMAND);
                        $to = "nitish.sharma@jeevansathi.com,vibhor.garg@jeevansathi.com,manoj.rana@naukri.com,ankita.g@jeevansathi.com";
                        if(JsConstants::$whichMachine == "test"){
                            $to = "nitish.sharma@jeevansathi.com";
                        }
                        $subject = "[Match Alert] Instant Notification Queue Consumer killed";
                        $msgBody = "[Match Alert] Instant Notification Queue Consumer killed";
                        SendMail::send_email($to, $msgBody, $subject);
                        $this->sendAlertSMS();
                    }
                }
                
            }
            else{
                $msg = "MatchAlert started @$mailerStartTime";
                $to = "nitish.sharma@jeevansathi.com";
                $subject = "[Match Alert] MatchAlert started @$mailerStartTime";
                $msgBody = "[Match Alert] MatchAlert started @$mailerStartTime";
                SendMail::send_email($to, $msgBody, $subject);
                $this->sms("8989931104",$msg);
            }
        }
    }
  }
  
  public function sendAlertSMS($msg=''){
    $mobileNumberArr = array("vibhor"=>"9868673709","manoj"=>"9999216910","nitish"=>"8989931104","ankita"=>"9650879575");
    if(JsConstants::$whichMachine == "test"){
        $mobileNumberArr = array("nitish"=>"8989931104");
    }
    foreach($mobileNumberArr as $k=>$v){
        $this->sms($v,$msg);
    }
  }
  
  public function sms($mobile,$msg){
        $t = time();
        if($msg){
            $message    = "Mysql Error Count have reached ".$msg." $t";
        }
        else{
            $message    = "Mysql Error Count have reached InstantNotificationConsumer killed $t";
        }
        $from           = "JSSRVR";
        $profileid      = "144111";
        $smsState = send_sms($message,$from,$mobile,$profileid,'','Y');
        $date = date("Y-m-d h");
    }
}
?>
