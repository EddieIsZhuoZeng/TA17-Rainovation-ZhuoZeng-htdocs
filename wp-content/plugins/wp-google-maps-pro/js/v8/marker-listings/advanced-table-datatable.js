/**
 * @namespace WPGMZA
 * @module AdvancedTableDataTable
 * @requires WPGMZA.DataTable
 */
jQuery(function($) {
	
	WPGMZA.AdvancedTableDataTable = function(element, listing)
	{
		var self = this;
		
		this.element = element;
		this.listing = listing;
		
		WPGMZA.DataTable.apply(this, arguments);
		
		this.overrideListingOrderSettings = false;
		
		$(this.dataTableElement).on("click", "th", function(event) {
			
			self.onUserChangedOrder(event);
			
		});
	}
	
	WPGMZA.AdvancedTableDataTable.prototype = Object.create(WPGMZA.DataTable.prototype);
	WPGMZA.AdvancedTableDataTable.prototype.constructor = WPGMZA.AdvancedTableDataTable;
	
	WPGMZA.AdvancedTableDataTable.prototype.getDataTableSettings = function()
	{
		var self = this;
		var options = WPGMZA.DataTable.prototype.getDataTableSettings.apply(this, arguments);
		var json;
		
		if(json = $(this.element).attr("data-order-json"))
			options.order = JSON.parse(json);
		
		options.drawCallback = function(settings) {
			
			var ths = $(self.element).find(".wpgmza_table > thead th");
			
			if(!self.lastResponse || !self.lastResponse.meta)
				return; // Not ready yet
			
			if(self.lastResponse.meta.length == 0)
			{
				self.map.markerListing.trigger("markerlistingupdated");
				return; // No results
			}
			
			$(self.element).find(".wpgmza_table > tbody > tr").each(function(index, tr) {
				
				var meta = self.lastResponse.meta[index];
				
				$(tr).addClass("wpgmaps_mlist_row");
				$(tr).attr("mid", meta.id);
				$(tr).attr("mapid", self.map.id);
				
				$(tr).children("td").each(function(col, td) {
					
					var wpgmza_class = ths[col].className.match(/wpgmza_\w+/)[0];
					$(td).addClass(wpgmza_class);
					
				});
				
			});
			
			$(self.element).find("[data-marker-icon-src]").each(function(index, el) {
				
				var data;
				var src = $(el).attr("data-marker-icon-src");
				
				try{
					data = JSON.parse( src );
				}catch(e) {
					data = src;
				}
				
				var icon = WPGMZA.MarkerIcon.createInstance(data);
				
				icon.applyToElement(el);
				
			});
			
			
			self.map.markerListing.trigger("markerlistingupdated");
		};
		
		return options;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onAJAXRequest = function(data, settings)
	{
		var request;
		var listingParams			= this.listing.getAJAXRequestParameters().data;
		var listingFilteringParams	= listingParams.filteringParams;
		var overrideMarkerIDs		= listingParams.overrideMarkerIDs;
		
		delete listingParams.filteringParams;
		delete listingParams.overrideMarkerIDs;
		
		request = $.extend(
			{},
			listingParams,
			WPGMZA.DataTable.prototype.onAJAXRequest.apply(this, arguments)
		);
		
		request.filteringParams = $.extend(
			{},
			listingFilteringParams,
			this.filteringParams
		);
		
		if(this.filteredMarkerIDs)
			request.markerIDs = this.filteredMarkerIDs.join(",");
		
		//if(this.filteringParams)
			//request.filteringParams = this.filteringParams;
		
		if(this.overrideListingOrderSettings !== undefined)
			request.overrideListingOrderSettings = this.overrideListingOrderSettings;
		
		return request;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onMarkerFilterFilteringComplete = function(event)
	{
		var self = this;
		
		this.filteredMarkerIDs = [];
		
		event.filteredMarkers.forEach(function(data) {
			self.filteredMarkerIDs.push(data.id);
		});
		
		self.filteringParams = event.filteringParams;
	}
	
	WPGMZA.AdvancedTableDataTable.prototype.onUserChangedOrder = function(event)
	{
		this.overrideListingOrderSettings = true;
	}
	
});