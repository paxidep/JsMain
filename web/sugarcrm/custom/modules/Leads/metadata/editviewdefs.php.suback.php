<?php
// created: 2010-07-27 13:20:29
$viewdefs['Leads']['EditView'] = array (
  'templateMeta' => 
  array (
    'form' => 
    array (
      'hidden' => 
      array (
        0 => '<input type="hidden" name="prospect_id" value="{if isset($smarty.request.prospect_id)}{$smarty.request.prospect_id}{else}{$bean->prospect_id}{/if}">',
        1 => '<input type="hidden" name="account_id" value="{if isset($smarty.request.account_id)}{$smarty.request.account_id}{else}{$bean->account_id}{/if}">',
        2 => '<input type="hidden" name="contact_id" value="{if isset($smarty.request.contact_id)}{$smarty.request.contact_id}{else}{$bean->contact_id}{/if}">',
        3 => '<input type="hidden" name="opportunity_id" value="{if isset($smarty.request.opportunity_id)}{$smarty.request.opportunity_id}{else}{$bean->opportunity_id}{/if}">',
      ),
      'buttons' => 
      array (
        0 => 'SAVEL',
        1 => 'CANCEL',
      ),
    ),
    'maxColumns' => '2',
    'widths' => 
    array (
      0 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
      1 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
    ),
    'includes' => 
    array (
      0 => 
      array (
        'file' => 'custom/modules/Leads/leads_cstm.js',
      ),
    ),
    'javascript' => '<script type="text/javascript" language="Javascript">function copyAddressRight(form)  {ldelim} form.alt_address_street.value = form.primary_address_street.value;form.alt_address_city.value = form.primary_address_city.value;form.alt_address_state.value = form.primary_address_state.value;form.alt_address_postalcode.value = form.primary_address_postalcode.value;form.alt_address_country.value = form.primary_address_country.value;return true; {rdelim} function copyAddressLeft(form)  {ldelim} form.primary_address_street.value =form.alt_address_street.value;form.primary_address_city.value = form.alt_address_city.value;form.primary_address_state.value = form.alt_address_state.value;form.primary_address_postalcode.value =form.alt_address_postalcode.value;form.primary_address_country.value = form.alt_address_country.value;return true; {rdelim} </script>',
  ),
  'panels' => 
  array (
    'lbl_panel2' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'email1',
          'label' => 'LBL_EMAIL_ADDRESS',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'phone_mobile',
          'label' => 'LBL_MOBILE_PHONE',
          'customCode' => '<input id="phone_mobile" type="text" tabindex="2" title="" value="{$fields.phone_mobile.value}" maxlength="25" size="30" name="phone_mobile" onblur="xmlhttpPost(\'phone_mobile\')"/><div name="result1" id="result1"></div><input type="hidden" id="phone_mobile_hd">',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'std_c',
          'label' => 'LBL_STD',
          'customCode' => '<input id="std_c" type="text" tabindex="3" title="" value="{$fields.std_c.value}" maxlength="25" size="10" name="std_c" onblur="xmlhttpPost(\'phone_home\')"/>',
        ),
        1 => 
        array (
          'name' => 'phone_home',
          'label' => 'LBL_HOME_PHONE',
          'customCode' => '<input id="phone_home" type="text" tabindex="4" title="" value="{$fields.phone_home.value}" maxlength="25" size="30" name="phone_home" onblur="xmlhttpPost(\'phone_home\')"/><div name="result" id="result"></div>',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'do_not_email_c',
          'label' => 'LBL_DO_NOT_EMAIL',
        ),
        1 => NULL,
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'do_not_call',
          'label' => 'LBL_DO_NOT_CALL',
        ),
        1 => '',
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'primary_address_street',
          'label' => 'LBL_PRIMARY_ADDRESS_STREET',
        ),
        1 => NULL,
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'primary_address_postalcode',
          'label' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
        ),
        1 => NULL,
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'city_c',
          'label' => 'LBL_CITY',
        ),
      ),
    ),
    'lbl_contact_information' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'posted_by_c',
          'label' => 'LBL_POSTED_BY',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'assistant',
          'label' => 'LBL_ASSISTANT',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'last_name',
          'label' => 'LBL_LAST_NAME',
        ),
        1 => 'opportunity_amount',
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'date_birth_c',
          'label' => 'LBL_DATE_BIRTH',
        ),
        1 => 
        array (
          'name' => 'age_c',
          'label' => 'LBL_AGE',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'gender_c',
          'label' => 'LBL_GENDER',
        ),
        1 => 
        array (
          'name' => 'have_photo_c',
          'label' => 'LBL_HAVE_PHOTO',
        ),
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'height_c',
          'label' => 'LBL_HEIGHT',
        ),
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'marital_status_c',
          'label' => 'LBL_MARITAL_STATUS',
        ),
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'religion_c',
          'label' => 'LBL_RELIGION',
          'displayParams' => 
          array (
            'javascript' => 'onchange="initData()"',
          ),
        ),
      ),
      8 => 
      array (
        0 => 
        array (
          'name' => 'mother_tongue_c',
          'label' => 'LBL_MOTHER_TONGUE',
        ),
        1 => 
        array (
          'name' => 'new_mtongue_c',
          'label' => 'LBL_NEW_MTONGUE',
        ),
      ),
      9 => 
      array (
        0 => 
        array (
          'name' => 'caste_c',
          'label' => 'LBL_CASTE',
        ),
        1 => 
        array (
          'name' => 'subcaste_c',
          'label' => 'LBL_SUBCASTE',
        ),
      ),
      10 => 
      array (
        0 => 
        array (
          'name' => 'education_c',
          'label' => 'LBL_EDUCATION',
        ),
      ),
      11 => 
      array (
        0 => 
        array (
          'name' => 'occupation_c',
          'label' => 'LBL_OCCUPATION',
        ),
      ),
      12 => 
      array (
        0 => 
        array (
          'name' => 'income_c',
          'label' => 'LBL_INCOME',
        ),
      ),
      13 => 
      array (
        0 => 
        array (
          'name' => 'manglik_c',
          'label' => 'LBL_MANGLIK',
        ),
        1 => 
        array (
          'name' => 'have_email_c',
          'label' => 'LBL_HAVE_EMAIL',
        ),
      ),
      14 => 
      array (
        0 => 'birthdate',
      ),
    ),
    'lbl_panel4' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'lead_source',
          'label' => 'LBL_LEAD_SOURCE',
        ),
      ),
    ),
    'lbl_panel3' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'type_c',
          'label' => 'LBL_TYPE',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'edition_date_c',
          'label' => 'LBL_EDITION_DATE',
        ),
      ),
    ),
    'lbl_panel1' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'campaign_name',
          'label' => 'LBL_CAMPAIGN',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'strength_c',
          'label' => 'LBL_STRENGTH',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'response_ad_c',
          'label' => 'LBL_RESPONSE_AD',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'product_c',
          'label' => 'LBL_PRODUCT',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'product_value_c',
          'label' => 'LBL_PRODUCT_VALUE',
        ),
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'status',
          'label' => 'LBL_STATUS',
        ),
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'expect_pay_in_c',
          'label' => 'LBL_EXPECT_PAY_IN',
        ),
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_ASSIGNED_TO_NAME',
        ),
      ),
    ),
  ),
);
?>
