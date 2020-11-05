/**
 * @namespace WPGMZA
 * @module ProMarkerPanel
 * @pro-requires WPGMZA
 */
jQuery(function($){
	
	WPGMZA.ProMarkerPanel = function()
	{
		var self = this;
		
		this.element = $(".wpgmza-marker-panel");
		
		this.initAutoComplete();
		this.initMarkerIconPicker();
		this.initMarkerGalleryInput();
		this.initCategoryPicker();
		
		this.addressChanged = false;
		
		$(this.element).find("button.wpgmza-save-marker").on("click", function(event) {
			self.onSave(event);
		});
		
		$(document.body).on("click", "[data-edit-marker-id]", function(event) {
			self.onEditMarker(event);
		});
		
		$(this.element).find("input[data-ajax-name='address']").on("input change", function(event) {
			self.onAddressChanged(event);
		});
	}
	
	WPGMZA.ProMarkerPanel.prototype.initAutoComplete = function()
	{
		if(!window.google || !google.maps)
			return;
		
		if(!window.google.maps.places)
		{
			console.warn("Please enable the Places API to use address autocomplete");
			return;
		}
		
		this.autoComplete = new google.maps.places.Autocomplete(
			this.element.find("[data-ajax-name='address']")[0]
		);
	}
	
	WPGMZA.ProMarkerPanel.prototype.initMarkerIconPicker = function()
	{
		this.markerIconPicker = new WPGMZA.MarkerIconPicker($(this.element).find(".wpgmza-marker-icon-picker"));
	}
	
	WPGMZA.ProMarkerPanel.prototype.initMarkerGalleryInput = function()
	{
		this.markerGalleryInput = new WPGMZA.MarkerGalleryInput($(this.element).find("input[data-ajax-name='gallery']"));
	}
	
	WPGMZA.ProMarkerPanel.prototype.initCategoryPicker = function()
	{
		this.categoryPicker = new WPGMZA.CategoryPicker($(this.element).find(".wpgmza-category-picker"));
	}
	
	WPGMZA.ProMarkerPanel.prototype.showPleaseWaitButton = function()
	{
		$(this.element).find("button.wpgmza-save-marker").text(WPGMZA.localized_strings.add_marker);
		$(this.element).find("button.wpgmza-save-marker").prop("disabled", true);
	}
	
	WPGMZA.ProMarkerPanel.prototype.restoreSaveButton = function()
	{
		if($(this.element).find("[data-ajax-name='id']").val() == "-1")
			$(this.element).find("button.wpgmza-save-marker").text(WPGMZA.localized_strings.add_marker);
		else
			$(this.element).find("button.wpgmza-save-marker").text(WPGMZA.localized_strings.save_marker);
		
		$(this.element).find("button.wpgmza-save-marker").prop("disabled", false);
	}
	
	WPGMZA.ProMarkerPanel.prototype.reset = function()
	{
		$(this.element).find("[data-ajax-name='id']").val("-1");
		$(this.element).find("[data-ajax-name]:not([data-ajax-name='map_id'])").val("");
		$(this.element).find("select[data-ajax-name]>option:first-child").prop("selected", true);
		
		$(this.element).find("input[type='checkbox']").prop("checked", false);
		
		if(tinyMCE.get("wpgmza-description-editor"))
			tinyMCE.get("wpgmza-description-editor").setContent("");
		else
			$("#wpgmza-description-editor").val("");
		
		this.markerIconPicker.reset();
		this.categoryPicker.setSelection(null);
		this.markerGalleryInput.clear();
		
		this.restoreSaveButton();
		
		this.addressChanged = false;
	}
	
	WPGMZA.ProMarkerPanel.prototype.select = function(marker)
	{
		var self = this;
		
		this.reset();
		this.showPleaseWaitButton();
		
		WPGMZA.restAPI.call("/markers/" + marker.id + "?skip_cache=1&raw_data=true", {
			success: function(data, status, xhr) {
				
				self.populate(data);
				self.restoreSaveButton();
				
			}
		});
	}
	
	WPGMZA.ProMarkerPanel.prototype.populate = function(data)
	{
		for(var name in data)
		{
			switch(name)
			{
				case "description":
					if(tinyMCE.get("wpgmza-description-editor"))
						tinyMCE.get("wpgmza-description-editor").setContent(data.description);
					else
						$("#wpgmza-description-editor").val(data.description);
					break;
				
				case "icon":
					// NB: 8.0.10 and onwards select raw data
					this.markerIconPicker.setIcon(data.icon);
					continue;
					break;
				
				case "categories":
					this.categoryPicker.setSelection(data.categories);
					break;
				
				case "gallery":
					if(data.gallery)
						this.markerGalleryInput.populate(data.gallery);
					break;
				
				case "custom_field_data":
					
					data.custom_field_data.forEach(function(field) {
						$("fieldset[data-custom-field-id='" + field.id + "'] input[data-ajax-name]").val(field.value);
					});
				
					break;
				
				default:
					break;
			}
			
			var target = $(this.element).find("[data-ajax-name='" + name + "']");
			
			switch((target.attr("type") || "").toLowerCase())
			{
				case "checkbox":
				
					target.prop("checked", data[name] == 1);
				
					break;
				
				default:
				
					$(this.element).find("[data-ajax-name='" + name + "']:not(select)").val(data[name]);
					
					$(this.element).find("select[data-ajax-name='" + name + "']").each(function(index, el) {
						
						if(typeof data[name] == "string" && data[name].length == 0)
							return;
						
						$(el).val(data[name]);
						
					});
				
					break;
			}
			
			
		}
		
		this.addressChanged = false;
		
		// Legacy support - Add the pic to the gallery, but only if the gallery is blank
		if(data.pic && data.pic.length && (!data.gallery || !data.gallery.length))
		{
			this.markerGalleryInput.addPicture({
				url: data.pic
			});
		}
	}
	
	WPGMZA.ProMarkerPanel.prototype.getFieldData = function()
	{
		var fields = $(this.element).find("[data-ajax-name]");
		var data = {};
		
		fields.each(function(index, el) {
			
			var type = "text";
			if($(el).attr("type"))
				type = $(el).attr("type").toLowerCase();
			
			switch(type)
			{
				case "checkbox":
					data[$(el).attr("data-ajax-name")] = $(el).prop("checked") ? 1 : 0;
					break;
				
				case "radio":
					if($(el).prop("checked"))
						data[$(el).attr("data-ajax-name")] = $(el).val();
					break;
					
				default:
					data[$(el).attr("data-ajax-name")] = $(el).val()
					break;
			}
			
		});
		
		if(tinyMCE.get("wpgmza-description-editor")) {
			data.description = tinyMCE.get("wpgmza-description-editor").getContent();
		}
		else
			data.description = $("#wpgmza-description-editor").val();
		
		data.gallery = this.markerGalleryInput.toJSON();
		
		if(data.gallery.length == 0)
			data.gallery = null;
		
		return data;
	}
	
	WPGMZA.ProMarkerPanel.prototype.onEditMarker = function(event)
	{
		var self = this;
		var id = $(event.currentTarget).attr("data-edit-marker-id");
		
		this.reset();
		this.showPleaseWaitButton();
		
		WPGMZA.restAPI.call("/markers/" + id + "?skip_cache=1&raw_data=true", {
			
			success: function(data, status, xhr) {
				
				self.populate(data);
				self.restoreSaveButton();
			}
			
		});
	}
	
	WPGMZA.ProMarkerPanel.prototype.onAddressChanged = function(event)
	{
		this.addressChanged = true;
	}
	
	WPGMZA.ProMarkerPanel.prototype.onSave = function(event)
	{
		var self = this;
		var address = $(this.element).find("[data-ajax-name='address']").val();
		
		if(address.length == 0)
		{
			alert(WPGMZA.localized_strings.no_address_specified);
			return;
		}
		
		this.showPleaseWaitButton();
		
		function callback()
		{
			var id = $(self.element).find("[data-ajax-name='id']").val();
			var route = "/markers/";
			var markerIsNew = id == -1;
			
			if(!markerIsNew)
				route += id;
			
			WPGMZA.restAPI.call(route, {
				method: "POST",
				data: self.getFieldData(),
				success: function(data, status, xhr) {
					
					var marker;
					var map = WPGMZA.maps[0];
					
					self.reset();
					
					if(marker = map.getMarkerByID(id))
						map.removeMarker(marker);
					
					marker = WPGMZA.Marker.createInstance(data);
					map.addMarker(marker);
					
					marker.panIntoView();
					
					WPGMZA.adminMarkerDataTable.reload();
					
				}
			});
		}
		
		if(!this.addressChanged)
			callback();
		else
		{
			var geocoder = WPGMZA.Geocoder.createInstance();
			
			geocoder.geocode({
				address: address
			}, function(results, status) {
				
				switch(status)
				{

					case WPGMZA.Geocoder.SUCCESS:
					
						var latLng = results[0].latLng;
						
						$(self.element).find("[data-ajax-name='lat']").val(latLng.lat);
						$(self.element).find("[data-ajax-name='lng']").val(latLng.lng);
						
						callback();
						
						break;
						
					case WPGMZA.Geocoder.ZERO_RESULTS:
						alert(WPGMZA.localized_strings.address_not_found);
						self.restoreSaveButton();
						break;
						
					default:
						alert(WPGMZA.localized_strings.geocode_fail);
						self.restoreSaveButton();
						break;
				}
				
			});
		}
	}
	
});