/**
 * Weatherly Widgets - WordPress Free Tier
 * Fetches from API and renders inline (no Shadow DOM).
 * Updated for flat API response: temperature_f, condition, icon, wind, humidity, city_url.
 */
(function () {
  "use strict";

  var API_BASE = "https://weatherlywidgets.com/api/v1/widget";

  function attr(el, name, def) {
    var v = el.getAttribute("data-" + name);
    return v !== null && v !== "" ? v : def;
  }

  function buildUrl(el) {
    var city = attr(el, "city", "");
    var state = attr(el, "state", "");
    if (!city || !state) return null;
    return (
      API_BASE +
      "?city=" +
      encodeURIComponent(city) +
      "&state=" +
      encodeURIComponent(state)
    );
  }

  function escapeHtml(s) {
    if (s == null) return "";
    var t = String(s);
    return t
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function initOne(el) {
    var url = buildUrl(el);
    if (!url) {
      el.innerHTML =
        '<div style="padding:16px;font-size:14px;color:#6b7280;">Please set data-city and data-state.</div>';
      return;
    }

    var cityUrl = attr(el, "city-url", "") || "";
    var iconsBase = attr(el, "icons-base", "https://weatherlywidgets.com/static/icons/");
    var cityDisplay = attr(el, "city", "") + ", " + attr(el, "state", "");

    fetch(url)
      .then(function (r) {
        if (!r.ok) throw new Error("Weather unavailable");
        return r.json();
      })
      .then(function (d) {
        if (!d || d.error) {
          el.innerHTML =
            '<div style="padding:16px;font-size:14px;color:#6b7280;">Weather temporarily unavailable. <a href="' +
            escapeHtml(cityUrl) +
            '" target="_blank">View forecast</a></div>';
          return;
        }

        var temp = d.temperature_f != null ? d.temperature_f : "--";
        var cond = d.condition || "N/A";
        var icon = d.icon || "";
        var wind = d.wind || "--";
        var humidity = d.humidity != null ? d.humidity : "--";

        var cityUrlFinal = d.city_url || cityUrl;
        var cityDisplayFinal =
          d.city && d.state ? d.city + ", " + d.state : cityDisplay;

        var iconHtml = "";
        if (icon) {
          var src =
            icon.indexOf("http") === 0 ? icon : iconsBase + icon + ".svg";
          iconHtml =
            '<img decoding="async" src="' +
            escapeHtml(src) +
            '" alt="' +
            escapeHtml(cond) +
            '" width="48" height="48" style="flex-shrink:0;" />';
        }

        el.innerHTML =
          '<div style="display:flex;align-items:center;gap:12px;padding:16px;">' +
          iconHtml +
          '<div>' +
          '<div style="font-size:14px;font-weight:600;color:#374151;"><a href="' +
          escapeHtml(cityUrlFinal) +
          '" target="_blank" rel="noopener" style="color:#374151;text-decoration:none;">' +
          escapeHtml(cityDisplayFinal) +
          "</a></div>" +
          '<div style="font-size:28px;font-weight:700;color:#111827;line-height:1.2;">' +
          escapeHtml(temp) +
          "&deg;F</div>" +
          '<div style="font-size:14px;color:#6b7280;">' +
          escapeHtml(cond) +
          "</div>" +
          "</div>" +
          "</div>" +
          '<div style="display:flex;gap:16px;padding:0 16px 12px;font-size:13px;color:#6b7280;border-top:1px solid #f3f4f6;padding-top:12px;">' +
          '<span><span style="color:#9ca3af;font-weight:500;">Wind </span>' +
          escapeHtml(wind) +
          "</span>" +
          '<span><span style="color:#9ca3af;font-weight:500;">Humidity </span>' +
          escapeHtml(humidity) +
          "%</span>" +
          "</div>" +
          '<div style="padding:8px 16px;border-top:1px solid #f3f4f6;font-size:11px;color:#9ca3af;">' +
          '<a href="' +
          escapeHtml(cityUrlFinal) +
          '" target="_blank" rel="noopener" style="color:#9ca3af;">Full forecast on Weatherly Widgets</a>' +
          "</div>";
      })
      .catch(function () {
        el.innerHTML =
          '<div style="padding:16px;font-size:14px;color:#6b7280;">Weather temporarily unavailable. <a href="' +
          escapeHtml(cityUrl) +
          '" target="_blank">View forecast</a></div>';
      });
  }

  function init() {
    document.querySelectorAll(".weatherly-widget-free").forEach(function (el) {
      initOne(el);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
