/**
 * @namespace WPGMZA
 * @module ProMapSettingsPage
 * @requires WPGMZA
 */
jQuery(function($) {
	
	WPGMZA.ProMapSettingsPage = function()
	{
		WPGMZA.MapSettingsPage.call(this);
	}
	
	WPGMZA.extend(WPGMZA.ProMapSettingsPage, WPGMZA.MapSettingsPage);
	
	WPGMZA.MapSettingsPage.createInstance = function()
	{
		return new WPGMZA.ProMapSettingsPage();
	}
	
});