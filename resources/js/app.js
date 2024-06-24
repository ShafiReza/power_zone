import './bootstrap';
$(document).ready(function() {
    $('#sortable-list').sortable(); // Example initialization
});
$(document).ready(function() {
    // Initialize Vector Map
    $('#world-map').vectorMap({
        map: 'usa_en',
        backgroundColor: '#ffffff',
        borderColor: '#f2f2f2',
        borderOpacity: 0.8,
        borderWidth: 1,
        color: '#e6e6e6',
        enableZoom: true,
        hoverColor: '#f5821f',
        hoverOpacity: null,
        normalizeFunction: 'linear',
        scaleColors: ['#b6d6ff', '#005ace'],
        selectedColor: '#f5821f',
        selectedRegions: null,
        showTooltip: true,
        onRegionClick: function(element, code, region) {
            // Handle region click event
        }
    });

    // Initialize Sparkline
    $('#sparkline-chart').sparkline([5, 8, 3, 7, 9, 4, 6], {
        type: 'line',
        width: '100%',
        height: '150',
        lineWidth: '2',
        lineColor: '#f5821f',
        fillColor: 'rgba(245, 130, 31, 0.2)',
        spotColor: '#f5821f',
        minSpotColor: '#f5821f',
        maxSpotColor: '#f5821f',
        highlightLineColor: '#f5821f',
        highlightSpotColor: '#f5821f'
    });
});
