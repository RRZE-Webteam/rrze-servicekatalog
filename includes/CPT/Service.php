<?php

/* ---------------------------------------------------------------------------
 * Custom Post Type 'rrze-service'
 * ------------------------------------------------------------------------- */

namespace RRZE\Servicekatalog\CPT;

use function RRZE\Servicekatalog\plugin;

defined('ABSPATH') || exit;

class Service
{
    const POST_TYPE = 'rrze-service';

    public static function init()
    {
        // Register Post Type.
        add_action('init', [__CLASS__, 'registerPostType']);
        // Register Taxonomies.
        add_action('init', [__CLASS__, 'registerTaxonomies']);
        // CMB2 Fields
        add_action('cmb2_admin_init', [__CLASS__, 'serviceFields']);
        add_action('cmb2_admin_init', [__CLASS__, 'serviceCategoryCommitmentFields']);
        // Templates
        add_filter('single_template', [__CLASS__, 'includeSingleTemplate']);
        add_filter('archive_template', [__CLASS__, 'includeArchiveTemplate']);
    }

    public static function registerPostType()
    {
        $labels = [
            'name'               => _x('Services', 'post type general name', 'rrze-servicekatalog'),
            'singular_name'      => _x('Service', 'post type singular name', 'rrze-servicekatalog'),
            'menu_name'          => _x('Services', 'admin menu', 'rrze-servicekatalog'),
            'name_admin_bar'     => _x('Services', 'add new on admin bar', 'rrze-servicekatalog'),
            'add_new'            => _x('Add New', 'admin menu', 'rrze-servicekatalog'),
            'add_new_item'       => __('Add New Service', 'rrze-servicekatalog'),
            'new_item'           => __('New Service', 'rrze-servicekatalog'),
            'edit_item'          => __('Edit Service', 'rrze-servicekatalog'),
            'view_item'          => __('View Service', 'rrze-servicekatalog'),
            'all_items'          => __('All Services', 'rrze-servicekatalog'),
            'search_items'       => __('Search Services', 'rrze-servicekatalog'),
            'parent_item_colon'  => __('Parent Services:', 'rrze-servicekatalog'),
            'not_found'          => __('No services found.', 'rrze-servicekatalog'),
            'not_found_in_trash' => __('No services found in Trash.', 'rrze-servicekatalog'),
            'featured_image'        => __( 'Service icon', 'rrze-servicekatalog' ),    //used in post.php
            'set_featured_image'    => __( 'Set service icon', 'rrze-servicekatalog' ),    //used in post.php
            'remove_featured_image' => __( 'Remove service icon', 'rrze-servicekatalog' ), //used in post.php
            'use_featured_image'    => __( 'Use as service icon', 'rrze-servicekatalog' ), //used in post.php
            'insert_into_item'      => __( 'Insert into service', 'rrze-servicekatalog' ),  //used in post.php
            'uploaded_to_this_item' => __( 'Uploaded to this service', 'rrze-servicekatalog' ), //used in post.php

        ];

        $args = [
            'labels'             => $labels,
            'hierarchical'       => false,
            'public'             => true,
            'supports'           => ['title', 'author', 'thumbnail'],
            'menu_icon'          => 'dashicons-portfolio',
            'capability_type'    => 'page',
            'has_archive'        => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public static function registerTaxonomies()
    {
        // Target Groups
        $labels = [
            'name'              => _x('Target Groups', 'Taxonomy general name', 'rrze-servicekatalog'),
            'singular_name'     => _x('Target Group', 'Taxonomy singular name', 'rrze-servicekatalog'),
            'edit_item'     => _x('Edit Target Group', 'Taxonomy singular name', 'rrze-servicekatalog'),
            'add_new_item'     => _x('Add New Target Group', 'Taxonomy singular name', 'rrze-servicekatalog'),
        ];
        $args = [
            'labels'            => $labels,
            'public'            => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
        ];
        register_taxonomy('rrze-service-target-group', self::POST_TYPE, $args);

        // Usecases
        $labels = [
            'name'              => _x('Commitment Levels', 'Taxonomy general name', 'rrze-servicekatalog'),
            'singular_name'     => _x('Commitment Level', 'Taxonomy singular name', 'rrze-servicekatalog'),
            'edit_item'     => _x('Edit Commitment Level', 'Taxonomy singular name', 'rrze-servicekatalog'),
            'add_new_item'     => _x('Add New Commitment Level', 'Taxonomy singular name', 'rrze-servicekatalog'),
        ];
        $args = [
            'labels'            => $labels,
            'public'            => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
        ];
        register_taxonomy('rrze-service-commitment', self::POST_TYPE, $args);

        // Tags
        $labels = [
            'name'              => _x('Tags', 'Taxonomy general name', 'rrze-servicekatalog'),
            'singular_name'     => _x('Tag', 'Taxonomy singular name', 'rrze-servicekatalog'),
        ];
        $args = [
            'labels'            => $labels,
            'public'            => false,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true
        ];
        register_taxonomy('rrze-service-tag', self::POST_TYPE, $args);
    }

    public static function serviceFields()
    {
        global $wp_locale;

        // General Information
        $cmb = new_cmb2_box([
            'id' => 'rrze-service-info',
            'title' => __('General Information', 'rrze-servicekatalog'),
            'object_types' => [self::POST_TYPE],
            'context' => 'normal',
            'priority' => 'high',
            'show_names' => true,
        ]);
        $cmb->add_field(array(
            'name'    => esc_html__('Short Description', 'rrze-servicekatalog'),
            'desc'    => __('Max. 240 characters', 'rrze-servicekatalog'),
            'id'      => 'description',
            'type'    => 'wysiwyg',
            'options' => array(
                'textarea_rows' => 8,
                'teeny' => true,
            ),
        ));
        $cmb->add_field(array(
            'name' => esc_html__('URL Portal', 'rrze-servicekatalog'),
            //'desc' => esc_html__( '', 'rrze-servicekatalog' ),
            'id'   => 'url-portal',
            'type' => 'text_url',
        ));
        $cmb->add_field(array(
            'name' => esc_html__('URL Service Description', 'rrze-servicekatalog'),
            //'desc' => esc_html__( '', 'rrze-servicekatalog' ),
            'id'   => 'url-description',
            'type' => 'text_url',
        ));
        $cmb->add_field(array(
            'name' => esc_html__('URL Tutorial', 'rrze-servicekatalog'),
            //'desc' => esc_html__( '', 'rrze-servicekatalog' ),
            'id'   => 'url-tutorial',
            'type' => 'text_url',
        ));
        $cmb->add_field(array(
            'name' => esc_html__('URL Video Tutorial', 'rrze-servicekatalog'),
            //'desc' => esc_html__( '', 'rrze-servicekatalog' ),
            'id'   => 'url-video',
            'type' => 'text_url',
        ));
        $cmb->add_field( array(
            'name'           => esc_html__('Target Groups', 'rrze-servicekatalog'),
            //'desc'           => esc_html__('', 'rrze-servicekatalog'),
            'id'             => 'rrze-service-target-group',
            'taxonomy'       => 'rrze-service-target-group', //Enter Taxonomy Slug
            'type'           => 'taxonomy_multicheck',
            'show_option_none' => esc_html__('None', 'rrze-servicekatalog'),
            'remove_default' => 'true', // Removes the default metabox provided by WP core.
            // Optionally override the args sent to the WordPress get_terms function.
            'text'           => array(
                'no_terms_text' => esc_html__('Sorry, no target groups could be found.', 'rrze-servicekatalog') // Change default text. Default: "No terms"
            ),
            'query_args' => array(
                // 'orderby' => 'slug',
                // 'hide_empty' => true,
            ),
            'select_all_button' => true,
        ) );
        $cmb->add_field( array(
            'name'           => esc_html__('Commitment Level', 'rrze-servicekatalog'),
            //'desc'           => esc_html__('', 'rrze-servicekatalog'),
            'id'             => 'rrze-service-commitment',
            'taxonomy'       => 'rrze-service-commitment', //Enter Taxonomy Slug
            'type'           => 'taxonomy_select',
            //'show_option_none' => esc_html__('None', 'rrze-servicekatalog'),
            'show_option_none' => false,
            'remove_default' => 'true', // Removes the default metabox provided by WP core.
            // Optionally override the args sent to the WordPress get_terms function.
            'text'           => array(
                'no_terms_text' => esc_html__('Sorry, no target groups could be found.', 'rrze-servicekatalog') // Change default text. Default: "No terms"
            ),
            'query_args' => array(
                'orderby' => 'meta_value_num',
                'meta_key'  => 'rrze-service-commitment-order',
                'hide_empty' => false,
            ),
        ) );
    }

    /**
     * Hook in and add a metabox to add fields to taxonomy terms
     */
    public static function serviceCategoryCommitmentFields() {
        $prefix = 'rrze-service-commitment_';

        /**
         * Metabox to add fields to categories and tags
         */
        $cmb_term = new_cmb2_box( array(
            'id'               => $prefix . 'edit',
            'title'            => esc_html__( 'Color', 'rrze-servicekatalog' ), // Doesn't output for term boxes
            'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
            'taxonomies'       => array( 'rrze-service-commitment' ), // Tells CMB2 which taxonomies should have these fields
            'new_term_section' => true, // Will display in the "Add New Category" section
        ) );

        $cmb_term->add_field( array(
            'name'    => esc_html__( 'Color', 'rrze-servicekatalog' ),
            'id'      => 'rrze-service-commitment-color',
            'type'    => 'colorpicker',
            //'default' => '#2962FF',
            'attributes' => array(
                'data-colorpicker' => json_encode( array(
                    // Iris Options set here as values in the 'data-colorpicker' array
                    'palettes' => array( '#8dbd05', '#00a1ae', '#5e36cc', '#fe318e', '#ff7540', '#fd9800'),
                ) ),
            ),
            // 'options' => array(
            // 	'alpha' => true, // Make this a rgba color picker.
            // ),
        ) );
        $cmb_term->add_field([
            'name'      => esc_html__( 'Order Number', 'rrze-servicekatalog' ),
            'desc'      => esc_html__('The order in which this commitment level is displayed e.g. in select lists.', 'rrze-servicekatalog'),
            'id'        => 'rrze-service-commitment-order',
            'type'      => 'text_small',
            'attributes'    => [
                'type'  => 'number',
            ],
            'default'   => 0,
        ]);
    }

    public static function includeSingleTemplate($template_path)
    {
        global $post;

        if (!($post->post_type == 'rrze-service')) {
            return $template_path;
        }

        $template_path = plugin()->getPath() . 'templates/single-service.php';

        wp_enqueue_style('rrze-servicekatalog');

        return $template_path;
    }

    public static function includeArchiveTemplate($template_path)
    {
        global $post;
        if ($post->post_type == 'rrze-service' && is_archive()) {
            if ($theme_file = locate_template(array('archive-service.php'))) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin()->getPath() . '/templates/archive-service.php';
            }
        }

        wp_enqueue_style('rrze-servicekatalog');

        return $template_path;
    }
}
