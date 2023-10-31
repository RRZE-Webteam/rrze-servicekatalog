<?php

namespace RRZE\Servicekatalog\Shortcodes;

use RRZE\Servicekatalog\CPT\Service;
use RRZE\Servicekatalog\Utils;

use function RRZE\Servicekatalog\Config\getShortcodeSettings;

defined('ABSPATH') || exit;

class Servicekatalog
{
    public function __construct() {
        $this->settings = getShortcodeSettings();
        add_action('admin_enqueue_scripts', [$this, 'enqueueGutenberg']);
        add_action('init', [$this, 'initGutenberg']);
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        add_shortcode('servicekatalog', [$this, 'shortcodeOutput']);
    }

    public function shortcodeOutput($atts, $content = "") {
        $atts = self::sanitizeAtts($atts);

        if (!empty($atts['target-group'])) {
            $atts['group'] = $atts['target-group'];
        }
        foreach ($atts as $k => $v) {
            if (!is_array($v) // Shortcode
                && in_array($k, ['group', 'commitment', 'tag', 'id', 'hide'])) {
                $atts[$k] = Utils::strListToArray($atts[$k], 'sanitize_title');
            } elseif (is_array($v) // Block
                && isset($atts[$k][0])
                && $atts[$k][0] == '0') { // "all" selected
                $atts[$k] = [];
            }
        }
        $groups = $atts['group'];
        $commitments = $atts['commitment'];
        $tags = $atts['tag'];
        $IDs = $atts['id'];
        $hideItems = $atts['hide'];
        $orderby = $atts['orderby'];

        $getParams = Utils::array_map_recursive('sanitize_text_field', $_GET);

        $args = [
            'post_type' => Service::POST_TYPE,
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => [
                'relation' => 'AND',
            ],
        ];

        if (isset($getParams['search']) && $getParams['search'] != '') {
            //$args['s'] = $getParams['search'];
            $args['title'] = $getParams['search'];
        }

        // Target Groups
        if (isset($getParams['group'])) {
            $groups = $getParams['group'];
            $spanGroupsSelected = '<span class="filter-count">' . count($groups) . '</span>';
        } else {
            $spanGroupsSelected = '';
        }
        if (!empty($groups)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'rrze-service-target-group',
                'field' => 'slug',
                'terms' => $groups,
            );
        }

        // Commitment Levels
        if (isset($getParams['commitment'])) {
            $commitments = $getParams['commitment'];
            $spanCommitmentsSelected = '<span class="filter-count">' . count($commitments) . '</span>';
        } else {
            $spanCommitmentsSelected = '';
        }
        if (!empty($commitments)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'rrze-service-commitment',
                'field' => 'slug',
                'terms' => $commitments,
            );
        }

        // Tags
        if (isset($getParams['tag'])) {
            $tags = $getParams['tag'];
            $spanTagsSelected = '<span class="filter-count">' . count($tags) . '</span>';
        } else {
            $spanTagsSelected = '';
        }
        if (!empty($tags)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'rrze-service-tag',
                'field' => 'slug',
                'terms' => $tags,
            );
        }

        // IDs
        if (!empty($IDs)) {
            $args['post__in'] = $IDs;
        }

        $services = get_posts($args );

        switch($orderby) {
            case 'commitment':
                foreach($services as $service) {
                    $commitmentTerms = get_the_terms($service->ID, 'rrze-service-commitment');
                    if (!is_wp_error($commitmentTerms)) {
                        $orderNumber = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-order', true);
                        $servicesOrdered[$orderNumber][] = $service;
                    }
                }
                break;
            case 'group':
                foreach($services as $service) {
                    $groupTerms = get_the_terms($service->ID, 'rrze-service-target-group');
                    if (!is_wp_error($groupTerms)) {
                        foreach($groupTerms as $groupTerm) {
                            $servicesOrdered[$groupTerm->name][] = $service;
                        }
                    }
                }
                break;
            case 'tag':
                foreach($services as $service) {
                    $tagTerms = get_the_terms($service->ID, 'rrze-service-tag');
                    if (!is_wp_error($tagTerms)) {
                        foreach($tagTerms as $tagTerm) {
                            $servicesOrdered[$tagTerm->name][] = $service;
                        }
                    }
                }
                break;
            default:
                $servicesOrdered['unordered'] = $services;
        }
        ksort($servicesOrdered);

        $output = '<div class="rrze-servicekatalog">';

        /*
         * Filter Area
         */
        $showFilter = in_array($atts['searchform'], [true, 'true', '1', 'yes', 'ja', 'on']);
        if ($showFilter) {
            $taxCommitments = get_terms([
                'taxonomy' => 'rrze-service-commitment',
                'hide_empty' => false,
                'orderby' => 'meta_value_num',
                'meta_key'  => 'rrze-service-commitment-order',]);
            $taxGroups = get_terms([
                'taxonomy' => 'rrze-service-target-group',
                'hide_empty' => false,
                'orderby' => 'name',
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => 'rrze-service-group-internal',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => 'rrze-service-group-internal',
                        'value' => 'on',
                        'compare' => '!=',
                    ],
                ],
                ],
                );
            $taxTags = get_terms([
                'taxonomy' => 'rrze-service-tag',
                'hide_empty' => false,
                'orderby' => 'name',]);

            $output .= '<form method="get" class="servicekatalog-filter">'
                . '<div class="search-title">'
                . '<label for="rrze-servicekatalog-search" class="label">' . __('Search term', 'rrze-servicekatalog') . '</label><input type="text" name="search" id="rrze-servicekatalog-search" placeholder="' . __('Search for...', 'rrze-servicekatalog') . '" value="' . ($getParams['search'] ?? "") . '">'
                 . '</div>';

            if (!is_wp_error($taxCommitments) && !empty($taxCommitments)) {
                $output .= '<div class="filter-commitment">'
                    . '<button type="button" class="checklist-toggle">' . __('Use', 'rrze-servicekatalog') . $spanCommitmentsSelected . '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></button><div class="checklist">';
                foreach ($taxCommitments as $taxCommitment) {
                    $output .= '<label><input type="checkbox" name="commitment[]" value="' . $taxCommitment->slug . '"' . (isset($getParams['commitment']) && in_array($taxCommitment->slug, $getParams['commitment']) ? "checked" : "") . '>' . $taxCommitment->name . '</label>';
                }
                $output .= '</div></div>';
            }
            if (!is_wp_error($taxGroups) && !empty($taxGroups)) {
                $output .= '<div class="filter-group">'
                    . '<button type="button" class="checklist-toggle">' . __('Target Groups', 'rrze-servicekatalog') . $spanGroupsSelected . '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></button><div class="checklist">';
                foreach ($taxGroups as $taxGroup) {
                    $output .= '<label><input type="checkbox" name="group[]" value="' . $taxGroup->slug . '"' . (isset($getParams['group']) && in_array($taxGroup->slug, $getParams['group']) ? "checked" : "") . '>' . $taxGroup->name . '</label>';
                }
                $output .= '</div></div>';
            }
            if (!is_wp_error($taxTags) && !empty($taxTags)) {
                $output .= '<div class="filter-tag">'
                    . '<button type="button" class="checklist-toggle">' . __('Tags', 'rrze-servicekatalog') . $spanTagsSelected . '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></button><div class="checklist">';
                foreach ($taxTags as $taxTag) {
                    $output .= '<label><input type="checkbox" name="tag[]" value="' . $taxTag->slug . '"' . (isset($getParams['tag']) && in_array($taxTag->slug, $getParams['tag']) ? "checked" : "") . '>' . ucfirst($taxTag->name) . '</label>';
                }
                $output .= '</div></div>';
            }
            $output .= '<input type="submit" value="' . _x('Search', 'Verb, infinitive', 'rrze-servicekatalog') . '">'
                //. '</div>'
                . '</form>';
        }

        /*
         * Output
         */
        if (count($services) < 1) {
            $output .= __('No services found.', 'rrze-servicekatalog');
        } else {
            // Hide Items
            $showThumbnail = true;
            $showCommitment = true;
            $showGroup = true;
            $showTags = true;
            $showDescription = true;
            $showUrlPortal = true;
            $showUrlDescription = true;
            $showUrlTutorial = true;
            $showUrlVideo = true;
            if (isset($hideItems)) {
                $showThumbnail = !in_array('thumbnail', $hideItems);
                $showCommitment = !in_array('commitment', $hideItems);
                $showGroup = !in_array('group', $hideItems);
                $showTags = !in_array('tag', $hideItems);
                $showDescription = !in_array('description', $hideItems);
                $showUrlPortal = !in_array('url-portal', $hideItems);
                $showUrlDescription = !in_array('url-description', $hideItems);
                $showUrlTutorial = !in_array('url-tutorial', $hideItems);
                $showUrlVideo = !in_array('url-video', $hideItems);
                if (in_array('urls', $hideItems)) {
                    $showUrlPortal = $showUrlDescription = $showUrlTutorial = $showUrlVideo = false;
                }
            }
            // Services List / Grid
            $layout = $atts['display'] == 'list' ? 'list' : 'grid';
            $output .= '<ul class="display-' . $layout . '">';
            $prevID = '';
            foreach ($servicesOrdered as $services) {
                foreach ($services as $service) {
                    if ($service->ID == $prevID)
                        continue;
                    $prevID = $service->ID;
                    $commitmentTerms = get_the_terms($service->ID, 'rrze-service-commitment');
                    $commitmentBgColor = '#fff';
                    $commitmentName = '';
                    if ($commitmentTerms) {
                        $commitmentName = $commitmentTerms[0]->name;
                        $commitmentSlug = $commitmentTerms[0]->slug;
                        $commitmentURL = get_term_link($commitmentSlug, 'rrze-service-commitment');
                        $commitmentBgColor = get_term_meta($commitmentTerms[0]->term_id, 'rrze-service-commitment-color', TRUE);
                        //$commitmentTextColor = Utils::calculateContrastColor($commitmentBgColor);
                        $commitmentLink = '<a href="' . esc_attr($commitmentURL) . '">' . esc_html($commitmentName) . '</a>';
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
                            $groupLinks[] = '<a href="' . esc_attr($groupURL) . '">' . esc_html($groupName) . '</a>';
                            $groupNames[] = esc_html($groupName);
                        }
                    }
                    $tags = get_the_terms($service->ID, 'rrze-service-tag');
                    $tagLinks = [];
                    $tagNames = [];
                    if ($tags) {
                        foreach ($tags as $tag) {
                            $tagName = $tag->name;
                            $tagSlug = $tag->slug;
                            $tagURL = get_term_link($tagSlug, 'rrze-service-tag');
                            $tagLinks[] = '<a href="' . esc_attr($tagURL) . '">' . strtoupper(esc_html($tagName)) . '</a>';
                            $tagNames[] = strtoupper(esc_html($tagName));
                        }
                    }
                    $postMeta = get_post_meta($service->ID);
                    $description = Utils::getMeta($postMeta, 'description');
                    $links['portal']['label'] = __('Portal', 'rrze-servicekatalog');
                    $links['portal']['url'] = Utils::getMeta($postMeta, 'url-portal');
                    $links['portal']['icon'] = 'dashicons-admin-home';
                    $links['description']['label'] = __('Service Description', 'rrze-servicekatalog');
                    $links['description']['url'] = Utils::getMeta($postMeta, 'url-description');
                    $links['description']['icon'] = 'dashicons-info';
                    $links['tutorial']['label'] = __('Tutorial', 'rrze-servicekatalog');
                    $links['tutorial']['url'] = Utils::getMeta($postMeta, 'url-tutorial');
                    $links['tutorial']['icon'] = 'dashicons-book';
                    $links['video']['label'] = __('Video Tutorial', 'rrze-servicekatalog');
                    $links['video']['url'] = Utils::getMeta($postMeta, 'url-video');
                    $links['video']['icon'] = 'dashicons-video-alt2';

                    $output .= '<li class="service-preview"><div class="service-preview-content"><a href="' . get_permalink($service->ID) . '" class="service-link">';
                    if (has_post_thumbnail($service->ID) && $showThumbnail) {
                        //$output .= '<a class="service-link" href="' . get_permalink($service->ID) . '" style="border-color: ' . $commitmentBgColor . ';">' . get_the_post_thumbnail($service->ID, 'medium') . '</a>';
                        $output .= get_the_post_thumbnail($service->ID, 'medium', ['style' => 'border-color: ' . $commitmentBgColor]);
                    } else {
                        $output .= '<div style="height: 5px; background:' . $commitmentBgColor . ';" aria-hidden="true"></div>';
                    }
                    $output .= '</a>';
                    $output .= '<div class="service-details" style="border-color: ' . $commitmentBgColor . ';">'
                        . '<a class="service-title" href="' . get_permalink($service->ID) . '">' . $service->post_title . '</a>';
                    if ($showDescription) {
                        $output .= '<div class="service-description">' . $description . '</div>';
                    }
                    if (($showUrlPortal && $links['portal']['url'] != '')
                        || ($showUrlDescription && $links['description']['url'] != '')
                        || ($showUrlTutorial && $links['tutorial']['url'] != '')
                        || ($showUrlVideo && $links['video']['url'] != '')) {
                        $output .= '<div class="service-urls"><ul>';
                        foreach ($links as $link) {
                            if ($link['url'] != '') {
                                //$output .= '<li><span class="dashicons ' . $link['icon'] . '"></span><a href="' . $link['url'] . '">' . $link['label'] . '</a></li>';
                                $output .= '<li>' . do_shortcode('[button url="' . $link['url'] . '" style="ghost" size="small"]' . $link['label'] . '[/button]') . '</li>';
                            }
                        }
                        $output .= '</ul></div>';
                    }
                    if ($showCommitment || $showGroup || $showTags) {
                        $output .= '<div class="service-meta">';
                        if ($commitmentTerms && $showCommitment) {
                            $output .= '<div class="service-commitments"><span class="dashicons dashicons-shield" title="' . __('Use', 'rrze-servicekatalog') . '" style="color:' . $commitmentIconColor . ';" aria-hidden="true"></span><span class="screen-reader-text">' . __('Use', 'rrze-servicekatalog') . ': </span>' . $commitmentLink . '</div>';
                        }
                        if ($groupTerms && $showGroup) {
                            $output .= '<div class="service-groups"><span class="dashicons dashicons-admin-users" title="' . __('Target Group', 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . __('Target Group', 'rrze-servicekatalog') . ': </span>'
                                . implode(', ', $groupLinks)
                                . '</div>';
                        }
                        if ($tags && $showTags) {
                            $output .= '<div class="service-tags"><span class="dashicons dashicons-tag" title="' . _n('Tag', 'Tags', count($tags), 'rrze-servicekatalog') . '" aria-hidden="true"></span><span class="screen-reader-text">' . __('Tags', 'rrze-servicekatalog') . ': </span>'
                                . implode(', ', $tagLinks)
                                . '</div>';
                        }
                        $output .= '</div>';
                    }
                    $output .= '</div>';
                    $output .= '</div></li>';
                }
            }
            $output .= '</ul>';
        }
        $output .= '</div>';

        wp_enqueue_style('rrze-servicekatalog');
        if ($showFilter) {
            wp_enqueue_script('rrze-servicekatalog-sc');
        }

        return $output;
    }

    private function sanitizeAtts($atts) {
        $defaults = [
            'group' => '',
            'target-group' => '',
            'commitment' => '',
            'tag' => '',  // Multiple tags (slugs) are separated by commas
            'id' => '',  // Multiple ids are separated by commas
            'number' => 0,       // Number of services to show. Default value: 0
            'show' => '',
            //'hide' => 'description, url-portal, url-description, url-tutorial, url-video',
            'hide' => '',
            'display' => 'grid',
            'searchform' => '',
            'orderby' => '',
            ];
        $args = shortcode_atts($defaults, $atts);
        array_walk($args, 'sanitize_text_field');
        return $args;
    }

    public function fillGutenbergOptions(): array {
        // fill selects "category" and "tag"
        $fields = array('target-group', 'commitment', 'tag');
        foreach ($fields as $field) {
            // set new params for gutenberg / the old ones are used for shortcode in classic editor
            $this->settings[$field]['values'] = array();
            $this->settings[$field]['field_type'] = 'multi_select';
            $this->settings[$field]['default'] = array(0);
            $this->settings[$field]['type'] = 'array';
            $this->settings[$field]['items'] = array('type' => 'string');
            $this->settings[$field]['values'][] = ['id' => 0, 'val' => __('-- all --', 'rrze-servicekatalog')];

            // get categories and tags from this website
            $terms = get_terms([
                'taxonomy' => 'rrze-service-' . $field,
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            ]);

            foreach ($terms as $term) {
                $this->settings[$field]['values'][] = [
                    'id' => $term->slug,
                    'val' => $term->name,
                ];
            }
        }

        // fill select id ( = FAQ )
        $faqs = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => 'rrze-service',
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        $this->settings['id']['values'] = array();
        $this->settings['id']['field_type'] = 'multi_select';
        $this->settings['id']['default'] = array(0);
        $this->settings['id']['type'] = 'array';
        $this->settings['id']['items'] = array('type' => 'number');
        $this->settings['id']['values'][] = ['id' => 0, 'val' => __('-- all --', 'rrze-servicekatalog')];
        foreach ($faqs as $faq) {
            $this->settings['id']['values'][] = [
                'id' => $faq->ID,
                'val' => str_replace("'", "", str_replace('"', "", $faq->post_title)),
            ];
        }

        return $this->settings;
    }

    public function isGutenberg()
    {
        $postID = get_the_ID();
        if ($postID && !use_block_editor_for_post($postID)) {
            return false;
        }

        return true;
    }

    public function initGutenberg()
    {
        if (!$this->isGutenberg()) {
            return;
        }

        // get prefills for dropdowns
        $this->settings = $this->fillGutenbergOptions();

        // register js-script to inject php config to call gutenberg lib
        $editor_script = $this->settings['block']['blockname'] . '-block';
        $js = '../../assets/js/' . $editor_script . '.js';
        $css = '../../assets/css/' . $editor_script . '.css';

        wp_register_script(
            $editor_script,
            plugins_url($js, __FILE__),
            array(
                'RRZE-Gutenberg',
            ),
            null
        );
        wp_localize_script($editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings);

        wp_register_style(
            $editor_script,
            plugins_url($css, __FILE__),
        );

        // register block
        register_block_type(
            $this->settings['block']['blocktype'],
            array(
                'editor_script' => $editor_script,
                'render_callback' => [$this, 'shortcodeOutput'],
                'attributes' => $this->settings,
                'editor_style' => $editor_script,
            )
        );
    }

    public function enqueueGutenberg()
    {
        if (!$this->isGutenberg()) {
            return;
        }

        // include gutenberg lib
        wp_enqueue_script(
            'RRZE-Gutenberg',
            plugins_url('../assets/js/gutenberg.js', __FILE__),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor',
            ),
            null
        );
    }
}