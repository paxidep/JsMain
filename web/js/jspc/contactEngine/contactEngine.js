var actionUrl = {"CONTACT_DETAIL":"/api/v2/contacts/contactDetails","INITIATE":"/api/v2/contacts/postEOI","CANCEL_INTEREST":"/api/v2/contacts/postCancelInterest","CANCEL":"/api/v2/contacts/cancel","SHORTLIST":"/api/v1/common/AddBookmark","DECLINE":"/api/v2/contacts/postNotInterested","REMINDER":"/api/v2/contacts/postSendReminder","WRITE_MESSAGE":"/api/v2/contacts/postWriteMessage","WRITE_MESSAGE_LIST":"/api/v2/contacts/WriteMessage","SEND_MESSAGE":"/api/v2/contacts/MessageHandle","ACCEPT":"/api/v2/contacts/postAccept","MESSAGE":"/api/v2/contacts/preWriteMessage","IGNORE":"/api/v1/common/ignoreprofile","PHONEVERIFICATION":"/phone/jsmsDisplay","MEMBERSHIP":"/profile/mem_comparison.php","COMPLETEPROFILE":"/profile/viewprofile.php","PHOTO_UPLOAD":'/social/MobilePhotoUpload',"ACCEPT_MYJS":"/api/v2/contacts/postAccept","DECLINE_MYJS":"/api/v2/contacts/postNotInterested","MESSAGE_WRITE":"/api/v2/contacts/postWriteMessage","REMOVE":"/inbox/removeFromICList","EDITPROFILE":"/profile/viewprofile.php?ownview=1"};

var PAGETYPE='';   
var ignoreLayerOpened=3; //for IgnoredLayer on VDP
var currentActionLayer="";
var WARNING= new Object();
var buttonsCssMap={'INITIATE':{'enable':'mailicon','disable':'msgsendicon'},
              'SHORTLIST':{'enable':'staricon','disable':'staractive2'},
              'CONTACT_DETAIL':{'enable':'callicon','disable':'callicon'},
              'CHAT':{'enable':'chaticon','disable':'chaticon'}}

var DetailbuttonsActionMap={'INITIATE':{'icon':'prfic1'},
              'SHORTLIST':{'enable':'prfic4','disable':'prfic49'},
              'CONTACT_DETAIL':{'icon':'prfic2'},
              'REMINDER':{'icon':'prfic1'},
              'CANCEL_INTEREST':{'icon':'prfic44'},
              'CANCEL':{'icon':'prfic44'},
              'CHAT':{'icon':'prfic3'},
              'MESSAGE':{'icon':'prfic1'},
              'DECLINE':{'icon':'prfic44'},
              'ACCEPT':{'icon':'prfic47'},
              'IGNORE':{'icon':'prfic47'}
              };



jQuery.fn.outerHtml = function(newHtml) {
  return jQuery('<div />').append(this.clone()).html();
};


var ContactEngineCard = function(Name) {
  PAGETYPE=Name;
  this.name = Name;
};


var SMSContactsDivBinding = function(obj) {
$(obj).unbind('click');
var proChecksum=$(obj).attr('profilechecksum');
var ajaxConfig={};
ajaxData={'profileChecksum':proChecksum};
ajaxConfig.url="/phone/SMSContactsToMobile";
ajaxConfig.data=ajaxData;
ajaxConfig.type='POST';
ajaxConfig.success=function(response){

if(response.responseStatusCode=='0') 
$(obj).html('Details sent to '+response.mobile);
else 
$(obj).html(response.SMSError);
$(obj).removeClass('cursp').css('color','#666');

 };

jQuery.myObj.ajax(ajaxConfig);
$(obj).html($("#SMSLoader").html());
}

ContactEngineCard.prototype.singleButtonDisplay= function(button,info,actionName){
var buttonArray=new Array(button);
if(info.profileChecksum)
	this.profileChecksum=info.profileChecksum;
else if(info.profilechecksum)
	this.profileChecksum=info.profilechecksum;
	
var innerHtml="";
switch(this.name)
      {
        case 'search':
        case 'VSP':
            var mainDivElement=$("#PreFourButtonsSearch").clone();
            var basicButtonElement = mainDivElement.find('.contactEngineIcon');
            innerHtml=this.setButtonProperties(buttonArray,basicButtonElement);
            break;
        case 'VDP':
        case 'VSP_VDP':
			var toBeUpdatedContainerId='#'+actionName+"-"+this.profileChecksum+"-"+this.name;
            var toBeUpdatedLabelId='#'+actionName+"-"+this.profileChecksum+"-"+this.name+"_LABEL";
            var toBeUpdatedIconId='#'+actionName+"-"+this.profileChecksum+"-"+this.name+"_ICON";
           
           if(buttonArray[0].id=='SHORTLIST')
           if(buttonArray[0].params)
           {

           	if(buttonArray[0].params.indexOf('&shortlist=true')>-1)
				$(toBeUpdatedIconId).removeClass("prfic4").addClass("prfic49");
			if(buttonArray[0].params.indexOf('&shortlist=false')>-1)
				$(toBeUpdatedIconId).removeClass("prfic49").addClass("prfic4");
		}
            $(toBeUpdatedContainerId).attr("data",buttonArray[0].params);
            $(toBeUpdatedLabelId).html(buttonArray[0].label);
            if(buttonArray[0].enable!=undefined && buttonArray[0].enable!=null)
			{
				if(!buttonArray[0].enable)
					$(toBeUpdatedContainerId).removeClass('contactEngineIcon').addClass('prfbg8CC');
            }
            break;
       case 'CC':
			var toBeUpdatedContainerId='#'+actionName+"-"+this.profileChecksum+"-"+this.name;
            var toBeUpdatedMessageId='#'+actionName+"_INFO";
            
            $(toBeUpdatedContainerId).attr("data",buttonArray[0].params);
            $(toBeUpdatedContainerId).html(buttonArray[0].label).removeClass("mt16").addClass("mt8");
            $(toBeUpdatedMessageId).html("");
            if(buttonArray[0].enable!=undefined && buttonArray[0].enable!=null)
			{
				if(!buttonArray[0].enable){
					$(toBeUpdatedContainerId).removeClass('contactEngineIcon cursp').addClass('prfbg8CC');
					$(toBeUpdatedContainerId).unbind();
					cECommonBinding();
				}
            }
            break;
	}
cECommonBinding();
	cECloseBinding(); 
return innerHtml;
}

ContactEngineCard.prototype.setButtonProperties = function(buttonArray,basicDivObj,info){

	var innerHtml="";
	var profileChecksum=this.profileChecksum;
	var divObj='';
            for(i=0;i<buttonArray.length;i++)
           {  
              divObj=basicDivObj.clone();
              divObj.attr('id',buttonArray[i].id+'-'+profileChecksum+"-"+this.name);
              if (buttonArray[i].params) divObj.attr('data',buttonArray[i].params);
              if (buttonArray[i].id=='SHORTLIST'){
                var enabled='enable';
            if (buttonArray[i].params) enabled=(buttonArray[i].params.indexOf('true')>-1)?'disable':'enable';  
           
            }
            else { if(buttonArray[i].enable==false){
               var enabled='disable'; divObj.removeClass('contactEngineIcon').removeClass('cursp').find('.buttonImage').removeClass('cursp');   
 
              } else var enabled='enable';

            }
          var  action=buttonArray[i].id ?  buttonArray[i].id : this.buttonObj.name;
             divObj.find('.buttonImage').addClass(buttonsCssMap[action][enabled]);
             divObj.attr('title',buttonArray[i].label);
             
            if(typeof(loggedInJspcUser)!="undefined")
			{
				if(loggedInJspcUser=="")
					divObj.removeClass('contactEngineIcon').addClass("loginLayerJspc");
			}
			if(buttonArray[i].id=="CHAT" )
			{ 
				if(info.userloginstatus!="undefined" && info.userloginstatus=="Online now")
				{
					divObj.addClass("OnlineChat");
				}
				else
				{
					divObj.addClass('classForDisableIconsearch').attr('title',"Offline").find('i').removeClass('cursp');

				}
			}
             innerHtml+=divObj.outerHtml();
            }
            
            return innerHtml;  


}

ContactEngineCard.prototype.buttonDisplay = function(buttonsObj,info,postResponse){
   var FinalHtml="";

var buttonArray=buttonsObj.buttons;

if(info.profileChecksum)
	this.profileChecksum=info.profileChecksum;
else if(info.profilechecksum)
	this.profileChecksum=info.profilechecksum;
     switch(this.name)
      {
        case 'search':
        case 'VSP':
        	if(!buttonArray) return;
			var  mainDivElement=$("#PreFourButtonsSearch").clone();
            var basicButtonElement = mainDivElement.find('.contactEngineIcon');
            var innerHtml=this.setButtonProperties(buttonArray,basicButtonElement,info)
            FinalHtml=mainDivElement.html(innerHtml).outerHtml();
            break;

		case 'VDP':
		case 'VSP_VDP':
			var InnerHtml="";
	if(buttonArray){
			if(buttonArray.length==4)
			{
				FinalHtmlElement = $("#cEPreFourButtonsDetail").clone();
				liElementHtml=FinalHtmlElement.find('li').eq(0).outerHtml();
 
				var temp=buttonArray;
				for (var i =  0; i < temp.length; i++) {
					htmlResp=liElementHtml;
					if(temp[i].id=="REMINDER" && !temp[i].enable)           
						htmlResp=htmlResp.replace(/\{\{className\}\}/g,"clearfix prfbg8CC cursd");	
					else
						htmlResp=htmlResp.replace(/\{\{className\}\}/g,"clearfix");
					
					htmlResp=htmlResp.replace(/\{\{iconName\}\}/g,temp[i].label);
					if(temp[i].params!=undefined)
						htmlResp=htmlResp.replace(/\{\{params\}\}/g,temp[i].params);
					else
						htmlResp=htmlResp.replace(/\{\{params\}\}/g,'');
						
				   if (temp[i].id=='SHORTLIST'){
						var enabled='enable';
						if (temp[i].params)
						enabled=(temp[i].params.indexOf('true')>-1)?'disable':'enable';
						tempObj=$(htmlResp);
						tempObj.find('.js-CeIcon').removeClass('prfic49 prfic4').addClass(DetailbuttonsActionMap[temp[i].id][enabled]);  
						htmlResp=tempObj.outerHtml();
				   }
				   else 
						htmlResp=htmlResp.replace(/\{\{icon\}\}/g,DetailbuttonsActionMap[temp[i].id]['icon']);
					htmlResp=htmlResp.replace(/\{\{ACTION_ID\}\}/g,temp[i].id+'-'+this.profileChecksum+"-"+this.name);
					
					if(typeof(loggedInJspcUser)!="undefined")
					{
						if(loggedInJspcUser=="")
							htmlResp=htmlResp.replace(/\{\{LOGIN_LOGOUT\}\}/g,"loginLayerJspc");
						else
							htmlResp=htmlResp.replace(/\{\{LOGIN_LOGOUT\}\}/g,"contactEngineIcon");
					}
					else
						htmlResp=htmlResp.replace(/\{\{LOGIN_LOGOUT\}\}/g,"contactEngineIcon");
					
					InnerHtml+=htmlResp;
					FinalHtmlElement.find("#PreFourButtonsDetail").html(InnerHtml);
					if(temp[i].enable==false)
					{
						FinalHtmlElement.find("#"+temp[i].id+'-'+this.profileChecksum+"-"+this.name).removeClass("contactEngineIcon").addClass("prfbg8CC cursd");
						InnerHtml=FinalHtmlElement.find("#PreFourButtonsDetail").html();
					}
					
					if( temp[i].id=="CHAT" ){ 
						if(info.userloginstatus!="undefined" && info.userloginstatus=="Online now")
						FinalHtmlElement.find("#"+temp[i].id+'-'+this.profileChecksum+"-"+this.name).addClass("OnlineChat");
					else 
						FinalHtmlElement.find("#"+temp[i].id+'-'+this.profileChecksum+"-"+this.name).addClass("classForDisableIcon").css('cursor','default').attr('title','Offline');
					}

					InnerHtml=FinalHtmlElement.find("#PreFourButtonsDetail").html();
				}
				FinalHtml=FinalHtmlElement.html();
			}
			else if(buttonArray.length==2)
			{
				var ulElement = $("#cEUlContainer").clone();
				var temp=buttonArray;
				for (var i = 0; i < temp.length; i++) {
				  htmlResp=ulElement.html();
				  if(temp[i].params!=undefined)
						htmlResp=htmlResp.replace(/\{\{params\}\}/g,temp[i].params);
					else
						htmlResp=htmlResp.replace(/\{\{params\}\}/g,'');
				htmlResp=htmlResp.replace(/\{\{ACTION_ID\}\}/g,temp[i].id+'-'+this.profileChecksum+"-"+this.name);
				htmlResp=htmlResp.replace(/\{\{Button_label\}\}/g,temp[i].label);
				
				InnerHtml+=htmlResp;
			  }
				var FinalHtml=$("#PreDetailTwoButton").clone();
				FinalHtml.find("#cEUlContainer").html(InnerHtml);
				FinalHtml.find("li").eq(1).removeClass('ml5').addClass('ml37');
				FinalHtml.find("i").eq(0).addClass('prfic47');
				FinalHtml.find("i").eq(1).addClass('prfic48');
				FinalHtml=FinalHtml.html();
				FinalHtml=FinalHtml.replace(/\{\{InfoMsg\}\}/g,buttonsObj.infomsglabel);
				if(typeof(loggedInJspcUser)!="undefined")
				{
					if(loggedInJspcUser=="")
						FinalHtml=FinalHtml.replace(/\{\{LOGIN_LOGOUT\}\}/g,"loginLayerJspc");
					else
						FinalHtml=FinalHtml.replace(/\{\{LOGIN_LOGOUT\}\}/g,"contactEngineIcon");
				}
				else
					FinalHtml=FinalHtml.replace(/\{\{LOGIN_LOGOUT\}\}/g,"contactEngineIcon");
			  }
			else if(buttonArray.length==1)
			{
				var FinalHtml=$("#PreDetailOneButton").html();
				var temp=buttonArray;
				FinalHtml=FinalHtml.replace(/\{\{ButtonDisplayName\}\}/g,temp[0].label);
				
        
        if (temp[0].id=='SHORTLIST'){
            var enabled='enable';
           if (temp[0].params)enabled=(temp[0].params.indexOf('true')>-1)?'disable':'enable';
         FinalHtml=FinalHtml.replace(/\{\{icon\}\}/g,DetailbuttonsActionMap[temp[0].id][enabled]);  
           }
           else 
				FinalHtml=FinalHtml.replace(/\{\{icon\}\}/g,DetailbuttonsActionMap[temp[0].id]['icon']);
				if(temp[0].params!=undefined)
					FinalHtml=FinalHtml.replace(/\{\{params\}\}/g,temp[0].params);
				else
					FinalHtml=FinalHtml.replace(/\{\{params\}\}/g,'');
				FinalHtml=FinalHtml.replace(/\{\{ACTION_ID\}\}/g,temp[0].id+'-'+this.profileChecksum+"-"+this.name);
				FinalHtml=FinalHtml.replace(/\{\{MessageDisplay\}\}/g,buttonsObj.infomsglabel);
				if(temp[0].id=="IGNORE")
					FinalHtml=FinalHtml.replace(/\{\{VisibilityClass\}\}/g,'vishid');
				else
					FinalHtml=FinalHtml.replace(/\{\{VisibilityClass\}\}/g,'');
				if(typeof(loggedInJspcUser)!="undefined")
				{
					if(loggedInJspcUser=="")
						FinalHtml=FinalHtml.replace(/\{\{LOGIN_LOGOUT\}\}/g,"loginLayerJspc");
					else
						FinalHtml=FinalHtml.replace(/\{\{LOGIN_LOGOUT\}\}/g,"contactEngineIcon");
				}
				else
					FinalHtml=FinalHtml.replace(/\{\{LOGIN_LOGOUT\}\}/g,"contactEngineIcon");
			 }
			}
	else
	 {
	 			if (buttonsObj.infomsglabel)
	 			{
					FinalHtml=$("#ExpectationDetailDiv").html();
					FinalHtml=FinalHtml.replace(/\{\{INFO_MSG_LABEL\}\}/g,buttonsObj.infomsglabel);
				}
	 }
				
            break;
            

		case 'CC':
		if(buttonArray){
          if(buttonArray.length==2)
          {
			  
			  preCCOneBtn=$("#PreContactCenterTwoButton").clone();
            var temp=buttonArray;
				          
				preCCOneBtn.find('.buttonLabel1').html(temp[0].label);
					if(temp[0].params!=undefined)
						preCCOneBtn.find('.buttonId1').attr('data',temp[0].params);
					if(temp[0].id)
					preCCOneBtn.find('.buttonId1').attr('id',temp[0].id+'-'+this.profileChecksum+"-"+this.name);
				if(!temp[0].enable)
					preCCOneBtn.find('.buttonId1').removeClass("cursp contactEngineIcon").addClass("prfbg8CC");				
					
					preCCOneBtn.find('.buttonLabel2').html(temp[1].label);
					if(temp[1].params!=undefined)
						preCCOneBtn.find('.buttonId2').attr('data',temp[1].params);
					if(temp[1].id)
					preCCOneBtn.find('.buttonId2').attr('id',temp[1].id+'-'+this.profileChecksum+"-"+this.name);
				if(!temp[1].enable)
					preCCOneBtn.find('.buttonId2').removeClass("cursp contactEngineIcon").addClass("prfbg8CC");
					
			FinalHtml=preCCOneBtn.html();
				
          }
          else if(buttonArray.length==1)
          {
            preCCOneBtn=$("#PreContactCenterOneButton").clone();
            var temp=buttonArray;				          
				preCCOneBtn.find('.buttonLabel').html(temp[0].label);
					if(temp[0].params!=undefined)
						preCCOneBtn.find('.buttonLabel').attr('data',temp[0].params);
					if(temp[0].id)
					preCCOneBtn.find('.buttonLabel').attr('id',temp[0].id+'-'+this.profileChecksum+"-"+this.name);
				if(temp[0].action=='COMMENT')if(postResponse){
					postResponseStr=postResponse.CC_CALL_MESSAGE+'|'+postResponse.CC_CALL_COMMENTS;
					preCCOneBtn.find('.buttonLabel').attr('postData',postResponseStr);} 
				if(buttonsObj.infomsglabel)
					preCCOneBtn.find('.msgDisplay').attr('id',temp[0].id+'_INFO').html(buttonsObj.infomsglabel);
				if(!temp[0].enable)
					preCCOneBtn.find('.buttonLabel').removeClass("cursp contactEngineIcon").addClass("prfbg8CC");
			FinalHtml=preCCOneBtn.html();		
			}
		}
		 else
        {
            	 if(buttonsObj.infomsglabel)
	 			{
					FinalHtml=$("#PreContactNoCenterButton").html();
					FinalHtml=FinalHtml.replace(/\{\{INFO_MSG_LABEL\}\}/g,buttonsObj.infomsglabel);
				}
		}
            break;
      }
      cECommonBinding();
	cECloseBinding(); 
return FinalHtml;

}

ContactEngineCard.prototype.postDisplay = function(Obj,profileChecksum,isError){
	if(!isError){
		if(Obj.actiondetails!=null)
		{
			if(Obj.actiondetails.contact4!=null)
			{
				currentActionLayer="ViewContact";

				if(Obj.actiondetails.contact4.value!='blur')
				{ 	 
					if (this.name=='CC') return this.postCCViewContactLayer(Obj,profileChecksum); 
					else return this.postViewContactLayer(Obj,profileChecksum);
				}
			}
			else if(Obj.actiondetails.writemsgbutton!=undefined)
			{
				currentActionLayer="Message";
				if(typeof Obj.actiondetails.limitWarning != "undefined"){
					var contactedUser = Obj.actiondetails.limitWarning.contactedUser;
					WARNING[contactedUser]  = Obj.actiondetails.limitWarning;
					WARNING.pageSource = this.name;
				}
				return postCommonMessageLayer(Obj,profileChecksum,this.name);
			}
			else if(typeof Obj.actiondetails.limitWarning != "undefined"){
				currentActionLayer = "limitWarning";
				return postCommonWarningLayer(Obj.actiondetails.limitWarning, profileChecksum, this.name, "FREE");
			}
			else if(Obj.actiondetails.footerbutton!=null)
			{
			  currentActionLayer="CommonDisplay";
			  return this.postCommonDisplayLayer(Obj,profileChecksum);
			}
		}
		else
			return this.postCommonDisplayLayer(Obj,profileChecksum);
		return this.postCommonDisplayLayer(Obj,profileChecksum);
	}
	else{
		currentActionLayer="error";
		return postDisplayError(this.name);
	}
};

function postDisplayError(pageSource)
{
	if(pageSource=="CC")
		return FinalHtml=$("#postCCErrorCommonLayer").html();
	else
		return FinalHtml=$("#postCommonErrorLayer").html();
}

function postCommonWarningLayer(Data, profilechecksum,pageSource, userType){
	var templateID = "postCommonErrorLayer";
	var contentDiv = "disp-cell";
	if(pageSource == "CC"){
		templateID = "postCCErrorCommonLayer";
		contentDiv = "js-genericMsg";
	}
	var template = $("#"+templateID).clone();
	var textToShow = "";
	if(userType === "FREE"){
		/* CASE FOR FREE MEMBER */
		/* Has two cases: 1 where limit is day/month/week type 2 where overall limit is type*/
		if(Data.type == "OVERALL"){
			textToShow = "<b>Please Note</b>: You have already sent "+
			Data.count+
			" out of "+
			Data.limit+
			" interests allowed on your account.<br>\
			We encourage you to send interests only to people where you match most\
			 of the preferences mentioned in their partner preference.";
		}else{
			textToShow = "<b>Please Note</b>: You have already sent "+
			Data.count+
			" out of "+
			Data.limit+
			" interests allowed till the "+Data.text+" ending "+Data.expiry+".<br/>\
			We encourage you to send interests only to people where you match most\
			 of the preferences mentioned in their partner preference.<br/><br/>"+
			"<a href='/static/page/disclaimer#limitsTable' class='colr5' >Click Here</a> to know more about Interest Limits";
		}
		

		template.find("."+contentDiv).html(textToShow);

		if(pageSource == "CC"){
			template.find('span').removeClass("disp-none");
			template.find('i').addClass("disp-none");
		}
	}else if(userType === "PAID"){
		/* CASE FOR PAID MEMBER */
		var textToShow = "<b>Please Note</b>: You have already sent "+
		Data.count+
		" out of "+
		Data.limit+
		" interests allowed till the "+Data.text+" ending "+Data.expiry+".<br/>\
		We encourage you to send interests only to people where you match most\
		 of the preferences mentioned in their partner preference.<br/><br/>"+
		"<a href='/static/page/disclaimer#limitsTable' class='colr5' >Click Here</a> to know more about Interest Limits";
		template.find("."+contentDiv).html(textToShow);
		if(pageSource == "CC"){
			template.find("."+contentDiv).css({"font-size": "11px"});
			template.find('span').removeClass("disp-none");
			template.find('i').addClass("disp-none");
		}
	}
	return template;
}

function postCommonMessageLayer(Obj,profileChecksum,pageSource)
{
	if(pageSource=="CC")
		var FinalHtml=$("#postContactCenterCommonMessageLayer").html();
	else
		var FinalHtml=$("#postCommonMessageLayer").html();
	FinalHtml=FinalHtml.replace(/\{\{ACTION_ID\}\}/g,Obj.actiondetails.writemsgbutton.id+"-"+profileChecksum+"-"+pageSource);
	if(Obj.actiondetails.writemsgbutton.params!=undefined)
		FinalHtml=FinalHtml.replace(/\{\{params\}\}/g,Obj.actiondetails.writemsgbutton.params);
	else
		FinalHtml=FinalHtml.replace(/\{\{params\}\}/g,'');
	if(Obj.actiondetails.draftmessage!=undefined)
		FinalHtml=FinalHtml.replace(/\{\{MESSAGE_TEXT\}\}/g,Obj.actiondetails.draftmessage);
	else
		FinalHtml=FinalHtml.replace(/\{\{MESSAGE_TEXT\}\}/g,"Write a personalized message");

	return FinalHtml;
}

ContactEngineCard.prototype.postCommonDisplayLayer=function(Obj,profileChecksum)
{

	if(this.name=="CC"){
		var FinalHtml=$("#postContactCenterCommonLayer").html();
	}
	else
		var FinalHtml=$("#postCommonDisplayLayer").html();
	if(typeof(Obj.actiondetails.headerlabel_viewSimilar)!=undefined && Obj.actiondetails.headerlabel_viewSimilar!=null){
		if(typeof(Obj.actiondetails.headerlabel_viewSimilar)!=undefined && Obj.actiondetails.headerlabel_viewSimilar!=null){
			FinalHtml=FinalHtml.replace(/\{\{header\}\}/g,Obj.actiondetails.headerlabel_viewSimilar);
			FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_header\}\}/g,'pt25');
		}
		
		if(typeof(Obj.actiondetails.viewSimilarUsername)!=undefined && Obj.actiondetails.viewSimilarUsername!=null)
		{	
			FinalHtml=FinalHtml.replace(/\{\{ViewSimiarProfile\}\}/g,"<a href='/search/viewSimilarProfile?profilechecksum="+profileChecksum+"&stype=VSI&contactedProfileDetails=hide&SIM_USERNAME="+Obj.actiondetails.viewSimilarUsername+"' class='color5 fontreg'>  View Similar Profiles</a>");	
		}
		else
		{
			FinalHtml=FinalHtml.replace(/\{\{ViewSimiarProfile\}\}/g,'');
		}	
	}
	else{
		FinalHtml=FinalHtml.replace(/\{\{header\}\}/g,'');
		FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_header\}\}/g,'');
		FinalHtml=FinalHtml.replace(/\{\{ViewSimiarProfile\}\}/g,'');
	}
	if(Obj.actiondetails.errmsglabel!=null)
	{
		FinalHtml=FinalHtml.replace(/\{\{ErrorMsg\}\}/g,Obj.actiondetails.errmsglabel);
		if(Obj.actiondetails.infomsglabel!=null)
			FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Error\}\}/g,'pt10');
		else
			FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Error\}\}/g,'pt25');
	}
	else
	{
		if(this.name=="CC" && Obj.actiondetails.infomsglabel!=null)
			FinalHtml=FinalHtml.replace(/\{\{ErrorMsg\}\}/g,Obj.actiondetails.infomsglabel);
		else
			FinalHtml=FinalHtml.replace(/\{\{ErrorMsg\}\}/g,'');
		FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Error\}\}/g,'vishid');
	}
	if(Obj.actiondetails.infomsglabel!=null)
	{
		FinalHtml=FinalHtml.replace(/\{\{InfoMsg\}\}/g,Obj.actiondetails.infomsglabel);
		if(Obj.actiondetails.errmsglabel!=null)
			FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Info\}\}/g,'pt10');
		else
			FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Info\}\}/g,'pt25');
	}
	else
	{
		FinalHtml=FinalHtml.replace(/\{\{InfoMsg\}\}/g,"");
		FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Info\}\}/g,'vishid');
	}
	if(Obj.actiondetails.footerbutton!=null)
	{
		FinalHtml=FinalHtml.replace(/\{\{ButtonLabel\}\}/g,Obj.actiondetails.footerbutton.label);
		FinalHtml=FinalHtml.replace(/\{\{ButtonClass\}\}/g,"");
		FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Button\}\}/g,'');
		if(Obj.actiondetails.footerbutton.params!=null)
			FinalHtml=FinalHtml.replace(/\{\{paramData\}\}/g,Obj.actiondetails.footerbutton.params);
		else
			FinalHtml=FinalHtml.replace(/\{\{paramData\}\}/g,null);
    if(Obj.actiondetails.footerbutton.text!=null)
    {
      FinalHtml=FinalHtml.replace(/\{\{ButtonLabelText\}\}/g,Obj.actiondetails.footerbutton.text);
      FinalHtml=FinalHtml.replace(/\{\{ButtonLabelShiftClass\}\}/g,"ccp10");
    }
    else
    {
      FinalHtml=FinalHtml.replace(/\{\{ButtonLabelText\}\}/g,"");
      FinalHtml=FinalHtml.replace(/\{\{ButtonLabelShiftClass\}\}/g,"txtc");
    }
		FinalHtml=FinalHtml.replace(/\{\{ACTION_ID\}\}/g,Obj.actiondetails.footerbutton.action+"-"+profileChecksum+"-"+this.name);
	}
	else
	{
		FinalHtml=FinalHtml.replace(/\{\{ButtonLabel\}\}/g,'');
		FinalHtml=FinalHtml.replace(/\{\{ButtonLabelText\}\}/g,"");
		FinalHtml=FinalHtml.replace(/\{\{ButtonLabelShiftClass\}\}/g,"txtc");
		FinalHtml=FinalHtml.replace(/\{\{ButtonClass\}\}/g,"disp-none");
		FinalHtml=FinalHtml.replace(/\{\{VisibilityClass_Button\}\}/g,'vishid');
		FinalHtml=FinalHtml.replace(/\{\{paramData\}\}/g,null);
		FinalHtml=FinalHtml.replace(/\{\{ACTION_ID\}\}/g,'ActionButton'+profileChecksum+"-"+this.name);
	}

	if(typeof(Obj.actiondetails.newerrmsglabel) != "undefined" && Obj.actiondetails.newerrmsglabel != null)
	{
		FinalHtml = FinalHtml.replace(/\{\{VisibilityClass_freeMember\}\}/g,'');
		FinalHtml = FinalHtml.replace(/\{\{VisibilityClass_Othercase\}\}/g,'disp-none');
		
		FinalHtml = FinalHtml.replace(/\{\{ErrorMsglabel\}\}/g,Obj.actiondetails.newerrmsglabel);
		FinalHtml = FinalHtml.replace(/\{\{MembershipMsgHeader\}\}/g,Obj.actiondetails.membershipmsgheading);

		FinalHtml = FinalHtml.replace(/\{\{subheading1\}\}/g,Obj.actiondetails.membershipmsg.subheading1);
		FinalHtml = FinalHtml.replace(/\{\{subheading2\}\}/g,Obj.actiondetails.membershipmsg.subheading2);
		FinalHtml = FinalHtml.replace(/\{\{subheading3\}\}/g,Obj.actiondetails.membershipmsg.subheading3);

		if(typeof(Obj.actiondetails.offer) != "undefined" && Obj.actiondetails.offer != null)
		{
			FinalHtml = FinalHtml.replace(/\{\{MembershipOffer\}\}/g,Obj.actiondetails.offer.membershipOfferMsg1 + " " + Obj.actiondetails.offer.membershipOfferMsg2);
			FinalHtml = FinalHtml.replace(/\{\{currency\}\}/g,Obj.actiondetails.membershipoffercurrency);

			if(typeof(Obj.actiondetails.strikedprice) != "undefined" && Obj.actiondetails.strikedprice != null)
			{
				FinalHtml = FinalHtml.replace(/\{\{oldPrice\}\}/g,Obj.actiondetails.strikedprice);
				FinalHtml = FinalHtml.replace(/\{\{strikedPriceDisp\}\}/g,'');
			}
			else
			{
				FinalHtml = FinalHtml.replace(/\{\{strikedPriceDisp\}\}/g,'disp-none');
			}

			FinalHtml = FinalHtml.replace(/\{\{newPrice\}\}/g,Obj.actiondetails.discountedprice);
			FinalHtml = FinalHtml.replace(/\{\{MembershipOfferDisp\}\}/g,'');
			FinalHtml = FinalHtml.replace(/\{\{LowestOfferDisp\}\}/g,'disp-none');
		}
		else if(typeof(Obj.actiondetails.lowestoffer) != "undefined" && Obj.actiondetails.lowestoffer != null)
		{
			FinalHtml = FinalHtml.replace(/\{\{LowestOffer\}\}/g,Obj.actiondetails.lowestoffer);
			FinalHtml = FinalHtml.replace(/\{\{MembershipOfferDisp\}\}/g,'disp-none');
			FinalHtml = FinalHtml.replace(/\{\{LowestOfferDisp\}\}/g,'');
		}
		else
		{
			FinalHtml = FinalHtml.replace(/\{\{MembershipOfferDisp\}\}/g,'disp-none');
			FinalHtml = FinalHtml.replace(/\{\{LowestOfferDisp\}\}/g,'disp-none');
		}
		FinalHtml=FinalHtml.replace(/\{\{MEM_ACTION_ID\}\}/g,Obj.actiondetails.footerbutton.action+"-"+profileChecksum+"-"+this.name);
		FinalHtml=FinalHtml.replace(/\{\{ButtonLabelNew\}\}/g,Obj.actiondetails.footerbutton.newlabel);
	}
	else
	{
		FinalHtml = FinalHtml.replace(/\{\{VisibilityClass_freeMember\}\}/g,'disp-none');
		FinalHtml = FinalHtml.replace(/\{\{VisibilityClass_Othercase\}\}/g,'');
	}
	

	return FinalHtml;

}


ContactEngineCard.prototype.postOnlyButtonsDisplay=function(Obj,profileChecksum)
{

buttonDetails=Obj.buttondetails;

if(buttonDetails.buttons!=null)
if(buttonDetails.buttons.length==1){

button=buttonDetails.buttons[0];
elementObj=$($('#postUndoLayerDisplay').html());
elementObj.find('.contactEngineIcon').attr('id',button.id).html(button.label).attr('data',button.params);

}






}



ContactEngineCard.prototype.postCCViewContactLayer= function(Obj,profileChecksum){
	viewContactElement=$($("#postCCCommonViewLayer").html());
	actionDetails=Obj.actiondetails;
	username=this.buttonObj.parent.find('.js-username').html();
	userLoginStatus=this.buttonObj.parent.find('.js-userLoginStatus').html();
	phoneContact='';


	if (actionDetails.contact1){phoneContact+=(actionDetails.contact1.value+',    ');}
	if (actionDetails.contact2){phoneContact+=(actionDetails.contact2.value+',    ');}
	if (actionDetails.contact3){phoneContact+=(actionDetails.contact3.value+' ');}
	
	if(!phoneContact){
		if(actionDetails.contact1_message || actionDetails.contact2_message || actionDetails.contact3_message )
 		{
 			if((actionDetails.contact1_message && actionDetails.contact1_message.indexOf('accept')!=-1) 
			|| (actionDetails.contact2_message && actionDetails.contact2_message.indexOf('accept')!=-1)
			|| (actionDetails.contact3_message && actionDetails.contact3_message.indexOf('accept')!=-1) )
				phoneContact = "Phone number visible on accept";
			else phoneContact = "Phone number hidden";
		}
	}
	if (!phoneContact) phoneContact='NA';
		viewContactElement.find('.js-phoneContactCC').removeClass('disp-none').find('.js-phoneValuesCC').html(phoneContact);
		

	if(actionDetails.contact4)
		viewContactElement.find('.js-emailContactCC').removeClass('disp-none').find('.js-emailValueCC').html(actionDetails.contact4.value);

	if(actionDetails.contact9){
		viewContactElement.find('.js-relationshipPhoneCC').removeClass('disp-none').find('.js-relationshipPhoneCC').html(actionDetails.contact9.value);
		if(actionDetails.contact4){
			viewContactElement.find('.js-emailContactCC').removeClass('pt10').addClass('pt6');
		}
	}

	if(actionDetails.leftviewvalue){
	viewContactElement.find('.js-leftToView1').removeClass('disp-none').html(actionDetails.leftviewvalue);
	viewContactElement.find('.js-leftToView2').removeClass('disp-none').html(actionDetails.leftviewlabel);
									}
if(actionDetails.contact5)
viewContactElement.find('.js-timeToCallCC').html('('+actionDetails.contact5.value+')');
viewContactElement.find('.js-usernameCC').html(username);
viewContactElement.find('.js-onlineStatusCC').html(userLoginStatus);
viewContactElement.find(".SMSContactsDiv").removeClass('disp-none').attr('profileChecksum',profileChecksum).bind('click',function(){SMSContactsDivBinding(this);});


return viewContactElement;




}



ContactEngineCard.prototype.postViewContactLayer=function(Obj,profileChecksum)
{ 
	
	var viewContactElement=$("#postViewContactLayer").clone();
	var liFinalHtml="";
	if(Obj.actiondetails.contact4!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact4,false);
	}
	if(Obj.actiondetails.contact1!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact1,true,'M','',profileChecksum);
	}
	else if(Obj.actiondetails.contact1_message)
	{
		liFinalHtml+=viewContactHiddenLabel('Phone No.',Obj.actiondetails.contact1_message);
	}
	
	if(Obj.actiondetails.contact2!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact2,true,'L','',profileChecksum);
	}
	else if(Obj.actiondetails.contact2_message)
	{
		liFinalHtml+=viewContactHiddenLabel('Landline',Obj.actiondetails.contact2_message);
	}
	
	if(Obj.actiondetails.contact3!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact3,false);
	}
	else if(Obj.actiondetails.contact3_message)
	{
		liFinalHtml+=viewContactHiddenLabel('Alternate No.',Obj.actiondetails.contact3_message);
	}
	
	if(Obj.actiondetails.contact5!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact5,false);
	}
	
	if(Obj.actiondetails.contact7!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact7,false);
	}
	
	if(Obj.actiondetails.contact8!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact8,false);
	}

	if(Obj.actiondetails.contact9!=null)
	{
		liFinalHtml+=ViewContactLiCreate(Obj.actiondetails.contact9,true,'L','',profileChecksum);
	}
	
	viewContactElement.find("#cEViewContactListing").html(liFinalHtml);
	var FinalHtml=viewContactElement.html();
	FinalHtml=FinalHtml.replace(/\{\{USERNAME\}\}/g,Obj.actiondetails.headerlabel);
	if(Obj.actiondetails.topmsg!=null && Obj.actiondetails.topmsg!='undefined')
	{
		FinalHtml=FinalHtml.replace(/\{\{DETAILED_PROFILE_INFO\}\}/g,Obj.actiondetails.topmsg);
		viewContactElement.html(FinalHtml);
		if(Obj.actiondetails.membership && Obj.actiondetails.membership!=undefined)
			viewContactElement.find("#cEMembershipEvalue").removeClass("disp-none");
	}
	if(Obj.actiondetails.contact6!=null )
	{
		FinalHtml=FinalHtml.replace(/\{\{DETAILED_PROFILE_INFO\}\}/g,Obj.actiondetails.contact6.label +" "+Obj.actiondetails.contact6.value);
		if(Obj.actiondetails.leftviewvalue!=null && Obj.actiondetails.leftviewvalue!='undefined')
		{
			FinalHtml=FinalHtml.replace(/\{\{CONTACTS_LEFT\}\}/g,Obj.actiondetails.leftviewvalue);
			viewContactElement.html(FinalHtml);
			viewContactElement.find("#CONTACTS_LEFT").removeClass("disp-none");
		}
		else
			viewContactElement.html(FinalHtml);
			
	}
	FinalHtml=viewContactElement.html();
	jObject=$(FinalHtml);
	var profileChecksum=this.buttonObj.profileChecksum;		

	jObject.find(".SMSContactsDiv").removeClass('disp-none').attr('profileChecksum',profileChecksum).bind('click',function(){SMSContactsDivBinding(this);});
	jObject.find('.reportInvalid').bind('click',function(){showReportInvalidLayer(this);});
	return jObject;
}

function ViewContactLiCreate(Obj,reportInvalid,phoneType,label,profileChecksum)
{
	var liHtml = $("#cEViewContactListing").html();
	if(Obj!=null)
	{
		liHtml=liHtml.replace(/\{\{CONTACT_NAME\}\}/g,Obj.label);
		liHtml=liHtml.replace(/\{\{CONTACT_VALUE\}\}/g,Obj.value);
		if(reportInvalid){
			liHtml=liHtml.replace(/\{\{phonetype\}\}/g,"phoneType='"+phoneType+"'");
			liHtml=liHtml.replace(/\{\{DISP_REPORT\}\}/g,"");
			liHtml=liHtml.replace(/\{\{prochecksum\}\}/g,"prochecksum='"+profileChecksum+"'");

		}
		else
			liHtml=liHtml.replace(/\{\{DISP_REPORT\}\}/g,"disp-none");
	}
	else
	{
		liHtml=liHtml.replace(/\{\{CONTACT_NAME\}\}/g,label);
		liHtml=liHtml.replace(/\{\{CONTACT_VALUE\}\}/g,'NA');
		if(reportInvalid)
			liHtml=liHtml.replace(/\{\{DISP_REPORT\}\}/g,"");
		else
			liHtml=liHtml.replace(/\{\{DISP_REPORT\}\}/g,"disp-none");
	}
	return liHtml;
}

function viewContactHiddenLabel(label,value){
	var liHtml = $("#cEViewContactListing").html();
		liHtml=liHtml.replace(/\{\{CONTACT_NAME\}\}/g,label);
		liHtml=liHtml.replace(/\{\{CONTACT_VALUE\}\}/g,value);
		liHtml=liHtml.replace(/\{\{DISP_REPORT\}\}/g,"disp-none");
	return liHtml;
}

function cECommonBinding()
{
	$(".contactEngineIcon").off("click");
	$(".contactEngineIcon").on('click',function() {
	cEButtonActionCalling($(this));
	return false;
	})
}

function cECloseBinding()
{
	
$(".closeContactDetailLayer").unbind("click");  
$(".closeContactDetailLayer").bind('click',function() {
	var layerDiv=$(this).closest('#contactEngineLayerDiv');
  
	if($( "#contactEngineLayerDiv ~ div" ))
	{
		var viewSimilarDiv=$( "#contactEngineLayerDiv ~ div" );
		if(viewSimilarDiv.attr('id'))
		{
			var arr= viewSimilarDiv.attr('id').split('-');
			if($("#jsCcVSP-"+arr[1]).length)
				$("#jsCcVSP-"+arr[1]).css("display","");
		}
	}
	var layerDivParent = layerDiv.parent();
	superdata = layerDiv.attr('superdata');
	layerDiv.remove();
	if(superdata){
	layerDivParent.append(postCommonWarningLayer(WARNING[superdata], superdata, WARNING.pageSource, "PAID"));
	cECloseBinding();
	}
	return false;
	});
	
	
}

function DetailPageBinding()
{
	
	$(".cEIgnoreDetailProfile").bind('click',function() {
		ignoreLayerOpened=1;
		cEButtonActionCalling($(this));
	});
	$(".cEUndoIgnoreLayerDetail").bind('click',function() {
		ignoreLayerOpened=0;
		cEButtonActionCalling($(this));
	});
	
}
var ifChatListingIsCreated=0;
function cEButtonActionCalling(elementObj)
{
	var arrID=elementObj.attr('id').split('-');

	if(arrID[0]!="CHAT")
	{
		var buttonObj=new Button(elementObj);
		buttonObj.request();
		//update chat list on pc based on contact engine action type
		updateChatRosterList(elementObj,arrID);
	}
	else
	{
		if(elementObj.attr('data') && elementObj.hasClass('OnlineChat'))
		{
			var data=elementObj.attr('data').split(',');

                        if($("chat-box[user-id='"+data[1]+"'] .downBarUserName").length)
                                $("chat-box[user-id='"+data[1]+"'] .downBarUserName").click()

			if(data[0]!="undefined" && data[1]!="undefined")
			{
				if($('#js-chatLogin').length)
				{
					invokePluginLoginHandler($("#js-chatLogin").click());
				}
				else if($(".js-minpanel").length != 0){
					$(".js-minpanel").click();
				}
				var checkExist = setInterval(function() {
				if (ifChatListingIsCreated==1){
				      clearInterval(checkExist);
					openNewJSChat(arrID[1],data);
				   }
				}, 100);
				//openChatWindow(data[1],arrID[1],data[1],data[0],'','');
			}
		}
	}
}

function updateChatRosterList(elementObj,arrID){
	if(arrID[0] == "IGNORE"){
		//console.log("ignore from cEButtonActionCalling");
		var chatData = elementObj.attr("data-chat");
		if(chatData != undefined){
			var chatSplitData = chatData.split(",");
			if(updateNonRosterListOnCEAction && typeof updateNonRosterListOnCEAction == "function"){
				updateNonRosterListOnCEAction({
										"user_id":chatSplitData[0],
										"action":chatSplitData[1]
									});
			}
		}
	}
	else{
		//non roster actions(shortlist,remove shortlist) other than ignore and chat
		if(updateNonRosterListOnCEAction && typeof updateNonRosterListOnCEAction == "function"){
			if(arrID[1] != undefined){
				var profileSplitData = arrID[1].split("i");
				var chatStatus = "offline";
				if(elementObj.parent().find(".OnlineChat").length == 1){
					chatStatus = "online";
				}
				var pcChatData = [],action = "",group="";
				switch(arrID[0]){
					case "SHORTLIST":
						var isShortlisted = elementObj.attr("data");
						if(isShortlisted.indexOf("&shortlist=false")>-1){
							action = "ADD";
							group = "shortlist";
						}
						else if(isShortlisted.indexOf("&shortlist=true")>-1){
							action = "REMOVE";
							group = "shortlist"
						}
						break;
				}
				if(action != "" && elementObj.parent().parent().hasClass("pcChatHelpData")){
					pcChatData = (elementObj.parent().parent().attr("data-pcChat")).split(",");
					//console.log("from cEButtonActionCalling ",profileSplitData,action,group,chatStatus);
					updateNonRosterListOnCEAction({
											"user_id":profileSplitData[1],
											"action":action,
											"chatStatus":chatStatus,
											"username":pcChatData[0],
											"profilechecksum":pcChatData[1],
											"groupId":group,
											"otherGender":pcChatData[2]
										});
				}
			}
		}
	}
}

function hpOverlayBinding()
{
	$(".js-overlay").bind('click',function() {
			$('#ignore-layer').fadeOut(300,"linear",function(){ $('.js-overlay').fadeOut(200,"linear")});	
			$(".js-overlay").unbind('click');
		});		
}


/*
function phoneReportInvalid(ele,profileChecksum){
if(!profileChecksum || !ele) return;

var parent=$(ele).closest('.cEParent');
parent.find('.js-usernameCE');

showCommonLoader();
var phoneType=$(ele).attr('phoneType');
if (phoneType=='L') {var mobile='N';var phone='Y';}
if (phoneType=='M') {var mobile='Y';var phone='N';}
ajaxConfig=new Object();
ajaxData={'mobile':mobile,'phone':phone,'profilechecksum':profileChecksum};
ajaxConfig.url='/phone/reportInvalid';
ajaxConfig.data=ajaxData;
ajaxConfig.type='POST';
ajaxConfig.success=function(response){
	          	hideCommonLoader();

		var layerObj=$("#reportInvalidLayer");
		layerObj.find('.js-usernameCC').html(response.username);
		layerObj.find('.js-otherProfilePic').attr('src',response.userPhoto);


		$('.js-overlay').eq(0).fadeIn(200,"linear",function(){$('#reportInvalidLayer').fadeIn(300,"linear",function(){})}); 
closeReportInvalidLayer=function(){

		$('#reportInvalidLayer').eq(0).fadeOut(100,"linear",function(){$('.js-overlay').fadeOut(300,"linear",function(){})}); 

}
$('.js-overlay').bind('click',closeReportInvalidLayer);

	}

jQuery.myObj.ajax(ajaxConfig);


}

*/

function prePostResponse(type,buttonParent){
switch (type){

case 'REMOVE':
var tempObj=$($("#postCCErrorCommonLayer").html().trim());
tempObj.find('.js-genericMsg').html('This profile has been removed from your call list and will not be called by us');
tempObj.find('.sendcross1').css('display','none');
buttonParent.prepend(tempObj);
break;

case 'COMMENT':
var tempObj=$("#callerComment-layer");
var str=buttonParent.find('[id^="COMMENT"]').attr('postdata');
if(!str) return; 
var strArr=str.split('|');
tempObj.find('.otherProfilePic').attr('src',buttonParent.find('img').attr('src'));
tempObj.find('.js-usernameCC').html(buttonParent.find('.js-username').html());

tempObj.find('.js-commHeading').html(strArr[0]);
tempObj.find('.js-commMessage').html(strArr[1]);

		$('.js-overlay').eq(0).fadeIn(200,"linear",function(){$('#callerComment-layer').fadeIn(300,"linear",function(){})}); 
closeCommentLayer=function(){

		$('.js-overlay').eq(0).fadeOut(200,"linear",function(){$('#callerComment-layer').fadeOut(300,"linear",function(){})}); 

}
$('.js-overlay').bind('click',closeCommentLayer);
tempObj.find('.closeCommLayer').bind('click',closeCommentLayer);
break;





}


}


$(document).ready(function() {
 	cECommonBinding();
  	DetailPageBinding(); 

  	// binding for viewprofilepage for the communication history page
$('.communicationParent').bind('click',
			function () {	
					communicationLayerAjax(1);
			});	
	customOptionButton('report_profile');
 })

function openChatWindow(aJid,param,profileID,userName,have_photo,checksum){
	//alert("login or not>>>>~$LOGIN`");
	
	//alert("top.ajaxChatRequest is >>>>>"+top.ajaxChatRequest);
	if(top.ajaxChatRequest){
		top.ajaxChatRequest(aJid,param,profileID,userName,have_photo,checksum);
	}else{
		var ajaxConfig = {};
		ajaxConfig.url = '/profile/jsChatBarNotFound.php';

		var onSuccess = function(){
			$(".js-overlay").bind("click",function(){
				closeCurrentLayerCommon();
			});
			$("#closeButtonCALayer").bind("click",function(){
				closeCurrentLayerCommon();
			});
		};
		sendAjaxHtmlDisplay(ajaxConfig,onSuccess);

	}
	
}


function reportInvalidReason(ele,profileChecksum,username,photoUrl){
if(!profileChecksum || !ele) return; 
var reason;
var Otherreason='';
var layerObj=$("#reportInvalidReason-layer");
if(layerObj.find("#otherOptionBtn").is(':checked')) {
 reason=layerObj.find("#otherOptionMsgBox textarea").eq(0).val();
	if(!reason) {layerObj.find('#errorText').removeClass('disp-none').html('Please Enter The Comments');return;}
	
	Otherreason = reason;
}
$('.js-overlay').unbind('click');

var phoneType=ele;
if (phoneType=='L') {var mobile='N';var phone='Y';}
if (phoneType=='M') {var mobile='Y';var phone='N';}

var rCode = $("input:radio[name=report_profile]:checked").val();

ajaxConfig=new Object();
if(!layerObj.find(".selected").length) {layerObj.find('#RAReasonHead').text("*Please Select a reason").addClass('colorerror').removeClass('color12');return;}
if(!reason) reason=layerObj.find(".selected").eq(0).text().trim();
if(!reason) return;
showCommonLoader();
reason=$.trim(reason);
ajaxData={'mobile':mobile,'phone':phone,'profilechecksum':profileChecksum,'reasonCode':rCode,'otherReasonValue':Otherreason};
ajaxConfig.url='/phone/reportInvalid';
ajaxConfig.data=ajaxData;
ajaxConfig.type='POST';
ajaxConfig.success=function(response){

	if(response.responseStatusCode == '1')
	{	
		if(typeof response.heading != 'undefined'){
		$('#headingReportInvalid').html(response.heading);
		}
	}
	else if(response.responseStatusCode == '0')
	{	
		$('#headingReportInvalid').html('Phone no. reported as invalid');
	}
	$('#invalidConfirmMessage').html(response.message);
	$('#reportInvalidReason-layer').fadeOut(300,"linear");
	hideCommonLoader();
	var jObject=$("#reportInvalidConfirmLayer");
	jObject.find('.js-username').html(username);
	jObject.find('.js-otherProfilePic').attr('src',photoUrl);
	layerObj.find("#otherOptionMsgBox textarea").val('');
		$('.js-overlay').eq(0).fadeIn(200,"linear",function(){$('#reportInvalidConfirmLayer').fadeIn(300,"linear",function(){})}); 

closeInvalidConfirmLayer=function() {

$('#reportInvalidConfirmLayer').fadeOut(200,"linear",function(){ 
	$('.js-overlay').fadeOut(300,"linear")});
	$('.js-overlay').unbind('click');

};

$('.js-overlay').unbind().bind('click',closeInvalidConfirmLayer);

	}

jQuery.myObj.ajax(ajaxConfig);

}

function showReportInvalidLayer(obj){
	var layerObj=$("#reportInvalidReason-layer");
	if(!layerObj.find(".selected").length) {layerObj.find('#RAReasonHead').text("Select reason").addClass('color12').removeClass('colorerror');}
	var jObject=$("#reportInvalidReason-layer");
	if(typeof(viewedProfileUsername)!="undefined" && viewedProfileUsername){
	var otherUser = viewedProfileUsername;
	var imgUrl = $("#profilePicScrollBar").attr('src');
	jObject.find('.js-username').html(otherUser);
	jObject.find('.js-otherProfilePic').attr('src',imgUrl);
	}
	else
	{	
		var parent = $(obj).closest('.CEParent');
		var otherUser = parent.find('.js-usernameCE').html();
		var imgUrl = parent.find('.js-searchTupleImage').eq(0).find('img').eq(0).attr('src');
		jObject.find('.js-username').html(otherUser);
		jObject.find('.js-otherProfilePic').attr('src',imgUrl);

	}
var phoneType = $(obj).attr('phonetype');
var profileChecksum = $(obj).attr('prochecksum');
$("#reportInvalidReasonLayer").unbind().bind('click',function(){reportInvalidReason(phoneType,profileChecksum,otherUser,imgUrl);});	

$('.js-overlay').eq(0).fadeIn(200,"linear",function(){$('#reportInvalidReason-layer').fadeIn(300,"linear",function(){})}); 
$('.js-overlay').unbind();

closeReportInvalidLayer=function() {

$('#reportInvalidReason-layer').fadeOut(200,"linear",function(){ 
	$('.js-overlay').fadeOut(300,"linear")});
	
};
$('#reportInvalidCross').unbind().bind('click',closeReportInvalidLayer);
}


function customOptionButton(optionBtnName) {

       var checkBox = $('input[name="' + optionBtnName + '"]');
       $(checkBox).each(function() {
               $(this).wrap("<span class='custom-checkbox-reportAbuse'></span>");
                       if ($(this).is(':checked')) {
                               $(this).closest('li').addClass("selected");
                       }
                       else $(this).closest('li').removeClass("selected"); 
               });
                $(checkBox).click(function() {
//                $(".otherOptionMsgBox").each(function() {
//                $(this).addClass("disp-none");
//                })
                    $('input[name="' + optionBtnName + '"]').closest('li').removeClass('selected');
                    $(this).closest('li').addClass("selected");



                    if($(this).attr("id") == "openBox") {
                        
                     //$(this).closest("li").find(".otherOptionMsgBox").removeClass("disp-none");
                     $(".js-reportAttachLayer").removeClass('disp-none');
                     $(".js-reportOptionLayer").addClass('disp-none');
                     $('.js-selectedOption').html( $(this).closest('li').find('.reason').text() );
                    } else {
                     $(this).closest("li").find(".otherOptionMsgBox").addClass("disp-none");
                    }
               });
}


var arrReportAbuseFiles = [];
var bUploadAttachmentInProgress = false;
var bUploadingDone = false;

/**
 * 
 */
function attachAbuseDocument(event) {

    var dom = $("<input>",{id:"file", type:"file", accept : ".jpg,.bmp,.jpeg,.gif,.png", multiple:"multiple"});
    var MAX_FILE_SIZE_IN_MB = 6;
    
    var ShowErrorMsg = function(msg){
        $('.js-attachError').html(msg);  
        $('#reportAbuseList_1').mCustomScrollbar("scrollTo","bottom",{ scrollEasing:"easeOut"});
    }
    
    var HideErrorMsg = function() {
        $('.js-attachError').html("");  
    }
    
    var onCrossClick = function() {
        HideErrorMsg();
        var result = [];
        var self = $(this);
        for(var itr = 0; itr < arrReportAbuseFiles.length; itr++) {
            if(arrReportAbuseFiles[itr].myId == self.attr('id')) {
                
                //If file is already uploaded then remove from server also
                if( "undefined" != typeof arrReportAbuseFiles.tempAttachmentId && arrReportAbuseFiles[itr].uploaded ) {
                    
                    var formData = new FormData();                    
                    var apiUrl = "/api/v1/faq/abuseDeleteAttachment"; 
                    
                    formData.append('feed[attachment_id]', arrReportAbuseFiles['tempAttachmentId'] );
                    formData.append('feed[file_name]', arrReportAbuseFiles[itr].name );
                    var ajaxConfig=new Object();
                    showCommonLoader();
                    ajaxConfig = {
                        url     : apiUrl,
                        type  : 'POST',
                        data    : formData,
                        async   : true,
                        cache: false,
                        processData: false,
                        success : function ( response ) {
                                        //TODO : Update Loader Layer
                                        hideCommonLoader();
                                        if(response.responseStatusCode == 0) {
                                           self.parent().remove();
                                        } else {
                                            result.push(arrReportAbuseFiles[itr]);
                                            
                                            ShowTopDownError(['Something went wrong. Please try again.'], 2000);
                                        }
                                    },
                        error   :  function ( response ) {
                                       
                                       result.push(arrReportAbuseFiles[itr]);
                                       ShowTopDownError(['Something went wrong. Please try again.'], 2000);
                                       return ;
                                    },
                    };
                    jQuery.myObj.ajax(ajaxConfig);
                }else {
                    self.parent().remove();
                }
                
                continue;
            }
            
            result.push(arrReportAbuseFiles[itr]);
        }
        if(arrReportAbuseFiles.tempAttachmentId) {
            result.tempAttachmentId = arrReportAbuseFiles.tempAttachmentId;
        }
        arrReportAbuseFiles = result;
    }
    
    
    /**
     * 
     */
    var createPhotoPreview = function(fileObject) {
        /**
         *  
         <li class="clearfix">
            <div class="fl wid80p textTru">
                Screesho1233.jpeg
            </div>
            <div class="fr pos-rel">
                <div class="attc_close"></div>
            </div>                                                
        </li>
         */
        var previewDom = $("<li />", {"class" : "clearfix"});
        var picNameDom = $("<div />", {"class" : "fl wid80p textTru"}).html(fileObject.name);
        
        previewDom.append(picNameDom);
        
        var closeOuterDivDom = $("<div />", {class:"js-closeIcon fr pos-rel cursp", id :fileObject.myId});
        closeOuterDivDom.append( $("<div />", {class : 'attc_close'} ) );
        closeOuterDivDom.on('click',onCrossClick);
        
        previewDom.append(closeOuterDivDom);
                
        var reader = new FileReader();
        reader.readAsDataURL(fileObject);
        
        $('#previewContainer').append(previewDom);
         
        return previewDom;
    }
    
    /**
     * 
     */
    var onFileChange = function(event) {
        
        HideErrorMsg();
        
        var existingLength = arrReportAbuseFiles.length;
   
        var validFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];
        
        //loop on files .. do basic checks like size, type
        $.each( this.files, function( key, file ) {
            
            if( ( file.size / 1048576 ).toFixed(1) > MAX_FILE_SIZE_IN_MB ) {
                ShowErrorMsg( file.name + ' You can attach a proof less than 6 MB in size' );
                return ;
            }
            
            if( validFileTypes.indexOf(file.type) == -1 ) {
                ShowErrorMsg( file.name + ' Invalid type of attachment' );
                return ;
            }
            
            if( arrReportAbuseFiles.length >= 5 ) {
                ShowErrorMsg( 'You can attach maximum 5 proofs' );
                setTimeout(function(){HideErrorMsg()},3000);
                return ;
            }
          
            arrReportAbuseFiles.push(file);
        });
        
        if(arrReportAbuseFiles.length == 0) {
            ShowErrorMsg( 'No valid attachments' );
            return ;
        }
        
        var iterator = 1;
        arrReportAbuseFiles.forEach( function (file) { 
            if(file.hasOwnProperty('preview') === false) {
                file.myId = iterator;
                createPhotoPreview(file);
            }
            file.preview = true;
            iterator++;
        });
    }
    
    dom.on('change',onFileChange);
    dom.trigger("click");
    
}


function uploadAttachment()
{   
   
    /**
     * 
     */
    var ShowErrorMsg = function(msg){
        $('.js-attachError').html(msg);  
        $('#reportAbuseList_1').mCustomScrollbar("scrollTo","bottom",{ scrollEasing:"easeOut"});
    }
    
    var HideErrorMsg = function() {
        $('.js-attachError').html("");  
    }
    var SendAjax = function(fileObject, temp_attachment_id) {
        var apiUrl = "/api/v1/faq/abuseAttachment";
        
        var formData = new FormData();
        formData.append("feed[attachment_1]", fileObject);
        
        if( ( ( typeof temp_attachment_id == "string" && temp_attachment_id.length ) || typeof temp_attachment_id == "number" ) &&
              isNaN( temp_attachment_id ) == false
                ) {
            formData.append("feed[attachment_id]", temp_attachment_id);
        }
        showCommonLoader()        
        var ajaxConfig = {
            url     : apiUrl,
            type  : 'POST',
            data    : formData,
            async   : true,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend:function(){
                bUploadAttachmentInProgress = true;
            },
              complete:function(){
                bUploadAttachmentInProgress = false;
            },
            success : function ( response ) {
                            hideCommonLoader();
                            if(response.responseStatusCode == 0) {
                               if(file.hasOwnProperty('error')) {
                                   delete file.error;
                                   delete fileObject.errorMsg;
                               }
                               arrReportAbuseFiles['tempAttachmentId'] = response.attachment_id;
                               fileObject.uploaded = true;
                            } else {
                                fileObject.error = true
                                fileObject.errorMsg = response.message;
                                ShowErrorMsg( response.message );                                
                            }
                        },
            error   :  function ( response ) {
                            hideCommonLoader();
                            ShowErrorMsg("Something went wrong. Please try again");
                        },
        };
        jQuery.myObj.ajax(ajaxConfig);
        return;
    }
    
    if(0 == arrReportAbuseFiles.length) {
        return true;
    }

    if(bUploadAttachmentInProgress == true) {
        setTimeout(function(){uploadAttachment()},20); return false;
    }
    var len = arrReportAbuseFiles.length ;
    for(var itr =0 ; itr < len; itr++) {
        file = arrReportAbuseFiles[itr];
        if( file.hasOwnProperty("uploaded") == false || file.uploaded == false  ) {
                if(( file.hasOwnProperty('error') && file.error == true )) {
                    setTimeout(function(){
                        ShowErrorMsg(file.errorMsg);
                        hideCommonLoader();
                    },0);
                    return false;
                 }
                var tempId = (typeof arrReportAbuseFiles['tempAttachmentId'] == "undefined") ? "" : arrReportAbuseFiles['tempAttachmentId'] ;
                SendAjax( file, tempId );
                setTimeout(function(){uploadAttachment()},20);return false;
            }
    }
    for(var itr =0 ; itr < len; itr++) {
        file = arrReportAbuseFiles[itr];
        if(file.hasOwnProperty("uploaded") == false || file.uploaded == false) {
            return false;
        }
    }
    if(false == bUploadingDone) {
        bUploadingDone = true;
        reportAbuse();
    }
    return true;
}