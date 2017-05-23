<?php
/**
 * Copyright (c) 2015-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace FacebookAds\Object\Fields;

use FacebookAds\Enum\AbstractEnum;

/**
 * This class is auto-genereated.
 *
 * For any issues or feature requests related to this class, please let us know
 * on github and we'll fix in our codegen framework. We'll not be able to accept
 * pull request for this class.
 *
 */

class AdAccountFields extends AbstractEnum {

  const ACCOUNT_ID = 'account_id';
  const ACCOUNT_STATUS = 'account_status';
  const AGE = 'age';
  const AGENCY_CLIENT_DECLARATION = 'agency_client_declaration';
  const AMOUNT_SPENT = 'amount_spent';
  const BALANCE = 'balance';
  const BUSINESS = 'business';
  const BUSINESS_CITY = 'business_city';
  const BUSINESS_COUNTRY_CODE = 'business_country_code';
  const BUSINESS_NAME = 'business_name';
  const BUSINESS_STATE = 'business_state';
  const BUSINESS_STREET = 'business_street';
  const BUSINESS_STREET2 = 'business_street2';
  const BUSINESS_ZIP = 'business_zip';
  const CAPABILITIES = 'capabilities';
  const CREATED_TIME = 'created_time';
  const CURRENCY = 'currency';
  const DISABLE_REASON = 'disable_reason';
  const END_ADVERTISER = 'end_advertiser';
  const END_ADVERTISER_NAME = 'end_advertiser_name';
  const FAILED_DELIVERY_CHECKS = 'failed_delivery_checks';
  const FUNDING_SOURCE = 'funding_source';
  const FUNDING_SOURCE_DETAILS = 'funding_source_details';
  const HAS_MIGRATED_PERMISSIONS = 'has_migrated_permissions';
  const ID = 'id';
  const IO_NUMBER = 'io_number';
  const IS_NOTIFICATIONS_ENABLED = 'is_notifications_enabled';
  const IS_PERSONAL = 'is_personal';
  const IS_PREPAY_ACCOUNT = 'is_prepay_account';
  const IS_TAX_ID_REQUIRED = 'is_tax_id_required';
  const LINE_NUMBERS = 'line_numbers';
  const MEDIA_AGENCY = 'media_agency';
  const MIN_CAMPAIGN_GROUP_SPEND_CAP = 'min_campaign_group_spend_cap';
  const MIN_DAILY_BUDGET = 'min_daily_budget';
  const NAME = 'name';
  const OFFSITE_PIXELS_TOS_ACCEPTED = 'offsite_pixels_tos_accepted';
  const OWNER = 'owner';
  const PARTNER = 'partner';
  const RF_SPEC = 'rf_spec';
  const SPEND_CAP = 'spend_cap';
  const TAX_ID = 'tax_id';
  const TAX_ID_STATUS = 'tax_id_status';
  const TAX_ID_TYPE = 'tax_id_type';
  const TIMEZONE_ID = 'timezone_id';
  const TIMEZONE_NAME = 'timezone_name';
  const TIMEZONE_OFFSET_HOURS_UTC = 'timezone_offset_hours_utc';
  const TOS_ACCEPTED = 'tos_accepted';
  const USER_ROLE = 'user_role';

  public function getFieldTypes() {
    return array(
      'account_id' => 'string',
      'account_status' => 'unsigned int',
      'age' => 'float',
      'agency_client_declaration' => 'AgencyClientDeclaration',
      'amount_spent' => 'string',
      'balance' => 'string',
      'business' => 'Business',
      'business_city' => 'string',
      'business_country_code' => 'string',
      'business_name' => 'string',
      'business_state' => 'string',
      'business_street' => 'string',
      'business_street2' => 'string',
      'business_zip' => 'string',
      'capabilities' => 'list<string>',
      'created_time' => 'datetime',
      'currency' => 'string',
      'disable_reason' => 'unsigned int',
      'end_advertiser' => 'string',
      'end_advertiser_name' => 'string',
      'failed_delivery_checks' => 'list<DeliveryCheck>',
      'funding_source' => 'string',
      'funding_source_details' => 'FundingSourceDetails',
      'has_migrated_permissions' => 'bool',
      'id' => 'string',
      'io_number' => 'string',
      'is_notifications_enabled' => 'bool',
      'is_personal' => 'unsigned int',
      'is_prepay_account' => 'bool',
      'is_tax_id_required' => 'bool',
      'line_numbers' => 'list<int>',
      'media_agency' => 'string',
      'min_campaign_group_spend_cap' => 'string',
      'min_daily_budget' => 'unsigned int',
      'name' => 'string',
      'offsite_pixels_tos_accepted' => 'bool',
      'owner' => 'string',
      'partner' => 'string',
      'rf_spec' => 'ReachFrequencySpec',
      'spend_cap' => 'string',
      'tax_id' => 'string',
      'tax_id_status' => 'unsigned int',
      'tax_id_type' => 'string',
      'timezone_id' => 'unsigned int',
      'timezone_name' => 'string',
      'timezone_offset_hours_utc' => 'float',
      'tos_accepted' => 'map<string, int>',
      'user_role' => 'string',
    );
  }
}
