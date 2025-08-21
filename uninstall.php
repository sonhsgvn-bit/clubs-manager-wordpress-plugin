<?php
/**
 * Uninstall script for Clubs Manager
 *
 * @package ClubsManager
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Delete all club posts and related data
 */
function clubs_manager_delete_all_data() {
    global $wpdb;
    
    // Delete all posts of type 'billiard_club'
    $posts = get_posts(array(
        'post_type' => 'billiard_club',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ));
    
    foreach ($posts as $post_id) {
        // Delete post meta
        $wpdb->delete($wpdb->postmeta, array('post_id' => $post_id));
        
        // Delete the post
        $wpdb->delete($wpdb->posts, array('ID' => $post_id));
    }
    
    // Delete taxonomy terms
    $terms = get_terms(array(
        'taxonomy' => 'club_area',
        'hide_empty' => false,
        'fields' => 'ids'
    ));
    
    foreach ($terms as $term_id) {
        wp_delete_term($term_id, 'club_area');
    }
    
    // Delete options
    delete_option('clubs_manager_options');
    
    // Delete any cached data
    wp_cache_flush();
}

// Only delete data if the user has confirmed
if (get_option('clubs_manager_delete_data_on_uninstall', false)) {
    clubs_manager_delete_all_data();
}