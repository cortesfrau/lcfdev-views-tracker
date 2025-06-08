<?php

defined('ABSPATH') || exit;

/**
 * Plugin Name: LCFDev Views Tracker
 * Description: Plugin to track and get the most viewed content of the site.
 * Version: 1.0.0
 * Author URI: https://lluiscortes.com/
 * Author: Lluís Cortès
 * Text Domain: lcfdev-views-tracker
 * GitHub Plugin URI: https://github.com/cortesfrau/lcfdev-views-tracker
 * Domain Path: /languages
 * License: GPLv2 or later
 */

define('LCFDEV_VIEWS_TRACKER_DIR', plugin_dir_path(__FILE__));
define('LCFDEV_VIEWS_TRACKER_URL', plugin_dir_url(__FILE__));
define('LCFDEV_VIEWS_TRACKER_FILE', __FILE__);
define('LCFDEV_VIEWS_TRACKER_BASENAME', plugin_basename(__FILE__));

require_once LCFDEV_VIEWS_TRACKER_DIR . 'includes/class-lcfdev-views-tracker.php';
require_once LCFDEV_VIEWS_TRACKER_DIR . 'includes/class-lcfdev-views-tracker-database.php';
require_once LCFDEV_VIEWS_TRACKER_DIR . 'includes/class-lcfdev-views-tracker-counter.php';
require_once LCFDEV_VIEWS_TRACKER_DIR . 'includes/class-lcfdev-views-tracker-query.php';
require_once LCFDEV_VIEWS_TRACKER_DIR . 'includes/functions.php';

LCFDev_Views_Tracker::instance();