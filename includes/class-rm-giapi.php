<?php
/**
 * Main plugin class.
 *
 * @package RM_GIAPI
 */
class RM_GIAPI {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.2';

	/**
	 * Holds the admin menu hook suffix for the "dummy" dashboard.
	 *
	 * @var string
	 */
	public $dashboard_menu_hook_suffix = '';

	/**
	 * Holds the admin menu hook suffix for Rank Math > Indexing API.
	 *
	 * @var string
	 */
	public $menu_hook_suffix = '';

	/**
	 * The default tab when visiting the admin page.
	 *
	 * @var string
	 */
	public $default_nav_tab = 'settings';

	/**
	 * Holds the current admin tab.
	 *
	 * @var string
	 */
	public $current_nav_tab = '';

	/**
	 * Holds the admin tabs.
	 *
	 * @var string
	 */
	public $nav_tabs = array();

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
		$this->debug    = ( defined( 'GIAPI_DEBUG' ) && GIAPI_DEBUG );
		$this->nav_tabs = array(
			'settings' => __( 'Settings', 'google-indexing-api-by-rank-math' ),
			'console'  => __( 'Console', 'google-indexing-api-by-rank-math' ),
		);
		$this->current_nav_tab = $this->default_nav_tab;
		if ( isset( $_GET['tab'] ) && isset( $this->nav_tabs[ $_GET['tab'] ] ) ) {
			$this->current_nav_tab = $_GET['tab']; //phpcs:ignore
		}
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
		add_action( 'admin_footer', array( $this, 'admin_footer' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_rm_giapi', array( $this, 'ajax_rm_giapi' ) );
		add_action( 'wp_ajax_rm_giapi_limits', array( $this, 'ajax_get_limits' ) );
		add_action( 'admin_init', array( $this, 'rm_missing_admin_notice_error' ), 20, 1 );
		add_action( 'admin_notices', array( $this, 'display_notices' ), 10, 1 );
		add_action( 'load-rank-math_page_rm-giapi', array( $this, 'save_settings' ), 10, 1 );
		add_filter( 'plugin_action_links_' . RM_GIAPI_FILE, array( $this, 'plugin_action_links' ) );

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
		$bulk_actions['giapi_update']    = __( 'Indexing API: Update', 'google-indexing-api-by-rank-math' );
		$bulk_actions['giapi_getstatus'] = __( 'Indexing API: Get Status', 'google-indexing-api-by-rank-math' );
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

	/**
	 * Add new action links for the post listing screen.
	 *
	 * @param  array  $actions Current action links.
	 * @param  object $post    WP_Post object.
	 * @return array  $actions New action links.
	 */
	public function send_to_api_link( $actions, $post ) {
		if ( ! current_user_can( apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ) ) ) {
			return $actions;
		}
		$post_types = $this->get_setting( 'post_types', array() );
		if ( empty( $post_types[ $post->post_type ] ) ) {
			return $actions;
		}

		if ( $post->post_status !== 'publish' ) {
			return $actions;
		}

		$nonce                        = wp_create_nonce( 'giapi-action' );
		$actions['rmgiapi_update']    = '<a href="' . admin_url( 'admin.php?page=rm-giapi-console&apiaction=update&_wpnonce=' . $nonce . '&apiurl=' . rawurlencode( get_permalink( $post ) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __( 'Indexing API: Update', 'google-indexing-api-by-rank-math' ) . '</a>';
		$actions['rmgiapi_getstatus'] = '<a href="' . admin_url( 'admin.php?page=rm-giapi-console&apiaction=getstatus&_wpnonce=' . $nonce . '&apiurl=' . rawurlencode( get_permalink( $post ) ) ) . '" class="rmgiapi-link rmgiapi_update">' . __( 'Indexing API: Get Status', 'google-indexing-api-by-rank-math' ) . '</a>';
		return $actions;
	}

	/**
	 * Load text-domain.
	 *
	 * @return void
	 */
	public function mythemeshop_giapi_load_textdomain() {
		load_plugin_textdomain( 'mythemeshop-giapi', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
	}

	/**
	 * AJAX handler for the console.
	 *
	 * @return void
	 */
	public function ajax_rm_giapi() {
		if ( ! current_user_can( apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ) ) ) {
			die( '0' );
		}
		$url_input = $this->get_input_urls();
		$action    = sanitize_title( wp_unslash( $_POST['api_action'] ) );
		header( 'Content-type: application/json' );

		$result = $this->send_to_api( $url_input, $action );
		wp_send_json( $result );
		exit();
	}

	/**
	 * Submit one or more URLs to Google's API using their API library.
	 *
	 * @param  array  $url_input URLs.
	 * @param  string $action    API action.
	 * @return array  $data      Result of the API call.
	 */
	public function send_to_api( $url_input, $action ) {
		$url_input = (array) $url_input;

		include_once RM_GIAPI_PATH . 'vendor/autoload.php';
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
			$post_body = new Google_Service_Indexing_UrlNotification();
			if ( $action === 'getstatus' ) {
				$request_part = $service->urlNotifications->getMetadata( array( 'url' => $url ) ); // phpcs:ignore
			} else {
				$post_body->setType( $action === 'update' ? 'URL_UPDATED' : 'URL_DELETED' );
				$post_body->setUrl( $url );
				$request_part = $service->urlNotifications->publish( $post_body ); // phpcs:ignore
			}
			$batch->add( $request_part, 'url-' . $i );
		}

		$results   = $batch->execute();
		$data      = array();
		$res_count = count( $results );
		foreach ( $results as $id => $response ) {
			// Change "response-url-1" to "url-1".
			$local_id = substr( $id, 9 );
			if ( is_a( $response, 'Google_Service_Exception' ) ) {
				$data[ $local_id ] = json_decode( $response->getMessage() );
			} else {
				$data[ $local_id ] = (array) $response->toSimpleObject();
			}
			if ( $res_count === 1 ) {
				$data = $data[ $local_id ];
			}
		}

		$this->log_request( $action );

		if ( $this->debug ) {
			error_log( 'RM GI API: ' . $action . ' ' . $url_input[0] . ( count( $url_input ) > 1 ? ' (+)' : '' ) . "\n" . print_r( $data, true ) ); // phpcs:ignore
		}

		return $data;
	}

	/**
	 * Log request type & timestamp to keep track of remaining quota.
	 *
	 * @param  string $type API action.
	 * @return void
	 */
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

	/**
	 * Get current quota (limits minus usage).
	 *
	 * @return array Current quota.
	 */
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

	/**
	 * AJAX handler to get current quota in JSON format.
	 *
	 * @return void
	 */
	public function ajax_get_limits() {
		wp_send_json( $this->get_limits() );
		die();
	}

	/**
	 * Normalize input URLs.
	 *
	 * @return array Input URLs.
	 */
	public function get_input_urls() {
		return array_values( array_filter( array_map( 'trim', explode( "\n", sanitize_textarea_field( wp_unslash( $_POST['url'] ) ) ) ) ) );
	}

	/**
	 * Add admin menu items.
	 *
	 * @return void
	 */
	public function admin_menu() {
		if ( ! class_exists( 'RankMath' ) ) {
			$this->dashboard_menu_hook_suffix = add_menu_page( 'Rank Math', 'Rank Math', apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ), 'rm-giapi-dashboard', null, 'dashicons-chart-area', 76 );
			$this->dashboard_menu_hook_suffix = add_submenu_page( 'rm-giapi-dashboard', 'Rank Math', __( 'Dashboard', 'google-indexing-api-by-rank-math' ), apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ), 'rm-giapi-dashboard', array( $this, 'show_dashboard' ), 'none', 76 );
			$this->menu_hook_suffix           = add_submenu_page( 'rm-giapi-dashboard', __( 'Google Indexing API', 'google-indexing-api-by-rank-math' ), __( 'Indexing API', 'google-indexing-api-by-rank-math' ), apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ), 'rm-giapi', array( $this, 'show_admin_page' ) );
			return;
		}

		$this->menu_hook_suffix  = add_submenu_page( 'rank-math', __( 'Google Indexing API', 'google-indexing-api-by-rank-math' ), __( 'Indexing API', 'google-indexing-api-by-rank-math' ), apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ), 'rm-giapi', array( $this, 'show_admin_page' ) );
	}

	/**
	 * Output Indexing API Console page contents.
	 *
	 * @return void
	 */
	public function show_console() {
		$limits = $this->get_limits();
		$urls   = home_url( '/' );
		if ( isset( $_GET['apiurl'] ) ) {
			$urls = esc_url_raw( wp_unslash( $_GET['apiurl'] ) );
		} elseif ( isset( $_GET['apipostid'] ) ) {
			$ids  = (array) wp_unslash( $_GET['apipostid'] ); // phpcs:ignore
			$ids  = array_map( 'absint', $ids ); // We sanitize it here.
			$urls = '';
			foreach ( $ids as $id ) {
				if ( get_post_status( $id ) === 'publish' ) {
					$urls .= get_permalink( $id ) . "\n";
				}
			}
		}
		$selected_action = 'update';
		if ( isset( $_GET['apiaction'] ) ) {
			$selected_action = sanitize_title( wp_unslash( $_GET['apiaction'] ) );
		}

		include_once RM_GIAPI_PATH . 'views/console.php';
	}

	/**
	 * Admin page content.
	 *
	 * @return void
	 */
	public function show_admin_page() {
		$this->nav_tabs();

		$method = 'show_' . $this->current_nav_tab;
		if ( method_exists( $this, $method ) ) {
			$this->$method();
		}
	}

	/**
	 * Admin page tabs.
	 *
	 * @return void
	 */
	public function nav_tabs() {
		echo '<div class="nav-tab-wrapper">';
		foreach ( $this->nav_tabs as $tab => $label ) {
			echo '<a href="' . esc_url( add_query_arg( 'tab', $tab ) ) . '" class="nav-tab ' . ( $this->current_nav_tab == $tab ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
		}
		echo '</div>';
	}

	/**
	 * Enqueue CSS & JS for the admin pages.
	 *
	 * @param  string $hook_suffix Hook suffix of the current page.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		$min = '.min';
		if ( $this->debug ) {
			$min = '';
		}
		if ( $hook_suffix === $this->dashboard_menu_hook_suffix ) {
			wp_enqueue_script( 'rm-giapi-dashboard', RM_GIAPI_URL . "assets/js/dashboard{$min}.js", array( 'jquery', 'updates' ), $this->version, true );
			wp_enqueue_style( 'rm-giapi-dashboard', RM_GIAPI_URL . 'assets/css/dashboard.css', array(), $this->version );
		} elseif ( $hook_suffix === $this->menu_hook_suffix ) {
			wp_enqueue_script( 'rm-giapi-console', RM_GIAPI_URL . "assets/js/console{$min}.js", array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'rm-giapi-admin', RM_GIAPI_URL . 'assets/css/admin.css', array(), $this->version );

			$submit_onload = false;
			if ( ! empty( $_GET['apiaction'] ) && ( ! empty( $_GET['apiurl'] ) || ! empty( $_GET['apipostid'] ) ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'giapi-action' ) ) {
				$submit_onload = true;
			}
			wp_localize_script(
				'rm-giapi-console',
				'rm_giapi',
				array(
					'submit_onload'     => $submit_onload,
					'l10n_success'      => __( 'Success', 'google-indexing-api-by-rank-math' ),
					'l10n_error'        => __( 'Error', 'google-indexing-api-by-rank-math' ),
					'l10n_last_updated' => __( 'Last updated ', 'google-indexing-api-by-rank-math' ),
					'l10n_see_response' => __( 'See response for details.', 'google-indexing-api-by-rank-math' ),
				)
			);
		}
	}

	/**
	 * Output Indexing API Settings page contents.
	 *
	 * @return void
	 */
	public function show_settings() {
		include_once RM_GIAPI_PATH . 'views/settings.php';
	}

	/**
	 * Handle settings save.
	 *
	 * @return void
	 */
	public function save_settings() {
		if ( ! isset( $_POST['giapi_settings'] ) ) {
			return;
		}
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_title( wp_unslash( $_POST['_wpnonce'] ) ), 'giapi-save' ) ) {
			return;
		}
		if ( ! current_user_can( apply_filters( 'rank_math/indexing_api/capability', 'manage_options' ) ) ) {
			return;
		}

		$json = sanitize_textarea_field( wp_unslash( $_POST['giapi_settings']['json_key'] ) );
		if ( isset( $_FILES['json_file'] ) && isset( $_FILES['json_file']['tmp_name'] ) && file_exists( sanitize_file_name( wp_unslash( $_FILES['json_file']['tmp_name'] ) ) ) ) {
			$json = file_get_contents( $_FILES['json_file']['tmp_name'] ); // phpcs:ignore
		}

		$post_types = (array) $_POST['giapi_settings']['post_types']; // phpcs:ignore
		$post_types = array_map( 'sanitize_title', $post_types );

		update_option(
			'giapi_settings',
			array(
				'json_key'   => $json,
				'post_types' => $post_types,
			)
		);
		$this->add_notice( __( 'Settings updated.', 'google-indexing-api-by-rank-math' ), 'notice-success' );
	}

	/**
	 * Add a notice message to internal list, to be displayed on the next page load.
	 *
	 * @param string  $message Meaningful message.
	 * @param string  $class   Additional class for the notice element.
	 * @param array   $show_on Admin page IDs where the notice should be displayed.
	 * @param boolean $persist Whether the notice should be stored in the database until it is displayed.
	 * @return void
	 */
	public function add_notice( $message, $class = '', $show_on = null, $persist = false, $id = '' ) {
		$notice = array(
			'message' => $message,
			'class'   => $class,
			'show_on' => $show_on,
		);

		if ( ! $id ) {
			$id = md5( serialize( $notice ) );
		}

		if ( $persist ) {
			$notices        = get_option( 'giapi_notices', array() );
			$notices[ $id ] = $notice;
			update_option( 'giapi_notices', $notices );
			return;
		}
		$this->notices[ $id ] = $notice;
	}

	/**
	 * Output notices from internal list.
	 *
	 * @return void
	 */
	public function display_notices() {
		$screen        = get_current_screen();
		$stored        = get_option( 'giapi_notices', array() );
		$this->notices = array_merge( $stored, $this->notices );
		delete_option( 'giapi_notices' );
		foreach ( $this->notices as $notice ) {
			if ( ! empty( $notice['show_on'] ) && is_array( $notice['show_on'] ) && ! in_array( $screen->id, $notice['show_on'], true ) ) {
				return;
			}
			$class = 'notice rm-giapi-notice ' . $notice['class'];
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $notice['message'] ) );
		}
	}

	/**
	 * Output checkbox inputs for the registered post types.
	 *
	 * @return void
	 */
	public function post_types_checkboxes() {
		$settings   = $this->get_setting( 'post_types', array() );
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $post_types as $post_type ) {
			?>
			<input type="hidden" name="giapi_settings[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="0">
			<label><input type="checkbox" name="giapi_settings[post_types][<?php echo esc_attr( $post_type->name ); ?>]" value="1" <?php checked( ! empty( $settings[ $post_type->name ] ) ); ?>> <?php echo esc_html( $post_type->label ); ?></label><br>
			<?php
		}
	}

	/**
	 * Get a specific plugin setting.
	 *
	 * @param  string $setting Setting name.
	 * @param  string $default Default value if setting is not found.
	 * @return string Setting value or default.
	 */
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

		if ( $setting === 'json_key' ) {
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ) {
				return file_get_contents( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' );
			}
		}

		return ( isset( $settings[ $setting ] ) ? $settings[ $setting ] : $default );
	}

	/**
	 * Output Rank Math Dashboard page contents
	 *
	 * @return void
	 */
	public function show_dashboard() {
		include_once RM_GIAPI_PATH . 'views/dashboard.php';
	}

	/**
	 * Add Rank Math module.
	 *
	 * @param array $modules Current modules.
	 * @return array $modules New modules.
	 */
	public function add_rm_module( $modules ) {
		$modules['indexing-api'] = array(
			'id'            => 'indexing-api',
			'title'         => esc_html__( 'Google Indexing API (Beta)', 'rank-math' ),
			'desc'          => esc_html__( 'Directly notify Google when pages are added, updated or removed. The Indexing API supports pages with either job posting or livestream structured data.', 'rank-math' ) . ' <a href="' . $this->setup_guide_url . '" target="_blank">' . __( 'Read our setup guide', 'google-indexing-api-by-rank-math' ) . '</a>',
			'class'         => 'RM_GIAPI_Module',
			'icon'          => 'dashicons-admin-site-alt3',
			'settings_link' => admin_url( 'admin.php?page=rm-giapi' ),
		);
		return $modules;
	}

	/**
	 * Add Javascript to the Dashboard.
	 *
	 * @param  string $hook_suffix Hook suffix of the current admin page.
	 * @return void
	 */
	public function admin_footer( $hook_suffix ) {
		$screen = get_current_screen();
		if ( $screen->id !== 'toplevel_page_rank-math' ) {
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
					.text('<?php echo esc_js( __( 'Active (Plugin)', 'google-indexing-api-by-rank-math' ) ); ?>')
					.closest('.rank-math-box')
					.addClass('active');
			});
		</script>
		<?php
	}

	/**
	 * Add admin notice about Rank Math if it's not installed.
	 *
	 * @return void
	 */
	public function rm_missing_admin_notice_error() {
		if ( class_exists( 'RankMath' ) ) {
			return;
		}

		/* translators: %s is a link to Rank Math plugin page */
		$message = sprintf( __( 'It is recommended to use %s along with the Indexing API plugin.', 'google-indexing-api-by-rank-math' ), '<a href="https://wordpress.org/plugins/seo-by-rank-math/" target="_blank">' . __( 'Rank Math SEO' ) . '</a>' );
		$class   = 'notice-error';
		$show_on = array( 'rank-math_page_rm-giapi', 'rank-math_page_rm-giapi-dashboard' );

		$this->add_notice( $message, $class, $show_on );
	}

	/**
	 * When a post from a watched post type is published, submit its URL
	 * to the API and add notice about it.
	 *
	 * @param  int $post_id Post ID.
	 * @return void
	 */
	public function publish_post( $post_id ) {
		$post = get_post( $post_id );
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$send_url = apply_filters( 'rank_math/indexing_api/publish_url', get_permalink( $post ), $post );
		// Early exit if filter is set to false.
		if ( ! $send_url ) {
			return;
		}

		if ( $post->post_status === 'publish' ) {
			$this->send_to_api( $send_url, 'update' );
			$this->add_notice( __( 'The post was automatically submitted to the Google Indexing API for indexation.', 'google-indexing-api-by-rank-math' ), 'notice-info', null, true );
		}
	}

	/**
	 * When a post is deleted, check its post type and submit its URL
	 * to the API if appropriate, then add notice about it.
	 *
	 * @param  int $post_id Post ID.
	 * @return void
	 */
	public function delete_post( $post_id ) {
		$post_types = $this->get_setting( 'post_types', array() );
		$post       = get_post( $post_id );
		if ( empty( $post_types[ $post->post_type ] ) ) {
			return;
		}

		$send_url = apply_filters( 'rank_math/indexing_api/delete_url', get_permalink( $post ), $post );
		// Early exit if filter is set to false.
		if ( ! $send_url ) {
			return;
		}

		$this->send_to_api( $send_url, 'delete' );
		$this->add_notice( __( 'The post was automatically submitted to the Google Indexing API for deletion.', 'google-indexing-api-by-rank-math' ), 'notice-info', null, true );
	}

	/**
	 * Add Settings to plugin action links.
	 *
	 * @param  array $actions Original actions.
	 * @return array $actions New actions.
	 */
	public function plugin_action_links( $actions ) {
		$actions['settings'] = '<a href="' . admin_url( 'admin.php?page=rm-giapi' ) . '">' . __( 'Settings', 'google-indexing-api-by-rank-math' ) . '</a>';
		return $actions;
	}

}
