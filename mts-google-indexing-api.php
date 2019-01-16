<?php
/*
 * Plugin Name: MTS Google Indexing API
 * Plugin URI: https://mythemeshop.com
 * Description: Crawl pages instantly with the indexing API.
 * Version: 1.0
 * Author: MyThemeShop
 * Author URI: https://mythemeshop.com
 * License: GPLv2
 */

defined('ABSPATH') or die;

class MTS_GIAPI {

    function __construct() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_ajax_mts_giapi',array( $this,'ajax_mts_giapi' ) );
        add_action( 'wp_ajax_mts_giapi_deauth',array( $this,'ajax_mts_giapi_deauth' ) );
        
        // localization
        add_action( 'plugins_loaded', array( $this, 'mythemeshop_giapi_load_textdomain' ) );

        include_once 'google-api-php-client/vendor/autoload.php';
        $this->client = new Google_Client();
        $this->client->setAuthConfig(plugin_dir_path( __FILE__ ).'rank-math-835b6feb842b.json');
        $this->client->addScope('https://www.googleapis.com/auth/indexing');
    }
    
    function mythemeshop_giapi_load_textdomain() {
        load_plugin_textdomain( 'mythemeshop-giapi', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' ); 
    }
    
    function ajax_mts_giapi() {
        header("Content-type: application/json");
        $action = sanitize_title( $_POST['api_action'] );
        $url = esc_url( $_POST['url'] );
        
        $req_method = 'post';
        $req_url = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
        $req_body = array( 'url' => $url );
        if ( $action == 'getstatus' ) {
            $req_method = 'get';
            $req_body = null;
            $req_url = 'https://indexing.googleapis.com/v3/urlNotifications/metadata';
            $req_url = add_query_arg( 'url', $url, $req_url );
        } elseif ( $action == 'update' ) {
            $req_body['type'] = 'URL_UPDATED';
        } elseif ( $action == 'remove' ) {
            $req_body['type'] = 'URL_DELETED';
        }

        //$response = $this->client->$req_method( $req_url, $req_body );
        //$response = array( 'method' => $req_method, 'url' => $req_url, 'body' => $req_body );

        $httpClient = $this->client->authorize();
        $content = $req_body ? json_encode( $req_body ) : '';
        $response = $httpClient->$req_method( $req_url, array( 'body' => $content ) );

        $body = $response->getBody()->getContents();
        if ( $body ) {
            $body = json_decode( $body, true );
        }
        $data = array( 'code' => $response->getStatusCode(), 'body' => $body );
        wp_send_json( $data );
        exit;
    }

    function ajax_mts_giapi_auth() {
        $code = isset( $_POST['code'] ) ? trim( wp_unslash( $_POST['code'] ) ) : false;
        if ( ! $code ) {
            wp_send_json( array( 'error' => esc_html__( 'No authentication code found.', 'rank-math' ) ) );
        }
        //$data = $this->client->fetch_access_token( $code );
        wp_send_json( $data );
        exit;
    }

    function ajax_mts_giapi_deauth() {
        //$this->client->disconnect();
        exit();
    }

    function ajax_mts_giapi_get_profiles() {
        //$profiles = $this->client->fetch_profiles();
        if ( empty( $profiles ) ) {
            wp_send_json( array( 'error' => 'No profiles found.' ) );
        }
        wp_send_json( array(
            'profiles' => $profiles,
            'selected' => $this->select_profile( $profiles ),
        ));
        exit;
    }

    function admin_menu() {
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        // Add the new admin menu and page and save the returned hook suffix    
        $this->menu_hook_suffix = add_management_page(__('Google Indexing API', 'mts-giapi'), __('Indexing API', 'mts-giapi'), 'administrator', 'mts-giapi', array( $this, 'show_ui' ) );
        //$this->settings_menu_hook_suffix = add_options_page( __('Google Indexing API', 'mts-giapi'), __('Indexing API', 'mts-giapi'), 'administrator', 'mts-giapi-settings', array( $this, 'show_settings' ) );
        // Use the hook suffix to compose the hook and register an action executed when plugin's options page is loaded
        add_action( 'load-' . $this->menu_hook_suffix , array( $this, 'ui_onload' ) );
    
    }
    

    function admin_init() {        
        
    }

    function admin_enqueue_scripts( $hook_suffix ) {
        
    }
    
    public function show_ui() {
        //$data = $this->client->search_console_data();
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>
            <?php /* if ( ! $data['authorized'] ) { ?>
                <div id="giapi-unauthorized-wrapper">
                    <p><?php _e('You need to authorize access in Settings > Indexing API.', 'mts-giapi'); ?></p>
                </div>

                <?php echo '</div>'; return; ?>
            <?php } */ ?>
            <form id="mts-giapi" class="wpform" method="post">
                <label for="giapi-url"><?php _e('URL:', 'mts-giapi'); ?></label><br>
                <input type="text" name="url" id="giapi-url" class="regular-text code" style="min-width: 600px;" value="<?php echo home_url( '/' ); ?>"><br><br>
                <label><?php _e('Action:', 'mts-giapi'); ?></label><br>
                <label><input type="radio" name="api_action" value="update" checked="checked" class="giapi-action"> <?php _e('Publish/update', 'mts-giapi'); ?></label><br>
                <label><input type="radio" name="api_action" value="remove" class="giapi-action"> <?php _e('Remove', 'mts-giapi'); ?></label><br>
                <label><input type="radio" name="api_action" value="getstatus" class="giapi-action"> <?php _e('Get status', 'mts-giapi'); ?></label><br><br>
                <input type="submit" id="giapi-submit" class="button button-primary" value="<?php esc_attr_e('Send to API', 'mts-giapi'); ?>">
            </form>
            <div style="display: none;" id="giapi-response-wrapper">
                <br><hr><br>
                <textarea id="giapi-response" class="large-text code" rows="10" placeholder="<?php esc_attr_e('Response...', 'mts-giapi'); ?>"></textarea>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var $responseTextarea = $('#giapi-response');
                var $submitButton = $('#giapi-submit');
                var $urlField = $('#giapi-url');
                var $actionRadio = $('.giapi-action');
                var logResponse = function( info ) {
                    var d = new Date();
                    var n = d.toLocaleTimeString();

                    info = n + " " + $actionRadio.filter(':checked').val() + " " + $urlField.val() + " " + info;
                    var current = $responseTextarea.val();
                    $responseTextarea.val(info + "\n" + current);
                };

                $('#mts-giapi').submit(function(event) {
                    event.preventDefault();
                    $submitButton.attr('disabled', 'disabled');
                    $('#giapi-response-wrapper').show();
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: { action: 'mts_giapi', url: $urlField.val(), api_action: $actionRadio.filter(':checked').val() },
                    })
                    .done(function(data) {
                        logResponse(JSON.stringify(data, null, 2));
                    })
                    .fail(function() {
                        logResponse('HTTP Error, check console.');
                    }).always(function() {
                        $submitButton.removeAttr('disabled');
                    });
                    
                });
            });        
        </script>
        <?php

    }

    public function show_settings() {
        if ( isset( $_POST['token'] ) ) {
            $token = $_POST['token'];
            //$this->client->fetch_access_token( $token );
        }
        //$data = $this->client->search_console_data();
        ?>

        <div class="wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>
            <?php if ( $data['authorized'] ) { ?>
                <p><?php _e('Authorized!', 'mts-giapi'); ?></p>
                <?php echo "</div>"; return; ?>
            <?php } ?>
            <form id="mts-giapi-settings" class="wpform" method="post">
                <a href="<?php // echo $this->client->get_console_auth_url(); ?>" id="giapi-submit" class="button button-secondary"><?php _e('Get Code', 'mts-giapi'); ?></a><br><br>
                <label for="giapi-token"><?php _e('Auth Code', 'mts-giapi'); ?></label><br>
                <input type="text" name="token" id="giapi-token" class="regular-text code" style="" value=""> 
                <input type="submit" id="giapi-submit" class="button button-primary" value="<?php esc_attr_e('Authorize', 'mts-giapi'); ?>">
            </form>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#mts-giapi').submit(function(event) {
                    event.preventDefault();

                });
            });        
        </script>
        <?php
    }


    public function ui_onload() {
        
    }

    /**
     * Disconnect client connection.
     */
    public function disconnect() {
        //$this->client->search_console_data( false );
        add_option( 'mtsgiapi_data', array(
            'authorized' => false,
            'profiles'   => array(),
        ) );
        //$this->client->set_data();
    }

}

$mts_giapi = new MTS_GIAPI();
