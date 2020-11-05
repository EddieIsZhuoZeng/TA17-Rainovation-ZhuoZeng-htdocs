
    var heatmap = [];
    var WPGM_PathLine = [];
	var WPGM_Path = [];
	var WPGM_PathLineData = [];
	var WPGM_PathData = [];

    var marker_pull = wpgmza_legacy_map_edit_page_vars.marker_pull;
	var db_marker_array = wpgmza_legacy_map_edit_page_vars.db_marker_array;
	
	function initShiftClick()
	{
		var lastSelectedRow;
		var $ = jQuery;
		
		jQuery(document.body).on("click", "[data-wpgmza-admin-marker-datatable] input[name='mark']", function(event) {
			
			var checkbox = event.currentTarget;
			var row = jQuery(checkbox).closest("tr");
			
			if(lastSelectedRow && event.shiftKey)
			{
				var prevIndex = lastSelectedRow.index();
				var currIndex = row.index();
				var startIndex = Math.min(prevIndex, currIndex);
				var endIndex = Math.max(prevIndex, currIndex);
				var rows = jQuery("[data-wpgmza-admin-marker-datatable] tbody>tr");
				
				// Clear
				jQuery("[data-wpgmza-admin-marker-datatable] input[name='mark']").prop("checked", false);
				
				for(var i = startIndex; i <= endIndex; i++)
					jQuery(rows[i]).find("input[name='mark']").prop("checked", true);
				
				
				console.log(prevIndex);
				console.log(currIndex);
			}
			
			lastSelectedRow = row;
			
		});
	}
	initShiftClick();
	
	function wpgmza_quickfix_clear_all_markers()
	{
		var map = WPGMZA.maps[0];
		map.markers.forEach(function(marker) {
			map.removeMarker(marker);
		});
	}
	
	function wpgmza_native_map_type_to_google_map_type(nativeMapType)
	{
		var mapTypeId;
		
		switch(nativeMapType)
		{
			case "2":
				mapTypeId = google.maps.MapTypeId.SATELLITE;
				break;
			
			case "3":
				mapTypeId = google.maps.MapTypeId.HYBRID;
				break;
			
			case "4":
				mapTypeId = google.maps.MapTypeId.TERRAIN;
				break;
			
			default:
				mapTypeId = google.maps.MapTypeId.ROADMAP;
				break;
		}
		
		return mapTypeId;
	}
	
    jQuery(function() {
    	var placeSearch, autocomplete, wpgmza_def_i;

		if(!WPGMZA.settings.engine || WPGMZA.settings.engine == "google-maps")
		jQuery("#wpgmza_map_type").on("change", function(event) {
			
			WPGMZA.maps[0].setOptions({
				mapTypeId: wpgmza_native_map_type_to_google_map_type(this.value)
			});
			
		});
        
        function fillInAddress() {
          // Get the place details from the autocomplete object.
          //var place = autocomplete.getPlace();	
        }	

        
        var wpgmza_table_length;


                jQuery(document).ready(function(){
                	var wpgmzaTable;
                	wpgmza_def_i = jQuery("#wpgmza_cmm").html();

                    jQuery("#wpgmaps_show_advanced").click(function() {
                      jQuery("#wpgmaps_advanced_options").show();
                      jQuery("#wpgmaps_show_advanced").hide();
                      jQuery("#wpgmaps_hide_advanced").show();

                    });
                    jQuery("#wpgmaps_hide_advanced").click(function() {
                      jQuery("#wpgmaps_advanced_options").hide();
                      jQuery("#wpgmaps_show_advanced").show();
                      jQuery("#wpgmaps_hide_advanced").hide();

                    });



                    wpgmzaTable = jQuery('#wpgmza_table').DataTable({
                        "bProcessing": true,
                        "aaSorting": [[ wpgmza_legacy_map_edit_page_vars.order_by, wpgmza_legacy_map_edit_page_vars.order_choice ]]
                    });
                    function wpgmza_reinitialisetbl() {
                        var elem = jQuery("#wpgmza_marker_holder>[data-wpgmza-table]")[0];
						elem.wpgmzaDataTable.reload();
                    }
                    function wpgmza_InitMap() {
                        var myLatLng = {
							lat: wpgmza_legacy_map_edit_page_vars.wpgmza_lat,
							lng: wpgmza_legacy_map_edit_page_vars.wpgmza_lng
						};
						
                        MYMAP.init('#wpgmza_map', myLatLng, wpgmza_legacy_map_edit_page_vars.start_zoom);
                        UniqueCode=Math.round(Math.random()*10000);
                        MYMAP.placeMarkers(
							wpgmza_legacy_map_edit_page_vars.marker_url + '?u='+UniqueCode,
							wpgmza_legacy_map_edit_page_vars.map_id
						);
                    }

                    jQuery("#wpgmza_map").css({
                        height: wpgmza_legacy_map_edit_page_vars.wpgmza_height + wpgmza_legacy_map_edit_page_vars.wpgmza_height_type,
                        width: wpgmza_legacy_map_edit_page_vars.wpgmza_width + wpgmza_legacy_map_edit_page_vars.wpgmza_width_type

                    });
                    
                    
                    jQuery("#sl_line_color").focusout(function() {
                        poly.setOptions({ strokeColor: "#"+jQuery("#poly_line").val() }); 
                    });
                    jQuery("#sl_fill_color").keyup(function() {
                        poly.setOptions({ strokeOpacity: jQuery("#poly_opacity").val() }); 
                    });
                    jQuery("#sl_opacity").keyup(function() {
                        poly.setOptions({ strokeWeight: jQuery("#poly_thickness").val() }); 
                    });
                    
					var geocoder = WPGMZA.Geocoder.createInstance();
                    wpgmza_InitMap();


                    jQuery("select[name=wpgmza_table_length]").change(function () {
                    	wpgmza_table_length = jQuery(this).val();
                    })
                    jQuery("body").on("click", ".wpgmza_del_btn", function() {
                    	
                        var cur_id = jQuery(this).attr("id");

                      

                            
                    
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_marker',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                marker_id: cur_id
                        };
                        
                        jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
								
                                db_marker_array = JSON.stringify(returned_data.marker_data);
								
                                wpgmza_InitMap();

	                    		//jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
	                            wpgmza_reinitialisetbl();
                                
								
                        });

                    });
                    jQuery("body").on("click", ".wpgmza_approve_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'approve_marker',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                marker_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
                                db_marker_array = JSON.stringify(returned_data.marker_data);
                                wpgmza_InitMap();
                                //jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                wpgmza_reinitialisetbl();

                        });

                    });
                    jQuery("body").on("click", ".wpgmza_poly_del_btn", function() {
                        var cur_id = parseInt(jQuery(this).attr("id"));
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_poly',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                poly_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                
                                	
                                WPGM_Path[cur_id].setMap(null);
                                delete WPGM_PathData[cur_id];
                                delete WPGM_Path[cur_id];
                                /*wpgmza_InitMap();*/
                                jQuery("#wpgmza_poly_holder").html(response);
                                /*window.location.reload();*/
                        });

                    });
                    jQuery("body").on("click", ".wpgmza_polyline_del_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_polyline',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                poly_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                                WPGM_PathLine[cur_id].setMap(null);
                                delete WPGM_PathLineData[cur_id];
                                delete WPGM_PathLine[cur_id];
                                /*wpgmza_InitMap();*/
                                jQuery("#wpgmza_polyline_holder").html(response);
                                /*window.location.reload();*/
                        });

                    });
                    jQuery("body").on("click", ".wpgmza_dataset_del_btn", function() {
                        var cur_id = jQuery(this).attr("id");
                        var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_dataset',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                poly_id: cur_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
							heatmap[cur_id].setMap(null);
                                delete heatmap[cur_id];
                                /*wpgmza_InitMap();*/
                                jQuery("#wpgmza_heatmap_holder").html(response);
                                /*window.location.reload();*/
                        });

                    });
					
					jQuery("body").on("click", ".wpgmza_circle_del_btn", function() {
						
						var circle_id = jQuery(this).attr("id");
						var map_id = jQuery("#wpgmza_id").val();
						
						var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_circle',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                circle_id: circle_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery("#tabs-m-5 table").replaceWith(response);
							circle_array.forEach(function(circle) {
								
								if(circle.id == circle_id)
								{
									circle.setMap(null);
									return false;
								}
								
							});
                            
                        });
						
					});
					
					jQuery("body").on("click", ".wpgmza_rectangle_del_btn", function() {
						
						var rectangle_id = jQuery(this).attr("id");
						var map_id = jQuery("#wpgmza_id").val();
						
						var wpgm_map_id = "0";
                        if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                        var data = {
                                action: 'delete_rectangle',
                                security: wpgmza_legacy_map_edit_page_vars.ajax_nonce,
                                map_id: wpgm_map_id,
                                rectangle_id: rectangle_id
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery("#tabs-m-6 table").replaceWith(response);
							rectangle_array.forEach(function(rectangle) {
								
								if(rectangle.id == rectangle_id)
								{
									rectangle.setMap(null);
									return false;
								}
								
							});
                            
                        });
						
					});

            });

            });

			window.WPGM_PathData = wpgmza_legacy_map_edit_page_vars.WPGM_PathData;
			
			jQuery(function($) {
				if(WPGMZA.settings.engine == "google-maps" && window.google) {
					
					function stringCoordinateArrayToGoogleMapsLagLngArray(stringCoordinatePairs)
					{
						var points = [];
						
						for(var i = 0; i < stringCoordinatePairs.length; i++)
						{
							var latLng = WPGMZA.stringToLatLng(stringCoordinatePairs[i]);
							points.push(latLng.toGoogleLatLng());
						}
						
						return points;
					}
				
					for(var poly_id in wpgmza_legacy_map_edit_page_vars.WPGM_PathData)
					{
						var options = jQuery.extend(
							{
								path: stringCoordinateArrayToGoogleMapsLagLngArray( wpgmza_legacy_map_edit_page_vars.WPGM_PathData[poly_id] ),
								clickable: false
							}, 
							wpgmza_legacy_map_edit_page_vars.polygon_options_by_id[poly_id]
						);
						
						WPGM_Path[poly_id] = new google.maps.Polygon(options);
					}
					
					for(var polyline_id in wpgmza_legacy_map_edit_page_vars.WPGM_PathLineData)
					{
						var options = jQuery.extend(
							{
								path: stringCoordinateArrayToGoogleMapsLagLngArray( wpgmza_legacy_map_edit_page_vars.WPGM_PathLineData[polyline_id] )
							},
							wpgmza_legacy_map_edit_page_vars.polyline_options_by_id[polyline_id]
						);
						
						WPGM_PathLine[polyline_id] = new google.maps.Polyline(options);
					}
				
					for(var dataset_id in wpgmza_legacy_map_edit_page_vars.heatmaps)
					{
						var stringCoordinatePairs = wpgmza_legacy_map_edit_page_vars.heatmaps[dataset_id];
						var points = stringCoordinateArrayToGoogleMapsLagLngArray(stringCoordinatePairs);
						
						heatmap[dataset_id] = new google.maps.visualization.HeatmapLayer({
							data: points
						});              	
					}
				}
			});
			
            var MYMAP = {
                map: null,
                bounds: null,
                mc: null
            }
            MYMAP.init = function(selector, latLng, zoom) {
              var myOptions = {
                zoom: parseInt(zoom),
                minZoom: parseInt(wpgmza_legacy_map_edit_page_vars.max_zoom),
                maxZoom: 21,
                center: latLng
              };
			  
			  jQuery.extend(myOptions, wpgmza_legacy_map_edit_page_vars.mapOptions);
			  
			if(WPGMZA.settings.engine == "google-maps" && window.google)
				myOptions.mapTypeId = google.maps.MapTypeId[wpgmza_legacy_map_edit_page_vars.map_type];
			
			var element = jQuery(selector)[0];
			var map_id = window.location.href.match(/map_id=(\d+)/)[1];
			element.setAttribute("data-map-id", map_id);
			element.setAttribute("data-maps-engine", WPGMZA.settings.engine);
			
			WPGMZA.maps = [];
			
			this.map = WPGMZA.Map.createInstance(element, myOptions);
            this.bounds = new WPGMZA.LatLngBounds();
			
			if(!WPGMZA.settings.engine || WPGMZA.settings.engine == "google-maps")
				this.map.setOptions({mapTypeId: wpgmza_native_map_type_to_google_map_type(jQuery("#wpgmza_map_type").val())});
			
			var theme_data = wpgmza_legacy_map_edit_page_vars.theme_data;
			if(theme_data && theme_data.length)
			{
				try{
					this.map.setOptions({
						styles: JSON.parse(theme_data)
					});
				}catch(e) {
					console.warn("Error applying theme data");
				}
			}
			
			if(WPGMZA.settings.engine == "google-maps") {
				window.circle_array = [];
				for(var circle_id in wpgmza_circle_data_array)
				{
					var data = jQuery.extend({}, wpgmza_circle_data_array[circle_id]);
					data.map = MYMAP.map;

					if(!data.center)
					{
						console.warn("No center data for circle ID " + circle_id)
						continue;
					}
					
					var m = data.center.match(/-?\d+(\.\d*)?/g);
					data.center = new WPGMZA.LatLng({
						lat: parseFloat(m[0]),
						lng: parseFloat(m[1]),
					});
					
					data.radius = parseFloat(data.radius);
					data.fillColor = data.color;
					data.fillOpacity = parseFloat(data.opacity);
					
					data.strokeOpacity = 0;
					
					var circle = WPGMZA.Circle.createInstance(data);
					circle_array.push(circle);
				}
				
				window.rectangle_array = [];
				for(var rectangle_id in wpgmza_rectangle_data_array)
				{
					var data = jQuery.extend({}, wpgmza_rectangle_data_array[rectangle_id]);
					data.map = MYMAP.map.googleMap;
					
					if(!data.cornerA || !data.cornerB)
					{
						console.warn("No center data for rectangle ID " + rectangle_id)
						continue;
					}
					
					var northWest = data.cornerA;
					var southEast = data.cornerB;
					
					var m = northWest.match(/-?\d+(\.\d+)?/g);
					var north = parseFloat(m[0]);
					var west = parseFloat(m[1]);
					
					m = southEast.match(/-?\d+(\.\d+)?/g);
					var south = parseFloat(m[0]);
					var east = parseFloat(m[1]);
					
					data.bounds = {
						north: north,
						west: west,
						south: south,
						east: east
					};
					
					data.fillColor = data.color;
					data.fillOpacity = parseFloat(data.opacity);
					
					data.strokeOpacity = 0;
					
					var rectangle = new google.maps.Rectangle(data);
					rectangle_array.push(rectangle);
				}
			}

            //google.maps.event.addListener(MYMAP.map, 'zoom_changed', function() {
			MYMAP.map.on("zoomchanged", function() {
                zoomLevel = MYMAP.map.getZoom();
                jQuery("#wpgmza_start_zoom").val(zoomLevel);
            });
            
			MYMAP.map.on("rightclick", function(event) {
				
                if(!WPGMZA.mapEditor)
					WPGMZA.mapEditor = {};
				
				var marker;
				
				if(!WPGMZA.mapEditor.rightClickMarker)
				{
					marker = WPGMZA.mapEditor.rightClickMarker = WPGMZA.Marker.createInstance({
						draggable: true
					});
					
					marker.on("dragend", function(event) {
						jQuery(".wpgmza-marker-panel [data-ajax-name='address']").val(event.latLng.lat+', '+event.latLng.lng);
						jQuery(".wpgmza-marker-panel [data-ajax-name='lat']").val(event.latLng.lat);
						jQuery(".wpgmza-marker-panel [data-ajax-name='lng']").val(event.latLng.lng);
					} );
					
					

					MYMAP.map.on("click", function() {
						marker.setMap(null);
					});
				}
				else
					marker = WPGMZA.mapEditor.rightClickMarker;
				
				marker.setPosition(event.latLng);
				marker.setMap(MYMAP.map);
				
                jQuery(".wpgmza-marker-panel [data-ajax-name='address']").val(event.latLng.lat+', '+event.latLng.lng);
				jQuery(".wpgmza-marker-panel [data-ajax-name='lat']").val(event.latLng.lat);
				jQuery(".wpgmza-marker-panel [data-ajax-name='lng']").val(event.latLng.lng);
				
                jQuery("#wpgm_notice_message_save_marker").show();
                setTimeout(function() {
                    jQuery("#wpgm_notice_message_save_marker").fadeOut('slow')
                }, 3000);
               
            });

			MYMAP.map.on("bounds_changed", function() {
				
				var center = MYMAP.map.getCenter();
				var zoom = MYMAP.map.getZoom();
				var $ = jQuery;
				
				jQuery("#wpgmza_start_location").val(center.lat + "," + center.lng);
				jQuery("#wpgmza_start_zoom").val(zoom);
				
				jQuery("#wpgmaps_save_reminder").show();
				
			});
			
			for(var dataset_id in wpgmza_legacy_map_edit_page_vars.heatmaps)
			{
				var options = wpgmza_legacy_map_edit_page_vars.heatmap_options_by_id[dataset_id];
				var heatmapInstance = heatmap[dataset_id];
				
				heatmapInstance.setMap(this.map.googleMap);
				heatmapInstance.set("opacity", options.opacity);
				heatmapInstance.set("radius", options.radius);
				
				var json;
				try{
					json = JSON.parse(options.gradient);
				}catch(e) {
					console.warn("Invalid heatmap gradient");
				}
				
				if(json && typeof json == "object")
					heatmapInstance.set("gradient", JSON.parse(options.gradient));
			}
			
			for(var polygon_id in WPGM_Path)
			{
				WPGM_Path[polygon_id].setMap(this.map.googleMap);
			}
            
			for(var polyline_id in WPGM_PathLine)
			{
				WPGM_PathLine[polyline_id].setMap(this.map.googleMap);
			}

			MYMAP.map.on("bounds_changed", function() {
                var location = MYMAP.map.getCenter();
                jQuery("#wpgmza_start_location").val(location.lat+","+location.lng);
                jQuery("#wpgmaps_save_reminder").show();
            });

			if(window.google)
			{
				if(wpgmza_legacy_map_edit_page_vars.bicycle_layer == "1")
				{
					var bikeLayer = new google.maps.BicyclingLayer();
					bikeLayer.setMap(this.map.googleMap);
				}
				
				if(wpgmza_legacy_map_edit_page_vars.traffic_layer == "1")
				{
					var trafficLayer = new google.maps.TrafficLayer();
					trafficLayer.setMap(this.map.googleMap);
				}
				
				if(wpgmza_legacy_map_edit_page_vars.transport_layer == "1")
				{
					var transitLayer = new google.maps.TransitLayer();
					transitLayer.setMap(this.map.googleMap);
				}
				
				var now = new Date();
				var timestamp = now.getTime();
				// NB: Moved to map modules
				/*if(wpgmza_legacy_map_edit_page_vars.kml_urls && wpgmza_legacy_map_edit_page_vars.kml_urls.length)
				{
					var temp = wpgmza_legacy_map_edit_page_vars.kml_urls;
					arr = temp.split(',');
					arr.forEach(function(entry) {
						var georssLayer = new google.maps.KmlLayer(entry+'?tstamp='+timestamp, {
								preserveViewport: true
						});
						georssLayer.setMap(MYMAP.map.googleMap);
					});
				}*/
				
				if(wpgmza_legacy_map_edit_page_vars.fusion_table && wpgmza_legacy_map_edit_page_vars.fusion_table.length)
				{
					var fusionlayer = new google.maps.FusionTablesLayer(wpgmza_legacy_map_edit_page_vars.fusion, {
						  suppressInfoWindows: false
					});
					fusionlayer.setMap(this.map.googleMap);
				}
			}


            } // End MYMAP.init

			jQuery(function($) {
				window.infoWindow = WPGMZA.InfoWindow.createInstance();
				
				if(wpgmza_legacy_map_edit_page_vars.infowindow_width && 
					wpgmza_legacy_map_edit_page_vars.infowindow_width.length &&
					parseInt(wpgmza_legacy_map_edit_page_vars.infowindow_width) > 0)
					infoWindow.setOptions({maxWidth: parseInt(wpgmza_legacy_map_edit_page_vars.infowindow_width)});
			});
			
			// TODO: Re-do, come up with proper JS solution to remember center before resize, then set after resize, rather than hard coding center
            /*google.maps.event.addDomListener(window, 'resize', function() {
                var myLatLng = new WPGMZA.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                MYMAP.map.setCenter(myLatLng);
            });*/

            MYMAP.placeMarkers = function(filename,map_id) {}
