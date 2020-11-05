/**
 * @namespace WPGMZA
 * @module LiveTracker
 */
jQuery(function($) {
	
	WPGMZA.LiveTracker = function()
	{
		var self = this;
		
		this.update();
		this.intervalID = setInterval(function() {
			self.update();
		}, 60000);
	}
	
	WPGMZA.LiveTracker.prototype.update = function()
	{
		var mapIDs = [];
		
		WPGMZA.maps.forEach(function(map) {
			mapIDs.push(map.id);
		});
		
		if(mapIDs.length == 0)
			return;
		
		WPGMZA.restAPI.call("/live-tracker/devices/", {
			data: {
				"map_ids": mapIDs.join(",")
			},
			success: function(data, status, xhr) {
				
				for(var i = 0; i < data.length; i++)
				{
					var device = data[i];
					
					if(!device.marker)
						continue;
					
					var map = WPGMZA.getMapByID(device.marker.map_id);
					
					if(!map)
						continue;
					
					var marker = map.getMarkerByID(device.marker.id);
					
					if(!marker)
					{
						// This marker doesn't exist on the map, so create it
						marker = WPGMZA.Marker.createInstance(device.marker);
						map.addMarker(marker);
					}
					
					marker.setPosition(new WPGMZA.LatLng({
						lat: device.marker.lat,
						lng: device.marker.lng
					}));
				}
				
			}
		});
	}
	
	$(window).on("load", function() {
		
		if(!WPGMZA.settings.enable_live_tracking)
			return;
		
		WPGMZA.liveTracker = new WPGMZA.LiveTracker();
		
	});
	
});