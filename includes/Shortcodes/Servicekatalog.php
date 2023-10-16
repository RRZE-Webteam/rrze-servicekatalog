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
            'display' => 'grid',
            'searchform' => ''
        ];
        $atts = shortcode_atts($atts_default, $atts);

        $getParams = Utils::array_map_recursive('sanitize_text_field', $_GET);

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
        }
        if (isset($getParams['group'])) {
            $groups = $getParams['group'];
        }
        if (isset($groups)) {
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
        }
        if (isset($getParams['commitment'])) {
            $commitments = $getParams['commitment'];
        }
        if (isset($commitments)) {
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
        if ($atts['commitment'] != '') {
            $tags = Utils::strListToArray($atts['tag'], 'sanitize_title');
        }
        if (isset($getParams['tag'])) {
            $tags = $getParams['tag'];
        }
        if (isset($tags)) {
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
            $IDs = Utils::strListToArray($atts['ids'], 'intval');
            $args['post__in'] = $IDs;
        }

        $showThumbnail = true;
        $showCommitment = true;
        $showGroup = true;
        $showTags = true;
        if ($atts['hide'] != '') {
            $hideItems = Utils::strListToArray($atts['hide'], 'sanitize_title');
            $showThumbnail = !in_array('thumbnail', $hideItems);
            $showCommitment = !in_array('commitment', $hideItems);
            $showGroup = !in_array('group', $hideItems);
            $showTags = !in_array('tag', $hideItems);
        }

        $services = get_posts($args );

        $output = '<div class="rrze-servicekatalog">';

        // Filter Area
        $showFilter = in_array($atts['searchform'], ['true', '1', 'yes', 'ja']);
        if ($showFilter) {
            $taxCommitments = get_terms([
                'taxonomy' => 'rrze-service-commitment',
                'hide_empty' => false,
                'orderby' => 'meta_value_num',
                'meta_key'  => 'rrze-service-commitment-order',]);
            $taxGroups = get_terms([
                'taxonomy' => 'rrze-service-target-group',
                'hide_empty' => false,
                'orderby' => 'name',]);
            $taxTags = get_terms([
                'taxonomy' => 'rrze-service-tag',
                'hide_empty' => false,
                'orderby' => 'name',]);

            $output .= '<form method="get" class="servicekatalog-filter">'
                . '<div class="search-title">'
                . '<label for="rrze-servicekatalog-search" class="label">' . __('Search term', 'rrze-servicekatalog') . '</label><input type="text" name="search" id="rrze-servicekatalog-search" placeholder="' . __('Search for...', 'rrze-servicekatalog') . '" value="' . ($getParams['search'] ?? "") . '">'
                 . '</div>';

            if (!is_wp_error($taxCommitments)) {
                $output .= '<div class="filter-commitment">'
                    . '<span class="label">' . __('Use', 'rrze-servicekatalog') . '</span>';
                foreach ($taxCommitments as $taxCommitment) {
                    $output .= '<label><input type="checkbox" name="commitment[]" value="' . $taxCommitment->slug . '"' . (isset($getParams['commitment']) && in_array($taxCommitment->slug, $getParams['commitment']) ? "checked" : "") . '>' . $taxCommitment->name . '</label>';
                }
                $output .= '</div>';
            }
            if (!is_wp_error($taxGroups)) {
                $output .= '<div class="filter-group"><span class="label">' . __('Target Groups', 'rrze-servicekatalog') . '</span>';
                foreach ($taxGroups as $taxGroup) {
                    $output .= '<label><input type="checkbox" name="group[]" value="' . $taxGroup->slug . '"' . (isset($getParams['group']) && in_array($taxGroup->slug, $getParams['group']) ? "checked" : "") . '>' . $taxGroup->name . '</label>';
                }
                $output .= '</div>';
            }
            if (!is_wp_error($taxTags)) {
                $output .= '<div class="filter-tag"><span class="label">' . __('Tags', 'rrze-servicekatalog') . '</span>';
                foreach ($taxTags as $taxTag) {
                    $output .= '<label><input type="checkbox" name="tag[]" value="' . $taxTag->slug . '"' . (isset($getParams['tag']) && in_array($taxTag->slug, $getParams['tag']) ? "checked" : "") . '>' . ucfirst($taxTag->name) . '</label>';
                }
                $output .= '</div>';
            }
            $output .= '<input type="submit" value="' . _x('Search', 'Verb, infinitive', 'rrze-servicekatalog') . '">'
                //. '</div>'
                . '</form>';
        }

        if (count($services) < 1) {
            $output .= __('No services found.', 'rrze-servicekatalog');
        } else {
            // Services List / Grid
            $layout = $atts['display'] == 'list' ? 'list' : 'grid';
            $output .= '<ul class="display-' . $layout . '">';
            foreach ($services as $service) {
                $commitmentTerms = get_the_terms($service->ID, 'rrze-service-commitment');
                $commitmentBgColor = '#fff';
                $commitmentName = '';
                if ($commitmentTerms) {
                    $commitmentName = $commitmentTerms[0]->name;
                    $commitmentSlug = $commitmentTerms[0]->slug;
                    $commitmentURL = get_term_link($commitmentSlug, 'rrze-service-commitment');
                    $commitmentBgColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', TRUE);
                    //$commitmentTextColor = Utils::calculateContrastColor($commitmentBgColor);
                    //$commitmentLink = '<a href="' . esc_attr($commitmentURL) . '">' . esc_html($commitmentName) . '</a>';
                }
                $commitmentIconColor = in_array($commitmentBgColor, ['#fff', '#ffffff']) ? '#5b5858' : $commitmentBgColor;
                $groupTerms = get_the_terms($service->ID, 'rrze-service-target-group');
                $groupLinks = [];
                $groupNames = [];
                if ($groupTerms) {
                    foreach ($groupTerms as $groupTerm) {
                        $groupName = $groupTerm->name;
                        $groupSlug = $groupTerm->slug;
                        $groupURL = get_term_link($groupSlug, 'rrze-service-target-group');
                        //$groupLinks[] = '<a class="service-group" href="' . esc_attr($groupURL) . '">' . esc_html($groupName) . '</a>';
                        $groupNames[] = esc_html($groupName);
                    }
                }
                $tags = get_the_terms($service->ID, 'rrze-service-tag');
                $tagLinks = [];
                $tagNames = [];
                if ($tags) {
                    foreach ($tags as $tag) {
                        $tagName = $tag->name;
                        //$tagSlug = $tag->slug;
                        //$tagURL = get_term_link($tagSlug, 'rrze-service-tag');
                        //$tagLinks[] = '<a class="service-group" href="' . esc_attr($tagURL) . '">' . strtoupper(esc_html($tagName)) . '</a>';
                        $tagNames[] = strtoupper(esc_html($tagName));
                    }
                }

                $output .= '<li class="service-preview"><a href="' . get_permalink($service->ID) . '" class="service-link"  style="border-color: ' . $commitmentBgColor . ';">';
                if (has_post_thumbnail($service->ID) && $showThumbnail) {
                    //$output .= '<a class="service-link" href="' . get_permalink($service->ID) . '" style="border-color: ' . $commitmentBgColor . ';">' . get_the_post_thumbnail($service->ID, 'medium') . '</a>';
                    $output .= get_the_post_thumbnail($service->ID, 'medium');
                }
                $output .= '<div class="service-details" style="border-color: ' . $commitmentBgColor . ';">'
                    //. '<a class="service-title" href="' . get_permalink($service->ID) . '">' . $service->post_title . '</a>';
                    . '<span class="service-title">' . $service->post_title . '</span>';
                if ($showCommitment || $showGroup || $showTags) {
                    $output .= '<div class="service-meta">'
                        . ($commitmentTerms && $showCommitment ? '<div class="service-commitment"><span class="dashicons dashicons-shield" title="' . __('Use', 'rrze-servicekatalog') . '" style="color:' . $commitmentIconColor . ';" aria-hidden="true"></span><span class="screen-reader-text">' . __('Use', 'rrze-servicekatalog') . ': </span>' . $commitmentName . '</div>' : '')
                        . ($groupTerms && $showGroup ? '<div class="service-groups"><span class="dashicons dashicons-admin-users" title="' . _n('Target Group', 'Target Groups', count($groupTerms), 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . _n('Target Group', 'Target Groups', count($groupTerms), 'rrze-servicekatalog') . ': </span>' . implode(', ', $groupNames) . '</div>' : '')
                        . ($tags && $showTags ? '<div class="service-tags"><span class="dashicons dashicons-tag" title="' . _n('Target Group', 'Target Groups', count($tags), 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . __('Tags', 'rrze-servicekatalog') . ': </span>' . implode(', ', $tagNames) . '</div>' : '')
                        . '</div>';
                }
                $output .= '</div>';
                $output .= '</a></li>';
            }
            $output .= '</ul>';
        }
        $output .= '</div>';

        wp_enqueue_style('rrze-servicekatalog');

        return $output;
    }
}