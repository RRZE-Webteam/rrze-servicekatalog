<?php

/**
 * The template for displaying a single post.
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
 */

use RRZE\Servicekatalog\Util;

wp_enqueue_style('rrze-servicekatalog');
wp_enqueue_style( 'dashicons' );

get_header();

while (have_posts()) : the_post();

    $id = get_the_ID();
    $meta = get_post_meta($id);
    $description = Util::getMeta($meta, 'description');
    $links['portal']['label'] = __('URL Portal', 'rrze-servicekatalog');
    $links['portal']['url'] = Util::getMeta($meta, 'url-portal');
    $links['portal']['icon'] = 'dashicons-admin-home';
    $links['description']['label'] = __('URL Service Description', 'rrze-servicekatalog');
    $links['description']['url'] = Util::getMeta($meta, 'url-description');
    $links['description']['icon'] = 'dashicons-info';
    $links['tutorial']['label'] = __('URL Tutorial', 'rrze-servicekatalog');
    $links['tutorial']['url'] = Util::getMeta($meta, 'url-tutorial');
    $links['tutorial']['icon'] = 'dashicons-book';
    $links['video']['label'] = __('URL Video Tutorial', 'rrze-servicekatalog');
    $links['video']['url'] = Util::getMeta($meta, 'url-video');
    $links['video']['icon'] = 'dashicons-video-alt2';
    $commitmentTerms = get_the_terms( $id, 'rrze-service-commitment');
    if ($commitmentTerms) {
        $commitmentName = $commitmentTerms[0]->name;
        $commitmentSlug = $commitmentTerms[0]->slug;
        $commitmentURL = get_term_link($commitmentSlug, 'rrze-service-commitment');
        $commitmentBgColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', true);
        $commitmentTextColor = Util::calculateContrastColor($commitmentBgColor);
    }
    $groupTerms = get_the_terms( $id, 'rrze-service-target-group');
    if ($groupTerms) {
        $groupName = $groupTerms[0]->name;
        $groupSlug = $groupTerms[0]->slug;
        $groupURL = get_term_link($groupSlug, 'rrze-service-commitment');
    }
    ?>

    <div id="content">
        <div class="content-container">
            <div class="content-row">
                <main>
                    <article class="rrze-service">

                        <header class="entry-header">
                            <h1 id="maintop"  class="mobiletitle"><?php the_title(); ?></h1>
                            </header><!-- .entry-header -->

                        <div class="rrze-service-meta">
                            <?php if ($commitmentTerms) { ?>
                                <a class="service-commitment" href="<?php echo esc_attr($commitmentURL); ?>" style="background-color:<?php echo esc_attr($commitmentBgColor); ?>;color:<?php echo esc_attr($commitmentTextColor); ?>;"><?php echo esc_html($commitmentName); ?></a>
                            <?php }
                            if ($groupTerms) {
                                echo '<div class="service-groups"><span class="label">' . __('Target Groups', 'rrze-servicekatalog') . ': </span>';
                                foreach ($groupTerms as $groupTerm) {
                                    $groupName = $groupTerm->name;
                                    $groupSlug = $groupTerm->slug;
                                    $groupURL = get_term_link($groupSlug, 'rrze-service-target-group');
                                    $groupLinks[] = '<a class="service-group" href="' . esc_attr($groupURL) . '">' . esc_html($groupName) . '</a>';
                                }
                                echo implode(', ', $groupLinks);
                                echo '</div>';
                            } ?>
                        </div>

                        <div class="rrze-service-main">

                            <?php if (has_post_thumbnail() && !post_password_required()) { ?>
                                <figure class="post-thumbnail wp-caption">
                                    <?php the_post_thumbnail('medium'); ?>
                                    <figcaption class="wp-caption-text"><?php echo get_the_post_thumbnail_caption(); ?></figcaption>
                                </figure>
                            <?php } ?>

                            <div class="service-description">
                                <?php echo $description ;
                                //var_dump($commitmentURL);
                                ?>
                            </div>

                            <div class="service-urls">
                                <ul>
                                <?php foreach ($links as $link) {
                                    if ($link['url'] != '') {
                                        echo '<li><span class="dashicons ' . $link['icon'] . '"></span><a href="' . $link['url'] . '">' . $link['label'] . '</a></li>';
                                    }
                                } ?>
                                </ul>
                            </div>


                        </div>

                    </article>

                </main>
            </div>
        </div>
    </div>
<?php endwhile;

get_footer();
