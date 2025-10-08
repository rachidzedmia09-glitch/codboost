<?php
/**
 * One Click Demo Import integration for Med Express Delivery.
 *
 * @package MedExpress
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'medexpress_demo_import_files' ) ) {
    /**
     * Register demo import files for One Click Demo Import.
     *
     * @return array
     */
    function medexpress_demo_import_files() {
        $demo_dir = trailingslashit( get_template_directory() ) . 'demo/';

        return array(
            array(
                'import_file_name'           => __( 'Med Express Elementor Demo', 'med-express' ),
                'categories'                 => array( __( 'Logistics', 'med-express' ), __( 'Corporate', 'med-express' ) ),
                'local_import_file'          => $demo_dir . 'content.xml',
                'local_import_widget_file'   => $demo_dir . 'widgets.json',
                'local_import_customizer_file' => $demo_dir . 'customizer.dat',
                'import_preview_image_url'   => get_template_directory_uri() . '/demo/preview.svg',
                'import_notice'              => __( 'Importing the Med Express starter content will create Elementor pages, assign menus, and configure widgets that match the theme demo.', 'med-express' ),
            ),
        );
    }
}
add_filter( 'pt-ocdi/import_files', 'medexpress_demo_import_files' );

if ( ! function_exists( 'medexpress_after_demo_import' ) ) {
    /**
     * Configure site after demo import completes.
     */
    function medexpress_after_demo_import() {
        $front_page = get_page_by_title( 'Home' );
        $blog_page  = get_page_by_title( 'Blog' );

        if ( $front_page && $blog_page ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $front_page->ID );
            update_option( 'page_for_posts', $blog_page->ID );
        }

        $primary_menu_id = medexpress_ensure_menu_exists( 'Primary Menu' );
        if ( $primary_menu_id ) {
            medexpress_seed_menu_items( $primary_menu_id, array( 'Home', 'Services', 'Pricing', 'Contact', 'Blog' ) );
            medexpress_assign_menu_location( 'primary', $primary_menu_id );
        }

        $footer_menu_id = medexpress_ensure_menu_exists( 'Footer Menu' );
        if ( $footer_menu_id ) {
            medexpress_seed_menu_items( $footer_menu_id, array( 'Services', 'Pricing', 'Contact' ) );
            medexpress_assign_menu_location( 'footer', $footer_menu_id );
        }
    }
}
add_action( 'pt-ocdi/after_import', 'medexpress_after_demo_import' );

if ( ! function_exists( 'medexpress_ensure_menu_exists' ) ) {
    /**
     * Create a nav menu if it doesn't exist yet.
     *
     * @param string $menu_name Menu name.
     *
     * @return int|null Menu term ID.
     */
    function medexpress_ensure_menu_exists( $menu_name ) {
        $menu = wp_get_nav_menu_object( $menu_name );

        if ( $menu ) {
            return (int) $menu->term_id;
        }

        $menu_id = wp_create_nav_menu( $menu_name );

        if ( is_wp_error( $menu_id ) ) {
            return null;
        }

        return (int) $menu_id;
    }
}

if ( ! function_exists( 'medexpress_seed_menu_items' ) ) {
    /**
     * Populate menu with links to provided page titles if they exist.
     *
     * @param int   $menu_id    Menu term ID.
     * @param array $page_titles Page titles to include.
     */
    function medexpress_seed_menu_items( $menu_id, array $page_titles ) {
        $existing_items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
        $existing_links = array();

        if ( $existing_items ) {
            foreach ( $existing_items as $item ) {
                $existing_links[] = (int) $item->object_id;
            }
        }

        foreach ( $page_titles as $title ) {
            $page = get_page_by_title( $title );

            if ( ! $page ) {
                continue;
            }

            if ( in_array( (int) $page->ID, $existing_links, true ) ) {
                continue;
            }

            wp_update_nav_menu_item(
                $menu_id,
                0,
                array(
                    'menu-item-title'  => $title,
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $page->ID,
                    'menu-item-type'   => 'post_type',
                    'menu-item-status' => 'publish',
                )
            );
        }
    }
}

if ( ! function_exists( 'medexpress_assign_menu_location' ) ) {
    /**
     * Assign a menu to a registered theme location.
     *
     * @param string $location Location slug.
     * @param int    $menu_id  Menu term ID.
     */
    function medexpress_assign_menu_location( $location, $menu_id ) {
        $locations = (array) get_theme_mod( 'nav_menu_locations', array() );
        $locations[ $location ] = (int) $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }
}

if ( ! function_exists( 'medexpress_import_elementor_templates' ) ) {
    /**
     * Import Elementor template kit JSON files after demo import.
     */
    function medexpress_import_elementor_templates() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        $demo_dir = trailingslashit( get_template_directory() ) . 'demo/elementor/';
        $files    = glob( $demo_dir . '*.json' );

        if ( empty( $files ) ) {
            return;
        }

        foreach ( $files as $file ) {
            $data = file_get_contents( $file );
            if ( ! $data ) {
                continue;
            }

            $decoded = json_decode( $data, true );
            if ( empty( $decoded['content'] ) ) {
                continue;
            }

            $title = isset( $decoded['title'] ) ? $decoded['title'] : basename( $file, '.json' );
            $type  = isset( $decoded['type'] ) ? $decoded['type'] : 'page';

            $post_id = wp_insert_post(
                array(
                    'post_title'   => wp_strip_all_tags( $title ),
                    'post_status'  => 'publish',
                    'post_type'    => 'elementor_library',
                    'post_content' => '',
                )
            );

            if ( is_wp_error( $post_id ) ) {
                continue;
            }

            update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
            update_post_meta( $post_id, '_elementor_template_type', $type );
            update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $decoded['content'] ) ) );
            update_post_meta( $post_id, '_elementor_page_settings', isset( $decoded['page_settings'] ) ? $decoded['page_settings'] : array() );

            if ( ! empty( $decoded['location'] ) ) {
                update_post_meta( $post_id, '_medexpress_elementor_location', sanitize_key( $decoded['location'] ) );
            }

            if ( ! empty( $decoded['assign_to'] ) && is_array( $decoded['assign_to'] ) ) {
                $assign_to = wp_parse_args(
                    $decoded['assign_to'],
                    array(
                        'post_title' => '',
                        'post_type'  => 'page',
                    )
                );

                $page = get_page_by_title( $assign_to['post_title'], OBJECT, $assign_to['post_type'] );
                if ( $page ) {
                    update_post_meta( $page->ID, '_elementor_edit_mode', 'builder' );
                    update_post_meta( $page->ID, '_elementor_template_type', $type );
                    update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $decoded['content'] ) ) );
                    update_post_meta( $page->ID, '_elementor_page_settings', isset( $decoded['page_settings'] ) ? $decoded['page_settings'] : array() );
                }
            }
        }
    }
}
add_action( 'pt-ocdi/after_import', 'medexpress_import_elementor_templates', 15 );

if ( ! function_exists( 'medexpress_import_assign_elementor' ) ) {
    /**
     * Assign Elementor templates to theme locations when available.
     */
    function medexpress_import_assign_elementor() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        $templates = get_posts(
            array(
                'post_type'      => 'elementor_library',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            )
        );

        if ( empty( $templates ) ) {
            return;
        }

        if ( ! isset( \Elementor\Plugin::$instance->theme_builder ) ) {
            return;
        }

        $locations_manager = \Elementor\Plugin::$instance->theme_builder->get_locations_manager();
        if ( ! $locations_manager ) {
            return;
        }

        foreach ( $templates as $template ) {
            $location_meta = get_post_meta( $template->ID, '_medexpress_elementor_location', true );
            if ( ! $location_meta ) {
                continue;
            }

            $locations_manager->save_location( $location_meta, $template->ID, array() );
        }
    }
}
add_action( 'pt-ocdi/after_import', 'medexpress_import_assign_elementor', 20 );
