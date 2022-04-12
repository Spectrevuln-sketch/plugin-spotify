<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://porto.storymadeid.my.id
 * @since      1.0.0
 *
 * @package    Spotify_Web_Api
 * @subpackage Spotify_Web_Api/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Spotify_Web_Api
 * @subpackage Spotify_Web_Api/includes
 * @author     Gerry <storymadeid@gmail.com>
 */
class Spotify_Web_Api
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Spotify_Web_Api_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('SPOTIFY_WEB_API_VERSION')) {
			$this->version = SPOTIFY_WEB_API_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'spotify-web-api';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Spotify_Web_Api_Loader. Orchestrates the hooks of the plugin.
	 * - Spotify_Web_Api_i18n. Defines internationalization functionality.
	 * - Spotify_Web_Api_Admin. Defines all hooks for the admin area.
	 * - Spotify_Web_Api_Public. Defines all hooks for the public side of the site.
	 * - Spotify_Web_Api_Fields. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-spotify-web-api-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-spotify-web-api-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-spotify-web-api-admin.php';
		/**
		 * The class responsible for defining Custom Fields.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-spotify-web-api-fields.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-spotify-web-api-public.php';

		$this->loader = new Spotify_Web_Api_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Spotify_Web_Api_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Spotify_Web_Api_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Spotify_Web_Api_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_menu_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');

		$plugin_custom_field = new Spotify_Web_Api_Fields($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_custom_field, 'style_carbon');
		// $this->loader->add_action('wp_enqueue_scripts', $plugin_custom_field, 'scripts_spotify_web_api');
		$this->loader->add_action('wp_head', $plugin_custom_field, 'style_carbon_fields');
		$this->loader->add_action('after_setup_theme',				$plugin_custom_field, 'load_carbon_fields', 		 		1);
		$this->loader->add_action('carbon_fields_register_fields',	$plugin_custom_field, 'register_fields', 		 		10);
		$this->loader->add_filter('spotify-web-api/general/container',		$plugin_custom_field, 'get_container', 			 		999);
		$this->loader->add_filter('spotify-web-api/general/fields',		$plugin_custom_field, 'set_spotify_web_api_general_fields', 		9999);
		$this->loader->add_action('carbon_fields_fields_registered', $plugin_custom_field, 'login_spotify_button');
		$this->loader->add_action('carbon_fields_fields_registered', $plugin_custom_field, 'enable_login_as_spotify');
		$this->loader->add_action('rest_api_init', $plugin_custom_field, 'callback_spotify');
		$this->loader->add_action('carbon_fields_fields_registered', $plugin_custom_field, 'shortcode_artist');
		$this->loader->add_action('wp_ajax_go_album', $plugin_custom_field, 'ajax_go_album');
		$this->loader->add_action('wp_ajax_nopriv_go_album', $plugin_custom_field, 'ajax_go_album');
		$this->loader->add_action('wp_ajax_play_now', $plugin_custom_field, 'ajax_play_now');
		$this->loader->add_action('wp_ajax_nopriv_play_now', $plugin_custom_field, 'ajax_play_now');
		$this->loader->add_action('wp_ajax_submit_find_data', $plugin_custom_field, 'ajax_submit_find_data');
		$this->loader->add_action('wp_ajax_nopriv_submit_find_data', $plugin_custom_field, 'ajax_submit_find_data');
		$this->loader->add_action('wp_ajax_search_track', $plugin_custom_field, 'search_track_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_track', $plugin_custom_field, 'search_track_ajax');
		$this->loader->add_action('wp_ajax_search_podcast', $plugin_custom_field, 'search_podcast_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_podcast', $plugin_custom_field, 'search_podcast_ajax');
		$this->loader->add_action('wp_ajax_search_genres', $plugin_custom_field, 'search_genres_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_genres', $plugin_custom_field, 'search_genres_ajax');
		$this->loader->add_action('wp_ajax_search_top', $plugin_custom_field, 'search_top_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_top', $plugin_custom_field, 'search_top_ajax');
		$this->loader->add_action('wp_ajax_search_top_artist', $plugin_custom_field, 'search_top_artist_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_top_artist', $plugin_custom_field, 'search_top_artist_ajax');
		$this->loader->add_action('wp_ajax_search_new', $plugin_custom_field, 'search_new_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_new', $plugin_custom_field, 'search_new_ajax');
		$this->loader->add_action('wp_ajax_search_chart', $plugin_custom_field, 'search_chart_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_chart', $plugin_custom_field, 'search_chart_ajax');

		/** Detail Followed */
		$this->loader->add_action('wp_ajax_get_followed_artist', $plugin_custom_field, 'get_followed_artist_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_followed_artist', $plugin_custom_field, 'get_followed_artist_ajax');

		/** Detail Artist */
		$this->loader->add_action('wp_ajax_get_current_track', $plugin_custom_field, 'get_current_track_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_current_track', $plugin_custom_field, 'get_current_track_ajax');

		/** Detail Artist */
		$this->loader->add_action('wp_ajax_get_top_track_detail', $plugin_custom_field, 'get_top_track_detail_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_top_track_detail', $plugin_custom_field, 'get_top_track_detail_ajax');
		$this->loader->add_action('wp_ajax_get_my_top_artist', $plugin_custom_field, 'get_my_top_artist_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_my_top_artist', $plugin_custom_field, 'get_my_top_artist_ajax');
		$this->loader->add_action('wp_ajax_get_saved_track', $plugin_custom_field, 'get_saved_track_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_saved_track', $plugin_custom_field, 'get_saved_track_ajax');
		$this->loader->add_action('wp_ajax_get_saved_album', $plugin_custom_field, 'get_saved_album_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_saved_album', $plugin_custom_field, 'get_saved_album_ajax');
		$this->loader->add_action('wp_ajax_get_podcast', $plugin_custom_field, 'get_podcast_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_podcast', $plugin_custom_field, 'get_podcast_ajax');
		$this->loader->add_action('wp_ajax_get_release', $plugin_custom_field, 'get_release_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_release', $plugin_custom_field, 'get_release_ajax');
		$this->loader->add_action('wp_ajax_search_playlist', $plugin_custom_field, 'search_playlist_ajax');
		$this->loader->add_action('wp_ajax_nopriv_search_playlist', $plugin_custom_field, 'search_playlist_ajax');
		$this->loader->add_action('wp_ajax_get_playlist', $plugin_custom_field, 'get_playlist_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_playlist', $plugin_custom_field, 'get_playlist_ajax');
		$this->loader->add_action('wp_ajax_get_spotify_playlist', $plugin_custom_field, 'get_spotify_playlist_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_spotify_playlist', $plugin_custom_field, 'get_spotify_playlist_ajax');
		$this->loader->add_action('wp_ajax_get_playlist_detail', $plugin_custom_field, 'get_playlist_detail_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_playlist_detail', $plugin_custom_field, 'get_playlist_detail_ajax');
		$this->loader->add_action('wp_ajax_get_genre_data', $plugin_custom_field, 'get_genre_data_ajax');
		$this->loader->add_action('wp_ajax_nopriv_get_genre_data', $plugin_custom_field, 'get_genre_data_ajax');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Spotify_Web_Api_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Spotify_Web_Api_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
