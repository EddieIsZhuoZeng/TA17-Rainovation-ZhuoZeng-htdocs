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