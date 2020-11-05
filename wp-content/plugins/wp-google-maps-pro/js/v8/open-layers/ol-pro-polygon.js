/**
 * @namespace WPGMZA
 * @module OLProPolygon
 * @requires WPGMZA.OLPolygon
 */
jQuery(function($) {
	
	WPGMZA.OLProPolygon = function(row, olFeature)
	{
		var self = this;
		
		WPGMZA.OLPolygon.call(this, row, olFeature);
		
		this.addEventListener("mouseover", function(event) {
			self.onMouseOver(event);
		});
		this.addEventListener("mouseout", function(event) {
			self.onMouseOut(event);
		});
	}
	
	WPGMZA.OLProPolygon.prototype = Object.create(WPGMZA.OLPolygon.prototype);
	WPGMZA.OLProPolygon.prototype.constructor = WPGMZA.OLProPolygon;
	
	WPGMZA.OLProPolygon.prototype.onMouseOver = function(event)
	{
		if(!this.olHoverStyle)
		{
			var params = {};
			
			if(this.settings.hoverOpacity)
				params.fill = new ol.style.Fill({
					color: WPGMZA.hexOpacityToRGBA(this.settings.hoverFillColor, this.settings.hoverOpacity)
				});
				
			if(this.settings.hoverStrokeColor)
				params.stroke = new ol.style.Stroke({
					color: WPGMZA.hexOpacityToRGBA(this.settings.hoverStrokeColor, 1)
				});
				
			this.olHoverStyle = new ol.style.Style(params);
		}
		
		this.layer.setStyle(this.olHoverStyle);
	}
	
	WPGMZA.OLProPolygon.prototype.onMouseOut = function(event)
	{
		this.layer.setStyle(this.olStyle);
	}
	
});