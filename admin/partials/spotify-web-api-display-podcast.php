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
$search_podcast = $api->search('p', 'show');
$grid = get_option('_number_grid_podcast');
$background_color = get_option('_bg_box_podcast');
$offset = [
    '20' => 20,
    '40' => 40,
    '50' => 50,
];




?>
<style>
    .entry-header {
        display: none;
    }

    .page .entry-title {
        display: none;
    }

    .bg-box {
        background-color: <?= $background_color ? $background_color : '#fff' ?>;
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

    @media (min-width: 300px) and (max-width: 767.98px) {

        #frame {
            overflow-y: scroll !important;
            height: 30em !important;
        }

        .bg-box {
            background-color: <?= $background_color ? $background_color : '#fff' ?>;
        }

        #artist_track iframe {
            width: 15em !important;
            height: 4em;
        }

    }

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



<div class="flex flex-1 flex-col bg-box" style="max-width: inherit;">
    <div class="flex flex-col justify-center items-center my-5">
        <form action="#" id="" method="POST" data-url="<?= admin_url('admin-ajax.php'); ?>">
            <input type="text" class="form-search" name="search_podcast" id="search_podcast" placeholder="Search Podcast" />
            <select id="offset" name="offset">
                <option value="hide" selected="selected">-- Jumlah Item --</option>
                <?php foreach ($offset as $idx => $val) :  ?>
                    <option value="<?= $idx; ?>"><?= $val; ?></option>

                <?php endforeach;  ?>
            </select>
            <input type="hidden" name="action" id="search_podcast" value="search_podcast" />
            <input type="submit" name="submit_search" id="submit_search" value="Cari" />
        </form>
    </div>
    <!-- grid -->
    <div class="flex flex-1 flex-row">
        <div class="grid grid-cols-2 lg:grid-cols-4 mx-auto gap-4" id="frame">
            <?php foreach ($search_podcast->shows->items as $search_pod) : ?>
                <a href="#data_playlist" onclick="ShowPodCast(this)" data-name="<?= $search_pod->name; ?>" data-id="<?= $search_pod->id; ?>" class="flex flex-col hover:bg-transparent focus:bg-transparent">
                    <img src="<?= $search_pod->images[0]->url; ?>" class="lg:w-40 md:h-40 lg:w-40 md:h-40 sm:w-36 sm:h-36" />
                    <p class="text-center"><?= $search_pod->name; ?></p>
                </a>
            <?php endforeach; ?>
        </div>

    </div>

</div>

<div style="display: none;" id="detail_followed" class="lg:mt-12">

</div>

<div class="mt-10 lg:mt-12">
    <div style="display: none;" class="flex flex-1 flex-col gap-4" id="play_music">

    </div>
</div>


<!-- Detail Artist -->


<script>
    let $ = jQuery;

    function ShowPodCast(event) {
        $('#detail_followed').fadeOut().html('')
        $('#play_music').slideUp().html('')
        let data_id = event.getAttribute('data-id')
        let data_name = event.getAttribute('data-name')

        $.ajax({
            url: '<?= admin_url('admin-ajax.php'); ?>',
            method: 'post',
            dataType: 'json',
            data: {
                action: 'get_podcast',
                id: data_id,
                name: data_name
            },
            success: (res) => {
                let release = res.podcast_data.episodes.items.sort((a, b) => new Date(b.release_date).getFullYear() - new Date(a.release_date).getFullYear())
                console.log(release)
                console.log(res)
                let html = `
      <div class="flex flex-1 flex-col gap-5 p-4 shadow-md">
        <div class="flex flex-1 justify-center items-center gap-4">
            <img src="${res.podcast_data.images[0].url}" class="w-14 h-14 lg:w-36 lg:h-36" />
            <div class="flex flex-1 flex-col gap-2">
            <h1 class="text-md lg:text-xl font-bold">${res.podcast_data.name}</h1>
            <p>From  ${(new Date(release[0].release_date).getFullYear())}</p>
            <p>By ${res.podcast_data.publisher}</p>
            </div>
        </div>

              
        </div>
        `

                let html_player = `<div style="left: 0; width: 100%; height: 252px; position: relative;"><iframe src="https://open.spotify.com/embed/show/${res.podcast_data.id}?utm_source=oembed" style="top: 0; left: 0; width: 100%; height: 100%; position: absolute; border: 0;" allowfullscreen allow="encrypted-media;"></iframe></div>`;

                $('#detail_followed').fadeIn().html(html)
                $('#play_music').fadeIn().html(html_player);


                window.location.href = "#play_music"
            }
        })

    }



    jQuery(document).ready(function($) {






        $('#submit_search').click(function(event) {
            event.preventDefault();
            var search_podcast = $('#search_podcast').val();
            var offset = $('#offset').val();
            var frame_pod = [];
            $.ajax({
                url: '<?= admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'search_podcast',
                    offset: offset,
                    search_podcast: search_podcast
                },
                success: function(data) {
                    let search = JSON.parse(data);
                    console.log(search)
                    if (search !== '') {
                        $.each(search.podcast, (idx, val) => {
                            let podcastData = `
                            <a href="#" onclick="ShowEpisode(this)" class="flex flex-col items-center" data-id="${val.id}">
                            <img src="${val.images[0].url}" alt="#${val.name}" class="w-46 h-46 rounded-full" />
                            <h3 class="text-sm font-bold">${val.name}</h3>
                            </a>`
                            frame_pod.push(podcastData)
                        })
                        $('#frame').html('');
                        $('#frame').append(frame_pod);
                    }

                }
            });
        });
    });
</script>