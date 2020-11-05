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