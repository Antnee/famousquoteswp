<?php
namespace AFQ;

use function array_push;
use function add_action;
use function add_filter;
use function add_menu_page;
use function add_submenu_page;
use function add_site_option;
use function define;
use function delete_site_option;
use function get_page_by_title;
use function plugin_basename;
use function plugin_dir_path;
use function register_activation_hook;
use function register_uninstall_hook;
use function wp_insert_post;

/**
 * @package AnthonysFamousQuotes
 */

/*
Plugin Name: Anthony's Famous Quotes
Plugin URI: https://github.com/Antnee/famousquotes-wp
Description: A simple plugin as part of a code bootcamp. You must configure the plugin with a compatible API endpoint where your famous quotes will be stored
Version: 1.0.0
Author: Anthony Chambers
Author URI: https://github.com/Antnee
License: MIT
License URI: https://opensource.org/licenses/MIT
*/
define('ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ANTHONYS_FAMOUS_QUOTES__PLUGIN_URI', plugin_dir_url(__FILE__));

require(ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'Views.php');

/**
 * Set up on plugin activation
 */
function activate(): void
{
    add_site_option('afk-api_uri', 'http://localhost');
    add_site_option('afk-api_key', '');

    $pageTitle = 'Anthony\'s Famous Quotes';
    $check = get_page_by_title($pageTitle);
    if (!isset($check->ID)){
        wp_insert_post([
            'post_type'    => 'page',
            'post_title'   => $pageTitle,
            'post_content' => '<p>Please click an author in the footer to see quotes by them</p>',
            'post_status'  => 'publish',
            'post_author'  => 1,
        ]);
    }
}
register_activation_hook(__FILE__, 'AFQ\\activate');

/**
 * Tear down on plugin uninstallation
 */
function uninstall(): void
{
    delete_site_option('famous_quotes_api_key');
    delete_site_option('famous_quotes_api_uri');
}
register_uninstall_hook(__FILE__, 'AFQ\\uninstall');

/**
 * Add settings link on plugin page when activated
 *
 * @param array $links
 * @return array
 */
function pluginSettingsLink(array $links): array
{
    $link = sprintf('<a href="admin.php?page=famous-quotes">%s</a>', __('Settings'));
    array_push($links, $link);
    return $links;
}
add_filter(sprintf("plugin_action_links_%s", plugin_basename(__FILE__)), 'AFQ\\pluginSettingsLink');

/**
 * Custom CSS for the plugin admin pages
 */
function adminCss(): void
{
    echo '<style>.afq dt { font-weight: bold; }</style>';
}
add_action('admin_head', 'AFQ\\adminCss');

/**
 * Set up menu structure
 */
function adminMenus(): void
{
    // Top level menu
    add_menu_page(
        'Anthony\'s Famous Quotes',
        'Famous Quotes',
        'manage_options',
        'famous-quotes',
        ['\\AFQ\\Views', 'generalAdminPage']
    );

    // Submenus
    add_submenu_page(
        'famous-quotes',
        'Anthony\'s Famous Quotes: Manage API Connection',
        'API Settings',
        'manage_options',
        'famous-quotes-api',
        ['\\AFQ\\Views', 'apiSettingsAdminPage']
    );

    add_submenu_page(
        'famous-quotes',
        'Anthony\'s Famous Quotes: Manage Authors',
        'Authors',
        'manage_options',
        'famous-quotes-authors',
        ['\\AFQ\\Views', 'authorsAdminPage']
    );
    add_submenu_page(
        'famous-quotes',
        'Anthony\'s Famous Quotes: Manage Quotes',
        'Quotes',
        'manage_options',
        'famous-quotes-manage',
        ['\\AFQ\\Views', 'quotesAdminPage']
    );
    add_action('admin_init', 'AFQ\\registerSettings');
}
add_action('admin_menu', 'AFQ\\adminMenus');

/**
 * Register Plugin Settings
 */
function registerSettings(): void
{
    register_setting('afq-group', 'afk-api_uri');
    register_setting('afq-group', 'afk-api_key');
}

add_action('wp_footer', ['\\AFQ\\Views', 'footer']);
add_action('the_post', ['\\AFQ\\Views', 'authorPageTitle']);
add_filter('the_content', ['\\AFQ\\Views', 'authorPageContent']);