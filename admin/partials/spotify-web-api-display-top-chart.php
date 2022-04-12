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
$TopChart = $api->getCategoryPlaylists('toplists', [
    'limit' => 20,
    'offset' => 0,
]);
$bg_box_top_chart = get_option('_bg_box_top_chart');
$number_grid_top_chart = get_option('_number_grid_top_chart');
$countPerpage = 20;
$total = $TopChart->playlists->total;
$totalPage = ceil($total / $countPerpage);
$aktifPage = (empty($_GET['page'])) ? 1 : $_GET['page'];
$offset = ($aktifPage - 1) * $countPerpage;

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
        <div class="grid grid-cols-2 lg:grid-cols-4 mx-auto gap-4" id="frame">
            <?php foreach ($TopChart->playlists->items as $top_chart) : ?>
                <a href="#data_playlist" onclick="ShowAnyTracks(this)" data-name="<?= $top_chart->name; ?>" data-id="<?= $top_chart->id; ?>" class="flex flex-col hover:bg-transparent focus:bg-transparent">
                    <img src="<?= $top_chart->images[0]->url; ?>" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
                    <p class="text-center"><?= $top_chart->name; ?></p>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
    <div class="flex flex-1 flex-row">
        <div class="flex flex-1 flex-col justify-center items-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-center">
                    <?php for ($i = 1; $i <= $totalPage; $i++) : ?>
                        <li class="page-item focus:bg-transparent hover:bg-transparent <?= ($i == $aktifPage) ? 'text-red-500' : ''; ?>">
                            <button class="page-link" data-page="<?= $i; ?>"><?= $i; ?></button>
                        </li>
                    <?php endfor; ?>
                    <?php if ($TopChart->playlists->next != null) : ?>
                        <li class="page-item">
                            <button class="page-link" data-page="<?= $aktifPage + 1; ?>">Next</button>
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
                action: 'get_current_track',
                id: data_id,
                name: data_name
            },
            success: (res) => {

                console.log(res)
                $html = `
          <div class="flex flex-1 flex-col gap-5 p-4 shadow-md">
            <div class="flex flex-1 justify-center items-center gap-4">
                <img src="${res.current_category.images[0].url}" class="w-14 h-14 lg:w-36 lg:h-36" />
                <div class="flex flex-1 flex-col gap-2">
                <h1 class="text-md lg:text-xl font-bold">${res.current_category.name}</h1>
                <p>${res.current_category.followers.total} Followers</p>
                <p>${res.current_category.description}</p>
                </div>
                
            </div>
            <div class="flex flex-1 flex-col gap-4">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-2">
                ${res.track_playlist.map(item => {
                return `
                <button type="button" onclick="PlayTrack(this);" class="hover:bg-transparent focus:bg-transparent hover:scale-y-125 flex flex-col gap-4 justify-center items-center lg:justify-center lg:items-center sm:mt-2" data-id="${item.track.id}">
                <img src="${item.track.album.images[0].url}" class="mx-auto w-14 h-14 lg:w-24 lg:h-24" />
                <p class="text-sm lg:text-center text-center">${item.track.name}</p>
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
        $('#search_chart').keyup(function(event) {
            event.preventDefault();
            var search_chart = $('#search_chart').val();
            var data_array = [];
            var result_search = []
            $.ajax({
                url: '<?= admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'search_chart',
                    search_chart: search_chart
                },
                success: function(data) {
                    let search = JSON.parse(data);
                    // get all string with regexp
                    console.log(search)
                    if (search_chart !== '') {

                        for (var i = 0; i < search.track.length; i++) {
                            let obj = search.track[i]
                            data_array.push(obj);
                        }
                        // console.log(data_array);
                        // search
                        let obj_data = data_array.find(x => x.name.toLowerCase().includes(search_chart.toLowerCase()));
                        result_search.push(obj_data);
                        $.each(result_search, function(index, val) {
                            console.log(val)
                            if (val) {
                                $('#frame').html('');
                                $('#frame').append(`
                                <button type="button" onclick="ShowChart(this)" data-id="${val.id}" class="flex flex-col hover:bg-transparent focus:bg-transparent text-center">
                                    <img src="${val.images[0].url}" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
                                    <p>${val.name}</p>
                                </button>
                                `);
                            } else {
                                $('#frame').html('');
                                $('#frame').append(`
                                <div class="flex flex-col justify-center items-center my-5">
                                    <h1 class="text-center text-2xl font-bold">No Result</h1>
                                </div>
                                `);
                            }
                        });

                    } else {
                        $('#frame').html('');
                        for (var i = 0; i < search.track.length; i++) {
                            let obj = search.track[i]
                            let all_frame = `<button type="button" onclick="ShowChart(this)" data-id="${obj.id}" class="flex flex-col text-center hover:bg-transparent focus:bg-transparent">
                                    <img src="${obj.images[0].url}" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
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