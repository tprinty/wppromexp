<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.edisonave.com
 * @since      1.0.0
 *
 * @package    Wppromexp
 * @subpackage Wppromexp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wppromexp
 * @subpackage Wppromexp/public
 * @author     Tom Printy <tprinty@edisonave.com>
 */
class Wppromexp_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wppromexp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wppromexp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wppromexp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wppromexp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wppromexp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wppromexp-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Register the API URL.
	 *
	 * @since    1.0.0
	 */
	public function wppromexp_register_route(){
		
		register_rest_route(
			'metrics',
			'/accesskey/(?P<accesskey>\d+)',
			array(
	            'methods'  => 'GET',
	            'callback' =>  array($this,'wppromexp_rest_serve_request'),
			)
		);
	}
	
	/**
	 * Error if user not allowed.
	 *
	 * @since    1.0.0
	 */
	public function wppromexp_api_error(){
		echo '{ "error": "You are not authorized to view this page." }';
	}
	
	/**
	 * Register the API URL.
	 *
	 * @since    1.0.0
	 */
	public function wppromexp_rest_serve_request(WP_REST_Request $request ){
		
		$get_accesskey = $request['accesskey'];
		
		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
		
		
			
		if ( ($access_key = get_option( 'wppromexp_access_key' )) == false ) {
			$this->wppromexp_api_error();
		} 
		
		if ($get_accesskey === $access_key ) {
			$metrics = $this->wppromexp_get_metrics();
			echo $metrics;
		}else{
			print "We here";
			$this->wppromexp_api_error();
		}
		
		
		$served = true;	
	}
	
	/**
	 * Build the array of metrics.
	 *
	 * @since    1.0.0
	 */
	public function wppromexp_get_metrics(){
		global $wpdb, $table_prefix;
		require_once ABSPATH . '/wp-admin/includes/update.php';
		
		$result = '';
		
		/* Users */	
		$users   = count_users();
		$result .= "# HELP wp_users_total Total number of users.\n";
		$result .= "# TYPE wp_users_total counter\n";
		$result .= 'wp_users_total{host="' . get_site_url() . '"} ' . $users[ 'total_users' ] . "\n";
	
		/* Posts */	
		$posts       = wp_count_posts();
		$n_posts_pub = $posts->publish;
		$n_posts_dra = $posts->draft;
		$result .= "# HELP wp_posts_total Total number of posts published.\n";
		$result .= "# TYPE wp_posts_total counter\n";
		$result .= 'wp_posts_total{host="' . get_site_url() . '", status="published"} ' . $n_posts_pub . "\n";
		$result .= 'wp_posts_total{host="' . get_site_url() . '", status="draft"} ' . $n_posts_dra . "\n";
		
		/*Pages */
		$n_pages = wp_count_posts( 'page' );
		$result .= "# HELP wp_pages_total Total number of pages published.\n";
		$result .= "# TYPE wp_pages_total counter\n";
		$result .= 'wp_pages_total{host="' . get_site_url() . '", status="published"} ' . $n_pages->publish . "\n";
		$result .= 'wp_pages_total{host="' . get_site_url() . '", status="draft"} ' . $n_pages->draft . "\n";
		
		/*Auto Load Options*/
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "options` WHERE `autoload` = 'yes'", ARRAY_A ); 
		$result .= "# HELP wp_options_autoload Options in autoload.\n";
		$result .= "# TYPE wp_options_autoload counter\n";
		$result .= 'wp_options_autoload{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
		
		/*Auto Load Size*/
		$query   = $wpdb->get_results( 'SELECT ROUND(SUM(LENGTH(option_value))/ 1024) as value FROM `' . $table_prefix . "options` WHERE `autoload` = 'yes'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_options_autoload_size Options size in KB in autoload.\n";
		$result .= "# TYPE wp_options_autoload_size counter\n";
		$result .= 'wp_options_autoload_size{host="' . get_site_url() . '"} ' . $query[ 0 ][ 'value' ] . "\n";
		
		/*Transients Size*/
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "options` WHERE `autoload` = 'yes' AND `option_name` LIKE '%transient%'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_transient_autoload DB Transient in autoload.\n";
		$result .= "# TYPE wp_transient_autoload counter\n";
		$result .= 'wp_transient_autoload{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
		
		/*User Sessions*/
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "options` WHERE `option_name` LIKE '_wp_session_%'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_user_sessions User sessions.\n";
		$result .= "# TYPE wp_user_sessions counter\n";
		$result .= 'wp_user_sessions{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
		
		/*Posts/Pages with no title*/
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "posts` WHERE post_title='' AND post_status!='auto-draft' AND post_status!='draft' AND post_status!='trash' AND (post_type='post' OR post_type='page')", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_posts_without_title Post/Page without title.\n";
		$result .= "# TYPE wp_posts_without_title counter\n";
		$result .= 'wp_posts_without_title{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
		
		/*Posts/Pages with no content*/
		$query   = $wpdb->get_results( 'SELECT * FROM `' . $table_prefix . "posts` WHERE post_content='' AND post_status!='draft' AND post_status!='trash' AND post_status!='auto-draft' AND (post_type='post' OR post_type='page')", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_posts_without_content Post/Page without content.\n";
		$result .= "# TYPE wp_posts_without_content counter\n";
		$result .= 'wp_posts_without_content{host="' . get_site_url() . '"} ' . count( $query ) . "\n";
		
		/*Total DB size*/
		$query   = $wpdb->get_results( "SELECT SUM(ROUND(((data_length + index_length) / 1024 / 1024), 2)) as value FROM information_schema.TABLES WHERE table_schema = '" . DB_NAME . "'", ARRAY_A ); // phpcs:ignore WordPress.DB
		$result .= "# HELP wp_db_size Total DB size in MB.\n";
		$result .= "# TYPE wp_db_size counter\n";
		$result .= 'wp_db_size{host="' . get_site_url() . '"} ' . $query[ 0 ][ 'value' ] . "\n";
		
		/*Updates*/
		$update_data =  get_site_transient( 'update_plugins' );	
		$result .= "# HELP wp_plugin_updates Total Plugins that need updating.\n";
		$result .= "# TYPE wp_plugin_updates counter\n";
		$result .= 'wp_plugin_updates{host="' . get_site_url() . '"} ' . absint(count($update_data->response)). "\n";;
		
		$update_data =  get_site_transient( 'update_themes' );
		$result .= "# HELP wp_theme_updates Total Themes that need updating.\n";
		$result .= "# TYPE wp_theme_updates counter\n";
		$result .= 'wp_theme_updates{host="' . get_site_url() . '"} ' . absint(count($update_data->response)). "\n";
		
		$core_update = 0;
		if (function_exists( 'get_core_updates' ) ) {
        	$update_wordpress = get_core_updates( array( 'dismissed' => false ) );
        	if ( ! empty( $update_wordpress ) && ! in_array( $update_wordpress[0]->response, array( 'development', 'latest' ) ) ) {
				$core_update = 1;
			}
		}
		$result .= "# HELP wp_core_updates If WP Core needs updating.\n";
		$result .= "# TYPE wp_core_updates counter\n";
		$result .= 'wp_core_updates{host="' . get_site_url() . '"} ' . 	$core_update . "\n";
		
		$current = get_site_transient('update_core');
		$result .= "# HELP wp_core_version Current Version of WP.\n";
		$result .= "# TYPE wp_core_version gauge\n";
		$result .= 'wp_core_version{host="' . get_site_url() . '"} ' . 	$current->version_checked . "\n";
		
		return $result;
		
	}
	
	
	
	

}
