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