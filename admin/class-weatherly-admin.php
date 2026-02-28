<?php
/**
 * Admin settings page.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Weatherly_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'wp_ajax_weatherly_validate_license', array( $this, 'ajax_validate_license' ) );
    }

    /**
     * Add settings page under Settings menu.
     */
    public function add_menu() {
        add_options_page(
            __( 'Weatherly Widgets', 'weatherly-widgets' ),
            __( 'Weatherly Widgets', 'weatherly-widgets' ),
            'manage_options',
            'weatherly-widgets',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        register_setting( 'weatherly_settings', 'weatherly_license_key', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'weatherly_settings', 'weatherly_default_city', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'weatherly_settings', 'weatherly_default_state', array(
            'type'              => 'string',
            'sanitize_callback' => array( $this, 'sanitize_state' ),
        ) );
        register_setting( 'weatherly_settings', 'weatherly_cache_ttl', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
        ) );
        register_setting( 'weatherly_settings', 'weatherly_units', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
    }

    /**
     * Sanitize state code (2 uppercase letters).
     */
    public function sanitize_state( $value ) {
        return strtoupper( substr( sanitize_text_field( $value ), 0, 2 ) );
    }

    /**
     * Enqueue admin styles.
     */
    public function enqueue_admin_styles( $hook ) {
        if ( 'settings_page_weatherly-widgets' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'weatherly-admin',
            WEATHERLY_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            WEATHERLY_VERSION
        );
    }

    /**
     * AJAX handler for license validation.
     */
    public function ajax_validate_license() {
        check_ajax_referer( 'weatherly_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $license_key = sanitize_text_field( $_POST['license_key'] ?? '' );

        if ( empty( $license_key ) ) {
            // Deactivate
            $license = new Weatherly_License();
            $license->deactivate();
            wp_send_json_success( array( 'message' => 'License removed. Using free tier.' ) );
        }

        // Save and validate
        update_option( 'weatherly_license_key', $license_key );
        $license = new Weatherly_License();
        $result = $license->validate( $license_key );

        if ( $result['valid'] === true ) {
            wp_send_json_success( $result );
        } elseif ( $result['valid'] === null ) {
            wp_send_json_error( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $license  = new Weatherly_License();
        $status   = $license->get_status();

        include WEATHERLY_PLUGIN_DIR . 'admin/views/settings.php';
    }
}

// Initialize admin
new Weatherly_Admin();
