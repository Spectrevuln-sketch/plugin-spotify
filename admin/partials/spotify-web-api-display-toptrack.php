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
$number_grid = get_option('_number_grid_toptrack');
$bg_box_template = get_option('_bg_box_track');
$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($this->accessToken);
$me = $api->me();
$mytop = $api->getMyTop('tracks');
$countPerpage = 20;
$total = $mytop->total;
$totalPage = ceil($total / $countPerpage);

$mytop = $api->getMyTop('tracks', [
    'limit' => $countPerpage,
    'offset' => $countPerpage
]);
?>

<style>
    .entry-header {
        display: none;
    }

    .page .entry-title {
        display: none;
    }

    .bg-box {
        background-color: <?= $bg_box_track ? $bg_box_track : '#fff'; ?> !important;
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
            background-color: <?= $bg_box_track ? $bg_box_track : '#fff'; ?> !important;
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
            <input type="text" class="form-search" name="search_top" id="search_top" placeholder="Search Top Chart" />
            <input type="hidden" name="action" id="search_chart" value="search_chart" />
        </form>
    </div>
    <div class="flex flex-1 flex-row">
        <div class="grid grid-cols-2 lg:grid-cols-<?= $number_grid ? $number_grid : '4'; ?> mx-auto gap-4" id="frame">
            <?php foreach ($mytop->items as $top_track) : ?>
                <a href="#data_playlist" onclick="ShowAnyTracks(this)" data-name="<?= $top_track->name; ?>" data-id="<?= $top_track->id; ?>" class="flex flex-col hover:bg-transparent focus:bg-transparent">
                    <img src="<?= $top_track->album->images[0]->url; ?>" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
                    <p class="text-center"><?= $top_track->name; ?></p>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
    <div class="flex flex-1 flex-row">
        <div class="flex flex-1 flex-col justify-center items-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination flex flex-1 flex-row justify-center gap-2">
                    <?php if ($mytop->previous != null) : ?>
                        <li class="page-item">
                            <button class="page-link" data-page="<?= $mytop->previous ?>">Previous</button>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPage; $i++) : ?>
                        <li class="page-item focus:bg-transparent hover:bg-transparent">
                            <button class="page-link <?= ($i == $aktifPage) ? 'text-red-500' : ''; ?>" data-page="<?= $i; ?>"><?= $i; ?></button>
                        </li>
                    <?php endfor; ?>
                    <?php if ($mytop->next != null) : ?>
                        <li class="page-item">
                            <button class="page-link" data-page="<?= $mytop->next ?>">Next</button>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div style="display: none;" id="detail_followed" class="lg:mt-12">

</div>

<div class="mt-10 lg:mt-12">
    <div style="display: none;" class="flex flex-1 flex-col gap-4" id="play_music">

    </div>
</div>


</div>
<!-- Detail Artist -->
<script>
    let $ = jQuery;


    function ShowAnyTracks(event) {
        $('#detail_followed').fadeOut().html('')
        $('#play_music').slideUp().html('')
        let data_id = event.getAttribute('data-id')
        let data_name = event.getAttribute('data-name')

        $.ajax({
            url: '<?= admin_url('admin-ajax.php'); ?>',
            method: 'post',
            dataType: 'json',
            data: {
                action: 'get_top_track_detail',
                id: data_id,
                name: data_name
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
                    <h1 class="font-bold capitalize">SINGLES & COMPILATIONS</h1>
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
                $('#detail_followed').fadeIn().html($html)
                window.location.href = "#detail_followed"
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


    jQuery(document).ready(function($) {

        $('#search_top').keyup(function(event) {
            event.preventDefault();
            var search_top = $('#search_top').val();
            var data_array = [];
            var result_search = [];
            var fileter_iframe = [];
            $.ajax({
                url: '<?= admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'search_top',
                    search_top: search_top
                },
                success: function(data) {
                    let search = JSON.parse(data);
                    console.log(search);
                    // get all string with regexp
                    if (search_top !== '') {

                        for (var i = 0; i < search.track.length; i++) {
                            let obj = search.track[i]
                            data_array.push(obj);
                        }
                        console.log(data_array);
                        // search
                        let obj_data = data_array.find(x => x.name.toLowerCase().includes(search_top.toLowerCase()));
                        result_search.push(obj_data);
                        $.each(result_search, function(index, val) {
                            if (val) {
                                let frame = `<a href="#data_playlist" onclick="ShowAnyTracks(this)" data-name="<?= $top_track->name; ?>" data-id="${val.id}" class="flex flex-col hover:bg-transparent focus:bg-transparent">
                    <img src="${val.album.images[0].url}" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
                    <p class="text-center">${val.name}</p>
                </a>`

                                fileter_iframe.push(frame);
                            } else {
                                $('#frame').html('');
                                $('#frame').append(`
                                <div class="flex flex-col justify-center items-center my-5">
                                    <h1 class="text-center text-2xl font-bold">No Result</h1>
                                </div>
                                `);
                            }
                        });
                        $('#frame').html('');
                        $('#frame').append(fileter_iframe);

                    } else {
                        $('#frame').html('');
                        for (var i = 0; i < search.track.length; i++) {
                            let obj = search.track[i]
                            let all_frame = `<button type="button" onclick="PlayTrack(this)" data-id="${obj.id}" class="flex flex-col hover:bg-transparent focus:bg-transparent text-center">
                                    <img src="${obj.album.images[0].url}" class="w-40 h-40" />
                                    <p>${obj.name}</p>
                                </button>`
                            $('#frame').append(all_frame);
                        }
                    }
                }
            });
        });
    });
</script>