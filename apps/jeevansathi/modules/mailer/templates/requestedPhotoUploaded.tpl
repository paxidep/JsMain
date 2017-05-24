~include_partial("global/mailerheader")`
<body>
		 <table align="center" style="font-size: 11px;">
        <tr>
            <td>Please add ~$mailerName` to your address book to ensure delivery of this mail into you inbox</td>
        </tr>
     </table>
    <table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="border:1px solid #dcdcdc; max-width:650px; text-align:left" align="center">
        <tr>
            <td style="border-bottom:1px solid #ededed">
			~include_partial("global/mailer_sub_header",[commonParamaters=>$data.commonParamaters,stype=>$data.stypeMatch,mailerLinks=>$mailerLinks])`
            </td>
        </tr>
        <tr>
            <td width="650" style="border-top:1px solid #dcdcdc">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
                    <tr>
                        <td colspan="3" height="10"></td>
                    </tr>
                    <tr>
                        <td width="22"><img src="~sfConfig::get('app_img_url')`/images/symfonyMailer/spacer.gif" width="6" height="1" vspace="0" hspace="0" align="left" /></td>
                        <td width="606">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:Arial, Times New Roman, Times, serif; font-size:12px; color:#000000; text-align:left;">
                                <tr>
                                    <td valign="top">Dear ~if $data.RECEIVER.PROFILE->getNAME()`~$data.RECEIVER.PROFILE->getNAME()`~else`~$data.RECEIVER.PROFILE->getUSERNAME()`~/if`,</td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="10"></td>
                                </tr>
                                <tr>
                                    <td valign="top" width="606">
                                        <table border="0" cellspacing="0" cellpadding="0" style="max-width:540px;font-family:Arial, Times New Roman, Times, serif; font-size:12px; color:#000000; text-align:left;" align="left">
                                            <tr>
                                                <td width="540">~$data.body`
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="22"><img src="~sfConfig::get('app_img_url')`/images/symfonyMailer/spacer.gif" width="6" height="1" vspace="0" hspace="0" align="left" /></td>
                    </tr>
                    ~if $data.MEMBERSHIP.vd`
                    <tr>
                        <td colspan="3" height="10"></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center">
				 ~include_partial("global/mailerVD",[vd=>$data.MEMBERSHIP.vd,commonParamaters=>$data.commonParamaters,stype=>$data.stypeMatch,profilechecksum=>$data.RECEIVER.PROFILECHECKSUM,tracking=>$data.MEMBERSHIP.tracking,mailerLinks=>$mailerLinks])`
                        </td>
                    </tr>
                    ~/if`
                    <tr>
                        <td colspan="3" height="10"></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                        	~include_partial("global/mailerTupleContent",[users=>$data.USERS,logic=>$data.logic,commonParameters=>$data.commonParamaters,stypeMatch=>$data.stypeMatch,count=>$data.COUNT,mailerLinks=>$mailerLinks])`
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" height="10"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            ~if $data.MEMBERSHIP.membership eq 0 || ( $data.MEMBERSHIP.membership eq 1 && $data.MEMBERSHIP.renew.RENEW eq 1)`
                            	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="text-align:left;">
                                	<tr>
                                    		<td>
                                        		<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                                            		<tr>
                                                	<td height="10" style="border-top:1px solid #eae9e9"><img src="~sfConfig::get('app_img_url')`/images/symfonyMailer/spacer.gif" width="6" height="10" vspace="0" hspace="0" align="left" /></td>
                                            		</tr>
                                        		</table>
                                    		</td>
                                	</tr>
                                	<tr>
                                    		<td style="padding-left:10px;">
                                        	~include_partial("global/mailerUpgrade",[vd=>$data.MEMBERSHIP.vd,membership=>$data.MEMBERSHIP.membership,renew=>$data.MEMBERSHIP.renew,commonParameters=>$data.commonParamaters,receiverProfilechecksum=>$data.RECEIVER.PROFILECHECKSUM,stypeMatch=>$data.stypeMatch,discount=>$data.MEMBERSHIP.tracking,mailerLinks=>$mailerLinks])`
                                    		</td>
                                	</tr>
                                	<tr>
                                    		<td>
                                        	<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                                            	<tr>
                                                <td height="10" style="border-bottom:1px solid #eae9e9"><img src="~sfConfig::get('app_img_url')`/images/symfonyMailer/spacer.gif" width="6" height="10" vspace="0" hspace="0" align="left" /></td>
                                            	</tr>
                                        	</table>
                                    		</td>
                                	</tr>
                            	</table>
                            ~/if`
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" height="27"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            ~include_partial("global/mailerJsSignature")`
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" height="24"></td>
                    </tr>
                    <tr>
			<td></td>
                        <td colspan="3">
                            ~include_partial("global/mailerfooter",[logic=>$data.logic,mailerParameter=>"matchalertTrack=1",mailerLinks=>$mailerLinks])`
                        </td>
			<td></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="15"></td>
        </tr>
        <tr>
            <td align="center" style="padding-left:1%">
            </td>
        </tr>
    </table>
</body>
