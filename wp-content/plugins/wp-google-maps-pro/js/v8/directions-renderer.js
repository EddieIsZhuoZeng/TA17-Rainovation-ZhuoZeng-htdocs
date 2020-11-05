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