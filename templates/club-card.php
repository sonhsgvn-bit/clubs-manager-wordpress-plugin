<?php
/**
 * Template for displaying individual club cards
 * 
 * Variables available:
 * $club_id - The club post ID
 * $club - The club post object
 * $price, $tables, $parking, $address, $phone - Club meta data
 * $areas - Club area terms
 */

if (!isset($club_id)) {
    $club_id = get_the_ID();
}

if (!isset($club)) {
    $club = get_post($club_id);
}

if (!isset($price)) {
    $price = get_post_meta($club_id, '_club_price', true);
}

if (!isset($tables)) {
    $tables = get_post_meta($club_id, '_club_tables', true);
}

if (!isset($parking)) {
    $parking = get_post_meta($club_id, '_club_parking', true);
}

if (!isset($address)) {
    $address = get_post_meta($club_id, '_club_address', true);
}

if (!isset($phone)) {
    $phone = get_post_meta($club_id, '_club_phone', true);
}

if (!isset($areas)) {
    $areas = get_the_terms($club_id, 'club-area');
}
?>

<div class="club-card" data-club-id="<?php echo esc_attr($club_id); ?>">
    
    <div class="club-card-image">
        <?php if (has_post_thumbnail($club_id)) : ?>
            <a href="<?php echo get_permalink($club_id); ?>">
                <?php echo get_the_post_thumbnail($club_id, 'medium', array('class' => 'club-thumbnail')); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo get_permalink($club_id); ?>" class="no-image">
                <div class="placeholder-image">
                    <span class="dashicons dashicons-games"></span>
                </div>
            </a>
        <?php endif; ?>
        
        <?php if ($price) : ?>
            <div class="price-badge">
                <?php echo number_format($price, 0, ',', '.'); ?> VNĐ/giờ
            </div>
        <?php endif; ?>
    </div>
    
    <div class="club-card-content">
        
        <header class="club-card-header">
            <h3 class="club-title">
                <a href="<?php echo get_permalink($club_id); ?>"><?php echo get_the_title($club_id); ?></a>
            </h3>
            
            <?php if ($areas && !is_wp_error($areas)) : ?>
                <div class="club-areas">
                    <?php foreach ($areas as $area) : ?>
                        <span class="area-tag"><?php echo esc_html($area->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </header>
        
        <div class="club-card-info">
            
            <?php if ($address) : ?>
                <div class="info-item address">
                    <span class="icon">📍</span>
                    <span class="text"><?php echo esc_html(wp_trim_words($address, 8, '...')); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($tables) : ?>
                <div class="info-item tables">
                    <span class="icon">🎱</span>
                    <span class="text"><?php echo esc_html($tables); ?> bàn</span>
                </div>
            <?php endif; ?>
            
            <div class="info-item parking">
                <span class="icon"><?php echo $parking === 'yes' ? '🅿️' : '🚫'; ?></span>
                <span class="text">
                    <?php echo $parking === 'yes' ? __('Có chỗ đậu xe', 'clubs-manager') : __('Không chỗ đậu xe', 'clubs-manager'); ?>
                </span>
            </div>
            
            <?php if ($phone) : ?>
                <div class="info-item phone">
                    <span class="icon">📞</span>
                    <a href="tel:<?php echo esc_attr($phone); ?>" class="text phone-link"><?php echo esc_html($phone); ?></a>
                </div>
            <?php endif; ?>
            
        </div>
        
        <div class="club-card-excerpt">
            <?php
            $excerpt = get_the_excerpt($club_id);
            if ($excerpt) {
                echo '<p>' . esc_html(wp_trim_words($excerpt, 15)) . '</p>';
            }
            ?>
        </div>
        
        <div class="club-card-actions">
            <a href="<?php echo get_permalink($club_id); ?>" class="button primary view-details">
                <?php _e('Xem chi tiết', 'clubs-manager'); ?>
            </a>
            
            <?php if ($phone) : ?>
                <a href="tel:<?php echo esc_attr($phone); ?>" class="button secondary call-now">
                    <?php _e('Gọi ngay', 'clubs-manager'); ?>
                </a>
            <?php endif; ?>
        </div>
        
    </div>
    
</div>