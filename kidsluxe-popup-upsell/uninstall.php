<?php
/**
 * Uninstall cleanup.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'klpu_settings' );
