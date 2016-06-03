<!doctype html public "-//w3c//dtd html 4.0 transitional//en">

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>JeevanSathi</title>
  <link rel="stylesheet" href="jeevansathi.css" type="text/css">
  <link rel="stylesheet" href="../profile/images/styles.css" type="text/css">
 </head>
 ~include file="head.htm"`
 <br>
 <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width=100% cellspacing="1" cellpadding='0' ALIGN="CENTER" >
   <tr width=100% border=1>
    <td width="3%" class="formhead" height="23">&nbsp;</td>
    <td width="62%" class="formhead" height="23"><font><b>Welcome :~$username`</b></font></td>
    <td width="6%" class="formhead" align='RIGHT' height="23">
     <a href="logout.php?cid=~$cid`">
      Logout
     </a>
    </td>
    <td width="3%" class="formhead" height="23">
     &nbsp;
    </td>
   </tr>
  </table>
  <form enctype="multipart/form-data" action="uploadphoto.php" method="POST"> 
   <input type=hidden name="profileid" value="~$profileid`">
   <input type=hidden name="cid" value="~$cid`">
   <input type=hidden name="username" value="~$username`">
   <input type=hidden name="delete_main_photo" value="~$delete_main_photo`">
   <input type=hidden name="delete_album_photo1" value="~$delete_album_photo1`">
   <input type=hidden name="delete_album_photo2" value="~$delete_album_photo2`">
   <input type=hidden name="count_photos" value="~$count_photos`"> 
   <table width=100% align="CENTER" >
	<tr>
		<td>
			~if $no_reason`
			<font color="red">
				You have to specify the reason !
			</font>
			~else`
				Select the reason why you are deleting the photo
			~/if`
			~if $delete_main_photo`
				<br><br>Main Photo<br><br>			
				<input type="checkbox" name="main_photo_reason[] " value="1">The photo is not clear		
				<br><br>
				<input type= "checkbox" name="main_photo_reason[]" value="2">The photo is a group photo
				<br><br>				
				<input type= "checkbox" name="main_photo_reason[]" value="3">The photo is of a well known personality
				<br><br>				
				<input type= "checkbox" name="main_photo_reason[]" value="4">The photo is not proper
				<br><br>				
				&nbsp;Other reasons <input type="text" name="main_photo_reason_other">					
				<br><br>
				<font color="red">Deleting this photo will delete all other photos as well</font>				
			~else`
				~if $delete_album_photo1`
					<br><br>Album Photo 1<br><br>			
					<input type= "checkbox" name="album_photo1_reason[]" value="1">The photo is not clear		
					<br><br>
					<input type= "checkbox" name="album_photo1_reason[]" value="2">The photo is a group photo
					<br><br>				
					<input type= "checkbox" name="album_photo1_reason[]" value="3">The photo is of a well known personality
					<br><br>
					<input type= "checkbox" name="album_photo1_reason[]" value="4">The photo is not proper
					<br><br>				
					&nbsp;Other reasons <input type="text" name="album_photo1_reason_other" value="">
				~/if`	
				~if $delete_album_photo2`
					<br><br>Album Photo 1<br><br>			
					<input type= "checkbox" name="album_photo2_reason[]" value="1">The photo is not clear		
					<br><br>
					<input type= "checkbox" name="album_photo2_reason[]" value="2">The photo is a group photo
					<br><br>				
					<input type= "checkbox" name="album_photo2_reason[]" value="3">The photo is of a well known personality
					<br><br>
					<input type= "checkbox" name="album_photo2_reason[]" value="4">The photo is not proper
					<br><br>				
					&nbsp;Other reasons <input type="text" name="album_photo2_reason_other">		
				~/if`
			~/if`			
			<br><br>
		</td>
	</tr>
	<tr>
		<td>
			Are you sure you want to delete ? 
		</td>
	</tr>
	<tr>
		<td>&nbsp; Send Email : <input type=checkbox name="confirm_sendemail" value="Y"></td>
	</tr>
   </table>
	<br>	   
   <div align="center">
	<input type="submit" name="confirm_delete" value="Yes">   
    <input type="submit" name="cancel_delete" value="No"> 
   </div>
  </form>
  <br><br><br><br><br><br><br>
  ~include file="foot.htm"`
 </body>
</html>
