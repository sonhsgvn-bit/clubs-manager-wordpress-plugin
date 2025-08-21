<?php
/**
 * Club Card Template Part
 *
 * @package ClubsManager
 */

$club_address = get_post_meta(get_the_ID(), 'club_address', true);
$club_price = get_post_meta(get_the_ID(), 'club_price', true);
$club_tables = get_post_meta(get_the_ID(), 'club_tables', true);
$club_parking = get_post_meta(get_the_ID(), 'club_parking', true);
$club_phone = get_post_meta(get_the_ID(), 'club_phone', true);

$terms = get_the_terms(get_the_ID(), 'club_area');
?>

<article class="club-card">
    <div class="club-card-image">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
        <?php else : ?>
            <a href="<?php the_permalink(); ?>">
                <div class="placeholder-image">
                    <span><?php _e('Chưa có ảnh', 'clubs-manager'); ?></span>
                </div>
            </a>
        <?php endif; ?>
    </div>

    <div class="club-card-content">
        <header class="club-card-header">
            <h3 class="club-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            
            <?php if ($terms && !is_wp_error($terms)) : ?>
                <div class="club-card-area">
                    <?php
                    $term_names = array();
                    foreach ($terms as $term) {
                        $term_names[] = esc_html($term->name);
                    }
                    echo implode(', ', $term_names);
                    ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="club-card-details">
            <?php if ($club_address) : ?>
                <div class="detail-item">
                    <span class="detail-icon">📍</span>
                    <span class="detail-text"><?php echo esc_html($club_address); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($club_price) : ?>
                <div class="detail-item">
                    <span class="detail-icon">💰</span>
                    <span class="detail-text"><?php echo number_format($club_price); ?> VNĐ/giờ</span>
                </div>
            <?php endif; ?>

            <?php if ($club_tables) : ?>
                <div class="detail-item">
                    <span class="detail-icon">🎱</span>
                    <span class="detail-text"><?php echo esc_html($club_tables); ?> bàn bi-a</span>
                </div>
            <?php endif; ?>

            <?php if ($club_parking) : ?>
                <div class="detail-item">
                    <span class="detail-icon">🚗</span>
                    <span class="detail-text">
                        <?php echo $club_parking === 'yes' ? __('Có chỗ đậu xe', 'clubs-manager') : __('Không có chỗ đậu xe', 'clubs-manager'); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($club_phone) : ?>
                <div class="detail-item">
                    <span class="detail-icon">📞</span>
                    <span class="detail-text">
                        <a href="tel:<?php echo esc_attr($club_phone); ?>"><?php echo esc_html($club_phone); ?></a>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (has_excerpt()) : ?>
            <div class="club-card-excerpt">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>

        <footer class="club-card-footer">
            <a href="<?php the_permalink(); ?>" class="club-card-button">
                <?php _e('Xem chi tiết', 'clubs-manager'); ?>
            </a>
        </footer>
    </div>
</article>