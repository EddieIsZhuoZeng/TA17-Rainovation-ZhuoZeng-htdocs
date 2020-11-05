/**
 * @namespace WPGMZA
 * @module CarouselMarkerListing
 * @requires WPGMZA.MarkerListing
 */
jQuery(function($) {
	
	WPGMZA.CarouselMarkerListing = function(map, element, options)
	{
		WPGMZA.MarkerListing.call(this, map, element, 
			$.extend({paginationEnabled: false}, options)
		);
	}
	
	WPGMZA.CarouselMarkerListing.prototype = Object.create(WPGMZA.MarkerListing.prototype);
	WPGMZA.CarouselMarkerListing.prototype.constructor = WPGMZA.CarouselMarkerListing;
	
	WPGMZA.CarouselMarkerListing.createInstance = function(el)
	{
		return new WPGMZA.CarouselMarkerListing(el);
	}
	
	WPGMZA.CarouselMarkerListing.prototype.getOwlCarouselOptions = function()
	{
		var options = {
			autoplay: 			true,
			autoplayTimeout:	5000,
			lazyLoad: 			false,
			autoHeight:			false,
			dots:				false,
			nav:				false,
			loop:				true,
			responsive: {
				0: {
					items: 1
				},
				500: {
					items: 3
				},
				800: {
					items: 5
				}
			}
		};
		
		if(wpgmaps_localize_global_settings['carousel_lazyload'] == "yes")
			options.lazyLoad = true;
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_autoplay']))
			options.autoplayTimeout = parseInt(wpgmaps_localize_global_settings['carousel_autoplay']);
		
		if(wpgmaps_localize_global_settings['carousel_autoheight'] == "yes")
			options.autoHeight = true;
		
		if(wpgmaps_localize_global_settings['carousel_pagination'] == "yes")
			options.dots = true;
		
		if(wpgmaps_localize_global_settings['carousel_navigation']  == "yes")
			options.nav = true;
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_items']))
			options.responsive["800"].items = parseInt(wpgmaps_localize_global_settings['carousel_items']);
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_items_tablet']))
			options.responsive["500"].items = parseInt(wpgmaps_localize_global_settings['carousel_items_tablet']);
		
		if(!isNaN(wpgmaps_localize_global_settings['carousel_items_mobile']))
			options.responsive["0"].items = parseInt(wpgmaps_localize_global_settings['carousel_items_mobile']);
		
		return options;
	}
	
	WPGMZA.CarouselMarkerListing.prototype.getAJAXRequestParameters = function(params)
	{
		var params = WPGMZA.MarkerListing.prototype.getAJAXRequestParameters.call(this, params);
		
		// The carousel fetches all items, so remove limits
		delete params.data.start;
		delete params.data.length;
		
		return params;
	}
	
	WPGMZA.CarouselMarkerListing.prototype.onHTMLResponse = function(html)
	{
		WPGMZA.MarkerListing.prototype.onHTMLResponse.call(this, html);
		
		$(this.element).trigger('destroy.owl.carousel');
		$(this.element).owlCarousel(this.getOwlCarouselOptions());
	}
	
	/*$(document).ready(function() {
		
		$("[data-wpgmza-carousel-marker-listing]").each(function(index, el) {
			
			el.wpgmzaCarouselMarkerListing = 
				el.wpgmzaMarkerListing = 
				WPGMZA.CarouselMarkerListing.createInstance(el);
			
		});
		
	});*/
	
});
