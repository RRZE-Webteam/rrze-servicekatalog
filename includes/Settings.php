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
        if (!has_action('cmb2_render_toggle')) {
            add_action( 'cmb2_render_toggle', [$this, 'render_field' ], 10, 5 );
            add_action( 'admin_head', [$this, 'add_style' ] );
        }
        add_action( 'update_option', [$this, 'updatedOption'], 10, 3 );
    }

    public function registerSettings() {

        if (delete_transient('rrze_servicekatalog_flush_rules')) flush_rewrite_rules();

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
            'name' => esc_html__('General', 'rrze-servicekatalog'),
            //'desc' => esc_html__('', 'rrze-servicekatalog'),
            'type' => 'title',
            'id'   => 'title_general'
        ) );

        $main_options->add_field([
            'name' => __('Slug', 'rrze-servicekatalog'),
            'desc' => __('Allowed Characters: [a-z], [0-9], [-], [_]', 'rrze-servicekatalog'),
            'id' => 'slug',
            'type' => 'text',
            'default' => 'rrze-service',
            'sanitization_cb' => 'sanitize_title',
        ]);

        $main_options->add_field([
            'name' => __('Allow Shortcodes', 'rrze-servicekatalog'),
            'desc' => __('Allow shortcodes in description field', 'rrze-servicekatalog'),
            'id' => 'allow_shortcodes',
            'type' => 'toggle',
        ]);

        $main_options->add_field( array(
            'name' => esc_html__('Layout Archive Pages', 'rrze-servicekatalog'),
            //'desc' => esc_html__('', 'rrze-servicekatalog'),
            'type' => 'title',
            'id'   => 'title_layout'
        ) );

        $settings = (new Shortcodes\Servicekatalog)->fillGutenbergOptions();
        foreach ($settings as $key => $setting) {
            if (!in_array($key, ['display', 'searchform', 'pdf_link', 'orderby', 'hide', 'teaser_length'])) continue;
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

        $main_options->add_field( array(
            'name' => esc_html__('Marketing', 'rrze-servicekatalog'),
            //'desc' => esc_html__('', 'rrze-servicekatalog'),
            'type' => 'title',
            'id'   => 'title_marketing'
        ) );

        $main_options->add_field([
            'name' => __('PDF: Link Parameters for QR Code', 'rrze-servicekatalog'),
            'desc' => __('Add tracking parameters that are added to QR code links', 'rrze-servicekatalog'),
            'id' => 'qr_link_parameters',
            'type' => 'text',
            'attributes' => [
                'class' => 'large-text'
            ],
        ]);

        /*$main_options->add_field([
             'name' => __('Link Parameters for Text Links', 'rrze-servicekatalog'),
             'desc' => __('', 'rrze-servicekatalog'),
             'id' => 'text_link_parameters',
             'type' => 'text',
             'attributes' => [
                 'class' => 'large-text'
             ],
         ]);*/
    }

    /*
     * CMB2 Toggle
     * Source: https://kittygiraudel.com/2021/04/05/an-accessible-toggle/
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

        echo '<label class="cmb2-toggle" for="' . esc_attr( $args['id'] ) . '">
  <input type="checkbox" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $return_value ) . '" class="Toggle__input" ' . checked( $escaped_value, $return_value, false ) . ' />

  <span class="Toggle__display" hidden>
    <svg
      aria-hidden="true"
      focusable="false"
      class="Toggle__icon Toggle__icon--checkmark"
      width="18"
      height="14"
      viewBox="0 0 18 14"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M6.08471 10.6237L2.29164 6.83059L1 8.11313L6.08471 13.1978L17 2.28255L15.7175 1L6.08471 10.6237Z"
        fill="currentcolor"
        stroke="currentcolor"
      />
    </svg>
    <svg
      aria-hidden="true"
      focusable="false"
      class="Toggle__icon Toggle__icon--cross"
      width="13"
      height="13"
      viewBox="0 0 13 13"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M11.167 0L6.5 4.667L1.833 0L0 1.833L4.667 6.5L0 11.167L1.833 13L6.5 8.333L11.167 13L13 11.167L8.333 6.5L13 1.833L11.167 0Z"
        fill="currentcolor"
      />
    </svg>
  </span>

  <span class="screen-reader-text"> ' . esc_html($field->args['name']) . '</span>
</label>';

        $field_type_object->_desc( true, true );
    }

    public function add_style() {
        ?>
        <style>
            .cmb2-toggle {
                display: inline-flex; /* 1 */
                align-items: center; /* 1 */
                flex-wrap: wrap; /* 2 */
                position: relative; /* 3 */
                gap: 1ch; /* 4 */
            }
            .Toggle__display {
                --offset: 0.25em;
                --diameter: 1.8em;

                display: inline-flex; /* 1 */
                align-items: center; /* 1 */
                justify-content: space-around; /* 1 */

                width: calc(var(--diameter) * 2 + var(--offset) * 2); /* 2 */
                height: calc(var(--diameter) + var(--offset) * 2); /* 2 */
                box-sizing: content-box; /* 2 */

                border: 0.1em solid rgb(0 0 0 / 0.2); /* 3 */

                position: relative; /* 4 */
                border-radius: 100vw; /* 5 */
                background-color: #fbe4e2; /* 6 */

                transition: 250ms;
                cursor: pointer;
            }
            .Toggle__display::before {
                content: '';

                width: var(--diameter); /* 1 */
                height: var(--diameter); /* 1 */
                border-radius: 50%; /* 1 */

                box-sizing: border-box; /* 2 */
                border: 0.1px solid rgb(0 0 0 / 0.2); /* 2 */

                position: absolute; /* 3 */
                z-index: 2; /* 3 */
                top: 50%; /* 3 */
                left: var(--offset); /* 3 */
                transform: translate(0, -50%); /* 3 */

                background-color: #fff; /* 4 */
                transition: inherit;
            }
            @media (prefers-reduced-motion: reduce) {
                .Toggle__display {
                    transition-duration: 0ms;
                }
            }
            .Toggle__input {
                position: absolute;
                opacity: 0;
                width: 100%;
                height: 100%;
            }
            .Toggle__input:focus + .Toggle__display {
                outline: 1px dotted #212121; /* 1 */
                outline: 1px auto -webkit-focus-ring-color; /* 1 */
            }
            .Toggle__input:focus:not(:focus-visible) + .Toggle__display {
                outline: 0; /* 1 */
            }
            .Toggle__input:checked + .Toggle__display {
                background-color: #e3f5eb; /* 1 */
            }
            .Toggle__input:checked + .Toggle__display::before {
                transform: translate(100%, -50%); /* 1 */
            }
            .Toggle__input:disabled + .Toggle__display {
                opacity: 0.6; /* 1 */
                filter: grayscale(40%); /* 1 */
                cursor: not-allowed; /* 1 */
            }
            [dir='rtl'] .Toggle__display::before {
                left: auto; /* 1 */
                right: var(--offset); /* 1 */
            }
            [dir='rtl'] .Toggle__input:checked + .Toggle__display::before {
                transform: translate(-100%, -50%); /* 1 */
            }
            .Toggle__icon {
                display: inline-block;
                width: 1em;
                height: 1em;
                color: inherit;
                fill: currentcolor;
                vertical-align: middle;
            }
            .Toggle__icon--cross {
                color: #e74c3c;
                font-size: 85%; /* 1 */
            }

            .Toggle__icon--checkmark {
                color: #1fb978;
            }
        </style>
        <?php
    }

    public function updatedOption($option_name, $old_value, $new_value) {
        //var_dump($option_name, $old_value, $new_value); exit;
        if ( 'rrze-servicekatalog-settings' !== $option_name )
            return;
        if (!isset($new_value['slug']) || $new_value['slug'] == '') {
            $new_value['slug'] = 'rrze-service';
        }
        if (!isset($old_value['slug']) || $new_value['slug'] != $old_value['slug']) {
            set_transient('rrze_servicekatalog_flush_rules', true);
        }
    }

}