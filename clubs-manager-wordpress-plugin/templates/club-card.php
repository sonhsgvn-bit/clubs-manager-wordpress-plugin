<?php
/**
 * Club Card Template
 * 
 * Template part for displaying club cards in listings
 */

$club_id = get_the_ID();
$address = get_post_meta( $club_id, '_club_address', true );
$price = get_post_meta( $club_id, '_club_price', true );
$tables = get_post_meta( $club_id, '_club_tables', true );
$phone = get_post_meta( $club_id, '_club_phone', true );

// Get club areas
$areas = get_the_terms( $club_id, 'club-area' );
$area_names = array();
if ( $areas && ! is_wp_error( $areas ) ) {
    $area_names = wp_list_pluck( $areas, 'name' );
}

// Get facilities
$facilities = array();
$facility_fields = array(
    '_club_parking' => __( 'Chỗ để xe', 'clubs-manager' ),
    '_club_wifi' => __( 'WiFi', 'clubs-manager' ),
    '_club_food_service' => __( 'Ăn uống', 'clubs-manager' ),
    '_club_air_conditioning' => __( 'Điều hòa', 'clubs-manager' ),
    '_club_cue_rental' => __( 'Cho thuê cơ', 'clubs-manager' ),
);

foreach ( $facility_fields as $meta_key => $label ) {
    if ( get_post_meta( $club_id, $meta_key, true ) === '1' ) {
        $facilities[] = $label;
    }
}

// Check if club is open now
$is_open_now = false;
$current_day = strtolower( date( 'l' ) );
$current_time = date( 'H:i' );
$today_hours = get_post_meta( $club_id, '_club_hours_' . $current_day, true );
$is_closed_today = get_post_meta( $club_id, '_club_closed_' . $current_day, true );

if ( ! $is_closed_today && $today_hours ) {
    // Parse hours (e.g., "8:00 - 24:00")
    if ( preg_match( '/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $today_hours, $matches ) ) {
        $open_time = $matches[1];
        $close_time = $matches[2];
        $is_open_now = ( $current_time >= $open_time && $current_time <= $close_time );
    }
}
?>

<article class="club-card" data-club-id="<?php echo $club_id; ?>">
    
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="club-card-image">
            <a href="<?php the_permalink(); ?>" class="image-link">
                <?php the_post_thumbnail( 'medium', array( 
                    'loading' => 'lazy',
                    'alt' => get_the_title()
                ) ); ?>
                
                <?php if ( $is_open_now ) : ?>
                    <span class="status-badge status-open"><?php _e( 'Đang mở', 'clubs-manager' ); ?></span>
                <?php elseif ( $is_closed_today ) : ?>
                    <span class="status-badge status-closed"><?php _e( 'Đóng cửa', 'clubs-manager' ); ?></span>
                <?php endif; ?>
                
                <?php if ( get_post_meta( $club_id, '_club_featured', true ) === '1' ) : ?>
                    <span class="featured-badge"><?php _e( 'Nổi bật', 'clubs-manager' ); ?></span>
                <?php endif; ?>
            </a>
            
            <div class="club-card-actions">
                <button class="quick-view-btn" data-club-id="<?php echo $club_id; ?>" title="<?php _e( 'Xem nhanh', 'clubs-manager' ); ?>">
                    <span class="dashicons dashicons-visibility"></span>
                </button>
                
                <?php if ( $phone ) : ?>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="quick-call-btn" title="<?php _e( 'Gọi ngay', 'clubs-manager' ); ?>">
                        <span class="dashicons dashicons-phone"></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="club-card-image club-card-no-image">
            <a href="<?php the_permalink(); ?>" class="image-link">
                <div class="no-image-placeholder">
                    <span class="dashicons dashicons-format-image"></span>
                    <span class="no-image-text"><?php _e( 'Không có ảnh', 'clubs-manager' ); ?></span>
                </div>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="club-card-content">
        
        <header class="club-card-header">
            <h3 class="club-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            
            <?php if ( ! empty( $area_names ) ) : ?>
                <div class="club-areas">
                    <span class="area-icon">📍</span>
                    <span class="area-names"><?php echo implode( ', ', array_slice( $area_names, 0, 2 ) ); ?></span>
                    <?php if ( count( $area_names ) > 2 ) : ?>
                        <span class="more-areas">+<?php echo count( $area_names ) - 2; ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </header>
        
        <div class="club-card-body">
            <?php if ( $address ) : ?>
                <div class="club-address">
                    <span class="address-icon">🏠</span>
                    <span class="address-text"><?php echo esc_html( wp_trim_words( $address, 8 ) ); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="club-meta">
                <?php if ( $price ) : ?>
                    <div class="club-price">
                        <span class="price-icon">💰</span>
                        <span class="price-value"><?php echo number_format( $price, 0, ',', '.' ); ?> VNĐ/giờ</span>
                    </div>
                <?php endif; ?>
                
                <?php if ( $tables ) : ?>
                    <div class="club-tables">
                        <span class="tables-icon">🎱</span>
                        <span class="tables-count"><?php echo $tables; ?> bàn</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ( has_excerpt() ) : ?>
                <div class="club-excerpt">
                    <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $facilities ) ) : ?>
                <div class="club-facilities">
                    <div class="facilities-list">
                        <?php foreach ( array_slice( $facilities, 0, 3 ) as $facility ) : ?>
                            <span class="facility-item">✅ <?php echo esc_html( $facility ); ?></span>
                        <?php endforeach; ?>
                        
                        <?php if ( count( $facilities ) > 3 ) : ?>
                            <span class="more-facilities">+<?php echo count( $facilities ) - 3; ?> tiện ích</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $today_hours && ! $is_closed_today ) : ?>
                <div class="club-hours-today">
                    <span class="hours-icon">🕐</span>
                    <span class="hours-text">
                        <?php _e( 'Hôm nay:', 'clubs-manager' ); ?> <?php echo esc_html( $today_hours ); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        
        <footer class="club-card-footer">
            <div class="club-actions">
                <a href="<?php the_permalink(); ?>" class="club-details-btn primary-btn">
                    <?php _e( 'Xem chi tiết', 'clubs-manager' ); ?>
                </a>
                
                <?php if ( $phone ) : ?>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="club-call-btn secondary-btn">
                        <span class="call-icon">📞</span>
                        <?php _e( 'Gọi', 'clubs-manager' ); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="club-rating">
                <?php
                // Placeholder for rating system (can be extended with reviews plugin integration)
                $rating = get_post_meta( $club_id, '_club_rating', true );
                if ( $rating ) :
                ?>
                    <div class="rating-stars">
                        <?php
                        $full_stars = floor( $rating );
                        $half_star = ( $rating - $full_stars ) >= 0.5;
                        
                        for ( $i = 1; $i <= 5; $i++ ) {
                            if ( $i <= $full_stars ) {
                                echo '<span class="star filled">★</span>';
                            } elseif ( $i == $full_stars + 1 && $half_star ) {
                                echo '<span class="star half">☆</span>';
                            } else {
                                echo '<span class="star empty">☆</span>';
                            }
                        }
                        ?>
                        <span class="rating-value"><?php echo number_format( $rating, 1 ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </footer>
        
    </div>
    
</article>