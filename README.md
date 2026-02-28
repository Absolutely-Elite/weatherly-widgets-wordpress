# Weatherly Widgets for WordPress

Free weather plugin for WordPress. Display real-time forecasts for 35,000+ US & Canadian cities with a simple shortcode or Gutenberg block.

![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/weatherly-widgets)
![License](https://img.shields.io/badge/license-GPLv2-blue)

## Quick Start

Install the plugin, then add weather to any page:

```
[weatherly city="Houston" state="TX"]
```

Or use the **Weatherly Weather** block in the Gutenberg editor.

## Features

- **35,000+ cities** across the US and Canada
- **National Weather Service** data (official government source)
- **Shortcode + Gutenberg block** — works everywhere
- **Smart caching** via WordPress transients
- **Zero configuration** for the free tier

## Pro Features

Upgrade to [Weatherly Widgets Pro](https://weatherlywidgets.com/wordpress) for:

- **Server-side HTML rendering** — weather content Google can actually index
- **Schema.org structured data** — automatic WeatherForecast + Place markup
- **5 display formats** — compact, full forecast, sidebar, 7-day, hourly
- **Unlimited cities** (free tier: 3)
- **Custom CSS** — full styling control
- **Configurable cache** — 30 min to 6 hours

## Installation

### From WordPress.org

1. Go to Plugins → Add New in your WordPress admin
2. Search for "Weatherly Widgets"
3. Click Install Now → Activate

### Manual

1. Download the latest release from this repo
2. Upload to `/wp-content/plugins/weatherly-widgets/`
3. Activate in Plugins → Installed Plugins

## Usage

### Shortcode

```
[weatherly city="Houston" state="TX"]
[weatherly city="Denver" state="CO" format="full"]  <!-- Pro -->
```

### Gutenberg Block

Search for "Weatherly Weather" in the block inserter. Configure city and state in the block settings panel.

### Template Tag (Theme Developers)

```php
<?php echo do_shortcode('[weatherly city="Houston" state="TX"]'); ?>
```

## Settings

Go to **Settings → Weatherly Widgets** to:

- Set a default city and state
- Enter your Pro license key
- Configure cache duration (Pro)

## Data Sources

- **US cities**: [National Weather Service API](https://www.weather.gov/documentation/services-web-api) (free, no key required)
- **Canadian cities**: [Environment Canada](https://weather.gc.ca/)

## Support

- **Free tier**: [GitHub Issues](https://github.com/Absolutely-Elite/weatherly-widgets-wordpress/issues)
- **Pro tier**: support@weatherlywidgets.com

## License

GPLv2 or later. See [LICENSE](LICENSE).

## Links

- [Weatherly Widgets](https://weatherlywidgets.com) — Main site
- [Pro Plugin](https://weatherlywidgets.com/wordpress) — Pricing & features
- [API Documentation](https://weatherlywidgets.com/api) — JSON API

---

Built by [Absolutely Elite LLC](https://absolutelyelite.com) · Houston, TX
