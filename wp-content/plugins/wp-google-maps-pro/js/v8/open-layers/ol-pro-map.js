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