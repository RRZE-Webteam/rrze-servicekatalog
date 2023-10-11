<?php

/**
 * The template for displaying a single post.
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
 */

use RRZE\Servicekatalog\Utils;

wp_enqueue_style('rrze-servicekatalog');
wp_enqueue_style( 'dashicons' );

get_header();

while (have_posts()) : the_post();

    $id = get_the_ID();
    $meta = get_post_meta($id);
    $description = Utils::getMeta($meta, 'description');
    $links['portal']['label'] = __('URL Portal', 'rrze-servicekatalog');
    $links['portal']['url'] = Utils::getMeta($meta, 'url-portal');
    $links['portal']['icon'] = 'dashicons-admin-home';
    $links['description']['label'] = __('URL Service Description', 'rrze-servicekatalog');
    $links['description']['url'] = Utils::getMeta($meta, 'url-description');
    $links['description']['icon'] = 'dashicons-info';
    $links['tutorial']['label'] = __('URL Tutorial', 'rrze-servicekatalog');
    $links['tutorial']['url'] = Utils::getMeta($meta, 'url-tutorial');
    $links['tutorial']['icon'] = 'dashicons-book';
    $links['video']['label'] = __('URL Video Tutorial', 'rrze-servicekatalog');
    $links['video']['url'] = Utils::getMeta($meta, 'url-video');
    $links['video']['icon'] = 'dashicons-video-alt2';
    $commitmentTerms = get_the_terms( $id, 'rrze-service-commitment');
    if ($commitmentTerms) {
        $commitmentName = $commitmentTerms[0]->name;
        $commitmentSlug = $commitmentTerms[0]->slug;
        $commitmentURL = get_term_link($commitmentSlug, 'rrze-service-commitment');
        $commitmentBgColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', true);
        $commitmentTextColor = Utils::calculateContrastColor($commitmentBgColor);
    }
    $groupTerms = get_the_terms( $id, 'rrze-service-target-group');
    $tags = get_the_terms( $id, 'rrze-service-tag');
    ?>

    <div id="content">
        <div class="content-container">
            <div class="content-row">
                <main>
                    <article class="rrze-service">

                        <header class="entry-header">
                            <h1 id="maintop"  class="mobiletitle"><?php the_title(); ?></h1>
                            </header><!-- .entry-header -->

                        <div class="rrze-service-main">

                            <?php if (has_post_thumbnail() && !post_password_required()) { ?>
                                <div class="post-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php } ?>

                            <div class="service-info">
                                <div class="rrze-service-meta">
                                    <?php if ($commitmentTerms) {
                                        echo '<div class="service-commitments"><span class="dashicons dashicons-shield" title="' . __('Use', 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . __('Use', 'rrze-servicekatalog') . ': </span>';
                                        echo '<a class="service-commitment" href="' . esc_attr($commitmentURL) . '" style="background-color:' . esc_attr($commitmentBgColor) . ';color:' . esc_attr($commitmentTextColor) . ';">' . esc_html($commitmentName) . '</a>';
                                        echo '</div>';
                                    }
                                    if ($groupTerms) {
                                        foreach ($groupTerms as $groupTerm) {
                                            $groupName = $groupTerm->name;
                                            $groupSlug = $groupTerm->slug;
                                            $groupURL = get_term_link($groupSlug, 'rrze-service-target-group');
                                            $groupLinks[] = '<a class="service-group" href="' . esc_attr($groupURL) . '">' . esc_html($groupName) . '</a>';
                                        }
                                        echo '<div class="service-groups"><span class="dashicons dashicons-admin-users" title="' . _n('Target Group', 'Target Groups', count($groupTerms), 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . _n('Target Group', 'Target Groups', count($groupTerms), 'rrze-servicekatalog') . ': </span>'
                                            . implode(', ', $groupLinks)
                                            . '</div>';
                                    } ?>
                                </div>
                                <div class="service-description">
                                    <?php echo wpautop(esc_html($description)); ?>
                                </div>
                                <?php if ($tags) {
                                    foreach ($tags as $tag) {
                                        $tagName = $tag->name;
                                        $tagSlug = $tag->slug;
                                        $tagURL = get_term_link($tagSlug, 'rrze-service-tag');
                                        $tagLinks[] = '<a class="service-group" href="' . esc_attr($tagURL) . '">' . strtoupper(esc_html($tagName)) . '</a>';
                                    }
                                    echo '<div class="service-tags"><span class="dashicons dashicons-tag" title="' . __('Tags', 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . __('Tags', 'rrze-servicekatalog') . ': </span>' . implode(', ', $tagLinks) . '</div>';
                                } ?>
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
