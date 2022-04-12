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

	$api = new SpotifyWebAPI\SpotifyWebAPI();
	try {

		$accessTokenCookie = $_COOKIE['spotify-web-api_access_token'];
		$api->setAccessToken($accessTokenCookie);
		$searchArtist = $api->search($tag, 'artist');
		$options = [
			'limit' => 50,
			'offset' => 0,
			'album_type' => 'album,single'
		];
		$artist = $api->getArtistAlbums($searchArtist->artists->items[0]->id, $options);
	} catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
		if ($e) {

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
				$accessToken = $session->getAccessToken();
				$api->setAccessToken($accessToken);
				setcookie('spotify-web-api_access_token', $accessToken, time() + (86400 * 7), "/");
			}
		}
	}

	?>

 <div class="flex flex-1 flex-col">

 	<div class="flex flex-col text-2xl font-bold" style="display: inline;">
 		<div class="text-center">
 			<?php if (!empty($searchArtist)) : ?>
 				<img class="w-60 mx-auto" src="<?= $searchArtist->artists->items[0]->images[0]->url ?>" />
 				<h2><?= $searchArtist->artists->items[0]->name ?></h2>
 		</div>
 		<div class="flex flex-row">
 			<span class="grid grid-cols-4 gap-2 text-white mx-auto text-center mt-4 mb-12">
 				<?php foreach ($searchArtist->artists->items[0]->genres as $genres) : ?>
 					<p class="bg-gray-400 px-4 py-2 text-sm uppercase font-mono"><?= $genres ?></p>
 				<?php endforeach; ?>
 			</span>
 		</div>
 	</div>
 	<div class="flex flex-row justify-center items-center">
 		<div class="grid grid-cols-6 gap-4">
 			<?php foreach ($artist->items as $item) : ?>
 				<form id="album-<?= $item->id ?>" action="#" method="post" class="text-center" data-url="<?php echo admin_url('admin-ajax.php'); ?>" style="cursor:pointer;">
 					<input name="album_id" type="hidden" value="<?= $item->id ?>">
 					<img src="<?php echo $item->images[0]->url; ?>" alt="">
 					<p class="text-sm"><?= $item->name; ?></p>
 					<input type="hidden" name="action" value="go_album">
 				</form>
 				<script>
 					(function($) {
 						$(() => {

 							/** trigger on click in */
 							$('#album-<?= $item->id ?>').click((e) => {
 								e.preventDefault();
 								let id = $('#album-<?= $item->id ?>').find('input[name="album_id"]').val();
 								let url = $('#album-<?= $item->id ?>').attr('data-url');
 								let action = $('[name="action"]').val();

 								$.ajax({
 									url: `${url}`,
 									method: 'POST',
 									dataType: 'json',
 									data: {
 										action,
 										id
 									},
 									success: (res) => {
 										console.log('Done');
 										getPlayNow(res)
 									}
 								})
 							})

 						})

 					})(jQuery);
 				</script>
 			<?php endforeach; ?>
 		</div>
 	</div>
 </div>
 <?php endif; ?>

 <style>
 	@media only screen and (max-width: 768px) {
 		.modal {
 			/* Hidden by default */
 			position: fixed;
 			/* Stay in place */
 			z-index: 1;

 			/* Sit on top */
 			padding-top: 50px;
 			/* Location of the box */
 			left: 0;
 			top: 0;
 			right: 0;
 			bottom: 0;
 			width: 100% !important;
 			/* Full width */
 			height: 100%;
 			/* Full height */
 			overflow: auto;
 			/* Enable scroll if needed */
 			/* background-color: rgb(0, 0, 0); */
 			/* Fallback color */
 			/* background-color: rgba(0, 0, 0, 0.4); */
 			/* Black w/ opacity */
 		}

 	}


 	.modal {
 		/* Hidden by default */
 		position: fixed;
 		/* Stay in place */
 		z-index: 1;

 		/* Sit on top */
 		padding-top: 50px;
 		/* Location of the box */
 		left: 0;
 		top: 0;
 		right: 0;
 		bottom: 0;
 		width: 100% !important;
 		/* Full width */
 		height: 100%;
 		/* Full height */
 		overflow: auto;
 		/* Enable scroll if needed */
 		/* background-color: rgb(0, 0, 0); */
 		/* Fallback color */
 		/* background-color: rgba(0, 0, 0, 0.4); */
 		/* Black w/ opacity */
 	}

 	.modal-content {
 		background-color: #fefefe;
 		margin: auto;
 		padding: 20px;
 		/* border: 1px solid #888; */
 		width: 80%;
 	}

 	/* The Close Button */
 	.close {
 		color: #aaaaaa;
 		float: right;
 		font-size: 28px;
 		font-weight: bold;
 	}

 	.close:hover,
 	.close:focus {
 		color: #000;
 		text-decoration: none;
 		cursor: pointer;
 	}

 	#select_track::-webkit-scrollbar {
 		display: none;
 	}

 	#select_track {
 		-ms-overflow-style: none;
 		scrollbar-width: none;
 	}
 </style>
 <div id="myModal" class="modal" style="display: none;">

 	<!-- Modal content -->
 	<div class="modal-content bg-gray-500 text-white rounded-lg">
 		<div class="flex flex-1 flex-col">
 			<div class="flex flex-1 justify-between items-center">
 				<h4>Player</h4>
 				<span class="close">&times;</span>
 			</div>
 			<div class="flex flex-row justify-center items-center">
 				<h4>Select Track</h4>
 			</div>
 			<div class="h-80 overflow-y-scroll bg-indigo-400 px-5 py-8 rounded-lg mt-5 bg-opacity-50" id="select_track">

 			</div>
 		</div>
 	</div>


 </div>
 <!-- partial -->
 <script src='https://unpkg.com/@reactivex/rxjs@5.0.0-beta.1/dist/global/Rx.umd.js'></script>



 <script>
 	var span = document.getElementsByClassName("close")[0];
 	let modal = document.getElementById('myModal');
 	span.onclick = function() {
 		modal.style.display = "none";
 	}

 	window.onclick = function(event) {
 		if (event.target == modal) {
 			modal.style.display = "none";
 		}
 	}





 	function getPlayNow(data) {
 		console.log('from get playnow:', data)
 		if (data) {

 			console.log(data)
 			let track = document.getElementById('select_track')
 			let html_data = data.data.items.map(track => {
 				let duration = '0' + new Date(track.duration_ms).getMinutes() + ': ' + new Date(track.duration_ms).getSeconds();
 				return `
				 <div class="flex justify-between items-center shadow-lg p-5 hover:bg-indigo-500">
 					<div class="flex flex-col gap-2">
 						<h4 id="track_name">${track.name}</h4>
 						<p id="durasi">${duration}</p>
 					</div>
 					<div>
 						<form action="#" id="play_button_${data.my_devices.devices[0].id}" method="post" class="bg-transparent hover:bg-indigo-500" data-url="<?php echo admin_url('admin-ajax.php'); ?>" onclick="PlayCurrent(this)" data-track_uri="${track.uri}"  data-device="${data.my_devices.devices[0].id}">
						 	<input type="hidden" name="action" value="play_now">
 							<img class="w-26" src="https://img.icons8.com/external-others-amoghdesign/24/000000/external-play-multimedia-solid-24px-others-amoghdesign.png" />
 						</form>
 					</div>
 				</div>
				 `
 			})
 			track.innerHTML = html_data

 			modal.style.display = 'block';

 		} else {
 			modal.display = 'none';
 		}
 	}


 	function PlayCurrent(e) {
 		let device_id = e.getAttribute('data-device');
 		let url = e.getAttribute('data-url');
 		let idForm = e.getAttribute('id');
 		let playNowFormData = document.getElementById(`${idForm}`)
 		let action = playNowFormData.querySelector('[name="action"]').value;
 		let data = {
 			action: action,
 			device_id: device_id,
 			track_uri: e.getAttribute('data-track_uri')
 		}
 		console.log(data)
 		jQuery.ajax({
 			url: url,
 			method: 'POST',
 			data: data,
 			dataType: 'json',
 			success: (res) => {
 				console.log(res)
 			}
 		});
 	}
 </script>