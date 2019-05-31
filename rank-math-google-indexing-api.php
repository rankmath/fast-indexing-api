<?php
/**
 * Plugin Name: Rank Math Google Indexing API
 * Plugin URI: https://s.rankmath.com/indexing-api
 * Description: Crawl pages instantly with the indexing API.
 * Version: 1.2
 * Author: Rank Math
 * Author URI: https://rankmath.com
 * License: GPLv2
 *
 * @package Google Indexing API
 */

defined( 'ABSPATH' ) || die;

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
