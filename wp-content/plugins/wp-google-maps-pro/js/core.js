

var MYMAP = new Array();
var wpgmzaTable = new Array();

var directionsDisplay = new Array();
var directionsService = new Array();
var infoWindow = new Array();
var store_locator_marker = new Array();
var cityCircle = new Array();
var infoWindow_poly = new Array();
var polygon_center = new Array();
var WPGM_Path_Polygon = new Array();
var WPGM_Path = new Array();
var marker_array = new Array();
var marker_array2 = new Array();
var marker_sl_array = new Array();
var wpgmza_controls_active = new Array();
var wpgmza_adv_styling_json = new Array();
// TODO: Some of these should be changed, these are very generic variables names and they're on the global scope
var lazyload;
var autoplay;
var items;
var items_tablet;
var items_mobile;
var default_items;
var pagination;
var navigation;
var modern_iw_open = new Array();
var markerClusterer = new Array();
var original_iw;
var orig_fetching_directions;
var wpgmaps_map_mashup = new Array();
var focused_on_lat_lng = false;

/**
 * Variables used to focus the map on a specific LAT and LNG once the map has loaded.
 */
var focus_lat = false, focus_lng = false; 



var wpgmza_iw_Div = new Array();

var autocomplete = new Array();


var retina = window.devicePixelRatio > 1;


var click_from_list = false;
var wpgmza_user_marker = null; 

var wpgmzaForceLegacyMarkerClusterer = false;
            
autoheight = true;
autoplay = 6000;
lazyload = true;
pagination = false;
navigation = true;
items = 5;
items_tablet = 3;
items_mobile = 1;

 if (typeof Array.prototype.forEach != 'function') {
    Array.prototype.forEach = function(callback){
      for (var i = 0; i < this.length; i++){
        callback.apply(this, [this[i], i, this]);
      }
    };
}

for (var entry in wpgmaps_localize) {
    modern_iw_open[entry] = false;
    if ('undefined' === typeof window.jQuery) {
        setTimeout(function(){ document.getElementById('wpgmza_map_'+wpgmaps_localize[entry]['id']).innerHTML = 'Error: In order for WP Google Maps to work, jQuery must be installed. A check was done and jQuery was not present. Please see the <a href="http://www.wpgmaps.com/documentation/troubleshooting/jquery-troubleshooting/" title="WP Google Maps - jQuery Troubleshooting">jQuery troubleshooting section of our site</a> for more information.'; }, 5000);
    }
    
    
}

/* find out if we are dealing with mashups and which maps they relate to */
if (typeof wpgmza_mashup_ids !== "undefined") {
    for (var mashup_entry in wpgmza_mashup_ids) {
        wpgmaps_map_mashup[mashup_entry] = true;
    }
}

var wpgmza_retina_width;
var wpgmza_retina_height;

if ("undefined" !== typeof wpgmaps_localize_global_settings['wpgmza_settings_retina_width']) { wpgmza_retina_width = parseInt(wpgmaps_localize_global_settings['wpgmza_settings_retina_width']); } else { wpgmza_retina_width = 31; }
if ("undefined" !== typeof wpgmaps_localize_global_settings['wpgmza_settings_retina_height']) { wpgmza_retina_height = parseInt(wpgmaps_localize_global_settings['wpgmza_settings_retina_height']); } else { wpgmza_retina_height = 45; }

function wpgmza_parse_theme_data(raw)
{
	var json;
	
	try{
		json = JSON.parse(raw);
	}catch(e) {
		try{
			json = eval(raw);
		}catch(e) {
			console.warn("Couldn't parse theme data");
			return [];
		}
	}
	
	return json;
}

function wpgmza_get_info_window_style(map_id)
{
	var globalInfoWindowStyle = WPGMZA.settings.wpgmza_iw_type;
	var localInfoWindowStyle = MYMAP[map_id].map.settings.wpgmza_iw_type;
	
	var infoWindowStyle = WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE;
	
	if(globalInfoWindowStyle != WPGMZA.ProInfoWindow.STYLE_INHERIT)
		infoWindowStyle = globalInfoWindowStyle;
	
	if(localInfoWindowStyle != WPGMZA.ProInfoWindow.STYLE_INHERIT)
		infoWindowStyle = localInfoWindowStyle;
	
	return infoWindowStyle;
}

var user_location;
var wpgmza_store_locator_circles_by_map_id = [];

function wpgmza_show_store_locator_radius(map_id, center, radius, distance_type, settings)
{
	
}


function InitMap(map_id,cat_id,reinit) {
    modern_iw_open[map_id] = false /* set modern infowindow open boolean to false to reset the creation of it considering the map has been reinitialized */
    
    if ('undefined' !== typeof wpgmaps_localize_shortcode_data) {
        if (wpgmaps_localize_shortcode_data[map_id]['lat'] !== false && wpgmaps_localize_shortcode_data[map_id]['lng'] !== false) {
            wpgmaps_localize[map_id]['map_start_lat'] = wpgmaps_localize_shortcode_data[map_id]['lat'];
            wpgmaps_localize[map_id]['map_start_lng'] = wpgmaps_localize_shortcode_data[map_id]['lng'];

        }
    }
    
    
    if ('undefined' === cat_id || cat_id === '' || !cat_id || cat_id === 0 || cat_id === "0") { cat_id = 'all'; }

    
	var myLatLng = new WPGMZA.LatLng(wpgmaps_localize[map_id]['map_start_lat'],wpgmaps_localize[map_id]['map_start_lng']);

    if (reinit === false) {
        if (typeof wpgmza_override_zoom !== "undefined" && typeof wpgmza_override_zoom[map_id] !== "undefined") {
            MYMAP[map_id].init("#wpgmza_map_"+map_id, myLatLng, parseInt(wpgmza_override_zoom[map_id]), wpgmaps_localize[map_id]['type'],map_id);
        } else {
            MYMAP[map_id].init("#wpgmza_map_"+map_id, myLatLng, parseInt(wpgmaps_localize[map_id]['map_start_zoom']), wpgmaps_localize[map_id]['type'],map_id);
        }
    }
    

    UniqueCode=Math.round(Math.random()*10000);
    if ('undefined' !== typeof wpgmaps_localize_shortcode_data) {
        if (wpgmaps_localize_shortcode_data[map_id]['lat'] !== false && wpgmaps_localize_shortcode_data[map_id]['lng'] !== false) {
            /* we're using custom fields to create, only show the one marker */
            var point = new WPGMZA.LatLng(parseFloat(wpgmaps_localize_shortcode_data[map_id]['lat']),parseFloat(wpgmaps_localize_shortcode_data[map_id]['lng']));
            var marker = WPGMZA.Marker.createInstance({
                position: point,
                map: MYMAP[map_id].map
            });

        }
    } else {
		MYMAP[map_id].placeMarkers(wpgmaps_markerurl+map_id+'markers.xml?u='+UniqueCode,map_id,cat_id,null,null,null,null,true);
    }
};

function resetLocations(map_id) {
	
	var map = WPGMZA.getMapByID(map_id);
	
	map.setZoom(map.settings.map_start_zoom);
	
	if(map.modernStoreLocatorCircle)
		map.modernStoreLocatorCircle.setVisible(false);
	
	map.markerFilter.update({}, map.storeLocator);
	
	if(map.storeLocatorMarker)
	{
		map.removeMarker(map.storeLocatorMarker);
		delete map.storeLocatorMarker;
	}
	
	if(map.storeLocatorCircle)
	{
		map.removeCircle(map.storeLocatorCircle);
		delete map.storeLocatorCircle;
	}
  
}

function fillInAddress(mid) {
  
  //var place = autocomplete[mid].getPlace();
}


jQuery(window).on("load", function() {
	
	var $ = jQuery;

	for (var entry in wpgmaps_localize) {
		
		var curmid = wpgmaps_localize[entry]['id'];
		
		var elementExists = document.getElementById('addressInput_'+curmid);

		var wpgmza_input_to_exists = document.getElementById('wpgmza_input_to_'+curmid);
		var wpgmza_input_from_exists = document.getElementById('wpgmza_input_from_'+curmid);
		
		if(!window.WPGMZA)
		{
			console.warn("The plugin scripts loaded in a non-standard order");
			return;
		}

		if (typeof google === 'object' && 
			typeof google.maps === 'object' && 
			typeof google.maps.places === 'object' && 
			typeof google.maps.places.Autocomplete === 'function' &&
			WPGMZA.settings.engine == "google-maps")
		{

			if (elementExists !== null) {
				if (typeof wpgmaps_localize[curmid]['other_settings']['wpgmza_store_locator_restrict'] !== "undefined" && wpgmaps_localize[curmid]['other_settings']['wpgmza_store_locator_restrict'] != "") {
					autocomplete[curmid] = new google.maps.places.Autocomplete(
					(document.getElementById('addressInput_'+curmid)),
					{fields: ["name", "formatted_address"], types: ['geocode'], componentRestrictions: {country: wpgmaps_localize[curmid]['other_settings']['wpgmza_store_locator_restrict']} });
					google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
						fillInAddress(curmid);
					});
				} else {
					autocomplete[curmid] = new google.maps.places.Autocomplete(
					(document.getElementById('addressInput_'+curmid)),
					{fields: ["name", "formatted_address"], types: ['geocode']});
					google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
						fillInAddress(curmid);
					});
				}
			}

			if (wpgmza_input_to_exists !== null) {
				autocomplete[curmid] = new google.maps.places.Autocomplete(
				(document.getElementById('wpgmza_input_to_'+curmid)),
				{fields: ["name", "formatted_address"], types: ['geocode']});
				google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
					fillInAddress(curmid);
				});
			}

			if (wpgmza_input_from_exists !== null) {
				autocomplete[curmid] = new google.maps.places.Autocomplete(
				(document.getElementById('wpgmza_input_from_'+curmid)),
				{fields: ["name", "formatted_address"], types: ['geocode']});
				google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
					fillInAddress(curmid);
				});
			}
			if (document.getElementById('wpgmza_ugm_add_address_'+curmid) !== null && WPGMZA.settings.engine == "google-maps") {

				/* initialize the autocomplete form */
				  autocomplete[curmid] = new google.maps.places.Autocomplete(
					  /** @type {HTMLInputElement} */(document.getElementById('wpgmza_ugm_add_address_'+curmid)),
					  { fields: ["name", "formatted_address"], types: ['geocode'] });
				  /* When the user selects an address from the dropdown,
				   populate the address fields in the form. */
				  google.maps.event.addListener(autocomplete[curmid], 'place_changed', function() {
					fillInAddress(curmid);
				  });
			  }
		}
	}

	$("[id^='wpgmza_input_from_'], [id^='wpgmza_input_to_']").each(function(index, el) {
		
		if(!WPGMZA.UseMyLocationButton)
			return;
		
		var button = new WPGMZA.UseMyLocationButton(el);
		$(el).after(button.element);
		
	});
	
	//if(MYMAP[curmid].settings.)
	
});  

function searchLocations(map_id) {
    if (document.getElementById("addressInput_"+map_id) === null) { var address = null; } else { var address = document.getElementById("addressInput_"+map_id).value; }
    if (document.getElementById("nameInput_"+map_id) === null) { var search_title = null; } else { var search_title = document.getElementById("nameInput_"+map_id).value; }
    
    

    checkedCatValues = 'all';
    if (jQuery(".wpgmza_cat_checkbox_"+map_id).length > 0) { 
        var checkedCatValues = jQuery('.wpgmza_checkbox:checked').map(function() { return this.value; }).get();
        if (checkedCatValues === "" || checkedCatValues.length < 1 || checkedCatValues === 0 || checkedCatValues === "0") { checkedCatValues = 'all'; }
    }  
    if (jQuery(".wpgmza_filter_select_"+map_id).length > 0) { 
        var checkedCatValues = jQuery(".wpgmza_filter_select_"+map_id).find(":selected").val();
        if (checkedCatValues === "" || checkedCatValues.length < 1 || checkedCatValues === 0 || checkedCatValues === "0") { checkedCatValues = 'all'; }
    }


    if (address === null || address === "") {
		document.getElementById("addressInput_"+map_id).focus();
		return;
		
         //var map_center = MYMAP[map_id].map.getCenter();
        //searchLocationsNear(map_id,checkedCatValues,map_center,search_title);
    } else {

        checker = address.split(",");
        var wpgm_lat = "";
        var wpgm_lng = "";
		
        wpgm_lat = checker[0];
        wpgm_lng = checker[1];
		
		if(wpgm_lat)
			wpgm_lat = wpgm_lat.trim();
		
		if(wpgm_lng)
			wpgm_lng = wpgm_lng.trim();
		
        checker1 = parseFloat(checker[0]);
        checker2 = parseFloat(checker[1]);

		var regexNumber = /^-?\d*(\.\d+)?$/;
        var geocoder = WPGMZA.Geocoder.createInstance();
		var options = {address: address};
		
		if(wpgmaps_localize[map_id]['other_settings']['wpgmza_store_locator_restrict'])
			options.componentRestrictions = {
				country: wpgmaps_localize[map_id]['other_settings']['wpgmza_store_locator_restrict']
			};
			
		if(wpgm_lat && 
			wpgm_lng && 
			
			wpgm_lat.match(regexNumber) &&
			wpgm_lng.match(regexNumber) && 
			
			wpgm_lat >= -90 && wpgm_lat <= 90 &&
			wpgm_lng >= -180 && wpgm_lng <= 180)
		{
			// Coordinates entered, no need to geocode
			var point = new WPGMZA.LatLng(parseFloat(wpgm_lat),parseFloat(wpgm_lng));
			point.latLng = point;
			
			MYMAP[map_id].map.trigger({
				type: 		"storelocatorgeocodecomplete",
				results:	[point],
				status:		WPGMZA.Geocoder.SUCCESS
			});
			
			searchLocationsNear(map_id,checkedCatValues,point,search_title);
		}
		else
		{
			// Must geocode
			geocoder.geocode(options, function(results, status) {

				MYMAP[map_id].map.trigger({
					type: 		"storelocatorgeocodecomplete",
					results:	results,
					status:		status
				});
				
				if (status == WPGMZA.Geocoder.SUCCESS)
					searchLocationsNear(map_id,checkedCatValues,results[0].geometry.location,search_title);
				else
					alert(address + ' not found');
			});
		}
	}
}

function searchLocationsNear(mapid,category,center_searched,search_title) {
	
    if (jQuery("#wpgmza_marker_holder_"+mapid).length > 0) {
        jQuery("#wpgmza_marker_holder_"+mapid).show();
    }
    if( jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).length > 0 ){
        jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).show();                         
    }
}

jQuery(function() {

    jQuery(window).on("load", function(){
		
        jQuery(".wpgmaps_auto_get_directions").each(function() {
            var this_bliksem = jQuery(this);
            var this_bliksem_id = jQuery(this).attr('id');
            jQuery("#wpgmaps_directions_edit_"+this_bliksem_id).show( function() {
                jQuery(this_bliksem).click();
            });

        });
		
		if(!WPGMZA.visibilityWorkaroundIntervalID)
		{
			// This should handle all cases of tabs, accordions or any other offscreen maps
			var invisibleMaps = jQuery(".wpgmza_map:hidden");
			
			WPGMZA.visibilityWorkaroundIntervalID = setInterval(function() {
				
				jQuery(invisibleMaps).each(function(index, el) {
					
					if(jQuery(el).is(":visible"))
					{
						var id = jQuery(el).attr("id").match(/\d+/);
						var map = WPGMZA.getMapByID(id);
						
						map.onElementResized();
						
						invisibleMaps.splice(invisibleMaps.toArray().indexOf(el), 1);
					}
					
				});
				
			}, 1000);
		}
		
    });

    jQuery(document).ready(function(){
        if (typeof wpgmaps_localize_marker_data !== "undefined") { document.marker_data_array = wpgmaps_localize_marker_data; }

        if (/1\.(0|1|2|3|4|5|6|7)\.(0|1|2|3|4|5|6|7|8|9)/.test(jQuery.fn.jquery))
            console.warn("You are running a version of jQuery which may not be compatible with WP Google Maps.");
	
		jQuery("body").on("click", ".sl_use_loc", function() {
			var wpgmza_map_id = jQuery(this).attr("mid");
			jQuery('#addressInput_'+wpgmza_map_id).val(wpgmaps_lang_getting_location);

			var geocoder = WPGMZA.Geocoder.createInstance();
			var input = jQuery('#addressInput_'+wpgmza_map_id);
			
			WPGMZA.getCurrentPosition(function(result) {
				
				geocoder.geocode({
					latLng: new WPGMZA.LatLng({
						lat: result.coords.latitude,
						lng: result.coords.longitude
					})
				}, function(results, status) {
					
					if(status != WPGMZA.Geocoder.SUCCESS)
					{
						input.val(WPGMZA.localized_strings.failed_to_get_address);
						return;
					}
					
					var result = results[0];
					input.val(result);
					
				});
				
			});
		});       
		jQuery("body").on("click", "#wpgmza_use_my_location_from", function() {
			var wpgmza_map_id = jQuery(this).attr("mid");
			jQuery('#wpgmza_input_from_'+wpgmza_map_id).val(wpgmaps_lang_getting_location);

			var geocoder = WPGMZA.Geocoder.createInstance();
			geocoder.geocode({'latLng': user_location}, function(results, status) {
			  if (status == WPGMZA.Geocoder.SUCCESS) {
				if (results[0]) {
				  jQuery('#wpgmza_input_from_'+wpgmza_map_id).val(results[0]);
				}
			  }
			});
		});              
		jQuery("body").on("click", "#wpgmza_use_my_location_to", function() {
			var wpgmza_map_id = jQuery(this).attr("mid");
			jQuery('#wpgmza_input_to_'+wpgmza_map_id).val(wpgmaps_lang_getting_location);
			var geocoder = new WPGMZA.Geocoder.createInstance();
			geocoder.geocode({'latLng': user_location}, function(results, status) {
			  if (status == WPGMZA.Geocoder.SUCCESS) {
				if (results[0]) {
				  jQuery('#wpgmza_input_to_'+wpgmza_map_id).val(results[0]);
				}
			  }
			});
		});
	
		for(var entry in wpgmaps_localize) {
			jQuery("#wpgmza_map_"+wpgmaps_localize[entry]['id']).css({
				height:wpgmaps_localize[entry]['map_height']+''+wpgmaps_localize[entry]['map_height_type'],
				width:wpgmaps_localize[entry]['map_width']+''+wpgmaps_localize[entry]['map_width_type']

			});            
		}
		

		for(var entry in wpgmaps_localize) {
			InitMap(wpgmaps_localize[entry]['id'],wpgmaps_localize_cat_ids[wpgmaps_localize[entry]['id']],false);
		}

		for(var entry in wpgmaps_localize) {

			if (wpgmaps_localize_global_settings['wpgmza_default_items'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_default_items']) { wpgmza_settings_default_items = 10; } else { wpgmza_settings_default_items = parseInt(wpgmaps_localize_global_settings['wpgmza_default_items']);  }
			
			if (typeof wpgmaps_localize[entry]['other_settings']['store_locator_hide_before_search'] !== "undefined" && wpgmaps_localize[entry]['other_settings']['store_locator_hide_before_search'] === 1) { 
				if( jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).length > 0 ){
					jQuery('#wpgmza_marker_list_container_'+wpgmaps_localize[entry]['id']).hide();                         
				}
			}
		}

	
	
        
    });
    
    for(var entry in wpgmaps_localize) {

    /* general directions settings and variables */
	
	if(WPGMZA.settings.engine == "google-maps" && window.google && window.google.maps) {
		directionsDisplay[wpgmaps_localize[entry]['id']];
		directionsService[wpgmaps_localize[entry]['id']] = new google.maps.DirectionsService();
	}
    var currentDirections = null;
    var oldDirections = [];
    var new_gps;

    if (wpgmaps_localize[entry]['styling_json'] && wpgmaps_localize[entry]['styling_json'].length && wpgmaps_localize[entry]['styling_enabled'] === "1") {
        wpgmza_adv_styling_json[wpgmaps_localize[entry]['id']] = wpgmza_parse_theme_data(wpgmaps_localize[entry]['styling_json']);
    } else {
        wpgmza_adv_styling_json[wpgmaps_localize[entry]['id']] = "";
    }


    MYMAP[wpgmaps_localize[entry]['id']] = {
        map: null,
        bounds: null,
        mc: null
    };
	
	jQuery(document.body).on("init.wpgmza", function(event) {
		
		if(!(event.target instanceof WPGMZA.Map))
			return;
		
		MYMAP[event.target.id].customFieldFilterController = event.target.customFieldFilterController;
		
	});

    if (wpgmaps_localize_global_settings['wpgmza_settings_map_draggable'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_draggable']) { wpgmza_settings_map_draggable = true; } else { wpgmza_settings_map_draggable = false;  }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_clickzoom'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_clickzoom']) { wpgmza_settings_map_clickzoom = false; } else { wpgmza_settings_map_clickzoom = true; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_scroll'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_scroll']) { wpgmza_settings_map_scroll = true; } else { wpgmza_settings_map_scroll = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_zoom'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_zoom']) { wpgmza_settings_map_zoom = true; } else { wpgmza_settings_map_zoom = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_pan'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_pan']) { wpgmza_settings_map_pan = true; } else { wpgmza_settings_map_pan = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_type'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_type']) { wpgmza_settings_map_type = true; } else { wpgmza_settings_map_type = false; }
    if (wpgmaps_localize_global_settings['wpgmza_settings_map_streetview'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_streetview']) { wpgmza_settings_map_streetview = true; } else { wpgmza_settings_map_streetview = false; }


    if ('undefined' === typeof wpgmaps_localize[entry]['other_settings']['map_max_zoom'] || wpgmaps_localize[entry]['other_settings']['map_max_zoom'] === "") { wpgmza_max_zoom = 0; } else { wpgmza_max_zoom = parseInt(wpgmaps_localize[entry]['other_settings']['map_max_zoom']); }
    if ('undefined' === typeof wpgmaps_localize[entry]['other_settings']['map_min_zoom'] || wpgmaps_localize[entry]['other_settings']['map_min_zoom'] === "") { wpgmza_min_zoom = 21; } else { wpgmza_min_zoom = parseInt(wpgmaps_localize[entry]['other_settings']['map_min_zoom']); }


    
    MYMAP[wpgmaps_localize[entry]['id']].init = function(selector, latLng, zoom, maptype, mapid) {
		
		var $ = jQuery;
		
		if(WPGMZA.googleAPIStatus && WPGMZA.googleAPIStatus.code == "USER_CONSENT_NOT_GIVEN")
		{
			$("#wpgmza_map, .wpgmza_map").each(function(index, el) {
				$(el).append($(WPGMZA.api_consent_html));
				$(el).css({height: "auto"});
			});
			
			$("button.wpgmza-api-consent").on("click", function(event) {
				Cookies.set("wpgmza-api-consent-given", true);
				window.location.reload();
			});
			
			return;
		}
		
        if (typeof wpgmaps_localize_map_types !== "undefined") {
            var override_type = wpgmaps_localize_map_types[mapid];
        } else {
            var override_type = "";
        }

        var myOptions = {
                zoom:zoom,
                minZoom: wpgmza_max_zoom,
                maxZoom: wpgmza_min_zoom,
                center: latLng,
                draggable: wpgmza_settings_map_draggable,
                disableDoubleClickZoom: wpgmza_settings_map_clickzoom,
                scrollwheel: wpgmza_settings_map_scroll,
                zoomControl: wpgmza_settings_map_zoom,
                panControl: wpgmza_settings_map_pan,
                mapTypeControl: wpgmza_settings_map_type,
                streetViewControl: wpgmza_settings_map_streetview
        };
		
		if(WPGMZA.settings.engine == "google-maps" && window.google && window.google.maps)
		{
			myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
			
			if (override_type !== "") {
				if (override_type === "ROADMAP") { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
				else if (override_type === "SATELLITE") { myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE; }
				else if (override_type === "HYBRID") { myOptions.mapTypeId = google.maps.MapTypeId.HYBRID; }
				else if (override_type === "TERRAIN") { myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN; } 
				else { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
			} else {
				if (maptype === "1") { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
				else if (maptype === "2") { myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE; }
				else if (maptype === "3") { myOptions.mapTypeId = google.maps.MapTypeId.HYBRID; }
				else if (maptype === "4") { myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN; }
				else { myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; }
			}
			
			if(typeof wpgmza_force_greedy_gestures !== "undefined"){
				myOptions.gestureHandling = wpgmza_force_greedy_gestures;
			}

			if (wpgmaps_localize_global_settings['wpgmza_settings_map_full_screen_control'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_map_full_screen_control']) { 
				myOptions.fullscreenControl = true;
			} else {
				myOptions.fullscreenControl = false;
			}
		}

		var themeData = wpgmaps_localize[mapid]['other_settings']['wpgmza_theme_data'];
        if (themeData && themeData.length) {
            myOptions.styles = wpgmza_parse_theme_data(themeData);
        }

        
        if(typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_auto_night'] != 'undefined' && wpgmaps_localize[mapid]['other_settings']['wpgmza_auto_night'] == 1 ){
    	
    	var date = new Date();
		var isNightTime = date.getHours() < 7 || date.getHours() > 19;

    	if(isNightTime) {
    		myOptions.styles = myOptions.styles.concat(WPGMZA.Map.nightTimeThemeData);
    		}
   		 }
		
		var element = jQuery(selector)[0];
		element.setAttribute("data-map-id", mapid);
        element.setAttribute("data-maps-engine", WPGMZA.settings.engine);
		this.map = WPGMZA.Map.createInstance(element, myOptions);
		
		/*var themeData = wpgmaps_localize[mapid]['other_settings']['wpgmza_theme_data'];
        if (themeData && themeData.length) {
			var obj = wpgmza_parse_theme_data(themeData);
            this.map.setOptions({styles: obj});
        }*/

        if (WPGMZA.settings.engine == "google-maps" && override_type === "STREETVIEW") {
            var panoramaOptions = {
                position: latLng
              };
            var panorama = new google.maps.StreetViewPanorama(jQuery(selector)[0], panoramaOptions);
            this.map.setStreetView(panorama);
        }

		this.bounds = new WPGMZA.LatLngBounds();
		
        jQuery( "#wpgmza_map_"+mapid).trigger( 'wpgooglemaps_loaded' );
                
        /* insert polygon and polyline functionality */
        if (wpgmaps_localize_heatmap_settings !== null) {
            if (typeof wpgmaps_localize_heatmap_settings[mapid] !== "undefined") {
                  for(var poly_entry in wpgmaps_localize_heatmap_settings[mapid]) {
                    add_heatmap(mapid,poly_entry);
                  }
            }
        }
        if (wpgmaps_localize_polygon_settings !== null) {
            if (typeof wpgmaps_localize_polygon_settings[mapid] !== "undefined") {
                  for(var poly_entry in wpgmaps_localize_polygon_settings[mapid]) {
                    add_polygon(mapid,poly_entry);
                  }
            }
        }
        if (wpgmaps_localize_polyline_settings !== null) {
            if (typeof wpgmaps_localize_polyline_settings[mapid] !== "undefined") {
                  for(var poly_entry in wpgmaps_localize_polyline_settings[mapid]) {
                    add_polyline(mapid,poly_entry);
                  }
            }
        }
		
		if(window.wpgmza_circle_data_array[mapid]) {
			window.circle_array = [];
			
			for(var circle_id in wpgmza_circle_data_array[mapid]) {
				
				// Check that this belongs to the array itself, as opposed to its prototype, or else this will break if you add methods to the array prototype (please don't extend the native types)
				if(!wpgmza_circle_data_array[mapid].hasOwnProperty(circle_id))
					continue;
				
				add_circle(mapid, wpgmza_circle_data_array[mapid][circle_id]);
			}
		}
		
		if(window.wpgmza_rectangle_data_array[mapid]) {
			window.rectangle_array = [];
			
			for(var rectangle_id in wpgmza_rectangle_data_array[mapid]) {
				
				// Check that this belongs to the array itself, as opposed to its prototype, or else this will break if you add methods to the array prototype (please don't extend the native types)
				if(!wpgmza_rectangle_data_array[mapid].hasOwnProperty(rectangle_id))
					continue;
				
				add_rectangle(mapid, wpgmza_rectangle_data_array[mapid][rectangle_id]);
				
			}
		}
		
		if(wpgmaps_localize[mapid].other_settings && WPGMZA.isModernComponentStyleAllowed())
		{
			if(wpgmaps_localize[mapid].other_settings.store_locator_style == 'modern' || WPGMZA.settings.user_interface_style == "modern")
			{
				function bind(bind_id) {
					setTimeout(function() {
						MYMAP[bind_id].storeLocator = WPGMZA.ModernStoreLocator.createInstance(bind_id);
					}, 1);
				}
				bind(mapid);
			}
		}
		 
		if(WPGMZA.settings.engine == "google-maps")
		{
			if (wpgmaps_localize[mapid]['bicycle'] === "1") {
				var bikeLayer = new google.maps.BicyclingLayer();
				bikeLayer.setMap(MYMAP[mapid].map.googleMap);
			}
			if (wpgmaps_localize[mapid]['traffic'] === "1") {
				var trafficLayer = new google.maps.TrafficLayer();
				trafficLayer.setMap(MYMAP[mapid].map.googleMap);
			}        
			if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['weather_layer'] && wpgmaps_localize[mapid]['other_settings']['weather_layer'] === 1) {
				if ("undefined" === typeof google.maps.weather) { } else {
					if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['weather_layer_temp_type'] && wpgmaps_localize[mapid]['other_settings']['weather_layer_temp_type'] === 2) {
						var weatherLayer = new google.maps.weather.WeatherLayer({ 
							temperatureUnits: google.maps.weather.TemperatureUnit.FAHRENHEIT
						});
						weatherLayer.setMap(MYMAP[mapid].map.googleMap);
					} else {
						var weatherLayer = new google.maps.weather.WeatherLayer({ 
							temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS
						});
						weatherLayer.setMap(MYMAP[mapid].map.googleMap);
					}
				}
			}        
			if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['cloud_layer'] && wpgmaps_localize[mapid]['other_settings']['cloud_layer'] === 1) {
				if ("undefined" === typeof google.maps.weather) { } else {
					var cloudLayer = new google.maps.weather.CloudLayer();
					cloudLayer.setMap(MYMAP[mapid].map.googleMap);
				}
			}        
			if ("undefined" !== typeof wpgmaps_localize[mapid]['other_settings']['transport_layer'] && wpgmaps_localize[mapid]['other_settings']['transport_layer'] === 1) {
					var transitLayer = new google.maps.TransitLayer();
					transitLayer.setMap(MYMAP[mapid].map.googleMap);
			}        
			// NB: Moved to map modules
			/*if (wpgmaps_localize[mapid]['kml'] !== "") {
				var wpgmaps_d = new Date();
				var wpgmaps_ms = wpgmaps_d.getTime();
				
				arr = wpgmaps_localize[mapid]['kml'].split(',');
				arr.forEach(function(entry) {
					var georssLayer = new google.maps.KmlLayer(entry+'?tstamp='+wpgmaps_ms,{preserveViewport: true});
					georssLayer.setMap(MYMAP[mapid].map.googleMap);
				});


				
			}        */
			if (wpgmaps_localize[mapid]['fusion'] !== "") {
				var fusionlayer = new google.maps.FusionTablesLayer(wpgmaps_localize[mapid]['fusion'], {
					  suppressInfoWindows: false
				});
				fusionlayer.setMap(MYMAP[mapid].map.googleMap);
			}        



			if (typeof wpgmaps_localize[mapid]['other_settings']['push_in_map'] !== 'undefined' && 
				wpgmaps_localize[mapid]['other_settings']['push_in_map'] === "1" && 
				wpgmaps_localize[mapid].other_settings.list_markers_by != WPGMZA.MarkerListing.STYLE_MODERN) {


				if (typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_width'] !== 'undefined') {
					var wpgmza_con_width = wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_width'];
				} else {
					var wpgmza_con_width = "30%";
				}
				if (typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_height'] !== 'undefined') {
					var wpgmza_con_height = wpgmaps_localize[mapid]['other_settings']['wpgmza_push_in_map_height'];
				} else {
					var wpgmza_con_height = "50%";
				}

				if (jQuery('#wpgmza_marker_holder_'+mapid).length) {
					var legend = document.getElementById('wpgmza_marker_holder_'+mapid);
					jQuery(legend).width(wpgmza_con_width);
					jQuery(legend).css('margin','15px');
					jQuery(legend).addClass('wpgmza_innermap_holder');
					jQuery(legend).addClass('wpgmza-shadow');
					jQuery('#wpgmza_table_'+mapid).addClass('');
					wpgmza_controls_active[mapid] = true;
				} else if (jQuery('#wpgmza_marker_list_container_'+mapid).length) {
					var legend_tmp = document.getElementById('wpgmza_marker_list_container_'+mapid);
					
					jQuery('#wpgmza_marker_list_container_'+mapid).wrap("<div id='wpgmza_marker_list_parent_"+mapid+"'></div>");
					var legend = document.getElementById('wpgmza_marker_list_parent_'+mapid);
					jQuery(legend).width(wpgmza_con_width);
					jQuery(legend).height(wpgmza_con_height);

					jQuery(legend).css('margin','15px');
					jQuery(legend).css('overflow','auto');

					/* check if we're using the carousel option */
					if (jQuery(legend_tmp).hasClass("wpgmza_marker_carousel")) { } else {
						jQuery(legend).addClass('wpgmza_innermap_holder');
						jQuery(legend).addClass('wpgmza-shadow');
					}

					jQuery('#wpgmza_marker_list_'+mapid).addClass('');
					wpgmza_controls_active[mapid] = true;

				} else if (jQuery('#wpgmza_marker_list_'+mapid).length) {
					var legend_tmp = document.getElementById('wpgmza_marker_list_'+mapid);
					
					jQuery('#wpgmza_marker_list_'+mapid).wrap("<div id='wpgmza_marker_list_parent_"+mapid+"'></div>");
					var legend = document.getElementById('wpgmza_marker_list_parent_'+mapid);
					jQuery(legend).width(wpgmza_con_width);
					jQuery(legend).height(wpgmza_con_height);

					jQuery(legend).css('margin','15px');
					jQuery(legend).css('overflow','auto');

					/* check if we're using the carousel option */
					if (jQuery(legend_tmp).hasClass("wpgmza_marker_carousel")) { } else {
						jQuery(legend).addClass('wpgmza_innermap_holder');
						jQuery(legend).addClass('wpgmza-shadow');
					}

					jQuery('#wpgmza_marker_list_'+mapid).addClass('');
					wpgmza_controls_active[mapid] = true;
				}
				
				if (typeof legend !== 'undefined' &&
					typeof wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'] !== 'undefined')
				{
					var position;
					
					switch(wpgmaps_localize[mapid]['other_settings']['push_in_map_placement'])
					{
						case "1":
						default:
							position = google.maps.ControlPosition.TOP_CENTER;
							break;
							
						case "2":
							position = google.maps.ControlPosition.TOP_LEFT;
							break;
							
						case "3":
							position = google.maps.ControlPosition.TOP_RIGHT;
							break;
							
						case "4":
							position = google.maps.ControlPosition.LEFT_TOP;
							break;
							
						case "5":
							position = google.maps.ControlPosition.RIGHT_TOP;
							break;
							
						case "6":
							position = google.maps.ControlPosition.LEFT_CENTER;
							break;
							
						case "7":
							position = google.maps.ControlPosition.RIGHT_CENTER;
							break;
							
						case "8":
							position = google.maps.ControlPosition.LEFT_BOTTOM;
							break;
							
						case "9":
							position = google.maps.ControlPosition.RIGHT_BOTTOM;
							break;
							
						case "10":
							position = google.maps.ControlPosition.BOTTOM_CENTER;
							break;
							
						case "11":
							position = google.maps.ControlPosition.BOTTOM_LEFT;
							break;
							
						case "12":
							position = google.maps.ControlPosition.BOTTOM_RIGHT;
							break;
					}
					
					MYMAP[mapid].map.googleMap.controls[position].push(legend);
				}
			
			}
		}
    };    

    jQuery(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange', function() {
        var isFullScreen = document.fullScreen ||
            document.mozFullScreen ||
            document.webkitIsFullScreen;
        var modernMarkerButton = jQuery('.wpgmza-modern-marker-open-button');
        var modernPopoutPanel = jQuery('.wpgmza-popout-panel');
        var modernStoreLocator = jQuery('.wpgmza-modern-store-locator');
        var fullScreenMap = undefined;
        if (modernMarkerButton.length) {
            fullScreenMap = modernMarkerButton.parent('.wpgmza_map').children('div').first();
        } else if (modernPopoutPanel.length) {
            fullScreenMap = modernPopoutPanel.parent('.wpgmza_map').children('div').first();
        } else {
            fullScreenMap = modernStoreLocator.parent('.wpgmza_map').children('div').first();
        }
        if (isFullScreen && typeof fullScreenMap !== "undefined") {
            fullScreenMap.append(modernMarkerButton, modernPopoutPanel, modernStoreLocator);
        }
    });



    MYMAP[wpgmaps_localize[entry]['id']].placeMarkers = function(filename,map_id,cat_id,radius,searched_center,distance_type,search_title,show_markers) {

		if(WPGMZA.googleAPIStatus && WPGMZA.googleAPIStatus.code == "USER_CONSENT_NOT_GIVEN")
			return;
	
        var total_marker_cat_count;
        var slNotFoundMessage = jQuery('.js-not-found-msg');
        var markerStoreLocatorsNum = 0;
        if( Object.prototype.toString.call( cat_id ) === '[object Array]' ) {
            total_marker_cat_count = Object.keys(cat_id).length;
        } else {
            total_marker_cat_count = 1;
        }

        /* reset store locator circle */
        if (typeof cityCircle[map_id] !== "undefined") {
            cityCircle[map_id].setMap(null);
        }

        /* reset store locator i` if any */
        if (typeof store_locator_marker[map_id] !== "undefined") {
            store_locator_marker[map_id].setMap(null);
        }

        marker_array[map_id] = new Array(); 
        marker_sl_array[map_id] = new Array(); 
        marker_array2[map_id] = new Array(); 
        

        if (show_markers || typeof show_markers === "undefined") { 
            
            if (typeof wpgm_g_e !== "undefined" && wpgm_g_e === '1') {
                var mcOptions = {
                    gridSize: 20,
                    maxZoom: 15,
                    styles: [{
                        height: 53,
                        url: "//ccplugins.co/markerclusterer/images/m1.png",
                        width: 53
                    },
                    {
                        height: 56,
                        url: "//ccplugins.co/markerclusterer/images/m2.png",
                        width: 56
                    },
                    {
                        height: 66,
                        url: "//ccplugins.co/markerclusterer/images/m3.png",
                        width: 66
                    },
                    {
                        height: 78,
                        url: "//ccplugins.co/markerclusterer/images/m4.png",
                        width: 78
                    },
                    {
                        height: 90,
                        url: "//ccplugins.co/markerclusterer/images/m5.png",
                        width: 90
                    }] 
                };


                if(typeof wpgmaps_custom_cluster_options !== "undefined"){
                    var customMcOptions = {};

                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_grid_size'] !== "undefined"){ customMcOptions['gridSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_grid_size']); } else { customMcOptions['gridSize'] = mcOptions['gridSize']; }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_max_zoom'] !== "undefined"){ customMcOptions['maxZoom'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_max_zoom']); } else { customMcOptions['maxZoom'] = mcOptions['maxZoom']; }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_min_cluster_size'] !== "undefined"){ customMcOptions['minimumClusterSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_min_cluster_size']); } 
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_zoom_click'] !== "undefined"){ customMcOptions['zoomOnClick'] = true; } else { customMcOptions['zoomOnClick'] = false; }


                    var level1 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level1'] !== "undefined"){ level1['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level1'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level1_width'] !== "undefined"){ level1['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level1_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level1_height'] !== "undefined"){ level1['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level1_height']); }

                    var level2 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level2'] !== "undefined"){ level2['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level2'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level2_width'] !== "undefined"){ level2['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level2_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level2_height'] !== "undefined"){ level2['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level2_height']); }

                    var level3 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level3'] !== "undefined"){ level3['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level3'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level3_width'] !== "undefined"){ level3['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level3_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level3_height'] !== "undefined"){ level3['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level3_height']); }

                    var level4 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level4'] !== "undefined"){ level4['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level4'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level4_width'] !== "undefined"){ level4['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level4_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level4_height'] !== "undefined"){ level4['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level4_height']); }

                    var level5 = {};
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level5'] !== "undefined"){ level5['url'] = wpgmaps_custom_cluster_options['wpgmza_gold_cluster_level5'].replace(/%2F/g,"/"); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level5_width'] !== "undefined"){ level5['width'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level5_width']); }
                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_level5_height'] !== "undefined"){ level5['height'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_level5_height']); }


                    if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'] !== "undefined"){
                        level1['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level2['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level3['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level4['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];
                        level5['textColor'] = wpgmaps_custom_cluster_options['wpgmza_cluster_font_color'];                       
                    }

                     if(typeof wpgmaps_custom_cluster_options['wpgmza_cluster_font_size'] !== "undefined"){
                        level1['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level2['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level3['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level4['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);
                        level5['textSize'] = parseInt(wpgmaps_custom_cluster_options['wpgmza_cluster_font_size']);                       
                    }

                    customMcOptions['styles'] = [ level1, level2, level3, level4, level5 ];

                    mcOptions = customMcOptions; //Override
                }

                if (wpgmaps_localize[map_id]['mass_marker_support'] === "1" || wpgmaps_localize[map_id]['mass_marker_support'] === null) { 
				
                    if (typeof markerClusterer[map_id] !== "undefined") { markerClusterer[map_id].clearMarkers(); }
					
					if(WPGMZA.MarkerClusterer && !wpgmzaForceLegacyMarkerClusterer)
					{
						markerClusterer[map_id] = new WPGMZA.MarkerClusterer(MYMAP[map_id].map, null, mcOptions)
						MYMAP[map_id].map.markerClusterer = markerClusterer[map_id];
					}
					else
						markerClusterer[map_id] = MYMAP[map_id].map.markerClusterer = new MarkerClusterer(MYMAP[map_id].map.googleMap, null, mcOptions);
					
                }
            }
			
            var check1 = 0;

            if (wpgmaps_localize_global_settings['wpgmza_settings_image_width'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_image_width']) { wpgmaps_localize_global_settings['wpgmza_settings_image_width'] = 'auto'; } else { wpgmaps_localize_global_settings['wpgmza_settings_image_width'] = wpgmaps_localize_global_settings['wpgmza_settings_image_width']+'px'; }
            if (wpgmaps_localize_global_settings['wpgmza_settings_image_height'] === "" || 'undefined' === typeof wpgmaps_localize_global_settings['wpgmza_settings_image_height']) { wpgmaps_localize_global_settings['wpgmza_settings_image_height'] = 'auto'; } else { wpgmaps_localize_global_settings['wpgmza_settings_image_height'] = wpgmaps_localize_global_settings['wpgmza_settings_image_height']+'px'; }

        }
        
    };
    
    function wpgmza_open_marker_func(map_id,marker,html,click_from_list,marker_data,wpmgza_marker_id,val) {
		
		if(WPGMZA.settings.wpgmza_settings_disable_infowindows)
			return;
		
		var map = WPGMZA.getMapByID(map_id);
		var marker = map.getMarkerByID(wpmgza_marker_id);
		
		marker.openInfoWindow();
		marker.infoWindow.setContent(html);
		
    }
    
    function wpgmza_create_new_iw_window(mapid) {
		
		// First let's get the map from the map ID
		var map = WPGMZA.getMapByID(mapid);
		
		if(wpgmaps_localize_global_settings.wpgmza_settings_disable_infowindows)
			return;
		
		if(wpgmza_get_info_window_style(mapid) == WPGMZA.ProInfoWindow.STYLE_NATIVE_GOOGLE)
			return;
		
        /* handle new modern infowindow */
        if ((typeof wpgmaps_localize_global_settings['wpgmza_iw_type'] !== 'undefined' && parseInt(wpgmaps_localize_global_settings['wpgmza_iw_type']) >= 1) || (typeof wpgmaps_localize[mapid]['other_settings']['wpgmza_iw_type'] !== "undefined" && parseInt(wpgmaps_localize[mapid]['other_settings']['wpgmza_iw_type']) >= 1)) {
               
                
        }
    }
    
function add_heatmap(mapid,datasetid) {

	if(WPGMZA.settings.engine != "google-maps")
		return;

	var tmp_data = wpgmaps_localize_heatmap_settings[mapid][datasetid];
	var current_poly_id = datasetid;
	var tmp_polydata = tmp_data['polydata'];
	var WPGM_PathData = new Array();
	for (tmp_entry2 in tmp_polydata) {
		 if (typeof tmp_polydata[tmp_entry2][0] !== "undefined") {
			
			WPGM_PathData.push(new google.maps.LatLng(tmp_polydata[tmp_entry2][0], tmp_polydata[tmp_entry2][1]));
		}
	 }
	 if (tmp_data['radius'] === null || tmp_data['radius'] === "") { tmp_data['radius'] = 20; }
	 if (tmp_data['gradient'] === null || tmp_data['gradient'] === "") { tmp_data['gradient'] = null; }
	 if (tmp_data['opacity'] === null || tmp_data['opacity'] === "") { tmp_data['opacity'] = 0.6; }
	 
	 var bounds = new google.maps.LatLngBounds();
	 for (i = 0; i < WPGM_PathData.length; i++) {
	   bounds.extend(WPGM_PathData[i]);
	 }

	WPGM_Path_Polygon[datasetid] = new google.maps.visualization.HeatmapLayer({
		 data: WPGM_PathData,
		 map: MYMAP[mapid].map.googleMap
	});
	
   WPGM_Path_Polygon[datasetid].setMap(MYMAP[mapid].map.googleMap);
   var gradient = JSON.parse(tmp_data['gradient']);
   WPGM_Path_Polygon[datasetid].set('radius', tmp_data['radius']);
   WPGM_Path_Polygon[datasetid].set('opacity', tmp_data['opacity']);
   WPGM_Path_Polygon[datasetid].set('gradient', gradient);


   polygon_center = bounds.getCenter();
}

    function add_polygon(mapid,polygonid) {
		
		if(WPGMZA.settings.engine == "open-layers")
			return;
		
        var tmp_data = wpgmaps_localize_polygon_settings[mapid][polygonid];
         var current_poly_id = polygonid;
         var tmp_polydata = tmp_data['polydata'];
         var WPGM_PathData = new Array();
         for (tmp_entry2 in tmp_polydata) {
             if (typeof tmp_polydata[tmp_entry2][0] !== "undefined") {
                
                WPGM_PathData.push(new google.maps.LatLng(tmp_polydata[tmp_entry2][0], tmp_polydata[tmp_entry2][1]));
            }
         }
         if (tmp_data['lineopacity'] === null || tmp_data['lineopacity'] === "") {
             tmp_data['lineopacity'] = 1;
         }
         
         var bounds = new google.maps.LatLngBounds();
         for (i = 0; i < WPGM_PathData.length; i++) {
           bounds.extend(WPGM_PathData[i]);
         }

		 function addPolygonLabel(googleLatLngs)
		 {
			 var label = tmp_data.title;
			 
			 console.log(label);
			 
			 var geojson = [[]];
			 
			 googleLatLngs.forEach(function(latLng) {
				geojson[0].push([
					latLng.lng(),
					latLng.lat()
				])
			 });
			 
			 var lngLat = WPGMZA.ProPolygon.getLabelPosition(geojson);
			 
			 var latLng = new WPGMZA.LatLng({
				 lat: lngLat[1],
				 lng: lngLat[0]
			 });
			 
			 var marker = WPGMZA.Marker.createInstance({
				 position: latLng
			 });
			 
			 // TODO: Support target map
			 // TODO: Read polygon title
			 
			 var text = WPGMZA.Text.createInstance({
				 text: label,
				 map: WPGMZA.getMapByID(mapid),
				 position: latLng
			 });
			 
			 //var marker = WPGMZA.Marker.createInst)
		 }
		 
        WPGM_Path_Polygon[polygonid] = new google.maps.Polygon({
             path: WPGM_PathData,
             clickable: true, /* must add option for this */ 
             strokeColor: "#"+tmp_data['linecolor'],
             fillOpacity: tmp_data['opacity'],
             strokeOpacity: tmp_data['lineopacity'],
             fillColor: "#"+tmp_data['fillcolor'],
             strokeWeight: 2,
             map: MYMAP[mapid].map.googleMap
       });
       WPGM_Path_Polygon[polygonid].setMap(MYMAP[mapid].map.googleMap);
	   
		var map = WPGMZA.getMapByID(mapid);
		if(map.settings.polygon_labels)
			addPolygonLabel(WPGM_PathData);

        polygon_center = bounds.getCenter();

        if (tmp_data['title'] !== "") {
         infoWindow_poly[polygonid] = new google.maps.InfoWindow();
		 infoWindow_poly[polygonid].setZIndex(WPGMZA.GoogleInfoWindow.Z_INDEX);
		 
         google.maps.event.addListener(WPGM_Path_Polygon[polygonid], 'click', function(event) {
             infoWindow_poly[polygonid].setPosition(event.latLng);
             content = "";
             if (tmp_data['link'] !== "") {
                 var content = "<a href='"+tmp_data['link']+"'><h4 class='wpgmza_polygon_title'>"+tmp_data['title']+"</h4></a>";
                 if (tmp_data['description'] !== "") {
                 	content += '<p class="wpgmza_polygon_description">' + tmp_data['description'] + '</p>';
                 }
             } else {
                 var content = '<h4 class="wpgmza_polygon_title">' + tmp_data['title'] + '</h4>';
                 if (tmp_data['description'] !== "") {
                 	content += '<p class="wpgmza_polygon_description">' + tmp_data['description'] + '</p>';
                 }
             }
             infoWindow_poly[polygonid].setContent(content);
             infoWindow_poly[polygonid].open(MYMAP[mapid].map.googleMap, this.position);
         }); 
        }


       google.maps.event.addListener(WPGM_Path_Polygon[polygonid], "mouseover", function(event) {
             this.setOptions({fillColor: "#"+tmp_data['ohfillcolor']});
             this.setOptions({fillOpacity: tmp_data['ohopacity']});
             this.setOptions({strokeColor: "#"+tmp_data['ohlinecolor']});
             this.setOptions({strokeWeight: 2});
             this.setOptions({strokeOpacity: 0.9});
       });
       google.maps.event.addListener(WPGM_Path_Polygon[polygonid], "click", function(event) {

             this.setOptions({fillColor: "#"+tmp_data['ohfillcolor']});
             this.setOptions({fillOpacity: tmp_data['ohopacity']});
             this.setOptions({strokeColor: "#"+tmp_data['ohlinecolor']});
             this.setOptions({strokeWeight: 2});
             this.setOptions({strokeOpacity: 0.9});
       });
       google.maps.event.addListener(WPGM_Path_Polygon[polygonid], "mouseout", function(event) {
             this.setOptions({fillColor: "#"+tmp_data['fillcolor']});
             this.setOptions({fillOpacity: tmp_data['opacity']});
             this.setOptions({strokeColor: "#"+tmp_data['linecolor']});
             this.setOptions({strokeWeight: 2});
             this.setOptions({strokeOpacity: tmp_data['lineopacity']});
       });


           
        
        
    }
    function add_polyline(mapid,polyline) {
        
		if(WPGMZA.settings.engine == "open-layers")
			return;
        
        var tmp_data = wpgmaps_localize_polyline_settings[mapid][polyline];

        var current_poly_id = polyline;
        var tmp_polydata = tmp_data['polydata'];
        var WPGM_Polyline_PathData = new Array();
        for (tmp_entry2 in tmp_polydata) {
            if (typeof tmp_polydata[tmp_entry2][0] !== "undefined" && typeof tmp_polydata[tmp_entry2][1] !== "undefined") {
                var lat = tmp_polydata[tmp_entry2][0].replace(')', '');
                lat = lat.replace('(','');
                var lng = tmp_polydata[tmp_entry2][1].replace(')', '');
                lng = lng.replace('(','');
                WPGM_Polyline_PathData.push(new google.maps.LatLng(lat, lng));
            }
             
             
        }
         if (tmp_data['lineopacity'] === null || tmp_data['lineopacity'] === "") {
             tmp_data['lineopacity'] = 1;
         }

        WPGM_Path[polyline] = new google.maps.Polyline({
             path: WPGM_Polyline_PathData,
             strokeColor: "#"+tmp_data['linecolor'],
             strokeOpacity: tmp_data['opacity'],
             strokeWeight: tmp_data['linethickness'],
             map: MYMAP[mapid].map.googleMap
       });
       WPGM_Path[polyline].setMap(MYMAP[mapid].map.googleMap);
        
        
    }
	
	function add_circle(mapid, data)
	{
		if(WPGMZA.settings.engine != "google-maps" || !MYMAP.hasOwnProperty(mapid))
			return;
		
		data.map = MYMAP[mapid].map.googleMap;
		
		if(!(data.center instanceof google.maps.LatLng)) {
			var m = data.center.match(/-?\d+(\.\d*)?/g);
			data.center = new google.maps.LatLng({
				lat: parseFloat(m[0]),
				lng: parseFloat(m[1]),
			});
		}
		
		data.radius = parseFloat(data.radius);
		data.fillColor = data.color;
		data.fillOpacity = parseFloat(data.opacity);
		
		data.strokeOpacity = 0;
		
		var circle = new google.maps.Circle(data);
		circle_array.push(circle);
	}
    
	function add_rectangle(mapid, data)
	{
		if(WPGMZA.settings.engine != "google-maps" || !MYMAP.hasOwnProperty(mapid))
			return;
		
		data.map = MYMAP[mapid].map.googleMap;
		
		data.fillColor = data.color;
		data.fillOpacity = parseFloat(data.opacity);
		
		var northWest = data.cornerA;
		var southEast = data.cornerB;
		
		var m = northWest.match(/-?\d+(\.\d+)?/g);
		var north = parseFloat(m[0]);
		var west = parseFloat(m[1]);
		
		m = southEast.match(/-?\d+(\.\d+)?/g);
		var south = parseFloat(m[0]);
		var east = parseFloat(m[1]);
		
		data.bounds = {
			north: north,
			west: west,
			south: south,
			east: east
		};
		
		data.strokeOpacity = 0;
		
		var rectangle = new google.maps.Rectangle(data);
		rectangle_array.push(rectangle);
	}
    

}

});

jQuery("body").on("keypress",".addressInput", function(event) {
  if ( event.which == 13 ) {
    var mid = jQuery(this).attr("mid");
     jQuery('.wpgmza_sl_search_button_'+mid).trigger('click');
  }
});

jQuery('body').on('click', '.wpgmza_modern_infowindow_close', function(){
    var mid = jQuery(this).attr('mid');
    jQuery("#wpgmza_iw_holder_"+mid).remove();


});
