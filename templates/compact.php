<?php
/**
 * Compact weather widget template.
 *
 * Available variables:
 *   $data  — array of weather data from the API
 *   $city  — city name
 *   $state — state code
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current     = $data['current'] ?? array();
$temp        = $current['temp_f'] ?? '--';
$condition   = $current['condition'] ?? 'Unknown';
$icon        = $current['icon'] ?? 'cloudy';
$city_name   = esc_html( $data['city'] ?? $city );
$state_display = esc_html( ! empty( $data['state'] ) ? ucwords( strtolower( $data['state'] ) ) : ( $state ?? '' ) );
$city_url    = esc_url( $data['city_url'] ?? WEATHERLY_API_BASE );
$icon_url    = esc_url( WEATHERLY_PLUGIN_URL . 'public/icons/' . $icon . '.svg' );
$wind_dir    = $this->degrees_to_compass( $current['wind_direction'] ?? '' );
$wind_speed  = $this->format_wind_speed( $current['wind_speed'] ?? '' );
?>
<div class="weatherly-widget weatherly-compact">
    <div class="weatherly-header">
        <img src="<?php echo $icon_url; ?>"
             alt="<?php echo esc_attr( $condition . ', ' . $temp . '°F in ' . $city_name ); ?>"
             class="weatherly-icon" width="48" height="48" loading="lazy">
        <div class="weatherly-info">
            <div class="weatherly-location">
                <a href="<?php echo $city_url; ?>" target="_blank" rel="noopener"><?php echo $city_name; ?>, <?php echo $state_display; ?></a>
            </div>
            <div class="weatherly-temp"><?php echo esc_html( $temp ); ?>°F</div>
            <div class="weatherly-condition"><?php echo esc_html( $condition ); ?></div>
        </div>
    </div>
    <div class="weatherly-details">
        <?php if ( ! empty( $current['wind_speed'] ) && $current['wind_speed'] !== null && $current['wind_speed'] !== 'None' ) : ?>
        <span class="weatherly-detail">
            <span class="weatherly-label">Wind</span>
            <?php echo esc_html( trim( $wind_dir . ' ' . $wind_speed ) ); ?> mph
        </span>
        <?php endif; ?>
        <?php if ( ! empty( $current['humidity'] ) && $current['humidity'] !== null && $current['humidity'] !== 'None' ) : ?>
        <span class="weatherly-detail">
            <span class="weatherly-label">Humidity</span>
            <?php echo esc_html( $current['humidity'] ); ?>%
        </span>
        <?php endif; ?>
    </div>
    <?php if ( isset( $tier ) && $tier !== 'pro' ) : ?>
    <div class="weatherly-footer">
        <small>
            <a href="<?php echo $city_url; ?>" target="_blank" rel="noopener">
                <?php esc_html_e( 'Full forecast on Weatherly Widgets', 'weatherly-widgets' ); ?>
            </a>
        </small>
    </div>
    <?php endif; ?>
</div>
