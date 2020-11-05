/**
 * @namespace WPGMZA
 * @module ProMapEditPage
 * @pro-requires WPGMZA.MapEditPage
 */
jQuery(function($) {
	
	if(WPGMZA.currentPage != "map-edit")
		return;
	
	WPGMZA.ProMapEditPage = function()
	{
		var self = this;
		
		WPGMZA.MapEditPage.apply(this, arguments);
		
		this.markerPanel = new WPGMZA.ProMarkerPanel();
		this.directionsOriginIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_origin_icon_picker_container > .wpgmza-marker-icon-picker") );
		this.directionsDestinationIconPicker = new WPGMZA.MarkerIconPicker( $("#directions_destination_icon_picker_container > .wpgmza-marker-icon-picker") );
		
		this.advancedSettingsMarkerIconPicker = new WPGMZA.MarkerIconPicker( $("#advanced-settings-marker-icon-picker-container > .wpgmza-marker-icon-picker") );

		$("input[name='store_locator_search_area']").on("input", function(event) {
			self.onStoreLocatorSearchAreaChanged(event);
		});
		self.onStoreLocatorSearchAreaChanged();

		// NB: Workaround for bad DOM
		$("#open-route-service-key-notice").wrapInner("<div class='notice notice-error'><p></p></div>");
	
		var store_locator_search_area = $("input[name='store_locator_search_area']");	
		$(store_locator_search_area).on('change', function() {
			self.onStoreLocatorSearchAreaChanged();
		});

		self.onDirectionsBoxStyle();
		$('body').on('change', '[name="directions_box_style"]', function(){
			self.onDirectionsBoxStyle();
		});
	
	}
	
	WPGMZA.extend(WPGMZA.ProMapEditPage, WPGMZA.MapEditPage);
	
	WPGMZA.ProMapEditPage.prototype.onStoreLocatorSearchAreaChanged = function(event)
	{
		var value = $("input[name='store_locator_search_area']:checked").val();
		
		$("[data-search-area='" + value + "']").show();
		$("[data-search-area][data-search-area!='" + value + "']").hide();

		if(value == 'auto'){	
			$('.wpgmza-store-locator-radial-setting').hide();
			$('#wpgmza_store_locator_bounce_conditional').hide();	
		}
		else{
			$('.wpgmza-store-locator-radial-setting').show();
			$('#wpgmza_store_locator_bounce_conditional').show();
		}
	}

	WPGMZA.ProMapEditPage.prototype.onDirectionsBoxStyle = function()
	{
		var style = $('[name="directions_box_style"]:checked').val();

		if(style == "modern" || WPGMZA.settings.user_interface_style == "modern") 
		{
			$('#wpgmza-directions-box-open-by-default, #wpgmza-directions-box-width').hide();
		}
		else if(style == "default") {
			$('#wpgmza-directions-box-open-by-default, #wpgmza-directions-box-width').show();
		}
	}
	
});