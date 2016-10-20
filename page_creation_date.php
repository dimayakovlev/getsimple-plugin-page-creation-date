<?php
/*
Plugin Name: Page Creation Date
Description: Add support for page creation date
Version: 1.0
Author: Dmitry Yakovlev
Author URI: http://dimayakovlev.ru/
*/
$thisfile = basename(__FILE__, ".php");

if (!is_frontend()) {
  i18n_merge($thisfile) || i18n_merge($thisfile, 'en_US');
  add_action('edit-meta', 'pluginPageCreationDateGUI', array($thisfile));
  add_filter('pagesavexml', 'pluginPageCreationDateSaveData');
  add_filter('draftsavexml', 'pluginPageCreationDateSaveData');
}

register_plugin(
  $thisfile,
  i18n_r($thisfile.'/TITLE'),
  '1.0',
  i18n_r($thisfile.'/AUTHOR'),
  'http://dimayakovlev.ru',
  i18n_r($thisfile.'/DESCRIPTION'),
  '',
  ''
);

function pluginPageCreationDateGUI($plugin_name) {
  global $data_edit;
  if (isset($data_edit)) {
    $page_creation_date = !empty($data_edit->creDate) ? $data_edit->creDate : $data_edit->pubDate;
  } else {
    $page_creation_date = '';
  }
  echo '<input type="hidden" name="post-creDate" value="'.$page_creation_date.'">'.PHP_EOL;
  if ($page_creation_date) echo '<div class="wideopt"><p><i class="fa fa-calendar" aria-hidden="true"></i> <strong>'.i18n_r($plugin_name.'/PAGE_WAS_CREATED').'</strong> '.output_datetime((string)$page_creation_date).'</p></div>'.PHP_EOL;
}

function pluginPageCreationDateSaveData($xml) {
  $page_creation_date = isset($_POST['post-creDate']) ? safe_slash_html($_POST['post-creDate']) : date('r');
  $xml->addCDataChild('creDate', $page_creation_date);
  return $xml;
}
/**
 * Get Page Creation Date
 *
 * This will return the page's creation or page's update date/timestamp
 *
 * @uses $data_index
 * @uses $date
 * @uses $TIMEZONE
 *
 * @param string $i Optional, default is "l, F jS, Y - g:i A"
 * @param bool $echo Optional, default is true. False will 'return' value
 * @param bool $force Options, default is true. Force to use page's update date/timestamp if creation date/timestamp was not setted
 * @return string|null Echos or returns based on param $echo
 */
function get_page_creation_date($i = "l, F jS, Y - g:i A", $echo = true, $force = true) {
  global $TIMEZONE, $data_index;
  $page_creation_date = (string)$data_index->creDate;
  if (!$page_creation_date) {
    if ($force) {
      get_page_date($i, $echo);
    } else {
      return null;
    }
  }

	if ($TIMEZONE != '' && function_exists('date_default_timezone_set')) {
		date_default_timezone_set($TIMEZONE);
	}
	
	$str = formatDate($i, strtotime($page_creation_date));
	return echoReturn($str,$echo);
}