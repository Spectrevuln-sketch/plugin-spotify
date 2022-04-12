<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://porto.storymadeid.my.id
 * @since      1.0.0
 *
 * @package    Spotify_Web_Api
 * @subpackage Spotify_Web_Api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Spotify_Web_Api
 * @subpackage Spotify_Web_Api/admin
 * @author     Gerry <storymadeid@gmail.com>
 */
class Spotify_Web_Api_Admin
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Spotify_Web_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Spotify_Web_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


		// wp_enqueue_style( $this->plugin_name . '-carbon-custom', plugin_dir_url( __FILE__ ) . 'css/spotify-web-api-admin.css', array(), $this->version, 'all' );
		// wp_enqueue_style( 'tailwind', 'https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css', array(), '2.2.19', 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Spotify_Web_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Spotify_Web_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/spotify-web-api-admin.js', array('jquery'), $this->version, false);
	}

	public function add_menu_page()
	{
		// add_menu_page(
		// 	'Spotify Web API',
		// 	'Spotify Web API',
		// 	'manage_options',
		// 	'spotify-web-api',
		// 	array( $this, 'spotify_web_api_admin_page' ),
		// 	'dashicons-format-audio',
		// 	3
		// );
	}


	public function spotify_web_api_admin_page()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/spotify-web-api-admin-display.php';
	}

	/** Register Settings Field  */
	public function register_settings()
	{
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/inc/spotify-web-api-carbon-filed.php';
		// register_setting( 'spotify-web-api-settings-group', 'spotify-web-api-settings-groups' );

		// add_settings_section(
		// 	'spotify_web_api_settings',
		// 	__('Spotify Web API Settings', 'spotify-web-api'),
		// 	array( $this, 'spotify_web_api_settings_callback' ),
		// 	'spotify-web-api-settings-group'
		// );

		// add_settings_field(
		// 	'spotify_web_api_client_id',
		// 	__('Client ID', 'spotify-web-api'),
		// 	array( $this, 'spotify_web_api_client_id_callback' ),
		// 	'spotify-web-api-settings-group',
		// 	'spotify_web_api_settings',
		// 	array(
		// 		'label_for' => 'spotify_web_api_client_id',
		// 		'class'     => 'spotify_web_api_client_id',
		// 	)
		// );

		// add_settings_field(
		// 	'spotify_web_api_client_secret',
		// 	__('Client Secret', 'spotify-web-api'),
		// 	array( $this, 'spotify_web_api_client_secret_callback' ),
		// 	'spotify-web-api-settings-group',
		// 	'spotify_web_api_settings',
		// 	array(
		// 		'label_for' => 'spotify_web_api_client_secret',
		// 		'class'     => 'spotify_web_api_client_secret',
		// 	)
		// );

		// add_settings_field(
		// 	'spotify_web_api_redirect_uri',
		// 	__('Redirect URI', 'spotify-web-api'),
		// 	array( $this, 'spotify_web_api_redirect_uri_callback' ),
		// 	'spotify-web-api-settings-group',
		// 	'spotify_web_api_settings',
		// 	array(
		// 		'label_for' => 'spotify_web_api_redirect_uri',
		// 		'class'    => 'spotify_web_api_redirect_uri',
		// 	)
		// );
	}

	/** settings section callback */
	public function spotify_web_api_settings_callback()
	{
		echo '<p>' . __('Pleas', 'spotify-web-api') . '</p>';
	}

	/** client id settings field */
	public function spotify_web_api_client_id_callback()
	{
		$client_id = get_option('spotify_web_api_client_id');
		echo '<input type="text" name="spotify_web_api_client_id" value="' . $client_id . '" class="regular-text" />';
	}
	/** client secret settings field */
	public function spotify_web_api_client_secret_callback()
	{
		$client_secret = get_option('spotify_web_api_client_secret');
		echo '<input type="text" name="spotify_web_api_client_secret" value="' . $client_secret . '" class="regular-text" />';
	}
	/** redirect uri settings field */
	public function spotify_web_api_redirect_uri_callback()
	{
		$redirect_uri = get_option('spotify_web_api_redirect_uri');
		echo '<input type="text" name="spotify_web_api_redirect_uri" value="' . $redirect_uri . '" class="regular-text" />';
	}
}
