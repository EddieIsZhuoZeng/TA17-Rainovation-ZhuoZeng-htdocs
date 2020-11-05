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