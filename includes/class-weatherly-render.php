<?php
/**
 * Widget renderer.
 *
 * Free tier: outputs a div + JS embed.
 * Pro tier: fetches data and renders server-side PHP templates.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Weatherly_Render {

    private $api;
    private $cache;

    public function __construct() {
        $this->api   = new Weatherly_API();
        $this->cache = new Weatherly_Cache();
    }

    /**
     * Render a weather widget.
     *
     * @param array $atts Shortcode/block attributes.
     * @return string HTML output.
     */
    public function render( $atts ) {
        $city   = sanitize_text_field( $atts['city'] ?? '' );
        $state  = sanitize_text_field( $atts['state'] ?? '' );
        $format = sanitize_text_field( $atts['format'] ?? 'compact' );

        if ( empty( $city ) || empty( $state ) ) {
            return $this->render_error( 'Please specify a city and state.' );
        }

        $tier = get_option( 'weatherly_tier', 'free' );

        if ( 'free' === $tier ) {
            return $this->render_free( $city, $state );
        }

        return $this->render_pro( $city, $state, $format );
    }

    /**
     * Free tier: Output a container div that widget.js populates.
     */
    private function render_free( $city, $state ) {
        $city_slug  = sanitize_title( $city );
        $state_slug = strtolower( sanitize_text_field( $state ) );
        $widget_id  = 'weatherly-widget-' . $city_slug . '-' . $state_slug;

        $city_url = esc_url( WEATHERLY_API_BASE . '/weather/' . $state_slug . '/' . $city_slug );

        $html  = '<div id="' . esc_attr( $widget_id ) . '" ';
        $html .= 'class="weatherly-widget weatherly-free" ';
        $html .= 'data-city="' . esc_attr( $city ) . '" ';
        $html .= 'data-state="' . esc_attr( strtoupper( $state ) ) . '">';
        $html .= '</div>';
        $html .= '<p class="weatherly-attribution">';
        $html .= '<a href="' . $city_url . '" target="_blank" rel="noopener">Powered by Weatherly Widgets</a>';
        $html .= '</p>';

        return $html;
    }

    /**
     * Pro tier: Fetch data and render server-side HTML.
     */
    private function render_pro( $city, $state, $format ) {
        // Check cache first
        $data = $this->cache->get( $city, $state, $format );

        if ( false === $data ) {
            // Fetch from API
            $data = $this->api->get_weather( $city, $state, $format );

            if ( is_wp_error( $data ) ) {
                // Try stale cache
                $stale = $this->cache->get( $city, $state, $format );
                if ( false !== $stale ) {
                    $data = $stale;
                } else {
                    return $this->render_fallback( $city, $state );
                }
            } else {
                // Cache the fresh data
                $this->cache->set( $city, $state, $format, $data );
            }
        }

        // Validate format
        $allowed_formats = array( 'compact', 'full', 'sidebar', 'sevenday', 'hourly' );
        if ( ! in_array( $format, $allowed_formats, true ) ) {
            $format = 'compact';
        }

        // Render the template
        $template_file = WEATHERLY_PLUGIN_DIR . 'templates/' . $format . '.php';

        if ( ! file_exists( $template_file ) ) {
            $template_file = WEATHERLY_PLUGIN_DIR . 'templates/compact.php';
        }

        ob_start();
        include $template_file;
        $html = ob_get_clean();

        // Inject Schema.org if available (Pro only)
        if ( ! empty( $data['schema_json_ld'] ) ) {
            $html .= '<script type="application/ld+json">' . $data['schema_json_ld'] . '</script>';
        }

        return $html;
    }

    /**
     * Render fallback when API is unavailable.
     */
    private function render_fallback( $city, $state ) {
        $city_slug  = sanitize_title( $city );
        $state_slug = strtolower( sanitize_text_field( $state ) );
        $city_url   = esc_url( WEATHERLY_API_BASE . '/weather/' . $state_slug . '/' . $city_slug );

        $html  = '<div class="weatherly-widget weatherly-fallback">';
        $html .= '<p>Weather data temporarily unavailable.</p>';
        $html .= '<p><a href="' . $city_url . '" target="_blank" rel="noopener">';
        $html .= 'View ' . esc_html( $city ) . ', ' . esc_html( strtoupper( $state ) ) . ' weather on Weatherly Widgets';
        $html .= '</a></p>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render an error message.
     */
    private function render_error( $message ) {
        return '<div class="weatherly-widget weatherly-error"><p>' . esc_html( $message ) . '</p></div>';
    }
}
