<?php
/**
 * Cache manager using WordPress transients.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Weatherly_Cache {

    /**
     * Get cached weather data.
     *
     * @param string $city  City name.
     * @param string $state State code.
     * @param string $format Display format.
     * @return array|false Cached data or false if expired/missing.
     */
    public function get( $city, $state, $format = 'compact' ) {
        $key = $this->build_key( $city, $state, $format );
        $data = get_transient( $key );

        if ( false === $data ) {
            return false;
        }

        return $data;
    }

    /**
     * Store weather data in cache.
     *
     * @param string $city   City name.
     * @param string $state  State code.
     * @param string $format Display format.
     * @param array  $data   Weather data to cache.
     */
    public function set( $city, $state, $format, $data ) {
        $key = $this->build_key( $city, $state, $format );
        $ttl = $this->get_ttl();
        set_transient( $key, $data, $ttl );
    }

    /**
     * Delete cached data for a specific city.
     *
     * @param string $city   City name.
     * @param string $state  State code.
     * @param string $format Display format.
     */
    public function delete( $city, $state, $format = 'compact' ) {
        $key = $this->build_key( $city, $state, $format );
        delete_transient( $key );
    }

    /**
     * Build a transient cache key.
     */
    private function build_key( $city, $state, $format ) {
        $raw = strtolower( $city . '_' . $state . '_' . $format );
        return 'weatherly_' . md5( $raw );
    }

    /**
     * Get cache TTL in seconds.
     * Free: 7200 (2 hours, fixed).
     * Pro: configurable (1800–21600).
     */
    private function get_ttl() {
        $tier = get_option( 'weatherly_tier', 'free' );

        if ( 'free' === $tier ) {
            return 7200;
        }

        $ttl = (int) get_option( 'weatherly_cache_ttl', 3600 );
        return max( 1800, min( 21600, $ttl ) ); // Clamp between 30min and 6hr
    }
}
