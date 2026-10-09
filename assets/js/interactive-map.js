/**
 * Tools Adapter - Widget Carte Interactive des Zones d'Intervention
 * Gestionnaire Leaflet, Rayons concentriques, Marqueurs personnalisés & Filtres
 */

(function ($) {
  'use strict';

  /* ------------------------------------------------------------------
     Fournisseurs de tuiles 100% GRATUITS et SANS CLÉ API
     ------------------------------------------------------------------ */
  var tileProviders = {
    /* OpenStreetMap Standard — fiable, sans inscription */
    osm_standard: {
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      subdomains: 'abc',
      attr: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19
    },
    /* OpenStreetMap HOT (coloration humanitaire chaude) */
    osm_hot: {
      url: 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
      subdomains: 'abc',
      attr: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles style by <a href="https://www.hotosm.org" target="_blank">HOT</a>',
      maxZoom: 19
    },
    /* ESRI World Light Gray — épuré, professionnel (sans clé) */
    stadia_smooth: {
      url: 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
      subdomains: null,
      attr: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
      maxZoom: 16
    },
    /* ESRI World Street Map — coloré et détaillé (sans clé) */
    stadia_bright: {
      url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
      subdomains: null,
      attr: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012',
      maxZoom: 20
    },
    /* ESRI World Imagery — fond satellite (sans clé) */
    stadia_dark: {
      url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
      subdomains: null,
      attr: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
      maxZoom: 20
    },
    /* ESRI World Topo Map — relief & topographie (sans clé) */
    stadia_outdoors: {
      url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
      subdomains: null,
      attr: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community',
      maxZoom: 20
    }
  };

  /* ------------------------------------------------------------------
     Utilitaire : appliquer la couleur personnalisée sur un marqueur
     ------------------------------------------------------------------ */
  function applyMarkerColor($iconElement, color) {
    if (color) {
      $iconElement.find('.marker-pin').css('background-color', color);
      $iconElement.find('.marker-pulse').css('background-color', hexToRgba(color, 0.45));
    }
  }

  function hexToRgba(hex, alpha) {
    var r = 0, g = 0, b = 0;
    if (!hex) return 'rgba(0,119,182,' + alpha + ')';
    hex = hex.replace('#', '');
    if (hex.length === 3) {
      r = parseInt(hex[0] + hex[0], 16);
      g = parseInt(hex[1] + hex[1], 16);
      b = parseInt(hex[2] + hex[2], 16);
    } else {
      r = parseInt(hex.substring(0, 2), 16);
      g = parseInt(hex.substring(2, 4), 16);
      b = parseInt(hex.substring(4, 6), 16);
    }
    return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
  }

  /* ------------------------------------------------------------------
     Gestionnaire principal du widget
     ------------------------------------------------------------------ */
  var InteractiveMapHandler = function ($scope, $) {
    var $wrapper = $scope.find('.tools-adapter-map-wrapper');
    if (!$wrapper.length) {
      $wrapper = $scope;
    }

    var $canvas = $wrapper.find('.ta-map-canvas');
    if (!$canvas.length || typeof L === 'undefined') {
      return;
    }

    var canvasId = $canvas.attr('id');
    var rawConfig = $wrapper.attr('data-map-config');
    if (!rawConfig) {
      return;
    }

    var config = {};
    try {
      config = JSON.parse(rawConfig);
    } catch (e) {
      console.error('Invalid Tools Adapter Map Config JSON:', e);
      return;
    }

    /* --- Centrage et zoom --- */
    var centerLat  = parseFloat(config.center_lat)  || 48.715;
    var centerLng  = parseFloat(config.center_lng)  || 7.735;
    var defaultZoom = parseInt(config.default_zoom, 10) || 10;
    if (window.innerWidth < 768 && defaultZoom > 9) {
      defaultZoom = defaultZoom - 1;
    }

    /* --- Instanciation de la carte --- */
    var map = L.map(canvasId, {
      center: [centerLat, centerLng],
      zoom: defaultZoom,
      scrollWheelZoom: config.scroll_wheel_zoom === 'yes',
      dragging: config.dragging !== 'no'
    });

    /* --- Fournisseur de tuiles (OSM par défaut, aucune clé requise) --- */
    var themeKey = config.tile_theme || 'osm_standard';
    var provider = tileProviders[themeKey] || tileProviders.osm_standard;

    var tileOptions = {
      attribution: provider.attr,
      maxZoom: provider.maxZoom || 19
    };
    if (provider.subdomains) {
      tileOptions.subdomains = provider.subdomains;
    }

    L.tileLayer(provider.url, tileOptions).addTo(map);

    /* --- Zones d'intervention concentriques (Cercles) --- */
    if (config.zones && Array.isArray(config.zones)) {
      config.zones.forEach(function (zone) {
        if (!zone.enabled) return;

        var radiusMeters   = (parseFloat(zone.radius_km) || 20) * 1000;
        var borderColor    = zone.border_color || '#0077b6';
        var fillColor      = zone.fill_color   || borderColor;
        var fillOpacity    = parseFloat(zone.fill_opacity) || 0.12;
        var borderWeight   = parseInt(zone.border_weight, 10) || 2;

        var circleOptions = {
          radius: radiusMeters,
          color: borderColor,
          weight: borderWeight,
          fillColor: fillColor,
          fillOpacity: fillOpacity
        };

        if (zone.border_style === 'dashed') {
          circleOptions.dashArray = '6, 8';
        }

        var circleCenter = [centerLat, centerLng];
        if (zone.center_lat && zone.center_lng) {
          circleCenter = [parseFloat(zone.center_lat), parseFloat(zone.center_lng)];
        }

        L.circle(circleCenter, circleOptions).addTo(map);
      });
    }

    /* --- Marqueurs & Popups --- */
    var markersMap = {};
    if (config.locations && Array.isArray(config.locations)) {
      config.locations.forEach(function (loc) {
        if (!loc.lat || !loc.lng) return;

        var locLat   = parseFloat(loc.lat);
        var locLng   = parseFloat(loc.lng);
        var locType  = loc.type || 'city';
        var hasPulse = loc.has_pulse === 'yes';
        var pinColor = loc.color || null; // couleur personnalisée depuis Elementor

        /* Icône FA */
        // Elementor embarque Font Awesome 5 : préfixes « fas / far / fab ».
        var iconClass = loc.icon ? loc.icon : 'fas fa-map-marker-alt';
        if (!/(^|\s)(fas|far|fab|fa-solid|fa-regular|fa-brands)(\s|$)/.test(iconClass)) {
          iconClass = 'fas ' + iconClass;
        }

        /* Couleur inline sur le pin */
        var pinStyle = pinColor ? ' style="background-color:' + pinColor + ';"' : '';
        /* Couleur inline sur le pulse */
        var pulseStyle = (hasPulse && pinColor)
          ? ' style="background-color:' + hexToRgba(pinColor, 0.4) + ';"'
          : '';

        var customIcon = L.divIcon({
          className: 'ta-custom-map-marker type-' + locType + (hasPulse ? ' has-pulse' : ''),
          html: '<div class="marker-pin"' + pinStyle + '><i class="' + iconClass + '"></i></div>' +
                (hasPulse ? '<div class="marker-pulse"' + pulseStyle + '></div>' : ''),
          iconSize: [36, 36],
          iconAnchor: [18, 18],
          popupAnchor: [0, -20]
        });

        /* Contenu du Popup */
        var badgeBg = pinColor ? 'background:' + hexToRgba(pinColor, 0.12) + ';color:' + (pinColor || '#0077b6') + ';' : '';
        var popupHtml = '<div class="ta-map-popup-card">';
        if (loc.badge) {
          popupHtml += '<span class="ta-map-popup-badge type-' + locType + '"' +
                       (badgeBg ? ' style="' + badgeBg + '"' : '') + '>' + loc.badge + '</span>';
        }
        if (loc.title) {
          popupHtml += '<div class="ta-map-popup-title">' + loc.title + '</div>';
        }
        if (loc.address) {
          popupHtml += '<div class="ta-map-popup-address"><i class="fas fa-map-pin"></i> ' + loc.address + '</div>';
        }
        if (loc.meta_text) {
          var metaColor = pinColor ? 'color:' + pinColor + ';' : '';
          popupHtml += '<div class="ta-map-popup-meta"' + (metaColor ? ' style="' + metaColor + '"' : '') + '>' + loc.meta_text + '</div>';
        }
        if (loc.btn_text && loc.btn_url) {
          var btnBg = pinColor ? 'background-color:' + pinColor + ';' : '';
          popupHtml += '<a href="' + loc.btn_url + '" class="ta-map-popup-btn"' +
                       (btnBg ? ' style="' + btnBg + '"' : '') + '>' + loc.btn_text + '</a>';
        }
        popupHtml += '</div>';

        var marker = L.marker([locLat, locLng], { icon: customIcon }).addTo(map).bindPopup(popupHtml, {
          maxWidth: 280
        });

        if (loc.slug) {
          markersMap[loc.slug] = {
            marker: marker,
            coords: [locLat, locLng]
          };
        }
      });
    }

    /* --- Interaction Pilules de Communes --- */
    var $pills = $wrapper.find('.ta-map-pill');
    $pills.on('click', function (e) {
      e.preventDefault();
      var $this   = $(this);
      var cityKey = $this.attr('data-city');

      $pills.removeClass('active');
      $this.addClass('active');

      if (cityKey === 'all') {
        map.flyTo([centerLat, centerLng], defaultZoom, { duration: 1.2 });
      } else if (markersMap[cityKey]) {
        var item = markersMap[cityKey];
        map.flyTo(item.coords, 13, { duration: 1.2 });
        setTimeout(function () {
          item.marker.openPopup();
        }, 600);
      }
    });

    /* --- Vérificateur d'Éligibilité --- */
    var $checkerInput  = $wrapper.find('.ta-map-checker-input');
    var $checkerBtn    = $wrapper.find('.ta-map-checker-btn');
    var $checkerResult = $wrapper.find('.ta-map-checker-result');

    var runEligibilityCheck = function () {
      if (!$checkerInput.length || !$checkerResult.length) return;
      var query = $checkerInput.val().trim().toLowerCase();
      if (!query) return;

      var allowedZip    = (config.checker_allowed_zip    || '67').split(',').map(function (s) { return s.trim().toLowerCase(); });
      var allowedCities = (config.checker_allowed_cities || '').split(',').map(function (s) { return s.trim().toLowerCase(); });

      var isZipMatch = allowedZip.some(function (zip) {
        return query.replace(/\s/g, '').startsWith(zip) || query === zip;
      });
      var isCityMatch = allowedCities.some(function (city) {
        return city && (query.indexOf(city) !== -1 || city.indexOf(query) !== -1);
      });

      if (isZipMatch || isCityMatch) {
        var successMsg = config.checker_success_msg || 'Parfait ! Votre commune est 100% couverte par nos équipes. Déplacement et devis offerts.';
        $checkerResult
          .removeClass('extended error')
          .addClass('success')
          .html('<i class="fas fa-check-circle"></i><div><strong>Secteur Couvert !</strong><br><span>' + successMsg + '</span></div>')
          .show();

        var matchedKey = Object.keys(markersMap).find(function (key) {
          return query.indexOf(key) !== -1 || key.indexOf(query) !== -1;
        });
        if (matchedKey) {
          $pills.removeClass('active');
          $wrapper.find('.ta-map-pill[data-city="' + matchedKey + '"]').addClass('active');
          map.flyTo(markersMap[matchedKey].coords, 13, { duration: 1.2 });
          setTimeout(function () { markersMap[matchedKey].marker.openPopup(); }, 600);
        }
      } else {
        var extendedMsg = config.checker_extended_msg || 'Nous étudions toute demande en dehors de notre rayon habituel. Contactez-nous pour vérifier votre secteur.';
        $checkerResult
          .removeClass('success error')
          .addClass('extended')
          .html('<i class="fas fa-info-circle"></i><div><strong>Zone périphérique</strong><br><span>' + extendedMsg + '</span></div>')
          .show();
      }
    };

    if ($checkerBtn.length)   { $checkerBtn.on('click', runEligibilityCheck); }
    if ($checkerInput.length) {
      $checkerInput.on('keypress', function (e) {
        if (e.which === 13) { e.preventDefault(); runEligibilityCheck(); }
      });
    }

    /* --- Invalidation taille (tabs Elementor / resize) --- */
    setTimeout(function () { map.invalidateSize(); }, 300);
    $(window).on('resize', function () { map.invalidateSize(); });
  };

  /* ------------------------------------------------------------------
     Enregistrement Elementor Frontend Hook
     ------------------------------------------------------------------ */
  $(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction(
      'frontend/element_ready/tools-adapter-interactive-map.default',
      InteractiveMapHandler
    );
  });

  /* Fallback Vanilla / démo HTML statique */
  $(document).ready(function () {
    $('.tools-adapter-map-wrapper').each(function () {
      var $el = $(this);
      if (!$el.closest('.elementor-widget-tools-adapter-interactive-map').length) {
        InteractiveMapHandler($el, $);
      }
    });
  });

})(jQuery);
