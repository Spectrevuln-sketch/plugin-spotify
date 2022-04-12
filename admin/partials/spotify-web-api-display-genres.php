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

// pageination spotify

$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($this->accessToken);
$me = $api->me();
$search_genres = $api->getGenreSeeds();

$grid = get_option('_number_grid_genres');
$background_color = get_option('_bg_box_genres');
$offset = [
    '20' => 20,
    '40' => 40,
    '50' => 50,
]


?>

<style>
    .entry-header {
        display: none;
    }

    .page .entry-title {
        display: none;
    }

    .bg-box {
        background-color: #fff;
        padding: 20px 20px 20px 20px;
    }

    .form-search {
        border-radius: 20px !important;
        border: 1px solid #ccc;
        height: 40px;
        padding: 10px 0 10px 0;
    }

    #artist_track iframe {
        width: 21em !important;
        height: 100% !important;
    }

    .modal {
        transition: opacity 0.25s ease;
    }

    body.modal-active {
        overflow-x: hidden;
        overflow-y: visible !important;
    }

    .opacity-95 {
        opacity: .95;
    }

    @media (min-width: 300px) and (max-width: 767.98px) {

        #frame {
            overflow-y: scroll !important;
            height: 30em !important;
        }

        .bg-box {
            background-color: #fff;
        }

        #artist_track iframe {
            width: 15em !important;
            height: 4em;
        }

    }
</style>



<div class="flex flex-1 flex-col bg-box gap-2 sm:-mb-4" style="max-width: inherit;">
    <div class="flex flex-col justify-center items-center my-5">
        <form action="#" id="" method="POST" data-url="<?= admin_url('admin-ajax.php'); ?>">
            <input type="text" class="form-search" name="search_chart" id="search_chart" placeholder="Search Chart" />
            <input type="hidden" name="action" id="search_chart" value="search_chart" />
        </form>
    </div>
    <div class="flex flex-1 flex-row">
        <div class="gap-4 h-80 overflow-y-scroll" id="frame">
            <div class="my-3 gap-4">

                <div class="grid grid-cols-2 lg:grid-cols-4 mx-auto gap-4">

                    <?php foreach ($search_genres->genres as $data) : ?>
                        <a href="#data_playlist" onclick="ShowListGenre(this)" data-name="<?= $data; ?>" data-id="<?= $data; ?>" class="flex flex-col hover:bg-transparent focus:bg-transparent">
                            <p><?= $data; ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<div style="display: none;" id="detail_followed" class="lg:mt-12">

</div>

<div style="display: none;" id="detail_artist" class="lg:mt-12">

</div>


<div class="mt-10 lg:mt-12">
    <div style="display: none;" class="flex flex-1 flex-col gap-4" id="play_music">

    </div>
</div>


</div>
<!-- Detail Artist -->
<script>
    let $ = jQuery;




    function ShowListGenre(event) {
        let id = event.getAttribute('data-id')
        let url = event.getAttribute('data-url')
        console.log(id, url)
        $.ajax({
            url: `<?= admin_url('admin-ajax.php'); ?>`,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'search_genres',
                genre: id,
            },
            statusCode: {
                200: (res) => {
                    console.log(res)
                    $html = `
          <div class="flex flex-1 flex-col gap-5 p-4 shadow-md">
            <div class="flex flex-1 justify-center items-center gap-4">
                <img src="${res.tracks[0].album.images[0].url}" class="w-14 h-14 lg:w-36 lg:h-36" />
                <div class="flex flex-1 flex-col gap-2">
                <h1 class="text-md lg:text-xl font-bold">${res.tracks[0].name}</h1>
                    <span class="flex flex-row flex-wrap" style="font-size:8px;">
                    See Also ${res.genres.toString().replace(/'/g, '')}
                    </span>
                </div>
                
            </div>
            <div class="flex flex-1 flex-col gap-4">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-2">
                ${res.tracks.map(item => {
                return `
                <button type="button" onclick="ShowData(this);" class="hover:bg-transparent focus:bg-transparent hover:scale-y-125 flex flex-col gap-4 justify-center items-center lg:justify-center lg:items-center sm:mt-2" data-id="${item.id}" data-artist="${item.artists[0].id}" data-album="${item.album.id}">
                <img src="${item.album.images[0].url}" class="mx-auto w-14 h-14 lg:w-24 lg:h-24" />
                <p class="text-sm lg:text-center text-center">${item.name}</p>
                </button>
                `
                }).join('')}
                </div>
            </div>
            </div>
            `
                    $('#detail_followed').fadeIn().html($html)
                    window.location.href = "#detail_followed"

                },
                400: (res) => {
                    console.log(res)
                }
            }
        })
    }

    function ShowData(event) {
        let id_playlist = event.getAttribute('data-id')
        let id_artist = event.getAttribute('data-artist')
        let id_album = event.getAttribute('data-album')


        $.ajax({
            url: '<?= admin_url('admin-ajax.php'); ?>',
            method: 'post',
            dataType: 'json',
            data: {
                action: 'get_genre_data',
                id: id_playlist,
            },
            success: (res) => {

                console.log(res)
                $html = `
      <div class="flex flex-1 flex-col gap-5 p-4 shadow-md">
        <div class="flex flex-1 justify-center items-center gap-4">
            <img src="${res.track_data.album.images[0].url}" class="w-14 h-14 lg:w-36 lg:h-36" />
            <div class="flex flex-1 flex-col gap-2">
            <h1 class="text-md lg:text-xl font-bold">${res.track_data.name}</h1>
            <p>From the album ${res.track_data.album.name +' '+ (new Date(res.track_data.album.release_date).getFullYear())}</p>
            <p>By ${res.track_data.artists[0].name}</p>
            </div>
        </div>
        
                <div class="flex flex-1 justify-center items-center gap-4">
                    <img src="${res.track_data.album.images[0].url}" class="w-14 h-14 lg:w-36 lg:h-36" />
                    <div class="flex flex-1 flex-col">
                    <h1 class="text-md lg:text-xl font-bold">${res.track_data.name}</h1>
                    <p>${res.artist_data.followers.total} Followers</p>
                    </div>
                    <button>
                    <img src="${res.artist_data.images[0].url}" class="mr-5 w-14 h-14" />
                    </button>
                </div>
                <h1 class="font-bold">Top Tracks</h1>
        <div class="flex flex-1 flex-col gap-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-2">
            ${res.top_track.map(item => {
            return `
            <button type="button" onclick="PlayTrack(this);" class="hover:bg-transparent focus:bg-transparent hover:scale-y-125 flex flex-col gap-4 justify-center items-center lg:justify-center lg:items-center sm:mt-2" data-id="${item.id}">
            <img src="${item.album.images[0].url}" class="mx-auto w-14 h-14 lg:w-24 lg:h-24" />
            <p class="text-sm lg:text-center text-center">${item.name}</p>
            </button>
            `
            }).join('')}
            </div>
            <h1 class="font-bold">Albums</h1>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-2">
                    ${res.album_artist.map(item => {
                    return `
                    <button type="button" onclick="PlayAlbum(this);" class="hover:bg-transparent focus:bg-transparent hover:scale-y-125 flex flex-col gap-4 justify-center items-center lg:justify-center lg:items-center sm:mt-2" data-id="${item.id}">
                    <img src="${item.images[0].url}" class="mx-auto w-14 h-14 lg:w-24 lg:h-24" />
                    <p class="text-sm lg:text-center">${item.name}</p>
                    </button>
                    `
                    }).join('')}
                    </div>
                    <h1 class="font-bold capitalieze">SINGLES & COMPILATIONS</h1>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-2">
                    ${res.compalation_artist.map(item => {
                    return `
                    <button type="button" onclick="PlayAlbum(this);" class="hover:bg-transparent focus:bg-transparent hover:scale-y-125 flex flex-col gap-4 justify-center items-center lg:justify-center lg:items-center sm:mt-2" data-id="${item.id}">
                    <img src="${item.images[0].url}" class="mx-auto w-14 h-14 lg:w-24 lg:h-24" />
                    <p class="text-sm lg:text-center">${item.name}</p>
                    </button>
                    `
                    }).join('')}
                    </div>
                    <h1 class="font-bold">Related Artist</h1>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-2">
                    ${res.compalation_artist.map(item => {
                    return `
                    <button type="button" onclick="PlayAlbum(this);" class="hover:bg-transparent focus:bg-transparent hover:scale-y-125 flex flex-col gap-4 justify-center items-center lg:justify-center lg:items-center sm:mt-2" data-id="${item.id}">
                    <img src="${item.images[0].url}" class="mx-auto w-14 h-14 lg:w-24 lg:h-24" />
                    <p class="text-sm lg:text-center">${item.name}</p>
                    </button>
                    `
                    }).join('')}
                    </div>
        </div>
        </div>
        `
                $('#detail_artist').fadeIn().html($html)
                window.location.href = "#detail_artist"
            }
        })

    }

    function PlayTrack(event) {
        $('#play_music').slideUp().html('')
        let data_id = event.getAttribute('data-id')
        let frame = `<iframe src="https://open.spotify.com/embed/track/${data_id}?utm_source=oembed" allowfullscreen allow="encrypted-media;"></iframe>`
        $('#play_music').html(frame).slideDown()
        window.location.href = "#play_music"
    }

    function PlayAlbum(event) {
        $('#play_music').slideUp().html('')
        let data_id = event.getAttribute('data-id')
        let frame = `<iframe src="https://open.spotify.com/embed/album/${data_id}?utm_source=oembed" allowfullscreen allow="encrypted-media;" class="h-80"></iframe>`
        $('#play_music').html(frame).slideDown()
        window.location.href = "#play_music"
    }
</script>