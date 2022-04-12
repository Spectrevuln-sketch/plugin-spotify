<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://porto.storymadeid.my.id
 * @since      1.0.0
 *
 * @package    Spotify_Web_Api
 * @subpackage Spotify_Web_Api/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<form action='options.php' method='post'> 
    <?php
        settings_fields( 'spotify-web-api-settings-group' );
        do_settings_sections( 'spotify-web-api-settings-group' );
        submit_button(); 
        ?> 
    </form>
