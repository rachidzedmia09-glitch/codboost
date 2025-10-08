<?php
/**
 * Custom search form markup.
 *
 * @package MedExpress
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'med-express' ); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search…', 'placeholder', 'med-express' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <button type="submit" class="button button--primary">
        <span class="dashicons dashicons-search"></span>
        <span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'med-express' ); ?></span>
    </button>
</form>
