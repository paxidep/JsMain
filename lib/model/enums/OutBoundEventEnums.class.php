<?php

/**
 * Description of OutBoundEventEnums
 * Class which contain all the defined constants and enums
 * related to OutBound
 * @package     cache
 * @author      Kunal Verma
 * @created     13th June 2017
 */
class OutBoundEventEnums
{
  //FU  Stands for FreeUser
  //
  const VIEW_CONTACT = 1; // When PG View Contact Details of PoG
  const INTEREST_ACCEPTED = 2; // When PG Accepts PoG Interest, then This event will be generated for POG, bcz his/her interest has been accepted
  const ACCEPT_INTEREST = 3; // When PG Accepts PoG Interest, then This event will be generated for PG, bcz he/she accept pog interest
  
  const OUTBOUND_CALL_TIME_START = "09";
  const OUTBOUND_CALL_TIME_END = "21";
  
  const THIRD_PARTY_API_ID = "jeevansathi";
  const THIRD_PARTY_API_AUTH_TOKEN = "d82ea09300058f6b2cd3b9effe4c267bf7df8c86";
}
