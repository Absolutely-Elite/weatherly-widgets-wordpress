<?php
/**
 * Settings page template.
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap weatherly-settings">
    <h1>
        <img src="<?php echo esc_url( WEATHERLY_PLUGIN_URL . 'assets/icon-24.png' ); ?>" alt="" style="vertical-align: middle; margin-right: 8px;" width="24" height="24">
        <?php esc_html_e( 'Weatherly Widgets', 'weatherly-widgets' ); ?>
    </h1>

    <!-- License Status Banner -->
    <?php if ( 'pro' === $status['tier'] ) : ?>
        <div class="notice notice-success" style="margin-top: 15px;">
            <p>
                <strong>✓ Pro License Active</strong>
                <?php if ( ! empty( $status['expires'] ) ) : ?>
                    — Expires <?php echo esc_html( date( 'M j, Y', strtotime( $status['expires'] ) ) ); ?>
                <?php endif; ?>
            </p>
        </div>
    <?php else : ?>
        <div class="notice notice-info" style="margin-top: 15px;">
            <p>
                <strong>Free Tier</strong> — Upgrade to Pro for server-rendered HTML, SEO schema, and custom styling.
                <a href="<?php echo esc_url( WEATHERLY_API_BASE . '/wordpress' ); ?>" target="_blank">Learn more →</a>
            </p>
        </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'weatherly_settings' ); ?>

        <table class="form-table" role="presentation">
            <!-- License Key -->
            <tr>
                <th scope="row">
                    <label for="weatherly_license_key"><?php esc_html_e( 'License Key', 'weatherly-widgets' ); ?></label>
                </th>
                <td>
                    <input type="text" id="weatherly_license_key" name="weatherly_license_key"
                           value="<?php echo esc_attr( get_option( 'weatherly_license_key', '' ) ); ?>"
                           class="regular-text" placeholder="ww_wp_pro_XXXXXXXX..."
                           style="font-family: monospace;">
                    <button type="button" id="weatherly-validate-btn" class="button button-secondary">
                        <?php esc_html_e( 'Validate', 'weatherly-widgets' ); ?>
                    </button>
                    <span id="weatherly-license-status" style="margin-left: 10px;"></span>
                    <p class="description">
                        <?php esc_html_e( 'Enter your Pro license key. Leave blank for free tier.', 'weatherly-widgets' ); ?>
                        <a href="<?php echo esc_url( WEATHERLY_API_BASE . '/wordpress#pricing' ); ?>" target="_blank">
                            <?php esc_html_e( 'Get a license', 'weatherly-widgets' ); ?>
                        </a>
                    </p>
                </td>
            </tr>

            <!-- Default City -->
            <tr>
                <th scope="row">
                    <label for="weatherly_default_city"><?php esc_html_e( 'Default City', 'weatherly-widgets' ); ?></label>
                </th>
                <td>
                    <input type="text" id="weatherly_default_city" name="weatherly_default_city"
                           value="<?php echo esc_attr( get_option( 'weatherly_default_city', '' ) ); ?>"
                           class="regular-text" placeholder="Houston">
                    <p class="description">
                        <?php esc_html_e( 'Used when [weatherly] is called without specifying a city.', 'weatherly-widgets' ); ?>
                    </p>
                </td>
            </tr>

            <!-- Default State -->
            <tr>
                <th scope="row">
                    <label for="weatherly_default_state"><?php esc_html_e( 'Default State', 'weatherly-widgets' ); ?></label>
                </th>
                <td>
                    <input type="text" id="weatherly_default_state" name="weatherly_default_state"
                           value="<?php echo esc_attr( get_option( 'weatherly_default_state', '' ) ); ?>"
                           class="regular-text" maxlength="2" style="width: 80px;" placeholder="TX">
                    <p class="description">
                        <?php esc_html_e( 'Two-letter state or province code (e.g., TX, CA, ON).', 'weatherly-widgets' ); ?>
                    </p>
                </td>
            </tr>

            <!-- Cache TTL (Pro only) -->
            <?php if ( 'pro' === $status['tier'] ) : ?>
            <tr>
                <th scope="row">
                    <label for="weatherly_cache_ttl"><?php esc_html_e( 'Cache Duration', 'weatherly-widgets' ); ?></label>
                </th>
                <td>
                    <select id="weatherly_cache_ttl" name="weatherly_cache_ttl">
                        <option value="1800" <?php selected( get_option( 'weatherly_cache_ttl' ), 1800 ); ?>>30 minutes</option>
                        <option value="3600" <?php selected( get_option( 'weatherly_cache_ttl' ), 3600 ); ?>>1 hour</option>
                        <option value="7200" <?php selected( get_option( 'weatherly_cache_ttl' ), 7200 ); ?>>2 hours (default)</option>
                        <option value="14400" <?php selected( get_option( 'weatherly_cache_ttl' ), 14400 ); ?>>4 hours</option>
                        <option value="21600" <?php selected( get_option( 'weatherly_cache_ttl' ), 21600 ); ?>>6 hours</option>
                    </select>
                    <p class="description">
                        <?php esc_html_e( 'How long to cache weather data before refreshing from the API.', 'weatherly-widgets' ); ?>
                    </p>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <?php submit_button(); ?>
    </form>

    <!-- Usage Guide -->
    <div class="weatherly-usage-guide" style="margin-top: 30px; background: #fff; border: 1px solid #ccd0d4; padding: 20px; border-radius: 4px;">
        <h2><?php esc_html_e( 'How to Use', 'weatherly-widgets' ); ?></h2>

        <h3><?php esc_html_e( 'Shortcode', 'weatherly-widgets' ); ?></h3>
        <p>Add weather to any page or post:</p>
        <code style="display: block; padding: 10px; background: #f0f0f1; margin: 10px 0;">[weatherly city="Houston" state="TX"]</code>

        <?php if ( 'pro' === $status['tier'] ) : ?>
        <p>Pro formats:</p>
        <code style="display: block; padding: 10px; background: #f0f0f1; margin: 10px 0;">[weatherly city="Houston" state="TX" format="full"]</code>
        <p>Available formats: <code>compact</code>, <code>full</code>, <code>sidebar</code>, <code>sevenday</code>, <code>hourly</code></p>
        <?php endif; ?>

        <h3><?php esc_html_e( 'Gutenberg Block', 'weatherly-widgets' ); ?></h3>
        <p>Search for "Weatherly Weather" in the block editor to add the weather block visually.</p>

        <?php if ( 'pro' === $status['tier'] ) : ?>
        <h3><?php esc_html_e( 'Template Tag (Theme Developers)', 'weatherly-widgets' ); ?></h3>
        <code style="display: block; padding: 10px; background: #f0f0f1; margin: 10px 0;">&lt;?php echo do_shortcode('[weatherly city="Houston" state="TX" format="full"]'); ?&gt;</code>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#weatherly-validate-btn').on('click', function() {
        var btn = $(this);
        var status = $('#weatherly-license-status');
        var key = $('#weatherly_license_key').val();

        btn.prop('disabled', true).text('Validating...');
        status.text('');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'weatherly_validate_license',
                nonce: '<?php echo esc_js( wp_create_nonce( 'weatherly_admin_nonce' ) ); ?>',
                license_key: key
            },
            success: function(response) {
                btn.prop('disabled', false).text('Validate');
                if (response.success) {
                    status.html('<span style="color: #00a32a;">✓ ' + response.data.message + '</span>');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    status.html('<span style="color: #d63638;">✗ ' + response.data.message + '</span>');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Validate');
                status.html('<span style="color: #d63638;">Connection error. Try again.</span>');
            }
        });
    });
});
</script>
