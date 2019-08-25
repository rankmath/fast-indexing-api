<?php
/**
 * Plugin Name: Instant Indexing Google Bing
 * Plugin URI: https://s.rankmath.com/indexing-api
 * Description: Crawl pages instantly with the Google & Bing Indexing API.
 * Version: 1.2
 * Author: Rank Math
 * Author URI: https://s.rankmath.com/home
 * License: GPLv2
 * Text Domain: instant-indexing-google-bing
 * Domain Path: /languages
 *
 * @package Google Indexing API
 */

defined( 'ABSPATH' ) || die;

define( 'RM_GIAPI_PATH', plugin_dir_path( __FILE__ ) );
define( 'RM_GIAPI_FILE', plugin_basename( __FILE__ ) );
define( 'RM_GIAPI_URL', plugin_dir_url( __FILE__ ) );

/**
 * Require Rank Math module class.
 */
require_once 'includes/class-rm-giapi-module.php';

/**
 * Require plugin class.
 */
require_once 'includes/class-rm-giapi.php';

/**
 * Instantiate plugin.
 */
$rm_giapi = new RM_GIAPI();
