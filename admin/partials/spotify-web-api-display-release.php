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
$newRelease = $api->getNewReleases([
    'limit' => 50,
]);
$bg_box_release = get_option('_bg_box_release');
$number_grid_release = get_option('_number_grid_release');

?>

<style>
    .entry-header {
        display: none;
    }

    .page .entry-title {
        display: none;
    }

    .bg-box {
        background-color: <?= $bg_box_release ? $bg_box_release : '#fff'; ?>;
    }

    .form-search {
        border-radius: 20px !important;
        border: 1px solid #ccc;
        height: 40px;
        padding: 10px 0 10px 0;
    }

    iframe.saved-track {
        width: 18em !important;
        height: 4em;
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

    @media (min-width: 200px) and (max-width: 300.98px) {

        #frame {
            overflow-y: scroll !important;
            height: 30em !important;
        }

        .bg-box {
            background-color: <?= $bg_box_release ? $bg_box_release : '#fff'; ?>;
        }

        iframe.saved-track {
            width: 18em !important;
            height: 4em;
        }

    }
</style>



<style>
    .entry-header {
        display: none;
    }

    .page .entry-title {
        display: none;
    }

    .bg-box {
        background-color: #fff;
    }

    .form-search {
        border-radius: 20px !important;
        border: 1px solid #ccc;
        height: 40px;
        padding: 10px 0 10px 0;
    }

    iframe.saved-track {
        width: 18em !important;
        height: 4em;
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

    @media (min-width: 200px) and (max-width: 300.98px) {

        #frame {
            overflow-y: scroll !important;
            height: 30em !important;
        }

        .bg-box {
            background-color: <?= $bg_box_savetrack ? $bg_box_savetrack :  '#fff' ?>;
        }

        iframe.saved-track {
            width: 18em !important;
            height: 4em;
        }

    }
</style>



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
            <input type="text" class="form-search" name="search_new" id="search_new" placeholder="Search Track" />
            <input type="hidden" name="action" id="search_new" value="search_new" />
        </form>
    </div>
    <div class="flex flex-1 flex-row">
        <div class="grid grid-cols-2 lg:grid-cols-4 mx-auto gap-4" id="frame">
            <?php foreach ($newRelease->albums->items as $release) : ?>
                <a href="#data_playlist" onclick="ShowSavedAlbum(this)" data-name="<?= $release->name; ?>" data-id="<?= $release->id; ?>" class="flex flex-col hover:bg-transparent focus:bg-transparent">
                    <img src="<?= $release->images[0]->url; ?>" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
                    <p class="text-center"><?= $release->name; ?></p>
                </a>
            <?php endforeach; ?>
        </div>

    </div>

</div>

<div class="lg:mt-12">
    <div class="flex flex-1 flex-col gap-5 p-4 shadow-md" id="detail_followed" style="display: none;">

    </div>
</div>

<div class="mt-10 lg:mt-12">
    <div style="display: none;" class="flex flex-1 flex-col gap-4" id="play_music">

    </div>
</div>



<script>
    var $ = jQuery;

    function ShowSavedAlbum(event) {
        $('#detail_followed').fadeOut().html('')
        $('#play_music').slideUp().html('')
        let data_id = event.getAttribute('data-id')
        let data_name = event.getAttribute('data-name')

        $.ajax({
            url: '<?= admin_url('admin-ajax.php'); ?>',
            method: 'post',
            dataType: 'json',
            data: {
                action: 'get_release',
                id: data_id,
                name: data_name
            },
            success: (res) => {
                let Realese = new Date(res.album_data.release_date)
                const monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                console.log(res)
                $html =
                    `
                    <div class="flex flex-1 flex-row gap-4">
                    <div class="flex flex-col">
                    <img src="${res.album_data.images[0].url}" alt="#${res.album_data.type}" class="w-28 h-28 lg:w-44 lg:h-44"/>
                    </div>
                    <div class="flex flex-col">
                    <h1 class="text-md lg:text-xl font-bold">${res.album_data.name}</h1>
                    <p class="text-sm lg:text-md font-light">${monthNames[Realese.getMonth()] + ' ' + Realese.getDate() + ', ' + Realese.getFullYear()}</p>
                    <p class="text-sm lg:text-md font-light">Album By <strong>${res.album_data.artists[0].name}</stro></p>
                    <img alt="#none" src="${res.artist_data.images[0].url}" class="w-10 h-10 lg:w-14 lg:h-14 rounded-full"/>
                    </div>
                    </div>
                    <div class="flex flex-row h-80">
                    <iframe src="https://open.spotify.com/embed/album/${res.album_data.id}?utm_source=oembed" allowfullscreen allow="encrypted-media;"></iframe>
                    </div>
                </div>`
                $('#detail_followed').fadeIn().html($html)
                window.location.href = "#detail_followed"
            }
        })

    }


    jQuery(document).ready(function($) {
        $('#search_new').keyup(function(event) {
            event.preventDefault();
            var search_new = $('#search_new').val();
            var data_array = [];
            var result_search = []
            $.ajax({
                url: '<?= admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'search_new',
                    search_new: search_new
                },
                success: function(data) {
                    let search = JSON.parse(data);
                    // get all string with regexp
                    console.log(search)
                    if (search_new !== '') {

                        for (var i = 0; i < search.track.length; i++) {
                            let obj = search.track[i]
                            data_array.push(obj);
                        }
                        // console.log(data_array);
                        // search
                        let obj_data = data_array.find(x => x.name.toLowerCase().includes(search_new.toLowerCase()));
                        result_search.push(obj_data);
                        $.each(result_search, function(index, val) {
                            console.log(val)
                            if (val) {
                                $('#frame').html('');
                                $('#frame').append(`
                                <button type="button" onclick="PlayTrack(this)" data-id="${val.id}" class="flex flex-col hover:bg-transparent focus:bg-transparent text-center">
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
                            let obj = search.track[i].track
                            let all_frame = `<button type="button" onclick="PlayTrack(this)" data-id="${obj.id}" class="flex flex-col text-center hover:bg-transparent focus:bg-transparent">
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