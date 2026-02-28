<?php
/**
 * Shortcode handler: [weatherly]
 *
 * Usage:
 *   [weatherly city="Houston" state="TX"]
 *   [weatherly city="Denver" state="CO" format="full"]
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Weatherly_Shortcode {

    /**
     * Register the shortcode.
     */
    public function register() {
        add_shortcode( 'weatherly', array( $this, 'render' ) );
    }

    /**
     * Render the shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render( $atts ) {
        $atts = shortcode_atts(
            array(
                'city'   => get_option( 'weatherly_default_city', '' ),
                'state'  => get_option( 'weatherly_default_state', '' ),
                'format' => 'compact',
                'zip'    => '',
            ),
            $atts,
            'weatherly'
        );

        // If ZIP provided, it takes priority (future feature)
        // For now, city + state are required
        if ( empty( $atts['city'] ) || empty( $atts['state'] ) ) {
            return '<div class="weatherly-widget weatherly-error">'
                 . '<p>Please specify city and state: <code>[weatherly city="Houston" state="TX"]</code></p>'
                 . '</div>';
        }

        // Free tier: enforce 3 city limit
        $tier = get_option( 'weatherly_tier', 'free' );
        if ( 'free' === $tier && ! $this->check_city_limit( $atts['city'], $atts['state'] ) ) {
            return '<div class="weatherly-widget weatherly-error">'
                 . '<p>Free tier supports up to 3 cities. <a href="' . esc_url( WEATHERLY_API_BASE . '/wordpress' ) . '" target="_blank">Upgrade to Pro</a> for unlimited cities.</p>'
                 . '</div>';
        }

        // Free tier: force compact format
        if ( 'free' === $tier ) {
            $atts['format'] = 'compact';
        }

        $renderer = new Weatherly_Render();
        return $renderer->render( $atts );
    }

    /**
     * Check if adding this city exceeds the free tier limit of 3.
     */
    private function check_city_limit( $city, $state ) {
        $used_cities = get_option( 'weatherly_used_cities', array() );
        $key = strtolower( $city . '_' . $state );

        if ( in_array( $key, $used_cities, true ) ) {
            return true; // Already tracked
        }

        if ( count( $used_cities ) >= 3 ) {
            return false; // Limit reached
        }

        // Track this city
        $used_cities[] = $key;
        update_option( 'weatherly_used_cities', $used_cities );
        return true;
    }
}
