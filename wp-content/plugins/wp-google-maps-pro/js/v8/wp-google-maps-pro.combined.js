
// js/v8/core.js
/**
 * @module WPGMZA
 * @summary This is the core Javascript module. Some code exists in ../core.js, the functionality there will slowly be handed over to this module.
 */
jQuery(function($) {
	
	var core = {
		MARKER_PULL_DATABASE:	"0",
		MARKER_PULL_XML:		"1",
		
		PAGE_MAP_LIST: 			"map-list",
		PAGE_MAP_EDIT:			"map-edit",
		PAGE_SETTINGS:			"map-settings",
		PAGE_SUPPORT:			"map-support",
		
		PAGE_CATEGORIES:		"categories",
		PAGE_ADVANCED:			"advanced",
		PAGE_CUSTOM_FIELDS:		"custom-fields",
		
		/**
		 * Indexed array of map instances
		 * @constant {array} maps
		 * @static
		 */
		maps: [],
		
		/**
		 * Global EventDispatcher used to listen for global plugin events
		 * @constant {EventDispatcher} events
		 * @static
		 */
		events: null,
		
		/**
		 * Settings, passed from the server
		 * @constant {object} settings
		 * @static
		 */
		settings: null,
		
		/**
		 * Instance of the restAPI. Not to be confused with WPGMZA.RestAPI, which is the instances constructor
		 * @constant {RestAPI} restAPI
		 * @static
		 */
		restAPI: null,
		
		/**
		 * Key and value pairs of localized strings passed from the server
		 * @constant {object} localized_strings
		 * @static
		 */
		localized_strings: null,
		
		loadingHTML: '<div class="wpgmza-preloader"><div class="wpgmza-loader">...</div></div>',
		
		getCurrentPage: function() {
			
			switch(WPGMZA.getQueryParamValue("page"))
			{
				case "wp-google-maps-menu":
					if(window.location.href.match(/action=edit/) && window.location.href.match(/map_id=\d+/))
						return WPGMZA.PAGE_MAP_EDIT;
				
					return WPGMZA.PAGE_MAP_LIST;
					break;
					
				case 'wp-google-maps-menu-settings':
					return WPGMZA.PAGE_SETTINGS;
					break;
					
				case 'wp-google-maps-menu-support':
					return WPGMZA.PAGE_SUPPORT;
					break;
					
				case 'wp-google-maps-menu-categories':
					return WPGMZA.PAGE_CATEGORIES;
					break;
					
				case 'wp-google-maps-menu-advanced':
					return WPGMZA.PAGE_ADVANCED;
					break;
					
				case 'wp-google-maps-menu-custom-fields':
					return WPGMZA.PAGE_CUSTOM_FIELDS;
					break;
					
				default:
					return null;
					break;
			}
			
		},
		
		/**
		 * Override this method to add a scroll offset when using animated scroll, useful for sites with fixed headers.
		 * @method getScrollAnimationOffset
		 * @static
		 * @return {number} The scroll offset
		 */
		getScrollAnimationOffset: function() {
			return (WPGMZA.settings.scroll_animation_offset || 0) + $("#wpadminbar").height();
		},
		
		getScrollAnimationDuration: function() {
			if(WPGMZA.settings.scroll_animation_milliseconds)
				return WPGMZA.settings.scroll_animation_milliseconds;
			else
				return 500;
		},
		
		/**
		 * Animated scroll, accounts for animation settings and fixed header height
		 * @method animateScroll
		 * @static
		 * @param {HTMLElement} element The element to scroll to
		 * @param {number} [milliseconds] The time in milliseconds to scroll over. Defaults to 500 if no value is specified.
		 * @return void
		 */
		animateScroll: function(element, milliseconds) {
			
			var offset = WPGMZA.getScrollAnimationOffset();
			
			if(!milliseconds)
				milliseconds = WPGMZA.getScrollAnimationDuration();
			
			$("html, body").animate({
				scrollTop: $(element).offset().top - offset
			}, milliseconds);
			
		},
		
		extend: function(child, parent) {
			
			var constructor = child;
			
			child.prototype = Object.create(parent.prototype);
			child.prototype.constructor = constructor;
			
		},
		
		/**
		 * Generates and returns a GUID
		 * @method guid
		 * @static
		 * @return {string} The GUID
		 */
		guid: function() { // Public Domain/MIT
		  var d = new Date().getTime();
			if (typeof performance !== 'undefined' && typeof performance.now === 'function'){
				d += performance.now(); //use high-precision timer if available
			}
			return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
				var r = (d + Math.random() * 16) % 16 | 0;
				d = Math.floor(d / 16);
				return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
			});
		},
		
		/**
		 * Takes a hex string and opacity value and converts it to Openlayers RGBA format
		 * @method hexOpacityToRGBA
		 * @static
		 * @param {string} colour The hex color string
		 * @param {number} opacity The opacity from 0.0 - 1.0
		 * @return {array} RGBA array where color components are 0 - 255 and opacity is 0.0 - 1.0
		 */
		hexOpacityToRGBA: function(colour, opacity)
		{
			var hex = parseInt(colour.replace(/^#/, ""), 16);
			return [
				(hex & 0xFF0000) >> 16,
				(hex & 0xFF00) >> 8,
				hex & 0xFF,
				parseFloat(opacity)
			];
		},
		
		/**
		 * Takes a hex color string and converts it to an RGBA object.
		 * @method hexToRgba
		 * @static
		 * @param {string} hex The hex color string
		 * @return {object} Object with r, g, b and a properties, or 0 if the input is invalid.
		 */
		hexToRgba: function(hex) {
			var c;
			if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
				c= hex.substring(1).split('');
				if(c.length== 3){
					c= [c[0], c[0], c[1], c[1], c[2], c[2]];
				}
				c= '0x'+c.join('');
				
				return {
					r: (c>>16)&255,
					g: (c>>8)&255,
					b: c&255,
					a: 1
				};
			}
			
			return 0;
			
			//throw new Error('Bad Hex');
		},
		
		/**
		 * Takes an object with r, g, b and a properties and returns a CSS rgba color string
		 * @method rgbaToString
		 * @static
		 * @param {string} rgba The input object
		 * @return {string} The CSS rgba color string
		 */
		rgbaToString: function(rgba) {
			return "rgba(" + rgba.r + ", " + rgba.g + ", " + rgba.b + ", " + rgba.a + ")";
		},
		
		/**
		 * A regular expression that matches a latitude / longitude coordinate pair
		 * @constant {RegExp} latLngRegexp
		 * @static
		 */
		latLngRegexp: /^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/,
		
		/**
		 * Utility function returns true is string is a latitude and longitude
		 * @method isLatLngString
		 * @deprecated Moved to WPGMZA.LatLng.isLatLngString
		 * @static
		 * @param str {string} The string to attempt to parse as coordinates
		 * @return {array} the matched latitude and longitude or null if no match
		 */
		isLatLngString: function(str)
		{
			if(typeof str != "string")
				return null;
			
			// Remove outer brackets
			if(str.match(/^\(.+\)$/))
				str = str.replace(/^\(|\)$/, "");
			
			var m = str.match(WPGMZA.latLngRegexp);
			
			if(!m)
				return null;
			
			return new WPGMZA.LatLng({
				lat: parseFloat(m[1]),
				lng: parseFloat(m[3])
			});
		},
		
		/**
		 * Utility function returns a latLng literal given a valid latLng string
		 * @method stringToLatLng
		 * @static
		 * @param str {string} The string to attempt to parse as coordinates
		 * @return {object} LatLng literal
		 */
		stringToLatLng: function(str)
		{
			var result = WPGMZA.isLatLngString(str);
			
			if(!result)
				throw new Error("Not a valid latLng");
			
			return result;
		},
		
		/**
		 * Utility function returns a latLng literal given a valid latLng string
		 * @method stringToLatLng
		 * @static
		 * @param str {string} The string to attempt to parse as coordinates
		 * @return {object} LatLng literal
		 */
		isHexColorString: function(str)
		{
			if(typeof str != "string")
				return false;
			
			return (str.match(/#[0-9A-F]{6}/i) ? true : false);
		},
		
		/**
		 * Cache of image dimensions by URL, for internal use only
		 * @var imageDimensionsCache
		 * @inner
		 * @see WPGMZA.getImageDimensions
		 */
		imageDimensionsCache: {},
		
		/**
		 * Utility function to get the dimensions of an image, caches results for best performance
		 * @method getImageDimensions
		 * @static
		 * @param src {string} Image source URL
		 * @param callback {function} Callback to recieve image dimensions
		 * @return {void}
		 */
		getImageDimensions: function(src, callback)
		{
			if(WPGMZA.imageDimensionsCache[src])
			{
				callback(WPGMZA.imageDimensionsCache[src]);
				return;
			}
			
			var img = document.createElement("img");
			img.onload = function(event) {
				var result = {
					width: img.width,
					height: img.height
				};
				WPGMZA.imageDimensionsCache[src] = result;
				callback(result);
			};
			img.src = src;
		},
		
		decodeEntities: function(input)
		{
			return input.replace(/&(nbsp|amp|quot|lt|gt);/g, function(m, e) {
				return m[e];
			}).replace(/&#(\d+);/gi, function(m, e) {
				return String.fromCharCode(parseInt(e, 10));
			});
		},
		
		/**
		 * Returns true if developer mode is set or if developer mode cookie is set
		 * @method isDeveloperMode
		 * @static
		 * @return {boolean} True if developer mode is on
		 */
		isDeveloperMode: function()
		{
			return this.settings.developer_mode || (window.Cookies && window.Cookies.get("wpgmza-developer-mode"));
		},
		
		/**
		 * Returns true if the Pro add-on is active
		 * @method isProVersion
		 * @static
		 * @return {boolean} True if the Pro add-on is active
		 */
		isProVersion: function()
		{
			return (this._isProVersion == "1");
		},
		
		/**
		 * Opens the WP media dialog and returns the result to a callback
		 * @method openMediaDialog
		 * @param {function} callback Callback to recieve the attachment ID as the first parameter and URL as the second
		 * @static
		 * @return {void}
		 */
		openMediaDialog: function(callback) {
			// Media upload
			var file_frame;
			
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				// Set the post ID to what we want
				file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
				// Open frame
				file_frame.open();
				return;
			}
			
			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select a image to upload',
				button: {
					text: 'Use this image',
				},
				multiple: false	// Set to true to allow multiple files to be selected
			});
			
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();
				
				callback(attachment.id, attachment.url);
			});
			
			// Finally, open the modal
			file_frame.open();
		},
		
		/**
		 * @function getCurrentPosition
		 * @summary This function will get the users position, it first attempts to get
		 * high accuracy position (mobile with GPS sensors etc.), if that fails
		 * (desktops will time out) then it tries again without high accuracy
		 * enabled
		 * @static
		 * @return {object} The users position as a LatLng literal
		 */
		getCurrentPosition: function(callback, error, watch)
		{
			var trigger = "userlocationfound";
			var nativeFunction = "getCurrentPosition";
			
			if(WPGMZA.userLocationDenied)
			{
				// NB: This code can also be reached on non https:// sites, the error code is the same
				if(error)
					error({code: 1, message: "Location unavailable"});
				
				return; // NB: The user has declined to share location. Only ask once per session.
			}
			
			if(watch)
			{
				trigger = "userlocationupdated";
				nativeFunction = "watchPosition";
				
				// Call again immediatly to get current position, watchPosition won't fire until the user moves
				/*setTimeout(function() {
					WPGMZA.getCurrentPosition(callback, false);
				}, 0);*/
			}
			
			if(!navigator.geolocation)
			{
				console.warn("No geolocation available on this device");
				return;
			}
			
			var options = {
				enableHighAccuracy: true
			};
			
			if(!navigator.geolocation[nativeFunction])
			{
				console.warn(nativeFunction + " is not available");
				return;
			}
			
			navigator.geolocation[nativeFunction](function(position) {
				if(callback)
					callback(position);
				
				WPGMZA.events.trigger("userlocationfound");
			},
			function(err) {
				
				options.enableHighAccuracy = false;
				
				navigator.geolocation[nativeFunction](function(position) {
					if(callback)
						callback(position);
					
					WPGMZA.events.trigger("userlocationfound");
				},
				function(err) {
					console.warn(err.code, err.message);
					
					if(err.code == 1)
						WPGMZA.userLocationDenied = true;
					
					if(error)
						error(err);
				},
				options);
				
			},
			options);
		},
		
		watchPosition: function(callback, error)
		{
			return WPGMZA.getCurrentPosition(callback, error, true);
		},
		
		/**
		 * Runs a catchable task and displays a friendly error if the function throws an error
		 * @method runCatchableTask
		 * @static
		 * @param {function} callback The function to run
		 * @param {HTMLElement} friendlyErrorContainer The container element to hold the error
		 * @return {void}
		 * @see WPGMZA.FriendlyError
		 */
		runCatchableTask: function(callback, friendlyErrorContainer) {
			
			if(WPGMZA.isDeveloperMode())
				callback();
			else
				try{
					callback();
				}catch(e) {
					var friendlyError = new WPGMZA.FriendlyError(e);
					$(friendlyErrorContainer).html("");
					$(friendlyErrorContainer).append(friendlyError.element);
					$(friendlyErrorContainer).show();
				}
		},
		
		/**
		 * This function is for checking inheritence has been setup correctly. For objects that have engine and Pro specific classes, it will automatically add the engine and pro prefix to the supplied string and if such an object exists it will test against that name rather than the un-prefix argument supplied.
		 *
		 * For example, if we are running the Pro addon with Google maps as the engine, if you supply Marker as the instance name the function will check to see if instance is an instance of GoogleProMarker
		 * @method assertInstanceOf
		 * @static
		 * @param {object} instance The object to check
		 * @param {string} instanceName The class name as a string which this object should be an instance of
		 * @return {void}
		 */
		assertInstanceOf: function(instance, instanceName) {
			var engine, fullInstanceName, assert;
			var pro = WPGMZA.isProVersion() ? "Pro" : "";
			
			switch(WPGMZA.settings.engine)
			{
				case "open-layers":
					engine = "OL";
					break;
				
				default:
					engine = "Google";
					break;
			}
			
			if(WPGMZA[engine + pro + instanceName])
				fullInstanceName = engine + pro + instanceName;
			else if(WPGMZA[pro + instanceName])
				fullInstanceName = pro + instanceName;
			else if(WPGMZA[engine + instanceName])
				fullInstanceName = engine + instanceName;
			else
				fullInstanceName = instanceName;
			
			assert = instance instanceof WPGMZA[fullInstanceName];
			
			if(!assert)
				throw new Error("Object must be an instance of " + fullInstanceName + " (did you call a constructor directly, rather than createInstance?)");
		},
		
		/**
		 * @method getMapByID
		 * @static
		 * @param {mixed} id The ID of the map to retrieve
		 * @return {object} The map object, or null if no such map exists
		 */
		getMapByID: function(id) {
			
			// Workaround for map ID member not set correctly
			
			if(WPGMZA.isProVersion() && !(MYMAP.map instanceof WPGMZA.Map))
				return MYMAP[id].map;
			
			return MYMAP.map;
			
			/*for(var i = 0; i < WPGMZA.maps.length; i++) {
				if(WPGMZA.maps[i].id == id)
					return WPGMZA.maps[i];
			}
			
			return null;*/
		},
		
		/**
		 * Shorthand function to determine if the Places Autocomplete is available
		 * @method isGoogleAutocompleteSupported
		 * @static
		 * @return {boolean} True if the places autocomplete is available
		 */
		isGoogleAutocompleteSupported: function() {
			return typeof google === 'object' && typeof google.maps === 'object' && typeof google.maps.places === 'object' && typeof google.maps.places.Autocomplete === 'function';
		},
		
		/**
		 * The Google API status script enqueue, as reported by the server
		 * @constant
		 * @static
		 */
		googleAPIStatus: window.wpgmza_google_api_status,
		
		/**
		 * Makes an educated guess as to whether the browser is Safari
		 * @method isSafari
		 * @static
		 * @return {boolean} True if it's likely the browser is Safari
		 */
		isSafari: function() {
			
			var ua = navigator.userAgent.toLowerCase();
			return (ua.match(/safari/i) && !ua.match(/chrome/i));
			
		},
		
		/**
		 * Makes an educated guess as to whether the browser is running on a touch device
		 * @method isTouchDevice
		 * @static
		 * @return {boolean} True if it's likely the browser is running on a touch device
		 */
		isTouchDevice: function() {
			
			return ("ontouchstart" in window);
			
		},
		
		/**
		 * Makes an educated guess whether the browser is running on an iOS device
		 * @method isDeviceiOS
		 * @static
		 * @return {boolean} True if it's likely the browser is running on an iOS device
		 */
		isDeviceiOS: function() {
			
			return (
			
				(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream)
				
				||
				
				(!!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform))
			
			);
			
		},
		
		/**
		 * This function prevents modern style components being used with new UI styles (8.0.0)
		 * @method isModernComponentStyleAllowed
		 * @static
		 * @return {boolean} True if modern or legacy style is selected, or no UI style is selected
		 */
		isModernComponentStyleAllowed: function() {
			
			return (!WPGMZA.settings.user_interface_style || WPGMZA.settings.user_interface_style == "legacy" || WPGMZA.settings.user_interface_style == "modern");
			
		},
		
		isElementInView: function(element) {
			
			var pageTop = $(window).scrollTop();
			var pageBottom = pageTop + $(window).height();
			var elementTop = $(element).offset().top;
			var elementBottom = elementTop + $(element).height();

			if(elementTop < pageTop && elementBottom > pageBottom)
				return true;
			
			if(elementTop >= pageTop && elementTop <= pageBottom)
				return true;
			
			if(elementBottom >= pageTop && elementBottom <= pageBottom)
				return true;
			
			return false;
			
		},
		
		isFullScreen: function() {
			
			return isFullScreen;
			
		},
		
		getQueryParamValue: function(name) {
			
			var regex = new RegExp(name + "=([^&#]*)");
			var m;
			
			if(!(m = window.location.href.match(regex)))
				return null;
			
			return m[1];	
		},

		notification: function(text, time) {
			
			switch(arguments.length)
			{
				case 0:
					text = "";
					time = 4000;
					break;
					
				case 1:
					time = 4000;
					break;
			}
			
			var html = '<div class="wpgmza-popup-notification">' + text + '</div>';
			jQuery('body').append(html);
			setTimeout(function(){
				jQuery('body').find('.wpgmza-popup-notification').remove();
			}, time);
			
		}
		
	};
	
	if(window.WPGMZA)
		window.WPGMZA = $.extend(window.WPGMZA, core);
	else
		window.WPGMZA = core;
	
	for(var key in WPGMZA_localized_data)
	{
		var value = WPGMZA_localized_data[key];
		WPGMZA[key] = value;
	}
	
	WPGMZA.settings.useLegacyGlobals = true;
	
	jQuery(function($) {
		
		$(window).trigger("ready.wpgmza");
		
		// Combined script warning
		if($("script[src*='wp-google-maps.combined.js'], script[src*='wp-google-maps-pro.combined.js']").length)
			console.warn("Minified script is out of date, using combined script instead.");
		
		// Check for multiple jQuery versions
		var elements = $("script").filter(function() {
			return this.src.match(/(^|\/)jquery\.(min\.)?js(\?|$)/i);
		});

		if(elements.length > 1)
			console.warn("Multiple jQuery versions detected: ", elements);
		
		// Rest API
		WPGMZA.restAPI = WPGMZA.RestAPI.createInstance();
		
		// TODO: Move to map edit page JS
		$(document).on("click", ".wpgmza_edit_btn", function() {
			
			WPGMZA.animateScroll("#wpgmaps_tabs_markers");
			
		});
		
	});
	
	var isFullScreen = false;
	
	$(document).on("fullscreenchange", function() {
		
		isFullScreen = document.fullscreenElement ? true : false;
		
	});
	
	$(window).on("load", function(event) {
		
		// Array incorrectly extended warning
		var test = [];
		for(var key in test) {
			console.warn("The Array object has been extended incorrectly by your theme or another plugin. This can cause issues with functionality.");
			break;
		}
		
		// Geolocation warnings
		if(window.location.protocol != 'https:')
		{
			var warning = '<div class="notice notice-warning"><p>' + WPGMZA.localized_strings.unsecure_geolocation + "</p></div>";
			
			$(".wpgmza-geolocation-setting").each(function(index, el) {
				$(el).after( $(warning) );
			});
		}
		
	});
	
	function onScroll(event)
	{
		
		// Test if map is scrolled into view
		$(".wpgmza_map").each(function(index, el) {
			
			var isInView = WPGMZA.isElementInView(el);
			
			if(!el.wpgmzaScrollIntoViewTriggerFlag)
			{
				if(isInView)
				{
					$(el).trigger("mapscrolledintoview.wpgmza");
					el.wpgmzaScrollIntoViewTriggerFlag = true;
				}
			}
			else if(!isInView)
				el.wpgmzaScrollIntoViewTriggerFlag = false;
			
		});
		
	}
	
	$(window).on("scroll", onScroll);
	$(window).on("load", onScroll);
	
	if(WPGMZA.refreshOnLoad)
		window.location.reload();
	
});

// js/v8/compatibility.js
/**
 * @namespace WPGMZA
 * @module Compatibility
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Reverse compatibility module
	 *
	 * @class WPGMZA.Compatibility
	 * @constructor WPGMZA.Compatibility
	 * @memberof WPGMZA
	 */
	WPGMZA.Compatibility = function()
	{
		this.preventDocumentWriteGoogleMapsAPI();
	}
	
	/**
	 * Prevents document.write from outputting Google Maps API script tag
	 *
	 * @method
	 * @memberof WPGMZA.Compatibility
	 */
	WPGMZA.Compatibility.prototype.preventDocumentWriteGoogleMapsAPI = function()
	{
		var old = document.write;
		
		document.write = function(content)
		{
			if(content.match && content.match(/maps\.google/))
				return;
			
			old.call(document, content);
		}
	}
	
	WPGMZA.compatiblityModule = new WPGMZA.Compatibility();
	
});

// js/v8/css-escape.js
/**
 * Polyfill for CSS.escape, with thanks to @mathias
 * @namespace WPGMZA
 * @module CSS
 * @requires WPGMZA
 */

/*! https://mths.be/cssescape v1.5.1 by @mathias | MIT license */
;(function(root, factory) {
	// https://github.com/umdjs/umd/blob/master/returnExports.js
	if (typeof exports == 'object') {
		// For Node.js.
		module.exports = factory(root);
	} else if (typeof define == 'function' && define.amd) {
		// For AMD. Register as an anonymous module.
		define([], factory.bind(root, root));
	} else {
		// For browser globals (not exposing the function separately).
		factory(root);
	}
}(typeof global != 'undefined' ? global : this, function(root) {

	if (root.CSS && root.CSS.escape) {
		return root.CSS.escape;
	}

	// https://drafts.csswg.org/cssom/#serialize-an-identifier
	var cssEscape = function(value) {
		if (arguments.length == 0) {
			throw new TypeError('`CSS.escape` requires an argument.');
		}
		var string = String(value);
		var length = string.length;
		var index = -1;
		var codeUnit;
		var result = '';
		var firstCodeUnit = string.charCodeAt(0);
		while (++index < length) {
			codeUnit = string.charCodeAt(index);
			// Note: there’s no need to special-case astral symbols, surrogate
			// pairs, or lone surrogates.

			// If the character is NULL (U+0000), then the REPLACEMENT CHARACTER
			// (U+FFFD).
			if (codeUnit == 0x0000) {
				result += '\uFFFD';
				continue;
			}

			if (
				// If the character is in the range [\1-\1F] (U+0001 to U+001F) or is
				// U+007F, […]
				(codeUnit >= 0x0001 && codeUnit <= 0x001F) || codeUnit == 0x007F ||
				// If the character is the first character and is in the range [0-9]
				// (U+0030 to U+0039), […]
				(index == 0 && codeUnit >= 0x0030 && codeUnit <= 0x0039) ||
				// If the character is the second character and is in the range [0-9]
				// (U+0030 to U+0039) and the first character is a `-` (U+002D), […]
				(
					index == 1 &&
					codeUnit >= 0x0030 && codeUnit <= 0x0039 &&
					firstCodeUnit == 0x002D
				)
			) {
				// https://drafts.csswg.org/cssom/#escape-a-character-as-code-point
				result += '\\' + codeUnit.toString(16) + ' ';
				continue;
			}

			if (
				// If the character is the first character and is a `-` (U+002D), and
				// there is no second character, […]
				index == 0 &&
				length == 1 &&
				codeUnit == 0x002D
			) {
				result += '\\' + string.charAt(index);
				continue;
			}

			// If the character is not handled by one of the above rules and is
			// greater than or equal to U+0080, is `-` (U+002D) or `_` (U+005F), or
			// is in one of the ranges [0-9] (U+0030 to U+0039), [A-Z] (U+0041 to
			// U+005A), or [a-z] (U+0061 to U+007A), […]
			if (
				codeUnit >= 0x0080 ||
				codeUnit == 0x002D ||
				codeUnit == 0x005F ||
				codeUnit >= 0x0030 && codeUnit <= 0x0039 ||
				codeUnit >= 0x0041 && codeUnit <= 0x005A ||
				codeUnit >= 0x0061 && codeUnit <= 0x007A
			) {
				// the character itself
				result += string.charAt(index);
				continue;
			}

			// Otherwise, the escaped character.
			// https://drafts.csswg.org/cssom/#escape-a-character
			result += '\\' + string.charAt(index);

		}
		return result;
	};

	if (!root.CSS) {
		root.CSS = {};
	}

	root.CSS.escape = cssEscape;
	return cssEscape;

}));

// js/v8/distance.js
/**
 * Collection of distance utility functions and constants
 * @namespace WPGMZA
 * @module Distance
 * @requires WPGMZA
 */
jQuery(function($) {
	
	var earthRadiusMeters = 6371;
	var piTimes360 = Math.PI / 360;
	
	function deg2rad(deg) {
	  return deg * (Math.PI/180)
	};
	
	/**
	 * @class WPGMZA.Distance
	 * @memberof WPGMZA
	 * @deprecated Will be dropped wiht the introduction of global distance units
	 */
	WPGMZA.Distance = {
		
		/**
		 * Miles, represented as true by legacy versions of the plugin
		 * @constant MILES
		 * @static
		 * @memberof WPGMZA.Distance
		 */
		MILES:					true,
		
		/**
		 * Kilometers, represented as false by legacy versions of the plugin
		 * @constant KILOMETERS
		 * @static
		 * @memberof WPGMZA.Distance
		 */
		KILOMETERS:				false,
		
		/**
		 * Miles per kilometer
		 * @constant MILES_PER_KILOMETER
		 * @static
		 * @memberof WPGMZA.Distance
		 */
		MILES_PER_KILOMETER:	0.621371,
		
		/**
		 * Kilometers per mile
		 * @constant KILOMETERS_PER_MILE
		 * @static
		 */
		KILOMETERS_PER_MILE:	1.60934,
		
		// TODO: Implement WPGMZA.settings.distance_units
		
		/**
		 * Converts a UI distance (eg from a form control) to meters,
		 * accounting for the global units setting
		 * @method uiToMeters
		 * @static
		 * @memberof WPGMZA.Distance
		 * @param {number} uiDistance The distance from the UI, could be in miles or kilometers depending on settings
		 * @return {number} The input distance in meters
		 */
		uiToMeters: function(uiDistance)
		{
			return parseFloat(uiDistance) / (WPGMZA.settings.distance_units == WPGMZA.Distance.MILES ? WPGMZA.Distance.MILES_PER_KILOMETER : 1) * 1000;
		},
		
		/**
		 * Converts a UI distance (eg from a form control) to kilometers,
		 * accounting for the global units setting
		 * @method uiToKilometers
		 * @static
		 * @memberof WPGMZA.Distance
		 * @param {number} uiDistance The distance from the UI, could be in miles or kilometers depending on settings
		 * @return {number} The input distance in kilometers
		 */
		uiToKilometers: function(uiDistance)
		{
			return WPGMZA.Distance.uiToMeters(uiDistance) * 0.001;
		},
		
		/**
		 * Converts a UI distance (eg from a form control) to miles, according to settings
		 * @method uiToMiles
		 * @static
		 * @memberof WPGMZA.Distance
		 * @param {number} uiDistance The distance from the UI, could be in miles or kilometers depending on settings
		 * @return {number} The input distance 
		 */
		uiToMiles: function(uiDistance)
		{
			return WPGMZA.Distance.uiToKilometers(uiDistance) * WPGMZA.Distance.MILES_PER_KILOMETER;
		},
		
		/**
		 * Converts kilometers to a UI distance, either the same value, or converted to miles depending on settings.
		 * @method kilometersToUI
		 * @static
		 * @memberof WPGMZA.Distance
		 * @param {number} km The input distance in kilometers
		 * @param {number} The UI distance in the units specified by settings
		 */
		kilometersToUI: function(km)
		{
			if(WPGMZA.settings.distance_units == WPGMZA.Distance.MILES)
				return km * WPGMZA.Distance.MILES_PER_KILOMETER;
			return km;
		},
		
		/**
		 * Returns the distance, in kilometers, between two LatLng's
		 * @method between
		 * @static
		 * @memberof WPGMZA.Distance
		 * @param {WPGMZA.Latlng} The first point
		 * @param {WPGMZA.Latlng} The second point
		 * @return {number} The distance, in kilometers
		 */
		between: function(a, b)
		{
			if(!(a instanceof WPGMZA.LatLng) && !("lat" in a && "lng" in a))
				throw new Error("First argument must be an instance of WPGMZA.LatLng or a literal");
			
			if(!(b instanceof WPGMZA.LatLng) && !("lat" in b && "lng" in b))
				throw new Error("Second argument must be an instance of WPGMZA.LatLng or a literal");
			
			if(a === b)
				return 0.0;
			
			var lat1 = a.lat;
			var lon1 = a.lng;
			var lat2 = b.lat;
			var lon2 = b.lng;
			
			var dLat = deg2rad(lat2 - lat1);
			var dLon = deg2rad(lon2 - lon1); 
			
			var a = 
				Math.sin(dLat/2) * Math.sin(dLat/2) +
				Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
				Math.sin(dLon/2) * Math.sin(dLon/2); 
				
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			var d = earthRadiusMeters * c; // Distance in km
			
			return d;
		}
		
	};
	
});

// js/v8/elias-fano.js
/**
 * @namespace WPGMZA
 * @module EliasFano
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.EliasFano = function()
	{
		if(!WPGMZA.EliasFano.isSupported)
			throw new Error("Elias Fano encoding is not supported on browsers without Uint8Array");
		
		if(!WPGMZA.EliasFano.decodingTablesInitialised)
			WPGMZA.EliasFano.createDecodingTable();
	}
	
	WPGMZA.EliasFano.isSupported = ("Uint8Array" in window);
	
	WPGMZA.EliasFano.decodingTableHighBits			= [];
	WPGMZA.EliasFano.decodingTableDocIDNumber		= null;
	WPGMZA.EliasFano.decodingTableHighBitsCarryover = null;
	
	WPGMZA.EliasFano.createDecodingTable = function()
	{
		WPGMZA.EliasFano.decodingTableDocIDNumber = new Uint8Array(256);
		WPGMZA.EliasFano.decodingTableHighBitsCarryover = new Uint8Array(256);
		
		var decodingTableHighBits = WPGMZA.EliasFano.decodingTableHighBits;
		var decodingTableDocIDNumber = WPGMZA.EliasFano.decodingTableDocIDNumber;
		var decodingTableHighBitsCarryover = WPGMZA.EliasFano.decodingTableHighBitsCarryover;
		
		for(var i = 0; i < 256; i++)
		{
			var zeroCount = 0;
			
			decodingTableHighBits[i] = [];
			
			for(var j = 7; j >= 0; j--)
			{
				if((i & (1 << j)) > 0)
				{
					decodingTableHighBits[i][decodingTableDocIDNumber[i]] = zeroCount;
					
					decodingTableDocIDNumber[i]++;
					zeroCount = 0;
				}
				else
					zeroCount = (zeroCount + 1) % 0xFF;
			}
			
			decodingTableHighBitsCarryover[i] = zeroCount;
		}
		
		WPGMZA.EliasFano.decodingTablesInitialised = true;
	}
	
	WPGMZA.EliasFano.prototype.encode = function(list)
	{
		var lastDocID		= 0,
			buffer1 		= 0,
			bufferLength1 	= 0,
			buffer2 		= 0,
			bufferLength2 	= 0;
		
		if(list.length == 0)
			return result;
		
		function toByte(n)
		{
			return n & 0xFF;
		}
		
		var compressedBufferPointer1 = 0;
		var compressedBufferPointer2 = 0;
		var largestBlockID = list[list.length - 1];
		var averageDelta = largestBlockID / list.length;
		var averageDeltaLog = Math.log2(averageDelta);
		var lowBitsLength = Math.floor(averageDeltaLog);
		var lowBitsMask = (1 << lowBitsLength) - 1;
		var prev = null;
		
		var maxCompressedSize = Math.floor(
			(
				2 + Math.ceil(
					Math.log2(averageDelta)
				)
			) * list.length / 8
		) + 6;
		
		var compressedBuffer = new Uint8Array(maxCompressedSize);
		
		if(lowBitsLength < 0)
			lowBitsLength = 0;
		
		compressedBufferPointer2 = Math.floor(lowBitsLength * list.length / 8 + 6);
		
		compressedBuffer[compressedBufferPointer1++] = toByte( list.length );
		compressedBuffer[compressedBufferPointer1++] = toByte( list.length >> 8 );
		compressedBuffer[compressedBufferPointer1++] = toByte( list.length >> 16 );
		compressedBuffer[compressedBufferPointer1++] = toByte( list.length >> 24 );
		
		compressedBuffer[compressedBufferPointer1++] = toByte( lowBitsLength );
		
		list.forEach(function(docID) {
			
			var docIDDelta = (docID - lastDocID - 1);
			
			if(!$.isNumeric(docID))
				throw new Error("Value is not numeric");
			
			// NB: Force docID to an integer in case it's a string
			docID = parseInt(docID);
			
			if(prev !== null && docID <= prev)
				throw new Error("Elias Fano encoding can only be used on a sorted, ascending list of unique integers.");
			
			prev = docID;
			
			buffer1 <<= lowBitsLength;
			buffer1 |= (docIDDelta & lowBitsMask);
			bufferLength1 += lowBitsLength;
			
			// Flush buffer 1
			while(bufferLength1 > 7)
			{
				bufferLength1 -= 8;
				compressedBuffer[compressedBufferPointer1++] = toByte( buffer1 >> bufferLength1 );
			}
			
			var unaryCodeLength = (docIDDelta >> lowBitsLength) + 1;
			
			buffer2 <<= unaryCodeLength;
			buffer2 |= 1;
			bufferLength2 += unaryCodeLength;
			
			// Flush buffer 2
			while(bufferLength2 > 7)
			{
				bufferLength2 -= 8;
				compressedBuffer[compressedBufferPointer2++] = toByte( buffer2 >> bufferLength2 );
			}
			
			lastDocID = docID;
		});
		
		if(bufferLength1 > 0)
			compressedBuffer[compressedBufferPointer1++] = toByte( buffer1 << (8 - bufferLength1) );
		
		if(bufferLength2 > 0)
			compressedBuffer[compressedBufferPointer2++] = toByte( buffer2 << (8 - bufferLength2) );
		
		var result = new Uint8Array(compressedBuffer);
		
		result.pointer = compressedBufferPointer2;
		
		return result;
	}
	
	WPGMZA.EliasFano.prototype.decode = function(compressedBuffer)
	{
		var resultPointer = 0;
		var list = [];
		
		//console.log("Decoding buffer from pointer " + compressedBuffer.pointer);
		//console.log(compressedBuffer);
		
		var decodingTableHighBits = WPGMZA.EliasFano.decodingTableHighBits;
		var decodingTableDocIDNumber = WPGMZA.EliasFano.decodingTableDocIDNumber;
		var decodingTableHighBitsCarryover = WPGMZA.EliasFano.decodingTableHighBitsCarryover;
		
		var lowBitsPointer = 0,
			lastDocID = 0,
			docID = 0,
			docIDNumber = 0;
		
		var listCount = compressedBuffer[lowBitsPointer++];
		
		//console.log("listCount is now " + listCount);
		
		listCount |= compressedBuffer[lowBitsPointer++] << 8;
		
		//console.log("listCount is now " + listCount);
		
		listCount |= compressedBuffer[lowBitsPointer++] << 16;
		
		//console.log("listCount is now " + listCount);
		
		listCount |= compressedBuffer[lowBitsPointer++] << 24;
		
		//console.log("Read list count " + listCount);
		
		var lowBitsLength = compressedBuffer[lowBitsPointer++];
		
		//console.log("lowBitsLength = " + lowBitsLength);
		
		var highBitsPointer,
			lowBitsCount = 0,
			lowBits = 0,
			cb = 1;
		
		for(
			highBitsPointer = Math.floor(lowBitsLength * listCount / 8 + 6);
			highBitsPointer < compressedBuffer.pointer;
			highBitsPointer++
			)
		{
			docID += decodingTableHighBitsCarryover[cb];
			cb = compressedBuffer[highBitsPointer];
			
			docIDNumber = decodingTableDocIDNumber[cb];
			
			for(var i = 0; i < docIDNumber; i++)
			{
				docID <<= lowBitsCount;
				docID |= lowBits & ((1 << lowBitsCount) - 1);
				
				while(lowBitsCount < lowBitsLength)
				{
					docID <<= 8;
					
					lowBits = compressedBuffer[lowBitsPointer++];
					docID |= lowBits;
					lowBitsCount += 8;
				}
				
				lowBitsCount -= lowBitsLength;
				docID >>= lowBitsCount;
				
				docID += (decodingTableHighBits[cb][i] << lowBitsLength) + lastDocID + 1;
				
				list[resultPointer++] = docID;
				
				lastDocID = docID;
				docID = 0;
			}
		}
		
		return list;
	}
	
});

// js/v8/event-dispatcher.js
/**
 * @namespace WPGMZA
 * @module EventDispatcher
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Base class for any (non HTMLElement) object which dispatches or listens for events
	 * @class WPGMZA.EventDispatcher
	 * @constructor WPGMZA.EventDispatcher
	 * @memberof WPGMZA
	 */
	WPGMZA.EventDispatcher = function()
	{
		WPGMZA.assertInstanceOf(this, "EventDispatcher");
		
		this._listenersByType = {};
	}

	/**
	 * Adds an event listener on this object
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @param {string} type The event type, or multiple types separated by spaces
	 * @param {function} callback The callback to call when the event fires
	 * @param {object} [thisObject] The object to use as "this" when firing the callback
	 * @param {bool} [useCapture] If true, fires the callback on the capture phase, as opposed to bubble phase
	 */
	WPGMZA.EventDispatcher.prototype.addEventListener = function(type, listener, thisObject, useCapture)
	{
		var types = type.split(/\s+/);
		if(types.length > 1)
		{
			for(var i = 0; i < types.length; i++)
				this.addEventListener(types[i], listener, thisObject, useCapture);
			
			return;
		}
		
		if(!(listener instanceof Function))
			throw new Error("Listener must be a function");
	
		var target;
		if(!this._listenersByType.hasOwnProperty(type))
			target = this._listenersByType[type] = [];
		else
			target = this._listenersByType[type];
		
		var obj = {
			listener: listener,
			thisObject: (thisObject ? thisObject : this),
			useCapture: (useCapture ? true : false)
			};
			
		target.push(obj);
	}

	/**
	 * Alias for addEventListener
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @see WPGMZA.EventDispatcher#addEventListener
	 */
	WPGMZA.EventDispatcher.prototype.on = WPGMZA.EventDispatcher.prototype.addEventListener;

	/**
	 * Removes event listeners from this object
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @param {string} type The event type to remove listeners from
	 * @param {function} [listener] The function to remove. If omitted, all listeners will be removed
	 * @param {object} [thisObject] Use the parameter to remove listeners bound with the same thisObject
	 * @param {bool} [useCapture] Remove the capture phase event listener. Otherwise, the bubble phase event listener will be removed.
	 */
	WPGMZA.EventDispatcher.prototype.removeEventListener = function(type, listener, thisObject, useCapture)
	{
		var arr, index, obj;

		if(!(arr = this._listenersByType[type]))
			return;
			
		if(!thisObject)
			thisObject = this;
			
		useCapture = (useCapture ? true : false);
		
		for(var i = 0; i < arr.length; i++)
		{
			obj = arr[i];
		
			if((arguments.length == 1 || obj.listener == listener) && obj.thisObject == thisObject && obj.useCapture == useCapture)
			{
				arr.splice(i, 1);
				return;
			}
		}
	}

	/**
	 * Alias for removeEventListener
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @see WPGMZA.EventDispatcher#removeEventListener
	 */
	WPGMZA.EventDispatcher.prototype.off = WPGMZA.EventDispatcher.prototype.removeEventListener;

	/**
	 * Test for listeners of type on this object
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @param {string} type The event type to test for
	 * @return {bool} True if this object has listeners bound for the specified type
	 */
	WPGMZA.EventDispatcher.prototype.hasEventListener = function(type)
	{
		return (_listenersByType[type] ? true : false);
	}

	/**
	 * Fires an event on this object
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @param {string|WPGMZA.Event} event Either the event type as a string, or an instance of WPGMZA.Event
	 */
	WPGMZA.EventDispatcher.prototype.dispatchEvent = function(event)
	{
		if(!(event instanceof WPGMZA.Event))
		{
			if(typeof event == "string")
				event = new WPGMZA.Event(event);
			else
			{
				var src = event;
				event = new WPGMZA.Event();
				for(var name in src)
					event[name] = src[name];
			}
		}

		event.target = this;
			
		var path = [];
		for(var obj = this.parent; obj != null; obj = obj.parent)
			path.unshift(obj);
		
		event.phase = WPGMZA.Event.CAPTURING_PHASE;
		for(var i = 0; i < path.length && !event._cancelled; i++)
			path[i]._triggerListeners(event);
			
		if(event._cancelled)
			return;
			
		event.phase = WPGMZA.Event.AT_TARGET;
		this._triggerListeners(event);
			
		event.phase = WPGMZA.Event.BUBBLING_PHASE;
		for(i = path.length - 1; i >= 0 && !event._cancelled; i--)
			path[i]._triggerListeners(event);
		
		// Native DOM event
		var topMostElement = this.element;
		for(var obj = this.parent; obj != null; obj = obj.parent)
		{
			if(obj.element)
				topMostElement = obj.element;
		}
		
		if(topMostElement)
		{
			var customEvent = {};
			
			for(var key in event)
			{
				var value = event[key];
				
				if(key == "type")
					value += ".wpgmza";
				
				customEvent[key] = value;
			}
			
			$(topMostElement).trigger(customEvent);
		}
	}

	/**
	 * Alias for removeEventListener
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @see WPGMZA.EventDispatcher#removeEventListener
	 */
	WPGMZA.EventDispatcher.prototype.trigger = WPGMZA.EventDispatcher.prototype.dispatchEvent;

	/**
	 * Handles the logic of triggering listeners
	 * @method
	 * @memberof WPGMZA.EventDispatcher
	 * @inner
	 */
	WPGMZA.EventDispatcher.prototype._triggerListeners = function(event)
	{
		var arr, obj;
		
		if(!(arr = this._listenersByType[event.type]))
			return;
			
		for(var i = 0; i < arr.length; i++)
		{
			obj = arr[i];
			
			if(event.phase == WPGMZA.Event.CAPTURING_PHASE && !obj.useCapture)
				continue;
				
			obj.listener.call(arr[i].thisObject, event);
		}
	}

	WPGMZA.events = new WPGMZA.EventDispatcher();

});

// js/v8/event.js
/**
 * @namespace WPGMZA
 * @module Event
 * @requires WPGMZA
 */ 
jQuery(function($) {
		
	/**
	 * Base class used for events (for non-HTMLElement objects)
	 * @class WPGMZA.Event
	 * @constructor WPGMZA.Event
	 * @memberof WPGMZA
	 * @param {string|object} options The event type as a string, or an object of options to be mapped to this event
	 */
	WPGMZA.Event = function(options)
	{
		if(typeof options == "string")
			this.type = options;
		
		this.bubbles		= true;
		this.cancelable		= true;
		this.phase			= WPGMZA.Event.PHASE_CAPTURE;
		this.target			= null;
		
		this._cancelled = false;
		
		if(typeof options == "object")
			for(var name in options)
				this[name] = options[name];
	}

	WPGMZA.Event.CAPTURING_PHASE		= 0;
	WPGMZA.Event.AT_TARGET				= 1;
	WPGMZA.Event.BUBBLING_PHASE			= 2;

	/**
	 * Prevents any further propagation of this event
	 * @method
	 * @memberof WPGMZA.Event
	 */
	WPGMZA.Event.prototype.stopPropagation = function()
	{
		this._cancelled = true;
	}
	
});

// js/v8/fancy-controls.js
/**
 * @namespace WPGMZA
 * @module FancyControls
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.FancyControls = {
		
		formatToggleSwitch: function(el)
		{
			var div			= $("<div class='switch'></div>");
			var input		= el;
			var container	= el.parentNode;
			var text		= $(container).text().trim();
			var label		= $("<label></label>");
			
			$(input).addClass("cmn-toggle cmn-toggle-round-flat");
			$(input).attr("id", $(input).attr("name"));
			
			$(label).attr("for", $(input).attr("name"));
			
			$(div).append(input);
			$(div).append(label);
			
			$(container).replaceWith(div);
			
			$(div).wrap($("<div></div>"));
			$(div).after(text);
		},
		
		formatToggleButton: function(el)
		{
			var div			= $("<div class='switch'></div>");
			var input		= el;
			var container	= el.parentNode;
			var text		= $(container).text().trim();
			var label		= $("<label></label>");
			
			$(input).addClass("cmn-toggle cmn-toggle-yes-no");
			$(input).attr("id", $(input).attr("name"));
			
			$(label).attr("for", $(input).attr("name"));
			
			$(label).attr("data-on", WPGMZA.localized_strings.yes);
			$(label).attr("data-off", WPGMZA.localized_strings.no);
			
			$(div).append(input);
			$(div).append(label);
			
			$(container).replaceWith(div);
			
			$(div).wrap($("<div></div>"));
			$(div).after(text);
		}
		
	};
	
	$(".wpgmza-fancy-toggle-switch").each(function(index, el) {
		WPGMZA.FancyControls.formatToggleSwitch(el);
	});
	
	$(".wpgmza-fancy-toggle-button").each(function(index, el) {
		WPGMZA.FancyControls.formatToggleButton(el);
	});
	
});

// js/v8/friendly-error.js
/**
 * @namespace WPGMZA
 * @module FriendlyError
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Deprecated
	 * @class WPGMZA.FriendlyError
	 * @constructor WPGMZA.FriendlyError
	 * @memberof WPGMZA
	 * @deprecated
	 */
	WPGMZA.FriendlyError = function()
	{
		
	}
	
	/*var template = '\
		<div class="notice notice-error"> \
			<p> \
			' + WPGMZA.localized_strings.friendly_error + ' \
			</p> \
			<pre style="white-space: pre-line;"></pre> \
		<div> \
		';
	
	WPGMZA.FriendlyError = function(nativeError)
	{
		if(!WPGMZA.is_admin)
		{
			this.element = $(WPGMZA.preloaderHTML);
			$(this.element).removeClass("animated");
			return;
		}
		
		$("#wpgmza-map-edit-page>.wpgmza-preloader").remove();
		
		this.element = $(template);
		this.element.find("pre").html(nativeError.message + "\r\n" + nativeError.stack + "\r\n\r\n on " + window.location.href);
	}*/
	
});

// js/v8/geocoder.js
/**
 * @namespace WPGMZA
 * @module Geocoder
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Base class for geocoders. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.Geocoder
	 * @constructor WPGMZA.Geocoder
	 * @memberof WPGMZA
	 * @see WPGMZA.Geocoder.createInstance
	 */
	WPGMZA.Geocoder = function()
	{
		WPGMZA.assertInstanceOf(this, "Geocoder");
	}
	
	/**
	 * Indicates a successful geocode, with one or more results
	 * @constant SUCCESS
	 * @memberof WPGMZA.Geocoder
	 */
	WPGMZA.Geocoder.SUCCESS			= "success";
	
	/**
	 * Indicates the geocode was successful, but returned no results
	 * @constant ZERO_RESULTS
	 * @memberof WPGMZA.Geocoder
	 */
	WPGMZA.Geocoder.ZERO_RESULTS	= "zero-results";
	
	/**
	 * Indicates the geocode failed, usually due to technical reasons (eg connectivity)
	 * @constant FAIL
	 * @memberof WPGMZA.Geocoder
	 */
	WPGMZA.Geocoder.FAIL			= "fail";
	
	/**
	 * Returns the contructor to be used by createInstance, depending on the selected maps engine.
	 * @method
	 * @memberof WPGMZA.Geocoder
	 * @return {function} The appropriate contructor
	 */
	WPGMZA.Geocoder.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return WPGMZA.OLGeocoder;
				break;
				
			default:
				return WPGMZA.GoogleGeocoder;
				break;
		}
	}
	
	/**
	 * Creates an instance of a Geocoder, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>
	 * @method
	 * @memberof WPGMZA.Geocoder
	 * @return {WPGMZA.Geocoder} A subclass of WPGMZA.Geocoder
	 */
	WPGMZA.Geocoder.createInstance = function()
	{
		var constructor = WPGMZA.Geocoder.getConstructor();
		return new constructor();
	}
	
	/**
	 * Attempts to convert a street address to an array of potential coordinates that match the address, which are passed to a callback. If the address is interpreted as a latitude and longitude coordinate pair, the callback is immediately fired.
	 * @method
	 * @memberof WPGMZA.Geocoder
	 * @param {object} options The options to geocode, address is mandatory.
	 * @param {function} callback The callback to receive the geocode result.
	 * @return {void}
	 */
	WPGMZA.Geocoder.prototype.getLatLngFromAddress = function(options, callback)
	{
		if(WPGMZA.isLatLngString(options.address))
		{
			var parts = options.address.split(/,\s*/);
			var latLng = new WPGMZA.LatLng({
				lat: parseFloat(parts[0]),
				lng: parseFloat(parts[1])
			});
			
			// NB: Quick fix, solves issue with right click marker. Solve this there by making behaviour consistent
			latLng.latLng = latLng;
			
			callback([latLng], WPGMZA.Geocoder.SUCCESS);
		}
	}
	
	/**
	 * Attempts to convert latitude eand longitude coordinates into a street address. By default this will simply return the coordinates wrapped in an array.
	 * @method
	 * @memberof WPGMZA.Geocoder
	 * @param {object} options The options to geocode, latLng is mandatory.
	 * @param {function} callback The callback to receive the geocode result.
	 * @return {void}
	 */
	WPGMZA.Geocoder.prototype.getAddressFromLatLng = function(options, callback)
	{
		var latLng = new WPGMZA.LatLng(options.latLng);
		callback([latLng.toString()], WPGMZA.Geocoder.SUCCESS);
	}
	
	/**
	 * Geocodes either an address or a latitude and longitude coordinate pair, depending on the input
	 * @method
	 * @memberof WPGMZA.Geocoder
	 * @param {object} options The options to geocode, you must supply <em>either</em> latLng <em>or</em> address.
	 * @throws You must supply either a latLng or address
	 * @return {void}
	 */
	WPGMZA.Geocoder.prototype.geocode = function(options, callback)
	{
		if("address" in options)
			return this.getLatLngFromAddress(options, callback);
		else if("latLng" in options)
			return this.getAddressFromLatLng(options, callback);
		
		throw new Error("You must supply either a latLng or address");
	}
	
});

// js/v8/google-api-error-handler.js
/**
 * @namespace WPGMZA
 * @module GoogleAPIErrorHandler
 * @requires WPGMZA
 */
jQuery(function($) { 

	/**
	 * This class catches Google Maps API errors and presents them in a friendly manner, before sending them on to the consoles default error handler.
	 * @class WPGMZA.GoogleAPIErrorHandler
	 * @constructor WPGMZA.GoogleAPIErrorHandler
	 * @memberof WPGMZA
	 */
	WPGMZA.GoogleAPIErrorHandler = function() {
		
		var self = this;
		
		// Don't do anything if Google isn't the selected API
		if(WPGMZA.settings.engine != "google-maps")
			return;
		
		// Only allow on the map edit page, or front end if user has administrator role
		if(!(WPGMZA.currentPage == "map-edit" || (WPGMZA.is_admin == 0 && WPGMZA.userCanAdministrator == 1)))
			return;
		
		this.element = $(WPGMZA.html.googleMapsAPIErrorDialog);
		
		if(WPGMZA.is_admin == 1)
			this.element.find(".wpgmza-front-end-only").remove();
		
		this.errorMessageList = this.element.find(".wpgmza-google-api-error-list");
		this.templateListItem = this.element.find("li.template").remove();
		
		this.messagesAlreadyDisplayed = {};
		
		//if(WPGMZA.settings.developer_mode)
			//return;
		
		// Override error function
		var _error = console.error;
		
		console.error = function(message)
		{
			self.onErrorMessage(message);
			
			_error.apply(this, arguments);
		}
		
		// Check for no API key
		if(
			WPGMZA.settings.engine == "google-maps" 
			&& 
			(!WPGMZA.settings.wpgmza_google_maps_api_key || !WPGMZA.settings.wpgmza_google_maps_api_key.length)
			&&
			WPGMZA.getCurrentPage() != WPGMZA.PAGE_MAP_EDIT
			)
			this.addErrorMessage(WPGMZA.localized_strings.no_google_maps_api_key, ["https://www.wpgmaps.com/documentation/creating-a-google-maps-api-key/"]);
	}
	
	/**
	 * Overrides console.error to scan the error message for Google Maps API error messages.
	 * @method 
	 * @memberof WPGMZA.GoogleAPIErrorHandler
	 * @param {string} message The error message passed to the console
	 */
	WPGMZA.GoogleAPIErrorHandler.prototype.onErrorMessage = function(message)
	{
		var m;
		var regexURL = /http(s)?:\/\/[^\s]+/gm;
		
		if(!message)
			return;
		
		if((m = message.match(/You have exceeded your (daily )?request quota for this API/)) || (m = message.match(/This API project is not authorized to use this API/)) || (m = message.match(/^Geocoding Service: .+/)))
		{
			var urls = message.match(regexURL);
			this.addErrorMessage(m[0], urls);
		}
		else if(m = message.match(/^Google Maps.+error: (.+)\s+(http(s?):\/\/.+)/m))
		{
			this.addErrorMessage(m[1].replace(/([A-Z])/g, " $1"), [m[2]]);
		}
	}
	
	/**
	 * Called by onErrorMessage when a Google Maps API error is picked up, this will add the specified message to the Maps API error message dialog, along with URLs to compliment it. This function ignores duplicate error messages.
	 * @method
	 * @memberof WPGMZA.GoogleAPIErrorHandler
	 * @param {string} message The message, or part of the message, intercepted from the console
	 * @param {array} [urls] An array of URLs relating to the error message to compliment the message.
	 */
	WPGMZA.GoogleAPIErrorHandler.prototype.addErrorMessage = function(message, urls)
	{
		var self = this;
		
		if(this.messagesAlreadyDisplayed[message])
			return;
		
		var li = this.templateListItem.clone();
		$(li).find(".wpgmza-message").html(message);
		
		var buttonContainer = $(li).find(".wpgmza-documentation-buttons");
		
		var buttonTemplate = $(li).find(".wpgmza-documentation-buttons>a");
		buttonTemplate.remove();
		
		if(urls && urls.length)
		{
			for(var i = 0; i < urls.length; i++)
			{
				var url = urls[i];
				var button = buttonTemplate.clone();
				var icon = "fa-external-link";
				var text = WPGMZA.localized_strings.documentation;
				
				button.attr("href", urls[i]);
				
				/*if(url.match(/google.+documentation/))
				{
					// icon = "fa-google";
					icon = "fa-wrench"
				}
				else if(url.match(/maps-no-account/))
				{
					icon = "fa-wrench"
					text = WPGMZA.localized_strings.verify_project;
				}
				else if(url.match(/console\.developers\.google/))
				{
					icon = "fa-wrench";
					text = WPGMZA.localized_strings.api_dashboard;
				}*/
				
				$(button).find("i").addClass(icon);
				$(button).append(text);
			}
			
			buttonContainer.append(button);
		}
		
		$(this.errorMessageList).append(li);
		
		/*if(!this.dialog)
			this.dialog = $(this.element).remodal();
		
		switch(this.dialog.getState())
		{
			case "open":
			case "opened":
			case "opening":
				break;
				
			default:
				this.dialog.open();
				break;
		}*/
		
		$("#wpgmza_map, .wpgmza_map").each(function(index, el) {
			
			var container = $(el).find(".wpgmza-google-maps-api-error-overlay");

			if(container.length == 0)
			{
				container = $("<div class='wpgmza-google-maps-api-error-overlay'></div>");
				container.html(self.element.html());
			}
			
			setTimeout(function() {
				$(el).append(container);
			}, 1000);
		});
		
		$(".gm-err-container").parent().css({"z-index": 1});
		
		this.messagesAlreadyDisplayed[message] = true;
	}
	
	WPGMZA.googleAPIErrorHandler = new WPGMZA.GoogleAPIErrorHandler();

});

// js/v8/info-window.js
/**
 * @namespace WPGMZA
 * @module InfoWindow
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	/**
	 * Base class for infoWindows. This acts as an abstract class so that infoWindows for both Google and OpenLayers can be interacted with seamlessly by the overlying logic. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.InfoWindow
	 * @constructor WPGMZA.InfoWindow
	 * @memberof WPGMZA
	 * @see WPGMZA.InfoWindow.createInstance
	 */
	WPGMZA.InfoWindow = function(mapObject)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.call(this);
		
		WPGMZA.assertInstanceOf(this, "InfoWindow");
		
		this.on("infowindowopen", function(event) {
			self.onOpen(event);
		});
		
		if(!mapObject)
			return;
		
		this.mapObject = mapObject;
		this.state = WPGMZA.InfoWindow.STATE_CLOSED;
		
		if(mapObject.map)
		{
			// This has to be slightly delayed so the map initialization won't overwrite the infowindow element
			setTimeout(function() {
				self.onMapObjectAdded(event);
			}, 100);
		}
		else
			mapObject.addEventListener("added", function(event) { 
				self.onMapObjectAdded(event);
			});
	}
	
	WPGMZA.InfoWindow.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.InfoWindow.prototype.constructor = WPGMZA.InfoWindow;
	
	WPGMZA.InfoWindow.OPEN_BY_CLICK = 1;
	WPGMZA.InfoWindow.OPEN_BY_HOVER = 2;
	
	WPGMZA.InfoWindow.STATE_OPEN	= "open";
	WPGMZA.InfoWindow.STATE_CLOSED	= "closed";
	
	/**
	 * Fetches the constructor to be used by createInstance, based on the selected maps engine
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 * @return {function} The appropriate constructor
	 */
	WPGMZA.InfoWindow.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				if(WPGMZA.isProVersion())
					return WPGMZA.OLProInfoWindow;
				return WPGMZA.OLInfoWindow;
				break;
			
			default:
				if(WPGMZA.isProVersion())
					return WPGMZA.GoogleProInfoWindow;
				return WPGMZA.GoogleInfoWindow;
				break;
		}
	}
	
	/**
	 * Creates an instance of an InfoWindow, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 * @param {object} options Options for the object (optional)
	 */
	WPGMZA.InfoWindow.createInstance = function(mapObject)
	{
		var constructor = this.getConstructor();
		return new constructor(mapObject);
	}
	
	/**
	 * Gets the content for the info window and passes it to the specified callback - this allows for delayed loading (eg AJAX) as well as instant content
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 * @return void
	 */
	WPGMZA.InfoWindow.prototype.getContent = function(callback)
	{
		var html = "";
		
		if(this.mapObject instanceof WPGMZA.Marker)
			html = this.mapObject.address;
		
		callback(html);
	}
	
	/**
	 * Opens the info window on the specified map, with the specified map object as the subject.
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 * @param {WPGMZA.Map} map The map to open this InfoWindow on.
	 * @param {WPGMZA.MapObject} mapObject The map object (eg marker, polygon) to open this InfoWindow on.
	 * @return boolean FALSE if the info window should not and will not open, TRUE if it will. This can be used by subclasses to establish whether or not the subclassed open should bail or open the window.
	 */
	WPGMZA.InfoWindow.prototype.open = function(map, mapObject)
	{
		var self = this;
		
		this.mapObject = mapObject;
		
		if(WPGMZA.settings.disable_infowindows || WPGMZA.settings.wpgmza_settings_disable_infowindows == "1")
			return false;
		
		if(this.mapObject.disableInfoWindow)
			return false;
		
		this.state = WPGMZA.InfoWindow.STATE_OPEN;
		
		return true;
	}
	
	/**
	 * Abstract function, closes this InfoWindow
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 */
	WPGMZA.InfoWindow.prototype.close = function()
	{
		if(this.state == WPGMZA.InfoWindow.STATE_CLOSED)
			return;
		
		this.state = WPGMZA.InfoWindow.STATE_CLOSED;
		this.trigger("infowindowclose");
	}
	
	/**
	 * Abstract function, sets the content in this InfoWindow
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 */
	WPGMZA.InfoWindow.prototype.setContent = function(options)
	{
		
	}
	
	/**
	 * Abstract function, sets options on this InfoWindow
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 */
	WPGMZA.InfoWindow.prototype.setOptions = function(options)
	{
		
	}
	
	/**
	 * Event listener for when the map object is added. This will cause the info window to open if the map object has infoopen set
	 * @method
	 * @memberof WPGMZA.InfoWindow
	 * @return void
	 */
	WPGMZA.InfoWindow.prototype.onMapObjectAdded = function()
	{
		if(this.mapObject.settings.infoopen == 1)
			this.open();
	}
	
	WPGMZA.InfoWindow.prototype.onOpen = function()
	{
		
	}
	
});

// js/v8/latlng.js
/**
 * @namespace WPGMZA
 * @module LatLng
 * @requires WPGMZA
 */
jQuery(function($) {

	/**
	 * This class represents a latitude and longitude coordinate pair, and provides utilities to work with coordinates, parsing and conversion.
	 * @class WPGMZA.LatLng
	 * @constructor WPGMZA.LatLng
	 * @memberof WPGMZA
	 * @param {number|object} arg A latLng literal, or latitude
	 * @param {number} [lng] The latitude, where arg is a longitude
	 */
	WPGMZA.LatLng = function(arg, lng)
	{
		this._lat = 0;
		this._lng = 0;
		
		if(arguments.length == 0)
			return;
		
		if(arguments.length == 1)
		{
			// TODO: Support latlng string
			
			if(typeof arg == "string")
			{
				var m;
				
				if(!(m = arg.match(WPGMZA.LatLng.REGEXP)))
					throw new Error("Invalid LatLng string");
				
				arg = {
					lat: m[1],
					lng: m[3]
				};
			}
			
			if(typeof arg != "object" || !("lat" in arg && "lng" in arg))
				throw new Error("Argument must be a LatLng literal");
			
			this.lat = arg.lat;
			this.lng = arg.lng;
		}
		else
		{
			this.lat = arg;
			this.lng = lng;
		}
	}
	
	/**
	 * A regular expression which matches latitude and longitude coordinate pairs from a string. Matches 1 and 3 correspond to latitude and longitude, respectively,
	 * @constant {RegExp}
	 * @memberof WPGMZA.LatLng
	 */
	WPGMZA.LatLng.REGEXP = /^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/;
	
	/**
	 * Returns true if the supplied object is a LatLng literal, also returns true for instances of WPGMZA.LatLng
	 * @method
	 * @static
	 * @memberof WPGMZA.LatLng
	 * @param {object} obj A LatLng literal, or an instance of WPGMZA.LatLng
	 * @return {bool} True if this object is a valid LatLng literal or instance of WPGMZA.LatLng
	 */
	WPGMZA.LatLng.isValid = function(obj)
	{
		if(typeof obj != "object")
			return false;
		
		if(!("lat" in obj && "lng" in obj))
			return false;
		
		return true;
	}
	
	WPGMZA.LatLng.isLatLngString = function(str)
	{
		if(typeof str != "string")
			return false;
		
		return str.match(WPGMZA.LatLng.REGEXP) ? true : false;
	}
	
	/**
	 * The latitude, guaranteed to be a number
	 * @property lat
	 * @memberof WPGMZA.LatLng
	 */
	Object.defineProperty(WPGMZA.LatLng.prototype, "lat", {
		get: function() {
			return this._lat;
		},
		set: function(val) {
			if(!$.isNumeric(val))
				throw new Error("Latitude must be numeric");
			this._lat = parseFloat( val );
		}
	});
	
	/**
	 * The longitude, guaranteed to be a number
	 * @property lng
	 * @memberof WPGMZA.LatLng
	 */
	Object.defineProperty(WPGMZA.LatLng.prototype, "lng", {
		get: function() {
			return this._lng;
		},
		set: function(val) {
			if(!$.isNumeric(val))
				throw new Error("Longitude must be numeric");
			this._lng = parseFloat( val );
		}
	});
	
	WPGMZA.LatLng.fromString = function(string)
	{
		if(!WPGMZA.LatLng.isLatLngString(string))
			throw new Error("Not a valid latlng string");
		
		var m = string.match(WPGMZA.LatLng.REGEXP);
		
		return new WPGMZA.LatLng({
			lat: parseFloat(m[1]),
			lng: parseFloat(m[3])
		});
	}
	
	/**
	 * Returns this latitude and longitude as a string
	 * @method
	 * @memberof WPGMZA.LatLng
	 * @return {string} This object represented as a string
	 */
	WPGMZA.LatLng.prototype.toString = function()
	{
		return this._lat + ", " + this._lng;
	}
	
	/**
	 * Queries the users current location and passes it to a callback, you can pass
	 * geocodeAddress through options if you would like to also receive the address
	 * @method
	 * @memberof WPGMZA.LatLng
	 * @param {function} A callback to receive the WPGMZA.LatLng
	 * @param {object} An object of options, only geocodeAddress is currently supported
	 * @return void
	 */
	WPGMZA.LatLng.fromCurrentPosition = function(callback, options)
	{
		if(!options)
			options = {};
		
		if(!callback)
			return;
		
		WPGMZA.getCurrentPosition(function(position) {
			
			var latLng = new WPGMZA.LatLng({
				lat: position.coords.latitude,
				lng: position.coords.longitude
			});
			
			if(options.geocodeAddress)
			{
				var geocoder = WPGMZA.Geocoder.createInstance();
				
				geocoder.getAddressFromLatLng({
					latLng: latLng
				}, function(results) {
					
					if(results.length)
						latLng.address = results[0];
					
					callback(latLng);
					
				});
				
				
			}	
			else
				callback(latLng);
			
		});
	}
	
	/**
	 * Returns an instnace of WPGMZA.LatLng from an instance of google.maps.LatLng
	 * @method
	 * @static
	 * @memberof WPGMZA.LatLng
	 * @param {google.maps.LatLng} The google.maps.LatLng to convert
	 * @return {WPGMZA.LatLng} An instance of WPGMZA.LatLng built from the supplied google.maps.LatLng
	 */
	WPGMZA.LatLng.fromGoogleLatLng = function(googleLatLng)
	{
		return new WPGMZA.LatLng(
			googleLatLng.lat(),
			googleLatLng.lng()
		);
	}
	
	WPGMZA.LatLng.toGoogleLatLngArray = function(arr)
	{
		var result = [];
		
		arr.forEach(function(nativeLatLng) {
			
			if(! (nativeLatLng instanceof WPGMZA.LatLng || ("lat" in nativeLatLng && "lng" in nativeLatLng)) )
				throw new Error("Unexpected input");
			
			result.push(new google.maps.LatLng({
				lat: parseFloat(nativeLatLng.lat),
				lng: parseFloat(nativeLatLng.lng)
			}));
			
		});
		
		return result;
	}
	
	/**
	 * Returns an instance of google.maps.LatLng with the same coordinates as this object
	 * @method
	 * @memberof WPGMZA.LatLng
	 * @return {google.maps.LatLng} This object, expressed as a google.maps.LatLng
	 */
	WPGMZA.LatLng.prototype.toGoogleLatLng = function()
	{
		return new google.maps.LatLng({
			lat: this.lat,
			lng: this.lng
		});
	}
	
	WPGMZA.LatLng.prototype.toLatLngLiteral = function()
	{
		return {
			lat: this.lat,
			lng: this.lng
		};
	}
	
	/**
	 * Moves this latLng by the specified kilometers along the given heading. This function operates in place, as opposed to creating a new instance of WPGMZA.LatLng. With many thanks to Hu Kenneth - https://gis.stackexchange.com/questions/234473/get-a-lonlat-point-by-distance-or-between-2-lonlat-points
	 * @method
	 * @memberof WPGMZA.LatLng
	 * @param {number} kilometers The number of kilometers to move this LatLng by
	 * @param {number} heading The heading, in degrees, to move along, where zero is North
	 * @return {void}
	 */
	WPGMZA.LatLng.prototype.moveByDistance = function(kilometers, heading)
	{
		var radius 		= 6371;
		
		var delta 		= parseFloat(kilometers) / radius;
		var theta 		= parseFloat(heading) / 180 * Math.PI;
		
		var phi1 		= this.lat / 180 * Math.PI;
		var lambda1 	= this.lng / 180 * Math.PI;
		
		var sinPhi1 	= Math.sin(phi1), cosPhi1 = Math.cos(phi1);
		var sinDelta	= Math.sin(delta), cosDelta = Math.cos(delta);
		var sinTheta	= Math.sin(theta), cosTheta = Math.cos(theta);
		
		var sinPhi2		= sinPhi1 * cosDelta + cosPhi1 * sinDelta * cosTheta;
		var phi2		= Math.asin(sinPhi2);
		var y			= sinTheta * sinDelta * cosPhi1;
		var x			= cosDelta - sinPhi1 * sinPhi2;
		var lambda2		= lambda1 + Math.atan2(y, x);
		
		this.lat		= phi2 * 180 / Math.PI;
		this.lng		= lambda2 * 180 / Math.PI;
	}
	
	/**
	 * @function getGreatCircleDistance
	 * @summary Uses the haversine formula to get the great circle distance between this and another LatLng / lat & lng pair
	 * @param arg1 [WPGMZA.LatLng|Object|Number] Either a WPGMZA.LatLng, an object representing a lat/lng literal, or a latitude
	 * @param arg2 (optional) If arg1 is a Number representing latitude, pass arg2 to represent the longitude
	 * @return number The distance "as the crow files" between this point and the other
	 */
	WPGMZA.LatLng.prototype.getGreatCircleDistance = function(arg1, arg2)
	{
		var lat1 = this.lat;
		var lon1 = this.lng;
		var other;
		
		if(arguments.length == 1)
			other = new WPGMZA.LatLng(arg1);
		else if(arguments.length == 2)
			other = new WPGMZA.LatLng(arg1, arg2);
		else
			throw new Error("Invalid number of arguments");
		
		var lat2 = other.lat;
		var lon2 = other.lng;
		
		var R = 6371; // Kilometers
		var phi1 = lat1.toRadians();
		var phi2 = lat2.toRadians();
		var deltaPhi = (lat2-lat1).toRadians();
		var deltaLambda = (lon2-lon1).toRadians();

		var a = Math.sin(deltaPhi/2) * Math.sin(deltaPhi/2) +
				Math.cos(phi1) * Math.cos(phi2) *
				Math.sin(deltaLambda/2) * Math.sin(deltaLambda/2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

		var d = R * c;
		
		return d;
	}
	
});

// js/v8/latlngbounds.js
/**
 * @namespace WPGMZA
 * @module LatLngBounds
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * This class represents latitude and longitude bounds as a rectangular area.
	 * NB: This class is not fully implemented
	 * @class WPGMZA.LatLngBounds
	 * @constructor WPGMZA.LatLngBounds
	 * @memberof WPGMZA
	 */
	WPGMZA.LatLngBounds = function(southWest, northEast)
	{
		//console.log("Created bounds", southWest, northEast);
		
		if(southWest instanceof WPGMZA.LatLngBounds)
		{
			var other = southWest;
			this.south = other.south;
			this.north = other.north;
			this.west = other.west;
			this.east = other.east;
		}
		else if(southWest && northEast)
		{
			// TODO: Add checks and errors
			this.south = southWest.lat;
			this.north = northEast.lat;
			this.west = southWest.lng;
			this.east = northEast.lng;
		}
	}
	
	WPGMZA.LatLngBounds.fromGoogleLatLngBounds = function(googleLatLngBounds)
	{
		if(!(googleLatLngBounds instanceof google.maps.LatLngBounds))
			throw new Error("Argument must be an instance of google.maps.LatLngBounds");
		
		var result = new WPGMZA.LatLngBounds();
		var southWest = googleLatLngBounds.getSouthWest();
		var northEast = googleLatLngBounds.getNorthEast();
		
		result.north = northEast.lat();
		result.south = southWest.lat();
		result.west = southWest.lng();
		result.east = northEast.lng();
		
		return result;
	}
	
	/**
	 * Returns true if this object is in it's initial state (eg no points specified to gather bounds from)
	 * @method
	 * @memberof WPGMZA.LatLngBounds
	 * @return {bool} True if the object is in it's initial state
	 */
	WPGMZA.LatLngBounds.prototype.isInInitialState = function()
	{
		return (this.north == undefined && this.south == undefined && this.west == undefined && this.east == undefined);
	}
	
	/**
	 * Extends this bounds object to encompass the given latitude and longitude coordinates
	 * @method
	 * @memberof WPGMZA.LatLngBounds
	 * @param {object|WPGMZA.LatLng} latLng either a LatLng literal or an instance of WPGMZA.LatLng
	 */
	WPGMZA.LatLngBounds.prototype.extend = function(latLng)
	{
		if(!(latLng instanceof WPGMZA.LatLng))
			latLng = new WPGMZA.LatLng(latLng);
		
		//console.log("Expanding bounds to " + latLng.toString());
		
		if(this.isInInitialState())
		{
			this.north = this.south = latLng.lat;
			this.west = this.east = latLng.lng;
			return;
		}
		
		if(latLng.lat < this.north)
			this.north = latLng.lat;
		
		if(latLng.lat > this.south)
			this.south = latLng.lat;
		
		if(latLng.lng < this.west)
			this.west = latLng.lng;
		
		if(latLng.lng > this.east)
			this.east = latLng.lng;
	}
	
	WPGMZA.LatLngBounds.prototype.extendByPixelMargin = function(map, x, arg)
	{
		var y = x;
		
		if(!(map instanceof WPGMZA.Map))
			throw new Error("First argument must be an instance of WPGMZA.Map");
		
		if(this.isInInitialState())
			throw new Error("Cannot extend by pixels in initial state");
		
		if(arguments.length >= 3)
			y = arg;
		
		var southWest = new WPGMZA.LatLng(this.south, this.west);
		var northEast = new WPGMZA.LatLng(this.north, this.east);
		
		southWest = map.latLngToPixels(southWest);
		northEast = map.latLngToPixels(northEast);
		
		southWest.x -= x;
		southWest.y += y;
		
		northEast.x += x;
		northEast.y -= y;
		
		southWest = map.pixelsToLatLng(southWest.x, southWest.y);
		northEast = map.pixelsToLatLng(northEast.x, northEast.y);
		
		var temp = this.toString();
		
		this.north = northEast.lat;
		this.south = southWest.lat;
		this.west = southWest.lng;
		this.east = northEast.lng;
		
		// console.log("Extended", temp, "to", this.toString());
	}
	
	WPGMZA.LatLngBounds.prototype.contains = function(latLng)
	{
		//console.log("Checking if latLng ", latLng, " is within bounds " + this.toString());
		
		if(!(latLng instanceof WPGMZA.LatLng))
			throw new Error("Argument must be an instance of WPGMZA.LatLng");
		
		if(latLng.lat < Math.min(this.north, this.south))
			return false;
		
		if(latLng.lat > Math.max(this.north, this.south))
			return false;
		
		if(this.west < this.east)
			return (latLng.lng >= this.west && latLng.lng <= this.east);
		
		return (latLng.lng <= this.west || latLng.lng >= this.east);
	}
	
	WPGMZA.LatLngBounds.prototype.toString = function()
	{
		return this.north + "N " + this.south + "S " + this.west + "W " + this.east + "E";
	}
	
	WPGMZA.LatLngBounds.prototype.toLiteral = function()
	{
		return {
			north: this.north,
			south: this.south,
			west: this.west,
			east: this.east
		};
	}
	
});

// js/v8/map-edit-page.js
/**
 * @namespace WPGMZA
 * @module MapEditPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	if(WPGMZA.currentPage != "map-edit")
		return;
	
	WPGMZA.MapEditPage = function()
	{
		var self = this;
		
		this.themePanel = new WPGMZA.ThemePanel();
		this.themeEditor = new WPGMZA.ThemeEditor();
		
		this.map = WPGMZA.maps[0];

		// Check if user enabled any interactions
		if(WPGMZA.settings.wpgmza_settings_map_scroll == 'yes' || WPGMZA.settings.wpgmza_settings_map_draggable == "yes" || WPGMZA.settings.wpgmza_settings_map_clickzoom == 'yes')
		{
			// Display notice and button if user enabled interactions
			var diplay_enable_interactions_notice = $("<div class='notice notice-info wpgmza_disabled_interactions_notice' style= 'height: 45px; padding: 7px 5px 2px 5px;'><p style='float: left; padding-top: 10px;'>" + WPGMZA.localized_strings.disabled_interactions_notice + 
			"</p><a class='button button-primary enable_interactions_notice_button' style='float: right;'>" + WPGMZA.localized_strings.disabled_interactions_button + "</a></div>");
			
			$(".wpgmza_map").after(diplay_enable_interactions_notice);

			$(".enable_interactions_notice_button").on("click", function() {
	
				WPGMZA.mapEditPage.map.enableAllInteractions();
	
				$(diplay_enable_interactions_notice).fadeOut('slow');
	
				var successNotice = $("<div class='notice notice-success'><p>" + WPGMZA.localized_strings.interactions_enabled_notice + "</p></div>");
				$(WPGMZA.mapEditPage.map.element).after(successNotice);
				$(successNotice).delay(2000).fadeIn('slow');
				$(successNotice).delay(4000).fadeOut('slow');
			});
		}

		$('#wpgmza_max_zoom, #wpgmza_min_zoom').on("change input", function(event) {
			self.onZoomLimitChanged(event);
		});
	}
	
	WPGMZA.MapEditPage.createInstance = function()
	{
		if(WPGMZA.isProVersion() && WPGMZA.Version.compare(WPGMZA.pro_version, "8.0.0") >= WPGMZA.Version.EQUAL_TO)
			return new WPGMZA.ProMapEditPage();
		
		return new WPGMZA.MapEditPage();
	}

	WPGMZA.MapEditPage.prototype.onZoomLimitChanged = function(event)
	{
		this.map.setOptions({
			minZoom:	$("#wpgmza_max_zoom").val(),
			maxZoom:	$("#wpgmza_min_zoom").val()
		});
	}
	
	$(window).on("load", function(event) {
		
		WPGMZA.mapEditPage = WPGMZA.MapEditPage.createInstance();
		
	});
	
});

// js/v8/map-object.js
/**
 * @namespace WPGMZA
 * @module MapObject
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	/**
	 * Base class for Map Objects (known as Features in Map Block), that is, markers, polygons, polylines, circles, rectangles and heatmaps. Implements functionality shared by all map objects, such as parsing geometry and serialization.
	 * @class WPGMZA.MapObject
	 * @constructor WPGMZA.MapObject
	 * @memberof WPGMZA
	 * @augments WPGMZA.EventDispatcher
	 */
	WPGMZA.MapObject = function(row)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "MapObject");
		
		WPGMZA.EventDispatcher.call(this);
		
		this.id = -1;
		this.map_id = null;
		this.guid = WPGMZA.guid();
		this.modified = true;
		this.settings = {};
		
		if(row)
		{
			for(var name in row)
			{
				if(name == "settings")
				{
					if(row["settings"] == null)
						this["settings"] = {};
					else switch(typeof row["settings"]) {
						case "string":
							this["settings"] = JSON.parse(row[name]);
							break;
						case "object":
							this["settings"] = row[name];
							break;
						default:
							throw new Error("Don't know how to interpret settings")
							break;
					}
					
					for(var name in this.settings)
					{
						var value = this.settings[name];
						if(String(value).match(/^-?\d+$/))
							this.settings[name] = parseInt(value);
					}
				}
				else
					this[name] = row[name];
			}
		}		
	}
	
	WPGMZA.MapObject.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.MapObject.prototype.constructor = WPGMZA.MapObject;
	
	/**
	 * Scans a string for all floating point numbers and build an array of latitude and longitude literals from the matched numbers
	 * @method
	 * @memberof WPGMZA.MapObject
	 * @param {string} string The string to parse numbers from
	 * @return {array} An array of LatLng literals parsed from the string
	 */
	WPGMZA.MapObject.prototype.parseGeometry = function(string)
	{
		var stripped, pairs, coords, results = [];
		stripped = string.replace(/[^ ,\d\.\-+e]/g, "");
		pairs = stripped.split(",");
		
		for(var i = 0; i < pairs.length; i++)
		{
			coords = pairs[i].split(" ");
			results.push({
				lat: parseFloat(coords[1]),
				lng: parseFloat(coords[0])
			});
		}
				
		return results;
	}
	
	/**
	 * Returns a copy of this object as a JSON object for serializsation
	 * @method
	 * @memberof WPGMZA.MapObject
	 * @return {object} This object as represented by JSON
	 */
	WPGMZA.MapObject.prototype.toJSON = function()
	{
		return {
			id: this.id,
			guid: this.guid,
			settings: this.settings
		};
	}
	
});

// js/v8/circle.js
/**
 * @namespace WPGMZA
 * @module Circle
 * @requires WPGMZA.MapObject
 */
jQuery(function($) {
	
	var Parent = WPGMZA.MapObject;
	
	/**
	 * Base class for circles. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.Circle
	 * @constructor WPGMZA.Circle
	 * @memberof WPGMZA
	 * @augments WPGMZA.MapObject
	 * @see WPGMZA.Circle.createInstance
	 */
	WPGMZA.Circle = function(options, engineCircle)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "Circle");
		
		this.center = new WPGMZA.LatLng();
		this.radius = 100;
		
		Parent.apply(this, arguments);
	}
	
	WPGMZA.Circle.prototype = Object.create(Parent.prototype);
	WPGMZA.Circle.prototype.constructor = WPGMZA.Circle;
	
	/**
	 * Creates an instance of a circle, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.Circle
	 * @param {object} options Options for the object (optional)
	 */
	WPGMZA.Circle.createInstance = function(options)
	{
		var constructor;
		
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				constructor = WPGMZA.OLCircle;
				break;
			
			default:
				constructor = WPGMZA.GoogleCircle;
				break;
		}
		
		return new constructor(options);
	}
	
	/**
	 * Gets the circles center
	 *
	 * @method
	 * @memberof WPGMZA.Circle
	 * @returns {WPGMZA.LatLng}
	 */
	WPGMZA.Circle.prototype.getCenter = function()
	{
		return this.center.clone();
	}
	
	/**
	 * Sets the circles center
	 *
	 * @method
	 * @memberof WPGMZA.Circle
	 * @param {object|WPGMZA.LatLng} latLng either a literal or as a WPGMZA.LatLng
	 */
	WPGMZA.Circle.prototype.setCenter = function(latLng)
	{
		this.center.lat = latLng.lat;
		this.center.lng = latLng.lng;
	}
	
	/**
	 * Gets the circles radius, in kilometers
	 *
	 * @method
	 * @memberof WPGMZA.Circle
	 * @param {object|WPGMZA.LatLng} latLng either a literal or as a WPGMZA.LatLng
	 * @returns {WPGMZA.LatLng}
	 */
	WPGMZA.Circle.prototype.getRadius = function()
	{
		return this.radius;
	}
	
	/**
	 * Sets the circles radius, in kilometers
	 *
	 * @method
	 * @memberof WPGMZA.Circle
	 * @param {number} radius The radius
	 * @returns {void}
	 */
	WPGMZA.Circle.prototype.setRadius = function(radius)
	{
		this.radius = radius;
	}
	
	/**
	 * Returns the map that this circle is being displayed on
	 *
	 * @method
	 * @memberof WPGMZA.Circle
	 * @return {WPGMZA.Map}
	 */
	WPGMZA.Circle.prototype.getMap = function()
	{
		return this.map;
	}
	
	/**
	 * Puts this circle on a map
	 *
	 * @method
	 * @memberof WPGMZA.Circle
	 * @param {WPGMZA.Map} map The target map
	 * @return {void}
	 */
	WPGMZA.Circle.prototype.setMap = function(map)
	{
		if(this.map)
			this.map.removeCircle(this);
		
		if(map)
			map.addCircle(this);
			
	}
	
});

// js/v8/map-settings-page.js
/**
 * @namespace WPGMZA
 * @module MapSettingsPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * This class helps manage the map settings pageX
	 * @class WPGMZA.MapSettingsPage
	 * @constructor WPGMZA.MapSettingsPage
	 * @memberof WPGMZA
	 */
	WPGMZA.MapSettingsPage = function()
	{
		var self = this;
		
		this._keypressHistory = [];
		
		this.updateEngineSpecificControls();
		this.updateGDPRControls();
		
		$("#wpgmza-developer-mode").hide();
		$(window).on("keypress", function(event) {
			self.onKeyPress(event);
		});
		
		$("select[name='wpgmza_maps_engine']").on("change", function(event) {
			self.updateEngineSpecificControls();
		});
		
		$("input[name='wpgmza_gdpr_require_consent_before_load'], input[name='wpgmza_gdpr_require_consent_before_vgm_submit'], input[name='wpgmza_gdpr_override_notice']").on("change", function(event) {
			self.updateGDPRControls();
		});
	}
	
	WPGMZA.MapSettingsPage.createInstance = function()
	{
		return new WPGMZA.MapSettingsPage();
	}
	
	/**
	 * Updates engine specific controls, hiding irrelevant controls (eg Google controls when OpenLayers is the selected engine) and showing relevant controls.
	 * @method
	 * @memberof WPGMZA.MapSettingsPage
	 */
	WPGMZA.MapSettingsPage.prototype.updateEngineSpecificControls = function()
	{
		var engine = $("select[name='wpgmza_maps_engine']").val();
		
		$("[data-required-maps-engine][data-required-maps-engine!='" + engine + "']").hide();
		$("[data-required-maps-engine='" + engine + "']").show();
	}
	
	/**
	 * Updates the GDPR controls (eg visibility state) based on the selected GDPR settings
	 * @method
	 * @memberof WPGMZA.MapSettingsPage
	 */
	WPGMZA.MapSettingsPage.prototype.updateGDPRControls = function()
	{
		var showNoticeControls = $("input[name='wpgmza_gdpr_require_consent_before_load']").prop("checked");
		
		var vgmCheckbox = $("input[name='wpgmza_gdpr_require_consent_before_vgm_submit']");
		
		if(vgmCheckbox.length)
			showNoticeControls = showNoticeControls || vgmCheckbox.prop("checked");
		
		var showOverrideTextarea = showNoticeControls && $("input[name='wpgmza_gdpr_override_notice']").prop("checked");
		
		if(showNoticeControls)
		{
			$("#wpgmza-gdpr-compliance-notice").show("slow");
		}
		else
		{
			$("#wpgmza-gdpr-compliance-notice").hide("slow");
		}
		
		if(showOverrideTextarea)
		{
			$("#wpgmza_gdpr_override_notice_text").show("slow");
		}
		else
		{
			$("#wpgmza_gdpr_override_notice_text").hide("slow");
		}
	}

	/**
	 * Flushes the geocode cache
	 */
	WPGMZA.MapSettingsPage.prototype.flushGeocodeCache = function()
	{
		var OLGeocoder = new WPGMZA.OLGeocoder();
		OLGeocoder.clearCache(function(response){
			jQuery('#wpgmza_flush_cache_btn').removeAttr('disabled');
		});
	}
	
	WPGMZA.MapSettingsPage.prototype.onKeyPress = function(event)
	{
		var string;
		
		this._keypressHistory.push(event.key);
		
		if(this._keypressHistory.length > 9)
			this._keypressHistory = this._keypressHistory.slice(this._keypressHistory.length - 9);
		
		string = this._keypressHistory.join("");
		
		if(string == "codecabin" && !this._developerModeRevealed)
		{
			$("#wpgmza-developer-mode").show();
			this._developerModeRevealed = true;
		}
	}
	
	jQuery(function($) {
		
		if(!window.location.href.match(/wp-google-maps-menu-settings/))
			return;
		
		WPGMZA.mapSettingsPage = WPGMZA.MapSettingsPage.createInstance();

		jQuery(document).ready(function(){
			jQuery('#wpgmza_flush_cache_btn').on('click', function(){
				jQuery(this).attr('disabled', 'disabled');
				WPGMZA.mapSettingsPage.flushGeocodeCache();
			});
		});
		
	});
	
});

// js/v8/map-settings.js
/**
 * @namespace WPGMZA
 * @module MapSettings
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Handles map settings, parsing them from the data-settings attribute on the maps HTML element.
	 * NB: This will be split into GoogleMapSettings and OLMapSettings in the future.
	 * @class WPGMZA.MapSettings
	 * @constructor WPGMZA.MapSettings
	 */
	WPGMZA.MapSettings = function(element)
	{
		var self = this;
		var str = element.getAttribute("data-settings");
		var json;
		
		try{
			json = JSON.parse(str);
		}catch(e) {
			
			str = str.replace(/\\%/g, "%");
			str = str.replace(/\\\\"/g, '\\"');
			
			try{
				json = JSON.parse(str);
			}catch(e) {
				json = {};
				console.warn("Failed to parse map settings JSON");
			}
			
		}
		
		WPGMZA.assertInstanceOf(this, "MapSettings");
		
		function addSettings(input)
		{
			if(!input)
				return;
			
			for(var key in input)
			{
				if(key == "other_settings")
					continue; // Ignore other_settings
				
				var value = input[key];
				
				if(String(value).match(/^-?\d+$/))
					value = parseInt(value);
					
				self[key] = value;
			}
		}
		
		addSettings(WPGMZA.settings);
		
		addSettings(json);
		
		if(json && json.other_settings)
			addSettings(json.other_settings);
	}
	
	/**
	 * Returns settings on this object converted to OpenLayers view options
	 * @method
	 * @memberof WPGMZA.MapSettings
	 * @return {object} The map settings, in a format understood by OpenLayers
	 */
	WPGMZA.MapSettings.prototype.toOLViewOptions = function()
	{
		var self = this;
		var options = {
			center: ol.proj.fromLonLat([-119.4179, 36.7783]),
			zoom: 4
		};
		
		function empty(name)
		{
			if(typeof self[name] == "object")
				return false;
			
			return !self[name] || !self[name].length;
		}
		
		// Start location
		if(typeof this.start_location == "string")
		{
			var coords = this.start_location.replace(/^\(|\)$/g, "").split(",");
			if(WPGMZA.isLatLngString(this.start_location))
				options.center = ol.proj.fromLonLat([
					parseFloat(coords[1]),
					parseFloat(coords[0])
				]);
			else
				console.warn("Invalid start location");
		}
		
		if(this.center)
		{
			options.center = ol.proj.fromLonLat([
				parseFloat(this.center.lng),
				parseFloat(this.center.lat)
			]);
		}
		
		if(!empty("map_start_lat") && !empty("map_start_lng"))
		{
			options.center = ol.proj.fromLonLat([
				parseFloat(this.map_start_lng),
				parseFloat(this.map_start_lat)
			]);
		}
		
		// Start zoom
		if(this.zoom)
			options.zoom = parseInt(this.zoom);
		
		if(this.start_zoom)
			options.zoom = parseInt(this.start_zoom);
		
		// Zoom limits
		// TODO: This matches the Google code, so some of these could be potentially put on a parent class
		if(this.map_min_zoom && this.map_max_zoom)
		{
			options.minZoom = Math.min(this.map_min_zoom, this.map_max_zoom);
			options.maxZoom = Math.max(this.map_min_zoom, this.map_max_zoom);
		}
		
		return options;
	}
	
	/**
	 * Returns settings on this object converted to Google's MapOptions spec.
	 * @method
	 * @memberof WPGMZA.MapSettings
	 * @return {object} The map settings, in the format specified by google.maps.MapOptions
	 */
	WPGMZA.MapSettings.prototype.toGoogleMapsOptions = function()
	{
		var self = this;
		var latLngCoords = (this.start_location && this.start_location.length ? this.start_location.split(",") : [36.7783, -119.4179]);
		
		function empty(name)
		{
			if(typeof self[name] == "object")
				return false;
			
			return !self[name] || !self[name].length;
		}
		
		function formatCoord(coord)
		{
			if($.isNumeric(coord))
				return coord;
			return parseFloat( String(coord).replace(/[\(\)\s]/, "") );
		}
		
		var latLng = new google.maps.LatLng(
			formatCoord(latLngCoords[0]),
			formatCoord(latLngCoords[1])
		);
		
		var zoom = (this.start_zoom ? parseInt(this.start_zoom) : 4);
		
		if(!this.start_zoom && this.zoom)
			zoom = parseInt( this.zoom );
		
		var options = {
			zoom:			zoom,
			center:			latLng
		};
		
		if(!empty("center"))
			options.center = new google.maps.LatLng({
				lat: parseFloat(this.center.lat),
				lng: parseFloat(this.center.lng)
			});
		
		if(!empty("map_start_lat") && !empty("map_start_lng"))
		{
			// NB: map_start_lat and map_start_lng are the "real" values. Not sure where start_location comes from
			options.center = new google.maps.LatLng({
				lat: parseFloat(this.map_start_lat),
				lng: parseFloat(this.map_start_lng)
			});
		}
		
		if(this.map_min_zoom && this.map_max_zoom)
		{
			options.minZoom = Math.min(this.map_min_zoom, this.map_max_zoom);
			options.maxZoom = Math.max(this.map_min_zoom, this.map_max_zoom);
		}
		
		// These settings are all inverted because the checkbox being set means "disabled"
		options.zoomControl				= !(this.wpgmza_settings_map_zoom == 'yes');
        options.panControl				= !(this.wpgmza_settings_map_pan == 'yes');
        options.mapTypeControl			= !(this.wpgmza_settings_map_type == 'yes');
        options.streetViewControl		= !(this.wpgmza_settings_map_streetview == 'yes');
        options.fullscreenControl		= !(this.wpgmza_settings_map_full_screen_control == 'yes');
        
        options.draggable				= !(this.wpgmza_settings_map_draggable == 'yes');
        options.disableDoubleClickZoom	= (this.wpgmza_settings_map_clickzoom == 'yes');
		
		// NB: This setting is handled differently as setting scrollwheel to true breaks gestureHandling
		if(this.wpgmza_settings_map_scroll)
			options.scrollwheel			= false;
		
		if(this.wpgmza_force_greedy_gestures == "greedy" || this.wpgmza_force_greedy_gestures == "yes")
			options.gestureHandling = "greedy";
		else
			options.gestureHandling = "cooperative";
		
		switch(parseInt(this.type))
		{
			case 2:
				options.mapTypeId = google.maps.MapTypeId.SATELLITE;
				break;
			
			case 3:
				options.mapTypeId = google.maps.MapTypeId.HYBRID;
				break;
			
			case 4:
				options.mapTypeId = google.maps.MapTypeId.TERRAIN;
				break;
				
			default:
				options.mapTypeId = google.maps.MapTypeId.ROADMAP;
				break;
		}
		
		if(this.wpgmza_theme_data && this.wpgmza_theme_data.length)
			options.styles = WPGMZA.GoogleMap.parseThemeData(this.wpgmza_theme_data);
		
		return options;
	}
});

// js/v8/map.js
/**
 * @namespace WPGMZA
 * @module Map
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	/**
	 * Base class for maps. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.Map
	 * @constructor WPGMZA.Map
	 * @memberof WPGMZA
	 * @param {HTMLElement} element to contain map
	 * @param {object} [options] Options to apply to this map
	 * @augments WPGMZA.EventDispatcher
	 */
	WPGMZA.Map = function(element, options)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "Map");
		
		WPGMZA.EventDispatcher.call(this);
		
		if(!(element instanceof HTMLElement))
			throw new Error("Argument must be a HTMLElement");
		
		// NB: This should be moved to a getID function or similar and offloaded to Pro. ID should be fixed to 1 in basic.
		if(element.hasAttribute("data-map-id"))
			this.id = element.getAttribute("data-map-id");
		else
			this.id = 1;
		
		if(!/\d+/.test(this.id))
			throw new Error("Map ID must be an integer");
		
		WPGMZA.maps.push(this);
		
		this.element = element;
		this.element.wpgmzaMap = this;
		
		this.engineElement = element;
		
		this.markers = [];
		this.polygons = [];
		this.polylines = [];
		this.circles = [];
		this.rectangles = [];
		
		this.loadSettings(options);
		
		this.shortcodeAttributes = {};
		if($(this.element).attr("data-shortcode-attributes"))
			try{
				this.shortcodeAttributes = JSON.parse($(this.element).attr("data-shortcode-attributes"))
			}catch(e) {
				console.warn("Error parsing shortcode attributes");
			}
		
		if(WPGMZA.getCurrentPage() != WPGMZA.PAGE_MAP_EDIT)
			this.initStoreLocator();
		
		this.markerFilter = WPGMZA.MarkerFilter.createInstance(this);
	}
	
	WPGMZA.Map.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.Map.prototype.constructor = WPGMZA.Map;
	WPGMZA.Map.nightTimeThemeData = [{"elementType":"geometry","stylers":[{"color":"#242f3e"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#746855"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#242f3e"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#575663"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#263c3f"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#6b9a76"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#38414e"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#212a37"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#9ca5b3"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#746855"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#80823e"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#1f2835"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#f3d19c"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#2f3948"}]},{"featureType":"transit.station","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#17263c"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#1b737a"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#515c6d"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#17263c"}]}];
	
	/**
	 * Returns the contructor to be used by createInstance, depending on the selected maps engine.
	 * @method
	 * @memberof WPGMZA.Map
	 * @return {function} The appropriate contructor
	 */
	WPGMZA.Map.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				if(WPGMZA.isProVersion())
					return WPGMZA.OLProMap;
				
				return WPGMZA.OLMap;
				break;
			
			default:
				if(WPGMZA.isProVersion())
					return WPGMZA.GoogleProMap;
				
				return WPGMZA.GoogleMap;
				break;
		}
	}

	/**
	 * Creates an instance of a map, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {HTMLElement} element to contain map
	 * @param {object} [options] Options to apply to this map
	 * @return {WPGMZA.Map} An instance of WPGMZA.Map
	 */
	WPGMZA.Map.createInstance = function(element, options)
	{
		var constructor = WPGMZA.Map.getConstructor();
		return new constructor(element, options);
	}
	
	/**
	 * The maps current latitude
	 * 
	 * @property lat
	 * @memberof WPGMZA.Map
	 * @name WPGMZA.Map#lat
	 * @type Number
	 */
	Object.defineProperty(WPGMZA.Map.prototype, "lat", {
		
		get: function() {
			return this.getCenter().lat;
		},
		
		set: function(value) {
			var center = this.getCenter();
			center.lat = value;
			this.setCenter(center);
		}
		
	});
	
	/**
	 * The maps current longitude
	 * 
	 * @property lng
	 * @memberof WPGMZA.Map
	 * @name WPGMZA.Map#lng
	 * @type Number
	 */
	Object.defineProperty(WPGMZA.Map.prototype, "lng", {
		
		get: function() {
			return this.getCenter().lng;
		},
		
		set: function(value) {
			var center = this.getCenter();
			center.lng = value;
			this.setCenter(center);
		}
		
	});
	
	/**
	 * The maps current zoom level
	 *  
	 * @property zoom
	 * @memberof WPGMZA.Map
	 * @name WPGMZA.Map#zoom
	 * @type Number
	 */
	Object.defineProperty(WPGMZA.Map.prototype, "zoom", {
		
		get: function() {
			return this.getZoom();
		},
		
		set: function(value) {
			this.setZoom(value);
		}
		
	});
	
	/**
	 * Loads the maps settings and sets some defaults
	 * @method
	 * @memberof WPGMZA.Map
	 */
	WPGMZA.Map.prototype.loadSettings = function(options)
	{
		var settings = new WPGMZA.MapSettings(this.element);
		var other_settings = settings.other_settings;
		
		delete settings.other_settings;
		
		/*if(other_settings)
			for(var key in other_settings)
				settings[key] = other_settings[key];*/
			
		if(options)
			for(var key in options)
				settings[key] = options[key];
			
		this.settings = settings;
	}
	
	WPGMZA.Map.prototype.initStoreLocator = function()
	{
		var storeLocatorElement = $(".wpgmza_sl_main_div");
		if(storeLocatorElement.length)
			this.storeLocator = WPGMZA.StoreLocator.createInstance(this, storeLocatorElement[0]);
	}
	
	/**
	 * Sets options in bulk on map
	 * @method
	 * @memberof WPGMZA.Map
	 */
	WPGMZA.Map.prototype.setOptions = function(options)
	{
		for(var name in options)
			this.settings[name] = options[name];
	}
	
	/**
	 * Gets the distance between two latLngs in kilometers
	 * NB: Static function
	 * @return number
	 */
	var earthRadiusMeters = 6371;
	var piTimes360 = Math.PI / 360;
	
	function deg2rad(deg) {
	  return deg * (Math.PI/180)
	};
	
	/**
	 * This gets the distance in kilometers between two latitude / longitude points
	 * TODO: Move this to the distance class, or the LatLng class
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {number} lat1 Latitude from the first coordinate pair
	 * @param {number} lon1 Longitude from the first coordinate pair
	 * @param {number} lat2 Latitude from the second coordinate pair
	 * @param {number} lon1 Longitude from the second coordinate pair
	 * @return {number} The distance between the latitude and longitudes, in kilometers
	 */
	WPGMZA.Map.getGeographicDistance = function(lat1, lon1, lat2, lon2)
	{
		var dLat = deg2rad(lat2-lat1);
		var dLon = deg2rad(lon2-lon1); 
		
		var a = 
			Math.sin(dLat/2) * Math.sin(dLat/2) +
			Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
			Math.sin(dLon/2) * Math.sin(dLon/2); 
			
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
		var d = earthRadiusMeters * c; // Distance in km
		
		return d;
	}
	
	/**
	 * Centers the map on the supplied latitude and longitude
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {object|WPGMZA.LatLng} latLng A LatLng literal or an instance of WPGMZA.LatLng
	 */
	WPGMZA.Map.prototype.setCenter = function(latLng)
	{
		if(!("lat" in latLng && "lng" in latLng))
			throw new Error("Argument is not an object with lat and lng");
	}
	
	/**
	 * Sets the dimensions of the map engine element
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {number} width Width as a CSS string
	 * @param {number} height Height as a CSS string
	 */
	WPGMZA.Map.prototype.setDimensions = function(width, height)
	{
		$(this.element).css({
			width: width
		});
		
		$(this.engineElement).css({
			width: "100%",
			height: height
		});
	}
	
	/**
	 * Adds the specified marker to this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Marker} marker The marker to add
	 * @fires markeradded
	 * @fires WPGMZA.Marker#added
	 * @throws Argument must be an instance of WPGMZA.Marker
	 */
	WPGMZA.Map.prototype.addMarker = function(marker)
	{
		if(!(marker instanceof WPGMZA.Marker))
			throw new Error("Argument must be an instance of WPGMZA.Marker");
		
		marker.map = this;
		marker.parent = this;
		
		this.markers.push(marker);
		this.dispatchEvent({type: "markeradded", marker: marker});
		marker.dispatchEvent({type: "added"});
	}
	
	/**
	 * Removes the specified marker from this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Marker} marker The marker to remove
	 * @fires markerremoved
	 * @fires WPGMZA.Marker#removed
	 * @throws Argument must be an instance of WPGMZA.Marker
	 * @throws Wrong map error
	 */
	WPGMZA.Map.prototype.removeMarker = function(marker)
	{
		if(!(marker instanceof WPGMZA.Marker))
			throw new Error("Argument must be an instance of WPGMZA.Marker");
		
		if(marker.map !== this)
			throw new Error("Wrong map error");
		
		if(marker.infoWindow)
			marker.infoWindow.close();
		
		marker.map = null;
		marker.parent = null;
		
		this.markers.splice(this.markers.indexOf(marker), 1);
		this.dispatchEvent({type: "markerremoved", marker: marker});
		marker.dispatchEvent({type: "removed"});
	}
	
	/**
	 * Gets a marker by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the marker to get
	 * @return {WPGMZA.Marker|null} The marker, or null if no marker with the specified ID is found
	 */
	WPGMZA.Map.prototype.getMarkerByID = function(id)
	{
		for(var i = 0; i < this.markers.length; i++)
		{
			if(this.markers[i].id == id)
				return this.markers[i];
		}
		
		return null;
	}
	
	WPGMZA.Map.prototype.getMarkerByTitle = function(title)
	{
		if(typeof title == "string")
			for(var i = 0; i < this.markers.length; i++)
			{
				if(this.markers[i].title == title)
					return this.markers[i];
			}
		else if(title instanceof RegExp)
			for(var i = 0; i < this.markers.length; i++)
			{
				if(title.test(this.markers[i].title))
					return this.markers[i];
			}
		else
			throw new Error("Invalid argument");
		
		return null;
	}
	
	/**
	 * Removes a marker by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the marker to remove
	 * @fires markerremoved
	 * @fires WPGMZA.Marker#removed
	 */
	WPGMZA.Map.prototype.removeMarkerByID = function(id)
	{
		var marker = this.getMarkerByID(id);
		
		if(!marker)
			return;
		
		this.removeMarker(marker);
	}
	
	/**
	 * Adds the specified polygon to this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Polygon} polygon The polygon to add
	 * @fires polygonadded
	 * @throws Argument must be an instance of WPGMZA.Polygon
	 */
	WPGMZA.Map.prototype.addPolygon = function(polygon)
	{
		if(!(polygon instanceof WPGMZA.Polygon))
			throw new Error("Argument must be an instance of WPGMZA.Polygon");
		
		polygon.map = this;
		
		this.polygons.push(polygon);
		this.dispatchEvent({type: "polygonadded", polygon: polygon});
	}
	
	/**
	 * Removes the specified polygon from this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Polygon} polygon The polygon to remove
	 * @fires polygonremoved
	 * @throws Argument must be an instance of WPGMZA.Polygon
	 * @throws Wrong map error
	 */
	WPGMZA.Map.prototype.removePolygon = function(polygon)
	{
		if(!(polygon instanceof WPGMZA.Polygon))
			throw new Error("Argument must be an instance of WPGMZA.Polygon");
		
		if(polygon.map !== this)
			throw new Error("Wrong map error");
		
		polygon.map = null;
		
		this.polygons.splice(this.polygons.indexOf(polygon), 1);
		this.dispatchEvent({type: "polygonremoved", polygon: polygon});
	}
	
	/**
	 * Gets a polygon by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the polygon to get
	 * @return {WPGMZA.Polygon|null} The polygon, or null if no polygon with the specified ID is found
	 */
	WPGMZA.Map.prototype.getPolygonByID = function(id)
	{
		for(var i = 0; i < this.polygons.length; i++)
		{
			if(this.polygons[i].id == id)
				return this.polygons[i];
		}
		
		return null;
	}
	
	/**
	 * Removes a polygon by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the polygon to remove
	 */
	WPGMZA.Map.prototype.removePolygonByID = function(id)
	{
		var polygon = this.getPolygonByID(id);
		
		if(!polygon)
			return;
		
		this.removePolygon(polygon);
	}
	
	/**
	 * Gets a polyline by ID
	 * @return void
	 */
	WPGMZA.Map.prototype.getPolylineByID = function(id)
	{
		for(var i = 0; i < this.polylines.length; i++)
		{
			if(this.polylines[i].id == id)
				return this.polylines[i];
		}
		
		return null;
	}
	
	/**
	 * Adds the specified polyline to this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Polyline} polyline The polyline to add
	 * @fires polylineadded
	 * @throws Argument must be an instance of WPGMZA.Polyline
	 */
	WPGMZA.Map.prototype.addPolyline = function(polyline)
	{
		if(!(polyline instanceof WPGMZA.Polyline))
			throw new Error("Argument must be an instance of WPGMZA.Polyline");
		
		polyline.map = this;
		
		this.polylines.push(polyline);
		this.dispatchEvent({type: "polylineadded", polyline: polyline});
	}
	
	/**
	 * Removes the specified polyline from this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Polyline} polyline The polyline to remove
	 * @fires polylineremoved
	 * @throws Argument must be an instance of WPGMZA.Polyline
	 * @throws Wrong map error
	 */
	WPGMZA.Map.prototype.removePolyline = function(polyline)
	{
		if(!(polyline instanceof WPGMZA.Polyline))
			throw new Error("Argument must be an instance of WPGMZA.Polyline");
		
		if(polyline.map !== this)
			throw new Error("Wrong map error");
		
		polyline.map = null;
		
		this.polylines.splice(this.polylines.indexOf(polyline), 1);
		this.dispatchEvent({type: "polylineremoved", polyline: polyline});
	}
	
	/**
	 * Gets a polyline by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the polyline to get
	 * @return {WPGMZA.Polyline|null} The polyline, or null if no polyline with the specified ID is found
	 */
	WPGMZA.Map.prototype.getPolylineByID = function(id)
	{
		for(var i = 0; i < this.polylines.length; i++)
		{
			if(this.polylines[i].id == id)
				return this.polylines[i];
		}
		
		return null;
	}
	
	/**
	 * Removes a polyline by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the polyline to remove
	 */
	WPGMZA.Map.prototype.removePolylineByID = function(id)
	{
		var polyline = this.getPolylineByID(id);
		
		if(!polyline)
			return;
		
		this.removePolyline(polyline);
	}
	
	/**
	 * Adds the specified circle to this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Circle} circle The circle to add
	 * @fires polygonadded
	 * @throws Argument must be an instance of WPGMZA.Circle
	 */
	WPGMZA.Map.prototype.addCircle = function(circle)
	{
		if(!(circle instanceof WPGMZA.Circle))
			throw new Error("Argument must be an instance of WPGMZA.Circle");
		
		circle.map = this;
		
		this.circles.push(circle);
		this.dispatchEvent({type: "circleadded", circle: circle});
	}
	
	/**
	 * Removes the specified circle from this map
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {WPGMZA.Circle} circle The circle to remove
	 * @fires circleremoved
	 * @throws Argument must be an instance of WPGMZA.Circle
	 * @throws Wrong map error
	 */
	WPGMZA.Map.prototype.removeCircle = function(circle)
	{
		if(!(circle instanceof WPGMZA.Circle))
			throw new Error("Argument must be an instance of WPGMZA.Circle");
		
		if(circle.map !== this)
			throw new Error("Wrong map error");
		
		circle.map = null;
		
		this.circles.splice(this.circles.indexOf(circle), 1);
		this.dispatchEvent({type: "circleremoved", circle: circle});
	}
	
	/**
	 * Gets a circle by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the circle to get
	 * @return {WPGMZA.Circle|null} The circle, or null if no circle with the specified ID is found
	 */
	WPGMZA.Map.prototype.getCircleByID = function(id)
	{
		for(var i = 0; i < this.circles.length; i++)
		{
			if(this.circles[i].id == id)
				return this.circles[i];
		}
		
		return null;
	}
	
	/**
	 * Removes a circle by ID
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {int} id The ID of the circle to remove
	 */
	WPGMZA.Map.prototype.removeCircleByID = function(id)
	{
		var circle = this.getCircleByID(id);
		
		if(!circle)
			return;
		
		this.removeCircle(circle);
	}
	
	WPGMZA.Map.prototype.nudgeLatLng = function(latLng, x, y)
	{
		var pixels = this.latLngToPixels(latLng);
		
		pixels.x += parseFloat(x);
		pixels.y += parseFloat(y);
		
		if(isNaN(pixels.x) || isNaN(pixels.y))
			throw new Error("Invalid coordinates supplied");
		
		return this.pixelsToLatLng(pixels);
	}
	
	/**
	 * Nudges the map viewport by the given pixel coordinates
	 * @method
	 * @memberof WPGMZA.Map
	 * @param {number} x Number of pixels to nudge along the x axis
	 * @param {number} y Number of pixels to nudge along the y axis
	 * @throws Invalid coordinates supplied
	 */
	WPGMZA.Map.prototype.nudge = function(x, y)
	{
		var nudged = this.nudgeLatLng(this.getCenter(), x, y);
		
		this.setCenter(nudged);
	}
	
	WPGMZA.Map.prototype.animateNudge = function(x, y, origin, milliseconds)
	{
		var nudged;
	
		if(!origin)
			origin = this.getCenter();
		else if(!(origin instanceof WPGMZA.LatLng))
			throw new Error("Origin must be an instance of WPGMZA.LatLng");

		nudged = this.nudgeLatLng(origin, x, y);
		
		if(!milliseconds)
			milliseconds = WPGMZA.getScrollAnimationDuration();
		
		$(this).animate({
			lat: nudged.lat,
			lng: nudged.lng
		}, milliseconds);
	}
	
	/**
	 * Called when the window resizes
	 * @method
	 * @memberof WPGMZA.Map
	 */
	WPGMZA.Map.prototype.onWindowResize = function(event)
	{
		
	}
	
	/**
	 * Called when the engine map div is resized
	 * @method
	 * @memberof WPGMZA.Map
	 */
	WPGMZA.Map.prototype.onElementResized = function(event)
	{
		
	}
	
	/**
	 * Called when the map viewport bounds change. Fires the legacy bounds_changed event.
	 * @method
	 * @memberof WPGMZA.Map
	 * @fires boundschanged
	 * @fires bounds_changed
	 */
	WPGMZA.Map.prototype.onBoundsChanged = function(event)
	{
		// Native events
		this.trigger("boundschanged");
		
		// Google / legacy compatibility events
		this.trigger("bounds_changed");
	}
	
	/**
	 * Called when the map viewport becomes idle (eg movement done, tiles loaded)
	 * @method
	 * @memberof WPGMZA.Map
	 * @fires idle
	 */
	WPGMZA.Map.prototype.onIdle = function(event)
	{
		this.trigger("idle");
	}

	WPGMZA.Map.prototype.hasVisibleMarkers = function(event)
	{
		// see how many markers is visible
		var markers_visible = 0;

		// loop through the markers
		for(var marker_id in marker_array)
		{
			// find markers on map after search
			var marker = marker_array[marker_id];
			
			// NB: We check whether the marker is on a map or not here, Pro toggles visibility, basic adds and removes markers
			if(marker.isFilterable && marker.getMap())
			{
				// count markers visible
				markers_visible++;
				break;			
			}
		}

		return markers_visible > 0; // Returns true if markers are visible, false if not
	}
	
	WPGMZA.Map.prototype.closeAllInfoWindows = function()
	{
		this.markers.forEach(function(marker) {
			
			if(marker.infoWindow)
				marker.infoWindow.close();
				
		});
	}
	
});

// js/v8/maps-engine-dialog.js
/**
 * @namespace WPGMZA
 * @module MapsEngineDialog
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * The modal dialog presented to the user in the map edit page, prompting them to choose a map engine, if they haven't done so already
	 * @class WPGMZA.MapEngineDialog
	 * @constructor WPGMZA.MapEngineDialog
	 * @memberof WPGMZA
	 * @param {HTMLElement} element to create modal dialog from
	 */
	WPGMZA.MapsEngineDialog = function(element)
	{
		var self = this;
		
		this.element = element;
		
		if(window.wpgmzaUnbindSaveReminder)
			window.wpgmzaUnbindSaveReminder();
		
		$(element).show();
		$(element).remodal().open();
		
		$(element).find("input:radio").on("change", function(event) {
			
			$("#wpgmza-confirm-engine").prop("disabled", false);
			
		});
		
		$("#wpgmza-confirm-engine").on("click", function(event) {
			
			self.onButtonClicked(event);
			
		});
	}
	
	/**
	 * Triggered when an engine is selected. Makes an AJAX call to the server to save the selected engine.
	 * @method
	 * @memberof WPGMZA.MapEngineDialog
	 * @param {object} event The click event from the selected button.
	 */
	WPGMZA.MapsEngineDialog.prototype.onButtonClicked = function(event)
	{
		$(event.target).prop("disabled", true);
		
		$.ajax(WPGMZA.ajaxurl, {
			method: "POST",
			data: {
				action: "wpgmza_maps_engine_dialog_set_engine",
				engine: $("[name='wpgmza_maps_engine']:checked").val(),
				nonce: $("#wpgmza-maps-engine-dialog").attr("data-ajax-nonce")
			},
			success: function(response, status, xhr) {
				window.location.reload();
			}
		});
	}
	
	$(window).on("load", function(event) {
		
		var element = $("#wpgmza-maps-engine-dialog");
		
		if(!element.length)
			return;
		
		if(WPGMZA.settings.wpgmza_maps_engine_dialog_done)
			return;
		
		if(WPGMZA.settings.wpgmza_google_maps_api_key && WPGMZA.settings.wpgmza_google_maps_api_key.length)
			return;
		
		WPGMZA.mapsEngineDialog = new WPGMZA.MapsEngineDialog(element);
		
	});
	
});

// js/v8/marker-filter.js
/**
 * @namespace WPGMZA
 * @module MarkerFilter
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.MarkerFilter = function(map)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.call(this);
		
		this.map = map;
	}
	
	WPGMZA.MarkerFilter.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.MarkerFilter.prototype.constructor = WPGMZA.MarkerFilter;
	
	WPGMZA.MarkerFilter.createInstance = function(map)
	{
		return new WPGMZA.MarkerFilter(map);
	}
	
	WPGMZA.MarkerFilter.prototype.getFilteringParameters = function()
	{
		var params = {map_id: this.map.id};
		
		if(this.map.storeLocator)
			params = $.extend(params, this.map.storeLocator.getFilteringParameters());
		
		return params;
	}
	
	WPGMZA.MarkerFilter.prototype.update = function()
	{
		// NB: This function takes no action. The client can hide and show markers based on radius without putting load on the server. This function is only used by the ProMarkerFilter module
	}
	
	WPGMZA.MarkerFilter.prototype.onFilteringComplete = function(results)
	{
		
	}
	
});

// js/v8/marker-panel.js
/**
 * @namespace WPGMZA
 * @module MarkerPanel
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.MarkerPanel = function(element)
	{
		this.element = element;
	}
	
	$(window).on("load", function(event) {
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_MAP_EDIT)
			WPGMZA.mapEditPage.markerPanel = new WPGMZA.MarkerPanel($("#wpgmza-marker-edit-panel")[0]);
		
	});
	
});

// js/v8/marker.js
/**
 * @namespace WPGMZA
 * @module Marker
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Base class for markers. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.Marker
	 * @constructor WPGMZA.Marker
	 * @memberof WPGMZA
	 * @param {object} [row] Data to map to this object (eg from the database)
	 * @augments WPGMZA.MapObject
	 */
	WPGMZA.Marker = function(row)
	{
		var self = this;
		
		this._offset = {x: 0, y: 0};
		
		WPGMZA.assertInstanceOf(this, "Marker");
		
		this.lat = "36.778261";
		this.lng = "-119.4179323999";
		this.address = "California";
		this.title = null;
		this.description = "";
		this.link = "";
		this.icon = "";
		this.approved = 1;
		this.pic = null;
		
		this.isFilterable = true;
		this.disableInfoWindow = false;
		
		WPGMZA.MapObject.apply(this, arguments);
		
		if(row && row.heatmap)
			return; // Don't listen for these events on heatmap markers.
		
		if(row)
			this.on("init", function(event) {
				if(row.position)
					this.setPosition(row.position);
				
				if(row.map)
					row.map.addMarker(this);
			});
		
		this.addEventListener("added", function(event) {
			self.onAdded(event);
		});
		
		this.handleLegacyGlobals(row);
	}
	
	WPGMZA.Marker.prototype = Object.create(WPGMZA.MapObject.prototype);
	WPGMZA.Marker.prototype.constructor = WPGMZA.Marker;
	
	/**
	 * Returns the contructor to be used by createInstance, depending on the selected maps engine.
	 * @method
	 * @memberof WPGMZA.Marker
	 * @return {function} The appropriate contructor
	 */
	WPGMZA.Marker.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				if(WPGMZA.isProVersion())
					return WPGMZA.OLProMarker;
				return WPGMZA.OLMarker;
				break;
				
			default:
				if(WPGMZA.isProVersion())
					return WPGMZA.GoogleProMarker;
				return WPGMZA.GoogleMarker;
				break;
		}
	}
	
	/**
	 * Creates an instance of a marker, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {object} [row] Data to map to this object (eg from the database)
	 */
	WPGMZA.Marker.createInstance = function(row)
	{
		var constructor = WPGMZA.Marker.getConstructor();
		return new constructor(row);
	}
	
	WPGMZA.Marker.ANIMATION_NONE			= "0";
	WPGMZA.Marker.ANIMATION_BOUNCE			= "1";
	WPGMZA.Marker.ANIMATION_DROP			= "2";
	
	Object.defineProperty(WPGMZA.Marker.prototype, "offsetX", {
		
		get: function()
		{
			return this._offset.x;
		},
		
		set: function(value)
		{
			this._offset.x = value;
			this.updateOffset();
		}
		
	});
	
	Object.defineProperty(WPGMZA.Marker.prototype, "offsetY", {
		
		get: function()
		{
			return this._offset.y;
		},
		
		set: function(value)
		{
			this._offset.y = value;
			this.updateOffset();
		}
		
	});
	
	/**
	 * Called when the marker has been added to a map
	 * @method
	 * @memberof WPGMZA.Marker
	 * @listens module:WPGMZA.Marker~added
	 * @fires module:WPGMZA.Marker~select When this marker is targeted by the marker shortcode attribute
	 */
	WPGMZA.Marker.prototype.onAdded = function(event)
	{
		var self = this;
		
		this.addEventListener("click", function(event) {
			self.onClick(event);
		});
		
		this.addEventListener("mouseover", function(event) {
			self.onMouseOver(event);
		});
		
		this.addEventListener("select", function(event) {
			self.onSelect(event);
		});
		
		if(this.map.settings.marker == this.id)
			self.trigger("select");
		
		if(this.infoopen == "1")
			this.openInfoWindow();
	}
	
	WPGMZA.Marker.prototype.handleLegacyGlobals = function(row)
	{
		if(!(WPGMZA.settings.useLegacyGlobals && this.map_id && this.id))
			return;
		
		var m;
		if(WPGMZA.pro_version && (m = WPGMZA.pro_version.match(/\d+/)))
		{
			if(m[0] <= 7)
				return; // Don't touch the legacy globals
		}
		
		if(!window.marker_array)
			window.marker_array = {};
		
		if(!marker_array[this.map_id])
			marker_array[this.map_id] = [];
		
		marker_array[this.map_id][this.id] = this;
		
		if(!window.wpgmaps_localize_marker_data)
			window.wpgmaps_localize_marker_data = {};
		
		if(!wpgmaps_localize_marker_data[this.map_id])
			wpgmaps_localize_marker_data[this.map_id] = [];
		
		var cloned = $.extend({marker_id: this.id}, row);
		wpgmaps_localize_marker_data[this.map_id][this.id] = cloned;
	}
	
	WPGMZA.Marker.prototype.initInfoWindow = function()
	{
		if(this.infoWindow)
			return;
		
		this.infoWindow = WPGMZA.InfoWindow.createInstance();
	}
	
	/**
	 * Placeholder for future use
	 * @method
	 * @memberof WPGMZA.Marker
	 */
	WPGMZA.Marker.prototype.openInfoWindow = function()
	{
		if(!this.map)
		{
			console.warn("Cannot open infowindow for marker with no map");
			return;
		}
		
		// NB: This is a workaround for "undefined" in InfoWindows (basic only) on map edit page
		if(WPGMZA.currentPage == "map-edit" && !WPGMZA.pro_version)
			return;
		
		if(this.map.lastInteractedMarker)
			this.map.lastInteractedMarker.infoWindow.close();
		this.map.lastInteractedMarker = this;
		
		this.initInfoWindow();
		this.infoWindow.open(this.map, this);
	}
	
	/**
	 * Called when the marker has been clicked
	 * @method
	 * @memberof WPGMZA.Marker
	 * @listens module:WPGMZA.Marker~click
	 */
	WPGMZA.Marker.prototype.onClick = function(event)
	{
		
	}
	
	/**
	 * Called when the marker has been selected, either by the icon being clicked, or from a marker listing
	 * @method
	 * @memberof WPGMZA.Marker
	 * @listens module:WPGMZA.Marker~select
	 */
	WPGMZA.Marker.prototype.onSelect = function(event)
	{
		this.openInfoWindow();
	}
	
	/**
	 * Called when the user hovers the mouse over this marker
	 * @method
	 * @memberof WPGMZA.Marker
	 * @listens module:WPGMZA.Marker~mouseover
	 */
	WPGMZA.Marker.prototype.onMouseOver = function(event)
	{
		if(this.map.settings.info_window_open_by == WPGMZA.InfoWindow.OPEN_BY_HOVER)
			this.openInfoWindow();
	}
	
	/**
	 * Gets the marker icon image URL, without the protocol prefix
	 * @method
	 * @memberof WPGMZA.Marker
	 * @return {string} The URL to the markers icon image
	 */
	WPGMZA.Marker.prototype.getIcon = function()
	{
		function stripProtocol(url)
		{
			if(typeof url != "string")
				return url;
			
			return url.replace(/^http(s?):/, "");
		}
		
		if(WPGMZA.defaultMarkerIcon)
			return stripProtocol(WPGMZA.defaultMarkerIcon);
		
		return stripProtocol(WPGMZA.settings.default_marker_icon);
	}
	
	/**
	 * Gets the position of the marker
	 * @method
	 * @memberof WPGMZA.Marker
	 * @return {object} LatLng literal of this markers position
	 */
	WPGMZA.Marker.prototype.getPosition = function()
	{
		return new WPGMZA.LatLng({
			lat: parseFloat(this.lat),
			lng: parseFloat(this.lng)
		});
	}
	
	/**
	 * Sets the position of the marker.
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {object|WPGMZA.LatLng} latLng The position either as a LatLng literal or instance of WPGMZA.LatLng.
	 */
	WPGMZA.Marker.prototype.setPosition = function(latLng)
	{
		if(latLng instanceof WPGMZA.LatLng)
		{
			this.lat = latLng.lat;
			this.lng = latLng.lng;
		}
		else
		{
			this.lat = parseFloat(latLng.lat);
			this.lng = parseFloat(latLng.lng);
		}
	}
	
	WPGMZA.Marker.prototype.setOffset = function(x, y)
	{
		this._offset.x = x;
		this._offset.y = y;
		
		this.updateOffset();
	}
	
	WPGMZA.Marker.prototype.updateOffset = function()
	{
		
	}
	
	/**
	 * Returns the animation set on this marker (see WPGMZA.Marker ANIMATION_* constants).
	 * @method
	 * @memberof WPGMZA.Marker
	 */
	WPGMZA.Marker.prototype.getAnimation = function(animation)
	{
		return this.settings.animation;
	}
	
	/**
	 * Sets the animation for this marker (see WPGMZA.Marker ANIMATION_* constants).
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {int} animation The animation to set.
	 */
	WPGMZA.Marker.prototype.setAnimation = function(animation)
	{
		this.settings.animation = animation;
	}
	
	/**
	 * Get the marker visibility
	 * @method
	 * @todo Implement
	 * @memberof WPGMZA.Marker
	 */
	WPGMZA.Marker.prototype.getVisible = function()
	{
		
	}
	
	/**
	 * Set the marker visibility. This is used by the store locator etc. and is not a setting. Closes the InfoWindow if the marker is being hidden and the InfoWindow for this marker is open.
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {bool} visible Whether the marker should be visible or not
	 */
	WPGMZA.Marker.prototype.setVisible = function(visible)
	{
		if(!visible && this.infoWindow)
			this.infoWindow.close();
	}
	
	WPGMZA.Marker.prototype.getMap = function()
	{
		return this.map;
	}
	
	/**
	 * Sets the map this marker should be displayed on. If it is already on a map, it will be removed from that map first, before being added to the supplied map.
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {WPGMZA.Map} map The map to add this markmer to
	 */
	WPGMZA.Marker.prototype.setMap = function(map)
	{
		if(!map)
		{
			if(this.map)
				this.map.removeMarker(this);
		}
		else
			map.addMarker(this);
		
		this.map = map;
	}
	
	/**
	 * Gets whether this marker is draggable or not
	 * @method
	 * @memberof WPGMZA.Marker
	 * @return {bool} True if the marker is draggable
	 */
	WPGMZA.Marker.prototype.getDraggable = function()
	{
		
	}
	
	/**
	 * Sets whether the marker is draggable
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {bool} draggable Set to true to make this marker draggable
	 */
	WPGMZA.Marker.prototype.setDraggable = function(draggable)
	{
		
	}
	
	/**
	 * Sets options on this marker
	 * @method
	 * @memberof WPGMZA.Marker
	 * @param {object} options An object containing the options to be set
	 */
	WPGMZA.Marker.prototype.setOptions = function(options)
	{
		
	}
	
	WPGMZA.Marker.prototype.setOpacity = function(opacity)
	{
		
	}
	
	/**
	 * Centers the map this marker belongs to on this marker
	 * @method
	 * @memberof WPGMZA.Marker
	 * @throws Marker hasn't been added to a map
	 */
	WPGMZA.Marker.prototype.panIntoView = function()
	{
		if(!this.map)
			throw new Error("Marker hasn't been added to a map");
		
		this.map.setCenter(this.getPosition());
	}
	
	/**
	 * Overrides MapObject.toJSON, serializes the marker to a JSON object
	 * @method
	 * @memberof WPGMZA.Marker
	 * @return {object} A JSON representation of this marker
	 */
	WPGMZA.Marker.prototype.toJSON = function()
	{
		var result = WPGMZA.MapObject.prototype.toJSON.call(this);
		var position = this.getPosition();
		
		$.extend(result, {
			lat: position.lat,
			lng: position.lng,
			address: this.address,
			title: this.title,
			description: this.description,
			link: this.link,
			icon: this.icon,
			pic: this.pic,
			approved: this.approved
		});
		
		return result;
	}
	
	
});

// js/v8/modern-store-locator-circle.js
/**
 * @namespace WPGMZA
 * @module ModernStoreLocatorCircle
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * This is the base class the modern store locator circle. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.ModernStoreLocatorCircle
	 * @constructor WPGMZA.ModernStoreLocatorCircle
	 * @param {int} map_id The ID of the map this circle belongs to
	 * @param {object} [settings] Settings to pass into this circle, such as strokeColor
	 */
	WPGMZA.ModernStoreLocatorCircle = function(map_id, settings) {
		var self = this;
		var map;
		
		if(WPGMZA.isProVersion())
			map = this.map = MYMAP[map_id].map;
		else
			map = this.map = MYMAP.map;
		
		this.map_id = map_id;
		this.mapElement = map.element;
		this.mapSize = {
			width:  $(this.mapElement).width(),
			height: $(this.mapElement).height()
		};
			
		this.initCanvasLayer();
		
		this.settings = {
			center: new WPGMZA.LatLng(0, 0),
			radius: 1,
			color: "#63AFF2",
			
			shadowColor: "white",
			shadowBlur: 4,
			
			centerRingRadius: 10,
			centerRingLineWidth: 3,

			numInnerRings: 9,
			innerRingLineWidth: 1,
			innerRingFade: true,
			
			numOuterRings: 7,
			
			ringLineWidth: 1,
			
			mainRingLineWidth: 2,
			
			numSpokes: 6,
			spokesStartAngle: Math.PI / 2,
			
			numRadiusLabels: 6,
			radiusLabelsStartAngle: Math.PI / 2,
			radiusLabelFont: "13px sans-serif",
			
			visible: false
		};
		
		if(settings)
			this.setOptions(settings);
	};
	
	/**
	 * Returns the contructor to be used by createInstance, depending on the selected maps engine.
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @return {function} The appropriate contructor
	 */
	WPGMZA.ModernStoreLocatorCircle.createInstance = function(map, settings) {
		
		if(WPGMZA.settings.engine == "google-maps")
			return new WPGMZA.GoogleModernStoreLocatorCircle(map, settings);
		else
			return new WPGMZA.OLModernStoreLocatorCircle(map, settings);
		
	};
	
	/**
	 * Abstract function to initialize the canvas layer
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.initCanvasLayer = function() {
		
	}
	
	/**
	 * Handles the map viewport being resized
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.onResize = function(event) { 
		this.draw();
	};
	
	/**
	 * Updates and redraws the circle
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.onUpdate = function(event) { 
		this.draw();
	};
	
	/**
	 * Sets options on the circle (for example, strokeColor)
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @param {object} options An object of options to iterate over and set on this circle.
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.setOptions = function(options) {
		for(var name in options)
		{
			var functionName = "set" + name.substr(0, 1).toUpperCase() + name.substr(1);
			
			if(typeof this[functionName] == "function")
				this[functionName](options[name]);
			else
				this.settings[name] = options[name];
		}
	};
	
	/**
	 * Gets the resolution scale for drawing on the circles canvas.
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @return {number} The device pixel ratio, or 1 where that is not present.
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getResolutionScale = function() {
		return window.devicePixelRatio || 1;
	};
	
	/**
	 * Returns the center of the circle
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @return {object} A latLng literal
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getCenter = function() {
		return this.getPosition();
	};
	
	/**
	 * Sets the center of the circle
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @param {WPGMZA.LatLng|object} A LatLng literal or instance of WPGMZA.LatLng
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.setCenter = function(value) {
		this.setPosition(value);
	};
	
	/**
	 * Gets the center of the circle
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @return {object} The center as a LatLng literal
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getPosition = function() {
		return this.settings.center;
	};
	
	/**
	 * Alias for setCenter
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.setPosition = function(position) {
		this.settings.center = position;
	};
	
	/**
	 * Gets the circle radius, in kilometers
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @return {number} The circles radius, in kilometers
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getRadius = function() {
		return this.settings.radius;
	};
	
	/**
	 * Sets the circles radius, in kilometers
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @param {number} radius The radius, in kilometers
	 * @throws Invalid radius
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.setRadius = function(radius) {
		
		if(isNaN(radius))
			throw new Error("Invalid radius");
		
		this.settings.radius = radius;
	};
	
	/**
	 * Gets the visibility of the circle
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @return {bool} Whether or not the circle is visible
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getVisible = function() {
		return this.settings.visible;
	};
	
	/**
	 * Sets the visibility of the circle
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @param {bool} visible Whether the circle should be visible
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.setVisible = function(visible) {
		this.settings.visible = visible;
	};
	
	/**
	 * Abstract function to get the transformed circle radius (see subclasses)
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @param {number} km The input radius, in kilometers
	 * @throws Abstract function called
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getTransformedRadius = function(km)
	{
		throw new Error("Abstract function called");
	}
	
	/**
	 * Abstract function to set the canvas context
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @param {string} type The context type
	 * @throws Abstract function called
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getContext = function(type)
	{
		throw new Error("Abstract function called");
	}
	
	/**
	 * Abstract function to get the canvas dimensions
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 * @throws Abstract function called
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.getCanvasDimensions = function()
	{
		throw new Error("Abstract function called");
	}
	
	/**
	 * Validates the circle settings and corrects them where they are invalid
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.validateSettings = function()
	{
		if(!WPGMZA.isHexColorString(this.settings.color))
			this.settings.color = "#63AFF2";
	}
	
	/**
	 * Draws the circle to the canvas
	 * @method
	 * @memberof WPGMZA.ModernStoreLocatorCircle
	 */
	WPGMZA.ModernStoreLocatorCircle.prototype.draw = function() {
		
		this.validateSettings();
		
		var settings = this.settings;
		var canvasDimensions = this.getCanvasDimensions();
		
        var canvasWidth = canvasDimensions.width;
        var canvasHeight = canvasDimensions.height;
		
		var map = this.map;
		var resolutionScale = this.getResolutionScale();
		
		context = this.getContext("2d");
        context.clearRect(0, 0, canvasWidth, canvasHeight);

		if(!settings.visible)
			return;
		
		context.shadowColor = settings.shadowColor;
		context.shadowBlur = settings.shadowBlur;
		
		// NB: 2018/02/13 - Left this here in case it needs to be calibrated more accurately
		/*if(!this.testCircle)
		{
			this.testCircle = new google.maps.Circle({
				strokeColor: "#ff0000",
				strokeOpacity: 0.5,
				strokeWeight: 3,
				map: this.map,
				center: this.settings.center
			});
		}
		
		this.testCircle.setCenter(settings.center);
		this.testCircle.setRadius(settings.radius * 1000);*/
		
        // Reset transform
        context.setTransform(1, 0, 0, 1, 0, 0);
        
        var scale = this.getScale();
        context.scale(scale, scale);

		// Translate by world origin
		var offset = this.getWorldOriginOffset();
		context.translate(offset.x, offset.y);

        // Get center and project to pixel space
		var center = new WPGMZA.LatLng(this.settings.center);
		var worldPoint = this.getCenterPixels();
		
		var rgba = WPGMZA.hexToRgba(settings.color);
		var ringSpacing = this.getTransformedRadius(settings.radius) / (settings.numInnerRings + 1);
		
		// TODO: Implement gradients for color and opacity
		
		// Inside circle (fixed?)
        context.strokeStyle = settings.color;
		context.lineWidth = (1 / scale) * settings.centerRingLineWidth;
		
		context.beginPath();
		context.arc(
			worldPoint.x, 
			worldPoint.y, 
			this.getTransformedRadius(settings.centerRingRadius) / scale, 0, 2 * Math.PI
		);
		context.stroke();
		context.closePath();
		
		// Spokes
		var radius = this.getTransformedRadius(settings.radius) + (ringSpacing * settings.numOuterRings) + 1;
		var grad = context.createRadialGradient(0, 0, 0, 0, 0, radius);
		var rgba = WPGMZA.hexToRgba(settings.color);
		var start = WPGMZA.rgbaToString(rgba), end;
		var spokeAngle;
		
		rgba.a = 0;
		end = WPGMZA.rgbaToString(rgba);
		
		grad.addColorStop(0, start);
		grad.addColorStop(1, end);
		
		context.save();
		
		context.translate(worldPoint.x, worldPoint.y);
		context.strokeStyle = grad;
		context.lineWidth = 2 / scale;
		
		for(var i = 0; i < settings.numSpokes; i++)
		{
			spokeAngle = settings.spokesStartAngle + (Math.PI * 2) * (i / settings.numSpokes);
			
			x = Math.cos(spokeAngle) * radius;
			y = Math.sin(spokeAngle) * radius;
			
			context.setLineDash([2 / scale, 15 / scale]);
			
			context.beginPath();
			context.moveTo(0, 0);
			context.lineTo(x, y);
			context.stroke();
		}
		
		context.setLineDash([]);
		
		context.restore();
		
		// Inner ringlets
		context.lineWidth = (1 / scale) * settings.innerRingLineWidth;
		
		for(var i = 1; i <= settings.numInnerRings; i++)
		{
			var radius = i * ringSpacing;
			
			if(settings.innerRingFade)
				rgba.a = 1 - (i - 1) / settings.numInnerRings;
			
			context.strokeStyle = WPGMZA.rgbaToString(rgba);
			
			context.beginPath();
			context.arc(worldPoint.x, worldPoint.y, radius, 0, 2 * Math.PI);
			context.stroke();
			context.closePath();
		}
		
		// Main circle
		context.strokeStyle = settings.color;
		context.lineWidth = (1 / scale) * settings.centerRingLineWidth;
		
		context.beginPath();
		context.arc(worldPoint.x, worldPoint.y, this.getTransformedRadius(settings.radius), 0, 2 * Math.PI);
		context.stroke();
		context.closePath();
		
		// Outer ringlets
		var radius = radius + ringSpacing;
		for(var i = 0; i < settings.numOuterRings; i++)
		{
			if(settings.innerRingFade)
				rgba.a = 1 - i / settings.numOuterRings;
			
			context.strokeStyle = WPGMZA.rgbaToString(rgba);
			
			context.beginPath();
			context.arc(worldPoint.x, worldPoint.y, radius, 0, 2 * Math.PI);
			context.stroke();
			context.closePath();
		
			radius += ringSpacing;
		}
		
		// Text
		if(settings.numRadiusLabels > 0)
		{
			var m;
			var radius = this.getTransformedRadius(settings.radius);
			var clipRadius = (12 * 1.1) / scale;
			var x, y;
			
			if(m = settings.radiusLabelFont.match(/(\d+)px/))
				clipRadius = (parseInt(m[1]) / 2 * 1.1) / scale;
			
			context.font = settings.radiusLabelFont;
			context.textAlign = "center";
			context.textBaseline = "middle";
			context.fillStyle = settings.color;
			
			context.save();
			
			context.translate(worldPoint.x, worldPoint.y)
			
			for(var i = 0; i < settings.numRadiusLabels; i++)
			{
				var spokeAngle = settings.radiusLabelsStartAngle + (Math.PI * 2) * (i / settings.numRadiusLabels);
				var textAngle = spokeAngle + Math.PI / 2;
				var text = settings.radiusString;
				var width;
				
				if(Math.sin(spokeAngle) > 0)
					textAngle -= Math.PI;
				
				x = Math.cos(spokeAngle) * radius;
				y = Math.sin(spokeAngle) * radius;
				
				context.save();
				
				context.translate(x, y);
				
				context.rotate(textAngle);
				context.scale(1 / scale, 1 / scale);
				
				width = context.measureText(text).width;
				height = width / 2;
				context.clearRect(-width, -height, 2 * width, 2 * height);
				
				context.fillText(settings.radiusString, 0, 0);
				
				context.restore();
			}
			
			context.restore();
		}
	}
	
});

// js/v8/native-maps-icon.js
/**
 * @namespace WPGMZA
 * @module NativeMapsAppIcon
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Small utility class to create an icon for the native maps app, an Apple icon on iOS devices, a Google icon on other devices
	 * @method WPGMZA.NativeMapsAppIcon
	 * @constructor WPGMZA.NativeMapsAppIcon
	 * @memberof WPGMZA
	 */
	WPGMZA.NativeMapsAppIcon = function() {
		if(navigator.userAgent.match(/^Apple|iPhone|iPad|iPod/))
		{
			this.type = "apple";
			this.element = $('<span><i class="fab fa fa-apple" aria-hidden="true"></i></span>');
		}
		else
		{
			this.type = "google";
			this.element = $('<span><i class="fab fa fa-google" aria-hidden="true"></i></span>');
		}
	};
	
});

// js/v8/polyfills.js
/**
 * @namespace WPGMZA
 * @module Polyfills
 * @requires WPGMZA
 */
jQuery(function($) {

	// IE11 polyfill for slice not being implemented on Uint8Array (used by text.js)
	if (!Uint8Array.prototype.slice) {
		Object.defineProperty(Uint8Array.prototype, 'slice', {
			value: function (begin, end) {
				return new Uint8Array(Array.prototype.slice.call(this, begin, end));
			}
		});
	}
	
	// Safari polyfill for Enfold themes TypeError: 'undefined' is not a valid argument for 'in'
	if(WPGMZA.isSafari() && !window.external)
		window.external = {};

});

// js/v8/polygon.js
/**
 * @namespace WPGMZA
 * @module Polygon
 * @requires WPGMZA.MapObject
 */
jQuery(function($) {
	
	/**
	 * Base class for polygons. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.Polygon
	 * @constructor WPGMZA.Polygon
	 * @memberof WPGMZA
	 * @param {object} [row] Options to apply to this polygon.
	 * @param {object} [enginePolygon] An engine polygon, passed from the drawing manager. Used when a polygon has been created by a drawing manager.
	 * @augments WPGMZA.MapObject
	 */
	WPGMZA.Polygon = function(row, enginePolygon)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "Polygon");
		
		this.paths = null;
		this.title = null;
		this.name = null;
		this.link = null;
		
		WPGMZA.MapObject.apply(this, arguments);
	}
	
	WPGMZA.Polygon.prototype = Object.create(WPGMZA.MapObject.prototype);
	WPGMZA.Polygon.prototype.constructor = WPGMZA.Polygon;
	
	/**
	 * Returns the contructor to be used by createInstance, depending on the selected maps engine.
	 * @method
	 * @memberof WPGMZA.Polygon
	 * @return {function} The appropriate contructor
	 */
	WPGMZA.Polygon.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				if(WPGMZA.isProVersion())
					return WPGMZA.OLProPolygon;
				return WPGMZA.OLPolygon;
				break;
			
			default:
				if(WPGMZA.isProVersion())
					return WPGMZA.GoogleProPolygon;
				return WPGMZA.GooglePolygon;
				break;
		}
	}
	
	/**
	 * Creates an instance of a map, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.Polygon
	 * @param {object} [row] Options to apply to this polygon.
	 * @param {object} [enginePolygon] An engine polygon, passed from the drawing manager. Used when a polygon has been created by a drawing manager.
	 * @returns {WPGMZA.Polygon} An instance of WPGMZA.Polygon
	 */
	WPGMZA.Polygon.createInstance = function(row, engineObject)
	{
		var constructor = WPGMZA.Polygon.getConstructor();
		return new constructor(row, engineObject);
	}
	
	/**
	 * Returns a JSON representation of this polygon, for serialization
	 * @method
	 * @memberof WPGMZA.Polygon
	 * @returns {object} A JSON object representing this polygon
	 */
	WPGMZA.Polygon.prototype.toJSON = function()
	{
		var result = WPGMZA.MapObject.prototype.toJSON.call(this);
		
		$.extend(result, {
			name:		this.name,
			title:		this.title,
			link:		this.link,
		});
	
		return result;
	}
	
});

// js/v8/polyline.js
/**
 * @namespace WPGMZA
 * @module Polyline
 * @requires WPGMZA.MapObject
 */
jQuery(function($) {
	
	/**
	 * Base class for polylines. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.Polyline
	 * @constructor WPGMZA.Polyline
	 * @memberof WPGMZA
	 * @param {object} [row] Options to apply to this polyline.
	 * @param {object} [enginePolyline] An engine polyline, passed from the drawing manager. Used when a polyline has been created by a drawing manager.
	 * @augments WPGMZA.MapObject
	 */
	WPGMZA.Polyline = function(row, googlePolyline)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "Polyline");
		
		this.title = null;
		
		WPGMZA.MapObject.apply(this, arguments);
	}
	
	WPGMZA.Polyline.prototype = Object.create(WPGMZA.MapObject.prototype);
	WPGMZA.Polyline.prototype.constructor = WPGMZA.Polyline;
	
	/**
	 * Returns the contructor to be used by createInstance, depending on the selected maps engine.
	 * @method
	 * @memberof WPGMZA.Polyline
	 * @return {function} The appropriate contructor
	 */
	WPGMZA.Polyline.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return WPGMZA.OLPolyline;
				break;
			
			default:
				return WPGMZA.GooglePolyline;
				break;
		}
	}
	
	/**
	 * Creates an instance of a map, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.Polyline
	 * @param {object} [row] Options to apply to this polyline.
	 * @param {object} [enginePolyline] An engine polyline, passed from the drawing manager. Used when a polyline has been created by a drawing manager.
	 * @returns {WPGMZA.Polyline} An instance of WPGMZA.Polyline
	 */
	WPGMZA.Polyline.createInstance = function(row, engineObject)
	{
		var constructor = WPGMZA.Polyline.getConstructor();
		return new constructor(row, engineObject);
	}
	
	/**
	 * Gets the points on this polylines
	 * @return {array} An array of LatLng literals
	 */
	WPGMZA.Polyline.prototype.getPoints = function()
	{
		return this.toJSON().points;
	}
	
	/**
	 * Returns a JSON representation of this polyline, for serialization
	 * @method
	 * @memberof WPGMZA.Polyline
	 * @returns {object} A JSON object representing this polyline
	 */
	WPGMZA.Polyline.prototype.toJSON = function()
	{
		var result = WPGMZA.MapObject.prototype.toJSON.call(this);
		
		result.title = this.title;
		
		return result;
	}
	
	
});

// js/v8/popout-panel.js
/**
 * @namespace WPGMZA
 * @module PopoutPanel
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Common functionality for popout panels, which is the directions box, directions result box, and the modern style marker listing
	 * @class WPGMZA.PopoutPanel
	 * @constructor WPGMZA.PopoutPanel
	 * @memberof WPGMZA
	 */
	WPGMZA.PopoutPanel = function(element)
	{
		this.element = element;
	}
	
	/**
	 * Opens the direction box
	 * @method
	 * @memberof WPGMZA.PopoutPanel
	 */
	WPGMZA.PopoutPanel.prototype.open = function() {
		$(this.element).addClass("wpgmza-open");
	};
	
	/**
	 * Closes the direction box
	 * @method
	 * @memberof WPGMZA.PopoutPanel
	 */
	WPGMZA.PopoutPanel.prototype.close = function() {
		$(this.element).removeClass("wpgmza-open");
	};
	
});

// js/v8/rest-api.js
/**
 * @namespace WPGMZA
 * @module WPGMZA.RestAPI
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * Used to interact with the WordPress REST API. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.RestAPI
	 * @constructor WPGMZA.RestAPI
	 * @memberof WPGMZA
	 */
	WPGMZA.RestAPI = function()
	{
		WPGMZA.RestAPI.URL = WPGMZA.resturl;
		
		this.useAJAXFallback = false;
	}
	
	WPGMZA.RestAPI.CONTEXT_REST		= "REST";
	WPGMZA.RestAPI.CONTEXT_AJAX		= "AJAX";
	
	/**
	 * Creates an instance of a RestAPI, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.RestAPI
	 */
	WPGMZA.RestAPI.createInstance = function() 
	{
		return new WPGMZA.RestAPI();
	}
	
	Object.defineProperty(WPGMZA.RestAPI.prototype, "isCompressedPathVariableSupported", {
		
		get: function()
		{
			return WPGMZA.serverCanInflate && "Uint8Array" in window && "TextEncoder" in window;
		}
		
	});
	
	Object.defineProperty(WPGMZA.RestAPI.prototype, "isCompressedPathVariableAllowed", {
		
		get: function()
		{
			// NB: Pro 7 still has a "disable" setting. So use that if Pro 7 is installed.
			if(!WPGMZA.pro_version || WPGMZA.Version.compare(WPGMZA.pro_version, "8.0.0") >= WPGMZA.Version.EQUAL_TO)
				return !WPGMZA.settings.disable_compressed_path_variables;
			
			// Running Pro 7 or below
			return WPGMZA.settings.enable_compressed_path_variables;
		}
		
	});
	
	Object.defineProperty(WPGMZA.RestAPI.prototype, "maxURLLength", {
		
		get: function()
		{
			return 2083;
		}
		
	});
	
	WPGMZA.RestAPI.prototype.compressParams = function(params)
	{
		var suffix = "";
		
		if(params.markerIDs)
		{
			var markerIDs	= params.markerIDs.split(",");
			
			if(markerIDs.length > 1)
			{
				// NB: Only use Elias Fano encoding if more than one marker is present. The server side decoder does not correctly decode a single digit.
				var encoder		= new WPGMZA.EliasFano();
				var encoded		= encoder.encode(markerIDs);
				var compressed	= pako.deflate(encoded);
				var string		= Array.prototype.map.call(compressed, function(ch) {
					return String.fromCharCode(ch);
				}).join("");
				
				// NB: Append as another path component, this stops the code below performing base64 encoding twice and enlarging the request
				suffix = "/" + btoa(string).replace(/\//g, "-").replace(/=+$/, "");
				
				// NB: midcbp = Marker ID compressed buffer pointer, abbreviated to save space
				params.midcbp = encoded.pointer;
				
				delete params.markerIDs;
			}
		}
		
		var string		= JSON.stringify(params);
		var encoder		= new TextEncoder();
		var input		= encoder.encode(string);
		var compressed	= pako.deflate(input);
		var raw			= Array.prototype.map.call(compressed, function(ch) {
			return String.fromCharCode(ch);
		}).join("");
		
		var base64		= btoa(raw);
		return base64.replace(/\//g, "-").replace(/=+$/, "") + suffix;
	}
	
	function sendAJAXFallbackRequest(route, params)
	{
		var params = $.extend({}, params);
		
		if(!params.data)
			params.data = {};
		
		if("route" in params.data)
			throw new Error("Cannot send route through this method");
		
		if("action" in params.data)
			throw new Error("Cannot send action through this method");
		
		params.data.route = route;
		params.data.action = "wpgmza_rest_api_request";
		
		WPGMZA.restAPI.addNonce(route, params, WPGMZA.RestAPI.CONTEXT_AJAX);
		
		return $.ajax(WPGMZA.ajaxurl, params);
	}
	
	WPGMZA.RestAPI.prototype.getNonce = function(route)
	{
		var matches = [];
		
		for(var pattern in WPGMZA.restnoncetable)
		{
			var regex = new RegExp(pattern);
			
			if(route.match(regex))
				matches.push({
					pattern: pattern,
					nonce: WPGMZA.restnoncetable[pattern],
					length: pattern.length
				});
		}
		
		if(!matches.length)
			throw new Error("No nonce found for route");
		
		matches.sort(function(a, b) {
			return b.length - a.length;
		});
		
		return matches[0].nonce;
	}
	
	WPGMZA.RestAPI.prototype.addNonce = function(route, params, context)
	{
		var self = this;
		
		var setRESTNonce = function(xhr) {
			if(context == WPGMZA.RestAPI.CONTEXT_REST)
				xhr.setRequestHeader('X-WP-Nonce', WPGMZA.restnonce);
			
			if(params && params.method && !params.method.match(/^GET$/i))
				xhr.setRequestHeader('X-WPGMZA-Action-Nonce', self.getNonce(route));
		};
		
		if(!params.beforeSend)
			params.beforeSend = setRESTNonce;
		else
		{
			var base = params.beforeSend;
			
			params.beforeSend = function(xhr) {
				base(xhr);
				setRESTNonce(xhr);
			}
		}
	}
	
	/**
	 * Makes an AJAX to the REST API, this function is a wrapper for $.ajax
	 * @method
	 * @memberof WPGMZA.RestAPI
	 * @param {string} route The REST API route
	 * @param {object} params The request parameters, see http://api.jquery.com/jquery.ajax/
	 */
	WPGMZA.RestAPI.prototype.call = function(route, params)
	{
		if(this.useAJAXFallback)
			return sendAJAXFallbackRequest(route, params);
		
		var self = this;
		var attemptedCompressedPathVariable = false;
		var fallbackRoute = route;
		var fallbackParams = $.extend({}, params);
		
		if(typeof route != "string" || (!route.match(/^\//) && !route.match(/^http/)))
			throw new Error("Invalid route");
		
		if(WPGMZA.RestAPI.URL.match(/\/$/))
			route = route.replace(/^\//, "");
		
		if(!params)
			params = {};
		
		this.addNonce(route, params, WPGMZA.RestAPI.CONTEXT_REST);
		
		if(!params.error)
			params.error = function(xhr, status, message) {
				if(status == "abort")
					return;	// Don't report abort, let it happen silently
				
				switch(xhr.status)
				{
					case 401:
					case 403:
						// Report back to the server. This is usually due to a security plugin blocking REST requests for non-authenticated users
						$.post(WPGMZA.ajaxurl, {
							action: "wpgmza_report_rest_api_blocked"
						}, function(response) {});
						
						console.warn("The REST API was blocked. This is usually due to security plugins blocking REST requests for non-authenticated users.");
						
						this.useAJAXFallback = true;
						
						return sendAJAXFallbackRequest(fallbackRoute, fallbackParams);
						break;
					
					case 414:
						if(!attemptedCompressedPathVariable)
							break;
					
						// Fallback for HTTP 414 - Request too long with compressed requests
						fallbackParams.method = "POST";
						fallbackParams.useCompressedPathVariable = false;
						
						return WPGMZA.restAPI.call(fallbackRoute, fallbackParams);
					
						break;
				}
				
				throw new Error(message);
			}
		
		if(params.useCompressedPathVariable && 
			this.isCompressedPathVariableSupported && 
			this.isCompressedPathVariableAllowed)
		{
			var compressedParams = $.extend({}, params);
			var data = params.data;
			var base64 = this.compressParams(data);
			
			if(WPGMZA.isServerIIS)
				base64 = encodeURIComponent(base64);
			
			var compressedRoute = route.replace(/\/$/, "") + "/base64" + base64;
			var fullCompressedRoute = WPGMZA.RestAPI.URL + compressedRoute;
			
			compressedParams.method = "GET";
			delete compressedParams.data;
			
			if(params.cache === false)
				compressedParams.data = {
					skip_cache: 1
				};
			
			if(compressedRoute.length < this.maxURLLength)
			{
				attemptedCompressedPathVariable = true;
				
				route = compressedRoute;
				params = compressedParams;
			}
			else
			{
				// Fallback for when URL exceeds predefined length limit
				if(!WPGMZA.RestAPI.compressedPathVariableURLLimitWarningDisplayed)
					console.warn("Compressed path variable route would exceed URL length limit");
				
				WPGMZA.RestAPI.compressedPathVariableURLLimitWarningDisplayed = true;
			}
		}
		
		// NB: Support plain permalinks
		if(WPGMZA.RestAPI.URL.match(/\?/))
			route = route.replace(/\?/, "&");
		
		return $.ajax(WPGMZA.RestAPI.URL + route, params);
	}
	
	var nativeCallFunction = WPGMZA.RestAPI.call;
	WPGMZA.RestAPI.call = function()
	{
		console.warn("WPGMZA.RestAPI.call was called statically, did you mean to call the function on WPGMZA.restAPI?");
		
		nativeCallFunction.apply(this, arguments);
	}

	$(document.body).on("click", "#wpgmza-rest-api-blocked button.notice-dismiss", function(event) {
		
		WPGMZA.restAPI.call("/rest-api/", {
			method: "POST",
			data: {
				dismiss_blocked_notice: true
			}
		});
		
	});
	
});

// js/v8/settings-page.js
/**
 * @namespace WPGMZA
 * @module SettingsPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.SettingsPage = function()
	{
		$("#wpgmza-global-settings").tabs();
	}
	
	WPGMZA.SettingsPage.createInstance = function()
	{
		return new WPGMZA.SettingsPage();
	}
	
	$(window).on("load", function(event) {
		
		var useLegacyHTML = WPGMZA.settings.useLegacyHTML || !window.location.href.match(/no-legacy-html/);
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_SETTINGS && !useLegacyHTML)
			WPGMZA.settingsPage = WPGMZA.SettingsPage.createInstance();
		
	});
	
});

// js/v8/store-locator.js
/**
 * @namespace WPGMZA
 * @module StoreLocator
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.StoreLocator = function(map, element)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.call(this);
		
		this._center = null;
		
		this.map = map;
		this.element = element;
		this.state = WPGMZA.StoreLocator.STATE_INITIAL;
		
		$(element).find(".wpgmza-not-found-msg").hide();
		
		// TODO: This will be moved into this module instead of listening to the map event
		this.map.on("storelocatorgeocodecomplete", function(event) {
			self.onGeocodeComplete(event);
		});
		
		this.map.on("init", function(event) {
			
			self.map.markerFilter.on("filteringcomplete", function(event) {
				self.onFilteringComplete(event);
			});
			
		});
		
		// Catch enter
		$(element).on("keypress", "input", function(event) {
			
			if(event.which != 13)
				return;
			
			// NB: Legacy compatibilty. Do not merge this into 8.1.0
			searchLocations(self.map.id);
			
			self.onSearch(event);
			
		});

		// Legacy store locator buttons
		$(document.body).on("click", ".wpgmza_sl_search_button_" + map.id + ", [data-map-id='" + map.id + "'] .wpgmza_sl_search_button", function(event) {
			self.onSearch(event);
		});
		
		$(document.body).on("click", ".wpgmza_sl_reset_button_" + map.id + ", [data-map-id='" + map.id + "'] .wpgmza_sl_reset_button_div", function(event) {
			self.onReset(event);
		});
	}
	
	WPGMZA.StoreLocator.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.StoreLocator.prototype.constructor = WPGMZA.StoreLocator;
	
	WPGMZA.StoreLocator.STATE_INITIAL		= "initial";
	WPGMZA.StoreLocator.STATE_APPLIED		= "applied";
	
	WPGMZA.StoreLocator.createInstance = function(map, element)
	{
		return new WPGMZA.StoreLocator(map, element);
	}
	
	Object.defineProperty(WPGMZA.StoreLocator.prototype, "distanceUnits", {
	
		"get": function() {
			if(this.map.settings.store_locator_distance == 1)
				return WPGMZA.Distance.MILES;
			
			return WPGMZA.Distance.KILOMETERS;
		}
	
	});
	
	Object.defineProperty(WPGMZA.StoreLocator.prototype, "radius", {
		"get": function() {
			return $("#radiusSelect, #radiusSelect_" + this.map.id).val();
		}
	});
	
	Object.defineProperty(WPGMZA.StoreLocator.prototype, "center", {
		"get": function() {
			return this._center;
		}
	});
	
	Object.defineProperty(WPGMZA.StoreLocator.prototype, "bounds", {
		"get": function() {
			return this._bounds;
		}
	});
	
	Object.defineProperty(WPGMZA.StoreLocator.prototype, "marker", {
		
		"get": function() {
			
			if(this.map.settings.store_locator_bounce != 1)
				return null;
			
			if(this._marker)
				return this._marker;
			
			var options = {
				visible: false
			};
			
			this._marker = WPGMZA.Marker.createInstance(options);
			this._marker.disableInfoWindow = true;
			this._marker.isFilterable = false;
			
			this._marker.setAnimation(WPGMZA.Marker.ANIMATION_BOUNCE);
			
			return this._marker;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.StoreLocator.prototype, "circle", {
		
		"get": function() {
			
			if(this._circle)
				return this._circle;
			
			if(this.map.settings.wpgmza_store_locator_radius_style == "modern" && !WPGMZA.isDeviceiOS())
			{
				this._circle = WPGMZA.ModernStoreLocatorCircle.createInstance(this.map.id);
				this._circle.settings.color = this.circleStrokeColor;
			}
			else
			{
				this._circle = WPGMZA.Circle.createInstance({
					strokeColor:	"#ff0000",
					strokeOpacity:	"0.25",
					strokeWeight:	2,
					fillColor:		"#ff0000",
					fillOpacity:	"0.15",
					visible:		false,
					clickable:      false
				});
			}
			
			return this._circle;
			
		}
		
	});
	
	WPGMZA.StoreLocator.prototype.onGeocodeComplete = function(event)
	{
		if(!event.results || !event.results.length)
		{
			this._center = null;
			this._bounds = null;

			return;
		}
		else
		{
			this._center = new WPGMZA.LatLng( event.results[0].latLng );
			this._bounds = new WPGMZA.LatLngBounds( event.results[0].bounds );
		}
		
		this.map.markerFilter.update({}, this);
	}
	
	WPGMZA.StoreLocator.prototype.onSearch = function(event)
	{
		this.state = WPGMZA.StoreLocator.STATE_APPLIED;
	}
	
	WPGMZA.StoreLocator.prototype.onReset = function(event)
	{
		this.state = WPGMZA.StoreLocator.STATE_INITIAL;
		
		this._center = null;
		this._bounds = null;
		
		this.map.markerFilter.update({}, this);
	}
	
	WPGMZA.StoreLocator.prototype.getFilteringParameters = function()
	{
		if(!this.center)
			return {};
		
		return {
			center: this.center,
			radius: this.radius
		};
	}
	
	WPGMZA.StoreLocator.prototype.onFilteringComplete = function(event)
	{
		var params = event.filteringParams;
		var marker = this.marker;
		
		if(marker)
			marker.setVisible(false);
		
		// Center point marker
		if(params.center && marker)
		{
			marker.setPosition(params.center);
			marker.setVisible(true);
			
			if(marker.map != this.map)
				this.map.addMarker(marker);
		}
		
		var circle = this.circle;
		
		if(circle)
		{
			var factor = (this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.Distance.KILOMETERS_PER_MILE : 1.0);
			
			circle.setVisible(false);
			
			if(params.center && params.radius)
			{
				circle.setRadius(params.radius * factor);
				circle.setCenter(params.center);
				circle.setVisible(true);
				
				if(circle.map != this.map)
					this.map.addCircle(circle);
			}
			
			if(circle instanceof WPGMZA.ModernStoreLocatorCircle)
				circle.settings.radiusString = this.radius;
		}

		// Show / hide not found message
		if(!this.map.hasVisibleMarkers())
			$(this.element).find(".wpgmza-not-found-msg").show();
		else
			$(this.element).find(".wpgmza-not-found-msg").hide();
	}
	
});

// js/v8/text.js
/**
 * @namespace WPGMZA
 * @module Text
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.Text = function(options)
	{
		if(options)
			for(var name in options)
				this[name] = options[name];
	}
	
	WPGMZA.Text.createInstance = function(options)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLText(options);
				break;
				
			default:
				return new WPGMZA.GoogleText(options);
				break;
		}
	}
	
});

// js/v8/theme-editor.js
/**
 * @namespace WPGMZA
 * @module ThemeEditor
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.ThemeEditor = function()
	{
		var self = this;
		
		WPGMZA.EventDispatcher.call(this);
		
		this.element = $("#wpgmza-theme-editor");
		
		if(!this.element.length)
		{
			console.warn("No element to initialise theme editor on");
			return;
		}
		
		if(WPGMZA.settings.engine == "open-layers")
		{
			this.element.remove();
			return;
		}
		
		this.json = [{}];
		this.mapElement = WPGMZA.maps[0].element;

		this.element.appendTo('#wpgmza-map-theme-editor__holder');
		
		$(window).on("scroll", function(event) {
			//self.updatePosition();
		});
		
		setInterval(function() {
			//self.updatePosition();
		}, 200);
		
		this.initHTML();
		
		WPGMZA.themeEditor = this;
	}
	
	WPGMZA.extend(WPGMZA.ThemeEditor, WPGMZA.EventDispatcher);
	
	WPGMZA.ThemeEditor.prototype.updatePosition = function()
	{
		//var offset = $(this.mapElement).offset();
		
		// var relativeTop = offset.top - $(window).scrollTop();
		// var relativeLeft = offset.left - $(window).scrollLeft();
		// var height = $(this.mapElement).height();
		// var width = $(this.mapElement).width();

		// this.element.css({
		// 	top:	(relativeTop - (height + 5)) + "px",
		// 	left:	(relativeLeft + width) + "px",
		// 	height:	height + "px",
		// 	width: width + 'px'
		// });
	}
	
	WPGMZA.ThemeEditor.features = {
		'all' : [],
		'administrative' : [
			'country',
			'land_parcel',
			'locality',
			'neighborhood',
			'province'
		],
		'landscape' : [
			'man_made',
			'natural',
			'natural.landcover',
			'natural.terrain'
		],
		'poi' : [
			'attraction',
			'business',
			'government',
			'medical',
			'park',
			'place_of_worship',
			'school',
			'sports_complex'
		],
		'road' : [
			'arterial',
			'highway',
			'highway.controlled_access',
			'local'
		],
		'transit' : [
			'line',
			'station',
			'station.airport',
			'station.bus',
			'station.rail'
		],
		'water' : []
	};
	
	WPGMZA.ThemeEditor.elements = {
		'all' : [],
		'geometry' : [
			'fill',
			'stroke'
		],
		'labels' : [
			'icon',
			'text',
			'text.fill',
			'text.stroke'
		]
	};
	
	WPGMZA.ThemeEditor.prototype.parse = function()
	{
		$('#wpgmza_theme_editor_feature option, #wpgmza_theme_editor_element option').css('font-weight', 'normal');
		$('#wpgmza_theme_editor_error').hide();
		$('#wpgmza_theme_editor').show();
		$('#wpgmza_theme_editor_do_hue').prop('checked', false);
		$('#wpgmza_theme_editor_hue').val('#000000');
		$('#wpgmza_theme_editor_lightness').val('');
		$('#wpgmza_theme_editor_saturation').val('');
		$('#wpgmza_theme_editor_gamma').val('');
		$('#wpgmza_theme_editor_do_invert_lightness').prop('checked', false);
		$('#wpgmza_theme_editor_visibility').val('inherit');
		$('#wpgmza_theme_editor_do_color').prop('checked', false);
		$('#wpgmza_theme_editor_color').val('#000000');
		$('#wpgmza_theme_editor_weight').val('');
		
		var textarea = $('textarea[name="wpgmza_theme_data"]')
		
		if (!textarea.val() || textarea.val().length < 1) {
			this.json = [{}];
			return;
		}
		
		try {
			this.json = $.parseJSON($('textarea[name="wpgmza_theme_data"]').val());
		} catch (e) {
			this.json = [{}
			];
			$('#wpgmza_theme_editor').hide();
			$('#wpgmza_theme_editor_error').show();
			return;
		}
		if (!$.isArray(this.json)) {
			var jsonCopy = this.json;
			this.json = [];
			this.json.push(jsonCopy);
		}
		
		this.highlightFeatures();
		this.highlightElements();
		this.loadElementStylers();
	}
	
	WPGMZA.ThemeEditor.prototype.highlightFeatures = function()
	{
		$('#wpgmza_theme_editor_feature option').css('font-weight', 'normal');
		$.each(this.json, function (i, v) {
			if (v.hasOwnProperty('featureType')) {
				$('#wpgmza_theme_editor_feature option[value="' + v.featureType + '"]').css('font-weight', 'bold');
			} else {
				$('#wpgmza_theme_editor_feature option[value="all"]').css('font-weight', 'bold');
			}
		});

	}
	
	WPGMZA.ThemeEditor.prototype.highlightElements = function()
	{
		var feature = $('#wpgmza_theme_editor_feature').val();
		$('#wpgmza_theme_editor_element option').css('font-weight', 'normal');
		$.each(this.json, function (i, v) {
			if ((v.hasOwnProperty('featureType') && v.featureType == feature) ||
				(feature == 'all' && !v.hasOwnProperty('featureType'))) {
				if (v.hasOwnProperty('elementType')) {
					$('#wpgmza_theme_editor_element option[value="' + v.elementType + '"]').css('font-weight', 'bold');
				} else {
					$('#wpgmza_theme_editor_element option[value="all"]').css('font-weight', 'bold');
				}
			}
		});
	}
	
	WPGMZA.ThemeEditor.prototype.loadElementStylers = function()
	{
		var feature = $('#wpgmza_theme_editor_feature').val();
		var element = $('#wpgmza_theme_editor_element').val();
		$('#wpgmza_theme_editor_do_hue').prop('checked', false);
		$('#wpgmza_theme_editor_hue').val('#000000');
		$('#wpgmza_theme_editor_lightness').val('');
		$('#wpgmza_theme_editor_saturation').val('');
		$('#wpgmza_theme_editor_gamma').val('');
		$('#wpgmza_theme_editor_do_invert_lightness').prop('checked', false);
		$('#wpgmza_theme_editor_visibility').val('inherit');
		$('#wpgmza_theme_editor_do_color').prop('checked', false);
		$('#wpgmza_theme_editor_color').val('#000000');
		$('#wpgmza_theme_editor_weight').val('');
		$.each(this.json, function (i, v) {
			if ((v.hasOwnProperty('featureType') && v.featureType == feature) ||
				(feature == 'all' && !v.hasOwnProperty('featureType'))) {
				if ((v.hasOwnProperty('elementType') && v.elementType == element) ||
					(element == 'all' && !v.hasOwnProperty('elementType'))) {
					if (v.hasOwnProperty('stylers') && $.isArray(v.stylers) && v.stylers.length > 0) {
						$.each(v.stylers, function (ii, vv) {
							if (vv.hasOwnProperty('hue')) {
								$('#wpgmza_theme_editor_do_hue').prop('checked', true);
								$('#wpgmza_theme_editor_hue').val(vv.hue);
							}
							if (vv.hasOwnProperty('lightness')) {
								$('#wpgmza_theme_editor_lightness').val(vv.lightness);
							}
							if (vv.hasOwnProperty('saturation')) {
								$('#wpgmza_theme_editor_saturation').val(vv.xaturation);
							}
							if (vv.hasOwnProperty('gamma')) {
								$('#wpgmza_theme_editor_gamma').val(vv.gamma);
							}
							if (vv.hasOwnProperty('invert_lightness')) {
								$('#wpgmza_theme_editor_do_invert_lightness').prop('checked', true);
							}
							if (vv.hasOwnProperty('visibility')) {
								$('#wpgmza_theme_editor_visibility').val(vv.visibility);
							}
							if (vv.hasOwnProperty('color')) {
								$('#wpgmza_theme_editor_do_color').prop('checked', true);
								$('#wpgmza_theme_editor_color').val(vv.color);
							}
							if (vv.hasOwnProperty('weight')) {
								$('#wpgmza_theme_editor_weight').val(vv.weight);
							}
						});
					}
				}
			}
		});

	}
	
	WPGMZA.ThemeEditor.prototype.writeElementStylers = function()
	{
		var feature = $('#wpgmza_theme_editor_feature').val();
		var element = $('#wpgmza_theme_editor_element').val();
		var indexJSON = null;
		var stylers = [];
		
		if ($('#wpgmza_theme_editor_visibility').val() != "inherit") {
			stylers.push({
				'visibility': $('#wpgmza_theme_editor_visibility').val()
			});
		}
		if ($('#wpgmza_theme_editor_do_color').prop('checked') === true) {
			stylers.push({
				'color': $('#wpgmza_theme_editor_color').val()
			});
		}
		if ($('#wpgmza_theme_editor_do_hue').prop('checked') === true) {
			stylers.push({
				"hue": $('#wpgmza_theme_editor_hue').val()
			});
		}
		if ($('#wpgmza_theme_editor_gamma').val().length > 0) {
			stylers.push({
				'gamma': parseFloat($('#wpgmza_theme_editor_gamma').val())
			});
		}
		if ($('#wpgmza_theme_editor_weight').val().length > 0) {
			stylers.push({
				'weight': parseFloat($('#wpgmza_theme_editor_weight').val())
			});
		}
		if ($('#wpgmza_theme_editor_saturation').val().length > 0) {
			stylers.push({
				'saturation': parseFloat($('#wpgmza_theme_editor_saturation').val())
			});
		}
		if ($('#wpgmza_theme_editor_lightness').val().length > 0) {
			stylers.push({
				'lightness': parseFloat($('#wpgmza_theme_editor_lightness').val())
			});
		}
		if ($('#wpgmza_theme_editor_do_invert_lightness').prop('checked') === true) {
			stylers.push({
				'invert_lightness': true
			});
		}
		
		$.each(this.json, function (i, v) {
			if ((v.hasOwnProperty('featureType') && v.featureType == feature) ||
				(feature == 'all' && !v.hasOwnProperty('featureType'))) {
				if ((v.hasOwnProperty('elementType') && v.elementType == element) ||
					(element == 'all' && !v.hasOwnProperty('elementType'))) {
					indexJSON = i;
				}
			}
		});
		if (indexJSON === null) {
			if (stylers.length > 0) {
				var new_feature_element_stylers = {};
				if (feature != 'all') {
					new_feature_element_stylers.featureType = feature;
				}
				if (element != 'all') {
					new_feature_element_stylers.elementType = element;
				}
				new_feature_element_stylers.stylers = stylers;
				this.json.push(new_feature_element_stylers);
			}
		} else {
			if (stylers.length > 0) {
				this.json[indexJSON].stylers = stylers;
			} else {
				this.json.splice(indexJSON, 1);
			}
		}
		
		$('textarea[name="wpgmza_theme_data"]').val(JSON.stringify(this.json).replace(/:/g, ': ').replace(/,/g, ', '));
		
		this.highlightFeatures();
		this.highlightElements();
		
		WPGMZA.themePanel.updateMapTheme();
	}
	
	// TODO: WPGMZA.localized_strings
	
	WPGMZA.ThemeEditor.prototype.initHTML = function()
	{
		var self = this;

		$.each(WPGMZA.ThemeEditor.features, function (i, v) {
			$('#wpgmza_theme_editor_feature').append('<option value="' + i + '">' + i + '</option>');
			if (v.length > 0) {
				$.each(v, function (ii, vv) {
					$('#wpgmza_theme_editor_feature').append('<option value="' + i + '.' + vv + '">' + i + '.' + vv + '</option>');
				});
			}
		});
		$.each(WPGMZA.ThemeEditor.elements, function (i, v) {
			$('#wpgmza_theme_editor_element').append('<option value="' + i + '">' + i + '</option>');
			if (v.length > 0) {
				$.each(v, function (ii, vv) {
					$('#wpgmza_theme_editor_element').append('<option value="' + i + '.' + vv + '">' + i + '.' + vv + '</option>');
				});
			}
		});

		this.parse();
		
		// Bind listeners
		$('textarea[name="wpgmza_theme_data"]').on('input selectionchange propertychange', function() {
			self.parse();
		});
		
		$('.wpgmza_theme_selection').click(function(){
			setTimeout(function(){$('textarea[name="wpgmza_theme_data"]').trigger('input');}, 1000);
		});
		
		$('#wpgmza_theme_editor_feature').on("change", function() {
			self.highlightElements();
			self.loadElementStylers();
		});
		
		$('#wpgmza_theme_editor_element').on("change", function() {
			self.loadElementStylers();
		});
		
		$('#wpgmza_theme_editor_do_hue, #wpgmza_theme_editor_hue, #wpgmza_theme_editor_lightness, #wpgmza_theme_editor_saturation, #wpgmza_theme_editor_gamma, #wpgmza_theme_editor_do_invert_lightness, #wpgmza_theme_editor_visibility, #wpgmza_theme_editor_do_color, #wpgmza_theme_editor_color, #wpgmza_theme_editor_weight').on('input selectionchange propertychange', function() {
			self.writeElementStylers();
		});
		
		if(WPGMZA.settings.engine == "open-layers")
			$("#wpgmza_theme_editor :input").prop("disabled", true);
	}
	
});

// js/v8/theme-panel.js
/**
 * @namespace WPGMZA
 * @module ThemePanel
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.ThemePanel = function()
	{
		var self = this;
		
		this.element = $("#wpgmza-theme-panel");
		this.map = WPGMZA.maps[0];
		
		if(!this.element.length)
		{
			console.warn("No element to initialise theme panel on");
			return;
		}
		
		$("#wpgmza-theme-presets").owlCarousel({
			items: 5,
			dots: true
		});
		
		this.element.on("click", "#wpgmza-theme-presets label", function(event) {
			self.onThemePresetClick(event);
		});
		
		$("#wpgmza-open-theme-editor").on("click", function(event) {
			$('#wpgmza-map-theme-editor__holder').addClass('active');
			$("#wpgmza-theme-editor").addClass('active');
			WPGMZA.animateScroll($("#wpgmza-theme-editor"));
		});
		
		WPGMZA.themePanel = this;
		
		/*CodeMirror.fromTextArea($("textarea[name='wpgmza_theme_data']")[0], {
			lineNumbers: true,
			mode: "javascript"
		});*/
	}
	
	// NB: These aren't used anywhere, but they are recorded here for future use in making preview images
	WPGMZA.ThemePanel.previewImageCenter	= {lat: 33.701806462148646, lng: -118.15949896058983};
	WPGMZA.ThemePanel.previewImageZoom		= 11;
	
	WPGMZA.ThemePanel.prototype.onThemePresetClick = function(event)
	{
		var selectedData	= $(event.currentTarget).find("[data-theme-json]").attr("data-theme-json");
		var textarea		= $(this.element).find("textarea[name='wpgmza_theme_data']");
		var existingData	= textarea.val();
		var allPresetData	= [];
		
		$(this.element).find("[data-theme-json]").each(function(index, el) {
			allPresetData.push( $(el).attr("data-theme-json") );
		});
		
		// NB: This code will only prompt the user to overwrite if a custom theme is not being used. This way you can still flick through the unmodified themes
		if(existingData.length && allPresetData.indexOf(existingData) == -1)
		{
			if(!confirm(WPGMZA.localized_strings.overwrite_theme_data))
				return;
		}
		
		textarea.val(selectedData);
		
		this.updateMapTheme();
		WPGMZA.themeEditor.parse();
	}
	
	WPGMZA.ThemePanel.prototype.updateMapTheme = function()
	{
		var data;
		
		try{
			data = JSON.parse($("textarea[name='wpgmza_theme_data']").val());
		}catch(e) {
			alert(WPGMZA.localized_strings.invalid_theme_data);
			return;
		}
		
		this.map.setOptions({styles: data});
	}
	
});

// js/v8/version.js
/**
 * @namespace WPGMZA
 * @module Version
 * @requires WPGMZA
 */
jQuery(function($) {

	function isPositiveInteger(x) {
		// http://stackoverflow.com/a/1019526/11236
		return /^\d+$/.test(x);
	}

	function validateParts(parts) {
		for (var i = 0; i < parts.length; ++i) {
			if (!isPositiveInteger(parts[i])) {
				return false;
			}
		}
		return true;
	}
	
	WPGMZA.Version = function()
	{
		
	}
	
	WPGMZA.Version.GREATER_THAN		= 1;
	WPGMZA.Version.EQUAL_TO			= 0;
	WPGMZA.Version.LESS_THAN		= -1;
	
	/**
	 * Compare two software version numbers (e.g. 1.7.1)
	 * Returns:
	 *
	 *  0 if they're identical
	 *  negative if v1 < v2
	 *  positive if v1 > v2
	 *  NaN if they in the wrong format
	 *
	 *  "Unit tests": http://jsfiddle.net/ripper234/Xv9WL/28/
	 *
	 *  Taken from http://stackoverflow.com/a/6832721/11236
	 */
	WPGMZA.Version.compare = function(v1, v2)
	{
		var v1parts = v1.match(/\d+/g);
		var v2parts = v2.match(/\d+/g);

		for (var i = 0; i < v1parts.length; ++i) {
			if (v2parts.length === i) {
				return 1;
			}

			if (v1parts[i] === v2parts[i]) {
				continue;
			}
			if (v1parts[i] > v2parts[i]) {
				return 1;
			}
			return -1;
		}

		if (v1parts.length != v2parts.length) {
			return -1;
		}

		return 0;
	}

});

// js/v8/3rd-party-integration/integration.js
/**
 * @namespace WPGMZA
 * @module Integration
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.Integration = {};
	WPGMZA.integrationModules = {};
	
});

// js/v8/3rd-party-integration/gutenberg/dist/gutenberg.js
"use strict";

/**
 * @namespace WPGMZA.Integration
 * @module Gutenberg
 * @requires WPGMZA.Integration
 * @requires wp-i18n
 * @requires wp-blocks
 * @requires wp-editor
 * @requires wp-components
 */

/**
 * Internal block libraries
 */
jQuery(function ($) {

	if (!window.wp || !wp.i18n || !wp.blocks || !wp.editor || !wp.components) return;

	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var _wp$editor = wp.editor,
	    InspectorControls = _wp$editor.InspectorControls,
	    BlockControls = _wp$editor.BlockControls;
	var _wp$components = wp.components,
	    Dashicon = _wp$components.Dashicon,
	    Toolbar = _wp$components.Toolbar,
	    Button = _wp$components.Button,
	    Tooltip = _wp$components.Tooltip,
	    PanelBody = _wp$components.PanelBody,
	    TextareaControl = _wp$components.TextareaControl,
	    CheckboxControl = _wp$components.CheckboxControl,
	    TextControl = _wp$components.TextControl,
	    SelectControl = _wp$components.SelectControl,
	    RichText = _wp$components.RichText;


	WPGMZA.Integration.Gutenberg = function () {
		registerBlockType('gutenberg-wpgmza/block', this.getBlockDefinition());
	};

	WPGMZA.Integration.Gutenberg.prototype.getBlockTitle = function () {
		return __("WP Google Maps");
	};

	WPGMZA.Integration.Gutenberg.prototype.getBlockInspectorControls = function (props) {

		/*
  <TextControl
  				name="overrideWidthAmount"
  				label={__("Override Width Amount")}
  				checked={props.overrideWidthAmount}
  				onChange={onPropertiesChanged}
  				/>
  			
  			<SelectControl
  				name="overrideWidthUnits"
  				label={__("Override Width Units")}
  				options={[
  					{value: "px", label: "px"},
  					{value: "%", label: "%"},
  					{value: "vw`", label: "vw"},
  					{value: "vh", label: "vh"}
  				]}
  				onChange={onPropertiesChanged}
  				/>
  				
  			<CheckboxControl
  				name="overrideHeight"
  				label={__("Override Height")}
  				checked={props.overrideWidth}
  				onChange={onPropertiesChanged}
  				/>
  				
  			<TextControl
  				name="overrideHeightAmount"
  				label={__("Override Height Amount")}
  				checked={props.overrideWidthAmount}
  				onChange={onPropertiesChanged}
  				/>
  			
  			<SelectControl
  				name="overrideHeightUnits"
  				label={__("Override Height Units")}
  				options={[
  					{value: "px", label: "px"},
  					{value: "%", label: "%"},
  					{value: "vw`", label: "vw"},
  					{value: "vh", label: "vh"}
  				]}
  				onChange={onPropertiesChanged}
  				/>
  				*/

		var onOverrideWidthCheckboxChanged = function onOverrideWidthCheckboxChanged(value) {};

		return React.createElement(
			InspectorControls,
			{ key: "inspector" },
			React.createElement(
				PanelBody,
				{ title: __('Map Settings') },
				React.createElement(
					"p",
					{ "class": "map-block-gutenberg-button-container" },
					React.createElement(
						"a",
						{ href: WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu&action=edit&map_id=1",
							target: "_blank",
							"class": "button button-primary" },
						React.createElement("i", { "class": "fa fa-pencil-square-o", "aria-hidden": "true" }),
						__('Go to Map Editor')
					)
				),
				React.createElement(
					"p",
					{ "class": "map-block-gutenberg-button-container" },
					React.createElement(
						"a",
						{ href: "https://www.wpgmaps.com/documentation/creating-your-first-map/",
							target: "_blank",
							"class": "button button-primary" },
						React.createElement("i", { "class": "fa fa-book", "aria-hidden": "true" }),
						__('View Documentation')
					)
				)
			)
		);
	};

	WPGMZA.Integration.Gutenberg.prototype.getBlockAttributes = function () {
		return {};
	};

	WPGMZA.Integration.Gutenberg.prototype.getBlockDefinition = function (props) {
		var _this = this;

		return {

			title: __("WP Google Maps"),
			description: __('The easiest to use Google Maps plugin! Create custom Google Maps with high quality markers containing locations, descriptions, images and links. Add your customized map to your WordPress posts and/or pages quickly and easily with the supplied shortcode. No fuss.'),
			category: 'common',
			icon: 'location-alt',
			keywords: [__('Map'), __('Maps'), __('Google')],
			attributes: this.getBlockAttributes(),

			edit: function edit(props) {
				return [!!props.isSelected && _this.getBlockInspectorControls(props), React.createElement(
					"div",
					{ className: props.className + " wpgmza-gutenberg-block" },
					React.createElement(Dashicon, { icon: "location-alt" }),
					React.createElement(
						"span",
						{ "class": "wpgmza-gutenberg-block-title" },
						__("Your map will appear here on your websites front end")
					)
				)];
			},
			// Defining the front-end interface
			save: function save(props) {
				// Rendering in PHP
				return null;
			}

		};
	};

	WPGMZA.Integration.Gutenberg.getConstructor = function () {
		return WPGMZA.Integration.Gutenberg;
	};

	WPGMZA.Integration.Gutenberg.createInstance = function () {
		var constructor = WPGMZA.Integration.Gutenberg.getConstructor();
		return new constructor();
	};

	// Allow the Pro module to extend and create the module, only create here when Pro isn't loaded
	if(!WPGMZA.isProVersion() && !(/^6/.test(WPGMZA.pro_version))) WPGMZA.integrationModules.gutenberg = WPGMZA.Integration.Gutenberg.createInstance();
});

// js/v8/compatibility/astra-theme-compatibility.js
/**
 * @namespace WPGMZA
 * @module AstraThemeCompatiblity
 * @requires WPGMZA
 * @description Prevents the document.body.onclick handler firing for markers, which causes the Astra theme to throw an error, preventing the infowindow from opening
 */
jQuery(function($) {
	
	$(window).on("load", function(event) {
		
		var parent = document.body.onclick;
		
		if(!parent)
			return;
		
		document.body.onclick = function(event)
		{
			if(event.target instanceof WPGMZA.Marker)
				return;
			
			parent(event);
		}
		
	});
	
});

// js/v8/compatibility/google-ui-compatibility.js
/**
 * @namespace WPGMZA
 * @module GoogleUICompatibility
 * @requires WPGMZA
 */ 
jQuery(function($) {
	
	WPGMZA.GoogleUICompatibility = function()
	{
		var isSafari = navigator.vendor && navigator.vendor.indexOf('Apple') > -1 &&
				   navigator.userAgent &&
				   navigator.userAgent.indexOf('CriOS') == -1 &&
				   navigator.userAgent.indexOf('FxiOS') == -1;
		
		if(!isSafari)
		{
			var style = $("<style id='wpgmza-google-ui-compatiblity-fix'/>");
			style.html(".wpgmza_map img:not(button img) { padding:0 !important; }");
			$(document.head).append(style);
		}
	}
	
	WPGMZA.googleUICompatibility = new WPGMZA.GoogleUICompatibility();
	
});

// js/v8/google-maps/google-circle.js
/**
 * @namespace WPGMZA
 * @module GoogleCircle
 * @requires WPGMZA.Circle
 */
jQuery(function($) {
	
	/**
	 * Subclass, used when Google is the maps engine. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.GoogleCircle
	 * @constructor WPGMZA.GoogleCircle
	 * @memberof WPGMZA
	 * @augments WPGMZA.Circle
	 * @see WPGMZA.Circle.createInstance
	 */
	WPGMZA.GoogleCircle = function(options, googleCircle)
	{
		var self = this;
		
		WPGMZA.Circle.call(this, options, googleCircle);
		
		if(googleCircle)
		{
			this.googleCircle = googleCircle;
		}
		else
		{
			this.googleCircle = new google.maps.Circle();
			this.googleCircle.wpgmzaCircle = this;
		}
		
		google.maps.event.addListener(this.googleCircle, "click", function() {
			self.dispatchEvent({type: "click"});
		});
		
		if(options)
			this.setOptions(options);
	}
	
	WPGMZA.GoogleCircle.prototype = Object.create(WPGMZA.Circle.prototype);
	WPGMZA.GoogleCircle.prototype.constructor = WPGMZA.GoogleCircle;
	
	WPGMZA.GoogleCircle.prototype.setCenter = function(center)
	{
		WPGMZA.Circle.prototype.setCenter.apply(this, arguments);
		
		this.googleCircle.setCenter(center);
	}
	
	WPGMZA.GoogleCircle.prototype.setRadius = function(radius)
	{
		WPGMZA.Circle.prototype.setRadius.apply(this, arguments);
		
		this.googleCircle.setRadius(parseFloat(radius) * 1000);
	}
	
	WPGMZA.GoogleCircle.prototype.setVisible = function(visible)
	{
		this.googleCircle.setVisible(visible ? true : false);
	}
	
	WPGMZA.GoogleCircle.prototype.setOptions = function(options)
	{
		var googleOptions = {};
		
		googleOptions = $.extend({}, options);
		delete googleOptions.map;
		delete googleOptions.center;
		
		if(options.center)
			googleOptions.center = new google.maps.LatLng({
				lat: parseFloat(options.center.lat),
				lng: parseFloat(options.center.lng)
			});
			
		if(options.radius)
			googleOptions.radius = parseFloat(options.radius);
		
		if(options.color)
			googleOptions.fillColor = options.color;
		
		if(options.opacity)
		{
			googleOptions.fillOpacity = parseFloat(options.opacity);
			googleOptions.strokeOpacity = parseFloat(options.opacity);

		}
		
		this.googleCircle.setOptions(googleOptions);
		
		if(options.map)
			options.map.addCircle(this);
	}
	
});

// js/v8/google-maps/google-geocoder.js
/**
 * @namespace WPGMZA
 * @module GoogleGeocoder
 * @requires WPGMZA.Geocoder
 */
jQuery(function($) {
	
	/**
	 * Subclass, used when Google is the maps engine. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.GoogleGeocoder
	 * @constructor WPGMZA.GoogleGeocoder
	 * @memberof WPGMZA
	 * @augments WPGMZA.Geocoder
	 * @see WPGMZA.Geocoder.createInstance
	 */
	WPGMZA.GoogleGeocoder = function()
	{
		
	}
	
	WPGMZA.GoogleGeocoder.prototype = Object.create(WPGMZA.Geocoder.prototype);
	WPGMZA.GoogleGeocoder.prototype.constructor = WPGMZA.GoogleGeocoder;
	
	WPGMZA.GoogleGeocoder.prototype.getLatLngFromAddress = function(options, callback)
	{
		if(!options || !options.address)
			throw new Error("No address specified");
		
		if(WPGMZA.isLatLngString(options.address))
			return WPGMZA.Geocoder.prototype.getLatLngFromAddress.call(this, options, callback);
		
		if(options.country)
			options.componentRestrictions = {
				country: options.country
			};
		
		var geocoder = new google.maps.Geocoder();
		
		geocoder.geocode(options, function(results, status) {
			if(status == google.maps.GeocoderStatus.OK)
			{
				var location = results[0].geometry.location;
				var latLng = {
					lat: location.lat(),
					lng: location.lng()
				};
				var bounds = null;
				
				if(results[0].geometry.bounds)
					bounds = WPGMZA.LatLngBounds.fromGoogleLatLngBounds(results[0].geometry.bounds);
				
				var results = [
					{
						geometry: {
							location: latLng
						},
						latLng: latLng,
						lat: latLng.lat,
						lng: latLng.lng,
						bounds: bounds
					}
				];
				
				
				
				callback(results, WPGMZA.Geocoder.SUCCESS);
			}
			else
			{
				var nativeStatus = WPGMZA.Geocoder.FAIL;
				
				if(status == google.maps.GeocoderStatus.ZERO_RESULTS)
					nativeStatus = WPGMZA.Geocoder.ZERO_RESULTS;
				
				callback(null, nativeStatus);
			}
		});
	}
	
	WPGMZA.GoogleGeocoder.prototype.getAddressFromLatLng = function(options, callback)
	{
		if(!options || !options.latLng)
			throw new Error("No latLng specified");
		
		var latLng = new WPGMZA.LatLng(options.latLng);
		var geocoder = new google.maps.Geocoder();
		
		var options = $.extend(options, {
			location: {
				lat: latLng.lat,
				lng: latLng.lng
			}
		});
		delete options.latLng;
		
		geocoder.geocode(options, function(results, status) {
			
			if(status !== "OK")
				callback(null, WPGMZA.Geocoder.FAIL);
			
			if(!results || !results.length)
				callback([], WPGMZA.Geocoder.NO_RESULTS);
			
			callback([results[0].formatted_address], WPGMZA.Geocoder.SUCCESS);
			
		});
	}
	
});

// js/v8/google-maps/google-html-overlay.js
/**
 * @namespace WPGMZA
 * @module GoogleHTMLOverlay
 * @requires WPGMZA
 */
jQuery(function($) {
	
	// https://developers.google.com/maps/documentation/javascript/customoverlays
	
	if(WPGMZA.settings.engine && WPGMZA.settings.engine != "google-maps")
		return;
	
	if(!window.google || !window.google.maps)
		return;
	
	WPGMZA.GoogleHTMLOverlay = function(map)
	{
		this.element	= $("<div class='wpgmza-google-html-overlay'></div>");
		
		this.visible	= true;
		this.position	= new WPGMZA.LatLng();
		
		this.setMap(map.googleMap);
		this.wpgmzaMap = map;
	}
	
	WPGMZA.GoogleHTMLOverlay.prototype = new google.maps.OverlayView();
	
	WPGMZA.GoogleHTMLOverlay.prototype.onAdd = function()
	{
		var panes = this.getPanes();
		panes.overlayMouseTarget.appendChild(this.element[0]);
		
		/*google.maps.event.addDomListener(this.element, "click", function() {
			
		});*/
	}
	
	WPGMZA.GoogleHTMLOverlay.prototype.onRemove = function()
	{
		if(this.element && $(this.element).parent().length)
		{
			$(this.element).remove();
			this.element = null;
		}
	}
	
	WPGMZA.GoogleHTMLOverlay.prototype.draw = function()
	{
		this.updateElementPosition();
	}
	
	/*WPGMZA.GoogleHTMLOverlay.prototype.setMap = function(map)
	{
		if(!(map instanceof WPGMZA.Map))
			throw new Error("Map must be an instance of WPGMZA.Map");
		
		google.maps.OverlayView.prototype.setMap.call(this, map.googleMap);
		
		this.wpgmzaMap = map;
	}*/
	
	/*WPGMZA.GoogleHTMLOverlay.prototype.getVisible = function()
	{
		return $(this.element).css("display") != "none";
	}
	
	WPGMZA.GoogleHTMLOverlay.prototype.setVisible = function(visible)
	{
		$(this.element).css({
			"display": (visible ? "block" : "none")
		});
	}*/
	
	/*WPGMZA.GoogleHTMLOverlay.prototype.getPosition = function()
	{
		return new WPGMZA.LatLng(this.position);
	}
	
	WPGMZA.GoogleHTMLOverlay.prototype.setPosition = function(position)
	{
		if(!(position instanceof WPGMZA.LatLng))
			throw new Error("Argument must be an instance of WPGMZA.LatLng");
		
		this.position = position;
		this.updateElementPosition();
	}*/
	
	WPGMZA.GoogleHTMLOverlay.prototype.updateElementPosition = function()
	{
		//var pixels = this.wpgmzaMap.latLngToPixels(this.position);
		
		var projection = this.getProjection();
		
		if(!projection)
			return;
		
		var pixels = projection.fromLatLngToDivPixel(this.position.toGoogleLatLng());
		
		$(this.element).css({
			"left": pixels.x,
			"top": pixels.y
		});
	}
});

// js/v8/google-maps/google-modern-store-locator-circle.js
/**
 * @namespace WPGMZA
 * @module GoogleModernStoreLocatorCircle
 * @requires WPGMZA.ModernStoreLocatorCircle
 */
jQuery(function($) {
	
	WPGMZA.GoogleModernStoreLocatorCircle = function(map, settings)
	{
		var self = this;
		
		WPGMZA.ModernStoreLocatorCircle.call(this, map, settings);
		
		this.intervalID = setInterval(function() {
			
			var mapSize = {
				width: $(self.mapElement).width(),
				height: $(self.mapElement).height()
			};
			
			if(mapSize.width == self.mapSize.width && mapSize.height == self.mapSize.height)
				return;
			
			self.canvasLayer.resize_();
			self.canvasLayer.draw();
			
			self.mapSize = mapSize;
			
		}, 1000);
		
		$(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function() {
			
			self.canvasLayer.resize_();
			self.canvasLayer.draw();
			
		});
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype = Object.create(WPGMZA.ModernStoreLocatorCircle.prototype);
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.constructor = WPGMZA.GoogleModernStoreLocatorCircle;
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.initCanvasLayer = function()
	{
		var self = this;
		
		if(this.canvasLayer)
		{
			this.canvasLayer.setMap(null);
			this.canvasLayer.setAnimate(false);
		}
		
		this.canvasLayer = new CanvasLayer({
			map: this.map.googleMap,
			resizeHandler: function(event) {
				self.onResize(event);
			},
			updateHandler: function(event) {
				self.onUpdate(event);
			},
			animate: true,
			resolutionScale: this.getResolutionScale()
        });
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.setOptions = function(options)
	{
		WPGMZA.ModernStoreLocatorCircle.prototype.setOptions.call(this, options);
		
		this.canvasLayer.scheduleUpdate();
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.setPosition = function(position)
	{
		WPGMZA.ModernStoreLocatorCircle.prototype.setPosition.call(this, position);
		
		this.canvasLayer.scheduleUpdate();
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.setRadius = function(radius)
	{
		WPGMZA.ModernStoreLocatorCircle.prototype.setRadius.call(this, radius);
		
		this.canvasLayer.scheduleUpdate();
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.getTransformedRadius = function(km)
	{
		var multiplierAtEquator = 0.006395;
		var spherical = google.maps.geometry.spherical;
		
		var center = this.settings.center;
		var equator = new WPGMZA.LatLng({
			lat: 0.0,
			lng: 0.0
		});
		var latitude = new WPGMZA.LatLng({
			lat: center.lat,
			lng: 0.0
		});
		
		var offsetAtEquator = spherical.computeOffset(equator.toGoogleLatLng(), km * 1000, 90);
		var offsetAtLatitude = spherical.computeOffset(latitude.toGoogleLatLng(), km * 1000, 90);
		
		var factor = offsetAtLatitude.lng() / offsetAtEquator.lng();
		var result = km * multiplierAtEquator * factor;
		
		if(isNaN(result))
			throw new Error("here");
		
		return result;
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.getCanvasDimensions = function()
	{
		return {
			width: this.canvasLayer.canvas.width,
			height: this.canvasLayer.canvas.height
		};
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.getWorldOriginOffset = function()
	{
		var projection = this.map.googleMap.getProjection();
		var position = projection.fromLatLngToPoint(this.canvasLayer.getTopLeft());
		
		return {
			x: -position.x,
			y: -position.y
		};
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.getCenterPixels = function()
	{
		var center = new WPGMZA.LatLng(this.settings.center);
		var projection = this.map.googleMap.getProjection();
		return projection.fromLatLngToPoint(center.toGoogleLatLng());
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.getContext = function(type)
	{
		return this.canvasLayer.canvas.getContext("2d");
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.getScale = function()
	{
		return Math.pow(2, this.map.getZoom()) * this.getResolutionScale();
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.setVisible = function(visible)
	{
		WPGMZA.ModernStoreLocatorCircle.prototype.setVisible.call(this, visible);
		
		this.canvasLayer.scheduleUpdate();
	}
	
	WPGMZA.GoogleModernStoreLocatorCircle.prototype.destroy = function()
	{
		this.canvasLayer.setMap(null);
		this.canvasLayer = null;
		
		clearInterval(this.intervalID);
	}
	
});

// js/v8/google-maps/google-polyline.js
/**
 * @namespace WPGMZA
 * @module GooglePolyline
 * @requires WPGMZA.Polyline
 */
jQuery(function($) {
	
	WPGMZA.GooglePolyline = function(options, googlePolyline)
	{
		var self = this;
		
		WPGMZA.Polyline.call(this, options, googlePolyline);
		
		if(googlePolyline)
		{
			this.googlePolyline = googlePolyline;
		}
		else
		{
			this.googlePolyline = new google.maps.Polyline(this.settings);			
			
			if(options)
			{
				var googleOptions = $.extend({}, options);
				
				if(options.polydata)
					googleOptions.path = this.parseGeometry(options.polydata);
				
				if(options.linecolor)
					googleOptions.strokeColor = "#" + options.linecolor;
				
				if(options.linethickness)
					googleOptions.strokeWeight = parseInt(options.linethickness);
				
				if(options.opacity)
					googleOptions.strokeOpacity = parseFloat(options.opacity);
			}
			
			if(options && options.polydata)
			{
				var path = this.parseGeometry(options.polydata);
				this.setPoints(path);
			}
		}
		
		this.googlePolyline.wpgmzaPolyline = this;
		
		google.maps.event.addListener(this.googlePolyline, "click", function() {
			self.dispatchEvent({type: "click"});
		});
	}
	
	WPGMZA.GooglePolyline.prototype = Object.create(WPGMZA.Polyline.prototype);
	WPGMZA.GooglePolyline.prototype.constructor = WPGMZA.GooglePolyline;
	
	WPGMZA.GooglePolyline.prototype.setEditable = function(value)
	{
		this.googlePolyline.setOptions({editable: value});
	}
	
	WPGMZA.GooglePolyline.prototype.setPoints = function(points)
	{
		this.googlePolyline.setOptions({path: points});
	}
	
	WPGMZA.GooglePolyline.prototype.toJSON = function()
	{
		var result = WPGMZA.Polyline.prototype.toJSON.call(this);
		
		result.points = [];
		
		var path = this.googlePolyline.getPath();
		for(var i = 0; i < path.getLength(); i++)
		{
			var latLng = path.getAt(i);
			result.points.push({
				lat: latLng.lat(),
				lng: latLng.lng()
			});
		}
		
		return result;
	}
	
});

// js/v8/google-maps/google-text.js
/**
 * @namespace WPGMZA
 * @module GoogleText
 * @requires WPGMZA.Text
 */
jQuery(function($) {
	
	WPGMZA.GoogleText = function(options)
	{
		WPGMZA.Text.apply(this, arguments);
		
		this.overlay = new WPGMZA.GoogleTextOverlay(options);
	}
	
	WPGMZA.extend(WPGMZA.GoogleText, WPGMZA.Text);
	
});

// js/v8/google-maps/google-text-overlay.js
/**
 * @namespace WPGMZA
 * @module GoogleTextOverlay
 * @requires WPGMZA.GoogleText
 */
jQuery(function($) {
	
	WPGMZA.GoogleTextOverlay = function(options)
	{
		this.element = $("<div class='wpgmza-google-text-overlay'><div class='wpgmza-inner'></div></div>");
		
		if(!options)
			options = {};
		
		if(options.position)
			this.position = options.position;
		
		if(options.text)
			this.element.find(".wpgmza-inner").text(options.text);
		
		if(options.map)
			this.setMap(options.map.googleMap);
	}
	
	if(window.google && google.maps && google.maps.OverlayView)
		WPGMZA.GoogleTextOverlay.prototype = new google.maps.OverlayView();
	
	WPGMZA.GoogleTextOverlay.prototype.onAdd = function()
	{
		var overlayProjection = this.getProjection();
		var position = overlayProjection.fromLatLngToDivPixel(this.position.toGoogleLatLng());
		
		this.element.css({
			position: "absolute",
			left: position.x + "px",
			top: position.y + "px"
		});

		var panes = this.getPanes();
		panes.floatPane.appendChild(this.element[0]);
	}
	
	WPGMZA.GoogleTextOverlay.prototype.draw = function()
	{
		var overlayProjection = this.getProjection();
		var position = overlayProjection.fromLatLngToDivPixel(this.position.toGoogleLatLng());
		
		this.element.css({
			position: "absolute",
			left: position.x + "px",
			top: position.y + "px"
		});
	}
	
	WPGMZA.GoogleTextOverlay.prototype.onRemove = function()
	{
		this.element.remove();
	}
	
	WPGMZA.GoogleTextOverlay.prototype.hide = function()
	{
		this.element.hide();
	}
	
	WPGMZA.GoogleTextOverlay.prototype.show = function()
	{
		this.element.show();
	}
	
	WPGMZA.GoogleTextOverlay.prototype.toggle = function()
	{
		if(this.element.is(":visible"))
			this.element.hide();
		else
			this.element.show();
	}
	
});

// js/v8/google-maps/google-vertex-context-menu.js
/**
 * @namespace WPGMZA
 * @module GoogleVertexContextMenu
 * @requires wpgmza_api_call
 */
jQuery(function($) {
	
	if(WPGMZA.settings.engine != "google-maps")
		return;
	
	if(WPGMZA.googleAPIStatus && WPGMZA.googleAPIStatus.code == "USER_CONSENT_NOT_GIVEN")
		return;
	
	WPGMZA.GoogleVertexContextMenu = function(mapEditPage)
	{
		var self = this;
		
		this.mapEditPage = mapEditPage;
		
		this.element = document.createElement("div");
		this.element.className = "wpgmza-vertex-context-menu";
		this.element.innerHTML = "Delete";
		
		google.maps.event.addDomListener(this.element, "click", function(event) {
			self.removeVertex();
			event.preventDefault();
			event.stopPropagation();
			return false;
		});
	}
	
	WPGMZA.GoogleVertexContextMenu.prototype = new google.maps.OverlayView();
	
	WPGMZA.GoogleVertexContextMenu.prototype.onAdd = function()
	{
		var self = this;
		var map = this.getMap();
		
		this.getPanes().floatPane.appendChild(this.element);
		this.divListener = google.maps.event.addDomListener(map.getDiv(), "mousedown", function(e) {
			if(e.target != self.element)
				self.close();
		}, true);
	}
	
	WPGMZA.GoogleVertexContextMenu.prototype.onRemove = function()
	{
		google.maps.event.removeListener(this.divListener);
		this.element.parentNode.removeChild(this.element);
		
		this.set("position");
		this.set("path");
		this.set("vertex");
	}
	
	WPGMZA.GoogleVertexContextMenu.prototype.open = function(map, path, vertex)
	{
		this.set('position', path.getAt(vertex));
		this.set('path', path);
		this.set('vertex', vertex);
		this.setMap(map);
		this.draw();
	}
	
	WPGMZA.GoogleVertexContextMenu.prototype.close = function()
	{
		this.setMap(null);
	}
	
	WPGMZA.GoogleVertexContextMenu.prototype.draw = function()
	{
		var position = this.get('position');
		var projection = this.getProjection();

		if (!position || !projection)
		  return;

		var point = projection.fromLatLngToDivPixel(position);
		this.element.style.top = point.y + 'px';
		this.element.style.left = point.x + 'px';
	}
	
	WPGMZA.GoogleVertexContextMenu.prototype.removeVertex = function()
	{
		var path = this.get('path');
		var vertex = this.get('vertex');

		if (!path || vertex == undefined) {
		  this.close();
		  return;
		}

		path.removeAt(vertex);
		this.close();
	}
	
});

// js/v8/open-layers/ol-circle.js
/**
 * @namespace WPGMZA
 * @module OLCircle
 * @requires WPGMZA.Circle
 */
jQuery(function($) {
	
	var Parent = WPGMZA.Circle;
	
	WPGMZA.OLCircle = function(options, olFeature)
	{
		var self = this;
		
		this.center = {lat: 0, lng: 0};
		this.radius = 0;
		
		this.fillcolor = "#ff0000";
		this.opacity = 0.6;
		
		Parent.call(this, options, olFeature);
		
		this.olStyle = new ol.style.Style(this.getStyleFromSettings());
		
		this.vectorLayer3857 = this.layer = new ol.layer.Vector({
			source: new ol.source.Vector(),
			style: this.olStyle
		});
		
		if(olFeature)
			this.olFeature = olFeature;
		else
			this.recreate();
	}
	
	WPGMZA.OLCircle.prototype = Object.create(Parent.prototype);
	WPGMZA.OLCircle.prototype.constructor = WPGMZA.OLCircle;
	
	WPGMZA.OLCircle.prototype.recreate = function()
	{
		if(this.olFeature)
		{
			this.layer.getSource().removeFeature(this.olFeature);
			delete this.olFeature;
		}
		
		if(!this.center || !this.radius)
			return;
		
		// IMPORTANT: Please note that due to what appears to be a bug in OpenLayers, the following code MUST be exected specifically in this order, or the circle won't appear
		var radius = parseFloat(this.radius) * 1000 / 2;
		var x, y;
		
		x = this.center.lng;
		y = this.center.lat;
		
		var circle4326 = ol.geom.Polygon.circular([x, y], radius, 64);
		var circle3857 = circle4326.clone().transform('EPSG:4326', 'EPSG:3857');
		
		this.olFeature = new ol.Feature(circle3857);
		
		this.layer.getSource().addFeature(this.olFeature);
	}
	
	WPGMZA.OLCircle.prototype.getStyleFromSettings = function()
	{
		var params = {};
				
		/*if(this.settings.strokeOpacity)
			params.stroke = new ol.style.Stroke({
				color: WPGMZA.hexOpacityToRGBA(this.settings.strokeColor, this.settings.strokeOpacity)
			});*/
		
		if(this.opacity)
			params.fill = new ol.style.Fill({
				color: WPGMZA.hexOpacityToRGBA(this.fillColor, this.opacity)
			});
			
		return params;
	}
	
	WPGMZA.OLCircle.prototype.updateStyleFromSettings = function()
	{
		// Re-create the style - working on it directly doesn't cause a re-render
		var params = this.getStyleFromSettings();
		this.olStyle = new ol.style.Style(params);
		this.layer.setStyle(this.olStyle);
	}
	
	WPGMZA.OLCircle.prototype.setVisible = function(visible)
	{
		this.layer.setVisible(visible ? true : false);
	}
	
	WPGMZA.OLCircle.prototype.setCenter = function(center)
	{
		WPGMZA.Circle.prototype.setCenter.apply(this, arguments);
		
		this.recreate();
	}
	
	WPGMZA.OLCircle.prototype.setRadius = function(radius)
	{
		WPGMZA.Circle.prototype.setRadius.apply(this, arguments);
		
		this.recreate();
	}
	
});

// js/v8/open-layers/ol-geocoder.js
/**
 * @namespace WPGMZA
 * @module OLGeocoder
 * @requires WPGMZA.Geocoder
 */
jQuery(function($) {
	
	/**
	 * @class OLGeocoder
	 * @extends Geocoder
	 * @summary OpenLayers geocoder - uses Nominatim by default
	 */
	WPGMZA.OLGeocoder = function()
	{
		
	}
	
	WPGMZA.OLGeocoder.prototype = Object.create(WPGMZA.Geocoder.prototype);
	WPGMZA.OLGeocoder.prototype.constructor = WPGMZA.OLGeocoder;
	
	/**
	 * @function getResponseFromCache
	 * @access protected
	 * @summary Tries to retrieve cached coordinates from server cache
	 * @param {string} address The street address to geocode
	 * @param {function} callback Where to send the results, as an array
	 * @return {void}
	 */
	WPGMZA.OLGeocoder.prototype.getResponseFromCache = function(query, callback)
	{
		WPGMZA.restAPI.call("/geocode-cache", {
			data: {
				query: JSON.stringify(query)
			},
			success: function(response, xhr, status) {
				// Legacy compatibility support
				response.lng = response.lon;
				
				callback(response);
			},
			useCompressedPathVariable: true
		});
		
		/*$.ajax(WPGMZA.ajaxurl, {
			data: {
				action: "wpgmza_query_nominatim_cache",
				query: JSON.stringify(query)
			},
			success: function(response, xhr, status) {
				// Legacy compatibility support
				response.lng = response.lon;
				
				callback(response);
			}
		});*/
	}
	
	/**
	 * @function getResponseFromNominatim
	 * @access protected
	 * @summary Queries Nominatim on the specified address
	 * @param {object} options An object containing the options for geocoding, address is a mandatory field
	 * @param {function} callback The function to send the results to, as an array
	 */
	WPGMZA.OLGeocoder.prototype.getResponseFromNominatim = function(options, callback)
	{
		var data = {
			q: options.address,
			format: "json"
		};
		
		if(options.componentRestrictions && options.componentRestrictions.country)
			data.countrycodes = options.componentRestrictions.country;
		
		$.ajax("https://nominatim.openstreetmap.org/search/", {
			data: data,
			success: function(response, xhr, status) {
				callback(response);
			},
			error: function(response, xhr, status) {
				callback(null, WPGMZA.Geocoder.FAIL)
			}
		});
	}
	
	/**
	 * @function cacheResponse
	 * @access protected
	 * @summary Caches a response on the server, usually after it's been returned from Nominatim
	 * @param {string} address The street address
	 * @param {object|array} response The response to cache
	 * @returns {void}
	 */
	WPGMZA.OLGeocoder.prototype.cacheResponse = function(query, response)
	{
		$.ajax(WPGMZA.ajaxurl, {
			data: {
				action: "wpgmza_store_nominatim_cache",
				query: JSON.stringify(query),
				response: JSON.stringify(response)
			},
			method: "POST"
		});
	}

	/**
	 * @function clearCache
	 * @access protected
	 * @summary Clears the Nomanatim geocode cache
	 * @returns {void}
	 */
	WPGMZA.OLGeocoder.prototype.clearCache = function(callback)
	{
		$.ajax(WPGMZA.ajaxurl, {
			data: {
				action: "wpgmza_clear_nominatim_cache"
			},
			method: "POST",
			success: function(response){
				callback(response);
			}
		});
	}
	
	WPGMZA.OLGeocoder.prototype.getLatLngFromAddress = function(options, callback)
	{
		return WPGMZA.OLGeocoder.prototype.geocode(options, callback);
	}
	
	WPGMZA.OLGeocoder.prototype.getAddressFromLatLng = function(options, callback)
	{
		return WPGMZA.OLGeocoder.prototype.geocode(options, callback);
	}
	
	WPGMZA.OLGeocoder.prototype.geocode = function(options, callback)
	{
		var self = this;
		
		if(!options)
			throw new Error("Invalid options");
		
		if(WPGMZA.LatLng.REGEXP.test(options.address))
		{
			var latLng = WPGMZA.LatLng.fromString(options.address);
			
			callback([{
				geometry: {
					location: latLng
				},
				latLng: latLng,
				lat: latLng.lat,
				lng: latLng.lng
			}], WPGMZA.Geocoder.SUCCESS);
			
			return;
		}
		
		if(options.location)
			options.latLng = new WPGMZA.LatLng(options.location);
		
		var finish, location;
		
		if(options.address)
		{
			location = options.address;
			
			finish = function(response, status)
			{
				for(var i = 0; i < response.length; i++)
				{
					response[i].geometry = {
						location: new WPGMZA.LatLng({
							lat: parseFloat(response[i].lat),
							lng: parseFloat(response[i].lon)
						})
					};
					
					response[i].latLng = {
						lat: parseFloat(response[i].lat),
						lng: parseFloat(response[i].lon)
					};
					
					response[i].bounds = new WPGMZA.LatLngBounds(
						new WPGMZA.LatLng({
							lat: response[i].boundingbox[1],
							lng: response[i].boundingbox[2]
						}),
						new WPGMZA.LatLng({
							lat: response[i].boundingbox[0],
							lng: response[i].boundingbox[3]
						})
					);
					
					// Backward compatibility with old UGM
					response[i].lng = response[i].lon;
				}
				
				callback(response, status);
			}
		}
		else if(options.latLng)
		{
			location = options.latLng.toString();
			
			finish = function(response, status)
			{
				var address = response[0].display_name;
				callback([address], status);
			}
		}
		else
			throw new Error("You must supply either a latLng or address")
		
		var query = {location: location, options: options};
		this.getResponseFromCache(query, function(response) {
			if(response.length)
			{
				finish(response, WPGMZA.Geocoder.SUCCESS);
				return;
			}
			
			self.getResponseFromNominatim($.extend(options, {address: location}), function(response, status) {
				if(status == WPGMZA.Geocoder.FAIL)
				{
					callback(null, WPGMZA.Geocoder.FAIL);
					return;
				}
				
				if(response.length == 0)
				{
					callback([], WPGMZA.Geocoder.ZERO_RESULTS);
					return;
				}
				
				finish(response, WPGMZA.Geocoder.SUCCESS);
				
				self.cacheResponse(query, response);
			});
		});
	}
	
});

// js/v8/open-layers/ol-modern-store-locator-circle.js
/**
 * @namespace WPGMZA
 * @module OLModernStoreLocatorCircle
 * @requires WPGMZA.ModernStoreLocatorCircle
 */
jQuery(function($) {
	
	WPGMZA.OLModernStoreLocatorCircle = function(map, settings)
	{
		WPGMZA.ModernStoreLocatorCircle.call(this, map, settings);
	}
	
	WPGMZA.OLModernStoreLocatorCircle.prototype = Object.create(WPGMZA.ModernStoreLocatorCircle.prototype);
	WPGMZA.OLModernStoreLocatorCircle.prototype.constructor = WPGMZA.OLModernStoreLocatorCircle;
	
	WPGMZA.OLModernStoreLocatorCircle.prototype.initCanvasLayer = function()
	{
		var self = this;
		var mapElement = $(this.map.element);
		var olViewportElement = mapElement.children(".ol-viewport");
		
		this.canvas = document.createElement("canvas");
		this.canvas.className = "wpgmza-ol-canvas-overlay";
		mapElement.append(this.canvas);
		
		this.renderFunction = function(event) {
			
			if(self.canvas.width != olViewportElement.width() || self.canvas.height != olViewportElement.height())
			{
				self.canvas.width = olViewportElement.width();
				self.canvas.height = olViewportElement.height();
				
				$(this.canvas).css({
					width: olViewportElement.width() + "px",
					height: olViewportElement.height() + "px"
				});
			}
			
			self.draw();
		};
		
		this.map.olMap.on("postrender", this.renderFunction);
	}

	WPGMZA.OLModernStoreLocatorCircle.prototype.getContext = function(type)
	{
		return this.canvas.getContext(type);
	}
	
	WPGMZA.OLModernStoreLocatorCircle.prototype.getCanvasDimensions = function()
	{
		return {
			width: this.canvas.width,
			height: this.canvas.height
		};
	}
	
	WPGMZA.OLModernStoreLocatorCircle.prototype.getCenterPixels = function()
	{
		var center = this.map.latLngToPixels(this.settings.center);
		
		return center;
	}
		
	WPGMZA.OLModernStoreLocatorCircle.prototype.getWorldOriginOffset = function()
	{
		return {
			x: 0,
			y: 0
		};
	}
	
	WPGMZA.OLModernStoreLocatorCircle.prototype.getTransformedRadius = function(km)
	{
		var center = new WPGMZA.LatLng(this.settings.center);
		var outer = new WPGMZA.LatLng(center);
		
		outer.moveByDistance(km, 90);
		
		var centerPixels = this.map.latLngToPixels(center);
		var outerPixels = this.map.latLngToPixels(outer);
		
		return Math.abs(outerPixels.x - centerPixels.x);

		/*if(!window.testMarker){
			window.testMarker = WPGMZA.Marker.createInstance({
				position: outer
			});
			WPGMZA.maps[0].addMarker(window.testMarker);
		}
		
		return 100;*/
	}
	
	WPGMZA.OLModernStoreLocatorCircle.prototype.getScale = function()
	{
		return 1;
	}
	
	WPGMZA.OLModernStoreLocatorCircle.prototype.destroy = function()
	{
		$(this.canvas).remove();
		
		this.map.olMap.un("postrender", this.renderFunction);
		this.map = null;
		this.canvas = null;
	}
	
});

// js/v8/open-layers/ol-polyline.js
/**
 * @namespace WPGMZA
 * @module OLPolyline
 * @requires WPGMZA.Polyline
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.OLPolyline = function(options, olFeature)
	{
		var self = this;
		
		WPGMZA.Polyline.call(this, options);
		
		this.olStyle = new ol.style.Style();
		
		if(olFeature)
		{
			this.olFeature = olFeature;
		}
		else
		{
			var coordinates = [];
			
			if(options && (options.polydata || options.points))
			{
				var path;
				
				if(options.polydata)
					path = this.parseGeometry(options.polydata);
				else	
					path = options.points;
				
				for(var i = 0; i < path.length; i++)
				{
					if(!($.isNumeric(path[i].lat)))
						throw new Error("Invalid latitude");
					
					if(!($.isNumeric(path[i].lng)))
						throw new Error("Invalid longitude");
					
					coordinates.push(ol.proj.fromLonLat([
						parseFloat(path[i].lng),
						parseFloat(path[i].lat)
					]));
				}
			}
			
			var params = this.getStyleFromSettings();
			this.olStyle = new ol.style.Style(params);
			
			this.olFeature = new ol.Feature({
				geometry: new ol.geom.LineString(coordinates)
			});
		}
		
		this.layer = new ol.layer.Vector({
			source: new ol.source.Vector({
				features: [this.olFeature]
			}),
			style: this.olStyle
		});
		
		this.layer.getSource().getFeatures()[0].setProperties({
			wpgmzaPolyline: this
		});
	}
	
	Parent = WPGMZA.Polyline;
		
	WPGMZA.OLPolyline.prototype = Object.create(Parent.prototype);
	WPGMZA.OLPolyline.prototype.constructor = WPGMZA.OLPolyline;
	
	WPGMZA.OLPolyline.prototype.getStyleFromSettings = function()
	{
		var params = {};
		
		if(this.settings.strokeOpacity)
			params.stroke = new ol.style.Stroke({
				color: WPGMZA.hexOpacityToRGBA(this.settings.strokeColor, this.settings.strokeOpacity),
				width: parseInt(this.settings.strokeWeight)
			});
			
		return params;
	}
	
	WPGMZA.OLPolyline.prototype.updateStyleFromSettings = function()
	{
		// Re-create the style - working on it directly doesn't cause a re-render
		var params = this.getStyleFromSettings();
		this.olStyle = new ol.style.Style(params);
		this.layer.setStyle(this.olStyle);
	}
	
	WPGMZA.OLPolyline.prototype.setEditable = function(editable)
	{
		
	}
	
	WPGMZA.OLPolyline.prototype.setPoints = function(points)
	{
		if(this.olFeature)
			this.layer.getSource().removeFeature(this.olFeature);
		
		var coordinates = [];
		
		for(var i = 0; i < points.length; i++)
			coordinates.push(ol.proj.fromLonLat([
				parseFloat(points[i].lng),
				parseFloat(points[i].lat)
			]));
		
		this.olFeature = new ol.Feature({
			geometry: new ol.geom.LineString(coordinates)
		});
		
		this.layer.getSource().addFeature(this.olFeature);
	}
	
	WPGMZA.OLPolyline.prototype.toJSON = function()
	{
		var result = Parent.prototype.toJSON.call(this);
		var coordinates = this.olFeature.getGeometry().getCoordinates();
		
		result.points = [];
		
		for(var i = 0; i < coordinates.length; i++)
		{
			var lonLat = ol.proj.toLonLat(coordinates[i]);
			var latLng = {
				lat: lonLat[1],
				lng: lonLat[0]
			};
			result.points.push(latLng);
		}
		
		return result;
	}
	
});

// js/v8/open-layers/ol-text.js
/**
 * @namespace WPGMZA
 * @module OLText
 * @requires WPGMZA.Text
 */
jQuery(function($) {
	
	WPGMZA.OLText = function()
	{
		
	}
	
});

// js/v8/tables/datatable.js
/**
 * @namespace WPGMZA
 * @module DataTable
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.DataTable = function(element)
	{
		var self = this;
		if(!$.fn.dataTable)
		{
			console.warn("The dataTables library is not loaded. Cannot create a dataTable. Did you enable 'Do not enqueue dataTables'?");
			
			if(WPGMZA.settings.wpgmza_do_not_enqueue_datatables && WPGMZA.getCurrentPage() == WPGMZA.PAGE_MAP_EDIT)
				alert("You have selected 'Do not enqueue DataTables' in WP Google Maps' settings. No 3rd party software is loading the DataTables library. Because of this, the marker table cannot load. Please uncheck this option to use the marker table.");
			
			return;
		}
		
		if($.fn.dataTable.ext)
			$.fn.dataTable.ext.errMode = "throw";
		else
		{
			var version = $.fn.dataTable.version ? $.fn.dataTable.version : "unknown";
			
			console.warn("You appear to be running an outdated or modified version of the dataTables library. This may cause issues with table functionality. This is usually caused by 3rd party software loading an older version of DataTables. The loaded version is " + version + ", we recommend version 1.10.12 or above.");
		}
		
		this.element = element;
		this.element.wpgmzaDataTable = this;
		this.dataTableElement = this.getDataTableElement();

		var settings = this.getDataTableSettings();
		
		this.phpClass			= $(element).attr("data-wpgmza-php-class");
		this.wpgmzaDataTable	= this;
		
		this.useCompressedPathVariable = (WPGMZA.restAPI.isCompressedPathVariableSupported && WPGMZA.settings.enable_compressed_path_variables);
		this.method = (this.useCompressedPathVariable ? "GET" : "POST");
		
		if(this.getLanguageURL() == undefined || this.getLanguageURL() == "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json") 
		{
			this.dataTable			= $(this.dataTableElement).DataTable(settings);
			this.dataTable.ajax.reload();
		}
		else{
		
			$.ajax(this.getLanguageURL(), {

				success: function(response, status, xhr)
				{
					self.languageJSON = response;
					self.dataTable = $(self.dataTableElement).DataTable(settings);
					self.dataTable.ajax.reload();
				}
				
			  });
		}
	}
	
	WPGMZA.DataTable.prototype.getDataTableElement = function()
	{
		return $(this.element).find("table");
	}
	
	/**
	 * This function wraps the request so it doesn't collide with WP query vars,
	 * it also adds the PHP class so that the controller knows which class to 
	 * instantiate
	 * @return object
	 */
	WPGMZA.DataTable.prototype.onAJAXRequest = function(data, settings)
	{
		// TODO: Move this to the REST API module and add useCompressedPathVariable
		var params = {
			"phpClass":	this.phpClass
		};
		
		var attr = $(this.element).attr("data-wpgmza-ajax-parameters");
		if(attr)
			$.extend(params, JSON.parse(attr));
		
		return $.extend(data, params);
	}
	
	WPGMZA.DataTable.prototype.onDataTableAjaxRequest = function(data, callback, settings)
	{
		var self = this;
		var element = this.element;
		var route = $(element).attr("data-wpgmza-rest-api-route");
		var params = this.onAJAXRequest(data, settings);
		var draw = params.draw;
		
		delete params.draw;
		
		if(!route)
			throw new Error("No data-wpgmza-rest-api-route attribute specified");
		
		var options = {
			method: "POST",
			useCompressedPathVariable: true,
			data: params,
			dataType: "json",
			cache: !this.preventCaching,
			beforeSend: function(xhr) {
				// Put draw in header, for compressed requests
				xhr.setRequestHeader("X-DataTables-Draw", draw);
			},
			success: function(response, status, xhr) {
				
				response.draw = draw;
				self.lastResponse = response;
				
				callback(response);
				self.onAJAXResponse(response);
				
			}
		};
		
		return WPGMZA.restAPI.call(route, options);
	}
	
	WPGMZA.DataTable.prototype.getDataTableSettings = function()
	{
		var self = this;
		var element = this.element;
		var options = {};
		
		if($(element).attr("data-wpgmza-datatable-options"))
			options = JSON.parse($(element).attr("data-wpgmza-datatable-options"));
	
		options.deferLoading = true;
		options.processing = true;
		options.serverSide = true;
		options.ajax = function(data, callback, settings) { 
			return WPGMZA.DataTable.prototype.onDataTableAjaxRequest.apply(self, arguments); 
		}
		
		if(WPGMZA.AdvancedTableDataTable && this instanceof WPGMZA.AdvancedTableDataTable && WPGMZA.settings.wpgmza_default_items)
			options.iDisplayLength = parseInt(WPGMZA.settings.wpgmza_default_items);
		
		options.aLengthMenu = [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", WPGMZA.localized_strings.all]];
		
		var languageURL = this.getLanguageURL();
		if(languageURL)
			options.language = {
				"url": languageURL
			};
		
		return options;
	}
	
	WPGMZA.DataTable.prototype.getLanguageURL = function()
	{
		if(!WPGMZA.locale)
			return null;
		
		var languageURL;
		
		switch(WPGMZA.locale.substr(0, 2))
		{
			case "af":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Afrikaans.json";
				break;

			case "sq":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Albanian.json";
				break;

			case "am":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Amharic.json";
				break;

			case "ar":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Arabic.json";
				break;

			case "hy":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Armenian.json";
				break;

			case "az":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Azerbaijan.json";
				break;

			case "bn":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Bangla.json";
				break;

			case "eu":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Basque.json";
				break;

			case "be":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Belarusian.json";
				break;

			case "bg":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Bulgarian.json";
				break;

			case "ca":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Catalan.json";
				break;

			case "zh":
				if(WPGMZA.locale == "zh_TW")
					languageURL = WPGMZA.pluginDirURL + "languages/datatables/Chinese-traditional.json";
				else
					languageURL = "//cdn.datatables.net/plug-ins/1.10.12/i18n/Chinese.json";
				break;

			case "hr":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Croatian.json";
				break;

			case "cs":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Czech.json";
				break;

			case "da":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Danish.json";
				break;

			case "nl":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Dutch.json";
				break;

			/*case "en":
				languageURL = "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json";
				break;*/

			case "et":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Estonian.json";
				break;

			case "fi":
				if(WPGMZA.locale.match(/^fil/))
					languageURL = WPGMZA.pluginDirURL + "languages/datatables/Filipino.json";
				else
					languageURL = WPGMZA.pluginDirURL + "languages/datatables/Finnish.json";
				break;

			case "fr":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/French.json";
				break;

			case "gl":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Galician.json";
				break;

			case "ka":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Georgian.json";
				break;

			case "de":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/German.json";
				break;

			case "el":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Greek.json";
				break;

			case "gu":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Gujarati.json";
				break;

			case "he":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Hebrew.json";
				break;

			case "hi":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Hindi.json";
				break;

			case "hu":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Hungarian.json";
				break;

			case "is":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Icelandic.json";
				break;

			/*case "id":
				languageURL = "//cdn.datatables.net/plug-ins/1.10.12/i18n/Indonesian-Alternative.json";
				break;*/
			
			case "id":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Indonesian.json";
				break;

			case "ga":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Irish.json";
				break;

			case "it":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Italian.json";
				break;

			case "ja":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Japanese.json";
				break;

			case "kk":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Kazakh.json";
				break;

			case "ko":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Korean.json";
				break;

			case "ky":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Kyrgyz.json";
				break;

			case "lv":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Latvian.json";
				break;

			case "lt":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Lithuanian.json";
				break;

			case "mk":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Macedonian.json";
				break;

			case "ml":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Malay.json";
				break;

			case "mn":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Mongolian.json";
				break;

			case "ne":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Nepali.json";
				break;

			case "nb":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Norwegian-Bokmal.json";
				break;
			
			case "nn":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Norwegian-Nynorsk.json";
				break;
			
			case "ps":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Pashto.json";
				break;

			case "fa":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Persian.json";
				break;

			case "pl":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Polish.json";
				break;

			case "pt":
				if(WPGMZA.locale == "pt_BR")
					languageURL = WPGMZA.pluginDirURL + "languages/datatables/Portuguese-Brasil.json";
				else
					languageURL = "//cdn.datatables.net/plug-ins/1.10.12/i18n/Portuguese.json";
				break;
			
			case "ro":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Romanian.json";
				break;

			case "ru":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Russian.json";
				break;

			case "sr":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Serbian.json";
				break;

			case "si":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Sinhala.json";
				break;

			case "sk":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Slovak.json";
				break;

			case "sl":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Slovenian.json";
				break;

			case "es":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Spanish.json";
				break;

			case "sw":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Swahili.json";
				break;

			case "sv":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Swedish.json";
				break;

			case "ta":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Tamil.json";
				break;

			case "te":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/telugu.json";
				break;

			case "th":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Thai.json";
				break;

			case "tr":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Turkish.json";
				break;

			case "uk":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Ukrainian.json";
				break;

			case "ur":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Urdu.json";
				break;

			case "uz":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Uzbek.json";
				break;

			case "vi":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Vietnamese.json";
				break;

			case "cy":
				languageURL = WPGMZA.pluginDirURL + "languages/datatables/Welsh.json";
				break;
		}
		
		return languageURL;
	}
	
	WPGMZA.DataTable.prototype.onAJAXResponse = function(response)
	{
		
	}
	
	WPGMZA.DataTable.prototype.reload = function()
	{
		this.dataTable.ajax.reload(null, false); // null callback, false for resetPaging
	}
	
});

// js/v8/tables/admin-marker-datatable.js
/**
 * @namespace WPGMZA
 * @module AdminMarkerDataTable
 * @requires WPGMZA.DataTable
 */
jQuery(function($) {
	
	WPGMZA.AdminMarkerDataTable = function(element)
	{
		var self = this;
		
		this.preventCaching = true;
		
		WPGMZA.DataTable.call(this, element);
		
		// NB: Pro marker panel currently manages edit marker buttons
		
		$(element).on("click", "[data-delete-marker-id]", function(event) {
			self.onDeleteMarker(event);
		});
		
		$(element).find(".wpgmza.select_all_markers").on("click", function(event) {
			self.onSelectAll(event);
		});
		
		$(element).find(".wpgmza.bulk_delete").on("click", function(event) {
			self.onBulkDelete(event);
		});

		$(element).on("click", "[data-center-marker-id]", function(event) {
			self.onCenterMarker(event);
		});
	}
	
	WPGMZA.AdminMarkerDataTable.prototype = Object.create(WPGMZA.DataTable.prototype);
	WPGMZA.AdminMarkerDataTable.prototype.constructor = WPGMZA.AdminMarkerDataTable;
	
	WPGMZA.AdminMarkerDataTable.createInstance = function(element)
	{
		return new WPGMZA.AdminMarkerDataTable(element);
	}
	
	WPGMZA.AdminMarkerDataTable.prototype.getDataTableSettings = function()
	{
		var self = this;
		var options = WPGMZA.DataTable.prototype.getDataTableSettings.call(this);
		
		options.createdRow = function(row, data, index)
		{
			var meta = self.lastResponse.meta[index];
			row.wpgmzaMarkerData = meta;
		}
		
		return options;
	}
	
	WPGMZA.AdminMarkerDataTable.prototype.onEditMarker = function(event)
	{
		WPGMZA.animatedScroll("#wpgmaps_tabs_markers");
	}
	
	WPGMZA.AdminMarkerDataTable.prototype.onDeleteMarker = function(event)
	{
		var self	= this;
		var id		= $(event.currentTarget).attr("data-delete-marker-id");
		
		var data	= {
			action: 'delete_marker',
			security: WPGMZA.legacyajaxnonce,
			map_id: WPGMZA.mapEditPage.map.id,
			marker_id: id
		};
		
		$.post(ajaxurl, data, function(response) {
			
			WPGMZA.mapEditPage.map.removeMarkerByID(id);
			self.reload();
			
		});
	}
	
	// NB: Move this to UGM
	WPGMZA.AdminMarkerDataTable.prototype.onApproveMarker = function(event)
	{
		var self	= this;
		var cur_id	= $(this).attr("id");
		
		var data = {
			action:		'approve_marker',
			security:	WPGMZA.legacyajaxnonce,
			map_id:		WPGMZA.mapEditPage.map.id,
			marker_id:	cur_id
		};
		$.post(ajaxurl, data, function (response) {
			
			
			wpgmza_InitMap();
			wpgmza_reinitialisetbl();

		});
	}
	
	WPGMZA.AdminMarkerDataTable.prototype.onSelectAll = function(event)
	{
		$(this.element).find("input[name='mark']").prop("checked", true);
	}
	
	WPGMZA.AdminMarkerDataTable.prototype.onBulkDelete = function(event)
	{
		var self = this;
		var ids = [];
		var map = WPGMZA.maps[0];
		
		$(this.element).find("input[name='mark']:checked").each(function(index, el) {
			var row = $(el).closest("tr")[0];
			ids.push(row.wpgmzaMarkerData.id);
		});
		
		ids.forEach(function(marker_id) {
			var marker = map.getMarkerByID(marker_id);
			
			if(marker)
				map.removeMarker(marker);
		});
		
		WPGMZA.restAPI.call("/markers/", {
			method: "DELETE",
			data: {
				ids: ids
			},
			complete: function() {
				self.reload();
			}
		});
	}

	WPGMZA.AdminMarkerDataTable.prototype.onCenterMarker = function(event)
	{
		var id;

		//Check if we have selected the center on marker button or called this function elsewhere 
		if(event.currentTarget == undefined)
		{
			id = event;
		}
		else{
			id = $(event.currentTarget).attr("data-center-marker-id");
		}

		var marker = WPGMZA.mapEditPage.map.getMarkerByID(id);
		
		if(marker){
			var latLng = new WPGMZA.LatLng({
				lat: marker.lat,
				lng: marker.lng
			});
			
			//Set a static zoom level
			var zoom_value = 6;
			WPGMZA.mapEditPage.map.setCenter(latLng);
			WPGMZA.mapEditPage.map.setZoom(zoom_value);
			WPGMZA.animateScroll("#wpgmaps_tabs_markers");
		}


	}
	
	$(document).ready(function(event) {
		
		$("[data-wpgmza-admin-marker-datatable]").each(function(index, el) {
			WPGMZA.adminMarkerDataTable = WPGMZA.AdminMarkerDataTable.createInstance(el);
		});
		
	});
	
});

// js/v8/admin-map-datatable.js
/**
 * @namespace WPGMZA
 * @module AdminDataTable
 * @requires WPGMZA.DataTable
 */
 jQuery(function($) {

 	WPGMZA.AdminMapDataTable = function(element) 
 	{	
 		var self = this;

 		WPGMZA.DataTable.call(this, element);

    	$(element).on("mousedown", "button[data-action='edit']", function(event){
        	switch (event.which) {
                case 1:
					var map_id = $(event.target).attr("data-map-id");
					window.location.href = window.location.href + "&action=edit&map_id=" + map_id;
                    break;
                case 2:
                    var map_id = $(event.target).attr("data-map-id");
					window.open(window.location.href + "&action=edit&map_id=" + map_id);
                    break;
            }
        });

 		$(element).find(".wpgmza.select_all_maps").on("click", function(event) {
			self.onSelectAll(event); 
		});
		
		$(element).find(".wpgmza.bulk_delete_maps").on("click", function(event) {
			self.onBulkDelete(event);
		});

		$(element).on("click", "button[data-action='duplicate']", function(event) {

			var map_id = $(event.target).attr('data-map-id');

			WPGMZA.restAPI.call("/maps/", {
				method: "POST",
				data: {
					id: map_id,
					action: "duplicate"
				},
				success: function(response, status, xhr) {
					self.reload();
				}
			});

		}); 

 		$(element).on("click", "button[data-action='trash']", function(event) {

 			var result = confirm(WPGMZA.localized_strings.map_delete_prompt_text);

 			if (result) {

	 			var map_id = $(event.target).attr('data-map-id');

	 			WPGMZA.restAPI.call("/maps/", {
	 				method: "DELETE",
	 				data: {
	 					id: map_id
	 				},
	 				success: function(response, status, xhr) {
	 					self.reload();
	 				}
	 			})
	 		}

 		});
 	}

 	WPGMZA.extend(WPGMZA.AdminMapDataTable, WPGMZA.DataTable);

 	WPGMZA.AdminMapDataTable.prototype.getDataTableSettings = function()
	{
		var self = this;
		var options = WPGMZA.DataTable.prototype.getDataTableSettings.call(this);
		
		options.createdRow = function(row, data, index)
		{
			var meta = self.lastResponse.meta[index];
			row.wpgmzaMapData = meta;
		}
		
		return options;
	}

 	WPGMZA.AdminMapDataTable.prototype.onSelectAll = function(event)
	{
		$(this.element).find("input[name='mark']").prop("checked", true);
	}

	WPGMZA.AdminMapDataTable.prototype.onBulkDelete = function(event)
	{
		var self = this;
		var ids = [];
		
		$(this.element).find("input[name='mark']:checked").each(function(index, el) {
			var row = $(el).closest("tr")[0];
			ids.push(row.wpgmzaMapData.id);
		});
		
		var result = confirm(WPGMZA.localized_strings.map_bulk_delete_prompt_text);

		if (result) {	
			WPGMZA.restAPI.call("/maps/", {
				method: "DELETE",
				data: {
					ids: ids
				},
				complete: function() {
					self.reload();
				}
			});		
		}
	}

 	$(document).ready(function(event){

 		$("[data-wpgmza-admin-map-datatable]").each(function(index, el) {
 			WPGMZA.AdminMapDataTable = new WPGMZA.AdminMapDataTable(el);
 		});

 	});

 });


// js/v8/advanced-page.js
/**
 * @namespace WPGMZA
 * @module AdvancedPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.AdvancedPage = function()
	{
		WPGMZA.restAPI.call("/markers?action=count-duplicates", {
			
			success: function(result) {
				
				// $("button#wpgmza-remove-duplicates").append(" (" + result.count + ")");
			
			}
				
		});
		
		$("button#wpgmza-remove-duplicates").on("click", function(event) {
			
			if(!confirm(WPGMZA.localized_strings.confirm_remove_duplicates))
				return;
			
			$(event.target).prop("disabled", true);
			
			WPGMZA.restAPI.call("/markers?action=remove-duplicates", {
				
				success: function(result) {
					
					alert(result.message);
					$(event.target).prop("disabled", false);
					
				}
				
			});
			
		});
	}
	
	$(window).on("load", function(event) {
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_ADVANCED)
			WPGMZA.advancedPage = new WPGMZA.AdvancedPage();
		
	});
	
});

// js/v8/categories-page.js
/**
 * @namespace WPGMZA
 * @module WPGMZA.CategoriesPage
 * @requires WPGMZA
 */
jQuery(function($){ 

	WPGMZA.CategoriesPage = function()
	{
		if($(".wpgmza-marker-icon-picker").length > 0)
			this.markerIconPicker = new WPGMZA.MarkerIconPicker($(".wpgmza-marker-icon-picker"));
	}
	
	$(window).on("load", function(event) {
		
		if(WPGMZA.getCurrentPage() == WPGMZA.PAGE_CATEGORIES)
			WPGMZA.categoriesPage = new WPGMZA.CategoriesPage();
		
	});

});

// js/v8/category-picker.js
/**
 * @namespace WPGMZA
 * @module WPGMZA.CategoryPicker
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.CategoryPicker = function(element)
	{
		var self = this;
		var data = JSON.parse( $(element).attr("data-js-tree-data") );
		
		this.element = element;
		this.input = $(this.element).find("input.wpgmza-category-picker-input");
		
		$(this.element).jstree({
			"core": {
				"data": data
			},
			"plugins": [
				"checkbox"
			]
		}).on("loaded.jstree", function() {
			$(self.element).jstree("open_all");
		});
		
		$(this.element).after(this.input);
		
		$(this.element).on("changed.jstree", function(e, data) {
			
			self.input.val(self.getSelection().join(","));
			
		});
	}
	
	WPGMZA.CategoryPicker.prototype.getSelection = function()
	{
		return $(this.element).jstree("get_selected");
	}
	
	WPGMZA.CategoryPicker.prototype.setSelection = function(arr)
	{
		$(this.element).jstree("deselect_all");
		
		if(!arr)
			return;
		
		$(this.element).jstree("select_node", arr);
	}
	
});

// js/v8/category-tree-node.js
/**
 * @namespace WPGMZA
 * @module CategoryTreeNode
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.CategoryTreeNode = function(options)
	{
		this.children = [];
		
		for(var name in options)
		{
			switch(name)
			{
				case "children":
				
					for(var i = 0; i < options.children.length; i++)
					{
						var child = WPGMZA.CategoryTreeNode.createInstance(options.children[i]);
						child.parent = this;
						this.children.push(child);
					}
					
					break;
				
				default:
				
					this[name] = options[name];
					
					break;
			}
		}
	}
	
	WPGMZA.extend(WPGMZA.CategoryTreeNode, WPGMZA.EventDispatcher);
	
	WPGMZA.CategoryTreeNode.createInstance = function(options)
	{
		return new WPGMZA.CategoryTreeNode(options);
	}
	
	WPGMZA.CategoryTreeNode.prototype.getChildByID = function(id)
	{
		if(this.id == id)
			return this;
		
		for(var i = 0; i < this.children.length; i++)
		{
			var result = this.children[i].getChildByID(id);
			
			if(result)
				return result;
		}
		
		return null;
	}
	
});

// js/v8/category-tree.js
/**
 * @namespace WPGMZA
 * @module CategoryTree
 * @requires WPGMZA.CategoryTreeNode
 */
jQuery(function($) {
	
	WPGMZA.CategoryTree = function(options)
	{
		WPGMZA.CategoryTreeNode.call(this, options);
	}
	
	WPGMZA.extend(WPGMZA.CategoryTree, WPGMZA.CategoryTreeNode);
	
	WPGMZA.CategoryTree.createInstance = function(options)
	{
		return new WPGMZA.CategoryTree(options);
	}
	
	WPGMZA.CategoryTree.prototype.getCategoryByID = function(id)
	{
		return this.getChildByID(id);
	}
	
	if(WPGMZA.categoryTreeData)
	{
		WPGMZA.categories = WPGMZA.CategoryTree.createInstance(WPGMZA.categoryTreeData);
		
		// Delete the data, we require that the user interacts with the interface, not the raw data
		delete WPGMZA.categoryTreeData;
	}
	
});

// js/v8/custom-field-filter-controller.js
/**
 * @namespace WPGMZA
 * @module CustomFieldFilterController
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/**
	 * This module handles the custom field filtering logic
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterController = function(map_id)
	{
		var self = this;
		
		this.map_id = map_id;
		this.widgets = [];
		this.ajaxTimeoutID = null;
		this.ajaxRequest = null;
		
		// TODO: This will break pagination (page count mismatch) when we integrate pagination for basic styles. I suggest we unify the filtering before doing so
		this.markerListingCSS = $("<style type='text/css'/>");
		$(document.body).append(this.markerListingCSS);
		
		WPGMZA.CustomFieldFilterController.controllersByMapID[map_id] = this;
		
		$("[data-wpgmza-filter-widget-class][data-map-id=" + map_id + "]").each(function(index, el) {
			self.widgets.push( WPGMZA.CustomFieldFilterWidget.createInstance(el) );
			
			$(el).on("input change", function(event) {
				self.onWidgetChanged(event);
			});
			
			if($(el).is(":checkbox"))
				$(el).on("click", function(event) {
					self.onWidgetChanged(event);
				});
		});
		
		var container = $(".wpgmza-filter-widgets[data-map-id='" + map_id + "']");
		$(container).find("button.wpgmza-reset-custom-fields").on("click", function(event) {
			$(container).find("input:not([type='checkbox']):not([type='radio']), textarea").val("");
			$(container).find("input[type='checkbox']").prop("checked", false);
			//$(container).find("option:selected").prop("selected", false);
			//$(container).find("option[value='*']").prop("selected", true);
			$(container).find("select").val("");
			self.onWidgetChanged();
		});
	};
	
	WPGMZA.CustomFieldFilterController.AJAX_DELAY = 500;
	WPGMZA.CustomFieldFilterController.controllersByMapID = {};
	WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID = {};
	
	WPGMZA.CustomFieldFilterController.createInstance = function(map_id)
	{
		return new WPGMZA.CustomFieldFilterController(map_id);
	};
	
	WPGMZA.CustomFieldFilterController.prototype.getAjaxRequestData = function() {
		var self = this;
		
		var result = {
			url: ajaxurl,
			method: "POST",
			data: {
				action: "wpgmza_custom_field_filter_get_filtered_marker_ids",
				map_id: this.map_id,
				widgetData: []
			},
			success: function(response, status, xhr) {
				self.onAjaxResponse(response, status, xhr);
			}
		};
		
		this.widgets.forEach(function(widget) {
			result.data.widgetData.push(widget.getAjaxRequestData());
		});
		
		return result;
	};
	
	WPGMZA.CustomFieldFilterController.prototype.onWidgetChanged = function(event) {
		var self = this;
		
		var map = WPGMZA.getMapByID(this.map_id);
		map.markerFilter.update({}, this);
	};
	
	WPGMZA.CustomFieldFilterController.prototype.onAjaxResponse = function(response, status, xhr) {
		this.lastResponse = response;
		
		var selectors = [];
		
		for(var marker_id in marker_array[this.map_id])
		{
			var visible = (response.marker_ids.length == 0 || response.marker_ids.indexOf(marker_id) > -1);
			marker_array[this.map_id][marker_id].setVisible(visible);
			
			if(!visible)
				selectors.push(".wpgmaps_mlist_row[mid='" + marker_id + "']");
		}
		
		if(wpgmaps_localize[this.map_id].order_markers_by && wpgmaps_localize[this.map_id].order_markers_by == 2)
		{
			wpgmza_update_data_table(
				WPGMZA.CustomFieldFilterController.dataTablesSourceHTMLByMapID[this.map_id],
				this.map_id
			);
		}
		else
		{
			this.markerListingCSS.html( selectors.join(", ") + "{ display: none; }" );
			
			var container;
			if(this.currAdvancedTableHTML)
				container = $("#wpgmza_marker_holder_" + this.map_id);
			else
				container = $(this.currAdvancedTableHTML);
			
			this.applyToAdvancedTable(container);
		}
	};
	
	/**
	 * This function is a quick hack to re-apply the last response after the store locator
	 * has been used or marker listing filtering changes. This should be deprecated and
	 * the filtering system unified at some point.
	 * @return void
	 */
	WPGMZA.CustomFieldFilterController.prototype.reapplyLastResponse = function() {
		if(!this.lastResponse)
			return;
		
		var response = this.lastResponse;
		
		for(var marker_id in marker_array[this.map_id])
		{
			var visible = (response.marker_ids.indexOf(marker_id) > -1);
			marker_array[this.map_id][marker_id].setVisible(visible);
		}
	};
	
	WPGMZA.CustomFieldFilterController.prototype.applyToAdvancedTable = function() {
		if(!this.lastResponse)
			return;
		
		var response = this.lastResponse;
		var container = $("#wpgmza_marker_holder_" + this.map_id);
		
		$(container).find("[mid]").each(function(index, el) {
			var marker_id = $(el).attr("mid");
			if(response.marker_ids.indexOf(marker_id) == -1)
				$(el).remove();
		});
	};
	
	$(window).on("load", function(event) {
		
		if(WPGMZA.is_admin == 1)
			return;
		
		$(".wpgmza_map").each(function(index, el) {
			var map_id = parseInt( $(el).attr("id").match(/\d+/)[0] );
			
			/*MYMAP[map_id].customFieldFilterController 
				= MYMAP[map_id].map.customFieldFilterController 
				= WPGMZA.CustomFieldFilterController.createInstance(map_id);*/

            setTimeout(function () {
                $(el).children('div').first().after($('.wpgmza-modern-marker-open-button'));
            }, 500);
		});
		
		
	});
	
});

// js/v8/custom-field-filter-widget.js
/**
 * @namespace WPGMZA
 * @module CustomFieldFilterWidget
 * @requires WPGMZA
 */
jQuery(function($) {

	/**
	 * This is the base module for custom field filter widgets
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget = function(element) {
		this.element = element;
	};
	
	WPGMZA.CustomFieldFilterWidget.createInstance = function(element) {
		var widgetPHPClass = $(element).attr("data-wpgmza-filter-widget-class");
		var constructor = null;
		
		switch(widgetPHPClass)
		{
			case "WPGMZA\\CustomFieldFilterWidget\\Text":
				constructor = WPGMZA.CustomFieldFilterWidget.Text;
				break;
				
			case "WPGMZA\\CustomFieldFilterWidget\\Dropdown":
				constructor = WPGMZA.CustomFieldFilterWidget.Dropdown;
				break;
			
			case "WPGMZA\\CustomFieldFilterWidget\\Checkboxes":
				constructor = WPGMZA.CustomFieldFilterWidget.Checkboxes;
				break;

			case "WPGMZA\\CustomFieldFilterWidget\\Time":
				constructor = WPGMZA.CustomFieldFilterWidget.Time;
				break;

			case "WPGMZA\\CustomFieldFilterWidget\\Date":
				constructor = WPGMZA.CustomFieldFilterWidget.Date;
				break;
				
			default:
				throw new Error("Unknown field type '" + widgetPHPClass + "'");
				break;
		}
		
		return new constructor(element);
	};
	
	WPGMZA.CustomFieldFilterWidget.prototype.getAjaxRequestData = function() {
		var data = {
			field_id: $(this.element).attr("data-field-id"),
			value: $(this.element).val()
		};
		
		return data;
	};
	
	/**
	 * Text field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Text = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Text.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Text.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Text;
	
	/**
	 * Dropdown field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Dropdown = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Dropdown.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Dropdown.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Dropdown;
	
	/**
	 * Checkboxes field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Checkboxes = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};
	
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype = Object.create(WPGMZA.CustomFieldFilterWidget.prototype);
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype.constructor = WPGMZA.CustomFieldFilterWidget.Checkboxes;
	
	WPGMZA.CustomFieldFilterWidget.Checkboxes.prototype.getAjaxRequestData = function() {
		var checked = [];
		
		$(this.element).find(":checked").each(function(index, el) {
			checked.push($(el).val());
		});
		
		return {
			field_id: $(this.element).attr("data-field-id"),
			value: checked
		}
	};
	
	$(document.body).on("mouseover", ".wpgmza-placeholder-label", function(event) {
	
		$(event.currentTarget).children("ul.wpgmza-checkboxes").stop(true, false).fadeIn();
	
	});
	
	$(document.body).on("mouseleave", ".wpgmza-placeholder-label", function(event) {
	
		$(event.currentTarget).children("ul.wpgmza-checkboxes").stop(true, false).fadeOut();
	
	});

	/**
	 * Time field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Time = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};

	WPGMZA.CustomFieldFilterWidget.Time.prototype.getAjaxRequestData = function() {

		var field_id = $(this.element).attr("data-field-id");

		var data = {
			field_id: field_id,
			value_start: $('[data-field-id="' + field_id + '"][data-date-start="true"]').val(),
			value_end: $('[data-field-id="' + field_id + '"][data-date-end="true"]').val(),
			type: 'time'
		};

		return data;
	};

	/**
	 * Date field custom field filter
	 * @constructor
	 */
	WPGMZA.CustomFieldFilterWidget.Date = function(element) {
		WPGMZA.CustomFieldFilterWidget.apply(this, arguments);
	};

	WPGMZA.CustomFieldFilterWidget.Date.prototype.getAjaxRequestData = function() {
		var field_id = $(this.element).attr("data-field-id");
		var data = {
			field_id: field_id,
			value_start: $('[data-field-id="' + field_id + '"][data-date-start="true"]').val(),
			value_end: $('[data-field-id="' + field_id + '"][data-date-end="true"]').val(),
			type: 'date'
		};
		
		return data;
	};
	
});

// js/v8/directions-box.js
/**
 * @namespace WPGMZA
 * @module DirectionsBox
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.DirectionsBox = function(map)
	{
		var self = this;
		
		this.map = map;
		this.element = $("#wpgmaps_directions_edit_" + map.id);
		
		this.element[0].wpgmzaMap = map;
		
		this.optionsElement = this.element.find(".wpgmza-directions-options");
		this.optionsElement.hide();
		
		this.showOptionsElement = this.element.find("#wpgmza_show_options_" + map.id);
		this.showOptionsElement.on("click", function(event) {
			self.onShowOptions(event);
		});
		
		this.hideOptionsElement = this.element.find("#wpgmza_hide_options_" + map.id);
		this.hideOptionsElement.on("click", function(event) {
			self.onHideOptions(event);
		});
		this.hideOptionsElement.hide();
		
		this.waypointTemplateItem = $(this.element).find(".wpgmaps_via.wpgmaps_template");
		this.waypointTemplateItem.removeClass("wpgmaps_template");
		this.waypointTemplateItem.remove();
		
		this.element.find(".wpgmaps_add_waypoint a").on("click", function(event) {
			self.onAddWaypoint(event);
		});
		
		this.element.on("click", ".wpgmza_remove_via", function(event) {
			self.onRemoveWaypoint(event);
		});
		
		if($("body").sortable)
			$(this.element).find(".wpgmaps_directions_outer_div [data-map-id]").sortable({
				items: ".wpgmza-form-field.wpgmaps_via"
			});
		
		this.getDirectionsButton = this.element.find(".wpgmaps_get_directions");
		this.getDirectionsButton.on("click", function(event) {
			self.onGetDirections();
		});
		
		$(this.element).find(".wpgmza-reset-directions").on("click", function(event) {
			self.onResetDirections(event);
		});
		
		$(this.element).find(".wpgmza-print-directions").on("click", function(event) {
			self.onPrintDirections(event);
		});
		
		this.service = WPGMZA.DirectionsService.createInstance(map);
		this.renderer = WPGMZA.DirectionsRenderer.createInstance(map);
		
		if(this.map.shortcodeAttributes.directions_from)
			$("#wpgmza_input_from_" + this.map.id).val(this.map.shortcodeAttributes.directions_from);
		
		if(this.map.shortcodeAttributes.directions_to)
			$("#wpgmza_input_to_" + this.map.id).val(this.map.shortcodeAttributes.directions_to);
		
		if(this.map.shortcodeAttributes.directions_waypoints)
		{
			var addresses = this.map.shortcodeAttributes.directions_waypoints.split("|");
			
			for(var i = 0; i < addresses.length; i++)
				this.addWaypoint(addresses[i]);
		}
		
		if(this.map.shortcodeAttributes.directions_auto == "true")
			this.route();
		
		if(this.openExternal && this.isUsingAppleMaps)
			$(".wpgmza-add-waypoint").hide();
	}
	
	WPGMZA.DirectionsBox.prototype = Object.create(WPGMZA.EventDispatcher);
	WPGMZA.DirectionsBox.prototype.constructor = WPGMZA.DirectionsBox;
	
	WPGMZA.DirectionsBox.STYLE_DEFAULT			= "default";
	WPGMZA.DirectionsBox.STYLE_MODERN			= "modern";
	
	WPGMZA.DirectionsBox.STATE_INPUT			= "input";
	WPGMZA.DirectionsBox.STATE_DISPLAY			= "display";
	
	WPGMZA.DirectionsBox.forceGoogleMaps = false;
	
	WPGMZA.DirectionsBox.createInstance = function(map)
	{
		if(WPGMZA.isModernComponentStyleAllowed() && (
				map.settings.directions_box_style == "modern" 
				|| 
				WPGMZA.settings.user_interface_style == "modern"
			))
			return new WPGMZA.ModernDirectionsBox(map);
		else
			return new WPGMZA.DirectionsBox(map);
	}
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "style", {
		
		get: function()
		{
			return this.map.settings.directions_box_style;
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "state", {
		
		set: function(value)
		{
			$(".wpgmza-directions-box[data-map-id='" + this.map.id + "']").show();
			
			switch(value)
			{
				case WPGMZA.DirectionsBox.STATE_INPUT:
				
					$("#wpgmaps_directions_editbox_" + this.map.id).show("slow");
					$("#wpgmaps_directions_notification_" + this.map.id).hide("slow");
					
					$(this.element).find("input.wpgmza-get-directions").show();
					$(this.element).find("a.wpgmza-reset-directions").hide();
					$(this.element).find("a.wpgmza-print-directions").hide();
					
					break;
				
				case WPGMZA.DirectionsBox.STATE_DISPLAY:
				
					$("#wpgmaps_directions_editbox_" + this.map.id).hide("slow");
					$("#wpgmaps_directions_notification_" + this.map.id).show("slow");
					
					$(this.element).find("input.wpgmza-get-directions").hide();
					$(this.element).find("a.wpgmza-reset-directions").show();
					$(this.element).find("a.wpgmza-print-directions").show();
					
					break;
				
				default:
				
					throw new Error("Unknown state");
					
					break;
			}
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "start", {
		
		get: function()
		{
			return this.from;
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "end", {
		
		get: function()
		{
			return this.to;
		}
		
	});
	
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "from", {
		
		get: function()
		{
			return $("#wpgmza_input_from_" + this.map.id).val();
		},
		
		set: function(value)
		{
			$("#wpgmza_input_from_" + this.map.id).val(value);
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "to", {
		
		get: function()
		{
			return $("#wpgmza_input_to_" + this.map.id).val();
		},
		
		set: function(value)
		{
			$("#wpgmza_input_to_" + this.map.id).val(value);
		}
		
	});
	
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "avoidTolls", {
		
		get: function()
		{
			return $("#wpgmza_tolls_" + this.map.id).is(":checked");
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "avoidHighways", {
		
		get: function()
		{
			return $("#wpgmza_highways_" + this.map.id).is(":checked");
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "avoidFerries", {
		
		get: function()
		{
			return $("#wpgmza_ferries_" + this.map.id).is(":checked");
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "travelMode", {
		
		get: function()
		{
			return $("#wpgmza_dir_type_" + this.map.id).val();
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "travelModeShort", {
		
		get: function()
		{
			return this.travelMode.substr(0, 1).toLowerCase();
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "openExternal", {
		
		get: function()
		{
			if(this.map.settings.directions_behaviour == "external")
				return true;
			
			if(this.map.settings.directions_behaviour == "intelligent")
				return WPGMZA.isTouchDevice();
			
			return false;
		}
		
	});
	
	Object.defineProperty(WPGMZA.DirectionsBox.prototype, "isUsingAppleMaps", {
		
		get: function()
		{
			return navigator.platform.match(/iPhone|iPod|iPad/) && !this.map.settings.force_google_directions_app;
		}
		
	});
	
	WPGMZA.DirectionsBox.prototype.getAjaxParameters = function()
	{
		var request = {
			origin: 					this.from,
			destination:				this.to,
			provideRouteAlternatives: 	true,
			avoidHighways:				this.avoidHighways,
			avoidTolls:					this.avoidTolls,
			avoidFerries:				this.avoidFerries,
			travelMode:					this.travelMode
		};
		
		var addresses = this.getWaypointAddresses();
		var waypoints = [];
		
		if(addresses.length)
		{
			for(var i in addresses)
			{
				var location = addresses[i];
				
				waypoints[i] = {
					location: location,
					stopover: false
				};
			}
			
			request.waypoints = waypoints;
		}
		
		return request;
	}
	
	WPGMZA.DirectionsBox.prototype.getWaypointAddresses = function()
	{
		var waypoints = $("#wpgmza_input_waypoints_" + this.map.id).val();
		var elements = $("#wpgmaps_directions_edit_" + this.map.id + " input.wpgmaps_via");
		var values = [];
		
		if(elements.length)
		{
			elements.each(function(index, el) {
				values.push($(el).val());
			});
		}
		
		return values;
	}
	
	WPGMZA.DirectionsBox.prototype.getExternalURLParameters = function(options)
	{
		var pararms, waypoints;
		
		if(!options)
			options = {};
		
		if(options.scheme == "apple")
		{
			params = {
				saddr:			this.from,
				daddr:			this.to
			};
			
			if(options.marker)
				params.daddr = options.marker.address;
		}
		else
		{
			params = {
				api:			1,
				origin:			this.from,
				destination:	this.to,
				travelmode:		this.travelMode
			};
		
			waypoints = this.getWaypointAddresses();
			
			if(waypoints.length)
				params.waypoints = waypoints.join("|");
			
			if(options.marker)
				params.destination = options.marker.address;
		}
		
		if(options.format == "string")
		{
			var components = [];
			
			for(var name in params)
				components.push(name + "=" + encodeURIComponent(params[name]));
			
			return "?" + components.join("&");
		}
		
		return params;
	}
	
	WPGMZA.DirectionsBox.prototype.getExternalURL = function(options)
	{
		if(!options)
			options = {};
		
		options = $.extend(options, {
			format: "string"
		});
		
		if(this.isUsingAppleMaps)
		{
			options.scheme = "apple";
			return "https://maps.apple.com/maps" + this.getExternalURLParameters(options);
		}
		
		return "https://www.google.com/maps/dir/" + this.getExternalURLParameters(options);
	}
	
	WPGMZA.DirectionsBox.prototype.route = function()
	{
		var self = this;
		
		if(this.from == "" && this.to == "")
		{
			alert(WPGMZA.localized_strings.please_fill_out_both_from_and_to_fields);
			return;
		}
		
		var params = this.getAjaxParameters();
		var usingModernStyleDirectionsBox =
			(
				WPGMZA.settings.user_interface_style == "legacy" 
				&&
				self.map.settings.directions_box_style == "modern"
			)
			|| 
			WPGMZA.settings.user_interface_style == "modern";

		this.state = WPGMZA.DirectionsBox.STATE_DISPLAY;
		
		if(this.map.modernDirectionsBox)
			this.map.modernDirectionsBox.open();
		
		this.service.route(params, function(response, status) {
			
			switch(status)
			{
				case WPGMZA.DirectionsService.SUCCESS:
				
					$("#wpgmaps_directions_notification_" + self.map.id).html("");
					$("#directions_panel_" + self.map.id).show();
					
					self.renderer.setDirections(response);
					
					break;
				
				case WPGMZA.DirectionsService.ZERO_RESULTS:
				
					self.state = WPGMZA.DirectionsBox.STATE_INPUT;
					
					$("#wpgmaps_directions_notification_" + self.map.id).html(WPGMZA.localized_strings.zero_results);
					
					self.reset();
					
					break;
				
				case WPGMZA.DirectionsService.NOT_FOUND:
				
					self.state = WPGMZA.DirectionsBox.STATE_INPUT;
					
					$("#wpgmaps_directions_notification_" + self.map.id).html(WPGMZA.localized_strings.zero_results);
					
					self.reset();
					
					if(response.geocoded_waypoints && response.geocoded_waypoints.length)
					{
						for(var i = 0; i < response.geocoded_waypoints.length; i++)
						{
							var waypoint = response.geocoded_waypoints[i];
							var status = waypoint.geocoder_status;
							
							if(status == WPGMZA.DirectionsService.NOT_FOUND)
							{
								if(i == 0)
								{
									$(self.element).find(".wpgmza-directions-from").addClass("wpgmza-not-found");
								}
								else if(i == response.geocoded_waypoints.length - 1)
								{
									$(self.element).find(".wpgmza-directions-to").addClass("wpgmza-not-found");
								}
								else
								{
									$($(self.element).find("div.wpgmza-waypoint-via")[i-1]).addClass("wpgmza-not-found");
								}
							}
						}
					}
					
					break;
				
				default:
				
					alert(WPGMZA.localized_strings.unknown_directions_service_status);
					
					this.state = WPGMZA.DirectionsBox.STATE_INPUT;
					
					break;
			}
			
		});
		
		
	}
	
	WPGMZA.DirectionsBox.prototype.reset = function()
	{
		$("#wpgmaps_directions_editbox_" + this.map.id).show();
		$("#directions_panel_" + this.map.id).hide();
		$("#directions_panel_" + this.map.id).html('');
		$("#wpgmaps_directions_notification_" + this.map.id).hide();
		$("#wpgmaps_directions_reset_" + this.map.id).hide();
		$("#wpgmaps_directions_notification_" + this.map.id).html(WPGMZA.localized_strings.fetching_directions);
		$(".wpgmza-not-found").removeClass("wpgmza-not-found");
		
		this.state = WPGMZA.DirectionsBox.STATE_INPUT;
		
		this.renderer.clear();
	}
	
	WPGMZA.DirectionsBox.prototype.showOptions = function(show)
	{
		if(show || arguments.length == 0)
		{
			this.optionsElement.show();
			this.showOptionsElement.hide();
			this.hideOptionsElement.show();
		}
		else
		{
			this.optionsElement.hide();
			this.showOptionsElement.show();
			this.hideOptionsElement.hide();
		}
	}
	
	WPGMZA.DirectionsBox.prototype.hideOptions = function()
	{
		this.showOptions(false);
	}
	
	WPGMZA.DirectionsBox.prototype.addWaypoint = function(address)
	{
		var row = this.waypointTemplateItem.clone();
		
		$(this.element).find("div.wpgmza-directions-to").before(row);
		
		if(address)
			$(row).find("input").val(address);
		
		if(window.google && google.maps && google.maps.places)
		{
			var options = {
				fields: ["name", "formatted_address"],
				types: ['geocode']
			};
			
			var restrict = wpgmaps_localize[this.map.id]['other_settings']['wpgmza_store_locator_restrict'];
			if(restrict && restrict.length)
				options.componentRestrictions = {
					country: restrict
				};
			
			new google.maps.places.Autocomplete($(row).find("input")[0], options);
		}
		
		return row;
	}
	
	WPGMZA.DirectionsBox.prototype.onAddWaypoint = function()
	{
		var row = this.addWaypoint();
		
		row.find("input").focus();
	}
	
	WPGMZA.DirectionsBox.prototype.onShowOptions = function(event)
	{
		$(this.element).find(".wpgmza-directions-options").show();
		$(this.element).find(".wpgmza-hide-directions-options").show();
		$(this.element).find(".wpgmza-show-directions-options").hide();
	}
	
	WPGMZA.DirectionsBox.prototype.onHideOptions = function(event)
	{
		$(this.element).find(".wpgmza-directions-options").hide();
		$(this.element).find(".wpgmza-hide-directions-options").hide();
		$(this.element).find(".wpgmza-show-directions-options").show();
	}
	
	WPGMZA.DirectionsBox.prototype.onRemoveWaypoint = function()
	{
		$(event.target).closest(".wpgmza-form-field").remove();
	}
	
	WPGMZA.DirectionsBox.prototype.onGetDirections = function(event)
	{
		if(this.openExternal)
		{
			window.open(this.getExternalURL(), "_blank");
			return;
		}
		
		this.route();
	}
	
	WPGMZA.DirectionsBox.prototype.onPrintDirections = function(event)
	{
		var url = this.getExternalURL() + "&om=1";
		window.open(url, "_blank");
	}
	
	WPGMZA.DirectionsBox.prototype.onResetDirections = function(event)
	{
		this.reset();
	}

	$(document.body).on("click", ".wpgmza_gd, .wpgmza-directions-button", function(event) {
		
		var component;
		var marker, address, coords, map;
		
		component = $(event.currentTarget).closest("[data-wpgmza-marker-listing]");
		
		if(!component.length)
			component = $(event.currentTarget).closest(".wpgmza_modern_infowindow, [data-map-id]");
		
		if(!component.length)
			return; // NB: ProInfoWindow handles this
		
		if(component.length)
		{
			var element = component[0];
			
			if(element.wpgmzaMarkerListing)
			{
				map = element.wpgmzaMarkerListing.map;
				marker = map.getMarkerByID($(event.currentTarget).closest("[data-marker-id]").attr("data-marker-id"));
			}
			else if(element.wpgmzaInfoWindow)
			{
				marker = element.wpgmzaInfoWindow.mapObject;
				map = marker.map;
			}
			else if(element.wpgmzaMap)
			{
				map = element.wpgmzaMap;
				marker = element.wpgmzaMap.getMarkerByID($(event.currentTarget).attr("data-marker-id"));
			}
		}
		
		address = marker.address;
		coords = marker.getPosition().toString();
		
		if(map.directionsBox.openExternal)
			window.open(map.directionsBox.getExternalURL({marker: marker}));
		else
		{
			map.directionsBox.state = WPGMZA.DirectionsBox.STATE_INPUT;				
			map.directionsBox.to = (address && address.length ? address : coords);
			$("#wpgmza_input_from_" + map.id).focus().select();
			
			if(map.directionsBox instanceof WPGMZA.ModernDirectionsBox)
				map.directionsBox.open();
			else
				WPGMZA.animateScroll( map.directionsBox.element );
		}
		
	});
	
});

// js/v8/directions-renderer.js
/**
 * @namespace WPGMZA
 * @module DirectionsRenderer
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.DirectionsRenderer = function(map)
	{
		WPGMZA.EventDispatcher.apply(this, arguments);
		
		this.map = map;
	}
	
	WPGMZA.extend(WPGMZA.DirectionsRenderer, WPGMZA.EventDispatcher);
	
	WPGMZA.DirectionsRenderer.createInstance = function(map)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLDirectionsRenderer(map);
				break;
			
			default:
				return new WPGMZA.GoogleDirectionsRenderer(map);
				break;
		}
	}
	
	WPGMZA.DirectionsRenderer.prototype.setDirections = function(directions)
	{
		
	}
	
});

// js/v8/directions-service.js
/**
 * @namespace WPGMZA
 * @module DirectionsService
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.DirectionsService = function(map)
	{
		WPGMZA.EventDispatcher.apply(this, arguments);
		
		this.map = map;
	}
	
	WPGMZA.extend(WPGMZA.DirectionsService, WPGMZA.EventDispatcher);
	
	WPGMZA.DirectionsService.ZERO_RESULTS	= "zero-results";
	WPGMZA.DirectionsService.NOT_FOUND		= "not-found";
	WPGMZA.DirectionsService.SUCCESS		= "success";
	
	WPGMZA.DirectionsService.DRIVING		= "driving";
	WPGMZA.DirectionsService.WALKING		= "walking";
	WPGMZA.DirectionsService.TRANSIT		= "transit";
	WPGMZA.DirectionsService.BICYCLING		= "bicycling";
	
	WPGMZA.DirectionsService.createInstance = function(map)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLDirectionsService(map);
			
			default:
				return new WPGMZA.GoogleDirectionsService(map);
		}
	}
	
	WPGMZA.DirectionsService.route = function(params, callback)
	{
		
	}
	
});

// js/v8/heatmap.js
/**
 * @namespace WPGMZA
 * @module Heatmap
 * @requires WPGMZA.MapObject
 */
jQuery(function($) {
	
	WPGMZA.Heatmap = function(row)
	{
		var self = this;
		
		WPGMZA.assertInstanceOf(this, "EventDispatcher");
		
		this.name = "";
		this.points = [];
		
		WPGMZA.MapObject.apply(this, arguments);
		
		// Parse gradient
		if(typeof this.settings.gradient != "array")
		{
			console.warn("Ignoring invalid gradient");
			delete this.settings.gradient;
			
			this.settings.gradient = [
				"rgba(0, 255, 255, 0)",
				"rgba(0, 255, 255, 1)",
				"rgba(0, 191, 255, 1)",
				"rgba(0, 127, 255, 1)",
				"rgba(0, 63, 255, 1)",
				"rgba(0, 0, 255, 1)",
				"rgba(0, 0, 223, 1)",
				"rgba(0, 0, 191, 1)",
				"rgba(0, 0, 159, 1)",
				"rgba(0, 0, 127, 1)",
				"rgba(63, 0, 91, 1)",
				"rgba(127, 0, 63, 1)",
				"rgba(191, 0, 31, 1)",
				"rgba(255, 0, 0, 1)"
			];
		}
		
		// Keep a hash map of points so they can be quickly looked up by lat/lng (for removing them)
		this.hashMap = {};
		
		// Parse points
		if(row && row.points)
		{
			this.points = this.parseGeometry(row.points);
			for(var i = 0; i < this.points.length; i++)
				this.addPointToHashMap(this.points[i]);
		}
	}
	
	WPGMZA.Heatmap.prototype = Object.create(WPGMZA.MapObject.prototype);
	WPGMZA.Heatmap.prototype.constructor = WPGMZA.Heatmap;
	
	WPGMZA.Heatmap.getConstructor = function()
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return WPGMZA.OLHeatmap;
				break;
			
			default:
				return WPGMZA.GoogleHeatmap;
				break;
		}
	}
	
	WPGMZA.Heatmap.createInstance = function(row)
	{
		var constructor = WPGMZA.Heatmap.getConstructor();
		return new constructor(row);
	}
	
	WPGMZA.Heatmap.prototype.getHashFromLatLng = function(latLng)
	{
		return parseFloat(latLng.lat).toFixed(11) + "," + parseFloat(latLng.lng).toFixed(11);
	}
	
	WPGMZA.Heatmap.prototype.addPointToHashMap = function(point)
	{
		var hash = this.getHashFromLatLng({
			lat: point.lat,
			lng: point.lng
		});
		this.hashMap[hash] = point;
	}
	
	WPGMZA.Heatmap.prototype.addPoint = function(latLng)
	{
		this.points.push(latLng);
		this.addPointToHashMap(latLng);
		this.modified = true;
	}
	
	WPGMZA.Heatmap.prototype.removePoint = function(latLng)
	{
		var hash = this.getHashFromLatLng(latLng);
		var point = this.hashMap[hash];
		var index = this.points.indexOf(point);
		
		if(index == -1)
		{
			console.warn("No point found at " + hash);
			return;
		}
		
		this.points.splice(index, 1);
		this.modified = true;
	}
	
	WPGMZA.Heatmap.prototype.toJSON = function()
	{
		var result = WPGMZA.MapObject.prototype.toJSON.call(this);
		
		result.points = [];
		
		for(var i = 0; i < this.points.length; i++)
		{
			var latLng = this.points[i];
			result.points.push({
				lat: latLng.lat,
				lng: latLng.lng
			});
		}
		
		return result;
	}
	
});

// js/v8/legacy-json-converter.js
/**
 * @namespace WPGMZA
 * @module LegacyJSONConverter
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.LegacyJSONConverter = function()
	{
		
	}
	
	WPGMZA.LegacyJSONConverter.prototype.convert = function(json)
	{
		var markers = [];
		
		if(typeof json == "string")
			json = JSON.parse(json);
		
		for(var key in json)
		{
			
			function getField(name)
			{
				return json[name];
			}
			
			var data = {
				map_id:			getField("map_id"),
				marker_id:		getField("marker_id"),
				title:			getField("title"),
				address:		getField("address"),
				icon:			getField("icon"),
				pic:			getField("pic"),
				desc:			getField("desc"),
				linkd:			getField("linkd"),
				anim:			getField("anim"),
				retina:			getField("retina"),
				category:		getField("category"),
				lat:			getField("lat"),
				lng:			getField("lng"),
				infoopen:		getField("infoopen")
			};
			
			markers[data.marker_id] = data;
			
		}
		
		return markers;
	}
	
});

// js/v8/marker-gallery-input.js
/**
 * @namespace WPGMZA
 * @module MarkerGalleryInput
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.MarkerGalleryInput = function(input)
	{
		var self = this;
		var container = $(input).parent();
		
		container.append("<div class='wpgmza-gallery-input'><ul><li class='wpgmza-add-new-picture'><i class='fa fa-camera' aria-hidden='true'></i></li></ul></div>");
		
		this.input = input;
		this.element = container.find(".wpgmza-gallery-input");
		
		$(this.input).next("#upload_image_button").remove();
		$(this.input).hide();
		
		this.addNewPictureButton = $(this.element).find(".wpgmza-add-new-picture");
		this.addNewPictureButton.on("click", function(event) {
			self.onAddNewPictureClicked(event);
		});
		
		this.templateItem = $(this.addNewPictureButton).clone();
		this.templateItem.removeClass("wpgmza-add-new-picture");
		this.templateItem.find("i").remove();
		
		$(this.element).find("ul").sortable({
			items: "li:not(.wpgmza-add-new-picture)",
			stop: function() {
				self.onDragEnd();
			}
		});
		
		$(document.body).on("click", ".wpgmza-delete-gallery-item", function(event) {
			self.onDeleteItem(event);
		});
	}
	
	WPGMZA.MarkerGalleryInput.prototype.populate = function(arr)
	{
		this.clear();
		
		if(!arr || !arr.length)
			return;
		
		for(var i = 0; i < arr.length; i++)
			this.addPicture(arr[i]);
	}
	
	WPGMZA.MarkerGalleryInput.prototype.update = function()
	{
		var string = this.serialize();
		
		this.input.val(string);
		this.input.attr("value", string);
	}
	
	WPGMZA.MarkerGalleryInput.prototype.clear = function()
	{
		$(this.element).find("[data-picture-url]").remove();
	}
	
	WPGMZA.MarkerGalleryInput.prototype.addPicture = function(picture)
	{
		var item = this.templateItem.clone();
		var url = picture.url;
		
		item.css({
			"background-image": "url('" + url + "')"
		});
		item.attr("data-picture-url", url);
		item.attr("data-attachment-id", picture.attachment_id);
		item.insertBefore(this.addNewPictureButton);
		
		item.append($("<button type='button' class='wpgmza-delete-gallery-item'>✖</button>"));
		
		this.update();
	}
	
	WPGMZA.MarkerGalleryInput.prototype.serialize = function()
	{
		return JSON.stringify(this.toJSON());
	}
	
	WPGMZA.MarkerGalleryInput.prototype.toJSON = function()
	{
		var gallery = [];
		
		$(this.element).find("[data-picture-url]").each(function(index, el) {
			gallery.push({
				attachment_id:	$(el).attr("data-attachment-id"),
				url: 			$(el).attr("data-picture-url")
			});
		});
		
		return gallery;
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onDragEnd = function()
	{
		this.update();
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onAttachmentPicked = function(attachment_id, attachment_url)
	{
		this.addPicture({
			attachment_id: attachment_id,
			url: attachment_url
		});
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onAddNewPictureClicked = function(event)
	{
		var self = this;
		
		WPGMZA.openMediaDialog(function(attachment_id, attachment_url) {
			
			self.onAttachmentPicked(attachment_id, attachment_url);
			
		});
	}
	
	WPGMZA.MarkerGalleryInput.prototype.onDeleteItem = function(event)
	{
		$(event.target).closest("[data-picture-url]").remove();
	}
	
});

// js/v8/marker-gallery.js
/**
 * @namespace WPGMZA
 * @module WPGMZA.MarkerGallery
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.MarkerGallery = function(marker, context)
	{
		var self = this;
		var guid = WPGMZA.guid();
		
		this.element = $("<div class='wpgmza-empty-gallery'/>");
		this.marker = marker;
		
		if(!marker.gallery)
			return;
		
		if(marker.gallery.length < 2)
		{
			// NB: No carousel with only one item.
			// NB: Check that thumbnail exists. Users migrating from legacy versions may have this set as false
			
			var first	= marker.gallery[0];
			
			if(marker.gallery.length == 0)
				return;
			
			var preview	= first.thumbnail ? first.thumbnail : first.url;
			
			var img = context.getImageElementFromURL(preview);
			img.attr("data-featherlight", first.url);
			
			if(context instanceof WPGMZA.ProInfoWindow)
			{
				img.attr("id", guid);
				
				context.on("domready", function(event) {
					
					$("#" + guid).on("click", function(event) {
						self.onFeatherLightClick(event);
					});
					
				});
			}
			
			this.element = img;
			
			return;
		}
		
		this.element = $("<div class='wpgmza-marker-gallery'><div id='" + guid + "' class='owl-carousel'></div></div>");
		this.carouselElement = this.element.find(".owl-carousel");
		
		marker.gallery.forEach(function(item) {
			self.addPicture(item, context);
		});
		
		if(context instanceof WPGMZA.ProInfoWindow)
		{
			var width = context.imageWidth;
			
			if(!width)
				width = 200;
			
			this.element.css({
				"width": width + "px",
				"max-width": width + "px",
				"overflow": "hidden"
			});
			
			this.carouselElement.css({
				"width": width + "px",
				"max-width": width + "px",
				"overflow": "hidden"
			});
			
			context.on("domready", function(event) {
				
				// NB: For some reason, this will fail on a Google native InfoWindow if you try to use the element directly, so a GUID is used instead.
				// It might be a good idea to pass the element in rather than self creating. That's how all other components with elements work.
				$("#" + guid).owlCarousel(self.getOwlCarouselOptions());
				
				$("#" + guid).on("click", "[data-featherlight]", function(event) {
					self.onFeatherLightClick(event);
				});
			
			});
		}
		else
		{
			if(context instanceof WPGMZA.CarouselMarkerListing)
			{
				setTimeout(function() {
					
					var width = $(context.element).find(".owl-item").innerWidth() - 40;
					
					self.element.css({
						"width": width + "px",
						"max-width": width + "px",
						"overflow": "hidden"
					});
					
					self.carouselElement.css({
						"width": width + "px",
						"max-width": width + "px",
						"overflow": "hidden"
					});
					
					$(self.carouselElement).owlCarousel(self.getOwlCarouselOptions());
					
				}, 1000);
			}
			else
				setTimeout(function() {
					$(self.carouselElement).owlCarousel(self.getOwlCarouselOptions());
				}, 100);
		}
	}
	
	WPGMZA.MarkerGallery.prototype.getOwlCarouselOptions = function()
	{
		return {
			navigation: true,
			pagination: false,
			dots: false,
			slideSpeed: 3000,
			paginationSpeed: 400,
			singleItem: true,
			loop: true,
			items: 1,
			autoplay: true,
			autoplayTimeout: 4000
		};
	}
	
	WPGMZA.MarkerGallery.prototype.addPicture = function(item, context)
	{
		var container = $("<div/>"), img;
		
		// NB: Check that thumbnail exists. Users migrating from legacy versions may have this set as false
		if(!item.thumbnail)
			item.thumbnail = item.url;
		
		if(context instanceof WPGMZA.ProInfoWindow)
		{
			img = context.getImageElementFromURL(item.thumbnail);
		}
		else
		{
			img = $("<img/>");
			img.attr("src", item.thumbnail);
		}
		
		img.css({"float": "none"});
		img.attr("data-featherlight", item.url);
		
		container.append(img);
		
		$(this.carouselElement).append(container);
	}
	
	WPGMZA.MarkerGallery.prototype.onFeatherLightClick = function(event)
	{
		var self = this;
		
		if(WPGMZA.isFullScreen())
		{
			// NB: Allow a short delay for featherlight to open first
			setTimeout(function() {
				$( $(self.marker.map.element).find(".gm-style")[0] ).append($(".featherlight"));
			}, 250);
		}
	}
	
	$(document).on("fullscreenchange", function() {
		
		$(".featherlight").remove();
		
	});
	
});

// js/v8/marker-icon-picker.js
/**
 * @namespace WPGMZA
 * @module MarkerIconPicker
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.MarkerIconPicker = function(element)
	{
		var self = this;
		
		if(!element)
			throw new Error("Element cannot be undefined");
		
		if(!(element instanceof HTMLElement) && !(element instanceof jQuery && element.length == 1))
			throw new Error("Invalid element");
		
		this.element = element;
		
		var input = $(this.element).find("input[name]");
		var name =  $(input).attr("name");
		
		if(input.length)
		{
			if(!name)
				throw new Error("Input must have a name for marker library to function");
			
			$(this.element).find("button.wpgmza-marker-library").attr("data-target-name", name);
			
			var icon = WPGMZA.MarkerIcon.createInstance(input.val());
			
			// NB: The above seems to be unfinished, or redundant
		}
		
		$(this.element).find("button.wpgmza-upload").on("click", function(event) {
			self.onUploadImage(event);
		});
		
		$(this.element).find("button.wpgmza-reset").on("click", function(event) {
			self.onReset(event);
		});
	}
	
	WPGMZA.MarkerIconPicker.prototype.setIcon = function(input)
	{
		var icon = WPGMZA.MarkerIcon.createInstance(input);
		var url = icon.url;
		
		var preview = url;
		
		if(url != WPGMZA.defaultMarkerIcon)
			$(this.element).find("input.wpgmza-marker-icon-url").val(url);
		else
			$(this.element).find("input.wpgmza-marker-icon-url").val("");
		
		if(url.length == 0)
			preview = WPGMZA.defaultMarkerIcon;
		
		$(this.element).find(".wpgmza-marker-icon-preview").css({
			"background-image": "url('" + preview + "')"
		});
	}
	
	WPGMZA.MarkerIconPicker.prototype.onUploadImage = function()
	{
		var self = this;
		
		WPGMZA.openMediaDialog(function(attachment_id, attachment_url) {
			self.setIcon(attachment_url);
			$(this.element).find("input.wpgmza-marker-icon-url").val("");
		});
	}
	
	WPGMZA.MarkerIconPicker.prototype.onReset = function()
	{
		this.reset();
	}
	
	WPGMZA.MarkerIconPicker.prototype.reset = function()
	{
		this.setIcon(WPGMZA.defaultMarkerIcon);
	}
	
});

// js/v8/marker-icon.js
/**
 * @namespace WPGMZA
 * @module MarkerIcon
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.MarkerIcon = function(options)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.apply(this, arguments);
		
		this.isLoaded	= false;
		
		this.url		= "";
		this.retina		= false;
		
		if(typeof options == "object")
		{
			for(var key in options)
				this[key] = options[key];
		}
		else if(typeof options == "string")
		{
			try{
				var json = JSON.parse(options);
				
				for(var key in json)
					this[key] = json[key];
			}catch(e) {
				this.url = options;
			}
		}
		else if(options)
			throw new Error("Argument must be an object");
		
		this.url = this.url.replace(/^http(s?):/, "");
		
		this.dimensions = {
			width: null,
			height: null
		};
		
		var url = (this.isDefault ? WPGMZA.defaultMarkerIcon : this.url);
		WPGMZA.getImageDimensions(url, function(dimensions) {
			
			self.dimensions = dimensions;
			
			self.isLoaded = true;
			self.trigger("load");
			
		});
	}
	
	WPGMZA.extend(WPGMZA.MarkerIcon, WPGMZA.EventDispatcher);
	
	WPGMZA.MarkerIcon.createInstance = function(options)
	{
		return new WPGMZA.MarkerIcon(options);
	}
	
	Object.defineProperty(WPGMZA.MarkerIcon.prototype, "width", {
		
		get: function()
		{
			if(this.retina)
				return parseInt(WPGMZA.settings.retinaWidth);
				
			return parseInt(this.dimensions.width);
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerIcon.prototype, "height", {
		
		get: function()
		{
			if(this.retina)
				return parseInt(WPGMZA.settings.retinaHeight);
				
			return parseInt(this.dimensions.height);
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerIcon.prototype, "isDefault", {
		
		"get": function()
		{
			return this.url.length == 0 || this.url == WPGMZA.defaultMarkerIcon;
		}
		
	});
	
	WPGMZA.MarkerIcon.prototype.applyToElement = function(element)
	{
		if(this.isDefault)
			$(element).attr("src", WPGMZA.defaultMarkerIcon);
		else
			$(element).attr("src", this.url);
		
		if(this.retina)
		{
			$(element).css({
				"width":	this.width + "px",
				"height":	this.height + "px"
			});
		}
	}
	
});


// js/v8/modern-directions-box.js
/**
 * @namespace WPGMZA
 * @module ModernDirectionsBox
 * @requires WPGMZA.DirectionsBox
 */
jQuery(function($) {
	
	/**
	 * The new modern look directions box. It takes the elements
	 * from the default look and moves them into the map, wrapping
	 * in a new element so we can apply new styles.
	 * @return Object
	 */
	WPGMZA.ModernDirectionsBox = function(map) {
		
		WPGMZA.DirectionsBox.apply(this, arguments);
		
		var self = this;
		var original = this.element;
		
		if(!original.length)
			return;
		
		var container = $(map.element);
		
		this.map = map;
		
		// Build element
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-directions-box'></div>");
		this.panel = new WPGMZA.PopoutPanel(this.element);
		
		// Add to DOM tree
		this.element.append(original);
		container.append(this.element);
		
		// Add buttons
		$(this.element).find("h2").after($("\
			<div class='wpgmza-directions-buttons'>\
				<span class='wpgmza-close'><i class='fa fa-times' aria-hidden='true'></i></span>\
			</div>\
		"));
		
		// Remove labels
		$(this.element).find("td:first-child").remove();
		
		// Move show options and options box to after the type select
		var row = $(this.element).find("select[name^='wpgmza_dir_type']").closest("tr");
		$(this.element).find(".wpgmaps_to_row").after(row);
		
		// Options box
		$(this.element).find("#wpgmza_options_box_" + map.id).addClass("wpgmza-directions-options");
		
		// Fancy checkboxes (This would require adding admin styles)
		//$(this.element).find("input:checkbox").addClass("postform cmn-toggle cmn-toggle-round-flat");
		
		// NB: Via waypoints is handled below to be compatible with legacy systems. Search "Waypoint JS"
		
		// Result box
		this.resultBox = new WPGMZA.ModernDirectionsResultBox(map, this);
		
		var behaviour = map.settings.directions_behaviour;
		
		if(behaviour == "intelligent")
		{
			if(WPGMZA.isTouchDevice())
				behaviour = "external";
			else
				behaviour = "default";
		}
		
		if(behaviour == "default")
		{
			$(this.element).find(".wpgmaps_get_directions").on("click", function(event) {
				if(self.from.length == 0 || self.to.length == 0)
					return;
				
				self.resultBox.open();
			});
		}
		
		// Close button
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.panel.close();
		});

		$(this.element).on('click', '.wpgmza-travel-mode-option', function(){
		    var mode = jQuery(this).data('mode');
		    jQuery('body').find('.wpgmza-travel-mode-option').removeClass('wpgmza-travel-option__selected');
		    jQuery(this).addClass('wpgmza-travel-option__selected');
		    jQuery('body').find('.wpgmza-travel-mode').val(mode);
		});
	};
	
	WPGMZA.extend(WPGMZA.ModernDirectionsBox, WPGMZA.DirectionsBox);
	
	Object.defineProperty(WPGMZA.ModernDirectionsBox.prototype, "from", {
		get: function() {
			return $(this.element).find("input.wpgmza-directions-from").val();
		},
		set: function(value) {
			return $(this.element).find("input.wpgmza-directions-from").val(value);
		}
	});
	
	Object.defineProperty(WPGMZA.ModernDirectionsBox.prototype, "to", {
		get: function() {
			return $(this.element).find("input.wpgmza-directions-to").val();
		},
		set: function(value) {
			return $(this.element).find("input.wpgmza-directions-to").val(value);
		}
	});
	
	/**
	 * Opens the popup and closes the results box if it's open
	 * @return void
	 */
	WPGMZA.ModernDirectionsBox.prototype.open = function()
	{
		this.panel.open();
		
		if(this.resultBox)
			this.resultBox.close();
		
		$(this.element).children().show();
	};
	
	/**
	 * Fires when the "open native map" button is clicked
	 * @return void
	 */
	WPGMZA.ModernDirectionsBox.prototype.onNativeMapsApp = function()
	{
		var url = this.getExternalURL();
		window.open(url, "_blank");
	}
	
});

// js/v8/modern-directions-result-box.js
/**
 * @namespace WPGMZA
 * @module ModernDirectionsResultBox
 * @requires WPGMZA.PopoutPanel
 */
jQuery(function($) {
	
	/**
	 * The second step of the directions box
	 * @return Object
	 */
	WPGMZA.ModernDirectionsResultBox = function(map, directionsBox)
	{
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var self = this;
		var container = $(map.element);
		
		this.map = map;
		
		this.directionsBox = directionsBox;
		
		// Build element
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-directions-box'>\
			<h2 class='wpgmza-directions-box__title'>" + $(directionsBox.element).find("h2").html() + "</h2>\
			<div class='wpgmza-directions-buttons'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
				<a class='wpgmza-print' style='display: none;'><i class='fa fa-print' aria-hidden='true'></i></a>\
			</div>\
			<div class='wpgmza-directions-results'>\
			</div>\
		</div>");

		this.element.addClass('wpgmza-modern-directions-box__results');
		
		var nativeIcon = new WPGMZA.NativeMapsAppIcon();
		this.nativeMapAppIcon = nativeIcon;
		$(this.element).find(".wpgmza-directions-buttons").append(nativeIcon.element);
		$(nativeIcon.element).on("click", function(event) {
			self.onNativeMapsApp(event);
		});
		
		// Add to DOM tree
		container.append(this.element);
		
		this.element.append($("#directions_panel_" + map.id));
		
		// Print directions link
		$(this.element).find(".wpgmza-print").attr("href", "data:text/html,<script>document.body.innerHTML += sessionStorage.wpgmzaPrintDirectionsHTML; window.print();</script>");
		
		// Event listeners
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
		
		$(this.element).find(".wpgmza-print").on("click", function(event) {
			self.onPrint(event);
		});
		
		this.map.on("directionsserviceresult", function(event) {
			self.onDirectionsChanged(event, event.response, event.status);
		});
		
		// Initial state
		this.clear();
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernDirectionsResultBox.prototype.constructor = WPGMZA.ModernDirectionsResultBox;
	
	WPGMZA.ModernDirectionsResultBox.prototype.clear = function()
	{
		$(this.element).find(".wpgmza-directions-results").html("");
		$(this.element).find("a.wpgmza-print").attr("href", "");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.open = function()
	{
		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		this.showPreloader();
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.showPreloader = function()
	{
		$(this.element).find(".wpgmza-directions-results").html("<img src='" + wpgmza_ajax_loader_gif.src + "'/>");
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onDirectionsChanged = function(event, response, status)
	{
		this.clear();
		
		switch(status)
		{
			case WPGMZA.DirectionsService.SUCCESS:
				// NB: The new directions renderers take care of this themselves
				break;
				
			case WPGMZA.DirectionsService.NOT_FOUND:
			case WPGMZA.DirectionsService.ZERO_RESULTS:

				var key = status.toLowerCase();
				var message = WPGMZA.localized_strings[key];
				
				$(this.element).find(".wpgmza-directions-results").html(
					'<i class="fa fa-times" aria-hidden="true"></i>' + message
				);
				
				break;
			
			default:
				
				var message = WPGMZA.localized_strings.unknown_error;
				
				$(this.element).find(".wpgmza-directions-results").html(
					'<i class="fa fa-times" aria-hidden="true"></i>' + message
				);
				
				break;
		}
	};
	
	WPGMZA.ModernDirectionsResultBox.prototype.onNativeMapsApp = function(event)
	{
		var url = this.directionsBox.getExternalURL();
		window.open(url, "_blank");
	}
	
	WPGMZA.ModernDirectionsResultBox.prototype.onPrint = function(event)
	{
		var content = $(this.element).find(".wpgmza-directions-results").html();
		var doc = document.implementation.createHTMLDocument();
		var html;
		
		sessionStorage.wpgmzaPrintDirectionsHTML = content;
	};
	
	
});

// js/v8/modern-marker-listing-marker-view.js
/**
 * @namespace WPGMZA
 * @module ModernMarkerListingMarkerView
 * @requires WPGMZA.PopoutPanel
 */
jQuery(function($) {
	
	/**
	 * This is the 2nd step of the modern look and feel marker listing
	 * @return Object
	 */
	WPGMZA.ModernMarkerListingMarkerView = function(map)
	{
		var self = this;
		
		this.map = map;
		this.map_id = map.id;
		
		WPGMZA.PopoutPanel.apply(this, arguments);
		
		var container = $("#wpgmza_map_" + map.id);
		
		this.element = $("<div class='wpgmza-popout-panel wpgmza-modern-marker-listing-marker-view'>\
			<div class='wpgmza-close-container'>\
				<span class='wpgmza-close'><i class='fa fa-arrow-left' aria-hidden='true'></i></span>\
				<span class='wpgmza-close'><i class='fa fa-times' aria-hidden='true'></i></span>\
			</div>\
			<div data-name='title'></div>\
			<div data-name='address'></div>\
			<div data-name='category'></div>\
			<img data-name='pic'/>\
			<div data-name='description'></div>\
			<div class='wpgmza-modern-marker-listing-buttons'>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-link-button'>\
					<i class='fa fa-link' aria-hidden='true'></i>\
					<div>\
						" + WPGMZA.localized_strings.link + "\
					</div>\
				</div>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-directions-button'>\
					<i class='fa fa-road' aria-hidden='true'></i>\
					<div>\
						" + WPGMZA.localized_strings.directions + "\
					</div>\
				</div>\
				<div class='wpgmza-modern-marker-listing-button wpgmza-zoom-button'>\
					<i class='fa fa-search-plus' aria-hidden='true'></i>\
					<div>\
						" + WPGMZA.localized_strings.zoom + "\
					</div>\
				</div>\
			</div>\
		</div>");
		
		map.on("init", function() {
			
			container.append(self.element);
			
		});
		
		map.on("click", function(event) {
			
			if(!(event.target instanceof WPGMZA.Marker))
				return;
			
			if(event.target == self.map.userLocationMarker || event.target == self.map.storeLocatorMarker)
				return;
			
			self.open(event.target.id);
			
		});
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
            $("#wpgmza_map_" + self.map_id + " .wpgmza-modern-store-locator").removeClass("wpgmza_sl_mv_offset");
		});
		
		$(this.element).find(".wpgmza-link-button").on("click", function(event) {
			self.onLink(event);
		});
		
		$(this.element).find(".wpgmza-directions-button").on("click", function(event) {
			self.onDirections(event);
		});
		
		$(this.element).find(".wpgmza-zoom-button").on("click", function(event) {
			self.onZoom(event);
		});
		
		$(container).append(this.element);
		
		// NB: Don't obscure the modern directions box
		if(container.children(".wpgmza-modern-directions-box").length)
			$(this.element).after(container.children(".wpgmza-modern-directions-box"));
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype = Object.create(WPGMZA.PopoutPanel.prototype);
	WPGMZA.ModernMarkerListingMarkerView.prototype.constructor = WPGMZA.ModernMarkerListingMarkerView;
	
	/*WPGMZA.ModernMarkerListingMarkerView.prototype.getMarkerAndData = function(marker_id)
	{
		var result = {
			marker: null,
			data: null
		};
		
		var mashup_ids = this.parent.mashup_ids;
		var map_id = this.map_id;
		var map_ids = [map_id];
		
		if(mashup_ids && mashup_ids.length)
			map_ids = mashup_ids.split(",");
		
		map_ids.forEach(function(map_id) {
			
			
			
		});
		
		return result;
	}*/
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.open = function(marker_id)
	{
		var self = this;
		var marker = this.map.getMarkerByID(marker_id);
		
		if(marker.disableInfoWindow)
			return;

		WPGMZA.PopoutPanel.prototype.open.apply(this, arguments);
		
		this.marker = marker;
		
		$(this.element).find("[data-name]").each(function(index, el) {
			
			var name = $(el).attr("data-name");
			var value;
			
			if(!marker[name])
				value = "";
			else
				value = marker[name];
			
			switch(name)
			{
				case "pic":
					$(el).attr("src", value);
					$(el).attr("alt", marker['title']);
					// $(el).css({visibility: (value == "" ? "hidden" : "visible")});
					
					if(marker['pic'].length)
						$(el).show();
					else
						$(el).hide();
					
					break;
				
				case "category":
					var ids = value.split(",");
					var names = [];
					
					for(var i = 0; i < ids.length; i++) {
						var id = ids[i];
						
						if(wpgmza_category_data[id])
							names.push(wpgmza_category_data[id].category_name);
					}
					
					$(el).html(names.join(", "));
					
					break;
				
				default:
					$(el).html(value);
					break;
			}
			
		});
		
		if(!marker["link"] || marker["link"].length == 0)
			$(this.element).find(".wpgmza-link-button").hide();
		else
			$(this.element).find(".wpgmza-link-button").show();

        $("#wpgmza_map_" + this.map_id + " .wpgmza-modern-store-locator").addClass("wpgmza_sl_mv_offset");
	 
		$(this.element).find("[data-custom-field-name]").remove();
		$(this.element).find(".wpgmza-modern-marker-listing-buttons").before(marker.custom_fields_html);
		
		$(this.element).find(".wpgmza-directions-button").attr("data-marker-id", marker_id);
		
		$(this.element).find(".wpgmza-close").on("click", function(event) {
			self.close();
		});
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onLink = function(event)
	{
		window.open(this.marker.link, "_blank");
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onDirections = function(event)
	{
		this.map.directionsBox.to = this.marker.address;
		this.map.directionsBox.element.show();
	}
	
	WPGMZA.ModernMarkerListingMarkerView.prototype.onZoom = function(event)
	{
		var map = MYMAP[this.map_id].map;
		
		map.setCenter(this.focusedMarker.getPosition());
		map.setZoom(14);
	}
	
});

// js/v8/pro-info-window.js
/**
 * @namespace WPGMZA
 * @module ProInfoWindow
 * @requires WPGMZA.InfoWindow
 */
jQuery(function($) {
	
	WPGMZA.ProInfoWindow = function(mapObject)
	{
		var self = this;
		
		WPGMZA.InfoWindow.call(this, mapObject);
		
		this.on("infowindowopen", function(event) {
			self.updateDistanceFromLocation();
		});
	}
	
	WPGMZA.ProInfoWindow.prototype = Object.create(WPGMZA.InfoWindow.prototype);
	WPGMZA.ProInfoWindow.prototype.constructor = WPGMZA.ProInfoWindow;
	
	WPGMZA.ProInfoWindow.STYLE_INHERIT			= "-1";
	WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE	= "0";
	WPGMZA.ProInfoWindow.STYLE_MODERN			= "1";
	WPGMZA.ProInfoWindow.STYLE_MODERN_PLUS		= "2";
	WPGMZA.ProInfoWindow.STYLE_MODERN_CIRCULAR	= "3";
	WPGMZA.ProInfoWindow.STYLE_TEMPLATE			= "template";
	
	WPGMZA.ProInfoWindow.OPEN_BY_CLICK			= 1;
	WPGMZA.ProInfoWindow.OPEN_BY_HOVER			= 2;
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "maxWidth", {
		
		get: function() {
			var width = WPGMZA.settings.wpgmza_settings_infowindow_width;
			
			if(!width || !(/^\d+$/.test(width)))
				return false;
			
			return width;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "imageWidth", {
		
		get: function() {
			var width = WPGMZA.settings.wpgmza_settings_image_width;
			
			if(!width || !(/^\d+$/.test(width)))
				return false;
				
			return width;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "imageHeight", {
		
		get: function() {
			var height = WPGMZA.settings.wpgmza_settings_image_height;
			
			if(!height || !(/^\d+$/.test(height)))
				return false;
				
			return height;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "enableImageResizing", {
		
		get: function() {
			return WPGMZA.settings.wpgmza_settings_image_resizing == "yes";
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "linkTarget", {
		
		get: function() {
			return WPGMZA.settings.wpgmza_settings_infowindow_links == "yes" ? "_BLANK" : "";
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "linkText", {
		
		get: function() {
			return window.wpgmaps_lang_more_details;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "directionsText", {
		
		get: function() {
			return window.wpgmaps_lang_get_dir;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "distanceUnits", {
		
		get: function() {
			return this.mapObject.map.settings.store_locator_distance == 1 ? WPGMZA.Distance.MILES : WPGMZA.Distance.KILOMETERS;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "showAddress", {
		
		get: function() {
			return WPGMZA.settings.wpgmza_settings_infowindow_address != "yes";
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "style", {
		
		get: function() {
			
			if(this.map && this.map.userLocationMarker == this)
				return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
			
			return this.getSelectedStyle();
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProInfoWindow.prototype, "isPanIntoViewAllowed", {
		
		"get": function()
		{
			return (this.style == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE);
		}
		
	});
	
	WPGMZA.ProInfoWindow.prototype.getSelectedStyle = function()
	{
		var globalTypeSetting = WPGMZA.settings.wpgmza_iw_type;
		var localTypeSetting = this.mapObject.map.settings.wpgmza_iw_type;
		var type = localTypeSetting;
		
		if(localTypeSetting == WPGMZA.ProInfoWindow.STYLE_INHERIT ||
			typeof localTypeSetting == "undefined")
		{
			type = globalTypeSetting;
			
			if(type == WPGMZA.ProInfoWindow.STYLE_INHERIT)
				return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		}
			
		if(!type)
			return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
			
		return String(type);
	}
	
	WPGMZA.ProInfoWindow.prototype.getImageElementFromURL = function(url)
	{
		var img = $("<img/>");
			
		img.addClass("wpgmza_infowindow_image");
		img.attr("src", url);
		img.css({"float": "right"});
		
		if(this.maxWidth)
			img.css({"max-width": this.maxWidth});
		
		if(this.enableImageResizing && this.imageWidth)
		{
			img.css({"width": this.imageWidth});
			img.css({"height": this.imageHeight});
		}
		
		if(!this.enableImageResizing)
			img.css({"margin": "5px"});
		
		return img;
	}
	
	WPGMZA.ProInfoWindow.prototype.updateDistanceFromLocation = function()
	{
		var marker = this.mapObject;
		
		if(!(marker instanceof WPGMZA.Marker))
		{
			console.warn("This function is only intended for use with markers and should not have been called in this manner");
			return;
		}
		
		var location = marker.map.showDistanceFromLocation;
		
		if(!location)
			return; // No location (no search performed, user location unavailable)
		
		var distanceInKM = WPGMZA.Distance.between(location, marker.getPosition());
		var distanceToDisplay = distanceInKM;
			
		if(this.distanceUnits == WPGMZA.Distance.MILES)
			distanceToDisplay /= WPGMZA.Distance.KILOMETERS_PER_MILE;
		
		var text = Math.round(distanceToDisplay, 2);
		var source = location.source == WPGMZA.ProMap.SHOW_DISTANCE_FROM_USER_LOCATION ? WPGMZA.localized_strings.from_your_location : WPGMZA.localized_strings.from_searched_location;
		
		$(this.element).find(".wpgmza-distance-from-location .wpgmza-amount").text(text);
		$(this.element).find(".wpgmza-distance-from-location .wpgmza-source").text(source);
	}
	
	WPGMZA.ProInfoWindow.prototype.legacyCreateDefaultInfoWindow = function(map)
	{
		var marker = this.mapObject;
		var map = marker.map;
		
		function empty(field)
		{
			return !(field && field.length && field.length > 0);
		}
		
		var container = $("<div class='wpgmza_markerbox scrollFix'></div>");
		
		if(this.maxWidth)
			container.css({"max-width": this.maxWidth});
		
		if(!empty(marker.gallery))
		{
			var gallery = new WPGMZA.MarkerGallery(marker, this);
			container.append(gallery.element);
		}
		else if(!empty(marker.pic))
		{
			// Fallback for legacy picture, which was before the marker gallery was implemented in v8. This SHOULD have been taken care of on the server by ProMarker, but this fallback is provided just in case. This can be deprecated in the future
			var img = this.getImageElementFromURL(marker.pic);
			container.append(img);
		}
		
		if(!empty(marker.title))
		{
			var p = $("<p class='wpgmza_infowindow_title'></p>");
			
			p.html(marker.title);
			
			container.append(p);
		}
		
		if(!empty(marker.address) && this.showAddress)
		{
			var p = $("<p class='wpgmza_infowindow_address'></p>");
			
			p.html(marker.address);
			
			container.append(p);
		}
		
		if(!empty(marker.desc) || !empty(marker.description))
		{
			var description = empty(marker.desc) ? marker.description : marker.desc;
			var div = $("<div class='wpgmza_infowindow_description'></div>");
			
			div.html(description);
			
			container.append(div);
		}
		
		if(map.settings.show_distance_from_location == 1)
		{
			var p = $("<p class='wpgmza-distance-from-location'><span class='wpgmza-amount'></span> <span class='wpgmza-units'></span> <span class='wpgmza-source'></span></p>");
			
			var units = this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.localized_strings.miles_away : WPGMZA.localized_strings.kilometers_away;
			
			p.find(".wpgmza-units").text(units);
			
			container.append(p);
		}
		
		if(!empty(marker.linkd) || !empty(marker.link))
		{
			var link = empty(marker.link) ? marker.linkd : marker.link;
			var p = $("<p class='wpgmza_infowindow_link'></p>");
			var a = $("<a class='wpgmza_infowindow_link'></a>");
			
			a.attr("href", WPGMZA.decodeEntities(link));
			a.attr("target", this.linkTarget);
			a.text(this.linkText);
			
			p.append(a);
			container.append(p);
		}
		
		if(map.directionsEnabled)
		{
			var p = $("<p></p>");
			var a = $("<a class='wpgmza_gd'></a>");
			
			a.attr("href", "javascript: ;");
			a.attr("id", map.id);
			
			a.attr("data-address", marker.address);
			a.attr("data-latlng", marker.getPosition().toString());
			a.attr("data-marker-id", marker.id);
			
			// Legacy fields
			a.attr("wpgm_addr_field", marker.address);
			a.attr("gps", marker.lat+","+marker.lng);
			
			a.text(this.directionsText);
			
			p.append(a);
			container.append(p);
		}
		
		if(marker.custom_fields_html)
			container.append(marker.custom_fields_html);
		
		this.setContent(container.html());
	}
	
	WPGMZA.ProInfoWindow.prototype.legacyCreateModernInfoWindow = function(map)
	{
		// Legacy code
		var mapid = map.id;
		var self = this;
		
		if($("#wpgmza_iw_holder_" + map.id).length == 0)
			$(document.body).append("<div id='wpgmza_iw_holder_" + map.id + "'></div>");
		else
			return;
		
		var legend = document.getElementById('wpgmza_iw_holder_' + mapid);
		if (legend !== null)
			$(legend).remove();

		wpgmza_iw_Div[mapid] = document.createElement('div');
		
		wpgmza_iw_Div[mapid].wpgmzaInfoWindow = this;
		
		wpgmza_iw_Div[mapid].id = 'wpgmza_iw_holder_' + mapid;
		wpgmza_iw_Div[mapid].style = 'display:block;';
		document.getElementsByTagName('body')[0].appendChild(wpgmza_iw_Div[mapid]);

		wpgmza_iw_Div_inner = document.createElement('div');
		wpgmza_iw_Div_inner.className = 'wpgmza_modern_infowindow_inner wpgmza_modern_infowindow_inner_' + mapid;
		wpgmza_iw_Div[mapid].appendChild(wpgmza_iw_Div_inner);

		wpgmza_iw_Div_close = document.createElement('div');
		wpgmza_iw_Div_close.className = 'wpgmza_modern_infowindow_close';
		wpgmza_iw_Div_close.setAttribute('mid', mapid);

		var t = document.createTextNode("x");
		wpgmza_iw_Div_close.appendChild(t);
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_Div_close);

		wpgmza_iw_Div_img = document.createElement('div');
		wpgmza_iw_Div_img.className = 'wpgmza_iw_image';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_Div_img);

		wpgmza_iw_img = document.createElement('img');
		wpgmza_iw_img.className = 'wpgmza_iw_marker_image';
		wpgmza_iw_img.src = '';
		wpgmza_iw_img.style = 'max-width:100%;';
		wpgmza_iw_Div_img.appendChild(wpgmza_iw_img);

		wpgmza_iw_img_div = document.createElement('div');
		wpgmza_iw_img_div.className = 'wpgmza_iw_title';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_img_div);

		wpgmza_iw_img_div_p = document.createElement('p');
		wpgmza_iw_img_div_p.className = 'wpgmza_iw_title_p';
		wpgmza_iw_img_div.appendChild(wpgmza_iw_img_div_p);

		wpgmza_iw_address_div = document.createElement('div');
		wpgmza_iw_address_div.className = 'wpgmza_iw_address';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_address_div);

		wpgmza_iw_address_p = document.createElement('p');
		wpgmza_iw_address_p.className = 'wpgmza_iw_address_p';
		wpgmza_iw_address_div.appendChild(wpgmza_iw_address_p);

		wpgmza_iw_description = document.createElement('div');
		wpgmza_iw_description.className = 'wpgmza_iw_description';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_description);

		wpgmza_iw_description_p = document.createElement('p');
		wpgmza_iw_description_p.className = 'wpgmza_iw_description_p';
		wpgmza_iw_description.appendChild(wpgmza_iw_description_p);
		
		if(map.settings.show_distance_from_location == 1)
		{
			var p = $("<p class='wpgmza-distance-from-location'><span class='wpgmza-amount'></span> <span class='wpgmza-units'></span> <span class='wpgmza-source'></span></p>");
			
			var units = this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.localized_strings.miles_away : WPGMZA.localized_strings.kilometers_away;
			
			p.find(".wpgmza-units").text(units);
			
			$(wpgmza_iw_Div_inner).append(p);
		}
		
		var ratingContainer = $("<div class='wpgmza-rating-container'></div>")[0];
		wpgmza_iw_Div_inner.appendChild(ratingContainer);

		// Custom fields
		$(wpgmza_iw_Div_inner).append("<div class='wpgmza_iw_custom_fields'/>");
		
		wpgmza_iw_buttons = document.createElement('div');
		wpgmza_iw_buttons.className = 'wpgmza_iw_buttons';
		wpgmza_iw_Div_inner.appendChild(wpgmza_iw_buttons);

		wpgmza_directions_button = document.createElement('a');
		wpgmza_directions_button.className = 'wpgmza_button wpgmza_left wpgmza_directions_button';
		wpgmza_directions_button.src = '#';
		
		var t = document.createTextNode(wpgmaps_lang_directions);
		wpgmza_directions_button.appendChild(t);
		wpgmza_iw_buttons.appendChild(wpgmza_directions_button);

		wpgmza_more_info_button = document.createElement('a');
		wpgmza_more_info_button.className = 'wpgmza_button wpgmza_right wpgmza_more_info_button';
		wpgmza_more_info_button.src = '#';
		
		var t = document.createTextNode(wpgmaps_lang_more_info);
		wpgmza_more_info_button.appendChild(t);
		wpgmza_iw_buttons.appendChild(wpgmza_more_info_button);

		var legend = document.getElementById('wpgmza_iw_holder_' + mapid);
		$(legend).css('display', 'block');
		$(legend).addClass('wpgmza_modern_infowindow');
		$(legend).addClass('wpgmza-shadow');
		
		if (WPGMZA.settings.engine == "google-maps")
			MYMAP[mapid].map.googleMap.controls[google.maps.ControlPosition.RIGHT_TOP].push(legend);
		else {
			var container = $(".wpgmza-ol-modern-infowindow-container[data-map-id='" + mapid + "']");
			if (!container.length) {
				container = $("<div class='wpgmza-ol-modern-infowindow-container' data-map-id='" + mapid + "'></div>");
				$(".wpgmza_map[data-map-id='" + mapid + "']").append(container);
			}

			container.append(legend);
		}

	}
	
	WPGMZA.ProInfoWindow.prototype.open = function(map, mapObject)
	{
		var self = this;
		var marker = mapObject;
		
		// Legacy support
		if(window.infoWindow)
			infoWindow[mapObject.map.id] = this;
		
		if(!WPGMZA.InfoWindow.prototype.open.call(this, map, mapObject))
			return false;	// Parent class has detected that the window shouldn't open
		
		if(this.mapObject == map.userLocationMarker)
			return true;	// Allow the default style window to open for user location markers
		
		if(map.settings.list_markers_by == WPGMZA.MarkerListing.STYLE_MODERN)
			return false;	// Don't show if modern style marker listing is selected
		
		if(WPGMZA.settings.wpgmza_settings_disable_infowindows)
			return false;	// Global setting "disable infowindows" is set
		
		// Legacy support
		if(this.style == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE || WPGMZA.currentPage == "map-edit")
		{
			this.legacyCreateDefaultInfoWindow();
			return true;	// Always show default style when on map edit page
		}
		
		this.legacyCreateModernInfoWindow(map);
		
		modern_iw_open[map.id] = true;

		var element = this.element = jQuery("#wpgmza_iw_holder_" + map.id);

		// Reset the contents first
		element.find(".wpgmza_iw_marker_image").attr("src",""); 
		element.find(".wpgmza_iw_title").html(""); 
		element.find(".wpgmza_iw_description").html(""); 
		element.find(".wpgmza_iw_address_p").html(""); 


		element.find(".wpgmza_more_info_button").attr("href","#"); 
		element.find(".wpgmza_more_info_button").attr("target",""); 
		element.find(".wpgmza_directions_button").attr("gps",""); 
		element.find(".wpgmza_directions_button").attr("href","#"); 
		element.find(".wpgmza_directions_button").attr("id",""); 
		element.find(".wpgmza_directions_button").attr("wpgm_addr_field",""); 

		
		
		if (marker.image === "" && marker.title === "") {  
			element.find(".wpgmza_iw_image").css("display","none"); 
		} else {
			element.find(".wpgmza_iw_image").css("display","block"); 
		}

		var container = $("#wpgmza_iw_holder_" + map.id + " .wpgmza_iw_image");
		container.html("");
		
		if(marker.gallery)
		{
			var gallery = new WPGMZA.MarkerGallery(marker, this);
			gallery.element.css({
				"float": "none"
			});
			container.append(gallery.element);
		}
		else if(marker.pic.length)
		{
			var image = $("<img class='wpgmza_infowindow_image'/>");
			image.attr("src", marker.pic);
			image.css({"display": "block"});
			container.append(image);
		}
		else
		{
			element.find(".wpgmza_iw_marker_image").css("display","none"); 
			element.find(".wpgmza_iw_title").attr("style","position: relative !important"); 
		}
		
		if (marker.title !== "") { element.find(".wpgmza_iw_title").html(marker.title); }

		var description = "";
		
		if(marker.desc)
			description = marker.desc;
		else if(marker.description)
			description = marker.description;

		if (description && description.length)
		{ 
			element.find(".wpgmza_iw_description").css("display","block"); 
			element.find(".wpgmza_iw_description").html(description); 
		}
		else
			element.find(".wpgmza_iw_description").css("display","none");

		// Custom fields
		var container = element.find(".wpgmza_iw_description");
		if(marker.custom_fields_html)
		{
			container.append(marker.custom_fields_html);
			container.css("display","block");
		}
		
		if (typeof wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] !== 'undefined' && wpgmaps_localize_global_settings['wpgmza_settings_infowindow_address'] === "yes") {
		} else {
			if (typeof marker.address !== "undefined" && marker.address !== "") { element.find(".wpgmza_iw_address_p").html(marker.address); }
		}
		

		if (typeof marker.link !== "undefined" && marker.link !== "") { 
			element.find(".wpgmza_more_info_button").show();
			element.find(".wpgmza_more_info_button").attr("href",marker.link);
			
			element.find(".wpgmza_more_info_button").attr("target",this.linkTarget); 
		} else {
			element.find(".wpgmza_more_info_button").hide();
		}
		if (map.directionsEnabled) { 
			element.find(".wpgmza_directions_button").show();
			element.find(".wpgmza_directions_button").attr("href","javascript:void(0);"); 
			element.find(".wpgmza_directions_button").attr("gps",marker.lat + "," + marker.lng); 
			element.find(".wpgmza_directions_button").attr("wpgm_addr_field",marker.address); 
			element.find(".wpgmza_directions_button").attr("id",map.id); 
			element.find(".wpgmza_directions_button").addClass("wpgmza_gd"); 

		} else {
			element.find(".wpgmza_directions_button").hide();
		}
		
		element.show();

		this.trigger("domready");
		this.trigger("infowindowopen");

		return true;
	}
	
	// TODO: This doesn't appear to do anything, nor does it call the parent method
	WPGMZA.ProInfoWindow.prototype.close = function()
	{
		$(this.mapObject.map.element).find(".wpgmza-pro-info-window-container").html();
	}
	
	// TODO: This should be taken care of already in core.js
	$(document).ready(function(event) {
		$(document.body).on("click", ".wpgmza-close-info-window", function(event) {
			$(event.target).closest(".wpgmza-info-window").remove();
		});
	});
	
});

// js/v8/google-maps/google-info-window.js
/**
 * @namespace WPGMZA
 * @module GoogleInfoWindow
 * @requires WPGMZA.InfoWindow
 * @pro-requires WPGMZA.ProInfoWindow
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.GoogleInfoWindow = function(mapObject)
	{
		Parent.call(this, mapObject);
		
		this.setMapObject(mapObject);
	}
	
	WPGMZA.GoogleInfoWindow.Z_INDEX		= 99;
	
	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProInfoWindow;
	else
		Parent = WPGMZA.InfoWindow;
	
	WPGMZA.GoogleInfoWindow.prototype = Object.create(Parent.prototype);
	WPGMZA.GoogleInfoWindow.prototype.constructor = WPGMZA.GoogleInfoWindow;
	
	WPGMZA.GoogleInfoWindow.prototype.setMapObject = function(mapObject)
	{
		if(mapObject instanceof WPGMZA.Marker)
			this.googleObject = mapObject.googleMarker;
		else if(mapObject instanceof WPGMZA.Polygon)
			this.googleObject = mapObject.googlePolygon;
		else if(mapObject instanceof WPGMZA.Polyline)
			this.googleObject = mapObject.googlePolyline;
	}
	
	WPGMZA.GoogleInfoWindow.prototype.createGoogleInfoWindow = function()
	{
		var self = this;
		
		if(this.googleInfoWindow)
			return;
		
		this.googleInfoWindow = new google.maps.InfoWindow();
		
		this.googleInfoWindow.setZIndex(WPGMZA.GoogleInfoWindow.Z_INDEX);
		
		google.maps.event.addListener(this.googleInfoWindow, "domready", function(event) {
			self.trigger("domready");
		});
		
		google.maps.event.addListener(this.googleInfoWindow, "closeclick", function(event) {
			
			if(self.state == WPGMZA.InfoWindow.STATE_CLOSED)
				return;
			
			self.state = WPGMZA.InfoWindow.STATE_CLOSED;
			self.trigger("infowindowclose");
			
		});
	}
	
	/**
	 * Opens the info window
	 * @return boolean FALSE if the info window should not & will not open, TRUE if it will
	 */
	WPGMZA.GoogleInfoWindow.prototype.open = function(map, mapObject)
	{
		var self = this;
		
		if(!Parent.prototype.open.call(this, map, mapObject))
			return false;
		
		// Set parent for events to bubble up to
		this.parent = map;
		
		this.createGoogleInfoWindow();
		this.setMapObject(mapObject);

		if(this.googleObject instanceof google.maps.Polygon)
		{

		}
		else{
			this.googleInfoWindow.open(
				this.mapObject.map.googleMap,
				this.googleObject
			);
		}
		

		
		var guid = WPGMZA.guid();
		var html = "<div id='" + guid + "'>" + this.content + "</div>";

		this.googleInfoWindow.setContent(html);
		
		var intervalID;
		intervalID = setInterval(function(event) {
			
			div = $("#" + guid);
			
			if(div.length)
			{
				clearInterval(intervalID);
				
				div[0].wpgmzaMapObject = self.mapObject;
				div.addClass("wpgmza-infowindow");
				
				self.element = div[0];
				self.trigger("infowindowopen");
			}
			
		}, 50);
		
		return true;
	}
	
	WPGMZA.GoogleInfoWindow.prototype.close = function()
	{
		if(!this.googleInfoWindow)
			return;
		
		WPGMZA.InfoWindow.prototype.close.call(this);
		
		this.googleInfoWindow.close();
	}
	
	WPGMZA.GoogleInfoWindow.prototype.setContent = function(html)
	{
		Parent.prototype.setContent.call(this, html);
		
		this.content = html;
		
		this.createGoogleInfoWindow();
		
		this.googleInfoWindow.setContent(html);
	}
	
	WPGMZA.GoogleInfoWindow.prototype.setOptions = function(options)
	{
		Parent.prototype.setOptions.call(this, options);
		
		this.createGoogleInfoWindow();
		
		this.googleInfoWindow.setOptions(options);
	}
	
});

// js/v8/open-layers/ol-info-window.js
/**
 * @namespace WPGMZA
 * @module OLInfoWindow
 * @requires WPGMZA.InfoWindow
 * @pro-requires WPGMZA.ProInfoWindow
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.OLInfoWindow = function(mapObject)
	{
		var self = this;
		
		Parent.call(this, mapObject);
		
		this.element = $("<div class='wpgmza-infowindow ol-info-window-container ol-info-window-plain'></div>")[0];
			
		$(this.element).on("click", ".ol-info-window-close", function(event) {
			self.close();
		});
	}
	
	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProInfoWindow;
	else
		Parent = WPGMZA.InfoWindow;
	
	WPGMZA.OLInfoWindow.prototype = Object.create(Parent.prototype);
	WPGMZA.OLInfoWindow.prototype.constructor = WPGMZA.OLInfoWindow;
	
	Object.defineProperty(WPGMZA.OLInfoWindow.prototype, "isPanIntoViewAllowed", {
		
		"get": function()
		{
			return true;
		}
		
	});
	
	/**
	 * Opens the info window
	 * TODO: This should take a mapObject, not an event
	 * @return boolean FALSE if the info window should not & will not open, TRUE if it will
	 */
	WPGMZA.OLInfoWindow.prototype.open = function(map, mapObject)
	{
		var self = this;
		var latLng = mapObject.getPosition();
		
		if(!Parent.prototype.open.call(this, map, mapObject))
			return false;
		
		// Set parent for events to bubble up
		this.parent = map;
		
		if(this.overlay)
			this.mapObject.map.olMap.removeOverlay(this.overlay);
			
		this.overlay = new ol.Overlay({
			element: this.element,
			stopEvent: false
		});
		
		this.overlay.setPosition(ol.proj.fromLonLat([
			latLng.lng,
			latLng.lat
		]));
		self.mapObject.map.olMap.addOverlay(this.overlay);
		
		$(this.element).show();
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			WPGMZA.getImageDimensions(mapObject.getIcon(), function(size) {
				
				$(self.element).css({left: Math.round(size.width / 2) + "px"});
				
			});
		}
		
		this.trigger("infowindowopen");
		this.trigger("domready");
	}
	
	WPGMZA.OLInfoWindow.prototype.close = function(event)
	{
		// TODO: Why? This shouldn't have to be here. Removing the overlay should hide the element (it doesn't)
		$(this.element).hide();
		
		if(!this.overlay)
			return;
		
		WPGMZA.InfoWindow.prototype.close.call(this);
		
		this.trigger("infowindowclose");
		
		this.mapObject.map.olMap.removeOverlay(this.overlay);
		this.overlay = null;
	}
	
	WPGMZA.OLInfoWindow.prototype.setContent = function(html)
	{
		$(this.element).html("<i class='fa fa-times ol-info-window-close' aria-hidden='true'></i>" + html);
	}
	
	WPGMZA.OLInfoWindow.prototype.setOptions = function(options)
	{
		if(options.maxWidth)
		{
			$(this.element).css({"max-width": options.maxWidth + "px"});
		}
	}
	
	WPGMZA.OLInfoWindow.prototype.onOpen = function()
	{
		var self = this;
		var imgs = $(this.element).find("img");
		var numImages = imgs.length;
		var numImagesLoaded = 0;
		
		WPGMZA.InfoWindow.prototype.onOpen.apply(this, arguments);
		
		if(this.isPanIntoViewAllowed)
		{
			function inside(el, viewport)
			{
				var a = $(el)[0].getBoundingClientRect();
				var b = $(viewport)[0].getBoundingClientRect();
				
				return a.left >= b.left && a.left <= b.right &&
						a.right <= b.right && a.right >= b.left &&
						a.top >= b.top && a.top <= b.bottom &&
						a.bottom <= b.bottom && a.bottom >= b.top;
			}
			
			function panIntoView()
			{
				var height	= $(self.element).height();
				var offset	= -height * 0.45;
				
				self.mapObject.map.animateNudge(0, offset, self.mapObject.getPosition());
			}
			
			imgs.each(function(index, el) {
				el.onload = function() {
					if(++numImagesLoaded == numImages && !inside(self.element, self.mapObject.map.element))
						panIntoView();
				}
			});
			
			if(numImages == 0 && !inside(self.element, self.mapObject.map.element))
				panIntoView();
		}
	}
	
});

// js/v8/pro-latlng.js
/**
 * @namespace WPGMZA
 * @module ProLatLng
 * @requires WPGMZA.LatLng
 */
jQuery(function($) {
	
	WPGMZA.LatLng.fromJpeg = function(src, callback)
	{
		var img = new Image();
		
		img.onload = function() {
			
			EXIF.getData(img, function() {
				
				var aLat = EXIF.getTag(img, "GPSLatitude");
				var aLng = EXIF.getTag(img, "GPSLongitude");
				
				if(!(aLat && aLng))
				{
					callback(null);
					return;
				}
				
				var latRef = EXIF.getTag(img, "GPSLatitudeRef") || "N";
				var lngRef = EXIF.getTag(img, "GPSLongitudeRef") || "W";
				
				var fLat = (aLat[0] + aLat[1] / 60 + aLat[2] / 3600) * (latRef == "N" ? 1 : -1);
				var fLng = (aLng[0] + aLng[1] / 60 + aLng[2] / 3600) * (lngRef == "W" ? -1 : 1);
				
				callback(new WPGMZA.LatLng({
					lat: fLat,
					lng: fLng
				}));
				
			});
			
		}
		
		img.src = src;
	}
	
	// When reverse geocoding JPEG EXIF GPS coordinates to an address, this is the threshold for accuracy
	WPGMZA.LatLng.EXIF_ADDRESS_GEOCODE_KM_THRESHOLD = 0.5;
	
	$(document.body).on("click", ".wpgmza-get-location-from-picture[data-source][data-destination]", function(event) {
		
		var style, m, url;
		var source = $(this).attr("data-source");
		var dest = $(this).attr("data-destination");
		
		var lat = $(this).attr("data-destination-lat");
		var lng = $(this).attr("data-destination-lng");
		
		if(!$(source).length)
			throw new Error("Source element not found");
		
		if(!$(dest).length)
			throw new Error("Destination element not found");
		
		if($(source).is("img"))
		{
			url = $(source).attr("src");
		}
		else
		{
			style = $(source).css("background-image");
			
			if(!(m = style.match(/url\(["'](.+)["'"]\)/)))
				throw new Error("No background image found");
			
			url = m[1];
		}
		
		if(!url || url.length == 0)
			alert(WPGMZA.localised_strings.no_picture_found);
		
		WPGMZA.LatLng.fromJpeg(url, function(jpegLatLng) {
			
			if(!jpegLatLng)
			{
				// No coordinates found, inform the user and bail out
				alert(WPGMZA.localized_strings.no_gps_coordinates);
				return;
			}
			
			// Fill the destination with the coordinates
			$(dest).val(jpegLatLng.toString());
			
			// Fill the lat and lng fields if applicable
			if(lat && lng)
			{
				$(lat).val(jpegLatLng.lat);
				$(lng).val(jpegLatLng.lng);
			}
			
			if(WPGMZA.settings.useRawJpegCoordinates)
				return;
			
			// Attempt to get the address from these coordinates
			var geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.getAddressFromLatLng({
				latLng: jpegLatLng
			}, function(results, status) {
				
				// Failed to get the address, coordinates will be used
				if(status != WPGMZA.Geocoder.SUCCESS)
					return;
				
				// We have an address
				var address = results[0];
				
				// Let's geocode this address and see how close that address is to the raw GPS coordinates
				geocoder.getLatLngFromAddress({
					address: address
				}, function(results, status) {
					
					// Failed to geocode the found address (this should not happen)
					if(status != WPGMZA.Geocoder.SUCCESS)
						return;
					
					// Find the distance in KM between the raw GPS point and the geocoded address
					var addressLatLng = new WPGMZA.LatLng(results[0].latLng);
					var kmOffset = WPGMZA.Distance.between(addressLatLng, jpegLatLng);
					
					// If it's below the threshold, use the address instead of raw coordinates
					if(kmOffset <= WPGMZA.LatLng.EXIF_ADDRESS_GEOCODE_KM_THRESHOLD)
						$(dest).val(address);
					
				});
				
			});
			
		});
		
	});
	
});

// js/v8/pro-map-edit-page.js
/**
 * @namespace WPGMZA
 * @module ProMapEditPage
 * @pro-requires WPGMZA.MapEditPage
 */
jQuery(function($) {
	
	if(WPGMZA.currentPage != "map-edit")
		return;
	
	WPGMZA.ProMapEditPage = function()
	{
		var self = this;
		
		WPGMZA.MapEditPage.apply(this, arguments);
		
		this.markerPanel = new WPGMZA.ProMarkerPanel();
		this.directionsOriginIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_origin_icon_picker_container > .wpgmza-marker-icon-picker") );
		this.directionsDestinationIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_destination_icon_picker_container > .wpgmza-marker-icon-picker") );
		
		this.advancedSettingsMarkerIconPicker = new WPGMZA.MarkerIconPicker( $("#advanced-settings-marker-icon-picker-container > .wpgmza-marker-icon-picker") );

		$("input[name='store_locator_search_area']").on("input", function(event) {
			self.onStoreLocatorSearchAreaChanged(event);
		});
		self.onStoreLocatorSearchAreaChanged();

		// NB: Workaround for bad DOM
		$("#open-route-service-key-notice").wrapInner("<div class='notice notice-error'><p></p></div>");
	
		var store_locator_search_area = $("input[name='store_locator_search_area']");	
		$(store_locator_search_area).on('change', function() {
			self.onStoreLocatorSearchAreaChanged();
		});

		self.onDirectionsBoxStyle();
		$('body').on('change', '[name="directions_box_style"]', function(){
			self.onDirectionsBoxStyle();
		});
	
	}
	
	WPGMZA.extend(WPGMZA.ProMapEditPage, WPGMZA.MapEditPage);
	
	WPGMZA.ProMapEditPage.prototype.onStoreLocatorSearchAreaChanged = function(event)
	{
		var value = $("input[name='store_locator_search_area']:checked").val();
		
		$("[data-search-area='" + value + "']").show();
		$("[data-search-area][data-search-area!='" + value + "']").hide();

		if(value == 'auto'){	
			$('.wpgmza-store-locator-radial-setting').hide();
			$('#wpgmza_store_locator_bounce_conditional').hide();	
		}
		else{
			$('.wpgmza-store-locator-radial-setting').show();
			$('#wpgmza_store_locator_bounce_conditional').show();
		}
	}

	WPGMZA.ProMapEditPage.prototype.onDirectionsBoxStyle = function()
	{
		var style = $('[name="directions_box_style"]:checked').val();

		if(style == "modern" || WPGMZA.settings.user_interface_style == "modern") 
		{
			$('#wpgmza-directions-box-open-by-default, #wpgmza-directions-box-width').hide();
		}
		else if(style == "default") {
			$('#wpgmza-directions-box-open-by-default, #wpgmza-directions-box-width').show();
		}
	}
	
});

// js/v8/pro-map-settings-page.js
/**
 * @namespace WPGMZA
 * @module ProMapSettingsPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.ProMapSettingsPage = function()
	{
		WPGMZA.MapSettingsPage.call(this);
	}
	
	WPGMZA.extend(WPGMZA.ProMapSettingsPage, WPGMZA.MapSettingsPage);
	
	WPGMZA.MapSettingsPage.createInstance = function()
	{
		return new WPGMZA.ProMapSettingsPage();
	}
	
});

// js/v8/pro-map.js
/**
 * @namespace WPGMZA
 * @module ProMap
 * @requires WPGMZA.Map
 */
jQuery(function($) {
	
	/**
	 * Base class for maps. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.ProMap
	 * @constructor WPGMZA.ProMap
	 * @memberof WPGMZA
	 * @param {HTMLElement} element to contain map
	 * @param {object} [options] Options to apply to this map
	 * @augments WPGMZA.Map
	 */
	WPGMZA.ProMap = function(element, options)
	{
		var self = this;
		
		this._markersPlaced = false;
		
		// Some objects created in the parent constructor use the category data, so load that first
		this.element = element;
		
		// Call the parent constructor
		WPGMZA.Map.call(this, element, options);
		
		// Default marker icon
		this.defaultMarkerIcon = null;
		
		if(this.settings.upload_default_marker)
			this.defaultMarkerIcon = WPGMZA.MarkerIcon.createInstance(this.settings.upload_default_marker)
		
		this.heatmaps = [];
		
		// Showing distance from this position
		this.showDistanceFromLocation = null;
		
		// Custom field filtering
		this.initCustomFieldFilterController();
		
		// User location
		this.initUserLocationMarker();
		
		// Update on filtering
		this.on("filteringcomplete", function() {
			//call onFilteringComplete function
			self.onFilteringComplete();

		});
		
		// Init
		this.on("init", function(event) {
			self.onInit(event);
		});
		
		// Place markers
		this._onMarkersPlaced = function(event) {
			self.onMarkersPlaced(event);
		}
		this.on("markersplaced", this._onMarkersPlaced);
	}
	
	WPGMZA.ProMap.prototype = Object.create(WPGMZA.Map.prototype);
	WPGMZA.ProMap.prototype.constructor = WPGMZA.ProMap;
	
	WPGMZA.ProMap.SHOW_DISTANCE_FROM_USER_LOCATION		= "user";
	WPGMZA.ProMap.SHOW_DISTANCE_FROM_SEARCHED_ADDRESS	= "searched";
	
	/**
	 * The mashup map ID's, or an empty array if there are none selected
	 *  
	 * @name WPGMZA.ProMap#mashupIDs
	 * @type Array
	 */
	Object.defineProperty(WPGMZA.ProMap.prototype, "mashupIDs", {
		
		get: function() {
			
			var result = [];
			var attr = $(this.element).attr("data-mashup-ids");
			
			if(attr && attr.length)
				result = result = attr.split(",");
			
			return result;
			
		}
		
	});
	
	/**
	 * Whether directions are enabled or not
	 *  
	 * @name WPGMZA.ProMap#directionsEnabled
	 * @type Boolean
	 */
	Object.defineProperty(WPGMZA.ProMap.prototype, "directionsEnabled", {
		
		get: function() {
			return this.settings.directions_enabled == 1;
		}
		
	});
	
	/**
	 * Whether or not the markers have been placed yet
	 *  
	 * @name WPGMZA.ProMap#markersPlaced
	 * @type Boolean
	 * @readonly
	 */
	Object.defineProperty(WPGMZA.ProMap.prototype, "markersPlaced", {
		
		get: function() {
			return this._markersPlaced;
		},
		
		set: function(value) {
			throw new Error("Value is read only");
		}
		
	});
	
	/**
	 * Called by the engine specific map classes when the map has fully initialised
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.Event} The event
	 * @listens module:WPGMZA.Map~init
	 */
	WPGMZA.ProMap.prototype.onInit = function(event)
	{
		var self = this;
		
		this.initPreloader();
		this.initDirectionsBox();
		
		if(!("autoFetchMarkers" in this.settings) || (this.settings.autoFetchMarkers !== false))
			this.fetchMarkers();

		if(this.shortcodeAttributes.lat && this.shortcodeAttributes.lng)
		{
			var latLng = new WPGMZA.LatLng({
				lat: this.shortcodeAttributes.lat,
				lng: this.shortcodeAttributes.lng
			});
			
			this.setCenter(latLng);
		}
		else if(this.shortcodeAttributes.address)
		{
			var geocoder = WPGMZA.Geocoder.createInstance(); // Will return a GoogleGeocoder or OLGeocoder depending on engine selection
			
			geocoder.geocode({address: this.shortcodeAttributes.address}, function(results, status) {
				
				if(status != WPGMZA.Geocoder.SUCCESS)
				{
					console.warn("Shortcode attribute address could not be geocoded");
					return;
				}
				
				self.setCenter(results[0].latLng); 	// I think - not sure about the format off the top of my head. May need to log results
				
			});
		}
		
		var zoom;
		if(zoom = WPGMZA.getQueryParamValue("mzoom"))
			this.setZoom(zoom);
		
		if(WPGMZA.getCurrentPage() != WPGMZA.PAGE_MAP_EDIT && this.settings.automatically_pan_to_users_location == "1"){

			WPGMZA.getCurrentPosition(function(result) {
						
				self.setCenter(
					new WPGMZA.LatLng({
						lat: result.coords.latitude,
						lng: result.coords.longitude
					})
				);
				
				if(self.settings.override_users_location_zoom_level == "1")
					self.setZoom(self.settings.override_users_location_zoom_levels);
					
			});

			
		
		}

	}
	
	/**
	 * Called when all the markers have been loaded and placed
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.Event} The event
	 * @listens module:WPGMZA.ProMap~markersplaced
	 */
	WPGMZA.ProMap.prototype.onMarkersPlaced = function(event)
	{
		var self = this;
		
		// NB: Marker listing. We delay this til here because the marker gallery will need to fetch marker data from here
		// A good alternative to this would be to transmit the marker data in a data- attribute
		
		var jumpToNearestMarker = (WPGMZA.is_admin == 0 && self.settings.jump_to_nearest_marker_on_initialization == 1);
		
		if(this.settings.order_markers_by == WPGMZA.MarkerListing.ORDER_BY_DISTANCE || this.settings.show_distance_from_location == 1 || jumpToNearestMarker)
		{
			WPGMZA.getCurrentPosition(function(result) {
				
				var location = new WPGMZA.LatLng({
					lat: result.coords.latitude,
					lng: result.coords.longitude
				});
				
				self.userLocation = location;
				self.userLocation.source = WPGMZA.ProMap.SHOW_DISTANCE_FROM_USER_LOCATION;
				
				self.showDistanceFromLocation = location;

				self.updateInfoWindowDistances();
				
				if(self.markerListing)
					if(self.markersPlaced)
						self.markerListing.reload();
					else
					{					
						self.on("markersplaced", function(event) {
							self.markerListing.reload();
						});
					}
				
				// Checks if jump_to_nearest_marker_on_initialization setting is enabled, only on the front end though
				if(jumpToNearestMarker)
					self.panToNearestMarker(location);
				
			}, function(error) {
				
				if(self.markerListing)
					self.markerListing.reload();
				
			});
	}
		
		self.initMarkerListing();

		// Clustering
		if(window.wpgm_g_e && wpgm_g_e == 1 && this.settings.mass_marker_support == 1)
		{
			this.markerClusterer.addMarkers(this.markers);
			
			// Legacy support
			if(typeof window.markerClusterer == "array")
				window.markerClusterer[this.id] = clusterer;
		}

		//Check if Fit map bounds to markers setting is enable
		if(this.settings.fit_maps_bounds_to_markers == '1')
		{
			self.fitMapBoundsToMarkers();
		}
	}

	/**
	 * Pans to the nearest marker to the specified latlng, or the center of the map if no latlng is specified
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.LatLng} [latlng] Pan to the nearest marker to this latlng, optional. The center is used if no value is specified.
	 */
	WPGMZA.ProMap.prototype.panToNearestMarker = function(latlng)
	{
		var closestMarker;
		var distance = Infinity;
		
		if(!latlng)
			latlng = this.getCenter();

    	// Loop through each marker on this map
    	for (var i = 0; i < this.markers.length; i++) {

        	// Calculate the distance from the latlng passed in to marker[i]
        	var distanceToMarker = WPGMZA.Distance.between(latlng, this.markers[i].getPosition());
        
        	// Is this closer than our current recorded nearest marker?
        	if(distanceToMarker < distance)
        	{
            	// Yes it is, store marker[i] as the closest marker
            	closestMarker = this.markers[i];
            
            	// Store the distance as the new closest difference
            	distance = distanceToMarker;
        	}
		}

    	// Now that the loop has completed, marker will hold the nearest marker to latlng (or null if there are no markers on this map)
    	if(!closestMarker)
        	return;
    
   		 // Pan to it
    	this.panTo(closestMarker.getPosition(this.setZoom(7)));
	}

	/**
	 * Fits the map boundaries to any unfiltered (visible) markers in the specified array, or all markers on the map if no markers are specified.
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @param {WPGMZA.Marker[]} [markers] Markers to fit the map boundaries to. If no markers are specified, all markers are used.
	 */
	WPGMZA.ProMap.prototype.fitBoundsToMarkers = function(markers)
	{
		var bounds = new WPGMZA.LatLngBounds();
		
		if(!markers)
			markers = this.markers;
		
		// Loop through the markers
		for (var i = 0; i < markers.length; i++)
		{
			if(!(markers[i] instanceof WPGMZA.Marker))
				throw new Error("Invalid input, not a WPGMZA.Marker");
			
			if (!markers[i].isFiltered)
			{
				// Set map bounds to these markers
				bounds.extend(markers[i]);
			}
		}
		
		this.fitBounds(bounds);
	}
	
	// NB: Legacy support
	WPGMZA.ProMap.prototype.fitMapBoundsToMarkers = WPGMZA.ProMap.prototype.fitBoundsToMarkers;

	/**
	 * Resets the map latitude, longitude and zoom to their starting values in the map settings.
	 * @method
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.resetBounds = function()
	{
		var latlng = new WPGMZA.LatLng(this.settings.map_start_lat, this.settings.map_start_lng);
		this.panTo(latlng);
		this.setZoom(this.settings.map_start_zoom);
	}

	/**
	 * Callback for when the marker filter has completed
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @listens module:WPGMZA.Map~onFilteringComplete
	 */
	WPGMZA.ProMap.prototype.onFilteringComplete = function()
	{
		// Check if Fit map bounds to markers after filtering setting is enabled
		if(this.settings.fit_maps_bounds_to_markers_after_filtering == '1')
		{
			var self = this;
			var areMarkersVisible = false;
			
			// Loop through the markers
			for (var i = 0; i < this.markers.length; i++) 
			{
				if(!this.markers[i].isFiltered){
					// Total markers filtered
					areMarkersVisible = true;
					break;
				}
			}		
			
			if(areMarkersVisible)
			{
				// If total markers filtered is more than 0, call fitMapBoundsToMarkers function
				self.fitBoundsToMarkers();
			}
		}
	}
	
	/**
	 * Initialises the preloader
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @protected
	 */
	WPGMZA.ProMap.prototype.initPreloader = function()
	{
		this.preloader = $("<div class='wpgmza-preloader'><div></div><div></div><div></div><div></div></div>");
		
		$(this.preloader).hide();
		
		$(this.element).append(this.preloader);
	}
	
	/**
	 * Shows or hides the maps preloader
	 * @method
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.showPreloader = function(show)
	{
		if(show)
			$(this.preloader).show();
		else
			$(this.preloader).hide();
	}
	
	/**
	 * Initialises the marker listing
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initMarkerListing = function()
	{
		if(WPGMZA.is_admin == "1")
			return;	// NB: No marker listings on the back end
		
		/*if(this.markerListing)
		{
			console.warn("Marker listing already initialized. No action will be taken.");
			return;
		}*/
		
		var markerListingElement = $("[data-wpgmza-marker-listing][id$='_" + this.id + "']");
		
		// NB: This is commented out to allow the category filter to still function with "No marker listing". This will be rectified in the future with a unified filtering interface
		//if(markerListingElement.length)
		this.markerListing = WPGMZA.MarkerListing.createInstance(this, markerListingElement[0]);
	
		this.off("markersplaced", this._onMarkersPlaced);
		delete this._onMarkersPlaced;
	}
	
	/**
	 * Initialises the custom field filter controller
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initCustomFieldFilterController = function()
	{
		this.customFieldFilterController = WPGMZA.CustomFieldFilterController.createInstance(this.id);
	}
	
	/**
	 * Initialises the user location marker, if the setting is enabled
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initUserLocationMarker = function()
	{
		var self = this;
		
		if(this.settings.show_user_location != 1)
			return;
		
		var icon = this.settings.upload_default_ul_marker;
		var options = {
			id: WPGMZA.guid(),
			animation: WPGMZA.Marker.ANIMATION_DROP
		};
		
		if(icon && icon.length)
			options.icon = icon;
		
		var marker = WPGMZA.Marker.createInstance(options);
		
		marker.isFilterable = false;
		marker.setOptions({
			zIndex: 999999
		});
		
		WPGMZA.watchPosition(function(position) {
			
			marker.setPosition({
				lat: position.coords.latitude,
				lng: position.coords.longitude
			});
			
			if(!marker.map)
				self.addMarker(marker);
			
			if(!self.userLocationMarker)
			{
				self.userLocationMarker = marker;
				self.trigger("userlocationmarkerplaced");
			}

			var directionsFromField = jQuery('body').find('.wpgmza-directions-from');
			directionsFromField.val(position.coords.latitude + ", " + position.coords.longitude);
			
			// WPGMZA.Geocoder.createInstance().geocode({
			//     latLng: {
			//     	lat: position.coords.latitude,
			//     	lng: position.coords.longitude
			//     }
			// }, function(results){
			// 	if (results.length > 0) {
			// 		directionsFromField.val(results[0]);
			// 	}
			//     console.log(results);
			// });
			
		});
	}
	
	/**
	 * Initialises the directions box on the front end, if the setting is enabled
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.initDirectionsBox = function()
	{
		if(WPGMZA.is_admin == 1)
			return;
		
		if(!this.directionsEnabled)
			return;
		
		this.directionsBox = WPGMZA.DirectionsBox.createInstance(this);
	}
	
	/**
	 * Get's arrays of all features for each of the feature types on the map
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 * @deprecated Will be removed in Pro 8.1.0
	 */
	WPGMZA.ProMap.prototype.getMapObjectArrays = function()
	{
		var arrays = WPGMZA.Map.prototype.getMapObjectArrays.call(this);
		
		arrays.heatmaps = this.heatmaps;
		
		return arrays;
	}
	
	/**
	 * Adds the specified heatmap to the map
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.addHeatmap = function(heatmap)
	{
		if(!(heatmap instanceof WPGMZA.Heatmap))
			throw new Error("Argument must be an instance of WPGMZA.Heatmap");
		
		heatmap.map = this;
		
		this.heatmaps.push(heatmap);
		this.dispatchEvent({type: "heatmapadded", heatmap: heatmap});
	}
	
	/**
	 * Gets a heatmap by ID
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.getHeatmapByID = function(id)
	{
		for(var i = 0; i < this.heatmaps.length; i++)
			if(this.heatmaps[i].id == id)
				return this.heatmaps[i];
			
		return null;
	}
	
	/**
	 * Removes the specified heatmap and fires an event
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.removeHeatmap = function(heatmap)
	{
		if(!(heatmap instanceof WPGMZA.Heatmap))
			throw new Error("Argument must be an instance of WPGMZA.Heatmap");
		
		if(heatmap.map != this)
			throw new Error("Wrong map error");
		
		heatmap.map = null;
		
		// TODO: This shoud not be here in the generic class
		heatmap.googleHeatmap.setMap(null);
		
		this.heatmaps.splice(this.heatmaps.indexOf(heatmap), 1);
		this.dispatchEvent({type: "heatmapremoved", heatmap: heatmap});
	}
	
	/**
	 * Removes the specified heatmap and fires an event
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return void
	 */
	WPGMZA.ProMap.prototype.removeHeatmapByID = function(id)
	{
		var heatmap = this.getHeatmapByID(id);
		
		if(!heatmap)
			return;
		
		this.removeHeatmap(heatmap);
	}
	
	/**
	 * Get's the selected infowindow style for this map, or the global style if "inherit" is selected.
	 * 
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @return {mixed} The InfoWindow style, see WPGMZA.ProInfoWindow for possible values
	 */
	WPGMZA.ProMap.prototype.getInfoWindowStyle = function()
	{
		if(!this.settings.other_settings)
			return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		
		var local = this.settings.other_settings.wpgmza_iw_type;
		var global = WPGMZA.settings.wpgmza_iw_type;
		
		if(local == "-1" && global == "-1")
			return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		
		if(local == "-1")
			return global;
		
		if(local)
			return local;
		
		return WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
	}
	
	/**
	 * Fetches the markers, either by REST API or XML file depending on the selected setting.
	 * 
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @protected
	 */
	WPGMZA.ProMap.prototype.fetchMarkers = function()
	{
		var self = this;
		
		if(WPGMZA.settings.wpgmza_settings_marker_pull != WPGMZA.MARKER_PULL_XML || WPGMZA.is_admin == "1")
		{
			var data, request;
			var filter = {
				map_id: this.id,
				mashup_ids: this.mashupIDs
			};
			
			if(WPGMZA.is_admin == "1")
			{
				filter.includeUnapproved = true;
				filter.excludeIntegrated = true;
			}
			
			if(this.shortcodeAttributes.acf_post_id)
			{
				if($.isNumeric(this.shortcodeAttributes.acf_post_id))
					filter.acf_post_id = this.shortcodeAttributes.acf_post_id;
				else if(this.shortcodeAttributes.acf_post_id == "this")
					filter.acf_post_id = WPGMZA.postID;
			}
			
			data = {
				filter: JSON.stringify(filter)
			};
			
			request = {
				useCompressedPathVariable: true,
				
				data: data,
				
				success: function(data, status, xhr) {
					self.onMarkersFetched(data);
				}
			};
			
			if(WPGMZA.is_admin == 1)
			{
				data.skip_cache = 1;
				request.useCompressedPathVariable = false;
			}
			
			this.showPreloader(true);
			WPGMZA.restAPI.call("/markers/", request);
		}
		else
		{
			var urls = [
				WPGMZA.markerXMLPathURL + this.id + "markers.xml"
			];
			
			if(this.mashupIDs)
				this.mashupIDs.forEach(function(id) {
					urls.push(WPGMZA.markerXMLPathURL + id + "markers.xml")
				});
			
			var unique = urls.filter(function(item, index) {
				return urls.indexOf(item) == index;
			});
			
			urls = unique;
			
			if(window.Worker && window.Blob && window.URL && WPGMZA.settings.enable_asynchronous_xml_parsing)
			{
				var source 	= WPGMZA.loadXMLAsWebWorker.toString().replace(/function\(\)\s*{([\s\S]+)}/, "$1");
				var blob 	= new Blob([source], {type: "text/javascript"});
				var worker	= new Worker(URL.createObjectURL(blob));
				
				worker.onmessage = function(event) {
					self.onMarkersFetched(event.data);
				};
				
				worker.postMessage({
					command: "load",
					protocol: window.location.protocol,
					urls: urls
				});
			}
			else
			{
				var filesLoaded = 0;
				var converter = new WPGMZA.XMLCacheConverter();
				var converted = [];
				
				for(var i = 0; i < urls.length; i++)
				{
					$.ajax(urls[i], {
						success: function(response, status, xhr) {
							converted = converted.concat( converter.convert(response) );
							
							if(++filesLoaded == urls.length)
								self.onMarkersFetched(converted);
						}
					});
				}
			}
		}
	}
	
	/**
	 * Called once fetchMarkers has finished fetching all the markers. This function will populate the map with the fetched data, then fire events.
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @protected
	 * @fires module:WPGMZA.ProMap~markersplaced
	 * @fires module:WPGMZA.ProMap~filteringcomplete
	 */
	WPGMZA.ProMap.prototype.onMarkersFetched = function(data)
	{
		var self = this;
		var startFiltered = (this.shortcodeAttributes.cat && this.shortcodeAttributes.cat.length)
		
		this.showPreloader(false);
		
		for(var i = 0; i < data.length; i++)
		{
			var obj = data[i];
			var marker = WPGMZA.Marker.createInstance(obj);
			
			if(startFiltered)
			{
				marker.isFiltered = true;
				marker.setVisible(false);
			}
			
			this.addMarker(marker);
			
			// Legacy support
			if(window.marker_array)
				marker_array[this.id][obj.id] = marker;
		}
		
		var triggerEvent = function()
		{
			self._markersPlaced = true;
			self.trigger("markersplaced");
			self.off("filteringcomplete", triggerEvent);
		}
		
		if(this.shortcodeAttributes.cat)
		{
			var categories = this.shortcodeAttributes.cat.split(",");
			
			// Set filtering controls
			var select = $("select[mid='" + this.id + "'][name='wpgmza_filter_select']");
			
			for(var i = 0; i < categories.length; i++)
			{
				$("input[type='checkbox'][mid='" + this.id + "'][value='" + categories[i] + "']").prop("checked", true);
				select.val(categories[i]);
			}
			
			this.on("filteringcomplete", triggerEvent);
			
			// Force category ID's in case no filtering controls are present
			this.markerFilter.update({
				categories: categories
			});
		}
		else
			triggerEvent();

		//Check to see if they have added markers in the shortcode
		if(this.shortcodeAttributes.markers)
		{	 
			//remove all , from the shortcode to find ID's  
			var arr = this.shortcodeAttributes.markers.split(",");

			//Store all the markers ID's
			var markers = [];
		   
			//loop through the shortcode
			for (var i = 0; i < arr.length; i++)
			{
				var id = arr[i];
			    id = id.replace(' ', '');
				
				var marker = this.getMarkerByID(id);
		   
				//push the marker infromation to markers
				markers.push(marker);
			}

			//call fitMapBoundsToMarkers function on markers ID's in shortcode
			this.fitMapBoundsToMarkers(markers);	   
		}
		
		if(this.shortcodeAttributes.acf_post_id)
			marker.trigger("select");
	}
	
	/**
	 * Called internally to update the infowindow distances, for example, when the users location has changed or a new search has been performed
	 * @method
	 * @protected
	 * @memberof WPGMZA.ProMap
	 */
	WPGMZA.ProMap.prototype.updateInfoWindowDistances = function()
	{
		var location = this.showDistanceFromLocation;
		
		this.markers.forEach(function(marker) {
			
			if(!marker.infoWindow)
				return;
			
			marker.infoWindow.updateDistanceFromLocation();
			
		});
	}

	/**
	 * Find out if the map has visible markers. Only counts filterable markers (not the user location marker, store locator center point marker, etc.)
	 * @method
	 * @memberof WPGMZA.ProMap
	 * @returns {Boolean} True if at least one marker is visible
	 */
	WPGMZA.ProMap.prototype.hasVisibleMarkers = function()
	{
		 // grab markers
		 var markers = this.markers;
		 
		 // loop through all the markers
		 for (var i = 0; i < markers.length; i++)
		 {
			 // Find only visible markers after filtering
			 if(markers[i].isFilterable && markers[i].getVisible())
				return true;
		 }
		 
		 return false;
	}
	
});

// js/v8/google-maps/google-map.js
/**
 * @namespace WPGMZA
 * @module GoogleMap
 * @requires WPGMZA.Map
 * @pro-requires WPGMZA.ProMap
 */
jQuery(function($) {
	var Parent;
	
	/**
	 * Constructor
	 * @param element to contain the map
	 */
	WPGMZA.GoogleMap = function(element, options)
	{
		var self = this;
		
		Parent.call(this, element, options);
		
		if(!window.google)
		{
			var status = WPGMZA.googleAPIStatus;
			var message = "Google API not loaded";
			
			if(status && status.message)
				message += " - " + status.message;
			
			if(status.code == "USER_CONSENT_NOT_GIVEN")
			{
				return;
			}
			
			$(element).html("<div class='notice notice-error'><p>" + WPGMZA.localized_strings.google_api_not_loaded + "<pre>" + message + "</pre></p></div>");
			
			throw new Error(message);
		}
		
		this.loadGoogleMap();
		
		if(options)
			this.setOptions(options, true);

		google.maps.event.addListener(this.googleMap, "click", function(event) {
			var wpgmzaEvent = new WPGMZA.Event("click");
			wpgmzaEvent.latLng = {
				lat: event.latLng.lat(),
				lng: event.latLng.lng()
			};
			self.dispatchEvent(wpgmzaEvent);
		});
		
		google.maps.event.addListener(this.googleMap, "rightclick", function(event) {
			var wpgmzaEvent = new WPGMZA.Event("rightclick");
			wpgmzaEvent.latLng = {
				lat: event.latLng.lat(),
				lng: event.latLng.lng()
			};
			self.dispatchEvent(wpgmzaEvent);
		});
		
		google.maps.event.addListener(this.googleMap, "dragend", function(event) {
			self.dispatchEvent("dragend");
		});
		
		google.maps.event.addListener(this.googleMap, "zoom_changed", function(event) {
			self.dispatchEvent("zoom_changed");
			self.dispatchEvent("zoomchanged");
		});
		
		// Idle event
		google.maps.event.addListener(this.googleMap, "idle", function(event) {
			self.onIdle(event);
		});
		
		// Dispatch event
		if(!WPGMZA.isProVersion())
		{
			this.trigger("init");
			
			this.dispatchEvent("created");
			WPGMZA.events.dispatchEvent({type: "mapcreated", map: this});
			
			// Legacy event
			$(this.element).trigger("wpgooglemaps_loaded");
		}
	}
	
	// If we're running the Pro version, inherit from ProMap, otherwise, inherit from Map
	if(WPGMZA.isProVersion())
	{
		Parent = WPGMZA.ProMap;
		WPGMZA.GoogleMap.prototype = Object.create(WPGMZA.ProMap.prototype);
	}
	else
	{
		Parent = WPGMZA.Map;
		WPGMZA.GoogleMap.prototype = Object.create(WPGMZA.Map.prototype);
	}
	WPGMZA.GoogleMap.prototype.constructor = WPGMZA.GoogleMap;
	
	WPGMZA.GoogleMap.parseThemeData = function(raw)
	{
		var json;
		
		try{
			json = JSON.parse(raw);	// Try to parse strict JSON
		}catch(e) {
			
			try{
				
				json = eval(raw);	// Try to parse JS object
				
			}catch(e) {
				
				var str = raw;
				
				str = str.replace(/\\'/g, '\'');
				str = str.replace(/\\"/g, '"');
				str = str.replace(/\\0/g, '\0');
				str = str.replace(/\\\\/g, '\\');
				
				try{
					
					json = eval(str);
					
				}catch(e) {
					
					console.warn("Couldn't parse theme data");
				
				return [];
					
				}
				
			}
			
		}
		
		return json;
	}
	
	/**
	 * Creates the Google Maps map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.loadGoogleMap = function()
	{
		var self = this;
		var options = this.settings.toGoogleMapsOptions();
		
		this.googleMap = new google.maps.Map(this.engineElement, options);
		
		google.maps.event.addListener(this.googleMap, "bounds_changed", function() { 
			self.onBoundsChanged();
		});
		
		if(this.settings.bicycle == 1)
			this.enableBicycleLayer(true);
		if(this.settings.traffic == 1)
			this.enableTrafficLayer(true);
		if(this.settings.transport == 1)
			this.enablePublicTransportLayer(true);
		this.showPointsOfInterest(this.settings.show_point_of_interest);
		
		// Move the loading wheel into the map element (it has to live outside in the HTML file because it'll be overwritten by Google otherwise)
		$(this.engineElement).append($(this.element).find(".wpgmza-loader"));
	}
	
	WPGMZA.GoogleMap.prototype.setOptions = function(options, initializing)
	{
		Parent.prototype.setOptions.call(this, options);
		
		if(options.scrollwheel)
			delete options.scrollwheel;	// NB: Delete this when true, scrollwheel: true breaks gesture handling
		
		if(!initializing)
		{
			this.googleMap.setOptions(options);
			return;
		}
		
		var converted = $.extend(options, this.settings.toGoogleMapsOptions());
		
		var clone = $.extend({}, converted);
		if(!clone.center instanceof google.maps.LatLng && (clone.center instanceof WPGMZA.LatLng || typeof clone.center == "object"))
			clone.center = {
				lat: parseFloat(clone.center.lat),
				lng: parseFloat(clone.center.lng)
			};
		
		if(this.settings.hide_point_of_interest == "1")
		{
			var noPoi = {
				featureType: "poi",
				elementType: "labels",
				stylers: [
					{
						visibility: "off"
					}
				]
			};
			
			if(!clone.styles)
				clone.styles = [];
			
			clone.styles.push(noPoi);
		}
		
		this.googleMap.setOptions(clone);
	}
	
	/**
	 * Adds the specified marker to this map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.addMarker = function(marker)
	{
		marker.googleMarker.setMap(this.googleMap);
		
		Parent.prototype.addMarker.call(this, marker);
	}
	
	/**
	 * Removes the specified marker from this map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.removeMarker = function(marker)
	{
		marker.googleMarker.setMap(null);
		
		Parent.prototype.removeMarker.call(this, marker);
	}
	
	/**
	 * Adds the specified polygon to this map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.addPolygon = function(polygon)
	{
		polygon.googlePolygon.setMap(this.googleMap);
		
		Parent.prototype.addPolygon.call(this, polygon);
	}
	
	/**
	 * Removes the specified polygon from this map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.removePolygon = function(polygon)
	{
		polygon.googlePolygon.setMap(null);
		
		Parent.prototype.removePolygon.call(this, polygon);
	}
	
	/**
	 * Adds the specified polyline to this map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.addPolyline = function(polyline)
	{
		polyline.googlePolyline.setMap(this.googleMap);
		
		Parent.prototype.addPolyline.call(this, polyline);
	}
	
	/**
	 * Removes the specified polygon from this map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.removePolyline = function(polyline)
	{
		polyline.googlePolyline.setMap(null);
		
		Parent.prototype.removePolyline.call(this, polyline);
	}
	
	WPGMZA.GoogleMap.prototype.addCircle = function(circle)
	{
		circle.googleCircle.setMap(this.googleMap);
		
		Parent.prototype.addCircle.call(this, circle);
	}
	
	WPGMZA.GoogleMap.prototype.removeCircle = function(circle)
	{
		circle.googleCircle.setMap(null);
		
		Parent.prototype.removeCircle.call(this, circle);
	}
	
	WPGMZA.GoogleMap.prototype.addRectangle = function(rectangle)
	{
		rectangle.googleRectangle.setMap(this.googleMap);
		
		Parent.prototype.addRectangle.call(this, rectangle);
	}
	
	WPGMZA.GoogleMap.prototype.removeRectangle = function(rectangle)
	{
		rectangle.googleRectangle.setMap(null);
		
		Parent.prototype.removeRectangle.call(this, rectangle);
	}
	
	/**
	 * Delegate for google maps getCenter
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.getCenter = function()
	{
		var latLng = this.googleMap.getCenter();
		
		return {
			lat: latLng.lat(),
			lng: latLng.lng()
		};
	}
	
	/**
	 * Delegate for google maps setCenter
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.setCenter = function(latLng)
	{
		WPGMZA.Map.prototype.setCenter.call(this, latLng);
		
		if(latLng instanceof WPGMZA.LatLng)
			this.googleMap.setCenter({
				lat: latLng.lat,
				lng: latLng.lng
			});
		else
			this.googleMap.setCenter(latLng);
	}
	
	/**
	 * Delegate for google maps setPan
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.panTo = function(latLng)
	{
		if(latLng instanceof WPGMZA.LatLng)
			this.googleMap.panTo({
				lat: latLng.lat,
				lng: latLng.lng
			});
		else
			this.googleMap.panTo(latLng);
	}
	
	/**
	 * Delegate for google maps getCenter
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.getZoom = function()
	{
		return this.googleMap.getZoom();
	}
	
	/**
	 * Delegate for google maps getZoom
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.setZoom = function(value)
	{
		if(isNaN(value))
			throw new Error("Value must not be NaN");
		
		return this.googleMap.setZoom(parseInt(value));
	}
	
	/**
	 * Gets the bounds
	 * @return object
	 */
	WPGMZA.GoogleMap.prototype.getBounds = function()
	{
		var bounds = this.googleMap.getBounds();
		var northEast = bounds.getNorthEast();
		var southWest = bounds.getSouthWest();
		
		var nativeBounds = new WPGMZA.LatLngBounds({});
		
		nativeBounds.north = northEast.lat();
		nativeBounds.south = southWest.lat();
		nativeBounds.west = southWest.lng();
		nativeBounds.east = northEast.lng();
		
		// Backward compatibility
		nativeBounds.topLeft = {
			lat: northEast.lat(),
			lng: southWest.lng()
		};
		
		nativeBounds.bottomRight = {
			lat: southWest.lat(),
			lng: northEast.lng()
		};
		
		return nativeBounds;
	}
	
	/**
	 * Fit to given boundaries
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.fitBounds = function(southWest, northEast)
	{
		if(southWest instanceof WPGMZA.LatLng)
			southWest = {lat: southWest.lat, lng: southWest.lng};
		if(northEast instanceof WPGMZA.LatLng)
			northEast = {lat: northEast.lat, lng: northEast.lng};
		else if(southWest instanceof WPGMZA.LatLngBounds)
		{
			var bounds = southWest;
			
			southWest = {
				lat: bounds.south,
				lng: bounds.west
			};
			
			northEast = {
				lat: bounds.north,
				lng: bounds.east
			};
		}
		
		var nativeBounds = new google.maps.LatLngBounds(southWest, northEast);
		this.googleMap.fitBounds(nativeBounds);
	}
	
	/**
	 * Fit the map boundaries to visible markers
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.fitBoundsToVisibleMarkers = function()
	{
		var bounds = new google.maps.LatLngBounds();
		for(var i = 0; i < this.markers.length; i++)
		{
			if(markers[i].getVisible())
				bounds.extend(markers[i].getPosition());
		}
		this.googleMap.fitBounds(bounds);
	}
	
	/**
	 * Enables / disables the bicycle layer
	 * @param enable boolean, enable or not
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.enableBicycleLayer = function(enable)
	{
		if(!this.bicycleLayer)
			this.bicycleLayer = new google.maps.BicyclingLayer();
		
		this.bicycleLayer.setMap(
			enable ? this.googleMap : null
		);
	}
	
	/**
	 * Enables / disables the bicycle layer
	 * @param enable boolean, enable or not
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.enableTrafficLayer = function(enable)
	{
		if(!this.trafficLayer)
			this.trafficLayer = new google.maps.TrafficLayer();
		
		this.trafficLayer.setMap(
			enable ? this.googleMap : null
		);
	}
	
	/**
	 * Enables / disables the bicycle layer
	 * @param enable boolean, enable or not
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.enablePublicTransportLayer = function(enable)
	{
		if(!this.publicTransportLayer)
			this.publicTransportLayer = new google.maps.TransitLayer();
		
		this.publicTransportLayer.setMap(
			enable ? this.googleMap : null
		);
	}
	
	/**
	 * Shows / hides points of interest
	 * @param show boolean, enable or not
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.showPointsOfInterest = function(show)
	{
		// TODO: This will bug the front end because there is no textarea with theme data
		var text = $("textarea[name='theme_data']").val();
		
		if(!text)
			return;
		
		var styles = JSON.parse(text);
		
		styles.push({
			featureType: "poi",
			stylers: [
				{
					visibility: (show ? "on" : "off")
				}
			]
		});
		
		this.googleMap.setOptions({styles: styles});
	}
	
	/**
	 * Gets the min zoom of the map
	 * @return int
	 */
	WPGMZA.GoogleMap.prototype.getMinZoom = function()
	{
		return parseInt(this.settings.min_zoom);
	}
	
	/**
	 * Sets the min zoom of the map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.setMinZoom = function(value)
	{
		this.googleMap.setOptions({
			minZoom: value,
			maxZoom: this.getMaxZoom()
		});
	}
	
	/**
	 * Gets the min zoom of the map
	 * @return int
	 */
	WPGMZA.GoogleMap.prototype.getMaxZoom = function()
	{
		return parseInt(this.settings.max_zoom);
	}
	
	/**
	 * Sets the min zoom of the map
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.setMaxZoom = function(value)
	{
		this.googleMap.setOptions({
			minZoom: this.getMinZoom(),
			maxZoom: value
		});
	}
	
	WPGMZA.GoogleMap.prototype.latLngToPixels = function(latLng)
	{
		var map = this.googleMap;
		var nativeLatLng = new google.maps.LatLng({
			lat: parseFloat(latLng.lat),
			lng: parseFloat(latLng.lng)
		});
		var topRight = map.getProjection().fromLatLngToPoint(map.getBounds().getNorthEast());
		var bottomLeft = map.getProjection().fromLatLngToPoint(map.getBounds().getSouthWest());
		var scale = Math.pow(2, map.getZoom());
		var worldPoint = map.getProjection().fromLatLngToPoint(nativeLatLng);
		return {
			x: (worldPoint.x - bottomLeft.x) * scale, 
			y: (worldPoint.y - topRight.y) * scale
		};
	}
	
	WPGMZA.GoogleMap.prototype.pixelsToLatLng = function(x, y)
	{
		if(y == undefined)
		{
			if("x" in x && "y" in x)
			{
				y = x.y;
				x = x.x;
			}
			else
				console.warn("Y coordinate undefined in pixelsToLatLng (did you mean to pass 2 arguments?)");
		}
		
		var map = this.googleMap;
		var topRight = map.getProjection().fromLatLngToPoint(map.getBounds().getNorthEast());
		var bottomLeft = map.getProjection().fromLatLngToPoint(map.getBounds().getSouthWest());
		var scale = Math.pow(2, map.getZoom());
		var worldPoint = new google.maps.Point(x / scale + bottomLeft.x, y / scale + topRight.y);
		var latLng = map.getProjection().fromPointToLatLng(worldPoint);
		return {
			lat: latLng.lat(),
			lng: latLng.lng()
		};
	}
	
	/**
	 * Handle the map element resizing
	 * @return void
	 */
	WPGMZA.GoogleMap.prototype.onElementResized = function(event)
	{
		if(!this.googleMap)
			return;
		google.maps.event.trigger(this.googleMap, "resize");
	}

	WPGMZA.GoogleMap.prototype.enableAllInteractions = function()
	{	
		var options = {};

		options.scrollwheel  = true;
		options.draggable	=  true;
		options.disableDoubleClickZoom	= false;
		
		this.googleMap.setOptions(options);
	}
	
});

// js/v8/open-layers/ol-map.js
/**
 * @namespace WPGMZA
 * @module OLMap
 * @requires WPGMZA.Map
 * @pro-requires WPGMZA.ProMap
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.OLMap = function(element, options)
	{
		var self = this;
		
		Parent.call(this, element);
		
		this.setOptions(options);
		
		var viewOptions = this.settings.toOLViewOptions();
		
		$(this.element).html("");
		
		this.olMap = new ol.Map({
			target: $(element)[0],
			layers: [
				this.getTileLayer()
			],
			view: new ol.View(viewOptions)
		});
		
		// TODO: Re-implement using correct setting names
		// Interactions
		this.olMap.getInteractions().forEach(function(interaction) {
			
			// NB: The true and false values are flipped because these settings represent the "disabled" state when true
			if(interaction instanceof ol.interaction.DragPan)
				interaction.setActive( (self.settings.wpgmza_settings_map_draggable == "yes" ? false : true) );
			else if(interaction instanceof ol.interaction.DoubleClickZoom)
				interaction.setActive( (self.settings.wpgmza_settings_map_clickzoom ? false : true) );
			else if(interaction instanceof ol.interaction.MouseWheelZoom)
				interaction.setActive( (self.settings.wpgmza_settings_map_scroll == "yes" ? false : true) );
			
		}, this);
		
		// Cooperative gesture handling
		if(!(this.settings.wpgmza_force_greedy_gestures == "greedy" || this.settings.wpgmza_force_greedy_gestures == "yes"))
		{
			this.gestureOverlay = $("<div class='wpgmza-gesture-overlay'></div>")
			this.gestureOverlayTimeoutID = null;
			
			if(WPGMZA.isTouchDevice())
			{
				// On touch devices, require two fingers to drag and pan
				// NB: Temporarily removed due to inconsistent behaviour
				/*this.olMap.getInteractions().forEach(function(interaction) {
					
					if(interaction instanceof ol.interaction.DragPan)
						self.olMap.removeInteraction(interaction);
					
				});
				
				this.olMap.addInteraction(new ol.interaction.DragPan({
					
					condition: function(olBrowserEvent) {
						
						var allowed = olBrowserEvent.originalEvent.touches.length == 2;
						
						if(!allowed)
							self.showGestureOverlay();
						
						return allowed;
					}
					
				}));
				
				this.gestureOverlay.text(WPGMZA.localized_strings.use_two_fingers);*/
			}
			else
			{
				// On desktops, require Ctrl + zoom to zoom, show an overlay if that condition is not met
				this.olMap.on("wheel", function(event) {
					
					if(!ol.events.condition.platformModifierKeyOnly(event))
					{
						self.showGestureOverlay();
						event.originalEvent.preventDefault();
						return false;
					}
					
				});
				
				this.gestureOverlay.text(WPGMZA.localized_strings.use_ctrl_scroll_to_zoom);
			}
		}
		
		// Controls
		this.olMap.getControls().forEach(function(control) {
			
			// NB: The true and false values are flipped because these settings represent the "disabled" state when true
			if(control instanceof ol.control.Zoom && WPGMZA.settings.wpgmza_settings_map_zoom == "yes")
				self.olMap.removeControl(control);
			
		}, this);
		
		if(WPGMZA.settings.wpgmza_settings_map_full_screen_control != "yes")
			this.olMap.addControl(new ol.control.FullScreen());
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			// Marker layer
			this.markerLayer = new ol.layer.Vector({
				source: new ol.source.Vector({
					features: []
				})
			});
			this.olMap.addLayer(this.markerLayer);
			
			this.olMap.on("click", function(event) {
				var features = self.olMap.getFeaturesAtPixel(event.pixel);
				
				if(!features || !features.length)
					return;
				
				var marker = features[0].wpgmzaMarker;
				
				if(!marker)
					return;
				
				marker.trigger("click");
				marker.trigger("select");
			});
		}
		
		// Listen for drag start
		this.olMap.on("movestart", function(event) {
			self.isBeingDragged = true;
		});
		
		// Listen for end of pan so we can wrap longitude if needs be
		this.olMap.on("moveend", function(event) {
			self.wrapLongitude();
			
			self.isBeingDragged = false;
			self.dispatchEvent("dragend");
			self.onIdle();
		});
		
		// Listen for zoom
		this.olMap.getView().on("change:resolution", function(event) {
			self.dispatchEvent("zoom_changed");
			self.dispatchEvent("zoomchanged");
			setTimeout(function() {
				self.onIdle();
			}, 10);
		});
		
		// Listen for bounds changing
		this.olMap.getView().on("change", function() {
			// Wrap longitude
			self.onBoundsChanged();
		});
		self.onBoundsChanged();
		
		// Store locator center
		var marker;
		if(this.storeLocator && (marker = this.storeLocator.centerPointMarker))
		{
			this.olMap.addOverlay(marker.overlay);
			marker.setVisible(false);
		}
		
		// Right click listener
		$(this.element).on("click contextmenu", function(event) {
			
			var isRight;
			event = event || window.event;
			
			var latLng = self.pixelsToLatLng(event.offsetX, event.offsetY);
			
			if("which" in event)
				isRight = event.which == 3;
			else if("button" in event)
				isRight = event.button == 2;
			
			if(event.which == 1 || event.button == 1)
			{
				if(self.isBeingDragged)
					return;
				
				// Left click
				if($(event.target).closest(".ol-marker").length)
					return; // A marker was clicked, not the map. Do nothing
				
				self.trigger({
					type: "click",
					latLng: latLng
				});
				
				return;
			}
			
			if(!isRight)
				return;
			
			return self.onRightClick(event);
		});
		
		// Dispatch event
		if(!WPGMZA.isProVersion())
		{
			this.trigger("init");
			
			this.dispatchEvent("created");
			WPGMZA.events.dispatchEvent({type: "mapcreated", map: this});
			
			// Legacy event
			$(this.element).trigger("wpgooglemaps_loaded");
		}
	}

	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProMap;
	else
		Parent = WPGMZA.Map;
	
	WPGMZA.OLMap.prototype = Object.create(Parent.prototype);
	WPGMZA.OLMap.prototype.constructor = WPGMZA.OLMap;
	
	WPGMZA.OLMap.prototype.getTileLayer = function()
	{
		var options = {};
		
		if(WPGMZA.settings.tile_server_url)
			options.url = WPGMZA.settings.tile_server_url;
		
		return new ol.layer.Tile({
			source: new ol.source.OSM(options)
		});
	}
	
	WPGMZA.OLMap.prototype.wrapLongitude = function()
	{
		var transformed = ol.proj.transform(this.olMap.getView().getCenter(), "EPSG:3857", "EPSG:4326");
		var center = {
			lat: transformed[1],
			lng: transformed[0]
		};
		
		if(center.lng >= -180 && center.lng <= 180)
			return;
		
		center.lng = center.lng - 360 * Math.floor(center.lng / 360);
		
		if(center.lng > 180)
			center.lng -= 360;
		
		this.setCenter(center);
	}
	
	WPGMZA.OLMap.prototype.getCenter = function()
	{
		var lonLat = ol.proj.toLonLat(
			this.olMap.getView().getCenter()
		);
		return {
			lat: lonLat[1],
			lng: lonLat[0]
		};
	}
	
	WPGMZA.OLMap.prototype.setCenter = function(latLng)
	{
		var view = this.olMap.getView();
		
		WPGMZA.Map.prototype.setCenter.call(this, latLng);
		
		view.setCenter(ol.proj.fromLonLat([
			latLng.lng,
			latLng.lat
		]));
		
		this.wrapLongitude();

		this.onBoundsChanged();
	}
	
	WPGMZA.OLMap.prototype.getBounds = function()
	{
		var bounds = this.olMap.getView().calculateExtent(this.olMap.getSize());
		var nativeBounds = new WPGMZA.LatLngBounds();
		
		var topLeft = ol.proj.toLonLat([bounds[0], bounds[1]]);
		var bottomRight = ol.proj.toLonLat([bounds[2], bounds[3]]);
		
		nativeBounds.north = topLeft[1];
		nativeBounds.south = bottomRight[1];
		
		nativeBounds.west = topLeft[0];
		nativeBounds.east = bottomRight[0];
		
		return nativeBounds;
	}
	
	/**
	 * Fit to given boundaries
	 * @return void
	 */
	WPGMZA.OLMap.prototype.fitBounds = function(southWest, northEast)
	{
		if(southWest instanceof WPGMZA.LatLng)
			southWest = {lat: southWest.lat, lng: southWest.lng};
		if(northEast instanceof WPGMZA.LatLng)
			northEast = {lat: northEast.lat, lng: northEast.lng};
		else if(southWest instanceof WPGMZA.LatLngBounds)
		{
			var bounds = southWest;
			
			southWest = {
				lat: bounds.south,
				lng: bounds.west
			};
			
			northEast = {
				lat: bounds.north,
				lng: bounds.east
			};
		}
		
		var view = this.olMap.getView();
		
		var extent = ol.extent.boundingExtent([
			ol.proj.fromLonLat([
				parseFloat(southWest.lng),
				parseFloat(southWest.lat)
			]),
			ol.proj.fromLonLat([
				parseFloat(northEast.lng),
				parseFloat(northEast.lat)
			])
		]);
		view.fit(extent, this.olMap.getSize());
	}
	
	WPGMZA.OLMap.prototype.panTo = function(latLng, zoom)
	{
		var view = this.olMap.getView();
		var options = {
			center: ol.proj.fromLonLat([
				parseFloat(latLng.lng),
				parseFloat(latLng.lat),
			]),
			duration: 500
		};
		
		if(arguments.length > 1)
			options.zoom = parseInt(zoom);
		
		view.animate(options);
	}
	
	WPGMZA.OLMap.prototype.getZoom = function()
	{
		return Math.round( this.olMap.getView().getZoom() );
	}
	
	WPGMZA.OLMap.prototype.setZoom = function(value)
	{
		this.olMap.getView().setZoom(value);
	}
	
	WPGMZA.OLMap.prototype.getMinZoom = function()
	{
		return this.olMap.getView().getMinZoom();
	}
	
	WPGMZA.OLMap.prototype.setMinZoom = function(value)
	{
		this.olMap.getView().setMinZoom(value);
	}
	
	WPGMZA.OLMap.prototype.getMaxZoom = function()
	{
		return this.olMap.getView().getMaxZoom();
	}
	
	WPGMZA.OLMap.prototype.setMaxZoom = function(value)
	{
		this.olMap.getView().setMaxZoom(value);
	}
	
	WPGMZA.OLMap.prototype.setOptions = function(options)
	{
		Parent.prototype.setOptions.call(this, options);
		
		if(!this.olMap)
			return;
		
		this.olMap.getView().setProperties( this.settings.toOLViewOptions() );
	}
	
	/**
	 * TODO: Consider moving all these functions to their respective classes, same on google map (DO IT!!! It's very misleading having them here)
	 */
	WPGMZA.OLMap.prototype.addMarker = function(marker)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT)
			this.olMap.addOverlay(marker.overlay);
		else
		{
			this.markerLayer.getSource().addFeature(marker.feature);
			marker.featureInSource = true;
		}
		
		Parent.prototype.addMarker.call(this, marker);
	}
	
	WPGMZA.OLMap.prototype.removeMarker = function(marker)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT)
			this.olMap.removeOverlay(marker.overlay);
		else
		{
			this.markerLayer.getSource().removeFeature(marker.feature);
			marker.featureInSource = false;
		}
		
		Parent.prototype.removeMarker.call(this, marker);
	}
	
	WPGMZA.OLMap.prototype.addPolygon = function(polygon)
	{
		this.olMap.addLayer(polygon.layer);
		
		Parent.prototype.addPolygon.call(this, polygon);
	}
	
	WPGMZA.OLMap.prototype.removePolygon = function(polygon)
	{
		this.olMap.removeLayer(polygon.layer);
		
		Parent.prototype.removePolygon.call(this, polygon);
	}
	
	WPGMZA.OLMap.prototype.addPolyline = function(polyline)
	{
		this.olMap.addLayer(polyline.layer);
		
		Parent.prototype.addPolyline.call(this, polyline);
	}
	
	WPGMZA.OLMap.prototype.removePolyline = function(polyline)
	{
		this.olMap.removeLayer(polyline.layer);
		
		Parent.prototype.removePolyline.call(this, polyline);
	}
	
	WPGMZA.OLMap.prototype.addCircle = function(circle)
	{
		this.olMap.addLayer(circle.layer);
		
		Parent.prototype.addCircle.call(this, circle);
	}
	
	WPGMZA.OLMap.prototype.removeCircle = function(circle)
	{
		this.olMap.removeLayer(circle.layer);
		
		Parent.prototype.removeCircle.call(this, circle);
	}
	
	WPGMZA.OLMap.prototype.addRectangle = function(rectangle)
	{
		this.olMap.addLayer(rectangle.layer);
		
		Parent.prototype.addRectangle.call(this, rectangle);
	}
	
	WPGMZA.OLMap.prototype.removeRectangle = function(rectangle)
	{
		this.olMap.removeLayer(rectangle.layer);
		
		Parent.prototype.removeRectangle.call(this, rectangle);
	}
	
	WPGMZA.OLMap.prototype.pixelsToLatLng = function(x, y)
	{
		if(y == undefined)
		{
			if("x" in x && "y" in x)
			{
				y = x.y;
				x = x.x;
			}
			else
				console.warn("Y coordinate undefined in pixelsToLatLng (did you mean to pass 2 arguments?)");
		}
		
		var coord = this.olMap.getCoordinateFromPixel([x, y]);
		
		if(!coord)
			return {
				x: null,
				y: null
			};
		
		var lonLat = ol.proj.toLonLat(coord);
		return {
			lat: lonLat[1],
			lng: lonLat[0]
		};
	}
	
	WPGMZA.OLMap.prototype.latLngToPixels = function(latLng)
	{
		var coord = ol.proj.fromLonLat([latLng.lng, latLng.lat]);
		var pixel = this.olMap.getPixelFromCoordinate(coord);
		
		if(!pixel)
			return {
				x: null,
				y: null
			};
		
		return {
			x: pixel[0],
			y: pixel[1]
		};
	}
	
	WPGMZA.OLMap.prototype.enableBicycleLayer = function(value)
	{
		if(value)
		{
			if(!this.bicycleLayer)
				this.bicycleLayer = new ol.layer.Tile({
					source: new ol.source.OSM({
						url: "http://{a-c}.tile.opencyclemap.org/cycle/{z}/{x}/{y}.png"
					})
				});
				
			this.olMap.addLayer(this.bicycleLayer);
		}
		else
		{
			if(!this.bicycleLayer)
				return;
			
			this.olMap.removeLayer(this.bicycleLayer);
		}
	}
	
	WPGMZA.OLMap.prototype.showGestureOverlay = function()
	{
		var self = this;
		
		clearTimeout(this.gestureOverlayTimeoutID);
		
		$(this.gestureOverlay).stop().animate({opacity: "100"});
		$(this.element).append(this.gestureOverlay);
		
		$(this.gestureOverlay).css({
			"line-height":	$(this.element).height() + "px",
			"opacity":		"1.0"
		});
		$(this.gestureOverlay).show();
		
		this.gestureOverlayTimeoutID = setTimeout(function() {
			self.gestureOverlay.fadeOut(2000);
		}, 2000);
	}
	
	WPGMZA.OLMap.prototype.onElementResized = function(event)
	{
		this.olMap.updateSize();
	}
	
	WPGMZA.OLMap.prototype.onRightClick = function(event)
	{
		if($(event.target).closest(".ol-marker, .wpgmza_modern_infowindow, .wpgmza-modern-store-locator").length)
			return true;
		
		var parentOffset = $(this.element).offset();
		var relX = event.pageX - parentOffset.left;
		var relY = event.pageY - parentOffset.top;
		var latLng = this.pixelsToLatLng(relX, relY);
		
		this.trigger({type: "rightclick", latLng: latLng});
		
		// Legacy event compatibility
		$(this.element).trigger({type: "rightclick", latLng: latLng});
		
		// Prevent menu
		event.preventDefault();
		return false;
	}

	WPGMZA.OLMap.prototype.enableAllInteractions = function()
	{	

		this.olMap.getInteractions().forEach(function(interaction) {
			
			if(interaction instanceof ol.interaction.DragPan || interaction instanceof ol.interaction.DoubleClickZoom || interaction instanceof ol.interaction.MouseWheelZoom)
			{
				interaction.setActive(true);
			}
			
		}, this);

	}
	
});

// js/v8/pro-marker-filter.js
/**
 * @namespace WPGMZA
 * @module ProMarkerFilter
 * @requires WPGMZA.MarkerFilter
 */
jQuery(function($) {
	
	WPGMZA.ProMarkerFilter = function(map)
	{
		var self = this;
		
		WPGMZA.MarkerFilter.call(this, map);
	}
	
	WPGMZA.ProMarkerFilter.prototype = Object.create(WPGMZA.MarkerFilter.prototype);
	WPGMZA.ProMarkerFilter.prototype.constructor = WPGMZA.ProMarkerFilter;
	
	WPGMZA.MarkerFilter.createInstance = function(map)
	{
		return new WPGMZA.ProMarkerFilter(map);
	}
	
	WPGMZA.ProMarkerFilter.prototype.getFilteringParameters = function()
	{
		var params = WPGMZA.MarkerFilter.prototype.getFilteringParameters.call(this);
		var mashupIDs = this.map.mashupIDs;
		
		if(mashupIDs)
			params.mashupIDs = mashupIDs;
		
		if(this.map.markerListing)
			params = $.extend(params, this.map.markerListing.getFilteringParameters());
		
		if(this.map.customFieldFilterController)
		{
			var customFieldFilterAjaxParams = this.map.customFieldFilterController.getAjaxRequestData();
			var customFieldFilterFilteringParams = customFieldFilterAjaxParams.data.widgetData;
			params.customFields = customFieldFilterFilteringParams;
		}
		
		return params;
	}
	
	WPGMZA.ProMarkerFilter.prototype.update = function(params, source)
	{
		var self = this;
		
		if(this.updateTimeoutID)
			return;
		
		if(!params)
			params = {};
		
		if(this.xhr)
		{
			this.xhr.abort();
			delete this.xhr;
		}
		
		function dispatchEvent(result)
		{
			var event = new WPGMZA.Event("filteringcomplete");
			
			event.map = self.map;
			event.source = source;
			
			event.filteredMarkers = result;
			event.filteringParams = params;
			
			self.onFilteringComplete(event);
			
			self.trigger(event);
			self.map.trigger(event);
		}
		
		this.updateTimeoutID = setTimeout(function() {
			
			params = $.extend(self.getFilteringParameters(), params);
			
			if(params.center instanceof WPGMZA.LatLng)
				params.center = params.center.toLatLngLiteral();
			
			if(params.hideAll)
			{
				// Hide all markers before a store locator search is done
				dispatchEvent([]);
				delete self.updateTimeoutID;
				return;
			}
			
			self.map.showPreloader(true);
			
			self.xhr = WPGMZA.restAPI.call("/markers", {
				data: {
					fields: ["id"],
					filter: JSON.stringify(params)
				},
				success: function(result, status, xhr) {
					
					self.map.showPreloader(false);
					
					dispatchEvent(result);
					
				},
				useCompressedPathVariable: true
			});
			
			delete self.updateTimeoutID;
			
		}, 0);
	}
	
	WPGMZA.ProMarkerFilter.prototype.onFilteringComplete = function(event)
	{
		var self = this;
		var map = [];
		
		event.filteredMarkers.forEach(function(data) {
			map[data.id] = true;
		});
		
		this.map.markers.forEach(function(marker) {
			
			if(!marker.isFilterable)
				return;
				
			var allowByFilter = map[marker.id] ? true : false;
			
			marker.isFiltered = !allowByFilter;
			marker.setVisible(allowByFilter);
			
		});
	}
	
});

// js/v8/pro-marker-panel.js
/**
 * @namespace WPGMZA
 * @module ProMarkerPanel
 * @pro-requires WPGMZA
 */
jQuery(function($){
	
	WPGMZA.ProMarkerPanel = function()
	{
		var self = this;
		
		this.element = $(".wpgmza-marker-panel");
		
		this.initAutoComplete();
		this.initMarkerIconPicker();
		this.initMarkerGalleryInput();
		this.initCategoryPicker();
		
		this.addressChanged = false;
		
		$(this.element).find("button.wpgmza-save-marker").on("click", function(event) {
			self.onSave(event);
		});
		
		$(document.body).on("click", "[data-edit-marker-id]", function(event) {
			self.onEditMarker(event);
		});
		
		$(this.element).find("input[data-ajax-name='address']").on("input change", function(event) {
			self.onAddressChanged(event);
		});
	}
	
	WPGMZA.ProMarkerPanel.prototype.initAutoComplete = function()
	{
		if(!window.google || !google.maps)
			return;
		
		if(!window.google.maps.places)
		{
			console.warn("Please enable the Places API to use address autocomplete");
			return;
		}
		
		this.autoComplete = new google.maps.places.Autocomplete(
			this.element.find("[data-ajax-name='address']")[0]
		);
	}
	
	WPGMZA.ProMarkerPanel.prototype.initMarkerIconPicker = function()
	{
		this.markerIconPicker = new WPGMZA.MarkerIconPicker($(this.element).find(".wpgmza-marker-icon-picker"));
	}
	
	WPGMZA.ProMarkerPanel.prototype.initMarkerGalleryInput = function()
	{
		this.markerGalleryInput = new WPGMZA.MarkerGalleryInput($(this.element).find("input[data-ajax-name='gallery']"));
	}
	
	WPGMZA.ProMarkerPanel.prototype.initCategoryPicker = function()
	{
		this.categoryPicker = new WPGMZA.CategoryPicker($(this.element).find(".wpgmza-category-picker"));
	}
	
	WPGMZA.ProMarkerPanel.prototype.showPleaseWaitButton = function()
	{
		$(this.element).find("button.wpgmza-save-marker").text(WPGMZA.localized_strings.add_marker);
		$(this.element).find("button.wpgmza-save-marker").prop("disabled", true);
	}
	
	WPGMZA.ProMarkerPanel.prototype.restoreSaveButton = function()
	{
		if($(this.element).find("[data-ajax-name='id']").val() == "-1")
			$(this.element).find("button.wpgmza-save-marker").text(WPGMZA.localized_strings.add_marker);
		else
			$(this.element).find("button.wpgmza-save-marker").text(WPGMZA.localized_strings.save_marker);
		
		$(this.element).find("button.wpgmza-save-marker").prop("disabled", false);
	}
	
	WPGMZA.ProMarkerPanel.prototype.reset = function()
	{
		$(this.element).find("[data-ajax-name='id']").val("-1");
		$(this.element).find("[data-ajax-name]:not([data-ajax-name='map_id'])").val("");
		$(this.element).find("select[data-ajax-name]>option:first-child").prop("selected", true);
		
		$(this.element).find("input[type='checkbox']").prop("checked", false);
		
		if(tinyMCE.get("wpgmza-description-editor"))
			tinyMCE.get("wpgmza-description-editor").setContent("");
		else
			$("#wpgmza-description-editor").val("");
		
		this.markerIconPicker.reset();
		this.categoryPicker.setSelection(null);
		this.markerGalleryInput.clear();
		
		this.restoreSaveButton();
		
		this.addressChanged = false;
	}
	
	WPGMZA.ProMarkerPanel.prototype.select = function(marker)
	{
		var self = this;
		
		this.reset();
		this.showPleaseWaitButton();
		
		WPGMZA.restAPI.call("/markers/" + marker.id + "?skip_cache=1&raw_data=true", {
			success: function(data, status, xhr) {
				
				self.populate(data);
				self.restoreSaveButton();
				
			}
		});
	}
	
	WPGMZA.ProMarkerPanel.prototype.populate = function(data)
	{
		for(var name in data)
		{
			switch(name)
			{
				case "description":
					if(tinyMCE.get("wpgmza-description-editor"))
						tinyMCE.get("wpgmza-description-editor").setContent(data.description);
					else
						$("#wpgmza-description-editor").val(data.description);
					break;
				
				case "icon":
					// NB: 8.0.10 and onwards select raw data
					this.markerIconPicker.setIcon(data.icon);
					continue;
					break;
				
				case "categories":
					this.categoryPicker.setSelection(data.categories);
					break;
				
				case "gallery":
					if(data.gallery)
						this.markerGalleryInput.populate(data.gallery);
					break;
				
				case "custom_field_data":
					
					data.custom_field_data.forEach(function(field) {
						$("fieldset[data-custom-field-id='" + field.id + "'] input[data-ajax-name]").val(field.value);
					});
				
					break;
				
				default:
					break;
			}
			
			var target = $(this.element).find("[data-ajax-name='" + name + "']");
			
			switch((target.attr("type") || "").toLowerCase())
			{
				case "checkbox":
				
					target.prop("checked", data[name] == 1);
				
					break;
				
				default:
				
					$(this.element).find("[data-ajax-name='" + name + "']:not(select)").val(data[name]);
					
					$(this.element).find("select[data-ajax-name='" + name + "']").each(function(index, el) {
						
						if(typeof data[name] == "string" && data[name].length == 0)
							return;
						
						$(el).val(data[name]);
						
					});
				
					break;
			}
			
			
		}
		
		this.addressChanged = false;
		
		// Legacy support - Add the pic to the gallery, but only if the gallery is blank
		if(data.pic && data.pic.length && (!data.gallery || !data.gallery.length))
		{
			this.markerGalleryInput.addPicture({
				url: data.pic
			});
		}
	}
	
	WPGMZA.ProMarkerPanel.prototype.getFieldData = function()
	{
		var fields = $(this.element).find("[data-ajax-name]");
		var data = {};
		
		fields.each(function(index, el) {
			
			var type = "text";
			if($(el).attr("type"))
				type = $(el).attr("type").toLowerCase();
			
			switch(type)
			{
				case "checkbox":
					data[$(el).attr("data-ajax-name")] = $(el).prop("checked") ? 1 : 0;
					break;
				
				case "radio":
					if($(el).prop("checked"))
						data[$(el).attr("data-ajax-name")] = $(el).val();
					break;
					
				default:
					data[$(el).attr("data-ajax-name")] = $(el).val()
					break;
			}
			
		});
		
		if(tinyMCE.get("wpgmza-description-editor")) {
			data.description = tinyMCE.get("wpgmza-description-editor").getContent();
		}
		else
			data.description = $("#wpgmza-description-editor").val();
		
		data.gallery = this.markerGalleryInput.toJSON();
		
		if(data.gallery.length == 0)
			data.gallery = null;
		
		return data;
	}
	
	WPGMZA.ProMarkerPanel.prototype.onEditMarker = function(event)
	{
		var self = this;
		var id = $(event.currentTarget).attr("data-edit-marker-id");
		
		this.reset();
		this.showPleaseWaitButton();
		
		WPGMZA.restAPI.call("/markers/" + id + "?skip_cache=1&raw_data=true", {
			
			success: function(data, status, xhr) {
				
				self.populate(data);
				self.restoreSaveButton();
			}
			
		});
	}
	
	WPGMZA.ProMarkerPanel.prototype.onAddressChanged = function(event)
	{
		this.addressChanged = true;
	}
	
	WPGMZA.ProMarkerPanel.prototype.onSave = function(event)
	{
		var self = this;
		var address = $(this.element).find("[data-ajax-name='address']").val();
		
		if(address.length == 0)
		{
			alert(WPGMZA.localized_strings.no_address_specified);
			return;
		}
		
		this.showPleaseWaitButton();
		
		function callback()
		{
			var id = $(self.element).find("[data-ajax-name='id']").val();
			var route = "/markers/";
			var markerIsNew = id == -1;
			
			if(!markerIsNew)
				route += id;
			
			WPGMZA.restAPI.call(route, {
				method: "POST",
				data: self.getFieldData(),
				success: function(data, status, xhr) {
					
					var marker;
					var map = WPGMZA.maps[0];
					
					self.reset();
					
					if(marker = map.getMarkerByID(id))
						map.removeMarker(marker);
					
					marker = WPGMZA.Marker.createInstance(data);
					map.addMarker(marker);
					
					marker.panIntoView();
					
					WPGMZA.adminMarkerDataTable.reload();
					
				}
			});
		}
		
		if(!this.addressChanged)
			callback();
		else
		{
			var geocoder = WPGMZA.Geocoder.createInstance();
			
			geocoder.geocode({
				address: address
			}, function(results, status) {
				
				switch(status)
				{

					case WPGMZA.Geocoder.SUCCESS:
					
						var latLng = results[0].latLng;
						
						$(self.element).find("[data-ajax-name='lat']").val(latLng.lat);
						$(self.element).find("[data-ajax-name='lng']").val(latLng.lng);
						
						callback();
						
						break;
						
					case WPGMZA.Geocoder.ZERO_RESULTS:
						alert(WPGMZA.localized_strings.address_not_found);
						self.restoreSaveButton();
						break;
						
					default:
						alert(WPGMZA.localized_strings.geocode_fail);
						self.restoreSaveButton();
						break;
				}
				
			});
		}
	}
	
});

// js/v8/pro-marker.js
/**
 * @namespace WPGMZA
 * @module ProMarker
 * @requires WPGMZA.Marker
 */
jQuery(function($) {
	
	/**
	 *  Pro marker class. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.ProMarker
	 * @constructor
	 * @memberof WPGMZA
	 * @param {object} row The data for the marker
	 * @augments WPGMZA.Marker
	 */
	WPGMZA.ProMarker = function(row)
	{
		var self = this;
		
		this._icon = WPGMZA.MarkerIcon.createInstance();
		
		this.title = "";
		this.description = "";
		this.categories = [];
		this.approved = 1;
		
		if(row && row.category && row.category.length)
		{
			var m = row.category.match(/\d+/g);
			
			if(m)
				this.categories = m;
		}
		
		WPGMZA.Marker.call(this, row);
		
		this.on("mouseover", function(event) {
			self.onMouseOver(event);
		});
	}
	
	WPGMZA.ProMarker.prototype = Object.create(WPGMZA.Marker.prototype);
	WPGMZA.ProMarker.prototype.constructor = WPGMZA.ProMarker;
	
	WPGMZA.ProMarker.STICKY_ZINDEX			= 999999;
	
	// NB: I feel this should be passed from the server rather than being linked to the ID, however this should suffice for now as integrated markers should never have an integer ID (it would potentially collide with native markers)
	Object.defineProperty(WPGMZA.ProMarker.prototype, "isIntegrated", {
		
		get: function() {
			
			return /[^\d]/.test(this.id);
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProMarker.prototype, "icon", {
		
		get: function()
		{
			return this._icon;
		},
		
		set: function(value)
		{
			if(value instanceof WPGMZA.MarkerIcon)
			{
				this._icon = value;
				
				if(this.map)
					this.updateIcon();
			}
			else if(typeof value == "object" || typeof value == "string")
			{
				this._icon = WPGMZA.MarkerIcon.createInstance(value);
				
				if(this.map)
					this.updateIcon();
			}
			else
				throw new Error("Value must be an instance of WPGMZA.MarkerIcon, an icon literal, or a string");
		}
		
	});
	
	/**
	 * Called when the marker has been added to a map
	 * @method
	 * @memberof WPGMZA.Marker
	 * @listens module:WPGMZA.ProMarker~added
	 * @fires module:WPGMZA.ProMarker~select When this marker is targeted by the marker shortcode attribute
	 */
	WPGMZA.ProMarker.prototype.onAdded = function(event)
	{
		var m;
		
		WPGMZA.Marker.prototype.onAdded.call(this, event);
		
		this.updateIcon();
		
		if(this.map.storeLocator && this == this.map.storeLocator.marker)
			return;
		
		if(this == this.map.userLocationMarker)
			return;
		
		if(this.map.settings.store_locator_hide_before_search == 1 && WPGMZA.is_admin != 1 && this.isFilterable)
		{
			this.isFiltered = true;
			this.setVisible(false);
			
			return;
		}
		
		if(
			WPGMZA.getQueryParamValue("markerid") == this.id
			|| 
			this.map.shortcodeAttributes.marker == this.id
			)
		{
			this.openInfoWindow();
			this.map.setCenter(this.getPosition());
		}
		
		if("approved" in this && this.approved == 0)
			this.setOpacity(0.6);
		
		if(this.sticky == 1)
			this.setOptions({
				zIndex: WPGMZA.ProMarker.STICKY_ZINDEX
			});
	}
	
	/**
	 * Called when the marker has been clicked
	 * @method
	 * @memberof WPGMZA.ProMarker
	 * @listens module:WPGMZA.ProMarker~click
	 */
	WPGMZA.ProMarker.prototype.onClick = function(event)
	{
		WPGMZA.Marker.prototype.onClick.apply(this, arguments);
		
		if(this.map.settings.click_open_link == 1 && this.link && this.link.length)
		{
			if(WPGMZA.settings.wpgmza_settings_infowindow_links == "yes")
				window.open(this.link);
			else
				window.open(this.link, '_self');
		}
	}
	
	/**
	 * Called when the user hovers the mouse over this marker
	 * @method
	 * @memberof WPGMZA.ProMarker
	 * @listens module:WPGMZA.ProMarker~mouseover
	 */
	WPGMZA.ProMarker.prototype.onMouseOver = function(event)
	{
		if(WPGMZA.settings.wpgmza_settings_map_open_marker_by == WPGMZA.ProInfoWindow.OPEN_BY_HOVER)
			this.openInfoWindow();
	}
	
	/*WPGMZA.ProMarker.prototype.getIcon = function()
	{
		if(this.icon && this.icon.url.length)
			return this.icon;
		
		if(this.map.defaultMarkerIcon)
			return this.map.defaultMarkerIcon;
		
		return WPGMZA.MarkerIcon.createInstance({url: WPGMZA.defaultMarkerIcon});
	}*/
	
	WPGMZA.ProMarker.prototype.getIconFromCategory = function()
	{
		if(!this.categories.length)
			return;
		
		var self = this;
		var categoryIDs = this.categories.slice();
		
		// TODO: This could be taken from the category table now that it's cached. Would take some load off the client
		categoryIDs.sort(function(a, b) {
			var categoryA = self.map.getCategoryByID(a);
			var categoryB = self.map.getCategoryByID(b);
			
			if(!categoryA || !categoryB)
				return null;	// One of the category IDs is invalid
			
			return (categoryA.depth < categoryB.depth ? -1 : 1);
		});
		
		for(var i = 0; i < categoryIDs.length; i++)
		{
			var category = this.map.getCategoryByID(categoryIDs[i]);
			if(!category)
				continue;	// Invalid category ID
			
			var icon = category.icon;
			if(icon && icon.length)
				return icon;
		}
	}
	
	// NB: Deprecated, replaced with property. Provided for compatibility reasons
	WPGMZA.ProMarker.prototype.setIcon = function(icon)
	{
		this.icon = icon;
	}
	
	WPGMZA.ProMarker.prototype.openInfoWindow = function()
	{
		WPGMZA.Marker.prototype.openInfoWindow.apply(this);
		
		if(this.disableInfoWindow)
			return false;
		
		if(this.map && this.map.userLocationMarker == this)
			this.infoWindow.setContent(WPGMZA.localized_strings.my_location);
	}
	
});

// js/v8/google-maps/google-marker.js
/**
 * @namespace WPGMZA
 * @module GoogleMarker
 * @requires WPGMZA.Marker
 * @pro-requires WPGMZA.ProMarker
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.GoogleMarker = function(row)
	{
		var self = this;
		
		Parent.call(this, row);
		
		this._opacity = 1.0;
		
		var settings = {};
		if(row)
		{
			for(var name in row)
			{
				if(row[name] instanceof WPGMZA.LatLng)
				{
					settings[name] = row[name].toGoogleLatLng();
				}
				else if(row[name] instanceof WPGMZA.Map || name == "icon")
				{
					// NB: Ignore map here, it's not a google.maps.Map, Google would throw an exception
					// NB: Ignore icon here, it conflicts with updateIcon in Pro
				}
				else
					settings[name] = row[name];
			}
		}
		
		this.googleMarker = new google.maps.Marker(settings);
		this.googleMarker.wpgmzaMarker = this;
		
		this.googleMarker.setPosition(new google.maps.LatLng({
			lat: parseFloat(this.lat),
			lng: parseFloat(this.lng)
		}));
			
		// this.googleMarker.setLabel(this.settings.label);
		
		if(this.anim)
			this.googleMarker.setAnimation(this.anim);
		if(this.animation)
			this.googleMarker.setAnimation(this.animation);
			
		google.maps.event.addListener(this.googleMarker, "click", function() {
			self.dispatchEvent("click");
			self.dispatchEvent("select");
		});
		
		google.maps.event.addListener(this.googleMarker, "mouseover", function() {
			self.dispatchEvent("mouseover");
		});
		
		google.maps.event.addListener(this.googleMarker, "dragend", function() {
			var googleMarkerPosition = self.googleMarker.getPosition();
			
			self.setPosition({
				lat: googleMarkerPosition.lat(),
				lng: googleMarkerPosition.lng()
			});
			
			self.dispatchEvent({
				type: "dragend",
				latLng: self.getPosition()
			});
		});
		
		this.trigger("init");
	}
	
	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProMarker;
	else
		Parent = WPGMZA.Marker;
	WPGMZA.GoogleMarker.prototype = Object.create(Parent.prototype);
	WPGMZA.GoogleMarker.prototype.constructor = WPGMZA.GoogleMarker;
	
	Object.defineProperty(WPGMZA.GoogleMarker.prototype, "opacity", {
		
		"get": function() {
			return this._opacity;
		},
		
		"set": function(value) {
			this._opacity = value;
			this.googleMarker.setOpacity(value);
		}
		
	});
	
	WPGMZA.GoogleMarker.prototype.setLabel = function(label)
	{
		if(!label)
		{
			this.googleMarker.setLabel(null);
			return;
		}
		
		this.googleMarker.setLabel({
			text: label
		});
		
		if(!this.googleMarker.getIcon())
			this.googleMarker.setIcon(WPGMZA.settings.default_marker_icon);
	}
	
	/**
	 * Sets the position of the marker
	 * @return void
	 */
	WPGMZA.GoogleMarker.prototype.setPosition = function(latLng)
	{
		Parent.prototype.setPosition.call(this, latLng);
		this.googleMarker.setPosition({
			lat: this.lat,
			lng: this.lng
		});
	}
	
	/**
	 * Sets the position offset of a marker
	 * @return void
	 */
	WPGMZA.GoogleMarker.prototype.updateOffset = function()
	{
		var self = this;
		var icon = this.googleMarker.getIcon();
		var img = new Image();
		var params;
		var x = this._offset.x;
		var y = this._offset.y;
		
		if(!icon)
			icon = WPGMZA.settings.default_marker_icon;
		
		if(typeof icon == "string")
			params = {
				url: icon
			};
		else
			params = icon;
		
		img.onload = function()
		{
			var defaultAnchor = {
				x: img.width / 2,
				y: img.height
			};
			
			params.anchor = new google.maps.Point(defaultAnchor.x - x, defaultAnchor.y - y);
			
			self.googleMarker.setIcon(params);
		}
		
		img.src = params.url;
	}
	
	WPGMZA.GoogleMarker.prototype.setOptions = function(options)
	{
		this.googleMarker.setOptions(options);
	}
	
	/**
	 * Set the marker animation
	 * @return void
	 */
	WPGMZA.GoogleMarker.prototype.setAnimation = function(animation)
	{
		Parent.prototype.setAnimation.call(this, animation);
		this.googleMarker.setAnimation(animation);
	}
	
	/**
	 * Sets the visibility of the marker
	 * @return void
	 */
	WPGMZA.GoogleMarker.prototype.setVisible = function(visible)
	{
		Parent.prototype.setVisible.call(this, visible);
		
		this.googleMarker.setVisible(visible ? true : false);
	}
	
	WPGMZA.GoogleMarker.prototype.getVisible = function(visible)
	{
		return this.googleMarker.getVisible();
	}
	
	WPGMZA.GoogleMarker.prototype.setDraggable = function(draggable)
	{
		this.googleMarker.setDraggable(draggable);
	}
	
	WPGMZA.GoogleMarker.prototype.setOpacity = function(opacity)
	{
		this.googleMarker.setOpacity(opacity);
	}
	
});

// js/v8/open-layers/ol-marker.js
/**
 * @namespace WPGMZA
 * @module OLMarker
 * @requires WPGMZA.Marker
 * @pro-requires WPGMZA.ProMarker
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.OLMarker = function(row)
	{
		var self = this;
		
		Parent.call(this, row);

		var origin = ol.proj.fromLonLat([
			parseFloat(this.lng),
			parseFloat(this.lat)
		]);
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT)
		{
			var img = $("<img alt=''/>")[0];
			img.onload = function(event) {
				self.updateElementHeight();
				if(self.map)
					self.map.olMap.updateSize();
			}
			img.src = WPGMZA.defaultMarkerIcon;
			
			this.element = $("<div class='ol-marker'></div>")[0];
			$(this.element).attr('title', this.title);
			this.element.appendChild(img);
			
			this.element.wpgmzaMarker = this;
			
			$(this.element).on("mouseover", function(event) {
				self.dispatchEvent("mouseover");
			});
			
			this.overlay = new ol.Overlay({
				element: this.element,
				position: origin,
				positioning: "bottom-center",
				stopEvent: false
			});
			this.overlay.setPosition(origin);
			
			if(this.animation)
				this.setAnimation(this.animation);
			
			this.setLabel(this.settings.label);
			
			if(row)
			{
				if(row.draggable)
					this.setDraggable(true);
			}
			
			this.rebindClickListener();
		}
		else if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			this.feature = new ol.Feature({
				geometry: new ol.geom.Point(origin)
			});
			
			this.feature.setStyle(this.getVectorLayerStyle());
			this.feature.wpgmzaMarker = this;
		}
		else
			throw new Error("Invalid marker render mode");
		
		this.trigger("init");
	}
	
	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProMarker;
	else
		Parent = WPGMZA.Marker;
	
	WPGMZA.OLMarker.prototype = Object.create(Parent.prototype);
	WPGMZA.OLMarker.prototype.constructor = WPGMZA.OLMarker;
	
	WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT		= "element";
	WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER		= "vector";	// NB: This feature is experimental
	
	WPGMZA.OLMarker.renderMode = WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT;
	
	if(WPGMZA.settings.engine == "open-layers" && WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
	{
		WPGMZA.OLMarker.defaultVectorLayerStyle = new ol.style.Style({
			image: new ol.style.Icon({
				anchor: [0.5, 1],
				src: WPGMZA.defaultMarkerIcon
			})
		});
		
		WPGMZA.OLMarker.hiddenVectorLayerStyle = new ol.style.Style({});
	}
	
	WPGMZA.OLMarker.prototype.getVectorLayerStyle = function()
	{
		if(this.vectorLayerStyle)
			return this.vectorLayerStyle;
		
		return WPGMZA.OLMarker.defaultVectorLayerStyle;
	}
	
	WPGMZA.OLMarker.prototype.updateElementHeight = function(height, calledOnFocus)
	{
		var self = this;
		
		if(!height)
			height = $(this.element).find("img").height();
		
		if(height == 0 && !calledOnFocus)
		{
			$(window).one("focus", function(event) {
				self.updateElementHeight(false, true);
			});
		}
		
		$(this.element).css({height: height + "px"});
	}
	
	WPGMZA.OLMarker.prototype.addLabel = function()
	{
		this.setLabel(this.getLabelText());
	}
	
	WPGMZA.OLMarker.prototype.setLabel = function(label)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			console.warn("Marker labels are not currently supported in Vector Layer rendering mode");
			return;
		}
		
		if(!label)
		{
			if(this.label)
				$(this.element).find(".ol-marker-label").remove();
			
			return;
		}
		
		if(!this.label)
		{
			this.label = $("<div class='ol-marker-label'/>");
			$(this.element).append(this.label);
		}
		
		this.label.html(label);
	}
	
	WPGMZA.OLMarker.prototype.getVisible = function(visible)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			
		}
		else
			return this.overlay.getElement().style.display != "none";
	}
	
	WPGMZA.OLMarker.prototype.setVisible = function(visible)
	{
		Parent.prototype.setVisible.call(this, visible);
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			if(visible)
			{
				var style = this.getVectorLayerStyle();
				this.feature.setStyle(style);
			}
			else
				this.feature.setStyle(null);
			
			/*var source = this.map.markerLayer.getSource();
			
			/*if(this.featureInSource == visible)
				return;
			
			if(visible)
				source.addFeature(this.feature);
			else
				source.removeFeature(this.feature);
			
			this.featureInSource = visible;*/
		}
		else
			this.overlay.getElement().style.display = (visible ? "block" : "none");
	}
	
	WPGMZA.OLMarker.prototype.setPosition = function(latLng)
	{
		Parent.prototype.setPosition.call(this, latLng);
		
		var origin = ol.proj.fromLonLat([
			parseFloat(this.lng),
			parseFloat(this.lat)
		]);
	
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
			this.feature.setGeometry(new ol.geom.Point(origin));
		else
			this.overlay.setPosition(origin);
	}
	
	WPGMZA.OLMarker.prototype.updateOffset = function(x, y)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			console.warn("Marker offset is not currently supported in Vector Layer rendering mode");
			return;
		}
		
		var x = this._offset.x;
		var y = this._offset.y;
		
		this.element.style.position = "relative";
		this.element.style.left = x + "px";
		this.element.style.top = y + "px";
	}
	
	WPGMZA.OLMarker.prototype.setAnimation = function(anim)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			console.warn("Marker animation is not currently supported in Vector Layer rendering mode");
			return;
		}
		
		Parent.prototype.setAnimation.call(this, anim);
		
		switch(anim)
		{
			case WPGMZA.Marker.ANIMATION_NONE:
				$(this.element).removeAttr("data-anim");
				break;
			
			case WPGMZA.Marker.ANIMATION_BOUNCE:
				$(this.element).attr("data-anim", "bounce");
				break;
			
			case WPGMZA.Marker.ANIMATION_DROP:
				$(this.element).attr("data-anim", "drop");
				break;
		}
	}
	
	WPGMZA.OLMarker.prototype.setDraggable = function(draggable)
	{
		var self = this;
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			console.warn("Marker dragging is not currently supported in Vector Layer rendering mode");
			return;
		}
		
		if(draggable)
		{
			var options = {
				disabled: false
			};
			
			if(!this.jQueryDraggableInitialized)
			{
				options.start = function(event) {
					self.onDragStart(event);
				}
				
				options.stop = function(event) {
					self.onDragEnd(event);
				};
			}
			
			$(this.element).draggable(options);
			this.jQueryDraggableInitialized = true;
			
			this.rebindClickListener();
		}
		else
			$(this.element).draggable({disabled: true});
	}
	
	WPGMZA.OLMarker.prototype.setOpacity = function(opacity)
	{
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_VECTOR_LAYER)
		{
			console.warn("Marker opacity is not currently supported in Vector Layer rendering mode");
			return;
		}
		
		$(this.element).css({opacity: opacity});
	}
	
	WPGMZA.OLMarker.prototype.onDragStart = function(event)
	{
		this.isBeingDragged = true;
		
		this.map.olMap.getInteractions().forEach(function(interaction) {
			
			if(interaction instanceof ol.interaction.DragPan)
				interaction.setActive(false);
			
		});
	}
	
	WPGMZA.OLMarker.prototype.onDragEnd = function(event)
	{
		var self = this;
		var offset = {
			top:	parseFloat( $(this.element).css("top").match(/-?\d+/)[0] ),
			left:	parseFloat( $(this.element).css("left").match(/-?\d+/)[0] )
		};
		
		$(this.element).css({
			top: 	"0px",
			left: 	"0px"
		});
		
		var currentLatLng 		= this.getPosition();
		var pixelsBeforeDrag 	= this.map.latLngToPixels(currentLatLng);
		var pixelsAfterDrag		= {
			x: pixelsBeforeDrag.x + offset.left,
			y: pixelsBeforeDrag.y + offset.top
		};
		var latLngAfterDrag		= this.map.pixelsToLatLng(pixelsAfterDrag);
		
		this.setPosition(latLngAfterDrag);
		
		this.isBeingDragged = false;
		this.trigger({type: "dragend", latLng: latLngAfterDrag});
		
		// NB: "yes" represents disabled
		if(this.map.settings.wpgmza_settings_map_draggable != "yes")
			this.map.olMap.getInteractions().forEach(function(interaction) {
				
				if(interaction instanceof ol.interaction.DragPan)
					interaction.setActive(true);
				
			});
	}
	
	WPGMZA.OLMarker.prototype.onElementClick = function(event)
	{
		var self = event.currentTarget.wpgmzaMarker;
		
		if(self.isBeingDragged)
			return; // Don't dispatch click event after a drag
		
		self.dispatchEvent("click");
		self.dispatchEvent("select");
	}
	
	/**
	 * Binds / rebinds the click listener. This must be bound after draggable is initialized,
	 * this solves the click listener firing before dragend
	 */
	WPGMZA.OLMarker.prototype.rebindClickListener = function()
	{
		$(this.element).off("click", this.onElementClick);
		$(this.element).on("click", this.onElementClick);
	}
	
});

// js/v8/pro-polygon.js
/**
 * @namespace WPGMZA
 * @module ProPolygon
 * @requires WPGMZA.Polygon
 */
jQuery(function($) {
	
	WPGMZA.ProPolygon = function(row, googlePolygon)
	{
		WPGMZA.Polygon.call(this, row, googlePolygon);
	}
	
	WPGMZA.ProPolygon.prototype = Object.create(WPGMZA.Polygon.prototype);
	WPGMZA.ProPolygon.prototype.constructor = WPGMZA.ProPolygon;
	
	/*
	 * Adapted from, and with thanks to https://github.com/mapbox/polylabel
	 */
	WPGMZA.ProPolygon.getLabelPosition = function(geojson, precision, debug)
	{
		var polygon = geojson;
		
		precision = precision || 1.0;

		// find the bounding box of the outer ring
		var minX, minY, maxX, maxY;
		for (var i = 0; i < polygon[0].length; i++) {
			var p = polygon[0][i];
			if (!i || p[0] < minX) minX = p[0];
			if (!i || p[1] < minY) minY = p[1];
			if (!i || p[0] > maxX) maxX = p[0];
			if (!i || p[1] > maxY) maxY = p[1];
		}

		var width = maxX - minX;
		var height = maxY - minY;
		var cellSize = Math.min(width, height);
		var h = cellSize / 2;

		if (cellSize === 0) return [minX, minY];

		// a priority queue of cells in order of their "potential" (max distance to polygon)
		var cellQueue = new WPGMZA.Queue(null, compareMax);

		// cover polygon with initial cells
		for (var x = minX; x < maxX; x += cellSize) {
			for (var y = minY; y < maxY; y += cellSize) {
				cellQueue.push(new Cell(x + h, y + h, h, polygon));
			}
		}

		// take centroid as the first best guess
		var bestCell = getCentroidCell(polygon);

		// special case for rectangular polygons
		var bboxCell = new Cell(minX + width / 2, minY + height / 2, 0, polygon);
		if (bboxCell.d > bestCell.d) bestCell = bboxCell;

		var numProbes = cellQueue.length;

		while (cellQueue.length) {
			// pick the most promising cell from the queue
			var cell = cellQueue.pop();

			// update the best cell if we found a better one
			if (cell.d > bestCell.d) {
				bestCell = cell;
			}

			// do not drill down further if there's no chance of a better solution
			if (cell.max - bestCell.d <= precision) continue;

			// split the cell into four cells
			h = cell.h / 2;
			cellQueue.push(new Cell(cell.x - h, cell.y - h, h, polygon));
			cellQueue.push(new Cell(cell.x + h, cell.y - h, h, polygon));
			cellQueue.push(new Cell(cell.x - h, cell.y + h, h, polygon));
			cellQueue.push(new Cell(cell.x + h, cell.y + h, h, polygon));
			numProbes += 4;
		}

		return [bestCell.x, bestCell.y];
	}
	
	function compareMax(a, b) {
		return b.max - a.max;
	}

	function Cell(x, y, h, polygon) {
		this.x = x; // cell center x
		this.y = y; // cell center y
		this.h = h; // half the cell size
		this.d = pointToPolygonDist(x, y, polygon); // distance from cell center to polygon
		this.max = this.d + this.h * Math.SQRT2; // max distance to polygon within a cell
	}

	// signed distance from point to polygon outline (negative if point is outside)
	function pointToPolygonDist(x, y, polygon) {
		var inside = false;
		var minDistSq = Infinity;

		for (var k = 0; k < polygon.length; k++) {
			var ring = polygon[k];

			for (var i = 0, len = ring.length, j = len - 1; i < len; j = i++) {
				var a = ring[i];
				var b = ring[j];

				if ((a[1] > y !== b[1] > y) &&
					(x < (b[0] - a[0]) * (y - a[1]) / (b[1] - a[1]) + a[0])) inside = !inside;

				minDistSq = Math.min(minDistSq, getSegDistSq(x, y, a, b));
			}
		}

		return (inside ? 1 : -1) * Math.sqrt(minDistSq);
	}

	// get polygon centroid
	function getCentroidCell(polygon) {
		var area = 0;
		var x = 0;
		var y = 0;
		var points = polygon[0];

		for (var i = 0, len = points.length, j = len - 1; i < len; j = i++) {
			var a = points[i];
			var b = points[j];
			var f = a[0] * b[1] - b[0] * a[1];
			x += (a[0] + b[0]) * f;
			y += (a[1] + b[1]) * f;
			area += f * 3;
		}
		if (area === 0) return new Cell(points[0][0], points[0][1], 0, polygon);
		return new Cell(x / area, y / area, 0, polygon);
	}

	// get squared distance from a point to a segment
	function getSegDistSq(px, py, a, b) {

		var x = a[0];
		var y = a[1];
		var dx = b[0] - x;
		var dy = b[1] - y;

		if (dx !== 0 || dy !== 0) {

			var t = ((px - x) * dx + (py - y) * dy) / (dx * dx + dy * dy);

			if (t > 1) {
				x = b[0];
				y = b[1];

			} else if (t > 0) {
				x += dx * t;
				y += dy * t;
			}
		}

		dx = px - x;
		dy = py - y;

		return dx * dx + dy * dy;
	}
	
});

// js/v8/google-maps/google-polygon.js
/**
 * @namespace WPGMZA
 * @module GooglePolygon
 * @requires WPGMZA.Polygon
 * @pro-requires WPGMZA.ProPolygon
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.GooglePolygon = function(options, googlePolygon)
	{
		var self = this;
		
		Parent.call(this, options, googlePolygon);
		
		if(googlePolygon)
		{
			this.googlePolygon = googlePolygon;
		}
		else
		{
			this.googlePolygon = new google.maps.Polygon();
			
			if(options)
			{
				var googleOptions = $.extend({}, options);
				
				if(options.polydata)
					googleOptions.paths = this.parseGeometry(options.polydata);
				
				if(options.linecolor)
					googleOptions.strokeColor = "#" + options.linecolor;
				
				if(options.lineopacity)
					googleOptions.strokeOpacity = parseFloat(options.lineopacity);
				
				if(options.fillcolor)
					googleOptions.fillColor = "#" + options.fillcolor;
				
				if(options.opacity)
					googleOptions.fillOpacity = parseFloat(options.opacity);
				
				this.googlePolygon.setOptions(googleOptions);
			}
		}
		
		this.googlePolygon.wpgmzaPolygon = this;
			
		google.maps.event.addListener(this.googlePolygon, "click", function() {
			self.dispatchEvent({type: "click"});
		});
	}
	
	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProPolygon;
	else
		Parent = WPGMZA.Polygon;
		
	WPGMZA.GooglePolygon.prototype = Object.create(Parent.prototype);
	WPGMZA.GooglePolygon.prototype.constructor = WPGMZA.GooglePolygon;
	
	/**
	 * Returns true if the polygon is editable
	 * @return void
	 */
	WPGMZA.GooglePolygon.prototype.getEditable = function()
	{
		return this.googlePolygon.getOptions().editable;
	}
	
	/**
	 * Sets the editable state of the polygon
	 * @return void
	 */
	WPGMZA.GooglePolygon.prototype.setEditable = function(value)
	{
		this.googlePolygon.setOptions({editable: value});
	}
	
	/**
	 * Returns the polygon represented by a JSON object
	 * @return object
	 */
	WPGMZA.GooglePolygon.prototype.toJSON = function()
	{
		var result = WPGMZA.Polygon.prototype.toJSON.call(this);
		
		result.points = [];
		
		// TODO: Support holes using multiple paths
		var path = this.googlePolygon.getPath();
		for(var i = 0; i < path.getLength(); i++)
		{
			var latLng = path.getAt(i);
			result.points.push({
				lat: latLng.lat(),
				lng: latLng.lng()
			});
		}
		
		return result;
	}
	
});

// js/v8/open-layers/ol-polygon.js
/**
 * @namespace WPGMZA
 * @module OLPolygon
 * @requires WPGMZA.Polygon
 * @pro-requires WPGMZA.ProPolygon
 */
jQuery(function($) {
	
	var Parent;
	
	WPGMZA.OLPolygon = function(options, olFeature)
	{
		var self = this;
		
		Parent.call(this, options, olFeature);
		
		this.olStyle = new ol.style.Style();
		
		if(olFeature)
		{
			this.olFeature = olFeature;
		}
		else
		{
			var coordinates = [[]];
			
			if(options && options.polydata)
			{
				var paths = this.parseGeometry(options.polydata);
				
				for(var i = 0; i < paths.length; i++)
					coordinates[0].push(ol.proj.fromLonLat([
						parseFloat(paths[i].lng),
						parseFloat(paths[i].lat)
					]));
				
				this.olStyle = new ol.style.Style(this.getStyleFromSettings());
			}
			
			this.olFeature = new ol.Feature({
				geometry: new ol.geom.Polygon(coordinates)
			});
		}
		
		this.layer = new ol.layer.Vector({
			source: new ol.source.Vector({
				features: [this.olFeature]
			}),
			style: this.olStyle
		});
		
		this.layer.getSource().getFeatures()[0].setProperties({
			wpgmzaPolygon: this
		});
	}
	
	if(WPGMZA.isProVersion())
		Parent = WPGMZA.ProPolygon;
	else
		Parent = WPGMZA.Polygon;
	
	WPGMZA.OLPolygon.prototype = Object.create(Parent.prototype);
	WPGMZA.OLPolygon.prototype.constructor = WPGMZA.OLPolygon;

	WPGMZA.OLPolygon.prototype.getStyleFromSettings = function()
	{
		var params = {};
				
		if(this.linecolor && this.lineopacity)
			params.stroke = new ol.style.Stroke({
				color: WPGMZA.hexOpacityToRGBA("#" + this.linecolor, this.lineopacity)
			});
		
		if(this.opacity)
			params.fill = new ol.style.Fill({
				color: WPGMZA.hexOpacityToRGBA(this.fillcolor, this.opacity)
			});
			
		return params;
	}
	
	WPGMZA.OLPolygon.prototype.updateStyleFromSettings = function()
	{
		// Re-create the style - working on it directly doesn't cause a re-render
		var params = this.getStyleFromSettings();
		this.olStyle = new ol.style.Style(params);
		this.layer.setStyle(this.olStyle);
	}
	
	WPGMZA.OLPolygon.prototype.setEditable = function(editable)
	{
		
	}
	
	WPGMZA.OLPolygon.prototype.toJSON = function()
	{
		var result = Parent.prototype.toJSON.call(this);
		var coordinates = this.olFeature.getGeometry().getCoordinates()[0];
		
		result.points = [];
		
		for(var i = 0; i < coordinates.length; i++)
		{
			var lonLat = ol.proj.toLonLat(coordinates[i]);
			var latLng = {
				lat: lonLat[1],
				lng: lonLat[0]
			};
			result.points.push(latLng);
		}
		
		return result;
	}
	
});

// js/v8/pro-store-locator.js
/**
 * @namespace WPGMZA
 * @module ProStoreLocator
 * @requires WPGMZA.StoreLocator
 */
jQuery(function($) {
	
	WPGMZA.ProStoreLocator = function(map, element)
	{
		var self = this;
		
		WPGMZA.StoreLocator.call(this, map, element);
		
		// Initially disable buttons
		var buttons = $(element).find("input[type='button'], button:not(.wpgmza-use-my-location)");
		buttons.prop("disabled", true);
		map.on("markersplaced", function(event) {
			buttons.prop("disabled", false);
		});
		
		if(map.settings.store_locator_use_their_location == "1")
		{
			var input = $(this.element).find(".addressInput");
			var button = new WPGMZA.UseMyLocationButton(input);
			input.after(button.element);
		}
		
		if(map.settings.store_locator_search_area == WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO)
		{
			$(this.element).find(".wpgmza_sl_radius_select").remove();
		}
	}
	
	WPGMZA.ProStoreLocator.prototype = Object.create(WPGMZA.StoreLocator.prototype);
	WPGMZA.ProStoreLocator.prototype.constructor = WPGMZA.ProStoreLocator;
	
	WPGMZA.ProStoreLocator.SEARCH_AREA_RADIAL		= "radial";
	WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO			= "auto";
	
	WPGMZA.StoreLocator.createInstance = function(map, element)
	{
		return new WPGMZA.ProStoreLocator(map, element);
	}
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "keywords", {
		
		"get": function() {
			
			var legacy = $(".wpgmza_name_search_string + input").val();
			
			if(legacy)
				return legacy;
			
			var modern = $(this.map.element).find(".wpgmza-text-search").val();
			
			return modern;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "categories", {
		
		"configurable": true,
		
		"get": function() {
			var dropdown, checkboxes, value, results;
			
			var isModernStyle = $(this.map.element).find(".wpgmza-modern-store-locator").length > 0;
			
			if(isModernStyle)
			{
				$(this.map.element).find(".wpgmza-modern-store-locator [name='wpgmza_cat_checkbox']:checked").each(function(index, el) {
					
					if(!results)
						results = [];
					
					results.push( $(el).val() );
					
				});
			}
			else
			{
				if((dropdown = $(this.element).find(".wpgmza_sl_category_div > select")).length)
				{
					value = dropdown.val();
					
					if(value == "0")
						return null;
					
					return [value];
				}
				
				$(this.element).find(".wpgmza_sl_category_div :checked").each(function(index, el) {
					
					if(!results)
						results = [];
					
					results.push( $(el).val() );
					
				});
			}
			
			return results;
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "hideMarkersInInitialState", {
		
		"get": function() {
			
			return this.map.settings.store_locator_hide_before_search == 1;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleStrokeColor", {
		
		"get": function() {
			
			if(this.map.settings.sl_stroke_color)
				return "#" + this.map.settings.sl_stroke_color;
			
			return "#ff0000";
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleFillColor", {
		
		"get": function() {
			
			if(this.map.settings.sl_fill_color)
				return "#" + this.map.settings.sl_fill_color;
			
			return "#ff0000";
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleStrokeOpacity", {
		
		"get": function() {
			
			if(this.map.settings.sl_stroke_opacity !== undefined && this.map.settings.sl_stroke_opacity !== "")
				return parseFloat(this.map.settings.sl_stroke_opacity);
			
			return 0.25;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circleFillOpacity", {
		
		"get": function() {
			
			if(this.map.settings.sl_fill_opacity !== undefined && this.map.settings.sl_fill_opacity !== "")
				return parseFloat(this.map.settings.sl_fill_opacity);
			
			return 0.15;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "circle", {
		
		"get": function() {
			
			if(this.map.settings.store_locator_search_area == WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO)
				return null;
			
			if(this._circle)
				return this._circle;
			
			if(!WPGMZA.isDeviceiOS() && this.map.settings.wpgmza_store_locator_radius_style == "modern")
			{
				this._circle = WPGMZA.ModernStoreLocatorCircle.createInstance(this.map.id);
				this._circle.settings.color = this.circleStrokeColor;
			}
			else
			{
				this._circle = WPGMZA.Circle.createInstance({
					strokeColor:	this.circleStrokeColor,
					strokeOpacity:	this.circleStrokeOpacity,
					strokeWeight:	2,
					fillColor:		this.circleFillColor,
					fillOpacity:	this.circleFillOpacity,
					visible:		false,
					clickable:      false
				});
			}
			
			return this._circle;
			
		}
		
	});
	
	Object.defineProperty(WPGMZA.ProStoreLocator.prototype, "marker", {
		
		"get": function() {
			
			if(this.map.settings.store_locator_bounce != 1)
				return null;
			
			if(this._marker)
				return this._marker;
			
			var options = {
				visible: false
			};
			
			if(this.map.settings.upload_default_sl_marker && this.map.settings.upload_default_sl_marker.length)
				options.icon = this.map.settings.upload_default_sl_marker;
			
			this._marker = WPGMZA.Marker.createInstance(options);
			this._marker.disableInfoWindow = true;
			this._marker.isFilterable = false;
			
			if(this.map.settings.wpgmza_sl_animation == 1)
				this._marker.setAnimation(WPGMZA.Marker.ANIMATION_BOUNCE);
			else if(this.map.settings.wpgmza_sl_animation == 2)
				this._marker.setAnimation(WPGMZA.Marker.ANIMATION_DROP);
			
			return this._marker;
			
		}
		
	});
	
	WPGMZA.ProStoreLocator.prototype.getZoomFromRadius = function(radius)
	{
		if(this.distanceUnits == WPGMZA.Distance.MILES)
			radius *= WPGMZA.Distance.KILOMETERS_PER_MILE;
		
		return Math.round(14 - Math.log(radius) / Math.LN2);
	}
	
	WPGMZA.ProStoreLocator.prototype.getFilteringParameters = function()
	{
		if(this.state == WPGMZA.StoreLocator.STATE_INITIAL)
		{
			if(this.hideMarkersInInitialState)
			{
				return {
					hideAll: true
				};
			}
			
			return {};	// No search has been performed yet
		}
		
		var params = WPGMZA.StoreLocator.prototype.getFilteringParameters.call(this);
		var proParams = {};
		
		if(this.map.settings.store_locator_search_area == WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO)
		{
			delete params.center;
			delete params.radius;
		}
		
		if(this.keywords)
			proParams.keywords = this.keywords;
		
		if(this.categories)
			proParams.categories = this.categories;
		
		return $.extend(params, proParams);
	}
	
	WPGMZA.ProStoreLocator.prototype.onFilteringComplete = function(event)
	{
		var params = event.filteringParams;
		var circle = this.circle;
		var marker = this.marker;
		var factor = (this.distanceUnits == WPGMZA.Distance.MILES ? WPGMZA.Distance.KILOMETERS_PER_MILE : 1.0);
		
		if(!(event.source instanceof WPGMZA.StoreLocator))
			return;
		
		WPGMZA.StoreLocator.prototype.onFilteringComplete.apply(this, arguments);
		
		switch(this.map.settings.store_locator_search_area)
		{
			case WPGMZA.ProStoreLocator.SEARCH_AREA_AUTO:
			
				if(!this.bounds || this.bounds.isInInitialState())
				{
					this.map.setZoom(this.map.settings.map_start_zoom);
					/*this.map.setCenter(new WPGMZA.LatLng(
						this.map.settings.map_start_lat,
						this.map.settings.map_start_lng
					));*/
					
					break;
				}
			
				this.map.fitBounds(this.bounds);
				
				var maxZoom = this.map.settings.store_locator_auto_area_max_zoom;
				
				if(maxZoom && this.map.getZoom() >= maxZoom)
					this.map.setZoom(maxZoom);
			
				break;
			
			default:
			
				if(circle)
					circle.setVisible(false);
				
				if(params.center && params.radius)
				{
					// Focus on center and zoom
					this.map.setCenter(params.center);
					this.map.setZoom(this.getZoomFromRadius(params.radius));
					
					if(circle)
					{
						if(circle instanceof WPGMZA.ModernStoreLocatorCircle)
							circle.settings.radiusString = Math.round(params.radius);
						
						circle.setRadius(params.radius * factor);
						circle.setCenter(params.center);
						circle.setVisible(true);
						
						if(circle.map != this.map)
							this.map.addCircle(circle);
					}
					
					break;
				
			}
		}
		
		var storeLocatorResultEvent = {type: "storelocatorresult"};
		
		if(event.center)
			storeLocatorResultEvent.center = event.center;
		
		this.map.trigger(storeLocatorResultEvent);
	}
	
	WPGMZA.ProStoreLocator.prototype.onGeocodeComplete = function(event)
	{
		if(event.results && event.results.length)
		{
			var location = new WPGMZA.LatLng({
				lat: event.results[0].lat,
				lng: event.results[0].lng
			});
			
			location.source = WPGMZA.ProMap.SHOW_DISTANCE_FROM_SEARCHED_ADDRESS;
			
			this.map.showDistanceFromLocation = location;
		}
		
		WPGMZA.StoreLocator.prototype.onGeocodeComplete.apply(this, arguments);
	}
	
	WPGMZA.ProStoreLocator.prototype.onReset = function(event)
	{
		this.map.showDistanceFromLocation = this.map.userLocation;
		this.map.updateInfoWindowDistances();
		
		WPGMZA.StoreLocator.prototype.onReset.apply(this, arguments);
	}
	
	
});

// js/v8/queue.js
/**
 * @namespace WPGMZA
 * @module Queue
 * @requires WPGMZA
 */
jQuery(function($) {
	
	/*
	 * Adapted from, and with thanks to https://github.com/mourner/tinyqueue
	 */
	
	function defaultCompare(a, b) {
		return a < b ? -1 : a > b ? 1 : 0;
	}

	WPGMZA.Queue = function(data, compare)
	{
		if(!data)
			data = [];
		
		if(!compare)
			compare = defaultCompare;
		
		this.data = data;
		this.length = this.data.length;
		this.compare = compare;
		
		if(this.lenght > 0)
			for(var i = (this.length >> 1) - 1; i >= 0; i--)
				this._down(i);
	}

	WPGMZA.Queue.prototype.push = function(item)
	{
		this.data.push(item);
		this.length++;
		this._up(this.length - 1);
	}

	WPGMZA.Queue.prototype.pop = function()
	{
		if(this.length === 0)
			return undefined;
		
		var top = this.data[0];
		var bottom = this.data.pop();
		this.length--;
		
		if(this.length > 0)
		{
			this.data[0] = bottom;
			this._down(0);
		}
		
		return top;
	}

	WPGMZA.Queue.prototype.peek = function()
	{
		return this.data[0];
	}

	WPGMZA.Queue.prototype._up = function(pos)
	{
		var data = this.data;
		var compare = this.compare;
		var item = data[pos];
		
		while(pos > 0)
		{
			var parent = (pos - 1) >> 1;
			var current = data[parent];
			
			if(compare(item, current) >= 0)
				break;
			
			data[pos] = current;
			pos = parent;
		}
		
		data[pos] = item;
	}

	WPGMZA.Queue.prototype._down = function(pos)
	{
		var data = this.data;
		var compare = this.compare;
		var halfLength = this.length >> 1;
		var item = data[pos];
		
		while(pos < halfLength)
		{
			var left = (pos << 1) + 1;
			var best = data[left];
			var right = left + 1;
			
			if(right < this.length && compare(data[right], best) < 0)
			{
				left = right;
				best = data[right];
			}
			
			if(compare(best, item) >= 0)
				break;
			
			data[pos] = best;
			pos = left;
		}
		
		data[pos] = item;
	}
	
});

// js/v8/use-my-location-button.js
/**
 * @namespace WPGMZA
 * @module UseMyLocationButton
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.UseMyLocationButton = function(target, options)
	{
		var self = this;
		
		this.options = {};
		if(options)
			this.options = options;
		
		this.target = $(target);
		
		this.element = $("<button class='wpgmza-use-my-location' type='button' title='" + WPGMZA.localized_strings.use_my_location + "'><i class='fa fa-crosshairs' aria-hidden='true'></i></button>");
		this.element.on("click", function(event) {
			self.onClick(event);
		});
	}
	
	WPGMZA.UseMyLocationButton.prototype = Object.create(WPGMZA.EventDispatcher.prototype);
	WPGMZA.UseMyLocationButton.prototype.constructor = WPGMZA.UseMyLocationButton;
	
	WPGMZA.UseMyLocationButton.prototype.onClick = function(event)
	{
		var self = this;
		
		WPGMZA.getCurrentPosition(function(position) {
			
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			
			self.target.val(lat + ", " + lng);
			self.target.trigger("change");
			
			var geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.geocode({latLng: {lat: lat, lng: lng}}, function(results) {
				
				if(results && results.length)
					self.target.val(results[0]);
				
			});
			
		});
	}
	
});

// js/v8/modern-store-locator.js
/**
 * @namespace WPGMZA
 * @module ModernStoreLocator
 * @requires WPGMZA
 * @pro-requires WPGMZA.UseMyLocationButton
 */
jQuery(function($) {
	
	/**
	 * The new modern look store locator. It takes the elements from the default look and moves them into the map, wrapping in a new element so we can apply new styles. <strong>Please <em>do not</em> call this constructor directly. Always use createInstance rather than instantiating this class directly.</strong> Using createInstance allows this class to be externally extensible.
	 * @class WPGMZA.ModernStoreLocator
	 * @constructor WPGMZA.ModernStoreLocator
	 * @memberof WPGMZA
	 * @param {int} map_id The ID of the map this store locator belongs to
	 */
	WPGMZA.ModernStoreLocator = function(map_id)
	{
		var self = this;
		var original;
		var map = WPGMZA.getMapByID(map_id);
		
		WPGMZA.assertInstanceOf(this, "ModernStoreLocator");
		
		if(WPGMZA.isProVersion())
			original = $(".wpgmza_sl_search_button[mid='" + map_id + "'], .wpgmza_sl_search_button_" + map_id).closest(".wpgmza_sl_main_div");
		else
			original = $(".wpgmza_sl_search_button").closest(".wpgmza_sl_main_div");
		
		if(!original.length)
			return;
		
		// Build / re-arrange elements
		this.element = $("<div class='wpgmza-modern-store-locator'><div class='wpgmza-inner wpgmza-modern-hover-opaque'/></div>")[0];
		
		var inner = $(this.element).find(".wpgmza-inner");
		
		var addressInput;
		if(WPGMZA.isProVersion())
			addressInput = $(original).find(".addressInput");
		else
			addressInput = $(original).find("#addressInput");
		
		if(wpgmaps_localize[map_id].other_settings.store_locator_query_string && wpgmaps_localize[map_id].other_settings.store_locator_query_string.length)
			addressInput.attr("placeholder", wpgmaps_localize[map_id].other_settings.store_locator_query_string);
		
		inner.append(addressInput);
		
		var titleSearch = $(original).find("[id='nameInput_" + map_id + "']");
		if(titleSearch.length)
		{
			var placeholder = wpgmaps_localize[map_id].other_settings.store_locator_name_string;
			if(placeholder && placeholder.length)
				titleSearch.attr("placeholder", placeholder);
			inner.append(titleSearch);
		}
		
		var button;
		if(button = $(original).find("button.wpgmza-use-my-location"))
			inner.append(button);
		
		$(addressInput).on("keydown keypress", function(event) {
			
			if(event.keyCode == 13)
			{
				// NB: Hacky workaround
				self.searchButton.trigger("click");
				
				// NB: Legacy support
				searchLocations(map_id);
				
				map.storeLocator.onSearch(event);
			}
			
		});
		
		$(addressInput).on("input", function(event) {
			
			self.searchButton.show();
			self.resetButton.hide();
			
		});
		
		inner.append($(original).find("select.wpgmza_sl_radius_select"));
		// inner.append($(original).find(".wpgmza_filter_select_" + map_id));
		
		// Buttons
		this.searchButton = $(original).find( ".wpgmza_sl_search_button, .wpgmza_sl_search_button_div" );
		inner.append(this.searchButton);
		
		this.resetButton = $(original).find( ".wpgmza_sl_reset_button_div" );
		inner.append(this.resetButton);
		
		this.resetButton.on("click", function(event) {
			resetLocations(map_id);
		});
		
		this.resetButton.hide();
		
		if(WPGMZA.isProVersion())
		{
			this.searchButton.on("click", function(event) {
				if($("addressInput_" + map_id).val() == 0)
					return;
				
				self.searchButton.hide();
				self.resetButton.show();
				
				map.storeLocator.state = WPGMZA.StoreLocator.STATE_APPLIED;
			});
			this.resetButton.on("click", function(event) {
				self.resetButton.hide();
				self.searchButton.show();
				
				map.storeLocator.state = WPGMZA.StoreLocator.STATE_INITIAL;
			});
		}
		
		// Distance type
		inner.append($("#wpgmza_distance_type_" + map_id));
		
		// Categories
		var container = $(original).find(".wpgmza_cat_checkbox_holder");
		var ul = $(container).children("ul");
		var items = $(container).find("li");
		var numCategories = 0;
		
		//$(items).find("ul").remove();
		//$(ul).append(items);
		
		var icons = [];
		
		items.each(function(index, el) {
			var id = $(el).attr("class").match(/\d+/);
			
			for(var category_id in wpgmza_category_data) {
				
				if(id == category_id) {
					var src = wpgmza_category_data[category_id].image;
					var icon = $('<div class="wpgmza-chip-icon"/>');
					
					icon.css({
						"background-image": "url('" + src + "')",
						"width": $("#wpgmza_cat_checkbox_" + category_id + " + label").height() + "px"
					});
					icons.push(icon);
					
                    if(src != null && src != ""){
					   //$(el).find("label").prepend(icon);
                       $("#wpgmza_cat_checkbox_" + category_id + " + label").prepend(icon);
                    }
					
					numCategories++;
					
					break;
				}
				
			}
		});

        $(this.element).append(container);

		
		if(numCategories) {
			this.optionsButton = $('<span class="wpgmza_store_locator_options_button"><i class="fa fa-list"></i></span>');
			$(this.searchButton).before(this.optionsButton);
		}
		
		setInterval(function() {
			
			icons.forEach(function(icon) {
				var height = $(icon).height();
				$(icon).css({"width": height + "px"});
				$(icon).closest("label").css({"padding-left": height + 8 + "px"});
			});
			
			$(container).css("width", $(self.element).find(".wpgmza-inner").outerWidth() + "px");
			
		}, 1000);
		
		$(this.element).find(".wpgmza_store_locator_options_button").on("click", function(event) {
			
			if(container.hasClass("wpgmza-open"))
				container.removeClass("wpgmza-open");
			else
				container.addClass("wpgmza-open");
			
		});
		
		// Remove original element
		$(original).remove();
		
		// Event listeners
		$(this.element).find("input, select").on("focus", function() {
			$(inner).addClass("active");
		});
		
		$(this.element).find("input, select").on("blur", function() {
			$(inner).removeClass("active");
		});
		
		$(this.element).on("mouseover", "li.wpgmza_cat_checkbox_item_holder", function(event) {
			self.onMouseOverCategory(event);
		});
		
		$(this.element).on("mouseleave", "li.wpgmza_cat_checkbox_item_holder", function(event) {
			self.onMouseLeaveCategory(event);
		});
		
		$(map.markerFilter).on("filteringcomplete", function(event) {

			if(!this.map.hasVisibleMarkers())
			{
				if(this.map.settings.store_locator_not_found_message !=  WPGMZA.localized_strings.zero_results && this.map.settings.store_locator_not_found_message != "")
				{
					alert(this.map.settings.store_locator_not_found_message);

				}
				else{
					alert(WPGMZA.localized_strings.zero_results);
				}
			}

		});


		$('body').on('click', '.wpgmza_store_locator_options_button', function(event) {
			setTimeout(function(){

				if ($('.wpgmza_cat_checkbox_holder').hasClass('wpgmza-open')) {

					var p_cat = $( ".wpgmza_cat_checkbox_holder" );
					var position_cat = p_cat.position().top + p_cat.outerHeight(true) + $('.wpgmza-modern-store-locator').height();
			
					var $p_map = $('.wpgmza_map');  
					var position_map = $p_map.position().top + $p_map.outerHeight(true); 

					var cat_height = position_cat;

					if (cat_height >= position_map) {
			
						$('.wpgmza_cat_ul').css('overflow', 'scroll ');
					
						$('.wpgmza_cat_ul').css('height', '100%');
				
						$('.wpgmza-modern-store-locator').css('height','100%');
						$('.wpgmza_cat_checkbox_holder.wpgmza-open').css({'padding-bottom': '50px', 'height': '100%'});
					}
				}
			}, 500);
		});

	}
	
	/**
	 * Creates an instance of a modern store locator, <strong>please <em>always</em> use this function rather than calling the constructor directly</strong>.
	 * @method
	 * @memberof WPGMZA.ModernStoreLocator
	 * @param {int} map_id The ID of the map this store locator belongs to
	 * @return {WPGMZA.ModernStoreLocator} An instance of WPGMZA.ModernStoreLocator
	 */
	WPGMZA.ModernStoreLocator.createInstance = function(map_id)
	{
		switch(WPGMZA.settings.engine)
		{
			case "open-layers":
				return new WPGMZA.OLModernStoreLocator(map_id);
				break;
			
			default:
				return new WPGMZA.GoogleModernStoreLocator(map_id);
				break;
		}
	}
	
	// TODO: Move these to a Pro module
	WPGMZA.ModernStoreLocator.prototype.onMouseOverCategory = function(event)
	{
		var li = event.currentTarget;
		
		$(li).children("ul.wpgmza_cat_checkbox_item_holder").stop(true, false).fadeIn();
	}
	
	WPGMZA.ModernStoreLocator.prototype.onMouseLeaveCategory = function(event)
	{
		var li = event.currentTarget;
		
		$(li).children("ul.wpgmza_cat_checkbox_item_holder").stop(true, false).fadeOut();
	}
	
});

// js/v8/google-maps/google-modern-store-locator.js
/**
 * @namespace WPGMZA
 * @module GoogleModernStoreLocator
 * @requires WPGMZA.ModernStoreLocator
 */
jQuery(function($) {
	
	WPGMZA.GoogleModernStoreLocator = function(map_id)
	{
		var googleMap, self = this;
		
		this.map = WPGMZA.getMapByID(map_id);
		
		WPGMZA.ModernStoreLocator.call(this, map_id);

		var options = {
			fields: ["name", "formatted_address"],
			types: ["geocode"]
		};
		var restrict = wpgmaps_localize[map_id]["other_settings"]["wpgmza_store_locator_restrict"];
		
		this.addressInput = $(this.element).find(".addressInput, #addressInput")[0];
		
		if(this.addressInput)
		{
			if(restrict && restrict.length)
				options.componentRestrictions = {
					country: restrict
				};
			
			this.autoComplete = new google.maps.places.Autocomplete(
				this.addressInput,
				options
			);
		}
		
		// Positioning for Google
		this.map.googleMap.controls[google.maps.ControlPosition.TOP_CENTER].push(this.element);
	}
	
	WPGMZA.GoogleModernStoreLocator.prototype = Object.create(WPGMZA.ModernStoreLocator.prototype);
	WPGMZA.GoogleModernStoreLocator.prototype.constructor = WPGMZA.GoogleModernStoreLocator;
	
});

// js/v8/open-layers/ol-modern-store-locator.js
/**
 * @namespace WPGMZA
 * @module OLModernStoreLocator
 * @requires WPGMZA.ModernStoreLocator
 */
jQuery(function($) {
	
	WPGMZA.OLModernStoreLocator = function(map_id)
	{
		var element;
		
		WPGMZA.ModernStoreLocator.call(this, map_id);
		
		if(WPGMZA.isProVersion())
			element = $(".wpgmza_map[data-map-id='" + map_id + "']");
		else
			element = $("#wpgmza_map");
		
		element.append(this.element);
	}
	
	WPGMZA.OLModernStoreLocator.prototype = Object.create(WPGMZA.ModernStoreLocator);
	WPGMZA.OLModernStoreLocator.prototype.constructor = WPGMZA.OLModernStoreLocator;
	
});

// js/v8/xml-cache-converter.js
/**
 * @namespace WPGMZA
 * @module XMLCacheConverter
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.XMLCacheConverter = function()
	{
		
	}
	
	WPGMZA.XMLCacheConverter.prototype.convert = function(xml)
	{
		var markers = [];
		var remap = {
			"marker_id":	"id",
			"linkd":		"link"
		};
		
		$(xml).find("marker").each(function(index, el) {
			
			var data = {};
			
			$(el).children().each(function(j, child) {
				
				var key = child.nodeName;
				
				if(remap[key])
					key = remap[key];
				
				if(child.hasAttribute("data-json"))
					data[key] = JSON.parse($(child).text());
				else
					data[key] = $(child).text();
				
			});
			
			markers.push(data);
			
		});
		
		return markers;
	}
	
});

// js/v8/xml-parse-web-worker.js
/**
 * @namespace WPGMZA
 * @module XMLParseWebWorker
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.loadXMLAsWebWorker = function()
	{
		// tXml by Tobias Nickel
		/**
		 * @author: Tobias Nickel
		 * @created: 06.04.2015
		 * I needed a small xmlparser chat can be used in a worker.
		 */
		function tXml(a,d){function c(){for(var l=[];a[b];){if(60==a.charCodeAt(b)){if(47===a.charCodeAt(b+1)){b=a.indexOf(">",b);break}else if(33===a.charCodeAt(b+1)){if(45==a.charCodeAt(b+2)){for(;62!==a.charCodeAt(b)||45!=a.charCodeAt(b-1)||45!=a.charCodeAt(b-2)||-1==b;)b=a.indexOf(">",b+1);-1===b&&(b=a.length)}else for(b+=2;62!==a.charCodeAt(b);)b++;b++;continue}var c=f();l.push(c)}else c=b,b=a.indexOf("<",b)-1,-2===b&&(b=a.length),c=a.slice(c,b+1),0<c.trim().length&&l.push(c);b++}return l}function l(){for(var c=
		b;-1===g.indexOf(a[b]);)b++;return a.slice(c,b)}function f(){var d={};b++;d.tagName=l();for(var f=!1;62!==a.charCodeAt(b);){var e=a.charCodeAt(b);if(64<e&&91>e||96<e&&123>e){for(var g=l(),e=a.charCodeAt(b);39!==e&&34!==e&&!(64<e&&91>e||96<e&&123>e)&&62!==e;)b++,e=a.charCodeAt(b);f||(d.attributes={},f=!0);if(39===e||34===e){var e=a[b],h=++b;b=a.indexOf(e,h);e=a.slice(h,b)}else e=null,b--;d.attributes[g]=e}b++}47!==a.charCodeAt(b-1)&&("script"==d.tagName?(f=b+1,b=a.indexOf("\x3c/script>",b),d.children=
		[a.slice(f,b-1)],b+=8):"style"==d.tagName?(f=b+1,b=a.indexOf("</style>",b),d.children=[a.slice(f,b-1)],b+=7):-1==k.indexOf(d.tagName)&&(b++,d.children=c(g)));return d}d=d||{};var g="\n\t>/= ",k=["img","br","input","meta","link"],h=null;if(d.searchId){var b=(new RegExp("s*ids*=s*['\"]"+d.searchId+"['\"]")).exec(a).index;-1!==b&&(b=a.lastIndexOf("<",b),-1!==b&&(h=f()));return b}b=0;h=c();d.filter&&(h=tXml.filter(h,d.filter));d.simplify&&(h=tXml.simplefy(h));return h}
		tXml.simplify=function(a){var d={};if(1===a.length&&"string"==typeof a[0])return a[0];a.forEach(function(a){d[a.tagName]||(d[a.tagName]=[]);if("object"==typeof a){var c=tXml.simplefy(a.children);d[a.tagName].push(c);a.attributes&&(c._attributes=a.attributes)}else d[a.tagName].push(a)});for(var c in d)1==d[c].length&&(d[c]=d[c][0]);return d};tXml.filter=function(a,d){var c=[];a.forEach(function(a){"object"===typeof a&&d(a)&&c.push(a);a.children&&(a=tXml.filter(a.children,d),c=c.concat(a))});return c};
		tXml.domToXml=function(a){function d(a){if(a)for(var f=0;f<a.length;f++)if("string"==typeof a[f])c+=a[f].trim();else{var g=a[f];c+="<"+g.tagName;var k=void 0;for(k in g.attributes)c=-1===g.attributes[k].indexOf('"')?c+(" "+k+'="'+g.attributes[k].trim()+'"'):c+(" "+k+"='"+g.attributes[k].trim()+"'");c+=">";d(g.children);c+="</"+g.tagName+">"}}var c="";d(O);return c};"object"!==typeof window&&(module.exports=tXml);
		
		var worker = self;
		var inputData;
		var dataForMainThread = [];
		var filesLoaded = 0;
		var totalFiles;
		
		function onXMLLoaded(request)
		{
			if(request.readyState != 4 || request.status != 200)
				return;
			
			var start	= new Date().getTime();
			var xml		= tXml(request.responseText);
			
			convertAndAppend(xml);
			
			if(++filesLoaded >= totalFiles)
			{
				worker.postMessage(dataForMainThread);
				return;
			}
			
			loadNextFile();
		}
		
		function convertAndAppend(xml)
		{
			var root	= xml[0];
			var markers	= root.children[0];
			var json	= [];
			var remap	= {
				"marker_id":	"id",
				"linkd":		"link"
			};
			
			for(var i = 0; i < markers.children.length; i++)
			{
				var data = {};
				
				markers.children[i].children.forEach(function(node) {
					
					var key = node.tagName;
					
					if(remap[key])
						key = remap[key];
					
					if(node.attributes["data-json"])
						data[key] = JSON.parse(node.children[0]);
					else
					{
						if(node.children.length)
							data[key] = node.children[0];
						else
							data[key] = "";
					}
					
				});
				
				dataForMainThread.push(data);
			}
		}
		
		function loadNextFile()
		{
			var url = inputData.urls[filesLoaded];
			var request = new XMLHttpRequest();
			
			request.onreadystatechange = function() {
				onXMLLoaded(this);
			};
			
			request.open("GET", inputData.protocol + url, true);
			request.send();
		}
		
		self.addEventListener("message", function(event) {
			
			var data = event.data;
			
			switch(data.command)
			{
				case "load":
				
					inputData = data;
					dataForMainThread = [];
					filesLoaded = 0;
					totalFiles = data.urls.length;
					
					loadNextFile();
					
					break;
				
				default:
					throw new Error("Unknown command");
					break;
			}
			
		}, false);
		
	}
	
});

// js/v8/3rd-party-integration/gutenberg/dist/pro-gutenberg.js
"use strict";

/**
 * @namespace WPGMZA.Integration
 * @module ProGutenberg
 * @requires WPGMZA.Gutenberg
 */

/**
 * Internal block libraries
 */
jQuery(function ($) {

	if (!window.wp || !wp.i18n || !wp.blocks || !wp.editor || !wp.components) return;

	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var _wp$editor = wp.editor,
	    InspectorControls = _wp$editor.InspectorControls,
	    BlockControls = _wp$editor.BlockControls;
	var _wp$components = wp.components,
	    Dashicon = _wp$components.Dashicon,
	    Toolbar = _wp$components.Toolbar,
	    Button = _wp$components.Button,
	    Tooltip = _wp$components.Tooltip,
	    PanelBody = _wp$components.PanelBody,
	    TextareaControl = _wp$components.TextareaControl,
	    TextControl = _wp$components.TextControl,
	    RichText = _wp$components.RichText,
	    SelectControl = _wp$components.SelectControl,
	    RangeControl = _wp$components.RangeControl;


	WPGMZA.Integration.ProGutenberg = function () {
		WPGMZA.Integration.Gutenberg.call(this);
	};

	WPGMZA.Integration.ProGutenberg.prototype = Object.create(WPGMZA.Integration.Gutenberg.prototype);
	WPGMZA.Integration.ProGutenberg.prototype.constructor = WPGMZA.Integration.ProGutenberg;

	WPGMZA.Integration.Gutenberg.getConstructor = function () {
		return WPGMZA.Integration.ProGutenberg;
	};

	WPGMZA.Integration.ProGutenberg.prototype.getMapSelectOptions = function () {
		var result = [];

		WPGMZA.gutenbergData.maps.forEach(function (el) {

			result.push({
				key: el.id,
				value: el.id,
				label: el.map_title + " (" + el.id + ")"
			});
		});

		return result;
	};

	WPGMZA.Integration.ProGutenberg.prototype.updateMarkerSelectOptions = function (props) {
		var select = $("select[name='marker']");
		var mashup_ids = $("select[name='mashup_ids']").val();
		var none = $("<option value='none'></option>");
		var request = {
			fields: ["id", "address", "title"],
			filter: {
				map_id: $("select[name='map_id']").val()
			}
		};

		none.text(__("None"));

		if (mashup_ids) request.filter.mashup_ids = mashup_ids;

		select.prop("disabled", true);

		WPGMZA.restAPI.call("/markers/", {
			success: function success(response, status, xhr) {

				select.html("");
				select.append(none);

				response.forEach(function (data) {

					var option = $("<option/>");

					option.val(data.id);
					option.prop("value", data.id);
					option.text((data.title.length ? data.title : data.address) + " (" + data.id + ")");

					select.append(option);
				});

				select.prop("disabled", false);

				if (props.attributes.marker) select.val(props.attributes.marker);
			},
			data: request
		});
	};

	WPGMZA.Integration.ProGutenberg.prototype.updateCategorySelectOptions = function (props) {
		var select = $("select[name='cat']");
		var none = $("<option value='none'></option>");
		var request = {
			filter: {
				map_id: $("select[name='map_id']").val()
			}
		};

		none.text(__("None"));

		select.prop("disabled", true);

		function addNodeChildren(node, depth) {
			if (!depth) depth = 0;

			if (!node.children) return;

			node.children.forEach(function (child) {

				var prefix = "";
				var option = $("<option/>");

				for (var i = 0; i < depth; i++) {
					prefix += "&nbsp;&nbsp;&nbsp;&nbsp;";
				}option.val(child.id);
				option.prop(child.id);
				option.html(prefix + child.name + " (" + child.id + ")");

				select.append(option);

				addNodeChildren(child, depth + 1);
			});
		}

		WPGMZA.restAPI.call("/categories/", {
			success: function success(response, status, xhr) {

				select.html("");
				select.append(none);

				addNodeChildren(response);

				select.prop("disabled", false);

				if (props.attributes.cat) select.val(props.attributes.cat);
			},
			data: request
		});
	};

	WPGMZA.Integration.ProGutenberg.prototype.getBlockInspectorControls = function (props) {
		var self = this;

		var onChangeMap = function onChangeMap(value) {
			props.setAttributes({ id: value });
		};

		var onChangeMashupIDs = function onChangeMashupIDs(value) {
			props.setAttributes({ mashup_ids: value });
		};

		var onResetMashupIDs = function onResetMashupIDs(value) {
			$("select[name='mashup_ids']").val(null);
			props.setAttributes({ mashup_ids: [] });
		};

		var onEditMap = function onEditMap(event) {

			var select = $("select[name='map_id']");
			var map_id = select.val();

			window.open(WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu&action=edit&map_id=" + map_id);

			event.preventDefault();
			return false;
		};

		var onChangeFocusedMarker = function onChangeFocusedMarker(value) {
			props.setAttributes({ marker: value });
		};

		var onChangeOverrideZoom = function onChangeOverrideZoom(value) {
			props.setAttributes({ zoom: value });
		};

		var onResetOverrideZoom = function onResetOverrideZoom(event) {
			props.setAttributes({ zoom: "" });
		};

		var onChangeInitialCategory = function onChangeInitialCategory(value) {
			props.setAttributes({ cat: value });
		};

		var selectedMapID = "1";

		if (props.attributes.id) selectedMapID = props.attributes.id;else if (WPGMZA.gutenbergData.maps.length) selectedMapID = WPGMZA.gutenbergData.maps[0].id;

		setTimeout(function () {
			self.updateMarkerSelectOptions(props);
			self.updateCategorySelectOptions(props);
		}, 100);

		return React.createElement(
			InspectorControls,
			{ key: "inspector" },
			React.createElement(
				PanelBody,
				{ title: __('Map Settings') },
				React.createElement(SelectControl, {
					name: "map_id",
					label: __("Map"),
					value: selectedMapID,
					options: this.getMapSelectOptions(),
					onChange: onChangeMap
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"a",
						{ href: WPGMZA.adminurl + "admin.php?page=wp-google-maps-menu",
							onClick: onEditMap,
							target: "_blank",
							className: "button button-primary" },
						React.createElement("i", { className: "fa fa-pencil-square-o", "aria-hidden": "true" }),
						__('Go to Map Editor')
					)
				),
				React.createElement(SelectControl, {
					name: "mashup_ids",
					label: __("Mashup IDs"),
					value: props.attributes.mashup_ids || [],
					options: this.getMapSelectOptions(),
					multiple: true,
					onChange: onChangeMashupIDs
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"button",
						{ className: "button button-primary", onClick: onResetMashupIDs },
						React.createElement("i", { className: "fa fa-times", "aria-hidden": "true" }),
						__('Reset Mashup IDs')
					)
				),
				React.createElement(SelectControl, {
					name: "marker",
					label: __("Focused Marker"),
					value: "none",
					options: [{
						key: "none",
						value: "none",
						label: __("None")
					}],
					onChange: onChangeFocusedMarker
				}),
				React.createElement(RangeControl, {
					name: "zoom",
					label: __("Override Zoom"),
					onChange: onChangeOverrideZoom,
					min: 1,
					max: 21,
					step: 1,
					value: parseInt(props.attributes.zoom)
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"button",
						{ className: "button button-primary", onClick: onResetOverrideZoom },
						React.createElement("i", { className: "fa fa-times", "aria-hidden": "true" }),
						__('Reset Override Zoom')
					)
				),
				React.createElement(SelectControl, {
					name: "cat",
					label: __("Initial Category"),
					value: "none",
					options: [{
						key: "none",
						value: "none",
						label: __("None")
					}],
					onChange: onChangeInitialCategory
				}),
				React.createElement(
					"p",
					{ className: "map-block-gutenberg-button-container" },
					React.createElement(
						"a",
						{ href: "https://www.wpgmaps.com/documentation/creating-your-first-map/",
							target: "_blank",
							className: "button button-primary" },
						React.createElement("i", { className: "fa fa-book", "aria-hidden": "true" }),
						__('View Documentation')
					)
				)
			)
		);
	};

	WPGMZA.Integration.ProGutenberg.prototype.getBlockAttributes = function (props) {
		return {
			"id": {
				type: "string"
			},
			"mashup_ids": {
				type: "array"
			},
			"marker": {
				type: "string"
			},
			"zoom": {
				type: "string"
			},
			"cat": {
				type: "string"
			}
		};
	};

	WPGMZA.Integration.ProGutenberg.prototype.getBlockDefinition = function (props) {
		var definition = WPGMZA.Integration.Gutenberg.prototype.getBlockDefinition.call(this, props);

		return definition;
	};

	WPGMZA.integrationModules.gutenberg = WPGMZA.Integration.Gutenberg.createInstance();
});

// js/v8/google-maps/google-directions-renderer.js
/**
 * @namespace WPGMZA
 * @module GoogleDirectionsRenderer
 * @requires WPGMZA.DirectionsRenderer
 */
jQuery(function($) {
	
	WPGMZA.GoogleDirectionsRenderer = function(map)
	{
		WPGMZA.DirectionsRenderer.apply(this, arguments);
		
		this.map = map;
		
		this.googleDirectionsDisplay = new google.maps.DirectionsRenderer({
			map: map.googleMap,
			preserveViewport: true,
			draggable: true,
			suppressMarkers: true,
		});
		
		this.googleDirectionsDisplay.setPanel($("#directions_panel_" + map.id)[0]);
	}


	WPGMZA.extend(WPGMZA.GoogleDirectionsRenderer, WPGMZA.DirectionsRenderer);
	
	WPGMZA.GoogleDirectionsRenderer.prototype.setDirections = function(directions)
	{
		this.googleDirectionsDisplay.setDirections(directions.originalResponse);

		if(directions.routes && directions.routes[0] && directions.routes[0].legs && directions.routes[0].legs[0])
    {

			this.directionLeg = directions.routes[0].legs[0];

			this.directionStartMarker = WPGMZA.Marker.createInstance({
				position: new WPGMZA.LatLng( this.directionLeg.start_location.lat(), this.directionLeg.start_location.lng() ),
				icon: this.map.settings.directions_route_origin_icon,
				disableInfoWindow: true
			});

			this.map.addMarker(this.directionStartMarker);

			this.directionEndMarker = WPGMZA.Marker.createInstance({
				position: new WPGMZA.LatLng( this.directionLeg.end_location.lat(), this.directionLeg.end_location.lng() ),
				icon: this.map.settings.directions_route_destination_icon,
				disableInfoWindow: true
			});

			this.map.addMarker(this.directionEndMarker);
		}
		
		var options = {
			polylineOptions: {
			   strokeColor: "#4285F4"
			}
		};

		if(this.map.settings.directions_route_stroke_color)
			options.polylineOptions.strokeColor = this.map.settings.directions_route_stroke_color;

		if(this.map.settings.directions_route_stroke_weight)
			options.polylineOptions.strokeWeight = parseFloat(this.map.settings.directions_route_stroke_weight);
   
		if(this.map.settings.directions_route_stroke_opacity)
			options.polylineOptions.strokeOpacity = parseFloat(this.map.settings.directions_route_stroke_opacity);
	   
		this.googleDirectionsDisplay.setMap(this.map.googleMap);
		this.googleDirectionsDisplay.setOptions(options);
	}

	WPGMZA.GoogleDirectionsRenderer.prototype.clear = function()
    {	

		this.googleDirectionsDisplay.setMap(null);
    
        if (this.directionStartMarker)
          this.map.removeMarker(this.directionStartMarker);

        if (this.directionEndMarker)
          this.map.removeMarker(this.directionEndMarker);

    }
	
});



// js/v8/google-maps/google-directions-service.js
/**
 * @namespace WPGMZA
 * @module GoogleDirectionsService
 * @requires WPGMZA.DirectionsService
 */
jQuery(function($) {
	
	WPGMZA.GoogleDirectionsService = function(map)
	{
		WPGMZA.DirectionsService.apply(this, arguments);
		
		this.googleDirectionsService = new google.maps.DirectionsService();
	}
	
	WPGMZA.extend(WPGMZA.GoogleDirectionsService, WPGMZA.DirectionsService);
	
	WPGMZA.GoogleDirectionsService.prototype.route = function(request, callback)
	{
		var self = this;
		
		request.travelMode = request.travelMode.toUpperCase();
		
		this.googleDirectionsService.route(request, function(response) {
			
			var status;
			
			response.originalResponse = $.extend({}, response);
			
			switch(response.status)
			{
				case google.maps.DirectionsStatus.OK:
					status = WPGMZA.DirectionsService.SUCCESS;
					break;
					
				case google.maps.DirectionsStatus.ZERO_RESULTS:
					status = WPGMZA.DirectionsService.ZERO_RESULTS;
					break;
					
				case google.maps.DirectionsStatus.NOT_FOUND:
					status = WPGMZA.DirectionsService.NOT_FOUND;
					break;
				
				default:
					console.warn("Failed to get directions from Google: " + response.status);
					return;
					break;
			}
			
			callback(response, status);
			
			var event = new WPGMZA.Event({
				type: "directionsserviceresult",
				response: response,
				status: status
			});
			
			self.map.trigger(event);
			
		});
	}
	
});

// js/v8/google-maps/google-heatmap.js
/**
 * @namespace WPGMZA
 * @module GoogleHeatmap
 * @requires WPGMZA.Heatmap
 */
jQuery(function($) {
	
	WPGMZA.GoogleHeatmap = function(row)
	{
		WPGMZA.Heatmap.call(this, row);
		
		if(!google.maps.visualization)
		{
			console.warn("Heatmaps disabled. You must include the visualization library in the Google Maps API");
			return;
		}
		
		this.googleHeatmap = new google.maps.visualization.HeatmapLayer(this.settings);
		
		this.updateGoogleHeatmap();
	}
	
	WPGMZA.GoogleHeatmap.prototype = Object.create(WPGMZA.Heatmap.prototype);
	WPGMZA.GoogleHeatmap.prototype.constructor = WPGMZA.GoogleHeatmap;
	
	WPGMZA.GoogleHeatmap.prototype.updateGoogleHeatmap = function()
	{
		var points = this.points;
		var len = points.length;
		var data = [];
		
		// TODO: There are optimizations that could be made here, instead of regenerating the entire array and calling new google.maps.LatLng for each point, it would be better to keep an array and splice it
		
		for(var i = 0; i < len; i++)
			data.push(
				new google.maps.LatLng(
					points[i].lat, 
					points[i].lng
				)
			);
		
		this.googleHeatmap.setData(data);
		
		if(this.map)
			this.googleHeatmap.setMap(this.map.googleMap);
	}
	
	WPGMZA.GoogleHeatmap.prototype.addPoint = function(latLng)
	{
		WPGMZA.Heatmap.prototype.addPoint.call(this, latLng);
		
		this.updateGoogleHeatmap();
	}
	
	WPGMZA.GoogleHeatmap.prototype.removePoint = function(latLng)
	{
		WPGMZA.Heatmap.prototype.removePoint.call(this, latLng);
		
		this.updateGoogleHeatmap();
	}
	
});

// js/v8/google-maps/google-pro-info-window.js
/**
 * @namespace WPGMZA
 * @module GoogleProInfoWindow
 * @requires WPGMZA.GoogleInfoWindow
 */
jQuery(function($) {

	WPGMZA.GoogleProInfoWindow = function(mapObject)
	{
		WPGMZA.GoogleInfoWindow.call(this, mapObject);
	}
	
	WPGMZA.GoogleProInfoWindow.prototype = Object.create(WPGMZA.GoogleInfoWindow.prototype);
	WPGMZA.GoogleProInfoWindow.prototype.constructor = WPGMZA.GoogleProInfoWindow;

	WPGMZA.GoogleProInfoWindow.prototype.open = function(map, mapObject)
	{
		this.mapObject = mapObject;
		
		var style = (WPGMZA.currentPage == "map-edit" ? WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE : this.style);
		
		switch(style)
		{
			case WPGMZA.ProInfoWindow.STYLE_MODERN:
			case WPGMZA.ProInfoWindow.STYLE_MODERN_PLUS:
			case WPGMZA.ProInfoWindow.STYLE_MODERN_CIRCULAR:
			case WPGMZA.ProInfoWindow.STYLE_TEMPLATE:
				return WPGMZA.ProInfoWindow.prototype.open.call(this, map, mapObject);
				break;
			
			default:
				var result = WPGMZA.GoogleInfoWindow.prototype.open.call(this, map, mapObject);
				
				if(this.maxWidth && this.googleInfoWindow) // There will be no Google InfoWindow with Modern style marker listing selected
					this.googleInfoWindow.setOptions({maxWidth: this.maxWidth});
				
				return result;
				break;
		}
	}
		
});

// js/v8/google-maps/google-pro-map.js
/**
 * @namespace WPGMZA
 * @module GoogleProMap
 * @requires WPGMZA.GoogleMap
 */
jQuery(function($) {
	WPGMZA.GoogleProMap = function(element, options)
	{
		WPGMZA.GoogleMap.call(this, element, options);
		
		// Load KML layers
		this.loadKMLLayers();
		
		// Dispatch event
		this.trigger("init");
		
		this.dispatchEvent("created");
		WPGMZA.events.dispatchEvent({type: "mapcreated", map: this});
	}
	
	WPGMZA.GoogleProMap.prototype = Object.create(WPGMZA.GoogleMap.prototype);
	WPGMZA.GoogleProMap.prototype.constructor = WPGMZA.GoogleProMap.prototype;
	
	WPGMZA.GoogleProMap.prototype.addHeatmap = function(heatmap)
	{
		heatmap.googleHeatmap.setMap(this.googleMap);
		
		WPGMZA.ProMap.prototype.addHeatmap.call(this, heatmap);
	}
	
	/**
	 * Loads KML/GeoRSS layers
	 * @return void
	 */
	WPGMZA.GoogleProMap.prototype.loadKMLLayers = function()
	{
		// Remove old layers
		if(this.kmlLayers)
		{
			for(var i = 0; i < this.kmlLayers.length; i++)
				this.kmlLayers[i].setMap(null);
		}
		
		this.kmlLayers = [];
		
		if(!this.settings.kml)
			return;
		
		// Add layers
		var urls = this.settings.kml.split(",");
		var cachebuster = new Date().getTime();
		
		for(var i = 0; i < urls.length; i++)
		{
			this.kmlLayers.push(
				new google.maps.KmlLayer(urls[i] + "?cachebuster=" + cachebuster,
					{
						map: this.googleMap,
						preserveViewport: true
					}
				)
			);
		}
	}
	
	WPGMZA.GoogleProMap.prototype.loadFusionTableLayer = function() 
	{
		if(!this.settings.fusion)
			return;
		
		console.warn("Fusion Table Layers are deprecated and will cease functioning from 2019/12/03");
		
		this.fusionLayer = new google.maps.FusionTablesLayer(this.settings.fusion, {
			map: this.googleMap,
			surpressInfoWindows: true
		});
	}
	
	WPGMZA.GoogleProMap.prototype.setStreetView = function(options)
	{
		var latLng = this.getCenter();
		
		if(!options)
			options = {
				bearing: 0,
				pitch: 10
			};
		
		if("marker" in options && (marker = this.getMarkerByID(options.marker)))
		{
			latLng = marker.getPosition().toLatLngLiteral();
		}
		else if(("lat" in options) && ("lng" in options))
		{
			latLng = {
				lat: parseFloat(options.lat),
				lng: parseFloat(options.lng)
			};
		}
		
		if("bearing" in options)
		{
			options.bearing = parseInt(options.bearing);
			
			if(isNaN(options.bearing))
				console.warn("Invalid bearing");
		}
		
		if("pitch" in options)
		{
			options.pitch = parseInt(options.pitch);
			
			if(isNaN(options.pitch))
				console.warn("Invalid pitch");
		}
		
		this.panorama = new google.maps.StreetViewPanorama(
			this.element,
			{
				position: latLng,
				pov: {
					heading: parseInt(options.bearing),
					pitch: parseInt(options.pitch)
				}
			}
		);
	}
	
	WPGMZA.GoogleProMap.prototype.onInit = function(event)
	{
		WPGMZA.GoogleMap.prototype.onInit.call(this, event);
		
		if(this.shortcodeAttributes.streetview && !this.shortcodeAttributes.marker)
			this.setStreetView(this.shortcodeAttributes);
	}
	
	WPGMZA.GoogleProMap.prototype.onMarkersPlaced = function(event) 
	{
		WPGMZA.GoogleMap.prototype.onMarkersPlaced.call(this, event);
		
		if(this.shortcodeAttributes.streetview && this.shortcodeAttributes.marker)
			this.setStreetView(this.shortcodeAttributes);
	}
	
});

// js/v8/google-maps/google-pro-marker.js
/**
 * @namespace WPGMZA
 * @module GoogleProMarker
 * @requires WPGMZA.GoogleMarker
 */
jQuery(function($) {
	
	WPGMZA.GoogleProMarker = function(row)
	{
		WPGMZA.GoogleMarker.call(this, row);
	}
	
	WPGMZA.GoogleProMarker.prototype = Object.create(WPGMZA.GoogleMarker.prototype);
	WPGMZA.GoogleProMarker.prototype.constructor = WPGMZA.GoogleProMarker;
	
	WPGMZA.GoogleProMarker.prototype.onAdded = function(event)
	{
		WPGMZA.GoogleMarker.prototype.onAdded.apply(this, arguments);
		
		if(this.map.settings.wpgmza_settings_disable_infowindows)
			this.googleMarker.setOptions({clickable: false});
	}
	
	WPGMZA.GoogleProMarker.prototype.updateIcon = function()
	{
		var self = this;
		var icon = this._icon;
		
		if(icon.retina)
		{
			var img = new Image();
			
			img.onload = function(event) {
				
				var autoDetect = false;
				
				//var isSVG = icon.match(/\.svg/i);
				
				var size;
				
				if(!autoDetect)
					size = new google.maps.Size(
						WPGMZA.settings.retinaWidth ? parseInt(WPGMZA.settings.retinaWidth) : Math.round(img.width / 2),
						WPGMZA.settings.retinaHeight ? parseInt(WPGMZA.settings.retinaHeight) : Math.round(img.height / 2)
					);
				else
					size = new google.maps.Size(
						Math.round(img.width / 2),
						Math.round(img.height / 2)
					);
				
					
				self.googleMarker.setIcon(
					new google.maps.MarkerImage(icon.url, null, null, null, size)
				);
				
			};
			
			img.src = (icon.isDefault ? WPGMZA.defaultMarkerIcon : icon.url);
		}
		else
			this.googleMarker.setIcon(icon.url);
	}
	
});

// js/v8/google-maps/google-pro-polygon.js
/**
 * @namespace WPGMZA
 * @module GoogleProPolygon
 * @requires WPGMZA.GooglePolygon
 */
jQuery(function($) {
	
	WPGMZA.GoogleProPolygon = function(row, googlePolygon)
	{
		var self = this;
		
		WPGMZA.GooglePolygon.call(this, row, googlePolygon);
		
		google.maps.event.addListener(this.googlePolygon, "mouseover", function(event) {
			self.onMouseOver(event);
		});
		
		google.maps.event.addListener(this.googlePolygon, "mouseout", function(event) {
			self.onMouseOut(event);
		});
	}
	
	WPGMZA.GoogleProPolygon.prototype = Object.create(WPGMZA.GooglePolygon.prototype);
	WPGMZA.GoogleProPolygon.prototype.constructor = WPGMZA.GoogleProPolygon;
	
	/**
	 * Called when the user hovers their cursor over the polygon
	 * @return void
	 */
	WPGMZA.GoogleProPolygon.prototype.onMouseOver = function(event)
	{
		var options = {};
		
		// Check all these properties to see if they're empty first, so that we don't end up making the polygon black when no values are specified
		if(this.settings.hoverFillColor && this.settings.hoverFillColor.length)
			options.fillColor = this.settings.hoverFillColor;
		
		if(this.settings.hoverOpacity && this.settings.hoverOpacity.length)
			options.fillOpacity = this.settings.hoverOpacity;
		
		if(this.settings.hoverStrokeColor && this.settings.hoverStrokeColor.length)
			options.strokeColor = this.settings.hoverStrokeColor;
		
		this.googlePolygon.setOptions(options);
	}
	
	/**
	 * Called when the user hovers their cursor over the polygon
	 * @return void
	 */
	WPGMZA.GoogleProPolygon.prototype.onMouseOut = function(event)
	{
		this.googlePolygon.setOptions(this.settings);
	}
	
});

// js/v8/marker-listings/advanced-table-datatable.js
/**
 * @namespace WPGMZA
 * @module AdvancedTableDataTable
 * @requires WPGMZA.DataTable
 */
jQuery(function($) {
	
	WPGMZA.AdvancedTableDataTable = function(element, listing)
	{
		var self = this;
		
		this.element = element;
		this.listing = listing;
		
		WPGMZA.DataTable.apply(this, arguments);
		
		this.overrideListingOrderSettings = false;
		
		$(this.dataTableElement).on("click", "th", function(event) {
			
			self.onUserChangedOrder(event);
			
		});
	}
	
	WPGMZA.AdvancedTableDataTable.prototype = Object.create(WPGMZA.DataTable.prototype);
	WPGMZA.AdvancedTableDataTable.prototype.constructor = WPGMZA.AdvancedTableDataTable;
	
	WPGMZA.AdvancedTableDataTable.prototype.getDataTableSettings = function()
	{
		var self = this;
		var options = WPGMZA.DataTable.prototype.getDataTableSettings.apply(this, arguments);
		var json;
		
		if(json = $(this.element).attr("data-order-json"))
			options.order = JSON.parse(json);
		
		options.drawCallback = function(settings) {
			
			var ths = $(self.element).find(".wpgmza_table > thead th");
			
			if(!self.lastResponse || !self.lastResponse.meta)
				return; // Not ready yet
			
			if(self.lastResponse.meta.length == 0)
			{
				self.map.markerListing.trigger("markerlistingupdated");
				return; // No results
			}
			
			$(self.element).find(".wpgmza_table > tbody > tr").each(function(index, tr) {
				
				var meta = self.lastResponse.meta[index];
				
				$(tr).addClass("wpgmaps_mlist_row");
				$(tr).attr("mid", meta.id);
				$(tr).attr("mapid", self.map.id);
				
				$(tr).children("td").each(function(col, td) {
					
					var wpgmza_class = ths[col].className.match(/wpgmza_\w+/)[0];
					$(td).addClass(wpgmza_class);
					
				});
				
			});
			
			$(self.element).find("[data-marker-icon-src]").each(function(index, el) {
				
				var data;
				var src = $(el).attr("data-marker-icon-src");
				
				try{
					data = JSON.parse( src );
				}catch(e) {
					data = src;
				}
				
				var icon = WPGMZA.MarkerIcon.createInstance(data);
				
				icon.applyToElement(el);
				
			});
			
			
			self.map.markerListing.trigger("markerlistingupdated");
		};
		
		return options;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onAJAXRequest = function(data, settings)
	{
		var request;
		var listingParams			= this.listing.getAJAXRequestParameters().data;
		var listingFilteringParams	= listingParams.filteringParams;
		var overrideMarkerIDs		= listingParams.overrideMarkerIDs;
		
		delete listingParams.filteringParams;
		delete listingParams.overrideMarkerIDs;
		
		request = $.extend(
			{},
			listingParams,
			WPGMZA.DataTable.prototype.onAJAXRequest.apply(this, arguments)
		);
		
		request.filteringParams = $.extend(
			{},
			listingFilteringParams,
			this.filteringParams
		);
		
		if(this.filteredMarkerIDs)
			request.markerIDs = this.filteredMarkerIDs.join(",");
		
		//if(this.filteringParams)
			//request.filteringParams = this.filteringParams;
		
		if(this.overrideListingOrderSettings !== undefined)
			request.overrideListingOrderSettings = this.overrideListingOrderSettings;
		
		return request;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onMarkerFilterFilteringComplete = function(event)
	{
		var self = this;
		
		this.filteredMarkerIDs = [];
		
		event.filteredMarkers.forEach(function(data) {
			self.filteredMarkerIDs.push(data.id);
		});
		
		self.filteringParams = event.filteringParams;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onUserChangedOrder = function(event)
	{
		this.overrideListingOrderSettings = true;
	}
	
});

// js/v8/marker-listings/marker-listing.js
/**
 * @namespace WPGMZA
 * @module MarkerListing
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.MarkerListing = function(map, element, options)
	{
		var self = this;
		
		WPGMZA.EventDispatcher.apply(this);
		
		this._paginationEnabled = true;
		
		this.map = this.parent = map;
		this.element = element;
		
		if(this.element)
		{
			this.element.wpgmzaMarkerListing = this;
		}
		else if(WPGMZA.isDeveloperMode())
		{
			console.warn("Marker listing initialised with null element. This is presently supported to allow the marker listing category filter to still function, however this will be removed in the future.");
		}
		
		if(options)
			for(var key in options)
				this[key] = options[key];
		
		this.categoryDropdown = $(".wpgmza-marker-listing-category-filter[data-map-id='" + this.map.id + "'] select");
		if(!$(this.categoryDropdown).closest(".wpgmza-store-locator").length)
			this.categoryDropdown.on("change", function(event) {
				var map = WPGMZA.getMapByID(self.map.id);
				map.markerFilter.update();
			});
		
		this.categoryCheckboxes = $(".wpgmza-marker-listing-category-filter[data-map-id='" + this.map.id + "'] input[type='checkbox']");
		this.categoryCheckboxes.on("change", function(event) {
			var map = WPGMZA.getMapByID(self.map.id);
			map.markerFilter.update();
		});
		
		if(map.settings.store_locator_hide_before_search == 1)
		{
			this.showOnFilteringComplete = true;
			$(this.element).hide();
		}
		
		$(this.element).on("click", ".wpgmaps_mlist_row, .wpgmaps_blist_row", function(event) {
			self.onItemClick(event);
		});
		
		$(document.body).on("filteringcomplete.wpgmza", function(event) {
			
			if(event.map.id == self.map.id)
				self.onFilteringComplete(event);
			
		});
		
		this.reload();
	}
	
	WPGMZA.extend(WPGMZA.MarkerListing, WPGMZA.EventDispatcher);
	
	WPGMZA.MarkerListing.createInstance = function(map, element, options)
	{
		switch(map.settings.list_markers_by)
		{
			case WPGMZA.MarkerListing.STYLE_ADVANCED_TABLE:
				return new WPGMZA.AdvancedTableMarkerListing(map, element, options); 
				break;
			
			case WPGMZA.MarkerListing.STYLE_CAROUSEL:
				return new WPGMZA.CarouselMarkerListing(map, element, options);
				break;
			
			case WPGMZA.MarkerListing.STYLE_MODERN:
				return new WPGMZA.ModernMarkerListing(map, element, options);
				break;
			
			default:
				return new WPGMZA.MarkerListing(map, element, options);
				break;
		}
	}
	
	WPGMZA.MarkerListing.STYLE_NONE					= 0;
	WPGMZA.MarkerListing.STYLE_BASIC_TABLE			= 1;
	WPGMZA.MarkerListing.STYLE_BASIC_LIST 			= 4;
	WPGMZA.MarkerListing.STYLE_ADVANCED_TABLE		= 2;
	WPGMZA.MarkerListing.STYLE_CAROUSEL				= 3;
	WPGMZA.MarkerListing.STYLE_MODERN				= 6;
	
	WPGMZA.MarkerListing.ORDER_BY_ID				= 1;
	WPGMZA.MarkerListing.ORDER_BY_TITLE				= 2;
	WPGMZA.MarkerListing.ORDER_BY_ADDRESS			= 3;
	WPGMZA.MarkerListing.ORDER_BY_DESCRIPTION		= 4;
	WPGMZA.MarkerListing.ORDER_BY_CATEGORY			= 5;
	WPGMZA.MarkerListing.ORDER_BY_CATEGORY_PRIORITY	= 6;
	WPGMZA.MarkerListing.ORDER_BY_DISTANCE			= 7;
	WPGMZA.MarkerListing.ORDER_BY_RATING			= 8;
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "mapID", {
		
		"get": function() {
			return this.map.id;
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "paginationEnabled", {
		
		"get": function() {
			return this._paginationEnabled;
		},
		
		"set": function(value) {
			this._paginationEnabled = (value ? true : false);
		}
		
	});
	
	/**
	 * The page size, or the default of 10 if none is set
	 */
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "pageSize", {
		
		"get": function() {
			
			if(!WPGMZA.settings.wpgmza_default_items)
				return 10;
			
			var pageSize = parseInt( WPGMZA.settings.wpgmza_default_items );
			
			if(isNaN(pageSize))
			{
				//console.warn("Invalid page size");
				return null;
			}
			
			return pageSize;
			
		},
		
		"set": function(value) {
			this.pagination("pageSize", value);
		}
		
	});
	
	/**
	 * The current page number, zero based
	 */
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "currentPage", {
		
		"get": function() {
			if(!this.paginationElement)
				return 0;
			
			try{
				return $(this.paginationElement).pagination("getSelectedPageNum") - 1;
			}catch(e) {
				//console.warn("pagination.js getSelectedPageNum failed");
				return 0;
			}
		},
		
		"set": function(value) {
			throw new Error("Not yet implemented");
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "imageWidth", {
		
		get: function() {
			var width = WPGMZA.settings.wpgmza_settings_image_width;
			
			if(!width || !(/^\d+$/.test(width)))
				return false;
				
			return width;
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "imageHeight", {
		
		get: function() {
			var height = WPGMZA.settings.wpgmza_settings_image_height;
			
			if(!height || !(/^\d+$/.test(height)))
				return false;
				
			return height;
		}
		
	});
	
	Object.defineProperty(WPGMZA.MarkerListing.prototype, "style", {
		
		"get": function() {
			return this.map.settings.list_markers_by;
		}
		
	});
	
	WPGMZA.MarkerListing.prototype.initPagination = function()
	{
		if(this.paginationElement)
		{
			try{
				$(this.paginationElement).pagination("destroy");
			}catch(e) {
				//console.warn(e);
			}
			$(this.paginationElement).remove();
		}
		
		if(!this.paginationEnabled || this.showOnFilteringComplete)
			return;
		
		if(this.pageSize)
		{
			var options = this.getPaginationOptions();
			
			if(this.lastAJAXResponse.recordsFiltered <= options.pageSize)
				return;
			
			this.paginationElement = $("<div class='wpgmza-pagination'/>");
			this.pagination = $(this.paginationElement).pagination(this.getPaginationOptions());
			
			$(this.element).after(this.paginationElement);
		}
	}
	
	WPGMZA.MarkerListing.prototype.getPaginationOptions = function()
	{
		var self = this;
		
		var options = {
			
			triggerPagingOnInit: false,
			pageSize: this.pageSize,
			
			dataSource: function(done) {
				done( self.getPaginationDataSource() )
			},
			
			callback: function(data, pagination) {
				self.pageOnPaginationReinit = $(self.paginationElement).pagination("getSelectedPageNum");
				$(self.paginationElement).pagination("disable");
				self.reload();
			}
			
		};
		
		if(this.pageOnPaginationReinit)
			options.pageNumber = this.pageOnPaginationReinit;
		
		return options;
	}
	
	WPGMZA.MarkerListing.prototype.getPaginationDataSource = function()
	{
		var source = [];
		
		if(!this.lastAJAXResponse)
			return source;
		
		for(var i = 0; i < this.lastAJAXResponse.recordsFiltered; i++)
			source.push(i);
		
		return source;
	}
	
	WPGMZA.MarkerListing.prototype.getAJAXRequestParameters = function(params)
	{
		var self = this;
		
		// Create parameters object if it doesn't exist already
		if(!params)
			params = {};
		if(!params.data)
			params.data = {};
		
		// We use POST as the requests can become quite large with marker IDs, don't want to hit the GET limit
		params.method = "POST";
		params.useCompressedPathVariable = true;
		params.cache = true;
		
		// Parse parameters passed from the server
		var str = $(this.element).attr("data-wpgmza-ajax-parameters");
		if(!str || !str.length)
			throw new Error("No AJAX parameters specified on Marker Listing attribute");
		
		var attributeParameters = JSON.parse(str);
		
		// Put PHP class and attribute parameters in params.data
		$.extend(
			params.data, 
			{
				"phpClass": $(this.element).attr("data-wpgmza-php-class"),
				"start": this.currentPage * this.pageSize,
				"length": this.pageSize
			},
			attributeParameters
		);
		
		if(this.overrideMarkerIDs)
			params.data.overrideMarkerIDs = this.overrideMarkerIDs.join(",");
		
		if(this.lastFilteringParams)
			params.data.filteringParams = this.lastFilteringParams;

		if(this.map.showDistanceFromLocation)
		{
			if(!params.data.filteringParams)
				params.data.filteringParams = {};
			
			var location = this.map.showDistanceFromLocation;
			
			params.data.filteringParams.center = {
				lat:	location.lat,
				lng:	location.lng,
				source: location.source
			};
		}
		
		// Add success callback
		params.success = function(response, textStatus, xhr) {
			self.onAJAXResponse(response, textStatus, xhr);
		};
		
		return params;
	}
	
	WPGMZA.MarkerListing.prototype.onAJAXResponse = function(response, textStatus, xhr)
	{
		this.map.showPreloader(false);
		
		this.lastAJAXResponse = response;
		
		this.onHTMLResponse(response.html);
		this.initPagination();
		
		this.trigger("markerlistingupdated");
	}
	
	WPGMZA.MarkerListing.prototype.onHTMLResponse = function(html)
	{
		var self = this;
		
		$(this.element).html(html);
		
		$(this.element).find(".wpgmza-gallery-container").each(function(index, el) {
			
			var map = self.map;
			var marker_id = $(el).closest("[data-marker-id]").attr("data-marker-id");
			var marker = map.getMarkerByID(marker_id);
			
			if(!marker.gallery)
				return;
			
			var gallery = new WPGMZA.MarkerGallery(marker, self);
			
			$(el).html("");
			$(el).append(gallery.element);
			
			
		});
		
		$(this.element).find("[data-marker-icon-src]").each(function(index, el) {
			
			var data;
			var src = $(el).attr("data-marker-icon-src");
			
			try{
				data = JSON.parse( src );
			}catch(e) {
				data = src;
			}
			
			var icon = WPGMZA.MarkerIcon.createInstance(data);
			
			icon.applyToElement(el);
			
		});
	}
	
	WPGMZA.MarkerListing.prototype.getImageElementFromURL = function(url)
	{
		var img = $("<img class='wpgmza_map_image'/>");
		
		$(img).attr("src", url);
		
		if(this.imageWidth)
			$(img).css({"width": this.imageWidth + "px"});
		
		if(this.imageHeight)
			$(img).css({"height": this.imageHeight + "px"});
		
		return img;
	}
	
	WPGMZA.MarkerListing.prototype.getRatingWidget = function(marker)
	{
		var options = {
			type: "marker",
			id: marker.id
		};
		
		if(marker.rating)
		{
			options.averageRating = marker.rating.average;
			options.numRatings = marker.rating.count;
		}
		
		var widget = WPGMZA.RatingWidget.createInstance(options);
		
		return widget;
	}
	
	WPGMZA.MarkerListing.prototype.reload = function()
	{
		// NB: This allows for the marker category filter to work even if "No marker listing" is selected
		if(!this.element)
			return;
		
		if(this.prevXHRRequest)
			this.prevXHRRequest.abort();
		
		var route = $(this.element).attr("data-wpgmza-rest-api-route");
		var params = this.getAJAXRequestParameters();
		
		this.map.showPreloader(true);
		
		this.prevXHRRequest = WPGMZA.restAPI.call(route, params);
	}
	
	WPGMZA.MarkerListing.prototype.enable = function(value)
	{
		if(!value)
			this.pagination("disable");
		else
			this.pagination("enable");
	}
	
	WPGMZA.MarkerListing.prototype.getFilteringParameters = function()
	{
		var params = {};
		
		if(this.categoryDropdown.length && this.categoryDropdown.val() != "0")
			params.categories = [this.categoryDropdown.val()];
		
		if(this.categoryCheckboxes.length)
		{
			params.categories = [];
			
			this.categoryCheckboxes.each(function(index, el) {
				
				if($(el).prop("checked"))
					params.categories.push($(el).val());
				
			});
		}
		
		return params;
	}
	
	WPGMZA.MarkerListing.prototype.onFilteringComplete = function(event)
	{
		var self = this;
		
		if(this.showOnFilteringComplete)
		{
			$(this.element).show();
			delete this.showOnFilteringComplete;
		}
		
		this.overrideMarkerIDs = [];
		
		event.filteredMarkers.forEach(function(data) {
			self.overrideMarkerIDs.push(data.id);
		});
		
		this.lastFilteringParams = event.filteringParams;
		
		// NB: Workaround for paginatejs not resetting, as it's not aware of what's going on with our data
		this.pageOnPaginationReinit = 1;
		
		this.reload();
	}
	
	WPGMZA.MarkerListing.prototype.onItemClick = function(event)
	{
		var marker_id = $(event.currentTarget).attr("mid");
		var marker = this.map.getMarkerByID(marker_id);
		var listingPushedInMap = WPGMZA.maps[0].settings.push_in_map && WPGMZA.maps[0].settings.push_in_map.length;
		var clickedGetDirections = $(event.target).hasClass("wpgmza_gd");
		var zoomLevelOnClick = 13;
		
		marker.trigger("select");
		
		if(this.style != WPGMZA.MarkerListing.STYLE_MODERN && 
			!WPGMZA.settings.disable_scroll_on_marker_listing_click &&
			!clickedGetDirections &&
			!listingPushedInMap)
		{
			var offset = 0;
			
			if(WPGMZA.settings.marker_listing_item_click_scroll_offset)
				offset = parseInt(WPGMZA.settings.marker_listing_item_click_scroll_offset);
			
			$('html, body').animate({
				scrollTop: $(this.map.element).offset().top - offset
			}, 500);
		}
		
		if(this.map.settings.zoom_level_on_marker_listing_click)
			zoomLevelOnClick = this.map.settings.zoom_level_on_marker_listing_click;
		
		if(this.map instanceof WPGMZA.GoogleMap)
		{
			this.map.panTo(marker.getPosition());
			this.map.setZoom(zoomLevelOnClick);
		}
		else
		{
			this.map.panTo(marker.getPosition(), zoomLevelOnClick);
		}
	}
	
});

// js/v8/marker-listings/advanced-table-marker-listing.js
/**
 * @namespace WPGMZA
 * @module AdvancedTableMarkerListing
 * @requires WPGMZA.MarkerListing
 */
jQuery(function($) {
	
	WPGMZA.AdvancedTableMarkerListing = function(map, element, options)
	{
		var self = this;
		
		// NB: Legacy compatibility
		this.element = element = $("#wpgmza_marker_holder_" + map.id + ", #wpgmza_marker_list_" + map.id);
		
		WPGMZA.MarkerListing.apply(this, arguments);
		
		this.dataTable = new WPGMZA.AdvancedTableDataTable(element, this);
		this.dataTable.map = map;
	}
	
	WPGMZA.AdvancedTableMarkerListing.prototype = Object.create(WPGMZA.MarkerListing.prototype);
	WPGMZA.AdvancedTableMarkerListing.prototype.constructor = WPGMZA.AdvancedTableMarkerListing;
	
	WPGMZA.AdvancedTableMarkerListing.prototype.reload = function()
	{
		if(!this.dataTable)
			return; // NB: Still construction. We return, as the dataTable will load itself on init.
		
		this.dataTable.reload();
	}
	
	WPGMZA.AdvancedTableMarkerListing.prototype.onFilteringComplete = function(event)
	{
		this.dataTable.onMarkerFilterFilteringComplete(event);
		
		WPGMZA.MarkerListing.prototype.onFilteringComplete.apply(this, arguments);
	}
	
	WPGMZA.AdvancedTableMarkerListing.prototype.onItemClick = function(event)
	{
		var isFirstCell = $(event.target).is(":first-child");
		var isCollapsed	= $(event.target).closest(".dataTable").is(".collapsed");
		
		if(isCollapsed && isFirstCell)
			return;	// NB: Do nothing. ALlow dataTables responsive module to expand and collapse the row
		
		// NB: Call the parent function
		WPGMZA.MarkerListing.prototype.onItemClick.call(this, event);
	}
	
});

// js/v8/marker-listings/carousel-marker-listing.js
/**
 * @namespace WPGMZA
 * @module CarouselMarkerListing
 * @requires WPGMZA.MarkerListing
 */
jQuery(function($) {
	
	WPGMZA.CarouselMarkerListing = function(map, element, options)
	{
		WPGMZA.MarkerListing.call(this, map, element, 
			$.extend({paginationEnabled: false}, options)
		);
	}
	
	WPGMZA.CarouselMarkerListing.prototype = Object.create(WPGMZA.MarkerListing.prototype);
	WPGMZA.CarouselMarkerListing.prototype.constructor = WPGMZA.CarouselMarkerListing;
	
	WPGMZA.CarouselMarkerListing.createInstance = function(el)
	{
		return new WPGMZA.CarouselMarkerListing(el);
	}
	
	WPGMZA.CarouselMarkerListing.prototype.getOwlCarouselOptions = function()
	{
		var options = {
			autoplay: 			true,
			autoplayTimeout:	5000,
			lazyLoad: 			false,
			autoHeight:			false,
			dots:				false,
			nav:				false,
			loop:				true,
			responsive: {
				0: {
					items: 1
				},
				500: {
					items: 3
				},
				800: {
					items: 5
				}
			}
		};
		
		if(wpgmaps_localize_global_settings['carousel_lazyload'] == "yes")
			options.lazyLoad = true;
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_autoplay']))
			options.autoplayTimeout = parseInt(wpgmaps_localize_global_settings['carousel_autoplay']);
		
		if(wpgmaps_localize_global_settings['carousel_autoheight'] == "yes")
			options.autoHeight = true;
		
		if(wpgmaps_localize_global_settings['carousel_pagination'] == "yes")
			options.dots = true;
		
		if(wpgmaps_localize_global_settings['carousel_navigation']  == "yes")
			options.nav = true;
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_items']))
			options.responsive["800"].items = parseInt(wpgmaps_localize_global_settings['carousel_items']);
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_items_tablet']))
			options.responsive["500"].items = parseInt(wpgmaps_localize_global_settings['carousel_items_tablet']);
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_items_mobile']))
			options.responsive["0"].items = parseInt(wpgmaps_localize_global_settings['carousel_items_mobile']);
		
		return options;
	}
	
	WPGMZA.CarouselMarkerListing.prototype.getAJAXRequestParameters = function(params)
	{
		var params = WPGMZA.MarkerListing.prototype.getAJAXRequestParameters.call(this, params);
		
		// The carousel fetches all items, so remove limits
		delete params.data.start;
		delete params.data.length;
		
		return params;
	}
	
	WPGMZA.CarouselMarkerListing.prototype.onHTMLResponse = function(html)
	{
		WPGMZA.MarkerListing.prototype.onHTMLResponse.call(this, html);
		
		$(this.element).trigger('destroy.owl.carousel');
		$(this.element).owlCarousel(this.getOwlCarouselOptions());
	}
	
	/*$(document).ready(function() {
		
		$("[data-wpgmza-carousel-marker-listing]").each(function(index, el) {
			
			el.wpgmzaCarouselMarkerListing = 
				el.wpgmzaMarkerListing = 
				WPGMZA.CarouselMarkerListing.createInstance(el);
			
		});
		
	});*/
	
});


// js/v8/marker-listings/modern-marker-listing.js
/**
 * @namespace WPGMZA
 * @module ModernMarkerListing
 * @requires WPGMZA.MarkerListing
 * @requires WPGMZA.PopoutPanel
 */
jQuery(function($) {
	
	/**
	 * The modern look and feel marker listing
	 * @return Object
	 */
	WPGMZA.ModernMarkerListing = function(map, element, options)
	{
		var self = this;
		var map_id = map.id;
		var container = $("#wpgmza_map_" + map_id);
		var mashup_ids = container.attr("data-mashup-ids");
		
		WPGMZA.MarkerListing.apply(this, arguments);
		
		this.map = map;
		
		this.element = element;
		this.openButton = $('<div class="wpgmza-modern-marker-open-button wpgmza-modern-shadow wpgmza-modern-hover-opaque"><i class="fa fa-map-marker"></i> <i class="fa fa-list"></i></div>');
		
		container.append(this.openButton);
		container.append(this.element);
		
		this.popoutPanel = new WPGMZA.PopoutPanel();
		this.popoutPanel.element = this.element;
		
		map.on("init", function(event) {
			
			container.append(self.element);
			container.append(self.openButton);
			
		});
		
		self.openButton.on("click", function(event) {
			
			self.open();
			$("#wpgmza_map_" + map_id + " .wpgmza-modern-store-locator").addClass("wpgmza_sl_offset");
			
		});
		
		// Marker view
		this.markerView = new WPGMZA.ModernMarkerListingMarkerView(map);
		this.markerView.parent = this;
		
		// Event listeners
		$(this.element).find(".wpgmza-close-container").on("click", function(event) {
			self.close();
            $("#wpgmza_map_" + self.map.id + " .wpgmza-modern-store-locator").removeClass("wpgmza_sl_offset");
		});
		
		$(this.element).on("click", "li", function(event) {
			self.markerView.open($(event.currentTarget).attr("mid"));
		});
		
		$(document.body).on("click", ".wpgmza_sl_reset_button_" + map_id, function(event) {
			$(self.element).find("li[mid]").show();
		});
		
		$(document.body).on("filteringcomplete.wpgmza", function(event) {
			
			if(event.map.id == self._mapID)
				self.onFilteringComplete(event);
			
		});
	};
	
	WPGMZA.ModernMarkerListing.prototype = Object.create(WPGMZA.MarkerListing.prototype);
	WPGMZA.ModernMarkerListing.prototype.constructor = WPGMZA.ModernMarkerListing;
	
	WPGMZA.ModernMarkerListing.prototype.initPagination = function()
	{
		WPGMZA.MarkerListing.prototype.initPagination.apply(this, arguments);
		
		if(this.pageSize)
			$(this.element).find("ul").after(this.paginationElement);
	}
	
	WPGMZA.ModernMarkerListing.prototype.onHTMLResponse = function(html)
	{
		$(this.element).find("ul.wpgmza-modern-marker-listing-list-item-container").html(html);
	}
	
	WPGMZA.ModernMarkerListing.prototype.open = function()
	{
		this.popoutPanel.open();
	}
	
	WPGMZA.ModernMarkerListing.prototype.close = function()
	{
		this.popoutPanel.close();
	}
	
});

// js/v8/open-layers/ol-directions-renderer.js
/**
 * @namespace WPGMZA
 * @module OLDirectionsRenderer
 * @requires WPGMZA.DirectionsRenderer
 */
jQuery(function($) {
	
	WPGMZA.OLDirectionsRenderer = function(map)
	{
		var self = this;
		
		WPGMZA.DirectionsRenderer.apply(this, arguments);
		
		this.panel = $("#directions_panel_" + map.id);
		this.panel.on("click", ".wpgmza-directions-step", function(event) {
			self.onStepClicked(event);
		});
	}
	
	WPGMZA.extend(WPGMZA.OLDirectionsRenderer, WPGMZA.DirectionsRenderer);
	
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_LEFT				= 0;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_RIGHT				= 1;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SHARP_LEFT			= 2;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SHARP_RIGHT		= 3;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SLIGHT_LEFT		= 4;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_SLIGHT_RIGHT		= 5;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_STRAIGHT			= 6;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_ENTER_ROUNDABOUT	= 7;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_EXIT_ROUNDABOUT	= 8;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_U_TURN				= 9;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_GOAL				= 10;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_DEPART				= 11;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_KEEP_LEFT			= 12;
	WPGMZA.OLDirectionsRenderer.INSTRUCTION_TYPE_KEEP_RIGHT			= 13;
	
	WPGMZA.OLDirectionsRenderer.instructionTypeToClassName = function(type)
	{
		for(var name in WPGMZA.OLDirectionsRenderer)
		{
			if(!name.match(/^INSTRUCTION_TYPE_/))
				continue;
			
			if(WPGMZA.OLDirectionsRenderer[name] == type)
				return "wpgmza-" + name.replace(/_/g, "-").toLowerCase();
		}
	}
	
	WPGMZA.OLDirectionsRenderer.prototype.clear = function()
	{
		if(this.polyline)
		{
			this.map.removePolyline(this.polyline);
			delete this.polyline;
		}
		
		if(this.stepHighlightPolyline)
		{
			this.map.removePolyline(this.stepHighlightPolyline);
			delete this.stepHighlightPolyline;
		}
		
		this.panel.html("");
	}
	
	WPGMZA.OLDirectionsRenderer.prototype.setDirections = function(directions)
	{
		var self = this;
		
		// Polyline route
		var route = directions.routes[0];
		var source = window.polyline.decode(route.geometry);
		var points = [];
		
		this.clear();
		
		source.forEach(function(arr) {
			
			points.push(new WPGMZA.LatLng({
				lat: arr[0],
				lng: arr[1]
			}));
			
		});
		
		var settings = {
			strokeColor: "#4285F4",
			strokeWeight: 4,
			strokeOpacity: 0.8
		}

		if(this.map.settings.directions_route_stroke_color){
			settings.strokeColor = this.map.settings.directions_route_stroke_color;
		}

		 if(this.map.settings.directions_route_stroke_weight){
		 	settings.strokeWeight = this.map.settings.directions_route_stroke_weight;
		 }

		 if(this.map.settings.directions_route_stroke_opacity){
		 	settings.strokeOpacity = this.map.settings.directions_route_stroke_opacity;
		 }


		this.polyline = WPGMZA.Polyline.createInstance({
			points: points,
			settings: settings
		});
		
		this.map.addPolyline(this.polyline);
		
		// Adds markers to origin and destination and removes if directions are searched once more
		if (this.directionStartMarker) {
			this.map.removeMarker(this.directionStartMarker);
		}

		if (this.directionEndMarker) {
			this.map.removeMarker(this.directionEndMarker);
		}

		this.directionStartMarker = WPGMZA.Marker.createInstance({
			position: points[0],
			icon: this.map.settings.directions_route_origin_icon,
			disableInfoWindow: true
		});

		this.map.addMarker(this.directionStartMarker);

		this.directionEndMarker = WPGMZA.Marker.createInstance({
			position: points[points.length - 1],
			icon: this.map.settings.directions_route_destination_icon,
			disableInfoWindow: true
		});

		this.map.addMarker(this.directionEndMarker);

		// Panel
		var steps = [];
		
		if(route.segments)
			route.segments.forEach(function(segment) {
				steps = steps.concat(segment.steps);
			});
		
		steps.forEach(function(step) {
			
			var div = $("<div class='wpgmza-directions-step'></div>");
			
			div[0].wpgmzaDirectionsStep = step;
			
			div.html(step.instruction);
			div.addClass(WPGMZA.OLDirectionsRenderer.instructionTypeToClassName(step.type));
			
			self.panel.append(div);
			
		});
	}
	
	WPGMZA.OLDirectionsRenderer.prototype.onStepClicked = function(event)
	{
		var step = event.currentTarget.wpgmzaDirectionsStep;
		var bounds = new WPGMZA.LatLngBounds();
		var startIndex = step.way_points[0];
		var endIndex = step.way_points[step.way_points.length - 1];
		
		if(this.stepHighlightPolyline)
		{
			this.map.removePolyline(this.stepHighlightPolyline);
			delete this.stepHighlightPolyline;
		}
		
		if(startIndex == endIndex)
			return;
		
		var points = [];
		
		for(var i = startIndex; i <= endIndex; i++)
		{
			var vertex = this.polyline.points[i];
			
			points.push(vertex);
			bounds.extend(vertex);
		}
		
		var polyline = WPGMZA.Polyline.createInstance({
			points: points,
			settings: {
				strokeColor: "#ff0000",
				strokeWeight: 4,
				strokeOpacity: 0.8
			}
		});
		
		this.stepHighlightPolyline = polyline;
		
		this.map.addPolyline(this.stepHighlightPolyline);
		this.map.fitBounds(bounds);
		
		WPGMZA.animateScroll(this.map.element);
	}
	
});

// js/v8/open-layers/ol-directions-service.js
/**
 * @namespace WPGMZA
 * @module OLDirectionsService
 * @requires WPGMZA.DirectionsService
 */
jQuery(function($) {
	
	WPGMZA.OLDirectionsService = function(map)
	{
		WPGMZA.DirectionsService.apply(this, arguments);
		
		this.apiKey = WPGMZA.settings.open_route_service_key;
	}
	
	WPGMZA.extend(WPGMZA.OLDirectionsService, WPGMZA.DirectionsService);
	
	WPGMZA.OLDirectionsService.prototype.geocodeWaypoints = function(waypoints, callback)
	{
		var geocoder = WPGMZA.Geocoder.createInstance();
		var index = 0;
		var coordinates = [];
		
		function geocodeNextWaypoint()
		{
			geocoder.geocode({address: waypoints[index]}, function(results) {
				
				if(!results.length)
					coordinates.push(WPGMZA.DirectionsService.NOT_FOUND);
				else
					coordinates.push(
						[
							results[0].latLng.lng,
							results[0].latLng.lat
						]
					);
				
				if(++index == waypoints.length)
					callback(coordinates);
				else
					geocodeNextWaypoint();
				
			});
		}
		
		geocodeNextWaypoint();
	}
	
	WPGMZA.OLDirectionsService.prototype.route = function(request, callback)
	{
		var self = this;
		var profile, url;
		var translated = {};
		
		// URL and Travel mode
		switch(request.travelMode)
		{
			case WPGMZA.DirectionsService.WALKING:
				profile = "foot-walking";
				break;
			
			case WPGMZA.DirectionsService.BICYCLING:
				profile = "cycling-regular";
				break;
			
			case WPGMZA.DirectionsService.TRANSIT:
				console.warn("Public transport profile is not supported by OpenRouteService");
			
			default:
				profile = "driving-car";
				break;
		}
		
		url = "https://api.openrouteservice.org/v2/directions/" + profile;
		
		// Coordinates
		var waypoints = [request.origin];
		
		if(request.waypoints)
			request.waypoints.forEach(function(obj) {
				waypoints.push(obj.location);
			});
		
		waypoints.push(request.destination);
		
		this.geocodeWaypoints(waypoints, function(coordinates) {
			
			for(var i = 0; i < coordinates.length; i++)
			{
				if(coordinates[i] == WPGMZA.DirectionsService.NOT_FOUND)
				{
					var response = {
						geocoded_waypoints: []
					};
					
					for(var i = 0; i < waypoints.length; i++)
					{
						response.geocoded_waypoints.push({
							geocoder_status: coordinates[i]
						});
					}
					
					callback(response, WPGMZA.DirectionsService.NOT_FOUND);
					return;
				}
			}
			
			translated.coordinates = coordinates;
			switch(WPGMZA.locale.substr(0, 2))
			{
				case "de":
				case "en":
				case "pt":
				case "ru":
				case "hu":
				case "fr":
				case "it":
				case "cn":
				case "dk":
				case "de":
					translated.language = WPGMZA.locale.substr(0, 2);
					break;
				default:
					break;
			}
			
			$.ajax(url, {
				method: "POST",
				dataType: "json",
				contentType: "application/json; charset=utf-8",
				data: JSON.stringify(translated),
				beforeSend: function(xhr) {
					xhr.setRequestHeader('Authorization', self.apiKey);
				},
				success: function(response, status, xhr) {
					
					var status;
					var data = {
						originalResponse: response
					};
					
					if(response.routes && response.routes.length > 0)
						status = WPGMZA.DirectionsService.SUCCESS;
					else
						status = WPGMZA.DirectionsService.ZERO_RESULTS;
					
					callback(response, status);
					
					var event = new WPGMZA.Event({
						type: "directionsserviceresult",
						response: response,
						status: status
					});
					
					self.map.trigger(event);
					
				}
			});
			
		});
	}
	
});

// js/v8/open-layers/ol-heatmap.js
/**
 * @namespace WPGMZA
 * @module OLHeatmap
 * @requires WPGMZA.Heatmap
 */
jQuery(function($) {
	
	WPGMZA.OLHeatmap = function(row)
	{
		WPGMZA.Heatmap.call(this, row);
		
		var settings = $.extend({
			source: this.getSource()
		}, this.settings);
		
		this.olHeatmap = new ol.layer.Heatmap(settings);
		
		this.updateOLHeatmap();
	}
	
	WPGMZA.OLHeatmap.prototype = Object.create(WPGMZA.Heatmap.prototype);
	WPGMZA.OLHeatmap.prototype.constructor = WPGMZA.OLHeatmap;
	
	/**
	 * Updates the OL heatmap layer
	 * TODO: This shouldn't need a timeout. I haven't been able to figure out why but it cuts the last point off sometimes without this timeout. Maybe the OL interactions have something to do with this, at this point I've already spent nearly 2 hours trying to debug this issue so I'm goint to leave it like this for now. - Perry
	 * NB: This issue may pertain to the above: https://github.com/openlayers/openlayers/issues/6394
	 * @return void
	 */
	WPGMZA.OLHeatmap.prototype.updateOLHeatmap = function()
	{
		var self = this;
		setTimeout(function() {
			self.olHeatmap.setSource(self.getSource());
		}, 1000);
	}
	
	WPGMZA.OLHeatmap.prototype.getSource = function()
	{
		var points = this.points;
		var len = points.length;
		var features = [];
		
		for(var i = 0; i < len; i++)
			features.push(
				new ol.Feature({
					geometry: new ol.geom.Point(ol.proj.fromLonLat([
						points[i].lng,
						points[i].lat
					]))
				})
			);
		
		return new ol.source.Vector({
			features: features
		});
	}
	
	WPGMZA.OLHeatmap.prototype.addPoint = function(latLng)
	{
		WPGMZA.Heatmap.prototype.addPoint.call(this, latLng);
		
		this.updateOLHeatmap();
	}
	
	WPGMZA.OLHeatmap.prototype.removePoint = function(latLng)
	{
		WPGMZA.Heatmap.prototype.removePoint.call(this, latLng);
		
		this.updateOLHeatmap();
	}
	
});

// js/v8/open-layers/ol-pro-info-window.js
/**
 * @namespace WPGMZA
 * @module OLProInfoWindow
 * @requires WPGMZA.OLInfoWindow
 */
jQuery(function($) {
	
	WPGMZA.OLProInfoWindow = function(mapObject)
	{
		WPGMZA.OLInfoWindow.call(this, mapObject);
	}
	
	WPGMZA.OLProInfoWindow.prototype = Object.create(WPGMZA.OLInfoWindow.prototype);
	WPGMZA.OLProInfoWindow.prototype.constructor = WPGMZA.OLProInfoWindow;
	
	Object.defineProperty(WPGMZA.OLProInfoWindow.prototype, "panIntoViewOnOpen", {
		
		"get": function() {
			return this.style == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
		}
		
	});
	
	WPGMZA.OLProInfoWindow.prototype.open = function(map, mapObject)
	{
		this.mapObject = mapObject;
		
		var style = (WPGMZA.currentPage == "map-edit" ? WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE : this.style);
		
		switch(style)
		{
			case WPGMZA.ProInfoWindow.STYLE_MODERN:
			case WPGMZA.ProInfoWindow.STYLE_MODERN_PLUS:
			case WPGMZA.ProInfoWindow.STYLE_MODERN_CIRCULAR:
			case WPGMZA.ProInfoWindow.STYLE_TEMPLATE:
				return WPGMZA.ProInfoWindow.prototype.open.call(this, map, mapObject);
				break;
			
			default:
				return WPGMZA.OLInfoWindow.prototype.open.call(this, map, mapObject);
				break;
		}
	}
	
});

// js/v8/open-layers/ol-pro-map.js
/**
 * @namespace WPGMZA
 * @module OLProMap
 * @requires WPGMZA.OLMap
 */
jQuery(function($) {
	
	WPGMZA.OLProMap = function(element, options)
	{
		var self = this;
		
		WPGMZA.OLMap.call(this, element, options);
		
		var prevHoveringMapObjects = [];
		
		// Load KML layers
		this.loadKMLLayers();
		
		// Hover interaction
		this.olMap.on("pointermove", function(event) {
			if(event.dragging)
				return;
			
			var pixel = event.map.getEventPixel(event.originalEvent);
			var currentHoveringMapObjects = [];
			
			var hit = event.map.forEachFeatureAtPixel(pixel, function(feature, layer) {
				
				if(layer && layer.wpgmzaObject)
				{
					if(!layer.wpgmzaObject.hovering)
					{
						layer.wpgmzaObject.hovering = true;
						layer.wpgmzaObject.dispatchEvent("mouseover");
					}
					currentHoveringMapObjects.push(layer.wpgmzaObject);
				}
				
				return true;
			});
			
			for(var i = 0; i < prevHoveringMapObjects.length; i++)
			{
				if(currentHoveringMapObjects.indexOf(prevHoveringMapObjects[i]) == -1)
				{
					prevHoveringMapObjects[i].hovering = false;
					prevHoveringMapObjects[i].dispatchEvent("mouseout");
				}
			}
			
			prevHoveringMapObjects = currentHoveringMapObjects;
		});
		
		this.trigger("init");
		
		this.dispatchEvent("created");
		WPGMZA.events.dispatchEvent({type: "mapcreated", map: this});
	}
	
	WPGMZA.OLProMap.prototype = Object.create(WPGMZA.OLMap.prototype);
	WPGMZA.OLProMap.prototype.constructor = WPGMZA.OLMap.prototype;
	
	WPGMZA.OLProMap.prototype.addHeatmap = function(heatmap)
	{
		this.olMap.addLayer(heatmap.olHeatmap);
		
		WPGMZA.ProMap.prototype.addHeatmap.call(this, heatmap);
	}
	
	/**
	 * Loads KML/GeoRSS layers
	 * @return void
	 */
	WPGMZA.OLProMap.prototype.loadKMLLayers = function()
	{
		// Remove old layers
		if(this.kmlLayers)
		{
			for(var i = 0; i < this.kmlLayers.length; i++)
				this.olMap.removeLayer(this.kmlLayers[i]);
		}
		
		this.kmlLayers = [];
		
		if(!this.settings.kml)
			return;
		
		// Add layers
		var urls = this.settings.kml.split(",");
		var cachebuster = new Date().getTime();
		
		for(var i = 0; i < urls.length; i++)
		{
			var layer = new ol.layer.Vector({
				source: new ol.source.Vector({
					url: urls[i],
					format: new ol.format.KML({
						// extractStyle: true,
						extractAttributes: true
					})
				})
			});
			
			this.kmlLayers.push(layer);
			this.olMap.addLayer(layer);
		}
	}
	
});

// js/v8/open-layers/ol-pro-marker.js
/**
 * @namespace WPGMZA
 * @module OLProMarker
 * @requires WPGMZA.OLMarker
 */
jQuery(function($) {
	
	WPGMZA.OLProMarker = function(row)
	{
		WPGMZA.OLMarker.call(this, row);
	}
	
	WPGMZA.OLProMarker.prototype = Object.create(WPGMZA.OLMarker.prototype);
	WPGMZA.OLProMarker.prototype.constructor = WPGMZA.OLProMarker;
	
	WPGMZA.OLProMarker.prototype.updateIcon = function()
	{
		var self = this;
		var icon = this._icon;
		
		if(WPGMZA.OLMarker.renderMode == WPGMZA.OLMarker.RENDER_MODE_HTML_ELEMENT)
		{
			this.icon.applyToElement(
				$(this.element).find("img")
			);
			
			WPGMZA.getImageDimensions(icon.url, function(dimensions) {
				self.updateElementHeight(dimensions.height);
			});
		}
		else
		{
			this.vectorLayerStyle = new ol.style.Style({
				image: new ol.style.Icon({
					anchor: [0.5, 1],
					src: icon.url
				})
			});
			this.feature.setStyle(this.vectorLayerStyle);
		}
	}
	
});

// js/v8/open-layers/ol-pro-polygon.js
/**
 * @namespace WPGMZA
 * @module OLProPolygon
 * @requires WPGMZA.OLPolygon
 */
jQuery(function($) {
	
	WPGMZA.OLProPolygon = function(row, olFeature)
	{
		var self = this;
		
		WPGMZA.OLPolygon.call(this, row, olFeature);
		
		this.addEventListener("mouseover", function(event) {
			self.onMouseOver(event);
		});
		this.addEventListener("mouseout", function(event) {
			self.onMouseOut(event);
		});
	}
	
	WPGMZA.OLProPolygon.prototype = Object.create(WPGMZA.OLPolygon.prototype);
	WPGMZA.OLProPolygon.prototype.constructor = WPGMZA.OLProPolygon;
	
	WPGMZA.OLProPolygon.prototype.onMouseOver = function(event)
	{
		if(!this.olHoverStyle)
		{
			var params = {};
			
			if(this.settings.hoverOpacity)
				params.fill = new ol.style.Fill({
					color: WPGMZA.hexOpacityToRGBA(this.settings.hoverFillColor, this.settings.hoverOpacity)
				});
				
			if(this.settings.hoverStrokeColor)
				params.stroke = new ol.style.Stroke({
					color: WPGMZA.hexOpacityToRGBA(this.settings.hoverStrokeColor, 1)
				});
				
			this.olHoverStyle = new ol.style.Style(params);
		}
		
		this.layer.setStyle(this.olHoverStyle);
	}
	
	WPGMZA.OLProPolygon.prototype.onMouseOut = function(event)
	{
		this.layer.setStyle(this.olStyle);
	}
	
});

// js/v8/tables/pro-admin-marker-datatable.js
/**
 * @namespace WPGMZA
 * @module ProAdminMarkerDataTable
 * @pro-requires WPGMZA.AdminMarkerDataTable
 */
jQuery(function($) {
	
	WPGMZA.ProAdminMarkerDataTable = function(element)
	{
		WPGMZA.AdminMarkerDataTable.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.ProAdminMarkerDataTable, WPGMZA.AdminMarkerDataTable);
	
	WPGMZA.AdminMarkerDataTable.createInstance = function(element)
	{
		return new WPGMZA.ProAdminMarkerDataTable(element);
	}
	
	WPGMZA.ProAdminMarkerDataTable.prototype.onAJAXResponse = function(response)
	{
		WPGMZA.AdminMarkerDataTable.prototype.onAJAXResponse.apply(this, arguments);
		
		$("[data-marker-icon-src]").each(function(index, element) {
			
			var icon = WPGMZA.MarkerIcon.createInstance(
				$(element).attr("data-marker-icon-src")
			);
			
			icon.applyToElement(element);
			
		});
	}
	
});