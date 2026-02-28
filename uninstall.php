<?php
/**
 * Uninstall Weatherly Widgets.
 *
 * Fired when the plugin is deleted (not just deactivated).
 * Removes all plugin options and transients from the database.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove options
$options = array(
    'weatherly_license_key',
    'weatherly_license_status',
    'weatherly_license_expires',
    'weatherly_default_city',
    'weatherly_default_state',
    'weatherly_cache_ttl',
    'weatherly_units',
    'weatherly_tier',
    'weatherly_used_cities',
);

foreach ( $options as $option ) {
    delete_option( $option );
}

// Remove all transients
global $wpdb;
$wpdb->query(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_weatherly_%' OR option_name LIKE '_transient_timeout_weatherly_%'"
);
