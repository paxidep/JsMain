<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Market Page</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
</head>
<body>

<!--start:header-->
<div class="M-header posrel hgta">
  <div class="container wida"> 
    <!--start:logo-->
    <div class="pt34"> <img src="~sfConfig::get('app_img_url')`/images/sathi/M-logo1.png" alt="Jeevansathi.com"/> </div>
    <!--end:logo--> 
    <!--start:text-->
    <div class="pt131 f30 fs1 color1 pl28">
      <p>Send us your #MySathiMoments</p>
      <p>and win a holiday of a lifetime!</p>
    </div>
    <!--end:text--> 
  </div>
</div>
<!--start:header--> 

<!--start:content-->
<div class="container pad1 widb">
  <p class="fs2 f14 color2 txtc lh18">We are inviting you to send us your #MySathiMoments - A <strong>photograph/10 second video</strong> of those unique moments you've shared with the one you love. <strong>Dinner dates, Movie Nights, Long Drives, Exotic Travels</strong> - we'd love to see all those precious moments that define your world.</p>
  <p class="pt50 color3 f20 fs1 txtc widc maut"> The best entries stand a chance to win a <span class = "fsos2">holiday of a lifetime</span>, a <span class = "fsos2">photo-shoot opportunity</span> and other <span class = "fsos2">exciting prizes</span>. </p>
</div>
<!--end:content--> 

<!--start:section-->
<div class="bg1">
  <div class="container widb pad2 clearfix"> 
    
    <!--start:left-->
    <div class="fl widd">
      
      <p class="fsos2 f15 color4 pb10 pt60">To participate, enter the following details:</p>
      <div class="bg2 fullw shade1">
      
      <div class="pad3 fs2" id="submitform">
        <form id="submitform" action="/SathiForLife/?submitForm=1" method="POST" enctype="multipart/form-data">
          <div class="clearfix pb20"> 
            <!--start:name field-->
            <div class="fl widf">
              <label class="wid40p dispibl">Name</label>
              <span id="nameError" class="dn pt5 color6 fr f11 pr8"></span>
              <input class="wid90p" name="NAME" id="nameField" type="text"/>
            </div>
            <!--end:name field--> 
            <!--start:age field-->
            <div class="fr widf">
              <label class="wid40p dispibl">Age</label> 	
              <span id="ageError" class="dn pt5 color6 fr f11 pr8"></span>
              <input class="wid90p"  name="AGE" id="ageField" type="text"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
            </div>
            <!--end:age field--> 
          </div>
          <div class="clearfix pb20"> 
            <!--start:sathi name field-->
            <div class="fl widf">
              <label class="wid40p dispibl">#Sathi's Name</label>
              <span id="sathiNameError" class="dn pt5 color6 fr f11 pr8"></span>
              <input class="wid90p" name="PARTNER_NAME" id="sathiNameField" type="text"/>
            </div>
            <!--end:sathi name field--> 
            <!--start:JS IS field-->
            <div class="fr widf">
              <label>Jeevansathi Id (If registered Jeevansathi user)</label>
              <input class="wid90p" name="USERNAME" id="idField" type="text"/>
            </div>
            <!--end:JS ID field--> 
          </div>
          <div class="clearfix pb20"> 
            <!--start:phone no. field-->
            <div class="fl widf">
              <label class="wid40p dispibl">Phone No.</label>
              <span id="phoneError" class="dn pt5 color6 fr f11 pr8"></span>
              <input maxlength="10" class="wid90p" name="PHONE" id="phoneField" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
            </div>
            <!--end:phone no. field--> 
            <!--start:email field-->
            <div class="fr widf">
              <label class="wid40p dispibl">E-mail Id</label>
              <span id="emailError" class="dn pt5 color6 fr f11 pr8"></span>
              <input class="wid90p" name="EMAIL" id="emailField" type="text"/>
            </div>
            <!--end:email field--> 
          </div>
          <div class="pb20">
            <label class="wid50p dispibl">Hashtag that best describes you and your #Sathi</label>
            <span id="hashtagError" class="dn pt5 color6 fr f11 pr8"></span>
            <input class="wid95p" name="DESCRIPTION" id="hashtagField" type="text" placeholder="Example: #FoodieSathi"/>
          </div>
          <div class="pb30 clearfix">
          	<div class="fl widf">
              <div id="photoError" class="f11 color6 vishid">Please upload photo.</div>
            	<input name="PICTURE" class="dn" type="file" name="fileToUpload" id="fileToUpload" onchange="readURL(this);" accept="image/gif, image/jpeg"/>
                <div id="upBtn" class="upbtn txtc">Upload photo</div>
                <label class="mt10 f10 txtc">(Please upload a jpg/gif image of size less than 6MB)</label>
                
            </div>
            <div class="fr widf">
            	<label>Provide link of 10 second video (Optional)</label>
              <input class="wid90p" name="VIDEO_URL" id="videoUrl" type="text" placeholder="Upload video via Youtube or Vimeo and paste link here"/>
            
            </div>
          
          
          
          </div>
          <div class="pb20">
            <label>Your #MySathiMoment Story (Optional)</label>
            <input class="wid95p" name="SATHI_STORY" id="storyField" type="text"  maxlength="2000" placeholder="Less than 2000 characters"/>
          </div>
          <div class="clearfix pb50"> 
            <!--start:Twitter Handle (Optional) field-->
            <div class="fl widf">
              <label>Twitter Handle (Optional)</label>
              <input class="wid90p" name="TWITTER_HANDLE" id="twitterField" type="text"/>
            </div>
            <!--end:Twitter Handle (Optional) field--> 
            <!--start:Instagram Username (Optional) field-->
            <div class="fr widf">
              <label>Instagram Username (Optional)</label>
              <input class="wid90p" name="INSTA_USERNAME" id="instaField" type="text"/>
            </div>
            <!--end:Instagram Username (Optional) field--> 
          </div>
          <div class="widf">
          	<input type="submit" id="subbtn" class="subbtn txtc" value="Submit" />
          </div>
          <div class="fullw txtc f12 pt20 color2">If unable to submit, mail your entries to <a href="mailto:sathiforlife@gmail.com">sathiforlife@gmail.com</a></div>
        </form>
      </div>
    </div>
    </div>
    <!--start:left--> 
    <!--start:right-->
    <div class="fr wide"> 
    
    
        <div class="posrel txtc">
            <div class="posabs lquo2 pos1"></div>
            <div class="posabs rquo2 pos2"></div>
            
            <div class="f18 fs1 color4 pt10 pb10"><p>Everything you do becomes so</p><p> much more fun when you do</p><p> it with the one you love</p></div>
          
        </div>
        <div class="pt10">
            <div class="bg2 fullw shade1">
                <div class="pad7">
                 <ul class="imglist">
                        <li>
                            <img src="~sfConfig::get('app_img_url')`/images/sathi/DanceSathi.jpg" class="imgd"/>
                            <p class="f13 fsos2 color2 pad6">#DanceSathi</p>
                        </li>
                        <li>
                            <img src="~sfConfig::get('app_img_url')`/images/sathi/FoodieSathi.jpg" class="imgd"/>
                            <p class="f13 fsos2 color2 pad6">#FoodieSathi</p>
                        </li>
                        <li>
                            <img src="~sfConfig::get('app_img_url')`/images/sathi/NatureSathi.jpg" class="imgd"/>
                            <p class="f13 fsos2 color2 pad6">#NatureSathi</p>
                        </li>
                         <li>
                            <img src="~sfConfig::get('app_img_url')`/images/sathi/ArtySathi.jpg" class="imgd"/>
                            <p class="f13 fsos2 color2 pad6">#ArtySathi</p>
                        </li>
                       
                    
                    
                    </ul>
                
                </div>            
            </div>        
        </div>
    
    
    
    
    </div>
    <!--end:right--> 
  </div>
</div>
<!--end:section-->

<!--start:section-->
<div class="container widb pad1">
  <p class="color4 f15 pb20 fsos3">Winners from Season 1 of #SathiforLife</p>
  <div class="dispt">
    <div class="disptc bdr2 pr10">
      <div> <a href="https://www.youtube.com/watch?v=NBEvsHb3ZHA"><img src="~sfConfig::get('app_img_url')`/images/sathi/shruti-mohit.jpg"> </a></div>
      <p class="fsos3 f13 color2 pt12">Shruti and Mohit</p>
      <div class="clearfix fsos1 color2 f13 cls1 pt20 pb10">
        <div class="dispib lquo"></div>
        <div class="dispi">Imagine my happiness when Mohit actually said ki Shaadi ke baad Mamma hamare sath rahengi</div>
        <div class="dispib rquo"></div>
      </div>
    </div>
    <div class="disptc bdr2 pr10 pl10">
      <div> <a href="https://www.youtube.com/watch?v=AKaCg8M24ks"><img src="~sfConfig::get('app_img_url')`/images/sathi/Nitesh-Esha.jpg"></a> </div>
      <p class="fsos3 f13 color2 pt12">Nitesh and Esha</p>
      <div class="clearfix fsos1 color2 f13 cls1 pt20 pb10">
        <div class="dispib lquo"></div>
        <div class="dispi">Love is a lot closer than what we think it is</div>
        <div class="dispib rquo"></div>
      </div>
    </div>
    <div class="disptc bdr2 pr10 pl10">
      <div><a href="https://www.youtube.com/watch?v=6Sdy_hdudSc"> <img src="~sfConfig::get('app_img_url')`/images/sathi/Archika-Akshat.jpg"> </a></div>
      <p class="fsos3 f13 color2 pt12">Archika and Akshat</p>
      <div class="clearfix fsos1 color2 f13 cls1 pt20 pb10">
        <div class="dispib lquo"></div>
        <div class="dispi">I was scared that marriage would mean giving up my independence</div>
        <div class="dispib rquo"></div>
      </div>
    </div>
    <div class="disptc pl10">
      <div> <a href="https://www.youtube.com/watch?v=uSl3HtJURTM"><img src="~sfConfig::get('app_img_url')`/images/sathi/Nishit-Shakeel.jpg"> </a></div>
      <p class="fsos3 f13 color2 pt12">Nishit and Shakeel </p>
      <div class="clearfix fsos1 color2 f13 cls1 pt20 pb10">
        <div class="dispib lquo"></div>
        <div class="dispi">I wanted a partner who was quite different from me</div>
        <div class="dispib rquo"></div>
      </div>
    </div>
  </div>
</div>
<!--end:section--> 

<!--start:footer-->
<footer>
  <div class="bg3 pad1 f12">
    <div class="widg clearfix maut">
 
      <div class="fl bdr1" style="padding-right:20px">
 
       <a href="#" class="color5 f13 fsos2">* Terms and Conditions</a> 
 
      </div>
      <div class="fr color5 fsos2" style="padding-left:20px">
        <p>Got any questions?</p>
        <p>E-mail us at: <a class="color5" href="mailto:sathiforlife@gmail.com">sathiforlife@gmail.com</a></p>
 
      </div>
 
    </div>
  </div>
</footer>
<!--end:footer-->

</body>

</html>
