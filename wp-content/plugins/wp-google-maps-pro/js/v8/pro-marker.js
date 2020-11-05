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