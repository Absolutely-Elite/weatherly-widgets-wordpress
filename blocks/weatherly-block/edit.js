/**
 * Weatherly Weather — Gutenberg Block Editor
 */
( function( blocks, element, components, i18n ) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps = wp.blockEditor.useBlockProps;

    blocks.registerBlockType( 'weatherly/weather', {
        title: i18n.__( 'Weatherly Weather', 'weatherly-widgets' ),
        description: i18n.__( 'Display real-time weather for any US or Canadian city.', 'weatherly-widgets' ),
        icon: 'cloud',
        category: 'widgets',
        keywords: [ 'weather', 'forecast', 'temperature', 'weatherly' ],

        attributes: {
            city: { type: 'string', default: '' },
            state: { type: 'string', default: '' },
            format: { type: 'string', default: 'compact' },
        },

        edit: function( props ) {
            var attrs = props.attributes;
            var tier = ( window.weatherlyBlockData && window.weatherlyBlockData.tier ) || 'free';

            var formatOptions = [ { label: 'Compact', value: 'compact' } ];

            if ( tier === 'pro' ) {
                formatOptions = formatOptions.concat( [
                    { label: 'Full Forecast', value: 'full' },
                    { label: 'Sidebar Card', value: 'sidebar' },
                    { label: '7-Day Outlook', value: 'sevenday' },
                    { label: 'Hourly Strip', value: 'hourly' },
                ] );
            }

            return el(
                element.Fragment,
                null,
                // Inspector (sidebar) controls
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: i18n.__( 'Weather Settings', 'weatherly-widgets' ), initialOpen: true },
                        el( TextControl, {
                            label: i18n.__( 'City', 'weatherly-widgets' ),
                            value: attrs.city,
                            placeholder: 'Houston',
                            onChange: function( val ) { props.setAttributes( { city: val } ); },
                        } ),
                        el( TextControl, {
                            label: i18n.__( 'State / Province Code', 'weatherly-widgets' ),
                            value: attrs.state,
                            placeholder: 'TX',
                            maxLength: 2,
                            onChange: function( val ) { props.setAttributes( { state: val.toUpperCase() } ); },
                        } ),
                        el( SelectControl, {
                            label: i18n.__( 'Display Format', 'weatherly-widgets' ),
                            value: attrs.format,
                            options: formatOptions,
                            onChange: function( val ) { props.setAttributes( { format: val } ); },
                        } )
                    )
                ),
                // Block preview in editor
                el(
                    'div',
                    useBlockProps( { className: 'weatherly-block-preview' } ),
                    el(
                        'div',
                        {
                            style: {
                                border: '1px solid #e5e7eb',
                                borderRadius: '8px',
                                padding: '16px',
                                background: '#f9fafb',
                                display: 'flex',
                                alignItems: 'center',
                                gap: '12px',
                                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            },
                        },
                        el( 'span', { className: 'dashicons dashicons-cloud', style: { fontSize: '32px', color: '#2563eb' } } ),
                        el(
                            'div',
                            null,
                            el( 'strong', { style: { fontSize: '14px' } },
                                ( attrs.city && attrs.state )
                                    ? attrs.city + ', ' + attrs.state + ' Weather'
                                    : i18n.__( 'Weatherly Weather Widget', 'weatherly-widgets' )
                            ),
                            el( 'div', { style: { fontSize: '12px', color: '#6b7280', marginTop: '2px' } },
                                ( attrs.city && attrs.state )
                                    ? 'Format: ' + attrs.format
                                    : i18n.__( 'Set city and state in the block settings →', 'weatherly-widgets' )
                            )
                        )
                    )
                )
            );
        },

        // Dynamic block — rendered server-side via PHP
        save: function() {
            return null;
        },
    } );
} )(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.i18n
);
