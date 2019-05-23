<?php
/*
 * Plugin Name: Rank Math Google Indexing API
 * Plugin URI: https://rankmath.com
 * Description: Crawl pages instantly with the indexing API.
 * Version: 1.1
 * Author: Rank Math
 * Author URI: https://rankmath.com
 * License: GPLv2
 */

defined('ABSPATH') or die;

class RM_GIAPI {

    public $dashboard_menu_hook_suffix = '';
    public $console_menu_hook_suffix = '';
    public $settings_menu_hook_suffix = '';
    public $notices = array();
    public $debug = false;
    public $setup_guide_url = 'https://rankmath.com/blog/google-indexing-api/';

    function __construct() {
        $this->debug = ( defined( 'GIAPI_DEBUG' ) && GIAPI_DEBUG );

        add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
        add_action( 'admin_footer', array( $this, 'admin_footer' ), 20 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'wp_ajax_rm_giapi',array( $this,'ajax_rm_giapi' ) );
        add_action( 'wp_ajax_rm_giapi_limits',array( $this,'ajax_get_limits' ) );
        add_action( 'admin_init', array( $this, 'rm_missing_admin_notice_error' ), 20, 1 );
        add_action( 'admin_notices', array( $this, 'display_notices' ), 10, 1 );
        add_action( 'load-rank-math_page_rm-giapi-settings', array( $this, 'save_settings' ), 10, 1 );
        
        if ( $this->get_setting( 'json_key' ) ) {
            $post_types = $this->get_setting( 'post_types', array() );
            foreach ( $post_types as $post_type => $enabled ) {
                if ( ! $enabled ) {
                    continue;
                }
                add_filter( $post_type . '_row_actions', array( $this, 'send_to_api_link' ), 10, 2 );
                add_action( 'save_post_' . $post_type, array( $this, 'publish_post' ), 10, 2 );
            }
            add_action( 'trashed_post', array( $this, 'delete_post' ), 10, 1);
        }
        
        // localization
        add_action( 'plugins_loaded', array( $this, 'mythemeshop_giapi_load_textdomain' ) );
        
        add_filter( 'rank_math/modules', array( $this, 'add_rm_module' ), 25 );
    }

    function send_to_api_link( $actions, $post ) {
        if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
            return $actions;
        }
        $nonce = wp_create_nonce( 'giapi-action' );
        $actions['rmgiapi_update'] = '<a href="' . admin_url( 'admin.php?page=rm-giapi-console&apiaction=update&_wpnonce='.$nonce.'&apiurl='.rawurlencode( get_permalink( $post) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __('Indexing API: Update', 'rm-giapi') . '</a>';
        $actions['rmgiapi_getstatus'] = '<a href="' . admin_url( 'admin.php?page=rm-giapi-console&apiaction=getstatus&_wpnonce='.$nonce.'&apiurl='.rawurlencode( get_permalink( $post) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __('Indexing API: Get Status', 'rm-giapi') . '</a>';
        return $actions;
    }
    
    function mythemeshop_giapi_load_textdomain() {
        load_plugin_textdomain( 'mythemeshop-giapi', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' ); 
    }
    
    function ajax_rm_giapi() {
        if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
            die('0');
        }
        $url_input = $this->get_input_urls();
        $action = sanitize_title( $_POST['api_action'] );
        header("Content-type: application/json");

        $result = $this->send_to_api( $url_input, $action );
        wp_send_json( $result );
        exit();
    
    }

    function send_to_api( $url_input, $action ) {
        $url_input = (array) $url_input;

        include_once 'vendor/autoload.php';
        $this->client = new Google_Client();
        $this->client->setAuthConfig( json_decode( $this->get_setting( 'json_key' ), true ) );
        $this->client->setConfig('base_path', 'https://indexing.googleapis.com');
        $this->client->addScope( 'https://www.googleapis.com/auth/indexing' );

        // Batch request
        $this->client->setUseBatch(true);
        //init google batch and set root URL
        $service = new Google_Service_Indexing($this->client);
        $batch = new Google_Http_Batch($this->client,false,'https://indexing.googleapis.com');
        foreach ( $url_input as $i => $url ) {
            $postBody = new Google_Service_Indexing_UrlNotification();
            if ( $action == 'getstatus' ) {
                $request_part = $service->urlNotifications->getMetadata( array( 'url' => $url ) );
            } else {
                $postBody->setType( $action == 'update' ? 'URL_UPDATED' : 'URL_DELETED' );
                $postBody->setUrl( $url );
                $request_part = $service->urlNotifications->publish( $postBody );
            }
            $batch->add( $request_part, 'url-'.$i );
        }

        $results = $batch->execute();
        $data = array();
        $rc = count( $results );
        foreach ( $results as $id => $response ) {
            if ( is_a( $response, 'Google_Service_Exception' ) ) {
                $data[substr( $id, 9 )] = json_decode( $response->getMessage() );
            } else {
                $data[substr( $id, 9 )] = (array) $response->toSimpleObject();
            }
            if ( $rc === 1 ) {
                $data = $data[substr( $id, 9 )];
            }
        }

        $this->log_request( $action );

        if ( $this->debug ) {
            error_log( 'RM GI API result: ' . print_r( $data, true ) );
        }
        
        return $data;
    }

    function log_request( $type ) {
        $requests_log = get_option( 'giapi_requests', array( 'update' => array(), 'delete' => array(), 'getstatus' => array() ) );
        $requests_log[$type][] = time();
        if ( count( $requests_log[$type] ) > 600 ) {
            $requests_log[$type] = array_slice( $requests_log[$type], -600, 600, true );
        }
        update_option( 'giapi_requests', $requests_log );
    }

    function get_limits() {
        $current_limits = array(
            'publishperday' => 0,
            'permin' => 0,
            'metapermin' => 0
        );

        $limit_publishperday = 200;
        $limit_permin = 600;
        $limit_metapermin = 180;
        $requests_log = get_option( 'giapi_requests', array( 'update' => array(), 'delete' => array(), 'getstatus' => array() ) );
        $timestamp_1day_ago = strtotime('-1 day');
        $timestamp_1min_ago = strtotime('-1 minute');

        $publish_1day = 0;
        $all_1min = 0;
        $meta_1min = 0;
        foreach ( $requests_log['update'] as $time ) {
            if ( $time > $timestamp_1day_ago ) {
                $publish_1day++;
            }
            if ( $time > $timestamp_1min_ago ) {
                $all_1min++;
            }
        }
        foreach ( $requests_log['delete'] as $time ) {
            if ( $time > $timestamp_1min_ago ) {
                $all_1min++;
            }
        }
        foreach ( $requests_log['getstatus'] as $time ) {
            if ( $time > $timestamp_1min_ago ) {
                $all_1min++;
                $meta_1min++;
            }
        }
        $current_limits['publishperday'] = 200 - $publish_1day;
        $current_limits['permin'] = 600 - $all_1min;
        $current_limits['metapermin'] = 180 - $meta_1min;

        return $current_limits;
    }

    function ajax_get_limits() {
        wp_send_json( $this->get_limits() );
    }

    function get_input_urls() {
        return array_values( array_filter( array_map( 'trim', explode( "\n", $_POST['url'] ) ) ) );
    }

    function admin_menu() {
        // If Rank Math is not active: add Rank Math & Dashboard & Indexing API subpages
        if ( ! class_exists( 'RankMath' ) ) {
            $this->dashboard_menu_hook_suffix = add_menu_page( 'Rank Math', 'Rank Math', apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-dashboard', null, 'dashicons-chart-area', 76 );
            $this->dashboard_menu_hook_suffix = add_submenu_page( 'rm-giapi-dashboard', 'Rank Math', __( 'Dashboard', 'rm-giapi' ), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-dashboard', array( $this, 'show_dashboard' ), 'none', 76 );
            $this->console_menu_hook_suffix = add_submenu_page( 'rm-giapi-dashboard', __( 'Google Indexing API', 'rm-giapi' ), __( 'Indexing API Console', 'rm-giapi'), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-console', array( $this, 'show_console' ) );
            $this->settings_menu_hook_suffix = add_submenu_page( 'rm-giapi-dashboard', __( 'Rank Math Indexing API Settings', 'rm-giapi' ), __( 'Indexing API Settings', 'rm-giapi'), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-settings', array( $this, 'show_settings' ) );
            return;
        }

        // If Rank Math is installed: add module control + settings & console pages
        $this->console_menu_hook_suffix = add_submenu_page( 'rank-math', __( 'Google Indexing API', 'rm-giapi' ), __( 'Indexing API Console', 'rm-giapi'), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-console', array( $this, 'show_console' ) );
        $this->settings_menu_hook_suffix = add_submenu_page( 'rank-math', __( 'Rank Math Indexing API Settings', 'rm-giapi' ), __( 'Indexing API Settings', 'rm-giapi'), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-settings', array( $this, 'show_settings' ) );
    }
    
    public function show_console() {
        $limits = $this->get_limits();
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>

            <?php 
            if ( ! $this->get_setting( 'json_key' ) ) { 
                ?>
                <p class="description"><?php printf( __( 'Please navigate to the %s page to configure the plugin.', 'rm-giapi' ), '<a href="' . admin_url( 'admin.php?page=rm-giapi-settings' ) . '">' . __('Indexing API Settings', 'rm-giapi' ) . '</a>' ); ?></p>
                <?php 
                return;
            } 
            ?>

            <div class="giapi-limits">
                <p class="" style="line-height: 1.8"><a href="https://developers.google.com/search/apis/indexing-api/v3/quota-pricing" target="_blank"><strong><?php _e('API Limits:', 'rm-giapi'); ?></strong></a><br>
                <code>PublishRequestsPerDayPerProject = <strong id="giapi-limit-publishperday"><?php echo $limits['publishperday']; ?></strong></code><br>
                <code>RequestsPerMinutePerProject = <strong id="giapi-limit-permin"><?php echo $limits['permin']; ?></strong></code><br>
                <code>MetadataRequestsPerMinutePerProject = <strong id="giapi-limit-metapermin"><?php echo $limits['metapermin']; ?></strong></code></p>
            </div>

            <form id="rm-giapi" class="wpform" method="post">
                <label for="giapi-url"><?php _e('URLs (one per line, up to 100):', 'rm-giapi'); ?></label><br>
                <textarea name="url" id="giapi-url" class="regular-text code" style="min-width: 600px;" rows="5"><?php echo esc_textarea( home_url( '/' ) ); ?></textarea>
                <br><br>
                <label><?php _e('Action:', 'rm-giapi'); ?></label><br>
                <label><input type="radio" name="api_action" value="update" checked="checked" class="giapi-action"> <?php _e('Publish/update', 'rm-giapi'); ?></label><br>
                <label><input type="radio" name="api_action" value="remove" class="giapi-action"> <?php _e('Remove', 'rm-giapi'); ?></label><br>
                <label><input type="radio" name="api_action" value="getstatus" class="giapi-action"> <?php _e('Get status', 'rm-giapi'); ?></label><br><br>
                <input type="submit" id="giapi-submit" class="button button-primary" value="<?php esc_attr_e('Send to API', 'rm-giapi'); ?>">
            </form>
            <div id="giapi-response-userfriendly" class="not-ready">
                <br>
                <hr>
                <div class="response-box">
                    <code class="response-id"></code>
                    <h4 class="response-status"></h4>
                    <p class="response-message"></p>
                </div>
                <a href="#" id="giapi-response-trigger" class="button button-secondary"><?php _e( 'Show Raw Response', 'rm-giapi' ); ?> <span class="dashicons dashicons-arrow-down-alt2" style="margin-top: 3px;"></span></a>
            </div>
            <div id="giapi-response-wrapper">
                <br>
                <textarea id="giapi-response" class="large-text code" rows="10" placeholder="<?php esc_attr_e('Response...', 'rm-giapi'); ?>"></textarea>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var $responseTextarea = $('#giapi-response');
                var $submitButton = $('#giapi-submit');
                var $urlField = $('#giapi-url');
                var $actionRadio = $('.giapi-action');
                var $ufResponse = $('#giapi-response-userfriendly');
                var logResponse = function( info, url ) {
                    var d = new Date();
                    var n = d.toLocaleTimeString();
                    var urls = $urlField.val().split('\n').filter(Boolean);
                    var urls_str = urls[0];
                    var is_batch = false;
                    var action = $actionRadio.filter(':checked').val();
                    if ( urls.length > 1 ) {
                        urls_str = '(batch)';
                        is_batch = true;
                    }

                    $ufResponse.removeClass('not-ready fail success').addClass('ready').find('.response-id').html('<strong>' + action + '</strong>' + ' ' + urls_str);
                    if ( ! is_batch ) {
                        if ( typeof info.error !== 'undefined' ) {
                            $ufResponse.addClass('fail').find('.response-status').text('<?php echo esc_js( __( 'Error', 'rm-giapi' ) ); ?> '+info.error.code).siblings('.response-message').text(info.error.message);
                        } else {
                            var base = info;
                            if ( typeof info.urlNotificationMetadata != 'undefined' ) {
                                base = info.urlNotificationMetadata;
                            }
                            var d = new Date(base.latestUpdate.notifyTime);
                            $ufResponse.addClass('success').find('.response-status').text('<?php echo esc_js( __( 'Success', 'rm-giapi' ) ); ?> ').siblings('.response-message').text('<?php echo esc_js( __( 'Last updated ', 'rm-giapi' ) ); ?> ' + d.toString());
                        }
                    } else {
                        $ufResponse.addClass('success').find('.response-status').text('<?php echo esc_js( __( 'Success', 'rm-giapi' ) ); ?> ').siblings('.response-message').text('<?php echo esc_js( __( 'See response for details.', 'rm-giapi' ) ); ?>');
                        $.each(info, function(index, val) {
                            if ( typeof val.error !== 'undefined' ) {
                                $ufResponse.addClass('fail').find('.response-status').text('<?php echo esc_js( __( 'Error', 'rm-giapi' ) ); ?> '+val.error.code).siblings('.response-message').text(val.error.message);
                            }
                        });
                    }

                    var rawdata = n + " " + action + " " + urls_str + "\n" + JSON.stringify(info, null, 2) + "\n" + "-".repeat(56);
                    var current = $responseTextarea.val();
                    $responseTextarea.val(rawdata + "\n" + current);
                };

                $('#giapi-response-trigger').click(function(e) {
                    e.preventDefault();
                    $(this).find('.dashicons').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2')
                    $('#giapi-response-wrapper').toggle();
                });

                $('#rm-giapi').submit(function(event) {
                    event.preventDefault();
                    $submitButton.attr('disabled', 'disabled');
                    var input_url = $urlField.val();
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: { action: 'rm_giapi', url: input_url, api_action: $actionRadio.filter(':checked').val() },
                    }).always(function(data) {
                        logResponse( data, input_url );
                        $submitButton.removeAttr('disabled');
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            dataType: 'json',
                            data: { action: 'rm_giapi_limits' },
                        })
                        .done(function( data ) {
                            $.each( data, function(index, val) {
                                 $('#giapi-limit-'+index).text(val);
                            });
                        });
                        
                    });
                    
                });

                <?php if ( ! empty( $_GET['apiaction'] ) && ! empty( $_GET['apiurl'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'giapi-action' ) ) { ?>
                    $('#giapi-url').val('<?php echo esc_url_raw( $_GET['apiurl'] ); ?>');
                    $('#rm-giapi').find('input.giapi-action[value="<?php echo sanitize_title( $_GET['apiaction'] ); ?>"]').prop('checked', true);
                    $('#rm-giapi').submit();
                <?php } ?>
            });        
        </script>
        <?php

    }

    function admin_enqueue_scripts( $hook_suffix ) {
        if ( $hook_suffix == $this->dashboard_menu_hook_suffix ) {
            wp_enqueue_script( 'updates' );
            wp_enqueue_style( 'rm-giapi-dashboard', plugin_dir_url( __FILE__ ) . 'dashboard.css' );
        } elseif ( $hook_suffix == $this->console_menu_hook_suffix ) {
            wp_enqueue_style( 'rm-giapi-console', plugin_dir_url( __FILE__ ) . 'console.css' );
        } elseif ( $hook_suffix == $this->settings_menu_hook_suffix ) {
            wp_enqueue_style( 'rm-giapi-settings', plugin_dir_url( __FILE__ ) . 'settings.css' );
        }
    }

    public function show_settings() {
        ?>
        <div class="wrap rank-math-wrap">
            <h1><?php _e('Indexing API Settings', 'rm-giapi' ); ?></h1>
            <form enctype="multipart/form-data" method="POST" action="">
                <?php wp_nonce_field( 'giapi-save', '_wpnonce', true, true ); ?>
                <table class="form-table">
                  <tr valign="top">
                        <th scope="row">
                            <?php _e('JSON Key:', 'rm-giapi' ); ?>
                            <p class="description"><?php _e('Upload the Service Account JSON key file you obtained from Google API Console or paste its contents in the field.', 'rm-giapi' ); ?></p>
                            <div style="display: inline-block; border: 1px solid #ccc; background: #fafafa; padding: 10px 10px 10px 6px; margin-top: 8px;"><span class="dashicons dashicons-editor-help"></span> <a href="<?php echo $this->setup_guide_url; ?>" target="_blank"><?php _e('Read our setup guide', 'rm-giapi' ); ?></a></div>
                        </th>
                        <td>
                            <?php if ( file_exists( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ) { ?>
                                <textarea name="giapi_settings[json_key]" class="large-text" rows="8" readonly="readonly"><?php echo esc_textarea( file_get_contents( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ); ?></textarea>
                                <br>
                                <p class="description"><?php _e('<code>rank-math-835b6feb842b.json</code> file found in the plugin folder. You cannot change the JSON key from here until you delete or remame this file.', 'rm-giapi' ); ?></p>
                            <?php } else { ?>
                                <textarea name="giapi_settings[json_key]" class="large-text" rows="8"><?php echo esc_textarea( $this->get_setting( 'json_key' ) ); ?></textarea>
                                <br>
                                <label>
                                    <?php _e('Or upload JSON file: ', 'rm-giapi' ); ?>
                                    <input type="file" name="json_file" />
                                </label>
                            <?php } ?>
                        </td>
                  </tr>
                  <tr valign="top">
                        <th scope="row">
                            <?php _e('Post Types:', 'rm-giapi' ); ?>
                            <p class="description"><?php _e('Submit posts from these post types automatically in the background when a post is published, edited, or deleted. Also adds action links to submit manually.', 'rm-giapi' ); ?></p>
                        </th>
                      <td><?php $this->post_types_checkboxes(); ?></td>
                  </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    function save_settings() {
        if ( ! isset( $_POST['giapi_settings'] ) ) {
            return;
        }
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'giapi-save' ) ) {
            return;
        }
        if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
            return;
        }

        $json = stripslashes( $_POST['giapi_settings']['json_key'] );
        if ( isset( $_FILES['json_file'] ) && isset( $_FILES['json_file']['tmp_name'] ) && file_exists( $_FILES['json_file']['tmp_name'] ) ) {
            $json = file_get_contents( $_FILES['json_file']['tmp_name'] );
        }

        $post_types = (array) $_POST['giapi_settings']['post_types'];

        update_option( 'giapi_settings', array( 'json_key' => $json, 'post_types' => $post_types ) );
        $this->add_notice( __('Settings updated.', 'rm-giapi' ), 'notice-success' );
    }

    function add_notice( $message, $class = '', $show_on = null ) {
        $this->notices[] = array( 'message' => $message, 'class' => $class, 'show_on' => $show_on );
    }

    function display_notices() {
        $screen = get_current_screen();
        foreach ( $this->notices as $notice ) {
            if ( ! empty( $notice['show_on'] ) && is_array( $notice['show_on'] ) && ! in_array( $screen->id, $notice['show_on'] ) ) {
                return;
            }
            $class = 'notice rm-giapi-notice ' . $notice['class'];
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $notice['message'] ); 
        }
    }

    public function post_types_checkboxes() {
        $settings = $this->get_setting( 'post_types', array() );
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        foreach ( $post_types as $post_type ) {
            ?>
            <input type="hidden" name="giapi_settings[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="0">
            <label><input type="checkbox" name="giapi_settings[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="1" <?php checked( ! empty( $settings[$post_type->name] ) ); ?>> <?php echo $post_type->label; ?></label><br>
            <?php
        }
    }

    public function get_setting( $setting, $default = null ) {
        $defaults = array(
            'json_key' => '',
            'post_types' => array( 'post' => 1, 'page' => 1 ),
        );
        $settings = get_option( 'giapi_settings', array() );
        $settings = array_merge( $defaults, $settings );

        if ( $setting == 'json_key' ) {
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ) {
                return file_get_contents( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' );
            }
        }

        return ( isset( $settings[$setting] ) ? $settings[$setting] : $default );
    }

    public function show_dashboard() {
        ?>
        <div class="rank-math-page">
        <div class="wrap rank-math-wrap">

    <span class="wp-header-end"></span>

    <h1><?php _e('Welcome to Rank Math!', 'rm-giapi' ); ?></h1>

    <div class="rank-math-text">
        <?php _e('The most complete WordPress SEO plugin to convert your website into a traffic generating machine.', 'rm-giapi' ); ?>
    </div>


            <h2 class="nav-tab-wrapper">
                        <a class="nav-tab nav-tab-active" href="#" title="<?php esc_attr_e('Modules', 'rm-giapi' ); ?>"><?php _e('Modules', 'rm-giapi' ); ?></a>
                        <a class="nav-tab" href="#" title="<?php esc_attr_e('Setup Wizard', 'rm-giapi' ); ?>"><?php _e('Setup Wizard', 'rm-giapi' ); ?></a>
                        <a class="nav-tab" href="#" title="<?php esc_attr_e('Import &amp; Export', 'rm-giapi' ); ?>"><?php _e('Import &amp; Export', 'rm-giapi' ); ?></a>
                    </h2>
        
            <div class="rank-math-ui module-listing">

            <div class="two-col">
                            <div class="col">
                    <div class="rank-math-box active">

                        <span class="dashicons dashicons-admin-site-alt3"></span>

                        <header>
                            <h3><?php _e('Indexing API (Beta)', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Directly notify Google when pages are added, updated or removed. The Indexing API supports pages with either job posting or livestream structured data.', 'rm-giapi' ); ?> <a href="<?php echo $this->setup_guide_url; ?>" target="_blank"><?php _e('Read our setup guide', 'rm-giapi' ); ?></a></em></p>

                                                            <a class="module-settings" href="<?php echo admin_url( 'admin.php?page=rm-giapi-settings' ); ?>"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-indexing-api" name="modules[]" value="indexing-api" checked="checked" readonly="readonly">
                                <label for="module-indexing-api" class="indexing-api-label">
                                    <?php _e('Toggle', 'rm-giapi' ); ?>
                                </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="rank-math-box ">

                        <span class="dashicons dashicons-dismiss"></span>

                        <header>
                            <h3><?php _e('404 Monitor', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Records the URLs on which visitors &amp; search engines run into 404 Errors. You can also turn on Redirections to redirect the error causing URLs to other URLs.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-404-monitor" name="modules[]" value="404-monitor">
                                <label for="module-404-monitor" class="">
                                    <?php _e('Toggle', 'rm-giapi' ); ?>
                                </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="rank-math-box ">

                        <span class="dashicons dashicons-smartphone"></span>

                        <header>
                            <h3><?php _e('AMP', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Install the AMP plugin from WordPress.org to make Rank Math work with Accelerated Mobile Pages. It is required because AMP are different than WordPress pages and our plugin doesn\'t work with them out-of-the-box.', 'rm-giapi' ); ?></em></p>

                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
                                <label for="module-amp" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                 </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status', 'rm-giapi' ); ?>:                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box ">

                        <span class="dashicons dashicons-cart"></span>

                        <header>
                            <h3><?php _e('bbPress', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Add required meta tags on bbPress pages.', 'rm-giapi' ); ?></em></p>

                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-bbpress" name="modules[]" value="bbpress" disabled="disabled">
                                <label for="module-bbpress" class="rank-math-tooltip"><?php _e('Toggle', 'rm-giapi' ); ?>                                    <span><?php _e('Please activate bbPress plugin to use this module.', 'rm-giapi' ); ?></span>                             </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-admin-links"></span>

                        <header>
                            <h3><?php _e('Link Counter', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Counts the total number of internal, external links, to and from links inside your posts.', 'rm-giapi' ); ?></em></p>

                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-link-counter" name="modules[]" value="link-counter">
                                <label for="module-link-counter" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                    </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-location-alt"></span>

                        <header>
                            <h3><?php _e('Local SEO &amp; Google Knowledge Graph', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Dominate the search results for local audience by optimizing your website and posts using this Rank Math module.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-local-seo" name="modules[]" value="local-seo">
                                <label for="module-local-seo" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                   </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-randomize"></span>

                        <header>
                            <h3><?php _e('Redirections', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Redirect non-existent content easily with 301 and 302 status code. This can help reduce errors and improve your site ranking.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-redirections" name="modules[]" value="redirections">
                                <label for="module-redirections" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                    </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-awards"></span>

                        <header>
                            <h3><?php _e('Rich Snippets', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Enable support for the Rich Snippets, which adds metadata to your website, resulting in rich search results and more traffic.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-rich-snippet" name="modules[]" value="rich-snippet">
                                <label for="module-rich-snippet" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                    </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-admin-users"></span>

                        <header>
                            <h3><?php _e('Role Manager', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('The Role Manager allows you to use internal WordPress\' roles to control which of your site admins can change Rank Math\'s settings', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-role-manager" name="modules[]" value="role-manager">
                                <label for="module-role-manager" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                    </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-search"></span>

                        <header>
                            <h3><?php _e('Search Console', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Connect Rank Math with Google Search Console to see the most important information from Google directly in your WordPress dashboard.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-search-console" name="modules[]" value="search-console">
                                <label for="module-search-console" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                  </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-chart-bar"></span>

                        <header>
                            <h3><?php _e('SEO Analysis', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Let Rank Math analyze your website and your website\'s content using 70+ different tests to provide tailor-made SEO Analysis to you.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-seo-analysis" name="modules[]" value="seo-analysis">
                                <label for="module-seo-analysis" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                    </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-networking"></span>

                        <header>
                            <h3><?php _e('Sitemap', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('Enable Rank Math\'s sitemap feature, which helps search engines index your website\'s content effectively.', 'rm-giapi' ); ?></em></p>

                                                            <a class="module-settings" href="#"><?php _e('Settings', 'rm-giapi' ); ?></a>
                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-sitemap" name="modules[]" value="sitemap">
                                <label for="module-sitemap" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                                                 </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="rank-math-box">

                        <span class="dashicons dashicons-cart"></span>

                        <header>
                            <h3><?php _e('WooCommerce', 'rm-giapi' ); ?></h3>

                            <p><em><?php _e('WooCommerce module to use Rank Math to optimize WooCommerce Product Pages.', 'rm-giapi' ); ?></em></p>

                            
                        </header>
                        <div class="status wp-clearfix">
                            <span class="rank-math-switch">
                                <input type="checkbox" class="rank-math-modules" id="module-woocommerce" name="modules[]" value="woocommerce">
                                <label for="module-woocommerce" class=""><?php _e('Toggle', 'rm-giapi' ); ?>                                 <span><?php _e('Please activate WooCommerce plugin to use this module.', 'rm-giapi' ); ?></span>                             </label>
                                <span class="input-loading"></span>
                            </span>
                            <label>
                                <?php _e('Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e('Active', 'rm-giapi' ); ?> </span>
                                <span class="module-status inactive-text"><?php _e('Inactive', 'rm-giapi' ); ?> </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
        </div>

        <?php 
        if ( file_exists( WP_PLUGIN_DIR . '/seo-by-rank-math' ) ) {
            $text         = __( 'Activate Now', 'schema-markup' );
            $path         = 'seo-by-rank-math/rank-math.php';
            $link         = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $path ), 'activate-plugin_' . $path );
            $button_class = 'activate-now';
        } else {
            $text         = __( 'Install for Free', 'schema-markup' );
            $link         = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=seo-by-rank-math' ), 'install-plugin_seo-by-rank-math' );
            $button_class = 'install-now';
        }

         ?>
        <div class="rank-math-feedback-modal rank-math-ui try-rankmath-panel" id="rank-math-feedback-form">
            <div class="rank-math-feedback-content">

                <?php /*<header>
                    <h2>
                        <?php echo __( 'Rank Math SEO Suite', '404-monitor' ); ?>
                    </h2>
                </header> */ ?>

                <div class="plugin-card plugin-card-seo-by-rank-math">
                    <span class="button-close dashicons dashicons-no-alt alignright"></span>
                    <div class="plugin-card-top">
                        <div class="name column-name">
                            <h3>
                                <a href="https://rankmath.com/wordpress/plugin/seo-suite/" target="_blank">
                                <?php esc_html_e( 'WordPress SEO Plugin – Rank Math', '404-monitor' ); ?>
                                <img src="https://ps.w.org/seo-by-rank-math/assets/icon.svg" class="plugin-icon" alt="<?php esc_html_e( 'Rank Math SEO', '404-monitor' ); ?>">
                                </a>
                                <span class="vers column-rating">
                                    <a href="https://wordpress.org/support/plugin/seo-by-rank-math/reviews/" target="_blank">
                                        <div class="star-rating">
                                            <div class="star star-full" aria-hidden="true"></div>
                                            <div class="star star-full" aria-hidden="true"></div>
                                            <div class="star star-full" aria-hidden="true"></div>
                                            <div class="star star-full" aria-hidden="true"></div>
                                            <div class="star star-full" aria-hidden="true"></div>
                                        </div>
                                        <span class="num-ratings" aria-hidden="true">(195)</span>
                                    </a>
                                </span>
                            </h3>
                        </div>

                        <div class="desc column-description">
                            <p><?php esc_html_e( 'Rank Math is a revolutionary SEO plugin that combines the features of many SEO tools in a single package & helps you multiply your traffic.', '404-monitor' ); ?></p>
                        </div>
                    </div>

                    <div class="plugin-card-bottom">
                        <div class="column-compatibility">
                            <span class="compatibility-compatible"><strong><?php esc_html_e( 'Compatible', '404-monitor' ); ?></strong> <?php esc_html_e( 'with your version of WordPress', '404-monitor' ); ?></span>
                        </div>
                        <a href="<?php echo $link; ?>" class="button button-primary <?php echo $button_class; ?>" data-slug="seo-by-rank-math" data-name="Rank Math"><?php echo $text; ?></a>
                    </div>
                </div>

            </div>

        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var dialog         = $( '#rank-math-feedback-form' )

                dialog.on( 'click', '.button-close', function( event ) {
                    event.preventDefault()
                    dialog.fadeOut()
                })

                // Enable/Disable Modules
                $( '.module-listing .rank-math-box:not(.active)' ).on( 'click', function(e) {
                    e.preventDefault();

                    $( '#rank-math-feedback-form' ).fadeIn();

                    return false;
                });

                $( '#rank-math-feedback-form' ).on( 'click', function( e ) {
                    if ( 'rank-math-feedback-form' === e.target.id ) {
                        $( this ).find( '.button-close' ).trigger( 'click' );
                    }
                });

                $('a.nav-tab').not('.nav-tab-active').click(function(event) {
                    $( '#rank-math-feedback-form' ).fadeIn();
                });

                // Install & Activate Rank Math from modal.
                var tryRankmathPanel = $( '.try-rankmath-panel' ),
                    installRankmathSuccess;

                installRankmathSuccess = function( response ) {
                    response.activateUrl += '&from=schema-try-rankmath';
                    response.activateLabel = wp.updates.l10n.activatePluginLabel.replace( '%s', response.pluginName );
                    tryRankmathPanel.find('.install-now').text('Activating...');
                    window.location.href = response.activateUrl;
                };

                tryRankmathPanel.on( 'click', '.install-now', function( e ) {
                    e.preventDefault();
                    var args = {
                            slug: $( e.target ).data( 'slug' ),
                            success: installRankmathSuccess
                    };
                    wp.updates.installPlugin( args );
                } );
            
            });
        </script>
        <?php
    }


    function add_rm_module( $modules ) {
        $modules['indexing-api'] = array(
            'id'            => 'indexing-api',
            'title'         => esc_html__( 'Google Indexing API (Beta)', 'rank-math' ),
            'desc'          => esc_html__( 'Directly notify Google when pages are added, updated or removed. The Indexing API supports pages with either job posting or livestream structured data.', 'rank-math' ) . ' <a href="' . $this->setup_guide_url . '" target="_blank">' . __('Read our setup guide', 'rm-giapi' ) . '</a>',
            'class'         => 'RM_GIAPI_Module',
            'icon'          => 'dashicons-admin-site-alt3',
            'settings_link' => admin_url( 'admin.php?page=rm-giapi-settings' ),
        );
        return $modules;
    }

    function admin_footer( $hook_suffix ) {
        $screen = get_current_screen();
        if ( $screen->id != 'toplevel_page_rank-math' ) {
            return;
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#module-indexing-api')
                    .prop('checked', true)
                    .prop('readonly', true)
                    .closest('.rank-math-switch')
                    .css({opacity: 0.7})
                    .closest('div.status')
                    .css({pointerEvents: 'none'})
                    .find('.active-text')
                    .text('<?php echo esc_js( __('Active (Plugin)', 'rm-giapi' ) ); ?>')
                    .closest('.rank-math-box')
                    .addClass('active');
            });
        </script>
        <?php
    }

    function rm_missing_admin_notice_error() {
        if ( class_exists( 'RankMath' ) ) {
            return;
        }

        $message = sprintf(__( 'It is recommended to use %s along with the Indexing API plugin.', 'sample-text-domain' ), '<a href="https://wordpress.org/plugins/seo-by-rank-math/" target="_blank">'.__('Rank Math SEO').'</a>');
        $class = 'notice-error';
        $show_on = array( 'rank-math_page_rm-giapi-console', 'rank-math_page_rm-giapi-settings', 'rank-math_page_rm-giapi-dashboard' );

        $this->add_notice( $message, $class, $show_on );
    }

    function publish_post( $post_id ) {
        $post = get_post( $post_id );
        if ( $post->post_status == 'publish' ) {
            $this->send_to_api( get_permalink( $post ), 'update' );
        }
    }

    function delete_post( $post_id ) {
        $post_types = $this->get_setting( 'post_types', array() );
        $post = get_post( $post_id );
        if ( empty( $post_types[$post->post_type] ) ) {
            return;
        }
        $this->send_to_api( get_permalink( $post ), 'delete' );
    }

}

class RM_GIAPI_Module {}

$rm_giapi = new RM_GIAPI();
