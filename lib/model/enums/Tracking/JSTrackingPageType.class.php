<?php
//values for different source from acceptace can take place
class JSTrackingPageType
{
	const OTHER = 0; // other page source 
	const CONTACT_AWAITING = 1;   // contact center - members awaiting 
	const PROFILE_PAGE = 2;      // profile page
	const CONTACT_OTHER = 3;     // contact center - other page
	const SEARCH = 4;            // search page
	const MYJS_AWAITING = 5;     // my jeevansathi page
	const EOI_MAILER = 6;         // eoi mailer 
	const YN_MAILER = 7;          // yes no mailer
	const EOI_FILTER_MAILER = 30;	//eoi filter mailer
	const MOBILE_AWAITING = 8;    // mobile page - member awaiting
	const SMS = 9;               // eoi sms
	const MOBILE_FILTER = 10;
	const MYJS_ANDROID_APP = 19;
	const GCM_PROFILE_PAGE = 20;
	const PROFILE_PAGE_APP=21;
	const INBOX_EOI_APP=11;
	const SHORTLIST_APP=12;
	const SHORTLIST_JSMS=13;
	const MYJS_EOI_JSMS=15;
	const PROFILE_PAGE_JSMS=16;
	const PHONEBOOK_JSMS  = 17;
	const FILTERED_INTEREST_JSMS  = 18;			//filtered interests page new jsms
	const CONTACT_VIEWERS_JSMS=14;         //for new mobile site people who viewed my contacts
    const FILTERED_INTEREST_ANDROID = 22;
    const CONTACTS_VIEWED_ANDROID = 23;  //PHONEBOOK
    const CONTACT_VIEWERS_ANDROID = 24; //PEOPLE WHO VIEWED MY CONTACTS

	const MOBILE_AWAITING_IOS = 41;
	const SHORTLIST_IOS=42;
	const PHONEBOOK_IOS  = 43;
	const FILTERED_INTEREST_IOS  = 44;
	const CONTACT_VIEWERS_IOS=45;
	const MYJS_EOI_IOS=46;
	const PROFILE_PAGE_IOS=47;
	const MYJS_EOI_PC = 30;
	const MYJS_FILTER_PC = 31;
	const MYJS_SHORTLIST_PC = 32;
	//const CC_PHOTO_REQUEST_SENT_PC=8;
	//const CC_PHOTO_REQUEST_RECEIVED_PC=8;
	//const CC_HOROSCOPE_REQUEST_RECEIVED_PC=8;
	//const CC_HOROSCOPE_REQUEST_SENT_PC=8;
	//const CC_INTEREST_FILTERED_PC=10;
	const EOI_NOTIFICATION_JSMS = 48;
	const EOI_REMINDER_NOTIFICATION_JSMS = 49;
}

?>