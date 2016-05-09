<?php
/*****************************************************bms_display.php******************************************************/
/* 	
	*	Created By         :	Abhinav	Katiyar		
	*	Last Modified By   :	Abhinav Katiyar	
	*	Description        :	This file displays banners and logs the impressions
	*	Includes/Libraries :	./includes/bms_connect.php 
***************************************************************************************************************************/

include("includes/bms_display_include.php");

global $filewritestring;
global $mailer,$subzone,$hit , $flash99 , $searchcriteria,$showall;

$_LogosZone = 190;      //Logos Zone where Banner String is used instead of Zone string

if ($zonestr == '')	// in case the zone id passed is empty
{
	exit(0);
}

$dbbms = getConnectionBms(); // database connection 

/*****************************************************************
Function to get location details Based on IP address of visitor
input : No Input
output: Returns IP location
****************************************************************/

function getIPLocation()
{
	global $_SERVER,$dbbms;
	$ip = FetchClientIP(); // gives the IP Address of the machine from which the user is accessing the site
	$iparr = explode(".",$ip);

	$ipnum = (256*256*256)*$iparr[0] + (256*256)*$iparr[1] + 256*$iparr[2] + $iparr[3];
        $sql = "Select endIpNUM from GeoIP.Blocks  where endIpNum >= '$ipnum' ORDER BY endIpNUM LIMIT 1 ";
        if ($result = @mysql_query($sql))
	{
        	if ($myrow = mysql_fetch_array($result))
        	{
                	$endipnum = $myrow["endIpNUM"];
                	$sql1 = "Select locid from GeoIP.Blocks where endIpNUM = '$endipnum' AND startIpNum <= '$ipnum'";
                	if ($result1 = mysql_query($sql1)) 
			{
                		if ($myrow1 = mysql_fetch_array($result1))
                		{
                        		$locid = $myrow1["locid"];
                        		return $locid ;
                		}
			}
			else
			{
				logErrorBms("Error in getIpLocation",$sql1,"continue","YES");
				defaultbanner();// to show a transparent image in case of mysql error.
			}
        	}
	}
	else
	{
		logErrorBms("Error in getIpLocation",$sql,"continue","YES");
		defaultbanner(); // to show a transparent image in case of mysql error.
	}
}

/*****************************************************************************************
	*	FUNCTION	:	GETS CRITERIA VALUES OF THE USER IF HE IS LOGGED IN
	*	INPUT		:	data
	*	OUTPUT		:	array containing value of the user		  
*******************************************************************************************/
function get_user_value($data)
{
	if($data)
	{
		// query to find the details of the logged in user from JPROFILE table

	        $sql = "SELECT GENDER,COUNTRY_RES,CITY_RES,AGE FROM newjs.JPROFILE WHERE PROFILEID=".$data." ";
		if ($result = @mysql_query($sql))
		{
			$myrow = mysql_fetch_array($result);
			$user['GENDER'] = $myrow["GENDER"];
			$user['COUNTRY_RES'] = $myrow["COUNTRY_RES"];
			$user['CITY_RES'] = $myrow["CITY_RES"];
	        	$user['INCOME'] = $myrow["INCOME"];
			$user['AGE'] = $myrow["AGE"];
	        	return $user;
		}
		else
		{
			logErrorBms("Error in get_user_value",$sql,"continue","YES");
			defaultbanner(); // to show a transparent image in case of mysql error.
		}
	}
	else
	{
                $user['GENDER']="";
                $user['COUNTRY_RES']="";
                $user['CITY_RES']="";
                $user['INCOME']="";
		$user['AGE']="";
                return $user;
	}
}

 /*****************************************************************************************
   Parsing Function for conversion of criterias
   input : Region,Zone(s),criterias
   output: Calls Display function and returns the array containing final display string zone wise
 *****************************************************************************************/
function bannerDisplay_2($region,$zone,$data)
{
	global $critarr;
	$critarr["zone"] = $zone;

    	display($critarr);
}
  
 /**************************************************************************************************
  	This function is use to calculate age of user
	input  : date of birth
	output : age 
  **************************************************************************************************/ 
  
function getAgeByDate($dob)
{

	list($iYear,$iMonth,$iDay) = explode("-",$dob);

	$nMonth = date("m");
	$nDay = date("d");
	$nYear = date("Y");

	$baseyear = $nYear - $iYear-1 ;
	if ($iMonth < $nMonth OR ($iMonth == $nMonth AND $iDay <= $nDay))
	{
		// had birthday
		$baseyear++;
	}
	return $baseyear;
}

/*******************************************************************************************
	Function for Display of Banners
  	input : Array containing Criterias,Zone,Region
    	output: returns the array containing final display string zone wise
*******************************************************************************************/
function display($critarr)
{
    	global $filewritestring , $dbbms , $bannerstring , $critarr;
    	$zone = $critarr["zone"];

	// gets the zone details
	$sql = "Select SQL_CACHE * from bms2.ZONE where ZoneId ='$zone' and ZoneStatus='active'";

     	if ($result = @mysql_query($sql))
	{
		if ($myrow = mysql_fetch_array($result))
		{
			$currzone = $myrow["ZoneId"];
			$zones = $myrow["ZoneId"];
			$zonearr[$currzone]["maxbans"] = $myrow["ZoneMaxBans"];
			$zonearr[$currzone]["maxrot"] = $myrow["ZoneMaxBansInRot"];
			$zonearr[$currzone]["align"] = $myrow["ZoneAlignment"];
			$zonearr[$currzone]["width"] = $myrow["ZoneBanWidth"];
			$zonearr[$currzone]["height"] = $myrow["ZoneBanHeight"];
			$zonearr[$currzone]["ispop"] = $myrow["ZonePopup"];
			$zonearr[$currzone]["criterias"] = $myrow["ZoneCriterias"];
			$criterias = explode(",",$zonearr[$currzone]["criterias"]);

			//$zonecriterias = Array(); // to create an array of criteria of a particular zone
			$criteria_count = count($criterias);
			for ($i = 0;$i < $criteria_count;$i++)
			{
				if ($criterias[$i])
				if (is_array($zonecriterias))
				{
					if (!in_array($criterias[$i],$zonecriterias))
					{
							$cnt = count($zonecriterias);
							$zonecriterias[$cnt] = $criterias[$i];	
					}	
				}
				else 
				{
					$zonecriterias[0] = $criterias[$i];
				}
			}

			// query returned for selection of banners for a particaular zone
			$bannarr = createQuery($critarr,$zonecriterias,$zones);
			$zoneval = $zones;
			$zonearr[$zoneval]["banners"] = $bannerstring;
			if(count($bannarr) > 1)
			{
				$banner_list = $zonearr[$zoneval]["banners"];

				// query for fetching BannerCount in case more than one banner is being displayed in the same zone for a particaular criteria
				$sql_heap = "Select BannerId , BannerCount from bms2.BANNERHEAP where BannerId IN ($banner_list)";
				if($res_heap = mysql_query($sql_heap))
				{
					while($row=mysql_fetch_array($res_heap))
					{
						$bannerid = $row["BannerId"];
						$bannarr[$bannerid]["served"] = $row["BannerCount"];
					}
				}
				else
				{
					logErrorBms("Error in display",$sql_heap,"continue","YES");
					defaultbanner();
				}
			}
			// no banner running in this zone. cache the frame for 1 hour
			elseif(count($bannarr)==0)
			{
				header("Cache-Control: public");
	                        header("Expires: " . gmdate('D, d M Y H:i:s', time()+3600) . " GMT");
                        }
			
			// function for further filteration of the selected banners
			filterBanners($critarr,$zonecriterias,$zones,$bannarr,$zonearr);
		}
		// no banner defined for this zone or no zoneid passed. So, cache this frame for 1 hour
		else
		{
			header("Cache-Control: public");
			header("Expires: " . gmdate('D, d M Y H:i:s', time()+3600) . " GMT");
	                exit;
		}
	}
	else
	{
		logErrorBms("Error in display",$sql,"continue","YES");
		defaultbanner();
	}
}

  
/*******************************************************************************
	Function that finally displays the banners
  	input : List of filtered banners,array of details of all the banners,
		array of details of all the zones(required zones) and current zone
    	output: Returns the string for display in a zone
********************************************************************************/
function actual_display($finlist,$bannarr,$zonearr,$zone)
{
	global $_LogosZone,$_HITSFILE,$smarty,$othersrcp , $_SERVER , $_SITEURL; 
	global $mailer , $hit , $dbbms , $flash99;
	global $showall;

	$maxbans = $zonearr[$zone]["maxbans"];
	$align = $zonearr[$zone]["align"];
	$width = $zonearr[$zone]["width"];
	$height = $zonearr[$zone]["height"];
	$ispopup = $zonearr[$zone]["ispop"];
    
	//print_r($bannarr);
    	if($zone!=$_LogosZone)
    	{
    		if($ispopup!='Y')
    		{
			if ($showall == 1)
			{
				echo ("<Table border=0 cellpadding=0 cellspacing=0>");
                                                                                                                            
                                if($align=='H')
                                {
                                        echo ("<TR>");
                                }

			}
   			for($i=1;$i<=$maxbans;$i++)
   			{
   				$banner=$finlist[$zone][$i];
   				if($banner)
   				{	
					$isstat = $bannarr[$banner]["isstat"];
					$gif = $bannarr[$banner]["gif"];
					$class = $bannarr[$banner]["class"];
					if(strstr(substr($gif,-3),"htm"))
					{
						//to zip the file before sending it

						$zipIt = 0;
						if (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
							$zipIt = 1;
						if($zipIt)
							ob_start("ob_gzhandler");

						//end of it
					}
					
					if($align=='H') // in case of horizontal alignment of the banners
					{
						if ($mailer == 1 || $flash99 == 1) // for banner display in matchalert
                                                {
							if(!$hit)
							{
								logimpression($banner);
								if(strstr(substr($gif,-3),"gif"))
                                                                	header("Content-Type:image/gif");
                                                        	readfile("$gif");
							}
							else
							{
								$dt=Date('Y-m-d');
                                                                                                                            
								$sql="Update bms2.BANNERMIS set Clicks=Clicks+1 where BannerId='$banner' and Date='$dt'";
								mysql_query($sql,$dbbms) or logErrorBms("bms_hits.php:2: <br><!--$sql(".mysql_error($dbbms).")-->:".mysql_errno($dbbms),$sql,"continue","YES");
																						    
								if(mysql_affected_rows($dbbms)==0)
								{
									$sql="Insert into bms2.BANNERMIS (BannerId,Date,Clicks) values ('$banner','$dt','1')";
									mysql_query($sql,$dbbms) or logErrorBms("bms_hits.php:3: <br><!--$sql(".mysql_error($dbbms).")-->:".mysql_errno($dbbms),$sql,"continue","YES");
								}
								$sql="Select SQL_CACHE BannerUrl from bms2.BANNER where BannerId='$banner'";
								$result=mysql_query($sql,$dbbms) or logErrorBms("bms_hits.php:1: <br><!--$sql(".mysql_error($dbbms).")-->:".mysql_errno($dbbms),$sql,"continue","YES");
                                                                                                                            
                                                                                                                            
								if($myrow=mysql_fetch_array($result))
								{
									$url=$myrow["BannerUrl"];
									if($othersrcp && trim($othersrcp)!='')
									$url=preg_replace("/othersrcp=[^\&]*/","othersrcp=$othersrcp",$url);
									if($url) echo "</html><html><body><META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0;URL=$url\"></body></html>";
								}

							}
						}
						else
						{
							logimpression($banner);
							if ($showall == 1)
                                                        {
                                                                echo("<TD width=\"1\">");
                                                        }
							if($isstat != 'Y')	// if the banner is clickable
								echo("<a href=\"$_HITSFILE?banner=$banner\" target='_blank'>");
							if(strstr(substr($gif,-3),"htm")) // if banner is an html file
							{
								readfile("$gif");
								if ($showall == 1)
								{
									echo ("</TD><TD>&nbsp;</TD>");
								}
							}
							elseif(strstr(substr($gif,-3),"swf")) // if the banner is a flash file 
                                                        {
                                                                echo("<object><embed src=\"$gif\" width=$width height=$height></embed></object>");
								if ($showall == 1)
								{
									echo("</TD><TD>&nbsp;</TD>");
								}
                                                        }
							else
							{
								echo("<img src=\"$gif\" border=0\"></a>");
								if ($showall == 1)
									echo("</TD><TD>&nbsp;</TD>");
							}	
						}
					}
					elseif($align=='V') // in case of vertical alignment of banners
					{	
						if ($mailer == 1 || $flash99 == 1) // for banner display in matchalert
                                                {
							if(!$hit)
							{
								logimpression($banner);
								if(strstr(substr($gif,-3),"gif"))
                                                                	header("Content-Type:image/gif");
                                                        	readfile("$gif");
							}
							else
							{
								$dt=Date('Y-m-d');
                                                                                                                            
								$sql="Update bms2.BANNERMIS set Clicks=Clicks+1 where BannerId='$banner' and Date='$dt'";
								mysql_query($sql,$dbbms) or logErrorBms("bms_hits.php:2: <br><!--$sql(".mysql_error($dbbms).")-->:".mysql_errno($dbbms),$sql,"continue","YES");
																						    
								if(mysql_affected_rows($dbbms)==0)
								{
									$sql="Insert into bms2.BANNERMIS (BannerId,Date,Clicks) values ('$banner','$dt','1')";
									mysql_query($sql,$dbbms) or logErrorBms("bms_hits.php:3: <br><!--$sql(".mysql_error($dbbms).")-->:".mysql_errno($dbbms),$sql,"continue","YES");
								}
								$sql="Select SQL_CACHE BannerUrl from bms2.BANNER where BannerId='$banner'";
								$result=mysql_query($sql,$dbbms) or logErrorBms("bms_hits.php:1: <br><!--$sql(".mysql_error($dbbms).")-->:".mysql_errno($dbbms),$sql,"continue","YES");
                                                                                                                            
                                                                                                                            
								if($myrow=mysql_fetch_array($result))
								{
									$url=$myrow["BannerUrl"];
									if($othersrcp && trim($othersrcp)!='')
									$url=preg_replace("/othersrcp=[^\&]*/","othersrcp=$othersrcp",$url);
									if($url) echo "</table></html><html><META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0;URL=$url\"></html>";
								}

							}
                                                }
						else
						{
							logimpression($banner);
							if ($showall == 1)
								echo("<tr><TD>");
							if($isstat != 'Y')	// if the banner is clickable
								echo("<a href=\"$_HITSFILE?banner=$banner\" target='_blank'>");
							if(strstr(substr($gif,-3),"htm"))  // if the banner is a html file
							{
                                        			readfile($gif);
								if ($showall == 1)
                                                                         echo ("</TD></TR><TR><TD height=1></TD></TR>");
							}
							elseif(strstr(substr($gif,-3),"swf"))   // if the banner is a flash file
                                                	{
                                                		echo("<object><embed src=\"$gif\" width=$width height=$height></embed></object>");
								if ($showall == 1)
									echo ("</TD></TR><TR><TD height=1></TD></TR>");
                                                	}
							else
							{
								echo("<img src=\"$gif\" border=0\"></a>");
								if ($showall == 1)
									echo ("</TD></TR><TR><TD height=1></TD></TR>");

							}
						}
					}
				}
    			}
			if ($showall == 1)
			{
				if($align=='H')
                        	{
                                	echo("</tr>");
                        	}
				$showallstr.="</TABLE>";
			}

		}
    		else  // for pop up/popunder / banner in new window
    		{
    		  	for($i=1;$i<=$maxbans;$i++)
			{
				$banner = $finlist[$zone][$i];

				if ($banner)
				{
					$features=$bannarr[$banner]["features"];
					$class=$bannarr[$banner]["class"];
					$gif=$bannarr[$banner]["gif"];

					if(strstr(substr($gif,-3),"htm")) // if the popup / popunder banner is a html file
					{	
						$popstr = "$gif";
					}
					else
					{	
						// to create an html file out of .gif 
						$popstr = "$_SITEURL/bmsjs/jspopup.php?zone=$zone&banner=$banner&gif=$gif";
					}
					$echostr = $popstr."#".$features."#".$class;
					popupwin($echostr); // function for opening popup/popunder
				}
    			}
    		}
  	}
  	else
	{
		for($i=1;$i<=$maxbans;$i++)
   		{
   			$banner=$finlist[$zone][$i];
   			
   			if($banner)
   			{
				$isstat=$bannarr[$banner]["isstat"];
				$gif=$bannarr[$banner]["gif"];
				$class=$bannarr[$banner]["class"];
				$bannerstring=$bannarr[$banner]["string"];
				if(strstr(substr($gif,-3),"htm"))
				{
					//to zip the file before sending it
														    
					$zipIt = 0;
					if (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
						$zipIt = 1;
					if($zipIt)
						ob_start("ob_gzhandler");
					//end of it
				}

				if($isstat=='Y')
				{
					logimpression($banner);
					if(strstr(substr($gif,-3),"htm"))
						readfile("$gif");
					else
						echo("<img src=\"$gif\" border=0\"></a>");

  	   			}
  	   			else
  	   			{	logimpression($banner);
					echo("<a href=\"$_HITSFILE?banner=$banner\" target='_blank'>");
					if(strstr(substr($gif,-3),"htm"))
						readfile("$gif");
					else
						echo("<img src=\"$gif\" border=0\"></a>");

  	   			}
  	   			$smarty->assign($bannerstring,$echostr);
  	   			$echostr="";
   			}
   		}
   		$returnzones=$echostr;
  	}
  	return $returnzones;	
}
   
/*******************************************************************************************
	Function that creates query for extraction of banners from databases
   	  input : Criterias array,zone wise criterias allowed,zone listing for querying
   	  output : Final Query On Banners Table
*****************************************************************************************/
function createQuery($critarr,$zonecriterias,$zones)
{
	global $subzone , $dbbms , $data , $bannerstring , $critarr , $searchcriteria;
	global $showall;
	// query for selecting banners on the basis of logged in user's details

	if ($showall == 1)
		$sql="Select SQL_CACHE * from bms2.BANNER where ZoneId = '$zones' and BannerStatus='live'";
	else
		$sql="Select SQL_CACHE * from bms2.BANNER where ZoneId = '$zones' and BannerStatus='live' and BannerPriority='$subzone'";
	$res=mysql_query($sql) or logErrorBms("Error in getting banners",$sql,"continue","YES");

	$i =0;
	$locationcount = 0;
	$agecount = 0;
	$ipcount = 0;
	$incomecount =0;
	$gendercount = 0;
	$memcount =0;
	$mstatuscount = 0;
	$relcount =0;
	$educount =0;
	$occcount =0;
	$comcount =0;
	$propcitycount =0;
	$propinrcount =0;
	$proptypecount =0;
	$propcatcount = 0;

	while($row=mysql_fetch_array($res))
	{
		if($row["BannerDefault"] != 'Y')  // if the banners are not for for default criteria
		{	
			// creating any array of banners on 'location' criterion

			if($row["BannerLocation"] != '')
			{	
				$locationvalue[$locationcount]["country"] = $row["BannerCountry"];
				if ($row["BannerInCity"])
				{
					$locationvalue[$locationcount]["indiancity"]=$row["BannerInCity"];
				}
				$locationvalue[$locationcount]["bannerid"]=$row["BannerId"];
				$locationcount++;
			}
			// creating any array of banners on 'age' criterion
			if($row["BannerAgeMax"] != '-1' && $row["BannerAgeMin"] != '-1')
			{
				$agevalue[$banid]["minage"] = $row["BannerAgeMin"];
				$agevalue[$banid]["maxage"] = $row["BannerAgeMax"];
				$agevalue[$agecount]["bannerid"]=$row["BannerId"];
				$agecount++;
			}
	
			// creating any array of banners on 'ip' criterion
			if($row["BannerIP"] != '')
			{
				$ipvalue[$ipcount]["IP"] = $row["BannerIP"];
				$ipvalue[$ipcount]["bannerid"] = $row["BannerId"];
				$ipcount++;
			}

			// creating any array of banners on 'income' criterion
			if($row["BannerCTC"] != '')
			{
				$incomevalue[$incomecount]["income"] = $row["BannerCTC"];
				$incomevalue[$incomecount]["bannerid"] = $row["BannerId"];
				$incomecount++;
			}
			// creating any array of banners on 'membership' criterion
			if($row["BannerMEM"] != '')
                        {
                                $memvalue[$memcount]["mem"] = $row["BannerMEM"];
                                $memvalue[$memcount]["bannerid"] = $row["BannerId"];
                                $memcount++;
                        }
			// creating any array of banners on 'gender' criterion
			if($row["BannerGender"] != '')
			{
				$gendervalue[$gendercount]["gender"] = $row["BannerGender"];
				$gendervalue[$gendercount]["bannerid"] = $row["BannerId"];
				$gendercount++;
			}
			// creating an array of banners on 'marital status' criterion
			if($row["BannerMARITALSTATUS"] != '')
                        {
                               	$mstatusvalue[$mstatuscount]["mstatus"] = $row["BannerMARITALSTATUS"];
                                $mstatusvalue[$mstatuscount]["bannerid"] = $row["BannerId"];
                                $mstatuscount++;
                        }
			 // creating an array of banners on 'religion' criterion
			if($row["BannerREL"] != '')
                        {
                                $relvalue[$relcount]["rel"] = $row["BannerREL"];
                                $relvalue[$relcount]["bannerid"] = $row["BannerId"];
                                $relcount++;
                        }
			 // creating an array of banners on 'education' criterion
			if($row["BannerEDU"] != '')
                        {
                                $eduvalue[$educount]["edu"] = $row["BannerEDU"];
                                $eduvalue[$educount]["bannerid"] = $row["BannerId"];
                                $educount++;
                        }
			 // creating an array of banners on 'occupation' criterion
			if($row["BannerOCC"] != '')
                        {
                                $occvalue[$occcount]["occ"] = $row["BannerOCC"];
                                $occvalue[$occcount]["bannerid"] = $row["BannerId"];
                                $occcount++;
                        }
			 // creating an array of banners on 'community' criterion
			if($row["BannerCOM"] != '')
                        {
                                $comvalue[$comcount]["com"] = $row["BannerCOM"];
                                $comvalue[$comcount]["bannerid"] = $row["BannerId"];
                                $comcount++;
                        }
			 // creating an array of banners on '99 acres property city' criterion
			if($row["BannerPROPCITY"] != '')
                        {
                                $propcityvalue[$propcitycount]["propcity"] = $row["BannerPROPCITY"];
                                $propcityvalue[$propcitycount]["bannerid"] = $row["BannerId"];
                                $propcitycount++;
                        }
			// creating an array of banners on '99 acres property inr' criterion
			if($row["BannerPROPINR"] != '')
                        {
                               	$propinrvalue[$propinrcount]["propinr"] = $row["BannerPROPINR"];
				$propinrvalue[$propinrcount]["category"] = $row["BannerPROPCAT"];
                                $propinrvalue[$propinrcount]["bannerid"] = $row["BannerId"];
                                $propinrcount++;
                        }
			// creating an array of banners on '99 acres property type' criterion
			if($row["BannerPROPTYPE"] != '')
                        {
                                $proptypevalue[$proptypecount]["proptype"] = $row["BannerPROPTYPE"];
                                $proptypevalue[$proptypecount]["bannerid"] = $row["BannerId"];
                                $proptypecount++;
                        }
			// creating an array of banners on '99 acres property category' criterion
			if($row["BannerPROPCAT"] != '' && $row["BannerPROPINR"] == '')
                        {
                                $propcatvalue[$propcatcount]["propcat"] = $row["BannerPROPCAT"];
                                $propcatvalue[$propcatcount]["bannerid"] = $row["BannerId"];
                                $propcatcount++;
                        }
		}

		// array of banner features
		$banners[$i]["bannerid"]=$row["BannerId"];
		$banners[$i]["class"] = $row["BannerClass"];
		$banners[$i]["isstat"]=$row["BannerStatic"];
                $banners[$i]["priority"]=$row["BannerPriority"];
                $banners[$i]["gif"]=$row["BannerGif"];
                $banners[$i]["url"]=$row["BannerUrl"];
                $banners[$i]["default"]=$row["BannerDefault"];

		$banners[$i]["class"]=$row["BannerClass"];
		$banners[$i]["isstat"]=$row["BannerStatic"];
		$banners[$i]["priority"]=$row["BannerPriority"];
		$banners[$i]["weight"]=$row["BannerWeightage"];
		$banners[$i]["gif"]=$row["BannerGif"];
		$banners[$i]["url"]=$row["BannerUrl"];
		$banners[$i]["default"]=$row["BannerDefault"];
		$banners[$i]["string"]=$row["BannerString"];
		$banners[$i]["features"]=$row["BannerFeatures"];
		$banners[$i]["location"]=$row["BannerLocation"];
		$banners[$i]["country"]=$row["BannerCountry"];
		$banners[$i]["incity"]=$row["BannerInCity"];
		$banners[$i]["uscity"]=$row["BannerUsCity"];
		$banners[$i]["ip"]=$row["BannerIP"];
		$banners[$i]["ipcity"]=$row["BannerCity"];
		$banners[$i]["maxage"]=$row["BannerAgeMax"];
		$banners[$i]["minage"]=$row["BannerAgeMin"];
		$banners[$i]["gender"]=$row["BannerGender"];
		$banners[$i]["weight"]=$row["BannerWeightage"];
		$banners[$i]["ctc"]   =$row["BannerCTC"];
		$banners[$i]["mem"]   =$row["BannerMEM"];
		$banners[$i]["mstatus"]   =$row["BannerMARITALSTATUS"];
		$banners[$i]["rel"]	=$row["BannerREL"];
		$banners[$i]["edu"]     =$row["BannerEDU"];
		$banners[$i]["occ"]     =$row["BannerOCC"];
		$banners[$i]["com"]     =$row["BannerCOM"];
		$banners[$i]["propcity"] = $row["BannerPROPCITY"];
		$banners[$i]["proptype"] = $row["BannerPROPTYPE"];
		$banners[$i]["propinr"] = $row["BannerPROPINR"];
		$banners[$i]["propcat"] = $row["BannerPROPCAT"];
		$i++;
	}
	if (($searchcriteria && $data) || (!$searchcriteria && $data))
	{
		if (is_numeric($data))
		{
			if ($agecount >= 1)
			if ($sql_userdata)
				$sql_userdata.=" , AGE";
			else
				$sql_userdata.=" AGE";
			if ($locationcount >= 1)
        		if ($sql_userdata)
                		$sql_userdata.=" , COUNTRY_RES , CITY_RES";
        		else
                		 $sql_userdata.=" COUNTRY_RES , CITY_RES";
			if ($gendercount >= 1)
        		if ($sql_userdata)
                		$sql_userdata.=" , GENDER";
        		else
                		 $sql_userdata.=" GENDER";
			if ($incomecount >= 1)
        		if ($sql_userdata)
                		$sql_userdata.=" , INCOME";
        		else
                		 $sql_userdata.=" INCOME";
			if ($relcount >=1)
			if ($sql_userdata)
				$sql_userdata.=" , RELIGION";
                	else
                        	$sql_userdata.=" RELIGION";
			if ($educount >=1)
			if ($sql_userdata)
                        	$sql_userdata.=" , EDU_LEVEL_NEW";
                	else
                        	$sql_userdata.=" EDU_LEVEL_NEW";
			if ($occcount >=1)
			if ($sql_userdata)
                        	$sql_userdata.=" , OCCUPATION";
                	else
                        	$sql_userdata.=" OCCUPATION";
			if ($comcount >=1)
			if ($sql_userdata)
                        	$sql_userdata.=" , MTONGUE";
                	else
                        	$sql_userdata.=" MTONGUE";
			if ($mstatuscount >=1)
			if ($sql_userdata)
                        	$sql_userdata.=" , MSTATUS";
                	else
                        	$sql_userdata.=" MSTATUS";
			if ($memcount >= 1)
        		if ($sql_userdata)
                		$sql_userdata.=" , SUBSCRIPTION";
        		else
                		$sql_userdata.=" SUBSCRIPTION";
			if ($ipcount >= 1)
                		$critarr['ip'] = getIpLocation();

			$sql_user_data = "Select ".$sql_userdata." FROM newjs.JPROFILE where PROFILEID='$data'";
			if ($sql_userdata)
			{
				if ($result = @mysql_query($sql_user_data))
                		{
                        		$myrow = mysql_fetch_array($result);
					$critarr["gender"] = $myrow["GENDER"];
					$critarr["country"] = $myrow["COUNTRY_RES"];
					if ($myrow["COUNTRY_RES"] == '128')
					{
						$critarr["country"] = '127';
					}
					if ($myrow["COUNTRY_RES"] == '127')
					{
						$critarr["country"] = '128';
					}
					if ($myrow["SUBSCRIPTION"] != '')
					{
						$critarr["mem"] = 'F';//$myrow["SUBSCRIPTION"];
					}
					else
						$critarr["mem"] = 'R';

					$critarr["mstatus"]= $myrow["MSTATUS"];
					$critarr["city"] = $myrow["CITY_RES"];
					$critarr["ctc"] = $myrow["INCOME"];
					$critarr["age"] = $myrow["AGE"];
					$critarr["edu"] = $myrow["EDU_LEVEL_NEW"];
					$critarr["rel"] = $myrow["RELIGION"];
					$critarr["occ"] = $myrow["OCCUPATION"];
					$critarr["com"] = $myrow["MTONGUE"];
                		}
				else
                		{
                        		logErrorBms("Error in get_user_value",$sql,"continue","YES");
                        		defaultbanner(); // to show a transparent image in case of mysql error.
                		}
			}
		}
		else
		{
			$datastr = explode("|",$data);
			if ($datastr[1] != '' )                // for property category (Rent or Buy)
			{
                                $critarr["propcat"] = $datastr[1];
				/*if ($datastr[1] == 'R')
					echo $critarr["propcat"] = 'Rent';
				if ($datastr[1] == 'S')
					echo $critarr["propcat"] = 'Buy';*/
			}
			if ($datastr[2] != '' )                // for property city
                        	$critarr["propcity"] = $datastr[2];
			if ($datastr[3] != '' )                 // for property inr
				$critarr["propinr"] =  $datastr[3];
			if ($datastr[4] != '' )                 // for property type
				$critarr["proptype"] = $datastr[4];
			if ($datastr[5] != '' )			// for age
				$critarr["age"] = $datastr[5];
			if ($datastr[6] != '' )                 // for community
				$critarr["com"] = $datastr[6];
			if ($datastr[7] != '' )			// for country
				$critarr["country"] = $datastr[7];
			if ($datastr[8] != '')				// for city
				$critarr["city"] = $datastr[8];
			if ($datastr[9] != '' )                 // for education
				$critarr["edu"] = $datastr[9];
			if ($datastr[10] != '' )			// for gender
				$critarr["gender"] = $datastr[10];
			if ($datastr[11] != '' )			// for income
				$critarr["income"] = $datastr[11];	
			if ($datastr[12] != '' )			// for subscription
				$critarr["mem"] = $datastr[12];
			if ($datastr[13] != '' )                 // for marital status
				$critarr["mstatus"] = $datastr[13];
			if ($datastr[14] != '' )                 // for religion
				$critarr["rel"] = $datastr[14];
			if ($datastr[15] != '' )                 // for occupation
				$critarr["occ"] = $datastr[15];
		}
	}
	elseif(($searchcriteria && $data || ($searchcriteria && !$data)))
	{
		$searchstr = explode("|",$searchcriteria);
		if ($searchstr[1] != '' )                // for property category
		{
			$critarr["propcat"] = $searchstr[1];
			/*if ($searchstr[1] == 'R')
                        	$critarr["propcat"]  =  'Rent';
			elseif ($searchstr[1] == 'S')
				$critarr["propcat"]  =  'Buy';*/
		}
                if ($searchstr[2] != '' )                // for property city
                	$critarr["propcity"] =  $searchstr[2];
                if ($searchstr[3] != '' )                 // for property inr
                	$critarr["propinr"]  =  $searchstr[3];
                if ($searchstr[4] != '' )                 // for property type
                	$critarr["proptype"] =  $searchstr[4];
		//$critarr["proptype"] = $searchcriteria;
	}
	unset($sql_userdata);
	$j =0;

	if ($propcatcount >= 1)  // if banners are booked on 'property category' criterion
        {
                $j =0;
                for ($i = 0;$i < $propcatcount;$i++)
                {
                        if (strstr($critarr["propcat"],","))
                        {
                                //$searcharr = explode(",",$searchcriteria);
                                $searcharr = explode(",",$critarr["propcat"]);
                                for ($t = 0;$t < count($searcharr);$t++)
                                {
					if ($searcharr[$t] == 'S')
						$searcharr[$t] = 'Buy';
					elseif ($searcharr[$t] == 'R')
						$searcharr[$t] = 'Rent';
                                        if (strstr($propcatvalue[$i]["propcat"] ,$searcharr[$t]))
                                        {
                                                $bannerlist[$j] =  $propcatvalue[$i]["bannerid"];
                                        }
                                }
                        }
                        else
                        {
				if ($critarr["propcat"] == 'S')
					$critarr["propcat"] = 'Buy';
				elseif ($critarr["propcat"] == 'R')
					$critarr["propcat"] = 'Rent';

                                // to select banner based on user's income
                                if(strstr($propcatvalue[$i]["propcat"] ,$critarr["propcat"]))
                                        $bannerlist[$j] =  $propcatvalue[$i]["bannerid"];
                        }
                        $j++;
                }
                if ($bannerlist)
                        $propcatbanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($propcatcount);
        unset($bannerlist);
        unset($propcatvalue);

	if ($propcitycount >= 1)  // if banners are booked on 'property city' criterion
        {
                $j =0;
                for ($i = 0;$i < $propcitycount;$i++)
                {
			if (strstr($critarr["propcity"],","))
                        {
                                //$searcharr = explode(",",$searchcriteria);
                                $searcharr = explode(",",$critarr["propcity"]);
                                for ($t = 0;$t < count($searcharr);$t++)
                                {
                                        if (strstr($propcityvalue[$i]["propcity"] ,$searcharr[$t]))
                                        {
                                                $bannerlist[$j] =  $propcityvalue[$i]["bannerid"];
                                        }
                                }
                        }
			else
			{
                        	// to select banner based on user's income
                        	if(strstr($propcityvalue[$i]["propcity"] ,$critarr["propcity"]))
                                	$bannerlist[$j] =  $propcityvalue[$i]["bannerid"];
			}
                        $j++;
                }
                if ($bannerlist)
                	$propcitybanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($propcitycount);
        unset($bannerlist);
        unset($propcityvalue);

	if ($propinrcount >= 1)  // if banners are booked on 'membership' criterion
        {
                $j =0;
                for ($i = 0;$i < $propinrcount;$i++)
                {
			if (strstr($critarr["propinr"],","))
                        {
                                //$searcharr = explode(",",$searchcriteria);
                                $searcharr = explode(",",$critarr["propinr"]);
			
                                for ($t = 0;$t < count($searcharr);$t++)
                                {
					if ($critarr["propcat"] == 'S')
                                        	$critarr["propcat"] = 'Buy';
                               	 	elseif ($critarr["propcat"] == 'R')
                                        	$critarr["propcat"] = 'Rent';
                                        if ((strstr($propinrvalue[$i]["propinr"] ,$searcharr[$t]) && strstr($propinrvalue[$i]["category"],$critarr["propcat"])))
                                        {
                                                $bannerlist[$j] =  $propinrvalue[$i]["bannerid"];
                                        }
                                }
                        }
			else
			{
				if ($critarr["propcat"] == 'S')
                                        $critarr["propcat"] = 'Buy';
                                elseif ($critarr["propcat"] == 'R')
                                        $critarr["propcat"] = 'Rent';
                        	// to select banner based on user's income
                        	if(strstr($propinrvalue[$i]["propinr"] ,$critarr["propinr"]) && strstr($propinrvalue[$i]["category"],$critarr["propcat"]))
                                	$bannerlist[$j] =  $propinrvalue[$i]["bannerid"];
			}
                        $j++;
                }
		//print_r($propinrvalue);
                if ($bannerlist)
                	$propinrbanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($propinrcount);
        unset($bannerlist);
        unset($propinrvalue);
	$temp =0;
	if ($proptypecount >= 1)  // if banners are booked on 'membership' criterion
        {
                $j =0;
		
                for ($i = 0;$i < $proptypecount;$i++)
                {
                        // to select banner based on user's income
			//if (strstr($searchcriteria,","))
			if (strstr($critarr["proptype"],","))
			{
				//$searcharr = explode(",",$searchcriteria);
				$searcharr = explode(",",$critarr["proptype"]);
				for ($t = 0;$t < count($searcharr);$t++)
				{	
					if (strstr($proptypevalue[$i]["proptype"] ,$searcharr[$t]))
					{
						$bannerlist[$j] =  $proptypevalue[$i]["bannerid"];
					}
				}
			}
			else
			{
                        	if(strstr($proptypevalue[$i]["proptype"] ,$critarr["proptype"]))
                                	$bannerlist[$j] =  $proptypevalue[$i]["bannerid"];
			}
                        $j++;
                }
                if ($bannerlist)
                	$proptypebanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($proptypecount);
        unset($bannerlist);
        unset($proptypevalue);

	if($locationcount >= 1) // if banners have been booked on 'location' criterion (status live)
	{
		for ($i = 0;$i < $locationcount;$i++)
		{
			// to select banner according to user's country and city of residenece
			if (strstr($locationvalue[$i]["country"],$critarr["country"]))
			{
				if ($critarr["country"] != 51 )	// for non Indian country
				{
					$bannerlist[$j] = $locationvalue[$i]["bannerid"];
				}
				if ($critarr["country"] == 51 && $critarr["city"] !='') // India  with a city
				{
					if (strstr($locationvalue[$i]["indiancity"],$critarr["city"]))
					{
						$bannerlist[$j] = $locationvalue[$i]["bannerid"];
					}
				}
				if ($critarr["country"] == 51 && $critarr["city"] =='')  // India without  city
                                {
					if ($locationvalue[$i]["indiancity"] == '')
					{
						$bannerlist[$j] = $locationvalue[$i]["bannerid"];
					}
				}
				$j++;
			}
		}
		if(($bannerlist))
			$locationbanner = implode(',',$bannerlist);  // creating a string of banners for location criterion
	}
	unset($locationcount);
	unset($bannerlist);
	unset($locationvalue);
	unset($j);

	if ($ipcount >= 1) // if banners are booked on IP criterion
	{
		$j =0;
		for ($i = 0;$i < $ipcount;$i++)
		{
			// to select banner based on the ip address of user's machine
			if(strstr($ipvalue[$i]["IP"],$critarr['ip']))
				$bannerlist[$j] =  $ipvalue[$i]["bannerid"];
			$j++;
		}
		if ($bannerlist)
			$ipbanner = implode(',',$bannerlist); // creating a string of banners for ip criterion
	}
	unset($ipcount);
	unset($bannerlist);
	unset($ipvalue);

	if ($memcount >= 1)  // if banners are booked on 'membership' criterion
        {
                $j =0;
                for ($i = 0;$i < $memcount;$i++)
                {
                        // to select banner based on user's income
                        if(strstr($memvalue[$i]["mem"] ,$critarr["mem"]))
                        	$bannerlist[$j] =  $memvalue[$i]["bannerid"];
                        $j++;
                }
                if ($bannerlist)
                       $membanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($memcount);
        unset($bannerlist);
        unset($memvalue);

	if ($relcount >= 1)  // if banners are booked on 'religion' criterion
        {
                $j =0;
                for ($i = 0;$i < $relcount;$i++)
                {
                        // to select banner based on user's income
                        if(strstr($relvalue[$i]["rel"] ,$critarr["rel"]))
                                $bannerlist[$j] =  $relvalue[$i]["bannerid"];
                        $j++;
                }
                if ($bannerlist)
                       $relbanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($relcount);
        unset($bannerlist);
        unset($relvalue);

	if ($occcount >= 1)  // if banners are booked on 'occupation' criterion
        {
                $j =0;
                for ($i = 0;$i < $occcount;$i++)
                {
                        // to select banner based on user's occupation
                        if(strstr($occvalue[$i]["occ"] ,$critarr["occ"]))
                                $bannerlist[$j] =  $occvalue[$i]["bannerid"];
                        $j++;
                }
                if ($bannerlist)
                       $occbanner  = implode(',',$bannerlist);  // creating a string of banners for occupation criterion
        }
        unset($occcount);
        unset($bannerlist);
        unset($occvalue);

	if ($comcount >= 1)  // if banners are booked on 'community' criterion
        {
                $j =0;
                for ($i = 0;$i < $comcount;$i++)
                {
                        // to select banner based on user's community
                        if(strstr($comvalue[$i]["com"] ,$critarr["com"]))
                                $bannerlist[$j] =  $comvalue[$i]["bannerid"];
                        $j++;
                }
                if ($bannerlist)
                       $combanner  = implode(',',$bannerlist);  // creating a string of banners for community criterion
        }
        unset($comcount);
        unset($bannerlist);
        unset($comvalue);

	if ($educount >= 1)  // if banners are booked on 'education' criterion
        {
                $j =0;
                for ($i = 0;$i < $educount;$i++)
                {
                        // to select banner based on user's education
                        if(strstr($eduvalue[$i]["edu"] ,$critarr["edu"]))
                                $bannerlist[$j] =  $relvalue[$i]["bannerid"];
                        $j++;
                }
                if ($bannerlist)
                       $edubanner  = implode(',',$bannerlist);  // creating a string of banners for education criterion
        }
        unset($educount);
        unset($bannerlist);
        unset($eduvalue);

	if ($incomecount >= 1)  // if banners are booked on 'income' criterion
	{	
		$j =0;
		for ($i = 0;$i < $incomecount;$i++)
		{
			// to select banner based on user's income
			if(strstr($incomevalue[$i]["income"] ,$critarr["ctc"]))
				$bannerlist[$j] =  $incomevalue[$i]["bannerid"];
			$j++;
		}
		if ($bannerlist)
			$ctcbanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
	}
	unset($incomecount);
	unset($bannerlist);
	unset($incomevalue);

	if ($mstatuscount >= 1)  // if banners are booked on 'mstatus' criterion
        {
                $j =0;
                for ($i = 0;$i < $mstatuscount;$i++)
                {
                        // to select banner based on user's income 
                        if(strstr($mstatusvalue[$i]["mstatus"] ,$critarr["mstatus"]))
                                $bannerlist[$j] =  $mstatusvalue[$i]["bannerid"];
                        $j++;
                }
                if ($bannerlist)
                        $mstatusbanner  = implode(',',$bannerlist);  // creating a string of banners for income criterion
        }
        unset($mstatuscount);
        unset($bannerlist);
        unset($mstatusvalue);
	
	if ($agecount >= 1)	// if banners are booked on 'age' criterion
	{
		$j =0;
		for ($i = 0;$i < $agecount;$i++)
		{	
			// to select banner based on user's age
			if($agevalue[$i]["minage"] >= $critarr["age"] && $agevalue[$i]["maxage"] <= $critarr["age"])
				$bannerlist[$j] =  $agevalue[$i]["bannerid"];
			$j++;
		}
		if ($bannerlist)
			$agebanner = implode(',',$bannerlist);	// creating a string of banners for age criterion
	}
	unset($agecount);
	unset($bannerlist);
	unset($agevalue);

	if ($gendercount >= 1) 	// if banners are booked on 'gender' criterion
	{
		$j =0;
		for ($i = 0;$i < $gendercount;$i++)
		{	
			// to select banner based on user's gender
			if($gendervalue[$i]["gender"] == $critarr["gender"])
				$bannerlist[$j] =  $gendervalue[$i]["bannerid"];
			$j++;
		}
		if ($bannerlist)
			$genderbanner = implode(',',$bannerlist);     // creating a string of banners for gender criterion
	}
	unset($gendercount);
	unset($gendervalue);
	unset($bannerlist);
	// creating string of banners booked on any one of the zone criteria

	if ($locationbanner)
	if ($bannerstring)
		$bannerstring.=" , $locationbanner";
	else
		$bannerstring = $locationbanner; 
	if ($genderbanner)
	if ($bannerstring)
		$bannerstring.=" , $genderbanner";
	else
		$bannerstring = $genderbanner;
	if ($ipbanner)
        if ($bannerstring)
                $bannerstring.=" , $ipbanner";
        else
                $bannerstring = $ipbanner;
	if ($membanner)
        if ($bannerstring)
                $bannerstring.=" , $membanner";
        else
                $bannerstring = $membanner;
	if ($ctcbanner)
        if ($bannerstring)
                $bannerstring.=" , $ctcbanner";
        else
                $bannerstring = $ctcbanner;
	if ($mstatusbanner)
        if ($bannerstring)
                $bannerstring.=" , $mstatusbanner";
        else
                $bannerstring = $mstatusbanner;
	if ($relbanner)
        if ($bannerstring)
                $bannerstring.=" , $relbanner";
        else
                $bannerstring = $relbanner;
	if ($edubanner)
        if ($bannerstring)
                $bannerstring.=" , $edubanner";
        else
                $bannerstring = $edubanner;
	if ($occbanner)
        if ($bannerstring)
                $bannerstring.=" , $occbanner";
        else
                $bannerstring = $occbanner;
	if ($combanner)
        if ($bannerstring)
                $bannerstring.=" , $combanner";
        else
                $bannerstring = $combanner;
	if ($agebanner)
        if ($bannerstring)
                $bannerstring.=" , $agebanner";
        else
                $bannerstring = $agebanner;
	if ($propcatbanner)
        if ($bannerstring)
                $bannerstring.=" , $propcatbanner";
        else
                $bannerstring = $propcatbanner;
	if ($propcitybanner)
        if ($bannerstring)
                $bannerstring.=" , $propcitybanner";
        else
                $bannerstring = $propcitybanner;
	if ($propinrbanner)
        if ($bannerstring)
                $bannerstring.=" , $propinrbanner";
        else
                $bannerstring = $propinrbanner;
	if ($proptypebanner)
        if ($bannerstring)
                $bannerstring.=" , $proptypebanner";
        else
                $bannerstring = $proptypebanner;
	$count_banner = count($banners);

	for($i =0;$i < $count_banner;$i++)
	{
		// selecting default and banners booked on any criterion

		if(strstr($bannerstring,$banners[$i]["bannerid"]) || $banners[$i]["default"]=='Y') 
		{
			$banner = $banners[$i]["bannerid"];
			$bannerarr[$i]=$banner;

			$bannarr[$banner]["class"]=$banners[$i]["class"];
			$bannarr[$banner]["isstat"]=$banners[$i]["isstat"];
			$bannarr[$banner]["priority"]=$banners[$i]["priority"];
			$bannarr[$banner]["weight"]=$banners[$i]["weight"];
			$bannarr[$banner]["gif"]=$banners[$i]["gif"];
			$bannarr[$banner]["url"]=$banners[$i]["url"];
			$bannarr[$banner]["default"]=$banners[$i]["default"];
			$bannarr[$banner]["string"]= $banners[$i]["string"];
			$bannarr[$banner]["features"]=$banners[$i]["features"];
			$bannarr[$banner]["location"]=$banners[$i]["location"];
			$bannarr[$banner]["country"]=$banners[$i]["country"];
			$bannarr[$banner]["incity"]=$banners[$i]["incity"];
			$bannarr[$banner]["uscity"]=$banners[$i]["uscity"];
			$bannarr[$banner]["ip"]=$banners[$i]["ip"];
			$bannarr[$banner]["ipcity"]=$banners[$i]["ipcity"];
			$bannarr[$banner]["maxage"]=$banners[$i]["maxage"];
			$bannarr[$banner]["minage"]=$banners[$i]["minage"];
			$bannarr[$banner]["gender"]=$banners[$i]["gender"];
			$bannarr[$banner]["ctc"]   =$banners[$i]["ctc"];
			$bannarr[$banner]["mem"]   =$banners[$i]["mem"];
			$bannarr[$banner]["mstatus"]   =$banners[$i]["mstatus"];
			$bannarr[$banner]["edu"]   =$banners[$i]["edu"];
			$bannarr[$banner]["rel"]   =$banners[$i]["rel"];
			$bannarr[$banner]["occ"]   =$banners[$i]["occ"];
			$bannarr[$banner]["com"]   =$banners[$i]["com"];
			$bannarr[$banner]["propcity"]   =$banners[$i]["propcity"];
			$bannarr[$banner]["proptype"]   =$banners[$i]["proptype"];
			$bannarr[$banner]["propinr"]   =$banners[$i]["propinr"];
			$bannarr[$banner]["propcat"]   =$banners[$i]["propcat"];
		}
	}
	if ($bannerarr)
		$bannerstring = implode(',',$bannerarr);

	unset($bannerarr);
	unset($banners);
	return $bannarr;
} 
  
  
   /*********************************************************************************************
	Function for filtering banners to get the actual banners to display	
    	input : Criterias array,zone wise criterias allowed,zone listing for querying,banners details,zones details
   	output : Assigns the display string to zone string
   *********************************************************************************************/
function filterBanners($critarr,$zonecriterias,$zones,$bannarr,$zonearr)
{
	global $smarty,$filewritestring,$critarr;
	$zone = $zones;
	$banners=$zonearr[$zone]["banners"];
	$bannersinzone=explode(",",$banners);
	for($j=0;$j<count($bannersinzone);$j++)
	{
		$banner=$bannersinzone[$j];
		$zonecrit=$zonearr[$zone]["criterias"];
		$returnarray=filterOnCriteria($bannarr,$banner,$critarr,$zonecrit);
		if($returnarray["isselected"]=="True")
		{
			$bannarr[$banner]["dueto"]=$returnarray["criteria"];
			
			$pri=$bannarr[$banner]["priority"];
			if($banzonepriority[$zone][$pri]["banners"])
			{
				$banzonepriority[$zone][$pri]["banners"].=",".$banner;
			}
			else 
			{
				$banzonepriority[$zone][$pri]["banners"]=$banner;
			}
			
			if($bannarr[$banner]["default"]=="Y")
			{
				$banzonepriority[$zone][$pri]["defaultcount"]++;
			}
			else 
			{
				$banzonepriority[$zone][$pri]["notdefaultcount"]++;
			}
		}
	}
	// for selecting which banner is to be displayed in case more than one banner is running in a zone for same criteria based on the priority and weightage of the respective banners.

	$maxbans=$zonearr[$zone]["maxbans"];
	for($j=1;$j<=$maxbans;$j++)
	{
		if($banzonepriority[$zone][$j]["banners"])
		{
			$bannerarray=explode(",",$banzonepriority[$zone][$j]["banners"]);
			for($k=0;$k<count($bannerarray);$k++)
			{
				$banner=$bannerarray[$k];
				if($banzonepriority[$zone][$j]["notdefaultcount"]>0)
				{	
					if($bannarr[$banner]["default"]!='Y')
						$diff[$zone][$j][$banner]=$bannarr[$banner]["weight"]-$bannarr[$banner]["served"]/$bannarr[$banner]["weight"];
				}
				elseif($banzonepriority[$zone][$j]["defaultcount"]>0)
				{	
					if($bannarr[$banner]["default"]=='Y')
						$diff[$zone][$j][$banner]=$bannarr[$banner]["weight"]-$bannarr[$banner]["served"]/$bannarr[$banner]["weight"];
				}
			}
				
			$finbanner=array_search(max($diff[$zone][$j]),$diff[$zone][$j]);
			
			if($finlist[$zone][$j])
			{
				$finlist[$zone][$j].=",".$finbanner;
			}
			else 
			{
				$finlist[$zone][$j]=$finbanner;
			}
		}
	}
	if(is_array($finlist[$zone])) $bannerlist[$zone]=implode(",",$finlist[$zone]);
		
	$bannerlisting=explode(",",$bannerlist[$zone]);//print_r($bannerlisting);

	for($cnt=0;$cnt<count($bannerlisting);$cnt++)
	{
		$banner=$bannerlisting[$cnt];
		if($bannarr[$banner]["dueto"] && $bannarr[$banner]["default"]!="Y")
		{
				$filewritestring.=$banner."#".$bannarr[$banner]["dueto"]."\n";
		}
	}
	  
	$string="zonedisp".$zone;
	$returnzones[$string]=actual_display($finlist,$bannarr,$zonearr,$zone);
	$smarty->assign($string,$returnzones[$string]);
}	
   /***************************************************************************
	Function that logs impressions
	Input : Banners for which to log Impressions
   *****************************************************************************/
   
function logimpression($banlist)
{
	if($banlist != 0)
	{
   		$sql="Update bms2.BANNERHEAP set BannerCount=(BannerCount+1) where BannerId = '$banlist'";
    		mysql_query($sql) or logErrorBms("Error in logging impressions",$sql,"continue","YES");
	}  
}
   
  /*****************************************************************************************
        Function that opens a banner in a new window , creates pop up/popunder
        Input : String including url of gif alongwith the parameters (height , width etc) for pop up/popunder
   ****************************************************************************************/
function popupwin($echostr)
{
	echo("<html><head>");
	echo("<script language=\"javascript\">");
	echo("function pop(){");
	echo("if(document.bmsform.hiddenValuesFromBMS){");
	echo("var theURL=document.bmsform.hiddenValuesFromBMS.value;");
	echo("if(theURL.length>0){");
	echo("var str=theURL.split(\",\");");
	echo("for(var i=0;i<str.length;i++){");
	echo("if(str[i]!=\"\"){");
	echo("flist=str[i].split(\"#\");");
	echo("var respstr;");
	echo("if(flist[1]){respstr=\"ScreenX=\"+flist[1];}");
	echo("if(flist[2]){");
	echo("if(respstr){ respstr=respstr+\",ScreenY=\"+flist[2];}");
	echo("{respstr=\"ScreenY=\"+flist[2];} }");
	echo("if(flist[3]){ if(respstr) { respstr=respstr+\",left=\"+flist[3]; }");
	echo("else { respstr=\"left=\"+flist[3];} }");
	echo("if(flist[4]) {if(respstr){ respstr=respstr+\", height=\"+flist[4];} else { respstr=\"height=\"+flist[4];} }");
	echo("if(flist[5]) {if(respstr){ respstr=respstr+\",Width=\"+flist[5];}  else  { respstr=\"width=\"+flist[5];}}");
	echo("if(flist[6]=='PopUp'){ ow(flist[0],i,respstr);}else { owunder(flist[0],i,respstr);}}");
	echo(" } } } }");
	echo("</script></head>");
	echo("<body onload=\"pop();\">");
	echo("<script language=\"javascript\">");
	echo("function ow(theURL,winName,features){ window.open(theURL,winName,features); }");
	echo("function owunder(theURL,winName,features)
	      {  
			var win2; 
			win2=window.open(theURL,winName,features); 
			if(win2)
			{
				win2.blur(); 
				window.focus();
			}
		}");
	echo("</script>");
	echo("<form name=\"bmsform\"><input type=\"hidden\" name=\"hiddenValuesFromBMS\" value=\"$echostr\"></form>");
	echo("</body></html>");
   }
/*************************************************************************************
	Filtering banners based on various criterias(getting banners that exactly matches the criterias specified)
	Input : Banner Details,Banner,Criterias,Criterias for this zone
	Output : Whether the banner matches the criterias specified
************************************************************************************/
   
function filterOnCriteria($bannarr,$banner,$critarr,$zonecrit)
{	
	global $critarr;//print_r($bannarr);
	if($zonecrit && trim($zonecrit)!='')
	{
		$zonecritarr=explode(",",$zonecrit);//print_r($zonecritarr);
		if(in_array("IP",$zonecritarr))  // filtering banner on basis of IP address of the user
		{
			if($bannarr[$banner]["ip"] && trim($bannarr[$banner]["ip"])!='')
			{
				if($critarr["ip"] && trim($critarr["ip"])!='')
				{
					$temp = explode("#",$bannarr[$banner]["ip"]);
					$string = trim($temp[1]);
					$string = str_replace(" ","",$string);
					$strarr = explode(",",$string);

					if(in_array($critarr["ip"],$strarr))
					{
						$resfrom["ip"] = "True";
						$bancityarr = explode("#",$bannarr[$banner]["ipcity"]);
						$bancity = str_replace(" ","",$bancityarr[1]);
						$dueto["ip"] = $bancity;
						$valueip = $critarr["ip"];
					}
					else 
					{
						$returnArray["isselected"]="False";
						$returnArray["criteria"]="";
						return $returnArray;
					}
				}
				else 
				{
					$returnArray["isselected"]="False";
					$returnArray["criteria"]="";
					return $returnArray;
				}
			}
		}

		if(in_array("LOCATION",$zonecritarr)) // filtering on basis of country_res and city_res
		{
			if($bannarr[$banner]["location"] && trim($bannarr[$banner]["location"])!='')
			{
				if($critarr["country"] && trim($critarr["country"])!='')
				{
					$locationcity = 2;
					$country = $bannarr[$banner]["country"];
					$incity  = $bannarr[$banner]["incity"];
					$uscity  = $bannarr[$banner]["uscity"];
					$ctrystring=str_replace(" , ",",",$country);
					if (trim($incity)!='')
						$incitystring=str_replace(" , ",",",$incity);
														    
					if (trim($uscity)!='')
						$uscitystring=str_replace(" , ",",",$uscity);
					$citystring = $incitystring;
					if (trim($uscitystring)!='')
					$citystring.=",".$uscitystring;
														    
					$countryarr=explode(",",$ctrystring); // creating string for countries
					$cityarr=explode(",",$citystring);  // creating string for cities

					$locationcrit = in_array($critarr["country"],$countryarr);
					if (trim($critarr["city"])!= '' && count($countryarr)!= 0 && $citystring)
						$locationcity= in_array($critarr['city'],$cityarr);

					if ($locationcity != 2 )
					{
						if ($locationcrit == 1 && $locationcity == 1)
						{
							$resfrom["location"]="True";
							$dueto["location"]=$critarr["country"];
							if (trim($critarr["city"]) != '')
							{
								$dueto["location"]=$critarr["country"]." |X| ".$critarr["city"];
                                                                	$resfrom["city"]="True";
							}
						}
						else
						{      
							$returnArray["isselected"]="False";
							$returnArray["criteria"]="";
							return $returnArray;
						}
					}
					else
					{
						if ($locationcrit == 1)
						{
							$resfrom["location"]="True";
							$dueto["location"]=$critarr["country"];
						}
						else
						{
							$returnArray["isselected"]="False";
							$returnArray["criteria"]="";
							return $returnArray;
						}

					}
				}
				else
				{      
					$returnArray["isselected"]="False";
					$returnArray["criteria"]="";
					return $returnArray;
				}
			}
		}
				
		if(in_array("GENDER",$zonecritarr))  // filtering on basis of gender of user
		{
			if($bannarr[$banner]["gender"]!='')
			 {	
				if($critarr["gender"] && trim($critarr["gender"])!='')
				{
					if($critarr["gender"] == $bannarr[$banner]["gender"])
					{
						$resfrom["gender"]="True";
						$dueto["gender"]=$critarr["gender"];
					}
					else 
					{
						$returnArray["isselected"]="False";
						$returnArray["criteria"]="";
						return $returnArray;
					}
				}
				else 
				{
					$returnArray["isselected"]="False";
					$returnArray["criteria"]="";
					return $returnArray;
				}
			}
		}

		if(in_array("AGE",$zonecritarr))  // filtering on basis of age of user
		{
			if($bannarr[$banner]["minage"]>=0 && $bannarr[$banner]["maxage"]>=0)
			{
				if($critarr["age"] && trim($critarr["age"])!='')
				{
					if(($critarr["age"] >= $bannarr[$banner]["minage"]) && ($critarr["age"] <= $bannarr[$banner]["maxage"]))
					{
						$resfrom["age"]="True";
						$dueto["age"]=$critarr["age"];
					}
					else 
					{
						$returnArray["isselected"]="False";
						$returnArray["criteria"]="";
						return $returnArray;
					}
				}
				else 
				{
					$returnArray["isselected"]="False";
					$returnArray["criteria"]="";
					return $returnArray;
				}
			}
		}	
		if(in_array("INCOME",$zonecritarr))  // filtering on basis of income of user
		{
		       if($bannarr[$banner]["ctc"] && trim($bannarr[$banner]["ctc"])!='')
		       {
				if($critarr["ctc"] && trim($critarr["ctc"])!='')
				{
					$temp=explode("#",$bannarr[$banner]["ctc"]);
					$string=trim($temp[1]);
					$string=str_replace(" ","",$string);
					$strarr=explode(",",$string);
					if(in_array($critarr["ctc"],$strarr))
					{
						$resfrom["ctc"]="True";
						$dueto["ctc"]=$critarr["ctc"];
					}
					else 
					{
						$returnArray["isselected"]="False";
						$returnArray["criteria"]="";
						return $returnArray;
					}
				}
				else 
				{
					$returnArray["isselected"]="False";
					$returnArray["criteria"]="";
					return $returnArray;
				}
			}
		}
		if(in_array("SUBSCRIPTION",$zonecritarr))  // filtering on basis of income of user
                {	
                       if($bannarr[$banner]["mem"] && trim($bannarr[$banner]["mem"])!='')
                       {
                                if($critarr["mem"] && trim($critarr["mem"])!='')
                                {
                                        $temp=explode("#",$bannarr[$banner]["mem"]);
                                        $string=trim($temp[1]);
                                        $string=str_replace(" ","",$string);
                                        $strarr=explode(",",$string);

					if($critarr["mem"] == 'F')
                                        if(!in_array('R',$strarr))   // if member is not a free member
                                        {
                                                $resfrom["mem"]="True";
                                                $dueto["mem"]=$critarr["mem"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
					elseif($critarr["mem"] == 'R')
                                        if(in_array('R',$strarr))   // if member is not a free member
                                        {
                                                $resfrom["mem"]="True";
                                                $dueto["mem"]=$critarr["mem"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("MARITALSTATUS",$zonecritarr))  // filtering on basis of marital status of user
                {
                       if($bannarr[$banner]["mstatus"] && trim($bannarr[$banner]["mstatus"])!='')
                       {	
                                if($critarr["mstatus"] && trim($critarr["mstatus"])!='')
                                {
                                        $temp=explode("#",$bannarr[$banner]["mstatus"]);
                                        $string=trim($temp[1]);
                                        $string=str_replace(" ","",$string);
                                        $strarr=explode(",",$string); 
                                        if(in_array($critarr["mstatus"],$strarr))
                                        {
                                                $resfrom["mstatus"]="True";
                                                $dueto["mstatus"]=$critarr["mstatus"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("RELIGION",$zonecritarr))  // filtering on basis of reigion of user
                {
                       if($bannarr[$banner]["rel"] && trim($bannarr[$banner]["rel"])!='')
                       {
                                if($critarr["rel"] && trim($critarr["rel"])!='')
                                {
                                        $temp=explode("#",$bannarr[$banner]["rel"]);
                                        $string=trim($temp[1]);
                                        $string=str_replace(" ","",$string);
                                        $strarr=explode(",",$string);
                                        if(in_array($critarr["rel"],$strarr))
                                        {
                                                $resfrom["rel"]="True";
                                                $dueto["rel"]=$critarr["rel"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {	
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("EDUCATION",$zonecritarr))  // filtering on basis of education level of user
                {
                       if($bannarr[$banner]["edu"] && trim($bannarr[$banner]["edu"])!='')
                       {
                                if($critarr["edu"] && trim($critarr["edu"])!='')
                                {
                                        $temp=explode("#",$bannarr[$banner]["edu"]);
                                        $string=trim($temp[1]);
                                        $string=str_replace(" ","",$string);
                                        $strarr=explode(",",$string); //print_r($strarr);
                                        if(in_array($critarr["edu"],$strarr))
                                        {
                                                $resfrom["edu"]="True";
                                                $dueto["edu"]=$critarr["edu"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("OCCUPATION",$zonecritarr))  // filtering on basis of occupation of user
                {
                       if($bannarr[$banner]["occ"] && trim($bannarr[$banner]["occ"])!='')
                       {
                                if($critarr["occ"] && trim($critarr["occ"])!='')
                                {
                                        $temp=explode("#",$bannarr[$banner]["occ"]);
                                        $string=trim($temp[1]);
                                        $string=str_replace(" ","",$string);
                                        $strarr=explode(",",$string); //print_r()
                                        if(in_array($critarr["occ"],$strarr))
                                        {
                                                $resfrom["occ"]="True";
                                                $dueto["occ"]=$critarr["occ"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("MTONGUE",$zonecritarr))  // filtering on basis of marital status of user
                {
                       if($bannarr[$banner]["com"] && trim($bannarr[$banner]["com"])!='')
                       {
                                if($critarr["com"] && trim($critarr["com"])!='')
                                {
                                        $temp=explode("#",$bannarr[$banner]["com"]);
                                        $string=trim($temp[1]);
                                        $string=str_replace(" ","",$string);
                                        $strarr=explode(",",$string); //print_r()
                                        if(in_array($critarr["com"],$strarr))
                                        {
                                                $resfrom["com"]="True";
                                                $dueto["com"]=$critarr["com"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("PROPCITY",$zonecritarr))  // filtering on basis of marital status of user
                {
                       if($bannarr[$banner]["propcity"] && trim($bannarr[$banner]["propcity"])!='')
                       {
                                if($critarr["propcity"] && trim($critarr["propcity"])!='')
                                {
					$propcityres =0;
                                        if (strstr($critarr["propcity"],","))
                                        {
                                                $propcityarr = explode(",",$critarr["propcity"]);
                                                for ($t = 0;$t < count($propcityarr);$t++)
                                                {
                                                        if (strstr($bannarr[$banner]["propcity"] ,$propcityarr[$t]))
                                                        {
                                                                $propcityres = 1;
                                                        }
                                                }
                                                if ($propcityres == 1)
                                                {
                                                        $resfrom["propcity"]="True";
                                                        $dueto["propcity"]=$critarr["propcity"];
                                                }
                                                else
                                                {
                                                        $returnArray["isselected"]="False";
                                                        $returnArray["criteria"]="";
                                                        return $returnArray;
                                                }
                                        }
					else
					{
                                        	$temp=explode("#",$bannarr[$banner]["propcity"]);
                                        	$string=trim($temp[1]);
                                        	$string=str_replace(" ","",$string);
                                        	$strarr=explode(",",$string); //print_r($strarr);
                                        	if(in_array($critarr["propcity"],$strarr))
						{
							$resfrom["propcity"]="True";
							$dueto["propcity"]=$critarr["propcity"];
						}
						else
						{
							$returnArray["isselected"]="False";
							$returnArray["criteria"]="";
							return $returnArray;
						}
					}
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("PROPCAT",$zonecritarr))  // filtering on basis of gender of user
                {
                        if($bannarr[$banner]["propcat"]!='')
                        {
                                if($critarr["propcat"] && trim($critarr["propcat"])!='')
                                {
                                        if($critarr["propcat"] == $bannarr[$banner]["propcat"])
                                        {
                                                $resfrom["propcat"]="True";
                                                $dueto["propcat"]=$critarr["propcat"];
                                        }
                                        else
                                        {
                                                $returnArray["isselected"]="False";
                                                $returnArray["criteria"]="";
                                                return $returnArray;
                                        }
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("PROPINR",$zonecritarr))
		{
                       if($bannarr[$banner]["propinr"] && trim($bannarr[$banner]["propinr"])!='')
                       {
                                if($critarr["propinr"] && trim($critarr["propinr"])!='')
                                {
					$propinrres =0;
					if (strstr($critarr["propinr"],","))
					{
						$propinrarr = explode(",",$critarr["propinr"]);
						for ($t = 0;$t < count($propinrarr);$t++)
                                                {
                                                        if (strstr($bannarr[$banner]["propinr"] ,$propinrarr[$t]))
                                                        {
                                                                $propinrres = 1;
                                                        }
                                                }
                                                if ($propinrres == 1)
                                                {
                                                        $resfrom["propinr"]="True";
                                                        $dueto["propinr"]=$critarr["propinr"];
                                                }
                                                else
                                                {
                                                        $returnArray["isselected"]="False";
                                                        $returnArray["criteria"]="";
                                                        return $returnArray;
                                                }
					}
					else
					{
                                        	$temp=explode("#",$bannarr[$banner]["propinr"]);
                                        	$string=trim($temp[1]);
						$string=str_replace(" ","",$string);
						$strarr=explode(",",$string); //print_r()
						if(in_array($critarr["propinr"],$strarr))
						{
							$resfrom["propinr"]="True";
							$dueto["propinr"]=$critarr["propinr"];
						}
						else
						{
							$returnArray["isselected"]="False";
							$returnArray["criteria"]="";
							return $returnArray;
						}
					}
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if(in_array("PROPTYPE",$zonecritarr))  // filtering on basis of marital status of user
                {
                       if($bannarr[$banner]["proptype"] && trim($bannarr[$banner]["proptype"])!='')
                       {
                                if($critarr["proptype"] && trim($critarr["proptype"])!='')
                                {
					$proptyperes = 0;
					if (strstr($critarr["proptype"],","))
					{
                                		$proptypearr = explode(",",$critarr["proptype"]);//print_r($proptypearr);
                        			for ($t = 0;$t < count($proptypearr);$t++)
                        			{
                                			if (strstr($bannarr[$banner]["proptype"] ,$proptypearr[$t]))
                                			{
								$proptyperes = 1;
                                			}
						}
						if ($proptyperes == 1)
						{
							$resfrom["proptype"]="True";
                                                        $dueto["proptype"]=$critarr["proptype"];
						}
						else
                                        	{
                                                	$returnArray["isselected"]="False";
                                                	$returnArray["criteria"]="";
                                                	return $returnArray;
                                        	}
					}
					else
					{
                                        	$temp=explode("#",$bannarr[$banner]["proptype"]);
                                        	$string=trim($temp[1]);
                                        	$string=str_replace(" ","",$string);
                                        	$strarr=explode(",",$string); //print_r($strarr);
                                        	if(in_array($critarr["proptype"],$strarr))
                                        	{
                                                	$resfrom["proptype"]="True";
                                                	$dueto["proptype"]=$critarr["proptype"];
                                        	}
                                        	else
                                        	{
                                                	$returnArray["isselected"]="False";
                                                	$returnArray["criteria"]="";
                                                	return $returnArray;
                                        	}
					}
                                }
                                else
                                {
                                        $returnArray["isselected"]="False";
                                        $returnArray["criteria"]="";
                                        return $returnArray;
                                }
                        }
                }
		if($resfrom["propcity"]=="True")
                {
                        $savestring.=$dueto["propcity"]."#";
                }
                else
                {
                        $savestring.="##";
                }
		if($resfrom["propinr"]=="True")
                {
                        $savestring.=$dueto["propinr"]."#";
                }
                else
                {
                        $savestring.="##";
                }
		if($resfrom["proptype"]=="True")
                {
                        $savestring.=$dueto["proptype"]."#";
                }
                else
                {
                        $savestring.="##";
                }
		if($resfrom["ip"]=="True")
		{
			$savestring.=$dueto["ip"]."#";
			$savestring.=$valueip."#";
		}
		else 
		{
			$savestring.="##";
		}
		if($resfrom["location"]=="True")
		{
			$savestring.=$dueto["location"]."#";
		}
		else 
		{
			$savestring.="#";
		}
		if($resfrom["ctc"]=="True")
		{
			$savestring.=$dueto["ctc"]."#";	
		}
		else 
		{
			$savestring.="#";
		}
		if($resfrom["mem"]=="True")
                {
                        $savestring.=$dueto["mem"]."#";
                }
                else
                {
                        $savestring.="#";
                }
		if($resfrom["rel"]=="True")
                {
                        $savestring.=$dueto["rel"]."#";
                }
                else
                {
                        $savestring.="#";
                }
		if($resfrom["edu"]=="True")
                {
                        $savestring.=$dueto["edu"]."#";
                }
                else
                {
                        $savestring.="#";
                }
		if($resfrom["occ"]=="True")
                {
                        $savestring.=$dueto["occ"]."#";
                }
                else
                {
                        $savestring.="#";
                }
		if($resfrom["com"]=="True")
                {
                        $savestring.=$dueto["com"]."#";
                }
                else
                {
                        $savestring.="#";
                }
		if($resfrom["mstatus"]=="True")
                {
                        $savestring.=$dueto["mstatus"]."#";
                }
                else
                {
                        $savestring.="#";
                }
		if($resfrom["age"]=="True")
		{
			$savestring.=$dueto["age"]."#";			
		}
		else 
		{
			$savestring.="#";
		}
		if($resfrom["gender"]=="True")
		{
			$savestring.=$dueto["gender"]."#";			
		}
		else 
		{
			$savestring.="#";
		}
		$returnArray["isselected"]="True";  
		$returnArray["criteria"]=$savestring;   // string of bannerid alongwith criteria to be written to the file
		$returnArray["banner"]=$banner;
		return $returnArray;
	}
	else 
	{
		$returnArray["isselected"]="True";
		$returnArray["criteria"]="";
		return $returnArray;
		
	}
	
}


/********************************************************************
	Function that logs the criterias in a flat file
	Input : String to be logged
	Output : Writes into the file
*******************************************************************/	
function writetofile($filewritestring)
{
  	global $_LOGPATH;
  	$dt=date("Ymd");
  	if($filewritestring)
  	{
  		$filename="$_LOGPATH/bmsconditionaloutput".$dt.".txt";
  	
  		if($fp=@fopen($filename,"a"))
		{
  			@fwrite($fp,$filewritestring);
			fclose($fp);
		}
		else
			defaultbanner();
  	}
}
//echo "<!--";	
  bannerDisplay_2($regionstr,$zonestr,$data);
@mysql_close($dbbms);
//  writetofile($filewritestring);
//echo "-->";
?>
