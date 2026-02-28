=== Weatherly Widgets — Free Weather Forecasts ===
Contributors: absolutelyelite
Donate link: https://weatherlywidgets.com
Tags: weather, forecast, weather widget, temperature, NWS
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display real-time weather forecasts on your WordPress site. Current conditions, 7-day outlook, and hourly forecasts for 35,000+ US & Canadian cities.

== Description ==

**Weatherly Widgets** lets you add real-time weather data to any WordPress page or post with a simple shortcode or Gutenberg block.

Weather data comes directly from the **National Weather Service** (US) and **Environment Canada** (CA) — the same official government sources used by major weather apps. No third-party data resellers.

= Free Features =

* Current conditions widget (temperature, condition, wind, humidity)
* Simple shortcode: `[weatherly city="Houston" state="TX"]`
* Gutenberg block editor support
* 35,000+ cities across the US and Canada
* 2-hour auto-refresh cache
* Up to 3 cities per site

= Pro Features ($9.99/mo) =

* **Server-side HTML rendering** — Google indexes your weather content (unlike iframe widgets)
* **Schema.org structured data** — WeatherForecast + Place markup injected automatically
* 5 display formats: compact, full forecast, sidebar card, 7-day outlook, hourly strip
* Unlimited cities
* Full CSS customization via WP Customizer or custom CSS field
* Configurable cache TTL (30 min – 6 hours)
* Remove "Powered by" backlink
* Priority email support

[Learn more about Pro →](https://weatherlywidgets.com/wordpress)

= Why Weatherly Widgets? =

Most weather widgets use iframes or client-side JavaScript. Search engines can't see this content, so it adds zero SEO value to your pages.

**Weatherly Pro renders real HTML directly into your page.** Google can crawl it, index it, and use the Schema.org markup for rich results. This is the key difference.

= Perfect For =

* City and local niche sites
* Real estate listing pages
* Travel blogs and destination guides
* Local business directories
* Event and venue sites
* RV and camping sites

= Supported Cities =

Over 35,000 cities across all 50 US states, DC, and Canadian provinces. Data refreshes from official government weather APIs.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/weatherly-widgets/` or install through the WordPress plugin screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Settings → Weatherly Widgets** to configure defaults and enter your Pro license key (optional).

= Quick Start =

Add weather to any page with the shortcode:

`[weatherly city="Houston" state="TX"]`

Or use the **Weatherly Weather** Gutenberg block in the block editor.

= Pro Setup =

1. Purchase a license at [weatherlywidgets.com/wordpress](https://weatherlywidgets.com/wordpress)
2. Go to Settings → Weatherly Widgets
3. Paste your license key and click Validate
4. Pro features are now active — use `format="full"` and other display formats

== Frequently Asked Questions ==

= Will this help my SEO? =

The free tier uses JavaScript rendering (like most weather widgets), which search engines typically can't index. The Pro tier renders server-side HTML directly into your page with Schema.org structured data — Google indexes every word.

= What cities are supported? =

Over 35,000 cities across the United States and Canada. Weather data comes from the National Weather Service (US) and Environment Canada.

= How often does the data update? =

Free tier: every 2 hours. Pro tier: configurable from 30 minutes to 6 hours.

= Do I need an API key? =

Free tier: no. Pro tier: a license key is provided when you subscribe.

= Can I use it on multiple sites? =

Each Pro license covers one domain. Volume discounts available — contact connect@weatherlywidgets.com.

= What happens if your server is down? =

The plugin caches weather data locally using WordPress transients. If the API is temporarily unreachable, it serves cached data with a "last updated" note. If no cache exists, it shows a friendly fallback with a link to the city's page on weatherlywidgets.com.

== Screenshots ==

1. Compact weather widget on a page
2. Full forecast format (Pro)
3. Gutenberg block editor
4. WordPress admin settings page

== Changelog ==

= 1.0.0 =
* Initial release
* Shortcode support: `[weatherly city="Houston" state="TX"]`
* Gutenberg block: Weatherly Weather
* Free tier: compact widget via JavaScript embed
* Pro tier: server-side rendered HTML with SEO schema
* WordPress transient caching (2hr free, configurable Pro)
* License key validation and management
* 35,000+ US and Canadian cities supported

== Upgrade Notice ==

= 1.0.0 =
Initial release. Install and add weather to your site in 60 seconds.
