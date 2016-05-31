      <!--start:div two-->
      <div class="pup2">
        <!--start:boxes-->

		<form  id="submitForm" action="" method="post" enctype="multipart/form-data">
			<input type="file" name="photo" id="file" accept="image/*" style="width:0px;height:0px;position:absolute;" required multiple/>
			<input type="hidden" name="uploadSource" value="desktopGallery" style="display:none;" required />
			<input type="hidden" name="perform" value="mobUploadPhoto" style="display:none;" required />
		</form>

        <ul class="hor_list clearfix pulist1" id="photoFolder">
           <li class="addPhoto cursp">
            <div class="disp-tbl txtc pos-rel" id="addMoreButton">
              <div class="disp-cell vmid pudim2 pubg6 pubdr1 fontlig f80 pucolor1">+</div>
            </div>
          </li>
        </ul>
        <!--end:boxes-->
      </div>
      <!--end:div two-->
<div id="userPhoto" style="display:none;">
          <li>
            <div class="disp-tbl txtc pos-rel imagePreview opa50 uploading{{COUNT}}" style = "background-size:100% 100%;background-image: url({{PHOTO_URL}});">
               <i class="cursp sprite2 pos-abs photocross deletePhoto dp{{COUNT}}" id="deletePhoto{{COUNT}}" data-pictureid="{{PICTUREID}}" style="display:none;"></i>
              <div class="disp-cell vmid ">
		<span id="{{PHOTO_ID}}" ><img  class="vtop pudim1"  oncontextmenu="return false;" galleryimg="NO" src="/profile/ser4_images/transparent_img.gif"></span>
		</div>
		<div class="pos_abs fullwid stillUploading uploadingBar{{COUNT}}">
			<div class="puwid9 brdr12 pum3">
				<div class="bg_pink mrl0 uploadingPercent{{COUNT}}" style="width:0px;">
				</div>
			</div>
		</div>
            </div>
          </li>
</div>
<div id="error" style="display:none;">
	<li class="errorBox{{COUNT}}">
            <div class="disp-tbl txtc pos-rel">
               <i class="sprite2 pos-abs photocross pc{{COUNT}} cursp js-del"></i>
              <div class="disp-cell vmid pudim2 pubg6 pubdr1 fontlig pucolor1">
                <i class="sprite2 puic13"></i>
                <div class="f13 fontlig color11 padall-10">{{ERRORTEXT}}</div>
                <p class="f14 fontlig color5 cursp js-addPhotoOnError" style="display:none;">SELECT PHOTO</p>

              </div>
            </div>
	</li>
</div>
<div id="addPhoto" style="display:none;">
           <li class="addPhotoBlankDivs">
            <div class="disp-tbl txtc pos-rel">
              <div class="disp-cell vmid pudim2 pubdr1 fontlig f80 pucolor2">+</div>
            </div>
          </li>
</div>
<div id="previewText" style="display:none;">
<li id="previewTxt">
	<div class="disp-tbl txtc pos-rel">
		<div class="disp-cell vmid pudim2 pubdr1 f80" style="color:#d9475c;font-size:14px;text-align:center;font-family:"Roboto Light, Arial, sans-serif, Helvetica Neue",Helvetica;"><br><br><center>Generating<br>Preview....</center>
		</div>
	</div>
</li>
</div>