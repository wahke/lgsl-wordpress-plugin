<?php
/**
 * Plugin Name: LGSL WordPress Plugin
 * Plugin URI: https://github.com/wahke/lgsl-wordpress-plugin
 * Description: A WordPress plugin to integrate LGSL (Live Game Server List) using an existing LGSL installation's MySQL database.
 * Version: 1.2
 * Author: wahke
 * Author URI: https://wahke.me
 * License: GPL2
 * Text Domain: lgsl-wordpress-plugin
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

include_once plugin_dir_path(__FILE__) . 'includes/update-checker.php';

// Load plugin textdomain
function lgsl_load_textdomain() {
    load_plugin_textdomain('lgsl-wordpress-plugin', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'lgsl_load_textdomain');

// Rest of your plugin code...

// Activation hook to set default options and redirect to setup
function lgsl_activate_plugin() {
    // Set default options on activation
    add_option('lgsl_db_host', 'localhost');
    add_option('lgsl_db_name', '');
    add_option('lgsl_db_user', '');
    add_option('lgsl_db_pass', '');
    add_option('lgsl_db_table', 'lgsl');

    // Set display options
    add_option('lgsl_display_servername', '1');
    add_option('lgsl_display_ip', '1');
    add_option('lgsl_display_port', '1');
    add_option('lgsl_display_game', '1');
    add_option('lgsl_display_map', '1');
    add_option('lgsl_display_players', '1');
    add_option('lgsl_display_status', '1');

    // Set URL option
    add_option('lgsl_base_url', '');

    // Redirect to setup page
    add_option('lgsl_plugin_activated', true);
}
register_activation_hook(__FILE__, 'lgsl_activate_plugin');

// Redirect to setup page after activation
function lgsl_redirect_to_setup() {
    if (get_option('lgsl_plugin_activated', false)) {
        delete_option('lgsl_plugin_activated');
        wp_redirect(admin_url('admin.php?page=lgsl-settings'));
        exit;
    }
}
add_action('admin_init', 'lgsl_redirect_to_setup');

// Create an admin menu
add_action('admin_menu', 'lgsl_create_menu');

function lgsl_create_menu() {
    add_menu_page(
        __('LGSL Settings', 'lgsl-wordpress-plugin'),
        __('LGSL Settings', 'lgsl-wordpress-plugin'),
        'manage_options',
        'lgsl-settings',
        'lgsl_settings_page',
        'dashicons-desktop'
    );
    add_action('admin_init', 'lgsl_register_settings');
}

function lgsl_register_settings() {
    register_setting('lgsl-settings-group', 'lgsl_db_host');
    register_setting('lgsl-settings-group', 'lgsl_db_name');
    register_setting('lgsl-settings-group', 'lgsl_db_user');
    register_setting('lgsl-settings-group', 'lgsl_db_pass');
    register_setting('lgsl-settings-group', 'lgsl_db_table');

    // Display options
    register_setting('lgsl-settings-group', 'lgsl_display_servername');
    register_setting('lgsl-settings-group', 'lgsl_display_ip');
    register_setting('lgsl-settings-group', 'lgsl_display_port');
    register_setting('lgsl-settings-group', 'lgsl_display_game');
    register_setting('lgsl-settings-group', 'lgsl_display_map');
    register_setting('lgsl-settings-group', 'lgsl_display_players');
    register_setting('lgsl-settings-group', 'lgsl_display_status');

    // URL option
    register_setting('lgsl-settings-group', 'lgsl_base_url');
}

function lgsl_settings_page() {
    // Get available IDs
    global $lgsl_db;
    $db_table = get_option('lgsl_db_table', 'lgsl');
    $available_ids = '';

    if ($lgsl_db) {
        $results = $lgsl_db->query("SELECT id FROM $db_table");
        if ($results) {
            while ($row = $results->fetch_assoc()) {
                $available_ids .= $row['id'] . ', ';
            }
            $available_ids = rtrim($available_ids, ', ');
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e('LGSL Settings', 'lgsl-wordpress-plugin'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('lgsl-settings-group'); ?>
            <?php do_settings_sections('lgsl-settings-group'); ?>
            <div class="lgsl-settings-container">
                <div class="lgsl-settings-column">
                    <h2><?php _e('Database Settings', 'lgsl-wordpress-plugin'); ?></h2>
                    <p><?php _e('Please enter the database details of your existing LGSL installation. This information will be used to display the server data in WordPress.', 'lgsl-wordpress-plugin'); ?></p>
                    <table class="form-table lgsl-centered-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Database Host', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="text" name="lgsl_db_host" value="<?php echo esc_attr(get_option('lgsl_db_host', 'localhost')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Database Name', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="text" name="lgsl_db_name" value="<?php echo esc_attr(get_option('lgsl_db_name')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Database User', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="text" name="lgsl_db_user" value="<?php echo esc_attr(get_option('lgsl_db_user')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Database Password', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="password" name="lgsl_db_pass" value="<?php echo esc_attr(get_option('lgsl_db_pass')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Database Table', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="text" name="lgsl_db_table" value="<?php echo esc_attr(get_option('lgsl_db_table', 'lgsl')); ?>" /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Base URL', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="text" name="lgsl_base_url" value="<?php echo esc_attr(get_option('lgsl_base_url')); ?>" /></td>
                        </tr>
                    </table>
                </div>
                <div class="lgsl-settings-column">
                    <h2><?php _e('Display Settings', 'lgsl-wordpress-plugin'); ?></h2>
                    <p><?php _e('These settings determine which information is displayed in the server list. This affects the shortcode', 'lgsl-wordpress-plugin'); ?> <code>[lgsl_server_list]</code>.</p>
                    <table class="form-table lgsl-centered-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Show Server Name', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_servername" value="1" <?php checked(1, get_option('lgsl_display_servername', 1)); ?> /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Show IP', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_ip" value="1" <?php checked(1, get_option('lgsl_display_ip', 1)); ?> /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Show Port', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_port" value="1" <?php checked(1, get_option('lgsl_display_port', 1)); ?> /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Show Game', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_game" value="1" <?php checked(1, get_option('lgsl_display_game', 1)); ?> /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Show Map', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_map" value="1" <?php checked(1, get_option('lgsl_display_map', 1)); ?> /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Show Players', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_players" value="1" <?php checked(1, get_option('lgsl_display_players', 1)); ?> /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Show Status', 'lgsl-wordpress-plugin'); ?></th>
                            <td><input type="checkbox" name="lgsl_display_status" value="1" <?php checked(1, get_option('lgsl_display_status', 1)); ?> /></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="lgsl-available-ids">
                <h2><?php _e('Available Server IDs', 'lgsl-wordpress-plugin'); ?></h2>
                <p><?php echo $available_ids ? $available_ids : __('No server IDs found.', 'lgsl-wordpress-plugin'); ?></p>
            </div>
            <div class="lgsl-centered-button">
                <?php submit_button(__('Save Settings and Finish Setup', 'lgsl-wordpress-plugin')); ?>
            </div>
        </form>
        <h2><?php _e('Available Shortcodes', 'lgsl-wordpress-plugin'); ?></h2>
        <ul>
            <li><strong>[lgsl_server_list]</strong>: <?php _e('Displays the server list based on the display settings in the admin panel.', 'lgsl-wordpress-plugin'); ?></li>
            <li><strong>[lgsl_server_list id="1,2,3"]</strong>: <?php _e('Displays the server list for the specified server IDs and ignores the display settings in the admin panel.', 'lgsl-wordpress-plugin'); ?></li>
            <li><strong>[lgsl_server_list id="1,2,3" show_servername="false" show_ip="false" show_port="true" show_game="true" show_map="true" show_players="true" show_status="false"]</strong>: <?php _e('Displays the server list for the specified server IDs and uses the provided display options to show or hide specific information.', 'lgsl-wordpress-plugin'); ?></li>
        </ul>
        <p>&copy; <?php echo date("Y"); ?> wahke. <?php _e('All rights reserved.', 'lgsl-wordpress-plugin'); ?></p>
    </div>
    <?php
}

// Add custom CSS to style the settings page
function lgsl_admin_css() {
    echo '<style>
        .lgsl-settings-container {
            display: flex;
            justify-content: space-between;
        }
        .lgsl-settings-column {
            width: 45%;
        }
        .lgsl-centered-table {
            width: 100%;
        }
        .lgsl-centered-table th {
            text-align: left;
            padding-right: 10px;
        }
        .lgsl-centered-table td {
            text-align: left;
        }
        .lgsl-centered-button {
            text-align: center;
        }
        .lgsl-centered-button .button-primary {
            float: none;
            margin: 20px auto;
            display: block;
        }
        .lgsl-available-ids {
            margin-top: 20px;
            margin-bottom: 20px;
            width: 100%;
            text-align: center;
        }
    </style>';
}
add_action('admin_head', 'lgsl_admin_css');

// Modify LGSL configuration to use settings from the admin panel
function lgsl_custom_db_settings() {
    $db_host = get_option('lgsl_db_host', 'localhost');
    $db_name = get_option('lgsl_db_name');
    $db_user = get_option('lgsl_db_user');
    $db_pass = get_option('lgsl_db_pass');
    $db_table = get_option('lgsl_db_table', 'lgsl');

    if ($db_name && $db_user) {
        define('LGSL_DB_HOST', $db_host);
        define('LGSL_DB_NAME', $db_name);
        define('LGSL_DB_USER', $db_user);
        define('LGSL_DB_PASS', $db_pass);
        define('LGSL_DB_TABLE', $db_table);

        // Connect to the database
        global $lgsl_db;
        $lgsl_db = new mysqli(LGSL_DB_HOST, LGSL_DB_USER, LGSL_DB_PASS, LGSL_DB_NAME);

        // Check connection
        if ($lgsl_db->connect_error) {
            die("Connection failed: " . $lgsl_db->connect_error);
        }
    }
}
add_action('init', 'lgsl_custom_db_settings');

// Include LGSL files
function lgsl_include_files() {
    if (defined('LGSL_DB_NAME') && defined('LGSL_DB_USER')) {
        if (!class_exists('LGSL')) {
            include_once plugin_dir_path(__FILE__) . 'lgsl/lgsl_class.php';
        }
        if (!class_exists('LGSL_Protocol')) {
            include_once plugin_dir_path(__FILE__) . 'lgsl/lgsl_protocol.php';
        }
        if (!function_exists('lgsl_server_dns')) {
            include_once plugin_dir_path(__FILE__) . 'lgsl/lgsl_functions.php';
        }
    }
}
add_action('plugins_loaded', 'lgsl_include_files');

// Create a shortcode to display the server list
function lgsl_display_server_list($atts) {
    // Check if specific IDs are provided
    $has_specific_ids = !empty($atts['id']);

    // Get default options from database only if no specific IDs are provided
    $default_atts = array(
        'id' => '', // Default is an empty string, meaning all servers
        'show_servername' => $has_specific_ids ? 'true' : get_option('lgsl_display_servername', 'true'),
        'show_ip' => $has_specific_ids ? 'true' : get_option('lgsl_display_ip', 'true'),
        'show_port' => $has_specific_ids ? 'true' : get_option('lgsl_display_port', 'true'),
        'show_game' => $has_specific_ids ? 'true' : get_option('lgsl_display_game', 'true'),
        'show_map' => $has_specific_ids ? 'true' : get_option('lgsl_display_map', 'true'),
        'show_players' => $has_specific_ids ? 'true' : get_option('lgsl_display_players', 'true'),
        'show_status' => $has_specific_ids ? 'true' : get_option('lgsl_display_status', 'true'),
    );

    // Override default options with provided attributes
    $atts = shortcode_atts($default_atts, $atts, 'lgsl_server_list');

    ob_start();
    $file_path = plugin_dir_path(__FILE__) . 'lgsl/lgsl_list.php';
    if (file_exists($file_path)) {
        include $file_path;
    } else {
        echo __('The file lgsl_list.php was not found.', 'lgsl-wordpress-plugin');
    }
    return ob_get_clean();
}
add_shortcode('lgsl_server_list', 'lgsl_display_server_list');

// Enqueue necessary scripts and styles if any (adjust paths as needed)
function lgsl_enqueue_scripts() {
    wp_enqueue_style('lgsl-style', plugins_url('lgsl/lgsl_files/lgsl.css', __FILE__));
    wp_enqueue_script('lgsl-script', plugins_url('lgsl/lgsl_files/lgsl.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'lgsl_enqueue_scripts');
