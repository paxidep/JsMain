<?php
include_once("./includes/bms_connect.php");

echo "Hiiiii";
$dbbms = getConnectionBms();
echo "Hiiiii";
echo("<HTML>");
echo("<body><table width=\"200\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
//echo("<tr><td valign=\"top\"><a href=\"http://192.168.2.220/bmsjs/bms_hits.php?popup=1&banner=$banner\" target='_blank'>");
echo("<tr><td valign=\"top\"><a href=\"http://ser4.jeevansathi.com/bmsjs/bms_hits.php?popup=1&banner=$banner\" target='_blank'>");
echo("<img src=\"$gif\" border=\"0\"></a></td></tr>");

$sql = "Update bms2.BANNERHEAP set BannerCount=(BannerCount+1) , BannerServed=(BannerServed+1) where BannerId='$banner'";
$res = mysql_query($sql) or logErrorBms("Error in impressions",$sql,"continue","YES");

echo("</table></BODY></HTML>");
?>

