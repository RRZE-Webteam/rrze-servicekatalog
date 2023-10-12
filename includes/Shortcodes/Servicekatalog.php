<?php

namespace RRZE\Servicekatalog\Shortcodes;

use RRZE\Servicekatalog\CPT\Service;
use RRZE\Servicekatalog\Utils;

defined('ABSPATH') || exit;

class Servicekatalog
{
    public static function init()
    {
        add_shortcode('servicekatalog', [__CLASS__, 'shortcode']);
    }

    public static function shortcode($atts, $content = "") {
        $atts_default = [
            'group' => '',
            'commitment' => '',
            'tag' => '',  // Multiple tags (slugs) are separated by commas
            'ids' => '',  // Multiple ids are separated by commas
            'number' => 0,       // Number of services to show. Default value: 0
            'show' => '',
            'hide' => '',
            'layout' => 'grid',
        ];
        $atts = shortcode_atts($atts_default, $atts);

        $args = [
            'post_type' => Service::POST_TYPE,
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
            ],
            'orderby' => 'title',
        ];

        // Target Groups
        if ($atts['group'] != '') {
            $groups = Utils::strListToArray($atts['group'], 'sanitize_title');
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'rrze-service-target-group',
                    'field' => 'slug',
                    'terms' => $groups,
                )
            );
        }

        // Commitment Levels
        if ($atts['commitment'] != '') {
            $commitments = Utils::strListToArray($atts['commitment'], 'sanitize_title');
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'rrze-service-commitment',
                    'field' => 'slug',
                    'terms' => $commitments,
                )
            );
        }

        // Tags
        if ($atts['tag'] != '') {
            $tags = Utils::strListToArray($atts['tag'], 'sanitize_title');
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'rrze-service-tag',
                    'field' => 'slug',
                    'terms' => $tags,
                )
            );
        }

        // IDs
        if ($atts['ids'] != '') {
            $IDs = Utils::strListToArray($atts['id'], 'intval');
            $args['post__in'] = $IDs;
        }

        $services = get_posts($args );
        //var_dump($services);

        if (count($services) < 1) {
            return __('No services found.', 'rrze-servicekatalog');
        }

        $layout = $atts['layout'] == 'list' ? 'list' : 'grid';
        echo '<ul class="rrze-servicekatalog display-' . $layout . '">';
        foreach ($services as $service) {
            $commitmentTerms = get_the_terms( $service->ID, 'rrze-service-commitment');
            $commitmentBgColor = '#fff';
            if ($commitmentTerms) {
                $commitmentName = $commitmentTerms[0]->name;
                $commitmentSlug = $commitmentTerms[0]->slug;
                $commitmentURL = get_term_link($commitmentSlug, 'rrze-service-commitment');
                $commitmentBgColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', true);
                //$commitmentTextColor = Utils::calculateContrastColor($commitmentBgColor);
                $commitmentOut = '<a href="' . esc_attr($commitmentURL) . '">' . esc_html($commitmentName) . '</a>';
            }
            $groupTerms = get_the_terms( $service->ID, 'rrze-service-target-group');
            $groupLinks = [];
            if ($groupTerms) {
                foreach ($groupTerms as $groupTerm) {
                    $groupName = $groupTerm->name;
                    $groupSlug = $groupTerm->slug;
                    $groupURL = get_term_link($groupSlug, 'rrze-service-target-group');
                    $groupLinks[] = '<a class="service-group" href="' . esc_attr($groupURL) . '">' . esc_html($groupName) . '</a>';
                }
            }
            $tags = get_the_terms( $service->ID, 'rrze-service-tag');
            $tagLinks = [];
            if ($tags) {
                foreach ($tags as $tag) {
                    $tagName = $tag->name;
                    $tagSlug = $tag->slug;
                    $tagURL = get_term_link($tagSlug, 'rrze-service-tag');
                    $tagLinks[] = '<a class="service-group" href="' . esc_attr($tagURL) . '">' . strtoupper(esc_html($tagName)) . '</a>';
                }
            }
            echo '<li class="service-preview">';
            if (has_post_thumbnail($service->ID)) {
                //echo get_the_post_thumbnail($service->ID, 'medium');
                echo '<a class="service-link" href="' . get_permalink($service->ID) . '">' . get_the_post_thumbnail($service->ID, 'medium') . '</a>';
            }
            echo '<div class="service-details" style="border-top: 5px solid ' . $commitmentBgColor . ';">'
                . '<a class="service-title" href="' . get_permalink($service->ID) . '">' . $service->post_title . '</a>'
                . '<div class="service-meta">'
                . ($commitmentTerms ? '<div class="service-commitment"><span class="dashicons dashicons-shield" title="' . __('Use', 'rrze-servicekatalog') . '" style="color:' . $commitmentBgColor . ';" aria-hidden="true"></span><span class="screen-reader-text">' . __('Use', 'rrze-servicekatalog') . ': </span>' . $commitmentOut . '</div>' : '')
                . ($groupTerms ? '<div class="service-groups"><span class="dashicons dashicons-admin-users" title="' . _n('Target Group', 'Target Groups', count($groupTerms), 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . _n('Target Group', 'Target Groups', count($groupTerms), 'rrze-servicekatalog') . ': </span>' . implode(', ', $groupLinks) . '</div>' : '')
                . ($tags ? '<div class="service-tags"><span class="dashicons dashicons-tag" title="' . _n('Target Group', 'Target Groups', count($tags), 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . __('Tags', 'rrze-servicekatalog') . ': </span>' . implode(', ', $tagLinks) . '</div>' : '')
                . '</div>'
                . '</div>';
            echo '</li>';
        }
        echo '</ul>';

        wp_enqueue_style('rrze-servicekatalog');
    }
}