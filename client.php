<?php

defined('ABSPATH') or die;

class MTS_GIAPI_Client {
    /**
     * Is client authorized the oAuth2.
     *
     * @var boolean
     */
    public $is_authorized = null;

    /**
     * Hold data.
     *
     * @var array
     */
    public $data = array();

    /**
     * Hold selected profile.
     *
     * @var string
     */
    public $profile;

    /**
     * The Constructor.
     */
    public function __construct() {
        $this->set_data();
    }

    /**
     * Make an HTTP GET request - for retrieving data.
     *
     * @param  string $url     URL to do request.
     * @param  array  $args    Assoc array of arguments (usually your data).
     * @param  int    $timeout Timeout limit for request in seconds.
     * @return array|false     Assoc array of API response, decoded from JSON.
     */
    public function get( $url, $args = array(), $timeout = 10 ) {
        return $this->make_request( 'get', $url, $args, $timeout );
    }

    /**
     * Make an HTTP POST request - for creating and updating items.
     *
     * @param  string $url     URL to do request.
     * @param  array  $args    Assoc array of arguments (usually your data).
     * @param  int    $timeout Timeout limit for request in seconds.
     * @return array|false     Assoc array of API response, decoded from JSON.
     */
    public function post( $url, $args = array(), $timeout = 10 ) {
        return $this->make_request( 'post', $url, $args, $timeout );
    }

    /**
     * Make an HTTP PUT request - for creating new items.
     *
     * @param  string $url     URL to do request.
     * @param  array  $args    Assoc array of arguments (usually your data).
     * @param  int    $timeout Timeout limit for request in seconds.
     * @return array|false     Assoc array of API response, decoded from JSON.
     */
    public function put( $url, $args = array(), $timeout = 10 ) {
        return $this->make_request( 'put', $url, $args, $timeout );
    }

    /**
     * Make an HTTP DELETE request - for deleting data.
     *
     * @param  string $url     URL to do request.
     * @param  array  $args    Assoc array of arguments (usually your data).
     * @param  int    $timeout Timeout limit for request in seconds.
     * @return array|false     Assoc array of API response, decoded from JSON.
     */
    public function delete( $url, $args = array(), $timeout = 10 ) {
        return $this->make_request( 'delete', $url, $args, $timeout );
    }

    /**
     * Performs the underlying HTTP request. Not very exciting.
     *
     * @param string $http_verb The HTTP verb to use: get, post, put, patch, delete.
     * @param string $url       URL to do request.
     * @param array  $args       Assoc array of parameters to be passed.
     * @param int    $timeout    Timeout limit for request in seconds.
     * @return array|false Assoc array of decoded result.
     */
    private function make_request( $http_verb, $url, $args = array(), $timeout = 10 ) {
        if ( ! isset( $this->data['access_token'] ) ) {
            return false;
        }
        $http_verb = strtolower( $http_verb );
        $params = array(
            'timeout' => $timeout,
            'method'  => $http_verb,
            'headers' => array( 'Authorization' => 'Bearer ' . $this->data['access_token'] ),
        );
        if ( 'delete' === $http_verb || 'put' === $http_verb ) {
            $params['headers']['Content-Length'] = '0';
        } elseif ( 'post' === $http_verb && ! empty( $args ) && is_array( $args ) ) {
            $params['body']                    = wp_json_encode( $args, JSON_UNESCAPED_SLASHES );
            $params['headers']['Content-Type'] = 'application/json';
//echo $url.' - '.print_r($params);die();
        }
        $response = wp_remote_request( $url, $params );
        return $this->process_response( $response );
    }

    /**
     * Process api response.
     *
     * @param  array $response Api response array.
     * @return array
     */
    public function process_response( $response ) {
        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $body = wp_remote_retrieve_body( $response );
            $headers = wp_remote_retrieve_headers( $response );
            if ( ! empty( $body ) ) {
                $body = json_decode( $body, true );
            }

            if ( 200 === $code || 204 === $code ) {
                return array(
                    'status' => 'success',
                    'code'   => '200',
                    'body'   => $body,
                );
            }

            if ( isset( $body['error_description'] ) && 'Bad Request' === $body['error_description'] ) {
                $body['error_description'] = esc_html__( 'Bad request. Please check the code.', 'mts-giapi' );
            }

            return array(
                'status' => 'fail',
                'code'   => $code,
                'body'   => $body,
                'headers'=> $headers,
            );
        }

        return array(
            'status' => 'fail',
            'code'   => $response->get_error_code(),
            'body'   => array( 'error_description' => 'WP_Error: ' . $response->get_error_message() ),
        );
    }

    /**
     * Fetch profiles api wrapper.
     *
     * @return array
     */
    public function fetch_profiles() {
        $profiles = array();

        if ( ! $this->is_authorized ) {
            return $profiles;
        }

        $response = $this->get( 'https://www.googleapis.com/webmasters/v3/sites' );
        if ( 'success' === $response['status'] ) {
            foreach ( $response['body']['siteEntry'] as $site ) {
                $profiles[ $site['siteUrl'] ] = $site['siteUrl'];
            }
            $this->search_console_data( array(
                'profiles' => $profiles,
            ));
        } else {
            $this->error_notice( $response, true );
        }

        return $profiles;
    }

    /**
     * Get/Update search console data.
     *
     * @param  bool|array $data Data to save.
     * @return bool|array
     */
    public static function search_console_data( $data = null ) {
        $key = 'mtsgiapi_data';
        if ( false === $data ) {
            delete_option( $key );
            return false;
        }
        $saved = get_option( $key, array() );
        if ( is_null( $data ) ) {
            return wp_parse_args( $saved, array(
                'authorized' => false,
                'profiles'   => array(),
            ) );
        }
        $data = wp_parse_args( $data, $saved );
        update_option( $key, $data );
        return $data;
    }

    /**
     * Get search console api config.
     *
     * @return array
     */
    public static function get_console_api_config() {
        /*return array(
            'application_name' => 'Rank Math',
            'client_id'        => '521003500769-8d6470bsfup1am315t7f77fq3stqa95e.apps.googleusercontent.com',
            'client_secret'    => 'ljpahQGhbD066VCRArEWeMme',
            'redirect_uri'     => 'urn:ietf:wg:oauth:2.0:oob',
            'scopes'           => array( 'https://www.googleapis.com/auth/indexing' ),
            'token_url'        => 'https://accounts.google.com/o/oauth2/token',
            'auth_url'         => 'https://accounts.google.com/o/oauth2/auth',
            'signon_certs_rl'  => 'https://www.googleapis.com/oauth2/v1/certs',
            'revoke_url'       => 'https://accounts.google.com/o/oauth2/revoke',
        );*/
        return array(
   'type' => 'service_account',
   'project_id' => 'rank-math',
   'private_key_id' => '835b6feb842bd54f45f33f251c4654384e83d0be',
   'private_key' => '-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCtlaoeZ3CnIXat
iestytJFRZkr+uoXxYEtcYb5iM6FVzLC9WIjiuv//rh0sbkBxqnj1AUME8eF8XJo
FLcqavHidRIYAZS0Zl5NNs3eq5Smq2xk688baFrlZG4two+pqSNxV+wQ5yImbuQx
bCQsyubzPNYgv0X1g1l1Id9M2U2QCGDmmsShw4Gtvjo+7M0bpY3Jgc8QzABqaOYz
YMqzNvhEWEsB1LVk3Dun19E8V4xhcBfYuOM4me1QxKoMZOH7Hxn4hz4xJe3x4upG
Hwt/pb4xCVNv55bjp3kyTw340EsfQ+KwDdbMKQGlvf8Z/EAVXAlYvMYQD22PO466
PtSbmhfdAgMBAAECggEAAQJWu9iEGiSiqP2dRWXhdQ+jhVLvG3ZFevsm+rpl4PaR
z/pXOLetCY70iZEi7zS1diKcaOaQiWHO9XXxXqjCgTuHAGa585aIw9AmwD2lHpbu
iWOpj5I6vQypa1CvPyBBmX4WXD7Lvwd0AimLyErhrtZStFwITxYVZg3tW2gEAGtZ
mWXeo/xUR/+uOmXJ1bTSfLDED7xNoLLw6QYgWK4HClnAiEusrABqvcFYchdltBTm
GmHPESlIi66JxW4RANjDzaGrGCN4yLavWbc8CddDi0/6kCfHpiqbr5c4CJrt0j1r
GWPNrAWfboE4LJslUlDxepUlYdASc2gfrCbYD436bQKBgQDTMvcg53FuHa+XV9tS
0w08hbg770nb13dmKQu/itC2YFEihEf1PF9Bv3Y/Fu6jYEB0PV9uVukkxOOetb21
Ov2WCET+pML858d9QurrLWFp0Vf8SSVH7GGVczdfDiUaHHbu1Ygqu5bYX9d+D/c1
t7aE94ndwUqhUV0XQdj1oEwn+wKBgQDSaBHO09+r3gZrwMIJEbp0iCcjQYPqHaXW
EbLc7QUEfglApbnKdFrhmmFvlbmtjeI+oXS0D9E7d6pQkTgEfnBdX3rfmBnBNXdv
blEvDYdDME6nBO7JSmrxuRv3mhBdPnCsFmNc5V7Kcns2kuAiPo97SpsUAol9+kqX
iYVO7H8ABwKBgCDgfCv5G2VC5cSvforkI9laD5X06BB6+DFFDnkgyOC7GaY/5Vu9
rC51+ZhUn417PE1cCHEKwnxg0fYw4HlH15X1b5gcWeY4xosvUZaOl+17EzFIya7q
kz5Kk0IO16O67qC7SNkuHotMUCVlotwP3y/PwPxuTInUOqOWMon6DCDlAoGAKmxt
WsSBjse/h65f8TAv5hsluQgWrve3XkhHRiBMKLG47936bgWz5VUZXERWIdY/zd0E
COI/j7v7DS+amd1cjpcX0Ul7t+ct47vchp41rahp02c9NjzY/1ARgYUA06wpT6lb
83kT+cHHciN/Kahvia5rQkeYrVvv+knyJQ2uNbUCgYAhC2IHZ+BnA2lD/NYwT9Fy
E1BQbYFH8yWY9ltM7gtu9JsklVGXEfebCqiehTEVoxj0DmUmDHKwVVIZ6tONWf4h
WL1B1qeMP5s7RNpYUHs/A7/GSFabRNoWTLsNfIC4kgg6+GS8pWeXbRQXFQCqXCqA
/uxi9ICJfhRhK7SAtAsDDg==
-----END PRIVATE KEY-----
',
    'client_id' => '114363778534361415626',
    'client_email' => 'indexing-api-service-account@rank-math.iam.gserviceaccount.com',
    'auth_url' => 'https://accounts.google.com/o/oauth2/auth',
    'token_url' => 'https://oauth2.googleapis.com/token',
    'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
    'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/indexing-api-service-account%40rank-math.iam.gserviceaccount.com',
    'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob',
    'scopes'           => array( 'https://www.googleapis.com/auth/indexing' ),
);
    }

    /**
     * Get auth url.
     *
     * @return string
     */
    public static function get_console_auth_url() {
        $config = self::get_console_api_config();
        $params = array(
            'response_type'     => 'code',
            'client_id'         => $config['client_id'],
            'client_email'      => $config['client_email'],
            //'signing_key'       => $config['private_key'],
            //'signing_algorithm' => $config['HS256'],
            //'redirect_uri'      => $config['redirect_uri'],
            'scope'             => implode( ' ', $config['scopes'] ),
        );
        return add_query_arg( $params, $config['auth_url'] );
    }

    /**
     * Fetch access token
     *
     * @param string $code oAuth token.
     * @return array
     */
    public function fetch_access_token( $code ) {
        $config = $this->get_console_api_config();

        $response = wp_remote_post( $config['token_url'], array(
            'body'    => array(
                'code'          => $code,
                'client_id'         => $config['client_id'],
                'client_email'      => $config['client_email'],
                'signing_key'       => $config['private_key'],
                'signing_algorithm' => $config['HS256'],
                'redirect_uri'      => $config['redirect_uri'],
                'grant_type'    => 'authorization_code',
            ),
            'timeout' => 15,
        ) );

        $data = $this->process_response( $response );
        if ( 'success' === $data['status'] ) {
            $this->search_console_data( array(
                'authorized'    => true,
                'expire'        => time() + $data['body']['expires_in'],
                'access_token'  => $data['body']['access_token'],
                'refresh_token' => $data['body']['refresh_token'],
            ));
        }

        $this->set_data();

        return $data;
    }

    /**
     * Maybe we need to refresh the token before processing api request.
     */
    public function maybe_refresh_token() {
        if ( ! isset( $this->data['expire'] ) ) {
            return;
        }

        $expire = $this->data['expire'];

        // If it has expired or does so in the next 30 seconds then refresh token.
        if ( $expire && time() > ( $expire - 120 ) ) {
            $new_token = $this->refresh_token();
            if ( 'success' !== $new_token['status'] ) {
                $this->error_notice( $new_token, true );
            }
        }
    }

    /**
     * Refresh token using saved data.
     *
     * @return array
     */
    public function refresh_token() {
        $config = $this->get_console_api_config();

        $response = wp_remote_post( $config['token_url'], array(
            'body'    => array(
                'refresh_token' => $this->data['refresh_token'],
                'client_id'         => $config['client_id'],
                'client_email'      => $config['client_email'],
                'signing_key'       => $config['private_key'],
                'signing_algorithm' => $config['HS256'],
                'redirect_uri'      => $config['redirect_uri'],
                'grant_type'    => 'refresh_token',
            ),
            'timeout' => 15,
        ) );

        $data = $this->process_response( $response );
        if ( 'success' === $data['status'] ) {
            $this->search_console_data( array(
                'expire'       => time() + $data['body']['expires_in'],
                'access_token' => $data['body']['access_token'],
            ));
        }

        return $data;
    }

    /**
     * Disconnect client connection.
     */
    public function disconnect() {

        $this->search_console_data( false );
        add_option( 'mtsgiapi_data', array(
            'authorized' => false,
            'profiles'   => array(),
        ) );

        $this->set_data();
    }


    /**
     * Set data.
     */
    private function set_data() {
        $this->data          = $this->search_console_data();
        $this->is_authorized = $this->data['authorized'] && $this->data['access_token'] && $this->data['refresh_token'];
        $this->profile       = $this->get_setting( 'profile' );

        if ( ! $this->profile ) {
            if ( ! empty( $this->data['profiles'] ) ) {
                $this->profile = key( $this->data['profiles'] );
            } else {
                $this->profile = $this->select_profile();
            }
        }
        $this->profile_salt = $this->profile ? md5( $this->profile ) : '';
    }

    public function get_setting( $setting, $default = false ) {
        $settings = get_option( 'mts_giapi_settings', $default );
        if ( isset( $settings[$setting] ) ) {
            return $settings[$setting];
        }
        return $default;
    }

    /**
     * Get profiles list.
     */
    public function get_profiles() {
        $profiles = $this->fetch_profiles();
        if ( empty( $profiles ) ) {
            $this->error( 'No profiles found.' );
        }
        $this->success( array(
            'profiles' => $profiles,
            'selected' => $this->select_profile( $profiles ),
        ));
    }

    /**
     * Select profile
     *
     * @param  array $profiles Array of fetched profiles.
     * @return string
     */
    private function select_profile( $profiles = array() ) {
        /* $home_url = home_url( '/', 'https' );
        if ( in_array( $home_url, $profiles ) ) {
            return $home_url;
        }
        $home_url = home_url( '/', 'http' );
        if ( in_array( $home_url, $profiles ) ) {
            return $home_url;
        }
        return ''; */

        $home_url = home_url( '/' );
        return $home_url;
    }
}
