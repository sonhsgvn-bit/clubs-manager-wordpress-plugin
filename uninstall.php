<?php
/**
 * Uninstall script for Clubs Manager plugin
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all club posts
$clubs = get_posts(array(
    'post_type' => 'billiard-club',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($clubs as $club) {
    wp_delete_post($club->ID, true);
}

// Delete all club meta data
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_club_%'");

// Delete taxonomy terms
$terms = get_terms(array(
    'taxonomy' => 'club-area',
    'hide_empty' => false,
));

foreach ($terms as $term) {
    wp_delete_term($term->term_id, 'club-area');
}

// Delete plugin options
delete_option('clubs_manager_options');

// Remove custom capabilities if any were added
// Note: WordPress core handles post type capabilities automatically

// Clear any cached data
wp_cache_flush();

// Delete any transients
delete_transient('clubs_manager_version_check');

// Clean up any scheduled hooks
wp_clear_scheduled_hook('clubs_manager_daily_cleanup');

// Remove any custom database tables if created
// (Not needed for this plugin as we use WordPress core tables)

// Log uninstall for debugging (optional)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Clubs Manager plugin has been uninstalled and all data removed.');
}