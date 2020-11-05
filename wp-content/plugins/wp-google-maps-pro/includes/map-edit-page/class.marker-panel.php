<?php

namespace WPGMZA;

class MarkerPanel extends DOMDocument
{
	public function __construct($map_id)
	{
		global $wpgmza;
		
		DOMDocument::__construct();
		
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-sortable');
		// wp_enqueue_script('wpgmza-jquery-ui-sortable-animation', WPGMZA_PRO_DIR_URL . 'lib/jquery.ui.sortable-animation.js', array(), $wpgmza->getProVersion());
		
		@$this->loadPHPFile(WPGMZA_PRO_DIR_PATH . 'html/map-edit-page/pro-marker-panel.html.php');
		$this->querySelector('input[data-ajax-name="map_id"]')->setAttribute('value', $map_id);
		
		$panel = $this->querySelector('.wpgmza-marker-panel');
		
		$customFieldsHTML = CustomMapObjectFields::adminHtml();
		$panel->import($customFieldsHTML);
		
		$fieldset = $this->querySelector(".wpgmza-save-marker-container");
		$panel->appendChild($fieldset);
		
		$this->initCategoryPicker(array(
			'map_id' => $map_id
		));
	}
	
	protected function initCategoryPicker($options=null)
	{
		$arr = array(
			'ajaxName' => 'category'
		);
		
		if($options)
			$arr = array_merge($arr, $options);
		
		$categoryPicker = new CategoryPicker($arr);
		$container = $this->querySelector(".wpgmza-category-picker-container");
		$container->import($categoryPicker);
	}
	
	protected function initCustomFields()
	{
		
	}
}
