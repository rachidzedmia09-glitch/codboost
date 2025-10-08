<?php
/**
 * Register required and recommended plugins for Med Express Delivery.
 *
 * @package MedExpress
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/lib/class-tgm-plugin-activation.php';

if ( ! function_exists( 'medexpress_register_required_plugins' ) ) {
    /**
     * Hook into tgmpa_register to declare required plugins.
     */
    function medexpress_register_required_plugins() {
        $plugins = array(
            array(
                'name'     => __( 'Elementor Website Builder', 'med-express' ),
                'slug'     => 'elementor',
                'required' => true,
            ),
            array(
                'name'     => __( 'Elementor Header & Footer Builder', 'med-express' ),
                'slug'     => 'header-footer-elementor',
                'required' => false,
            ),
            array(
                'name'     => __( 'One Click Demo Import', 'med-express' ),
                'slug'     => 'one-click-demo-import',
                'required' => false,
            ),
            array(
                'name'     => __( 'Contact Form 7', 'med-express' ),
                'slug'     => 'contact-form-7',
                'required' => false,
            ),
            array(
                'name'     => __( 'WP Mail SMTP', 'med-express' ),
                'slug'     => 'wp-mail-smtp',
                'required' => false,
            ),
        );

        $config = array(
            'id'           => 'med-express',
            'default_path' => '',
            'menu'         => 'medexpress-install-plugins',
            'parent_slug'  => 'themes.php',
            'capability'   => 'edit_theme_options',
            'has_notices'  => true,
            'dismissable'  => true,
            'is_automatic' => false,
            'strings'      => array(
                'page_title'                      => __( 'Install Required Plugins', 'med-express' ),
                'menu_title'                      => __( 'Install Plugins', 'med-express' ),
                'installing'                      => __( 'Installing Plugin: %s', 'med-express' ),
                'updating'                        => __( 'Updating Plugin: %s', 'med-express' ),
                'oops'                            => __( 'Something went wrong with the plugin API.', 'med-express' ),
                'notice_can_install_required'     => _n_noop(
                    'The Med Express Delivery theme requires the following plugin: %1$s.',
                    'The Med Express Delivery theme requires the following plugins: %1$s.',
                    'med-express'
                ),
                'notice_can_install_recommended'  => _n_noop(
                    'The Med Express Delivery theme recommends the following plugin: %1$s.',
                    'The Med Express Delivery theme recommends the following plugins: %1$s.',
                    'med-express'
                ),
                'notice_ask_to_update'            => _n_noop(
                    'The following plugin needs to be updated to ensure compatibility with Med Express Delivery: %1$s.',
                    'The following plugins need to be updated to ensure compatibility with Med Express Delivery: %1$s.',
                    'med-express'
                ),
                'notice_ask_to_update_maybe'      => _n_noop(
                    'There is an update available for: %1$s.',
                    'There are updates available for the following plugins: %1$s.',
                    'med-express'
                ),
                'notice_can_activate_required'    => _n_noop(
                    'The following required plugin is currently inactive: %1$s.',
                    'The following required plugins are currently inactive: %1$s.',
                    'med-express'
                ),
                'notice_can_activate_recommended' => _n_noop(
                    'The following recommended plugin is currently inactive: %1$s.',
                    'The following recommended plugins are currently inactive: %1$s.',
                    'med-express'
                ),
                'install_link'                    => _n_noop(
                    'Begin installing plugin',
                    'Begin installing plugins',
                    'med-express'
                ),
                'activate_link'                   => _n_noop(
                    'Begin activating plugin',
                    'Begin activating plugins',
                    'med-express'
                ),
                'return'                          => __( 'Return to Theme Plugins Installer', 'med-express' ),
                'plugin_activated'                => __( 'Plugin activated successfully.', 'med-express' ),
                'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'med-express' ),
                'dismiss'                         => __( 'Dismiss this notice', 'med-express' ),
                'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'med-express' ),
            ),
        );

        tgmpa( $plugins, $config );
    }
}
add_action( 'tgmpa_register', 'medexpress_register_required_plugins' );
