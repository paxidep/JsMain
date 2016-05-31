<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	include_once($path."/profile/connect.inc");
	include_once($path."/profile/screening_functions.php");
	include_once($path."/profile/cuafunction.php");
	include_once($path."/profile/registration_functions.inc");
	$db = connect_db();
	if($_POST)
	{
		$ajaxValidation = $_POST['ajaxValidation'];
		$forgot_password = $_POST['forgot_password'];
		$autosuggest = $_POST['autosuggest'];
		$check_contact_number = $_POST['check_contact_number'];
		$caste_mapping = $_POST['caste_mapping'];

		if($ajaxValidation)
		{
			$email = $_POST['email'];
			$username = $_POST['username'];
			$invalid = 0;
			$already_exist = 0;
			$status = 0;

			$xml = new DomDocument;
			loadMyXML($path."/profile/registration_pg1_eng.xml");
			$registration_pg1 = $xml->getElementsByTagName("registrationPage1")->item(0);
			$messages = $registration_pg1->getElementsByTagName("submitErrorMessages")->item(0);

			if($email)
			{
				$email_messages = $messages->getElementsByTagName("email")->item(0);
				if($_COOKIE['OPERATOR']=="")
				{
					$email_flag = checkemail($email);
					$old_email_flag = checkoldemail($email);
					$af_email_flag = checkemail_af($email);

					if($email_flag == 1 || $af_email_flag == 1)
						$msg = $email_messages->getElementsByTagName("invalid")->item(0)->nodeValue;
					elseif($email_flag == 2 || $old_email_flag == 2 || $af_email_flag == 2)
					{
						$msg = $email_messages->getElementsByTagName("alreadyExists")->item(0)->nodeValue;
						$activated = get_profile_active_status($email);
						if($activated == "D")
							$msg .= "<a onclick=\"javascript:{tb_show('','$SITE_URL/profile/faqs_layer.php?width=500&questiontext=Retrieve%20username/password&retrieve_profile=1&email=$email','');return false;}\" href=\"".$SITE_URL."/profile/faqs_layer.php?width=500&questiontext=\'+'Retrieve%20username/password'&retrieve_profile=1&email=$email\" name=\"retrieve_profile_link\"  id=\"retrieve_profile_link\" >";
						else
							$msg .= "<a href=\"\" name=\"forgot_password_link\" id=\"forgot_password_link\">";

						$msg .= $email_messages->getElementsByTagName("clickHere")->item(0)->nodeValue;
						$msg .= "</a>.#LINK";
					}
					elseif($email_flag == 3 || $old_email_flag == 3)
						$msg = $email_messages->getElementsByTagName("jeevansathiInvalid")->item(0)->nodeValue;
					elseif($email_flag == 4)
						$msg = $email_messages->getElementsByTagName("blockedDomainName")->item(0)->nodeValue;
					else
					{
						$msg = $email_messages->getElementsByTagName("perfect")->item(0)->nodeValue;
						$msg .= "#OK";
					}
				}
				else
				{
					$msg = $email_messages->getElementsByTagName("perfect")->item(0)->nodeValue;
					$msg .= "#OK";
				}

			}
			if($username)
			{
				$username_messages = $messages->getElementsByTagName("username")->item(0);
				$username_flag = validate_username($username,$email);
				if($username_flag == 1)
					$msg = $username_messages->getElementsByTagName("characterStarting")->item(0)->nodeValue;
				elseif($username_flag == 2)
					$msg = $username_messages->getElementsByTagName("minimumCharacters")->item(0)->nodeValue;
				elseif($username_flag == 3)
					$msg = $username_messages->getElementsByTagName("obscene")->item(0)->nodeValue;
				elseif($username_flag == 4)
					$msg = $username_messages->getElementsByTagName("continuousNumerics")->item(0)->nodeValue;
				elseif($username_flag == 5)
					$msg = $username_messages->getElementsByTagName("domainNameUsage")->item(0)->nodeValue;
				elseif($username_flag == 6)
					$msg = $username_messages->getElementsByTagName("specialCharacters")->item(0)->nodeValue;
				elseif($username_flag == 7)
					$msg = $username_messages->getElementsByTagName("alreadyExists")->item(0)->nodeValue;
				elseif($username_flag == 8)
					$msg = $username_messages->getElementsByTagName("sameAsEmail")->item(0)->nodeValue;
				else
				{
					$msg = $username_messages->getElementsByTagName("perfect")->item(0)->nodeValue;
					$msg .= "#OK";
				}
			}
			echo $msg;
			exit;
		}
		elseif($forgot_password)
		{
			$record_found = 0;
			$email = $_POST['to_send_email'];
			$sql = "SELECT PROFILEID FROM newjs.OLDEMAIL WHERE OLD_EMAIL = '$email'";
			$res = mysql_query_decide($sql) or logError("error",$sql);
			if($row = mysql_fetch_array($res))
				$profileid = $row['PROFILEID'];

			if($profileid)
				$sql = "SELECT USERNAME,PASSWORD,EMAIL,ACTIVATED FROM newjs.JPROFILE WHERE PROFILEID='$profileid'";
			else
				$sql = "SELECT USERNAME,PASSWORD,EMAIL,ACTIVATED FROM newjs.JPROFILE WHERE EMAIL='$email'";
			$res = mysql_query_decide($sql) or logError("error",$sql);
			if($row = mysql_fetch_array($res))
			{
				$record_found = 1;
				$user = ereg_replace(" ","&nbsp;",htmlspecialchars($row["USERNAME"]));
				$pass = ereg_replace(" ","&nbsp;",htmlspecialchars($row["PASSWORD"]));
				$email = $row["EMAIL"];
			}
			else
			{
				if($profileid)
					$sql = "SELECT USERNAME,PASSWORD,EMAIL,ACTIVATED FROM newjs.JPROFILE_AFFILIATE WHERE PROFILEID='$profileid'";
				else
					$sql = "SELECT USERNAME,PASSWORD,EMAIL,ACTIVATED FROM newjs.JPROFILE_AFFILIATE WHERE EMAIL='$email'";
				if($row = mysql_fetch_array($res))
				{
					$record_found = 1;
					$user = ereg_replace(" ","&nbsp;",htmlspecialchars($row["USERNAME"]));
					$pass = ereg_replace(" ","&nbsp;",htmlspecialchars($row["PASSWORD"]));
					$email = $row["EMAIL"];
				}
			}

			if($user && $pass && $email)
			{
				$announce_from="webmaster@jeevansathi.com";
				$announce_subject="Jeevansathi Password Mail";
				$MP = "/usr/sbin/sendmail -t";
				$MP .= " -f $announce_from";
				$fd = popen($MP,"w"); //write the mail from here

				fputs($fd, "To: $email \n");
				fputs($fd, "From: $announce_from \n");
				fputs($fd, "Subject: $announce_subject \n");
				fputs($fd, "X-Mailer: PHP3\n");
				fputs($fd, "Content-type: text/html; charset=us-ascii \n");
				fputs($fd, "Content-Transfer-Encoding: 7bit \n");
				fputs($fd, "\n\n");

				$smarty->assign ("username",$user);
				$smarty->assign ("password",$pass);
				$smarty->assign ("HEAD_MAILER",$smarty->fetch("head_mailer.htm"));
				$smarty->assign ("SUBFOOTER_MAILER",$smarty->fetch("subfooter_mailer.htm"));
				$smarty->assign("FOOT",$smarty->fetch("foot.htm"));

				$output = $smarty->fetch("password_mail.htm");

				fputs($fd, "$output \n\n");
				pclose($fd);

				$xml = new DomDocument;
				loadMyXML($path."/profile/registration_pg1_eng.xml");
				$registration_pg1 = $xml->getElementsByTagName("registrationPage1")->item(0);
				$messages = $registration_pg1->getElementsByTagName("submitErrorMessages")->item(0);
				$forgot_password_message = $messages->getElementsByTagName("forgotPasswordMessage")->item(0)->nodeValue;
				echo $forgot_password_message;
			}
			exit;
                }
		elseif($autosuggest)
		{
			$subcaste = $_POST['subcaste'];
			$gotra = $_POST['gotra'];
			$diocese = $_POST['diocese'];

			if($subcaste)
			{
			/*	$path = $_SERVER['DOCUMENT_ROOT']."/profile/suggestalgo_subcaste.php $subcaste";
				$cmd = "php -q ".$path;
				$a = passthru($cmd);
				echo $a;*/
			}
			elseif($gotra)
			{
				$path = $_SERVER['DOCUMENT_ROOT']."/profile/suggestalgo.php $gotra";
				$cmd = "/usr/local/php/bin/php -q ".$path;
				$a = passthru($cmd);
				echo $a;
			}
			elseif($diocese)
			{
				$path = $_SERVER['DOCUMENT_ROOT']."/profile/suggestalgo_diocese.php $diocese";
				$cmd = "/usr/local/php/bin/php -q ".$path;
				$a = passthru($cmd);
				echo $a;
			}
			exit;
		}
		elseif($check_contact_number)
		{
			$for_which_number = $_POST['for_which_number'];
			$value = $_POST['value'];

			if($for_which_number == "PHONE")
				$where_field = "PHONE_RES";
			elseif($for_which_number == "MOBILE")
				$where_field = "PHONE_MOB";

 			$sql = "SELECT COUNT(*) AS COUNT FROM newjs.JPROFILE WHERE $where_field = '$value'";
			$res = mysql_query_decide($sql) or die(mysql_error()) or logError("error",$sql);
			$row = mysql_fetch_array($res);
			if($row['COUNT'] > 0)
			{
				if($where_field == "PHONE_MOB")
					echo "SHOW_LOGIN_MOB";
				elseif($where_field == "PHONE_RES")
					echo "SHOW_LOGIN_PHONE";
				
				// Mailer Intergrated for the duplicate contact number holders.

				$sql = "SELECT PROFILEID FROM newjs.JPROFILE WHERE $where_field LIKE ('$value')";
				$res = mysql_query_decide($sql) or die(mysql_error()) or logError("error",$sql);
				while($row = mysql_fetch_array($res))
				{
					$oldprofile[] = $row['PROFILEID'];
				}
				$oldprofile=implode(",",$oldprofile);

				if($where_field == "PHONE_RES")
					$contact = "Phone";
				elseif($where_field == "PHONE_MOB")
					$contact = "Mobile";

				$msg = "Hi,</br>User Name <b>$username</b> has registered with the $contact number $value.</br>We already have profile $oldprofile with the same number";
				$cc ="productsupport@jeevansathi.com";
				$to ="mahesh@infoedge.com";
				$from = "info@jeevansathi.com";
				$subject = "Duplicate $contact Number Tracked";
				send_email($to,$msg,$subject,$from,$cc);
			}
			
			exit;
		}
		elseif($caste_mapping)
		{
			$mtongue = $_POST['mtongue'];
			$caste = $_POST['caste'];
			$religion = $_POST['religion'];

			$sql = "SELECT SQL_CACHE PARENT, LABEL, VALUE FROM newjs.CASTE ORDER BY SORTBY";
			$res = mysql_query_decide($sql) or die(mysql_error()) or logError("error",$sql);
			$i=0;
			while($row = mysql_fetch_array($res))
			{
				$full_caste_arr[$i]["VALUE"] = $row["VALUE"];
				$full_caste_arr[$i]["LABEL"] = $row["LABEL"];
				$full_caste_arr[$i]["PARENT"] = $row["PARENT"];
				$i++;
			}

			$caste_community = $caste."-".$mtongue;
			$sql = "SELECT MAP FROM newjs.CASTE_COMMUNITY_MAPPING WHERE CASTE_COMMUNITY = '$caste_community'";
			$res = mysql_query_decide($sql) or die(mysql_error()) or logError("error",$sql);
			$row = mysql_fetch_array($res);

			$caste_community_arr = @explode(",",$row['MAP']);
			for($i=0;$i<count($caste_community_arr);$i++)
			{
				$temp_caste_arr = @explode("-",$caste_community_arr[$i]);
				if(!@in_array($temp_caste_arr[0],$mapped_caste))
					$mapped_caste[] = $temp_caste_arr[0];
			}
			unset($temp_caste_arr);

			$j=0;
			for($i=0;$i<count($full_caste_arr);$i++)
			{
				if(@in_array($full_caste_arr[$i]["VALUE"], $mapped_caste))
				{
					$temp_label = explode(": ",$full_caste_arr[$i]["LABEL"]);
					if($temp_label[1])
					{
						$temp_caste_arr[] = $full_caste_arr[$i]["VALUE"];
						$final_caste_arr[$j]["VALUE"] = $full_caste_arr[$i]["VALUE"];
						$final_caste_arr[$j]["LABEL"] = $temp_label[1];
						$j++;
					}
				}
			}

			$mapped_caste_values = @implode("|#|",$temp_caste_arr);
			unset($temp_caste_arr);
			unset($temp_label);
			unset($mapped_caste);

			if(count($final_caste_arr))
			{
				$final_caste_arr[$j]["VALUE"] = "";
				$final_caste_arr[$j]["LABEL"] = "";
				$full_caste_id_str = @implode("|#|",$full_caste_arr);
				$j++;
			}
			for($i=0;$i<count($full_caste_arr);$i++)
			{
//				if($full_caste_arr[$i]["PARENT"] == $religion && !@in_array($full_caste_arr[$i]["VALUE"],$temp_caste_arr))
				if($full_caste_arr[$i]["PARENT"] == $religion)
				{
					$temp_label = explode(": ",$full_caste_arr[$i]["LABEL"]);
					if($temp_label[1])
					{
						$final_caste_arr[$j]["VALUE"] = $full_caste_arr[$i]["VALUE"];
						$final_caste_arr[$j]["LABEL"] = $temp_label[1];
						$j++;
					}
				}
			}
			unset($temp_label);
			unset($full_caste_arr);
			
			$xml = new DomDocument;
			loadMyXml($path."/profile/registration_pg1_eng.xml");
			$registration_pg1 = $xml->getElementsByTagName("registrationPage1")->item(0);
			$dropdowns = $registration_pg1->getElementsByTagName("dropdowns")->item(0);
			$dmat = $dropdowns->getElementsByTagName("doesntMatter")->item(0)->nodeValue;

			$div_write_to_htm = "<div id=\"partner_caste_DM\">";
			$div_write_to_htm .= "<label>";
			$div_write_to_htm .= $dmat;
			$div_write_to_htm .= "</label></div><br />";

			for($i=0;$i<count($final_caste_arr);$i++)
			{
				if($final_caste_arr[$i]["VALUE"] == "")
				{
					$write_to_htm .= "<div class=\"dhrow\">";
					$write_to_htm .= "<span style=\"color:#0a89fe;\">------</span>";
					$write_to_htm .= "</div>";
					$write_to_htm .= "<div class=\"clear\"></div>";
				}
				else
				{
					$write_to_htm .= "<input type=\"checkbox\" name=\"partner_caste_displaying_arr[]\" value=\"".$final_caste_arr[$i]["VALUE"]."\" class=\"chbx checkboxalign\" id=\"partner_caste_displaying_".$final_caste_arr[$i]["VALUE"]."\"/>";
					$write_to_htm .= "<label id=\"partner_caste_displaying_label_".$final_caste_arr[$i]["VALUE"]."\">";
					$write_to_htm .= $final_caste_arr[$i]["LABEL"];
					$write_to_htm .= "</label>";
					$write_to_htm .= "<br />";
				}
			}

			$hidden_write_to_htm .= "<input type=\"hidden\" name=\"mapped_caste_values\" value=\"".$mapped_caste_values."\" id=\"mapped_caste_values\"/>";
			
			$hidden_write_to_htm .= "<input type=\"checkbox\" name=\"partner_caste_arr[]\" value=\"DM\" id=\"partner_caste_DM\"/>";
			$hidden_write_to_htm .= "<label id=\"partner_caste_label_DM\">";
			$hidden_write_to_htm .= $dmat;
			$hidden_write_to_htm .= "</label>";
			$hidden_write_to_htm .= "<br />";
			
			for($i=0;$i<count($final_caste_arr);$i++)
			{
				if($final_caste_arr[$i]["VALUE"] != "")
				{
					$hidden_write_to_htm .= "<input type=\"checkbox\" name=\"partner_caste_arr[]\" value=\"".$final_caste_arr[$i]["VALUE"]."\" id=\"partner_caste_".$final_caste_arr[$i]["VALUE"]."\"/>";
					$hidden_write_to_htm .= "<label id=\"partner_caste_label_".$final_caste_arr[$i]["VALUE"]."\">";
					$hidden_write_to_htm .= $final_caste_arr[$i]["LABEL"];
					$hidden_write_to_htm .= "</label>";
					$hidden_write_to_htm .= "<br />";
				}
			}
			
			unset($final_caste_arr);
			echo $write_to_htm."|#X#|".$hidden_write_to_htm."|#X#|".$div_write_to_htm;
			exit;
		}
	}
?>