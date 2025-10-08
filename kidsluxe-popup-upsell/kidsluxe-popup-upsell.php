<?php
/**
 * Plugin Name: Kids-Luxe Pop-Up Upsell by Codboost
 * Plugin URI: https://codboost.pro/
 * Description: Affiche un pop-up d'upsell premium lors de l'ajout au panier ou de l'achat immédiat dans WooCommerce.
 * Version: 1.0.1
 * Author: Codboost
 * Author URI: https://codboost.pro/
 * Text Domain: kidsluxe-popup-upsell
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * WC requires at least: 8.0
 */

defined( 'ABSPATH' ) || exit;

define( 'KLPU_PLUGIN_FILE', __FILE__ );
define( 'KLPU_PLUGIN_DIR', plugin_dir_path( KLPU_PLUGIN_FILE ) );
define( 'KLPU_PLUGIN_URL', plugin_dir_url( KLPU_PLUGIN_FILE ) );
define( 'KLPU_VERSION', '1.0.1' );

require_once KLPU_PLUGIN_DIR . 'includes/helpers.php';
require_once KLPU_PLUGIN_DIR . 'includes/class-KLPU-Admin.php';
require_once KLPU_PLUGIN_DIR . 'includes/class-KLPU-Frontend.php';
require_once KLPU_PLUGIN_DIR . 'includes/class-KLPU-Ajax.php';
require_once KLPU_PLUGIN_DIR . 'includes/class-KLPU-Price.php';

if ( ! function_exists( 'KLPU_init_plugin' ) ) {
    function KLPU_init_plugin(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        load_plugin_textdomain( 'kidsluxe-popup-upsell', false, dirname( plugin_basename( KLPU_PLUGIN_FILE ) ) . '/languages' );

        KLPU_Admin::get_instance();
        KLPU_Frontend::get_instance();
        KLPU_Ajax::get_instance();
        KLPU_Price::get_instance();
    }
}
add_action( 'plugins_loaded', 'KLPU_init_plugin', 11 );

if ( ! function_exists( 'KLPU_activate_plugin' ) ) {
    function KLPU_activate_plugin(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            deactivate_plugins( plugin_basename( KLPU_PLUGIN_FILE ) );
            wp_die( esc_html__( 'Kids-Luxe Pop-Up Upsell nécessite WooCommerce pour fonctionner.', 'kidsluxe-popup-upsell' ) );
        }
    }
}
register_activation_hook( KLPU_PLUGIN_FILE, 'KLPU_activate_plugin' );
