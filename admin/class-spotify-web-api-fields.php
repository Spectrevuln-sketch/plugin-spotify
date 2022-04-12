<?php

use Carbon_Fields\Field\Field;
use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container\Container;


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
class Spotify_Web_Api_Fields
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

	/**
	 * initialize spotify-web-api
	 * 
	 */
	private $accessToken;

	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_path = SPOTIFY_DIR_2;
		require SPOTIFY_DIR . '/vendor/autoload.php';
		/** instance Sprotify */
		$client_id = get_option('_spotify-web-api_client_id');
		$client_secret = get_option('_spotify-web-api_client_secret');
		$redirect = get_option('_spotify-web-api_client_redirect');
		if (!empty($_COOKIE['spotify-web-api_refresh_token'])) {

			$session = new SpotifyWebAPI\Session(
				$client_id,
				$client_secret,
				$redirect !== '' ? $redirect : get_site_url()
			);
			$session->refreshAccessToken($_COOKIE['spotify-web-api_refresh_token']);
			$this->accessToken = $session->getAccessToken();
		}

		$this->show_shortcode_ui_advance_api_spotify();
		add_shortcode('' . $this->plugin_name . '_play_now', array($this, 'shortcode_play_now_callback'));
		add_shortcode('' . $this->plugin_name . '_my_playlist', array($this, 'shortcode_playlist_callback'));
		add_shortcode('' . $this->plugin_name . '_my_toptrack', array($this, 'shortcode_toptrack_callback'));
		add_shortcode('' . $this->plugin_name . '_my_topartist', array($this, 'shortcode_topartist_callback'));
		add_shortcode('' . $this->plugin_name . '_save_track', array($this, 'shortcode_savetrack_callback'));
		add_shortcode('' . $this->plugin_name . '_save_album', array($this, 'shortcode_savealbum_callback'));
		add_shortcode('' . $this->plugin_name . '_followed_artist', array($this, 'shortcode_followed_artist_callback'));
		add_shortcode('' . $this->plugin_name . '_realise_radar', array($this, 'shortcode_realise_radar_callback'));
		add_shortcode('' . $this->plugin_name . '_podcast', array($this, 'shortcode_podcast_callback'));
		add_shortcode('' . $this->plugin_name . '_genres', array($this, 'shortcode_genres_callback'));
		add_shortcode('' . $this->plugin_name . '_release', array($this, 'shortcode_release_callback'));
		add_shortcode('' . $this->plugin_name . '_top_chart', array($this, 'shortcode_top_chart_callback'));
		add_shortcode('' . $this->plugin_name . '_spotify_playlist', array($this, 'shortcode_spotify_playlist_callback'));
	}

	public function load_carbon_fields()
	{
		require SPOTIFY_DIR . '/vendor/autoload.php';
		\Carbon_Fields\Carbon_Fields::boot();
	}


	/**
	 * Register the stylesheets for the admin area.
	 * @since 1.0.0
	 */

	public function style_carbon()
	{
		wp_enqueue_style($this->plugin_name . '-carbon-custom', plugin_dir_url(__FILE__) . 'css/spotify-web-api-admin.css', array(), $this->version, 'all');
		wp_enqueue_style('carbon-fields-styles', 'https://cdn.jsdelivr.net/npm/carbon-components@latest/css/carbon-components.min.css');
	}

	public function style_carbon_fields()
	{
		wp_enqueue_style('tailwind', 'https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css', array(), '2.2.19', 'all');
	}

	public function scripts_spotify_web_api()
	{
		// wp_enqueue_script($this->plugin_name . 'artist_scripts', plugin_dir_url(__FILE__) . 'js/spotify-web-api-artist.js', array('jquery'), $this->version, false);
	}



	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */

	public function register_fields()
	{
		$fields = apply_filters('spotify-web-api/general/fields', []);
		if (is_array($fields) && 0 < count($fields)) :

			$this->container = Container::make('theme_options', __('Spotify Web API', 'spotify-web-api'))
				->set_icon(plugin_dir_url(__FILE__) . 'images/spotify-web-api-icon.png')
				->set_page_menu_position(3)
				->set_classes('spotify-web-api-metabox');

			foreach ($fields as $field) :
				$this->container->add_tab($field['title'], $field['fields']);
			endforeach;

		endif;
	}

	// ARTIST ALBUMS
	/**
	 * artist shortcode callback
	 */
	public function shortcode_artist_callback($atts, $content, $tag)
	{
		if (!empty($_COOKIE['spotify-web-api_access_token'])) {
			ob_start();
			echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

			require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-artist-album.php');
			echo "<script src='" . SPOTIFY_URL . "admin/js/spotify-web-api-artist.js'></script>";
			return ob_get_clean();
		} else {
			return '<p>Please login first</p>';
		}
	}

	/** shortcode play now */
	public function shortcode_play_now_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token'])) {
			ob_start();
			echo "<link rel='stylesheet' href='" . SPOTIFY_URL . "admin/css/style-player.css'  />";
			echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>';
			require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-play-now.php');

			return ob_get_clean();
		} else {
			return '<p>Please login first</p>';
		}
	}




	/** ajax go to current album */
	public function ajax_go_album()
	{

		try {
			$api = new SpotifyWebAPI\SpotifyWebAPI();

			$accessTokenCookie = $_COOKIE['spotify-web-api_access_token'];
			$api->setAccessToken($accessTokenCookie);
			$track = $api->getAlbumTracks($_POST['id']);
			// $redirect_url = get_option('_play_now_uri');
			$device = $api->getMyDevices();

			if ($track) {
				$payload = [
					'data' => $track,
					'me' => $api->me(),
					'my_devices' => $device
				];
				echo json_encode($payload);
				die();
			}

			/** insert to playlist */
			// $options = [
			// 	'limit' => 50,
			// ];
			// $myPlaylist = $api->getMyPlaylists($options);
			// var_dump($myPlaylist);
			// die;
			// if ($myPlaylist->total > 0) {
			// 	$api->
			// }
		} catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
			if ($e) {

				$client_id = get_option('_spotify-web-api_client_id');
				$client_secret = get_option('_spotify-web-api_client_secret');
				$redirect = get_option('_spotify-web-api_client_redirect');
				$refreshToken = $_COOKIE['spotify-web-api_refresh_token'];
				$session = new SpotifyWebAPI\Session(
					$client_id,
					$client_secret,
					$redirect !== '' ? $redirect : get_site_url()
				);
				$session->refreshAccessToken($refreshToken);
				$accessToken = $session->getAccessToken();
				$api->setAccessToken($accessToken);
				setcookie('spotify-web-api_access_token', $accessToken, time() + (86400 * 7), "/");
				do_action('ajax_go_album', $_POST['id']);
			}
		}
	}


	// END ARTIST ALBUM
	/**
	 * album shortcode callback
	 */
	public function shortcode_album_callback($atts, $content, $tag)
	{

		if ($_COOKIE['spotify-web-api_access_token']) {
			ob_start();
			echo "<link rel='stylesheet' href='" . SPOTIFY_URL . "admin/css/style-player.css'  />";
			echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>';
			require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-album.php');
			echo '<script src="' . SPOTIFY_URL . 'admin/js/script-player.js"></script>';
			echo '<scrpit src="' . SPOTIFY_URL . 'admin/js/spotify-web-api-artist.js"></scrpit>';
			return ob_get_clean();
		}
	}

	/**
	 * Shortcode find artist
	 */
	public function shortcode_artist()
	{

		$artists_shortcode = carbon_get_theme_option('spotify-web-api_shortcode_artist');

		if ($artists_shortcode) {

			foreach ($artists_shortcode as $as) :
				$shortCode = str_replace(['[', ']'], '', $as['shortcode_artist']);
				add_shortcode($shortCode, array($this, 'shortcode_artist_callback'));
			endforeach;
		}
	}



	/**
	 * Button View
	 */
	public function button_view()
	{
		/** diharuskan menggunakan _ di awal  pada saat mengambil carbon themes option data */
		$radius = get_option('_button_radius');
		$background_color = get_option('_bg_color_button');
		$text_color = get_option('_text_color_button');

		$button_login = '
		<style>
		.spotify_button_login{
			border-radius:' . $radius . 'px;
			background-color:' . $background_color . ';
			color:' . $text_color . ';
			border:none;
			padding:10px;
			display:flex;
			direction:row;
			justify-content:space-between;
			align-items:center;
		}
		
		.spotify-image{
			width:20px;
			height:20px;
			margin-right:10px;
		}
		</style>
		<button class="spotify_button_login">
		<img src="https://www.freeiconspng.com/uploads/spotify-icon-2.png" class="spotify-image"/>
		Login With Spotify
		</button>
		';
		return $button_login;
	}

	/**
	 * shortcode api spotify
	 */
	public function shortcode_box()
	{
		ob_start();
		require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-box.php');
		echo "<script src='" . SPOTIFY_URL . "admin/js/spotify-web-api-box.js'></script>";
		return ob_get_clean();
	}


	/**
	 * Create New Field For Bridge Rest API
	 * 
	 */
	public function set_spotify_web_api_general_fields(array $fields)
	{
		$fields['main']	= array(
			'title'  => __('Setup Awal', 'spotify-web-api'),
			'fields' => array(
				Field::make('separator', 'sep_general_display_data_in_front_end', __('Setting Sesuai Kebutuhan Anda', 'spotify-web-api')),

				Field::make('html', 	 'html_general_display_data_in_front_end')
					->set_html(
						__('<strong>Note: </strong>Diharuskan Mendaftar Terlebih Dahulu di ', 'spotify-web-api')
					),

				// Field::make('checkbox', 'spotify-web-api_client_id', __('Kirimkan Data User Sejoli', 'spotify-web-api')),
				Field::make('text', 'spotify-web-api_client_id', __('Client ID Spotify', 'spotify-web-api'))->set_width(5),
				Field::make('text', 'spotify-web-api_client_secret', __('Client Secret Spotify', 'spotify-web-api'))->set_width(5),
				Field::make('text', 'spotify-web-api_client_redirect', __('Redirect Uri', 'spotify-web-api')),
			)
		);

		/** Login Button Spotify */
		$fields['setup_view']	= array(
			'title'  => __('Setup Button UI', 'spotify-web-api'),
			'fields' => array(
				Field::make('separator', 'setup_ui', __('Settings Button UI', 'spotify-web-api')),
				Field::make('html', 	 'html_setup_shortcode_login')
					->set_html(
						__('<strong>Short Code Untuk Button Login: </strong>[login_spotify] ', 'spotify-web-api')
					),
				Field::make('html', 	 'button_view')
					->set_html(
						__('' . $this->button_view() . '', 'spotify-web-api')
					),
				Field::make('text', 'redirect_url_login', __('Redirect Url', 'spotify-web-api'))->set_width(5),
				Field::make('checkbox', 'enable_login_button', __('Aktifkan Login Button', 'spotify-web-api')),
				Field::make('text', 'button_radius', __('Border Radius Button', 'spotify-web-api'))->set_width(5),
				Field::make('color', 'bg_color_button', __('Background Button', 'spotify-web-api'))->set_width(5),
				Field::make('color', 'text_color_button', __('Text Color Button', 'spotify-web-api'))->set_width(5),
			)
		);

		/** Find By Artist */
		$fields['setup_artist']	= array(
			'title'  => __('Setup ShortCode Artist', 'spotify-web-api'),
			'fields' => array(
				Field::make('separator', 'setup_find_by_artist', __('Setup Artist', 'spotify-web-api')),
				Field::make('html', 	 'html_shortcode_artist')
					->set_html(
						__('<strong>Untuk Mengisi Shortcode (spasi) digantikan <code>_</code>: </strong> <code>[nama_artist]</code> ', 'spotify-web-api')
					),
				// Field::make('html', 	 'html_shortcode_play_now')
				// 	->set_html(
				// 		__('<strong>Tambahkan Kode Berikut Ke Halaman Untuk Menampilkan Halaman Setelah Klik Album Pada Artis: </strong> <code>[' . $this->plugin_name . '_play_now]</code> ', 'spotify-web-api')
				// 	),
				// Field::make('text', 'play_now_uri', __('Link Halaman Play Now', 'spotify-web-api'))->set_width(5),
				Field::make('complex', 'spotify-web-api_shortcode_artist', __('Buat Short Code Artis', 'spotify-web-api'))
					->add_fields(array(
						Field::make('text', 'artist_name', __('Nama Artis', 'spotify-web-api'))->set_width(5),
						Field::make('text', 'shortcode_artist', __('Short Code', 'spotify-web-api'))->set_width(5),
					))
			)
		);



		/** Setup Your Playlist */
		// $fields['setup_api_spotify']	= array(
		// 	'title'  => __('Advance Setup Api Spotify', 'spotify-web-api'),
		// 	'fields' => array(
		// 		Field::make('separator', 'tab_title_advance_api_spotify', __('Page Maker', 'spotify-web-api')),
		// 		Field::make('html', 	 'html_shortcode_advance_api_spotify')
		// 			->set_html(
		// 				__('<strong>Untuk Menampilkan Ui: </strong> <code>[show_spotify]</code> ', 'spotify-web-api')
		// 			),
		// 		Field::make('html', 	 'interface_myplaylist')
		// 			->set_html(__([$this, 'shortcode_box'], 'spotify-web-api')),
		// 		Field::make('checkbox', 'enable_box', __('Enable Box API', 'spotify-web-api')),
		// 		Field::make('image', 'file_image_api', __('Logo Image', 'spotify-web-api')),
		// 		Field::make('color', 'bg_box_ui', __('Background BOX', 'spotify-web-api'))->set_width(25),
		// 		Field::make('color', 'text_color_ui', __('Text Color', 'spotify-web-api'))->set_width(25),
		// 		Field::make('text', 'width_box', __('Width', 'spotify-web-api'))->set_width(5),
		// 		Field::make('text', 'height_box', __('Height', 'spotify-web-api'))->set_width(5),
		// 		Field::make('text', 'radius_box', __('Radius', 'spotify-web-api'))->set_width(5),
		// 		Field::make('html', 	 'rule_radius')
		// 			->set_html(
		// 				__('<span style="font-style:italic; color:red;">* Radius Tidak Boleh Lebih Dari 20 </span>', 'spotify-web-api')
		// 			),
		// 		Field::make('html', 	 'tab_title_button_api')
		// 			->set_html(
		// 				__('<h4>Button Api Style</h4>', 'spotify-web-api')
		// 			),
		// 		Field::make('color', 'bg_button_ui', __('Background Button', 'spotify-web-api'))->set_width(25),
		// 		Field::make('color', 'text_button_color', __('Text Color', 'spotify-web-api'))->set_width(25),
		// 		Field::make('text', 'width_button', __('Width', 'spotify-web-api'))->set_width(5),
		// 		Field::make('text', 'height_button', __('Height', 'spotify-web-api'))->set_width(5),
		// 		Field::make('text', 'radius_button', __('Radius', 'spotify-web-api'))->set_width(5),
		// 	)
		// );
		/** Setup page shortcode maker */
		$fields['page_maker_template']	= array(
			'title'  => __('Page Maker Template', 'spotify-web-api'),
			'fields' => array(
				/** playlist Field */
				Field::make('separator', 'tab_title_page_maker', __('Page Maker', 'spotify-web-api')),
				Field::make('html', 	 'notif_shortcode_playlist')
					->set_html(
						__('<strong>Untuk Menampilkan playlist: </strong> <code>[' . $this->plugin_name . '_my_playlist]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_myplaylist', __('Enable Page My Playlist', 'spotify-web-api')),
				Field::make('text', 'number_grid_playlist', __('Grid Content playlist', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_playlist', __('Background Template', 'spotify-web-api'))->set_width(5),
				/** End playlist Field */
				/** TopTrack Field */
				Field::make('html', 	 'shortcode_my_toptracks')
					->set_html(
						__('<strong>Untuk Menampilkan Toptrack Akun: </strong> <code>[' . $this->plugin_name . '_my_toptrack]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_toptrack', __('Enable Page My Top Track', 'spotify-web-api')),
				Field::make('text', 'number_grid_toptrack', __('Grid Top Track', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_track', __('Background Template', 'spotify-web-api'))->set_width(5),
				/** End TopTrack Field */
				/** Top Artist */
				Field::make('html', 	 'shortcode_my_topartist')
					->set_html(
						__('<strong>Untuk Menampilkan Top Artist Akun: </strong> <code>[' . $this->plugin_name . '_my_topartist]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_topartist', __('Enable Page My Top Artist', 'spotify-web-api')),
				Field::make('text', 'number_grid_topartist', __('Grid Top Track', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_topartist', __('Background Template', 'spotify-web-api'))->set_width(5),
				/** End Top Artist */

				/** Sava Tracks */
				Field::make('html', 	 'shortcode_save_tracks')
					->set_html(
						__('<strong>Untuk Menampilkan Save Track Akun: </strong> <code>[' . $this->plugin_name . '_save_track]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_savetrack', __('Enable Page My Saved Track', 'spotify-web-api')),
				Field::make('text', 'number_grid_savetrack', __('Grid Top Track', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_savetrack', __('Background Save Track', 'spotify-web-api'))->set_width(5),
				/** End Sava Tracks */
				/** Save Album */
				Field::make('html', 	 'shortcode_save_album')
					->set_html(
						__('<strong>Untuk Menampilkan Save Album Akun: </strong> <code>[' . $this->plugin_name . '_save_album]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_savealbum', __('Enable Page My Saved Album', 'spotify-web-api')),
				Field::make('color', 'bg_box_savealbum', __('Background Save Album', 'spotify-web-api'))->set_width(5),
				/** End Save Album */
				Field::make('html', 	 'shortcode_followed_artists')
					->set_html(
						__('<strong>Untuk Menampilkan Followed Artist: </strong> <code>[' . $this->plugin_name . '_followed_artist]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_followed_artist', __('Enable Page Followed Artist', 'spotify-web-api')),

				/** Realese Radar */
				// Field::make('html', 	 'shortcode_realise_radar')
				// 	->set_html(
				// 		__('<strong>Untuk Menampilkan Realise Radar: </strong> <code>[' . $this->plugin_name . '_realise_radar]</code> ', 'spotify-web-api')
				// 	),
				// Field::make('checkbox', 'enable_realise_radar', __('Enable Page Realise Radar', 'spotify-web-api')),
				/** End Realese Radar */

				/** PodCast  */
				Field::make('html', 	 'shortcode_podcast')
					->set_html(
						__('<strong>Untuk Menampilkan PodCast: </strong> <code>[' . $this->plugin_name . '_podcast]</code> ', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_podcast', __('Enable Page Podcast', 'spotify-web-api')),
				Field::make('text', 'number_grid_podcast', __('Grid Podcast', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_podcast', __('Background Template podcast', 'spotify-web-api'))->set_width(5),
				/** End End Podcast */

				/** Genres  */
				Field::make('html', 	 'shortcode_genres')
					->set_html(
						__('<strong>Untuk Menampilkan Genres: </strong> <code>[' . $this->plugin_name . '_genres]</code>', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_genres', __('Enable Page genres', 'spotify-web-api')),
				Field::make('text', 'number_grid_genres', __('Grid genres', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_genres', __('Background Template genres', 'spotify-web-api'))->set_width(5),
				/** End End Genres */

				/** New Release  */
				Field::make('html', 	 'shortcode_release')
					->set_html(
						__('<strong>Untuk Menampilkan release: </strong> <code>[' . $this->plugin_name . '_release]</code>', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_release', __('Enable Page release', 'spotify-web-api')),
				Field::make('text', 'number_grid_release', __('Grid release', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_release', __('Background Template release', 'spotify-web-api'))->set_width(5),
				/** End End Release */


				/** Top Chart  */
				Field::make('html', 	 'shortcode_top_chart')
					->set_html(
						__('<strong>Untuk Menampilkan Top Chart: </strong> <code>[' . $this->plugin_name . '_top_chart]</code>', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_top_chart', __('Enable Page Top Chart', 'spotify-web-api')),
				Field::make('text', 'number_grid_top_chart', __('Grid Top Chart', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_top_chart', __('Background Template Top Chart', 'spotify-web-api'))->set_width(5),
				/** End End Top Chart */

				/** Spotify Playlist  */
				Field::make('html', 	 'shortcode_spotify_playlist')
					->set_html(
						__('<strong>Untuk Menampilkan Spotify Playlist: </strong> <code>[' . $this->plugin_name . '_spotify_playlist]</code>', 'spotify-web-api')
					),
				Field::make('checkbox', 'enable_spotify_playlist', __('Enable Page Spotify Playlist', 'spotify-web-api')),
				Field::make('text', 'number_grid_spotify_playlist', __('Grid Spotify Playlist', 'spotify-web-api'))
					->set_attributes([
						'type' => 'number',
					])->set_width(5),
				Field::make('color', 'bg_box_spotify_playlist', __('Background Template Spotify Playlist', 'spotify-web-api'))->set_width(5),
				/** End End Spotify Playlist */


			)
		);



		return $fields;
	}





	/** Ajax search_new_ajax */

	public function search_chart_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$TopChart = $api->getCategoryPlaylists('toplists', [
			'limit' => 20,
			'offset' => 0,
		]);
		$tracks = $TopChart->playlists->items;
		// $search_query = $api->search($_POST['search_track'], 'track');
		$post_data = $_POST['search_chart'];
		$search = [
			'search' => $post_data,
			'track' => $tracks
		];

		echo json_encode($search);
		die;
	}

	/** Ajax search_new_ajax */

	public function search_new_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$newRelease = $api->getNewReleases();
		$tracks = $newRelease->albums->items;
		// $search_query = $api->search($_POST['search_track'], 'track');
		$post_data = $_POST['search_new'];
		$search = [
			'search' => $post_data,
			'track' => $tracks
		];

		echo json_encode($search);
		die;
	}




	/** Ajax ajax_submit_find_data */
	public function ajax_submit_find_data()
	{
		try {
			$api = new SpotifyWebAPI\SpotifyWebAPI();
			$accessTokenCookie = $_COOKIE['spotify-web-api_access_token'];
			$api->setAccessToken($accessTokenCookie);
			$me = $api->me();
			$data_name = $_POST['name'];
			if ($data_name == 'your_playlist') {
				$myPlaylist = $api->getMyPlaylists();
				echo json_encode($myPlaylist);
				die();
			}
		} catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
			if ($e) {

				$client_id = get_option('_spotify-web-api_client_id');
				$client_secret = get_option('_spotify-web-api_client_secret');
				$redirect = get_option('_spotify-web-api_client_redirect');
				$refreshToken = $_COOKIE['spotify-web-api_refresh_token'];
				$session = new SpotifyWebAPI\Session(
					$client_id,
					$client_secret,
					$redirect !== '' ? $redirect : get_site_url()
				);
				$session->refreshAccessToken($refreshToken);
				$accessToken = $session->getAccessToken();
				$api->setAccessToken($accessToken);
				setcookie('spotify-web-api_access_token', $accessToken, time() + (86400 * 7), "/");
				do_action('ajax_go_album', $_POST['id']);
			}
		}
	}



	/** Ajax Play Now */
	public function ajax_play_now()
	{
		try {

			$api = new SpotifyWebAPI\SpotifyWebAPI();

			$accessTokenCookie = $_COOKIE['spotify-web-api_access_token'];
			$api->setAccessToken($accessTokenCookie);
			$api->pause($_POST['device_id']);
			$play_data = $api->play($_POST['device_id']);
			var_dump($play_data);
		} catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
			if ($e) {

				$client_id = get_option('_spotify-web-api_client_id');
				$client_secret = get_option('_spotify-web-api_client_secret');
				$redirect = get_option('_spotify-web-api_client_redirect');
				$refreshToken = $_COOKIE['spotify-web-api_refresh_token'];
				$session = new SpotifyWebAPI\Session(
					$client_id,
					$client_secret,
					$redirect !== '' ? $redirect : get_site_url()
				);
				$session->refreshAccessToken($refreshToken);
				$accessToken = $session->getAccessToken();
				$api->setAccessToken($accessToken);
				setcookie('spotify-web-api_access_token', $accessToken, time() + (86400 * 7), "/");
				do_action('ajax_go_album', $_POST['id']);
			}
		}
	}

	/** Ajax search_genres_ajax */
	public function search_genres_ajax()
	{


		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$track_id = [];
		$track = $api->search($_POST['genre'] ? $_POST['genre'] : 'p', 'track', [
			'limit' =>  50,
		]);
		$genres = $api->getGenreSeeds();
		foreach ($track->tracks->items as $key => $value) {
			$track_id[] = $value->id;
		}
		$tracks = $api->getTracks($track_id, [
			'limit' =>  50,
		]);


		$many_tracks = [
			'tracks' => $tracks->tracks,
			'genres' => $genres->genres
		];


		$search = $many_tracks;
		echo json_encode($search);
		die;
	}

	public function get_genre_data_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$track_data = $api->getTrack($_POST['id']);
		$artist_data = $api->getArtist($track_data->artists[0]->id);
		$top_track = $api->getArtistTopTracks($artist_data->id, [
			'country' => 'ID'
		]);
		$albumArtist = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'album',
			'limit' => 50
		]);

		$compalation = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'single,compilation,appears_on',
			'limit' => 50
		]);
		$related_artist = $api->getArtistRelatedArtists($artist_data->id);
		$data = [
			'track_data' => $track_data,
			'artist_data' => $artist_data,
			'top_track' => $top_track->tracks,
			'album_artist' => $albumArtist->items,
			'compalation_artist' => $compalation->items,
			'related_artist' => $related_artist->artists
		];
		echo json_encode($data);
		die;
	}



	/* ------------------------- TEMPLATE SHORTCODE PAGE ------------------------ */
	public function shortcode_spotify_playlist_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_spotify_playlist = get_option('_enable_spotify_playlist');
			if (!empty($enable_spotify_playlist)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-spotify-playlist.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}
	/** shortcode shortcode_top_chart_callback */
	public function shortcode_top_chart_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_top_chart = get_option('_enable_top_chart');
			if (!empty($enable_top_chart)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-top-chart.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}
	/** shortcode shortcode_release_callback */
	public function shortcode_release_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_release = get_option('_enable_release');
			if (!empty($enable_release)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-release.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}




	/** shortcode to save followed Artist */
	public function shortcode_genres_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_genres = get_option('_enable_genres');
			if (!empty($enable_genres)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-genres.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}

	/** shortcode to save followed Artist */
	public function shortcode_followed_artist_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_followed_artist = get_option('_enable_followed_artist');
			if (!empty($enable_followed_artist)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-followed.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}

	/** shortcode to save album */
	public function shortcode_savealbum_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_savealbum = get_option('_enable_savealbum');
			if (!empty($enable_savealbum)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-mysavealbum.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}

	/** Short code Realise Radar */
	public function shortcode_realise_radar_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_realise_radar = get_option('_enable_realise_radar');
			if (!empty($enable_realise_radar)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-realise-radar.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}

	/** Short code Podcast */
	public function shortcode_podcast_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_podcast = get_option('_enable_podcast');
			if (!empty($enable_podcast)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';
				echo '<script src="https://open.spotify.com/embed-podcast/iframe-api/v1" async></script>';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-podcast.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}

	/** ajax search_top_artist_ajax */
	public function search_top_artist_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$mytopartist = $api->getMyTop('artists');
		$tracks = $mytopartist->items;
		// $search_query = $api->search($_POST['search_track'], 'track');
		$post_data = $_POST['search_top'];
		$search = [
			'search' => $post_data,
			'track' => $tracks
		];

		echo json_encode($search);
		die;
	}


	/** Ajax Search Top Track */

	public function search_top_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$currentArtist = $api->getArtist('id');
		$tracks = $currentArtist->items;
		// $search_query = $api->search($_POST['search_track'], 'track');
		$post_data = $_POST['search_top'];
		$search = [
			'search' => $post_data,
			'track' => $tracks
		];

		echo json_encode($search);
		die;
	}



	/** Ajax Serach Podcast */
	public function search_podcast_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$podcast = $api->search($_POST['search_podcast'] ? $_POST['search_podcast'] : 'p', 'show', [
			'limit' => (int)$_POST['offset'] ? (int)$_POST['offset'] : 20,
			'market' => 'ID'
		]);
		$items = $podcast->shows->items;
		$search = [
			'podcast' => $items,
			'search' => $_POST['search_podcast'] ? $_POST['search_podcast'] : '',
		];
		echo json_encode($search);
		die;
	}

	/** Ajax Search Track */
	public function search_track_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$mySavedTrack = $api->getMySavedTracks();
		$tracks = $mySavedTrack->items;
		// $search_query = $api->search($_POST['search_track'], 'track');
		$post_data = $_POST['search_track'];
		$search = [
			'search' => $post_data,
			'track' => $tracks
		];

		echo json_encode($search);
		die;
	}


	/** shortcode to save track */
	public function shortcode_savetrack_callback()
	{
		if (!empty($_COOKIE['spotify-web-api_access_token']) && !empty($_COOKIE['spotify-web-api_refresh_token'])) {
			$enable_savetrack = get_option('_enable_savetrack');
			if (!empty($enable_savetrack)) {
				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-mysavetrack.php');
				return ob_get_clean();
			}
		} else {
			return 'Please Login First';
		}
	}

	/** Shortcode top artist */
	public function shortcode_topartist_callback()
	{
		if ($_COOKIE['spotify-web-api_access_token']) {
			$enable_topartist = get_option('_enable_topartist');
			if ($enable_topartist) {

				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-topartist.php');
				return ob_get_clean();
			} else {
				return '';
			}
		}
	}

	// Template Shortcode Top Track
	public function shortcode_toptrack_callback()
	{
		if ($_COOKIE['spotify-web-api_access_token']) {
			$enable_toptrack = get_option('_enable_toptrack');
			if ($enable_toptrack) {

				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-toptrack.php');
				return ob_get_clean();
			} else {
				return '';
			}
		}
	}



	//  TEMPLATE SHORTCODE YOURPLAYLIST
	public function shortcode_playlist_callback()
	{
		if ($_COOKIE['spotify-web-api_access_token']) {
			$enable_myplaylist = get_option('_enable_myplaylist');
			if ($enable_myplaylist) {


				ob_start();
				echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.css" integrity="sha512-vA/fpEI8+rrDsPceGG+Rz4NBhaNE4lvJ8CrNfspqDQi6uyIs82Hwr8gm/E+SRs+ZKjJ2ihOdb6esDSAuJrWOhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />';

				require_once(SPOTIFY_DIR . 'admin/partials/spotify-web-api-display-myplaylist.php');
				return ob_get_clean();
			} else {
				return '';
			}
		} else {
			return '<p>Please login first</p>';
		}
	}





	/* -------------------------- ADVANCE SETUP BOX API ------------------------- */
	/** create shortcode if enable box_ui */
	public function show_shortcode_ui_advance_api_spotify()
	{
		$enable_box = get_option('_enable_box');
		if ($enable_box) :
			add_shortcode('show_spotify', [$this, 'shortcode_box']);
		endif;
	}





	/* ------------------------------- LOGIN SETUP ------------------------------ */
	/** function Rest API Spotify Callback */
	public function callback_spotify_api()
	{
		$client_id = get_option('_spotify-web-api_client_id');
		$client_secret = get_option('_spotify-web-api_client_secret');
		$redirect = get_option('_spotify-web-api_client_redirect');

		$session = new SpotifyWebAPI\Session(
			$client_id,
			$client_secret,
			$redirect !== '' ? $redirect : get_site_url()
		);
		$options = [
			'auto_refresh' => true
		];
		$api = new SpotifyWebAPI\SpotifyWebAPI($options, $session);
		$api->setSession($session);
		$state = $_GET['state'];

		// Request a access token using the code from Spotify
		$session->requestCredentialsToken();
		$session->requestAccessToken($_GET['code']);

		$accessToken = $session->getAccessToken();
		$refreshToken = $session->getRefreshToken();

		/** set cookie 1 week */
		setcookie('spotify-web-api_state', $state, time() + (86400 * 7), "/");
		setcookie('spotify-web-api_access_token', $accessToken, time() + (86400 * 7), "/");
		setcookie('spotify-web-api_refresh_token', $refreshToken, time() + (86400 * 7), "/");
		$redirect = get_option('_redirect_url_login');
		wp_redirect($redirect ? $redirect : get_site_url());
		exit;
	}

	/** permission callback spotify api */
	public function permission_callback()
	{
		return true;
	}


	/** Register Callback Rest API */
	public function callback_spotify()
	{
		register_rest_route(
			'spotify-web-api',
			'callback',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'callback_spotify_api'),
				'permission_callback' => array($this, 'permission_callback'),
			)
		);
	}


	/** Show Login Button */
	public function enable_login_as_spotify()
	{
		global $wpdb;
		require SPOTIFY_DIR . '/vendor/autoload.php';
		$client_id = carbon_get_theme_option('spotify-web-api_client_id');
		$client_secret = carbon_get_theme_option('spotify-web-api_client_secret');
		$redirect = carbon_get_theme_option('spotify-web-api_client_redirect');
		$radius = carbon_get_theme_option('button_radius');
		$session = new SpotifyWebAPI\Session(
			$client_id,
			$client_secret,
			$redirect !== '' ? $redirect : get_site_url()
		);

		$state = $session->generateState();
		// https://accounts.spotify.com/en/login?continue=https%3A%2F%2Faccounts.spotify.com%2Fauthorize%3Fscope%3Dplaylist-read-private%2Bplaylist-read-collaborative%2Bplaylist-modify-public%2Bplaylist-modify-private%2Buser-follow-read%2Buser-follow-modify%2Buser-library-read%2Buser-library-modify%2Buser-read-birthdate%2Buser-read-email%2Buser-read-private%2Bstreaming%2Buser-top-read%26response_type%3Dtoken%26redirect_uri%3Dhttps%253A%252F%252Fdiscoverquickly.com%252F%26state%3Drlva771h9MszOlxN%26client_id%3D155b87449b11484bb1a1c93a618b7a21

		$options = [
			// 'auto_refresh' => true,      
			'scope' => [
				'playlist-read-private',
				'playlist-modify-private',
				'playlist-read-collaborative',
				'playlist-modify-public',
				'user-read-currently-playing',
				'user-read-recently-played',
				'user-read-playback-state',
				'user-read-private',
				'user-read-email',
				'user-modify-playback-state',
				'user-library-modify',
				'user-library-read',
				'user-follow-read',
				'user-follow-modify',
				'user-top-read',
				'streaming',
				'app-remote-control'
			],
			'state' => $state,
		];
		/** diharuskan menggunakan _ di awal  pada saat mengambil carbon themes option data */
		$radius = get_option('_button_radius');
		$background_color = get_option('_bg_color_button');
		$text_color = get_option('_text_color_button');
		return '
	<style>
		a.spotify_button_login{
			border-radius:' . $radius . 'px;
			background-color:' . $background_color . ';
			color:' . $text_color . ';
			border:none;
			padding-top:10px;
			padding-bottom:10px;
			padding-left:5px;
			text-decoration:none;
			underline:none;
			display:flex;
			direction:row;
			justify-content:space-evenly;
			align-items:center;
			width:30%;
			font-size:15px;
			font-family: "Roboto", sans-serif;
		}
		
		.spotify-image{
			width:20px;
			height:20px;
			margin-right:5px;
		}
		</style>
		<a href="' .  $session->getAuthorizeUrl($options) . '" class="spotify_button_login">
		<img src="https://www.freeiconspng.com/uploads/spotify-icon-2.png" class="spotify-image"/>
		Login With Spotify
		</a>
	';
	}

	public function login_spotify_button()
	{
		$enable_login_button = carbon_get_theme_option('enable_login_button');
		$client_id = carbon_get_theme_option('spotify-web-api_client_id');
		$client_secret = carbon_get_theme_option('spotify-web-api_client_secret');
		$redirect = carbon_get_theme_option('spotify-web-api_client_redirect');
		if ($enable_login_button === true && !empty($client_id) && !empty($client_secret) && !empty($redirect)) {
			add_shortcode('login_spotify', array($this, 'enable_login_as_spotify'));
		}
		return false;
	}

	/* ----------------------------- END LOGIN SETUP ---------------------------- */


	/* -------------------------- AJAX APPENDED DETAIL -------------------------- */
	public function get_followed_artist_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$searchCurrent = $api->search($_POST['name'], 'artist');
		$followedArtist = $api->getUserFollowedArtists($_POST['id']);
		$currentArtist = $api->getArtist($_POST['id']);
		$topTrackArtist = $api->getArtistTopTracks($_POST['id'], [
			'country' => 'ID'
		]);
		$albumArtist = $api->getArtistAlbums($_POST['id'], [
			'include_groups' => 'album',
			'limit' => 50
		]);

		$compalation = $api->getArtistAlbums($_POST['id'], [
			'include_groups' => 'single,compilation,appears_on',
			'limit' => 50
		]);

		$related_artist = $api->getArtistRelatedArtists($_POST['id']);

		$data = [
			'artist_tracks' => $searchCurrent->artists->items,
			'current_artist' => $currentArtist,
			'top_track_artist' => $topTrackArtist->tracks,
			'album_artist' => $albumArtist->items,
			'followed_artist' => $followedArtist,
			'compalation_artist' => $compalation->items,
			'related_artist' => $related_artist->artists
		];



		echo json_encode($data);
		die;
	}

	public function get_current_track_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$get_playlist_items = $api->getPlaylistTracks($_POST['id'], [
			'fields' => 'items(track(name,album(name,images,id),artists(name,id),id,duration_ms,uri))',
			'limit' => 50
		]);
		$get_current_category = $api->getPlaylist($_POST['id']);
		$track_playlist = $get_playlist_items->items;

		$data = [
			'track_playlist' => $track_playlist,
			'current_category' => $get_current_category
		];

		echo json_encode($data);
		die;
	}

	public function get_top_track_detail_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$track_data = $api->getTrack($_POST['id']);
		$artist_data = $api->getArtist($track_data->artists[0]->id);
		$top_track = $api->getArtistTopTracks($artist_data->id, [
			'country' => 'ID'
		]);
		$albumArtist = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'album',
			'limit' => 50
		]);

		$compalation = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'single,compilation,appears_on',
			'limit' => 50
		]);
		$data = [
			'track_data' => $track_data,
			'artist_data' => $artist_data,
			'top_track' => $top_track->tracks,
			'album_artist' => $albumArtist->items,
			'compalation_artist' => $compalation->items
		];

		echo json_encode($data);
		die;
	}


	public function get_my_top_artist_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$artist_data = $api->getArtist($_POST['id']);
		$top_track = $api->getArtistTopTracks($_POST['id'], [
			'country' => 'ID'
		]);
		$albumArtist = $api->getArtistAlbums($_POST['id'], [
			'include_groups' => 'album',
			'limit' => 50
		]);

		$compalation = $api->getArtistAlbums($_POST['id'], [
			'include_groups' => 'single,compilation,appears_on',
			'limit' => 50
		]);
		$data = [
			'artist_data' => $artist_data,
			'top_track' => $top_track->tracks,
			'album_artist' => $albumArtist->items,
			'compalation_artist' => $compalation->items
		];

		echo json_encode($data);
		die;
	}

	public function get_saved_track_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$track_data = $api->getTrack($_POST['id']);
		$artist_data = $api->getArtist($track_data->artists[0]->id);
		$top_track = $api->getArtistTopTracks($artist_data->id, [
			'country' => 'ID'
		]);
		$albumArtist = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'album',
			'limit' => 50
		]);

		$compalation = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'single,compilation,appears_on',
			'limit' => 50
		]);
		$data = [
			'track_data' => $track_data,
			'artist_data' => $artist_data,
			'top_track' => $top_track->tracks,
			'album_artist' => $albumArtist->items,
			'compalation_artist' => $compalation->items
		];

		echo json_encode($data);
		die;
	}

	public function get_saved_album_ajax()
	{

		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$album_data = $api->getAlbum($_POST['id']);

		$artist_data = $api->getArtist($album_data->artists[0]->id);

		$data = [
			'album_data' => $album_data,
			'artist_data' => $artist_data,
		];

		echo json_encode($data);
		die;
	}


	public function get_podcast_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$podcast_data = $api->getShow($_POST['id']);

		$data = [
			'podcast_data' => $podcast_data,
		];

		echo json_encode($data);
		die;
	}

	public function get_release_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$album_data = $api->getAlbum($_POST['id']);

		$artist_data = $api->getArtist($album_data->artists[0]->id);

		$data = [
			'album_data' => $album_data,
			'artist_data' => $artist_data,
		];

		echo json_encode($data);
		die;
	}


	/* ----------------------------- SEARCH PLAYLIST ---------------------------- */
	public function search_playlist_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$playlist_data = $api->getMyPlaylists();
		$keyword = $_POST['search_top'];
		$data = [
			'playlist' => $playlist_data->items,
			'search_top' => $keyword
		];

		echo json_encode($data);
		die;
	}

	public function get_playlist_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$playlist_data = $api->getPlaylist($_POST['id']);
		$data = [
			'playlist' => $playlist_data,
		];

		echo json_encode($data);
		die;
	}

	public function get_spotify_playlist_ajax()
	{
		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$get_playlist_items = $api->getPlaylistTracks($_POST['id'], [
			'fields' => 'items(track(name,album(name,images,id),artists(name,id),id,duration_ms,uri))',
			'limit' => 50
		]);
		$get_current_category = $api->getPlaylist($_POST['id']);
		$track_playlist = $get_playlist_items->items;

		$data = [
			'track_playlist' => $track_playlist,
			'current_category' => $get_current_category
		];

		echo json_encode($data);
		die;
	}

	public function get_playlist_detail_ajax()
	{

		$api = new SpotifyWebAPI\SpotifyWebAPI();
		$api->setAccessToken($this->accessToken);
		$me = $api->me();
		$track_data = $api->getTrack($_POST['id']);
		$artist_data = $api->getArtist($track_data->artists[0]->id);
		$top_track = $api->getArtistTopTracks($artist_data->id, [
			'country' => 'ID'
		]);
		$albumArtist = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'album',
			'limit' => 50
		]);

		$compalation = $api->getArtistAlbums($artist_data->id, [
			'include_groups' => 'single,compilation,appears_on',
			'limit' => 50
		]);
		$data = [
			'track_data' => $track_data,
			'artist_data' => $artist_data,
			'top_track' => $top_track->tracks,
			'album_artist' => $albumArtist->items,
			'compalation_artist' => $compalation->items
		];

		echo json_encode($data);
		die;
	}



	/* ---------------------------- END APPEND DETAIL --------------------------- */




	/**
	 * Generate Container
	 */
	public function get_container($container = '')
	{
		return $this->container;
	}
}
