<?php

namespace RRZE\Servicekatalog;

defined('ABSPATH') || exit;

use RRZE\Servicekatalog\CPT\Service;
use RRZE\Servicekatalog\Shortcodes\Servicekatalog;

class Main
{
    /**
     * __construct
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'wpEnqueueScripts']);

        Service::init();
        Servicekatalog::init();

    }

    public function adminEnqueueScripts()
    {
        $screen = get_current_screen();
        if (is_null($screen)) {
            return;
        }
        if (in_array($screen->post_type, [Service::POST_TYPE])) {
            wp_enqueue_style(
                'rrze-servicekatalog-admin',
                plugins_url('build/admin.css', plugin()->getBasename()),
                [],
                plugin()->getVersion(true)
            );
            wp_enqueue_script(
                'rrze-servicekatalog-admin',
                plugins_url('build/admin.js', plugin()->getBasename()),
                ['jquery', 'wp-color-picker'],
                plugin()->getVersion(true)
            );
        }
    }

    public function wpEnqueueScripts()
    {
        wp_register_style(
            'rrze-servicekatalog-sc-servicekatalog',
            plugins_url('build/servicekatalog.css', plugin()->getBasename()),
            [],
            plugin()->getVersion(true)
        );
        wp_register_script(
            'rrze-servicekatalog-sc-servicekatalog',
            plugins_url('build/servicekatalog.js', plugin()->getBasename()),
            ['jquery'],
            plugin()->getVersion(true)
        );
        wp_localize_script('rrze-servicekatalog-sc-servicekatalog', 'rrze_servicekatalog_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'rrze-servicekatalog-ajax-nonce' ),
        ]);
    }
}
