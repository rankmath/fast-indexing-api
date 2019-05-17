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

    function __construct() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_ajax_rm_giapi',array( $this,'ajax_rm_giapi' ) );
        add_action( 'wp_ajax_rm_giapi_deauth',array( $this,'ajax_rm_giapi_deauth' ) );
        
        $post_types = apply_filters( 'rmgiapi_post_types', array( 'post', 'page' ) );
        foreach ( $post_types as $pt ) {
            add_filter( $pt.'_row_actions', array( $this, 'send_to_api_link' ), 10, 2 );
        }
        // localization
        add_action( 'plugins_loaded', array( $this, 'mythemeshop_giapi_load_textdomain' ) );
    }

    function send_to_api_link( $actions, $post ) {
        if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
            return $actions;
        }
        $nonce = wp_create_nonce( 'giapi-action' );
        $actions['rmgiapi_update'] = '<a href="' . admin_url( 'tools.php?page=rm-giapi&apiaction=update&_wpnonce='.$nonce.'&apiurl='.rawurlencode( get_permalink( $post) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __('Indexing API: Update', 'rm-giapi') . '</a>';
        $actions['rmgiapi_getstatus'] = '<a href="' . admin_url( 'tools.php?page=rm-giapi&apiaction=getstatus&_wpnonce='.$nonce.'&apiurl='.rawurlencode( get_permalink( $post) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __('Indexing API: Get Status', 'rm-giapi') . '</a>';
        return $actions;
    }
    
    function mythemeshop_giapi_load_textdomain() {
        load_plugin_textdomain( 'mythemeshop-giapi', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' ); 
    }
    
    function ajax_rm_giapi() {
        if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
            die('0');
        }

        include_once 'vendor/autoload.php';
        $this->client = new Google_Client();
        $this->client->setAuthConfig(plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json');
        $this->client->setConfig('base_path', 'https://indexing.googleapis.com');
        $this->client->addScope( 'https://www.googleapis.com/auth/indexing' );

        header("Content-type: application/json");
        $action = sanitize_title( $_POST['api_action'] );
        $url_input = $this->get_input_urls();
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
        wp_send_json( $data );
        exit();
    
    }

    function get_input_urls() {
        return array_values( array_filter( array_map( 'trim', explode( "\n", $_POST['url'] ) ) ) );
    }

    function batch_request( $method, $url ) {

    }

    function admin_menu() {
        // If Rank Math is not installed: add Rank Math > Dashboard & Indexing API subpages

        // If Rank Math is installed: add as module
        $this->menu_hook_suffix = add_management_page(__('Google Indexing API', 'rm-giapi'), __('Indexing API', 'rm-giapi'), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi', array( $this, 'show_ui' ) );
        add_action( 'load-' . $this->menu_hook_suffix , array( $this, 'ui_onload' ) );
    
    }
    
    function admin_init() {        
        
    }

    function admin_enqueue_scripts( $hook_suffix ) {
        if ( ! $hook_suffix == $this->menu_hook_suffix ) {
            return;
        }
    }
    
    public function show_ui() {
        //$data = $this->client->search_console_data();
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>

            <div class="giapi-limits">
                <p class="" style="line-height: 1.8"><a href="https://developers.google.com/search/apis/indexing-api/v3/quota-pricing" target="_blank"><strong><?php _e('API Limits:', 'rm-giapi'); ?></strong></a><br>
                <code>PublishRequestsPerDayPerProject = <strong>200</strong></code><br>
                <code>RequestsPerMinutePerProject = <strong>600</strong></code><br>
                <code>MetadataRequestsPerMinutePerProject = <strong>180</strong></code></p>
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
                    });
                    
                });

                <?php if ( ! empty( $_GET['apiaction'] ) && ! empty( $_GET['apiurl'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'giapi-action' ) ) { ?>
                    $('#giapi-url').val('<?php echo esc_url_raw( $_GET['apiurl'] ); ?>');
                    $('#rm-giapi').find('input.giapi-action[value="<?php echo sanitize_title( $_GET['apiaction'] ); ?>"]').prop('checked', true);
                    $('#rm-giapi').submit();
                <?php } ?>
            });        
        </script>
        <style type="text/css">
            #giapi-response-wrapper, #giapi-response-userfriendly {
                display: none;
            }
            #giapi-response-userfriendly.ready {
                display: block;
            }
            #giapi-response-trigger {

            }
            .giapi-limits {
                width: 420px;
                float: right;
            }
            .response-box {
                background: #fff;
                padding: 18px 20px;
                margin: 20px 0;
                text-align: center;
            }

            .response-box .response-id {}

            .response-box .response-status {
                font-size: 24px;
                line-height: 1.5;
                margin: 0 0 10px 0;
            }

            .response-box .response-message {
                margin: 0;
            }

            .response-box .response-status:after {
                font-family: dashicons;
                font-size: 28px;
                position: relative;
                top: 5px;
            }

            .success .response-status:after {
                content: "\f147";
            }

            .fail .response-status:after {
                content: "\f158";
            }

            .success .response-status {
                color: #156115;
            }

            .fail .response-status {
                color: #bb2b2b;
            }
        </style>
        <?php

    }

    public function ui_onload() {
        
    }

}

$rm_giapi = new RM_GIAPI();
