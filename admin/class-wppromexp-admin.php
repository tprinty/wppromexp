<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.edisonave.com
 * @since      1.0.0
 *
 * @package    Wppromexp
 * @subpackage Wppromexp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wppromexp
 * @subpackage Wppromexp/admin
 * @author     Tom Printy <tprinty@edisonave.com>
 */
class Wppromexp_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wppromexp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wppromexp-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * The admin menus and associated screens
	 *
	 * @since    1.0.0
	 */

	function admin_menu() {
		add_menu_page("", "WP Prometheus Exporter", 'manage_options', 'wppromexp_admin',  array( $this, 'wppromexp_admin'), 'dashicons-performance', 74);
		
		//Actions for the pages above
		$this->add_admin_page( 'wppromexp_settings_process' );
		
	}
	
	
	/**
	 * The admin process options page 
	 *
	 * @since    1.0.0
	 */
	function wppromexp_admin(){
		global $wpdb;
			
		$acess_key = "";
		
		if ( ($access_key = get_option( 'wppromexp_access_key' )) == false ) {
			$access_key = preg_replace("/\./", "", uniqid ("prom", TRUE));
			update_option( 'wppromexp_access_key', $accesskey );
		}
		
		require 'partials/wppromexp-admin-display.php';
	}
	

	/**
	 * The admin process options page 
	 *
	 * @since    1.0.0
	 */
	function wppromexp_settings_process(){
		
		$accesskey = trim($this->admin_post('accesskey'));
		
		if ( get_option( 'wppromexp_access_key' ) !== false ) {
 
		    // The option already exists, so update it.
		    update_option( 'wppromexp_access_key', $accesskey );
		 
		} else {
		 
		    // The option hasn't been created yet, so add it with $autoload set to 'no'.
		    $deprecated = null;
		    $autoload = 'no';
		    add_option( 'wppromexp_access_key', $accesskey, $deprecated, $autoload );
		}
		
		wp_redirect($this->admin_link('wppromexp_admin', '', false), 303);
	  	exit;
	
	}
	
	//Helpers
	
   /**
   * A safe alternative to accessing potentially unset $_POST variables.
   *
   * @param string $name 
   * @return mixed
   * @author Robert Kosek, Wood Street Inc.
   */
	function admin_post($name) {
	    // prevents a warning.
	    if(array_key_exists($name, $_POST) === false) {
	      return null;
	    }
	    return is_string($_POST[$name]) ? stripslashes($_POST[$name]) : $_POST[$name];
	}
	
	
	function admin_link($hook, $query_str=false, $echo=true) {
    	$url = admin_url("admin.php?page=${hook}");
	    
	    if($query_str && !empty($query_str))
	      $url .= '&' . (is_string($query_str) ? $query_str : http_build_query($query_str));
	        
	    if($echo)
	      echo $url;
	    else
	      return $url;
	}
	
	function add_admin_page($hook) {
	    global $_registered_pages;
	    $hookname = get_plugin_page_hookname($hook, 'admin.php');
	    if(!empty($hookname)) {
	      add_action($hookname, array($this, $hook));
	      $_registered_pages[$hookname] = true;
	    }
	}
	
}
