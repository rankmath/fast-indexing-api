<?php

class RM_GIAPI {

	/**
	 * Holds the admin menu hook suffix for the "dummy" dashboard.
	 *
	 * @var string
	 */
	public $dashboard_menu_hook_suffix = '';

	/**
	 * Holds the admin menu hook suffix for Rank Math > Indexing API Console.
	 *
	 * @var string
	 */
	public $console_menu_hook_suffix = '';

	/**
	 * Holds the admin menu hook suffix for Rank Math > Indexing API Settings.
	 *
	 * @var string
	 */
	public $settings_menu_hook_suffix = '';

	/**
	 * Holds the admin notice messages.
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Debug mode. Enable with define( 'GIAPI_DEBUG', true );
	 *
	 * @var bool
	 */
	public $debug = false;

	/**
	 * URL of the plugin setup guide on rankmath.com.
	 *
	 * @var string
	 */
	public $setup_guide_url = 'https://s.rankmath.com/indexing-api';

	/**
	 * Constructor method.
	 */
	public function __construct() {
		$this->debug = ( defined( 'GIAPI_DEBUG' ) && GIAPI_DEBUG );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
		add_action( 'admin_footer', array( $this, 'admin_footer' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_rm_giapi', array( $this, 'ajax_rm_giapi' ) );
		add_action( 'wp_ajax_rm_giapi_limits', array( $this, 'ajax_get_limits' ) );
		add_action( 'admin_init', array( $this, 'rm_missing_admin_notice_error' ), 20, 1 );
		add_action( 'admin_notices', array( $this, 'display_notices' ), 10, 1 );
		add_action( 'load-rank-math_page_rm-giapi-settings', array( $this, 'save_settings' ), 10, 1 );

		if ( $this->get_setting( 'json_key' ) ) {
			$post_types = $this->get_setting( 'post_types', array() );
			foreach ( $post_types as $post_type => $enabled ) {
				if ( empty( $enabled ) ) {
					continue;
				}
				add_action( 'save_post_' . $post_type, array( $this, 'publish_post' ), 10, 2 );
				add_filter( 'bulk_actions-edit-' . $post_type, array( $this, 'register_bulk_actions' ) );
				add_filter( 'handle_bulk_actions-edit-' . $post_type, array( $this, 'bulk_action_handler' ), 10, 3 );
			}
			add_filter( 'post_row_actions', array( $this, 'send_to_api_link' ), 10, 2 );
			add_filter( 'page_row_actions', array( $this, 'send_to_api_link' ), 10, 2 );
			add_action( 'trashed_post', array( $this, 'delete_post' ), 10, 1 );
		}

		// Localization.
		add_action( 'plugins_loaded', array( $this, 'mythemeshop_giapi_load_textdomain' ) );

		add_filter( 'rank_math/modules', array( $this, 'add_rm_module' ), 25 );
	}

	/**
	 * Register actions for the bulk edit dropdowns on the post listing screen.
	 *
	 * @param  array $bulk_actions Actions.
	 * @return array $bulk_actions
	 */
	public function register_bulk_actions( $bulk_actions ) {
		$bulk_actions['giapi_update']    = __( 'Indexing API: Update', 'rm-giapi' );
		$bulk_actions['giapi_getstatus'] = __( 'Indexing API: Get Status', 'rm-giapi' );
		return $bulk_actions;
	}

	/**
	 * Handle custom bulk actions.
	 *
	 * @param  string $redirect_to The redirect URL.
	 * @param  string $doaction    The action being taken.
	 * @param  array  $post_ids    The items to take the action on.
	 *
	 * @return string $redirect_to The redirect URL.
	 */
	public function bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
		if ( $doaction !== 'giapi_update' && $doaction !== 'giapi_getstatus' ) {
			return $redirect_to;
		}

		$nonce       = wp_create_nonce( 'giapi-action' );
		$redirect_to = add_query_arg(
			array(
				'page'      => 'rm-giapi-console',
				'apiaction' => substr( $doaction, 6 ),
				'apipostid' => $post_ids,
				'_wpnonce'  => $nonce,

			),
			admin_url( 'admin.php' )
		);
		return $redirect_to;
	}

	public function send_to_api_link( $actions, $post ) {
		if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
			return $actions;
		}
		$post_types = $this->get_setting( 'post_types', array() );
		if ( empty( $post_types[ $post->post_type ] ) ) {
			return $actions;
		}

		if ( $post->post_status != 'publish' ) {
			return $actions;
		}

		$nonce                        = wp_create_nonce( 'giapi-action' );
		$actions['rmgiapi_update']    = '<a href="' . admin_url( 'admin.php?page=rm-giapi-console&apiaction=update&_wpnonce=' . $nonce . '&apiurl=' . rawurlencode( get_permalink( $post ) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __( 'Indexing API: Update', 'rm-giapi' ) . '</a>';
		$actions['rmgiapi_getstatus'] = '<a href="' . admin_url( 'admin.php?page=rm-giapi-console&apiaction=getstatus&_wpnonce=' . $nonce . '&apiurl=' . rawurlencode( get_permalink( $post ) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __( 'Indexing API: Get Status', 'rm-giapi' ) . '</a>';
		return $actions;
	}

	public function mythemeshop_giapi_load_textdomain() {
		load_plugin_textdomain( 'mythemeshop-giapi', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
	}

	public function ajax_rm_giapi() {
		if ( ! current_user_can( apply_filters( 'rmgiapi_capability', 'manage_options' ) ) ) {
			die( '0' );
		}
		$url_input = $this->get_input_urls();
		$action    = sanitize_title( $_POST['api_action'] );
		header( 'Content-type: application/json' );

		$result = $this->send_to_api( $url_input, $action );
		wp_send_json( $result );
		exit();
	}

	public function send_to_api( $url_input, $action ) {
		$url_input = (array) $url_input;

		include_once 'vendor/autoload.php';
		$this->client = new Google_Client();
		$this->client->setAuthConfig( json_decode( $this->get_setting( 'json_key' ), true ) );
		$this->client->setConfig( 'base_path', 'https://indexing.googleapis.com' );
		$this->client->addScope( 'https://www.googleapis.com/auth/indexing' );

		// Batch request.
		$this->client->setUseBatch( true );
		// init google batch and set root URL.
		$service = new Google_Service_Indexing( $this->client );
		$batch   = new Google_Http_Batch( $this->client, false, 'https://indexing.googleapis.com' );
		foreach ( $url_input as $i => $url ) {
			$postBody = new Google_Service_Indexing_UrlNotification();
			if ( $action == 'getstatus' ) {
				$request_part = $service->urlNotifications->getMetadata( array( 'url' => $url ) );
			} else {
				$postBody->setType( $action == 'update' ? 'URL_UPDATED' : 'URL_DELETED' );
				$postBody->setUrl( $url );
				$request_part = $service->urlNotifications->publish( $postBody );
			}
			$batch->add( $request_part, 'url-' . $i );
		}

		$results = $batch->execute();
		$data    = array();
		$rc      = count( $results );
		foreach ( $results as $id => $response ) {
			// Change "response-url-1" to "url-1".
			$local_id = substr( $id, 9 );
			if ( is_a( $response, 'Google_Service_Exception' ) ) {
				$data[ $local_id ] = json_decode( $response->getMessage() );
			} else {
				$data[ $local_id ] = (array) $response->toSimpleObject();
			}
			if ( $rc === 1 ) {
				$data = $data[ $local_id ];
			}
		}

		$this->log_request( $action );

		if ( $this->debug ) {
			error_log( 'RM GI API: ' . $action . ' ' . $url_input[0] . ( count( $url_input ) > 1 ? ' (+)' : '' ) . "\n" . print_r( $data, true ) );
		}

		return $data;
	}

	public function log_request( $type ) {
		$requests_log            = get_option(
			'giapi_requests',
			array(
				'update'    => array(),
				'delete'    => array(),
				'getstatus' => array(),
			)
		);
		$requests_log[ $type ][] = time();
		if ( count( $requests_log[ $type ] ) > 600 ) {
			$requests_log[ $type ] = array_slice( $requests_log[ $type ], -600, 600, true );
		}
		update_option( 'giapi_requests', $requests_log );
	}

	public function get_limits() {
		$current_limits = array(
			'publishperday' => 0,
			'permin'        => 0,
			'metapermin'    => 0,
		);

		$limit_publishperday = 200;
		$limit_permin        = 600;
		$limit_metapermin    = 180;
		$requests_log        = get_option(
			'giapi_requests',
			array(
				'update'    => array(),
				'delete'    => array(),
				'getstatus' => array(),
			)
		);
		$timestamp_1day_ago  = strtotime( '-1 day' );
		$timestamp_1min_ago  = strtotime( '-1 minute' );

		$publish_1day = 0;
		$all_1min     = 0;
		$meta_1min    = 0;
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
		$current_limits['permin']        = 600 - $all_1min;
		$current_limits['metapermin']    = 180 - $meta_1min;

		return $current_limits;
	}

	public function ajax_get_limits() {
		wp_send_json( $this->get_limits() );
	}

	public function get_input_urls() {
		return array_values( array_filter( array_map( 'trim', explode( "\n", $_POST['url'] ) ) ) );
	}

	public function admin_menu() {
		// If Rank Math is not active: add Rank Math & Dashboard & Indexing API subpages.
		if ( ! class_exists( 'RankMath' ) ) {
			$this->dashboard_menu_hook_suffix = add_menu_page( 'Rank Math', 'Rank Math', apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-dashboard', null, 'dashicons-chart-area', 76 );
			$this->dashboard_menu_hook_suffix = add_submenu_page( 'rm-giapi-dashboard', 'Rank Math', __( 'Dashboard', 'rm-giapi' ), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-dashboard', array( $this, 'show_dashboard' ), 'none', 76 );
			$this->console_menu_hook_suffix   = add_submenu_page( 'rm-giapi-dashboard', __( 'Google Indexing API', 'rm-giapi' ), __( 'Indexing API Console', 'rm-giapi' ), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-console', array( $this, 'show_console' ) );
			$this->settings_menu_hook_suffix  = add_submenu_page( 'rm-giapi-dashboard', __( 'Rank Math Indexing API Settings', 'rm-giapi' ), __( 'Indexing API Settings', 'rm-giapi' ), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-settings', array( $this, 'show_settings' ) );
			return;
		}

		// If Rank Math is installed: add module control + settings & console pages.
		$this->console_menu_hook_suffix  = add_submenu_page( 'rank-math', __( 'Google Indexing API', 'rm-giapi' ), __( 'Indexing API Console', 'rm-giapi' ), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-console', array( $this, 'show_console' ) );
		$this->settings_menu_hook_suffix = add_submenu_page( 'rank-math', __( 'Rank Math Indexing API Settings', 'rm-giapi' ), __( 'Indexing API Settings', 'rm-giapi' ), apply_filters( 'rmgiapi_capability', 'manage_options' ), 'rm-giapi-settings', array( $this, 'show_settings' ) );
	}

	public function show_console() {
		$limits = $this->get_limits();
		$urls   = home_url( '/' );
		if ( isset( $_GET['apiurl'] ) ) {
			$urls = esc_url_raw( $_GET['apiurl'] );
		} elseif ( isset( $_GET['apipostid'] ) ) {
			$ids  = (array) $_GET['apipostid'];
			$ids  = array_map( 'absint', $ids );
			$urls = '';
			foreach ( $ids as $id ) {
				if ( get_post_status( $id ) == 'publish' ) {
					$urls .= get_permalink( $id ) . "\n";
				}
			}
		}

		include_once '../views/console.php';
	}

	public function admin_enqueue_scripts( $hook_suffix ) {
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
		include_once '../views/settings.php';
	}

	public function save_settings() {
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

		update_option(
			'giapi_settings',
			array(
				'json_key'   => $json,
				'post_types' => $post_types,
			)
		);
		$this->add_notice( __( 'Settings updated.', 'rm-giapi' ), 'notice-success' );
	}

	public function add_notice( $message, $class = '', $show_on = null, $persist = false ) {
		if ( $persist ) {
			$notices   = get_option( 'giapi_notices', array() );
			$notices[] = array(
				'message' => $message,
				'class'   => $class,
				'show_on' => $show_on,
			);
			update_option( 'giapi_notices', $notices );
			return;
		}
		$this->notices[] = array(
			'message' => $message,
			'class'   => $class,
			'show_on' => $show_on,
		);
	}

	public function display_notices() {
		$screen        = get_current_screen();
		$stored        = get_option( 'giapi_notices', array() );
		$this->notices = array_merge( $stored, $this->notices );
		delete_option( 'giapi_notices' );
		foreach ( $this->notices as $notice ) {
			if ( ! empty( $notice['show_on'] ) && is_array( $notice['show_on'] ) && ! in_array( $screen->id, $notice['show_on'] ) ) {
				return;
			}
			$class = 'notice rm-giapi-notice ' . $notice['class'];
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $notice['message'] );
		}
	}

	public function post_types_checkboxes() {
		$settings   = $this->get_setting( 'post_types', array() );
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $post_types as $post_type ) {
			?>
			<input type="hidden" name="giapi_settings[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="0">
			<label><input type="checkbox" name="giapi_settings[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="1" <?php checked( ! empty( $settings[ $post_type->name ] ) ); ?>> <?php echo $post_type->label; ?></label><br>
			<?php
		}
	}

	public function get_setting( $setting, $default = null ) {
		$defaults = array(
			'json_key'   => '',
			'post_types' => array(
				'post' => 1,
				'page' => 1,
			),
		);
		$settings = get_option( 'giapi_settings', array() );
		$settings = array_merge( $defaults, $settings );

		if ( $setting == 'json_key' ) {
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ) {
				return file_get_contents( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' );
			}
		}

		return ( isset( $settings[ $setting ] ) ? $settings[ $setting ] : $default );
	}

	public function show_dashboard() {
		include_once '../views/dashboard.php';
	}


	public function add_rm_module( $modules ) {
		$modules['indexing-api'] = array(
			'id'            => 'indexing-api',
			'title'         => esc_html__( 'Google Indexing API (Beta)', 'rank-math' ),
			'desc'          => esc_html__( 'Directly notify Google when pages are added, updated or removed. The Indexing API supports pages with either job posting or livestream structured data.', 'rank-math' ) . ' <a href="' . $this->setup_guide_url . '" target="_blank">' . __( 'Read our setup guide', 'rm-giapi' ) . '</a>',
			'class'         => 'RM_GIAPI_Module',
			'icon'          => 'dashicons-admin-site-alt3',
			'settings_link' => admin_url( 'admin.php?page=rm-giapi-settings' ),
		);
		return $modules;
	}

	public function admin_footer( $hook_suffix ) {
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
					.text('<?php echo esc_js( __( 'Active (Plugin)', 'rm-giapi' ) ); ?>')
					.closest('.rank-math-box')
					.addClass('active');
			});
		</script>
		<?php
	}

	public function rm_missing_admin_notice_error() {
		if ( class_exists( 'RankMath' ) ) {
			return;
		}

		$message = sprintf( __( 'It is recommended to use %s along with the Indexing API plugin.', 'rm-giapi' ), '<a href="https://wordpress.org/plugins/seo-by-rank-math/" target="_blank">' . __( 'Rank Math SEO' ) . '</a>' );
		$class   = 'notice-error';
		$show_on = array( 'rank-math_page_rm-giapi-console', 'rank-math_page_rm-giapi-settings', 'rank-math_page_rm-giapi-dashboard' );

		$this->add_notice( $message, $class, $show_on );
	}

	public function publish_post( $post_id ) {
		$post = get_post( $post_id );
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( $post->post_status == 'publish' ) {
			$this->send_to_api( get_permalink( $post ), 'update' );
			$this->add_notice( __( 'The post was automatically submitted to the Google Indexing API for indexation.', 'rm-giapi' ), 'notice-info', null, true );
		}
	}

	public function delete_post( $post_id ) {
		$post_types = $this->get_setting( 'post_types', array() );
		$post       = get_post( $post_id );
		if ( empty( $post_types[ $post->post_type ] ) ) {
			return;
		}
		$this->send_to_api( get_permalink( $post ), 'delete' );
		$this->add_notice( __( 'The post was automatically submitted to the Google Indexing API for deletion.', 'rm-giapi' ), 'notice-info', null, true );
	}

}
