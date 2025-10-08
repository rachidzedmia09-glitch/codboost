<?php
/**
 * Elementor integration helpers.
 *
 * @package MedExpress
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'medexpress_register_elementor_locations' ) ) {
    /**
     * Allow Elementor Theme Builder to override core locations.
     *
     * @param \Elementor\Theme\Locations_Manager $elementor_theme_manager Theme manager instance.
     */
    function medexpress_register_elementor_locations( $elementor_theme_manager ) {
        if ( method_exists( $elementor_theme_manager, 'register_all_core_location' ) ) {
            $elementor_theme_manager->register_all_core_location();
            return;
        }

        $locations = array( 'header', 'footer', 'single', 'archive', '404' );
        foreach ( $locations as $location ) {
            if ( method_exists( $elementor_theme_manager, 'register_location' ) ) {
                $elementor_theme_manager->register_location( $location );
            }
        }
    }
}
add_action( 'elementor/theme/register_locations', 'medexpress_register_elementor_locations' );

if ( ! function_exists( 'medexpress_elementor_render_location' ) ) {
    /**
     * Attempt to render the first Elementor location that exists.
     *
     * @param array $locations Location slugs to test.
     *
     * @return bool Whether a location was rendered.
     */
    function medexpress_elementor_render_location( array $locations ) {
        if ( ! function_exists( 'elementor_theme_do_location' ) ) {
            return false;
        }

        foreach ( $locations as $location ) {
            if ( elementor_theme_do_location( $location ) ) {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists( 'medexpress_is_elementor_page' ) ) {
    /**
     * Determine whether a given post is built with Elementor.
     *
     * @param int|null $post_id Optional post ID. Defaults to current post in the loop.
     *
     * @return bool
     */
    function medexpress_is_elementor_page( $post_id = null ) {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return false;
        }

        if ( null === $post_id ) {
            $post_id = get_the_ID();
        }

        if ( ! $post_id ) {
            return false;
        }

        $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $post_id );

        if ( ! $document ) {
            return false;
        }

        return $document->is_built_with_elementor();
    }
}
