<?php
/**
 * Plugin Name: WP Native Blocks
 * Description: Scaffold native Gutenberg blocks for block themes with per-block builds
 * Version: 3.0.3
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Imagewize
 * Author URI: https://imagewize.com
 * License: MIT
 * Text Domain: wp-native-blocks
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_NATIVE_BLOCKS_VERSION', '3.0.3');
define('WP_NATIVE_BLOCKS_PATH', plugin_dir_path(__FILE__));
define('WP_NATIVE_BLOCKS_URL', plugin_dir_url(__FILE__));

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Register WP-CLI command
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('block create', 'Imagewize\\WpNativeBlocks\\CLI\\CreateCommand');
}
