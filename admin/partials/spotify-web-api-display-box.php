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
	$bg_box = get_option('_bg_box_ui') ? get_option('_bg_box_ui') : 'transparent';
	$text_color = get_option('_text_color_ui') ? get_option('_text_color_ui') : '#000';
	$width = get_option('_width_box') ? get_option('_width_box') : '100%';
	$height = get_option('_height_box') ? get_option('_height_box') : '100%';
	$radius_box = get_option('_radius_box') ? get_option('_radius_box') : '10px';
	/** Button API */
	$bg_button_ui = get_option('_bg_button_ui') ? get_option('_bg_button_ui') : '#f5f5f5';
	$text_button_color = get_option('_text_button_color') ? get_option('_text_button_color') : '#000';
	$radius_button = get_option('_radius_button') ? get_option('_radius_button') : '10px';
	$width_button = get_option('_width_button') ? get_option('_width_button') : '100%';
	$height_button = get_option('_height_button') ? get_option('_height_button') : '100%';
	?>

 <style>
 	.<?= $this->plugin_name ?>-view-box {
 		display: flex;
 		max-width: inherit;
 		flex-direction: row;
 		justify-content: center;
 		align-items: center;
 		background-color: <?= $bg_box ?>;
 		width: <?= $width ?>px;
 		height: <?= $height ?>px;
 		border-radius: <?= $radius_box ?>px;
 		background-repeat: no-repeat;
 	}

 	.<?= $this->plugin_name ?>-content-box form {
 		display: grid;
 		grid-template-columns: repeat(6, minmax(0, 1fr));
 		gap: 10px;
 		padding-top: 20px;
 		padding-bottom: 20px;
 		padding-left: 10px;
 		padding-right: 10px;
 		color: <?= $text_color ?>;
 		justify-content: center;
 		align-items: center;
 		background-repeat: no-repeat;
 	}

 	.<?= $this->plugin_name ?>-button-api {
 		display: flex;
 		direction: row;
 		justify-content: center;
 		align-items: center;
 		text-align: center;
 		border-radius: <?= $radius_button ?>px;
 		background-color: <?= $bg_button_ui ?> !important;
 		background: <?= $bg_button_ui ?> !important;
 		color: <?= $text_button_color ?> !important;
 		border: none;
 		padding-top: 5px;
 		padding-bottom: 5px;
 		padding-left: 80px;
 		padding-right: 80px;
 		width: <?= $width_button ?>px;
 		height: <?= $height_button ?>px;
 		background-repeat: no-repeat;
 	}

 	.<?= $this->plugin_name ?>-button-api:hover {
 		display: flex;
 		direction: row;
 		justify-content: center;
 		align-items: center;
 		text-align: center;
 		border-radius: <?= $radius_button ?>px;
 		background-color: <?= $bg_button_ui ?> !important;
 		filter: brightness(50%);
 		background: <?= $bg_button_ui ?> !important;
 		color: <?= $text_button_color ?> !important;
 		border: none;
 		padding-top: 5px;
 		padding-bottom: 5px;
 		padding-left: 80px;
 		padding-right: 80px;
 		width: <?= $width_button ?>px;
 		height: <?= $height_button ?>px;
 		background-repeat: no-repeat;
 		cursor: pointer;
 	}

 	.bg-playlist {
 		background-color: #000 !important;

 	}
 </style>
 <div class="<?= $this->plugin_name ?>-view-box">
 	<div class="<?= $this->plugin_name ?>-content-box">
 		<form action="#" id="" method="POST" data-url="<?= admin_url('admin-ajax.php'); ?>">
 			<input type="button" id="<?= $this->plugin_name . '-endpoint' ?>" class="<?= $this->plugin_name ?>-button-api" value="Your Playlist" data-name="your_playlist" />
 			<input type="hidden" id="action" name="action" class="<?= $this->plugin_name ?>-button-api" value="submit_find_data" />
 		</form>
 		<form action="#" id="" method="POST" data-url="<?= admin_url('admin-ajax.php'); ?>">
 			<input type="button" id="<?= $this->plugin_name . '-endpoint' ?>" class="<?= $this->plugin_name ?>-button-api" value="" data-name="" />
 			<input type="hidden" id="action" name="action" class="<?= $this->plugin_name ?>-button-api" value="submit_find_data" />
 		</form>
 	</div>
 </div>
 <div class="flex flex-1" style="display:none;">
 	<div class="flex flex-1 bg-gray-500 py-4 px-4" id="playlist">
 	</div>
 </div>
 <script>
 	jQuery(document).ready(function($) {
 		$('#<?= $this->plugin_name . '-endpoint' ?>').click(function(e) {
 			e.preventDefault();
 			let url = $(this).parent().data('url');
 			let action = $(this).parent().find('#action').val()
 			var data = {
 				action: action,
 				name: $(this).data('name')
 			};
 			$.ajax({
 				url: url,
 				method: 'post',
 				data: data,
 				success: (res) => {
 					if (action === 'submit_find_data') {

 						let playlist = JSON.parse(res);
 						console.log(playlist)
 						$('#playlist').parent().fadeIn('slow').css('max-width', 'inherit');

 						let html_playlist = playlist.items.map(data => {


 							return `
						 <div class="flex flex-col content-area bg-gray-600 shadow-md">
						 <div class="px-2 py-2 divide-y divide-y-reverse divide-zinc-300 my-3">
						 <div class="flex flex-col justify-between items-center text-center">
						 <img src="${data.images[0].url}" class="w-32 h-32 rounded-lg"/>
						 <div class="text-white">
						 <p>${data.name}</p>
						 <p class="w-48 text-sm">By ${data.owner.display_name}</p>
						 </div>
						 </div>
						 </div>
						 </div>
						 `
 						})
 						$('#playlist').html(html_playlist)
 					}
 				}
 			})
 		});
 	});
 </script>