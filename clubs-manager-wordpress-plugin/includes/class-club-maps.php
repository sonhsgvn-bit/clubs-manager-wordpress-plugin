<?php
/**
 * Club Maps Class
 * 
 * Handles Google Maps integration and functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Club_Maps {
    
    /**
     * Initialize maps functionality
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_maps_scripts' ) );
        add_action( 'wp_footer', array( __CLASS__, 'add_maps_init_script' ) );
    }
    
    /**
     * Enqueue maps scripts
     */
    public static function enqueue_maps_scripts() {
        if ( self::should_load_maps() ) {
            $options = get_option( 'clubs_manager_options' );
            $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
            
            if ( ! empty( $api_key ) ) {
                wp_enqueue_script( 
                    'google-maps', 
                    'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $api_key ) . '&libraries=places', 
                    array(), 
                    null, 
                    true 
                );
                
                wp_enqueue_script(
                    'clubs-maps',
                    CLUBS_MANAGER_URL . 'assets/js/maps.js',
                    array( 'jquery', 'google-maps' ),
                    CLUBS_MANAGER_VERSION,
                    true
                );
                
                wp_localize_script( 'clubs-maps', 'clubsMapsConfig', array(
                    'defaultZoom' => isset( $options['default_map_zoom'] ) ? $options['default_map_zoom'] : 15,
                    'markerIcon' => CLUBS_MANAGER_URL . 'assets/images/marker.png',
                    'infoWindowTemplate' => self::get_info_window_template(),
                ) );
            }
        }
    }
    
    /**
     * Check if maps should be loaded
     */
    private static function should_load_maps() {
        global $post;
        
        // Load on single club pages
        if ( is_singular( 'billiard-club' ) ) {
            return true;
        }
        
        // Load on archive pages
        if ( is_post_type_archive( 'billiard-club' ) || is_tax( 'club-area' ) ) {
            return true;
        }
        
        // Load if shortcode is present
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'clubs_map' ) ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Add maps initialization script to footer
     */
    public static function add_maps_init_script() {
        if ( ! self::should_load_maps() ) {
            return;
        }
        
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Initialize maps when Google Maps API is loaded
            function initializeMaps() {
                // Initialize single club map
                if ($('#club-map-container').length) {
                    initSingleClubMap();
                }
                
                // Initialize clubs listing map
                if ($('.clubs-map').length) {
                    $('.clubs-map').each(function() {
                        initClubsMap($(this));
                    });
                }
            }
            
            // Single club map initialization
            function initSingleClubMap() {
                var mapContainer = $('#club-map-container');
                if (!mapContainer.length) return;
                
                var address = mapContainer.data('address');
                var title = mapContainer.data('title');
                
                if (!address) return;
                
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': address}, function(results, status) {
                    if (status === 'OK') {
                        var map = new google.maps.Map(mapContainer[0], {
                            zoom: clubsMapsConfig.defaultZoom,
                            center: results[0].geometry.location,
                            styles: getMapStyles()
                        });
                        
                        var marker = new google.maps.Marker({
                            position: results[0].geometry.location,
                            map: map,
                            title: title,
                            icon: clubsMapsConfig.markerIcon
                        });
                        
                        var infoWindow = new google.maps.InfoWindow({
                            content: '<div class="map-info-window"><h4>' + title + '</h4><p>' + address + '</p></div>'
                        });
                        
                        marker.addListener('click', function() {
                            infoWindow.open(map, marker);
                        });
                    }
                });
            }
            
            // Multiple clubs map initialization
            function initClubsMap(mapElement) {
                var clubsData = mapElement.data('clubs');
                var zoom = parseInt(mapElement.data('zoom')) || clubsMapsConfig.defaultZoom;
                var center = mapElement.data('center');
                
                if (!clubsData || !clubsData.length) return;
                
                var mapOptions = {
                    zoom: zoom,
                    styles: getMapStyles()
                };
                
                // Set center if provided, otherwise use first club
                if (center) {
                    var centerParts = center.split(',');
                    mapOptions.center = {
                        lat: parseFloat(centerParts[0]),
                        lng: parseFloat(centerParts[1])
                    };
                } else {
                    // Will be set after geocoding first address
                }
                
                var map = new google.maps.Map(mapElement[0], mapOptions);
                var bounds = new google.maps.LatLngBounds();
                var geocoder = new google.maps.Geocoder();
                var markersProcessed = 0;
                
                clubsData.forEach(function(club, index) {
                    geocoder.geocode({'address': club.address}, function(results, status) {
                        if (status === 'OK') {
                            var position = results[0].geometry.location;
                            
                            var marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: club.title,
                                icon: clubsMapsConfig.markerIcon
                            });
                            
                            var infoContent = buildInfoWindowContent(club);
                            var infoWindow = new google.maps.InfoWindow({
                                content: infoContent
                            });
                            
                            marker.addListener('click', function() {
                                infoWindow.open(map, marker);
                            });
                            
                            bounds.extend(position);
                            
                            // Set center and fit bounds after processing all markers
                            markersProcessed++;
                            if (markersProcessed === clubsData.length) {
                                if (!center && clubsData.length > 1) {
                                    map.fitBounds(bounds);
                                } else if (!center) {
                                    map.setCenter(position);
                                }
                            }
                        }
                    });
                });
            }
            
            // Build info window content
            function buildInfoWindowContent(club) {
                var content = '<div class="map-info-window">';
                content += '<h4><a href="' + club.url + '">' + club.title + '</a></h4>';
                
                if (club.address) {
                    content += '<p class="club-address">📍 ' + club.address + '</p>';
                }
                
                if (club.price) {
                    content += '<p class="club-price">💰 ' + club.price.toLocaleString() + ' VNĐ/giờ</p>';
                }
                
                if (club.tables) {
                    content += '<p class="club-tables">🎱 ' + club.tables + ' bàn</p>';
                }
                
                if (club.excerpt) {
                    content += '<p class="club-excerpt">' + club.excerpt.substring(0, 100) + '...</p>';
                }
                
                content += '<p><a href="' + club.url + '" class="info-details-link">Xem chi tiết →</a></p>';
                content += '</div>';
                
                return content;
            }
            
            // Get custom map styles
            function getMapStyles() {
                return [
                    {
                        "featureType": "all",
                        "elementType": "geometry.fill",
                        "stylers": [{"weight": "2.00"}]
                    },
                    {
                        "featureType": "all",
                        "elementType": "geometry.stroke",
                        "stylers": [{"color": "#9c9c9c"}]
                    },
                    {
                        "featureType": "all",
                        "elementType": "labels.text",
                        "stylers": [{"visibility": "on"}]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "all",
                        "stylers": [{"color": "#f2f2f2"}]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry.fill",
                        "stylers": [{"color": "#ffffff"}]
                    },
                    {
                        "featureType": "landscape.man_made",
                        "elementType": "geometry.fill",
                        "stylers": [{"color": "#ffffff"}]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "all",
                        "stylers": [{"visibility": "off"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "all",
                        "stylers": [{"saturation": -100}, {"lightness": 45}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry.fill",
                        "stylers": [{"color": "#eeeeee"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#7b7b7b"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.stroke",
                        "stylers": [{"color": "#ffffff"}]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "all",
                        "stylers": [{"visibility": "simplified"}]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.icon",
                        "stylers": [{"visibility": "off"}]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "all",
                        "stylers": [{"visibility": "off"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "all",
                        "stylers": [{"color": "#46bcec"}, {"visibility": "on"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [{"color": "#c8d7d4"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#070707"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.stroke",
                        "stylers": [{"color": "#ffffff"}]
                    }
                ];
            }
            
            // Initialize when ready
            if (typeof google !== 'undefined' && google.maps) {
                initializeMaps();
            } else {
                // Wait for Google Maps to load
                window.clubsMapInit = function() {
                    initializeMaps();
                };
            }
        });
        </script>
        <?php
    }
    
    /**
     * Get info window template
     */
    private static function get_info_window_template() {
        return '
        <div class="map-info-window">
            <h4><a href="{url}">{title}</a></h4>
            {address && <p class="club-address">📍 {address}</p>}
            {price && <p class="club-price">💰 {price} VNĐ/giờ</p>}
            {tables && <p class="club-tables">🎱 {tables} bàn</p>}
            {excerpt && <p class="club-excerpt">{excerpt}</p>}
            <p><a href="{url}" class="info-details-link">Xem chi tiết →</a></p>
        </div>';
    }
    
    /**
     * Get club coordinates from address using Google Geocoding API
     */
    public static function get_club_coordinates( $address ) {
        if ( empty( $address ) ) {
            return false;
        }
        
        $options = get_option( 'clubs_manager_options' );
        $api_key = isset( $options['google_maps_api_key'] ) ? $options['google_maps_api_key'] : '';
        
        if ( empty( $api_key ) ) {
            return false;
        }
        
        // Check cache first
        $cache_key = 'club_coords_' . md5( $address );
        $coordinates = get_transient( $cache_key );
        
        if ( $coordinates !== false ) {
            return $coordinates;
        }
        
        // Geocode the address
        $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query( array(
            'address' => $address,
            'key' => $api_key,
        ) );
        
        $response = wp_remote_get( $geocode_url );
        
        if ( is_wp_error( $response ) ) {
            return false;
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( $data['status'] === 'OK' && ! empty( $data['results'] ) ) {
            $location = $data['results'][0]['geometry']['location'];
            $coordinates = array(
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            );
            
            // Cache for 1 week
            set_transient( $cache_key, $coordinates, WEEK_IN_SECONDS );
            
            return $coordinates;
        }
        
        return false;
    }
}