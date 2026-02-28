<?php
/**
 * Weatherly API client.
 *
 * Handles all communication with the Weatherly Widgets API.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Weatherly_API {

    /**
     * API base URL.
     */
    private $base_url;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->base_url = WEATHERLY_API_BASE;
    }

    /**
     * Get weather data for a city.
     *
     * @param string $city  City name.
     * @param string $state Two-letter state/province code.
     * @param string $format Display format (compact, full, sidebar, sevenday, hourly).
     * @return array|WP_Error Weather data array or error.
     */
    public function get_weather( $city, $state, $format = 'compact' ) {
        $tier = get_option( 'weatherly_tier', 'free' );

        if ( 'pro' === $tier ) {
            return $this->get_weather_pro( $city, $state, $format );
        }

        return $this->get_weather_free( $city, $state );
    }

    /**
     * Free tier: Use public widget endpoint.
     */
    private function get_weather_free( $city, $state ) {
        $url = add_query_arg(
            array(
                'city'  => rawurlencode( $city ),
                'state' => rawurlencode( strtoupper( $state ) ),
            ),
            $this->base_url . '/api/v1/widget'
        );

        return $this->make_request( $url );
    }

    /**
     * Pro tier: Use authenticated wp-plugin endpoint.
     */
    private function get_weather_pro( $city, $state, $format = 'compact' ) {
        $license_key = get_option( 'weatherly_license_key', '' );

        $url = add_query_arg(
            array(
                'city'   => rawurlencode( $city ),
                'state'  => rawurlencode( strtoupper( $state ) ),
                'format' => sanitize_text_field( $format ),
                'key'    => $license_key,
                'schema' => '1',
            ),
            $this->base_url . '/api/v1/wp-plugin'
        );

        return $this->make_request( $url );
    }

    /**
     * Make an API request.
     *
     * @param string $url Full API URL.
     * @return array|WP_Error Decoded response or error.
     */
    private function make_request( $url ) {
        $response = wp_remote_get( $url, array(
            'timeout'    => 10,
            'user-agent' => 'WeatherlyWidgets-WP/' . WEATHERLY_VERSION . ' (' . home_url() . ')',
            'headers'    => array(
                'Accept' => 'application/json',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );

        if ( 200 !== $code ) {
            return new WP_Error(
                'weatherly_api_error',
                sprintf( 'API returned status %d', $code ),
                array( 'status' => $code, 'body' => $body )
            );
        }

        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'weatherly_json_error', 'Invalid JSON response' );
        }

        return $data;
    }

    /**
     * Validate a license key with the server.
     *
     * @param string $license_key License key to validate.
     * @return array|WP_Error Validation response or error.
     */
    public function validate_license( $license_key ) {
        $response = wp_remote_post(
            $this->base_url . '/api/v1/license/validate',
            array(
                'timeout' => 10,
                'headers' => array( 'Content-Type' => 'application/json' ),
                'body'    => wp_json_encode( array(
                    'license_key' => $license_key,
                    'domain'      => $this->get_site_domain(),
                ) ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'weatherly_json_error', 'Invalid JSON response' );
        }

        return $data;
    }

    /**
     * Get the normalized site domain.
     */
    private function get_site_domain() {
        $url = home_url();
        $parsed = wp_parse_url( $url );
        $domain = isset( $parsed['host'] ) ? $parsed['host'] : $url;

        // Strip www
        if ( 0 === strpos( $domain, 'www.' ) ) {
            $domain = substr( $domain, 4 );
        }

        return strtolower( $domain );
    }
}
