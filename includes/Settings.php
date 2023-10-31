<?php

/* ---------------------------------------------------------------------------
 * Settings Page
* ------------------------------------------------------------------------- */

namespace RRZE\Servicekatalog;

use RRZE\Calendar\Shortcodes\Shortcode;

use function RRZE\Servicekatalog\Config\getShortcodeSettings;

defined('ABSPATH') || exit;

class Settings {

    public function __construct() {
        add_action('cmb2_admin_init', [$this, 'registerSettings']);
        add_action( 'cmb2_render_toggle', [$this, 'render_field' ], 10, 5 );
        add_action( 'admin_head', [$this, 'add_style' ] );
    }

    public function registerSettings() {
        $main_options = new_cmb2_box([
            'id' => 'rrze-servicekatalog',
            'title' => esc_html__('RRZE Servicekatalog', 'rrze-servicekatalog'),
            'object_types' => ['options-page'],
            'option_key' => 'rrze-servicekatalog-settings', // The option key and admin menu page slug.
            // 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
            'menu_title'      => esc_html__( 'RRZE Servicekatalog', 'rrze-servicekatalog' ), // Falls back to 'title' (above).
            'parent_slug'     => 'options-general.php',
            // 'capability'      => 'manage_options', // Cap required to view options-page.
            // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
            // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
            // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
            // 'save_button'     => esc_html__( 'Save Theme Options', 'rrze-servicekatalog' ), // The text for the options-page save button. Defaults to 'Save'.
            // 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
            // 'message_cb'      => 'yourprefix_options_page_message_callback',
        ]);

        $main_options->add_field( array(
            'name' => esc_html__('Layout Archive Pages', 'rrze-servicekatalog'),
            'desc' => esc_html__('', 'rrze-servicekatalog'),
            'type' => 'title',
            'id'   => 'title'
        ) );

        $settings = (new Shortcodes\Servicekatalog)->fillGutenbergOptions();
        foreach ($settings as $key => $setting) {
            if (!in_array($key, ['display', 'searchform', 'hide'])) continue;
            $options = [];
            $attributes = [];
            switch ($setting['field_type']) {
                case 'radio':
                    $options = $setting['values'] ?? [];
                    break;
                case 'select':
                    if (isset($setting['values'])) {
                        foreach ($setting['values'] as $pair) {
                            $options[$pair['id']] = $pair['val'];
                        }
                    }
                    break;
                case 'multi_select':
                    //$setting['field_type'] = 'select';
                    //$attributes = ['multiple' => 'multiple'];
                    $setting['field_type'] = 'multicheck_inline';
                    if (isset($setting['values'])) {
                        foreach ($setting['values'] as $pair) {
                            $options[$pair['id']] = $pair['val'];
                        }
                    }
                    break;
            }
            $main_options->add_field([
                'name' => $setting['label'] ?? '',
                'desc' => $setting['description'] ?? '',
                'id' => $key,
                'type' => $setting['field_type'],
                'default' => $setting['default'] ?? '',
                'options' => $options,
                'attributes' => $attributes,
            ]);
        }
    }

    /*
     * CMB2 Toggle
     * Source: https://github.com/themevan/cmb2-toggle/
     */
    public function render_field( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
        $field_name = $field->_name();

        $return_value = 'on';

        if ( $field->args( 'return_value' ) && ! empty( $field->args( 'return_value' ) ) ) {
            $return_value = $field->args( 'return_value' );
        }

        $args = array(
            'type'  => 'checkbox',
            'id'    => $field_name,
            'name'  => $field_name,
            'desc'  => '',
            'value' => $return_value,
        );

        echo '<label class="cmb2-toggle">';
        echo '<input type="checkbox" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $return_value ) . '" ' . checked( $escaped_value, $return_value, false ) . ' />';
        echo '<span class="cmb2-toggle-slider round"></span>';
        echo '</label>';

        $field_type_object->_desc( true, true );
    }

    public function add_style() {
        global $_wp_admin_css_colors;

        $color_scheme = get_user_option( 'admin_color' );

        $scheme_colors = array();

        if ( isset( $_wp_admin_css_colors[ $color_scheme ] ) && ! empty( $_wp_admin_css_colors[ $color_scheme ] ) ) {
            $scheme_colors = $_wp_admin_css_colors[ $color_scheme ]->colors;
        }

        $toggle_color = ! empty( $scheme_colors ) ? end( $scheme_colors ) : '#72aee6';
        ?>
        <style>
            .cmb2-toggle {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
            }

            .cmb2-toggle input {
                display: none;
            }

            .cmb2-toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .cmb2-toggle-slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            #side-sortables .cmb-row .cmb2-toggle + .cmb2-metabox-description {
                padding-bottom: 0;
            }

            input:checked + .cmb2-toggle-slider {
                background-color: <?php echo $toggle_color ?>;
            }

            input:focus + .cmb2-toggle-slider {
                box-shadow: 0 0 1px <?php echo $toggle_color ?>;
            }

            input:checked + .cmb2-toggle-slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            .cmb2-toggle-slider.round {
                border-radius: 34px;
            }

            .cmb2-toggle-slider.round:before {
                border-radius: 50%;
            }
        </style>
        <?php
    }

}