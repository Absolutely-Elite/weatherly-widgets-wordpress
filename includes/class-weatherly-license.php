<?php
/**
 * License key validation.
 *
 * Validates the Pro license key against the Weatherly API server.
 * Caches the result for 24 hours to avoid hitting the API on every page load.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Weatherly_License {

    private $api;

    public function __construct() {
        $this->api = new Weatherly_API();
    }

    /**
     * Validate the license if the cached check has expired.
     * Called on every admin page load but only hits the API once per day.
     */
    public function maybe_validate() {
        $license_key = get_option( 'weatherly_license_key', '' );

        if ( empty( $license_key ) ) {
            $this->set_tier( 'free' );
            return;
        }

        // Check if we already validated recently
        $last_check = get_transient( 'weatherly_license_check' );
        if ( false !== $last_check ) {
            return; // Still fresh
        }

        $this->validate( $license_key );
    }

    /**
     * Force-validate a license key (called when user enters a new key).
     *
     * @param string $license_key License key to validate.
     * @return array Result with 'valid' and 'message' keys.
     */
    public function validate( $license_key ) {
        $result = $this->api->validate_license( $license_key );

        if ( is_wp_error( $result ) ) {
            // Network error — keep current tier, try again later
            set_transient( 'weatherly_license_check', 'error', HOUR_IN_SECONDS );
            return array(
                'valid'   => null,
                'message' => 'Could not connect to license server. Will retry later.',
            );
        }

        if ( ! empty( $result['valid'] ) && true === $result['valid'] ) {
            $this->set_tier( 'pro' );
            update_option( 'weatherly_license_status', 'active' );
            update_option( 'weatherly_license_expires', $result['expires'] ?? '' );

            // Cache for 24 hours
            set_transient( 'weatherly_license_check', 'valid', DAY_IN_SECONDS );

            return array(
                'valid'   => true,
                'message' => 'License activated. Pro features enabled.',
            );
        }

        // License invalid or expired
        $this->set_tier( 'free' );
        update_option( 'weatherly_license_status', $result['status'] ?? 'invalid' );

        // Cache the failure for 1 hour (so they can retry after fixing)
        set_transient( 'weatherly_license_check', 'invalid', HOUR_IN_SECONDS );

        return array(
            'valid'   => false,
            'message' => $result['message'] ?? 'Invalid license key.',
        );
    }

    /**
     * Deactivate the license (when user removes the key).
     */
    public function deactivate() {
        delete_option( 'weatherly_license_key' );
        delete_option( 'weatherly_license_status' );
        delete_option( 'weatherly_license_expires' );
        delete_transient( 'weatherly_license_check' );
        $this->set_tier( 'free' );
    }

    /**
     * Get the current license status.
     */
    public function get_status() {
        return array(
            'key'     => get_option( 'weatherly_license_key', '' ),
            'status'  => get_option( 'weatherly_license_status', 'none' ),
            'tier'    => get_option( 'weatherly_tier', 'free' ),
            'expires' => get_option( 'weatherly_license_expires', '' ),
        );
    }

    /**
     * Set the active tier.
     */
    private function set_tier( $tier ) {
        update_option( 'weatherly_tier', $tier );
    }
}
