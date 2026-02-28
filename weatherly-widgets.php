<?php
/**
 * Plugin Name:       Weatherly Widgets
 * Plugin URI:        https://weatherlywidgets.com/wordpress
 * Description:       Display real-time weather forecasts on your WordPress site. Current conditions, 7-day outlook, and hourly forecasts for 35,000+ US & Canadian cities.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Absolutely Elite LLC
 * Author URI:        https://absolutelyelite.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       weatherly-widgets
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'WEATHERLY_VERSION', '1.0.0' );
define( 'WEATHERLY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WEATHERLY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WEATHERLY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WEATHERLY_API_BASE', 'https://weatherlywidgets.com' );

// Include core classes
require_once WEATHERLY_PLUGIN_DIR . 'includes/class-weatherly-api.php';
require_once WEATHERLY_PLUGIN_DIR . 'includes/class-weatherly-cache.php';
require_once WEATHERLY_PLUGIN_DIR . 'includes/class-weatherly-render.php';
require_once WEATHERLY_PLUGIN_DIR . 'includes/class-weatherly-shortcode.php';
require_once WEATHERLY_PLUGIN_DIR . 'includes/class-weatherly-license.php';

// Admin
if ( is_admin() ) {
    require_once WEATHERLY_PLUGIN_DIR . 'admin/class-weatherly-admin.php';
}

/**
 * Main plugin class.
 */
final class Weatherly_Widgets {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        // Activation / deactivation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Initialize components after plugins are loaded
        add_action( 'init', array( $this, 'init' ) );

        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Register Gutenberg block
        add_action( 'init', array( $this, 'register_block' ) );

        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . WEATHERLY_PLUGIN_BASENAME, array( $this, 'settings_link' ) );
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        // Set default options
        $defaults = array(
            'weatherly_license_key'   => '',
            'weatherly_default_city'  => '',
            'weatherly_default_state' => '',
            'weatherly_cache_ttl'     => 7200, // 2 hours
            'weatherly_units'         => 'imperial',
            'weatherly_tier'          => 'free',
        );

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( $key ) ) {
                add_option( $key, $value );
            }
        }

        // Clear any stale transients
        $this->clear_all_transients();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        $this->clear_all_transients();
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin components.
     */
    public function init() {
        // Register shortcode
        $shortcode = new Weatherly_Shortcode();
        $shortcode->register();

        // Check license status (cached, runs once per day)
        if ( ! wp_doing_cron() && ! wp_doing_ajax() ) {
            $license = new Weatherly_License();
            $license->maybe_validate();
        }
    }

    /**
     * Enqueue frontend scripts and styles.
     */
    public function enqueue_scripts() {
        // Only load on pages that use the shortcode or block
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }

        $has_shortcode = has_shortcode( $post->post_content, 'weatherly' );
        $has_block     = has_block( 'weatherly/weather', $post );

        if ( $has_shortcode || $has_block ) {
            // Free tier: load widget.js from Weatherly CDN
            $tier = get_option( 'weatherly_tier', 'free' );

            if ( 'free' === $tier ) {
                wp_enqueue_script(
                    'weatherly-widget',
                    WEATHERLY_API_BASE . '/static/js/widget.js',
                    array(),
                    WEATHERLY_VERSION,
                    true
                );
            }

            // Always load local styles
            wp_enqueue_style(
                'weatherly-widget-css',
                WEATHERLY_PLUGIN_URL . 'public/css/weatherly-widget.css',
                array(),
                WEATHERLY_VERSION
            );
        }
    }

    /**
     * Register Gutenberg block.
     */
    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        // Editor script
        wp_register_script(
            'weatherly-block-editor',
            WEATHERLY_PLUGIN_URL . 'blocks/weatherly-block/edit.js',
            array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
            WEATHERLY_VERSION,
            true
        );

        // Pass settings to editor
        wp_localize_script( 'weatherly-block-editor', 'weatherlyBlockData', array(
            'apiBase'      => WEATHERLY_API_BASE,
            'tier'         => get_option( 'weatherly_tier', 'free' ),
            'defaultCity'  => get_option( 'weatherly_default_city', '' ),
            'defaultState' => get_option( 'weatherly_default_state', '' ),
        ) );

        register_block_type( 'weatherly/weather', array(
            'editor_script'   => 'weatherly-block-editor',
            'render_callback' => array( $this, 'render_block' ),
            'attributes'      => array(
                'city'   => array( 'type' => 'string', 'default' => '' ),
                'state'  => array( 'type' => 'string', 'default' => '' ),
                'format' => array( 'type' => 'string', 'default' => 'compact' ),
            ),
        ) );
    }

    /**
     * Server-side render callback for the Gutenberg block.
     */
    public function render_block( $attributes ) {
        $shortcode = new Weatherly_Shortcode();
        return $shortcode->render( $attributes );
    }

    /**
     * Add settings link on the plugins page.
     */
    public function settings_link( $links ) {
        $settings = '<a href="' . admin_url( 'options-general.php?page=weatherly-widgets' ) . '">' . __( 'Settings', 'weatherly-widgets' ) . '</a>';
        array_unshift( $links, $settings );
        return $links;
    }

    /**
     * Clear all weatherly transients.
     */
    private function clear_all_transients() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_weatherly_%' OR option_name LIKE '_transient_timeout_weatherly_%'"
        );
    }
}

// Initialize
Weatherly_Widgets::instance();
