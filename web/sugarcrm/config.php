<?php
// created: 2010-07-27 13:48:34
$sugar_config = array (
  'admin_access_control' => true,
  'admin_export_only' => false,
  'cache_dir' => 'c/',
  'calculate_response_time' => true,
  'common_ml_dir' => '',
  'create_default_user' => false,
  'currency' => '',
  'dashlet_display_row_options' => 
  array (
    0 => '1',
    1 => '3',
    2 => '5',
    3 => '10',
  ),
  'date_formats' => 
  array (
    'Y-m-d' => '2006-12-23',
    'm-d-Y' => '12-23-2006',
    'd-m-Y' => '23-12-2006',
    'Y/m/d' => '2006/12/23',
    'm/d/Y' => '12/23/2006',
    'd/m/Y' => '23/12/2006',
    'Y.m.d' => '2006.12.23',
    'd.m.Y' => '23.12.2006',
    'm.d.Y' => '12.23.2006',
  ),
  'datef' => 'm/d/Y',
  'dbconfig' => 
  array (
    'db_host_name' => MysqlDbConstants::$master[HOST].":".MysqlDbConstants::$master[PORT],
    'db_host_instance' => 'SQLEXPRESS',
    'db_user_name' => MysqlDbConstants::$master[USER],
    'db_password' => MysqlDbConstants::$master[PASS],
    'db_name' => 'sugarcrm',
    'db_type' => 'mysql',
  ),
  'dbconfigoption' => 
  array (
    'persistent' => false,
    'autofree' => false,
    'debug' => 0,
    'seqname_format' => '%s_seq',
    'portability' => 0,
    'ssl' => false,
  ),
  'default_action' => 'index',
  'default_charset' => 'UTF-8',
  'default_currencies' => 
  array (
    'AUD' => 
    array (
      'name' => 'Australian Dollars',
      'iso4217' => 'AUD',
      'symbol' => '$',
    ),
    'BRL' => 
    array (
      'name' => 'Brazilian Reais',
      'iso4217' => 'BRL',
      'symbol' => 'R$',
    ),
    'GBP' => 
    array (
      'name' => 'British Pounds',
      'iso4217' => 'GBP',
      'symbol' => '£',
    ),
    'CAD' => 
    array (
      'name' => 'Canadian Dollars',
      'iso4217' => 'CAD',
      'symbol' => '$',
    ),
    'CNY' => 
    array (
      'name' => 'Chinese Yuan',
      'iso4217' => 'CNY',
      'symbol' => '￥',
    ),
    'EUR' => 
    array (
      'name' => 'Euro',
      'iso4217' => 'EUR',
      'symbol' => '€',
    ),
    'HKD' => 
    array (
      'name' => 'Hong Kong Dollars',
      'iso4217' => 'HKD',
      'symbol' => '$',
    ),
    'INR' => 
    array (
      'name' => 'Indian Rupees',
      'iso4217' => 'INR',
      'symbol' => '₨',
    ),
    'KRW' => 
    array (
      'name' => 'Korean Won',
      'iso4217' => 'KRW',
      'symbol' => '₩',
    ),
    'YEN' => 
    array (
      'name' => 'Japanese Yen',
      'iso4217' => 'JPY',
      'symbol' => '¥',
    ),
    'MXM' => 
    array (
      'name' => 'Mexican Pesos',
      'iso4217' => 'MXM',
      'symbol' => '$',
    ),
    'SGD' => 
    array (
      'name' => 'Singaporean Dollars',
      'iso4217' => 'SGD',
      'symbol' => '$',
    ),
    'CHF' => 
    array (
      'name' => 'Swiss Franc',
      'iso4217' => 'CHF',
      'symbol' => 'SFr.',
    ),
    'THB' => 
    array (
      'name' => 'Thai Baht',
      'iso4217' => 'THB',
      'symbol' => '฿',
    ),
    'USD' => 
    array (
      'name' => 'US Dollars',
      'iso4217' => 'USD',
      'symbol' => '$',
    ),
  ),
  'default_currency_iso4217' => 'INR',
  'default_currency_name' => 'Indian Rupees',
  'default_currency_significant_digits' => '2',
  'default_currency_symbol' => '₨',
  'default_date_format' => 'd-m-Y',
  'default_decimal_seperator' => '.',
  'default_email_charset' => 'UTF-8',
  'default_email_client' => 'sugar',
  'default_email_editor' => 'html',
  'default_export_charset' => 'UTF-8',
  'default_language' => 'en_us',
  'default_locale_name_format' => 's f l',
  'default_max_subtabs' => '12',
  'default_max_tabs' => '12',
  'default_module' => 'Home',
  'default_navigation_paradigm' => 'm',
  'default_number_grouping_seperator' => ',',
  'default_password' => '',
  'default_permissions' => 
  array (
    'dir_mode' => 1528,
    'file_mode' => 432,
    'user' => '',
    'group' => '',
  ),
  'default_subpanel_links' => false,
  'default_subpanel_tabs' => true,
  'default_swap_last_viewed' => false,
  'default_swap_shortcuts' => false,
  'default_theme' => 'Sugar',
  'default_time_format' => 'h:ia',
  'default_user_is_admin' => false,
  'default_user_name' => '',
  'demoData' => 'Yes',
  'developerMode' => true,
  'disable_export' => false,
  'disable_persistent_connections' => 'false',
  'display_email_template_variable_chooser' => false,
  'display_inbound_email_buttons' => false,
  'dump_slow_queries' => false,
  'email_default_client' => 'sugar',
  'email_default_delete_attachments' => true,
  'email_default_editor' => 'html',
  'email_num_autoreplies_24_hours' => 10,
  'export_delimiter' => ',',
  'history_max_viewed' => 10,
  'host_name' => substr(JsConstants::$siteUrl,6),
  'import_dir' => 'c/import/',
  'import_max_execution_time' => 3600,
  'import_max_records_per_file' => '1000',
  'installer_locked' => true,
  'js_custom_version' => 1,
  'js_lang_version' => 1,
  'languages' => 
  array (
    'en_us' => 'English (US)',
  ),
  'large_scale_test' => false,
  'list_max_entries_per_page' => 20,
  'list_max_entries_per_subpanel' => 10,
  'lock_default_user_name' => false,
  'lock_homepage' => false,
  'lock_subpanels' => false,
  'log_dir' => '.',
  'log_file' => 'sugarcrm.log',
  'log_memory_usage' => false,
  'logger' => 
  array (
    'level' => 'fatal',
    'file' => 
    array (
      'ext' => '.log',
      'name' => 'sugarcrm',
      'dateFormat' => '%c',
      'maxSize' => '10MB',
      'maxLogs' => 10,
      'suffix' => '%m_%Y',
    ),
  ),
  'login_nav' => false,
  'max_dashlets_homepage' => '15',
  'passwordsetting' => 
  array (
    'SystemGeneratedPasswordON' => '',
    'generatepasswordtmpl' => '',
    'lostpasswordtmpl' => '',
    'forgotpasswordON' => false,
    'linkexpiration' => '1',
    'linkexpirationtime' => '30',
    'linkexpirationtype' => '1',
    'systexpiration' => '0',
    'systexpirationtime' => '',
    'systexpirationtype' => '0',
    'systexpirationlogin' => '',
  ),
  'portal_view' => 'single_user',
  'require_accounts' => true,
  'resource_management' => NULL,
  'rss_cache_time' => '10800',
  'save_query' => 'all',
  'session_dir' => '',
  'showDetailData' => true,
  'showThemePicker' => true,
  'site_url' => JsConstants::$siteUrl . "/sugarcrm",
  'slow_query_time_msec' => '100',
  'stack_trace_errors' => false,
  'sugar_version' => '6.0.0',
  'sugarbeet' => true,
  'time_formats' => 
  array (
    'H:i' => '23:00',
    'h:ia' => '11:00pm',
    'h:iA' => '11:00PM',
    'H.i' => '23.00',
    'h.ia' => '11.00pm',
    'h.iA' => '11.00PM',
  ),
  'timef' => 'H:i',
  'tmp_dir' => 'c/xml/',
  'tracker_max_display_length' => 15,
  'translation_string_prefix' => false,
  'unique_key' => '68df2217822d80417a7ffaecfc4dc5ca',
  'upload_badext' => 
  array (
    0 => 'php',
    1 => 'php3',
    2 => 'php4',
    3 => 'php5',
    4 => 'pl',
    5 => 'cgi',
    6 => 'py',
    7 => 'asp',
    8 => 'cfm',
    9 => 'js',
    10 => 'vbs',
    11 => 'html',
    12 => 'htm',
  ),
  'upload_dir' => 'c/upload/',
  'upload_maxsize' => '21000000',
  'use_common_ml_dir' => false,
  'use_php_code_json' => true,
  'use_real_names' => false,
  'vcal_time' => '2',
  'verify_client_ip' => false,
  'mail_deleted_profile_retrieval'=>'mahesh@jeevansathi.com',
  'mail_ltf_supervisor'=>'ltfsupervisor@jeevansathi.com',
);
?>