<?php

/**
 * Menghapus header x-powered-by yang diatur oleh PHP
 */

header_remove('x-powered-by');

/**
 * Hapus X-Pingback header
 */

add_filter('pings_open', function() {
    return false;
});

/**
 * Nonaktifkan xmlrpc.php
 * Nonaktifkan hanya jika situs Anda tidak memerlukan penggunaan xmlrpc
 */

add_filter('xmlrpc_enabled', function() {
    return false;
});

/**
 *  Nonaktifkan REST API sepenuhnya untuk pengguna yang tidak masuk
 * Gunakan opsi ini hanya jika situs Anda tidak memerlukan penggunaan REST API
 */

// add_filter('rest_authentication_errors', function($result) {
// return (is_user_logged_in()) ? $result : new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
// });


/**
 * Menonaktifkan Wordpress default REST API endpoints.
 * Gunakan opsi ini jika plugin Anda memerlukan penggunaan REST API, tetapi masih ingin menonaktifkan core endpoints.
 */

add_filter('rest_endpoints', function($endpoints) {
    // Jika pengguna masuk, izinkan semua endpoints
    if(is_user_logged_in()) {
        return $endpoints;
    }
    foreach($endpoints as $route => $endpoint) {
        if(stripos($route, '/wp/') === 0) {
            unset($endpoints[ $route ]);
        }
    }
    return $endpoints;
});

/**
 * Nonaktifkan pemberitahuan update otomatis plugin via email
 */

add_filter( 'auto_plugin_update_send_email', function() {
    return false;
});

/**
 * Nonaktifkan pemberitahuan update otomatis tema via emal
 */

add_filter( 'auto_theme_update_send_email', function() {
    return false;
});

/**
 * Menghapus informasi yang tidak perlu dari tag <head>
 */

add_action('init', function() {
    // Hapus tautan feed artikel dan komentar
    remove_action( 'wp_head', 'feed_links', 2 );

    // Hapus tautan kategori artikel
	remove_action('wp_head', 'feed_links_extra', 3);

    // Hapus tautan ke Really Simple Discovery service endpoint
	remove_action('wp_head', 'rsd_link');

    // Hapus tautan ke file manifes WLW 
	remove_action('wp_head', 'wlwmanifest_link');

    // Hapus generator XHTML yang dibuat di wp_head hook, versi WP
	remove_action('wp_head', 'wp_generator');

    // Hapus tautan awal [start]
	remove_action('wp_head', 'start_post_rel_link');

    // Hapus tautan index
	remove_action('wp_head', 'index_rel_link');

    // Hapus tautan sebelumnya [previous[
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);

    // Hapus tautan relasional untuk postingan yang berdekatan dengan postingan saat ini
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

    // Hapus tautan relasional untuk postingan yang berdekatan dengan postingan saat ini
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Hapus tautan REST API
    remove_action('wp_head', 'rest_output_link_wp_head');

    // Hapus tautan untuk header REST API
    remove_action('template_redirect', 'rest_output_link_header', 11, 0 );

    // Hapus tautan untuk header shortlink
    remove_action('template_redirect', 'wp_shortlink_header', 11, 0 );

});

/**
 * Daftar feeds yang dinonaktifkan
*/

$feeds = [
    'do_feed',
    'do_feed_rdf',
    'do_feed_rss',
    'do_feed_rss2',
    'do_feed_atom',
    'do_feed_rss2_comments',
    'do_feed_atom_comments',
];

foreach($feeds as $feed) {
    add_action($feed, function() {
        wp_die('Feed has been disabled.');
    }, 1);
}

/**
 * Hapus file wp-embed.js dari pemuatan [loading]
 */

add_action( 'wp_footer', function() {
    wp_deregister_script('wp-embed');
});

/**
 * Mengaktifkan unggahan [upload] file gambar format WebP
 */

add_filter('mime_types', function($existing_mimes) {
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
});

/**
 * Tampilkan thumbnail WebP
 */

add_filter('file_is_displayable_image', function($result, $path) {
    return ($result) ? $result : (empty(@getimagesize($path)) || !in_array(@getimagesize($path)[2], [IMAGETYPE_WEBP]));
}, 10, 2);

?>
