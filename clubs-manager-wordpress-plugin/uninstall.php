<?php
/**
 * Clubs Manager Uninstall
 * 
 * Fired when the plugin is uninstalled
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
delete_option( 'clubs_manager_options' );
delete_option( 'clubs_manager_version' );

// Remove any transients
global $wpdb;

// Delete all transients with 'club_coords_' prefix
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_club_coords_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_club_coords_%'" );

// Delete all posts of type 'billiard-club'
$clubs = get_posts( array(
    'post_type' => 'billiard-club',
    'numberposts' => -1,
    'post_status' => 'any'
) );

foreach ( $clubs as $club ) {
    wp_delete_post( $club->ID, true );
}

// Delete all meta data for clubs
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_club_%'" );

// Delete taxonomy terms
$terms = get_terms( array(
    'taxonomy' => 'club-area',
    'hide_empty' => false,
) );

if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
    foreach ( $terms as $term ) {
        wp_delete_term( $term->term_id, 'club-area' );
    }
}

// Remove custom capabilities (if any were added)
$role = get_role( 'administrator' );
if ( $role ) {
    $role->remove_cap( 'manage_clubs' );
    $role->remove_cap( 'edit_clubs' );
    $role->remove_cap( 'delete_clubs' );
}

// Clear any cached data
wp_cache_flush();

// Remove rewrite rules
flush_rewrite_rules();