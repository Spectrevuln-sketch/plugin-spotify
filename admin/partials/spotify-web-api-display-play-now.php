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
    $options = [
        'limit' => 50,
        'offset' => 0,
    ];
    $TracksAlbum = $api->getAlbumTracks($_GET['album_id'], $options);
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
    }
}

?>




<!-- partial:index.partial.html -->
<!-- design inspired by https://dribbble.com/shots/4290719-Player -->
<!-- smooth parallax https://css-tricks.com/animated-intro-rxjs/ -->
<div style="max-width:100%; height: 100%;">

    <div class="toggles">
        <button class="toggle--dark">
            Dark Theme
        </button>
        <button class="toggle-light">
            Light Theme
        </button>
    </div>

    <div class="album">
        <svg class="album__menu" height="12px" version="1.1" viewBox="0 0 18 12" width="18px">
            <g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1">
                <g fill="#000000" id="Core" transform="translate(-87.000000, -342.000000)">
                    <g id="menu" transform="translate(87.000000, 342.000000)">
                        <path d="M0,12 L18,12 L18,10 L0,10 L0,12 L0,12 Z M0,7 L18,7 L18,5 L0,5 L0,7 L0,7 Z M0,0 L0,2 L18,2 L18,0 L0,0 L0,0 Z" id="Shape" />
                    </g>
                </g>
            </g>
        </svg>
        <div class="album__reflection"></div>
        <div class="album__player">
            <div class="album__song">
                <h2 class="album__song__album">
                    <marquee scrollamount="3">
                        <!-- SOMEBODY STOP ME -->
                        Grouper - Grid of Points
                    </marquee>
                </h2>
                <h1 class="album__song__title">
                    The Races
                </h1>
                <h2 class="album__song__position">
                    track 01 of 07
                </h2>
            </div>

            <div class="album__controls">

                <svg class="album__prev" height="12px" viewBox="0 0 18 12" width="18px">
                    <g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1">
                        <g fill="#000000" id="Icons-AV" transform="translate(-43.000000, -5.000000)">
                            <g id="fast-forward" transform="translate(43.000000, 5.000000)">
                                <path d="M0,12 L8.5,6 L0,0 L0,12 L0,12 Z M9,0 L9,12 L17.5,6 L9,0 L9,0 Z" id="Shape" />
                            </g>
                        </g>
                    </g>
                </svg>

                <svg class="album__play" height="20" viewBox="0 0 48 48" width="20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-838-2232H562v3600H-838z" fill="none" />
                    <path d="M16 10v28l22-14z" />
                    <path d="M0 0h48v48H0z" fill="none" />
                </svg>

                <svg class="album__next" height="12px" viewBox="0 0 18 12" width="18px">
                    <g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1">
                        <g fill="#000000" id="Icons-AV" transform="translate(-43.000000, -5.000000)">
                            <g id="fast-forward" transform="translate(43.000000, 5.000000)">
                                <path d="M0,12 L8.5,6 L0,0 L0,12 L0,12 Z M9,0 L9,12 L17.5,6 L9,0 L9,0 Z" id="Shape" />
                            </g>
                        </g>
                    </g>
                </svg>

            </div>
            <div class="album__song__scrubber">
                <input type="range" min="0" max="100" class="album__song__scrubber">
                <span class="album__song__current-time">0:28</span>
                <span class="album__song__full-length">0:50</span>
            </div>

        </div>

        <!--   <img src="https://f4.bcbits.com/img/a3330461814_10.jpg" alt="" class="album__art"> -->
        <div class="album__art"></div>

        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/36124/profile/profile-80.jpg?1520103364" class="album__avatar">

        <div class="album__interactions">
            <svg enable-background="new 0 0 128 128" height="12px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="12px" class="album__like">
                <path d="M127,44.205c0-18.395-14.913-33.308-33.307-33.308c-12.979,0-24.199,7.441-29.692,18.276  c-5.497-10.835-16.714-18.274-29.694-18.274C15.912,10.898,1,25.81,1,44.205C1,79,56.879,117.104,64.001,117.104  C71.124,117.104,127,79.167,127,44.205z" fill="#232323" />
            </svg>

            <svg enable-background="new 0 0 24 24" height="12px" id="Layer_1" version="1.1" viewBox="0 0 24 24" width="12px" class="album__add">
                <path clip-rule="evenodd" d="M22.5,14H14v8.5c0,0.276-0.224,0.5-0.5,0.5h-4C9.224,23,9,22.776,9,22.5V14H0.5  C0.224,14,0,13.776,0,13.5v-4C0,9.224,0.224,9,0.5,9H9V0.5C9,0.224,9.224,0,9.5,0h4C13.776,0,14,0.224,14,0.5V9h8.5  C22.776,9,23,9.224,23,9.5v4C23,13.776,22.776,14,22.5,14z" fill-rule="evenodd" />
            </svg>
        </div>

        <svg class="album__volume" enable-background="new 0 0 512 512" id="Layer_1" version="1.1" viewBox="0 0 512 512" xml:space="preserve" width="20" height="20" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g>
                <path d="M114.8,368.1H32.1c-5.8,0-10.5-4.7-10.5-10.5V154.4c0-5.8,4.7-10.5,10.5-10.5h82.7   c5.8,0,10.5,4.7,10.5,10.5v203.2C125.4,363.4,120.7,368.1,114.8,368.1z M42.7,347.1h61.6V165H42.7V347.1z" fill="#6A6E7C" />
                <path d="M303.7,512c-2.3,0-4.5-0.7-6.4-2.2L108.4,366c-2.6-2-4.2-5.1-4.2-8.4V154.4c0-3.3,1.5-6.4,4.2-8.4   L297.3,2.2c3.2-2.4,7.5-2.8,11.1-1.1c3.6,1.8,5.9,5.4,5.9,9.5v490.9c0,4-2.3,7.7-5.9,9.5C306.8,511.6,305.2,512,303.7,512z    M125.4,352.4l167.7,127.8V31.8L125.4,159.6V352.4z" fill="#6A6E7C" />
                <path d="M393.6,334.9c-5.8,0-10.5-4.7-10.5-10.5V187.7c0-5.8,4.7-10.5,10.5-10.5c5.8,0,10.5,4.7,10.5,10.5v136.7   C404.1,330.2,399.4,334.9,393.6,334.9z" fill="#6A6E7C" />
                <path d="M479.9,392.4c-5.8,0-10.5-4.7-10.5-10.5V130.1c0-5.8,4.7-10.5,10.5-10.5c5.8,0,10.5,4.7,10.5,10.5v251.7   C490.4,387.7,485.7,392.4,479.9,392.4z" fill="#6A6E7C" />
            </g>
        </svg>

    </div>
</div>
<!-- partial -->
<script src='https://unpkg.com/@reactivex/rxjs@5.0.0-beta.1/dist/global/Rx.umd.js'></script>
<script src="./script.js"></script>
<script>
    (function($) {
        $(() => {
            $('.site-header').css('display', 'none');
            $('.site-footer').css('display', 'none');

        })

    })(jQuery);
</script>