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