<?php

/*
Plugin Name:     RRZE Servicekatalog
Plugin URI:      https://github.com/RRZE-Webteam/rrze-servicekatalog
Description:     Presentation of RRZE Services
Version:         1.3.1
Author:          RRZE Webteam
Author URI:      https://blogs.fau.de/webworking/
License:         GNU General Public License v3.0
License URI:     https://www.gnu.org/licenses/gpl-3.0.en.html
Domain Path:     /languages
Text Domain:     rrze-servicekatalog
*/

namespace RRZE\Servicekatalog;

defined('ABSPATH') || exit;

use RRZE\Servicekatalog\CPT\Service;
use RRZE\WP\Plugin;

const RRZE_PHP_VERSION = '8.0';
const RRZE_WP_VERSION = '6.2';

// Autoloader
require_once 'vendor/autoload.php';

require_once 'config/config.php';

register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');

add_action('plugins_loaded', __NAMESPACE__ . '\loaded');
add_action('init', __NAMESPACE__ . '\init');

/**
 * loadTextdomain
 */
function loadTextdomain()
{
    load_plugin_textdomain(
        'rrze-servicekatalog',
        false,
        sprintf('%s/languages/', dirname(plugin_basename(__FILE__)))
    );
}

/**
 * System requirements verification.
 * @return string Return an error message.
 */
function systemRequirements(): string
{
    global $wp_version;
    // Strip off any -alpha, -RC, -beta, -src suffixes.
    [$wpVersion] = explode('-', $wp_version);
    $phpVersion = phpversion();
    $error = '';
    if (!is_php_version_compatible(RRZE_PHP_VERSION)) {
        $error = sprintf(
        /* translators: 1: Server PHP version number, 2: Required PHP version number. */
            __('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'rrze-servicekatalog'),
            $phpVersion,
            RRZE_PHP_VERSION
        );
    } elseif (!is_wp_version_compatible(RRZE_WP_VERSION)) {
        $error = sprintf(
        /* translators: 1: Server WordPress version number, 2: Required WordPress version number. */
            __('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'rrze-servicekatalog'),
            $wpVersion,
            RRZE_WP_VERSION
        );
    }
    return $error;
}

/**
 * Activation callback function.
 */
function activation()
{
    loadTextdomain();
    if ($error = systemRequirements()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            sprintf(
            /* translators: 1: The plugin name, 2: The error string. */
                __('Plugins: %1$s: %2$s', 'rrze-servicekatalog'),
                plugin_basename(__FILE__),
                $error
            )
        );
    }

    add_action(
        'init',
        function () {
            Service::registerPostType();
            flush_rewrite_rules(false);
        }
    );

    flush_rewrite_rules(false);
}

/**
 * Deactivation callback function.
 */
function deactivation()
{
    flush_rewrite_rules(false);
}

/**
 * Instantiate Plugin class.
 * @return object Plugin
 */
function plugin()
{
    static $instance;
    if (null === $instance) {
        $instance = new Plugin(__FILE__);
    }
    return $instance;
}

/**
 * Execute on 'plugins_loaded' API/action.
 * @return void
 */
function loaded()
{
    plugin()->loaded();
    if ($error = systemRequirements()) {
        add_action('admin_init', function () use ($error) {
            if (current_user_can('activate_plugins')) {
                $pluginData = get_plugin_data(plugin()->getFile());
                $pluginName = $pluginData['Name'];
                $tag = is_plugin_active_for_network(plugin()->getBaseName()) ? 'network_admin_notices' : 'admin_notices';
                add_action($tag, function () use ($pluginName, $error) {
                    printf(
                        '<div class="notice notice-error"><p>' .
                        /* translators: 1: The plugin name, 2: The error string. */
                        __('Plugins: %1$s: %2$s', 'rrze-servicekatalog') .
                        '</p></div>',
                        esc_html($pluginName),
                        esc_html($error)
                    );
                });
            }
        });
        return;
    }
    new Main;

    add_action('init', __NAMESPACE__ . '\createBlock');
}

function init()
{
    loadTextdomain();
}

function createBlock(): void {
    register_block_type( __DIR__ . '/build/block' );
    $script_handle = generate_block_asset_handle( 'rrze/servicekatalog', 'editorScript' );
    wp_set_script_translations( $script_handle, 'rrze-servicekatalog', plugin_dir_path( __FILE__ ) . 'languages' );
}

/**
 * Adds custom block category if not already present.
 *
 * @param array   $categories Existing block categories.
 * @param WP_Post $post       Current post object.
 * @return array Modified block categories.
 */
function rrze_block_category($categories, $post) {
    // Check if there is already a RRZE category present
    foreach ($categories as $category) {
        if (isset($category['slug']) && $category['slug'] === 'rrze') {
            return $categories;
        }
    }

    $custom_category = [
        'slug'  => 'rrze',
        'title' => __('RRZE', 'rrze-servicekatalog'),
    ];

    // Add RRZE to the end of the categories array
    $categories[] = $custom_category;

    return $categories;
}

// Register the Custom RRZE Category, if it is not set by another plugin
add_filter('block_categories_all', __NAMESPACE__ . '\rrze_block_category', 10, 2);
