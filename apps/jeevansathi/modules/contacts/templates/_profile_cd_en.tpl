<div class="maindiv">
~include_partial("contacts/profile_locked_phoneEmail")`
<div class="sp15"></div>
~if $contactEngineObj->getComponent()->contactDetailsObj->getEvalueLimitUser() eq CONTACT_ELEMENTS::EVALUE_STOP || $contactEngineObj->getComponent()->contactDetailsObj->getEvalueLimitUser() eq CONTACT_ELEMENTS::EVALUE_NO`
~if $contactEngineObj->getComponent()->contactDetailsObj->getEvalueLimitUser() eq CONTACT_ELEMENTS::EVALUE_STOP`
<div class="fs14">To continue to view phone/email of ~$contactEngineObj->contactHandler->getViewed()->getUSERNAME()` (and other) members, Upgrade your membership<div class="sp12"></div> 
~else`
<div class="fs14">Upgrade your membership to view phone/email of  ~$contactEngineObj->contactHandler->getViewed()->getUSERNAME()` (and other) members <div class="sp12"></div> 
~/if`
<div>
<div class="sp12"></div> 
<strong>Why Upgrade?</strong>
<div class="sp12"></div> 
<ul>
<li>Instantly see phone/email</li>
<li>Initiate messages and chat</li>
<li>Get more interests and faster responses</li>
</ul>
<div class="sp12"></div> 
<div class="sp12"></div> 
<a href="/profile/mem_comparison.php"> View Membership Plans </a>
</div>
</div>
~else`
<div class="fs16">To see Phone/Email of this member, <div class="sp12"></div> 
  ~Messages::getBuyPaidMembershipButton(["NAVIGATOR"=>$NAVIGATOR])`
</div>
~/if`
<br />
</div>