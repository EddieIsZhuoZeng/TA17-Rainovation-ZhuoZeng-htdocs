/**
 * @namespace WPGMZA
 * @module ProAdminMarkerDataTable
 * @pro-requires WPGMZA.AdminMarkerDataTable
 */
jQuery(function($) {
	
	WPGMZA.ProAdminMarkerDataTable = function(element)
	{
		WPGMZA.AdminMarkerDataTable.apply(this, arguments);
	}
	
	WPGMZA.extend(WPGMZA.ProAdminMarkerDataTable, WPGMZA.AdminMarkerDataTable);
	
	WPGMZA.AdminMarkerDataTable.createInstance = function(element)
	{
		return new WPGMZA.ProAdminMarkerDataTable(element);
	}
	
	WPGMZA.ProAdminMarkerDataTable.prototype.onAJAXResponse = function(response)
	{
		WPGMZA.AdminMarkerDataTable.prototype.onAJAXResponse.apply(this, arguments);
		
		$("[data-marker-icon-src]").each(function(index, element) {
			
			var icon = WPGMZA.MarkerIcon.createInstance(
				$(element).attr("data-marker-icon-src")
			);
			
			icon.applyToElement(element);
			
		});
	}
	
});