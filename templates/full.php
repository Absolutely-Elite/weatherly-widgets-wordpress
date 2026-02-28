<?php
/**
 * Full forecast widget template (Pro).
 *
 * @package WeatherlyWidgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current   = $data['current'] ?? array();
$forecast  = $data['forecast'] ?? array();
$sun       = $data['sun'] ?? array();
$city_name = esc_html( $data['city'] ?? $city );
$state_code = esc_html( strtoupper( $data['state'] ?? $state ) );
$city_url  = esc_url( $data['city_url'] ?? WEATHERLY_API_BASE );
$icon      = $current['icon'] ?? 'cloudy';
$icon_url  = esc_url( WEATHERLY_PLUGIN_URL . 'public/icons/' . $icon . '.svg' );
?>
<div class="weatherly-widget weatherly-full">
    <!-- Current Conditions -->
    <div class="weatherly-current">
        <div class="weatherly-current-header">
            <img src="<?php echo $icon_url; ?>"
                 alt="<?php echo esc_attr( ( $current['condition'] ?? '' ) . ', ' . ( $current['temp_f'] ?? '' ) . '°F' ); ?>"
                 class="weatherly-icon-lg" width="64" height="64" loading="lazy">
            <div>
                <h3 class="weatherly-city-name">
                    <a href="<?php echo $city_url; ?>" target="_blank" rel="noopener"><?php echo $city_name; ?>, <?php echo $state_code; ?></a>
                </h3>
                <div class="weatherly-temp-lg"><?php echo esc_html( $current['temp_f'] ?? '--' ); ?>°F</div>
                <div class="weatherly-condition-text"><?php echo esc_html( $current['condition'] ?? '' ); ?></div>
            </div>
        </div>
        <div class="weatherly-current-details">
            <div class="weatherly-detail-item">
                <span class="weatherly-label">Wind</span>
                <span><?php echo esc_html( ( $current['wind_direction'] ?? '' ) . ' ' . ( $current['wind_speed'] ?? '--' ) ); ?> mph</span>
            </div>
            <div class="weatherly-detail-item">
                <span class="weatherly-label">Humidity</span>
                <span><?php echo esc_html( $current['humidity'] ?? '--' ); ?>%</span>
            </div>
            <div class="weatherly-detail-item">
                <span class="weatherly-label">Dewpoint</span>
                <span><?php echo esc_html( $current['dewpoint'] ?? '--' ); ?>°F</span>
            </div>
            <?php if ( ! empty( $sun['sunrise'] ) ) : ?>
            <div class="weatherly-detail-item">
                <span class="weatherly-label">Sunrise</span>
                <span><?php echo esc_html( $sun['sunrise'] ); ?></span>
            </div>
            <div class="weatherly-detail-item">
                <span class="weatherly-label">Sunset</span>
                <span><?php echo esc_html( $sun['sunset'] ); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 7-Day Forecast -->
    <?php if ( ! empty( $forecast ) ) : ?>
    <div class="weatherly-forecast">
        <h4 class="weatherly-section-title">7-Day Forecast</h4>
        <div class="weatherly-forecast-grid">
            <?php foreach ( array_slice( $forecast, 0, 7 ) as $day ) :
                $day_icon_url = esc_url( WEATHERLY_PLUGIN_URL . 'public/icons/' . ( $day['icon'] ?? 'cloudy' ) . '.svg' );
            ?>
            <div class="weatherly-forecast-day">
                <div class="weatherly-day-name"><?php echo esc_html( $day['day'] ?? '' ); ?></div>
                <img src="<?php echo $day_icon_url; ?>"
                     alt="<?php echo esc_attr( $day['condition'] ?? '' ); ?>"
                     width="32" height="32" loading="lazy">
                <div class="weatherly-day-temps">
                    <span class="weatherly-high"><?php echo esc_html( $day['high'] ?? '--' ); ?>°</span>
                    <span class="weatherly-low"><?php echo esc_html( $day['low'] ?? '--' ); ?>°</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Attribution -->
    <div class="weatherly-footer">
        <small>
            <a href="<?php echo $city_url; ?>" target="_blank" rel="noopener">
                <?php esc_html_e( 'Full forecast on Weatherly Widgets', 'weatherly-widgets' ); ?>
            </a>
            · <?php echo esc_html( $data['attribution'] ?? 'Weather data from National Weather Service' ); ?>
        </small>
    </div>
</div>
