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